;(function($, window, undefined) {
    preTally.Item = {
        menuNewOfzItem: function(id) {
            var ITId = id;
                     
            if (!dhxMiddleBlockTabs.cells("menuNewOfzItem")) {
                dhxMiddleBlockTabs.addTab("menuNewOfzItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Add New Item", 180);
                dhxMiddleBlockTabs.tabs("menuNewOfzItem").setActive();
                             
                menuOfzItemLayout = dhxMiddleBlockTabs.cells("menuNewOfzItem").attachLayout('4C');
                                
                menuOfzItemLayout.cells("a").setText("Add New Item");
                menuOfzItemLayout.cells("a").setWidth(600);
                
                menuOfzItemLayout.cells("b").setText("Add Description");
                menuOfzItemLayout.cells("b").setHeight(200);
                menuOfzItemLayout.cells("b").fixSize(true, true);
                //menuOfzItemLayout.cells("b").hideHeader();
                
                menuOfzItemLayout.cells("c").setText("Add Default Descriptions");
                menuOfzItemLayout.cells("c").setHeight(90);
                menuOfzItemLayout.cells("c").fixSize(true, true);
               
                menuOfzItemLayout.cells("d").setText("List Description");
                
                if(ITId == 0) {
                    menuOfzItemLayout.cells("b").collapse();
                    menuOfzItemLayout.cells("b").hideArrow();
                    menuOfzItemLayout.cells("c").collapse();
                    menuOfzItemLayout.cells("c").hideArrow();
                    menuOfzItemLayout.cells("d").hideArrow();
                    menuOfzItemLayout.cells("b").setCollapsedText("<span style='color:red;'>Add / Select an item to add new Description!</span>");
                    menuOfzItemLayout.cells("c").setCollapsedText("<span style='color:red;'>Add / Select an item to add default Description!</span>");
                }else{
                    menuOfzItemLayout.cells("b").setText("Add Description for Item " + listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                    menuOfzItemLayout.cells("c").setText("Add Default Descriptions for Item " + listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                }
                
                //------------------ Attach Grid for Designation --------------------//
                listItemDescriptionGrid = menuOfzItemLayout.cells("d").attachGrid();
                listItemDescriptionGrid.enableTooltips("false,false,false");
                listItemDescriptionGrid.init();
                
                listItemDescriptionGrid.attachEvent("onXLS", function() {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listItemDescriptionGrid.attachEvent("onXLE", function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                var params  = "IT_Id="+ITId; 
                listItemDescriptionGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), function() {
                    listItemDescriptionGrid.attachEvent("onRowSelect",preTally.Item.editItemDescription);
                });

                
                //------------------ Attach Form for Item ----------------------//
                addOfzItemForm = menuOfzItemLayout.cells("a").attachForm();
                addOfzItemForm.loadStruct(preTally.Initialize.encryptURL("requisites/newOfzItem.php&r=" + new Date().getTime()), function() {
                    
                    preTally.Settings.progressOff(true, dhxLayout, null);
                           
                    SH_OfzCombo  = addOfzItemForm.getCombo("SH_Id");
                    MH_OfzCombo  = addOfzItemForm.getCombo("MH_Type");
                    addOfzItemForm.getInput("IT_Comments").setAttribute("placeholder","Enter Item Details Here for Future Use."); 
                                  
                    if(ITId!=0)
                    {
                        addOfzItemForm.setItemValue("IT_Id", ITId);
                        addOfzItemForm.setItemValue("IT_Name", listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                        addOfzItemForm.setItemValue("IT_Comments",listEditCustomItemGrid.getUserData(ITId, "IT_Comments"));
                        addOfzItemForm.setItemValue("US_Id",listEditCustomItemGrid.getUserData(ITId, "US_Id"));
                        addOfzItemForm.setItemValue("IT_Business",listEditCustomItemGrid.getUserData(ITId, "IT_Business"));
                        addOfzItemForm.setItemValue("IT_PettyCash",listEditCustomItemGrid.getUserData(ITId, "IT_PettyCash"));
                        addOfzItemForm.setItemValue("IT_Transfers",listEditCustomItemGrid.getUserData(ITId, "IT_Transfers"));
                        addOfzItemForm.setItemValue("IT_DualEntry",listEditCustomItemGrid.getUserData(ITId, "IT_DualEntry"));
                        addOfzItemForm.setItemValue("IT_MinAmount",listEditCustomItemGrid.getUserData(ITId, "IT_MinAmount"));
                        addOfzItemForm.setItemValue("IT_MaxAmount",listEditCustomItemGrid.getUserData(ITId, "IT_MaxAmount"));
                        addOfzItemForm.setItemValue("IT_Status",listEditCustomItemGrid.getUserData(ITId, "IT_Status"));
                        addOfzItemForm.setItemValue("IT_OtherUser",listEditCustomItemGrid.getUserData(ITId, "IT_OtherUser")); //28-05-2025
                        SH_OfzCombo.clearAll();
                        addOfzItemForm.setItemValue("MH_Type", listEditCustomItemGrid.getUserData(ITId, "MH_Type"));
                        SHId = listEditCustomItemGrid.getUserData(ITId, "SH_Id");
                        var params="type=" + listEditCustomItemGrid.getUserData(ITId, "MH_Type")+"&SHId="+SHId;
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/subheads.php&"+ params), function(xml) {
                            SH_OfzCombo.load(xml.xmlDoc.responseText);
                        });
                        var params="type=" + listEditCustomItemGrid.getUserData(ITId, "MH_Type");
                        SH_OfzCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
            
                        if(listEditCustomItemGrid.getUserData(ITId, 'IT_DualEntry') == 1){
                            
                            var params = "IT_Id="+listEditCustomItemGrid.getUserData(ITId, 'IT_DualItem');
                            var itemData = {type: "combo", name: "IT_DualItem", label: "Dual Item", required : "true",  value : "",offsetTop:"20", serverFiltering : "requisites/itemsDual.php&"}
                            addOfzItemForm.addItem(null, itemData, 9 );
                            
                            var dualItemCombo = addOfzItemForm.getCombo("IT_DualItem");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/itemsDual.php&"+ params), function(xml) {
                                dualItemCombo.load(xml.xmlDoc.responseText);
                            });
                        
                        }
                    } else {
                        var MhTyp = addOfzItemForm.getItemValue("MH_Type");
                        var params="type=" + MhTyp;
                        SH_OfzCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                    }
                
                    MH_OfzCombo.attachEvent("onChange", function(loader, response) { 
                            var MhTyp = addOfzItemForm.getItemValue("MH_Type");
                            SH_OfzCombo.clearAll();
                            SH_OfzCombo.setComboText("");
                            SH_OfzCombo.setComboValue("");
                            var params="type="+MhTyp;
                            SH_OfzCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                            if(ITId!=0) addOfzItemForm.setItemValue("SH_Id", listEditCustomItemGrid.getUserData(ITId, "SH_Id")); 
                            //SH_OfzCombo.load("requisites/subheads.php?"+encrypt(params));
                    });
                    
                    addOfzItemForm.attachEvent("onChange", function(name, value, state){
                        if(name == 'IT_DualEntry'){
                            if(addOfzItemForm.getItemValue('IT_DualEntry') == 1){
                                
                                var MH_Type = addOfzItemForm.getItemValue('MH_Type');
                                var params = "type="+MH_Type;
                                var itemData = {type: "combo", name: "IT_DualItem", label: "Dual Item", required : "true",  value : "",offsetTop:"20", serverFiltering : "requisites/itemsDual.php&"}
                                addOfzItemForm.addItem(null, itemData, 9 );
                    
                            }else{
                                addOfzItemForm.removeItem("IT_DualItem"); 
                            }
                        }else if(name == 'IT_PettyCash'){
                            if(addOfzItemForm.getItemValue('IT_PettyCash') == 1)
                                addOfzItemForm.setItemValue('IT_Transfers',1);
//                            else
//                                addOfzItemForm.setItemValue('IT_Transfers',0);
                        }
                        else if(name == 'IT_Transfers'){
                            if(addOfzItemForm.getItemValue('IT_Transfers') == 0 && addOfzItemForm.getItemValue('IT_PettyCash') == 1){
                                dhtmlx.message({text: "Petty Cash Item must be an Internal Transfer."});
                                addOfzItemForm.setItemValue('IT_Transfers',1);
                            }
                        }
                    });
                    
                    addOfzItemForm.attachEvent("onButtonClick", function(name) {

                        var values = addOfzItemForm.getFormData();
                      
                        if (name == 'newItemValidate') {
                            var newItem = addOfzItemForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'IT_Name', addOfzItemForm, 'title');
                            
                            var DualItemValidate = addOfzItemForm.getItemValue("IT_DualItem");
                            
                            if (newItem  && CstmValidate && ($.isNumeric(DualItemValidate) || DualItemValidate == null )) {
                                                               
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addOfzItemForm.send(preTally.Initialize.encryptURL('warehouse/newOfzItem.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null); 
                                   
                                    if (response != 'fail' && response != 'Invalid' && response!="flag") {
                                        
                                        if($.isNumeric(response)) response = "Item Created Successfully";
                                        addOfzItemForm.resetValidateCss();
                                        addOfzItemForm.clear();
                                        addOfzItemForm.setItemValue("IT_Id", 0);
                                        SH_OfzCombo.clearAll();
                                        SH_OfzCombo.setComboText("");
                                        SH_OfzCombo.setComboValue("");
                                        
                                        if(addOfzItemForm.isItem("IT_DualItem"))addOfzItemForm.removeItem("IT_DualItem");
                                        
                                        menuOfzItemLayout.cells("b").collapse();
                                        menuOfzItemLayout.cells("b").hideArrow();
                                        menuOfzItemLayout.cells("c").collapse();
                                        menuOfzItemLayout.cells("c").hideArrow();
                                        menuOfzItemLayout.cells("d").hideArrow();
                                        menuOfzItemLayout.cells("b").setCollapsedText("<span style='color:red;'>Add / Select an item to add new Description!</span>");
                                        menuOfzItemLayout.cells("c").setCollapsedText("<span style='color:red;'>Add / Select an item to add default Description!</span>");
                                     
                                        var params  = "IT_Id= ";
                                        listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {
                                            //preTally.BalanceSheet.editDescription();
                                        });
                                        
                                    } else if(response == 'Invalid') {
                                        response = 'Invalid Subhead. Please Re-Try';
                                    } else if(response =="flag") {
                                        response = "Item can't be both Transaction and Bussiness.";
                                    } else {
                                        response = 'Item Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                });
                            } else if(DualItemValidate!= null) {
                                addOfzItemForm.setValidateCss("IT_DualItem", false );
                                dhtmlx.message({text: "Invalid Dual Item. Please Re-Try"});
                            }
                        } else if(name == 'newItemProceed'){
                            var newItem = addOfzItemForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'IT_Name', addOfzItemForm, 'title');
                            if (newItem  && CstmValidate) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addOfzItemForm.send(preTally.Initialize.encryptURL('warehouse/newOfzItem.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null); 
                                    if ( $.isNumeric(response)  && response!="flag" ) {
                                        ITId = response;
                                       
                                        var name = addOfzItemForm.getItemValue("IT_Name");
                                        addItemDescriptionForm.setItemValue("IT_Id", ITId);
                                        addDefaultItemDescriptionForm.setItemValue("IT_Id", ITId);
                                        addOfzItemForm.setItemValue("IT_Id", ITId);
                                        
                                        menuOfzItemLayout.cells("b").expand();
                                        menuOfzItemLayout.cells("b").showArrow();
                                        menuOfzItemLayout.cells("c").expand();
                                        menuOfzItemLayout.cells("c").showArrow();
                                        menuOfzItemLayout.cells("d").showArrow();
                                        menuOfzItemLayout.cells("b").setText("Add Description for Item " +name);
                                        menuOfzItemLayout.cells("c").setText("Add Default Descriptions for Item " +name);
                                        
                                        response = 'Item Created Successfully.';
                                       
                                    }else if(response == 'Invalid') {
                                        response = 'Invalid Subhead. Please Re-Try';
                                    }else if(response =="flag") {
                                        response = "Item can't be both Transaction and Bussiness.";
                                    }else if(response == 'fail') {
                                        response = 'Item Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                });
                            }
                        } else { 
                                addOfzItemForm.resetValidateCss();
                                addOfzItemForm.clear();
                                addOfzItemForm.setItemValue("IT_Id", 0);
                                SH_OfzCombo.clearAll();
                                SH_OfzCombo.setComboText("");
                                SH_OfzCombo.setComboValue("");
                                
                                addItemDescriptionForm.resetValidateCss();
                                addItemDescriptionForm.clear();
                                addItemDescriptionForm.setItemValue("DS_Id", 0);
                            
                                menuOfzItemLayout.cells("b").collapse();
                                menuOfzItemLayout.cells("b").hideArrow();
                                menuOfzItemLayout.cells("c").collapse();
                                menuOfzItemLayout.cells("c").hideArrow();
                                menuOfzItemLayout.cells("d").hideArrow();
                                menuOfzItemLayout.cells("b").setCollapsedText("<span style='color:red;'>Add / Select an item to add new Description!</span>");
                                menuOfzItemLayout.cells("c").setCollapsedText("<span style='color:red;'>Add / Select an item to add default Description!</span>");

                                var params  = "IT_Id= ";
                                listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+params), true, true, function() {
                                    //preTally.BalanceSheet.editDescription();
                                });
                        }
                    });

                });
                
                
                //----------------- Attach Form for Description --------------------//
                addItemDescriptionForm = menuOfzItemLayout.cells("b").attachForm();
                addItemDescriptionForm.loadStruct(preTally.Initialize.encryptURL("requisites/newItemDescription.php&r=" + new Date().getTime()), function() {
                    
                    if(ITId != 0) addItemDescriptionForm.setItemValue("IT_Id", ITId);
                    addItemDescriptionForm.attachEvent("onButtonClick", function(name) { 
                        ITId = addItemDescriptionForm.getItemValue("IT_Id");
                        if (name == 'newItemDescriptionValidate') {
                            var newItemDescription = addItemDescriptionForm.validate();
                            if (newItemDescription) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addItemDescriptionForm.send(preTally.Initialize.encryptURL('warehouse/newItemDescription.php'), function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {  
                                        addItemDescriptionForm.resetValidateCss();
                                        addItemDescriptionForm.clear();
                                        addItemDescriptionForm.setItemValue("DS_Id", 0);
                                        addItemDescriptionForm.setItemValue("IT_Id", ITId);
                                    } else { 
                                        response = 'Description Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    var params  = "IT_Id="+addItemDescriptionForm.getItemValue("IT_Id"); 
                                    listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {
                                        //preTally.BalanceSheet.editDescription();
                                    });
                                });
                            }
                        }
                        else { 
                            addItemDescriptionForm.resetValidateCss();
                            addItemDescriptionForm.clear();
                            addItemDescriptionForm.setItemValue("DS_Id", 0);
                        }
                    });
                  
                });
                
                
                 //----------------- Attach Form for Description --------------------//
                addDefaultItemDescriptionForm = menuOfzItemLayout.cells("c").attachForm();
                addDefaultItemDescriptionForm.loadStruct(preTally.Initialize.encryptURL("requisites/newDefaultDescription.php&r=" + new Date().getTime()), function() {
                    
                    if(ITId != 0) addDefaultItemDescriptionForm.setItemValue("IT_Id", ITId);
                    addDefaultItemDescriptionForm.attachEvent("onButtonClick", function(name) { 
//                        ITId = addDefaultItemDescriptionForm.getItemValue("IT_Id");
                        if (name == 'newLCDescription') {

                            dhxLCDecsription = new dhtmlXWindows();
                            LCDSWin = dhxLCDecsription.createWindow("addBranchWin",100, 100 , 600, 400);
                            LCDSWin.button("minmax1").hide();
                            LCDSWin.button("minmax2").hide();
                            LCDSWin.button("park").hide();
                            LCDSWin.center();
                            LCDSWin.setModal(true);
                            LCDSWin.setText("Add Branches as Default Descriptions");
                            listBranchesGrid = LCDSWin.attachGrid();
                            preTally.Settings.progressOn(true,LCDSWin,null);
                            
                            listBranchesGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBranches.php"),function(){
                                preTally.Settings.progressOff(true,LCDSWin,null);
                                listBranchesGrid.enableTooltips("false,false,false");
                            });
                            
                            LCDSWin.attachStatusBar({
                                text   : "<input type='button' value='SAVE' onclick='preTally.Item.addBranchesDesc();'  style='margin:5px 10px 5px 0; float:right;' ",
                                height : 35
                            });
                            
                        }else if (name == 'newBNKDescription') {

                            dhxBNKDecsription = new dhtmlXWindows();
                            BNKDSWin = dhxBNKDecsription.createWindow("addBanksWin",100, 100 , 600, 400);
                            BNKDSWin.button("minmax1").hide();
                            BNKDSWin.button("minmax2").hide();
                            BNKDSWin.button("park").hide();
                            BNKDSWin.center();
                            BNKDSWin.setModal(true);
                            BNKDSWin.setText("Add Banks as Default Descriptions");
                            listBanksGrid = BNKDSWin.attachGrid();
                            preTally.Settings.progressOn(true,BNKDSWin,null);
                            
                            listBanksGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBankDispNames.php"),function(){
                                preTally.Settings.progressOff(true,BNKDSWin,null);
                                 listBanksGrid.enableTooltips("false,false,false");
                            });
                            
                            BNKDSWin.attachStatusBar({
                                text   : "<input type='button' value='SAVE' onclick='preTally.Item.addBanksDesc();'  style='margin:5px 10px 5px 0; float:right;' ",
                                height : 35
                            });
                            
                        }else if (name == 'newUSDescription') {

                            dhxUSDecsription = new dhtmlXWindows();
                            USDSWin = dhxUSDecsription.createWindow("addUsersWin",100, 100 , 800, 400);
                            USDSWin.button("minmax1").hide();
                            USDSWin.button("minmax2").hide();
                            USDSWin.button("park").hide();
                            USDSWin.center();
                            USDSWin.setModal(true);
                            USDSWin.setText("Add Users as Default Descriptions");
                            listUsersGrid = USDSWin.attachGrid();
                            preTally.Settings.progressOn(true,USDSWin,null);
                            
                            listUsersGrid.loadXML(preTally.Initialize.encryptURL("requisites/listRecipients.php"),function(){
                                preTally.Settings.progressOff(true,USDSWin,null);
                                listUsersGrid.enableTooltips("false,false,false,false,false,false");
                            });
                            
                            USDSWin.attachStatusBar({
                                text   : "<input type='button' value='SAVE' onclick='preTally.Item.addUsersDesc();'  style='margin:5px 10px 5px 0; float:right;' ",
                                height : 35
                            });
                            
                        } else { 
                            alert("Welcome To Pretally");
                        }
                    });
                  
                });
                
            } else { 
                preTally.Settings.progressOn(true, dhxLayout, null);    
                if(ITId!=0) {   
                    
                    addDefaultItemDescriptionForm.setItemValue("IT_Id", ITId);
                    
                    addOfzItemForm.setItemValue("IT_Id", ITId);
                    addOfzItemForm.setItemValue("US_Id",listEditCustomItemGrid.getUserData(ITId, "US_Id"));
                    addOfzItemForm.setItemValue("IT_Name", listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                    addOfzItemForm.setItemValue("MH_Type", listEditCustomItemGrid.getUserData(ITId, "MH_Type"));
                    addOfzItemForm.setItemValue("IT_Comments", listEditCustomItemGrid.getUserData(ITId, "IT_Comments"));
                    addOfzItemForm.setItemValue("IT_Business",listEditCustomItemGrid.getUserData(ITId, "IT_Business"));
                    addOfzItemForm.setItemValue("IT_PettyCash",listEditCustomItemGrid.getUserData(ITId, "IT_PettyCash"));
                    addOfzItemForm.setItemValue("IT_Transfers",listEditCustomItemGrid.getUserData(ITId, "IT_Transfers"));
                    addOfzItemForm.setItemValue("IT_DualEntry",listEditCustomItemGrid.getUserData(ITId, "IT_DualEntry"));
                    addOfzItemForm.setItemValue("IT_MinAmount",listEditCustomItemGrid.getUserData(ITId, "IT_MinAmount"));
                    addOfzItemForm.setItemValue("IT_MaxAmount",listEditCustomItemGrid.getUserData(ITId, "IT_MaxAmount"));
                    addOfzItemForm.setItemValue("IT_Status",listEditCustomItemGrid.getUserData(ITId, "IT_Status"));
                    addOfzItemForm.setItemValue("IT_OtherUser",listEditCustomItemGrid.getUserData(ITId, "IT_OtherUser")); //28-05-2025
                    SH_OfzCombo.clearAll();
                    SH_OfzCombo.setComboText("");
                    SH_OfzCombo.setComboValue("");
                    SHId = listEditCustomItemGrid.getUserData(ITId, "SH_Id");
                    var params="type=" + listEditCustomItemGrid.getUserData(ITId, "MH_Type")+"&SHId="+SHId;
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/subheads.php&"+ params), function(xml) {
                        SH_OfzCombo.load(xml.xmlDoc.responseText);
                    });
                    
                    if(addOfzItemForm.isItem("IT_DualItem"))addOfzItemForm.removeItem("IT_DualItem");
                    if(listEditCustomItemGrid.getUserData(ITId, 'IT_DualEntry') == 1){
                        
                        var params = "IT_Id="+listEditCustomItemGrid.getUserData(ITId, 'IT_DualItem');
                        var itemData = {type: "combo", name: "IT_DualItem", label: "Dual Item", required : "true",  value : "",offsetTop:"20", serverFiltering : "requisites/itemsDual.php&"}
                        addOfzItemForm.addItem(null, itemData, 9 );

                        var dualItemCombo = addOfzItemForm.getCombo("IT_DualItem");
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/itemsDual.php&"+ params), function(xml) {
                            dualItemCombo.load(xml.xmlDoc.responseText);
                        });

                    }
                        
                    menuOfzItemLayout.cells("b").expand();
                    menuOfzItemLayout.cells("b").showArrow();
                    menuOfzItemLayout.cells("c").expand();
                    menuOfzItemLayout.cells("c").showArrow();
                    menuOfzItemLayout.cells("d").showArrow();
                    menuOfzItemLayout.cells("b").setText("Add Description for Item " + listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                    menuOfzItemLayout.cells("b").setText("Add Default Descriptions for Item " + listEditCustomItemGrid.getUserData(ITId, "IT_Name"));
                    
                    
                    addItemDescriptionForm.resetValidateCss();
                    addItemDescriptionForm.clear();
                    addItemDescriptionForm.setItemValue("DS_Id", 0);
                    addItemDescriptionForm.setItemValue("IT_Id", ITId);

                    var params  = "IT_Id="+ITId; 
                    listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {
                        //preTally.BalanceSheet.editDescription();
                    });

                }
                preTally.Settings.progressOff(true, dhxLayout, null); 
                dhxMiddleBlockTabs.tabs("menuNewOfzItem").setActive();
            }
        },
        menuListPretallyItem: function() {
            if (!dhxMiddleBlockTabs.cells("menuListPretallyItem")) {
                dhxMiddleBlockTabs.addTab("menuListPretallyItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Pretally Items", 150);
                dhxMiddleBlockTabs.tabs("menuListPretallyItem").setActive();
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                listPretallyItemGrid = dhxMiddleBlockTabs.cells("menuListPretallyItem").attachGrid();
                
                var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);              
                var OFId = bsl.split("_");
                
                listPretallyItemGrid.setImagePath("assets/grid/codebase/imgs/");
                listPretallyItemGrid.setSkin("dhx_skyblue");
                if(OFId[1] != 1){
                        listPretallyItemGrid.setHeader("SlNo,\n\
                        <select style = 'width:50px;' class='selectFilter_PT' id='ieP_F'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,\n\
                        <input type='text'  class='textFilter_PT' id='itmP_F' style='width: 90%;' placeholder='Item'>,\n\
                        <input type='text'  class='textFilter_PT' id='shP_F' style='width: 90%;' placeholder='Sub Head'>,\
                        <select style = 'width:80px;' class='selectFilter_PT' id='mhP_F'><option value ='0'>All</option><option value ='1'>Direct Income</option><option value ='2'>Direct Expense</option><option value ='3'>Indirect Income</option><option value ='4'>Indirect Expense</option><option value ='5'>Assets</option></select>,#master_checkbox");
                    listPretallyItemGrid.setColTypes("ro,ro,ro,ro,ro,ch");
                } else {
                    listPretallyItemGrid.setHeader("SlNo,\n\
                        <select style = 'width:50px;' class='selectFilter_PT' id='ieP_F'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,\n\
                        <input type='text'  class='textFilter_PT' id='itmP_F' style='width: 90%;' placeholder='Item'>,\n\
                        <input type='text'  class='textFilter_PT' id='shP_F' style='width: 90%;' placeholder='Sub Head'>,\
                        <select style = 'width:80px;' class='selectFilter_PT' id='mhP_F'><option value ='0'>All</option><option value ='1'>Direct Income</option><option value ='2'>Direct Expense</option><option value ='3'>Indirect Income</option><option value ='4'>Indirect Expense</option><option value ='5'>Assets</option></select>,Status");
                    listPretallyItemGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                }
                listPretallyItemGrid.setInitWidths("50,80,*,*,*,60")
                listPretallyItemGrid.setColAlign("center,left,left,left,left,center")
                listPretallyItemGrid.setColSorting("na,na,na,na,na,na");                
                listPretallyItemGrid.enableEditEvents(true,true,true);
                listPretallyItemGrid.enableTooltips("false,false,false,false,false,false");

                listPretallyItemGrid.init();
                listPretallyItemGrid.enableSmartRendering(true,50);
                listPretallyItemGrid.enableColSpan(true);

                var filtrInterval;
                listPretallyItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listPretallyItems.php"), function() {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                    if(listPretallyItemGrid.getUserData("","OF_Id") != '1') {
                        dhxMiddleBlockTabs.cells("menuListPretallyItem").showStatusBar();    
                    }

                    $( ".textFilter_PT" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.Item.applyPretallyItemFilter(); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    });

                    $( ".selectFilter_PT" ).change(function() {
                        preTally.Item.applyPretallyItemFilter(); 
                    });

                });

