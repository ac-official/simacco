;
(function ($, window, undefined) {
    var invntGrid;
    preTally.Inventory = {
        addInventory: function () {
            if (!dhxMiddleBlockTabs.cells("addInventory")) {
                var INV_ItemNameCombo, LocCombo, AssigneeCombo;
                dhxMiddleBlockTabs.addTab("addInventory", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Add Inventory", 180);
                dhxMiddleBlockTabs.tabs("addInventory").setActive();

                addInventoryLayout = dhxMiddleBlockTabs.cells("addInventory").attachLayout('1C');
                addInventoryLayout.cells("a").setText("Inventory Management");
                addInventoryLayout.cells("a").hideHeader();
                var invntToolbar = addInventoryLayout.cells('a').attachToolbar();
                invntToolbar.setIconsPath("images/icon/default_18/");
                invntToolbar.addButton('Inv_Item', 0, "New Item", 'new.gif', 'new.gif');
                invntToolbar.addButton('Inv_Categ', 1, "New Type", 'other.gif', 'other.gif');
                invntToolbar.addButton('Inv_MngPattern', 2, "Manage Type Pattern", 'other.gif', 'other.gif');
                invntToolbar.attachEvent("onClick", function (id) {
                    if (id == "Inv_Item") {
                        var dhxInvWin = new dhtmlXWindows();
                        var InvWin = dhxInvWin.createWindow("wins_inv", 200, 400, 700, 400);
                        InvWin.button("minmax1").hide();
                        InvWin.button("minmax2").hide();
                        InvWin.button("park").hide();
                        InvWin.center();
                        InvWin.setModal(true);
                        InvWin.setText("Add Inventory Item");
                        var addInventoryForm = InvWin.attachForm();
                        addInventoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/newInventoryItem.php"), function () {
                            INV_ItemNameCombo = addInventoryForm.getCombo("INV_TypeID");
                            LocCombo = addInventoryForm.getCombo("LC_Id");
                            AssigneeCombo = addInventoryForm.getCombo("INV_ItemAssignee");
                            INV_ItemNameCombo.load(preTally.Initialize.encryptURL("requisites/inventorytypes.php"));
                            LocCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=all"));
                            AssigneeCombo.load(preTally.Initialize.encryptURL("requisites/persons.php"));
                            addInventoryForm.attachEvent("onButtonClick", function (id) {
                                if (id == "newInvItemSave") {
                                    var InvFormVal = addInventoryForm.validate();
                                    if (InvFormVal) {
                                        addInventoryForm.send(preTally.Initialize.encryptURL("warehouse/newInventoryItem.php"), function (loader, response) {
                                            addInventoryForm.clear();
                                            dhtmlx.message(response);
                                            invntGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listInventoryItem.php"), true);
                                        });
                                    }
                                }
                            });


                        });
                    } else if (id == "Inv_Categ") {
                        var dhxInvTypeWin = new dhtmlXWindows();
                        var InvTypeWin = dhxInvTypeWin.createWindow("wins_inv", 200, 400, 300, 150);
                        InvTypeWin.button("minmax1").hide();
                        InvTypeWin.button("minmax2").hide();
                        InvTypeWin.button("park").hide();
                        InvTypeWin.center();
                        InvTypeWin.setModal(true);
                        InvTypeWin.setText("Add Inventory Item Type");
                        var addInventoryTypeForm = InvTypeWin.attachForm();
                        addInventoryTypeForm.loadStruct([{type: "hidden", name: "INV_TypeID", value: "0"}, {type: "input", name: "InvntTypeName", label: "Type", offsetLeft: "10"},
                            {type: "block", list: [{type: "button", name: "SaveTypeButton", value: "Save"},
                                    {type: "newcolumn"}, {type: "button", name: "CancelTypeButton", value: "Cancel"}]}], function () {
                            addInventoryTypeForm.attachEvent("onButtonClick", function (id) {
                                if (id == "SaveTypeButton") {
                                    var Invnttypeval = addInventoryTypeForm.validate();
                                    if (Invnttypeval) {
                                        addInventoryTypeForm.send(preTally.Initialize.encryptURL("warehouse/newInventoryType.php"), function (loader, response) {
                                            dhtmlx.message(response);
                                        });
                                    }
                                }
                            });
                        });
                    }else if(id == "Inv_MngPattern"){
                        preTally.Inventory.manageInventoryPattern();
                    }
                });
                invntGrid = addInventoryLayout.cells('a').attachGrid();
                invntGrid.setImagePath("../../codebase/imgs/");
                invntGrid.setSkin("dhx_skyblue")
                invntGrid.setHeader("SlNo,Brand/Name,Model,Type,Quantity,Assignee/Owner,Branch,Status,Purchase Date,,");
                invntGrid.setInitWidths("50,*,250,200,75,120,120,120,120,50,0");
                invntGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                invntGrid.setColAlign("center,left,left,left,center,left,left,center,center,center,left");
                invntGrid.attachHeader(",#text_filter,#text_filter,#select_filter,,#select_filter,#select_filter,#select_filter,,,,,");
                invntGrid.init();
                invntGrid.load(preTally.Initialize.encryptURL("requisites/listInventoryItem.php"), function () {


                });

            } else {                
                dhxMiddleBlockTabs.tabs("addInventory").setActive();
            }
        }, editInventoryItem: function (inp, rowId) {

            var INV_ItemNameCombo, LocCombo, AssigneeCombo;
            var INV_ItemID = rowId;
            var dhxInvWin = new dhtmlXWindows();
            var InvWin = dhxInvWin.createWindow("wins_inv", 200, 400, 700, 400);
            InvWin.button("minmax1").hide();
            InvWin.button("minmax2").hide();
            InvWin.button("park").hide();
            InvWin.center();
            InvWin.setModal(true);
            InvWin.setText("Update Inventory Item");
            var addInventoryForm = InvWin.attachForm();
            var params = "INV_ItemID=" + rowId;
            addInventoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/newInventoryItem.php&" + params), function () {
                addInventoryForm.setItemValue("INV_ItemID", rowId);
                addInventoryForm.setItemValue("INV_ItemName", invntGrid.getUserData(rowId, "INV_ItemName")); //INV_ItemID
                addInventoryForm.setItemValue("INV_ItemModel", invntGrid.getUserData(rowId, "INV_ItemModel"));
                addInventoryForm.setItemValue("INV_ItemQty", invntGrid.getUserData(rowId, "INV_ItemQty"));
                addInventoryForm.setItemValue("INV_PurchaseDate", invntGrid.getUserData(rowId, "INV_PurchaseDate"));
                addInventoryForm.setItemValue("INV_ItemRemarks", invntGrid.getUserData(rowId, "INV_ItemRemarks"));

                INV_ItemNameCombo = addInventoryForm.getCombo("INV_TypeID");
                LocCombo = addInventoryForm.getCombo("LC_Id");
                AssigneeCombo = addInventoryForm.getCombo("INV_ItemAssignee");
                INV_ItemNameCombo.load(preTally.Initialize.encryptURL("requisites/inventorytypes.php"), function () {
                    INV_ItemNameCombo.setComboValue(invntGrid.getUserData(rowId, "INV_TypeID"));
                });
                LocCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=all&seltdQffz=" + invntGrid.getUserData(rowId, "LC_Id")), function () {
                    //LocCombo.setComboValue(invntGrid.getUserData(rowId, "LC_Id")); 
                });
                AssigneeCombo.load(preTally.Initialize.encryptURL("requisites/persons.php"), function () {
                    AssigneeCombo.setComboValue(invntGrid.getUserData(rowId, "INV_ItemAssignee"));
                });
                addInventoryForm.attachEvent("onButtonClick", function (id) {
                    if (id == "newInvItemSave") {
                        var InvFormVal = addInventoryForm.validate();
                        if (InvFormVal) {
                            addInventoryForm.send(preTally.Initialize.encryptURL("warehouse/newInventoryItem.php"), function (loader, response) {
                                addInventoryForm.clear();
                                dhtmlx.message(response);
                                invntGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listInventoryItem.php"), true);
                                InvWin.close();
                            });
                        }
                    }
                });

            });

        },manageInventoryPattern:function(){
             if (!dhxMiddleBlockTabs.cells("manageInvTypePattern")) {                
                dhxMiddleBlockTabs.addTab("mngInvTypePattern", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Inventory Pattern", 200);
                dhxMiddleBlockTabs.tabs("mngInvTypePattern").setActive();
                ManageInvPatternLayout = dhxMiddleBlockTabs.cells("mngInvTypePattern").attachLayout('3T');
                ManageInvPatternLayout.cells("a").setText("Inventory Management");
                ManageInvPatternLayout.cells("a").setHeight("100");
                ManageInvPatternLayout.cells("b").setText("Items");
                ManageInvPatternLayout.cells("c").setText("Selected Patterns");
                ManageInvPatternLayout.cells("a").hideHeader();
                ManageInvPatternForm=ManageInvPatternLayout.cells("a").attachForm();
                ManageInvPatternForm.load(preTally.Initialize.encryptURL("requisites/manageInvPatternItem.php"),function(){
                    var ManageTypeCombo=ManageInvPatternForm.getCombo("InvTypeCombo");
                    ManageTypeCombo.load(preTally.Initialize.encryptURL("requisites/inventorytypes.php"),function(){
                        
                    });
                    ManageTypeCombo.attachEvent("onChange",function(id){
                        var TypeComboVal=ManageTypeCombo.getSelectedValue();
                        ManageInvPatternItemGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listInventoryTypes.php&type=filt&typeid="+TypeComboVal));
                        ManageInvPatternSelecGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listInventoryTypes.php&type=sel&typeid="+TypeComboVal));
                    });
                });
                ManageInvPatternItemGrid=ManageInvPatternLayout.cells("b").attachGrid();
                ManageInvPatternItemGrid.setHeader("SlNo,Items,");
                ManageInvPatternItemGrid.setInitWidths("0,100,*");  
                ManageInvPatternItemGrid.setColTypes("ro,ro,ro");
                ManageInvPatternItemGrid.setColAlign("center,left,left");
                ManageInvPatternItemGrid.enableDragAndDrop(true);
                ManageInvPatternItemGrid.init();
                ManageInvPatternItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listInventoryTypes.php"));
                
                ManageInvPatternSelecGrid=ManageInvPatternLayout.cells("c").attachGrid();
                ManageInvPatternSelecGrid.setHeader("SlNo,Items,");
                ManageInvPatternSelecGrid.setInitWidths("0,100,*");  
                ManageInvPatternSelecGrid.setColTypes("ro,ro,ro");
                ManageInvPatternSelecGrid.setColAlign("center,left,left");
                ManageInvPatternSelecGrid.enableDragAndDrop(true);
                ManageInvPatternSelecGrid.init();
                ManageInvPatternLayout.cells("c").attachStatusBar({
                    text  : "<input type='button' value='SAVE' onclick='preTally.Inventory.InvPatternSave();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
            }else{
                dhxMiddleBlockTabs.tabs("mngInvTypePattern").setActive();
            }
        },InvPatternSave: function(){
            var TypeID = ManageInvPatternForm.getItemValue("InvTypeCombo");
            if(TypeID != 0) { alert("hello");
                var PatternIdList = ManageInvPatternSelecGrid.getAllRowIds().split(",");
                var patternResponse = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/manageInvPattern.php&'+"INVID="+TypeID),"id="+ JSON.stringify( PatternIdList , encodeURI(1)));
                if(patternResponse.xmlDoc.responseText != null) {
                    dhtmlx.message({text: patternResponse.xmlDoc.responseText});
                }
            }else {
                dhtmlx.message({text: "Select a Type to Set Pattern! Please Re-try ! "});
            }
            
        }
    };

})(jQuery, this);