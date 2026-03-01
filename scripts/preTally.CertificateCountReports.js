;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthData,yearData;
    preTally.CertificateCountReports = {
        viewCertificateCountReports : function(){
            if (!dhxMiddleBlockTabs.cells("view_certificateCountReports")) {
                dhxMiddleBlockTabs.addTab("view_certificateCountReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Location Based Certificate Count&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='cCkRefreshTab'/>", 390);
                dhxMiddleBlockTabs.tabs("view_certificateCountReports").setActive();
                
                dhxCertificateCountReportsLayout   =  dhxMiddleBlockTabs.cells("view_certificateCountReports").attachLayout("1C");
                CertificateCountReportToolbar      = dhxCertificateCountReportsLayout.cells("a").attachToolbar();                
                CertificateCountReportToolbar.setIconsPath("images/icon/default_18/");
                CertificateCountReportToolbar.setAlign('right');
                
                $(".cCkRefreshTab").click(function(){
                    preTally.CertificateCountReports.clearAllFlags();
                    var actvId = CertificateCountReportsTabbar.getActiveTab();
                    if(actvId == 'viewMonthwiseCertificateStateRpt')    preTally.CertificateCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseCertificateCityRpt')     preTally.CertificateCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseCertificateLocationRpt') preTally.CertificateCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseCertificatePlaceRpt')    preTally.CertificateCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseCertificateStreetRpt')   preTally.CertificateCountReports.viewMonthwiseStreetReport('','','','');
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "view_certificateCountReports")
                        preTally.CertificateCountReports.clearAllFlags();
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
                
                CertificateCountReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                CertificateCountReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                CertificateCountReportToolbar.attachEvent("onClick", function(id){  
                    preTally.CertificateCountReports.clearAllFlags();
                    var pId     = CertificateCountReportToolbar.getParentId(id);
                    var actvId  = CertificateCountReportsTabbar.getActiveTab();

                    if(pId == 'rpt_month_filter') {
                        yearData        = '';
                        if(CertificateCountReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = CertificateCountReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        CertificateCountReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData       = '';
                        if(CertificateCountReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData    = CertificateCountReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        CertificateCountReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(actvId == 'viewMonthwiseCertificateStateRpt')    preTally.CertificateCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseCertificateCityRpt')     preTally.CertificateCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseCertificateLocationRpt') preTally.CertificateCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseCertificatePlaceRpt')    preTally.CertificateCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseCertificateStreetRpt')   preTally.CertificateCountReports.viewMonthwiseStreetReport('','','','');
                });
 
                var date    = new Date();
                monthData   = date.getMonth();
                monthData   = monthData+1; // get current month number;starting with 1
                CertificateCountReportToolbar.setItemText('rpt_month_filter',monthNames[monthData]);
                
                
                
                var date        = new Date();
//                var currentYear = date.getFullYear();
//                var nextYear    = currentYear+1;
//                CertificateCountReportToolbar.setItemText('rpt_year_filter',currentYear+'-'+nextYear);
                
                CertificateCountReportsTabbar = dhxCertificateCountReportsLayout.cells("a").attachTabbar();
                CertificateCountReportsTabbar.addTab("viewMonthwiseCertificateStateRpt", "Statewise Report");
                CertificateCountReportsTabbar.addTab("viewMonthwiseCertificateCityRpt", "Citywise Report");
                CertificateCountReportsTabbar.addTab("viewMonthwiseCertificateLocationRpt", "Locationwise Report");
                CertificateCountReportsTabbar.addTab("viewMonthwiseCertificatePlaceRpt", "Placewise Report");
                CertificateCountReportsTabbar.addTab("viewMonthwiseCertificateStreetRpt", "Streetwise Report");
                
                CstateRptFlag = 0;
                preTally.CertificateCountReports.viewMonthwiseStateReport();
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateStateRpt").setActive();
                CertificateCountReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'viewMonthwiseCertificateStateRpt')    preTally.CertificateCountReports.viewMonthwiseStateReport();
                    if(id == 'viewMonthwiseCertificateCityRpt')     preTally.CertificateCountReports.viewMonthwiseCityReport('');
                    if(id == 'viewMonthwiseCertificateLocationRpt') preTally.CertificateCountReports.viewMonthwiseLocationReport('','');
                    if(id == 'viewMonthwiseCertificatePlaceRpt')    preTally.CertificateCountReports.viewMonthwisePlaceReport('','','');
                    if(id == 'viewMonthwiseCertificateStreetRpt')   preTally.CertificateCountReports.viewMonthwiseStreetReport('','','','');
                    return true;
                });
            } else {
                dhxMiddleBlockTabs.tabs("view_certificateCountReports").setActive();
                var actvId = CertificateCountReportsTabbar.getActiveTab(); 
                if(actvId == 'viewMonthwiseCertificateStateRpt') preTally.CertificateCountReports.viewMonthwiseStateReport();
            }
        },
        clearAllFlags : function() {
            CstateRptFlag        = 0;  
            CcityRptFlag         = 0;
            ClocationFlag        = 0;
            CplaceRptFlag        = 0;
            CstreetRptFlag       = 0;
        },
        viewMonthwiseStateReport : function() {
            if(CstateRptFlag != 1){
                CstateRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateStateRpt").setActive();
                dhxCertificateStateReportLayout =  CertificateCountReportsTabbar.cells("viewMonthwiseCertificateStateRpt").attachLayout("1C");
                dhxCertificateStateReportLayout.cells("a").hideHeader();
                                                 
                dhxCertificateStateReportLayout.cells("a").setWidth('200');
                CStateReportsGrid = dhxCertificateStateReportLayout.cells("a").attachGrid();
                CStateReportsGrid.enableColSpan(true);
                
                CStateReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < CStateReportsGrid.getRowsNum(); i++){
                        rowID = CStateReportsGrid.getRowId(i);   
                        CStateReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                preTally.Settings.progressOn(true, dhxLayout, null);
               
                CStateReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCState.php&m="+monthData+"&y="+yearData), function() { 

                    CstateCombo = new dhtmlXCombo("CstateFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                        CstateCombo.load(data); 
                    });

                    preTally.Settings.progressOff(true, dhxLayout, null);

                    if(CStateReportsGrid.getRowsNum() == 0 ) {
                        CStateReportsGrid.addRow("row1",['No records found'],0); 
                        CStateReportsGrid.setColspan("row1",0,CStateReportsGrid.getColumnsNum());
                        CStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }  

                    CStateReportsGrid.attachEvent("onRowSelect",function(rowId,ind){
                        if(rowId != 'row1') {
                            CcityRptFlag = 0 ;
                            preTally.CertificateCountReports.viewMonthwiseCityReport(rowId);
                        }   else return false;
                    });

                    CstateCombo.setOptionWidth(280);
                    CstateCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"));
                    var combofiltrInterval;
                    $( "#CstateFlt" ).keyup(function(){
                        if(!CstateCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                CstateCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                    CstateCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });    
                    
                    CstateCombo.attachEvent("onChange", function() {
                        var CstateComboVal = CstateCombo.getSelectedValue();
                        if(!CstateCombo.getSelectedValue() && CstateCombo.getComboText()) CstateComboVal = CstateCombo.getComboText();
                        CstateCombo.setComboValue(CstateComboVal);
                        if(CstateCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                CstateCombo.load(data);
                            });
                        }

                        preTally.CertificateCountReports.applyMonthwiseStateFilter(CstateComboVal);
                    });

                    preTally.CertificateCountReports.calculateFooterValues(CStateReportsGrid);
                    if(CStateReportsGrid.doesRowExist("row1"))
                        CStateReportsGrid.enableRowsHover(false);
                    else
                        CStateReportsGrid.enableRowsHover(true,"bonusReportHover");

                    CStateReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        if(index != 0) {
                            var rowID = 0;
                            var i;
                            for (i = 0; i < CStateReportsGrid.getRowsNum(); i++){
                                rowID = CStateReportsGrid.getRowId(i);   
                                CStateReportsGrid.cells(rowID,0).setValue(i+1);
                            };
                        }
                        var sort = 'na';
                        for(i = 1; i < CStateReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CStateReportsGrid.setColSorting(sort);
                    });

                    var srtFlg          = 0;
                    $('.tkSt_Sort_BIR').click(function() {
                        var colId       =   $(this).attr("colNum");
                        $('.tkSt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');

                        if(srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "asc";
                                CStateReportsGrid.setCustomSorting(preTally.CertificateCountReports.str_custom,1);
                                CStateReportsGrid.sortRows(0,"int","asc");
                            }   else {
                                CStateReportsGrid.sortRows(colId,"int","asc");
                            }
                            srtFlg      = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "desc";
                                CStateReportsGrid.setCustomSorting(preTally.CertificateCountReports.str_custom,1);
                                CStateReportsGrid.sortRows(0,"int","desc");
                            } else {
                                CStateReportsGrid.sortRows(colId,"int","desc");
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

            CStateReportsGrid.clearAll();
            CStateReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCState.php"+rptFilterParams+"&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);

                if(CStateReportsGrid.getRowsNum() == 0 ) {
                    CStateReportsGrid.addRow("row1",['No records found'],0); 
                    CStateReportsGrid.setColspan("row1",0,CStateReportsGrid.getColumnsNum());
                    CStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }  
                
                preTally.CertificateCountReports.calculateFooterValues(CStateReportsGrid);
                if(CStateReportsGrid.doesRowExist("row1"))
                    CStateReportsGrid.enableRowsHover(false);
                else
                    CStateReportsGrid.enableRowsHover(true,"bonusReportHover");
                
            });
        },
        viewMonthwiseCityReport : function(rptSt_Id) {
            if(CcityRptFlag != 1){
                var combofiltrInterval,cityFilInterval;
                CcityRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateCityRpt").setActive();
                dhxCertificateCityReportLayout =  CertificateCountReportsTabbar.cells("viewMonthwiseCertificateCityRpt").attachLayout("1C");
                dhxCertificateCityReportLayout.cells("a").hideHeader();
                                                 
                dhxCertificateCityReportLayout.cells("a").setWidth('200');
                CCityReportsGrid     = dhxCertificateCityReportLayout.cells("a").attachGrid();
                CCityReportsGrid.enableColSpan(true);
                notfButtonBar       = CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateCityRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='city_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='cityPaging'></span></div>",
                    height: 35
                });
                
