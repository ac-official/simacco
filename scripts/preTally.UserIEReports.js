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
    var itemMRRptFlag = 0;
    var visualRptFlag = 0;
    var entryTypeCombo;
    var branchMRItemRptFlag = 0;
    var ItemBasedMRRptFlag = 0;
    var UserBasedMRRptFlag = 0;
    var BranchMReportsGrid;
    var UserBasedMReportsGrid;
    var rptItemId;
    var rptItem_UserId = '';
    var rptItem_BranchId = '';
    var selectedMonth;
    var selectedYear;
    var ItemGridFilters = "";
    var Filter_Item = "";
    var Filter_Loc = "";
    var Filter_User = "";
    var Filter_EntryType = "All";
    var Filter_Subhead = "";
    var Filter_Mainhead = "";
    var brnchRptMode = "";
    var brnchRptItem = "";
    var BMR_ZNId="";
    var mode = 1;
    var amtSort = 'asc';
    var ubmr_itmFilter;
    var ubmr_brFilter;
    var ubmr_usFilter;
    var itmFiltrCombo;
    var addByCombo;
    var paidForBrnch;
    var paidForUser;
    var BMR_branchId;
    preTally.UserIEReports = {
        viewIEReports: function (RPTNAME) {
            branchItemRptMode = 1;
            if (!dhxMiddleBlockTabs.cells("viewUserIEReports")) {
                reportInit = 1;
                dhxLayout.cells("b").collapse();
                dhxMiddleBlockTabs.addTab("viewUserIEReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;"+RPTNAME+"&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshTab'/>", 200);
                dhxMiddleBlockTabs.tabs("viewUserIEReports").setActive();
                visualRptFlag = 0;
                itemMRRptFlag = 0;
                branchMRItemRptFlag = 0;
                ItemBasedMRRptFlag = 0;
                UserBasedMRRptFlag = 0
                dhxUserIEReportsLayout = dhxMiddleBlockTabs.cells("viewUserIEReports").attachLayout("1C");

                ptUserIEReportsToolbar = dhxUserIEReportsLayout.cells("a").attachToolbar();
                ptUserIEReportsToolbar.setIconsPath("images/icon/default_18/");
               
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4)
                    var params = '&filter=BMR';
                else if (bsp == 2)
                    var params = '&mask=Self';
                var znacl = unescape(JGG1P3bDnUSDL22Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if(znacl==1){
                     ptUserIEReportsToolbar.setAlign('left');
                    ptUserIEReportsToolbar.addText('textuie_zone', '0', 'Zone');
                    ptUserIEReportsToolbar.addText('rptuie_zone', '1', '<div id="rptuie_zone_combo"></div>');
                    zoneUIEFilterCombo = new dhtmlXCombo("rptuie_zone_combo");

                    zoneUIEFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_userzone.php&type=UIE"), function () {
                        if (zoneUIEFilterCombo.getOptionsCount() != 0) {
                            zoneUIEFilterCombo.selectOption('0');
                            
                            zoneUIEFilterCombo.attachEvent("onChange", function (id) {
                    BMR_ZNId=id;
                    visualRptFlag = 0;
                    itemMRRptFlag = 0;
                    branchMRItemRptFlag = 0;
                    ItemBasedMRRptFlag = 0;
                    UserBasedMRRptFlag = 0
                    var actvId = ptUserIEReportsTabbar.getActiveTab();
                    if (actvId == 'viewMRReportsVisual')
                        preTally.UserIEReports.viewMRReportsVisual();
                    if (actvId == 'viewMRItemReports')
                        preTally.UserIEReports.viewMRItemReports();
                    if (actvId == 'viewMRBranchReports')
                        preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                    if (actvId == 'viewUserBasedMRReports')
                        preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId, mode)
                    if (actvId == 'viewItemBasedMReports')
                        preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId, mode);
                            });
                        }else{
                            zoneUIEFilterCombo.setComboText('-Self Branch-');
                        }
                    });
                }else{
                    ptUserIEReportsToolbar.setAlign('right');
                }

                
                
                $("#rptuie_zone_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                $("#rptuie_zone_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                $("#rptuie_zone_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                $("#rptuie_zone_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                $("#rptuie_zone_combo").css({width: "170"});
                

                $(".refreshTab").click(function () {
                    visualRptFlag = 0;
                    itemMRRptFlag = 0;
                    branchMRItemRptFlag = 0;
                    ItemBasedMRRptFlag = 0;
                    UserBasedMRRptFlag = 0
                    var actvId = ptUserIEReportsTabbar.getActiveTab();
                    if (actvId == 'viewMRReportsVisual')
                        preTally.UserIEReports.viewMRReportsVisual();
                    if (actvId == 'viewMRItemReports')
                        preTally.UserIEReports.viewMRItemReports();
                    if (actvId == 'viewMRBranchReports')
                        preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                    if (actvId == 'viewUserBasedMRReports')
                        preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId, mode)
                    if (actvId == 'viewItemBasedMReports')
                        preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId, mode);
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
                ptUserIEReportsToolbar.addSpacer("rptuie_zone");
                ptUserIEReportsToolbar.addButtonSelect("rpt_day_filter", '4', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptUserIEReportsToolbar.addButtonSelect("rpt_month_filter", '5', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                ptUserIEReportsToolbar.addButtonSelect("rpt_year_filter", '6', "Select Year", Years_Options, '', '', true, true, 10, 'select');
                

                ptUserIEReportsToolbar.addSeparator('sep2','7');

                ptUserIEReportsToolbar.addText("text_from", '8', "From");
                ptUserIEReportsToolbar.addInput("rpt_date_from",'9', "", 75);
                ptUserIEReportsToolbar.addButton("rpt_df_clear",'10', "", "close.gif");
                ptUserIEReportsToolbar.addSeparator('sep2','11');

                ptUserIEReportsToolbar.addText("text_till",'12', "Till");
                ptUserIEReportsToolbar.addInput("rpt_date_till",'13', "", 75);
                ptUserIEReportsToolbar.addButton("rpt_dt_clear",'14', "", "close.gif");
                ptUserIEReportsToolbar.addSeparator('sep3','15');
                ptUserIEReportsToolbar.addButton("rpt_date_filter",'16', "Search", "save.gif");


                var ptRpTb_Inp_Frm = ptUserIEReportsToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptUserIEReportsToolbar.getValue("rpt_date_till"))
                        preTally.UserIEReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptUserIEReportsToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptUserIEReportsToolbar.getValue("rpt_date_from"))
                        preTally.UserIEReports.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptUserIEReportsToolbar.attachEvent("onClick", function (id) {
                    visualRptFlag = 0;
                    itemMRRptFlag = 0;
                    branchMRItemRptFlag = 0;
                    ItemBasedMRRptFlag = 0;
                    UserBasedMRRptFlag = 0

                    var pId = ptUserIEReportsToolbar.getParentId(id);

                    if (id == 'rpt_df_clear') {
                        ptUserIEReportsToolbar.setValue('rpt_date_from', '', false);
                        prevFirstDay = '';
                    }

                    if (id == 'rpt_dt_clear') {
                        ptUserIEReportsToolbar.setValue('rpt_date_till', '', false);
                        prevLastDay = '';
                    }

                    if (pId == 'rpt_day_filter') {
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptUserIEReportsToolbar.setValue('rpt_date_from', dateToday, false);
                        ptUserIEReportsToolbar.setValue('rpt_date_till', dateToday, false);
                        ptUserIEReportsToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptUserIEReportsToolbar.setItemText('rpt_year_filter', 'Select Year');
                        monthData = '';
                        var actvId = ptUserIEReportsTabbar.getActiveTab();

                        prevFirstDay = dateToday;
                        prevLastDay = dateToday;
                        if (actvId == 'viewMRReportsVisual')
                            preTally.UserIEReports.viewMRReportsVisual();
                        if (actvId == 'viewMRItemReports')
                            preTally.UserIEReports.viewMRItemReports();
                        if (actvId == 'viewMRBranchReports') {
                            preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                        }
                        if (actvId == 'viewUserBasedMRReports'){
                            selectedFirstDay = dateToday;
                            selectedLastDay = dateToday;
                            preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId,mode)
                            }
                        if (actvId == 'viewItemBasedMReports'){
                            selectedFirstDay = dateToday;
                            selectedLastDay = dateToday;
                            preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId,mode);
                        }
                    }
                    if (pId == 'rpt_month_filter') {

                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptUserIEReportsToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        var currMonth = new Date(yeartoolbar, id - 1, 1).toString("MM");
                        var currYear = new Date(yeartoolbar, id - 1, 1).toString("yyyy");
                        ptUserIEReportsToolbar.setValue('rpt_date_from', firstDay, false);
                        ptUserIEReportsToolbar.setValue('rpt_date_till', lastDay, false);
                        ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptUserIEReportsToolbar.setItemText('rpt_year_filter', 'Select Year');

                        if (ptUserIEReportsToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData = ptUserIEReportsToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');

//                        prevFirstDay = firstDay;
//                        prevLastDay  = lastDay;

                        prevFirstDay = firstDay;
                        prevLastDay = lastDay;

                        var actvId = ptUserIEReportsTabbar.getActiveTab();
                        if (actvId == 'viewMRReportsVisual')
                            preTally.UserIEReports.viewMRReportsVisual();
                        if (actvId == 'viewMRItemReports')
                            preTally.UserIEReports.viewMRItemReports();
                        if (actvId == 'viewMRBranchReports') {
                            preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                        }
                        if (actvId == 'viewUserBasedMRReports'){
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId,mode)
                        }
                            
                        if (actvId == 'viewItemBasedMReports'){
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId,mode);
                        }
                            

                    }
                    if (pId == 'rpt_year_filter') {
                        var tmpDate = new Date();
                        if (parseInt(id) + 1 == tmpDate.getFullYear())
                            var m = tmpDate.getMonth();
                        else
                            var m = '02';

                        yearData = ptUserIEReportsToolbar.getListOptionSelected("rpt_year_filter");
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

                        ptUserIEReportsToolbar.setValue('rpt_date_from', firstDay, false);
                        ptUserIEReportsToolbar.setValue('rpt_date_till', lastDay, false);
                        ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptUserIEReportsToolbar.setItemText('rpt_month_filter', 'Select Month');
                        monthData = '';
                        var actvId = ptUserIEReportsTabbar.getActiveTab();
                        if (actvId == 'viewMRReportsVisual')
                            preTally.UserIEReports.viewMRReportsVisual();
                        if (actvId == 'viewMRItemReports')
                            preTally.UserIEReports.viewMRItemReports();
                        if (actvId == 'viewMRBranchReports')
                            preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                        if (actvId == 'viewUserBasedMRReports'){
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId,mode)
                        }
                        if (actvId == 'viewItemBasedMReports'){
                            selectedFirstDay = firstDay;
                            selectedLastDay = lastDay;
                            preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId,mode);
                        }

                    }

                    if (id == 'rpt_date_filter') {
                        monthData = '';

                        prevFirstDay = ptUserIEReportsToolbar.getValue('rpt_date_from');
                        prevLastDay = ptUserIEReportsToolbar.getValue('rpt_date_till');

                        ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptUserIEReportsToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptUserIEReportsToolbar.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = ptUserIEReportsTabbar.getActiveTab();
                        if (actvId == 'viewMRReportsVisual')
                            preTally.UserIEReports.viewMRReportsVisual();
                        if (actvId == 'viewMRItemReports')
                            preTally.UserIEReports.viewMRItemReports();
                        if (actvId == 'viewMRBranchReports') {
                            preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                        }
                        if (actvId == 'viewUserBasedMRReports'){
                             selectedFirstDay = ptUserIEReportsToolbar.getValue('rpt_date_from');;
                            selectedLastDay = ptUserIEReportsToolbar.getValue('rpt_date_till');
                            preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId, mode)
                        }
                        if (actvId == 'viewItemBasedMReports'){
                             selectedFirstDay = ptUserIEReportsToolbar.getValue('rpt_date_from');
                            selectedLastDay = ptUserIEReportsToolbar.getValue('rpt_date_till');
                            preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId, mode);
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
                    ptUserIEReportsToolbar.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                    ptUserIEReportsToolbar.setValue('rpt_date_till', tDate);
                    ptUserIEReportsToolbar.setItemText('rpt_month_filter', m_names[monthData]);

                    reportInit = 0;
                    monthData = monthData + 1; // get current month number;starting with 1

                    prevFirstDay = "01." + Date.today().toString("MM.yyyy");
                    prevLastDay = tDate;

                } else {
                    cDate = Date.today().toString("dd.MM.yyyy");
                    ptUserIEReportsToolbar.setValue('rpt_date_till', cDate);
                    ptUserIEReportsToolbar.setValue('rpt_date_from', cDate);
                    ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Today');
                    reportInit = 0;

                    prevFirstDay = prevLastDay = cDate;

                }

