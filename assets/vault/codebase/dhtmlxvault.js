/*
Product Name: dhtmlxVault 
Version: 2.2 
Edition: Standard 
License: content of this file is covered by GPL. Usage outside GPL terms is prohibited. To obtain Commercial or Enterprise license contact sales@dhtmlx.com
Copyright UAB Dinamenta http://www.dhtmlx.com
*/

function dhtmlXVaultObject(conf) {
	
	var that = this;
	
	this.conf = {
		version: "2.2",
		skin: (conf.skin||window.dhx4.skin||(typeof(dhtmlx)!="undefined"?dhtmlx.skin:null)||window.dhx4.skinDetect("dhxvault")||"dhx_skyblue"),
		param_name: (typeof(conf.paramName)!="undefined"?conf.paramName:"file"),
		engine: null,
		list: "default",
		url: conf.uploadUrl||"",
		// multiple files, html5/flash only
		multiple_files: (typeof(conf.multiple)!="undefined"?conf.multiple==true:true),
		// swf-file path
		swf_file: conf.swfPath||"",
		swf_url:  conf.swfUrl||"",
		swf_logs: conf.swfLogs,
		// sl-data
		sl_xap:  conf.slXap,
		sl_url:  conf.slUrl,
		sl_logs: conf.slLogs,
		// common
		enabled: true,
		auto_start: (typeof(conf.autoStart)!="undefined"?conf.autoStart==true:true), // true by default
		auto_remove: (typeof(conf.autoRemove)!="undefined"?conf.autoRemove==true:false), // false by default
		files_added: 0,
		uploaded_count: 0,
		files_limit: (typeof(conf.filesLimit)!="undefined"?conf.filesLimit:0), // max files
		max_file_size: 0, // can be truth if > 0
		buttons: { // visible buttons
			upload: (typeof(conf.buttonUpload)!="undefined"?(conf.buttonUpload==true):false),
			clear: (typeof(conf.buttonClear)!="undefined"?(conf.buttonClear==true):true)
		},
		// offsets
		ofs: {
			dhx_skyblue: 5,
			dhx_web: 7,
			dhx_terrace: 10,
			bootstrap: 10
		},
		// data
		uploaded_state: {}, // save state tru/false for uploaded or failed
		uploaded_files: {}, // uploaded files data
		// progress mode
		progress_mode: "percent", // "percent","eta"
		// icons
		icon_def: "",
		icons: {} // generated
	}
	
	this.list = this["list_"+this.conf.list];
	
	// icons
	this.conf.icon_def = this.icon_def;
	for (var a in this.icons) {
		for (var q=0; q<this.icons[a].length; q++) this.conf.icons[this.icons[a][q]] = a;
	}
	
	// engine detect
	if (typeof(conf.mode) == "string" && typeof(this[conf.mode]) == "function") {
		this.conf.engine = conf.mode;
	} else {
		this.conf.engine = "html4";
		
		var k = null;
		if (typeof(window.FormData) != "undefined" && typeof(window.XMLHttpRequest) != "undefined") {
			k = new XMLHttpRequest();
			if (typeof(k.upload) == "undefined") k = null;
		}
		
		if (k != null) {
			// IE10, IE11, FF, Chrome, Opera
			this.conf.engine = "html5";
		} else if (typeof(window.swfobject) != "undefined" || k === false) {
			var k = swfobject.getFlashPlayerVersion();
			if (k.major >= 10) this.conf.engine = "flash";
		} else {
			// check if silverlight installed
			this.conf.sl_v = this.getSLVersion();
			if (this.conf.sl_v) this.conf.engine = "sl";
		}
		k = null;
	}
	
	var base = (typeof(conf.parent) != "undefined" ? conf.parent : conf.container);
	base = (typeof(base)=="string"?document.getElementById(base):base);
	conf.parent = conf.container = null;
	
	if (base._attach_mode == true) {
		this.base = base;
	} else {
		this.base = document.createElement("DIV");
		base.appendChild(this.base);
	}
	this.base.className += " dhx_vault_"+this.conf.skin;
	if (base._no_border == true) this.base.style.border = "0px solid white";
	
	base = conf = null;
	
	// buttons
	this.p_controls = document.createElement("DIV");
	this.p_controls.className = "dhx_vault_controls";
	this.base.appendChild(this.p_controls);
	this.p_controls.onselectstart = function(e){
		e = e||event;
		if (e.preventDefault) e.preventDefault(); else e.returnValue = false;
		return false;
	}
	
	// files
	this.p_files = document.createElement("DIV");
	this.p_files.className = "dhx_vault_files";
	this.base.appendChild(this.p_files);
	
	this._doOnFilesClick = function(e) {
		e = e||event;
		var t = e.target||e.srcElement;
		var action = null;
		while (t != that.p_files && action == null) {
			if (action == null && t != null && t._action != null) {
				action = t._action;
			} else {
				t = t.parentNode;
			}
		}
		if (action == null) return;
		if (action.data == "delete_file") {
			if (that.conf.enabled) that._removeFileFromQueue(action.id);
		}
	}
	if (typeof(window.addEventListener) == "function") {
		this.p_files.addEventListener("click", this._doOnFilesClick, false);
	} else {
		this.p_files.attachEvent("onclick", this._doOnFilesClick);
	}
	
	this.file_data = {};
	this.file_items = {};
	
	this._initToolbar = function() {
		
		// add
		this.b_opts = {
			browse:	{ str: "btnAdd", onclick: null },
			upload:	{ str: "btnUpload", onclick: function() { if (!that.conf.enabled) return; if (!that.conf.uploading) { that._uploadStart(); } } },
			cancel:	{ str: "btnCancel", onclick: function() { if (!that.conf.enabled) return; that._uploadStop(); that._switchButton(false); } },
			clear:	{ str: "btnClean", onclick: function() { if (!that.conf.enabled) return; that.clear(); }, css: "float:right!important;"}
		};
		
		this.buttons = {};
		
		for (var a in this.b_opts) {
			var k = document.createElement("DIV");
			k.innerHTML = "<div class='dhxvault_button_icon dhx_vault_icon_"+a+"'></div>"+
					"<div class='dhxvault_button_text'>"+this.strings[this.b_opts[a].str]+"</div>";
			
			if (this.b_opts[a].css != null) k.style.cssText += this.b_opts[a].css;
			k.className = "dhx_vault_button";
			k._css = k.className;
			k._onclick = this.b_opts[a].onclick;
			k.onmouseover = function() {
				if (that.conf.enabled != true) return;
				if (this._hover == true) return;
				this._hover = true;
				this.className = this._css+" dhx_vault_button"+this._css_p+"_hover";
			}
			k.onmouseout = function() {
				if (that.conf.enabled != true) return;
				if (this._hover != true) return;
				this._hover = false;
				this.className = this._css;
			}
			k.onmousedown = function() {
				if (that.conf.enabled != true) return;
				if (this._hover != true) return;
				this._pressed = true;
				this.className = this._css+" dhx_vault_button"+this._css_p+"_pressed";
			}
			k.onmouseup = function(e) {
				if (that.conf.enabled != true) return;
				if (this._pressed != true) return;
				this._pressed = false;
				this.className = this._css+(this._hover?" dhx_vault_button"+this._css_p+"_hover":"");
				if (this._onclick != null) this._onclick();
			}
			if (this.b_opts[a].tooltip) k.title = this.b_opts[a].tooltip;
			this.p_controls.appendChild(k);
			this.buttons[a] = k;
			k = null;
			
			// visibile
			if (a == "upload" || a == "clear") this.buttons[a].style.display = (this.conf.buttons[a] == true?"":"none");
			
			this.b_opts[a].onclick = null;
			this.b_opts[a] = null;
			delete this.b_opts[a];
		}
		
		this.b_opts = null;
		delete this.b_opts;
		
		this.buttons.cancel.style.display = "none";
	}
	
	this._readableSize = function(t) {
		var i = false;
		var b = ["b","Kb","Mb","Gb","Tb","Pb","Eb"];
		for (var q=0; q<b.length; q++) if (t > 1024) t = t / 1024; else if (i === false) i = q;
		if (i === false) i = b.length-1;
		return Math.round(t*100)/100+" "+b[i];
	}
	
	this._beforeAddFileToList = function(name, size) {
		return (this.callEvent("onBeforeFileAdd", [{
			id: null,
			name: name,
			size: size,
			serverName: null,
			uploaded: false,
			error: false
		}])===true);
	}
	
	this._addFileToList = function(id, name, size, state, progress) {
		
		var ext = this.getFileExtension(name);
		var icon = (ext.length>0?(this.conf.icons[ext.toLowerCase()]||this.conf.icon_def):this.conf.icon_def);
		
		var t = document.createElement("DIV");
		this.p_files.appendChild(t);
		this.file_items[id] = t;
		
		t._idd = id;
		
		// render file in list
		this.list.renderFileRecord(t, {name: name, icon: icon, size: size, readableSize: this._readableSize(size||0), state: state, progress: progress});
		
		this.callEvent("onFileAdd", [{
			id: id,
			name: name,
			size: size,
			serverName: null,
			uploaded: false,
			error: false
		}]);
		
		t = null;
	}
	
	this._removeFileFromList = function(id) {
		
		if (!this.file_items[id]) return;
		
		// remove from list
		this.list.removeFileRecord(this.file_items[id]);
		
		this.file_items[id]._idd = null;
		this.file_items[id].parentNode.removeChild(this.file_items[id]);
		this.file_items[id] = null;
		delete this.file_items[id];
		
		if (this.conf.uploaded_files[id]) {
			this.conf.uploaded_files[id] = null;
			delete this.conf.uploaded_files[id];
		}
		
		if (this.conf.uploaded_state[id]) {
			this.conf.uploaded_state[id] = null;
			delete this.conf.uploaded_state[id];
		}
		
	}
	
	this._updateFileInList = function(id, state, progress) {
		if (!this.file_items[id]) return;
		if (state == "uploading" && this.conf.progress_mode == "eta" && this._etaStart != null) this._etaStart(id);
		// progress
		this._updateProgress(id, state, progress);
	}
	
	this._updateProgress = function(id, state, progress) {
		if (state == "added") {
			this.list.updateFileState(this.file_items[id], {state: state});
			if (this.conf.progress_mode == "eta" && this._etaEnd != null) this._etaEnd(id);
			return;
		}
		if (state == "fail") {
			this.list.updateFileState(this.file_items[id], {state: state, str_error: this.strings.error});
			if (this.conf.progress_mode == "eta" && this._etaEnd != null) this._etaEnd(id);
			return;
		}
		if (state == "uploaded") {
			if (this.conf.progress_mode == "eta" && this._etaEnd != null) this._etaEnd(id);
			var item = this.file_items[id];
			var str_done = this.strings.done;
			var nameSizeData = (this.conf.engine != "html4" ? null : {name: this.file_data[id].name, size: this.file_data[id].size, readableSize: this._readableSize(this.file_data[id].size||0)}); // for html4 mode - update size
			window.setTimeout(function(){
				that.list.updateFileState(item, {state: "uploaded", str_done: str_done});
				if (nameSizeData != null) that.list.updateFileNameSize(item, nameSizeData);
				item = null;
			}, 100); // for very little files or goood internet line
			return;
		}
		if (state == "uploading") {
			if (progress < 100 && this.conf.progress_type == "loader") {
				/* html4 mode, no progress */
				this.list.updateFileState(this.file_items[id], {state: "uploading_html4"});
			} else if (this.conf.progress_mode == "eta") {
				var eta = (this._etaCheck!=null?this._etaCheck(id,progress):null);
				this.list.updateFileState(this.file_items[id], {state: "uploading", progress: progress, eta: (eta==null?null:"eta: "+eta)});
			} else if (this.conf.progress_mode == "percent") {
				this.list.updateFileState(this.file_items[id], {state: "uploading", progress: progress, eta: progress+"%"});
			}
		}
	}
	
	this._removeFilesByState = function(state) {
		for (var a in this.file_data) {
			if (state === true || this.file_data[a].state == state) {
				this._removeFileFromQueue(a);
			}
		}
	}
	
	this._switchButton = function(state) {
		
		if (state == true) {
			if (this.conf.buttons.upload == true) {
				this.buttons.upload.style.display = "none";
				this.buttons.cancel.style.display = "";
			}
		} else {
			var t = this.conf.uploaded_count;
			var f = [];
			for (var a in this.conf.uploaded_state) {
				f.push({
					id: a,
					name: this._fileName,
					size: (this.file_data[a]!=null?this.file_data[a].size:null),
					serverName: (this.conf.uploaded_files[a]?this.conf.uploaded_files[a].serverName:null),
					uploaded: this.conf.uploaded_state[a],
					error: !this.conf.uploaded_state[a]
				});
			}
			if (this.conf.buttons.upload == true) {
				this.buttons.upload.style.display = "";
				this.buttons.cancel.style.display = "none";
			}
			this.conf.uploaded_count = 0;
			this.conf.uploaded_state = {};
			if (t > 0) this.callEvent("onUploadComplete",[f]);
		}
	}
	
	this._uploadStart = function() {
		
		this._switchButton(true);
		
		// change status for prev fail auploads if any
		if (!this.conf.uploading) {
			for (var a in this.file_data) {
				if (this.file_data[a].state == "fail") {
					this.file_data[a].state = "added";
					this._updateFileInList(a, "added", 0);
				}
			}
		}
		
		this.conf.uploading = true;
		
		var t = false;
		
		for (var a in this.file_data) {
			if (!t && [this.file_data[a].state] == "added") {
				t = true;
				this.file_data[a].state = "uploading";
				this._updateFileInList(a, "uploading", 0);
				this._doUploadFile(a);
			}
		}
		if (!t) {
			this.conf.uploading = false;
			this._switchButton(false);
		}
		
	}
	
	this._onUploadSuccess = function(id, serverName, r, extra) {
		
		// flash mode
		if (typeof(r) != "undefined" && this.conf.engine == "flash") {
			try {eval("dhx4.temp="+r.data);} catch(e){dhx4.temp=null;};
			var t = dhx4.temp;
			dhx4.temp = null;
			if (t != null && t.state == true && t.name != null) {
				serverName = t.name;
				if (t.extra != null) extra = t.extra;
			} else {
				this._onUploadFail(id, (t!=null&&t.extra!=null?t.extra:null));
				return;
			}
		}
		//
		this.conf.uploaded_count++;
		this.conf.uploaded_files[id] = {realName: this.file_data[id].name, serverName: serverName};
		this.file_data[id].state = "uploaded";
		this.conf.uploaded_state[id] = true;
		this._updateFileInList(id, "uploaded", 100);
		this.callEvent("onUploadFile", [{
			id: id,
			name: this.file_data[id].name,
			size: this.file_data[id].size,
			serverName: serverName,
			uploaded: true,
			error: false
		}, extra]);
		if (this.conf.auto_remove) this._removeFileFromQueue(id);
		if (this.conf.uploading) this._uploadStart();
	}
	
	this._onUploadFail = function(id, extra) {
		this.file_data[id].state = "fail";
		this._updateFileInList(id, "fail", 0);
		this.conf.uploaded_state[id] = false;
		this.callEvent("onUploadFail", [{
			id: id,
			name: this.file_data[id].name,
			size: this.file_data[id].size,
			serverName: null,
			uploaded: false,
			error: true
		}, extra]);
		if (this.conf.uploading) this._uploadStart();
	}
	
	this._onUploadAbort = function(id) {
		this.conf.uploading = false;
		this.file_data[id].state = "added";
		this._updateFileInList(id, "added", 0);
		this.callEvent("onUploadCancel",[{
			id: id,
			name: this.file_data[id].name,
			size: this.file_data[id].size,
			serverName: null,
			uploaded: false,
			error: false
		}]);
	}
	
	this.unload = function() {
		
		this.callEvent = function(){return true;}; // some events while files will removed from list
		
		//
		if (typeof(window.addEventListener) == "function") {
			this.p_files.removeEventListener("click", this._doOnFilesClick, false);
		} else {
			this.p_files.detachEvent("onclick", this._doOnFilesClick);
		}
		
		// remove all files from queue/list
		this._removeFilesByState(true);
		this.conf.uploaded_files = null;
		this.file_data = null;
		this.file_items = null;
		
		// custom engine stuff
		this._unloadEngine();
		
		this.list = null;
		this.list_default = null;
		this.icons = null;
		
		// buttons
		for (var a in this.buttons) {
			this.buttons[a].onclick = null;
			this.buttons[a].onmouseover = null;
			this.buttons[a].onmouseout = null;
			this.buttons[a].onmousedown = null;
			this.buttons[a].onmouseup = null;
			this.buttons[a]._onclick = null;
			this.buttons[a].parentNode.removeChild(this.buttons[a]);
			this.buttons[a] = null;
			delete this.buttons[a];
		}
		this.buttons = null;
		
		// buttons container
		this.p_controls.onselectstart = null;
		this.p_controls.parentNode.removeChild(this.p_controls);
		this.p_controls = null;
		
		// buttons container
		this.p_files.parentNode.removeChild(this.p_files);
		this.p_files = null;
		
		window.dhx4._eventable(this, "clear");
		this.callEvent = null;
		
		for (var a in this.conf) {
			this.conf[a] = null;
			delete this.conf[a];
		}
		this.conf = null;
		this.strings = null;
		
		for (var a in this) {
			if (typeof(this[a]) == "function") this[a] = null;
		}
		
		// main container
		if (this.base._attach_mode != true) this.base.parentNode.removeChild(this.base);
		this.base = null;
		
		that = a = null;
		
	}
	
	// init engine-relative funcs
	var e = new this[this.conf.engine]();
	for (var a in e) { this[a] = e[a]; e[a] = null; }
	a = e = p = null;
	
	// init app
	this._initToolbar();
	this._initEngine();
	this.setSkin(this.conf.skin);
	
	window.dhx4._eventable(this);
	
	// files limit
	this.attachEvent("onFileAdd", function(){
		this.conf.files_added++;
	});
	this.attachEvent("onBeforeFileAdd", function(){
		if (this.conf.files_limit == 0) return true;
		return (this.conf.files_added < this.conf.files_limit);
	});
	
	// server settings if any
	window.dhx4.ajax.post(this.conf.url, "mode=conf", function(r){
		try {eval("dhx4.temp="+r.xmlDoc.responseText);} catch(e){};
		var t = dhx4.temp;
		dhx4.temp = null;
		if (t != null) {
			if (t.maxFileSize != null) that.conf.max_file_size = parseInt(t.maxFileSize);
		}
	});
	
	return this;
	
};