//                CCityReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
//                CCityReportsGrid.enablePaging(true,50,5,'cityPaging',false);
//                CCityReportsGrid.setPagingSkin("toolbar");
                CCityReportsGrid.init();  
                CCityReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < CCityReportsGrid.getRowsNum(); i++){
                        rowID = CCityReportsGrid.getRowId(i);   
                        CCityReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxCityRptTlbr = dhxCertificateCityReportLayout.cells("a").attachToolbar();
                dhxCityRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxCityRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:500px;" id="CstateName"></div>' );
                dhxCityRptTlbr.setIconSize(32);

                CCstatePlaceFilterCombo = new dhtmlXCombo("CstateName");

                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                    CCstatePlaceFilterCombo.load(data);
                    if (!rptSt_Id) {
                        CCstatePlaceFilterCombo.setComboValue(CCstatePlaceFilterCombo.getSelectedValue());
                    } 
                });
                CCstatePlaceFilterCombo.setOptionWidth(280);
                
                var combofiltrInterval;
                $("#CstateName").keyup(function() {
                    CCstatePlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                    if(!CCstatePlaceFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            CCstatePlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                CCstatePlaceFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });
                
                CCstatePlaceFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CCstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        CcityCombo.load(data);
                    });
                    var CstateComboVal = CCstatePlaceFilterCombo.getSelectedValue();
                    if(CstateComboVal == 0) {
                        $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                            CCstatePlaceFilterCombo.load(data);
                        });
                    }
                    if(!CCstatePlaceFilterCombo.getSelectedValue() && CCstatePlaceFilterCombo.getComboText()) CstateComboVal = CCstatePlaceFilterCombo.getComboText();
                       
                    $( "#CstateName" ).val(CstateComboVal);
                    CCstatePlaceFilterCombo.setComboValue(CstateComboVal);

                    $("#CcityFlt").val(0);
                    preTally.CertificateCountReports.applyMonthwiseCityFilter(CCstatePlaceFilterCombo.getSelectedValue(),'','');
                });

                var filterValue = new Array(monthData,yearData,rptSt_Id);
                CCityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCCity.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(CCityReportsGrid.getRowsNum() == 0 ) {
                        CCityReportsGrid.addRow("row1",['No records found'],0); 
                        CCityReportsGrid.setColspan("row1",0,CCityReportsGrid.getColumnsNum());
                        CCityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        CCityReportsGrid.enableRowsHover(false);
                    } else {
                        CCityReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.city_cnt_tot').html("# : "+CCityReportsGrid.getUserData("", "TL_Count")+" ");

                    CCityReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< CCityReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CCityReportsGrid.setColSorting(sort);
                    });

                    CcityCombo = new dhtmlXCombo("CcityFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                        CcityCombo.load(data);
                    });

                    CcityCombo.setOptionWidth(350);
                    CcityCombo.attachEvent("onChange", function() {
                        $( "#CcityFlt" ).val(0);
                        var CcityComboVal = CcityCombo.getSelectedValue();
                        
                        if(CcityComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CCstatePlaceFilterCombo.getSelectedValue()), function( data ){
                                CcityCombo.load(data);
                            });
                        }
                        if(!CcityCombo.getSelectedValue() && CcityCombo.getComboText()) CcityComboVal = CcityCombo.getComboText();
                       
                        $( "#CcityFlt" ).val(CcityComboVal);
                        CcityCombo.setComboValue(CcityComboVal);
                        preTally.CertificateCountReports.applyMonthwiseCityFilter(CCstatePlaceFilterCombo.getSelectedValue(),'','');
                    });
                    
                    preTally.CertificateCountReports.calculateFooterValues(CCityReportsGrid);

                    $("#CcityFlt").keyup(function() {
                        CcityCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CCstatePlaceFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!CcityCombo.getComboText()) {
                            if(cityFilInterval) clearInterval(cityFilInterval);
                            cityFilInterval = setInterval( function() { 
                                CcityCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CCstatePlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    CcityCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(cityFilInterval); 
                            }, 500);
                        }
                    });                   

                    CCityReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            ClocationFlag = 0;
                            rptCT_Id = rowId;
                            preTally.CertificateCountReports.viewMonthwiseLocationReport(rowId,CCityReportsGrid.getUserData(rowId, "ST_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkCt_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkCt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.CertificateCountReports.applyMonthwiseCityFilter(CCstatePlaceFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.CertificateCountReports.applyMonthwiseCityFilter(CCstatePlaceFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseCityFilter : function(rptSt_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);

            if($("#CcityFlt").val() == 'undefined')  
                $("#CcityFlt").val(0);
      
            var filterValue = new Array(monthData,yearData,rptSt_Id,$("#CcityFlt").val(),'1',colId,srtFlg);

            CCityReportsGrid.clearAll();
            CCityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCCity.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.city_cnt_tot').html("# : "+CCityReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.CertificateCountReports.calculateFooterValues(CCityReportsGrid);
                if(CCityReportsGrid.getRowsNum() == 0 ) {
                    CCityReportsGrid.addRow("row1",['No records found'],0); 
                    CCityReportsGrid.setColspan("row1",0,CCityReportsGrid.getColumnsNum());
                    CCityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    CCityReportsGrid.enableRowsHover(false);
                } else {
                    CCityReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseLocationReport : function(rptCT_Id,rptST_Id) {
            if(ClocationFlag != 1){
                var combofiltrInterval,filtrInterval;
                ClocationFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateLocationRpt").setActive();
                dhxCertificateLocationReportLayout =  CertificateCountReportsTabbar.cells("viewMonthwiseCertificateLocationRpt").attachLayout("1C");
                dhxCertificateLocationReportLayout.cells("a").hideHeader();
                                                 
                dhxCertificateLocationReportLayout.cells("a").setWidth('200');
                CLocationReportsGrid = dhxCertificateLocationReportLayout.cells("a").attachGrid();
                CLocationReportsGrid.setImagePath("../../codebase/imgs/");
                CLocationReportsGrid.setSkin("dhx_skyblue");
                
                CLocationReportsGrid.enableColSpan(true);
                notfLocButtonBar = CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateLocationRpt").attachStatusBar({
                    text  : "<div class='loc_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='loc_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='locPaging'></span></div>",
                    height: 35
                });
                
//                CLocationReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
//                CLocationReportsGrid.enablePaging(true,50,5,'locPaging',false);
//                CLocationReportsGrid.setPagingSkin("toolbar");
                
                CLocationReportsGrid.enableColSpan(true);
                CLocationReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < CLocationReportsGrid.getRowsNum(); i++){
                        rowID = CLocationReportsGrid.getRowId(i);   
                        CLocationReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxLocRptTlbr = dhxCertificateLocationReportLayout.cells("a").attachToolbar();
                dhxLocRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxLocRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:400px;" id="CstateLocName"></div>' );
                dhxLocRptTlbr.addText('masterRptToolbar', '2', 'City' );
                dhxLocRptTlbr.addText('masterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="CcityLocName"></div>' );
                dhxLocRptTlbr.setIconSize(32);

                CstateLocFilterCombo = new dhtmlXCombo("CstateLocName");
 
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    CstateLocFilterCombo.load(data); 
                });
                
                CstateLocFilterCombo.setOptionWidth(280);
                
                CstateLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);
                $("#CstateLocName").keyup(function() {

                    if(!CstateLocFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            CstateLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                CstateLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });        

                CstateLocFilterCombo.attachEvent("onChange", function() {
                    rptCT_Id = '';

                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+CstateLocFilterCombo.getSelectedValue()), function( data ){
                        CcityLocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php"), function( data ){
                        ClocCombo.load(data);
                    });
                    $("#CloctnFlt").val(0);

                    CcityLocFilterCombo.setComboValue(0);
                    if(CstateLocFilterCombo.getSelectedValue()) {
                        if(CstateLocFilterCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                CstateLocFilterCombo.load(data); 
                            }); 
                        }
                        preTally.CertificateCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                    }
                });
                
                CcityLocFilterCombo = new dhtmlXCombo("CcityLocName");
                CcityLocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    CcityLocFilterCombo.load(data);
                    if (!rptCT_Id) {
                        CcityLocFilterCombo.setComboValue(CcityLocFilterCombo.getSelectedValue());
                    } 
                });
                
                $("#CcityLocName").keyup(function() {
                    CcityLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                    if(!CcityLocFilterCombo.getComboText()) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            CcityLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                CcityLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176);
                                CcityLocFilterCombo.setComboValue('');
                            });
                            clearInterval(filtrInterval); 
                        }, 500);
                    }
                });       

                CcityLocFilterCombo.attachEvent("onChange", function() {
                    $( "#CloctnFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+CcityLocFilterCombo.getSelectedValue()), function( data ){
                        ClocCombo.load(data);
                    });

                    if(CcityLocFilterCombo.getSelectedValue()) {
                        if(CcityLocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateLocFilterCombo.getSelectedValue()), function( data ){
                                CcityLocFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwiseLocationFilter(CcityLocFilterCombo.getSelectedValue());
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptCT_Id,rptST_Id);

                CLocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCLoc.php&filter="+filterValue), function() { 
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    ClocCombo = new dhtmlXCombo("CloctnFlt");
                
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+rptST_Id+"&CT_Id="+rptCT_Id), function( data ){
                        ClocCombo.load(data);
                        ClocCombo.setOptionWidth(350);

                        ClocCombo.attachEvent("onChange", function() {
                            var ClocComboVal = ClocCombo.getSelectedValue();
                            if(ClocComboVal == 0)   {
                                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+CcityLocFilterCombo.getSelectedValue()), function( data ){
                                    ClocCombo.load(data);
                                });
                            }
                            if(!ClocCombo.getSelectedValue() && ClocCombo.getComboText()) ClocComboVal = ClocCombo.getComboText();
                            $( "#CloctnFlt" ).val(ClocComboVal);
                            ClocCombo.setComboValue(ClocComboVal);
                            
                            preTally.CertificateCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                        });

                        $("#CloctnFlt").keyup(function() {
                            ClocCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+CcityLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!ClocCombo.getComboText()) {
                                if(combofiltrInterval) clearInterval(combofiltrInterval);
                                combofiltrInterval = setInterval( function() { 
                                    ClocCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+CcityLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ClocCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(combofiltrInterval); 
                                }, 500);
                            }
                        });     
                    });
              
                    if(CLocationReportsGrid.getRowsNum() == 0 ) {
                        CLocationReportsGrid.addRow("row1",['No records found'],0); 
                        CLocationReportsGrid.setColspan("row1",0,CLocationReportsGrid.getColumnsNum());
                        CLocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        CLocationReportsGrid.enableRowsHover(false);
                    } else {
                        CLocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    
                    $('.loc_cnt_tot').html("# : "+CLocationReportsGrid.getUserData("", "TL_Count")+" ");

                    CLocationReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< CLocationReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CLocationReportsGrid.setColSorting(sort);
                    });
                    preTally.CertificateCountReports.calculateFooterValues(CLocationReportsGrid);
                    
                    CLocationReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            CplaceRptFlag = 0;
                            preTally.CertificateCountReports.viewMonthwisePlaceReport(rowId,CLocationReportsGrid.getUserData(rowId, "ST_Id"),CLocationReportsGrid.getUserData(rowId, "CT_Id"));
                        }   else return false;
                    });
                    
                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkLC_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkLC_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.CertificateCountReports.applyMonthwiseLocationFilter(CcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.CertificateCountReports.applyMonthwiseLocationFilter(CcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseLocationFilter : function(rptCT_Id,colId,srtFlg) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            if($("#CloctnFlt").val() == 'undefined')  
                $("#CloctnFlt").val(0);
            
            var filterValue = new Array(monthData,yearData,CcityLocFilterCombo.getSelectedValue(),CstateLocFilterCombo.getSelectedValue(),$("#CloctnFlt").val(),'1',colId,srtFlg);    
            CLocationReportsGrid.clearAll();
            CLocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCLoc.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.loc_cnt_tot').html("# : "+CLocationReportsGrid.getUserData("", "TL_Count")+" ");
                               
                preTally.CertificateCountReports.calculateFooterValues(CLocationReportsGrid);
                if(CLocationReportsGrid.getRowsNum() == 0 ) {
                    CLocationReportsGrid.addRow("row1",['No records found'],0); 
                    CLocationReportsGrid.setColspan("row1",0,CLocationReportsGrid.getColumnsNum());
                    CLocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    CLocationReportsGrid.enableRowsHover(false);
                } else {
                    CLocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                } 
            });
        },
        viewMonthwisePlaceReport : function(rowId,rptST_Id,rptCT_Id) {
            if(CplaceRptFlag != 1){
                var combofiltrInterval,CstateComboFiltInterval,CcityComboFiltInterval,ClocComboFiltInterval;
                CplaceRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificatePlaceRpt").setActive();
                dhxCertificatePlaceReportLayout =  CertificateCountReportsTabbar.cells("viewMonthwiseCertificatePlaceRpt").attachLayout("1C");
                dhxCertificatePlaceReportLayout.cells("a").hideHeader();
                                                 
                dhxCertificatePlaceReportLayout.cells("a").setWidth('200');
                CplaceReportsGrid     = dhxCertificatePlaceReportLayout.cells("a").attachGrid();
                CplaceReportsGrid.enableColSpan(true);
                notfButtonBar       = CertificateCountReportsTabbar.tabs("viewMonthwiseCertificatePlaceRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='place_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='placePaging'></span></div>",
                    height: 35
                });
                
//                CplaceReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
//                CplaceReportsGrid.enablePaging(true,50,5,'placePaging',false);
//                CplaceReportsGrid.setPagingSkin("toolbar");
                CplaceReportsGrid.init();  

                dhxCertificatePlaceRptTlbr = dhxCertificatePlaceReportLayout.cells("a").attachToolbar();
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '0', 'State' );
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '1', '<div style="font-weight:bold;width:300px;" id="CstatePLName"></div>' );
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '2', 'City' );
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '3', '<div style="font-weight:bold;width:300px;" id="cityPLName"></div>' );
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '4', 'Location' );
                dhxCertificatePlaceRptTlbr.addText('plRptToolbar', '5', '<div style="font-weight:bold;width:300px;" id="ClocPLName"></div>' );
                dhxCertificatePlaceRptTlbr.setIconSize(32);

                statePlaceFilterCombo = new dhtmlXCombo("CstatePLName");
                statePlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    statePlaceFilterCombo.load(data,function() {
                        statePlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                        $("#CstatePLName").keyup(function() {
                            if(!statePlaceFilterCombo.getComboText()) {
                                if(CstateComboFiltInterval) clearInterval(CstateComboFiltInterval);
                                CstateComboFiltInterval = setInterval( function() { 
                                    statePlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        statePlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(CstateComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    });
                });
                
                CcityPlaceFilterCombo = new dhtmlXCombo("cityPLName");
                CcityPlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    CcityPlaceFilterCombo.load(data,function() {

                        $("#cityPLName").keyup(function() {
                            CcityPlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!CcityPlaceFilterCombo.getComboText()) {
                                if(CcityComboFiltInterval) clearInterval(CcityComboFiltInterval);
                                CcityComboFiltInterval = setInterval( function() { 
                                    CcityPlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        CcityPlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(CcityComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    }); 
                });
                
                ClocFilterCombo = new dhtmlXCombo("ClocPLName");
                ClocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rowId), function( data ){
                    ClocFilterCombo.load(data,function() {
                        
                        $("#ClocPLName").keyup(function() {
                            ClocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&flag=1"),false);
                            if(!ClocFilterCombo.getComboText()) {
                                if(ClocComboFiltInterval) clearInterval(ClocComboFiltInterval);
                                ClocComboFiltInterval = setInterval( function() { 
                                    ClocFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ClocFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(ClocComboFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                statePlaceFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        CcityPlaceFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        ClocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                        CplaceCombo.load(data);
                    });
                    
                    $("#CplaceFlt").val(0);
                    
                    if(statePlaceFilterCombo.getSelectedValue()) {
                        if(statePlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                statePlaceFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),'','','','',''); 
                    }
                });
                
                CcityPlaceFilterCombo.attachEvent("onChange", function() {
                    $( "#CplaceFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        ClocFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        CplaceCombo.load(data);
                    });
                    
                    if(CcityPlaceFilterCombo.getSelectedValue()) {
                        if(CcityPlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+statePlaceFilterCombo.getSelectedValue()), function( data ){
                                CcityPlaceFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),CcityPlaceFilterCombo.getSelectedValue(),'','','','');
                    }
                });
               
                ClocFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ClocFilterCombo.getSelectedValue()), function( data ){
                        CplaceCombo.load(data);
                    });
                    if(ClocFilterCombo.getSelectedValue()) {
                        if(ClocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ClocFilterCombo.getSelectedValue()), function( data ){
                                ClocFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),CcityPlaceFilterCombo.getSelectedValue(),ClocFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rowId);

                CplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCPlace.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(CplaceReportsGrid.getRowsNum() == 0 ) {
                        CplaceReportsGrid.addRow("row1",['No records found'],0); 
                        CplaceReportsGrid.setColspan("row1",0,CplaceReportsGrid.getColumnsNum());
                        CplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        CplaceReportsGrid.enableRowsHover(false);
                    } else {
                        CplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.place_cnt_tot').html("# : "+CplaceReportsGrid.getUserData("", "TL_Count")+" ");

                    CplaceReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< CplaceReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CplaceReportsGrid.setColSorting(sort);
                    });

                    CplaceCombo = new dhtmlXCombo("CplaceFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ALC_Id="+rowId), function( data ){
                        CplaceCombo.load(data);
                    });

                    CplaceCombo.setOptionWidth(350);
                    CplaceCombo.attachEvent("onChange", function() {
                        $( "#CplaceFlt" ).val(0);
                        var CplaceComboVal = CplaceCombo.getSelectedValue();
                        
                        if(CplaceComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php"), function( data ){
                                CplaceCombo.load(data);
                            });
                        }
                    
                        if(!CplaceCombo.getSelectedValue() && CplaceCombo.getComboText()) CplaceComboVal = CplaceCombo.getComboText();
                        $( "#CplaceFlt" ).val(CplaceComboVal);
                        CplaceCombo.setComboValue(CplaceComboVal);
                        preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),CcityPlaceFilterCombo.getSelectedValue(),ClocFilterCombo.getSelectedValue(),$("#CplaceFlt").val(),'','');
                    });
                    preTally.CertificateCountReports.calculateFooterValues(CplaceReportsGrid);
  
                    $("#CplaceFlt").keyup(function() {
                        CplaceCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ClocFilterCombo.getSelectedValue()+"&flag=1"),false);
                        if(!CplaceCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                CplaceCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+statePlaceFilterCombo.getSelectedValue()+"&CT_Id="+CcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ClocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    CplaceCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });  
  
                    CplaceReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            CstreetRptFlag = 0;
                            preTally.CertificateCountReports.viewMonthwiseStreetReport(rowId,CplaceReportsGrid.getUserData(rowId, "ST_Id"),CplaceReportsGrid.getUserData(rowId, "CT_Id"),CplaceReportsGrid.getUserData(rowId, "ALC_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkPL_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkPL_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),CcityPlaceFilterCombo.getSelectedValue(),ClocFilterCombo.getSelectedValue(),CplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.CertificateCountReports.applyMonthwisePlaceFilter(statePlaceFilterCombo.getSelectedValue(),CcityPlaceFilterCombo.getSelectedValue(),ClocFilterCombo.getSelectedValue(),CplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwisePlaceFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,'1',colId,srtFlg);

            CplaceReportsGrid.clearAll();
            CplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCPlace.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.place_cnt_tot').html("# : "+CplaceReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.CertificateCountReports.calculateFooterValues(CplaceReportsGrid);
                if(CplaceReportsGrid.getRowsNum() == 0 ) {
                    CplaceReportsGrid.addRow("row1",['No records found'],0); 
                    CplaceReportsGrid.setColspan("row1",0,CplaceReportsGrid.getColumnsNum());
                    CplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    CplaceReportsGrid.enableRowsHover(false);
                } else {
                    CplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseStreetReport : function(rowId,rptST_Id,rptCT_Id,rptACL_Id) {
            if(CstreetRptFlag != 1){
                var stateFiltInterval,cityFiltInterval,locFiltInterval,placeFiltInterval,streetFiltInterval;
                CstreetRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateStreetRpt").setActive();
                dhxCertificateStreetReportLayout =  CertificateCountReportsTabbar.cells("viewMonthwiseCertificateStreetRpt").attachLayout("1C");
                dhxCertificateStreetReportLayout.cells("a").hideHeader();
                                                 
                dhxCertificateStreetReportLayout.cells("a").setWidth('200');
                CstreetReportsGrid     = dhxCertificateStreetReportLayout.cells("a").attachGrid();
                CstreetReportsGrid.enableColSpan(true);
                CertificateCountReportsTabbar.tabs("viewMonthwiseCertificateStreetRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='street_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='streetPaging'></span></div>",
                    height: 35
                });
                
//                CstreetReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
//                CstreetReportsGrid.enablePaging(true,50,5,'streetPaging',false);
//                CstreetReportsGrid.setPagingSkin("toolbar");
                CstreetReportsGrid.init();  
           
                dhxStreetRptTlbr = dhxCertificateStreetReportLayout.cells("a").attachToolbar();
                dhxStreetRptTlbr.addText('stRptToolbar', '0', 'State' );
                dhxStreetRptTlbr.addText('stRptToolbar', '1', '<div style="font-weight:bold;width:230px;" id="CstateSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '2', 'City' );
                dhxStreetRptTlbr.addText('stRptToolbar', '3', '<div style="font-weight:bold;width:230px;" id="CcitySTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '4', 'Location' );
                dhxStreetRptTlbr.addText('stRptToolbar', '5', '<div style="font-weight:bold;width:230px;" id="ClocSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '6', 'Place' );
                dhxStreetRptTlbr.addText('stRptToolbar', '7', '<div style="font-weight:bold;width:230px;" id="CplaceSTName"></div>' );
                dhxStreetRptTlbr.setIconSize(32);

                CstateStreetFilterCombo = new dhtmlXCombo("CstateSTName");
                CstateStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    CstateStreetFilterCombo.load(data,function() {

                        $("#CstateSTName").keyup(function() {
                            CstateStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                            if(!CstateStreetFilterCombo.getComboText()) {
                                if(stateFiltInterval) clearInterval(stateFiltInterval);
                                stateFiltInterval = setInterval( function() { 
                                    CstateStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        CstateStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(stateFiltInterval); 
                                }, 500);
                            }
                        });   
                    });
                });
                
                CcityStreetFilterCombo = new dhtmlXCombo("CcitySTName");
                CcityStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    CcityStreetFilterCombo.load(data,function() {

                        $("#CcitySTName").keyup(function() {
                            CcityStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!CcityStreetFilterCombo.getComboText()) {
                                if(cityFiltInterval) clearInterval(cityFiltInterval);
                                cityFiltInterval = setInterval( function() { 
                                    CcityStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        CcityStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(cityFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                ClocStreetFilterCombo = new dhtmlXCombo("ClocSTName");
                ClocStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id), function( data ){
                    ClocStreetFilterCombo.load(data,function() {
                    
                        $("#ClocSTName").keyup(function() {
                            ClocStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!ClocStreetFilterCombo.getComboText()) {
                                if(locFiltInterval) clearInterval(placeFiltInterval);
                                locFiltInterval = setInterval( function() { 
                                ClocStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ClocStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(locFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                CplaceStreetFilterCombo = new dhtmlXCombo("CplaceSTName");
                CplaceStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id+"&PL_Id="+rowId), function( data ){
                    CplaceStreetFilterCombo.load(data,function() {
                   
                        $("#CplaceSTName").keyup(function() {
                           CplaceStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                           if(!CplaceStreetFilterCombo.getComboText()) {
                               if(placeFiltInterval) clearInterval(placeFiltInterval);
                               placeFiltInterval = setInterval( function() { 
                               CplaceStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                       CplaceStreetFilterCombo.openSelect();
                                       $(".dhxcombolist_dhx_skyblue").height(176);
                                   });
                                   clearInterval(placeFiltInterval); 
                               }, 500);
                           }
                       });  
                    }); 
                });
                
                CstateStreetFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateStreetFilterCombo.getSelectedValue()), function( data ){
                        CcityStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()), function( data ){
                        ClocStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()), function( data ){
                        CplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()), function( data ){
                        CstreetCombo.load(data);
                    });
                    
                    $("#CstreetFlt").val(0);
                    
                    if(CstateStreetFilterCombo.getSelectedValue()) {
                        if(CstateStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                CstateStreetFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),'','','','','',''); 
                    }
                });
                
                CcityStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()), function( data ){
                        ClocStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()), function( data ){
                        CplaceStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()), function( data ){
                        CstreetCombo.load(data);
                    });
                    $("#CstreetFlt").val(0);
                    if(CcityStreetFilterCombo.getSelectedValue()) {
                        if(CcityStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+CstateStreetFilterCombo.getSelectedValue()), function( data ){
                                CcityStreetFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),'','','','','');
                    }
                });
               
                ClocStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()), function( data ){
                        CplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()), function( data ){
                        CstreetCombo.load(data);
                    });
                    $("#CstreetFlt").val(0);
                    if(ClocStreetFilterCombo.getSelectedValue()) {
                        if(ClocStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()), function( data ){
                                ClocStreetFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),ClocStreetFilterCombo.getSelectedValue(),'','','','');
                    }
                });
                
                CplaceStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&PL_Id="+CplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        CstreetCombo.load(data);
                    });
                    $("#CstreetFlt").val(0);
                    if(CplaceStreetFilterCombo.getSelectedValue()) {
                        if(CplaceStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()), function( data ){
                                CplaceStreetFilterCombo.load(data);
                            });
                        }
                        preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),ClocStreetFilterCombo.getSelectedValue(),CplaceStreetFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptACL_Id,rowId);

                CstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCStreet.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(CstreetReportsGrid.getRowsNum() == 0 ) {
                        CstreetReportsGrid.addRow("row1",['No records found'],0); 
                        CstreetReportsGrid.setColspan("row1",0,CstreetReportsGrid.getColumnsNum());
                        CstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        CstreetReportsGrid.enableRowsHover(false);
                    } else {
                        CstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.street_cnt_tot').html("# : "+CstreetReportsGrid.getUserData("", "TL_Count")+" ");

                    CstreetReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i < CstreetReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        CstreetReportsGrid.setColSorting(sort);
                    });

                    CstreetCombo = new dhtmlXCombo("CstreetFlt");
                    CstreetCombo.setOptionWidth(350);
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&PL_Id="+CplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        CstreetCombo.load(data);
                    });

                    CstreetCombo.attachEvent("onChange", function() {
                        $( "#CstreetFlt" ).val(0);
                        var CstreetComboVal = CstreetCombo.getSelectedValue();
                        if(CstreetComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&PL_Id="+CplaceStreetFilterCombo.getSelectedValue()), function( data ){
                                CstreetCombo.load(data);
                            });
                        }
                        if(!CstreetCombo.getSelectedValue() && CstreetCombo.getComboText()) CstreetComboVal = CstreetCombo.getComboText();
                        $( "#CstreetFlt" ).val(CstreetComboVal);
                        CstreetCombo.setComboValue(CstreetComboVal);
                        preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),ClocStreetFilterCombo.getSelectedValue(),CplaceStreetFilterCombo.getSelectedValue(),$("#CstreetFlt").val(),'','');
                    });
                    
                    preTally.CertificateCountReports.calculateFooterValues(CstreetReportsGrid);
               
                    $("#CstreetFlt").keyup(function() {
                        CstreetCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&PL_Id="+CplaceStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!CstreetCombo.getComboText()) {
                            if(streetFiltInterval) clearInterval(streetFiltInterval);
                            streetFiltInterval = setInterval( function() { 
                            CstreetCombo.load(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+CstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+CcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ClocStreetFilterCombo.getSelectedValue()+"&PL_Id="+CplaceStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    CstreetCombo.openSelect();
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
                            preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),ClocStreetFilterCombo.getSelectedValue(),CplaceStreetFilterCombo.getSelectedValue(),$("#CstreetFlt").val(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.CertificateCountReports.applyMonthwiseStreetFilter(CstateStreetFilterCombo.getSelectedValue(),CcityStreetFilterCombo.getSelectedValue(),ClocStreetFilterCombo.getSelectedValue(),CplaceStreetFilterCombo.getSelectedValue(),$("#CstreetFlt").val(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseStreetFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,'1',colId,srtFlg);

            CstreetReportsGrid.clearAll();
            CstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseCCStreet.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.street_cnt_tot').html("# : "+CstreetReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.CertificateCountReports.calculateFooterValues(CstreetReportsGrid);
                if(CstreetReportsGrid.getRowsNum() == 0 ) {
                    CstreetReportsGrid.addRow("row1",['No records found'],0); 
                    CstreetReportsGrid.setColspan("row1",0,CstreetReportsGrid.getColumnsNum());
                    CstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    CstreetReportsGrid.enableRowsHover(false);
                } else {
                    CstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
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