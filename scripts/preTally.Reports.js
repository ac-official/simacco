;(function($, window, undefined) {
    var SHName ="";
    var shHRF ="";
    preTally.Reports = {
        viewReports: function() {

            if (!dhxMiddleBlockTabs.cells("viewReports")) {
                mainReportInit=1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details"); 
                
                dhxMiddleBlockTabs.addTab("viewReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Hierarchical Report <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupHeirarchy'/>", 240);
                dhxMiddleBlockTabs.tabs("viewReports").setActive();
                $(".popupHeirarchy").click(function(){                    
                    preTally.Reports.filterReport(ptyRprtTree.getSelectedItemId());                     
                })
                dhxReportsLayout =  dhxMiddleBlockTabs.cells("viewReports").attachLayout("2U");
                dhxReportsLayout.cells("a").setWidth(200);
                dhxReportsLayout.cells("a").hideHeader();
                //dhxReportsLayout.cells("b").hideHeader();
                incomeExpenceFilter=0;
                data="";
                dhxReportsLayout.cells("a").fixSize(true, true);    
                dhxReportsLayout.cells("b").fixSize(true, true);  
                dhxReportsLayout.cells('b').hideArrow();
                
                ptyRprtTree = dhxReportsLayout.cells("a").attachTree();
                
                ptyRprtTree.attachEvent("onXLS", function(){
                    preTally.Settings.progressOn(false, dhxReportsLayout, 'a');
                });
                ptyRprtTree.attachEvent("onXLE", function(){
                    preTally.Settings.progressOff(false, dhxReportsLayout, 'a');
                });
                
                ptyRprtTree.enableHighlighting(true);                 
//                ptyRprtTree.setOnClickHandler(preTally.Reports.filterReport);
                ptyRprtTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                ptyRprtTree.loadXML(preTally.Initialize.encryptURL("requisites/offzTree.php&f=BS&r=OF_H"), function(){
                    var bsl = unescape(JGG1P3bDnUSDL3Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                    if(bsl){
                        ptyRprtTree.selectItem(bsl,'onClick',true);
                        preTally.Reports.filterReport(bsl);
                    }	
                    
                }); 
                //var OFType_Options = [
                   // ['OF_H', 'obj', 'Hierarchy Map', 'hierarchy.gif'],
                    
               // ];
                
                ptyRprtTree.attachEvent("onClick", function(id){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    setTimeout(function(){
                        preTally.Reports.filterReport(id);
                    },1);
                });
                
                dhxRprtTlbr = dhxReportsLayout.cells("a").attachToolbar();
                dhxRprtTlbr.setIconsPath("images/icon/");
                dhxRprtTlbr.addText('odhType_Rptz', '1', 'Office Map' );
                //dhxRprtTlbr.addButtonSelect('odhType_Rptz', '1', '', OFType_Options, '', '', true, true,3,'select');
                dhxRprtTlbr.setAlign('right');
                //dhxRprtTlbr.setListOptionSelected('odhType_Rptz', 'OF_H');                
                //dhxRprtTlbr.attachEvent('onClick', function(id){
                    //preTally.Settings.reLoadOffz('BS',id,ptyRprtTree);
                //});  
                
                ptReportToolbar = dhxReportsLayout.cells("b").attachToolbar();                
                ptReportToolbar.setIconsPath("images/icon/default_18/");
                
                
//                var RPType_Options = [
//                    ['0', 'obj', 'Income-Expense Report', 'incomeexpense.png'],
//                    ['1', 'obj', 'Business Report', 'business.png'],
//                ];
//                
//                ptReportToolbar.addButtonSelect('iebsType_Rptz', '1', '', RPType_Options, '', '', true, true,3,'select');
//                ptReportToolbar.setAlign('left');
//                ptReportToolbar.setListOptionSelected('iebsType_Rptz', '0');
                
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
                
                ptReportToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                
               
                ptReportToolbar.addSeparator();
                          
                ptReportToolbar.addText("text_from", null, "From");
                ptReportToolbar.addInput("rpt_date_from", null, "", 75);
                ptReportToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                ptReportToolbar.addSeparator();
                
                ptReportToolbar.addText("text_till", null, "Till");
                ptReportToolbar.addInput("rpt_date_till", null, "", 75);
                ptReportToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                ptReportToolbar.addSeparator();
                
                ptReportToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                
                ptReportToolbar.addButton("excel_export", null , "Export", "excel.png");
                
                var ptRpTb_Inp_Frm = ptReportToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptReportToolbar.getValue("rpt_date_till")) preTally.Reports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptReportToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptReportToolbar.getValue("rpt_date_from")) preTally.Reports.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 ptReportToolbar.attachEvent("onClick", function(id){  
                   var pId = ptReportToolbar.getParentId(id);
                   
                    if(id == 'rpt_df_clear')
                        ptReportToolbar.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptReportToolbar.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        ptReportToolbar.setValue('rpt_date_from', dateToday, false);
                        ptReportToolbar.setValue('rpt_date_till', dateToday, false);
                        ptReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.Reports.filterReport(rptFilterID);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptReportToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptReportToolbar.setValue('rpt_date_from', firstDay, false);
                        ptReportToolbar.setValue('rpt_date_till', lastDay, false);
                        ptReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.Reports.filterReport(rptFilterID);
                        
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
                        
                        ptReportToolbar.setValue('rpt_date_from', firstDay, false);
                        ptReportToolbar.setValue('rpt_date_till', lastDay, false);
                        ptReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        preTally.Reports.filterReport(rptFilterID);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        /*if(!ptReportToolbar.getValue("rpt_date_from") && !ptReportToolbar.getValue("rpt_date_till")) {
                            dhtmlx.message({text: 'Please Select From / To Date'});
                        } else if(!rptFilterID){
                            dhtmlx.message({text: 'Please Select Office / Branch / User'});
                        } else {
                            preTally.Reports.filterReport(rptFilterID);
                        }*/
                        ptReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                       preTally.Reports.filterReport(rptFilterID);
                    } 
                    
                    if(id == 'excel_export'){
                        preTally.Reports.exportCashBalanceReport();
                    }
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                ptReportToolbar.setAlign('right');
                
                
                /*var tb_data_txt = ' <div class="tb_data_txt_secl">\
                                        <div class="tb_rpt_inc">INCOME : 0</div>\
                                        <div class="tb_rpt_exp">EXPENSE : 0</div>\
                                        <div class="It_paid">IT_paid : 0</div>\
                                        <div class="It_received">IT_Received : 0</div>\
                                        <div class="It_buss">Bussiness : 0</div>\
                                    </div>';
                
                var tbRptObj = dhxReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 60            // custom height
                });*/
                // var tb_data_txt ="<div class='tb_rpt_inc'>INCOME : 0</div> <span>hai<span><div class='tb_rpt_exp'>EXPENSE : 0</div><div class='It_paid'>IT_paid : 0</div></div>";  
                /* var tb_data_txt = ' <div class="testdiv" style="height:30px;">\
                                        <div style="height:30px;" class="tblinc"></div>\
                                        <div style="height:30px;width:50px" class="tbl_rpt_exp">EXPENSE : 0</div>\
                                        <div style="height:30px;" class="tbl_rpt_inc">INCOME : 0</div>\
                                    </div><div class="testdiv" style="height:30px;">\
                                        <div style="width:50px;height:30px;" class="It_buss">BUSINESS : 0</div>\
                                        <div style="width:50px;height:30px;" class="It_paid">INTERNAL TRANSFER PAID : 0</div>\
                                        <div style="width:50px;height:30px;" class="It_received">INTERNAL TRANSFER RECEIVED : 0</div>\
                                    </div>';
                
                var tbRptObj = dhxReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 70            // custom height
                });*/
                var tb_data_txt = '<div class="testdiv" style="height:20px;top:0px !important;">\
                                        <div style="height:20px;padding-left:60px;width:200px !important" class="tbl_rpt_inc">INCOME : 0</div>\
                                        <div style="height:20px;width:200px !important;padding-left:60px;" class="tbl_rpt_exp">EXPENSE : 0</div>\
                                        <div style="width:200px !important;height:20px;padding-left:60px;" class="It_buss" >BUSINESS : 0</div>\
                                    </div><div class="testdiv" style="height:20px; top:21px !important">\
                                        <div style="width:310px !important;height:20px;padding-left:60px;" class="It_paid">INTERNAL TRANSFER PAID : 0</div>\
                                        <div style="width:310px !important;height:20px;padding-left:60px;" class="It_received">INTERNAL TRANSFER RECEIVED : 0</div>\
                                    </div><div id="pageHirReport" style="line-height:15px !important ;top:5px !important;width:100% !important;"></div>';
                
                var tbRptObj = dhxReportsLayout.cells("b").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 70            // custom height
                });
                                        
                                       
                                    
                                        
                
                
                ptReportsTabbar = dhxReportsLayout.cells("b").attachTabbar();
                //ptReportsTabbar.addTab("a1", tb_data_txt, "465px");
                ptReportsTabbar.addTab("a2", "Balance Sheet Items");
                //ptReportsTabbar.addTab("a3", "Report Visulization");
                
                ptReportsTabbar.tabs("a2").setActive();
                
                ptReportsTabbar.attachEvent("onSelect", function(id, last_id){  
                    if(id != "a1") {
                        if (IE_DetailDataPop && IE_DetailDataPop.isVisible()) { 
                            IE_DetailDataPop.hide();
                        }
                        if(id == 'a2') preTally.Reports.reLoadSHGrid();
                        //if(id == 'a3') preTally.Reports.reLoadGraph();
                        return true;
                    } 
                });
  
                dhxReportsGrid = ptReportsTabbar.cells("a2").attachGrid();  
//                dhxReportsGrid.loadXML("requisites/reportData.php", function() { 
//                    dhxReportsGrid.makeFilter("text_filter" , 1);
//                    var fltTxtValue = dhxReportsGrid.getFilterElement(1);
//                    fltTxtValue.onkeyup = function(){
//                        reportDetailsGrid.filterBy(1,this.value);
//                    };
//                });
                
                dhxReportsGrid.attachEvent("onFilterEnd", function(elements){ 
                    if(dhxReportsGrid.getRowsNum() == 0 ) {
                        dhxReportsGrid.addRow(0,['','','','','No Records Found...','','',''],0); 
                        dhxReportsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }else{
                        
                    }
                });              
                 
               /* dhxReportDetailLayout =  ptReportsTabbar.cells("a3").attachLayout("3U");
                dhxReportDetailLayout.cells("b").setWidth(300);
                dhxReportDetailLayout.cells("b").setHeight(180);
                dhxReportDetailLayout.cells("b").hideHeader();
                dhxReportDetailLayout.cells("a").setText("Income & Expense");
                dhxReportDetailLayout.cells("c").setText("Details Of Income & Expense");
                dhxReportChart =  dhxReportDetailLayout.cells("b").attachChart({                   
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

                dhxReportsForm = dhxReportDetailLayout.cells("a").attachForm();
                dhxReportsForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewReports.php&r=" + new Date().getTime()), function() {  
                    //$("label").css(" text-align","right");
                    $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                    $('input[name=PL_Data]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
                });
                 
                var IE_Options = [
                    ['IE_I', 'obj', 'Income', 'arrow.up.icon.gif'],
                    ['IE_E', 'obj', 'Expense', 'arrow.down.icon.gif'],
                ];

                ptReportsToolbar = dhxReportDetailLayout.cells("c").attachToolbar();
                ptReportsToolbar.setIconsPath("images/icon/");
                ptReportsToolbar.addButtonSelect('ieType', '1', '', IE_Options, '', '', true, true,2,'select');
                ptReportsToolbar.setAlign('right');
                ptReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                ptReportsToolbar.disableItem('ieType');
                
                ptReportsToolbar.attachEvent('onClick', function(id){
                    preTally.Reports.reLoadGraph();
                });

                reportSubHeadChart = dhxReportDetailLayout.cells("c").attachChart({      
                    view: "bar",
                    container: "chart2",
                    value: "#amount#",
                    label:  currency + "#amount#",
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
                });*/

                
            } 
            else {
                preTally.Reports.filterReport(ptyRprtTree.getSelectedItemId());
                dhxMiddleBlockTabs.tabs("viewReports").setActive();
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
            reportDetailsGrid.setInitWidths("20,*,90,0");
            reportDetailsGrid.setColAlign("left,left,right,left");
            reportDetailsGrid.setColTypes("ro,ro,ro,ro");  
            reportDetailsGrid.enableTooltips("false,false,false,false");                        
            reportDetailsGrid.init();
            
            //reportSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();
            
                //reportSubHeadChart.parse(chartIE_Response_Inc,"json");
                //reportSubHeadChart.parse(chartIE_Response_Exp,"json");
            var LGD_Inc = chartIE_Response_Inc;
            var LGD_Exp = chartIE_Response_Exp;
            
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
            shHRF = '';
            reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
//                preTally.Reports.filterSubHead(id);
                    var data=reportDetailsGrid.cells(id,1).getValue();
                    var str = data.split(" ");  

                    if(data!='All')
                        shHRF = data;
                    else shHRF = '';
                    
                    preTally.Reports.applyFilter();
            }) ;   
        },
        filterReport: function(id) { 
            
            //alert(id);
            //var Ids = ptyRprtTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval ;
            var filtrBranchInterval ;
            var subIds = ptyRprtTree.getAllSubItems(id);
            //console.log(subIds);
            if(subIds != ''){
                RP_Ids = id+","+ptyRprtTree.getAllSubItems(id);
            }else {
                RP_Ids = id;
            }
            var d = new Date();
            d.setTime(d.getTime() + (24*60*60*1000));
            var expires = "expires="+d.toUTCString();
            document.cookie="HReprt_ID="+btoa(RP_Ids)+";"+expires;
            //mapIdRptz = dhxRprtTlbr.getListOptionSelected('odhType_Rptz');            
            rptFilterID = id;
            if(!rptFilterID){
                dhtmlx.message({text: 'Please Select Office / Branch / User'});
            } else {
                preTally.Settings.progressOn(true, dhxLayout, null);
                //ptReportsToolbar.setListOptionSelected('ieType', 'IE_I');
                
                 if(mainReportInit==1)
                 {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                    if(bsp==4)
                    {   var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
                        ptReportToolbar.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        ptReportToolbar.setValue('rpt_date_till', tDate);
                        ptReportToolbar.setItemText('rpt_month_filter',m_names[month]);
                        mainReportInit=0;
                    }
                    else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy"); 
                        ptReportToolbar.setValue('rpt_date_till', cDate);
                        ptReportToolbar.setValue('rpt_date_from', cDate);
                        ptReportToolbar.setItemText('rpt_day_filter', 'Today');
                        mainReportInit=0;
                    }
                 
                }
                chartFilterParams = 'r='+id+'&f='+ptReportToolbar.getValue("rpt_date_from")+'&t='+ptReportToolbar.getValue("rpt_date_till");
             
                dhxReportsGrid.destructor();                
                dhxReportsGrid = ptReportsTabbar.cells("a2").attachGrid();
                        
                dhxReportsGrid.setImagePath("assets/codebase/imgs/");
                dhxReportsGrid.setHeader("Msg,Edit,SlNo,<select style = 'width:60px;' id = 'ieHRF' class='hrpt_select_filter' ><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  id= 'itmHRF' class = 'hrpt_text_filter' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,,<input type='text' id='amtHRF' class='hrpt_text_filter' style='width: 90%;' placeholder='Amount'>,<input type='text' id='brHRF' class='hrpt_text_filter' style='width: 90%;' placeholder='Branch'>");
               
                //dhxReportsGrid.attachFooter("Income,#cspan,<div class='tb_rpt_inc'>0</div>,c,Expence,<div class='tb_rpt_exp'>0</div>",["text-align:left;"]);
                //dhxReportsGrid.attachFooter("IntlTrnsfrResved,#cspan,<span style='float:left;><span class='class='It_received'>0</span><span style='margin-left:10em;'>Business|</span><span class='It_buss'>0</span></span>,c,IntTrnsferPaid,<div class='It_paid'>0</div>",["text-align:left;"]);
                
                dhxReportsGrid.setInitWidths("40,40,40,80,*,0,100,150");
                dhxReportsGrid.setColAlign("center,center,center,center,left,left,right,right")
                dhxReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                
                dhxReportsGrid.setColSorting("na,na,na,na,na,na,na,na");
                dhxReportsGrid.init();                
                dhxReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                dhxReportsGrid.setSkin("dhx_skyblue")
                dhxReportsGrid.setPagingWTMode(true,false,true,[15,30,50,80]);                
                dhxReportsGrid.enablePaging(true,50,5,"pageHirReport",true);
                dhxReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                //dhxReportsGrid.enableSmartRendering(true,50);
                dhxReportsGrid.enableTooltips("true,true,false,false,false,false,false,false");
//                dhxReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 0) {
//                        this.cells(id,ind).cell.title = 'Click here to sent message';
//                        return false;
//                    }
//                    if(ind == 1) {
//                        this.cells(id,ind).cell.title = 'Click here to edit';
//                        return false;
//                    }
//                });
                
//                dhxReportsGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 7) {
//                        this.cells(id,ind).cell.title = 'Click here for More Details';
//                        return false;
//                    }
//                });
//              onkeyup='preTally.Reports.applyFilter(this.value);'
                dhxReportsGrid.attachEvent("onDynXLS",function(start,count){ //will be called for dyn. loading attempt                     
                    dhxReportsGrid.post(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams+"&posStart="+start+"&count="+count),"ID="+btoa(RP_Ids)); //custom loading code
                    return false; //blocking native loading functionality
                    });

                $( ".hrpt_select_filter" ).change(function() {
                    preTally.Reports.applyFilter();
                });


                $( ".hrpt_text_filter" ).keyup(function() {
                    if(filtrInterval) clearInterval(filtrInterval);
                    
                    filtrInterval = setInterval( function() { 
                        preTally.Reports.applyFilter(); 
                        clearInterval(filtrInterval); 
                    }, 500);
                   
                });
                

