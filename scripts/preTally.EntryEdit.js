;(function ($, window, undefined) {
    var filterValue;
    var EdtRptFilterParams;
    var edtReportInit;
    preTally.EntryEdit = {        
        view_editReports : function(){
            if (!dhxMiddleBlockTabs.cells("view_editEntry")) {       
                var filtrInterval ;  
                edtReportInit=1;
                dhxMiddleBlockTabs.addTab("view_editEntry", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Account Entries", 210);
                dhxMiddleBlockTabs.tabs("view_editEntry").setActive();
                dhxEdtEntryEditLayout =  dhxMiddleBlockTabs.cells("view_editEntry").attachLayout("1C");
                dhxEdtEntryEditLayout.cells("a").setText("Manage Entries");      
                
                edtReportToolbar = dhxEdtEntryEditLayout.cells("a").attachToolbar();                
                edtReportToolbar.setIconsPath("images/icon/default_18/");                
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
                
                edtReportToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                edtReportToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                edtReportToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                
               
                edtReportToolbar.addSeparator();
                          
                edtReportToolbar.addText("text_from", null, "From");
                edtReportToolbar.addInput("rpt_date_from", null, "", 75);
                edtReportToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                edtReportToolbar.addSeparator();
                
                edtReportToolbar.addText("text_till", null, "Till");
                edtReportToolbar.addInput("rpt_date_till", null, "", 75);
                edtReportToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                edtReportToolbar.addSeparator();
                
                edtReportToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                
              
                
                var ptRpTb_Inp_Frm = edtReportToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(edtReportToolbar.getValue("rpt_date_till")) preTally.EntryEdit.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = edtReportToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(edtReportToolbar.getValue("rpt_date_from")) preTally.EntryEdit.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 edtReportToolbar.attachEvent("onClick", function(id){  
                   var pId = edtReportToolbar.getParentId(id);
                   
                    if(id == 'rpt_df_clear')
                        edtReportToolbar.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        edtReportToolbar.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        edtReportToolbar.setValue('rpt_date_from', dateToday, false);
                        edtReportToolbar.setValue('rpt_date_till', dateToday, false);
                        edtReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        edtReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.EntryEdit.filterReport(rptFilterID);
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(edtReportToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        edtReportToolbar.setValue('rpt_date_from', firstDay, false);
                        edtReportToolbar.setValue('rpt_date_till', lastDay, false);
                        edtReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //edtReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.EntryEdit.filterReport(rptFilterID);
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        edtReportToolbar.setValue('rpt_date_from', firstDay, false);
                        edtReportToolbar.setValue('rpt_date_till', lastDay, false);
                        edtReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        edtReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        preTally.EntryEdit.filterReport(rptFilterID);
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){                     
                        edtReportToolbar.setItemText('rpt_day_filter', 'Select Day');
                        edtReportToolbar.setItemText('rpt_month_filter', 'Select Month');
                        edtReportToolbar.setItemText('rpt_year_filter', 'Select Year');
                       preTally.EntryEdit.filterReport(rptFilterID);
                    }  
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");                
                edtReportToolbar.setAlign('right');                
                preTally.EntryEdit.filterReport();
            }
            else
            {
                dhxMiddleBlockTabs.cells("view_editEntry").setActive();
            }
            
        },
        ReportEntrySave:function(){      
             var itemObject      = {};
             var error           = 0;
             var checked=dhxEntryEditGrid.getCheckedRows(10);
             if(checked==null || checked==""){
                 dhtmlx.message({text: "No Entry Edited"}); 
             }else{
            dhxEntryEditGrid.forEachRow(function(rId){                
                if(dhxEntryEditGrid.cells(rId,10).getValue() == 1) {
                    var itemDetails = {}; 
                        itemDetails['SH_Track']=dhxEntryEditGrid.getUserData(rId,"SH_Track");
                        if(dhxEntryEditGrid.getUserData(rId,"SH_Track")==1){                            
                                itemDetails['TR_Id'] = dhxEntryEditGrid.getUserData(rId,"TR_Id");                                 
                        }              
                        itemDetails['BS_Amount']=0;
                        if(dhxEntryEditGrid.cells(rId,5).getValue()==null || dhxEntryEditGrid.cells(rId,5).getValue()=="")
                        {                            
                            itemDetails['BS_Amount'] = dhxEntryEditGrid.getUserData(rId,"BS_Amount");
                        }else{
                            itemDetails['BS_Amount'] = dhxEntryEditGrid.cells(rId,5).getValue();
                        }
                        if(dhxEntryEditGrid.cells(rId,6).getValue()==null || dhxEntryEditGrid.cells(rId,6).getValue()==""){                            
                            
                            itemDetails['BS_Date'] = dhxEntryEditGrid.getUserData(rId,"BS_Date");
                        }else{
                            itemDetails['BS_Date']  = dhxEntryEditGrid.cells(rId,6).getValue();
                        }
                        itemDetails['BS_PettyCashRefId'] = dhxEntryEditGrid.getUserData(rId,"BS_PettyCashRefId");
                        itemDetails['IT_PettyCash'] = dhxEntryEditGrid.getUserData(rId,"IT_PettyCash");
                        itemObject[rId] = itemDetails;                        
                    }
                });  
                if(error == 0) {
//                console.log(itemObject);
                $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL("warehouse/report_edtSave.php"),
                    data   : itemObject                    
                }).done(function(data) { 
                    if (data != '') {
                        dhtmlx.message({text: data }); 
                        dhxEntryEditGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                        dhxEntryEditGrid.clearAll();
                        preTally.Settings.progressOn(true, dhxLayout, null);

                        dhxEntryEditGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams), function() {
                            preTally.EntryEdit.setEntryGridColType();
                            preTally.Settings.progressOff(true, dhxLayout, null);
                        });
                        
//                        dhxEntryEditGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams),true,true);    
                                             
//                        dhxEntryEditGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams),true,true); 
//                        if (dhxEntryEditGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked==true) {
//                            dhxEntryEditGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
//                        }
                    }
                });
            }
            }
        },
        DeleteBSItem:function(bid){
            dhtmlx.confirm({
                title: "Delete Balance Sheet Entry",
                type:"confirm-warning",
                ok  : "Yes", cancel : "No",
                text: "Do you want to Delete " + "'" + dhxEntryEditGrid.cells(bid,2).getValue() + " ?",
                callback: function(response) {
                    if(response){
                         $.post(preTally.Initialize.encryptURL("warehouse/block_BSItems.php"),{data:bid,CHQ_Number:dhxEntryEditGrid.getUserData(bid, "CHQ_Number"), IT_PettyCash:dhxEntryEditGrid.getUserData(bid, "IT_PettyCash"),BS_PettyCashRefId :dhxEntryEditGrid.getUserData(bid, "BS_PettyCashRefId") },function(data){
                            dhxEntryEditGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                            dhxEntryEditGrid.clearAll();
                            preTally.Settings.progressOn(true, dhxLayout, null);

                            dhxEntryEditGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams), function() {
                                preTally.EntryEdit.setEntryGridColType();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                            });

                            dhtmlx.message(data);  

                        });
//            $.ajax({                    
//                    url    : preTally.Initialize.encryptURL("warehouse/block_BSItems.php&data="+bid+"&CHQ_Number="+dhxEntryEditGrid.getUserData(bid, "CHQ_Number"))                    
//                                                }).done(function(data) { 
//                    
//                    dhxEntryEditGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
//                    dhxEntryEditGrid.clearAll();
//                    preTally.Settings.progressOn(true, dhxLayout, null);
//
//                    dhxEntryEditGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams), function() {
//                        preTally.EntryEdit.setEntryGridColType();
//                        preTally.Settings.progressOff(true, dhxLayout, null);
//                    });
//                    
//                    dhtmlx.message(data);  
////                    dhxEntryEditGrid.deleteRow(bid);                    
////                    dhxEntryEditGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams),true,true);
//                });
            }
        }
    });
        },
        applyRptFilter:function (id){  
            var amount = $( "#edt_amtF" ).val().replace( /,/g, "" ); 
            filterValue = new Array($('#edt_ieF').val(), $('#edt_itmF').val(), $('#edt_descF').val(),$('#edt_trkF').val(), $('#edt_brnF').val(),$('#edt_usrF').val(),amount);            
            dhxEntryEditGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);            
            dhxEntryEditGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/report_edtData.php&filter="+filterValue+"&"+EdtRptFilterParams), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                preTally.EntryEdit.setEntryGridColType();
            });
        },
       filterReport: function() {                         
            preTally.Settings.progressOn(true, dhxLayout, null);   
            if(edtReportInit==1)
                 {
                    var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                    if(bsp==4)
                    {   var date = new Date();
                        var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                        var month=date.getMonth();
                        tDate = Date.today().toString("dd.MM.yyyy"); 
                        edtReportToolbar.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                        edtReportToolbar.setValue('rpt_date_till', tDate);
                        edtReportToolbar.setItemText('rpt_month_filter',m_names[month]);
                        edtReportInit=0;
                    }
                    else
                    {
                        cDate = Date.today().toString("dd.MM.yyyy"); 
                        edtReportToolbar.setValue('rpt_date_till', cDate);
                        edtReportToolbar.setValue('rpt_date_from', cDate);
                        edtReportToolbar.setItemText('rpt_day_filter', 'Today');
                        edtReportInit=0;
                    }
                 
                }
                EdtRptFilterParams = '&f='+edtReportToolbar.getValue("rpt_date_from")+'&t='+edtReportToolbar.getValue("rpt_date_till");             
                preTally.EntryEdit.loadRptGrid(preTally.Initialize.encryptURL('requisites/report_edtData.php'+EdtRptFilterParams));                
                setTimeout(function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                },1500);
            
        },
        loadRptGrid :function(loadDataString)
        { var filtrInterval;
            dhxEntryEditGrid = dhxEdtEntryEditLayout.cells("a").attachGrid();                 
                //dhxEntryEditGrid.setHeader("#,Type,Item,Desc,Track,Amount,Date,Branch,,");
                 RptButtonBar = dhxMiddleBlockTabs.tabs("view_editEntry").attachStatusBar({
                            text  : "<input type='button'id='stats_bar_btn' value='SAVE' onclick='preTally.EntryEdit.ReportEntrySave();' style='margin:5px 10px 5px 0; float:right;' /><div id='mng_entry_paging'></div>",
                            height: 30
                        }); 
                dhxEntryEditGrid.setHeader("SlNo,<select style = 'width:60px;' id='edt_ieF' class = 'edtRpt_select_filter'>\
                                                    <option value ='0'>All</option>\
                                                    <option value ='1'>Income</option>\
                                                    <option value ='2'>Expense</option></select>,\
                                                <input type='text'  id='edt_itmF' class='edtRpt_text_filter' style='width: 90%;' placeholder='Items'>,\
                                                <input type='text'  id='edt_descF' class='edtRpt_text_filter' style='width: 90%;' placeholder='Description'>\
                                                ,<input type='text'  id='edt_trkF' class='edtRpt_text_filter' style='width: 90%;' placeholder='Track Number'>\
                                                ,<input type='text' id='edt_amtF' class='edtRpt_text_filter' style='width: 90%;' placeholder='Amount'>,\
                                                Date,<div id='edt_brnF' style='width: 90%;' placeholder='Branch'></div>,\
                                                <input type='text'  id='edt_usrF' class='edtRpt_text_filter' style='width: 90%;' placeholder='Added By'>,,#master_checkbox");                
                dhxEntryEditGrid.setColAlign("center,center,left,left,left,right,left,left,left,center,center");                
               if(unescape(JGG1P3bDnUSDL24Mui7KzYjj30UjPdWxCCtGkJSHeuo)!='1'){
                dhxEntryEditGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                }else{
                dhxEntryEditGrid.setColTypes("ro,ro,ro,ro,combo,ed,dhxCalendarA,ro,ro,ro,ch");
                }
                dhxEntryEditGrid.setColSorting("str,str,str,str,str,str,date,str,str,str,str")
                dhxEntryEditGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true,false")
                
                var bsl = unescape(JGG1P3bDnUSDL20Mui7KzYjj28UjPdWxCCtGkJSHeuo); 
                if (bsl == 1) {
                    dhxEntryEditGrid.setColumnHidden(9,false);
                }else{
                    dhxEntryEditGrid.setColumnHidden(9,true);
                }
                
//                dhxEntryEditGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 9) {
//                        this.cells(id,ind).cell.title = 'Click here to delete';
//                        return false;
//                    }
//                   
//                });
                
                dhxEntryEditGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na");
                
                dhxEntryEditGrid.setInitWidths("50,100,*,*,100,100,100,100,100,30,70");

                dhxEntryEditGrid.setImagePath("assets/grid/codebase/imgs/"); 
                dhxEntryEditGrid.setSkin("dhx_skyblue");
                dhxEntryEditGrid.init();
                dhxEntryEditGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxEntryEditGrid.enablePaging(true,50,5,"mng_entry_paging",true);
                dhxEntryEditGrid.setPagingSkin("toolbar", "dhx_skyblue");
              //dhxEntryEditGrid.enableSmartRendering(true,50);
               dhxEntryEditGrid.enableEditEvents(true,false,true);
                 var branchCombo = new dhtmlXCombo("edt_brnF");
                 branchCombo.addOption([["",'All']]);
			branchCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=all"), function(){
                            branchCombo.setPlaceholder('Branch');
                            branchCombo.setFilterHandler(function(mask, option){
                                var r = false;
                                if (mask.length == 0) {
                                        r = true;
                                } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                        r = true;
                                }
                                return r;
                            });
			});
                        branchCombo.setOptionWidth(180);
                        branchCombo.attachEvent("onChange", function() {
                            var brComboVal = branchCombo.getSelectedValue();
                            if( !branchCombo.getSelectedValue() && branchCombo.getComboText()) brComboVal = branchCombo.getComboText();
                            $( "#edt_brnF" ).val(brComboVal);                            
                            preTally.EntryEdit.applyRptFilter();
                        });       
                        
                dhxEntryEditGrid.clearAndLoad(loadDataString,function(){
                    
                    preTally.EntryEdit.setEntryGridColType();
                    
                    $( ".edtRpt_text_filter" ).keyup(function() {
                               if(filtrInterval) clearInterval(filtrInterval);
                               filtrInterval = setInterval( function() { 
                                   preTally.EntryEdit.applyRptFilter(); 
                                   clearInterval(filtrInterval); 
                               }, 500);
                           });
                              
                    $( ".edtRpt_select_filter" ).change(function() {
                        preTally.EntryEdit.applyRptFilter(); 
                    });
                    dhxEntryEditGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                        //dhxEntryEditGrid.editStop();
                        if(stage == 0) { 

                                if(cInd == 4){  
                                    var BusinessType  = dhxEntryEditGrid.getUserData(rId, "SH_Track");
                                    if(BusinessType==1){    
                                        TrkColCombo   = dhxEntryEditGrid.cells(rId,cInd).getCellCombo();
                                        TrkColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/tracks.php"));
                                        TrkColCombo.setOptionWidth(150);
                                        TrkColCombo.attachEvent("onChange",function(loader,response){
                                            dhxEntryEditGrid.setUserData(rId,"TR_Id",TrkColCombo.getSelectedValue());
                                        });
                                        
                                    } 
                                    else{
                                        dhtmlx.message("Item is not a Track Entry");
                                        return false;
                                    }
                                }
                                
                                if(cInd == 4 || cInd == 5 || cInd == 6 || cInd == 10){  
                                    var blockedDate  = dhxEntryEditGrid.getUserData(rId, "BES_Date");
                                                                       
                                    var parts1   = blockedDate.split("-");
                                    var BesDate  = new Date(parts1[1] + "/" + parts1[2] + "/" + parts1[0]);
                                    
                                    var parts2   = dhxEntryEditGrid.cells(rId,6).getValue().split("/");
                                    var EntryDate  = new Date(parts2[1] + "/" + parts2[0] + "/" + parts2[2]);
      
                                    if(blockedDate != '' && EntryDate <= BesDate){    
                                        dhtmlx.message("Edit is restricted for this entry.");
                                        return false;
                                    }
                                }
                        }
                        else if(stage==1){
                            if(cInd != 10){   
                            $('#stats_bar_btn').prop("disabled",true);
                            }                            
                        }
                        else if(stage==2){   
                            var BusinessType  = dhxEntryEditGrid.getUserData(rId, "SH_Track");
                            if(BusinessType==1){  
                            if(dhxEntryEditGrid.getUserData(rId,"TR_Id")==null){
                                dhtmlx.message("Invalid Track.Please Select/Swap Track and Re-Try on Row #"+dhxEntryEditGrid.cells(rId,0).getValue());                                
                                dhxEntryEditGrid.cells(rId,4).setTextColor('red');
                                dhxEntryEditGrid.selectRowById(rId);
                                $('#stats_bar_btn').prop("disabled",true);
                                return false;
                            }
                            else{
                                dhxEntryEditGrid.cells(rId,10).setValue(1); 
                                $('#stats_bar_btn').prop("disabled",false);
                            }
                            
                        }
                        else{
                                dhxEntryEditGrid.cells(rId,10).setValue(1); 
                                $('#stats_bar_btn').prop("disabled",false);
                            }
                    }
                        return true;
                    });
                    
                });                
                 
        },
         setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        setEntryGridColType : function() {
            if(dhxEntryEditGrid.getUserData("","TL_Count") == 0) {
                dhxEntryEditGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ch");
            }else {
                if(unescape(JGG1P3bDnUSDL24Mui7KzYjj30UjPdWxCCtGkJSHeuo)!='1'){
                dhxEntryEditGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                }else{
                dhxEntryEditGrid.setColTypes("ro,ro,ro,ro,combo,ed,dhxCalendarA,ro,ro,ro,ch");
                }
                
            }
        }
        
};
})(jQuery, this);