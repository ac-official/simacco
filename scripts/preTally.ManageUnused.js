;(function($, window, undefined) {
    preTally.ManageUnused = {
        menuManageUnused : function() {
            if (!dhxMiddleBlockTabs.cells("menuManageUnused")) {
                dhxMiddleBlockTabs.addTab("menuManageUnused", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Unused Items & Descriptions", 290);
                dhxMiddleBlockTabs.tabs("menuManageUnused").setActive();
                
                dhxUnusedItemDescLayout =  dhxMiddleBlockTabs.cells("menuManageUnused").attachLayout("2U");
                dhxUnusedItemDescLayout.cells("a").setWidth(180);
                dhxUnusedItemDescLayout.cells("a").hideHeader();
                itemDescTabbar = dhxUnusedItemDescLayout.cells("b").attachTabbar();
                itemDescTabbar.addTab("manageUnusedItems", "Manage Unused Items");
                itemDescTabbar.addTab("manageUnusedDesc", "Manage Unused Descriptions");
                itemDescTabbar.tabs("manageUnusedItems").setActive();
               
                itemDescTabbar.attachEvent("onSelect", function(id, last_id){ 
                    if(id == 'manageUnusedItems') preTally.ManageUnused.manageUnusedItems();
                    if(id == 'manageUnusedDesc')  preTally.ManageUnused.manageUnusedDescripions();
                    return true;
                });
                preTally.ManageUnused.manageUnusedItems();
            } else {
                dhxMiddleBlockTabs.tabs("menuManageUnused").setActive();
            }
        },
        manageUnusedItems : function() {
            var filtrInterval ;
            dhxUnusedItemsLayout =  itemDescTabbar.cells("manageUnusedItems").attachLayout("1C");
            dhxUnusedItemsLayout.cells("a").hideHeader();
          
            manageItemsGrid = dhxUnusedItemsLayout.cells("a").attachGrid();
            manageItemsGrid.setImagePath("assets/grid/codebase/imgs/");
            manageItemsGrid.setSkin("dhx_skyblue")
            manageItemsGrid.setHeader("SlNo,\
                    <input type='text'  class='text_item_filter' id='itemFilter' style='width: 90%;' placeholder='Item'>,\
                    <input type='text'  class='text_item_filter' id='shFilter' style='width: 90%;' placeholder='Sub Head'>, \
                    <select style = 'width:80px;' class='select_filter' id='mhFilter'>\n\
                        <option value ='0'>All</option>\n\
                        <option value ='1'>Direct Income</option>\n\
                        <option value ='2'>Direct Expense</option>\n\
                        <option value ='3'>Indirect Income</option>\n\
                        <option value ='4'>Indirect Expense</option>\n\
                        <option value ='5'>Assets</option></select>,#master_checkbox");
            manageItemsGrid.setInitWidths("60,*,190,120,120");
            manageItemsGrid.setColTypes("ro,ed,ro,ro,ch");
            manageItemsGrid.setColAlign("center,left,left,left,center");
            manageItemsGrid.enableColSpan(true);
            manageItemsGrid.enableEditEvents(true,false,true);
            manageItemsGrid.enableSmartRendering(true,50);
            manageItemsGrid.init();
            
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            $(".text_item_filter").keyup(function(value) {
                if(filtrInterval) clearInterval(filtrInterval);
                filtrInterval = setInterval( function() { 
                    preTally.ManageUnused.applyItemFilter(); 
                    clearInterval(filtrInterval); 
                }, 500);
            });
            
            $(".select_filter").change(function() {
                preTally.ManageUnused.applyItemFilter(); 
            });
                            
            manageItemsGrid.loadXML(preTally.Initialize.encryptURL("requisites/manageUnusedItems.php"), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                oldItems = [];
                manageItemsGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                    if(stage == 0) {
                        if(cInd == 1) {
                           oldItems[rId] = manageItemsGrid.cells(rId,cInd).getValue();
                        }
                    }
                    if(stage == 2){
                        if(cInd == 1) {
                            newItemName = manageItemsGrid.cells(rId,cInd).getValue();
                            if(newItemName == "") {
                                dhtmlx.message({text: "Item cannot be left empty"});
                                manageItemsGrid.cells(rId,cInd).setValue(oldItems[rId]);
                            }
                            else if(oldItems[rId] != newItemName && newItemName != "") {
                                dhtmlx.confirm({
                                    title: "Confirm Edit",
                                    type:"confirm-warning",
                                    ok  : "Yes", cancel : "No",
                                    text: "Do you want to change the item from "+oldItems[rId]+" to "+newItemName+" ?",
                                    callback: function(response) {    
                                        if(response){
                                            preTally.Settings.progressOn(true, dhxLayout, null);
                                            params="IT_Id="+rId +"&IT_Name="+newItemName+"&type=item";   
                                            $.post(preTally.Initialize.encryptURL("warehouse/unusedItemDesc.php&"+params), function( data ){
                                                if(data == "success") {
                                                    dhtmlx.message({text: "Successfully updated item"});
                                                }
                                                else if(data == "exists") {
                                                    dhtmlx.message({text: "Item already exists. Please try with another."});
                                                    manageItemsGrid.cells(rId,cInd).setValue(oldItems[rId]);
                                                }
                                                preTally.ManageUnused.applyItemFilter(); 
                                            });  
                                        }
                                        else {
                                            manageItemsGrid.cells(rId,cInd).setValue(oldItems[rId]);
                                        }
                                    }
                                }); 
                            }
                        }
                    }
                    return true;
                });
                Unused_ItemCnt = manageItemsGrid.getUserData("","TL_Count");
                deleteButtonBar = dhxUnusedItemsLayout.attachStatusBar({
                    text  : "<div class='unused_items_div'><div class='unused_item_count' id='unused_item_count' style='float:left;'># : "+Unused_ItemCnt+"</div><div class='unused_delete_div' style='float:right;'><input type='button' value='DELETE' onclick='preTally.ManageUnused.deleteItems();' /></div></div>",
                    height: 35
                });
            });
        },
        manageUnusedDescripions : function() {
            var filtrDescInterval ;
            //var TL_Count;
            dhxUnusedDescLayout =  itemDescTabbar.cells("manageUnusedDesc").attachLayout("1C");
            dhxUnusedDescLayout.cells("a").hideHeader();
          
           
            manageDescGrid = dhxUnusedDescLayout.cells("a").attachGrid();
            manageDescGrid.setImagePath("assets/grid/codebase/imgs/");
            manageDescGrid.setSkin("dhx_skyblue")
            manageDescGrid.setHeader("SlNo,\
                    <input type='text'  class='text_desc_filter' id='desc_Filter' style='width: 90%;' placeholder='Description'>,\
                    <input type='text'  class='text_desc_filter' id='item_Filter' style='width: 90%;' placeholder='Item Name'> ,#master_checkbox");
            manageDescGrid.setInitWidths("60,*,260,120");
            manageDescGrid.setColTypes("ro,ed,ro,ch");
            manageDescGrid.setColAlign("center,left,left,center");
            manageDescGrid.enableColSpan(true);
            manageDescGrid.enableEditEvents(true,false,true,false);
            manageDescGrid.enableSmartRendering(true,50);
            manageDescGrid.init();
            
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            $(".text_desc_filter").keyup(function(value) {
                if(filtrDescInterval) clearInterval(filtrDescInterval);
                filtrDescInterval = setInterval( function() { 
                    preTally.ManageUnused.applyDescFilter(); 
                    clearInterval(filtrDescInterval); 
                }, 500);
            });
            
            manageDescGrid.loadXML(preTally.Initialize.encryptURL("requisites/manageUnusedDesc.php"), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                oldDesc = [];
                manageDescGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                    if(stage == 0) {
                        if(cInd == 1) {
                           oldDesc[rId] = manageDescGrid.cells(rId,cInd).getValue();
                        }
                    }
                    if(stage == 2){
                        if(cInd == 1) {
                            newDescName = manageDescGrid.cells(rId,cInd).getValue();
                            if(newDescName == "") {
                                dhtmlx.message({text: "Description cannot be left empty"});
                                manageDescGrid.cells(rId,cInd).setValue(oldDesc[rId]);
                            }
                            else if(oldDesc[rId] != newDescName && newDescName != "") {
                                dhtmlx.confirm({
                                    title: "Confirm Edit",
                                    type:"confirm-warning",
                                    ok  : "Yes", cancel : "No",
                                    text: "Do you want to change the description from "+oldDesc[rId]+" to "+newDescName+" ?",
                                    callback: function(response) {    
                                        if(response){
                                            preTally.Settings.progressOn(true, dhxLayout, null);
                                            params="DS_Id="+rId +"&DS_Description="+newDescName+"&type=desc";   
                                            $.post(preTally.Initialize.encryptURL("warehouse/unusedItemDesc.php&"+params), function( data ){
                                                if(data == "success") {
                                                    dhtmlx.message({text: "Successfully updated description"});
                                                }
                                                else if(data == "exists") {
                                                    dhtmlx.message({text: "Description already exists. Please try with another."});
                                                    manageDescGrid.cells(rId,cInd).setValue(oldDesc[rId]);
                                                }
                                                preTally.ManageUnused.applyDescFilter(); 
                                            });  
                                        }
                                        else {
                                            manageDescGrid.cells(rId,cInd).setValue(oldDesc[rId]);
                                        }
                                    }
                                }); 
                            }
                        }
                    }
                    return true;
                });
                Unused_DescCnt = manageDescGrid.getUserData("","TL_Count");
                deleteDescButtonBar = dhxUnusedDescLayout.attachStatusBar({
                    text  : "<div class='unused_items_div'><div class='unused_item_count' id='unused_dsc_count' style='float:left;'># : "+Unused_DescCnt+"</div><div class='unused_delete_div' style='float:right;'><input type='button' value='DELETE' onclick='preTally.ManageUnused.deleteDesc();' /></div></div>",
                    height: 35
                });
            
            });
        },
        applyItemFilter : function() {
            var filterValue = new Array($.trim($('#itemFilter').val()), $.trim($('#shFilter').val()), $('#mhFilter').val());
            manageItemsGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            manageItemsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/manageUnusedItems.php&filter="+JSON.stringify(filterValue)), function() { 
                $('#unused_item_count').html("# : "+manageItemsGrid.getUserData("","TL_Count"));
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyDescFilter : function() {
            var filterValue = new Array($.trim($('#desc_Filter').val()), $.trim($('#item_Filter').val()));
            manageDescGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            manageDescGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/manageUnusedDesc.php&filter="+JSON.stringify(filterValue)), function() { 
                $('#unused_dsc_count').html("# : "+manageDescGrid.getUserData("","TL_Count"));
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        deleteItems : function() {
            var ItemList = manageItemsGrid.getCheckedRows(4).split(",") ;
            if(ItemList != "") {
                dhtmlx.confirm({
                    title: "Confirm Delete",
                    type:"confirm-warning",
                    ok  : "Yes", cancel : "No",
                    text: "Do you want to delete selected items ?",
                    callback: function(response) {    
                        if(response){
                            $.ajax({
                                type   : "POST",
                                url    : preTally.Initialize.encryptURL("warehouse/unusedItemDesc.php"),
                                data   : "delete_ItemsID="+encodeURIComponent(ItemList)                   
                            }).done(function(data) { 
                                if (data == 'success') {
                                    dhtmlx.message({text: 'Successfully deleted selected items' });
                                    preTally.ManageUnused.applyItemFilter(); 
                                    manageItemsGrid.hdr.rows[1].cells[4].getElementsByTagName("INPUT")[0].checked = false;
                                }else {
                                   dhtmlx.message({text: 'Some error has occured. Please try again.'});
                                }
                            });
                        }
                    }
                }); 
            }
            else {
                dhtmlx.message({text: 'Please select items'});
            }
        },
        deleteDesc : function() {
            var DescList = manageDescGrid.getCheckedRows(3).split(",") ;
            if(DescList != "") { 
                dhtmlx.confirm({
                    title: "Confirm Delete",
                    type:"confirm-warning",
                    ok  : "Yes", cancel : "No",
                    text: "Do you want to delete selected descriptions ?",
                    callback: function(response) {    
                        if(response){
                            $.ajax({
                                type   : "POST",
                                url    : preTally.Initialize.encryptURL("warehouse/unusedItemDesc.php"),
                                data   : "delete_DescID="+encodeURIComponent(DescList)                   
                            }).done(function(data) { 
                                if (data == 'success') {
                                    dhtmlx.message({text: 'Successfully deleted selected descriptions' });
                                    preTally.ManageUnused.applyDescFilter(); 
                                    manageDescGrid.hdr.rows[1].cells[3].getElementsByTagName("INPUT")[0].checked = false;
                                }else {
                                    dhtmlx.message({text: 'Some error has occured. Please try again.'});
                                }
                            });
                        }
                    }
                }); 
            }
            else {
                dhtmlx.message({text: 'Please select descriptions'});
            }
        }
    };
})(jQuery, this);