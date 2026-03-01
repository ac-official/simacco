;
(function ($, window, undefined) {
    var filterObject;
    var BMR_branchId = '';
    var BMR_ItmRptId = '';
    var commonFilters = '';
    var itCombo;
    var brcmbFlag = 0;
    var BMRUsrItCombo;
    var BMRUsrAddCombo;
    var BMRIBItCombo;
    var BMRIBAddCombo;
    preTally.BranchMasterReports = {
        viewBranchMasterReports: function () {

            if (!dhxMiddleBlockTabs.cells("viewBranchMasterReports")) {
                BMRptInit = 1;
                BMR_itemRptFlag = 0;
                BMR_visualRptFlag = 0;
                BMR_branchItemRptFlag = 0;
                BMR_ItemBasedRptFlag = 0;
                var ZNFLAG = 0;
                dhxMiddleBlockTabs.addTab("viewBranchMasterReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Branch Master Reports&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshTab'/>", 220);
                dhxMiddleBlockTabs.tabs("viewBranchMasterReports").setActive();

                dhxBranchMstrRpt = dhxMiddleBlockTabs.cells("viewBranchMasterReports").attachLayout("1C");

                ptBranchMstrRptToolbar = dhxBranchMstrRpt.cells("a").attachToolbar();
//                ptBranchMstrRptToolbar.setIconSize(32);
                ptBranchMstrRptToolbar.setIconsPath("images/icon/default_18/");

                ptBranchMstrRptToolbar.setAlign('left');
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4)
                    var params = '&filter=BMR';
                else if (bsp == 2)
                    var params = '&mask=Self';
//                $.post(preTally.Initialize.encryptURL("warehouse/getZoneCheck.php"), function (response) {
//                    ZNFLAG = response;
//                });
                var params = '&filter=BMR';
                var znacl = unescape(JGG1P3bDnUSDL23Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                ptBranchMstrRptToolbar.addText('text_branch', '2', 'Branch');
                ptBranchMstrRptToolbar.addText('rpt_branch', '3', '<div id="rpt_branch_combo"></div>');
                branchFilterCombo = new dhtmlXCombo("rpt_branch_combo");
                if (znacl == 1) {
                    ptBranchMstrRptToolbar.addText('text_zone', '0', 'Zone');
                    ptBranchMstrRptToolbar.addText('rpt_zone', '1', '<div id="rpt_zone_combo"></div>');
                    zoneFilterCombo = new dhtmlXCombo("rpt_zone_combo");

                    zoneFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_userzone.php"), function () {
                        if (zoneFilterCombo.getOptionsCount() != 0) {
                            zoneFilterCombo.selectOption('0');
                             if(bsp==2){
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params+"&ZnId=All"), function () {  
                                        branchFilterCombo.selectOption('0');    
                                        branchFilterCombo.setComboValue(branchFilterCombo.getSelectedValue());
                                        });
                                    }else{
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                                        });
                                    }
                            
                            zoneFilterCombo.attachEvent("onChange", function (id) {
                                branchFilterCombo.clearAll();
                                if (zoneFilterCombo.getSelectedValue() == "All") {
                                    if(bsp==2){
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params+"&ZnId=All"), function () {
                                        $(".refreshTab").trigger( "click" );
                                        branchFilterCombo.setComboValue(branchFilterCombo.getSelectedValue());
                                        });
                                    }else{
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                                        $(".refreshTab").trigger( "click" );
                                        branchFilterCombo.setComboValue(branchFilterCombo.getSelectedValue());
                                        });
                                    }
                                    
                                } else {
                                    branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params + "&ZnId=" + zoneFilterCombo.getSelectedValue()), function () {
                                        branchFilterCombo.selectOption('0');
                                    });
                                }

                            });
                        } else {
                            zoneFilterCombo.setComboText("No Zone Assigned");
                            branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                            });
                        }
                    });
                } else {
                    branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                    });
                }

                if (bsp == 2)
                branchFilterCombo.readonly(true);
                branchFilterCombo.enableFilteringMode('between');
                branchFilterCombo.setOptionWidth(180);
                branchFilterCombo.attachEvent("onChange", function () {

                    BMR_itemRptFlag = 0;
                    BMR_visualRptFlag = 0;
                    BMR_branchItemRptFlag = 0;
                    BMR_ItemBasedRptFlag = 0;

                    BMR_branchId = branchFilterCombo.getSelectedValue();
                    var actvId = ptBranchMstrRptTabbar.getActiveTab();

                    if (actvId == 'viewBMR_Visual')
                        preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                    if (actvId == 'viewBMR_ItemReports')
                        preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                    if (actvId == 'viewBMR_UserItmReports')
                        preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, '');
                    if (actvId == 'viewBMR_ItemBasedReports')
                        preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, '', '');


                });

                $("#rpt_zone_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                $("#rpt_zone_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                $("#rpt_zone_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                $("#rpt_zone_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                $("#rpt_zone_combo").css({width: "170"});

                $("#rpt_branch_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                $("#rpt_branch_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                $("#rpt_branch_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                $("#rpt_branch_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                $("#rpt_branch_combo").css({width: "170"});

                $(".refreshTab").click(function () {

                    BMR_itemRptFlag = 0;
                    BMR_visualRptFlag = 0;
                    BMR_branchItemRptFlag = 0;
                    BMR_ItemBasedRptFlag = 0;
                    //commonFilters = "";
                    BMR_branchId = branchFilterCombo.getSelectedValue();
                    var actvId = ptBranchMstrRptTabbar.getActiveTab();

                    if (actvId == 'viewBMR_Visual')
                        preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                    if (actvId == 'viewBMR_ItemReports')
                        preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                    if (actvId == 'viewBMR_UserItmReports')
                        preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                    if (actvId == 'viewBMR_ItemBasedReports')
                        preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);


                });


                var Days_Options = [];
                var Months_Options = [];
                var Years_Options = [];

                var monthNames = [
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
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }

                for (i = Date.today().getFullYear(); i >= 2024; i--) {
                    Years_Options.push([i, 'obj', i, "calendar_Y.png"]);
                }
                ptBranchMstrRptToolbar.addSpacer("rpt_branch");
                ptBranchMstrRptToolbar.addButtonSelect("rpt_day_filter", '4', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptBranchMstrRptToolbar.addButtonSelect("rpt_month_filter", '5', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                ptBranchMstrRptToolbar.addButtonSelect("rpt_year_filter", '6', "Select Year", Years_Options, '', '', true, true, 10, 'select');


                ptBranchMstrRptToolbar.addSeparator(null, '7');

                ptBranchMstrRptToolbar.addText("text_from", '8', "From");
                ptBranchMstrRptToolbar.addInput("rpt_date_from", '9', "", 75);
                ptBranchMstrRptToolbar.addButton("rpt_df_clear", '10', "", "close.gif");
                ptBranchMstrRptToolbar.addSeparator(null, '11');

                ptBranchMstrRptToolbar.addText("text_till", '12', "Till");
                ptBranchMstrRptToolbar.addInput("rpt_date_till", '13', "", 75);
                ptBranchMstrRptToolbar.addButton("rpt_dt_clear", '14', "", "close.gif");
                ptBranchMstrRptToolbar.addSeparator(null, '15');

                ptBranchMstrRptToolbar.addButton("rpt_date_filter", '16', "Search", "save.gif");


                var ptRpTb_Inp_Frm = ptBranchMstrRptToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptBranchMstrRptToolbar.getValue("rpt_date_till"))
                        preTally.BranchMasterReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptBranchMstrRptToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptBranchMstrRptToolbar.getValue("rpt_date_from"))
                        preTally.BranchMasterReports.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptBranchMstrRptToolbar.attachEvent("onClick", function (id) {
                    //commonFilters = "";
                    BMR_itemRptFlag = 0;
                    BMR_visualRptFlag = 0;
                    BMR_branchItemRptFlag = 0;
                    BMR_ItemBasedRptFlag = 0;
                    BMR_branchId = branchFilterCombo.getSelectedValue();
                    var pId = ptBranchMstrRptToolbar.getParentId(id);

                    if (id == 'rpt_df_clear')
                        ptBranchMstrRptToolbar.setValue('rpt_date_from', '', false);

                    if (id == 'rpt_dt_clear')
                        ptBranchMstrRptToolbar.setValue('rpt_date_till', '', false);

                    if (pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptBranchMstrRptToolbar.setValue('rpt_date_from', dateToday, false);
                        ptBranchMstrRptToolbar.setValue('rpt_date_till', dateToday, false);
                        ptBranchMstrRptToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptBranchMstrRptToolbar.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = ptBranchMstrRptTabbar.getActiveTab();

                        if (actvId == 'viewBMR_Visual')
                            preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                        if (actvId == 'viewBMR_ItemReports')
                            preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                        if (actvId == 'viewBMR_UserItmReports')
                            preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                        if (actvId == 'viewBMR_ItemBasedReports')
                            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);

                    }
                    if (pId == 'rpt_month_filter') {
                        //commonFilters = "";
                        BMR_itemRptFlag = 0;
                        BMR_visualRptFlag = 0;
                        BMR_branchItemRptFlag = 0;
                        BMR_ItemBasedRptFlag = 0;
                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptBranchMstrRptToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptBranchMstrRptToolbar.setValue('rpt_date_from', firstDay, false);
                        ptBranchMstrRptToolbar.setValue('rpt_date_till', lastDay, false);
                        ptBranchMstrRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptBranchMstrRptToolbar.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = ptBranchMstrRptTabbar.getActiveTab();

                        if (actvId == 'viewBMR_Visual')
                            preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                        if (actvId == 'viewBMR_ItemReports')
                            preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                        if (actvId == 'viewBMR_UserItmReports')
                            preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                        if (actvId == 'viewBMR_ItemBasedReports')
                            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);


                    }
                    if (pId == 'rpt_year_filter') {
                        //commonFilters = "";
                        BMR_itemRptFlag = 0;
                        BMR_visualRptFlag = 0;
                        BMR_branchItemRptFlag = 0;
                        BMR_ItemBasedRptFlag = 0;
                        var tmpDate = new Date();
                        var firstDay = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(id, 11, 31).toString("dd.MM.yyyy");

                        ptBranchMstrRptToolbar.setValue('rpt_date_from', firstDay, false);
                        ptBranchMstrRptToolbar.setValue('rpt_date_till', lastDay, false);
                        ptBranchMstrRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptBranchMstrRptToolbar.setItemText('rpt_month_filter', 'Select Month');

                        var actvId = ptBranchMstrRptTabbar.getActiveTab();

                        if (actvId == 'viewBMR_Visual')
                            preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                        if (actvId == 'viewBMR_ItemReports')
                            preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                        if (actvId == 'viewBMR_UserItmReports')
                            preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                        if (actvId == 'viewBMR_ItemBasedReports')
                            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);


                    }

                    if (id == 'rpt_date_filter') {
                        //commonFilters = "";
                        BMR_itemRptFlag = 0;
                        BMR_visualRptFlag = 0;
                        BMR_branchItemRptFlag = 0;
                        BMR_ItemBasedRptFlag = 0;
                        ptBranchMstrRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptBranchMstrRptToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptBranchMstrRptToolbar.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = ptBranchMstrRptTabbar.getActiveTab();

                        if (actvId == 'viewBMR_Visual')
                            preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                        if (actvId == 'viewBMR_ItemReports')
                            preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                        if (actvId == 'viewBMR_UserItmReports')
                            preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                        if (actvId == 'viewBMR_ItemBasedReports')
                            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);


