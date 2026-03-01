;(function ($, window, undefined) {
    preTally.ExpenseControl = {        
        view_ExpenseControl : function(){
            if (!dhxMiddleBlockTabs.cells("view_ExpenseControl")) {       
                var filtrInterval ;  
               
                dhxMiddleBlockTabs.addTab("view_ExpenseControl", "<img src='images/icon/chart1.png' style='margin-top:2px;' />&nbsp;&nbsp;Expense Control", 210);
                dhxMiddleBlockTabs.tabs("view_ExpenseControl").setActive();
                dhxExpCntrlLayout =  dhxMiddleBlockTabs.cells("view_ExpenseControl").attachLayout("1C");
                dhxExpCntrlLayout.cells("a").setText("Expense Control");      
                
                expCntrlToolbar = dhxExpCntrlLayout.cells("a").attachToolbar();                
                expCntrlToolbar.setIconsPath("images/icon/default_18/");                
                
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
                
                expCntrlToolbar.addButtonSelect( "rpt_day_filter",'1',"Select Day",Days_Options,'','', true, true, 10, 'select');
                expCntrlToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options,'','', true, true, 10, 'select');
                expCntrlToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options,'','', true, true, 10, 'select');                
               
                
               
                expCntrlToolbar.addSeparator();
                          
                expCntrlToolbar.addText("text_from", null, "From");
                expCntrlToolbar.addInput("rpt_date_from", null, "", 75);
                expCntrlToolbar.addButton("rpt_df_clear", null, "", "close.gif");
                expCntrlToolbar.addSeparator();
                
                expCntrlToolbar.addText("text_till", null, "Till");
                expCntrlToolbar.addInput("rpt_date_till", null, "", 75);
                expCntrlToolbar.addButton("rpt_dt_clear", null, "", "close.gif");
                expCntrlToolbar.addSeparator();
                
                expCntrlToolbar.addButton("rpt_date_filter", null, "Search", "save.gif");
                
              
                
                var ptRpTb_Inp_Frm = expCntrlToolbar.getInput("rpt_date_from");
                ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                ptRpTb_Inp_Frm.onclick = function() {
                    if(expCntrlToolbar.getValue("rpt_date_till")) preTally.ExpenseControl.setSens(ptRpTb_Inp_Til, "max");
                }
                var ptRpTb_Inp_Til = expCntrlToolbar.getInput("rpt_date_till");
                ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                ptRpTb_Inp_Til.onclick = function() {
                    if(expCntrlToolbar.getValue("rpt_date_from")) preTally.ExpenseControl.setSens(ptRpTb_Inp_Frm, "min");
                }     
                
                 expCntrlToolbar.attachEvent("onClick", function(id){  
                   var pId = expCntrlToolbar.getParentId(id);
                   
                    if(id == 'rpt_df_clear')
                        expCntrlToolbar.setValue('rpt_date_from', '', false);
                    
                    if(id == 'rpt_dt_clear')
                        expCntrlToolbar.setValue('rpt_date_till', '', false);
                    
                    if(pId == 'rpt_day_filter') {
                        //console.log(id);
                        var dateToday = id+"."+Date.today().toString("MM.yyyy");                        
                        expCntrlToolbar.setValue('rpt_date_from', dateToday, false);
                        expCntrlToolbar.setValue('rpt_date_till', dateToday, false);
                        expCntrlToolbar.setItemText('rpt_month_filter', 'Select Month');
                        expCntrlToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.ExpenseControl.applyExpCntrlFilter();
                        
                    }
                    if(pId == 'rpt_month_filter') {
                        //console.log(id);
                        id = id.substr(1);
                        var tmpDate     = new Date();
                        var yeartoolbar = parseInt(expCntrlToolbar.getItemText('rpt_year_filter')); //01-01-2026
                        yeartoolbar     = (!Number.isInteger(yeartoolbar)) ? tmpDate.getFullYear() : yeartoolbar; //01-01-2026
                        var firstDay    = new Date(yeartoolbar, id-1, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(yeartoolbar, id, 0).toString("dd.MM.yyyy");
                        expCntrlToolbar.setValue('rpt_date_from', firstDay, false);
                        expCntrlToolbar.setValue('rpt_date_till', lastDay, false);
                        expCntrlToolbar.setItemText('rpt_day_filter', 'Select Day');
                        //expCntrlToolbar.setItemText('rpt_year_filter', 'Select Year');
                        preTally.ExpenseControl.applyExpCntrlFilter();
                        
                    }
                    if(pId == 'rpt_year_filter') {
                        var tmpDate    = new Date();
                        var firstDay    = new Date(id, 00, 1).toString("dd.MM.yyyy");
                        var lastDay     = new Date(id, 11, 31).toString("dd.MM.yyyy");
                        
                        expCntrlToolbar.setValue('rpt_date_from', firstDay, false);
                        expCntrlToolbar.setValue('rpt_date_till', lastDay, false);
                        expCntrlToolbar.setItemText('rpt_day_filter', 'Select Day');
                        expCntrlToolbar.setItemText('rpt_month_filter', 'Select Month');
                        preTally.ExpenseControl.applyExpCntrlFilter();
                        
                    }
                                       
                    if(id == 'rpt_date_filter'){                     
                        expCntrlToolbar.setItemText('rpt_day_filter', 'Select Day');
                        expCntrlToolbar.setItemText('rpt_month_filter', 'Select Month');
                        expCntrlToolbar.setItemText('rpt_year_filter', 'Select Year');
                       preTally.ExpenseControl.applyExpCntrlFilter();
                    }  
                });
                
                // init calendar;
                ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                ptRpTb_Calendar.setDateFormat("%d.%m.%Y");                
                expCntrlToolbar.setAlign('right');                
//                preTally.ExpenseControl.applyExpCntrlFilter();
                expCntrlStatusBar = dhxMiddleBlockTabs.tabs("view_ExpenseControl").attachStatusBar({
                    text  : '<div class="expCntrl_cnt_tot"># : 0</div><div id="expCntrl_paging"></div>',
                    height: 35
                }); 
                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxExpCntrlGrid = dhxExpCntrlLayout.cells("a").attachGrid();                 
                dhxExpCntrlGrid.setHeader("Msg,Edit,SlNo,<select style = 'width:60px;' id='ie_ECF' class = 'expCntrl_selectFilter'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,\
                                            <input type='text'  id='it_ECF' class='expCntrl_text_filter' style='width: 90%;' placeholder='Items'>,\
                                            <input type='text'  id='ds_ECF' class='expCntrl_text_filter' style='width: 90%;' placeholder='Description'>,\
                                            <input type='text'  id='tr_ECF' class='expCntrl_text_filter' style='width: 90%;' placeholder='Track'>,\
                                            <div style='text-align:left;'> <select style = 'width:80px;' id='amt_ECF' class = 'expCntrl_selectFilter'><option value ='0'>All</option><option value ='1'>Below Minimum Amount</option><option value ='2'>Normal Amount</option><option value ='3'>Above Maximum Amount</option></select><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='7' class='btn_Sort_Exp'/></div>,\
                                            Minimum,Maximum,Date<img src='images/icon/sort-descending-icon.png' title='Click here to Sort' colNum='10' class='btn_Sort_Exp'/>,<div id='br_ECF' style='width: 90%;' placeholder='Branch'></div>,\
                                            <input type='text'  id='us_ECF' class='expCntrl_text_filter' style='width: 90%;' placeholder='Added By'>");   
                dhxExpCntrlGrid.setColAlign("center,center,center,center,left,left,left,right,right,right,left,left,left,")  
                dhxExpCntrlGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,");
//                dhxExpCntrlGrid.setColSorting("str,str,str,str,str,str,str,date,str,str")
                dhxExpCntrlGrid.enableTooltips("true,true,false,false,false,false,false,false,false,false,false,false,false,")
                dhxExpCntrlGrid.enableColSpan(true);
                
                dhxExpCntrlGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,");
                
                dhxExpCntrlGrid.setInitWidths("40,40,50,100,*,*,100,120,100,100,100,100,100,");

                dhxExpCntrlGrid.setImagePath("assets/grid/codebase/imgs/"); 
                dhxExpCntrlGrid.setSkin("dhx_skyblue");
                dhxExpCntrlGrid.init();
                dhxExpCntrlGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                dhxExpCntrlGrid.enablePaging(true,50,5,"expCntrl_paging",true);
                dhxExpCntrlGrid.setPagingSkin("toolbar", "dhx_skyblue");
                //dhxExpCntrlGrid.enableSmartRendering(true,50);
                
//                attachEvent("onMouseOver", function(id,ind) { 
//                    if(ind == 0) {
//                        this.cells(id,ind).cell.title = 'Click here to sent message';
//                        return false;
//                    }
//                    if(ind == 1) {
//                        this.cells(id,ind).cell.title = 'Click here to edit';
//                        return false;
//                    }
//                });
                
                var branchCombo = new dhtmlXCombo("br_ECF");
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
                    $( "#br_ECF" ).val(brComboVal);                            
                    preTally.ExpenseControl.applyExpCntrlFilter();
                });       
                var srtFlg = 0;
                colId = '';
                var orderBy="";
                $('.btn_Sort_Exp').click(function() { 
                    colId = $(this).attr("colNum");
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        preTally.ExpenseControl.applyExpCntrlFilter(srtFlg); 
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        preTally.ExpenseControl.applyExpCntrlFilter(srtFlg); 
                        srtFlg = 0;
                        }
                    });
                
                $( ".expCntrl_text_filter" ).keyup(function() {
                    if(filtrInterval) clearInterval(filtrInterval);
                    filtrInterval = setInterval( function() { 
                        preTally.ExpenseControl.applyExpCntrlFilter(); 
                        clearInterval(filtrInterval); 
                    }, 500);
                });

                $( ".expCntrl_selectFilter" ).change(function() {
                    preTally.ExpenseControl.applyExpCntrlFilter(); 
                });
                    
                
                
                var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
                if(bsp==4)
                {   var date = new Date();
                    var m_names = new Array("January", "February", "March","April", "May", "June", "July", "August", "September","October", "November", "December"); 
                    var month=date.getMonth();
                    tDate = Date.today().toString("dd.MM.yyyy"); 
                    expCntrlToolbar.setValue('rpt_date_from', "01."+Date.today().toString("MM.yyyy"));
                    expCntrlToolbar.setValue('rpt_date_till', tDate);
                    expCntrlToolbar.setItemText('rpt_month_filter',m_names[month]);
//                    edtReportInit=0;
                }
                else
                {
                    cDate = Date.today().toString("dd.MM.yyyy"); 
                    expCntrlToolbar.setValue('rpt_date_till', cDate);
                    expCntrlToolbar.setValue('rpt_date_from', cDate);
                    expCntrlToolbar.setItemText('rpt_day_filter', 'Today');
//                    edtReportInit=0;
                } 
                expCntrlFilterParams = '&f='+expCntrlToolbar.getValue("rpt_date_from")+'&t='+expCntrlToolbar.getValue("rpt_date_till");
                
                dhxExpCntrlGrid.loadXML(preTally.Initialize.encryptURL("requisites/expCntrlRpt.php&"+expCntrlFilterParams), function() {
                        $('.expCntrl_cnt_tot').html("# : "+dhxExpCntrlGrid.getUserData("", "TL_Count")+" ");
                        preTally.Settings.progressOff(true, dhxLayout, null);
                });
            }
            else {
                dhxMiddleBlockTabs.cells("view_ExpenseControl").setActive();
            }
            
        },
        applyExpCntrlFilter:function (srtFlg){
            var filterValue = new Array($('#ie_ECF').val(), $('#it_ECF').val(), $('#ds_ECF').val(),$('#tr_ECF').val(), $('#amt_ECF').val(), $('#br_ECF').val(),$('#us_ECF').val(),srtFlg,colId);            
            expCntrlFilterParams = '&f='+expCntrlToolbar.getValue("rpt_date_from")+'&t='+expCntrlToolbar.getValue("rpt_date_till");
            dhxExpCntrlGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);            
            dhxExpCntrlGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/expCntrlRpt.php&filter="+filterValue+"&"+expCntrlFilterParams), function() {
                $('.expCntrl_cnt_tot').html("# : "+dhxExpCntrlGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        sendCorrectionMsg: function(inp, BS_Id, SH_Id) {

            var dhxMsgWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            
            var rptMsgWin = dhxMsgWin.createWindow("rptMsgWin_" + BS_Id, x, y, 540, 400);
            rptMsgWin.center();
            rptMsgWin.button("minmax1").hide();
            rptMsgWin.button("minmax2").hide();
            rptMsgWin.button("park").hide();
            rptMsgWin.setModal(true);
            rptMsgWin.setText("Send Correction Message");
            
            rptMsgForm = rptMsgWin.attachForm();
            preTally.Settings.progressOn(true, rptMsgWin, null);            
            rptMsgForm.loadStruct(preTally.Initialize.encryptURL("requisites/sendCorrectionMsg.php"), function() {                
                rptMsgForm.setItemValue("US_Id", dhxExpCntrlGrid.getUserData(BS_Id, "US_Id"));
                rptMsgForm.setItemValue("MSG_To", dhxExpCntrlGrid.getUserData(BS_Id, "US_Name"));
                rptMsgForm.setItemValue("Entry", dhxExpCntrlGrid.getUserData(BS_Id, "Entry"));
                rptMsgForm.setItemValue("BS_Id", BS_Id);
                rptMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptMsgWin, null);
                rptMsgForm.attachEvent("onButtonClick", function(name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptMsgWin, null);
                            var params = "type=expcntrl";
                            rptMsgForm.send(preTally.Initialize.encryptURL("warehouse/sendCorrectionMsg.php&"+params), function(loader, response) {
                                preTally.Settings.progressOff(true, rptMsgWin, null);
                                if(response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    rptMsgForm.clear();
                                    dhxMsgWin.window("rptMsgWin_" + BS_Id).close();
                                }
                                else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if(name == 'addRecpt') {
                        dhxUserWin = new dhtmlXWindows();
                        var recpWin = dhxUserWin.createWindow("addRecpWin", x, y, 1050, 350);
                        recpWin.button("minmax1").hide();
                        recpWin.button("minmax2").hide();
                        recpWin.button("park").hide();
                        recpWin.center();
                        recpWin.setModal(true);
                        recpWin.setText("Add Recipients");
                        
                        listUserGrid = recpWin.attachGrid();
                        listUserGrid.enableAutoWidth(true);
                        preTally.Settings.progressOn(true, recpWin, null);         

                        var listRecipientsXML= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked="+rptMsgForm.getItemValue('US_Id')); 
                        listUserGrid.parse(listRecipientsXML.xmlDoc.responseText,function(){
                            preTally.Settings.progressOff(true, recpWin, null);
                        });

                        recpWin.attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.ExpenseControl.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                        
                    } else {
                        dhxMsgWin.window("rptMsgWin_" + BS_Id).close();
                    }
                });
            });
        },
        addRecipients: function() {           
            var userDetails = []; 
            var userIds     = [];
            
            listUserGrid.forEachRow(function(rId){
                if(listUserGrid.cells(rId,6).getValue() == 1) {
                    userDetails.push(listUserGrid.cells(rId,2).getValue());
                    userIds.push(rId);
                }
            });
            
            rptMsgForm.setItemValue("MSG_To",userDetails);
            rptMsgForm.setItemValue('US_Id',userIds);
            dhxUserWin.window("addRecpWin").close();
             
        },
        editExpCnrtlEntryDetails : function (inp, BS_Id, SH_Id) {
            
            dhxBSMsgDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            
            bsMsgDetailsWin = dhxBSMsgDetails.createWindow("bsDetailsWinMsg_" + BS_Id, x, y, 900, 550);
            bsMsgDetailsWin.center();
            bsMsgDetailsWin.button("minmax1").hide();
            bsMsgDetailsWin.button("minmax2").hide();
            bsMsgDetailsWin.button("park").hide();
            bsMsgDetailsWin.setModal(true);
            bsMsgDetailsWin.setText("Edit Details");
            bsMsgDetailsForm = bsMsgDetailsWin.attachForm();
            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);

            var params = "SHID="+SH_Id+"&BSID="+BS_Id;
            bsMsgDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportECDetailsEdit.php&"+params), function() {

                preTally.Settings.progressOff(true, bsMsgDetailsWin, null);
                var IT_Note;
                var DS_Note;
                var BS_MHType = bsMsgDetailsForm.getItemValue("MH_Type");

                if(BS_MHType == 1) {
                    IT_Note = 'NAME OF THE INCOME';
                    DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                } else {
                    IT_Note = 'NAME OF THE EXPENSE';
                    DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                }

                bsMsgDetailsForm.setNote('MH_Type', { text: "ENTRY TYPE", width : "100" });
                bsMsgDetailsForm.setNote('IT_Id', { text: IT_Note, width : "100" });
                bsMsgDetailsForm.setNote('BS_Description', { text: DS_Note , width : "200" });
                bsMsgDetailsForm.setNote('TR_Id', { text: "TRACK" , width : "100" });

                var BS_MHCombo      =   bsMsgDetailsForm.getCombo("MH_Type");
                var BS_ITCombo      =   bsMsgDetailsForm.getCombo("IT_Id");
                var BS_DSCombo      =   bsMsgDetailsForm.getCombo("BS_Description");
                var BS_TRCombo      =   bsMsgDetailsForm.getCombo("TR_Id");
                var BS_BNKCombo     =   bsMsgDetailsForm.getCombo("BNK_Id");
                var BS_BBCombo      =   bsMsgDetailsForm.getCombo("BB_Id");
                var BS_BACombo      =   bsMsgDetailsForm.getCombo("BA_Id");
                var BS_BChqCombo    =   bsMsgDetailsForm.getCombo("CHQ_Number");

                if(bsMsgDetailsForm.getItemValue("HiddenTR_Id") == 0) {
                    bsMsgDetailsForm.hideItem("TR_Id");
                    bsMsgDetailsForm.setRequired("TR_Id",false);
                    BS_TRCombo.setComboValue('0');
                    BS_TRCombo.setComboText('0');
                } 

                BS_ITCombo.enableFilteringMode(true);
                BS_DSCombo.enableFilteringMode(true);

                BS_ITCombo.setOptionWidth(400);
                BS_DSCombo.setOptionWidth(300);

                BS_MHCombo.attachEvent("onClose", function() {
                        BS_ITCombo.clearAll();
                        BS_ITCombo.setComboText("");
                        BS_ITCombo.setComboValue("");
                        BS_DSCombo.clearAll();
                        BS_DSCombo.setComboText("");
                        BS_DSCombo.setComboValue("");

                        BS_MHType = BS_MHCombo.getSelectedValue();
                        var params="type="+BS_MHType;
                        BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&"+params));
                        if(BS_MHType == 1) {
                            IT_Note = 'NAME OF THE INCOME';
                            DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                        } else {
                            IT_Note = 'NAME OF THE EXPENSE';
                            DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                        }
                        bsMsgDetailsForm.setNote('IT_Id', { text: IT_Note , width : "100" });
                        bsMsgDetailsForm.setNote('BS_Description', { text: DS_Note , width : "200" });
                        BS_TRCombo.setComboValue('0');
                        BS_TRCombo.setComboText('0');
                });

                BS_ITCombo.attachEvent("onClose", function() { 
                    BS_DSCombo.clearAll();
                    BS_DSCombo.setComboText('');
                    var BS_ITId = BS_ITCombo.getSelectedValue();                        
                    if (!isNaN(BS_ITId) && (BS_ITId != 0)) {
                        bsMsgDetailsForm.setItemValue("IT_Flag","0"); 
                        var params="IT_Id=" + BS_ITId;
                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&"+params)
                        }).done(function(data) { 
                            BS_TRCombo.setComboValue('0');
                            BS_TRCombo.setComboText('0');
                            BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&"+params), function() {});
                            if(!bsMsgDetailsForm.isItemHidden("TR_Id")) {
                                bsMsgDetailsForm.hideItem("TR_Id");
                                BS_TRCombo.setComboValue('0');
                                BS_TRCombo.setComboText('0');
                                bsMsgDetailsForm.setRequired("TR_Id",false)
                            }
                            if(data == 1){
                                var filtrInterval;
                                bsMsgDetailsForm.showItem("TR_Id"); 
//                                    BS_TRCombo  =   bsMsgDetailsForm.getCombo("TR_Id");
                                BS_TRCombo.setComboValue('');
                                BS_TRCombo.setComboText('');
                                bsMsgDetailsForm.setRequired("TR_Id",true);
                            }                           
                        });
                    }
                });

                bsMsgDetailsForm.setItemValue("MH_Type",bsMsgDetailsForm.getItemValue("HiddenMH_Type"));
                if (bsMsgDetailsForm.isItem("IT_Id")) { 
                    var params = "type=" + bsMsgDetailsForm.getItemValue("MH_Type");
                    BS_ITCombo.load(preTally.Initialize.encryptURL("requisites/rptEditItems.php&"+params),function(){
                        if(bsMsgDetailsForm.getItemValue("IT_Status")!= '4' && bsMsgDetailsForm.getItemValue("IT_Status")!= '0'){
                        bsMsgDetailsForm.setItemValue("IT_Id",bsMsgDetailsForm.getItemValue("HiddenIT_Id"));
                    }else{
                            BS_ITCombo.setComboText(bsMsgDetailsForm.getItemValue("IT_Name"));
                            bsMsgDetailsForm.setItemValue("IT_Flag","del_item");                             
                        } 
                    });
                }

                if (bsMsgDetailsForm.isItem("BS_Description")) { 
                    var params = "IT_Id=" + bsMsgDetailsForm.getItemValue("HiddenIT_Id")+"&DS_Id=" + bsMsgDetailsForm.getItemValue("DS_Id")+"&updateType=rpt";
                    BS_DSCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&"+params), function() {
                       bsMsgDetailsForm.setItemValue("BS_Description",bsMsgDetailsForm.getItemValue("DS_Id"));
                    });
                }
                if (bsMsgDetailsForm.isItem("TR_Id")) { 
                    BS_TRCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/tracks.php"));
                    var params = "mask=" + bsMsgDetailsForm.getItemValue("TR_Track");
                    BS_TRCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&"+params),function(){
                        bsMsgDetailsForm.setItemValue("TR_Id",bsMsgDetailsForm.getItemValue("HiddenTR_Id"));
                    });
                }
                if (bsMsgDetailsForm.isItem("PM_Id")) {
                    bsMsgDetailsForm.hideItem("BS_PaidDate");
                    bsMsgDetailsForm.hideItem("BS_PayType");
                    bsMsgDetailsForm.hideItem("BNK_Id");
                    bsMsgDetailsForm.hideItem("BB_Id");
                    bsMsgDetailsForm.hideItem("BA_Id");
                    bsMsgDetailsForm.hideItem("CHQ_Number");
                    bsMsgDetailsForm.hideItem("BS_Transaction");
                    bsMsgDetailsForm.hideItem("BS_PayersBank");
                    bsMsgDetailsForm.hideItem("BS_PayersChQ");
                    var item_list = new Array( "BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id","CHQ_Number", "BS_PayersChQ", "BS_PayersBank" );
                    var patmentMode = bsMsgDetailsForm.getCombo("PM_Id");
                    patmentMode.setComboValue(bsMsgDetailsForm.getUserData("PM_Id","cId")); 

                    if (bsMsgDetailsForm.getUserData("PM_Id","cId") == '2'){
                        for(i=0;i<=10;i++){
                           bsMsgDetailsForm.showItem(item_list[i]); 
                        }
                        bsMsgDetailsForm.setItemValue("PM_Id",bsMsgDetailsForm.getUserData("PM_Id","cId"));

                        if (bsMsgDetailsForm.isItem("BNK_Id")) {  
                            bsMsgDetailsForm.setItemValue("BNK_Id",bsMsgDetailsForm.getUserData("BNK_Id","cId"));
                        }
                        if (bsMsgDetailsForm.isItem("BB_Id")) {  
                            var params = "Bnk_Id=" + bsMsgDetailsForm.getUserData("BNK_Id","cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&"+params), function(xml){
                                var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                bsMsgDetailsForm.setItemValue("BB_Id",BBName);
                                BS_BBCombo.setComboValue(bsMsgDetailsForm.getUserData("BB_Id","cId"));
                            });
                        }
                        if (bsMsgDetailsForm.isItem("BA_Id")) {  
                            var params="BB_Id=" + bsMsgDetailsForm.getUserData("BB_Id","cId");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&"+params), function(xml){
                                var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                bsMsgDetailsForm.setItemValue("BA_Id",BAName);
                                BS_BACombo.setComboValue(bsMsgDetailsForm.getUserData("BA_Id","cId"));
                            });
                        }
                        if (bsMsgDetailsForm.isItem("CHQ_Number")) { 
                            var params="BA_Id=" + bsMsgDetailsForm.getUserData("BA_Id","cId");
                            BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);

                            BS_BChqCombo.addOption([[bsMsgDetailsForm.getUserData("CHQ_Number","cId"),bsMsgDetailsForm.getUserData("CHQ_Number","cValue")]]);
                            BS_BChqCombo.setComboValue(bsMsgDetailsForm.getUserData("CHQ_Number","cId"));
                        }
                    }

                    patmentMode.attachEvent("onChange", function() {
                        var pmMode = patmentMode.getSelectedValue();
                        for(i=0;i<=10;i++){
                             if (pmMode == '1'){ 
                                bsMsgDetailsForm.hideItem(item_list[i]); }
                             else {  
                                bsMsgDetailsForm.showItem(item_list[i]); }
                        }

                    });
                    if (bsMsgDetailsForm.isItem("BS_PayType")) {
                        BS_PayTypeCombo = bsMsgDetailsForm.getCombo("BS_PayType");
                        bsMsgDetailsForm.setItemValue("BS_PayType",bsMsgDetailsForm.getUserData("BS_PayType","cId"));

                        BS_PayTypeCombo.attachEvent("onChange", function() {
                            var pmType = BS_PayTypeCombo.getSelectedValue();
                            if(pmType == '2'){
                                bsMsgDetailsForm.hideItem("BS_Transaction");
                                bsMsgDetailsForm.showItem("CHQ_Number");
                            }
                            if(pmType == '3'){
                                bsMsgDetailsForm.setItemLabel("BS_Transaction","DD Number" );
                                bsMsgDetailsForm.showItem("BS_Transaction");
                                bsMsgDetailsForm.hideItem("CHQ_Number");
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                            }
                            if(pmType == '4' || pmType == '5' || pmType == '6'){
                                bsMsgDetailsForm.setItemLabel("BS_Transaction","Transaction Id" );
                                bsMsgDetailsForm.showItem("BS_Transaction");
                                bsMsgDetailsForm.hideItem("CHQ_Number");
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                            }
                        });
                    }
                }

                if (bsMsgDetailsForm.isItem("BB_Id")) {

                    BS_BBCombo.readonly(true);
                    BS_BACombo.readonly(true);

                    BS_BNKCombo.attachEvent("onClose", function() {
                        BS_BBCombo.clearAll();
                        BS_BACombo.clearAll();

                        BS_BBCombo.setComboText('');
                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if(bsMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_id = BS_BNKCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BS_BBCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&"+params), function() {
                            });
                        }
                        bsMsgDetailsForm.setItemValue("updateType","");
                    });

                    BS_BBCombo.attachEvent("onClose", function() {
                        BS_BACombo.clearAll();                        

                        BS_BACombo.setComboText('');
                        BS_BACombo.setComboValue('');

                        if(bsMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll();
                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');
                        }

                        var bnk_acc_id = BS_BBCombo.getSelectedValue();
                        if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                            var params="BB_Id=" + bnk_acc_id;
                            BS_BACombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&"+params), function() {
                            });
                        }
                        bsMsgDetailsForm.setItemValue("updateType","");
                    });

                    BS_BACombo.attachEvent("onClose", function() {

                        if(bsMsgDetailsForm.isItem("CHQ_Number")){
                            BS_BChqCombo.clearAll()

                            BS_BChqCombo.setComboText('');
                            BS_BChqCombo.setComboValue('');

                            var BNK_AC_Id = BS_BACombo.getSelectedValue();
                            if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                var params="BA_Id=" + BNK_AC_Id;
                                BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);
                            }
                        }
                        bsMsgDetailsForm.setItemValue("updateType","");
                    });
                }
                if (bsMsgDetailsForm.isItem("CHQ_Number")){
                    BS_BChqCombo.attachEvent("onClose", function() {
                        bsMsgDetailsForm.setItemValue("updateType","");
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_User")) { 

                        UserCombo  =  bsMsgDetailsForm.getCombo("BS_User");
                        LCCombo    =  bsMsgDetailsForm.getCombo("BS_PrchsdFor");

                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                            var prucForName = LCCombo.load(xml.xmlDoc.responseText);
                            bsMsgDetailsForm.setItemValue("BS_PrchsdFor",prucForName);
                            LCCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_PrchsdFor","cId"));
                        });

                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                            var User_Name = UserCombo.load(xml.xmlDoc.responseText);
                            bsMsgDetailsForm.setItemValue("BS_User",User_Name);
                            UserCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_User","cId"));
                        });

                        LCCombo.attachEvent("onClose", function() {
                            UserCombo.clearAll();
                            StaffCombo.clearAll();

                            UserCombo.setComboText('');
                            StaffCombo.setComboText('');

                            var BS_Office = LCCombo.getSelectedValue(); 
                            if ((BS_Office) && (BS_Office != 0)) { 
                                var params="LC_Id="+ BS_Office;
                                UserCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&"+params)); 
                            }
                        });

                }
                if (bsMsgDetailsForm.isItem("BS_StaffId")) { 
                        UserCombo  =  bsMsgDetailsForm.getCombo("BS_User");
                        StaffCombo =  bsMsgDetailsForm.getCombo("BS_StaffId");
                        StaffCombo.readonly(true);

                        UserCombo.attachEvent("onClose", function() {
                            StaffCombo.clearAll();

                            StaffCombo.setComboText('');

                            var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                            if ((BS_User) && (BS_User != 0)) {
                                var params="Emp_Id=" + BS_User;
                                StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&"+params), function() {
                                });
                            }
                        });

                }
                if (bsMsgDetailsForm.isItem("LC_Id")) {  
                    var LC_IdCombo = bsMsgDetailsForm.getCombo("LC_Id");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                        var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("LC_Id",LC_Name);
                        LC_IdCombo.setComboValue(bsMsgDetailsForm.getUserData("LC_Id","cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_IEByLC")) {  
                    var ieByLCCombo = bsMsgDetailsForm.getCombo("BS_IEByLC");
                    var ieByLCid   = bsMsgDetailsForm.getUserData("BS_IEByLC","cId");
                    var ieByLCname = bsMsgDetailsForm.getUserData("BS_IEByLC","cValue");
                    var params="";
                    if(!ieByLCid || ieByLCid == 0){
                        params = "&mask=Self";
                    }else{
                        params = "&mask="+ieByLCname;
                    }

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"+params), function(xml){
                        ieByLCCombo.load(xml.xmlDoc.responseText);                                       
                        if(ieByLCid != '') bsMsgDetailsForm.setItemValue("BS_IEByLC",ieByLCid);

                        var ieByUSCombo = bsMsgDetailsForm.getCombo("BS_IEByUS");                                                              
                        var ieByUSid    = bsMsgDetailsForm.getUserData("BS_IEByUS","cId");
                        var ieByUSname  = bsMsgDetailsForm.getUserData("BS_IEByUS","cValue");
                        var params="";
                        if(!ieByUSid){
                            params = "&ctype=check&mask=Self&LCId="+ieByLCCombo.getSelectedValue();
                        }else{
                            params = "&ctype=check&mask="+ieByUSname+"&LCId="+ieByLCCombo.getSelectedValue();
                        }

                        ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php"+params));
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
                            ieByUSCombo.load(xml.xmlDoc.responseText);
                            if(ieByUSid != '') bsMsgDetailsForm.setItemValue("BS_IEByUS",ieByUSid);
                        });

                        ieByLCCombo.attachEvent("onChange", function() {
                            ieByUSCombo.clearAll();
                            ieByUSCombo.setComboValue(0);
                            ieByUSCombo.setComboText('');

                            var params = "&LCId="+ieByLCCombo.getSelectedValue();
                            ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php&ctype=check"+params),false);
                        });
                    });
                }   
                if (bsMsgDetailsForm.isItem("BS_PaidBy")) {  
                    var BS_PaidByCombo = bsMsgDetailsForm.getCombo("BS_PaidBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var paidByUser_Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_PaidBy",paidByUser_Name);
                        BS_PaidByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_PaidBy","cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_PaidTo")) {  
                    bsMsgDetailsForm.setItemValue("BS_PaidTo", bsMsgDetailsForm.getItemValue("DS_Description"));
                    bsMsgDetailsForm.setReadonly("BS_PaidTo", true);
                }
                if (bsMsgDetailsForm.isItem("BS_PayTime")) {  
                    bsMsgDetailsForm.setItemValue("BS_PayTime",bsMsgDetailsForm.getItemValue("BS_PayTime"));
                } 
                if (bsMsgDetailsForm.isItem("BS_StaffId")) {  
                    var BS_StaffIdCombo = bsMsgDetailsForm.getCombo("BS_StaffId");
                    BS_StaffIdCombo.addOption([[bsMsgDetailsForm.getUserData("BS_StaffId","cId"),bsMsgDetailsForm.getUserData("BS_StaffId","cValue")]]);
                    BS_StaffIdCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_StaffId","cId"));
                }
                if (bsMsgDetailsForm.isItem("BS_AprovlGvnBy")) {  
                    var BS_AprovlGvnByCombo = bsMsgDetailsForm.getCombo("BS_AprovlGvnBy");

                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var aprlUser_Name = BS_AprovlGvnByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("LC_Id",aprlUser_Name);
                        BS_AprovlGvnByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_AprovlGvnBy","cId"));
                    });

                }
                if (bsMsgDetailsForm.isItem("BS_AprovlTknBy")) {  
                    var BS_AprovlTknByCombo = bsMsgDetailsForm.getCombo("BS_AprovlTknBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var aprlTknUser_Name = BS_AprovlTknByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_AprovlTknBy",aprlTknUser_Name);
                        BS_AprovlTknByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_AprovlTknBy","cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_AprovdDate")) { 
                    bsMsgDetailsForm.setItemValue(bsMsgDetailsForm.getUserData("BS_AprovdDate","cValue"));
                }
                if (bsMsgDetailsForm.isItem("BS_RcvdFrm")) {  
                    bsMsgDetailsForm.setReadonly("BS_RcvdFrm", true);
                }
                if (bsMsgDetailsForm.isItem("BS_RcvdBy")) { 
                    var BS_RcvdByCombo = bsMsgDetailsForm.getCombo("BS_RcvdBy");
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                        var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                        bsMsgDetailsForm.setItemValue("BS_RcvdBy",rcvdByUser_Name);
                        BS_RcvdByCombo.setComboValue(bsMsgDetailsForm.getUserData("BS_RcvdBy","cId"));
                    });
                }
                if (bsMsgDetailsForm.isItem("BS_PettyCashRefId")) { 
                    bsMsgDetailsForm.setItemValue("BS_PettyCashRefId",bsMsgDetailsForm.getUserData("BS_PettyCashRefId","cId"));
                    var BS_PCRefidCombo = bsMsgDetailsForm.getCombo("BS_PettyCashRefId"); 
                    BS_PCRefidCombo.setOptionWidth(350);
                    
                    BS_PCRefidCombo.attachEvent("onXLE", function(){
                        BS_PCRefidCombo.deleteOption(BS_Id);
                    });

                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BS_Id+"&PCRefId="+bsMsgDetailsForm.getUserData("BS_PettyCashRefId","cId"))
                    }).done(function(data) { 
                        bsMsgDetailsForm.setItemValue("PettyCashAmount",data);
                    });
                    BS_PCRefidCombo.attachEvent("onClose",function(){

                        $.ajax({
                            url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BS_Id+"&PCRefId="+BS_PCRefidCombo.getSelectedValue())
                        }).done(function(data) { 
                            bsMsgDetailsForm.setItemValue("PettyCashAmount",data);
                        });

                    });
                }

                bsMsgDetailsForm.attachEvent("onButtonClick", function(name) {

                    if (name == "balSheetDetailsSave") {
                        
//                        var messageValidate = bsMsgDetailsForm.validate(); 
                        var values = bsMsgDetailsForm.getFormData();
                        var CstmValidate = preTally.Validate.Validate(values, 'BS_Amount', bsMsgDetailsForm, 'decimal'); 

                        if(CstmValidate == false) {
                            bsMsgDetailsForm.setValidateCss('BS_Amount', CstmValidate,  'validate_red');
                            return false;
                        }

//                        bsMsgDetailsForm.setValidation('BS_Amount', 'ValidInteger,NotEmpty');
                        if (bsMsgDetailsForm.isItem("BS_Price")) bsMsgDetailsForm.setValidation('BS_Price', 'ValidNumeric');
                        if (bsMsgDetailsForm.isItem("BS_Quantity")) bsMsgDetailsForm.setValidation('BS_Quantity', 'ValidNumeric');
                        if (bsMsgDetailsForm.isItem("BS_StaffId")) bsMsgDetailsForm.setValidation('BS_StaffId', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_PrchsdFor")) bsMsgDetailsForm.setValidation('BS_PrchsdFor', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_User")) bsMsgDetailsForm.setValidation('BS_User', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_AprovlGvnBy")) bsMsgDetailsForm.setValidation('BS_AprovlGvnBy', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_AprovlTknBy")) bsMsgDetailsForm.setValidation('BS_AprovlTknBy', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("CN_Id")) bsMsgDetailsForm.setValidation('CN_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("ST_Id")) bsMsgDetailsForm.setValidation('ST_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_Persons")) bsMsgDetailsForm.setValidation('BS_Persons', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("LC_Id")) bsMsgDetailsForm.setValidation('LC_Id', 'ValidInteger');
                        if (bsMsgDetailsForm.isItem("BS_PaidBy")) bsMsgDetailsForm.setValidation('BS_PaidBy', 'ValidInteger');
                        //if (bsMsgDetailsForm.isItem("CHQ_Number")) bsMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty');
                        if (bsMsgDetailsForm.isItem("IT_Id")) bsMsgDetailsForm.setValidation('IT_Id', 'ValidInteger,NotEmpty');
                        if (bsMsgDetailsForm.isItem("BS_Description")) bsMsgDetailsForm.setValidation('BS_Description', 'NotEmpty');
                        bsMsgDetailsForm.setValidation('BS_IEByUS', 'ValidInteger,NotEmpty');
                        bsMsgDetailsForm.setValidation('BS_IEByLC', 'ValidInteger,NotEmpty');
                    
                    
                        if (!bsMsgDetailsForm.isItemHidden("TR_Id")) {
                            bsMsgDetailsForm.setValidation('TR_Id', 'ValidInteger,NotEmpty');
                        }else{  bsMsgDetailsForm.clearValidation('TR_Id'); }

                        if (bsMsgDetailsForm.isItem("PM_Id")) {
                            var patmentMode = bsMsgDetailsForm.getCombo("PM_Id");
                            var pmMode = patmentMode.getSelectedValue();
                            if (pmMode == 2){ 
                                bsMsgDetailsForm.setValidation('BNK_Id', 'NotEmpty'); 
                                bsMsgDetailsForm.setValidation('BB_Id', 'NotEmpty'); 
                                bsMsgDetailsForm.setValidation('BA_Id', 'NotEmpty'); 
                                bsMsgDetailsForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                                bsMsgDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty'); 

                                if (bsMsgDetailsForm.isItem("BS_PayType")) {
                                    var paymentType = bsMsgDetailsForm.getCombo("BS_PayType");
                                    var pmType = paymentType.getSelectedValue();
                                    if(pmType == '2'){
                                        bsMsgDetailsForm.setValidation('CHQ_Number', 'NotEmpty'); 
                                    }else if(pmType == '3' || pmType == '4' || pmType == '5'){
                                       bsMsgDetailsForm.setItemValue("CHQ_Number","");
                                       bsMsgDetailsForm.setValidation('CHQ_Number', 'null'); 
                                    }
                                }
                            } else {
                                bsMsgDetailsForm.setItemValue("BNK_Id","");
                                bsMsgDetailsForm.setItemValue("BB_Id","");
                                bsMsgDetailsForm.setItemValue("BB_Id","");
                                bsMsgDetailsForm.setItemValue("BA_Id","");
                                bsMsgDetailsForm.setItemValue("CHQ_Number","");
                                bsMsgDetailsForm.setItemValue("BS_PayersBank","");
                                bsMsgDetailsForm.setItemValue("BS_PayersChQ","");

                                bsMsgDetailsForm.setValidation('BNK_Id', 'null'); 
                                bsMsgDetailsForm.setValidation('BB_Id', 'null'); 
                                bsMsgDetailsForm.setValidation('BB_Id', 'null');
                                bsMsgDetailsForm.setValidation('BA_Id', 'null');
                                bsMsgDetailsForm.setValidation('CHQ_Number', 'null');
                                bsMsgDetailsForm.setValidation('BS_PayersBank', 'null'); 
                                bsMsgDetailsForm.setValidation('BS_PayersChQ', 'null'); 
                            }
                        }
                        
                        bsMsgDetailsForm.attachEvent("onValidateError", function (name, value, result){
                           bsMsgDetailsForm.setValidateCss(name, false, 'validate_red');
                           return false;
                        });
                        
                        var messageValidate = bsMsgDetailsForm.validate();
                        
                        if(bsMsgDetailsForm.getItemValue("IT_Flag") == "del_item"){
                                 bsMsgDetailsForm.setValidateCss('IT_Id', false);   
                                 dhtmlx.message({text : 'Selected Item is deleted.Please Verify'});                                 
                                 return false;
                            }
                            
                        if(bsMsgDetailsForm.getItemValue("BS_PettyCashRefId") != 0){
                            if (Number(bsMsgDetailsForm.getItemValue("BS_Amount")) > Number(bsMsgDetailsForm.getItemValue("PettyCashAmount"))){
                                dhtmlx.message({text : ' Amount exceeds petty cash Amount. Please Verify'});
                                bsMsgDetailsForm.setValidateCss('BS_Amount', false);
                                return false;
                            }
                        }
                        
                        if (messageValidate && CstmValidate) { 
                            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);
                            var params="bsId="+BS_Id+"&TR_Track="+BS_TRCombo.getSelectedText();
                            
                            bsMsgDetailsForm.setItemValue("TR_Track",BS_TRCombo.getComboText());
                            
                            bsMsgDetailsForm.send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&'+params) , function(loader, response) {
                                preTally.Settings.progressOff(true, bsMsgDetailsWin, null);
                                if(response != 1 && response != 2  && response != 'pettycash') {
                                    bsMsgDetailsForm.clear();
                                    dhxBSMsgDetails.window("bsDetailsWinMsg_"+ BS_Id).close();
                                    var filterValue = new Array($('#ie_ECF').val(), $('#it_ECF').val(), $('#ds_ECF').val(),$('#tr_ECF').val(), $('#amt_ECF').val(), $('#br_ECF').val(),$('#us_ECF').val());            
                                    expCntrlFilterParams = '&f='+expCntrlToolbar.getValue("rpt_date_from")+'&t='+expCntrlToolbar.getValue("rpt_date_till");

                                    dhxExpCntrlGrid.clearAll();
                                    preTally.Settings.progressOn(true, dhxLayout, null);            
                                    dhxExpCntrlGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/expCntrlRpt.php&filter="+filterValue+"&"+expCntrlFilterParams), function() {
                                        $('.expCntrl_cnt_tot').html("# : "+dhxExpCntrlGrid.getUserData("", "TL_Count")+" ");
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                    });
                                }else if (response == 'pettycash'){
                                    bsMsgDetailsForm.setValidateCss('BS_Amount', false);
                                    response = 'Petty Cash Amount is incorrect. Please Verify.';
                                }else if (response == '2'){
                                    response = 'Edit is restricted for this entry.';
                                } else if (response == '1'){
                                    response = 'Invalid Track.Please Select/Swap Track and Re-Try.';
//                                    dhtmlx.message({text:response });
                                    bsMsgDetailsForm.setValidateCss('TR_Id', false,  'validate_red');                                    
                                }else {
                                    response = 'Invalid Entry. Please Verify.';
                                }
                                dhtmlx.message({text:response });
                            });
                        }
                    } else {
                        dhxBSMsgDetails.window("bsDetailsWinMsg_"+ BS_Id).close();
                    }
                });
            });
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        }
        
};
})(jQuery, this);