//                listPretallyItemGrid.init();
//                listPretallyItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listPretallyItems.php"), function() {
//                    preTally.Settings.progressOff(true, dhxLayout, null);
//                    if(listPretallyItemGrid.getUserData("","OF_Id") != '1') {
//                        dhxMiddleBlockTabs.cells("menuListPretallyItem").showStatusBar();    
//                    }
//                });

                listPretallyItemGrid.attachEvent("onRowCreated",function(id){
                    var cell = listPretallyItemGrid.cells(id,5); //checkbox cell
                    if (cell.getAttribute("disabled")) cell.setDisabled(true);
                });
                    
                itemButtonBar = dhxMiddleBlockTabs.tabs("menuListPretallyItem").attachStatusBar({
                    text  : "<input type='button' value='SAVE' onclick='preTally.Item.addToCompanyItemSave();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
                dhxMiddleBlockTabs.cells("menuListPretallyItem").hideStatusBar();
            } else {
            dhxMiddleBlockTabs.tabs("menuListPretallyItem").setActive();
            }
                
        },
        menuListOtherCompanyItem: function() {
            if (!dhxMiddleBlockTabs.cells("menuListOtherCompanyItem")) {
                dhxMiddleBlockTabs.addTab("menuListOtherCompanyItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Other Company Items", 200);
                dhxMiddleBlockTabs.tabs("menuListOtherCompanyItem").setActive();
                
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                listOtherCompanyItemGrid = dhxMiddleBlockTabs.cells("menuListOtherCompanyItem").attachGrid();
                
                
                listOtherCompanyItemGrid.setImagePath("assets/grid/codebase/imgs/");
                listOtherCompanyItemGrid.setSkin("dhx_skyblue");
                listOtherCompanyItemGrid.setHeader("SlNo,\
                        <select style = 'width:50px;' class='selectFilter_OT' id='ieO_F'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,\n\
                        <input type='text'  class='textFilter_OT' id='itmO_F' style='width: 90%;' placeholder='Item'>,\n\
                        <input type='text'  class='textFilter_OT' id='shO_F' style='width: 90%;' placeholder='Sub Head'>,\
                        <select style = 'width:80px;' class='selectFilter_OT' id='mhO_F'><option value ='0'>All</option><option value ='1'>Direct Income</option><option value ='2'>Direct Expense</option><option value ='3'>Indirect Income</option><option value ='4'>Indirect Expense</option><option value ='5'>Assets</option></select>,\n\
                        <div id='ofzO_F' style='width: 90%;' placeholder='Item'></div>,#master_checkbox");
                listOtherCompanyItemGrid.setColTypes("ro,ro,ro,ro,ro,ro,ch");
                listOtherCompanyItemGrid.setInitWidths("50,80,*,*,*,*,60")
                listOtherCompanyItemGrid.setColAlign("center,left,left,left,left,left,center")
                listOtherCompanyItemGrid.setColSorting("na,na,na,na,na,na,na");                
                listOtherCompanyItemGrid.enableEditEvents(true,true,true);
                listOtherCompanyItemGrid.enableTooltips("false,false,false,false,false,false,false");

                listOtherCompanyItemGrid.init();
                listOtherCompanyItemGrid.enableSmartRendering(true,50);
                listOtherCompanyItemGrid.enableColSpan(true);
                
                var ofzCombo = new dhtmlXCombo("ofzO_F");
                ofzCombo.load(preTally.Initialize.encryptURL("requisites/offices.php"), function(){
                    ofzCombo.setPlaceholder('Company ');
                    ofzCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                                r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                r = true;
                        }
                        return r;
                    });
                });
                ofzCombo.setOptionWidth(250);
                ofzCombo.attachEvent("onChange", function() {
                    var ofzComboVal = ofzCombo.getSelectedValue();
                    if(!ofzCombo.getSelectedValue() && ofzCombo.getComboText()) ofzComboVal = ofzCombo.getComboText();
                    $( "#ofzO_F" ).val(ofzComboVal);
                    preTally.Item.applyOtherCompanyItemFilter();
                });
                        

                var filtrInterval;
                listOtherCompanyItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOtherCompanyItems.php"), function() {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                   
                    $( ".textFilter_OT" ).keyup(function() {
                        if(filtrInterval) clearInterval(filtrInterval);

                        filtrInterval = setInterval( function() { 
                            preTally.Item.applyOtherCompanyItemFilter(); 
                            clearInterval(filtrInterval); 
                        }, 500);
                    });

                    $( ".selectFilter_OT" ).change(function() {
                        preTally.Item.applyOtherCompanyItemFilter(); 
                    });

                });
