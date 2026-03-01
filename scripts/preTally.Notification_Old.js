;(function ($, window, undefined) {
    preTally.Notification = {

        menuNotification: function(inp) {
            
            if (preTallyNotfPop.isVisible()) {
                preTallyNotfPop.hide();
            } else {
                /*$.ajax({
                    url: "requisites/notifications.php"
                }).done(function(data) {
                    preTallyNotfPop.attachList("name,price", JSON.parse(data));
                });*/
                
                preTallyNotfPop_X = $('#'+inp.idPrefix+'notifications').position().left;
                preTallyNotfPop.attachList("item,count", notfData);
                preTallyNotfPop.show(preTallyNotfPop_X, 0, 100, 30);
                
            }    
        },
        countNotification : function(){
            
            var notfInterval;
            var pageGenID = 0;
            
            var notfPoll = function(){
                //$.post('requisites/?'+encrypt('cn'),                 
                $.post('requisites/countNotification.php', 
                { lastupdate: 1 }, 
                processUpdate, 'html');
            }
            
            var processUpdate = function( response,status ) {
                
                //refreshPrd = 3000;                
                var notfResponse = $.parseJSON( response );
                if ( pageGenID != response ) {   
                    
                    dhxToolbar.setItemImage("notifications", "notification/"+notfResponse[2]+".png");
                    
                    notfData = [{
                        id      : "NI",
                        item    : "New Items",
                        count   : '<b style="color:#EC1D25;">'+notfResponse[0]+'</b>'
                    }, preTallyNotfPop.separator, {
                        id      : "ND",
                        item    : "New Description",
                        count   : '<b style="color:#EC1D25;">'+notfResponse[1]+'</b>'
                    }];
               
                    pageGenID = response;
                    if (preTallyNotfPop.isVisible()) {
                        preTallyNotfPop.attachList("item,count", notfData);
                        preTallyNotfPop.show(preTallyNotfPop_X, 0, 100, 30);
                    }  
                    if (dhxMiddleBlockTabs.cells("menuNotfItem")) {
                        listItemNoftGrid.updateFromXML("requisites/listItemDescNotifications.php", true, true, function() {
                            preTally.Notification.NoftItem();       
                        });
                    }
                    if (dhxMiddleBlockTabs.cells("menuNotfDesc")) {
                        listDescNoftGrid.updateFromXML("requisites/listNotfDesc.php", true, true, function() {
                            preTally.Notification.NoftDesc();       
                        });
                    }
                }
                //$('title').text(ptTitle + ' ('+response+')');
            }
            notfPoll();
            notfInterval = setInterval( function() { notfPoll();  }, 60000); 

            setIdleTimeout(120000); // 2 Minute
            setAwayTimeout(300000); // 5 Minute
            document.onIdle = function() {
                clearInterval(notfInterval);
                notfInterval = setInterval( function() { notfPoll();  }, 180000); // Delay 3 Minute
                //console.log('idle');
            }
            document.onAway = function() {
                clearInterval(notfInterval);
                notfInterval = setInterval( function() { notfPoll();  }, 360000); // Delay 6 Minute
                //console.log('away');
            }
            document.onBack = function(isIdle, isAway) {
                clearInterval(notfInterval);
                notfInterval = setInterval( function() { notfPoll();  }, 60000);
                //console.log('back');
            }
        
        },
        viewNotification : function(id){
                if(id=='NI'){
                    if (!dhxMiddleBlockTabs.cells("menuNotfItem")) {
                        dhxMiddleBlockTabs.addTab("menuNotfItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Item Notifications", 200);
                        dhxMiddleBlockTabs.tabs("menuNotfItem").setActive();

//                        listItemNoftGrid = dhxMiddleBlockTabs.cells("menuNotfItem").attachGrid();
//                        listItemNoftGrid.init();
//                        listItemNoftGrid.loadXML("requisites/listNotfItem.php", function() {
//                                preTally.Notification.NoftItem();
//                        });

                        preTally.Settings.progressOn(true,dhxLayout, null);
                        listItemNoftGrid = dhxMiddleBlockTabs.cells("menuNotfItem").attachGrid();
                        listItemNoftGrid.enableValidation(true);
                        listItemNoftGrid.init();
                        listItemNoftGrid.enableEditEvents(true,true,true);
                        listItemNoftGrid.enableTooltips("false,false,false,false,false,false,false,false,false");

                        listItemNoftGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php"), function() {
                            preTally.Settings.progressOff(true,dhxLayout, null);
                           
                            listItemNoftGrid.forEachRow(function(id) { 
                                    listItemNoftGrid.setUserData(id,"SH_Id",listItemNoftGrid.getUserData(id, "SH_Id_Old"));
                                    listItemNoftGrid.setUserData(id,"MH_Id",listItemNoftGrid.getUserData(id, "MH_Id_Old"));
                            });
                            
//                            var entryTypeCombo=listItemNoftGrid.getColumnCombo(1); 
//                            entryTypeCombo.addOption([
//                               ["1","Income"],
//                               ["2","Expense"],
//                            ]);
                            
//                            var ITColCombo=listItemNoftGrid.getColumnCombo(2); 
//                            var DSColCombo=listItemNoftGrid.getColumnCombo(3); 
//                            var SHColCombo=listItemNoftGrid.getColumnCombo(4); 
//                            var MHColCombo=listItemNoftGrid.getColumnCombo(5); 
                            
                            //var ind=listItemNoftGrid.getSelectedCellIndex();alert(ind);

                            listItemNoftGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                                if(stage == 0) {
                                    if(cInd == 1){
                                        var entryTypeCombo  = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        var optnCnt = entryTypeCombo.getOptionsCount();
                                        if(optnCnt == 0) {
                                            entryTypeCombo.addOption([
                                                ["1","Income"],
                                                ["2","Expense"],
                                            ]);
                                        }
                                        entryTypeCombo.attachEvent("onChange", function(loader, response) {
                                            listItemNoftGrid.setUserData(rId,"MH_Type",entryTypeCombo.getSelectedValue());
                                        });
                                    }else if(cInd == 2){
                                        var MHComboType = listItemNoftGrid.getUserData(rId, "MH_Type");
                                        var ITColCombo  = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        var params = "type=" + MHComboType;
                                        ITColCombo.clearAll();
                                        ITColCombo.enableFilteringMode(true,"requisites/ItemsCombo.php?"+encrypt(params));
                                        ITColCombo.setOptionWidth(310);
                                        
                                        ITColCombo.attachEvent("onChange", function(loader, response) {
                                            listItemNoftGrid.setUserData(rId,"IT_Id",ITColCombo.getSelectedValue());
                                        });
                                    }else if(cInd == 3){
                                        var ITComboType  = listItemNoftGrid.getUserData(rId, "IT_Id");//listItemNoftGrid.cellById(rId, 2).getAttribute("id");
                                        var DSColCombo   = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        var params = "IT_Id=" +ITComboType+"&filter=1";
                                        DSColCombo.enableFilteringMode(true,"requisites/descriptions.php?"+encrypt(params));
                                        DSColCombo.setOptionWidth(310);
                                    }else if(cInd == 4){
                                        var MHComboType  = listItemNoftGrid.getUserData(rId, "MH_Type"); // listItemNoftGrid.cellById(rId, 1).getAttribute("id");
                                        var SHColCombo   = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        SHColCombo.clearAll();
                                        var params = "type=" +MHComboType;
                                        SHColCombo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
                                        SHColCombo.setOptionWidth(310);
                                    }else if(cInd == 5){
//                                    var MHComboType  = listItemNoftGrid.cellById(rId, 1).getAttribute("id");
                                        var MHComboType = listItemNoftGrid.getUserData(rId, "MH_Type");
                                        var MHColCombo   = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        MHColCombo.clearAll();
                                        MHColCombo.readonly(true);
                                        var params = "MH_Type=" +MHComboType;
                                        MHColCombo.load("requisites/mainheads.php?"+encrypt(params));
//                                        MHColCombo.openSelect();
                                    }
                                } else if(stage == 2){
                                    if(oValue != nValue) listItemNoftGrid.cells(rId,8).setValue(1);
                                }
                                return true;
                            });
                            
                        });
                      
                        
   
                            
                        notfButtonBar = dhxMiddleBlockTabs.tabs("menuNotfItem").attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.Notification.notificationSave();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                        dhxMiddleBlockTabs.cells("menuNotfItem").showStatusBar();
                       
                        dpNotificationSave = new dataProcessor("warehouse/notificationSave.php");
                        
                        dpNotificationSave.setTransactionMode("POST",true);
                        dpNotificationSave.setUpdateMode("off");
                        dpNotificationSave.enableDataNames(true);
//                        dpNotificationSave.enablePartialDataSend(true);
                        dpNotificationSave.init(listItemNoftGrid);
	
                        listItemNoftGrid.attachEvent("onCheckbox", function(rId,cInd,state){
                            if(state == false) {
                                listItemNoftGrid.setRowTextStyle(rId,false);
                                dpNotificationSave.setUpdated(rId,false);
                            }
                        });

          
                    } else {
                    dhxMiddleBlockTabs.tabs("menuNotfItem").setActive();
                    }
                }
                if(id=='ND'){ 
                    if (!dhxMiddleBlockTabs.cells("menuNotfDesc")) {
                        dhxMiddleBlockTabs.addTab("menuNotfDesc", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Description Notifications", 210);
                        dhxMiddleBlockTabs.tabs("menuNotfDesc").setActive();

                        listDescNoftGrid = dhxMiddleBlockTabs.cells("menuNotfDesc").attachGrid();
                        listDescNoftGrid.init();
                        listDescNoftGrid.loadXML(preTally.Initialize.encryptURL("requisites/listNotfDesc.php"), function() {
                                preTally.Notification.NoftDesc();
                        });
                    } else {
                    dhxMiddleBlockTabs.tabs("menuNotfDesc").setActive();
                    }
                }
           
        },
        notificationSave: function() {
                           
//            dpNotificationSave.sendData();
//            
//            dpNotificationSave.attachEvent("onAfterUpdateFinish", function() {
////                console.log(dpNotificationSave.getSyncState());
//                dhtmlx.message({text: 'Accounts Entry Item Details Updated Successfully', id: 'Msg'});
//
////                if (dhxSubGridBalSheet[rId].getRowsNum() == 0) {
////                    var main_rwID = 1;
////                    dhxGridBalSheet.deleteRow(rId);
////                    dhxGridBalSheet.forEachRow(function(id) {
////                        dhxGridBalSheet.cells(id, 1).setValue(main_rwID);
////                        main_rwID++;
////                    });
////                } else {
////                    var BSItemCount = (dhxGridBalSheet.cells(rId, 3).getValue() - 1);
////                    dhxGridBalSheet.cells(rId, 3).setValue(BSItemCount);
////
////                    var sub_rwID = 1;
////                    dhxSubGridBalSheet[rId].forEachRow(function(id) {
////                        dhxSubGridBalSheet[rId].cells(id, 1).setValue(sub_rwID);
////                        sub_rwID++;
////                    });
////                }
//            });
            
            
            var itemObject = {};
            var error = 0;
            listItemNoftGrid.forEachRow(function(rId){
                if(listItemNoftGrid.cells(rId,8).getValue() == 1) {
                    var itemDetails = {};                  
                    itemDetails['ie'] = listItemNoftGrid.cells(rId,1).getValue();
                    itemDetails['it'] = listItemNoftGrid.cells(rId,2).getValue();
                    itemDetails['de'] = listItemNoftGrid.cells(rId,3).getValue();
                    itemDetails['sh'] = listItemNoftGrid.cells(rId,4).getValue();
                    itemDetails['mh'] = listItemNoftGrid.cells(rId,5).getValue();
                    
                    if(!itemDetails['ie'] || !itemDetails['it'] || !itemDetails['de'] || !itemDetails['sh'] || !itemDetails['mh']) {
                        dhtmlx.message({text: '<b style="color:#FF0000;">Item ' + listItemNoftGrid.cells(rId,0).getValue() + ' is Error. Please Verify.</b>'});
                        error = 1;
                    }
                    itemObject[rId] = itemDetails;
                }
            });
            if(error == 0) {
                console.log(itemObject);
            }
                                                  
        },