//                $( "#text_filter" ).keyup(function(value) {
//                    var mask = this.value;
//                    if(filtrInterval) clearInterval(filtrInterval);
//                    
//                    filtrInterval = setInterval( function() { 
//                        preTally.Reports.applyFilter(mask); 
//                        clearInterval(filtrInterval); 
//                    }, 500);
//                   
//                });
//                
//                $( "#text_filter_branch" ).keyup(function(value) {
//                    var mask = this.value;
//                    if(filtrBranchInterval) clearInterval(filtrBranchInterval);
//                    
//                    filtrBranchInterval = setInterval( function() { 
//                        preTally.Reports.applyFilterBranch(mask); 
//                        clearInterval(filtrBranchInterval); 
//                    }, 500);
//                   
//                });
                
                //if(mapIdRptz == 'OF_H'){ 
                    var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_IE.php&"+chartFilterParams),"ID="+RP_Ids, encodeURI(1)); 
                //}
                //else { 
                   // var reportIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/report_IE.php&"+chartFilterParams), encodeURI(1));
                //}
                if(reportIE.xmlDoc.responseText != null) {
 
                    var reportIE_Response = $.parseJSON( reportIE.xmlDoc.responseText );
                    //dhxReportsForm.setItemValue("INCOME", reportIE_Response[0].value);
                    //dhxReportsForm.setItemValue("EXPENSE", reportIE_Response[1].value);
                    
                    
                    $('.tbl_rpt_inc').html('INCOME : '+reportIE_Response[0].value);
                    $('.tbl_rpt_exp').html('EXPENSE : '+reportIE_Response[1].value);
                    $('.It_paid').html('INTERNAL TRANSFER PAID : '+reportIE_Response[2].value);
                    $('.It_received').html('INTERNAL TRANSFER RECEIVED : '+reportIE_Response[3].value);
                    $('.It_buss').html('BUSINESS : '+reportIE_Response[4].value);
                    
                    
                   // dhxReportsGrid.attachFooter("Total Amount,#cspan,<span style='float:right;'><div id='mr_OB'>0</div></span>,#cspan,<span style='float:right;'>{#stat_total}</span>");
                   

                    var pl = reportIE_Response[0].value - reportIE_Response[1].value;
                    if(pl < 0) { 
                        plColor = '#EE4339';
                        //bkColor = '#E06666';
                        plText  = 'LOSS';
                        plSign  = '-';
                    }
                    else { 
                        plColor = '#435F2D';
                        //bkColor = '#6AA84F';
                        plText  = 'PROFIT';
                        plSign  = '';
                    }
                    
//                    $('input[name=PL_Data]').css({'color':plColor});
//                    $('.tb_rpt_pl').css({'background-color':bkColor});
                    
                    //dhxReportsForm.setItemValue("PL_Data", Math.abs(pl));
                    $('.tb_rpt_pl').html(plText+' : '+Math.abs(pl.toFixed(2)));
                    

                    //dhxReportChart.clearAll();
                    //dhxReportChart.parse(reportIE_Response,"json");
                    
                    $('input[name=INCOME]').formatCurrency();
                    $('input[name=EXPENSE]').formatCurrency();
                    $('input[name=PL_Data]').formatCurrency();
                    $('input[name=PL_Data]').val(plText +'   : ' + $('input[name=PL_Data]').val());
                }
                
                
                //if(mapIdRptz == 'OF_H')
                //{ 
                    var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_IE.php&"+chartFilterParams),"ID="+RP_Ids);
                //}
                //else {
                   // var chartIE = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/chart_IE.php&"+chartFilterParams), encodeURI(1));
                //}
                if (chartIE.xmlDoc.responseText != null) {

                    //ptReportsToolbar.enableItem('ieType');
                    var chartData = eval(chartIE.xmlDoc.responseText);

                    chartIE_Response_Inc = $.parseJSON( chartData[0] );
                    chartIE_Response_Exp = $.parseJSON( chartData[1] );

                    var actvId = ptReportsTabbar.getActiveTab();
                    if(actvId == 'a2') preTally.Reports.reLoadSHGrid();
                    if(actvId == 'a3') preTally.Reports.reLoadGraph();
                }             
                //if(mapIdRptz == 'OF_H'){
                    data="";
                    dhxReportsGrid.post(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams),"ID="+btoa(RP_Ids));
                   
                   /* var hirReportGrid= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams), "ID="+btoa(RP_Ids)); 
                    dhxReportsGrid.parse(hirReportGrid.xmlDoc.responseText,function(){
                        $('.tb_cnt_tot').html("# : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");     
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });*/
                //} else {
                    //dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams), function() {
