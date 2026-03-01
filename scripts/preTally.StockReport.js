;(function($, window, undefined) {
    preTally.StockReport = {
        view_StockReports: function() {

            if (!dhxMiddleBlockTabs.cells("view_stockreports")) {
                reportStockInit=1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details"); 
                
                dhxMiddleBlockTabs.addTab("view_stockreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Stock Summary Report&nbsp;&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupStock'/>", 240);
                dhxMiddleBlockTabs.tabs("view_stockreports").setActive();
                $(".popupStock").click(function(){
                    preTally.StockReport.filterReport(ofzStockRprtTree.getSelectedItemId());
                    /*$(this).data('clicked', true); 
                    x = getAbsoluteLeft(this);
                    y = getAbsoluteTop(this); 
                    w = this.offsetWidth;
                    h = this.offsetHeight;
                    var myPop = new dhtmlXPopup();
                    myPop.attachHTML("Stock Summary Report");
                    myPop.show(x ,y,w,h);*/
                })
                dhxStockReportsLayout =  dhxMiddleBlockTabs.cells("view_stockreports").attachLayout("2U");
                dhxStockReportsLayout.cells("a").setWidth(200);
                dhxStockReportsLayout.cells("a").hideHeader();
                dhxStockReportsLayout.cells("a").fixSize(true, true);    
                dhxStockReportsLayout.cells("b").fixSize(true, true);  
                dhxStockReportsLayout.cells('b').hideArrow();
                
                ofzStockRprtTree = dhxStockReportsLayout.cells("a").attachTree();
                
                ofzStockRprtTree.attachEvent("onXLS", function(){
                    preTally.Settings.progressOn(false, dhxStockReportsLayout, 'a');
                });
                ofzStockRprtTree.attachEvent("onXLE", function(){
                    preTally.Settings.progressOff(false, dhxStockReportsLayout, 'a');
                });
                
                ofzStockRprtTree.enableHighlighting(true);                 
                ofzStockRprtTree.setOnClickHandler(preTally.StockReport.filterReport);
                ofzStockRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                ofzStockRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/ofzCashRptTree.php&f=BS&r=OF_O"), function(){
                    var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                    if(bsl){
                        ofzStockRprtTree.selectItem(bsl,'onClick',true);
                        preTally.StockReport.filterReport(bsl);
                    }	
                }); 
                var OFType_Options = [
                    ['OF_O', 'obj', 'Office Map', 'office.gif'],
                ];
                
//                ofzStockRprtTree.attachEvent("onClick", function(id){
//                    preTally.Settings.progressOn(true, dhxLayout, null);
//                    setTimeout(function(){
//                        preTally.StockReport.filterReport(id);
//                    },1);
//                });
                
                dhxStockRprtTlbr = dhxStockReportsLayout.cells("a").attachToolbar();
                dhxStockRprtTlbr.setIconsPath("images/icon/");
                dhxStockRprtTlbr.addText('odhType_Rptz', '1', 'Office Map' );
                dhxStockRprtTlbr.setAlign('right');
//                dhxStockRprtTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
//                dhxStockRprtTlbr.setAlign('right');
//                dhxStockRprtTlbr.setListOptionSelected('odhType_Rptz', 'OF_O');                
//                dhxStockRprtTlbr.attachEvent('onClick', function(id){
//                    preTally.Settings.reLoadOffz('BS',id,ofzStockRprtTree);
//                });  
                
                ptStockRprtTlbr = dhxStockReportsLayout.cells("b").attachToolbar();                
                ptStockRprtTlbr.setIconsPath("images/icon/default_18/");
                
                
//                var RPType_Options = [
//                    ['0', 'obj', 'Income-Expense Report', 'incomeexpense.png'],
//                    ['1', 'obj', 'Stock Report', 'stock.png'],
//                ];
//                
//                ptStockRprtTlbr.addButtonSelect('iebsType_Rptz', '1', '', RPType_Options, '', '', true, true,3,'select');
//                ptStockRprtTlbr.setAlign('left');
//                ptStockRprtTlbr.setListOptionSelected('iebsType_Rptz', '0');
                
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
                
                ptStockRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptStockRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptStockRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                

                ptStockRprtTlbr.addSeparator();
                          
                ptStockRprtTlbr.addText("text_from", null, "From");
                ptStockRprtTlbr.addInput("rpt_date_from", null, "", 75);
                ptStockRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                ptStockRprtTlbr.addSeparator();
                
                ptStockRprtTlbr.addText("text_till", null, "Till");
                ptStockRprtTlbr.addInput("rpt_date_till", null, "", 75);
                ptStockRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                ptStockRprtTlbr.addSeparator();
                
                ptStockRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                ptStockRprtTlbr.addSeparator();
                ptStockRprtTlbr.addButton("excel_export", null , "Export", "excel.png");
                
                var ptRpTb_Inp_Frm = ptStockRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptStockRprtTlbr.getValue("rpt_date_till")) preTally.StockReport.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptStockRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptStockRprtTlbr.getValue("rpt_date_from")) preTally.StockReport.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 ptStockRprtTlbr.attachEvent("onClick", function(id){  
                   var pId = ptStockRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        ptStockRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptStockRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        ptStockRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        ptStockRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        ptStockRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptStockRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.StockReport.filterReport(rptStockFilterID);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptStockRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptStockRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptStockRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptStockRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptStockRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.StockReport.filterReport(rptStockFilterID);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        
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
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        ptStockRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        ptStockRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        ptStockRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptStockRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.StockReport.filterReport(rptStockFilterID);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        /*if(!ptStockRprtTlbr.getValue("rpt_date_from") && !ptStockRprtTlbr.getValue("rpt_date_till")) {
                            dhtmlx.message({text: 'Please Select From / To Date'});
                        } else if(!rptStockFilterID){
                            dhtmlx.message({text: 'Please Select Office / Branch / User'});
                        } else {
                            preTally.StockReport.filterReport(rptStockFilterID);
                        }*/
                        ptStockRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptStockRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptStockRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.StockReport.filterReport(rptStockFilterID);
                    }  
                    if(id == 'excel_export') {
                       preTally.StockReport.exportStockReport();
                    }
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                ptStockRprtTlbr.setAlign('right');
                
                if(unescape(JGG1P3bDnUSDL6Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                    var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;">\
                                        <div class="tb_cnt_tot"># : 0</div>\<div class="testdiv" style="height:20px; top:0px !important;width:900px;">\
                                        <div class="tb_rpt_oldstock">OLD STOCK  : 0  </div>\
                                        <div class="tb_rpt_stockbuss">BUSINESS  : 0  </div>\
                                        <div class="tb_rpt_stockstock">CURRENT STOCK : 0  </div>\</div><div class="testdiv" style="height:20px; top:21px !important;width:600px;">\
                                        <div class="tb_rpt_stockinc">AMOUNT RECEIVED : 0  </div>\
                                        <div class="tb_rpt_stockexp">AMOUNT SPENT : 0  </div>\</div>\
                                   </div><div id="stkRpt_paging" style="top:0px !important;float:left !important;width:100% !important;"></div>';
                }else{
                    var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;">\
                                        <div class="tb_cnt_tot"># : 0</div>\<div class="testdiv" style="height:20px; top:0px !important;width:900px;">\
                                        <div class="tb_rpt_oldstock">OLD STOCK  : 0  </div>\
                                        <div class="tb_rpt_stockbuss">BUSINESS : 0  </div>\
                                        <div class="tb_rpt_stockstock">CURRENT STOCK : 0  </div>\</div><div class="testdiv" style="height:20px; top:21px !important;width:600px;">\
                                        <div class="tb_rpt_stockinc">AMOUNT RECEIVED : 0  </div>\</div>\
                                   </div><div id="stkRpt_paging" style="top:0px !important;float:left !important;width:100% !important;"></div>';
                }
                
                
                var tbRptObj = dhxStockReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 75            // custom height
                });
                
                ptStockRprtsTabbar = dhxStockReportsLayout.cells("b").attachTabbar();
                //ptStockRprtsTabbar.addTab("a1", tb_data_txt, "730px");
                ptStockRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptStockRprtsTabbar.addTab("a3", "Report Visulization");
                ptStockRprtsTabbar.tabs("a3").hide();
                
                ptStockRprtsTabbar.tabs("a2").setActive();
                
                ptStockRprtsTabbar.attachEvent("onSelect", function(id, last_id){  
                    if(id != "a1") {
                        if (StockReport_DetailDataPop && StockReport_DetailDataPop.isVisible()) { 
                            StockReport_DetailDataPop.hide();
                        }
                        if(id == 'a2') preTally.StockReport.reLoadSHGrid();
                        if(id == 'a3') preTally.StockReport.reLoadGraph();
                        return true;
                    } 
                });
  
                dhxStockReportsGrid = ptStockRprtsTabbar.cells("a2").attachGrid();  
//                dhxStockReportsGrid.loadXML("requisites/reportStockData.php", function() { 
//                    dhxStockReportsGrid.makeFilter("stockrpt_text_filter" , 1);
//                    var fltTxtValue = dhxStockReportsGrid.getFilterElement(1);
//                    fltTxtValue.onkeyup = function(){
//                        reportDetailsGrid.filterBy(1,this.value);
//                    };
//                });
                
                dhxStockReportsGrid.attachEvent("onFilterEnd", function(elements){ 
                    if(dhxStockReportsGrid.getRowsNum() == 0 ) {
                        dhxStockReportsGrid.addRow(0,['','No Records Found...','','',''],0); 
                        dhxStockReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxStockReportDetailLayout =  ptStockRprtsTabbar.cells("a3").attachLayout("3U");
                dhxStockReportDetailLayout.cells("b").setWidth(300);
                dhxStockReportDetailLayout.cells("b").setHeight(180);
                dhxStockReportDetailLayout.cells("b").hideHeader();
                
                dhxStockReportDetailLayout.cells("a").setText("Income & Expense");
                dhxStockReportDetailLayout.cells("c").setText("Details Of Income & Expense");
                    
                dhxStockReportChart =  dhxStockReportDetailLayout.cells("b").attachChart({                   
                    view: "pie",
                    container: "chart",
                    value: "#value#",
                    labelOffset: -20,
                    radius: 75,
                    label: function(obj) {
                        return "<div class='pieChartLabel' style='border:1px solid " + obj.color + "'> " + currency +" " + obj.value + "</div>";
                    },
                    color: "#color#",
                    legend: {
                        width: 75,
                        align: "right",
                        valign: "bottom",
                        template: "#IE#"
                    }       
                });                
//                dhxStockReportsForm = dhxStockReportDetailLayout.cells("a").attachForm();
//                dhxStockReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    $("label").css(" text-align","right");
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });
                 
                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptStockReportsToolbar = dhxStockReportDetailLayout.cells("c").attachToolbar();
                ptStockReportsToolbar.setIconsPath("images/icon/");
                ptStockReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true,2,'select');
                ptStockReportsToolbar.setAlign('right');
                ptStockReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptStockReportsToolbar.disableItem('ieType');
                
                ptStockReportsToolbar.attachEvent('onClick', function(id){
                    preTally.StockReport.reLoadGraph();
                });

                reportStockSubHeadChart = dhxStockReportDetailLayout.cells("c").attachChart({      
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

                
            } else {
                preTally.StockReport.filterReport(ofzStockRprtTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("view_stockreports").setActive();
            }
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        reLoadSHGrid: function() {
            
            if(reportDetailsGrid) reportDetailsGrid.destructor();
            
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader(",Category, Amount,#select_filter,");
            reportDetailsGrid.setInitWidths("20,*,90,1,1");
            reportDetailsGrid.setColAlign("left,left,right,left,left");
            reportDetailsGrid.setColTypes("ro,ro,ro,ro,ro");
            reportDetailsGrid.enableTooltips("false,false,false,false");
            
            reportDetailsGrid.init();
            
            reportStockSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();
            
            reportStockSubHeadChart.parse(chartStock_Response_OB,"json");
            reportStockSubHeadChart.parse(chartStock_Response_CB,"json");
            var LGD_Inc = chartStock_Response_OB;
            var LGD_Exp = chartStock_Response_CB;
            
            var LData   = new Object();
            var LGDrows = [];
            
            if((LGD_Inc.length !=0) || (LGD_Exp.length !=0))
            {    
           LGDrows.push({ id  : 1, data: ['' , "All",'', 'Income','' ] });
            var LGCount = 2;
            $.each(LGD_Inc, function(index, value) {
                LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/up_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Income', value.shId ] });
                LGCount ++;
            });
            $.each(LGD_Exp, function(index, value) {
                LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/down_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Expense', value.shId ] });
                LGCount ++;
            });
            }
            LData.rows = LGDrows;//alert(JSON.stringify(LData));
            reportDetailsGrid.parse(LData,"json");         
            reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
                preTally.StockReport.filterSubHead(id);
            }) ;   
        },
        filterSubHead:function (id){
            
            $( "#shBRF" ).val(reportDetailsGrid.cells(id,4).getValue());
            
            preTally.StockReport.applyFilter();  
        },
        filterReport: function(id) { 
            //alert(id);
            //var Ids = ofzStockRprtTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval ;
            var filtrBranchInterval ;
            
            var subIds = ofzStockRprtTree.getAllSubItems(id);
            if(subIds != ''){
                RP_Ids = id+","+ofzStockRprtTree.getAllSubItems(id);
            }else {
                RP_Ids = id;
            }
                        
            rptStockFilterID = id;
            if(!rptStockFilterID){
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                ptStockReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                
                 if(reportStockInit==1)
                 {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                    if(bsp==4)
                    {   var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
//                        ptStockRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        ptStockRprtTlbr.setValue('rpt_date_till', tDate);
                        ptStockRprtTlbr.setItemText('rpt_month_filter',m_names[month]);
                        reportStockInit=0;
                    }
                    else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy"); 
                        ptStockRprtTlbr.setValue('rpt_date_till', cDate);
//                        ptStockRprtTlbr.setValue('rpt_date_from', cDate);
                        ptStockRprtTlbr.setItemText('rpt_day_filter', 'Today');
                        reportStockInit=0;
                    }
                 
                }
                chartStockFilterParams = 'r='+id+'&f='+ptStockRprtTlbr.getValue("rpt_date_from")+'&t='+ptStockRprtTlbr.getValue("rpt_date_till");
             
                dhxStockReportsGrid.destructor();                
                dhxStockReportsGrid = ptStockRprtsTabbar.cells("a2").attachGrid();
                        
                dhxStockReportsGrid.setImagePath("../../codebase/imgs/");
//                dhxStockReportsGrid.setHeader("#,<input type='text'  class='stockrpt_text_filter' id ='itmSRF' style='width: 90%;' placeholder='Track'>,<input type='hidden' id='shSRF' >,Business ,Amount Received,Amount Spent,Current Stock,<input type='text'  class='stockrpt_text_filter' id='addSRF' style='width: 90%;' placeholder='Branch'>,<input type='text'  class='stockrpt_text_filter' id='brnSRF' style='width: 90%;' placeholder='Added By'> ");
//                //dhxStockReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
//                dhxStockReportsGrid.setInitWidths("60,*,0,*,*,*,*,*,*")
//                dhxStockReportsGrid.setColAlign("center,left,left,right,right,right,right,left,left")
//                dhxStockReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
                
                if(unescape(JGG1P3bDnUSDL6Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                    dhxStockReportsGrid.setHeader("SlNo,<input type='text'  class='stockrpt_text_filter' id ='itmSRF' style='width: 90%;' placeholder='Track'>,<input type='hidden' id='shSRF' >,Business ,Amount Received,Amount Spent,Current Stock,<input type='text'  class='stockrpt_text_filter' id='addSRF' style='width: 90%;' placeholder='Branch'>,<input type='text'  class='stockrpt_text_filter' id='brnSRF' style='width: 90%;' placeholder='Added By'>, ");
                    //dhxStockReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                    dhxStockReportsGrid.setInitWidths("60,*,0,*,*,*,*,*,*,50")
                    dhxStockReportsGrid.setColAlign("center,left,left,right,right,right,right,left,left,center")
                    dhxStockReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                    dhxStockReportsGrid.setColSorting("na,na,na,na,na,na,na,na,na,na");  
                    //dhxStockReportsGrid.enableSmartRendering(true,50);
                   //dhxStockReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true");
                   dhxStockReportsGrid.init();
                   dhxStockReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxStockReportsGrid.setSkin("dhx_skyblue")
                dhxStockReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxStockReportsGrid.enablePaging(true,50,5,"stkRpt_paging",true);
                dhxStockReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                }else{
                    dhxStockReportsGrid.setHeader("SlNo,<input type='text'  class='stockrpt_text_filter' id ='itmSRF' style='width: 90%;' placeholder='Track'>,<input type='hidden' id='shSRF' >,Business ,Amount Received,Current Stock,<input type='text'  class='stockrpt_text_filter' id='addSRF' style='width: 90%;' placeholder='Branch'>,<input type='text'  class='stockrpt_text_filter' id='brnSRF' style='width: 90%;' placeholder='Added By'>, ");
                    //dhxStockReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                    dhxStockReportsGrid.setInitWidths("60,*,0,*,*,*,*,*,50")
                    dhxStockReportsGrid.setColAlign("center,left,left,right,right,right,left,left,center")
                    dhxStockReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
                    dhxStockReportsGrid.setColSorting("na,na,na,na,na,na,na,na,na");                    
                    dhxStockReportsGrid.setSkin("dhx_skyblue");
                    dhxStockReportsGrid.enableSmartRendering(true,50);
                    //dhxStockReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,true");
                    dhxStockReportsGrid.init();
                dhxStockReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxStockReportsGrid.setSkin("dhx_skyblue")
                dhxStockReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxStockReportsGrid.enablePaging(true,50,5,"stkRpt_paging",true);
                dhxStockReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                }
//                dhxStockReportsGrid.setHeader("#,<input type='text'  class='stockrpt_text_filter' id ='itmSRF' style='width: 90%;' placeholder='Track'>,<input type='hidden' id='shSRF' >,Business ,Amount Received,Amount Spent,Current Stock,<input type='text'  class='stockrpt_text_filter' id='addSRF' style='width: 90%;' placeholder='Branch'>,<input type='text'  class='stockrpt_text_filter' id='brnSRF' style='width: 90%;' placeholder='Added By'> ");
//                //dhxStockReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
//                dhxStockReportsGrid.setInitWidths("60,180,0,110,110,110,110,*,150")
//                dhxStockReportsGrid.setColAlign("center,left,left,right,right,right,right,left,left")
//                dhxStockReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
//                
//                dhxStockReportsGrid.setColSorting("na,na,na,na,na,na,na,na,na")
//                dhxStockReportsGrid.init();
//                dhxStockReportsGrid.setSkin("dhx_skyblue")
//                dhxStockReportsGrid.enableSmartRendering(true,50);
//                dhxStockReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                //ptStockRprtsTabbar.cells("a2").attachStatusBar({text:'<input type="button" value="Export as Excel" onclick="preTally.StockReport.exportStockReport()">', height:30});

                dhxStockReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 7) {
//                        this.cells(id,ind).cell.title = 'Click here for More Details';
//                        return false;
//                    }
                });
//              onkeyup='preTally.StockReport.applyFilter(this.value);'

                $( ".stockrpt_select_filter" ).change(function() {
                    preTally.StockReport.applyFilter();
                });
                
                
                $( ".stockrpt_text_filter" ).keyup(function(value) {
                    if(filtrInterval) clearInterval(filtrInterval);
                    
                    filtrInterval = setInterval( function() { 
                        preTally.StockReport.applyFilter(); 
                        clearInterval(filtrInterval); 
                    }, 500);
                   
                });
                
                /*$( "#text_filter_branch" ).keyup(function(value) {
                    var mask = this.value;
                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
                    
                    filtrBranchInterval = setInterval( function() { 
                        preTally.StockReport.applyFilterBranch(mask); 
                        clearInterval(filtrBranchInterval); 
                    }, 500);
                   
                });*/
               
                var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_Stock.php&"+chartStockFilterParams), encodeURI(1)); 
                if(reportIE.xmlDoc.responseText != null) {
 
                    var reportIE_Response = $.parseJSON( reportIE.xmlDoc.responseText );
//                    dhxStockReportsForm.setItemValue("INCOME", reportIE_Response[0].value);
//                    dhxStockReportsForm.setItemValue("EXPENSE", reportIE_Response[1].value);
                    
                    //$('.tb_rpt_cob').html('OPENING BALANCE : '+Math.abs(reportIE_Response[2].value).toFixed(2));
                    
                    $('.tb_rpt_oldstock').html('OLD STOCK   : '+reportIE_Response[0].value);
                    $('.tb_rpt_stockbuss').html('BUSINESS   : '+reportIE_Response[1].value);
                    $('.tb_rpt_stockinc').html('AMOUNT RECEIVED  : '+reportIE_Response[2].value);
                    $('.tb_rpt_stockstock').html('CURRENT STOCK  : '+reportIE_Response[4].value);
                    
                    if(unescape(JGG1P3bDnUSDL6Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) 
                        $('.tb_rpt_stockexp').html('AMOUNT SPENT  : '+reportIE_Response[3].value);
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
//                    dhxStockReportsForm.setItemValue("PL_Data", Math.abs(pl));
//                    $('.tb_rpt_pl').html(plText+' : '+Math.abs(pl.toFixed(2)));
                    

                    dhxStockReportChart.clearAll();
                    dhxStockReportChart.parse(reportIE_Response,"json");
                    
                    $('input[name=INCOME]').formatCurrency();
                    $('input[name=EXPENSE]').formatCurrency();
                    /*$('input[name=PL_Data]').formatCurrency();
                    $('input[name=PL_Data]').val(plText +'   : ' + $('input[name=PL_Data]').val());*/
                }
                
                var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_Stock.php&"+chartStockFilterParams), encodeURI(1));
                if (chartIE.xmlDoc.responseText != null) {

                    ptStockReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);

                    chartStock_Response_OB = $.parseJSON( chartData[0] );
                    chartStock_Response_CB = $.parseJSON( chartData[1] );

                    var actvId = ptStockRprtsTabbar.getActiveTab();
                    if(actvId == 'a2') preTally.StockReport.reLoadSHGrid();
                    if(actvId == 'a3') preTally.StockReport.reLoadGraph();
                } 
               
                
                dhxStockReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportStockData.php&"+chartStockFilterParams), function() {
                          $('.tb_cnt_tot').html("# : "+dhxStockReportsGrid.getUserData("", "TL_Count")+" ");
                });
                
                dhxStockReportsGrid.attachEvent("onRowSelect", function(id,ind){
                    if(unescape(JGG1P3bDnUSDL6Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                        if(ind != 8) $( ".targetStock_"+id ).click();
                    }else {
                        if(ind != 7) $( ".targetStock_"+id ).click();
                    }
                    
                });

                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
            }
        },
        applyFilter:  function(){
            
            var filterValue = new Array($('#itmSRF').val(), $('#brnSRF').val(),$('#addSRF').val(),$( "#shSRF" ).val());
            dhxStockReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxStockReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportStockData.php&"+chartStockFilterParams+"&filter="+filterValue), function() { 
                 $('.tb_cnt_tot').html("# : "+dhxStockReportsGrid.getUserData("", "TL_Count")+" ");
                 preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
//        applyFilterBranch:  function(value){
//            
//                dhxStockReportsGrid.clearAll();
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                dhxStockReportsGrid.clearAndLoad("requisites/reportStockData.php?"+encrypt(chartStockFilterParams)+"&LC_Name="+value, function() {preTally.Settings.progressOff(true, dhxLayout, null);});
//                
//        },
        reLoadGraph: function() {
            var IE_Type = ptStockReportsToolbar.getListOptionSelected('ieType');
            
            if(reportDetailsGrid) reportDetailsGrid.destructor();
            
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("Legend, Category");
            reportDetailsGrid.setInitWidths("60,*");
            reportDetailsGrid.setColAlign("centre,left");
            reportDetailsGrid.setColTypes("cp,ed");
            reportDetailsGrid.enableTooltips("false,false");
            
            reportDetailsGrid.init();

            if(dhxWins.window("chartOtherDetails")) dhxWins.window("chartOtherDetails").close(); 
            reportStockSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';   
            if(IE_Type === 'IE_I'){ 
                reportStockSubHeadChart.parse(chartStock_Response_OB,"json");
                legendGridData = chartStock_Response_OB;
            }
            if(IE_Type === 'IE_E') {
                reportStockSubHeadChart.parse(chartStock_Response_CB,"json");
                legendGridData = chartStock_Response_CB;
            } 
             
            var legendData  = new Object();
            var rows        = [];
            $.each(legendGridData, function(index, value) {
                rows.push({
                    id  : index,
                    data: [value.color, value.category]
                });
            });
            legendData.rows = rows;
            reportDetailsGrid.parse(legendData,"json"); 
            
        },
        chartOtherDetails : function(ie) {
            preTally.Settings.progressOn(false, dhxLayout, 'b'); 
            dhxWins.createWindow('chartOtherDetails', 400, 100, 600, 350);
            dhxWins.window('chartOtherDetails').setText('Other Category in Chart');
            dhxWins.window('chartOtherDetails').button("minmax1").hide();
            dhxWins.window('chartOtherDetails').button("minmax2").hide();
            dhxWins.window('chartOtherDetails').button("park").hide();
            
            chartOtherDetailsGrid = dhxWins.window('chartOtherDetails').attachGrid();
            chartStockFilterParams += "&ie="+ie;
            
            chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartCashOCOther.php&"+chartStockFilterParams), function() {
                preTally.Settings.progressOff(false, dhxLayout, 'b');
            });
         
            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
                $('#stockrpt_text_filter').val('');
                dhxStockReportsGrid.clearAll();
                data=chartOtherDetailsGrid.cells(id,1).getValue();
                
                
                dhxStockReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportStockData.php&"+chartStockFilterParams+"&SHName="+data), function() { 
                    $('.tb_cnt_tot').html("# : "+dhxStockReportsGrid.getUserData("", "TL_Count")+" ");
                });
                
            });