dhtmlXVaultObject.prototype.icon_def = "icon_def";
dhtmlXVaultObject.prototype.icons = {
	// css => list_of_extensions
	"icon_image": ["jpg","jpeg","gif","png","bmp","tiff","pcx","svg","ico"],
	"icon_psd":   ["psd"],
	"icon_video": ["avi","mpg","mpeg","rm","move","mov","mkv","flv","f4v","mp4","3gp"],
	"icon_audio": ["wav","aiff","au","mp3","aac","wma","ogg","flac","ape","wv","m4a","mid","midi"],
	"icon_arch":  ["rar","zip","tar","tgz","arj","gzip","bzip2","7z","ace","apk","deb"],
	"icon_text":  ["txt","nfo","djvu","xml"],
	"icon_html":  ["htm","html"],
	"icon_doc":   ["doc","docx","rtf","odt"],
	"icon_pdf":   ["pdf", "ps"],
	"icon_xls":   ["xls"],
	"icon_exe":   ["exe"],
	"icon_dmg":   ["dmg"]
};

dhtmlXVaultObject.prototype.upload = function() {
	if (!this.conf.uploading) this._uploadStart();
};

dhtmlXVaultObject.prototype.setAutoStart = function(state) {
	this.conf.auto_start = (state==true);
};