//                
//                listOtherCompanyItemGrid.init();
//                listOtherCompanyItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOtherCompanyItems.php"), function() {
//                    preTally.Settings.progressOff(true, dhxLayout, null);
//                });

                var itemButtonBar = dhxMiddleBlockTabs.tabs("menuListOtherCompanyItem").attachStatusBar({
                    text  : "<input type='button' value='SAVE' onclick='preTally.Item.mapOtherCompanyItem();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
            } else {
            dhxMiddleBlockTabs.tabs("menuListOtherCompanyItem").setActive();
            }
                
        },
        mapOtherCompanyItem: function(ITId) {
            //alert('alias');
            //console.log(mapItemCustomSubGrid.getUserData(ITId, "IT_Name"));
            dhtmlx.confirm({
                title: "Add Item To PreTally Item Pool",
                ok: "Yes", cancel: "No",
                text: "Confirm Add Selected Items To PreTally Item Pool",
                callback: function(result) {
                    if (result == true){
                        var ItemMapList = listOtherCompanyItemGrid.getCheckedRows(6).split(",") ;
                        
                        var otrItemObject = {};
            
                        $.each(ItemMapList, function(index, value){

                            var otrItemDetails = {};
                
                            otrItemDetails['MH_Type'] = listOtherCompanyItemGrid.getUserData(value,'MH_Type');
                            otrItemDetails['IT_Name'] = listOtherCompanyItemGrid.getUserData(value,'IT_Name');
                            otrItemDetails['IT_Id']   = listOtherCompanyItemGrid.getUserData(value,'IT_Id');
                            otrItemDetails['SH_Id']   = listOtherCompanyItemGrid.getUserData(value,'SH_Id');
                            otrItemDetails['OF_Id_Alias']   = listOtherCompanyItemGrid.getUserData(value,'OF_Id_Alias');

                            otrItemObject[value] = otrItemDetails;

                        });
            
                        $.ajax({
                                type   : "POST",
                                url    : preTally.Initialize.encryptURL('warehouse/mapOtherCompanyItem.php'),
                                data   : otrItemObject                    

                        }).done(function(data) { 
                            if (data != '') {
                                dhtmlx.message({text: data });
                                listOtherCompanyItemGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listOtherCompanyItems.php"), true, true, function() {});    
                            }
                        });
                    }
                }
            });
        },
        menuListEditCustomItem: function(OFName) {
            if (!dhxMiddleBlockTabs.cells("menuListEditCustomItem")) {
                
                var filtrItemInterval ;
                
                dhxMiddleBlockTabs.addTab("menuListEditCustomItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;"+OFName, 190);
                dhxMiddleBlockTabs.tabs("menuListEditCustomItem").setActive();

                    preTally.Settings.progressOn(true, dhxLayout, null);
                    listEditCustomItemGrid = dhxMiddleBlockTabs.cells("menuListEditCustomItem").attachGrid();
                    listEditCustomItemGrid.setImagePath("assets/grid/codebase/imgs/");
                    listEditCustomItemGrid.setSkin("dhx_skyblue")
                    listEditCustomItemGrid.setHeader("SlNo, \
                    <select style = 'width:50px;' class='select_filter' id='ieCF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>, \
                    <div id='itmCF' style='width: 90%;' placeholder='Item'></div>,\
                    Description, \
                    <input type='text'  class='text_filter' id='shCF' style='width: 90%;' placeholder='Sub Head'>, \
                    <select style = 'width:80px;' class='select_filter' id='mhCF'><option value ='0'>All</option><option value ='1'>Direct Income</option><option value ='2'>Direct Expense</option><option value ='3'>Indirect Income</option><option value ='4'>Indirect Expense</option><option value ='5'>Assets</option></select>, \
                    <div id='addCF' style='width: 90%;' placeholder='Added By'></div>, \
                    <div id='brnCF' style='width: 90%;' placeholder='Branch'></div>,\
                    <div id='aprCF' style='width: 90%;' placeholder='Approved By'></div>,\n\
                    <select style = 'width:50px;' class='select_filter' id='stCF'><option value =''>All</option><option value ='1'>Approved</option><option value ='0'>Suspended</option></select>,Desc, Edit ,#master_checkbox");
                    //listEditCustomItemGrid.attachHeader(",#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,");
                    listEditCustomItemGrid.setInitWidths("40,60,*,*,180,100,80,100,100,60,40,40,40")
                    listEditCustomItemGrid.setColAlign("center,center,left,left,left,left,left,left,left,center,center,center,left")
                    listEditCustomItemGrid.setColTypes("ro,combo,combo,ro,combo,ro,ro,ro,ro,ro,ro,ro,ch");
                    //listEditCustomItemGrid.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str");                
                    listEditCustomItemGrid.enableEditEvents(true,true,true);
                    listEditCustomItemGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true,true,true");
                    listEditCustomItemGrid.init();
                    listEditCustomItemGrid.enableSmartRendering(true,50);
                    listEditCustomItemGrid.enableColSpan(true);
                        
                    listEditCustomItemGrid.attachEvent("onMouseOver", function(id,ind) { 
                        if(ind == 9) {
                            var ITStatus = listEditCustomItemGrid.getUserData(id, "IT_Status");
                            if(ITStatus == 0 ) var message = "Suspended";
                            if(ITStatus == 1 ) var message = "Approved";
                           
                            this.cells(id,ind).cell.title = message;
                            return false;
                        }
                        if(ind == 10) {
                            this.cells(id,ind).cell.title = 'Click here to view more Details';
                            return false;
                        }
                        if(ind == 11) {
                            this.cells(id,ind).cell.title = 'Click here to edit Item Details';
                            return false;
                        }
                    });

                        
                        var itemNotfCombo = new dhtmlXCombo("itmCF");
			itemNotfCombo.load(preTally.Initialize.encryptURL("requisites/ItemsCombo.php&ctype=filt"), function(){
                            itemNotfCombo.setPlaceholder('Item ');
                            itemNotfCombo.setFilterHandler(function(mask, option){
                                var r = false;
                                if (mask.length == 0) {
                                        r = true;
                                } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                        r = true;
                                }
                                return r;
                            });
			});
                        itemNotfCombo.setOptionWidth(250);
                        itemNotfCombo.attachEvent("onChange", function() {
                            var itmComboVal = itemNotfCombo.getSelectedValue();
                            if(!itemNotfCombo.getSelectedValue() && itemNotfCombo.getComboText()) itmComboVal = itemNotfCombo.getComboText();
                            $( "#itmCF" ).val(itmComboVal);
                            preTally.Item.applyCustomItemFilter();
                        });

                        var branchCombo = new dhtmlXCombo("brnCF");
			branchCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check"), function(){
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
                            $( "#brnCF" ).val(brComboVal);
                            preTally.Item.applyCustomItemFilter();
                        });
                        
                        var addedByCombo = new dhtmlXCombo("addCF");
                        var approvedByCombo = new dhtmlXCombo("aprCF");
                        
                        $.ajax({
                            url : preTally.Initialize.encryptURL("requisites/usersReportsMe.php&type=filt")
                        }).done(function(data) {
                            
                            var UserlistCombo = data;
//                            var addedByCombo = new dhtmlXCombo("addCF");
                            addedByCombo.load(UserlistCombo, function(){
                                addedByCombo.setPlaceholder('Added By');
                                addedByCombo.setFilterHandler(function(mask, option){
                                    var r = false;
                                    if (mask.length == 0) {
                                            r = true;
                                    } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                            r = true;
                                    }
                                    return r;
                                });
                            });
                            addedByCombo.setOptionWidth(180);
                            addedByCombo.attachEvent("onChange", function() {
                                var adComboVal = addedByCombo.getSelectedValue();
                                if( !addedByCombo.getSelectedValue() && addedByCombo.getComboText()) adComboVal = addedByCombo.getComboText();
                                $( "#addCF" ).val(adComboVal);
                                preTally.Item.applyCustomItemFilter();
                            });

//                            var approvedByCombo = new dhtmlXCombo("aprCF");
                            approvedByCombo.load(UserlistCombo, function(){
                                approvedByCombo.setPlaceholder('Approved By');
                                approvedByCombo.setFilterHandler(function(mask, option){
                                    var r = false;
                                    if (mask.length == 0) {
                                            r = true;
                                    } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                            r = true;
                                    }
                                    return r;
                                });
                            });
                            approvedByCombo.setOptionWidth(180);
                            approvedByCombo.attachEvent("onChange", function() {
                                var aprComboVal = approvedByCombo.getSelectedValue();
                                if( !approvedByCombo.getSelectedValue() && approvedByCombo.getComboText()) aprComboVal = approvedByCombo.getComboText();
                                $( "#aprCF" ).val(aprComboVal);
                                preTally.Item.applyCustomItemFilter();
                            });
                        });
                          
                        listEditCustomItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listEditCustomItems.php"), function() {
                            preTally.Settings.progressOff(true,dhxLayout, null);
                            
                            
                            $( ".text_filter" ).keyup(function() {
                                if(filtrItemInterval) clearInterval(filtrItemInterval);

                                filtrItemInterval = setInterval( function() { 
                                    preTally.Item.applyCustomItemFilter(); 
                                    clearInterval(filtrItemInterval); 
                                }, 500);
                            });
                              
                            $( ".select_filter" ).change(function() {
                                preTally.Item.applyCustomItemFilter(); 
                            });
                        
                        });
                
                                            
                        var itemIDs = new Array();

                        listEditCustomItemGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                            if(stage == 0) {
                                if(cInd == 1){
                                    var entryTypeCombo  = listEditCustomItemGrid.cells(rId,cInd).getCellCombo();
                                    var optnCnt = entryTypeCombo.getOptionsCount();

                                    if(optnCnt == 0) {
                                        entryTypeCombo.addOption([
                                            ["1","Income"],
                                            ["2","Expense"],
                                        ]);

                                        entryTypeCombo.attachEvent("onChange", function() {
                                            if(itemIDs.indexOf(rId) >= 0) {
                                                listEditCustomItemGrid.cells(rId,4).setValue('');
                                                listEditCustomItemGrid.cells(rId,5).setValue('');
                                                listEditCustomItemGrid.setUserData(rId,"MH_Type",entryTypeCombo.getSelectedValue());  
                                            } else {
                                                 itemIDs.push(rId);
                                            }
                                        });
                                    }

                                } else if(cInd == 2){
                                    
                                    var MHComboType = listEditCustomItemGrid.getUserData(rId, "MH_Type");
                                    var ITColCombo  = listEditCustomItemGrid.cells(rId,cInd).getCellCombo();
                                    var params = "type=" + MHComboType;
                                    ITColCombo.clearAll();
                                    ITColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/ItemsCombo.php&"+ params));
                                    ITColCombo.setOptionWidth(410);

                                    ITColCombo.attachEvent("onChange", function(loader, response) {
                                        listEditCustomItemGrid.setUserData(rId,"IT_Id",ITColCombo.getSelectedValue());
                                    });
                                } else if(cInd == 3){
                                    
                                    var ITComboType  = listEditCustomItemGrid.getUserData(rId, "IT_Id");//listEditCustomItemGrid.cellById(rId, 2).getAttribute("id");
                                    var DSColCombo   = listEditCustomItemGrid.cells(rId,cInd).getCellCombo();
                                    var params = "IT_Id=" +ITComboType+"&filter=1";
                                    DSColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/descriptions.php&"+params));
                                    DSColCombo.setOptionWidth(310);
                                    
                                } else if(cInd == 4){
                                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                        var MHComboType  = listEditCustomItemGrid.getUserData(rId, "MH_Type"); // listEditCustomItemGrid.cellById(rId, 1).getAttribute("id");
                                        var SHColCombo   = listEditCustomItemGrid.cells(rId,cInd).getCellCombo();
                                        SHColCombo.clearAll();
                                        var params = "type=" +MHComboType+ "&for=notfEdit";
                                        SHColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                                        SHColCombo.setOptionWidth(310);
                                        var onChgEvnt = SHColCombo.attachEvent("onChange", function(value, text) {

                                            var jsonResponse = SHColCombo.getSelectedValue();
                                            var obj = jQuery.parseJSON( jsonResponse );
                                            if(jsonResponse) {
                                                listEditCustomItemGrid.cells(rId,5).setValue(obj.MHName);
                                                SHColCombo.setComboValue(obj.SHId);
                                                SHColCombo.detachEvent(onChgEvnt);
                                            } else {
                                                listEditCustomItemGrid.cells(rId,5).setValue('');
                                            }
                                        });
                                    }
                                }
                            } else if(stage == 2){//console.log(nValue+"--"+oValue);
                                if(oValue != nValue) listEditCustomItemGrid.cells(rId,12).setValue(1);
//                                if(cInd == 2 && nValue != oValue){
//                                    dhtmlx.confirm({
//                                        title: "Approve Item "+listEditCustomItemGrid.cells(rId,cInd).getText(),
//                                        //type:"confirm-warning",
//                                        ok  : "Yes", cancel : "No",
//                                        text: "Do you want to change All " + "'" + listEditCustomItemGrid.getUserData(rId, "IT_Name_Old") + "'" + " item to " + "'" + listEditCustomItemGrid.cells(rId,cInd).getText() + "'" +" ?",
//                                        callback: function(response) {
//                                            if(response){
//                                                $.post(
//                                                    'warehouse/approveMultipleItem.php',
//                                                    { IT_Id : listEditCustomItemGrid.cells(rId,cInd).getValue() , IT_Name : listEditCustomItemGrid.cells(rId,cInd).getText(), IT_Id_Old : listEditCustomItemGrid.getUserData(rId, "IT_Id_Old") },
//                                                    function(responseText) {
//                                                        
//                                                        itemNotfCombo.clearAll();
//                                                        var params = "IT_Id="+$('#itmCF').val();
//                                                        itemNotfCombo.load("requisites/itemsFilter.php?"+encrypt(params));
//                                                        
//                                                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
//                                                            var filterValue = new Array($('#ieF').val(), $('#itmCF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addCF').val(), $('#brnCF').val(), $('#aprCF').val(), $('#aprvlF').val());
//                                                        } else {
//                                                            var filterValue = new Array($('#ieF').val(), $('#itmCF').val(), $('#decF').val(), '', '', '', $('#addCF').val(), $('#brnCF').val(), $('#aprCF').val(), $('#aprvlF').val());
//                                                        }
//
//                                                        listEditCustomItemGrid.clearAll();
//                                                        preTally.Settings.progressOn(true, dhxLayout, null);
//
//                                                        listEditCustomItemGrid.clearAndLoad("requisites/listEditCustomItems.php?"+encrypt('filter='+filterValue), function() {
//                                                            preTally.Settings.progressOff(true, dhxLayout, null);
//                                                        });
//                                                       
//                                                        dhtmlx.message({text: responseText });
//            
//                                                    }
//                                                );  
//                                            }
//                                        }
//                                    });
//                                }
//                                console.log( listEditCustomItemGrid.cells(rId,cInd).getValue()+"--"+ listEditCustomItemGrid.cells(rId,cInd).getText() +"--"+ listEditCustomItemGrid.getUserData(rId, "IT_Id_Old") );
                            }
                            return true;
                        });
  
                        itemButtonBar = dhxMiddleBlockTabs.tabs("menuListEditCustomItem").attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.Item.editCustomItem();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                        dhxMiddleBlockTabs.cells("menuListEditCustomItem").showStatusBar();
                        listEditCustomItemGrid.attachEvent("onCheckbox", function(rId,cInd,state){
                            if(state == false) {
                                listEditCustomItemGrid.setRowTextStyle(rId,false);
                            }
                        }); 
                } else { 
                dhxMiddleBlockTabs.tabs("menuListEditCustomItem").setActive();
                }
                
        },
        applyCustomItemFilter:  function(value){
            
            var filterValue = new Array($('#ieCF').val(), $('#itmCF').val(), $('#decCF').val(), $('#shCF').val(), $('#mhCF').val(), $('#addCF').val(), $('#brnCF').val(),  $('#aprCF').val(), $('#stCF').val());
                        
            listEditCustomItemGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            listEditCustomItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listEditCustomItems.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyPretallyItemFilter :  function(value){

                var filterValue = new Array($('#ieP_F').val(), $('#itmP_F').val(), $('#shP_F').val(), $('#mhP_F').val());
                
                listPretallyItemGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                listPretallyItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listPretallyItems.php&filter="+filterValue), function() {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                });
        },
        applyOtherCompanyItemFilter :  function(value){

                var filterValue = new Array($('#ieO_F').val(), $('#itmO_F').val(), $('#shO_F').val(), $('#mhO_F').val(), $("#ofzO_F").val());
                
                listOtherCompanyItemGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);
                
                listOtherCompanyItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listOtherCompanyItems.php&filter="+filterValue), function() {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                });
        },
        editCustomItem: function() {
                             
            var itemObject      = {};
            var error           = 0;
            var ieArray         = new Array();
            ieArray['Income']   = '1';
            ieArray['Expense']  = '2';
                    
            listEditCustomItemGrid.forEachRow(function(rId){
              
                if(listEditCustomItemGrid.cells(rId,12).getValue() == 1) {
                    var itemDetails = {}; 

                    itemDetails['MH_Type'] = listEditCustomItemGrid.cells(rId,1).getValue();
                    itemDetails['IT_Name'] = listEditCustomItemGrid.cells(rId,2).getValue();
//                    itemDetails['DS_Description'] = listEditCustomItemGrid.cells(rId,3).getText();
                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                        itemDetails['SH_Name'] = listEditCustomItemGrid.cells(rId,4).getValue();
                        itemDetails['MH_Name'] = listEditCustomItemGrid.cells(rId,5).getValue();
                        itemDetails['BS_Amount']  = listEditCustomItemGrid.cells(rId,6).getValue();
                    } else {
                        itemDetails['SH_Name'] = 1;
                        itemDetails['MH_Name'] = 1;
                        itemDetails['BS_Amount'] = 1;
                    }
                    
                    if(!itemDetails['MH_Type'] || !itemDetails['IT_Name'].trim() || !itemDetails['SH_Name'] || !itemDetails['MH_Name'] || !itemDetails['BS_Amount']) {
                        dhtmlx.message({text: '<b style="color:#FF0000;">Item ' + listEditCustomItemGrid.cells(rId,0).getValue() + ' is Error. Please Verify.</b>'});
                        error = 1;
                    } else {

                        if (/^[\],:{}\s]*$/.test(listEditCustomItemGrid.cells(rId,4).getValue().replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                            var obj = jQuery.parseJSON( listEditCustomItemGrid.cells(rId,4).getValue() );
                        }else{
                            var obj = jQuery.parseJSON( listEditCustomItemGrid.getUserData(rId, "SHDetails") );
                        }
                        itemDetails['MH_Type'] = ieArray[listEditCustomItemGrid.cells(rId,1).getText()];
                        itemDetails['IT_Name'] = listEditCustomItemGrid.cells(rId,2).getValue();
                        itemDetails['IT_Id_Old'] = listEditCustomItemGrid.getUserData(rId,"IT_Id_Old");
//                        itemDetails['DS_Description'] = listEditCustomItemGrid.cells(rId,3).getText();
                        itemDetails['SH_Name'] = obj.SHId;
                        itemDetails['MH_Name'] = obj.MHId;
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            itemDetails['BS_Amount']  = listEditCustomItemGrid.cells(rId,6).getValue();
                        } else { 
                            itemDetails['BS_Amount']  = listEditCustomItemGrid.getUserData(rId, "BS_Amount");
                        }
//                        alert(listEditCustomItemGrid.cells(rId,2).getText());
                        itemObject[rId] = itemDetails;
                    }
                }
            });
            if(error == 0) {
//                console.log(itemObject);
                $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL("warehouse/editCustomItem.php"),
                    data   : itemObject
                }).done(function(data) {
                    
                    
                    if (data != '') {
                        listEditCustomItemGrid.hdr.rows[1].cells[12].getElementsByTagName("INPUT")[0].checked = false;
                        listEditCustomItemGrid.clearAll();
                        preTally.Settings.progressOn(true, dhxLayout, null);

                        listEditCustomItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listEditCustomItems.php"), function() {
                            preTally.Settings.progressOff(true, dhxLayout, null);
                        });
                                                        
                        dhtmlx.message({text: data });
//                        listEditCustomItemGrid.updateFromXML("requisites/listEditCustomItems.php", true, true, function() {});    
                    }else {
                       dhtmlx.message({text: '<b style="color:#FF0000;">Please Select an Item to Save.</b>'});
                    }
                                        
                });
            }
        },
        editCompanyItem : function(inp,rowId){ 
            var ITId = rowId;
            if (dhxMiddleBlockTabs.cells("menuNewOfzItem")) {
                addOfzItemForm.resetValidateCss();
                addOfzItemForm.clear();
            }
            preTally.Item.menuNewOfzItem(ITId);
        },
        addToCompanyItemSave: function() {
            var ItemMapList = listPretallyItemGrid.getCheckedRows(5).split(",") ;
            var ptItemObject = {};
            
            $.each(ItemMapList, function(index, value){

                var ptItemDetails = {};
                
                ptItemDetails['MH_Type'] = listPretallyItemGrid.getUserData(value,'MH_Type');
                ptItemDetails['IT_Name'] = listPretallyItemGrid.getUserData(value,'IT_Name');
                ptItemDetails['SH_Id']   = listPretallyItemGrid.getUserData(value,'SH_Id');
                
                ptItemObject[value] = ptItemDetails;
                
            });
            
            $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL('warehouse/mapPretallyItem.php&c=' + JSON.stringify( ItemMapList )),
                    data   : ptItemObject                    
                    
            }).done(function(data) { 
                if (data != '') {
                    dhtmlx.message({text: data });
                    listPretallyItemGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listPretallyItems.php"), true, true, function() {});    
                }
            });
