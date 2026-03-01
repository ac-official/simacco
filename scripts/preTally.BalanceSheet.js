;
(function ($, window, undefined) {
    preTally.BalanceSheet = {
        menuNewMainhead: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewMainHead")) {
                dhxMiddleBlockTabs.addTab("menuNewMainHead", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Mainheads", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewMainHead");
                dhxMiddleBlockTabs.tabs("menuNewMainHead").setActive();
                var menuMainheadLayout = dhxMiddleBlockTabs.cells("menuNewMainHead").attachLayout('2U');
                menuMainheadLayout.cells("a").setText("New Main Head");
                menuMainheadLayout.cells("b").setText("List Main Head");
                menuMainheadLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Mainhead --------------------//
                listMainheadGrid = menuMainheadLayout.cells("b").attachGrid();
                listMainheadGrid.init();
                listMainheadGrid.loadXML(preTally.Initialize.encryptURL("requisites/listMainhead.php"), function () {
                    listMainheadGrid.attachEvent("onRowSelect", preTally.BalanceSheet.editMainhead);
                });
                //----------------- Attach Form for Mainhead --------------------//
                addMainheadForm = menuMainheadLayout.cells("a").attachForm();
                addMainheadForm.loadStruct(preTally.Initialize.encryptURL("requisites/newMainhead.php"), function () {
                    addMainheadForm.attachEvent("onButtonClick", function (name) {
                        var values = addMainheadForm.getFormData();
                        if (name == 'newMainheadValidate') {
                            var newMainhead = addMainheadForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'MH_Name', addMainheadForm, 'title');
                            if (newMainhead && CstmValidate) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addMainheadForm.send(preTally.Initialize.encryptURL('warehouse/newMainhead.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addMainheadForm.resetValidateCss();
                                        addMainheadForm.clear();
                                        addMainheadForm.setItemValue("MH_Id", 0);
                                    } else {
                                        response = 'Mainhead Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listMainheadGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listMainhead.php"), true, true, function () {
                                        //preTally.BalanceSheet.editMainhead();
                                    });
                                });
                            }
                        } else {
                            addMainheadForm.resetValidateCss();
                            addMainheadForm.clear();
                            addMainheadForm.setItemValue("MH_Id", 0);
                        }
                    });

                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewMainHead").setActive();
            }
        },
        editMainhead: function (rowId) {
            var MHId = rowId;
            //listMainheadGrid.attachEvent("onRowSelect", function(MHId) {
            addMainheadForm.setItemValue("MH_Id", MHId);
            addMainheadForm.setItemValue("MH_Name", listMainheadGrid.getUserData(MHId, "MH_Name"));
            addMainheadForm.setItemValue("MH_Comments", listMainheadGrid.getUserData(MHId, "MH_Comments"));
            addMainheadForm.setItemValue("MH_Status", listMainheadGrid.getUserData(MHId, "MH_Status"));
            addMainheadForm.setItemValue("MH_Type", listMainheadGrid.getUserData(MHId, "MH_Type"));
            //});
        },
        menuNewSubhead: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewSubhead")) {
                dhxMiddleBlockTabs.addTab("menuNewSubhead", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Sub Heads", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewSubhead");
                dhxMiddleBlockTabs.tabs("menuNewSubhead").setActive();

                var menuSubheadLayout = dhxMiddleBlockTabs.cells("menuNewSubhead").attachLayout('2U');
                menuSubheadLayout.cells("a").setText("New Sub Head");
                menuSubheadLayout.cells("b").setText("List Sub Head");
                menuSubheadLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Subhead --------------------//
                listSubheadGrid = menuSubheadLayout.cells("b").attachGrid();
                listSubheadGrid.init();
                listSubheadGrid.loadXML(preTally.Initialize.encryptURL("requisites/listSubhead.php"), function () {
                    listSubheadGrid.attachEvent("onRowSelect", preTally.BalanceSheet.editSubhead);
                });

                //----------------- Attach Form for Subhead --------------------//
                addSubheadForm = menuSubheadLayout.cells("a").attachForm();
                addSubheadForm.loadStruct(preTally.Initialize.encryptURL("requisites/newSubhead.php&r=" + new Date().getTime()), function () {
                    addSubheadForm.attachEvent("onButtonClick", function (name) {

                        var values = addSubheadForm.getFormData();


                        if (name == 'newSubheadValidate') {
                            var newSubhead = addSubheadForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'SH_Name', addSubheadForm, 'title');
                            if (newSubhead && CstmValidate) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addSubheadForm.send(preTally.Initialize.encryptURL('warehouse/newSubhead.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addSubheadForm.resetValidateCss();
                                        addSubheadForm.clear();
                                        addSubheadForm.setItemValue("SH_Id", 0);
                                    } else {
                                        response = 'Subhead Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listSubheadGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listSubhead.php"), true, true, function () {
                                        // preTally.BalanceSheet.editSubhead();
                                    });
                                });
                            }
                        } else {
                            addSubheadForm.resetValidateCss();
                            addSubheadForm.clear();
                            addSubheadForm.setItemValue("SH_Id", 0);
                        }

                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewSubhead").setActive();
            }
        },
        editSubhead: function (rowId) {
            var SHId = rowId;
            //listSubheadGrid.attachEvent("onRowSelect", function(SHId) {
            addSubheadForm.setItemValue("SH_Id", SHId);
            addSubheadForm.setItemValue("SH_Track", listSubheadGrid.getUserData(SHId, "SH_Track"));
            addSubheadForm.setItemValue("SH_Name", listSubheadGrid.getUserData(SHId, "SH_Name"));
            addSubheadForm.setItemValue("MH_Id", listSubheadGrid.getUserData(SHId, "MH_Id"));
            addSubheadForm.setItemValue("SH_Comments", listSubheadGrid.getUserData(SHId, "SH_Comments"));
            addSubheadForm.setItemValue("SH_Status", listSubheadGrid.getUserData(SHId, "SH_Status"));

            //});
        },
        menuManagePattern: function () {
            if (!dhxMiddleBlockTabs.cells("menuManagePattern")) {
                dhxMiddleBlockTabs.addTab("menuManagePattern", "<img src='images/icon/home_12.gif' style='margin-top:2px;' />&nbsp;&nbsp;Manage Patterns", 180);
                dhxMiddleBlockTabs.tabs("menuManagePattern").setActive();
                var menuManagePatternLayout = dhxMiddleBlockTabs.tabs("menuManagePattern").attachLayout("3T");

                menuManagePatternLayout.cells("b").setText("Custom Pattern Items");
                menuManagePatternLayout.cells("c").setText("Selected Pattern Items");

                menuManagePatternLayout.cells("a").setHeight(100);
                menuManagePatternLayout.cells("a").fixSize(true, true);
                menuManagePatternLayout.cells("a").hideHeader();

                managePatternForm = menuManagePatternLayout.cells("a").attachForm();
                managePatternForm.loadStruct(preTally.Initialize.encryptURL("requisites/managePattern.php&r=" + new Date().getTime()), function () {

                    SH_PICombo = managePatternForm.getCombo("SH_Id");
                    SH_PICombo.attachEvent("onChange", function (loader, response) {
                        listSelectedPatternItemGrid.clearAll();
                        listPatternItemGrid.clearAll();
                        //var params = "SH_Id = "+loader;
                        var params = "SH_Id=" + managePatternForm.getItemValue("SH_Id");
                        listSelectedPatternItemGrid.loadXML(preTally.Initialize.encryptURL('requisites/listSelectedPatternItems.php&' + params));
                        listPatternItemGrid.loadXML(preTally.Initialize.encryptURL('requisites/listPatternItems.php&' + params));

                    });
                });

                listPatternItemGrid = menuManagePatternLayout.cells('b').attachGrid();
                listPatternItemGrid.enableDragAndDrop(true);
                listPatternItemGrid.init();
                listPatternItemGrid.loadXML(preTally.Initialize.encryptURL('requisites/listPatternItems.php'));

                listSelectedPatternItemGrid = menuManagePatternLayout.cells('c').attachGrid();
                listSelectedPatternItemGrid.enableDragAndDrop(true);
                listSelectedPatternItemGrid.init();
                listSelectedPatternItemGrid.loadXML(preTally.Initialize.encryptURL('requisites/listSelectedPatternItems.php'));

                menuManagePatternLayout.cells("c").attachStatusBar({
                    text: "<input type='button' value='SAVE' onclick='preTally.BalanceSheet.addPatternSave();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });

            } else {
                dhxMiddleBlockTabs.tabs("menuManagePattern").setActive();
            }
        },
        addPatternSave: function () {
            var SHID = managePatternForm.getItemValue("SH_Id");
            if (SHID != 0) {
                var PatternIdList = listSelectedPatternItemGrid.getAllRowIds().split(",");
                var patternResponse = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/managePattern.php&' + "SHID=" + SHID + "&id=" + JSON.stringify(PatternIdList)), encodeURI(1));
                if (patternResponse.xmlDoc.responseText != null) {
                    dhtmlx.message({text: patternResponse.xmlDoc.responseText});
                }
            } else {
                dhtmlx.message({text: "Select a Subhead to Set Pattern! Please Re-try ! "});
            }

        },
        menuNewItem: function (ITId) {
            var itemVal = '';
            var ItemId = '';
            var mainHeadVal = '';
            var subHeadVal = '';
            var ItemCmts = '';
            var ItmStatus = '';
            if (ITId != 0)
            {
                delItmId = ITId;
                itemVal = listItemNoftGrid.getUserData(ITId, "IT_Name");
                ItemId = listItemNoftGrid.getUserData(ITId, "IT_Id");
                mainHeadVal = listItemNoftGrid.getUserData(ITId, "MH_Type");
                subHeadVal = listItemNoftGrid.getUserData(ITId, "SH_Id"); //alert(subHeadVal);
                ItemCmts = listItemNoftGrid.getUserData(ITId, "IT_Comments");
                ItmStatus = listItemNoftGrid.getUserData(ITId, "IT_Status");
            }

            if (!dhxMiddleBlockTabs.cells("menuNewItem")) {
                dhxMiddleBlockTabs.addTab("menuNewItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Items", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewItem");
                dhxMiddleBlockTabs.tabs("menuNewItem").setActive();


                var menuItemLayout = dhxMiddleBlockTabs.cells("menuNewItem").attachLayout('2U');
                menuItemLayout.cells("a").setText("New Item");
                menuItemLayout.cells("a").setWidth(400);
                menuItemLayout.cells("b").setText("List Item");
                itemButtonBar = menuItemLayout.cells("b").attachStatusBar({
//                    text  : "<label style='float:left;' >Total Items Selected : <b id='selItemCount' style='font-weight:bold;'></b></label><input type='button' value='SAVE' onclick='preTally.BalanceSheet.addToMyItemSave();' style='margin:5px 10px 5px 0; float:right;' />",
                    text: "<input type='button' value='SAVE' onclick='preTally.BalanceSheet.addPatternSave();' style='margin:5px 10px 5px 0; float:right;' />",
                    height: 35
                });
                menuItemLayout.cells("b").hideStatusBar();


                //----------------- Attach Grid for Item --------------------//
                listItemGrid = menuItemLayout.cells("b").attachGrid();
                listItemGrid.enableAutoWidth(true);
                listItemGrid.init();
                listItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemType.php"), function () {
                    listItemGrid.attachEvent("onRowSelect", preTally.Settings.editItem());
                });

                listItemGrid.attachEvent("onMouseOver", function (id, ind) {

                    if (listItemGrid.getUserData("", "OF_Id") != '1') {
                        if (ind == 3 && id == 1) {
                            var Cnt = listItemGrid.cells(id, ind).getValue();
                            this.cells(id, ind).cell.title = Cnt + ' Items in your list';
                            return false;
                        }
                        if (ind == 3 && id == 2) {
                            var Cnt = listItemGrid.cells(id, ind).getValue();
                            c1 = Cnt.split("/");
                            this.cells(id, ind).cell.title = c1[0] + ' of ' + c1[1] + ' Items Selected from Pretally pool.';
                            return false;
                        }
                    } else {
                        if (ind == 3 && id == 1) {
                            var Cnt = listItemGrid.cells(id, ind).getValue();
                            this.cells(id, ind).cell.title = Cnt + ' Items added by Others.';
                            return false;
                        }
                        if (ind == 3 && id == 2) {
                            var Cnt = listItemGrid.cells(id, ind).getValue();
                            this.cells(id, ind).cell.title = Cnt + ' Pretally Added Items.';
                            return false;
                        }
                    }
                });

                listItemGrid.attachEvent("onRowSelect", function (id) {

                    if (listItemGrid.doesRowExist(openITId) && id != 0) {
                        listItemGrid.cells(openITId, 0).close();
                    }
                    if (openITId == id) {
                        openITId = 0;
                    } else {
                        listItemGrid.cells(id, 0).open();
                        openITId = id;
                    }
                });


                listItemGrid.attachEvent("onSubRowOpen", function (id, state) {
                    mapsubGridItemOpen[id] = state;
                    if ((id == '1') && (state == true)) {
                        mapItemCustomSubGrid = listItemGrid.cells(1, 0).getSubGrid();

                        mapItemCustomSubGrid.attachEvent("onFilterEnd", function (elements) {
                            if (mapItemCustomSubGrid.getRowsNum() == 0) {
                                mapItemCustomSubGrid.addRow(0, ['', 'No Records Found...', '', '', ''], 0);
                                mapItemCustomSubGrid.setRowTextStyle(0, "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
                            }
                        });
                    }
                    if ((id == '2') && (state == true)) {
                        mapItemSubGrid = listItemGrid.cells(2, 0).getSubGrid();

                        mapItemSubGrid.attachEvent("onFilterEnd", function (elements) {
                            if (mapItemSubGrid.getRowsNum() == 0)
                                mapItemSubGrid.addRow(0, ['', 'No Records Found...', '', '', ''], 1);
                            mapItemSubGrid.setRowTextStyle(0, "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
                        });

                        if (listItemGrid.getUserData("", "OF_Id") != '1') {
                            listItemGrid.attachEvent("onSubGridLoaded", function (id, subgrid) {
                                mapItemSubGrid.attachEvent("onCheckbox", function (rId, cInd, state) {
                                    //preTally.BalanceSheet.addToMyItem(); // To Display Count
                                });
                                $('#selItemCount').html(mapItemSubGrid.getUserData("", "MP_CNT"));
                            });
                            menuItemLayout.cells("b").showStatusBar();
                            //preTally.BalanceSheet.addToMyItem();     // To Display Count


                        }
                    }

                    /*if((id == '1') && (state == true)){
                     listItemGrid.attachEvent("onSubGridLoaded",function(id,subgrid){
                     mapItemCustomSubGrid = listItemGrid.cells(1,0).getSubGrid();                   
                     }); 
                     }
                     if((id == '2') && (state == true) && (listItemGrid.getUserData("","OF_Id") != '1')) {
                     menuItemLayout.cells("b").showStatusBar();
                     
                     listItemGrid.attachEvent("onSubGridLoaded",function(id,subgrid){
                     mapItemSubGrid = listItemGrid.cells(2,0).getSubGrid();
                     preTally.BalanceSheet.addToMyItem();
                     
                     mapItemSubGrid.attachEvent("onCheckbox", function(rId,cInd,state){
                     preTally.BalanceSheet.addToMyItem();
                     });                   
                     });                      
                     }*/
                    if ((id == '2') && (state == false) && (listItemGrid.getUserData("", "OF_Id") != '1')) {
                        menuItemLayout.cells("b").hideStatusBar();
                    }
                });



                //----------------- Attach Form for Item --------------------//
                addItemForm = menuItemLayout.cells("a").attachForm();
                addItemForm.loadStruct(preTally.Initialize.encryptURL("requisites/newItem.php&r=" + new Date().getTime()), function () {

                    SH_Combo = addItemForm.getCombo("SH_Id");
                    MH_Combo = addItemForm.getCombo("MH_Type");
                    addItemForm.getInput("IT_Comments").setAttribute("placeholder", "Enter Item Details Here for Future Use.");

                    if (ITId != 0)
                    {
                        addItemForm.setItemValue("IT_Name", itemVal);
                        addItemForm.setItemValue("IT_Id", ItemId);
                        addItemForm.setItemValue("IT_SpId", ItemId);
                        addItemForm.setItemValue("IT_Comments", ItemCmts);
                        addItemForm.setItemValue("IT_Status", ItmStatus);
                        addItemForm.setItemValue("US_Id", listItemNoftGrid.getUserData(ITId, "US_Id"));
                        SH_Combo.clearAll();
                        addItemForm.setItemValue("MH_Type", mainHeadVal);
                        SHId = listItemNoftGrid.getUserData(ITId, "SH_Id");
                        var params = "type=" + mainHeadVal + "&SHId=" + SHId;
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function (xml) {
                            SH_Combo.load(xml.xmlDoc.responseText);
                        });
                        var params = "type=" + mainHeadVal;
                        SH_Combo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/subheads.php&" + params));
                    } else {
                        var MhTyp = addItemForm.getItemValue("MH_Type");
                        var params = "type=" + MhTyp;
                        SH_Combo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/subheads.php&" + params));
                    }

                    MH_Combo.attachEvent("onChange", function (loader, response) {
                        var MhTyp = addItemForm.getItemValue("MH_Type");
                        SH_Combo.clearAll();
                        SH_Combo.setComboText("");
                        SH_Combo.setComboValue("");
                        var params = "type=" + MhTyp;
                        SH_Combo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/subheads.php&" + params));
                        addItemForm.setItemValue("SH_Id", subHeadVal);
                        //SH_Combo.load("requisites/subheads.php?"+encrypt(params));
                    });

                    addItemForm.attachEvent("onButtonClick", function (name) {

                        var values = addItemForm.getFormData();

                        if (name == 'newItemValidate') {
                            var newItem = addItemForm.validate();
                            var CstmValidate = preTally.Validate.Validate(values, 'IT_Name', addItemForm, 'title');
                            if (newItem && CstmValidate) {
                                var ItId = addItemForm.getItemValue("IT_SpId");

                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addItemForm.send(preTally.Initialize.encryptURL('warehouse/newItem.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail' && response != 'Invalid') {
                                        addItemForm.resetValidateCss();
                                        addItemForm.clear();
                                        addItemForm.setItemValue("IT_Id", 0);
                                        SH_Combo.clearAll();
                                        SH_Combo.setComboText("");
                                        SH_Combo.setComboValue("");

                                        if (ItId != 0) {
                                            var itm_rwID = 1;
                                            listItemNoftGrid.deleteRow(delItmId);
                                            listItemNoftGrid.forEachRow(function (id) {
                                                listItemNoftGrid.cells(id, 0).setValue(itm_rwID);
                                                itm_rwID++;
                                            });
                                            dhxMiddleBlockTabs.tabs("menuNotfItem").setActive();
                                        }
                                    } else if (response == 'Invalid') {
                                        response = 'Invalid Subhead. Please Re-Try';
                                    } else {
                                        response = 'Item Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listItemGrid.clearAll();
                                    listItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemType.php"), function () {
                                        if (mapsubGridItemOpen[1] == true)
                                            listItemGrid.cells("1", 0).open();
                                        if (mapsubGridItemOpen[2] == true)
                                            listItemGrid.cells("2", 0).open();
                                    });
                                });
                            }
                        } else {
                            addItemForm.resetValidateCss();
                            addItemForm.clear();
                            addItemForm.setItemValue("IT_Id", 0);
                            SH_Combo.clearAll();
                            SH_Combo.setComboText("");
                            SH_Combo.setComboValue("");
                        }
                    });

                });
            } else {
                if (ITId != 0)
                {
                    addItemForm.setItemValue("IT_Name", itemVal);
                    addItemForm.setItemValue("IT_Id", ItemId);
                    addItemForm.setItemValue("MH_Type", mainHeadVal);
                    addItemForm.setItemValue("IT_Comments", ItemCmts);
                    addItemForm.setItemValue("US_Id", listItemNoftGrid.getUserData(ITId, "US_Id"));
                    SH_Combo.clearAll();
                    SH_Combo.setComboText("");
                    SH_Combo.setComboValue("");
                    SHId = listItemNoftGrid.getUserData(ITId, "SH_Id");
                    var params = "type=" + mainHeadVal + "&SHId=" + SHId;
                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function (xml) {
                        SH_Combo.load(xml.xmlDoc.responseText);
                    });
                    addItemForm.setItemValue("IT_SpId", ItemId);
                }
                dhxMiddleBlockTabs.tabs("menuNewItem").setActive();
            }
        },
        editItem: function (rowId) {
            var ITId = rowId;

            if (this.getUserData("", "OF_Id") == '1')
                var gridObj = mapItemSubGrid;
            else
                var gridObj = mapItemCustomSubGrid;
            SH_Combo.clearAll();
            addItemForm.setItemValue("IT_Id", ITId);
            addItemForm.setItemValue("IT_Name", gridObj.getUserData(ITId, "IT_Name"));
            addItemForm.setItemValue("IT_Comments", gridObj.getUserData(ITId, "IT_Comments"));
            addItemForm.setItemValue("IT_Status", gridObj.getUserData(ITId, "IT_Status"));
            addItemForm.setItemValue("IT_Business", gridObj.getUserData(ITId, "IT_Business"));
            addItemForm.setItemValue("MH_Type", gridObj.getUserData(ITId, "MH_Type"));
            MTypeVal = gridObj.getUserData(ITId, "MH_Type");
            SHId = gridObj.getUserData(ITId, "SH_Id");
            var params = "type=" + MTypeVal + "&SHId=" + SHId;
            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function (xml) {
                SH_Combo.load(xml.xmlDoc.responseText);
            });

        },
        addToMyItem: function () {
            var chkd = 0;
            var count = mapItemSubGrid.getRowsNum();
            for (var i = 0; i < count; i++) {
                var ItmValue = mapItemSubGrid.cellByIndex(i, 4).getValue();
                if (ItmValue == 1) {
                    chkd++;
                }
                $('#selItemCount').html(chkd);
            }
        },
        addToMyItemSave: function () {
//            mapItemArray = [];
//            var count = mapItemSubGrid.getRowsNum(); 
//            for(var i=0;i<count;i++){ 
//                var ItmValue = mapItemSubGrid.cellByIndex(i,4).getValue();
//                if(ItmValue == 1) {
//                    mapItemArray.push(mapItemSubGrid.getRowId(i));    
//                }
//            }
            var ItemMapList = mapItemSubGrid.getCheckedRows(4).split(",");
            var itemResponseMap = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/mapItemCompany.php&c=' + JSON.stringify(ItemMapList)), encodeURI(1));
            if (itemResponseMap.xmlDoc.responseText != null) {
                dhtmlx.message({text: itemResponseMap.xmlDoc.responseText});
            }

        },
        aliasItem: function (ITId) {
            //alert('alias');
            //console.log(mapItemCustomSubGrid.getUserData(ITId, "IT_Name"));
            dhtmlx.confirm({
                title: "Add Item To PreTally Item Pool",
                ok: "Yes", cancel: "No",
                text: "Confirm Add <b>" + mapItemCustomSubGrid.getUserData(ITId, "IT_Name") + "</b> To PreTally Item Pool",
                callback: function (result) {
                    if (result == true) {
                        var itemResponseMap = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/mapItemPreTally.php&item=' + ITId), encodeURI(1));
                        if (itemResponseMap.xmlDoc.responseText != null) {
                            dhtmlx.message({text: itemResponseMap.xmlDoc.responseText});
                            listItemGrid.clearAll();
                            listItemGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemType.php"), function () {
                                if (mapsubGridItemOpen[1] == true)
                                    listItemGrid.cells("1", 0).open();
                                if (mapsubGridItemOpen[2] == true)
                                    listItemGrid.cells("2", 0).open();
                            });
                        }
                    }
                }
            });
        },
        saveBalanceSheetItem: function () {

            dhxNewBalSheetForm.attachEvent("onButtonClick", function (name) {

                dhxNewBalSheetForm.attachEvent("onValidateError", preTally.Settings.validateCss);
                var newBalSheetItem = dhxNewBalSheetForm.validate();
                var values = dhxNewBalSheetForm.getFormData();
                var CstmValidate = preTally.Validate.Validate(values, 'IT_Id', dhxNewBalSheetForm, 'combo');
                if (CstmValidate == false) {
                    dhxNewBalSheetForm.setValidateCss('IT_Id', CstmValidate, 'validate_red');
                    return false
                }

                if (newBalSheetItem) {
                    preTally.Settings.progressOn(true, dhxLayout, null);
//                    descriptionText = descCombo.getComboText();
//                    newItemText = prtNewCombo.getComboText();
                    dhxNewBalSheetForm.setItemValue("IT_Name", prtNewCombo.getComboText());
                    dhxNewBalSheetForm.setItemValue("DS_Description", descCombo.getComboText());

                    if (!dhxNewBalSheetForm.isItemHidden("BS_Track"))
                    {
                        dhxNewBalSheetForm.setItemValue("TR_Track", trackCombo.getComboText());
                        trackID = dhxNewBalSheetForm.getItemValue("TR_Track");
                    }

                    dhxNewBalSheetForm.send(preTally.Initialize.encryptURL('warehouse/newBalSheet.php'), function (loader, response) {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                        if (response == "track_fail") {
                            dhtmlx.message({text: "Track ID is already suspended"});
                            dhxNewBalSheetForm.setValidateCss("BS_Track", false, 'validate_red');
                        }
                        if (response == "track_date") {
                            dhtmlx.message({text: "Track Date not Allowed."});
                            dhxNewBalSheetForm.setValidateCss("BS_Date", false, 'validate_red');
                        }
                        if (response == "item_fail") {
                            dhtmlx.alert({
                                title: "Item-Error",
                                type: "alert-error",
                                text: "Adding new item restricted!! Contact Accounts Head"
                            });
                            dhxNewBalSheetForm.setValidateCss("BS_Track", false, 'validate_red');
                        } else if (response == "invalid") {
                            dhtmlx.message({text: "Please enter a valid track ID"});
                        } else {
                            dhxNewBalSheetForm.resetValidateCss();
                            prtNewCombo.clearAll();
                            descCombo.clearAll();

                            var MH_Type = dhxNewBalSheetForm.getItemValue('MH_Type');
                            var params = "type=" + MH_Type;
                            prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItems.php&" + params));

                            prtNewCombo.setComboText("");
                            prtNewCombo.setComboValue("");
                            descCombo.setComboText('');
                            descCombo.setComboValue('');
                            dhxNewBalSheetForm.setItemValue("BS_Track", "");
                            if (!dhxNewBalSheetForm.isItemHidden("BS_Track"))
                            {
                                trackCombo.setComboValue('');
                                trackCombo.setComboText('');
                                trackCombo.clearAll();
                                dhxNewBalSheetForm.hideItem("BS_Track");
                                dhxNewBalSheetForm.setRequired("BS_Track", false);
                                dhxNewBalSheetForm.setRequired("BS_PayMode", true)
                                dhxNewBalSheetForm.showItem("BS_PayMode");
                            }
                            if (!dhxNewBalSheetForm.isItemHidden("BS_PayBankAC")) {
                                dhxNewBalSheetForm.setRequired("BS_PayBankAC", false)
                                dhxNewBalSheetForm.getCombo("BS_PayBankAC").setComboValue('');
                                dhxNewBalSheetForm.getCombo("BS_PayBankAC").setComboText('');
                                dhxNewBalSheetForm.hideItem("BS_PayBankAC");
                            }
                            dhxNewBalSheetForm.getCombo("BS_PayMode").setComboValue('1');
                            dhxNewBalSheetForm.setItemValue("BS_Amount", "");
                            dhxNewBalSheetForm.setItemValue("BS_Date", "");//28-11-2024

                            dhtmlx.message({text: response});
//                            dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function() {},"json");                            
                            if (dhxGridBalSheet.doesRowExist("0"))
                                dhxGridBalSheet.deleteRow('0');
                            dhxGridBalSheet.updateFromJSON(preTally.Initialize.encryptURL("requisites/listBSItems.php"), true, true, function () {
                                dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");
                            });
                        }
                    });

                }
            });

        },
        onRowBalSheet: function (gridId) {
            dhxSubGridBalSheet[gridId].attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                var amountColId, quantityColId, priceColId, dataAmount;
                amountColId = dhxSubGridBalSheet[gridId].getColIndexById("BS_Amount");
                quantityColId = dhxSubGridBalSheet[gridId].getColIndexById("BS_Quantity");
                priceColId = dhxSubGridBalSheet[gridId].getColIndexById("BS_Price");
                dataAmount = dhxSubGridBalSheet[gridId].cells(rId, amountColId).getValue("dataAmount");

                if (nValue) {
                    if (priceColId == cInd) {
                        quantity = (dataAmount / nValue);
                        dhxSubGridBalSheet[gridId].cells(rId, quantityColId).setValue(quantity);
                    } else if (quantityColId == cInd) {
                        unitprice = (dataAmount / nValue);
                        dhxSubGridBalSheet[gridId].cells(rId, priceColId).setValue(unitprice);
                    }
                }
                return true;
            });
        },
        editBalSheetItems: function (rId) {
            $('.saveBalSheetItem' + rId).click(function (e) {
                dhtmlx.message.hide("Msg");
                BSId = $(this).attr('BSId');
                var SHID = $(this).attr('SHID');

                updateBS['SH_' + SHID].sendData(BSId);

            });

            updateBS[rId].attachEvent("onAfterUpdateFinish", function () {
                dhtmlx.message({text: 'Accounts Entry Item Details Updated Successfully', id: 'Msg'});

                if (dhxSubGridBalSheet[rId].getRowsNum() == 0) {
                    var main_rwID = 1;
                    dhxGridBalSheet.deleteRow(rId);
                    dhxGridBalSheet.forEachRow(function (id) {
                        dhxGridBalSheet.cells(id, 1).setValue(main_rwID);
                        main_rwID++;
                    });
                } else {
                    var BSItemCount = (dhxGridBalSheet.cells(rId, 3).getValue() - 1);
                    dhxGridBalSheet.cells(rId, 3).setValue(BSItemCount);

                    var sub_rwID = 1;
                    dhxSubGridBalSheet[rId].forEachRow(function (id) {
                        dhxSubGridBalSheet[rId].cells(id, 1).setValue(sub_rwID);
                        sub_rwID++;
                    });
                }
            });
        },
        addBalSheetItem: function (BsId) {
            BSForm[BsId].attachEvent("onButtonClick", function (name) {
                if (name == 'balSheetFormSave') {

                    BSForm[BsId].setValidation('BS_Price', 'ValidNumeric');
                    BSForm[BsId].setValidation('BS_Amount', 'ValidNumeric,NotEmpty');
                    BSForm[BsId].setValidation('BS_Quantity', 'ValidNumeric');
                    BSForm[BsId].setValidation('BS_StaffId', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_PrchsdFor', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_User', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_AprovlGvnBy', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_AprovlTknBy', 'ValidInteger');
                    BSForm[BsId].setValidation('CN_Id', 'ValidInteger');
                    BSForm[BsId].setValidation('ST_Id', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_Persons', 'ValidInteger');
                    BSForm[BsId].setValidation('LC_Id', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_PaidBy', 'ValidInteger');
                    BSForm[BsId].setValidation('BS_IEByUS', 'ValidInteger,NotEmpty');
                    BSForm[BsId].setValidation('BS_IEByLC', 'ValidInteger,NotEmpty');

                    var patmentMode = BSForm[BsId].getCombo("PM_Id");
                    var pmMode = patmentMode.getSelectedValue();
                    if (pmMode == 2) {
//                            BSForm[BsId].setValidation('BS_PayType', 'NotEmpty'); 
//                            BSForm[BsId].setValidation('BS_PayType', 'NotEmpty'); 
                        BSForm[BsId].setValidation('BNK_Id', 'NotEmpty');
                        BSForm[BsId].setValidation('BB_Id', 'NotEmpty');
                        BSForm[BsId].setValidation('BA_Id', 'NotEmpty');
                        BSForm[BsId].setValidation('BS_PayersBank', 'NotEmpty');
                        BSForm[BsId].setValidation('BS_PayersChQ', 'NotEmpty');

                        if (BSForm[BsId].isItem("BS_PayType")) {
                            var patmentType = BSForm[BsId].getCombo("BS_PayType");
                            var pmType = patmentType.getSelectedValue();
                            if (pmType == '2') {
                                BSForm[BsId].setValidation('CHQ_Number', 'NotEmpty');
                            }
                            if (pmType == '3') {
                                // BSForm[BsId].setValidation('CHQ_Number', 'null'); 
                                BSForm[BsId].clearValidation('CHQ_Number');
                                BSForm[BsId].clearValidation('BS_PayersChQ');
                            }
                        }
                    } else {
//                            BSForm[BsId].setValidation('BS_PayType', 'null'); 
//                            BSForm[BsId].setValidation('BS_PayType', 'null'); 
//                            BSForm[BsId].setValidation('BNK_Id', 'null'); 
//                            BSForm[BsId].setValidation('BB_Id', 'null'); 
//                            BSForm[BsId].setValidation('BA_Id', 'null'); 
//                            BSForm[BsId].setValidation('CHQ_Number', 'null'); 
//                            BSForm[BsId].setValidation('BS_PayersBank', 'null'); 
//                            BSForm[BsId].setValidation('BS_PayersChQ', 'null'); 
                        BSForm[BsId].clearValidation('BNK_Id');
                        BSForm[BsId].clearValidation('BB_Id');
                        BSForm[BsId].clearValidation('BA_Id');
                        BSForm[BsId].clearValidation('CHQ_Number');
                        BSForm[BsId].clearValidation('BS_PayersBank');
                        BSForm[BsId].clearValidation('BS_PayersChQ');
                    }


                    var newBSEntry = BSForm[BsId].validate();

                    if (BSForm[BsId].getItemValue("BS_PettyCashRefId") != 0) {

                        if (Number(BSForm[BsId].getItemValue("BS_Amount")) > Number(BSForm[BsId].getItemValue("PettyCashAmount"))) {
                            dhtmlx.message({text: ' Amount exceeds petty cash Amount. Please Verify'});
                            BSForm[BsId].setValidateCss('BS_Amount', false);
                            return false;
                        }
                    }

                    if (newBSEntry) {
                        preTally.Settings.progressOn(true, dhxLayout, null);

                        if (BSForm[BsId].getUserData("CHQ_Number", "cId") == BSForm[BsId].getItemValue("CHQ_Number")) {
                            BSForm[BsId].setItemValue("updateType", "rpt");
                        }
                        var params = "bsId=" + BsId;
                        BSForm[BsId].send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&' + params), function (loader, response) {
                            preTally.Settings.progressOff(true, dhxLayout, null);

                            if (response != 1 && response != 2 && response != 'pettycash') {
                                dhxGridBalSheet.cells(BsId, 0).close();
                                openBSID = 0;
                                var sh_id = BSForm[BsId].getItemValue("SH_Id");
                                var stats = BSForm[BsId].getItemValue("H_Stats");
                                if ((sh_id != 58) && (sh_id != 59) && (stats == 1)) {
                                    dhxGridBalSheet.deleteRow(BsId); //alert(BsId);
                                } else {
//                                                    dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function() {},"json");
                                    dhxGridBalSheet.updateFromJSON(preTally.Initialize.encryptURL("requisites/listBSItems.php"), true, true, function () {
                                        dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");
                                    });
                                }
                                if (dhxGridBalSheet.getRowsNum() == 0) {
                                    dhxGridBalSheet.addRow(0, ['', '', 'No records found', '', ''], 0);
                                    dhxGridBalSheet.setRowTextStyle('0', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                                }
                                //dhxGridSavedBSItems.loadXML("requisites/listSavedBSItems.php"); 
                                var main_rwID = 1;
                                dhxGridBalSheet.forEachRow(function (id) { //alert(id);
                                    dhxGridBalSheet.cells(id, 1).setValue(main_rwID);
                                    main_rwID++;
                                });
                            } else if (response == 'pettycash') {
                                BSForm[BsId].setValidateCss('BS_Amount', false);
                                response = 'Petty Cash Amount is incorrect. Please Verify.';
                            } else if (response == '2') {
                                response = 'Edit is restricted for this entry.';
                            } else {
                                BSForm[BsId].setValidateCss('CHQ_Number', false);
                                response = 'Invalid Entry ! Please Re-Try !';
                            }

                            dhtmlx.message({text: response});

                        });
                    }
                } else {
                    dhtmlx.confirm({
                        title: "Close",
                        type: "confirm",
                        text: "Are you sure you want to do it?",
                        callback: function (id) {
                            if (id == true) {
                                BSForm[BsId].resetValidateCss();
                                // BSForm[BsId].clear();
                                //BSForm[BsId].setItemValue("BS_Id", 0);

                                dhxGridBalSheet.cells(BsId, 0).close();
                                openBSID = 0;
                            }
                            //dhtmlx.confirm("Test confirm");
                        }
                    });


                }
            });

        },
        BSFormCeterAlign: function (BSFormHeight, margin) {             
            if (margin == 0)
                $('.newBalSheet').closest('.form_base').css({"margin": "0 0"});
            margin += 2;
            var BSHeightNow = $('.form_base').height();
            
            if (BSFormHeight == BSHeightNow) { 
                $('.newBalSheet').closest('.form_base').css({"margin": "0 " + margin + "px"});
                preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight, margin);
            } else {
                if (BSFormHeight > BSHeightNow){
                    var WinWidth =dhxMiddleBlockLayout.cells("a").getWidth();
                    var BSWidthNow = $('.form_base').width();
                    margin=(WinWidth-BSWidthNow)*0.45;
                }
                $('.newBalSheet').closest('.form_base').css({"margin": "0 " + (margin - 15) + "px"});
            }
        },
        menuNewBalSheet: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewBalSheet")) {

                dhxMiddleBlockTabs.addTab("menuNewBalSheet", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Accounts Entry", 150);
                dhxMiddleBlockTabs.tabs("menuNewBalSheet").setActive();
                dhxMiddleBlockLayout = dhxMiddleBlockTabs.cells("menuNewBalSheet").attachLayout("2E");

                dhxMiddleBlockLayout.cells("a").setHeight(90);
                dhxMiddleBlockLayout.cells("a").fixSize(true, true);
                dhxMiddleBlockLayout.cells("a").hideHeader();
                dhxMiddleBlockLayout.cells("b").hideHeader();
                /*ptBalSheetTab = dhxMiddleBlockLayout.cells("c").attachTabbar();
                 
                 ptBalSheetTab.setSkin('dhx_skyblue');
                 //ptBalSheetTab.setImagePath("assets/tabbar/codebase/imgs/");
                 ptBalSheetTab.addTab("a1", "New Item", "100px");
                 ptBalSheetTab.addTab("a2", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Week", "100px");
                 ptBalSheetTab.addTab("a3", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Month", "100px");
                 ptBalSheetTab.addTab("a4", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Year", "100px");
                 //ptBalSheetTab.setTabActive("a1");
                 ptBalSheetTab.tabs("a1").setActive();*/

                var NBS_Width = dhxMiddleBlockLayout.cells("a").getWidth();
                //dhxNewBalSheetForm.setItemWidth("IT_Id", 170);
                //dhxNewBalSheetForm.setItemWidth("BS_Description", 170);
                //var tmp = dhxNewBalSheetForm.getItemWidth("MH_Type");
                //var tmp = dhxNewBalSheetForm.getItemWidth('MH_Type');
                //console.log(tmp);

                dhxNewBalSheetForm = dhxMiddleBlockLayout.cells("a").attachForm();
                dhxNewBalSheetForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBalSheet.php&w=" + NBS_Width + "&x=" + new Date().getTime()), function () {
                    //dhxNewBalSheetForm.setFontSize(14+'px');
                    if (!dhxNewBalSheetForm.isItemHidden("BS_Track")) {
                        dhxNewBalSheetForm.hideItem("BS_Track");
                        dhxNewBalSheetForm.setRequired("BS_Track", false)
                    }
                    if (!dhxNewBalSheetForm.isItemHidden("BS_PayBankAC")) {
                        dhxNewBalSheetForm.hideItem("BS_PayBankAC");
                        dhxNewBalSheetForm.setRequired("BS_PayBankAC", false)
                    }
                    dhxNewBalSheetForm.hideItem("BS_BranchTo"); //28-05-2025
                    var todayd        = new Date(); //09-04-2025
                    todayd.setDate(todayd.getDate() - 1); //09-04-2025
                    dhxNewBalSheetForm.getCalendar("BS_Date").setSensitiveRange(todayd, new Date()); //28-11-24
//                    $( ".dhxform_base" ).addClass( "myClass yourClass" );
                    $(".newBalSheet").parent().addClass("form_base");

                    BSFormHeight = $('.form_base').height();
                    preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight, 0);

                    var IT_Note = 'NAME OF THE EXPENSE';
                    var DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';

                    prtNewCombo = dhxNewBalSheetForm.getCombo("IT_Id");
                    descCombo = dhxNewBalSheetForm.getCombo("BS_Description");
                    paymodeCombo = dhxNewBalSheetForm.getCombo("BS_PayMode");
                    bankaccCombo = dhxNewBalSheetForm.getCombo("BS_PayBankAC");
                    bankaccCombo.load(preTally.Initialize.encryptURL("requisites/combo_BankAccts.php"));
                    preTally.Settings.comboSelectPreload(descCombo, "desccombo");
                    preTally.Settings.comboSelectPreload(prtNewCombo, "itemcombo");
                    prtNewCombo.setOptionWidth(500);
                    descCombo.setOptionWidth(400);                    
                    bankaccCombo.setOptionWidth(250);
                    bankaccCombo.allowFreeText(false);
                    //prtNewCombo.readonly("Enable");
                    prtNewCombo.enableFilteringMode('between');
                    bankaccCombo.enableFilteringMode('between');
                    descCombo.enableFilteringMode('between');
                    paymodeCombo.attachEvent("onChange", function () {
                        if (paymodeCombo.getSelectedValue() == 2) {
                            dhxNewBalSheetForm.showItem("BS_PayBankAC");
                            dhxNewBalSheetForm.setRequired("BS_PayBankAC", true)
                        } else {
                            dhxNewBalSheetForm.hideItem("BS_PayBankAC");
                            dhxNewBalSheetForm.setRequired("BS_PayBankAC", false)
                        }
                       BSFormHeight = $('.form_base').height();
                       preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight, 0);
                    });
                    var params = "";
                    prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItems.php&type=2"));

                    dhxNewBalSheetForm.attachEvent("onChange", function (loader, response) {
                        if (loader == 'MH_Type') {
                            prtNewCombo.clearAll();
                            prtNewCombo.setComboText("");
                            prtNewCombo.setComboValue("");
                            descCombo.clearAll();
                            descCombo.setComboText("");
                            descCombo.setComboValue("");
                            prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItems.php&type=" + response));
                            if (response == 1) {
                                IT_Note = 'NAME OF THE INCOME';
                                DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                            } else {
                                IT_Note = 'NAME OF THE EXPENSE';
                                DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                            }
                            dhxNewBalSheetForm.setNote('IT_Id', {text: IT_Note});
                            dhxNewBalSheetForm.setNote('BS_Description', {text: DS_Note});
                            dhxNewBalSheetForm.getCombo("BS_PayMode").setComboValue('1');

                        }

                    });

                    //descCombo = dhxNewBalSheetForm.getCombo("BS_Description");

                    prtNewCombo.attachEvent("onChange", function () {
                        descCombo.clearAll();
                        descCombo.setComboText('');
                        var Itm_id = prtNewCombo.getSelectedValue();
                        if (!isNaN(Itm_id) && (Itm_id != 0)) {
                            $.ajax({
                                url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&IT_Id=" + Itm_id+"&type=BS")
                            }).done(function (data) {
                                var trackFlag   = 0;
                                var BussFlag    = 0;
                                var branFlag    = 0; //28-05-2025
                                if (data != 'null') {
                                    var SubheadData = JSON.parse(data);
                                    trackFlag   = SubheadData.SH_Track;
                                    BussFlag    = SubheadData.IT_Business;
                                    branFlag    = SubheadData.IT_OtherUser; //28-05-2025
                                }
                                //descCombo.enableFilteringMode(false);
                                descCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&IT_Id=" + Itm_id + "&filter=1&type=init"), function () {
                                });
                                descCombo.enableFilteringMode("between", preTally.Initialize.encryptURL("requisites/descriptions.php&IT_Id=" + Itm_id + "&filter=1&type=init"), function () {
                                });
                                dhxNewBalSheetForm.setNote('IT_Id', {text: IT_Note});
                                dhxNewBalSheetForm.setNote('BS_Description', {text: DS_Note});
                                if (!dhxNewBalSheetForm.isItemHidden("BS_Track")) {
                                    dhxNewBalSheetForm.hideItem("BS_Track");
                                    dhxNewBalSheetForm.setRequired("BS_Track", false)
                                    dhxNewBalSheetForm.showItem("BS_PayMode");
                                    dhxNewBalSheetForm.setRequired("BS_PayMode", true);
                                    dhxNewBalSheetForm.getCombo("BS_PayMode").setComboValue('1');
                                }
                                // set the login user branch id once change the item name 29-05-2025
                                dhxNewBalSheetForm.setItemValue("BS_BranchTo", unescape(JGG1P3bDnUSDL22Mui7KzYjj28UjPdWxCCtGkJSHeuo)); 
                                if ( branFlag == '1' ) { // new section added 28-05-25
                                    dhxNewBalSheetForm.showItem("BS_BranchTo");
                                } else {
                                    dhxNewBalSheetForm.hideItem("BS_BranchTo");
                                }
                                if (trackFlag == '1') {
                                    var filtrInterval;
                                    dhxNewBalSheetForm.showItem("BS_Track");
                                    trackCombo = dhxNewBalSheetForm.getCombo("BS_Track");
                                    trackCombo.setComboValue('');
                                    trackCombo.setComboText('');
                                    dhxNewBalSheetForm.setRequired("BS_Track", true);
                                    if (BussFlag == '1') {
                                        dhxNewBalSheetForm.hideItem("BS_PayMode");
                                        dhxNewBalSheetForm.setRequired("BS_PayMode", false);
                                        dhxNewBalSheetForm.hideItem("BS_PayBankAC");
                                        dhxNewBalSheetForm.setRequired("BS_PayBankAC", false);
                                    } else {
                                        dhxNewBalSheetForm.showItem("BS_PayMode");
                                        dhxNewBalSheetForm.setRequired("BS_PayMode", true);
                                        dhxNewBalSheetForm.getCombo("BS_PayMode").setComboValue('1');
                                    }                                   


                                    $(".trackCombo").keyup(function (value) {
                                        //var mask = this.value;alert(mask);
                                        if (filtrInterval)
                                            clearInterval(filtrInterval);

                                        filtrInterval = setInterval(function () {
                                            trackCombo.clearAll();
                                            //trackCombo.enableFilteringMode("between");
                                            trackCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&mask=" + trackCombo.getComboText()), function () {
                                                trackCombo.openSelect();
                                            });

                                            clearInterval(filtrInterval);
                                        }, 500);

                                    });
                                }
                                BSFormHeight = $('.form_base').height();
                                preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight, 0);
                            });
                        }
                    });
                    preTally.BalanceSheet.saveBalanceSheetItem();


                });

                //----------------- Attach Grid for Accounts Entry in Main Block --------------------//
                //dhxGridBalSheet = ptBalSheetTab.cells("a1").attachGrid();
                dhxGridBalSheet = dhxMiddleBlockLayout.cells("b").attachGrid();
                dhxGridBalSheet.attachEvent("onXLE", function () {
                    dhxLayout.progressOff();
                });

                dhxGridBalSheet.setImagePath("assets/grid/codebase/imgs/");
                dhxGridBalSheet.init();
                //dhxGridBalSheet.splitAt(2);
                //dhxGridBalSheet.enableSmartRendering(true); 


                dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function () {

                    dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");

                    dhxGridBalSheet.enableRowsHover(true, "hover");

                    dhxGridBalSheet.attachEvent("onRowSelect", function (id) {

                        if (dhxGridBalSheet.doesRowExist(openBSID) && id != 0) {
                            dhxGridBalSheet.cells(openBSID, 0).close();
                            //dhxGridBalSheet.setRowTextNormal(openBSID);
                            dhxGridBalSheet.setRowTextStyle(openBSID, "padding-top: 3px;");
                        }

                        if (openBSID == id) {
                            openBSID = 0;
                        } else {
                            dhxGridBalSheet.cells(id, 0).open();
                            //dhxGridBalSheet.setRowTextBold(id);
                            dhxGridBalSheet.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                            openBSID = id;
                        }
                        dhxGridBalSheet.clearSelection();
                    });

                    dhxGridBalSheet.attachEvent("onSubRowOpen", function (id, state) {
                        if (state == true) {
                            dhxGridBalSheet.showRow(id);
                            dhxGridBalSheet.setRowTextBold(id);
                            dhxGridBalSheet.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                        } else {
                            dhxGridBalSheet.setRowTextNormal(id);
                            dhxGridBalSheet.setRowTextStyle(id, "padding-top: 3px;");
                        }

                    });

                    dhxGridBalSheet.attachEvent("onSubAjaxLoad", function (id, state) {

                        $(".BB_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Select Bank Before to Select Bank Branch');
                        $(".BA_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Select Branch Name Before to Select Account');
                        $(".CHQ_NumberCombo").find(".dhxcombo_input").attr('placeholder', 'Select Branch Name and Type Cheque Number');
                        $(".BS_PrchsdForCombo").find(".dhxcombo_input").attr('placeholder', 'Type Branch Name Here');
                        $(".BS_UserCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".BS_StaffIdCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name Before');
                        $(".BS_AprovlGvnByCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".BS_AprovlTknByCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".LC_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Type Branch Name Here');
                        $(".ST_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Type State Name Here');
                        $(".BS_PersonsCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name');
                        $(".BS_PaidByCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name');


                        dhxGridBalSheet.showRow(id);
                        //$('#BS_Form_'+id+' .dhxform_label_align_left').css({ "text-align": "right" });
                        if (BSForm[id]) {

                            var ComboValues = $('#ComboValues').val();

                            var str = new Object();
                            var ComboNames = jQuery.parseJSON(ComboValues);
                            $.each(ComboNames, function (index, value) {                                
                                var ComboNme = ComboCls = value + "Combo";
                                ComboNme = BSForm[id].getCombo(value);                                
                                preTally.Settings.comboFilterPreload(ComboNme, ComboCls);
                            });
                            var item_list = new Array("BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id", "CHQ_Number", "BS_Transaction", "BS_PayersChQ", "BS_PayersBank");
                            if (BSForm[id].getUserData("PM_Id", "cId") == '2') {
                                for (var i = 0; i <= 10; i++) {
                                    BSForm[id].showItem(item_list[i]);
                                }
                                BSForm[id].setItemValue("PM_Id", BSForm[id].getUserData("PM_Id", "cId"));
                            }
                            BSForm[id].attachEvent("onInputChange", function (name) {
                                if (name == 'BS_Price') {
                                    var BS_Qty = 0;
                                    if (BSForm[id].getItemValue("BS_Price") > 0)
                                        BS_Qty = BSForm[id].getItemValue("BS_Amount") / BSForm[id].getItemValue("BS_Price");
                                    BSForm[id].setItemValue("BS_Quantity", Math.round(BS_Qty * 100) / 100);
                                }

                                if (name == 'BS_Quantity') {
                                    var BS_Prce = 0;
                                    if (BSForm[id].getItemValue("BS_Quantity") > 0)
                                        BS_Prce = BSForm[id].getItemValue("BS_Amount") / BSForm[id].getItemValue("BS_Quantity");
                                    BSForm[id].setItemValue("BS_Price", Math.round(BS_Prce * 100) / 100);
                                }
                            });
                            if (BSForm[id].isItem("PM_Id")) {

                                BSForm[id].hideItem("BS_PaidDate");
                                BSForm[id].hideItem("BS_PayType");
                                BSForm[id].hideItem("BNK_Id");
                                BSForm[id].hideItem("BB_Id");
                                BSForm[id].hideItem("BA_Id");
                                BSForm[id].hideItem("CHQ_Number");
                                BSForm[id].hideItem("BS_Transaction");
                                BSForm[id].hideItem("BS_PayersBank");
                                BSForm[id].hideItem("BS_PayersChQ");
                                var item_list = new Array("BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id", "CHQ_Number", "BS_Transaction", "BS_PayersChQ", "BS_PayersBank");
                                var patmentMode = BSForm[id].getCombo("PM_Id");
                                patmentMode.setComboValue(BSForm[id].getUserData("PM_Id", "cId"));

                                if (BSForm[id].getUserData("PM_Id", "cId") == '2') {
                                    for (i = 0; i <= 10; i++) {
                                        BSForm[id].showItem(item_list[i]);
                                    }
                                    BSForm[id].setItemValue("PM_Id", BSForm[id].getUserData("PM_Id", "cId"));

                                    if (BSForm[id].isItem("BNK_Id")) {
                                        BSForm[id].setItemValue("BNK_Id", BSForm[id].getUserData("BNK_Id", "cId"));
                                    }
                                    if (BSForm[id].isItem("BB_Id")) {
                                        var BS_BBCombo = BSForm[id].getCombo("BB_Id");
                                        var params = "Bnk_Id=" + BSForm[id].getUserData("BNK_Id", "cId");
                                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function (xml) {
                                            var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                            BSForm[id].setItemValue("BB_Id", BBName);
                                            BS_BBCombo.setComboValue(BSForm[id].getUserData("BB_Id", "cId"));
                                        });

                                        //                            BS_BBCombo.addOption([[BSForm[id].getUserData("BB_Id","cId"),BSForm[id].getUserData("BB_Id","cValue")]]);
                                        //                            BS_BBCombo.setComboValue(bsDetailsForm.getUserData("BB_Id","cId"));
                                    }
                                    if (BSForm[id].isItem("BA_Id")) {
                                        var BS_BACombo = BSForm[id].getCombo("BA_Id");
                                        var params = "BB_Id=" + BSForm[id].getUserData("BB_Id", "cId");
                                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function (xml) {
                                            var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                            BSForm[id].setItemValue("BA_Id", BAName);
                                            BS_BACombo.setComboValue(BSForm[id].getUserData("BA_Id", "cId"));
                                        });
                                    }
                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        var BS_BChqCombo = BSForm[id].getCombo("CHQ_Number");
                                        var params = "BA_Id=" + BSForm[id].getUserData("BA_Id", "cId");
                                        BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);

                                        BS_BChqCombo.addOption([[BSForm[id].getUserData("CHQ_Number", "cId"), BSForm[id].getUserData("CHQ_Number", "cValue")]]);
                                        BS_BChqCombo.setComboValue(BSForm[id].getUserData("CHQ_Number", "cId"));
                                    }
                                }
                                patmentMode.attachEvent("onChange", function () {
                                    var pmMode = patmentMode.getSelectedValue();
                                    for (i = 0; i <= 10; i++) {
                                        if (pmMode == '1') {
                                            BSForm[id].hideItem(item_list[i]);
                                        } else {
                                            BSForm[id].showItem(item_list[i]);
                                        }
                                    }

                                });
                                if (BSForm[id].isItem("BS_PayType")) {
                                    var patmentType = BSForm[id].getCombo("BS_PayType");
                                    patmentType.attachEvent("onChange", function () {
                                        var pmType = patmentType.getSelectedValue();
                                        if (pmType == '2') {
                                            BSForm[id].hideItem("BS_Transaction");
                                            BSForm[id].showItem("CHQ_Number");
                                        }
                                        if (pmType == '3') {
                                            BSForm[id].setItemLabel("BS_Transaction", "DD Number");
                                            BSForm[id].showItem("BS_Transaction");
                                            BSForm[id].hideItem("CHQ_Number");
                                        }
                                        if (pmType == '4' || pmType == '5' || pmType == '6') {
                                            BSForm[id].setItemLabel("BS_Transaction", "Transaction Id");
                                            BSForm[id].showItem("BS_Transaction");
                                            BSForm[id].hideItem("CHQ_Number");
                                        }
                                    });
                                }


                            }
                            if (BSForm[id].isItem("BB_Id")) {
                                BnkCombo = BSForm[id].getCombo("BNK_Id");
                                BnkBrhCombo = BSForm[id].getCombo("BB_Id");
                                BnkAccCombo = BSForm[id].getCombo("BA_Id");
                                if (BSForm[id].isItem("CHQ_Number"))
                                    BnkChqCombo = BSForm[id].getCombo("CHQ_Number");

                                BnkBrhCombo.readonly(true);
                                BnkAccCombo.readonly(true);


                                BnkCombo.attachEvent("onClose", function () {
                                    BnkBrhCombo.clearAll();
                                    BnkAccCombo.clearAll();

                                    BnkBrhCombo.setComboText('');
                                    BnkAccCombo.setComboText('');
                                    BnkAccCombo.setComboValue('');

                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');
                                    }

                                    var bnk_id = BnkCombo.getSelectedValue();
                                    if ((bnk_id) && (bnk_id != 0)) {
                                        var params = "Bnk_Id=" + bnk_id;
                                        BnkBrhCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                                        });
                                    }
                                });
                                BnkBrhCombo.attachEvent("onClose", function () {
                                    BnkAccCombo.clearAll();

                                    BnkAccCombo.setComboText('');

                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');
                                    }

                                    var bnk_acc_id = BnkBrhCombo.getSelectedValue();
                                    if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                                        var params = "BB_Id=" + bnk_acc_id;
                                        BnkAccCombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                                        });
                                    }
                                });
                                BnkAccCombo.attachEvent("onClose", function () {
                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');

                                        var BNK_AC_Id = BnkAccCombo.getSelectedValue();
                                        if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                            var params = "BA_Id=" + BNK_AC_Id;
                                            BnkChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);
                                            //                                            BnkChqCombo.load("requisites/getAccountChqNo.php?"+encrypt(params), function() {
                                            //                                            });
                                        }
                                    }
                                });
                            }
                            if (BSForm[id].isItem("BS_User")) {

                                UserCombo = BSForm[id].getCombo("BS_User");
                                LCCombo = BSForm[id].getCombo("BS_PrchsdFor");

                                LCCombo.attachEvent("onClose", function () {
                                    UserCombo.clearAll();
                                    StaffCombo.clearAll();

                                    UserCombo.setComboText('');
                                    StaffCombo.setComboText('');

                                    var BS_Office = LCCombo.getSelectedValue();
                                    if ((BS_Office) && (BS_Office != 0)) {
                                        var params = "LC_Id=" + BS_Office;
                                        UserCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&" + params));
                                        //                                        UserCombo.attachEvent("onDynXLS", function(text){ 
                                        //                                            
                                        //                                        dhx4.ajax.get("requisites/getOfficeStaffs.php?mask="+text+"&OF_Id="+ BS_Office, function(xml){
                                        //                                            UserCombo.load(xml.xmlDoc.responseText);
                                        //                                            UserCombo.openSelect();
                                        //                                        });
                                        //                                        });
                                    }
                                });

                            }
                            if (BSForm[id].isItem("BS_StaffId")) {

                                UserCombo = BSForm[id].getCombo("BS_User");
                                StaffCombo = BSForm[id].getCombo("BS_StaffId");

                                StaffCombo.readonly(true);

                                UserCombo.attachEvent("onClose", function () {
                                    StaffCombo.clearAll();

                                    StaffCombo.setComboText('');

                                    var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                                    if ((BS_User) && (BS_User != 0)) {
                                        var params = "Emp_Id=" + BS_User;
                                        StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&" + params), function () {
                                        });
                                    }
                                });

                            }

                            if (BSForm[id].isItem("BS_PayType")) {
                                var BS_PayType = BSForm[id].getCombo("BS_PayType");
                                //var params = "mask=Self";
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/paymentMode.php"), function (xml) {
                                    Name = BS_PayType.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PayType", BSForm[id].getUserData("BS_PayType", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PaidBy")) {
                                var BS_PaidByCombo = BSForm[id].getCombo("BS_PaidBy");
                                //var params = "mask=Self";
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php&mask=Self"), function (xml) {
                                    Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PaidBy", BSForm[id].getUserData("BS_PaidBy", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PayTime")) {
                                var BS_PayTimeCombo = BSForm[id].getCombo("BS_PayTime");
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/modes.php&mode=time"), function (xml) {
                                    Name = BS_PayTimeCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PayTime", BSForm[id].getUserData("BS_PayTime", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("LC_Id")) {
                                var LC_IdCombo = BSForm[id].getCombo("LC_Id");
                                var lcid = BSForm[id].getUserData("LC_Id", "cId");
                                var lc_name = BSForm[id].getUserData("LC_Id", "cValue");
                                var params = "";
                                if (!lcid) {
                                    params = "&mask=Self";
                                } else {
                                    params = "&mask=" + lc_name;
                                }
                                //console.log(params);
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                                    var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("LC_Id", lcid);
                                });
                            }

                            if (BSForm[id].isItem("BS_IEByLC")) {
                                var BS_IEByLCCombo = BSForm[id].getCombo("BS_IEByLC");
                                var ieByLCid = BSForm[id].getUserData("BS_IEByLC", "cId");
                                var ieByLCname = BSForm[id].getUserData("BS_IEByLC", "cValue");
                                var params = "";
                                if (!ieByLCid || ieByLCid == 0) {
                                    params = "&mask=Self";
                                } else {
                                    params = "&mask=" + ieByLCname;
                                }

                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                                    BS_IEByLCCombo.load(xml.xmlDoc.responseText);
                                    if (ieByLCid)
                                        BSForm[id].setItemValue("BS_IEByLC", ieByLCid);

                                    var BS_IEByUSCombo = BSForm[id].getCombo("BS_IEByUS");
                                    var ieByUSid = BSForm[id].getUserData("BS_IEByUS", "cId");
                                    var ieByUSname = BSForm[id].getUserData("BS_IEByUS", "cValue");
                                    var params = "";
                                    if (!ieByUSid) {
                                        params = "&ctype=check&mask=Self&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                    } else {
                                        params = "&ctype=check&mask=" + ieByUSname + "&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                    }

                                    //ieByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php" + params));
                                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php" + params), function (xml) {
                                        BS_IEByUSCombo.load(xml.xmlDoc.responseText);
                                        BS_IEByUSCombo.enableFilteringMode(true);
                                        BS_IEByUSCombo.allowFreeText(false);
                                        if (ieByUSid)
                                            BSForm[id].setItemValue("BS_IEByUS", ieByUSid);
                                    });

                                    BS_IEByLCCombo.attachEvent("onChange", function () {
                                        BS_IEByUSCombo.clearAll();
                                        BS_IEByUSCombo.setComboValue(0);
                                        BS_IEByUSCombo.setComboText('');

                                        var params = "&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                        BS_IEByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php&ctype=check" + params), false);
                                    });
                                });
                            }
