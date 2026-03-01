;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthData,yearData,branchFilterCombo;
    var paginationFlag = 0;
    preTally.BusinessBonusReport = {
        viewBusinessBonusReports : function(){
            paginationFlag = 0;
            if (!dhxMiddleBlockTabs.cells("view_monthlybusinessreports")) {
                dhxMiddleBlockTabs.addTab("view_monthlybusinessreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Business and Bonus&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='rptRefreshTab'/>", 305);
                dhxMiddleBlockTabs.tabs("view_monthlybusinessreports").setActive();
                
                dhxMasterReportsLayout =  dhxMiddleBlockTabs.cells("view_monthlybusinessreports").attachLayout("1C");
                
                BSBonusReportToolbar = dhxMasterReportsLayout.cells("a").attachToolbar();                
                BSBonusReportToolbar.setIconsPath("images/icon/default_18/");
                BSBonusReportToolbar.setAlign('right');
                
                $(".rptRefreshTab").click(function(){
                    
                    preTally.BusinessBonusReport.clearAllFlags();
                    var actvId = BSBonusReportsTabbar.getActiveTab();
                    if(actvId == 'viewMonthwiseBusinessRpt') preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
                    if(actvId == 'viewMonthwiseTaxRpt') preTally.BusinessBonusReport.viewMonthwiseTaxReport();
                    if(actvId == 'viewMonthwiseStatutoryRpt') preTally.BusinessBonusReport.viewMonthwiseStatutoryReport();
                    if(actvId == 'viewMonthwiseGrossRpt') preTally.BusinessBonusReport.viewMonthwiseGrossReport();
                    if(actvId == 'viewMonthwiseFixedExpRpt') preTally.BusinessBonusReport.viewMonthwiseFixedExpReport();
                    if(actvId == 'viewMonthwiseVarExpRpt') preTally.BusinessBonusReport.viewMonthwiseVarExpReport();
                    if(actvId == 'viewMonthwiseGeneralExpRpt') preTally.BusinessBonusReport.viewMonthwiseGeneralReport();
                    if(actvId == 'viewMonthwiseNetProfitRpt') preTally.BusinessBonusReport.viewMonthwiseNetProfitReport();                    
                    if(actvId == 'viewMonthwiseOverviewRpt') preTally.BusinessBonusReport.viewMonthwiseOverviewReport();
                    if(actvId == 'viewMonthwiseBonusRpt') preTally.BusinessBonusReport.viewMonthwiseBonusReport();
                    if(actvId == 'viewSettings') {
                        if(BSSettingsTabbar.getActiveTab() == 'viewFixedExpSettings')
                            preTally.BusinessBonusReport.viewFixedExpenseSettings();
                        else if(BSSettingsTabbar.getActiveTab() == 'viewBonusSettings')
                            preTally.BusinessBonusReport.viewBonusSettings();
                    }
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "view_monthlybusinessreports")
                        preTally.BusinessBonusReport.clearAllFlags();
                    return true;
                });

                var Months_Options  = [];
                var Years_Options   = [];
                /*
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

                    if(j < 10)
                        j = '0'+i;
                    
                    Months_Options.push(['m'+m,'obj',monthNames[m-1],"calendar_M.png"]);
                }
                */
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
                
                BSBonusReportToolbar.addButton("business_overview_export", null, "Export", "excel.png");
                BSBonusReportToolbar.addSeparator();
                BSBonusReportToolbar.hideItem('business_overview_export');
            
                BSBonusReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                BSBonusReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                BSBonusReportToolbar.attachEvent("onClick", function(id){  
                    preTally.BusinessBonusReport.clearAllFlags();
                    var pId     = BSBonusReportToolbar.getParentId(id);
                    var actvId  = BSBonusReportsTabbar.getActiveTab();
                    if(pId == 'rpt_month_filter') {
                        yearData = '';
                        if(BSBonusReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = BSBonusReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        BSBonusReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData = '';
                        if(BSBonusReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData = BSBonusReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        BSBonusReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(id == 'business_overview_export'){
                        preTally.BusinessBonusReport.exportBusinessOverviewReport();
                        return;
                    }
                    
                    if(actvId == 'viewMonthwiseBusinessRpt') preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
                    if(actvId == 'viewMonthwiseTaxRpt') preTally.BusinessBonusReport.viewMonthwiseTaxReport();
                    if(actvId == 'viewMonthwiseStatutoryRpt') preTally.BusinessBonusReport.viewMonthwiseStatutoryReport();
                    if(actvId == 'viewMonthwiseGrossRpt') preTally.BusinessBonusReport.viewMonthwiseGrossReport();
                    if(actvId == 'viewMonthwiseFixedExpRpt') preTally.BusinessBonusReport.viewMonthwiseFixedExpReport();
                    if(actvId == 'viewMonthwiseVarExpRpt') preTally.BusinessBonusReport.viewMonthwiseVarExpReport();
                    if(actvId == 'viewMonthwiseGeneralExpRpt') preTally.BusinessBonusReport.viewMonthwiseGeneralReport();
                    if(actvId == 'viewMonthwiseNetProfitRpt') preTally.BusinessBonusReport.viewMonthwiseNetProfitReport();                    
                    if(actvId == 'viewMonthwiseOverviewRpt') preTally.BusinessBonusReport.viewMonthwiseOverviewReport();
                    if(actvId == 'viewMonthwiseBonusRpt') preTally.BusinessBonusReport.viewMonthwiseBonusReport();
                    if(actvId == 'viewSettings') preTally.BusinessBonusReport.viewSettings();
                });
 
                var date    = new Date();
                var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                monthData   = date.getMonth();
                BSBonusReportToolbar.setItemText('rpt_month_filter',m_names[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                yearData    =date.getFullYear();
                BSBonusReportsTabbar = dhxMasterReportsLayout.cells("a").attachTabbar();
                BSBonusReportsTabbar.addTab("viewMonthwiseBusinessRpt", "Business Report",120);
                BSBonusReportsTabbar.addTab("viewMonthwiseTaxRpt", "Tax Expense",85);
                BSBonusReportsTabbar.addTab("viewMonthwiseStatutoryRpt", "Statutory Expense",120);
                BSBonusReportsTabbar.addTab("viewMonthwiseGrossRpt", "Gross Profit",80);
                BSBonusReportsTabbar.addTab("viewMonthwiseFixedExpRpt", "Fixed Expense",90);
                BSBonusReportsTabbar.addTab("viewMonthwiseVarExpRpt", "Flexi Expense",90);
                BSBonusReportsTabbar.addTab("viewMonthwiseGeneralExpRpt", "General Expense",100);
                BSBonusReportsTabbar.addTab("viewMonthwiseNetProfitRpt", "Net Profit",70);
                BSBonusReportsTabbar.addTab("viewMonthwiseOverviewRpt", "Business and Bonus Overview",175);
                BSBonusReportsTabbar.addTab("viewMonthwiseBonusRpt", "Individual Bonus",105);
                BSBonusReportsTabbar.addTab("viewSettings", "Expense Settings",110);
                
                businessRptFlag = 0;
                preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
                
                BSBonusReportsTabbar.tabs("viewMonthwiseBusinessRpt").setActive();
                BSBonusReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    
                    if(paginationFlag !== 0) { 
                        dhtmlx.alert({text:"Some setting changed.<br><b> SAVE & CONTINUE. <b>"});
                        return false;
                    }
                    if(id == 'viewMonthwiseOverviewRpt'){
                        BSBonusReportToolbar.showItem('business_overview_export');
                    }else{
                       BSBonusReportToolbar.hideItem('business_overview_export');
                    }
                    
                    if(id == 'viewMonthwiseBusinessRpt') preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
                    if(id == 'viewMonthwiseTaxRpt') preTally.BusinessBonusReport.viewMonthwiseTaxReport();
                    if(id == 'viewMonthwiseStatutoryRpt') preTally.BusinessBonusReport.viewMonthwiseStatutoryReport();
                    if(id == 'viewMonthwiseGrossRpt') preTally.BusinessBonusReport.viewMonthwiseGrossReport();
                    if(id == 'viewMonthwiseFixedExpRpt') preTally.BusinessBonusReport.viewMonthwiseFixedExpReport();
                    if(id == 'viewMonthwiseVarExpRpt') preTally.BusinessBonusReport.viewMonthwiseVarExpReport();
                    if(id == 'viewMonthwiseGeneralExpRpt') preTally.BusinessBonusReport.viewMonthwiseGeneralReport();
                    if(id == 'viewMonthwiseNetProfitRpt') preTally.BusinessBonusReport.viewMonthwiseNetProfitReport();                    
                    if(id == 'viewMonthwiseOverviewRpt') preTally.BusinessBonusReport.viewMonthwiseOverviewReport();
                    if(id == 'viewMonthwiseBonusRpt') preTally.BusinessBonusReport.viewMonthwiseBonusReport();
                    if(id == 'viewSettings') {
                        if(expenseFlag == 1 || bonusFlag == 1) {
                            BSSettingsTabbar.tabs(BSSettingsTabbar.getActiveTab()).setActive();
                            preTally.BusinessBonusReport.viewFixedExpenseSettings();
                        } else {
                            preTally.BusinessBonusReport.viewSettings();
                        }
                    }
                    return true;
                });
                
            } else {
                dhxMiddleBlockTabs.tabs("view_monthlybusinessreports").setActive();
                var actvId = BSBonusReportsTabbar.getActiveTab(); 
                if(actvId == 'viewMonthwiseBusinessRpt') preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
            }
        },
        viewMonthwiseBusinessReport : function() {
            if(businessRptFlag != 1){
                businessRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/getBranches.php&rpt=bs")
                }).done(function(data) {
                    branchFilterCombo = data;
                });
                
                BSBonusReportsTabbar.tabs("viewMonthwiseBusinessRpt").setActive();
                dhxBusinessReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseBusinessRpt").attachLayout("1C");
                dhxBusinessReportLayout.cells("a").hideHeader();
                                                 
                dhxBusinessReportLayout.cells("a").setWidth('200');
                BSBonusReportsGrid = dhxBusinessReportLayout.cells("a").attachGrid();
                BSBonusReportsGrid.setImagePath("../../codebase/imgs/");
                BSBonusReportsGrid.setSkin("dhx_skyblue");
                BSBonusReportsGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseBusinessFilter();
            }
        },
        applyMonthwiseBusinessFilter:  function(flag){
            var filtrInterval;
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchItmF").val();        
            BSBonusReportsGrid.clearAll();
            BSBonusReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseBusiness.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
               
                busBranchCombo = new dhtmlXCombo("branchItmF");
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
                    $("#branchItmF").val(busComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseBusinessFilter(busComboVal);
                });

                if(BSBonusReportsGrid.doesRowExist("no_record"))
                    BSBonusReportsGrid.enableRowsHover(false);
                else
                    BSBonusReportsGrid.enableRowsHover(true,"bonusReportHover");

                var srtFlg          = 0;
                $('.bs_Sort_BIR').click(function() {                          
                    var colId       =   $(this).attr("colNum");
                    if(colId == 1)
                        BSBonusReportsGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            BSBonusReportsGrid.sortRows(colId,"int","asc");
                        srtFlg      = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            BSBonusReportsGrid.sortRows(colId,"int","desc");
                        srtFlg      = 0;
                    }
                });
            });
        },
        str_custom : function(a,b,order){ 
            return (a.toLowerCase() > b.toLowerCase() ? 1 : -1)*(order=="asc" ? 1 : -1);
        },
        viewMonthwiseStatutoryReport: function() {
            if(statutoryRptFlag != 1){
                statutoryRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseStatutoryRpt").setActive();
                dhxStatutoryReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseStatutoryRpt").attachLayout("1C");
                dhxStatutoryReportLayout.cells("a").hideHeader();
                                                 
                dhxStatutoryReportLayout.cells("a").setWidth('200');
                BSStatutoryReportsGrid = dhxStatutoryReportLayout.cells("a").attachGrid();
                BSStatutoryReportsGrid.setImagePath("../../codebase/imgs/");
                BSStatutoryReportsGrid.setSkin("dhx_skyblue");
                BSStatutoryReportsGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseStatutoryFilter();
            }
        },
        applyMonthwiseStatutoryFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchStatItmF").val();        
            BSStatutoryReportsGrid.clearAll();
            BSStatutoryReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseStatutory.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                
                statBranchCombo = new dhtmlXCombo("branchStatItmF");
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
                    $( "#branchStatItmF" ).val(statComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseStatutoryFilter(statComboVal);
                });
               
                if(BSStatutoryReportsGrid.doesRowExist("no_record"))
                    BSStatutoryReportsGrid.enableRowsHover(false);
                else
                    BSStatutoryReportsGrid.enableRowsHover(true,"bonusReportHover");
                var srtFlg          = 0;
               
                $('.bs_Sort_BIR').click(function() {                          
                    var colId       =   $(this).attr("colNum");
                    if(colId == 1)
                        BSStatutoryReportsGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            BSStatutoryReportsGrid.sortRows(colId,"int","asc");
                        srtFlg      = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            BSStatutoryReportsGrid.sortRows(colId,"int","desc");
                        srtFlg      = 0;
                    }
                });
            });
        },
        viewSettings : function() {
            if(settingsFlag != 1){
                settingsFlag = 1; 
                var filtrInterval;
                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/items.php&fixedExp=1")
                }).done(function(data) {
                    itemsFilterCombo = data;
                });
                BSBonusReportsTabbar.tabs("viewSettings").setActive();
                var settingsLayout = BSBonusReportsTabbar.cells("viewSettings").attachLayout("1C");
                BSSettingsTabbar = settingsLayout.cells("a").attachTabbar();
                BSSettingsTabbar.addTab("viewFixedExpSettings", "Expense Settings");
                BSSettingsTabbar.addTab("viewBonusSettings", "Bonus Settings");
                
                preTally.BusinessBonusReport.viewFixedExpenseSettings();
                BSSettingsTabbar.tabs("viewFixedExpSettings").setActive();
                BSSettingsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'viewFixedExpSettings') preTally.BusinessBonusReport.viewFixedExpenseSettings();
                    if(id == 'viewBonusSettings') preTally.BusinessBonusReport.viewBonusSettings();
                    return true;
                });
            }
        },
        viewFixedExpenseSettings : function() {
            if(expenseFlag != 1){
                expenseFlag = 1; 
                var filtrInterval;
                BRS_General_Items = BRS_Fixed_Items = BRS_Variable_Items = '';
               
                FixedExpenseSettingsGrid = BSSettingsTabbar.cells("viewFixedExpSettings").attachGrid();
                FixedExpenseSettingsGrid.setImagePath("../../codebase/imgs/");
                FixedExpenseSettingsGrid.setSkin("dhx_skyblue");
                FixedExpenseSettingsGrid.setHeader("SlNo,Item,Sub Head,Branch Fixed,Branch Flexi,General,Do not include");
                FixedExpenseSettingsGrid.attachHeader(',<div id="expItmF" style="width: 90%;" placeholder="Item"></div>,<input type="text" class="fixedSubheadTxt_filter" id="fixedExSubhead" style="width: 90%;" placeholder="Sub Head">,<select class = "checkstat" id = "branchFxdExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "branchVarExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "generalExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>,<select class = "checkstat" id = "excludeExp"><option value="">All</option><option value ="1">Enabled</option><option value ="2">Disabled</option>');

                FixedExpenseSettingsGrid.setInitWidths("40,*,*,110,110,110,110");
                FixedExpenseSettingsGrid.enableColSpan(true);
                FixedExpenseSettingsGrid.setColAlign("left,left,left,center,center,center,center");
                FixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ch,ch,ch,ch");
                FixedExpenseSettingsGrid.enableTooltips("false,false,false,false,false,false,false");
                
                notfButtonBar = BSSettingsTabbar.tabs("viewFixedExpSettings").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='exp_cnt_totBS'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='fixedExpenseItemsBS'></span></div>\
                            <input type='button' value='SAVE' onclick='preTally.BusinessBonusReport.saveExpenseSettings();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
                        
                FixedExpenseSettingsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                FixedExpenseSettingsGrid.enablePaging(true,50,5,'fixedExpenseItemsBS',false);
                FixedExpenseSettingsGrid.setPagingSkin("toolbar");
                FixedExpenseSettingsGrid.init();
                
                FixedExpenseSettingsGrid.attachEvent("onBeforePageChanged", function(ind,count){ 
                    if(paginationFlag !== 0) {
                        dhtmlx.alert({text:"Some setting changed.<br><b> SAVE & CONTINUE. <b>"});
                        return false;
                    }
                    return true;
                });
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                FixedExpenseSettingsGrid.loadXML(preTally.Initialize.encryptURL("requisites/fixedExpenseSettings.php"), function() {
                    $('.exp_cnt_totBS').html("# : "+FixedExpenseSettingsGrid.getUserData("", "TL_Count")+" ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
       
                    if(FixedExpenseSettingsGrid.doesRowExist("no_records")) {
                        FixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                    } else {
                        FixedExpenseSettingsGrid.setColTypes("ro,ro,ro,ch,ch,ch,ch");
                    }
                    FixedExpenseSettingsGrid.attachEvent("onCheck", function(rId,cInd,state) {
 
                        BRS_Fixed_Items         = FixedExpenseSettingsGrid.getUserData("", "BRS_Fixed_Items");
                        var fixed_split_str     = BRS_Fixed_Items.split(",");
                        var fixedIndexValue     = fixed_split_str.indexOf(rId);

                        BRS_Variable_Items      = FixedExpenseSettingsGrid.getUserData("", "BRS_Variable_Items");
                        var var_split_str       = BRS_Variable_Items.split(",");
                        var varIndexValue       = var_split_str.indexOf(rId);
                        
                        BRS_General_Items       = FixedExpenseSettingsGrid.getUserData("", "BRS_General_Items");
                        var gen_split_str       = BRS_General_Items.split(",");
                        var genIndexValue       = gen_split_str.indexOf(rId);
                         
                        if(cInd == 3 ) {
                            if(state) {   // item checked

                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    BRS_General_Items = gen_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_General_Items",BRS_General_Items);
                                }
                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    BRS_Variable_Items = var_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Variable_Items",BRS_Variable_Items);
                                }
                                if (fixedIndexValue === -1) {  // string doesn't contain the checked item
                                    BRS_Fixed_Items += ","+rId;
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Fixed_Items",BRS_Fixed_Items);
                                }
                            } else {  // item unchecked
                                if (fixedIndexValue !== -1) {  // string contain the unchecked item
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    BRS_Fixed_Items = fixed_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Fixed_Items",BRS_Fixed_Items);
                                }
                            } 
                            FixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                            //preTally.BusinessBonusReport.saveExpenseItems();

                        } else if(cInd == 4) {
                           
                            if(state) {   // item checked
                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    BRS_General_Items = gen_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_General_Items",BRS_General_Items);
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    BRS_Fixed_Items = fixed_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Fixed_Items",BRS_Fixed_Items);
                                }
                                if (varIndexValue === -1) {  // string doesn't contain the checked item
                                    BRS_Variable_Items += ","+rId;
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Variable_Items",BRS_Variable_Items);
                                }
                            } else {  // item unchecked
                                if (varIndexValue !== -1) {  // string contain the unchecked item
                                    var_split_str.splice(varIndexValue,1);
                                    BRS_Variable_Items = var_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Variable_Items",BRS_Variable_Items);
                                }
                            } 
                            FixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                            //preTally.BusinessBonusReport.saveExpenseItems();
                                 
                        } else if(cInd == 5) {
                            
                            if(state) {   // item checked

                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    BRS_Variable_Items = var_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Variable_Items",BRS_Variable_Items);
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    BRS_Fixed_Items = fixed_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Fixed_Items",BRS_Fixed_Items);
                                }
                                if (genIndexValue === -1) {  // string doesn't contain the checked item
                                    BRS_General_Items += ","+rId;
                                    FixedExpenseSettingsGrid.setUserData("","BRS_General_Items",BRS_General_Items);
                                }
                            } else {  // item unchecked
                                if (genIndexValue !== -1) {  // string contain the unchecked item
                                    gen_split_str.splice(genIndexValue,1);
                                    BRS_General_Items = gen_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_General_Items",BRS_General_Items);
                                }
                            } 
                            FixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                            FixedExpenseSettingsGrid.cells(rId,6).setValue(0); 
                            //preTally.BusinessBonusReport.saveExpenseItems();
                               
                        } else if(cInd == 6) {
                            
                            if(state) {   // item checked
                                if (varIndexValue !== -1) { 
                                    var_split_str.splice(varIndexValue,1);
                                    BRS_Variable_Items = var_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Variable_Items",BRS_Variable_Items);
                                }
                                if (fixedIndexValue !== -1) { 
                                    fixed_split_str.splice(fixedIndexValue,1);
                                    BRS_Fixed_Items = fixed_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_Fixed_Items",BRS_Fixed_Items);
                                }
                                if (genIndexValue !== -1) { 
                                    gen_split_str.splice(genIndexValue,1);
                                    BRS_General_Items = gen_split_str.join(",");
                                    FixedExpenseSettingsGrid.setUserData("","BRS_General_Items",BRS_General_Items);
                                }
                                FixedExpenseSettingsGrid.cells(rId,3).setValue(0); 
                                FixedExpenseSettingsGrid.cells(rId,4).setValue(0); 
                                FixedExpenseSettingsGrid.cells(rId,5).setValue(0); 
                                FixedExpenseSettingsGrid.cells(rId,6).setValue(1);  
                            } 
                            //preTally.BusinessBonusReport.saveExpenseItems();
                        }
                        paginationFlag = 1 ;
                    });
                
                    itemCombo = new dhtmlXCombo("expItmF");
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
                        $( "#expItmF" ).val(itmComboVal);
                        preTally.BusinessBonusReport.fixedExpenseSettingsFilter();
                    });
                    
                    $(".checkstat" ).change(function() {
                        var FiltrId = this.id;
                        $(".checkstat" ).each(function(){
                            if(FiltrId != this.id){
                                $('#'+this.id).val('');
                            }
                        });                        
                        preTally.BusinessBonusReport.fixedExpenseSettingsFilter();
                    });
                    
                    $( ".fixedSubheadTxt_filter" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.BusinessBonusReport.fixedExpenseSettingsFilter();
                            clearInterval(filtrInterval); 
                        }, 500);
                    });
                });
            }
        },
        saveExpenseItems : function() {
            $.post(preTally.Initialize.encryptURL("warehouse/fixedExpenseSettingsSave.php"),{BRS_Fixed_Items:BRS_Fixed_Items,BRS_Variable_Items:BRS_Variable_Items,BRS_General_Items:BRS_General_Items},function(data){
                dhtmlx.message({text:data});
                preTally.BusinessBonusReport.fixedExpenseSettingsFilter(1);
            });
        },
        saveExpenseSettings : function(){
            BRS_Fixed_Items_Save         = FixedExpenseSettingsGrid.getUserData("", "BRS_Fixed_Items");
            BRS_Variable_Items_Save      = FixedExpenseSettingsGrid.getUserData("", "BRS_Variable_Items");
            BRS_General_Items_Save       = FixedExpenseSettingsGrid.getUserData("", "BRS_General_Items");
            
            $.post(preTally.Initialize.encryptURL("warehouse/fixedExpenseSettingsSave.php"),
            {
                BRS_Fixed_Items     :BRS_Fixed_Items_Save,
                BRS_Variable_Items  :BRS_Variable_Items_Save,
                BRS_General_Items   :BRS_General_Items_Save
            },function(data){
                dhtmlx.message({text:data});
                paginationFlag = 0 ;
                preTally.BusinessBonusReport.fixedExpenseSettingsFilter(1);
            });
        },
        fixedExpenseSettingsFilter : function(flag) {
            
            if(paginationFlag !== 0) {
                dhtmlx.alert({text:"Some setting changed.<br><b> SAVE & CONTINUE. <b>"});
                return false;
            }
           
            var currpage    = FixedExpenseSettingsGrid.currentPage ;
            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array($('#expItmF').val(), $('#fixedExSubhead').val(),$("#generalExp").val(),$("#branchFxdExp").val(),$("#branchVarExp").val(),$("#excludeExp").val());

            FixedExpenseSettingsGrid.clearAll();
            FixedExpenseSettingsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/fixedExpenseSettings.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(flag)
                    FixedExpenseSettingsGrid.changePage(currpage); 
                $('.exp_cnt_totBS').html("# : "+FixedExpenseSettingsGrid.getUserData("", "TL_Count")+" ");
            });
        },
        viewBonusSettings : function(){
            if(bonusFlag != 1){
                bonusFlag = 1; 
                
                var bonusLayout = BSSettingsTabbar.cells("viewBonusSettings").attachLayout("2U");
                
                bonusLayout.cells("a").setText("New Setting");
                bonusLayout.cells("b").setText("List Bonus Settings");
                bonusLayout.cells("a").setWidth(400);
                
                listBonusGrid = bonusLayout.cells("b").attachGrid();
                listBonusGrid.enableTooltips("false,false,false,false,false");
                listBonusGrid.attachHeader(',#select_filter,#select_filter,#text_filter,#text_filter');
                listBonusGrid.enableColSpan(true);
                listBonusGrid.init();
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                listBonusGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBonusSettings.php"), function() {
                    listBonusGrid.attachEvent("onRowSelect",preTally.BusinessBonusReport.editBonusSettings);                   
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    listBonusGrid.attachEvent("onFilterEnd", function() {      
                        if(listBonusGrid.getRowsNum() == 0) {
                            if(listBonusGrid.doesRowExist("no_record"))
                                listBonusGrid.deleteRow("no_record");
                            listBonusGrid.addRow('no_record',"No records found");
                            listBonusGrid.setColTypes("ro");
                            listBonusGrid.setRowTextStyle('no_record','font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                            listBonusGrid.setColspan("no_record",0,4);
                        } else {
                            var rowID = 0;
                            var i;
                            for (i = 0; i < listBonusGrid.getRowsNum(); i++){
                                rowID = listBonusGrid.getRowId(i);   
                                listBonusGrid.cells(rowID,0).setValue(i+1);
                            };  
                        }
                    });
                });
                
                BonusForm = bonusLayout.cells("a").attachForm();
                BonusForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBonusSettings.php"), function() {
                
                    BonusForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'newSettingsValidate') {
                            var newSettings = BonusForm.validate();
                            if (newSettings) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                BonusForm.send(preTally.Initialize.encryptURL('warehouse/newBonusSettings.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        BonusForm.resetValidateCss();
                                        BonusForm.clear();
                                        BonusForm.setItemValue("BPS_Id", 0);
                                    } else {
                                        response = 'Settings Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});

                                    listBonusGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBonusSettings.php"), true, true, function() {
                                        for (i = 0; i < listBonusGrid.getRowsNum(); i++){
                                            rowID = listBonusGrid.getRowId(i);   
                                            listBonusGrid.cells(rowID,0).setValue(i+1);
                                        };     
                                    });
                                });
                            }
                        } else {
                            BonusForm.resetValidateCss();
                            BonusForm.clear();
                            BonusForm.setItemValue("BPS_Id", 0);
                        }
                    });
                });
            }
        },
        editBonusSettings: function(rowId) {
            var BPS_Id = rowId;
            BonusForm.setItemValue("BPS_Id", BPS_Id);
            BonusForm.setItemValue("BPS_BranchShare", listBonusGrid.getUserData(BPS_Id, "BPS_BranchShare"));
            BonusForm.setItemValue("BPS_GroupShare", listBonusGrid.getUserData(BPS_Id, "BPS_GroupShare"));
            BonusForm.setItemValue("BPS_Year", listBonusGrid.getUserData(BPS_Id, "BPS_Year"));
            BonusForm.setItemValue("BPS_Month", listBonusGrid.getUserData(BPS_Id, "BPS_Month"));
        },
        viewMonthwiseFixedExpReport: function() {
            if(fixedExpRptFlag != 1){
                fixedExpRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseFixedExpRpt").setActive();
                dhxFixedReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseFixedExpRpt").attachLayout("1C");
                dhxFixedReportLayout.cells("a").hideHeader();
                                                 
                dhxFixedReportLayout.cells("a").setWidth('200');
                dhxFixedReportGrid   = dhxFixedReportLayout.cells("a").attachGrid();
                dhxFixedReportGrid.setImagePath("../../codebase/imgs/");
                dhxFixedReportGrid.setSkin("dhx_skyblue");
                dhxFixedReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseFixedExpFilter();
            }
        },
        applyMonthwiseFixedExpFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#fxdBranchItmF").val();        
            dhxFixedReportGrid.clearAll();
            dhxFixedReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseFixedExp.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxFixedReportGrid.doesRowExist("no_record"))
                    dhxFixedReportGrid.enableRowsHover(false);
                else
                    dhxFixedReportGrid.enableRowsHover(true,"bonusReportHover");
                
                fixedBranchCombo = new dhtmlXCombo("fxdBranchItmF");
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
                    $("#fxdBranchItmF").val(fixedComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseFixedExpFilter(fixedComboVal);
                });
                    
                var srtFlg      = 0;
                $('.bs_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxFixedReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxFixedReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxFixedReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        clearAllFlags : function() {
            businessRptFlag     = 0;  
            statutoryRptFlag    = 0;
            settingsFlag        = 0;
            fixedExpRptFlag     = 0;
            overviewRptFlag     = 0;
            varExpRptFlag       = 0;
            taxRptFlag          = 0;
            grossRptFlag        = 0;
            generalRptFlag      = 0;
            netRptFlag          = 0;
            expenseFlag         = 0;
            bonusFlag           = 0;
            bonusRptFlag        = 0;
        },
        viewMonthwiseOverviewReport : function() {
            if(overviewRptFlag != 1){
                overviewRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseOverviewRpt").setActive();
                dhxOverviewReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseOverviewRpt").attachLayout("1C");
                dhxOverviewReportLayout.cells("a").hideHeader();
                                                 
                dhxOverviewReportLayout.cells("a").setWidth('200');
                dhxOverviewReportGrid = dhxOverviewReportLayout.cells("a").attachGrid();
                dhxOverviewReportGrid.setImagePath("../../codebase/imgs/");
                dhxOverviewReportGrid.setSkin("dhx_skyblue");
                dhxOverviewReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseOverviewFilter();
            }
        },
        applyMonthwiseOverviewFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#ovBranchItmF").val();        
            dhxOverviewReportGrid.clearAll();
            dhxOverviewReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseOverview.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxOverviewReportGrid.doesRowExist("no_record"))
                    dhxOverviewReportGrid.enableRowsHover(false);
                else
                    dhxOverviewReportGrid.enableRowsHover(true,"bonusReportHover");
                
                ovBranchCombo = new dhtmlXCombo("ovBranchItmF");
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
                    $("#ovBranchItmF").val(ovComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseOverviewFilter(ovComboVal);
                });
                
                var srtFlg      = 0;
                $('.ov_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxOverviewReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxOverviewReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxOverviewReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
                preTally.BusinessBonusReport.calculateFooterValues(dhxOverviewReportGrid);
            });
        },
        viewMonthwiseVarExpReport: function() {
            if(varExpRptFlag != 1){
                varExpRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseVarExpRpt").setActive();
                dhxVariableReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseVarExpRpt").attachLayout("1C");
                dhxVariableReportLayout.cells("a").hideHeader();
                                                 
                dhxVariableReportLayout.cells("a").setWidth('200');
                dhxVariableReportGrid = dhxVariableReportLayout.cells("a").attachGrid();
                dhxVariableReportGrid.setImagePath("../../codebase/imgs/");
                dhxVariableReportGrid.setSkin("dhx_skyblue");
                dhxVariableReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseVariableFilter();
            }
        },
        applyMonthwiseVariableFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#varBranchStatItmF").val();              
            dhxVariableReportGrid.clearAll();
            dhxVariableReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseVariableRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxVariableReportGrid.doesRowExist("no_record"))
                    dhxVariableReportGrid.enableRowsHover(false);
                else
                    dhxVariableReportGrid.enableRowsHover(true,"bonusReportHover");
                
                varBranchCombo = new dhtmlXCombo("varBranchStatItmF");
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
                    $("#varBranchStatItmF").val(varComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseVariableFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.var_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxVariableReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxVariableReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxVariableReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseTaxReport : function() {
            if(taxRptFlag != 1){
                taxRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseTaxRpt").setActive();
                dhxTaxReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseTaxRpt").attachLayout("1C");
                dhxTaxReportLayout.cells("a").hideHeader();
                                                 
                dhxTaxReportLayout.cells("a").setWidth('200');
                dhxTaxReportGrid = dhxTaxReportLayout.cells("a").attachGrid();
                dhxTaxReportGrid.setImagePath("../../codebase/imgs/");
                dhxTaxReportGrid.setSkin("dhx_skyblue");
                dhxTaxReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseTaxFilter();
            }
        },
        applyMonthwiseTaxFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchTax").val();              
            dhxTaxReportGrid.clearAll();
            dhxTaxReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseTaxRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxTaxReportGrid.doesRowExist("no_record"))
                    dhxTaxReportGrid.enableRowsHover(false);
                else
                    dhxTaxReportGrid.enableRowsHover(true,"bonusReportHover");
                
                txBranchCombo = new dhtmlXCombo("branchTax");
                txBranchCombo.load(branchFilterCombo, function(){
                    txBranchCombo.setPlaceholder('Branch');
                    txBranchCombo.setFilterHandler(function(mask, option){
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
                            txBranchCombo.setComboValue('');
                        else
                            txBranchCombo.setComboValue(flag);
                    } else {
                        txBranchCombo.setComboValue('');
                    }
                });

                txBranchCombo.setOptionWidth(350);
                txBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = txBranchCombo.getSelectedValue();
                    if(!txBranchCombo.getSelectedValue() && txBranchCombo.getComboText()) varComboVal = txBranchCombo.getComboText();
                    $("#branchTax").val(varComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseTaxFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.tax_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxTaxReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxTaxReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxTaxReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseGrossReport : function() {
            if(grossRptFlag != 1){
                grossRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseGrossRpt").setActive();
                dhxGrossReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseGrossRpt").attachLayout("1C");
                dhxGrossReportLayout.cells("a").hideHeader();
                                                 
                dhxGrossReportLayout.cells("a").setWidth('200');
                dhxGrossReportGrid = dhxGrossReportLayout.cells("a").attachGrid();
                dhxGrossReportGrid.setImagePath("../../codebase/imgs/");
                dhxGrossReportGrid.setSkin("dhx_skyblue");
                dhxGrossReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseGrossFilter();
            }
        },
        applyMonthwiseGrossFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchGross").val();              
            dhxGrossReportGrid.clearAll();
            dhxGrossReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseGrossRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxGrossReportGrid.doesRowExist("no_record"))
                    dhxGrossReportGrid.enableRowsHover(false);
                else
                    dhxGrossReportGrid.enableRowsHover(true,"bonusReportHover");
                
                bnGrossBranchCombo = new dhtmlXCombo("branchGross");
                bnGrossBranchCombo.load(branchFilterCombo, function(){
                    bnGrossBranchCombo.setPlaceholder('Branch');
                    bnGrossBranchCombo.setFilterHandler(function(mask, option){
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
                            bnGrossBranchCombo.setComboValue('');
                        else
                            bnGrossBranchCombo.setComboValue(flag);
                    } else {
                        bnGrossBranchCombo.setComboValue('');
                    }
                });

                bnGrossBranchCombo.setOptionWidth(350);
                bnGrossBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = bnGrossBranchCombo.getSelectedValue();
                    if(!bnGrossBranchCombo.getSelectedValue() && bnGrossBranchCombo.getComboText()) varComboVal = bnGrossBranchCombo.getComboText();
                    $("#branchGross").val(varComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseGrossFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.gross_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxGrossReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxGrossReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxGrossReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseGeneralReport : function() {
            if(generalRptFlag != 1){
                generalRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseGeneralExpRpt").setActive();
                dhxGeneralReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseGeneralExpRpt").attachLayout("1C");
                dhxGeneralReportLayout.cells("a").hideHeader();
                                                 
                dhxGeneralReportLayout.cells("a").setWidth('200');
                dhxGeneralReportGrid = dhxGeneralReportLayout.cells("a").attachGrid();
                dhxGeneralReportGrid.setImagePath("../../codebase/imgs/");
                dhxGeneralReportGrid.setSkin("dhx_skyblue");
                dhxGeneralReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseGeneralFilter();
            }
        },
        applyMonthwiseGeneralFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#generalRptBranch").val();              
            dhxGeneralReportGrid.clearAll();
            dhxGeneralReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseGeneralRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxGeneralReportGrid.doesRowExist("no_record"))
                    dhxGeneralReportGrid.enableRowsHover(false);
                else
                    dhxGeneralReportGrid.enableRowsHover(true,"bonusReportHover");
                
                bnGenCombo = new dhtmlXCombo("generalRptBranch");
                bnGenCombo.load(branchFilterCombo, function(){
                    bnGenCombo.setPlaceholder('Branch');
                    bnGenCombo.setFilterHandler(function(mask, option){
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
                            bnGenCombo.setComboValue('');
                        else
                            bnGenCombo.setComboValue(flag);
                    } else {
                        bnGenCombo.setComboValue('');
                    }
                });

                bnGenCombo.setOptionWidth(350);
                bnGenCombo.attachEvent("onChange", function() {
                    var varComboVal = bnGenCombo.getSelectedValue();
                    if(!bnGenCombo.getSelectedValue() && bnGenCombo.getComboText()) varComboVal = bnGenCombo.getComboText();
                    $("#generalRptBranch").val(varComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseGeneralFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.gen_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxGeneralReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxGeneralReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxGeneralReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseNetProfitReport : function() {
            if(netRptFlag != 1){
                netRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseNetProfitRpt").setActive();
                dhxNetReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseNetProfitRpt").attachLayout("1C");
                dhxNetReportLayout.cells("a").hideHeader();
                                                 
                dhxNetReportLayout.cells("a").setWidth('200');
                dhxNetReportGrid = dhxNetReportLayout.cells("a").attachGrid();
                dhxNetReportGrid.setImagePath("../../codebase/imgs/");
                dhxNetReportGrid.setSkin("dhx_skyblue");
                dhxNetReportGrid.enableColSpan(true);
                
                preTally.BusinessBonusReport.applyMonthwiseNetFilter();
            }
        },
        applyMonthwiseNetFilter : function(flag) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m='+monthData+'&y='+yearData+'&LC_Id='+$("#branchNet").val();              
            dhxNetReportGrid.clearAll();
            dhxNetReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseNetRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxNetReportGrid.doesRowExist("no_record"))
                    dhxNetReportGrid.enableRowsHover(false);
                else
                    dhxNetReportGrid.enableRowsHover(true,"bonusReportHover");
                
                bnNetBranchCombo = new dhtmlXCombo("branchNet");
                bnNetBranchCombo.load(branchFilterCombo, function(){
                    bnNetBranchCombo.setPlaceholder('Branch');
                    bnNetBranchCombo.setFilterHandler(function(mask, option){
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
                            bnNetBranchCombo.setComboValue('');
                        else
                            bnNetBranchCombo.setComboValue(flag);
                    } else {
                        bnNetBranchCombo.setComboValue('');
                    }
                });

                bnNetBranchCombo.setOptionWidth(350);
                bnNetBranchCombo.attachEvent("onChange", function() {
                    var varComboVal = bnNetBranchCombo.getSelectedValue();
                    if(!bnNetBranchCombo.getSelectedValue() && bnNetBranchCombo.getComboText()) varComboVal = bnNetBranchCombo.getComboText();
                    $("#branchNet").val(varComboVal);
                    preTally.BusinessBonusReport.applyMonthwiseNetFilter(varComboVal);
                });
                
                var srtFlg      = 0;
                $('.net_Sort_BIR').click(function() {                          
                    var colId   = $(this).attr("colNum");
                    if(colId == 1)
                        dhxNetReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,1);
                    if(srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId != 1)
                            dhxNetReportGrid.sortRows(colId,"int","asc");
                        srtFlg  = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        if(colId != 1)
                            dhxNetReportGrid.sortRows(colId,"int","desc");
                        srtFlg  = 0;
                    }
                });
            });
        },
        viewMonthwiseBonusReport : function() {
            if(bonusRptFlag != 1){
                bonusRptFlag = 1; 
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                BSBonusReportsTabbar.tabs("viewMonthwiseBonusRpt").setActive();
                dhxBonusReportLayout =  BSBonusReportsTabbar.cells("viewMonthwiseBonusRpt").attachLayout("1C");
                dhxBonusReportLayout.cells("a").hideHeader();
                                                 
                dhxBonusReportLayout.cells("a").setWidth('200');
                dhxBonusReportGrid = dhxBonusReportLayout.cells("a").attachGrid();
                dhxBonusReportGrid.setImagePath("../../codebase/imgs/");
                dhxBonusReportGrid.setSkin("dhx_skyblue");
                dhxBonusReportGrid.enableColSpan(true);
                
                preTally.Settings.progressOn(true, dhxLayout, null);

                var rptFilterParams = '&m='+monthData+'&y='+yearData;
                dhxBonusReportGrid.clearAll();
                
                dhxBonusReportGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthwiseBonusRpt.php"+rptFilterParams), function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    if(dhxBonusReportGrid.doesRowExist("no_record"))
                        dhxBonusReportGrid.enableRowsHover(false);
                    else
                        dhxBonusReportGrid.enableRowsHover(true,"bonusReportHover");

                    bnNetBranchCombo = new dhtmlXCombo("bonusBranchItmF");
                    bnNetBranchCombo.load(branchFilterCombo, function(){
                        bnNetBranchCombo.setPlaceholder('Branch');
                        bnNetBranchCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                r = true;
                            }
                            return r;
                        });
                    });

                    bnNetBranchCombo.setOptionWidth(350);
                    bnNetBranchCombo.attachEvent("onChange", function() {
                        var varComboVal = bnNetBranchCombo.getSelectedValue();
                        if(!bnNetBranchCombo.getSelectedValue() && bnNetBranchCombo.getComboText()) varComboVal = bnNetBranchCombo.getComboText();
                        $("#bonusBranchItmF").val(varComboVal);
                        preTally.BusinessBonusReport.applyMonthwiseBonusRptFilter();
                    });

                    var filtrInterval;
                    $("#bonususer").keyup(function() {                
                        if(filtrInterval) clearInterval(filtrInterval);                    
                        filtrInterval = setInterval( function() { 
                            preTally.BusinessBonusReport.applyMonthwiseBonusRptFilter();
                            clearInterval(filtrInterval); 
                        }, 500);
                    });  

                    dhxBonusReportGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i < dhxBonusReportGrid.getColumnsNum(); i++)
                            sort += ',na';
                        dhxBonusReportGrid.setColSorting(sort);
                    });

                    var srtFlg      = 0;
                    $('.bns_Sort_BIR').click(function() {                          
                        var colId   = $(this).attr("colNum");
                        $('.bns_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(colId == 1 || colId == 2)
                            dhxBonusReportGrid.setCustomSorting(preTally.BusinessBonusReport.str_custom,colId);
                        if(srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            if(colId != 1 && colId != 2)
                                dhxBonusReportGrid.sortRows(colId,"int","asc");
                            srtFlg  = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            if(colId != 1 && colId != 2)
                                dhxBonusReportGrid.sortRows(colId,"int","desc");
                            srtFlg  = 0;
                        }
                    });
                    preTally.BusinessBonusReport.calculateFooterValues(dhxBonusReportGrid);
                });
            }
        },
        applyMonthwiseBonusRptFilter : function() {
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            var rptFilterParams = '&rfrshflag=1&m='+monthData+'&y='+yearData+'&LC_Id='+$("#bonusBranchItmF").val()+'&US_Name='+$("#bonususer").val();              
            dhxBonusReportGrid.clearAll();
            dhxBonusReportGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseBonusRpt.php"+rptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                if(dhxBonusReportGrid.doesRowExist("no_record"))
                    dhxBonusReportGrid.enableRowsHover(false);
                else
                    dhxBonusReportGrid.enableRowsHover(true,"bonusReportHover");
                
                preTally.BusinessBonusReport.calculateFooterValues(dhxBonusReportGrid);
            });
        },
        calculateFooterValues : function(grid){
            var columnSum   = grid.getUserData("", "colSum");
            var count       = 0;
            var arr         = [];
            if(columnSum) {
                var parsed  = JSON.parse(columnSum);
                for(var x in parsed){
                  arr.push(parsed[x]);
                }
            }
            for(i = 2; i < grid.getColumnsNum(); i++) {
                var footVal = (typeof arr[count] == "undefined") ? '0' : arr[count];
                grid.setFooterLabel((i-1),'<div id = "ft_'+i+'">'+footVal+'</div>');
                count++;
            }
        },
        exportBusinessOverviewReport : function(){ 
            var exportfilterValue = {};
            
            exportfilterValue['LCID']   = $("#ovBranchItmF").val();
            exportfilterValue['m']      = monthData;
            exportfilterValue['y']      = yearData;

            preTally.Settings.progressOn(true, dhxLayout, null);
            
            $.post(
                preTally.Initialize.encryptURL('warehouse/BusinessOverviewExcelExport.php'),
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