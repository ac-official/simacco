;var preTally 			= {};
;var dhxLayout;                 //2U Layout attached to the body of document defined in pretally.initialize.initMainLayout
;var dhxWins;                   //Window attached to the body of document defined in pretally.initialize.initWindow
;var dhxAccord;                 //Accordion attached to the cell b of dhxLayout defined in pretally.initialize.initMenuAccord
;var dhxACLTree;                //checked not found
;var dhxToolbar;                //Toolbar attached to the dhxLayout defined in pretally.initialize.initMainToolbar
;var dhxStatusBar;              //status bar attached to the dhxLayout defined in pretally.initialize.initStatusBar
;var dhxMiddleBlockTabs;        //Tabbar attached to cell a of the dhxLayout defined in pretally.initialize.initMainLayout
;var ptBalSheetTab;             //checked not found
;var ptPayrollAttendence;       //Grid attached to cell menuPayroll of dhxMiddleBlockTabs defined in preTally.Settings.menuPayroll
;var dhxMiddleBlockLayout;      //3L Layout attached to the cell menuNewBalSheet of dhxMiddleBlockTabs defined in preTally.BalanceSheet.menuNewBalSheet
;var dhxRightBlockLayout;       //checked not found
;var dhxNewBalSheetForm;        //Form attached to cell b of dhxMiddleBlockLayout  defined in preTally.BalanceSheet.menuNewBalSheet

;var prtNewCombo;               //Item name combo defined in preTally.Balancesheet.saveBalanceSheetItem

;var dhxCompleteBalSheet;       //Checked not found
;var dhxItemBalSheet;           //Checked not found
;var dhxBalSheetDetailForm;     //Checked not found
;var dhxBalSheetItemForm;       //Checked not found

;var toolTipPop;                //Popup defined in preTally.script.showTooltip
;var spotterFlag		= new Array;//ask
;var currentMousePos	= { X: -1, Y: -1, W: 0, H: 0 };
;var preTallyACL;                   //checked not found

;var AF_Particulars;                //checked not found
;var AF_Users;                      //checked not found
;var dhxRightBlockCalendar;         //checked not found
;var currentTimeString;             //Formatted time
;var currentDateString;             //Formatted date

;var uploadPath;                    //Upload path defined in preTally.UserProfile.menuNewUser
;var uploadSrc;                     //ask
;var uploadForm;                    //Form attached to the dhxMiddleBlockTabs defined in preTally.UserProfile.menuNewUser function
;var uploadField;                   //ask

;var listPreTallyUser;              //Grid attached to dhxMiddleBlockTabs of cell list_user defined in preTally.UserProfile.menuListUser function
;var ptPayrollToolbar;              //Toolbar attached to dhxMiddleBlockTabs of cell menuPayroll defined in preTally.Settings.menuPayroll function
;var ptPayrollToolbarCalendarPop;   //dhtmlXPopup attached to ptPayrollToolbar defined in preTally.Settings.menuPayroll function

;var ptNewUserToolbar=[];           //Toolbar attached to cell userModeForm of dhxMiddleBlockTabs defined in preTally.UserProfile.menuNewUser
;var new_user_form = [];            //Form attached the index userModeForm of the new_user_form array defined in preTally.UserProfile.menuNewUser
;var officeCombo=[];                //Combo for listing office names in newuserform
;var locateCombo =[];               //Combo for listing branch names in newuserform
;var UTCombo =[];                   //Combo for listing usertypes/acl in newuserform                
;var DPCombo =[];                   //Combo for listing departments in newuserform
;var DGCombo =[];                   //Combo for listing designations in newuserform
;var ESCombo =[];                   //Combo for listing employee status in newuserform
;var US_ReportCombo=[];             //Combo for listing reporting user in newuserform
;var SSCombo =[];                  //Combo for listing salary structure in newuserform
;var loginHrsCombo=[];             //Combo for listing login time hour
;var loginMinsCombo=[];             //Combo for listing login time minute
;var logoutHrsCombo=[];             //Combo for listing logout time hour
;var logoutMinsCombo=[];            //Combo for listing logout time minute
;var addDesignationForm;            //Form attached to cell a of menuDesignationLayout menuNewDesignation defined in preTally.Settings.menuNewDesignation function
;var listDesignationGrid;           //Grid attached to cell b of menuDesignationLayout defined in preTally.Settings.menuNewDesignation function


;var addLocationForm;               //Form attached to ath cell of menuLocationLayout  defined in preTally.Settings.functionmenuNewLocation 
;var listLocationGrid;              //Grid attached to cell b of menuLocationLayout defined in preTally.Settings.functionmenuNewLocation 

;var holidayForm;                   //Form attached to cell a of holidayForm dhxHolidayLayout defined in preTally.Settings.menuHolidays function .
;var statecombo;                    //Combo  in the holidayForm lists states which defined in preTally.Settings.menuHolidays



;var addItemForm;                   //Form attached to ath cell of menuItemLayout defined in preTally.BalanceSheet.menuNewItem 
;var listDescNoftGrid;              //not found checked

;var dhxNotificationTab             //Tabbar attached to cell a of dhxNotificationLayout defined in preTally.Notification.viewNotification
;var listItemNoftGrid;              //Grid attached to dhxNotificationTab of cell a1 defined in preTally.Notification.viewNotification
;var listPendingSHGrid;             //Grid attached to dhxNotificationTab of a2 defined in preTally.Notification.viewNotification
;var listPretallyItemGrid;          //Grid attached to dhxMiddleBlockTabs of cell menuListPretallyItem defined in preTally.Item.menuListPretallyItem
//;var listCustomItemGrid;
;var listEditCustomItemGrid;        //Grid attached to dhxMiddleBlockTabs of cell menuListEditCustomItem defined in preTally.Item.menuListEditCustomItem
;var DS_Pop;                        //checked not found
;var dsPosition;                    //checked not found
;var dsCount;                       //checked not found