dhtmlXVaultObject.prototype.setAutoRemove = function(state) {
	this.conf.auto_remove = (state==true);
};

dhtmlXVaultObject.prototype.setURL = function(url) {
	this.conf.url = url;
};

dhtmlXVaultObject.prototype.enable = function() {
	if (this.conf.enabled == true) return;
	this.conf.enabled = true;
	this.base.className = String(this.base.className).replace(/\s{0,}dhx_vault_dis/gi,"");
	//this.p_files.className = "dhx_vault_files";
	//this.p_controls.className = "dhx_vault_controls";
	if (this.conf.engine == "flash") document.getElementById(this.conf.swf_obj_id).style.display = "";
};

dhtmlXVaultObject.prototype.disable = function() {
	if (this.conf.enabled != true) return;
	this.conf.enabled = false;
	this.base.className += " dhx_vault_dis";
	// this.p_files.className = "dhx_vault_files dhx_vault_dis";
	// this.p_controls.className = "dhx_vault_controls dhx_vault_dis";
	if (this.conf.engine == "flash") document.getElementById(this.conf.swf_obj_id).style.display = "none";
};

dhtmlXVaultObject.prototype.setWidth = function(w) { // set width of the control in pixels
	if (this.base._attach_mode == true) return;
	this.base.parentNode.style.width = w+"px";
	this.setSizes();
};

