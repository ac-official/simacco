;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthData,yearData,branchFilterCombo;
    preTally.IncomeProfitReport = {
        viewIncomeProfitReports : function(){
            if (!dhxMiddleBlockTabs.cells("income_monthlybusinessreports")) {
                dhxMiddleBlockTabs.addTab("income_monthlybusinessreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Income and Profit&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='incRefreshTab'/>", 305);
                dhxMiddleBlockTabs.tabs("income_monthlybusinessreports").setActive();
                
                dhxIncomeReportsLayout =  dhxMiddleBlockTabs.cells("income_monthlybusinessreports").attachLayout("1C");
                IncomeReportToolbar = dhxIncomeReportsLayout.cells("a").attachToolbar();                
                IncomeReportToolbar.setIconsPath("images/icon/default_18/");
                IncomeReportToolbar.setAlign('right');
                
                $(".incRefreshTab").click(function(){
                    preTally.IncomeProfitReport.clearAllFlags();
                    var actvId = IncomeReportsTabbar.getActiveTab();
                    if(actvId == 'viewIncomeBusinessRpt') preTally.IncomeProfitReport.viewMonthwiseBusinessReport();
                    if(actvId == 'viewIncomeTaxRpt') preTally.IncomeProfitReport.viewMonthwiseTaxReport();
                    if(actvId == 'viewIncomeStatutoryRpt') preTally.IncomeProfitReport.viewMonthwiseStatutoryReport();
                    if(actvId == 'viewIncomeGrossRpt') preTally.IncomeProfitReport.viewMonthwiseGrossReport();
                    if(actvId == 'viewIncomeFixedExpRpt') preTally.IncomeProfitReport.viewMonthwiseFixedExpReport();
                    if(actvId == 'viewIncomeVarExpRpt') preTally.IncomeProfitReport.viewMonthwiseVarExpReport();
                    if(actvId == 'viewIncomeGeneralExpRpt') preTally.IncomeProfitReport.viewMonthwiseGeneralReport();
                    if(actvId == 'viewIncomeNetProfitRpt') preTally.IncomeProfitReport.viewMonthwiseNetProfitReport();                    
                    if(actvId == 'viewIncomeOverviewRpt') preTally.IncomeProfitReport.viewMonthwiseOverviewReport();
                    if(actvId == 'viewIncomeFixedExpSettings') preTally.IncomeProfitReport.viewFixedExpenseSettings();
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "income_monthlybusinessreports")
                        preTally.IncomeProfitReport.clearAllFlags();
                    return true;
                });

                var Months_Options  = [];
                var Years_Options   = [];
                var currentMonth    = Date.today().getMonth()+1;
        
                if(currentMonth == 1)  // less than April
                    curMonth = 13;
                else if(currentMonth == 2)
                    curMonth = 14;
                else if(currentMonth == 3)
                    curMonth = 15;
                else
                    curMonth = currentMonth;

                for (i = 4; i <= curMonth ; i++) {
                    j = m = i;    
                    if(j == 13) {
                        j = '01';
                        m = 1;   // as index for month array
                    } else if(j == 14) {
                        j = '02';
                        m = 2;
                    } else if(j == 15) {
                        j = '03';
                        m = 3;  
                    }

                    if(j < 10){
                        j = '0'+i;
                    } 
                    
                    Months_Options.push(['m'+m,'obj',monthNames[m-1],"calendar_M.png"]);
                }
                
                for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                    if(i == Date.today().getFullYear()) {
                        if( Date.today().getMonth()+1 > 3)
                            Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                    } else
                        Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                }
                
                IncomeReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                IncomeReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                IncomeReportToolbar.attachEvent("onClick", function(id){  
                    preTally.IncomeProfitReport.clearAllFlags();
                    var pId = IncomeReportToolbar.getParentId(id);
                    var actvId = IncomeReportsTabbar.getActiveTab();
                    if(pId == 'rpt_month_filter') {
                        yearData = '';
                        if(IncomeReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = IncomeReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        IncomeReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData = '';
                        if(IncomeReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData = IncomeReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        IncomeReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(actvId == 'viewIncomeBusinessRpt') preTally.IncomeProfitReport.viewMonthwiseBusinessReport();
                    if(actvId == 'viewIncomeTaxRpt') preTally.IncomeProfitReport.viewMonthwiseTaxReport();
                    if(actvId == 'viewIncomeStatutoryRpt') preTally.IncomeProfitReport.viewMonthwiseStatutoryReport();
                    if(actvId == 'viewIncomeGrossRpt') preTally.IncomeProfitReport.viewMonthwiseGrossReport();
                    if(actvId == 'viewIncomeFixedExpRpt') preTally.IncomeProfitReport.viewMonthwiseFixedExpReport();
                    if(actvId == 'viewIncomeVarExpRpt') preTally.IncomeProfitReport.viewMonthwiseVarExpReport();
                    if(actvId == 'viewIncomeGeneralExpRpt') preTally.IncomeProfitReport.viewMonthwiseGeneralReport();
                    if(actvId == 'viewIncomeNetProfitRpt') preTally.IncomeProfitReport.viewMonthwiseNetProfitReport();                    
                    if(actvId == 'viewIncomeOverviewRpt') preTally.IncomeProfitReport.viewMonthwiseOverviewReport();
                    if(actvId == 'viewIncomeFixedExpSettings') preTally.IncomeProfitReport.viewFixedExpenseSettings();
                });
 
                var date    = new Date();
                var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                monthData   = date.getMonth();
                IncomeReportToolbar.setItemText('rpt_month_filter',m_names[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                
                IncomeReportsTabbar = dhxIncomeReportsLayout.cells("a").attachTabbar();
                IncomeReportsTabbar.addTab("viewIncomeBusinessRpt", "Income Report");
                IncomeReportsTabbar.addTab("viewIncomeTaxRpt", "Tax Expense");
                IncomeReportsTabbar.addTab("viewIncomeStatutoryRpt", "Statutory Expense");
                IncomeReportsTabbar.addTab("viewIncomeGrossRpt", "Gross Profit");
                IncomeReportsTabbar.addTab("viewIncomeFixedExpRpt", "Fixed Expense");
                IncomeReportsTabbar.addTab("viewIncomeVarExpRpt", "Flexi Expense");
                IncomeReportsTabbar.addTab("viewIncomeGeneralExpRpt", "General Expense");
                IncomeReportsTabbar.addTab("viewIncomeNetProfitRpt", "Net Profit");
                IncomeReportsTabbar.addTab("viewIncomeOverviewRpt", "Income and Profit Overview");
                IncomeReportsTabbar.addTab("viewIncomeFixedExpSettings", "Expense Settings");
                
                businessIncRptFlag = 0;
                preTally.IncomeProfitReport.viewMonthwiseBusinessReport();
                
                IncomeReportsTabbar.tabs("viewIncomeBusinessRpt").setActive();
                IncomeReportsTabbar.attachEvent("onSelect", function(id, last_id){ 

                    if(id == 'viewIncomeBusinessRpt') preTally.IncomeProfitReport.viewMonthwiseBusinessReport();
                    if(id == 'viewIncomeTaxRpt') preTally.IncomeProfitReport.viewMonthwiseTaxReport();
                    if(id == 'viewIncomeStatutoryRpt') preTally.IncomeProfitReport.viewMonthwiseStatutoryReport();
                    if(id == 'viewIncomeGrossRpt') preTally.IncomeProfitReport.viewMonthwiseGrossReport();
                    if(id == 'viewIncomeFixedExpRpt') preTally.IncomeProfitReport.viewMonthwiseFixedExpReport();
                    if(id == 'viewIncomeVarExpRpt') preTally.IncomeProfitReport.viewMonthwiseVarExpReport();
                    if(id == 'viewIncomeGeneralExpRpt') preTally.IncomeProfitReport.viewMonthwiseGeneralReport();
                    if(id == 'viewIncomeNetProfitRpt') preTally.IncomeProfitReport.viewMonthwiseNetProfitReport();                    
                    if(id == 'viewIncomeOverviewRpt') preTally.IncomeProfitReport.viewMonthwiseOverviewReport();
                    if(id == 'viewIncomeFixedExpSettings') preTally.IncomeProfitReport.viewFixedExpenseSettings();
                    return true;
                });
                
            } else {
                dhxMiddleBlockTabs.tabs("income_monthlybusinessreports").setActive();
                var actvId = IncomeReportsTabbar.getActiveTab(); 
                if(actvId == 'viewIncomeBusinessRpt') preTally.IncomeProfitReport.viewMonthwiseBusinessReport();
            }
        },
        viewMonthwiseBusinessReport : function() {
            if(businessIncRptFlag != 1){
                businessIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/getBranches.php&rpt=bs")
                }).done(function(data) {
                    branchFilterCombo = data;
                });
                
                IncomeReportsTabbar.tabs("viewIncomeBusinessRpt").setActive();
                incBusinessReportLayout =  IncomeReportsTabbar.cells("viewIncomeBusinessRpt").attachLayout("1C");
                incBusinessReportLayout.cells("a").hideHeader();
                                                 
                incBusinessReportLayout.cells("a").setWidth('200');
                IncReportsGrid = incBusinessReportLayout.cells("a").attachGrid();
                IncReportsGrid.setImagePath("../../codebase/imgs/");
                IncReportsGrid.setSkin("dhx_skyblue");
                IncReportsGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseBusinessFilter();
            }
        },
        applyMonthwiseBusinessFilter:  function(flag){
            var filtrInterval;
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#bs_branchItmF").val();        
            IncReportsGrid.clearAll();
            IncReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeBusiness.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
               
                busBranchCombo = new dhtmlXCombo("bs_branchItmF");
                busBranchCombo.load(branchFilterCombo, function(){
                    busBranchCombo.setPlaceholder('Branch');
                    busBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            busBranchCombo.setComboValue('');
                        else
                            busBranchCombo.setComboValue(flag);
                    } else {
                        busBranchCombo.setComboValue('');
                    }
                });

                busBranchCombo.setOptionWidth(350);
                busBranchCombo.attachEvent("onChange", function() {
                    var busComboVal = busBranchCombo.getSelectedValue();
                    if(!busBranchCombo.getSelectedValue() && busBranchCombo.getComboText()) busComboVal = busBranchCombo.getComboText();
                    $("#bs_branchItmF").val(busComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseBusinessFilter(busComboVal);
                });

                if(IncReportsGrid.doesRowExist("no_record"))
                    IncReportsGrid.enableRowsHover(false);
                else
                    IncReportsGrid.enableRowsHover(true,"bonusReportHover");

                var srtFlg          = 0;
                $('.inc_bs_Sort_BIR').click(function() {                          
                    var colId       =   $(this).attr("colNum");
                    if(colId == 1)
                        IncReportsGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            IncReportsGrid.sortRows(colId,"int","asc");
                        srtFlg      = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            IncReportsGrid.sortRows(colId,"int","desc");
                        srtFlg      = 0;
                    }
                });
            });
        },
        str_custom : function(a,b,order){ 
            return (a.toLowerCase() > b.toLowerCase() ? 1 : -1)*(order=="asc" ? 1 : -1);
        },
        viewMonthwiseStatutoryReport: function() {
            if(statutoryIncRptFlag != 1){
                statutoryIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeStatutoryRpt").setActive();
                IncStatutoryReportLayout =  IncomeReportsTabbar.cells("viewIncomeStatutoryRpt").attachLayout("1C");
                IncStatutoryReportLayout.cells("a").hideHeader();
                                                 
                IncStatutoryReportLayout.cells("a").setWidth('200');
                IncStatutoryReportsGrid = IncStatutoryReportLayout.cells("a").attachGrid();
                IncStatutoryReportsGrid.setImagePath("../../codebase/imgs/");
                IncStatutoryReportsGrid.setSkin("dhx_skyblue");
                IncStatutoryReportsGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseStatutoryFilter();
            }
        },
        applyMonthwiseStatutoryFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#incStatItmF").val();        
            IncStatutoryReportsGrid.clearAll();
            IncStatutoryReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeStatutory.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                
                statBranchCombo = new dhtmlXCombo("incStatItmF");
                statBranchCombo.load(branchFilterCombo, function(){
                    statBranchCombo.setPlaceholder('Branch');
                    statBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            statBranchCombo.setComboValue('');
                        else
                            statBranchCombo.setComboValue(flag);
                    } else {
                        statBranchCombo.setComboValue('');
                    }
                });

                statBranchCombo.setOptionWidth(350);
                statBranchCombo.attachEvent("onChange", function() {
                    var statComboVal = statBranchCombo.getSelectedValue();
                    if(!statBranchCombo.getSelectedValue() && statBranchCombo.getComboText()) statComboVal = statBranchCombo.getComboText();
                    $( "#incStatItmF" ).val(statComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseStatutoryFilter(statComboVal);
                });
               
                if(IncStatutoryReportsGrid.doesRowExist("no_record"))
                    IncStatutoryReportsGrid.enableRowsHover(false);
                else
                    IncStatutoryReportsGrid.enableRowsHover(true,"bonusReportHover");
                var srtFlg          = 0;
               
                $('.st_Sort_BIR').click(function() {                          
                    var colId       =   $(this).attr("colNum");
                    if(colId == 1)
                        IncStatutoryReportsGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            IncStatutoryReportsGrid.sortRows(colId,"int","asc");
                        srtFlg      = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            IncStatutoryReportsGrid.sortRows(colId,"int","desc");
                        srtFlg      = 0;
                    }
                });
            });
        },
        viewFixedExpenseSettings : function() {
            if(settingsIncFlag != 1){
                settingsIncFlag = 1; 
                var filtrInterval;
                PRS_General_Items = PRS_Fixed_Items = PRS_Variable_Items = '';
                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/items.php&fixedExp=1")
                }).done(function(data) {
                    itemsFilterCombo = data;
                });
                
                incFixedExpenseSettingsLayout =  IncomeReportsTabbar.cells("viewIncomeFixedExpSettings").attachLayout("1C");
                incFixedExpenseSettingsLayout.cells("a").hideHeader();
                                                 
                incFixedExpenseSettingsLayout.cells("a").setWidth('200');
                IncomeFixedExpenseSettingsGrid = incFixedExpenseSettingsLayout.cells("a").attachGrid();
                IncomeFixedExpenseSettingsGrid.setImagePath("../../codebase/imgs/");
                IncomeFixedExpenseSettingsGrid.setSkin("dhx_skyblue");
                IncomeFixedExpenseSettingsGrid.setHeader("SlNo,Item,Sub Head,Statutory,Branch Fixed,Branch Flexi,General,Do not include");
                IncomeFixedExpenseSettingsGrid.attachHeader(',<div id="expIncItmF" style="width: 90%;" placeholder="Item"></div>,<input type="text" class="incSubheadTxt_filter" id="incExSubhead" style="width: 90%;" placeholder="Sub Head">,<select class = "checkstat" id = "statIncExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "branchIncFxdExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "branchIncVarExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "generalIncExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "excludeIncExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>');

                IncomeFixedExpenseSettingsGrid.setInitWidths("40,*,*,110,110,110,110,110");
                IncomeFixedExpenseSettingsGrid.enableColSpan(true);
                IncomeFixedExpenseSettingsGrid.setColAlign("left,left,left,center,center,center,center,center");
                IncomeFixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ch,ch,ch,ch,ch");
                IncomeFixedExpenseSettingsGrid.enableTooltips("false,false,false,false,false,false,false,false");
                
                notfButtonBar = IncomeReportsTabbar.tabs("viewIncomeFixedExpSettings").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='exp_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='fixedExpenseItems'></span></div>",
                    height: 35
                });
                        
                IncomeFixedExpenseSettingsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                IncomeFixedExpenseSettingsGrid.enablePaging(true,50,5,'fixedExpenseItems',false);
                IncomeFixedExpenseSettingsGrid.setPagingSkin("toolbar");
                IncomeFixedExpenseSettingsGrid.init();
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                IncomeFixedExpenseSettingsGrid.loadXML(preTally.Initialize.encryptURL("requisites/fixedExpenseSettingsIncRpt.php"), function() {
                    $('.exp_cnt_tot').html("# : "+IncomeFixedExpenseSettingsGrid.getUserData("", "TL_Count")+" ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
       
                    if(IncomeFixedExpenseSettingsGrid.doesRowExist("no_records")) {
                        IncomeFixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                    } else {
                        IncomeFixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ch,ch,ch,ch,ch");
                    }
                    IncomeFixedExpenseSettingsGrid.attachEvent("onCheck", function(rId,cInd,state) {
                        
                        PRS_Stat_Items          = IncomeFixedExpenseSettingsGrid.getUserData("", "PRS_Stat_Items");
                        var stat_split_str      = PRS_Stat_Items.split(",");
                        var statIndexValue      = stat_split_str.indexOf(rId);

                        PRS_Fixed_Items         = IncomeFixedExpenseSettingsGrid.getUserData("", "PRS_Fixed_Items");
                        var fixed_split_str     = PRS_Fixed_Items.split(",");
                        var fixedIndexValue     = fixed_split_str.indexOf(rId);

                        PRS_Variable_Items      = IncomeFixedExpenseSettingsGrid.getUserData("", "PRS_Variable_Items");
                        var var_split_str       = PRS_Variable_Items.split(",");
                        var varIndexValue       = var_split_str.indexOf(rId);
                        
                        PRS_General_Items       = IncomeFixedExpenseSettingsGrid.getUserData("", "PRS_General_Items");
                        var gen_split_str       = PRS_General_Items.split(",");
                        var genIndexValue       = gen_split_str.indexOf(rId);
                               
                        if(cInd == 3 ) {
                            
                            if(state) {   // item checked
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    PRS_Fixed_Items = fixed_split_str.join(",");
                                }
                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    PRS_Variable_Items = var_split_str.join(",");
                                }
                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    PRS_General_Items = gen_split_str.join(",");
                                }
                                if (statIndexValue === -1) {  // string doesn't contain the checked item
                                    PRS_Stat_Items += ","+rId;
                                }
                            } else {  // item unchecked
                                if (statIndexValue !== -1) {  // string contain the unchecked item
                                    stat_split_str.splice(statIndexValue,1);
                                    PRS_Stat_Items = stat_split_str.join(",");
                                }
                            } 
                            IncomeFixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,7).setValue(0); 
                            preTally.IncomeProfitReport.saveExpenseItems();

                        } else if(cInd == 4 ) {
                                if(state) {   // item checked

                                    if (genIndexValue !== -1) { 
                                        gen_split_str.splice(genIndexValue,1);
                                        PRS_General_Items = gen_split_str.join(",");
                                    }
                                    if (varIndexValue !== -1) { 
                                        var_split_str.splice(varIndexValue,1);
                                        PRS_Variable_Items = var_split_str.join(",");
                                    }
                                    if (statIndexValue !== -1) { 
                                        stat_split_str.splice(statIndexValue,1);
                                        PRS_Stat_Items = stat_split_str.join(",");
                                    }
                                    if (fixedIndexValue === -1) {  // string doesn't contain the checked item
                                        PRS_Fixed_Items += ","+rId;
                                    }
                                } else {  // item unchecked
                                    if (fixedIndexValue !== -1) {  // string contain the unchecked item
                                        fixed_split_str.splice(fixedIndexValue,1);
                                        PRS_Fixed_Items = fixed_split_str.join(",");
                                    }
                                } 
                                IncomeFixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                                IncomeFixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                                IncomeFixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                                IncomeFixedExpenseSettingsGrid.cells(rId,7).setValue(0); 
                                preTally.IncomeProfitReport.saveExpenseItems();

                        } else if(cInd == 5) {
                            
                            if(state) {   // item checked
                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    PRS_General_Items = gen_split_str.join(",");
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    PRS_Fixed_Items = fixed_split_str.join(",");
                                }
                                if (statIndexValue !== -1) { 
                                    stat_split_str.splice(statIndexValue,1);
                                    PRS_Stat_Items = stat_split_str.join(",");
                                }
                                if (varIndexValue === -1) {  // string doesn't contain the checked item
                                    PRS_Variable_Items += ","+rId;
                                }
                            } else {  // item unchecked
                                if (varIndexValue !== -1) {  // string contain the unchecked item
                                    var_split_str.splice(varIndexValue,1);
                                    PRS_Variable_Items = var_split_str.join(",");
                                }
                            } 
                            IncomeFixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,6).setValue(0);
                            IncomeFixedExpenseSettingsGrid.cells(rId,7).setValue(0);
                            preTally.IncomeProfitReport.saveExpenseItems();

                        } else if(cInd == 6) {
                           
                            if(state) {   // item checked
                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    PRS_Variable_Items = var_split_str.join(",");
                                }
                                if (statIndexValue !== -1) { 
                                    stat_split_str.splice(statIndexValue,1);
                                    PRS_Stat_Items = stat_split_str.join(",");
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    PRS_Fixed_Items = fixed_split_str.join(",");
                                }
                                if (genIndexValue === -1) {  // string doesn't contain the checked item
                                    PRS_General_Items += ","+rId;
                                }

                            } else {  // item unchecked
                                if (genIndexValue !== -1) {  // string contain the unchecked item
                                    gen_split_str.splice(genIndexValue,1);
                                    PRS_General_Items = gen_split_str.join(",");
                                }
                            } 
                            IncomeFixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,7).setValue(0); 
                            preTally.IncomeProfitReport.saveExpenseItems();
                                  
                        } else if(cInd == 7) {
                           
                            if(state) {   // item checked
                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    PRS_Variable_Items = var_split_str.join(",");
                                }
                                if (statIndexValue !== -1) { 
                                    stat_split_str.splice(statIndexValue,1);
                                    PRS_Stat_Items = stat_split_str.join(",");
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    PRS_Fixed_Items = fixed_split_str.join(",");
                                }
                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    PRS_General_Items = gen_split_str.join(",");
                                }
                                IncomeFixedExpenseSettingsGrid.cells(rId,7).setValue(1);  
                            } 
                            IncomeFixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                            IncomeFixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                            preTally.IncomeProfitReport.saveExpenseItems();
                        }
                    });
                
                    itemCombo = new dhtmlXCombo("expIncItmF");
                    itemCombo.load(itemsFilterCombo, function(){
                        itemCombo.setPlaceholder('Item');
                        itemCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                r = true;
                            }
                            return r;
                        });
                    });

                    itemCombo.setOptionWidth(350);
                    itemCombo.attachEvent("onChange", function() {
                        var itmComboVal = itemCombo.getSelectedValue();
                        if(!itemCombo.getSelectedValue() && itemCombo.getComboText()) itmComboVal = itemCombo.getComboText();
                        $( "#expIncItmF" ).val(itmComboVal);
                        preTally.IncomeProfitReport.fixedExpenseSettingsFilter();
                    });
                    
                    $(".checkstat" ).change(function() {
                        var FiltrId =   this.id;
                        $(".checkstat" ).each(function(){
                            if(FiltrId  !=  this.id){
                                $('#'+this.id).val('');
                            }
                        });
                        preTally.IncomeProfitReport.fixedExpenseSettingsFilter();
                    });
                    
                    $( ".incSubheadTxt_filter" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.IncomeProfitReport.fixedExpenseSettingsFilter();
                            clearInterval(filtrInterval); 
                        }, 500);
                    });
                });
            }
        },
        saveExpenseItems : function() {
            $.post(preTally.Initialize.encryptURL("warehouse/fixedExpenseSettingsIncRptSave.php"),{PRS_Fixed_Items:PRS_Fixed_Items,PRS_Variable_Items:PRS_Variable_Items,PRS_General_Items:PRS_General_Items,PRS_Stat_Items:PRS_Stat_Items},function(data){
                dhtmlx.message({text:data});
                preTally.IncomeProfitReport.fixedExpenseSettingsFilter(1);
            });
        },
        fixedExpenseSettingsFilter : function(flag) {
            var currpage = IncomeFixedExpenseSettingsGrid.currentPage ;
            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array($('#expIncItmF').val(), $('#incExSubhead').val(),$("#generalIncExp").val(),$("#branchIncFxdExp").val(),$("#branchIncVarExp").val(),$("#excludeIncExp").val(),$("#statIncExp").val());

            IncomeFixedExpenseSettingsGrid.clearAll();
            IncomeFixedExpenseSettingsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/fixedExpenseSettingsIncRpt.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(flag)
                    IncomeFixedExpenseSettingsGrid.changePage(currpage); 
                $('.exp_cnt_tot').html("# : "+IncomeFixedExpenseSettingsGrid.getUserData("", "TL_Count")+" ");
            });
        },
        viewMonthwiseFixedExpReport: function() {
            if(fixedIncExpRptFlag != 1){
                fixedIncExpRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeFixedExpRpt").setActive();
                incFixedReportLayout =  IncomeReportsTabbar.cells("viewIncomeFixedExpRpt").attachLayout("1C");
                incFixedReportLayout.cells("a").hideHeader();
                                                 
                incFixedReportLayout.cells("a").setWidth('200');
                IncFixedReportGrid = incFixedReportLayout.cells("a").attachGrid();
                IncFixedReportGrid.setImagePath("../../codebase/imgs/");
                IncFixedReportGrid.setSkin("dhx_skyblue");
                IncFixedReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseFixedExpFilter();
            }
        },
        applyMonthwiseFixedExpFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#fxdExpItmF").val();        
            IncFixedReportGrid.clearAll();
            IncFixedReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeFixed.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(IncFixedReportGrid.doesRowExist("no_record"))
                    IncFixedReportGrid.enableRowsHover(false);
                else
                    IncFixedReportGrid.enableRowsHover(true,"bonusReportHover");
                
                fixedBranchCombo = new dhtmlXCombo("fxdExpItmF");
                fixedBranchCombo.load(branchFilterCombo, function(){
                    fixedBranchCombo.setPlaceholder('Branch');
                    fixedBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            fixedBranchCombo.setComboValue('');
                        else
                            fixedBranchCombo.setComboValue(flag);
                    } else {
                        fixedBranchCombo.setComboValue('');
                    }
                });

                fixedBranchCombo.setOptionWidth(350);
                fixedBranchCombo.attachEvent("onChange", function() {
                    var fixedComboVal = fixedBranchCombo.getSelectedValue();
                    if(!fixedBranchCombo.getSelectedValue() && fixedBranchCombo.getComboText()) fixedComboVal = fixedBranchCombo.getComboText();
                    $("#fxdExpItmF").val(fixedComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseFixedExpFilter(fixedComboVal);
                });
                    
                var srtFlg      = 0;
                $('.fx_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        IncFixedReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            IncFixedReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            IncFixedReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        clearAllFlags : function() {
            businessIncRptFlag     = 0;  
            statutoryIncRptFlag    = 0;
            settingsIncFlag        = 0;
            fixedIncExpRptFlag     = 0;
            overviewIncRptFlag     = 0;
            varExpIncRptFlag       = 0;
            taxIncRptFlag          = 0;
            grossIncRptFlag        = 0;
            generalIncRptFlag      = 0;
            netIncRptFlag          = 0;
        },
        viewMonthwiseOverviewReport : function() {
            if(overviewIncRptFlag != 1){
                overviewIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeOverviewRpt").setActive();
                incOverviewReportLayout =  IncomeReportsTabbar.cells("viewIncomeOverviewRpt").attachLayout("1C");
                incOverviewReportLayout.cells("a").hideHeader();
                                                 
                incOverviewReportLayout.cells("a").setWidth('200');
                incOverviewReportGrid = incOverviewReportLayout.cells("a").attachGrid();
                incOverviewReportGrid.setImagePath("../../codebase/imgs/");
                incOverviewReportGrid.setSkin("dhx_skyblue");
                incOverviewReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseOverviewFilter();
            }
        },
        applyMonthwiseOverviewFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#ovIncBranchItmF").val();        
            incOverviewReportGrid.clearAll();
            incOverviewReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeOverview.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(incOverviewReportGrid.doesRowExist("no_record"))
                    incOverviewReportGrid.enableRowsHover(false);
                else
                    incOverviewReportGrid.enableRowsHover(true,"bonusReportHover");
                
                ovBranchCombo = new dhtmlXCombo("ovIncBranchItmF");
                ovBranchCombo.load(branchFilterCombo, function(){
                    ovBranchCombo.setPlaceholder('Branch');
                    ovBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            ovBranchCombo.setComboValue('');
                        else
                            ovBranchCombo.setComboValue(flag);
                    } else {
                        ovBranchCombo.setComboValue('');
                    }
                });

                ovBranchCombo.setOptionWidth(350);
                ovBranchCombo.attachEvent("onChange", function() {
                    var ovComboVal = ovBranchCombo.getSelectedValue();
                    if(!ovBranchCombo.getSelectedValue() && ovBranchCombo.getComboText()) ovComboVal = ovBranchCombo.getComboText();
                    $("#ovIncBranchItmF").val(ovComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseOverviewFilter(ovComboVal);
                });
                
                var srtFlg      = 0;
                $('.ovInc_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        incOverviewReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            incOverviewReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            incOverviewReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseVarExpReport: function() {
            if(varExpIncRptFlag != 1){
                varExpIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeVarExpRpt").setActive();
                incVariableReportLayout =  IncomeReportsTabbar.cells("viewIncomeVarExpRpt").attachLayout("1C");
                incVariableReportLayout.cells("a").hideHeader();
                                                 
                incVariableReportLayout.cells("a").setWidth('200');
                IncVariableReportGrid = incVariableReportLayout.cells("a").attachGrid();
                IncVariableReportGrid.setImagePath("../../codebase/imgs/");
                IncVariableReportGrid.setSkin("dhx_skyblue");
                IncVariableReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseVariableFilter();
            }
        },
        applyMonthwiseVariableFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#flexiBranchItmF").val();              
            IncVariableReportGrid.clearAll();
            IncVariableReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeFlexi.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(IncVariableReportGrid.doesRowExist("no_record"))
                    IncVariableReportGrid.enableRowsHover(false);
                else
                    IncVariableReportGrid.enableRowsHover(true,"bonusReportHover");
                
                varBranchCombo = new dhtmlXCombo("flexiBranchItmF");
                varBranchCombo.load(branchFilterCombo, function(){
                    varBranchCombo.setPlaceholder('Branch');
                    varBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            varBranchCombo.setComboValue('');
                        else
                            varBranchCombo.setComboValue(flag);
                    } else {
                        varBranchCombo.setComboValue('');
                    }
                });

                varBranchCombo.setOptionWidth(350);
                varBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = varBranchCombo.getSelectedValue();
                    if(!varBranchCombo.getSelectedValue() && varBranchCombo.getComboText()) varComboVal = varBranchCombo.getComboText();
                    $("#flexiBranchItmF").val(varComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseVariableFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.flexi_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        IncVariableReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            IncVariableReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            IncVariableReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseTaxReport : function() {
            if(taxIncRptFlag != 1){
                taxIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeTaxRpt").setActive();
                incTaxReportLayout =  IncomeReportsTabbar.cells("viewIncomeTaxRpt").attachLayout("1C");
                incTaxReportLayout.cells("a").hideHeader();
                                                 
                incTaxReportLayout.cells("a").setWidth('200');
                incTaxReportGrid = incTaxReportLayout.cells("a").attachGrid();
                incTaxReportGrid.setImagePath("../../codebase/imgs/");
                incTaxReportGrid.setSkin("dhx_skyblue");
                incTaxReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseTaxFilter();
            }
        },
        applyMonthwiseTaxFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#incTax").val();              
            incTaxReportGrid.clearAll();
            incTaxReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeTax.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(incTaxReportGrid.doesRowExist("no_record"))
                    incTaxReportGrid.enableRowsHover(false);
                else
                    incTaxReportGrid.enableRowsHover(true,"bonusReportHover");
                
                taxBranchCombo = new dhtmlXCombo("incTax");
                taxBranchCombo.load(branchFilterCombo, function(){
                    taxBranchCombo.setPlaceholder('Branch');
                    taxBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            taxBranchCombo.setComboValue('');
                        else
                            taxBranchCombo.setComboValue(flag);
                    } else {
                        taxBranchCombo.setComboValue('');
                    }
                });

                taxBranchCombo.setOptionWidth(350);
                taxBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = taxBranchCombo.getSelectedValue();
                    if(!taxBranchCombo.getSelectedValue() && taxBranchCombo.getComboText()) varComboVal = taxBranchCombo.getComboText();
                    $("#incTax").val(varComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseTaxFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.taxInc_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        incTaxReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            incTaxReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            incTaxReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseGrossReport : function() {
            if(grossIncRptFlag != 1){
                grossIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeGrossRpt").setActive();
                incGrossReportLayout =  IncomeReportsTabbar.cells("viewIncomeGrossRpt").attachLayout("1C");
                incGrossReportLayout.cells("a").hideHeader();
                                                 
                incGrossReportLayout.cells("a").setWidth('200');
                incGrossReportGrid = incGrossReportLayout.cells("a").attachGrid();
                incGrossReportGrid.setImagePath("../../codebase/imgs/");
                incGrossReportGrid.setSkin("dhx_skyblue");
                incGrossReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseGrossFilter();
            }
        },
        applyMonthwiseGrossFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#grossIncTax").val();              
            incGrossReportGrid.clearAll();
            incGrossReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeGross.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(incGrossReportGrid.doesRowExist("no_record"))
                    incGrossReportGrid.enableRowsHover(false);
                else
                    incGrossReportGrid.enableRowsHover(true,"bonusReportHover");
                
                grossBranchCombo = new dhtmlXCombo("grossIncTax");
                grossBranchCombo.load(branchFilterCombo, function(){
                    grossBranchCombo.setPlaceholder('Branch');
                    grossBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            grossBranchCombo.setComboValue('');
                        else
                            grossBranchCombo.setComboValue(flag);
                    } else {
                        grossBranchCombo.setComboValue('');
                    }
                });

                grossBranchCombo.setOptionWidth(350);
                grossBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = grossBranchCombo.getSelectedValue();
                    if(!grossBranchCombo.getSelectedValue() && grossBranchCombo.getComboText()) varComboVal = grossBranchCombo.getComboText();
                    $("#grossIncTax").val(varComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseGrossFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.grossInc_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        incGrossReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            incGrossReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            incGrossReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseGeneralReport : function() {
            if(generalIncRptFlag != 1){
                generalIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeGeneralExpRpt").setActive();
                incGeneralReportLayout =  IncomeReportsTabbar.cells("viewIncomeGeneralExpRpt").attachLayout("1C");
                incGeneralReportLayout.cells("a").hideHeader();
                                                 
                incGeneralReportLayout.cells("a").setWidth('200');
                incGeneralReportGrid = incGeneralReportLayout.cells("a").attachGrid();
                incGeneralReportGrid.setImagePath("../../codebase/imgs/");
                incGeneralReportGrid.setSkin("dhx_skyblue");
                incGeneralReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseGeneralFilter();
            }
        },
        applyMonthwiseGeneralFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#generalIncRptBranch").val();              
            incGeneralReportGrid.clearAll();
            incGeneralReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeGeneral.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(incGeneralReportGrid.doesRowExist("no_record"))
                    incGeneralReportGrid.enableRowsHover(false);
                else
                    incGeneralReportGrid.enableRowsHover(true,"bonusReportHover");
                
                genBranchCombo = new dhtmlXCombo("generalIncRptBranch");
                genBranchCombo.load(branchFilterCombo, function(){
                    genBranchCombo.setPlaceholder('Branch');
                    genBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            genBranchCombo.setComboValue('');
                        else
                            genBranchCombo.setComboValue(flag);
                    } else {
                        genBranchCombo.setComboValue('');
                    }
                });

                genBranchCombo.setOptionWidth(350);
                genBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = genBranchCombo.getSelectedValue();
                    if(!genBranchCombo.getSelectedValue() && genBranchCombo.getComboText()) varComboVal = genBranchCombo.getComboText();
                    $("#generalIncRptBranch").val(varComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseGeneralFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.genInc_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        incGeneralReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            incGeneralReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            incGeneralReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseNetProfitReport : function() {
            if(netIncRptFlag != 1){
                netIncRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                IncomeReportsTabbar.tabs("viewIncomeNetProfitRpt").setActive();
                incNetReportLayout =  IncomeReportsTabbar.cells("viewIncomeNetProfitRpt").attachLayout("1C");
                incNetReportLayout.cells("a").hideHeader();
                                                 
                incNetReportLayout.cells("a").setWidth('200');
                IncNetReportGrid = incNetReportLayout.cells("a").attachGrid();
                IncNetReportGrid.setImagePath("../../codebase/imgs/");
                IncNetReportGrid.setSkin("dhx_skyblue");
                IncNetReportGrid.enableColSpan(true);
                
                preTally.IncomeProfitReport.applyMonthwiseNetFilter();
            }
        },
        applyMonthwiseNetFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchIncNet").val();              
            IncNetReportGrid.clearAll();
            IncNetReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportIncomeNet.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(IncNetReportGrid.doesRowExist("no_record"))
                    IncNetReportGrid.enableRowsHover(false);
                else
                    IncNetReportGrid.enableRowsHover(true,"bonusReportHover");
                
                netBranchCombo = new dhtmlXCombo("branchIncNet");
                netBranchCombo.load(branchFilterCombo, function(){
                    netBranchCombo.setPlaceholder('Branch');
                    netBranchCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                    if(flag) {
                        if(flag == 'All')
                            netBranchCombo.setComboValue('');
                        else
                            netBranchCombo.setComboValue(flag);
                    } else {
                        netBranchCombo.setComboValue('');
                    }
                });

                netBranchCombo.setOptionWidth(350);
                netBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = netBranchCombo.getSelectedValue();
                    if(!netBranchCombo.getSelectedValue() && netBranchCombo.getComboText()) varComboVal = netBranchCombo.getComboText();
                    $("#branchIncNet").val(varComboVal);
                    preTally.IncomeProfitReport.applyMonthwiseNetFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.netInc_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        IncNetReportGrid.setCustomSorting(preTally.IncomeProfitReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            IncNetReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            IncNetReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
   };
})(jQuery, this);