//                            if (BSForm[id].isItem("BS_IEByUS")) {  
//                                
//                                var ieByUSCombo = BSForm[id].getCombo("BS_IEByUS");                                                              
//                                var ieByUSid    = BSForm[id].getUserData("BS_IEByUS","cId");
//                                var ieByUSname  = BSForm[id].getUserData("BS_IEByUS","cValue");
//                                var params="";
//                                if(!ieByUSid){
//                                    params = "&mask=Self";
//                                }else{
//                                    params = "&mask="+ieByUSname;
//                                }
//                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
//                                    ieByUSCombo.load(xml.xmlDoc.responseText);
//                                    BSForm[id].setItemValue("BS_IEByUS",ieByUSid);
//                                });
//                            }   
                            if (BSForm[id].isItem("BS_PrchsdFor")) {
                                var BS_PrchsdForCombo = BSForm[id].getCombo("BS_PrchsdFor");
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBranches.php"), function (xml) {
                                    BSForm[id].setItemValue("BS_PrchsdFor", BSForm[id].getUserData("BS_PrchsdFor", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PaidTo")) {
                                BSForm[id].setReadonly("BS_PaidTo", true);
                            }
                            if (BSForm[id].isItem("BS_RcvdFrm")) {
                                BSForm[id].setReadonly("BS_RcvdFrm", true);
                            }

                            if (BSForm[id].isItem("BS_PettyCashRefId")) {
                                BSForm[id].setItemValue("BS_PettyCashRefId", BSForm[id].getUserData("BS_PettyCashRefId", "cId"));
                                var BS_PCRefidCombo = BSForm[id].getCombo("BS_PettyCashRefId");
                                BS_PCRefidCombo.setOptionWidth(350);

                                BS_PCRefidCombo.attachEvent("onXLE", function () {
                                    BS_PCRefidCombo.deleteOption(id);
                                });
//                                
//                                
//                                BSForm[id].setItemValue("PettyCashAmount",addItemForm.getItemValue("PettyCashAmount"));


//                                $.ajax({
//                                    url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BSForm[id].getUserData("BS_PettyCashRefId","cId"))
//                                }).done(function(data) { 
//                                    BSForm[id].setItemValue("PettyCashAmount",data);
//                                });
                                $.ajax({
                                    url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + id + "&PCRefId=" + BSForm[id].getUserData("BS_PettyCashRefId", "cId"))
                                }).done(function (data) {
                                    BSForm[id].setItemValue("PettyCashAmount", data);
                                });
                                BS_PCRefidCombo.attachEvent("onClose", function () {

                                    $.ajax({
                                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + id + "&PCRefId=" + BS_PCRefidCombo.getSelectedValue())
                                    }).done(function (data) {
                                        BSForm[id].setItemValue("PettyCashAmount", data);
                                    });
                                });
                            }

                            preTally.BalanceSheet.addBalSheetItem(id);
                        }

                    });

                }, "json");
                /*ptBalSheetTab.attachEvent("onTabClick", function(id, lastId){
                 if(id!="a1"){
                 if(id=="a2"){sortKey="week";}else if(id=="a3"){sortKey="mnt";}else if(id=="a4"){sortKey="year";}
                 
                 dhxGridSavedBSItems = ptBalSheetTab.cells(id).attachGrid();
                 dhxGridSavedBSItems.init();
                 var params = "sortKey=" + sortKey;
                 dhxGridSavedBSItems.loadXML("requisites/listSavedBSItems.php?"+encrypt(params), function() {
                 dhxGridSavedBSItems.enableRowsHover(true, "hover");
                 
                 dhxGridSavedBSItems.attachEvent("onRowSelect", function(id) {
                 
                 if (dhxGridSavedBSItems.doesRowExist(openBSID)) {
                 dhxGridSavedBSItems.cells(openBSID, 0).close();
                 //dhxGridBalSheet.setRowTextNormal(openBSID);
                 dhxGridSavedBSItems.setRowTextStyle(openBSID, "padding-top: 3px;");
                 }
                 
                 if (openBSID == id) {
                 openBSID = 0;
                 } else {
                 dhxGridSavedBSItems.cells(id, 0).open();
                 //dhxGridBalSheet.setRowTextBold(id);
                 dhxGridSavedBSItems.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                 openBSID = id;
                 }
                 dhxGridSavedBSItems.clearSelection();
                 });
                 });
                 }
                 });*/

            } else {
                var MainCombo = dhxNewBalSheetForm.getCombo("MH_Type");
                var MH_Type = MainCombo.getSelectedValue();
                dhxNewBalSheetForm.reloadOptions("IT_Id", preTally.Initialize.encryptURL("requisites/newBSItems.php&type=" + MH_Type));
                dhxGridBalSheet.updateFromJSON(preTally.Initialize.encryptURL("requisites/listBSItems.php"), true, true, function () { });
//                dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function() {},"json");

                dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");

                dhxMiddleBlockTabs.tabs("menuNewBalSheet").setActive();
            }
        },
        menuNewDescription: function (id) {
            DSId = id;
            if (!dhxMiddleBlockTabs.cells("menuNewDescription")) {
                dhxMiddleBlockTabs.addTab("menuNewDescription", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Descriptions", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewDescription");
                dhxMiddleBlockTabs.tabs("menuNewDescription").setActive();


                var menuDescriptionLayout = dhxMiddleBlockTabs.cells("menuNewDescription").attachLayout('2U');
                menuDescriptionLayout.cells("a").setText("New Description");
                menuDescriptionLayout.cells("b").setText("List Description");
                menuDescriptionLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Description --------------------//
                listDescriptionGrid = menuDescriptionLayout.cells("b").attachGrid();
                dhxLayout.progressOn();
                listDescriptionGrid.init();
                var params = "user=1";
                listDescriptionGrid.loadXML(preTally.Initialize.encryptURL("requisites/listDescription.php&" + params), function () {
                    dhxLayout.progressOff();

                    listDescriptionGrid.attachEvent("onRowSelect", preTally.BalanceSheet.editDescription);
                });
                //----------------- Attach Form for Description --------------------//
                addDescriptionForm = menuDescriptionLayout.cells("a").attachForm();
                addDescriptionForm.loadStruct(preTally.Initialize.encryptURL("requisites/newDescription.php&r=" + new Date().getTime()), function () {
                    var k = 5;
                    var i = 1;
                    MHCombo = addDescriptionForm.getCombo("MH_Type");
                    subheadCombo = addDescriptionForm.getCombo("SH_Id");
                    DS_ItmCombo = addDescriptionForm.getCombo("IT_Id");

                    MHCombo.attachEvent("onClose", function () {
                        subheadCombo.clearAll();
                        subheadCombo.setComboText("");
                        subheadCombo.setComboValue("");
                        DS_ItmCombo.clearAll();
                        var MH_Id = MHCombo.getSelectedValue();
                        if ((MH_Id) && (MH_Id != 0)) {
                            var params = "type=" + MH_Id + "&filter=1";
                            subheadCombo.load(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function () {
                            });
                            DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                            });
                        }
                    });
                    subheadCombo.attachEvent("onClose", function () {
                        DS_ItmCombo.clearAll();
                        var SH_Id = subheadCombo.getSelectedValue();
                        if ((SH_Id) && (SH_Id != 0)) {
                            var params = "SH_Id=" + SH_Id;
                            DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                            });
                        } else {
                            var MH_Id = MHCombo.getSelectedValue();
                            if ((MH_Id) && (MH_Id != 0)) {
                                var params = "type=" + MH_Id;
                                DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                                });
                            }
                        }
                    });

                    if (DSId != 0) {
                        MH_Id = listDescNoftGrid.getUserData(DSId, "MH_Type");
                        addDescriptionForm.setItemValue("MH_Type", MH_Id);
                        subheadCombo.clearAll();
                        DS_ItmCombo.clearAll();
                        var params = "type=" + MH_Id + "&filter=1";
                        subheadCombo.load(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function () {
                            addDescriptionForm.setItemValue("SH_Id", listDescNoftGrid.getUserData(DSId, "SH_Id"));
                        });
                        DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                            addDescriptionForm.setItemValue("IT_Id", listDescNoftGrid.getUserData(DSId, "IT_Id"));
                        });

                        addDescriptionForm.setItemValue("DS_Id", listDescNoftGrid.getUserData(DSId, "DS_Id"));
                        addDescriptionForm.setItemValue("DS_Description[1]", listDescNoftGrid.getUserData(DSId, "DS_Description"));
                        addDescriptionForm.setItemValue("DS_Status", listDescNoftGrid.getUserData(DSId, "DS_Status"));
                    } else {
                        var params = "type=" + 2 + "&filter=1";
                        subheadCombo.load(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function () { });
                        DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () { });
                    }
                    addDescriptionForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newDescriptionValidate') {
                            var newDescription = addDescriptionForm.validate();
                            if (newDescription) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addDescriptionForm.send(preTally.Initialize.encryptURL('warehouse/newDescription.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (isNaN(response)) {
                                        addDescriptionForm.resetValidateCss();
                                        addDescriptionForm.clear();
                                        addDescriptionForm.setItemValue("DS_Id", 0);
                                        if (DSId != 0) {
                                            var ds_rwID = 1;
                                            listDescNoftGrid.deleteRow(DSId);
                                            listDescNoftGrid.forEachRow(function (id) {
                                                listDescNoftGrid.cells(id, 0).setValue(ds_rwID);
                                                ds_rwID++;
                                            });
                                            dhxMiddleBlockTabs.tabs("menuNotfDesc").setActive();
                                            DSId = 0;
                                        }
                                        while (i > 1) {
                                            addDescriptionForm.removeItem("DS_Description[" + i + "]");
                                            i--;
                                            k--;
                                        }
                                    } else {
                                        addDescriptionForm.setValidateCss('DS_Description[' + response + ']', false, 'validate_red');
                                        response = 'Description Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});

                                    listDescriptionGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listDescription.php"), true, true, function () {
                                        //preTally.BalanceSheet.editDescription();
                                    });
                                });
                            }
                        } else if (name == 'addDescription') {
                            i++;
                            var itemData = {type: "input", name: "DS_Description[" + i + "]", label: "Description " + i, required: "true", rows: "3", note: [
                                    {text: "Description"},
                                ]};
                            addDescriptionForm.addItem(null, itemData, k++);
                        } else if (name == 'removeDescription') {
                            if (i > 1) {
                                addDescriptionForm.removeItem("DS_Description[" + i + "]");
                                i--;
                                k--;
                            }
                        } else {
                            addDescriptionForm.resetValidateCss();
                            addDescriptionForm.clear();
                            addDescriptionForm.setItemValue("DS_Id", 0);
                        }
                    });

                });
            } else {
                if (DSId != 0) {
                    addDescriptionForm.setItemValue("DS_Id", listDescNoftGrid.getUserData(DSId, "DS_Id"));
                    MH_Id = listDescNoftGrid.getUserData(DSId, "MH_Type");
                    addDescriptionForm.setItemValue("MH_Type", MH_Id);
                    subheadCombo.clearAll();
                    DS_ItmCombo.clearAll();
                    var params = "type=" + MH_Id + "&filter=1";
                    subheadCombo.load(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function () {
                        addDescriptionForm.setItemValue("SH_Id", listDescNoftGrid.getUserData(DSId, "SH_Id"));
                    });
                    DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                        addDescriptionForm.setItemValue("IT_Id", listDescNoftGrid.getUserData(DSId, "IT_Id"));
                    });

