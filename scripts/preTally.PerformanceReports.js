;(function($, window, undefined) {
    preTally.PerformanceReports = {
        viewPerformanceReports: function() {

            if (!dhxMiddleBlockTabs.cells("viewPerformanceReports")) {
                reportInit=1;
                
                dhxMiddleBlockTabs.addTab("viewPerformanceReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Performance Reports", 200);
                dhxMiddleBlockTabs.tabs("viewPerformanceReports").setActive();
                
                dhxPerformanceReportsLayout =  dhxMiddleBlockTabs.cells("viewPerformanceReports").attachLayout("1C");
                
                ptPerfRptToolbar = dhxPerformanceReportsLayout.cells("a").attachToolbar();                
                ptPerfRptToolbar.setIconsPath("images/icon/default_18/");
                
                ptPerfRptToolbar.setAlign('right');
                
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
                
                ptPerfRptToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptPerfRptToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptPerfRptToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                ptPerfRptToolbar.addSeparator();
                          
                ptPerfRptToolbar.addText("text_from", null, "From");
                ptPerfRptToolbar.addInput("rpt_date_from", null, "", 75);
                ptPerfRptToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                ptPerfRptToolbar.addSeparator();
                
                ptPerfRptToolbar.addText("text_till", null, "Till");
                ptPerfRptToolbar.addInput("rpt_date_till", null, "", 75);
                ptPerfRptToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                ptPerfRptToolbar.addSeparator();
                
                ptPerfRptToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                
              
                
                var ptRpTb_Inp_Frm = ptPerfRptToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptPerfRptToolbar.getValue("rpt_date_till")) preTally.PerformanceReports.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptPerfRptToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptPerfRptToolbar.getValue("rpt_date_from")) preTally.PerformanceReports.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                ptPerfRptToolbar.attachEvent("onClick", function(id){  
                   var pId = ptPerfRptToolbar.getParentId(id);
                   
                    if(id == 'rpt_df_clear')
                        ptPerfRptToolbar.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptPerfRptToolbar.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        ptPerfRptToolbar.setValue('rpt_date_from', dateToday, false);
                        ptPerfRptToolbar.setValue('rpt_date_till', dateToday, false);
                        ptPerfRptToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptPerfRptToolbar.setItemText('rpt_year_filter', 'Select Year');
//                        
//                        var actvId = ptPerformanceReportsTabbar.getActiveTab();
//                        
//                        if(actvId == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
//                        if(actvId == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
//                        if(actvId == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
//                        if(actvId == 'viewBrnchItemReports') preTally.PerformanceReports.viewBrnchItemReports(rptBrnchItemId);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptPerfRptToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptPerfRptToolbar.setValue('rpt_date_from', firstDay, false);
                        ptPerfRptToolbar.setValue('rpt_date_till', lastDay, false);
                        ptPerfRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptPerfRptToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
//                        var actvId = ptPerformanceReportsTabbar.getActiveTab();
//                        
//                        if(actvId == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
//                        if(actvId == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
//                        if(actvId == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
//                        if(actvId == 'viewBrnchItemReports') preTally.PerformanceReports.viewBrnchItemReports(rptBrnchItemId);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate     = new Date();
                       
                        var firstDay    = new Date(id, 03, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(++id, 02, 31).toString("dd.MM.yyyy");
                        
                        ptPerfRptToolbar.setValue('rpt_date_from', firstDay, false);
                        ptPerfRptToolbar.setValue('rpt_date_till', lastDay, false);
                        ptPerfRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptPerfRptToolbar.setItemText('rpt_month_filter', 'Select Month');
                        
//                        var actvId = ptPerformanceReportsTabbar.getActiveTab();
//                        
//                        if(actvId == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
//                        if(actvId == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
//                        if(actvId == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
//                        if(actvId == 'viewBrnchItemReports') preTally.PerformanceReports.viewBrnchItemReports(rptBrnchItemId);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        ptPerfRptToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptPerfRptToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptPerfRptToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
//                        var actvId = ptPerformanceReportsTabbar.getActiveTab();
//                        
//                        if(actvId == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
//                        if(actvId == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
//                        if(actvId == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
//                        if(actvId == 'viewBrnchItemReports') preTally.PerformanceReports.viewBrnchItemReports(rptBrnchItemId);
//                        
//                        preTally.PerformanceReports.viewItemWiseReports(rptFilterID);
                    }  
                });
                ptMRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptMRpTb_Calendar.setDateFormat("%d.%m.%Y");
                           
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp==4){   
                    var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    var month=date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    ptPerfRptToolbar.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    ptPerfRptToolbar.setValue('rpt_date_till', tDate);
                    ptPerfRptToolbar.setItemText('rpt_month_filter',m_names[month]);
                    reportInit=0;
                } else {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    ptPerfRptToolbar.setValue('rpt_date_till', cDate);
                    ptPerfRptToolbar.setValue('rpt_date_from', cDate);
                    ptPerfRptToolbar.setItemText('rpt_day_filter', 'Today');
                    reportInit=0;
                }
                
//                ptPerfRptToolbar.setAlign('right');
       
                ptPerformanceReportsTabbar = dhxPerformanceReportsLayout.cells("a").attachTabbar();
//                ptPerformanceReportsTabbar.addTab("viewPerformanceRptSummary", tb_data_txt, "465px");
                ptPerformanceReportsTabbar.addTab("viewPerformanceRptSummary", "Performance Reports");
//                ptPerformanceReportsTabbar.addTab("viewBranchWiseReports", "Branch Based Reports");
//                ptPerformanceReportsTabbar.addTab("viewItemWiseReports", "Item Based Reports");
//                
                preTally.PerformanceReports.viewPerformanceRptSummary();
                ptPerformanceReportsTabbar.tabs("viewPerformanceRptSummary").setActive();
                
//                ptPerformanceReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
//                    
//                    if(id =='viewBranchWiseReports') {
//                        dhxAccord.cells("a4").show();
//                        dhxAccord.cells("a4").open();                                 
//                    } else {
//                        dhxAccord.cells("a4").hide();
//                        dhxAccord.cells("a1").open();  
//                    }
//                    
//                    if(id == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
//                    if(id == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
//                    if(id == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
//                    return true;
//                   
//                });
                
                  
            } else {
                dhxMiddleBlockTabs.tabs("viewPerformanceReports").setActive();
                
                var actvId = ptPerformanceReportsTabbar.getActiveTab();                
//                if( actvId =='viewBranchWiseReports') {
//                    dhxAccord.cells("a4").show();
//                    dhxAccord.cells("a4").open();                                 
//                } else {
//                    dhxAccord.cells("a4").hide();
//                    dhxAccord.cells("a1").open();  
//                }

                if(actvId == 'viewPerformanceRptSummary') preTally.PerformanceReports.viewPerformanceRptSummary();
                if(actvId == 'viewItemWiseReports')   preTally.PerformanceReports.viewItemWiseReports();
                if(actvId == 'viewBranchWiseReports') preTally.PerformanceReports.viewBranchWiseReports();
                return true;
                
            }
        },
        viewPerformanceRptSummary : function(){
            
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            dhxReportsVisualLayout =  ptPerformanceReportsTabbar.cells("viewPerformanceRptSummary").attachLayout("1C");
            dhxReportsVisualLayout.cells("a").hideHeader();
            
            var Width = dhxReportsVisualLayout.cells("a").getWidth();
             
            dhxReportsVisualForm = dhxReportsVisualLayout.cells("a").attachForm();
            rptFilterParams = 'f='+ptPerfRptToolbar.getValue("rpt_date_from")+'&t='+ptPerfRptToolbar.getValue("rpt_date_till");
            
            dhxReportsVisualForm.loadStruct(preTally.initialize.encryptURL("requisites/viewPerformanceRptSummary.php&Width="+Width+"&"+rptFilterParams), function() {  
                
                
                var itemData = {type: "template",  offsetLeft:"30", offsetTop:"50",labelWidth : "550", inputWidth : "380", list: [
                                {type: "template", offsetLeft:"20", name : "yearHead" , value: "DATE WISE DETAILS",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"10",  name : "allYear" ,  value : "ALL YEAR",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "thisYear",  value : "THIS YEAR", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "thisMonth", value : "THIS MONTH", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "thisWeek",  value : "THIS WEEK",  format : preTally.PerformanceReports.setTemplate },
                                {type: "newcolumn", offsetLeft : "150"},
                                {type: "template", offsetLeft:"20", name : "locHead" ,  value: "BRANCH WISE DETAILS",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "allBranch", value : "ALL BRANCH",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "singleBranch",  value : "SINGLE BRANCH", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "allDept",   value : "ALL DEPARTMENT", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "allUsers",  value : "ALL USER",  format : preTally.PerformanceReports.setTemplate },
                                {type: "newcolumn", offsetLeft : "150"},
                                {type: "template", offsetLeft:"20", name : "itmHead" ,  value: "ITEM WISE DETAILS",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "mainHead" , value : "MAIN HEAD",  format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "subHead",   value : "SUB HEAD", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "allItem",   value : "ITEM", format : preTally.PerformanceReports.setTemplate },
                                {type: "template", offsetTop:"3",   name : "allDesc",   value : "DESCRIPTION",  format : preTally.PerformanceReports.setTemplate }
                            ]};
                dhxReportsVisualForm.addItem(null, itemData, 7 );
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
                
        },
        setTemplate : function(name, value){
            if(name == 'yearHead' || name == 'locHead' || name == 'itmHead' ) return "<div class='simple_bold'>"+value+"</div>";
            else return '<div class="simple_link"><a href="javascript:void(0);" onclick="preTally.PerformanceReports.viewCategoryWiseReports(\'' + name + '\');">'+value+'</a></div>';
        },
        viewCategoryWiseReports : function(tabName){
            
            if(tabName == 'allYear')   preTally.PerformanceReports.viewYearWiseReports();
            if(tabName == 'thisYear')  preTally.PerformanceReports.viewThisYearReports();
            if(tabName == 'thisMonth') preTally.PerformanceReports.viewThisMonthReports();
            if(tabName == 'thisWeek')  preTally.PerformanceReports.viewThisWeekReports();
            if(tabName == 'allBranch') preTally.PerformanceReports.viewBranchWiseReports();
            if(tabName == 'singleBranch') preTally.PerformanceReports.viewItemWiseReports();
            if(tabName == 'allDept')   preTally.PerformanceReports.viewBranchWiseReports();
            if(tabName == 'allUsers')  preTally.PerformanceReports.viewItemWiseReports();
            if(tabName == 'allBranch') preTally.PerformanceReports.viewBranchWiseReports();
            if(tabName == 'mainHead')  preTally.PerformanceReports.viewItemWiseReports();
            if(tabName == 'subHead')   preTally.PerformanceReports.viewBranchWiseReports();
            if(tabName == 'allItem')   preTally.PerformanceReports.viewItemWiseReports();
            if(tabName == 'allDesc')   preTally.PerformanceReports.viewDescWiseReports();
        },
        viewDescWiseReports : function(){
            myTreeGrid = new dhtmlXGridObject('gridbox');
			myTreeGrid.setImagePath("../../../codebase/imgs/");
			myTreeGrid.setHeader("Tree,Plain Text,Long Text,Color,Checkbox");
			myTreeGrid.setInitWidths("150,100,100,100,100");
			myTreeGrid.setColAlign("left,left,left,left,center");
			myTreeGrid.setColTypes("tree,ed,txt,ch,ch");
			myTreeGrid.setColSorting("str,str,str,na,str");
			myTreeGrid.init();
			myTreeGrid.setSkin("dhx_skyblue");
			myTreeGrid.kidsXmlFile = "php/treeGrid_dynamic2.php";
			myTreeGrid.loadXML("php/treeGrid_dynamic2.php");
        },
        viewYearWiseReports : function(){
            
            if(!ptPerformanceReportsTabbar.cells("viewYearWiseReports")){
                    
                ptPerformanceReportsTabbar.addTab("viewYearWiseReports", "Yearly Reports",null, null, null, true);
                ptPerformanceReportsTabbar.tabs("viewYearWiseReports").setActive();
                
                dhxYearlyReportsLayout =  ptPerformanceReportsTabbar.cells("viewYearWiseReports").attachLayout("1C");
                dhxYearlyReportsLayout.cells("a").hideHeader();
                yearlyReportsGrid = dhxYearlyReportsLayout.cells("a").attachGrid();
                yearlyReportsGrid.init();
                
                yearlyReportsGrid.loadXML(preTally.initialize.encryptURL("requisites/reportYearlyData.php"),function(){
                    
                });
                           
            }else{
                ptPerformanceReportsTabbar.tabs("viewYearWiseReports").setActive();     
            }
        },
        viewThisYearReports : function(){
            
            if(!ptPerformanceReportsTabbar.cells("viewThisYearReports")){
                    
                ptPerformanceReportsTabbar.addTab("viewThisYearReports", "This Year Reports",null, null, null, true);
                ptPerformanceReportsTabbar.tabs("viewThisYearReports").setActive();
                
                dhxYearlyReportsLayout =  ptPerformanceReportsTabbar.cells("viewThisYearReports").attachLayout("1C");
                dhxYearlyReportsLayout.cells("a").hideHeader();
                yearlyReportsGrid = dhxYearlyReportsLayout.cells("a").attachGrid();
                yearlyReportsGrid.init();
                
                yearlyReportsGrid.loadXML(preTally.initialize.encryptURL("requisites/reportThisYearData.php"),function(){
                    
                });
                           
            }else{
                ptPerformanceReportsTabbar.tabs("viewThisYearReports").setActive();     
            }
        },
        viewThisMonthReports : function(){
            
            if(!ptPerformanceReportsTabbar.cells("viewThisMonthReports")){
                    
                ptPerformanceReportsTabbar.addTab("viewThisMonthReports", "This Month Reports",null, null, null, true);
                ptPerformanceReportsTabbar.tabs("viewThisMonthReports").setActive();
                
                dhxThisMntReportsLayout =  ptPerformanceReportsTabbar.cells("viewThisMonthReports").attachLayout("1C");
                dhxThisMntReportsLayout.cells("a").hideHeader();
                dailyReportsGrid = dhxThisMntReportsLayout.cells("a").attachGrid();
                dailyReportsGrid.init();
                
                dailyReportsGrid.loadXML(preTally.initialize.encryptURL("requisites/reportThisMonthData.php"),function(){
                    
                });
                           
            }else{
                ptPerformanceReportsTabbar.tabs("viewThisMonthReports").setActive();     
            }
        },
        viewThisWeekReports : function(){
            
            if(!ptPerformanceReportsTabbar.cells("viewThisWeekReports")){
                    
                ptPerformanceReportsTabbar.addTab("viewThisWeekReports", "Current Week Reports",null, null, null, true);
                ptPerformanceReportsTabbar.tabs("viewThisWeekReports").setActive();
                
                dhxThisMntReportsLayout =  ptPerformanceReportsTabbar.cells("viewThisWeekReports").attachLayout("1C");
                dhxThisMntReportsLayout.cells("a").hideHeader();
                dailyReportsGrid = dhxThisMntReportsLayout.cells("a").attachGrid();
                dailyReportsGrid.init();
                
                dailyReportsGrid.loadXML(preTally.initialize.encryptURL("requisites/reportThisWeekData.php"),function(){
                    
                });
                           
            }else{
                ptPerformanceReportsTabbar.tabs("viewThisWeekReports").setActive();     
            }
        },
        viewBranchWiseReports : function(){
            if(!ptPerformanceReportsTabbar.cells("viewBranchWiseReports")){
                    ptPerformanceReportsTabbar.addTab("viewBranchWiseReports", "Branch Wise  Reports",null, null, null, true);
                    ptPerformanceReportsTabbar.tabs("viewBranchWiseReports").setActive();    
                    var filterObject;
                    dhxBranchReportsLayout =  ptPerformanceReportsTabbar.cells("viewBranchWiseReports").attachLayout("1C");
                    dhxBranchReportsLayout.cells("a").hideHeader();

                    //BranchReportsGrid.destructor();                
                    BranchReportsGrid = dhxBranchReportsLayout.cells("a").attachGrid();
                    BranchReportsGrid.setImagePath("../../codebase/imgs/");
                    BranchReportsGrid.setHeader("#,Branch,Opening Balance,#cspan,#cspan,Income,#cspan,Expense,#cspan,Closing Balance,#cspan,#cspan,<div style='text-align:left;'>Business <img src='images/icon/arrow_g_16.png' title='View Internal Transfers' class='btn_IT'/></div>,Internal Transfer Received,#cspan,Internal Transfer Paid,#cspan");
                    BranchReportsGrid.attachHeader("#,#text_filter,Cash,Bank,Stock,Cash,Bank,Cash,Bank,Cash,Bank,Stock,Business,Cash,Bank,Cash,Bank");
                    BranchReportsGrid.setInitWidths("40,*,*,*,*,*,*,*,*,*,*,*,100,*,*,*,*")
                    BranchReportsGrid.setColAlign("center,left,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right")
                    BranchReportsGrid.setColTypes("ro,ro,price,price,price,price,price,price,price,price,price,price,price,price,price,price,price")
                    BranchReportsGrid.init();
                    BranchReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                    BranchReportsGrid.enableEditEvents(false,false,false);
                    BranchReportsGrid.setSkin("dhx_skyblue")
                    BranchReportsGrid.enableSmartRendering(true,50);
                    BranchReportsGrid.attachFooter("Sum,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>");
                    BranchReportsGrid.attachFooter("Total Amount,#cspan,<span style='float:right;'><div id='mr_OB'>0</div></span>,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_INC'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_EXP'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_CB'>0</div></span>,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_TR'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_TP'>0</div></span>,#cspan");
                    BranchReportsGrid.attachFooter("<span style='float:right;'><div id='mr_OBS'>0</div></span>,#cspan,#cspan,#cspan,#cspan,<span style='float:right;'><div id='mr_INCS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_EXPS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_CBS'>0</div></span>,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'><div id='mr_TRS'>0</div></span>,#cspan,<span style='float:right;'><div id='mr_TPS'>0</div></span>,#cspan");
                    BranchReportsGrid.setNumberFormat('0000',2);
                    BranchReportsGrid.setNumberFormat('0000',3);
                    BranchReportsGrid.setNumberFormat('0000',4);
                    BranchReportsGrid.setNumberFormat('0000',5);
                    BranchReportsGrid.setNumberFormat('0000',6);
                    BranchReportsGrid.setNumberFormat('0000',7);
                    BranchReportsGrid.setNumberFormat('0000',8);
                    BranchReportsGrid.setNumberFormat('0000',9);
                    BranchReportsGrid.setNumberFormat('0000',10);
                    BranchReportsGrid.setNumberFormat('0000',11);
                    BranchReportsGrid.setNumberFormat('0000',12);
                    BranchReportsGrid.setNumberFormat('0000',13);
                    BranchReportsGrid.setNumberFormat('0000',14);
                    BranchReportsGrid.setNumberFormat('0000',15);
                    BranchReportsGrid.setNumberFormat('0000',16);

                    BranchReportsGrid.setColumnHidden(13, true);
                    BranchReportsGrid.setColumnHidden(14, true);
                    BranchReportsGrid.setColumnHidden(15, true);
                    BranchReportsGrid.setColumnHidden(16, true);


                    BranchReportsGrid.attachEvent("onXLE",function(){
                           preTally.Settings.progressOff(true, dhxLayout, null);
                       });
                       BranchReportsGrid.attachEvent("onXLS",function(){
                           preTally.Settings.progressOn(true, dhxLayout, null);
                       });
                    rptFilterParams = 'f='+ptPerfRptToolbar.getValue("rpt_date_from")+'&t='+ptPerfRptToolbar.getValue("rpt_date_till");

                    BranchReportsGrid.loadXML(preTally.initialize.encryptURL("requisites/reportBranchData.php&"+rptFilterParams),function(){
                        filterObject = BranchReportsGrid.getFilterElement(1);
                        filterObject.onkeyup = function(){
                            if(reportDetailsGrid) reportDetailsGrid.clearSelection();
                        };
                        filterObject.placeholder = "Branch";
                        preTally.PerformanceReports.calculateFooterValues();
                        var flag = 1;
                        $('.btn_IT').click(function() {

                            if(flag == 1){
                              $(this).attr("src", 'images/icon/arrow_lr_16.gif');
                                $(this).attr("title", 'Hide Internal Transfers');

                                BranchReportsGrid.setColumnHidden(13, false);
                                BranchReportsGrid.setColumnHidden(14, false);
                                BranchReportsGrid.setColumnHidden(15, false);
                                BranchReportsGrid.setColumnHidden(16, false);

        //                        preTally.PerformanceReports.calculateFooterValues();

                                flag = 0;

                            }else{

                                $(this).attr("src", 'images/icon/arrow_g_16.png');
                                $(this).attr("title", 'View Internal Transfers');

                                BranchReportsGrid.setColumnHidden(13, true);
                                BranchReportsGrid.setColumnHidden(14, true);
                                BranchReportsGrid.setColumnHidden(15, true);
                                BranchReportsGrid.setColumnHidden(16, true);

        //                        preTally.PerformanceReports.calculateFooterValues();

                                flag = 1;
                            }



                        });

                   });

                    if(reportDetailsGrid) reportDetailsGrid.destructor();

                    dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp; Legend Details"); 
                    reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
                    reportDetailsGrid.setHeader("<div style='text-align:left;'>Branch <input type = 'button' name='ALL' value = 'View All' class='btn_All'/></div>");
                    reportDetailsGrid.setInitWidths("*");
                    reportDetailsGrid.setColAlign("left");
                    reportDetailsGrid.setColTypes("ro");
                    reportDetailsGrid.enableTooltips("false");

                    reportDetailsGrid.init();
                    reportDetailsGrid.loadXML(preTally.initialize.encryptURL("requisites/rpt_listBranch.php"), function() {

                        $('.btn_All').click(function() {
                            BranchReportsGrid.filterBy(1,'');
                            filterObject.value = "All";
                            if(reportDetailsGrid) reportDetailsGrid.clearSelection();

                            preTally.PerformanceReports.calculateFooterValues();

                        });

                       reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
        //                       console.log(reportDetailsGrid.cells(id,0).getValue()+"--"+this.value);
                            var LCfilterText = reportDetailsGrid.cells(id,0).getValue();
                            if (LCfilterText == 'ALL') LCfilterText = '';
                            BranchReportsGrid.filterBy(1,LCfilterText);
                            filterObject.value = LCfilterText;

                            preTally.PerformanceReports.calculateFooterValues();
                       });                    
                    });

                    BranchReportsGrid.attachEvent("onFilterEnd", function(elements){
                        preTally.PerformanceReports.calculateFooterValues();
                    });                                          
            }else{
                ptPerformanceReportsTabbar.tabs("viewBranchWiseReports").setActive();    
            }
        },
        viewItemWiseReports : function(){   
            if(!ptPerformanceReportsTabbar.cells("viewItemWiseReports")){
                
                ptPerformanceReportsTabbar.addTab("viewItemWiseReports", "Item Wise  Reports",null, null, null, true);
                ptPerformanceReportsTabbar.tabs("viewItemWiseReports").setActive();  
                dhxItemReportsLayout =  ptPerformanceReportsTabbar.cells("viewItemWiseReports").attachLayout("1C");
                dhxItemReportsLayout.cells("a").hideHeader();
                
                ItemReportsGrid = dhxItemReportsLayout.cells("a").attachGrid();
                ItemReportsGrid.setImagePath("../../codebase/imgs/");
                ItemReportsGrid.setSkin("dhx_skyblue")
                //ItemReportsGrid.setHeader("#,<div id='itmRF' style='width: 90%;' placeholder='Item'></div>,\Sub Head,Amount");                    
                ItemReportsGrid.setHeader("#,#select_filter,#combo_filter,#combo_filter,#combo_filter,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' class='btn_Sort'/></div>");
                ItemReportsGrid.setNumberFormat("0,000.00",4);
                ItemReportsGrid.setInitWidths("40,120,*,*,*,120")
                ItemReportsGrid.setColAlign("left,left,left,left,left,right")
                ItemReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro");
//                ItemReportsGrid.setColSorting("int,str,str,str,str");  
                ItemReportsGrid.enableEditEvents(true,true,true);
                //ItemReportsGrid.enableTooltips("false,false,false,false");
                ItemReportsGrid.init();
                
                var itCombo = ItemReportsGrid.getFilterElement(2);
                itCombo.setPlaceholder("Items");
                
                var shCombo = ItemReportsGrid.getFilterElement(3);
                shCombo.setPlaceholder("Subhead");
                
                var mhCombo = ItemReportsGrid.getFilterElement(4);
                mhCombo.setPlaceholder("Mainhead");
                
                ItemReportsGrid.attachEvent("onRowSelect",function(rowId){
                    rptBrnchItemId=rowId;
                    preTally.PerformanceReports.viewBrnchItemReports(rowId,ItemReportsGrid.getUserData(rowId,"IT_Name"));
                });
                ItemReportsGrid.attachEvent("onXLE",function(){
                   preTally.Settings.progressOff(true, dhxLayout, null);
                });
                ItemReportsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                preTally.PerformanceReports.applyReportItemFilter();
            }else{
                ptPerformanceReportsTabbar.tabs("viewItemWiseReports").setActive();    
            }
        },
        applyReportItemFilter:  function(){
//            var filterValue ="";
//            if($('#itmRF').val()){
//            filterValue ="&filter="+$('#itmRF').val();
//            }  
            ItemReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            rptFilterParams = '&f='+ptPerfRptToolbar.getValue("rpt_date_from")+'&t='+ptPerfRptToolbar.getValue("rpt_date_till");
           
            ItemReportsGrid.clearAndLoad(preTally.initialize.encryptURL("requisites/reportItemData.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null); 
                
                var srtFlg = 0;
                $('.btn_Sort').click(function() { 
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        ItemReportsGrid.sortRows(5,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        ItemReportsGrid.sortRows(5,"int","desc");
                        srtFlg = 0;
                    }
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
        calculateFooterValues : function(){
            var mrOB = document.getElementById("mr_OB");
            mrOB.innerHTML = preTally.PerformanceReports.sumColumn(2)+preTally.PerformanceReports.sumColumn(3);

            var mrInc = document.getElementById("mr_INC");
            mrInc.innerHTML = preTally.PerformanceReports.sumColumn(5)+preTally.PerformanceReports.sumColumn(6);

            var mrExp = document.getElementById("mr_EXP");
            mrExp.innerHTML = preTally.PerformanceReports.sumColumn(7)+preTally.PerformanceReports.sumColumn(8);
            
            var mrCB = document.getElementById("mr_CB");
            mrCB.innerHTML = preTally.PerformanceReports.sumColumn(9)+preTally.PerformanceReports.sumColumn(10);

            var mrTR = document.getElementById("mr_TR");
            mrTR.innerHTML = preTally.PerformanceReports.sumColumn(12)+preTally.PerformanceReports.sumColumn(13);
            
            var mrTP = document.getElementById("mr_TP");
            mrTP.innerHTML = preTally.PerformanceReports.sumColumn(14)+preTally.PerformanceReports.sumColumn(15);
            
            var mrOBS = document.getElementById("mr_OBS");
            mrOBS.innerHTML = "Opening Balance :  " + ( parseFloat( $("#mr_OB").text() )+preTally.PerformanceReports.sumColumn(4) ).toFixed(0);
           
            var mrCBS = document.getElementById("mr_CBS");
            mrCBS.innerHTML = "Closing Balance :  " + ( parseFloat( $("#mr_CB").text() )+preTally.PerformanceReports.sumColumn(11) ).toFixed(0);
            
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
		for(var i=0;i< BranchReportsGrid.getRowsNum();i++){
			out+= parseFloat(BranchReportsGrid.cells2(i,ind).getValue());
		}
		return out;
	},
        viewBrnchItemReports : function(Item_id){              
                preTally.Settings.progressOn(true, dhxLayout, null); 
                if(!ptPerformanceReportsTabbar.cells("viewBrnchItemReports")){
                    ptPerformanceReportsTabbar.addTab("viewBrnchItemReports", "Branch Based Item Reports",null, null, null, true);
                    dhxBrnchItemReportsLayout =  ptPerformanceReportsTabbar.cells("viewBrnchItemReports").attachLayout("2U");
                dhxBrnchItemReportsLayout.cells("a").hideHeader();                                   
                dhxBrnchItemReportsLayout.cells("b").hideHeader();  
                dhxBrnchItemReportsLayout.cells("a").setWidth('200');
                BrnchItemReportsGrid = dhxBrnchItemReportsLayout.cells("b").attachGrid();
                BrnchItemReportsGrid.setImagePath("../../codebase/imgs/");
                BrnchItemReportsGrid.setSkin("dhx_skyblue")
                BrnchItemReportsGrid.setHeader("#,Item,#select_filter_strict,Amount");                    
                BrnchItemReportsGrid.setNumberFormat("0,000.00",4);
                BrnchItemReportsGrid.setInitWidths("40,*,*,*,120")
                BrnchItemReportsGrid.setColAlign("left,left,left,right")
                BrnchItemReportsGrid.setColTypes("ro,ro,ro,ro");
//              BrnchItemReportsGrid.setColSorting("int,str,str,str,str");  
                BrnchItemReportsGrid.enableEditEvents(true,true,true);
                BrnchItemReportsGrid.enableTooltips("false,false,false,false");
                BrnchItemReportsGrid.attachFooter("Total Amount,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>");                
               BrnchItemReportsGrid.attachEvent("onXLE",function(){
                   preTally.Settings.progressOff(true, dhxLayout, null);
               });
               BrnchItemReportsGrid.attachEvent("onXLS",function(){
                   preTally.Settings.progressOn(true, dhxLayout, null);
               });
               BrnchItemReportsGrid.init();
                        }
                ptPerformanceReportsTabbar.tabs("viewBrnchItemReports").setActive();                
                preTally.PerformanceReports.applyReportBrnchItemFilter(Item_id);
        },
        applyReportBrnchItemFilter:  function(Item_id){
            
            var filterValue ="&filter="+Item_id;            
            BrnchItemReportsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null); 
            rptFilterParams = '&f='+ptPerfRptToolbar.getValue("rpt_date_from")+'&t='+ptPerfRptToolbar.getValue("rpt_date_till");
            BrnchItemReportsGrid.clearAndLoad(preTally.initialize.encryptURL("requisites/reportBrnchItemData.php"+filterValue+rptFilterParams), function() {
            preTally.Settings.progressOff(true, dhxLayout, null);
            });
        }
        
    };
})(jQuery, this);