;(function($, window, undefined) {
    preTally.BusinessReportManage = {
        viewBusinessReportManage : function() {
              if (!dhxMiddleBlockTabs.cells("manage_businessrprt")) {
                dhxMiddleBlockTabs.addTab("manage_businessrprt", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Business Report - Manage&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='manageBSRptRfrsh'/>", 305);
                dhxMiddleBlockTabs.tabs("manage_businessrprt").setActive();
                
                $(".manageBSRptRfrsh").click(function(){
                    preTally.BusinessReportManage.filterReport();
                });
                
                dhxManageBSReportsLayout    = dhxMiddleBlockTabs.cells("manage_businessrprt").attachLayout("1C");
                dhxManageBSReportsLayout.cells("a").hideHeader();
                manageBusinessRprtTlbr      = dhxManageBSReportsLayout.cells("a").attachToolbar();                
                manageBusinessRprtTlbr.setIconsPath("images/icon/default_18/");
                
                var Days_Options    = [];
                var Months_Options  = [];
                var Years_Options   = [];
                monthNames          = [ 
                        "January", "February", "March","April", "May", "June",
                        "July", "August", "September","October", "November", "December"
                ];
                    
                var todayDt         = Date.today().getDate();  
                for (i = todayDt; i >= 1 ; i--) {
                    if(i < 10){
                        i   =   '0'+i;
                    }    
                    if(i == todayDt){
                        Days_Options.push([i,'obj','Today',"calendar_D.png"]);
                    }else{
                        Days_Options.push([i,'obj',i,"calendar_D.png"]);
                    }
                }
                //Date.today().getMonth()+1
                for (i = 12; i >=1 ; i--) {
                    j   = i;    
                    if(j < 10){
                        j   = '0'+i;
                    } 
                    Months_Options.push(['m'+j,'obj',monthNames[i-1],"calendar_M.png"]);
                }
                
                for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                    Years_Options.push([i,'obj',i,"calendar_Y.png"]);
                }
                
                manageBusinessRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                manageBusinessRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                manageBusinessRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
                manageBusinessRprtTlbr.addSeparator();
                          
                manageBusinessRprtTlbr.addText("text_from", null, "From");
                manageBusinessRprtTlbr.addInput("rpt_date_from", null, "", 75);
                manageBusinessRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                manageBusinessRprtTlbr.addSeparator();
                
                manageBusinessRprtTlbr.addText("text_till", null, "Till");
                manageBusinessRprtTlbr.addInput("rpt_date_till", null, "", 75);
                manageBusinessRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                manageBusinessRprtTlbr.addSeparator();
                
                manageBusinessRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");

                var ptRpTb_Inp_Frm = manageBusinessRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(manageBusinessRprtTlbr.getValue("rpt_date_till")) preTally.BusinessReportManage.setSens(ptRpTb_Inp_Til, "max");
                };
                
                var ptRpTb_Inp_Til = manageBusinessRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(manageBusinessRprtTlbr.getValue("rpt_date_from")) preTally.BusinessReportManage.setSens(ptRpTb_Inp_Frm, "min");
                };    
                
                manageBusinessRprtTlbr.attachEvent("onClick", function(id){  
                    var pId = manageBusinessRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        manageBusinessRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        manageBusinessRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        manageBusinessRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        manageBusinessRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        manageBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        manageBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReportManage.filterReport();
                    }
                    
                    if(pId == 'rpt_month_filter') {
                        id              = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(manageBusinessRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        manageBusinessRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        manageBusinessRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        manageBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //manageBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReportManage.filterReport();
                    } 
                    
                    if(pId == 'rpt_year_filter') {
                        var tmpDate     = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        manageBusinessRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        manageBusinessRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        manageBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        manageBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.BusinessReportManage.filterReport();
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        manageBusinessRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        manageBusinessRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        manageBusinessRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BusinessReportManage.filterReport();
                    }  
                    
//                    if(id == 'excel_export') {
//                        preTally.BusinessReportManage.exportBusinessReport();
//                    }
                });
                
                var date    =   new Date();
                var month   =   date.getMonth();
                tDate       =   Date.today().toString("dd.MM.yyyy"); 
                manageBusinessRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                manageBusinessRprtTlbr.setValue('rpt_date_till', tDate);
                manageBusinessRprtTlbr.setItemText('rpt_month_filter',monthNames[month]);
                 
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                manageBusinessRprtTlbr.setAlign('right');
                var tb_data_txt = ' <div class="tb_data_txt_secl">\
                                    <div class="mngBS_cnt_tot"># : 0</div></div><div id="mngBussRpt_paging"></div>';
                
                dhxManageBSReportsLayout.cells("a").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 30             // custom height
                });
                preTally.BusinessReportManage.filterReport();
            } else {
                dhxMiddleBlockTabs.tabs("manage_businessrprt").setActive();
            }
           
        },
        filterReport: function(id) { 
            var filtrInterval ;
            preTally.Settings.progressOn(true, dhxLayout, null);

            chartBusinessFilterParams = 'f='+manageBusinessRprtTlbr.getValue("rpt_date_from")+'&t='+manageBusinessRprtTlbr.getValue("rpt_date_till");

            dhxManageBusinessRprtGrid = dhxManageBSReportsLayout.cells("a").attachGrid();                        
            dhxManageBusinessRprtGrid.setHeader("SlNo,<input type='text' class='mng_bussrpt_filter' id ='TK_Id' style='width: 90%;' placeholder='Track ID'>,<input type='text' class='mng_bussrpt_filter' id ='itmMng' style='width: 90%;' placeholder='Enter name of Income to search . . .'>,<input type='text' class='mng_bussrpt_filter' id='amtMng' style='width: 90%;' placeholder='Amount'>,<input type='text' class='mng_bussrpt_filter' id ='addedByMng' style='width: 90%;' placeholder='Added By'>,<input type='text' class='mng_bussrpt_filter' id='brnMng' style='width: 90%;' placeholder='Branch'>,<select id='hiddenIds'><option value=''>All</option><option value ='1'>Hidden</option><option value = '2'>Not Hidden</option></select>");
            dhxManageBusinessRprtGrid.setInitWidths("60,140,*,150,200,200,150,90");
            dhxManageBusinessRprtGrid.setColAlign("center,left,left,right,left,left,center");
            dhxManageBusinessRprtGrid.setColTypes("ro,ro,ro,ro,ro,ro,ch");
            dhxManageBusinessRprtGrid.setColSorting("na,na,na,na,na,na,na");
            dhxManageBusinessRprtGrid.init();
            dhxManageBusinessRprtGrid.setImagePath("assets/grid/codebase/imgs/");
            dhxManageBusinessRprtGrid.setSkin("dhx_skyblue");
            dhxManageBusinessRprtGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
            dhxManageBusinessRprtGrid.enablePaging(true,50,5,"mngBussRpt_paging",true);
            dhxManageBusinessRprtGrid.setPagingSkin("toolbar", "dhx_skyblue");
            dhxManageBusinessRprtGrid.enableTooltips("false,false,false,false,false,false,true");
            dhxManageBusinessRprtGrid.enableColSpan(true);

            $( ".mng_bussrpt_filter" ).keyup(function() {
                if(filtrInterval) clearInterval(filtrInterval);

                filtrInterval = setInterval( function() { 
                    preTally.BusinessReportManage.applyFilter(); 
                    clearInterval(filtrInterval); 
                }, 500);
            });
            
            $( "#hiddenIds" ).change(function() {
                preTally.BusinessReportManage.applyFilter();
            });
                
            dhxManageBusinessRprtGrid.attachEvent("onCheck", function(rId,cInd,state){
                $.post(preTally.Initialize.encryptURL("warehouse/trackManageIdsSave.php"),
                { TR_Id : rId, state : state}, 
                function( data ){
                    dhtmlx.message({text: "Successfully updated track ID in hidden list"});
                    preTally.BusinessReportManage.applyFilter();
                });
            });
            
            dhxManageBusinessRprtGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBussManage.php&"+chartBusinessFilterParams), function() {
                $('.mngBS_cnt_tot').html("# : "+dhxManageBusinessRprtGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });

        },
        applyFilter : function() {
            preTally.Settings.progressOn(true, dhxLayout, null);
            var amount      = $("#amtMng").val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#TK_Id').val(), $('#itmMng').val(), $('#addedByMng').val(),$('#brnMng').val(),amount,$("#hiddenIds").val());
            dhxManageBusinessRprtGrid.clearAll();
            
            if(dhxManageBusinessRprtGrid.doesRowExist("0"))
                dhxManageBusinessRprtGrid.deleteRow("0");
            
            dhxManageBusinessRprtGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportBussManage.php&"+chartBusinessFilterParams+"&filter="+filterValue), function() { 
                $('.mngBS_cnt_tot').html("# : "+dhxManageBusinessRprtGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
    };
})(jQuery, this);