//                    addDescriptionForm.setItemValue("DS_Id", DSId);
//                    addDescriptionForm.setItemValue("IT_Id", listDescNoftGrid.getUserData(DSId, "IT_Id"));
                    addDescriptionForm.setItemValue("DS_Description[1]", listDescNoftGrid.getUserData(DSId, "DS_Description"));
                    addDescriptionForm.setItemValue("DS_Status", listDescNoftGrid.getUserData(DSId, "DS_Status"));
                } else {
                    preTally.Reload.reInitialize("menuNewDescription");
                }
                dhxMiddleBlockTabs.tabs("menuNewDescription").setActive();
            }
        },
        editDescription: function (rowId) {
            //addDescriptionForm.hideItem("addDescription");
            var DSId = rowId;
            //listDescriptionGrid.attachEvent("onRowSelect", function(DSId) {
            addDescriptionForm.setItemValue("DS_Id", DSId);
            addDescriptionForm.setItemValue("DS_Description[1]", listDescriptionGrid.getUserData(DSId, "DS_Description"));
            addDescriptionForm.setItemValue("DS_Status", listDescriptionGrid.getUserData(DSId, "DS_Status"));

            addDescriptionForm.setItemValue("MH_Type", listDescriptionGrid.getUserData(DSId, "MH_Type"));
            subheadCombo.clearAll();
            MH_typ = listDescriptionGrid.getUserData(DSId, "MH_Type");
            var params = "type=" + MH_typ + "&filter=1";
            subheadCombo.load(preTally.Initialize.encryptURL("requisites/subheads.php&" + params), function () {
                addDescriptionForm.setItemValue("SH_Id", listDescriptionGrid.getUserData(DSId, "SH_Id"));
            });
            DS_ItmCombo.clearAll();
            var SH_Id = listDescriptionGrid.getUserData(DSId, "SH_Id");
            params = "SH_Id=" + SH_Id;
            DS_ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&" + params), function () {
                addDescriptionForm.setItemValue("IT_Id", listDescriptionGrid.getUserData(DSId, "IT_Id"));
            });
            //});
        }, menuNewBalSheetSample: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewBalSheetSample")) {
                if (dhxMiddleBlockTabs.cells("menuNewBalSheet")) {
                    dhxMiddleBlockTabs.tabs("menuNewBalSheet").close();
                }
                dhxMiddleBlockTabs.addTab("menuNewBalSheetSample", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Accounts Entry", 150);
                dhxMiddleBlockTabs.tabs("menuNewBalSheetSample").setActive();
                dhxMiddleBlockLayout = dhxMiddleBlockTabs.cells("menuNewBalSheetSample").attachLayout("2E");
                dhxMiddleBlockLayout.cells("a").hideHeader();

                dhxMiddleBlockLayout.cells("a").setHeight(90);
                dhxMiddleBlockLayout.cells("a").fixSize(true, true);
                dhxMiddleBlockLayout.cells("a").hideHeader();
                dhxMiddleBlockLayout.cells("b").hideHeader();

                /*ptBalSheetTab = dhxMiddleBlockLayout.cells("c").attachTabbar();
                 
                 ptBalSheetTab.setSkin('dhx_skyblue');
                 //ptBalSheetTab.setImagePath("assets/tabbar/codebase/imgs/");
                 ptBalSheetTab.addTab("a1", "New Item", "100px");
                 ptBalSheetTab.addTab("a2", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Week", "100px");
                 ptBalSheetTab.addTab("a3", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Month", "100px");
                 ptBalSheetTab.addTab("a4", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Last Year", "100px");
                 //ptBalSheetTab.setTabActive("a1");
                 ptBalSheetTab.tabs("a1").setActive();*/

                var NBS_Width = dhxMiddleBlockLayout.cells("a").getWidth();
                //dhxNewBalSheetForm.setItemWidth("IT_Id", 170);
                //dhxNewBalSheetForm.setItemWidth("BS_Description", 170);
                //var tmp = dhxNewBalSheetForm.getItemWidth("MH_Type");
                //var tmp = dhxNewBalSheetForm.getItemWidth('MH_Type');
                //console.log(tmp);

                dhxNewBalSheetFormSamp = dhxMiddleBlockLayout.cells("a").attachForm();
                dhxNewBalSheetFormSamp.loadStruct(preTally.Initialize.encryptURL("requisites/newBalSheetSample.php&w=" + NBS_Width + "&x=" + new Date().getTime()), function () {
                    //dhxNewBalSheetFormSamp.setFontSize(14+'px');
                    if (!dhxNewBalSheetFormSamp.isItemHidden("BS_Track")) {
                        dhxNewBalSheetFormSamp.hideItem("BS_Track");
                        dhxNewBalSheetFormSamp.setRequired("BS_Track", false)
                    }

//                    $( ".dhxform_base" ).addClass( "myClass yourClass" );
                    $(".newBalSheet_2").parent().addClass("form_baseX");
                    $('.save2').hide();
                    BSFormHeight_2 = $('.form_baseX').height();

                    preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight_2, 0);

                    var IT_Note = 'NAME OF THE EXPENSE';
                    var DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                    var GST_Note = 'GST/VAT NO: OF SHOP/PAYEE';
                    prtNewCombo = dhxNewBalSheetFormSamp.getCombo("IT_Id");
                    descCombo = dhxNewBalSheetFormSamp.getCombo("BS_Description");
                    mhtypeCombo = dhxNewBalSheetFormSamp.getCombo("MH_Type");
                    preTally.Settings.comboSelectPreload(descCombo, "desccombo");
                    preTally.Settings.comboSelectPreload(prtNewCombo, "itemcombo");

                    prtNewCombo.setOptionWidth(500);
                    descCombo.setOptionWidth(400);
                    //prtNewCombo.readonly("Enable");
                    //prtNewCombo.enableFilteringMode('between');
                    descCombo.enableFilteringMode(true);
                    var params = "";
                    prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItemsSample.php&type=2"));
                    /*dhxNewBalSheetFormSamp.attachEvent("onFocus",function(id,response){                                               
                     if(id=="GOGL_PlaceSearch"){
                     dhxMiddleBlockLayout.cells("a").setHeight(120);
                     $(".newLocDetail div.dhxform_obj_dhx_skyblue.dhxform_block").attr("style","float:right!important;display:block;padding-right:2px;");
                     BSFormHeight = $('.form_base').height();
                     }
                     });*/
                    mhtypeCombo.attachEvent("onChange", function (response, text) {
                        /*if (loader == 'MH_Type') {*/
                        prtNewCombo.clearAll();
                        prtNewCombo.setComboText("");
                        prtNewCombo.setComboValue("");
                        descCombo.clearAll();
                        descCombo.setComboText("");
                        descCombo.setComboValue("");
                        dhxNewBalSheetFormSamp.setItemValue("BS_Amount", "");
                        dhxNewBalSheetFormSamp.setItemValue("BS_Invoice", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_PlaceSearch", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_Place", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_City", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_Place", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_State", "");
                        dhxNewBalSheetFormSamp.setItemValue("GOGL_Country", "");
                        dhxNewBalSheetFormSamp.setItemValue("BS_GST", "");
                        prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItemsSample.php&type=" + response));
                        if (response == 1) {
                            IT_Note = 'NAME OF THE INCOME';
                            DS_Note = 'RECEIVED FROM [ NAME AND DETAILS PARTIES ]';
                            GST_Note = 'GST/VAT NO: OF CUSTOMER';
                        } else {
                            IT_Note = 'NAME OF THE EXPENSE';
                            DS_Note = 'PAID TO [ NAME AND DETAILS PARTIES ]';
                            GST_Note = 'GST/VAT NO: OF SHOP/PAYEE'
                        }
                        dhxNewBalSheetFormSamp.setNote('IT_Id', {text: IT_Note});
                        dhxNewBalSheetFormSamp.setNote('BS_Description', {text: DS_Note});
                        dhxNewBalSheetFormSamp.setNote('BS_GST', {text: GST_Note});

                        /*}*/
                    });
                    var filtrInterval;

                    prtNewCombo.attachEvent("onKeyPressed", function (inp) {
                        if ((inp >= '95' && inp <= '122') || (inp >= '65' && inp <= '90') || (inp >= '49' && inp <= '57')) {
                            if (filtrInterval)
                                clearInterval(filtrInterval);
                            filtrInterval = setInterval(function () {
                                prtNewCombo.clearAll();
                                prtNewCombo.load(preTally.Initialize.encryptURL("requisites/newBSItemsSample.php&type=" + mhtypeCombo.getSelectedValue() + "&mask=" + prtNewCombo.getComboText()), function () {
                                    prtNewCombo.openSelect();
                                });
                                clearInterval(filtrInterval);
                            }, 500);
                        }
                    });
                    dhxNewBalSheetFormSamp.attachEvent("onKeyUp", function (inp, ev, name, value) {


                        if (name == "GOGL_PlaceSearch") {
                            if (filtrInterval)
                                clearInterval(filtrInterval);
                            filtrInterval = setInterval(function () {
                                preTally.GoogleAddress.googleAddress(dhxNewBalSheetFormSamp);
                                clearInterval(filtrInterval);
                            }, 500);

                        }
                    });

                    prtNewCombo.attachEvent("onChange", function () {
                        descCombo.clearAll();
                        descCombo.setComboText('');
                        var Itm_id = prtNewCombo.getSelectedValue();
                        if (!isNaN(Itm_id) && (Itm_id != 0)) {
                            $.ajax({
                                url: preTally.Initialize.encryptURL("warehouse/getSubHeadType.php&IT_Id=" + Itm_id)
                            }).done(function (data) {

                                //descCombo.enableFilteringMode(false);
                                descCombo.load(preTally.Initialize.encryptURL("requisites/descriptions.php&IT_Id=" + Itm_id), function () {
                                });
                                dhxNewBalSheetFormSamp.setNote('IT_Id', {text: IT_Note});
                                dhxNewBalSheetFormSamp.setNote('BS_Description', {text: DS_Note});
                                if (!dhxNewBalSheetFormSamp.isItemHidden("BS_Track")) {
                                    dhxNewBalSheetFormSamp.hideItem("BS_Track");
                                    dhxNewBalSheetFormSamp.setRequired("BS_Track", false);
                                    dhxMiddleBlockLayout.cells("a").setHeight(90);
                                    $(".newLocDetail div.dhxform_obj_dhx_skyblue.dhxform_block").attr("style", "display:none;padding-right:2px;");
                                    $('.save1').show();
                                    $('.save2').hide();
                                    BSFormHeight = $('.form_base').height();
                                    preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight_2, 0);
                                }
                                if (data == 1) {

                                    var filtrInterval;
                                    dhxNewBalSheetFormSamp.showItem("BS_Track");
                                    trackCombo = dhxNewBalSheetFormSamp.getCombo("BS_Track");

                                    trackCombo.setComboValue('');
                                    trackCombo.setComboText('');

                                    dhxNewBalSheetFormSamp.setRequired("BS_Track", true);
                                    dhxMiddleBlockLayout.cells("a").setHeight(120);
                                    $(".newLocDetail div.dhxform_obj_dhx_skyblue.dhxform_block").attr("style", "display:block;padding-right:2px;");
                                    $('.save1').hide();
                                    $('.save2').show();
                                    BSFormHeight = $('.form_base').height();
                                    preTally.BalanceSheet.BSFormCeterAlign(BSFormHeight_2, 0);


                                    $(".trackCombo").keyup(function (value) {
                                        //var mask = this.value;alert(mask);
                                        if (filtrInterval)
                                            clearInterval(filtrInterval);

                                        filtrInterval = setInterval(function () {
                                            trackCombo.clearAll();
                                            trackCombo.enableFilteringMode(true);
                                            trackCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php&mask=" + trackCombo.getComboText()), function () {});
//                                                trackCombo.enableFilteringMode(true,"requisites/tracks.php");
                                            clearInterval(filtrInterval);
                                        }, 500);

                                    });
                                }
                            });
                        }
                    });
                    //preTally.BalanceSheet.saveBalanceSheetItem();


                });



                //----------------- Attach Grid for Accounts Entry in Main Block --------------------//
                //dhxGridBalSheet = ptBalSheetTab.cells("a1").attachGrid();
                dhxGridBalSheet = dhxMiddleBlockLayout.cells("b").attachGrid();
                dhxGridBalSheet.attachEvent("onXLE", function () {
                    dhxLayout.progressOff();
                });

                dhxGridBalSheet.setImagePath("assets/grid/codebase/imgs/");
                dhxGridBalSheet.init();
                //dhxGridBalSheet.splitAt(2);
                //dhxGridBalSheet.enableSmartRendering(true); 


                dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function () {

                    dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");

                    dhxGridBalSheet.enableRowsHover(true, "hover");

                    dhxGridBalSheet.attachEvent("onRowSelect", function (id) {

                        if (dhxGridBalSheet.doesRowExist(openBSID) && id != 0) {
                            dhxGridBalSheet.cells(openBSID, 0).close();
                            //dhxGridBalSheet.setRowTextNormal(openBSID);
                            dhxGridBalSheet.setRowTextStyle(openBSID, "padding-top: 3px;");
                        }

                        if (openBSID == id) {
                            openBSID = 0;
                        } else {
                            dhxGridBalSheet.cells(id, 0).open();
                            //dhxGridBalSheet.setRowTextBold(id);
                            dhxGridBalSheet.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                            openBSID = id;
                        }
                        dhxGridBalSheet.clearSelection();
                    });

                    dhxGridBalSheet.attachEvent("onSubRowOpen", function (id, state) {
                        if (state == true) {
                            dhxGridBalSheet.showRow(id);
                            dhxGridBalSheet.setRowTextBold(id);
                            dhxGridBalSheet.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                        } else {
                            dhxGridBalSheet.setRowTextNormal(id);
                            dhxGridBalSheet.setRowTextStyle(id, "padding-top: 3px;");
                        }

                    });

                    dhxGridBalSheet.attachEvent("onSubAjaxLoad", function (id, state) {

                        $(".BB_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Select Bank Before to Select Bank Branch');
                        $(".BA_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Select Branch Name Before to Select Account');
                        $(".CHQ_NumberCombo").find(".dhxcombo_input").attr('placeholder', 'Select Branch Name and Type Cheque Number');
                        $(".BS_PrchsdForCombo").find(".dhxcombo_input").attr('placeholder', 'Type Branch Name Here');
                        $(".BS_UserCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".BS_StaffIdCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name Before');
                        $(".BS_AprovlGvnByCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".BS_AprovlTknByCombo").find(".dhxcombo_input").attr('placeholder', 'Type User Name Here');
                        $(".LC_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Type Branch Name Here');
                        $(".ST_IdCombo").find(".dhxcombo_input").attr('placeholder', 'Type State Name Here');
                        $(".BS_PersonsCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name');
                        $(".BS_PaidByCombo").find(".dhxcombo_input").attr('placeholder', 'Select User Name');


                        dhxGridBalSheet.showRow(id);
                        //$('#BS_Form_'+id+' .dhxform_label_align_left').css({ "text-align": "right" });
                        if (BSForm[id]) {

                            var ComboValues = $('#ComboValues').val();

                            var str = new Object();
                            var ComboNames = jQuery.parseJSON(ComboValues);
                            $.each(ComboNames, function (index, value) {
                                var ComboNme = ComboCls = value + "Combo";
                                ComboNme = BSForm[id].getCombo(value);
                                preTally.Settings.comboFilterPreload(ComboNme, ComboCls);
                            });
                            BSForm[id].attachEvent("onInputChange", function (name) {
                                if (name == 'BS_Price') {
                                    var BS_Qty = 0;
                                    if (BSForm[id].getItemValue("BS_Price") > 0)
                                        BS_Qty = BSForm[id].getItemValue("BS_Amount") / BSForm[id].getItemValue("BS_Price");
                                    BSForm[id].setItemValue("BS_Quantity", Math.round(BS_Qty * 100) / 100);
                                }

                                if (name == 'BS_Quantity') {
                                    var BS_Prce = 0;
                                    if (BSForm[id].getItemValue("BS_Quantity") > 0)
                                        BS_Prce = BSForm[id].getItemValue("BS_Amount") / BSForm[id].getItemValue("BS_Quantity");
                                    BSForm[id].setItemValue("BS_Price", Math.round(BS_Prce * 100) / 100);
                                }
                            });
                            if (BSForm[id].isItem("PM_Id")) {

                                BSForm[id].hideItem("BS_PaidDate");
                                BSForm[id].hideItem("BS_PayType");
                                BSForm[id].hideItem("BNK_Id");
                                BSForm[id].hideItem("BB_Id");
                                BSForm[id].hideItem("BA_Id");
                                BSForm[id].hideItem("CHQ_Number");
                                BSForm[id].hideItem("BS_Transaction");
                                BSForm[id].hideItem("BS_PayersBank");
                                BSForm[id].hideItem("BS_PayersChQ");
                                var item_list = new Array("BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id", "CHQ_Number", "BS_Transaction", "BS_PayersChQ", "BS_PayersBank");
                                var patmentMode = BSForm[id].getCombo("PM_Id");
                                patmentMode.setComboValue(BSForm[id].getUserData("PM_Id", "cId"));

                                if (BSForm[id].getUserData("PM_Id", "cId") == '2') {
                                    for (i = 0; i <= 10; i++) {
                                        BSForm[id].showItem(item_list[i]);
                                    }
                                    BSForm[id].setItemValue("PM_Id", BSForm[id].getUserData("PM_Id", "cId"));

                                    if (BSForm[id].isItem("BNK_Id")) {
                                        BSForm[id].setItemValue("BNK_Id", BSForm[id].getUserData("BNK_Id", "cId"));
                                    }
                                    if (BSForm[id].isItem("BB_Id")) {
                                        var BS_BBCombo = BSForm[id].getCombo("BB_Id");
                                        var params = "Bnk_Id=" + BSForm[id].getUserData("BNK_Id", "cId");
                                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function (xml) {
                                            var BBName = BS_BBCombo.load(xml.xmlDoc.responseText);
                                            BSForm[id].setItemValue("BB_Id", BBName);
                                            BS_BBCombo.setComboValue(BSForm[id].getUserData("BB_Id", "cId"));
                                        });

                                        //                            BS_BBCombo.addOption([[BSForm[id].getUserData("BB_Id","cId"),BSForm[id].getUserData("BB_Id","cValue")]]);
                                        //                            BS_BBCombo.setComboValue(bsDetailsForm.getUserData("BB_Id","cId"));
                                    }
                                    if (BSForm[id].isItem("BA_Id")) {
                                        var BS_BACombo = BSForm[id].getCombo("BA_Id");
                                        var params = "BB_Id=" + BSForm[id].getUserData("BB_Id", "cId");
                                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function (xml) {
                                            var BAName = BS_BACombo.load(xml.xmlDoc.responseText);
                                            BSForm[id].setItemValue("BA_Id", BAName);
                                            BS_BACombo.setComboValue(BSForm[id].getUserData("BA_Id", "cId"));
                                        });
                                    }
                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        var BS_BChqCombo = BSForm[id].getCombo("CHQ_Number");
                                        var params = "BA_Id=" + BSForm[id].getUserData("BA_Id", "cId");
                                        BS_BChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);

                                        BS_BChqCombo.addOption([[BSForm[id].getUserData("CHQ_Number", "cId"), BSForm[id].getUserData("CHQ_Number", "cValue")]]);
                                        BS_BChqCombo.setComboValue(BSForm[id].getUserData("CHQ_Number", "cId"));
                                    }
                                }
                                patmentMode.attachEvent("onChange", function () {
                                    var pmMode = patmentMode.getSelectedValue();
                                    for (i = 0; i <= 10; i++) {
                                        if (pmMode == '1') {
                                            BSForm[id].hideItem(item_list[i]);
                                        } else {
                                            BSForm[id].showItem(item_list[i]);
                                        }
                                    }

                                });
                                if (BSForm[id].isItem("BS_PayType")) {
                                    var patmentType = BSForm[id].getCombo("BS_PayType");
                                    patmentType.attachEvent("onChange", function () {
                                        var pmType = patmentType.getSelectedValue();
                                        if (pmType == '2') {
                                            BSForm[id].hideItem("BS_Transaction");
                                            BSForm[id].showItem("CHQ_Number");
                                        }
                                        if (pmType == '3') {
                                            BSForm[id].setItemLabel("BS_Transaction", "DD Number");
                                            BSForm[id].showItem("BS_Transaction");
                                            BSForm[id].hideItem("CHQ_Number");
                                        }
                                        if (pmType == '4' || pmType == '5' || pmType == '6') {
                                            BSForm[id].setItemLabel("BS_Transaction", "Transaction Id");
                                            BSForm[id].showItem("BS_Transaction");
                                            BSForm[id].hideItem("CHQ_Number");
                                        }
                                    });
                                }


                            }
                            if (BSForm[id].isItem("BB_Id")) {
                                BnkCombo = BSForm[id].getCombo("BNK_Id");
                                BnkBrhCombo = BSForm[id].getCombo("BB_Id");
                                BnkAccCombo = BSForm[id].getCombo("BA_Id");
                                if (BSForm[id].isItem("CHQ_Number"))
                                    BnkChqCombo = BSForm[id].getCombo("CHQ_Number");

                                BnkBrhCombo.readonly(true);
                                BnkAccCombo.readonly(true);


                                BnkCombo.attachEvent("onClose", function () {
                                    BnkBrhCombo.clearAll();
                                    BnkAccCombo.clearAll();

                                    BnkBrhCombo.setComboText('');
                                    BnkAccCombo.setComboText('');
                                    BnkAccCombo.setComboValue('');

                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');
                                    }

                                    var bnk_id = BnkCombo.getSelectedValue();
                                    if ((bnk_id) && (bnk_id != 0)) {
                                        var params = "Bnk_Id=" + bnk_id;
                                        BnkBrhCombo.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                                        });
                                    }
                                });
                                BnkBrhCombo.attachEvent("onClose", function () {
                                    BnkAccCombo.clearAll();

                                    BnkAccCombo.setComboText('');

                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');
                                    }

                                    var bnk_acc_id = BnkBrhCombo.getSelectedValue();
                                    if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                                        var params = "BB_Id=" + bnk_acc_id;
                                        BnkAccCombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                                        });
                                    }
                                });
                                BnkAccCombo.attachEvent("onClose", function () {
                                    if (BSForm[id].isItem("CHQ_Number")) {
                                        BnkChqCombo.clearAll();
                                        BnkChqCombo.setComboText('');
                                        BnkChqCombo.setComboValue('');

                                        var BNK_AC_Id = BnkAccCombo.getSelectedValue();
                                        if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                            var params = "BA_Id=" + BNK_AC_Id;
                                            BnkChqCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&" + params), false);
                                            //                                            BnkChqCombo.load("requisites/getAccountChqNo.php?"+encrypt(params), function() {
                                            //                                            });
                                        }
                                    }
                                });
                            }
                            if (BSForm[id].isItem("BS_User")) {

                                UserCombo = BSForm[id].getCombo("BS_User");
                                LCCombo = BSForm[id].getCombo("BS_PrchsdFor");

                                LCCombo.attachEvent("onClose", function () {
                                    UserCombo.clearAll();
                                    StaffCombo.clearAll();

                                    UserCombo.setComboText('');
                                    StaffCombo.setComboText('');

                                    var BS_Office = LCCombo.getSelectedValue();
                                    if ((BS_Office) && (BS_Office != 0)) {
                                        var params = "LC_Id=" + BS_Office;
                                        UserCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/getBranchStaffs.php&" + params));
                                        //                                        UserCombo.attachEvent("onDynXLS", function(text){ 
                                        //                                            
                                        //                                        dhx4.ajax.get("requisites/getOfficeStaffs.php?mask="+text+"&OF_Id="+ BS_Office, function(xml){
                                        //                                            UserCombo.load(xml.xmlDoc.responseText);
                                        //                                            UserCombo.openSelect();
                                        //                                        });
                                        //                                        });
                                    }
                                });

                            }
                            if (BSForm[id].isItem("BS_StaffId")) {

                                UserCombo = BSForm[id].getCombo("BS_User");
                                StaffCombo = BSForm[id].getCombo("BS_StaffId");

                                StaffCombo.readonly(true);

                                UserCombo.attachEvent("onClose", function () {
                                    StaffCombo.clearAll();

                                    StaffCombo.setComboText('');

                                    var BS_User = UserCombo.getSelectedValue(); //alert(BS_User);
                                    if ((BS_User) && (BS_User != 0)) {
                                        var params = "Emp_Id=" + BS_User;
                                        StaffCombo.load(preTally.Initialize.encryptURL("requisites/getStaffId.php&" + params), function () {
                                        });
                                    }
                                });

                            }

                            if (BSForm[id].isItem("BS_PayType")) {
                                var BS_PayType = BSForm[id].getCombo("BS_PayType");
                                //var params = "mask=Self";
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/paymentMode.php"), function (xml) {
                                    Name = BS_PayType.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PayType", BSForm[id].getUserData("BS_PayType", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PaidBy")) {
                                var BS_PaidByCombo = BSForm[id].getCombo("BS_PaidBy");
                                //var params = "mask=Self";
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php&mask=Self"), function (xml) {
                                    Name = BS_PaidByCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PaidBy", BSForm[id].getUserData("BS_PaidBy", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PayTime")) {
                                var BS_PayTimeCombo = BSForm[id].getCombo("BS_PayTime");
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/modes.php&mode=time"), function (xml) {
                                    Name = BS_PayTimeCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("BS_PayTime", BSForm[id].getUserData("BS_PayTime", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("LC_Id")) {
                                var LC_IdCombo = BSForm[id].getCombo("LC_Id");
                                var lcid = BSForm[id].getUserData("LC_Id", "cId");
                                var lc_name = BSForm[id].getUserData("LC_Id", "cValue");
                                var params = "";
                                if (!lcid) {
                                    params = "&mask=Self";
                                } else {
                                    params = "&mask=" + lc_name;
                                }
                                //console.log(params);
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                                    var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                                    BSForm[id].setItemValue("LC_Id", lcid);
                                });
                            }

                            if (BSForm[id].isItem("BS_IEByLC")) {
                                var BS_IEByLCCombo = BSForm[id].getCombo("BS_IEByLC");
                                var ieByLCid = BSForm[id].getUserData("BS_IEByLC", "cId");
                                var ieByLCname = BSForm[id].getUserData("BS_IEByLC", "cValue");
                                var params = "";
                                if (!ieByLCid || ieByLCid == 0) {
                                    params = "&mask=Self";
                                } else {
                                    params = "&mask=" + ieByLCname;
                                }

                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php" + params), function (xml) {
                                    BS_IEByLCCombo.load(xml.xmlDoc.responseText);
                                    if (ieByLCid)
                                        BSForm[id].setItemValue("BS_IEByLC", ieByLCid);

                                    var ieByUSCombo = BSForm[id].getCombo("BS_IEByUS");
                                    var ieByUSid = BSForm[id].getUserData("BS_IEByUS", "cId");
                                    var ieByUSname = BSForm[id].getUserData("BS_IEByUS", "cValue");
                                    var params = ""; 
                                    if (!ieByUSid) {
                                        params = "&ctype=check&mask=Self&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                    } else {
                                        params = "&ctype=check&mask=" + ieByUSname + "&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                    }

                                    ieByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php" + params));
                                    dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php" + params), function (xml) {
                                        ieByUSCombo.load(xml.xmlDoc.responseText);
                                        if (ieByUSid)
                                            BSForm[id].setItemValue("BS_IEByUS", ieByUSid);
                                    });

                                    BS_IEByLCCombo.attachEvent("onChange", function () {
                                        ieByUSCombo.clearAll();
                                        ieByUSCombo.setComboValue(0);
                                        ieByUSCombo.setComboText('');
                                        alert("Persons");
                                        var params = "&LCId=" + BS_IEByLCCombo.getSelectedValue();
                                        ieByUSCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php&ctype=check" + params), false);
                                    });
                                });
                            }
