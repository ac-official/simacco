;(function ($, window, undefined) {
    preTally.Reload = {
        reInitialize : function(id, last_id){ 
            
            var res = id.match(/edit_user_/);
            if(id==last_id)return;
            if(id == "menuNewDescription") {
                listDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listDescription.php"), true, true, function() { });
              
                var MHCombo = addDescriptionForm.getCombo("MH_Type");
                var SHCombo = addDescriptionForm.getCombo("SH_Id");
                var ITCombo = addDescriptionForm.getCombo("IT_Id");
                
                var MH_Id = MHCombo.getSelectedValue();
                var SH_Id = subheadCombo.getSelectedValue(); 
                var IT_Id = ITCombo.getSelectedValue();
               
                if(SH_Id == '') {var params="type=" + MH_Id ;}
                else {var params="SH_Id=" + SH_Id ; }                
                addDescriptionForm.reloadOptions("IT_Id",preTally.Initialize.encryptURL("requisites/items.php&"+ params));

                addDescriptionForm.setItemValue("MH_Type", MH_Type);
                addDescriptionForm.setItemValue("SH_Id", SH_Id);
                addDescriptionForm.setItemValue("IT_Id", IT_Id);
               
            } 
            if(id == "new_user") {                
                officeCombo[id] = new_user_form[id].getCombo("OF_Id");
                locateCombo[id] = new_user_form[id].getCombo("LC_Id");
                UTCombo[id] = new_user_form[id].getCombo("US_Type");
                DPCombo[id] = new_user_form[id].getCombo("DP_Id");
                DGCombo[id] = new_user_form[id].getCombo("DG_Id");
                SSCombo[id] = new_user_form[id].getCombo("SS_Id");
                        
                var offz_id = officeCombo[id].getSelectedValue();
                var LC_Id   = locateCombo[id].getSelectedValue();
                var US_Type = UTCombo[id].getSelectedValue();
                var DP_Id   = DPCombo[id].getSelectedValue();
                var DG_Id   = DGCombo[id].getSelectedValue();
                var SS_Id   = SSCombo[id].getSelectedValue();
                
                var params = "ofid=" + offz_id;                
                new_user_form[id].reloadOptions("LC_Id",preTally.Initialize.encryptURL("requisites/locations.php&" +params));
                new_user_form[id].reloadOptions("US_Type",preTally.Initialize.encryptURL("requisites/userTypes.php&" +params));
                new_user_form[id].reloadOptions("DP_Id",preTally.Initialize.encryptURL("requisites/departments.php&" +params));
                new_user_form[id].reloadOptions("DG_Id",preTally.Initialize.encryptURL("requisites/designations.php&" +params));
                new_user_form[id].reloadOptions("SS_Id",preTally.Initialize.encryptURL("requisites/salaryStructures.php&" +params));
                
                new_user_form[id].setItemValue("LC_Id", LC_Id);
                new_user_form[id].setItemValue("US_Type", US_Type);
                new_user_form[id].setItemValue("DP_Id", DP_Id);
                new_user_form[id].setItemValue("DG_Id", DG_Id);
                new_user_form[id].setItemValue("SS_Id", SS_Id);
            }
            if(res == "edit_user_"){                                                            
                officeCombo[id] = new_user_form[id].getCombo("OF_Id");
                locateCombo[id] = new_user_form[id].getCombo("LC_Id");
                UTCombo[id] = new_user_form[id].getCombo("US_Type");
                DPCombo[id] = new_user_form[id].getCombo("DP_Id");
                DGCombo[id] = new_user_form[id].getCombo("DG_Id");
                SSCombo[id] = new_user_form[id].getCombo("SS_Id");
                ESCombo[id] = new_user_form[id].getCombo("ES_Id");        
                var offz_id = officeCombo[id].getSelectedValue();
                var LC_Id   = locateCombo[id].getSelectedValue();
                var US_Type = UTCombo[id].getSelectedValue();
                var DP_Id   = DPCombo[id].getSelectedValue();
                var DG_Id   = DGCombo[id].getSelectedValue();
                var SS_Id   = SSCombo[id].getSelectedValue();
                var ES_Id   = ESCombo[id].getSelectedValue();
                var params = "ofid=" + offz_id;                
                new_user_form[id].reloadOptions("LC_Id",preTally.Initialize.encryptURL("requisites/locations.php&" +params));
                new_user_form[id].reloadOptions("US_Type",preTally.Initialize.encryptURL("requisites/userTypes.php&" +params));
                new_user_form[id].reloadOptions("DP_Id",preTally.Initialize.encryptURL("requisites/departments.php&" +params));
                new_user_form[id].reloadOptions("DG_Id",preTally.Initialize.encryptURL("requisites/designations.php&" +params));
                new_user_form[id].reloadOptions("SS_Id",preTally.Initialize.encryptURL("requisites/salaryStructures.php&" +params));
                new_user_form[id].reloadOptions("ES_Id",preTally.Initialize.encryptURL("requisites/employeestatus.php&" +params));
                
                new_user_form[id].setItemValue("LC_Id", LC_Id);
                new_user_form[id].setItemValue("US_Type", US_Type);
                new_user_form[id].setItemValue("DP_Id", DP_Id);
                new_user_form[id].setItemValue("DG_Id", DG_Id);
                new_user_form[id].setItemValue("SS_Id", SS_Id);
                new_user_form[id].setItemValue("ES_Id", ES_Id);
            }
            if(id == "menuNewBalSheet"){
                var MainCombo = dhxNewBalSheetForm.getCombo("MH_Type");
                var MH_Type   = MainCombo.getSelectedValue();
                var params="type="+MH_Type;
                dhxNewBalSheetForm.reloadOptions("IT_Id",preTally.Initialize.encryptURL("requisites/newBSItems.php&"+ params));
                dhxGridBalSheet.loadXML(preTally.Initialize.encryptURL("requisites/listBSItems.php"));
            }
            if(id == "menuNewBalSheetSample"){
                var MainCombo = dhxNewBalSheetFormSamp.getCombo("MH_Type");
                var MH_Type   = MainCombo.getSelectedValue();
                var params="type="+MH_Type;
                dhxNewBalSheetFormSamp.reloadOptions("IT_Id",preTally.Initialize.encryptURL("requisites/newBSItemsSample.php&"+ params));
                dhxGridBalSheet.loadXML(preTally.Initialize.encryptURL("requisites/listBSItems.php"));
            }
            if(id == "viewReports"){ 
               // preTally.Reports.filterReport(ptyRprtTree.getSelectedItemId());
               preTally.Reports.reLoadSHGrid()
            }
            if(id == "view_cashbsreports"){ 
               // preTally.CashBalanceSheet.filterReport(ofzRprtTree.getSelectedItemId());
               preTally.CashBalanceSheet.reLoadSHGrid()
            }
            if(id == "view_businessreports"){ 
               // preTally.BusinessReport.filterReport(ofzBusinessRprtTree.getSelectedItemId());
               preTally.BusinessReport.reLoadSHGrid()
            }
            if(id == "view_bankbsreports"){ 
              //  preTally.BankBalanceSheet.filterReport(ofzBankRprtTree.getSelectedItemId());
              preTally.BankBalanceSheet.reLoadSHGrid()
            }
            if(id == "view_stockreports"){ 
              //  preTally.StockReport.filterReport(ofzStockRprtTree.getSelectedItemId());
              preTally.StockReport.reLoadSHGrid()
            }
            if(id == "view_branchbsreports") {
              //  preTally.BranchBSReports.filterReport(ofzBranchRprtTree.getSelectedItemId());
              preTally.BranchBSReports.reLoadSHGrid()
            }
            if(id == "view_ExpenseControl") {
                preTally.ExpenseControl.applyExpCntrlFilter();
            }
            if(id == "view_pettycashreports") {
                var actvId = pettyCashRptTabbar.getActiveTab();
                preTally.PettyCash.applyFilter(actvId);
            }
            if(id == "view_mywallet") {
                var actvId = myWalletRptTabbar.getActiveTab();
                preTally.MyWallet.applyFilter(actvId);
            }
            
//            if(id == "menuListCustomItem"){ 
//                listCustomItemGrid.updateFromXML("requisites/listCustomItems.php", true, true);
//            }
            if(id == "menuListEditCustomItem"){ 
                
                var filterValue = new Array($('#ieCF').val(), $('#itmCF').val(), $('#decCF').val(), $('#shCF').val(), $('#mhCF').val(), $('#addCF').val(), $('#brnCF').val(),  $('#aprCF').val(), $('#stCF').val());
                        
                listEditCustomItemGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);

                listEditCustomItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listEditCustomItems.php&filter="+filterValue), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                listEditCustomItemGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listEditCustomItems.php"), true, true, function(){
//                    preTally.Settings.progressOff(true, dhxLayout, null);
//                });
            }
            if(id == "menuNewCompanyItem"){ 
                var MainCombo = addCompanyItemForm.getCombo("MH_Type");
                var MH_Type   = MainCombo.getSelectedValue();
                var SH_Id     = SH_CMpnyCombo.getSelectedValue();
                var params="type="+MH_Type;
                addCompanyItemForm.reloadOptions("SH_Id",preTally.Initialize.encryptURL("requisites/subheads.php&" +params));
                addCompanyItemForm.setItemValue("SH_Id", SH_Id);
            }
            if(id== "menuNewBnkAccount"){
                var BABnkBrhCombo = addBnkAccountForm.getCombo("BB_Id");                 
                var BA_Id   = BABnkBrhCombo.getSelectedValue();
                var params="type="+BA_Id;
                addBnkAccountForm.clear();
                addBnkAccountForm.setItemValue("BA_Id","0");
                addBnkAccountForm.reloadOptions("LC_Id",preTally.Initialize.encryptURL("requisites/locations.php"));   
                addBnkAccountForm.reloadOptions("BB_Id",preTally.Initialize.encryptURL("requisites/bankbranch.php&"+ params));                
                listBnkAccountGrid.enableTooltips("false,false,false,false");          
                preTally.Settings.clearGridFilters(listBnkAccountGrid);
                listBnkAccountGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBnkAccount.php"),function(){
                    
                });
            }
            if(id== "menuNewBnkBranch"){
                var BnkCombo = addBnkBranchForm.getCombo("BNK_Id");                 
                var BNK_Id   = BnkCombo.getSelectedValue();
                var params="type="+BNK_Id;
                addBnkBranchForm.clear();
                addBnkBranchForm.setItemValue("BB_Id","0");
                addBnkBranchForm.reloadOptions("BNK_Id",preTally.Initialize.encryptURL("requisites/banks.php&"+ params));
                preTally.Settings.clearGridFilters(listBnkBranchGrid);                
                listBnkBranchGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBnkBranch.php"));
            }
            if(id== "menuNewCheque"){
                var BnkAccCombo = addChequeForm.getCombo("BA_Id");                 
                var BA_Id   = BnkAccCombo.getSelectedValue();
                addChequeForm.clear();
                addChequeForm.setItemValue("CHQ_Id","0");
                var params="type="+BA_Id;
                addChequeForm.reloadOptions("BA_Id",preTally.Initialize.encryptURL("requisites/bankaccounts.php&"+ params));
                listChequeGrid.enableTooltips("false,false,false,false,false");
                preTally.Settings.clearGridFilters(listChequeGrid);
                listChequeGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listCheque.php"));
            }
                        
            if(id== "menuOpenBalance"){               
                listOpenBalanceGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBrnchOpnBal.php"),true,true);
            }
            if(id== "menuBnkOpenBalance"){                
                listBnkBalanceGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBnkOpnBal.php"),true,true);
            }
            var ACLAttendView=unescape(JGG1P3bDnUSDL19Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            var ACLAttendEdit=unescape(JGG1P3bDnUSDL14Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            if(id== "menulistAttendance" && (ACLAttendView==1 || ACLAttendEdit==1)){   
              /*  var tmpDate = new Date();
                var dateToday = Date.today().toString("dd.MM.yyyy");
                listAttendanceToolbar.setValue('att_date', dateToday, false);             
                AttendGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendanceUsers.php"),function(){                               
                        $('.Att_count').html("Total:"+AttendGrid.getRowsNum());
                        $('.att_rpt_mark').html("Marked: " + AttendGrid.getUserData("", "att_marked") + " ");
                        $('.att_rpt_unmark').html("Unmarked: " + AttendGrid.getUserData("", "att_unmarked") + " ");
                        $('.att_rpt_norm').html("Signed In : " + AttendGrid.getUserData("", "att_signedin") + " ");
                        $('.att_rpt_late').html("Late SignIn: " + AttendGrid.getUserData("", "att_latein") + " ");
                        $('.att_rpt_early').html("Early SignOut : " + AttendGrid.getUserData("", "att_earlyout") + " ");
                        $('.att_lvapplied').html("Leave Applied : " + AttendGrid.getUserData("", "att_lvapplied") + " ");
                        $('.att_lvrptd').html("Leave Reported : " + AttendGrid.getUserData("", "att_lvrptd") + " ");
                        $('.att_invalid').html("Not Signed Out : " + AttendGrid.getUserData("", "att_invalid") + " ");
                        $('.att_ntmarked').html("Not Marked : " + AttendGrid.getUserData("", "att_ntmarked") + " ");
                });            */   
            }
//            if(id=="adminBugReports"){                
//                BugGrid.clearAndLoad(preTally.Initialize.encryptURL('requisites/listBugs.php'));
//                BugGrid.refreshFilters();
//            }
            if(id=="manageChequeLeaves"){
                listChqBooks.updateFromXML(preTally.Initialize.encryptURL("requisites/listChequeDetails.php"),true,true);
                listChqBooks.refreshFilters();
                listChqLeaves.clearAll();
                if(listChqLeaves.getFilterElement(1)) {
                    listChqLeaves.getFilterElement(1).value="";
                    listChqLeaves.getFilterElement(2).value="";
                    listChqLeaves.getFilterElement(4).value="";
                    listChqLeaves.getFilterElement(5).value="";
                    $('#CkStatus option[value!=""]').remove();
                }
            }
            if(id=="menuMyAttendance"){
                myLeaveGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/myleavegrid.php"),true,true);
            }
            if(id=="ApproveLeave"){
                 //ApproveLeaveGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listApproveLeave.php&"+params),true,true)                        
            }
            if(id=="list_track_jobs") {
                if(typeof TrckListTlbr == 'undefined') { 
                    preTally.Track.ListTrackJobs();
                    preTally.Track.applyFilter(TrckListTlbr,"listTrackJobs",trackListJobGrid);
                } else {
                    var filterValue = '&f='+TrckListTlbr.getValue("srch_date_from")+'&t='+TrckListTlbr.getValue("srch_date_till");
                    filterValue += '&filter='+ new Array($('#itmTrkName').val()+"--"+$('#itmTrkTrack').val()+"--"+$('#itmTrkTtlAmount').val()+"--"+$('#itmTrkCrUS').val()+"--"+$('#itmTrkCrBrh').val()+"--"+$( "#itmTrkSatus" ).val());
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    trackListJobGrid.loadXML(preTally.Initialize.encryptURL("requisites/listTrackJobs.php"+filterValue),function() {
                        $('.tb_cnt_tot').html("# : "+trackListJobGrid.getUserData("", "TL_Count")+" ");
                        if(last_id != 'track_dashboard')
                            preTally.Track.disableEnableToolBarItems('list_track_jobs');
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                }
            }
            if(id == "viewMasterReports"){                
                dhxMiddleBlockTabs.tabs("viewMasterReports").setActive();
                var actvId = ptMasterReportsTabbar.getActiveTab();
                if(actvId =='viewBranchReports') {//actvId =='viewItemReports' || 
                    dhxAccord.cells("a4").show();
                    dhxAccord.cells("a4").open();                                 
                } else {
                    dhxAccord.cells("a4").hide();
                    dhxAccord.cells("a1").open();  
                }
//                if(actvId == 'viewReportsVisual') preTally.MasterReports.viewReportsVisual();
//                if(actvId == 'viewItemReports')   preTally.MasterReports.viewItemReports();
                if(actvId == 'viewBranchReports') preTally.MasterReports.viewBranchReports();
                return true;
            }
       
        }
    }
})(jQuery, this);