;(function($, window, undefined) {
    var filterObject;
    var monthData;
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var sel_year;
    var monthlySummaryData;
    preTally.AccSummaryReports = {
        viewAccSummaryReports: function() {
                    AccSummaryFlag_Full   = 0;  
                    AccSummaryFlag_Med = 0;
                    AccSummaryFlag_Small = 0;
                    


            if (!dhxMiddleBlockTabs.cells("viewAccSummaryReports")) {
                reportInit=1;                
                dhxMiddleBlockTabs.addTab("viewAccSummaryReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Account Summary&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshTab'/>", 300);
                dhxMiddleBlockTabs.tabs("viewAccSummaryReports").setActive();
                
                dhxAccSummaryLayout =  dhxMiddleBlockTabs.cells("viewAccSummaryReports").attachLayout("1C");
                
                ptAccSummaryToolbar = dhxAccSummaryLayout.cells("a").attachToolbar();                
                ptAccSummaryToolbar.setIconsPath("images/icon/default_18/");
                
                ptAccSummaryToolbar.setAlign('right');
                dhxLayout.cells("b").collapse();
                $(".refreshTab").click(function(){

                    AccSummaryFlag_Full   = 0;  
                    AccSummaryFlag_Med = 0;
                    AccSummaryFlag_Small = 0;
                    if(id =='viewAccSummaryReportsSmall') {
                        dhxLayout.cells("b").expand();                                                       
                    } else {
                       dhxLayout.cells("b").collapse();
                    }
                    var actvId = ptAccSummaryTabbar.getActiveTab();                    
                    if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                    
                    if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull(); 
                });
                var Days_Options = [];
                var Months_Options = [];
                var Years_Options = [];

//                var monthNames = [ 
//                        "January", "February", "March","April", "May", "June",
//                        "July", "August", "September","October", "November", "December"
//                    ];
                    
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
                    if(i == Date.today().getFullYear()) {
                        if( Date.today().getMonth()+1 > 3)
                            Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                    } else
                        Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                }
                
               // ptAccSummaryToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptAccSummaryToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
               // ptAccSummaryToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                ptAccSummaryToolbar.addSeparator();
                
                ptAccSummaryToolbar.addText("text_from", null, "From");
                ptAccSummaryToolbar.addInput("rpt_date_from", null, "", 75);
                ptAccSummaryToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                ptAccSummaryToolbar.addSeparator();
                
                ptAccSummaryToolbar.addText("text_till", null, "Till");
                ptAccSummaryToolbar.addInput("rpt_date_till", null, "", 75);
                ptAccSummaryToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                ptAccSummaryToolbar.addSeparator();
                
                ptAccSummaryToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                

                var ptRpTb_Inp_Frm = ptAccSummaryToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptAccSummaryToolbar.getValue("rpt_date_till")) preTally.AccSummaryReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptAccSummaryToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptAccSummaryToolbar.getValue("rpt_date_from")) preTally.AccSummaryReports.setSens(ptRpTb_Inp_Frm, "min");
                }     
                ptAccSummaryToolbar.attachEvent("onClick", function(id){  
                    
                     AccSummaryFlag_Full   = 0;  
                    AccSummaryFlag_Med = 0;
                    AccSummaryFlag_Small = 0;
                    
                    var pId = ptAccSummaryToolbar.getParentId(id);
                    if(id == 'rpt_df_clear')
                        ptAccSummaryToolbar.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptAccSummaryToolbar.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        ptAccSummaryToolbar.setValue('rpt_date_from', dateToday, false);
                        ptAccSummaryToolbar.setValue('rpt_date_till', dateToday, false);
                        //ptAccSummaryToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptAccSummaryToolbar.setItemText('rpt_year_filter', 'Select Year');
                        monthData = '';
                        var actvId = ptAccSummaryTabbar.getActiveTab();
                    
                    if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                   
                    if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull(); 
                       
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptAccSummaryToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptAccSummaryToolbar.setValue('rpt_date_from', firstDay, false);
                        ptAccSummaryToolbar.setValue('rpt_date_till', lastDay, false);
                        ptAccSummaryToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptAccSummaryToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
                        if(ptAccSummaryToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData = ptAccSummaryToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        var actvId = ptAccSummaryTabbar.getActiveTab();
                        
                    
                    if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                    
                    if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull(); 
                       
                        
                    }
                    
                    if(pId == 'rpt_year_filter') {  
                        
                        var tmpDate     = new Date();
                        var m = tmpDate.getMonth();
                        var start_year=id;
                        var start_month,end_year,end_month;                        
                        if(id == tmpDate.getFullYear())
                        {
                            end_year=tmpDate.getFullYear()
                            if(m<8){
                                start_year=parseInt(id)-1;    
                                start_month=9;
                                /*if(m<6)
                                start_month=parseInt(m)+6;
                                else
                                start_month=parseInt(m)-6;*/
                            }else{
                            start_month=3
                            start_year=id;                            
                            }
                            end_month=parseInt(m)+1;
                        }
                        else {
                            end_year=parseInt(id)+1;
                            start_year=id;
                            start_month=3;
                            end_month=3;
                        }                         
                        var firstDay    = new Date(start_year, start_month, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(end_year, end_month, 0).toString("dd.MM.yyyy");
                        
                        ptAccSummaryToolbar.setValue('rpt_date_from', firstDay, false);
                        ptAccSummaryToolbar.setValue('rpt_date_till', lastDay, false);
                        //ptAccSummaryToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptAccSummaryToolbar.setItemText('rpt_month_filter', 'Select Month');
                        monthData = '';
                        var actvId = ptAccSummaryTabbar.getActiveTab();
                        sel_year=id;
                     
                    if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                    
                    if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull(); 

                        
                    }
                    if(id == 'rpt_date_filter'){
                        monthData = '';
                        //ptAccSummaryToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptAccSummaryToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptAccSummaryToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = ptAccSummaryTabbar.getActiveTab();
                       
                    if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                    
                    if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull();                      
                        
//                        preTally.AccSummaryReports.viewItemReports(rptFilterID);
                    }  
                });     
                
                ptMRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptMRpTb_Calendar.setDateFormat("%d.%m.%Y");
                           
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);   
                
                var tmpDate     = new Date();
                var financialYear = Date.today().getMonth() <= 2 ? Date.today().getFullYear() -1 : Date.today().getFullYear() ;
                var id=financialYear;
                        var m = tmpDate.getMonth();
                        var start_year=id;
                        var start_month,end_year,end_month;                        
                        if(id == tmpDate.getFullYear())
                        {
                            end_year=tmpDate.getFullYear()
                            if(m<8){
                                start_year=parseInt(id)-1; 
                                start_month=9;
                                /*if(m<6)
                                start_month=parseInt(m)+6;
                                else
                                start_month=parseInt(m)-6;*/
                            }else{
                            start_month=3
                            start_year=id;                            
                            }
                            end_month=parseInt(m)+1;
                        }
                        else {
                            end_year=parseInt(id)+1;
                            start_year=id;
                            start_month=3;
                            end_month=3;
                        }                         
                        var firstDay    = new Date(start_year, start_month, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(end_year, end_month, 0).toString("dd.MM.yyyy");
                        var selendyear=parseInt(id)+1
                        ptAccSummaryToolbar.setItemText('rpt_year_filter', id+"-"+selendyear);
                if(bsp==4){  
                    var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    monthData = date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    ptAccSummaryToolbar.setValue('rpt_date_from', firstDay);
                    ptAccSummaryToolbar.setValue('rpt_date_till', lastDay);
                    //ptAccSummaryToolbar.setItemText('rpt_month_filter',m_names[monthData]);
                   
                    reportInit=0;
                    monthData = monthData+1; // get current month number;starting with 1
                } else {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    ptAccSummaryToolbar.setValue('rpt_date_till', firstDay);
                    ptAccSummaryToolbar.setValue('rpt_date_from', lastDay);
                   // ptAccSummaryToolbar.setItemText('rpt_day_filter', 'Today');
                    reportInit=0;
                }
                 
       
                ptAccSummaryTabbar = dhxAccSummaryLayout.cells("a").attachTabbar();
                //ptAccSummaryTabbar.addTab("viewAccSummaryReportsSmall", "Account Summary");   
                ptAccSummaryTabbar.addTab("viewAccSummaryReportsMed", "Account Summary - Detailed");   
                ptAccSummaryTabbar.addTab("viewAccSummaryReportsFull", "Account Summary - Detailed with Sum");   
                preTally.AccSummaryReports.viewAccSummaryReportsMed(); 
                ptAccSummaryTabbar.tabs("viewAccSummaryReportsMed").setActive();
                
                ptAccSummaryTabbar.attachEvent("onTabClick", function(id, last_id){ 
                    
                    if(id =='viewAccSummaryReportsSmall') {
                        dhxLayout.cells("b").expand();                                                       
                    } else {
                       dhxLayout.cells("b").collapse();
                    }
                      
                    if(id == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();                    
                    if(id == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                    if(id == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull();
                    


                    return true;
                   
                });
                
                  
            } else {
                dhxMiddleBlockTabs.tabs("viewAccSummaryReports").setActive();
                
                var actvId = ptAccSummaryTabbar.getActiveTab();                
                if(id =='viewAccSummaryReportsSmall') {
                        dhxLayout.cells("b").expand();                                                       
                    } else {
                       dhxLayout.cells("b").collapse();
                    }

                 
                if(actvId == 'viewAccSummaryReportsSmall') preTally.AccSummaryReports.viewAccSummaryReportsSmall();
                if(actvId == 'viewAccSummaryReportsMed') preTally.AccSummaryReports.viewAccSummaryReportsMed();
                if(actvId == 'viewAccSummaryReportsFull') preTally.AccSummaryReports.viewAccSummaryReportsFull();                
                return true;
                
            }
        },  
        viewAccSummaryReportsSmall:function(){
            
            
             if(AccSummaryFlag_Small != 1){

                AccSummaryFlag_Small = 1;
                dhxAccSummaryLayoutSmall =  ptAccSummaryTabbar.cells("viewAccSummaryReportsSmall").attachLayout("1C");
                dhxAccSummaryLayoutSmall.cells("a").hideHeader();

                AccSummaryGridSmall = dhxAccSummaryLayoutSmall.cells("a").attachGrid();
                AccSummaryGridSmall.setImagePath("../../codebase/imgs/");                
                AccSummaryGridSmall.setHeader("SlNo,Year,Month,Opening Balance,Income,Expense,Closing Balance",null,['text-align:center;','text-align:center;','text-align:center;','text-align:center;','text-align:center;color:green;','text-align:center;color:red','text-align:center;']);                
                AccSummaryGridSmall.setInitWidths("50,150,150,*,*,*,*")
                AccSummaryGridSmall.setColAlign("center,left,left,right,right,right,right")
                AccSummaryGridSmall.setColTypes("ro,ro,ro,edn,edn,edn,edn");                
                AccSummaryGridSmall.init();
                AccSummaryGridSmall.enableTooltips("false,false,false,false,false,false,false");
                AccSummaryGridSmall.enableEditEvents(false,false,false);
                AccSummaryGridSmall.enableColSpan(true);
                AccSummaryGridSmall.setSkin("dhx_skyblue")
                AccSummaryGridSmall.enableSmartRendering(true,50);               
                
                AccSummaryGridSmall.setNumberFormat('0000',3);
                AccSummaryGridSmall.setNumberFormat('0000',4);
                AccSummaryGridSmall.setNumberFormat('0000',5);
                AccSummaryGridSmall.setNumberFormat('0000',6);                
                AccSummaryGridSmall.setNumberFormat('0000',7);
                var srtFlg = 0;
                $('.btn_mstrbrnchrptSort').click(function() {                          
                    var colId=$(this).attr("colNum")
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        AccSummaryGridSmall.sortRows(colId,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        AccSummaryGridSmall.sortRows(colId,"int","desc");
                        srtFlg = 0;
                    }
                });

                AccSummaryGridSmall.attachEvent("onXLE",function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                AccSummaryGridSmall.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                if(!sel_year)sel_year=Date.today().getFullYear();
                rptFilterParams = 'f='+ptAccSummaryToolbar.getValue("rpt_date_from")+'&t='+ptAccSummaryToolbar.getValue("rpt_date_till");

                AccSummaryGridSmall.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthlyDataSM.php&"+rptFilterParams),function(){                                      
                    

               });      
        }else{
                ptAccSummaryTabbar.tabs("viewAccSummaryReportsSmall").setActive();     
            } 
            
        },
        viewAccSummaryReportsMed:function(){
            
          if(AccSummaryFlag_Med != 1){

                AccSummaryFlag_Med = 1; 
                dhxAccSummaryLayoutMed =  ptAccSummaryTabbar.cells("viewAccSummaryReportsMed").attachLayout("1C");
                dhxAccSummaryLayoutMed.cells("a").hideHeader();

                AccSummaryGridMed = dhxAccSummaryLayoutMed.cells("a").attachGrid();
                AccSummaryGridMed.setImagePath("../../codebase/imgs/");
                AccSummaryGridMed.setHeader(",,,,#cspan,#cspan,#cspan,Income,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Expense,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,,#cspan,#cspan,#cspan");
                AccSummaryGridMed.attachHeader("SlNo,Year,Month,<div class='gridhdr green'>Opening Balance</div>,#cspan,#cspan,#cspan,<div class='gridhdr green2'>Income</div>,#cspan,#cspan,#cspan,<div class='gridhdr green3'>Internal Transfer</div>,#cspan,#cspan,#cspan,<div class='gridhdr red'>Expenses</div>,#cspan,#cspan,<div class='gridhdr red2'>Internal Transfer</div>,#cspan,#cspan,#cspan,<div class='gridhdr green'>Closing Balance</div>,#cspan,#cspan,#cspan,#cspan",["text-align:center;","text-align:center;","text-align:center;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;"]);
                AccSummaryGridMed.attachHeader("#,,,Cash,Bank,Stock,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank/Stock Opening Balance' class='btn_IT' id='ob'/>,Cash,Bank,Stock,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank/Stock Incomes' class='btn_IT' id='inc'/>,Cash,Bank,Job,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank Internal Transfer Income' class='btn_IT' id='it_inc'/>,Cash,Bank,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank/Stock Expense' class='btn_IT' id='exp'/>,Cash,Bank,Job,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank/Stock Internal Transfer Expense' class='btn_IT' id='it_exp'/>,Cash,Bank,Stock,Total<img src='images/icon/dbl_right_20.png' title='View Cash/Bank/Stock Closing Balance' id='cb' class='btn_IT'/>",["text-align:center;","text-align:center;","text-align:center;","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#b30000","text-align:center;color:#b30000","text-align:center;color:#b30000","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600"]);
                AccSummaryGridMed.setInitWidths("50,70,70,0,0,0,*,0,0,0,*,0,0,0,*,0,0,*,0,0,0,*,0,0,0,*")
                AccSummaryGridMed.setColumnMinWidth("50,70,70,0,0,0,125,0,0,0,125,0,0,0,125,0,0,125,0,0,0,125,0,0,0,125");
                AccSummaryGridMed.setColAlign("center,left,left,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right")
                AccSummaryGridMed.setColTypes("ro,ro,ro,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn")
                
                AccSummaryGridMed.init();
               // AccSummaryGridMed.splitAt(3);
                AccSummaryGridMed.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
                AccSummaryGridMed.enableEditEvents(false,false,false);
                AccSummaryGridMed.enableColSpan(true);
                AccSummaryGridMed.setSkin("dhx_skyblue")
                AccSummaryGridMed.enableSmartRendering(true,50);               
                
                AccSummaryGridMed.setNumberFormat("0,000",3,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",4,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",5,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",6,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",7,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",8,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",9,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",10,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",11,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",12,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",13,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",14,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",15,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",16,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",17,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",18,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",19,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",20,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",21,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",22,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",23,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",24,".",",");
                AccSummaryGridMed.setNumberFormat("0,000",25,".",",");
                var srtFlg = 0;
                $('.btn_mstrbrnchrptSort').click(function() {                          
                    var colId=$(this).attr("colNum")
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        AccSummaryGridMed.sortRows(colId,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        AccSummaryGridMed.sortRows(colId,"int","desc");
                        srtFlg = 0;
                    }
                });
                
                
                 var ob_flag    = 1;
                 var inc_flag   = 1;
                 var itinc_flag = 1;
                 var exp_flag   = 1;
                 var itexp_flag = 1;
                 var cb_flag    = 1;
                 
                    $('.btn_IT').click(function() {
                        var type=$(this).attr("id");

                        if(type=="ob"){
                        if(ob_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of Opening Balance');
                           
                            AccSummaryGridMed.setColWidth(3,"125");
                            AccSummaryGridMed.setColWidth(4,"125");
                            AccSummaryGridMed.setColWidth(5,"125");
                            AccSummaryGridMed.setColWidth(6,"125");
                            ob_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Opening Balance');
                            
                            AccSummaryGridMed.setColWidth(3,"0");
                            AccSummaryGridMed.setColWidth(4,"0");
                            AccSummaryGridMed.setColWidth(5,"0");
                            AccSummaryGridMed.setColWidth(6,"*");
                            
                            ob_flag = 1;
                        }
                    }else if(type=="inc"){
                        
                        if(inc_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of Income');
                           
                            AccSummaryGridMed.setColWidth(7,"125");
                            AccSummaryGridMed.setColWidth(8,"125");
                            AccSummaryGridMed.setColWidth(9,"125");
                            AccSummaryGridMed.setColWidth(10,"125");
                            inc_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Income');
                            
                            AccSummaryGridMed.setColWidth(7,"0");
                            AccSummaryGridMed.setColWidth(8,"0");
                            AccSummaryGridMed.setColWidth(9,"0");
                            AccSummaryGridMed.setColWidth(10,"*");
                            
                            inc_flag = 1;
                        }
                        
                        
                    }
                    else if(type=="it_inc"){
                        
                        if(itinc_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of  Internal Transfer');
                           
                            AccSummaryGridMed.setColWidth(11,"125");
                            AccSummaryGridMed.setColWidth(12,"125");
                            AccSummaryGridMed.setColWidth(13,"0");
                            AccSummaryGridMed.setColWidth(14,"125");
                            itinc_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Internal Transfer');
                            
                            AccSummaryGridMed.setColWidth(11,"0");
                            AccSummaryGridMed.setColWidth(12,"0");
                            AccSummaryGridMed.setColWidth(13,"0");
                            AccSummaryGridMed.setColWidth(14,"*");
                            
                            itinc_flag = 1;
                        }
                        
                        
                    }
                    else if(type=="exp"){
                        
                        if(itinc_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of  Internal Transfer');
                           
                            AccSummaryGridMed.setColWidth(15,"125");
                            AccSummaryGridMed.setColWidth(16,"125");
                            AccSummaryGridMed.setColWidth(17,"125");
                            
                            itinc_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Internal Transfer');
                            
                            AccSummaryGridMed.setColWidth(15,"0");
                            AccSummaryGridMed.setColWidth(16,"0");
                            AccSummaryGridMed.setColWidth(17,"*");                            
                            
                            itinc_flag = 1;
                        }
                        
                        
                    }
                    else if(type=="it_exp"){
                        
                        if(itinc_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of  Internal Transfer');
                           
                            AccSummaryGridMed.setColWidth(18,"125");
                            AccSummaryGridMed.setColWidth(19,"125");
                            AccSummaryGridMed.setColWidth(20,"0");
                            AccSummaryGridMed.setColWidth(21,"125");
                            itinc_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Internal Transfer');
                            
                            AccSummaryGridMed.setColWidth(18,"0");
                            AccSummaryGridMed.setColWidth(19,"0");
                            AccSummaryGridMed.setColWidth(20,"0");
                            AccSummaryGridMed.setColWidth(21,"*");
                            
                            itinc_flag = 1;
                        }
                        
                        
                    }else if(type=="cb"){
                        
                        if(itinc_flag == 1){
                          $(this).attr("src", 'images/icon/dbl_left_20.png');
                            $(this).attr("title", 'Hide Cash/Bank/Stock of  Internal Transfer');
                           
                            AccSummaryGridMed.setColWidth(22,"125");
                            AccSummaryGridMed.setColWidth(23,"125");
                            AccSummaryGridMed.setColWidth(24,"125");
                            AccSummaryGridMed.setColWidth(25,"125");
                            itinc_flag = 0;
                        }else{
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            $(this).attr("title", 'View Cash/Bank/Stock of Internal Transfer');
                            
                            AccSummaryGridMed.setColWidth(22,"0");
                            AccSummaryGridMed.setColWidth(23,"0");
                            AccSummaryGridMed.setColWidth(24,"0");
                            AccSummaryGridMed.setColWidth(25,"*");
                            
                            itinc_flag = 1;
                        }
                        
                        
                    }

                        

                    });
                AccSummaryGridMed.attachEvent("onXLE",function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                AccSummaryGridMed.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                if(!sel_year)sel_year=Date.today().getFullYear();
                rptFilterParams = 'f='+ptAccSummaryToolbar.getValue("rpt_date_from")+'&t='+ptAccSummaryToolbar.getValue("rpt_date_till");

                AccSummaryGridMed.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthlyData.php&"+rptFilterParams),function(){                                      
                    

               });         
         }else{
                ptAccSummaryTabbar.tabs("viewAccSummaryReportsMed").setActive();     
            } 
        },
        viewAccSummaryReportsFull : function(){            
          if(AccSummaryFlag_Full != 1){

                AccSummaryFlag_Full = 1; 
                dhxAccSummaryLayout =  ptAccSummaryTabbar.cells("viewAccSummaryReportsFull").attachLayout("1C");
                dhxAccSummaryLayout.cells("a").hideHeader();

                AccSummaryGridFull = dhxAccSummaryLayout.cells("a").attachGrid();
                AccSummaryGridFull.setImagePath("../../codebase/imgs/");
                AccSummaryGridFull.setHeader(",,,,#cspan,#cspan,#cspan,Income,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Expense,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,,#cspan,#cspan,#cspan");
                AccSummaryGridFull.attachHeader("SlNo,Year,Month,<div class='gridhdr green'>Opening Balance</div>,#cspan,#cspan,#cspan,<div class='gridhdr green2'>Income</div>,#cspan,#cspan,#cspan,<div class='gridhdr green3'>Internal Transfer</div>,#cspan,#cspan,#cspan,<div class='gridhdr red'>Expenses</div>,#cspan,#cspan,<div class='gridhdr red2'>Internal Transfer</div>,#cspan,#cspan,#cspan,<div class='gridhdr green'>Closing Balance</div>,#cspan,#cspan,#cspan,#cspan",["text-align:center;","text-align:center;","text-align:center;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;","text-align:center;padding:0px;"]);
                AccSummaryGridFull.attachHeader("#,,,Cash,Bank,Stock,Total,Cash,Bank,Stock,Total,Cash,Bank,Job,Total,Cash,Bank,Total,Cash,Bank,Job,Total,Cash,Bank,Stock,Total",["text-align:center;","text-align:center;","text-align:center;","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#4d9900","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#66cc00","text-align:center;color:#b30000","text-align:center;color:#b30000","text-align:center;color:#b30000","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#ff6666","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600","text-align:center;color:#226600"]);
                AccSummaryGridFull.setInitWidths("50,70,70,125,125,125,125,125,125,125,125,125,125,0,125,125,125,125,125,125,0,125,125,125,125,125,125,125,125")
                AccSummaryGridFull.setColAlign("center,left,left,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right")
                AccSummaryGridFull.setColTypes("ro,ro,ro,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn,edn")
                
                AccSummaryGridFull.init();
               // AccSummaryGridFull.splitAt(3);
                AccSummaryGridFull.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
                AccSummaryGridFull.enableEditEvents(false,false,false);
                AccSummaryGridFull.enableColSpan(true);
                AccSummaryGridFull.setSkin("dhx_skyblue")
                AccSummaryGridFull.enableSmartRendering(true,50);
                AccSummaryGridFull.setNumberFormat("0,000",3,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",4,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",5,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",6,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",7,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",8,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",9,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",10,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",11,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",12,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",13,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",14,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",15,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",16,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",17,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",18,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",19,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",20,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",21,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",22,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",23,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",24,".",",");
                AccSummaryGridFull.setNumberFormat("0,000",25,".",",");
                var srtFlg = 0;
                $('.btn_mstrbrnchrptSort').click(function() {                          
                    var colId=$(this).attr("colNum")
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        AccSummaryGridFull.sortRows(colId,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        AccSummaryGridFull.sortRows(colId,"int","desc");
                        srtFlg = 0;
                    }
                });

                AccSummaryGridFull.attachEvent("onXLE",function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                AccSummaryGridFull.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                if(!sel_year)sel_year=Date.today().getFullYear();
                rptFilterParams = 'f='+ptAccSummaryToolbar.getValue("rpt_date_from")+'&t='+ptAccSummaryToolbar.getValue("rpt_date_till");

                AccSummaryGridFull.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthlyData.php&"+rptFilterParams),function(){                                      
                    

               });         
          }else{
                ptAccSummaryTabbar.tabs("viewAccSummaryReportsFull").setActive();     
            } 
        },        
        setSens: function (inp, k) {
            if (k == "min") {
                ptMRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptMRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        calculateFooterValues : function(){
            var mrOB = document.getElementById("mr_OB");
            mrOB.innerHTML = (preTally.AccSummaryReports.sumColumn(2)+preTally.AccSummaryReports.sumColumn(3)).toFixed(2);

            var mrInc = document.getElementById("mr_INC");
            mrInc.innerHTML = (preTally.AccSummaryReports.sumColumn(5)+preTally.AccSummaryReports.sumColumn(6)).toFixed(2);

            var mrExp = document.getElementById("mr_EXP");
            mrExp.innerHTML = (preTally.AccSummaryReports.sumColumn(7)+preTally.AccSummaryReports.sumColumn(8)).toFixed(2);
            
            var mrCB = document.getElementById("mr_CB");
            mrCB.innerHTML = (preTally.AccSummaryReports.sumColumn(9)+preTally.AccSummaryReports.sumColumn(10)).toFixed(2);

            var mrTR = document.getElementById("mr_TR");
            mrTR.innerHTML = (preTally.AccSummaryReports.sumColumn(13)+preTally.AccSummaryReports.sumColumn(14)).toFixed(2);
            
            var mrTP = document.getElementById("mr_TP");
            mrTP.innerHTML = (preTally.AccSummaryReports.sumColumn(15)+preTally.AccSummaryReports.sumColumn(16)).toFixed(2);
            
            var mrOBS = document.getElementById("mr_OBS");
            mrOBS.innerHTML = "Opening Balance :  " + ( parseFloat( $("#mr_OB").text() )+preTally.AccSummaryReports.sumColumn(4) ).toFixed(0);
           
            var mrCBS = document.getElementById("mr_CBS");
            mrCBS.innerHTML = "Closing Balance :  " + ( parseFloat( $("#mr_CB").text() )+preTally.AccSummaryReports.sumColumn(11) ).toFixed(0);
            
            var mrIncS = document.getElementById("mr_INCS");
            mrIncS.innerHTML = $("#mr_INC").text();

            var mrExpS = document.getElementById("mr_EXPS");
            mrExpS.innerHTML = $("#mr_EXP").text();

            var mrTRS = document.getElementById("mr_TRS");
            mrTRS.innerHTML = $("#mr_TR").text();
            
            var mrTPS = document.getElementById("mr_TPS");
            mrTPS.innerHTML = $("#mr_TP").text();

        },
        sumColumn : function(ind){
		var out = 0;
		for(var i=0;i< AccSummaryGrid.getRowsNum();i++){
			out+= parseFloat(AccSummaryGrid.cells2(i,ind).getValue());
		}
		return out;
	}
    };
})(jQuery, this);