//                            if (BSForm[id].isItem("BS_IEByUS")) {  
//                                
//                                var ieByUSCombo = BSForm[id].getCombo("BS_IEByUS");                                                              
//                                var ieByUSid    = BSForm[id].getUserData("BS_IEByUS","cId");
//                                var ieByUSname  = BSForm[id].getUserData("BS_IEByUS","cValue");
//                                var params="";
//                                if(!ieByUSid){
//                                    params = "&mask=Self";
//                                }else{
//                                    params = "&mask="+ieByUSname;
//                                }
//                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
//                                    ieByUSCombo.load(xml.xmlDoc.responseText);
//                                    BSForm[id].setItemValue("BS_IEByUS",ieByUSid);
//                                });
//                            }   
                            if (BSForm[id].isItem("BS_PrchsdFor")) {
                                var BS_PrchsdForCombo = BSForm[id].getCombo("BS_PrchsdFor");
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getBranches.php"), function (xml) {
                                    BSForm[id].setItemValue("BS_PrchsdFor", BSForm[id].getUserData("BS_PrchsdFor", "cId"));
                                });
                            }
                            if (BSForm[id].isItem("BS_PaidTo")) {
                                BSForm[id].setReadonly("BS_PaidTo", true);
                            }
                            if (BSForm[id].isItem("BS_RcvdFrm")) {
                                BSForm[id].setReadonly("BS_RcvdFrm", true);
                            }

                            if (BSForm[id].isItem("BS_PettyCashRefId")) {
                                BSForm[id].setItemValue("BS_PettyCashRefId", BSForm[id].getUserData("BS_PettyCashRefId", "cId"));
                                var BS_PCRefidCombo = BSForm[id].getCombo("BS_PettyCashRefId");
                                BS_PCRefidCombo.setOptionWidth(350);

                                BS_PCRefidCombo.attachEvent("onXLE", function () {
                                    BS_PCRefidCombo.deleteOption(id);
                                });
//                                
//                                
//                                BSForm[id].setItemValue("PettyCashAmount",addItemForm.getItemValue("PettyCashAmount"));


//                                $.ajax({
//                                    url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId="+BSForm[id].getUserData("BS_PettyCashRefId","cId"))
//                                }).done(function(data) { 
//                                    BSForm[id].setItemValue("PettyCashAmount",data);
//                                });
                                $.ajax({
                                    url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + id + "&PCRefId=" + BSForm[id].getUserData("BS_PettyCashRefId", "cId"))
                                }).done(function (data) {
                                    BSForm[id].setItemValue("PettyCashAmount", data);
                                });
                                BS_PCRefidCombo.attachEvent("onClose", function () {

                                    $.ajax({
                                        url: preTally.Initialize.encryptURL("warehouse/getAmount.php&BSId=" + id + "&PCRefId=" + BS_PCRefidCombo.getSelectedValue())
                                    }).done(function (data) {
                                        BSForm[id].setItemValue("PettyCashAmount", data);
                                    });
                                });
                            }

                            preTally.BalanceSheet.addBalSheetItem(id);
                        }

                    });

                }, "json");
                /*ptBalSheetTab.attachEvent("onTabClick", function(id, lastId){
                 if(id!="a1"){
                 if(id=="a2"){sortKey="week";}else if(id=="a3"){sortKey="mnt";}else if(id=="a4"){sortKey="year";}
                 
                 dhxGridSavedBSItems = ptBalSheetTab.cells(id).attachGrid();
                 dhxGridSavedBSItems.init();
                 var params = "sortKey=" + sortKey;
                 dhxGridSavedBSItems.loadXML("requisites/listSavedBSItems.php?"+encrypt(params), function() {
                 dhxGridSavedBSItems.enableRowsHover(true, "hover");
                 
                 dhxGridSavedBSItems.attachEvent("onRowSelect", function(id) {
                 
                 if (dhxGridSavedBSItems.doesRowExist(openBSID)) {
                 dhxGridSavedBSItems.cells(openBSID, 0).close();
                 //dhxGridBalSheet.setRowTextNormal(openBSID);
                 dhxGridSavedBSItems.setRowTextStyle(openBSID, "padding-top: 3px;");
                 }
                 
                 if (openBSID == id) {
                 openBSID = 0;
                 } else {
                 dhxGridSavedBSItems.cells(id, 0).open();
                 //dhxGridBalSheet.setRowTextBold(id);
                 dhxGridSavedBSItems.setRowTextStyle(id, "vertical-align: top; padding-top: 3px; border:none;");
                 openBSID = id;
                 }
                 dhxGridSavedBSItems.clearSelection();
                 });
                 });
                 }
                 });*/

            } else {
                var MainCombo = dhxNewBalSheetFormSamp.getCombo("MH_Type");
                var MH_Type = MainCombo.getSelectedValue();
                dhxNewBalSheetFormSamp.reloadOptions("IT_Id", preTally.Initialize.encryptURL("requisites/newBSItems.php&type=" + MH_Type));
                dhxGridBalSheet.updateFromJSON(preTally.Initialize.encryptURL("requisites/listBSItems.php"), true, true, function () { });
//                dhxGridBalSheet.load(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function() {},"json");

                dhxGridBalSheet.enableTooltips("false,false,false,false,false,false,false");

                dhxMiddleBlockTabs.tabs("menuNewBalSheetSample").setActive();
            }
        },
        //List all pending track and updated status to close.
        pendingTrack: function() {
            if (!dhxMiddleBlockTabs.cells("pendingTrack")) {

                dhxMiddleBlockTabs.addTab("pendingTrack", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;My Pending Tracks", 160);
                dhxMiddleBlockTabs.tabs("pendingTrack").setActive();
                // layout define
                pTrackLayout = dhxMiddleBlockTabs.cells("pendingTrack").attachLayout("1C");
                pTrackLayout.cells("a").hideHeader();

                // status bar or pagination and count
                pTrackLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('ptrack'),
                    height: 35
                });                 

                // grid base section declaration 
                pTrackGrid = pTrackLayout.cells("a").attachGrid();
                pTrackGrid.setHeader("Slno, Track No <input type='text' id ='fltPTrack' style='width: 90%;' placeholder='Enter Track No'>\
                ,Track Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='date' class='pTrkGSort' />\
                , Added By <input type='text' id='fltPTUser' style='width: 90%;' placeholder='Enter Staff Name'>, Action");
                pTrackGrid.setInitWidths("75,*,110,*,115");
                pTrackGrid.setColAlign("center,left,center,left,center");
                pTrackGrid.setColTypes("ro,ro,ro,ro,ro");
                pTrackGrid.setColSorting("na,na,na,na,na");             
                pTrackGrid.enableTooltips("false,false,false,false,false");
                // common grid define section
                preTally.Attendance.commonGridDefine(pTrackGrid, 'ptrack');

                // Track name/Number enter filter start
                var filtrInterval;
                $( "#fltPTrack, #fltPTUser" ).keyup(function(value) {
                    if(filtrInterval) clearInterval(filtrInterval);
                    filtrInterval = setInterval( function() { 
                        preTally.BalanceSheet.listPTracks('');
                        clearInterval(filtrInterval); 
                    }, 500);
                });
                $('.pTrkGSort').click(function () {
                    var sfield  = $(this).attr("sortField"); 
                    var sorder  = 'ASC';
                    if ($(this).attr("src") == 'images/icon/sort-descending-icon.png') {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        sorder  = 'ASC';
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        sorder  = 'DESC'; 
                    }
                    var extrafilter = "&sort="+sfield+"&order="+sorder;
                    preTally.BalanceSheet.listPTracks(extrafilter);               
                }); 
                // fetching data from server 
                preTally.BalanceSheet.listPTracks('');
            } else {
                dhxMiddleBlockTabs.tabs("pendingTrack").setActive();
            }
        },
        listPTracks: function(extra) {
            var trackno  = $('#fltPTrack').val();
            var username = $('#fltPTUser').val();            
            pTrackGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listPendingTrack.php&trackno="+trackno+"&staff="+username+extra), function() { 
                $('#total_ptrack').html("# : "+pTrackGrid.getUserData("", "Data_Count")+" ");
                if (pTrackGrid.getUserData("", "track_close_acl") == 0 && pTrackGrid.getUserData("", "track_view_acl") == 0) {
                    $('#fltPTUser').prop('readonly', true);
                    $("#fltPTUser").attr("type", "hidden");
                }
            });
        },
        closeTrack: function(trid) {
            dhtmlx.confirm({
                title: "Confirm",
                type: "confirm-warning",
                ok: "Yes", cancel: "No",
                text: "Do you want to close this track ?",
                callback: function (result) {
                    if (result) {
                        $.post(preTally.Initialize.encryptURL("warehouse/closeTrack.php&trid=" + trid), function (response) {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.status == 1) {
                                dhtmlx.message({ text: jsonResponse.message });
                                preTally.BalanceSheet.listPTracks('');
                            } else {
                                dhtmlx.message({ type: "error", text: jsonResponse.message });
                            }
                        });
                    }
                }
            });
        },
    };
})(jQuery, this);