dhtmlXVaultObject.prototype.setHeight = function(h) { // set height of the control in pixels
	if (this.base._attach_mode == true) return;
	this.base.parentNode.style.height = h+"px";
	this.setSizes();
};

dhtmlXVaultObject.prototype.setFilesLimit = function(t) { // control the number of uploaded files
	this.conf.files_added = 0; // reset old settings
	this.conf.files_limit = t;
};

dhtmlXVaultObject.prototype.getStatus = function() {
	// 0 - filelist is empty
	// 1 - all files in filelist uploaded
	//-1 - not all files uploaded
	var t = 0;
	for (var a in this.file_data) {
		if (this.file_data[a].state != "uploaded") return -1;
		t = 1;
	}
	return t;
};

dhtmlXVaultObject.prototype.getData = function() {
	// return struct of uploaded files
	var t = [];
	for (var a in this.conf.uploaded_files) {
		t.push({
			id: a,
			name: this.file_data[a].name,
			size: this.file_data[a].size,
			serverName: this.conf.uploaded_files[a].serverName,
			uploaded: true,
			error: false
		});
	}
	return t;
};

dhtmlXVaultObject.prototype.clear = function() {
	if (this.callEvent("onBeforeClear", []) !== true) return;
	if (this.conf.uploading) this._uploadStop();
	this._switchButton(false);
	this._removeFilesByState(true);
	this.callEvent("onClear",[]);
};

dhtmlXVaultObject.prototype.setSkin = function(skin) {
	if (skin != this.conf.skin) {
		this.base.className = String(this.base.className).replace(new RegExp("\s{0,}dhx_vault_"+this.conf.skin)," dhx_vault_"+skin);
		this.conf.skin = skin;
	}
	
	// update buttons data
	this._updateBttonsSkin();
	
	
	var ofs = this.conf.ofs[this.conf.skin];
	
	this.buttons.browse.style.marginLeft = ofs+"px";
	this.buttons.upload.style.marginLeft = (skin=="dhx_terrace"?"-1px":ofs+"px");
	this.buttons.cancel.style.marginLeft = this.buttons.upload.style.marginLeft;
	this.buttons.clear.style.marginRight = ofs+"px";
	
	// border-radius
	var r = "";
	if (skin == "dhx_terrace") {
		r = (this.conf.buttons.upload == true) ? "0px":"3px";
	}
	
	this.buttons.browse.style.borderTopRightRadius = r;
	this.buttons.browse.style.borderBottomRightRadius = r;
	this.buttons.upload.style.borderTopLeftRadius = r;
	this.buttons.upload.style.borderBottomLeftRadius = r;
	this.buttons.cancel.style.borderTopLeftRadius = this.buttons.upload.style.borderTopLeftRadius;
	this.buttons.cancel.style.borderBottomLeftRadius = this.buttons.upload.style.borderBottomLeftRadius;
	
	this.setSizes();
};

dhtmlXVaultObject.prototype._updateBttonsSkin = function() {
	for (var a in this.buttons) {
		var css = "dhx_vault_button";
		var css_p = "";
		if (this.buttonCss != null && this.buttonCss[this.conf.skin] != null && this.buttonCss[this.conf.skin][a] != null) {
			css_p = this.buttonCss[this.conf.skin][a];
			css += css_p;
		}
		this.buttons[a]._css = this.buttons[a].className = css;
		this.buttons[a]._css_p = css_p;
	}
};

dhtmlXVaultObject.prototype.setSizes = function() {
	
	var w1 = this.base.offsetWidth-(this.base.clientWidth||this.base.scrollWidth);
	var h1 = this.base.offsetHeight-this.base.clientHeight;
	
	this.base.style.width = Math.max(0, this.base.parentNode.clientWidth-w1)+"px";
	this.base.style.height = Math.max(0, this.base.parentNode.clientHeight-h1)+"px";
	
	var ofs = this.conf.ofs[this.conf.skin];
	this.p_files.style.top = this.p_controls.offsetHeight+"px";
	this.p_files.style.left = ofs+"px";
	if (!this.conf.ofs_f) {
		this.p_files.style.width = "100px";
		this.p_files.style.height = "100px";
		this.conf.ofs_f = {
			w: this.p_files.offsetWidth-this.p_files.clientWidth,
			h: this.p_files.offsetHeight-this.p_files.clientHeight
		};
	}
	
	this.p_files.style.width = Math.max(this.base.clientWidth-ofs*2-this.conf.ofs_f.w,0)+"px";
	this.p_files.style.height = Math.max(this.base.clientHeight-this.p_controls.offsetHeight-ofs-this.conf.ofs_f.h,0)+"px";
};

dhtmlXVaultObject.prototype.getFileExtension = function(name) {
	var ext = "";
	var k = String(name).match(/\.([^\.\s]*)$/i); // "filename.jpg" -> [".jpg","jpg"]
	if (k != null) ext = k[1];
	return ext;
};

dhtmlXVaultObject.prototype.strings = {
	// labels
	done: "Done",
	error: "File could not be uploaded for some reason.",
	// buttons
	btnAdd: "Add files",
	btnUpload: "Upload",
	btnClean: "Clear all",
	btnCancel: "Cancel"
};

dhtmlXVaultObject.prototype.setStrings = function(data) {
	for (var a in data) this.strings[a] = data[a];
	// update files in list
	for (var a in this.file_items) {
		var state = this.file_data[a].state;
		if (state == "uploaded" || state == "fail") {
			this.list.updateFileState(this.file_items[a], {
				state: state,
				str_error: this.strings.error,
				str_done: this.strings.done
			});
		}
	}
	// update buttons
	var t = {browse: "btnAdd", upload: "btnUpload", clear: "btnClean", cancel: "btnCancel"};
	for (var a in t) this.buttons[a].childNodes[1].innerHTML = this.strings[t[a]];
	
};

dhtmlXVaultObject.prototype.getMaxFileSize = function() {
	return this.conf.max_file_size;
};

/****************************************************************************************************************************************************************************************************************/
//	HTML 5

