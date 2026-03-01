;(function($, window, undefined) {
    preTally.BankBalanceSheet = {
        view_BankBSReports: function() {

            if (!dhxMiddleBlockTabs.cells("view_bankbsreports")) {
                reportBankBSInit=1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details"); 
                dhxMiddleBlockTabs.addTab("view_bankbsreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Bank Balance Sheet&nbsp;&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupBank'/>", 210);
                dhxMiddleBlockTabs.tabs("view_bankbsreports").setActive();
                $(".popupBank").click(function(){
                    preTally.BankBalanceSheet.filterReport(ofzBankRprtTree.getSelectedItemId());
                    /*$(this).data('clicked', true);
                    x = getAbsoluteLeft(this);
                    y = getAbsoluteTop(this); 
                    w = this.offsetWidth;
                    h = this.offsetHeight;
                    var myPop = new dhtmlXPopup();
                    myPop.attachHTML("Bank Balance Sheet");
                    myPop.show(x ,y,w,h);*/
                })
                dhxBankOBReportsLayout =  dhxMiddleBlockTabs.cells("view_bankbsreports").attachLayout("2U");
                dhxBankOBReportsLayout.cells("a").setWidth(200);
                dhxBankOBReportsLayout.cells("a").hideHeader();
                dhxBankOBReportsLayout.cells("a").fixSize(true, true);    
                dhxBankOBReportsLayout.cells("b").fixSize(true, true);  
                dhxBankOBReportsLayout.cells('b').hideArrow();
                
                ofzBankRprtTree = dhxBankOBReportsLayout.cells("a").attachTree();
                
                ofzBankRprtTree.attachEvent("onXLS", function(){
                    preTally.Settings.progressOn(false, dhxBankOBReportsLayout, 'a');
                });
                ofzBankRprtTree.attachEvent("onXLE", function(){
                    preTally.Settings.progressOff(false, dhxBankOBReportsLayout, 'a');
                });
                
                ofzBankRprtTree.enableHighlighting(true);                 
                ofzBankRprtTree.setOnClickHandler(preTally.BankBalanceSheet.filterReport);
                ofzBankRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                ofzBankRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/bnkBankRptTree.php&f=BS&r=OF_O"), function(){
                    var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                    if(bsl){
                        ofzBankRprtTree.selectItem(bsl,'onClick',true);
                        preTally.BankBalanceSheet.filterReport(bsl);
                    }	
                }); 
//                var OFType_Options = [
//                    ['OF_O', 'obj', 'Office Map', 'office.gif'],
//                ];
                
//                ofzBankRprtTree.attachEvent("onClick", function(id){
//                    preTally.Settings.progressOn(true, dhxLayout, null);
//                    setTimeout(function(){
//                        preTally.BankBalanceSheet.filterReport(id);
//                    },1);
//                });
                
                dhxBankBSTreeTlbr = dhxBankOBReportsLayout.cells("a").attachToolbar();
                dhxBankBSTreeTlbr.setIconsPath("images/icon/");
                dhxBankBSTreeTlbr.addText('odhType_Rptz', '1', 'Bank Map' );
                dhxBankBSTreeTlbr.setAlign('right');
//                dhxBankBSRprtTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
//                dhxBankBSRprtTlbr.setAlign('right');
//                dhxBankBSRprtTlbr.setListOptionSelected('odhType_Rptz', 'OF_O');                
//                dhxBankBSRprtTlbr.attachEvent('onClick', function(id){
//                    preTally.Settings.reLoadOffz('BS',id,ofzBankRprtTree);
//                });  
                
                dhxBankBSRprtTlbr = dhxBankOBReportsLayout.cells("b").attachToolbar();                
                dhxBankBSRprtTlbr.setIconsPath("images/icon/default_18/");
                
                
//                var RPType_Options = [
//                    ['0', 'obj', 'Income-Expense Report', 'incomeexpense.png'],
//                    ['1', 'obj', 'Business Report', 'business.png'],
//                ];
//                
//                dhxBankBSRprtTlbr.addButtonSelect('iebsType_Rptz', '1', '', RPType_Options, '', '', true, true,3,'select');
//                dhxBankBSRprtTlbr.setAlign('left');
//                dhxBankBSRprtTlbr.setListOptionSelected('iebsType_Rptz', '0');
                
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
                
                dhxBankBSRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                dhxBankBSRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                dhxBankBSRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                

                dhxBankBSRprtTlbr.addSeparator();
                          
                dhxBankBSRprtTlbr.addText("text_from", null, "From");
                dhxBankBSRprtTlbr.addInput("rpt_date_from", null, "", 75);
                dhxBankBSRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                dhxBankBSRprtTlbr.addSeparator();
                
                dhxBankBSRprtTlbr.addText("text_till", null, "Till");
                dhxBankBSRprtTlbr.addInput("rpt_date_till", null, "", 75);
                dhxBankBSRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                dhxBankBSRprtTlbr.addSeparator();
                
                dhxBankBSRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                dhxBankBSRprtTlbr.addSeparator();
              
                dhxBankBSRprtTlbr.addButton("excel_export", null , "Export", "excel.png");

                var ptRpTb_Inp_Frm = dhxBankBSRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(dhxBankBSRprtTlbr.getValue("rpt_date_till")) preTally.BankBalanceSheet.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = dhxBankBSRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(dhxBankBSRprtTlbr.getValue("rpt_date_from")) preTally.BankBalanceSheet.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 dhxBankBSRprtTlbr.attachEvent("onClick", function(id){  
                   var pId = dhxBankBSRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        dhxBankBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBankBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BankBalanceSheet.filterReport(rptBankBSFilterID);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(dhxBankBSRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBankBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //dhxBankBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BankBalanceSheet.filterReport(rptBankBSFilterID);
                        
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
                        
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBankBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBankBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.BankBalanceSheet.filterReport(rptBankBSFilterID);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        /*if(!dhxBankBSRprtTlbr.getValue("rpt_date_from") && !dhxBankBSRprtTlbr.getValue("rpt_date_till")) {
                            dhtmlx.message({text: 'Please Select From / To Date'});
                        } else if(!rptBankBSFilterID){
                            dhtmlx.message({text: 'Please Select Office / Branch / User'});
                        } else {
                            preTally.BankBalanceSheet.filterReport(rptBankBSFilterID);
                        }*/
                        dhxBankBSRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBankBSRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBankBSRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BankBalanceSheet.filterReport(rptBankBSFilterID);
                    }  
                    if(id == 'excel_export') {
                        preTally.BankBalanceSheet.exportBankBalanceReport();
                    }
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                dhxBankBSRprtTlbr.setAlign('right');
                
                
                var tb_data_txt = ' <div class="tb_data_txt_secl">\
                                        <div class="tb_cnt_tot"># : 0</div></div><div id="bankBS_paging"></div>';
                
                var tbRptObj = dhxBankOBReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 30             // custom height
                });
                
                ptBankBSRprtsTabbar = dhxBankOBReportsLayout.cells("b").attachTabbar();
                //ptBankBSRprtsTabbar.addTab("a1", tb_data_txt, "730px");
                ptBankBSRprtsTabbar.addTab("a2", "Balance Sheet Items");
                ptBankBSRprtsTabbar.addTab("a3", "Report Visulization");
                ptBankBSRprtsTabbar.tabs("a3").hide();
                
                ptBankBSRprtsTabbar.tabs("a2").setActive();
                
                ptBankBSRprtsTabbar.attachEvent("onSelect", function(id, last_id){  
                    if(id != "a1") {
                        if (BankBS_DetailDataPop && BankBS_DetailDataPop.isVisible()) { 
                            BankBS_DetailDataPop.hide();
                        }
                        if(id == 'a2') preTally.BankBalanceSheet.reLoadSHGrid();
                        if(id == 'a3') preTally.BankBalanceSheet.reLoadGraph();
                        return true;
                    } 
                });
  
                dhxBankBSReportsGrid = ptBankBSRprtsTabbar.cells("a2").attachGrid();
                dhxBankBSReportsGrid.setImagePath("assets/grid/codebase/imgs/"); 
                dhxBankBSReportsGrid.setSkin("dhx_skyblue")
                //dhxBankBSReportsGrid.enableSmartRendering(true,50);
                dhxBankBSReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxBankBSReportsGrid.enablePaging(true,50,5,"bankBS_paging",true);
                dhxBankBSReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
//                dhxBankBSReportsGrid.loadXML("requisites/reportBankBSData.php", function() { 
//                    dhxBankBSReportsGrid.makeFilter("text_filter" , 1);
//                    var fltTxtValue = dhxBankBSReportsGrid.getFilterElement(1);
//                    fltTxtValue.onkeyup = function(){
//                        reportDetailsGrid.filterBy(1,this.value);
//                    };
//                });
                
                dhxBankBSReportsGrid.attachEvent("onFilterEnd", function(elements){ 
                    if(dhxBankBSReportsGrid.getRowsNum() == 0 ) {
                        dhxBankBSReportsGrid.addRow(0,['','No Records Found...','','',''],0); 
                        dhxBankBSReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }
                });

                dhxBankBSReportDetailLayout =  ptBankBSRprtsTabbar.cells("a3").attachLayout("3U");
                dhxBankBSReportDetailLayout.cells("b").setWidth(300);
                dhxBankBSReportDetailLayout.cells("b").setHeight(180);
                dhxBankBSReportDetailLayout.cells("b").hideHeader();
                
                dhxBankBSReportDetailLayout.cells("a").setText("Income & Expense");
                dhxBankBSReportDetailLayout.cells("c").setText("Details Of Income & Expense");
                    
                dhxBankBSReportChart =  dhxBankBSReportDetailLayout.cells("b").attachChart({                   
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

//                dhxBankBSReportsForm = dhxBankBSReportDetailLayout.cells("a").attachForm();
//                dhxBankBSReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
//                    $("label").css(" text-align","right");
//                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
//                });
                 
                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptBankBSReportsToolbar = dhxBankBSReportDetailLayout.cells("c").attachToolbar();
                ptBankBSReportsToolbar.setIconsPath("images/icon/");
                ptBankBSReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true,2,'select');
                ptBankBSReportsToolbar.setAlign('right');
                ptBankBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptBankBSReportsToolbar.disableItem('ieType');
                
                ptBankBSReportsToolbar.attachEvent('onClick', function(id){
                    preTally.BankBalanceSheet.reLoadGraph();
                });

                reportBankBSSubHeadChart = dhxBankBSReportDetailLayout.cells("c").attachChart({      
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
                preTally.BankBalanceSheet.filterReport(ofzBankRprtTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("view_bankbsreports").setActive();
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
            reportDetailsGrid.setHeader(",Category, Amount,#select_filter");
            reportDetailsGrid.setInitWidths("20,*,90,1");
            reportDetailsGrid.setColAlign("left,left,right,left");
            reportDetailsGrid.setColTypes("ro,ro,ro,ro");
            reportDetailsGrid.enableTooltips("false,false");
            reportDetailsGrid.init();
            
            reportBankBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();
            
            reportBankBSSubHeadChart.parse(chartBankBS_Response_OB,"json");
            reportBankBSSubHeadChart.parse(chartBankBS_Response_CB,"json");
            var LGD_Inc = chartBankBS_Response_OB;
            var LGD_Exp = chartBankBS_Response_CB;
            
            var LData   = new Object();
            var LGDrows = [];
            
            if((LGD_Inc.length !=0) || (LGD_Exp.length !=0))
            {    
            LGDrows.push({ id  : 1, data: ['' , "All",'', 'Income' ] });
            var LGCount = 2;
            $.each(LGD_Inc, function(index, value) {
                LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/up_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Income' ] });
                LGCount ++;
            });
            $.each(LGD_Exp, function(index, value) {
                LGDrows.push({ id  : LGCount, data: ['<img src="images/icon/down_12.png" /> ' , value.category, $("<span>"+value.amount+"</span>").formatCurrency().text(), 'Expense' ] });
                LGCount ++;
            });
            }
            LData.rows = LGDrows;//alert(JSON.stringify(LData));
            reportDetailsGrid.parse(LData,"json");         
            reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
                preTally.BankBalanceSheet.filterSubHead(id);
            }) ;   
        },
        filterSubHead:function (id){
            
            $( "#shBRF" ).val(reportDetailsGrid.cells(id,4).getValue());
            preTally.BankBalanceSheet.applyFilter();     
            
        },
        filterMainHead:function (id){
            $('#bnkrpt_text_filter').val('');
            dhxBankBSReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxBankBSReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBankBSData.php&"+chartCashBSFilterParams+"&MHType="+id), function() { });
          
            setTimeout(function(){
                preTally.Settings.progressOff(true, dhxLayout, null);
            },1500);
           
        },
        filterReport: function(id) { 
            //alert(id);
            //var Ids = ofzBankRprtTree.getAllSubItems(id); //alert(treeIds);
            subHeadData = "";
            var filtrInterval ;
            var filtrBranchInterval ;
                       
            rptBankBSFilterID = id;
            if(!rptBankBSFilterID){
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                ptBankBSReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                
                 if(reportBankBSInit==1)
                 {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                    if(bsp==4)
                    {   var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', tDate);
                        dhxBankBSRprtTlbr.setItemText('rpt_month_filter',m_names[month]);
                        reportBankBSInit=0;
                    }
                    else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy"); 
                        dhxBankBSRprtTlbr.setValue('rpt_date_till', cDate);
                        dhxBankBSRprtTlbr.setValue('rpt_date_from', cDate);
                        dhxBankBSRprtTlbr.setItemText('rpt_day_filter', 'Today');
                        reportBankBSInit=0;
                    }
                 
                }
                chartBankBSFilterParams = 'r='+id+'&f='+dhxBankBSRprtTlbr.getValue("rpt_date_from")+'&t='+dhxBankBSRprtTlbr.getValue("rpt_date_till");
             
                dhxBankBSReportsGrid.destructor();                
                dhxBankBSReportsGrid = ptBankBSRprtsTabbar.cells("a2").attachGrid();
                //dhxBankBSReportsGrid.setHeader("#,<select style = 'width:60px;' id = 'select_filter' onchange='preTally.BankBalanceSheet.filterMainHead(this.value);'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  id=text_filter style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,,Amount,#text_filter");
                dhxBankBSReportsGrid.setHeader("Msg,SlNo,<select style = 'width:60px;' class = 'bnk_select_filter' id='ieBRF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  class='bnkrpt_text_filter' id ='itmBRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shBRF' >,<input type='text' class='bnkrpt_text_filter' id='amtBRF' style='width: 90%;' placeholder='Amount'>,<input type='text' class='bnkrpt_text_filter' id='brnBRF' style='width: 90%;' placeholder='Branch'>,Confirm ");
//                 dhxBankBSReportsGrid.attachHeader(",,,,,,");
                dhxBankBSReportsGrid.setInitWidths("60,60,80,*,0,100,150,150")
                dhxBankBSReportsGrid.setColAlign("center,center,center,left,left,right,left,center")
                dhxBankBSReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                
                dhxBankBSReportsGrid.setColSorting("na,na,na,na,na,na,na,na")
//                dhxBankBSReportsGrid.enableEditEvents(true,true,true);
                dhxBankBSReportsGrid.init();
                dhxBankBSReportsGrid.enableCollSpan(true);
                dhxBankBSReportsGrid.setImagePath("assets/grid/codebase/imgs/"); 
                dhxBankBSReportsGrid.setSkin("dhx_skyblue")
                //dhxBankBSReportsGrid.enableSmartRendering(true,50);
                dhxBankBSReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxBankBSReportsGrid.enablePaging(true,50,5,"bankBS_paging",true);
                dhxBankBSReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                dhxBankBSReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                //ptBankBSRprtsTabbar.cells("a2").attachStatusBar({text:'<input type="button" value="Export as Excel" onclick="preTally.BankBalanceSheet.exportBankBalanceReport()">', height:30});
                dhxBankBSReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 7) {
//                        this.cells(id,ind).cell.title = 'Click here for More Details';
//                        return false;
//                    }
                });
//              onkeyup='preTally.BankBalanceSheet.applyFilter(this.value);'


                $( ".bnk_select_filter" ).change(function() {
                    preTally.BankBalanceSheet.applyFilter();
                });


                $( ".bnkrpt_text_filter" ).keyup(function() {
                    if(filtrInterval) clearInterval(filtrInterval);
                    
                    filtrInterval = setInterval( function() { 
                        preTally.BankBalanceSheet.applyFilter(); 
                        clearInterval(filtrInterval); 
                    }, 500);
                   
                });
                
                
                var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_BankOC.php&"+chartBankBSFilterParams), encodeURI(1));
                if (chartIE.xmlDoc.responseText != null) {

                    ptBankBSReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);

                    chartBankBS_Response_OB = $.parseJSON( chartData[0] );
                    chartBankBS_Response_CB = $.parseJSON( chartData[1] );

                    var actvId = ptBankBSRprtsTabbar.getActiveTab();
                    if(actvId == 'a2') preTally.BankBalanceSheet.reLoadSHGrid();
                    if(actvId == 'a3') preTally.BankBalanceSheet.reLoadGraph();
                } 
               
                
                dhxBankBSReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBankBSData.php&"+chartBankBSFilterParams), function() {
//                          $('.tb_cnt_tot').html("Total Number of Items : "+dhxBankBSReportsGrid.getUserData("", "TL_Count")+" ");
                    $('.tb_cnt_tot').html("# : "+dhxBankBSReportsGrid.getUserData("", "TL_Count")+" ");
                });
                 
                
                dhxBankBSReportsGrid.attachEvent("onRowSelect", function(id,ind){                    
                    if(ind != 0 && ind != 6 && ind != 7) $( ".targetBank_"+id ).click();
                });

                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
            }
        },
        applyFilter:  function(){
//            console.log($('#ieBRF').val()+"--"+$('#itmBRF').val()+"--"+$('#brnBRF').val()+"--"+$('#addCRF').val()+"--"+$( "#shBRF" ).val());
            var amount = $( "#amtBRF" ).val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#ieBRF').val(), $('#itmBRF').val(), $('#brnBRF').val(),$('#addCRF').val(),$( "#shBRF" ).val(),amount);
            dhxBankBSReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxBankBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBankBSData.php&"+chartBankBSFilterParams+"&filter="+filterValue), function() { 
                $('.tb_cnt_tot').html("# : "+dhxBankBSReportsGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        reLoadGraph: function() {
            var IE_Type = ptBankBSReportsToolbar.getListOptionSelected('ieType');
            
            if(reportDetailsGrid) reportDetailsGrid.destructor();
            
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("Legend, Category");
            reportDetailsGrid.setInitWidths("60,*");
            reportDetailsGrid.setColAlign("centre,left");
            reportDetailsGrid.setColTypes("cp,ed");
            reportDetailsGrid.enableTooltips("false,false");
            reportDetailsGrid.init();

            if(dhxWins.window("chartOtherDetails")) dhxWins.window("chartOtherDetails").close(); 
            reportBankBSSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';   
            if(IE_Type === 'IE_I'){ 
                reportBankBSSubHeadChart.parse(chartBankBS_Response_OB,"json");
                legendGridData = chartBankBS_Response_OB;
            }
            if(IE_Type === 'IE_E') {
                reportBankBSSubHeadChart.parse(chartBankBS_Response_CB,"json");
                legendGridData = chartBankBS_Response_CB;
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
            if(!dhxWins.isWindow('chartOtherDetails')){
            dhxWins.createWindow('chartOtherDetails', 400, 100, 600, 350);
            dhxWins.window('chartOtherDetails').setText('Other Category in Chart');
            dhxWins.window('chartOtherDetails').button("minmax1").hide();
            dhxWins.window('chartOtherDetails').button("minmax2").hide();
            dhxWins.window('chartOtherDetails').button("park").hide();            
            chartOtherDetailsGrid = dhxWins.window('chartOtherDetails').attachGrid();
            }
            chartBankBSFilterParams += "&ie="+ie;
            chartOtherDetailsGrid.setColSorting("na,na,na");
            chartOtherDetailsGrid.enableTooltips("false,false,false");
            chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartBankOCOther.php&"+chartBankBSFilterParams), function() {
                preTally.Settings.progressOff(false, dhxLayout, 'b');
            });
         
            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
                $('#bnkrpt_text_filter').val('');
                dhxBankBSReportsGrid.clearAll();
                
                $( "#shBRF" ).val(chartOtherDetailsGrid.getUserData(id, "shId"));
                preTally.BankBalanceSheet.applyFilter();
                
            });

        },
        showDetailData : function(inp,BS_Id,SH_Id){   
//            preTally.Settings.progressOn(false, dhxBankBSReportDetailLayout, 'c');
            if (!BankBS_DetailDataPop) {
                BankBS_DetailDataPop = new dhtmlXPopup({mode: "left"});   
                
            }
            if (BankBS_DetailDataPop.isVisible()) {
                BankBS_DetailDataPop.hide();
            } 
                
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;           
            var rptDetailsPop = BankBS_DetailDataPop.attachForm();
            //rptDetailsPop.style.backgroundColor="red";
            var params = "SHID="+SH_Id+"&BSID="+BS_Id
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_BankBSPop.php&"+params), function() {
                BankBS_DetailDataPop.show(x, y, w, h);
//                preTally.Settings.progressOff(false, dhxBankBSReportDetailLayout, 'c');
                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
                column0.style.borderRight = "1px solid #a4bed4";
                //column1.style.paddingLeft = "6px";
                //console.log(column0);               
                
                
            });
            
            $('.target_'+BS_Id).unbind();
        },
        hideDetailData : function(){
            if (BankBS_DetailDataPop.isVisible()) {
                BankBS_DetailDataPop.hide();
            } 
        },
        saveBankBookDetails: function(inp, BS_Id, MH_Type) {

            dhxBankBSDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);

            bnkBSDetailsWin = dhxBankBSDetails.createWindow("bnkBSDetailsWin_" + BS_Id, x, y, 600, 350);
            bnkBSDetailsWin.center();
            bnkBSDetailsWin.button("minmax1").hide();
            bnkBSDetailsWin.button("minmax2").hide();
            bnkBSDetailsWin.button("park").hide();
            bnkBSDetailsWin.setModal(true);
            bnkBSDetailsWin.setText("Confirm Payment");
            bsBnkDetailsForm = bnkBSDetailsWin.attachForm();
            preTally.Settings.progressOn(true, bnkBSDetailsWin, null);

            var params = "&MH_Type="+MH_Type+"&BSID="+BS_Id;
            
            bsBnkDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportBankBookUpdate.php&"+params), function() {
                    
                    preTally.Settings.progressOff(true, bnkBSDetailsWin, null);
                    
                    bsBnkDetailsForm.setItemValue("CHQ_Number", dhxBankBSReportsGrid.getUserData(BS_Id, "CHQ_Number"));
                    
