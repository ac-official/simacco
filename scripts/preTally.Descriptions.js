;
(function ($, window, undefined) {
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var monthData, yearData;
    var descStatusCombo,descAddedByCombo,itemSearchCombo;
    preTally.Descriptions = {        
        viewAllDescriptions:function(){
             if (!dhxMiddleBlockTabs.cells('listAllDescEntry')) {
                dhxMiddleBlockTabs.addTab("listAllDescEntry", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Descriptions&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshTab'/> ", 200);
                dhxMiddleBlockTabs.tabs("listAllDescEntry").setActive();
                dhxMiddleBlockTabs.cells("listAllDescEntry").attachStatusBar({
                    text: "<div class='tb_mng_desc'>\
                                <div style='float:left;font-weight:bold;' class='tb_mng_desc_tot'># : 0</div>\
                            </div><div style='float:right;' id='manageAllDesc_paging'></div>",
                    height: 30
                });
                var filtrInterval;
                listAllDescGrid = dhxMiddleBlockTabs.cells("listAllDescEntry").attachGrid();
                listAllDescGrid.setImagePath("assets/grid/codebase/imgs/");
                listAllDescGrid.setSkin("dhx_skyblue");
                listAllDescGrid.setHeader("Slno,Descriptions,Item,Added By,Status,");
                listAllDescGrid.attachHeader(",<input id='msg_desc_desc_fltr' style='width:90%;' class='mngDescAll'/>,<div id='msg_desc_itm_fltr' style='width:90%;' ></div>,<div id='msg_desc_addedby_fltr' style='width:90%;'></div>,<select id='msg_desc_stat_fltr' style='width:90%;'><option value='' selected='true'>All</option> <option value='0'>Suspended</option> <option value='1'>Published</option> <option value='2'>Notification</option> <option value='3'>Semi Approved</option></select>,");
                listAllDescGrid.setInitWidths("50,*,*,120,80,25")
                listAllDescGrid.setColAlign("center,left,left,left,center,center")
                listAllDescGrid.setColTypes("ro,ed,ro,ro,combo,ro");
                listAllDescGrid.setColSorting("na,na,na,na,na,na");
                listAllDescGrid.enableTooltips("false,false,false,false,false,false");
                listAllDescGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                listAllDescGrid.enablePaging(true, 50, 5, "manageAllDesc_paging", true);
                listAllDescGrid.setPagingSkin("toolbar", "dhx_skyblue");
                listAllDescGrid.enableEditEvents(true, false, true);
                listAllDescGrid.enableColSpan(true);
                listAllDescGrid.enableRowsHover(true, "bonusReportHover");
                listAllDescGrid.init();
                listAllDescGrid.attachEvent("OnXLS",function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                })
                listAllDescGrid.attachEvent("OnXLE",function(){
                    preTally.Settings.progressOff(true, dhxLayout, null);
                })
                var filtrInterval;
                $(".refreshTab").click(function () {
                    preTally.Descriptions.applyAllDescFilter();
                });
                $(".mngDescAll" ).keyup(function() {     
                    console.log("asdasd");
                    if(filtrInterval) clearInterval(filtrInterval);                    
                    filtrInterval = setInterval( function() { 
                        preTally.Descriptions.applyAllDescFilter();
                        clearInterval(filtrInterval); 
                    }, 500);
                }); 
                itemSearchCombo = new dhtmlXCombo("msg_desc_itm_fltr");
                itemSearchCombo.load(preTally.Initialize.encryptURL("requisites/allOfzItemsFilter.php&type=AllItems"), function () {
                    itemSearchCombo.setPlaceholder('Item ');
                    itemSearchCombo.enableFilteringMode("between");
                });
                itemSearchCombo.setOptionWidth(250); 
                descAddedByCombo = new dhtmlXCombo("msg_desc_addedby_fltr");
                descAddedByCombo.load(preTally.Initialize.encryptURL("requisites/usersCombo.php&type=filt"));   
                descAddedByCombo.enableFilteringMode('between');
                descAddedByCombo.attachEvent("onChange",function(){
                   preTally.Descriptions.applyAllDescFilter(); 
                });
                 itemSearchCombo.attachEvent("onChange",function(){
                   if(itemSearchCombo.getSelectedText()==""){
                       itemSearchCombo.setComboValue('0');
                   }  
                   preTally.Descriptions.applyAllDescFilter(); 
                });
                $("#msg_desc_stat_fltr").change(function(){
                     preTally.Descriptions.applyAllDescFilter();
                });
                /*descStatusCombo = new dhtmlXCombo("msg_desc_stat_fltr");
                descStatusCombo.addOption([
                                    ["0","Suspended"],
                                    ["1","Published"],
                                    ["2","Notification"],
                                    ["3","Semi Approved"]                                    
                                ]); 
                descStatusCombo.attachEvent("onChange",function(){
                   preTally.Descriptions.applyAllDescFilter(); 
                });     */
                listAllDescGrid.attachEvent("onRowSelect", function (rowId, Indx) {
                        if (Indx == 5) {
                            preTally.Descriptions.viewDescHistory(rowId);
                        }
                    });
                listAllDescGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
                    if(stage==0){
                        if(cInd==4){
                            DescStatus = listAllDescGrid.cells(rId, cInd).getCellCombo();
                            DescStatus.clearAll();
                             DescStatus.addOption([
                                    ["0","Suspended"],                                    
                                    ["3","Senior Approved"],
                                    ["4","Deleted"]
                                ]); 
                        }
                    }
                    if(stage==2)
                    {
                        if(cInd==1){
                            if(listAllDescGrid.cells(rId,1).getValue()!=""){
                            $.post(preTally.Initialize.encryptURL("warehouse/updateDescriptGrid.php"), {DS_Id: rId, DescName:listAllDescGrid.cells(rId,1).getValue()}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.Descriptions.applyAllDescFilter(); 
                                    });  
                                }else{
                                    dhtmlx.message("Enter Description");
                                    return false;
                                }
                        }
                        else if(cInd==4){
                           $.post(preTally.Initialize.encryptURL("warehouse/updateDescriptGrid.php"), {DS_Id: rId, DescStatus: DescStatus.getSelectedValue()}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.Descriptions.applyAllDescFilter(); 
                                    });  
                        }
                    }
                });
                preTally.Settings.progressOn(true, dhxLayout, null);
                listAllDescGrid.load(preTally.Initialize.encryptURL("requisites/manageAllDescriptions.php&type=filt"),function(){
                    console.log(listAllDescGrid.getUserData("","DS_Count"));
                    $(".tb_mng_desc_tot").html("#"+listAllDescGrid.getUserData("","DS_Count"));
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
            } else {
                dhxMiddleBlockTabs.tabs("listAllDescEntry").setActive();
            }
        },applyAllDescFilter:function(){
            preTally.Settings.progressOn(true, dhxLayout, null);
            var descFilter="&descFilter="+$("#msg_desc_desc_fltr").val()+"&itmFilter="+itemSearchCombo.getSelectedValue()+"&addedByFilter="+descAddedByCombo.getSelectedValue()+"&status="+$("#msg_desc_stat_fltr").val();
            listAllDescGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/manageAllDescriptions.php"+descFilter),function(){                    
                    $(".tb_mng_desc_tot").html("#"+listAllDescGrid.getUserData("","DS_Count"));
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
        }, viewDescHistory: function (rId) {
            if (!dhxMiddleBlockTabs.cells('viewDescHistory')) {
                dhxMiddleBlockTabs.addTab("viewDescHistory", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Description History ", 200);
                dhxMiddleBlockTabs.tabs("viewDescHistory").setActive();

                dhxDescHistoryForm = dhxMiddleBlockTabs.cells("viewDescHistory").attachForm();
                dhxDescHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/DescHistory.php&DSId=" + rId), function () {});

            } else {

                dhxDescHistoryForm.unload();
                dhxDescHistoryForm = dhxMiddleBlockTabs.cells("viewDescHistory").attachForm();
                dhxDescHistoryForm.loadStruct(preTally.Initialize.encryptURL("requisites/DescHistory.php&DSId=" + rId), function () {});
                dhxMiddleBlockTabs.tabs("viewDescHistory").setActive();
            }
        },
    };
})(jQuery, this);