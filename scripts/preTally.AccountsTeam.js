/** 
 This file created for account teams reports 
 created at 03-06-2025 BY Bilin
*/
; accCashTrnsInit = 0;
;var accTBDateFrm = {};
;var accTBDateTil = {};
;var accTabcal    = {};
;var accTypeCbo   = {};
;var accGroupCbo  = {};
;var accSGroupCbo = {};
;var accLedgerCbo = {};
;var accSLedgerCbo= {};
;var toolBarOffice= {};
;(function ($, window, undefined) {
	preTally.AccountsTeam = {
		// start cash transaction summery report
		acc_CashBSReports: function() { 
			if (!dhxMiddleBlockTabs.cells("acc_CashBSReports")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_CashBSReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Cash Transaction Summary &nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='reloadCashTrans'/>", 245);
                dhxMiddleBlockTabs.tabs("acc_CashBSReports").setActive();
                $(".reloadCashTrans").click(function () {
                    //rptCBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                    preTally.AccountsTeam.cashFilterReport('');
                })
                dhxAccCashTrnsLayout = dhxMiddleBlockTabs.cells("acc_CashBSReports").attachLayout("1C");
                dhxAccCashTrnsLayout.cells("a").hideHeader();
                // tool bar with filter strt
                accCashTrnsTbr = dhxAccCashTrnsLayout.cells("a").attachToolbar();
                accCashTrnsTbr.setIconsPath("images/icon/default_18/");
                accCashTrnsTbr.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(accCashTrnsTbr,'cash');
                // staus bar section start
                dhxAccCashTrnsLayout.cells("a").setText("Status bar");
                dhxAccCashTrnsLayout.cells("a").attachStatusBar({
                    text:   preTally.AccountsTeam.customAccStatusbar('cash'),// status bar text text
                    height: 75             // custom height
                });
                // initialise the tab bar...
                accCashBsTabbar = dhxAccCashTrnsLayout.cells("a").attachTabbar();
                accCashBsTabbar.addTab("a2", "Cash Transactions");
                accCashBsTabbar.tabs("a2").setActive();
                // grid initialise start
                accCashBsGrid = accCashBsTabbar.cells("a2").attachGrid();
                preTally.AccountsTeam.loadGridHead(accCashBsGrid,'cash');   
                // find the transaction type list income/expense 
                /*var transtypeOpt = preTally.BranchBSReports.transactionOptions(1); 
                // create the grid header with filter options
                accCashBsGrid.setHeader("SlNo,<select style = 'width:60px;' data-type='cash' class = 'acc_grid_cbo_fltr' gridType='cash' id='accIEcash'>"+transtypeOpt+"</select>\
                    ,<input type='text'  data-type='cash' class = 'acc_grid_txt_fltr'  gridType='cash' id ='accItemcash' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,\
                    <input type='text'  data-type='cash'  class='acc_grid_txt_fltr' gridType='cash' id ='accAmtcash' style='width: 90%;' placeholder='Amount'>,\
                    <div  data-type='cash' id='accBrachcash' style='width: 90%;' placeholder='Entry Branch'></div>,\
                    <div  data-type='cash' id='accAddBrnhcash' style='width: 90%;' placeholder='Added Branch'></div>,\
                    Date");
                accCashBsGrid.setInitWidths("80,100,*,100,150,150,100");
                accCashBsGrid.setColAlign("center,left,left,right,left,left,center");
                accCashBsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                accCashBsGrid.setColSorting("na,na,na,na,na,na,na");
                accCashBsGrid.init();
                accCashBsGrid.setImagePath("assets/grid/codebase/imgs/");
                accCashBsGrid.setSkin("dhx_skyblue");
                accCashBsGrid.enableTooltips("false,false,false,false,false,false,false");
                accCashBsGrid.enableColSpan(true);
                // paginations                
                accCashBsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);                
                accCashBsGrid.enablePaging(true, 50, 5, "paging_acc_cash", true);
                accCashBsGrid.setPagingSkin("toolbar", "dhx_skyblue");*/
                //branch combo box
                accBrachcash = new dhtmlXCombo("accBrachcash");
                preTally.AccountsTeam.loadLocationCbo(accBrachcash,"accBrachcash","cash");
                accBrachcash.setPlaceholder('Entry Branch');
                accAddBrnhcash = new dhtmlXCombo("accAddBrnhcash");
                preTally.AccountsTeam.loadLocationCbo(accAddBrnhcash,"accAddBrnhcash","cash");
                accAddBrnhcash.setPlaceholder('Added Branch');
                // text fields filter start
                preTally.AccountsTeam.accTextFilters();

                

                // default data loading start
                preTally.AccountsTeam.cashFilterReport('');
            } else {
            	dhxMiddleBlockTabs.tabs("acc_CashBSReports").setActive();
                // default data loading start
            	preTally.AccountsTeam.cashFilterReport('');
            }
		},
		cashFilterReport: function(filters) {
            var amount      = $('#accAmtcash').val().replace( /,/g, "" );
            var filterValue = new Array($('#accIEcash').val(), $('#accItemcash').val(), amount);            
			filters         = filters+'&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);			
			filters         = filters+'&LC_Id='+$('#accAddBrnhcash').val()+'&Branch='+$('#accBrachcash').val();
            filters         = filters+"&filter="+filterValue+"&type=cash"+"&from="+accCashTrnsTbr.getValue("rpt_date_from")+"&to="+accCashTrnsTbr.getValue("rpt_date_till");
            //console.log("Data Loading.....Cash");
			//console.log(filters);
            preTally.Settings.progressOn(true, dhxLayout, null);
            accCashBsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/accountReports.php"+filters), function() { 
                $('.total_acc_cash').html("# : "+accCashBsGrid.getUserData("", "Data_Count")+" ");                
                var type        = 'cash';
                $('.total_ic_'+type).html('INCOME : ' + accCashBsGrid.getUserData("", "total_ic"));
                $('.total_ex_'+type).html('EXPENSE : ' + accCashBsGrid.getUserData("", "total_ex"));
                $('.total_inlrev_'+type).html('INTL RECEIVED : ' + accCashBsGrid.getUserData("", "total_inlrev"));
                $('.total_inlpaid_'+type).html('INTL PAID : ' + accCashBsGrid.getUserData("", "total_inlpaid"));
                //$('.total_openbal_'+type).html('OPEN BAL : ' + accCashBsGrid.getUserData("", "open_balance"));

                preTally.Settings.progressOff(true, dhxLayout, null);
            });
		},
        // start bank transaction summery report
        acc_BankBSReports: function() { 
            if (!dhxMiddleBlockTabs.cells("acc_BankBSReports")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_BankBSReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Bank Transaction Summary &nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='reloadBankTrans'/>", 245);
                dhxMiddleBlockTabs.tabs("acc_BankBSReports").setActive();
                $(".reloadBankTrans").click(function () {
                    //rptCBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                    preTally.AccountsTeam.bankFilterReport('');
                })
                dhxAccBankTrnsLayout = dhxMiddleBlockTabs.cells("acc_BankBSReports").attachLayout("1C");
                dhxAccBankTrnsLayout.cells("a").hideHeader();
                // tool bar with filter strt
                accBankTrnsTbr = dhxAccBankTrnsLayout.cells("a").attachToolbar();
                accBankTrnsTbr.setIconsPath("images/icon/default_18/");
                accBankTrnsTbr.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(accBankTrnsTbr,'bank');

                dhxAccBankTrnsLayout.cells("a").setText("Status bar");
                dhxAccBankTrnsLayout.cells("a").attachStatusBar({
                    text:   preTally.AccountsTeam.customAccStatusbar('bank'),// status bar text text
                    height: 75             // custom height
                });
                // initialise the tab bar...
                accBankBsTabbar = dhxAccBankTrnsLayout.cells("a").attachTabbar();
                accBankBsTabbar.addTab("a2", "Bank Transactions");
                accBankBsTabbar.tabs("a2").setActive();
                // grid initialise start
                accBankBsGrid = accBankBsTabbar.cells("a2").attachGrid();
                preTally.AccountsTeam.loadGridHead(accBankBsGrid,'bank'); 
                // find the transaction type list income/expense 
                /*var transtypeOpt = preTally.BranchBSReports.transactionOptions(1); 
                // create the grid header with filter options
                accBankBsGrid.setHeader("SlNo,<select style = 'width:60px;' data-type='bank' class = 'acc_grid_cbo_fltr' gridType='bank' id='accIEbank'>"+transtypeOpt+"</select>\
                    ,<input type='text'  data-type='bank' class = 'acc_grid_txt_fltr' gridType='bank' id ='accItembank' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,\
                    <input type='text'  data-type='bank'  class='acc_grid_txt_fltr' gridType='bank' id ='accAmtbank' style='width: 90%;' placeholder='Amount'>,\
                    <div  data-type='bank' id='accBrachbank' style='width: 90%;' placeholder='Entry Branch'></div>,\
                    <div  data-type='bank' id='accAddBrnhbank' style='width: 90%;' placeholder='Added Branch'></div>,\
                    Date");
                accBankBsGrid.setInitWidths("80,100,*,100,150,150,100");
                accBankBsGrid.setColAlign("center,left,left,right,left,left,center");
                accBankBsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                accBankBsGrid.setColSorting("na,na,na,na,na,na,na");
                accBankBsGrid.init();
                accBankBsGrid.setImagePath("assets/grid/codebase/imgs/");
                accBankBsGrid.setSkin("dhx_skyblue");
                accBankBsGrid.enableTooltips("false,false,false,false,false,false,false");
                accBankBsGrid.enableColSpan(true);
                // paginations                
                accBankBsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);                
                accBankBsGrid.enablePaging(true, 50, 5, "paging_acc_bank", true);
                accBankBsGrid.setPagingSkin("toolbar", "dhx_skyblue");*/
                //branch combo box
                accBrachbank = new dhtmlXCombo("accBrachbank");
                preTally.AccountsTeam.loadLocationCbo(accBrachbank,"accBrachbank","bank");
                accBrachbank.setPlaceholder('Entry Branch');
                accAddBrnhbank = new dhtmlXCombo("accAddBrnhbank");
                preTally.AccountsTeam.loadLocationCbo(accAddBrnhbank,"accAddBrnhbank","bank");
                accAddBrnhbank.setPlaceholder('Added Branch');
                // find the  transaction bank filter
                accBankbank = new dhtmlXCombo("accBankbank");
                preTally.AccountsTeam.loadBankCbo(accBankbank, 0, 0, 'bank','accBankbank');  
                accBankbank.setPlaceholder('Bank Name');
                // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                


                // default data loading start
                preTally.AccountsTeam.bankFilterReport('');
            } else {
                dhxMiddleBlockTabs.tabs("acc_BankBSReports").setActive();
                // default data loading start
                preTally.AccountsTeam.bankFilterReport('');
            }
        },
        bankFilterReport: function(filters) {
            
            var amount      = $('#accAmtbank').val().replace( /,/g, "" );
            var filterValue = new Array($('#accIEbank').val(), $('#accItembank').val(), amount);            
            filters         = filters+'&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);         
            filters         = filters+'&LC_Id='+$('#accAddBrnhbank').val()+'&Branch='+$('#accBrachbank').val()+'&Bank='+$('#accBankbank').val();
            filters         = filters+"&filter="+filterValue+"&type=bank"+"&from="+accBankTrnsTbr.getValue("rpt_date_from")+"&to="+accBankTrnsTbr.getValue("rpt_date_till");
            //console.log("Data Loading..... bank");
            //console.log(filters);
            preTally.Settings.progressOn(true, dhxLayout, null);
            accBankBsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/accountReports.php"+filters), function() { 
                $('.total_acc_bank').html("# : "+accBankBsGrid.getUserData("", "Data_Count")+" ");                
                var type        = 'bank';
                $('.total_ic_'+type).html('INCOME : ' + accBankBsGrid.getUserData("", "total_ic"));
                $('.total_ex_'+type).html('EXPENSE : ' + accBankBsGrid.getUserData("", "total_ex"));
                $('.total_inlrev_'+type).html('INTL RECEIVED : ' + accBankBsGrid.getUserData("", "total_inlrev"));
                $('.total_inlpaid_'+type).html('INTL PAID : ' + accBankBsGrid.getUserData("", "total_inlpaid"));                
                $('.total_openbal_'+type).html('OPEN BAL : ' + accBankBsGrid.getUserData("", "open_balance"));

                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
		//view_CashBSReports (acc_CashBSReports) -  cash summery main page





        // common tool bar with date filter 
		customDateFilterToolBar: function(toolbar, type) {

			var Days_Options 	= [];
            var Months_Options 	= [];
            var Years_Options 	= [];
            var monthNames 		= [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            var todayDt 		= Date.today().getDate();
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
            for (i = 12; i >= 1; i--) {
                j = i;
                if (j < 10) {
                    j = '0' + i;
                }
                Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
            }
            for (i = Date.today().getFullYear(); i >= 2024; i--) {
                if (i < 2025  && (type == "listBreak" || type == "breaktime") ) {
                    // no need to show old year
                } else {
                    Years_Options.push([i, 'obj', i, "calendar_Y.png"]);
                }
            }
            if (type == "trackexp" || type == "accextdta") { // 05-01-2026
                toolbar.addText('text_company', '2', 'Office');
                toolbar.addText('rpt_company', '3', '<div id="rpt_company_combo"></div>');
                toolBarOffice[type] = new dhtmlXCombo("rpt_company_combo");
                toolBarOffice[type].load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=9&selted="+unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)), function () {
                    toolBarOffice[type].setOptionWidth(250);
                    toolBarOffice[type].attachEvent("onChange", function (id) {
                        switch (type) {
                            case 'trackexp'     : preTally.ManageTracks.filterTrackExpense('');
                            break;
                            case 'accextdta'    : preTally.AccountsTeam.loadExtraDatas('');
                            break;
                        }  
                    });
                });                
                toolbar.addSpacer("rpt_company");
            }
            //toolbar.addSpacer("rpt_branch");
            toolbar.addButtonSelect("rpt_day_filter", '4', "Select Day", Days_Options, '', '', true, true, 10, 'select');
            toolbar.addButtonSelect("rpt_month_filter", '5', "Select Month", Months_Options, '', '', true, true, 10, 'select');
            toolbar.addButtonSelect("rpt_year_filter", '6', "Select Year", Years_Options, '', '', true, true, 10, 'select');
            toolbar.addSeparator('sep1', '7');
            toolbar.addText("text_from", '8', "From");
            toolbar.addInput("rpt_date_from", '9', "", 75);
            toolbar.addButton("rpt_df_clear", '10', "", "close.gif");
            toolbar.addSeparator('sep2', '11');
            toolbar.addText("text_till", '12', "Till");
            toolbar.addInput("rpt_date_till", '13', "", 75);
            toolbar.addButton("rpt_dt_clear", '14', "", "close.gif");
            toolbar.addSeparator('sep3', '15');
            toolbar.addButton("rpt_date_filter", '16', "Search", "save.gif");
            if (type != "breaktime" && type != "accbills" && type != "accupam" && type != "accextdta") { // break time list no need excel export
                toolbar.addSeparator('sep4', '17');
                toolbar.addButton("excel_export", '18', "Export", "excel.png");
            }

            // disable the from and to date calander based on another field
            accTBDateFrm[type] = toolbar.getInput("rpt_date_from");
            accTBDateFrm[type].setAttribute("readOnly", "true");
            accTBDateFrm[type].onclick = function () {
                if (toolbar.getValue("rpt_date_till"))
                    preTally.AccountsTeam.setSens(accTBDateTil[type], "max",accTabcal[type]);
            }
            accTBDateTil[type] = toolbar.getInput("rpt_date_till");
            accTBDateTil[type].setAttribute("readOnly", "true");
            accTBDateTil[type].onclick = function () {
                if (toolbar.getValue("rpt_date_from"))
                    preTally.AccountsTeam.setSens(accTBDateFrm[type], "min",accTabcal[type]);
            }
            // init calendar;
            accTabcal[type] = new dhtmlXCalendarObject([accTBDateFrm[type], accTBDateTil[type]]);
            accTabcal[type].setDateFormat("%d.%m.%Y");  
            // change or click or clear the calander filters
            toolbar.attachEvent("onClick", function (id) {
                var pId = toolbar.getParentId(id);
                var loaddata = 0;
                if (id == 'rpt_df_clear') {
                    toolbar.setValue('rpt_date_from', '', false);
                }
                if (id == 'rpt_dt_clear') {
                    toolbar.setValue('rpt_date_till', '', false);
                }
                if (pId == 'rpt_day_filter') {
                    var dateToday = id + "." + Date.today().toString("MM.yyyy");
                    toolbar.setValue('rpt_date_from', dateToday, false);
                    toolbar.setValue('rpt_date_till', dateToday, false);
                    toolbar.setItemText('rpt_month_filter', 'Select Month');
                    toolbar.setItemText('rpt_year_filter', 'Select Year');
                    loaddata =  1;
                }
                else if (pId == 'rpt_month_filter') {
                    id = id.substr(1);
                    var tmpDate = new Date();
                    var yeartoolbar = parseInt(toolbar.getItemText('rpt_year_filter')); //01-01-2026
                    var yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                    var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                    var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                    toolbar.setValue('rpt_date_from', firstDay, false);
                    toolbar.setValue('rpt_date_till', lastDay, false);
                    toolbar.setItemText('rpt_day_filter', 'Select Day');
                    //toolbar.setItemText('rpt_year_filter', 'Select Year'); // hide 01-01-2026
                    loaddata =  1;
                }
                else if (pId == 'rpt_year_filter') {
                    var tmpDate = new Date();
                    var firstDay = new Date(id, 00, 1).toString("dd.MM.yyyy");
                    var lastDay = new Date(id, 11, 31).toString("dd.MM.yyyy");
                    toolbar.setValue('rpt_date_from', firstDay, false);
                    toolbar.setValue('rpt_date_till', lastDay, false);
                    toolbar.setItemText('rpt_day_filter', 'Select Day');
                    toolbar.setItemText('rpt_month_filter', 'Select Month');
                    loaddata =  1;
                }
                if (id == 'rpt_date_filter') {
                    toolbar.setItemText('rpt_day_filter', 'Select Day');
                    toolbar.setItemText('rpt_month_filter', 'Select Month');
                    toolbar.setItemText('rpt_year_filter', 'Select Year');
                    loaddata =  1;
                }

                if (id == 'excel_export') {
                    // export the excel based on the paramaters
                    //console.log("click export..");
                    switch (type) {
                        case 'listBreak' : preTally.Attendance.breakTimeRptExport();
                        break;
                        case 'trackexp'  : preTally.ManageTracks.exportTrackExpense();
                        break;
                        default :
                        preTally.AccountsTeam.exportReport(type, toolbar);
                        break;
                    }
                } else if (loaddata == 1) {
                    // load all data grid based on the parameters
                    switch (type) {
                        case 'cash'         : preTally.AccountsTeam.cashFilterReport('');
                        break;
                        case 'bank'         : preTally.AccountsTeam.bankFilterReport('');
                        break;
                        case 'breaktime'    : preTally.Attendance.breakFilterList();
                        break;                        
                        case 'listBreak'    : preTally.Attendance.breakTimeRptFilter();
                        break;                      
                        case 'accbills'     : preTally.AccountsTeam.loadBillList('');
                        break;                     
                        case 'accupam'      : preTally.AccountsTeam.chgAftMapReport('');
                        break;  
                        case 'trackexp'     : preTally.ManageTracks.filterTrackExpense('');
                        break; 
                        case 'accextdta'    : preTally.AccountsTeam.loadExtraDatas('');
                        break;                     
                    }                    
                }
            });
            if (unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 4 && type != "breaktime" && type != "listBreak") {
                // default filter calander if in the balance sheet acl
                var date    = new Date();
                var month   = date.getMonth();
                tDate       = Date.today().toString("dd.MM.yyyy");
                toolbar.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                toolbar.setValue('rpt_date_till', tDate);
                toolbar.setItemText('rpt_month_filter', monthNames[month]);
            } else {
                cDate   = Date.today().toString("dd.MM.yyyy");
                toolbar.setValue('rpt_date_till', cDate);
                toolbar.setValue('rpt_date_from', cDate);
                toolbar.setItemText('rpt_day_filter', 'Today');
            }
		},
        setSens: function (inp, k, accTabcalender) {
            if (k == "min") {
                accTabcalender.setSensitiveRange(inp.value, null);
            } else {
                accTabcalender.setSensitiveRange(null, inp.value);
            }
        },
        setDefaultDateFilter: function(toolbar, type) {
            
            cDate   = Date.today().toString("dd.MM.yyyy");
            toolbar.setValue('rpt_date_till', cDate);
            toolbar.setValue('rpt_date_from', cDate);
            toolbar.setItemText('rpt_day_filter', 'Today');
        },


        // common status bar content start
        customAccStatusbar: function(type) {
            if (type == 'bank') {
                var extrafield = '<div class="greenColor total_openbal_'+type+'">OPEN BAL: 0</div>';
            } else {
                var extrafield = '';
            }
            return '<div class="statusBarAcc" >\
                <div style="float:left"><div class="total_acc_'+type+'"># : 0</div>\
                </div><div style="float:right">'+extrafield+'\
                <div class="greenColor total_ic_'+type+'">INCOME : 0</div>\
                <div class="redColor total_ex_'+type+'">EXPENSE : 0</div>\
                <div class="greenColor total_inlrev_'+type+'" title="Internal Transfer Received"> INTL RECEIVED : 0</div>\
                <div class="redColor total_inlpaid_'+type+'" title="Internal Transfer Paid"> INTL PAID : 0</div>\
            </div></div><div id="paging_acc_'+type+'" style="top:0px !important;float:left !important;width:100% !important;"></div>';  
            /*return '<div class="statusBarAcc" >\
                <div class="total_acc_'+type+'"># : 0</div>\
                <div class="blueColor total_ob_'+type+'"> OP BALANCE : 0</div>\
                <div class="greenColor total_ic_'+type+'">INCOME : 0</div>\
                <div class="redColor total_ex_'+type+'">EXPENSE : 0</div>\
                <div class="blueColor total_cb_'+type+'"> CL BALANCE : 0</div>\
                <div class="greenColor total_inlrev_'+type+'" title="Internal Transfer Received"> INTL RECEIVED : 0</div>\
                <div class="redColor total_inlpaid_'+type+'" title="Internal Transfer Paid"> INTL PAID : 0</div>\
            </div><div id="paging_acc_'+type+'" style="top:0px !important;float:left !important;width:100% !important;"></div>'; */
        },
        loadLocationCbo: function(cboids, cboname, type) {
            //load cbos
            var offids = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            if (type == 'accjournal') {
                offids = "0&sibling_id=1";
            }
            cboids.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check&ofid="+offids), function () {                
                preTally.UserProfile.applyFilterHandler(cboids);
                cboids.setOptionWidth(230);
            });
            // on change the branchs start                 
            cboids.attachEvent("onChange", function () {
                var branchVal = cboids.getSelectedValue();
                if (!cboids.getSelectedValue() && cboids.getComboText())
                    branchVal = cboids.getComboText();
                $("#"+cboname).val(branchVal);
                switch (type) {
                    case 'cash' : preTally.AccountsTeam.cashFilterReport('');
                    break;
                    case 'bank' : preTally.AccountsTeam.bankFilterReport('');
                    break;
                    case 'accjournal': preTally.AccountsTeam.loadJournals('');
                    break;
                }
            });     
        },
        loadOfficeCbo: function(cboids, cboname, type) {
            //load cbos
            cboids.load(preTally.Initialize.encryptURL("requisites/offices.php&sibling_id=1"), function () {                
                preTally.UserProfile.applyFilterHandler(cboids);
                cboids.setOptionWidth(230);
            });
            // on change the branchs start                 
            cboids.attachEvent("onChange", function () {
                var officeVal = cboids.getSelectedValue();
                $("#"+cboname).val(officeVal);
                switch (type) {
                    case 'accopbal': preTally.AccountsTeam.loadOpenBalance('');
                    break;
                }
            });     
        },
        loadGridHead: function(accRptGrid, type) {
            // find the transaction type list income/expense 
            var transtypeOpt    = preTally.BranchBSReports.transactionOptions(1); 
            var extrafields     = "";
            var extraFldWidth   = "";
            var extraFldAlign   = "";
            var extraFldType    = "";
            var extraFldSort    = "";
            var extraFldTool    = "";
            if (type == 'bank') {
                extrafields     = "<div  data-type='"+type+"' id='accBank"+type+"' style='width: 90%;' placeholder='Bank Name'></div>,";
                extraFldWidth   = "100,";
                extraFldAlign   = "left,";
                extraFldType    = "ro,";
                extraFldSort    = "na,";
                extraFldTool    = "false,";
            }
            // create the grid header with filter options
            accRptGrid.setHeader("SlNo,<select style = 'width:60px;' data-type='"+type+"' class = 'acc_grid_cbo_fltr' gridType='"+type+"' id='accIE"+type+"'>"+transtypeOpt+"</select>\
                ,<input type='text'  data-type='"+type+"' class = 'acc_grid_txt_fltr' gridType='"+type+"' id ='accItem"+type+"' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,\
                <input type='text'  data-type='"+type+"'  class='acc_grid_txt_fltr' gridType='"+type+"' id ='accAmt"+type+"' style='width: 90%;' placeholder='Amount'>,\
                "+extrafields+"\
                <div  data-type='"+type+"' id='accBrach"+type+"' style='width: 90%;' placeholder='Entry Branch'></div>,\
                <div  data-type='"+type+"' id='accAddBrnh"+type+"' style='width: 90%;' placeholder='Added Branch'></div>,\
                Date");
            accRptGrid.setInitWidths("80,120,*,100,"+extraFldWidth+"150,150,100");
            accRptGrid.setColAlign("center,left,left,right,"+extraFldAlign+"left,left,center");
            accRptGrid.setColTypes("ro,ro,ro,ro,"+extraFldType+"ro,ro,ro");
            accRptGrid.setColSorting("na,na,na,na,"+extraFldSort+"na,na,na");
            accRptGrid.init();
            accRptGrid.setImagePath("assets/grid/codebase/imgs/");
            accRptGrid.setSkin("dhx_skyblue");
            accRptGrid.enableTooltips("false,false,false,false,"+extraFldTool+"false,false,false");
            accRptGrid.enableColSpan(true);
            // paginations                
            accRptGrid.setPagingWTMode(true, false, true, [15, 30, 50, 100, 150]);                
            accRptGrid.enablePaging(true, 50, 5, "paging_acc_"+type, true);
            accRptGrid.setPagingSkin("toolbar", "dhx_skyblue");

            // click on the row then show the popup
            accRptGrid.attachEvent("onRowSelect", function (id, ind) {               
                var SH_Id = accRptGrid.getUserData(id, "SH_Id");                
                preTally.AccountsTeam.showDetailData(this,id,SH_Id);
            });
        },
        accTextFilters: function() {
            var filtrInterval;
            $('.acc_grid_txt_fltr').unbind('keyup');
            $( ".acc_grid_txt_fltr" ).keyup(function(value) {
                var type    = $(this).attr("gridType");
                if(filtrInterval) clearInterval(filtrInterval);
                filtrInterval = setInterval( function() { 
                    switch (type) {
                        case 'cash'         : preTally.AccountsTeam.cashFilterReport('');
                        break;
                        case 'bank'         : preTally.AccountsTeam.bankFilterReport('');
                        break;
                        case 'accgrp'       : preTally.AccountsTeam.loadGroupList('');
                        break;
                        case 'accledger'    : preTally.AccountsTeam.loadLedgerList('');
                        break;
                        case 'accmledger'   : preTally.AccountsTeam.loadMapLedgerList('');
                        break;
                        case 'accjournal'   : preTally.AccountsTeam.loadJournals('');
                        break;
                        case 'accmapjnl'    : preTally.AccountsTeam.loadItemJournal('');
                        break; 
                        case 'acctds'       : preTally.AccountsTeam.loadTdsList('');
                        break; 
                        case 'accvendor'    : preTally.AccountsTeam.loadVendorList('');
                        break; 
                        case 'accbills'     : preTally.AccountsTeam.loadBillList('');
                        break; 
                        case 'accbillr'     : preTally.AccountsTeam.loadBillRCList('');
                        break; 
                        case 'accbkitm'     : preTally.AccountsTeam.loadBlockTransList('');
                        break; 
                        case 'accjentry'    : preTally.AccountsTeam.loadJournalEntry('');   
                        break; 
                        case 'accjdata'     : preTally.AccountsTeam.loadLedgerData('');   
                        break; 
                        case 'accopbal'     : preTally.AccountsTeam.loadOpenBalance('');   
                        break; 
                        case 'accupam'      : preTally.AccountsTeam.chgAftMapReport('');   
                        break; 
                        case 'trackexp'     : preTally.ManageTracks.filterTrackExpense('');   
                        break; 
                        case 'accextdta'     : preTally.AccountsTeam.loadExtraDatas('');   
                        break;                         
                    }      
                    clearInterval(filtrInterval); 
                }, 500);
            });
            $('.acc_grid_cbo_fltr').unbind('change');
            $( ".acc_grid_cbo_fltr" ).change(function(value) {
                var type    = $(this).attr("gridType");
                switch (type) {
                    case 'cash'         : preTally.AccountsTeam.cashFilterReport('');
                    break;
                    case 'bank'         : preTally.AccountsTeam.bankFilterReport('');
                    break;
                    case 'accgrp'       : preTally.AccountsTeam.loadGroupList('');
                    break;
                    case 'accledger'    : preTally.AccountsTeam.loadLedgerList('');
                    break;
                    case 'accmledger'   : preTally.AccountsTeam.loadMapLedgerList('');
                    break;
                    case 'acctds'       : preTally.AccountsTeam.loadTdsList('');
                    break;
                    case 'accvendor'    : preTally.AccountsTeam.loadVendorList('');
                    break;
                    case 'accbillr'     : preTally.AccountsTeam.loadBillRCList('');
                    break; 
                    case 'accjournal'   : preTally.AccountsTeam.loadJournals('');
                    break;
                } 
            });            
        },
        showDetailData: function (inp, BS_Id, SH_Id) {
            // details popup shown
            if (typeof accDetailDataPop == "undefined") {
                accDetailDataPop = new dhtmlXPopup({mode: "right"});

            }
            if (accDetailDataPop.isVisible()) {
                accDetailDataPop.hide();
            }

            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;
            var rptDetailsPop = accDetailDataPop.attachForm();
            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/detailsPopup.php&" + params), function () {
                accDetailDataPop.show(x, y, w, h);
                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1);
                column0.style.borderRight = "1px solid #a4bed4";
            });
        },
        hideDetailData: function () {
            if (accDetailDataPop.isVisible()) {
                accDetailDataPop.hide();
            }
        },
        exportReport: function(type, toolbar) {
            // export excels
            var amount          = $('#accAmt'+type).val().replace( /,/g, "" );
            var filters         =  {};
            filters['filter']   = new Array($('#accIE'+type).val(), $('#accItem'+type).val(), amount);            
            filters['OFID']     = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);         
            filters['LC_Id']    = $('#accAddBrnh'+type).val();
            filters['Branch']   = $('#accBrach'+type).val();
            filters['type']     =  type;
            filters['from']     =  toolbar.getValue("rpt_date_from");
            filters['to']       =  toolbar.getValue("rpt_date_till");
            if (type == 'bank') { // bank filters added
                filters['Bank']   = $('#accBank'+type).val();
            }
            //console.log("Data Loading..... "+type);
            //console.log(filters);
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                preTally.Initialize.encryptURL('warehouse/accounts/ExportReport.php'),
                { inpdata : filters },
                function(data) {
                    fileName = data.split("XL_");
                    if(fileName[1]) {
                         document.location ="uploads/excelFile/"+fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
            });
        
        },
        // account settings sections started 08-07-2025
        acc_Groups : function() {
            if (!dhxMiddleBlockTabs.cells("acc_Groups")) {
                dhxMiddleBlockTabs.addTab("acc_Groups", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Accounts Groups &nbsp; ", 185);
                dhxMiddleBlockTabs.tabs("acc_Groups").setActive();
                // layout and heading start
                var accGrpLayout = dhxMiddleBlockTabs.cells("acc_Groups").attachLayout('2U');
                accGrpLayout.cells("a").setText("Add Group &amp; Sub Group");
                accGrpLayout.cells("b").setText("List Group &amp; Sub Group");
                accGrpLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accGrpLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accgrp'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accGrpGrid = accGrpLayout.cells("b").attachGrid();
                
                /*accGrpGrid.setHeader("SlNo, Group Type "+preTally.AccountsTeam.commonGTypeCbo('accgrp')+", \
                Group Name <input type='text' class = 'acc_grid_txt_fltr'  gridType='accgrp'  id ='accgrpName' style='width: 90%;' placeholder='Enter Group Name'>,\
                Parent Name, Status "+preTally.AccountsTeam.commonStatusCbo('accgrp')+",\
                Updated By, Verified By, Action");*/
                accGrpGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accgrp' class='accGridSort' />, \
                    Group Type<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='type' gridType='accgrp' class='accGridSort' />, \
                    Group Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='group' gridType='accgrp' class='accGridSort' />, \
                    Parent Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='parent' gridType='accgrp' class='accGridSort' />,\
                     Status, Updated By, Verified By, Action");
                accGrpGrid.attachHeader(",#combo_filter,#text_filter,#combo_filter,#text_filter,,,");
                accGrpGrid.setInitWidths("50,90,*,*,110,150,150,100");
                accGrpGrid.setColAlign("center,left,left,left,Center,left,left,center");
                accGrpGrid.setColTypes("ro,ro,ro,ro,combo,ro,ro,ro");
                accGrpGrid.setColSorting("int,str,str,str,na,na,na,na");              
                accGrpGrid.enableTooltips("false,false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accGrpGrid, 'accgrp');
                // local side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadGroupList('');
                // Grid Click (edit cell)
                preTally.AccountsTeam.editStatusColumn(accGrpGrid,4,"addGroup.php"); 
                 // text fields filter start
                //preTally.AccountsTeam.accTextFilters(); // server side filter 
                //  -------------- Attach FORM -------------- //
                accGrpForm = accGrpLayout.cells("a").attachForm();
                accGrpForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addGroup.php"), function () {
                    
                    accTypeCbo['accgrp'] = accGrpForm.getCombo("type");
                    accGroupCbo['accgrp']= accGrpForm.getCombo("parent_id");
                    preTally.AccountsTeam.changeCboCustom('accgrp');                    

                    // save or cancel button click
                    accGrpForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccGrp") {
                            // click on save button
                            if (!preTally.AccountsTeam.commonTextValid(accGrpForm,"description")) {
                                return false;
                            }                           
                            // check the input fields values are correct
                            var isValid = accGrpForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accGrpForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addGroup.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        accGrpForm.resetValidateCss();
                                        accGrpForm.clear();
                                        accGrpForm.setItemValue("eid", 0); 
                                        accGroupCbo['accgrp'].setComboText('All');
                                        preTally.AccountsTeam.loadGroupList();
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });
                            }else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            }
                        } else if (name == "CancelAccGrp") {
                            // click on cancel button
                            accGrpForm.resetValidateCss();
                            accGrpForm.clear();
                            accGrpForm.setItemValue("eid", 0);  
                        }
                    }); // button click
                    preTally.AccountsTeam.clearFormlayout(accGrpLayout,accGrpForm); // form loading permission check

                }); // form loading...

            } else {
                dhxMiddleBlockTabs.tabs("acc_Groups").setActive();
            }
        },
        acc_Ledger : function() {
            if (!dhxMiddleBlockTabs.cells("acc_Ledger")) {
                dhxMiddleBlockTabs.addTab("acc_Ledger", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Accounts Ledger &nbsp; ", 185);
                dhxMiddleBlockTabs.tabs("acc_Ledger").setActive();
                // layout and heading start
                var accLedgerLayout = dhxMiddleBlockTabs.cells("acc_Ledger").attachLayout('2U');
                accLedgerLayout.cells("a").setText("Add Ledger &amp; Sub Ledger");
                accLedgerLayout.cells("b").setText("List Ledger &amp; Sub Ledger");
                accLedgerLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accLedgerLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accledger'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accLedgerGrid = accLedgerLayout.cells("b").attachGrid();
                accLedgerGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accledger' class='accGridSort' />, Group Type <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='type' gridType='accledger' class='accGridSort' /> "+preTally.AccountsTeam.commonGTypeCbo('accledger')+", \
                Ledger Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accledger' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accledger' id ='accledgerName' style='width: 90%;' placeholder='Enter Ledger Name'>,\
                Parent Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='parent' gridType='accledger' class='accGridSort' />, Group Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='group' gridType='accledger' class='accGridSort' /> <div id='accledgerGroup' style='width: 98%;' placeholder='Group Name'></div>,\
                Status "+preTally.AccountsTeam.commonStatusCbo('accledger')+", Action");
                accLedgerGrid.setInitWidths("50,90,*,*,*,100,100");
                accLedgerGrid.setColAlign("center,left,left,left,left,Center,center");
                accLedgerGrid.setColTypes("ro,ro,ro,ro,ro,combo,ro");
                accLedgerGrid.setColSorting("na,na,na,na,na,na,na");              
                accLedgerGrid.enableTooltips("false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accLedgerGrid, 'accledger');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadLedgerList('');   
                // Grid Click (edit cell)
                preTally.AccountsTeam.editStatusColumn(accLedgerGrid,5,"addLedger.php");  
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                // Extra combo filter 
                accledgerGroup = new dhtmlXCombo("accledgerGroup");
                accledgerGroup.load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=3"), function () {                
                    accledgerGroup.setOptionWidth(200);
                    accledgerGroup.enableFilteringMode('between'); 
                    //preTally.UserProfile.applyFilterHandler(accledgerGroup);
                });
                accledgerGroup.attachEvent("onChange", function () {
                    var btypVal = accledgerGroup.getSelectedValue();                    
                    $("#accledgerGroup").val(btypVal);
                    preTally.AccountsTeam.loadLedgerList(''); 
                });
                //  -------------- Attach FORM -------------- //
                accLedgerForm = accLedgerLayout.cells("a").attachForm();
                var editid = 0;
                accLedgerForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addLedger.php&edit_id="+editid), function () {
                    //accTypeCbo['accledger']     = accLedgerForm.getCombo("type");
                    //accGroupCbo['accledger']    = accLedgerForm.getCombo("group_id");
                    accSGroupCbo['accledger']   = accLedgerForm.getCombo("subgroup_id");
                    accLedgerCbo['accledger']   = accLedgerForm.getCombo("parent_id");
                    accLedgerCbo['accledgerless']   = accLedgerForm.getCombo("less_id");
                    accLedgerCbo['accledgerplus']   = accLedgerForm.getCombo("plus_id");
                    accVendorCbo                = accLedgerForm.getCombo("vendor_id");
                    preTally.AccountsTeam.changeCboCustom('accledger');
                    // new set up with custom filterledger
                    accSGroupCbo['accledger'].enableFilteringMode('between');   
                    accLedgerCbo['accledgerless'].enableFilteringMode('between');   
                    accLedgerCbo['accledgerplus'].enableFilteringMode('between');   
                    //preTally.UserProfile.applyFilterHandler(accSGroupCbo['accledger']); 
                    accSGroupCbo['accledger'].setOptionWidth(400);
                    accLedgerCbo['accledgerless'].setOptionWidth(400);
                    accLedgerCbo['accledgerplus'].setOptionWidth(400);
                    //accLedgerCbo['accledgerless'].disable();
                    //accLedgerCbo['accledgerplus'].disable();
                    accLedgerForm.disableItem("less_id");
                    accLedgerForm.disableItem("plus_id");
                    // vendor combo filter search
                    preTally.UserProfile.applyFilterHandler(accVendorCbo); 
                    // contra entry set or remove 06-11-2025
                    isContraCbo   = accLedgerForm.getCombo("is_contra");                    
                    isContraCbo.attachEvent("onChange", function () {
                        if (isContraCbo.getSelectedValue() == 0 ) {
                            accLedgerCbo['accledgerless'].setComboValue(0);
                            accLedgerCbo['accledgerplus'].setComboValue(0);
                            //accLedgerCbo['accledgerless'].disable();
                            //accLedgerCbo['accledgerplus'].disable();
                            accLedgerForm.disableItem("less_id");
                            accLedgerForm.disableItem("plus_id");
                        } else {
                            //accLedgerCbo['accledgerless'].enable();
                            //accLedgerCbo['accledgerplus'].enable();
                            accLedgerForm.enableItem("less_id");
                            accLedgerForm.enableItem("plus_id");
                        }
                    });

                     // save or cancel button click
                    accLedgerForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccLedger") {
                            // click on save button
                            if (!preTally.AccountsTeam.commonTextValid(accLedgerForm,"description")) {
                                return false;
                            }                           
                            // check the input fields values are correct
                            var isValid = accLedgerForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accLedgerForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addLedger.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        accLedgerForm.resetValidateCss();
                                        accLedgerForm.clear();
                                        accLedgerForm.setItemValue("eid", 0); 
                                        //preTally.AccountsTeam.clearCboCustom('accledger',1); 
                                        preTally.AccountsTeam.clearCboCustom('accledger',3); 
                                        preTally.AccountsTeam.loadLedgerList();
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });
                            }else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            }
                        } else if (name == "CancelAccLedger") {
                            // click on cancel button
                            accLedgerForm.resetValidateCss();
                            accLedgerForm.clear();
                            accLedgerForm.setItemValue("eid", 0); 
                            //preTally.AccountsTeam.clearCboCustom('accledger',1); 
                            preTally.AccountsTeam.clearCboCustom('accledger',3); 
                        }
                    }); // button click
                    preTally.AccountsTeam.clearFormlayout(accLedgerLayout,accLedgerForm); // form loading permission check
                }); // form close
                
            } else {
                dhxMiddleBlockTabs.tabs("acc_Ledger").setActive();
            }
        },
        acc_MapItems : function() {
            if (!dhxMiddleBlockTabs.cells("acc_MapItems")) {
                dhxMiddleBlockTabs.addTab("acc_MapItems", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Map Accounts Ledger &nbsp; ", 185);
                dhxMiddleBlockTabs.tabs("acc_MapItems").setActive();
                // layout and heading start
                var accMapLedgerLayout = dhxMiddleBlockTabs.cells("acc_MapItems").attachLayout('2U');
                accMapLedgerLayout.cells("a").setText("Map Ledger &amp; Items");
                accMapLedgerLayout.cells("b").setText("List Mapped Ledger &amp; Items");
                accMapLedgerLayout.cells("a").setWidth(410);
                // status bar or pagination and count
                accMapLedgerLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accmledger','accexp'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accMapLedgerGrid = accMapLedgerLayout.cells("b").attachGrid();
                accMapLedgerGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accmledger' class='accGridSort' />,\
                Group Type <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='type' gridType='accmledger' class='accGridSort' />"+preTally.AccountsTeam.commonGTypeCbo('accmledger')+", \
                Group Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='group' gridType='accmledger' class='accGridSort' /> <div id='accmledgerGroup' style='width: 98%;' placeholder='Group Name'></div>,\
                Ledger Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accmledger' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accmledger' id ='accmledgerName' style='width: 90%;' placeholder='Enter Ledger Name'>,\
                Item Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='item' gridType='accmledger' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accmledger' id ='accmledgerItem' style='width: 90%;' placeholder='Enter Item Name'>,\
                Item Head<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='subhead' gridType='accmledger' class='accGridSort' /> <div id='accmledgerSH' style='width: 98%;' placeholder='Sub Head Name'></div>,\
                Status "+preTally.AccountsTeam.commonStatusCbo('accmledger')+", Action");
                accMapLedgerGrid.setInitWidths("50,90,*,*,*,110,90,90");
                accMapLedgerGrid.setColAlign("center,left,left,left,left,left,Center,center");
                accMapLedgerGrid.setColTypes("ro,ro,ro,ro,ro,ro,combo,ro");
                accMapLedgerGrid.setColSorting("na,na,na,na,na,na,na,na");              
                accMapLedgerGrid.enableTooltips("false,false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accMapLedgerGrid, 'accmledger');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadMapLedgerList('');  
                 // Extra combo filter 
                accmledgerSH = new dhtmlXCombo("accmledgerSH");
                accmledgerSH.load(preTally.Initialize.encryptURL("requisites/subheadCombo.php&ctype=check&item=only"), function () {                
                    accmledgerSH.setOptionWidth(400);
                    accmledgerSH.setComboValue(0);
                    //accmledgerSH.readonly(true);
                    //preTally.UserProfile.applyFilterHandler(accmledgerSH);
                    accmledgerSH.enableFilteringMode('between');   
                });
                accmledgerSH.attachEvent("onChange", function () {
                    var btypVal = accmledgerSH.getSelectedValue();                    
                    $("#accmledgerSH").val(btypVal);
                    preTally.AccountsTeam.loadMapLedgerList(''); 
                });
                accmledgerGroup = new dhtmlXCombo("accmledgerGroup");
                accmledgerGroup.load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=3"), function () {                
                    accmledgerGroup.setOptionWidth(400);
                    //accmledgerGroup.readonly(true);
                    //preTally.UserProfile.applyFilterHandler(accmledgerGroup);
                    accmledgerGroup.enableFilteringMode('between');                     
                    //accmledgerGroup.allowFreeText(false);
                    //accmledgerGroup.enableFilteringMode(true);
                });
                accmledgerGroup.attachEvent("onChange", function () {
                    var btypVal = accmledgerGroup.getSelectedValue();                    
                    $("#accmledgerGroup").val(btypVal);
                    preTally.AccountsTeam.loadMapLedgerList(''); 
                });
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                // Grid Click (edit cell)
                preTally.AccountsTeam.editStatusColumn(accMapLedgerGrid,6,"mapLedger.php");

                //  -------------- Attach FORM -------------- //
                accmLedgerForm = accMapLedgerLayout.cells("a").attachForm();
                
                accmLedgerForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/mapLedgerItem.php"), function () {
                    
                    // declare all dynamic loaded combo box
                    //accTypeCbo['accmledger']    = accmLedgerForm.getCombo("type");
                    //accGroupCbo['accmledger']   = accmLedgerForm.getCombo("group_id");                    
                    //accSGroupCbo['accmledger']  = accmLedgerForm.getCombo("subgroup_id");
                    accLedgerCbo['accmledger']  = accmLedgerForm.getCombo("ledger_id");
                    accSLedgerCbo['accmledger'] = accmLedgerForm.getCombo("subledger_id");
                    accSubHeadCbo               = accmLedgerForm.getCombo("subhead_id");
                    accItemCbo                  = accmLedgerForm.getCombo("item_id");
                    preTally.AccountsTeam.changeCboCustom('accmledger'); 
                    // new set up with custom filterledger
                    //preTally.UserProfile.applyFilterHandler(accLedgerCbo['accmledger']); 
                    accLedgerCbo['accmledger'].enableFilteringMode('between');                     
                    accLedgerCbo['accmledger'].setOptionWidth(400);
                    // loading items based on the sub head..
                   //preTally.UserProfile.applyFilterHandler(accSubHeadCbo);
                    accSubHeadCbo.enableFilteringMode('between'); 
                    accSubHeadCbo.setOptionWidth(400);
                    accSLedgerCbo['accmledger'].setOptionWidth(400);
                    accItemCbo.setOptionWidth(400);

                    accSubHeadCbo.attachEvent("onChange", function () {
                        //console.log("Sub head combo changed");
                        var subhead_id = accSubHeadCbo.getSelectedValue();
                        preTally.AccountsTeam.clearCboCustom('',100, accItemCbo);
                        if (subhead_id > 0) {
                            var sitem_id = accmLedgerForm.getItemValue('sitem_id');                            
                            accItemCbo.load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=5&parent_id="+subhead_id+"&item_id=" + sitem_id), function () {
                                //preTally.UserProfile.applyFilterHandler(accItemCbo);
                                accItemCbo.enableFilteringMode('between');
                                accItemCbo.setComboValue(sitem_id);
                            });
                        } else {
                            accItemCbo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/customCbo.php&flag=5&parent_id=0"),true);
                        }
                    });
                    accItemCbo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/customCbo.php&flag=5"),true);
                    // save or cancel button click
                    accmLedgerForm.attachEvent("onButtonClick", function (name) {

                       if (name == "saveMapLedger") {// click on save button
                                          
                            // check the input fields values are correct
                            var isValid = accmLedgerForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                dhtmlx.confirm({
                                    title: "Confirm",
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Do you want to Continue ?",
                                    callback: function (result) {
                                        if (result) {
                                            accmLedgerForm.send(preTally.Initialize.encryptURL("warehouse/accounts/mapLedger.php&flag=0"), function (loader, response) {
                                                var jsonres = JSON.parse(response);
                                                if (jsonres.status == 1) {

                                                    dhtmlx.message({ text: jsonres.message });
                                                    accmLedgerForm.resetValidateCss();
                                                    accmLedgerForm.clear();
                                                    accmLedgerForm.setItemValue("eid", 0); 
                                                    preTally.AccountsTeam.clearCboCustom('',100, accItemCbo);
                                                    //preTally.AccountsTeam.clearCboCustom('accmledger',1); 
                                                    preTally.AccountsTeam.clearCboCustom('accmledger',4); 
                                                    preTally.AccountsTeam.loadMapLedgerList();
                                                } else {
                                                    dhtmlx.message({ type: "error", text: jsonres.message });
                                                }
                                            });
                                        }
                                    }
                                });                               
                            }else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            }
                        } else if (name == "CancelMapLedger") {
                            // click on cancel button
                            accmLedgerForm.resetValidateCss();
                            accmLedgerForm.clear();
                            accmLedgerForm.setItemValue("eid", 0);  
                            //preTally.AccountsTeam.clearCboCustom('accmledger',1); 
                            preTally.AccountsTeam.clearCboCustom('accmledger',4); 
                            preTally.AccountsTeam.clearCboCustom('',100, accItemCbo);
                        }
                    }); // button click
                    preTally.AccountsTeam.clearFormlayout(accMapLedgerLayout,accmLedgerForm); // form loading permission check
                }); // close form loading
                
            } else {
                dhxMiddleBlockTabs.tabs("acc_MapItems").setActive();
            }
        },
        loadGroupList : function(extra) {
            // get all Groups server side filter values
            //var filterValue = new Array($('#accgrpName').val(),$('#accgrpType').val(),$( "#accgrpStatus" ).val());
            var filterValue = new Array();
            //console.log(filterValue);
            accGrpGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listGroup.php&filter="+filterValue), function() { 
                $('#total_accgrp').html("# : "+accGrpGrid.getUserData("", "Data_Count")+" "); 
                accGrpGrid.filterByAll();                
            });
        },
        editGroup: function (elem, eid) {            
            var status      = accGrpGrid.getUserData(eid, "status");
            if (status == 0) { // blocked item no need to edit
                dhtmlx.message({ type: "error", text: "Edit Not Possible For Blocked Items." });
                return false;
            }
            accTypeCbo['accgrp'].setComboValue(0);
            accGrpForm.setItemValue("eid",eid);  
            accGrpForm.setItemValue("parent_id", accGrpGrid.getUserData(eid, "parent_id"));
            accGrpForm.setItemValue("mgroup_id", accGrpGrid.getUserData(eid, "parent_id"));      
            accGrpForm.setItemValue("type", accGrpGrid.getUserData(eid, "type"));
            accGrpForm.setItemValue("title", accGrpGrid.getUserData(eid, "title"));
            accGrpForm.setItemValue("description", accGrpGrid.getUserData(eid, "description"));
            //accTypeCbo['accgrp'].setComboValue(accGrpGrid.getUserData(eid, "type"));
        }, 
        loadLedgerList : function(extra) {
            // get all ledger List
            var filterValue = new Array($('#accledgerName').val(),$('#accledgerType').val(),$( "#accledgerStatus" ).val(),$('#accledgerGroup').val());
            accLedgerGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listLedger.php&filter="+filterValue+extra), function() { 
                $('#total_accledger').html("# : "+accLedgerGrid.getUserData("", "Data_Count")+" ");
            });
        },
        /*loadLedgerForm: function(editid) {

            editid = (typeof editid != "undefined") ? editid : 0;
            //accLedgerForm.clearAll();
            accLedgerForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addLedger.php&edit_id="+editid), function () {
                //accTypeCbo['accledger']     = accLedgerForm.getCombo("type");
                //accGroupCbo['accledger']    = accLedgerForm.getCombo("group_id");
                accSGroupCbo['accledger']   = accLedgerForm.getCombo("subgroup_id");
                accLedgerCbo['accledger']   = accLedgerForm.getCombo("parent_id");
                preTally.AccountsTeam.changeCboCustom('accledger');
                // new set up with custom filterledger
                preTally.UserProfile.applyFilterHandler(accSGroupCbo['accledger']); 
                accSGroupCbo['accledger'].setOptionWidth(300);

                 // save or cancel button click
                accLedgerForm.attachEvent("onButtonClick", function (name) {
                    if (name == "saveAccLedger") {
                        // click on save button
                        if (!preTally.AccountsTeam.commonTextValid(accLedgerForm,"description")) {
                            return false;
                        }                           
                        // check the input fields values are correct
                        var isValid = accLedgerForm.validate();
                        if (isValid) {  
                            // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                            accLedgerForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addLedger.php&flag=0"), function (loader, response) {
                                var jsonres = JSON.parse(response);
                                if (jsonres.status == 1) {

                                    dhtmlx.message({ text: jsonres.message });
                                    accLedgerForm.resetValidateCss();
                                    accLedgerForm.clear();
                                    accLedgerForm.setItemValue("eid", 0); 
                                    //preTally.AccountsTeam.clearCboCustom('accledger',1); 
                                    preTally.AccountsTeam.clearCboCustom('accledger',3); 
                                    preTally.AccountsTeam.loadLedgerList();
                                } else {
                                    dhtmlx.message({ type: "error", text: jsonres.message });
                                }
                            });
                        }else {
                            dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                        }
                    } else if (name == "CancelAccLedger") {
                        // click on cancel button
                        accLedgerForm.resetValidateCss();
                        accLedgerForm.clear();
                        accLedgerForm.setItemValue("eid", 0); 
                        //preTally.AccountsTeam.clearCboCustom('accledger',1); 
                        preTally.AccountsTeam.clearCboCustom('accledger',3); 
                    }
                }); // button click
                preTally.AccountsTeam.clearFormlayout(accLedgerLayout,accLedgerForm); // form loading permission check
            }); // form close
        },*/
        editLedger: function (eid) {
            var status      = accLedgerGrid.getUserData(eid, "status");
            if (status == 0) { // blocked item no need to edit
                dhtmlx.message({ type: "error", text: "Edit Not Possible For Blocked Items." });
                return false;
            }
            //var type        = accLedgerForm.getItemValue("type");
            //var group_id    = accLedgerForm.getItemValue("group_id");
            //var subgroup_id = accLedgerForm.getItemValue("subgroup_id");
            //accTypeCbo['accledger'].setComboValue(0);
            accLedgerForm.setItemValue("eid",eid);  
            accLedgerForm.setItemValue("office_id", accLedgerGrid.getUserData(eid, "office_id"));    
            accLedgerForm.setItemValue("sgroup_id", accLedgerGrid.getUserData(eid, "sgroup_id"));      
            accLedgerForm.setItemValue("mledger_id", accLedgerGrid.getUserData(eid, "parent_id"));
            accVendorCbo.setComboValue(0); 
            accLedgerForm.setItemValue("vendor_id", accLedgerGrid.getUserData(eid, "vendor_id"));            
            accLedgerForm.setItemValue("title", accLedgerGrid.getUserData(eid, "title"));
            accLedgerForm.setItemValue("description", accLedgerGrid.getUserData(eid, "description"));
            accLedgerForm.setItemValue("is_office", accLedgerGrid.getUserData(eid, "is_office")); 
            accLedgerForm.setItemValue("mgroup_id", accLedgerGrid.getUserData(eid, "mgroup_id"));     
            accLedgerForm.setItemValue("trans_type", accLedgerGrid.getUserData(eid, "trans_type"));     
            accLedgerForm.setItemValue("is_return", accLedgerGrid.getUserData(eid, "is_return"));     
            accLedgerForm.setItemValue("is_same_side", accLedgerGrid.getUserData(eid, "is_same_side"));     
            accLedgerForm.setItemValue("is_internal", accLedgerGrid.getUserData(eid, "is_internal"));     
            accLedgerForm.setItemValue("is_job_type", accLedgerGrid.getUserData(eid, "is_job_type"));     
            // contra entry related settings 06-11-2025
            accLedgerForm.setItemValue("is_contra", accLedgerGrid.getUserData(eid, "is_contra")); 
            if (accLedgerGrid.getUserData(eid, "is_contra") == 0 ) { 
                //accLedgerCbo['accledgerless'].setComboValue(0);
                //accLedgerCbo['accledgerplus'].setComboValue(0);
                accLedgerForm.setItemValue("less_id", 0);     
                accLedgerForm.setItemValue("plus_id", 0);
                accLedgerForm.disableItem("less_id");
                accLedgerForm.disableItem("plus_id");
                //accLedgerCbo['accledgerless'].disable();
                //accLedgerCbo['accledgerplus'].disable();
            } else {
                accLedgerForm.setItemValue("less_id", accLedgerGrid.getUserData(eid, "less_id"));     
                accLedgerForm.setItemValue("plus_id", accLedgerGrid.getUserData(eid, "plus_id"));                
                //accLedgerCbo['accledgerless'].enable();
                //accLedgerCbo['accledgerplus'].enable();                
                accLedgerForm.enableItem("less_id");
                accLedgerForm.enableItem("plus_id");
            }     
            //accLedgerForm.setItemValue("type", accLedgerGrid.getUserData(eid, "type"));
            
            /*if (type == accLedgerGrid.getUserData(eid, "type")) {
                accTypeCbo['accledger'].setComboValue(accLedgerGrid.getUserData(eid, "type"));
            }
            if (group_id == accLedgerGrid.getUserData(eid, "mgroup_id")) {
                accGroupCbo['accledger'].setComboValue(accLedgerGrid.getUserData(eid, "mgroup_id"));
            }
            if (subgroup_id == accLedgerGrid.getUserData(eid, "sgroup_id")) {
                accLedgerCbo['accledger'].setComboValue(accLedgerGrid.getUserData(eid, "parent_id"));
            } */
            accSGroupCbo['accledger'].setComboValue(0);
            accSGroupCbo['accledger'].setComboValue(accLedgerGrid.getUserData(eid, "sgroup_id"));           
            accLedgerCbo['accledger'].setComboValue(accLedgerGrid.getUserData(eid, "parent_id"));         
        },     
        loadMapLedgerList : function(extra) {
            // get all ledger List            
            var filterValue = new Array($('#accmledgerName').val(),$('#accmledgerType').val(),$( "#accmledgerStatus" ).val(),$('#accmledgerGroup').val(),$('#accmledgerItem').val(),$('#accmledgerSH').val());
            accMapLedgerGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listMapLedger.php&filter="+filterValue+extra), function() { 
                $('#total_accmledger').html("# : "+accMapLedgerGrid.getUserData("", "Data_Count")+" ");
            });                    
        },     
        exportaccmledger : function(extra) { //26-11-2025
            // get all ledger List            
            var filterValue = new Array($('#accmledgerName').val(),$('#accmledgerType').val(),$( "#accmledgerStatus" ).val(),$('#accmledgerGroup').val(),$('#accmledgerItem').val(),$('#accmledgerSH').val());
           preTally.AccountsTeam.commonExport(filterValue, 2);                    
        },        
        editMapLedger: function (eid) {            
            var status      = accMapLedgerGrid.getUserData(eid, "status");
            if (status == 0) { // blocked item no need to edit
                dhtmlx.message({ type: "error", text: "Edit Not Possible For Blocked Items." });
                return false;
            }
            accmLedgerForm.setItemValue("eid",eid);  
            //accTypeCbo['accmledger'].setComboValue(0);
            accmLedgerForm.setItemValue("mledger_id", accMapLedgerGrid.getUserData(eid, "mledger_id"));      
            accmLedgerForm.setItemValue("sledger_id", accMapLedgerGrid.getUserData(eid, "sledger_id"));      
            accmLedgerForm.setItemValue("office_id", accMapLedgerGrid.getUserData(eid, "office_id"));
            accmLedgerForm.setItemValue("sitem_id", accMapLedgerGrid.getUserData(eid, "item_id")); 
            accmLedgerForm.setItemValue("subhead_id", accMapLedgerGrid.getUserData(eid, "subhead_id"));
            /*accmLedgerForm.setItemValue("mgroup_id", accMapLedgerGrid.getUserData(eid, "mgroup_id"));      
            accmLedgerForm.setItemValue("sgroup_id", accMapLedgerGrid.getUserData(eid, "sgroup_id")); 
            accmLedgerForm.setItemValue("type", accMapLedgerGrid.getUserData(eid, "type")); */

            /*accTypeCbo['accmledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "type"));
            accGroupCbo['accmledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "mgroup_id"));
            accSGroupCbo['accledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "sgroup_id"));
            accLedgerCbo['accmledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "mledger_id"));           
            */
            accLedgerCbo['accmledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "mledger_id"));
            accSLedgerCbo['accmledger'].setComboValue(accMapLedgerGrid.getUserData(eid, "sledger_id"));
        }, 
        acc_Journal : function() {
            // add, edit, delete journal entries
            if (!dhxMiddleBlockTabs.cells("acc_Journal")) {
                dhxMiddleBlockTabs.addTab("acc_Journal", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Map Ledger Data &nbsp; ", 185);
                dhxMiddleBlockTabs.tabs("acc_Journal").setActive();
                // layout and heading start
                var accJournalLayout = dhxMiddleBlockTabs.cells("acc_Journal").attachLayout('2E');
                accJournalLayout.cells("a").setText("Maping Ledger Data Pending");
                accJournalLayout.cells("b").setText("List Mapped Ledger Data");
                accJournalLayout.cells("a").setWidth(600);
                // status bar or pagination and count
                accJournalLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accjournal','accexp'),
                    height: 35
                });
                // status bar or pagination and count 2
                accJournalLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accmapjnl','accexp'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accJournalGrid = accJournalLayout.cells("b").attachGrid();
                accJournalGrid.setHeader("SlNo, \
                   Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accjournal' class='accGridSort' />\
                   "+preTally.AccountsTeam.commonDMYCbo('accjournal')+",\
                   Track No <input type='text' class = 'acc_grid_txt_fltr' gridType='accjournal' id ='accjournalTrack' style='width: 80%;' placeholder='Search'>,\
                   Ledger Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accjournal' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accjournal' id ='accjournalLedger' style='width: 80%;' placeholder='Enter Ledger Name'>,\
                   Branch <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='branch' gridType='accjournal' class='accGridSort' /> <div  data-type='accjournal' id='accjournalbranch' style='width: 97%;' placeholder='Branch Name'></div>,\
                   Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amount' gridType='accjournal' class='accGridSort' /> <input type='number' class = 'acc_grid_txt_fltr' gridType='accjournal' id='accjournalamt' style='width: 80%;' placeholder='Amount'>,\
                   Updated At<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='updated' gridType='accjournal' class='accGridSort' />, \
                   Updated By, Action");
                accJournalGrid.setInitWidths("60,110,90,*,*,90,100,120,100");
                accJournalGrid.setColAlign("center,left,left,left,left,right,center,left,center");
                accJournalGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
                accJournalGrid.setColSorting("na,na,na,na,na,na,na,na");              
                accJournalGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                accJournalGrid.enableMultiline(true);
                preTally.Attendance.commonGridDefine(accJournalGrid, 'accjournal');
                
                // date calander filters
               /*
               <input type='calendar' data-type='accjournal' id='accjournalDate' style='width: 90%;' placeholder='Date'>,\
                accjournalDate = new dhtmlXCalendarObject("accjournalDate");
                accjournalDate.setDateFormat("%d-%m-%Y");
                accjournalDate.setSensitiveRange(null, new Date());
                accjournalDate.setDate(null);
                accjournalDate.hideTime();
                accjournalDate.attachEvent("onHide", function(date, state){
                    preTally.AccountsTeam.loadJournals(''); 
                });
                accjournalDate.attachEvent("onShow", function(date, state){
                    accjournalDate.setDate(null);
                });*/ 

                 // server side sort fields fetching
                //preTally.AccountsTeam.customServerSort();              
                preTally.AccountsTeam.accTextFilters();
                accJournalBranch = new dhtmlXCombo("accjournalbranch");
                preTally.AccountsTeam.loadLocationCbo(accJournalBranch,"accjournalbranch","accjournal");
                // Load Data into saved journal Grid list   
                preTally.AccountsTeam.loadJournals(''); 


                //----------------- Attach Map Journal Grid --------------------//
                accMapJnlGrid = accJournalLayout.cells("a").attachGrid();
                accMapJnlGrid.setHeader("<input type='checkbox' name='accmapjnlCheck' id='accmapjnlCheck'/> <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accmapjnl' class='accGridSort' />, \
                 Item Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='item' gridType='accmapjnl' class='accGridSort' />\
                 <input type='text' class = 'acc_grid_txt_fltr' gridType='accmapjnl' id ='accAJItem' style='width: 75%;' placeholder='Enter Item Name'>,\
                 Ledger Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accmapjnl' class='accGridSort' />\
                 <input type='text' class = 'acc_grid_txt_fltr' gridType='accmapjnl' id ='accAJLedgr' style='width: 75%;' placeholder='Enter Ledger Name'>,\
                 Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amt' gridType='accmapjnl' class='accGridSort' /> <input type='number' class = 'acc_grid_txt_fltr' gridType='accmapjnl' id='accAJamt' style='width: 90%;' placeholder='Amount'>,\
                 Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accmapjnl' class='accGridSort' /> \
                 <input type='calendar' data-type='accmapjnl' id='accAJDate' style='width: 90%;' placeholder='Date'>,\
                 <input type='hidden' id='hidNewLedrDate' value=''>\
                 <input type='button' id='accmapjnlSave' name='accmapjnlSave' class='custButton' value='Save' onclick='preTally.AccountsTeam.saveJournal()' />");
                accMapJnlGrid.setInitWidths("65,*,*,100,100,100,120");                  
                accMapJnlGrid.setColAlign("center,left,left,right,center,center");
                accMapJnlGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accMapJnlGrid.setColSorting("na,na,na,na,na,na");              
                accMapJnlGrid.enableTooltips("false,false,false,false,false,false"); 
                accMapJnlGrid.enableMultiline(true);              
                preTally.Attendance.commonGridDefine(accMapJnlGrid, 'accmapjnl');
                // date calander filters
                accmapjnlDate = new dhtmlXCalendarObject("accAJDate");
                accmapjnlDate.setDateFormat("%d-%m-%Y");
                accmapjnlDate.setSensitiveRange(null, new Date());
                accmapjnlDate.setDate(null);
                accmapjnlDate.hideTime();
                accmapjnlDate.attachEvent("onHide", function(date, state){
                    preTally.AccountsTeam.loadItemJournal(''); 
                });
                accmapjnlDate.attachEvent("onShow", function(date, state){
                    accmapjnlDate.setDate(null);
                });

                  // server side sort fields fetching
                preTally.AccountsTeam.customServerSort(); // common for both grid
                preTally.AccountsTeam.accTextFilters();
                // load data into the grid
                preTally.AccountsTeam.loadItemJournal(''); 
                // on click on the top check box
                document.getElementById('accmapjnlCheck').addEventListener('change', function() {
                    var isChecked   = this.checked;
                    var checkboxes  = document.querySelectorAll('.itmchkbox');

                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = isChecked;
                    });
                    preTally.AccountsTeam.enableDisabledButton(); 
                });
                
                // click on the next or previous of pagination then check the check box if selected.
                accMapJnlGrid.attachEvent("onPageChanged", function(ind,fInd,lInd){
                    // your code here
                    var ischecked   = document.getElementById('accmapjnlCheck').checked;
                    setTimeout(function(){ 
                        var checkboxes  = document.querySelectorAll('.itmchkbox');
                        
                        checkboxes.forEach(function(checkbox) {
                            checkbox.checked = ischecked;                           
                        });
                        preTally.AccountsTeam.changeCheckItems();
                        preTally.AccountsTeam.enableDisabledButton(); 
                    },1500);
                });

            }else {
                dhxMiddleBlockTabs.tabs("acc_Journal").setActive();
            }
        },            
        loadJournals : function(extra) {
            // list all saved journal entries
            //var datefilt     = accjournalDate.getDate(true);            
            //var filterValue = new Array($('#accjournalLedger').val(),$('#accjournalbranch').val(),$("#accjournalTrack").val(),datefilt);
            var filterValue = new Array($('#accjournalLedger').val(),$('#accjournalbranch').val(),$("#accjournalTrack").val(),'',$("#accjournalMonths").val(),$("#accjournalYears").val(),$("#accjournalDays").val(),$("#accjournalamt").val());
            accJournalGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listJournal.php&filter="+filterValue+extra), function() { 
                $('#total_accjournal').html("# : "+accJournalGrid.getUserData("", "Data_Count")+" ");
            }); 
        },
        // export above showed 26-11-2025
        exportaccjournal: function() {
            var filterValue = new Array($('#accjournalLedger').val(),$('#accjournalbranch').val(),$("#accjournalTrack").val(),'',$("#accjournalMonths").val(),$("#accjournalYears").val(),$("#accjournalDays").val(),$("#accjournalamt").val());
            preTally.AccountsTeam.commonExport(filterValue, 1);
        },
        loadItemJournal : function(extra) {
            //console.log("loading items need to added into journals"); 
            var datefilt     = accmapjnlDate.getDate(true);
            var filterValue = new Array($('#accAJItem').val(),$('#accAJLedgr').val(),datefilt,$('#accAJamt').val());
            accMapJnlGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/addJournal.php&filter="+filterValue+extra), function() { 
                $('#total_accmapjnl').html("# : "+accMapJnlGrid.getUserData("", "Data_Count")+" ");
                preTally.AccountsTeam.changeCheckItems();  
                preTally.AccountsTeam.enableDisabledButton();              
            });
        },
        exportaccmapjnl: function() {
            var datefilt     = accmapjnlDate.getDate(true);
            var filterValue = new Array($('#accAJItem').val(),$('#accAJLedgr').val(),datefilt,$('#accAJamt').val());
            preTally.AccountsTeam.commonExport(filterValue, 3);
        },
        changeCheckItems: function() {  //unchecked sub item then main chek also unchecked
            $('.itmchkbox').unbind('change');
            $('.itmchkbox').change(function () {
                if (!$(this).is(':checked')) {
                    document.getElementById('accmapjnlCheck').checked = false;
                }  
                preTally.AccountsTeam.enableDisabledButton();             
            });            
        },
        enableDisabledButton: function() {
            var checkboxes  = document.querySelectorAll('.itmchkbox:checked');
            if (checkboxes.length > 0) {
                document.getElementById('accmapjnlSave').disabled = false;
            } else {
                document.getElementById('accmapjnlSave').disabled = true;
            }
        }, 
        saveJournal : function() {
            // save all journals (from already mapped itesm data)
            var checkboxes  = document.querySelectorAll('.itmchkbox:checked');
            const values = Array.from(checkboxes).map(checkbox => checkbox.value);
            if (values.length > 0) {
                $("#hidNewLedrDate").val("");
                dhtmlx.confirm({
                    title: "Confirm Group Save",
                    type: "confirm-warning",
                    ok: "Yes", cancel: "No",
                    text: "<div style='text-align:left; padding-left:8px;'><div><div style='float:left;'>If you want to change all the selected entries date in to new date : <input type='date' style='width:140px;' placeholder='dd/mm/yyyy' id='newLdgrDate' name='newLdgrDate'/></div><br></div><br><br><br><b>Do you want to Continue ?</b></div>",
                    callback: function (result) {
                        if (result) {
                            $.post(preTally.Initialize.encryptURL("warehouse/accounts/addJournal.php&flag=1&bs_ids="+values+"&newdate="+$("#hidNewLedrDate").val()), function (response) {
                                var jsonResponse = JSON.parse(response);
                                if (jsonResponse.status == 1) {
                                    dhtmlx.message({ text: jsonResponse.message });
                                    preTally.AccountsTeam.loadItemJournal(''); 
                                    preTally.AccountsTeam.loadJournals('');  
                                } else {
                                    dhtmlx.message({ type: "error", text: jsonResponse.message });
                                }
                            }); // post to server
                        } // if check based on user pool 
                    } // call back
                }); // confirm check
                // common date for all selected entries 18-12-2025
                $('#newLdgrDate').unbind('click');          
                $('#newLdgrDate').change(function () {
                    $("#hidNewLedrDate").val($(this).val());
                });
            } else {
                dhtmlx.message({ type: "error", text: "Please select the entries from the list" });
            }
        },
        loadItemdataForm: function(id,bsid, jid, flags) {
            if (typeof flags == "undefined") {
                flags = 6;
            }
            // add or edit journal entry popup (click on edit button)
            dhxJournalFrmDet    = new dhtmlXWindows();
            jrnlFrmWin     = dhxJournalFrmDet.createWindow("journalEditWid", 200, 200, 850, 550);
            jrnlFrmWin.center();
            jrnlFrmWin.button("minmax1").hide();
            jrnlFrmWin.button("minmax2").hide();
            jrnlFrmWin.button("park").hide();
            jrnlFrmWin.setModal(true);
            jrnlFrmWin.setText("Mapping Ledger Entry");
            journalForm     = jrnlFrmWin.attachForm();
            preTally.Settings.progressOn(true, jrnlFrmWin, null);

            var params = "&bsid="+bsid+"&jid="+id+"&id="+jid+"&flags="+flags;
            journalForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/editJournal.php&"+params), function() {
                preTally.Settings.progressOff(true, jrnlFrmWin, null);
                 // fuature bill date block
                journalForm.getCalendar("date_entry").setSensitiveRange(null, new Date());
                if (id > 0 && journalForm.getItemValue("parent_id") <= 0) {
                    jrnlFrmWin.close();
                    flags = journalForm.getItemValue("flags");
                    if (flags == 9 ) { //05-01-2026
                        preTally.AccountsTeam.chgAftMapReport('');
                    } else {
                        preTally.AccountsTeam.loadItemJournal(''); 
                        preTally.AccountsTeam.loadJournals('');  
                    }
                    return false;
                }
                journalForm.hideItem('ledgerContainer'); // ledger listing container hide by default
                // loading company and branch combos
                var tocompanycbo      = journalForm.getCombo("company_id");
                var tobranchcbo       = journalForm.getCombo("branch");
                var vendorcbo         = journalForm.getCombo("vendor_id");
                var ietypecbo         = journalForm.getCombo("ie_type");
                tocompanycbo.setOptionWidth(450);
                tobranchcbo.setOptionWidth(450);
                vendorcbo.setOptionWidth(450);
                journalForm.getCombo("ba_id").setOptionWidth(350);
                preTally.UserProfile.applyFilterHandler(journalForm.getCombo("ba_id"));
                var tobranch_id       = journalForm.getItemValue("branch_id");
                var tocompany_id      = tocompanycbo.getSelectedValue();
                // load branch combo default
                preTally.AccountsTeam.loadBranchCbo(tobranchcbo, tocompany_id, tobranch_id, 'editJournal');
                preTally.UserProfile.applyFilterHandler(tocompanycbo);
                preTally.UserProfile.applyFilterHandler(vendorcbo);
                // on change company combo then load the branch combo
                tocompanycbo.attachEvent("onChange", function () {
                    tocompany_id = tocompanycbo.getSelectedValue();
                    tobranch_id  = journalForm.getItemValue("branch_id");
                    tobranchcbo.setComboText(''); 
                    preTally.AccountsTeam.loadBranchCbo(tobranchcbo, tocompany_id, tobranch_id, 'editJournal');
                    preTally.AccountsTeam.hideshowAmt(journalForm, tocompany_id,journalForm.getItemValue("office_id")); //14-01-2026 
                });                
                preTally.AccountsTeam.hideshowAmt(journalForm, tocompany_id,journalForm.getItemValue("office_id")); //14-01-2026 
                // form input change track to branch finding function start...
                journalForm.attachEvent("onBlur",function(name) {//29-01-2026
                    var trkno =journalForm.getItemValue("trackno");
                    if (name == 'trackno' && trkno != '') { 
                        var locdata  = preTally.Attendance.commonPostSyn("requisites/customJsonData.php&flag=8&trackno="+trkno);
                        if (locdata.data > 0) {
                            tobranchcbo.setComboValue(locdata.data);
                        }
                    }
                });
                // form input change function start...
                journalForm.attachEvent("onInputChange",function(name, value, form) {
                    // converted Amt section start 14-01-2025
                    if (name == "amount_ratio" || name == "amount") { 
                        var totmat  = parseFloat(journalForm.getItemValue("amount"));
                        var ratio   = parseFloat(journalForm.getItemValue("amount_ratio"));
                        if (totmat > 0 && ratio > 0) {
                            journalForm.setItemValue("converted", preTally.AccountsTeam.roundToDecimal(parseFloat(totmat*ratio),4)); 
                        } else {
                            journalForm.setItemValue("converted", 0); 
                        }
                        return false;
                    } else if (name == "converted") {
                        var totmat  = parseFloat(journalForm.getItemValue("amount"));
                        if (totmat > 0 && value > 0) {
                            journalForm.setItemValue("amount_ratio", preTally.AccountsTeam.roundToDecimal(parseFloat(value/totmat),4)); 
                        } else {
                            journalForm.setItemValue("amount_ratio", 0); 
                        }
                        return false;
                    } else if (name != 'search_ledger') {
                        return false;
                    }                     

                    if (value.trim() == '') {
                        journalForm.hideItem('ledgerContainer');
                    } else { // load the grid 

                        journalForm.showItem('ledgerContainer');
                        ledgerGrid = new dhtmlXGridObject(journalForm.getContainer("ledgerContainer"));                            
                        ledgerGrid.setHeader("Slno,Ledger,Parent Ledger,Group, Type");
                        ledgerGrid.setInitWidths("50,210,175,175,85");
                        ledgerGrid.setColAlign("left,left,left,left,left");
                        ledgerGrid.setColSorting("na,na,na,na,na");
                        ledgerGrid.setColTypes("ro,ro,ro,ro,ro");
                        ledgerGrid.enableColSpan(true); 
                        ledgerGrid.enableTooltips("false,false,false,false,false"); 
                        ledgerGrid.enableAutoHeight(true); 
                        ledgerGrid.enableMultiline(true); 
                        ledgerGrid.init();       
                        //ledgerGrid.addRow("newRowId", ["1","New Book","New Author","12.50","154"]);
                        // auto list the ledger based on the search words
                        ledgerGrid.loadXML(preTally.Initialize.encryptURL("requisites/accounts/gridLedger.php&search=" + value), function() { 
                            if (ledgerGrid.getUserData("", "Data_Count") <= 0) {
                                journalForm.hideItem('ledgerContainer');
                            } else {
                                ledgerGrid.attachEvent("onRowSelect", function(id,ind){
                                    var ledgerid    = ledgerGrid.getUserData(id, "ledger_id");
                                    var ledgername  = ledgerGrid.getUserData(id, "ledger_name");
                                    journalForm.setItemValue("ledger_name", ledgername); 
                                    journalForm.setItemValue("ledger_id", ledgerid); 
                                    journalForm.hideItem('ledgerContainer');
                                    journalForm.setItemValue("search_ledger", ''); 
                                    ietypecbo.clearAll();
                                    ietypecbo.setComboText(''); 
                                    console.log(ietypecbo.getSelectedValue()+"======"+ledgerGrid.getUserData(id, "ie_type"));
                                    if (ledgerGrid.getUserData(id, "ie_type") == 1) {            
                                        ietypecbo.addOption('2', 'Subtract 2');
                                        ietypecbo.addOption('1', 'Add 1');
                                    } else {
                                        ietypecbo.addOption('1', 'Subtract 1');
                                        ietypecbo.addOption('2', 'Add 2');
                                    }
                                    journalForm.setItemValue("ie_type",ledgerGrid.getUserData(id, "ie_type"));
                                    //
                                }); // on row select
                            } // data present - else 
                        }); // grid data loading
                    } // search not null
                }); //search or input change 

                // confirm journal entry grid
                cJournalGrid = new dhtmlXGridObject(journalForm.getContainer("journalContainer")); 
                cJournalGrid.setHeader("Ledger, Company, Branch, Amount, Vendor, Action");
                cJournalGrid.setInitWidths("210,150,125,85,65,80");
                cJournalGrid.setColAlign("left,left,left,right,center,center");
                cJournalGrid.setColSorting("na,na,na,na,na,na");
                cJournalGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                cJournalGrid.enableColSpan(true); 
                cJournalGrid.enableTooltips("false,false,false,false,false,false"); 
                cJournalGrid.enableAutoHeight(true); 
                cJournalGrid.enableMultiline(true); 
                cJournalGrid.init();
                journalForm.hideItem('journalContainer');
                journalForm.hideItem('saveAcJournal');
                var confirm_entry   = journalForm.getItemValue("confirm_entry");
                if (confirm_entry != "") {
                    preTally.AccountsTeam.loadCJournalGrid(JSON.parse(confirm_entry));
                }
                //cJournalGrid.addRow("newRowId1", ["New Book","New Author","12.50","154",preTally.AccountsTeam.customJournalAction(1)]);
                //cJournalGrid.setUserData("newRowId1","priority","high");
                // save or cancel button click
                journalForm.attachEvent("onButtonClick", function (name) {
                    journalForm.resetValidateCss();
                    var confirm_entry   = journalForm.getItemValue("confirm_entry");
                    var confirm_detail  = [];
                    var ledger_id       = journalForm.getItemValue("ledger_id");
                    var ledger_name     = journalForm.getItemValue("ledger_name");
                    var trackno         = journalForm.getItemValue("trackno");
                    var amount          = journalForm.getItemValue("amount");
                    var converted       = journalForm.getItemValue("converted");
                    var ratio           = journalForm.getItemValue("amount_ratio");
                    var remarks         = journalForm.getItemValue("remarks");
                    var temp_id         = journalForm.getItemValue("temp_id");
                    var tocompany_id    = tocompanycbo.getSelectedValue();
                    var tobranch_id     = tobranchcbo.getSelectedValue();
                    var vendor_id       = vendorcbo.getSelectedValue();
                    var vendor_name     = vendorcbo.getComboText();
                    var tocompany_name  = tocompanycbo.getComboText();
                    var tobranch_name   = tobranchcbo.getComboText();
                    if (name == "addAcJournal") { // click on add+ button
                        var isValid = journalForm.validate();
                        if (isValid) { 
                            
                            if (!(ledger_id > 0 && tocompany_id > 0 && tobranch_id > 0)) {
                                dhtmlx.message({ type: "error", text: "Please select all the fields"});
                                return false;
                            } // check form elements empty
                            
                            var ii              = 0; 
                            if (confirm_entry != "") {
                                confirm_detail  = JSON.parse(confirm_entry);
                                ii              = parseInt(confirm_detail.length);
                            } // if 
                            if (temp_id != "" && confirm_entry != "") {

                                confirm_detail.forEach(function(rowdata, index) {
                                    if (rowdata.id == temp_id) {
                                        confirm_detail[index].ledger_id     = ledger_id;
                                        confirm_detail[index].ledger_name   = ledger_name;
                                        confirm_detail[index].trackno       = trackno;
                                        confirm_detail[index].tocompany_id  = tocompany_id;
                                        confirm_detail[index].tobranch_id   = tobranch_id;
                                        confirm_detail[index].tocompany_name= tocompany_name;
                                        confirm_detail[index].tobranch_name = tobranch_name;
                                        confirm_detail[index].amount        = amount;
                                        confirm_detail[index].converted     = converted;
                                        confirm_detail[index].ratio         = ratio;
                                        confirm_detail[index].remarks       = remarks;
                                        confirm_detail[index].vendor_id     = vendor_id;
                                        confirm_detail[index].vendor_name   = vendor_name;
                                    }
                                });
                            } else {
                                confirm_detail.push({ "ledger_id": ledger_id, "ledger_name": ledger_name, 
                                "tocompany_id": tocompany_id, "tobranch_id": tobranch_id, 
                                "tocompany_name": tocompany_name, "tobranch_name": tobranch_name, "vendor_name":vendor_name, 
                                "amount": amount, "converted": converted, "ratio": ratio, "remarks":remarks, 
                                "id": "tmp"+ii, "server_id":0, "vendor_id":vendor_id, "trackno":trackno });
                            } // else                         
                            journalForm.setItemValue("confirm_entry", JSON.stringify(confirm_detail)); 

                            preTally.AccountsTeam.loadCJournalGrid(confirm_detail);
                            journalForm.setItemValue("ledger_name", ""); 
                            journalForm.setItemValue("trackno", ""); 
                            journalForm.setItemValue("ledger_id", 0);
                            journalForm.setItemValue("temp_id", '');
                            journalForm.setItemValue("amount", '');
                            journalForm.setItemValue("converted", '');
                            journalForm.setItemValue("amount_ratio", '');
                            journalForm.setItemValue("remarks", '');
                            journalForm.setItemValue("vendor_id", '');
                        }// validation check end
                            
                    } else if (name == "saveAcJournal") { 
                        // click on the save button                        
                        if (confirm_entry == "" && !(ledger_id > 0 && tocompany_id > 0 && tobranch_id > 0)) {
                            dhtmlx.message({ type: "error", text: "Please enter or select data and click to Add button"});
                            return false;
                        }
                        if (confirm_entry != "") { // already click on add button
                            confirm_detail  = JSON.parse(confirm_entry);
                            if (temp_id != "" && ledger_id > 0 && amount > 0  && tobranch_id > 0) {
                                confirm_detail.forEach(function(rowdata, index) {
                                    if (rowdata.id == temp_id) {
                                        confirm_detail[index].ledger_id     = ledger_id;
                                        confirm_detail[index].ledger_name   = ledger_name;
                                        confirm_detail[index].trackno       = trackno;
                                        confirm_detail[index].tocompany_id  = tocompany_id;
                                        confirm_detail[index].tobranch_id   = tobranch_id;
                                        confirm_detail[index].tocompany_name= tocompany_name;
                                        confirm_detail[index].tobranch_name = tobranch_name;
                                        confirm_detail[index].amount        = amount;
                                        confirm_detail[index].converted     = converted;
                                        confirm_detail[index].ratio         = ratio;
                                        confirm_detail[index].remarks       = remarks;
                                        confirm_detail[index].vendor_id     = vendor_id;
                                        confirm_detail[index].vendor_name   = vendor_name;
                                    }
                                });
                                journalForm.setItemValue("confirm_entry", JSON.stringify(confirm_detail)); 
                                preTally.AccountsTeam.loadCJournalGrid(confirm_detail);
                            }                            
                        } else {
                             var isValid = journalForm.validate();
                            if (isValid) {
                                confirm_detail.push({ "ledger_id": ledger_id, "ledger_name": ledger_name, 
                                "tocompany_id": tocompany_id, "tobranch_id": tobranch_id, 
                                "tocompany_name": tocompany_name, "tobranch_name": tobranch_name, "vendor_name":vendor_name, 
                                "amount": amount, "converted": converted, "ratio": ratio, "remarks":remarks, 
                                "id": "tmp0", "server_id":0, "vendor_id":vendor_id, "trackno":trackno });
                                journalForm.setItemValue("confirm_entry", JSON.stringify(confirm_detail));                                 
                                preTally.AccountsTeam.loadCJournalGrid(confirm_detail);
                            } else {
                                return false;
                            }
                        }// validation check end
                        // save or edit the journals based on the balance sheet id 
                        journalForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addJournal.php&flag=2"), function (loader, response) {
                            var jsonres = JSON.parse(response);
                            if (jsonres.status == 1) {

                                dhtmlx.message({ text: jsonres.message });
                                flags = journalForm.getItemValue("flags");
                                if (flags == 9 ) { //05-01-2026
                                    preTally.AccountsTeam.chgAftMapReport('');
                                } else {
                                    preTally.AccountsTeam.loadItemJournal(''); 
                                    preTally.AccountsTeam.loadJournals('');  
                                }                                
                                jrnlFrmWin.close();
                            } else {
                                dhtmlx.message({ type: "error", text: jsonres.message });
                            }
                        });


                    }else if (name == "clearAcJournal") {
                        journalForm.setItemValue("ledger_name", ""); 
                        journalForm.setItemValue("trackno", ""); 
                        journalForm.setItemValue("ledger_id", 0);
                        journalForm.setItemValue("temp_id", '');
                        journalForm.setItemValue("amount", '');
                        journalForm.setItemValue("converted", '');
                        journalForm.setItemValue("amount_ratio", '');
                        journalForm.setItemValue("remarks", '');
                        journalForm.setItemValue("vendor_id", '');
                        var confirm_entry   = JSON.parse(journalForm.getItemValue("confirm_entry"));
                        preTally.AccountsTeam.loadCJournalGrid(confirm_entry);
                    } else if (name == "deleteAcJournal") {
                        flags = journalForm.getItemValue("flags");
                        preTally.AccountsTeam.deleteJournal(flags,0,journalForm.getItemValue("parent_id"),journalForm.getItemValue("balance_sheet_id"));                        
                    } // delete button click

                }); // form button click
            }); // form loading
        },
        customJournalAction: function(id) {
            if (journalForm.getItemValue("parent_id") > 0) {
                return "<div><div style='width:46%;float:left;cursor:pointer;' onclick='preTally.AccountsTeam.editCJournal("+id+")'>"
            +"<img src='images/icon/pencil.png' style='margin:2px 0;height:13px;'/></div>";
            } else {
                return "<div><div style='width:46%;float:left;cursor:pointer;border-right:1px solid;' onclick='preTally.AccountsTeam.editCJournal("+id+")'>"
            +"<img src='images/icon/pencil.png' style='margin:2px 0;height:13px;'/></div>"
            +"<div style='width:30%;float:right;cursor:pointer;' onclick='preTally.AccountsTeam.deleteCJournal("+id+")'>"
            +"<img src='images/icon/trash.png' style='margin:2px 3px;height:13px;'/></div></div>";
            }
            
        },
        editCJournal: function(id) {
            // click on the edit button in the popup
            var confirm_entry   = JSON.parse(journalForm.getItemValue("confirm_entry"));
            var rowdata         = confirm_entry[id];
            //cJournalGrid.deleteRow("nr"+id);
            journalForm.setItemValue("ledger_name", rowdata.ledger_name); 
            journalForm.setItemValue("trackno", rowdata.trackno); 
            journalForm.setItemValue("ledger_id", rowdata.ledger_id);
            journalForm.setItemValue("temp_id", rowdata.id);
            journalForm.setItemValue("amount", rowdata.amount);
            journalForm.setItemValue("converted", rowdata.converted);
            journalForm.setItemValue("amount_ratio", rowdata.ratio);
            journalForm.setItemValue("remarks", rowdata.remarks);
            journalForm.setItemValue("vendor_id", rowdata.vendor_id);
            journalForm.setItemValue("branch_id", rowdata.tobranch_id);
            journalForm.getCombo("branch").setComboValue(rowdata.tobranch_id);
            journalForm.getCombo("company_id").setComboValue(rowdata.tocompany_id);
        },
        deleteCJournal: function(id) {
            // delete the popup journal temporary list
            //cJournalGrid.deleteRow("nr"+id);
            var confirm_entry   = JSON.parse(journalForm.getItemValue("confirm_entry"));
            confirm_entry.splice(id, 1);
            var confirm_detail  = [];
            if (confirm_entry.length > 0) {
                var j = 0;
                confirm_entry.forEach(function(rowdata, index) {
                    rowdata.id = "tmp"+j;
                    confirm_detail[j] = rowdata;   
                    j++;                 
                });
                journalForm.setItemValue("confirm_entry", JSON.stringify(confirm_detail));
                preTally.AccountsTeam.loadCJournalGrid(confirm_detail);
            } else {
                journalForm.setItemValue("confirm_entry", '');
                cJournalGrid.clearAll();                
                journalForm.hideItem('journalContainer');
                journalForm.hideItem('saveAcJournal');
                journalForm.showItem('journalconfirmmsg');
            } // no data present else    
            // clear form elements        
            journalForm.setItemValue("ledger_name", ""); 
            journalForm.setItemValue("trackno", ""); 
            journalForm.setItemValue("ledger_id", 0);
            journalForm.setItemValue("temp_id", '');
            journalForm.setItemValue("amount", '');
            journalForm.setItemValue("converted", '');
            journalForm.setItemValue("amount_ratio", '');
            journalForm.setItemValue("remarks", '');
            journalForm.setItemValue("vendor_id", '');
        },
        loadCJournalGrid : function(arydata) {
            // temporary journal list for confirmation
            cJournalGrid.clearAll();
            if (arydata.length > 0) {

                journalForm.showItem('journalContainer');
                journalForm.showItem('saveAcJournal');
                journalForm.hideItem('journalconfirmmsg');
            } else {
                journalForm.hideItem('journalContainer');
                journalForm.hideItem('saveAcJournal');
                journalForm.showItem('journalconfirmmsg');
            }
            var total_amount = 0.00;
            arydata.forEach(function(rowdata, index) {
              var vendorcol = (rowdata.vendor_id > 0) ? "<img src='images/icon/info_18.png' onmouseout='preTally.Settings.hideLabel(this);'  onmouseover='preTally.Settings.showLabel(this, \""+rowdata.vendor_name+"\");' />" : "-";
              var ledger_name = rowdata.ledger_name+((rowdata.trackno != "" && rowdata.trackno != null) ? "  - ("+rowdata.trackno+")":"");
              cJournalGrid.addRow("nr"+index, [ledger_name,rowdata.tocompany_name,rowdata.tobranch_name,rowdata.amount,vendorcol,preTally.AccountsTeam.customJournalAction(index)]);
              cJournalGrid.setUserData("nr"+index,"temp_id",rowdata.id);
              total_amount = parseFloat(total_amount+parseFloat(rowdata.amount)); 
              console.log("total:",total_amount, " New amt:",rowdata.amount);  
              journalForm.setItemValue("total_camt", total_amount.toFixed(2));          
            }); // loop the array elements ends
            //total_amount = total_amount.toFixed(2);
            console.log("Final total:",total_amount);  
        },
        deleteJournal: function (flag, id, parent_id, bs_id) {
            var sublabel = (flag == 4) ? "Block":"UnBlock";
            // delete journals based on flag
            dhtmlx.confirm({
                title: "Confirm",
                type: "confirm-warning",
                ok: "Yes", cancel: "No",
                text: "Do you want to "+(flag == 4 || flag == 8 ? sublabel:"Delete")+"?",
                callback: function (result) {
                    if (result && (id > 0 || ((flag == 4 || flag == 8) && bs_id > 0) || ((flag == 6 || flag == 9 ) && parent_id > 0))) {
                        $.post(preTally.Initialize.encryptURL("warehouse/accounts/addJournal.php&flag="+flag+"&id="+id+"&parent_id="+parent_id+"&bs_id="+bs_id), function (response) {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.status == 1) {
                                dhtmlx.message({ text: jsonResponse.message });
                                if (flag == 6 || flag == 9) {
                                    jrnlFrmWin.close();
                                }
                                if (flag == 9) { //05-01-26
                                    preTally.AccountsTeam.chgAftMapReport(''); 
                                }else if (flag == 8 ) {
                                    preTally.AccountsTeam.loadBlockTransList(''); 
                                }else if (flag == 11 ) {
                                    preTally.AccountsTeam.loadExtraDatas(''); 
                                } else {
                                    preTally.AccountsTeam.loadItemJournal(''); 
                                    if (flag != 4 ) {
                                        preTally.AccountsTeam.loadJournals(''); 
                                    }
                                }
                            } else {
                                dhtmlx.message({ type: "error", text: jsonResponse.message });
                            }
                        });
                    } // if click on yes 
                } // call back 
            }); // delete confirm checking
        },
        // Tds management start..... 02-09-25
        acc_TdsRules : function() {
            if (!dhxMiddleBlockTabs.cells("acc_TdsRules")) {
                dhxMiddleBlockTabs.addTab("acc_TdsRules", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; TDS Rules &nbsp; ", 130);
                dhxMiddleBlockTabs.tabs("acc_TdsRules").setActive();
                // layout and heading start
                var accTdsLayout = dhxMiddleBlockTabs.cells("acc_TdsRules").attachLayout('2U');
                accTdsLayout.cells("a").setText("Add TDS Rules");
                accTdsLayout.cells("b").setText("List TDS Rule");
                accTdsLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accTdsLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('acctds'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accTdsGrid = accTdsLayout.cells("b").attachGrid();
                accTdsGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='acctds' class='accGridSort' />, \
                Rule Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='name' gridType='acctds' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='acctds' id ='acctdsName' style='width: 90%;' placeholder='Enter Rule Name'>,\
                Section - Code,\
                Percentage <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='percent' gridType='acctds' class='accGridSort' />,\
                Time Period <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='time' gridType='acctds' class='accGridSort' />, \
                Status "+preTally.AccountsTeam.commonStatusCbo('acctds','1')+", Action");
                accTdsGrid.setInitWidths("50,*,100,80,120,100,150");
                accTdsGrid.setColAlign("center,left,left,center,left,Center,center");
                accTdsGrid.setColTypes("ro,ro,ro,ro,ro,combo,ro");
                accTdsGrid.setColSorting("na,na,na,na,na,na,na");              
                accTdsGrid.enableTooltips("false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accTdsGrid, 'acctds');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadTdsList('');   
                // Grid Click (edit cell)
                preTally.AccountsTeam.editStatusColumn(accTdsGrid,5,"addTds.php");  
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                //----------------- Attach FORM --------------------//
                accTdsForm = accTdsLayout.cells("a").attachForm();
                accTdsForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addTds.php"), function () {
                    // save or cancel button click
                    accTdsForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccTds") {
                            // click on save button
                            /*if (!preTally.AccountsTeam.commonTextValid(accTdsForm,"description")) {
                                return false;
                            } */                      
                            // check the input fields values are correct
                            var isValid = accTdsForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accTdsForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addTds.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        accTdsForm.resetValidateCss();
                                        accTdsForm.clear();
                                        accTdsForm.setItemValue("eid", 0); 
                                        preTally.AccountsTeam.loadTdsList('');
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                         } else if (name == "CancelAccTds") {
                            // click on cancel button
                            accTdsForm.resetValidateCss();
                            accTdsForm.clear();
                            accTdsForm.setItemValue("eid", 0); 
                        } // cancel button
                    }); // button click
                }); // form close
            } else {
                dhxMiddleBlockTabs.tabs("acc_TdsRules").setActive();
            }
        },
        loadTdsList: function(extra) {
            // get all TDS List
            var filterValue = new Array($('#acctdsName').val(),$( "#acctdsStatus" ).val());
            accTdsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listTds.php&filter="+filterValue+extra), function() { 
                $('#total_acctds').html("# : "+accTdsGrid.getUserData("", "Data_Count")+" ");
            });
        },
        editTds: function (eid) {            
            accTdsForm.setItemValue("eid",eid);  
            accTdsForm.setItemValue("status", accTdsGrid.getUserData(eid, "status"));    
            accTdsForm.setItemValue("percentage", accTdsGrid.getUserData(eid, "percentage"));      
            accTdsForm.setItemValue("section", accTdsGrid.getUserData(eid, "section"));             
            accTdsForm.setItemValue("code", accTdsGrid.getUserData(eid, "code"));
            accTdsForm.setItemValue("rule_name", accTdsGrid.getUserData(eid, "rule_name"));
            accTdsForm.setItemValue("start_date", accTdsGrid.getUserData(eid, "start_date"));         
        },     
        //vendor management start..... 02-09-25
        acc_Vendors : function() {
            if (!dhxMiddleBlockTabs.cells("acc_Vendors")) {
                dhxMiddleBlockTabs.addTab("acc_Vendors", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Vendors Or Debtor Or Creditor &nbsp; ", 245);
                dhxMiddleBlockTabs.tabs("acc_Vendors").setActive();  
                // layout and heading start
                var accVendorLayout = dhxMiddleBlockTabs.cells("acc_Vendors").attachLayout('2U');
                accVendorLayout.cells("a").setText("Add Vendor or Debtor or Creditor");
                accVendorLayout.cells("b").setText("List Vendor or Debtor or Creditor");
                accVendorLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accVendorLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accvendor'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accVendorGrid = accVendorLayout.cells("b").attachGrid();
                accVendorGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accvendor' class='accGridSort' />, \
                Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='name' gridType='accvendor' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accvendor' id ='accvendorName' style='width: 90%;' placeholder='Enter Vendor Name'>,\
                GST No <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='gst' gridType='accvendor' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accvendor' id ='accvendorNo' style='width: 90%;' placeholder='Enter GST No'>,\
                Contact Info <input type='text' class='acc_grid_txt_fltr' gridType='accvendor' id ='accvendorcontact' style='width: 90%;' placeholder='Enter Email Or Mobile'>, \
                Status "+preTally.AccountsTeam.commonStatusCbo('accvendor','1')+", Action");
                accVendorGrid.setInitWidths("50,*,100,*,100,150");
                accVendorGrid.setColAlign("center,left,center,left,Center,center");
                accVendorGrid.setColTypes("ro,ro,ro,ro,combo,ro");
                accVendorGrid.setColSorting("na,na,na,na,na,na");              
                accVendorGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accVendorGrid, 'accvendor');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadVendorList('');   
                // Grid Click (edit cell)
                preTally.AccountsTeam.editStatusColumn(accVendorGrid,4,"addVendor.php");  
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                 //----------------- Attach FORM --------------------//
                accVendorForm = accVendorLayout.cells("a").attachForm();
                accVendorForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addVendor.php"), function () {
                    // add custom filter in the country dropdown
                    accvendorCtry   = accVendorForm.getCombo("country_id");               
                    preTally.UserProfile.applyFilterHandler(accvendorCtry); 

                    // save or cancel button click
                    accVendorForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccVendor") {
                            // click on save button
                            /*if (!preTally.AccountsTeam.commonTextValid(accVendorForm,"description")) {
                                return false;
                            }  */                         
                            // check the input fields values are correct
                            var isValid = accVendorForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accVendorForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addVendor.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        accVendorForm.resetValidateCss();
                                        accVendorForm.clear();
                                        accVendorForm.setItemValue("eid", 0); 
                                        preTally.AccountsTeam.loadVendorList('');
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                         } else if (name == "CancelAccVendor") {
                            // click on cancel button
                            accVendorForm.resetValidateCss();
                            accVendorForm.clear();
                            accVendorForm.setItemValue("eid", 0); 
                        } // cancel button
                    }); // button click
                }); // form close
            } else {
                dhxMiddleBlockTabs.tabs("acc_Vendors").setActive();
            }
        },
        loadVendorList: function(extra) {
            // get all Vendor List
            var filterValue = new Array($('#accvendorName').val(),$('#accvendorNo').val(),$('#accvendorcontact').val(),$( "#accvendorStatus" ).val());
            accVendorGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listVendor.php&filter="+filterValue+extra), function() { 
                $('#total_accvendor').html("# : "+accVendorGrid.getUserData("", "Data_Count")+" ");
            });
        },
        editVendor: function (eid) {            
            accVendorForm.setItemValue("eid",eid);  
            accVendorForm.setItemValue("status", accVendorGrid.getUserData(eid, "status"));    
            accVendorForm.setItemValue("gst_no", accVendorGrid.getUserData(eid, "gst_no"));      
            accVendorForm.setItemValue("type", accVendorGrid.getUserData(eid, "type"));      
            accVendorForm.setItemValue("pan_no", accVendorGrid.getUserData(eid, "pan_no"));      
            accVendorForm.setItemValue("email", accVendorGrid.getUserData(eid, "email"));             
            accVendorForm.setItemValue("phone", accVendorGrid.getUserData(eid, "phone"));
            accVendorForm.setItemValue("name", accVendorGrid.getUserData(eid, "name"));
            accVendorForm.setItemValue("address", accVendorGrid.getUserData(eid, "address")); 
            accVendorForm.setItemValue("country_id", accVendorGrid.getUserData(eid, "country_id"));       
        },  
        // end the vendor section
        // start Bills 
        acc_Bills: function() {
            if (!dhxMiddleBlockTabs.cells("acc_Bills")) {
                dhxMiddleBlockTabs.addTab("acc_Bills", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Bills &nbsp; ", 90);
                dhxMiddleBlockTabs.tabs("acc_Bills").setActive();  
                 // layout and heading start
                var accBillLayout = dhxMiddleBlockTabs.cells("acc_Bills").attachLayout('2U');
                accBillLayout.cells("a").setText("Add Bills");
                accBillLayout.cells("b").setText("List Bill");
                accBillLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accBillLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accbills'),
                    height: 35
                });
                // tool bar with filter strt
                accBillTbr =  accBillLayout.cells("b").attachToolbar();
                accBillTbr.setIconsPath("images/icon/default_18/");
                accBillTbr.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(accBillTbr,'accbills');

                 //----------------- Attach Grid --------------------//
                accBillGrid = accBillLayout.cells("b").attachGrid();
                accBillGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accbills' class='accGridSort' />, \
                Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accbills' class='accGridSort' />, \
                Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='name' gridType='accbills' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accbills' id ='accbillsName' style='width: 90%;' placeholder='Enter Bill Name'>,\
                Vendor <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='vendor' gridType='accbills' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accbills' id ='accbillsVendor' style='width: 90%;' placeholder='Enter Vendor Name'>,\
                Bill, TDS, \
                Total Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amount' gridType='accbills' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accbills' id ='accbillsAmt' style='width: 90%;' placeholder='Enter Amount'>, \
                 Action");
                accBillGrid.setInitWidths("60,80,*,*,100,100,100,110");
                accBillGrid.setColAlign("center,center,left,left,right,right,right,center");
                accBillGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                accBillGrid.setColSorting("na,na,na,na,na,na,na,na");              
                accBillGrid.enableTooltips("false,false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accBillGrid, 'accbills');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadBillList('');   
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                //----------------- Attach FORM --------------------//
                accBillForm = accBillLayout.cells("a").attachForm();
                accBillForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addBills.php"), function () {
                    // fuature bill date block
                    accBillForm.getCalendar("bill_date").setSensitiveRange(null, new Date());
                    // initialise the combos used in the form
                    accbvendorCbo               = accBillForm.getCombo("vendor_id");
                    accbtdsCbo                  = accBillForm.getCombo("tds_id");
                    accbtaxCbo                  = accBillForm.getCombo("tax_id");
                    preTally.AccountsTeam.hideshowBillTax('');
                    // new set up with custom filterledger
                    preTally.UserProfile.applyFilterHandler(accbvendorCbo);                     
                    preTally.UserProfile.applyFilterHandler(accbtdsCbo);
                    accbvendorCbo.setOptionWidth(350);
                    accbtdsCbo.setOptionWidth(350);                    
                    // loading company and branch combos
                    var bcompanycbo      = accBillForm.getCombo("company_id");
                    bbranchcbo          = accBillForm.getCombo("branch");
                    var bbranch_id       = accBillForm.getItemValue("branch_id");
                    var bcompany_id      = bcompanycbo.getSelectedValue();
                    // load branch combo default
                    preTally.AccountsTeam.loadBranchCbo(bbranchcbo, bcompany_id, bbranch_id, 'accbills');

                    // on change the tds combos, bill amt and company combo
                    accBillForm.attachEvent("onChange", function (name, value, state){ 
                        if (name == "tds_id" ) {
                            // find the tds percentage and loaded into the hidden fields
                            var tds_details   = JSON.parse(accBillForm.getItemValue("tdsdetails"));
                            accBillForm.setItemValue("tds_percent", tds_details[value]); 
                        }
                        if (name == "tax_id") {
                            // find the tax name and hide and show the tax fields
                            preTally.AccountsTeam.hideshowBillTax(value);
                        } 
                        if (name == "tds_id" || name == "total_amount" || name == "tax_percent" || name == "tax_id") {
                            // change the bill amount then change the total and tds
                            var totalamt    = parseFloat(accBillForm.getItemValue("total_amount"));
                            var tdspercent  = parseFloat(accBillForm.getItemValue("tds_percent"));
                            var taxpercent  = parseFloat(accBillForm.getItemValue("tax_percent"));
                            var tax_no      = parseInt(accBillForm.getItemValue("tax_no"));
                            var tbillamt    = 0;
                            var taxtotal    = 0;
                            if (taxpercent > 0 && totalamt > 0) {
                                tbillamt    = parseFloat(totalamt * (100/(taxpercent+100)));
                                taxtotal    = parseFloat(totalamt - tbillamt);
                            } else {
                                tbillamt    = totalamt;
                            }
                            var billamt     = (tdspercent > 0 && tbillamt > 0) ? parseFloat(tbillamt*(100/(tdspercent+100))) :0;
                            var tdsamt      = parseFloat(tbillamt - billamt);
                            if (tax_no > 0 && taxtotal > 0) {
                                
                                accBillForm.setItemValue("tax_amount", taxtotal);
                                if (tax_no == 2) {
                                    accBillForm.setItemValue("sub_title1", preTally.AccountsTeam.roundToDecimal(parseFloat(taxtotal/2),3));  
                                    accBillForm.setItemValue("sub_title2", preTally.AccountsTeam.roundToDecimal(parseFloat(taxtotal/2),3));  
                                } else {
                                    accBillForm.setItemValue("sub_title1", preTally.AccountsTeam.roundToDecimal(taxtotal,3)); 
                                }
                            } else {
                                accBillForm.setItemValue("sub_title1", '0.000');
                                accBillForm.setItemValue("sub_title2", '0.000');
                                accBillForm.setItemValue("tax_amount", 0);
                            }
                            accBillForm.setItemValue("tds_amount", preTally.AccountsTeam.roundToDecimal(tdsamt,3)); 
                            accBillForm.setItemValue("bill_amount", preTally.AccountsTeam.roundToDecimal(billamt,3)); 
                        } // bill amt and tds change
                        if (name == "company_id")  {
                            bcompany_id     = value;
                            bbranch_id      = accBillForm.getItemValue("branch_id");
                            bbranchcbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(bbranchcbo, bcompany_id, bbranch_id, 'accbills');
                        }  // change company cbo                                          
                    }); // on change form close

                    // save or cancel button click
                    accBillForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccBill") {
                            // click on save button                            
                            var isValid = accBillForm.validate();// check the input fields values are correct
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accBillForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addBills.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        var taxdetails      = accBillForm.getItemValue("taxdetails");
                                        var tds_details     = accBillForm.getItemValue("tdsdetails");
                                        var bcompany_id     = bcompanycbo.getSelectedValue();
                                        accBillForm.resetValidateCss();
                                        accBillForm.clear();
                                        accBillForm.setItemValue("eid", 0); 
                                        accBillForm.setItemValue("total_amount", 0); 
                                        accBillForm.setItemValue("tdsdetails",tds_details);
                                        accBillForm.setItemValue("tds_amount",'0.000');
                                        accBillForm.setItemValue("taxdetails",taxdetails);                             
                                        accBillForm.setItemValue("bill_amount",'0.000');    
                                        accBillForm.setItemValue("tax_id", 0);              
                                        preTally.AccountsTeam.hideshowBillTax('');                                   
                                        bbranchcbo.setComboText('');
                                        accBillForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                                        if (bcompany_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                            preTally.AccountsTeam.loadBranchCbo(bbranchcbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accbills');
                                        }// company id not equal to user default id
                                        preTally.AccountsTeam.loadBillList('');
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });       
                            }else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "CancelAccBill") {
                            // click on cancel button
                            var tds_details     = accBillForm.getItemValue("tdsdetails");
                            var taxdetails      = accBillForm.getItemValue("taxdetails");
                            var bcompany_id     = bcompanycbo.getSelectedValue();
                            accBillForm.resetValidateCss();
                            accBillForm.clear();
                            accBillForm.setItemValue("eid", 0); 
                            accBillForm.setItemValue("total_amount", 0); 
                            accBillForm.setItemValue("tdsdetails",tds_details);
                            accBillForm.setItemValue("tds_amount",'0.000');
                            accBillForm.setItemValue("bill_amount",'0.000'); 
                            accBillForm.setItemValue("taxdetails",taxdetails);   
                            accBillForm.setItemValue("tax_id", 0);                           
                            preTally.AccountsTeam.hideshowBillTax('');                          
                            bbranchcbo.setComboText('');
                            accBillForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                            if (bcompany_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                preTally.AccountsTeam.loadBranchCbo(bbranchcbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accbills');
                            }// company id not equal to user default id
                        } // cancel button
                    }); // button click
                }); // form loading close
            } else {
                dhxMiddleBlockTabs.tabs("acc_Bills").setActive();
            }
        },        
        loadBillList: function(extra) {
            // get all bills saved 
            var filterValue = new Array($('#accbillsName').val(),$('#accbillsVendor').val(),$('#accbillsAmt').val(),accBillTbr.getValue("rpt_date_from"),accBillTbr.getValue("rpt_date_till"));
            accBillGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listBills.php&filter="+filterValue+extra), function() { 
                $('#total_accbills').html("# : "+accBillGrid.getUserData("", "Data_Count")+" ");
            });
        },
        hideshowBillTax: function(value, ttax) {
            accBillForm.hideItem("sub_title2");
            accBillForm.hideItem("tax_percent");
            var tax1    = '0.000';
            var tax2    = '0.000';
            if (value == '' || value == 0) {                
                accBillForm.hideItem("sub_title1");
                accBillForm.setItemValue("tax_percent",0);
                accBillForm.setItemValue("tax_no",0);
                accBillForm.setItemValue("tax_amount",0);
            } else {
                var tax_details   = JSON.parse(accBillForm.getItemValue("taxdetails"));
                if (tax_details[value]['title2'] != '') {
                    accBillForm.showItem("sub_title2");
                    accBillForm.setItemLabel("sub_title2", tax_details[value]['title2']);
                    accBillForm.setItemValue("tax_no",2);
                    tax2 = (ttax > 0) ? preTally.AccountsTeam.roundToDecimal(parseFloat(ttax/2),3) : tax2;
                    tax1 = tax2;
                    accBillForm.setItemValue("sub_title2",tax2);
                } else {
                    accBillForm.setItemValue("tax_no",1);
                    tax1 = (ttax > 0) ? preTally.AccountsTeam.roundToDecimal(ttax,3) : tax1;
                }
                accBillForm.showItem("sub_title1");
                accBillForm.setItemLabel("sub_title1", tax_details[value]['title1']);
                accBillForm.showItem("tax_percent");
                accBillForm.setItemValue("sub_title1",tax1);
            }
        },
        editBills: function (eid) {            
            accBillForm.setItemValue("eid",eid);  
            accBillForm.setItemValue("tax_id",'');  
            bbranchcbo.setComboText('');
            accBillForm.setItemValue("tds_percent", accBillGrid.getUserData(eid, "tds_percent"));    
            accBillForm.setItemValue("branch_id", accBillGrid.getUserData(eid, "branch_id"));      
            accBillForm.setItemValue("bill_name", accBillGrid.getUserData(eid, "bill_name"));      
            accBillForm.setItemValue("bill_date", accBillGrid.getUserData(eid, "bill_date"));      
            accBillForm.setItemValue("due_date", accBillGrid.getUserData(eid, "due_date"));      
            accBillForm.setItemValue("vendor_id", accBillGrid.getUserData(eid, "vendor_id"));             
            accBillForm.setItemValue("tds_id", accBillGrid.getUserData(eid, "tds_id"));
            accBillForm.setItemValue("bill_type", accBillGrid.getUserData(eid, "bill_type"));
            accBillForm.setItemValue("bill_amount", accBillGrid.getUserData(eid, "bill_amount")); 
            accBillForm.setItemValue("tds_amount", accBillGrid.getUserData(eid, "tds_amount"));       
            accBillForm.setItemValue("total_amount", accBillGrid.getUserData(eid, "total_amount"));  

            accBillForm.setItemValue("company_id", accBillGrid.getUserData(eid, "company_id"));  
            preTally.AccountsTeam.loadBranchCbo(bbranchcbo, accBillGrid.getUserData(eid, "company_id"), accBillGrid.getUserData(eid, "branch_id"), 'accbills');     
            accBillForm.setItemValue("branch", accBillGrid.getUserData(eid, "branch_id"));   
            // tax related settups
            accBillForm.setItemValue("tax_percent", accBillGrid.getUserData(eid, "tax_percent")); 
            accBillForm.setItemValue("tax_id", accBillGrid.getUserData(eid, "tax_id"));
            accBillForm.setItemValue("tax_amount", accBillGrid.getUserData(eid, "tax_amount"));            
            preTally.AccountsTeam.hideshowBillTax(accBillGrid.getUserData(eid, "tax_id"),accBillGrid.getUserData(eid, "tax_amount"));      
        },
        // recuring bills start
        acc_RecurringBill: function () {
            //console.log("Inside the recurring Bills....");
             if (!dhxMiddleBlockTabs.cells("acc_RecurringBill")) {
                dhxMiddleBlockTabs.addTab("acc_RecurringBill", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Recurring Bills &nbsp; ", 140);
                dhxMiddleBlockTabs.tabs("acc_RecurringBill").setActive();  
                 // layout and heading start
                var accRcBillLayout = dhxMiddleBlockTabs.cells("acc_RecurringBill").attachLayout('2U');
                accRcBillLayout.cells("a").setText("Add Recurring Bill");
                accRcBillLayout.cells("b").setText("List Recurring Bill");
                accRcBillLayout.cells("a").setWidth(424);
                // status bar or pagination and count
                accRcBillLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accbillr'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accRcBillGrid = accRcBillLayout.cells("b").attachGrid();
                accRcBillGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accbillr' class='accGridSort' />, \
                Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='name' gridType='accbillr' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accbillr' id ='accbillrcName' style='width: 90%;' placeholder='Enter Bill Name'>,\
                Last Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ldate' gridType='accbillr' class='accGridSort' />, \
                Next Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ndate' gridType='accbillr' class='accGridSort' />, \
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amount' gridType='accbillr' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accbillr' id ='accbillrcAmt' style='width: 90%;' placeholder='Enter Amount'>, \
                Status "+preTally.AccountsTeam.commonStatusCbo('accbillr','1')+",Action");
                accRcBillGrid.setInitWidths("60,*,95,95,95,95,95");
                accRcBillGrid.setColAlign("center,left,center,center,right,center,center");
                accRcBillGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                accRcBillGrid.setColSorting("na,na,na,na,na,na,na");              
                accRcBillGrid.enableTooltips("false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accRcBillGrid, 'accbillr');
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                // loading data into Grid...
                preTally.AccountsTeam.loadBillRCList('');  

                //----------------- Attach FORM --------------------//
                accRcBillForm       = accRcBillLayout.cells("a").attachForm();
                accRcBillForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addRcBill.php"), function () {
                    accRcBillCbo    = accRcBillForm.getCombo("bill_id");
                    accRcBillCbo.setOptionWidth(450);
                    accRcBillCbo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/accounts/autoFillCbo.php&flag=0"),true);
                    // save or cancel button click
                    accRcBillForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccBillr") {
                            // click on save button                            
                            var isValid = accRcBillForm.validate();// check the input fields values are correct
                            if (isValid) { 
                                 // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accRcBillForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addRcBill.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {

                                        dhtmlx.message({ text: jsonres.message });
                                        accRcBillForm.resetValidateCss();
                                        accRcBillForm.clear();
                                        accRcBillForm.setItemValue("eid", 0);                          
                                        accRcBillCbo.setComboText('');
                                        preTally.AccountsTeam.loadBillRCList('');
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    }
                                });  // save section ends 
                            }else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "CancelAccBillr") {
                            // click on cancel button
                            accRcBillForm.resetValidateCss();
                            accRcBillForm.clear();
                            accRcBillForm.setItemValue("eid", 0);                          
                            accRcBillCbo.setComboText('');                         
                        } // cancel button
                    }); // button click
                }); // form loading ends
            } else {
                dhxMiddleBlockTabs.tabs("acc_RecurringBill").setActive();
            }
        },
        loadBillRCList: function(extra) {
            // get all bills saved 
            var filterValue = new Array($('#accbillrcName').val(),$('#accbillrcAmt').val(),$('#accbillrStatus').val());
            accRcBillGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listRcBill.php&filter="+filterValue+extra), function() { 
                $('#total_accbillr').html("# : "+accRcBillGrid.getUserData("", "Data_Count")+" ");
            });
        },
        editRcBills: function (eid) {  
            accRcBillForm.resetValidateCss();          
            accRcBillForm.setItemValue("eid",eid); 
            accRcBillCbo.clearAll();
            accRcBillCbo.addOption(accRcBillGrid.getUserData(eid, "bill_id"), accRcBillGrid.getUserData(eid, "bill_name"));
            accRcBillForm.setItemValue("day_id", accRcBillGrid.getUserData(eid, "day_id"));
            accRcBillForm.setItemValue("status", accRcBillGrid.getUserData(eid, "status"));
            accRcBillForm.setItemValue("last_bill_id", accRcBillGrid.getUserData(eid, "last_bill_id"));
            accRcBillForm.setItemValue("next_date", accRcBillGrid.getUserData(eid, "next_date"));
            accRcBillForm.setItemValue("status", accRcBillGrid.getUserData(eid, "status"));  
            accRcBillForm.setItemValue("old_bill_id", accRcBillGrid.getUserData(eid, "bill_id"));
            accRcBillForm.setItemValue("bill_id", accRcBillGrid.getUserData(eid, "bill_id"));      
        },
        deleteRCBill: function (id) {
            // delete Recurring Bills based on flag
            preTally.AccountsTeam.commonGridDelete('accbillr', id, 'addRcBill.php');
        },
        // End Bills related section..
        // Start Vendor 
        acc_VendorReport: function() {
            if (!dhxMiddleBlockTabs.cells("acc_VendorReport")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_VendorReport", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Vendor or Debtor or Creditor Reports &nbsp;", 265);
                dhxMiddleBlockTabs.tabs("acc_VendorReport").setActive();
                // layout and heading start
                var accVendorRptLayout = dhxMiddleBlockTabs.cells("acc_VendorReport").attachLayout('2U');
                accVendorRptLayout.cells("a").hideHeader();
                accVendorRptLayout.cells("b").hideHeader();
                accVendorRptLayout.cells("a").setWidth(300);

                //----------------- Attach Grid --------------------//
                accVendorRptGrid    = accVendorRptLayout.cells("b").attachGrid();
                accVendorRptGrid.setHeader("Ledger Name, Debit, Credit");
                accVendorRptGrid.setInitWidths("*,160,160");
                accVendorRptGrid.setColAlign("left,right,right");
                accVendorRptGrid.setColTypes("ro,ro,ro");
                accVendorRptGrid.setColSorting("str,int,int");              
                accVendorRptGrid.enableTooltips("false,false,false");
                accVendorRptGrid.attachFooter("<div id='accVdBal'></div>,<div id='accVdIT'>0</div>,<div id='accVdET'>0</div>",["","text-align:right;","text-align:right;"]);
                preTally.Attendance.commonServerGrids(accVendorRptGrid, 'accvd');  

                //----------------- Filter Form For Grid --------------------//
                accVendorRptForm    = accVendorRptLayout.cells("a").attachForm();
                accVendorRptForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=1"), function () {
                    // fuature bill date block
                    accVendorRptForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accVendorRptForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var vdcompanycbo      = accVendorRptForm.getCombo("company_id");
                    var vdbranchcbo       = accVendorRptForm.getCombo("branch_id");
                    var vdvendorcbo       = accVendorRptForm.getCombo("vendor_id");
                    vdcompanycbo.setOptionWidth(350);
                    vdbranchcbo.setOptionWidth(350);             
                    vdvendorcbo.setOptionWidth(350); 
                    vdvendorcbo.enableFilteringMode('between');       
                    // load branch combo default based on loaded company                    
                    var vdcompany_id      = vdcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(vdbranchcbo, vdcompany_id, 0, 'filt');

                    // on change the company combo
                    accVendorRptForm.attachEvent("onChange", function (name, value, state) {                        
                        if (name == "company_id")  {
                            vdcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(vdbranchcbo, vdcompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accVendorRptForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadVendorRpt();             
                        } // search button
                    }); // button click

                    // loading grid after load the filter form
                    preTally.AccountsTeam.loadVendorRpt();
                }); // form loading close
            } else {
                dhxMiddleBlockTabs.tabs("acc_VendorReport").setActive();
            }
        },
        loadVendorRpt: function() {
            if (accVendorRptForm.getCombo("vendor_id").getSelectedValue() > 0) {
                accVendorRptGrid.setColLabel(0, "Ledger Name");
            } else {
                accVendorRptGrid.setColLabel(0, "Vendor or Debtor or Creditor");
            }
            var filterValue = new Array(accVendorRptForm.getItemValue("from_date", true),accVendorRptForm.getItemValue("to_date", true),accVendorRptForm.getCombo("company_id").getSelectedValue(),accVendorRptForm.getCombo("branch_id").getSelectedValue(),accVendorRptForm.getCombo("vendor_id").getSelectedValue());
            accVendorRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptVendor.php&filter="+filterValue), function() { 
                $('#accVdIT').html(accVendorRptGrid.getUserData("", "total_income"));
                $('#accVdET').html(accVendorRptGrid.getUserData("", "total_expense"));
                if (accVendorRptForm.getCombo("vendor_id").getSelectedValue() > 0) {
                    $('#accVdBal').html("Balance : "+accVendorRptGrid.getUserData("", "total_balance"));
                } else {
                    $('#accVdBal').html('');
                }
            });
        },
        // end vendor reports
        // start Profit and loss report 
        /*acc_ProfitLoss: function() {
            if (!dhxMiddleBlockTabs.cells("acc_PandLReport")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_PandLReport", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Profit And Loss Base &nbsp;", 210);
                dhxMiddleBlockTabs.tabs("acc_PandLReport").setActive();
                // layout and heading start
                var accPandLLayout = dhxMiddleBlockTabs.cells("acc_PandLReport").attachLayout('2U');     
                accPandLLayout.cells("a").hideHeader();
                accPandLLayout.cells("b").hideHeader();
                accPandLLayout.cells("a").setWidth(300);

                //----------------- Attach Grid --------------------//
                accPandLGrid        = accPandLLayout.cells("b").attachGrid();
                accPandLGrid.setHeader("Ledger Name, Income, Expense");
                accPandLGrid.setInitWidths("*,160,160");
                accPandLGrid.setColAlign("left,right,right");
                accPandLGrid.setColTypes("ro,ro,ro");
                accPandLGrid.setColSorting("str,int,int");              
                accPandLGrid.enableTooltips("false,false,false");
                accPandLGrid.attachFooter(",<div id='accPLIT'>0</div>,<div id='accPLET'>0</div>",["","text-align:right;","text-align:right;"]);
                preTally.Attendance.commonServerGrids(accPandLGrid, 'accpandl');                

                //----------------- Filter Form For Grid --------------------//               
                accPandLForm        = accPandLLayout.cells("a").attachForm();
                accPandLForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=0"), function () {
                    // fuature bill date block
                    accPandLForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accPandLForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var plcompanycbo      = accPandLForm.getCombo("company_id");
                    var plbranchcbo       = accPandLForm.getCombo("branch_id");
                    plcompanycbo.setOptionWidth(350);
                    plbranchcbo.setOptionWidth(350);             
                    // load branch combo default based on loaded company                    
                    var plcompany_id      = plcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(plbranchcbo, plcompany_id, 0, 'filt');

                    // on change the company combo
                    accPandLForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            plcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(plbranchcbo, plcompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accPandLForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadPandL();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadPandL(); 
                }); // form loading close

            } else {
                dhxMiddleBlockTabs.tabs("acc_PandLReport").setActive();
            }
        },
        loadPandL:function() {
            var filterValue = new Array(accPandLForm.getItemValue("from_date", true),accPandLForm.getItemValue("to_date", true),accPandLForm.getCombo("company_id").getSelectedValue(),accPandLForm.getCombo("branch_id").getSelectedValue(),accPandLForm.getCombo("trans_type").getSelectedValue());
            accPandLGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptPandL.php&filter="+filterValue), function() { 
                $('#accPLIT').html(accPandLGrid.getUserData("", "total_income"));
                $('#accPLET').html(accPandLGrid.getUserData("", "total_expense"));
            });
        },*/
        // end profit and loss
        // Ledger Report based on the selected period of time and branch / company
        acc_LedgerReport: function() {
            if (!dhxMiddleBlockTabs.cells("acc_LedgerReport")) {
                dhxMiddleBlockTabs.addTab("acc_LedgerReport", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Ledger Account Reports &nbsp;", 200);
                dhxMiddleBlockTabs.tabs("acc_LedgerReport").setActive();
                // layout and heading start
                var accLedgeRptLayout   = dhxMiddleBlockTabs.cells("acc_LedgerReport").attachLayout('2U');     
                accLedgeRptLayout.cells("a").hideHeader();
                accLedgeRptLayout.cells("b").hideHeader();
                accLedgeRptLayout.cells("a").setWidth(300);

                 // status bar or pagination and count
                accLedgeRptLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accledgrpt'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accledgeRptGrid         = accLedgeRptLayout.cells("b").attachGrid();
                accledgeRptGrid.setHeader("Slno,Ledger Transactions, Income, Expense");
                accledgeRptGrid.setInitWidths("70,*,160,160");
                accledgeRptGrid.setColAlign("left,left,right,right");
                accledgeRptGrid.setColTypes("ro,ro,ro,ro");
                accledgeRptGrid.setColSorting("na,str,int,int");              
                accledgeRptGrid.enableTooltips("false,false,false,false");
                accledgeRptGrid.attachFooter(",,<div id='accLdRIT'>0</div>,<div id='accLdRET'>0</div>",["","","text-align:right;","text-align:right;"]);
                preTally.Attendance.commonGridDefine(accledgeRptGrid, 'accledgrpt');

                //----------------- Filter Form For Grid --------------------//               
                accledgeRptForm         = accLedgeRptLayout.cells("a").attachForm();
                accledgeRptForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=2"), function () {
                    // fuature bill date block
                    accledgeRptForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accledgeRptForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var lrtcompanycbo   = accledgeRptForm.getCombo("company_id");
                    var lrtbranchcbo    = accledgeRptForm.getCombo("branch_id");
                    var lrtledgercbo    = accledgeRptForm.getCombo("ledger_id");
                    lrtcompanycbo.setOptionWidth(350);
                    lrtbranchcbo.setOptionWidth(350); 
                    lrtledgercbo.setOptionWidth(350);
                    lrtledgercbo.enableFilteringMode('between');                  
                    // load branch combo default based on loaded company                    
                    var lrtcompany_id      = lrtcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(lrtbranchcbo, lrtcompany_id, 0, 'filt');
                    // on change the company combo
                    accledgeRptForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            lrtcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(lrtbranchcbo, lrtcompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accledgeRptForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadLedgerRpt();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadLedgerRpt(); 
                 }); // form loading close
            } else { //cell block click active check else
                dhxMiddleBlockTabs.tabs("acc_LedgerReport").setActive();
            }
        }, 
        loadLedgerRpt: function() {
            if (accledgeRptForm.getCombo("ledger_id").getSelectedValue() > 0) {
                //,accledgeRptForm.getCombo("trans_type").getSelectedValue()
                var filterValue = new Array(accledgeRptForm.getItemValue("from_date", true),accledgeRptForm.getItemValue("to_date", true),accledgeRptForm.getCombo("company_id").getSelectedValue(),accledgeRptForm.getCombo("branch_id").getSelectedValue(),accledgeRptForm.getCombo("ledger_id").getSelectedValue(),accledgeRptForm.getCombo("trans_type").getSelectedValue());
               accledgeRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptLedger.php&filter="+filterValue), function() { 
                    $('#accLdRIT').html(accledgeRptGrid.getUserData("", "total_income"));
                    $('#accLdRET').html(accledgeRptGrid.getUserData("", "total_expense"));
                    $('#total_accledgrpt').html("# : "+accledgeRptGrid.getUserData("", "Data_Count")+" ");
                });
            } else {
                dhtmlx.message({ type: "error", text: "Please select the ledger." });
            }     
        }, 
        // new profit and loss account based on the new account ledger...
        acc_PandL: function() {
            if (!dhxMiddleBlockTabs.cells("acc_PandL")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_PandL", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Profit And Loss Account &nbsp;", 210);
                dhxMiddleBlockTabs.tabs("acc_PandL").setActive();
                // layout and heading start
                accPaLLayout = dhxMiddleBlockTabs.cells("acc_PandL").attachLayout('2U');     
                accPaLLayout.cells("a").hideHeader();
                accPaLLayout.cells("b").hideHeader();
                accPaLLayout.cells("a").setWidth(300);

                //----------------- Attach Grid --------------------//
                accPaLGrid        = accPaLLayout.cells("b").attachGrid();
                accPaLGrid.setHeader("Particulars, , DR Amt, Particulars, , CR Amt");
                accPaLGrid.setInitWidths("*,100,100,*,100,100");
                accPaLGrid.setColAlign("left,right,right,left,right,right");
                accPaLGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accPaLGrid.setColSorting("str,int,int,str,int,int");              
                accPaLGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonServerGrids(accPaLGrid, 'accpal');                

                //----------------- Filter Form For Grid --------------------//               
                accPaLForm        = accPaLLayout.cells("a").attachForm();
                accPaLForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=0"), function () {
                    // fuature bill date block
                    accPaLForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accPaLForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var palcompanycbo      = accPaLForm.getCombo("company_id");
                    var palbranchcbo       = accPaLForm.getCombo("branch_id");
                    palcompanycbo.setOptionWidth(350);
                    palbranchcbo.setOptionWidth(350);             
                    // load branch combo default based on loaded company                    
                    var palcompany_id      = palcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(palbranchcbo, palcompany_id, 0, 'filt');

                    // on change the company combo
                    accPaLForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            palcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(palbranchcbo, palcompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accPaLForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadPaL();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadPaL(); 
                }); // form loading close

            } else {
                dhxMiddleBlockTabs.tabs("acc_PandL").setActive();
            }
        },
        loadPaL: function() {           
            accPaLLayout.cells("b").setText("Profit And Loss Account  -  ("+accPaLForm.getItemValue("from_date", true)+" - "+accPaLForm.getItemValue("to_date", true)+") ");
            accPaLLayout.cells("b").showHeader();
            var filterValue = new Array(accPaLForm.getItemValue("from_date", true),accPaLForm.getItemValue("to_date", true),accPaLForm.getCombo("company_id").getSelectedValue(),accPaLForm.getCombo("branch_id").getSelectedValue(),accPaLForm.getCombo("trans_type").getSelectedValue(),accPaLForm.getCombo("viewtype").getSelectedValue());
            accPaLGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptProfitLoss.php&filter="+filterValue), function() {                 
            });           
        },
        // New balance sheet company, branch and date range based....
        acc_BalanceSheet: function() {
            //dhtmlx.message({ type: "error", text: "Comming Soon...." });
            if (!dhxMiddleBlockTabs.cells("acc_BalanceSheet")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_BalanceSheet", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Balance Sheet &nbsp;", 165);
                dhxMiddleBlockTabs.tabs("acc_BalanceSheet").setActive();
                // layout and heading start
                accBSLayout = dhxMiddleBlockTabs.cells("acc_BalanceSheet").attachLayout('2U');     
                accBSLayout.cells("a").hideHeader();
                accBSLayout.cells("b").hideHeader();
                accBSLayout.cells("a").setWidth(300);

                 //----------------- Attach Grid --------------------//
                accBSGrid        = accBSLayout.cells("b").attachGrid();
                accBSGrid.setHeader("Liabitities, , Amount, Assets, , Amount");
                accBSGrid.setInitWidths("*,100,100,*,100,100");
                accBSGrid.setColAlign("left,right,right,left,right,right");
                accBSGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accBSGrid.setColSorting("str,int,int,str,int,int");              
                accBSGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonServerGrids(accBSGrid, 'accbs');

                //----------------- Filter Form For Grid --------------------//               
                accBSForm        = accBSLayout.cells("a").attachForm();
                accBSForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=3"), function () {
                    // fuature bill date block
                    accBSForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accBSForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var bscompanycbo      = accBSForm.getCombo("company_id");
                    var bsbranchcbo       = accBSForm.getCombo("branch_id");
                    bscompanycbo.setOptionWidth(350);
                    bsbranchcbo.setOptionWidth(350);             
                    // load branch combo default based on loaded company                    
                    var bscompany_id      = bscompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(bsbranchcbo, bscompany_id, 0, 'filt');

                    // on change the company combo
                    accBSForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            bscompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(bsbranchcbo, bscompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accBSForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadBSData();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadBSData(); 
                }); // form loading close


            } else {
                dhxMiddleBlockTabs.tabs("acc_BalanceSheet").setActive();
            }
        },
        loadBSData: function() { 
            //console.log("Balance Sheet Data loading.....");
            dhtmlx.message({ type: "error", text: "Comming Soon...." });
            var filterValue = new Array(accBSForm.getItemValue("from_date", true),accBSForm.getItemValue("to_date", true),accBSForm.getCombo("company_id").getSelectedValue(),accBSForm.getCombo("branch_id").getSelectedValue());
            accBSGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptBalanceSheet.php&filter="+filterValue), function() { 
            //console.log("Balance Sheet Loaded.");                
            });
        },
        // not transfer and blocked item data old to new
        acc_NotTransfered: function() {
            if (!dhxMiddleBlockTabs.cells("acc_NotTransfered")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_NotTransfered", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Not Transfered / Blocked Report &nbsp;", 245);
                dhxMiddleBlockTabs.tabs("acc_NotTransfered").setActive();
                 // layout and heading start
                accBlkItmLayout = dhxMiddleBlockTabs.cells("acc_NotTransfered").attachLayout("1C");
                accBlkItmLayout.cells("a").hideHeader();

                // status bar or pagination and count
                accBlkItmLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accbkitm'),
                    height: 35
                });

                //----------------- Attach Grid --------------------//
                accBItmGrid        = accBlkItmLayout.cells("a").attachGrid();
                accBItmGrid.setHeader("Slno, Item Name<input type='text' class = 'acc_grid_txt_fltr' gridType='accbkitm' id ='accbkitmItem' style='width: 80%;' placeholder='Search Item Name'>,\
                 Amount, Date <input type='calendar' data-type='accbkitm' id='accbkitmDate' style='width: 90%;' placeholder='Date'>,\
                 Action");
                accBItmGrid.setInitWidths("80,*,100,100,120");
                accBItmGrid.setColAlign("left,left,right,center,center");
                accBItmGrid.setColTypes("ro,ro,ro,ro,ro"); 
                accBItmGrid.setColSorting("na,na,na,na,na");     
                accBItmGrid.enableTooltips("false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accBItmGrid, 'accbkitm');
                
                // date calander filters
                accbkitmDate = new dhtmlXCalendarObject("accbkitmDate");
                accbkitmDate.setDateFormat("%d-%m-%Y");
                accbkitmDate.setSensitiveRange(null, new Date());
                accbkitmDate.setDate(null);
                accbkitmDate.hideTime();
                /*accbkitmDate.attachEvent("onClick", function(date, state){
                    preTally.AccountsTeam.loadBlockTransList(''); 
                });*/
                accbkitmDate.attachEvent("onShow", function(date, state){
                    accbkitmDate.setDate(null);
                });
                accbkitmDate.attachEvent("onHide", function(date, state){
                    preTally.AccountsTeam.loadBlockTransList(''); 
                });
                // manage text filters
                preTally.AccountsTeam.accTextFilters();
                // loading data into the grid
                preTally.AccountsTeam.loadBlockTransList(''); 
            } else {
                dhxMiddleBlockTabs.tabs("acc_NotTransfered").setActive();
            }
        },
        loadBlockTransList: function (extra) {
            var filterValue = new Array($('#accbkitmItem').val(),accbkitmDate.getDate(true));
            accBItmGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptnotTransfer.php&filter="+filterValue+extra), function() { 
                $('#total_accbkitm').html("# : "+accBItmGrid.getUserData("", "Data_Count")+" ");
            });
        },
        acc_JournalEntry : function() {
            // add, edit, delete journal entries
            if (!dhxMiddleBlockTabs.cells("acc_JournalEntry")) {
                dhxMiddleBlockTabs.addTab("acc_JournalEntry", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Journal Entry &nbsp; ", 165);
                dhxMiddleBlockTabs.tabs("acc_JournalEntry").setActive();
                // layout and heading start
                var accjEntryLayout = dhxMiddleBlockTabs.cells("acc_JournalEntry").attachLayout('2U');
                accjEntryLayout.cells("a").setText("Add Journal Entry");
                accjEntryLayout.cells("b").setText("List Journal Entry");
                accjEntryLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accjEntryLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accjentry'),
                    height: 35
                }); 
                //----------------- Attach Grid --------------------//
                accjEntryGrid = accjEntryLayout.cells("b").attachGrid();
                accjEntryGrid.setHeader("SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accjentry' class='accGridSort' />, \
                DR Account <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='drledger' gridType='accjentry' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accjentry' id ='accjentryDr' style='width: 90%;' placeholder='Enter Dr Account'>,\
                CR Account <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='crledger' gridType='accjentry' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accjentry' id ='accjentryCr' style='width: 90%;' placeholder='Enter Cr Account'>,\
                Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accjentry' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accjentry' id ='accjentryDate' style='width: 90%;' placeholder='Enter Date'>, \
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amt' gridType='accjentry' class='accGridSort' />,\
                Action");
                accjEntryGrid.setInitWidths("50,*,*,100,100,100");
                accjEntryGrid.setColAlign("center,left,left,center,right,center");
                accjEntryGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accjEntryGrid.setColSorting("na,na,na,na,na,na");              
                accjEntryGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accjEntryGrid, 'accjentry');
                // date calander filters
                accjEntryDate = new dhtmlXCalendarObject("accjentryDate");
                accjEntryDate.setDateFormat("%d-%m-%Y");
                accjEntryDate.setSensitiveRange(null, new Date());
                accjEntryDate.setDate(null);
                accjEntryDate.hideTime();
                accjEntryDate.attachEvent("onHide", function(date, state){
                    preTally.AccountsTeam.loadJournalEntry(''); 
                });
                accjEntryDate.attachEvent("onShow", function(date, state){
                    accjEntryDate.setDate(null);
                });                
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadJournalEntry(''); 
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                //----------------- Attach FORM --------------------//
                accjEntryForm = accjEntryLayout.cells("a").attachForm();
                accjEntryForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addJournalEntry.php"), function () {

                     // fuature bill date block
                    accjEntryForm.getCalendar("date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var jecompanycbo      = accjEntryForm.getCombo("company_id");
                    jebranchcbo           = accjEntryForm.getCombo("branch");
                    var jebranch_id       = accjEntryForm.getItemValue("branch_id");
                    var jecompany_id      = jecompanycbo.getSelectedValue();                    
                    jebranchcbo.setOptionWidth(350);                    
                    jecompanycbo.setOptionWidth(350); 
                    accjEntryForm.getCombo("ledger_dr").enableFilteringMode('between');  
                    accjEntryForm.getCombo("ledger_cr").enableFilteringMode('between');  
                    // load branch combo default
                    preTally.AccountsTeam.loadBranchCbo(jebranchcbo, jecompany_id, jebranch_id, 'accjentry');

                    // on change the company combo and load branch
                    accjEntryForm.attachEvent("onChange", function (name, value, state){                         
                        if (name == "company_id")  {
                            jecompany_id     = value;
                            jebranch_id      = accjEntryForm.getItemValue("branch_id");
                            jebranchcbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(jebranchcbo, jecompany_id, jebranch_id, 'accjentry');
                        }  // change company cbo                                          
                    }); // on change form close

                    // save or cancel button click
                    accjEntryForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAccJEntry") {
                            // click on save button  
                            // check the input fields values are correct
                            var isValid = accjEntryForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accjEntryForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addJournalEntry.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {
                                        dhtmlx.message({ text: jsonres.message });
                                        var jecompany_id      = jecompanycbo.getSelectedValue(); 
                                        accjEntryForm.resetValidateCss();
                                        accjEntryForm.clear();
                                        accjEntryForm.setItemValue("eid", 0); 
                                        jebranchcbo.setComboText('');
                                        accjEntryForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                                        if (jecompany_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                            preTally.AccountsTeam.loadBranchCbo(jebranchcbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjentry');
                                        }// company id not equal to user default id
                                        preTally.AccountsTeam.loadJournalEntry(''); 
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    } // close server save/update failed 
                                }); // server sending close
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "CancelAccJEntry") {
                            // click on cancel button
                            var jecompany_id      = jecompanycbo.getSelectedValue(); 
                            accjEntryForm.resetValidateCss();
                            accjEntryForm.clear();
                            accjEntryForm.setItemValue("eid", 0);                        
                            jebranchcbo.setComboText('');
                            accjEntryForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                            if (jecompany_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                preTally.AccountsTeam.loadBranchCbo(jebranchcbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjentry');
                            }// company id not equal to user default id
                        } // cancel button
                    }); // button click
                }); // close the form opening form
            }else {
                dhxMiddleBlockTabs.tabs("acc_JournalEntry").setActive();
            }
        },     
        loadJournalEntry: function(extra) {
            var datefilt     = accjEntryDate.getDate(true);
            // list all saved journal entries
            var filterValue = new Array($('#accjentryDr').val(),$('#accjentryCr').val(),datefilt);
            accjEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listJournalEntry.php&filter="+filterValue+extra), function() { 
                $('#total_accjentry').html("# : "+accjEntryGrid.getUserData("", "Data_Count")+" ");
            }); 
        },
        editJournalEntry: function(eid) {            
            accjEntryForm.setItemValue("eid",eid);  
            //accjEntryForm.getCombo("branch").setComboText('');
            accjEntryForm.setItemValue("ledger_cr", accjEntryGrid.getUserData(eid, "ledger_cr"));    
            accjEntryForm.setItemValue("ledger_dr", accjEntryGrid.getUserData(eid, "ledger_dr"));    
            accjEntryForm.setItemValue("amount", accjEntryGrid.getUserData(eid, "amount"));      
            accjEntryForm.setItemValue("date", accjEntryGrid.getUserData(eid, "date"));         
            accjEntryForm.setItemValue("company_id", accjEntryGrid.getUserData(eid, "company_id"));    
            accjEntryForm.setItemValue("branch_id", accjEntryGrid.getUserData(eid, "branch_id"));   
            // Loading Branch and select             
            jebranchcbo.setComboText('');  
            preTally.AccountsTeam.loadBranchCbo(jebranchcbo, accjEntryGrid.getUserData(eid, "company_id"), accjEntryGrid.getUserData(eid, "branch_id"), 'accjentry');
            accjEntryForm.setItemValue("branch", accjEntryGrid.getUserData(eid, "branch_id")); 
        },
        acc_JournalData: function() {
            // direct entering ledger data save and list page 
             if (!dhxMiddleBlockTabs.cells("acc_JournalData")) {
                dhxMiddleBlockTabs.addTab("acc_JournalData", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Add Ledger Entry &nbsp; ", 165);
                dhxMiddleBlockTabs.tabs("acc_JournalData").setActive();
                // layout and heading start
                var accJDataLayout = dhxMiddleBlockTabs.cells("acc_JournalData").attachLayout('2U');
                accJDataLayout.cells("a").setText("Add Ledger Data");
                accJDataLayout.cells("b").setText("List Direct Added Data");
                accJDataLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accJDataLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accjdata'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accJDataGrid = accJDataLayout.cells("b").attachGrid();
                accJDataGrid.setHeader("SlNo <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accjdata' class='accGridSort' />, \
                Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accjdata' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accjdata' id ='accjdataDate' style='width: 90%;' placeholder='Enter Date'>, \
                Ledger Name <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='ledger' gridType='accjdata' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accjdata' id ='accjdataLedger' style='width: 90%;' placeholder='Enter Ledger'>,\
                Branch <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='branch' gridType='accjdata' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accjdata' id ='accjdataBranch' style='width: 90%;' placeholder='Enter Branch'>,\
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amt' gridType='accjdata' class='accGridSort' />,\
                Action");
                accJDataGrid.setInitWidths("50,100,*,*,100,100");
                accJDataGrid.setColAlign("center,center,left,left,right,center");
                accJDataGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accJDataGrid.setColSorting("na,na,na,na,na,na");              
                accJDataGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accJDataGrid, 'accjdata');
                // date calander filters
                accJDataDate = new dhtmlXCalendarObject("accjdataDate");
                accJDataDate.setDateFormat("%d-%m-%Y");
                accJDataDate.setSensitiveRange(null, new Date());
                accJDataDate.setDate(null);
                accJDataDate.hideTime();
                accJDataDate.attachEvent("onHide", function(date, state){
                    preTally.AccountsTeam.loadLedgerData(''); 
                });
                accJDataDate.attachEvent("onShow", function(date, state){
                    accJDataDate.setDate(null);
                });                
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadLedgerData(''); 
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters();
                //----------------- Attach FORM --------------------//
                accJDataForm = accJDataLayout.cells("a").attachForm();
                accJDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addLedgerData.php"), function () {

                    // fuature bill date block
                    accJDataForm.getCalendar("date_entry").setSensitiveRange(null, new Date());
                    // loading company and branch and bank combos
                    var jdCompCbo       = accJDataForm.getCombo("company_id");
                    var jdOffcCbo       = accJDataForm.getCombo("office_id");
                    jdBranhCbo          = accJDataForm.getCombo("branch");
                    jdLocCbo            = accJDataForm.getCombo("location");
                    jdBankCbo           = accJDataForm.getCombo("bankaccount");
                    jdBankDrCbo         = accJDataForm.getCombo("bankaccountdr");
                    // find the current values of branch and company
                    var jdBranh_id      = accJDataForm.getItemValue("branch_id");
                    var jdLoc_id        = accJDataForm.getItemValue("location_id");
                    var jdBank_id       = accJDataForm.getItemValue("ba_id");
                    var jdBank_Drid     = accJDataForm.getItemValue("ba_id_debit");
                    var jdComp_id       = jdCompCbo.getSelectedValue();                    
                    var jdOffc_id       = jdOffcCbo.getSelectedValue();                    
                    jdBranhCbo.setOptionWidth(350);                    
                    jdCompCbo.setOptionWidth(350);
                    jdLocCbo.setOptionWidth(350);                    
                    jdOffcCbo.setOptionWidth(350);  
                    // load branch combo default
                    preTally.AccountsTeam.loadBranchCbo(jdBranhCbo, jdComp_id, jdBranh_id, 'accjdata');
                    preTally.AccountsTeam.loadBranchCbo(jdLocCbo, jdOffc_id, jdLoc_id, 'accjdata');
                    if (accJDataForm.getItemValue("amt_type") == 1) {
                        preTally.AccountsTeam.loadBankCbo(jdBankCbo, jdOffc_id, jdBank_id, 'accjdata');                    
                        preTally.AccountsTeam.loadBankCbo(jdBankDrCbo, jdOffc_id, jdBank_Drid, 'accjdata');                    
                    } else {
                        accJDataForm.hideItem("bankaccount");
                        accJDataForm.hideItem("bankaccountdr");
                    }
                    // enable Filters in combos                     
                    accJDataForm.getCombo("ledger_id").enableFilteringMode('between');  
                    accJDataForm.getCombo("vendor_id").enableFilteringMode('between');
                    // check the office based currency 12-01-2026
                    //var off_currency   = JSON.parse(accJDataForm.getItemValue("off_currency"));
                    //off_currency['"'+jdOffc_id+'"']
                    preTally.AccountsTeam.hideshowAmt(accJDataForm, jdComp_id, jdOffc_id); //12-01-2026

                    // on change the company, office, trans type etc... load branch ad banks
                    accJDataForm.attachEvent("onChange", function (name, value, state){                         
                        if (name == "company_id")  {
                            jdComp_id       = value;
                            jdBranh_id      = accJDataForm.getItemValue("branch_id");
                            jdBranhCbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(jdBranhCbo, jdComp_id, jdBranh_id, 'accjdata'); 
                            preTally.AccountsTeam.hideshowAmt(accJDataForm, jdComp_id, accJDataForm.getItemValue("office_id")); //12-01-2026                                                                              
                        }  // change company cbo 
                        else if (name == "office_id")  {
                            jdOffc_id       = value;
                            jdLoc_id        = accJDataForm.getItemValue("location_id");
                            jdLocCbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(jdLocCbo, jdOffc_id, jdLoc_id, 'accjdata');
                            if (accJDataForm.getItemValue("amt_type") == 1) {
                                jdBank_id        = accJDataForm.getItemValue("ba_id");
                                preTally.AccountsTeam.loadBankCbo(jdBankCbo, jdOffc_id, jdBank_id, 'accjdata');                                
                                jdBank_Drid      = accJDataForm.getItemValue("ba_id_debit");
                                preTally.AccountsTeam.loadBankCbo(jdBankDrCbo, jdOffc_id, jdBank_Drid, 'accjdata'); 
                            }
                            preTally.AccountsTeam.hideshowAmt(accJDataForm, accJDataForm.getItemValue("company_id"),jdOffc_id); //12-01-2026                              
                        }  // change office cbo    
                        else if (name == "amt_type")  {                                                        
                            if (value == 1) {
                                accJDataForm.showItem("bankaccount");
                                jdOffc_id        = accJDataForm.getItemValue("office_id");
                                jdBank_id        = accJDataForm.getItemValue("ba_id");
                                preTally.AccountsTeam.loadBankCbo(jdBankCbo, jdOffc_id, jdBank_id, 'accjdata');
                                accJDataForm.showItem("bankaccountdr");
                                jdBank_Drid      = accJDataForm.getItemValue("ba_id_debit");
                                preTally.AccountsTeam.loadBankCbo(jdBankDrCbo, jdOffc_id, jdBank_Drid, 'accjdata'); 
                            } else {
                                jdBankCbo.clearAll();
                                jdBankCbo.setComboText('Select Bank');
                                accJDataForm.hideItem("bankaccount");
                                jdBankDrCbo.clearAll();
                                jdBankDrCbo.setComboText('Select Bank');
                                accJDataForm.hideItem("bankaccountdr");
                            }
                        }  // transaction type change(bank or cash) cbo 
                        else if (name == "amount_ratio" || name == "amount") { //12-01-2026
                            var totmat  = parseFloat(accJDataForm.getItemValue("amount"));
                            var ratio   = parseFloat(accJDataForm.getItemValue("amount_ratio"));
                            if (totmat > 0 && ratio > 0) {
                                accJDataForm.setItemValue("converted", preTally.AccountsTeam.roundToDecimal(parseFloat(totmat*ratio),4)); 
                            } else {
                                accJDataForm.setItemValue("converted", 0); 
                            }
                        } else if (name == "converted") {
                            var totmat  = parseFloat(accJDataForm.getItemValue("amount"));
                            if (totmat > 0 && value > 0) {
                                accJDataForm.setItemValue("amount_ratio", preTally.AccountsTeam.roundToDecimal(parseFloat(value/totmat),4)); 
                            } else {
                                accJDataForm.setItemValue("amount_ratio", 0); 
                            }
                        }
                    }); // on change form close
                    // save or cancel button click
                    accJDataForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveLedgerData") {
                            // click on save button  
                            // check the input fields values are correct
                            var isValid = accJDataForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accJDataForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addLedgerData.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {
                                        dhtmlx.message({ text: jsonres.message });
                                        var jdComp_id       = jdCompCbo.getSelectedValue();                    
                                        var jdOffc_id       = jdOffcCbo.getSelectedValue();
                                        accJDataForm.resetValidateCss();
                                        accJDataForm.clear();
                                        accJDataForm.setItemValue("amt_type", 2); 
                                        accJDataForm.hideItem("bankaccount");
                                        accJDataForm.hideItem("bankaccountdr");
                                        accJDataForm.setItemValue("eid", 0);                        
                                        jdBranhCbo.setComboText('');
                                        jdLocCbo.setComboText('');
                                        accJDataForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                                        accJDataForm.setItemValue("office_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                                        if (jdComp_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                            preTally.AccountsTeam.loadBranchCbo(jdBranhCbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjdata');
                                        }// company id not equal to user default id
                                        if (jdOffc_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                            preTally.AccountsTeam.loadBranchCbo(jdLocCbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjdata');
                                        }// company id not equal to user default id
                                        preTally.AccountsTeam.loadLedgerData(''); 
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    } // close server save/update failed 
                                }); // server sending close
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "cancelLedgerData") {
                            // click on cancel button
                            var jdComp_id       = jdCompCbo.getSelectedValue();                    
                            var jdOffc_id       = jdOffcCbo.getSelectedValue();
                            accJDataForm.resetValidateCss();
                            accJDataForm.clear();
                            accJDataForm.setItemValue("amt_type", 2); 
                            accJDataForm.setItemValue("ie_type", "add");
                            accJDataForm.hideItem("bankaccount");
                            accJDataForm.hideItem("bankaccountdr");
                            accJDataForm.setItemValue("eid", 0);                        
                            jdBranhCbo.setComboText('');
                            jdLocCbo.setComboText('');
                            accJDataForm.setItemValue("company_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                            accJDataForm.setItemValue("office_id", unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                            if (jdComp_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                preTally.AccountsTeam.loadBranchCbo(jdBranhCbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjdata');
                            }// company id not equal to user default id
                            if (jdOffc_id != unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)) {                                
                                preTally.AccountsTeam.loadBranchCbo(jdLocCbo, unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo), 0, 'accjdata');
                            }// company id not equal to user default id
                            preTally.AccountsTeam.hideshowAmt(accJDataForm, accJDataForm.getItemValue("company_id"),accJDataForm.getItemValue("office_id"));
                        } // cancel button
                    }); // button click
                }); // close the form opening form
             }else {
                dhxMiddleBlockTabs.tabs("acc_JournalData").setActive();
            }
        },
        loadLedgerData: function(extra) {
            var datefilt     = accJDataDate.getDate(true);
            // list all saved Ledger custom Entry Data
            var filterValue = new Array($('#accjdataLedger').val(),$('#accjdataBranch').val(),datefilt);
            accJDataGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listLedgerData.php&filter="+filterValue+extra), function() { 
                $('#total_accjdata').html("# : "+accJDataGrid.getUserData("", "Data_Count")+" ");
            }); 
        },
        editLedgerData: function(eid) {            
            accJDataForm.setItemValue("eid",eid);  
            accJDataForm.setItemValue("company_id", accJDataGrid.getUserData(eid, "company_id"));    
            accJDataForm.setItemValue("branch_id", accJDataGrid.getUserData(eid, "branch_id"));   
            accJDataForm.setItemValue("office_id", accJDataGrid.getUserData(eid, "office_id"));    
            accJDataForm.setItemValue("location_id", accJDataGrid.getUserData(eid, "location_id")); 
            accJDataForm.setItemValue("ledger_id", accJDataGrid.getUserData(eid, "ledger_id"));    
            accJDataForm.setItemValue("vendor_id", accJDataGrid.getUserData(eid, "vendor_id"));    
            accJDataForm.setItemValue("amt_type", accJDataGrid.getUserData(eid, "amt_type"));    
            accJDataForm.setItemValue("amount", accJDataGrid.getUserData(eid, "amount"));      
            accJDataForm.setItemValue("date_entry", accJDataGrid.getUserData(eid, "date_entry"));         
            accJDataForm.setItemValue("ba_id", accJDataGrid.getUserData(eid, "ba_id"));         
            accJDataForm.setItemValue("ba_id_debit", accJDataGrid.getUserData(eid, "ba_id_debit"));         
            accJDataForm.setItemValue("remarks", accJDataGrid.getUserData(eid, "remarks"));         
            accJDataForm.setItemValue("parent_id", accJDataGrid.getUserData(eid, "parent_id"));
            accJDataForm.setItemValue("trackno", accJDataGrid.getUserData(eid, "trackno")); //27-01-2026        
            accJDataForm.setItemValue("converted", accJDataGrid.getUserData(eid, "converted"));   //12-01-2026       
            accJDataForm.setItemValue("amount_ratio", accJDataGrid.getUserData(eid, "amount_ratio"));   //12-01-2026   
            accJDataForm.setItemValue("ie_type", accJDataGrid.getUserData(eid, "ie_type"));   //04-02-2026      
            preTally.AccountsTeam.hideshowAmt(accJDataForm, accJDataGrid.getUserData(eid, "company_id"),accJDataGrid.getUserData(eid, "office_id")); //12-01-2026                              

            if (accJDataGrid.getUserData(eid, "amt_type") == 1) {
                accJDataForm.showItem("bankaccount");
                preTally.AccountsTeam.loadBankCbo(jdBankCbo, accJDataGrid.getUserData(eid, "office_id"), accJDataGrid.getUserData(eid, "ba_id"), 'accjdata');
                accJDataForm.showItem("bankaccountdr");
                preTally.AccountsTeam.loadBankCbo(jdBankDrCbo, accJDataGrid.getUserData(eid, "office_id"), accJDataGrid.getUserData(eid, "ba_id_debit"), 'accjdata'); 
            } else {
                jdBankCbo.clearAll();
                jdBankCbo.setComboText('Select Bank');
                accJDataForm.hideItem("bankaccount");                
                jdBankDrCbo.clearAll();
                jdBankDrCbo.setComboText('Select Bank');
                accJDataForm.hideItem("bankaccountdr");
            }
            // Loading Branch and select             
            jdBranhCbo.setComboText('');  
            preTally.AccountsTeam.loadBranchCbo(jdBranhCbo, accJDataGrid.getUserData(eid, "company_id"), accJDataGrid.getUserData(eid, "branch_id"), 'accjdata');
            accJDataForm.setItemValue("branch", accJDataGrid.getUserData(eid, "branch_id"));
            jdLocCbo.setComboText('');  
            preTally.AccountsTeam.loadBranchCbo(jdLocCbo, accJDataGrid.getUserData(eid, "office_id"), accJDataGrid.getUserData(eid, "location_id"), 'accjdata');
            accJDataForm.setItemValue("location", accJDataGrid.getUserData(eid, "branch_id")); 
        },
        hideshowAmt: function(cformname, offid1, offid2) { //12-01-2026
            if (offid1 == offid2) {
                cformname.hideItem("amount_ratio");
                cformname.hideItem("converted");
                cformname.setItemValue("amount_ratio", 0);
                cformname.setItemValue("converted", 0);
            } else {
                cformname.showItem("amount_ratio");
                cformname.showItem("converted");
            }
        },
        // Financial year Based opening balance of capital, assets, loan etc....
        acc_OpenBalance: function() {
             if (!dhxMiddleBlockTabs.cells("acc_OpenBalance")) {
                dhxMiddleBlockTabs.addTab("acc_OpenBalance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Opening Balance&nbsp; ", 200);
                dhxMiddleBlockTabs.tabs("acc_OpenBalance").setActive();
                // layout and heading start
                var accOpBalLayout = dhxMiddleBlockTabs.cells("acc_OpenBalance").attachLayout('2U');
                accOpBalLayout.cells("a").setText("Add Opening Value");
                accOpBalLayout.cells("b").setText("List Opening Balance");
                accOpBalLayout.cells("a").setWidth(450);
                // status bar or pagination and count
                accOpBalLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accopbal'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accOpBalGrid = accOpBalLayout.cells("b").attachGrid();
                accOpBalGrid.setHeader("SlNo <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accopbal' class='accGridSort' />, \
                Year "+preTally.AccountsTeam.commonYearCbo('accopbal')+", \
                Title <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='title' gridType='accopbal' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accopbal' id ='accopbalTitle' style='width: 90%;' placeholder='Enter Title'>,\
                Company <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='company' gridType='accopbal' class='accGridSort' /> <div  data-type='accopbal' id='accopbalCompany' style='width: 97%;' placeholder='Company Name'></div>,\
                Branch <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='branch' gridType='accopbal' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accopbal' id ='accopbalBranch' style='width: 90%;' placeholder='Enter Branch'>,\
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amt' gridType='accopbal' class='accGridSort' />,\
                Action");
                accOpBalGrid.setInitWidths("50,100,*,*,*,100,100");
                accOpBalGrid.setColAlign("center,center,left,left,left,right,center");
                accOpBalGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                accOpBalGrid.setColSorting("na,na,na,na,na,na,na");              
                accOpBalGrid.enableTooltips("false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accOpBalGrid, 'accopbal');               
                // company combo filter define and onchange 
                accOpbalCompany = new dhtmlXCombo("accopbalCompany");
                preTally.AccountsTeam.loadOfficeCbo(accOpbalCompany,"accopbalCompany","accopbal");
                // year combo on change set up
                accOpbalYear = new dhtmlXCombo("accopbalYear");                        
                accOpbalYear.attachEvent("onChange", function () {
                    preTally.AccountsTeam.loadOpenBalance('');
                }); 
                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadOpenBalance(''); 
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters(); 

                 //----------------- Attach FORM --------------------//
                accOpbalForm = accOpBalLayout.cells("a").attachForm();
                accOpbalForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addOpenBalance.php"), function () {

                    // loading company and branch and bank combos
                    var obCompCbo       = accOpbalForm.getCombo("company_id");
                    obBranhCbo          = accOpbalForm.getCombo("branch");
                    // find the current values of company
                    var obComp_id       = obCompCbo.getSelectedValue();                    
                    obBranhCbo.setOptionWidth(350);                    
                    obCompCbo.setOptionWidth(350);
                    // load branch combo default
                    preTally.AccountsTeam.loadBranchCbo(obBranhCbo, obComp_id, 0, 'accopbal');
                     // on change the company, office, trans type etc... load branch ad banks
                    accOpbalForm.attachEvent("onChange", function (name, value, state){                         
                        if (name == "company_id")  {
                            obBranhCbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(obBranhCbo, value, 0, 'accopbal');                                                      
                        }  // change company cbo
                    }); // on change form close

                    // save or cancel button click
                    accOpbalForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveOpenBal") {
                            // click on save button  
                            // check the input fields values are correct
                            var isValid = accOpbalForm.validate();
                            if (isValid) {  
                                // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accOpbalForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addOpenBalance.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {
                                        dhtmlx.message({ text: jsonres.message });
                                        var obComp_id       = obCompCbo.getSelectedValue();  
                                        var comp_id         = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                                        accOpbalForm.resetValidateCss();
                                        accOpbalForm.clear();
                                        accOpbalForm.setItemValue("eid", 0); 
                                        accOpbalForm.setItemValue("company_id", comp_id);
                                        obBranhCbo.setComboText('');      
                                        if (obComp_id != comp_id) { 
                                            preTally.AccountsTeam.loadBranchCbo(obBranhCbo, comp_id, 0, 'accopbal');
                                        }// company id not equal to user default id
                                        preTally.AccountsTeam.loadOpenBalance(''); 
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    } // close server save/update failed 
                                }); // server sending close
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "cancelOpenBal") {
                            // click on cancel button
                            var obComp_id       = obCompCbo.getSelectedValue(); 
                            var comp_id         = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                            accOpbalForm.resetValidateCss();
                            accOpbalForm.clear();
                            accOpbalForm.setItemValue("eid", 0); 
                            accOpbalForm.setItemValue("company_id", comp_id);
                            obBranhCbo.setComboText('');
                            if (obComp_id != comp_id) {        
                                preTally.AccountsTeam.loadBranchCbo(obBranhCbo, comp_id, 0, 'accopbal');
                            }// company id not equal to user default id
                           //else {
                                //obBranhCbo.setComboValue('');
                            //}
                        } // cancel button
                    }); // button click
                }); // close the form opening form
             }else { // already opend 
                dhxMiddleBlockTabs.tabs("acc_OpenBalance").setActive();
            }
        },
        loadOpenBalance: function(extra) {
            //console.log("loading Opending balance data based on the search");
            // list all saved Ledger custom Entry Data
            var filterValue = new Array($('#accopbalYear').val(),$('#accopbalTitle').val(),accOpbalCompany.getSelectedValue(),$('#accopbalBranch').val());
            accOpBalGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listOpenBalance.php&filter="+filterValue+extra), function() { 
                $('#total_accopbal').html("# : "+accOpBalGrid.getUserData("", "Data_Count")+" ");
            });
        },
        editOpenBalance: function(eid) {            
            accOpbalForm.setItemValue("eid",eid);  
            accOpbalForm.setItemValue("company_id", accOpBalGrid.getUserData(eid, "company_id")); 
            accOpbalForm.setItemValue("title", accOpBalGrid.getUserData(eid, "title"));    
            accOpbalForm.setItemValue("type", accOpBalGrid.getUserData(eid, "type")); 
            accOpbalForm.setItemValue("ledger_id", accOpBalGrid.getUserData(eid, "ledger_id"));   
            accOpbalForm.setItemValue("amt_type", accOpBalGrid.getUserData(eid, "amt_type"));    
            accOpbalForm.setItemValue("amount", accOpBalGrid.getUserData(eid, "amount"));      
            accOpbalForm.setItemValue("year_date", accOpBalGrid.getUserData(eid, "year_date")); 
            
            // Loading Branch and select             
            obBranhCbo.setComboText('');  
            preTally.AccountsTeam.loadBranchCbo(obBranhCbo, accOpBalGrid.getUserData(eid, "company_id"), accOpBalGrid.getUserData(eid, "branch_id"), 'accopbal');
            accOpbalForm.setItemValue("branch", accOpBalGrid.getUserData(eid, "branch_id"));            
        },
        // same as p and l based on the new requirement
        acc_CashFlow: function() {
            dhtmlx.message({ type: "error", text: "Comming Soon...." });
            if (!dhxMiddleBlockTabs.cells("acc_CashFlow")) {
                accCashTrnsInit = 1;
                dhxMiddleBlockTabs.addTab("acc_CashFlow", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Cash Flow Statement &nbsp;", 200);
                dhxMiddleBlockTabs.tabs("acc_CashFlow").setActive();
                // layout and heading start
                accCFLayout = dhxMiddleBlockTabs.cells("acc_CashFlow").attachLayout('2U');     
                accCFLayout.cells("a").hideHeader();
                accCFLayout.cells("b").hideHeader();
                accCFLayout.cells("a").setWidth(300);

                //----------------- Attach Grid --------------------//
                accCFGrid        = accCFLayout.cells("b").attachGrid();
                accCFGrid.setHeader("Particulars, , DR Amt, Particulars, , CR Amt");
                accCFGrid.setInitWidths("*,100,100,*,100,100");
                accCFGrid.setColAlign("left,right,right,left,right,right");
                accCFGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accCFGrid.setColSorting("str,int,int,str,int,int");              
                accCFGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonServerGrids(accCFGrid, 'acccfw');                

                //----------------- Filter Form For Grid --------------------//               
                accCFForm        = accCFLayout.cells("a").attachForm();
                accCFForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=4"), function () {
                    // fuature bill date block
                    accCFForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accCFForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var cfcompanycbo      = accCFForm.getCombo("company_id");
                    var cfbranchcbo       = accCFForm.getCombo("branch_id");
                    cfcompanycbo.setOptionWidth(350);
                    cfbranchcbo.setOptionWidth(350);             
                    // load branch combo default based on loaded company                    
                    var cfcompany_id      = cfcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(cfbranchcbo, cfcompany_id, 0, 'filt');

                    // on change the company combo
                    accCFForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            cfcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(cfbranchcbo, cfcompany_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accCFForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadCasFlow();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadCasFlow(); 
                    
                }); // form loading close
            } else {
                dhxMiddleBlockTabs.tabs("acc_CashFlow").setActive();
            }
        },
        loadCasFlow: function() {
            accCFLayout.cells("b").setText("Cash Flow Statement  -  ("+accCFForm.getItemValue("from_date", true)+" - "+accCFForm.getItemValue("to_date", true)+") ");
            accCFLayout.cells("b").showHeader();
            var filterValue = new Array(accCFForm.getItemValue("from_date", true),accCFForm.getItemValue("to_date", true),accCFForm.getCombo("company_id").getSelectedValue(),accCFForm.getCombo("branch_id").getSelectedValue(),accCFForm.getCombo("trans_type").getSelectedValue());
            accCFGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptCashFlow.php&filter="+filterValue), function() {                 
            });
        },
        // cash flow amount report data loading ends
        //old data updated after the mapping to new account system 
        acc_ChangeAfterMap : function() {
            if (!dhxMiddleBlockTabs.cells("acc_ChangeAfterMap")) {
                dhxMiddleBlockTabs.addTab("acc_ChangeAfterMap", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Updated After Data Maping &nbsp;", 225);
                dhxMiddleBlockTabs.tabs("acc_ChangeAfterMap").setActive();
                 // layout and heading start
                accCAMLayout = dhxMiddleBlockTabs.cells("acc_ChangeAfterMap").attachLayout("1C");
                accCAMLayout.cells("a").hideHeader();
                //tool bar for date search
                accCAMTbr = accCAMLayout.cells("a").attachToolbar();
                accCAMTbr.setIconsPath("images/icon/default_18/");
                accCAMTbr.setAlign('right');
                preTally.AccountsTeam.customDateFilterToolBar(accCAMTbr,'accupam');
                // staus bar section start
                //accCAMLayout.cells("a").setText("Status bar");
                // status bar or pagination and count
                accCAMLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accupam'),
                    height: 35
                });                

                //----------------- Attach Grid --------------------//
                accCAMGrid        = accCAMLayout.cells("a").attachGrid();
                accCAMGrid.setHeader("Slno, Date, Track No <input type='text' class = 'acc_grid_txt_fltr' gridType='accupam' id ='accupamTrkno' style='width: 80%;' placeholder='Search'>,\
                Ledger Name <input type='text' class = 'acc_grid_txt_fltr' gridType='accupam' id ='accupamledger' style='width: 80%;' placeholder='Search Ledger Name'>,\
                 Amount, Mapping Date, Updated Date, Action");
                accCAMGrid.setInitWidths("70,90,100,*,100,90,90,120");
                accCAMGrid.setColAlign("left,center,left,left,right,center,center,center");
                accCAMGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro"); 
                accCAMGrid.setColSorting("na,na,na,na,na,na,na,na");     
                accCAMGrid.enableTooltips("false,false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accCAMGrid, 'accupam');

                // manage text filters
                preTally.AccountsTeam.accTextFilters();
                // loading data into the grid
                preTally.AccountsTeam.chgAftMapReport(''); 
            } else {
                dhxMiddleBlockTabs.tabs("acc_ChangeAfterMap").setActive();
            }
        },
        chgAftMapReport: function(extras) {
            // list all mapped data list and those original or old account system data will update after amp.
            var filterary   = new Array($('#accupamTrkno').val(),$('#accupamledger').val());
            var filterdate  = "&from="+accCAMTbr.getValue("rpt_date_from")+"&to="+accCAMTbr.getValue("rpt_date_till");
            accCAMGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/update_after_maping.php&filter="+filterary+filterdate+extras), function() { 
                $('#total_accupam').html("# : "+accCAMGrid.getUserData("", "Data_Count")+" ");
            });
        },
        // extra data uploaded via excel file : that are processed and edit 
        acc_ExtraData: function() {
            if (!dhxMiddleBlockTabs.cells("acc_ExtraData")) {
                dhxMiddleBlockTabs.addTab("acc_ExtraData", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Extra Upload Data&nbsp; ", 230);
                dhxMiddleBlockTabs.tabs("acc_ExtraData").setActive();
                // layout and heading start
                var accExtraLayout = dhxMiddleBlockTabs.cells("acc_ExtraData").attachLayout('2U');
                accExtraLayout.cells("a").setText("Add Extra Data");
                accExtraLayout.cells("b").setText("List Extra Uploaded Data");
                accExtraLayout.cells("a").setWidth(450);
                //tool bar for date search
                accExtDtaTbr = accExtraLayout.cells("b").attachToolbar();
                accExtDtaTbr.setIconsPath("images/icon/default_18/");
                accExtDtaTbr.setAlign('left');
                preTally.AccountsTeam.customDateFilterToolBar(accExtDtaTbr,'accextdta');
                // status bar or pagination and count
                accExtraLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accextdta'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accExDatGrid = accExtraLayout.cells("b").attachGrid();
                accExDatGrid.setHeader("SlNo <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='id' gridType='accextdta' class='accGridSort' />, \
                Type, Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' gridType='accextdta' class='accGridSort' />, \
                Title <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='title' gridType='accextdta' class='accGridSort' /> <input type='text' class='acc_grid_txt_fltr' gridType='accextdta' id ='accextdtaTitle' style='width: 90%;' placeholder='Enter Title'>,\
                Branch <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='branch' gridType='accextdta' class='accGridSort' /> <input type='text' class = 'acc_grid_txt_fltr' gridType='accextdta' id ='accextdtaBranch' style='width: 90%;' placeholder='Enter Branch'>,\
                Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='amount' gridType='accextdta' class='accGridSort' />,\
                Action");
                accExDatGrid.setInitWidths("50,80,80,*,*,80,100");
                accExDatGrid.setColAlign("center,left,center,left,left,right,center");
                accExDatGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                accExDatGrid.setColSorting("na,na,na,na,na,na,na");              
                accExDatGrid.enableTooltips("false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(accExDatGrid, 'accextdta');    

                // server side sort fields fetching
                preTally.AccountsTeam.customServerSort();
                // loading data into Grid...
                preTally.AccountsTeam.loadExtraDatas(''); 
                 // text fields filter start
                preTally.AccountsTeam.accTextFilters(); 

                 //----------------- Attach FORM --------------------//
                accExtDtaForm = accExtraLayout.cells("a").attachForm();
                accExtDtaForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/addExtraData.php"), function () {

                    // loading company and branch and bank combos
                    var edCompCbo       = accExtDtaForm.getCombo("company_id");
                    edBranhCbo          = accExtDtaForm.getCombo("branch");
                    edledgerCbo         = accExtDtaForm.getCombo("ledger_id");                  
                    edledgerCbo.setOptionWidth(350);
                    edledgerCbo.enableFilteringMode('between'); 
                    // find the current values of company
                    var edComp_id       = edCompCbo.getSelectedValue();                    
                    edBranhCbo.setOptionWidth(350);                    
                    edCompCbo.setOptionWidth(350);
                    // load branch combo default
                    preTally.AccountsTeam.loadBranchCbo(edBranhCbo, edComp_id, 0, 'accextdta');
                     // on change the company and load branch
                    accExtDtaForm.attachEvent("onChange", function (name, value, state){                         
                        if (name == "company_id")  {
                            edBranhCbo.setComboText(''); 
                            preTally.AccountsTeam.loadBranchCbo(edBranhCbo, value, 0, 'accextdta');                                                      
                        }  // change company cbo
                        else if (name == "ledger_id")  {
                            accExtDtaForm.setItemValue("title", edledgerCbo.getComboText());
                        }// change ledger combo
                        else if (name == "branch")  {
                            accExtDtaForm.setItemValue("branch_name", edBranhCbo.getComboText());
                        }// change branch combo
                    }); // on change form close
                     // future date block
                    accExtDtaForm.getCalendar("job_date").setSensitiveRange(null, new Date());
                     // save or cancel button click
                    accExtDtaForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveExtaData") {
                            // click on save button  
                            // check the input fields values are correct
                            var isValid = accExtDtaForm.validate();
                            if (isValid) { 
                                 // SAVE THE DATA AND SHOW THE SUCCESS OR FAIL MESSAGE
                                accExtDtaForm.send(preTally.Initialize.encryptURL("warehouse/accounts/addExtraData.php&flag=0"), function (loader, response) {
                                    var jsonres = JSON.parse(response);
                                    if (jsonres.status == 1) {
                                        dhtmlx.message({ text: jsonres.message });
                                        var edComp_id       = edCompCbo.getSelectedValue(); 
                                        var comp_id         = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                                        accExtDtaForm.resetValidateCss();
                                        accExtDtaForm.clear();
                                        accExtDtaForm.setItemValue("eid", 0);
                                        accExtDtaForm.setItemValue("addtype", 1);  
                                        accExtDtaForm.setItemValue("company_id", comp_id);
                                        edBranhCbo.setComboText('');
                                        if (edComp_id != comp_id) {        
                                            preTally.AccountsTeam.loadBranchCbo(edBranhCbo, comp_id, 0, 'accextdta');
                                        }// company id not equal to user default id 
                                        preTally.AccountsTeam.loadExtraDatas(''); 
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonres.message });
                                    } // close server save/update failed 
                                }); // server sending close
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            } // validation failed close
                        } else if (name == "cancelExtaData") {
                            // click on cancel button
                            var edComp_id       = edCompCbo.getSelectedValue(); 
                            var comp_id         = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                            accExtDtaForm.resetValidateCss();
                            accExtDtaForm.clear();
                            accExtDtaForm.setItemValue("eid", 0);
                            accExtDtaForm.setItemValue("addtype", 1);  
                            accExtDtaForm.setItemValue("company_id", comp_id);
                            edBranhCbo.setComboText('');
                            if (edComp_id != comp_id) {        
                                preTally.AccountsTeam.loadBranchCbo(edBranhCbo, comp_id, 0, 'accextdta');
                            }// company id not equal to user default id                           
                        } // cancel button
                    }); // button click
                }); // form load close
            } else { // already opend 
                dhxMiddleBlockTabs.tabs("acc_ExtraData").setActive();
            }
        },
        loadExtraDatas: function(extras) {
            // list all extra data entry (uploaded excel and manual added)
            var off_id      = (toolBarOffice['accextdta'].getSelectedValue() > 0) ? toolBarOffice['accextdta'].getSelectedValue() : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            var filterary   = new Array(off_id,$('#accextdtaTitle').val(),$('#accextdtaBranch').val());
            var filterdate  = "&from="+accExtDtaTbr.getValue("rpt_date_from")+"&to="+accExtDtaTbr.getValue("rpt_date_till");            
            accExDatGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/listExtraData.php&filter="+filterary+filterdate+extras), function() { 
                $('#total_accextdta').html("# : "+accExDatGrid.getUserData("", "Data_Count")+" ");
            });
        },
        editExtradataForm: function(eid) {
            accExtDtaForm.setItemValue("eid",eid);  
            accExtDtaForm.setItemValue("company_id", accExDatGrid.getUserData(eid, "company_id")); 
            accExtDtaForm.setItemValue("title", accExDatGrid.getUserData(eid, "title"));    
            accExtDtaForm.setItemValue("type", accExDatGrid.getUserData(eid, "type")); 
            accExtDtaForm.setItemValue("ledger_id", accExDatGrid.getUserData(eid, "ledger_id")); 
            accExtDtaForm.setItemValue("amount", accExDatGrid.getUserData(eid, "amount"));      
            accExtDtaForm.setItemValue("branch_name", accExDatGrid.getUserData(eid, "branch_name"));   
            accExtDtaForm.setItemValue("addtype", accExDatGrid.getUserData(eid, "addtype"));    
            accExtDtaForm.setItemValue("job_date", accExDatGrid.getUserData(eid, "job_date"));    
            
            // Loading Branch and select             
            edBranhCbo.setComboText('');  
            preTally.AccountsTeam.loadBranchCbo(edBranhCbo, accExDatGrid.getUserData(eid, "company_id"), accExDatGrid.getUserData(eid, "branch_id"), 'accextdta');
            accExtDtaForm.setItemValue("branch", accExDatGrid.getUserData(eid, "branch_id")); 
        },
        // Detailed profit and loss account report for ceo Start 15-01-2026
        acc_CPandL: function() {
            dhtmlx.message({ type: "error", text: "Comming Soon...." });
            if (!dhxMiddleBlockTabs.cells("acc_CPandL")) {
                dhxMiddleBlockTabs.addTab("acc_CPandL", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Detailed Profit And Loss Account &nbsp;", 255);
                dhxMiddleBlockTabs.tabs("acc_CPandL").setActive();
                // layout and heading start
                accDPaLLayout = dhxMiddleBlockTabs.cells("acc_CPandL").attachLayout('2U');     
                accDPaLLayout.cells("a").hideHeader();
                accDPaLLayout.cells("b").hideHeader();
                accDPaLLayout.cells("a").setWidth(300);

                //----------------- Attach Grid --------------------//
                accDPaLGrid             = accDPaLLayout.cells("b").attachGrid();
                accDPaLGrid.setHeader("Particulars, Detail, Amount, Particulars, Detail, Amount");
                accDPaLGrid.setInitWidths("*,100,100,*,100,100");
                accDPaLGrid.setColAlign("left,right,right,left,right,right");
                accDPaLGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                accDPaLGrid.setColSorting("str,int,int,str,int,int");              
                accDPaLGrid.enableTooltips("false,false,false,false,false,false");
                preTally.Attendance.commonServerGrids(accDPaLGrid, 'accdpal');                

                //----------------- Filter Form For Grid --------------------//               
                accDPaLForm             = accDPaLLayout.cells("a").attachForm();
                accDPaLForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=0"), function () {
                    // fuature bill date block
                    accDPaLForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accDPaLForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var dpalcompcbo     = accDPaLForm.getCombo("company_id");
                    var dpalbrnhcbo     = accDPaLForm.getCombo("branch_id");
                    dpalcompcbo.setOptionWidth(350);
                    dpalbrnhcbo.setOptionWidth(350);             
                    // load branch combo default based on loaded company                    
                    var dpalcomp_id   = dpalcompcbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(dpalbrnhcbo, dpalcomp_id, 0, 'filt');

                    // on change the company combo
                    accDPaLForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            dpalcomp_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(dpalbrnhcbo, dpalcomp_id, 0, 'filt');
                        }  // change company cbo                                          
                    }); // on change form close

                     // search button click
                    accDPaLForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadDPaL();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadDPaL(); 
                }); // form loading close

            } else {
                dhxMiddleBlockTabs.tabs("acc_CPandL").setActive();
            }
        },
        loadDPaL: function() {           
            accDPaLLayout.cells("b").setText("Detailed Profit and Loss Account  -  ("+accDPaLForm.getItemValue("from_date", true)+" - "+accDPaLForm.getItemValue("to_date", true)+") ");
            accDPaLLayout.cells("b").showHeader();
            var filterValue = new Array(accDPaLForm.getItemValue("from_date", true),accDPaLForm.getItemValue("to_date", true),accDPaLForm.getCombo("company_id").getSelectedValue(),accDPaLForm.getCombo("branch_id").getSelectedValue(),accDPaLForm.getCombo("trans_type").getSelectedValue());
            console.log(filterValue);
            accDPaLGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptDetProfitLoss.php&filter="+filterValue), function() {                 
            });           
        },
        // bank accounts and branch wise cash reports based on the search date and ledger 
        acc_BnkCashRpt: function() {
            if (!dhxMiddleBlockTabs.cells("acc_BnkCashRpt")) {
                dhxMiddleBlockTabs.addTab("acc_BnkCashRpt", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Bank And Branch Reports &nbsp;", 240);
                dhxMiddleBlockTabs.tabs("acc_BnkCashRpt").setActive();
                // layout and heading start
                var accBnkCshRptLayout  = dhxMiddleBlockTabs.cells("acc_BnkCashRpt").attachLayout('2U');     
                accBnkCshRptLayout.cells("a").hideHeader();
                accBnkCshRptLayout.cells("b").hideHeader();
                accBnkCshRptLayout.cells("a").setWidth(300);

                 // status bar or pagination and count
                accBnkCshRptLayout.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('accbncsrpt','accexp'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                accBnkCshRptGrid        = accBnkCshRptLayout.cells("b").attachGrid();
                accBnkCshRptGrid.setHeader("Slno,Description,Date, Income, Expense");
                accBnkCshRptGrid.setInitWidths("70,*,90,150,150");
                accBnkCshRptGrid.setColAlign("left,left,center,right,right");
                accBnkCshRptGrid.setColTypes("ro,ro,ro,ro,ro");
                accBnkCshRptGrid.setColSorting("na,str,int,int");              
                accBnkCshRptGrid.enableTooltips("false,false,false,false,false");
                accBnkCshRptGrid.attachFooter(",<div id='accBCRBT'>0</div>,,<div id='accBCRIT'>0</div>,<div id='accBCRET'>0</div>",["","","","text-align:right;","text-align:right;"]);
                preTally.Attendance.commonGridDefine(accBnkCshRptGrid, 'accbncsrpt');
               
                //----------------- Filter Form For Grid --------------------//               
                accBnkCshRptForm        = accBnkCshRptLayout.cells("a").attachForm();
                accBnkCshRptForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/plFilterForm.php&flag=5"), function () {
                    // fuature bill date block
                    accBnkCshRptForm.getCalendar("from_date").setSensitiveRange(null, new Date());
                    accBnkCshRptForm.getCalendar("to_date").setSensitiveRange(null, new Date());
                    // loading company and branch combos
                    var bcrcompanycbo   = accBnkCshRptForm.getCombo("company_id");
                    var bcrbranchcbo    = accBnkCshRptForm.getCombo("branch_id");
                    var bcrbnkcbo       = accBnkCshRptForm.getCombo("bank_acc_id");
                    bcrcompanycbo.setOptionWidth(350);
                    bcrbranchcbo.setOptionWidth(350);                 
                    // load branch combo and bank names default based on loaded company                    
                    var bcrcompany_id      = bcrcompanycbo.getSelectedValue();       
                    preTally.AccountsTeam.loadBranchCbo(bcrbranchcbo, bcrcompany_id, 0, 'filt');
                    preTally.AccountsTeam.loadBankCbo(bcrbnkcbo, bcrcompany_id, 0, 'accbncsrpt');
                    // on change the company combo
                    accBnkCshRptForm.attachEvent("onChange", function (name, value, state){                        
                        if (name == "company_id")  {
                            bcrcompany_id    = value;
                            preTally.AccountsTeam.loadBranchCbo(bcrbranchcbo, bcrcompany_id, 0, 'filt');
                            preTally.AccountsTeam.loadBankCbo(bcrbnkcbo, bcrcompany_id, 0, 'accbncsrpt');
                        }  // change company cbo                                          
                    }); // on change form close
                    // search button click
                    accBnkCshRptForm.attachEvent("onButtonClick", function (name) {
                        if (name == "searchPandL") {
                            preTally.AccountsTeam.loadBnkCashRpt();             
                        } // search button
                    }); // button click

                    // loading data into Grid after loading form...
                    preTally.AccountsTeam.loadBnkCashRpt(); 
                 }); // form loading close
                 
            } else { //cell block click active check else
                dhxMiddleBlockTabs.tabs("acc_BnkCashRpt").setActive();
            }
        }, 
        loadBnkCashRpt: function() {
            var filterValue = new Array(accBnkCshRptForm.getItemValue("from_date", true),accBnkCshRptForm.getItemValue("to_date", true),accBnkCshRptForm.getCombo("company_id").getSelectedValue(),accBnkCshRptForm.getCombo("branch_id").getSelectedValue(),accBnkCshRptForm.getCombo("bank_acc_id").getSelectedValue(),accBnkCshRptForm.getCombo("cash_bank_type").getSelectedValue());
            accBnkCshRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/accounts/rptBankCash.php&filter="+filterValue), function() { 
                $('#accBCRIT').html(accBnkCshRptGrid.getUserData("", "total_income"));
                $('#accBCRET').html(accBnkCshRptGrid.getUserData("", "total_expense"));
                $('#accBCRBT').html(accBnkCshRptGrid.getUserData("", "total_balance"));
                $('#total_accbncsrpt').html("# : "+accBnkCshRptGrid.getUserData("", "Data_Count")+" ");
            });               
        }, 
        exportaccbncsrpt: function() {
            var filterValue = new Array(accBnkCshRptForm.getItemValue("from_date", true),accBnkCshRptForm.getItemValue("to_date", true),accBnkCshRptForm.getCombo("company_id").getSelectedValue(),accBnkCshRptForm.getCombo("branch_id").getSelectedValue(),accBnkCshRptForm.getCombo("bank_acc_id").getSelectedValue(),accBnkCshRptForm.getCombo("cash_bank_type").getSelectedValue());
           preTally.AccountsTeam.commonExport(filterValue, 4); 
        },












        





            /*$( "#BTRptAmt" ).keyup(function(value) {
                    var mask = this.value;
                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
                    filtrBranchInterval = setInterval( function() { 
                        preTally.BranchBSReports.applyFilter(mask); 
                        clearInterval(filtrBranchInterval); 
                    }, 500);
                });*/
        commonStatusVal: function(status, flag) {
            flag    = (typeof flag != "undefined") ? flag:0;
            switch(status) {
                case '1' : return (flag == 2) ? "Active" : "Approved";
                break;
                case '0' : return (flag == 1) ? "<div style='color:red;'>Blocked</div>" :"Blocked";
                break;
                case '2' : return "Pending";
                break;
                case '4' : return "Rejected";
                break;
                default: return status;
                break;
            }
        },
        commonStatusCbo: function(type, flag) {
            flag    = (typeof flag != "undefined") ? flag:0;
            if (flag == 1) {
                return "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:98%;' id='"+type+"Status'><option value=''>All</option><option value='1'>Active</option><option value='0'>Blocked</option></select>";
            } else if (flag == 2) {
                return "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:98%;' id='"+type+"Status'><option value=''>All</option><option value='1'>Completed</option><option value='2'>Pending</option><option value='2'>Suspended</option></select>";
            }
            return "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:98%;' id='"+type+"Status'><option value=''>All</option><option value='1'>Approved</option><option value='2'>Pending</option><option value='0'>Blocked</option><option value='4'>Rejected</option></select>";
        },
        commonGTypeCbo: function(type) {
            var grouptypes  = preTally.Attendance.commonPostSyn("requisites/customJsonData.php&flag=2");
            var seltgrptype = "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:98%;' id='"+type+"Type'><option value=''>All</option>"; 
            for (const [key, value] of Object.entries(grouptypes.data)) {
                seltgrptype += "<option value='"+key+"'>"+value+"</option>";
            }
            seltgrptype     += "</select>";

            return seltgrptype;
        },
        commonYearCbo: function(type) {
            var yearcbo = "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:98%;' id='"+type+"Year'><option value=''>All Year</option>";
            for (var i  = Date.today().getFullYear(); i >= 2025; i--) {
                var j   = parseInt(i)+1;
                yearcbo += "<option value='"+i+"-"+j+"'>"+i+" - "+j+"</option>";
            }
            yearcbo     += "</select>";

            return yearcbo;
        },
        commonDMYCbo: function(type) {
            var returnCbos  = "<select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:30px;' id='"+type+"Days'><option value='0'>Day</option>";
            for (var i  = 1; i <= 31; i++) {
                returnCbos += "<option value='"+i+"'>"+i+"</option>";
            }
            returnCbos     += "</select><select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:35px;' id='"+type+"Months'><option value='0'>Month</option>";
            var months      = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            for (var i  = 12; i > 0 ; i--) {
                returnCbos += "<option value='"+i+"'>"+months[i-1]+"</option>";
            }
            returnCbos     += "</select><select class='acc_grid_cbo_fltr' gridType='"+type+"' style='width:35px;' id='"+type+"Years'><option value='0'>Year</option>";
            for (var i  = Date.today().getFullYear(); i >= 2025; i--) {
                returnCbos += "<option value='"+i+"'>"+i+"</option>";
            }
            returnCbos     += "</select>";

            return returnCbos;
        },
        commonTextValid: function(formid, fieldid) {
            var remark = formid.getItemValue(fieldid);
            if (remark != '') {
                // check the remarks fields have invalid charactors
                var chkRemark = /^[_a-zA-Z0-9 .,():-]+$/.test(remark);
                if ( !chkRemark ) {                             
                    dhtmlx.message({ type: "error", text: "Description/Remarks contain invalid characters. Only letters, numbers, spaces, periods (.) and commas (,) are allowed." });
                    return false; 
                }
            }
            return true;
        },        
        editStatusColumn: function(gridid, colid, savepage) {
            //status change in the grid                
            gridid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                var approve_acl     = gridid.getUserData("", "approve_acl");
                if (approve_acl == 0) {
                    return false;
                }
                var status_type     = (typeof gridid.getUserData("", "status_type") != "undefined") ? gridid.getUserData("", "status_type") :1;
                if (gridid.getUserData(rId, "edit_status") == 0) { // parent blocked 
                    return false;
                }
                var status      = gridid.getUserData(rId, "status");
                if (stage == 0) {
                   //console.log("before edit...");
                    if (cInd == colid) {       
                        cellComboObject = gridid.cells(rId, cInd).getCellCombo();
                        cellComboObject.readonly(true);
                        cellComboObject.clearAll();
                        if (status == 2) { //pending
                            cellComboObject.addOption('1', 'Approve');
                            cellComboObject.addOption('4', 'Reject');
                            cellComboObject.addOption('2', 'Pending');
                        } else {
                            cellComboObject.addOption('0', 'Block');
                            cellComboObject.addOption('1', 'Activate');
                        }                                
                    } else {
                        gridid.disableCell(rId,cInd,true);
                    }
                } else if (stage == 2 && status != nValue && !isNaN(nValue)) { 
                    //console.log("old value",status," - After column changed:"+nValue, "selected Id:",rId, "---",parseInt(nValue),"----");                        
                    dhtmlx.confirm({
                        title: "Confirm",
                        type: "confirm-warning",
                        ok: "Yes", cancel: "No",
                        text: "Do you want to change the status ?",
                        callback: function (result) {
                            if (result) {
                               $.post(preTally.Initialize.encryptURL("warehouse/accounts/"+savepage+"&flag=1&status=" + nValue+"&eid="+rId), function (response) {
                                    var jsonResponse = JSON.parse(response);
                                    if (jsonResponse.status == 1) {
                                        dhtmlx.message({ text: jsonResponse.message });
                                        gridid.setUserData(rId, "status",nValue);
                                        //console.log("new Status ",preTally.AccountsTeam.commonStatusVal(nValue));

                                        gridid.cells(rId, cInd).setValue(preTally.AccountsTeam.commonStatusVal(nValue, status_type));
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonResponse.message });
                                        gridid.cells(rId, cInd).setValue(oValue);
                                    }
                                });
                            } else {
                                gridid.cells(rId, cInd).setValue(oValue);                                    
                            }
                        }
                    });
                } else if (stage == 2) {
                    return false;
                }
                
                return true;
            });                 
        },
        changeCboCustom: function(type) {
            /*accTypeCbo['accgrp'].attachEvent("onClose", function () {
                console.log("type combo selected");
                var typeid = accTypeCbo['accgrp'].getSelectedValue();
                console.log("type selected:", typeid);
            });*/
            if (typeof accTypeCbo[type] != "undefined") {
                accTypeCbo[type].attachEvent("onChange", function () {
                    //console.log("type combo changed");
                    var typeid = accTypeCbo[type].getSelectedValue();    
                    preTally.AccountsTeam.clearCboCustom(type,1);
                    if (typeid != "" && typeid != 0) {
                        var group_id  = 0;
                        var delete_id = 0;
                        if (type == 'accgrp') {
                            group_id = accGrpForm.getItemValue('mgroup_id');
                        } else  if (type == 'accledger') {
                            group_id = accLedgerForm.getItemValue('mgroup_id');
                        } else  if (type == 'accmledger') {
                            group_id = accmLedgerForm.getItemValue('mgroup_id');
                        }
                        accGroupCbo[type].load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=3&parent_id=0&type=" + typeid+"&group_id="+group_id+"&delete_id="+delete_id), function () {
                            //preTally.UserProfile.applyFilterHandler(accGroupCbo[type]);
                            accGroupCbo[type].enableFilteringMode('between'); 
                            //accGroupCbo['accgrp'].setOptionWidth(230);
                            accGroupCbo[type].setComboValue(group_id);
                        }); 
                    }                        
                });
            }
            if (type != 'accgrp') {

                if (typeof accGroupCbo[type] != "undefined") {
                // not used this in group adding page there is no sub group combo
                accGroupCbo[type].attachEvent("onChange", function () {
                    //console.log("Group combo changed");
                    var mgroupid = accGroupCbo[type].getSelectedValue();
                    preTally.AccountsTeam.clearCboCustom(type,2);
                    if (mgroupid > 0) {
                        var sgroup_id = 0;
                        if (type == 'accledger') {
                            sgroup_id = accLedgerForm.getItemValue('sgroup_id');
                        } else  if (type == 'accmledger') {
                            sgroup_id = accmLedgerForm.getItemValue('sgroup_id');
                        }
                        accSGroupCbo[type].load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=3&parent_id=" + mgroupid+"&group_id="+sgroup_id), function () {
                            //preTally.UserProfile.applyFilterHandler(accSGroupCbo[type]);
                            accSGroupCbo[type].enableFilteringMode('between');                            
                            accSGroupCbo[type].setComboValue(sgroup_id);
                        }); 
                    }
                });
                }   
                if (typeof accSGroupCbo[type] != "undefined") {
                accSGroupCbo[type].attachEvent("onChange", function () {
                    //console.log("Sub Group combo changed");
                    var sgroup_id = accSGroupCbo[type].getSelectedValue();
                    preTally.AccountsTeam.clearCboCustom(type,3);
                    if (sgroup_id > 0) {
                        var mledger_id  = 0;
                        var delete_id   = 0;
                        if (type == 'accledger') {
                            mledger_id = accLedgerForm.getItemValue('mledger_id');
                            delete_id  = accLedgerForm.getItemValue('eid');
                        } else if (type == 'accmledger') {
                            mledger_id = accmLedgerForm.getItemValue('mledger_id');
                        }
                        accLedgerCbo[type].load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=4&parent_id=0&ledger_id=" + mledger_id+"&group_id="+sgroup_id+"&delete_id="+delete_id), function () {
                            //preTally.UserProfile.applyFilterHandler(accLedgerCbo[type]);
                            accLedgerCbo[type].enableFilteringMode('between');
                            accLedgerCbo[type].setComboValue(mledger_id);
                        }); 
                    }
                });  
                }              
            }
            if (type != 'accledger' && type != 'accgrp') {
                if (typeof accLedgerCbo[type] != "undefined") {
                // change the ledger and load sub ledger
                accLedgerCbo[type].attachEvent("onChange", function () {
                    //console.log("Ledger combo changed");
                    var mledger_id = accLedgerCbo[type].getSelectedValue();
                    preTally.AccountsTeam.clearCboCustom(type,4);
                    if (mledger_id > 0) {
                        var sledger_id = 0;
                        if (type == 'accmledger') {
                            sledger_id = accmLedgerForm.getItemValue('sledger_id');
                        }
                        accSLedgerCbo[type].load(preTally.Initialize.encryptURL("requisites/customCbo.php&flag=4&parent_id="+mledger_id+"&ledger_id=" + sledger_id), function () {
                            //preTally.UserProfile.applyFilterHandler(accSLedgerCbo[type]);
                            accSLedgerCbo[type].enableFilteringMode('between');
                            accSLedgerCbo[type].setComboValue(sledger_id);
                        });
                    }
                });
                }
            }
        },
        clearCboCustom: function(type, flag, selcbo) {
            if (flag == 1) {
                //console.log("clear flag 1");
                accGroupCbo[type].clearAll();
                accGroupCbo[type].setComboText('Select');                
                accGroupCbo[type].setComboValue('');
            } 
            if (flag <= 2 && type != 'accgrp') {
                //console.log("clear flag 2");
                accSGroupCbo[type].clearAll();
                accSGroupCbo[type].setComboText('Select');             
                accSGroupCbo[type].setComboValue('');
            } 
            if (flag <= 3 && type != 'accgrp') {
                //console.log("clear flag 3");
                accLedgerCbo[type].clearAll(); 
                accLedgerCbo[type].setComboText('Select');           
                accLedgerCbo[type].setComboValue('');
            } 
            if (flag <= 4 && type != 'accgrp' && type != 'accledger') {
                //console.log("clear flag 4");
                accSLedgerCbo[type].clearAll(); 
                accSLedgerCbo[type].setComboText('Select');         
                accSLedgerCbo[type].setComboValue('');
            }

            if (typeof selcbo !== "undefined" && flag == 100) { // flag = 100 type == ''
                selcbo.clearAll(); 
                selcbo.setComboText('Select');
                selcbo.setComboValue('');
            }
        },
        customServerSort: function() {
            $('.accGridSort').unbind('click');
            $('.accGridSort').click(function () {
                var type    = $(this).attr("gridType");  
                var sfield  = $(this).attr("sortField"); 
                var sorder  = 'ASC';
                if ($(this).attr("src") == 'images/icon/sort-descending-icon.png') {
                    $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                    sorder  = 'ASC';
                } else {
                    $(this).attr("src", 'images/icon/sort-descending-icon.png');
                    sorder  = 'DESC'; 
                }
                var extrafilter = "&sort="+sfield+"&order="+sorder;
                switch (type) {
                    case 'accledger'    : preTally.AccountsTeam.loadLedgerList(extrafilter);
                    break;
                    case 'accmledger'   : preTally.AccountsTeam.loadMapLedgerList(extrafilter);
                    break;
                    case 'accjournal'   : preTally.AccountsTeam.loadJournals(extrafilter);
                    break;
                    case 'accmapjnl'    : preTally.AccountsTeam.loadItemJournal(extrafilter);
                    break;  
                    case 'acctds'       : preTally.AccountsTeam.loadTdsList(extrafilter);
                    break; 
                    case 'accvendor'    : preTally.AccountsTeam.loadVendorList(extrafilter);
                    break; 
                    case 'accbills'     : preTally.AccountsTeam.loadBillList(extrafilter);
                    break;  
                    case 'accbillr'     : preTally.AccountsTeam.loadBillRCList(extrafilter);
                    break;  
                    case 'accjentry'    : preTally.AccountsTeam.loadJournalEntry(extrafilter);
                    break; 
                    case 'accjdata'     : preTally.AccountsTeam.loadLedgerData(extrafilter);
                    break; 
                    case 'accopbal'     : preTally.AccountsTeam.loadOpenBalance(extrafilter);
                    break; 
                    case 'trackexp'     : preTally.ManageTracks.filterTrackExpense(extrafilter);
                    break; 
                    case 'accextdta'    : preTally.AccountsTeam.loadExtraDatas(extrafilter);
                    break; 

                }         
            });            
        },
        clearFormlayout: function(layoutid, formid) {
            if (formid.getItemValue("allow_add") == 0) {
                layoutid.cells("a").collapse();
                layoutid.cells("a").hideHeader();
                layoutid.cells("a").hideArrow();
                layoutid.cells("a").detachObject();
                layoutid.cells("b").hideArrow();
            }            
        },
        loadBranchCbo: function(cboids, officeid, selectid, type) {
            //load cbos
            officeid    =  (typeof officeid != "undefined" && officeid > 0) ? officeid : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            selectid    =  (typeof selectid != "undefined" && selectid > 0) ? selectid : 0;
            cboids.load(preTally.Initialize.encryptURL("requisites/locations.php&seltdQffz="+selectid+"&ofid="+officeid+"&type="+type), function () {                
                preTally.UserProfile.applyFilterHandler(cboids);                
            }); 
        },
        loadBankCbo: function(cboids, officeid, selectid, type, cboname) {
            //load cbos
            officeid    =  (typeof officeid != "undefined" && officeid > 0) ? officeid : unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            selectid    =  (typeof selectid != "undefined" && selectid > 0) ? selectid : 0;
            cboids.load(preTally.Initialize.encryptURL("requisites/bankaccounts.php&seltdId="+selectid+"&ofid="+officeid+"&type="+type), function () {                
                cboids.setOptionWidth(350);  
                cboids.enableFilteringMode('between');               
            }); 

            if (type == 'bank') {
                // on change the branchs start                 
                cboids.attachEvent("onChange", function () {
                    var bankVal = cboids.getSelectedValue();
                    if (!cboids.getSelectedValue() && cboids.getComboText())
                        bankVal = cboids.getComboText();
                    $("#"+cboname).val(bankVal);
                    switch (type) {
                        case 'bank' : preTally.AccountsTeam.bankFilterReport('');
                        break;
                    }
                });  
            }
               
        },
        roundToDecimal: function(num, decimals) {
            // 2 decimal point default to all inputs
            if (num == parseInt(num) ) {
                return num+'.00';
            }       
            const factor = Math.pow(10, decimals);
            return Math.round(num * factor) / factor;
        },
        commonGridDelete: function(gridtype, id, savepage) {
            dhtmlx.confirm({
                title: "Confirm",
                type: "confirm-warning",
                ok: "Yes", cancel: "No",
                text: "Do you want to delete ?",
                callback: function (result) {
                    if (result) {
                       $.post(preTally.Initialize.encryptURL("warehouse/accounts/"+savepage+"&flag=2&eid="+id), function (response) {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.status == 1) {
                                dhtmlx.message({ text: jsonResponse.message });
                                switch (gridtype) {
                                    case 'accbillr'     : preTally.AccountsTeam.loadBillRCList('');
                                    break; 

                                }
                            } else {
                                dhtmlx.message({ type: "error", text: jsonResponse.message });
                            }
                        });
                    }
                }
            });                         
        },
        // common export function based on flag
        commonExport: function(filters, flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                preTally.Initialize.encryptURL('warehouse/accounts/commonExcelExport.php&flag='+flag),
                { inpdata : filters },
                function(data) {
                    fileName = data.split("XL_");
                    if(fileName[1]) {
                         document.location ="uploads/excelFile/"+fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
                }
            );
        },


	};
})(jQuery, this);