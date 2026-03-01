;(function($, window, undefined) {
    var filterObject;
    var monthData;
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var prevFirstDay;
    var prevLastDay;
    var selectedFirstDay ;
    var selectedLastDay ;
    
    preTally.MasterReportsLocationBased = {
        viewLocationBasedMasterReports: function() {

            if (!dhxMiddleBlockTabs.cells("viewLocationBasedMasterReports")) {
                reportMRInit=1;
                dhxLayout.cells("b").collapse();
                dhxMiddleBlockTabs.addTab("viewLocationBasedMasterReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports Location Based <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshMRLBTab'/>", 270);
                dhxMiddleBlockTabs.tabs("viewLocationBasedMasterReports").setActive();
                
                dhxLocationBasedMasterReportsLayout =  dhxMiddleBlockTabs.cells("viewLocationBasedMasterReports").attachLayout("1C");
                
                ptMRLocationBasedToolbar = dhxLocationBasedMasterReportsLayout.cells("a").attachToolbar();                
                ptMRLocationBasedToolbar.setIconsPath("images/icon/default_18/");
                
                ptMRLocationBasedToolbar.setAlign('right');
                
                $(".refreshMRLBTab").click(function(){

                    itemMRRptFlag   = 0;  
                    branchMRRptFlag = 0;
                    visualMRRptFlag = 0;
                    branchItemMRRptFlag = 0;
                    ItemBasedMRRptFlag  = 0;
                    
                    var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();
                        
                    if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                    if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                    if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                    if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                    if(actvId == 'viewLBMItemBasedReports') preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
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
                
                ptMRLocationBasedToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptMRLocationBasedToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptMRLocationBasedToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                ptMRLocationBasedToolbar.addSeparator();
                
                ptMRLocationBasedToolbar.addText("text_from", null, "From");
                ptMRLocationBasedToolbar.addInput("rpt_date_from", null, "", 75);
                ptMRLocationBasedToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                ptMRLocationBasedToolbar.addSeparator();
                
                ptMRLocationBasedToolbar.addText("text_till", null, "Till");
                ptMRLocationBasedToolbar.addInput("rpt_date_till", null, "", 75);
                ptMRLocationBasedToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                ptMRLocationBasedToolbar.addSeparator();
                
                ptMRLocationBasedToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                

                var ptRpTb_Inp_Frm = ptMRLocationBasedToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptMRLocationBasedToolbar.getValue("rpt_date_till")) preTally.MasterReportsLocationBased.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptMRLocationBasedToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptMRLocationBasedToolbar.getValue("rpt_date_from")) preTally.MasterReportsLocationBased.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                ptMRLocationBasedToolbar.attachEvent("onClick", function(id){  
                    
                    itemMRRptFlag   = 0;  
                    branchMRRptFlag = 0;
                    visualMRRptFlag = 0;
                    branchItemMRRptFlag = 0;
                    ItemBasedMRRptFlag = 0;
                    
                    var pId = ptMRLocationBasedToolbar.getParentId(id);
                   
                    if(id == 'rpt_df_clear'){
                        ptMRLocationBasedToolbar.setValue('rpt_date_from', '', false);
                        prevFirstDay = '';
                    }
                    
                    if(id == 'rpt_dt_clear'){
                        ptMRLocationBasedToolbar.setValue('rpt_date_till', '', false);
                        prevLastDay  = '';
                    }
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        ptMRLocationBasedToolbar.setValue('rpt_date_from', dateToday, false);
                        ptMRLocationBasedToolbar.setValue('rpt_date_till', dateToday, false);
                        ptMRLocationBasedToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptMRLocationBasedToolbar.setItemText('rpt_year_filter', 'Select Year');
                        monthData = '';
                        var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();
                        
                        prevFirstDay  = dateToday;
                        prevLastDay   = dateToday;
                    
//                        if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                        if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                        if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                        if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                        if(actvId == 'viewLBMItemBasedReports'){
                            selectedFirstDay = dateToday;
                            selectedLastDay  = dateToday;
                            preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                        }
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptMRLocationBasedToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptMRLocationBasedToolbar.setValue('rpt_date_from', firstDay, false);
                        ptMRLocationBasedToolbar.setValue('rpt_date_till', lastDay, false);
                        ptMRLocationBasedToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //ptMRLocationBasedToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
                        if(ptMRLocationBasedToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData = ptMRLocationBasedToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                        
//                        prevFirstDay = firstDay;
//                        prevLastDay  = lastDay;
                        
                        prevFirstDay =  firstDay;
                        prevLastDay  =  lastDay;
                        
                        var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();
                        
//                        if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                        if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                        if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                        if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                        if(actvId == 'viewLBMItemBasedReports'){
                            selectedFirstDay = firstDay;
                            selectedLastDay  = lastDay;
                            preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                        }
                        
                    }
                    if(pId == 'rpt_year_filter') { 
                        var tmpDate     = new Date();
                        if(parseInt(id)+1 == tmpDate.getFullYear())
                            var m = tmpDate.getMonth();
                        else 
                            var m = '02';
                        
                        yearData = ptMRLocationBasedToolbar.getListOptionSelected("rpt_year_filter");
                        currentYear=tmpDate.getFullYear();
                        if(yearData == currentYear){
                            var m = tmpDate.getMonth();
                            var firstDay    = new Date(parseInt(id)-1, 04, 1).toString("dd.MM.yyyy");
                            var lastDay     = new Date(parseInt(id), m+1, 0).toString("dd.MM.yyyy"); 
                        }
                        else{
                            var m = 2;
                            var firstDay    = new Date(id, 03, 1).toString("dd.MM.yyyy");
                            var lastDay     = new Date(parseInt(id)+1, m, 31).toString("dd.MM.yyyy");
                        }
                        
                        prevFirstDay =  firstDay;
                        prevLastDay  =  lastDay;
                        
                        ptMRLocationBasedToolbar.setValue('rpt_date_from', firstDay, false);
                        ptMRLocationBasedToolbar.setValue('rpt_date_till', lastDay, false);
                        ptMRLocationBasedToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptMRLocationBasedToolbar.setItemText('rpt_month_filter', 'Select Month');
                        monthData = '';
                        var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();
                        
//                        if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                        if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                        if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                        if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                        if(actvId == 'viewLBMItemBasedReports') {
                            selectedFirstDay = firstDay;
                            selectedLastDay  = lastDay;
                            preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                            
                        }
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        monthData = '';
                        
                        prevFirstDay = ptMRLocationBasedToolbar.getValue('rpt_date_from');
                        prevLastDay  = ptMRLocationBasedToolbar.getValue('rpt_date_till');
                        
                        ptMRLocationBasedToolbar.setItemText('rpt_day_filter', 'Select Day');
                        ptMRLocationBasedToolbar.setItemText('rpt_month_filter', 'Select Month');
                        ptMRLocationBasedToolbar.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();
//                        if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                        if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                        if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                        if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                        if(actvId == 'viewLBMItemBasedReports'){ 
                            
                            selectedFirstDay = ptMRLocationBasedToolbar.getValue('rpt_date_from');
                            selectedLastDay  = ptMRLocationBasedToolbar.getValue('rpt_date_till');
                            preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                        }
                        
//                        preTally.MasterReportsLocationBased.viewLBMItemReports(rptFilterID);
                    }  
                });
                ptMRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptMRpTb_Calendar.setDateFormat("%d.%m.%Y");
                           
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp==4){  
                    var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    monthData = date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    ptMRLocationBasedToolbar.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    ptMRLocationBasedToolbar.setValue('rpt_date_till', tDate);
                    ptMRLocationBasedToolbar.setItemText('rpt_month_filter',m_names[monthData]);
                    
                    reportMRInit=0;
                    monthData = monthData+1; // get current month number;starting with 1
                    
                    prevFirstDay = "01."+Date.today().toString("MM.yyyy");
                    prevLastDay  = tDate;
                    
                } else {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    ptMRLocationBasedToolbar.setValue('rpt_date_till', cDate);
                    ptMRLocationBasedToolbar.setValue('rpt_date_from', cDate);
                    ptMRLocationBasedToolbar.setItemText('rpt_day_filter', 'Today');
                    reportMRInit=0;
                    
                    prevFirstDay = prevLastDay = cDate;

                }
                
