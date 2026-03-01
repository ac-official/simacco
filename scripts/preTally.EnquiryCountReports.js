;(function($, window, undefined) {
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthData,yearData;
    preTally.EnquiryCountReports = {
        viewEnquiryCountReports : function(){
            if (!dhxMiddleBlockTabs.cells("view_enquiryCountReports")) {
                dhxMiddleBlockTabs.addTab("view_enquiryCountReports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Master Reports - Location Based Enquiry Count&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='eCkRefreshTab'/>", 370);
                dhxMiddleBlockTabs.tabs("view_enquiryCountReports").setActive();
                
                dhxEnquiryCountReportsLayout   =  dhxMiddleBlockTabs.cells("view_enquiryCountReports").attachLayout("1C");
                EnquiryCountReportToolbar      = dhxEnquiryCountReportsLayout.cells("a").attachToolbar();                
                EnquiryCountReportToolbar.setIconsPath("images/icon/default_18/");
                EnquiryCountReportToolbar.setAlign('right');
                
                $(".eCkRefreshTab").click(function(){
                    preTally.EnquiryCountReports.clearAllFlags();
                    var actvId = EnquiryCountReportsTabbar.getActiveTab();
                    if(actvId == 'viewMonthwiseEnquiryStateRpt')    preTally.EnquiryCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseEnquiryCityRpt')     preTally.EnquiryCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseEnquiryLocationRpt') preTally.EnquiryCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseEnquiryPlaceRpt')    preTally.EnquiryCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseEnquiryStreetRpt')   preTally.EnquiryCountReports.viewMonthwiseStreetReport('','','','');
                });
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id == "view_enquiryCountReports")
                        preTally.EnquiryCountReports.clearAllFlags();
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
                
                EnquiryCountReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                EnquiryCountReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                EnquiryCountReportToolbar.attachEvent("onClick", function(id){  
                    preTally.EnquiryCountReports.clearAllFlags();
                    var pId     = EnquiryCountReportToolbar.getParentId(id);
                    var actvId  = EnquiryCountReportsTabbar.getActiveTab();

                    if(pId == 'rpt_month_filter') {
                        yearData        = '';
                        if(EnquiryCountReportToolbar.getListOptionSelected("rpt_month_filter"))
                            monthData   = EnquiryCountReportToolbar.getListOptionSelected("rpt_month_filter").replace(/^m/, '');
                
                        EnquiryCountReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                    }
                    if(pId == 'rpt_year_filter') {
                        monthData       = '';
                        if(EnquiryCountReportToolbar.getListOptionSelected("rpt_year_filter")) 
                            yearData    = EnquiryCountReportToolbar.getListOptionSelected("rpt_year_filter").replace(/^m/, '');
                        
                        EnquiryCountReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                    }
                    if(actvId == 'viewMonthwiseEnquiryStateRpt')    preTally.EnquiryCountReports.viewMonthwiseStateReport();
                    if(actvId == 'viewMonthwiseEnquiryCityRpt')     preTally.EnquiryCountReports.viewMonthwiseCityReport('');
                    if(actvId == 'viewMonthwiseEnquiryLocationRpt') preTally.EnquiryCountReports.viewMonthwiseLocationReport('','');
                    if(actvId == 'viewMonthwiseEnquiryPlaceRpt')    preTally.EnquiryCountReports.viewMonthwisePlaceReport('','','');
                    if(actvId == 'viewMonthwiseEnquiryStreetRpt')   preTally.EnquiryCountReports.viewMonthwiseStreetReport('','','','');
                });
 
                var date    = new Date();
                monthData   = date.getMonth();
              //  EnquiryCountReportToolbar.setItemText('rpt_month_filter',monthNames[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                
                var date        = new Date();
                var currentYear = date.getFullYear();
                //var nextYear    = currentYear+1;
                //EnquiryCountReportToolbar.setItemText('rpt_year_filter',currentYear+'-'+nextYear);               
                monthData   = date.getMonth();
                EnquiryCountReportToolbar.setItemText('rpt_month_filter',monthNames[monthData]);
                monthData   = monthData+1; // get current month number;starting with 1
                
                EnquiryCountReportsTabbar = dhxEnquiryCountReportsLayout.cells("a").attachTabbar();
                EnquiryCountReportsTabbar.addTab("viewMonthwiseEnquiryStateRpt", "Statewise Report");
                EnquiryCountReportsTabbar.addTab("viewMonthwiseEnquiryCityRpt", "Citywise Report");
                EnquiryCountReportsTabbar.addTab("viewMonthwiseEnquiryLocationRpt", "Locationwise Report");
                EnquiryCountReportsTabbar.addTab("viewMonthwiseEnquiryPlaceRpt", "Placewise Report");
                EnquiryCountReportsTabbar.addTab("viewMonthwiseEnquiryStreetRpt", "Streetwise Report");
                
                EstateRptFlag = 0;
                preTally.EnquiryCountReports.viewMonthwiseStateReport();
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryStateRpt").setActive();
                EnquiryCountReportsTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'viewMonthwiseEnquiryStateRpt')    preTally.EnquiryCountReports.viewMonthwiseStateReport();
                    if(id == 'viewMonthwiseEnquiryCityRpt')     preTally.EnquiryCountReports.viewMonthwiseCityReport('');
                    if(id == 'viewMonthwiseEnquiryLocationRpt') preTally.EnquiryCountReports.viewMonthwiseLocationReport('','');
                    if(id == 'viewMonthwiseEnquiryPlaceRpt')    preTally.EnquiryCountReports.viewMonthwisePlaceReport('','','');
                    if(id == 'viewMonthwiseEnquiryStreetRpt')   preTally.EnquiryCountReports.viewMonthwiseStreetReport('','','','');
                    return true;
                });
            } else {
                dhxMiddleBlockTabs.tabs("view_enquiryCountReports").setActive();
                var actvId = EnquiryCountReportsTabbar.getActiveTab(); 
                if(actvId == 'viewMonthwiseEnquiryStateRpt') preTally.EnquiryCountReports.viewMonthwiseStateReport();
            }
        },
        clearAllFlags : function() {
            EstateRptFlag        = 0;  
            EcityRptFlag         = 0;
            ElocationFlag        = 0;
            EplaceRptFlag        = 0;
            EstreetRptFlag       = 0;
        },
        viewMonthwiseStateReport : function() {
            if(EstateRptFlag != 1){
                EstateRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryStateRpt").setActive();
                dhxEnquiryStateReportLayout =  EnquiryCountReportsTabbar.cells("viewMonthwiseEnquiryStateRpt").attachLayout("1C");
                dhxEnquiryStateReportLayout.cells("a").hideHeader();
                                                 
                dhxEnquiryStateReportLayout.cells("a").setWidth('200');
                EStateReportsGrid = dhxEnquiryStateReportLayout.cells("a").attachGrid();
                EStateReportsGrid.enableColSpan(true);
                
                EStateReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < EStateReportsGrid.getRowsNum(); i++){
                        rowID = EStateReportsGrid.getRowId(i);   
                        EStateReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                preTally.Settings.progressOn(true, dhxLayout, null);
               
                EStateReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMonthwiseECState.php&m="+monthData+"&y="+yearData), function() { 

                    EstateCombo = new dhtmlXCombo("EstateFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                        EstateCombo.load(data); 
                    });

                    preTally.Settings.progressOff(true, dhxLayout, null);

                    if(EStateReportsGrid.getRowsNum() == 0 ) {
                        EStateReportsGrid.addRow("row1",['No records found'],0); 
                        EStateReportsGrid.setColspan("row1",0,EStateReportsGrid.getColumnsNum());
                        EStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }  

                    EStateReportsGrid.attachEvent("onRowSelect",function(rowId,ind){
                        if(rowId != 'row1') {
                            EcityRptFlag = 0 ;
                            preTally.EnquiryCountReports.viewMonthwiseCityReport(rowId);
                        }   else return false;
                    });

                    EstateCombo.setOptionWidth(280);
                    EstateCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"));
                    var combofiltrInterval;
                    $( "#EstateFlt" ).keyup(function(){
                        if(!EstateCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                EstateCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                    EstateCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });    
                    
                    EstateCombo.attachEvent("onChange", function() {
                        var EstateComboVal = EstateCombo.getSelectedValue();
                        if(!EstateCombo.getSelectedValue() && EstateCombo.getComboText()) EstateComboVal = EstateCombo.getComboText();
                        EstateCombo.setComboValue(EstateComboVal);
                        if(EstateCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                EstateCombo.load(data);
                            });
                        }

                        preTally.EnquiryCountReports.applyMonthwiseStateFilter(EstateComboVal);
                    });

                    preTally.EnquiryCountReports.calculateFooterValues(EStateReportsGrid);
                    if(EStateReportsGrid.doesRowExist("row1"))
                        EStateReportsGrid.enableRowsHover(false);
                    else
                        EStateReportsGrid.enableRowsHover(true,"bonusReportHover");

                    EStateReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        if(index != 0) {
                            var rowID = 0;
                            var i;
                            for (i = 0; i < EStateReportsGrid.getRowsNum(); i++){
                                rowID = EStateReportsGrid.getRowId(i);   
                                EStateReportsGrid.cells(rowID,0).setValue(i+1);
                            };
                        }
                        var sort = 'na';
                        for(i = 1; i < EStateReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        EStateReportsGrid.setColSorting(sort);
                    });

                    var srtFlg          = 0;
                    $('.tkSt_Sort_BIR').click(function() {
                        var colId       =   $(this).attr("colNum");
                        $('.tkSt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');

                        if(srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "asc";
                                EStateReportsGrid.setCustomSorting(preTally.EnquiryCountReports.str_custom,1);
                                EStateReportsGrid.sortRows(0,"int","asc");
                            }   else {
                                EStateReportsGrid.sortRows(colId,"int","asc");
                            }
                            srtFlg      = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            if(colId == 0 || colId == 1 ) {
                                order   = "desc";
                                EStateReportsGrid.setCustomSorting(preTally.EnquiryCountReports.str_custom,1);
                                EStateReportsGrid.sortRows(0,"int","desc");
                            } else {
                                EStateReportsGrid.sortRows(colId,"int","desc");
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

            EStateReportsGrid.clearAll();
            EStateReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECState.php"+rptFilterParams+"&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);

                if(EStateReportsGrid.getRowsNum() == 0 ) {
                    EStateReportsGrid.addRow("row1",['No records found'],0); 
                    EStateReportsGrid.setColspan("row1",0,EStateReportsGrid.getColumnsNum());
                    EStateReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }  
                
                preTally.EnquiryCountReports.calculateFooterValues(EStateReportsGrid);
                if(EStateReportsGrid.doesRowExist("row1"))
                    EStateReportsGrid.enableRowsHover(false);
                else
                    EStateReportsGrid.enableRowsHover(true,"bonusReportHover");
                
            });
        },
        viewMonthwiseCityReport : function(rptSt_Id) {
            if(EcityRptFlag != 1){
                var combofiltrInterval,cityFilInterval;
                EcityRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryCityRpt").setActive();
                dhxEnquiryCityReportLayout =  EnquiryCountReportsTabbar.cells("viewMonthwiseEnquiryCityRpt").attachLayout("1C");
                dhxEnquiryCityReportLayout.cells("a").hideHeader();
                                                 
                dhxEnquiryCityReportLayout.cells("a").setWidth('200');
                ECityReportsGrid     = dhxEnquiryCityReportLayout.cells("a").attachGrid();
                ECityReportsGrid.enableColSpan(true);
                notfButtonBar       = EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryCityRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='city_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='cityPaging'></span></div>",
                    height: 35
                });
                
                //ECityReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                //ECityReportsGrid.enablePaging(true,50,5,'cityPaging',false);
                //ECityReportsGrid.setPagingSkin("toolbar");
                ECityReportsGrid.init();  
                ECityReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < ECityReportsGrid.getRowsNum(); i++){
                        rowID = ECityReportsGrid.getRowId(i);   
                        ECityReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxCityRptTlbr = dhxEnquiryCityReportLayout.cells("a").attachToolbar();
                dhxCityRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxCityRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:500px;" id="EstateName"></div>' );
                dhxCityRptTlbr.setIconSize(32);

                EstateFilterCombo = new dhtmlXCombo("EstateName");

                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                    EstateFilterCombo.load(data);
                    if (!rptSt_Id) {
                        EstateFilterCombo.setComboValue(EstateFilterCombo.getSelectedValue());
                    } 
                });
                EstateFilterCombo.setOptionWidth(280);
                
                var combofiltrInterval;
                $("#EstateName").keyup(function() {
                    EstateFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                    if(!EstateFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            EstateFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                EstateFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });
                
                EstateFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateFilterCombo.getSelectedValue()), function( data ){
                        EcityCombo.load(data);
                    });
                    var EstateComboVal = EstateFilterCombo.getSelectedValue();
                    if(EstateComboVal == 0) {
                        $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                            EstateFilterCombo.load(data);
                        });
                    }
                    if(!EstateFilterCombo.getSelectedValue() && EstateFilterCombo.getComboText()) EstateComboVal = EstateFilterCombo.getComboText();
                       
                    $( "#EstateName" ).val(EstateComboVal);
                    EstateFilterCombo.setComboValue(EstateComboVal);

                    $("#EcityFlt").val(0);
                    preTally.EnquiryCountReports.applyMonthwiseCityFilter(EstateFilterCombo.getSelectedValue(),'','');
                });

                var filterValue = new Array(monthData,yearData,rptSt_Id);
                ECityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECCity.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(ECityReportsGrid.getRowsNum() == 0 ) {
                        ECityReportsGrid.addRow("row1",['No records found'],0); 
                        ECityReportsGrid.setColspan("row1",0,ECityReportsGrid.getColumnsNum());
                        ECityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        ECityReportsGrid.enableRowsHover(false);
                    } else {
                        ECityReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.city_cnt_tot').html("# : "+ECityReportsGrid.getUserData("", "TL_Count")+" ");

                    ECityReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< ECityReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        ECityReportsGrid.setColSorting(sort);
                    });

                    EcityCombo = new dhtmlXCombo("EcityFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+rptSt_Id), function( data ){
                        EcityCombo.load(data);
                    });

                    EcityCombo.setOptionWidth(350);
                    EcityCombo.attachEvent("onChange", function() {
                        $( "#EcityFlt" ).val(0);
                        var EcityComboVal = EcityCombo.getSelectedValue();
                        
                        if(EcityComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateFilterCombo.getSelectedValue()), function( data ){
                                EcityCombo.load(data);
                            });
                        }
                        if(!EcityCombo.getSelectedValue() && EcityCombo.getComboText()) EcityComboVal = EcityCombo.getComboText();
                       
                        $( "#EcityFlt" ).val(EcityComboVal);
                        EcityCombo.setComboValue(EcityComboVal);
                        preTally.EnquiryCountReports.applyMonthwiseCityFilter(EstateFilterCombo.getSelectedValue(),'','');
                    });
                    
                    preTally.EnquiryCountReports.calculateFooterValues(ECityReportsGrid);

                    $("#EcityFlt").keyup(function() {
                        EcityCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!EcityCombo.getComboText()) {
                            if(cityFilInterval) clearInterval(cityFilInterval);
                            cityFilInterval = setInterval( function() { 
                                EcityCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    EcityCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(cityFilInterval); 
                            }, 500);
                        }
                    });                   

                    ECityReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            ElocationFlag = 0;
                            rptCT_Id = rowId;
                            preTally.EnquiryCountReports.viewMonthwiseLocationReport(rowId,ECityReportsGrid.getUserData(rowId, "ST_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkCt_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum"); console.log("Sort icon click----"+"------------------"+colId +"------------------"+ srtFlg+"-------------------"+EstateFilterCombo.getSelectedValue());
                        $('.tkCt_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwiseCityFilter(EstateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwiseCityFilter(EstateFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseCityFilter : function(rptSt_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);

            if($("#EcityFlt").val() == 'undefined')  
                $("#EcityFlt").val(0);
      
      console.log("--------------------------------------------------------------------------");
      console.log(monthData+"---------------------"+yearData+"---------------------"+rptSt_Id+"---------------------"+$("#EcityFlt").val()+"---------------------"+'1'+"---------------------"+colId+"---------------------"+srtFlg);
            var filterValue = new Array(monthData,yearData,rptSt_Id,$("#EcityFlt").val(),'1',colId,srtFlg);

            ECityReportsGrid.clearAll();
            ECityReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECCity.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.city_cnt_tot').html("# : "+ECityReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.EnquiryCountReports.calculateFooterValues(ECityReportsGrid);
                if(ECityReportsGrid.getRowsNum() == 0 ) {
                    ECityReportsGrid.addRow("row1",['No records found'],0); 
                    ECityReportsGrid.setColspan("row1",0,ECityReportsGrid.getColumnsNum());
                    ECityReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    ECityReportsGrid.enableRowsHover(false);
                } else {
                    ECityReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseLocationReport : function(rptCT_Id,rptST_Id) {
            if(ElocationFlag != 1){
                var combofiltrInterval,filtrInterval;
                ElocationFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryLocationRpt").setActive();
                dhxEnquiryLocationReportLayout =  EnquiryCountReportsTabbar.cells("viewMonthwiseEnquiryLocationRpt").attachLayout("1C");
                dhxEnquiryLocationReportLayout.cells("a").hideHeader();
                                                 
                dhxEnquiryLocationReportLayout.cells("a").setWidth('200');
                ELocationReportsGrid = dhxEnquiryLocationReportLayout.cells("a").attachGrid();
                ELocationReportsGrid.setImagePath("../../codebase/imgs/");
                ELocationReportsGrid.setSkin("dhx_skyblue");
                
                ELocationReportsGrid.enableColSpan(true);
                notfLocButtonBar = EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryLocationRpt").attachStatusBar({
                    text  : "<div class='loc_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='loc_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='locPaging'></span></div>",
                    height: 35
                });
                
                //ELocationReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                //ELocationReportsGrid.enablePaging(true,50,5,'locPaging',false);
                //ELocationReportsGrid.setPagingSkin("toolbar");
                
                ELocationReportsGrid.enableColSpan(true);
                ELocationReportsGrid.attachEvent("onFilterEnd", function() {                    
                    var rowID = 0;
                    var i;
                    for (i = 0; i < ELocationReportsGrid.getRowsNum(); i++){
                        rowID = ELocationReportsGrid.getRowId(i);   
                        ELocationReportsGrid.cells(rowID,0).setValue(i+1);
                    };                                       
                });
                
                dhxLocRptTlbr = dhxEnquiryLocationReportLayout.cells("a").attachToolbar();
                dhxLocRptTlbr.addText('masterRptToolbar', '0', 'State' );
                dhxLocRptTlbr.addText('masterRptToolbar', '1', '<div style="font-weight:bold;width:400px;" id="EstateLocName"></div>' );
                dhxLocRptTlbr.addText('masterRptToolbar', '2', 'City' );
                dhxLocRptTlbr.addText('masterRptToolbar', '3', '<div style="font-weight:bold;width:400px;" id="EcityLocName"></div>' );
                dhxLocRptTlbr.setIconSize(32);

                EstateLocFilterCombo = new dhtmlXCombo("EstateLocName");
 
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    EstateLocFilterCombo.load(data); 
                });
                
                EstateLocFilterCombo.setOptionWidth(280);
                
                EstateLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);
                $("#EstateLocName").keyup(function() {

                    if(!EstateLocFilterCombo.getComboText()) {
                        if(combofiltrInterval) clearInterval(combofiltrInterval);
                        combofiltrInterval = setInterval( function() { 
                            EstateLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                EstateLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176); 
                            });
                            clearInterval(combofiltrInterval); 
                        }, 500);
                    }
                });        

                EstateLocFilterCombo.attachEvent("onChange", function() {
                    rptCT_Id = '';

                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+EstateLocFilterCombo.getSelectedValue()), function( data ){
                        EcityLocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php"), function( data ){
                        ElocCombo.load(data);
                    });
                    $("#EloctnFlt").val(0);

                    EcityLocFilterCombo.setComboValue(0);
                    if(EstateLocFilterCombo.getSelectedValue()) {
                        if(EstateLocFilterCombo.getSelectedValue() == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                EstateLocFilterCombo.load(data); 
                            }); 
                        }
                        preTally.EnquiryCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                    }
                });
                
                EcityLocFilterCombo = new dhtmlXCombo("EcityLocName");
                EcityLocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    EcityLocFilterCombo.load(data);
                    if (!rptCT_Id) {
                        EcityLocFilterCombo.setComboValue(EcityLocFilterCombo.getSelectedValue());
                    } 
                });
                
                $("#EcityLocName").keyup(function() {
                    EcityLocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                    if(!EcityLocFilterCombo.getComboText()) {
                        if(filtrInterval) clearInterval(filtrInterval);
                        filtrInterval = setInterval( function() { 
                            EcityLocFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                EcityLocFilterCombo.openSelect();
                                $(".dhxcombolist_dhx_skyblue").height(176);
                                EcityLocFilterCombo.setComboValue('');
                            });
                            clearInterval(filtrInterval); 
                        }, 500);
                    }
                });       

                EcityLocFilterCombo.attachEvent("onChange", function() {
                    $( "#EloctnFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+EcityLocFilterCombo.getSelectedValue()), function( data ){
                        ElocCombo.load(data);
                    });

                    if(EcityLocFilterCombo.getSelectedValue()) {
                        if(EcityLocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateLocFilterCombo.getSelectedValue()), function( data ){
                                EcityLocFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwiseLocationFilter(EcityLocFilterCombo.getSelectedValue());
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptCT_Id,rptST_Id);

                ELocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECLoc.php&filter="+filterValue), function() { 
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    ElocCombo = new dhtmlXCombo("EloctnFlt");
                
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+rptST_Id+"&CT_Id="+rptCT_Id), function( data ){
                        ElocCombo.load(data);
                        ElocCombo.setOptionWidth(350);

                        ElocCombo.attachEvent("onChange", function() {
                            var ElocComboVal = ElocCombo.getSelectedValue();
                            if(ElocComboVal == 0)   {
                                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+EcityLocFilterCombo.getSelectedValue()), function( data ){
                                    ElocCombo.load(data);
                                });
                            }
                            if(!ElocCombo.getSelectedValue() && ElocCombo.getComboText()) ElocComboVal = ElocCombo.getComboText();
                            $( "#EloctnFlt" ).val(ElocComboVal);
                            ElocCombo.setComboValue(ElocComboVal);
                            
                            preTally.EnquiryCountReports.applyMonthwiseLocationFilter(rptCT_Id);
                        });

                        $("#EloctnFlt").keyup(function() {
                            ElocCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+EcityLocFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!ElocCombo.getComboText()) {
                                if(combofiltrInterval) clearInterval(combofiltrInterval);
                                combofiltrInterval = setInterval( function() { 
                                    ElocCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+EcityLocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ElocCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(combofiltrInterval); 
                                }, 500);
                            }
                        });     
                    });
              
                    if(ELocationReportsGrid.getRowsNum() == 0 ) {
                        ELocationReportsGrid.addRow("row1",['No records found'],0); 
                        ELocationReportsGrid.setColspan("row1",0,ELocationReportsGrid.getColumnsNum());
                        ELocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        ELocationReportsGrid.enableRowsHover(false);
                    } else {
                        ELocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    
                    $('.loc_cnt_tot').html("# : "+ELocationReportsGrid.getUserData("", "TL_Count")+" ");

                    ELocationReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< ELocationReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        ELocationReportsGrid.setColSorting(sort);
                    });
                    preTally.EnquiryCountReports.calculateFooterValues(ELocationReportsGrid);
                    
                    ELocationReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            EplaceRptFlag = 0;
                            preTally.EnquiryCountReports.viewMonthwisePlaceReport(rowId,ELocationReportsGrid.getUserData(rowId, "ST_Id"),ELocationReportsGrid.getUserData(rowId, "CT_Id"));
                        }   else return false;
                    });
                    
                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkLC_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkLC_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwiseLocationFilter(EcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwiseLocationFilter(EcityLocFilterCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseLocationFilter : function(rptCT_Id,colId,srtFlg) {
            preTally.Settings.progressOn(true, dhxLayout, null);
            if($("#EloctnFlt").val() == 'undefined')  
                $("#EloctnFlt").val(0);
            
            var filterValue = new Array(monthData,yearData,EcityLocFilterCombo.getSelectedValue(),EstateLocFilterCombo.getSelectedValue(),$("#EloctnFlt").val(),'1',colId,srtFlg);    
            ELocationReportsGrid.clearAll();
            ELocationReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECLoc.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.loc_cnt_tot').html("# : "+ELocationReportsGrid.getUserData("", "TL_Count")+" ");
                               
                preTally.EnquiryCountReports.calculateFooterValues(ELocationReportsGrid);
                if(ELocationReportsGrid.getRowsNum() == 0 ) {
                    ELocationReportsGrid.addRow("row1",['No records found'],0); 
                    ELocationReportsGrid.setColspan("row1",0,ELocationReportsGrid.getColumnsNum());
                    ELocationReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    ELocationReportsGrid.enableRowsHover(false);
                } else {
                    ELocationReportsGrid.enableRowsHover(true,"bonusReportHover");
                } 
            });
        },
        viewMonthwisePlaceReport : function(rowId,rptST_Id,rptCT_Id) {
            if(EplaceRptFlag != 1){
                var combofiltrInterval,EstateComboFiltInterval,EcityComboFiltInterval,ElocComboFiltInterval;
                EplaceRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryPlaceRpt").setActive();
                dhxEnquiryPlaceReportLayout =  EnquiryCountReportsTabbar.cells("viewMonthwiseEnquiryPlaceRpt").attachLayout("1C");
                dhxEnquiryPlaceReportLayout.cells("a").hideHeader();
                                                 
                dhxEnquiryPlaceReportLayout.cells("a").setWidth('200');
                EplaceReportsGrid     = dhxEnquiryPlaceReportLayout.cells("a").attachGrid();
                EplaceReportsGrid.enableColSpan(true);
                notfButtonBar       = EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryPlaceRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='place_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='placePaging'></span></div>",
                    height: 35
                });
                
                //EplaceReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
               // EplaceReportsGrid.enablePaging(true,50,5,'placePaging',false);
              //  EplaceReportsGrid.setPagingSkin("toolbar");
                EplaceReportsGrid.init();  

                dhxEnquiryPlaceRptTlbr = dhxEnquiryPlaceReportLayout.cells("a").attachToolbar();
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '0', 'State' );
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '1', '<div style="font-weight:bold;width:300px;" id="EstatePLName"></div>' );
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '2', 'City' );
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '3', '<div style="font-weight:bold;width:300px;" id="EcityPLName"></div>' );
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '4', 'Location' );
                dhxEnquiryPlaceRptTlbr.addText('plRptToolbar', '5', '<div style="font-weight:bold;width:300px;" id="ElocPLName"></div>' );
                dhxEnquiryPlaceRptTlbr.setIconSize(32);

                EstatePlaceFilterCombo = new dhtmlXCombo("EstatePLName");
                EstatePlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    EstatePlaceFilterCombo.load(data,function() {
                        EstatePlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                        $("#EstatePLName").keyup(function() {
                            if(!EstatePlaceFilterCombo.getComboText()) {
                                if(EstateComboFiltInterval) clearInterval(EstateComboFiltInterval);
                                EstateComboFiltInterval = setInterval( function() { 
                                    EstatePlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        EstatePlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(EstateComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    });
                });
                
                EcityPlaceFilterCombo = new dhtmlXCombo("EcityPLName");
                EcityPlaceFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    EcityPlaceFilterCombo.load(data,function() {

                        $("#EcityPLName").keyup(function() {
                            EcityPlaceFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!EcityPlaceFilterCombo.getComboText()) {
                                if(EcityComboFiltInterval) clearInterval(EcityComboFiltInterval);
                                EcityComboFiltInterval = setInterval( function() { 
                                    EcityPlaceFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        EcityPlaceFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(EcityComboFiltInterval); 
                                }, 500);
                            }
                        });    
                    }); 
                });
                
                ElocFilterCombo = new dhtmlXCombo("ElocPLName");
                ElocFilterCombo.setOptionWidth(280);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rowId), function( data ){
                    ElocFilterCombo.load(data,function() {
                        
                        $("#ElocPLName").keyup(function() {
                            ElocFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&flag=1"),false);
                            if(!ElocFilterCombo.getComboText()) {
                                if(ElocComboFiltInterval) clearInterval(ElocComboFiltInterval);
                                ElocComboFiltInterval = setInterval( function() { 
                                    ElocFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ElocFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(ElocComboFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                EstatePlaceFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        EcityPlaceFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        ElocFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()), function( data ){
                        EplaceCombo.load(data);
                    });
                    
                    $("#EplaceFlt").val(0);
                    
                    if(EstatePlaceFilterCombo.getSelectedValue()) {
                        if(EstatePlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                EstatePlaceFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),'','','','',''); 
                    }
                });
                
                EcityPlaceFilterCombo.attachEvent("onChange", function() {
                    $( "#EplaceFlt" ).val(0);
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        ElocFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()), function( data ){
                        EplaceCombo.load(data);
                    });
                    
                    if(EcityPlaceFilterCombo.getSelectedValue()) {
                        if(EcityPlaceFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()), function( data ){
                                EcityPlaceFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),EcityPlaceFilterCombo.getSelectedValue(),'','','','');
                    }
                });
               
                ElocFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ElocFilterCombo.getSelectedValue()), function( data ){
                        EplaceCombo.load(data);
                    });
                    if(ElocFilterCombo.getSelectedValue()) {
                        if(ElocFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ElocFilterCombo.getSelectedValue()), function( data ){
                                ElocFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),EcityPlaceFilterCombo.getSelectedValue(),ElocFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rowId);

                EplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECPlace.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(EplaceReportsGrid.getRowsNum() == 0 ) {
                        EplaceReportsGrid.addRow("row1",['No records found'],0); 
                        EplaceReportsGrid.setColspan("row1",0,EplaceReportsGrid.getColumnsNum());
                        EplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        EplaceReportsGrid.enableRowsHover(false);
                    } else {
                        EplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.place_cnt_tot').html("# : "+EplaceReportsGrid.getUserData("", "TL_Count")+" ");

                    EplaceReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i< EplaceReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        EplaceReportsGrid.setColSorting(sort);
                    });

                    EplaceCombo = new dhtmlXCombo("EplaceFlt");
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ALC_Id="+rowId), function( data ){
                        EplaceCombo.load(data);
                    });

                    EplaceCombo.setOptionWidth(350);
                    EplaceCombo.attachEvent("onChange", function() {
                        $( "#EplaceFlt" ).val(0);
                        var EplaceComboVal = EplaceCombo.getSelectedValue();
                        
                        if(EplaceComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php"), function( data ){
                                EplaceCombo.load(data);
                            });
                        }
                    
                        if(!EplaceCombo.getSelectedValue() && EplaceCombo.getComboText()) EplaceComboVal = EplaceCombo.getComboText();
                        $( "#EplaceFlt" ).val(EplaceComboVal);
                        EplaceCombo.setComboValue(EplaceComboVal);
                        preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),EcityPlaceFilterCombo.getSelectedValue(),ElocFilterCombo.getSelectedValue(),$("#EplaceFlt").val(),'','');
                    });
                    preTally.EnquiryCountReports.calculateFooterValues(EplaceReportsGrid);
  
                    $("#EplaceFlt").keyup(function() {
                        EplaceCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ElocFilterCombo.getSelectedValue()+"&flag=1"),false);
                        if(!EplaceCombo.getComboText()) {
                            if(combofiltrInterval) clearInterval(combofiltrInterval);
                            combofiltrInterval = setInterval( function() { 
                                EplaceCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstatePlaceFilterCombo.getSelectedValue()+"&CT_Id="+EcityPlaceFilterCombo.getSelectedValue()+"&ALC_Id="+ElocFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    EplaceCombo.openSelect();
                                    $(".dhxcombolist_dhx_skyblue").height(176);
                                });
                                clearInterval(combofiltrInterval); 
                            }, 500);
                        }
                    });  
  
                    EplaceReportsGrid.attachEvent("onRowSelect",function(rowId){
                        if(rowId != 'row1') {
                            EstreetRptFlag = 0;
                            preTally.EnquiryCountReports.viewMonthwiseStreetReport(rowId,EplaceReportsGrid.getUserData(rowId, "ST_Id"),EplaceReportsGrid.getUserData(rowId, "CT_Id"),EplaceReportsGrid.getUserData(rowId, "ALC_Id"));

                        }   else return false;
                    });

                    var srtFlg  = 0;
                    colId       = '';
                    $('.tkPL_Sort_BIR').click(function() {
                        colId = $(this).attr("colNum");
                        $('.tkPL_Sort_BIR').attr("src", 'images/icon/sort-ascending-icon.png');
                        if(srtFlg == 0){
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),EcityPlaceFilterCombo.getSelectedValue(),ElocFilterCombo.getSelectedValue(),EplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 1;
                        }else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwisePlaceFilter(EstatePlaceFilterCombo.getSelectedValue(),EcityPlaceFilterCombo.getSelectedValue(),ElocFilterCombo.getSelectedValue(),EplaceCombo.getSelectedValue(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwisePlaceFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,'1',colId,srtFlg);

            EplaceReportsGrid.clearAll();
            EplaceReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECPlace.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.place_cnt_tot').html("# : "+EplaceReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.EnquiryCountReports.calculateFooterValues(EplaceReportsGrid);
                if(EplaceReportsGrid.getRowsNum() == 0 ) {
                    EplaceReportsGrid.addRow("row1",['No records found'],0); 
                    EplaceReportsGrid.setColspan("row1",0,EplaceReportsGrid.getColumnsNum());
                    EplaceReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    EplaceReportsGrid.enableRowsHover(false);
                } else {
                    EplaceReportsGrid.enableRowsHover(true,"bonusReportHover");
                }
            });
        },
        viewMonthwiseStreetReport : function(rowId,rptST_Id,rptCT_Id,rptACL_Id) {
            if(EstreetRptFlag != 1){
                var stateFiltInterval,cityFiltInterval,locFiltInterval,placeFiltInterval,streetFiltInterval;
                EstreetRptFlag = 1; 
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryStreetRpt").setActive();
                dhxEnquiryStreetReportLayout =  EnquiryCountReportsTabbar.cells("viewMonthwiseEnquiryStreetRpt").attachLayout("1C");
                dhxEnquiryStreetReportLayout.cells("a").hideHeader();
                                                 
                dhxEnquiryStreetReportLayout.cells("a").setWidth('200');
                EstreetReportsGrid     = dhxEnquiryStreetReportLayout.cells("a").attachGrid();
                EstreetReportsGrid.enableColSpan(true);
                EnquiryCountReportsTabbar.tabs("viewMonthwiseEnquiryStreetRpt").attachStatusBar({
                    text  : "<div class='exp_txt_secl'>\
                                <div style= 'float: left;font-weight: bold;height: 27px;width: 130px;text-align: left;' class='street_cnt_tot'># : 0</div>\
                            </div>\
                            <div style= 'float: right;' ><span id='streetPaging'></span></div>",
                    height: 35
                });
                
                //EstreetReportsGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
               // EstreetReportsGrid.enablePaging(true,50,5,'streetPaging',false);
                //EstreetReportsGrid.setPagingSkin("toolbar");
                EstreetReportsGrid.init();  
           
                dhxStreetRptTlbr = dhxEnquiryStreetReportLayout.cells("a").attachToolbar();
                dhxStreetRptTlbr.addText('stRptToolbar', '0', 'State' );
                dhxStreetRptTlbr.addText('stRptToolbar', '1', '<div style="font-weight:bold;width:230px;" id="EstateSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '2', 'City' );
                dhxStreetRptTlbr.addText('stRptToolbar', '3', '<div style="font-weight:bold;width:230px;" id="EcitySTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '4', 'Location' );
                dhxStreetRptTlbr.addText('stRptToolbar', '5', '<div style="font-weight:bold;width:230px;" id="ElocSTName"></div>' );
                dhxStreetRptTlbr.addText('stRptToolbar', '6', 'Place' );
                dhxStreetRptTlbr.addText('stRptToolbar', '7', '<div style="font-weight:bold;width:230px;" id="EplaceSTName"></div>' );
                dhxStreetRptTlbr.setIconSize(32);

                EstateStreetFilterCombo = new dhtmlXCombo("EstateSTName");
                EstateStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&ST_Id="+rptST_Id), function( data ){
                    EstateStreetFilterCombo.load(data,function() {

                        $("#EstateSTName").keyup(function() {
                            EstateStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"),false);

                            if(!EstateStreetFilterCombo.getComboText()) {
                                if(stateFiltInterval) clearInterval(stateFiltInterval);
                                stateFiltInterval = setInterval( function() { 
                                    EstateStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/states.php&type=rpt&flag=1"), function() {
                                        EstateStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(stateFiltInterval); 
                                }, 500);
                            }
                        });   
                    });
                });
                
                EcityStreetFilterCombo = new dhtmlXCombo("EcitySTName");
                EcityStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id), function( data ){
                    EcityStreetFilterCombo.load(data,function() {

                        $("#EcitySTName").keyup(function() {
                            EcityStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!EcityStreetFilterCombo.getComboText()) {
                                if(cityFiltInterval) clearInterval(cityFiltInterval);
                                cityFiltInterval = setInterval( function() { 
                                    EcityStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        EcityStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(cityFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                ElocStreetFilterCombo = new dhtmlXCombo("ElocSTName");
                ElocStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id), function( data ){
                    ElocStreetFilterCombo.load(data,function() {
                    
                        $("#ElocSTName").keyup(function() {
                            ElocStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                            if(!ElocStreetFilterCombo.getComboText()) {
                                if(locFiltInterval) clearInterval(placeFiltInterval);
                                locFiltInterval = setInterval( function() { 
                                ElocStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                        ElocStreetFilterCombo.openSelect();
                                        $(".dhxcombolist_dhx_skyblue").height(176);
                                    });
                                    clearInterval(locFiltInterval); 
                                }, 500);
                            }
                        });  
                    }); 
                });
                
                EplaceStreetFilterCombo = new dhtmlXCombo("EplaceSTName");
                EplaceStreetFilterCombo.setOptionWidth(90);
                $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&CT_Id="+rptCT_Id+"&ST_Id="+rptST_Id+"&ALC_Id="+rptACL_Id+"&PL_Id="+rowId), function( data ){
                    EplaceStreetFilterCombo.load(data,function() {
                   
                        $("#EplaceSTName").keyup(function() {
                           EplaceStreetFilterCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                           if(!EplaceStreetFilterCombo.getComboText()) {
                               if(placeFiltInterval) clearInterval(placeFiltInterval);
                               placeFiltInterval = setInterval( function() { 
                               EplaceStreetFilterCombo.load(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                       EplaceStreetFilterCombo.openSelect();
                                       $(".dhxcombolist_dhx_skyblue").height(176);
                                   });
                                   clearInterval(placeFiltInterval); 
                               }, 500);
                           }
                       });  
                    }); 
                });
                
                EstateStreetFilterCombo.attachEvent("onChange", function() {
                   
                    $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateStreetFilterCombo.getSelectedValue()), function( data ){
                        EcityStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()), function( data ){
                        ElocStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()), function( data ){
                        EplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()), function( data ){
                        EstreetCombo.load(data);
                    });
                    
                    $("#EstreetFlt").val(0);
                    
                    if(EstateStreetFilterCombo.getSelectedValue()) {
                        if(EstateStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/states.php&type=rpt"), function( data ){
                                EstateStreetFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),'','','','','',''); 
                    }
                });
                
                EcityStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()), function( data ){
                        ElocStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()), function( data ){
                        EplaceStreetFilterCombo.load(data);
                    });
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()), function( data ){
                        EstreetCombo.load(data);
                    });
                    $("#EstreetFlt").val(0);
                    if(EcityStreetFilterCombo.getSelectedValue()) {
                        if(EcityStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/cities.php&type=rpt&ST_Id="+EstateStreetFilterCombo.getSelectedValue()), function( data ){
                                EcityStreetFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),'','','','','');
                    }
                });
               
                ElocStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()), function( data ){
                        EplaceStreetFilterCombo.load(data);
                    });
                    
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()), function( data ){
                        EstreetCombo.load(data);
                    });
                    $("#EstreetFlt").val(0);
                    if(ElocStreetFilterCombo.getSelectedValue()) {
                        if(ElocStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressLocations.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()), function( data ){
                                ElocStreetFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),ElocStreetFilterCombo.getSelectedValue(),'','','','');
                    }
                });
                
                EplaceStreetFilterCombo.attachEvent("onChange", function() {
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&PL_Id="+EplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        EstreetCombo.load(data);
                    });
                    $("#EstreetFlt").val(0);
                    if(EplaceStreetFilterCombo.getSelectedValue()) {
                        if(EplaceStreetFilterCombo.getSelectedValue() == 0 ) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressPlaces.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()), function( data ){
                                EplaceStreetFilterCombo.load(data);
                            });
                        }
                        preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),ElocStreetFilterCombo.getSelectedValue(),EplaceStreetFilterCombo.getSelectedValue(),'','','');
                    }
                });
                
                var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptACL_Id,rowId);

                EstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECStreet.php&filter="+filterValue), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    if(EstreetReportsGrid.getRowsNum() == 0 ) {
                        EstreetReportsGrid.addRow("row1",['No records found'],0); 
                        EstreetReportsGrid.setColspan("row1",0,EstreetReportsGrid.getColumnsNum());
                        EstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        EstreetReportsGrid.enableRowsHover(false);
                    } else {
                        EstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
                    }   
                    $('.street_cnt_tot').html("# : "+EstreetReportsGrid.getUserData("", "TL_Count")+" ");

                    EstreetReportsGrid.attachEvent("onAfterSorting", function(index,type,direction){
                        var sort = 'na';
                        for(i = 1; i < EstreetReportsGrid.getColumnsNum(); i++)
                            sort += ',na';
                        EstreetReportsGrid.setColSorting(sort);
                    });

                    EstreetCombo = new dhtmlXCombo("EstreetFlt");
                    EstreetCombo.setOptionWidth(350);
                    $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&PL_Id="+EplaceStreetFilterCombo.getSelectedValue()), function( data ){
                        EstreetCombo.load(data);
                    });

                    EstreetCombo.attachEvent("onChange", function() {
                        $( "#EstreetFlt" ).val(0);
                        var EstreetComboVal = EstreetCombo.getSelectedValue();
                        if(EstreetComboVal == 0) {
                            $.post(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&PL_Id="+EplaceStreetFilterCombo.getSelectedValue()), function( data ){
                                EstreetCombo.load(data);
                            });
                        }
                        if(!EstreetCombo.getSelectedValue() && EstreetCombo.getComboText()) EstreetComboVal = EstreetCombo.getComboText();
                        $( "#EstreetFlt" ).val(EstreetComboVal);
                        EstreetCombo.setComboValue(EstreetComboVal);
                        preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),ElocStreetFilterCombo.getSelectedValue(),EplaceStreetFilterCombo.getSelectedValue(),$("#EstreetFlt").val(),'','');
                    });
                    
                    preTally.EnquiryCountReports.calculateFooterValues(EstreetReportsGrid);
               
                    $("#EstreetFlt").keyup(function() {
                        EstreetCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&PL_Id="+EplaceStreetFilterCombo.getSelectedValue()+"&flag=1"),false);

                        if(!EstreetCombo.getComboText()) {
                            if(streetFiltInterval) clearInterval(streetFiltInterval);
                            streetFiltInterval = setInterval( function() { 
                            EstreetCombo.load(preTally.Initialize.encryptURL("requisites/addressStreets.php&ST_Id="+EstateStreetFilterCombo.getSelectedValue()+"&CT_Id="+EcityStreetFilterCombo.getSelectedValue()+"&ALC_Id="+ElocStreetFilterCombo.getSelectedValue()+"&PL_Id="+EplaceStreetFilterCombo.getSelectedValue()+"&flag=1"), function() {
                                    EstreetCombo.openSelect();
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
                            preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),ElocStreetFilterCombo.getSelectedValue(),EplaceStreetFilterCombo.getSelectedValue(),$("#EstreetFlt").val(),colId,srtFlg);
                            srtFlg = 1;
                        } else{
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            preTally.EnquiryCountReports.applyMonthwiseStreetFilter(EstateStreetFilterCombo.getSelectedValue(),EcityStreetFilterCombo.getSelectedValue(),ElocStreetFilterCombo.getSelectedValue(),EplaceStreetFilterCombo.getSelectedValue(),$("#EstreetFlt").val(),colId,srtFlg);
                            srtFlg = 0;
                        }
                    });
                });
            }
        },
        applyMonthwiseStreetFilter : function(rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,colId,srtFlg) {

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filterValue = new Array(monthData,yearData,rptST_Id,rptCT_Id,rptALC_Id,rptPL_Id,rptSR_Id,'1',colId,srtFlg);

            EstreetReportsGrid.clearAll();
            EstreetReportsGrid.load(preTally.Initialize.encryptURL("requisites/reportMonthwiseECStreet.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                $('.street_cnt_tot').html("# : "+EstreetReportsGrid.getUserData("", "TL_Count")+" ");
                preTally.EnquiryCountReports.calculateFooterValues(EstreetReportsGrid);
                if(EstreetReportsGrid.getRowsNum() == 0 ) {
                    EstreetReportsGrid.addRow("row1",['No records found'],0); 
                    EstreetReportsGrid.setColspan("row1",0,EstreetReportsGrid.getColumnsNum());
                    EstreetReportsGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    EstreetReportsGrid.enableRowsHover(false);
                } else {
                    EstreetReportsGrid.enableRowsHover(true,"bonusReportHover");
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