;var dhxAccHeadTree;                //checked not found

;var updateBS = [];                 //Balance sheet item details .Defined in  preTally.BalanceSheet.editBalSheetItems
;var off_name;                      //office name attached to dhxStatusBar defined in preTally.initialise.initStatusBar
;var loc_name;                      //location attached to dhxStatusBar defined in preTally.initialise.initStatusBar

;var access_level;                  //2U layout attached to dhxMiddleBlockTabs of cell access_level defined in preTally.Settings.menuAccessLevelUser function
;var listACLGrid;                   //Grid attached to access_level cells of b defined in preTally.Settings.menuAccessLevelUser function
;var manageACLForm;                 //Form attached to access_level cells of a defined in preTally.Settings.menuAccessLevelUser function

;var BalSheetCount;                 //checked not found
;var dhxSubGridBalSheet = [];       //Details of balancesheet data defined in preTally.BalanceSheet.onRowBalSheet
;var subGridObj;                    //checked not found

;var swpWindowObj;                  //window object defined in preTally.Notification.mapDesc
;var swpWindowForm;                 //Form attached to the swpWindowObj defined in preTally.Notification.mapDesc
;var descCombo;                     //Combo in swpWindowForm defined in preTally.Notification.mapDesc
;var subGridState = [];             //checked not found
;var balSheetForm = [];             //checked not found                 
;var dhxToolbarCalendarPop;         //checked not found
;var menuCalendar;                  //Tabbar in the dhxMiddleBlockTabs defined in preTally.Settings.menuCalendar

;var LocConCombo = [];              //Combo contains country id attached to the form new_user_form defined in preTally.UserProfile.menuNewUser
;var CmpBankACCombo = [];           //checked not found
;var LocSteCombo = [];              //Combo contain state id attached to the form new_user_form defined in preTally.UserProfile.menuNewUser
;var LocCtyCombo = [];              //Combo contain city id attached to the form new_user_form defined in preTally.UserProfile.menuNewUser
;var SPCombo = [];                  //Combo contains salary paymode  id attached to the form new_user_form defined in preTally.UserProfile.menuNewUser 
;var payable = [];                  //Contain payable details.Defined in preTally.UserProfile.filterUserForm
;var US_ReportCombo ;               //combo attached to the new_user_form of index userModeForm defined in preTally.UserProfile.menuNewUser
;var SH_Combo;                      //combo attached in the addItemForm defined in preTally.BalanceSheet.menuNewItem
;var addDescriptionForm;            //Form attached to ath cell of menuDescriptionLayout defined in preTally.BalanceSheet.menuNewDescription
;var subheadCombo;                  //Combo defined in addDescriptionForm defined in in preTally.BalanceSheet.menuNewDescription
;var DS_ItmCombo;                   //Combo defined in addDescriptionForm defined in in preTally.BalanceSheet.menuNewDescription
;var MHCombo;                       //Combo defined in addDescriptionForm defined in in preTally.BalanceSheet.menuNewDescription
;var SH_CMpnyCombo;                 //checked not found
;var addOfzItemForm;                //Form attached to menuOfzItemLayout at cell position a defined in preTally.Item.menuNewOfzItem
;var SH_OfzCombo;                   //combo attached to addOfzItemForm defined in preTally.Item.menuNewOfzItem

;var addCityForm;                    //Form attached to menuCityLayout at cell a, defined in pretally.settings.menuNewCity.
;var LocSteComboCty;                 //Combo attached to addCityForm defined in pretally.settings.menuNewCity.

;var LocCNId;                       //checked not found
;var LocSTId;                       //Contains state id from listLocationGrid defined in preTally.Settings.editLocation 
;var LocCTId;                       //Contains state id from listLocationGrid defined in preTally.Settings.editLocation 

;var addSearchForm;                 //Form attached to cell a of menuSearchLayout defined in preTally.Settings.searchAccount
;var locateCombo;                   //Combo attached to addSearchForm contains location details defined in preTally.Settings.searchAccount
;var officeCombo;                   //Combo attached to addSearchForm contains office details defined in preTally.Settings.searchAccount
;var reportCombo;                   //checked not found
;var edtflag;                       //checked not found
;var ed;                            //checked not found
;var eds;                           //checked not fiund
;var edof;                          //checked not found
;var edloc;                         //checked not found
;var addSalStructForm;              //Form attached to cell a of menuSalStructLayout defined in preTall.Settings.menuNewSalStruct
;var basic_per;                     //Basic salary in addSalStructForm defined in preTall.Settings.menuNewSalStruct 
;var da_per;                        //DA allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct  
;var hra_per;                       //HRA allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct  
;var convey_per;                    //CA allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct   
;var edu_per;                       //Educational allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct  
;var medi_per;                      //Medical allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct   
;var misc_per;                      //Misc allowance in addSalStructForm defined in preTall.Settings.menuNewSalStruct   
;var custom_flag;                   //ask
;var sal_flag=0;                    //indicating misc salary is negative.             
;var Struct_array=new Object();     //salary structure details defined in preTally.UserProfile.menuNewUser

;var dhxVislnLayout;                //2E Layout attached to the dhxAccord at cell a1 defined in preTally.Initialize.initMainMenu function
;var dhxVislnIncChart;              //chart attached to the ath cell of dhxVislnLayout defined in preTally.Initialize.initMainMenu function
;var dhxVislnExpChart;              //chat attached to bth cell of dhxVislnLayout defined in preTally.Initialize.initMainMenu function