//                          $('.tb_cnt_tot').html("Total Number of Items : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");
                   // });
                //}    
                
                dhxReportsGrid.attachEvent("onRowSelect", function(id,ind){
                    if(ind != 0 && ind != 1 && ind != 7)  $( ".target_"+id ).click();
                });
                

                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
            }
        },
        applyFilter:  function(id){
            //console.log(shHRF);
            var amount = $( "#amtHRF" ).val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#ieHRF').val(), $('#itmHRF').val(), $('#brHRF').val(),amount,shHRF);
            
            dhxReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            //var hirReportGrid= dhx4.ajax.postSync();             
            /*dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&filter="+filterValue),function(){                
                $('.tb_cnt_tot').html("# : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");     
                preTally.Settings.progressOff(true, dhxLayout, null);
            });*/
            
            var hirReportGrid= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&filter="+filterValue), "ID="+btoa(RP_Ids)); 
            dhxReportsGrid.parse(hirReportGrid.xmlDoc.responseText,function(){
                $('.tb_cnt_tot').html("# : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");     
                preTally.Settings.progressOff(true, dhxLayout, null);
            }); 
        },
        reLoadGraph: function() {
            //var IE_Type = ptReportsToolbar.getListOptionSelected('ieType');
            
            if(reportDetailsGrid) reportDetailsGrid.destructor();
            
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("Legend, Category");
            reportDetailsGrid.setInitWidths("60,*");
            reportDetailsGrid.setColAlign("centre,left");
            reportDetailsGrid.setColTypes("cp,ed");
            reportDetailsGrid.init();

            if(dhxWins.window("chartOtherDetails")) dhxWins.window("chartOtherDetails").close(); 
            //reportSubHeadChart.clearAll();
            reportDetailsGrid.clearAll();

            var legendGridData = '';   
            /*if(IE_Type === 'IE_I'){ 
                reportSubHeadChart.parse(chartIE_Response_Inc,"json");
                legendGridData = chartIE_Response_Inc;
            }
            if(IE_Type === 'IE_E') {
                reportSubHeadChart.parse(chartIE_Response_Exp,"json");
                legendGridData = chartIE_Response_Exp;
            }*/ 
             
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
            chartFilterParams += "&ie="+ie;
            chartOtherDetailsGrid.setColSorting("na,na,na");
            chartOtherDetailsGrid.enableTooltips("false,false,false");
            //if(mapIdRptz == 'OF_H'){ 
                chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartIEOther.php&"+ chartFilterParams)+"&ID="+btoa(RP_Ids), function() {
                    preTally.Settings.progressOff(false, dhxLayout, 'b');
                });
            //} else { 
                //chartOtherDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/chartIEOther.php& " + chartFilterParams), function() {
                    //preTally.Settings.progressOff(false, dhxLayout, 'b');
               // });
            //}
            chartOtherDetailsGrid.attachEvent("onRowSelect",function(id,data){ 
//                $('#text_filter').val('');
//                dhxReportsGrid.clearAll();
                data=chartOtherDetailsGrid.cells(id,1).getValue();
                
                shHRF = data;
                preTally.Reports.applyFilter();
                //if(mapIdRptz == 'OF_H'){
//                    dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams+"&SHName="+data)+"&ID="+btoa(RP_Ids), function() {
//                         $('.tb_cnt_tot').html("Total Number of Items : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");
//                    });
               // } else {
                   // dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams+"&SHName="+data), function() { 
                      //  $('.tb_cnt_tot').html("Total Number of Items : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");
                    //});
               // }
            });
        },
        showDetailData : function(inp,BS_Id,SH_Id){    
            
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;                
           // preTally.Settings.progressOn(false, dhxReportDetailLayout, 'c');
            if (!IE_DetailDataPop) {
                IE_DetailDataPop = new dhtmlXPopup({mode: "left"});   
                
            }
            if (IE_DetailDataPop.isVisible()) {
                IE_DetailDataPop.hide();
            } 
            var rptDetailsPop = IE_DetailDataPop.attachForm();
            //rptDetailsPop.style.backgroundColor="red";
//            if(SH_Id){
                var params = "SHID="+SH_Id+"&BSID="+BS_Id
                rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_iePop.php&"+ params), function() {
                    IE_DetailDataPop.show(x, y, w, h);
                    //preTally.Settings.progressOff(false, dhxReportDetailLayout, 'c');
                    var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                    var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
                    column0.style.borderRight = "1px solid #a4bed4";
                    //column1.style.paddingLeft = "6px";
                    //console.log(column0);               


                });
