;(function($, window, undefined){
    preTally.PettyCash = {
        view_pettycashreports : function(){
            if (!dhxMiddleBlockTabs.cells("view_pettycashreports")) {
                
                dhxMiddleBlockTabs.addTab("view_pettycashreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Petty Cash Report", 170);
                dhxMiddleBlockTabs.tabs("view_pettycashreports").setActive();
                
                pettyCashRptLayout = dhxMiddleBlockTabs.cells("view_pettycashreports").attachLayout('1C');
                pettyCashRptLayout.cells("a").hideHeader();
                
                ptPettyCashRptTlbr = pettyCashRptLayout.cells("a").attachToolbar();                
                ptPettyCashRptTlbr.setIconsPath("images/icon/default_18/");
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
                ptPettyCashRptTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptPettyCashRptTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptPettyCashRptTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select'); 
                ptPettyCashRptTlbr.addSeparator();
                ptPettyCashRptTlbr.addText("text_from", null, "From");
                ptPettyCashRptTlbr.addInput("rpt_date_from", null, "", 75);
                ptPettyCashRptTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                ptPettyCashRptTlbr.addSeparator();
                ptPettyCashRptTlbr.addText("text_till", null, "Till");
                ptPettyCashRptTlbr.addInput("rpt_date_till", null, "", 75);
                ptPettyCashRptTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                
                ptPettyCashRptTlbr.addSeparator();
                
                ptPettyCashRptTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
//                ptPettyCashRptTlbr.addSeparator();
//                ptPettyCashRptTlbr.addButton("excel_export", null , "Export", "excel.png");


                var ptRpTb_Inp_Frm = ptPettyCashRptTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptPettyCashRptTlbr.getValue("rpt_date_till")) preTally.PettyCash.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptPettyCashRptTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptPettyCashRptTlbr.getValue("rpt_date_from")) preTally.PettyCash.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 ptPettyCashRptTlbr.attachEvent("onClick", function(id){  
                   var pId = ptPettyCashRptTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        ptPettyCashRptTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptPettyCashRptTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        ptPettyCashRptTlbr.setValue('rpt_date_from', dateToday, false);
                        ptPettyCashRptTlbr.setValue('rpt_date_till', dateToday, false);
                        ptPettyCashRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptPettyCashRptTlbr.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = pettyCashRptTabbar.getActiveTab();
                        preTally.PettyCash.applyFilter(actvId);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptPettyCashRptTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptPettyCashRptTlbr.setValue('rpt_date_from', firstDay, false);
                        ptPettyCashRptTlbr.setValue('rpt_date_till', lastDay, false);
                        ptPettyCashRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptPettyCashRptTlbr.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = pettyCashRptTabbar.getActiveTab();
                        preTally.PettyCash.applyFilter(actvId);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay   = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay    = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        ptPettyCashRptTlbr.setValue('rpt_date_from', firstDay, false);
                        ptPettyCashRptTlbr.setValue('rpt_date_till', lastDay, false);
                        ptPettyCashRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptPettyCashRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        
                        var actvId = pettyCashRptTabbar.getActiveTab();
                        preTally.PettyCash.applyFilter(actvId);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        ptPettyCashRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptPettyCashRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptPettyCashRptTlbr.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = pettyCashRptTabbar.getActiveTab();
                        preTally.PettyCash.applyFilter(actvId);
                    }  
                    if(id == 'excel_export'){
                        preTally.PettyCash.exportCashBalanceReport();
                    }
                    
//                    var actvId = pettyCashRptTabbar.getActiveTab();
//                    preTally.PettyCash.applyFilter(actvId);
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                ptPettyCashRptTlbr.setAlign('right');
                
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp==4)
                {   var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    var month=date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    ptPettyCashRptTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    ptPettyCashRptTlbr.setValue('rpt_date_till', tDate);
                    ptPettyCashRptTlbr.setItemText('rpt_month_filter',m_names[month]);
                }
                else
                {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    ptPettyCashRptTlbr.setValue('rpt_date_till', cDate);
                    ptPettyCashRptTlbr.setValue('rpt_date_from', cDate);
                    ptPettyCashRptTlbr.setItemText('rpt_day_filter', 'Today');
                }
                
                
                pettyCashRptTabbar = pettyCashRptLayout.cells("a").attachTabbar();
                pettyCashRptTabbar.addTab("view_pettyCashItemReports","Petty Cash Item Reports",170);
                pettyCashRptTabbar.tabs("view_pettyCashItemReports").setActive();

                pettyCashItemRptLayout = pettyCashRptTabbar.cells("view_pettyCashItemReports").attachLayout('1C');
                pettyCashItemRptLayout.cells("a").hideHeader();
                
                pettyCashItemRptLayout.cells("a").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'><div class='pc_cnt_tot' style='float:left;'># : 0</div></div><div id='ptyRpt_paging'></div>",
                    height: 25             // custom heightattachStatusBar
                });

                pettyCashItemGrid = pettyCashItemRptLayout.cells("a").attachGrid();                
                pettyCashItemGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'ptycsh_select_filter' id='iePCRF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  class='ptycshrpt_text_filter' id ='itmPCRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,Available Balance, Amount Paid,<input type='text'  class='ptycshrpt_text_filter' id='brnPCRF' style='width: 90%;' placeholder='Branch'> ,<input type='text'  class='ptycshrpt_text_filter' id='addPCRF' style='width: 90%;' placeholder=' Added By'>,<input type='text'  class='ptycshrpt_text_filter' id='recvPCRF' style='width: 90%;' placeholder=' Received By'>,Date,View Entries");
                //pettyCashItemGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                pettyCashItemGrid.setInitWidths("50,80,*,100,100,150,150,150,150,50")
                pettyCashItemGrid.setColAlign("center,center,left,left,right,left,left,left,left,center");
                pettyCashItemGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                
                pettyCashItemGrid.setColSorting("na,na,na,na,na,na,na,na,na,na")
                pettyCashItemGrid.init();                
                pettyCashItemGrid.setImagePath("assets/grid/codebase/imgs/");
                pettyCashItemGrid.setSkin("dhx_skyblue")
                pettyCashItemGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                pettyCashItemGrid.enablePaging(true,50,5,"ptyRpt_paging",true);
                pettyCashItemGrid.setPagingSkin("toolbar", "dhx_skyblue");
                //pettyCashItemGrid.enableSmartRendering(true,50);
                pettyCashItemGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true");
                pettyCashItemGrid.enableColSpan(true);

                preTally.Settings.progressOn(true, dhxLayout, null);
                var filtrInterval;
                chartPettyCashFilterParams = '&f='+ptPettyCashRptTlbr.getValue("rpt_date_from")+'&t='+ptPettyCashRptTlbr.getValue("rpt_date_till");
                pettyCashItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportPettyCashItem.php&"+chartPettyCashFilterParams), function() {
                   
                    $('.pc_cnt_tot').html("# : "+pettyCashItemGrid.getUserData("", "TL_Count")+" ");
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    $( ".ptycsh_select_filter" ).change(function() {
                        preTally.PettyCash.applyFilter('view_pettyCashItemReports');
                    });


                    $( ".ptycshrpt_text_filter" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.PettyCash.applyFilter('view_pettyCashItemReports'); 
                            clearInterval(filtrInterval); 
                        }, 500);

                    });

                });
                          
                pettyCashItemGrid.attachEvent("onRowSelect",function(rowId){
                    if( rowId != 0 )preTally.PettyCash.viewPettyCashDetails(rowId);
                });
                
                pettyCashRptTabbar.attachEvent("onTabClick", function(id, last_id){ 
                    preTally.PettyCash.applyFilter(id);
                    return true;
                   
                });

            }else {
                dhxMiddleBlockTabs.tabs("view_pettycashreports").setActive();
            }
        },
        applyFilter:  function(actvId){
            
            if(actvId == 'view_pettyCashItemReports'){
                var filterValue = new Array($('#iePCRF').val(), $('#itmPCRF').val(), $('#brnPCRF').val(),$('#addPCRF').val(),$( "#recvPCRF" ).val());
                chartPettyCashFilterParams = '&f='+ptPettyCashRptTlbr.getValue("rpt_date_from")+'&t='+ptPettyCashRptTlbr.getValue("rpt_date_till");
                pettyCashItemGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);
                pettyCashItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashItem.php&"+chartPettyCashFilterParams+"&filter="+filterValue), function() { 
                    $('.pc_cnt_tot').html("# : "+pettyCashItemGrid.getUserData("", "TL_Count")+" ");                
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }else if(actvId == 'view_pettyCashDetails'){
                chartPettyCashFilterParams = '&f='+ptPettyCashRptTlbr.getValue("rpt_date_from")+'&t='+ptPettyCashRptTlbr.getValue("rpt_date_till")+'&BSId='+pettyCashDetailsGrid.getUserData('','BS_RefId');
                pettyCashDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartPettyCashFilterParams), function() {
                });
            }
        },
        viewPettyCashDetails : function(BSId){
                
            if (!pettyCashRptTabbar.cells("view_pettyCashDetails")) {
                pettyCashRptTabbar.addTab("view_pettyCashDetails","Petty Cash Entry Details");
                pettyCashRptTabbar.tabs("view_pettyCashDetails").setActive();

                pettyCashDetailsLayout = pettyCashRptTabbar.cells("view_pettyCashDetails").attachLayout('1C');
                pettyCashDetailsLayout.cells("a").hideHeader();

                pettyCashDetailsGrid = pettyCashDetailsLayout.cells("a").attachGrid();
                pettyCashDetailsGrid.enableColSpan(true);
                pettyCashDetailsGrid.enableTooltips("false,false,false,false,false,false,false");
                pettyCashDetailsGrid.attachEvent("onXLE",function(){
                    var itFilter = pettyCashDetailsGrid.getFilterElement(1);
                    itFilter.placeholder = " Items";

                    var dsFilter = pettyCashDetailsGrid.getFilterElement(2);
                    dsFilter.placeholder = " Description";

                    var trFilter = pettyCashDetailsGrid.getFilterElement(3);
                    trFilter.placeholder = " Track";

                    var addFilter = pettyCashDetailsGrid.getFilterElement(6);
                    addFilter.setPlaceholder("Added By");
                   preTally.Settings.progressOff(true, dhxLayout, null);
                });
                pettyCashDetailsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });

                chartPettyCashFilterParams = '&f='+ptPettyCashRptTlbr.getValue("rpt_date_from")+'&t='+ptPettyCashRptTlbr.getValue("rpt_date_till")+'&BSId='+BSId;