//            var itemResponseMap = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/mapItemCompany.php&c=' + JSON.stringify( ItemMapList )), encodeURI(1));
//            if(itemResponseMap.xmlDoc.responseText != null) {
//                dhtmlx.message({text: itemResponseMap.xmlDoc.responseText});
//            }
            
        },
        showDescription : function(inp,IT_Id){           
           
            
            dhxDescWin = new dhtmlXWindows();           
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);            
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;      
            
            
            
            DS_Win = dhxDescWin.createWindow("wins_desc", x, y, 500, 300);    
            DS_Win.button("minmax1").hide();
            DS_Win.button("minmax2").hide();
            DS_Win.button("park").hide();
            DS_Win.center();
            DS_Win.setModal(true);
            DS_Win.setText("Descriptions");   
            var DSDetailsGrid = DS_Win.attachGrid();
            DSDetailsGrid.attachEvent("onXLS",function(){
                preTally.Settings.progressOn(true, DS_Win, null);
           });
           DSDetailsGrid.attachEvent("onXLE",function(){
                preTally.Settings.progressOff(true, DS_Win, null);
           });
            DSDetailsGrid.setHeader("Slno,Description");
            DSDetailsGrid.setInitWidths("50,*");
            DSDetailsGrid.setColAlign("left,left");
            DSDetailsGrid.setColTypes("ro,ro");
            DSDetailsGrid.setColSorting("str,str"); 
            DSDetailsGrid.init();
           
            var IT_Name = listEditCustomItemGrid.getUserData(IT_Id, "IT_Name")
            var params  = "IT_Id="+IT_Id+"&IT_Name="+IT_Name; 
            DSDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/descriptionPop.php&"+ params));
        },
        hideDescription : function(){
            if(DS_Win.isVisible()) {
                DS_Win.hide();
            } 
        },
        editItemDescription: function(rowId) { 
            var DSId = rowId; 
                addItemDescriptionForm.setItemValue("DS_Id", DSId);
                addItemDescriptionForm.setItemValue("IT_Id", listItemDescriptionGrid.getUserData(DSId, "IT_Id"));
                addItemDescriptionForm.setItemValue("DS_Description", listItemDescriptionGrid.getUserData(DSId, "DS_Description"));                
                addItemDescriptionForm.setItemValue("DS_Status", listItemDescriptionGrid.getUserData(DSId, "DS_Status"));
                addItemDescriptionForm.setItemValue("DS_MinAmount", listItemDescriptionGrid.getUserData(DSId, "DS_MinAmount"));
                addItemDescriptionForm.setItemValue("DS_MaxAmount", listItemDescriptionGrid.getUserData(DSId, "DS_MaxAmount"));
        },
        addBranchesDesc: function() {           
            var branchNames = []; 
            
            listBranchesGrid.forEachRow(function(rId){
                if(listBranchesGrid.cells(rId,2).getValue() == 1) { 
                    branchNames.push(listBranchesGrid.cells(rId,1).getValue());
                }
            });
            
            addDefaultItemDescriptionForm.setItemValue('DS_Description',branchNames);
            addDefaultItemDescriptionForm.send(preTally.Initialize.encryptURL("warehouse/newDefaultDescription.php"), function(loader, response) {
                dhtmlx.message({text: response});
                dhxLCDecsription.window("addBranchWin").close();
                
                var params  = "IT_Id="+addDefaultItemDescriptionForm.getItemValue("IT_Id"); 
                listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {});
            });
        },
        addBanksDesc: function() {           
            var bankDispNames = []; 
            
            listBanksGrid.forEachRow(function(rId){
                if(listBanksGrid.cells(rId,2).getValue() == 1) { 
                    bankDispNames.push(listBanksGrid.cells(rId,1).getValue());
                }
            });
            
            addDefaultItemDescriptionForm.setItemValue('DS_Description',bankDispNames);
            addDefaultItemDescriptionForm.send(preTally.Initialize.encryptURL("warehouse/newDefaultDescription.php"), function(loader, response) {
                dhtmlx.message({text: response});
                dhxBNKDecsription.window("addBanksWin").close();
                
                var params  = "IT_Id="+addDefaultItemDescriptionForm.getItemValue("IT_Id"); 
                listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {});
            });
             
        },
        addUsersDesc: function() {           
            var userNames = []; 
            
            listUsersGrid.forEachRow(function(rId){
                if(listUsersGrid.cells(rId,6).getValue() == 1) { 
                    userNames.push(listUsersGrid.cells(rId,2).getValue());
                }
            });
            
            addDefaultItemDescriptionForm.setItemValue('DS_Description',userNames);
            addDefaultItemDescriptionForm.send(preTally.Initialize.encryptURL("warehouse/newDefaultDescription.php"), function(loader, response) {
                dhtmlx.message({text: response});
                dhxUSDecsription.window("addUsersWin").close();
                
                var params  = "IT_Id="+addDefaultItemDescriptionForm.getItemValue("IT_Id"); 
                listItemDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescription.php&"+ params), true, true, function() {});
            });
             
        },
        
            /*    menuNewCompanyItem: function(id,obj) {
            var ITId = id; 
            if(obj){
                gridObject = listItemNoftGrid;
            }else{
                gridObject = listCustomItemGrid;
            }
            if (!dhxMiddleBlockTabs.cells("menuNewCompanyItem")) {
                dhxMiddleBlockTabs.addTab("menuNewCompanyItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Add New Item", 180);
                dhxMiddleBlockTabs.tabs("menuNewCompanyItem").setActive();

                preTally.Settings.progressOn(true, dhxLayout, null);
                var menuCompanyItemLayout = dhxMiddleBlockTabs.cells("menuNewCompanyItem").attachLayout('1C');
                menuCompanyItemLayout.cells("a").setText("Add New Item");
                
                //----------------- Attach Form for Item --------------------//
                addCompanyItemForm = menuCompanyItemLayout.cells("a").attachForm();
                addCompanyItemForm.loadStruct("requisites/newCompanyItem.php?r=" + new Date().getTime(), function() {
                    
                preTally.Settings.progressOff(true, dhxLayout, null);
                dsPosition = 3;
                dsCount = 1; 
            
                if(ITId!=0)   
                {    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var params="IT_Id="+ITId;
                    $.ajax({
                        url: "requisites/getDescriptions.php?"+encrypt(params)                                 
                    }).done(function(data) {
                        var descData = new Object();
                        descData = jQuery.parseJSON(data); 

                        $.each(descData, function(index, value) {
                            var DS_Description = descData[index].DS_Description;
                            var DS_Id = descData[index].DS_Id;

                            var itemData = {type: "label", name : "DS_Label[O_"+DS_Id+"]", offsetLeft:"0", list: [
                                {type: "input", name: "DS_Description[O_"+DS_Id+"]", label: "Description "+dsCount, required : "true",  value : DS_Description,offsetLeft:"-20", note:[
                                    {text: "Description"},]},
                                {type : "newcolumn"},
                                {type: "button",  name : "removeDescription-O_"+DS_Id , value : " x ", offsetTop : "-5"}
                            ]};
                            addCompanyItemForm.addItem(null, itemData, dsPosition++ );
                            dsCount++;
                        });
                        preTally.Settings.progressOff(true, dhxLayout, null); 
                        
                    });
                } else {
                    var itemData = {type: "label", name : "DS_Label[1]", list: [
                            {type: "input", name: "DS_Description[1]", label: "Description 1", required : "true",  value : "",offsetLeft:"-20", note:[
                                {text: "Description"},]},
			    {type : "newcolumn"},
			    {type: "button",  name : "removeDescription-1" , value : " x " }
		    ]};
                    addCompanyItemForm.addItem(null, itemData, dsPosition++ );
                    dsCount++;
                }
                    
                    SH_CMpnyCombo = addCompanyItemForm.getCombo("SH_Id");
                    MH_CMpnyCombo = addCompanyItemForm.getCombo("MH_Type");
                    addCompanyItemForm.getInput("IT_Comments").setAttribute("placeholder","Enter Item Details Here for Future Use."); 
                                  
                    if(ITId!=0)
                    {
                        addCompanyItemForm.setItemValue("IT_Id", ITId);
                        addCompanyItemForm.setItemValue("IT_Name", gridObject.getUserData(ITId, "IT_Name"));
                        addCompanyItemForm.setItemValue("IT_Comments",gridObject.getUserData(ITId, "IT_Comments"));
                        addCompanyItemForm.setItemValue("US_Id",gridObject.getUserData(ITId, "US_Id"));
                        addCompanyItemForm.setItemValue("IT_Business",gridObject.getUserData(ITId, "IT_Business"));
                        addCompanyItemForm.setItemValue("IT_Transfers",gridObject.getUserData(ITId, "IT_Transfers"));
                        addCompanyItemForm.setItemValue("IT_Status",gridObject.getUserData(ITId, "IT_Status"));
                        SH_CMpnyCombo.clearAll();
                        addCompanyItemForm.setItemValue("MH_Type", gridObject.getUserData(ITId, "MH_Type"));
                        SHId = gridObject.getUserData(ITId, "SH_Id");
                        var params="type=" + gridObject.getUserData(ITId, "MH_Type")+"&SHId="+SHId;
                        dhx4.ajax.get("requisites/subheads.php?"+encrypt(params), function(xml) {
                            SH_CMpnyCombo.load(xml.xmlDoc.responseText);
                        });
                        var params="type=" + gridObject.getUserData(ITId, "MH_Type");
                        SH_CMpnyCombo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
                    } else {
                        var MhTyp = addCompanyItemForm.getItemValue("MH_Type");
                        var params="type=" + MhTyp;
                        SH_CMpnyCombo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
                    }
                
                    MH_CMpnyCombo.attachEvent("onChange", function(loader, response) { 
                            var MhTyp = addCompanyItemForm.getItemValue("MH_Type");
                            SH_CMpnyCombo.clearAll();
                            SH_CMpnyCombo.setComboText("");
                            SH_CMpnyCombo.setComboValue("");
                            var params="type="+MhTyp;
                            SH_CMpnyCombo.enableFilteringMode(true,"requisites/subheads.php?"+encrypt(params));
                            if(ITId!=0) addCompanyItemForm.setItemValue("SH_Id", gridObject.getUserData(ITId, "SH_Id")); 
                            //SH_CMpnyCombo.load("requisites/subheads.php?"+encrypt(params));
                    });
                    
                    addCompanyItemForm.attachEvent("onButtonClick", function(name) {

                        var values = addCompanyItemForm.getFormData();
                      
                        if (name == 'newItemValidate') {
                            var newItem = addCompanyItemForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'IT_Name', addCompanyItemForm, 'title');
                            if (newItem  && CstmValidate) {
                                                               
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addCompanyItemForm.send('warehouse/newCompanyItem.php', function(loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null); 
                                    
                                    var res = response.split("_"); 
                                    if (response != 'fail' && response != 'Invalid' && res[0] != 'N' && res[0] != 'O' && response!="flag") {
                                        addCompanyItemForm.resetValidateCss();
                                        addCompanyItemForm.clear();
                                        addCompanyItemForm.setItemValue("IT_Id", 0);
                                        SH_CMpnyCombo.clearAll();
                                        SH_CMpnyCombo.setComboText("");
                                        SH_CMpnyCombo.setComboValue("");
                                      
                                        var desc_Name = [];
                                        addCompanyItemForm.forEachItem(function(name){
                                            var DS_name = name.split("[");
                                            if(DS_name[0] === 'DS_Label'){ desc_Name.push(name);  }
                                        });
                                        var desc_Count = desc_Name.length;
                                        var i = 0;
                                        while(desc_Count > i){ addCompanyItemForm.removeItem(desc_Name[i]); dsCount--; dsPosition--;  i++;}
                                        var itemData = {type: "label", name : "DS_Label[1]", list: [
                                                {type: "input", name: "DS_Description[1]", label: "Description 1", required : "true",  value : "",offsetLeft:"-20", note:[
                                                    {text: "Description"},]},
                                                {type : "newcolumn"},
                                                {type: "button",  name : "removeDescription-1" , value : " x " }
                                        ]};
                                        dsPosition = 3;
                                        dsCount = 2; 
                                        addCompanyItemForm.addItem(null, itemData, dsPosition++ );  
                                        
                                    } else if(response == 'Invalid') {
                                        response = 'Invalid Subhead. Please Re-Try';
                                    }else if(res[0] === 'N' || res[0] === 'O' ) { 
                                        addCompanyItemForm.setValidateCss('DS_Description['+response+']', false,  'validate_red');
                                        response = 'Description Exists. Please Re-Try';
                                    }
                                    else if(response =="flag"){
                                        response = "Item can't be both Transaction and Bussiness.";
                                    }
                                    else{
                                        response = 'Item Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                });
                            }
                        }else if(name == 'addDescription'){
                            var itemData = {type: "label",name : "DS_Label[N_"+dsCount+"]",offsetLeft: "0", list: [
                                    {type: "input", name: "DS_Description[N_"+dsCount+"]", label: "Description "+dsCount, required : "true",  value : "", offsetLeft:"-20",note:[
                                        {text: "Description"},]},
                                    {type : "newcolumn"},
                                    {type: "button",  name : "removeDescription-N_"+dsCount , value : " x ", id :"10"}
                            ]};
                            addCompanyItemForm.addItem(null, itemData, dsPosition++ );
                            dsCount++;
                        } else { 
                            var btn = name.split("-"); 
                            if(btn[0] == 'removeDescription') { 
                                addCompanyItemForm.removeItem("DS_Label["+btn[1]+"]"); dsPosition--;//dsCount--;
                                if(ITId != 0){
                                    var DSID = btn[1].split("_");
                                    var params="DSID="+DSID[1];
                                    dhx4.ajax.postSync("requisites/setDescription.php?"+encrypt(params));
                                }
                            } else {
                                addCompanyItemForm.resetValidateCss();
                                addCompanyItemForm.clear();
                                addCompanyItemForm.setItemValue("IT_Id", 0);
                                SH_CMpnyCombo.clearAll();
                                SH_CMpnyCombo.setComboText("");
                                SH_CMpnyCombo.setComboValue("");
                            }
                            
                        }
                    });

                });
                } else { 
                    if(ITId!=0)
                    {   
                        preTally.Settings.progressOn(true, dhxLayout, null); 
                        var desc_Name = [];
                        addCompanyItemForm.forEachItem(function(name){
                            var DS_name = name.split("[");
                            if(DS_name[0] === 'DS_Label'){
                                desc_Name.push(name);
                            }
                        });
                        var desc_Count = desc_Name.length;
                        var i = 0; 
                        while(desc_Count > i){ addCompanyItemForm.removeItem(desc_Name[i]); dsCount--; dsPosition--;  i++;}
                        
                        dsPosition = 3;
                        dsCount = 1; 
                
                        var params="IT_Id="+ITId;
                        $.ajax({
                            url: "requisites/getDescriptions.php?"+encrypt(params)                                 
                        }).done(function(data) {
                            var descData = new Object();
                            descData = jQuery.parseJSON(data); 

                            $.each(descData, function(index, value) {
                                var DS_Description = descData[index].DS_Description;
                                var DS_Id = descData[index].DS_Id;
                                var itemData = {type: "label",name: "DS_Label[O_"+DS_Id+"]", offsetLeft:"0", list: [
                                    {type: "input", name: "DS_Description[O_"+DS_Id+"]", label: "Description "+dsCount, required : "true",  value : DS_Description, offsetLeft:"-20", note:[
                                        {text: "Description"},]},
                                    {type : "newcolumn"},
                                    {type: "button",  name : "removeDescription-O_"+DS_Id , value : " x "}
                                ]};
                                addCompanyItemForm.addItem(null, itemData, dsPosition++ );
                                dsCount++;
                            });
                        });                
                                        
                        addCompanyItemForm.setItemValue("IT_Name", gridObject.getUserData(ITId, "IT_Name"));
                        addCompanyItemForm.setItemValue("IT_Id", ITId);
                        addCompanyItemForm.setItemValue("MH_Type", gridObject.getUserData(ITId, "MH_Type"));
                        addCompanyItemForm.setItemValue("IT_Comments", gridObject.getUserData(ITId, "IT_Comments"));
                        addCompanyItemForm.setItemValue("US_Id",gridObject.getUserData(ITId, "US_Id"));
                        addCompanyItemForm.setItemValue("IT_Business",gridObject.getUserData(ITId, "IT_Business"));
                        addCompanyItemForm.setItemValue("IT_Transfers",gridObject.getUserData(ITId, "IT_Transfers"));
                        addCompanyItemForm.setItemValue("IT_Status",gridObject.getUserData(ITId, "IT_Status"));
                        SH_CMpnyCombo.clearAll();
                        SH_CMpnyCombo.setComboText("");
                        SH_CMpnyCombo.setComboValue("");
                        SHId = gridObject.getUserData(ITId, "SH_Id");
                        var params="type=" + gridObject.getUserData(ITId, "MH_Type")+"&SHId="+SHId;
                        dhx4.ajax.get("requisites/subheads.php?"+encrypt(params), function(xml) {
                            SH_CMpnyCombo.load(xml.xmlDoc.responseText);
                        });
                    }
                    preTally.Settings.progressOff(true, dhxLayout, null); 
                    dhxMiddleBlockTabs.tabs("menuNewCompanyItem").setActive();
            }
        },*/