;var preTallyPopUp;                 //checked not found
;var Cheq_DetailPop;                //popUp showing check details defined in preTally.Settings.chqDetailsPopUp
;var BnkCombo;                      //Combo contains bank  details
;var BnkBrhCombo;                   //Combo contain bank branch details 
;var BnkAccCombo;                   //Combo contain account number details
;var UserCombo;                     //Combo contain user details
;var LCCombo;                       //combo contain location details
;var StaffCombo;                    //combo contain staff details                   
;var BnkChqCombo;                   //Combo contain bank check details

;var MainReportInit;                //flag 

;var dhxReportsLayout;              //2U layout attached to cell viewReports of dhxMiddleBlockTabs defined in preTally.Report.viewReports.
;var ptReportToolbar;               //Toolbar attached to cell b of dhxReportsLayout defined in preTally.Report.viewReports.
;var ptReportTBIEData;              //checked not found
;var ptRpTb_Calendar;               //init calendar
;var dhxReportChart;                //not in use
;var ptReportsTabbar;               //Tabbar attached to cell b of dhxReportsLayout defined in preTally.Report.viewReports.
;var dhxReportsGrid;                //Grid attached to cell a2 of ptReportsTabbar defined in preTally.Report.viewReports.

;var dhxReportChartLayout;          //checked not found
;var reportSubHeadChart;            //not in use
;var ptReportsToolbar;              //not in use
;var chartIE_Response_Inc;          //Income details in the  defined in preTally.Reports.filterReport.
;var chartIE_Response_Exp;          //expence details in the  defined in preTally.Reports.filterReport.
;var reportDetailsGrid;             //Grid attached to cell a4 of dhxAccord defined in preTally.Reports.reLoadGraph.
;var IE_DetailDataPop;              //Popup showing detailed Income and Expence data of defined in preTally.Reports.showDetailData.  
;var rptFilterID;                   //id of the user based on this filtering is done defined in preTally.Reports.filterReport.
;var chartOtherDetailsGrid;         //Grid attached to dhxWins, having id chartOtherDetails defined in preTally.Reports.chartOtherDetails.
;var chartFilterParams;             //Contains data used to filter. Defined in preTally.Reports.filterReport.
;var labelPop;                      //PopUp defined in preTally.Settings.showLabel.

;var listItemGrid;                  //Grid for item  attached to cell b of menuItemLayout defined in preTally.BalanceSheet.menuNewItem.
;var mapItemSubGrid;                //not in use
;var mapItemCustomSubGrid;          //not in use
;var rptFilterPopUp;                //Checked not found
;var mapIdRptz;                     //Set value based on selected option .Defined in preTally.Reports.viewReports.
;var RP_Ids;                        //Id of employees under selected reporting person defined in preTally.Report.filterReport.

;var reportBusinessInit;            //flag 
;var dhxBusinessReportsLayout;       //2U layout attached to cell view_businessreports of dhxMiddleBlockTabs defined in preTally.BusinessReport.view_BusinessReports.
;var ofzBusinessRprtTree;           //Tree attached to cell a of dhxBusinessReportsLayout defined in preTally.BusinessReport.view_BusinessReports.
;var dhxBusinessRprtTlbr;            //Toolbar attached to cell a of dhxBusinessReportsLayout defined in preTally.BusinessReport.view_BusinessReports.
;var ptBusinessRprtTlbr;            //Toolbar attached to cell b of dhxBusinessReportsLayout defined in preTally.BusinessReport.view_BusinessReports.
;var ptBusinessRprtsTabbar;         //Tabbar attached to cell b of dhxBusinessReportsLayout defined in preTally.BusinessReport.view_BusinessReports.
;var rptBusinessFilterID;           //Id of the office or branch or user defined in preTally.BussinessReport.filterReport.
;var BusinessReport_DetailDataPop;  //PopUp showing detailed business report data defined in preTally.BusinessReport.showDetailData. 
;var dhxBusinessReportsGrid;        //Grid attached to cell a2 of ptBusinessRprtsTabbar defined in preTally.BusinessReport.view_BusinessReports.
;var dhxBusinessReportDetailLayout; //3U layout attached to cell a3 of ptBusinessRprtsTabbar defined in preTally.BusinessReport.view_BusinessReports.
;var dhxBusinessReportChart;        //Chart attached to cell b of dhxBusinessReportDetailLayout defined in preTally.BusinessReport.view_BusinessReports. 
;var dhxBusinessReportChartLayout;  //checked not found
;var reportBusinessSubHeadChart;    // Chart attached to cell c of dhxBusinessReportDetailLayout defined in preTally.BusinessReport.view_BusinessReports.  
;var reportBusinessDetailsGrid;     //checked not found
;var chartBusiness_Response_OB;     //Income details defined in preTally.BusinessReport.reLoadSHGrid.
;var chartBusiness_Response_CB;     //Expence details defined in preTally.BusinessReport.reLoadSHGrid.
;var chartBusinessFilterParams;     //Contains data used to filter. Defined in preTally.BusinessReport.filterReport.