//                ptMRLocationBasedToolbar.setAlign('right');
       
                ptLocationBasedMasterReportsTabbar = dhxLocationBasedMasterReportsLayout.cells("a").attachTabbar();
//                ptLocationBasedMasterReportsTabbar.addTab("viewLBMReportsVisual", tb_data_txt, "465px");
//                ptLocationBasedMasterReportsTabbar.addTab("viewLBMReportsVisual", "Overall Reports");
                ptLocationBasedMasterReportsTabbar.addTab("viewLBMBranchReports", "Branch Based Reports");
                ptLocationBasedMasterReportsTabbar.addTab("viewLBMItemReports", "Item Based Reports");
                ptLocationBasedMasterReportsTabbar.addTab("viewLBMBrnchItemReports", "Branch Based Item Reports");

                preTally.MasterReportsLocationBased.viewLBMBranchReports();
                ptLocationBasedMasterReportsTabbar.tabs("viewLBMBranchReports").setActive();
                
                ptLocationBasedMasterReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    
                    if(id =='viewLBMBranchReports') {
                        dhxAccord.cells("a4").show();
                        dhxAccord.cells("a4").open();                                 
                    } else {
                        dhxAccord.cells("a4").hide();
                        dhxAccord.cells("a1").open();  
                    }
                    