dhtmlXVaultObject.prototype.html5 = function(){};
dhtmlXVaultObject.prototype.html5.prototype = {
	
	_initEngine: function() {
		
		var that = this;
		this.buttons["browse"].onclick = function(){
			if (that.conf.enabled) that.f.click();
		}
		
		this.conf.progress_type = "percentage";
		this.conf.dnd_enabled = true;
		
		// Safari on Windows sometimes have problem with multiple file selections
		// file length set to zero, do not allow multiple file selecting
		// d-n-d seems works fine
		
		var k = window.navigator.userAgent;
		var mp = true;
		if (k.match(/Windows/gi) != null && k.match(/AppleWebKit/gi) != null && k.match(/Safari/gi) != null) {
			if (k.match(/Version\/5\.1\.5/gi)) this.conf.multiple_files = false;
			if (k.match(/Version\/5\.1[^\.\d{1,}]/gi)) this.conf.dnd_enabled = false;
			if (k.match(/Version\/5\.1\.1/gi)) {
				this.conf.multiple_files = false;
				this.conf.dnd_enabled = false;
			}
			if (k.match(/Version\/5\.1\.2/gi)) this.conf.dnd_enabled = false;
			if (k.match(/Version\/5\.1\.7/gi)) this.conf.multiple_files = false;
		}
		
		// "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-EN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.1 Safari/533.17.8"	// ok, no dnd
		// "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-EN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5"	// ok, no dnd
		// "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-EN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4"	// ok, no dnd
		// "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-EN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27"	// ok, no dnd
		// "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-EN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1"	// ok, no dnd
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50"				// ok, dnd partialy fail, disabled
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.1 Safari/534.51.22"			// multiple files add - fail, dnd partialy fail, disabled
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.52.7"			// ok, dnd partialy fail, disabled
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.54.16 (KHTML, like Gecko) Version/5.1.4 Safari/534.54.16"			// ok
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.5 Safari/534.55.3"			// multiple files add - fail
		// "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2"			// dnd - ok, multiselect - fail (Windows 8)
		
		// input
		this._addFileInput();
		
		// FF, Opera, Chrome, IE10, IE11
		if (this.conf.dnd_enabled && this._initDND != null) this._initDND();
		
	},
	
	_addFileInput: function() {
		
		// complete input reload, opera needs
		if (this.f != null) {
			this.f.onchange = null;
			this.f.parentNode.removeChild(this.f);
			this.f = null;
		}
		
		var that = this;
		
		this.f = document.createElement("INPUT");
		this.f.type = "file";
		
		if (this.conf.multiple_files) this.f.multiple = "1";
		this.f.className = "dhx_vault_input";
		this.p_controls.appendChild(this.f);
		
		this.f.onchange = function() {
			that._parseFilesInInput(this.files);
			if (window.dhx4.isOpera) that._addFileInput(); else this.value = "";
		}
	},
	
	_doUploadFile: function(id) {
		
		var that = this;
		
		if (!this.loader) {
			this.loader = new XMLHttpRequest();
			this.loader.upload.onprogress = function(e) {
				if (that.file_data[this._idd].state == "uploading") that._updateFileInList(this._idd, "uploading", Math.round(e.loaded*100/e.total));
			}
			this.loader.onload = function(e) {
				try {eval("dhx4.temp="+this.responseText);} catch(e){};
				var r = dhx4.temp;
				dhx4.temp = null;
				if (r != null && typeof(r) == "object" && typeof(r.state) != "undefined" && r.state == true) {
					that._onUploadSuccess(this.upload._idd, r.name, null, r.extra);
					r = null;
				} else {
					that._onUploadFail(this.upload._idd, (r!=null&&r.extra!=null?r.extra:null));
				}
			}
			this.loader.onerror = function(e) {
				that._onUploadFail(this.upload._idd);
			}
			this.loader.onabort = function(e) {
				that._onUploadAbort(this.upload._idd);
			}
		}
		
		this.loader.upload._idd = id;
		
		var form = new FormData();
		form.append("mode", "html5");
		form.append("dhxr"+new Date().getTime(), "");
		
		if (this.file_data[id].size == 0 && (navigator.userAgent.indexOf("MSIE") > 0 || navigator.userAgent.indexOf("Trident") > 0)) { // IE10, IE11 - zero_size file issue, upload filename only
			form.append("file_name", String(this.file_data[id].name));
			form.append("zero_size", "1");
		} else {
			form.append(this.conf.param_name, this.file_data[id].file);
		}
		
		this.loader.open("POST", this.conf.url, true);
		this.loader.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		this.loader.send(form);
		
	},
	
	_uploadStop: function() {
		if (!this.conf.uploading || !this.loader) return;
		this.loader.abort();
	},
	
	_parseFilesInInput: function(f) {
		for (var q=0; q<f.length; q++) this._addFileToQueue(f[q]);
	},
	
	_addFileToQueue: function(f) {
		if (!this._beforeAddFileToList(f.name, f.size)) return;
		var id = (f._idd||window.dhx4.newId());
		this.file_data[id] = {file: f, name: f.name, size: f.size, state: "added"};
		this._addFileToList(id, f.name, f.size, "added", 0);
		if (this.conf.auto_start && !this.conf.uploading) this._uploadStart(true);
	},
	
	_removeFileFromQueue: function(id) {
		
		if (!this.file_data[id]) return;
		
		var name = this.file_data[id].name;
		var serverName = (this.conf.uploaded_files!=null&&this.conf.uploaded_files[id]!=null?this.conf.uploaded_files[id].serverName:null);
		
		var fileData = {
			id: Number(id),
			name: name,
			size: this.file_data[id].size,
			serverName: serverName,
			uploaded: (this.file_data[id].state == "uploaded"),
			error: (this.file_data[id].state == "fail")
		};
		if (this.callEvent("onBeforeFileRemove",[fileData]) !== true) return;
		
		var k = false;
		if (this.conf.uploading && id == this.loader.upload._idd && this.file_data[id].state == "uploading") {
			this._uploadStop();
			k = true;
		}
		
		this.file_data[id].file = null;
		this.file_data[id].name = null;
		this.file_data[id].size = null;
		this.file_data[id].state = null;
		this.file_data[id] = null;
		delete this.file_data[id];
		
		this._removeFileFromList(id);
		
		this.callEvent("onFileRemove",[fileData]);
		
		if (k) this._uploadStart();

	},
	
	_unloadEngine: function() {
		
		this.buttons["browse"].onclick = null;
		
		if (this.conf.dnd_enabled && this._unloadDND != null) this._unloadDND();
			
		this.f.onchange = null;
		this.f.parentNode.removeChild(this.f);
		this.f = null;
		
		if (this.loader) {
			this.loader.upload.onprogress = null;
			this.loader.onload = null;
			this.loader.onerror = null;
			this.loader.onabort = null;
			this.loader.upload._idd = null;
			this.loader = null;
		}
		
		this._initEngine = null;
		this._doUploadFile = null;
		this._uploadStop = null;
		this._parseFilesInInput = null;
		this._addFileToQueue = null;
		this._removeFileFromQueue = null;
		this._unloadEngine = null;
		
	}
	
};

