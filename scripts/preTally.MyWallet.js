;(function($, window, undefined){
    preTally.MyWallet = {
        view_mywallet : function(){
            if (!dhxMiddleBlockTabs.cells("view_mywallet")) {
                
                dhxMiddleBlockTabs.addTab("view_mywallet", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;My Wallet", 150);
                dhxMiddleBlockTabs.tabs("view_mywallet").setActive();
                
                myWalletRptLayout = dhxMiddleBlockTabs.cells("view_mywallet").attachLayout('1C');
                myWalletRptLayout.cells("a").hideHeader();
                
                ptMyWalletRptTlbr = myWalletRptLayout.cells("a").attachToolbar();                
                ptMyWalletRptTlbr.setIconsPath("images/icon/default_18/");
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
                ptMyWalletRptTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                ptMyWalletRptTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                ptMyWalletRptTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select'); 
                ptMyWalletRptTlbr.addSeparator();
                ptMyWalletRptTlbr.addText("text_from", null, "From");
                ptMyWalletRptTlbr.addInput("rpt_date_from", null, "", 75);
                ptMyWalletRptTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                ptMyWalletRptTlbr.addSeparator();
                ptMyWalletRptTlbr.addText("text_till", null, "Till");
                ptMyWalletRptTlbr.addInput("rpt_date_till", null, "", 75);
                ptMyWalletRptTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                
                ptMyWalletRptTlbr.addSeparator();
                
                ptMyWalletRptTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
//                ptMyWalletRptTlbr.addSeparator();
//                ptMyWalletRptTlbr.addButton("excel_export", null , "Export", "excel.png");


                var ptRpTb_Inp_Frm = ptMyWalletRptTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(ptMyWalletRptTlbr.getValue("rpt_date_till")) preTally.MyWallet.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = ptMyWalletRptTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(ptMyWalletRptTlbr.getValue("rpt_date_from")) preTally.MyWallet.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 ptMyWalletRptTlbr.attachEvent("onClick", function(id){  
                   var pId = ptMyWalletRptTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        ptMyWalletRptTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        ptMyWalletRptTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        ptMyWalletRptTlbr.setValue('rpt_date_from', dateToday, false);
                        ptMyWalletRptTlbr.setValue('rpt_date_till', dateToday, false);
                        ptMyWalletRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptMyWalletRptTlbr.setItemText('rpt_year_filter', 'Select Year');
                        
                        var actvId = myWalletRptTabbar.getActiveTab();
                        preTally.MyWallet.applyFilter(actvId);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(ptMyWalletRptTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        ptMyWalletRptTlbr.setValue('rpt_date_from', firstDay, false);
                        ptMyWalletRptTlbr.setValue('rpt_date_till', lastDay, false);
                        ptMyWalletRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //ptMyWalletRptTlbr.setItemText('rpt_year_filter', 'Select Year');
    
                        var actvId = myWalletRptTabbar.getActiveTab();
                        preTally.MyWallet.applyFilter(actvId);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay   = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay    = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        ptMyWalletRptTlbr.setValue('rpt_date_from', firstDay, false);
                        ptMyWalletRptTlbr.setValue('rpt_date_till', lastDay, false);
                        ptMyWalletRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptMyWalletRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        
                         var actvId = myWalletRptTabbar.getActiveTab();
                        preTally.MyWallet.applyFilter(actvId);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){
                        ptMyWalletRptTlbr.setItemText('rpt_day_filter', 'Select Day');
                        ptMyWalletRptTlbr.setItemText('rpt_month_filter', 'Select Month');
                        ptMyWalletRptTlbr.setItemText('rpt_year_filter', 'Select Year');

                        var actvId = myWalletRptTabbar.getActiveTab();
                        preTally.MyWallet.applyFilter(actvId);
                    }  
                    if(id == 'excel_export'){
                        preTally.MyWallet.exportCashBalanceReport();
                    }
                    
//                    var actvId = myWalletRptTabbar.getActiveTab();
//                    preTally.MyWallet.applyFilter(actvId);
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                
                ptMyWalletRptTlbr.setAlign('right');
                
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp==4)
                {   var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    var month=date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    ptMyWalletRptTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    ptMyWalletRptTlbr.setValue('rpt_date_till', tDate);
                    ptMyWalletRptTlbr.setItemText('rpt_month_filter',m_names[month]);
                }
                else
                {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    ptMyWalletRptTlbr.setValue('rpt_date_till', cDate);
                    ptMyWalletRptTlbr.setValue('rpt_date_from', cDate);
                    ptMyWalletRptTlbr.setItemText('rpt_day_filter', 'Today');
                }
                
                
                myWalletRptTabbar = myWalletRptLayout.cells("a").attachTabbar();
                myWalletRptTabbar.addTab("view_myWalletItemReports","Petty Cash Item Reports",170);
                myWalletRptTabbar.tabs("view_myWalletItemReports").setActive();

                myWalletItemRptLayout = myWalletRptTabbar.cells("view_myWalletItemReports").attachLayout('1C');
                myWalletItemRptLayout.cells("a").hideHeader();
                
                myWalletItemRptLayout.cells("a").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'><div class='mw_cnt_tot' style='float:left;'># : 0</div> <div class='mw_curr_bal' style='float:right;color:red;padding-right:25px;'>Current Balance : 0</div></div>",
                    height: 23             // custom heightattachStatusBar
                });

                myWalletItemGrid = myWalletItemRptLayout.cells("a").attachGrid();
                myWalletItemGrid.setImagePath("assets/grid/codebase/imgs/");
                myWalletItemGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'ptycsh_select_filter' id='iePCRF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  class='ptycshrpt_text_filter' id ='itmPCRF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,Available Balance, Amount Paid,<input type='text'  class='ptycshrpt_text_filter' id='brnPCRF' style='width: 90%;' placeholder='Branch'> ,<input type='text'  class='ptycshrpt_text_filter' id='addPCRF' style='width: 90%;' placeholder=' Added By'>,<input type='text'  class='ptycshrpt_text_filter' id='recvPCRF' style='width: 90%;' placeholder=' Received By'>,Date,View Entries");
                //myWalletItemGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                myWalletItemGrid.setInitWidths("50,80,*,100,100,150,150,150,150,50")
                myWalletItemGrid.setColAlign("center,center,left,left,right,left,left,left,left,center");
                myWalletItemGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                
                myWalletItemGrid.setColSorting("na,na,na,na,na,na,na,na,na,na")
                myWalletItemGrid.init();
                myWalletItemGrid.setSkin("dhx_skyblue");
                myWalletItemGrid.enableSmartRendering(true,50);
                myWalletItemGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true");
                myWalletItemGrid.enableColSpan(true);

                preTally.Settings.progressOn(true, dhxLayout, null);
                var filtrInterval;
                chartMyWalletFilterParams = '&f='+ptMyWalletRptTlbr.getValue("rpt_date_from")+'&t='+ptMyWalletRptTlbr.getValue("rpt_date_till")+'&list=self';
                myWalletItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportPettyCashItem.php&"+chartMyWalletFilterParams), function() {
                   
                    $('.mw_cnt_tot').html("# : "+myWalletItemGrid.getUserData("", "TL_Count")+" ");
                   $('.mw_curr_bal').html("Current Balance : "+myWalletItemGrid.getUserData("", "MW_Balance")+" ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    
                    $( ".ptycsh_select_filter" ).change(function() {
                        preTally.MyWallet.applyFilter('view_myWalletItemReports');
                    });


                    $( ".ptycshrpt_text_filter" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.MyWallet.applyFilter('view_myWalletItemReports'); 
                            clearInterval(filtrInterval); 
                        }, 500);

                    });

                });
                          
                myWalletItemGrid.attachEvent("onRowSelect",function(rowId){
                    if( rowId != 0 )preTally.MyWallet.viewMyWalletDetails(rowId);
                });
                
                myWalletRptTabbar.attachEvent("onTabClick", function(id, last_id){ 
                    preTally.MyWallet.applyFilter(id);
                    return true;
                   
                });

            }else {
                dhxMiddleBlockTabs.tabs("view_mywallet").setActive();
            }
        },
        applyFilter:  function(actvId){
            
            if(actvId == 'view_myWalletItemReports'){
                var filterValue = new Array($('#iePCRF').val(), $('#itmPCRF').val(), $('#brnPCRF').val(),$('#addPCRF').val(),$( "#recvPCRF" ).val());
                chartMyWalletFilterParams = '&f='+ptMyWalletRptTlbr.getValue("rpt_date_from")+'&t='+ptMyWalletRptTlbr.getValue("rpt_date_till")+'&list=self';
                //myWalletItemGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);
                myWalletItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashItem.php&"+chartMyWalletFilterParams+"&filter="+filterValue), function() { 
                    $('.mw_cnt_tot').html("# : "+myWalletItemGrid.getUserData("", "TL_Count")+" ");                
                    $('.mw_curr_bal').html("Current Balance: "+myWalletItemGrid.getUserData("", "MW_Balance")+" ");
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }else if(actvId == 'view_pettyCashDetails'){
                chartMyWalletFilterParams = '&f='+ptMyWalletRptTlbr.getValue("rpt_date_from")+'&t='+ptMyWalletRptTlbr.getValue("rpt_date_till")+'&BSId='+myWalletDetailsGrid.getUserData('','BS_RefId')+'&list=self';
                myWalletDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartMyWalletFilterParams), function() {
                });
            }
        },
        viewMyWalletDetails : function(BSId){
                
            if (!myWalletRptTabbar.cells("view_pettyCashDetails")) {
                myWalletRptTabbar.addTab("view_pettyCashDetails","Petty Cash Entry Details");
                myWalletRptTabbar.tabs("view_pettyCashDetails").setActive();

                myWalletDetailsGrid = myWalletRptTabbar.cells("view_pettyCashDetails").attachLayout('1C');
                myWalletDetailsGrid.cells("a").hideHeader();

                myWalletDetailsGrid = myWalletDetailsGrid.cells("a").attachGrid();
//                myWalletDetailsGrid.setImagePath("../../codebase/imgs/");
//                myWalletDetailsGrid.setSkin("dhx_skyblue")
////                myWalletDetailsGrid.setHeader("#,#text_filter_inc,#text_filter_inc,#text_filter_inc,Amount,Date,#combo_filter");
//                myWalletDetailsGrid.setInitWidths("40,*,*,120,100,120,150")
//                myWalletDetailsGrid.setColAlign("left,left,left,left,left,right,left")
//                myWalletDetailsGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
//    //                myWalletDetailsGrid.setColSorting("int,str,str,str,str");  
                myWalletDetailsGrid.enableColSpan(true);
                myWalletDetailsGrid.enableTooltips("false,false,false,false,false,false,false");
//    //            myWalletDetailsGrid.attachFooter("Sum,#cspan,#cspan,#cspan,#cspan,<span style='float:right;'>{#stat_total}</span>");
//                myWalletDetailsGrid.init();
                
                
                
                myWalletDetailsGrid.attachEvent("onXLE",function(){
                    var itFilter = myWalletDetailsGrid.getFilterElement(1);
                    itFilter.placeholder = " Items";

                    var dsFilter = myWalletDetailsGrid.getFilterElement(2);
                    dsFilter.placeholder = " Description";

                    var trFilter = myWalletDetailsGrid.getFilterElement(3);
                    trFilter.placeholder = " Track";

                    var addFilter = myWalletDetailsGrid.getFilterElement(6);
                    addFilter.setPlaceholder("Added By");
                   preTally.Settings.progressOff(true, dhxLayout, null);
                });
                myWalletDetailsGrid.attachEvent("onXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });

                chartMyWalletFilterParams = '&f='+ptMyWalletRptTlbr.getValue("rpt_date_from")+'&t='+ptMyWalletRptTlbr.getValue("rpt_date_till")+'&BSId='+BSId+'&list=self';
//                myWalletDetailsGrid.clearAll(true);
                myWalletDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartMyWalletFilterParams), function() {
                    myWalletDetailsGrid.attachEvent("onFilterEnd", function(elements){
                        if(myWalletDetailsGrid.getRowsNum() == 0 ){
                            myWalletDetailsGrid.addRow(0,['No Records Found'],1);
                            myWalletDetailsGrid.setColspan(0,0,7);
                            myWalletDetailsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" );
                            myWalletDetailsGrid.addRow('NoRecord',[]);
                            myWalletDetailsGrid.setColspan('NoRecord',0,7);
                            myWalletDetailsGrid.setRowTextStyle('NoRecord', "background-color:white" ); 
                        }
                    });
                });
            }else{
                myWalletRptTabbar.tabs("view_pettyCashDetails").setActive();
                chartMyWalletFilterParams = '&f='+ptMyWalletRptTlbr.getValue("rpt_date_from")+'&t='+ptMyWalletRptTlbr.getValue("rpt_date_till")+'&BSId='+BSId+'&list=self';
//                myWalletDetailsGrid.clearAll(true);
                myWalletDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportPettyCashDetails.php"+chartMyWalletFilterParams), function() {
                    myWalletDetailsGrid.attachEvent("onFilterEnd", function(elements){ 
                        if(myWalletDetailsGrid.getRowsNum() == 0 ){
                            myWalletDetailsGrid.addRow(0,['No Records Found'],1); 
                            myWalletDetailsGrid.setColspan(0,0,7);
                            myWalletDetailsGrid.setRowTextStyle(0, "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                            myWalletDetailsGrid.addRow('NoRecord',[]);
                            myWalletDetailsGrid.setColspan('NoRecord',0,7);
                            myWalletDetailsGrid.setRowTextStyle('NoRecord', "background-color:white" ); 
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