;var reportStockInit;               //flag   
;var reportTrackDupInit             //flag  
;var sortVal                        //flag  
;var dhxStockReportsLayout;         //2U layout attached to cell view_stockreports of dhxMiddleBlockTabs defined in preTally.StockReport.view_StockReports.
;var ofzStockRprtTree;              //Tree attached to cell a of dhxStockReportsLayout defined in preTally.StockReport.view_StockReports. 
;var dhxStockRprtTlbr;              //Toolbar attached to cell a of dhxStockReportsLayout defined in preTally.StockReport.view_StockReports.
;var ptStockRprtTlbr;               //Toolbar attached to cell b of dhxStockReportsLayout defined in preTally.StockReport.view_StockReports.
;var ptStockRprtsTabbar;            //Tabbar attached to cell b of dhxStockReportsLayout defined in preTally.StockReport.view_StockReports.          
;var rptStockFilterID;              //Id of the office or branch or user defined in preTally.StockReport.filterReport.
;var StockReport_DetailDataPop;     //Popup showing detailed ralated to  stock information defined in preTally.StockReport.showDetailData. 
;var TrackDupReports_DetailDataPop; //Popup showing detailed ralated to  track duplicate information defined in preTally.TrackDupReports.showDetailData. 
;var rptDetailsWin;                 //Window showing details of item defined in preTally.StockReport.showDetailData.
;var dhxStockReportsGrid;           //Grid attached to a2 cell of ptStockRprtsTabbar defined in preTally.StockReport.view_StockReports.   
;var dhxStockReportDetailLayout;    //3U layout attached to cell a3 of ptStockRprtsTabbar defined in preTally.StockReport.view_StockReports.    
;var dhxStockReportChart;           //chart attached to cell b of  dhxStockReportDetailLayout defined in preTally.StockReport.view_StockReports.           
;var dhxStockReportChartLayout;     //checked not found
;var reportStockSubHeadChart;       //Chart attached to cell c of dhxStockReportDetailLayout defined in preTally.StockReport.view_StockReports.
;var reportStockDetailsGrid;        //Checked not found.
;var chartStock_Response_OB;        //Income details defined in preTally.StockReport.reLoadSHGrid.
;var chartStock_Response_CB;        //Expence details defined in preTally.StockReport.reLoadSHGrid.
;var chartStockFilterParams;        //Contains data used to filter. Defined in preTally.StockReport.filterReport.

;var reportCashBSInit;              //flag     
;var dhxCashOBReportsLayout;        //2U layout ttached to the cell id view_cashbsreports of dhxMiddleBlockTabs defined in preTally.CashBalanceSheet.view_CashBSReports. 
;var ofzRprtTree;                   //Office Tree attached to cell a of dhxCashOBReportsLayout defined in preTally.CashBalanceSheet.view_CashBSReports .
;var dhxCashBSRprtTlbr;             //Toolbar attached to cell a of dhxCashOBReportsLayout  defined in preTally.CashBalanceSheet.view_CashBSReports. 
;var ptCashBSRprtTlbr;              //Toolbar attached to cell b of dhxCashOBReportsLayout  defined in preTally.CashBalanceSheet.view_CashBSReports. 
;var ptCashBSRprtsTabbar;           //Tabbar attached to cell b of dhxCashOBReportsLayout  defined in preTally.CashBalanceSheet.view_CashBSReports. 
;var rptCashBSFilterID;             //Id of office or branch or user defined in preTally.CashBalanceSheet.filterReport.
;var CashBS_DetailDataPop;          //popUp window defined in preTally.CashBalanceSheet.showDetailData.
;var dhxCashBSReportsGrid;          //Grid attached to cell a2 of ptCashBSRprtsTabbar preTally.CashBalanceSheet.view_CashBSReports.
;var dhxCashBSReportChart;          //not in use      
;var dhxCashBSReportChartLayout;    //checked not found    
;var reportCashBSSubHeadChart;      //not in use
;var reportCashBSDetailsGrid;       //checked not found
;var chartCashBS_Response_OB;       //Income details defined in preTally.CashBalanceSheet.reLoadSHGrid.
;var chartCashBS_Response_CB;       //Expence details defined in preTally.CashBalanceSheet.reLoadSHGrid.
;var chartCashBSFilterParams;       //Contains data used to filter. Defined in preTally.CashBalanceSheet.filterReport.

;var reportBankBSInit;              //flag
;var dhxBankOBReportsLayout;        //2U layout attach to cell view_bankbsreports of dhxMiddleBlockTabs defined in preTally.BankBalanceSheet.view_BankBSReports.
;var ofzBankRprtTree;               //Tree attached to cell a of dhxBankOBReportsLayout defined in preTally.BankBalanceSheet.view_BankBSReports.  
;var dhxBankBSTreeTlbr;             //Toolbar attached to cell a of dhxBankOBReportsLayout defined in preTally.BankBalanceSheet.view_BankBSReports. 
;var dhxBankBSRprtTlbr;             //Toolbar attached to cell b of dhxBankOBReportsLayout defined in preTally.BankBalanceSheet.view_BankBSReports. 
;var ptBankBSRprtsTabbar;           //Tabbar attached to cell b of dhxBankOBReportsLayout defined in preTally.BankBalanceSheet.view_BankBSReports.  
;var rptBankBSFilterID;             //Id of the office or branch or user defined in preTally.BankBalanceSheet.filterReport.
;var BankBS_DetailDataPop;          //PopUp window defined in preTally.BankBalanceSheet.showDetailData.
;var MyBank_DetailDataPop;          //PopUp window defined in preTally.MyBankBook.showDetailData.
;var dhxBankBSReportsGrid;          //Grid attached to cell a2 of ptBankBSRprtsTabbar defined in preTally.BankBalanceSheet.view_BankBSReports. 
;var dhxBankBSReportChart;          //not in use
;var dhxBankBSReportChartLayout;    //checked not found
;var reportBankBSSubHeadChart;      //not in use
;var chartBankBS_Response_OB;       //Income details defined in preTally.BankBalanceSheet.reLoadSHGrid.
;var chartBankBS_Response_CB;       //Expence details defined in preTally.BankBalanceSheet.reLoadSHGrid.
;var chartBankBSFilterParams;       //Contains data used to filter. Defined in preTally.BankBalanceSheet.filterReport.

