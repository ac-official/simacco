;var companyFltrCombo = {};
;(function($, window, undefined) {    
    preTally.ManageTracks = {
        viewTracks: function() {
            if (!dhxMiddleBlockTabs.cells("manageTracksTab")) {
                dhxMiddleBlockTabs.addTab("manageTracksTab", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Manage Tracks", 150);
                dhxMiddleBlockTabs.tabs("manageTracksTab").setActive();
                var manageTracksLayout = dhxMiddleBlockTabs.cells("manageTracksTab").attachLayout('2U');
                manageTracksLayout.cells("a").setWidth(200);
                manageTracksLayout.cells("a").setText("");
                manageTracksLayout.cells("b").setText("Manage Tracks");
                manageTracksLayout.cells("b").attachStatusBar({text:"<div id='mngTrack_paging'></div>",height:23});
                manageTracksGrid = manageTracksLayout.cells("b").attachGrid();                               
               // manageTracksGrid.setHeader("Sl No:,<input type='text' class='stockrpt_text_filter' id ='itmSRF' style='width: 90%;' placeholder='Track ID'>,<input type='text' class='stockrpt_text_filter' id='addSRF' style='width: 90%;' placeholder='Added By'>,<input type='text' class='stockrpt_text_filter' id='brnSRF' style='width: 90%;' placeholder='Branch'>,\<select style = 'width:100px;' class='select_filter' id='statusChange'><option value =''>All</option><option value ='0'>Suspend</option><option value ='1'>Active</option><option value ='2'>Process Finished</option><option value ='3'>Delivered</option></select>,");
                manageTracksGrid.setHeader("SlNo,<input type='text' class='track_text_filter' id ='trackIDMT' style='width: 90%;' placeholder='Track ID'>,<input type='text' class='track_text_filter' id='addMT' style='width: 90%;' placeholder='Added By'>,<input type='text' class='track_text_filter' id='brnMT' style='width: 90%;' placeholder='Branch'>,\<select style = 'width:100px;' class='select_filter' id='statusChange'><option value =''>All</option><option value ='1'>Active</option><option value ='0'>Suspend</option></select>,");
                manageTracksGrid.setInitWidths("60,140,*,120,120,100");				
                if(unescape(JGG1P3bDnUSDL2Mui7KzYjj29UjPdWxCCtGkJSHeuo)==1)
                manageTracksGrid.setColTypes("ro,ed,ro,ro,combo,ro");
                else
                manageTracksGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                manageTracksGrid.setColAlign("center,left,left,left,left,center");
                manageTracksGrid.enableColSpan(true);
                manageTracksGrid.enableEditEvents(true,false,true);
                //manageTracksGrid.enableSmartRendering(true,50);
                manageTracksGrid.init();
                manageTracksGrid.setImagePath("assets/grid/codebase/imgs/");
                manageTracksGrid.setSkin("dhx_skyblue")
                manageTracksGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                manageTracksGrid.enablePaging(true,50,5,"mngTrack_paging",true);
                manageTracksGrid.enableTooltips("false,false,false,false,false");
                manageTracksGrid.setPagingSkin("toolbar", "dhx_skyblue");
                preTally.Settings.progressOn(true, dhxLayout, null);
                manageTracksGrid.loadXML(preTally.Initialize.encryptURL("requisites/manageTracks.php"), function() {
                    manageTracksGrid.enableTooltips("false,false,false,false,false");
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    $("#statusChange").change(function() {
                        preTally.ManageTracks.applyFilter(); 
                    });
                    
                    var filtrInterval ;
                    $( ".track_text_filter" ).keyup(function(value) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            preTally.ManageTracks.applyFilter(); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    });
                    
                    oldTracks = [];
                    manageTracksGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                         
                        if(stage == 0) {
                            if(cInd == 1) {
                              // trackName = manageTracksGrid.cells(rId,cInd).getValue();
                               oldTracks[rId] = manageTracksGrid.cells(rId,cInd).getValue();
                            }
                            else if(cInd == 4) {
                                selectop = manageTracksGrid.cells(rId,cInd).getValue();
                                statusCombo  = manageTracksGrid.cells(rId,cInd).getCellCombo();
                                statusCombo.readonly(true);
                                preTally.ManageTracks.selectStatusOptions(selectop,'');
                            }
                        }
                        if(stage == 2){
                            if(cInd == 1) {
                                newtrackName = manageTracksGrid.cells(rId,cInd).getValue();
                                //var regex = /^[a-zA-Z0-9 -_]+$/; 
                                var regex =  /^[a-z0-9 _\-]+$/i;
                                if (regex.test(newtrackName)) 
                                { 
                                    if(oldTracks[rId] != newtrackName && newtrackName != "") {
                                        dhtmlx.confirm({
                                            title: "Confirm Edit",
                                            type:"confirm-warning",
                                            ok  : "Yes", cancel : "No",
                                            text: "Do you want to change the track ID from "+oldTracks[rId]+" to "+newtrackName+" ?",
                                            callback: function(response) {   
                                                if(response){
                                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                                    params="TR_Id="+rId +"&TR_Track="+newtrackName+"&type=trackName";   
                                                    $.post(preTally.Initialize.encryptURL("warehouse/manageTracks.php&"+params), function( data ){
                                                        /*manageTracksGrid.updateFromXML("requisites/manageTracks.php",true,true,function() {
                                                            preTally.Settings.progressOff(true, dhxLayout, null);
                                                        });  */
                                                        
                                                        if(data == "success") {
                                                            dhtmlx.message({text: "Successfully updated track ID"});
                                                        }
                                                        else if(data == "exists") {
                                                            dhtmlx.message({text: "Track ID already exists. Please try with another."});
                                                            manageTracksGrid.cells(rId,cInd).setValue(oldTracks[rId]);
                                                        }
                                                        else if(data == "invalid"){
                                                            dhtmlx.message({text: "Please enter a valid track ID"});
                                                            manageTracksGrid.cells(rId,cInd).setValue(oldTracks[rId]);
                                                        }
                                                        preTally.ManageTracks.applyFilter();
                                                    });  
                                                }
                                                else {
                                                    manageTracksGrid.cells(rId,cInd).setValue(oldTracks[rId]);
                                                }
                                            }
                                        }); 
                                    }
                                }
                                else {
                                    dhtmlx.message({text: "Please enter a valid track ID"});
                                    manageTracksGrid.cells(rId,cInd).setValue(oldTracks[rId]);
                                    /*manageTracksGrid.selectCell(1,1,true,true,true);
                                    manageTracksGrid.editCell();*/
                                }
                            }
                            else if(cInd == 4) {
                                var newStatus = statusCombo.getSelectedValue();
                                if(newStatus != selectop && newStatus != null) {
                                    statusArray = ["Suspend", "Active", "Process Finished", "Delivered"];
                                    dhtmlx.confirm({
                                        title: "Confirm Edit",
                                        type:"confirm-warning",
                                        ok  : "Yes", cancel : "No",
                                        text: "Do you want to change the status from "+selectop+" to "+statusArray[newStatus]+" ?",
                                        callback: function(response) {
                                            if(response){
                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                params="TR_Id="+rId +"&TR_Status="+newStatus+"&type=status";   
                                                $.post(preTally.Initialize.encryptURL("warehouse/manageTracks.php&"+params), function( data ){
                                                    preTally.ManageTracks.selectStatusOptions(newStatus,'');
                                                    preTally.ManageTracks.applyFilter();
                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    if(data == "success")
                                                        dhtmlx.message({text: "Successfully updated track status"});
                                                }); 
                                            }
                                            else { 
                                                preTally.ManageTracks.selectStatusOptions(selectop,rId); 
                                            }
                                        }
                                    }); 
                                }
                            }
                        }
                        return true;
                    });
                });
            } 
            else {
                dhxMiddleBlockTabs.tabs("manageTracksTab").setActive();
            }
        },
        selectStatusOptions : function(value,rId) {
            statusCombo.clearAll();
            if(value != 0 && value != 'Suspend' ) {
                statusCombo.addOption('0','Suspend');
            }
            else if((value == 0 || value == 'Suspend') && rId != "" ) {
                manageTracksGrid.cells(rId,'4').setValue("Suspend");
            }
            
            if(value != 1 && value != 'Active') {
                statusCombo.addOption('1','Active');
            }
            else if((value == 1 || value == 'Active') && rId != "" ) {
                manageTracksGrid.cells(rId,'4').setValue("Active");
            }
            
            /*if(value != 2  && value != 'Process Finished') {
                statusCombo.addOption('2','Process Finished');
            }
            else if((value == 2 || value == 'Process Finished') && rId != "" ) {
                manageTracksGrid.cells(rId,'4').setValue("Process Finished");
            }
            
            if(value != 3  && value != 'Delivered') {
                statusCombo.addOption('3','Delivered');
            }
            else if((value == 3 || value == 'Delivered') && rId != "" ) {
                manageTracksGrid.cells(rId,'4').setValue("Delivered");
            }*/
            
        },
        viewTrackEntries : function(trackID,inp) {
            dhxTrackWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            trackWin = dhxTrackWin.createWindow("wins_track", x, y, 750, 400);
            trackWin.button("minmax1").hide();
            trackWin.button("minmax2").hide();
            trackWin.button("park").hide();
            trackWin.center();
            trackWin.setModal(true);
            trackWin.setText("Track Entries");
            manageTrackGrid = trackWin.attachGrid();
            manageTrackGrid.enableColSpan(true);
            preTally.Settings.progressOn(true, trackWin, null);
            params = "trname=" + trackID;
            manageTrackGrid.enableTooltips("false,false,false,false,false,false,false");
            manageTrackGrid.loadXML(preTally.Initialize.encryptURL("requisites/searchTrack.php&" + params), function() {
                preTally.Settings.progressOff(true, trackWin, null);
            });
        },
        applyFilter : function() {
            var filterValue = new Array($('#trackIDMT').val(), $('#addMT').val(),$('#brnMT').val(),$('#statusChange').val());
            manageTracksGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            manageTracksGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/manageTracks.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        // ------------------------------------------------------------------ //
        // - Search all track based on the date filter selected by the user - //
        //  ------   Created at 21-05-2025 new menu section BY Bilin -------  //
        searchAllTrack: function() {
            if (!dhxMiddleBlockTabs.cells("list_all_tracks")) {
                dhxMiddleBlockTabs.addTab("list_all_tracks", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Trackwise Reports ", 180);
                dhxMiddleBlockTabs.tabs("list_all_tracks").setActive();
                dhxAllTracksLayout = dhxMiddleBlockTabs.cells("list_all_tracks").attachLayout("1C");                
                dhxAllTracksLayout.cells("a").hideHeader();
                // tool bar initialise
                ptAllTrksTlbr = dhxAllTracksLayout.cells("a").attachToolbar();
                ptAllTrksTlbr.setIconsPath("images/icon/default_18/");
                ptAllTrksTlbr.setAlign('left');
                // tool bar combo data month and year
                var Days_Options    = [];
                var Months_Options  = [];
                var Years_Options   = [];
                var monthNames      = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var todayDt = Date.today().getDate();
                for (i = todayDt; i >= 1; i--) {
                    if (i < 10) {
                        i = '0' + i;
                    }
                    if (i == todayDt) {
                        Days_Options.push([i, 'obj', 'Today', "calendar_D.png"]);
                    } else {
                        Days_Options.push([i, 'obj', i, "calendar_D.png"]);
                    }
                }
                //Date.today().getMonth() + 1
                for (i = 12; i >= 1; i--) {
                    j = i;
                    if (j < 10) {   j = '0' + i; }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (i = Date.today().getFullYear(); i >= 2024; i--) {
                    Years_Options.push([i, 'obj', i, "calendar_Y.png"]);
                }                
                // company filter combo loading - 05-01-2026
                ptAllTrksTlbr.addText('text_company', '2', 'Office');
                ptAllTrksTlbr.addText('rpt_company', '3', '<div id="rpt_company_combo"></div>');
                companyFltrCombo = new dhtmlXCombo("rpt_company_combo");
                companyFltrCombo.load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=9&selted="+unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)), function () {
                    companyFltrCombo.setOptionWidth(250);
                    companyFltrCombo.attachEvent("onChange", function (id) {
                        preTally.ManageTracks.filterTrackLoads();
                    });
                });                
                ptAllTrksTlbr.addSpacer("rpt_company");
                // end the company filter - 05-01-2026
                // track search implement @ 21-01-2026
                ptAllTrksTlbr.addText("text_track", '4', "Track");
                ptAllTrksTlbr.addInput("rpt_trackno", '5', "", 75);
                var typecbos = [];
                typecbos.push(['0', 'obj', "Entry Date", ""]);
                typecbos.push(['1', 'obj', "Job Date", ""]);
                // tools bar filter sections
                ptAllTrksTlbr.addButtonSelect("rpt_day_filter", '6', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptAllTrksTlbr.addButtonSelect("rpt_month_filter", '7', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                ptAllTrksTlbr.addButtonSelect("rpt_year_filter", '8', "Select Year", Years_Options, '', '', true, true, 10, 'select');
                ptAllTrksTlbr.addButtonSelect("rpt_date_type", '9', "Type", typecbos, '', '', true, true, 10, 'select');
                ptAllTrksTlbr.addSeparator('sep1', '10');                
                ptAllTrksTlbr.addText("text_from", '11', "From");
                ptAllTrksTlbr.addInput("rpt_date_from", '12', "", 75);
                ptAllTrksTlbr.addButton("rpt_df_clear", '13', "", "close.gif");
                ptAllTrksTlbr.addSeparator('sep2', '14');
                ptAllTrksTlbr.addText("text_till", '15', "Till");
                ptAllTrksTlbr.addInput("rpt_date_till", '16', "", 75);
                ptAllTrksTlbr.addButton("rpt_dt_clear", '17', "", "close.gif");
                ptAllTrksTlbr.addSeparator('sep3', '18');
                ptAllTrksTlbr.addButton("rpt_date_filter", '19', "Search", "save.gif");
                ptAllTrksTlbr.addSeparator('sep4', '20');
                ptAllTrksTlbr.addButton("excel_export", '21', "Export", "excel.png");
                // calander fields settings (read only click on calandar shown)
                var ptAlTrk_FrmDt   = ptAllTrksTlbr.getInput("rpt_date_from");
                ptAlTrk_FrmDt.setAttribute("readOnly", "true");
                var ptAlTrk_ToDt    = ptAllTrksTlbr.getInput("rpt_date_till");
                ptAlTrk_ToDt.setAttribute("readOnly", "true");
                ptAlTrk_Calendar    = new dhtmlXCalendarObject([ptAlTrk_FrmDt, ptAlTrk_ToDt]);
                ptAlTrk_Calendar.setDateFormat("%d.%m.%Y");
                // max and min date settings base on from and to date selector
                ptAlTrk_FrmDt.onclick = function () {
                    if (ptAllTrksTlbr.getValue("rpt_date_till")) {
                        ptAlTrk_Calendar.setSensitiveRange(null, ptAlTrk_ToDt.value);
                    }
                }
                ptAlTrk_ToDt.onclick = function () {
                    if (ptAllTrksTlbr.getValue("rpt_date_from")) {
                        ptAlTrk_Calendar.setSensitiveRange(ptAlTrk_FrmDt.value, null);
                    }
                }
                // load the current month 
                var date    = new Date();
                var month   = date.getMonth();
                var tDate   = Date.today().toString("dd.MM.yyyy");
                ptAllTrksTlbr.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                ptAllTrksTlbr.setValue('rpt_date_till', tDate);
                
                ptAllTrksTlbr.setListOptionSelected('rpt_date_type', 0);  // set default entry date 
                // enter button click inside the test box filter
                ptAllTrksTlbr.attachEvent("onEnter", function(id, value){
                    //your code here
                    if (id == "rpt_trackno" && value != "") {
                        ptAllTrksTlbr.setValue('rpt_date_from', '', false);
                        ptAllTrksTlbr.setValue('rpt_date_till', '', false);
                        ptAllTrksTlbr.setListOptionSelected('rpt_date_type', 0);
                        preTally.ManageTracks.filterTrackLoads();
                    }
                })
                // click on the tool bar 
                ptAllTrksTlbr.attachEvent("onClick", function (id) {
                    var pId = ptAllTrksTlbr.getParentId(id);
                    if (id == 'rpt_df_clear') {
                        ptAllTrksTlbr.setValue('rpt_date_from', '', false);
                    }
                    if (id == 'rpt_dt_clear') {
                        ptAllTrksTlbr.setValue('rpt_date_till', '', false);
                    }
                    if (pId == 'rpt_date_type') {
                        preTally.ManageTracks.filterTrackLoads();
                    }
                    if (pId == 'rpt_day_filter') {
                        var dateToday   = id + "." + Date.today().toString("MM.yyyy");
                        ptAllTrksTlbr.setValue('rpt_date_from', dateToday, false);
                        ptAllTrksTlbr.setValue('rpt_date_till', dateToday, false);
                        ptAllTrksTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptAllTrksTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.ManageTracks.filterTrackLoads();
                    }
                    if (pId == 'rpt_month_filter') {
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptAllTrksTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptAllTrksTlbr.setValue('rpt_date_from', firstDay, false);
                        ptAllTrksTlbr.setValue('rpt_date_till', lastDay, false);
                        ptAllTrksTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptAllTrksTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.ManageTracks.filterTrackLoads();
                    }
                    if (pId == 'rpt_year_filter') {
                        var tmpDate     = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        ptAllTrksTlbr.setValue('rpt_date_from', firstDay, false);
                        ptAllTrksTlbr.setValue('rpt_date_till', lastDay, false);
                        ptAllTrksTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptAllTrksTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.ManageTracks.filterTrackLoads();
                    }
                    if (id == 'rpt_date_filter') {
                        ptAllTrksTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptAllTrksTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptAllTrksTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.ManageTracks.filterTrackLoads();
                    }
                    if (id == 'excel_export') {                        
                        preTally.ManageTracks.exportAllTrack();
                    }
                });
                // Pagination status bar
                dhxAllTracksLayout.cells("a").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='altrk_total_cnt'># : 0</div>\
                            </div><div style='float:right;' id='pageTrkbox'></div>",
                    height: 30
                });
                //grid tab heading settings
                ptAllTrksTabbar = dhxAllTracksLayout.cells("a").attachTabbar();
                ptAllTrksTabbar.addTab("a2", "List All Tracks");
                ptAllTrksTabbar.tabs("a2").setActive();
                // grid base initialise section (track no and date based details)
                dhxAllTrksGrid = ptAllTrksTabbar.cells("a2").attachGrid();               
                dhxAllTrksGrid.setHeader("SlNo,Track No, Entry Date,Job Date,Job Amount, Amount Deductable,Total Amount Received,Total Amount Spent,Balance Receivable,Amount Received After Search Date");
                dhxAllTrksGrid.setInitWidths("80,*,80,80,100,100,100,100,100,110")
                dhxAllTrksGrid.setColAlign("center,Left,center,center,right,right,right,right,right,right");
                dhxAllTrksGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                dhxAllTrksGrid.setColSorting("na,na,na,na,na,na,na,na,na,na");
                dhxAllTrksGrid.init();
                dhxAllTrksGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxAllTrksGrid.setSkin("dhx_skyblue");
                dhxAllTrksGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);                
                dhxAllTrksGrid.enablePaging(true, 50, 5, "pageTrkbox", true);
                dhxAllTrksGrid.setPagingSkin("toolbar", "dhx_skyblue");
                dhxAllTrksGrid.enableTooltips("true,true,false,false,false,false,false,false,false,false");
                dhxAllTrksGrid.enableColSpan(true);
                //load track based data into grid......
                preTally.ManageTracks.filterTrackLoads();
            } else {
                dhxMiddleBlockTabs.tabs("list_all_tracks").setActive();
                ptAllTrksTabbar.tabs("a2").setActive();
            }
        },
        filterTrackLoads: function () {
            //console.log("start to load the filter list data.....");            
            var cmpid = (companyFltrCombo.getSelectedValue() > 0) ? companyFltrCombo.getSelectedValue() : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            var newfilter = '&OFID=' + cmpid+'&fromdate=' + ptAllTrksTlbr.getValue("rpt_date_from") + '&todate=' + ptAllTrksTlbr.getValue("rpt_date_till")+'&track_no=' + ptAllTrksTlbr.getValue("rpt_trackno")+'&date_type='+ptAllTrksTlbr.getListOptionSelected("rpt_date_type");
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxAllTrksGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/allTrackList.php" + newfilter), function () {
                $('.altrk_total_cnt').html("# : " + dhxAllTrksGrid.getUserData("", "totalCount") + " ");
            });
            setTimeout(function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
            }, 1000);
        },
        exportAllTrack:  function() {
            //console.log("start to export trackwise report in excel format...");
            var comFilter = {};
            comFilter['from_date']  = ptAllTrksTlbr.getValue("rpt_date_from");
            comFilter['to_date']    = ptAllTrksTlbr.getValue("rpt_date_till");
            comFilter['off_id']     = (companyFltrCombo.getSelectedValue() > 0) ? companyFltrCombo.getSelectedValue() : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            comFilter['track_no']   = ptAllTrksTlbr.getValue("rpt_trackno");
            comFilter['date_type']  = ptAllTrksTlbr.getListOptionSelected("rpt_date_type");
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                preTally.Initialize.encryptURL('warehouse/exportAllTrackExcel.php'),
                {filter: comFilter},
                function (data) {
                    fileName = data.split("XL_");
                    if (fileName[1]) {
                        document.location = "uploads/excelFile/" + fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
                }
            );
        },
        // Search Track all listing section end @ 23-05-2025   
        // list all track expense based on the date filter and company based with excel export 05-01-2026
        listTrackExpense: function() {
            if (!dhxMiddleBlockTabs.cells("list_expense_track")) {
                dhxMiddleBlockTabs.addTab("list_expense_track", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Track Expense Reports ", 220);
                dhxMiddleBlockTabs.tabs("list_expense_track").setActive();
                dhxExpTracksLayout = dhxMiddleBlockTabs.cells("list_expense_track").attachLayout("1C");                
                dhxExpTracksLayout.cells("a").hideHeader();
                // status bar or pagination and count
                dhxExpTracksLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('trackexp'),
                    height: 35
                });

                // tool bar with filter strt
                expTrackTbr =  dhxExpTracksLayout.cells("a").attachToolbar();
                expTrackTbr.setIconsPath("images/icon/default_18/");
                expTrackTbr.setAlign('left');
                preTally.AccountsTeam.customDateFilterToolBar(expTrackTbr,'trackexp');

                //----------------- Attach Grid --------------------//
                expTrackGrid = dhxExpTracksLayout.cells("a").attachGrid();
                expTrackGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='trackexp' class='accGridSort' />, \
                Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='trackexp' class='accGridSort' />, \
                Track <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='track' gridType='trackexp' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='trackexp' id ='trackexpTrack' style='width: 90%;' placeholder='Track No'>,\
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amount' gridType='trackexp' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='trackexp' id ='trackexpAmt' style='width: 90%;' placeholder='Amount'>,\
                Details <input type='text' class='acc_grid_txt_fltr' gridType='trackexp' id ='trackexpSearch' style='width: 90%;' placeholder='Enter Item Name / Details'>");
                expTrackGrid.setInitWidths("70,100,100,100,*");
                expTrackGrid.setColAlign("center,center,center,right,left");
                expTrackGrid.setColTypes("ro,ro,ro,ro,ro");
                expTrackGrid.setColSorting("na,na,na,na,na");              
                expTrackGrid.enableTooltips("false,false,false,false,false");
                preTally.Attendance.commonGridDefine(expTrackGrid, 'trackexp');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                //load the data based on the filters and search
                preTally.ManageTracks.filterTrackExpense('');  
            } else {
                dhxMiddleBlockTabs.tabs("list_expense_track").setActive();
            }
        },  
        filterTrackExpense: function(filters) {         
            var filterValue = new Array($('#trackexpTrack').val(), $('#trackexpSearch').val(), $('#trackexpAmt').val().replace( /,/g, "" ));            
            filters         = "&filter="+filterValue+filters+"&OFID=" + ((toolBarOffice['trackexp'].getSelectedValue() > 0) ? toolBarOffice['trackexp'].getSelectedValue() : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));         
            filters         = filters+"&from="+expTrackTbr.getValue("rpt_date_from")+"&to="+expTrackTbr.getValue("rpt_date_till");
            expTrackGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/trackExpenseList.php" + filters), function () {
                $('#total_trackexp').html("# : " + expTrackGrid.getUserData("", "totalCount") + " ");
                $('#total_amt_trackexp').html("Total Amount : " + expTrackGrid.getUserData("", "totalAmt") + " ");                
            });
        },
        exportTrackExpense: function () {
            var comFilter = {};
            comFilter['from_date']  = expTrackTbr.getValue("rpt_date_from");
            comFilter['to_date']    = expTrackTbr.getValue("rpt_date_till");
            comFilter['off_id']     = (toolBarOffice['trackexp'].getSelectedValue() > 0) ? toolBarOffice['trackexp'].getSelectedValue() : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            comFilter['track']      = $('#trackexpTrack').val();
            comFilter['search']     = $('#trackexpSearch').val();
            comFilter['amount']     = $('#trackexpAmt').val().replace( /,/g, "" );
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                preTally.Initialize.encryptURL('warehouse/exportAllTrackExcel.php&flag=2'),
                {filter: comFilter},
                function (data) {
                    fileName = data.split("XL_");
                    if (fileName[1]) {
                        document.location = "uploads/excelFile/" + fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
                }
            );
        },
    };
})(jQuery, this);