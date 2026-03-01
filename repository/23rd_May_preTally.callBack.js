;(function ($, window, undefined) {
	preTally.callBack = {
		
		dragACL: function(from, to) {
			
			dhtmlx.confirm({
				//type:"confirm-warning",
				title:"ACL Permissions",
				ok:"Yes", cancel:"No",
				text:"Confirm Moving Node <b>" + dhxACLTree.getItemText(from) + "</b> to Item <b>" + dhxACLTree.getItemText(to) + "</b>?",
				callBack:function(result){
					return true;
				}
			});
		},
		
		//----------------- Balance Sheet Manage Window --------------------//
		balSheetWindow: function(winData) {
			if(winData['initialize'].window(winData['id']))
				winData['initialize'].window(winData['id']).close();
			var win = winData['initialize'].createWindow(winData['id'], winData['X'], winData['Y'], winData['W'], winData['H']);
				//win.denyResize();
				//win.denyPark();
				win.setText(winData['title']);	
				//win.setIcon(winData['iconEnable'], winData['iconDisable']);
				
			
			balSheetLayout = win.attachLayout("2U");
			balSheetLayout.cells("a").hideHeader();
			balSheetLayout.cells("b").hideHeader();
			
			dhxBalSheetForm = balSheetLayout.cells("a").attachForm();
			dhxBalSheetForm.loadStruct("requisites/newBalSheet.php?x=" + new Date().getTime(), function() {
				AF_Particulars = dhxBalSheetForm.getCombo("FL_Title");	
				AF_Particulars.enableFilteringMode(true);
				AF_Particulars.loadXML("requisites/data.xml");
				
				//AF_Particulars.attachEvent("onChange", preTally.callBack.onChangeFunc);
				//AF_Particulars.attachEvent("onSelectionChange", preTally.callBack.onSelectionChangeFunc);
				AF_Particulars.attachEvent("onBlur", preTally.callBack.onBlur_AF_Particulars);
				
				AF_Users = dhxBalSheetForm.getCombo("qwerty");	
				AF_Users.enableFilteringMode(true);
				AF_Users.loadXML("requisites/users.xml");
				
				//AF_Particulars.attachEvent("onChange", preTally.callBack.onChangeFunc);
				//AF_Particulars.attachEvent("onSelectionChange", preTally.callBack.onSelectionChangeFunc);
				AF_Users.attachEvent("onBlur", preTally.callBack.onBlur_AF_Users);
			});	
			
			
			
			
			
		},
		doCompleteBalSheet : function () {
			$('.balSheet_popup').prop("onclick", null);
			$('.balSheet_item_popup').prop("onclick", null);
			$('.balSheet_popup').click(function(e) {
				preTally.callBack.hideCompleteBalSheetPopup();
				preTally.callBack.hideItemBalSheetPopup();
				preTally.callBack.showCompleteBalSheetPopup(this);
			});
			
			$('.balSheet_item_popup').click(function(e) {
				preTally.callBack.hideCompleteBalSheetPopup();
				preTally.callBack.hideItemBalSheetPopup();
				preTally.callBack.showItemBalSheetPopup(this);
			});
		},
		showCompleteBalSheetPopup : function (inp) {
			console.log(1);
			var x = getAbsoluteLeft(inp);
			var y = getAbsoluteTop(inp);
			var w = inp.offsetWidth;
			var h = inp.offsetHeight;
			
			if (!dhxCompleteBalSheet) {
				dhxCompleteBalSheet = new dhtmlXPopup({
					mode: "left"
				});
				//dhxCompleteBalSheet.attachHTML("You can enter some text into here");
			} else {
				//dhxCompleteBalSheet.detachEvent(onShowFilterEvent);
			} 
			
			/*dhxCompleteBalSheet.attachList("name,price1,price2,price3,price4", [{
				id: "Quantity",
				name: "Unit Price",
				price1: "Quantity",
				price2: "ASDF",
				price3: "QWERTY",
				price4: "POIU"
			}, dhxCompleteBalSheet.separator, {
				id: 1,
				name: "<input type='text' value='asdf' />",
				price1: "<input type='text' value='asdf' />",
				price2: "<input type='text' value='asdf' />",
				price3: "<input type='text' value='asdf' />",
				price4: "<input type='text' value='asdf' />"
			}, dhxCompleteBalSheet.separator, {
				id: 2,
				name: "<input type='text' value='asdf' />",
				price1: "<input type='text' value='asdf' />",
				price2: "<input type='text' value='asdf' />",
				price3: "<input type='text' value='asdf' />",
				price4: "<input type='text' value='asdf' />"
			}, dhxCompleteBalSheet.separator, {
				id: 3,
				name: "<input type='text' value='asdf' />",
				price1: "<input type='text' value='asdf' />",
				price2: "<input type='text' value='asdf' />",
				price3: "<input type='text' value='asdf' />",
				price4: "<input type='text' value='asdf' />"
			}, dhxCompleteBalSheet.separator, {
				id: 4,
				name: "<input type='text' value='asdf' />",
				price1: "<input type='text' value='asdf' />",
				price2: "<input type='text' value='asdf' />",
				price3: "<input type='text' value='asdf' />",
				price4: "<input type='text' value='asdf' />"
			}, dhxCompleteBalSheet.separator, {
				id: 5,
				name: "<input type='text' value='asdf' />",
				price1: "<input type='text' value='asdf' />",
				price2: "<input type='text' value='asdf' />",
				price3: "<input type='text' value='asdf' />",
				price4: "<input type='text' value='asdf' />"
			}]);*/
			
			dhxBalSheetDetailForm = dhxCompleteBalSheet.attachForm();
			dhxBalSheetDetailForm.loadStruct("requisites/detailBalSheet.php?x=" + new Date().getTime(), function() {
				dhxCompleteBalSheet.show(x, y, w, h);
			});
	
 
			
		},
		hideCompleteBalSheetPopup: function () {//----------------- Hide Filter PopUp --------------------//
			if (dhxCompleteBalSheet)
				dhxCompleteBalSheet.hide();
		},
		showItemBalSheetPopup : function (inp) {
			console.log(2);
			var x = getAbsoluteLeft(inp);
			var y = getAbsoluteTop(inp);
			var w = inp.offsetWidth;
			var h = inp.offsetHeight;
			
			if (!dhxItemBalSheet) {
				dhxItemBalSheet = new dhtmlXPopup({
					mode: "left"
				});
				//dhxCompleteBalSheet.attachHTML("You can enter some text into here");
			} else {
				//dhxCompleteBalSheet.detachEvent(onShowFilterEvent);
			} 
			dhxBalSheetItemForm = dhxItemBalSheet.attachGrid();
			dhxBalSheetItemForm.init();
			dhxBalSheetItemForm.setAwaitedRowHeight(50);
			//dhxBalSheetItemForm.loadXML("requisites/itemBalSheet.php?x=" + new Date().getTime(), function() { 
			dhxBalSheetItemForm.loadXML("requisites/itemBalSheet.php",function() { 
				dhxItemBalSheet.show(x, y, w, h);
			});
		},
		hideItemBalSheetPopup: function () {//----------------- Hide Filter PopUp --------------------//
			if (dhxItemBalSheet)
				dhxItemBalSheet.hide();
		},
		onChangeFunc: function () {
			console.log("" + 'onChange' + " event has occured");
			return true;
		},
		onSelectionChangeFunc: function () {
			console.log("Selection was changed");
			return true;
		},
		onBlur_AF_Particulars: function () {
			//console.log("" + AF_Particulars.getSelectedValue() + " event has occured");
			if(AF_Particulars.getSelectedValue() % 2 == 0) {
				
				
				dhxBalSheetCusForm = balSheetLayout.cells("b").attachForm();
				dhxBalSheetCusForm.loadStruct("requisites/newBalSheet_op1.php?x=" + new Date().getTime(), function() {
					
				});
				
				
			} else {
				
				dhxBalSheetCusForm = balSheetLayout.cells("b").attachForm();
				dhxBalSheetCusForm.loadStruct("requisites/newBalSheet_op2.php?x=" + new Date().getTime(), function() {
					
				});
				
			}
			return true;
		},
		onBlur_AF_Users: function (id) {
			console.log("" + id + " event has occured");
			return true;
		},
		onRowBalSheet: function() {
			alert(1);
		},


		mainTBEvent: function() {
			dhxToolbar.attachEvent("onClick", function(id) {
				if(id == 'balSheet') {
					
					var winData = new Array();
					winData = {
						'initialize' 	: dhxWins,
						'title' 		: 'Balance Sheet Window', 
						'id' 			: 'balSheetWindow', 
						//'iconEnable' 	: 'home_20.png', 
						//'iconDisable' 	: 'key_20.png', 
						'X' 			: 300, 
						'Y' 			: 60, 
						'W' 			: 800, 
						'H' 			: 400
					};
					
					 
					preTally.callBack.balSheetWindow(winData);
				}
			});
		},
		mainTBMenu: function() {
			

		},
		mainTBProfile: function() {

		},
		doOnNewFilter: function() {
			alert(1);
		},
		mainTBUsers: function() {
			
		},
		
		
		
		
		
		menuSignOutProfile : function() {
			dhtmlx.confirm({
				//type:"confirm-warning",
				title:"Logout Pre-Tally",
				ok:"Yes", cancel:"No",
				text:"Confirm Logout Pre-Tally..",
				callback:function(result){
					if(result == true) location.href = 'logout.php';
				}
			});
		},
		menuMyProfile : function() {
			if(!dhxMiddleBlockTabs.cells("menuMyProfile")){
				dhxMiddleBlockTabs.addTab("menuMyProfile","<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;My Profile",130);
				dhxMiddleBlockTabs.setTabActive("menuMyProfile");
			
			} else {
				dhxMiddleBlockTabs.setTabActive("menuMyProfile");
			}
		},
		menuMyPassword : function() {
			if(!dhxMiddleBlockTabs.cells("menuMyPassword")){
				dhxMiddleBlockTabs.addTab("menuMyPassword","<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Password",130);
				dhxMiddleBlockTabs.setTabActive("menuMyPassword");
			
			} else {
				dhxMiddleBlockTabs.setTabActive("menuMyPassword");
			}
		},
		menuNewUser : function () {
			if(!dhxMiddleBlockTabs.cells("new_user")){
				dhxMiddleBlockTabs.addTab("new_user","<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;New User",120);
				dhxMiddleBlockTabs.setTabActive("new_user");
				
				ptNewUserToolbar = dhxMiddleBlockTabs.cells("new_user").attachToolbar();
				ptNewUserToolbar.loadXML("requisites/newUserToolbar.php?" + new Date().getTime(), function () {
					//alert(!ptNewUserToolbar.getItemState('authentication_details'));
					//ptNewUserToolbar.setItemState('authentication_details', true);
					preTally.callBack.disabledButton('authentication_details', 'select_16.gif');
					ptNewUserToolbar.attachEvent("onClick", function(id) {
						//ptNewUserToolbar.attachEvent("onClick", function(id) {
						
						
						ptNewUserToolbar.forEachItem( function(id){
							if(!ptNewUserToolbar.isEnabled(id)) {
								ptNewUserToolbar.enableItem(id);
							}
						});
						
						preTally.callBack.disabledButton(id, 'select_16.gif');
						
						//alert(id);
						if(id == 'authentication_details') {
							preTally.callBack.attachNewUserForm('au');
						}
						
						if(id == 'personal_details') {
							preTally.callBack.attachNewUserForm('pe');
						}
						
						if(id == 'educational_details') {
							preTally.callBack.attachNewUserForm('ed');
						}
					
					});
					
					preTally.callBack.attachNewUserForm('au');
				
				});
				ptNewUserToolbar.setAlign('right');
			
			
			} else {
				dhxMiddleBlockTabs.setTabActive("new_user");
			}
		},
		
		menuListUser : function() {
			if(!dhxMiddleBlockTabs.cells("list_user")){
				dhxMiddleBlockTabs.addTab("list_user","<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;List Users",130);
				dhxMiddleBlockTabs.setTabActive("list_user");
				
				listPreTallyUser = dhxMiddleBlockTabs.cells("list_user").attachGrid();
					listPreTallyUser.loadXML("requisites/listUsers.php",function() { 
				
				});
			
			
			} else {
				dhxMiddleBlockTabs.setTabActive("list_user");
			}
		},
		menuAccessLevelUser : function() {
			if(!dhxMiddleBlockTabs.cells("access_level")){
				dhxMiddleBlockTabs.addTab("access_level","<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Access Control Levels",200);
				dhxMiddleBlockTabs.setTabActive("access_level");
			
			} else {
				dhxMiddleBlockTabs.setTabActive("access_level");
			}
		},
		attachNewUserForm : function (r) {
			
			if (new_user_form) {
				new_user_form.unload();
				new_user_form = null;
			}
	
			new_user_form = dhxMiddleBlockTabs.cells("new_user").attachForm();
			new_user_form.loadStruct("requisites/newUser.php?r=" + r + "&" + new Date().getTime(), function() {

				if(r == 'au') {
					
					$('#changeProfileImage').click(function(){
						uploadSrc 	= '#profileImage';
						uploadPath 	= 'uploads/profileImage/';
						uploadForm	= new_user_form;
						uploadField	= 'US_Image';
						
						$('#uptext').trigger('click');
					});
					
					new_user_form.attachEvent("onButtonClick", function(name) {
						var newUserProfile = new_user_form.validate();
						if(newUserProfile) {
							preTally.callBack.progressOn(true,null);
							new_user_form.send('warehouse/newUser.php', function(loader, response) {
								preTally.callBack.progressOff(true,null);
								dhtmlx.message({text: response});
								alert(response);
							});
						}
					});
				}
			});

		},
		disabledButton : function (id,img) {
			ptNewUserToolbar.disableItem(id);
			ptNewUserToolbar.setItemImageDis(id, 'images/icon/' + img);
		},
		mainTBSettings: function() {
			
		},
		mainToolbar: function() {
			
			preTally.callBack.mainTBEvent();
			preTally.callBack.mainTBMenu();
			preTally.callBack.mainTBProfile();
			preTally.callBack.mainTBUsers();	
			preTally.callBack.mainTBSettings();					
		},
		attendanceSave: function(rowId, cellInd, state, dataDate, dataME) {
    		//alert("User clicked on checkbox or radiobutton on row " + rowId + " and cell with index " + cellInd + ".State changed to " + state+'--'+dataDate);
			var dataDate = ptPayrollAttendence.cells(rowId,cellInd).getAttribute("dataDate");
			var dataME = ptPayrollAttendence.cells(rowId,cellInd).getAttribute("dataME");
			var userID = ptPayrollAttendence.cells(rowId,cellInd).getAttribute("userID");
			
			$.post(
				'warehouse/attendance.php',
				{dataDate: dataDate, dataME: dataME, userID:userID, state:state},
				function(responseText){
					
				},
				"html"
			);
		
		
			//alert(dataDate+' -- '+dataME+' -- '+userID);
		},
		updateUpload: function (name){
			if(uploadSrc)   $(uploadSrc).attr('src', uploadPath + name);
			if(uploadField) $(uploadField).val(name);	
			if(uploadField && uploadForm) uploadForm.setItemValue(uploadField, name);
		},
		progressOn: function(fullLayout,layout) {
			if (fullLayout) {
				dhxLayout.progressOn();
			} else {
				dhxLayout.items[layout].progressOn();
			}
		},
		progressOff: function(fullLayout, layout) {
			if (fullLayout) {
				dhxLayout.progressOff();
			} else {
				dhxLayout.items[layout].progressOff();
			}
		},
		rc4: function(key, str){
			var s = [], j = 0, x, res = '';
			for (var i = 0; i < 256; i++) {
				s[i] = i;
			}
			for (i = 0; i < 256; i++) {
				j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
				x = s[i];
				s[i] = s[j];
				s[j] = x;
			}
			i = 0;
			j = 0;
			for (var y = 0; y < str.length; y++) {
				i = (i + 1) % 256;
				j = (j + s[i]) % 256;
				x = s[i];
				s[i] = s[j];
				s[j] = x;
				res += String.fromCharCode(str.charCodeAt(y) ^ s[(s[i] + s[j]) % 256]);
			}
			return res;
		}		
		
	};
})(jQuery, this);