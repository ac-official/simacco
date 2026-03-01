;
(function ($, window, undefined) {
    var ZNID='All';
    var LCID='All';
    preTally.CashBalanceSheet = {        
        view_CashBSReports: function () {

            if (!dhxMiddleBlockTabs.cells("view_cashbsreports")) {
                reportCashBSInit = 1;

                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details");
                dhxMiddleBlockTabs.addTab("view_cashbsreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp; Cash Transaction Summary &nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupTransaction'/>", 270);
                dhxMiddleBlockTabs.tabs("view_cashbsreports").setActive();
                $(".popupTransaction").click(function () {
                    //$(this).data('clicked', true);
                    rptCBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                    preTally.CashBalanceSheet.filterReport(rptCBSFilterID);
                    /*x = getAbsoluteLeft(this);
                     y = getAbsoluteTop(this); 
                     w = this.offsetWidth;
                     h = this.offsetHeight;
                     var myPop = new dhtmlXPopup();
                     myPop.attachHTML("Transaction Summary Report");
                     myPop.show(x ,y,w,h);//params are: x, y, width, height*/
                })

                dhxMiddleBlockTabs.tabs("view_cashbsreports").setActive();
                dhxCashOBReportsLayout = dhxMiddleBlockTabs.cells("view_cashbsreports").attachLayout("1C");
                //dhxCashOBReportsLayout.cells("a").setWidth(200);
                dhxCashOBReportsLayout.cells("a").hideHeader();
                //dhxCashOBReportsLayout.cells("a").fixSize(true, true);    
                //dhxCashOBReportsLayout.cells("b").fixSize(true, true);  
                //dhxCashOBReportsLayout.cells('b').hideArrow();
                /* ofzRprtTree = dhxCashOBReportsLayout.cells("a").attachTree();
                 
                 ofzRprtTree.attachEvent("onXLS", function(){
                 preTally.Settings.progressOn(false, dhxCashOBReportsLayout, 'a');
                 });
                 ofzRprtTree.attachEvent("onXLE", function(){
                 preTally.Settings.progressOff(false, dhxCashOBReportsLayout, 'a');
                 });                
                 ofzRprtTree.enableHighlighting(true);                 
                 ofzRprtTree.setOnClickHandler(preTally.CashBalanceSheet.filterReport);
                 ofzRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                 ofzRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/ofzCashRptTree.php&f=BS&r=OF_O"), function(){
                 var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                 if(bsl){
                 ofzRprtTree.selectItem(bsl,'onClick',true);
                 preTally.CashBalanceSheet.filterReport(bsl);
                 }	
                 }); 
                 var OFType_Options = [
                 ['OF_O', 'obj', 'Office Map', 'office.gif'],
                 ];
                 
                 //                ofzRprtTree.attachEvent("onClick", function(id){
                 //                    preTally.Settings.progressOn(true, dhxLayout, null);
                 //                    setTimeout(function(){
                 //                        preTally.CashBalanceSheet.filterReport(id);
                 //                    },1);
                 //                });
                 */
                /*  dhxCashBSRprtTlbr = dhxCashOBReportsLayout.cells("a").attachToolbar();
                 dhxCashBSRprtTlbr.setIconsPath("images/icon/");
                 dhxCashBSRprtTlbr.addText('odhType_Rptz', '1', 'Office Map' );
                 dhxCashBSRprtTlbr.setAlign('right');*/
//                dhxCashBSRprtTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
//                dhxCashBSRprtTlbr.setAlign('right');
//                dhxCashBSRprtTlbr.setListOptionSelected('odhType_Rptz', 'OF_O');                
//                dhxCashBSRprtTlbr.attachEvent('onClick', function(id){
//                    preTally.Settings.reLoadOffz('BS',id,ofzRprtTree);
//                });                  
                ptCashBSRprtTlbr = dhxCashOBReportsLayout.cells("a").attachToolbar();
                ptCashBSRprtTlbr.setIconsPath("images/icon/default_18/");
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo); //ACLBSheet
                var znacl = unescape(JGG1P3bDnUSDL23Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsp == 4)
                    var params = '&RPT_TYPE=BSR';
                else if (bsp == 2)
                    var params = '&mask=Self';
                if (znacl == 1 || bsp == 4) {
                    ptCashBSRprtTlbr.addText('text_branch', '2', 'Branch');
                    ptCashBSRprtTlbr.addText('rpt_branch', '3', '<div id="rptCBS_branch_combo"></div>');
                    branchFilterCombo = new dhtmlXCombo("rptCBS_branch_combo");
                    ptCashBSRprtTlbr.addText('text_zone', '0', 'Zone');
                    ptCashBSRprtTlbr.addText('rpt_zone', '1', '<div id="rptCBS_zone_combo"></div>');
                    zoneFilterCombo = new dhtmlXCombo("rptCBS_zone_combo");

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
                                            $(".popupTransaction").trigger("click");
                                            branchFilterCombo.selectOption('0');
                                        });
                                    } else {
                                        branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                                            $(".popupTransaction").trigger("click");
                                            branchFilterCombo.selectOption('0');
                                        });
                                    }

                                } else {
                                    branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/combo_znlocations.php" + params + "&ZnId=" + zoneFilterCombo.getSelectedValue()), function () {
                                        branchFilterCombo.selectOption('0');
                                        $(".popupTransaction").trigger("click");
                                    });
                                }
                                rptCashBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + branchFilterCombo.getSelectedValue() + '&ZNID=' + zoneFilterCombo.getSelectedValue();
                                ZNID=zoneFilterCombo.getSelectedValue()?zoneFilterCombo.getSelectedValue():'All'; 
                                LCID=branchFilterCombo.getSelectedValue()?branchFilterCombo.getSelectedValue():'All';
                            });
                        } else {
                            zoneFilterCombo.setComboText("No Zone Assigned");
                            ZNID='All';
                            LCID='All';
                            branchFilterCombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + params), function () {
                            });
                        }
                    });                
                branchFilterCombo.enableFilteringMode('between');
                branchFilterCombo.setOptionWidth(180);
                branchFilterCombo.attachEvent("onChange", function () {
                    LCID=branchFilterCombo.getSelectedValue()?branchFilterCombo.getSelectedValue():'All';
                    ZNID=zoneFilterCombo.getSelectedValue()?zoneFilterCombo.getSelectedValue():'All';                    
                    rptCashBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' +LCID+ '&ZNID=' + ZNID;
                    preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);
                });

                $("#rptCBS_zone_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                $("#rptCBS_zone_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                $("#rptCBS_zone_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                $("#rptCBS_zone_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                $("#rptCBS_zone_combo").css({width: "170"});

                $("#rptCBS_branch_combo").find(".dhxcombo_select_button").css({"background-image": "none", "border": "none"});
                $("#rptCBS_branch_combo").find(".dhxcombo_select_img").css({"display": "none", "background-repeat": "no-repeat", "background-image": "url('images/preload_combo.GIF')"});
                $("#rptCBS_branch_combo").find(".dhxcombo_dhx_skyblue").css({height: "18", width: "170"});
                $("#rptCBS_branch_combo").find(".dhxcombo_input").css({height: "18", width: "170", "fontSize": 11});
                $("#rptCBS_branch_combo").css({width: "170"});
                ptCashBSRprtTlbr.setAlign('left');
                }else{
                ptCashBSRprtTlbr.setAlign('right');
                }
                
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
                
                ptCashBSRprtTlbr.addSpacer("rpt_branch");
                ptCashBSRprtTlbr.addButtonSelect("rpt_day_filter", '4', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptCashBSRprtTlbr.addButtonSelect("rpt_month_filter", '5', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                ptCashBSRprtTlbr.addButtonSelect("rpt_year_filter", '6', "Select Year", Years_Options, '', '', true, true, 10, 'select');
                ptCashBSRprtTlbr.addSeparator('sep1', '7');
                ptCashBSRprtTlbr.addText("text_from", '8', "From");
                ptCashBSRprtTlbr.addInput("rpt_date_from", '9', "", 75);
                ptCashBSRprtTlbr.addButton("rpt_df_clear", '10', "", "close.gif");
                ptCashBSRprtTlbr.addSeparator('sep2', '11');
                ptCashBSRprtTlbr.addText("text_till", '12', "Till");
                ptCashBSRprtTlbr.addInput("rpt_date_till", '13', "", 75);
                ptCashBSRprtTlbr.addButton("rpt_dt_clear", '14', "", "close.gif");
                ptCashBSRprtTlbr.addSeparator('sep3', '15');
                ptCashBSRprtTlbr.addButton("rpt_date_filter", '16', "Search", "save.gif");
                ptCashBSRprtTlbr.addSeparator('sep4', '17');
                ptCashBSRprtTlbr.addButton("excel_export", '18', "Export", "excel.png");
                var ptRpTb_Inp_Frm = ptCashBSRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptCashBSRprtTlbr.getValue("rpt_date_till"))
                        preTally.CashBalanceSheet.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptCashBSRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptCashBSRprtTlbr.getValue("rpt_date_from"))
                        preTally.CashBalanceSheet.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptCashBSRprtTlbr.attachEvent("onClick", function (id) {
                    var pId = ptCashBSRprtTlbr.getParentId(id);

                    if (id == 'rpt_df_clear')
                        ptCashBSRprtTlbr.setValue('rpt_date_from', '', false);

                    if (id == 'rpt_dt_clear')
                        ptCashBSRprtTlbr.setValue('rpt_date_till', '', false);

                    if (pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptCashBSRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        ptCashBSRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        ptCashBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptCashBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);

                    }
                    if (pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptCashBSRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptCashBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptCashBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptCashBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptCashBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);

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

                        ptCashBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptCashBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptCashBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptCashBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);

                    }

                    if (id == 'rpt_date_filter') {
                        /*if(!ptCashBSRprtTlbr.getValue("rpt_date_from") && !ptCashBSRprtTlbr.getValue("rpt_date_till")) {
                         dhtmlx.message({text: 'Please Select From / To Date'});
                         } else if(!rptCashBSFilterID){
                         dhtmlx.message({text: 'Please Select Office / Branch / User'});
                         } else {
                         preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);
                         }*/
                        ptCashBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptCashBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptCashBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);
                    }
                    if (id == 'excel_export') {
                        preTally.CashBalanceSheet.exportCashBalanceReport();
                    }
                });

                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");             

                // internal section added at 19-05-2025
                var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;">\
                                       <div class="tb_cnt_tot"># : 0</div>\
                                       <div class="tb_rpt_cob"> OPENING BAL : 0</div>\
                                        <div class="tb_rpt_inc">INCOME : 0</div>\
                                        <div class="tb_rpt_exp">EXPENSE : 0</div>\
                                        <div class="tb_rpt_ccb"> CLOSING BAL : 0</div>\
                                        <div class="tb_rpt_inti" title="Internal Transfer Received">INTERNAL RECEIVED : 0</div>\
                                        <div class="tb_rpt_inte" title="Internal Transfer Paid">INTERNAL PAID : 0</div>\
                                    </div><div id="cashBS_paging" style="top:5px !important;float:left !important;width:100% !important;line-height:15px !important;"></div>';

                var tbRptObj = dhxCashOBReportsLayout.cells("a").attachStatusBar({
                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 60             // custom heightattachStatusBar
                });

                ptCashBSRprtsTabbar = dhxCashOBReportsLayout.cells("a").attachTabbar();
                //ptCashBSRprtsTabbar.addTab("a1", tb_data_txt, "730px");
                ptCashBSRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptCashBSRprtsTabbar.addTab("a3", "Report Visulization");
                ptCashBSRprtsTabbar.tabs("a3").hide();

                ptCashBSRprtsTabbar.tabs("a2").setActive();

                ptCashBSRprtsTabbar.attachEvent("onSelect", function (id, last_id) {
                    if (id != "a1") {
                        if (CashBS_DetailDataPop && CashBS_DetailDataPop.isVisible()) {
                            CashBS_DetailDataPop.hide();
                        }
                        if (id == 'a2')
                            preTally.CashBalanceSheet.reLoadSHGrid();
                        if (id == 'a3')
                            preTally.CashBalanceSheet.reLoadGraph();
                        return true;
                    }
                });
                dhxCashBSReportsGrid = ptCashBSRprtsTabbar.cells("a2").attachGrid();