//                        preTally.BranchMasterReports.viewBMR_ItemReports(rptFilterID);
                    }
                });
                ptMRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptMRpTb_Calendar.setDateFormat("%d.%m.%Y");

                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4) {
                    var date = new Date();
                    var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    var month = date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy");
                    ptBranchMstrRptToolbar.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                    ptBranchMstrRptToolbar.setValue('rpt_date_till', tDate);
                    ptBranchMstrRptToolbar.setItemText('rpt_month_filter', m_names[month]);
                    BMRptInit = 0;
                } else {
                    cDate = Date.today().toString("dd.MM.yyyy");
                    ptBranchMstrRptToolbar.setValue('rpt_date_till', cDate);
                    ptBranchMstrRptToolbar.setValue('rpt_date_from', cDate);
                    ptBranchMstrRptToolbar.setItemText('rpt_day_filter', 'Today');
                    BMRptInit = 0;
                }

//                ptBranchMstrRptToolbar.setAlign('right');

                ptBranchMstrRptTabbar = dhxBranchMstrRpt.cells("a").attachTabbar();
//                ptBranchMstrRptTabbar.addTab("viewBMR_Visual", tb_data_txt, "465px");
                ptBranchMstrRptTabbar.addTab("viewBMR_Visual", "Overall Reports");
                ptBranchMstrRptTabbar.addTab("viewBMR_ItemReports", "Item Based Reports");

                preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                ptBranchMstrRptTabbar.tabs("viewBMR_Visual").setActive();

                ptBranchMstrRptTabbar.attachEvent("onSelect", function (id, last_id) {
                    BMR_branchId = branchFilterCombo.getSelectedValue();
                    if (id == 'viewBMR_Visual')
                        preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                    if (id == 'viewBMR_ItemReports')
                        preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                    if (id == 'viewBMR_UserItmReports')
                        preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                    if (id == 'viewBMR_ItemBasedReports')
                        preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);
                    return true;

                });


            } else {
                dhxMiddleBlockTabs.tabs("viewBranchMasterReports").setActive();

                var actvId = ptBranchMstrRptTabbar.getActiveTab();

                if (actvId == 'viewBMR_Visual')
                    preTally.BranchMasterReports.viewBMR_Visual(BMR_branchId);
                if (actvId == 'viewBMR_ItemReports')
                    preTally.BranchMasterReports.viewBMR_ItemReports(BMR_branchId);
                if (actvId == 'viewBMR_UserItmReports')
                    preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                if (actvId == 'viewBMR_ItemBasedReports')
                    preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId);
                return true;

            }
        },
        viewBMR_Visual: function (LCId) {

            if (BMR_visualRptFlag != 1) {

                BMR_visualRptFlag = 1;
                preTally.Settings.progressOn(true, dhxLayout, null);
//                dhxReportsVisualLayout =  ptBranchMstrRptTabbar.cells("viewBMR_Visual").attachLayout("1C");
//                dhxReportsVisualLayout.cells("a").hideHeader();

                dhxOverAllReports = ptBranchMstrRptTabbar.cells("viewBMR_Visual").attachForm();
                rptFilterParams = 'f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till") + '&LCId=' + BMR_branchId;

                dhxOverAllReports.loadStruct(preTally.Initialize.encryptURL("requisites/viewBMRVisual.php&" + rptFilterParams), function () {
                    //                $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    //                $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }

        },
        viewBMR_ItemReports: function (LCId) {

            if (BMR_itemRptFlag != 1) {
                dhxItemReportsLayout = ptBranchMstrRptTabbar.cells("viewBMR_ItemReports").attachLayout("1C");
                dhxItemReportsLayout.cells("a").hideHeader();
                BMR_itemRptFlag = 1;

                ItemReportsGrid = dhxItemReportsLayout.cells("a").attachGrid();
                ItemReportsGrid.setImagePath("../../codebase/imgs/");
                ItemReportsGrid.setSkin("dhx_skyblue")
                //ItemReportsGrid.setHeader("#,<div id='itmRF' style='width: 90%;' placeholder='Item'></div>,\Sub Head,Amount");                    
                ItemReportsGrid.setHeader("SlNo,#select_filter,<div id='itmBMRcombo' style='width:90%;'></div>,#combo_filter,#combo_filter,<div><input style='width:70%' id='amtItRpt' placeholder='Amount'> <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort'/></div>,,#combo_filter");
                ItemReportsGrid.setNumberFormat("0,000.00", 5);
                ItemReportsGrid.setInitWidths("60,120,*,*,120,150,50,0");
                ItemReportsGrid.setColAlign("right,left,left,left,left,right,center,left");
                ItemReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                ItemReportsGrid.enableColSpan(true);
//                ItemReportsGrid.setColSorting("int,str,str,str,str");  
                ItemReportsGrid.enableEditEvents(true, true, true);
                ItemReportsGrid.enableTooltips("false,false,false,false,false,false,false,false");
                //ItemReportsGrid.enableTooltips("false,false,false,false");
                ItemReportsGrid.attachFooter("Sum,#cspan,#cspan,#cspan,#cspan,<span style='float:right;' onclick='preTally.BranchMasterReports.filterBMRUserBasedReports(ItemReportsGrid)'>{#stat_total}</span>,#cspan,#cspan");
                ItemReportsGrid.enableRowsHover(true, "bonusReportHover");
                ItemReportsGrid.init();
                if (ItemReportsGrid.getRowsNum() == 0)
                    ItemReportsGrid.makeFilter("amtItRpt", 5);
                //itCombo = ItemReportsGrid.getFilterElement(2);                
                itCombo = new dhtmlXCombo("itmBMRcombo")
                itCombo.setPlaceholder("Items");
                itCombo.enableFilteringMode("between");


                var shCombo = ItemReportsGrid.getFilterElement(3);
                shCombo.setPlaceholder("Subhead");
                shCombo.enableFilteringMode("between");

                var mhCombo = ItemReportsGrid.getFilterElement(4);
                mhCombo.setPlaceholder("Mainhead");
                mhCombo.enableFilteringMode("between");

                ItemReportsGrid.attachEvent("onRowSelect", function (rowId) {
                    if (rowId != "row1") {
                        BMR_ItemBasedRptFlag = 0;
                        BMR_branchItemRptFlag = 0;
                        BMR_ItmRptId = rowId;
                        //commonFilters = "";
                        preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
                    }
                });
                ItemReportsGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                ItemReportsGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                preTally.BranchMasterReports.applyReportItemFilter(LCId);
                rptFilterParams = '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till");
                /* itCombo.load(preTally.Initialize.encryptURL("requisites/ItemFilterBrnchReports.php&LCId="+BMR_branchId+"&type=filt"+rptFilterParams));*/
                itCombo.attachEvent("onChange", function (itid) {
                    var itmComboVal = itCombo.getSelectedValue();
                    if (itmComboVal != 'All') {
                        if (!itCombo.getSelectedValue() && itCombo.getComboText())
                            itmComboVal = itCombo.getComboText();
                        $("#itmBMRcombo").val(itmComboVal);
                        ItemReportsGrid.filterBy(7, itCombo.getComboText());
                        ItemReportsGrid.getFilterElement(7).setComboValue(itCombo.getComboText());
                    } else {
                        ItemReportsGrid.filterBy(7, '');
                        ItemReportsGrid.getFilterElement(7).setComboValue('');
                    }
                });

                ItemReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (ItemReportsGrid.getRowsNum() == 0) {
                        ItemReportsGrid.addRow("row1", ['', 'No Records Found', '', '', '', '', '', ''], 0);
                        ItemReportsGrid.setColspan("row1", 1, 5)
                        ItemReportsGrid.setRowTextStyle("row1", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        preTally.BranchMasterReports.RewriteSerialNumbers(ItemReportsGrid);
                    }

                });
            }
        },
        applyReportItemFilter: function (LCId) {

            ItemReportsGrid.clearAll();
//            preTally.Settings.progressOn(true, dhxLayout, null);

            rptFilterParams = '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till") + '&LCId=' + BMR_branchId;

            ItemReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBMRItemData.php" + rptFilterParams), function () {
//                preTally.Settings.progressOff(true, dhxLayout, null); 
                ItemReportsGrid.enableTooltips("false,false,false,false,false,false,true");
                itCombo.clearAll();
                itCombo.addOption('All', 'All', '', '', true);
                ItemReportsGrid.forEachRow(function (id) {
                    if (id != '0')
                        itCombo.addOption(id, ItemReportsGrid.getUserData(id, "IT_Name"));
                });
                itCombo.sort('asc');
                itCombo.setOptionIndex('All', 0);
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
                    preTally.BranchMasterReports.RewriteSerialNumbers(ItemReportsGrid);
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
            mrOB.innerHTML = preTally.BranchMasterReports.sumColumn(2) + preTally.BranchMasterReports.sumColumn(3);

            var mrInc = document.getElementById("mr_INC");
            mrInc.innerHTML = preTally.BranchMasterReports.sumColumn(5) + preTally.BranchMasterReports.sumColumn(6);

            var mrExp = document.getElementById("mr_EXP");
            mrExp.innerHTML = preTally.BranchMasterReports.sumColumn(7) + preTally.BranchMasterReports.sumColumn(8);

            var mrCB = document.getElementById("mr_CB");
            mrCB.innerHTML = preTally.BranchMasterReports.sumColumn(9) + preTally.BranchMasterReports.sumColumn(10);

            var mrTR = document.getElementById("mr_TR");
            mrTR.innerHTML = preTally.BranchMasterReports.sumColumn(13) + preTally.BranchMasterReports.sumColumn(14);

            var mrTP = document.getElementById("mr_TP");
            mrTP.innerHTML = preTally.BranchMasterReports.sumColumn(15) + preTally.BranchMasterReports.sumColumn(16);

            var mrOBS = document.getElementById("mr_OBS");
            mrOBS.innerHTML = "Opening Balance :  " + (parseFloat($("#mr_OB").text()) + preTally.BranchMasterReports.sumColumn(4)).toFixed(0);

            var mrCBS = document.getElementById("mr_CBS");
            mrCBS.innerHTML = "Closing Balance :  " + (parseFloat($("#mr_CB").text()) + preTally.BranchMasterReports.sumColumn(11)).toFixed(0);

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
        viewBMR_UserItmReports: function (LCId, Item_id) {
            if (BMR_branchItemRptFlag != 1) {
                BMR_branchItemRptFlag = 1;
//                preTally.Settings.progressOn(true, dhxLayout, null); 
                if (!ptBranchMstrRptTabbar.cells("viewBMR_UserItmReports")) {
                    ptBranchMstrRptTabbar.addTab("viewBMR_UserItmReports", "User Based Item Reports", null, 2, null, true);
                    dhxUserItemReportsLayout = ptBranchMstrRptTabbar.cells("viewBMR_UserItmReports").attachLayout("1C");
                    dhxUserItemReportsLayout.cells("a").hideHeader();
                    UserItemReportsGrid = dhxUserItemReportsLayout.cells("a").attachGrid();
                    UserItemReportsGrid.setImagePath("../../codebase/imgs/");
                    UserItemReportsGrid.setSkin("dhx_skyblue")
                    UserItemReportsGrid.setHeader("SlNo,<div id='UIR_ItmFiltr' style='width:90%;'></div>,<div id='UIR_UsrFiltr' style='width:90%;'></div>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_Sort_BIR'/></div>,");
                    UserItemReportsGrid.setNumberFormat("0,000.00", 3);
                    UserItemReportsGrid.setInitWidths("60,*,*,120,50")
                    UserItemReportsGrid.setColAlign("left,left,left,right,center")
                    UserItemReportsGrid.setColTypes("ro,ro,ro,ro,ro");
                    //              UserItemReportsGrid.setColSorting("int,str,str,str,str");  
                    UserItemReportsGrid.enableEditEvents(true, true, true);
                    UserItemReportsGrid.enableTooltips("false,false,false,false,true");
                    UserItemReportsGrid.attachFooter("Total Amount,#cspan,#cspan,<span style='float:right;' onclick='preTally.BranchMasterReports.filterBMRItemBasedReports(ItemReportsGrid)'>{#stat_total}</span>,#cspan");
                    UserItemReportsGrid.attachEvent("onRowSelect", function (rowId) {
                        if (rowId != "row1") {
                            BMR_ItemBasedRptFlag = 0;
                            BMR_ItmRptId = UserItemReportsGrid.getUserData(rowId, "IT_Id");
                            BMR_rptItemUSId = UserItemReportsGrid.getUserData(rowId, "US_Id");
                            //commonFilters = "";
                            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId, 3)
                        }
                    });
                    UserItemReportsGrid.attachEvent("onXLE", function () {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    UserItemReportsGrid.attachEvent("onXLS", function () {
                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    UserItemReportsGrid.attachEvent("onFilterEnd", function (elements) {
                        if (UserItemReportsGrid.getRowsNum() == 0) {
                            UserItemReportsGrid.addRow("row1", ['', 'No Records Found', '', '', ''], 0);
                            UserItemReportsGrid.setColspan("row1", 1, 5)
                            UserItemReportsGrid.setRowTextStyle("row1", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                        } else {
                            preTally.BranchMasterReports.RewriteSerialNumbers(UserItemReportsGrid);
                        }

                    });
                    UserItemReportsGrid.enableRowsHover(true, "bonusReportHover");                    
                    UserItemReportsGrid.init();
                    
                    BMRUsrItCombo = new dhtmlXCombo("UIR_ItmFiltr");
                    BMRUsrItCombo.enableFilteringMode("between");
                    BMRUsrItCombo.attachEvent("onChange", function (id) {
                        Item_id = id;
                        preTally.BranchMasterReports.applyReportUserItemFilter(LCId, Item_id);
                    });
                    BMRUsrAddCombo = new dhtmlXCombo("UIR_UsrFiltr");
                    BMRUsrAddCombo.attachEvent("onChange", function () {
                        preTally.BranchMasterReports.applyReportUserItemFilter(LCId, Item_id);
                    });
                    BMRUsrAddCombo.enableFilteringMode("between");
                }
                BMRUsrItCombo.clearAll();
                BMRUsrItCombo.load(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&mask=master&ITId=" + Item_id + "&typeOfEntry=" + ItemReportsGrid.getFilterElement("1").value.replace(/\s/g, '') + '&LCId=' + BMR_branchId + '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till")), function () {
                    if (typeof Item_id == 'undefined' || Item_id == 'null' || Item_id == "")
                        BMRUsrItCombo.selectOption('0');
                    else
                        BMRUsrItCombo.setComboValue(Item_id);
                    Item_id = BMRUsrItCombo.getSelectedValue();
                });
                BMRUsrAddCombo.clearAll();
                BMRUsrAddCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + ItemReportsGrid.getFilterElement("1").value.replace(/\s/g, '') + '&LCId=' + BMR_branchId + '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till")), function () {

                });
                ptBranchMstrRptTabbar.tabs("viewBMR_UserItmReports").setActive();
                preTally.BranchMasterReports.applyReportUserItemFilter(LCId, Item_id);
            }
        },
        applyReportUserItemFilter: function (LCId, Item_id) {            
            if (Item_id == 'undefined' || Item_id == 'null' || Item_id == "")
                BMRUsrItCombo.selectOption('0');
            else
                BMRUsrItCombo.setComboValue(Item_id);
            var filterValue = "&filter=" + Item_id + '&LCId=' + BMR_branchId + '&USId=' + BMRUsrAddCombo.getSelectedValue();
            UserItemReportsGrid.clearAll();
//            preTally.Settings.progressOn(true, dhxLayout, null); 
            rptFilterParams = '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till");
            UserItemReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBMRUserItemData.php" + filterValue + rptFilterParams + commonFilters), function () {//                

                var srtFlg = 0;
                $('.btn_Sort_BIR').click(function () {
                    var colId = $(this).attr("colNum");
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        UserItemReportsGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        UserItemReportsGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                    preTally.BranchMasterReports.RewriteSerialNumbers(UserItemReportsGrid);
                });

            });
        },
        viewBMR_ItemBasedReports: function (LCId, ITId, USId, mode) {
            if (BMR_ItemBasedRptFlag != 1) {
                BMR_ItemBasedRptFlag = 1;
                if (!ptBranchMstrRptTabbar.cells("viewBMR_ItemBasedReports")) {
                    ptBranchMstrRptTabbar.addTab("viewBMR_ItemBasedReports", "Item - Detailed Reports", null, 3, null, true);


                    dhxItemBasedReportsLayout = ptBranchMstrRptTabbar.cells("viewBMR_ItemBasedReports").attachLayout("1C");
                    dhxItemBasedReportsLayout.cells("a").hideHeader();

                    BMRItemBasedReportsGrid = dhxItemBasedReportsLayout.cells("a").attachGrid();
                    BMRItemBasedReportsGrid.setImagePath("../../codebase/imgs/");
                    BMRItemBasedReportsGrid.setSkin("dhx_skyblue");
                    BMRItemBasedReportsGrid.setHeader("SlNo,<div id='IBR_ItmFilter' style='width:90%'></div>,<input type='text' class='IBR_input' id='IBR_DescFilter' style='width:90%'></input>,<input type='text' class='IBR_input' id='IBR_TrkFilter' style='width:90%'></input>,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_BIR'/></div>,Date,<div id='IBR_UsrFilter' style='width:90%'></div>");
                    BMRItemBasedReportsGrid.setNumberFormat("0,000.00", 4);
                    BMRItemBasedReportsGrid.setInitWidths("40,*,*,90,150,80,120")
                    BMRItemBasedReportsGrid.setColAlign("left,left,left,left,right,left,left")
                    BMRItemBasedReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                    //                      BMRItemBasedReportsGrid.setColSorting("int,str,str,str,str");  
                    BMRItemBasedReportsGrid.enableEditEvents(true, true, true);
                    BMRItemBasedReportsGrid.enableTooltips("false,false,false,false,false,false");
                    BMRItemBasedReportsGrid.attachFooter("Total Amount,#cspan,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>,,");
                    BMRItemBasedReportsGrid.attachEvent("onXLE", function () {
                        //                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    BMRItemBasedReportsGrid.attachEvent("onXLS", function () {
                        //                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    BMRItemBasedReportsGrid.attachEvent("onFilterEnd", function (elements) {
                        if (BMRItemBasedReportsGrid.getRowsNum() == 0) {
                            BMRItemBasedReportsGrid.addRow("row1", ['', 'No Records Found', '', '', '', '', '', ''], 0);
                            BMRItemBasedReportsGrid.setColspan("row1", 1, 5)
                            BMRItemBasedReportsGrid.setRowTextStyle("row1", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                        } else {
                            preTally.BranchMasterReports.RewriteSerialNumbers(BMRItemBasedReportsGrid);
                        }

                    });
                    BMRItemBasedReportsGrid.enableRowsHover(true, "bonusReportHover");
                    BMRItemBasedReportsGrid.init();                    
                    BMRIBItCombo = new dhtmlXCombo("IBR_ItmFilter");
                    BMRIBItCombo.enableFilteringMode("between");
                    BMRIBItCombo.attachEvent("onChange", function (id) {
                        ITId = id;
                        preTally.BranchMasterReports.applyBMR_ItemBasedReports(BMR_branchId, BMRIBItCombo.getSelectedValue(), BMRIBAddCombo.getSelectedValue(),2);
                    });
                    BMRIBAddCombo = new dhtmlXCombo("IBR_UsrFilter");
                    BMRIBAddCombo.attachEvent("onChange", function (id) {
                        USId=id; 
                        preTally.BranchMasterReports.applyBMR_ItemBasedReports(BMR_branchId, BMRIBItCombo.getSelectedValue(), BMRIBAddCombo.getSelectedValue(),2);
                    });
                    BMRIBAddCombo.enableFilteringMode("between");
                    var filtrInterval;
                    $('.IBR_input').on("keyup", function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.BranchMasterReports.applyBMR_ItemBasedReports(BMR_branchId, BMRIBItCombo.getSelectedValue(), BMRIBAddCombo.getSelectedValue(),2);
                            clearInterval(filtrInterval);
                        }, 500);
                        
                    });
                }
                BMRIBItCombo.clearAll();
                BMRIBItCombo.load(preTally.Initialize.encryptURL("requisites/report_ItemFilter.php&mask=master&ITId=" + ITId + "&typeOfEntry=" + ItemReportsGrid.getFilterElement("1").value.replace(/\s/g, '') + '&LCId=' + BMR_branchId + '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till")), function () {
                    if (typeof ITId == 'undefined' || ITId == 'null' || ITId == "")
                        BMRIBItCombo.selectOption('0');                    
                    ITId = BMRIBItCombo.getSelectedValue();
                });
                BMRIBAddCombo.clearAll();
                BMRIBAddCombo.load(preTally.Initialize.encryptURL('requisites/report_cmbaddeduser.php&mask=master&typeOfEntry=' + ItemReportsGrid.getFilterElement("1").value.replace(/\s/g, '') + '&LCId=' + BMR_branchId + '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till")), function () {

                });
                ptBranchMstrRptTabbar.tabs("viewBMR_ItemBasedReports").setActive();
                preTally.BranchMasterReports.applyBMR_ItemBasedReports(LCId, ITId, USId, mode);
        }
        }, applyBMR_ItemBasedReports: function (LCId, ITId, USId, mode) {
            rptFilterParams = '&f=' + ptBranchMstrRptToolbar.getValue("rpt_date_from") + '&t=' + ptBranchMstrRptToolbar.getValue("rpt_date_till") + '&ITId=' + ITId+'&Desc='+$('#IBR_DescFilter').val() +'&Track='+$('#IBR_TrkFilter').val() + '&USId=' + USId + '&LCId=' + BMR_branchId + '&mode=' + mode;
            BMRItemBasedReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBMRUserEntryData.php" + rptFilterParams + commonFilters), function () {
                var srtFlg = 0;
                $('.btn_Sort_BIR').click(function () {
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BMRItemBasedReportsGrid.sortRows(4, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BMRItemBasedReportsGrid.sortRows(4, "int", "desc");
                        srtFlg = 0;
                    }
                    preTally.BranchMasterReports.RewriteSerialNumbers(BMRItemBasedReportsGrid);
                });
            });

        }, filterBMRUserBasedReports: function (GridName) {            
            commonFilters = "&mode=2&typeFilter=" + GridName.getFilterElement("1").value + "&ItemFilter=" + itCombo.getSelectedValue() + "&SHFilter=" + GridName.getFilterElement("3").getSelectedText() + "&MHFilter=" + GridName.getFilterElement("4").getSelectedText();
            BMR_ItemBasedRptFlag = 0;
            BMR_branchItemRptFlag = 0;
            BMR_ItmRptId = "";
            preTally.BranchMasterReports.viewBMR_UserItmReports(BMR_branchId, BMR_ItmRptId);
        }, filterBMRItemBasedReports: function (GridName) {            
            var UsrFilter = "";
            var ItmFilter = itCombo.getSelectedValue();
            if (BMRUsrItCombo.getSelectedValue())
                ItmFilter = BMRUsrItCombo.getSelectedValue();
            if (BMRUsrAddCombo.getSelectedValue())
            BMR_rptItemUSId = UsrFilter = BMRUsrAddCombo.getSelectedValue();
            commonFilters = "&typeFilter=" + GridName.getFilterElement("1").value + "&ItemFilter=" + ItmFilter + "&SHFilter=" + GridName.getFilterElement("3").getSelectedText() + "&MHFilter=" + GridName.getFilterElement("4").getSelectedText() + "&USFilter=" + UsrFilter;
            BMR_ItemBasedRptFlag = 0;
            BMR_branchItemRptFlag = 0;
            BMR_ItmRptId = "";
            preTally.BranchMasterReports.viewBMR_ItemBasedReports(BMR_branchId, BMR_ItmRptId, BMR_rptItemUSId, 2);
        }, RewriteSerialNumbers: function (GridName) {
            setTimeout(function () {
                var srvSLNo = 1;
                GridName.forEachRow(function (rId) {
                    if (rId != "row1") {
                        var changedPos = GridName.getRowId(srvSLNo - 1);
                        if (GridName.doesRowExist(changedPos) == true) {
                            GridName.cells(changedPos, 0).setValue(srvSLNo);
                            srvSLNo++;
                        }
                    }
                });
            }, 5);
        }
    };
})(jQuery, this);