//        menuListCustomItem: function() {
//            if (!dhxMiddleBlockTabs.cells("menuListCustomItem")) {
//                dhxMiddleBlockTabs.addTab("menuListCustomItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Custom Items", 150);
//                dhxMiddleBlockTabs.tabs("menuListCustomItem").setActive();
//
//                    preTally.Settings.progressOn(true, dhxLayout, null);
//                    listCustomItemGrid = dhxMiddleBlockTabs.cells("menuListCustomItem").attachGrid();
//                    listCustomItemGrid.init();
//                    listCustomItemGrid.enableTooltips("false,false,false,false,false,false,false,true");
//                
//                    listCustomItemGrid.attachEvent("onMouseOver", function(id,ind) { 
//                        if(ind == 5) {
//                            this.cells(id,ind).cell.title = 'Click here to view Descriptions';
//                            return false;
//                        }
//                        if(ind == 6) {
//                            this.cells(id,ind).cell.title = 'Click here to edit Item Details';
//                            return false;
//                        }
//                    });
//                
//                    listCustomItemGrid.loadXML("requisites/listCustomItems.php", function() {
//                            preTally.Settings.progressOff(true, dhxLayout, null);
//                            //preTally.BalanceSheet.menuNewCompanyItem();
//                    });
//                } else {
//                dhxMiddleBlockTabs.tabs("menuListCustomItem").setActive();
//                }
//                
//        }

    };
})(jQuery, this);