//                dhxCashBSReportsGrid.loadXML("requisites/reportCashBSData.php", function() { 
//                    dhxCashBSReportsGrid.makeFilter("cshrpt_text_filter" , 1);
//                    var fltTxtValue = dhxCashBSReportsGrid.getFilterElement(1);
//                    fltTxtValue.onkeyup = function(){
//                        reportDetailsGrid.filterBy(1,this.value);
//                    };
//                });                

                dhxCashBSReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (dhxCashBSReportsGrid.getRowsNum() == 0) {
                        dhxCashBSReportsGrid.addRow(0, ['', 'No Records Found...', '', '', ''], 0);
                        dhxCashBSReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxCashBSReportDetailLayout = ptCashBSRprtsTabbar.cells("a3").attachLayout("3U");
                dhxCashBSReportDetailLayout.cells("b").setWidth(300);
                dhxCashBSReportDetailLayout.cells("b").setHeight(180);
                dhxCashBSReportDetailLayout.cells("b").hideHeader();

                dhxCashBSReportDetailLayout.cells("a").setText("Income & Expense");
                dhxCashBSReportDetailLayout.cells("c").setText("Details Of Income & Expense");

                dhxCashBSReportChart = dhxCashBSReportDetailLayout.cells("b").attachChart({
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

//                dhxCashBSReportsForm = dhxCashBSReportDetailLayout.cells("a").attachForm();
//                dhxCashBSReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    //$("label").css(" text-align","right");
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });

                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptCashBSReportsToolbar = dhxCashBSReportDetailLayout.cells("c").attachToolbar();
                ptCashBSReportsToolbar.setIconsPath("images/icon/");
                ptCashBSReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true, 2, 'select');
                ptCashBSReportsToolbar.setAlign('left');
                ptCashBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptCashBSReportsToolbar.disableItem('ieType');

                ptCashBSReportsToolbar.attachEvent('onClick', function (id) {
                    preTally.CashBalanceSheet.reLoadGraph();
                });

                reportCashBSSubHeadChart = dhxCashBSReportDetailLayout.cells("c").attachChart({
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

                rptCashBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' +LCID + '&ZNID=' + ZNID;
                preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);
            } else {
                rptCashBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
                preTally.CashBalanceSheet.filterReport(rptCashBSFilterID);
                dhxMiddleBlockTabs.tabs("view_cashbsreports").setActive();
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

            reportCashBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            reportCashBSSubHeadChart.parse(chartCashBS_Response_OB, "json");
            reportCashBSSubHeadChart.parse(chartCashBS_Response_CB, "json");
            var LGD_Inc = chartCashBS_Response_OB;
            var LGD_Exp = chartCashBS_Response_CB;

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
                preTally.CashBalanceSheet.filterSubHead(id);
            });
        },
        filterSubHead: function (id) {

            $("#shCRF").val(reportDetailsGrid.cells(id, 4).getValue());
            preTally.CashBalanceSheet.applyFilter();
        },
        filterMainHead: function (id) {

            $('#cshrpt_text_filter').val('');
            dhxCashBSReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxCashBSReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportCashBSData.php&" + chartCashBSFilterParams + "&MHType=" + id), function () { });

            setTimeout(function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
            }, 1500);
        },
        filterReport: function (id) {
            //alert(id);
            //var Ids = ofzRprtTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval;
            var filtrBranchInterval;

            /* var subIds = ofzRprtTree.getAllSubItems(id);
             if(subIds != ''){
             RP_Ids = id+","+ofzRprtTree.getAllSubItems(id);
             }else {
             RP_Ids = id;
             }*/

            rptCashBSFilterID = '&OFID=' + unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo) + '&LCID=' + LCID + '&ZNID=' + ZNID;
            if (!rptCashBSFilterID) {
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                ptCashBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');

                if (reportCashBSInit == 1)
                {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                    if (bsp == 4)
                    {
                        var date = new Date();
                        var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        var month = date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy");
                        ptCashBSRprtTlbr.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                        ptCashBSRprtTlbr.setValue('rpt_date_till', tDate);
                        ptCashBSRprtTlbr.setItemText('rpt_month_filter', m_names[month]);
                        reportCashBSInit = 0;
                    } else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy");
                        ptCashBSRprtTlbr.setValue('rpt_date_till', cDate);
                        ptCashBSRprtTlbr.setValue('rpt_date_from', cDate);
                        ptCashBSRprtTlbr.setItemText('rpt_day_filter', 'Today');
                        reportCashBSInit = 0;
                    }

                }
                chartCashBSFilterParams = rptCashBSFilterID + '&f=' + ptCashBSRprtTlbr.getValue("rpt_date_from") + '&t=' + ptCashBSRprtTlbr.getValue("rpt_date_till");

                dhxCashBSReportsGrid.destructor();
                dhxCashBSReportsGrid = ptCashBSRprtsTabbar.cells("a2").attachGrid();

                dhxCashBSReportsGrid.setImagePath("../../codebase/imgs/");                
                var transtypeOpt = preTally.BranchBSReports.transactionOptions(1); // new options added at 19-05-2025
                dhxCashBSReportsGrid.setHeader("Msg,Edit,SlNo,<select style = 'width:60px;' class = 'csh_select_filter' id='ieCRF'>"+transtypeOpt+"</select>,<input type='text' class='cshrpt_text_filter' id ='itmCRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shCRF' >,<input type='text'  class='cshrpt_text_filter' id ='amtCRF' style='width: 90%;' placeholder='Amount'>,<input type='text'  class='cshrpt_text_filter' id='brnCRF' style='width: 90%;' placeholder='Branch'> ,<input type='text'  class='cshrpt_text_filter' id='addCRF' style='width: 90%;' placeholder=' Added By'>,Date");
                dhxCashBSReportsGrid.setInitWidths("40,40,50,80,*,0,100,90,90,90")
                dhxCashBSReportsGrid.setColAlign("center,center,center,center,left,left,right,left,left,left");
                dhxCashBSReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

                dhxCashBSReportsGrid.setColSorting("na,na,na,na,na,na,na,na,na,na")
                dhxCashBSReportsGrid.init();
                dhxCashBSReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxCashBSReportsGrid.setSkin("dhx_skyblue");
                dhxCashBSReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                dhxCashBSReportsGrid.enablePaging(true, 50, 5, "cashBS_paging", true);
                dhxCashBSReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                //dhxCashBSReportsGrid.enableSmartRende  dhxCashBSReportsGrid.setHeader("Msg,Edit,SlNo,<select style = 'width:60px;' class = 'csh_select_filter' id='ieCRF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text' class='cshrpt_text_filter' id ='itmCRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shCRF' >,<input type='text'  class='cshrpt_text_filter' id ='amtCRF' style='width: 90%;' placeholder='Amount'>,<input type='text'  class='cshrpt_text_filter' id='brnCRF' style='width: 90%;' placeholder='Branch'> ,<input type='text'  class='cshrpt_text_filter' id='addCRF' style='width: 90%;' placeholder=' Added By'>,Date");                ring(true,50);
                dhxCashBSReportsGrid.enableTooltips("true,true,false,false,false,false,false,false,false,false");
                //ptCashBSRprtsTabbar.cells("a2").attachStatusBar({text:'<input type="button" value="Export as Excel" onclick="preTally.CashBalanceSheet.exportCashBalanceReport()">', height:30});
//                dhxCashBSReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 0) {
//                        this.cells(id,ind).cell.title = 'Click here to sent message';
//                        return false;
//                    }
//                    if(ind == 1) {
//                        this.cells(id,ind).cell.title = 'Click here to edit';
//                        return false;
//                    }
//                });
//              onkeyup='preTally.CashBalanceSheet.applyFilter(this.value);'

                $(".csh_select_filter").change(function () {
                    preTally.CashBalanceSheet.applyFilter();
                });


                $(".cshrpt_text_filter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);

                    filtrInterval = setInterval(function () {
                        preTally.CashBalanceSheet.applyFilter();
                        clearInterval(filtrInterval);
                    }, 500);

                });