/****************************************************************************************************************************************************************************************************************/
//	HTML 4

dhtmlXVaultObject.prototype.html4 = function(){};
dhtmlXVaultObject.prototype.html4.prototype = {
	
	_initEngine: function() {
		
		this._addForm();
		this.conf.progress_type = "loader";
		
	},
	
	_addForm: function() {
		
		var that = this;
		var id = window.dhx4.newId();
		
		if (!this.k) {
			
			this.k = document.createElement("DIV");
			this.k.className = "dhx_vault_file_form_cont";
			this.buttons["browse"].appendChild(this.k);
			
			this.conf.fr_name = "dhx_vault_file_"+window.dhx4.newId();
			this.k.innerHTML = '<iframe name="'+this.conf.fr_name+'" style="height:0px;width:0px;" frameBorder="0"></iframe>';
			
			this.fr = this.k.firstChild;
			
			if (window.navigator.userAgent.indexOf("MSIE") >= 0) {
				this.fr.onreadystatechange = function() {
					if (this.readyState == "complete") that._onLoad();
				}
			} else {
				this.fr.onload = function() {
					that._onLoad();
				}
			}
			
		}
		
		var f = document.createElement("DIV");
		f.innerHTML = "<form method='POST' enctype='multipart/form-data' target='"+this.conf.fr_name+"' class='dhx_vault_file_form' name='dhx_vault_file_form_"+window.dhx4.newId()+"'>"+
				"<input type='hidden' name='mode' value='html4'>"+
				"<input type='hidden' name='uid' value='"+id+"'>"+
				"<input type='file' name='"+this.conf.param_name+"' class='dhx_vault_file_input'>"+
				"</form>";
		this.k.appendChild(f);
		
		f.firstChild.lastChild._idd = id;
		
		f.firstChild.lastChild.onchange = function(){
			var name = this.value.match(/[^\/\\]*$/)[0];
			if (!that._beforeAddFileToList(name, null)) return;
			that._addFileToQueue(this);
			this.onchange = null;
			this.parentNode.parentNode.style.display = "none";
			that._addForm();
		}
		
		f = null;
	},
	
	_onLoad: function() {
		
		if (this.conf.uploading) {
			try {eval("dhx4.temp="+this.fr.contentWindow.document.body.innerHTML);} catch(e){};
			var r = dhx4.temp;
			dhx4.temp = null;
			if (r != null) {
				if (typeof(r.state) != "undefined") {
					if (r.state == "cancelled") {
						this._onUploadAbort(this.fr._idd);
						this.fr.contentWindow.document.body.innerHTML = "";
						r = null;
						return;
					} else if (r.state == true) {
						if (typeof(r.size) != "undefined" && !isNaN(r.size)) this.file_data[this.fr._idd].size = r.size;
						this._onUploadSuccess(this.fr._idd, r.name, null, r.extra);
						r = null;
						return;
					}
				}
			}
			this._onUploadFail(this.fr._idd, (r!=null && r.extra != null ? r.extra:null));
		}
		
	},
	
	_addFileToQueue: function(t) {
		var v = t.value.match(/[^\\\/]*$/);
		if (v[0] != null) v = v[0]; else v = t.value;
		//
		this.file_data[t._idd] = { name: v, form: t.parentNode, node: t.parentNode.parentNode, input: t, state: "added"};
		this._addFileToList(t._idd, v, false, "added", 0);
		if (this.conf.auto_start && !this.conf.uploading) this._uploadStart(true);
	},
	
	_removeFileFromQueue: function(id) {
		
		var name = this.file_data[id].name;
		var serverName = (this.conf.uploaded_files!=null&&this.conf.uploaded_files[id]!=null?this.conf.uploaded_files[id].serverName:null);
		
		var fileData = {
			id: Number(id),
			name: name,
			size: this.file_data[id].size||null,
			serverName: serverName,
			uploaded: (this.file_data[id].state == "uploaded"),
			error: (this.file_data[id].state == "fail")
		};
		
		if (this.callEvent("onBeforeFileRemove",[fileData]) !== true) return;
		
		this.file_data[id].input.onchange = null;
		this.file_data[id].form.removeChild(this.file_data[id].input);
		this.file_data[id].node.removeChild(this.file_data[id].form);
		this.file_data[id].node.parentNode.removeChild(this.file_data[id].node);
		this.file_data[id].input = null;
		this.file_data[id].name = null;
		this.file_data[id].form = null;
		this.file_data[id].node = null;
		this.file_data[id].size = null;
		this.file_data[id].state = null;
		this.file_data[id] = null;
		delete this.file_data[id];
		
		this._removeFileFromList(id);
		
		this.callEvent("onFileRemove",[fileData]);
	},
	
	_doUploadFile: function(id) {
		this.fr._idd = id;
		this.file_data[id].form.action = this.conf.url;
		this.file_data[id].form.submit();
	},
	
	_uploadStop: function() {
		if (!this.conf.uploading) return;
		this._onUploadAbort(this.fr._idd);
		this.fr.contentWindow.location.href = (this.conf.url)+(this.conf.url.indexOf("?")<0?"?":"&")+"mode=html4&action=cancel&dhxr"+new Date().getTime();
	},
	
	_unloadEngine: function() {
		
		if (this.k) {
			
			this.conf.fr_name = null;
			this.fr.onreadystatechange = null;
			this.fr.onload = null;
			this.fr.parentNode.removeChild(this.fr);
			this.fr = null;
			
			// remove empty form
			this.k.firstChild.firstChild.lastChild.onchange = null;
			
			this.k.parentNode.removeChild(this.k);
			this.k = null;
			
		}
		
		this._initEngine = null;
		this._addForm = null;
		this._onLoad = null;
		this._addFileToQueue = null;
		this._removeFileFromQueue = null;
		this._doUploadFile = null;
		this._uploadStop = null;
		this._unloadEngine = null;
		
	}
	
};

/****************************************************************************************************************************************************************************************************************/
//	FLASH

