;
(function ($, window, undefined) {
    var filterObject;
    var monthData;
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var prevFirstDay;
    var prevLastDay;
    var selectedFirstDay;
    var selectedLastDay;
    var branchItemRptMode = 1;
    var itemRptCombo;
    var itmFiltrCombo;
    var addByCombo;
    var ClickInit = 0;
    var appOrd = '';
    var DtOrd = '';
    var ITFilter = '';
    var LCFilter = '';
    var modeFilter = '';
    var typeOfEntry='';
    var subHeadFilter='';
    var mainHeadFilter='';
    var itemRptFlag = 0;
    var branchRptFlag = 0;
    var  visualRptFlag = 0;
    var  branchItemRptFlag = 0;
    var  ItemBasedRptFlag = 0;
    var itmComboVal='';
    var toeComboVal='';
    var itemType;
    preTally.MasterReports = {
        viewMasterReports: function () {            
            branchItemRptMode = 1;
            if (!dhxMiddleBlockTabs.cells("viewMasterReports")) {
                reportInit = 1;
                dhxLayout.cells("b").collapse();
                dhxMiddleBlockTabs.addTab("viewMasterReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshTab'/>", 180);
                dhxMiddleBlockTabs.tabs("viewMasterReports").setActive();

                dhxMasterReportsLayout = dhxMiddleBlockTabs.cells("viewMasterReports").attachLayout("1C");

                ptMasterReportToolbar = dhxMasterReportsLayout.cells("a").attachToolbar();
                ptMasterReportToolbar.setIconsPath("images/icon/default_18/");

                ptMasterReportToolbar.setAlign('right');

                $(".refreshTab").click(function () {

                    itemRptFlag = 0;
                    branchRptFlag = 0;
                    visualRptFlag = 0;
                    branchItemRptFlag = 0;
                    ItemBasedRptFlag = 0;
                    var actvId = ptMasterReportsTabbar.getActiveTab();

                    if (actvId == 'viewReportsVisual')
                        preTally.MasterReports.viewReportsVisual();
                    if (actvId == 'viewItemReports')
                        preTally.MasterReports.viewItemReports();
                    if (actvId == 'viewBranchReports')
                        preTally.MasterReports.viewBranchReports();
                    if (actvId == 'viewBrnchItemReports')
                        preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                    if (actvId == 'viewItemBasedReports')
                        preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                });


                var Days_Options = [];
                var Months_Options = [];
                var Years_Options = [];

//                var monthNames = [ 
//                        "January", "February", "March","April", "May", "June",
//                        "July", "August", "September","October", "November", "December"
//                    ];

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
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }


                for (i = Date.today().getFullYear(); i >= 2024; i--) {
                    if (i == Date.today().getFullYear()) {
                        if (Date.today().getMonth() + 1 > 3)
                            Years_Options.push([i, 'obj', i + "-" + (i + 1), "calendar_Y.png"]);
                    } else
                        Years_Options.push([i, 'obj', i + "-" + (i + 1), "calendar_Y.png"]);
                }

                ptMasterReportToolbar.addButtonSelect("rpt_day_filter", '1', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptMasterReportToolbar.addButtonSelect("rpt_year_filter", '1', "Select Year", Years_Options, '', '', true, true, 10, 'select');
                ptMasterReportToolbar.addButtonSelect("rpt_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');

                ptMasterReportToolbar.addSeparator();

                ptMasterReportToolbar.addText("text_from", null, "From");
                ptMasterReportToolbar.addInput("rpt_date_from", null, "", 75);
                ptMasterReportToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                ptMasterReportToolbar.addSeparator();

                ptMasterReportToolbar.addText("text_till", null, "Till");
                ptMasterReportToolbar.addInput("rpt_date_till", null, "", 75);
                ptMasterReportToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                ptMasterReportToolbar.addSeparator();

                ptMasterReportToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                // 15-12-2025
                ptMasterReportToolbar.addSeparator();              
                ptMasterReportToolbar.addButton("excel_export", null , "Export", "excel.png");         
                // 15- 12-2025

                var ptRpTb_Inp_Frm = ptMasterReportToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptMasterReportToolbar.getValue("rpt_date_till"))
                        preTally.MasterReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptMasterReportToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptMasterReportToolbar.getValue("rpt_date_from"))
                        preTally.MasterReports.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptMasterReportToolbar.attachEvent("onClick", function (id) {

                    itemRptFlag = 0;
                    branchRptFlag = 0;
                    visualRptFlag = 0;
                    branchItemRptFlag = 0;
                    ItemBasedRptFlag = 0;

                    var pId = ptMasterReportToolbar.getParentId(id);

                    if (id == 'rpt_df_clear') {
                        ptMasterReportToolbar.setValue('rpt_date_from', '', false);
                        prevFirstDay = '';
                    }

                    if (id == 'rpt_dt_clear') {
                        ptMasterReportToolbar.setValue('rpt_date_till', '', false);
                        prevLastDay = '';
                    }

                    if (pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptMasterReportToolbar.setValue('rpt_date_from', dateToday, false);
                        ptMasterReportToolbar.setValue('rpt_date_till', dateToday, false);
                        ptMasterReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptMasterReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                        monthData = '';
                        var actvId = ptMasterReportsTabbar.getActiveTab();

                        prevFirstDay = dateToday;
                        prevLastDay = dateToday;

                        if (actvId == 'viewReportsVisual')
                            preTally.MasterReports.viewReportsVisual();
                        if (actvId == 'viewItemReports')
                            preTally.MasterReports.viewItemReports();
                        if (actvId == 'viewBranchReports')
                            preTally.MasterReports.viewBranchReports();
                        if (actvId == 'viewBrnchItemReports')
                            preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                        if (actvId == 'viewItemBasedReports') {
                            selectedFirstDay = dateToday;
                            selectedLastDay = dateToday;
                            preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                        }

                    }
                    if (pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptMasterReportToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptMasterReportToolbar.setValue('rpt_date_from', firstDay, false);
                        ptMasterReportToolbar.setValue('rpt_date_till', lastDay, false);
                        ptMasterReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptMasterReportToolbar.setItemText('rpt_year_filter', 'Select Year');

                        if (ptMasterReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData = ptMasterReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');

//                        prevFirstDay = firstDay;
//                        prevLastDay  = lastDay;

                        prevFirstDay = firstDay;
                        prevLastDay = lastDay;

                        var actvId = ptMasterReportsTabbar.getActiveTab();

                        if (actvId == 'viewReportsVisual')
                            preTally.MasterReports.viewReportsVisual();
                        if (actvId == 'viewItemReports')
                            preTally.MasterReports.viewItemReports();
                        if (actvId == 'viewBranchReports')
                            preTally.MasterReports.viewBranchReports();
                        if (actvId == 'viewBrnchItemReports')
                            preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                        if (actvId == 'viewItemBasedReports') {
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                        }

                    }
                    if (pId == 'rpt_year_filter') {
                        var tmpDate = new Date();
                        if (parseInt(id) + 1 == tmpDate.getFullYear())
                            var m = tmpDate.getMonth();
                        else
                            var m = '02';

                        yearData = ptMasterReportToolbar.getListOptionSelected("rpt_year_filter");
                        currentYear = tmpDate.getFullYear();
                        if (yearData == currentYear) {
                            var m = tmpDate.getMonth();
                            var firstDay = new Date(parseInt(id), 03, 1).toString("dd.MM.yyyy");
                            var lastDay = new Date(parseInt(id), m + 1, 0).toString("dd.MM.yyyy");
                        } else {
                            var m = 2;
                            var firstDay = new Date(id, 03, 1).toString("dd.MM.yyyy");
                            var lastDay = new Date(parseInt(id) + 1, m, 31).toString("dd.MM.yyyy");
                        }

                        prevFirstDay = firstDay;
                        prevLastDay = lastDay;

                        ptMasterReportToolbar.setValue('rpt_date_from', firstDay, false);
                        ptMasterReportToolbar.setValue('rpt_date_till', lastDay, false);
                        ptMasterReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptMasterReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        monthData = '';
                        var actvId = ptMasterReportsTabbar.getActiveTab();

                        if (actvId == 'viewReportsVisual')
                            preTally.MasterReports.viewReportsVisual();
                        if (actvId == 'viewItemReports')
                            preTally.MasterReports.viewItemReports();
                        if (actvId == 'viewBranchReports')
                            preTally.MasterReports.viewBranchReports();
                        if (actvId == 'viewBrnchItemReports')
                            preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                        if (actvId == 'viewItemBasedReports') {
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);

                        }

                    }

                    if (id == 'rpt_date_filter') {
                        monthData = '';

                        prevFirstDay = ptMasterReportToolbar.getValue('rpt_date_from');
                        prevLastDay = ptMasterReportToolbar.getValue('rpt_date_till');

                        ptMasterReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptMasterReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptMasterReportToolbar.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = ptMasterReportsTabbar.getActiveTab();
                        if (actvId == 'viewReportsVisual')
                            preTally.MasterReports.viewReportsVisual();
                        if (actvId == 'viewItemReports')
                            preTally.MasterReports.viewItemReports();
                        if (actvId == 'viewBranchReports')
                            preTally.MasterReports.viewBranchReports();
                        if (actvId == 'viewBrnchItemReports')
                            preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                        if (actvId == 'viewItemBasedReports') {

                            selectedFirstDay = ptMasterReportToolbar.getValue('rpt_date_from');
                            selectedLastDay = ptMasterReportToolbar.getValue('rpt_date_till');
                            preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                        }

//                        preTally.MasterReports.viewItemReports(rptFilterID);
                    }
                    if(id == 'excel_export') { //15-12-2025
                        var actvId = ptMasterReportsTabbar.getActiveTab();
                        if (actvId == 'viewBranchReports') {
                            preTally.Settings.ConverttoEXL(BranchReportsGrid);
                        } else if (actvId == 'viewBrnchItemReports') {
                            preTally.Settings.ConverttoEXL(BrnchItemReportsGrid);
                        } else if (actvId == 'viewItemReports') {
                            preTally.Settings.ConverttoEXL(ItemReportsGrid);
                        } else if (actvId == 'viewItemBasedReports') {
                            preTally.Settings.ConverttoEXL(ItemBasedReportsGrid);
                        }

                        
                    }
                });
                ptMRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptMRpTb_Calendar.setDateFormat("%d.%m.%Y");

                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4) {
                    var date = new Date();
                    var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    monthData = date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy");
                    ptMasterReportToolbar.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                    ptMasterReportToolbar.setValue('rpt_date_till', tDate);
                    ptMasterReportToolbar.setItemText('rpt_month_filter', m_names[monthData]);

                    reportInit = 0;
                    monthData = monthData + 1; // get current month number;starting with 1

                    prevFirstDay = "01." + Date.today().toString("MM.yyyy");
                    prevLastDay = tDate;

                } else {
                    cDate = Date.today().toString("dd.MM.yyyy");
                    ptMasterReportToolbar.setValue('rpt_date_till', cDate);
                    ptMasterReportToolbar.setValue('rpt_date_from', cDate);
                    ptMasterReportToolbar.setItemText('rpt_day_filter', 'Today');
                    reportInit = 0;

                    prevFirstDay = prevLastDay = cDate;

                }

//                ptMasterReportToolbar.setAlign('right');

                ptMasterReportsTabbar = dhxMasterReportsLayout.cells("a").attachTabbar();
//                ptMasterReportsTabbar.addTab("viewReportsVisual", tb_data_txt, "465px");
                ptMasterReportsTabbar.addTab("viewReportsVisual", "Overall Reports");
                ptMasterReportsTabbar.addTab("viewBranchReports", "Branch Based Reports");
                ptMasterReportsTabbar.addTab("viewItemReports", "Item Based Reports");
                ptMasterReportsTabbar.addTab("viewBrnchItemReports", "Branch Based Item Reports");
                ptMasterReportToolbar.hideItem("excel_export"); // 15-12-2025
                preTally.MasterReports.viewReportsVisual();
                ptMasterReportsTabbar.tabs("viewReportsVisual").setActive();

                ptMasterReportsTabbar.attachEvent("onTabClick", function (id, last_id) {                    
                    if (id == 'viewBranchReports') {
                        dhxAccord.cells("a4").show();
                        dhxAccord.cells("a4").open();
                    } else {
                        dhxAccord.cells("a4").hide();
                        dhxAccord.cells("a1").open();
                        ptMasterReportToolbar.hideItem("excel_export"); // 15-12-2025                       
                    }
                    if (id == 'viewReportsVisual')
                        preTally.MasterReports.viewReportsVisual();
                    if (id == 'viewItemReports') {
                        ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                        preTally.MasterReports.viewItemReports();
                    }
                    if (id == 'viewBranchReports') {
                        ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                        preTally.MasterReports.viewBranchReports();
                    }
                    if (id == 'viewBrnchItemReports') {
                        ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                        preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                    }
//                    if(id == 'viewBrnchItemReports') preTally.MasterReports.viewBrnchItemReports();

                    if (id == 'viewItemBasedReports') {
                        ptMasterReportToolbar.showItem("excel_export"); // 16-12-2025
                        preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                    }
                    return true;

                });


            } else {
                dhxMiddleBlockTabs.tabs("viewMasterReports").setActive();

                var actvId = ptMasterReportsTabbar.getActiveTab();
                if (actvId == 'viewBranchReports') {
                    dhxAccord.cells("a4").show();
                    dhxAccord.cells("a4").open();
                } else {
                    dhxAccord.cells("a4").hide();
                    dhxAccord.cells("a1").open();
                    ptMasterReportToolbar.hideItem("excel_export"); // 15-12-2025
                }

                if (actvId == 'viewReportsVisual')
                    preTally.MasterReports.viewReportsVisual();
                if (actvId == 'viewItemReports') {
                    ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                    preTally.MasterReports.viewItemReports();
                }
                if (actvId == 'viewBranchReports') {
                    ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                    preTally.MasterReports.viewBranchReports();
                }
                if (actvId == 'viewBrnchItemReports') {                    
                    ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
                    preTally.MasterReports.viewBrnchItemReports(rptBrnchItemId, branchItemRptMode);
                }
                if (actvId == 'viewItemBasedReports') {
                    ptMasterReportToolbar.showItem("excel_export"); // 16-12-2025
                    preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                }
                return true;

            }
        },
        viewReportsVisual: function () {

            ptMasterReportToolbar.setValue("rpt_date_from", prevFirstDay);
            ptMasterReportToolbar.setValue("rpt_date_till", prevLastDay);

            if (visualRptFlag != 1) {

                visualRptFlag = 1;
                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxReportsVisualLayout = ptMasterReportsTabbar.cells("viewReportsVisual").attachLayout("1C");
                dhxReportsVisualLayout.cells("a").hideHeader();

                dhxReportsVisualForm = dhxReportsVisualLayout.cells("a").attachForm();
                rptFilterParams = 'f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till");

                dhxReportsVisualForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReportsVisual.php&" + rptFilterParams), function () {
                    //                $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    //                $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }

        },
        viewBranchReports: function () {

            ptMasterReportToolbar.setValue("rpt_date_from", prevFirstDay);
            ptMasterReportToolbar.setValue("rpt_date_till", prevLastDay);

            if (branchRptFlag != 1) {
//                var filterObject;
                branchRptFlag = 1;
                dhxBranchReportsLayout = ptMasterReportsTabbar.cells("viewBranchReports").attachLayout("1C");
                dhxBranchReportsLayout.cells("a").hideHeader();

                //BranchReportsGrid.destructor();                
                BranchReportsGrid = dhxBranchReportsLayout.cells("a").attachGrid();
                BranchReportsGrid.setImagePath("../../codebase/imgs/");
                BranchReportsGrid.setHeader("SlNo,Branch,Opening Balance,#cspan,#cspan,Income,#cspan,Expense,#cspan,Closing Balance,#cspan,#cspan,<div style='text-align:left;'>Business <img src='images/icon/arrow_g_16.png' title='View Internal Transfers' class='btn_IT'/></div>,Internal Transfer Received,#cspan,Internal Transfer Paid,#cspan");
                BranchReportsGrid.attachHeader("#,#text_filter,Cash,Bank,Stock,<div>Cash<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_mstrbrnchrptSort' /></div>,<div>Bank<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_mstrbrnchrptSort' /></div>,<div>Cash<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='7' class='btn_mstrbrnchrptSort' /></div>,<div>Bank<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='8' class='btn_mstrbrnchrptSort' /></div>,<div>Cash<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='9' class='btn_mstrbrnchrptSort' /></div>,Bank,Stock,Business,Cash,Bank,Cash,Bank");
                BranchReportsGrid.setInitWidths("40,*,*,*,*,*,*,*,*,*,*,*,100,*,*,*,*")
                BranchReportsGrid.setColAlign("center,left,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right")
                BranchReportsGrid.setColTypes("ro,ro,price,price,price,price,price,price,price,price,price,price,price,price,price,price,price")
                BranchReportsGrid.init();
                BranchReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                BranchReportsGrid.enableEditEvents(false, false, false);
                BranchReportsGrid.enableColSpan(true);
                BranchReportsGrid.setSkin("dhx_skyblue")
                BranchReportsGrid.enableSmartRendering(true, 50);
                BranchReportsGrid.attachFooter("Sum,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>");
                BranchReportsGrid.attachFooter("Total Amount,#cspan,<span style='float:right;'><div id='mr_OB'>0</div></span>,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_INC'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_EXP'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_CB'>0</div></span>,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_TR'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_TP'>0</div></span>,#cspan");
                BranchReportsGrid.attachFooter("<span style='float:right;'><div id='mr_OBS'>0</div></span>,#cspan,#cspan,#cspan,#cspan,<span style='float:right;'><div id='mr_INCS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_EXPS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_CBS'>0</div></span>,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_TRS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_TPS'>0</div></span>,#cspan");
                BranchReportsGrid.setNumberFormat('0000', 2);
                BranchReportsGrid.setNumberFormat('0000', 3);
                BranchReportsGrid.setNumberFormat('0000', 4);
                BranchReportsGrid.setNumberFormat('0000', 5);
                BranchReportsGrid.setNumberFormat('0000', 6);
                BranchReportsGrid.setNumberFormat('0000', 7);
                BranchReportsGrid.setNumberFormat('0000', 8);
                BranchReportsGrid.setNumberFormat('0000', 9);
                BranchReportsGrid.setNumberFormat('0000', 10);
                BranchReportsGrid.setNumberFormat('0000', 11);
                BranchReportsGrid.setNumberFormat('0000', 12);
                BranchReportsGrid.setNumberFormat('0000', 13);
                BranchReportsGrid.setNumberFormat('0000', 14);
                BranchReportsGrid.setNumberFormat('0000', 15);
                BranchReportsGrid.setNumberFormat('0000', 16);

                BranchReportsGrid.setColumnHidden(13, true);
                BranchReportsGrid.setColumnHidden(14, true);
                BranchReportsGrid.setColumnHidden(15, true);
                BranchReportsGrid.setColumnHidden(16, true);
                var srtFlg = 0;
                $('.btn_mstrbrnchrptSort').click(function () {
                    var colId = $(this).attr("colNum")
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BranchReportsGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BranchReportsGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                });

                BranchReportsGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                BranchReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                rptFilterParams = 'f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till");

                BranchReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBranchData.php&" + rptFilterParams), function () {
                    filterObject = BranchReportsGrid.getFilterElement(1);
                    filterObject.onkeyup = function () {
                        if (reportDetailsGrid)
                            reportDetailsGrid.clearSelection();
                    };
                    filterObject.placeholder = "Branch";
                    preTally.MasterReports.calculateFooterValues();
                    var flag = 1;
                    $('.btn_IT').click(function () {

                        if (flag == 1) {
                            $(this).attr("src", 'images/icon/arrow_lr_16.gif');
                            $(this).attr("title", 'Hide Internal Transfers');

                            BranchReportsGrid.setColumnHidden(13, false);
                            BranchReportsGrid.setColumnHidden(14, false);
                            BranchReportsGrid.setColumnHidden(15, false);
                            BranchReportsGrid.setColumnHidden(16, false);

                            //                        preTally.MasterReports.calculateFooterValues();

                            flag = 0;

                        } else {

                            $(this).attr("src", 'images/icon/arrow_g_16.png');
                            $(this).attr("title", 'View Internal Transfers');

                            BranchReportsGrid.setColumnHidden(13, true);
                            BranchReportsGrid.setColumnHidden(14, true);
                            BranchReportsGrid.setColumnHidden(15, true);
                            BranchReportsGrid.setColumnHidden(16, true);

                            //                        preTally.MasterReports.calculateFooterValues();

                            flag = 1;
                        }



                    });

                });

                BranchReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    preTally.MasterReports.calculateFooterValues();
                    if (BranchReportsGrid.getRowsNum() == 0) {
                        BranchReportsGrid.addRow("row1", ['', 'Record not Found', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], 0);
                        BranchReportsGrid.setColspan("row1", 1, 16);
                        BranchReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        //preTally.MasterReports.calculateFooterValues();  
                    }

                });
                //        }else{
                //            ptMasterReportsTabbar.tabs("viewBranchReports").setActive();     
            }

            if (reportDetailsGrid)
                reportDetailsGrid.destructor();

            dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp; Legend Details");
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("<div style='text-align:left;'>Branch <input type = 'button' name='ALL' value = 'View All' class='btn_All'/></div>");
            reportDetailsGrid.setInitWidths("*");
            reportDetailsGrid.setColAlign("left");
            reportDetailsGrid.setColTypes("ro");
            reportDetailsGrid.enableTooltips("false");

            reportDetailsGrid.init();
            reportDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/rpt_listBranch.php"), function () {

                $('.btn_All').click(function () {
                    BranchReportsGrid.filterBy(1, '');
                    filterObject.value = "All";
                    if (reportDetailsGrid)
                        reportDetailsGrid.clearSelection();

                    preTally.MasterReports.calculateFooterValues();

                });

                reportDetailsGrid.attachEvent("onRowSelect", function (id, data) {
//                       console.log(reportDetailsGrid.cells(id,0).getValue()+"--"+this.value);
                    var LCfilterText = reportDetailsGrid.cells(id, 0).getValue();
                    if (LCfilterText == 'ALL')
                        LCfilterText = '';
                    BranchReportsGrid.filterBy(1, LCfilterText);
                    filterObject.value = LCfilterText;

                    preTally.MasterReports.calculateFooterValues();
                });
            });
        },
        viewItemReports: function () {            
            ptMasterReportToolbar.setValue("rpt_date_from", prevFirstDay);
            ptMasterReportToolbar.setValue("rpt_date_till", prevLastDay);
            
            if (itemRptFlag != 1) { 
                dhxItemReportsLayout = ptMasterReportsTabbar.cells("viewItemReports").attachLayout("1C");
                dhxItemReportsLayout.cells("a").hideHeader();
                itemRptFlag = 1;
                toeComboVal='';
                itmComboVal='';
                ItemReportsGrid = dhxItemReportsLayout.cells("a").attachGrid();
                ItemReportsGrid.setImagePath("../../codebase/imgs/");
                ItemReportsGrid.setSkin("dhx_skyblue")                
                //ItemReportsGrid.setHeader("<div>SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort' /></div>,<div id='itmType' style='width: 90%;' placeholder='Item Type'></div>,<div id='itmRF' style='width: 90%;' placeholder='Item'></div>,#combo_filter,#combo_filter,<div>Verified Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort'/></div>,<div>Not-Verified Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_Sort'/></div>,,#combo_filter");
                //26-12-2024
                ItemReportsGrid.setHeader("<div>SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort' /></div>,<div id='itmType' style='width: 90%;' placeholder='Item Type'></div>,<input type='text' name='itmRF' id='itmRF' style='width: 90%;' placeholder='Item' autocomplete='off'>,#combo_filter,#combo_filter,<div>Verified Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort'/></div>,<div>Not-Verified Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_Sort'/></div>,,#combo_filter");
                ItemReportsGrid.setNumberFormat("0,000.00", 4);
                ItemReportsGrid.setInitWidths("50,100,*,350,150,135,120,50,0");
                ItemReportsGrid.setColAlign("right,left,left,left,left,right,right,center,center");
                ItemReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
                ItemReportsGrid.enableColSpan(true);
//                ItemReportsGrid.setColSorting("int,str,str,str,str");  
                ItemReportsGrid.enableEditEvents(true, true, true);
                ItemReportsGrid.enableTooltips("false,false,false,false");
                ItemReportsGrid.attachFooter("<div onclick='preTally.MasterReports.viewBrnchReports()' style='width:100%;cursor: pointer;'>Total</div>,#cspan,#cspan,#cspan,#cspan,<div style='width:100%;float:right;cursor: pointer;' title='Branchwise Report of Listed Items' onclick='preTally.MasterReports.viewBrnchReports()'>{#stat_total}</div>,<div style='width:100%;float:right;cursor: pointer;' title='Branchwise Report of Listed Items' onclick='preTally.MasterReports.viewBrnchReports()'>{#stat_total}</div>,<span ></span>");
                //ItemReportsGrid.attachFooter("<span style='float:left;'>Expense : <div style='float:right;' id='itmRptExpense'> 0 </div></span>,#cspan,<span style='float:left;'> Income : <div style='float:right;' id='itmRptIncome'> 0 </div></span>,<span style='float:left;'> Business Returned : <div style='float:right;' id='itmRptBusinessReturn'> 0 </div></span>,<span style='float:left;'>Business Received : <div style='float:right;' id='itmRptBusinessReceived'> 0 </div></span>,<span style='float:left;'> Internal Transfer Paid : <div style='float:right;' id='itmRptInternalTransferPaid'> 0 </div></span>,<span style='float:left;'> Internal Transfer Received : <div style='float:right;' id='itmRptInternalTransferReceived'> 0 </div></span>,#cspan");
               // ItemReportsGrid.attachFooter("<span style='float:left;'> Internal Transfer Received : <div style='float:right;' id='itmRptInternalTransferReceived'> 0 </div></span>,#cspan,<span style='float:left;'> Internal Transfer Paid : <div style='float:right;' id='itmRptInternalTransferPaid'> 0 </div></span>,<span style='float:left;'>Business Received : <div style='float:right;' id='itmRptBusinessReceived'> 0 </div></span>,<span style='float:left;'> Business Returned : <div style='float:right;' id='itmRptBusinessReturn'> 0 </div></span>,<span style='float:left;'> Income : <div style='float:right;' id='itmRptIncome'> 0 </div></span>,<span style='float:left;'>Expense : <div style='float:right;' id='itmRptExpense'> 0 </div></span>,#cspan");
                ItemReportsGrid.attachFooter("<span style='float:left;margin-left:-7px;'><b>Internal Transfer Received:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptInternalTransferReceived'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVIntlTransRcd'>0</span></div></span>,#cspan,<span style='float:left;margin-left:-7px;'><b>Internal Transfer Paid:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptInternalTransferPaid'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVIntlTransPaid'>0</span></div></span>,<span style='float:left;margin-left:-7px;'><b>Business Received:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptBusinessReceived'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVBusRcvd'>0</span></div><span>,<span style='float:left;margin-left:-7px;'><b>Business Returned:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptBusinessReturn'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVBusRet'>0</span></div></span>,<span style='float:left;margin-left:-7px;'><b>Income:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptIncome'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVIncome'>0</span></div></span>,<span style='float:left;margin-left:-7px;'><b>Expense:-</b><br><div style='font-size: 11px;'>Verified : <span id='itmRptExpense'>0</span></div><div style='font-size: 11px;'>Not : <span id='itmRptUVExpense'>0</span></div></span>,#cspan"); //09-12-2024
                ItemReportsGrid.init();
//                if(ItemReportsGrid.getRowsNum() == 0)
                ItemReportsGrid.makeFilter("amtItRpt", 5);
                //var itCombo = ItemReportsGrid.getFilterElement(2);
                //itCombo.setPlaceholder("Items");
                //itCombo.readonly(true);                
                itemType = new dhtmlXCombo("itmType");
                itemType.setPlaceholder("Item Type");
                itemType.enableFilteringMode("between");
                itemType.addOption([
                        ["All", "All"],
                        ["InternalTransferReceived", "Internal Transfer Received"],
                        ["InternalTransferPaid", "Internal Transfer Paid"],
                        ["BusinessReceived", "Business Received"],
                        ["BusinessReturned", "Business Returned"],
                        ["Income", "Income"],
                        ["Expense", "Expense"]
                    ]);      
                itemType.setOptionWidth(250);
                itemType.attachEvent("onChange", function () {
                    //if(reportDetailsGrid) reportDetailsGrid.selectRowById(itemRptCombo.getSelectedValue()); //reportDetailsGrid.clearSelection();
                    var typeComboVal = itemType.getSelectedValue();
                    if (!itemType.getSelectedValue() && itemType.getComboText())
                        typeComboVal = itemType.getSelectedValue();
                    toeComboVal=itemType.getSelectedValue();
                    $("#itmType").val(typeComboVal);
                    //ItemReportsGrid.filterBy(1, itemType.getComboText());
                    //ItemReportsGrid.getFilterElement(1).setComboValue(itemType.getComboText());
                    preTally.MasterReports.applyReportItemFilter();
                    /*
                    hide below @ 26-12-2024 change the combo items
                    rptFilterParams = '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till") + '&m=' + monthData+'&typeOfEntry='+toeComboVal;
                    $.post(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&type=filt"+rptFilterParams), function (data) {
                            itemRptCombo.load(data);  
                            console.log("2----fetch item list for combo... chnage the item type on the same page");
                        });
                    */
                });  
                var shCombo = ItemReportsGrid.getFilterElement(3);
                shCombo.setPlaceholder("Sub head");
                shCombo.enableFilteringMode("between");
                //shCombo.readonly(true);

                var mhCombo = ItemReportsGrid.getFilterElement(4);
                mhCombo.setPlaceholder("Main head");
                //mhCombo.readonly(true);

                ItemReportsGrid.attachEvent("onRowSelect", function (rowId) {
                    branchItemRptFlag = 0;
                    rptBrnchItemId = rowId;
                    typeOfEntry=toeComboVal;
                    subHeadFilter=ItemReportsGrid.getFilterElement(3).getSelectedValue();
                    mainHeadFilter=ItemReportsGrid.getFilterElement(4).getSelectedValue();
                    if (rowId != 0) {
                        branchItemRptMode = 1;
                        preTally.MasterReports.viewBrnchItemReports(rowId, branchItemRptMode);
                    }
                });
                ItemReportsGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                ItemReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                preTally.MasterReports.applyReportItemFilter();
                ItemReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (ItemReportsGrid.getRowId(0) == "0") { //ItemReportsGrid.getRowsNum() == 0

                        ItemReportsGrid.deleteRow("0");
                        ItemReportsGrid.addRow("0", ['', 'Record Not Found', '', '', '', '', '', '','']);
                        ItemReportsGrid.setColspan("0", 1, 6);
                        ItemReportsGrid.setRowTextStyle("0", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        var rowID = 0;
                        var i;
                        for (i = 0; i < ItemReportsGrid.getRowsNum(); i++) {
                            rowID = ItemReportsGrid.getRowId(i);
                            ItemReportsGrid.cells(rowID, 0).setValue(i + 1);
                        }
                        ;

                    }
                });
                /* hide the combo at 26-12-2024 converted into text box
                itemRptCombo = new dhtmlXCombo("itmRF");
                rptFilterParams = '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till") + '&m=' + monthData;
                // old filter before 23-12-2024
                itemRptCombo.load(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&type=filt" + rptFilterParams), function () {
                    itemRptCombo.setPlaceholder('Item ');
                    itemRptCombo.enableFilteringMode('between');
                    console.log("1---combo loading..... 1 st time tab click time");
                });*/
                // old end
                // Client side custom filter based on ceo IDEA 23-12-2024
                /*itemRptCombo.load(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&type=filt" + rptFilterParams), function () {
                    itemRptCombo.setPlaceholder('Item ');
                    var indpgc = 0;
                    var pgcmsk = '';
                    var submask = [];
                    itemRptCombo.setFilterHandler(function(mask, option){   
                        indpgc = (pgcmsk != mask)? 0 : indpgc;
                        pgcmsk = mask;
                        var r = false;
                        if (mask.length == 0) {// search field is empty/blank
                             r = true; 
                        } else {
                            submask = mask.split(",");
                            if (submask.length > 1) {
                                for (let smark of submask) {
                                //$.each(submask, function (key, smark) {     
                                if($.trim(smark) != '') { 

                                    if (option.text.match(new RegExp(""+smark,"i")) != null) {
                                        r = true;
                                    }
                                    //if((option.text).toLowerCase().indexOf(smark) !== -1) {
                                        //r = true;
                                    //} 
                                    else {
                                        r = false;
                                        return false;
                                    }                               
                                }
                                }//);
                                
                            }else {
                                // search any words 
                                if (option.text.match(new RegExp("^"+mask,"i")) != null) {

                                    if(indpgc > 0) {
                                        for(var sip= 0; sip < indpgc; sip++) {
                                            if((itemRptCombo.getOptionByIndex(sip).text).length > (option.text).length) {
                                                itemRptCombo.setOptionIndex(option.value, sip);
                                                break;
                                            }
                                        }
                                        if(sip == indpgc) {
                                            itemRptCombo.setOptionIndex(option.value, sip);
                                        }
                                    }else {
                                        itemRptCombo.setOptionIndex(option.value, 0);
                                    }
                                    indpgc++;
                                    r = true;
                                } else if (option.text.match(new RegExp(""+mask,"i")) != null) {
                                 r = true; 
                                }
                            }
                        }
                        
                        return r;
                    });
                }); */
                // END CUSTOm FILTER CEO SUGGESTED 23-12-2024
                // Below Added BY Bilin @ 26-12-2024 
                var filtrInterval1;
                $("#itmRF").keyup(function () {
                    if (filtrInterval1)
                        clearInterval(filtrInterval1);

                    filtrInterval1 = setInterval(function () {
                        preTally.MasterReports.applyReportItemFilter();
                        clearInterval(filtrInterval1);
                    }, 500);
                });
                /*
                hide this @ 26-12-2024 because combo to text box converted
                itemRptCombo.setOptionWidth(250);
                itemRptCombo.attachEvent("onChange", function () { 
                    console.log("3---On change loading..... change time load the datas");                   
                    itmComboVal = itemRptCombo.getSelectedValue();
                    if (!itemRptCombo.getSelectedValue() && itemRptCombo.getComboText())
                        itmComboVal = itemRptCombo.getSelectedValue();
                    $("#itmRF").val(itmComboVal);
                    preTally.MasterReports.applyReportItemFilter();
                    //ItemReportsGrid.filterBy(7, itemRptCombo.getComboText());
                    //ItemReportsGrid.getFilterElement(7).setComboValue(itemRptCombo.getComboText());
                });*/
            }
        }, viewBrnchReports: function () {
            branchItemRptFlag = 0;
            rptItemId='';
            typeOfEntry=toeComboVal;
            subHeadFilter=ItemReportsGrid.getFilterElement(3).getSelectedValue();
            mainHeadFilter=ItemReportsGrid.getFilterElement(4).getSelectedValue();
            branchItemRptMode = 2;
            preTally.MasterReports.viewBrnchItemReports('', branchItemRptMode);
        },
        applyReportItemFilter: function () {
            ItemReportsGrid.clearAll();
            rptFilterParams = '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till")+"&ItmFilter="+itmComboVal+'&typeOfEntry='+toeComboVal;
            //26-12-2024 start
            var serchPNames = $('#itmRF').val();
            serchPNames     = serchPNames.replaceAll(', ', "-$Plus$-"); 
            serchPNames     = serchPNames.replaceAll(',', "-$Plus$-");
            serchPNames     = serchPNames.replaceAll(' ', "-$Plus$-");
            rptFilterParams += '&item_text_filtr='+serchPNames;
            // End 26-12-2024
            ItemReportsGrid.enableRowsHover(true, "bonusReportHover");
            ItemReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportItemData.php" + rptFilterParams), function () {
                ItemReportsGrid.enableTooltips("false,false,false,false,false,false,false,true");
//                preTally.Settings.progressOff(true, dhxLayout, null); 
                $("#itmRptExpense").html(ItemReportsGrid.getUserData("", "Expense"));
                $("#itmRptIncome").html(ItemReportsGrid.getUserData("", "Income"));
                $("#itmRptBusinessReturn").html(ItemReportsGrid.getUserData("", "BusinessReturned"));
                $("#itmRptBusinessReceived").html(ItemReportsGrid.getUserData("", "BusinessReceived"));
                $("#itmRptInternalTransferPaid").html(ItemReportsGrid.getUserData("", "InternalTransferPaid"));
                $("#itmRptInternalTransferReceived").html(ItemReportsGrid.getUserData("", "InternalTransferReceived"));
                //09-12-2024
                $("#itmRptUVExpense").html(ItemReportsGrid.getUserData("", "UvExpense"));
                $("#itmRptUVIncome").html(ItemReportsGrid.getUserData("", "UvIncome"));
                $("#itmRptUVBusRet").html(ItemReportsGrid.getUserData("", "UvBusinessReturned"));
                $("#itmRptUVBusRcvd").html(ItemReportsGrid.getUserData("", "UvBusinessReceived"));
                $("#itmRptUVIntlTransPaid").html(ItemReportsGrid.getUserData("", "UvInternalTransferPaid"));
                $("#itmRptUVIntlTransRcd").html(ItemReportsGrid.getUserData("", "UvInternalTransferReceived"));
                // end 09-12-2024
                var srtFlg = 0;
                $('.btn_Sort').click(function () {
                    var colId = $(this).attr("colNum")
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        ItemReportsGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        ItemReportsGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                });
            });
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptMRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptMRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        calculateFooterValues: function () {
            var mrOB = document.getElementById("mr_OB");
            mrOB.innerHTML = (preTally.MasterReports.sumColumn(2) + preTally.MasterReports.sumColumn(3)).toFixed(0);

            var mrInc = document.getElementById("mr_INC");
            mrInc.innerHTML = (preTally.MasterReports.sumColumn(5) + preTally.MasterReports.sumColumn(6)).toFixed(0);

            var mrExp = document.getElementById("mr_EXP");
            mrExp.innerHTML = (preTally.MasterReports.sumColumn(7) + preTally.MasterReports.sumColumn(8)).toFixed(0);

            var mrCB = document.getElementById("mr_CB");
            mrCB.innerHTML = (preTally.MasterReports.sumColumn(9) + preTally.MasterReports.sumColumn(10)).toFixed(0);

            var mrTR = document.getElementById("mr_TR");
            mrTR.innerHTML = (preTally.MasterReports.sumColumn(13) + preTally.MasterReports.sumColumn(14)).toFixed(0);

            var mrTP = document.getElementById("mr_TP");
            mrTP.innerHTML = (preTally.MasterReports.sumColumn(15) + preTally.MasterReports.sumColumn(16)).toFixed(0);

            var mrOBS = document.getElementById("mr_OBS");
            mrOBS.innerHTML = "Opening Balance :  " + (parseFloat($("#mr_OB").text()) + preTally.MasterReports.sumColumn(4)).toFixed(0);

            var mrCBS = document.getElementById("mr_CBS");
            mrCBS.innerHTML = "Closing Balance :  " + (parseFloat($("#mr_CB").text()) + preTally.MasterReports.sumColumn(11)).toFixed(0);

            var mrIncS = document.getElementById("mr_INCS");
            mrIncS.innerHTML = $("#mr_INC").text();

            var mrExpS = document.getElementById("mr_EXPS");
            mrExpS.innerHTML = $("#mr_EXP").text();

            var mrTRS = document.getElementById("mr_TRS");
            mrTRS.innerHTML = $("#mr_TR").text();

            var mrTPS = document.getElementById("mr_TPS");
            mrTPS.innerHTML = $("#mr_TP").text();

        },
        sumColumn: function (ind) {
            var out = 0;
            for (var i = 0; i < BranchReportsGrid.getRowsNum(); i++) {
                out += parseFloat(BranchReportsGrid.cells2(i, ind).getValue());
            }
            return out;
        },
        viewBrnchItemReports: function (Item_id, mode = 1) {
            if (!(ptMasterReportToolbar.isVisible("excel_export"))) {
                ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
            }
              
            ptMasterReportToolbar.setValue("rpt_date_from", prevFirstDay);
            ptMasterReportToolbar.setValue("rpt_date_till", prevLastDay);


            if (branchItemRptFlag != 1) {
                branchItemRptFlag = 1;
                if (ptMasterReportToolbar.getListOptionSelected("rpt_month_filter") && ptMasterReportToolbar.getItemText('rpt_month_filter') != 'Select Month') {
                    monthData = ptMasterReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                }
                var combofiltrInterval;
                ptMasterReportsTabbar.tabs("viewBrnchItemReports").setActive();
                dhxBrnchItemReportsLayout = ptMasterReportsTabbar.cells("viewBrnchItemReports").attachLayout("1C");
                dhxBrnchItemReportsLayout.cells("a").hideHeader();
                dhxBrnchItemReportsLayout.cells("a").setWidth('200');
                BrnchItemReportsGrid = dhxBrnchItemReportsLayout.cells("a").attachGrid();
                BrnchItemReportsGrid.setImagePath("../../codebase/imgs/");
                BrnchItemReportsGrid.setSkin("dhx_skyblue");
                if (ClickInit == 0) {
                    $(document).on('click', '.MastRBfooter', function () {
                        mode= $(this).attr('mode');
                        ItemBasedRptFlag = 0;
                        ClickInit = 1;
                        var itmId = '';                        
                        if (mode == 1)
                        itmId = itemFilterCombo.getSelectedValue();
                        else
                        itmId = "";    
                        rptItem_BranchId='';                     
                        preTally.MasterReports.viewItemBasedReports(itmId, '0', $(this).attr('month'), $(this).attr('year'),mode);
                    });
                }
                if (mode == 1) {
                    dhxBrnchRptTlbr = dhxBrnchItemReportsLayout.cells("a").attachToolbar();
                    dhxBrnchRptTlbr.addText('masterRptToolbar', '0', 'Type Of Entry');
                    dhxBrnchRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:250px;" id="typeOfEntry"></div>');
                    dhxBrnchRptTlbr.addText('masterRptToolbar', '2', 'Item');
                    dhxBrnchRptTlbr.addText('masterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="itemName"></div>');
                    dhxBrnchRptTlbr.setIconSize(32);

                    typeOfEntryCombo = new dhtmlXCombo("typeOfEntry");
                    typeOfEntryCombo.addOption([
                        ["All", "All"],
                        ["InternalTransferReceived", "Internal Transfer Received"],
                        ["InternalTransferPaid", "Internal Transfer Paid"],
                        ["BusinessReceived", "Business Received"],
                        ["BusinessReturned", "Business Returned"],
                        ["Income", "Income"],
                        ["Expense", "Expense"]
                    ]);

                    typeOfEntryCombo.readonly(true);
                    if(typeOfEntry)
                        typeOfEntryCombo.setComboValue(typeOfEntry.replace(/\s/g, ''));                    
                    else{
                        typeOfEntryCombo.setComboValue("All");
                    typeOfEntryCombo.setComboText("All");
                    }    
                    
                    itemFilterCombo = new dhtmlXCombo("itemName");
                    $.post(preTally.Initialize.encryptURL("requisites/items.php&mask=master&itemId=" + Item_id+"&typeOfEntry=" + typeOfEntry.replace(/\s/g, '')), function (data) {
                        itemFilterCombo.load(data);
                        if (typeof Item_id == 'undefined' || Item_id == 'null') {
                            itemFilterCombo.setComboValue(itemFilterCombo.getSelectedValue());
                            preTally.MasterReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                        } else {
                            itemFilterCombo.setComboValue(Item_id);

                        }
                        rptBrnchItemId = itemFilterCombo.getSelectedValue();
                        //var ITId = !isNaN(Item_id) ? Item_id : itemFilterCombo.getSelectedValue();
                        //preTally.MasterReports.applyReportBrnchItemFilter(ITId);
                    });
                    typeOfEntryCombo.attachEvent("onChange", function (value, text) {
                        typeOfEntry=typeOfEntryCombo.getSelectedValue();
                        typeOfEntryComboValue = typeOfEntryCombo.getSelectedValue();
                        $.post(preTally.Initialize.encryptURL("requisites/items.php&mask=master&typeOfEntry=" + typeOfEntryComboValue), function (data) {
                            itemFilterCombo.load(data);
                            preTally.MasterReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                        });

                    });
                    itemFilterCombo.setOptionWidth(280);

                    itemFilterCombo.attachEvent("onKeyPressed", function (keyCode) {
                        if (combofiltrInterval)
                            clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval(function () {
                            itemFilterCombo.load(preTally.Initialize.encryptURL("requisites/items.php&mask=master&IT_Name=" + itemFilterCombo.getComboText() + "&typeOfEntry=" + typeOfEntryCombo.getSelectedValue()), function () {
                                itemFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176);
                                if (itemFilterCombo.getOptionsCount() == 0) {
                                    itemFilterCombo.closeAll();
                                }
                            });
                            clearInterval(combofiltrInterval);
                        }, 500);
                    });

                    itemFilterCombo.attachEvent("onChange", function () {
                        rptBrnchItemId = itemFilterCombo.getSelectedValue();
                        preTally.MasterReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                    });
                } else {                    
                    Item_id = '';
                    //console.log("mode 1 time combo value fetch - not find in the use so hide 23-12-2024");
                    /* hide at 26-12-2024
                    if(itemRptCombo.getSelectedValue()){
                        Item_id =itemRptCombo.getSelectedValue();
                    }*/
                    rptBrnchItemId = Item_id;
                    var ItemGridFilters = '&type_filtr=' + itemType.getSelectedValue() + '&item_filtr=' + Item_id + '&subhead_filter=' + subHeadFilter + '&mainhead_filter=' + mainHeadFilter ;
                    //26-12-2024 start
                    var serchPNames = $('#itmRF').val();
                    serchPNames     = serchPNames.replaceAll(', ', "-$Plus$-"); 
                    serchPNames     = serchPNames.replaceAll(',', "-$Plus$-");
                    serchPNames     = serchPNames.replaceAll(' ', "-$Plus$-");
                    ItemGridFilters += '&item_text_filtr='+serchPNames;
                    // End 26-12-2024
                    preTally.MasterReports.applyReportBrnchItemFilter(Item_id, ItemGridFilters, mode);
                }
                BrnchItemReportsGrid.enableColSpan(true);


                BrnchItemReportsGrid.attachEvent("onXLE", function () {
//                    var brnchFilter = BrnchItemReportsGrid.getFilterElement(1);
//                    brnchFilter.setPlaceholder("Branch");
//                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                BrnchItemReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                BrnchItemReportsGrid.init();
                BrnchItemReportsGrid.attachEvent("onRowSelect", function (rowId) {
                    var cnId = BrnchItemReportsGrid.getSelectedCellIndex();
                    var selectedMonth = BrnchItemReportsGrid.cells(rowId, cnId).getAttribute("month");
                    var selectedYear = BrnchItemReportsGrid.cells(rowId, cnId).getAttribute("year");
//                    alert(selectedMonth+"------------------------------"+rowId);
//                    if(rowId != 0 && selectedMonth == undefined) {
//                        ItemBasedRptFlag = 0 ;
//                        rptItem_BranchId = rowId;
//                        rptItemId  = BrnchItemReportsGrid.getUserData(rowId,"IT_Id");
//                        preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
//                    }else 

                    if (selectedMonth) {
                        ItemBasedRptFlag = 0;
                        rptItem_BranchId = rowId;
                        if (mode == 1)
                            rptItemId = BrnchItemReportsGrid.getUserData(rowId, "IT_Id");
                        else
                            rptItemId = BrnchItemReportsGrid.getUserData("", "IT_IdMain");
                        prevFirstDay = ptMasterReportToolbar.getValue("rpt_date_from");
                        prevLastDay = ptMasterReportToolbar.getValue("rpt_date_till");
                        //ptMasterReportToolbar.hideItem("excel_export"); // 16-12-2025
                        preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId, selectedMonth, selectedYear, branchItemRptMode);
                    }

                    return false;
                });

                BrnchItemReportsGrid.attachEvent("onFilterEnd", function () {
                    if (BrnchItemReportsGrid.getRowId(0) != "0") {
                        var rowID = 0;
                        var i;
                        for (i = 0; i < BrnchItemReportsGrid.getRowsNum(); i++) {
                            rowID = BrnchItemReportsGrid.getRowId(i);
                            BrnchItemReportsGrid.cells(rowID, 0).setValue(i + 1);
                        }
                        ;
                    }
                });

        }
        },
        applyReportBrnchItemFilter: function (Item_id, ItemGridFilters, mode = 1) {
           preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = "&filter=" + Item_id;
            BrnchItemReportsGrid.clearAll();
            BrnchItemReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false");
            BrnchItemReportsGrid.enableRowsHover(true, "bonusReportHover");
            rptFilterParams = '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till") + '&m=' + monthData;
            BrnchItemReportsGrid.clearAll();
            BrnchItemReportsGrid.post(preTally.Initialize.encryptURL("requisites/reportBrnchItemData.php" + rptFilterParams), "&mode=" + mode + "&filter=" + Item_id + ItemGridFilters, function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
//                brnchcombo = BrnchItemReportsGrid.getFilterElement(1);
//                brnchcombo.setOptionWidth(150);
                /*
                 BrnchItemReportsGrid.attachEvent("onRowSelect",function(rowId){
                 if(rowId != 0) {
                 ItemBasedRptFlag = 0 ;
                 rptItem_BranchId = rowId;
                 rptItemId  = BrnchItemReportsGrid.getUserData(rowId,"IT_Id");
                 preTally.MasterReports.viewItemBasedReports(rptItemId, rptItem_BranchId);
                 }   else return false;
                 });
                 */
                BrnchItemReportsGrid.attachEvent("onMouseOver", function (id, ind) {
//                    
                });

                BrnchItemReportsGrid.makeFilter("LC_MR_Name", 1);
                LC_MR_NameCombo = dhtmlXComboFromSelect("LC_MR_Name");
                LC_MR_NameCombo.setOptionWidth(200);
                LC_MR_NameCombo.setSize(100);
                LC_MR_NameCombo.enableFilteringMode('between');
                LC_MR_NameCombo.attachEvent("onChange", function () {
                    var locCMBVal = LC_MR_NameCombo.getComboText();
                    if (locCMBVal == "All")
                        locCMBVal = '';
                    BrnchItemReportsGrid.filterBy(1, locCMBVal);
                });

                var srtFlg = 0;
                $('.btn_Sort_BIR').click(function () {
                    var colId = $(this).attr("colNum");
                    var sortType = colId != 1 ? 'int' : 'str';
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BrnchItemReportsGrid.sortRows(colId, sortType, "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BrnchItemReportsGrid.sortRows(colId, sortType, "desc");
                        srtFlg = 0;
                    }
                });

            });
        },
        viewItemBasedReports: function (ITId, LCId, Mnt, Yr, mode = 1) {                
            if (!(ptMasterReportToolbar.isVisible("excel_export"))) {
                ptMasterReportToolbar.showItem("excel_export"); // 15-12-2025
            }       
            ITFilter = ITId;
            LCFilter = LCId;
            appOrd = '';
            DtOrd='';
            modeFilter = mode;            
            if (!Mnt && !Yr && selectedFirstDay && selectedLastDay) {
                ptMasterReportToolbar.setValue("rpt_date_from", selectedFirstDay);
                ptMasterReportToolbar.setValue("rpt_date_till", selectedLastDay);
            }

            if (ItemBasedRptFlag != 1) {
                ItemBasedRptFlag = 1;

                if (Mnt && Yr) {
                    var date = new Date();
                    selectedFirstDay = new Date(parseInt(Yr), parseInt(Mnt) - 1, 1).toString("dd.MM.yyyy");
                    selectedLastDay = new Date(parseInt(Yr), parseInt(Mnt), 0).toString("dd.MM.yyyy");
                    ptMasterReportToolbar.setValue("rpt_date_from", selectedFirstDay);
                    ptMasterReportToolbar.setValue("rpt_date_till", selectedLastDay);

                    ptMasterReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                    ptMasterReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    ptMasterReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                }

                if (!ptMasterReportsTabbar.cells("viewItemBasedReports")) {
                    ptMasterReportsTabbar.addTab("viewItemBasedReports", "Item - Detailed Reports", null, null, null, true);
                    ptMasterReportsTabbar.tabs("viewItemBasedReports").setActive();

                    dhxItemBasedReportsLayout = ptMasterReportsTabbar.cells("viewItemBasedReports").attachLayout("1C");
                    dhxItemBasedReportsLayout.cells("a").hideHeader();
                    ItemBasedReportsGrid = dhxItemBasedReportsLayout.cells("a").attachGrid();
                    ItemBasedReportsGrid.setImagePath("../../codebase/imgs/");
                    ItemBasedReportsGrid.setSkin("dhx_skyblue");
                    ItemBasedReportsGrid.setHeader("SlNo,<div id='IMR_ItmFilter' style='width:90%'></div>,<input type='text' id='IMR_TxtDesc' class='IMR_TxtFilter' style='width:90%'/>,<input type='text' id='IMR_TxtTrack' class='IMR_TxtFilter' style='width:90%'/>, \
                        <div style='text-align:left;'><select id='IMR_CboAmt' class='IMR_CboFilter'><option value='' selected>All</option><option value='1'>Verified</option><option value='2'>Un-Verified</option></select> <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_AmtIR'/></div>,<div style='text-align:left;'>Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort_DateIR'/></div>,<div id='IMR_AddedbyFilter' style='width:90%'></div>");
                    ItemBasedReportsGrid.setNumberFormat("0,000.00", 4);
                    IBRStatbar = dhxItemBasedReportsLayout.cells("a").attachStatusBar({
                        text: "<div class='IBRStatbar'>\
                                <div style='float:left;font-weight:bold;' class='IBRStatbar_cnt_tot'># : 0</div>\
                                <div style='float:right;' id='IBRStatbar_paging'></div>\
                            </div>",
                        height: 30
                    });
                    ItemBasedReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50 ,80]);
                    ItemBasedReportsGrid.enablePaging(true, 50, 5, "IBRStatbar_paging", true);
                    ItemBasedReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                    ItemBasedReportsGrid.setImagePath('assets/grid/codebase/imgs/');
                    ItemBasedReportsGrid.setInitWidths("60,*,*,80,100,80,120")
                    ItemBasedReportsGrid.setColAlign("left,left,left,left,right,left,left")
                    ItemBasedReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                    ItemBasedReportsGrid.setColSorting("na,na,na,na,na,na,na,na");
                    ItemBasedReportsGrid.enableEditEvents(true, true, true);
                    ItemBasedReportsGrid.enableTooltips("false,false,false,false,false,false");
                    ItemBasedReportsGrid.attachEvent("onXLE", function () {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    ItemBasedReportsGrid.attachEvent("onXLS", function () {
                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    ItemBasedReportsGrid.init();
                    ItemBasedReportsGrid.attachFooter('Total,#cspan,#cspan,#cspan,<div id="IMR_Tot"></div>,<div id="IMRuv_Tot"></div>,#cspan');
                    itmFiltrCombo = new dhtmlXCombo("IMR_ItmFilter");
                    itmFiltrCombo.enableFilteringMode("between");
                    addByCombo = new dhtmlXCombo("IMR_AddedbyFilter");
                    addByCombo.enableFilteringMode("between");
                    var filtrInterval;
                    $('.IMR_TxtFilter').on("keyup", function () {
                        clearInterval(filtrInterval);
                        filtrInterval = setInterval(function () { 
                            preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                            clearInterval(filtrInterval);
                        }, 800);
                    });
                    
                    $('.IMR_CboFilter').on("change", function () { //16-12-2025
                        preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                    });
                    var appLvsrtFlg = 1
                    $(".btn_Sort_AmtIR").click(function () { 
                        DtOrd='';
                        if (appLvsrtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            appOrd = 'asc';
                            appLvsrtFlg = 1;

                        } else {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            appOrd = 'des';
                            appLvsrtFlg = 0;
                        }
                        
                        preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                    });
                    
                     var IRDateSort = 1
                    $(".btn_Sort_DateIR").click(function () {
                        appOrd='';
                        if (IRDateSort == 0) {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            DtOrd = 'asc';
                            IRDateSort = 1;

                        } else {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            DtOrd = 'des';
                            IRDateSort = 0;
                        }
                       
                        preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                    });
                    itmFiltrCombo.attachEvent("onChange", function () {
                        ITFilter = itmFiltrCombo.getSelectedValue();
                        
                        preTally.MasterReports.applyItemBasedRptFilter(itmFiltrCombo.getSelectedValue(), LCFilter, modeFilter);
                    });
                    addByCombo.attachEvent("onChange", function () {
                        
                        preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                    });
                    
                    preTally.MasterReports.applyItemBasedRptFilter(ITId, LCId, mode);
                    itmFiltrCombo.clearAll();
                    itmFiltrCombo.load(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&type=filt&ITId=" + ITId + "&typeOfEntry=" + typeOfEntry.replace(/\s/g, '') + '&LCId=' + LCId + '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till")), function () {
                        if (typeof ITId == 'undefined' || ITId == 'null' || ITId == "")
                            itmFiltrCombo.selectOption('0');
                        else
                            itmFiltrCombo.setComboValue(ITId);
                        ITId = itmFiltrCombo.getSelectedValue();
                    });
                    addByCombo.clearAll();
                    addByCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + typeOfEntry.replace(/\s/g, '') + '&LCId=' + LCId + '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till")), function () {
                    });
                } else {
                    itmFiltrCombo.clearAll();
                    itmFiltrCombo.load(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&type=filt&ITId=" + ITId + "&typeOfEntry=" + typeOfEntry.replace(/\s/g, '') + '&LCId=' + LCId + '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till")), function () {
                        if (typeof ITId == 'undefined' || ITId == 'null' || ITId == "")
                            itmFiltrCombo.selectOption('0');
                        else
                            itmFiltrCombo.setComboValue(ITId);
                        ITId = itmFiltrCombo.getSelectedValue();
                    });
                    addByCombo.clearAll();
                    addByCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + typeOfEntry.replace(/\s/g, '') + '&LCId=' + LCId + '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till")), function () {
                    });
                    
                     preTally.MasterReports.applyItemBasedRptFilter(ITFilter, LCFilter, modeFilter);
                    ptMasterReportsTabbar.tabs("viewItemBasedReports").setActive();
                }
        }
        },
        applyItemBasedRptFilter: function (ITId, LCId, mode) {
            var ItemGridFilters = '&mode=' + mode + '&type_filtr=' + typeOfEntry+ '&item_filtr=' + ITId + '&subhead_filter=' + subHeadFilter + '&mainhead_filter=' + mainHeadFilter + '&amtSort=' + appOrd +'&dtSort='+DtOrd;
            rptFilterParams = '&f=' + ptMasterReportToolbar.getValue("rpt_date_from") + '&t=' + ptMasterReportToolbar.getValue("rpt_date_till") + '&ITId=' + ITId + '&LCId=' + LCId + '&DescFilter=' + $('#IMR_TxtDesc').val() + '&TrkFilter=' + $('#IMR_TxtTrack').val() + '&USId=' + addByCombo.getSelectedValue()+ '&TrkAmtType=' + $('#IMR_CboAmt').val();
            ItemBasedReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportItemRptData.php" + rptFilterParams + ItemGridFilters), function () {
                $('#IMR_Tot').html(ItemBasedReportsGrid.getUserData("", "TL_SUM"));
                if (ItemBasedReportsGrid.getUserData("", "TL_SUMUV") > 0) {
                    $('#IMRuv_Tot').html(ItemBasedReportsGrid.getUserData("", "TL_SUMUV")+" (Unverified)");
                } else {
                    $('#IMRuv_Tot').html('');
                }
                $('.IBRStatbar_cnt_tot').html("#:" + ItemBasedReportsGrid.getUserData("", "TL_Count"));               
            });
        }
    };
})(jQuery, this);