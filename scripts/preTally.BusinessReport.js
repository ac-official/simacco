;
(function ($, window, undefined) {
    var ZNID = 'All';
    var LCID = 'All';
    preTally.BusinessReport = {
        view_BusinessReports: function () {

            if (!dhxMiddleBlockTabs.cells("view_businessreports")) {
                reportBusinessInit = 1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details");

                dhxMiddleBlockTabs.addTab("view_businessreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Business Summary Report &nbsp;&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupBussiness'/>", 245);
                dhxMiddleBlockTabs.tabs("view_businessreports").setActive();
                $(".popupBussiness").click(function () {
                    rptBusinessFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                    preTally.BusinessReport.filterReport(rptBusinessFilterID);
                    /*$(this).data('clicked', true);
                     x = getAbsoluteLeft(this);
                     y = getAbsoluteTop(this); 
                     w = this.offsetWidth;
                     h = this.offsetHeight;
                     var myPop = new dhtmlXPopup();
                     myPop.attachHTML("Bussiness Summary Report");
                     myPop.show(x ,y,w,h);*/
                })
                dhxBusinessReportsLayout = dhxMiddleBlockTabs.cells("view_businessreports").attachLayout("1C");
                rptBusinessFilterID = 'OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                /*      dhxBusinessReportsLayout.cells("a").fixSize(true, true);    
                 dhxBusinessReportsLayout.cells("a").fixSize(true, true);  
                 dhxBusinessReportsLayout.cells('a').hideArrow();                
                 ofzBusinessRprtTree = dhxBusinessReportsLayout.cells("a").attachTree();
                 
                 ofzBusinessRprtTree.attachEvent("onXLS", function(){
                 preTally.Settings.progressOn(false, dhxBusinessReportsLayout, 'a');
                 });
                 ofzBusinessRprtTree.attachEvent("onXLE", function(){
                 preTally.Settings.progressOff(false, dhxBusinessReportsLayout, 'a');
                 });
                 
                 ofzBusinessRprtTree.enableHighlighting(true);                 
                 ofzBusinessRprtTree.setOnClickHandler(preTally.BusinessReport.filterReport);
                 ofzBusinessRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                 ofzBusinessRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/ofzCashRptTree.php&f=BS&r=OF_O"), function(){
                 var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                 if(bsl){
                 ofzBusinessRprtTree.selectItem(bsl,'onClick',true);
                 preTally.BusinessReport.filterReport(bsl);
                 }	
                 }); 
                 var OFType_Options = [
                 ['OF_O', 'obj', 'Office Map', 'office.gif'],
                 ];
                 
                 //                ofzBusinessRprtTree.attachEvent("onClick", function(id){
                 //                    preTally.Settings.progressOn(true, dhxLayout, null);
                 //                    setTimeout(function(){
                 //                        preTally.BusinessReport.filterReport(id);
                 //                    },1);
                 //                });
                 
                 dhxBusinessRprtTlbr = dhxBusinessReportsLayout.cells("a").attachToolbar();
                 dhxBusinessRprtTlbr.setIconsPath("images/icon/");
                 dhxBusinessRprtTlbr.addText('odhType_Rptz', '1', 'Office Map' );
                 dhxBusinessRprtTlbr.setAlign('right');
                 //                dhxBusinessRprtTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
                 //                dhxBusinessRprtTlbr.setAlign('right');
                 //                dhxBusinessRprtTlbr.setListOptionSelected('odhType_Rptz', 'OF_O');                
                 //                dhxBusinessRprtTlbr.attachEvent('onClick', function(id){
                 //                    preTally.Settings.reLoadOffz('BS',id,ofzBusinessRprtTree);
                 //                });  
                 */
                ptBusinessRprtTlbr = dhxBusinessReportsLayout.cells("a").attachToolbar();
                ptBusinessRprtTlbr.setIconsPath("images/icon/default_18/");
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var znacl = unescape(JGG1P3bDnUSDL23Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4)
                    var params = '&RPT_TYPE=BSR';
                else if (bsp == 2)
                    var params = '&mask=Self';

                if (znacl == 1 || bsp==4) {
                    ptBusinessRprtTlbr.addText('text_branch', '2', 'Branch');
                    ptBusinessRprtTlbr.addText('rpt_branch', '3', '<div id="rptBR_branch_combo"></div>');
                    branchFilterCombo = new dhtmlXCombo("rptBR_branch_combo");
                    ptBusinessRprtTlbr.addText('text_zone', '0', 'Zone');
                    ptBusinessRprtTlbr.addText('rpt_zone', '1', '<div id="rptBR_zone_combo"></div>');
                    zoneFilterCombo = new dhtmlXCombo("rptBR_zone_combo");

                    zoneFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_userzone.php"), function () {
                        ZNID='All';
                        LCID='All';
                        if (zoneFilterCombo.getOptionsCount() != 0) {
                            zoneFilterCombo.selectOption('0');
                            if (bsp == 2) {
                                branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params), function () {
                                    branchFilterCombo.selectOption('0');
                                    branchFilterCombo.setComboValue(branchFilterCombo.getSelectedValue());
                                });
                            } else {
                                branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                                });
                            }

                            zoneFilterCombo.attachEvent("onChange", function (id) {
                                LCID='All';
                                branchFilterCombo.clearAll();
                                if (zoneFilterCombo.getSelectedValue() == "All") {
                                    if (bsp == 2) {
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params), function () {
                                            $(".popupBussiness").trigger("click");
                                            branchFilterCombo.selectOption('0');
                                        });
                                    } else {
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                                            $(".popupBussiness").trigger("click");
                                            branchFilterCombo.selectOption('0');
                                        });
                                    }

                                } else {
                                    branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params + "&ZnId=" + zoneFilterCombo.getSelectedValue()), function () {
                                        branchFilterCombo.selectOption('0');
                                        $(".popupBussiness").trigger("click");
                                    });
                                }                                
                                rptBusinessFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + branchFilterCombo.getSelectedValue() + '&ZNID=' + zoneFilterCombo.getSelectedValue();
                                ZNID=zoneFilterCombo.getSelectedValue()?zoneFilterCombo.getSelectedValue():'All'; 
                                LCID=branchFilterCombo.getSelectedValue()?branchFilterCombo.getSelectedValue():'All';
                            });
                        } else {
                            zoneFilterCombo.setComboText("No Zone Assigned");
                            branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                            });
                        }
                    });

                    branchFilterCombo.enableFilteringMode('between');
                    branchFilterCombo.setOptionWidth(180);
                    branchFilterCombo.attachEvent("onChange", function () {
                        LCID = branchFilterCombo.getSelectedValue() ? branchFilterCombo.getSelectedValue() : 'All';
                        ZNID = zoneFilterCombo.getSelectedValue() ? zoneFilterCombo.getSelectedValue() : 'All';
                        console.log('&LCID=' + LCID + '&ZNID=' + ZNID);
                        rptBusinessFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                        preTally.BusinessReport.filterReport(rptBusinessFilterID);
                    });

                    $("#rptBR_zone_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                    $("#rptBR_zone_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                    $("#rptBR_zone_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                    $("#rptBR_zone_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                    $("#rptBR_zone_combo").css({width: "170"});

                    $("#rptBR_branch_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                    $("#rptBR_branch_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                    $("#rptBR_branch_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                    $("#rptBR_branch_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                    $("#rptBR_branch_combo").css({width: "170"});
                    ptBusinessRprtTlbr.setAlign('left');
                } else {
                    ptBusinessRprtTlbr.setAlign('right');
                }

//                var RPType_Options = [
//                    ['0', 'obj', 'Income-Expense Report', 'incomeexpense.png'],
//                    ['1', 'obj', 'Business Report', 'business.png'],
//                ];
//                
//                ptBusinessRprtTlbr.addButtonSelect('iebsType_Rptz', '1', '', RPType_Options, '', '', true, true,3,'select');
//                ptBusinessRprtTlbr.setAlign('left');
//                ptBusinessRprtTlbr.setListOptionSelected('iebsType_Rptz', '0');

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


                ptBusinessRprtTlbr.addSpacer("rpt_branch");
                ptBusinessRprtTlbr.addButtonSelect("rpt_day_filter", '4', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptBusinessRprtTlbr.addButtonSelect("rpt_month_filter", '5', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                ptBusinessRprtTlbr.addButtonSelect("rpt_year_filter", '6', "Select Year", Years_Options, '', '', true, true, 10, 'select');


                ptBusinessRprtTlbr.addSeparator('sep1', '7');

                ptBusinessRprtTlbr.addText("text_from", '8', "From");
                ptBusinessRprtTlbr.addInput("rpt_date_from", '9', "", 75);
                ptBusinessRprtTlbr.addButton("rpt_df_clear", '10', "", "close.gif");
                ptBusinessRprtTlbr.addSeparator('sep2', '11');

                ptBusinessRprtTlbr.addText("text_till", '12', "Till");
                ptBusinessRprtTlbr.addInput("rpt_date_till", '13', "", 75);
                ptBusinessRprtTlbr.addButton("rpt_dt_clear", '14', "", "close.gif");
                ptBusinessRprtTlbr.addSeparator('sep3', '15');

                ptBusinessRprtTlbr.addButton("rpt_date_filter", '16', "Search", "save.gif");
                ptBusinessRprtTlbr.addSeparator('sep4', '17');
                ptBusinessRprtTlbr.addButton("excel_export", '18', "Export", "excel.png");



                var ptRpTb_Inp_Frm = ptBusinessRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptBusinessRprtTlbr.getValue("rpt_date_till"))
                        preTally.BusinessReport.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptBusinessRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptBusinessRprtTlbr.getValue("rpt_date_from"))
                        preTally.BusinessReport.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptBusinessRprtTlbr.attachEvent("onClick", function (id) {
                    var pId = ptBusinessRprtTlbr.getParentId(id);

                    if (id == 'rpt_df_clear')
                        ptBusinessRprtTlbr.setValue('rpt_date_from', '', false);

                    if (id == 'rpt_dt_clear')
                        ptBusinessRprtTlbr.setValue('rpt_date_till', '', false);

                    if (pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptBusinessRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        ptBusinessRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        ptBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReport.filterReport(rptBusinessFilterID);

                    }
                    if (pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptBusinessRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptBusinessRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptBusinessRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReport.filterReport(rptBusinessFilterID);

                    }
                    if (pId == 'rpt_year_filter') {
                        var tmpDate = new Date();

                        /*var curMonth = tmpDate.getMonth(); //get current month
                         var fiscalYr = "";
                         if (curMonth > 3) { //
                         
                         var firstDay = new Date(tmpDate.getFullYear() , 3, 1).toString("dd.MM.yyyy");
                         var lastDay = new Date(tmpDate.getFullYear() + 1, 2, 31).toString("dd.MM.yyyy");
                         }
                         else {
                         var firstDay = new Date(tmpDate.getFullYear() - 1, 3, 1).toString("dd.MM.yyyy");
                         var lastDay = new Date(tmpDate.getFullYear(), 2, 31).toString("dd.MM.yyyy");
                         }*/
                        var firstDay = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(id, 11, 31).toString("dd.MM.yyyy");

                        ptBusinessRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptBusinessRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.BusinessReport.filterReport(rptBusinessFilterID);

                    }

                    if (id == 'rpt_date_filter') {
                        /*if(!ptBusinessRprtTlbr.getValue("rpt_date_from") && !ptBusinessRprtTlbr.getValue("rpt_date_till")) {
                         dhtmlx.message({text: 'Please Select From / To Date'});
                         } else if(!rptBusinessFilterID){
                         dhtmlx.message({text: 'Please Select Office / Branch / User'});
                         } else {
                         preTally.BusinessReport.filterReport(rptBusinessFilterID);
                         }*/
                        ptBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReport.filterReport(rptBusinessFilterID);
                    }
                    if (id == 'excel_export') {
                        preTally.BusinessReport.exportBusinessReport();
                    }
                });

                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;">\
                                        <div class="tb_cnt_tot"># : 0</div>\
                                        <div class="tb_rpt_bussoldStk">OLD STOCK : 0  </div>\
                                        <div class="tb_rpt_bussinc">BUSINESS RECEIVED : 0  </div>\
                                        <div class="tb_rpt_bussexp">BUSINESS RETURNED : 0  </div>\
                                   </div><div id="bussRpt_paging" style="top:0px !important;float:left !important;width:100% !important;"></div>';

                var tbRptObj = dhxBusinessReportsLayout.cells("a").attachStatusBar({
                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 60             // custom height
                });

                ptBusinessRprtsTabbar = dhxBusinessReportsLayout.cells("a").attachTabbar();
                //ptBusinessRprtsTabbar.addTab("a1", tb_data_txt, "730px");
                ptBusinessRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptBusinessRprtsTabbar.addTab("a3", "Report Visulization");
                ptBusinessRprtsTabbar.tabs("a3").hide();

                ptBusinessRprtsTabbar.tabs("a2").setActive();

                ptBusinessRprtsTabbar.attachEvent("onSelect", function (id, last_id) {
                    if (id != "a1") {
                        if (BusinessReport_DetailDataPop && BusinessReport_DetailDataPop.isVisible()) {
                            BusinessReport_DetailDataPop.hide();
                        }
                        if (id == 'a2')
                            preTally.BusinessReport.reLoadSHGrid();
                        if (id == 'a3')
                            preTally.BusinessReport.reLoadGraph();
                        return true;
                    }
                });

                dhxBusinessReportsGrid = ptBusinessRprtsTabbar.cells("a2").attachGrid();
//                dhxBusinessReportsGrid.loadXML("requisites/reportBussData.php", function() { 
//                    dhxBusinessReportsGrid.makeFilter("bussrpt_text_filter" , 1);
//                    var fltTxtValue = dhxBusinessReportsGrid.getFilterElement(1);
//                    fltTxtValue.onkeyup = function(){
//                        reportDetailsGrid.filterBy(1,this.value);
//                    };
//                });

                dhxBusinessReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (dhxBusinessReportsGrid.getRowsNum() == 0) {
                        dhxBusinessReportsGrid.addRow(0, ['', 'No Records Found...', '', '', ''], 0);
                        dhxBusinessReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxBusinessReportDetailLayout = ptBusinessRprtsTabbar.cells("a3").attachLayout("3U");
                dhxBusinessReportDetailLayout.cells("b").setWidth(300);
                dhxBusinessReportDetailLayout.cells("b").setHeight(180);
                dhxBusinessReportDetailLayout.cells("b").hideHeader();

                dhxBusinessReportDetailLayout.cells("a").setText("Income & Expense");
                dhxBusinessReportDetailLayout.cells("c").setText("Details Of Income & Expense");

                dhxBusinessReportChart = dhxBusinessReportDetailLayout.cells("b").attachChart({
                    view: "pie",
                    container: "chart",
                    value: "#value#",
                    labelOffset: -20,
                    radius: 75,
                    label: function (obj) {
                        return "<div class='pieChartLabel' style='border:1px solid " + obj.color + "'> " + currency + " " + obj.value + "</div>";
                    },
                    color: "#color#",
                    legend: {
                        width: 75,
                        align: "right",
                        valign: "bottom",
                        template: "#IE#"
                    }
                });

//                dhxBusinessReportsForm = dhxBusinessReportDetailLayout.cells("a").attachForm();
//                dhxBusinessReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    $("label").css(" text-align","right");
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });

                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptBusinessReportsToolbar = dhxBusinessReportDetailLayout.cells("c").attachToolbar();
                ptBusinessReportsToolbar.setIconsPath("images/icon/");
                ptBusinessReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true, 2, 'select');
                ptBusinessReportsToolbar.setAlign('right');
                ptBusinessReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptBusinessReportsToolbar.disableItem('ieType');

                ptBusinessReportsToolbar.attachEvent('onClick', function (id) {
                    preTally.BusinessReport.reLoadGraph();
                });

                reportBusinessSubHeadChart = dhxBusinessReportDetailLayout.cells("c").attachChart({
                    view: "bar",
                    container: "chart2",
                    value: "#amount#",
                    label: currency + "#amount#",
                    color: "#color#",
                    width: 40,
                    radius: 0,
                    gradient: "rising",
                    tooltip: {
                        template: "#category#"
                    },
                    xAxis: {
                        template: "#title#",
                        title: "Item Category"
                    },
                    yAxis: {
                        title: "Cash"
                    }
                });

                preTally.BusinessReport.filterReport(rptBusinessFilterID);
            } else {
                preTally.BusinessReport.filterReport(rptBusinessFilterID);
                //preTally.BusinessReport.filterReport(ofzBusinessRprtTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("view_businessreports").setActive();
            }
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        reLoadSHGrid: function () {

            if (reportDetailsGrid)
                reportDetailsGrid.destructor();

            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader(",Category, Amount,#select_filter,");
            reportDetailsGrid.setInitWidths("20,*,90,1,1");
            reportDetailsGrid.setColAlign("left,left,right,left,left");
            reportDetailsGrid.setColTypes("ro,ro,ro,ro,ro");
            reportDetailsGrid.enableTooltips("false,false,false,false,false");
            reportDetailsGrid.init();
            reportBusinessSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();
            reportBusinessSubHeadChart.parse(chartBusiness_Response_OB, "json");
            reportBusinessSubHeadChart.parse(chartBusiness_Response_CB, "json");
            var LGD_Inc = chartBusiness_Response_OB;
            var LGD_Exp = chartBusiness_Response_CB;

            var LData = new Object();
            var LGDrows = [];

            if ((LGD_Inc.length != 0) || (LGD_Exp.length != 0))
            {
                LGDrows.push({id: 1, data: ['', "All", '', 'Income', '']});
                var LGCount = 2;
                $.each(LGD_Inc, function (index, value) {
                    LGDrows.push({id: LGCount, data: ['<img src="images/icon/up_12.png" /> ', value.category, $("<span>" + value.amount + "</span>").formatCurrency().text(), 'Income', value.shId]});
                    LGCount++;
                });
                $.each(LGD_Exp, function (index, value) {
                    LGDrows.push({id: LGCount, data: ['<img src="images/icon/down_12.png" /> ', value.category, $("<span>" + value.amount + "</span>").formatCurrency().text(), 'Expense', value.shId]});
                    LGCount++;
                });
            }
            LData.rows = LGDrows;//alert(JSON.stringify(LData));
            reportDetailsGrid.parse(LData, "json");
            reportDetailsGrid.attachEvent("onRowSelect", function (id, data) {
                preTally.BusinessReport.filterSubHead(id);
            });
        },
        filterSubHead: function (id) {

            $("#shJRF").val(reportDetailsGrid.cells(id, 4).getValue());

            preTally.BusinessReport.applyFilter();

            /*$('#bussrpt_text_filter').val('');
             $('#select_filter').val(0);
             dhxBusinessReportsGrid.clearAll();
             data=reportDetailsGrid.cells(id,1).getValue();
             preTally.Settings.progressOn(true, dhxLayout, null);
             str = data.split(" ");            
             if(str[0] == 'Others') {
             
             dhxBusinessReportsGrid.loadXML("requisites/reportBussData.php?"+encrypt(chartBusinessFilterParams), function() { });
             //                dhxBusinessReportsGrid.filterBy(3,'');    
             } else {
             if(data!='All')
             { 
             dhxBusinessReportsGrid.loadXML("requisites/reportBussData.php?"+encrypt(chartBusinessFilterParams+"&SHName="+data), function() { });
             
             //                    dhxBusinessReportsGrid.filterBy(3,function(data){                        
             //                        return data==reportDetailsGrid.cells(id,1).getValue();      
             //                    });
             } else {
             
             dhxBusinessReportsGrid.loadXML("requisites/reportBussData.php?"+encrypt(chartBusinessFilterParams), function() { });
             //                    dhxBusinessReportsGrid.filterBy(3,'');
             }  
             }
             setTimeout(function(){
             preTally.Settings.progressOff(true, dhxLayout, null);
             },1500);*/
        },