//                pettyCashDetailsGrid.clearAll(true);
                pettyCashDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartPettyCashFilterParams), function() {
                    pettyCashDetailsGrid.attachEvent("onFilterEnd", function(elements){
                        if(pettyCashDetailsGrid.getRowsNum() == 0 ){                            
                           pettyCashDetailsGrid.addRow(0,['No Records Found'],1);
                           pettyCashDetailsGrid.setColspan(0,0,7);
                           pettyCashDetailsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" ); 
                           pettyCashDetailsGrid.addRow('NoRecord',[]);
                           pettyCashDetailsGrid.setColspan('NoRecord',0,7);
                           pettyCashDetailsGrid.setRowTextStyle('NoRecord', "background-color:white" ); 
                        }
                    });
                });
            }else{
                pettyCashRptTabbar.tabs("view_pettyCashDetails").setActive();
                chartPettyCashFilterParams = '&f='+ptPettyCashRptTlbr.getValue("rpt_date_from")+'&t='+ptPettyCashRptTlbr.getValue("rpt_date_till")+'&BSId='+BSId;
//                pettyCashDetailsGrid.clearAll(true);
                pettyCashDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartPettyCashFilterParams), function() {
                    pettyCashDetailsGrid.attachEvent("onFilterEnd", function(elements){
                        if(pettyCashDetailsGrid.getRowsNum() == 0 ){                           
                           pettyCashDetailsGrid.addRow(0,['No Records Found'],1); 
                            pettyCashDetailsGrid.setColspan(0,0,7);
                            pettyCashDetailsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                            pettyCashDetailsGrid.addRow('NoRecord',[]);
                           pettyCashDetailsGrid.setColspan('NoRecord',0,7);
                           pettyCashDetailsGrid.setRowTextStyle('NoRecord', "background-color:white" ); 
                        }
                    }); 
                });
            }

        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
    }
})(jQuery, this);