;var reportMyBankBSInit;            //flag
;var BranchBS_DetailDataPop;        //not in use

;var ptOffzDept;                    //checked not found
;var ptyRprtTree;                   //Tree attached to cell a of dhxReportsLayout defined in preTally.Reports.viewReports.
;var dhxRprtTlbr;                   //Toolbar attached to cell a of dhxReportsLayout defined in preTally.Reports.viewReports.

;var myWallet;                      //not in use
;var myWalletTlbr;                  //not in use
;var myWalletPath = '';             //not in use
;var myWalletPathItem = [];         //checked not found
;var mwpiCount = 0;                 //checked not found
;var walletCMenu;                   //not in use
;var walletItemCMenu;               //not in use
;var folderMenuFlag = 0;            //not in use

;var mapsubGridItemOpen = [];       //Not in use
;var mapsubGridDesgOpen = [];       //checked not found
;var mapsubGridDeptOpen = [];       //checked not found

;var mapItemArray = [];             //checked not found
;var DSId;                          //Not in use

;var managePatternForm;             //Form attached to cell a of menuManagePatternLayout defined in preTally.BalanceSheet.menuManagePattern.
;var listSelectedPatternItemGrid;   //Grid attached to cell c of menuManagePatternLayout defined in preTally.BalanceSheet.menuManagePattern.   
;var listPatternItemGrid;           //Grid attached to cell b of menuManagePatternLayout defined in preTally.BalanceSheet.menuManagePattern.  
;var SH_PICombo;                    //Combo attached in managePatternForm defined in preTally.BalanceSheet.menuManagePattern.

;var dhxCertificateCountReportsLayout;        //1C layout attached to cell view_certificateCountReports of dhxMiddleBlockTabs defined in preTally.CertificateCountReports.viewCertificateCountReports.
;var CertificateCountReportToolbar;         //Toolbar attached to ath cell of dhxCertificateCountReportsLayout defined in preTally.CertificateCountReports.viewCertificateCountReports. 
;var CertificateCountReportsTabbar;         //Tabbar attached to cell a of dhxCertificateCountReportsLayout defined in preTally.CertificateCountReports.viewCertificateCountReports.

;var dhxJobCountReportsLayout;        //1C layout attached to cell view_jobCountReports of dhxMiddleBlockTabs defined in preTally.JobCountReports.viewJobCountReports.
;var JobCountReportToolbar;         //Toolbar attached to ath cell of dhxJobCountReportsLayout defined in preTally.JobCountReports.viewJobCountReports. 
;var JobCountReportsTabbar;         //Tabbar attached to cell a of dhxJobCountReportsLayout defined in preTally.JobCountReports.viewJobCountReports.

;var dhxMasterReportsLayout;        //1C layout attached to cell viewMasterReports of dhxMiddleBlockTabs defined in preTally.MasterReports.viewMasterReports.
;var ptMasterReportToolbar;         //Toolbar attached to ath cell of dhxMasterReportsLayout defined in preTally.MasterReports.viewMasterReports. 
;var ptMRpTb_Calendar;              //Init Calender defined in preTally.MasterReports.viewMasterReports.
;var ptMasterReportsTabbar;         //Tabbar attached to cell a of dhxMasterReportsLayout defined in preTally.MasterReports.viewMasterReports.
;var dhxBranchReportsLayout;        //1C layout attached to cell id  viewBranchReports of ptMasterReportsTabbar defined in preTally.MasterReports.viewBranchReports.
;var BranchReportsGrid;             //Grid attached to cell a of dhxBranchReportsLayout  defined in preTally.MasterReports.viewBranchReports .
;var rptFilterParams;               //filter variable defined in preTally.MasterReports.viewReportsVisual.

;var dhxItemReportsLayout;          //1C layout attached to cell id viewItemReports of ptMasterReportsTabbar defined in preTally.MasterReports.viewItemReports.
;var ItemReportsGrid;               //Grid attached to cell a of  dhxItemReportsLayout defined in preTally.MasterReports.viewItemReports . 
;var dhxReportsVisualLayout         //1C layout attached to cell id viewReportsVisual of  ptMasterReportsTabbar defined in preTally.MasterReports.viewReportsVisual.
;var dhxReportsVisualForm           //Form attached to the cell a of dhxReportsVisualLayout defined in preTally.MasterReports.viewReportsVisual. 
;var reportBranchDetailsGrid;       //checked not found

;var rptItem_BranchId;              //Branch id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var rptItemId;                     //Item Id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var branchRptFlag;                 //flag set when ptMasterReportsTabbar with id  viewBranchReports is active defined in preTally.MasterReport.viewBranchReports.
;var itemRptFlag;                   //flag sets when ptMasterReportsTabbar with id  viewItemReports is active defined in preTally.MasterReport.viewItemReports.
;var visualRptFlag;                 //flag sets when ptMasterReportsTabbar with id  viewReportsVisual is active defined in preTally.MasterReport.viewItemReports.
;var branchItemRptFlag;             //flag sets when ptMasterReportsTabbar with id viewBrnchItemReports is active defined in preTally.MasterReport.viewBrnchItemReports.
;var ItemBasedRptFlag;              //flag sets when ptMasterReportsTabbar with id viewItemBasedReports is active defined in preTally.MasterReport.viewItemBasedReports.
;var rptBrnchItemId;


