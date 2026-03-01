;(function($, window, undefined) {
    var lastOpenedTab   = 'track_dashboard';
    var notfOpenFlag    = 0 ;
    var TrackJobId    = 0 ;
    var uniqueId        = '';
    var AJNBId          = '';
    var proceedFlag     = 0;
    var checkedDoc      = '';
    var checkedDocSend  = '';
    var gridWindowOpen  = 0;
    var gridWinStatus   = false;
    var filterFocus     = false;
    var TAPOptionPopUp;
    var TAPSupDocPopUp;
    var TAPEditorPopUp;
    var thisCellID      = 0;
    var dragRowIndex    = 0;
    var automateObj     = {};
    var TAPOptionProcesses; //Grid For Automation Process
    var TAPSupportingDocs; //Grid For Supporting Documents
    var selectedProcessRow;
    var TAPEditor;
    var automateProcessCount = 2;
    var DocProcessLoad = false;
    var DocSupportingLoad = false;
    var dhxAutomateWinObj;
    var dhxAutomateWin;
    var automateLayout;
    var ATPOptions;
    var ATPOptionSelected;
    var TAPOptionCount = 0;
    var TAPNotesPopUp;
    var ATPCommentsPop;
    var ATPOptionObj = {};
    var ATPOptionComment;
    var ATPComment;
    var AJDRemarkId ;
    var trackAutomateForm;
    var TAPFormSupport;
    var TAPInstnPopUp;
    var TAPCancelPopUp;
    
    var TAPManagePopUp;
    var TAPFromPopUp;
    var TAPToPopUp;
    var TAPCFTypePopUp;
    var TAPAmtSplitPopUp;
    var TAPManageGridFm;
    var TAPManageGridTo;
    var TAPFromGridState;
    var TAPCommentPopUp;
    var TAPComment;
    var TAPProcedureID = 0;
    var TAPFromID = 0;
    var TAPToID = 0;
    var TAPCFTypeID = 0;
    var TAPCommentID = 0;
    var TAPProcedureStateID = '';
    var ATPProcedureGrid;
    var TrackJobSaved = 0;
    var TrackJobStatus = 0;
    var trackPaymentDetailsToolbar;
    var AJ_Id;
    var Print_CRID;
    var Print_DRID;
    
    
    var apsBackDateDetailsPop;
    var APSBackDateListGrid;
    
    preTally.Track = {
        Track: function() {
            if (!dhxMiddleBlockTabs.cells("TracksTab")) {
                dhxTrackLayout = dhxMiddleBlockTabs.addTab("TracksTab", "<img src='images/icon/track_16.png' style='margin-top:2px;' />&nbsp;&nbsp;Tracks", 150);
                dhxMiddleBlockTabs.tabs("TracksTab").setActive();
                
                trackToolbar = dhxMiddleBlockTabs.tabs("TracksTab").attachToolbar({
                    icons_path  : "images/icon/",
                    json        : preTally.Initialize.encryptURL('requisites/trackToolbarLayout.php'),
                    onload: function() {
                        
                        var popClickInterval;
                        var popClickFlag;
                        
                        //Notification
                        NotifiPopup = new dhtmlXPopup({toolbar: trackToolbar, id: "trackNotification"});
                        preTally.Track.NotificationList();
                            
                        NotifiPopup.attachEvent("onClick", function(id) {
                            var items = NotifiPopup.getItemData(id);
                            if(items.AJNB_Description!='No Notification Found') {
                                AJNBId=items.AJNB_Id;
                                if(AJNBId != "More") preTally.Track.trackNotification(this,AJNBId); 
                                else{ 
                                    trackToolbar.enableItem(lastOpenedTab);
                                    preTally.Track.listAllNotifications(this);
                                }
                            }
                        });
                        preTally.Track.trackButtonsShowHide(0,0,0,0);
                        
                        //--------------- Attaching Sidebar -------------------
                        trackConsoleSideBar = dhxMiddleBlockTabs.cells("TracksTab").attachSidebar({
                            template: "text",
                            width: 1                 
                        });
                        //--------------- Automate Enquiry ----------------------
                        preTally.Track.newTrackEnquiry();
                    }
                });
                
                trackToolbar.attachEvent("onClick", function(id) { 
                    if(id == 'cancelNewTrackRegistration') {
                        if(trackToolbar.getItemText('cancelNewTrackRegistration') == 'Close') {
                            delete trackConsoleSideBar;
                            preTally.Track.disableEnableToolBarItems('list_track_jobs');  
                            preTally.Track.ListTrackJobs();
                            preTally.Track.trackButtonsShowHide(0,0,0,0);
                            trackCandidateDetails.clear();
                        } else {
                            dhtmlx.confirm({
                                title: "Confirm Delete",
                                type:"confirm-warning",
                                ok  : "Yes", cancel : "No",
                                text: "Do you want to delete this job ?",
                                callback: function(response) {   
                                    if(response) { 
                                        if(!trackConsoleSideBar.items("SB_list_track_jobs")) {  //new
                                            preTally.Track.cancelNewJob(id,'new');
                                            preTally.Track.newTrackEnquiry();
                                            trackConsoleSideBar.cells("SB_new_job_user_registration_"+TrackJobId).remove();
                                        } else {  // edit
                                            preTally.Track.cancelNewJob(id,'edit');
                                            preTally.Track.applyFilter(TrckListTlbr,"listTrackJobs",trackListJobGrid); 
                                            preTally.Track.disableEnableToolBarItems('list_track_jobs');
                                            trackConsoleSideBar.items("SB_list_track_jobs").setActive();
                                        }
                                        delete trackConsoleSideBar;
                                        preTally.Track.trackButtonsShowHide(0,0,0,0);
                                    }  else 
                                        return false;
                                }
                            });
                        }
                    } else 
                        preTally.Track.selectMenu(id);
                }); 
            }   else 
                dhxMiddleBlockTabs.tabs("TracksTab").setActive();
        },
        selectMenu : function(id) { 
            
            if(id != 'newPhoneRegistration' && id !='trackNotification' && id !='master_data'  && id !='listings') {
                preTally.Track.trackButtonsShowHide(0,0,0,0);
                preTally.Track.disableEnableToolBarItems(id);
            }
            if(id == 'track_dashboard') 
                preTally.Track.drawDashboard();
            if(id == 'track_automate_enquiry') 
                preTally.Track.newTrackEnquiry();
            if(id == 'new_track_user_registration'){ 
                $.post(preTally.Initialize.encryptURL('requisites/checkBSSettings.php'), function(data) {
                    if(data=="0"){
                        dhtmlx.message({
                            title: "Warning",
                            type:  "alert-warning",
                            text: "You Have To Set Balance Sheet Settings Before Registration.",
                            callback: function(id) {
                                    preTally.Track.balSettings();
                                preTally.Track.disableEnableToolBarItems('balSettings');
                                preTally.Track.balSettings();
                            }
                        });
                    }else if(data=="1"){
                        preTally.Track.TrackRegistrationTabUser(this,0);
                    }else{
                           dhtmlx.message({
                            title: "Warning",
                            type:  "alert-warning",
                            text: "You have to set the Balance sheet settings first.",
                            callback: function(id) {
                                dhtmlx.alert({text: "You don't have the permission to access Balancesheet Settings."});
                               // preTally.Track.balSettings();
                            }
                        });
                    }
                });
            }
            if(id == 'list_track_jobs') 
                preTally.Track.ListTrackJobs();
            if(id == 'list_track_document') 
                preTally.Track.ListTrackDocumnet();
            if(id == 'list_track_enquiry') {
                preTally.Track.ListTrackEnquiry();
            }
            if(id == 'nextNewTrackRegistration') {
                if(newUserTrackSidebar.getActiveItem() == 'candidate_details') 
                   preTally.Track.saveCandidateDetails('candidate_details',1);

                if(newUserTrackSidebar.getActiveItem() == "certificate_details") 
                    preTally.Track.TrackRegistrationTabBilling("certificate_details",1);

                if(newUserTrackSidebar.getActiveItem() == 'payment_details') 
                    preTally.Track.trackButtonsShowHide(1,1,0,1);
            }
            if(id == 'previousNewTrackRegistration') {
                if(newUserTrackSidebar.getActiveItem() == "certificate_details") {
                    preTally.Track.TrackRegistrationTabBilling("certificate_details",2);
                } else if(newUserTrackSidebar.getActiveItem() == "payment_details") {
                    newUserTrackSidebar.items("certificate_details").setActive();
                    preTally.Track.trackButtonsShowHide(1,0,1,1);
                } else if(newUserTrackSidebar.getActiveItem() == 'candidate_details') 
                    preTally.Track.trackButtonsShowHide(1,0,1,0);
            }
            if(id == 'saveNewTrackRegistration') {
                preTally.Track.savePaymentDetails("payment_details",1);
            }
            
            if(id == 'documents') 
                preTally.Track.attestationDocuments();
            if(id == 'subprocess') 
                preTally.Track.subProcess();
            if(id == 'mainprocess') 
                preTally.Track.mainProcess();
            if(id == 'supporting_documents') 
                preTally.Track.attestationSupportingDocuments();
            if(id == 'trackSummary') 
                preTally.Track.trackSummary();
            if(id == 'balSettings') 
                preTally.Track.balSettings();
            if(id == 'trackAutomate')
                preTally.Track.trackAutomate();

            if(id == 'ATPInstruction')
                preTally.Track.ATPInstruction();
            
            if(id == 'trackNotification' ) {
                if(notfOpenFlag == 0){
                    preTally.Track.NotificationList();
                    notfOpenFlag = 1;
                } 
                NotifiPopup.attachEvent("onHide", function(){ notfOpenFlag = 0; });
            }
        },
        //----------------------------------------------------- Dashboard-------------------------------------------------------// 
        drawDashboard: function() { 
            preTally.Settings.progressOn(true, dhxLayout, null);
            if(!trackConsoleSideBar.items("SB_track_dashboard")) {
                trackConsoleSideBar.addItem({ id:"SB_track_dashboard"});
                trackConsoleSideBar.items("SB_track_dashboard").setActive();
                trackConsoleSideBar.attachEvent("onContentLoaded",function(id){if(id=="SB_track_dashboard")preTally.Settings.progressOff(true, dhxLayout, null);});
                trackConsoleSideBar.cells("SB_track_dashboard").attachURL("dashboard/trackDashboard.php");                
            } else {
                trackConsoleSideBar.cells("SB_track_dashboard").reloadURL();                
                trackConsoleSideBar.items("SB_track_dashboard").setActive();
            }
            preTally.Track.disableEnableToolBarItems('track_dashboard');
        },
        
        TAPManagePopUpAddRow : function(rowId) {
//            console.log('Added'+rowId);
            TAPManageGridTo.addRow(1000,["",TAPManageGridFm.cells(rowId,1).getValue(),""]);
            TAPManageGridTo.setUserData(1000,'processID',rowId);
            preTally.Track.TAPManagePopSortRow();
        },
        TAPManagePopManageRow : function(forP,rowId) {
            if(forP == 'up') {
                TAPManageGridTo.moveRow('process_'+rowId,"up");
                preTally.Track.TAPManagePopSortRow();
            }
            if(forP == 'down') {
                TAPManageGridTo.moveRow('process_'+rowId,"down");
                preTally.Track.TAPManagePopSortRow();
            }
            if(forP == 'remove') {
                TAPManageGridTo.deleteRow('process_'+rowId);
                preTally.Track.TAPManagePopSortRow();
            }
        },
        listStatesATP_MCB : function() { 
            TAPFromGridState.forEachRow(function(rId){
                if(rId != 10000) {
                    if ($('#listStatesATP_MTB').is(':checked')) {
                        TAPProcedureStateID.push(rId);
                        TAPFromGridState.cells(rId,2).setValue(1);
                    } else {
                        //console.log(TAPProcedureStateID.indexOf(rId));
                        TAPFromGridState.cells(rId,2).setValue(0);
                        TAPProcedureStateID = TAPProcedureStateID.filter(function(elem){
                            return elem != rId; 
                        });
                    }
                }
            });
        },
        trackAutomate : function() { 
            //console.log(1212);
            //($(document).height()-55)
            TAPManagePopUp = new dhtmlXPopup();
            TAPManagePopUp.show(100,100,100,100);
            
            TAPFromPopUp = new dhtmlXPopup();
            TAPFromPopUp.show(100,100,100,100);
            
            TAPToPopUp = new dhtmlXPopup();
            TAPToPopUp.show(100,100,100,100);
            
            TAPCFTypePopUp = new dhtmlXPopup();
            TAPCFTypePopUp.show(100,100,100,100);
            
            TAPCommentPopUp = new dhtmlXPopup();
            TAPComment = TAPCommentPopUp.attachEditor(400, 250);
            $('#'+TAPCommentPopUp._nodeId).find('.dhx_cell_editor').find('.dhx_cell_stb').append('<a href="javascript:void(0);" style="float:right; margin:6px; 3px 15px 3px;" onclick="preTally.Track.ATPCommentSave();"><img src="images/icon/tick_16.png" /></a><a href="javascript:void(0);" style="float:right; margin:6px; 3px;" onclick="preTally.Track.ATPCommentClose();"><img src="images/icon/cross.png" /></a>');
            
            var TAPManageLayout = TAPManagePopUp.attachLayout(700, ($(document).height()-55), "3T");
            var TAPFromLayout   = TAPFromPopUp.attachLayout(700, ($(document).height()-55), "3T");
            var TAPToLayout     = TAPToPopUp.attachLayout(350, ($(document).height()-55), "2E");
            var TAPCFTypeLayout = TAPCFTypePopUp.attachLayout(350, ($(document).height()-55), "2E");

            //----------- TAP From Country Layout Data -------------------------
            TAPFromLayout.cells("b").setText("Select Your Country");
            TAPFromLayout.cells("c").setText("Select Your State");
            TAPFromLayout.cells("a").hideHeader();
            TAPFromLayout.cells("a").setHeight(32);
            TAPFromLayout.cells("b").setWidth(350);
            
            //----------- TAP To Country Layout Data -------------------------
            TAPToLayout.cells("b").setText("Select Travelling Country");
            TAPToLayout.cells("a").hideHeader();
            TAPToLayout.cells("a").setHeight(32);
            TAPToLayout.cells("b").setWidth(350);
            
            //----------- TAP CF Type Layout Data -------------------------
            TAPCFTypeLayout.cells("b").setText("Select Certificate Type");
            TAPCFTypeLayout.cells("a").hideHeader();
            TAPCFTypeLayout.cells("a").setHeight(32);
            TAPCFTypeLayout.cells("b").setWidth(350);
            
            //----------- TAP Process Layout Data -------------------------
            TAPManageLayout.cells("b").setText("Select Your Process");
            TAPManageLayout.cells("c").setText("Move UP ( <img src='images/icon/up_12.png' /> ) / DOWN ( <img src='images/icon/down_12.png' /> ) To Arrange Process");
            TAPManageLayout.cells("a").hideHeader();
            TAPManageLayout.cells("a").setHeight(32);
            TAPManageLayout.cells("b").setWidth(350); 
            
            //--------- Attaching Toolbar To Layout -----------------------
            var TAPFromTB = TAPFromLayout.cells("a").attachToolbar();
            TAPFromTB.loadStruct(preTally.Data.TAPFromTB());
            
            var TAPToTB = TAPToLayout.cells("a").attachToolbar();
            TAPToTB.loadStruct(preTally.Data.TAPToTB());

            var TAPCFTypeTB = TAPCFTypeLayout.cells("a").attachToolbar();
            TAPCFTypeTB.loadStruct(preTally.Data.TAPCFTypeTB());
            
            var TAPManageTB = TAPManageLayout.cells("a").attachToolbar();
            TAPManageTB.loadStruct(preTally.Data.TAPManageTB());

            
            var TAPFromGrid = TAPFromLayout.cells("b").attachGrid();
            TAPFromGridState = TAPFromLayout.cells("c").attachGrid();
            
            $( "#listStatesATP_MTB" ).remove();
            
            TAPFromGrid.loadXML(preTally.Initialize.encryptURL("requisites/listCountriesATP.php&for=FROM"),function() {
                TAPFromGrid.attachEvent("onRowSelect", function(id,ind){
                    TAPFromGridState.clearAll();
                    $('#listStatesATP_MTB').prop( "checked", false );
                    TAPFromGridState.updateFromXML(preTally.Initialize.encryptURL("requisites/listStatesATP.php&CNID="+id), true, true, function() {
                        ATPStatesUpdate();
                    });
                });
            });
            
            TAPFromGridState.loadXML(preTally.Initialize.encryptURL("requisites/listStatesATP.php&CNID=0"),function() {
                ATPStatesLoad();
            });

            var TAPToGrid = TAPToLayout.cells("b").attachGrid();
            TAPToGrid.loadXML(preTally.Initialize.encryptURL("requisites/listCountriesATP.php&for=TO"),function() { 
                
            });
            
            var TAPCFTypeGrid = TAPCFTypeLayout.cells("b").attachGrid();
            TAPCFTypeGrid.setImagePath("assets/grid/codebase/imgs/");
            TAPCFTypeGrid.parse(preTally.Data.TAPCFTypeGrid(),"json");
   
            
            TAPManageTB.attachEvent("onClick", function(id){
                if(id == 'saveProcess') {

                    var processCount = TAPManageGridTo.getRowsNum();
                    var prsCount = 0;
                    var procedureData = '';
                    var processIDS = '';
                    TAPManageGridTo.forEachRow(function(id){
                        processIDS += TAPManageGridTo.getUserData(id,"processID")+',';
                        
                        if(prsCount != 0) {
                            procedureData += '<div class="automateBlockArrow">&nbsp;</div>';
                        }
                        procedureData += '<div class="automateBlock">'+TAPManageGridTo.cellById(id, 1).getValue()+'</div>';
                        prsCount++;
                    });
            
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                        TPID            : TAPProcedureID,
                        TPUpdateKey     : 'TP_Procedures',
                        TPUpdateVal     : processIDS.slice(0, -1)
                    },
                    function(response){
                        preTally.Settings.progressOff(true, dhxLayout, null); 
                        dhtmlx.message({text: 'Process Updated Successfully'});
                    });

                    ATPProcedureGrid.cellById(TAPProcedureID, 7).setValue(procedureData);
                    TAPManagePopUp.hide();
                    return;
                }
                if(id == 'closeProcess') {
                    TAPManagePopUp.hide();
                    return;
                }
            });
            TAPFromTB.attachEvent("onClick", function(id){
                if(id == 'saveFromCountry') {
                    preTally.Functions.clearGridFilters(TAPFromGrid);
                    preTally.Functions.clearGridFilters(TAPFromGridState);
                    var TAPProcedureStateID_UnQ = [];
                    TAPProcedureStateID = TAPProcedureStateID.map(function(item) {
                        return parseInt(item, 10);
                    });
                    $.each(TAPProcedureStateID, function(i, el){
                        if($.inArray(el, TAPProcedureStateID_UnQ) === -1) {
                            TAPProcedureStateID_UnQ.push(el);
                        }
                    });
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                        TPID            : TAPFromID,
                        TPUpdateKey     : 'TP_From',
                        TPUpdateVal     : TAPProcedureStateID_UnQ.join(",")
                    },
                    function(response){
                        ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                            preTally.Settings.progressOff(true, dhxLayout, null); 
                            dhtmlx.message({text: 'Parent Countries Updated Successfully'});
                        });
                    });
                    TAPFromPopUp.hide();
                    return;
                }
                if(id == 'closeFromCountry') {
                    TAPFromPopUp.hide();
                    return;
                }
            });
            TAPToTB.attachEvent("onClick", function(id){
                if(id == 'saveToCountry') {
                    preTally.Functions.clearGridFilters(TAPToGrid);
                    var TOCNIDs = TAPToGrid.getCheckedRows(2);
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                        TPID            : TAPToID,
                        TPUpdateKey     : 'TP_To',
                        TPUpdateVal     : TOCNIDs
                    },
                    function(response){
                        
                        ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                            preTally.Settings.progressOff(true, dhxLayout, null); 
                            dhtmlx.message({text: 'Travelling Countries Updated Successfully'});
                        });
                    });    
                    TAPCFTypeGrid.setCheckedRows(2,0);
                    
                    TAPToPopUp.hide();
                    return;
                }
                if(id == 'closeToCountry') {
                    TAPToPopUp.hide();
                    return;
                }
            });
            TAPCFTypeTB.attachEvent("onClick", function(id){
                if(id == 'saveCFType') {
                    //preTally.Functions.clearGridFilters(TAPCFTypeGrid);
                    var CFIDs = TAPCFTypeGrid.getCheckedRows(2);
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                        TPID            : TAPCFTypeID,
                        TPUpdateKey     : 'TP_Certificate',
                        TPUpdateVal     : CFIDs
                    },
                    function(response){
                        
                        ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                            preTally.Settings.progressOff(true, dhxLayout, null); 
                            dhtmlx.message({text: 'Procedure Certificates Updated Successfully'});
                        });
                    });    
                    TAPCFTypeGrid.setCheckedRows(2,0);
                    TAPCFTypePopUp.hide();
                    return;
                }
                if(id == 'closeCFType') {
                    TAPCFTypePopUp.hide();
                    return;
                }
            });
            
            TAPManageGridFm = TAPManageLayout.cells("b").attachGrid();
            TAPManageGridTo = TAPManageLayout.cells("c").attachGrid();
