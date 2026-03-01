;
(function($, window, undefined) {
    preTally.MyBankBook = {
        viewMyBankBook : function() {
            if (!dhxMiddleBlockTabs.cells("viewMyBankBook")) {
                reportMyBankBSInit = 1;
                dhxMiddleBlockTabs.addTab("viewMyBankBook", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;My Bank Book&nbsp;&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='popupBank'/>", 210);
                dhxMiddleBlockTabs.tabs("viewMyBankBook").setActive();
                $(".popupBank").click(function(){
                    preTally.MyBankBook.filterReport();
                })
                dhxMyBankReportsLayout =  dhxMiddleBlockTabs.cells("viewMyBankBook").attachLayout("1C");
                dhxMyBankReportsLayout.cells("a").setWidth(200);
                dhxMyBankReportsLayout.cells("a").hideHeader();
                dhxMyBankReportsLayout.cells("a").fixSize(true, true);    

                dhxMyBankRprtTlbr   = dhxMyBankReportsLayout.cells("a").attachToolbar();                
                dhxMyBankRprtTlbr.setIconsPath("images/icon/default_18/");
                
                var Days_Options    = [];
                var Months_Options  = [];
                var Years_Options   = [];
                var monthNames      = [ 
                        "January", "February", "March","April", "May", "June",
                        "July", "August", "September","October", "November", "December"
                    ];
                    
                var todayDt = Date.today().getDate();  
                for (i = todayDt; i >= 1 ; i--) {
                    if(i < 10){
                        i = '0'+i;
                    }    
                    if(i == todayDt){
                        Days_Options.push([i,'obj','Today',"calendar_D.png"]);
                    } else{
                        Days_Options.push([i,'obj',i,"calendar_D.png"]);
                    }
                }
                //Date.today().getMonth()+1
                for (i = 12; i >= 1 ; i--) {
                    j = i;    
                    if(j < 10){
                        j = '0'+i;
                    } 
                    Months_Options.push(['m'+j,'obj',monthNames[i-1],"calendar_M.png"]);
                }
                
                for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                    Years_Options.push([i,'obj',i,"calendar_Y.png"]);
                }
                
                dhxMyBankRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                dhxMyBankRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                dhxMyBankRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                

                dhxMyBankRprtTlbr.addSeparator();
                          
                dhxMyBankRprtTlbr.addText("text_from", null, "From");
                dhxMyBankRprtTlbr.addInput("rpt_date_from", null, "", 75);
                dhxMyBankRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                dhxMyBankRprtTlbr.addSeparator();
                
                dhxMyBankRprtTlbr.addText("text_till", null, "Till");
                dhxMyBankRprtTlbr.addInput("rpt_date_till", null, "", 75);
                dhxMyBankRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                dhxMyBankRprtTlbr.addSeparator();
                
                dhxMyBankRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                dhxMyBankRprtTlbr.addSeparator();
              
                dhxMyBankRprtTlbr.addButton("excel_export", null , "Export", "excel.png");

                var BankRp_Inp_Frm = dhxMyBankRprtTlbr.getInput("rpt_date_from");
                BankRp_Inp_Frm.setAttribute("readOnly", "true");
                BankRp_Inp_Frm.onclick = function() {
                    if(dhxMyBankRprtTlbr.getValue("rpt_date_till")) preTally.MyBankBook.setSens(BankRp_Inp_Til, "max");
                }
                var BankRp_Inp_Til = dhxMyBankRprtTlbr.getInput("rpt_date_till");
                BankRp_Inp_Til.setAttribute("readOnly", "true");
                BankRp_Inp_Til.onclick = function() {
                    if(dhxMyBankRprtTlbr.getValue("rpt_date_from")) preTally.MyBankBook.setSens(BankRp_Inp_Frm, "min");
                }     
                
                dhxMyBankRprtTlbr.attachEvent("onClick", function(id){  
                    var pId = dhxMyBankRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        dhxMyBankRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        dhxMyBankRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        dhxMyBankRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        dhxMyBankRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        dhxMyBankRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxMyBankRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.MyBankBook.filterReport();
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(dhxMyBankRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        dhxMyBankRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxMyBankRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxMyBankRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //dhxMyBankRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.MyBankBook.filterReport();
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        dhxMyBankRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxMyBankRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxMyBankRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxMyBankRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.MyBankBook.filterReport();
                    }
                    if(id == 'rpt_date_filter'){
                        dhxMyBankRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxMyBankRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxMyBankRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.MyBankBook.filterReport();
                    }  
                    if(id == 'excel_export') {
                        preTally.MyBankBook.exportMyBankBookReport();
                    }
                });
                
                // init calendar;
                BankRp_Calendar = new dhtmlXCalendarObject([BankRp_Inp_Frm, BankRp_Inp_Til]);
                BankRp_Calendar.setDateFormat("%d.%m.%Y");
                
                dhxMyBankRprtTlbr.setAlign('right');                
                var tb_data_txt = ' <div class="tb_data_txt_secl">\
                                        <div class="myBankBook_cnt"># : 0</div></div><div id="bankBook_paging"></div>';
                
                var tbRptObj = dhxMyBankReportsLayout.cells("a").attachStatusBar({
                    text:   tb_data_txt,   // status bar text 
                    height: 30             // custom height
                });
                preTally.MyBankBook.filterReport();    

            } else {
                dhxMiddleBlockTabs.tabs("viewMyBankBook").setActive();
            }
        },
        filterReport: function() { 
       
            var filtrInterval ;
            var filtrBranchInterval ;
            preTally.Settings.progressOn(true, dhxLayout, null);

            if(reportMyBankBSInit == 1) {
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp == 4) {   
                    var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    var month=date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    dhxMyBankRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    dhxMyBankRprtTlbr.setValue('rpt_date_till', tDate);
                    dhxMyBankRprtTlbr.setItemText('rpt_month_filter',m_names[month]);
                    reportMyBankBSInit = 0;
                }  else {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    dhxMyBankRprtTlbr.setValue('rpt_date_till', cDate);
                    dhxMyBankRprtTlbr.setValue('rpt_date_from', cDate);
                    dhxMyBankRprtTlbr.setItemText('rpt_day_filter', 'Today');
                    reportMyBankBSInit = 0;
                }
            }
            chartBankBSFilterParams = 'f='+dhxMyBankRprtTlbr.getValue("rpt_date_from")+'&t='+dhxMyBankRprtTlbr.getValue("rpt_date_till");

            dhxMyBankBookGrid = dhxMyBankReportsLayout.cells("a").attachGrid();
            dhxMyBankBookGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'bnk_select_filter' id='ieType'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text' class='bnkrpt_text_filter' id ='itmIE' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<div id='cboBankFliter' style='width: 90%;' placeholder='Bank Name'></div>,<input type='text' class='bnkrpt_text_filter' id='amtBank' style='width: 90%;' placeholder='Amount'>,<input type='text' class='bnkrpt_text_filter' id='addedByBank' style='width: 90%;' placeholder='Added By'>,<input type='text' class='bnkrpt_text_filter' id='brnBank' style='width: 90%;' placeholder='Created Branch'>,Created Date,Approved/ Rejected Date, <select class = 'bnk_select_filter' style = 'width:60px;' class = 'bnk_select_filter' id='bkStatus'><option value =''>All</option><option value ='1'>Approved</option><option value ='2'>Waiting for Approval</option><option value ='3'>Rejected</option></select>, Bank Reconciliation ");
            dhxMyBankBookGrid.setInitWidths("60,80,*,150,90,140,140,90,90,130,60");
            dhxMyBankBookGrid.setColAlign("center,left,left,left,right,left,left,left,left,left,center");
            dhxMyBankBookGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

            dhxMyBankBookGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na");
            dhxMyBankBookGrid.enableColSpan(true);
            dhxMyBankBookGrid.init();
            dhxMyBankBookGrid.setImagePath("assets/grid/codebase/imgs/"); 
            dhxMyBankBookGrid.setSkin("dhx_skyblue");
            dhxMyBankBookGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
            dhxMyBankBookGrid.enablePaging(true,50,5,"bankBook_paging",true);
            dhxMyBankBookGrid.setPagingSkin("toolbar", "dhx_skyblue");
            dhxMyBankBookGrid.enableTooltips("false,false,false,false,false,false,false,false,false");

            $( ".bnk_select_filter" ).change(function() {
                preTally.MyBankBook.applyFilter();
            });

            $( ".bnkrpt_text_filter" ).keyup(function() {
                if(filtrInterval) clearInterval(filtrInterval);

                filtrInterval = setInterval( function() { 
                    preTally.MyBankBook.applyFilter(); 
                    clearInterval(filtrInterval); 
                }, 500);
            });
            // start 21-10-2025
            cboBankFliter = new dhtmlXCombo("cboBankFliter");
            cboBankFliter.load(preTally.Initialize.encryptURL("requisites/combo_BankAccts.php"));
            cboBankFliter.setPlaceholder('Bank Name');                             
            cboBankFliter.setOptionWidth(250);
            cboBankFliter.allowFreeText(false);
            cboBankFliter.enableFilteringMode('between');
            // on change the Bank combo start                 
            cboBankFliter.attachEvent("onChange", function () {
                preTally.MyBankBook.applyFilter(); 
            });
            // end 21-10-2025
            //$('#accBrachcash').val()
            dhxMyBankBookGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportMyBankBookBSData.php&"+chartBankBSFilterParams), function() {
                $('.myBankBook_cnt').html("# : "+dhxMyBankBookGrid.getUserData("", "TL_Count")+" ");
            });

            dhxMyBankBookGrid.attachEvent("onRowSelect", function(id,ind){
                if(ind != 5 && ind != 10) { $( ".BSTarget_"+id ).click(); } // changed 4 to 5 and 10 added 
                else if (ind == 10 && dhxMyBankBookGrid.getUserData("", "bank_reconciliation") == 1) { //21-10-2025
                    dhtmlx.confirm({
                        title: "Confirm Bank Reconciliation",
                        type: "confirm-warning",
                        ok: "Yes", cancel: "No",
                        text: "Do you want to Continue ?",
                        callback: function (result) {
                            if (result) {
                                var chkbox = 0;
                                if (document.getElementById('chkbctr'+id).checked == true) {
                                     chkbox = 1;
                                }
                                $.post(preTally.Initialize.encryptURL("warehouse/accounts/addJournal.php&flag=7&type="+chkbox+"&bs_id="+id), function (response) {
                                    var jsonResponse = JSON.parse(response);
                                    if (jsonResponse.status == 1) {
                                        dhtmlx.message({ text: jsonResponse.message });
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonResponse.message });
                                    }
                                }); // post to server
                            } else {
                                document.getElementById('chkbctr'+id).checked = !document.getElementById('chkbctr'+id).checked;                                
                            }
                        }
                    });    
                }
            });

            setTimeout(function(){
                preTally.Settings.progressOff(true, dhxLayout, null);
            },1500);
            
        },
        setSens: function (inp, k) {
            if (k == "min") {
                BankRp_Calendar.setSensitiveRange(inp.value, null);
            } else {
                BankRp_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        showDetailData : function(inp,BS_Id,SH_Id){ 
            if (!MyBank_DetailDataPop) 
                MyBank_DetailDataPop = new dhtmlXPopup({mode: "left"});   
            
            if (MyBank_DetailDataPop.isVisible()) 
                MyBank_DetailDataPop.hide();
                
            var x               = getAbsoluteLeft(inp);
            var y               = getAbsoluteTop(inp);
            var w               = inp.offsetWidth;
            var h               = inp.offsetHeight;           
            var rptDetailsPop   = MyBank_DetailDataPop.attachForm();
            var params          = "SHID="+SH_Id+"&BSID="+BS_Id;
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_MyBankBookPop.php&"+params), function() {
                MyBank_DetailDataPop.show(x, y, w, h);
                var column0     = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1     = rptDetailsPop.getColumnNode("fieldsetname", 1);
                column0.style.borderRight = "1px solid #a4bed4";
            });
            $('.BSTarget_'+BS_Id).unbind();
        },
        hideDetailData : function(){
            if (MyBank_DetailDataPop.isVisible()) {
                MyBank_DetailDataPop.hide();
            } 
        },
        applyFilter:  function(){
            var amount = $( "#amtBank" ).val().replace( /,/g, "" );  // remove , from amount
            var bankvalues = cboBankFliter.getSelectedValue(); // 21-10-2025            
            var filterValue = new Array($('#ieType').val(), $('#itmIE').val(), $('#brnBank').val(),$('#addedByBank').val(),amount,$('#bkStatus').val(),bankvalues);
            dhxMyBankBookGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxMyBankBookGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMyBankBookBSData.php&"+chartBankBSFilterParams+"&filter="+filterValue), function() { 
                $('.myBankBook_cnt').html("# : "+dhxMyBankBookGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        exportMyBankBookReport : function() {
            
            var exportfilterValue               = {};
            exportfilterValue['From_Date']      = dhxMyBankRprtTlbr.getValue("rpt_date_from");
            exportfilterValue['To_Date']        = dhxMyBankRprtTlbr.getValue("rpt_date_till");
            exportfilterValue['TypeFilter']     = $('#ieType').val();
            exportfilterValue['ItemNameFilter'] = $('#itmIE').val();
            exportfilterValue['BranchFilter']   = $('#brnBank').val();
            exportfilterValue['amount']         = $('#amtBank').val().replace( /,/g, "" );  // remove , from amount
            exportfilterValue['report_type']    = "mybankbook";
            exportfilterValue['statusFilter']   = $('#bkStatus').val();
            exportfilterValue['AddedByFilter']  = $('#addedByBank').val();
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            $.post(
                preTally.Initialize.encryptURL('warehouse/ReportExcelExport.php'),
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