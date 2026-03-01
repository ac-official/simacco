;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthData,yearData;    
    preTally.TrackReports = {
        viewStateWiseReports : function(){
            yearData        = Date.today().getFullYear();
            if (!dhxMiddleBlockTabs.cells("view_trackReports")) {
                dhxMiddleBlockTabs.addTab("view_trackReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Location Based Job Amount&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='tkRefreshTab'/>", 350);
                dhxMiddleBlockTabs.tabs("view_trackReports").setActive();
                
                dhxTrackReportsLayout   =  dhxMiddleBlockTabs.cells("view_trackReports").attachLayout("1C");
                TrackReportToolbar      = dhxTrackReportsLayout.cells("a").attachToolbar();                
                TrackReportToolbar.setIconsPath("images/icon/default_18/");
                TrackReportToolbar.setAlign('right');
                
                $(".tkRefreshTab").click(function(){
                    preTally.TrackReports.clearAllFlags();
                    var actvId = TrackReportsTabbar.getActiveTab();
                    if(actvId == 'viewMonthwiseStateRpt')    preTally.TrackReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseCityRpt')     preTally.TrackReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseLocationRpt') preTally.TrackReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwisePlaceRpt')    preTally.TrackReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseStreetRpt')   preTally.TrackReports.viewMonthwiseStreetReport('','','','');
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "view_trackReports")
                        preTally.TrackReports.clearAllFlags();
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
                
                TrackReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                TrackReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                TrackReportToolbar.attachEvent("onClick", function(id){  
                    preTally.TrackReports.clearAllFlags();
                    var pId     = TrackReportToolbar.getParentId(id);
                    var actvId  = TrackReportsTabbar.getActiveTab();

                    if(pId == 'rpt_month_filter') {
                        yearData        = '';
                        if(TrackReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = TrackReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        TrackReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData       = '';
                        if(TrackReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData    = TrackReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        TrackReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(actvId == 'viewMonthwiseStateRpt')    preTally.TrackReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseCityRpt')     preTally.TrackReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseLocationRpt') preTally.TrackReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwisePlaceRpt')    preTally.TrackReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseStreetRpt')   preTally.TrackReports.viewMonthwiseStreetReport('','','','');
                });
 
                var date    = new Date();
                monthData   = date.getMonth();
                TrackReportToolbar.setItemText('rpt_month_filter',monthNames[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                
                TrackReportsTabbar = dhxTrackReportsLayout.cells("a").attachTabbar();
                TrackReportsTabbar.addTab("viewMonthwiseStateRpt", "Statewise Report");
                TrackReportsTabbar.addTab("viewMonthwiseCityRpt", "Citywise Report");
                TrackReportsTabbar.addTab("viewMonthwiseLocationRpt", "Locationwise Report");
                TrackReportsTabbar.addTab("viewMonthwisePlaceRpt", "Placewise Report");
                TrackReportsTabbar.addTab("viewMonthwiseStreetRpt", "Streetwise Report");
                
                stateRptFlag = 0;
                preTally.TrackReports.viewMonthwiseStateReport();
                
                TrackReportsTabbar.tabs("viewMonthwiseStateRpt").setActive();
                TrackReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'viewMonthwiseStateRpt')    preTally.TrackReports.viewMonthwiseStateReport();
                    if(id == 'viewMonthwiseCityRpt')     preTally.TrackReports.viewMonthwiseCityReport('');
                    if(id == 'viewMonthwiseLocationRpt') preTally.TrackReports.viewMonthwiseLocationReport('','');
                    if(id == 'viewMonthwisePlaceRpt')    preTally.TrackReports.viewMonthwisePlaceReport('','','');
                    if(id == 'viewMonthwiseStreetRpt')   preTally.TrackReports.viewMonthwiseStreetReport('','','','');
                    return true;
                });
            } else {
                dhxMiddleBlockTabs.tabs("view_trackReports").setActive();
                var actvId = TrackReportsTabbar.getActiveTab(); 
                if(actvId == 'viewMonthwiseStateRpt') preTally.TrackReports.viewMonthwiseStateReport();
            }
        },
        clearAllFlags : function() {
            stateRptFlag        = 0;  
            cityRptFlag         = 0;
            locationFlag        = 0;
            placeRptFlag        = 0;
            streetRptFlag       = 0;
        },
        viewMonthwiseStateReport : function() {
            if(stateRptFlag != 1){
                stateRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                TrackReportsTabbar.tabs("viewMonthwiseStateRpt").setActive();
                dhxStateReportLayout =  TrackReportsTabbar.cells("viewMonthwiseStateRpt").attachLayout("1C");
                dhxStateReportLayout.cells("a").hideHeader();
                                                 
                dhxStateReportLayout.cells("a").setWidth('200');
                StateReportsGrid = dhxStateReportLayout.cells("a").attachGrid();
                StateReportsGrid.enableColSpan(true);
                
                StateReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < StateReportsGrid.getRowsNum(); i++){
                        rowID = StateReportsGrid.getRowId(i);   
                        StateReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                preTally.Settings.progressOn(true, dhxLayout, null);
               
                StateReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKState.php&m="+monthData+"&y="+yearData), function() { 

                    stateCombo = new dhtmlXCombo("stateFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                        stateCombo.load(data); 
                    });

                    preTally.Settings.progressOff(true, dhxLayout, null);

                    if(StateReportsGrid.getRowsNum() == 0 ) {
                        StateReportsGrid.addRow("row1",['No records found'],0); 
                        StateReportsGrid.setColspan("row1",0,StateReportsGrid.getColumnsNum());
                        StateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }  

                    StateReportsGrid.attachEvent("onRowSelect",function(rowId,ind){
                        if(rowId != 'row1') {
                            cityRptFlag = 0 ;
                            preTally.TrackReports.viewMonthwiseCityReport(rowId);
                        }   else return false;
                    });

                    stateCombo.setOptionWidth(280);
                    stateCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"));
                    var combofiltrInterval;
                    $( "#stateFlt" ).keyup(function(){
                        if(!stateCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                stateCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                    stateCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });    
                    
                    stateCombo.attachEvent("onChange", function() {
                        var stateComboVal = stateCombo.getSelectedValue();
                        if(!stateCombo.getSelectedValue() && stateCombo.getComboText()) stateComboVal = stateCombo.getComboText();
                        stateCombo.setComboValue(stateComboVal);
                        if(stateCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                stateCombo.load(data);
                            });
                        }

                        preTally.TrackReports.applyMonthwiseStateFilter(stateComboVal);
                    });

                    preTally.TrackReports.calculateFooterValues(StateReportsGrid);
                    if(StateReportsGrid.doesRowExist("row1"))
                        StateReportsGrid.enableRowsHover(false);
                    else
                        StateReportsGrid.enableRowsHover(true,"bonusReportHover");

                    StateReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        if(index != 0) {
                            var rowID = 0;
                            var i;
                            for (i = 0; i < StateReportsGrid.getRowsNum(); i++){
                                rowID = StateReportsGrid.getRowId(i);   
                                StateReportsGrid.cells(rowID,0).setValue(i+1);
                            };
                        }
                        var sort = 'na';
                        for(i = 1; i < StateReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        StateReportsGrid.setColSorting(sort);
                    });

                    var srtFlg          = 0;
                    $('.tkSt_Sort_BIR').click(function() {
                        var colId       =   $(this).attr("colNum");
                        $('.tkSt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');

                        if(srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "asc";
                                StateReportsGrid.setCustomSorting(preTally.TrackReports.str_custom,1);
                                StateReportsGrid.sortRows(0,"int","asc");
                            }   else {
                                StateReportsGrid.sortRows(colId,"int","asc");
                            }
                            srtFlg      = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "desc";
                                StateReportsGrid.setCustomSorting(preTally.TrackReports.str_custom,1);
                                StateReportsGrid.sortRows(0,"int","desc");
                            } else {
                                StateReportsGrid.sortRows(colId,"int","desc");
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

            StateReportsGrid.clearAll();
            StateReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKState.php"+rptFilterParams+"&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);

                if(StateReportsGrid.getRowsNum() == 0 ) {
                    StateReportsGrid.addRow("row1",['No records found'],0); 
                    StateReportsGrid.setColspan("row1",0,StateReportsGrid.getColumnsNum());
                    StateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }  
                
                preTally.TrackReports.calculateFooterValues(StateReportsGrid);
                if(StateReportsGrid.doesRowExist("row1"))
                    StateReportsGrid.enableRowsHover(false);
                else
                    StateReportsGrid.enableRowsHover(true,"bonusReportHover");
                
            });
        },
        viewMonthwiseCityReport : function(rptSt_Id) {
            if(cityRptFlag != 1){
                var combofiltrInterval,cityFilInterval;
                cityRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                TrackReportsTabbar.tabs("viewMonthwiseCityRpt").setActive();
                dhxCityReportLayout =  TrackReportsTabbar.cells("viewMonthwiseCityRpt").attachLayout("1C");
                dhxCityReportLayout.cells("a").hideHeader();
                                                 
                dhxCityReportLayout.cells("a").setWidth('200');
                CityReportsGrid     = dhxCityReportLayout.cells("a").attachGrid();
                CityReportsGrid.enableColSpan(true);
                notfButtonBar       = TrackReportsTabbar.tabs("viewMonthwiseCityRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='city_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='cityPaging'></span></div>",
                    height: 35
                });
                
                CityReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                CityReportsGrid.enablePaging(true,50,5,'cityPaging',false);
                CityReportsGrid.setPagingSkin("toolbar");
                CityReportsGrid.init();  
                CityReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < CityReportsGrid.getRowsNum(); i++){
                        rowID = CityReportsGrid.getRowId(i);   
                        CityReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxCityRptTlbr = dhxCityReportLayout.cells("a").attachToolbar();
                dhxCityRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxCityRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:500px;" id="stateName"></div>' );
                dhxCityRptTlbr.setIconSize(32);

                stateFilterCombo = new dhtmlXCombo("stateName");

                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                    stateFilterCombo.load(data);
                    if (!rptSt_Id) {
                        stateFilterCombo.setComboValue(stateFilterCombo.getSelectedValue());
                    } 
                });
                stateFilterCombo.setOptionWidth(280);
                
                var combofiltrInterval;
                $("#stateName").keyup(function() {
                    stateFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                    if(!stateFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            stateFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                stateFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });
                
                stateFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateFilterCombo.getSelectedValue()), function( data ){
                        cityCombo.load(data);
                    });
                    var stateComboVal = stateFilterCombo.getSelectedValue();
                    if(stateComboVal == 0) {
                        $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                            stateFilterCombo.load(data);
                        });
                    }
                    if(!stateFilterCombo.getSelectedValue() && stateFilterCombo.getComboText()) stateComboVal = stateFilterCombo.getComboText();
                       
                    $( "#stateName" ).val(stateComboVal);
                    stateFilterCombo.setComboValue(stateComboVal);

                    $("#cityFlt").val(0);
                    preTally.TrackReports.applyMonthwiseCityFilter(stateFilterCombo.getSelectedValue(),'','');
                });

                var filterValue = new Array(monthData,yearData,rptSt_Id);
                CityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKCity.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(CityReportsGrid.getRowsNum() == 0 ) {
                        CityReportsGrid.addRow("row1",['No records found'],0); 
                        CityReportsGrid.setColspan("row1",0,CityReportsGrid.getColumnsNum());
                        CityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        CityReportsGrid.enableRowsHover(false);
                    } else {
                        CityReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.city_cnt_tot').html("# : "+CityReportsGrid.getUserData("", "TL_Count")+" ");

                    CityReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< CityReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CityReportsGrid.setColSorting(sort);
                    });

                    cityCombo = new dhtmlXCombo("cityFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                        cityCombo.load(data);
                    });

                    cityCombo.setOptionWidth(350);
                    cityCombo.attachEvent("onChange", function() {
                        $( "#cityFlt" ).val(0);
                        var cityComboVal = cityCombo.getSelectedValue();
                        
                        if(cityComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateFilterCombo.getSelectedValue()), function( data ){
                                cityCombo.load(data);
                            });
                        }
                        if(!cityCombo.getSelectedValue() && cityCombo.getComboText()) cityComboVal = cityCombo.getComboText();
                       
                        $( "#cityFlt" ).val(cityComboVal);
                        cityCombo.setComboValue(cityComboVal);
                        preTally.TrackReports.applyMonthwiseCityFilter(stateFilterCombo.getSelectedValue(),'','');
                    });
                    
                    preTally.TrackReports.calculateFooterValues(CityReportsGrid);

                    $("#cityFlt").keyup(function() {
                        cityCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!cityCombo.getComboText()) {
                            if(cityFilInterval) clearInterval(cityFilInterval);
                            cityFilInterval = setInterval( function() { 
                                cityCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    cityCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(cityFilInterval); 
                            }, 500);
                        }
                    });                   

                    CityReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            locationFlag = 0;
                            rptCT_Id = rowId;
                            preTally.TrackReports.viewMonthwiseLocationReport(rowId,CityReportsGrid.getUserData(rowId, "ST_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkCt_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkCt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.TrackReports.applyMonthwiseCityFilter(stateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.TrackReports.applyMonthwiseCityFilter(stateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseCityFilter : function(rptSt_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);

            if($("#cityFlt").val() == 'undefined')  
                $("#cityFlt").val(0);
      
            var filterValue = new Array(monthData,yearData,rptSt_Id,$("#cityFlt").val(),'1',colId,srtFlg);

            CityReportsGrid.clearAll();
            CityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKCity.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.city_cnt_tot').html("# : "+CityReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.TrackReports.calculateFooterValues(CityReportsGrid);
                if(CityReportsGrid.getRowsNum() == 0 ) {
                    CityReportsGrid.addRow("row1",['No records found'],0); 
                    CityReportsGrid.setColspan("row1",0,CityReportsGrid.getColumnsNum());
                    CityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    CityReportsGrid.enableRowsHover(false);
                } else {
                    CityReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseLocationReport : function(rptCT_Id,rptST_Id) {
            if(locationFlag != 1){
                var combofiltrInterval,filtrInterval;
                locationFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                TrackReportsTabbar.tabs("viewMonthwiseLocationRpt").setActive();
                dhxLocationReportLayout =  TrackReportsTabbar.cells("viewMonthwiseLocationRpt").attachLayout("1C");
                dhxLocationReportLayout.cells("a").hideHeader();
                                                 
                dhxLocationReportLayout.cells("a").setWidth('200');
                LocationReportsGrid = dhxLocationReportLayout.cells("a").attachGrid();
                LocationReportsGrid.setImagePath("../../codebase/imgs/");
                LocationReportsGrid.setSkin("dhx_skyblue");
                
                LocationReportsGrid.enableColSpan(true);
                notfLocButtonBar = TrackReportsTabbar.tabs("viewMonthwiseLocationRpt").attachStatusBar({
                    text  : "<div class='loc_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='loc_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='locPaging'></span></div>",
                    height: 35
                });
                
                LocationReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                LocationReportsGrid.enablePaging(true,50,5,'locPaging',false);
                LocationReportsGrid.setPagingSkin("toolbar");
                
                LocationReportsGrid.enableColSpan(true);
                LocationReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < LocationReportsGrid.getRowsNum(); i++){
                        rowID = LocationReportsGrid.getRowId(i);   
                        LocationReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxLocRptTlbr = dhxLocationReportLayout.cells("a").attachToolbar();
                dhxLocRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxLocRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:400px;" id="stateLocName"></div>' );
                dhxLocRptTlbr.addText('masterRptToolbar', '2', 'City' );
                dhxLocRptTlbr.addText('masterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="cityLocName"></div>' );
                dhxLocRptTlbr.setIconSize(32);

                stateLocFilterCombo = new dhtmlXCombo("stateLocName");
 
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    stateLocFilterCombo.load(data); 
                });
                
                stateLocFilterCombo.setOptionWidth(280);
                
                stateLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);
                $("#stateLocName").keyup(function() {

                    if(!stateLocFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            stateLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                stateLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });        

                stateLocFilterCombo.attachEvent("onChange", function() {
                    rptCT_Id = '';

                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+stateLocFilterCombo.getSelectedValue()), function( data ){
                        cityLocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php"), function( data ){
                        locCombo.load(data);
                    });
                    $("#loctnFlt").val(0);

                    cityLocFilterCombo.setComboValue(0);
                    if(stateLocFilterCombo.getSelectedValue()) {
                        if(stateLocFilterCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                stateLocFilterCombo.load(data); 
                            }); 
                        }
                        preTally.TrackReports.applyMonthwiseLocationFilter(rptCT_Id);
                    }
                });
                
                cityLocFilterCombo = new dhtmlXCombo("cityLocName");
                cityLocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    cityLocFilterCombo.load(data);
                    if (!rptCT_Id) {
                        cityLocFilterCombo.setComboValue(cityLocFilterCombo.getSelectedValue());
                    } 
                });
                
                $("#cityLocName").keyup(function() {
                    cityLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                    if(!cityLocFilterCombo.getComboText()) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            cityLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                cityLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176);
                                cityLocFilterCombo.setComboValue('');
                            });
                            clearInterval(filtrInterval); 
                        }, 500);
                    }
                });       

                cityLocFilterCombo.attachEvent("onChange", function() {
                    $( "#loctnFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+cityLocFilterCombo.getSelectedValue()), function( data ){
                        locCombo.load(data);
                    });

                    if(cityLocFilterCombo.getSelectedValue()) {
                        if(cityLocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateLocFilterCombo.getSelectedValue()), function( data ){
                                cityLocFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwiseLocationFilter(cityLocFilterCombo.getSelectedValue());
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptCT_Id,rptST_Id);

                LocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKLoc.php&filter="+filterValue), function() { 
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    locCombo = new dhtmlXCombo("loctnFlt");
                
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+rptST_Id+"&CT_Id="+rptCT_Id), function( data ){
                        locCombo.load(data);
                        locCombo.setOptionWidth(350);

                        locCombo.attachEvent("onChange", function() {
                            var locComboVal = locCombo.getSelectedValue();
                            if(locComboVal == 0)   {
                                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+cityLocFilterCombo.getSelectedValue()), function( data ){
                                    locCombo.load(data);
                                });
                            }
                            if(!locCombo.getSelectedValue() && locCombo.getComboText()) locComboVal = locCombo.getComboText();
                            $( "#loctnFlt" ).val(locComboVal);
                            locCombo.setComboValue(locComboVal);
                            
                            preTally.TrackReports.applyMonthwiseLocationFilter(rptCT_Id);
                        });

                        $("#loctnFlt").keyup(function() {
                            locCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+cityLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!locCombo.getComboText()) {
                                if(combofiltrInterval) clearInterval(combofiltrInterval);
                                combofiltrInterval = setInterval( function() { 
                                    locCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+cityLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        locCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(combofiltrInterval); 
                                }, 500);
                            }
                        });     
                    });
              
                    if(LocationReportsGrid.getRowsNum() == 0 ) {
                        LocationReportsGrid.addRow("row1",['No records found'],0); 
                        LocationReportsGrid.setColspan("row1",0,LocationReportsGrid.getColumnsNum());
                        LocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        LocationReportsGrid.enableRowsHover(false);
                    } else {
                        LocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    
                    $('.loc_cnt_tot').html("# : "+LocationReportsGrid.getUserData("", "TL_Count")+" ");

                    LocationReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< LocationReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        LocationReportsGrid.setColSorting(sort);
                    });
                    preTally.TrackReports.calculateFooterValues(LocationReportsGrid);
                    
                    LocationReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            placeRptFlag = 0;
                            preTally.TrackReports.viewMonthwisePlaceReport(rowId,LocationReportsGrid.getUserData(rowId, "ST_Id"),LocationReportsGrid.getUserData(rowId, "CT_Id"));
                        }   else return false;
                    });
                    
                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkLC_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkLC_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.TrackReports.applyMonthwiseLocationFilter(cityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.TrackReports.applyMonthwiseLocationFilter(cityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseLocationFilter : function(rptCT_Id,colId,srtFlg) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            if($("#loctnFlt").val() == 'undefined')  
                $("#loctnFlt").val(0);
            
            var filterValue = new Array(monthData,yearData,cityLocFilterCombo.getSelectedValue(),stateLocFilterCombo.getSelectedValue(),$("#loctnFlt").val(),'1',colId,srtFlg);    
            LocationReportsGrid.clearAll();
            LocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKLoc.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.loc_cnt_tot').html("# : "+LocationReportsGrid.getUserData("", "TL_Count")+" ");
                               
                preTally.TrackReports.calculateFooterValues(LocationReportsGrid);
                if(LocationReportsGrid.getRowsNum() == 0 ) {
                    LocationReportsGrid.addRow("row1",['No records found'],0); 
                    LocationReportsGrid.setColspan("row1",0,LocationReportsGrid.getColumnsNum());
                    LocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    LocationReportsGrid.enableRowsHover(false);
                } else {
                    LocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                } 
            });
        },
        viewMonthwisePlaceReport : function(rowId,rptST_Id,rptCT_Id) {
            if(placeRptFlag != 1){
                var combofiltrInterval,stateComboFiltInterval,cityComboFiltInterval,locComboFiltInterval;
                placeRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                TrackReportsTabbar.tabs("viewMonthwisePlaceRpt").setActive();
                dhxPlaceReportLayout =  TrackReportsTabbar.cells("viewMonthwisePlaceRpt").attachLayout("1C");
                dhxPlaceReportLayout.cells("a").hideHeader();
                                                 
                dhxPlaceReportLayout.cells("a").setWidth('200');
                placeReportsGrid     = dhxPlaceReportLayout.cells("a").attachGrid();
                placeReportsGrid.enableColSpan(true);
                notfButtonBar       = TrackReportsTabbar.tabs("viewMonthwisePlaceRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='place_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='placePaging'></span></div>",
                    height: 35
                });
                
                placeReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                placeReportsGrid.enablePaging(true,50,5,'placePaging',false);
                placeReportsGrid.setPagingSkin("toolbar");
                placeReportsGrid.init();  

                dhxPlaceRptTlbr = dhxPlaceReportLayout.cells("a").attachToolbar();
                dhxPlaceRptTlbr.addText('plRptToolbar', '0', 'State' );
                dhxPlaceRptTlbr.addText('plRptToolbar', '1', '<div style="font-weight:bold;width:300px;" id="statePLName"></div>' );
                dhxPlaceRptTlbr.addText('plRptToolbar', '2', 'City' );
                dhxPlaceRptTlbr.addText('plRptToolbar', '3', '<div style="font-weight:bold;width:300px;" id="cityPLName"></div>' );
                dhxPlaceRptTlbr.addText('plRptToolbar', '4', 'Location' );
                dhxPlaceRptTlbr.addText('plRptToolbar', '5', '<div style="font-weight:bold;width:300px;" id="locPLName"></div>' );
                dhxPlaceRptTlbr.setIconSize(32);

                statePlaceFilterCombo = new dhtmlXCombo("statePLName");
                statePlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    statePlaceFilterCombo.load(data,function() {
                        statePlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                        $("#statePLName").keyup(function() {
                            if(!statePlaceFilterCombo.getComboText()) {
                                if(stateComboFiltInterval) clearInterval(stateComboFiltInterval);
                                stateComboFiltInterval = setInterval( function() { 
                                    statePlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        statePlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(stateComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    });
                });
                
                cityPlaceFilterCombo = new dhtmlXCombo("cityPLName");
                cityPlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    cityPlaceFilterCombo.load(data,function() {

                        $("#cityPLName").keyup(function() {
                            cityPlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!cityPlaceFilterCombo.getComboText()) {
                                if(cityComboFiltInterval) clearInterval(cityComboFiltInterval);
                                cityComboFiltInterval = setInterval( function() { 
                                    cityPlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        cityPlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(cityComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    }); 
                });
                
                locFilterCombo = new dhtmlXCombo("locPLName");
                locFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rowId), function( data ){
                    locFilterCombo.load(data,function() {
                        
                        $("#locPLName").keyup(function() {
                            locFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&flag=1"),false);
                            if(!locFilterCombo.getComboText()) {
                                if(locComboFiltInterval) clearInterval(locComboFiltInterval);
                                locComboFiltInterval = setInterval( function() { 
                                    locFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        locFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(locComboFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                statePlaceFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        cityPlaceFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        locFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        placeCombo.load(data);
                    });
                    
                    $("#placeFlt").val(0);
                    
                    if(statePlaceFilterCombo.getSelectedValue()) {
                        if(statePlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                statePlaceFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),'','','','',''); 
                    }
                });
                
                cityPlaceFilterCombo.attachEvent("onChange", function() {
                    $( "#placeFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()), function( data ){
                        locFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()), function( data ){
                        placeCombo.load(data);
                    });
                    
                    if(cityPlaceFilterCombo.getSelectedValue()) {
                        if(cityPlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                                cityPlaceFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),cityPlaceFilterCombo.getSelectedValue(),'','','','');
                    }
                });
               
                locFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+locFilterCombo.getSelectedValue()), function( data ){
                        placeCombo.load(data);
                    });
                    if(locFilterCombo.getSelectedValue()) {
                        if(locFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+locFilterCombo.getSelectedValue()), function( data ){
                                locFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),cityPlaceFilterCombo.getSelectedValue(),locFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rowId);

                placeReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKPlace.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(placeReportsGrid.getRowsNum() == 0 ) {
                        placeReportsGrid.addRow("row1",['No records found'],0); 
                        placeReportsGrid.setColspan("row1",0,placeReportsGrid.getColumnsNum());
                        placeReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        placeReportsGrid.enableRowsHover(false);
                    } else {
                        placeReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.place_cnt_tot').html("# : "+placeReportsGrid.getUserData("", "TL_Count")+" ");

                    placeReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< placeReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        placeReportsGrid.setColSorting(sort);
                    });

                    placeCombo = new dhtmlXCombo("placeFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ALC_Id="+rowId), function( data ){
                        placeCombo.load(data);
                    });

                    placeCombo.setOptionWidth(350);
                    placeCombo.attachEvent("onChange", function() {
                        $( "#placeFlt" ).val(0);
                        var placeComboVal = placeCombo.getSelectedValue();
                        
                        if(placeComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php"), function( data ){
                                placeCombo.load(data);
                            });
                        }
                    
                        if(!placeCombo.getSelectedValue() && placeCombo.getComboText()) placeComboVal = placeCombo.getComboText();
                        $( "#placeFlt" ).val(placeComboVal);
                        placeCombo.setComboValue(placeComboVal);
                        preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),cityPlaceFilterCombo.getSelectedValue(),locFilterCombo.getSelectedValue(),$("#placeFlt").val(),'','');
                    });
                    preTally.TrackReports.calculateFooterValues(placeReportsGrid);
  
                    $("#placeFlt").keyup(function() {
                        placeCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+locFilterCombo.getSelectedValue()+"&flag=1"),false);
                        if(!placeCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                placeCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+cityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+locFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    placeCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });  
  
                    placeReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            streetRptFlag = 0;
                            preTally.TrackReports.viewMonthwiseStreetReport(rowId,placeReportsGrid.getUserData(rowId, "ST_Id"),placeReportsGrid.getUserData(rowId, "CT_Id"),placeReportsGrid.getUserData(rowId, "ALC_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkPL_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkPL_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),cityPlaceFilterCombo.getSelectedValue(),locFilterCombo.getSelectedValue(),placeCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.TrackReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),cityPlaceFilterCombo.getSelectedValue(),locFilterCombo.getSelectedValue(),placeCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwisePlaceFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,'1',colId,srtFlg);

            placeReportsGrid.clearAll();
            placeReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKPlace.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.place_cnt_tot').html("# : "+placeReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.TrackReports.calculateFooterValues(placeReportsGrid);
                if(placeReportsGrid.getRowsNum() == 0 ) {
                    placeReportsGrid.addRow("row1",['No records found'],0); 
                    placeReportsGrid.setColspan("row1",0,placeReportsGrid.getColumnsNum());
                    placeReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    placeReportsGrid.enableRowsHover(false);
                } else {
                    placeReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseStreetReport : function(rowId,rptST_Id,rptCT_Id,rptACL_Id) {
            if(streetRptFlag != 1){
                var stateFiltInterval,cityFiltInterval,locFiltInterval,placeFiltInterval,streetFiltInterval;
                streetRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                TrackReportsTabbar.tabs("viewMonthwiseStreetRpt").setActive();
                dhxStreetReportLayout =  TrackReportsTabbar.cells("viewMonthwiseStreetRpt").attachLayout("1C");
                dhxStreetReportLayout.cells("a").hideHeader();
                                                 
                dhxStreetReportLayout.cells("a").setWidth('200');
                streetReportsGrid     = dhxStreetReportLayout.cells("a").attachGrid();
                streetReportsGrid.enableColSpan(true);
                TrackReportsTabbar.tabs("viewMonthwiseStreetRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='street_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='streetPaging'></span></div>",
                    height: 35
                });
                
                streetReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                streetReportsGrid.enablePaging(true,50,5,'streetPaging',false);
                streetReportsGrid.setPagingSkin("toolbar");
                streetReportsGrid.init();  
           
                dhxStreetRptTlbr = dhxStreetReportLayout.cells("a").attachToolbar();
                dhxStreetRptTlbr.addText('stRptToolbar', '0', 'State' );
                dhxStreetRptTlbr.addText('stRptToolbar', '1', '<div style="font-weight:bold;width:230px;" id="stateSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '2', 'City' );
                dhxStreetRptTlbr.addText('stRptToolbar', '3', '<div style="font-weight:bold;width:230px;" id="citySTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '4', 'Location' );
                dhxStreetRptTlbr.addText('stRptToolbar', '5', '<div style="font-weight:bold;width:230px;" id="locSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '6', 'Place' );
                dhxStreetRptTlbr.addText('stRptToolbar', '7', '<div style="font-weight:bold;width:230px;" id="placeSTName"></div>' );
                dhxStreetRptTlbr.setIconSize(32);

                stateStreetFilterCombo = new dhtmlXCombo("stateSTName");
                stateStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    stateStreetFilterCombo.load(data,function() {

                        $("#stateSTName").keyup(function() {
                            stateStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                            if(!stateStreetFilterCombo.getComboText()) {
                                if(stateFiltInterval) clearInterval(stateFiltInterval);
                                stateFiltInterval = setInterval( function() { 
                                    stateStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        stateStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(stateFiltInterval); 
                                }, 500);
                            }
                        });   
                    });
                });
                
                cityStreetFilterCombo = new dhtmlXCombo("citySTName");
                cityStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    cityStreetFilterCombo.load(data,function() {

                        $("#citySTName").keyup(function() {
                            cityStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!cityStreetFilterCombo.getComboText()) {
                                if(cityFiltInterval) clearInterval(cityFiltInterval);
                                cityFiltInterval = setInterval( function() { 
                                    cityStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        cityStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(cityFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                locStreetFilterCombo = new dhtmlXCombo("locSTName");
                locStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id), function( data ){
                    locStreetFilterCombo.load(data,function() {
                    
                        $("#locSTName").keyup(function() {
                            locStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!locStreetFilterCombo.getComboText()) {
                                if(locFiltInterval) clearInterval(placeFiltInterval);
                                locFiltInterval = setInterval( function() { 
                                locStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        locStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(locFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                placeStreetFilterCombo = new dhtmlXCombo("placeSTName");
                placeStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id+"&PL_Id="+rowId), function( data ){
                    placeStreetFilterCombo.load(data,function() {
                   
                        $("#placeSTName").keyup(function() {
                           placeStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                           if(!placeStreetFilterCombo.getComboText()) {
                               if(placeFiltInterval) clearInterval(placeFiltInterval);
                               placeFiltInterval = setInterval( function() { 
                               placeStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                       placeStreetFilterCombo.openSelect();
                                       $(".dhxcombolist_dhx_skyblue").height(176);
                                   });
                                   clearInterval(placeFiltInterval); 
                               }, 500);
                           }
                       });  
                    }); 
                });
                
                stateStreetFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateStreetFilterCombo.getSelectedValue()), function( data ){
                        cityStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()), function( data ){
                        locStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()), function( data ){
                        placeStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()), function( data ){
                        streetCombo.load(data);
                    });
                    
                    $("#streetFlt").val(0);
                    
                    if(stateStreetFilterCombo.getSelectedValue()) {
                        if(stateStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                stateStreetFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),'','','','','',''); 
                    }
                });
                
                cityStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()), function( data ){
                        locStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()), function( data ){
                        placeStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()), function( data ){
                        streetCombo.load(data);
                    });
                    $("#streetFlt").val(0);
                    if(cityStreetFilterCombo.getSelectedValue()) {
                        if(cityStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+stateStreetFilterCombo.getSelectedValue()), function( data ){
                                cityStreetFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),'','','','','');
                    }
                });
               
                locStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()), function( data ){
                        placeStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()), function( data ){
                        streetCombo.load(data);
                    });
                    $("#streetFlt").val(0);
                    if(locStreetFilterCombo.getSelectedValue()) {
                        if(locStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()), function( data ){
                                locStreetFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),locStreetFilterCombo.getSelectedValue(),'','','','');
                    }
                });
                
                placeStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&PL_Id="+placeStreetFilterCombo.getSelectedValue()), function( data ){
                        streetCombo.load(data);
                    });
                    $("#streetFlt").val(0);
                    if(placeStreetFilterCombo.getSelectedValue()) {
                        if(placeStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()), function( data ){
                                placeStreetFilterCombo.load(data);
                            });
                        }
                        preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),locStreetFilterCombo.getSelectedValue(),placeStreetFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptACL_Id,rowId);

                streetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKStreet.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(streetReportsGrid.getRowsNum() == 0 ) {
                        streetReportsGrid.addRow("row1",['No records found'],0); 
                        streetReportsGrid.setColspan("row1",0,streetReportsGrid.getColumnsNum());
                        streetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        streetReportsGrid.enableRowsHover(false);
                    } else {
                        streetReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.street_cnt_tot').html("# : "+streetReportsGrid.getUserData("", "TL_Count")+" ");

                    streetReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i < streetReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        streetReportsGrid.setColSorting(sort);
                    });

                    streetCombo = new dhtmlXCombo("streetFlt");
                    streetCombo.setOptionWidth(350);
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&PL_Id="+placeStreetFilterCombo.getSelectedValue()), function( data ){
                        streetCombo.load(data);
                    });

                    streetCombo.attachEvent("onChange", function() {
                        $( "#streetFlt" ).val(0);
                        var streetComboVal = streetCombo.getSelectedValue();
                        if(streetComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&PL_Id="+placeStreetFilterCombo.getSelectedValue()), function( data ){
                                streetCombo.load(data);
                            });
                        }
                        if(!streetCombo.getSelectedValue() && streetCombo.getComboText()) streetComboVal = streetCombo.getComboText();
                        $( "#streetFlt" ).val(streetComboVal);
                        streetCombo.setComboValue(streetComboVal);
                        preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),locStreetFilterCombo.getSelectedValue(),placeStreetFilterCombo.getSelectedValue(),$("#streetFlt").val(),'','');
                    });
                    
                    preTally.TrackReports.calculateFooterValues(streetReportsGrid);
               
                    $("#streetFlt").keyup(function() {
                        streetCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&PL_Id="+placeStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!streetCombo.getComboText()) {
                            if(streetFiltInterval) clearInterval(streetFiltInterval);
                            streetFiltInterval = setInterval( function() { 
                            streetCombo.load(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+stateStreetFilterCombo.getSelectedValue()+"&CT_Id="+cityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+locStreetFilterCombo.getSelectedValue()+"&PL_Id="+placeStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    streetCombo.openSelect();
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
                            preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),locStreetFilterCombo.getSelectedValue(),placeStreetFilterCombo.getSelectedValue(),$("#streetFlt").val(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.TrackReports.applyMonthwiseStreetFilter(stateStreetFilterCombo.getSelectedValue(),cityStreetFilterCombo.getSelectedValue(),locStreetFilterCombo.getSelectedValue(),placeStreetFilterCombo.getSelectedValue(),$("#streetFlt").val(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseStreetFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,'1',colId,srtFlg);

            streetReportsGrid.clearAll();
            streetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseTKStreet.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.street_cnt_tot').html("# : "+streetReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.TrackReports.calculateFooterValues(streetReportsGrid);
                if(streetReportsGrid.getRowsNum() == 0 ) {
                    streetReportsGrid.addRow("row1",['No records found'],0); 
                    streetReportsGrid.setColspan("row1",0,streetReportsGrid.getColumnsNum());
                    streetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    streetReportsGrid.enableRowsHover(false);
                } else {
                    streetReportsGrid.enableRowsHover(true,"bonusReportHover");
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