;var rptLBMItem_BranchId;              //Branch id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var rptLBMItemId;                     //Item Id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var branchMRRptFlag;                 //flag set when ptMasterReportsTabbar with id  viewBranchReports is active defined in preTally.MasterReport.viewBranchReports.
;var itemMRRptFlag;                   //flag sets when ptMasterReportsTabbar with id  viewItemReports is active defined in preTally.MasterReport.viewItemReports.
;var visualMRRptFlag;                 //flag sets when ptMasterReportsTabbar with id  viewReportsVisual is active defined in preTally.MasterReport.viewItemReports.
;var branchItemMRRptFlag;             //flag sets when ptMasterReportsTabbar with id viewBrnchItemReports is active defined in preTally.MasterReport.viewBrnchItemReports.
;var ItemBasedMRRptFlag;              //flag sets when ptMasterReportsTabbar with id viewItemBasedReports is active defined in preTally.MasterReport.viewItemBasedReports.
;var rptLBMBrnchItemId;


;var AccSummaryFlag_Full;                //flag sets when ptAccSummaryTabbar with id viewAccSummaryReportsFull is active defined in preTally.AccSummaryReports.viewAccSummaryReportsFull.
;var AccSummaryFlag_Med;                //flag sets when ptAccSummaryTabbar with id viewAccSummaryReportsMed is active defined in preTally.AccSummaryReports.viewAccSummaryReportsMed.
;var AccSummaryFlag_Small;                //flag sets when ptAccSummaryTabbar with id viewAccSummaryReportsSmall is active defined in preTally.AccSummaryReports.viewAccSummaryReportsSmall.

;var BMR_rptItemUSId;               //User id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var BMR_rptItemId;                 //Item Id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var BMR_ItmRptId;                  //Item Id defined in preTally.MasterReports.applyReportBrnchItemFilter.
;var BMR_branchId;
;var BMR_itemRptFlag;               //flag sets when ptMasterReportsTabbar with id  viewItemReports is active defined in preTally.MasterReport.viewItemReports.
;var BMR_visualRptFlag;             //flag sets when ptMasterReportsTabbar with id  viewReportsVisual is active defined in preTally.MasterReport.viewItemReports.
;var BMR_branchItemRptFlag;         //flag sets when ptMasterReportsTabbar with id viewBrnchItemReports is active defined in preTally.MasterReport.viewBrnchItemReports.
;var BMR_ItemBasedRptFlag;          //flag sets when ptMasterReportsTabbar with id viewItemBasedReports is active defined in preTally.MasterReport.viewItemBasedReports.

;var rpt_Calendar;                  //Init calendar

;var ptTitle = $('title').text();
;var notfData = [{ id: "E", item: "", count: "" }];
;var preTallyNotfPopUp;             // pop-up window
;var dhxGridBalSheet;               //Grid attached to cell c of dhxMiddleBlockLayout.Defined in preTally.BalanceSheet.menuNewBalSheet.
;var openBSID = 0;                  //selected row id of dhxGridBalSheet defined in preTally.BalanceSheet.menuNewBalSheet .
;var openITId = 0;                  //Id of listItemGrid .Defined in preTally.BalanceSheet.menuNewItem .
;var dhxTrackListLayout;            //1C layout attached to cell TracksTab of dhxMiddleBlockTabs .Defined in preTally.Track.ListTrackJobs.
;var trackListJobGrid;              //Grid attached to cell a of dhxTrackListLayout.Defined in preTally.Track.ListTrackJobs. 
;var openTrackListID = 0;           //Id of trackListJobGrid defined in preTally.Track.ListTrackJobs.  
;var userModeForm = 'null';         //Specifies the type of user .Defined in preTally.UserProfile.menuNewUser.
;var addSubProcessForm;

;var menuOfzItemLayout;             //4C layout attached to cell menuNewOfzItem of  dhxMiddleBlockTabs defined in preTally.Item.menuNewOfzItem function.
;var addInventoryLayout;         //2E layout attached to cell addInventory of dhxMiddleBlockTabs defined in preTally.Inventory.addInventory function.
;var bsDEDetailsWin; //Dual Entry Details

;var listOfficeAdminGrid;           //Grid attached to cell b of listOfficeAdminLayout defined in preTally.Office.menuListOfficeAdmin.  

;var BSForm = new Array();          //Balancesheet form details defined in preTally.BalanceSheet.addBalSheetItem.

;var errorHandlerFlag = false;      //ask

;var ind = 0;                       //checked not found
;var arr = [];                      //checked not found
;var newFormData;                   //checked not found
;var inputValue;                    //checked not found
;var leng;                          //checked not found
;var defData = {BS_Brand: "", BS_Model: "", BS_Quantity: "",BS_Amount: "", BS_Price: "", UT_Id: "" };

;var timeNow         = new Date().getTime();
;var timePrevious    = new Date().getTime();
;var timeDelay       = 0;

;var idleTimer   = null;
;var idleState   = false;
;var idleWait    = 1000 * 300;      //1000 * seconds

;var netConnectivityError = false;
;var netConnectivityTimer;
;var netConnectivityRequests = 0;
;var requestedTab;                  //Id of active tab defined in preTally.initialise.initMainLayout.
;var requestedMenu;                 //Id of the selected menu defined in preTally.Menu.doOnMenuCall.
//-------------------------------------------------------

