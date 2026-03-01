;
(function ($, window, undefined) {
    preTally.History = {
        menuListAllOfzItems: function () {

            if (!dhxMiddleBlockTabs.cells('listOfzItem')) {
                dhxMiddleBlockTabs.addTab("listOfzItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage All Items ", 160);
                dhxMiddleBlockTabs.tabs("listOfzItem").setActive();

//                dhxListAllOfzItemsLayout = dhxMiddleBlockTabs.cells("listOfzItem").attachLayout("1C");
//                dhxListAllOfzItemsTab = dhxListAllOfzItemsLayout.cells("a").attachTabbar();
//                dhxListAllOfzItemsTab.addTab("a1", "All Company Items");
//                dhxListAllOfzItemsTab.tabs("a1").setActive();

                listAllOfzItemsGrid = dhxMiddleBlockTabs.cells("listOfzItem").attachGrid();
                listAllOfzItemsGrid.setImagePath("assets/grid/codebase/imgs/");
                listAllOfzItemsGrid.setSkin("dhx_skyblue")
                listAllOfzItemsGrid.setHeader("SlNo, \
                    <select style = 'width:50px;' class='ITHist_SF' id='ieIT_F'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>, \
                    <div id='itmIT_F' style='width: 90%;' placeholder='Item'></div>,\
                    <input type='text'  class='ITHist_TF' id='shIT_F' style='width: 90%;' placeholder='Subhead'>, \
                    <select style = 'width:80px;' class='ITHist_SF' id='mhIT_F'><option value ='0'>All</option><option value ='1'>Direct Income</option><option value ='2'>Direct Expense</option><option value ='3'>Indirect Income</option><option value ='4'>Indirect Expense</option><option value ='5'>Assets</option></select>, \
                    <select style = 'width:80px;' class='ITHist_SF' id='stIT_F'><option value =''>All</option><option value ='0'>Suspeneded</option><option value ='1'>Published</option><option value ='2'>Just Created</option><option value ='3'>Senior Approved</option></select>,");
                listAllOfzItemsGrid.setInitWidths("40,80,*,*,180,100,50")
                listAllOfzItemsGrid.setColAlign("center,left,left,left,left,left,center")
                listAllOfzItemsGrid.setColTypes("ro,ro,ed,combo,ro,combo,ro");
                listAllOfzItemsGrid.setColSorting("na,na,na,na,na,na,na");
                listAllOfzItemsGrid.enableEditEvents(true, true, true);
                listAllOfzItemsGrid.enableTooltips("false,false,false,false,false,false,true");
                listAllOfzItemsGrid.init();
                listAllitmsStatusbar = dhxMiddleBlockTabs.cells("listOfzItem").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='listAllItems_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='listAllItems_paging'></div>",
                    height: 30
                });
                listAllOfzItemsGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                listAllOfzItemsGrid.enablePaging(true, 50, 5, "listAllItems_paging", true);
                listAllOfzItemsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                listAllOfzItemsGrid.setImagePath('assets/grid/codebase/imgs/');
                listAllOfzItemsGrid.enableRowsHover(true, "bonusReportHover");

                var allItemsCombo = new dhtmlXCombo("itmIT_F");
                allItemsCombo.load(preTally.Initialize.encryptURL("requisites/allOfzItemsFilter.php"), function () {
                    allItemsCombo.setPlaceholder('Item ');
                    allItemsCombo.enableFilteringMode("between");
                });
                allItemsCombo.setOptionWidth(250);
                allItemsCombo.attachEvent("onChange", function () {
                    var itmComboVal = allItemsCombo.getSelectedValue();
                    if (!allItemsCombo.getSelectedValue() && allItemsCombo.getComboText())
                        itmComboVal = allItemsCombo.getComboText();
                    $("#itmIT_F").val(itmComboVal);
                    preTally.History.applyFilter_AllOfzItems();
                });

                var filtrInterval;

                preTally.Settings.progressOn(true, dhxLayout, null);
                listAllOfzItemsGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAllOfzItems.php"), function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                    listAllOfzItemsGrid.attachEvent("onRowSelect", function (rowId, Indx) {
                        if (Indx == 6) {
                            preTally.History.viewItemsHistory(rowId);
                        }
                    });
                    $('.listAllItems_cnt_tot').html("# : " + listAllOfzItemsGrid.getUserData("", "TL_Count") + " ");

                    $(".ITHist_TF").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.History.applyFilter_AllOfzItems();
                            clearInterval(filtrInterval);
                        }, 500);
                    });

                    $(".ITHist_SF").change(function () {
                        preTally.History.applyFilter_AllOfzItems();
                    });
                    listAllOfzItemsGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                        if (cInd == '2') {
                            if (stage == '0') {

                            } else if (stage == '2') {
                                if (listAllOfzItemsGrid.cells(rId, 2).getValue() != "") {
                                    $.post(preTally.Initialize.encryptURL("warehouse/updateItemFromgrid.php"), {ItmID: rId, ItmName: listAllOfzItemsGrid.cells(rId, 2).getValue()}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.History.applyFilter_AllOfzItems();
                                    });
                                }else{
                                    dhtmlx.message("Error in editing Item");
                                    return false;
                                }
                            }
                        } else if (cInd == '3')
                        {
                            if (stage == '0') {
                                HistorySubheadCombo = listAllOfzItemsGrid.cells(rId, cInd).getCellCombo();
                                HistorySubheadCombo.clearAll();
                                HistorySubheadCombo.load(preTally.Initialize.encryptURL("requisites/subheadCombo.php"));
                                HistorySubheadCombo.enableFilteringMode("between");
                                HistorySubheadCombo.allowFreeText(false);
                            } else if (stage == '2') {
                                if (listAllOfzItemsGrid.cells(rId, 3).getValue() != "") {
                                    $.post(preTally.Initialize.encryptURL("warehouse/updateItemFromgrid.php"), {ItmID: rId, ItmSubhead: HistorySubheadCombo.getSelectedValue()}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.History.applyFilter_AllOfzItems();
                                    });
                                } else {
                                    dhtmlx.message("Error in editing Subhead");
                                    return false;
                                }
                            }
                        } else if (cInd == '5')
                        {
                            if (stage == '0') {
                                HistoryItemStatusCombo = listAllOfzItemsGrid.cells(rId, cInd).getCellCombo();
                                HistoryItemStatusCombo.clearAll();
                                HistoryItemStatusCombo.addOption([["0", "Suspended"], ["3", "Senior Approved"]]);
                            } else if (stage == '2') {
                                $.post(preTally.Initialize.encryptURL("warehouse/updateItemFromgrid.php"), {ItmID: rId, ItmStatus: HistoryItemStatusCombo.getSelectedValue()}, function (data) {
                                    dhtmlx.message({text: data});
                                    preTally.History.applyFilter_AllOfzItems();
                                });
                            }
                        }

                        return true;
                    });

                });

                var tb_data_txt = ' <div class="tb_data_txt_secl">\ \n\
                                        <div class="npb_cnt_tot"># : 0</div>\
                                    </div>';

                var tbRptObj = dhxMiddleBlockTabs.cells("listOfzItem").attachStatusBar({
                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 23             // custom height
                });

            } else {
                dhxMiddleBlockTabs.tabs("listOfzItem").setActive();
            }

        },
        menuListAllOfzActsEntries: function () {

            if (!dhxMiddleBlockTabs.cells('listOfzActsEntry')) {
                dhxMiddleBlockTabs.addTab("listOfzActsEntry", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;List Accounts Entries ", 200);
                dhxMiddleBlockTabs.tabs("listOfzActsEntry").setActive();


                var filtrInterval;

                listAllOfzActsEntryGrid = dhxMiddleBlockTabs.cells("listOfzActsEntry").attachGrid();
                listAllOfzActsEntryGrid.setImagePath("assets/grid/codebase/imgs/");
                listAllOfzActsEntryGrid.setSkin("dhx_skyblue")
                listAllOfzActsEntryGrid.setHeader("SlNo,\
                    <select style = 'width:60px;' id='ieBS_F' class = 'BSHist_SF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,\
                    <input type='text'  id='itmBS_F' class='BSHist_TF' style='width: 90%;' placeholder='Items'>,\
                    <input type='text'  id='descBS_F' class='BSHist_TF' style='width: 90%;' placeholder='Description'>,\
                    <input type='text'  id='trkIT_F' class='BSHist_TF' style='width: 90%;' placeholder='Track Number'>,Amount,Date,\
                    <div id='brnBS_F' style='width: 90%;' placeholder='Branch'></div>,\
                    <input type='text'  id='addBS_F' class='BSHist_TF' style='width: 90%;' placeholder='Added By'>,\n\
                    <select style = 'width:100px;' id='statBS_F' class = 'BSHist_SF'><option value =''>All</option><option value ='0'>Deleted</option><option value ='1'>Published</option><option value ='2'>Bank Book</option><option value ='3'>Rejected</option></select>");
                listAllOfzActsEntryGrid.setColAlign("center,center,left,left,left,left,right,left,left,left")
                listAllOfzActsEntryGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
//                listAllOfzActsEntryGrid.setColSorting("str,str,str,str,str,str,date,str,str,str")
//                listAllOfzActsEntryGrid.enableTooltips("false,false,false,false,false,false,false,false")
                listAllOfzActsEntryGrid.setColSorting("na,na,na,na,na,na,na,na,na,na");
                listAllOfzActsEntryGrid.setInitWidths("50,100,*,*,100,100,100,100,120,140,");
                listAllOfzActsEntryGrid.init();

                listAllOfzActsEntryGrid.enableSmartRendering(true, 50);
                listAllOfzActsEntryGrid.enableEditEvents(true, false, true);

                var branchCombo = new dhtmlXCombo("brnBS_F");
                branchCombo.load(preTally.Initialize.encryptURL("requisites/locations.php"), function () {
                    branchCombo.setPlaceholder('Branch');
                    branchCombo.setFilterHandler(function (mask, option) {
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                });

                branchCombo.setOptionWidth(180);
                branchCombo.attachEvent("onChange", function () {
                    var brComboVal = branchCombo.getSelectedValue();
                    if (!branchCombo.getSelectedValue() && branchCombo.getComboText())
                        brComboVal = branchCombo.getComboText();
                    $("#brnBS_F").val(brComboVal);
                    preTally.History.applyFilter_AllOfzActsEntry();
                });

                preTally.Settings.progressOn(true, dhxLayout, null);
                listAllOfzActsEntryGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAllOfzActsEntry.php"), function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                    $('.npb_cnt_tot').html("# : " + listAllOfzActsEntryGrid.getUserData("", "TL_Count") + " ");
                    listAllOfzItemsGrid.attachEvent("onRowSelect", function (rowId, Indx) {
                        if (Indx == 6) {
                            preTally.History.viewItemsHistory(rowId);
                        }
                    });

                    $(".BSHist_TF").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);
                        filtrInterval = setInterval(function () {
                            preTally.History.applyFilter_AllOfzActsEntry();
                            clearInterval(filtrInterval);
                        }, 500);
                    });

                    $(".BSHist_SF").change(function () {
                        preTally.History.applyFilter_AllOfzActsEntry();
                    });

                });

                var tb_data_txt = ' <div class="tb_data_txt_secl">\ \n\
                                        <div class="npb_cnt_tot"># : 0</div>\
                                    </div>';

                var tbRptObj = dhxMiddleBlockTabs.cells("listOfzActsEntry").attachStatusBar({
                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 23             // custom height
                });


            } else {
                dhxMiddleBlockTabs.tabs("listOfzActsEntry").setActive();
            }

        },
        applyFilter_AllOfzItems: function () {

            var filterValue = new Array($('#ieIT_F').val(), $('#itmIT_F').val(), $('#shIT_F').val(), $('#mhIT_F').val(), $('#stIT_F').val()); //, $('#aprvlPF').val()

            listAllOfzItemsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);

            listAllOfzItemsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listAllOfzItems.php&filter=" + filterValue), function () {
                $('.listAllItems_cnt_tot').html("# : " + listAllOfzItemsGrid.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyFilter_AllOfzActsEntry: function () {

            var filterValue = new Array($('#ieBS_F').val(), $('#itmBS_F').val(), $('#descBS_F').val(), $('#trkIT_F').val(), $('#brnBS_F').val(), $('#addBS_F').val(), $('#statBS_F').val());

            listAllOfzActsEntryGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);

            listAllOfzActsEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listAllOfzActsEntry.php&filter=" + filterValue), function () {
                $('.npb_cnt_tot').html("# : " + listAllOfzActsEntryGrid.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        viewItemsHistory: function (rId) {
            if (!dhxMiddleBlockTabs.cells('viewItemsHistory')) {
                dhxMiddleBlockTabs.addTab("viewItemsHistory", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Item History ", 200);
                dhxMiddleBlockTabs.tabs("viewItemsHistory").setActive();

//                dhxItemsHistoryLayout = dhxMiddleBlockTabs.cells("viewItemsHistory").attachLayout("1C");
//                dhxItemsHistoryTab = dhxItemsHistoryLayout.cells("a").attachTabbar();
//                dhxItemsHistoryTab.addTab("a1", "All Company Items");
//                dhxItemsHistoryTab.tabs("a1").setActive();

                dhxItemsHistoryForm = dhxMiddleBlockTabs.cells("viewItemsHistory").attachForm();
                dhxItemsHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/ITHistory.php&ITId=" + rId), function () {});

            } else {

                dhxItemsHistoryForm.unload();
                dhxItemsHistoryForm = dhxMiddleBlockTabs.cells("viewItemsHistory").attachForm();
                dhxItemsHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/ITHistory.php&ITId=" + rId), function () {});
                dhxMiddleBlockTabs.tabs("viewItemsHistory").setActive();
            }
        },
        viewActsEntryHistory: function (rId) {
            if (!dhxMiddleBlockTabs.cells('viewActsEntryHistory')) {
                dhxMiddleBlockTabs.addTab("viewActsEntryHistory", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Accounts Entry History ", 220);
                dhxMiddleBlockTabs.tabs("viewActsEntryHistory").setActive();

//                dhxItemsHistoryLayout = dhxMiddleBlockTabs.cells("viewActsEntryHistory").attachLayout("1C");
//                dhxItemsHistoryTab = dhxItemsHistoryLayout.cells("a").attachTabbar();
//                dhxItemsHistoryTab.addTab("a1", "All Company Items");
//                dhxItemsHistoryTab.tabs("a1").setActive();

                dhxItemsHistoryForm = dhxMiddleBlockTabs.cells("viewActsEntryHistory").attachForm();
                dhxItemsHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/BSHistory.php&BSId=" + rId), function () {});

            } else {

                dhxItemsHistoryForm.unload();
                dhxItemsHistoryForm = dhxMiddleBlockTabs.cells("viewActsEntryHistory").attachForm();
                dhxItemsHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/BSHistory.php&BSId=" + rId), function () {});
                dhxMiddleBlockTabs.tabs("viewActsEntryHistory").setActive();
            }
        }
    }
})(jQuery, this);