//                $( "#text_filter_branch" ).keyup(function(value) {
//                    var mask = this.value;
//                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
//                    
//                    filtrBranchInterval = setInterval( function() { 
//                        preTally.CashBalanceSheet.applyFilterBranch(mask); 
//                        clearInterval(filtrBranchInterval); 
//                    }, 500);
//                   
//                });

                var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_CashOC.php&" + chartCashBSFilterParams), encodeURI(1));
                if (reportIE.xmlDoc.responseText != null) {

                    var reportIE_Response = $.parseJSON(reportIE.xmlDoc.responseText);
//                    dhxCashBSReportsForm.setItemValue("INCOME", reportIE_Response[0].value);
//                    dhxCashBSReportsForm.setItemValue("EXPENSE", reportIE_Response[1].value);

                    $('.tb_rpt_cob').html('OPENING BAL : ' + (reportIE_Response[2].value));
                    $('.tb_rpt_inc').html('INCOME : ' + reportIE_Response[0].value);
                    $('.tb_rpt_exp').html('EXPENSE : ' + reportIE_Response[1].value);
                    $('.tb_rpt_ccb').html('CLOSING BAL : ' + reportIE_Response[3].value);                    
                    $('.tb_rpt_inti').html('INTL RECD : ' + reportIE_Response[4].value); //19-05-25
                    $('.tb_rpt_inte').html('INTL PAID : ' + reportIE_Response[5].value); //19-05-25                    
                    var pl = reportIE_Response[0].value - reportIE_Response[1].value;
                    if (pl < 0) {
                        plColor = '#EE4339';
                        bkColor = '#E06666';
                        plText = 'LOSS';
                        plSign = '-';
                    } else {
                        plColor = '#435F2D';
                        bkColor = '#6AA84F';
                        plText = 'PROFIT';
                        plSign = '';
                    }

//                    $('input[name=PL_Data]').css({'color':plColor});
//                    $('.tb_rpt_pl').css({'background-color':bkColor});

//                    dhxCashBSReportsForm.setItemValue("PL_Data", Math.abs(pl));
                    $('.tb_rpt_pl').html(plText + ' : ' + Math.abs(pl.toFixed(2)));


                    dhxCashBSReportChart.clearAll();
                    dhxCashBSReportChart.parse(reportIE_Response, "json");

                    $('input[name=INCOME]').formatCurrency();
                    $('input[name=EXPENSE]').formatCurrency();
                    $('input[name=PL_Data]').formatCurrency();
                    $('input[name=PL_Data]').val(plText + '   : ' + $('input[name=PL_Data]').val());
                }

                var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_CashOC.php&" + chartCashBSFilterParams), encodeURI(1));
                if (chartIE.xmlDoc.responseText != null) {

                    ptCashBSReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);

                    chartCashBS_Response_OB = $.parseJSON(chartData[0]);
                    chartCashBS_Response_CB = $.parseJSON(chartData[1]);

                    var actvId = ptCashBSRprtsTabbar.getActiveTab();
                    if (actvId == 'a2')
                        preTally.CashBalanceSheet.reLoadSHGrid();
                    if (actvId == 'a3')
                        preTally.CashBalanceSheet.reLoadGraph();
                }


                dhxCashBSReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportCashBSData.php&" + chartCashBSFilterParams), function () {
                    $('.tb_cnt_tot').html("# : " + dhxCashBSReportsGrid.getUserData("", "TL_Count") + " ");
                });


                dhxCashBSReportsGrid.attachEvent("onRowSelect", function (id, ind) {
                    if (ind != 0 && ind != 1 && ind != 8)
                        $(".targetCash_" + id).click();
                });

                setTimeout(function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                }, 1500);
            }
        },
        applyFilter: function () {
//            console.log($('#ieCRF').val()+"--"+$('#itmCRF').val()+"--"+$('#brnCRF').val()+"--"+$('#addCRF').val()+"--"+$( "#shCRF" ).val());
            var amount = $("#amtCRF").val().replace(/,/g, "");  // remove , from amount
            var filterValue = new Array($('#ieCRF').val(), $('#itmCRF').val(), $('#brnCRF').val(), $('#addCRF').val(), $("#shCRF").val(), amount);
            dhxCashBSReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxCashBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportCashBSData.php&" + chartCashBSFilterParams + "&filter=" + filterValue), function () {
                $('.tb_cnt_tot').html("# : " + dhxCashBSReportsGrid.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
//        applyFilterBranch:  function(value){
//            
//                dhxCashBSReportsGrid.clearAll();
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                dhxCashBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportCashBSData.php&"+chartCashBSFilterParams+"&LC_Name="+value), function() {preTally.Settings.progressOff(true, dhxLayout, null);});
//                
//        },
        reLoadGraph: function () {
            var IE_Type = ptCashBSReportsToolbar.getListOptionSelected('ieType');

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
            reportCashBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';
            if (IE_Type === 'IE_I') {
                reportCashBSSubHeadChart.parse(chartCashBS_Response_OB, "json");
                legendGridData = chartCashBS_Response_OB;
            }
            if (IE_Type === 'IE_E') {
                reportCashBSSubHeadChart.parse(chartCashBS_Response_CB, "json");
                legendGridData = chartCashBS_Response_CB;
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
            chartCashBSFilterParams += "&ie=" + ie;
            chartOtherDetailsGrid.setColSorting("na,na,na");
            chartOtherDetailsGrid.enableTooltips("false,false,false");
            chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartCashOCOther.php&" + chartCashBSFilterParams), function () {
                preTally.Settings.progressOff(false, dhxLayout, 'b');
            });

            chartOtherDetailsGrid.attachEvent("onRowSelect", function (id, data) {
                $('#cshrpt_text_filter').val('');
                dhxCashBSReportsGrid.clearAll();

                $("#shCRF").val(chartOtherDetailsGrid.getUserData(id, "shId"));
                preTally.CashBalanceSheet.applyFilter();

            });
        },
        showDetailData: function (inp, BS_Id, SH_Id) {
            preTally.Settings.progressOn(false, dhxCashBSReportDetailLayout, 'c');
            if (!CashBS_DetailDataPop) {
                CashBS_DetailDataPop = new dhtmlXPopup({mode: "left"});

            }
            if (CashBS_DetailDataPop.isVisible()) {
                CashBS_DetailDataPop.hide();
            }

            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;
            var rptDetailsPop = CashBS_DetailDataPop.attachForm();
            //rptDetailsPop.style.backgroundColor="red";
            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_CashBSPop.php&" + params), function () {
                CashBS_DetailDataPop.show(x, y, w, h);
                preTally.Settings.progressOff(false, dhxCashBSReportDetailLayout, 'c');
                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
                column0.style.borderRight = "1px solid #a4bed4";
                //column1.style.paddingLeft = "6px";             
            });

            $('.target_' + BS_Id).unbind();
        },
        hideDetailData: function () {
            if (CashBS_DetailDataPop.isVisible()) {
                CashBS_DetailDataPop.hide();
            }
        },
        exportCashBalanceReport: function () {
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = ptCashBSRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = ptCashBSRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TypeFilter'] = $('#ieCRF').val();
            exportfilterValue['ItemNameFilter'] = $('#itmCRF').val();
            exportfilterValue['BranchFilter'] = $('#brnCRF').val();
            exportfilterValue['AddedByFilter'] = $('#addCRF').val();
            exportfilterValue['r'] = rptCashBSFilterID;
            exportfilterValue['amount'] = $("#amtCRF").val().replace(/,/g, "");  // remove , from amount
            exportfilterValue['report_type'] = "transaction";
            exportfilterValue['shCRF'] = $("#shCRF").val();
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
        sendCorrectionMsg: function (inp, BS_Id, SH_Id) {

            dhxMsgWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            rptMsgWin = dhxMsgWin.createWindow("rptMsgWin_" + BS_Id, x, y, 540, 400);
            rptMsgWin.center();
            rptMsgWin.button("minmax1").hide();
            rptMsgWin.button("minmax2").hide();
            rptMsgWin.button("park").hide();
            rptMsgWin.setModal(true);
            rptMsgWin.setText("Send Correction Message");
            rptMsgForm = rptMsgWin.attachForm();
            preTally.Settings.progressOn(true, rptMsgWin, null);
            rptMsgForm.loadStruct(preTally.Initialize.encryptURL("requisites/sendCorrectionMsg.php"), function () {
                rptMsgForm.setItemValue("US_Id", dhxCashBSReportsGrid.getUserData(BS_Id, "US_Id"));
                rptMsgForm.setItemValue("MSG_To", dhxCashBSReportsGrid.getUserData(BS_Id, "US_Name"));
                rptMsgForm.setItemValue("Entry", dhxCashBSReportsGrid.getUserData(BS_Id, "Entry"));
                rptMsgForm.setItemValue("BS_Id", BS_Id);
                rptMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptMsgWin, null);
                rptMsgForm.attachEvent("onButtonClick", function (name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptMsgWin, null);
                            param = "type=cash";
                            rptMsgForm.send(preTally.Initialize.encryptURL("warehouse/sendCorrectionMsg.php&" + param), function (loader, response) {
                                preTally.Settings.progressOff(true, rptMsgWin, null);
                                if (response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    rptMsgForm.clear();
                                    dhxMsgWin.window("rptMsgWin_" + BS_Id).close();
                                } else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if (name == 'addRecpt') {
                        dhxUserWin = new dhtmlXWindows();
                        recpWin = dhxUserWin.createWindow("addRecpWin", x, y, 1050, 350);
                        recpWin.button("minmax1").hide();
                        recpWin.button("minmax2").hide();
                        recpWin.button("park").hide();
                        recpWin.center();
                        recpWin.setModal(true);
                        recpWin.setText("Add Recipients");
                        listUserGrid = recpWin.attachGrid();
                        listUserGrid.enableAutoWidth(true);
                        preTally.Settings.progressOn(true, recpWin, null);

                        var listRecipientsXML = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked=" + rptMsgForm.getItemValue('US_Id'));
                        listUserGrid.parse(listRecipientsXML.xmlDoc.responseText, function () {
                            preTally.Settings.progressOff(true, recpWin, null);
                        });

                        recpWin.attachStatusBar({
                            text: "<input type='button' value='SAVE' onclick='preTally.CashBalanceSheet.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });

                    } else {
                        dhxMsgWin.window("rptMsgWin_" + BS_Id).close();
//                        rptMsgForm.resetValidateCss();
//                        rptMsgForm.clear();
                    }
                });
            });
        },
        setSensWindow: function (inp, k) {
            if (k == "min") {
                window_Calendar.setSensitiveRange(inp.value, null);
            } else {
                window_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        addRecipients: function () {
            var userDetails = [];
            var userIds = [];

            listUserGrid.forEachRow(function (rId) {
                if (listUserGrid.cells(rId, 6).getValue() == 1) { // if checked
                    userDetails.push(listUserGrid.cells(rId, 2).getValue());
                    userIds.push(rId);
                }
            });

            rptMsgForm.setItemValue("MSG_To", userDetails);
            rptMsgForm.setItemValue('US_Id', userIds);
            dhxUserWin.window("addRecpWin").close();

        },
        editBalSheetDetails: function (inp, BS_Id, SH_Id) {

//            if(SH_Id == 58 || SH_Id == 59){
//                dhtmlx.alert({
//                    title: "Alert!!!",
//                    text : "Waiting for admin Approval.Contact your administrator for further information.",
////                    callback: function(response) {
////
////                    }
//                });
//            }else{
            dhxBSDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            //            var w = inp.offsetWidth;
            //            var h = inp.offsetHeight; 
            bsDetailsWin = dhxBSDetails.createWindow("bsDetailsWin_" + BS_Id, x, y, 850, 400);
            bsDetailsWin.center();
            bsDetailsWin.button("minmax1").hide();
            bsDetailsWin.button("minmax2").hide();
            bsDetailsWin.button("park").hide();
            bsDetailsWin.setModal(true);
            bsDetailsWin.setText("Edit Details");
            bsDetailsForm = bsDetailsWin.attachForm();
            preTally.Settings.progressOn(true, bsDetailsWin, null);

            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id;
            bsDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportCashBSDetailsEdit.php&" + params), function () {

                preTally.Settings.progressOff(true, bsDetailsWin, null);

                var IT_Note;
                var DS_Note;

                var BS_MHType = dhxCashBSReportsGrid.getUserData(BS_Id, "MH_Type");
                if (BS_MHType == 1) {
                    IT_Note = 'NAME OF THE INCOME';
                    DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                } else {
                    IT_Note = 'NAME OF THE EXPENSE';
                    DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                }

                bsDetailsForm.setNote('MH_Type', {text: "ENTRY TYPE", width: "100"});
                bsDetailsForm.setNote('IT_Id', {text: IT_Note, width: "100"});
                bsDetailsForm.setNote('BS_Description', {text: DS_Note, width: "200"});
                bsDetailsForm.setNote('TR_Id', {text: "TRACK", width: "100"});

                var BS_MHCombo = bsDetailsForm.getCombo("MH_Type");
                var BS_ITCombo = bsDetailsForm.getCombo("IT_Id");
                var BS_DSCombo = bsDetailsForm.getCombo("BS_Description");
                var BS_TRCombo = bsDetailsForm.getCombo("TR_Id");
                var BS_BNKCombo = bsDetailsForm.getCombo("BNK_Id");
                var BS_BBCombo = bsDetailsForm.getCombo("BB_Id");
                var BS_BACombo = bsDetailsForm.getCombo("BA_Id");
                /*if(bsDetailsForm.isItem("CHQ_Number")) */ var BS_BChqCombo = bsDetailsForm.getCombo("CHQ_Number");


                if (!dhxCashBSReportsGrid.getUserData(BS_Id, "TR_Id")) {
                    bsDetailsForm.hideItem("TR_Id");
                    bsDetailsForm.setRequired("TR_Id", false);
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                }

                BS_ITCombo.enableFilteringMode(true);
                BS_DSCombo.enableFilteringMode(true);

                BS_ITCombo.setOptionWidth(400);
                BS_DSCombo.setOptionWidth(300);


                BS_MHCombo.attachEvent("onClose", function () {
                    BS_ITCombo.clearAll();
                    BS_ITCombo.setComboText("");
                    BS_ITCombo.setComboValue("");
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText("");
                    BS_DSCombo.setComboValue("");

                    BS_MHType = BS_MHCombo.getSelectedValue();
                    var params = "type=" + BS_MHType;
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&" + params));
                    if (BS_MHType == 1) {
                        IT_Note = 'NAME OF THE INCOME';
                        DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                    } else {
                        IT_Note = 'NAME OF THE EXPENSE';
                        DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                    }
                    bsDetailsForm.setNote('IT_Id', {text: IT_Note, width: "100"});
                    bsDetailsForm.setNote('BS_Description', {text: DS_Note, width: "200"});
                    bsDetailsForm.hideItem("TR_Id");
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                    //bsDetailsForm.hideItem("TR_Id");
                });

                BS_ITCombo.attachEvent("onClose", function () {
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText('');
                    var BS_ITId = BS_ITCombo.getSelectedValue();
                    if (!isNaN(BS_ITId) && (BS_ITId != 0)) {
                        var params = "IT_Id=" + BS_ITId;
                        bsDetailsForm.setItemValue("IT_Flag", "0");
                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&" + params)
                        }).done(function (data) {
                            BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&" + params), function () {});
                            BS_TRCombo.setComboValue('0');
                            BS_TRCombo.setComboText('0');
                            if (!bsDetailsForm.isItemHidden("TR_Id")) {
                                bsDetailsForm.hideItem("TR_Id");
                                bsDetailsForm.setRequired("TR_Id", false)
                                BS_TRCombo.setComboValue('0');
                                BS_TRCombo.setComboText('0');
                            }
                            if (data == 1) {
                                var filtrInterval;
                                bsDetailsForm.showItem("TR_Id");
//                                    BS_TRCombo  =   bsDetailsForm.getCombo("TR_Id");
                                BS_TRCombo.setComboValue('');
                                BS_TRCombo.setComboText('');
                                bsDetailsForm.setRequired("TR_Id", true);
                            }
                        });
                    }
                });


                bsDetailsForm.setItemValue("MH_Type", dhxCashBSReportsGrid.getUserData(BS_Id, "MH_Type"));

                if (bsDetailsForm.isItem("IT_Id")) {
                    var params = "type=" + dhxCashBSReportsGrid.getUserData(BS_Id, "MH_Type");
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&" + params), function () {
                        if (bsDetailsForm.getItemValue("IT_Status") != '4' && bsDetailsForm.getItemValue("IT_Status") != '0') {
                            bsDetailsForm.setItemValue("IT_Id", dhxCashBSReportsGrid.getUserData(BS_Id, "IT_Id"));
                        } else {
                            BS_ITCombo.setComboText(dhxCashBSReportsGrid.getUserData(BS_Id, "IT_Name"));
                            bsDetailsForm.setItemValue("IT_Flag", "del_item");
                        }
                    });
                }

                if (bsDetailsForm.isItem("BS_Description")) {
                    var params = "IT_Id=" + dhxCashBSReportsGrid.getUserData(BS_Id, "IT_Id") + "&updateType=rpt&DS_Id=" + dhxCashBSReportsGrid.getUserData(BS_Id, "DS_Id");
                    BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&" + params), function () {
                        bsDetailsForm.setItemValue("BS_Description", dhxCashBSReportsGrid.getUserData(BS_Id, "DS_Id"));
                    });
                }

                if (bsDetailsForm.isItem("TR_Id")) {
                    BS_TRCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/tracks.php"));
                    var params = "mask=" + dhxCashBSReportsGrid.getUserData(BS_Id, "TR_Track");
                    BS_TRCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&" + params), function () {
                        bsDetailsForm.setItemValue("TR_Id", dhxCashBSReportsGrid.getUserData(BS_Id, "TR_Id"));
                    });
                }

                if (bsDetailsForm.isItem("PM_Id")) {
                    bsDetailsForm.hideItem("BS_PaidDate");
                    bsDetailsForm.hideItem("BS_PayType");
                    bsDetailsForm.hideItem("BNK_Id");
                    bsDetailsForm.hideItem("BB_Id");
                    bsDetailsForm.hideItem("BA_Id");
                    bsDetailsForm.hideItem("CHQ_Number");
                    bsDetailsForm.hideItem("BS_Transaction");
                    bsDetailsForm.hideItem("BS_PayersBank");
                    bsDetailsForm.hideItem("BS_PayersChQ");
                    var item_list = new Array("BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id", "CHQ_Number", "BS_PayersChQ", "BS_PayersBank");
                    var patmentMode = bsDetailsForm.getCombo("PM_Id");
                    patmentMode.setComboValue(bsDetailsForm.getUserData("PM_Id", "cId"));

                    if (bsDetailsForm.getUserData("PM_Id", "cId") == '2') {
                        for (i = 0; i <= 10; i++) {
                            bsDetailsForm.showItem(item_list[i]);
                        }
                        bsDetailsForm.setItemValue("PM_Id", bsDetailsForm.getUserData("PM_Id", "cId"));

                        if (bsDetailsForm.isItem("BNK_Id")) {
                            bsDetailsForm.setItemValue("BNK_Id", bsDetailsForm.getUserData("BNK_Id", "cId"));
                        }
                        if (bsDetailsForm.isItem("BB_Id")) {
                            var params = "Bnk_Id=" + bsDetailsForm.getUserData("BNK_Id", "cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function (xml) {
                                var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                bsDetailsForm.setItemValue("BB_Id", BBName);
                                BS_BBCombo.setComboValue(bsDetailsForm.getUserData("BB_Id", "cId"));
                            });

                            //                            BS_BBCombo.addOption([[bsDetailsForm.getUserData("BB_Id","cId"),bsDetailsForm.getUserData("BB_Id","cValue")]]);
                            //                            BS_BBCombo.setComboValue(bsDetailsForm.getUserData("BB_Id","cId"));
                        }
                        if (bsDetailsForm.isItem("BA_Id")) {
                            var params = "BB_Id=" + bsDetailsForm.getUserData("BB_Id", "cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function (xml) {
                                var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                bsDetailsForm.setItemValue("BA_Id", BAName);
                                BS_BACombo.setComboValue(bsDetailsForm.getUserData("BA_Id", "cId"));
                            });
                        }
                        if (bsDetailsForm.isItem("CHQ_Number")) {
                            var params = "BA_Id=" + bsDetailsForm.getUserData("BA_Id", "cId");
                            BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);

                            BS_BChqCombo.addOption([[bsDetailsForm.getUserData("CHQ_Number", "cId"), bsDetailsForm.getUserData("CHQ_Number", "cValue")]]);
                            BS_BChqCombo.setComboValue(bsDetailsForm.getUserData("CHQ_Number", "cId"));
                        }
                    }

                    patmentMode.attachEvent("onChange", function () {
                        var pmMode = patmentMode.getSelectedValue();
                        for (i = 0; i <= 10; i++) {
                            if (pmMode == '1') {
                                bsDetailsForm.hideItem(item_list[i]);
                            } else {
                                bsDetailsForm.showItem(item_list[i]);
                            }
                        }

                    });
                    if (bsDetailsForm.isItem("BS_PayType")) {
                        BS_PayTypeCombo = bsDetailsForm.getCombo("BS_PayType");

                        bsDetailsForm.setItemValue("BS_PayType", bsDetailsForm.getUserData("BS_PayType", "cId"));
                        //                        BS_PayTypeCombo.setComboText(bsDetailsForm.getUserData("BS_PayType","cValue"));
                        //                        BS_PayTypeCombo.setComboValue(bsDetailsForm.getUserData("BS_PayType","cId"));

                        BS_PayTypeCombo.attachEvent("onChange", function () {
                            var pmType = BS_PayTypeCombo.getSelectedValue();
                            if (pmType == '2') {
                                bsDetailsForm.hideItem("BS_Transaction");
                                bsDetailsForm.showItem("CHQ_Number");
                            }
                            if (pmType == '3') {
                                bsDetailsForm.setItemLabel("BS_Transaction", "DD Number");
                                bsDetailsForm.showItem("BS_Transaction");
                                bsDetailsForm.hideItem("CHQ_Number");
                                bsDetailsForm.setValidation('CHQ_Number', 'null');
                            }
                            if (pmType == '4' || pmType == '5' || pmType == '6') {
                                bsDetailsForm.setItemLabel("BS_Transaction", "Transaction Id");
                                bsDetailsForm.showItem("BS_Transaction");
                                bsDetailsForm.hideItem("CHQ_Number");
                                //bsDetailsForm.setValidation('CHQ_Number', 'null'); 
                                bsDetailsForm.clearValidation('CHQ_Number');
                            }
                        });
                    }
                }

                if (bsDetailsForm.isItem("BB_Id")) {

                    BS_BBCombo.readonly(true);
                    BS_BACombo.readonly(true);

                    BS_BNKCombo.attachEvent("onClose", function () {
                        BS_BBCombo.clearAll();
                        BS_BACombo.clearAll();

                        BS_BBCombo.setComboText('');
                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if (bsDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_id = BS_BNKCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BS_BBCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                            });
                        }
                        bsDetailsForm.setItemValue("updateType", "");
                    });

                    BS_BBCombo.attachEvent("onClose", function () {
                        BS_BACombo.clearAll();

                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if (bsDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_acc_id = BS_BBCombo.getSelectedValue();
                        if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                            var params = "BB_Id=" + bnk_acc_id;
                            BS_BACombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                            });
                        }

                        bsDetailsForm.setItemValue("updateType", "");
                    });

                    BS_BACombo.attachEvent("onClose", function () {

                        if (bsDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll()

                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');

                            var BNK_AC_Id = BS_BACombo.getSelectedValue();
                            if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                var params = "BA_Id=" + BNK_AC_Id;
                                BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);

                            }
                        }
                        bsDetailsForm.setItemValue("updateType", "");
                    });

                }
                if (bsDetailsForm.isItem("CHQ_Number")) {
                    BS_BChqCombo.attachEvent("onClose", function () {
                        bsDetailsForm.setItemValue("updateType", "");
                    });
                }
                if (bsDetailsForm.isItem("BS_User")) {

                    UserCombo = bsDetailsForm.getCombo("BS_User");
                    LCCombo = bsDetailsForm.getCombo("BS_PrchsdFor");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function (xml) {
                        var prucForName = LCCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("BS_PrchsdFor", prucForName);
                        LCCombo.setComboValue(bsDetailsForm.getUserData("BS_PrchsdFor", "cId"));
                    });

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var User_Name = UserCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("BS_User", User_Name);
                        UserCombo.setComboValue(bsDetailsForm.getUserData("BS_User", "cId"));
                    });

                    LCCombo.attachEvent("onClose", function () {
                        UserCombo.clearAll();
                        StaffCombo.clearAll();

                        UserCombo.setComboText('');
                        StaffCombo.setComboText('');

                        var BS_Office = LCCombo.getSelectedValue();
                        if ((BS_Office) && (BS_Office != 0)) {
                            var params = "LC_Id=" + BS_Office;
                            UserCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&" + params));
                        }
                    });

                }
                if (bsDetailsForm.isItem("BS_StaffId")) {

                    UserCombo = bsDetailsForm.getCombo("BS_User");
                    StaffCombo = bsDetailsForm.getCombo("BS_StaffId");

                    StaffCombo.readonly(true);

                    UserCombo.attachEvent("onClose", function () {
                        StaffCombo.clearAll();

                        StaffCombo.setComboText('');

                        var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                        if ((BS_User) && (BS_User != 0)) {
                            var params = "Emp_Id=" + BS_User;
                            StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&" + params), function () {
                            });
                        }
                    });

                }
                if (bsDetailsForm.isItem("LC_Id")) {
                    var LC_IdCombo = bsDetailsForm.getCombo("LC_Id");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function (xml) {
                        var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("LC_Id", LC_Name);
                        LC_IdCombo.setComboValue(bsDetailsForm.getUserData("LC_Id", "cId"));
                    });
                }
                if (bsDetailsForm.isItem("BS_IEByLC")) {
                    var ieByLCCombo = bsDetailsForm.getCombo("BS_IEByLC");
                    var ieByLCid = bsDetailsForm.getUserData("BS_IEByLC", "cId");
                    var ieByLCname = bsDetailsForm.getUserData("BS_IEByLC", "cValue");
                    var params = "";
                    if (!ieByLCid || ieByLCid == 0) {
                        params = "&mask=Self";
                    } else {
                        params = "&mask=" + ieByLCname;
                    }

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                        ieByLCCombo.load(xml.xmlDoc.responseText);
                        if (ieByLCid != '')
                            bsDetailsForm.setItemValue("BS_IEByLC", ieByLCid);

                        var ieByUSCombo = bsDetailsForm.getCombo("BS_IEByUS");
                        var ieByUSid = bsDetailsForm.getUserData("BS_IEByUS", "cId");
                        var ieByUSname = bsDetailsForm.getUserData("BS_IEByUS", "cValue");
                        var params = "";
                        if (!ieByUSid) {
                            params = "&ctype=check&mask=Self&LCId=" + ieByLCCombo.getSelectedValue();
                        } else {
                            params = "&ctype=check&mask=" + ieByUSname + "&LCId=" + ieByLCCombo.getSelectedValue();
                        }

                        ieByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php" + params));
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php" + params), function (xml) {
                            ieByUSCombo.load(xml.xmlDoc.responseText);
                            if (ieByUSid != '')
                                bsDetailsForm.setItemValue("BS_IEByUS", ieByUSid);
                        });

                        ieByLCCombo.attachEvent("onChange", function () {
                            ieByUSCombo.clearAll();
                            ieByUSCombo.setComboValue(0);
                            ieByUSCombo.setComboText('');

                            var params = "&LCId=" + ieByLCCombo.getSelectedValue();
                            ieByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php&ctype=check" + params), false);
                        });
                    });
                }
                if (bsDetailsForm.isItem("BS_PaidBy")) {
                    var BS_PaidByCombo = bsDetailsForm.getCombo("BS_PaidBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var paidByUser_Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("BS_PaidBy", paidByUser_Name);
                        BS_PaidByCombo.setComboValue(bsDetailsForm.getUserData("BS_PaidBy", "cId"));
                    });
                }
                if (bsDetailsForm.isItem("BS_PaidTo")) {
                    bsDetailsForm.setItemValue("BS_PaidTo", dhxCashBSReportsGrid.getUserData(BS_Id, "DS_Description"));
                    bsDetailsForm.setReadonly("BS_PaidTo", true);
                }
                if (bsDetailsForm.isItem("BS_PayTime")) {
                    bsDetailsForm.setItemValue("BS_PayTime", bsDetailsForm.getUserData("BS_PayTime", "cId"));
                }
                if (bsDetailsForm.isItem("BS_StaffId")) {
                    var BS_StaffIdCombo = bsDetailsForm.getCombo("BS_StaffId");
                    BS_StaffIdCombo.addOption([[bsDetailsForm.getUserData("BS_StaffId", "cId"), bsDetailsForm.getUserData("BS_StaffId", "cValue")]]);
                    //                    BS_StaffIdCombo.setComboText(bsDetailsForm.getUserData("BS_StaffId","cValue"));
                    BS_StaffIdCombo.setComboValue(bsDetailsForm.getUserData("BS_StaffId", "cId"));
                }
                if (bsDetailsForm.isItem("BS_AprovlGvnBy")) {
                    var BS_AprovlGvnByCombo = bsDetailsForm.getCombo("BS_AprovlGvnBy");
                    //                    BS_AprovlGvnByCombo.setComboText(bsDetailsForm.getUserData("BS_AprovlGvnBy","cValue"));
                    //                    BS_AprovlGvnByCombo.setComboValue(bsDetailsForm.getUserData("BS_AprovlGvnBy","cId"));

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var aprlUser_Name = BS_AprovlGvnByCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("LC_Id", aprlUser_Name);
                        BS_AprovlGvnByCombo.setComboValue(bsDetailsForm.getUserData("BS_AprovlGvnBy", "cId"));
                    });

                }
                if (bsDetailsForm.isItem("BS_AprovlTknBy")) {
                    var BS_AprovlTknByCombo = bsDetailsForm.getCombo("BS_AprovlTknBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var aprlTknUser_Name = BS_AprovlTknByCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("BS_AprovlTknBy", aprlTknUser_Name);
                        BS_AprovlTknByCombo.setComboValue(bsDetailsForm.getUserData("BS_AprovlTknBy", "cId"));
                    });

                    //                    BS_AprovlTknByCombo.setComboText(bsDetailsForm.getUserData("BS_AprovlTknBy","cValue"));
                    //                    BS_AprovlTknByCombo.setComboValue(bsDetailsForm.getUserData("BS_AprovlTknBy","cId"));
                }
                if (bsDetailsForm.isItem("BS_AprovdDate")) {
                    bsDetailsForm.setItemValue(bsDetailsForm.getUserData("BS_AprovdDate", "cValue"));
                    //BS_PayTypeCombo.setComboValue(bsDetailsForm.getUserData("BS_PayType","cId"));
                }
                if (bsDetailsForm.isItem("BS_RcvdFrm")) {
//                        bsDetailsForm.setItemValue("BS_RcvdFrm",dhxCashBSReportsGrid.getUserData(BS_Id, "DS_Description"));
                    bsDetailsForm.setReadonly("BS_RcvdFrm", true);
                }
                if (bsDetailsForm.isItem("BS_RcvdBy")) {
                    var BS_RcvdByCombo = bsDetailsForm.getCombo("BS_RcvdBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                        bsDetailsForm.setItemValue("BS_RcvdBy", rcvdByUser_Name);
                        BS_RcvdByCombo.setComboValue(bsDetailsForm.getUserData("BS_RcvdBy", "cId"));
                    });
                }

                if (bsDetailsForm.isItem("BS_PettyCashRefId")) {
                    bsDetailsForm.setItemValue("BS_PettyCashRefId", bsDetailsForm.getUserData("BS_PettyCashRefId", "cId"));
                    var BS_PCRefidCombo = bsDetailsForm.getCombo("BS_PettyCashRefId");

                    BS_PCRefidCombo.attachEvent("onXLE", function () {
                        BS_PCRefidCombo.deleteOption(BS_Id);
                    });

                    BS_PCRefidCombo.setOptionWidth(350);

                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + BS_Id + "&PCRefId=" + bsDetailsForm.getUserData("BS_PettyCashRefId", "cId"))
                    }).done(function (data) {
                        bsDetailsForm.setItemValue("PettyCashAmount", data);
                    });

                    BS_PCRefidCombo.attachEvent("onClose", function () {

                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + BS_Id + "&PCRefId=" + BS_PCRefidCombo.getSelectedValue())
                        }).done(function (data) {
                            bsDetailsForm.setItemValue("PettyCashAmount", data);
                        });

                    });
                }

                bsDetailsForm.attachEvent("onButtonClick", function (name) {

                    if (name == "balSheetDetailsSave") {

//                            var messageValidate = bsDetailsForm.validate(); 
                        var values = bsDetailsForm.getFormData();
                        var CstmValidate = preTally.Validate.Validate(values, 'BS_Amount', bsDetailsForm, 'decimal');

                        if (CstmValidate == false) {
                            bsDetailsForm.setValidateCss('BS_Amount', CstmValidate, 'validate_red');
                            return false;
                        }

//                            bsDetailsForm.setValidation('BS_Amount', 'ValidInteger,NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$');
                        if (bsDetailsForm.isItem("BS_Price"))
                            bsDetailsForm.setValidation('BS_Price', 'ValidNumeric');
                        if (bsDetailsForm.isItem("BS_Quantity"))
                            bsDetailsForm.setValidation('BS_Quantity', 'ValidNumeric');
                        if (bsDetailsForm.isItem("BS_StaffId"))
                            bsDetailsForm.setValidation('BS_StaffId', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_PrchsdFor"))
                            bsDetailsForm.setValidation('BS_PrchsdFor', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_User"))
                            bsDetailsForm.setValidation('BS_User', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_AprovlGvnBy"))
                            bsDetailsForm.setValidation('BS_AprovlGvnBy', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_AprovlTknBy"))
                            bsDetailsForm.setValidation('BS_AprovlTknBy', 'ValidInteger');
                        if (bsDetailsForm.isItem("CN_Id"))
                            bsDetailsForm.setValidation('CN_Id', 'ValidInteger');
                        if (bsDetailsForm.isItem("ST_Id"))
                            bsDetailsForm.setValidation('ST_Id', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_Persons"))
                            bsDetailsForm.setValidation('BS_Persons', 'ValidInteger');
                        if (bsDetailsForm.isItem("LC_Id"))
                            bsDetailsForm.setValidation('LC_Id', 'ValidInteger');
                        if (bsDetailsForm.isItem("BS_PaidBy"))
                            bsDetailsForm.setValidation('BS_PaidBy', 'ValidInteger');
                        //if (bsDetailsForm.isItem("CHQ_Number")) bsDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                        if (bsDetailsForm.isItem("IT_Id"))
                            bsDetailsForm.setValidation('IT_Id', 'ValidInteger,NotEmpty');
                        if (bsDetailsForm.isItem("BS_Description"))
                            bsDetailsForm.setValidation('BS_Description', 'NotEmpty');
                        if (!bsDetailsForm.isItemHidden("TR_Id")) {
                            bsDetailsForm.setValidation('TR_Id', 'ValidInteger,NotEmpty');
                        } else {
                            bsDetailsForm.clearValidation('TR_Id');
                        }
                        bsDetailsForm.setValidation('BS_IEByUS', 'ValidInteger,NotEmpty');
                        bsDetailsForm.setValidation('BS_IEByLC', 'ValidInteger,NotEmpty');

                        if (bsDetailsForm.isItem("PM_Id")) {
                            var patmentMode = bsDetailsForm.getCombo("PM_Id");
                            var pmMode = patmentMode.getSelectedValue();
                            if (pmMode == 2) {
                                //                            bsDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
                                //                            bsDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
                                bsDetailsForm.setValidation('BNK_Id', 'NotEmpty');
                                bsDetailsForm.setValidation('BB_Id', 'NotEmpty');
                                bsDetailsForm.setValidation('BA_Id', 'NotEmpty');
                                bsDetailsForm.setValidation('BS_PayersBank', 'NotEmpty');
                                bsDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty');

                                if (bsDetailsForm.isItem("BS_PayType")) {
                                    var paymentType = bsDetailsForm.getCombo("BS_PayType");
                                    var pmType = paymentType.getSelectedValue();
                                    if (pmType == '2') {
                                        bsDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                                    } else if (pmType == '3' || pmType == '4' || pmType == '5') {
                                        bsDetailsForm.setItemValue("CHQ_Number", "");
                                        bsDetailsForm.setValidation('CHQ_Number', 'null');
                                    }
                                }
                            } else {
                                bsDetailsForm.setItemValue("BNK_Id", "");
                                bsDetailsForm.setItemValue("BB_Id", "");
                                bsDetailsForm.setItemValue("BB_Id", "");
                                bsDetailsForm.setItemValue("BA_Id", "");
                                bsDetailsForm.setItemValue("CHQ_Number", "");
                                bsDetailsForm.setItemValue("BS_PayersBank", "");
                                bsDetailsForm.setItemValue("BS_PayersChQ", "");

//                                    bsDetailsForm.setValidation('BNK_Id', 'null'); 
//                                    bsDetailsForm.setValidation('BB_Id', 'null'); 
//                                    bsDetailsForm.setValidation('BB_Id', 'null');
//                                    bsDetailsForm.setValidation('BA_Id', 'null');
//                                    bsDetailsForm.setValidation('CHQ_Number', 'null');
//                                    bsDetailsForm.setValidation('BS_PayersBank', 'null'); 
//                                    bsDetailsForm.setValidation('BS_PayersChQ', 'null'); 

                                bsDetailsForm.clearValidation('BNK_Id');
                                bsDetailsForm.clearValidation('BB_Id');
                                bsDetailsForm.clearValidation('BB_Id');
                                bsDetailsForm.clearValidation('BA_Id');
                                bsDetailsForm.clearValidation('CHQ_Number');
                                bsDetailsForm.clearValidation('BS_PayersBank');
                                bsDetailsForm.clearValidation('BS_PayersChQ');
                            }
                        }

                        bsDetailsForm.attachEvent("onValidateError", function (name, value, result) {
                            bsDetailsForm.setValidateCss(name, false, 'validate_red');
                            return false;
                        });

                        if (bsDetailsForm.getItemValue("IT_Flag") == "del_item") {
                            bsDetailsForm.setValidateCss('IT_Id', false);
                            dhtmlx.message({text: 'Selected Item is deleted.Please Verify'});
                            return false;
                        }
                        if (bsDetailsForm.getItemValue("BS_PettyCashRefId") != 0) {
                            if (Number(bsDetailsForm.getItemValue("BS_Amount")) > Number(bsDetailsForm.getItemValue("PettyCashAmount"))) {
                                dhtmlx.message({text: ' Amount exceeds petty cash Amount. Please Verify'});
                                bsDetailsForm.setValidateCss('BS_Amount', false);
                                return false;
                            }
                        }

                        var messageValidate = bsDetailsForm.validate();
                        if (messageValidate && CstmValidate) {
                            preTally.Settings.progressOn(true, bsDetailsWin, null);
                            var params = "bsId=" + BS_Id + "&TR_Track=" + BS_TRCombo.getSelectedText();
                            bsDetailsForm.send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&' + params), function (loader, response) {
                                preTally.Settings.progressOff(true, bsDetailsWin, null);
                                if (response != 1 && response != 2 && response != 'pettycash') {
                                    bsDetailsForm.clear();
                                    dhxBSDetails.window("bsDetailsWin_" + BS_Id).close();

                                    var filterValue = new Array($('#ieCRF').val(), $('#itmCRF').val(), $('#brnCRF').val(), $('#addCRF').val(), $("#shCRF").val());
                                    dhxCashBSReportsGrid.clearAll();
                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                    dhxCashBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportCashBSData.php&" + chartCashBSFilterParams + "&filter=" + filterValue), function () {
                                        $('.tb_cnt_tot').html("# : " + dhxCashBSReportsGrid.getUserData("", "TL_Count") + " ");
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                    });
                                    //dhxCashBSReportsGrid.clearAndLoad("requisites/reportCashBSData.php?"+encrypt(chartCashBSFilterParams), function() { });
                                } else if (response == 'pettycash') {
                                    bsDetailsForm.setValidateCss('BS_Amount', false);
                                    response = 'Petty Cash Amount is incorrect. Please Verify.';
                                } else if (response == '2') {
                                    response = 'Edit is restricted for this entry.';
                                } else {
                                    response = 'Invalid Entry. Please Verify.';
                                }
                                dhtmlx.message({text: response});
                            });
                        }
                    } else {
                        dhxBSDetails.window("bsDetailsWin_" + BS_Id).close();
                    }
                });
            });