//        filterMainHead:function (id){
//           
//            $('#bussrpt_text_filter').val('');
//            dhxBusinessReportsGrid.clearAll();
//            preTally.Settings.progressOn(true, dhxLayout, null);
//            dhxBusinessReportsGrid.loadXML("requisites/reportBussData.php?"+encrypt(chartBusinessFilterParams+"&MHType="+id), function() { });
//          
//            setTimeout(function(){
//                preTally.Settings.progressOff(true, dhxLayout, null);
//            },1500);
//        },
        filterReport: function (id) {
            //alert(id);
            //var Ids = ofzBusinessRprtTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval;
            var filtrBranchInterval;

            /*  var subIds = ofzBusinessRprtTree.getAllSubItems(id);
             if(subIds != ''){
             RP_Ids = id+","+ofzBusinessRprtTree.getAllSubItems(id);
             }else {
             RP_Ids = id;
             }*/

            rptBusinessFilterID = id;
            if (!rptBusinessFilterID) {
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                ptBusinessReportsToolbar.setListOptionSelected('ieType', 'IE_I');

                if (reportBusinessInit == 1)
                {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                    if (bsp == 4)
                    {
                        var date = new Date();
                        var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        var month = date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy");
                        ptBusinessRprtTlbr.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                        ptBusinessRprtTlbr.setValue('rpt_date_till', tDate);
                        ptBusinessRprtTlbr.setItemText('rpt_month_filter', m_names[month]);
                        reportBusinessInit = 0;
                    } else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy");
                        ptBusinessRprtTlbr.setValue('rpt_date_till', cDate);
                        ptBusinessRprtTlbr.setValue('rpt_date_from', cDate);
                        ptBusinessRprtTlbr.setItemText('rpt_day_filter', 'Today');
                        reportBusinessInit = 0;
                    }

                }
                chartBusinessFilterParams = id + '&f=' + ptBusinessRprtTlbr.getValue("rpt_date_from") + '&t=' + ptBusinessRprtTlbr.getValue("rpt_date_till");

                dhxBusinessReportsGrid.destructor();
                dhxBusinessReportsGrid = ptBusinessRprtsTabbar.cells("a2").attachGrid();

                dhxBusinessReportsGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'bussrpt_select_filter' id='ieJRF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text' class='bussrpt_text_filter' id ='itmJRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shJRF' >,<input type='text' class='bussrpt_text_filter' id ='amtJRF' style='width: 90%;' placeholder='Amount'>,<input type='text' class='bussrpt_text_filter' id='brnJRF' style='width: 90%;' placeholder='Branch'> ,<input type='text'  class='bussrpt_text_filter' id='addJRF' style='width: 90%;' placeholder=' Added By'>");
                //dhxBusinessReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                dhxBusinessReportsGrid.setInitWidths("60,80,*,0,100,150,150")
                dhxBusinessReportsGrid.setColAlign("center,center,left,left,right,left,left")
                dhxBusinessReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");

                dhxBusinessReportsGrid.setColSorting("na,na,na,na,na,na,na")
                dhxBusinessReportsGrid.init();
                dhxBusinessReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxBusinessReportsGrid.setSkin("dhx_skyblue")
                dhxBusinessReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                dhxBusinessReportsGrid.enablePaging(true, 50, 5, "bussRpt_paging", true);
                dhxBusinessReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                //dhxBusinessReportsGrid.enableSmartRendering(true,50);
                dhxBusinessReportsGrid.enableTooltips("false,false,false,false,false,false,false");
                //ptBusinessRprtsTabbar.cells("a2").attachStatusBar({text:'<input type="button" value="Export as Excel" onclick="preTally.BusinessReport.exportBusinessReport()">', height:30});
                dhxBusinessReportsGrid.attachEvent("onMouseOver", function (id, ind) {
//                    if(ind == 7) {
//                        this.cells(id,ind).cell.title = 'Click here for More Details';
//                        return false;
//                    }
                });
//              onkeyup='preTally.BusinessReport.applyFilter(this.value);'

                $(".bussrpt_select_filter").change(function () {
                    preTally.BusinessReport.applyFilter();
                });


                $(".bussrpt_text_filter").keyup(function (value) {
                    if (filtrInterval)
                        clearInterval(filtrInterval);

                    filtrInterval = setInterval(function () {
                        preTally.BusinessReport.applyFilter();
                        clearInterval(filtrInterval);
                    }, 500);

                });

                /*$( "#text_filter_branch" ).keyup(function(value) {
                 var mask = this.value;
                 if(filtrBranchInterval) clearInterval(filtrBranchInterval);
                 
                 filtrBranchInterval = setInterval( function() { 
                 preTally.BusinessReport.applyFilterBranch(mask); 
                 clearInterval(filtrBranchInterval); 
                 }, 500);
                 
                 });*/

                var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_Business.php&" + chartBusinessFilterParams), encodeURI(1));
                if (reportIE.xmlDoc.responseText != null) {

                    var reportIE_Response = $.parseJSON(reportIE.xmlDoc.responseText);
//                    dhxBusinessReportsForm.setItemValue("INCOME", reportIE_Response[0].value);
//                    dhxBusinessReportsForm.setItemValue("EXPENSE", reportIE_Response[1].value);

                    //$('.tb_rpt_cob').html('OPENING BALANCE : '+Math.abs(reportIE_Response[2].value).toFixed(2));
                    $('.tb_rpt_bussoldStk').html('OLD STOCK  : ' + reportIE_Response[0].value);
                    $('.tb_rpt_bussinc').html('BUSINESS RECEIVED  : ' + reportIE_Response[1].value);
                    $('.tb_rpt_bussexp').html('BUSINESS RETURNED  : ' + reportIE_Response[2].value);

                    //$('.tb_rpt_ccb').html('CLOSING BALANCE : '+Math.abs(reportIE_Response[3].value).toFixed(2));

                    /* var pl = reportIE_Response[0].value - reportIE_Response[1].value;
                     if(pl < 0) { 
                     plColor = '#EE4339';
                     bkColor = '#E06666';
                     plText  = 'LOSS';
                     plSign  = '-';
                     }
                     else { 
                     plColor = '#435F2D';
                     bkColor = '#6AA84F';
                     plText  = 'PROFIT';
                     plSign  = '';
                     }*/

//                    $('input[name=PL_Data]').css({'color':plColor});
//                    $('.tb_rpt_pl').css({'background-color':bkColor});
//                    
//                    dhxBusinessReportsForm.setItemValue("PL_Data", Math.abs(pl));
//                    $('.tb_rpt_pl').html(plText+' : '+Math.abs(pl.toFixed(2)));


                    dhxBusinessReportChart.clearAll();
                    dhxBusinessReportChart.parse(reportIE_Response, "json");

                    $('input[name=INCOME]').formatCurrency();
                    $('input[name=EXPENSE]').formatCurrency();
                    /*$('input[name=PL_Data]').formatCurrency();
                     $('input[name=PL_Data]').val(plText +'   : ' + $('input[name=PL_Data]').val());*/
                }

                var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_Business.php&" + chartBusinessFilterParams), encodeURI(1));
                if (chartIE.xmlDoc.responseText != null) {

                    ptBusinessReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);

                    chartBusiness_Response_OB = $.parseJSON(chartData[0]);
                    chartBusiness_Response_CB = $.parseJSON(chartData[1]);

                    var actvId = ptBusinessRprtsTabbar.getActiveTab();
                    if (actvId == 'a2')
                        preTally.BusinessReport.reLoadSHGrid();
                    if (actvId == 'a3')
                        preTally.BusinessReport.reLoadGraph();
                }


                dhxBusinessReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBussData.php&" + chartBusinessFilterParams), function () {
                    $('.tb_cnt_tot').html("# : " + dhxBusinessReportsGrid.getUserData("", "TL_Count") + " ");
                });


                dhxBusinessReportsGrid.attachEvent("onRowSelect", function (id, ind) {
                    if (ind != 6)
                        $(".targetCash_" + id).click();
                });

                setTimeout(function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                }, 1500);
            }
        },
        applyFilter: function () {
            var amount = $("#amtJRF").val().replace(/,/g, "");  // remove , from amount
            var filterValue = new Array($('#ieJRF').val(), $('#itmJRF').val(), $('#brnJRF').val(), $('#addJRF').val(), $("#shJRF").val(), amount);
            dhxBusinessReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxBusinessReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBussData.php&" + chartBusinessFilterParams + "&filter=" + filterValue), function () {
                $('.tb_cnt_tot').html("# : " + dhxBusinessReportsGrid.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
//        applyFilterBranch:  function(value){
//            
//                dhxBusinessReportsGrid.clearAll();
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                dhxBusinessReportsGrid.clearAndLoad("requisites/reportBussData.php?"+encrypt(chartBusinessFilterParams)+"&LC_Name="+value, function() {preTally.Settings.progressOff(true, dhxLayout, null);});
//                
//        },
        reLoadGraph: function () {
            var IE_Type = ptBusinessReportsToolbar.getListOptionSelected('ieType');

            if (reportDetailsGrid)
                reportDetailsGrid.destructor();

            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("Legend, Category");
            reportDetailsGrid.setInitWidths("60,*");
            reportDetailsGrid.setColAlign("centre,left");
            reportDetailsGrid.setColTypes("cp,ed");
            reportDetailsGrid.enableTooltips("false,false");

            reportDetailsGrid.init();

            if (dhxWins.window("chartOtherDetails"))
                dhxWins.window("chartOtherDetails").close();
            reportBusinessSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';
            if (IE_Type === 'IE_I') {
                reportBusinessSubHeadChart.parse(chartBusiness_Response_OB, "json");
                legendGridData = chartBusiness_Response_OB;
            }
            if (IE_Type === 'IE_E') {
                reportBusinessSubHeadChart.parse(chartBusiness_Response_CB, "json");
                legendGridData = chartBusiness_Response_CB;
            }

            var legendData = new Object();
            var rows = [];
            $.each(legendGridData, function (index, value) {
                rows.push({
                    id: index,
                    data: [value.color, value.category]
                });
            });
            legendData.rows = rows;
            reportDetailsGrid.parse(legendData, "json");

        },
        chartOtherDetails: function (ie) {
            preTally.Settings.progressOn(false, dhxLayout, 'b');
            if (!dhxWins.isWindow('chartOtherDetails')) {
                dhxWins.createWindow('chartOtherDetails', 400, 100, 600, 350);
                dhxWins.window('chartOtherDetails').setText('Other Category in Chart');
                dhxWins.window('chartOtherDetails').button("minmax1").hide();
                dhxWins.window('chartOtherDetails').button("minmax2").hide();
                dhxWins.window('chartOtherDetails').button("park").hide();
                chartOtherDetailsGrid = dhxWins.window('chartOtherDetails').attachGrid();
            }
            chartBusinessFilterParams += "&ie=" + ie;
            chartOtherDetailsGrid.setColSorting("na,na,na");
            chartOtherDetailsGrid.enableTooltips("false,false,false");
            chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartBussOCOther.php&" + chartBusinessFilterParams), function () {
                preTally.Settings.progressOff(false, dhxLayout, 'b');
            });

            chartOtherDetailsGrid.attachEvent("onRowSelect", function (id, data) {
                $('#bussrpt_text_filter').val('');
                dhxBusinessReportsGrid.clearAll();
                data = chartOtherDetailsGrid.cells(id, 1).getValue();
                $("#shJRF").val(chartOtherDetailsGrid.getUserData(id, "shId"));
                preTally.BusinessReport.applyFilter();

                /*dhxBusinessReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBussData.php&"+chartBusinessFilterParams+"&SHName="+data), function() { 
                 $('.tb_cnt_tot').html("# : "+dhxBusinessReportsGrid.getUserData("", "TL_Count")+" ");
                 });*/

            });
//            chartOtherDetailsGrid.loadXML("requisites/chartIEOther.php?"+encrypt(chartBusinessFilterParams), function() {
//                preTally.Settings.progressOff(false, dhxLayout, 'b');
//            });
//            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
//                var data=chartOtherDetailsGrid.cells(id,1).getValue(); 
//                dhxBusinessReportsGrid.filterBy(3,function(data){                        
//                        return data==chartOtherDetailsGrid.cells(id,1).getValue();      
//                    });
//            }) ;

        },
        showDetailData: function (inp, BS_Id, SH_Id) {
            preTally.Settings.progressOn(false, dhxBusinessReportDetailLayout, 'c');
            if (!BusinessReport_DetailDataPop) {
                BusinessReport_DetailDataPop = new dhtmlXPopup({mode: "left"});

            }
            if (BusinessReport_DetailDataPop.isVisible()) {
                BusinessReport_DetailDataPop.hide();
            }

            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;
            var rptDetailsPop = BusinessReport_DetailDataPop.attachForm();
            //rptDetailsPop.style.backgroundColor="red";
            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_BusinessPop.php&" + params), function () {
                BusinessReport_DetailDataPop.show(x, y, w, h);
                preTally.Settings.progressOff(false, dhxBusinessReportDetailLayout, 'c');
                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
                column0.style.borderRight = "1px solid #a4bed4";
                //column1.style.paddingLeft = "6px";
                //console.log(column0);               


            });

            $('.target_' + BS_Id).unbind();
        },
        hideDetailData: function () {
            if (BusinessReport_DetailDataPop.isVisible()) {
                BusinessReport_DetailDataPop.hide();
            }
        },
        exportBusinessReport: function () {

            var exportfilterValue = {};
            exportfilterValue['From_Date'] = ptBusinessRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = ptBusinessRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TypeFilter'] = $('#ieJRF').val();
            exportfilterValue['ItemNameFilter'] = $('#itmJRF').val();
            exportfilterValue['BranchFilter'] = $('#brnJRF').val();
            exportfilterValue['AddedByFilter'] = $('#addJRF').val();
            exportfilterValue['amount'] = $('#amtJRF').val().replace(/,/g, "");
            exportfilterValue['r'] = rptBusinessFilterID;
            exportfilterValue['report_type'] = "business";
            exportfilterValue['shCRF'] = $("#shJRF").val();
            preTally.Settings.progressOn(true, dhxLayout, null);
            var OFID=unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            $.post(
                    preTally.Initialize.encryptURL('warehouse/ReportExcelExport.php'),
                    {filter: exportfilterValue,OFID:OFID,LCID:LCID,ZNID:ZNID},
                    function (data) {
                        fileName = data.split("XL_");
                        if (fileName[1]) {
                            document.location = "uploads/excelFile/" + fileName[1];
                        }
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });

        },
        setSensWindow: function (inp, k) {
            if (k == "min") {
                window_Calendar.setSensitiveRange(inp.value, null);
            } else {
                window_Calendar.setSensitiveRange(null, inp.value);
            }
        }
    };
})(jQuery, this);