//--------------- Track Module Varibles -------------------
//;var newPhoneRegistrationForm;          //Form attached to loginPopup located in trackToolbar of id newPhoneRegistration defined in preTally.Track.Track function.
;var dhxTrackLayout;                    //Layout attached to dhxMiddleBlockTabs of Tracks tab defined in preTally.Track.Track function.
;var newUserTrackSidebar;               //Sidebar attached to dhxMiddleBlockTabs of TracksTab defined in preTally.Track.NewTrackUserRegistration function.
;var sidebarTabbar;                     //checked not found
;var trackToolbar;                      //Toolbar attached to dhxMiddleBlockTabs of TracksTab defined in preTally.Track.Track function.
;var trackAddProcessGrid;               //Grid attached to trackAddProcessGridPopUp defined in preTally.Track.NewTrackUserRegistration.
;var trackProcessCount = 0;             //Number of selected process defined in preTally.Track.processButtonCount .
;var trackDocumentCount = 0;            //sl no in the trackSupportingDocsGrid defined in preTally.track.NewTrackUserRegistration .
;var trackDocumentID = 0;               //Id of the track document defined in preTally.track.NewTrackUserRegistration.
;var trackAttestationProcessGrid;       //Grid attached to cell b of trackCertificateDetailsForm layout defined in preTally.Track.NewTrackUserRegistration.
;var newTrackCustomerForm;              //Form attached to cell a of trackCertificateDetailsForm layout defined in preTally.Track.NewTrackUserRegistration.
;var trackCertificateDetails;           //3J layout attached to certificate_details cell of newUserTrackSidebar defined in preTally.Track.NewTrackUserRegistration.
;var trackCertificateDetailsForm;       //2U Layout attached to cell a of trackCertificateDetails layout defined in preTally.Track.NewTrackUserRegistration.
;var trackDocumentArray = {};           //Storing track documents defined in preTally.Track.NewTrackUserRegistration.
;var trackDocumentAddEdit = 'new';      //Contains value 'new' or id of document used to add and edit document defined in preTally.Track.NewTrackUserRegistration.
;var trackPaymentDetails;               //3J layout attached to payment_details cell of newUserTrackSidebar defined in preTally.Track.NewTrackUserRegistration.
;var paymentDetailsPopUp;               //Popup attached to  trackPaymentDetailsDocuments form defined in preTally.Track.NewTrackUserRegistration.
;var trackSupportingDocsGrid;           //Grid attached to cell b of trackCertificateDetails layout defined in preTally.Track.NewTrackUserRegistration.
;var trackPaymentDetailsDocuments;      //Form attached to cell c of the trackPaymentDetails layout defined in preTally.Track.NewTrackUserRegistration.
;var trackPaymentDetailsTab;            //Tabbar attached to trackPaymentDetails of cell b defined in preTally.Track.NewTrackUserRegistration.
;var trackPaymentDetailsBilling;        // Form attached to trackPaymentDetails layout at cell position a defined in preTally.Track.NewTrackUserRegistration.
//;var loginPopup;                        //PopUp attached to the trackToolbar of newPhoneRegistration id defined in preTally.Track.Track function
;var trackAttestationCertificatedGrid   //Grid attached  to cell c of trackCertificateDetails layout defined in preTally.Track.NewTrackUserRegistration.
;var trackAddProcessGridPopUp           //PopUp attached to form named newTrackCustomerForm defined in preTally.Track.NewTrackUserRegistration.
;var TrckListTlbr;
;var trackConsoleSideBar;               //For the main sidebar (The size will be set as 1px)
;var NotifiPopupList;                   // Form attached to Notification PopUp
;var enqAmtDetailsPop;
;var ATPEnquiryCheckPop;
//-------------------------------------------------------

//--------------- MIS Report Varibles -------------------
;var MISToolbar;                        //Toolbar attached to dhxMiddleBlockTabs having id misReport defined in preTally.Reports.MISReport.
;var MISTree;                           //Grid attached to dhxMiddleBlockTabs having id misReport defined in preTally.Reports.MISReport.
;var toolbarIDObj = {};                 //contain Id of parent item defined in preTally.Reports.MISReportNodeManage.
;var toolbarIDArray = new Array();      //ask 
;var selectedItemID = 0;                //Id of the selected item in the MISTree defined in preTally.Reports.MISReport .    
//-------------------------------------------------------

//--------------Home Page Accordian Content Load Status -------------------
;var loadAccountHead = false;
;var loadOfficeTree = false;
//-------------------------------------------------------------------------

//--------------- 3 Button Confirmation -----------------
;var ConfBox_3Button;//ask
//-------------------------------------------------------

//----------- Balance Sheet Form Height In Home Page -----------------
;var BSFormHeight;
//--------------------------------------------------------------------

//--------------- Connectivity Error Message -------------------------
;var ConnectivityErrorMessage;
//--------------------------------------------------------------------

//--------------- Calculator Window Name -------------------------
var winCalculator;
//----------------------------------------------------------------

//--------------- Calendar Window Name -------------------------
var winCalendar;
//----------------------------------------------------------------

;var hd_count=new Array();              //ask 
;var hd_ids =new Array();               //ask
;var hd_pos;                            //ask
;var delhd_array=new Array();           //ask
;var definePath = new Array();          //ask

;var onRowSelectAttendance;             //Select Attendance Row For Edit
;var onEditCellAttendance;              //Select Attendance Cell For Edit
;var nameFilterAttendance;
;var ofceFilterAttendance;


