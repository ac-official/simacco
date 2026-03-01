;(function($, window, undefined) {
    preTally.BranchBSReports = {
        view_BranchBSReports: function() {
            if (!dhxMiddleBlockTabs.cells("view_branchbsreports")) {
                reportBranchBSInit=1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details"); 
                dhxMiddleBlockTabs.addTab("view_branchbsreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Bank Transaction Summary <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupBankTrans'/>", 300);
                dhxMiddleBlockTabs.tabs("view_branchbsreports").setActive();
                $(".popupBankTrans").click(function(){
                    preTally.BranchBSReports.filterReport(ofzBranchRprtTree.getSelectedItemId());
                    /*$(this).data('clicked', true); 
                    x = getAbsoluteLeft(this);
                    y = getAbsoluteTop(this); 
                    w = this.offsetWidth;
                    h = this.offsetHeight;
                    var myPop = new dhtmlXPopup();
                    myPop.attachHTML("Stock Summary Report");
                    myPop.show(x ,y,w,h);*/
                })
                dhxBranchOBReportsLayout =  dhxMiddleBlockTabs.cells("view_branchbsreports").attachLayout("2U");
                dhxBranchOBReportsLayout.cells("a").setWidth(200);
                dhxBranchOBReportsLayout.cells("a").hideHeader();
                dhxBranchOBReportsLayout.cells("a").fixSize(true, true);    
                dhxBranchOBReportsLayout.cells("b").fixSize(true, true);  
                dhxBranchOBReportsLayout.cells('b').hideArrow();
                ofzBranchRprtTree = dhxBranchOBReportsLayout.cells("a").attachTree();
                
                ofzBranchRprtTree.attachEvent("onXLS", function(){
                    preTally.Settings.progressOn(false, dhxBranchOBReportsLayout, 'a');
                });
                ofzBranchRprtTree.attachEvent("onXLE", function(){
                    preTally.Settings.progressOff(false, dhxBranchOBReportsLayout, 'a');
                });
                
                ofzBranchRprtTree.enableHighlighting(true);                 
                ofzBranchRprtTree.setOnClickHandler(preTally.BranchBSReports.filterReport);
                ofzBranchRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                ofzBranchRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/ofzBankRptTree.php&f=BS&r=OF_O"), function(){
                    var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                    if(bsl){
                        ofzBranchRprtTree.selectItem(bsl,'onClick',true);
                        preTally.BranchBSReports.filterReport(bsl);
                     
                    }	
                }); 
                dhxBranchBSTreeTlbr = dhxBranchOBReportsLayout.cells("a").attachToolbar();
                dhxBranchBSTreeTlbr.setIconsPath("images/icon/");
                dhxBranchBSTreeTlbr.addText('odhType_Rptz', '1', 'Branch Map' );
                dhxBranchBSTreeTlbr.setAlign('right');
                dhxBranchBSRprtTlbr = dhxBranchOBReportsLayout.cells("b").attachToolbar();                
                dhxBranchBSRprtTlbr.setIconsPath("images/icon/default_18/");
                
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
                
                dhxBranchBSRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                dhxBranchBSRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                dhxBranchBSRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
                dhxBranchBSRprtTlbr.addSeparator();
                          
                dhxBranchBSRprtTlbr.addText("text_from", null, "From");
                dhxBranchBSRprtTlbr.addInput("rpt_date_from", null, "", 75);
                dhxBranchBSRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                dhxBranchBSRprtTlbr.addSeparator();
                
                dhxBranchBSRprtTlbr.addText("text_till", null, "Till");
                dhxBranchBSRprtTlbr.addInput("rpt_date_till", null, "", 75);
                dhxBranchBSRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                dhxBranchBSRprtTlbr.addSeparator();
                
                dhxBranchBSRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                dhxBranchBSRprtTlbr.addSeparator();
              
                dhxBranchBSRprtTlbr.addButton("excel_export", null , "Export", "excel.png");

                var ptRpTb_Inp_Frm = dhxBranchBSRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(dhxBranchBSRprtTlbr.getValue("rpt_date_till")) preTally.BranchBSReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = dhxBranchBSRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(dhxBranchBSRprtTlbr.getValue("rpt_date_from")) preTally.BranchBSReports.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                dhxBranchBSRprtTlbr.attachEvent("onClick", function(id){  
                   var pId = dhxBranchBSRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        dhxBranchBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBranchBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BranchBSReports.filterReport(rptBranchBSFilterID);
                    }
                    if(pId == 'rpt_month_filter') {
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(dhxBranchBSRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBranchBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //dhxBranchBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BranchBSReports.filterReport(rptBranchBSFilterID);
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBranchBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBranchBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.BranchBSReports.filterReport(rptBranchBSFilterID);
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        dhxBranchBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBranchBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBranchBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BranchBSReports.filterReport(rptBranchBSFilterID);
                    }  
                    if(id == 'excel_export') {
                        preTally.BranchBSReports.exportBranchBalanceReport();
                    }
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                dhxBranchBSRprtTlbr.setAlign('right');
                // internal section added at 19-05-2025
                var tb_data_txt = '<div class="tb_data_txt_secl" style="line-height:15px !important;top:0px !important;height:36px;"">\
                                        <div class="totalbal_cnt_tot"># : 0</div>\
                                        <div class="totalbal_rpt_bob"> OP BALANCE : 0</div>\
                                        <div class="totalbal_rpt_inc">INCOME : 0</div>\
                                        <div class="totalbal_rpt_exp">EXPENSE : 0</div>\
                                        <div class="totalbal_rpt_bcb"> CL BALANCE : 0</div>\
                                        <div class="totalbal_int_inc" title="Internal Transfer Received"> INTL RECEIVED : 0</div>\
                                        <div class="totalbal_int_exp" title="Internal Transfer Paid"> INTL PAID : 0</div>\
                                    </div><div id="branchBS_paging" style="top:0px !important;float:left !important;width:100% !important;"></div>';
                
                var tbRptObj = dhxBranchOBReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 75             // custom height
                });
                
                ptBranchBSRprtsTabbar = dhxBranchOBReportsLayout.cells("b").attachTabbar();
                ptBranchBSRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptBranchBSRprtsTabbar.addTab("a3", "Report Visulization");
                ptBranchBSRprtsTabbar.tabs("a3").hide();
                ptBranchBSRprtsTabbar.tabs("a2").setActive();
                ptBranchBSRprtsTabbar.attachEvent("onSelect", function(id, last_id){  
                    if(id != "a1") {
                        if (BranchBS_DetailDataPop && BranchBS_DetailDataPop.isVisible()) { 
                            BranchBS_DetailDataPop.hide();
                        }
                        if(id == 'a2') preTally.BranchBSReports.reLoadSHGrid();
                        if(id == 'a3') preTally.BranchBSReports.reLoadGraph();
                        return true;
                    } 
                });
  
                dhxBranchBSReportsGrid = ptBranchBSRprtsTabbar.cells("a2").attachGrid();  
                dhxBranchBSReportsGrid.attachEvent("onFilterEnd", function(elements){ 
                    if(dhxBranchBSReportsGrid.getRowsNum() == 0 ) {
                        dhxBranchBSReportsGrid.addRow(0,['','No Records Found...','','',''],0); 
                        dhxBranchBSReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxBranchBSReportDetailLayout =  ptBranchBSRprtsTabbar.cells("a3").attachLayout("3U");
                dhxBranchBSReportDetailLayout.cells("b").setWidth(300);
                dhxBranchBSReportDetailLayout.cells("b").setHeight(180);
                dhxBranchBSReportDetailLayout.cells("b").hideHeader();
                dhxBranchBSReportDetailLayout.cells("a").setText("Income & Expense");
                dhxBranchBSReportDetailLayout.cells("c").setText("Details Of Income & Expense");
                    
                dhxBranchBSReportChart =  dhxBranchBSReportDetailLayout.cells("b").attachChart({                   
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

//                dhxBranchBSReportsForm = dhxBranchBSReportDetailLayout.cells("a").attachForm();
//                dhxBranchBSReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });
                 
                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptBranchBSReportsToolbar = dhxBranchBSReportDetailLayout.cells("c").attachToolbar();
                ptBranchBSReportsToolbar.setIconsPath("images/icon/");
                ptBranchBSReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true,2,'select');
                ptBranchBSReportsToolbar.setAlign('right');
                ptBranchBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptBranchBSReportsToolbar.disableItem('ieType');
                ptBranchBSReportsToolbar.attachEvent('onClick', function(id){
                    preTally.BranchBSReports.reLoadGraph();
                });

                reportBranchBSSubHeadChart = dhxBranchBSReportDetailLayout.cells("c").attachChart({      
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
            } else { 
                preTally.BranchBSReports.filterReport(ofzBranchRprtTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("view_branchbsreports").setActive();
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
            reportDetailsGrid.enableTooltips("false,false,false,false,false");
            reportDetailsGrid.init();
            reportBranchBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();
            reportBranchBSSubHeadChart.parse(chartBranchBS_Response_OB,"json");
            reportBranchBSSubHeadChart.parse(chartBranchBS_Response_CB,"json");
            var LGD_Inc = chartBranchBS_Response_OB;
            var LGD_Exp = chartBranchBS_Response_CB;
            
            var LData   = new Object();
            var LGDrows = [];
            
            if((LGD_Inc.length !=0) || (LGD_Exp.length !=0))
            {    
                LGDrows.push({ id  : 1, data: ['' , "All",'', 'Income' ] });
                var LGCount = 2;
                $.each(LGD_Inc, function(index, value) {
                    LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/up_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Income' ,value.shId ] });
                    LGCount ++;
                });
                $.each(LGD_Exp, function(index, value) {
                    LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/down_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Expense' ,value.shId ] });
                    LGCount ++;
                });
            }
            LData.rows = LGDrows;
            reportDetailsGrid.parse(LData,"json");        
           // preTally.BranchBSReports.filterSubHead();
            reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
                preTally.BranchBSReports.filterSubHead(id);
            }) ;   
        },
        reLoadGraph: function() {
            var IE_Type = ptBranchBSReportsToolbar.getListOptionSelected('ieType');
            if(reportDetailsGrid) reportDetailsGrid.destructor();
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("Legend, Category");
            reportDetailsGrid.setInitWidths("60,*");
            reportDetailsGrid.setColAlign("centre,left");
            reportDetailsGrid.setColTypes("cp,ed");
            reportDetailsGrid.enableTooltips("false,false");
            reportDetailsGrid.init();
            if(dhxWins.window("chartOtherDetails")) dhxWins.window("chartOtherDetails").close(); 
            reportBranchBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';   
            if(IE_Type === 'IE_I'){ 
                reportBranchBSSubHeadChart.parse(chartBranchBS_Response_OB,"json");
                legendGridData = chartBranchBS_Response_OB;
            }
            if(IE_Type === 'IE_E') {
                reportBranchBSSubHeadChart.parse(chartBranchBS_Response_CB,"json");
                legendGridData = chartBranchBS_Response_CB;
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
        filterReport: function(id) { 
            subHeadData = "";
            var filtrInterval ;
            var filtrBranchInterval ;
            rptBranchBSFilterID = id;
            if(!rptBranchBSFilterID){
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                ptBranchBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                if(reportBranchBSInit == 1)
                {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                    if(bsp == 4)
                    {   var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', tDate);
                        dhxBranchBSRprtTlbr.setItemText('rpt_month_filter',m_names[month]);
                        reportBranchBSInit=0;
                    }
                    else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy"); 
                        dhxBranchBSRprtTlbr.setValue('rpt_date_till', cDate);
                        dhxBranchBSRprtTlbr.setValue('rpt_date_from', cDate);
                        dhxBranchBSRprtTlbr.setItemText('rpt_day_filter', 'Today');
                        reportBranchBSInit=0;
                    }
                }
                // new options added at 19-05-2025
                var transtypeOpt = preTally.BranchBSReports.transactionOptions(1);
                chartBranchBSFilterParams = 'r='+id+'&f='+dhxBranchBSRprtTlbr.getValue("rpt_date_from")+'&t='+dhxBranchBSRprtTlbr.getValue("rpt_date_till");
                dhxBranchBSReportsGrid.destructor();                
                dhxBranchBSReportsGrid = ptBranchBSRprtsTabbar.cells("a2").attachGrid();   
                dhxBranchBSReportsGrid.setHeader("Msg, Edit,SlNo,<select style = 'width:60px;' id = 'BTRptIE' onchange='preTally.BranchBSReports.applyFilter(this.value);'>"+transtypeOpt+"</select>,<input type='text' id = 'BTRptItm' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shBR' >,<input type='text' id = 'BTRptAmt' style='width: 90%;' placeholder='Amount'>,<input type='text' id = 'BTRptBrnch' style='width: 90%;' placeholder = 'Branch' ");
                dhxBranchBSReportsGrid.setInitWidths("60,60,60,80,*,0,100,150")
                dhxBranchBSReportsGrid.setColAlign("center,center,center,center,left,left,right,left")
                dhxBranchBSReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                dhxBranchBSReportsGrid.setColSorting("na,na,na,na,na,na,na,na")
                dhxBranchBSReportsGrid.init();                
                dhxBranchBSReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxBranchBSReportsGrid.setSkin("dhx_skyblue")
                dhxBranchBSReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxBranchBSReportsGrid.enablePaging(true,50,5,"branchBS_paging",true);
                dhxBranchBSReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                
                //dhxBranchBSReportsGrid.enableSmartRendering(true,50);
                dhxBranchBSReportsGrid.enableColSpan(true);
                dhxBranchBSReportsGrid.enableTooltips("true,true,false,false,false,false");
//                dhxBranchBSReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 0) {
//                        this.cells(id,ind).cell.title = 'Click here to sent message';
//                        return false;
//                    }
//                    if(ind == 1) {
//                        this.cells(id,ind).cell.title = 'Click here to edit';
//                        return false;
//                    }
//                });

                $( "#BTRptItm" ).keyup(function(value) {
                    var mask = this.value;
                    if(filtrInterval) clearInterval(filtrInterval);
                    filtrInterval = setInterval( function() { 
                        preTally.BranchBSReports.applyFilter(mask); 
                        clearInterval(filtrInterval); 
                    }, 500);
                });
                
                $( "#BTRptAmt" ).keyup(function(value) {
                    var mask = this.value;
                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
                    filtrBranchInterval = setInterval( function() { 
                        preTally.BranchBSReports.applyFilter(mask); 
                        clearInterval(filtrBranchInterval); 
                    }, 500);
                });
               
                $( "#BTRptBrnch" ).keyup(function(value) {
                    var mask = this.value;
                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
                    filtrBranchInterval = setInterval( function() { 
                        preTally.BranchBSReports.applyFilter(mask); 
                        clearInterval(filtrBranchInterval); 
                    }, 500);
                });
                var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_BranchOC.php&"+chartBranchBSFilterParams), encodeURI(1)); 
                if(reportIE.xmlDoc.responseText != null) {
                    var reportIE_Response = $.parseJSON( reportIE.xmlDoc.responseText );
//                    dhxBranchBSReportsForm.setItemValue("INCOME", reportIE_Response[0].value);
//                    dhxBranchBSReportsForm.setItemValue("EXPENSE", reportIE_Response[1].value);
                    $('.totalbal_rpt_bob').html('OP BALANCE : '+reportIE_Response[2].value);
                    $('.totalbal_rpt_inc').html('INCOME : '+reportIE_Response[0].value);
                    $('.totalbal_rpt_exp').html('EXPENSE : '+reportIE_Response[1].value);
                    $('.totalbal_rpt_bcb').html('CL BALANCE : '+reportIE_Response[3].value);
                    // newly added 19-05-2025
                    $('.totalbal_int_inc').html('INTL RECED : '+reportIE_Response[4].value);
                    $('.totalbal_int_exp').html('INTL PAID : '+reportIE_Response[5].value);
                    

                    var pl = reportIE_Response[0].value - reportIE_Response[1].value;
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
                    }

//                    dhxBranchBSReportsForm.setItemValue("PL_Data", Math.abs(pl));
                    $('.tb_rpt_pl').html(plText+' : '+Math.abs(pl.toFixed(2)));
                    dhxBranchBSReportChart.clearAll();
                    dhxBranchBSReportChart.parse(reportIE_Response,"json");
                    
                    $('input[name=INCOME]').formatCurrency();
                    $('input[name=EXPENSE]').formatCurrency();
                    $('input[name=PL_Data]').formatCurrency();
                    $('input[name=PL_Data]').val(plText +'   : ' + $('input[name=PL_Data]').val());
                }
                var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_BranchOC.php&"+chartBranchBSFilterParams), encodeURI(1));
                if (chartIE.xmlDoc.responseText != null) {
                    ptBranchBSReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);
                    chartBranchBS_Response_OB = $.parseJSON( chartData[0] );
                    chartBranchBS_Response_CB = $.parseJSON( chartData[1] );
                    var actvId = ptBranchBSRprtsTabbar.getActiveTab(); 
                    if(actvId == 'a2') preTally.BranchBSReports.reLoadSHGrid();
                    if(actvId == 'a3') preTally.BranchBSReports.reLoadGraph();
                } 
                dhxBranchBSReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBranchBSData.php&"+chartBranchBSFilterParams), function() {
                    $('.totalbal_cnt_tot').html("# : "+dhxBranchBSReportsGrid.getUserData("", "TL_Count")+" ");
                });
                 
                dhxBranchBSReportsGrid.attachEvent("onRowSelect", function(id,ind){
                    if(ind != 0 && ind != 1 && ind != 7) $( ".targetBranch_"+id ).click();
                });

                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
            }
        },
        filterSubHead:function (id){
           
            $("#shBR").val(reportDetailsGrid.cells(id,4).getValue());
            preTally.BranchBSReports.applyFilter();
            
//            if(id == "1") { 
//                $("#shBR").val('');
//            }
//            else
//                $("#shBR").val(reportDetailsGrid.cells(id,1).getValue());
//            preTally.BranchBSReports.applyFilter(); 
        },
        applyFilter:  function(value){ 
            var amount = $('#BTRptAmt').val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#BTRptIE').val(), $('#BTRptItm').val(), $('#BTRptBrnch').val(),$("#shBR").val(),amount);
            dhxBranchBSReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxBranchBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBranchBSData.php&"+chartBranchBSFilterParams+"&filter="+filterValue), function() { 
                $('.totalbal_cnt_tot').html("# : "+dhxBranchBSReportsGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
           
        },
        chartOtherDetails : function(ie) {
            preTally.Settings.progressOn(false, dhxLayout, 'b'); 
            if(!dhxWins.isWindow('chartOtherDetails')){
            dhxWins.createWindow('chartOtherDetails', 400, 100, 600, 350);
            dhxWins.window('chartOtherDetails').setText('Other Category in Chart');
            dhxWins.window('chartOtherDetails').button("minmax1").hide();
            dhxWins.window('chartOtherDetails').button("minmax2").hide();
            dhxWins.window('chartOtherDetails').button("park").hide();            
            chartOtherDetailsGrid = dhxWins.window('chartOtherDetails').attachGrid();
            }
            chartBranchBSFilterParams = chartBranchBSFilterParams+"&ie="+ie;
            chartOtherDetailsGrid.setColSorting("na,na,na");
            chartOtherDetailsGrid.enableTooltips("false,false,false");
            chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartBranchOCOther.php&"+chartBranchBSFilterParams), function() {
                preTally.Settings.progressOff(false, dhxLayout, 'b');
            });
         
            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
                dhxBranchBSReportsGrid.clearAll();
                
                $("#shBR").val(chartOtherDetailsGrid.getUserData(id,"shId"));
//                $("#shBR").val(id);
                preTally.BranchBSReports.applyFilter();
                
            });

        },
        showDetailData : function(inp,BS_Id,SH_Id){   
            preTally.Settings.progressOn(false, dhxBranchBSReportDetailLayout, 'c');
            if (!BranchBS_DetailDataPop) {
                BranchBS_DetailDataPop = new dhtmlXPopup({mode: "left"});   
            }
            if (BranchBS_DetailDataPop.isVisible()) {
                BranchBS_DetailDataPop.hide();
            } 
                
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;           
            var rptBranchDetailsPop = BranchBS_DetailDataPop.attachForm();
            var params = "SHID="+SH_Id+"&BSID="+BS_Id;
            rptBranchDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_BranchBSPop.php&"+params), function() {
                BranchBS_DetailDataPop.show(x, y, w, h);
                preTally.Settings.progressOff(false, dhxBranchBSReportDetailLayout, 'c');
                var column0 = rptBranchDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptBranchDetailsPop.getColumnNode("fieldsetname", 1);
                column0.style.borderRight = "1px solid #a4bed4";
            });
            $('.target_'+BS_Id).unbind();
        },
        hideDetailData : function(){
            if (BranchBS_DetailDataPop.isVisible()) {
                BranchBS_DetailDataPop.hide();
            } 
        },
        sendCorrectionMsg: function(inp, BS_Id, SH_Id) {
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
            rptMsgForm.loadStruct(preTally.Initialize.encryptURL("requisites/sendCorrectionMsg.php"), function() {                
                rptMsgForm.setItemValue("US_Id", dhxBranchBSReportsGrid.getUserData(BS_Id, "US_Id"));
                rptMsgForm.setItemValue("MSG_To", dhxBranchBSReportsGrid.getUserData(BS_Id, "US_Name"));
                rptMsgForm.setItemValue("Entry", dhxBranchBSReportsGrid.getUserData(BS_Id, "Entry"));
                rptMsgForm.setItemValue("BS_Id", BS_Id);
                rptMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptMsgWin, null);
                rptMsgForm.attachEvent("onButtonClick", function(name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptMsgWin, null);
                            param = "type=branch";
                            rptMsgForm.send(preTally.Initialize.encryptURL("warehouse/sendCorrectionMsg.php&"+param), function(loader, response) {
                                preTally.Settings.progressOff(true, rptMsgWin, null);
                                if(response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    rptMsgForm.clear();
                                    dhxMsgWin.window("rptMsgWin_" + BS_Id).close();
                                }
                                else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if(name == 'addRecpt') {
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

                        var listRecipientsXML= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked="+rptMsgForm.getItemValue('US_Id')); 
                        listUserGrid.parse(listRecipientsXML.xmlDoc.responseText,function(){
                            preTally.Settings.progressOff(true, recpWin, null);
                        });

                        recpWin.attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.BranchBSReports.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                    } else {
                        rptMsgForm.resetValidateCss();
                        rptMsgForm.clear();
                        dhxMsgWin.window("rptMsgWin_"+ BS_Id).close();
                    }
                });
            });
        },
        addRecipients: function() {           
            var userDetails = []; 
            var userIds     = [];
            listUserGrid.forEachRow(function(rId){
                if(listUserGrid.cells(rId,6).getValue() == 1) { // if checked
                    userDetails.push(listUserGrid.cells(rId,2).getValue());
                    userIds.push(rId);
                }
            });
            rptMsgForm.setItemValue("MSG_To",userDetails);
            rptMsgForm.setItemValue('US_Id',userIds);
            dhxUserWin.window("addRecpWin").close();
        },
        editBalSheetDetailsFromMsg : function (inp, BS_Id, SH_Id) {
            dhxBranchBSMsgDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);

            branchBSMsgDetailsWin = dhxBranchBSMsgDetails.createWindow("branchBSDetailsWinMsg_" + BS_Id, x, y, 900, 400);
            branchBSMsgDetailsWin.center();
            branchBSMsgDetailsWin.button("minmax1").hide();
            branchBSMsgDetailsWin.button("minmax2").hide();
            branchBSMsgDetailsWin.button("park").hide();
            branchBSMsgDetailsWin.setModal(true);
            branchBSMsgDetailsWin.setText("Edit Details");
            branchBSMsgDetailsForm = branchBSMsgDetailsWin.attachForm();
            preTally.Settings.progressOn(true, branchBSMsgDetailsWin, null);

            var params = "SHID="+SH_Id+"&BSID="+BS_Id;
            branchBSMsgDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportCashBSEditMsg.php&"+params), function() {

                preTally.Settings.progressOff(true, branchBSMsgDetailsWin, null);
                var IT_Note;
                var DS_Note;
                var BS_MHType = branchBSMsgDetailsForm.getItemValue("HiddenMH_Type");
                if(BS_MHType == 1) {
                    IT_Note = 'NAME OF THE INCOME';
                    DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                } else {
                    IT_Note = 'NAME OF THE EXPENSE';
                    DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                }

                branchBSMsgDetailsForm.setNote('MH_Type', { text: "ENTRY TYPE", width : "100" });
                branchBSMsgDetailsForm.setNote('IT_Id', { text: IT_Note, width : "100" });
                branchBSMsgDetailsForm.setNote('BS_Description', { text: DS_Note , width : "200" });
                branchBSMsgDetailsForm.setNote('TR_Id', { text: "TRACK" , width : "100" });

                var BS_MHCombo      =   branchBSMsgDetailsForm.getCombo("MH_Type");
                var BS_ITCombo      =   branchBSMsgDetailsForm.getCombo("IT_Id");
                var BS_DSCombo      =   branchBSMsgDetailsForm.getCombo("BS_Description");
                var BS_TRCombo      =   branchBSMsgDetailsForm.getCombo("TR_Id");
                var BS_BNKCombo     =   branchBSMsgDetailsForm.getCombo("BNK_Id");
                var BS_BBCombo      =   branchBSMsgDetailsForm.getCombo("BB_Id");
                var BS_BACombo      =   branchBSMsgDetailsForm.getCombo("BA_Id");
                var BS_BChqCombo    =   branchBSMsgDetailsForm.getCombo("CHQ_Number");

                if(branchBSMsgDetailsForm.getItemValue("HiddenTR_Id") == 0) {
                    branchBSMsgDetailsForm.hideItem("TR_Id");
                    branchBSMsgDetailsForm.setRequired("TR_Id",false);
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                } 

                BS_ITCombo.enableFilteringMode(true);
                BS_DSCombo.enableFilteringMode(true);

                BS_ITCombo.setOptionWidth(400);
                BS_DSCombo.setOptionWidth(300);

                BS_MHCombo.attachEvent("onClose", function() {
                    BS_ITCombo.clearAll();
                    BS_ITCombo.setComboText("");
                    BS_ITCombo.setComboValue("");
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText("");
                    BS_DSCombo.setComboValue("");

                    BS_MHType = BS_MHCombo.getSelectedValue();
                    var params="type="+BS_MHType;
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&"+params));
                    if(BS_MHType == 1) {
                        IT_Note = 'NAME OF THE INCOME';
                        DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                    } else {
                        IT_Note = 'NAME OF THE EXPENSE';
                        DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                    }
                    branchBSMsgDetailsForm.setNote('IT_Id', { text: IT_Note , width : "100" });
                    branchBSMsgDetailsForm.setNote('BS_Description', { text: DS_Note , width : "200" });
                    branchBSMsgDetailsForm.hideItem("TR_Id");
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                });

                BS_ITCombo.attachEvent("onClose", function() { 
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText('');
                    var BS_ITId = BS_ITCombo.getSelectedValue();                        
                    if (!isNaN(BS_ITId) && (BS_ITId != 0)) {
                        var params="IT_Id=" + BS_ITId;
                        branchBSMsgDetailsForm.setItemValue("IT_Flag","0");  
                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&"+params)
                        }).done(function(data) { 
                            BS_TRCombo.setComboValue('0');
                            BS_TRCombo.setComboText('0');
                            BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&"+params), function() {});
                            if(!branchBSMsgDetailsForm.isItemHidden("TR_Id")) {
                                branchBSMsgDetailsForm.hideItem("TR_Id");
                                BS_TRCombo.setComboValue('0');
                                BS_TRCombo.setComboText('0');
                                branchBSMsgDetailsForm.setRequired("TR_Id",false)
                            }
                            if(data == 1){
                                var filtrInterval;
                                branchBSMsgDetailsForm.showItem("TR_Id"); 
                                BS_TRCombo.setComboValue('');
                                BS_TRCombo.setComboText('');
                                branchBSMsgDetailsForm.setRequired("TR_Id",true);
                            }                           
                        });
                    }
                });

                branchBSMsgDetailsForm.setItemValue("MH_Type",branchBSMsgDetailsForm.getItemValue("HiddenMH_Type"));
                if (branchBSMsgDetailsForm.isItem("IT_Id")) { 
                    var params = "type=" + branchBSMsgDetailsForm.getItemValue("MH_Type");                    
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&"+params),function(){
                        if(branchBSMsgDetailsForm.getItemValue("IT_Status")!= '4' && branchBSMsgDetailsForm.getItemValue("IT_Status")!= '0'){
                        branchBSMsgDetailsForm.setItemValue("IT_Id",branchBSMsgDetailsForm.getItemValue("HiddenIT_Id"));
                        }else{
                            BS_ITCombo.setComboText(branchBSMsgDetailsForm.getItemValue("IT_Name"));
                            branchBSMsgDetailsForm.setItemValue("IT_Flag","del_item");                             
                        } 
                    });
                }

                if (branchBSMsgDetailsForm.isItem("BS_Description")) { 
                    var params = "IT_Id=" + branchBSMsgDetailsForm.getItemValue("HiddenIT_Id")+"&DS_Id=" + branchBSMsgDetailsForm.getItemValue("DS_Id")+"&updateType=rpt";
                    BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&"+params), function() {
                       branchBSMsgDetailsForm.setItemValue("BS_Description",branchBSMsgDetailsForm.getItemValue("DS_Id"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("TR_Id")) { 
                    BS_TRCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/tracks.php"));
                    var params = "mask=" + branchBSMsgDetailsForm.getItemValue("TR_Track");
                    BS_TRCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&"+params),function(){
                        branchBSMsgDetailsForm.setItemValue("TR_Id",branchBSMsgDetailsForm.getItemValue("HiddenTR_Id"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("PM_Id")) {
                    branchBSMsgDetailsForm.hideItem("BS_PaidDate");
                    branchBSMsgDetailsForm.hideItem("BS_PayType");
                    branchBSMsgDetailsForm.hideItem("BNK_Id");
                    branchBSMsgDetailsForm.hideItem("BB_Id");
                    branchBSMsgDetailsForm.hideItem("BA_Id");
                    branchBSMsgDetailsForm.hideItem("CHQ_Number");
                    branchBSMsgDetailsForm.hideItem("BS_Transaction");
                    branchBSMsgDetailsForm.hideItem("BS_PayersBank");
                    branchBSMsgDetailsForm.hideItem("BS_PayersChQ");
                    var item_list = new Array( "BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "CHQ_Number", "BA_Id","BS_PayersChQ", "BS_PayersBank" );
                    var patmentMode = branchBSMsgDetailsForm.getCombo("PM_Id");
                    patmentMode.setComboValue(branchBSMsgDetailsForm.getUserData("PM_Id","cId")); 

                    if (branchBSMsgDetailsForm.getUserData("PM_Id","cId") == '2'){
                        for(i=0;i<=10;i++){
                           branchBSMsgDetailsForm.showItem(item_list[i]); 
                        }
                        branchBSMsgDetailsForm.setItemValue("PM_Id",branchBSMsgDetailsForm.getUserData("PM_Id","cId"));

                        if (branchBSMsgDetailsForm.isItem("BNK_Id")) {  
                            branchBSMsgDetailsForm.setItemValue("BNK_Id",branchBSMsgDetailsForm.getUserData("BNK_Id","cId"));
                        }
                        if (branchBSMsgDetailsForm.isItem("BB_Id")) {  
                            var params = "Bnk_Id=" + branchBSMsgDetailsForm.getUserData("BNK_Id","cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&"+params), function(xml){
                                var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                branchBSMsgDetailsForm.setItemValue("BB_Id",BBName);
                                BS_BBCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BB_Id","cId"));
                            });
                        }
                        if (branchBSMsgDetailsForm.isItem("BA_Id")) {  
                            var params="BB_Id=" + branchBSMsgDetailsForm.getUserData("BB_Id","cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&"+params), function(xml){
                                var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                branchBSMsgDetailsForm.setItemValue("BA_Id",BAName);
                                BS_BACombo.setComboValue(branchBSMsgDetailsForm.getUserData("BA_Id","cId"));
                            });
                        }
                        if (branchBSMsgDetailsForm.isItem("CHQ_Number")) { 
                            var params="BA_Id=" + branchBSMsgDetailsForm.getUserData("BA_Id","cId");
                            BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);

                            BS_BChqCombo.addOption([[branchBSMsgDetailsForm.getUserData("CHQ_Number","cId"),branchBSMsgDetailsForm.getUserData("CHQ_Number","cValue")]]);
                            BS_BChqCombo.setComboValue(branchBSMsgDetailsForm.getUserData("CHQ_Number","cId"));
                        }
                    }

                    patmentMode.attachEvent("onChange", function() {
                        var pmMode = patmentMode.getSelectedValue();
                        for(i=0;i<=10;i++){
                            if (pmMode == '1'){ 
                                branchBSMsgDetailsForm.hideItem(item_list[i]); }
                            else {  
                                branchBSMsgDetailsForm.showItem(item_list[i]); }
                        }
                    });
                    if (branchBSMsgDetailsForm.isItem("BS_PayType")) {
                        BS_PayTypeCombo = branchBSMsgDetailsForm.getCombo("BS_PayType");
                        branchBSMsgDetailsForm.setItemValue("BS_PayType",branchBSMsgDetailsForm.getUserData("BS_PayType","cId"));
                        BS_PayTypeCombo.attachEvent("onChange", function() {
                            var pmType = BS_PayTypeCombo.getSelectedValue();
                            if(pmType == '2'){
                                branchBSMsgDetailsForm.hideItem("BS_Transaction");
                                branchBSMsgDetailsForm.showItem("CHQ_Number");
                            }
                            if(pmType == '3'){
                                branchBSMsgDetailsForm.setItemLabel("BS_Transaction","DD Number" );
                                branchBSMsgDetailsForm.showItem("BS_Transaction");
                                branchBSMsgDetailsForm.hideItem("CHQ_Number");
                                //branchBSMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                                branchBSMsgDetailsForm.clearValidation('CHQ_Number'); 
                            }
                            if(pmType == '4' || pmType == '5' || pmType == '6'){
                                branchBSMsgDetailsForm.setItemLabel("BS_Transaction","Transaction Id" );
                                branchBSMsgDetailsForm.showItem("BS_Transaction");
                                branchBSMsgDetailsForm.hideItem("CHQ_Number");
                                branchBSMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                            }
                        });
                    }
                }

                if (branchBSMsgDetailsForm.isItem("BB_Id")) {
                    BS_BBCombo.readonly(true);
                    BS_BACombo.readonly(true);
                    BS_BNKCombo.attachEvent("onClose", function() {
                        BS_BBCombo.clearAll();
                        BS_BACombo.clearAll();
                        BS_BBCombo.setComboText('');
                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');
                        if(branchBSMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }
                        var bnk_id = BS_BNKCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BS_BBCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&"+params), function() {
                            });
                        }
                        branchBSMsgDetailsForm.setItemValue("updateType","");
                    });

                    BS_BBCombo.attachEvent("onClose", function() {
                        BS_BACombo.clearAll();                        
                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if(branchBSMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }
                        var bnk_acc_id = BS_BBCombo.getSelectedValue();
                        if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                            var params="BB_Id=" + bnk_acc_id;
                            BS_BACombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&"+params), function() {
                            });
                        }
                        branchBSMsgDetailsForm.setItemValue("updateType","");
                    });

                    BS_BACombo.attachEvent("onClose", function() {
                        if(branchBSMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');

                            var BNK_AC_Id = BS_BACombo.getSelectedValue();
                            if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                var params="BA_Id=" + BNK_AC_Id;
                                BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);
                            }
                        }
                        branchBSMsgDetailsForm.setItemValue("updateType","");
                    });
                }
                if (branchBSMsgDetailsForm.isItem("CHQ_Number")){
                    BS_BChqCombo.attachEvent("onClose", function() {
                        branchBSMsgDetailsForm.setItemValue("updateType","");
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_User")) { 
                    UserCombo  =  branchBSMsgDetailsForm.getCombo("BS_User");
                    LCCombo    =  branchBSMsgDetailsForm.getCombo("BS_PrchsdFor");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                        var prucForName = LCCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("BS_PrchsdFor",prucForName);
                        LCCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_PrchsdFor","cId"));
                    });

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var User_Name = UserCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("BS_User",User_Name);
                        UserCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_User","cId"));
                    });

                    LCCombo.attachEvent("onClose", function() {
                        UserCombo.clearAll();
                        StaffCombo.clearAll();
                        UserCombo.setComboText('');
                        StaffCombo.setComboText('');
                        var BS_Office = LCCombo.getSelectedValue(); 
                        if ((BS_Office) && (BS_Office != 0)) { 
                            var params="LC_Id="+ BS_Office;
                            UserCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&"+params)); 
                        }
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_StaffId")) { 
                        UserCombo  =  branchBSMsgDetailsForm.getCombo("BS_User");
                        StaffCombo =  branchBSMsgDetailsForm.getCombo("BS_StaffId");
                        StaffCombo.readonly(true);

                        UserCombo.attachEvent("onClose", function() {
                            StaffCombo.clearAll();
                            StaffCombo.setComboText('');
                            var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                            if ((BS_User) && (BS_User != 0)) {
                                var params="Emp_Id=" + BS_User;
                                StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&"+params), function() {
                                });
                            }
                        });
                }
                if (branchBSMsgDetailsForm.isItem("LC_Id")) {  
                    var LC_IdCombo = branchBSMsgDetailsForm.getCombo("LC_Id");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                        var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("LC_Id",LC_Name);
                        LC_IdCombo.setComboValue(branchBSMsgDetailsForm.getUserData("LC_Id","cId"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_IEByLC")) {  
                    var ieByLCCombo = branchBSMsgDetailsForm.getCombo("BS_IEByLC");
                    var ieByLCid   = branchBSMsgDetailsForm.getUserData("BS_IEByLC","cId");
                    var ieByLCname = branchBSMsgDetailsForm.getUserData("BS_IEByLC","cValue");
                    var params="";
                    if(!ieByLCid || ieByLCid == 0){
                        params = "&mask=Self";
                    }else{
                        params = "&mask="+ieByLCname;
                    }

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"+params), function(xml){
                        ieByLCCombo.load(xml.xmlDoc.responseText);                                       
                        if(ieByLCid != '') branchBSMsgDetailsForm.setItemValue("BS_IEByLC",ieByLCid);

                        var ieByUSCombo = branchBSMsgDetailsForm.getCombo("BS_IEByUS");                                                              
                        var ieByUSid    = branchBSMsgDetailsForm.getUserData("BS_IEByUS","cId");
                        var ieByUSname  = branchBSMsgDetailsForm.getUserData("BS_IEByUS","cValue");
                        var params="";
                        if(!ieByUSid){
                            params = "&ctype=check&mask=Self&LCId="+ieByLCCombo.getSelectedValue();
                        }else{
                            params = "&ctype=check&mask="+ieByUSname+"&LCId="+ieByLCCombo.getSelectedValue();
                        }

                        ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php"+params));
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
                            ieByUSCombo.load(xml.xmlDoc.responseText);
                            if(ieByUSid != '') branchBSMsgDetailsForm.setItemValue("BS_IEByUS",ieByUSid);
                        });

                        ieByLCCombo.attachEvent("onChange", function() {
                            ieByUSCombo.clearAll();
                            ieByUSCombo.setComboValue(0);
                            ieByUSCombo.setComboText('');

                            var params = "&LCId="+ieByLCCombo.getSelectedValue();
                            ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php&ctype=check"+params),false);
                        });
                    });
                }   
                if (branchBSMsgDetailsForm.isItem("BS_PaidBy")) {  
                    var BS_PaidByCombo = branchBSMsgDetailsForm.getCombo("BS_PaidBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var paidByUser_Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("BS_PaidBy",paidByUser_Name);
                        BS_PaidByCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_PaidBy","cId"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_PaidTo")) {  
                    branchBSMsgDetailsForm.setItemValue("BS_PaidTo", branchBSMsgDetailsForm.getItemValue("DS_Description"));
                    branchBSMsgDetailsForm.setReadonly("BS_PaidTo", true);
                }
                if (branchBSMsgDetailsForm.isItem("BS_PayTime")) {  
                    branchBSMsgDetailsForm.setItemValue("BS_PayTime",branchBSMsgDetailsForm.getItemValue("BS_PayTime"));
                } 
                if (branchBSMsgDetailsForm.isItem("BS_StaffId")) {  
                    var BS_StaffIdCombo = branchBSMsgDetailsForm.getCombo("BS_StaffId");
                    BS_StaffIdCombo.addOption([[branchBSMsgDetailsForm.getUserData("BS_StaffId","cId"),branchBSMsgDetailsForm.getUserData("BS_StaffId","cValue")]]);
                    BS_StaffIdCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_StaffId","cId"));
                }
                if (branchBSMsgDetailsForm.isItem("BS_AprovlGvnBy")) {  
                    var BS_AprovlGvnByCombo = branchBSMsgDetailsForm.getCombo("BS_AprovlGvnBy");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var aprlUser_Name = BS_AprovlGvnByCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("LC_Id",aprlUser_Name);
                        BS_AprovlGvnByCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_AprovlGvnBy","cId"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_AprovlTknBy")) {  
                    var BS_AprovlTknByCombo = branchBSMsgDetailsForm.getCombo("BS_AprovlTknBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var aprlTknUser_Name = BS_AprovlTknByCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("BS_AprovlTknBy",aprlTknUser_Name);
                        BS_AprovlTknByCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_AprovlTknBy","cId"));
                    });
                }
                if (branchBSMsgDetailsForm.isItem("BS_AprovdDate")) { 
                    branchBSMsgDetailsForm.setItemValue(branchBSMsgDetailsForm.getUserData("BS_AprovdDate","cValue"));
                }
                if (branchBSMsgDetailsForm.isItem("BS_RcvdFrm")) {  
                    branchBSMsgDetailsForm.setReadonly("BS_RcvdFrm", true);
                }
                if (branchBSMsgDetailsForm.isItem("BS_RcvdBy")) { 
                    var BS_RcvdByCombo = branchBSMsgDetailsForm.getCombo("BS_RcvdBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                        branchBSMsgDetailsForm.setItemValue("BS_RcvdBy",rcvdByUser_Name);
                        BS_RcvdByCombo.setComboValue(branchBSMsgDetailsForm.getUserData("BS_RcvdBy","cId"));
                    });
                }
                                
                if (branchBSMsgDetailsForm.isItem("BS_PettyCashRefId")) { 
                    branchBSMsgDetailsForm.setItemValue("BS_PettyCashRefId",branchBSMsgDetailsForm.getUserData("BS_PettyCashRefId","cId"));
                    var BS_PCRefidCombo = branchBSMsgDetailsForm.getCombo("BS_PettyCashRefId"); 
                    BS_PCRefidCombo.setOptionWidth(350);
                    
                    BS_PCRefidCombo.attachEvent("onXLE", function(){
                        BS_PCRefidCombo.deleteOption(BS_Id);
                    });
//                               
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BS_Id+"&PCRefId="+branchBSMsgDetailsForm.getUserData("BS_PettyCashRefId","cId"))
                    }).done(function(data) { 
                        branchBSMsgDetailsForm.setItemValue("PettyCashAmount",data);
                    });
                    BS_PCRefidCombo.attachEvent("onClose",function(){

                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BS_Id+"&PCRefId="+BS_PCRefidCombo.getSelectedValue())
                        }).done(function(data) { 
                            branchBSMsgDetailsForm.setItemValue("PettyCashAmount",data);
                        });

                    });
                }
                
                branchBSMsgDetailsForm.attachEvent("onButtonClick", function(name) {

                    if (name == "balSheetDetailsSave") {
                        
                        var messageValidate = branchBSMsgDetailsForm.validate(); 
                        var values = branchBSMsgDetailsForm.getFormData();
                        var CstmValidate = preTally.Validate.Validate(values, 'BS_Amount', branchBSMsgDetailsForm, 'decimal'); 

                        if(CstmValidate == false) {
                            branchBSMsgDetailsForm.setValidateCss('BS_Amount', CstmValidate,  'validate_red');
                            return false;
                        }

//                        branchBSMsgDetailsForm.setValidation('BS_Amount', 'ValidInteger,NotEmpty');
                        if (branchBSMsgDetailsForm.isItem("BS_Price")) branchBSMsgDetailsForm.setValidation('BS_Price', 'ValidNumeric');
                        if (branchBSMsgDetailsForm.isItem("BS_Quantity")) branchBSMsgDetailsForm.setValidation('BS_Quantity', 'ValidNumeric');
                        if (branchBSMsgDetailsForm.isItem("BS_StaffId")) branchBSMsgDetailsForm.setValidation('BS_StaffId', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_PrchsdFor")) branchBSMsgDetailsForm.setValidation('BS_PrchsdFor', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_User")) branchBSMsgDetailsForm.setValidation('BS_User', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_AprovlGvnBy")) branchBSMsgDetailsForm.setValidation('BS_AprovlGvnBy', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_AprovlTknBy")) branchBSMsgDetailsForm.setValidation('BS_AprovlTknBy', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("CN_Id")) branchBSMsgDetailsForm.setValidation('CN_Id', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("ST_Id")) branchBSMsgDetailsForm.setValidation('ST_Id', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_Persons")) branchBSMsgDetailsForm.setValidation('BS_Persons', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("LC_Id")) branchBSMsgDetailsForm.setValidation('LC_Id', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("BS_PaidBy")) branchBSMsgDetailsForm.setValidation('BS_PaidBy', 'ValidInteger');
                        if (branchBSMsgDetailsForm.isItem("CHQ_Number")) branchBSMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                        if (branchBSMsgDetailsForm.isItem("IT_Id")) branchBSMsgDetailsForm.setValidation('IT_Id', 'ValidInteger,NotEmpty');
                        if (branchBSMsgDetailsForm.isItem("BS_Description")) branchBSMsgDetailsForm.setValidation('BS_Description', 'NotEmpty');
                        branchBSMsgDetailsForm.setValidation('BS_IEByUS', 'ValidInteger,NotEmpty');
                        branchBSMsgDetailsForm.setValidation('BS_IEByLC', 'ValidInteger,NotEmpty');
                        
                        if (!branchBSMsgDetailsForm.isItemHidden("TR_Id")) {
                            branchBSMsgDetailsForm.setValidation('TR_Id', 'ValidInteger,NotEmpty');
                        }else{  branchBSMsgDetailsForm.clearValidation('TR_Id'); }

                        if (branchBSMsgDetailsForm.isItem("PM_Id")) {
                            var patmentMode = branchBSMsgDetailsForm.getCombo("PM_Id");
                            var pmMode = patmentMode.getSelectedValue();
                            if (pmMode == 2){ 
                                branchBSMsgDetailsForm.setValidation('BNK_Id', 'NotEmpty'); 
                                branchBSMsgDetailsForm.setValidation('BB_Id', 'NotEmpty'); 
                                branchBSMsgDetailsForm.setValidation('BA_Id', 'NotEmpty'); 
                                branchBSMsgDetailsForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                                branchBSMsgDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty'); 

                                if (branchBSMsgDetailsForm.isItem("BS_PayType")) {
                                    var paymentType = branchBSMsgDetailsForm.getCombo("BS_PayType");
                                    var pmType = paymentType.getSelectedValue();console.log(pmType);
                                    if(pmType == '2'){
                                        branchBSMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty'); 
                                    }else { //if(pmType == '3' || pmType == '4' || pmType == '5')
                                       branchBSMsgDetailsForm.setItemValue("CHQ_Number","");
                                      // branchBSMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                                       branchBSMsgDetailsForm.clearValidation('CHQ_Number'); 
                                    }
                                }
                            } else {
                                branchBSMsgDetailsForm.setItemValue("BNK_Id","");
                                branchBSMsgDetailsForm.setItemValue("BB_Id","");
                                branchBSMsgDetailsForm.setItemValue("BB_Id","");
                                branchBSMsgDetailsForm.setItemValue("BA_Id","");
                                branchBSMsgDetailsForm.setItemValue("CHQ_Number","");
                                branchBSMsgDetailsForm.setItemValue("BS_PayersBank","");
                                branchBSMsgDetailsForm.setItemValue("BS_PayersChQ","");

//                                branchBSMsgDetailsForm.setValidation('BNK_Id', 'null'); 
//                                branchBSMsgDetailsForm.setValidation('BB_Id', 'null'); 
//                                branchBSMsgDetailsForm.setValidation('BB_Id', 'null');
//                                branchBSMsgDetailsForm.setValidation('BA_Id', 'null');
//                                branchBSMsgDetailsForm.setValidation('CHQ_Number', 'null');
//                                branchBSMsgDetailsForm.setValidation('BS_PayersBank', 'null'); 
//                                branchBSMsgDetailsForm.setValidation('BS_PayersChQ', 'null'); 
                                
                                branchBSMsgDetailsForm.clearValidation('BNK_Id'); 
                                branchBSMsgDetailsForm.clearValidation('BB_Id'); 
                                branchBSMsgDetailsForm.clearValidation('BB_Id');
                                branchBSMsgDetailsForm.clearValidation('BA_Id');
                                branchBSMsgDetailsForm.clearValidation('CHQ_Number');
                                branchBSMsgDetailsForm.clearValidation('BS_PayersBank'); 
                                branchBSMsgDetailsForm.clearValidation('BS_PayersChQ');
                            }
                        }

                        branchBSMsgDetailsForm.attachEvent("onValidateError", function (name, value, result){
                           branchBSMsgDetailsForm.setValidateCss(name, false, 'validate_red');
                           return false;
                        });

//                        var messageValidate = branchBSMsgDetailsForm.validate();
                        
                        if(branchBSMsgDetailsForm.getItemValue("IT_Flag") == "del_item"){
                                 branchBSMsgDetailsForm.setValidateCss('IT_Id', false);   
                                 dhtmlx.message({text : 'Selected Item is deleted.Please Verify'});                                 
                                 return false;
                            }
                                                
                        if(branchBSMsgDetailsForm.getItemValue("BS_PettyCashRefId") != 0){
                            if (Number(branchBSMsgDetailsForm.getItemValue("BS_Amount")) > Number(branchBSMsgDetailsForm.getItemValue("PettyCashAmount"))){
                                dhtmlx.message({text : ' Amount exceeds petty cash Amount. Please Verify'});
                                branchBSMsgDetailsForm.setValidateCss('BS_Amount', false);
                                return false;
                            }
                        }
                        
                        if (messageValidate && CstmValidate) {
                            preTally.Settings.progressOn(true, branchBSMsgDetailsWin, null);
                            var params="bsId="+BS_Id+"&TR_Track="+BS_TRCombo.getSelectedText()+"&type=branch";
                            branchBSMsgDetailsForm.send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&'+params) , function(loader, response) {
                                preTally.Settings.progressOff(true, branchBSMsgDetailsWin, null);
                                if(response != 1 && response != 2  && response != 'pettycash') {
                                    branchBSMsgDetailsForm.clear();
                                    if(dhxMiddleBlockTabs.tabs("view_branchbsreports"))
                                    preTally.BranchBSReports.applyFilter();                                     
                                    dhxBranchBSMsgDetails.window("branchBSDetailsWinMsg_"+ BS_Id).close();
                                }else if (response == 'pettycash'){
                                    branchBSMsgDetailsForm.setValidateCss('BS_Amount', false);
                                    response = 'Petty Cash Amount is incorrect. Please Verify.';
                                }else if (response == '2'){
                                    response = 'Edit is restricted for this entry.';
                                } else {
                                    response = 'Invalid Entry. Please Verify.';
                                }
                                dhtmlx.message({text:response });
                            });
                        }
                    } else {
                        dhxBranchBSMsgDetails.window("branchBSDetailsWinMsg_"+ BS_Id).close();
                    }
                });
            });
        },
        exportBranchBalanceReport : function() {
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = dhxBranchBSRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = dhxBranchBSRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TypeFilter'] = $('#BTRptIE').val();
            exportfilterValue['ItemNameFilter'] = $('#BTRptItm').val();
            exportfilterValue['BranchFilter'] = $('#BTRptBrnch').val();            
            exportfilterValue['r'] = rptBranchBSFilterID;
            exportfilterValue['report_type'] = "branchbalancesheet";
            exportfilterValue['shCRF'] = $("#shBR").val();
            exportfilterValue['amount'] = $('#BTRptAmt').val().replace( /,/g, "" );            
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
        },
        transactionOptions: function(type) { //19-05-2025 common options
            if (typeof type != "undefined" && type == 1) {
                return "<option value ='0'>All</option>"+
                "<option value ='1'>Income</option>"+
                "<option value ='2'>Expense</option>"+
                "<option value ='1-1'>Income Only</option>"+
                "<option value ='2-1'>Expense Only</option>"+
                "<option value ='1-2'>Internal Transfer Received</option>"+
                "<option value ='2-2'>Internal Transfer Paid</option>";
            } else {
                return "<option value ='0'>All</option>"+
                "<option value ='1'>Income</option>"+
                "<option value ='2'>Expense</option>";
            }
        },
    };
})(jQuery, this);