//            TAPManageGridFm.enableDragAndDrop(true);
//            TAPManageGridFm.enableMercyDrag(true);
            TAPManageGridFm.loadXML(preTally.Initialize.encryptURL("requisites/listTrackMainProcess.php"),function() {
                TAPManageGridFm.attachEvent("onDrop",function(sInd,tInd){
                    TAPManageGridFm.deleteRow(sInd);
                    TAPManageGridFm.sortRows(0,"int","asc");
                });
            });
            TAPManageGridTo.enableDragAndDrop(true);    
            TAPManageGridTo.loadXML(preTally.Initialize.encryptURL("requisites/listTrackMainProcessDrag.php"),function() {
                
//                TAPManageGridTo.attachEvent("onDrop",function(sInd,tInd){
//                    preTally.Track.TAPManagePopSortRow();
//                    return true;
//                });
                
            });
            if(!trackConsoleSideBar.items("TAPConsole")) {
                trackConsoleSideBar.addItem({ id:"TAPConsole"});
            }
            trackConsoleSideBar.items("TAPConsole").setActive();
            var trackAutomateLayout = trackConsoleSideBar.cells('TAPConsole').attachLayout("1C");
            trackAutomateLayout.cells("a").setText("Automate Track Procedures");
            trackAutomateLayout.cells("a").hideHeader();
            
            var TAPListTB = trackAutomateLayout.cells("a").attachToolbar();
            TAPListTB.loadStruct(preTally.Data.TAPListTB());
            
            TAPListTB.attachEvent("onClick", function(id){
                if(id == 'newProcedure') {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php&newProcedure=true"), true, true, function() {
                        preTally.Settings.progressOff(true, dhxLayout, null); 
                        dhtmlx.message({text: 'Procedure Added Successfully'});
                    });
                }
            });
            
            ATPProcedureGrid = trackAutomateLayout.cells("a").attachGrid();
            ATPProcedureGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"),function() { });
           
            CloseTAPPopUp();
            ATPProcedureGrid.attachEvent("onRowSelect", function(id,ind){
                CloseTAPPopUp();
                //----------- From Country / State Selection Popup -------------
                if(this.cell._cellIndex == 2) {
                    TAPFromID = id;
                    var FromCNID = ATPProcedureGrid.getUserData(id,"CN_Id");
                    var FromCountry = ATPProcedureGrid.getUserData(id,"TP_From");
                    var x = getAbsoluteLeft(ATPProcedureGrid.cells(id,ind).cell);
                    var y = getAbsoluteTop(ATPProcedureGrid.cells(id,ind).cell);
                    var w = ATPProcedureGrid.cells(id,ind).cell.offsetWidth;
                    var h = ATPProcedureGrid.cells(id,ind).cell.offsetHeight;
                    TAPFromPopUp.show(x, y, w, h);

                    TAPFromGrid.getFilterElement(1).value = "";
                    TAPFromGrid.filterByAll();
                    TAPFromGrid.sortRows(1,"str","asc");
                    var colorCount = 0;
                    TAPFromGrid.forEachRow(function(id){
                        if(colorCount%2 == 0) {
                            TAPFromGrid.setRowColor(id,"#FFFFFF");
                        } else {
                            TAPFromGrid.setRowColor(id,"#EBF3FF");
                        }
                        colorCount++;
                    });

                    var FromCNIDArray = JSON.parse("[" + FromCNID + "]");
                    TAPProcedureStateID = JSON.parse("[" + FromCountry + "]");

                    FromCNIDArray.reverse();

                    //var j=0;
                    TAPFromGrid.addRow('dummyRow','',0);
                    var firstRowID = TAPFromGrid.getRowId(0);
                    for (var k in FromCNIDArray){
                        if (FromCNIDArray.hasOwnProperty(k)) {
                            TAPFromGrid.moveRowTo(FromCNIDArray[k],firstRowID,"move");
                            TAPFromGrid.setRowColor(FromCNIDArray[k],"#B5DEFF");
                        }
                    }
                    TAPFromGrid.deleteRow('dummyRow');

                    var FirstRowID = TAPFromGrid.getRowId(0);
                    TAPFromGrid.setRowColor(FirstRowID,"#67D1FE");
                    TAPFromGridState.clearAll();
                    $('#listStatesATP_MTB').prop( "checked", false );
                    TAPFromGridState.updateFromXML(preTally.Initialize.encryptURL("requisites/listStatesATP.php&CNID="+FirstRowID), true, true, function() {
                        ATPStatesUpdate();
                    });


                }
                //----------- Travelling Country Selection Popup ---------------
                if(this.cell._cellIndex == 4) {
                    TAPToID = id;
                    var ToCountry = ATPProcedureGrid.getUserData(id,"TP_To");
                    var x = getAbsoluteLeft(ATPProcedureGrid.cells(id,ind).cell);
                    var y = getAbsoluteTop(ATPProcedureGrid.cells(id,ind).cell);
                    var w = ATPProcedureGrid.cells(id,ind).cell.offsetWidth;
                    var h = ATPProcedureGrid.cells(id,ind).cell.offsetHeight;
                    TAPToPopUp.show(x, y, w, h);

                    TAPToGrid.setCheckedRows(2,0);
                    var ToArray = JSON.parse("[" + ToCountry + "]");
                    for (var k in ToArray){
                        if (ToArray.hasOwnProperty(k)) {
                            TAPToGrid.cells(ToArray[k],2).setValue(1);
                        }
                    }
                    ATPToCountryUpdate();
                }
                //----------- Certificate Type Selection Popup -----------------
                if(this.cell._cellIndex == 6) {
                    TAPCFTypeID = id;
                    var CFType = ATPProcedureGrid.getUserData(id,"TP_Certificate");
                    var x = getAbsoluteLeft(ATPProcedureGrid.cells(id,ind).cell);
                    var y = getAbsoluteTop(ATPProcedureGrid.cells(id,ind).cell);
                    var w = ATPProcedureGrid.cells(id,ind).cell.offsetWidth;
                    var h = ATPProcedureGrid.cells(id,ind).cell.offsetHeight;
                    TAPCFTypePopUp.show(x, y, w, h);

                    TAPCFTypeGrid.setCheckedRows(2,0);
                    var CFTypeArray = JSON.parse("[" + CFType + "]");
                    for (var k in CFTypeArray){
                        if (CFTypeArray.hasOwnProperty(k)) {
                             TAPCFTypeGrid.cells(CFTypeArray[k],2).setValue(1);
                        }
                    }
                }
                //----------- Process Drag & Sort Popup ------------------------
                if(this.cell._cellIndex == 8) {
                    TAPProcedureID = id;
                    if(id != thisCellID) {
                        var x = getAbsoluteLeft(ATPProcedureGrid.cells(id,ind).cell);
                        var y = getAbsoluteTop(ATPProcedureGrid.cells(id,ind).cell);
                        var w = ATPProcedureGrid.cells(id,ind).cell.offsetWidth;
                        var h = ATPProcedureGrid.cells(id,ind).cell.offsetHeight;
                        TAPManagePopUp.show(x, y, w, h);

                        TAPManageGridTo.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackMainProcessDrag.php&TPID="+id), true, true, function() {
                            preTally.Track.TAPManagePopSortRow();
                        });

                    } else {
                        thisCellID = 0;
                    }
                }
                //----------- Block / Publish Procedure ------------------------
                if(this.cell._cellIndex == 9) {
                    var TPStatus = ATPProcedureGrid.getUserData(id,"TPStatus");
                    var CMsg;
                    var newTPStatus;
                    if(TPStatus == 1) {
                        CMsg = 'Block';
                        newTPStatus = 0;
                    } else {
                        CMsg = 'Publish';
                        newTPStatus = 1;
                    }
                    dhtmlx.confirm({
                        title   : "Manage Procedure Status",
                        type    : "confirm-warning",
                        text    : "Are You Sure To "+CMsg+" This Procedure",
                        callback: function(response) {
                            if(response == true) {

                                preTally.Settings.progressOn(true, dhxLayout, null);
                                $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                                    TPID            : id,
                                    TPUpdateKey     : 'TP_Status',
                                    TPUpdateVal     : newTPStatus
                                },
                                function(response){
                                    ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                                        preTally.Settings.progressOff(true, dhxLayout, null); 
                                        dhtmlx.message({text: 'Procedure Status Updated Successfully'});
                                    });

                                });

                            }
                        }
                    });
                }
                //----------- Remove Procedure Permanently ---------------------
                if(this.cell._cellIndex == 10) { 
                    dhtmlx.confirm({
                        title   : "Remove Permanently",
                        type    : "confirm-warning",
                        text    : "Confirm To Remove Permanently. <br />This Cannot Be Undone. ",
                        callback: function(response) {
                            if(response == true) {

                                preTally.Settings.progressOn(true, dhxLayout, null);
                                $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                                    TPID            : id,
                                    TPUpdateKey     : 'TP_Status',
                                    TPUpdateVal     : 2
                                },
                                function(response){
                                    ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                                        preTally.Settings.progressOff(true, dhxLayout, null); 
                                        dhtmlx.message({text: 'Procedure Removed Successfully'});
                                    });

                                });

                            }
                        }
                    });
                }
                //----------- Add Comments For Procedure -----------------------
                if(this.cell._cellIndex == 11) { 
                    TAPCommentID = id;
                    if(id != thisCellID) {
                        var CommentData = ATPProcedureGrid.getUserData(id,"TP_Comment");
                        var x = getAbsoluteLeft(ATPProcedureGrid.cells(id,ind).cell);
                        var y = getAbsoluteTop(ATPProcedureGrid.cells(id,ind).cell);
                        var w = ATPProcedureGrid.cells(id,ind).cell.offsetWidth;
                        var h = ATPProcedureGrid.cells(id,ind).cell.offsetHeight;
                        TAPCommentPopUp.show(x, y, w, h);
                        TAPComment.setContent(CommentData)

                    } else {
                        thisCellID = 0;
                    }
                }
                    
                    

            });
            function ATPToCountryUpdate() {
                TAPToGrid.enableStableSorting(true);
                TAPToGrid.sortRows(0,"int","asc");    // sort by the sibling column
                    
                TAPToGrid.addRow('firstTempRow',"",0);
                var rowIndexG = 0;
                var firstRowID = TAPToGrid.getRowId(0);
                TAPToGrid.forEachRow(function(id){
                    var thisCellValue = TAPToGrid.cellById(id, 2).getValue();
                    if(thisCellValue == 1) {
                        TAPToGrid.moveRowTo(id,firstRowID,"move");
                        //TAPToGrid.moveRow(id,"up");
                        TAPToGrid.setRowColor(id,"#B5DEFF");
                    } else {
                        if(rowIndexG%2 == 0) TAPToGrid.setRowColor(id,"#FFF");
                        if(rowIndexG%2 == 1) TAPToGrid.setRowColor(id,"#EBF3FF");
                        
                        rowIndexG++;
                    }
                });
                TAPToGrid.deleteRow("firstTempRow");
            }
            function ATPStatesUpdate() {
                TAPFromGridState.getFilterElement(1).value = "";
                var firstRowID = TAPFromGridState.getRowId(0);
                if(TAPProcedureStateID) {
                    for (var k in TAPProcedureStateID){
                        if (TAPProcedureStateID.hasOwnProperty(k)) {
                            if(TAPFromGridState.doesRowExist(TAPProcedureStateID[k])) {
                                TAPFromGridState.cells(TAPProcedureStateID[k],2).setValue(1);
                                TAPFromGridState.moveRowTo(TAPProcedureStateID[k],firstRowID,"move");
                                TAPFromGridState.moveRow(TAPProcedureStateID[k],"up");
                                TAPFromGridState.setRowColor(TAPProcedureStateID[k],"#B5DEFF");
                            }  
                        }
                    }
                }
                preTally.Settings.progressOff(true, dhxLayout, null); 
            }
            function ATPStatesLoad() {
                TAPFromGridState.getFilterElement(1).value = "";
                TAPFromGridState.attachEvent("onCheck", function(rId,cInd,state){
                    if(state == true) {
                        TAPProcedureStateID.push(rId);
                    } else {
                        TAPProcedureStateID = TAPProcedureStateID.filter(function(elem){
                            return elem != rId; 
                        });
                    }
                });
                preTally.Settings.progressOff(true, dhxLayout, null); 
            }
            function CloseTAPPopUp () {
                TAPFromPopUp.hide();
                TAPToPopUp.hide();
                TAPCFTypePopUp.hide();
                TAPManagePopUp.hide();
                //TAPAmtSplitPopUp.hide();
            }

        },
        ATPCommentClose : function () {
            TAPCommentPopUp.hide();
        },
        ATPCommentSave : function () {
            var TAPCommentContent = TAPComment.getContent();
//            console.log(TAPCommentContent);
            
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(preTally.Initialize.encryptURL("warehouse/updateTrackProcedures.php"), {   
                TPID            : TAPCommentID,
                TPUpdateKey     : 'TP_Comment',
                TPUpdateVal     : TAPCommentContent
            },
            function(response){
                ATPProcedureGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listTrackProcedures.php"), true, true, function() {
                    preTally.Settings.progressOff(true, dhxLayout, null); 
                    dhtmlx.message({text: 'Comment Updated Successfully'});
                });
            });    
                            
                            
            TAPCommentPopUp.hide();
        },
        ATPAmountSplit : function (inp,rowId,formName) {

            var formObj = ATPOptions;
            if (!TAPAmtSplitPopUp) {
                TAPAmtSplitPopUp = new dhtmlXPopup({mode: "left"});   
            }
            
            if (TAPAmtSplitPopUp.isVisible()) {
                TAPAmtSplitPopUp.hide();
            } 
                
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;    
            TAPAmtSplitPopUp.show(x, y, w, h);
            var TAPAmtGrid = TAPAmtSplitPopUp.attachGrid(520,350);
            
            TAPAmtGrid.setImagePath("assets/grid/codebase/imgs/");
            TAPAmtGrid.setHeader(",#,Process Name,Total<img src='images/icon/cross_16.png' style='cursor:pointer; float:right; margin-right:10px;' onclick='preTally.Track.AmountSplitPopUpHide();' />");
            TAPAmtGrid.setInitWidths("50,50,300,120");
            TAPAmtGrid.setColAlign("center,center,left,right");
            TAPAmtGrid.setColTypes("sub_row,ro,ro,ro");
            TAPAmtGrid.init();
            
            var amountDetails;
            if(formName == 'U') {
                amountDetails = JSON.parse(ATPOptions.getUserData(rowId,'TAP_U_Amount'));
            }
            if(formName == 'N') {
                amountDetails = JSON.parse(ATPOptions.getUserData(rowId,'TAP_N_Amount'));
            }
            
            var countJ = 1;
            var totalAmountSum = 0;
            amountDetails.forEach(function (arrayElem){ 
                var totalAmount = parseFloat(arrayElem[1])+parseFloat(arrayElem[2])+parseFloat(arrayElem[3])+parseFloat(arrayElem[4])+parseFloat(arrayElem[5])+parseFloat(arrayElem[6]);
                TAPAmtGrid.addRow(countJ,[showSubgrid(arrayElem),countJ,arrayElem[0],totalAmount+'&nbsp;&nbsp;&nbsp;']);
                totalAmountSum += parseFloat(totalAmount);
                countJ++;
            });
            TAPAmtGrid.addRow(countJ,[,,,'<b style="color:#991212;">₹ '+totalAmountSum+'</b>&nbsp;&nbsp;&nbsp;']);

            function showSubgrid(arrayElem){
                var processGridData =  '<table style = "font-size: 12px; margin-top:20px;border-spacing: 30px 0; margin-bottom: 10px;line-height: 25px;border : 1px solid #a4bed4;">\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">Statutory Amount</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[1]+'</td><td></td></tr>\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">Extra Amount</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[2]+'</td><td></td></tr>\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">Courier Charges</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[3]+'</td><td></td></tr>\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">Travelling Expense</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[4]+'</td><td></td></tr>\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">ManPower Amount</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[5]+'</td><td></td></tr>\n\
                                        <tr style = "margin-left: 40px;"><td width = "140px;">Service Charges</td><td width = "10px;"> : </td><td align="right" width = "50px;">₹ '+arrayElem[6]+'</td><td></td></tr>\n\
                                    </table>';
                return processGridData;
}
        },
        AmountSplitPopUpHide : function () {
             TAPAmtSplitPopUp.hide();
        },
        TAPManagePopSortRow : function() {
            
            var cellCount = parseInt(new Date().getTime());
            for(var j=0; j<TAPManageGridTo.getRowsNum(); j++) {
                var upImg = 'up_12.png'
                var dnImg = 'down_12.png'
                if(j == 0) upImg = 'up_disabled_12.png';
                if(j == (TAPManageGridTo.getRowsNum()-1)) dnImg = 'down_disabled_12.png';
                TAPManageGridTo.setRowId(j,"process_"+cellCount);
                TAPManageGridTo.cellById("process_"+cellCount, 0).setValue(j+1);
                TAPManageGridTo.cellById("process_"+cellCount, 2).setValue('<img src="images/icon/'+upImg+'" class="TAPManageIcon" onclick="javascript:preTally.Track.TAPManagePopManageRow(\'up\', '+cellCount+');"/><img src="images/icon/'+dnImg+'" class="TAPManageIcon" onclick="javascript:preTally.Track.TAPManagePopManageRow(\'down\', '+cellCount+');"/><img src="images/icon/cross.gif" class="TAPManageIcon" onclick="javascript:preTally.Track.TAPManagePopManageRow(\'remove\', '+cellCount+');"/>');
                cellCount++;
            }
        },
        newTrackEnquiry : function(){
            var AEId ;
            dhxAutomateWinObj = new dhtmlXWindows();
            TAPNotesPopUp = new dhtmlXPopup();

            dhxAutomateWin = dhxAutomateWinObj.createWindow("wins_msg", 1, 38,($(document).width()-29),($(document).height()-65));
            dhxAutomateWin.denyResize();
            dhxAutomateWin.setModal(true);
            dhxAutomateWin.stick();
            dhxAutomateWin.hideHeader();
            
            automateLayout  = dhxAutomateWin.attachLayout("3L");
            automateLayout.cells("a").setText("Track Automate Form");
            automateLayout.cells("a").setCollapsedText("<b style='font-size:14px; cursor:pointer; width:"+ ($(document).height()-65) +"px; padding-left:"+ ($(document).height()-450) +"px; ' onclick='preTally.Track.collapseEnquiry();'><img src='images/icon/down_12.png' />&nbsp;&nbsp;Click Here To Enquire For More Documents.</b>");
            //automateLayout.cells("a").hideHeader();
            automateLayout.cells("b").setText("List Process Options");
            automateLayout.cells("b").hideHeader();
            automateLayout.cells("c").setText("Selected Certificates & Process");
            automateLayout.cells("a").setWidth(459);
            
            var automateToolbar = automateLayout.cells("b").attachToolbar();
            
            automateToolbar.addButton('enquireMoreDocs', 1, 'Enquiry Form', 'images/icon/arrow_anim.gif');
            automateToolbar.addSeparator('SPre2',4);
            automateToolbar.addButton('instructionEnquiry', 5, 'Instructions', 'images/icon/instructions_24.gif');
            automateToolbar.addSeparator('SPre3',6);
            automateToolbar.addButton('dummyButton', 7, '');
            
            automateToolbar.addSpacer('dummyButton');
            automateToolbar.addSeparator('S2',8);
            automateToolbar.addSeparator('S3',9);
            automateToolbar.addButton('saveEnquiry', 10, 'Proceed To Job', 'images/icon/tick_16.png');
            automateToolbar.addSeparator('S5',11);
            automateToolbar.addSeparator('S6',12);
            automateToolbar.addButton('cancelEnquiry', 13, 'Save As Enquiry', 'images/icon/track_16.png');
            automateToolbar.addSeparator('S8',14);
            automateToolbar.addSeparator('S9',15);
            automateToolbar.addButton('cancelProcess', 16, 'Cancel Process', 'images/icon/cross.png');
            automateToolbar.addSeparator('S11',17);
            automateToolbar.addSeparator('S12',18); 
            automateToolbar.addButton('closeEnquiry', 16, 'Close Enquiry', 'images/icon/cross_16.png');
            
            automateToolbar.disableItem('cancelProcess');
            
            automateToolbar.hideItem('enquireMoreDocs');
            automateToolbar.hideItem('SPre2');
            automateLayout.attachEvent("onExpand", function(name){
                automateToolbar.hideItem('enquireMoreDocs');
                automateToolbar.hideItem('SPre2');
            });

            automateLayout.attachEvent("onCollapse", function(name){
                automateToolbar.showItem('enquireMoreDocs');
                automateToolbar.showItem('SPre2');
            });
            
            TAPInstnPopUp = new dhtmlXPopup({ 
                toolbar: automateToolbar,
                id: "instructionEnquiry" //attaches popup to the "Open" button
            });
            var instnPopForm = TAPInstnPopUp.attachForm();
            var ATPInstnFormData = [
                {type:"fieldset", width:"800", label:"General Instructions In Track    <img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' id=\'instnCloseIcon\' onclick=\'preTally.Track.hideATPInstn()\' />", 
                 list:[
                    {type:"template", name:'instnFormItem', label:'', value:'' }] 
                }
            ];
            instnPopForm.load(ATPInstnFormData);
            window.dhx4.ajax.post(preTally.Initialize.encryptURL("requisites/ATPInstnForm.php"), function(response){
                instnPopForm.setItemValue('instnFormItem', response.xmlDoc.response);
            });
            
            TAPCancelPopUp = new dhtmlXPopup({ 
                toolbar: automateToolbar,
                id     : "cancelProcess" //attaches popup to the "Open" button
            });
            var cancelPopForm = TAPCancelPopUp.attachForm();
            var ATPCancelFormData = [{type:"fieldset", width:"250", label:"Reason for Cancelling Process <img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' id=\'instnCloseIcon\' onclick=\'preTally.Track.hideCancelPop()\' />", 
                                        list:[
                                            {type: "settings", position: "label-right"},
                                            {type: "radio",  name: "TAPCancel", value: "1", label: "Due to rate", checked: true},
                                            {type: "radio",  name: "TAPCancel", value: "2", label: "Another branch"},
                                            {type: "radio",  name: "TAPCancel", value: "3", label: "Another enquiry"},
                                            {type: "radio",  name: "TAPCancel", value: "4", label: "Others"},
                                            {type: "input",  name: "TAPCancel_Reason", rows : "2", value: "", label: "", note : { text : "Reason"} },
                                            {type: "button", name: "TAPCancel_Submit", value: "Submit", label: "" }
                                        ]
                                    }];
            cancelPopForm.load(ATPCancelFormData);
            cancelPopForm.hideItem("TAPCancel_Reason");
            
            cancelPopForm.attachEvent("onChange",function(name, value){ 
                if(value == 4 || name == 'TAPCancel_Reason')
                    cancelPopForm.showItem("TAPCancel_Reason");
                else
                    cancelPopForm.hideItem("TAPCancel_Reason");
            });
            cancelPopForm.attachEvent("onButtonClick",function(name){
                if(name == 'TAPCancel_Submit') {
                    var setReqArrayEnq = ["clientNo","clientName","clientEmail","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country","CN_Id", "AA_VisaType", "ADOC_Id","AA_Issuing_CNId", "APS_Id","AA_IssuedYear"];
                    preTally.Functions.setRequired(TAPFormSupport, setReqArrayEnq, true);

                    TAPFormSupport.attachEvent("onValidateError", function (name, value, result){
                        TAPFormSupport.setValidateCss(name, false, 'validate_red');
                        return false;
                    });

                    if(TAPFormSupport.validate() && AEId){ 
                        var params = "&id="+AEId;
                        cancelPopForm.send(preTally.Initialize.encryptURL("warehouse/cancelProcess.php"+params),function(loader, response){
                            dhtmlx.message({text: response}); 
                            TAPCancelPopUp.hide();
                            TAPFormSupport.clear();
                            var comboArray  = [AA_APSCombo, AA_ADOCCombo,AA_CountryCombo,AA_IssuingCNCombo,APM_IdCombo];
                            preTally.Functions.clearComboValues(comboArray);
                        });
                    }
                }
            });
            
            automateToolbar.attachEvent("onClick", function(id){
                //console.log(ATPOptionSelected);
                if(id == 'instructionEnquiry') {
                    TAPInstnPopUp.show();
                    $('#instnCloseIcon').css({'margin-left':'585px'});
                    return;
                }
                if(id == 'enquireMoreDocs') {
                    automateLayout.cells("a").expand();
                    return;
                }
                if(id == 'closeEnquiry'){ 
                    preTally.Track.ListTrackJobs();
                    preTally.Track.disableEnableToolBarItems('list_track_jobs');
                    dhxAutomateWin.close();
                    return;
                }
                
                var setReqArrayEnq = ["clientNo","clientName","clientEmail","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country","CN_Id", "AA_VisaType", "ADOC_Id","AA_Issuing_CNId", "APS_Id","AA_IssuedYear"];
                preTally.Functions.setRequired(TAPFormSupport, setReqArrayEnq, true);

                TAPFormSupport.attachEvent("onValidateError", function (name, value, result){
                    TAPFormSupport.setValidateCss(name, false, 'validate_red');
                    return false;
                });

                if(TAPFormSupport.validate()){ 
                   
                    var optionCount     = 1;
                    var jobOptionCount  = 1;
                    var enqOptionCount  = 1;
                    var ATPOptionObj    = {}
                    var ATPJobOptionObj = {};
                    var ATPEnqOptionObj = {};
                    
                    if(id == 'saveEnquiry') {
                        if(TAPFormSupport.validate() && ( ATPOptionSelected.getUserData('','TOT_Count') > 0 || TAPOptionCount > 0 ) ){
                            ATPOptionSelected.forEachRow(function(id){
                                if(ATPOptionSelected.cells(id,9).getValue() == 1) { 
                                    ATPJobOptionObj[jobOptionCount] = [];
                                    ATPJobOptionObj[jobOptionCount][0] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_CertificateDetails'));
                                    ATPJobOptionObj[jobOptionCount][1] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_Process'));
                                    ATPJobOptionObj[jobOptionCount][2] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_SupportingDocuments'));
                                    ATPJobOptionObj[jobOptionCount][3] = $.trim(ATPOptionSelected.cells(id,5).getValue().replace("₹", ""));
                                    ATPJobOptionObj[jobOptionCount][4] = ATPOptionSelected.getUserData(id,'AA_Comments');
                                    ATPJobOptionObj[jobOptionCount][5] = ATPOptionSelected.getUserData(id,'LC_Id');
                                    ATPJobOptionObj[jobOptionCount][6] = ATPOptionSelected.getUserData(id,'Date');
                                    ATPJobOptionObj[jobOptionCount][7] = ATPOptionSelected.getUserData(id,'AE_Comments');
                                    jobOptionCount++;
                                }else{
                                    ATPEnqOptionObj[enqOptionCount] = [];
                                    ATPEnqOptionObj[enqOptionCount][0] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_CertificateDetails'));
                                    ATPEnqOptionObj[enqOptionCount][1] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_Process'));
                                    ATPEnqOptionObj[enqOptionCount][2] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_SupportingDocuments'));
                                    ATPEnqOptionObj[enqOptionCount][3] = $.trim(ATPOptionSelected.cells(id,5).getValue().replace("₹", ""));
                                    ATPEnqOptionObj[enqOptionCount][4] = ATPOptionSelected.getUserData(id,'AA_Comments');
                                    ATPEnqOptionObj[enqOptionCount][5] = ATPOptionSelected.getUserData(id,'LC_Id');
                                    ATPEnqOptionObj[enqOptionCount][6] = ATPOptionSelected.getUserData(id,'Date');
                                    ATPEnqOptionObj[enqOptionCount][7] = ATPOptionSelected.getUserData(id,'AE_Comments');
                                    enqOptionCount++;
                                }
                            });

                            var ClientName  = TAPFormSupport.getItemValue('clientName');
                            var ClientPhone = TAPFormSupport.getItemValue('clientNo');
                            var ClientEmail = TAPFormSupport.getItemValue('clientEmail');
        //                    TAPFormSupport.setItemValue("AE_AutomateData", JSON.stringify(ATPOptionObj));
                            TAPFormSupport.setItemValue("AE_JobAutomateData", JSON.stringify(ATPJobOptionObj));
                            TAPFormSupport.setItemValue("AE_EnqAutomateData", JSON.stringify(ATPEnqOptionObj));

                            TAPFormSupport.send(preTally.Initialize.encryptURL("warehouse/ATPEnquiry.php&form=job"), function(loader, response) {
                                if(response != 'fail' && response != 'success') {
//                                    $.post(preTally.Initialize.encryptURL("warehouse/newAttestationJobFromEnquiry.php"),
//                                        { EnquiryId: response , EnquiryFNa : ClientName, EnquiryEml : ClientEmail, EnquiryMb : ClientPhone, JobDetails : JSON.stringify(ATPEnqOptionObj) },
//                                        function(data){
                                            preTally.Settings.progressOff(true, dhxLayout, null);
//                                            if(data!='fail') {
                                                dhxAutomateWin.close();
                                                preTally.Track.TrackRegistrationTabUser(this,parseInt(response.trim()));
//                                            }else {
//                                                dhtmlx.message({text: "Sorry, Some error has occured."}); 
//                                            }
//                                    });
                                }else if(response == 'success'){
                                    preTally.Settings.progressOff(true, dhxLayout, null);s
                                }else{
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    dhtmlx.message({text: "Sorry, Some error has occured."}); 
                                }
                            });
                        }else{
                            dhtmlx.message({text: "Please add atleast one option before Proceed to job"}); 
                        }
                    }

                    if(id == 'cancelEnquiry') {
                        if(TAPFormSupport.validate() && ( ATPOptionSelected.getUserData('','TOT_Count') > 0 || TAPOptionCount > 0 ) ){
                            ATPOptionSelected.forEachRow(function(id){
                                ATPOptionObj[optionCount] = [];
                                ATPOptionObj[optionCount][0] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_CertificateDetails'));
                                ATPOptionObj[optionCount][1] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_Process'));
                                ATPOptionObj[optionCount][2] = JSON.parse(ATPOptionSelected.getUserData(id,'AA_SupportingDocuments'));
                                ATPOptionObj[optionCount][3] = $.trim(ATPOptionSelected.cells(id,5).getValue().replace("₹", ""));
                                ATPOptionObj[optionCount][4] = ATPOptionSelected.getUserData(id,'AA_Comments');
                                ATPOptionObj[optionCount][5] = ATPOptionSelected.getUserData(id,'LC_Id');
                                ATPOptionObj[optionCount][6] = ATPOptionSelected.getUserData(id,'Date');
                                ATPOptionObj[optionCount][7] = ATPOptionSelected.getUserData(id,'AE_Comments');
                                optionCount++;
                            });

                            TAPFormSupport.setItemValue("AE_AutomateData", JSON.stringify(ATPOptionObj));
                            TAPFormSupport.send(preTally.Initialize.encryptURL("warehouse/ATPEnquiry.php&form=enq"), function(loader, response) {
                            });
                            preTally.Track.ListTrackJobs();
                            preTally.Track.disableEnableToolBarItems('list_track_jobs');
                            dhxAutomateWin.close();
                        }else{
                            dhtmlx.message({text: "Please add suggested option before save as enquiry."});
                        }
                            
                    }
                }else{
                    var showItemArray = ['GOGL_Street', 'GOGL_Place', 'GOGL_Location', 'GOGL_City', 'GOGL_State', 'GOGL_Country'];
                    $.each(showItemArray, function( index, value ) {
                        TAPFormSupport.showItem(value); 
                    });
                }
            });

            

            TAPFormSupport = automateLayout.cells('a').attachForm();
            preTally.Settings.progressOn(true, dhxLayout, null);
            TAPFormSupport.loadStruct(preTally.Initialize.encryptURL("requisites/trackAutomateForm.php&for=enq"),function(){
                
                preTally.GoogleAddress.googleAddress(TAPFormSupport);
                
                AA_CountryCombo     = TAPFormSupport.getCombo("CN_Id");
                AA_IssuingCNCombo   = TAPFormSupport.getCombo("AA_Issuing_CNId");
                APM_IdCombo         = TAPFormSupport.getCombo("AE_LastProcess");
                
                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/countries.php&form=automate")
                }).done(function(data) {
                    var listCountryCombo = data;

                    AA_CountryCombo.load(listCountryCombo, function(){ });
                    AA_IssuingCNCombo.load(listCountryCombo, function(){ AA_IssuingCNCombo.deleteOption(1000); AA_IssuingCNCombo.setComboValue(90); });
                });
                
                preTally.Settings.progressOff(true, dhxLayout, null);
                ATPOptionSelected = automateLayout.cells("c").attachGrid();
                ATPOptionSelected.setImagePath("assets/grid/codebase/imgs/");

                ATPOptionSelected.setHeader("SlNo,Certificate Details,Process To Be Completed,Supporting Documents Required,Added Details,Amount,Process,#cspan,#cspan,");
                ATPOptionSelected.setInitWidths("40,*,*,*,120,70,35,35,35,30");
                ATPOptionSelected.setColAlign("center,left,left,left,left,right,center,center,center,center");
                ATPOptionSelected.setColTypes("ro,ro,ro,ro,ro,ed,ro,ro,ro,ch,ro");
                ATPOptionSelected.enableEditEvents(true,true,true);
                ATPOptionSelected.enableColSpan(true);
                ATPOptionSelected.setMultiLine(true);
                ATPOptionSelected.init();
                TAPOptionCount = 0;
                
                ATPOptionSelected.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                    if(cInd == 5) {
                        if(stage==0){
                            //console.log("User starting cell editing: row id is"+rId+", cell index is "+cInd)
                        } else if(stage==1){
                            ATPOptionSelected.editor.getInput().value = $.trim(ATPOptionSelected.cells(rId,cInd).getValue().replace("₹", ""));
                        } else if(stage==2){
                            var optnAmount = $.trim(ATPOptionSelected.cells(rId,cInd).getValue().replace("₹", ""));
                            //console.log(optnAmount);
                            if(isNaN(optnAmount)) {
                                dhtmlx.message({
                                    title: "Amount Error",
                                    type:  "alert-warning",
                                    text:  "Please Enter A Valid Amount.",
                                });
                                return false;
                            } else {
                                ATPOptionSelected.cells(rId,cInd).setValue("₹ "+optnAmount); 
                                ATPOptionSelected.setUserData(rId,"AJD_Amount",optnAmount);
                            }
                        }
                    }
                    return true;
                })

                ATPOptionSelected.attachEvent("onMouseOver", function(id,ind) { 
                    if(ind == 6) {
                        this.cells(id,ind).cell.title = ' Delete';
                        return false;
                    }if(ind == 7) {
                        this.cells(id,ind).cell.title =  'Click here to view more details.';
                        return false;
                    }
                    if(ind == 8) {
                        this.cells(id,ind).cell.title =  'Add/view Comments.';
                        return false;
                    }
                });
                AA_CNCombo    = TAPFormSupport.getCombo("CN_Id");
                AA_VTypeCombo = TAPFormSupport.getCombo("AA_VisaType");
                AA_ADOCCombo  = TAPFormSupport.getCombo("ADOC_Id");
                AA_APSCombo   = TAPFormSupport.getCombo("APS_Id");

                AA_APSCombo.setOptionWidth(400);
                AA_ADOCCombo.setOptionWidth(400);
                
                AA_APSCombo.setTemplate({input: "#capital#", option: "#capital# - #country#"});
                
                TAPFormSupport.attachEvent("onBlur", function(name){
                    if(name == 'GOGL_PlaceSearch') {
                        setTimeout(function(){ 
                            var showItemArray = ['GOGL_Street', 'GOGL_Place', 'GOGL_Location', 'GOGL_City', 'GOGL_State', 'GOGL_Country'];
                            $.each(showItemArray, function( index, value ) {
                                TAPFormSupport.showItem(value); 
                            });
                        }, 200);
                    }
                });
                

                TAPFormSupport.attachEvent("onButtonClick", function(name){
                    if(name == 'listATPOptions') {
                        var setReqArray = ["CN_Id", "AA_VisaType", "ADOC_Id","AA_Issuing_CNId", "APS_Id","AA_IssuedYear"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArray, true);

                        var setReqArrayEnq = ["clientNo","clientName","clientEmail","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArrayEnq, true);

                        TAPFormSupport.attachEvent("onValidateError", function (name, value, result){
                           TAPFormSupport.setValidateCss(name, false, 'validate_red');
                           return false;
                        });
                        if(TAPFormSupport.validate()) {
                            automateToolbar.enableItem('cancelProcess');

                            TAPFormSupport.send(preTally.Initialize.encryptURL("warehouse/attestationEnquiry.php"), function(loader, response) {
                                if(!isNaN(response)) AEId = response;
                                    TAPFormSupport.setItemValue("AE_Id", AEId);
                            });

                            TAPFormSupport.send(preTally.Initialize.encryptURL("requisites/listATPOptions.php&forP=N"), function (data) {

                                ATPOptions = automateLayout.cells("b").attachGrid();
                                ATPOptions.init();  
                                ATPOptions.setMultiLine(true);
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                ATPOptions.parse(data.xmlDoc.response, function() {
                                    preTally.Settings.progressOff(true, dhxLayout, null);

                                });

                                var optionCount = ATPOptions.getUserData("","optionCount");
                                if(optionCount > 0) {
                                    automateLayout.cells("a").collapse();
                                }else if(optionCount == 0){
                                    ATPOptionPop = new dhtmlXPopup({ form: TAPFormSupport, id:["listATPOptions"] });
                                    ATPOptionPop.attachHTML("<b>No Presetted Data.</b>");
                                    ATPOptionPop.show("listATPOptions");
                                }

                                ATPOptions.attachEvent("onCheck", function(rId,cInd,state){
                                    ATPOptions.cells(rId,4).setValue(0);
                                    ATPOptions.cells(rId,7).setValue(0);

                                    ATPOptions.cells(rId,cInd).setValue(1);
                                });
                                                                
                                ATPOptions.attachEvent("onRowSelect", function(id,ind){
                                     if(ind != 0 && ind != 9 ) $( "._amntSplitUp"+id ).click();
                                });
                            });
                        }else{
                            var showItemArray = ['GOGL_Street', 'GOGL_Place', 'GOGL_Location', 'GOGL_City', 'GOGL_State', 'GOGL_Country'];
                            $.each(showItemArray, function( index, value ) {
                                TAPFormSupport.showItem(value); 
                            });
                        }
                    } else if(name == 'listCandidateATPOptions'){
                        var setReqArray = ["CN_Id", "AA_VisaType", "ADOC_Id","AA_Issuing_CNId", "AE_LastProcess", "APS_Id","AA_IssuedYear","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArray, false);

                        var setReqArrayEnq = ["clientNo","clientName","clientEmail","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArrayEnq, false);

                        var setReqArrayEnq = ["clientNo"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArrayEnq, true);

                        TAPFormSupport.attachEvent("onValidateError", function (name, value, result){
                           TAPFormSupport.setValidateCss(name, false, 'validate_red');
                           return false;
                        });
                        if(TAPFormSupport.validate()){
                            automateToolbar.enableItem('cancelProcess');
                            
                            var mobNum = TAPFormSupport.getItemValue('clientNo');
                            
                            TAPFormSupport.clear();
                            preTally.Functions.clearComboValues([AA_ADOCCombo, AA_APSCombo,AA_CNCombo,AA_VTypeCombo] , 'automateForm');
                            TAPFormSupport.getCombo("AA_Issuing_CNId").setComboValue(90);
                            
                            TAPFormSupport.setItemValue("clientNo", mobNum);

                            var params = '&filter='+ new Array(TAPFormSupport.getItemValue('clientName'),TAPFormSupport.getItemValue('clientNo'),TAPFormSupport.getItemValue('clientEmail')); 
                            ATPOptionSelected.clearAndLoad(preTally.Initialize.encryptURL("requisites/listATPEnquiry.php&"+params), function (data) {

                                AEId = ATPOptionSelected.getUserData("", "AE_Id");
                                TAPOptionCount = ATPOptionSelected.getUserData("", "TOT_Count");
                                if(TAPOptionCount == 0){
                                    ATPEnquiryCheckPop = new dhtmlXPopup({ form: TAPFormSupport, id:["listCandidateATPOptions"] });
                                    ATPEnquiryCheckPop.attachHTML("<b>We don't have any Records with this number</b>");
                                    ATPEnquiryCheckPop.show("listCandidateATPOptions"); 
                                }else{
                                    if(ATPEnquiryCheckPop)ATPEnquiryCheckPop.hide("listCandidateATPOptions"); 
                                }
                                TAPFormSupport.setItemValue("AE_Id", ATPOptionSelected.getUserData("", "AE_Id"));
//                                    if(ATPOptionSelected.getUserData("", "TOT_Count") > 0 ){
                                TAPFormSupport.setItemValue("clientName", ATPOptionSelected.getUserData("", "AE_Name"));
                                TAPFormSupport.setItemValue("clientEmail", ATPOptionSelected.getUserData("", "AE_Email"));
                                //TAPFormSupport.setItemValue("certificateHolderName", ATPOptionSelected.getUserData("", "AE_Cust_Name"));
//                                TAPFormSupport.setItemValue("clientCallingFrom", ATPOptionSelected.getUserData("", "AE_Cust_Loc"));
                                TAPFormSupport.setItemValue("GOGL_Street", ATPOptionSelected.getUserData("", "SR_Name"));
                                TAPFormSupport.setItemValue("GOGL_Place", ATPOptionSelected.getUserData("", "PL_Name"));
                                TAPFormSupport.setItemValue("GOGL_Location", ATPOptionSelected.getUserData("", "ALC_Name"));
                                TAPFormSupport.setItemValue("GOGL_City", ATPOptionSelected.getUserData("", "CT_Name"));
                                TAPFormSupport.setItemValue("GOGL_State", ATPOptionSelected.getUserData("", "ST_Name"));
                                TAPFormSupport.setItemValue("GOGL_Country", ATPOptionSelected.getUserData("", "CN_Name"));
                                //TAPFormSupport.setItemValue("GOGL_Pincode", ATPOptionSelected.getUserData("", "AE_Pincode"));
                                
                                TAPFormSupport.setItemValue("CN_Id", ATPOptionSelected.getUserData("", "CN_Id"));
                                TAPFormSupport.setItemValue("AA_VisaType", ATPOptionSelected.getUserData("", "AA_VisaType"));
                                TAPFormSupport.setItemValue("ADOC_Id", ATPOptionSelected.getUserData("", "ADOC_Id"));
                                TAPFormSupport.setItemValue("AA_Issuing_CNId", ATPOptionSelected.getUserData("", "AA_Issuing_CNId"));
                                TAPFormSupport.setItemValue("AE_LastProcess", ATPOptionSelected.getUserData("", "AE_LastProcess"));
                                TAPFormSupport.setItemValue("APS_Id", ATPOptionSelected.getUserData("", "APS_Id"));
                                TAPFormSupport.setItemValue("AA_CourseType", ATPOptionSelected.getUserData("", "AA_CourseType"));
                                TAPFormSupport.setItemValue("AA_IssuedYear", ATPOptionSelected.getUserData("", "AA_IssuedYear"));
                                
                                setTimeout(function(){
                                    TAPFormSupport.send(preTally.Initialize.encryptURL("requisites/listATPOptions.php&forP=N"), function (data) {

                                        ATPOptions = automateLayout.cells("b").attachGrid();
                                        ATPOptions.init();  
                                        ATPOptions.setMultiLine(true);
                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                        ATPOptions.parse(data.xmlDoc.response, function() {
                                            preTally.Settings.progressOff(true, dhxLayout, null);

                                        });

                                        ATPOptions.attachEvent("onCheck", function(rId,cInd,state){
                                            ATPOptions.cells(rId,4).setValue(0);
                                            ATPOptions.cells(rId,7).setValue(0);

                                            ATPOptions.cells(rId,cInd).setValue(1);
                                        });
                                        
                                        $('.optionCellClass').on('click',function(e){
                                            
                                            var id = $(this).find('.optionDivClass').val();
                                            if ($(e.target).is('img')) { 
                                                if($(this).find('.optionDivClass').attr('id'))
                                                    preTally.Track.showAutomateProcessComment(this,id,'listOptn');
                                            } else {
                                                $( "._amntSplitUp"+id ).click();
                                            }
                                        });
                                        

                                        ATPOptions.attachEvent("onRowSelect", function(id,ind){
                                           if(ind != 0 && ind != 9 && ind != 10 ) $( "._amntSplitUp"+id ).click();                                           
                                        });
                                    });
                                },50);
                            });
                            
                        } 
                    } 
                });

                TAPFormSupport.attachEvent("onChange", function(name, value){
                    automateToolbar.disableItem('cancelProcess');
                });
            });
            var popupNameArray = ["clientName","clientEmail","GOGL_PlaceSearch","GOGL_Street", "GOGL_Place", "GOGL_Location", "GOGL_City", "GOGL_State", "GOGL_Country","CN_Id","AA_VisaType","ADOC_Id","AA_Issuing_CNId","APS_Id","AA_CourseType"];
            var popupMsgArray  = ["Caller Name","Email ID","Enter Location","Street","Place","Location","City","State","Country","Where do you want to go?","Why you need this attestation?","Certificate/Degree/Service","Where do you study?","Certificate issued by?","Is this a regular course?"];
            TAPFormPop = new dhtmlXPopup({ form: TAPFormSupport, id:popupNameArray });

            TAPFormSupport.attachEvent("onFocus", function(id,value){ 
                if(id == 'clientNo' || id == 'AA_IssuedYear'){
                    TAPFormButtonPop = new dhtmlXPopup({ form: TAPFormSupport, id:["listCandidateATPOptions","listATPOptions"] });
                   
                    if(id == 'clientNo'){
                        TAPFormButtonPop.attachHTML("Mobile Number");
                        TAPFormButtonPop.show("listCandidateATPOptions");
                    }else if(id == 'AA_IssuedYear'){
                        TAPFormButtonPop.attachHTML("Passed Year");
                        TAPFormButtonPop.show("listATPOptions");
                    }
                    
                    TAPFormButtonPop.attachEvent("onHide", function(){ 
                        if($(':focus')[0]) {
                            if($(':focus')[0]['name'] == 'clientNo') {TAPFormButtonPop.show("listCandidateATPOptions");return true;}
                            if($(':focus')[0]['name'] == 'AA_IssuedYear') { TAPFormButtonPop.show("listATPOptions");return true; }
                        }
                    });
                } else {
                    TAPFormPop.attachHTML(popupMsgArray[popupNameArray.indexOf(id)]);
                    TAPFormPop.show(id);
                }
            });
            TAPFormSupport.attachEvent("onBlur", function(id,value){
                if(id == 'clientNo' || id == 'AA_IssuedYear')
                    TAPFormButtonPop.hide();
                else
                    TAPFormPop.hide();
            });
        },
        collapseEnquiry : function () {
            automateLayout.cells("a").expand();
        },
        hideATPInstn : function() {
            TAPInstnPopUp.hide();
        },
        hideCancelPop : function() {
            TAPCancelPopUp.hide();
        },
        showEnqAmtDetailsPop: function(inp,rowId){
            
            if(ATPEnquiryCheckPop) ATPEnquiryCheckPop.hide();
            
            if(!enqAmtDetailsPop)
            enqAmtDetailsPop = new dhtmlXPopup({mode: "right"});
                                                
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight; 
            
            
            var width  = automateLayout.cells("c").getWidth();
            var height = automateLayout.cells("c").getHeight();
            
            if (enqAmtDetailsPop.isVisible()) {
                enqAmtDetailsPop.hide();
            } 

            enqAmtDetailsPop.show(x,y,w,h);
            $.post(preTally.Initialize.encryptURL('requisites/enqAmountDetails.php'), {   
                AA_Process      : ATPOptions.getUserData(rowId,'AA_Process'),
                TAP_U_Amount    : ATPOptions.getUserData(rowId,'TAP_U_Amount'),
                TAP_N_Amount    : ATPOptions.getUserData(rowId,'TAP_N_Amount'),
                VisitingCN      : TAPFormSupport.getCombo("CN_Id").getComboText(),
                VisaType        : TAPFormSupport.getCombo("AA_VisaType").getComboText(),
                Certificate     : TAPFormSupport.getCombo("ADOC_Id").getComboText(),
                CRIssuedBy      : TAPFormSupport.getCombo("APS_Id").getComboText(),
                Width           : width,
                Height          : height
            },function(data) {
                enqAmtDetailsPop.attachHTML(data);
            });  
            
            $( "._amntSplitUp"+rowId ).unbind();
        },
        hideEnqAmtDetailsPop : function(){
            if (enqAmtDetailsPop.isVisible()) {
                enqAmtDetailsPop.hide();
            } 
        },
        printProcessDetails : function () {
            
            
            var head_start = '<!DOCTYPE html>'+
                    '<html>'+
                        '<head>'+
                            '<title>Bill Form</title>'+
                            
                            '<style>'+
                            '@page {size: A4;margin: 0;} @media print {html, body {width: 210mm;height: 297mm;}'+
                            '.page {page-break-after: always;}}'+
                            '#wapper{position: relative;width: 100%;height: 100%;overflow: auto;}'+
                            '#content{width:99%;height:auto;margin-top:1%;border:1px solid #000;margin-left:auto;margin-right:auto;padding-bottom: 1%;margin-bottom:1%;}'+	
                            '.heading{font-size: 24px;font-weight: bold;margin-top: 1%;text-align: center;width: auto;}'+	
                            '.heading2{font-size: 16px;text-align: center;width: auto;}'+
                            '.clr{clear:both;}'+
                            '.div1{font-size: 16px;width: auto;margin-left:1%;float:left;}'+	
                            '.div2{font-size: 16px;width: auto;margin-right:1%;float:right;}'+	
                            'hr{width:100%;height:1px;background:#000;}'+
                            '.invoice{width:auto;font-size:14px;float:left;margin-left:1%;}'+
                            '.date{width:auto;font-size:14px;float:right;margin-right:1%;}'+
                            '.datagrid table { border-collapse: collapse; text-align: left; width: 100%; }'+
                            '.datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden;  -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;margin-left: 1%; margin-top: 1%;width: 97.7%; }'+
                            '.datagrid table td, .datagrid table th { padding: 3px 10px; }'+
                            '.datagrid table thead th {border-bottom: 1px solid #000;border-left: 1px solid #000;color: black;font-size: 16px;font-weight: bold; }'+                           
                            '.datagrid table tbody td { color:#000; border-left: 1px solid black;font-size: 16px;font-weight: normal; }'+
                            '.datagrid table tbody .alt td { color: #000;border: 1px solid black; border-right:none; }'+  
                            '.datagrid table tbody td:first-child { border-left: none; }'+  
                            '.datagrid table tbody tr:last-child td { border-bottom: none; }'+  
                            '.datagrid table tfoot td div { border-top: 1px solid #006699;background: #E1EEF4;}'+  
                            '.datagrid table tfoot td { padding: 0; font-size: 12px } '+  
                            '.datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }'+  
                            '.datagrid table tfoot  li { display: inline; }'+
                            '.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #006699;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#006699", endColorstr="#00557F");background-color:#006699; }'+		
                            '.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #006699; color: #FFFFFF; background: none; background-color:#00557F;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }'+
                            '.borderleft-none{border-left:none !important;}'+
                            '.terms{margin-top:1%;margin-left:1%;font-size:15px;}'+
                            '.terms_conditions{width:95.7%;height:auto;margin-left:1%;padding:1%;border:1px solid;text-align:justify;}'+
                            '.div3{float: right;margin-right: 1%;margin-top: 2%;font-weight:bold;}'+
                            '.div4{float: right;margin-right: 1%;margin-top: 2%;}'+
                            '.div5{float: left;margin-left: 1%;margin-top: 2%;font-size:18px;}'+	
                            '.center_align {text-align: center;}'+
                            '.amntCols {text-align: right;}'+
                            '.subTitleDiv {width : 90% !important;}'+
                            '</style>';
                        '</head>'+
                        '<body style="width:100%;">';
                var head_end = '</body>'+
                    '</html>';
                
                
                //$('#printbtn').hide();
                //$('#closebtn').hide();
                var body = $("#content").clone();
                body.find('#printbtn, #closebtn').hide();
                //body.find('.trackPrintTable tr').length;
                
//                var printBreakStr = '</tbody></table><table class="trackPrintTable" border="1">'+
//                                    '<tr><th>Item</th><th>Urgent Amount</th><th>Normal Amount</th></tr>';
//                var printBreak = $.parseHTML( printBreakStr );            
//                body.find('.trackPrintTable tr:eq(32)').after(printBreak);    
               //body.find('.trackPrintTable tr').not('.tableBodyRows').remove();
              
                var divs = body.find('.trackPrintTable tr.tableBodyRows');
                body.find('.trackPrintTable').remove();
                body.find('.amountData').append(divs);
                //console.log(body.html());
                for(var i = 0; i < divs.length; i+=28) {
                    if(i>=28) divs.slice(i, i+28).wrapAll('<table class="trackPrintTable" style="margin-top : 40px !important;" border="2"></table>');
                    else  divs.slice(i, i+28).wrapAll('<table class="trackPrintTable" border="2"></table>');
                }
                
                
                //body.find('.trackPrintTable:first').remove();
                //body.find('.trackPrintTable:first').attr('border','0');
                
                //console.log(body.html());
                body.find('.trackPrintTable').prepend('<thead><tr><th>Item</th><th>Normal Amount</th><th>Urgent Amount</th></tr></thead>');
                body.find('.trackPrintTable').after('<div class="page"></div>');
                body.find('.page:last').remove();
                body=body.html();
                //console.log(body);
                
                //$('#printbtn').show();
                //$('#closebtn').show();
                
                //var body = '<div class = "page"> hai </div><div class = "page"> hai </div>';
                //console.log(body);
                var mywindow = window.open('', 'my div');
		mywindow.document.write(head_start+body+head_end);
                mywindow.print();
                mywindow.close();
                
                
                
                
//            $.ajax({
//                url : preTally.Initialize.encryptURL('trackProcessDetailsPDF.php'),
//                data : "data=2132"
//            }).done(function(data) {
//                if(data != "fail")
//                    window.open("uploads/processAmtReceipts/"+data,'Download');  
//                else
//                    dhtmlx.message({text: "Some error has occured."}); 
//            });
//
/*
            var divToPrint=document.getElementById("wapper");
            newWin= window.open("");
            newWin.document.write(divToPrint.outerHTML);
            newWin.print();
            //newWin.close();
            */

        },
        //---------------------------------------------------- Add/Edit Jobs ---------------------------------------------------//        
        TrackRegistrationTabUser : function(inp,AJ_Id){
            
            trackDocumentID    = 0;
            trackProcessCount  = 0;
            trackDocumentArray = []; 
            TrackJobId = AJ_Id;
            
            jobCommentPopUp = new dhtmlXPopup({mode: "left"});
            jobComment = jobCommentPopUp.attachEditor(500, 300);
            jobCommentPopUp.attachEvent("onBeforeHide", function(type, ev, id){
                //return true; 
                return false;
            });
            $('#'+jobCommentPopUp._nodeId).find('.dhx_cell_editor').find('.dhx_cell_stb').append('<a href="javascript:void(0);" style="float:right; margin:6px; 3px 15px 3px;" onclick="preTally.Track.TrackJobCommentPopUpSave();"><img src="images/icon/tick_16.png" /></a><a href="javascript:void(0);" style="float:right; margin:6px; 3px;" onclick="preTally.Track.TrackJobCommentPopUpHide();"><img src="images/icon/cross.png" /></a>');
            
            $('#pageReceiptTotalAmount').remove();
            $('#pageReceiptAmount').remove();
            $('#cDetailsEstimate').remove();
            $('#cDetailsReceipt').remove();
            $('#cDetailsInvoice').remove();
            $('#invoiceDate').remove();
            $('#DRCR_RPT').remove();
            $('#cDetailsInvoice').remove();
            $('#invoiceDate').remove();
            $('.datagrid').remove();
            $('#amountInRs').remove();
          
            preTally.Track.trackButtonsShowHide(1,0,1,0);
//            preTally.Track.disableEnableToolBarItems('new_track_user_registration');
            
            if(trackConsoleSideBar.items("SB_new_job_user_registration_"+TrackJobId)) 
                trackConsoleSideBar.cells("SB_new_job_user_registration_"+TrackJobId).remove();

            trackConsoleSideBar.addItem({ id:"SB_new_job_user_registration_"+TrackJobId});
            trackConsoleSideBar.items("SB_new_job_user_registration_"+TrackJobId).setActive();

            newUserTrackSidebar = trackConsoleSideBar.cells("SB_new_job_user_registration_"+TrackJobId).attachSidebar({
                icons_path  : "images/sidebar/",
                template    : "icons_text",
                width       : 120,
                items       :   [   {id: "candidate_details", text: "Personal Details", icon: "user.png", selected: true},
                                    {type: "separator"},
                                    {id: "certificate_details", text: "Documents", icon: "documents.png"},
                                    {type: "separator"},
                                    {id: "payment_details", text: "Billing / Invoice", icon: "invoice.png"},
                                    {type: "separator"},
                                    {id: "job_comments", text: "Comment", icon: "comments.png"},
                                    {type: "separator"},
                                    {id: "job_calculator", text: "Calculator", icon: "calculator.png"},
                                    {type: "separator"},
                                    {id: "job_calendar", text: "Calendar", icon: "calendar.png"},
                                    {type: "separator"}
                                ]
            });
            
            trackCandidateDetails = newUserTrackSidebar.cells('candidate_details').attachForm();
            preTally.Settings.progressOn(true, dhxLayout, null);
            var params = "&AJ_Id="+TrackJobId;
            trackCandidateDetails.loadStruct(preTally.Initialize.encryptURL("requisites/editAttestationJob.php"+params),function(){
                preTally.GoogleAddress.multipleGoogleAddress(trackCandidateDetails,3);
                AJ_Status =  trackCandidateDetails.getItemValue('AJ_Status'); 
                if(TrackJobId == 0 || AJ_Status != 0){
                    trackToolbar.setItemText('cancelNewTrackRegistration', 'Close');
                } else{
                    trackToolbar.setItemText('cancelNewTrackRegistration', 'Delete');
                }

                SubmittedTypeCombo = trackCandidateDetails.getCombo("AJ_SubmittedType");
                DeliveredTypeCombo = trackCandidateDetails.getCombo("AJ_DeliveredType");
                preTally.Track.filterJobForm('submittedBy',SubmittedTypeCombo.getSelectedValue());
                preTally.Track.filterJobForm('deliveredTo',DeliveredTypeCombo.getSelectedValue());

                SubmittedTypeCombo.attachEvent("onChange", function() {
                    var submittedByType = SubmittedTypeCombo.getSelectedValue();
                    preTally.Track.filterJobForm('submittedBy',submittedByType);
                });

                DeliveredTypeCombo.attachEvent("onChange", function() {
                    var deliveredByType = DeliveredTypeCombo.getSelectedValue();
                    if(deliveredByType == 3) {
                        trackCandidateDetails.showItem('AD_Branch');
                        trackCandidateDetails.setRequired("AD_Branch",true); 
                    }   else {
                        trackCandidateDetails.hideItem('AD_Branch');
                        trackCandidateDetails.setRequired("AD_Branch",false); 
                    }
                    preTally.Track.filterJobForm('deliveredTo',deliveredByType);
                });
               
                if(trackCandidateDetails.getItemValue("AJ_DeliveredType_Hid") == '2') {
                    if(trackCandidateDetails.getItemValue("AJ_JobDeliverdTo_Hid") == trackCandidateDetails.getItemValue("AJ_DeliveredType"))
                        trackCandidateDetails.setItemValue("AJ_DeliveredType", trackCandidateDetails.getItemValue("AJ_DeliveredType_Hid"));
                } else
                    trackCandidateDetails.setItemValue("AJ_DeliveredType", trackCandidateDetails.getItemValue("AJ_DeliveredType_Hid"));

                if(trackCandidateDetails.getItemValue("AJ_JobDeliverdTo_Hid") != 0) {
                    AD_BranchCombo = trackCandidateDetails.getCombo("AD_Branch");
                    AD_BranchCombo.load(preTally.Initialize.encryptURL("requisites/attestationBranch.php"), function() { 
                        trackCandidateDetails.setItemValue("AD_Branch", trackCandidateDetails.getItemValue("AJ_JobDeliverdTo_Hid"));
                    });
                }
                preTally.Settings.progressOff(true, dhxLayout, null);
            });

            /* Certificate Tab Starts */
            trackCertificateDetails = newUserTrackSidebar.cells('certificate_details').attachLayout({
                pattern: "3U",
                cells: [
                    {id: "a", text: "New Document Form", width: "459"}, //, height: "200"
                    {id: "b", text: "Document Process"},
                    {id: "c", text: "Added Documents"}
                ]
            });

            TAPFormSupport = trackCertificateDetails.cells('a').attachForm();
            
            TAPFormSupport.loadStruct(preTally.Initialize.encryptURL("requisites/trackAutomateForm.php&for=moreDocs"), function() {

                AA_CountryCombo     = TAPFormSupport.getCombo("CN_Id");
                AA_IssuingCNCombo   = TAPFormSupport.getCombo("AA_Issuing_CNId");

                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/countries.php&form=automate")
                }).done(function(data) {
                    var listCountryCombo = data;

                    AA_CountryCombo.load(listCountryCombo, function(){ });
                    AA_IssuingCNCombo.load(listCountryCombo, function(){ AA_IssuingCNCombo.setComboValue(90); });
                });
                
                preTally.Settings.progressOff(true, dhxLayout, null);

                TAPFormSupport.attachEvent("onButtonClick", function(name){
                    if(name == 'listATPOptions') {
                        var setReqArray = ["CN_Id", "AA_VisaType", "ADOC_Id","AA_Issuing_CNId", "AAUTH_Id","AA_IssuedYear"];
                        preTally.Functions.setRequired(TAPFormSupport, setReqArray, true);

                        
                        TAPFormSupport.attachEvent("onValidateError", function (name, value, result){
                           TAPFormSupport.setValidateCss(name, false, 'validate_red');
                           return false;
                        });
                        if(TAPFormSupport.validate()) {
         

                            TAPFormSupport.send(preTally.Initialize.encryptURL("requisites/listATPOptions.php&forP=U"), function (data) {

                                ATPOptions = trackCertificateDetails.cells("b").attachGrid();
                                ATPOptions.init();  
                                ATPOptions.setMultiLine(true);
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                ATPOptions.parse(data.xmlDoc.response, function() {
                                    preTally.Settings.progressOff(true, dhxLayout, null);

                                });

                                var optionCount = ATPOptions.getUserData("","optionCount");
                                if(optionCount > 0) {
                                    trackCertificateDetails.cells("a").collapse();
                                }else if(optionCount == 0){
                                    ATPOptionPop = new dhtmlXPopup({ form: TAPFormSupport, id:["listATPOptions"] });
                                    ATPOptionPop.attachHTML("<b>No Presetted Data.</b>");
                                    ATPOptionPop.show("listATPOptions");
                                }

                                ATPOptions.attachEvent("onCheck", function(rId,cInd,state){
                                    ATPOptions.cells(rId,4).setValue(0);
                                    ATPOptions.cells(rId,7).setValue(0);

                                    ATPOptions.cells(rId,cInd).setValue(1);
                                });
                            });
                        }
                    }
                });

                
            });

            ATPOptionSelected = trackCertificateDetails.cells("c").attachGrid();
            ATPOptionSelected.enableEditEvents(true,true,true);
                
            ATPOptionSelected.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                if(stage==0){
                    //console.log("User starting cell editing: row id is"+rId+", cell index is "+cInd)
                } else if(stage==1){
                    ATPOptionSelected.editor.getInput().value = $.trim(ATPOptionSelected.cells(rId,cInd).getValue().replace("₹", ""));
                } else if(stage==2){
                    var optnAmount = $.trim(ATPOptionSelected.cells(rId,cInd).getValue().replace("₹", ""));
                    //console.log(optnAmount);
                    if(isNaN(optnAmount)) {
                        dhtmlx.message({
                            title: "Amount Error",
                            type:  "alert-warning",
                            text:  "Please Enter A Valid Amount.",
                        });
                        return false;
                    } else {
                        ATPOptionSelected.cells(rId,cInd).setValue("₹ "+optnAmount); 
                        ATPOptionSelected.setUserData(rId,"AJD_Amount",optnAmount);
                        trackDocumentArray = []; 
                        ATPOptionSelected.forEachRow(function(rId){
                            preTally.Track.TrackRegistrationTabCertificate(rId);
                        });
                    }
                }
                return true;
            })

            ATPOptionSelected.loadXML(preTally.Initialize.encryptURL("requisites/listJobDocuments.php&AJ_Id="+TrackJobId), function() {
                

                ATPOptionSelected.forEachRow(function(rId){
                    preTally.Track.TrackRegistrationTabCertificate(rId);
                });
            });

            trackPaymentDetails = newUserTrackSidebar.cells('payment_details').attachLayout({
                pattern: "3J",
                cells: [
                    {id: "a", text: "Billing Details"}, //, height: "200"
                    {id: "b", text: "Invoice", width: "800"},//, width: "250"
                    {id: "c", text: "Document Details", height:"150"}
                ]
            });
            
            $('#TPDToolbarObj').remove();
            var trackPaymentDetailsStatusBar = trackPaymentDetails.cells('a').attachStatusBar({
                text:   "<div id='TPDToolbarObj' style='width:100%; margin:0px;'></div>",   
                height: 30             
            });

            trackPaymentDetailsToolbar = new dhtmlXToolbarObject({
                parent: "TPDToolbarObj",
                icons_path: "assets/toolbar/imgs/"
            });

            trackPaymentDetailsToolbar.addButton("pcr", 1, "Print Cash Receipt", "user.png", "disable.png");
            trackPaymentDetailsToolbar.addSeparator("sep1", 2);
            trackPaymentDetailsToolbar.addSeparator("sep2", 3);
            trackPaymentDetailsToolbar.addButton("per", 4, "Print Estimate", "user.png", "disable.png");
            trackPaymentDetailsToolbar.addSeparator("sep3", 5);
            trackPaymentDetailsToolbar.addSeparator("sep4", 6);
            trackPaymentDetailsToolbar.addButton("ptr", 7, "Print Tax Receipt", "user.png", "disable.png");
            
            trackPaymentDetailsToolbar.disableItem("pcr");
            trackPaymentDetailsToolbar.disableItem("per");
            trackPaymentDetailsToolbar.disableItem("ptr");
   
            trackPaymentDetailsToolbar.attachEvent("onClick", function(id){
                if(id == 'pcr') {
//                    console.log(TrackJobId);
//                    console.log(jobPaymentDetails.getUserData("","AJR_Id"));
                    //console.log();
                    preTally.Track.downloadToPDF('trackCashReceiptPDF.php',TrackJobId,jobPaymentDetails.getUserData("","AJR_Id"),1);
                }
                if(id == 'per') {
                    preTally.Track.downloadToPDF('trackDocReceiptPDF.php',TrackJobId,0,2);
                }
                if(id == 'ptr') {
                    preTally.Track.downloadToPDF('trackTaxInvoicePDF.php',TrackJobId,0,3);
                }
            });
            
            trackPaymentDetailsBilling = trackPaymentDetails.cells('a').attachForm();
            trackPaymentDetailsBilling.loadStruct(preTally.Initialize.encryptURL("requisites/trackPaymentBilling.php&AJ_Id="+TrackJobId), function() {

                trackPaymentDetailsBilling.attachEvent("onKeyUp",function(inp, ev, name, value){
                    if(name == 'TPBBalanceAmount') { //name == 'TPBTotalAmount' || TPBBalanceAmount
                        $('#pageReceiptAmount').html(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount'));
                        
                        var TPBBalanceAmountData = parseInt(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount')) - (parseInt(trackPaymentDetailsBilling.getItemValue('TPBPaidAmount')) + parseInt(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount')));
                        if(isNaN(TPBBalanceAmountData)) {
                            TPBBalanceAmountData = parseInt(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount')) - parseInt(trackPaymentDetailsBilling.getItemValue('TPBPaidAmount'));
                        }
                        trackPaymentDetailsBilling.setItemValue('TPBBalanceAmountData', '<b>' + TPBBalanceAmountData + '</b>');
                    }
                });

                remitModeCombo = trackPaymentDetailsBilling.getCombo("AB_RemitMode");
                var setItemArray = ['TPBAccount', 'TPBPayeeAccount', 'TPBPayeeBank', 'TPBPayeeChqDD'];
                preTally.Functions.setRequired(trackPaymentDetailsBilling, setItemArray, false); 
                preTally.Functions.hideItem(trackPaymentDetailsBilling, setItemArray); 
                remitModeCombo.attachEvent("onChange", function() {
                    
                    if(remitModeCombo.getSelectedValue() == '1') {
                        trackPaymentDetailsBilling.getCombo("TPBAccount").load(preTally.Initialize.encryptURL("requisites/bankaccounts.php"));
                        preTally.Functions.setRequired(trackPaymentDetailsBilling, setItemArray, true); 
                        preTally.Functions.showItem(trackPaymentDetailsBilling, setItemArray); 
                    } else {
                        preTally.Functions.setRequired(trackPaymentDetailsBilling, setItemArray, false); 
                        preTally.Functions.hideItem(trackPaymentDetailsBilling, setItemArray); 
                    }
                });
                trackPaymentDetailsBilling.attachEvent("onButtonClick", function(name) {
                    if (name == 'TPBProceed') {
                        if(trackPaymentDetailsBilling.validate()) {
                            proceedFlag=1;
                            preTally.Track.savePaymentDetails("payment_details",0);
                        }
                    }
                });

                trackPaymentDetailsTab = trackPaymentDetails.cells("b").attachTabbar();
                trackPaymentDetailsTab.addTab("TBDReceipt", "Cash Receipt",null,null,true);
                trackPaymentDetailsTab.addTab("TBDEstimate", "Document Receipt/ Estimate");

                if(trackPaymentDetailsBilling.getItemValue('ACL_TrackInvReceipt') == 1)  // if acl is set
                    trackPaymentDetailsTab.addTab("TBDInvoice", "Tax Invoice");
                
                trackPaymentDetailsTab.addTab("jobPaymentDetails", "Payment Details");
                
                trackPaymentDetailsTab.setSizes();
                $.post(preTally.Initialize.encryptURL('requisites/pageGetContents.php&page=requisites/TPDreceipt.php'), function(data) {
                    trackPaymentDetailsTab.cells('TBDReceipt').attachHTMLString(data);
                });

                $.post(preTally.Initialize.encryptURL('requisites/pageGetContents.php&page=requisites/TPDestimate.php'), function(data) {
                    trackPaymentDetailsTab.cells('TBDEstimate').attachHTMLString(data);
                });
                if(trackPaymentDetailsBilling.getItemValue('ACL_TrackInvReceipt') == 1) {
                    $.post(preTally.Initialize.encryptURL('requisites/pageGetContents.php&page=requisites/TPDinvoice.php'), function(data) {
                        trackPaymentDetailsTab.cells('TBDInvoice').attachHTMLString(data);
                    });
                }
                jobPaymentDetails = trackPaymentDetailsTab.cells('jobPaymentDetails').attachGrid();
                jobPaymentDetails.enableTooltips("false,false,false,false,false");
                jobPaymentDetails.init();

                jobPaymentDetails.loadXML(preTally.Initialize.encryptURL("requisites/trackPaymentDetails.php&AJ_Id="+TrackJobId), function() {
                });
            });

            trackPaymentDetailsDocuments = trackPaymentDetails.cells('c').attachForm();
            trackPaymentDetailsDocuments.loadStruct(preTally.Initialize.encryptURL("requisites/trackPaymentDocuments.json"), function() {
                paymentDetailsPopUp = new dhtmlXPopup({ 
                    form: trackPaymentDetailsDocuments, 
                    id: ["paymentDetailsDocsPopUp", "paymentDetailsSuppDocsPopUp"] 
                });
            });

            newUserTrackSidebar.attachEvent("onBeforeSelect", function(id, lastId){
                if(id === 'job_comments') {
                    //if($('div.dhxsidebar_side_items').children('.dhxsidebar_item').last().hasClass( "dhxsidebar_item_selected" )) {
                    if($( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(7)" ).hasClass( "dhxsidebar_item_selected" )) {
                        //$('div.dhxsidebar_side_items').children('.dhxsidebar_item').last().removeClass( "dhxsidebar_item_selected" );
                        //jobCommentPopUp.hide();
                    } else {
                        //$('div.dhxsidebar_side_items').children('.dhxsidebar_item').last().addClass( "dhxsidebar_item_selected" );
                        $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(7)" ).addClass( "dhxsidebar_item_selected" );
                        jobCommentPopUp.show(122,345,1,1);
                        jobComment.setContent(trackCandidateDetails.getItemValue("AJ_Comments"));
                    }

                    return false;
                } else if(id === 'job_calculator') {
                    if(! $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(9)" ).hasClass( "dhxsidebar_item_selected" )) {
                        $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(9)" ).addClass( "dhxsidebar_item_selected" );
                    }
                    preTally.Settings.menuCalculator();

                    return false;
                } else if(id === 'job_calendar') {
                    if(! $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(11)" ).hasClass( "dhxsidebar_item_selected" )) {
                        $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(11)" ).addClass( "dhxsidebar_item_selected" );
                        preTally.Track.menuCalendar();
                    }
                    return false;
                } else {
                    return true;
                }
            });


            newUserTrackSidebar.attachEvent("onSelect", function(id, lastId){ 
                preTally.Track.trackButtonsShowHide(1,0,1,1);
                
                if(id == 'candidate_details') {
                    preTally.Track.trackButtonsShowHide(1,0,1,0);
                }
                if(id == 'payment_details') {
                    preTally.Track.trackButtonsShowHide(1,1,0,1); 
                    preTally.Track.TrackRegistrationTabBilling(id,0);return false;
                }
                
                if(lastId == 'candidate_details'){ 
                    preTally.Track.saveCandidateDetails(id,0);
                    return false;
                }
                if(lastId == 'certificate_details') {
                    //preTally.Track.TrackRegistrationTabBilling(id,0);return false;
                }
                if(lastId == 'payment_details') {
                    //preTally.Track.savePaymentDetails(id,0);return false;
                }
                
            });
        },
        TrackJobCommentPopUpSave : function () {
            var jobCommentContent = jobComment.getContent();
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(preTally.Initialize.encryptURL("warehouse/updateJobComment.php"), {   
                TrackJobId      : TrackJobId,
                jobComment      : jobCommentContent
            },
            function(response){
                preTally.Settings.progressOff(true, dhxLayout, null); 
                dhtmlx.message({text: 'Comment Updated Successfully'});
                
                trackCandidateDetails.setItemValue("AJ_Comments",jobComment.getContent());
                $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(7)" ).removeClass( "dhxsidebar_item_selected" );
                jobCommentPopUp.hide();
                
            });     
        },
        TrackJobCommentPopUpHide : function () {
            $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(7)" ).removeClass( "dhxsidebar_item_selected" );
            jobCommentPopUp.hide();
        },
        TrackRegistrationTabCertificate : function (rId) { 
            //console.log(ATPOptionSelected.getUserData(rId,"AE_LastProcess"));
            var totalAmount    = 0;
            var trackSingleDocumentArray = new Array();
            trackSingleDocumentArray['AJD_Id']              = ATPOptionSelected.getUserData(rId,"AJD_Id");
            trackSingleDocumentArray['AJD_Amount']          = ATPOptionSelected.getUserData(rId,"AJD_Amount"); 
            trackSingleDocumentArray['AJD_Year']            = ATPOptionSelected.getUserData(rId,"AJD_Year"); 
            trackSingleDocumentArray['ADOC_Id']             = ATPOptionSelected.getUserData(rId,"ADOC_Id");
            trackSingleDocumentArray['ST_Id']               = ATPOptionSelected.getUserData(rId,"ST_Id");
            trackSingleDocumentArray['APS_Id']              = ATPOptionSelected.getUserData(rId,"APS_Id");
            trackSingleDocumentArray['AJD_LastProcess']     = ATPOptionSelected.getUserData(rId,"AE_LastProcess");
            trackSingleDocumentArray['AJD_VisitingCNId']    = ATPOptionSelected.getUserData(rId,"AJD_VisitingCNId"); 
            trackSingleDocumentArray['AJD_VisaType']        = ATPOptionSelected.getUserData(rId,"AJD_VisaType");
            trackSingleDocumentArray['AJD_IssuingCNId']     = ATPOptionSelected.getUserData(rId,"AJD_IssuingCNId");
            trackSingleDocumentArray['AJD_UniqueNo']        = ATPOptionSelected.getUserData(rId,"AJD_UniqueNo");
            trackSingleDocumentArray['AJD_Comment']         = ATPOptionSelected.getUserData(rId,"AA_Comments");
            trackSingleDocumentArray['AJD_Remarks']         = ATPOptionSelected.getUserData(rId,"AE_Comments");
            totalAmount += parseFloat(ATPOptionSelected.getUserData(rId,"AJD_Amount"));

            trackDocumentCount ++;
            trackDocumentID ++;
            //console.log(trackDocumentID);
            //console.log(ATPOptionSelected.getUserData(rId,"processIDs"));
            //trackSingleDocumentArray['processIds'] = ATPOptionSelected.getUserData(rId,"processIDs").split(',');  // rIds
            trackSingleDocumentArray['processIds'] = JSON.parse(ATPOptionSelected.getUserData(rId,"processIDs"));
            trackSingleDocumentArray['supDocIds'] = JSON.parse(ATPOptionSelected.getUserData(rId,"supDocIds"));
            trackDocumentArray[trackDocumentID] = trackSingleDocumentArray; 

            Array.prototype.clean = function(deleteValue) {
                for (var i = 0; i < this.length; i++) {
                    if (this[i] == deleteValue) {         
                        this.splice(i, 1);
                        i--;
                    }
                }
                return this;
            };
            trackDocumentArray.clean(undefined);
            //console.log(trackDocumentArray);
        },
        TrackRegistrationTabBilling : function(menuId,flag){
//            console.log(trackDocumentArray);
            var docEntryArray = [];
            //console.log(trackDocumentArray);
            trackDocumentArray.forEach(function(docEntry) {
                docEntryArray.push(parseInt(docEntry['ADOC_Id']+docEntry['AJD_IssuingCNId']+docEntry['AJD_VisaType']+docEntry['AJD_VisitingCNId']+docEntry['AJD_Year']+docEntry['APS_Id']+docEntry['AJD_LastProcess']));
            });
            var sorted_arr = docEntryArray.slice().sort();
            var count = 0;
            var indexes = [];
            for (var i = 0; i < docEntryArray.length - 1; i++) {
                if (sorted_arr[i + 1] == sorted_arr[i]) {
                    //count.push(sorted_arr[i]);
                    indexes.push(i+1);
                    count++;
                }
            }
            if(count >= 1) {
                newUserTrackSidebar.goToPrevItem();
                preTally.Track.trackButtonsShowHide(1,0,1,1);
                dhtmlx.message({
                    title: "Process Repeating",
                    type:  "alert-warning",
                    text:  "Process <b style='color:red;'>"+indexes+"</b> Are Repeating!<br />Please ReCheck The Process Before Continue.",
                });
                return
            }

            var supDocsIDs = new Array();
            var totalAmount = 0;
            trackDocumentArray.forEach(function(docEntry) {
                supDocsIDs = docEntry['supDocIds'];
                totalAmount += parseFloat(docEntry['AJD_Amount']);
            });
            var supDocsIDsUnq = [];
            $.each(supDocsIDs, function(i, el){
                if($.inArray(el, supDocsIDsUnq) === -1)  supDocsIDsUnq.push(el);
            });

            trackPaymentDetailsDocuments.setItemValue('paymentDetailsSuppDocsValue', ': ' + supDocsIDsUnq.length + ' Nos:');
            trackPaymentDetailsDocuments.setItemValue('paymentDetailsDocsValue', ': ' + Object.keys(trackDocumentArray).length + ' Nos:');
            
            var paidAmount = 0;
            function isInteger(n) { return Number(n) === n && n % 1 === 0; }
            function isFloat(n) { return Number(n) === n && n % 1 !== 0; }
            
            
            jobPaymentDetails.forEachRow(function(rowId){
                if(isInteger(parseInt(jobPaymentDetails.cells(rowId,2).getValue())) || isFloat(parseFloat(jobPaymentDetails.cells(rowId,2).getValue()))) {
                    paidAmount += parseInt(jobPaymentDetails.cells(rowId,2).getValue());
                }
            });
            
            trackPaymentDetailsBilling.setItemValue('TPBTotalAmount',totalAmount);
            trackPaymentDetailsBilling.setItemValue('TPBPaidAmount',paidAmount);
            trackPaymentDetailsBilling.setItemValue('TPBBalanceAmount',(totalAmount - paidAmount));
            trackPaymentDetailsBilling.setItemValue('TPBBalanceAmountData','<b>'+ (totalAmount - paidAmount) +'</b>');
            
            if(paidAmount == 0) {
                trackPaymentDetailsBilling.hideItem('TPBPaidAmount');
            }

            var checked=[];
            ASD_Id = checked.toString();
            TAPFormSupport.setItemValue('ASD_Id', ASD_Id);
            if(ATPOptionSelected.getRowsNum() > 0 ) {
                var validate = true;
            } else {
                var validate = TAPFormSupport.validate();
            }
            trackToolbar.setItemText('saveNewTrackRegistration', trackPaymentDetailsBilling.getItemLabel('TPBProceed'));
            var result = JSON.stringify(preTally.Functions.convArrToObj(trackDocumentArray));
            if(validate && (Object.keys(trackDocumentArray).length > 0 || ATPOptionSelected.getRowsNum() > 0 )) {
                var params = TrackJobId != 0 ? '&t_flag=1' : '' ;
                $.ajax({
                    url : preTally.Initialize.encryptURL("warehouse/newAttestationCertificate.php"+params),//
                    type: 'POST',
                    data : {"DOC_Array" : result,"AJ_Id":TrackJobId,"ASD_Id":ASD_Id},
                    success: function(data) {
                        ReceiptsDetails = JSON.parse(data);
                        if(flag == 1) {
                            newUserTrackSidebar.goToNextItem();
                            preTally.Track.trackButtonsShowHide(1,1,0,1);
                        }  else if(flag == 2) {
                            newUserTrackSidebar.goToPrevItem();
                            preTally.Track.trackButtonsShowHide(1,0,1,0);
                        }  else {
                            newUserTrackSidebar.items(menuId).setActive();
                            if(menuId == "candidate_details")
                                preTally.Track.trackButtonsShowHide(1,0,1,0);
                            else
                                preTally.Track.trackButtonsShowHide(1,1,0,1);
                        }
                        
                        trackPaymentDetailsBilling.setItemValue('AJID', TrackJobId);
                
                        TrackJobStatus = trackPaymentDetailsBilling.getItemValue("Job_Status");
                        if(TrackJobStatus !== '0') {
                            //$('#TPDReceiptBLR').remove();
                            $('#TPDEstimateBLR').remove();
                            $('#TPDInvoiceBLR').remove();

                            //trackPaymentDetailsToolbar.enableItem("pcr");
                            trackPaymentDetailsToolbar.enableItem("per");
                            trackPaymentDetailsToolbar.enableItem("ptr");
                        }

                        var receivedDate    = trackCandidateDetails.getCalendar("AJ_ReceivedDate");
                        var formattedDate   = receivedDate.getFormatedDate("%d-%m-%Y");
                        var AJ_FName        = trackCandidateDetails.getItemValue('AJ_FName');
                        var AJ_HouseNo      = trackCandidateDetails.getItemValue('AJ_HouseNo');
                        var AJ_HouseName    = trackCandidateDetails.getItemValue('AJ_HouseName');
                        var AJ_Mobile1      = trackCandidateDetails.getItemValue('AJ_Mobile1');
                        var AJ_Email        = trackCandidateDetails.getItemValue('AJ_Email');
                        var AJ_Place        = trackCandidateDetails.getItemValue('GOGL_Location_1');
                        var AJ_City         = trackCandidateDetails.getItemValue('GOGL_City_1');
                        var AJ_State        = trackCandidateDetails.getItemValue('GOGL_State_1');

                        var today           = new Date();
                        var dd              = today.getDate();
                        var mm              = today.getMonth()+1; //January is 0!
                        var yyyy            = today.getFullYear();
                        
                        var candidateDateEstimate = candidateDetailsInvoice = invoiceDetails = candidateDetailsReceipt = candidateDetailsEstimate = "";
                        
                        //----------------------- For Document Receipt / Estimate -----------------------------
                        candidateDetailsEstimate += 'Sl. No.: <label id="DRID_RPT">'+ trackPaymentDetailsBilling.getItemValue('AJR_DRId') +'</label><br />Date: <strong id="DRID_DATE">'+formattedDate+'</strong> <br />'+				
                            'Received the following documents / payment from: <strong>'+AJ_FName+' , '+AJ_Place+'</strong><br /> '+
                            'Phone No : <strong>'+AJ_Mobile1+' </strong><br />Email : <strong>'+AJ_Email+' </strong>';

                        candidateDateEstimate = ReceiptsDetails[3];
                        $('#cDetailsEstimate').html(candidateDetailsEstimate);
                        
                        var documentReceiptRows = '';
                        var documentReceiptCount = 1;
                        var documentTotalAmount = 0;
            
            
                        String.prototype.replaceAll = function(search, replacement) {
                            var target = this;
                            return target.replace(new RegExp(search, 'g'), replacement);
                        };
            
                        ATPOptionSelected.forEachRow(function(rId){
                            documentTotalAmount += parseInt($.trim(ATPOptionSelected.cells(rId,5).getValue().replace("₹", "")));
                            var docDetails = ATPOptionSelected.cells(rId,1).getValue().split('<br>');
                            var docProcess =  docDetails[1]+', '+docDetails[2] + '<br/>' + ATPOptionSelected.cells(rId,2).getValue().replaceAll('<br>',', ').slice(0, -2);
                            documentReceiptRows += '<tr class="alt"><td class="center_align">' + ATPOptionSelected.cells(rId,0).getValue() + '</td><td>'+docProcess+'</td><td class="center_align">1</td><td>' + ATPOptionSelected.cells(rId,5).getValue() + '</td></tr>';
                        });
                        var documentInvoiceRows = documentReceiptRows;
                        documentReceiptRows += '<tr class="alt"><td></td></td><td class="borderleft-none">Total</td><td class="center_align">' + documentReceiptCount + '</td><td>₹ ' + documentTotalAmount + '</td></tr>';
                        $('#TPBEstimateTable tbody').html(documentReceiptRows);
                        
                        //----------------------- For Invoice Receipt -----------------------------
                        candidateDetailsInvoice += 'Invoice No: <label id="INID_RPT">'+ trackPaymentDetailsBilling.getItemValue('AJR_INId') +'</label><br />	'+				
                            'To : '+AJ_FName+','+AJ_Place+','+AJ_City+' <br /> '+					
                            'Phone No : '+AJ_Mobile1+' <br />'+					
                            'Email : '+AJ_Email;

                        $('#cDetailsInvoice').html(candidateDetailsInvoice);
                        $('#invoiceDate').html( trackPaymentDetailsBilling.getItemValue('AJR_DRId') +"<br />Date : "+formattedDate);

                        var subTot          = (documentTotalAmount/1.14).toFixed(2);   // 14% of service tax + sub total = total
                        var statutoryAmt    = (documentTotalAmount*0.4).toFixed(2);   // 40% of total
                        var serviceTax      = (subTot*0.14).toFixed(2);
           
                        documentInvoiceRows += '<tr class="alt"><td></td><td class="borderleft-none" colspan="2" style="text-align:right; padding-right:30px;">STATUTORY AMOUNT</td><td>₹ '+statutoryAmt+'</td></tr>'+
                                    '<tr class="alt"><td></td><td class="borderleft-none" colspan="2" style="text-align:right; padding-right:30px;">SUB TOTAL</td><td>₹ '+subTot+'</td></tr>'+
                                    '<tr class="alt"><td></td><td class="borderleft-none" colspan="2" style="text-align:right; padding-right:30px;">SERVICE TAX@14%</td><td>₹ '+serviceTax+'</td></tr>'+
                                    '<tr class="alt"><td></td><td class="borderleft-none" colspan="2" style="text-align:right; padding-right:30px;">TOTAL</td><td>₹ '+documentTotalAmount+'</td></tr>';

                        $('#invoiceData').html(documentInvoiceRows);
            
            
                        //----------------------- For Invoice Receipt -----------------------------
                        var candidateDateEstimate = candidateDetailsInvoice = invoiceDetails = candidateDetailsReceipt = candidateDetailsEstimate = "";
                        var CRID_RPT_Data = $('#CRID_RPT').html();
                        if( ! CRID_RPT_Data ) { CRID_RPT_Data = ''; }
                        candidateDetailsReceipt += 'Sl. No.: <span id="CRID_RPT">'+ CRID_RPT_Data +'</span><br /> Date: <strong>'+formattedDate+'</strong><br /> '+
                                        'Received the following amount from : <strong>'+AJ_FName+' , '+AJ_Place+'</strong><br /> '+
                                        'Phone No : <strong>'+AJ_Mobile1+'</strong>';
                        $('#cDetailsReceipt').html(candidateDetailsReceipt);

                        $('#pageReceiptTotalAmount').html(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount'));
                        $('#amountInRs').html(preTally.Functions.convert_number(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount'))+' ONLY');
                        //$('#pageReceiptAmount').html(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount'));
                        $('#DRCR_RPT').html(ReceiptsDetails[0]); 
                        
                        if(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount') == 0) {
                            trackPaymentDetailsBilling.disableItem('TPBProceed');
                            trackToolbar.disableItem('saveNewTrackRegistration');
                        } else {
                            trackPaymentDetailsBilling.enableItem('TPBProceed');
                            trackToolbar.enableItem('saveNewTrackRegistration');
                        }
                        
                    }
                });
            }  else {
                preTally.Track.trackButtonsShowHide(1,0,1,1);
                dhtmlx.message({
                    title: "Warning",
                    type:"alert-warning",
                    text: "You have to fill the certificate details and submit it before proceeding to next section.",
                    callback: function(id) {
                        newUserTrackSidebar.items("certificate_details").setActive();
                    }
                });
            }
            
            
        },
        
        selectATPOption : function (ATPID, forP) {
            
            if(forP == 'U') { // In Case Of Updates From Track Job Saving Section
                var ATPOptionRowNum = ATPOptionSelected.getRowsNum();
                TAPOptionCount = ATPOptionSelected.getRowId(ATPOptionRowNum-1);
                if(isNaN(TAPOptionCount)) TAPOptionCount = 0;
            }

            if(TAPOptionCount == 0) ATPOptionSelected.clearAll();

            var ATPAmount = (ATPOptions.cells(ATPID,4).getValue() == 1) ? ATPOptions.cells(ATPID,3).getValue() : ATPOptions.cells(ATPID,6).getValue();
                          
            TAPOptionCount++;
            //console.log('Added : '+TAPOptionCount);
            
            var ATPCertificateDetails = TAPFormSupport.getCombo("CN_Id").getComboText()+', '+TAPFormSupport.getCombo("AA_VisaType").getComboText()+'<br />'+
                                        TAPFormSupport.getCombo("ADOC_Id").getComboText()+', '+TAPFormSupport.getItemValue('AA_IssuedYear')+'<br />'+
                                        TAPFormSupport.getCombo("APS_Id").getComboText()+', '+TAPFormSupport.getCombo("AA_Issuing_CNId").getComboText();

            var ATPCertificateData = '['+TAPFormSupport.getCombo("CN_Id").getSelectedValue()+','+TAPFormSupport.getCombo("AA_VisaType").getSelectedValue()+','+
                                     TAPFormSupport.getCombo("ADOC_Id").getSelectedValue()+','+TAPFormSupport.getItemValue('AA_IssuedYear')+','+
                                     TAPFormSupport.getCombo("APS_Id").getSelectedValue()+','+TAPFormSupport.getCombo("AA_Issuing_CNId").getSelectedValue()+']';

            
            
            var toAddCFDetails = ATPOptions.cells(ATPID,1).getValue();
            var toAddSuppDocms = ATPOptions.cells(ATPID,2).getValue();
            
            var jHtmlData = '';
            var editor = jQuery("<p>").append(jQuery(toAddCFDetails));
            editor.find(".automateBlockListFiller").remove();
            editor.find(".automateBlockListNo").remove();
            editor.find(".automateBlockList").each(function() {
                jHtmlData += $(this).html()+'<br>';
            });
//            console.log(jHtmlData);
            toAddCFDetails = jHtmlData;

            jHtmlData = '';
            editor = jQuery("<p>").append(jQuery(toAddSuppDocms));
            editor.find(".automateBlockListFiller").remove();
            editor.find(".automateBlockListNo").remove();
            editor.find(".automateBlockList").each(function() {
                jHtmlData += $(this).html()+'<br>';
            });
//            console.log(jHtmlData);
            toAddSuppDocms = jHtmlData;
            ATPOptionSelected.addRow(TAPOptionCount,['1',ATPCertificateDetails,toAddCFDetails,toAddSuppDocms,ATPOptions.getUserData('','LC_Name')+'<br />'+ATPOptions.getUserData('','Date'),ATPAmount,'<img src="images/icon/cross.png" style="cursor:pointer;" class="UIToolTip" UITitle="Delete Job Process" onclick="javascript:preTally.Track.removeATPOption('+TAPOptionCount+',\''+forP+'\');" />','<img src="images/icon/info_18.png" style="cursor:pointer;" class="UIToolTip" UITitle="Information On Process" onclick="preTally.Track.showAutomateProcessComment(this,'+ ATPID +',\'listOptn\');" />','<img title="Click here to add comments" style="cursor: pointer;" class="UIToolTip" UITitle="Additional Comments On Process" src="images/icon/note_20.png" onclick="preTally.Track.addOptionComment(this,'+ TAPOptionCount +',\'listOptn\');" >','1']);

            ATPOptionSelected.setUserData(TAPOptionCount,"AA_Process",ATPOptions.getUserData(ATPID,'AA_Process'));
            ATPOptionSelected.setUserData(TAPOptionCount,"AA_SupportingDocuments",ATPOptions.getUserData(ATPID,'AA_SupportingDocuments'));
            ATPOptionSelected.setUserData(TAPOptionCount,"AA_Comments",ATPOptions.getUserData(ATPID,'AA_Comments'));
            ATPOptionSelected.setUserData(TAPOptionCount,"AA_CertificateDetails",ATPCertificateData);
            ATPOptionSelected.setUserData(TAPOptionCount,"LC_Id",ATPOptions.getUserData('','LC_Id'));
            ATPOptionSelected.setUserData(TAPOptionCount,"Date",ATPOptions.getUserData('','Date'));

            var optionCount = 1;
            ATPOptionSelected.forEachRow(function(id){
                ATPOptionSelected.cellById(id,0).setValue(optionCount);
                optionCount++;
            });  
            
            if(forP == 'U') { // In Case Of Updates From Track Job Saving Section

                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_Id",0);
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_Amount",$.trim(ATPAmount.replace("₹", "")));
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_Year",TAPFormSupport.getItemValue('AA_IssuedYear'));
                ATPOptionSelected.setUserData(TAPOptionCount,"ADOC_Id",TAPFormSupport.getCombo("ADOC_Id").getSelectedValue());
                ATPOptionSelected.setUserData(TAPOptionCount,"ST_Id",ATPOptions.getUserData(ATPID,'ST_Id'));
                ATPOptionSelected.setUserData(TAPOptionCount,"APS_Id",TAPFormSupport.getCombo("APS_Id").getSelectedValue());
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_VisitingCNId",TAPFormSupport.getCombo("CN_Id").getSelectedValue());
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_VisaType",TAPFormSupport.getCombo("AA_VisaType").getSelectedValue());
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_IssuingCNId",TAPFormSupport.getCombo("AA_Issuing_CNId").getSelectedValue());
                ATPOptionSelected.setUserData(TAPOptionCount,"AJD_UniqueNo",preTally.Functions.generateUniqueId());
                //ATPOptionSelected.setUserData(TAPOptionCount,"AA_Comments",0);
                ATPOptionSelected.setUserData(TAPOptionCount,"processIDs",ATPOptions.getUserData(ATPID,'AA_Process'));  
                ATPOptionSelected.setUserData(TAPOptionCount,"supDocIds",ATPOptions.getUserData(ATPID,'AA_SupportingDocuments'));  
                ATPOptionSelected.setUserData(TAPOptionCount,"AE_LastProcess",TAPFormSupport.getCombo("AE_LastProcess").getSelectedValue());
                
                preTally.Track.TrackRegistrationTabCertificate(TAPOptionCount);
            }
//            console.log(trackDocumentArray);
        },
        addOptionComment : function(inp,optionId,formName){
            if (!ATPOptionComment) {
                ATPOptionComment = new dhtmlXPopup({mode: "left"});
                ATPComment = ATPOptionComment.attachEditor(400, 250);
            }

            if (ATPOptionComment.isVisible()) {
                ATPOptionComment.hide();
            } 
            
            $('#'+ATPOptionComment._nodeId).find('.dhx_cell_editor').find('.dhx_cell_stb').append('<div class ="AJDComments"><a href="javascript:void(0);" style="float:right; margin:6px; 3px 15px 3px;" onclick="preTally.Track.ATPOptionCommentSave('+optionId+',\''+formName+'\');"><img src="images/icon/tick_16.png" /></a><a href="javascript:void(0);" style="float:right; margin:6px; 3px;" onclick="preTally.Track.ATPOptionCommentClose();"><img src="images/icon/cross.png" /></a></div>');
            
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight; 
            ATPOptionComment.show(x, y, w, h);
            
            ATPComment.setContent(ATPOptionSelected.getUserData(optionId,'AE_Comments'));
            
            ATPOptionComment.attachEvent("onHide", function(){
                $('.AJDComments').empty();
            });
            
        },
        ATPOptionCommentClose : function () {
            ATPOptionComment.hide();
        },
        ATPOptionCommentSave : function (optionId,formName) {
            var ATPCommentContent = ATPComment.getContent();
            ATPOptionSelected.setUserData(optionId,"AE_Comments",ATPCommentContent);

            if(formName == 'listDocs'){
                var docArrayCount = 0;
                trackDocumentArray.forEach(function(trackDocDetails) {
                    if(trackDocDetails['AJD_Id'] == ATPOptionSelected.getUserData(optionId,'AJD_Id'))
                        trackDocumentArray[docArrayCount]['AJD_Remarks'] = ATPCommentContent;
                    docArrayCount++;
                });
            }
            
            ATPOptionComment.hide();
        },
        removeATPOption : function (rowID, forP) { 
            //console.log('Removed : '+rowID);
            ATPOptionSelected.deleteRow(rowID);
            
            var optionCount = 1;
            ATPOptionSelected.forEachRow(function(id){
                ATPOptionSelected.cellById(id,0).setValue(optionCount);
                optionCount++;
            });
            
            if(forP == 'U') {
//                console.log('For Updations');
                trackDocumentArray = []; 
                ATPOptionSelected.forEachRow(function(rId){
                    preTally.Track.TrackRegistrationTabCertificate(rId);
                });
            }
//            console.log(trackDocumentArray);

        },
        saveCandidateDetails: function(menuId,flag) {
            preTally.Track.trackButtonsShowHide(1,0,1,0);
            if (trackCandidateDetails.validate()) {
                preTally.Settings.progressOn(true, dhxLayout, null);newUserTrackSidebar.items("candidate_details").setActive();
                
                trackCandidateDetails.send(preTally.Initialize.encryptURL("warehouse/newAttestationJob.php"), function(loader, response) {
                    if(TrackJobId == 0 && proceedFlag==0) trackToolbar.setItemText('cancelNewTrackRegistration', 'Delete');
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(response == 'AS_CityErr' || response == 'AJ_CityErr' || response == 'AD_CityErr') {
                        newUserTrackSidebar.items("candidate_details").setActive();
                        if(response == 'AS_CityErr') 
                            trackCandidateDetails.setValidateCss("GOGL_City_2",false,"validate_red");
                        else if(response == 'AJ_CityErr') 
                            trackCandidateDetails.setValidateCss("GOGL_City_1",false,"validate_red");
                        else if(response == 'AD_CityErr') 
                            trackCandidateDetails.setValidateCss("GOGL_City_3",false,"validate_red");
                    } else if(response != "fail") { 
                        //AJ_Id = response;
                        //trackCandidateDetails.setItemValue('AJ_Id', AJ_Id);
                        if(flag == 1) {
                            newUserTrackSidebar.goToNextItem();
                            preTally.Track.trackButtonsShowHide(1,0,1,1);
                        }  else {
                            if(menuId == 'payment_details') { // && typeof cFlag == "undefined"
                                if((TrackJobId=="0")&& (ATPOptionSelected.getRowsNum()=='0')){
                                        newUserTrackSidebar.items("candidate_details").setActive();
                                        dhtmlx.confirm({
                                            title: "Warning",
                                            type:"confirm",
                                            text: "You have to fill the certificate details before proceeding to billing section.",
                                            callback: function(result) {
//                                                if(result) {
                                                    newUserTrackSidebar.items("certificate_details").setActive();
                                                    preTally.Track.trackButtonsShowHide(1,0,1,1);
//                                                }  else 
//                                                    newUserTrackSidebar.items("candidate_details").setActive();
                                            }
                                        });
                                }else{
                                    preTally.Track.paymentReadOnlyForm(trackPaymentDetailsBilling.getItemValue('ABP_AmountRecieved_Tot'),trackPaymentDetailsBilling.getItemValue('TPBTotalAmount'));
                                    newUserTrackSidebar.items(menuId).setActive();
                                    preTally.Track.trackButtonsShowHide(1,1,0,1);
                                }
                            }   else if(menuId == 'certificate_details') {
                                 newUserTrackSidebar.items(menuId).setActive();
                                    preTally.Track.trackButtonsShowHide(1,0,1,1); 
                            }
                        }
                    }
                });
            }  else {
                newUserTrackSidebar.items("candidate_details").setActive();
                dhtmlx.message({
                    title: "Warning",
                    type:  "alert-warning",
                    text: "You have to fill the candidate details before proceeding to next section.",
                    callback: function(id) {
                        preTally.Track.trackButtonsShowHide(1,0,1,0);
                        newUserTrackSidebar.items("candidate_details").setActive();
                    }
                });
            }
        },
        savePaymentDetails : function(menuId,flag) { 
//            console.log('Payment Details Saved');
            if (trackPaymentDetailsBilling.validate() ) {
                var amtReceived = trackPaymentDetailsBilling.getItemValue('ABP_AmountRecieved_Tot');
                var totAmount = trackPaymentDetailsBilling.getItemValue('TPBTotalAmount');
                preTally.Track.paymentReadOnlyForm(amtReceived,totAmount);
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                var params = "Desc="+TAPFormSupport.getItemValue('AJ_FName');
                trackPaymentDetailsBilling.send(preTally.Initialize.encryptURL("warehouse/addAdditionalTrackJob.php&"+params), function(loader, response) {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    
                    if(response == 'amtErr') {
                        dhtmlx.message({text: "Please enter a valid amount"});
                        trackPaymentDetailsBilling.setValidateCss("TPBAdvanceAmount",false,"validate_red");
                        if(menuId == "payment_details") {
                            preTally.Track.trackButtonsShowHide(1,1,0,1);
                        }
                    }  else {
//                        console.log('Am Hereee');
                        var responseDetails = JSON.parse(response);
                        TrackJobSaved = 1;
                        
                        trackPaymentDetailsBilling.showItem('TPBPaidAmount');
                        trackPaymentDetailsBilling.setItemValue('TPBPaidAmount',(parseInt(trackPaymentDetailsBilling.getItemValue('TPBPaidAmount')) + parseInt(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount'))));
                        trackPaymentDetailsBilling.setItemValue('TPBBalanceAmount',(parseInt(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount')) - parseInt(trackPaymentDetailsBilling.getItemValue('TPBPaidAmount'))));
                        trackPaymentDetailsBilling.setItemValue('TPBBalanceAmountData',(parseInt(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount')) - parseInt(trackPaymentDetailsBilling.getItemValue('TPBPaidAmount'))));
                        
                        $('#CRID_RPT').html(responseDetails['AJR_CRId']);
                        $('#DRCR_RPT').html(responseDetails['AJR_DRId']);
                        $('#DRID_RPT').html(responseDetails['AJR_DRId']);
                        $('#INID_RPT').html(responseDetails['AJR_INId']);
                        $('#DRID_DATE').html(responseDetails['AJR_CDate']);
                        
                        $('#invoiceDate').html( responseDetails['AJR_DRId'] +"<br />Date : "+responseDetails['AJR_CDate']);
                        //TrackJobStatus
                        $('#TPDReceiptBLR').remove();
                        $('#TPDEstimateBLR').remove();
                        $('#TPDInvoiceBLR').remove();

                        trackPaymentDetailsToolbar.enableItem("pcr");
                        trackPaymentDetailsToolbar.enableItem("per");
                        trackPaymentDetailsToolbar.enableItem("ptr");
//                        console.log(TrackJobId+' -- '+TrackJobId);
                        jobPaymentDetails.clearAndLoad(preTally.Initialize.encryptURL("requisites/trackPaymentDetails.php&AJ_Id="+TrackJobId));
                        
                        trackPaymentDetailsBilling.setItemLabel('TPBProceed', 'Confirm Payment');
                        trackToolbar.setItemText('saveNewTrackRegistration', trackPaymentDetailsBilling.getItemLabel('TPBProceed'));
                        
                        if(trackPaymentDetailsBilling.getItemValue('TPBBalanceAmount') == 0) {
                            trackPaymentDetailsBilling.disableItem('TPBProceed');
                            trackToolbar.disableItem('saveNewTrackRegistration');
                        }
                        
                        dhtmlx.message({text: "Job Saved Successfully."});
                        dhtmlx.message({
                            title: "Job Status : Saved",
                            type:  "alert-warning",
                            text:  "Job Saved Successfully. <br />You Can Now Print The Receipts/Invoive.",
                        });
                    }
                });

            }
        },
        trackButtonsShowHide : function (c,s,n,p) {
            if(c === 0) trackToolbar.hideItem('cancelNewTrackRegistration'); else {
                trackToolbar.showItem('cancelNewTrackRegistration');
            }
            if(s === 0) trackToolbar.hideItem('saveNewTrackRegistration'); else trackToolbar.showItem('saveNewTrackRegistration');
            if(n === 0) trackToolbar.hideItem('nextNewTrackRegistration'); else trackToolbar.showItem('nextNewTrackRegistration');
            if(p === 0) trackToolbar.hideItem('previousNewTrackRegistration'); else trackToolbar.showItem('previousNewTrackRegistration');
        },
        showPDDInfo : function(obj,from) {
            String.prototype.replaceAll = function(search, replacement) {
                var target = this;
                return target.replace(new RegExp(search, 'g'), replacement);
            };
                
            if(from === 'DD') {
                var supDocsText = '';
                var supDocsTextCount = 1;

                ATPOptionSelected.forEachRow(function(entry){
//                    console.log(ATPOptionSelected.cells(entry,1).getValue());
                    supDocsText += supDocsTextCount + ' :&nbsp;&nbsp;' + ATPOptionSelected.cells(entry,1).getValue().replaceAll("<br>", "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;") + '<br />';
                    supDocsTextCount ++;
                });
                if(supDocsText == '') supDocsText = 'No Document Selected';
                paymentDetailsPopUp.attachHTML(supDocsText); 
                
            } else if(from === 'DS') {
                var supDocsText = new Array();
                var supDocsTextCount = 1;
                ATPOptionSelected.forEachRow(function(entry){
                    supDocsText = ATPOptionSelected.cells(entry,3).getValue().split("<br>");
                });
                supDocsText = supDocsText.filter(function(e){ return e.replace(/(\r\n|\n|\r)/gm,"")});
                var supDocsTextUnq = [];
                var supDocsTextData = '';
                var k = 1;
                $.each(supDocsText, function(i, el){
                    if($.inArray(el, supDocsTextUnq) === -1) {
                        supDocsTextUnq.push(el);
                        supDocsTextData += k+' : '+el+'<br>';
                        k++;
                    }
                });

                if(supDocsTextData == '') supDocsText = 'No Document Selected';
                paymentDetailsPopUp.attachHTML(supDocsTextData);
            } else if(from === 'DP') {
                //paymentDetailsPopUp.attachHTML("Attestation Process");
            }
            var x = getAbsoluteLeft(obj);
            var y = getAbsoluteTop(obj);
            var w = obj.offsetWidth;
            var h = obj.offsetHeight;
            
            paymentDetailsPopUp.show(x,y,w,h);
        },
        paymentReadOnlyForm : function(amtReceived,totAmount) {
            
            amtReceived = parseInt(amtReceived);
            totAmount   = parseInt(totAmount);
            TPBAccountCombo = trackPaymentDetailsBilling.getCombo("TPBAccount");
            remitModeCombo = trackPaymentDetailsBilling.getCombo("AB_RemitMode");
            trackPaymentDetailsBilling.setItemValue('ABP_AmountRecieved_Tot',amtReceived);
            if(trackPaymentDetailsBilling.getItemValue('TPBTotalAmount')) {
                trackPaymentDetailsBilling.enableItem('TPBDocReceipt');
                trackPaymentDetailsBilling.enableItem('TPBTax');
            }
            
            if((amtReceived == totAmount) || (amtReceived > totAmount)) { 
                var setItemArray = ["TPBAccount", "AB_RemitMode","TPBProceed"];
                preTally.Functions.disableItem(trackPaymentDetailsBilling, setItemArray);
                trackPaymentDetailsBilling.setReadonly("TPBAdvanceAmount",true);
                trackPaymentDetailsBilling.setReadonly("TPBPayeeAccount",true);
                trackPaymentDetailsBilling.setReadonly("TPBPayeeBank",true);
                trackPaymentDetailsBilling.setReadonly("",true);
            } else {
                var setItemArray = ["TPBAccount", "AB_RemitMode", "TPBProceed"];
                preTally.Functions.enableItem(trackPaymentDetailsBilling, setItemArray);
                trackPaymentDetailsBilling.setReadonly("TPBAdvanceAmount",false);
                trackPaymentDetailsBilling.setReadonly("TPBPayeeAccount",false);
                trackPaymentDetailsBilling.setReadonly("TPBPayeeBank",false);
                trackPaymentDetailsBilling.setReadonly("TPBPayeeChqDD",false);
                
            }
        },
        disableEnableToolBarItems : function(id) {
            if(id != 'nextNewTrackRegistration' && id != 'previousNewTrackRegistration') {
                trackToolbar.forEachItem(function(itemId){
                    trackToolbar.enableItem(itemId);
                });
            }
         // var notDisable = new Array('master_data', 'listings', 'documents', 'supporting_documents', 'authority', 'subprocess', 'mainprocess', 'cancelNewTrackRegistration', 'saveNewTrackRegistration', 'nextNewTrackRegistration', 'previousNewTrackRegistration', 'newPhoneRegistration','trackNotification','balSettings','trackAutomate', 'list_track_jobs', 'list_track_document', 'list_track_enquiry','ATPInstruction');
            var notDisable = new Array('master_data', 'listings', 'documents', 'supporting_documents', 'authority', 'subprocess', 'mainprocess', 'cancelNewTrackRegistration', 'saveNewTrackRegistration', 'nextNewTrackRegistration', 'previousNewTrackRegistration', 'newPhoneRegistration','trackNotification','balSettings','trackAutomate', 'list_track_jobs', 'list_track_document', 'list_track_enquiry','ATPInstruction');
            if($.inArray( id, notDisable ) == -1) {
                lastOpenedTab=id;
                trackToolbar.disableItem(id);
            }
        },
        downloadToPDF: function(file,TrackJobId,AJR_Id,type) {    // download to pdf in edit job section
            $.ajax({
                url : preTally.Initialize.encryptURL("warehouse/trackReceiptOnEdit.php"),
                type: 'POST',
                data : {"type" : type,"AJ_Id":TrackJobId, "AJR_Id" : AJR_Id},
                success: function(data) { 
                    if(type == 3) {
                        var jsonData = JSON.parse(data);
                        jsonData[11] = preTally.Functions.number2text(jsonData[8]);
                        data = JSON.stringify(jsonData);
                    }
                    $.ajax({
                        url : preTally.Initialize.encryptURL(file),
                        data : "data="+ data+"&type="+type
                    }).done(function(data) {
                        if(data != "fail")
                            window.open("uploads/trackReceipts/"+data,'Download');  
                        else
                            dhtmlx.message({text: "Some error has occured."}); 
                    });
                }
            });
        },
        filterJobForm: function(displayItem,value) {
            trackCandidateDetails.forEachItem(function(itemID) {
                if (trackCandidateDetails.getUserData(itemID) == displayItem) {
                    if(displayItem != 'deliveredTo') {  // dieliver details section
                        if(value == 1) {  // if self is selected
                            trackCandidateDetails.hideItem(itemID);
                            var setReqArray = ["AS_FName", "GOGL_Street_2", "GOGL_Place_2", "GOGL_Location_2","GOGL_City_2", "GOGL_State_2","GOGL_Country_2", "AS_Mobile1"];
                            preTally.Functions.setRequired(trackCandidateDetails, setReqArray, false);
                        } else {
                            trackCandidateDetails.showItem(itemID);
                            var setReqArray = ["AS_FName", "GOGL_Street_2", "GOGL_Place_2", "GOGL_Location_2","GOGL_City_2", "GOGL_State_2","GOGL_Country_2", "AS_Mobile1"];
                            preTally.Functions.setRequired(trackCandidateDetails, setReqArray, true);
                        }
                    } else {
                        if(value != 4) {  // if others is not selected in submitted by
                            trackCandidateDetails.hideItem(itemID);
                            var setReqArray = ["AD_FName", "GOGL_Street_3", "GOGL_Place_3", "GOGL_Location_3","GOGL_City_3", "GOGL_State_3","GOGL_Country_3", "AD_Mobile1"];
                            preTally.Functions.setRequired(trackCandidateDetails, setReqArray, false);
                        } else {
                            trackCandidateDetails.showItem(itemID);
                            var setReqArray = ["AD_FName", "GOGL_Street_3", "GOGL_Place_3", "GOGL_Location_3","GOGL_City_3", "GOGL_State_3","GOGL_Country_3", "AD_Mobile1"];
                            preTally.Functions.setRequired(trackCandidateDetails, setReqArray, true);
                        }
                    }
               }
            });
        },
        cancelNewJob: function(id,action) { 
            if(id == 'cancelNewTrackRegistration')
                type = 'delete';
            else
                type = 'incomplete';
            if(action=='edit') {
                //if( typeof trackCandidateDetails != 'undefined' )
                    //AJ_Id =  trackCandidateDetails.getItemValue("AJ_Id");
            }
            var params = "&id="+TrackJobId+"&type="+type;
            $.post(preTally.Initialize.encryptURL("warehouse/cancelJob.php"+params),function(data){
                if(action=='edit') {    
                    preTally.Reload.reInitialize('list_track_jobs','track_dashboard');
                }
            });
        },
        
        //----------------------- Listing All Jobs -------------------------//        
        ListTrackJobs: function() { //----------- ListTrackJobs function for List All jobs, Listing by Customer wise
           
            if(!trackConsoleSideBar.items("SB_list_track_jobs")) { //------ Check Jobs List Tab is alredy Open Or Not
                trackConsoleSideBar.addItem({ id:"SB_list_track_jobs"});
                trackConsoleSideBar.items("SB_list_track_jobs").setActive();
                
                TrckListTlbr = trackConsoleSideBar.cells("SB_list_track_jobs").attachToolbar();                
                TrckListTlbr.setIconsPath("images/icon/default_18/");
                
                var tb_data_txt =  "<div  style='float:left;font-weight:bold' class='tb_cnt_tot'># : 0</div>\
                                   <div style='float:right;'> <div id='jobList_paging' style='float:left;'></div><input type='button' className= 'button_export' name='export_Enquiry' value='Export' onClick='preTally.Track.exportTrackReport(\"job_list\",TrckListTlbr)' style='float:right; background-image: url(images/icon/default_18/excel.png);background-repeat: no-repeat; background-position: 0px 3px ;padding-left: 22px;height: 25px'></div>";
                trackConsoleSideBar.cells("SB_list_track_jobs").attachStatusBar({
                    text:   tb_data_txt,   
                    height: 28             
                });
                
                trackListJobGrid = trackConsoleSideBar.cells("SB_list_track_jobs").attachGrid();
                trackListJobGrid.enableTooltips("true,false,false,false,false,false,false,false,false,false,false,true");
                trackListJobGrid.init();  
                trackListJobGrid.setPagingWTMode(true,false,true,[15,30,50]);
                trackListJobGrid.enablePaging(true,50,5,"jobList_paging",true);
                trackListJobGrid.setPagingSkin("toolbar", "dhx_skyblue");
                preTally.Track.dateFiltterMenu(TrckListTlbr,"listTrackJobs",trackListJobGrid);
                var filtrInterval;
                trackListJobGrid.attachHeader("#rspan,#rspan,#rspan,<input type='text' class='textFilter_JOB' id ='itmTrkName' style='width: 90%;' placeholder='Name'>,<input type='text' class='textFilter_JOB' id ='itmTrkTrack' style='width: 90%;' placeholder='Track ID'>,<input type='text' class='textFilter_JOB' id ='itmTrkTtlAmount' style='width: 90%;' placeholder='Total Amount'>,#rspan,#rspan,<input type='text' class='textFilter_JOB' id ='itmTrkCrUS' style='width: 90%;' placeholder='Created User'>,<input type='text' class='textFilter_JOB' id ='itmTrkCrBrh' style='width: 90%;' placeholder='Created Branch'>,<select style='width:95%; font-size:8pt; font-family:Tahoma;' class = 'selectFilter_JOB' id='itmTrkSatus'><option value =''>All</option><option value ='2'>Deleted</option><option value ='0'>Incompleted Registration</option><option value ='1'>Completed Registration</option><option value ='3'>Job UnderProcess</option><option value ='4'>Job Completed</option><option value ='5'>Delivered</option></select>,#rspan");          
                var filterValue = '&f='+TrckListTlbr.getValue("srch_date_from")+'&t='+TrckListTlbr.getValue("srch_date_till");
                preTally.Settings.progressOn(true, dhxLayout, null);
                //----------- Load Customer wise data in to Grid
                trackListJobGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackJobs.php"+filterValue), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    $('.tb_cnt_tot').html("# : "+trackListJobGrid.getUserData("", "TL_Count")+" "); // Set Total Count Of Load Data in footer
                    //------- filter based on text box 
                    $( ".textFilter_JOB" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            preTally.Track.applyFilter(TrckListTlbr,"listTrackJobs",trackListJobGrid); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    }); 
                    //------- filter based on Dorp  box 
                    $( ".selectFilter_JOB" ).change(function() { //------- filter based on Select Box
                        preTally.Track.applyFilter(TrckListTlbr,"listTrackJobs",trackListJobGrid);
                    });
                    trackListJobGrid.attachEvent("onRowSelect", function(id,ind) { //-------Row selected in grid Expanted the row and show details of Job(Documnet,Main Process & sub-process)
                        if(ind == 11 ) return false;
                        
                        if (trackListJobGrid.doesRowExist(openTrackListID)) {
                            trackListJobGrid.cells(openTrackListID, 1).close();
                            trackListJobGrid.setRowTextStyle(openTrackListID, "padding-top: 3px;");
                        }
                        if (openTrackListID == id) {
                            openTrackListID = 1;
                        } else if(ind != 0) { 
                            trackListJobGrid.cells(id, 1).open();
                            trackListJobGrid.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                            openTrackListID = id;
                        }
                        trackListJobGrid.clearSelection();
                    });
                });
                trackListJobGrid.attachEvent("onFilterEnd", function(elements){//------ Show "record not Found" Message in Empty Grid or no row exist in Grid
                    preTally.Functions.recordNotFound(trackListJobGrid,11);
                });
            } else { //------ Check Jobs List Tab is alredy Open Tab become active state
                trackConsoleSideBar.items("SB_list_track_jobs").setActive();
            }
        },
        dateFiltterMenu : function(ToolbrDateMenu,page,grid) { //--- function for attach Date filter menu
            var Days_Options = [];                
            var Months_Options = [];
            var Years_Options = [];
            var monthNames = [ 
                "January", "February", "March","April", "May", "June",
                "July", "August", "September","October", "November", "December"
            ];                    
            var todayDt = Date.today().getDate();  
            for (i = todayDt; i >=1 ; i--) {
                if(i<10){
                    i='0'+i;
                }    
                if(i==todayDt){
                    Days_Options.push([i,'obj','Today',"calendar_D.png"]);
                }else{
                    Days_Options.push([i,'obj',i,"calendar_D.png"]);
                }                    
            }
            //Date.today().getMonth()+1
            for (i = 12; i >=1 ; i--) {
                j = i;    
                if(j<10){
                    j='0'+i;
                } 
                    Months_Options.push(['m'+j,'obj',monthNames[i-1],"calendar_M.png"]);
            } 
            for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                Years_Options.push([i,'obj',i,"calendar_Y.png"]);
            }
            ToolbrDateMenu.addButtonSelect( "srch_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
            ToolbrDateMenu.addButtonSelect( "srch_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
            ToolbrDateMenu.addButtonSelect( "srch_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select'); 
            ToolbrDateMenu.addSeparator();
            ToolbrDateMenu.addText("text_from", null, "From");
            ToolbrDateMenu.addInput("srch_date_from", null, "", 75);
            ToolbrDateMenu.addButton("srch_df_clear", null, "", "close.gif");
            ToolbrDateMenu.addSeparator();
            ToolbrDateMenu.addText("text_till", null, "Till");
            ToolbrDateMenu.addInput("srch_date_till", null, "", 75);
            ToolbrDateMenu.addButton("srch_dt_clear", null, "", "close.gif");
            ToolbrDateMenu.addSeparator();

            ToolbrDateMenu.addButton("srch_date_filter", null, "Search", "save.gif");
            ToolbrDateMenu.setIconsPath("images/icon/default_18/");
            ToolbrDateMenu.addSeparator();
            
            //ToolbrDateMenu.addButton("job_details_export", null, "Export", "excel.png");
            ToolbrDateMenu.setIconsPath("images/icon/");
            ToolbrDateMenu.addSeparator();
            
            ToolbrDateMenu.addButton("reload_btn", null, "Reload", "refresh-icon.png");
            ToolbrDateMenu.setIconsPath("images/icon/default_18/");
            ToolbrDateMenu.addSeparator();
            var TrackSrch_Inp_Frm = ToolbrDateMenu.getInput("srch_date_from");
            TrackSrch_Inp_Frm.setAttribute("readOnly", "true");
            TrackSrch_Inp_Frm.onclick = function() {
                if(ToolbrDateMenu.getValue("srch_date_from")) preTally.Functions.setSens(trackSrch_Calendar,TrackSrch_Inp_Til, "max");
            }
            var TrackSrch_Inp_Til = ToolbrDateMenu.getInput("srch_date_till");
            TrackSrch_Inp_Til.setAttribute("readOnly", "true");
            TrackSrch_Inp_Til.onclick = function() {
                if(ToolbrDateMenu.getValue("srch_date_till")) preTally.Functions.setSens(trackSrch_Calendar,TrackSrch_Inp_Frm, "min");
            }
            var date = new Date();
            var month = date.getMonth();
            var year = date.getFullYear();
            var tillDate = Date.today().toString("dd.MM.yyyy"); 
            var fromDate = new Date(year,month,1).toString("dd.MM.yyyy");
            ToolbrDateMenu.setValue('srch_date_till', tillDate);
            ToolbrDateMenu.setValue('srch_date_from', fromDate);
            ToolbrDateMenu.setItemText('srch_month_filter',monthNames[month]);
            ToolbrDateMenu.setItemImage('srch_month_filter', 'calendar_M.png');   
            
            ToolbrDateMenu.attachEvent("onClick", function(id){
                var pId = ToolbrDateMenu.getParentId(id);
                if(id == 'srch_df_clear')
                    ToolbrDateMenu.setValue('srch_date_from', '', false);
                else if(id == 'srch_dt_clear')
                    ToolbrDateMenu.setValue('srch_date_till', '', false);
                else if(pId == 'srch_day_filter') {
                    var dateToday = id+"."+Date.today().toString("MM.yyyy");
                    ToolbrDateMenu.setValue('srch_date_from', dateToday, false);
                    ToolbrDateMenu.setValue('srch_date_till', dateToday, false);
                    ToolbrDateMenu.setItemText('srch_month_filter', 'Select Month');
                    ToolbrDateMenu.setItemText('srch_year_filter', 'Select Year');
                    ToolbrDateMenu.clearItemImage('srch_month_filter');
                    ToolbrDateMenu.clearItemImage('srch_year_filter');
                } else if(pId == 'srch_month_filter') {
                    id = id.substr(1);
                    var tmpDate     = new Date();
                    var yeartoolbar = parseInt(ToolbrDateMenu.getItemText('srch_year_filter')); //01-01-2026
                    yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                    var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                    var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                    ToolbrDateMenu.setValue('srch_date_from', firstDay, false);
                    ToolbrDateMenu.setValue('srch_date_till', lastDay, false);
                    ToolbrDateMenu.setItemText('srch_day_filter', 'Select Day');
                    //ToolbrDateMenu.setItemText('srch_year_filter', 'Select Year');
                    ToolbrDateMenu.clearItemImage('srch_day_filter');
                    //ToolbrDateMenu.clearItemImage('srch_year_filter');
                }   else if(pId == 'srch_year_filter') {
                    var tmpDate    = new Date();
                    var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                    var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                    
                    ToolbrDateMenu.setValue('srch_date_from', firstDay, false);
                    ToolbrDateMenu.setValue('srch_date_till', lastDay, false);
                    ToolbrDateMenu.setItemText('srch_day_filter', 'Select Day');
                    ToolbrDateMenu.setItemText('srch_month_filter', 'Select Month');
                    ToolbrDateMenu.clearItemImage('srch_day_filter');
                    ToolbrDateMenu.clearItemImage('srch_month_filter');
                }  else if(id == 'srch_date_filter'){
                    ToolbrDateMenu.setItemText('srch_day_filter', 'Select Day');
                    ToolbrDateMenu.setItemText('srch_month_filter', 'Select Month');
                    ToolbrDateMenu.setItemText('srch_year_filter', 'Select Year');
                    ToolbrDateMenu.clearItemImage('srch_day_filter');
                    ToolbrDateMenu.clearItemImage('srch_month_filter');
                    ToolbrDateMenu.clearItemImage('srch_year_filter');
                }   else if(id == 'job_details_export'){
                    preTally.Track.exportTrackReport("job_details");
                }
                if(id != 'srch_df_clear' && id != 'srch_dt_clear' && id != 'job_details_export'){
                    preTally.Track.applyFilter(ToolbrDateMenu,page,grid);
                }
            });
                
            //----init calendar;
            trackSrch_Calendar = new dhtmlXCalendarObject([TrackSrch_Inp_Frm, TrackSrch_Inp_Til]);
            trackSrch_Calendar.setDateFormat("%d.%m.%Y");

            ToolbrDateMenu.setAlign('right');
        }, 
        exportTrackReport : function(report,Toolbar){
           
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = Toolbar.getValue("srch_date_from");
            exportfilterValue['To_Date']   = Toolbar.getValue("srch_date_till");

            preTally.Settings.progressOn(true, dhxLayout, null);
            
            $.post(
                preTally.Initialize.encryptURL('warehouse/TrackExcelExport.php&type='+report),
                { filter : exportfilterValue },
                function(data) {
                    fileName = data.split("XL_");
                    if(fileName[1]) {
                         document.location ="uploads/excelFile/"+fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
            });
            
        },
        applyFilter:  function(Tlbr,param,ListGrid) { //----- applyFilter function Used for data filttering in grid (server Filtering)
            var filterValue = ''; 
            if(Tlbr!='NonDate') {
                filterValue = '&f='+Tlbr.getValue("srch_date_from")+'&t='+Tlbr.getValue("srch_date_till");
            }
            if(param == "listTrackJobs") {
                filterValue += '&filter='+ new Array($('#itmTrkName').val()+"--"+$('#itmTrkTrack').val()+"--"+$('#itmTrkTtlAmount').val()+"--"+$('#itmTrkCrUS').val()+"--"+$('#itmTrkCrBrh').val()+"--"+$( "#itmTrkSatus" ).val());
            } else if(param == "listTrackDocument") {
                if(trackListDocGrid.getCheckedRows(0) != '' ){
                    if(checkedDoc!=''){
                         checkedDoc  = checkedDoc+","+trackListDocGrid.getCheckedRows(0); 
                    }else{
                        checkedDoc  = trackListDocGrid.getCheckedRows(0);
                    }
                }
                
                var checkedDocArray = checkedDoc.split(',');
                var uniqueDocIds = [];
                $.each(checkedDocArray, function(i, el){
                    if($.inArray(el, uniqueDocIds) === -1) uniqueDocIds.push(el);
                });
             
                filterValue += '&filter='+ new Array($('#itmDoc').val()+"--"+$('#itmDocCusName').val()+"--"+$('#itmDocTrackId').val()+"--"+$('#itmDocCreateUser').val()+"--"+$( "#itmDocCreateBranch" ).val()+"--"+$( "#itmDocStatus" ).val()+"--"+$( "#itmDocNxtProcess" ).val()); 
                filterValue += '&GPIds='+uniqueDocIds;
                
            } else if(param == "listTrackDocGroup") {
                filterValue += '&filter='+ new Array($('#itmGpDoc').val()+"--"+$('#itmGpStatus').val()); 
            } else if(param == "listTrackEnquiry") { 
                filterValue += '&filter='+ new Array($('#itmEnqName').val()+"--"+$('#itmLastUser').val()+"--"+$('#itmLastBranch').val()+"--"+$('#itmFolupStatus').val()+"--"+$('#itmEnqCUser').val()+"--"+$('#itmEnqCBranch').val()+"--"+$('#itmEnqStatus').val()); 
            } else if(param == 'listTrackDocNotifi') {
                filterValue += '&filter='+ new Array($('#itmNotifiDoc').val()+"--"+$('#itmNotifiCusName').val()+"--"+$('#itmNotifiTrackId').val()+"--"+$('#itmNotifiCreateUser').val()+"--"+$( "#itmNotifiStatus" ).val()+"--"+AJNBId);  
            } else if(param == 'trackAllNotifications'){
                filterValue += '&filter='+ new Array($( ".track_notf_SFF" ).val());
            } else if(param == 'listAttestationSubProcess'){
                filterValue += '&filter='+ new Array($('#subProsName').val()+"--"+$('#mainProsName').val()+"--"+$('#subProcStatus').val());
            }

            if(param == 'listTrackDocNotifi') preTally.Settings.progressOn(true, NotifiWin, null);
            else preTally.Settings.progressOn(true, dhxLayout, null);
            ListGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/"+param+".php"+filterValue), function() { 
                if(param == "listTrackJobs") {
                    $('.tb_cnt_tot').html("# : "+ListGrid.getUserData("", "TL_Count")+" ");
                } else if(param == "listTrackDocument") {
                    $('.tb_cnt_tot_doc').html("# : "+ListGrid.getUserData("", "TL_Count")+" ");
                } else if(param == "listTrackEnquiry") {
                    $('.tb_cnt_tot_enqy').html("# : "+ListGrid.getUserData("", "TL_Count")+" ");
                }else if(param == "listTrackDocGroup") {
                    $('.tb_cnt_gp_tot').html("# : "+ListGrid.getUserData("", "TL_Gp_Count")+" ");
                }else if(param == 'trackAllNotifications'){
                    $('.status_cnt_totl').html("# : "+trackNotfGrid.getUserData("", "TL_Count")+" ");
                }else if(param == 'listAttestationSubProcess'){
                    $('.tb_subproc_cnt_tot').html("# : "+ListGrid.getUserData("", "TL_Count")+" ");
                }
                if(param == 'listTrackDocNotifi') preTally.Settings.progressOff(true, NotifiWin, null);
                else preTally.Settings.progressOff(true, dhxLayout, null);
//                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        //--------------------Managing Master Data Details -------------------------//          

        //--------------------Managing Document (Add/Edit) -------------------------//    
        attestationDocuments : function() {
            if(!trackConsoleSideBar.items("SB_documents")) {
                trackConsoleSideBar.addItem({ id:"SB_documents"});
                trackConsoleSideBar.items("SB_documents").setActive();
                
                var documentLayout = trackConsoleSideBar.cells('SB_documents').attachLayout("2U");
                documentLayout.cells("a").setText("New Document");
                documentLayout.cells("b").setText("List Documents");
                documentLayout.cells("a").setWidth(500);
                documentGrid = documentLayout.cells("b").attachGrid();
                documentGrid.attachHeader(",#text_filter,#select_filter,,<select id='DocStatusSelect'></select>");
                documentGrid.enableTooltips("false,false,false,false");
                documentGrid.enableColSpan(true);
                
                documentGrid.init();
                documentGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttestationDocuments.php"), function() {
                    documentGrid.makeFilter("DocStatusSelect",3);
                    documentGrid.attachEvent("onRowSelect",preTally.Track.editDocument);                   
                });

                documentGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(documentGrid,4);
                });

                addDocumentForm = documentLayout.cells("a").attachForm();
                preTally.Settings.progressOn(true, dhxLayout, null);
                addDocumentForm.loadStruct(preTally.Initialize.encryptURL("requisites/newAttestationDocument.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    addDocumentForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'newDocumentValidate') {
                            if (addDocumentForm.validate()) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addDocumentForm.send(preTally.Initialize.encryptURL('warehouse/newAttestationDocument.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addDocumentForm.resetValidateCss();
                                        addDocumentForm.clear();
                                        addDocumentForm.setItemValue("ADOC_Id", 0);
                                    } else 
                                        response = 'Some error has occured.';
                                    dhtmlx.message({text: response});
                                    documentGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttestationDocuments.php"), true, true, function() {
                                        documentGrid.filterByAll();
                                    });
                                });
                            }
                        } else { 
                            addDocumentForm.resetValidateCss();
                            addDocumentForm.clear();
                            addDocumentForm.setItemValue("ADOC_Id", 0);
                        }
                    });
                });
            } else 
                trackConsoleSideBar.items("SB_documents").setActive();
        },
        editDocument: function(rowId) {
            var DocId = rowId;
            addDocumentForm.setItemValue("ADOC_Id", DocId);
            addDocumentForm.setItemValue("ADOC_Document", documentGrid.getUserData(DocId, "UData_ADOC_Document"));
            addDocumentForm.setItemValue("ADOC_Type", documentGrid.getUserData(DocId, "UData_ADOC_Type"));
            addDocumentForm.setItemValue("ADOC_Status", documentGrid.getUserData(DocId, "UData_ADOC_Status"));
        },
        //-------------  Managing Supporting Document (Add/Edit) -------------------//         
        attestationSupportingDocuments: function() {
            if(!trackConsoleSideBar.items("SB_supporting_documents")) {
                trackConsoleSideBar.addItem({ id:"SB_supporting_documents"});
                trackConsoleSideBar.items("SB_supporting_documents").setActive();
                
                var supportingDocumentLayout = trackConsoleSideBar.cells('SB_supporting_documents').attachLayout("2U");
                supportingDocumentLayout.cells("a").setText("New Supporting Document");
                supportingDocumentLayout.cells("b").setText("List Supporting Documents");
                supportingDocumentLayout.cells("a").setWidth(500);
                supportingDocumentGrid = supportingDocumentLayout.cells("b").attachGrid();
                supportingDocumentGrid.attachHeader(",#text_filter,,<select id='SprtDocStatusSelect'></select>");
                supportingDocumentGrid.enableTooltips("false,false,false,false");
                supportingDocumentGrid.init();
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                supportingDocumentGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttestationSupportDocs.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    supportingDocumentGrid.makeFilter("SprtDocStatusSelect",2);
                    supportingDocumentGrid.attachEvent("onRowSelect",preTally.Track.editSupportingDocument);                   
                });

                supportingDocumentGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(supportingDocumentGrid,3);
                });

                addSupportingDocumentForm = supportingDocumentLayout.cells("a").attachForm();
                preTally.Settings.progressOn(true, dhxLayout, null);
                addSupportingDocumentForm.loadStruct(preTally.Initialize.encryptURL("requisites/newAttestationSupportDoc.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    addSupportingDocumentForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'newDocumentValidate') {
                            if (addSupportingDocumentForm.validate()) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addSupportingDocumentForm.send(preTally.Initialize.encryptURL('warehouse/newAttestationSupportDoc.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addSupportingDocumentForm.resetValidateCss();
                                        addSupportingDocumentForm.clear();
                                        addSupportingDocumentForm.setItemValue("ASD_Id", 0);
                                    } else 
                                        response = 'Some error has occured.';
                                    dhtmlx.message({text: response});
                                    supportingDocumentGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttestationSupportDocs.php"), true, true, function() {
                                        supportingDocumentGrid.filterByAll();
                                    });
                                });
                            }
                        } else { 
                            addSupportingDocumentForm.resetValidateCss();
                            addSupportingDocumentForm.clear();
                            addSupportingDocumentForm.setItemValue("ASD_Id", 0);
                        }
                    });
                });
                } else 
                trackConsoleSideBar.items("SB_supporting_documents").setActive();
        },
        editSupportingDocument: function(rowId){
            var DocId = rowId;
            addSupportingDocumentForm.setItemValue("ASD_Id", DocId);
            addSupportingDocumentForm.setItemValue("ASD_Document", supportingDocumentGrid.getUserData(DocId, "UData_ASD_Document"));
            addSupportingDocumentForm.setItemValue("ASD_Status", supportingDocumentGrid.getUserData(DocId, "UData_ASD_Status"));
        },

        //------------------  Managing Sub Process (Add/Edit) ------------------------//         
        subProcess: function() {
            var filtrSPInterval;
                if(!trackConsoleSideBar.items("SB_subprocess")) {
                trackConsoleSideBar.addItem({ id:"SB_subprocess"});
                trackConsoleSideBar.items("SB_subprocess").setActive();
                
                subProcessLayout = trackConsoleSideBar.cells('SB_subprocess').attachLayout("2U");
                subProcessLayout.cells("a").setText("New Sub Process");
                subProcessLayout.cells("b").setText("List Sub Process");
                subProcessLayout.cells("a").setWidth(450);
                subProcessGrid = subProcessLayout.cells("b").attachGrid();
//                subProcessGrid.attachHeader(",#text_filter,#select_filter,,,,,<select id='SubProcessStatusSelect'></select>");
                subProcessGrid.attachHeader(",<input type='text' class='textFilter_SUB' id ='subProsName' style='width: 90%;' placeholder='Subprocess'>,<div id ='mainProsName'></div>,,,,,<select style='width:95%; font-size:8pt; font-family:Tahoma;' class = 'selectFilter_SUB' id='subProcStatus'><option value =''>All</option><option value ='0'>Blocked</option><option value ='1'>Published</option></select>,");
                subProcessGrid.enableTooltips("false,false,false,false,false,false,false,false");
                subProcessGrid.init();
                
                var tb_data_txt =  "<div  style='float:left;font-weight:bold' class='tb_subproc_cnt_tot'># : 0</div>\
                                    <div style='float:right;' id='subProcessList_paging'></div>";
                subProcessLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   
                    height: 28             
                });
                
                
                                
                subProcessGrid.setPagingWTMode(true,false,true,[15,30,50]);
                subProcessGrid.enablePaging(true,50,5,"subProcessList_paging",true);
                subProcessGrid.setPagingSkin("toolbar", "dhx_skyblue");
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                subProcessGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttestationSubProcess.php"), function() {
                    $('.tb_subproc_cnt_tot').html("# : "+subProcessGrid.getUserData("", "TL_Count")+" ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    subProcessGrid.makeFilter("SubProcessStatusSelect",6);
                    subProcessGrid.attachEvent("onRowSelect",function(id,ind){
                        if(ind != 8) preTally.Track.newSubProcess(id); 
                        
                    }); 
                    
                    //------- filter based on combo box 
                    var trackAPMCombo = new dhtmlXCombo("mainProsName");
                    trackAPMCombo.load(preTally.Initialize.encryptURL("requisites/attestationMainProcess.php"), function(){
                        trackAPMCombo.setPlaceholder('Main Process ');
                        trackAPMCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                    r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                    r = true;
                            }
                            return r;
                        });
                    });
                    trackAPMCombo.setOptionWidth(250);
                    trackAPMCombo.attachEvent("onChange", function() {
                        var apmComboVal = trackAPMCombo.getSelectedValue();
                        if(!trackAPMCombo.getSelectedValue() && trackAPMCombo.getComboText()) apmComboVal = trackAPMCombo.getComboText();
                        $( "#mainProsName" ).val(apmComboVal);
                        preTally.Track.applyFilter('NonDate',"listAttestationSubProcess",subProcessGrid);
                    });
                    
                    //------- filter based on text box 
                    $( ".textFilter_SUB" ).keyup(function() {
                        if(filtrSPInterval) clearInterval(filtrSPInterval);
                        filtrSPInterval = setInterval( function() { 
                            preTally.Track.applyFilter('NonDate',"listAttestationSubProcess",subProcessGrid);
                            clearInterval(filtrSPInterval); 
                        }, 500);
                    }); 
                    //------- filter based on Dorp  box 
                    $( ".selectFilter_SUB" ).change(function() { //------- filter based on Select Box
                        preTally.Track.applyFilter('NonDate',"listAttestationSubProcess",subProcessGrid);
                    });
                });
                preTally.Track.newSubProcess();

                subProcessGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(subProcessGrid,4);
                });
            } else {
                APS_APM_Combo.load(preTally.Initialize.encryptURL("requisites/attestationMainProcess.php"),function(){
                   APS_APM_Combo.setComboValue(addSubProcessForm.getItemValue("APM_Id")); 
                });
                trackConsoleSideBar.items("SB_subprocess").setActive();
            }
        },
        newSubProcess : function(rowId){
            ASDOptionArray = [];
            addSubProcessForm = subProcessLayout.cells("a").attachForm();
            preTally.Settings.progressOn(true, dhxLayout, null);
            addSubProcessForm.loadStruct(preTally.Initialize.encryptURL("requisites/newAttestationSubProcess.php&APS_ID="+rowId), function() {
                addSubProcessForm.hideItem("CN_Id_Travelling");
                preTally.GoogleAddress.googleAddress(addSubProcessForm);
                preTally.Settings.progressOff(true, dhxLayout, null);
                
                APS_APM_Combo = addSubProcessForm.getCombo("APS_APM_Id");
                APS_APM_Combo.enableFilteringMode(true);
                APS_APM_Combo.load(preTally.Initialize.encryptURL("requisites/attestationMainProcess.php"),function(){
                   APS_APM_Combo.setComboValue(addSubProcessForm.getItemValue("APM_Id")); 
                });
                APS_APM_Combo.attachEvent("onChange",function(){
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getAPM_AliasName.php&APMId="+APS_APM_Combo.getSelectedValue())
                    }).done(function(data) { 
                        var mainProsDetails = $.parseJSON( data );
                        if(mainProsDetails[0] != '' && mainProsDetails[0] != null)
                            addSubProcessForm.setItemLabel('APS_Title', mainProsDetails[0]+" Process");
                        else
                            addSubProcessForm.setItemLabel('APS_Title', 'Sub Process');
                        
                        if(mainProsDetails[1] == 4){ 
                            addSubProcessForm.showItem("CN_Id_Travelling");
                            CNId_Travelling_Combo = addSubProcessForm.getCombo("CN_Id_Travelling");
                            CNId_Travelling_Combo.enableFilteringMode(true);
                            CNId_Travelling_Combo.setComboValue(addSubProcessForm.getItemValue("CNID_Travelling")); 
                        }else{
                            addSubProcessForm.hideItem("CN_Id_Travelling");
                        }
                    });
                });
                
                APS_ASD_Combo = addSubProcessForm.getCombo("ASD_Id");
                APS_ASD_Combo.setOptionWidth(350);
                APS_ASD_Combo.enableFilteringMode(true);
                
                $.post(preTally.Initialize.encryptURL("requisites/attestationSupDocs.php"),
                    {ASD_Id : addSubProcessForm.getItemValue("ASDIds") }
                ).done(function(data) {
                    var listSupportingDocCombo = data;

                    APS_ASD_Combo.load(listSupportingDocCombo, function(){
                        APS_ASD_Combo.deleteOption(0);
                        APS_ASD_Combo.setComboText('');

                        setTimeout(function(){ 
                            APS_ASD_Combo.setComboText(APS_ASD_Combo.getChecked().length + " Items Selected");
                        }, 200);
                    });
                });

                
                addSubProcessForm.attachEvent("onChange", function(name,value) { 
                    if(name == 'ASD_Id' && value != null){
                        var supDocArray = value.split(','); 
                        ASDOption = APS_ASD_Combo.getOption(value);

                        var state = APS_ASD_Combo.isChecked(APS_ASD_Combo.getIndexByValue(value)) ? false : true ;
                        if(supDocArray.length > 1){
                            supDocArray.forEach(function(AUTId) { 
                                APS_ASD_Combo.setChecked(APS_ASD_Combo.getIndexByValue(AUTId), state);
                            });
                            if($.inArray(value, ASDOptionArray) === -1 )ASDOptionArray.push(value)
                        }else{
                            ASDOptionArray.forEach(function(ASDOption) { 
                                if(ASDOption.indexOf(value) != -1) { 
                                    APS_ASD_Combo.setChecked(APS_ASD_Combo.getIndexByValue(ASDOption), false);
                                    ASDOptionArray.splice(ASDOptionArray.indexOf(ASDOption), 1);
                                }
                            });
                        }
                    }

                    if(['ASD_Id'].indexOf(name) > -1 ){
                        var checkedFlag = APS_ASD_Combo.isChecked(APS_ASD_Combo.getIndexByValue(value)) ? false : true ;
                        if(checkedFlag == false) APS_ASD_Combo.setComboText("");
                        APS_ASD_Combo.setChecked(APS_ASD_Combo.getIndexByValue(value), checkedFlag);
                        APS_ASD_Combo.openSelect();

                        APS_ASD_Combo.setComboText(APS_ASD_Combo.getChecked().length + " Items Selected");
                        APS_ASD_Combo.openSelect();
                    }
                });
                
                APS_ASD_Combo.attachEvent("onCheck", function(value, state){ 
                    setTimeout(function(){ 
                        preTally.Functions.setComboText(APS_ASD_Combo);
                    }, 200);
                    return true;
                });
    
                addSubProcessForm.attachEvent("onButtonClick", function(name) {
                    if (name == 'newSubProcessValidate') {
                        if (addSubProcessForm.validate()) {
                            if(!addSubProcessForm.isItemHidden('CN_Id_Travelling')) {
                                addSubProcessForm.setItemValue("CN_Name_Travelling",CNId_Travelling_Combo.getComboText());
                            }
                            
                            addSubProcessForm.setItemValue("ASDIds",APS_ASD_Combo.getChecked());
                            addSubProcessForm.setItemValue("APM_Title",APS_APM_Combo.getComboText());
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            addSubProcessForm.send(preTally.Initialize.encryptURL('warehouse/newAttestationSubProcess.php'), function(loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if (response != 'fail' && response != 'invalid' && response != 'country' && response != 'exists' && response != 'date'  && response != 'invaliddate') {
                                    addSubProcessForm.resetValidateCss();
                                    addSubProcessForm.clear();
//                                    APS_APM_Combo.clearAll();
                                    APS_APM_Combo.setComboText("");
                                    APS_APM_Combo.setComboValue("");
                                    preTally.Functions.clearComboCheckedValues(APS_ASD_Combo);
                                    
                                    subProcessGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttestationSubProcess.php"), true, true, function() {
                                        $('.tb_subproc_cnt_tot').html("# : "+subProcessGrid.getUserData("", "TL_Count")+" ");
                                        subProcessGrid.filterByAll();
        //                                APS_APM_Combo.clearAll();
                                        APS_APM_Combo.setComboText("");
                                        APS_APM_Combo.setComboValue("");
                                    });
                                    
                                } else if( response == 'invalid'){
                                    response = 'Invalid Main Process';
                                }else if( response == 'country'){
                                    response = 'Invalid Travelling country';
                                } else if( response == 'exists'){
                                    response = 'Sub Process already exists';
                                } else if( response == 'date'){
                                    response = 'Back date range already set.Please verify and Re-Try';
                                }else if( response == 'invaliddate'){
                                    response = 'Please Fill Back Date';
                                } else
                                    response = 'Some error has occured.';

                                dhtmlx.message({text: response});
                            });
                        }
                    } else { 
                        addSubProcessForm.resetValidateCss();
                        addSubProcessForm.clear();
                        preTally.Functions.clearComboCheckedValues(APS_ASD_Combo);
                        addSubProcessForm.setItemValue("APS_Id", 0);
//                        APS_APM_Combo.clearAll();
                        APS_APM_Combo.setComboText("");
                        APS_APM_Combo.setComboValue("");
                    }
                });
            });
        },

        //------------------  Managing Main Process (Add/Edit) ------------------------//
        mainProcess: function() {
                if(!trackConsoleSideBar.items("SB_mainprocess")) {
                trackConsoleSideBar.addItem({ id:"SB_mainprocess"});
                trackConsoleSideBar.items("SB_mainprocess").setActive();
                var mainProcessLayout = trackConsoleSideBar.cells('SB_mainprocess').attachLayout("2U");
                mainProcessLayout.cells("a").setText("New Main Process");
                mainProcessLayout.cells("b").setText("List Main Process");
                mainProcessLayout.cells("a").setWidth(400);
                mainProcessGrid = mainProcessLayout.cells("b").attachGrid();
                mainProcessGrid.attachHeader(",#text_filter,,#select_filter,,<select id='MainProcessStatusSelect'></select>");
                mainProcessGrid.enableTooltips("false,false,false,false,false,false");
                mainProcessGrid.init();
                preTally.Settings.progressOn(true, dhxLayout, null);
                mainProcessGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttestationMainProcess.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    mainProcessGrid.makeFilter("MainProcessStatusSelect",4);
                    mainProcessGrid.attachEvent("onRowSelect",preTally.Track.editMainProcess);
                    mainProcessGrid.attachEvent("onRowSelect",function(){
                        dhtmlx.confirm({
                            title: "Edit Details",
                            type:"confirm-warning",
                            ok  : "Yes", cancel : "No",
                            text: "Do you want to edit main process?",
                            callback: function(result) {
                                if(!result) {
                                    addMainProcessForm.clear();
                                    addMainProcessForm.setItemValue("APM_Id",0);
                                    MainProcessId = '';
                                    APM_APMA_Combo.setComboText("");
                                    APM_APMA_Combo.setComboValue("");
                                }
                            }
                        });
                     });
                });
                
                addMainProcessForm = mainProcessLayout.cells("a").attachForm();
                preTally.Settings.progressOn(true, dhxLayout, null);
                addMainProcessForm.loadStruct(preTally.Initialize.encryptURL("requisites/newAttestationMainProcess.php"), function() {
                    preTally.GoogleAddress.googleAddress(addMainProcessForm);
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    APM_APMA_Combo = addMainProcessForm.getCombo("APMA_Title");
                    APM_APMA_Combo.enableFilteringMode(true);
                    APM_APMA_Combo.load(preTally.Initialize.encryptURL("requisites/attestationAPMATitle.php"),function(){
                       APM_APMA_Combo.setComboValue(addMainProcessForm.getItemValue("APMA_Title")); 
                    });
                    addMainProcessForm.setItemValue("APMA_Id",0);
                    APM_APMA_Combo.attachEvent("onChange",function(){
                        actualAPMAComboValue=addMainProcessForm.getItemValue("APMA_Title");
                        APM_APMA_Combo.setComboValue(addMainProcessForm.getItemValue("APMA_Title"));
                        addMainProcessForm.setItemValue("APMA_Id",actualAPMAComboValue);
                    });
                    APM_Combo = addMainProcessForm.getCombo("APM_Status");
                    actualComboValue=APM_Combo.getSelectedValue();
                        APM_Combo.attachEvent("onClose",function(e){
                             if(MainProcessId){
                                dhtmlx.confirm({
                                    title: "Change Status",
                                    type:"confirm-warning",
                                    ok  : "Yes", cancel : "No",
                                    text: "Do you want to change Status?",
                                    callback: function(result) { 
                                       if(!result){
                                          APM_Combo.setComboValue(actualComboValue);
                                       }
                                       else{
                                          actualComboValue=APM_Combo.getSelectedValue();
                                       }   
                                   }
                                });
                             }
                        });
                      
                      addMainProcessForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'newMainProcessValidate') {
                            if (addMainProcessForm.validate()) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addMainProcessForm.send(preTally.Initialize.encryptURL('warehouse/newAttestationMainProcess.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addMainProcessForm.resetValidateCss();
                                        addMainProcessForm.clear();
                                        addMainProcessForm.setItemValue("APM_Id",0);
                                        MainProcessId = '';
                                        APM_APMA_Combo.setComboText("");
                                        APM_APMA_Combo.setComboValue("");
                                    } else 
                                        response = 'Some error has occured.';
//                                addMainProcessForm.setItemValue("APM_Id",0);
                                dhtmlx.message({text: response});
                                mainProcessGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttestationMainProcess.php"), true, true, function() {
                                    mainProcessGrid.filterByAll();
                                    APM_APMA_Combo.setComboText("");
                                    APM_APMA_Combo.setComboValue("");
                                    });
                                });
                            }

                        } else { 
                            addMainProcessForm.resetValidateCss();
                            addMainProcessForm.clear();
                        }
                        if(name == 'newMainProcessCancel'){
                           addMainProcessForm.setItemValue("APM_Id",0);
                           MainProcessId = '';
                           APM_APMA_Combo.setComboText("");
                           APM_APMA_Combo.setComboValue("");
                       }
                     });
                });
                mainProcessGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(mainProcessGrid,4);
            /*Set Order*/  
                        rowVal=mainProcessGrid.getAllRowIds();
                        exprowval=rowVal.split(',');
                        for (var i=0; i<mainProcessGrid.getRowsNum(); i++){
                           eachRowId=exprowval[i];
                           mainProcessGrid.cells(eachRowId,0).setValue(i+1);
                        }
           /*Set Order*/ 
                });
            } else 
                trackConsoleSideBar.items("SB_mainprocess").setActive();
        },
        editMainProcess: function (rowId){ 
            MainProcessId = rowId;
            addMainProcessForm.setItemValue("APM_Id", MainProcessId);
            addMainProcessForm.setItemValue("APM_Title", mainProcessGrid.getUserData(MainProcessId, "UData_APM_Title"));
            addMainProcessForm.setItemValue("APM_Title_Alias", mainProcessGrid.getUserData(MainProcessId, "UData_APM_Title_Alias"));
            addMainProcessForm.setItemValue("APM_Description", mainProcessGrid.getUserData(MainProcessId, "UData_APM_Description"));
            addMainProcessForm.setItemValue("APM_Status", mainProcessGrid.getUserData(MainProcessId, "UData_APM_Status"));
            addMainProcessForm.setItemValue("APMA_Title", mainProcessGrid.getUserData(MainProcessId, "UData_APMA_Title"));
            
        },      
        //------------------------ Managing Entry settings (Add/Edit) ---------------//        
        balSettings : function () {
            if(!trackConsoleSideBar.items("SB_balSettings")) {
                trackConsoleSideBar.addItem({ id:"SB_balSettings"});
                trackConsoleSideBar.items("SB_balSettings").setActive();
                
                var authorityLayout = trackConsoleSideBar.cells('SB_balSettings').attachLayout("2U");
                authorityLayout.cells("a").setText("Balance Sheet Settings");
                authorityLayout.cells("b").setText("Manage Balance Sheet Settings");
                authorityLayout.cells("a").setWidth(480);
                balSettingsGrid = authorityLayout.cells("b").attachGrid();
                balSettingsGrid.attachHeader(",<select id='typeSelect'></select>,,#text_filter_inc,#text_filter_inc,,<select id='StatusSelect'></select>");
                balSettingsGrid.enableTooltips("false,false,false,false,false,false,true");
                balSettingsGrid.init();
                preTally.Settings.progressOn(true, dhxLayout, null);
                balSettingsGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttestationBalSettings.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    balSettingsGrid.makeFilter("typeSelect",2);
                    balSettingsGrid.makeFilter("StatusSelect",5);
                    balSettingsGrid.attachEvent("onRowSelect",preTally.Track.editBalSettings);                   
                });

                balSettingsGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(balSettingsGrid,4);
                });

                balSettingsForm = authorityLayout.cells("a").attachForm();
               
                preTally.Settings.progressOn(true, dhxLayout, null);
                balSettingsForm.loadStruct(preTally.Initialize.encryptURL("requisites/newAttestationBalSheetSettings.php"), function() {
                     preTally.Settings.progressOff(true, dhxLayout, null);

                        balSettingsForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'newBalSheetSettingsValidate') {
                            if (balSettingsForm.validate()) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                balSettingsForm.send(preTally.Initialize.encryptURL('warehouse/newAttestationBalSheetSettings.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        balSettingsForm.resetValidateCss();
                                        balSettingsForm.clear();
                                        balSettingsForm.setItemValue("AJB_Id", 0);
                                        balSettingsForm.setItemValue("AJB_IT_Id", 0);
                                        balSettingsForm.setItemValue("MH_Type", 0);
                                        preTally.Track.BalSheetSettItemVisibility('disable');
                                        //ItemCombo.clearAll();
                                        //ItemCombo.setComboText("");
                                        //ItemCombo.setComboValue("");
                                    } else
                                        response = 'Some error has occured.';
                                    dhtmlx.message({text: response});
                                    balSettingsGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttestationBalSettings.php"), true, true, function() {
                                        balSettingsGrid.filterByAll();
                                        preTally.Track.BalSheetSettItemVisibility('disable');
                                    });
                                });
                            }
                        } else {
                            balSettingsForm.resetValidateCss();
                            balSettingsForm.clear();
                            balSettingsForm.setItemValue("AJB_Id", 0);
                            balSettingsForm.setItemValue("AJB_IT_Id", 0);
                            balSettingsForm.setItemValue("MH_Type", 0);
                            preTally.Track.BalSheetSettItemVisibility('disable');
                        }
                    });
                    
                    MH_Type_Combo = balSettingsForm.getCombo("MH_Type");
                    AJB_IT_Combo = balSettingsForm.getCombo("AJB_IT");
                    AJB_IT_Combo.enableFilteringMode(true);
                    preTally.Settings.comboSelectPreload(MH_Type_Combo, "MH_Type");
                    preTally.Settings.comboSelectPreload(AJB_IT_Combo, "AJB_IT");
                    MH_Type_Combo.attachEvent("onClose", function(){
                        AJB_IT_Combo.clearAll();
                        var MH_Type = MH_Type_Combo.getSelectedValue();
                        var params = "&type="+MH_Type
                        AJB_IT_Combo.load(preTally.Initialize.encryptURL("requisites/items.php" + params), function(loader, response) {});
                    });
                });
            } else 
                trackConsoleSideBar.items("SB_balSettings").setActive();
        },
        

        ATPInstruction : function() {
            if(!trackConsoleSideBar.items("ATPInstruction")) {
                trackConsoleSideBar.addItem({ id:"ATPInstruction"});
                trackConsoleSideBar.items("ATPInstruction").setActive();
                
                var ATPInstructionForm = trackConsoleSideBar.cells("ATPInstruction").attachForm();
                ATPInstructionForm.loadStruct(preTally.Initialize.encryptURL("requisites/ATPManageInstnForm.php&w="+$( document ).width()+"&h="+$( document ).height()), function() {
                
                    ATPInstructionForm.attachEvent("onButtonClick", function(name){
                        preTally.Settings.progressOn(true, dhxLayout, null);
                        ATPInstructionForm.send(preTally.Initialize.encryptURL("warehouse/ATPManageInstnForm.php"), function(loader,response) {         
                            preTally.Settings.progressOff(true, dhxLayout, null);
                        });
                    });
                });
            } else {

                trackConsoleSideBar.items("ATPInstruction").setActive();
            }
        },
        //========================================= End Of Automation Code ======================================//
        editBalSettings: function(rowId) {
            preTally.Track.BalSheetSettItemVisibility('enable');
            var AJB_Id = rowId;

            balSettingsForm.setItemValue("AJB_Id", balSettingsGrid.getUserData(AJB_Id, "UData_AJBS_Id"));
            balSettingsForm.setItemValue("AJB_Name", balSettingsGrid.getUserData(AJB_Id, "UData_AJS_Name")); 
            balSettingsForm.setItemValue("AJBS_Type", balSettingsGrid.getUserData(AJB_Id, "UData_AJBS_Type"));
            balSettingsForm.setItemValue("AJB_Status", balSettingsGrid.getUserData(AJB_Id, "UData_Bal_Status"));

            var MH_Type = balSettingsGrid.getUserData(AJB_Id, "UData_MH_Type")
            if(MH_Type=='--') MH_Type = ''; 
            balSettingsForm.setItemValue("MH_Type", MH_Type);
            var params = "&type="+MH_Type
            
            AJB_IT_Combo.load(preTally.Initialize.encryptURL("requisites/items.php" + params), function(loader, response) {
                balSettingsForm.setItemValue("AJB_IT", balSettingsGrid.getUserData(AJB_Id, "UData_AJS_ITId"));
            });
        },
        BalSheetSettItemVisibility: function(VisibilyItem) {
            var setItemArray = ["AJB_Name", "MH_Type", "AJB_IT","AJB_Status","newBalSheetSettingsValidate","newBalSheetSettingsCancel"];
            if(VisibilyItem=='enable') {
                preTally.Functions.enableItem(balSettingsForm, setItemArray);
            } else {
                preTally.Functions.disableItem(balSettingsForm, setItemArray);
            }
        },

        //----------------------------------------- Listing All Job Documents -------------------------------------------//         
        ListTrackDocumnet:function() { //------ Function Used for listing Documnet Wise list
            
            $.ajax({
                        url : preTally.Initialize.encryptURL("requisites/nextProcess.php"),
                        }).done(function(data) {
                        nxtProcessFlterComboData = data;
            });
            if(!trackConsoleSideBar.items("SB_list_track_document")) { //--- If Documnet Wise List tab is not open, open it
                trackConsoleSideBar.addItem({ id:"SB_list_track_document"});
                trackConsoleSideBar.items("SB_list_track_document").setActive();
                var filtrInterval;
                var status;
                dhxTrackDocumentListLayout =  trackConsoleSideBar.cells("SB_list_track_document").attachLayout("3L");
                dhxTrackDocumentListLayout.cells("a").hideHeader();
                dhxTrackDocumentListLayout.cells("b").setWidth(350);
                dhxTrackDocumentListLayout.cells("b").hideHeader();
                dhxTrackDocumentListLayout.cells("b").setHeight(240);
                
                //-----documnet grid status bar start
                var tb_data_txt = "<div  style='float:left;font-weight:bold' class='tb_cnt_tot_doc'># : 0</div>\
                                   <div style='float:right;'> <div id='docList_paging' style='float:left;'></div><input type='button' className= 'button_export' name='export_Enquiry' value='Export' onClick='preTally.Track.exportTrackReport(\"document_wise\",TrckListDocTlbr)' style='float:right; background-image: url(images/icon/default_18/excel.png);background-repeat: no-repeat; background-position: 0px 3px ;padding-left: 22px;height: 25px'></div>";
                dhxTrackDocumentListLayout.cells("a").attachStatusBar({
                    text:   tb_data_txt,   
                    height: 30             
                });//----documnet grid status bar End

                TrckListDocTlbr = dhxTrackDocumentListLayout.cells("a").attachToolbar();                
                TrckListDocTlbr.setIconsPath("images/icon/default_18/");
                trackListDocGrid = dhxTrackDocumentListLayout.cells("a").attachGrid(); 

                preTally.Track.dateFiltterMenu(TrckListDocTlbr,"listTrackDocument",trackListDocGrid); //--- Attach Date filter menu
                trackListDocGrid.attachHeader("#rspan,#rspan,<input type='text' class='textFilter_DOC' id ='itmDoc' style='width: 90%;' placeholder='Document'>,#rspan,#rspan,\n\
                    <input type='text' class='textFilter_DOC' id ='itmDocCusName' style='width: 90%;' placeholder='Customer Name'>,\n\
                    <input type='text' class='textFilter_DOC' id ='itmDocTrackId' style='width: 90%;' placeholder='Track ID'>,\n\
                    <input type='text' class='textFilter_DOC' id ='itmDocCreateUser' style='width: 90%;' placeholder='Created User'>,\n\
                    <input type='text' class='textFilter_DOC' id ='itmDocCreateBranch' style='width: 90%;' placeholder='Created Branch'>,\n\
                    <select class = 'selectFilter_DOC' id='itmDocStatus' style='width:70px; font-size:8pt; font-family:Tahoma;'>\n\
                        <option value ='0'>All</option>\n\
                        <option value ='1'>New</option>\n\
                        <option value ='2'>Underprocess</option>\n\
                        <option value ='3'>Transit</option>\n\
                        <option value ='4'>All Process Completed</option>\n\\n\
                        <option value ='5'>Delivered</option>\n\
                    </select>");
                trackListDocGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false");
                trackListDocGrid.setMultiLine(true);
                trackListDocGrid.attachEvent("onXLS", function() {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                trackListDocGrid.attachEvent("onXLE", function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                var filterValue = '&f='+TrckListDocTlbr.getValue("srch_date_from")+'&t='+TrckListDocTlbr.getValue("srch_date_till");
                //--- Load Data in Document Wise list
                trackListDocGrid.init();  
                trackListDocGrid.setPagingWTMode(true,false,true,[15,30,50]);
                trackListDocGrid.enablePaging(true,50,5,"docList_paging",true);
                trackListDocGrid.setPagingSkin("toolbar", "dhx_skyblue");
                trackListDocGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackDocument.php"+filterValue), function() {
                    
                    nxtProcessFlterCombo = new dhtmlXCombo("itmDocNxtProcess");
                    nxtProcessFlterCombo.setPlaceholder('Next Process');
                    nxtProcessFlterCombo.load(nxtProcessFlterComboData, function(){
                        nxtProcessFlterCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                    r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                    r = true;
                            }
                            return r;
                        });
                    });
                    
                    nxtProcessFlterCombo.setOptionWidth(180);
                    nxtProcessFlterCombo.attachEvent("onChange", function() {
                        $("#itmDocNxtProcess" ).val(nxtProcessFlterCombo.getSelectedValue());
                        preTally.Track.applyFilter(TrckListDocTlbr,"listTrackDocument",trackListDocGrid);
                    });

                    $('.tb_cnt_tot_doc').html("# : "+trackListDocGrid.getUserData("", "TL_Count")+" ");
                    $( ".selectFilter_DOC" ).change(function() {
                        preTally.Track.applyFilter(TrckListDocTlbr,"listTrackDocument",trackListDocGrid);
                    });
                    
                    $( ".textFilter_DOC" ).keyup(function() {
                        if(filtrInterval) {
                            clearInterval(filtrInterval);
                        }
                        filtrInterval = setInterval( function() { 
                            preTally.Track.applyFilter(TrckListDocTlbr,"listTrackDocument",trackListDocGrid); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    });
                });
                trackListDocGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(trackListDocGrid,10)
                });
                
                trackListDocGrid.attachEvent("onCheck", function(rId,cInd,state){
                    if(state == false) {
                        var checkedDocArray = checkedDoc.split(',');
                        var uniqueDocIds = [];
                        $.each(checkedDocArray, function(i, el){
                            if($.inArray(el, uniqueDocIds) === -1) uniqueDocIds.push(el);
                        });

                        var pos = uniqueDocIds.indexOf(rId);
                        if ( ~pos ) uniqueDocIds.splice(pos, 1);
                        checkedDoc = uniqueDocIds.join();
                    }
                });
                
                //---group form start
                TrackDocGroupForm = dhxTrackDocumentListLayout.cells("b").attachForm();
                TrackDocGroupForm.loadStruct(preTally.Initialize.encryptURL("requisites/trackGroupItemForm.php"), function() {
                    TrackDocGroupForm.attachEvent("onButtonClick", function(name) {
                        var AJGID = TrackDocGroupForm.getItemValue("AJG_Id");
                        if (name == 'BtnSaveGroup') {
                            var values = TrackDocGroupForm.getFormData();
                            var trDocGpValidate = preTally.Validate.Validate(values, 'AJG_Name', TrackDocGroupForm, 'title'); 
                            if(trDocGpValidate == false) {
                                TrackDocGroupForm.setValidateCss('AJG_Name', trDocGpValidate,  'validate_red');
                                return false;
                            }
                            var newGroupValidate = TrackDocGroupForm.validate();
//                            var ItemDocList = trackListDocGrid.getCheckedRows(0).split(",") ;
                            
                            if(trackListDocGrid.getCheckedRows(0) != '' ){
                                if(checkedDoc!=''){
                                    checkedDoc  = checkedDoc+","+trackListDocGrid.getCheckedRows(0); 
                                }else{
                                    checkedDoc  = trackListDocGrid.getCheckedRows(0); 
                                }
                            }
                
                            var checkedDocArray = checkedDoc.split(',');
                            var uniqueDocIds = [];
                            $.each(checkedDocArray, function(i, el){
                                if($.inArray(el, uniqueDocIds) === -1) uniqueDocIds.push(el);
                            });
                            
                            var ItemDocList = uniqueDocIds;
                            
                            if (newGroupValidate) {
                                if(ItemDocList.length<=50){
                                    if((ItemDocList['0']!=' ') && (ItemDocList['0']!=0)) {
                                        $.ajax({ //check for documents include Delivered
                                            url : preTally.Initialize.encryptURL("warehouse/trackDocStatus.php"),
                                            type: 'POST',
                                            data : {"docId" : ItemDocList,"status":[5]},
                                            success: function(data) {
                                                if(data == "success") {
                                                    TrackDocGroupForm.setItemValue("AJD_Id",ItemDocList);

                                                    TrackDocGroupForm.send(preTally.Initialize.encryptURL("warehouse/newAttestationProcessGroup.php"), function(loader,response) {
                                                        if(response!== "fail" && response!=="updated") {
                                                            dhtmlx.alert({text: 'Group created successfully<br>Group Number : '+response});
                                                            preTally.Track.clearGroup();
                                                        }else if(response==="updated") {
                                                            dhtmlx.message({text: 'Group updated successfully'});
                                                            preTally.Track.clearGroup();
                                                        } else{
                                                            dhtmlx.message({text: 'Some error occured. Please Re-Try'});
                                                        }                                      

                                                    });
                                                }else {
                                                    dhtmlx.message({
                                                        title: "Warning",
                                                        type:  "alert-warning",
                                                        text:  "Sorry, You can't Create Group With Delivered Documents!",
                                                    });
                                                }
                                            }
                                        });
                                    } else {
                                        dhtmlx.message({text: 'No Document(s) selected.Please select and Re-Try.'});
                                    }
                                }else{
                                    dhtmlx.message({text: 'Cant create group with greater than 50 documents .'});
                                }
                            }
                        } else if(name == "sendDoc") { 
                            status =[3, 5]; 
                            preTally.Track.gpDocumentsOperations(this,AJGID,'Send',status,'DocumnetsSend',570, 255); // used warehouse/DocumnetsSend.php
                        } else if(name == "updtPros") {
                            status =[3, 4, 5]; 
                            preTally.Track.gpUpdateProcess(this,AJGID,status);
                        } else if(name == "devryDoc") { 
                             status =[1, 2, 3, 5]; 
                            preTally.Track.gpDocumentsOperations(this,AJGID,'Delivered',status,'DocumnetsDeliverd',570,300); // used warehouse/DocumnetsDeliverd.php
                        } else if(name == "BtnClearGroup") { 
                            preTally.Track.clearGroup(); 
                        }
                    });
                });//----group form end
                //-----group grid start
                dhxTrackDocumentListLayout.cells("c").hideHeader();
                trackListGpDocGrid = dhxTrackDocumentListLayout.cells("c").attachGrid();
                trackListGpDocGrid.attachHeader("#rspan,<input type='text' class='textFilter_GP' id ='itmGpDoc' style='width: 90%;' placeholder=''>,#rspan,#rspan,\n\
                    <select class = 'selectFilter_GRP' id='itmGpStatus' style='width:40px; font-size:8pt; font-family:Tahoma;'>\n\
                        <option value ='0'>All</option>\n\
                        <option value ='1'>Blocked</option>\n\
                        <option value ='2'>Published</option>\n\
                    </select>");          
                trackListGpDocGrid.enableTooltips("false,false,false,false,false,false");
                trackListGpDocGrid.attachEvent("onXLS", function() {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                trackListGpDocGrid.attachEvent("onXLE", function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                trackListGpDocGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackDocGroup.php"), function() {
                    $('.tb_cnt_gp_tot').html("# : "+trackListGpDocGrid.getUserData("", "TL_Gp_Count")+" ");
                    trackListGpDocGrid.makeFilter("SprtDocStatusSelect",4);
                    
                    trackListGpDocGrid.attachEvent("onRowSelect",preTally.Track.editGroupList); 
                   
                    $( ".selectFilter_GRP" ).change(function() {
                        preTally.Track.applyFilter('NonDate',"listTrackDocGroup",trackListGpDocGrid);
                    });
                    
                    $( ".textFilter_GP" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);
                            filtrInterval = setInterval( function() { 
                                preTally.Track.applyFilter('NonDate',"listTrackDocGroup",trackListGpDocGrid); 
                                clearInterval(filtrInterval); 
                            }, 500);
                    });
                });
                trackListGpDocGrid.attachEvent("onFilterEnd", function(elements){
                    preTally.Functions.recordNotFound(trackListGpDocGrid,3);
                });
                var tb_dataGroup_txt = ' <div class="tb_data_txt_secl">\
                                       <div class="tb_cnt_gp_tot"># : 0</div>\
                                    </div>';
                 dhxTrackDocumentListLayout.cells("c").attachStatusBar({
                    text:   tb_dataGroup_txt,   
                    height: 23             
                });
                //----group grid end
            } else {
                trackConsoleSideBar.items("SB_list_track_document").setActive();
            }
        },
        clearGroup:function(action) { //-- Function for clear Data in group Grid
            checkedDoc = '';
            checkedDocSend = '';
            trackListDocGrid.uncheckAll();
            
            preTally.Track.clearDocFilters();
            TrackDocGroupForm.resetValidateCss();
            TrackDocGroupForm.clear();
            trackListDocGrid.hdr.rows[1].cells[0].getElementsByTagName("INPUT")[0].checked = false;
            
            if(action!='update') {
                var monthNames = [ "January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December" ]; 
                var date = new Date();
                var month = date.getMonth();
                var year = date.getFullYear();
                var tillDate = Date.today().toString("dd.MM.yyyy"); 
                var fromDate = new Date(year,month,1).toString("dd.MM.yyyy");
                
                TrckListDocTlbr.setValue('srch_date_till', tillDate);
                TrckListDocTlbr.setValue('srch_date_from', fromDate);
                TrckListDocTlbr.setItemText('srch_month_filter',monthNames[month]);
                TrckListDocTlbr.setItemImage('srch_month_filter', 'calendar_M.png');
            } 
            
            preTally.Track.applyFilter(TrckListDocTlbr,"listTrackDocument",trackListDocGrid);
            preTally.Track.applyFilter('NonDate',"listTrackDocGroup",trackListGpDocGrid);

        },
        editGroupList: function(GpId){ //---- Function For edit Group List
            preTally.Track.clearDocFilters();
            TrackDocGroupForm.setItemValue("AJG_Id", GpId);
            TrackDocGroupForm.setItemValue("AJG_Name", trackListGpDocGrid.getUserData(GpId, "UData_AJG_Name"));
           
            TrckListDocTlbr.setItemText('srch_month_filter', 'Select Month'); 
            TrckListDocTlbr.clearItemImage('srch_month_filter');
            TrckListDocTlbr.setValue('srch_date_from', '', false); 
            TrckListDocTlbr.setValue('srch_date_till', '', false);
            
            var filter = "&GpRwId="+GpId+"&filter=";
            trackListDocGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listTrackDocument.php"+filter), function() {
                checkedDoc = trackListDocGrid.getCheckedRows(0);
                checkedDocSend = checkedDoc;
                $('.tb_cnt_tot_doc').html("# : "+trackListDocGrid.getUserData("", "TL_Count")+" ");
            });
        },
        clearDocFilters : function(){
            TrckListDocTlbr.setItemText('srch_day_filter', 'Select Day'); 
            TrckListDocTlbr.setItemText('srch_year_filter', 'Select Year'); 
            TrckListDocTlbr.clearItemImage('srch_day_filter');
            TrckListDocTlbr.clearItemImage('srch_year_filter');
            $('#itmGpStatus').val(0);
            $('#itmDocStatus').val(0);
            nxtProcessFlterCombo.setComboText('');
            nxtProcessFlterCombo.setComboValue('');
            var setFilterArray = ['#itmDoc','#itmDocProcess','#itmDocCusName','#itmDocTrackId','#itmDocCreateUser','#itmDocCreateBranch','#itmGpDoc','#itmDocNxtProcess'];
            $.each(setFilterArray, function( index, value ) {
                $(value).val(""); 
            });
            
            
        },
        gpDocumentsOperations: function(inp,gpValue,gpAction,status,documnetPage,winX,winY){ //--- Function for Sent,Recive,Delivery Doument operations
                var docSendFlag=0;
                if(gpValue!=""&& gpValue!=0){
                    if(trackListDocGrid.getCheckedRows(0) != '' ){
                                if(checkedDoc!=''){
                                    checkedDoc  = checkedDoc+","+trackListDocGrid.getCheckedRows(0);
                                }else{
                                    checkedDoc  = trackListDocGrid.getCheckedRows(0); 
                                }
                            }
                
                            var checkedDocArray = checkedDoc.split(',');
                            var uniqueDocIds = [];
                            $.each(checkedDocArray, function(i, el){
                                if($.inArray(el, uniqueDocIds) === -1) uniqueDocIds.push(el);
                            });
                         checkedDocSendArr= checkedDocSend.split(",");
                         uniqueDocIdsArr=uniqueDocIds.sort().join(','); 
                         checkedDocSendArr=checkedDocSendArr.sort().join(','); 
                         if(uniqueDocIdsArr===checkedDocSendArr){
                             docSendFlag=0;
                         }else docSendFlag=1;
                    
                }
                if(docSendFlag==0){
                    var docId = trackListDocGrid.getCheckedRows(0).split(",") ;
                    gpValue = gpValue ? gpValue : 0; 

                    if((docId['0']!=' ') && (docId['0']!=0)) {
                       $.ajax({
                        url : preTally.Initialize.encryptURL("warehouse/trackDocStatus.php"),
                        type: 'POST',
                        data : {"docId" : docId,"status":status,"gpAction":gpAction},
                        success: function(data) { 
                                if(data == "success"){
                                    dhxMessageWin = new dhtmlXWindows();
                                    var x = getAbsoluteLeft(inp);
                                    var y = getAbsoluteTop(inp);

                                    messageWin = dhxMessageWin.createWindow("wins_msg", x, y,winX,winY);
                                    messageWin.button("minmax").hide();
                                    messageWin.button("park").hide();
                                    messageWin.denyResize();
                                    //messageWin.button("close").hide();
                                    messageWin.center();
                                    messageWin.setModal(true);
                                    messageWin.setText(gpAction+' Documents');
                                    gpSendDocumentsForm = messageWin.attachForm();
                                    preTally.Settings.progressOn(true, messageWin, null);

                                    gpSendDocumentsForm.loadStruct(preTally.Initialize.encryptURL('requisites/tracksndRevDevdDocForm.php&gpAction='+gpAction+'&gId='+gpValue), function() {
                                        preTally.Settings.progressOff(true, messageWin, null);
                                        if(gpAction!='Delivered') {
                                            GP_Brch_Combo = gpSendDocumentsForm.getCombo("LC_Id");
                                            AJ_descCombo  = gpSendDocumentsForm.getCombo("BS_Description");

                                            GP_Brch_Combo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/attestationBranch.php"), true);
                                            AJ_descCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/descriptions.php&trackAction=true"), true);

                                            preTally.Settings.comboFilterPreload(GP_Brch_Combo, "GP_Brch_Combo");
                                            preTally.Settings.comboFilterPreload(AJ_descCombo, "AJ_descCombo");
                                        }
                                        gpSendDocumentsForm.attachEvent("onButtonClick", function(Name) {
                                            if (Name == 'GpSubmitBtn') {
                                                var branchValidate = 1;
                                                if(gpAction=='Send') {
                                                    if(GP_Brch_Combo.getSelectedValue()==0) {
                                                        branchValidate = 0;
                                                        dhtmlx.message({text: "Invalid Branch Name"});  
                                                    }
                                                }
                                                var messageValidate = gpSendDocumentsForm.validate();
                                                if (messageValidate && branchValidate) {
                                                    preTally.Settings.progressOn(true, messageWin, null);
                                                    var params = "&docId="+docId;
                                                    if(documnetPage=="DocumnetsSend"){
                                                        params += "&Description="+AJ_descCombo.getComboText();
                                                    }
                                                    gpSendDocumentsForm.send(preTally.Initialize.encryptURL("warehouse/"+documnetPage+".php"+params),function(loader, response){
                                                       preTally.Settings.progressOff(true, messageWin, null);
                                                        if(response == "success") {
                                                            gpSendDocumentsForm.resetValidateCss();
                                                            gpSendDocumentsForm.clear();
                                                            dhtmlx.message({text: "Documents "+gpAction+" Successfully. "});
                                                            preTally.Settings.hideMessageWin();
                                                            preTally.Track.clearGroup('update'); 
                                                        } else {
                                                            dhtmlx.message({text: "Some error has occured.Please Re-Try"});
                                                        }
                                                    }); 
                                                }
                                            } else if(Name=='GpCancel'){
                                                gpSendDocumentsForm.resetValidateCss();
                                                gpSendDocumentsForm.clear();
                                                preTally.Settings.hideMessageWin();
                                            }
                                        });

                                    }); 

                                } else {
                                    if(gpAction=='Send'){
                                        var message ="Sorry, You can't Send "+'"'+"Transit/Deliverd"+'"'+" Documents!";
                                    } else if(gpAction =='Delivered'){
                                        var message ='Sorry, You can Only Deliver "All Process Completed" Documents!';
                                    }
                                    dhtmlx.message({
                                        title: "Warning",
                                        type:  "alert-warning",
                                        text:  message,
                                    });
                                }
                            }
                        });
                    } else {
                        dhtmlx.message({text: "Empty Item Selected."});
                    }
            }else{
                dhtmlx.message({text: "Please save the document and continue."});
            }
        },
        gpUpdateProcess: function(inp,gpValue,status){
           
            var  docId = trackListDocGrid.getCheckedRows(0).split(",") ;
            gpValue = gpValue ? gpValue : 0; 
            
            if((docId['0']!=' ') && (docId['0']!=0)) {
                $.ajax({
                url : preTally.Initialize.encryptURL("warehouse/trackDocStatus.php"),
                type: 'POST',
                data : {"docId" : docId,"status":status},
                success: function(data) { 
                        if(data == "success"){
                            dhxMessageWin = new dhtmlXWindows();
                            var x = getAbsoluteLeft(inp);
                            var y = getAbsoluteTop(inp);
                            messageWin = dhxMessageWin.createWindow("wins_msg", x, y, 800, 420);
                            messageWin.button("minmax").hide();
                            messageWin.button("park").hide();
                            messageWin.denyResize();
                            //messageWin.button("close").hide();
                            messageWin.center();
                            messageWin.setModal(true);
                            messageWin.setText("Update Process");
                            gpUpdateProcessForm = messageWin.attachForm();
                            preTally.Settings.progressOn(true, messageWin, null);
                
                            gpUpdateProcessForm.loadStruct(preTally.Initialize.encryptURL('requisites/trackProcessUpdateForm.php&gId='+gpValue+'&docId='+docId), function() {
                                preTally.Settings.progressOff(true, messageWin, null);
                                
                                GP_MP_Id_Combo = gpUpdateProcessForm.getCombo("MP_Id");
                                GP_SP_Id_Combo = gpUpdateProcessForm.getCombo("APS_Id");
                                preTally.Settings.comboSelectPreload(GP_MP_Id_Combo, "MP_Id");
                                preTally.Settings.comboSelectPreload(GP_SP_Id_Combo, "APS_Id");
                                GP_MP_Id_Combo.attachEvent("onChange", function(){
                                    GP_SP_Id_Combo.clearAll();
                                    var MP_Id = GP_MP_Id_Combo.getSelectedValue();
                                    var params = "&MP_Id="+MP_Id+"&gpValue="+gpValue+"&docId="+docId;
                                    GP_SP_Id_Combo.load(preTally.Initialize.encryptURL("requisites/trackSubProcess.php" + params), function(loader, response) {});
                                });
                                
                                GP_IT_PId_Combo = gpUpdateProcessForm.getCombo("IT_PId");
                                GP_DS_PId_Combo = gpUpdateProcessForm.getCombo("DS_PId");
                                GP_IT_TId_Combo = gpUpdateProcessForm.getCombo("IT_TId");
                                GP_DS_TId_Combo = gpUpdateProcessForm.getCombo("DS_TId");

                                preTally.Settings.comboSelectPreload(GP_IT_PId_Combo, "IT_PId");
                                preTally.Settings.comboSelectPreload(GP_DS_PId_Combo, "DS_PId");
                                preTally.Settings.comboSelectPreload(GP_IT_TId_Combo, "IT_TId");
                                preTally.Settings.comboSelectPreload(GP_DS_TId_Combo, "DS_TId");
                                
                                GP_IT_PId_Combo.setOptionWidth(350);
                                GP_DS_PId_Combo.setOptionWidth(350);
                                GP_IT_TId_Combo.setOptionWidth(350);
                                GP_DS_TId_Combo.setOptionWidth(350);
                                
                                GP_IT_PId_Combo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/items.php&type=2"));
                                GP_DS_PId_Combo.enableFilteringMode(true);
                                GP_IT_TId_Combo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/items.php&type=2"));
                                GP_DS_TId_Combo.enableFilteringMode(true);
                                
                                GP_IT_PId_Combo.attachEvent("onChange", function(){
                                    preTally.Functions.clearComboValues([GP_DS_PId_Combo]);
                                    var IT_PId = GP_IT_PId_Combo.getSelectedValue();
                                    var params = "&IT_Id="+IT_PId;
                                    GP_DS_PId_Combo.load(preTally.Initialize.encryptURL("requisites/descriptions.php" + params), function(loader, response) {});
                                });
                                
                                GP_IT_TId_Combo.attachEvent("onChange", function(){
                                    preTally.Functions.clearComboValues([GP_DS_TId_Combo]);
                                    var IT_TId = GP_IT_TId_Combo.getSelectedValue();
                                    var params = "&IT_Id="+IT_TId;
                                    GP_DS_TId_Combo.load(preTally.Initialize.encryptURL("requisites/descriptions.php" + params), function(loader, response) {});
                                });
                               
                                var setReqArray = ["IT_PId", "DS_PId", "BS_Amount_Process"];
                                
                                gpUpdateProcessForm.attachEvent("onChange", function (name, value){
                                    if(name == 'AJS_Status') {
                                        if(value == 2) {
                                            gpUpdateProcessForm.showItem("Process_Fee");
                                            preTally.Functions.setRequired(gpUpdateProcessForm, setReqArray, true);
                                        }else{
                                            gpUpdateProcessForm.hideItem("Process_Fee");  
                                            preTally.Functions.setRequired(gpUpdateProcessForm, setReqArray, false);
                                        }
                                    }
                                });

                                gpUpdateProcessForm.attachEvent("onButtonClick", function(Name) {
                                    gpUpdateProcessForm.setItemValue('DS_PDescription', GP_DS_PId_Combo.getComboText());
                                    gpUpdateProcessForm.setItemValue('DS_TDescription', GP_DS_TId_Combo.getComboText());
                                   
                                    gpUpdateProcessForm.attachEvent("onValidateError",preTally.Track.validateCss);
                                    if (Name == 'UpGpPros') {
                                        var upGpPros = gpUpdateProcessForm.validate();
                                        if (upGpPros) {
                                            gpUpdateProcessForm.send(preTally.Initialize.encryptURL("warehouse/updateProcess.php"),function(loader, response){ //updateGpProcessStatus
                                                if(response != "fail" ) {
                                                    dhtmlx.message({text: "Successfully Updated"});
                                                    var comboArray  = [GP_IT_PId_Combo, GP_DS_PId_Combo, GP_IT_TId_Combo, GP_DS_TId_Combo];
//                                                    gpUpdateProcessForm.clear();
                                                    GP_MP_Id_Combo.selectOption('0', false, true);
                                                    gpUpdateProcessForm.setItemValue('AJS_Status', 2);
                                                    gpUpdateProcessForm.setItemValue('BS_Amount_Process', '');
                                                    gpUpdateProcessForm.setItemValue('BS_Amount_Travel', '');
                                                    gpUpdateProcessForm.showItem("Process_Fee");
                                                    preTally.Functions.setRequired(gpUpdateProcessForm, setReqArray, true);
                                                    preTally.Functions.clearComboValues(comboArray);
                                                } else {
                                                    dhtmlx.message({text: "Some error has occured."});
                                                }
                                            });
                                        }
                                    } else {
                                        preTally.Track.hideMessageWin();
                                    }
                                });
                                messageWin.attachEvent("onClose", function(win){
                                    preTally.Track.clearGroup('update'); 
                                    return true;
                                });
                            });
                        }else {
                            dhtmlx.message({
                                title: "Warning",
                                type:  "alert-warning",
                                text:  'Sorry, You can Only Update "UnderProcess" Documents!',
                            });
                        }
                    }
                });
            } else {
                dhtmlx.message({text: "Empty Item Selected."});
            }
        },
        hideMessageWin: function() {
            dhxMessageWin.window("wins_msg").close();
        },
        
        validateCss: function(name, value, res) { //-- Function for text box boder color change when error occured/In-Valied Data
            gpUpdateProcessForm.setValidateCss(name, res, 'validate_red');
            return false;
        },
            
        //------------------- ListTrackEnquiry function for List All Phone Registration ---------------------------------//         
        ListTrackEnquiry: function() { //----------- ListTrackEnquiry function for List All Phone Registration
            if(!trackConsoleSideBar.items("SB_ListTrackEnquiry")) { //------ Check Jobs List Tab is alredy Open Or Not if open
                trackConsoleSideBar.addItem({ id:"SB_ListTrackEnquiry"});
                trackConsoleSideBar.items("SB_ListTrackEnquiry").setActive();
                
                TrackListEnquiryTlbr = trackConsoleSideBar.cells("SB_ListTrackEnquiry").attachToolbar();                
                TrackListEnquiryTlbr.setIconsPath("images/icon/default_18/");
                
                var tb_data_txt =  "<div  style='float:left;font-weight:bold' class='tb_cnt_tot_enqy'># : 0</div>\
                                       <div style='float:right;'> <div id='enqList_paging' style='float:left;'></div><input type='button' className= 'button_export' name='export_Enquiry' value='Export' onClick='preTally.Track.exportTrackReport(\"track_enquiry\",TrackListEnquiryTlbr)' style='float:right; background-image: url(images/icon/default_18/excel.png);background-repeat: no-repeat; background-position: 0px 3px ;padding-left: 22px;height: 25px'></div>";
                trackConsoleSideBar.cells("SB_ListTrackEnquiry").attachStatusBar({
                    text:   tb_data_txt,   
                    height: 28             
                });
               
                trackListEnquiryGrid = trackConsoleSideBar.cells("SB_ListTrackEnquiry").attachGrid();
                preTally.Track.dateFiltterMenu(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid);
                var filtrInterval;
                //trackListEnquiryGrid.enableTooltips("false,false,false,false,false,false,false,true");
                trackListEnquiryGrid.attachHeader("#rspan,\n\
                        <input type='text' class='textFilter_ENQ' id ='itmEnqName' style='width: 85%;'>,#rspan,#rspan,#rspan,\n\
                        <input type='text' class='textFilter_ENQ' id ='itmLastUser' placeholder = 'Contacted User' style='width: 75%;'>,\n\
                        <input type='text' class='textFilter_ENQ' id ='itmLastBranch' placeholder = 'Contacted Branch' style='width: 80%;'>,Last Contacted Date,#rspan,\n\
                        <select class = 'selectFilter_ENQ' id='itmFolupStatus' style='width:80px; font-size:8pt; font-family:Tahoma;'>\n\
                            <option value ='0'>All</option>\n\
                            <option value ='1'>Low</option>\n\
                            <option value ='2'>Normal</option>\n\
                            <option value ='3'>High</option>\n\
                            <option value ='4'>Very High</option>\n\
                        </select>,\n\
                        <input type='text' class='textFilter_ENQ' id ='itmEnqCUser' style='width: 75%;'>,\n\
                        <input type='text' class='textFilter_ENQ' id ='itmEnqCBranch' style='width: 75%;'>,\n\
                        <select class = 'selectFilter_ENQ' id='itmEnqStatus' style='width:60px; font-size:8pt; font-family:Tahoma;'>\n\
                            <option value ='0'>All</option>\n\
                            <option value ='1'>New</option>\n\\n\
                            <option value ='4'>Follow up</option>\n\
                            <option value ='2'>Processed</option>\n\
                            <option value ='3'>Cancelled</option>\n\
                        </select>,#rspan");     
                
                trackListEnquiryGrid.setPagingWTMode(true,false,true,[15,30,50]);
                trackListEnquiryGrid.enablePaging(true,50,5,"enqList_paging",true);
                trackListEnquiryGrid.setPagingSkin("toolbar", "dhx_skyblue");
                
                var filterValue = '&f='+TrackListEnquiryTlbr.getValue("srch_date_from")+'&t='+TrackListEnquiryTlbr.getValue("srch_date_till");
                preTally.Settings.progressOn(true, dhxLayout, null);
                //----------- Load Customer wise data in to Grid 
                trackListEnquiryGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackEnquiry.php"+filterValue), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    $('.tb_cnt_tot_enqy').html("# : "+trackListEnquiryGrid.getUserData("", "TL_Count")+" "); // Set Total Count Of Load Data in footer
                    //------- filter based on text box 
                    $( ".textFilter_ENQ" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            preTally.Track.applyFilter(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    }); 
                    //------- filter based on Dorp  box 
                    $( ".selectFilter_ENQ" ).change(function() { //------- filter based on Select Box
                        preTally.Track.applyFilter(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid);
                    });
                });
                
                trackListEnquiryGrid.attachEvent("onRowSelect",function(id,ind){
                    if(ind != 11 ) { 
                        preTally.Track.enquiryDetails(this,id)
                    }
                });
                trackListEnquiryGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                    var status = trackListEnquiryGrid.cells(rId,7).getValue();
                    if(status=="Cancelled"||status=="Processed" ){
                        return false;
                    }
                    if(stage==2) {
                        var status = trackListEnquiryGrid.cells(rId,7).getValue();
                        if(nValue!=oValue){
                            var status = trackListEnquiryGrid.cells(rId,7).getValue();
                            if(status=='New') {
                                status = 2;
                            } else if(status=='Processed') {
                                status = 1;
                            }
                            var enqUpdtDetails = {};
                            enqUpdtDetails['AE_Id']     = rId;
                            enqUpdtDetails['AE_Name']   = trackListEnquiryGrid.cells(rId,1).getValue();
                            enqUpdtDetails['AE_Mobile'] = trackListEnquiryGrid.cells(rId,2).getValue();
                            enqUpdtDetails['AE_Email']  = trackListEnquiryGrid.cells(rId,3).getValue();
                            enqUpdtDetails['AE_Remarks']= trackListEnquiryGrid.cells(rId,4).getValue();
                            enqUpdtDetails['AE_Status'] = status;
                             $.ajax({
                                type   : "POST",
                                url    : preTally.Initialize.encryptURL('warehouse/updateTrackEnquiry.php'),
                                data   : enqUpdtDetails                    
                            }).done(function(data) { 
                                if(data=='success') {
                                    preTally.Track.applyFilter(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid);
                                }else {
                                    dhtmlx.message({text: 'Some error occured. Please Re-Try'});
                                }
                            });
                        }
                    }
                });
                trackListEnquiryGrid.attachEvent("onFilterEnd", function(elements){//------ Show "record not Found" Message in Empty Grid or no row exist in Grid
                    preTally.Functions.recordNotFound(trackListEnquiryGrid,11);
                });
            } else { //------ Check Jobs List Tab is alredy Open Tab become active state
               // preTally.Reload.reInitialize('list_track_enquiry','track_dashboard');
                trackConsoleSideBar.items("SB_ListTrackEnquiry").setActive();
                
            }
        },
        EnquiryCovertToNewJob:function(AE_Id,AJ_FName,AJ_Mobile,AJ_Email){ 
            dhtmlx.confirm({
                title: "Create New Job",
                type:"confirm-warning",
                ok  : "Yes", cancel : "No",
                text: "Do you want to Create New Job ?",
                callback: function(response) {
                    if(response){
                        if(trackConsoleSideBar.items("SB_edit_track_document")) {
                            trackConsoleSideBar.cells("SB_edit_track_document").remove();
                        }
                        $.ajax({                    
                          url    : preTally.Initialize.encryptURL("warehouse/newAttestationJobFromEnquiry.php&EnquiryId="+AE_Id+"&EnquiryFNa="+AJ_FName+"&EnquiryMb="+AJ_Mobile+"&EnquiryEml="+AJ_Email)
                        }).done(function(data) {

                        if(data!='fail') {
                            preTally.Track.applyFilter(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid);
                            preTally.Track.TrackRegistrationTabUser(this,parseInt(data.trim()));
                        }else {
                            dhtmlx.message({text: "Sorry, Some error has occured."}); 
                        }
                            preTally.Settings.progressOff(true, dhxLayout, null);
                        });

                    }
                }
            });
        },
        enquiryDetails : function(inp,AEId){
            //trackToolbar.enableItem(lastOpenedTab);
            preTally.Track.disableEnableToolBarItems('list_track_jobs');
            
            preTally.Settings.progressOn(true, dhxLayout, null); 
            if(!trackConsoleSideBar.items("SB_enquiryDetails")) {
                trackConsoleSideBar.addItem({ id:"SB_enquiryDetails"});
                trackConsoleSideBar.items("SB_enquiryDetails").setActive();
            }else{
                trackConsoleSideBar.cells("SB_enquiryDetails").remove();
                trackConsoleSideBar.addItem({ id:"SB_enquiryDetails"});
                trackConsoleSideBar.items("SB_enquiryDetails").setActive();
            }
                
            enquiryDetailsLayout = trackConsoleSideBar.cells('SB_enquiryDetails').attachLayout("3L");
            
            enquiryDetailsLayout.cells("a").setText("Enquiry Details");
            enquiryDetailsLayout.cells("b").setText("Add Follow up Details");
            enquiryDetailsLayout.cells("c").setText("List Follow up Details");
            enquiryDetailsLayout.cells("b").setHeight(210);
            enquiryDetailsLayout.cells("a").setWidth(680);
            
            enquiryDetailsForm = enquiryDetailsLayout.cells('a').attachForm();

            var params = "AEId="+AEId ;
            enquiryDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/enquiryDetailsForm.php&"+params), function() {
                preTally.Settings.progressOff(true, dhxLayout,null);
//                
//                var enqStat = enquiryDetailsForm.getItemValue('AE_Status');
//                if(enqStat == 1){
//                    $.ajax({                    
//                        url : preTally.Initialize.encryptURL("warehouse/updateEnquiryStatus.php&AE_Id="+AEId)
//                    }).done(function(data) {
//                    });
//                }
                
                enquiryDetailsForm.attachEvent("onButtonClick", function(name){
                    if(name == 'ConvertJob'){
                        
                        dhtmlx.confirm({
                            title: "Create New Job",
                            type:"confirm-warning",
                            ok  : "Yes", cancel : "No",
                            text: "Do you want to Create New Job ?",
                            callback: function(response) {
                                if(response){
                                    if(trackConsoleSideBar.items("SB_edit_track_document")) {
                                        trackConsoleSideBar.cells("SB_edit_track_document").remove();
                                    }
                                    $.ajax({                    
                                      url    : preTally.Initialize.encryptURL("warehouse/newAttestationJobFromEnquiry.php&EnquiryId="+enquiryDetailsForm.getItemValue('AE_Id')+"&EnquiryFNa="+enquiryDetailsForm.getItemValue('AE_Name')+"&EnquiryMb="+enquiryDetailsForm.getItemValue('AE_Mobile')+"&EnquiryEml="+enquiryDetailsForm.getItemValue('AE_Email'))
                                    }).done(function(data) {

                                    if(data!='fail') {
                                        preTally.Track.applyFilter(TrackListEnquiryTlbr,"listTrackEnquiry",trackListEnquiryGrid);
                                        preTally.Track.TrackRegistrationTabUser(this,parseInt(data.trim()));
                                    }else {
                                        dhtmlx.message({text: "Sorry, Some error has occured."}); 
                                    }
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                    });

                                }
                            }
                        });
                        
                    }
                });
            });
            
            addFollowUpForm    = enquiryDetailsLayout.cells('b').attachForm();
            addFollowUpForm.loadStruct(preTally.Initialize.encryptURL("requisites/newEnquiryFollowup.php&"+params), function() {
                preTally.Settings.progressOff(true, dhxLayout,null);
                
                addFollowUpForm.attachEvent("onButtonClick", function(name){
                    if(name == 'newFollowupValidate'){
                        var newFollowup = addFollowUpForm.validate();
                        if(newFollowup){
                            preTally.Settings.progressOn(true, dhxLayout, null); 
                            addFollowUpForm.send(preTally.Initialize.encryptURL('warehouse/newEnquiryFollowup.php'), function(loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null); 
                                if(response != 'fail'){
                                    addFollowUpForm.clear();
                                    addFollowUpForm.resetValidateCss();
                                    addFollowUpForm.setItemValue("AE_Id", AEId);
                                    response = "Follow up details successfully added.";
//                                    listFollowUpGrid.clearAll();
                                    listFollowUpGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listFollowupDetails.php&"+params),function() { });
                                }else{                                    
                                    response = "Invalid Details.Please Re-Try.";
                                }
                                dhtmlx.message({text: response});
                            });
                        }
                    }else{
                        addFollowUpForm.resetValidateCss();
                        addFollowUpForm.clear();
                    }
                });
                
            }); 
            
            listFollowUpGrid    = enquiryDetailsLayout.cells('c').attachGrid();
            listFollowUpGrid.setMultiLine(true);
            listFollowUpGrid.loadXML(preTally.Initialize.encryptURL("requisites/listFollowupDetails.php&"+params),function() { 
                
            });           
        },
        //------------------------------ Listing Recent(in popup) And All Notifications ---------------------------------//       
        NotificationList: function() {
            $.ajax({
                url : preTally.Initialize.encryptURL("requisites/trackNotifiItems.php"),
            }).done(function(data) { 
                if (!NotifiPopupList) {
                    if(jQuery.isEmptyObject($.parseJSON(data))||$.parseJSON(data)=='fail'){
                        $("#trackNotification").css({
                            height: "24px",
                            width : "160px",
                        });
                        trackToolbar.setItemImage('trackNotification', 'trNotifi.png');  
                        NotifiPopupList = NotifiPopup.attachList("AJNB_Description",[{ AJNB_Description: "No Notification Found"}]); 
                    }else {
                        $("#trackNotification").css({
                            height  : "100px",
                            width   : "300px",
                            overflow: "scroll"
                        });
                        trackToolbar.setItemImage('trackNotification', 'NotifiAlert.png');  
                        NotifiPopupList = NotifiPopup.attachList("id,img,AJNB_Description,NO_DOC",$.parseJSON(data) ); 
                    }
                }
            });
        },
        listAllNotifications : function(inp){
           
            if(!trackConsoleSideBar.items("SB_list_notifications")) { //------ Check Jobs List Tab is alredy Open Or Not
                trackConsoleSideBar.addItem({ id:"SB_list_notifications"});
                trackConsoleSideBar.items("SB_list_notifications").setActive();
                var status_data_txt = ' <div class="tb_data_txt_secl">\
                                        <div class="status_cnt_totl"># : 0</div>\
                                    </div>';
                trackNotfGrid = trackConsoleSideBar.cells("SB_list_notifications").attachGrid();
                trackNotfGrid.enableSmartRendering(true,50);
                trackNotfGrid.attachHeader("#rspan,#rspan,#rspan,<select class = 'track_notf_SFF' id='itmNotifiStatuss' style='width:80px; font-size:8pt; font-family:Tahoma;'>\n\
                        <option value =''>All</option>\n\
                        <option value ='0'>New</option>\n\
                        <option value ='1'>Open</option>\n\
                    </select>");
                trackConsoleSideBar.cells("SB_list_notifications").attachStatusBar({
                    text:   status_data_txt,   
                    height: 23             
                });
                trackNotfGrid.load(preTally.Initialize.encryptURL("requisites/trackAllNotifications.php"), function() {
                    $('.status_cnt_totl').html("# : "+trackNotfGrid.getUserData("", "TL_Count")+" ");
                    trackNotfGrid.attachEvent("onRowSelect", function(id,ind,obj){
                        AJNBId=id;
                        preTally.Track.trackNotification(inp,id);
                    })
                    $( ".track_notf_SFF" ).change(function() { //------- filter based on Select Box
                       preTally.Track.applyFilter('NonDate',"trackAllNotifications",trackNotfGrid);
                    });

                });
                

            } else { //------ Check Jobs List Tab is alredy Open Tab become active state
                    trackNotfGrid.clearAll(true);
                    trackNotfGrid.attachHeader("#rspan,#rspan,#rspan,<select class = 'track_notf_SFF' id='itmNotifiStatuss' style='width:40px; font-size:8pt; font-family:Tahoma;'>\n\
                        <option value =''>All</option>\n\
                        <option value ='0'>New</option>\n\
                        <option value ='1'>Open</option>\n\
                    </select>");
                    trackNotfGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/trackAllNotifications.php"),function() {
                    $('.status_cnt_totl').html("# : "+trackNotfGrid.getUserData("", "TL_Count")+" ");
                    $( ".track_notf_SFF" ).change(function() { //------- filter based on Select Box
                       preTally.Track.applyFilter('NonDate',"trackAllNotifications",trackNotfGrid);
                    });
                    
                });
                trackConsoleSideBar.items("SB_list_notifications").setActive();
            }
        },
        trackNotification : function(inp,AJNBId) {
            dhxMessageWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var filtrInterval;
            NotifiWin = dhxMessageWin.createWindow("wins_Notifi", x, y, 1050, 350);
            NotifiWin.button("minmax1").hide();
            NotifiWin.button("minmax2").hide();
            NotifiWin.button("park").hide();
            NotifiWin.center();
            NotifiWin.setModal(true);
            NotifiWin.setText("Notification");
            trackListDocNotifiGrid = NotifiWin.attachGrid();
            trackListDocNotifiGrid.attachHeader("#rspan,<input type='text' class='track_notf_TF' id ='itmNotifiDoc' style='width: 90%;' placeholder='Document'>,#rspan,\n\
                    <input type='text' class='track_notf_TF' id ='itmNotifiCusName' style='width: 90%;' placeholder='Customer Name'>,\n\
                    <input type='text' class='track_notf_TF' id ='itmNotifiTrackId' style='width: 90%;' placeholder='Track ID'>,\n\
                    <input type='text' class='track_notf_TF' id ='itmNotifiCreateUser' style='width: 90%;' placeholder='Sender Branch'>,\n\
                    <select class = 'track_notf_SF' id='itmNotifiStatus' style='width:40px; font-size:8pt; font-family:Tahoma;'>\n\
                        <option value ='0'>All</option>\n\
                        <option value ='1'>New</option>\n\
                        <option value ='2'>Underprocess</option>\n\
                        <option value ='3'>Transit</option>\n\
                        <option value ='4'>All Process Completed</option>\n\\n\
                        <option value ='5'>Delivered</option>\n\
                    </select>,#rspan");
            trackListDocNotifiGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
            trackListDocNotifiGrid.enableAutoWidth(true);
            preTally.Settings.progressOn(true, NotifiWin, null); 
//            console.log(AJNBId);
            if(AJNBId != 'More') {
                var listRecipientsXML= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listTrackDocNotifi.php&BId="+AJNBId),function() {});
                trackListDocNotifiGrid.parse(listRecipientsXML.xmlDoc.responseText,function(){
                preTally.Settings.progressOff(true, NotifiWin, null);
                //------- filter based on text box 
                    $( ".track_notf_TF" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            preTally.Track.applyFilter('NonDate',"listTrackDocNotifi",trackListDocNotifiGrid); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    }); 
                    //------- filter based on Dorp  box 
                    $( ".track_notf_SF" ).change(function() { //------- filter based on Select Box
                        preTally.Track.applyFilter('NonDate',"listTrackDocNotifi",trackListDocNotifiGrid);
                    });
                });
                NotifiWin.attachEvent("onClose", function(win){
                        if( typeof trackNotfGrid != 'undefined' ) preTally.Track.applyFilter('NonDate',"trackAllNotifications",trackNotfGrid);
                        return true;
                });
                NotifiWin.attachStatusBar({
                    text  : "<input type='button' value='Receive Document' onclick='preTally.Track.receiveBranch("+AJNBId+");' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
            }else{
                 preTally.Track.listAllNotifications();
            }
            
        },
        receiveBranch :function (AJNBId){
            var AJDRD_Id = trackListDocNotifiGrid.getCheckedRows(7).split(",") ;
            if((AJDRD_Id['0']!=' ') && (AJDRD_Id['0']!=0)) {
                $.ajax({
                 url : preTally.Initialize.encryptURL("warehouse/trackNotification.php"),
                    type: 'POST',
                    data : {"AJDRD_Id" : AJDRD_Id,"BId":AJNBId},
                    success: function(data) { 
                        if(data != 'fail'){
                            dhxMessageWin.window("wins_Notifi").close();
                            dhtmlx.message({text: "Successfully Received"});
                        }else {
                            dhxMessageWin.window("wins_Notifi").close();
                            dhtmlx.message({text: "Sorry, Some error has occured."}); 
                        }
                    }
                });
                if( typeof trackNotfGrid != 'undefined' ) preTally.Track.applyFilter('NonDate',"trackAllNotifications",trackNotfGrid); 
            }
            else {
                dhtmlx.message({text: 'Empty Item Selected'});
            }
        },
        //------------------------------ Track Summary ---------------------------------// 
        trackSummary : function() {
            if(!trackConsoleSideBar.items("SB_trackSummary")) {
                trackConsoleSideBar.addItem({ id:"SB_trackSummary"});
                trackConsoleSideBar.items("SB_trackSummary").setActive();
                
                var trackSummary = trackConsoleSideBar.cells("SB_trackSummary").attachScheduler();
                $(".dhx_cal_tab").removeClass( "dhx_cal_tab_last" );
                $(".dhx_cal_navline").append('<div id="trackSummaryYearTab" class="dhx_cal_tab dhx_cal_tab_last active" name="year_tab" style="right: auto; left: 197px;">Year</div>')
                //trackSummary.locale.labels.year_tab ="Year";

                $(".dhx_cal_tab").click(function() {
                    $(".dhx_cal_tab").removeClass( "active" );
                    $(this).addClass( "active" );
                });
                $("#trackSummaryYearTab").click(function() {
                    $("#trackSummaryYearTab").addClass( "active" );
                    trackSummary.setCurrentView("","year");
                });
                trackSummary.setCurrentView("","year");
                trackSummary.config.xml_date = "%Y-%m-%d %H:%i";
                preTally.Settings.progressOn(true, dhxLayout, null);   
                trackSummary.load(preTally.Initialize.encryptURL("requisites/trackSummaryScheduler.php"), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

            } else 
                trackConsoleSideBar.items("SB_trackSummary").setActive();
        },
        jobTracking : function(inp,AJId){ 
//            trackToolbar.enableItem(lastOpenedTab);
            
            preTally.Settings.progressOn(true, dhxLayout, null); 
            if(!trackConsoleSideBar.items("SB_jobTracking")) {
                trackConsoleSideBar.addItem({ id:"SB_jobTracking"});
                trackConsoleSideBar.items("SB_jobTracking").setActive();
            }else{
                trackConsoleSideBar.cells("SB_jobTracking").remove();
                trackConsoleSideBar.addItem({ id:"SB_jobTracking"});
                trackConsoleSideBar.items("SB_jobTracking").setActive();
            }
                
            jobDetailsForm = trackConsoleSideBar.cells('SB_jobTracking').attachForm();

            var params = "AJId="+AJId ;
            jobDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/jobTracking.php&"+params), function() {
                preTally.Settings.progressOff(true, dhxLayout,null);
            });
        },
        showAutomateProcessComment : function(inp,rowId,formName){
            if(formName == 'Enq')
                var formObj = ATPOptionSelected;
            else if (formName == 'listDocs')
                var formObj = ATPOptionSelected;
            else
                var formObj = ATPOptions;
            
            if (!ATPCommentsPop) {
                ATPCommentsPop = new dhtmlXPopup({mode: "left"});   
            }
            
            if (ATPCommentsPop.isVisible()) {
                ATPCommentsPop.hide();
            } 
                
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;    
            ATPCommentsPop.show(x, y, w, h);
            
            var ATPCommentsPopForm = ATPCommentsPop.attachForm([
                {type: "fieldset", label: "<img src=\"images/icon/button-close.gif\" style=\"cursor:pointer;\" onclick=\"javascript:preTally.Track.hideAutomateProcessComment();\" />", inputWidth: 500, list:[
                    {type: "settings", position: "label-left", labelWidth: 0},
                    {type: "template", name: "notes", label: ":", value: formObj.getUserData(rowId,"AA_Comments")}
                ]}
                
            ]);
        },
        hideAutomateProcessComment : function(){
            if (ATPCommentsPop.isVisible()) {
                ATPCommentsPop.hide();
            } 
        },
        showDetailBackDateData : function(inp,APSId){
            if(!apsBackDateDetailsPop)
                apsBackDateDetailsPop = new dhtmlXPopup({mode: "right"});

                var x = getAbsoluteLeft(inp);
                var y = getAbsoluteTop(inp);
                var w = inp.offsetWidth;
                var h = inp.offsetHeight; 

                if (apsBackDateDetailsPop.isVisible()) {
                    apsBackDateDetailsPop.hide();
                }else{
                    apsBackDateDetailsPop.show(x,y,w,h);
                } 

                
                var APSPopLayout   = apsBackDateDetailsPop.attachLayout(750, 400, "1C");
                APSPopLayout.cells('a').hideHeader();
                APSBackDateListGrid = APSPopLayout.cells('a').attachGrid();
                APSBackDateListGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAPSBackDateDetails.php&APSID="+APSId),function() {
//                    ATPStatesLoad();
                    APSBackDateListGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                        if(stage == 2){
                            
                            if(nValue == '' && (cInd == 1|| cInd == 2 ) ){
                                return false;
                            }
                            
                            if (isNaN(nValue) && cInd != 1 && cInd != 2 ){ //&& nValue.toString().indexOf('.') != -1
                                return false;
                            }
//                            if(cInd == 2){
//                                console.log(APSBackDateListGrid.cells(rId,1).getValue() +"-------"+ APSBackDateListGrid.cells(rId,2).getValue());
//                                console.log(typeof new Date(APSBackDateListGrid.cells(rId,1).getValue()));
//                                if(APSBackDateListGrid.cells(rId,1).getValue() > APSBackDateListGrid.cells(rId,2).getValue()){
//                                    dhtmlx.message({text : 'Last Date Should be greater than First Date'});
//                                    return false;
//                                }
//                                    
//                            }
                        }
                        return true;
                    });
                });
                
                APSPopLayout.attachStatusBar({text: "<input type='button' value='SAVE' onclick='preTally.Track.BackDateEditSave();' />"});             
        },
        BackDateEditSave : function(){
            
            var backDateEditDetailsObj = {};
            var checkedRws =  APSBackDateListGrid.getCheckedRows(9);
            if(checkedRws == null || checkedRws == ''){
                dhtmlx.message({text : "No Rows Selected."});
            }else{
                
                APSBackDateListGrid.forEachRow(function(rId){
                    if(APSBackDateListGrid.cells(rId,9).getValue() == 1) {
                        var backDateEditDetails = {};
                        backDateEditDetails['APSE_Id']              = rId;
                        backDateEditDetails['APSE_FDate']           = APSBackDateListGrid.cells(rId,1).getValue();
                        backDateEditDetails['APSE_LDate']           = APSBackDateListGrid.cells(rId,2).getValue();
                        backDateEditDetails['APSE_StatutoryNAmt']   = APSBackDateListGrid.cells(rId,3).getValue();
                        backDateEditDetails['APSE_ExtraNAmt']       = APSBackDateListGrid.cells(rId,4).getValue();
                        backDateEditDetails['APSE_CourierNAmt']     = APSBackDateListGrid.cells(rId,5).getValue();
                        backDateEditDetails['APSE_TravellingNAmt']  = APSBackDateListGrid.cells(rId,6).getValue();
                        backDateEditDetails['APSE_ManpowerNAmt']    = APSBackDateListGrid.cells(rId,7).getValue();
                        backDateEditDetails['APSE_ServiceNAmt']     = APSBackDateListGrid.cells(rId,8).getValue();
                        backDateEditDetailsObj[rId] = backDateEditDetails;
                    }
                });
                
                $.ajax({
                    type    : "POST",
                    url     : preTally.Initialize.encryptURL('warehouse/attestationSubProcessBackDateEdit.php'),
                    data    : backDateEditDetailsObj
                }).done(function(data){
                    if(data != ''){
                        dhtmlx.message({text : data});
                    }
                });

            }
        },
        menuCalendar : function () {
            if (!winCalendar) {
                var winData = new Array();
                winData = {
                    'initialize': dhxWins,
                    'title': 'Widget || Calendar',
                    'id': 'widgetCalendar',
                    'X': 125,
                    'Y': 237,
                    'W': 228,
                    'H': 280
                };

                winCalendar = winData['initialize'].createWindow(winData['id'], winData['X'], winData['Y'], winData['W'], winData['H']);
                winCalendar.denyResize();
                winCalendar.setText(winData['title']);
                winCalendar.attachHTMLString('<div id="menuCalendar" />');
                var menuCalendar = new dhtmlXCalendarObject("menuCalendar");
                menuCalendar.show();

                winCalendar.attachEvent("onClose", function(win){ // To Close Calculator In Track
                    $( ".dhxsidebar_side_items .dhxsidebar_item:nth-child(11)" ).removeClass( "dhxsidebar_item_selected" );
                    winCalendar = '';
                    return true;
                });
            }
        }
    };
})(jQuery, this);