dhtmlXVaultObject.prototype.flash = function(){};
dhtmlXVaultObject.prototype.flash.prototype = {
	
	_initEngine: function() {
		
		if (!window.dhtmlXVaultSWFObjects) {
			window.dhtmlXVaultSWFObjects = {
				items: {},
				callEvent: function(id, name, params) {
					return window.dhtmlXVaultSWFObjects.items[id].uploader[name].apply(window.dhtmlXVaultSWFObjects.items[id].uploader,params);
				}
			};
		}
		
		var that = this;
		
		this.conf.swf_obj_id = "dhxVaultSWFObject_"+window.dhx4.newId();
		this.conf.swf_file = this.conf.swf_file+(this.conf.swf_file.indexOf("?")>=0?"&":"?")+"dhxr"+new Date().getTime();
		
		if (navigator.userAgent.indexOf("MSIE") >= 0 || navigator.userAgent.indexOf("Trident") >= 0) {
			// special for IE
			this.buttons.browse.innerHTML += "<div style='position:absolute;width:100%;height:100%;background-color:white;opacity:0;filter:alpha(opacity=0);left:0px;top:0px;'></div>";
		}
		this.buttons.browse.innerHTML += "<div class='dhx_vault_flash_obj'><div id='"+this.conf.swf_obj_id+"'></div></div>";
		swfobject.embedSWF(this.conf.swf_file, this.conf.swf_obj_id, "100%", "100%", "9", null, {ID:this.conf.swf_obj_id,enableLogs:this.conf.swf_logs,GVar:"dhtmlXVaultSWFObjects",paramName:this.conf.param_name,multiple:(this.conf.multiple_files?"Y":"")}, {wmode:"opaque"});
		
		var v = swfobject.getFlashPlayerVersion();
		
		this.conf.progress_type = "percentage";
		
		window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id] = {id: this.conf.swf_obj_id, uploader: this};
	},
	
	_beforeAddFileToQueue: function(name, size) {
		return (this.callEvent("onBeforeFileAdd", [{
			id: null,
			name: name,
			size: size,
			serverName: null,
			uploaded: false,
			error: false
		}])===true);
	},
	
	_addFileToQueue: function(id, name, size) {
		if (window.dhx4.isIE) {
			// focus+hide fix for IE
			var k = document.createElement("INPUT");
			k.type = "TEXT";
			k.style.position = "absolute";
			k.style.left = "0px";
			k.style.top = window.dhx4.absTop(this.buttons["browse"])+"px";
			k.style.width = "10px";
			document.body.appendChild(k);
			k.focus();
			document.body.removeChild(k);
			k = null;
		}
		this.file_data[id] = {name: name, size: size, state: "added"};
		this._addFileToList(id, name, size, "added", 0);
		if (this.conf.auto_start && !this.conf.uploading) this._uploadStart(true);
	},
	
	_removeFileFromQueue: function(id) {
		
		if (!this.file_data[id]) return;
		
		var name = this.file_data[id].name;
		var serverName = (this.conf.uploaded_files!=null&&this.conf.uploaded_files[id]!=null?this.conf.uploaded_files[id].serverName:null);
		
		var fileData = {
			id: Number(id),
			name: name,
			size: this.file_data[id].size,
			serverName: serverName,
			uploaded: (this.file_data[id].state == "uploaded"),
			error: (this.file_data[id].state == "fail")
		};
		if (this.callEvent("onBeforeFileRemove",[fileData]) !== true) return;
		
		var k = false;
		if (this.conf.uploading && this.file_data[id].state == "uploading") {
			this._uploadStop();
			k = true;
		}
		
		swfobject.getObjectById(this.conf.swf_obj_id).removeFileById(id);
		
		this.file_data[id].name = null;
		this.file_data[id].size = null;
		this.file_data[id].state = null;
		this.file_data[id] = null;
		delete this.file_data[id];
		
		this._removeFileFromList(id);
		
		this.callEvent("onFileRemove",[fileData]);
		
		if (k) this._uploadStart();
		
	},
	
	_doUploadFile: function(id) {
		swfobject.getObjectById(this.conf.swf_obj_id).upload(id, this.conf.swf_url);
	},
	
	_uploadStop: function(id) {
		for (var a in this.file_data) if (this.file_data[a].state == "uploading") swfobject.getObjectById(this.conf.swf_obj_id).uploadStop(a);
	},
	
	_unloadEngine: function() {
		
		// remove instance from global storage
		
		if (window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id]) {
			window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id].id = null;
			window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id].uploader = null;
			window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id] = null
			delete window.dhtmlXVaultSWFObjects.items[this.conf.swf_obj_id];
		}
		
		this.conf.swf_obj_id = null;
		
		this._initEngine = null;
		this._addFileToQueue = null;
		this._removeFileFromQueue = null;
		this._doUploadFile = null;
		this._uploadStop = null;
		this._unloadEngine = null;
	}
	
};

dhtmlXVaultObject.prototype.setSWFURL = function(swf_url) {
	this.conf.swf_url = swf_url;
};

/****************************************************************************************************************************************************************************************************************/
//	SILVERLIGHT

dhtmlXVaultObject.prototype.sl = function(){};
dhtmlXVaultObject.prototype.sl.prototype = {
	
	_initEngine: function() {
		
		if (typeof(this.conf.sl_v) == "undefined") this.conf.sl_v = this.getSLVersion();
		
		if (!window.dhtmlXVaultSLObjects) {
			window.dhtmlXVaultSLObjects = {
				items: {},
				callEvent: function(id, name, params) {
					window.dhtmlXVaultSLObjects.items[id].uploader[name].apply(window.dhtmlXVaultSLObjects.items[id].uploader,params);
				}
			};
		}
		
		//var that = this;
		
		this.conf.sl_obj_id = "dhtmlXFileUploaderSLObject_"+window.dhx4.newId();
		
		if (this.conf.sl_v != false) {
			this.buttons["browse"].innerHTML += '<div style="width:100%;height:100%;left:0px;top:0px;position:absolute;">'+
									'<object data="data:application/x-silverlight-2," type="application/x-silverlight-2" width="100%" height="100%" id="'+this.conf.sl_obj_id+'">'+
										'<param name="source" value="'+this.conf.sl_xap+'"/>'+
										'<param name="background" value="Transparent"/>'+
										'<param name="windowless" value="true"/>'+
										'<param name="initParams" value="SLID='+this.conf.sl_obj_id+',LOGS='+this.conf.sl_logs+',GVAR=dhtmlXVaultSLObjects"/>'+
										'<param name="minRuntimeVersion" value="5.0"/>'+
									'</object>'+
								'</div>';
		} else {
			this.buttons["browse"].style.cursor = "wait";
			this.buttons["browse"].title = "";
		}
		
		this.conf.progress_type = "percentage";
		
		window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id] = {id: this.conf.sl_obj_id, uploader: this};
	},
	
	_addFileToQueue: function(id, name, size) {
		this.file_data[id] = {name: name, size: size, state: "added"};
		this._addFileToList(id, name, size, "added", 0);
		if (this.conf.auto_start && !this.conf.uploading) this._uploadStart(true);
	},
	
	_removeFileFromQueue: function(id) {
		if (!this.file_data[id]) return;
		
		var k = false;
		if (this.conf.uploading && this.file_data[id].state == "uploading") {
			this._uploadStop();
			k = true;
		}
		
		document.getElementById([this.conf.sl_obj_id]).Content.a.removeFileById(id);
		
		this.file_data[id].name = null;
		this.file_data[id].size = null;
		this.file_data[id].state = null;
		this.file_data[id] = null;
		delete this.file_data[id];
		
		this._removeFileFromList(id);
		
		if (k) this._uploadStart();
		
	},
	
	_doUploadFile: function(id) {
		document.getElementById(this.conf.sl_obj_id).Content.a.upload(id, this.conf.sl_url, "&mode=sl&dhxr"+new Date().getTime()); // leading "&" required!
	},
	
	_uploadStop: function(id) {
		this.conf.uploading = false;
		for (var a in this.file_data) if (this.file_data[a].state == "uploading") document.getElementById(this.conf.sl_obj_id).Content.a.uploadStop(a);
	},
	
	_unloadEngine: function() {
		
		// remove instance from global storage
		
		if (window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id]) {
			window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id].id = null;
			window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id].uploader = null;
			window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id] = null
			delete window.dhtmlXVaultSLObjects.items[this.conf.sl_obj_id];
		}
		
		this.conf.sl_obj_id = null;
		
		this._initEngine = null;
		this._addFileToQueue = null;
		this._removeFileFromQueue = null;
		this._doUploadFile = null;
		this._uploadStop = null;
		this._unloadEngine = null;
	}
	
};