//            chartOtherDetailsGrid.loadXML("requisites/chartIEOther.php?"+encrypt(chartStockFilterParams), function() {
//                preTally.Settings.progressOff(false, dhxLayout, 'b');
//            });
//            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
//                var data=chartOtherDetailsGrid.cells(id,1).getValue(); 
//                dhxStockReportsGrid.filterBy(3,function(data){                        
//                        return data==chartOtherDetailsGrid.cells(id,1).getValue();      
//                    });
//            }) ;

        },
        showDetailData : function(inp,TR_Id){
          
            if (!StockReport_DetailDataPop) {
                StockReport_DetailDataPop = new dhtmlXWindows();  
            }
//            dhxDescWin = new dhtmlXWindows();           
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);            
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;      
            
            var rptDetailsWin = StockReport_DetailDataPop.createWindow("wins_desc", x, y, 950, 300);    
            rptDetailsWin.button("minmax1").hide();
            rptDetailsWin.button("minmax2").hide();
            rptDetailsWin.button("park").hide();
            rptDetailsWin.center();
            rptDetailsWin.setModal(true);
            rptDetailsWin.setText("Track Entries in Detail");   
            var DSDetailsGrid = rptDetailsWin.attachGrid();
            DSDetailsGrid.attachEvent("onXLS",function(){
                preTally.Settings.progressOn(true, rptDetailsWin, null);
            });
            DSDetailsGrid.attachEvent("onXLE",function(){
                 preTally.Settings.progressOff(true, rptDetailsWin, null);
            });
            
            var params = "trname=" + TR_Id;
            DSDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/report_StockPop.php&"+ params));
            
                
            
            /*preTally.Settings.progressOn(false, dhxStockReportDetailLayout, 'c');
            if (!StockReport_DetailDataPop) {
                StockReport_DetailDataPop = new dhtmlXPopup({mode: "left"});   
                
            }
            if (StockReport_DetailDataPop.isVisible()) {
                StockReport_DetailDataPop.hide();
            } 

            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;           
            var rptDetailsPop = StockReport_DetailDataPop.attachGrid();
            //rptDetailsPop.style.backgroundColor="red";
            params = "trname=" + TR_Id;
            rptDetailsPop.loadXML("requisites/report_StockPop.php?"+encrypt(params), function() {
                StockReport_DetailDataPop.show(x, y, w, h);
                preTally.Settings.progressOff(false, dhxStockReportDetailLayout, 'c');
//                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
//                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
//                column0.style.borderRight = "1px solid #a4bed4";
                //column1.style.paddingLeft = "6px";
                //console.log(column0);               
                
                
            });*/
            
            $('.targetStock_'+TR_Id).unbind();
        },
        hideDetailData : function(){
            if (StockReport_DetailDataPop.isVisible()) {
                StockReport_DetailDataPop.hide();
            } 
        },
        exportStockReport : function() {
            
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = ptStockRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = ptStockRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TrackIDFilter'] = $('#itmSRF').val();
            exportfilterValue['BranchFilter'] = $('#addSRF').val();
            exportfilterValue['AddedByFilter'] = $('#brnSRF').val();
            exportfilterValue['amount'] = $('#brnSRF').val();
            exportfilterValue['r'] = rptStockFilterID;
            exportfilterValue['report_type'] = "stocks";
            exportfilterValue['shCRF'] = $( "#shSRF" ).val();
            preTally.Settings.progressOn(true, dhxLayout, null);          
            $.post(
                preTally.Initialize.encryptURL('warehouse/ReportExcelExport.php'),
                { filter : exportfilterValue },                
                function(data) { 
                    fileName = data.split("XL_");                    
                    if(fileName[1]) {
                         document.location ="uploads/excelFile/"+fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
            });
            
        }
    };
})(jQuery, this);