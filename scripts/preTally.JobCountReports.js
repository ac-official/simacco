;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var yearDataList=[];
    var monthData,yearData;
    preTally.JobCountReports = {
        viewJobCountReports : function(){
            if (!dhxMiddleBlockTabs.cells("view_jobCountReports")) {
                dhxMiddleBlockTabs.addTab("view_jobCountReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Location Based Job [Candidate] Count&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='jCkRefreshTab'/>", 420);
                dhxMiddleBlockTabs.tabs("view_jobCountReports").setActive();
                
                dhxJobCountReportsLayout   =  dhxMiddleBlockTabs.cells("view_jobCountReports").attachLayout("1C");
                JobCountReportToolbar      = dhxJobCountReportsLayout.cells("a").attachToolbar();                
                JobCountReportToolbar.setIconsPath("images/icon/default_18/");
                JobCountReportToolbar.setAlign('right');
                
                $(".jCkRefreshTab").click(function(){
                    preTally.JobCountReports.clearAllFlags();
                    var actvId = JobCountReportsTabbar.getActiveTab();
                    if(actvId == 'viewMonthwiseJobCountStateRpt')    preTally.JobCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseJobCountCityRpt')     preTally.JobCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseJobCountLocationRpt') preTally.JobCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseJobCountPlaceRpt')    preTally.JobCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseJobCountStreetRpt')   preTally.JobCountReports.viewMonthwiseStreetReport('','','','');
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "view_jobCountReports")
                        preTally.JobCountReports.clearAllFlags();
                    return true;
                });

                var Months_Options  = [];
                var Years_Options   = [];
                var currentMonth    = curMonth  = Date.today().getMonth()+1;
        
                if(currentMonth == 1)  // less than April
                    curMonth = 13;
                else if(currentMonth == 2)
                    curMonth = 14;
                else if(currentMonth == 3)
                    curMonth = 15;

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
                
                for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                    if(i == Date.today().getFullYear()) {
                        if( Date.today().getMonth()+1 > 3)
                            Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                    } else
                        Years_Options.push([i,'obj',i+"-"+(i+1),"calendar_Y.png"]);
                }
                
                JobCountReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                
                JobCountReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                JobCountReportToolbar.attachEvent("onClick", function(id){  
                    preTally.JobCountReports.clearAllFlags();
                    var pId     = JobCountReportToolbar.getParentId(id);
                    var actvId  = JobCountReportsTabbar.getActiveTab();

                    if(pId == 'rpt_month_filter') {
                        yearData        = '';
                        if(JobCountReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = JobCountReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        JobCountReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData       = '';
                        if(JobCountReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData    = JobCountReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        JobCountReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(actvId == 'viewMonthwiseJobCountStateRpt')    preTally.JobCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseJobCountCityRpt')     preTally.JobCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseJobCountLocationRpt') preTally.JobCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseJobCountPlaceRpt')    preTally.JobCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseJobCountStreetRpt')   preTally.JobCountReports.viewMonthwiseStreetReport('','','','');
                });
 
                var date    = new Date();
                var date        = new Date();
//                var currentYear = date.getFullYear();
//                var nextYear    = currentYear+1;
//                JobCountReportToolbar.setItemText('rpt_year_filter',currentYear+'-'+nextYear);
                monthData   = date.getMonth();
                JobCountReportToolbar.setItemText('rpt_month_filter',monthNames[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                JobCountReportsTabbar = dhxJobCountReportsLayout.cells("a").attachTabbar();
                JobCountReportsTabbar.addTab("viewMonthwiseJobCountStateRpt", "Statewise Report");
                JobCountReportsTabbar.addTab("viewMonthwiseJobCountCityRpt", "Citywise Report");
                JobCountReportsTabbar.addTab("viewMonthwiseJobCountLocationRpt", "Locationwise Report");
                JobCountReportsTabbar.addTab("viewMonthwiseJobCountPlaceRpt", "Placewise Report");
                JobCountReportsTabbar.addTab("viewMonthwiseJobCountStreetRpt", "Streetwise Report");
                
                JstateRptFlag = 0;
                preTally.JobCountReports.viewMonthwiseStateReport();
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountStateRpt").setActive();
                JobCountReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'viewMonthwiseJobCountStateRpt')    preTally.JobCountReports.viewMonthwiseStateReport();
                    if(id == 'viewMonthwiseJobCountCityRpt')     preTally.JobCountReports.viewMonthwiseCityReport('');
                    if(id == 'viewMonthwiseJobCountLocationRpt') preTally.JobCountReports.viewMonthwiseLocationReport('','');
                    if(id == 'viewMonthwiseJobCountPlaceRpt')    preTally.JobCountReports.viewMonthwisePlaceReport('','','');
                    if(id == 'viewMonthwiseJobCountStreetRpt')   preTally.JobCountReports.viewMonthwiseStreetReport('','','','');
                    return true;
                });
            } else {
                dhxMiddleBlockTabs.tabs("view_jobCountReports").setActive();
                var actvId = JobCountReportsTabbar.getActiveTab(); 
                if(actvId == 'viewMonthwiseJobCountStateRpt') preTally.JobCountReports.viewMonthwiseStateReport();
            }
        },
        clearAllFlags : function() {
            JstateRptFlag        = 0;  
            JcityRptFlag         = 0;
            JlocationFlag        = 0;
            JplaceRptFlag        = 0;
            JstreetRptFlag       = 0;
        },
        viewMonthwiseStateReport : function() {
            if(JstateRptFlag != 1){
                JstateRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountStateRpt").setActive();
                dhxJobCountStateReportLayout =  JobCountReportsTabbar.cells("viewMonthwiseJobCountStateRpt").attachLayout("1C");
                dhxJobCountStateReportLayout.cells("a").hideHeader();
                                                 
                dhxJobCountStateReportLayout.cells("a").setWidth('200');
                JStateReportsGrid = dhxJobCountStateReportLayout.cells("a").attachGrid();
                JStateReportsGrid.enableColSpan(true);
                
                JStateReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < JStateReportsGrid.getRowsNum(); i++){
                        rowID = JStateReportsGrid.getRowId(i);   
                        JStateReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                preTally.Settings.progressOn(true, dhxLayout, null);
               
                JStateReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCState.php&m="+monthData+"&y="+yearData), function() { 

                    JstateCombo = new dhtmlXCombo("JstateFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                        JstateCombo.load(data); 
                    });

                    preTally.Settings.progressOff(true, dhxLayout, null);

                    if(JStateReportsGrid.getRowsNum() == 0 ) {
                        JStateReportsGrid.addRow("row1",['No records found'],0); 
                        JStateReportsGrid.setColspan("row1",0,JStateReportsGrid.getColumnsNum());
                        JStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }  

                    JStateReportsGrid.attachEvent("onRowSelect",function(rowId,ind){
                        if(rowId != 'row1') {
                            JcityRptFlag = 0 ;
                            preTally.JobCountReports.viewMonthwiseCityReport(rowId);
                        }   else return false;
                    });

                    JstateCombo.setOptionWidth(280);
                    JstateCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"));
                    var combofiltrInterval;
                    $( "#JstateFlt" ).keyup(function(){
                        if(!JstateCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                JstateCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                    JstateCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });    
                    
                    JstateCombo.attachEvent("onChange", function() {
                        var JstateComboVal = JstateCombo.getSelectedValue();
                        if(!JstateCombo.getSelectedValue() && JstateCombo.getComboText()) JstateComboVal = JstateCombo.getComboText();
                        JstateCombo.setComboValue(JstateComboVal);
                        if(JstateCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                JstateCombo.load(data);
                            });
                        }

                        preTally.JobCountReports.applyMonthwiseStateFilter(JstateComboVal);
                    });

                    preTally.JobCountReports.calculateFooterValues(JStateReportsGrid);
                    if(JStateReportsGrid.doesRowExist("row1"))
                        JStateReportsGrid.enableRowsHover(false);
                    else
                        JStateReportsGrid.enableRowsHover(true,"bonusReportHover");

                    JStateReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        if(index != 0) {
                            var rowID = 0;
                            var i;
                            for (i = 0; i < JStateReportsGrid.getRowsNum(); i++){
                                rowID = JStateReportsGrid.getRowId(i);   
                                JStateReportsGrid.cells(rowID,0).setValue(i+1);
                            };
                        }
                        var sort = 'na';
                        for(i = 1; i < JStateReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        JStateReportsGrid.setColSorting(sort);
                    });

                    var srtFlg          = 0;
                    $('.tkSt_Sort_BIR').click(function() {
                        var colId       =   $(this).attr("colNum");
                        $('.tkSt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');

                        if(srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "asc";
                                JStateReportsGrid.setCustomSorting(preTally.JobCountReports.str_custom,1);
                                JStateReportsGrid.sortRows(0,"int","asc");
                            }   else {
                                JStateReportsGrid.sortRows(colId,"int","asc");
                            }
                            srtFlg      = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "desc";
                                JStateReportsGrid.setCustomSorting(preTally.JobCountReports.str_custom,1);
                                JStateReportsGrid.sortRows(0,"int","desc");
                            } else {
                                JStateReportsGrid.sortRows(colId,"int","desc");
                            } 
                            srtFlg      = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseStateFilter : function(ST_Id) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            if(typeof ST_Id == 'undefined' || ST_Id == 'null')
                ST_Id           = '';
            
            var rptFilterParams = '&m='+monthData+'&y='+yearData;      
            var filterValue     = new Array(ST_Id,1);            

            JStateReportsGrid.clearAll();
            JStateReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCState.php"+rptFilterParams+"&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);

                if(JStateReportsGrid.getRowsNum() == 0 ) {
                    JStateReportsGrid.addRow("row1",['No records found'],0); 
                    JStateReportsGrid.setColspan("row1",0,JStateReportsGrid.getColumnsNum());
                    JStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }  
                
                preTally.JobCountReports.calculateFooterValues(JStateReportsGrid);
                if(JStateReportsGrid.doesRowExist("row1"))
                    JStateReportsGrid.enableRowsHover(false);
                else
                    JStateReportsGrid.enableRowsHover(true,"bonusReportHover");
                
            });
        },
        viewMonthwiseCityReport : function(rptSt_Id) {
            if(JcityRptFlag != 1){
                var combofiltrInterval,cityFilInterval;
                JcityRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountCityRpt").setActive();
                dhxJobCountCityReportLayout =  JobCountReportsTabbar.cells("viewMonthwiseJobCountCityRpt").attachLayout("1C");
                dhxJobCountCityReportLayout.cells("a").hideHeader();
                                                 
                dhxJobCountCityReportLayout.cells("a").setWidth('200');
                JCityReportsGrid     = dhxJobCountCityReportLayout.cells("a").attachGrid();
                JCityReportsGrid.enableColSpan(true);
                notfButtonBar       = JobCountReportsTabbar.tabs("viewMonthwiseJobCountCityRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='city_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='cityPaging'></span></div>",
                    height: 35
                });
                
                //JCityReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                //JCityReportsGrid.enablePaging(true,50,5,'cityPaging',false);
               // JCityReportsGrid.setPagingSkin("toolbar");
                JCityReportsGrid.init();  
                JCityReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < JCityReportsGrid.getRowsNum(); i++){
                        rowID = JCityReportsGrid.getRowId(i);   
                        JCityReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxCityRptTlbr = dhxJobCountCityReportLayout.cells("a").attachToolbar();
                dhxCityRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxCityRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:500px;" id="JstateName"></div>' );
                dhxCityRptTlbr.setIconSize(32);

                JstateFilterCombo = new dhtmlXCombo("JstateName");

                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                    JstateFilterCombo.load(data);
                    if (!rptSt_Id) {
                        JstateFilterCombo.setComboValue(JstateFilterCombo.getSelectedValue());
                    } 
                });
                JstateFilterCombo.setOptionWidth(280);
                
                var combofiltrInterval;
                $("#JstateName").keyup(function() {
                    JstateFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                    if(!JstateFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            JstateFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                JstateFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });
                
                JstateFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateFilterCombo.getSelectedValue()), function( data ){
                        JcityCombo.load(data);
                    });
                    var JstateComboVal = JstateFilterCombo.getSelectedValue();
                    if(JstateComboVal == 0) {
                        $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                            JstateFilterCombo.load(data);
                        });
                    }
                    if(!JstateFilterCombo.getSelectedValue() && JstateFilterCombo.getComboText()) JstateComboVal = JstateFilterCombo.getComboText();
                       
                    $( "#JstateName" ).val(JstateComboVal);
                    JstateFilterCombo.setComboValue(JstateComboVal);

                    $("#JcityFlt").val(0);
                    preTally.JobCountReports.applyMonthwiseCityFilter(JstateFilterCombo.getSelectedValue(),'','');
                });

                var filterValue = new Array(monthData,yearData,rptSt_Id);
                JCityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCCity.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(JCityReportsGrid.getRowsNum() == 0 ) {
                        JCityReportsGrid.addRow("row1",['No records found'],0); 
                        JCityReportsGrid.setColspan("row1",0,JCityReportsGrid.getColumnsNum());
                        JCityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        JCityReportsGrid.enableRowsHover(false);
                    } else {
                        JCityReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.city_cnt_tot').html("# : "+JCityReportsGrid.getUserData("", "TL_Count")+" ");

                    JCityReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< JCityReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        JCityReportsGrid.setColSorting(sort);
                    });

                    JcityCombo = new dhtmlXCombo("JcityFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                        JcityCombo.load(data);
                    });

                    JcityCombo.setOptionWidth(350);
                    JcityCombo.attachEvent("onChange", function() {
                        $( "#JcityFlt" ).val(0);
                        var JcityComboVal = JcityCombo.getSelectedValue();
                        
                        if(JcityComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateFilterCombo.getSelectedValue()), function( data ){
                                JcityCombo.load(data);
                            });
                        }
                        if(!JcityCombo.getSelectedValue() && JcityCombo.getComboText()) JcityComboVal = JcityCombo.getComboText();
                       
                        $( "#JcityFlt" ).val(JcityComboVal);
                        JcityCombo.setComboValue(JcityComboVal);
                        preTally.JobCountReports.applyMonthwiseCityFilter(JstateFilterCombo.getSelectedValue(),'','');
                    });
                    
                    preTally.JobCountReports.calculateFooterValues(JCityReportsGrid);

                    $("#JcityFlt").keyup(function() {
                        JcityCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!JcityCombo.getComboText()) {
                            if(cityFilInterval) clearInterval(cityFilInterval);
                            cityFilInterval = setInterval( function() { 
                                JcityCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    JcityCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(cityFilInterval); 
                            }, 500);
                        }
                    });                   

                    JCityReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            JlocationFlag = 0;
                            rptCT_Id = rowId;
                            preTally.JobCountReports.viewMonthwiseLocationReport(rowId,JCityReportsGrid.getUserData(rowId, "ST_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkCt_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkCt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.JobCountReports.applyMonthwiseCityFilter(JstateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.JobCountReports.applyMonthwiseCityFilter(JstateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                    
                    preTally.JobCountReports.calculateFooterValues(JCityReportsGrid);
                });
            }
        },
        applyMonthwiseCityFilter : function(rptSt_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);

            if($("#JcityFlt").val() == 'undefined')  
                $("#JcityFlt").val(0);
      
            var filterValue = new Array(monthData,yearData,rptSt_Id,$("#JcityFlt").val(),'1',colId,srtFlg);

            JCityReportsGrid.clearAll();
            JCityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCCity.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.city_cnt_tot').html("# : "+JCityReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.JobCountReports.calculateFooterValues(JCityReportsGrid);
                if(JCityReportsGrid.getRowsNum() == 0 ) {
                    JCityReportsGrid.addRow("row1",['No records found'],0); 
                    JCityReportsGrid.setColspan("row1",0,JCityReportsGrid.getColumnsNum());
                    JCityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    JCityReportsGrid.enableRowsHover(false);
                } else {
                    JCityReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseLocationReport : function(rptCT_Id,rptST_Id) {
            if(JlocationFlag != 1){
                var combofiltrInterval,filtrInterval;
                JlocationFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountLocationRpt").setActive();
                dhxJobCountLocationReportLayout =  JobCountReportsTabbar.cells("viewMonthwiseJobCountLocationRpt").attachLayout("1C");
                dhxJobCountLocationReportLayout.cells("a").hideHeader();
                                                 
                dhxJobCountLocationReportLayout.cells("a").setWidth('200');
                JLocationReportsGrid = dhxJobCountLocationReportLayout.cells("a").attachGrid();
                JLocationReportsGrid.setImagePath("../../codebase/imgs/");
                JLocationReportsGrid.setSkin("dhx_skyblue");
                
                JLocationReportsGrid.enableColSpan(true);
                notfLocButtonBar = JobCountReportsTabbar.tabs("viewMonthwiseJobCountLocationRpt").attachStatusBar({
                    text  : "<div class='loc_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='loc_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='locPaging'></span></div>",
                    height: 35
                });
                
                //JLocationReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
               // JLocationReportsGrid.enablePaging(true,50,5,'locPaging',false);
               // JLocationReportsGrid.setPagingSkin("toolbar");
                
                JLocationReportsGrid.enableColSpan(true);
                JLocationReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < JLocationReportsGrid.getRowsNum(); i++){
                        rowID = JLocationReportsGrid.getRowId(i);   
                        JLocationReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxLocRptTlbr = dhxJobCountLocationReportLayout.cells("a").attachToolbar();
                dhxLocRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxLocRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:400px;" id="JstateLocName"></div>' );
                dhxLocRptTlbr.addText('masterRptToolbar', '2', 'City' );
                dhxLocRptTlbr.addText('masterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="JcityLocName"></div>' );
                dhxLocRptTlbr.setIconSize(32);

                JstateLocFilterCombo = new dhtmlXCombo("JstateLocName");
 
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    JstateLocFilterCombo.load(data); 
                });
                
                JstateLocFilterCombo.setOptionWidth(280);
                
                JstateLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);
                $("#JstateLocName").keyup(function() {

                    if(!JstateLocFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            JstateLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                JstateLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });        

                JstateLocFilterCombo.attachEvent("onChange", function() {
                    rptCT_Id = '';

                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+JstateLocFilterCombo.getSelectedValue()), function( data ){
                        JcityLocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php"), function( data ){
                        JlocCombo.load(data);
                    });
                    $("#JloctnFlt").val(0);

                    JcityLocFilterCombo.setComboValue(0);
                    if(JstateLocFilterCombo.getSelectedValue()) {
                        if(JstateLocFilterCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                JstateLocFilterCombo.load(data); 
                            }); 
                        }
                        preTally.JobCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                    }
                });
                
                JcityLocFilterCombo = new dhtmlXCombo("JcityLocName");
                JcityLocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    JcityLocFilterCombo.load(data);
                    if (!rptCT_Id) {
                        JcityLocFilterCombo.setComboValue(JcityLocFilterCombo.getSelectedValue());
                    } 
                });
                
                $("#JcityLocName").keyup(function() {
                    JcityLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                    if(!JcityLocFilterCombo.getComboText()) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            JcityLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                JcityLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176);
                                JcityLocFilterCombo.setComboValue('');
                            });
                            clearInterval(filtrInterval); 
                        }, 500);
                    }
                });       

                JcityLocFilterCombo.attachEvent("onChange", function() {
                    $( "#JloctnFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+JcityLocFilterCombo.getSelectedValue()), function( data ){
                        JlocCombo.load(data);
                    });

                    if(JcityLocFilterCombo.getSelectedValue()) {
                        if(JcityLocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateLocFilterCombo.getSelectedValue()), function( data ){
                                JcityLocFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwiseLocationFilter(JcityLocFilterCombo.getSelectedValue());
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptCT_Id,rptST_Id);

                JLocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCLoc.php&filter="+filterValue), function() { 
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    JlocCombo = new dhtmlXCombo("JloctnFlt");
                
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+rptST_Id+"&CT_Id="+rptCT_Id), function( data ){
                        JlocCombo.load(data);
                        JlocCombo.setOptionWidth(350);

                        JlocCombo.attachEvent("onChange", function() {
                            var JlocComboVal = JlocCombo.getSelectedValue();
                            if(JlocComboVal == 0)   {
                                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+JcityLocFilterCombo.getSelectedValue()), function( data ){
                                    JlocCombo.load(data);
                                });
                            }
                            if(!JlocCombo.getSelectedValue() && JlocCombo.getComboText()) JlocComboVal = JlocCombo.getComboText();
                            $( "#JloctnFlt" ).val(JlocComboVal);
                            JlocCombo.setComboValue(JlocComboVal);
                            
                            preTally.JobCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                        });

                        $("#JloctnFlt").keyup(function() {
                            JlocCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+JcityLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!JlocCombo.getComboText()) {
                                if(combofiltrInterval) clearInterval(combofiltrInterval);
                                combofiltrInterval = setInterval( function() { 
                                    JlocCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+JcityLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        JlocCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(combofiltrInterval); 
                                }, 500);
                            }
                        });     
                    });
              
                    if(JLocationReportsGrid.getRowsNum() == 0 ) {
                        JLocationReportsGrid.addRow("row1",['No records found'],0); 
                        JLocationReportsGrid.setColspan("row1",0,JLocationReportsGrid.getColumnsNum());
                        JLocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        JLocationReportsGrid.enableRowsHover(false);
                    } else {
                        JLocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    
                    $('.loc_cnt_tot').html("# : "+JLocationReportsGrid.getUserData("", "TL_Count")+" ");

                    JLocationReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< JLocationReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        JLocationReportsGrid.setColSorting(sort);
                    });
                    preTally.JobCountReports.calculateFooterValues(JLocationReportsGrid);
                    
                    JLocationReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            JplaceRptFlag = 0;
                            preTally.JobCountReports.viewMonthwisePlaceReport(rowId,JLocationReportsGrid.getUserData(rowId, "ST_Id"),JLocationReportsGrid.getUserData(rowId, "CT_Id"));
                        }   else return false;
                    });
                    
                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkLC_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkLC_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.JobCountReports.applyMonthwiseLocationFilter(JcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.JobCountReports.applyMonthwiseLocationFilter(JcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseLocationFilter : function(rptCT_Id,colId,srtFlg) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            if($("#JloctnFlt").val() == 'undefined')  
                $("#JloctnFlt").val(0);
            
            var filterValue = new Array(monthData,yearData,JcityLocFilterCombo.getSelectedValue(),JstateLocFilterCombo.getSelectedValue(),$("#JloctnFlt").val(),'1',colId,srtFlg);    
            JLocationReportsGrid.clearAll();
            JLocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCLoc.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.loc_cnt_tot').html("# : "+JLocationReportsGrid.getUserData("", "TL_Count")+" ");
                               
                preTally.JobCountReports.calculateFooterValues(JLocationReportsGrid);
                if(JLocationReportsGrid.getRowsNum() == 0 ) {
                    JLocationReportsGrid.addRow("row1",['No records found'],0); 
                    JLocationReportsGrid.setColspan("row1",0,JLocationReportsGrid.getColumnsNum());
                    JLocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    JLocationReportsGrid.enableRowsHover(false);
                } else {
                    JLocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                } 
            });
        },
        viewMonthwisePlaceReport : function(rowId,rptST_Id,rptCT_Id) {
            if(JplaceRptFlag != 1){
                var combofiltrInterval,JstateComboFiltInterval,JcityComboFiltInterval,JlocComboFiltInterval;
                JplaceRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountPlaceRpt").setActive();
                dhxJobCountPlaceReportLayout =  JobCountReportsTabbar.cells("viewMonthwiseJobCountPlaceRpt").attachLayout("1C");
                dhxJobCountPlaceReportLayout.cells("a").hideHeader();
                                                 
                dhxJobCountPlaceReportLayout.cells("a").setWidth('200');
                JplaceReportsGrid     = dhxJobCountPlaceReportLayout.cells("a").attachGrid();
                JplaceReportsGrid.enableColSpan(true);
                notfButtonBar       = JobCountReportsTabbar.tabs("viewMonthwiseJobCountPlaceRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='place_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='placePaging'></span></div>",
                    height: 35
                });
                
                //JplaceReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
               // JplaceReportsGrid.enablePaging(true,50,5,'placePaging',false);
               // JplaceReportsGrid.setPagingSkin("toolbar");
                JplaceReportsGrid.init();  

                dhxJobCountPlaceRptTlbr = dhxJobCountPlaceReportLayout.cells("a").attachToolbar();
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '0', 'State' );
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '1', '<div style="font-weight:bold;width:300px;" id="JstatePLName"></div>' );
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '2', 'City' );
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '3', '<div style="font-weight:bold;width:300px;" id="JcityPLName"></div>' );
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '4', 'Location' );
                dhxJobCountPlaceRptTlbr.addText('plRptToolbar', '5', '<div style="font-weight:bold;width:300px;" id="JlocPLName"></div>' );
                dhxJobCountPlaceRptTlbr.setIconSize(32);

                JstatePlaceFilterCombo = new dhtmlXCombo("JstatePLName");
                JstatePlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    JstatePlaceFilterCombo.load(data,function() {
                        JstatePlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                        $("#JstatePLName").keyup(function() {
                            if(!JstatePlaceFilterCombo.getComboText()) {
                                if(JstateComboFiltInterval) clearInterval(JstateComboFiltInterval);
                                JstateComboFiltInterval = setInterval( function() { 
                                    JstatePlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        JstatePlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(JstateComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    });
                });
                
                JcityPlaceFilterCombo = new dhtmlXCombo("JcityPLName");
                JcityPlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    JcityPlaceFilterCombo.load(data,function() {

                        $("#JcityPLName").keyup(function() {
                            JcityPlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!JcityPlaceFilterCombo.getComboText()) {
                                if(JcityComboFiltInterval) clearInterval(JcityComboFiltInterval);
                                JcityComboFiltInterval = setInterval( function() { 
                                    JcityPlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        JcityPlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(JcityComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    }); 
                });
                
                JlocFilterCombo = new dhtmlXCombo("JlocPLName");
                JlocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rowId), function( data ){
                    JlocFilterCombo.load(data,function() {
                        
                        $("#JlocPLName").keyup(function() {
                            JlocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&flag=1"),false);
                            if(!JlocFilterCombo.getComboText()) {
                                if(JlocComboFiltInterval) clearInterval(JlocComboFiltInterval);
                                JlocComboFiltInterval = setInterval( function() { 
                                    JlocFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        JlocFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(JlocComboFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                JstatePlaceFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        JcityPlaceFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        JlocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        JplaceCombo.load(data);
                    });
                    
                    $("#JplaceFlt").val(0);
                    
                    if(JstatePlaceFilterCombo.getSelectedValue()) {
                        if(JstatePlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                JstatePlaceFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),'','','','',''); 
                    }
                });
                
                JcityPlaceFilterCombo.attachEvent("onChange", function() {
                    $( "#JplaceFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        JlocFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        JplaceCombo.load(data);
                    });
                    
                    if(JcityPlaceFilterCombo.getSelectedValue()) {
                        if(JcityPlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()), function( data ){
                                JcityPlaceFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),JcityPlaceFilterCombo.getSelectedValue(),'','','','');
                    }
                });
               
                JlocFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+JlocFilterCombo.getSelectedValue()), function( data ){
                        JplaceCombo.load(data);
                    });
                    if(JlocFilterCombo.getSelectedValue()) {
                        if(JlocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+JlocFilterCombo.getSelectedValue()), function( data ){
                                JlocFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),JcityPlaceFilterCombo.getSelectedValue(),JlocFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rowId);

                JplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCPlace.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(JplaceReportsGrid.getRowsNum() == 0 ) {
                        JplaceReportsGrid.addRow("row1",['No records found'],0); 
                        JplaceReportsGrid.setColspan("row1",0,JplaceReportsGrid.getColumnsNum());
                        JplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        JplaceReportsGrid.enableRowsHover(false);
                    } else {
                        JplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.place_cnt_tot').html("# : "+JplaceReportsGrid.getUserData("", "TL_Count")+" ");

                    JplaceReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< JplaceReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        JplaceReportsGrid.setColSorting(sort);
                    });

                    JplaceCombo = new dhtmlXCombo("JplaceFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ALC_Id="+rowId), function( data ){
                        JplaceCombo.load(data);
                    });

                    JplaceCombo.setOptionWidth(350);
                    JplaceCombo.attachEvent("onChange", function() {
                        $( "#JplaceFlt" ).val(0);
                        var JplaceComboVal = JplaceCombo.getSelectedValue();
                        
                        if(JplaceComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php"), function( data ){
                                JplaceCombo.load(data);
                            });
                        }
                    
                        if(!JplaceCombo.getSelectedValue() && JplaceCombo.getComboText()) JplaceComboVal = JplaceCombo.getComboText();
                        $( "#JplaceFlt" ).val(JplaceComboVal);
                        JplaceCombo.setComboValue(JplaceComboVal);
                        preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),JcityPlaceFilterCombo.getSelectedValue(),JlocFilterCombo.getSelectedValue(),$("#JplaceFlt").val(),'','');
                    });
                    preTally.JobCountReports.calculateFooterValues(JplaceReportsGrid);
  
                    $("#JplaceFlt").keyup(function() {
                        JplaceCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+JlocFilterCombo.getSelectedValue()+"&flag=1"),false);
                        if(!JplaceCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                JplaceCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+JcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+JlocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    JplaceCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });  
  
                    JplaceReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            JstreetRptFlag = 0;
                            preTally.JobCountReports.viewMonthwiseStreetReport(rowId,JplaceReportsGrid.getUserData(rowId, "ST_Id"),JplaceReportsGrid.getUserData(rowId, "CT_Id"),JplaceReportsGrid.getUserData(rowId, "ALC_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkPL_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkPL_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),JcityPlaceFilterCombo.getSelectedValue(),JlocFilterCombo.getSelectedValue(),JplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.JobCountReports.applyMonthwisePlaceFilter(JstatePlaceFilterCombo.getSelectedValue(),JcityPlaceFilterCombo.getSelectedValue(),JlocFilterCombo.getSelectedValue(),JplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwisePlaceFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,'1',colId,srtFlg);
            JplaceReportsGrid.clearAll();
            JplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCPlace.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.place_cnt_tot').html("# : "+JplaceReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.JobCountReports.calculateFooterValues(JplaceReportsGrid);
                if(JplaceReportsGrid.getRowsNum() == 0 ) {
                    JplaceReportsGrid.addRow("row1",['No records found'],0); 
                    JplaceReportsGrid.setColspan("row1",0,JplaceReportsGrid.getColumnsNum());
                    JplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    JplaceReportsGrid.enableRowsHover(false);
                } else {
                    JplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseStreetReport : function(rowId,rptST_Id,rptCT_Id,rptACL_Id) {
            if(JstreetRptFlag != 1){
                var stateFiltInterval,cityFiltInterval,locFiltInterval,placeFiltInterval,streetFiltInterval;
                JstreetRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountStreetRpt").setActive();
                dhxJobCountStreetReportLayout =  JobCountReportsTabbar.cells("viewMonthwiseJobCountStreetRpt").attachLayout("1C");
                dhxJobCountStreetReportLayout.cells("a").hideHeader();
                                                 
                dhxJobCountStreetReportLayout.cells("a").setWidth('200');
                JstreetReportsGrid     = dhxJobCountStreetReportLayout.cells("a").attachGrid();
                JstreetReportsGrid.enableColSpan(true);
                JobCountReportsTabbar.tabs("viewMonthwiseJobCountStreetRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='street_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='streetPaging'></span></div>",
                    height: 35
                });
                
               // JstreetReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
               // JstreetReportsGrid.enablePaging(true,50,5,'streetPaging',false);
               // JstreetReportsGrid.setPagingSkin("toolbar");
                JstreetReportsGrid.init();  
           
                dhxStreetRptTlbr = dhxJobCountStreetReportLayout.cells("a").attachToolbar();
                dhxStreetRptTlbr.addText('stRptToolbar', '0', 'State' );
                dhxStreetRptTlbr.addText('stRptToolbar', '1', '<div style="font-weight:bold;width:230px;" id="JstateSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '2', 'City' );
                dhxStreetRptTlbr.addText('stRptToolbar', '3', '<div style="font-weight:bold;width:230px;" id="JcitySTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '4', 'Location' );
                dhxStreetRptTlbr.addText('stRptToolbar', '5', '<div style="font-weight:bold;width:230px;" id="JlocSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '6', 'Place' );
                dhxStreetRptTlbr.addText('stRptToolbar', '7', '<div style="font-weight:bold;width:230px;" id="JplaceSTName"></div>' );
                dhxStreetRptTlbr.setIconSize(32);

                JstateStreetFilterCombo = new dhtmlXCombo("JstateSTName");
                JstateStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    JstateStreetFilterCombo.load(data,function() {

                        $("#JstateSTName").keyup(function() {
                            JstateStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                            if(!JstateStreetFilterCombo.getComboText()) {
                                if(stateFiltInterval) clearInterval(stateFiltInterval);
                                stateFiltInterval = setInterval( function() { 
                                    JstateStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        JstateStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(stateFiltInterval); 
                                }, 500);
                            }
                        });   
                    });
                });
                
                JcityStreetFilterCombo = new dhtmlXCombo("JcitySTName");
                JcityStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    JcityStreetFilterCombo.load(data,function() {

                        $("#JcitySTName").keyup(function() {
                            JcityStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!JcityStreetFilterCombo.getComboText()) {
                                if(cityFiltInterval) clearInterval(cityFiltInterval);
                                cityFiltInterval = setInterval( function() { 
                                    JcityStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        JcityStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(cityFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                JlocStreetFilterCombo = new dhtmlXCombo("JlocSTName");
                JlocStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id), function( data ){
                    JlocStreetFilterCombo.load(data,function() {
                    
                        $("#JlocSTName").keyup(function() {
                            JlocStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!JlocStreetFilterCombo.getComboText()) {
                                if(locFiltInterval) clearInterval(placeFiltInterval);
                                locFiltInterval = setInterval( function() { 
                                JlocStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        JlocStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(locFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                JplaceStreetFilterCombo = new dhtmlXCombo("JplaceSTName");
                JplaceStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id+"&PL_Id="+rowId), function( data ){
                    JplaceStreetFilterCombo.load(data,function() {
                   
                        $("#JplaceSTName").keyup(function() {
                           JplaceStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                           if(!JplaceStreetFilterCombo.getComboText()) {
                               if(placeFiltInterval) clearInterval(placeFiltInterval);
                               placeFiltInterval = setInterval( function() { 
                               JplaceStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                       JplaceStreetFilterCombo.openSelect();
                                       $(".dhxcombolist_dhx_skyblue").height(176);
                                   });
                                   clearInterval(placeFiltInterval); 
                               }, 500);
                           }
                       });  
                    }); 
                });
                
                JstateStreetFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateStreetFilterCombo.getSelectedValue()), function( data ){
                        JcityStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()), function( data ){
                        JlocStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()), function( data ){
                        JplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()), function( data ){
                        JstreetCombo.load(data);
                    });
                    
                    $("#JstreetFlt").val(0);
                    
                    if(JstateStreetFilterCombo.getSelectedValue()) {
                        if(JstateStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                JstateStreetFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),'','','','','',''); 
                    }
                });
                
                JcityStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()), function( data ){
                        JlocStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()), function( data ){
                        JplaceStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()), function( data ){
                        JstreetCombo.load(data);
                    });
                    $("#JstreetFlt").val(0);
                    if(JcityStreetFilterCombo.getSelectedValue()) {
                        if(JcityStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+JstateStreetFilterCombo.getSelectedValue()), function( data ){
                                JcityStreetFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),'','','','','');
                    }
                });
               
                JlocStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()), function( data ){
                        JplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()), function( data ){
                        JstreetCombo.load(data);
                    });
                    $("#JstreetFlt").val(0);
                    if(JlocStreetFilterCombo.getSelectedValue()) {
                        if(JlocStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()), function( data ){
                                JlocStreetFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),JlocStreetFilterCombo.getSelectedValue(),'','','','');
                    }
                });
                
                JplaceStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&PL_Id="+JplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        JstreetCombo.load(data);
                    });
                    $("#JstreetFlt").val(0);
                    if(JplaceStreetFilterCombo.getSelectedValue()) {
                        if(JplaceStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()), function( data ){
                                JplaceStreetFilterCombo.load(data);
                            });
                        }
                        preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),JlocStreetFilterCombo.getSelectedValue(),JplaceStreetFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptACL_Id,rowId);

                JstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCStreet.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(JstreetReportsGrid.getRowsNum() == 0 ) {
                        JstreetReportsGrid.addRow("row1",['No records found'],0); 
                        JstreetReportsGrid.setColspan("row1",0,JstreetReportsGrid.getColumnsNum());
                        JstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        JstreetReportsGrid.enableRowsHover(false);
                    } else {
                        JstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.street_cnt_tot').html("# : "+JstreetReportsGrid.getUserData("", "TL_Count")+" ");

                    JstreetReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i < JstreetReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        JstreetReportsGrid.setColSorting(sort);
                    });

                    JstreetCombo = new dhtmlXCombo("JstreetFlt");
                    JstreetCombo.setOptionWidth(350);
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&PL_Id="+JplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        JstreetCombo.load(data);
                    });

                    JstreetCombo.attachEvent("onChange", function() {
                        $( "#JstreetFlt" ).val(0);
                        var JstreetComboVal = JstreetCombo.getSelectedValue();
                        if(JstreetComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&PL_Id="+JplaceStreetFilterCombo.getSelectedValue()), function( data ){
                                JstreetCombo.load(data);
                            });
                        }
                        if(!JstreetCombo.getSelectedValue() && JstreetCombo.getComboText()) JstreetComboVal = JstreetCombo.getComboText();
                        $( "#JstreetFlt" ).val(JstreetComboVal);
                        JstreetCombo.setComboValue(JstreetComboVal);
                        preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),JlocStreetFilterCombo.getSelectedValue(),JplaceStreetFilterCombo.getSelectedValue(),$("#JstreetFlt").val(),'','');
                    });
                    
                    preTally.JobCountReports.calculateFooterValues(JstreetReportsGrid);
               
                    $("#JstreetFlt").keyup(function() {
                        JstreetCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&PL_Id="+JplaceStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!JstreetCombo.getComboText()) {
                            if(streetFiltInterval) clearInterval(streetFiltInterval);
                            streetFiltInterval = setInterval( function() { 
                            JstreetCombo.load(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+JstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+JcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+JlocStreetFilterCombo.getSelectedValue()+"&PL_Id="+JplaceStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    JstreetCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(streetFiltInterval); 
                            }, 500);
                        }
                    });  

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkSTR_Sort_BIR').click(function() {
                        colId   = $(this).attr("colNum");
                        $('.tkSTR_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),JlocStreetFilterCombo.getSelectedValue(),JplaceStreetFilterCombo.getSelectedValue(),$("#JstreetFlt").val(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.JobCountReports.applyMonthwiseStreetFilter(JstateStreetFilterCombo.getSelectedValue(),JcityStreetFilterCombo.getSelectedValue(),JlocStreetFilterCombo.getSelectedValue(),JplaceStreetFilterCombo.getSelectedValue(),$("#JstreetFlt").val(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseStreetFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,'1',colId,srtFlg);

            JstreetReportsGrid.clearAll();
            JstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseJCStreet.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.street_cnt_tot').html("# : "+JstreetReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.JobCountReports.calculateFooterValues(JstreetReportsGrid);
                if(JstreetReportsGrid.getRowsNum() == 0 ) {
                    JstreetReportsGrid.addRow("row1",['No records found'],0); 
                    JstreetReportsGrid.setColspan("row1",0,JstreetReportsGrid.getColumnsNum());
                    JstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    JstreetReportsGrid.enableRowsHover(false);
                } else {
                    JstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
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
        str_custom : function(a,b,ord){
            return (a.toLowerCase() > b.toLowerCase() ? 1 : -1)*(order=="asc" ? 1 : -1);
        },
    };
})(jQuery, this);