//            }
        },
        editBalSheetDetailsFromMsg: function (inp, BS_Id, SH_Id) {
            dhxBSMsgDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);

            bsMsgDetailsWin = dhxBSMsgDetails.createWindow("bsDetailsWinMsg_" + BS_Id, x, y, 900, 400);
            bsMsgDetailsWin.center();
            bsMsgDetailsWin.button("minmax1").hide();
            bsMsgDetailsWin.button("minmax2").hide();
            bsMsgDetailsWin.button("park").hide();
            bsMsgDetailsWin.setModal(true);
            bsMsgDetailsWin.setText("Edit Details");
            bsMsgDetailsForm = bsMsgDetailsWin.attachForm();
            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);

            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id;
            bsMsgDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportCashBSEditMsg.php&" + params), function () {

                preTally.Settings.progressOff(true, bsMsgDetailsWin, null);
                var IT_Note;
                var DS_Note;
                var BS_MHType = bsMsgDetailsForm.getItemValue("MH_Type");

                if (BS_MHType == 1) {
                    IT_Note = 'NAME OF THE INCOME';
                    DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                } else {
                    IT_Note = 'NAME OF THE EXPENSE';
                    DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                }

                bsMsgDetailsForm.setNote('MH_Type', {text: "ENTRY TYPE", width: "100"});
                bsMsgDetailsForm.setNote('IT_Id', {text: IT_Note, width: "100"});
                bsMsgDetailsForm.setNote('BS_Description', {text: DS_Note, width: "200"});
                bsMsgDetailsForm.setNote('TR_Id', {text: "TRACK", width: "100"});

                var BS_MHCombo = bsMsgDetailsForm.getCombo("MH_Type");
                var BS_ITCombo = bsMsgDetailsForm.getCombo("IT_Id");
                var BS_DSCombo = bsMsgDetailsForm.getCombo("BS_Description");
                var BS_TRCombo = bsMsgDetailsForm.getCombo("TR_Id");
                var BS_BNKCombo = bsMsgDetailsForm.getCombo("BNK_Id");
                var BS_BBCombo = bsMsgDetailsForm.getCombo("BB_Id");
                var BS_BACombo = bsMsgDetailsForm.getCombo("BA_Id");
                var BS_BChqCombo = bsMsgDetailsForm.getCombo("CHQ_Number");

                if (bsMsgDetailsForm.getItemValue("HiddenTR_Id") == 0) {
                    bsMsgDetailsForm.hideItem("TR_Id");
                    bsMsgDetailsForm.setRequired("TR_Id", false);
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                }

                BS_ITCombo.enableFilteringMode(true);
                BS_DSCombo.enableFilteringMode(true);

                BS_ITCombo.setOptionWidth(400);
                BS_DSCombo.setOptionWidth(300);

                BS_MHCombo.attachEvent("onClose", function () {
                    BS_ITCombo.clearAll();
                    BS_ITCombo.setComboText("");
                    BS_ITCombo.setComboValue("");
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText("");
                    BS_DSCombo.setComboValue("");

                    BS_MHType = BS_MHCombo.getSelectedValue();
                    var params = "type=" + BS_MHType;
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&" + params));
                    if (BS_MHType == 1) {
                        IT_Note = 'NAME OF THE INCOME';
                        DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                    } else {
                        IT_Note = 'NAME OF THE EXPENSE';
                        DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                    }
                    bsMsgDetailsForm.setNote('IT_Id', {text: IT_Note, width: "100"});
                    bsMsgDetailsForm.setNote('BS_Description', {text: DS_Note, width: "200"});
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                });

                BS_ITCombo.attachEvent("onClose", function () {
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText('');
                    var BS_ITId = BS_ITCombo.getSelectedValue();
                    if (!isNaN(BS_ITId) && (BS_ITId != 0)) {
                        bsMsgDetailsForm.setItemValue("IT_Flag", "0");
                        var params = "IT_Id=" + BS_ITId;
                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&" + params)
                        }).done(function (data) {
                            BS_TRCombo.setComboValue('0');
                            BS_TRCombo.setComboText('0');
                            BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&" + params), function () {});
                            if (!bsMsgDetailsForm.isItemHidden("TR_Id")) {
                                bsMsgDetailsForm.hideItem("TR_Id");
                                BS_TRCombo.setComboValue('0');
                                BS_TRCombo.setComboText('0');
                                bsMsgDetailsForm.setRequired("TR_Id", false)
                            }
                            if (data == 1) {
                                var filtrInterval;
                                bsMsgDetailsForm.showItem("TR_Id");
//                                    BS_TRCombo  =   bsMsgDetailsForm.getCombo("TR_Id");
                                BS_TRCombo.setComboValue('');
                                BS_TRCombo.setComboText('');
                                bsMsgDetailsForm.setRequired("TR_Id", true);
                            }
                        });
                    }
                });

                bsMsgDetailsForm.setItemValue("MH_Type", bsMsgDetailsForm.getItemValue("HiddenMH_Type"));
                if (bsMsgDetailsForm.isItem("IT_Id")) {

                    var params = "type=" + bsMsgDetailsForm.getItemValue("MH_Type") + "&ITId=" + bsMsgDetailsForm.getUserData(BS_Id, "IT_Id");
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&" + params), function () {
                        if (bsMsgDetailsForm.getItemValue("IT_Status") != '4') {
                            bsMsgDetailsForm.setItemValue("IT_Id", bsMsgDetailsForm.getItemValue("HiddenIT_Id"));
                        } else {
                            BS_ITCombo.setComboText(bsMsgDetailsForm.getItemValue("IT_Name"));
                            bsMsgDetailsForm.setItemValue("IT_Flag", "del_item");
                        }
                    });

                }

                if (bsMsgDetailsForm.isItem("BS_Description")) {
                    var params = "IT_Id=" + bsMsgDetailsForm.getItemValue("HiddenIT_Id") + "&DS_Id=" + bsMsgDetailsForm.getItemValue("DS_Id") + "&updateType=rpt";
                    BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&" + params), function () {
                        bsMsgDetailsForm.setItemValue("BS_Description", bsMsgDetailsForm.getItemValue("DS_Id"));
                    });
                }
                if (bsMsgDetailsForm.isItem("TR_Id")) {
                    BS_TRCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/tracks.php"));
                    var params = "mask=" + bsMsgDetailsForm.getItemValue("TR_Track");
                    BS_TRCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&" + params), function () {
                        bsMsgDetailsForm.setItemValue("TR_Id", bsMsgDetailsForm.getItemValue("HiddenTR_Id"));
                    });
                }
                if (bsMsgDetailsForm.isItem("PM_Id")) {
                    bsMsgDetailsForm.hideItem("BS_PaidDate");
                    bsMsgDetailsForm.hideItem("BS_PayType");
                    bsMsgDetailsForm.hideItem("BNK_Id");
                    bsMsgDetailsForm.hideItem("BB_Id");
                    bsMsgDetailsForm.hideItem("BA_Id");
                    bsMsgDetailsForm.hideItem("CHQ_Number");
                    bsMsgDetailsForm.hideItem("BS_Transaction");
                    bsMsgDetailsForm.hideItem("BS_PayersBank");
                    bsMsgDetailsForm.hideItem("BS_PayersChQ");
                    var item_list = new Array("BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id", "CHQ_Number", "BS_PayersChQ", "BS_PayersBank");
                    var patmentMode = bsMsgDetailsForm.getCombo("PM_Id");
                    patmentMode.setComboValue(bsMsgDetailsForm.getUserData("PM_Id", "cId"));

                    if (bsMsgDetailsForm.getUserData("PM_Id", "cId") == '2') {
                        for (i = 0; i <= 10; i++) {
                            bsMsgDetailsForm.showItem(item_list[i]);
                        }
                        bsMsgDetailsForm.setItemValue("PM_Id", bsMsgDetailsForm.getUserData("PM_Id", "cId"));

                        if (bsMsgDetailsForm.isItem("BNK_Id")) {
                            bsMsgDetailsForm.setItemValue("BNK_Id", bsMsgDetailsForm.getUserData("BNK_Id", "cId"));
                        }
                        if (bsMsgDetailsForm.isItem("BB_Id")) {
                            var params = "Bnk_Id=" + bsMsgDetailsForm.getUserData("BNK_Id", "cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function (xml) {
                                var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                bsMsgDetailsForm.setItemValue("BB_Id", BBName);
                                BS_BBCombo.setComboValue(bsMsgDetailsForm.getUserData("BB_Id", "cId"));
                            });
                        }
                        if (bsMsgDetailsForm.isItem("BA_Id")) {
                            var params = "BB_Id=" + bsMsgDetailsForm.getUserData("BB_Id", "cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function (xml) {
                                var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                bsMsgDetailsForm.setItemValue("BA_Id", BAName);
                                BS_BACombo.setComboValue(bsMsgDetailsForm.getUserData("BA_Id", "cId"));
                            });
                        }
                        if (bsMsgDetailsForm.isItem("CHQ_Number")) {
                            var params = "BA_Id=" + bsMsgDetailsForm.getUserData("BA_Id", "cId");
                            BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);

                            BS_BChqCombo.addOption([[bsMsgDetailsForm.getUserData("CHQ_Number", "cId"), bsMsgDetailsForm.getUserData("CHQ_Number", "cValue")]]);
                            BS_BChqCombo.setComboValue(bsMsgDetailsForm.getUserData("CHQ_Number", "cId"));
                        }
                    }

                    patmentMode.attachEvent("onChange", function () {
                        var pmMode = patmentMode.getSelectedValue();
                        for (i = 0; i <= 10; i++) {
                            if (pmMode == '1') {
                                bsMsgDetailsForm.hideItem(item_list[i]);
                            } else {
                                bsMsgDetailsForm.showItem(item_list[i]);
                            }
                        }

                    });
                    if (bsMsgDetailsForm.isItem("BS_PayType")) {
                        BS_PayTypeCombo = bsMsgDetailsForm.getCombo("BS_PayType");
                        bsMsgDetailsForm.setItemValue("BS_PayType", bsMsgDetailsForm.getUserData("BS_PayType", "cId"));

                        BS_PayTypeCombo.attachEvent("onChange", function () {
                            var pmType = BS_PayTypeCombo.getSelectedValue();
                            if (pmType == '2') {
                                bsMsgDetailsForm.hideItem("BS_Transaction");
                                bsMsgDetailsForm.showItem("CHQ_Number");
                            }
                            if (pmType == '3') {
                                bsMsgDetailsForm.setItemLabel("BS_Transaction", "DD Number");
                                bsMsgDetailsForm.showItem("BS_Transaction");
                                bsMsgDetailsForm.hideItem("CHQ_Number");
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null');
                            }
                            if (pmType == '4' || pmType == '5' || pmType == '6') {
                                bsMsgDetailsForm.setItemLabel("BS_Transaction", "Transaction Id");
                                bsMsgDetailsForm.showItem("BS_Transaction");
                                bsMsgDetailsForm.hideItem("CHQ_Number");
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null');
                            }
                        });
                    }
                }

                if (bsMsgDetailsForm.isItem("BB_Id")) {

                    BS_BBCombo.readonly(true);
                    BS_BACombo.readonly(true);

                    BS_BNKCombo.attachEvent("onClose", function () {
                        BS_BBCombo.clearAll();
                        BS_BACombo.clearAll();

                        BS_BBCombo.setComboText('');
                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if (bsMsgDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_id = BS_BNKCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BS_BBCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                            });
                        }
                        bsMsgDetailsForm.setItemValue("updateType", "");
                    });

                    BS_BBCombo.attachEvent("onClose", function () {
                        BS_BACombo.clearAll();

                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if (bsMsgDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_acc_id = BS_BBCombo.getSelectedValue();
                        if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                            var params = "BB_Id=" + bnk_acc_id;
                            BS_BACombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                            });
                        }
                        bsMsgDetailsForm.setItemValue("updateType", "");
                    });

                    BS_BACombo.attachEvent("onClose", function () {

                        if (bsMsgDetailsForm.isItem("CHQ_Number")) {
                            BS_BChqCombo.clearAll()

                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');

                            var BNK_AC_Id = BS_BACombo.getSelectedValue();
                            if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                var params = "BA_Id=" + BNK_AC_Id;
                                BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);
                            }
                        }
                        bsMsgDetailsForm.setItemValue("updateType", "");
                    });
                }
                if (bsMsgDetailsForm.isItem("CHQ_Number")) {
                    BS_BChqCombo.attachEvent("onClose", function () {
                        bsMsgDetailsForm.setItemValue("updateType", "");
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_User")) {

                    UserCombo = bsMsgDetailsForm.getCombo("BS_User");
                    LCCombo = bsMsgDetailsForm.getCombo("BS_PrchsdFor");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function (xml) {
                        var prucForName = LCCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_PrchsdFor", prucForName);
                        LCCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_PrchsdFor", "cId"));
                    });

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var User_Name = UserCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_User", User_Name);
                        UserCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_User", "cId"));
                    });

                    LCCombo.attachEvent("onClose", function () {
                        UserCombo.clearAll();
                        StaffCombo.clearAll();

                        UserCombo.setComboText('');
                        StaffCombo.setComboText('');

                        var BS_Office = LCCombo.getSelectedValue();
                        if ((BS_Office) && (BS_Office != 0)) {
                            var params = "LC_Id=" + BS_Office;
                            UserCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&" + params));
                        }
                    });

                }
                if (bsMsgDetailsForm.isItem("BS_StaffId")) {
                    UserCombo = bsMsgDetailsForm.getCombo("BS_User");
                    StaffCombo = bsMsgDetailsForm.getCombo("BS_StaffId");
                    StaffCombo.readonly(true);

                    UserCombo.attachEvent("onClose", function () {
                        StaffCombo.clearAll();

                        StaffCombo.setComboText('');

                        var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                        if ((BS_User) && (BS_User != 0)) {
                            var params = "Emp_Id=" + BS_User;
                            StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&" + params), function () {
                            });
                        }
                    });

                }
                if (bsMsgDetailsForm.isItem("LC_Id")) {
                    var LC_IdCombo = bsMsgDetailsForm.getCombo("LC_Id");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function (xml) {
                        var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("LC_Id", LC_Name);
                        LC_IdCombo.setComboValue(bsMsgDetailsForm.getUserData("LC_Id", "cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_IEByLC")) {
                    var ieByLCCombo = bsMsgDetailsForm.getCombo("BS_IEByLC");
                    var ieByLCid = bsMsgDetailsForm.getUserData("BS_IEByLC", "cId");
                    var ieByLCname = bsMsgDetailsForm.getUserData("BS_IEByLC", "cValue");
                    var params = "";
                    if (!ieByLCid) {
                        params = "&mask=Self";
                    } else {
                        params = "&mask=" + ieByLCname;
                    }

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                        ieByLCCombo.load(xml.xmlDoc.responseText);
                        if (ieByLCid != '')
                            bsMsgDetailsForm.setItemValue("BS_IEByLC", ieByLCid);
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_IEByUS")) {
                    var ieByUSCombo = bsMsgDetailsForm.getCombo("BS_IEByUS");
                    var ieByUSid = bsMsgDetailsForm.getUserData("BS_IEByUS", "cId");
                    var ieByUSname = bsMsgDetailsForm.getUserData("BS_IEByUS", "cValue");
                    var params = "";
                    if (!ieByUSid) {
                        params = "&mask=Self";
                    } else {
                        params = "&mask=" + ieByUSname;
                    }
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php" + params), function (xml) {
                        ieByUSCombo.load(xml.xmlDoc.responseText);
                        if (ieByUSid != '')
                            bsMsgDetailsForm.setItemValue("BS_IEByUS", ieByUSid);
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_PaidBy")) {
                    var BS_PaidByCombo = bsMsgDetailsForm.getCombo("BS_PaidBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var paidByUser_Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_PaidBy", paidByUser_Name);
                        BS_PaidByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_PaidBy", "cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_PaidTo")) {
                    bsMsgDetailsForm.setItemValue("BS_PaidTo", bsMsgDetailsForm.getItemValue("DS_Description"));
                    bsMsgDetailsForm.setReadonly("BS_PaidTo", true);
                }
                if (bsMsgDetailsForm.isItem("BS_PayTime")) {
                    bsMsgDetailsForm.setItemValue("BS_PayTime", bsMsgDetailsForm.getItemValue("BS_PayTime"));
                }
                if (bsMsgDetailsForm.isItem("BS_StaffId")) {
                    var BS_StaffIdCombo = bsMsgDetailsForm.getCombo("BS_StaffId");
                    BS_StaffIdCombo.addOption([[bsMsgDetailsForm.getUserData("BS_StaffId", "cId"), bsMsgDetailsForm.getUserData("BS_StaffId", "cValue")]]);
                    BS_StaffIdCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_StaffId", "cId"));
                }
                if (bsMsgDetailsForm.isItem("BS_AprovlGvnBy")) {
                    var BS_AprovlGvnByCombo = bsMsgDetailsForm.getCombo("BS_AprovlGvnBy");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var aprlUser_Name = BS_AprovlGvnByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("LC_Id", aprlUser_Name);
                        BS_AprovlGvnByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_AprovlGvnBy", "cId"));
                    });

                }
                if (bsMsgDetailsForm.isItem("BS_AprovlTknBy")) {
                    var BS_AprovlTknByCombo = bsMsgDetailsForm.getCombo("BS_AprovlTknBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var aprlTknUser_Name = BS_AprovlTknByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_AprovlTknBy", aprlTknUser_Name);
                        BS_AprovlTknByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_AprovlTknBy", "cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_AprovdDate")) {
                    bsMsgDetailsForm.setItemValue(bsMsgDetailsForm.getUserData("BS_AprovdDate", "cValue"));
                }
                if (bsMsgDetailsForm.isItem("BS_RcvdFrm")) {
                    bsMsgDetailsForm.setReadonly("BS_RcvdFrm", true);
                }
                if (bsMsgDetailsForm.isItem("BS_RcvdBy")) {
                    var BS_RcvdByCombo = bsMsgDetailsForm.getCombo("BS_RcvdBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function (xml) {
                        var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_RcvdBy", rcvdByUser_Name);
                        BS_RcvdByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_RcvdBy", "cId"));
                    });
                }

                if (bsMsgDetailsForm.isItem("BS_PettyCashRefId")) {
                    bsMsgDetailsForm.setItemValue("BS_PettyCashRefId", bsMsgDetailsForm.getUserData("BS_PettyCashRefId", "cId"));
                    var BS_PCRefidCombo = bsMsgDetailsForm.getCombo("BS_PettyCashRefId");
                    BS_PCRefidCombo.setOptionWidth(350);

                    BS_PCRefidCombo.attachEvent("onXLE", function () {
                        BS_PCRefidCombo.deleteOption(BS_Id);
                    });

                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + BS_Id + "&PCRefId=" + bsMsgDetailsForm.getUserData("BS_PettyCashRefId", "cId"))
                    }).done(function (data) {
                        bsMsgDetailsForm.setItemValue("PettyCashAmount", data);
                    });
                    BS_PCRefidCombo.attachEvent("onClose", function () {

                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + BS_Id + "&PCRefId=" + BS_PCRefidCombo.getSelectedValue())
                        }).done(function (data) {
                            bsMsgDetailsForm.setItemValue("PettyCashAmount", data);
                        });

                    });
                }

                bsMsgDetailsForm.attachEvent("onButtonClick", function (name) {

                    if (name == "balSheetDetailsSave") {

                        var messageValidate = bsMsgDetailsForm.validate();
                        var values = bsMsgDetailsForm.getFormData();
                        var CstmValidate = preTally.Validate.Validate(values, 'BS_Amount', bsMsgDetailsForm, 'decimal');

                        if (CstmValidate == false) {
                            bsMsgDetailsForm.setValidateCss('BS_Amount', CstmValidate, 'validate_red');
                            return false;
                        }

//                        bsMsgDetailsForm.setValidation('BS_Amount', 'ValidInteger,NotEmpty');
                        if (bsMsgDetailsForm.isItem("BS_Price"))
                            bsMsgDetailsForm.setValidation('BS_Price', 'ValidNumeric');
                        if (bsMsgDetailsForm.isItem("BS_Quantity"))
                            bsMsgDetailsForm.setValidation('BS_Quantity', 'ValidNumeric');
                        if (bsMsgDetailsForm.isItem("BS_StaffId"))
                            bsMsgDetailsForm.setValidation('BS_StaffId', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_PrchsdFor"))
                            bsMsgDetailsForm.setValidation('BS_PrchsdFor', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_User"))
                            bsMsgDetailsForm.setValidation('BS_User', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_AprovlGvnBy"))
                            bsMsgDetailsForm.setValidation('BS_AprovlGvnBy', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_AprovlTknBy"))
                            bsMsgDetailsForm.setValidation('BS_AprovlTknBy', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("CN_Id"))
                            bsMsgDetailsForm.setValidation('CN_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("ST_Id"))
                            bsMsgDetailsForm.setValidation('ST_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_Persons"))
                            bsMsgDetailsForm.setValidation('BS_Persons', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("LC_Id"))
                            bsMsgDetailsForm.setValidation('LC_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_PaidBy"))
                            bsMsgDetailsForm.setValidation('BS_PaidBy', 'ValidInteger');
                        //if (bsMsgDetailsForm.isItem("CHQ_Number")) bsMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                        if (bsMsgDetailsForm.isItem("IT_Id"))
                            bsMsgDetailsForm.setValidation('IT_Id', 'ValidInteger,NotEmpty');
                        if (bsMsgDetailsForm.isItem("BS_Description"))
                            bsMsgDetailsForm.setValidation('BS_Description', 'NotEmpty');

                        if (!bsMsgDetailsForm.isItemHidden("TR_Id")) {
                            bsMsgDetailsForm.setValidation('TR_Id', 'ValidInteger,NotEmpty');
                        } else {
                            bsMsgDetailsForm.clearValidation('TR_Id');
                        }

                        if (bsMsgDetailsForm.isItem("PM_Id")) {
                            var patmentMode = bsMsgDetailsForm.getCombo("PM_Id");
                            var pmMode = patmentMode.getSelectedValue();
                            if (pmMode == 2) {
                                bsMsgDetailsForm.setValidation('BNK_Id', 'NotEmpty');
                                bsMsgDetailsForm.setValidation('BB_Id', 'NotEmpty');
                                bsMsgDetailsForm.setValidation('BA_Id', 'NotEmpty');
                                bsMsgDetailsForm.setValidation('BS_PayersBank', 'NotEmpty');
                                bsMsgDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty');

                                if (bsMsgDetailsForm.isItem("BS_PayType")) {
                                    var paymentType = bsMsgDetailsForm.getCombo("BS_PayType");
                                    var pmType = paymentType.getSelectedValue();
                                    if (pmType == '2') {
                                        bsMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                                    } else if (pmType == '3' || pmType == '4' || pmType == '5') {
                                        bsMsgDetailsForm.setItemValue("CHQ_Number", "");
                                        bsMsgDetailsForm.setValidation('CHQ_Number', 'null');
                                    }
                                }
                            } else {
                                bsMsgDetailsForm.setItemValue("BNK_Id", "");
                                bsMsgDetailsForm.setItemValue("BB_Id", "");
                                bsMsgDetailsForm.setItemValue("BB_Id", "");
                                bsMsgDetailsForm.setItemValue("BA_Id", "");
                                bsMsgDetailsForm.setItemValue("CHQ_Number", "");
                                bsMsgDetailsForm.setItemValue("BS_PayersBank", "");
                                bsMsgDetailsForm.setItemValue("BS_PayersChQ", "");

                                bsMsgDetailsForm.setValidation('BNK_Id', 'null');
                                bsMsgDetailsForm.setValidation('BB_Id', 'null');
                                bsMsgDetailsForm.setValidation('BB_Id', 'null');
                                bsMsgDetailsForm.setValidation('BA_Id', 'null');
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null');
                                bsMsgDetailsForm.setValidation('BS_PayersBank', 'null');
                                bsMsgDetailsForm.setValidation('BS_PayersChQ', 'null');
                            }
                        }

                        bsMsgDetailsForm.attachEvent("onValidateError", function (name, value, result) {
                            bsMsgDetailsForm.setValidateCss(name, false, 'validate_red');
                            return false;
                        });

//                        var messageValidate = bsMsgDetailsForm.validate();
                        if (bsMsgDetailsForm.getItemValue("IT_Flag") == "del_item") {
                            bsMsgDetailsForm.setValidateCss('IT_Id', false);
                            dhtmlx.message({text: 'Selected Item is deleted.Please Verify'});
                            return false;
                        }
                        if (bsMsgDetailsForm.getItemValue("BS_PettyCashRefId") != 0) {
                            if (Number(bsMsgDetailsForm.getItemValue("BS_Amount")) > Number(bsMsgDetailsForm.getItemValue("PettyCashAmount"))) {
                                dhtmlx.message({text: ' Amount exceeds petty cash Amount. Please Verify'});
                                bsMsgDetailsForm.setValidateCss('BS_Amount', false);
                                return false;
                            }
                        }

                        if (messageValidate && CstmValidate) {
                            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);
                            var params = "bsId=" + BS_Id + "&TR_Track=" + BS_TRCombo.getSelectedText();
                            bsMsgDetailsForm.send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&' + params), function (loader, response) {
                                preTally.Settings.progressOff(true, bsMsgDetailsWin, null);
                                if (response != 1 && response != 2) {
                                    bsMsgDetailsForm.clear();
                                    dhxBSMsgDetails.window("bsDetailsWinMsg_" + BS_Id).close();
                                } else if (response == '2') {
                                    response = 'Edit is restricted for this entry.';
                                } else {
                                    response = 'Invalid Entry. Please Verify.';
                                }
                                dhtmlx.message({text: response});
                            });
                        }
                    } else {
                        dhxBSMsgDetails.window("bsDetailsWinMsg_" + BS_Id).close();
                    }
                });
            });
        }
    };
})(jQuery, this);