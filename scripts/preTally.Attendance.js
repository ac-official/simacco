/**
 * Attendance related special activities save or listing
 * employees break time defines, monthly , daily , user wise break time listing...
 * Created BY Bilin @ 11-06-2025
*/
;(function ($, window, undefined) {
	preTally.Attendance = {
		
		menuBreakTimes: function() {
			if (!dhxMiddleBlockTabs.cells("menuBreakTimes")) {
				// create the main tab on click
				dhxMiddleBlockTabs.addTab("menuBreakTimes", "<img src='images/icon/calendar_16.png' style='margin-top:2px;' />&nbsp;&nbsp; Employee Break Time &nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='reloadBreakTime'/>", 230);
                dhxMiddleBlockTabs.tabs("menuBreakTimes").setActive();
                // click on the reload button in the tab head
                $(".reloadBreakTime").click(function () {
                    preTally.Attendance.breakFilterList();
                    preTally.Attendance.loadBreakTimeForm(0);
                });
                // split screen into two (listing and adding)
                dhxBreakTimeLayout = dhxMiddleBlockTabs.cells("menuBreakTimes").attachLayout('2U');
                dhxBreakTimeLayout.cells("a").setWidth(400);
                dhxBreakTimeLayout.cells("a").setText("Add Employees Break Time");
                dhxBreakTimeLayout.cells("b").setText("List Employees Break Time");

                // listing section need tool bar and date search
                breakTimeToolbar = dhxBreakTimeLayout.cells("b").attachToolbar();                
                breakTimeToolbar.setIconsPath("images/icon/default_18/");  
                breakTimeToolbar.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(breakTimeToolbar,'breaktime');

                // status bar or pagination and count
                dhxBreakTimeLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('brktim'),
                    height: 35
                }); 

                // attach or includes list grid                
                breakTimeGrid 	= dhxBreakTimeLayout.cells("b").attachGrid();

                // grid initialise and header setting
                breakTimeGrid.setHeader("SlNo\
                ,<div id='breakTypeFlt' style='width: 90%;' placeholder='Break Type'></div>\
                ,<input type='text' id ='brktimUser' style='width: 90%;' placeholder='Enter Staff Name'>,\
				Date, Out Time, In Time <select style='width:98%;' id='brktimark'><option value ='0'>All</option><option value ='1'>Not Mark-IN</option></select>\
				, Time Taken, Remarks, Actions");
                breakTimeGrid.setInitWidths("60,100,*,80,85,85,80,100,140");
	            breakTimeGrid.setColAlign("center,left,left,center,center,center,center,left,left");
	            breakTimeGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
	            breakTimeGrid.setColSorting("na,na,na,na,na,na,na,na,na");	            
	            breakTimeGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
	            preTally.Attendance.commonGridDefine(breakTimeGrid, 'brktim');
	            
	            
	            //filter employee names
	            var filtrInterval;
	            $( "#brktimUser" ).keyup(function(value) {
	                if(filtrInterval) clearInterval(filtrInterval);
	                filtrInterval = setInterval( function() { 
	                    preTally.Attendance.breakFilterList();
	                    clearInterval(filtrInterval); 
	                }, 500);
	            });
	            // break type filter 
	           	breakTimeType = new dhtmlXCombo("breakTypeFlt");
	           	//load cbos
	            breakTimeType.load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=0"), function () {                
	                breakTimeType.setOptionWidth(150);
	            });
	           	breakTimeType.setPlaceholder('Break Type');
	           	// on change the branchs start                 
	            breakTimeType.attachEvent("onChange", function () {
	                var btypVal = breakTimeType.getSelectedValue();
	                if (!breakTimeType.getSelectedValue() && breakTimeType.getComboText()) {
	                    btypVal = breakTimeType.getComboText();
	                }
	                $("#breakTypeFlt").val(btypVal);
	                preTally.Attendance.breakFilterList();
	            }); 

	            $( "#brktimark" ).change(function(value) {
	            	preTally.Attendance.breakFilterList();
	            });    

	           	// on select the rows data 
	            /*breakTimeGrid.attachEvent("onRowSelect", function (id, ind) {
	            	console.log("selected id:",id, " - column id:",ind); 
	            });*/

	            // load the grid list
	            preTally.Attendance.breakFilterList();
	            // load the data entry form
	            preTally.Attendance.loadBreakTimeForm(0);

			}else {
				dhxMiddleBlockTabs.tabs("menuBreakTimes").setActive();
				preTally.Attendance.clearbreakTimeForm();
			}
			preTally.Attendance.commonTabClick();
		},
		breakFilterList: function() {
			// get all break time saved 
			var filterValue = new Array($('#brktimUser').val(), breakTimeToolbar.getValue("rpt_date_from"),breakTimeToolbar.getValue("rpt_date_till"),$('#breakTypeFlt').val(),$( "#brktimark" ).val());
			//preTally.Settings.progressOn(true, dhxLayout, null);
            breakTimeGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBreakTime.php&filter="+filterValue), function() { 
                $('#total_brktim').html("# : "+breakTimeGrid.getUserData("", "Data_Count")+" ");
                //preTally.Settings.progressOff(true, dhxLayout, null);
            });
		},
		loadBreakTimeForm: function(id) {
             //to hide entering break time form if the user have no permission
            if (unescape(JGG1P3bDnUSDL27Mui7KzYjj30UjPdWxCCtGkJSHeuo) == 0) {
                dhxBreakTimeLayout.cells("a").collapse();
                dhxBreakTimeLayout.cells("a").hideHeader();
                dhxBreakTimeLayout.cells("a").hideArrow();
                dhxBreakTimeLayout.cells("a").detachObject();
                dhxBreakTimeLayout.cells("b").hideArrow();
                return 0;
            }

			breakTimeForm 	= dhxBreakTimeLayout.cells("a").attachForm();
			preTally.Settings.progressOn(true, dhxLayout, null);  
			breakTimeForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBreakTime.php&edit_id="+id), function () {
				
				preTally.Settings.progressOff(true, dhxLayout, null);
				// enable search in user cbo
				var usecbos = breakTimeForm.getCombo("bt_user_id");
				if (usecbos) {
					preTally.UserProfile.applyFilterHandler(usecbos);
                    //usecbos.enableFilteringMode(true);
                	usecbos.setOptionWidth(230);
                	breakTimeForm.setItemValue("bt_user_id", breakTimeForm.getItemValue("us_id"));
                	// on change user id stored in hidden fields.
                	usecbos.attachEvent("onChange", function () {
                		var usid = usecbos.getSelectedValue();
                		breakTimeForm.setItemValue("us_id", usid);
                	});
                }
                // save or cancel button click
                breakTimeForm.attachEvent("onButtonClick", function (name) {
                	if (name == "saveBreakTime") {
                		// click on save button
            			var remark = breakTimeForm.getItemValue("remarks");
            			if (remark != '') {
            				// check the remarks fields have invalid charactors
	                        var chkRemark = /^[_a-zA-Z0-9 .,():-]+$/.test(remark);
	                        if ( !chkRemark ) {	                            
	                            dhtmlx.message({ type: "error", text: "Remarks contain invalid characters. Only letters, numbers, spaces, periods (.) and commas (,) are allowed." });
	                            return false; 
	                        }
            			}
            			// check the input fields values are correct
                        var isValid = breakTimeForm.validate();
                        if (isValid) {	
                        	// SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                        	preTally.Attendance.sendSaveBreakTime(2); //18-07-2025
                        }else {
                            dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                        }
                	} else if (name == "CancelBreakTime") {
                		// click on cancel button
                		preTally.Attendance.clearbreakTimeForm();
                	}
                });
                //to hide normal users for entering break time
                /*if (breakTimeForm.getItemValue("allow_other") == 0) {
                	dhxBreakTimeLayout.cells("a").collapse();
	                dhxBreakTimeLayout.cells("a").hideHeader();
	                dhxBreakTimeLayout.cells("a").hideArrow();
	                dhxBreakTimeLayout.cells("a").detachObject();
	                dhxBreakTimeLayout.cells("b").hideArrow();
                }*/

			});
				
		},
        sendSaveBreakTime: function(flag) {
            breakTimeForm.send(preTally.Initialize.encryptURL("warehouse/newBreakTime.php&flag="+flag), function (loader, response) {
                var jsonres = JSON.parse(response);
                if (jsonres.status == 1) {

                    dhtmlx.message({ text: jsonres.message });
                    preTally.Attendance.clearbreakTimeForm();
                    preTally.Attendance.breakFilterList();
                }else if (jsonres.status == 2) { // confirm break time saving
                    dhtmlx.confirm({
                        title: "Confirm",
                        type: "confirm-warning",
                        ok: "Yes", cancel: "No",
                        text: jsonres.message,
                        callback: function (result) {
                            if (result) {
                               preTally.Attendance.sendSaveBreakTime(0); 
                            } else {
                                preTally.Attendance.clearbreakTimeForm();                                  
                            }
                        }
                    });
                } else {
                    dhtmlx.message({ type: "error", text: jsonres.message });
                }
            });
        },
		clearbreakTimeForm: function() {
			// clear form imp fields
			var now 	= new Date();
    		var hours 	= now.getHours();
			var minutes = now.getMinutes();
			breakTimeForm.setItemValue("out_hour", parseInt(hours));
			breakTimeForm.setItemValue("out_minute", parseInt(minutes));
			breakTimeForm.setItemValue("from_time", hours+":"+minutes);
			if (breakTimeForm.getItemValue("btid") > 0 && document.getElementById("bt_in_time_div")) {

				document.getElementById("bt_in_time_div").style.display = "none";
			}
			breakTimeForm.setItemValue("btid", 0);
			if (breakTimeForm.getItemValue("allow_other") == 0) {
				var usid = unescape(JGG1P3bDnUSDL3Mui7KzYjj28UjPdWxCCtGkJSHeuo);
				breakTimeForm.setItemValue("us_id", usid);
			} else {				
				breakTimeForm.setItemValue("bt_user_id", '');
				breakTimeForm.setItemValue("us_id", 0);
                breakTimeForm.getCombo("bt_user_id").setComboValue('');
                breakTimeForm.getCombo("bt_user_id").setComboText('Select User');
			}
			breakTimeForm.setItemValue("remarks", '');
		},
		updateInTime: function(id, usid) {
			// mark in time update             
            $.post(preTally.Initialize.encryptURL("warehouse/newBreakTime.php&flag=1&btid=" + id+"&us_id="+usid), function (data, status) {
            	var jsonres = JSON.parse(data);
        		if (jsonres.status == 1) {

        			dhtmlx.message({ text: jsonres.message });
        			preTally.Attendance.breakFilterList();
        		} else {
        			dhtmlx.message({ type: "error", text: jsonres.message });
        		}
            });
		},
		commonTabClick: function () {
			dhxMiddleBlockTabs.attachEvent("onTabClick", function(id, lastId) {
			    // on click on the tab then clear the forms
			    if ("menuBreakTimes" == id) {
                    dhxMiddleBlockTabs.tabs("menuBreakTimes").setActive();
			    	preTally.Attendance.clearbreakTimeForm();
			    }
			});
		},
		// common status bar for pagination & total
		commonStatusBar: function (type,exportlnk) {
			var exportlink = '';
			var extradiv = '';
			if (typeof exportlnk != "undefined") {
				var functionname  = (exportlnk == "accexp") ? 'preTally.AccountsTeam.export'+type+'()' : 'return false';
				exportlink 	= '<input type="button" value="Export" onclick="'+functionname+'" style="float:right; background-image: url(images/icon/default_18/excel.png);background-repeat: no-repeat; background-position: 4px 1px ; margin-top:3px; padding-left:25px;height: 25px; border: 1px solid;"/>';
			}
			if (type == 'trackexp') {
				extradiv = '<div id="total_amt_'+type+'" style="width:200px !important;float:left; font-weight:bold;">Total Amount: 0</div>';
			}
			return '<div id="total_'+type+'" style="width:100px !important;float:left; font-weight:bold;"># : 0</div>'+extradiv+'<div id="export_'+type+'" style="width:100px !important;float:left; font-weight:bold;">'+exportlink+'</div><div style="float:right;" id="'+type+'_paging"></div>';
		},
		// common GRID related sections
		commonGridDefine: function(gridid, type) {
			gridid.init();
            gridid.setImagePath("assets/grid/codebase/imgs/");
            gridid.setSkin("dhx_skyblue");
            gridid.enableColSpan(true);
            // paginations                
            gridid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);                
            gridid.enablePaging(true, 50, 5, type+"_paging", true);
            gridid.setPagingSkin("toolbar", "dhx_skyblue");

            gridid.attachEvent("onXLS", function () {
    	        preTally.Settings.progressOn(true, dhxLayout, null);  
    	    });
    	    gridid.attachEvent("onXLE", function () {
    	        preTally.Settings.progressOff(true, dhxLayout, null);  
    	    });
		},
		// common Server side GRID related sections
		commonServerGrids: function(gridid) {
			gridid.enableColSpan(true);
            gridid.init();
            gridid.setColumnMinWidth('150', 1);
            
            gridid.attachEvent("onXLS", function () {
    	        preTally.Settings.progressOn(true, dhxLayout, null);  
    	    });
    	    gridid.attachEvent("onXLE", function () {
    	        preTally.Settings.progressOff(true, dhxLayout, null);
    	    });
		},
		// common ajax sync data call 
		commonPostSyn: function(urlDets) {

			var postSynRes = dhx4.ajax.postSync(preTally.Initialize.encryptURL(urlDets), encodeURI(1));
			
			return JSON.parse(postSynRes.xmlDoc.responseText);
		},
		// list all break time of users excel export too
		listBreakTimes: function() {
			if (!dhxMiddleBlockTabs.cells("listBreakTimes")) {
				// create the main tab on click
				dhxMiddleBlockTabs.addTab("listBreakTimes", "<img src='images/icon/calendar_16.png' style='margin-top:2px;' />&nbsp;&nbsp; Employees Break Time List &nbsp; ", 250);
                dhxMiddleBlockTabs.tabs("listBreakTimes").setActive();
                // layout define
                listBrkTimeLayout = dhxMiddleBlockTabs.cells("listBreakTimes").attachLayout("1C");
                listBrkTimeLayout.cells("a").hideHeader();

                // tool bar with filter strt
                listBrkTimeTbr = listBrkTimeLayout.cells("a").attachToolbar();
                listBrkTimeTbr.setIconsPath("images/icon/default_18/");
                listBrkTimeTbr.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(listBrkTimeTbr,'listBreak');

                // status bar or pagination and count
                listBrkTimeLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('ltbrktim'),
                    height: 35
                }); 

                // grid base section declaration 
                listBrkTimeGrid = listBrkTimeLayout.cells("a").attachGrid();
                // find the break time types
                var breakTypes 	= preTally.Attendance.commonPostSyn("requisites/customJsonData.php&flag=1");
                var gridhead 	= "Slno, Staff Name <input type='text' id ='fltBTUser' style='width: 90%;' placeholder='Enter Staff Name'>"
                +",Branch Name <div id='fltBTBranch' style='width: 90%;' placeholder='Select Branch'></div>"
                +", Date";	
                var gridwidth 	= "65,*,*,80";
                var gridalign	= "center,left,left,center";
                var gridcoltype = "ro,ro,ro,ro";
                var gridcolsort = "na,na,na,na";
                var gridtooltip = "false,false,false,false";
                (breakTypes.data).forEach(function(types) {
                	gridhead 	+= ","+types.title;
                	gridwidth	+= ",100";
                	gridalign 	+= ",left";
                	gridcoltype += ",ro";
                	gridcolsort += ",na";
                	gridtooltip += ",false";
                });
                gridhead 	+= ",Total Personal <select style='width:98%;' id='fltBTBStatus'><option value ='0'>All</option><option value ='1'>Not Mark-IN</option><option value ='2'>Time Exceed</option></select>";
                gridwidth	+= ",110";
                gridalign 	+= ",left";
                gridcoltype += ",ro";
                gridcolsort += ",na";
                gridtooltip += ",false";
                listBrkTimeGrid.setHeader(gridhead);
                listBrkTimeGrid.setInitWidths(gridwidth);
                listBrkTimeGrid.setColAlign(gridalign);
	            listBrkTimeGrid.setColTypes(gridcoltype);
	            listBrkTimeGrid.setColSorting(gridcolsort);	            
	            listBrkTimeGrid.enableTooltips(gridtooltip);	            
                // common grid define section
                preTally.Attendance.commonGridDefine(listBrkTimeGrid, 'ltbrktim');

                // grid filter branch combo define
                fltBTBranch = new dhtmlXCombo("fltBTBranch");
                fltBTBranch.setPlaceholder('Search Branch');
                //load cbos
	            fltBTBranch.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check&ofid="+unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)), function () {                
	                preTally.UserProfile.applyFilterHandler(fltBTBranch);
	                fltBTBranch.setOptionWidth(230);
	            });
	            // on change the branchs start                 
	            fltBTBranch.attachEvent("onChange", function () {
	                var branchVal = fltBTBranch.getSelectedValue();
	                if (!fltBTBranch.getSelectedValue() && fltBTBranch.getComboText())
	                    branchVal = fltBTBranch.getComboText();
	                $("#fltBTBranch").val(branchVal);
	                preTally.Attendance.breakTimeRptFilter();
	            });
	            // staff name enter filter start
	            var filtrInterval;
	            $( "#fltBTUser" ).keyup(function(value) {
	                if(filtrInterval) clearInterval(filtrInterval);
	                filtrInterval = setInterval( function() { 
	                    preTally.Attendance.breakTimeRptFilter();
	                    clearInterval(filtrInterval); 
	                }, 500);
	            });

	            // break time status and types filter start
	            $( "#fltBTBStatus" ).change(function(value) {
	            	preTally.Attendance.breakTimeRptFilter();
	            }); 

	            //loading data from database to grid
	            preTally.Attendance.breakTimeRptFilter();
            } else {
            	dhxMiddleBlockTabs.tabs("listBreakTimes").setActive();
            }
		},
		// loading break time data of employees based on filter
		breakTimeRptFilter : function() {
			// get all break time saved 
			var filterValue = new Array($('#fltBTUser').val(), listBrkTimeTbr.getValue("rpt_date_from"),listBrkTimeTbr.getValue("rpt_date_till"),$('#fltBTBranch').val(),$( "#fltBTBStatus" ).val());
			listBrkTimeGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBreakTimeRpt.php&filter="+filterValue), function() { 
               $('#total_ltbrktim').html("# : "+listBrkTimeGrid.getUserData("", "Data_Count")+" ");
            });
		},
        // export the filtered data into excel format 
        breakTimeRptExport: function() {
            var filters     = new Array($('#fltBTUser').val(), listBrkTimeTbr.getValue("rpt_date_from"),listBrkTimeTbr.getValue("rpt_date_till"),$('#fltBTBranch').val(),$( "#fltBTBStatus" ).val());
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                preTally.Initialize.encryptURL('warehouse/ExportBreakTime.php'),
                { filter : filters },
                function(data) {
                fileName = data.split("XL_");
                if(fileName[1]) {
                     document.location ="uploads/excelFile/"+fileName[1];
                }
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },




	};
})(jQuery, this);