dhtmlXVaultObject.prototype.setSLURL = function(url) {
	this.conf.sl_url = url;
};

dhtmlXVaultObject.prototype.getSLVersion = function() {
	var v = false;
	if (window.dhx4.isIE) {
		try {
			var t = new ActiveXObject('AgControl.AgControl');
			if (t != null) {
				// loop through [4-x, 0-9] until supported
				var k1 = 4, k2 = 0;
				while (t.isVersionSupported([k1,k2].join("."))) {
					v = [k1,k2];
					if (++k2 > 9) { k1++; k2=0; }
				}
			}
			t = null;
		} catch(e) {};
	} else {
		if (navigator.plugins["Silverlight Plug-In"] != null) {
			v = navigator.plugins["Silverlight Plug-In"].description.split(".");
		}
	}
	return v;
};

/****************************************************************************************************************************************************************************************************************/
// DEFAULT FILES VIEW

dhtmlXVaultObject.prototype.list_default = {
	
	renderFileRecord: function(item, data) {
		
		item.className = "dhx_vault_file dhx_vault_file_"+data.state;
		item.innerHTML = "<div class='dhx_vault_file_param dhx_vault_file_name'>&nbsp;</div>"+
				"<div class='dhx_vault_file_param dhx_vault_file_progress'>&nbsp;</div>"+
				"<div class='dhx_vault_file_param dhx_vault_file_delete'>&nbsp;</div>"+
				"<div class='dhx_vault_file_icon dhx_vault_"+data.icon+"'><div class='dhx_vault_all_icons'></div></div>";
		
		item.childNodes[2]._action = {id: item._idd, data: "delete_file"};
		
		this.updateFileNameSize(item, data);
		
		item = null;
	},
	
	removeFileRecord: function(item) {
		item.childNodes[2]._action = null;
		item = null;
	},
	
	updateFileNameSize: function(item, data) {
		item.childNodes[0].innerHTML = "<div class='dhx_vault_file_name_text'>"+data.name+(!isNaN(data.size) && data.size !== false ? " ("+data.readableSize+")":"&nbsp;")+"</div>";
		item.childNodes[0].title = data.name+(!isNaN(data.size) && data.size !== false ? " ("+data.readableSize+")" : "");
		item = null;
	},
	
	updateFileState: function(item, data) {
		
		var k = false;
		if (this.updateFileStateExtra != null) k = this.updateFileStateExtra(item, data);
		
		if (!k) {
			if (data.state == "added") {
				
				item.className = "dhx_vault_file dhx_vault_file_added";
				item.childNodes[1].className = "dhx_vault_file_param dhx_vault_file_progress";
				item.childNodes[1].innerHTML = "&nbsp;";
			}
			
			if (data.state == "fail") {
				item.className = "dhx_vault_file dhx_vault_file_fail";
				item.childNodes[1].className = "dhx_vault_file_param dhx_vault_file_progress";
				item.childNodes[1].innerHTML = data.str_error;
			}
			
			if (data.state == "uploaded") {
				item.className = "dhx_vault_file dhx_vault_file_uploaded";
				item.childNodes[1].className = "dhx_vault_file_param dhx_vault_file_progress";
				item.childNodes[1].innerHTML = data.str_done;
			}
			
			if (data.state == "uploading_html4" || data.state == "uploading") {
				// gif
				item.className = "dhx_vault_file dhx_vault_file_uploading";
				item.childNodes[1].className = "dhx_vault_file_param dhx_vault_file_uploading";
				item.childNodes[1].innerHTML = "<div class='dhx_vault_progress'><div class='dhx_vault_progress_loader'>&nbsp;</div></div>";
			}
			
		}
		
		item = null;
	},
	
	updateStrings: function() {
		
	}
	
};

// attach to container
if (typeof(window.dhtmlXCellObject) != "undefined" && typeof(dhtmlXCellObject.prototype.attachVault) == "undefined") {

	dhtmlXCellObject.prototype.attachVault = function(conf) {
		
		var obj = document.createElement("DIV");
		obj.style.position = "relative";
		obj.style.width = "100%";
		obj.style.height = "100%";
		obj.style.overflow = "hidden";
		this._attachObject(obj);
		
		obj._attach_mode = true;
		obj._no_border = true;
		
		// keep borders for windows
		if (typeof(window.dhtmlXWindowsCell) != "undefined" && this instanceof dhtmlXWindowsCell) {
			obj._no_border = false;
		}
		
		if (typeof(conf) != "object" || conf == null) conf = {};
		conf.parent = obj;
		if (typeof(conf.skin) == "undefined") conf.skin = this.conf.skin;
		
		this.dataType = "vault";
		this.dataObj = new dhtmlXVaultObject(conf);
		
		// sometimes layout broke vault's dimension
		if (typeof(window.dhtmlXLayoutCell) != "undefined" && this instanceof dhtmlXLayoutCell) {
			this.layout._getMainInst().attachEvent("onExpand", function(ids){
				for (var q=0; q<ids.length; q++) {
					var cell = this.cells(ids[q]);
					if (cell.dataType == "vault" && cell.dataObj != null) cell.dataObj.setSizes();
					cell = null;
				}
			});
		}
		
		conf.parent = null;
		conf = obj = null;
		
		return this.dataObj;
	};
	
};

// bootstrap skin buttons
dhtmlXVaultObject.prototype.buttonCss = {
	bootstrap: {
		browse: "_browse",
		upload: "_upload",
		cancel: "_cancel",
		clear: "_clear"
	}
};

// attach to popup
if (typeof(window.dhtmlXPopup) != "undefined" && typeof(dhtmlXPopup.prototype.attachVault) == "undefined") {
	dhtmlXPopup.prototype.attachVault = function(width, height, conf) {
		return this._attachNode("vault", {width: width||350, height: height||200, conf:conf||{}});
	};
	dhtmlXPopup.prototype._attach_init_vault = function(data) {
		data.conf.parent = this._nodeId;
		document.getElementById(this._nodeId)._no_border = true;
		if (typeof(data.conf.skin) == "undefined") data.conf.skin = this.conf.skin;
		this._nodeObj = new dhtmlXVaultObject(data.conf);
	};
};