//                    bsBnkDetailsForm.hideItem("BS_RJDate"); 
                    bsBnkDetailsForm.hideItem("BS_RJReason");
                    bsBnkDetailsForm.setItemLabel("BS_Date", "Credited Date");
                    
                    var BS_StatusCombo = bsBnkDetailsForm.getCombo("BS_Status");

                    BS_StatusCombo.attachEvent("onChange", function(){

                        var statusVal = BS_StatusCombo.getSelectedValue(); 

                            if(statusVal == 1){
                                bsBnkDetailsForm.hideItem("BS_RJReason");
                                if(MH_Type == 1) {
                                    bsBnkDetailsForm.showItem("BS_BReff");
                                    bsBnkDetailsForm.setItemLabel("BS_Date", "Credited Date");
                                }else {
                                    bsBnkDetailsForm.setItemLabel("BS_Date", "Cleared Date");
                                } 
                            }else{
                                bsBnkDetailsForm.setItemLabel("BS_Date", "Rejected Date");
                                bsBnkDetailsForm.showItem("BS_RJReason");
                                if(MH_Type == 1) bsBnkDetailsForm.hideItem("BS_BReff"); 
                            }
                    });
                    
                    bsBnkDetailsForm.attachEvent("onButtonClick", function(name){
                        if(name == 'bnkBookDetailsSave'){
                            
                            var bnkBookValidate = bsBnkDetailsForm.validate();
                            if(bnkBookValidate){
                            
                            bsBnkDetailsForm.send(preTally.Initialize.encryptURL('warehouse/manageBankBook.php&') , function(loader, response) {
                                
                                if(response != 'fail') {
                                    dhxBankBSReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBankBSData.php&"+chartBankBSFilterParams),function(){
                                        $('.tb_cnt_tot').html("# : "+dhxBankBSReportsGrid.getUserData("", "TL_Count")+" ");      
                                    });

                                    dhtmlx.message({text:response });
                                    bnkBSDetailsWin.close("bnkBSDetailsWin_");
                                }else{
                                    dhtmlx.message({text : "Confirmation of this entry is Restricted."});
                                }
                            });
                            
                            }
                            
                        }else{
                            bnkBSDetailsWin.close("bnkBSDetailsWin_");
                        }
                    });
            });
        },
        exportBankBalanceReport : function() {
            
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = dhxBankBSRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = dhxBankBSRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TypeFilter'] = $('#ieBRF').val();
            exportfilterValue['ItemNameFilter'] = $('#itmBRF').val();
            exportfilterValue['BranchFilter'] = $('#brnBRF').val();
            exportfilterValue['amount'] = $('#amtBRF').val().replace( /,/g, "" );  // remove , from amount
            exportfilterValue['r'] = rptBankBSFilterID;
            exportfilterValue['report_type'] = "bankbalancesheet";
            exportfilterValue['shBRF'] = subHeadData;
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
        sendCorrectionMsg: function(inp, BS_Id, SH_Id) {
            dhxMsgWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            rptBnkMsgWin = dhxMsgWin.createWindow("rptBnkMsgWin_" + BS_Id, x, y, 540, 400);
            rptBnkMsgWin.center();
            rptBnkMsgWin.button("minmax1").hide();
            rptBnkMsgWin.button("minmax2").hide();
            rptBnkMsgWin.button("park").hide();
            rptBnkMsgWin.setModal(true);
            rptBnkMsgWin.setText("Send Correction Message");
            rptBnkMsgForm = rptBnkMsgWin.attachForm();
            preTally.Settings.progressOn(true, rptBnkMsgWin, null);            
            rptBnkMsgForm.loadStruct(preTally.Initialize.encryptURL("requisites/sendBankBookNotify.php"), function() {                
                rptBnkMsgForm.setItemValue("US_Id", dhxBankBSReportsGrid.getUserData(BS_Id, "US_Id"));
                rptBnkMsgForm.setItemValue("Entry", dhxBankBSReportsGrid.getUserData(BS_Id, "Entry"));
                rptBnkMsgForm.setItemValue("MSG_To", dhxBankBSReportsGrid.getUserData(BS_Id, "US_Name"));                
                rptBnkMsgForm.setItemValue("BS_Id", BS_Id);
                rptBnkMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptBnkMsgWin, null);
                rptBnkMsgForm.attachEvent("onButtonClick", function(name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptBnkMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptBnkMsgWin, null);
                            param = "type=branch";
                            rptBnkMsgForm.send(preTally.Initialize.encryptURL("warehouse/sendBankBookNotify.php&"+param), function(loader, response) {
                                preTally.Settings.progressOff(true, rptBnkMsgWin, null);
                                if(response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    rptBnkMsgForm.clear();
                                    dhxMsgWin.window("rptBnkMsgWin_" + BS_Id).close();
                                    preTally.BankBalanceSheet.applyFilter();
                                }
                                else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if(name == 'addRecpt') {
                        dhxUserWin = new dhtmlXWindows();
                        recpBnkWin = dhxUserWin.createWindow("addRecpWin", x, y, 1050, 350);
                        recpBnkWin.button("minmax1").hide();
                        recpBnkWin.button("minmax2").hide();
                        recpBnkWin.button("park").hide();
                        recpBnkWin.center();
                        recpBnkWin.setModal(true);
                        recpBnkWin.setText("Add Recipients");
                        listUserGrid = recpBnkWin.attachGrid();
                        listUserGrid.enableAutoWidth(true);
                        preTally.Settings.progressOn(true, recpBnkWin, null);         

                        var listRecipientsXML= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked="+rptBnkMsgForm.getItemValue('US_Id')); 
                        listUserGrid.parse(listRecipientsXML.xmlDoc.responseText,function(){
                            preTally.Settings.progressOff(true, recpBnkWin, null);
                        });

                        recpBnkWin.attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.BankBalanceSheet.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                    } else {
                        rptBnkMsgForm.resetValidateCss();
                        rptBnkMsgForm.clear();
                        dhxMsgWin.window("rptBnkMsgWin_"+ BS_Id).close();
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
            rptBnkMsgForm.setItemValue("MSG_To",userDetails);
            rptBnkMsgForm.setItemValue('US_Id',userIds);
            dhxUserWin.window("addRecpWin").close();
        },
    };
})(jQuery, this);