//------------------- Business & Bonus Report Starts ---------------------------------
;var businessRptFlag; //flag sets when BSBonusReportsTabbar with id viewMonthwiseBusinessRpt is active defined in preTally.BusinessBonusReport.viewBusinessBonusReports().
;var statutoryRptFlag; //flag sets when BSBonusReportsTabbar with id viewMonthwiseStatutoryRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseStatutoryReport().
;var settingsFlag; //flag sets when BSBonusReportsTabbar with id viewSettings is active defined in preTally.BusinessBonusReport.viewFixedExpenseSettings().
;var fixedExpRptFlag; //flag sets when BSBonusReportsTabbar with id viewMonthwiseFixedExpRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseFixedExpReport().
;var overviewRptFlag; //flag sets when BSBonusReportsTabbar with id viewMonthwiseOverviewRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseOverviewReport().
;var varExpRptFlag; //flag sets when BSBonusReportsTabbar with id viewMonthwiseVarExpRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseVarExpReport()
;var taxRptFlag;    //flag sets when BSBonusReportsTabbar with id viewMonthwiseTaxRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseTaxReport()
;var grossRptFlag;  //flag sets when BSBonusReportsTabbar with id viewMonthwiseGrossRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseGrossReport()
;var generalRptFlag;    //flag sets when BSBonusReportsTabbar with id viewMonthwiseGeneralExpRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseGeneralReport()
;var netRptFlag;    //flag sets when BSBonusReportsTabbar with id viewMonthwiseNetProfitRpt is active defined in preTally.BusinessBonusReport.viewMonthwiseNetProfitReport()
;var expenseFlag;   // flag sets when BSBonusReportsTabbar with id viewFixedExpSettings is active
;var bonusFlag;  // flag sets when BSBonusReportsTabbar with id viewBonusSettings is active
;var bonusRptFlag;  // flag sets when BSBonusReportsTabbar with id viewMonthwiseBonusRpt is active
//------------------- Business & Bonus Report Ends ---------------------------------

//------------------- Income & Profit Report Starts ---------------------------------
;var businessIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeBusinessRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseBusinessReport().
;var statutoryIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeStatutoryRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseStatutoryReport().
;var settingsIncFlag; //flag sets when IncomeReportsTabbar with id viewIncomeFixedExpSettings is active defined in preTally.IncomeProfitReport.viewFixedExpenseSettings().
;var fixedIncExpRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeFixedExpRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseFixedExpReport().
;var overviewIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeOverviewRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseOverviewReport().
;var varExpIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeVarExpRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseVarExpReport()
;var taxIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeTaxRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseTaxReport()
;var grossIncRptFlag;   //flag sets when IncomeReportsTabbar with id viewIncomeGrossRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseGrossReport()
;var generalIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeGeneralExpRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseGeneralReport()
;var netIncRptFlag; //flag sets when IncomeReportsTabbar with id viewIncomeNetProfitRpt is active defined in preTally.IncomeProfitReport.viewMonthwiseNetProfitReport()
//------------------- Income & Profit Report Ends ---------------------------------




//-------------------- Track Reports Starts --------------------------------------
;var stateRptFlag;  
;var cityRptFlag;
;var locationFlag;
;var placeRptFlag;
;var streetRptFlag;
//-----------------------Job count---------------------------------
;var JstateRptFlag;  
;var JcityRptFlag;
;var JlocationFlag;
;var JplaceRptFlag;
;var JstreetRptFlag;
//------------------------Certificate count--------------------------------
;var CstateRptFlag;  
;var CcityRptFlag;
;var ClocationFlag;
;var CplaceRptFlag;
;var CstreetRptFlag;
//------------------------Enquiry count--------------------------------
;var EstateRptFlag;  
;var EcityRptFlag;
;var ElocationFlag;
;var EplaceRptFlag;
;var EstreetRptFlag;


//-------------------- Track Reports Ends --------------------------------------

//-------------------- Unique TimeStamp  --------------------------------------
;var timeoutStamp;
//-----------------------------------------------------------------------------

//-------------------- Job Comment PopUp  --------------------------------------
;var jobCommentPopUp;
;var jobComment;
//------------------------------------------------------------------------------

//----------------------------------- IM  --------------------------------------
;var dhxIMLayout;
;var dhxIMMembers;
;var dhxIMRecentMembers;
;var IMSearchGrid;
;var IMChatTab;
;var IMUserID;
;var IMUserChats = [];
;var IMActiveChatHeads = [];
;var IMDatDifference =  [];
;var IMDatePrevious =  [];
//;var IMItemPosition =  [];
//------------------------------------------------------------------------------


definePath = {
    'icon'      : icon_path,
    'basePath'  : base_path
};

/* Tracks Start */
;var tabFlag;
;var newRegTabCount = 0;
;var loadFlag = 0;
/* Tracks End*/

$(document).ready(function(e) {	
    preTally.Initialize.initACL();
    preTally.Initialize.initMainLayout();
    preTally.Initialize.initMainToolbar();
    preTally.Initialize.initMenuAccord();
    preTally.Initialize.initMainMenu(); 
    preTally.Initialize.initCombo();
    preTally.Initialize.initWindow();
    preTally.Initialize.initPopUp();
    preTally.Initialize.initStatusBar();
    preTally.Initialize.initNotification();
    preTally.Initialize.initHomeTab();
    preTally.Initialize.updateClock();
    preTally.Initialize.inActivity();
    
    preTally.Settings.progressOn(true, dhxLayout, null);
    
    $(window).bind("load", function(){
        preTally.Settings.progressOff(true, dhxLayout, null);
    });
    
    window.dhx4.attachEvent("onAjaxError", function(request, object){
        //console.log('Internet Error');
        //console.log(request['response'].indexOf("Internal Server Error"));
        //Internal Server Error
        if(netConnectivityError == false) {
            //console.log('Internet Error');
            netConnectivityError = true;
            
            $(".netConnectivity").show(); 
            $(".netConnectivity_msg").css({
                "background-color"  : "#F9AFB0",
                "color"             : "#752B2B"
            }); 
            $(".netConnectivity_msg").html('Response Lagging. Hold On For `a` Second'); 
            dhxMiddleBlockTabs.tabs(requestedTab).close();
            //preTally.Initialize.checkNetConnection();
            setTimeout(preTally.Initialize.checkNetConnection, 3000);
        }
    });
    setInterval("preTally.Initialize.updateClock()", 1000 );
    bkLib.onDomLoaded(function() {
        //console.log('Hii');
        IMNicEditor = new nicEditor();
    }); 
});   
  