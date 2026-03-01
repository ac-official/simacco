;
(function ($, window, undefined) {
    var DuplBranchCombo;
    preTally.TrackDupReports = {
        view_TrackDupReports: function () {
            reportTrackDupInit = 1;
            sortVal = "ASC";
            if (!dhxMiddleBlockTabs.cells("view_trackdupreports")) {
                reportStockInit = 1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details");

                dhxMiddleBlockTabs.addTab("view_trackdupreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Track Duplicate Report&nbsp;&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupTrkDup'/>", 240);
                dhxMiddleBlockTabs.tabs("view_trackdupreports").setActive();
                $(".popupTrkDup").click(function () {
                    var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                    if (bsl) {
                        preTally.TrackDupReports.filterReport(bsl);
                    }
                    //preTally.TrackDupReports.filterReport(ofzTrackDupTree.getSelectedItemId());                   
                })
                dhxTrackDupLayout = dhxMiddleBlockTabs.cells("view_trackdupreports").attachLayout("1C");
                //dhxTrackDupLayout.cells("a").setWidth(0);                
                //dhxTrackDupLayout.cells("a").hideHeader();
                //dhxTrackDupLayout.cells("a").fixSize(true, true);    
                //dhxTrackDupLayout.cells("b").fixSize(true, true);  
                dhxTrackDupLayout.cells('a').hideHeader();

                /* ofzTrackDupTree = dhxTrackDupLayout.cells("a").attachTree();
                 
                 ofzTrackDupTree.attachEvent("onXLS", function(){
                 preTally.Settings.progressOn(false, dhxTrackDupLayout, 'a');
                 });
                 ofzTrackDupTree.attachEvent("onXLE", function(){
                 preTally.Settings.progressOff(false, dhxTrackDupLayout, 'a');
                 });
                 
                 ofzTrackDupTree.enableHighlighting(true);                 
                 ofzTrackDupTree.setOnClickHandler(preTally.TrackDupReports.filterReport);
                 ofzTrackDupTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                 var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);                    
                 ofzTrackDupTree.loadXML(preTally.Initialize.encryptURL("requisites/ofzCashRptTree.php&f=BS&r=OF_O"), function(){
                 var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                 if(bsl){
                 ofzTrackDupTree.selectItem(bsl,'onClick',true);
                 preTally.TrackDupReports.filterReport(bsl);
                 }
                 }); 
                 var OFType_Options = [
                 ['OF_O', 'obj', 'Office Map', 'office.gif'],
                 ];
                 
                 ofzTrackDupTree.attachEvent("onClick", function(id){
                 preTally.Settings.progressOn(true, dhxLayout, null);
                 setTimeout(function(){
                 preTally.TrackDupReports.filterReport(id);
                 },1);
                 }); */

                /*dhxTrackDupTlbr = dhxTrackDupLayout.cells("a").attachToolbar();
                 dhxTrackDupTlbr.setIconsPath("images/icon/");
                 dhxTrackDupTlbr.addText('odhType_Rptz', '1', 'Office Map' );
                 dhxTrackDupTlbr.setAlign('right');*/
                //dhxTrackDupTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
                /* dhxTrackDupTlbr.setAlign('right');
                 dhxTrackDupTlbr.setListOptionSelected('odhType_Rptz', 'OF_O');                
                 dhxTrackDupTlbr.attachEvent('onClick', function(id){
                 preTally.Settings.reLoadOffz('BS',id,ofzTrackDupTree);
                 });  */

                ptTrackDupTlbr = dhxTrackDupLayout.cells("a").attachToolbar();
                ptTrackDupTlbr.setIconsPath("images/icon/default_18/");

                var RPType_Options = [
                    ['0', 'obj', 'Income-Expense Report', 'incomeexpense.png'],
                    ['1', 'obj', 'Stock Report', 'stock.png'],
                ];

//                ptTrackDupTlbr.addButtonSelect('iebsType_Rptz', '1', '', RPType_Options, '', '', true, true,3,'select');
//                ptTrackDupTlbr.setAlign('left');
//                ptTrackDupTlbr.setListOptionSelected('iebsType_Rptz', '0');

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

                ptTrackDupTlbr.addButtonSelect("rpt_day_filter", '1', "Select Day", Days_Options, '', '', true, true, 10, 'select');
                ptTrackDupTlbr.addButtonSelect("rpt_year_filter", '1', "Select Year", Years_Options, '', '', true, true, 10, 'select');
                ptTrackDupTlbr.addButtonSelect("rpt_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');

                ptTrackDupTlbr.addSeparator();

                ptTrackDupTlbr.addText("text_from", null, "From");
                ptTrackDupTlbr.addInput("rpt_date_from", null, "", 75);
                ptTrackDupTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                ptTrackDupTlbr.addSeparator();

                ptTrackDupTlbr.addText("text_till", null, "Till");
                ptTrackDupTlbr.addInput("rpt_date_till", null, "", 75);
                ptTrackDupTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                ptTrackDupTlbr.addSeparator();

                ptTrackDupTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                // ptTrackDupTlbr.addSeparator();
                //ptTrackDupTlbr.addButton("excel_export", null , "Export", "excel.png");

                var ptRpTb_Inp_Frm = ptTrackDupTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function () {
                    if (ptTrackDupTlbr.getValue("rpt_date_till"))
                        preTally.TrackDupReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptTrackDupTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function () {
                    if (ptTrackDupTlbr.getValue("rpt_date_from"))
                        preTally.TrackDupReports.setSens(ptRpTb_Inp_Frm, "min");
                }

                ptTrackDupTlbr.attachEvent("onClick", function (id) {
                    var pId = ptTrackDupTlbr.getParentId(id);

                    if (id == 'rpt_df_clear')
                        ptTrackDupTlbr.setValue('rpt_date_from', '', false);

                    if (id == 'rpt_dt_clear')
                        ptTrackDupTlbr.setValue('rpt_date_till', '', false);

                    if (pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id + "." + Date.today().toString("MM.yyyy");
                        ptTrackDupTlbr.setValue('rpt_date_from', dateToday, false);
                        ptTrackDupTlbr.setValue('rpt_date_till', dateToday, false);
                        ptTrackDupTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptTrackDupTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.TrackDupReports.filterReport(rptStockFilterID);

                    }
                    if (pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate = new Date();
                        var yeartoolbar = parseInt(ptTrackDupTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay = new Date(yeartoolbar, id - 1, 1).toString("dd.MM.yyyy");
                        var lastDay = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptTrackDupTlbr.setValue('rpt_date_from', firstDay, false);
                        ptTrackDupTlbr.setValue('rpt_date_till', lastDay, false);
                        ptTrackDupTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptTrackDupTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.TrackDupReports.filterReport(rptStockFilterID);

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

                        ptTrackDupTlbr.setValue('rpt_date_from', firstDay, false);
                        ptTrackDupTlbr.setValue('rpt_date_till', lastDay, false);
                        ptTrackDupTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptTrackDupTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.TrackDupReports.filterReport(rptStockFilterID);

                    }

                    if (id == 'rpt_date_filter') {

                        ptTrackDupTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptTrackDupTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptTrackDupTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.TrackDupReports.filterReport(rptStockFilterID);
                    }
                    if (id == 'excel_export') {
                        preTally.TrackDupReports.exportTrackDupReports();
                    }
                });

                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");

                ptTrackDupTlbr.setAlign('right');

                var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;">\
                                        <div class="tb_tdr-tot"># : 0</div>\
                                   </div><div id="trkDpRpt_paging" style="top:0px !important;float:right !important;width:50% !important;"></div>';



                var tbRptObj = dhxTrackDupLayout.cells("a").attachStatusBar({
                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_tdr-tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 30            // custom height
                });

                ptTrackDupRprtsTabbar = dhxTrackDupLayout.cells("a").attachTabbar();
                //ptTrackDupRprtsTabbar.addTab("a1", tb_data_txt, "730px");
                ptTrackDupRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptTrackDupRprtsTabbar.addTab("a3", "Report Visulization");
                ptTrackDupRprtsTabbar.tabs("a3").hide();

                ptTrackDupRprtsTabbar.tabs("a2").setActive();

                ptTrackDupRprtsTabbar.attachEvent("onSelect", function (id, last_id) {
                    if (id != "a1") {
                        if (TrackDupReports_DetailDataPop && TrackDupReports_DetailDataPop.isVisible()) {
                            TrackDupReports_DetailDataPop.hide();
                        }
                        if (id == 'a2')
                            preTally.TrackDupReports.reLoadSHGrid();
                        if (id == 'a3')
                            preTally.TrackDupReports.reLoadGraph();
                        return true;
                    }
                });

                dhxTrackDupReportsGrid = ptTrackDupRprtsTabbar.cells("a2").attachGrid();
                dhxTrackDupReportsGrid.attachEvent("onFilterEnd", function (elements) {
                    if (dhxTrackDupReportsGrid.getRowsNum() == 0) {
                        dhxTrackDupReportsGrid.addRow(0, ['', 'No Records Found...', '', '', ''], 0);
                        dhxTrackDupReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxTrackDupReportsDetailLayout = ptTrackDupRprtsTabbar.cells("a3").attachLayout("3U");
                dhxTrackDupReportsDetailLayout.cells("b").setWidth(300);
                dhxTrackDupReportsDetailLayout.cells("b").setHeight(180);
                dhxTrackDupReportsDetailLayout.cells("b").hideHeader();

                dhxTrackDupReportsDetailLayout.cells("a").setText("Income & Expense");
                dhxTrackDupReportsDetailLayout.cells("c").setText("Details Of Income & Expense");

                dhxTrackDupReportsChart = dhxTrackDupReportsDetailLayout.cells("b").attachChart({
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
//                dhxTrackDupReportssForm = dhxTrackDupReportsDetailLayout.cells("a").attachForm();
//                dhxTrackDupReportssForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    $("label").css(" text-align","right");
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });

                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptTrackDupReportssToolbar = dhxTrackDupReportsDetailLayout.cells("c").attachToolbar();
                ptTrackDupReportssToolbar.setIconsPath("images/icon/");
                ptTrackDupReportssToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true, 2, 'select');
                ptTrackDupReportssToolbar.setAlign('right');
                ptTrackDupReportssToolbar.setListOptionSelected('ieType', 'IE_I');
                ptTrackDupReportssToolbar.disableItem('ieType');

                ptTrackDupReportssToolbar.attachEvent('onClick', function (id) {
                    preTally.TrackDupReports.reLoadGraph();
                });

                reportStockSubHeadChart = dhxTrackDupReportsDetailLayout.cells("c").attachChart({
                    view: "bar",
                    container: "chart2",
                    value: "#amount#",
                    label: "" + currency + "#amount#",
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
                var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (bsl) {
                    preTally.TrackDupReports.filterReport(bsl);
                }

            } else {
                preTally.TrackDupReports.filterReport();
                //preTally.TrackDupReports.filterReport(ofzTrackDupTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("view_trackdupreports").setActive();
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
            reportDetailsGrid.enableTooltips("false,false,false,false");

            reportDetailsGrid.init();

            reportStockSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            reportStockSubHeadChart.parse(chartStock_Response_OB, "json");
            reportStockSubHeadChart.parse(chartStock_Response_CB, "json");
            var LGD_Inc = chartStock_Response_OB;
            var LGD_Exp = chartStock_Response_CB;

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
                preTally.TrackDupReports.filterSubHead(id);
            });
        },
        filterSubHead: function (id) {

            $("#shBRF").val(reportDetailsGrid.cells(id, 4).getValue());

            preTally.TrackDupReports.applyFilter();
        },
        filterReport: function (id) {
            //var Ids = ofzTrackDupTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval;
            var filtrBranchInterval;

            /*var subIds = ofzTrackDupTree.getAllSubItems(id);
             if(subIds != ''){
             RP_Ids = id+","+ofzTrackDupTree.getAllSubItems(id);
             }else {
             RP_Ids = id;
             }*/

            rptStockFilterID = id;
            if (!rptStockFilterID) {
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {



                if (reportTrackDupInit == 1)
                {
                    var date = new Date();
                    var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    var month = date.getMonth();

                    tDate = Date.today().toString("dd.MM.yyyy");
                    ptTrackDupTlbr.setValue('rpt_date_from', "01." + Date.today().toString("MM.yyyy"));
                    ptTrackDupTlbr.setValue('rpt_date_till', tDate);
                    ptTrackDupTlbr.setItemText('rpt_month_filter', m_names[month]);
                    reportTrackDupInit = 0;

                }
                chartTrkDpFilterParams = 'r=' + id + '&f=' + ptTrackDupTlbr.getValue("rpt_date_from") + '&t=' + ptTrackDupTlbr.getValue("rpt_date_till");

                dhxTrackDupReportsGrid.destructor();
                dhxTrackDupReportsGrid = ptTrackDupRprtsTabbar.cells("a2").attachGrid();

                dhxTrackDupReportsGrid.setImagePath("../../codebase/imgs/");
                dhxTrackDupReportsGrid.setHeader("SlNo,<input type='text'  class='trackduprpt_text_filter' id ='trkTDR' style='width: 70%;' placeholder='Track'>,<input type='text'  class='trackduprpt_text_filter' id ='itmTDR' style='width: 90%;' placeholder='Item'>,<div width='70%' id='tDupBrnchFiltr'></div>,<div>Duplications<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_TrkDPSort' /></div>,,");
                dhxTrackDupReportsGrid.setInitWidths("50,100,*,300,90,0,50")
                dhxTrackDupReportsGrid.setColAlign("center,left,left,center,center,center,center")
                dhxTrackDupReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                dhxTrackDupReportsGrid.setColSorting("na,na,na,na,na,na,na");
                dhxTrackDupReportsGrid.init();
                dhxTrackDupReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxTrackDupReportsGrid.setSkin("dhx_skyblue")
                dhxTrackDupReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                dhxTrackDupReportsGrid.enablePaging(true, 50, 5, "trkDpRpt_paging", true);
                dhxTrackDupReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                dhxTrackDupReportsGrid.enableTooltips("false,false,false,false,false,false,false");
                dhxTrackDupReportsGrid.enableCollSpan(true);
                dhxTrackDupReportsGrid.enableRowsHover(true,"bonusReportHover");
                var srtFlg = 0;
                DuplBranchCombo = new dhtmlXCombo("tDupBrnchFiltr");
                DuplBranchCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=all"));
                DuplBranchCombo.enableFilteringMode("between");
                DuplBranchCombo.attachEvent("onChange", function (id) {
                    preTally.TrackDupReports.applyFilter();
                });
                $('.btn_TrkDPSort').click(function () {
                    var colId = $(this).attr("colNum")
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        sortVal = "ASC";
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        sortVal = "DESC";
                        srtFlg = 0;
                    }
                    preTally.TrackDupReports.applyFilter();
                });
                $(".trackduprpt_text_filter").keyup(function (value) {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.TrackDupReports.applyFilter();
                        clearInterval(filtrInterval);
                    }, 500);

                });


                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxTrackDupReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportTrackDupData.php&" + chartTrkDpFilterParams), function () {
                    $('.tb_tdr-tot').html("# : " + dhxTrackDupReportsGrid.getUserData("", "TL_Count") + " ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                dhxTrackDupReportsGrid.attachEvent("onRowSelect", function (id, ind) {
                    if (id != 0)
                        preTally.TrackDupReports.showDetailData(dhxTrackDupReportsGrid.getUserData(id, "TR_Id"), dhxTrackDupReportsGrid.getUserData(id, "IT_Id"), dhxTrackDupReportsGrid.getUserData("", "F_Date"), dhxTrackDupReportsGrid.getUserData("", "T_Date"));
                });

                /* setTimeout(function(){
                 preTally.Settings.progressOff(true, dhxLayout, null);
                 },15000);*/
            }
        },
        applyFilter: function () {
            var filterValue = new Array($('#itmTDR').val(), $('#trkTDR').val(), DuplBranchCombo.getSelectedValue(), sortVal);
            dhxTrackDupReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxTrackDupReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportTrackDupData.php&" + chartTrkDpFilterParams + "&filter=" + filterValue), function () {
                $('.tb_tdr-tot').html("# : " + dhxTrackDupReportsGrid.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        reLoadGraph: function () {
            var IE_Type = ptTrackDupReportssToolbar.getListOptionSelected('ieType');

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
            reportStockSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';
            if (IE_Type === 'IE_I') {
                reportStockSubHeadChart.parse(chartStock_Response_OB, "json");
                legendGridData = chartStock_Response_OB;
            }
            if (IE_Type === 'IE_E') {
                reportStockSubHeadChart.parse(chartStock_Response_CB, "json");
                legendGridData = chartStock_Response_CB;
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
        showDetailData: function (TR_Id, IT_Id, F_Date, T_Date) {
            if (!TrackDupReports_DetailDataPop) {
                TrackDupReports_DetailDataPop = new dhtmlXWindows();
            }
//            dhxDescWin = new dhtmlXWindows();           
            /*   var x = getAbsoluteLeft(inp);
             var y = getAbsoluteTop(inp);            
             var w = inp.offsetWidth;
             var h = inp.offsetHeight;   */
            var width = window.innerWidth * 0.98;
            var height = window.innerHeight * 0.75;
            var rptDetailsWin = TrackDupReports_DetailDataPop.createWindow("wins_desc", 100, 100, width, height);
            rptDetailsWin.button("minmax1").hide();
            rptDetailsWin.button("minmax2").hide();
            rptDetailsWin.button("park").hide();
            rptDetailsWin.center();
            rptDetailsWin.setModal(true);
            rptDetailsWin.setText("Track Entries in Detail");
            var DSDetailsGrid = rptDetailsWin.attachGrid();
            DSDetailsGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, rptDetailsWin, null);
            });
            DSDetailsGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, rptDetailsWin, null);
            });
            if (F_Date == '')
                F_Date = 0;
            if (T_Date == '')
                T_Date = 0;
            var params = "trname=" + TR_Id + "&IT_Id=" + IT_Id + "&F_Date=" + F_Date + "&T_Date=" + T_Date;
            DSDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportTrackDupPop.php&" + params), function () {
                function date_custom(a, b, order) {
                    a = a.split("/")
                    b = b.split("/")
                    if (a[2] == b[2]) {
                        if (a[1] == b[1])
                            return (a[0] > b[0] ? 1 : -1) * (order == "asc" ? 1 : -1);
                        else
                            return (a[1] > b[1] ? 1 : -1) * (order == "asc" ? 1 : -1);
                    } else
                        return (a[2] > b[2] ? 1 : -1) * (order == "asc" ? 1 : -1);
                }
                ;

                DSDetailsGrid.setCustomSorting(date_custom, 6);
                DSDetailsGrid.enableRowsHover(true,"bonusReportHover");
                var srtFlg = 0;
                $('.btn_Sort_trackDupPop').click(function () {
                    var colId = $(this).attr("colNum");
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if (colId == '3' || colId == '5'){
                            DSDetailsGrid.sortRows(colId, "int", "asc");
                            }
                        else if (colId == '6')
                            DSDetailsGrid.sortRows(colId, "date", "asc");
                        else {
                            DSDetailsGrid.sortRows(colId, "str", "asc");
                        }
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if (colId == '3' || colId == '5'){
                            DSDetailsGrid.sortRows(colId, "int", "des");
                        }
                        else if (colId == '6')
                            DSDetailsGrid.sortRows(colId, "date", "des");
                        else
                            DSDetailsGrid.sortRows(colId, "str", "des");
                        srtFlg = 0;
                    }
                });
            });
            $('.targetStock_' + TR_Id).unbind();

        },
        hideDetailData: function () {
            if (TrackDupReports_DetailDataPop.isVisible()) {
                TrackDupReports_DetailDataPop.hide();
            }
        },
        exportTrackDupReports: function () {

            var exportfilterValue = {};
            exportfilterValue['From_Date'] = ptTrackDupTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = ptTrackDupTlbr.getValue("rpt_date_till");
            exportfilterValue['TrackIDFilter'] = $('#itmSRF').val();
            exportfilterValue['BranchFilter'] = $('#addSRF').val();
            exportfilterValue['AddedByFilter'] = $('#brnSRF').val();
            exportfilterValue['amount'] = $('#brnSRF').val();
            exportfilterValue['r'] = rptStockFilterID;
            exportfilterValue['report_type'] = "stocks";
            exportfilterValue['shCRF'] = $("#shSRF").val();
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(
                    preTally.Initialize.encryptURL('warehouse/ReportExcelExport.php'),
                    {filter: exportfilterValue},
                    function (data) {
                        fileName = data.split("XL_");
                        if (fileName[1]) {
                            document.location = "uploads/excelFile/" + fileName[1];
                        }
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });

        }
    };
})(jQuery, this);