//                ptUserIEReportsToolbar.setAlign('right');

                ptUserIEReportsTabbar = dhxUserIEReportsLayout.cells("a").attachTabbar();
//                ptUserIEReportsToolbar.addTab("viewReportsVisual", tb_data_txt, "465px");                
                ptUserIEReportsTabbar.addTab("viewMRReportsVisual", "Overall Reports");
                ptUserIEReportsTabbar.addTab("viewMRItemReports", "Item Based Reports");
                ptUserIEReportsTabbar.addTab("viewMRBranchReports", "Branch Based Reports");

                preTally.UserIEReports.viewMRReportsVisual();
                ptUserIEReportsTabbar.tabs("viewMRReportsVisual").setActive();

                ptUserIEReportsTabbar.attachEvent("onTabClick", function (id, last_id) {
                    if (id == 'viewMRReportsVisual')
                        preTally.UserIEReports.viewMRReportsVisual();
                    if (id == 'viewMRItemReports')
                        preTally.UserIEReports.viewMRItemReports();
                    if (id == 'viewMRBranchReports')
                        preTally.UserIEReports.viewMRBranchReports(brnchRptItem, brnchRptMode);
                    if (id == 'viewItemBasedMReports')
                        preTally.UserIEReports.viewItemBasedMReports(rptItemId, rptItem_UserId, rptItem_BranchId, mode);
                    if (id == 'viewUserBasedMRReports') {
                        preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId, mode)
                    }
                    return true;

                });


            } else {
                dhxMiddleBlockTabs.tabs("viewUserIEReports").setActive();
                return true;
            }
        }, viewMRReportsVisual: function () {

            ptUserIEReportsToolbar.setValue("rpt_date_from", prevFirstDay);
            ptUserIEReportsToolbar.setValue("rpt_date_till", prevLastDay);

            if (visualRptFlag != 1) {

                visualRptFlag = 1;
                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxMRReportsVisualLayout = ptUserIEReportsTabbar.cells("viewMRReportsVisual").attachLayout("1C");
                dhxMRReportsVisualLayout.cells("a").hideHeader();

                dhxMRReportsVisualGrid = dhxMRReportsVisualLayout.cells("a").attachGrid();
                dhxMRReportsVisualGrid.enableTooltips("false,false,false");                
                rptFilterParams = 'f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till")+'&znid='+BMR_ZNId;
                dhxMRReportsVisualGrid.enableRowsHover(true, "bonusReportHover");
                dhxMRReportsVisualGrid.load(preTally.Initialize.encryptURL("requisites/viewMRReportsVisual.php&" + rptFilterParams), function () {
                    //                $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    //                $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    var srtFlg = 0;
                    $('.mrr_Sort_Visual').click(function () {
                        var colId = $(this).attr("colNum");
                        var sortType = colId != 1 ? 'int' : 'str';
                        if (srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            dhxMRReportsVisualGrid.sortRows(colId, sortType, "asc");
                            srtFlg = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            dhxMRReportsVisualGrid.sortRows(colId, sortType, "desc");
                            srtFlg = 0;
                        }
                        preTally.UserIEReports.rearrangeSerials(dhxMRReportsVisualGrid);
                    });
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                dhxMRReportsVisualGrid.attachEvent("onRowSelect", function (rowId) {
                    itemMRRptFlag = 0;
                    Filter_EntryType = dhxMRReportsVisualGrid.cells(rowId, 1).getValue();
                    preTally.UserIEReports.viewMRItemReports(Filter_EntryType);
                    ptUserIEReportsTabbar.tabs("viewMRItemReports").setActive();

                });
            }

        }, viewMRItemReports: function (typeofEntry = 'All') {
            ptUserIEReportsToolbar.setValue("rpt_date_from", prevFirstDay);
            ptUserIEReportsToolbar.setValue("rpt_date_till", prevLastDay);
            if (itemMRRptFlag != 1) {
                Filter_EntryType = typeofEntry
                dhxMRItemReportsLayout = ptUserIEReportsTabbar.cells("viewMRItemReports").attachLayout("1C");
                dhxMRItemReportsLayout.cells("a").hideHeader();
                itemMRRptFlag = 1;

                ItemMRReportsGrid = dhxMRItemReportsLayout.cells("a").attachGrid();
                ItemMRReportsGrid.setImagePath("../../codebase/imgs/");
                ItemMRReportsGrid.setSkin("dhx_skyblue")
                ItemMRReportsGrid.setHeader("<div>SlNo</div>,<div id='entry_type_filter' width='90%'></div>,<div id='itmMR' style='width: 90%;' placeholder='Item'></div>,#combo_filter,#combo_filter,<div><input style='width:55%;display:none;' id='amtMRRpt' placeholder='Amount'> <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort'/></div>,,#combo_filter");                
                ItemMRReportsGrid.setInitWidths("50,100,*,0,0,120,60,0");
                ItemMRReportsGrid.setColAlign("right,left,left,left,left,right,center,center");
                ItemMRReportsGrid.setColTypes("ro,ro,ro,ro,ro,ron,ro,ro");
                ItemMRReportsGrid.enableColSpan(true);
                ItemMRReportsGrid.setNumberFormat("0,000.00", 5);
//                ItemMRReportsGrid.setColSorting("int,str,str,str,str");  
                ItemMRReportsGrid.enableEditEvents(false, false, false);
                ItemMRReportsGrid.enableTooltips("false,false,false,false");
                ItemMRReportsGrid.attachFooter("<div style='width:100%;cursor: pointer;'>Total</div>,#cspan,#cspan,#cspan,#cspan,<div style='width:100%;float:right;cursor: pointer;' title='Branchwise Report of Listed Items' onclick='preTally.UserIEReports.viewMRBrnchReports()'>{#stat_total}</div>,<span ></span>");
                //ItemMRReportsGrid.attachFooter("<span style='float:left;'> Internal Transfer Received : <div style='float:right;' id='itmMRptInternalTransferReceived'> 0 </div></span>,#cspan,<span style='float:left;'> Internal Transfer Paid : <div style='float:right;' id='itmMRptInternalTransferPaid'> 0 </div></span>,<span style='float:left;'>Business Received : <div style='float:right;' id='itmRptBusinessReceived'> 0 </div></span>,<span style='float:left;'> Business Returned : <div style='float:right;' id='itmMRptBusinessReturn'> 0 </div></span>,<span style='float:left;'> Income : <div style='float:right;' id='itmMRptIncome'> 0 </div></span>,<span style='float:left;'>Expense : <div style='float:right;' id='itmMRptExpense'> 0 </div></span>");
                ItemMRReportsGrid.attachFooter(",<span style='float:left;padding-left:20px;'> Income : <div style='float:right;' id='itmMRptIncome'> 0 </div></span><span style='float:left;padding-left:20px;'>Expense : <div style='float:right;' id='itmMRptExpense'> 0 </div></span><span style='float:left;padding-left:20px;'> Internal Transfer Received : <div style='float:right;' id='itmMRptInternalTransferReceived'> 0 </div></span><span style='float:left;padding-left:20px;'> Internal Transfer Paid : <div style='float:right;' id='itmMRptInternalTransferPaid'> 0 </div></span><span style='float:left;padding-left:20px;'>Business Received : <div style='float:right;' id='itmMRptBusinessReceived'> 0 </div></span><span style='float:left;padding-left:20px;'> Business Returned : <div style='float:right;' id='itmMRptBusinessReturn'> 0 </div></span>,#cspan,#cspan,#cspan,#cspan,#cspan");
                ItemMRReportsGrid.init();
                ItemMRReportsGrid.attachEvent("onRowSelect", function (rowId) { 
                    branchMRItemRptFlag = 0;
                    rptBrnchItemId = rowId;
                    if (rowId != 0) {
                        branchItemRptMode = 1;
                        Filter_EntryType=entryTypeCombo.getSelectedValue();
                        preTally.UserIEReports.viewMRBranchReports(rowId, branchItemRptMode);
                    }
                });
                ItemMRReportsGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                ItemMRReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });

                ItemMRReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (ItemMRReportsGrid.getRowsNum() == 0) { //ItemMRReportsGrid.getRowsNum() == 0

                        ItemMRReportsGrid.deleteRow("0");
                        ItemMRReportsGrid.addRow("0", ['', 'Record Not Found', '', '', '', '', '', '']);
                        ItemMRReportsGrid.setColspan("0", 1, 6);
                        ItemMRReportsGrid.setRowTextStyle("0", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        preTally.UserIEReports.rearrangeSerials(ItemMRReportsGrid);
                    }
                });                
                entryTypeCombo = new dhtmlXCombo("entry_type_filter");
                entryTypeCombo.addOption([
                    ["All", "All"],
                    ["Income", "Income"],
                    ["Expense", "Expense"],
                    ["Business Received", "Business Received"],
                    ["Business Returned", "Business Returned"],
                    ["Internal Transfer Received", "Internal Transfer Received"],
                    ["Internal Transfer Paid", "Internal Transfer Paid"]
                ]);
                entryTypeCombo.setOptionWidth(200);
                entryTypeCombo.attachEvent("onChange", function (id) {
                    Filter_EntryType = id;
                    if (id != 'All')
                        ItemMRReportsGrid.filterBy(1, entryTypeCombo.getComboText());
                    else
                        ItemMRReportsGrid.filterBy(1, '');
                    var entryTypeComboValue = entryTypeCombo.getSelectedValue();
                    $.post(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&typeOfEntry=" + entryTypeComboValue.replace(/\s/g, '') + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function (data) {
                        itemRptCombo.clearAll();
                        itemRptCombo.load(data);
                        itemRptCombo.enableFilteringMode('between');
                    });
                    itemRptCombo.setComboValue('');
                    itemRptCombo.setComboText('');
                    preTally.UserIEReports.applyReportItemFilter('', entryTypeCombo.getSelectedValue());
                    preTally.UserIEReports.rearrangeSerials(ItemMRReportsGrid);
                });
                entryTypeCombo.readonly(true);
                //mhCombo.readonly(true);


                preTally.UserIEReports.applyReportItemFilter('', typeofEntry);
                itemRptCombo = new dhtmlXCombo("itmMR");
                rptFilterParams = '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&m=' + monthData + '&typeFilter=' + typeofEntry+'&znid='+BMR_ZNId;
                itemRptCombo.clearAll();
                itemRptCombo.load(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&mask=master&typeOfEntry=" + entryTypeCombo.getSelectedValue() + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    itemRptCombo.setPlaceholder('Item ');
                    itemRptCombo.enableFilteringMode('between');
                });
                itemRptCombo.setOptionWidth(250);
                itemRptCombo.attachEvent("onChange", function (id) {
                    Filter_Item = id;
                    Filter_EntryType = entryTypeCombo.getSelectedValue();
                    preTally.UserIEReports.applyReportItemFilter(id, entryTypeCombo.getSelectedValue());
                });
        }
        }, viewMRBranchReports: function (Item_id, mode) {
            brnchRptItem = Item_id
            brnchRptMode = mode;
            ptUserIEReportsToolbar.setValue("rpt_date_from", prevFirstDay);
            ptUserIEReportsToolbar.setValue("rpt_date_till", prevLastDay);
            if (branchMRItemRptFlag != 1) {
                branchMRItemRptFlag = 1;
                if (ptUserIEReportsToolbar.getListOptionSelected("rpt_month_filter") && ptUserIEReportsToolbar.getItemText('rpt_month_filter') != 'Select Month') {
                    monthData = ptUserIEReportsToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                }
                var combofiltrInterval;
                ptUserIEReportsTabbar.tabs("viewMRBranchReports").setActive();

                dhxBranchMRReportsLayout = ptUserIEReportsTabbar.cells("viewMRBranchReports").attachLayout("1C");
                dhxBranchMRReportsLayout.cells("a").hideHeader();
                dhxBranchMRReportsLayout.cells("a").setWidth('200');
                BranchMReportsGrid = dhxBranchMRReportsLayout.cells("a").attachGrid();
                BranchMReportsGrid.setImagePath("../../codebase/imgs/");
                BranchMReportsGrid.setSkin("dhx_skyblue");
                $(document).on('click', '.MRBfooter', function () {
                    preTally.UserIEReports.MRBranchFooter($(this).attr('month'), $(this).attr('year'), $(this).attr('mode'));
                });
                if (mode != 2) {
                    dhxBrnchRptTlbr = dhxBranchMRReportsLayout.cells("a").attachToolbar();
                    dhxBrnchRptTlbr.addText('masterMRRptToolbar', '0', 'Type Of Entry');
                    dhxBrnchRptTlbr.addText('masterMRRptToolbar', '1', '<div style="font-weight:bold;width:250px;" id="typeOfEntryMR"></div>');
                    dhxBrnchRptTlbr.addText('masterMRRptToolbar', '2', 'Item');
                    dhxBrnchRptTlbr.addText('masterMRRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="itemNameMR"></div>');
                    dhxBrnchRptTlbr.setIconSize(32);

                    typeOfEntryCombo = new dhtmlXCombo("typeOfEntryMR");
                    typeOfEntryCombo.addOption([
                        ["All", "All"],
                        ["Income", "Income"],
                        ["Expense", "Expense"],
                        ["InternalTransferReceived", "Internal Transfer Received"],
                        ["InternalTransferPaid", "Internal Transfer Paid"],
                        ["BusinessReceived", "Business Received"],
                        ["BusinessReturned", "Business Returned"]
                    ]);

                    typeOfEntryCombo.readonly(true);
                    typeOfEntryCombo.setComboValue(Filter_EntryType.replace(/^m/, ''));
                    typeOfEntryCombo.setComboText(Filter_EntryType);
                    itemFilterCombo = new dhtmlXCombo("itemNameMR");

                    itemFilterCombo.load(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&mask=master&itemId=" + Item_id + "&typeOfEntry=" + Filter_EntryType.replace(/\s/g, '') + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        if (typeof Item_id == 'undefined' || Item_id == 'null') {
                            itemFilterCombo.setComboValue(itemFilterCombo.getSelectedValue());
                        } else {
                            itemFilterCombo.setComboValue(Item_id);

                        }
                        itemFilterCombo.enableFilteringMode('between');
                        Filter_Item = rptBrnchItemId = itemFilterCombo.getSelectedValue();
                        preTally.UserIEReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                    });



                    if (!Filter_Item) {
                        Filter_Item = itemFilterCombo.getSelectedValue();
                    }
                    typeOfEntryCombo.attachEvent("onChange", function (value, text) {
                        Filter_EntryType = typeOfEntryCombo.getSelectedValue();
                        $.post(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&mask=master&itemId=" + Item_id + "&typeOfEntry=" + Filter_EntryType.replace(/\s/g, '') + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function (data) {
                            itemFilterCombo.clearAll();
                            itemFilterCombo.load(data);
                            itemFilterCombo.enableFilteringMode('between');
                            preTally.UserIEReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                        });

                    });
                    itemFilterCombo.setOptionWidth(280);



                    itemFilterCombo.attachEvent("onChange", function () {
                        rptBrnchItemId = itemFilterCombo.getSelectedValue();
                        preTally.UserIEReports.applyReportBrnchItemFilter(itemFilterCombo.getSelectedValue(), '', mode);
                        Filter_Item = itemFilterCombo.getSelectedValue();
                    });
                } else {
                    Filter_Item = '';
                }
                BranchMReportsGrid.enableColSpan(true);
                BranchMReportsGrid.attachEvent("onXLE", function () {
                });
                BranchMReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                BranchMReportsGrid.init();

                BranchMReportsGrid.attachEvent("onRowSelect", function (rowId) {
                    var cnId = BranchMReportsGrid.getSelectedCellIndex();
                    selectedMonth = BranchMReportsGrid.cells(rowId, cnId).getAttribute("month");
                    selectedYear = BranchMReportsGrid.cells(rowId, cnId).getAttribute("year");
                    if (selectedMonth) {
                        UserBasedMRRptFlag = 0;
                        if(rowId!=0)
                        Filter_Loc = rptItem_BranchId = rowId;
                        else
                        Filter_Loc = rptItem_BranchId = 'NULL';    
                        rptItemId = BranchMReportsGrid.getUserData("", "IT_IdMain");
                        prevFirstDay = ptUserIEReportsToolbar.getValue("rpt_date_from");
                        prevLastDay = ptUserIEReportsToolbar.getValue("rpt_date_till");
                        preTally.UserIEReports.viewUserBasedMRReports('', rptItem_BranchId,mode, selectedMonth, selectedYear);
                    }

                    return false;
                });

                BranchMReportsGrid.attachEvent("onFilterEnd", function () {
                    if (BranchMReportsGrid.getRowId(0) != "0") {
                        preTally.UserIEReports.rearrangeSerials(BranchMReportsGrid);
                    }
                });
                if (mode == 2) {
                    Item_id = ItemMRReportsGrid.getAllRowIds();
                    rptBrnchItemId = Item_id;
                    Filter_Item = '';
                    var ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + Filter_Item;
                    preTally.UserIEReports.applyReportBrnchItemFilter(Filter_Item, ItemGridFilters, mode);
                }
            } else {
                ptUserIEReportsTabbar.tabs("viewMRBranchReports").setActive();
            }
        }, viewMRBrnchReports: function () {
            branchMRItemRptFlag = 0;
            var count = ItemMRReportsGrid.getRowsNum();
            if (count == 1) {
                Item_id = ItemMRReportsGrid.getRowId(0);
                mode = 1;
            } else {
                Item_id = ItemMRReportsGrid.getAllRowIds();
                mode = 2;
            }
            if (Item_id == '0')
                return;
            rptBrnchItemId = Item_id;
            ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + Filter_Item;
            preTally.UserIEReports.viewMRBranchReports(Item_id, mode);
        },
        applyReportBrnchItemFilter: function (Item_id, ItemGridFilters, mode = 1) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = "&filter=" + Item_id;
            BranchMReportsGrid.clearAll();
            BranchMReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false");
            BranchMReportsGrid.enableRowsHover(true, "bonusReportHover");
            rptFilterParams = '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&m=' + monthData+'&znid='+BMR_ZNId;
            BranchMReportsGrid.clearAll();
            ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + Filter_Item;
            BranchMReportsGrid.post(preTally.Initialize.encryptURL("requisites/reportMRBranchData.php" + rptFilterParams), "&mode=" + mode + "&filter=" + Item_id + ItemGridFilters, function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                var GridLength = $('.MRheader').length;
                var FooterString = "";
                for (var i = 0; i < GridLength; i++) {
                    FooterString += '<div id>'
                }
                BranchMReportsGrid.makeFilter("LC_MR_Name", 1);
                LC_MR_NameCombo = dhtmlXComboFromSelect("LC_MR_Name");
                LC_MR_NameCombo.setOptionWidth(200);
                LC_MR_NameCombo.setSize(100);
                LC_MR_NameCombo.enableFilteringMode('between');
                LC_MR_NameCombo.attachEvent("onChange", function () {
                    var locCMBVal = LC_MR_NameCombo.getComboText();
                    if (locCMBVal == "All")
                        locCMBVal = '';
                    BranchMReportsGrid.filterBy(1, locCMBVal);
                });

                var srtFlg = 0;
                $('.btn_Sort_BIR').click(function () {
                    var colId = $(this).attr("colNum");
                    var sortType = colId != 1 ? 'int' : 'str';
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BranchMReportsGrid.sortRows(colId, sortType, "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BranchMReportsGrid.sortRows(colId, sortType, "desc");
                        srtFlg = 0;
                    }
                    if (BranchMReportsGrid.getRowId(0) != "0") {
                        preTally.UserIEReports.rearrangeSerials(BranchMReportsGrid);
                    }
                });

            });
        }, applyReportItemFilter: function (itemid, typeofentry) {
            ItemMRReportsGrid.clearAll();
            rptFilterParams = '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&type_filtr=' + typeofentry + '&filter=' + itemid+'&znid='+BMR_ZNId;
            ItemMRReportsGrid.enableRowsHover(true, "bonusReportHover");
            ItemMRReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMRItemData.php" + rptFilterParams), function () {
                ItemMRReportsGrid.enableTooltips("false,false,false,false,false,false,true");
                $("#itmMRptExpense").html(ItemMRReportsGrid.getUserData("", "Expense"));
                $("#itmMRptIncome").html(ItemMRReportsGrid.getUserData("", "Income"));
                $("#itmMRptBusinessReturn").html(ItemMRReportsGrid.getUserData("", "BusinessReturned"));
                $("#itmMRptBusinessReceived").html(ItemMRReportsGrid.getUserData("", "BusinessReceived"));
                $("#itmMRptInternalTransferPaid").html(ItemMRReportsGrid.getUserData("", "InternalTransferPaid"));
                $("#itmMRptInternalTransferReceived").html(ItemMRReportsGrid.getUserData("", "InternalTransferReceived"));
                var srtFlg = 0;
                $('.btn_Sort').click(function () {
                    var colId = $(this).attr("colNum")
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        ItemMRReportsGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        ItemMRReportsGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                    preTally.UserIEReports.rearrangeSerials(ItemMRReportsGrid);
                });
                entryTypeCombo.setComboValue(Filter_EntryType);
            });
        }, viewItemBasedMReports: function (ITId, USId, LCId,mode = 1, Mnt, Yr) {
           if (!Mnt && !Yr && selectedFirstDay && selectedLastDay) {
                ptUserIEReportsToolbar.setValue("rpt_date_from", selectedFirstDay);
                ptUserIEReportsToolbar.setValue("rpt_date_till", selectedLastDay);
            }
            if (Mnt && Yr) {

                    var date = new Date();
                    selectedFirstDay = new Date(parseInt(Yr), parseInt(Mnt) - 1, 1).toString("dd.MM.yyyy");
                    selectedLastDay = new Date(parseInt(Yr), parseInt(Mnt), 0).toString("dd.MM.yyyy");
                    ptUserIEReportsToolbar.setValue("rpt_date_from", selectedFirstDay);
                    ptUserIEReportsToolbar.setValue("rpt_date_till", selectedLastDay);
                    selectedMonth = Mnt;
                    selectedYear = Yr;
                    ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Select Day');
                    ptUserIEReportsToolbar.setItemText('rpt_month_filter', 'Select Month');
                    ptUserIEReportsToolbar.setItemText('rpt_year_filter', 'Select Year');
                }
            if (ItemBasedMRRptFlag != 1) {
                //Filter_Item=ITId;
                ItemBasedMRRptFlag = 1;
                if (!ptUserIEReportsTabbar.cells("viewItemBasedMReports")) {
                    ptUserIEReportsTabbar.addTab("viewItemBasedMReports", "Item - Detailed Reports", null, null, null, true);
                    ptUserIEReportsTabbar.tabs("viewItemBasedMReports").setActive();

                    dhxItemBasedMReportsLayout = ptUserIEReportsTabbar.cells("viewItemBasedMReports").attachLayout("1C");
                    dhxItemBasedMReportsLayout.cells("a").hideHeader();
                    ItemBasedMReportsGridStBar = ptUserIEReportsTabbar.cells("viewItemBasedMReports").attachStatusBar({
                        text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='IBMR_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='IBMR_paging'></div>",
                        height: 30
                    });

                    ItemBasedMReportsGrid = dhxItemBasedMReportsLayout.cells("a").attachGrid();
                    ItemBasedMReportsGrid.setImagePath("../../codebase/imgs/");
                    ItemBasedMReportsGrid.setSkin("dhx_skyblue");
                    ItemBasedMReportsGrid.setHeader("SlNo,<div id='MRIBD_itemFilter' style='width:90%;'></div>,<input id='MRIBD_descFilter' style='width:90%;' class='ibmrtxtfilter'/>,<input id='MRIBD_trkFilter' style='width:90%;' class='ibmrtxtfilter'/>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_IBMR'/></div>,Date,<div id='MRIBD_addedbyFilter' style='width:90%;'></div>,<div id='MRIBD_effBranch' style='width:90%;'></div>,<div id='MRIBD_effUser' style='width:90%;'></div>");                    
                    ItemBasedMReportsGrid.setInitWidths("60,*,*,90,90,80,110,110,110")
                    ItemBasedMReportsGrid.setColAlign("left,left,left,left,right,left,left,left,left")
                    ItemBasedMReportsGrid.setColTypes("ro,ro,ro,ro,ron,ro,ro,ro,ro,ro");
                    ItemBasedMReportsGrid.setColSorting("na,na,na,na,na");                         
                    ItemBasedMReportsGrid.enableEditEvents(false, false, false);
                    ItemBasedMReportsGrid.setNumberFormat("0,000.00", 4);
                    ItemBasedMReportsGrid.enableRowsHover(true, "bonusReportHover");
                    ItemBasedMReportsGrid.enableTooltips("false,false,false,false,false,false,false,false");
                    ItemBasedMReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                    ItemBasedMReportsGrid.enablePaging(true, 50, 5, "IBMR_paging", true);
                    ItemBasedMReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                    ItemBasedMReportsGrid.setImagePath('assets/grid/codebase/imgs/');
                    ItemBasedMReportsGrid.init();
                    
                    ItemBasedMReportsGrid.attachEvent('onXLS', function () {
                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    ItemBasedMReportsGrid.attachEvent('onXLE', function () {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    itmFiltrCombo = new dhtmlXCombo("MRIBD_itemFilter");
                    itmFiltrCombo.load(preTally.Initialize.encryptURL('requisites/report_ItemFilter.php&mask=master&typeOfEntry=' + Filter_EntryType.replace(/\s/g, '')+ '&ITId=' + ITId + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        itmFiltrCombo.enableFilteringMode('between');
                    });
                    amtSort = "asc";
                    addByCombo = new dhtmlXCombo("MRIBD_addedbyFilter");
                    addByCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        addByCombo.enableFilteringMode('between');
                    });
                    paidForBrnch = new dhtmlXCombo("MRIBD_effBranch");
                    paidForBrnch.load(preTally.Initialize.encryptURL('requisites/report_cmbbranch.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        paidForBrnch.enableFilteringMode('between');
                    });
                    paidForUser = new dhtmlXCombo("MRIBD_effUser");
                    paidForUser.load(preTally.Initialize.encryptURL('requisites/report_cmbusers.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        paidForUser.enableFilteringMode('between');
                    });
                    itmFiltrCombo.attachEvent('onChange', function () {
                        preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), amtSort);
                    });
                    addByCombo.attachEvent('onChange', function () {
                        preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), amtSort);
                    });
                    paidForBrnch.attachEvent('onChange', function () {
                        preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), amtSort);
                    });
                    paidForUser.attachEvent('onChange', function () {
                        preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), amtSort);
                    });
                    var filtrInterval;
                    $(".ibmrtxtfilter").keyup(function () {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval(function () {
                            preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), amtSort);
                            clearInterval(filtrInterval);
                        }, 500);

                    });
                    ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + ITId;
                    rptFilterParams = '&mode=' + mode + '&CallType=new&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&ITId=' + ITId + '&USId=' + USId + '&LCId=' + LCId +'&znid='+BMR_ZNId+ ItemGridFilters;
                    ItemBasedMReportsGrid.clearAll();
                    ItemBasedMReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMRItemBasedData.php" + rptFilterParams), function () {
                        $('.IBMR_cnt_tot').html('# : ' + ItemBasedMReportsGrid.getUserData("", "TL_Count"));

                        var srtFlg = 0;
                        $('.btn_Sort_IBMR').click(function () {
                            var colId = $(this).attr("colNum");
                            if (srtFlg == 0) {
                                $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                                preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), 'ASC');
                                srtFlg = 1;
                            } else {
                                $(this).attr("src", 'images/icon/sort-descending-icon.png');
                                preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), 'DESC');
                                srtFlg = 0;
                            }
                        });
                    });

                } else {
                    ItemBasedMReportsGrid.detachFooter(0);
                    ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + ITId;
                    rptFilterParams = '&mode=' + mode + '&CallType=new&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&ITId=' + ITId + '&USId=' + USId + '&LCId=' + LCId +'&znid='+BMR_ZNId+ ItemGridFilters;
                    itmFiltrCombo.clearAll();                    
                    itmFiltrCombo.load(preTally.Initialize.encryptURL('requisites/report_ItemFilter.php&mask=master&typeOfEntry=' + Filter_EntryType.replace(/\s/g, '')+ '&ITId=' + ITId + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        itmFiltrCombo.enableFilteringMode('between');
                    });
                    addByCombo.clearAll();                    
                    addByCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        addByCombo.enableFilteringMode('between');
                    });
                    paidForBrnch.clearAll();                    
                    paidForBrnch.load(preTally.Initialize.encryptURL('requisites/report_cmbbranch.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        paidForBrnch.enableFilteringMode('between');
                    });
                    paidForUser.clearAll();                    
                    paidForUser.load(preTally.Initialize.encryptURL('requisites/report_cmbusers.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                        paidForUser.enableFilteringMode('between');
                    });
                    $(".ibmrtxtfilter").val('');
                    ItemBasedMReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMRItemBasedData.php" + rptFilterParams), function () {
                        $('.IBMR_cnt_tot').html('# : ' + ItemBasedMReportsGrid.getUserData("", "TL_Count"));
                        if (ItemBasedMReportsGrid.getRowId(0) == 0) {
                            //ItemBasedMReportsGrid.attachHeader("<div style='text-align:left;'> # <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort_IBMR'/></div>,Item,#text_filter,#text_filter,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_IBMR'/></div>,Date,#combo_filter");
                            ItemBasedMReportsGrid.detachFooter(0);

                        }
                        //ItemBasedMReportsGrid.detachHeader(1);
                        ItemBasedMReportsGrid.detachFooter(1);
                        var srtFlg = 0;
                        $('.btn_Sort_IBMR').click(function () {
                            var colId = $(this).attr("colNum");
                            if (srtFlg == 0) {
                                $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                                preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), 'ASC');
                                srtFlg = 1;
                            } else {
                                $(this).attr("src", 'images/icon/sort-descending-icon.png');
                                preTally.UserIEReports.applyIBMRFilter(itmFiltrCombo.getSelectedValue(), $('#MRIBD_descFilter').val(), $('#MRIBD_trkFilter').val(), addByCombo.getSelectedValue(), paidForBrnch.getSelectedValue(), paidForUser.getSelectedValue(), 'DESC');
                                srtFlg = 0;
                            }
                        });
                    });

                    ptUserIEReportsTabbar.tabs("viewItemBasedMReports").setActive();
                }
        }
        }, viewUserBasedMRReports: function (USId, LCId, mode = 1, Mnt, Yr,) {
            if (!Mnt && !Yr && selectedFirstDay && selectedLastDay) {
                ptUserIEReportsToolbar.setValue("rpt_date_from", selectedFirstDay);
                ptUserIEReportsToolbar.setValue("rpt_date_till", selectedLastDay);
            }
            if (Mnt && Yr) { 
                ptUserIEReportsToolbar.setValue("rpt_date_from", prevFirstDay);
                ptUserIEReportsToolbar.setValue("rpt_date_till", prevLastDay);
                var date = new Date();
                selectedMonth = Mnt;
                selectedYear = Yr;
                selectedFirstDay = new Date(parseInt(Yr), parseInt(Mnt) - 1, 1).toString("dd.MM.yyyy");
                selectedLastDay = new Date(parseInt(Yr), parseInt(Mnt), 0).toString("dd.MM.yyyy");
                ptUserIEReportsToolbar.setValue("rpt_date_from", selectedFirstDay);
                ptUserIEReportsToolbar.setValue("rpt_date_till", selectedLastDay);                
                ptUserIEReportsToolbar.setItemText('rpt_day_filter', 'Select Day');
                ptUserIEReportsToolbar.setItemText('rpt_month_filter', 'Select Month');
                ptUserIEReportsToolbar.setItemText('rpt_year_filter', 'Select Year');
            }
            if (!ptUserIEReportsTabbar.cells("viewUserBasedMRReports")) {
                ptUserIEReportsTabbar.addTab("viewUserBasedMRReports", "User Based Reports", null, null, null, true);
                ptUserIEReportsTabbar.tabs("viewUserBasedMRReports").setActive();

                dhxUserBasedMReportsLayout = ptUserIEReportsTabbar.cells("viewUserBasedMRReports").attachLayout("1C");
                dhxUserBasedMReportsLayout.cells("a").hideHeader();
                UserBasedMReportsGrid = dhxUserBasedMReportsLayout.cells("a").attachGrid();
                UserBasedMReportsGrid.setImagePath("../../codebase/imgs/");
                UserBasedMReportsGrid.setSkin("dhx_skyblue");
                UserBasedMReportsGrid.setHeader("SlNo,<div id='ubr_itmfiltr' style='width:90%;'></div>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_Sort_UBMR'/></div>,<div id='ubr_effBRfiltr' style='width:90%;'></div>,<div id='ubr_effUSfiltr' style='width:90%;'></div>");
                UserBasedMReportsGrid.setNumberFormat("0,000.00", 2);
                UserBasedMReportsGrid.setInitWidths("60,*,90,150,150")
                UserBasedMReportsGrid.setColAlign("left,left,right,left,left")
                UserBasedMReportsGrid.setColTypes("ro,ro,ron,ro,ro");
                //                      ItemBasedMReportsGrid.setColSorting("int,str,str,str,str");                         
                UserBasedMReportsGrid.enableEditEvents(false, false, false);
                UserBasedMReportsGrid.enableRowsHover(true, "bonusReportHover");
                UserBasedMReportsGrid.enableTooltips("false,false,false,false,false,false");
                //                    ItemBasedMReportsGrid.attachFooter("Total Amount,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>,#cspan,#cspan");
                UserBasedMReportsGrid.attachEvent("onFilterEnd", function () {
                    if (UserBasedMReportsGrid.getRowId(0) != "0") {
                        preTally.UserIEReports.rearrangeSerials(UserBasedMReportsGrid);
                    }
                });
                UserBasedMReportsGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                UserBasedMReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });

                UserBasedMReportsGrid.attachEvent("onRowSelect", function (id) {
                    ItemBasedMRRptFlag = 0;
                    var IT_Id = UserBasedMReportsGrid.getUserData(id, "IT_Id");
                    var LCId = UserBasedMReportsGrid.getUserData(id, "BS_IEByLC");
                    var USId = UserBasedMReportsGrid.getUserData(id, "BS_IEByUS");
                    if (id != 0)
                        preTally.UserIEReports.viewItemBasedMReports(IT_Id, USId, LCId, 1);
                });
                UserBasedMReportsGridStBar = ptUserIEReportsTabbar.cells("viewUserBasedMRReports").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='UBMR_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='UBMR_paging'></div>",
                    height: 30
                });

                UserBasedMReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                UserBasedMReportsGrid.enablePaging(true, 50, 5, "UBMR_paging", true);
                UserBasedMReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                UserBasedMReportsGrid.setImagePath('assets/grid/codebase/imgs/');
                UserBasedMReportsGrid.init();
                ubmr_itmFilter = new dhtmlXCombo("ubr_itmfiltr");
                ubmr_itmFilter.load(preTally.Initialize.encryptURL('requisites/report_ItemFilter.php&mask=master&typeOfEntry=' + Filter_EntryType+ '&ITId=' + Filter_Item + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_itmFilter.enableFilteringMode('between');
                });
                ubmr_itmFilter.attachEvent("onChange", function () {
                    preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), amtSort);
                });
                ubmr_brFilter = new dhtmlXCombo("ubr_effBRfiltr");
                ubmr_brFilter.load(preTally.Initialize.encryptURL('requisites/report_cmbbranch.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_brFilter.enableFilteringMode('between');
                });
                ubmr_brFilter.attachEvent("onChange", function () {
                    preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), amtSort);
                });
                ubmr_usFilter = new dhtmlXCombo("ubr_effUSfiltr");
                ubmr_usFilter.load(preTally.Initialize.encryptURL('requisites/report_cmbusers.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_usFilter.enableFilteringMode('between');
                });
                ubmr_usFilter.attachEvent("onChange", function () {
                    preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), amtSort);
                });

                UserBasedMReportsGrid.enableColSpan(true);
                ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + Filter_Item;
                rptFilterParams = '&CallType=new&mode=' + mode + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&USId=' + USId + '&LCId=' + LCId +'&znid='+BMR_ZNId+ ItemGridFilters;


                UserBasedMReportsGrid.clearAll();
                UserBasedMReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMRUserBasedData.php" + rptFilterParams), function () {
                    //                        preTally.Settings.progressOff(true, dhxLayout, null); 
                    $('.UBMR_cnt_tot').html('# : ' + UserBasedMReportsGrid.getUserData("", "TL_Count"));

                    $(document).on('click', '.RMR_UBD', function () {
                        preTally.UserIEReports.MRUBDFooter(selectedMonth, selectedYear, 2);
                    });
                    var srtFlg = 0;
                    $('.btn_Sort_UBMR').click(function () {
                        var colId = $(this).attr("colNum");
                        if (srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), 'ASC');
                            srtFlg = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), 'DESC');
                            srtFlg = 0;
                        }
                    });
                });
            } else {
                UserBasedMReportsGrid.clearAll();
                UserBasedMReportsGrid.detachFooter(0);
                ubmr_itmFilter.clearAll();                
                ubmr_itmFilter.load(preTally.Initialize.encryptURL('requisites/report_ItemFilter.php&mask=master&typeOfEntry=' + Filter_EntryType+ '&ITId=' + Filter_Item + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_itmFilter.enableFilteringMode('between');
                });
                ubmr_brFilter.clearAll();                
                ubmr_brFilter.load(preTally.Initialize.encryptURL('requisites/report_cmbbranch.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_brFilter.enableFilteringMode('between');
                });
                ubmr_usFilter.clearAll();                
                ubmr_usFilter.load(preTally.Initialize.encryptURL('requisites/report_cmbusers.php&mask=master&typeOfEntry=' + Filter_EntryType + '&LCId=' + LCId + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till"))+'&znid='+BMR_ZNId, function () {
                    ubmr_usFilter.enableFilteringMode('between');
                });
                ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + Filter_Item;
                rptFilterParams = '&CallType=new&mode=' + mode + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&USId=' + USId + '&LCId=' + LCId +'&znid='+BMR_ZNId+ ItemGridFilters;
                UserBasedMReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMRUserBasedData.php" + rptFilterParams), function () {
                    $('.UBMR_cnt_tot').html('# : ' + UserBasedMReportsGrid.getUserData("", "TL_Count"));
                    if (UserBasedMReportsGrid.getRowId(0) == 0) {
                        //UserBasedMReportsGrid.setHeader("SlNo,<div id='ubr_itmfiltr' style='width:90%;'></div>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_Sort_UBMR'/></div>,<div id='ubr_effBRfiltr' style='width:90%;'></div>,<div id='ubr_effUSfiltr' style='width:90%;'></div>");
                        UserBasedMReportsGrid.detachFooter(0);

                    }
                    //UserBasedMReportsGrid.detachHeader(1);
                    UserBasedMReportsGrid.detachFooter(1);
                    var srtFlg = 0;
                    $('.btn_Sort_UBMR').click(function () {
                        var colId = $(this).attr("colNum");
                        if (srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), 'ASC');
                            srtFlg = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.UserIEReports.applyUBMRFitler(ubmr_itmFilter.getSelectedValue(), ubmr_usFilter.getSelectedValue(), ubmr_brFilter.getSelectedValue(), 'DESC');
                            srtFlg = 0;
                        }
                    });
                });

                ptUserIEReportsTabbar.tabs("viewUserBasedMRReports").setActive();
        }

        }, applyUBMRFitler: function (itemId, effUS_Id, effBR_Id, amtSort) {
            UserBasedMReportsGrid.clearAll();
            UserBasedMReportsGrid.detachFooter(0);
            if (itemId == 'null' || itemId == '')
                itemId = Filter_Item;
            if (effBR_Id == 'null' || effBR_Id == '' || effBR_Id == '0')
                effBR_Id = Filter_Loc;
            ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + itemId;
            rptFilterParams = '&CallType=new&mode=' + mode + '&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&USId=' + effUS_Id + '&LCId=' + effBR_Id + '&amtSort=' + amtSort +'&znid='+BMR_ZNId+ ItemGridFilters;
            UserBasedMReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMRUserBasedData.php" + rptFilterParams), function () {
                $('.UBMR_cnt_tot').html('# : ' + UserBasedMReportsGrid.getUserData("", "TL_Count"));
                if (UserBasedMReportsGrid.getRowId(0) == 0) {
                    //UserBasedMReportsGrid.setHeader("SlNo,<div id='ubr_itmfiltr' style='width:90%;'></div>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_Sort_UBMR'/></div>,<div id='ubr_effBRfiltr' style='width:90%;'></div>,<div id='ubr_effUSfiltr' style='width:90%;'></div>");
                    UserBasedMReportsGrid.detachFooter(0);
                }
                //UserBasedMReportsGrid.detachHeader(1);
                UserBasedMReportsGrid.detachFooter(1);
            });
        }, applyIBMRFilter: function (itemId, desc, track, addedby, effBranch, effUser, amtSort) { console.log("EFF"+effBranch);
            ItemBasedMReportsGrid.clearAll();
            ItemBasedMReportsGrid.detachFooter(0);
            if (itemId == 'null' || itemId == '')
                itemId = Filter_Item;
            if (effBranch == 'null' || effBranch == '' || effBranch == '0')
                effBranch = Filter_Loc;
            if (effUser == 'null' || effUser == '')
                effUser = Filter_User;
            ItemGridFilters = '&type_filtr=' + Filter_EntryType + '&item_filtr=' + itemId;
            rptFilterParams = '&mode=' + mode + '&CallType=new&f=' + ptUserIEReportsToolbar.getValue("rpt_date_from") + '&t=' + ptUserIEReportsToolbar.getValue("rpt_date_till") + '&ITId=' + itemId + '&DescText=' + desc + '&Track=' + track + '&addedBy=' + addedby + '&USId=' + effUser + '&LCId=' + effBranch + '&amtSort=' + amtSort +'&znid='+BMR_ZNId+ ItemGridFilters;
            ItemBasedMReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMRItemBasedData.php" + rptFilterParams), function () {
                $('.IBMR_cnt_tot').html('# : ' + ItemBasedMReportsGrid.getUserData("", "TL_Count"));
                if (ItemBasedMReportsGrid.getRowId(0) == 0) {
                    //ItemBasedMReportsGrid.attachHeader("<div style='text-align:left;'> # <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort_IBMR'/></div>,Item,#text_filter,#text_filter,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_IBMR'/></div>,Date,#combo_filter");
                    ItemBasedMReportsGrid.detachFooter(0);

                }
                //ItemBasedMReportsGrid.detachHeader(1);
                ItemBasedMReportsGrid.detachFooter(1);
            });

        }, MRBranchFooter: function (selectedMonth, selectedYear, mode) {
            if (selectedMonth) {
                UserBasedMRRptFlag = 0;
                if (BranchMReportsGrid.getRowsNum() == 1 && BranchMReportsGrid.getRowId(0) != 0)
                    Filter_Loc = rptItem_BranchId = BranchMReportsGrid.getRowId(0);
                else
                    Filter_Loc = rptItem_BranchId = '';
                rptItemId = BranchMReportsGrid.getUserData("", "IT_IdMain");
                var tmpDate = new Date();
                var firstDay = new Date(selectedYear, selectedMonth - 1, 1).toString("dd.MM.yyyy");
                var lastDay = new Date(selectedYear, selectedMonth, 0).toString("dd.MM.yyyy");
                ptUserIEReportsToolbar.setValue('rpt_date_from', firstDay, false);
                ptUserIEReportsToolbar.setValue('rpt_date_till', lastDay, false);
                preTally.UserIEReports.viewUserBasedMRReports(rptItem_UserId, rptItem_BranchId, 2, selectedMonth, selectedYear);
            }
        },
        MRUBDFooter: function (selectedMonth, selectedYear, mode) {
          /*  if (selectedMonth) { */
                ItemBasedMRRptFlag = 0;
                if (BranchMReportsGrid.getRowsNum() == 1 && BranchMReportsGrid.getRowId(0) != 0)
                    rptItem_BranchId = BranchMReportsGrid.getRowId(0);
                else
                    rptItem_BranchId = '';
                rptItemId = BranchMReportsGrid.getUserData("", "IT_IdMain");
                var tmpDate = new Date();
                var firstDay = new Date(selectedYear, selectedMonth - 1, 1).toString("dd.MM.yyyy");
                var lastDay = new Date(selectedYear, selectedMonth, 0).toString("dd.MM.yyyy");
                ptUserIEReportsToolbar.setValue('rpt_date_from', firstDay, false);
                ptUserIEReportsToolbar.setValue('rpt_date_till', lastDay, false);
                var footerlcid = Filter_Loc;
                var footerusid = '';
                if (ubmr_itmFilter.getSelectedValue()) {
                    Filter_Item = ubmr_itmFilter.getSelectedValue();
                }
                if (ubmr_brFilter.getSelectedValue() != "" && ubmr_brFilter.getSelectedValue() != "0") {
                    rptItem_BranchId = footerlcid = ubmr_brFilter.getSelectedValue();
                } else {
                    footerlcid = Filter_Loc;
                }
                if (ubmr_usFilter.getSelectedValue() != "" && ubmr_usFilter.getSelectedValue() != "0") {
                    footerusid = ubmr_usFilter.getSelectedValue();
                }
                preTally.UserIEReports.viewItemBasedMReports(Filter_Item, footerusid, footerlcid, 2);
            /*}*/
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
            mrOB.innerHTML = (preTally.UserIEReports.sumColumn(2) + preTally.UserIEReports.sumColumn(3)).toFixed(0);

            var mrInc = document.getElementById("mr_INC");
            mrInc.innerHTML = (preTally.UserIEReports.sumColumn(5) + preTally.UserIEReports.sumColumn(6)).toFixed(0);

            var mrExp = document.getElementById("mr_EXP");
            mrExp.innerHTML = (preTally.UserIEReports.sumColumn(7) + preTally.UserIEReports.sumColumn(8)).toFixed(0);

            var mrCB = document.getElementById("mr_CB");
            mrCB.innerHTML = (preTally.UserIEReports.sumColumn(9) + preTally.UserIEReports.sumColumn(10)).toFixed(0);

            var mrTR = document.getElementById("mr_TR");
            mrTR.innerHTML = (preTally.UserIEReports.sumColumn(13) + preTally.UserIEReports.sumColumn(14)).toFixed(0);

            var mrTP = document.getElementById("mr_TP");
            mrTP.innerHTML = (preTally.UserIEReports.sumColumn(15) + preTally.UserIEReports.sumColumn(16)).toFixed(0);

            var mrOBS = document.getElementById("mr_OBS");
            mrOBS.innerHTML = "Opening Balance :  " + (parseFloat($("#mr_OB").text()) + preTally.UserIEReports.sumColumn(4)).toFixed(0);

            var mrCBS = document.getElementById("mr_CBS");
            mrCBS.innerHTML = "Closing Balance :  " + (parseFloat($("#mr_CB").text()) + preTally.UserIEReports.sumColumn(11)).toFixed(0);

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
            for (var i = 0; i < BranchMReportsGrid.getRowsNum(); i++) {
                out += parseFloat(BranchMReportsGrid.cells2(i, ind).getValue());
            }
            return out;
        },
        rearrangeSerials: function (GridName) {
            var rowID = 0;
            var i;
            for (i = 0; i < GridName.getRowsNum(); i++) {
                rowID = GridName.getRowId(i);
                GridName.cells(rowID, 0).setValue(i + 1);
            }
        }
    };
})(jQuery, this);