//        saveDescription : function() { 
//            $('.descriptionSave').click(function(e) {
//                var DS_Id = $(this).attr('DSId');//alert("DS_Id"+DS_Id);
//                var rowId = $(this).attr('rID');
//                var DS_Description = listDescNoftGrid.getUserData(rowId, "DS_Description");//alert(DS_Description);
//                var IT_Id = listDescNoftGrid.getUserData(rowId, "IT_Id");
//                var desc_rwID = 1;
//                
//                dhtmlx.confirm({
//                        title: "Approve Description "+DS_Description,
//                        ok: "Yes", cancel: "No",
//                        text: "Are you sure you want Approve it?",
//                        callback: function(result) {
//                            if (result == true) {
//                                    $.post(
//                                        'warehouse/newDescription.php',
//                                        { DS_Description: DS_Description, DS_Id: DS_Id, DS_Status: 1 , IT_Id : IT_Id },
//                                        function(responseText) {
//                                                listDescNoftGrid.deleteRow(rowId);
//                                                dhtmlx.message({text: responseText });
//                                                listDescNoftGrid.forEachRow(function(id) { 
//                                                        listDescNoftGrid.cells(id, 0).setValue(desc_rwID);
//                                                        desc_rwID++;
//                                                        preTally.Notification.countNotification();
//                                                });
//
//                                        },
//                                        "html"
//                                    );
//                            }
//                        }
//                });
//            });
//        },
        NoftItem : function(){
             $('.itemSave').click(function(e) {
                 var IT_Id = $(this).attr('ITId');
                 var rowId = $(this).attr('rID');
                 var itm_rwID=1;
                if (dhxMiddleBlockTabs.cells("menuNewItem")) {
                    addItemForm.resetValidateCss();
                    addItemForm.clear();
                }
//                preTally.BalanceSheet.menuNewItem(rowId);
                preTally.Item.menuNewCompanyItem(IT_Id,'Notf');
             });
        },
        NoftDesc : function(){
             $('.descriptionSave').click(function(e) {
                var DS_Id = $(this).attr('DSId');
                var rowId = $(this).attr('rID');
                if (dhxMiddleBlockTabs.cells("menuNewDescription")) {
                    addDescriptionForm.resetValidateCss();
                    addDescriptionForm.clear();
                }
                 preTally.BalanceSheet.menuNewDescription(rowId);
             });
        },
        approveItem : function(inp,IT_Id,IT_Name,rID){
            dhtmlx.confirm({
                title: "Approve Item "+IT_Name,
                //type:"confirm-warning",
                text: "Are you sure you want Approve it?",
                callback: function(response) {
                    if(response){
                        $.post(
                            'warehouse/approveItem.php',
                            { IT_Id : IT_Id },
                            function(responseText) {
                                dhtmlx.message({text: responseText });
                                listItemNoftGrid.deleteRow(rID);
                                preTally.Notification.countNotification();
                            }
                        );  
                    }
                }
            });
            
            
        },
        approveDescription : function(inp,DS_Id,DS_Description,rID){ 
            dhtmlx.confirm({
                title: "Approve Description "+DS_Description,
                //type:"confirm-warning",
                text: "Are you sure you want Approve it?",
                callback: function(response) {
                    if(response){
                        $.post(
                            'warehouse/approveDescription.php',
                            { DS_Id : DS_Id },
                            function(responseText) {
                                dhtmlx.message({text: responseText });
                                listDescNoftGrid.deleteRow(rID);
                                preTally.Notification.countNotification();
                            }
                        );  
                    }
                }
            });
            
            
        },
        mapItem : function(inp,IT_Id,IT_Name,rID){
            
            
            if(dhxWins.isWindow('mapItemNotfWindow')) dhxWins.window('mapItemNotfWindow').close();
            
            swpWindowObj=dhxWins.createWindow('mapItemNotfWindow', 400, 100, 410, 300);
            swpWindowObj.setText('Map Item ('+IT_Name+' )');
            swpWindowObj.button("minmax1").hide();
            swpWindowObj.button("minmax2").hide();
            swpWindowObj.button("park").hide();
            
            swpWindowForm = swpWindowObj.attachForm();
            var params = "IT_Id="+IT_Id+"&IT_Name="+IT_Name;
            swpWindowForm.attachEvent("onXLS",function(){
              
                preTally.Settings.progressOn(true,swpWindowObj,false);
            });
            swpWindowForm.attachEvent("onXLE",function(){
              
               preTally.Settings.progressOff(true,swpWindowObj,false);
               
            });
            swpWindowForm.loadStruct("requisites/map_itemNotf.php?"+encrypt(params), function() {     

                    ItmCombo = swpWindowForm.getCombo("IT_MapId");
                    ItmCombo.setPlaceholder('Please Select Item To Swap');
                    
                    var MhTypCombo = swpWindowForm.getCombo("MH_Type");
                    var SH_Combo = swpWindowForm.getCombo("SH_Id");
                    swpWindowForm.setItemValue("MH_Type", listItemNoftGrid.getUserData(IT_Id, "MH_Type"));
                    preTally.Settings.comboSelectPreload(SH_Combo,"subheadcombo");
                    preTally.Settings.comboSelectPreload(ItmCombo,"itmapcombo");
                    var params="type=" + listItemNoftGrid.getUserData(IT_Id, "MH_Type");
                    SH_Combo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
//                    SH_Combo.load("requisites/subheads.php?"+encrypt(params), function() { });

//                    MhTypCombo.attachEvent("onChange", function(loader, response) { 
//                        if (loader == 'MH_Type') {
//                            SH_Combo.clearAll();
//                            SH_Combo.setComboText("");
//                            SH_Combo.setComboValue("");
//                            var params="type="+response;
//                            SH_Combo.load("requisites/subheads.php?"+encrypt(params));
//                        }
//
//                    });
                
                    MhTypCombo.attachEvent("onClose", function() { 
                        SH_Combo.clearAll();
                        SH_Combo.setComboText('');
                        SH_Combo.setComboValue('');
                        ItmCombo.clearAll();
                        ItmCombo.setComboText("");
                        ItmCombo.setComboValue("");
                        var MH_Id = MhTypCombo.getSelectedValue();
                        if ((MH_Id) && (MH_Id != 0)) {
                            var params="type=" + MH_Id ;
                            SH_Combo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
                        }
                    });
                    SH_Combo.attachEvent("onChange", function() {
                        ItmCombo.clearAll();
                        ItmCombo.setComboText("");
                        ItmCombo.setComboValue("");
                        var SH_Id = SH_Combo.getSelectedValue();
                        if ((SH_Id) && (SH_Id != 0)) {
                            params="SH_Id=" + SH_Id+"&filter=1";
                            ItmCombo.load("requisites/items.php?"+encrypt(params), function() {
                            });
                        }
                    });
                    
                        
//                    ItmCombo.attachEvent("onFocus",function(event){ 
//                        if(ItmCombo.getSelectedText("IT_MapId") == 'Please Select Item To Swap'){
//                            ItmCombo.setComboText('');
//                        }else ItmCombo.setComboText('Please Select Item To Swap');
//
//                    });
                      swpWindowForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'mapItemValidate') {
                            var mapItmVal = swpWindowForm.validate();
                            if (mapItmVal) {
                                preTally.Settings.progressOn(true,dhxLayout, null);
                                swpWindowForm.send('warehouse/map_itemNotf.php', function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        var IT_rwID=1;
                                        dhxWins.window('mapItemNotfWindow').close();
                                        listItemNoftGrid.deleteRow(IT_Id);
                                        
                                        listItemNoftGrid.forEachRow(function(id) { 
                                                listItemNoftGrid.cells(id, 0).setValue(IT_rwID);
                                                IT_rwID++;
                                        });
                                    } else {
                                        response = 'Please Select Item To Swap.Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    
                                });
                            }
                        } else { 
                           // ItmCombo = swpWindowForm.getCombo("IT_MapId");
                            SH_Combo.setComboText('');
                            SH_Combo.setComboValue('');
                            ItmCombo.setComboText('');
                            ItmCombo.setComboValue('');
                            //ItmCombo.clearAll();
                            swpWindowForm.setItemValue("IT_Id", IT_Id);
                        }
                    });
                
            });

       },
       mapDesc : function(inp,DS_Id,DS_Description,rID,IT_Id){

            if(dhxWins.isWindow('mapDescNotfWindow')) dhxWins.window('mapDescNotfWindow').close();
            
            swpWindowObj=dhxWins.createWindow('mapDescNotfWindow', 400, 100, 410, 240);
            swpWindowObj.setText('Map Item ('+DS_Description+' )');
            swpWindowObj.button("minmax1").hide();
            swpWindowObj.button("minmax2").hide();
            swpWindowObj.button("park").hide();

            swpWindowForm = swpWindowObj.attachForm();
            var params = "DS_Id="+DS_Id+"&DS_Name="+DS_Description
            swpWindowForm.attachEvent("onXLS",function(){
              
                preTally.Settings.progressOn(true,swpWindowObj,false);
            });
            swpWindowForm.attachEvent("onXLE",function(){
              
               preTally.Settings.progressOff(true,swpWindowObj,false);
               
            });
            swpWindowForm.loadStruct("requisites/map_descNotf.php?"+encrypt(params), function() {
                
                    DescCombo = swpWindowForm.getCombo("DS_MapId");
                    var params ="IT_Id="+IT_Id+"&filter=1";
                    DescCombo.enableFilteringMode(true,"requisites/descriptions.php?"+encrypt(params), true);
                    DescCombo.setPlaceholder('Please Select Description To Swap');
//                    DescCombo.attachEvent("onFocus",function(event){ 
//                        if(DescCombo.getSelectedText("DS_MapId") == 'Please Select Description To Swap'){
//                            DescCombo.setComboText('');
//                        }else DescCombo.setPlaceholder('Please Select Description To Swap');
//
//                    });
                    
                    swpWindowForm.attachEvent("onButtonClick", function(name) {
                        if (name == 'mapDescValidate') {
                            var mapDescVal = swpWindowForm.validate();
                            if (mapDescVal) {
                                var DS_rwID=1;
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                swpWindowForm.send('warehouse/map_descNotf.php', function(loader, response) {
                                    preTally.Settings.progressOff(true,dhxLayout, null);
                                    if (response != 'fail') {
                                        dhxWins.window('mapDescNotfWindow').close();
                                        listDescNoftGrid.deleteRow(rID);
                                        
                                        listDescNoftGrid.forEachRow(function(id) { 
                                                listDescNoftGrid.cells(id, 0).setValue(DS_rwID);
                                                DS_rwID++;
                                        });
                                        
                                    } else {
                                        response = 'Please Select Description To Swap. Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    
                                });
                            }
                        } else {
                            //DescCombo = swpWindowForm.getCombo("DS_MapId");
                            DescCombo.setComboText('');
                            DescCombo.setComboValue('');
                            DescCombo.clearAll();
                            swpWindowForm.setItemValue("DS_Id", DS_Id);
                        }
                    });
            });
        },
        aliasNotfItem: function(ITId) { 
            //alert('alias');
            //console.log(mapItemCustomSubGrid.getUserData(ITId, "IT_Name"));
            dhtmlx.confirm({
                title: "Add Item To PreTally Item Pool",
                ok: "Yes", cancel: "No",
                text: "Confirm Add <b>"+listItemNoftGrid.getUserData(ITId, "IT_Name")+"</b> To PreTally Item Pool",
                callback: function(result) {
                    if (result == true){
                        var itemResponseMap = dhx4.ajax.postSync('warehouse/mapItemPreTally.php?item='+ITId, encodeURI(1));
                        if(itemResponseMap.xmlDoc.responseText != null) {
                            dhtmlx.message({text: itemResponseMap.xmlDoc.responseText});
                            listItemNoftGrid.clearAll();
                            listItemNoftGrid.loadXML(preTally.Initialize.encryptURL("requisites/listNotfItem.php"), function() {
                                if(mapsubGridItemOpen[1] == true) listItemGrid.cells("1",0).open();
                                if(mapsubGridItemOpen[2] == true) listItemGrid.cells("2",0).open();
                            });
                        }
                    }
                }
            });
        },
        
    };
})(jQuery, this);




	
