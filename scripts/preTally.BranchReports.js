;(function($, window, undefined) {
    preTally.BranchReports = {    
        viewBranchReports:function(){
            if (!dhxMiddleBlockTabs.cells("view_branchreports")) {
                reportBranch=1;
                dhxAccord.cells("a4").setText("<img src='images/icon/balsheet.gif' />&nbsp;&nbsp;&nbsp;Legend Details"); 
                
                dhxMiddleBlockTabs.addTab("view_branchreports", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Branch Reports", 180);
                dhxMiddleBlockTabs.tabs("view_branchreports").setActive();
                
                dhxBranchReportsLayout =  dhxMiddleBlockTabs.cells("view_branchreports").attachLayout("2U");
                dhxBranchReportsLayout.cells("a").setWidth(200);
                dhxBranchReportsLayout.cells("a").hideHeader();
                dhxBranchReportsLayout.cells("a").fixSize(true, true);    
                dhxBranchReportsLayout.cells("b").fixSize(true, true);  
                dhxBranchReportsLayout.cells('b').hideArrow();
                dhxBranchReportsLayout.cells("b").hideHeader();
                 dhxBranchRprtTlbr = dhxBranchReportsLayout.cells("b").attachToolbar();                
                dhxBranchRprtTlbr.setIconsPath("images/icon/default_18/");
                var Days_Options = [];                
                var Months_Options = [];
                var Years_Options = [];
                var monthNames = [ 
                        "January", "February", "March","April", "May", "June",
                        "July", "August", "September","October", "November", "December"
                    ];                    
                var todayDt = Date.today().getDate();  
                for (i = todayDt; i >=1 ; i--) {
                    if(i<10) {
                        i='0'+i;
                    }    
                    if(i==todayDt) {
                        Days_Options.push([i,'obj','Today',"calendar_D.png"]);
                    }else {
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
                
                dhxBranchRprtTlbr.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                dhxBranchRprtTlbr.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                dhxBranchRprtTlbr.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                
               
                dhxBranchRprtTlbr.addSeparator();
                          
                dhxBranchRprtTlbr.addText("text_from", null, "From");
                dhxBranchRprtTlbr.addInput("rpt_date_from", null, "", 75);
                dhxBranchRprtTlbr.addButton("rpt_df_clear", null, "", "close.gif");
                dhxBranchRprtTlbr.addSeparator();
                
                dhxBranchRprtTlbr.addText("text_till", null, "Till");
                dhxBranchRprtTlbr.addInput("rpt_date_till", null, "", 75);
                dhxBranchRprtTlbr.addButton("rpt_dt_clear", null, "", "close.gif");
                dhxBranchRprtTlbr.addSeparator();
                
                dhxBranchRprtTlbr.addButton("rpt_date_filter", null, "Search", "save.gif");
                
              
                
                var ptRpTb_Inp_Frm = dhxBranchRprtTlbr.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(dhxBranchRprtTlbr.getValue("rpt_date_till")) preTally.BankBalanceSheet.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = dhxBranchRprtTlbr.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(dhxBranchRprtTlbr.getValue("rpt_date_from")) preTally.BankBalanceSheet.setSens(ptRpTb_Inp_Frm, "min");
                }     
                 ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                 dhxBranchRprtTlbr.attachEvent("onClick", function(id){  
                   var pId = dhxBranchRprtTlbr.getParentId(id);
                    
                    if(id == 'rpt_df_clear')
                        dhxBranchRprtTlbr.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        dhxBranchRprtTlbr.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");
                        dhxBranchRprtTlbr.setValue('rpt_date_from', dateToday, false);
                        dhxBranchRprtTlbr.setValue('rpt_date_till', dateToday, false);
                        dhxBranchRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBranchRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BranchReports.filterReport(1);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(dhxBranchRprtTlbr.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        dhxBranchRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBranchRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBranchRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        //dhxBranchRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                        preTally.BranchReports.filterReport(1);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        dhxBranchRprtTlbr.setValue('rpt_date_from', firstDay, false);
                        dhxBranchRprtTlbr.setValue('rpt_date_till', lastDay, false);
                        dhxBranchRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBranchRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        preTally.BranchReports.filterReport(1);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){                        
                        dhxBranchRprtTlbr.setItemText('rpt_day_filter', 'Select Day');
                        dhxBranchRprtTlbr.setItemText('rpt_month_filter', 'Select Month');
                        dhxBranchRprtTlbr.setItemText('rpt_year_filter', 'Select Year');
                       preTally.BranchReports.filterReport(1);
                    }  
                });
                BranchReportsGrid = dhxBranchReportsLayout.cells("b").attachGrid();
                BranchReportsGrid.init();
               preTally.BranchReports.filterReport(1);
                
            }
            else
            {
                dhxMiddleBlockTabs.cells("view_branchreports").setActive();
            }
        },        
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        filterReport: function(id) { 
            //alert(id);
            //var Ids = ofzBankRprtTree.getAllSubItems(id); //alert(treeIds);
            var filtrInterval ;
            var filtrBranchInterval ;                       
            
                preTally.Settings.progressOn(true, dhxLayout, null);             
                
                 if(reportBranch==1)
                 { 
                     var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
                        dhxBranchRprtTlbr.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        dhxBranchRprtTlbr.setValue('rpt_date_till', tDate);
                        dhxBranchRprtTlbr.setItemText('rpt_month_filter',m_names[month]);
                        reportBranch=0;
                   
                 
                }
                chartBankBSFilterParams = 'r='+id+'&f='+dhxBranchRprtTlbr.getValue("rpt_date_from")+'&t='+dhxBranchRprtTlbr.getValue("rpt_date_till");
             
                BranchReportsGrid.destructor();                
                BranchReportsGrid = dhxBranchReportsLayout.cells("b").attachGrid();
                        
                BranchReportsGrid.setImagePath("../../codebase/imgs/");
                
                //BranchReportsGrid.attachHeader(",#select_filter_strict,#select_filter_strict,#select_filter_strict,,,,");
                
                BranchReportsGrid.init();
                BranchReportsGrid.setSkin("dhx_skyblue")
                BranchReportsGrid.enableSmartRendering(true,50);                                
                
                BranchReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/reportBranchData.php&"+ chartBankBSFilterParams));
                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
         
        }
    };
})(jQuery, this);