//            } else {
//                    rpData = [{
//                        id      : "NI",
//                        item    : "New Items",
//                    }];
//                        IE_DetailDataPop.attachList("item", rpData);
//                        IE_DetailDataPop.show(x, y, w, h);
//            }
            $('.target_'+BS_Id).unbind();
        },
        hideDetailData : function(){
            if (IE_DetailDataPop.isVisible()) {
                IE_DetailDataPop.hide();
            } 
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
                        branchBSMsgDetailsForm.setItemValue("IT_Flag","0"); 
                        var params="IT_Id=" + BS_ITId;
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
                    var item_list = new Array( "BS_PaidDate", "BS_PayType","CHQ_Number", "BNK_Id", "BB_Id", "BA_Id", "BS_PayersChQ", "BS_PayersBank" );
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
                    var ieByLCid    = branchBSMsgDetailsForm.getUserData("BS_IEByLC","cId");
                    var ieByLCname  = branchBSMsgDetailsForm.getUserData("BS_IEByLC","cValue");
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
                        //if (branchBSMsgDetailsForm.isItem("CHQ_Number")) branchBSMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
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
                                    var pmType = paymentType.getSelectedValue();
                                    if(pmType == '2'){
                                        branchBSMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty'); 
                                    }else if(pmType == '3' || pmType == '4' || pmType == '5'){
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
                                if(response != 1 && response != 2 ) {
                                    branchBSMsgDetailsForm.clear();
                                    //preTally.BranchBSReports.applyFilter(); 
                                    dhxBranchBSMsgDetails.window("branchBSDetailsWinMsg_"+ BS_Id).close();
                                    if(dhxReportsGrid){
                                        dhxReportsGrid.clearAll();
                                        preTally.Settings.progressOn(true, dhxLayout, null);

                                        var amount = $( "#amtHRF" ).val().replace( /,/g, "" );  // remove , from amount
                                        var filterValue = new Array($('#ieHRF').val(), $('#itmHRF').val(), $('#brHRF').val(),amount,shHRF);

                                        var hirReportGrid= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&filter="+filterValue), "ID="+btoa(RP_Ids)); 
                                        dhxReportsGrid.parse(hirReportGrid.xmlDoc.responseText,function(){
                                            $('.tb_cnt_tot').html("# : "+dhxReportsGrid.getUserData("", "TL_Count")+" ");     
                                            preTally.Settings.progressOff(true, dhxLayout, null);
                                        });
                                    }
//                                        dhxReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&MHType="+incomeExpenceFilter+"&mask="+textFilter+"&SHName="+data+"&LC_Name="+branchFilter)+"&ID="+btoa(RP_Ids), function() {});
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
                rptMsgForm.setItemValue("US_Id", dhxReportsGrid.getUserData(BS_Id, "US_Id"));
                rptMsgForm.setItemValue("MSG_To", dhxReportsGrid.getUserData(BS_Id, "US_Name"));
                rptMsgForm.setItemValue("Entry", dhxReportsGrid.getUserData(BS_Id, "Entry"));
                rptMsgForm.setItemValue("BS_Id", BS_Id);
                rptMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptMsgWin, null);
                rptMsgForm.attachEvent("onButtonClick", function(name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptMsgWin, null);
                            param = "type=hireport";
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
        MISReport : function(){
            if (!dhxMiddleBlockTabs.cells("misReport")) {
                dhxMiddleBlockTabs.addTab("misReport", "<img src='images/icon/globe_16.png' style='margin-top:2px;' />&nbsp;&nbsp;MIS Reports", 150);
                dhxMiddleBlockTabs.tabs("misReport").setActive();
                
                MISToolbar = dhxMiddleBlockTabs.tabs("misReport").attachToolbar({
                    // toolbar conf here
                });

                MISTree = dhxMiddleBlockTabs.tabs("misReport").attachGrid();

                MISTree.setImagePath("assets/treegrid/codebase/imgs/");
                MISTree.enableAlterCss("even","uneven");
                MISTree.enableTreeCellEdit(false);
                //MISTree.enableTreeGridLines();
                MISTree.enableMultiselect(true);
                MISTree.enableRowsHover(true,"grid_hover");
                MISTree.enableTooltips("false,false,false,false,false,false,false,false,false,false");
                MISTree.kidsXmlFile = preTally.Initialize.encryptURL("requisites/misReportLevel.php");
                
                //MISTree.setSkin("dhx_skyblue");
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                MISTree.loadXML(preTally.Initialize.encryptURL("requisites/misReport.php"), function() {
                    MISToolbar.addButton('1', '', MISTree.getUserData('main_parent','nodeName'), MISTree.getUserData('main_parent','nodeImage'));
                    MISToolbar.addSeparator();
                    preTally.Settings.progressOff(true, dhxLayout, null);
                                  
                    
                    
                    MISToolbar.attachEvent("onClick", function(id){
//                        console.log('ID - '+id);
                        //console.log(toolbarIDObj);
                        
                        var toolbarIDArray = $.map(toolbarIDObj, function(value, index) {
                            return [value];
                        });
                        toolbarIDArray.reverse();
                        //var closeRowID  = Math.ceil((parseInt(id)+4)/2);
                        var closeRowID  = parseInt(id)+1;
//                        console.log('Actual - '+closeRowID);
//                        console.log(toolbarIDArray);
                        //console.log(toolbarIDArray[Math.ceil(id/2)+2]);
                        //var closeRowID  = MISTree.getRowId(Math.ceil(id/2));
                        //console.log(toolbarIDArray[closeRowID]);
                        MISTree.closeItem(toolbarIDArray[closeRowID]);
                        //MISToolbar.removeItem(parseInt(id));
                        MISToolbar.removeItem(parseInt(id)+1);
                        MISToolbar.removeItem('S'+parseInt(id));
                    });

                    var openItemID = 0;
                    
                    MISTree.attachEvent("onRowSelect", function(id,ind){
                        if(MISTree.getOpenState(id) == false){
                            MISTree.openItem(id);
                            MISTree.showRow(id);
                        }else 
                            MISTree.closeItem(id);
                        
                        preTally.Reports.MISReportNodeManage(id, 0);
                    });
                    
                    
                    
                    
                    MISTree.attachEvent("onOpenStart", function(id,state){
                        //console.log(id+' -- '+state);
                        
                        if(state != 1) {
                            var itmLevel0 = MISTree.getLevel(openItemID);
                            var itmLevel1 = MISTree.getLevel(id);
                            if((openItemID != 0) && (openItemID != id) && (itmLevel0 >= itmLevel1)) {
                                MISTree.closeItem(openItemID);
                                //MISTree.collapseAll();
                                //MISTree.openItem(id);
                            }
                            openItemID = id;
                            
                            //MISTree.closeItem('main_parent');
                            //MISTree.openItem(id);
                            
                            
                        } else {
                            MISTree.collapseAll();
                            MISTree.openItem(id);
                            MISTree.showRow(id);
                        }
                        
                        selectedItemID = id;
                        toolbarIDObj = {};
                        preTally.Reports.MISReportNodeManage(id, 0);
                        return true;
                    });
                    MISTree.attachEvent("onXLS",function(){
                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    MISTree.attachEvent("onXLE",function(){
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });

                });
            } else {
                dhxMiddleBlockTabs.tabs("misReport").setActive();
            }
        },
         MISGrid : function(){
            if (!dhxMiddleBlockTabs.cells("misGrid")) {
                dhxMISGridTab = dhxMiddleBlockTabs.addTab("misGrid", "<img src='images/icon/globe_16.png' style='margin-top:2px;' />&nbsp;&nbsp;MIS Reports", 150);
                dhxMiddleBlockTabs.tabs("misGrid").setActive();
                  var MISGridLayout = dhxMiddleBlockTabs.cells("misGrid").attachLayout('2U');
                MISGridLayout.cells("a").setText("Search");
                MISGridLayout.cells("b").setText("Report");
                MISGridLayout.cells("a").setWidth(300);
                MISGridLayout.cells("a").hideHeader();
                MISGridLayout.cells("b").hideHeader();
                var MISSrchForm=MISGridLayout.cells("a").attachForm();
                var MISSrchGrid=MISGridLayout.cells("b").attachGrid();
                MISSrchGrid.loadXML(preTally.Initialize.encryptURL("requisites/misCustomReport.php"));
                MISSrchForm.loadStruct(preTally.Initialize.encryptURL("requisites/misSrchForm.php"),function(){
                        var Srch_Year= MISSrchForm.getCombo("Srch_Year"); 
                        var Srch_Mnth= MISSrchForm.getCombo("Srch_Month");
                        var Srch_Day = MISSrchForm.getCombo("Srch_Date");
                        var strt_date_clndr= MISSrchForm.getCalendar("Strt_Date");
                        var end_date_clndr= MISSrchForm.getCalendar("End_Date");    
                        strt_date_clndr.attachEvent("onClick",function(){
                        Srch_Year.setComboValue(0);    
                        Srch_Mnth.setComboValue(0);
                        Srch_Day.setComboValue(0);
                        });
                        end_date_clndr.attachEvent("onClick",function(){
                        Srch_Year.setComboValue(0);    
                        Srch_Mnth.setComboValue(0);
                        Srch_Day.setComboValue(0);
                        });
                        var date = new Date();  
                        var Mnth=date.getMonth()+1; 
                        var strt_date = new Date(date.getFullYear(), 0, 1);
                        var end_date = new Date(date.getFullYear(), 11, 31);
                        MISSrchForm.setItemValue("Strt_Date",strt_date);
                        MISSrchForm.setItemValue("End_Date",end_date);
                 
                   Srch_Year.attachEvent("onClose",function(){
                       Srch_Mnth.setComboValue(0);
                       Srch_Day.setComboValue(0);
                       var Year=Srch_Year.getSelectedValue();
                       var date = new Date(); 
                       if(Year==0)Year=date.getFullYear();                        
                       var strt_date=Year+"-01-01";
                       var end_date=Year+"-12-31";
                       MISSrchForm.setItemValue("Strt_Date",strt_date);
                       MISSrchForm.setItemValue("End_Date",end_date);
                                    
                   });
                   Srch_Mnth.attachEvent("onClose",function(){
                       Srch_Year.setComboValue(0);
                       Srch_Day.setComboValue(0);
                       var Mnth=Srch_Mnth.getSelectedValue();                       
                       var date = new Date();  
                       if(Mnth==0)Mnth=date.getMonth()+1; 
                       var strt_date = new Date(date.getFullYear(), parseInt(Mnth-1), 1);
                        var end_date = new Date(date.getFullYear(), parseInt(Mnth), 0);
                       MISSrchForm.setItemValue("Strt_Date",strt_date);
                       MISSrchForm.setItemValue("End_Date",end_date);
                   });
                    Srch_Day.attachEvent("onClose",function(){
                        Srch_Year.setComboValue(0);
                        Srch_Mnth.setComboValue(0);
                       var Day=Srch_Day.getSelectedValue();     
                       var date = new Date();  
                       if(Day==0)Day=date.getDate();                       
                       var strt_date = new Date(date.getFullYear(), date.getMonth(), Day);                       
                       MISSrchForm.setItemValue("Strt_Date",strt_date);
                       MISSrchForm.setItemValue("End_Date",strt_date);
                    
                   });
                    var FiltrByCombo=MISSrchForm.getCombo("Srch_FiltrBy");
                    var TypeCombo=MISSrchForm.getCombo("Srch_Type");
                    var LocateCombo=MISSrchForm.getCombo("LC_Id"); 
                    var UserCombo=MISSrchForm.getCombo("US_Id"); 
                    var SubHeadCombo=MISSrchForm.getCombo("SH_Id"); 
                    var ItemCombo=MISSrchForm.getCombo("IT_Id"); 
                    var FiltrCombo = LocateCombo;                    
                    preTally.Reports.comboCheckAll(LocateCombo,MISSrchForm);
                    preTally.Reports.comboCheckAll(UserCombo,MISSrchForm);
                    preTally.Reports.comboCheckAll(SubHeadCombo,MISSrchForm);
                    preTally.Reports.comboCheckAll(ItemCombo,MISSrchForm);
                    
                    
                     MISSrchForm.hideItem("LC_Id");
                        MISSrchForm.hideItem("SH_Id");
                        MISSrchForm.hideItem("IT_Id");
                        MISSrchForm.hideItem("US_Id");  
                    TypeCombo.attachEvent("onClose",function(){
                        MISSrchForm.setItemValue("rpt_type",TypeCombo.getSelectedText()+" Wise");                        
                    });
                    FiltrByCombo.attachEvent("onChange",function(){                       
                        MISSrchForm.removeItem("filt_block");
                        var Srch_FiltrBy=MISSrchForm.getItemValue("Srch_FiltrBy");
                        MISSrchForm.hideItem("LC_Id");
                        MISSrchForm.hideItem("SH_Id");
                        MISSrchForm.hideItem("IT_Id");
                        MISSrchForm.hideItem("US_Id");  
                        var headerstring="";
                        
                        switch(Srch_FiltrBy){                            
                            case '2':
                                MISSrchForm.showItem("LC_Id");                                                                
                                LocateCombo.clearAll();
                                LocateCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check"));
                                FiltrCombo = LocateCombo;                                
                                MISSrchForm.setItemValue("filt_lbl","Branches");
                                break;
                            case '3':
                                MISSrchForm.showItem("US_Id");                                     
                                UserCombo.clearAll();
                                UserCombo.load(preTally.Initialize.encryptURL("requisites/persons.php&ctype=check"));
                                FiltrCombo = UserCombo;
                                MISSrchForm.setItemValue("filt_lbl","User");
                                break;
                            case '4':
                                MISSrchForm.showItem("SH_Id");                                  
                                SubHeadCombo.clearAll();
                                SubHeadCombo.load(preTally.Initialize.encryptURL("requisites/subheadCombo.php&ctype=check"));
                                FiltrCombo = SubHeadCombo;
                                 MISSrchForm.setItemValue("filt_lbl","Subhead");
                                break;
                            case '5':
                                MISSrchForm.showItem("IT_Id");                                      
                                ItemCombo.clearAll();
                                ItemCombo.load(preTally.Initialize.encryptURL("requisites/ItemsCombo.php&ctype=check"));
                                FiltrCombo = ItemCombo;
                                MISSrchForm.setItemValue("filt_lbl","Item");
                                break;
                            default:  
                                    MISSrchForm.hideItem("LC_Id");
                                    MISSrchForm.hideItem("SH_Id");
                                    MISSrchForm.hideItem("IT_Id");
                                    MISSrchForm.hideItem("US_Id");
                                    break;
                        }
                        FiltrCombo.setComboText("All Options Selected");
                    });                    
                    MISSrchForm.attachEvent("onButtonClick",function(name,value){
                        if(name=="SrchButton"){
                           var filtrval;
                          /* if(FiltrCombo.getSelectedValue()!="0"){*/
                                filtrval= FiltrCombo.getChecked();
                            /*}else{
                                filtrval=0;
                            }*/
                            
                           var params= "filtrVal="+filtrval+"&filter="+MISSrchForm.getItemValue("Srch_FiltrBy")+"&type="+MISSrchForm.getItemValue("Srch_Type")+"&stDate="+strt_date_clndr.getFormatedDate("%y-%m-%d")+"&enDate="+end_date_clndr.getFormatedDate("%y-%m-%d");
                           //preTally.Settings.progressOn(true, recipientWin, null);         
                        var listSrchGrid= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/misCustomReport.php"),params); 
                        MISSrchGrid.parse(listSrchGrid.xmlDoc.responseText,function(){
                            //preTally.Settings.progressOff(true, recipientWin, null);
                        });
                            
                                // MISSrchGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/misCustomReport.php"+params));
                        }else if(name=="CancelSrchButton")
                        {   MISSrchForm.clear();
                            MISSrchForm.removeItem("filt_block");
                            MISSrchForm.setItemValue("Strt_Date",strt_date);
                            MISSrchForm.setItemValue("End_Date",end_date);
                        }
                    });
                    
                });                
                 
            }
            else{
                dhxMiddleBlockTabs.tabs("misGrid").setActive();
            }
            },comboCheckAll:function(object,Form){
              var flg_All;
                object.setComboValue("1");
                object.attachEvent("onCheck", function(value, state){   
                    flg_All=0;
                        if(value == '0') { 
                            for(var i = 0; i < object.getOptionsCount(); i++) {
                                if(object.getOptionByIndex(i).value != 0 ) {
                                    if(state == true) {
                                        object.setChecked(i, true);
                                    }
                                    else if(state == false)
                                        object.setChecked(i, false);
                                }
                            }
                            flg_All=1;
                            object.setComboValue("0");                                                        
                        }
                        else {
                            if(object.isChecked(0)) {
                                object.setChecked(0, false);
                            }                             
                        }
                        return true;
                    });
                    
                    object.attachEvent("onClose",function(){
                       
                       Form.removeItem("filt_block");
                       var itemData = {type: "block", name: "filt_block",  width : "200"}
                       Form.addItem("mis_info", itemData, 2 );
                       var FiltrString=$.map(object.getChecked(), function(value, index) {
                            return [value];
                        });
                      // FiltArr=FiltrString.split(",");                        
                       if(flg_All==0 && FiltrString.length>0){
                       for(var i=0;i<FiltrString.length;i++){                         
                       OptObj=object.getOption(FiltrString[i]);   
//                        console.log(OptObj.text);
                        var itemData = {type: "template", name: "Filtr_name",  value : OptObj.text};
                        Form.addItem("filt_block", itemData, 0 );
                        object.setComboText(FiltrString.length+" Options Selected");
                       } 
                   }else{
                        var itemData = {type: "template", name: "Filtr_name",  value :"All"}
                        Form.addItem("filt_block", itemData, 0 );
                        object.setComboText("All Options Selected");
                    }
                            
                    });
            
        },MISReportNodeManage : function (id, $count) {
            //console.log(id+'--');
            var itemParentID = MISTree.getParentId(id);
            //console.log(itemParentID);
            toolbarIDObj[$count] = itemParentID;
            $count++;
            if(itemParentID != 0) {
                preTally.Reports.MISReportNodeManage(itemParentID, $count);
                return;
            } else {
                //console.log(toolbarIDObj);
                //toolbarIDArray.reverse();
                
                
                /*var toolbarIDArray = $.map(toolbarIDObj, function(value, index) {
                    return [value];
                });
                toolbarIDArray.reverse();*/
                
                
                //console.log($count);
                //console.log(toolbarIDArray);
                //console.log(id);
                
                
                
                
                var imge = 'category_20.png';
                MISToolbar.clearAll();
                MISToolbar.addButton('1', '1', MISTree.getUserData('main_parent','nodeName'), MISTree.getUserData('main_parent','nodeImage'));
                MISToolbar.addSeparator('S1', '2');

                $j = 2;
                $jPosn = 2;
            
                if(selectedItemID != 'main_parent' && selectedItemID != 'allYear') {
                    var JSONObject = JSON.parse(selectedItemID);
                    //console.log(JSONObject);

                    $.each(JSONObject, function (key, data) {
                        if(key != 'request' && key != 'seed' && key != 'parent') {
//                            console.log(key + ' -- ' + data);
                            /*if(key == 'year' && data instanceof Array) {
                                var temp = '[ ' + data[0] + ' - ' + data[1] + ' ]';
                                data = temp;
                                imge = 'calender_1_24.png';
                            }
                            if(key == 'month') {
                                data = monthData[data];
                                imge = 'calendar_2_24.png';
                            }
                            if(nodeAlias.hasOwnProperty(key) && key != 'year') { 
                                imge = nodeAlias[key][1];
                                //data = nodeAlias[key][0];
                            }*/
                            //data = data[key][1];
                            //imge = data[key][2];
                            //console.log(data);
                            //console.log(data[1]);
                            //toolbarIDArray[$j] = key + ' -- ' + data;
                            MISToolbar.addButton($j, $jPosn, data[1], 'images/icon/'+data[2]); //id, pos, text, imgEn, imgDis
                            MISToolbar.addSeparator('S'+$j, parseInt($jPosn)+1);
                            //$j+=2;
                            $j++;
                            $jPosn += 2;

                            //console.log(key+' -- '+data);
                            
                        }
                        //console.log(toolbarIDArray);
                    })




                }
  
                //console.log(toolbarIDArray);
            
            }
            //console.log(toolbarIDArray);
            
        },
        __MISReportNodeManage : function (id) {
            var imge = 'category_20.png';
            MISToolbar.clearAll();
            MISToolbar.addButton('1', '1', MISTree.getUserData('main_parent','nodeName'), MISTree.getUserData('main_parent','nodeImage'));
            MISToolbar.addSeparator('2', '2');

            $j = 3;
//            console.log(id);
            if(id != 'main_parent' && id != 'allYear') {
                var JSONObject = JSON.parse(id);
                //console.log(JSONObject);

                $.each(JSONObject, function (key, data) {
                    if(key != 'request' && key != 'seed' && key != 'parent') {
                        //console.log(key + ' -- ' + data);
                        if(key == 'year' && data instanceof Array) {
                            var temp = '[ ' + data[0] + ' - ' + data[1] + ' ]';
                            data = temp;
                            imge = 'calender_1_24.png';
                        }
                        if(key == 'month') {
//                            data = monthData[data];
                            imge = 'calendar_2_24.png';
                        }
//                        if(nodeAlias.hasOwnProperty(key) && key != 'year') { 
//                            imge = nodeAlias[key][1];
//                            //data = nodeAlias[key][0];
//                        }

                        //console.log(key + ' -- ' + data);
                        //toolbarIDArray[$j] = key + ' -- ' + data;
                        MISToolbar.addButton($j, $j, data, 'images/icon/'+imge); //id, pos, text, imgEn, imgDis
                        MISToolbar.addSeparator($j+1, $j+1);
                        $j+=2;

                        //console.log(key+' -- '+data);
                    }
                })




            }
  
  
  
        },
        exportCashBalanceReport : function() {
            
            var exportfilterValue = {};
            exportfilterValue['From_Date'] = ptReportToolbar.getValue("rpt_date_from");
            exportfilterValue['To_Date'] = ptReportToolbar.getValue("rpt_date_till");
            exportfilterValue['TypeFilter'] = $('#ieHRF').val();
            exportfilterValue['ItemNameFilter'] = $('#itmHRF').val();
            exportfilterValue['BranchFilter'] = $('#brHRF').val();
//            exportfilterValue['AddedByFilter'] = $('#addCRF').val();
            exportfilterValue['amount'] = $( "#amtHRF" ).val().replace( /,/g, "" );  // remove , from amount
            exportfilterValue['report_type'] = "transaction";
            exportfilterValue['r'] = rptFilterID;
            exportfilterValue['report_type'] = "hierarchy";
            exportfilterValue['shCRF'] = shHRF;
            preTally.Settings.progressOn(true, dhxLayout, null);

            $.post(
                preTally.Initialize.encryptURL('warehouse/ReportExcelExport.php'),
                { filter : exportfilterValue ,ID  : btoa(RP_Ids) },
                function(data) {
                    fileName = data.split("XL_");
                    if(fileName[1]) {
                         document.location ="uploads/excelFile/"+fileName[1];
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        
        
        
//        applyFilter:  function(value){
//            
//            branchFilter=$( "#text_filter_branch" ).val();
//                dhxReportsGrid.clearAll();
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                //if(mapIdRptz == 'OF_H'){
//                    dhxReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams)+"&ID="+btoa(RP_Ids)+"&mask="+value+"&LC_Name="+ branchFilter+"&MHType="+incomeExpenceFilter +"&SHName="+data, function() {preTally.Settings.progressOff(true, dhxLayout, null);});
//                //} else {
//                    //dhxReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams+"&mask="+value), function() { preTally.Settings.progressOff(true, dhxLayout, null);});
//                //}
//        },
//        applyFilterBranch:  function(value){
//            
//                dhxReportsGrid.clearAll();
//                textFilter=$( "#text_filter" ).val();
//                preTally.Settings.progressOn(true, dhxLayout, null);
//                //if(mapIdRptz == 'OF_H'){
//                    dhxReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams)+"&ID="+btoa(RP_Ids)+"&LC_Name="+value+"&mask="+textFilter+"&MHType="+incomeExpenceFilter +"&SHName="+data, function() {preTally.Settings.progressOff(true, dhxLayout, null);});
//                //} else {
//                   // dhxReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&LC_Name="+value), function() { preTally.Settings.progressOff(true, dhxLayout, null);});
//                //}
//        },

        
//        
//        filterSubHead:function (id){
//           
//            $('#text_filter').val('');
//            $('#select_filter').val(0);
//            $( "#text_filter_branch" ).val('');
//            dhxReportsGrid.clearAll();
//            data=reportDetailsGrid.cells(id,1).getValue();//alert(reportDetailsGrid.cells(id,3).getValue());
//            var IETyp = reportDetailsGrid.cells(id,3).getValue();
//            if(IETyp == 'Income'){
//                var IETypVal = 1;
//            }else{
//                var IETypVal = 2;
//            }
//            preTally.Settings.progressOn(true, dhxLayout, null);
//            str = data.split(" ");            
//            if(str[0] == 'Others') {
//                data="";
//                //if(mapIdRptz == 'OF_H'){
//                    dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams)+"&ID="+btoa(RP_Ids), function() {});
//                //} else {
//                    //dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams), function() { });
//                //}
////                dhxReportsGrid.filterBy(3,'');    
//            } else if(data == 'Pending for Approval') {
//                //if(mapIdRptz == 'OF_H'){
//                    dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams)+"&ID="+btoa(RP_Ids)+"&ITPending="+IETypVal, function() {});
//                //} else {
////                    dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+ chartFilterParams), function() { });
//                //}
//            } else {
//                if(data!='All')
//                { 
//                    SHName = data;
//                    //if(mapIdRptz == 'OF_H'){
//                        dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams)+"&ID="+btoa(RP_Ids)+"&SHName="+data, function() {});
//                    //} else {
//                       // dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&SHName="+data), function() { });
//                    //}
////                    dhxReportsGrid.filterBy(3,function(data){                        
////                        return data==reportDetailsGrid.cells(id,1).getValue();      
////                    });
//                } else {
//                   data="";
//                   //if(mapIdRptz == 'OF_H'){
//                        dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams)+"&ID="+btoa(RP_Ids), function() {});
//                    //} else {
//                      //  dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams), function() { });
//                   // }
////                    dhxReportsGrid.filterBy(3,'');
//                }  
//            }
//            setTimeout(function(){
//                preTally.Settings.progressOff(true, dhxLayout, null);
//            },1500);
//        },
//        filterMainHead:function (id){
//           
//            
//            textFilter=$( "#text_filter" ).val();
//            branchFilter=$( "#text_filter_branch" ).val();
//            incomeExpenceFilter=id;
//            dhxReportsGrid.clearAll();
//            preTally.Settings.progressOn(true, dhxLayout, null);
//            
//            //if(mapIdRptz == 'OF_H'){
//                dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&MHType="+id+"&mask="+textFilter+"&SHName="+data+"&LC_Name="+branchFilter)+"&ID="+btoa(RP_Ids), function() {});
//            //} else {
//                //dhxReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportData.php&"+chartFilterParams+"&MHType="+id), function() { });
//            //}
//          
//            setTimeout(function(){
//                preTally.Settings.progressOff(true, dhxLayout, null);
//            },1500);
//        },
//        


        offzAdminDashboard : function() {
            if (!dhxMiddleBlockTabs.cells("offzAdminDB")) {
                dhxMiddleBlockTabs.addTab("offzAdminDB", "<img src='images/icon/dashboards16.png' style='margin-top:2px;' />&nbsp;&nbsp;My Dashboard", 150);
                dhxMiddleBlockTabs.tabs("offzAdminDB").setActive();
                
            } else {
                dhxMiddleBlockTabs.tabs("offzAdminDB").setActive();
            }
        }
    };
})(jQuery, this);