//                    if(id == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                    if(id == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                    if(id == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                    
                    if(id == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
//                    if(id == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports();

                    if(id == 'viewLBMItemBasedReports') preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                    return true;
                   
                });
                
                  
            } else {
                dhxMiddleBlockTabs.tabs("viewLocationBasedMasterReports").setActive();
                
                var actvId = ptLocationBasedMasterReportsTabbar.getActiveTab();                
                if( actvId =='viewLBMBranchReports') {
                    dhxAccord.cells("a4").show();
                    dhxAccord.cells("a4").open();                                 
                } else {
                    dhxAccord.cells("a4").hide();
                    dhxAccord.cells("a1").open();  
                }

//                if(actvId == 'viewLBMReportsVisual') preTally.MasterReportsLocationBased.viewLBMReportsVisual();
                if(actvId == 'viewLBMItemReports')   preTally.MasterReportsLocationBased.viewLBMItemReports();
                if(actvId == 'viewLBMBranchReports') preTally.MasterReportsLocationBased.viewLBMBranchReports();
                if(actvId == 'viewLBMBrnchItemReports') preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rptLBMBrnchItemId);
                if(actvId == 'viewLBMItemBasedReports') preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                return true;
                
            }
        },/*
        viewLBMReportsVisual : function(){
            
            ptMRLocationBasedToolbar.setValue("rpt_date_from",prevFirstDay);
            ptMRLocationBasedToolbar.setValue("rpt_date_till",prevLastDay);
            
            if(visualMRRptFlag != 1){
                
                visualMRRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxReportsVisualLayout =  ptLocationBasedMasterReportsTabbar.cells("viewLBMReportsVisual").attachLayout("1C");
                dhxReportsVisualLayout.cells("a").hideHeader();

                dhxReportsVisualForm = dhxReportsVisualLayout.cells("a").attachForm();
                rptFilterParams = 'f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till");

                dhxReportsVisualForm.loadStruct(preTally.Initialize.encryptURL("requisites/viewLBMReportsVisual.php&"+rptFilterParams), function() {  
    //                $('input[name=INCOME]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});
    //                $('input[name=EXPENSE]').css({'font-weight':'bold','background-color':'#DDECFF','text-align':'center'});

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }
                
        },*/
        viewLBMBranchReports : function(){
            
            ptMRLocationBasedToolbar.setValue("rpt_date_from",prevFirstDay);
            ptMRLocationBasedToolbar.setValue("rpt_date_till",prevLastDay);
            
            if(branchMRRptFlag != 1){
//                var filterObject;
                branchMRRptFlag = 1;
                dhxBranchReportsLayout =  ptLocationBasedMasterReportsTabbar.cells("viewLBMBranchReports").attachLayout("1C");
                dhxBranchReportsLayout.cells("a").hideHeader();

                //BranchReportsGrid.destructor();                
                BranchReportsGrid = dhxBranchReportsLayout.cells("a").attachGrid();
                BranchReportsGrid.setImagePath("../../codebase/imgs/");
                BranchReportsGrid.setHeader("SlNo,Branch,Income,#cspan,Expense,#cspan,Internal Transfer Received,#cspan,Internal Transfer Paid,#cspan");
                BranchReportsGrid.attachHeader("#,#text_filter_inc,<div>Cash<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='2' class='LBMBrnchRptSort' /></div>,<div>Bank<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='LBMBrnchRptSort' /></div>,<div>Cash<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='LBMBrnchRptSort' /></div>,<div>Bank<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='LBMBrnchRptSort' /></div>,Cash,Bank,Cash,Bank");
                BranchReportsGrid.setInitWidths("40,*,*,*,*,*,0,0,0,0")
                BranchReportsGrid.setColAlign("center,left,right,right,right,right,right,right,right,right")
                BranchReportsGrid.setColTypes("ro,ro,price,price,price,price,price,price,price,price")
                BranchReportsGrid.init();
                BranchReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                BranchReportsGrid.enableEditEvents(false,false,false);
                BranchReportsGrid.enableColSpan(true);
                BranchReportsGrid.setSkin("dhx_skyblue");
                BranchReportsGrid.enableSmartRendering(true,50);
                BranchReportsGrid.attachFooter("Sum,#cspan,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>,<span style='float:right;'>{#stat_total}</span>");
                BranchReportsGrid.attachFooter("Total Amount,#cspan,<span style='float:right;'><div id='lmr_INC'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_EXP'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_TR'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_TP'>0</div></span>,#cspan");
//                BranchReportsGrid.attachFooter("<span style='float:right;'><div id='lmr_INCS'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_EXPS'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_TRS'>0</div></span>,#cspan,<span style='float:right;'><div id='lmr_TPS'>0</div></span>,#cspan");
                BranchReportsGrid.setNumberFormat('0000',2);
                BranchReportsGrid.setNumberFormat('0000',3);
                BranchReportsGrid.setNumberFormat('0000',4);
                BranchReportsGrid.setNumberFormat('0000',5);
                BranchReportsGrid.setNumberFormat('0000',6);
                BranchReportsGrid.setNumberFormat('0000',7);
                BranchReportsGrid.setNumberFormat('0000',8);
                BranchReportsGrid.setNumberFormat('0000',9);
//                BranchReportsGrid.setNumberFormat('0000',10);
//                BranchReportsGrid.setNumberFormat('0000',11);
//                BranchReportsGrid.setNumberFormat('0000',12);
//                BranchReportsGrid.setNumberFormat('0000',13);
//                BranchReportsGrid.setNumberFormat('0000',14);
//                BranchReportsGrid.setNumberFormat('0000',15);
//                BranchReportsGrid.setNumberFormat('0000',16);

//                BranchReportsGrid.setColumnHidden(13, true);
//                BranchReportsGrid.setColumnHidden(14, true);
//                BranchReportsGrid.setColumnHidden(15, true);
//                BranchReportsGrid.setColumnHidden(16, true);
                var srtFlg = 0;
                $('.LBMBrnchRptSort').click(function() {                          
                    var colId=$(this).attr("colNum")
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BranchReportsGrid.sortRows(colId,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BranchReportsGrid.sortRows(colId,"int","desc");
                        srtFlg = 0;
                    }
                });

                BranchReportsGrid.attachEvent("onXLE",function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                BranchReportsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                rptFilterParams = 'f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till");

                BranchReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportLBMBranchData.php&"+rptFilterParams),function(){
                    filterObject = BranchReportsGrid.getFilterElement(1);
                    filterObject.onkeyup = function(){
                        if(reportDetailsGrid) reportDetailsGrid.clearSelection();
                    };
                    filterObject.placeholder = "Branch";
                    preTally.MasterReportsLocationBased.calculateFooterValues();
                    var flag = 1;
//                    $('.btn_IT').click(function() {
//
//                        if(flag == 1){
//                          $(this).attr("src", 'images/icon/arrow_lr_16.gif');
//                            $(this).attr("title", 'Hide Internal Transfers');
//
//                            BranchReportsGrid.setColumnHidden(13, false);
//                            BranchReportsGrid.setColumnHidden(14, false);
//                            BranchReportsGrid.setColumnHidden(15, false);
//                            BranchReportsGrid.setColumnHidden(16, false);
//
//    //                        preTally.MasterReportsLocationBased.calculateFooterValues();
//
//                            flag = 0;
//
//                        }else{
//
//                            $(this).attr("src", 'images/icon/arrow_g_16.png');
//                            $(this).attr("title", 'View Internal Transfers');
//
//                            BranchReportsGrid.setColumnHidden(13, true);
//                            BranchReportsGrid.setColumnHidden(14, true);
//                            BranchReportsGrid.setColumnHidden(15, true);
//                            BranchReportsGrid.setColumnHidden(16, true);
//
//    //                        preTally.MasterReportsLocationBased.calculateFooterValues();
//
//                            flag = 1;
//                        }
//
//
//
//                    });

               });

                BranchReportsGrid.attachEvent("onFilterEnd", function(elements){
                     preTally.MasterReportsLocationBased.calculateFooterValues(); 
                     if(BranchReportsGrid.getRowsNum() == 0 ) {
                        BranchReportsGrid.addRow("row1",['','Record not Found','','','','','','','','','','','','','','',''],0); 
                        BranchReportsGrid.setColspan("row1",1,16);
                        BranchReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        
                    }else{
                       //preTally.MasterReportsLocationBased.calculateFooterValues();  
                    }
                   
                });            
    //        }else{
    //            ptLocationBasedMasterReportsTabbar.tabs("viewLBMBranchReports").setActive();     
            }
            
            if(reportDetailsGrid) reportDetailsGrid.destructor();

            dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp; Legend Details"); 
            reportDetailsGrid = dhxAccord.cells("a4").attachGrid();
            reportDetailsGrid.setHeader("<div style='text-align:left;'>Branch <input type = 'button' name='ALL' value = 'View All' class='btn_All'/></div>");
            reportDetailsGrid.setInitWidths("*");
            reportDetailsGrid.setColAlign("left");
            reportDetailsGrid.setColTypes("ro");
            reportDetailsGrid.enableTooltips("false");

            reportDetailsGrid.init();
            reportDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/rpt_listBranch.php"), function() {

                $('.btn_All').click(function() {
                    BranchReportsGrid.filterBy(1,'');
                    filterObject.value = "All";
                    if(reportDetailsGrid) reportDetailsGrid.clearSelection();

                    preTally.MasterReportsLocationBased.calculateFooterValues();

                });

               reportDetailsGrid.attachEvent("onRowSelect",function(id,data){
//                       console.log(reportDetailsGrid.cells(id,0).getValue()+"--"+this.value);
                    var LCfilterText = reportDetailsGrid.cells(id,0).getValue();
                    if (LCfilterText == 'ALL') LCfilterText = '';
                    BranchReportsGrid.filterBy(1,LCfilterText);
                    filterObject.value = LCfilterText;

                    preTally.MasterReportsLocationBased.calculateFooterValues();
               });                    
            });
        },
        viewLBMItemReports : function(){   
            
            ptMRLocationBasedToolbar.setValue("rpt_date_from",prevFirstDay);
            ptMRLocationBasedToolbar.setValue("rpt_date_till",prevLastDay);
            
            if(itemMRRptFlag != 1){ 
                dhxItemReportsLayout =  ptLocationBasedMasterReportsTabbar.cells("viewLBMItemReports").attachLayout("1C");
                dhxItemReportsLayout.cells("a").hideHeader();
                itemMRRptFlag = 1;
                
                ItemReportsGrid = dhxItemReportsLayout.cells("a").attachGrid();
                ItemReportsGrid.setImagePath("../../codebase/imgs/");
                ItemReportsGrid.setSkin("dhx_skyblue")
                ItemReportsGrid.setHeader("SlNo,<div id='itmRF' style='width: 90%;' placeholder='Item'></div>,\Sub Head,Amount");                    
                ItemReportsGrid.setHeader("<div>SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort' /></div>,#select_filter,#combo_filter,#combo_filter,#combo_filter,<div><input style='width:70%' id='amtItRpt' placeholder='Amount'> <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='5' class='btn_Sort'/></div>,");
                ItemReportsGrid.setNumberFormat("0,000.00",4);
                ItemReportsGrid.setInitWidths("30,170,*,*,*,150,150");
                ItemReportsGrid.setColAlign("right,left,left,left,left,right,center");
                ItemReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                ItemReportsGrid.enableColSpan(true);
//                ItemReportsGrid.setColSorting("int,str,str,str,str");  
                ItemReportsGrid.enableEditEvents(true,true,true);
                ItemReportsGrid.enableTooltips("false,false,false,false");
                ItemReportsGrid.attachFooter("Total,#cspan,#cspan,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>,<span ></span>");
                //ItemReportsGrid.attachFooter("<span style='float:left;'>Expense : <div style='float:right;' id='itmRptExpense'> 0 </div></span>,#cspan,<span style='float:left;'> Income : <div style='float:right;' id='itmRptIncome'> 0 </div></span>,<span style='float:left;'> Business Returned : <div style='float:right;' id='itmRptBusinessReturn'> 0 </div></span>,<span style='float:left;'>Business Received : <div style='float:right;' id='itmRptBusinessReceived'> 0 </div></span>,<span style='float:left;'> Internal Transfer Paid : <div style='float:right;' id='itmRptInternalTransferPaid'> 0 </div></span>,<span style='float:left;'> Internal Transfer Received : <div style='float:right;' id='itmRptInternalTransferReceived'> 0 </div></span>,#cspan");
                ItemReportsGrid.attachFooter("<span style='float:left;'> Internal Transfer Received : <div style='float:right;' id='itmRptInternalTransferReceived'> 0 </div></span>,#cspan,<span style='float:left;'> Internal Transfer Paid : <div style='float:right;' id='itmRptInternalTransferPaid'> 0 </div></span>,<span style='float:left;'>Business Received : <div style='float:right;' id='itmRptBusinessReceived'> 0 </div></span>,<span style='float:left;'> Business Returned : <div style='float:right;' id='itmRptBusinessReturn'> 0 </div></span>,<span style='float:left;'> Income : <div style='float:right;' id='itmRptIncome'> 0 </div></span>,<span style='float:left;'>Expense : <div style='float:right;' id='itmRptExpense'> 0 </div></span>");
                ItemReportsGrid.init();
//                if(ItemReportsGrid.getRowsNum() == 0)
                    ItemReportsGrid.makeFilter("amtItRpt",5); 
                var itCombo = ItemReportsGrid.getFilterElement(2);
                itCombo.setPlaceholder("Items");
                //itCombo.readonly(true);
                
                var shCombo = ItemReportsGrid.getFilterElement(3);
                shCombo.setPlaceholder("Subhead");
                //shCombo.readonly(true);
                
                var mhCombo = ItemReportsGrid.getFilterElement(4);
                mhCombo.setPlaceholder("Mainhead");
                //mhCombo.readonly(true);
                
                ItemReportsGrid.attachEvent("onRowSelect",function(rowId){
                    branchItemMRRptFlag = 0;
                    rptLBMBrnchItemId=rowId;
                    if(rowId != 0){
                        preTally.MasterReportsLocationBased.viewLBMBrnchItemReports(rowId,ItemReportsGrid.getUserData(rowId,"IT_Name"));
                    }
                });
                ItemReportsGrid.attachEvent("onXLE",function(){
                   preTally.Settings.progressOff(true, dhxLayout, null);
                });
                ItemReportsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                
                ItemReportsGrid.attachEvent("onFilterEnd", function(elements){ 
                    if(ItemReportsGrid.getRowId(0) == "0" ) { //ItemReportsGrid.getRowsNum() == 0
                        
                        ItemReportsGrid.deleteRow("0");
                        ItemReportsGrid.addRow("0",['','Record Not Found','','','','','','']); 
                        ItemReportsGrid.setColspan("0",1,6);
                        ItemReportsGrid.setRowTextStyle("0", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        
                    } else{
                        var rowID = 0;
                        var i;
                        for (i = 0; i < ItemReportsGrid.getRowsNum(); i++){
                            rowID = ItemReportsGrid.getRowId(i);   
                            ItemReportsGrid.cells(rowID,0).setValue(i+1);
                        };  
                        
                    }
                   
                });    
                setTimeout(function(){
                    preTally.MasterReportsLocationBased.applyReportItemFilter(); 
                },500);
                
            }
        },
        applyReportItemFilter:  function(){
            ItemReportsGrid.clearAll();
            
            rptFilterParams = '&f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till");
            ItemReportsGrid.enableRowsHover(true,"bonusReportHover");
            ItemReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportLBMItemData.php"+rptFilterParams), function() {
                ItemReportsGrid.enableTooltips("false,false,false,false,false,false,true");
//                preTally.Settings.progressOff(true, dhxLayout, null); 
                $("#itmRptExpense").html(ItemReportsGrid.getUserData("","Expense"));
                $("#itmRptIncome").html(ItemReportsGrid.getUserData("","Income"));
                $("#itmRptBusinessReturn").html(ItemReportsGrid.getUserData("","BusinessReturned"));
                $("#itmRptBusinessReceived").html(ItemReportsGrid.getUserData("","BusinessReceived"));
                $("#itmRptInternalTransferPaid").html(ItemReportsGrid.getUserData("","InternalTransferPaid"));
                $("#itmRptInternalTransferReceived").html(ItemReportsGrid.getUserData("","InternalTransferReceived"));
                var srtFlg = 0;
                $('.btn_Sort').click(function() {                          
                    var colId=$(this).attr("colNum")
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        ItemReportsGrid.sortRows(colId,"int","asc");
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        ItemReportsGrid.sortRows(colId,"int","desc");
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
//            var mrOB = document.getElementById("lmr_OB");
//            mrOB.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(2)+preTally.MasterReportsLocationBased.sumColumn(3)).toFixed(0);

            var mrInc = document.getElementById("lmr_INC");
            mrInc.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(2)+preTally.MasterReportsLocationBased.sumColumn(3)).toFixed(0);

            var mrExp = document.getElementById("lmr_EXP");
            mrExp.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(4)+preTally.MasterReportsLocationBased.sumColumn(5)).toFixed(0);
            
//            var mrCB = document.getElementById("lmr_CB");
//            mrCB.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(9)+preTally.MasterReportsLocationBased.sumColumn(10)).toFixed(0);
//
//            var mrTR = document.getElementById("lmr_TR");
//            mrTR.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(13)+preTally.MasterReportsLocationBased.sumColumn(14)).toFixed(0);
//            
//            var mrTP = document.getElementById("lmr_TP");
//            mrTP.innerHTML = (preTally.MasterReportsLocationBased.sumColumn(15)+preTally.MasterReportsLocationBased.sumColumn(16)).toFixed(0);
//            
//            var mrOBS = document.getElementById("lmr_OBS");
//            mrOBS.innerHTML = "Opening Balance :  " + ( parseFloat( $("#mr_OB").text() )+preTally.MasterReportsLocationBased.sumColumn(4) ).toFixed(0);
//           
//            var mrCBS = document.getElementById("lmr_CBS");
//            mrCBS.innerHTML = "Closing Balance :  " + ( parseFloat( $("#lmr_CB").text() )+preTally.MasterReportsLocationBased.sumColumn(11) ).toFixed(0);
//            
//            var mrIncS = document.getElementById("lmr_INCS");
//            mrIncS.innerHTML = $("#lmr_INC").text();
//
//            var mrExpS = document.getElementById("lmr_EXPS");
//            mrExpS.innerHTML = $("#lmr_EXP").text();
//
//            var mrTRS = document.getElementById("lmr_TRS");
//            mrTRS.innerHTML = $("#lmr_TR").text();
//            
//            var mrTPS = document.getElementById("lmr_TPS");
//            mrTPS.innerHTML = $("#lmr_TP").text();

        },
        sumColumn : function(ind){
		var out = 0;
		for(var i=0;i< BranchReportsGrid.getRowsNum();i++){
			out+= parseFloat(BranchReportsGrid.cells2(i,ind).getValue());
		}
		return out;
	},
        viewLBMBrnchItemReports : function(Item_id){              
            
            ptMRLocationBasedToolbar.setValue("rpt_date_from",prevFirstDay);
            ptMRLocationBasedToolbar.setValue("rpt_date_till",prevLastDay);
                    
                    
            if(branchItemMRRptFlag != 1){
                branchItemMRRptFlag = 1;
                if(ptMRLocationBasedToolbar.getListOptionSelected("rpt_month_filter") && ptMRLocationBasedToolbar.getItemText('rpt_month_filter') != 'Select Month') {
                    monthData = ptMRLocationBasedToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                }
                var combofiltrInterval;
                ptLocationBasedMasterReportsTabbar.tabs("viewLBMBrnchItemReports").setActive();
                dhxBrnchItemReportsLayout =  ptLocationBasedMasterReportsTabbar.cells("viewLBMBrnchItemReports").attachLayout("1C");
                dhxBrnchItemReportsLayout.cells("a").hideHeader();                                   
                dhxBrnchItemReportsLayout.cells("a").setWidth('200');
                BrnchItemReportsGrid = dhxBrnchItemReportsLayout.cells("a").attachGrid();
                BrnchItemReportsGrid.setImagePath("../../codebase/imgs/");
                BrnchItemReportsGrid.setSkin("dhx_skyblue");
                
                dhxBrnchRptTlbr = dhxBrnchItemReportsLayout.cells("a").attachToolbar();
                dhxBrnchRptTlbr.addText('LBmasterRptToolbar', '0', 'Type Of Entry' );
                dhxBrnchRptTlbr.addText('LBmasterRptToolbar', '1', '<div style="font-weight:bold;width:250px;" id="TOE"></div>' );
                dhxBrnchRptTlbr.addText('LBmasterRptToolbar', '2', 'Item' );
                dhxBrnchRptTlbr.addText('LBmasterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="ITNm"></div>' );
                dhxBrnchRptTlbr.setIconSize(32);
                
                TOECombo = new dhtmlXCombo("TOE");
                TOECombo.addOption([
                        ["All","All"],
                        ["InternalTransferReceived","Internal Transfer Received"],
                        ["InternalTransferPaid","Internal Transfer Paid"],
                        ["BusinessReceived","Business Received"],
                        ["BusinessReturned","Business Returned"],
                        ["Income","Income"],
                        ["Expense","Expense"]
                ]);
                
                TOECombo.readonly(true);
                TOECombo.setComboValue("All");
                TOECombo.setComboText("All");
                ITNCombo = new dhtmlXCombo("ITNm");
                $.post(preTally.Initialize.encryptURL("requisites/items.php&mask=master&itemId="+Item_id), function( data ){
                    ITNCombo.load(data);
                    if (typeof Item_id == 'undefined' || Item_id == 'null') {
                        ITNCombo.setComboValue(ITNCombo.getSelectedValue());
                        preTally.MasterReportsLocationBased.applyReportBrnchItemFilter(ITNCombo.getSelectedValue());
                    } else {
                        ITNCombo.setComboValue(Item_id);
                        
                    }rptLBMBrnchItemId = ITNCombo.getSelectedValue();
                    //var ITId = !isNaN(Item_id) ? Item_id : ITNCombo.getSelectedValue();
                    //preTally.MasterReportsLocationBased.applyReportBrnchItemFilter(ITId);
                });
                TOECombo.attachEvent("onChange", function(value, text){
                    TOEComboValue=TOECombo.getSelectedValue();
                    $.post(preTally.Initialize.encryptURL("requisites/items.php&mask=master&typeOfEntry="+TOEComboValue), function( data ){
                        ITNCombo.load(data);
                        preTally.MasterReportsLocationBased.applyReportBrnchItemFilter(ITNCombo.getSelectedValue());
                    });
    
                });
                
                ITNCombo.setOptionWidth(280);
                
                ITNCombo.attachEvent("onKeyPressed", function(keyCode){
                    if(combofiltrInterval) clearInterval(combofiltrInterval);
                    combofiltrInterval = setInterval( function() { 
                        ITNCombo.load(preTally.Initialize.encryptURL("requisites/items.php&mask=master&IT_Name="+ITNCombo.getComboText()+"&typeOfEntry="+TOECombo.getSelectedValue()), function() {
                            ITNCombo.openSelect();
                            $(".dhxcombolist_dhx_skyblue").height(176);
                            if(ITNCombo.getOptionsCount() == 0) {
                                ITNCombo.closeAll();
                            } 
                        });
                        clearInterval(combofiltrInterval); 
                    }, 500);
                });
                
                ITNCombo.attachEvent("onChange", function() {
                    rptLBMBrnchItemId = ITNCombo.getSelectedValue();
                    preTally.MasterReportsLocationBased.applyReportBrnchItemFilter(ITNCombo.getSelectedValue());
                });
                    
                BrnchItemReportsGrid.enableColSpan(true);
                
                
                BrnchItemReportsGrid.attachEvent("onXLE",function(){
//                    var brnchFilter = BrnchItemReportsGrid.getFilterElement(1);
//                    brnchFilter.setPlaceholder("Branch");
//                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                BrnchItemReportsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                BrnchItemReportsGrid.init();
                
                BrnchItemReportsGrid.attachEvent("onRowSelect",function(rowId){
                    var cnId = BrnchItemReportsGrid.getSelectedCellIndex();
                    var selectedMonth = BrnchItemReportsGrid.cells(rowId,cnId).getAttribute("month");
                    var selectedYear  = BrnchItemReportsGrid.cells(rowId,cnId).getAttribute("year");
//                    alert(selectedMonth+"------------------------------"+rowId);
//                    if(rowId != 0 && selectedMonth == undefined) {
//                        ItemBasedMRRptFlag = 0 ;
//                        rptLBMItem_BranchId = rowId;
//                        rptLBMItemId  = BrnchItemReportsGrid.getUserData(rowId,"IT_Id");
//                        preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
//                    }else 
                        
                    if(selectedMonth){ 
                        ItemBasedMRRptFlag = 0 ;
                        rptLBMItem_BranchId = rowId;
                        rptLBMItemId  = BrnchItemReportsGrid.getUserData(rowId,"IT_Id");
                        
                        prevFirstDay = ptMRLocationBasedToolbar.getValue("rpt_date_from");
                        prevLastDay  = ptMRLocationBasedToolbar.getValue("rpt_date_till");
                    
                        preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId, selectedMonth,selectedYear);
                    } 
                        
                        return false;
                });
                
                BrnchItemReportsGrid.attachEvent("onFilterEnd", function() {
                    if(BrnchItemReportsGrid.getRowId(0)!="0"){
                        var rowID=0;
                        var i;
                        for (i=0; i<BrnchItemReportsGrid.getRowsNum(); i++){
                            rowID=BrnchItemReportsGrid.getRowId(i);   
                            BrnchItemReportsGrid.cells(rowID,0).setValue(i+1);
                        };                                       
                    }
                });

/////////////////////////////////////////////////////original code ; onXLE of combo
//                        ITNCombo.setComboValue(Item_id);
//                        rptLBMBrnchItemId = ITNCombo.getSelectedValue();
//                        var ITId = !isNaN(Item_id) ? Item_id : ITNCombo.getSelectedValue();
//                        preTally.MasterReportsLocationBased.applyReportBrnchItemFilter(ITId);
///////////////////////////////////////////////////////////////////


            }
        },
        applyReportBrnchItemFilter:  function(Item_id,TypeOfItem){
            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = "&filter="+Item_id;            
            BrnchItemReportsGrid.clearAll();
            BrnchItemReportsGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false");
            BrnchItemReportsGrid.enableRowsHover(true,"bonusReportHover");
            rptFilterParams = '&f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till")+'&m='+monthData;
            BrnchItemReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportLBMBrnchItemData.php"+filterValue+rptFilterParams), function() {                
                preTally.Settings.progressOff(true, dhxLayout, null);
//                brnchcombo = BrnchItemReportsGrid.getFilterElement(1);
//                brnchcombo.setOptionWidth(150);
                /*
                BrnchItemReportsGrid.attachEvent("onRowSelect",function(rowId){
                    if(rowId != 0) {
                        ItemBasedMRRptFlag = 0 ;
                        rptLBMItem_BranchId = rowId;
                        rptLBMItemId  = BrnchItemReportsGrid.getUserData(rowId,"IT_Id");
                        preTally.MasterReportsLocationBased.viewLBMItemBasedReports(rptLBMItemId, rptLBMItem_BranchId);
                    }   else return false;
                });
                */
                BrnchItemReportsGrid.attachEvent("onMouseOver", function(id,ind){
//                    
                });
                
                BrnchItemReportsGrid.makeFilter("LC_LBMR_Name",1);
                LC_LBMR_NameCombo = dhtmlXComboFromSelect("LC_LBMR_Name");
                LC_LBMR_NameCombo.setOptionWidth(200);
                LC_LBMR_NameCombo.setSize(100);
                
//                
//                LC_LBMR_NameCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=all"), function(){
                    LC_LBMR_NameCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
//                });
//                
                LC_LBMR_NameCombo.attachEvent("onChange", function() {
                    var locCMBVal = LC_LBMR_NameCombo.getComboText();
                    if(locCMBVal == "All") locCMBVal = ''; 
                    BrnchItemReportsGrid.filterBy(1, locCMBVal);
                });

                var srtFlg = 0;
                $('.btn_Sort_LBM').click(function() { 
                    var colId=$(this).attr("colNum");
                    var sortType = colId != 1 ? 'int' : 'str' ;
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        BrnchItemReportsGrid.sortRows(colId,sortType,"asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        BrnchItemReportsGrid.sortRows(colId,sortType,"desc");
                        srtFlg = 0;
                    }
                });
                
            });
        },
        viewLBMItemBasedReports : function(ITId,LCId,Mnt,Yr){   
            
            if(!Mnt && !Yr && selectedFirstDay && selectedLastDay){
                ptMRLocationBasedToolbar.setValue("rpt_date_from",selectedFirstDay);
                ptMRLocationBasedToolbar.setValue("rpt_date_till",selectedLastDay); 
                
                
            }
                    
            if(ItemBasedMRRptFlag != 1){
                ItemBasedMRRptFlag = 1;
                
                if(Mnt && Yr){
                    var date = new Date();
                    selectedFirstDay = new Date(parseInt(Yr), parseInt(Mnt)-1, 1).toString("dd.MM.yyyy");
                    selectedLastDay  = new Date(parseInt(Yr), parseInt(Mnt), 0).toString("dd.MM.yyyy");                    
                    ptMRLocationBasedToolbar.setValue("rpt_date_from",selectedFirstDay);
                    ptMRLocationBasedToolbar.setValue("rpt_date_till",selectedLastDay);    
                    
                    ptMRLocationBasedToolbar.setItemText('rpt_day_filter', 'Select Day');
                    ptMRLocationBasedToolbar.setItemText('rpt_month_filter', 'Select Month');
                    ptMRLocationBasedToolbar.setItemText('rpt_year_filter', 'Select Year');
                }
                
                if(!ptLocationBasedMasterReportsTabbar.cells("viewLBMItemBasedReports")){
                    ptLocationBasedMasterReportsTabbar.addTab("viewLBMItemBasedReports", "Item - Detailed Reports", null, null, null, true);
                    ptLocationBasedMasterReportsTabbar.tabs("viewLBMItemBasedReports").setActive();    

                    dhxItemBasedReportsLayout =  ptLocationBasedMasterReportsTabbar.cells("viewLBMItemBasedReports").attachLayout("2U");
                    dhxItemBasedReportsLayout.cells("a").hideHeader();                                   
                    dhxItemBasedReportsLayout.cells("b").hideHeader();  
                    dhxItemBasedReportsLayout.cells("a").setWidth('200');

                    ItemBasedReportsGrid = dhxItemBasedReportsLayout.cells("b").attachGrid();
                    ItemBasedReportsGrid.setImagePath("../../codebase/imgs/");
                    ItemBasedReportsGrid.setSkin("dhx_skyblue");
                    ItemBasedReportsGrid.setHeader("<div style='text-align:left;'>SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort_LBIR'/></div>,Item,#text_filter_inc,#text_filter_inc,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_LBIR'/></div>,Date,#combo_filter");                    
                    ItemBasedReportsGrid.setNumberFormat("0,000.00",4);
                    ItemBasedReportsGrid.setInitWidths("40,*,*,*,*,80,120")
                    ItemBasedReportsGrid.setColAlign("left,left,left,left,right,left,left")
                    ItemBasedReportsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
//                      ItemBasedReportsGrid.setColSorting("int,str,str,str,str");                         
                    ItemBasedReportsGrid.enableEditEvents(true,true,true);
                    ItemBasedReportsGrid.enableTooltips("false,false,false,false,false,false");                       
//                    ItemBasedReportsGrid.attachFooter("Total Amount,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>");      
                    ItemBasedReportsGrid.attachEvent("onXLE",function(){
//                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    ItemBasedReportsGrid.attachEvent("onXLS",function(){
//                        preTally.Settings.progressOn(true, dhxLayout, null);
                    });
                    ItemBasedReportsGrid.init();

                    var descTxt = ItemBasedReportsGrid.getFilterElement(2);
                    descTxt.placeholder = " Description";
//
                    var trkTxt = ItemBasedReportsGrid.getFilterElement(3);
                    trkTxt.placeholder = " Track";

                    var addByCombo = ItemBasedReportsGrid.getFilterElement(6);
                    addByCombo.setPlaceholder("Added By");


                    ItemBasedReportsGrid.attachEvent("onFilterEnd", function() {                            
                        if(ItemBasedReportsGrid.getRowId(0)!="0"){
                            var rowID = 0;
                            var i;
                            for (i = 0; i < ItemBasedReportsGrid.getRowsNum(); i++){
                            rowID = ItemBasedReportsGrid.getRowId(i);   
                            ItemBasedReportsGrid.cells(rowID,0).setValue(i+1);

                            };  
                        }
                    });



                        rptFilterParams = '&f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till")+'&ITId='+ITId+'&LCId='+LCId;
                    ItemBasedReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportLBMItemRptData.php"+rptFilterParams), function() {
//                        preTally.Settings.progressOff(true, dhxLayout, null); 

                        var srtFlg = 0;
                        $('.btn_Sort_LBIR').click(function() {                                
                            var colId=$(this).attr("colNum");
                            if(srtFlg == 0){
                                $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                                ItemBasedReportsGrid.sortRows(colId,"int","asc");
                                srtFlg = 1;
                            }else{
                                $(this).attr("src", 'images/icon/sort-descending-icon.png');
                                ItemBasedReportsGrid.sortRows(colId,"int","desc");
                                srtFlg = 0;
                            }
                        });
                    });
                     
                }else{
                    
    //                preTally.Settings.progressOn(true, dhxLayout, null); 
                        rptFilterParams = '&f='+ptMRLocationBasedToolbar.getValue("rpt_date_from")+'&t='+ptMRLocationBasedToolbar.getValue("rpt_date_till")+'&ITId='+ITId+'&LCId='+LCId;
                    ItemBasedReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportLBMItemRptData.php"+rptFilterParams), function() {
    //                    preTally.Settings.progressOff(true, dhxLayout, null);
                    if(ItemBasedReportsGrid.getRowId(0)== 0) {
                        ItemBasedReportsGrid.attachHeader("<div style='text-align:left;'> # <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_Sort_LBIR'/></div>,Item,#text_filter_inc,#text_filter_inc,<div style='text-align:left;'>Amount <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_LBIR'/></div>,Date,#combo_filter");
                        ItemBasedReportsGrid.detachFooter(0);
                        
                    }
                    ItemBasedReportsGrid.detachHeader(1);
                    ItemBasedReportsGrid.detachFooter(1);                    
                        var srtFlg = 0;
                        $('.btn_Sort_LBIR').click(function() {
                            var colId=$(this).attr("colNum");
                            if(srtFlg == 0){                                
                                $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                                ItemBasedReportsGrid.sortRows(colId,"int","asc");
                                srtFlg = 1;
                            }else{
                                $(this).attr("src", 'images/icon/sort-descending-icon.png');
                                ItemBasedReportsGrid.sortRows(colId,"int","desc");
                                srtFlg = 0;
                            }
                        });
                    });

                    ptLocationBasedMasterReportsTabbar.tabs("viewLBMItemBasedReports").setActive();     
                }
            }
        }
    };
})(jQuery, this);