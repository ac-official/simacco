;(function ($, window, undefined) {
    
    var itemNotfCombo;
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
                //preTallyNotfPop.attachList("item,count", notfData);
                preTallyNotfPop.show(preTallyNotfPop_X, 0, 100, 30);
                
            }    
        },
        countNotification : function(){
            
            var notfInterval;
            var pageGenID = 0;
            
            var notfPoll = function(){
                if(errorHandlerFlag == true) {
                    //console.log('Am Out');
                    return;
                }
            
                $.post(preTally.Initialize.encryptURL("requisites/countNotification.php"), 
                { lastupdate: 1 }, 
                processUpdate, 'html');
            }
            
            var processUpdate = function( response,status ) {
                
                //refreshPrd = 3000;                
                var notfResponse = $.parseJSON( response );
                if ( pageGenID != response ) {   
                    
                    if(notfResponse[3] != null){
                        dhxToolbar.setItemImage("notifications", "notification.png");
                        let tries = 0;
                        let maxTries = 10;
                        let checkMenuIconInterval = setInterval(function () {
                            let encodedImageName = encodeURIComponent(notfResponse[3]); // e.g. 9+ => 9%2B
                            let $img = $(`img[src$='images/icon/notification.png'][class='dhtmlxMenu_TopLevel_Item_Icon']`);
                            if ($img.length > 0 || tries >= maxTries) {
                                clearInterval(checkMenuIconInterval);
                            }

                            if ($img.length > 0 && notfResponse[3]!=0) {
                                // Get the div.right after the image
                                let $targetTextDiv = $img.next("div.top_level_text");

                                if ($targetTextDiv.length > 0 && $("#followup_noti_bubble_text").length === 0) {
                                    console.log('Appending badge');

                                    $targetTextDiv.append(`
                                        <div id="followup_noti_bubble_text" class="support_noti_bubble_menu blink-image">
                                            ${notfResponse[3]}
                                        </div>
                                    `);
                                } else {
                                    console.log('Text div not found or badge already exists');
                                }
                            } else {
                                console.log('Waiting for image...');
                            }
                            tries++;
                        }, 300);                    
//                    notfData = [{
//                        id      : "NI",
//                        item    : "New Items",
//                        count   : '<b style="color:#EC1D25;">'+notfResponse[0]+'</b>'
//                    }, preTallyNotfPop.separator, {
//                        id      : "ND",
//                        item    : "New Description",
//                        count   : '<b style="color:#EC1D25;">'+notfResponse[1]+'</b>'
//                    }];  

                    var ACL_LC      = unescape(JGG1P3bDnUSDL7Mui7KzYjj28UjPdWxCCtGkJSHeuo); 
                    var ACL_BNK     = unescape(JGG1P3bDnUSDL8Mui7KzYjj28UjPdWxCCtGkJSHeuo);   
                    
                    if(ACL_LC == 1 || ACL_BNK == 1){
                        if(notfResponse[3] == null) notfResponse[3] = 0;
                        notfData = [{
                            id      : "NI",
                            item    : "New Items & Descriptions",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[0]+'</b>'
                        },{
                            id      : "MSG",
                            item    : "My Messages",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[1]+'</b>'
                        },{
                            id      : "NPT",
                            item    : "Payment Notifications",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[2]+'</b>'
                        },{
                            id      : "PL",
                            item    : "Leave Applications",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[4]+'</b>'
                        },{
                            id      : "LE",
                            item    : "Late Entry Requests",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[5]+'</b>'
                        }];
                    } else {
                        if(notfResponse[3] == null) notfResponse[3] = 0;
                        notfData = [{
                            id      : "NI",
                            item    : "New Items & Descriptions",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[0]+'</b>'
                        },{
                            id      : "MSG",
                            item    : "My Messages",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[1]+'</b>'
                        },{
                            id      : "PL",
                            item    : "Leave Applications",
                            count   : '<b style="color:#EC1D25;">'+notfResponse[4]+'</b>'
                        }];
                    }
                
                    pageGenID = response;
                    if (preTallyNotfPop.isVisible()) {
                        preTallyNotfPop.attachList("item,count", notfData);
                        preTallyNotfPop.show(preTallyNotfPop_X, 0, 100, 30);
                    }  
//                    if (dhxMiddleBlockTabs.cells("menuNotfItem")) {
//                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
//                            var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
//                        } else {
//                            var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
//                        }
//
//                        listItemNoftGrid.clearAll();
//                        preTally.Settings.progressOn(true, dhxLayout, null);
//
//                        listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
//                            $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
//                            preTally.Settings.progressOff(true, dhxLayout, null);
//                        });
////                        listItemNoftGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php"), true, true, function() {
////                            $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
////                            preTally.Notification.NoftItem();       
////                        });
//                    }
//                    if (dhxMiddleBlockTabs.cells("menuNotfDesc")) {
//                        listDescNoftGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listNotfDesc.php"), true, true, function() {
//                            preTally.Notification.NoftDesc();       
//                        });
//                    }
                    }
                }
                //$('title').text(ptTitle + ' ('+response+')');
            }
            notfPoll();
            notfInterval = setInterval( function() { notfPoll();  }, 60000); // 1 Minute Delay

            setIdleTimeout(120000); // 2 Minute
            setAwayTimeout(180000); // 3 Minute
            document.onIdle = function() {
                clearInterval(notfInterval);
                notfInterval = setInterval( function() { notfPoll();  }, 120000); // Delay 2 Minute
                //console.log('idle');
            }
            document.onAway = function() {
                clearInterval(notfInterval);
                notfInterval = setInterval( function() { notfPoll();  }, 180000); // Delay 3 Minute
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
                    var filtrInterval ;
                    if (!dhxMiddleBlockTabs.cells("menuNotfItem")) {
                        dhxMiddleBlockTabs.addTab("menuNotfItem", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Item Notifications", 200);
                        dhxMiddleBlockTabs.tabs("menuNotfItem").setActive();
                        
                        var dhxNotificationLayout = dhxMiddleBlockTabs.cells("menuNotfItem").attachLayout("1C");
                        
                        
//                        dhxNotfTlbr = dhxNotificationLayout.cells("a").attachToolbar();
//                        dhxNotfTlbr.setIconsPath("images/icon/");
//                        dhxNotfTlbr.addText('notificationToolbar', '1', '<div><input type="radio" class = "itmRadio" name="itmRadio" value="allItems">View All Items<input type="radio" class = "itmRadio" name="itmRadio" value="newItems">View Newly added Items</div>' );
//                
                        dhxNotificationTab = dhxNotificationLayout.cells("a").attachTabbar();
                        dhxNotificationTab.addTab("a1", "All Notification Items");
                        dhxNotificationTab.tabs("a1").setActive();
                        
                        dhxNotfTlbr = dhxNotificationTab.cells("a1").attachToolbar();
                        dhxNotfTlbr.setIconsPath("images/icon/");
                        dhxNotfTlbr.addText('notificationToolbar', '1', '<div id="itmRadio"><input type="radio" class = "itmRadio" name="itmRadio" value="allItems" checked="true">View All Items<input type="radio" class = "itmRadio" name="itmRadio" value="newItems" ><span style="margin-top:5px;">View Newly Added Items</span></div>' );
//                
                        notfButtonBar = dhxNotificationTab.tabs("a1").attachStatusBar({
                            text  : "<div class='tb_data_txt_secl'>\
                                        <div class='nt_cnt_tot'># : 0</div>\
                                    </div>\
                                    <div class='pagingDiv'><span id='notifAllItems'></span></div>\
                                    <input type='button' value='SAVE' onclick='preTally.Notification.notificationSave();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });

                        listItemNoftGrid = dhxNotificationTab.cells("a1").attachGrid();
//                        listItemNoftGrid.loadXML("requisites/listItemDescNotifications.php");
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                dhxNotificationTab.addTab("a2", "Pending Subheads");
                                listPendingSHGrid = dhxNotificationTab.cells("a2").attachGrid();
                        }
                        
                        dhxNotificationTab.attachEvent("onTabClick", function(id, lastId){
                            if(id == 'a2') preTally.Notification.viewPendingSHItems();
                        });
                        
                        $( ".itmRadio" ).change(function() {
//                            console.log($(this).val());
                            $( "#itmRadio" ).val($(this).val());
                            preTally.Notification.applyNotficationFilter(); 
                        });
                            
                        preTally.Settings.progressOn(true,dhxLayout, null);
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            listItemNoftGrid.enableColSpan(true);
                            listItemNoftGrid.enableEditEvents(true,true,true);
                            listItemNoftGrid.enableTooltips("true,false,false,false,false,false,false,false,false,false,false,false");
                        } else {
                            listItemNoftGrid.enableColSpan(true);
                            listItemNoftGrid.enableEditEvents(true,true,true);
                            listItemNoftGrid.enableTooltips("true,false,false,false,false,false,false,false,false");
                        }
                        
 
                        
                        //listItemNoftGrid.enableSmartRendering(true,50);
                        listItemNoftGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
                        listItemNoftGrid.enablePaging(true,50,5,'notifAllItems',false);
                        listItemNoftGrid.setPagingSkin("toolbar");
                        
                        listItemNoftGrid.init();

                        $.ajax({
                            url : preTally.Initialize.encryptURL("requisites/itemsFilter.php&type=filt")
                        }).done(function(data) {
                            itemsFilterCombo = data;
                                
                        });
                        listItemNoftGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php"), function() {
                            preTally.Settings.progressOff(true,dhxLayout, null);
                            $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                            
                            itemNotfCombo = new dhtmlXCombo("itmF");
                            itemNotfCombo.load(itemsFilterCombo, function(){
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
                            itemNotfCombo.setOptionWidth(350);
                            itemNotfCombo.attachEvent("onChange", function() {
                                var itmComboVal = itemNotfCombo.getSelectedValue();
                                if(!itemNotfCombo.getSelectedValue() && itemNotfCombo.getComboText()) itmComboVal = itemNotfCombo.getComboText();
                                $( "#itmF" ).val(itmComboVal);
                                preTally.Notification.applyNotficationFilter();
                            });

                            var branchCombo = new dhtmlXCombo("brnF");
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
                                $("#brnF" ).val(brComboVal);
                                preTally.Notification.applyNotficationFilter();
                            });

                            var addedByCombo = new dhtmlXCombo("addF");
                            var approvedByCombo = new dhtmlXCombo("aprF");
                            var approvalCombo = new dhtmlXCombo("aprvlF");

                            $.ajax({
                                url : preTally.Initialize.encryptURL("requisites/usersReportsMe.php&type=filt")
                            }).done(function(data) {
                                var UserlistCombo = data;

    //                            var addedByCombo = new dhtmlXCombo("addF");
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
                                    $( "#addF" ).val(adComboVal);
                                    preTally.Notification.applyNotficationFilter();
                                });

    //                            var approvedByCombo = new dhtmlXCombo("aprF");
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
                                    $( "#aprF" ).val(aprComboVal);
                                    preTally.Notification.applyNotficationFilter();
                                });

    //                            var approvalCombo = new dhtmlXCombo("aprvlF");
                                approvalCombo.load(UserlistCombo, function(){
                                    approvalCombo.setPlaceholder('Waiting For');
                                    approvalCombo.setFilterHandler(function(mask, option){
                                        var r = false;
                                        if (mask.length == 0) {
                                                r = true;
                                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                                r = true;
                                        }
                                        return r;
                                    });
                                });
                                approvalCombo.setOptionWidth(180);
                                approvalCombo.attachEvent("onChange", function() {
                                    var aprComboVal = approvalCombo.getSelectedValue();
                                    if( !approvalCombo.getSelectedValue() && approvalCombo.getComboText()) aprComboVal = approvalCombo.getComboText();
                                    $( "#aprvlF" ).val(aprComboVal);
                                    preTally.Notification.applyNotficationFilter();
                                });

                            });
                            
                            $( ".text_filter" ).keyup(function() {
                                if(filtrInterval) clearInterval(filtrInterval);

                                filtrInterval = setInterval( function() { 
                                    preTally.Notification.applyNotficationFilter(); 
                                    clearInterval(filtrInterval); 
                                }, 500);
                            });
                              
                            $( ".select_filter" ).change(function() {
                                preTally.Notification.applyNotficationFilter(); 
                            });
                        
                        });
                
                                            
                        var itemIDs = new Array();

                        listItemNoftGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                            if(stage == 0) {
                                if(cInd == 2){
                                    var entryTypeCombo  = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                    var optnCnt = entryTypeCombo.getOptionsCount();

                                    if(optnCnt == 0) {
                                        entryTypeCombo.addOption([
                                            ["1","Income"],
                                            ["2","Expense"],
                                        ]);

                                        entryTypeCombo.attachEvent("onChange", function() {
                                            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                    if(itemIDs.indexOf(rId) >= 0) {
                                                        listItemNoftGrid.cells(rId,5).setValue('');
                                                        listItemNoftGrid.cells(rId,6).setValue('');
                                                        listItemNoftGrid.setUserData(rId,"MH_Type",entryTypeCombo.getSelectedValue());  
                                                    } else {
                                                         itemIDs.push(rId);
                                                    }
                                            }
                        
//                                            if(itemIDs.indexOf(rId) >= 0) {
//                                                listItemNoftGrid.cells(rId,5).setValue('');
//                                                listItemNoftGrid.cells(rId,6).setValue('');
//                                                listItemNoftGrid.setUserData(rId,"MH_Type",entryTypeCombo.getSelectedValue());  
//                                            } else {
//                                                 itemIDs.push(rId);
//                                            }
                                        });
                                    }

                                } else if(cInd == 3){
                                    
                                    var MHComboType = listItemNoftGrid.getUserData(rId, "MH_Type");
                                    var ITColCombo  = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                    var params = "type=" + MHComboType;
                                    ITColCombo.clearAll();
                                    ITColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/ItemsCombo.php&"+ params));
                                    ITColCombo.setOptionWidth(310);

                                    ITColCombo.attachEvent("onChange", function(loader, response) {
                                        listItemNoftGrid.setUserData(rId,"IT_Id",ITColCombo.getSelectedValue());
                                    });
                                } else if(cInd == 4){
                                    
                                    var ITComboType  = listItemNoftGrid.getUserData(rId, "IT_Id");//listItemNoftGrid.cellById(rId, 2).getAttribute("id");
                                    var DSColCombo   = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                    var params = "IT_Id=" +ITComboType+"&filter=1";
                                    DSColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/descriptions.php&"+ params));
                                    DSColCombo.setOptionWidth(310);
                                    
                                } else if(cInd == 5){
                                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                        var MHComboType  = listItemNoftGrid.getUserData(rId, "MH_Type"); // listItemNoftGrid.cellById(rId, 1).getAttribute("id");
                                        var SHColCombo   = listItemNoftGrid.cells(rId,cInd).getCellCombo();
                                        SHColCombo.clearAll();
                                        var params = "type=" +MHComboType+ "&for=notfEdit";
                                        SHColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                                        SHColCombo.setOptionWidth(310);
                                        var onChgEvnt = SHColCombo.attachEvent("onChange", function(value, text) {

                                            var jsonResponse = SHColCombo.getSelectedValue();
                                            var obj = jQuery.parseJSON( jsonResponse );
                                            if(jsonResponse) {
                                                listItemNoftGrid.cells(rId,6).setValue(obj.MHName);
                                                SHColCombo.setComboValue(obj.SHId);
                                                SHColCombo.detachEvent(onChgEvnt);
                                            } else {
                                                listItemNoftGrid.cells(rId,6).setValue('');
                                            }
                                        });
                                    }
                                }
                            } else if(stage == 2){//console.log(nValue+"--"+oValue);
                                if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                    if(oValue != nValue) listItemNoftGrid.cells(rId,12).setValue(1);
                                } else {
                                    if(oValue != nValue) listItemNoftGrid.cells(rId,9).setValue(1);
                                }
                                if(!nValue) { dhtmlx.message({text: '<b style="color:#FF0000;">Item ' + listItemNoftGrid.cells(rId,2).getValue() + ' is Error. Please Verify.</b>'});}
                                else{
                                    var ITstat = listItemNoftGrid.getUserData(rId, "IT_Status");
                                    var IT_ACL = listItemNoftGrid.getUserData(rId, "IT_ACL");
                                    var IT_OFz = listItemNoftGrid.getUserData(rId, "OF_Id");
                                    var Self_OFz = listItemNoftGrid.getUserData(rId, "Self_OFId");
                                    
    //                                if(cInd == 2 && nValue != oValue && ITstat != 1){ && ITstat == 1
                                    if(( !( $.isNumeric( listItemNoftGrid.cells(rId,3).getValue() ) ) && ITstat == 1 && IT_ACL == 1 && Self_OFz == IT_OFz && nValue != oValue && cInd == 3 && nValue != '') || (ITstat != 1 && nValue != oValue  && cInd == 3 && nValue != '') || ( ITstat != 1 && IT_ACL != 1  && nValue != oValue  && cInd == 3 && nValue != '') ){
                                        
                                        if(dhxWins.isWindow('ConfBox_3Button')) dhxWins.window('ConfBox_3Button').close();
                                        ConfBox_3Button = dhxWins.createWindow('ConfBox_3Button', 400, 100, 500, 150);
                                        ConfBox_3Button.setText("Approve Item "+listItemNoftGrid.cells(rId,cInd).getText());
                                        ConfBox_3Button.button("minmax1").hide();
                                        ConfBox_3Button.button("minmax2").hide();
                                        ConfBox_3Button.button("park").hide();
                                        ConfBox_3Button.button("close").hide();
                                        ConfBox_3Button.setIconCss("flag_blue");
                                        ConfBox_3Button.setModal(true);
                                        ConfBox_3Button.centerOnScreen();
                                        $('.dhxwin_brd').addClass('windowNoBorder');      
                                        ConfBox_3Button.attachEvent("onClose", function(win){
                                            $('.dhxwin_brd').removeClass('windowNoBorder'); 
                                            return true;
                                        });                            

                                        var formData = [
                                            {type: "settings", position: "label-left", labelWidth: 0, inputWidth: 480},
                                            {type: "template", name: "product", labelHeight: 50, label: ":", value: "Do You Want To Change All " + "'<b>" + listItemNoftGrid.getUserData(rId, "IT_Name_Old") + "</b>'" + " item to " + "'<b>" + listItemNoftGrid.cells(rId,cInd).getText() + "</b>'" +" ?" },
                                            {type: "block", width: 480, list:[{
                                                    type		: "button", 
                                                    value		: "<b>Change All</b>", 
                                                    name		: "change_all",
                                                    offsetLeft          : 0,
                                                },{
                                                    type:"newcolumn"
                                                },{
                                                    type		: "button", 
                                                    value		: "<b>Change This Only</b>", 
                                                    name		: "change_this_only",
                                                },{
                                                    type:"newcolumn"
                                                },{
                                                    type		: "button", 
                                                    value		: "<b>Cancel Selection</b>", 
                                                    name		: "cancel_selection",
                                                }]
                                            }
                                        ];

                                        var ConfBox_3ButtonForm = ConfBox_3Button.attachForm();
                                        ConfBox_3ButtonForm.loadStruct(formData);
                                        
                                        ConfBox_3ButtonForm.attachEvent("onButtonClick", function(name){
                                            if(name === 'change_all') {
                                                $.post(
                                                        preTally.Initialize.encryptURL('warehouse/approveMultipleItem.php'),
                                                        { IT_Id : listItemNoftGrid.cells(rId,cInd).getValue() , IT_Name : listItemNoftGrid.cells(rId,cInd).getText(), IT_Id_Old : listItemNoftGrid.getUserData(rId, "IT_Id_Old"), IT_Status : listItemNoftGrid.getUserData(rId, "IT_Status")  },
                                                        function(responseText) {

                                                            itemNotfCombo.clearAll();
                                                            var params = "IT_Id="+$('#itmF').val();
                                                            itemNotfCombo.load(preTally.Initialize.encryptURL("requisites/itemsFilter.php&"+params));

                                                            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            } else {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            }

                                                            listItemNoftGrid.clearAll();
                                                            preTally.Settings.progressOn(true, dhxLayout, null);

                                                            listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                                                                $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                                                                preTally.Settings.progressOff(true, dhxLayout, null);
                                                            });

                                                            dhtmlx.message({text: responseText });

                                                        }
                                                    ); 
                                                dhxWins.window('ConfBox_3Button').close();
                                            }
                                            if(name === 'change_this_only') {
                                                dhxWins.window('ConfBox_3Button').close();
                                            }
                                            if(name === 'cancel_selection') {
                                                if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                    var column = 12;
                                                } else {
                                                    var column = 9;
                                                }
                                                listItemNoftGrid.cells(rId,column).setValue(0)
                                                listItemNoftGrid.cells(rId,cInd).setValue(oValue);
                                                dhxWins.window('ConfBox_3Button').close();
                                            }
                                        });
                                        
                                        
                                        
                                        
                                        /*
                                        
                                        
                                        dhtmlx.confirm({
                                            title: "Approve Item "+listItemNoftGrid.cells(rId,cInd).getText(),
                                            //type:"confirm-warning",
                                            ok  : "Change All", cancel : "Change This Only",
                                            text: "Do You Want To Change All " + "'" + listItemNoftGrid.getUserData(rId, "IT_Name_Old") + "'" + " item to " + "'" + listItemNoftGrid.cells(rId,cInd).getText() + "'" +" ?",
                                            callback: function(response) {
                                                if(response){
                                                    $.post(
                                                        preTally.Initialize.encryptURL('warehouse/approveMultipleItem.php'),
                                                        { IT_Id : listItemNoftGrid.cells(rId,cInd).getValue() , IT_Name : listItemNoftGrid.cells(rId,cInd).getText(), IT_Id_Old : listItemNoftGrid.getUserData(rId, "IT_Id_Old"), IT_Status : listItemNoftGrid.getUserData(rId, "IT_Status")  },
                                                        function(responseText) {

                                                            itemNotfCombo.clearAll();
                                                            var params = "IT_Id="+$('#itmF').val();
                                                            itemNotfCombo.load(preTally.Initialize.encryptURL("requisites/itemsFilter.php&"+params));

                                                            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            } else {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            }

                                                            listItemNoftGrid.clearAll();
                                                            preTally.Settings.progressOn(true, dhxLayout, null);

                                                            listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                                                                $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                                                                preTally.Settings.progressOff(true, dhxLayout, null);
                                                            });

                                                            dhtmlx.message({text: responseText });

                                                        }
                                                    );  
                                                }
                                            }
                                        });
                                        
                                        */
                                    }
                                    var DSstat = listItemNoftGrid.getUserData(rId, "DS_Status");
                                    var IT_ACL = listItemNoftGrid.getUserData(rId, "IT_ACL");
                                    if(( !( $.isNumeric( listItemNoftGrid.cells(rId,4).getValue() ) ) && DSstat == 1 && IT_ACL == 1 && nValue != oValue && cInd == 4 ) || (DSstat != 1 && nValue != oValue  && cInd == 4 ) || ( DSstat != 1 && IT_ACL != 1  && nValue != oValue  && cInd == 4 ) ){
    //                                
    //                                if(cInd == 3 && nValue != oValue){// && DSstat != 1
    
                                            if(dhxWins.isWindow('ConfBox_3Button')) dhxWins.window('ConfBox_3Button').close();
                                            ConfBox_3Button = dhxWins.createWindow('ConfBox_3Button', 400, 100, 500, 150);
                                            ConfBox_3Button.setText("Approve Item "+listItemNoftGrid.cells(rId,cInd).getText());
                                            ConfBox_3Button.button("minmax1").hide();
                                            ConfBox_3Button.button("minmax2").hide();
                                            ConfBox_3Button.button("park").hide();
                                            ConfBox_3Button.button("close").hide();
                                            ConfBox_3Button.setIconCss("flag_blue");
                                            ConfBox_3Button.setModal(true);
                                            ConfBox_3Button.centerOnScreen();
                                            $('.dhxwin_brd').addClass('windowNoBorder');      
                                            ConfBox_3Button.attachEvent("onClose", function(win){
                                                $('.dhxwin_brd').removeClass('windowNoBorder'); 
                                                return true;
                                            });                            

                                            var formData = [
                                                {type: "settings", position: "label-left", labelWidth: 0, inputWidth: 480},
                                                {type: "template", name: "product", labelHeight: 50, label: ":", value: "Do You Want To Change All " + "'<b>" + listItemNoftGrid.getUserData(rId, "DS_Description_Old") + "</b>'" + " description to " + "'<b>" + listItemNoftGrid.cells(rId,cInd).getText() + "</b>'" +" ?" },
                                                {type: "block", width: 480, list:[{
                                                        type		: "button", 
                                                        value		: "<b>Change All</b>", 
                                                        name		: "change_all",
                                                        offsetLeft          : 0,
                                                    },{
                                                        type:"newcolumn"
                                                    },{
                                                        type		: "button", 
                                                        value		: "<b>Change This Only</b>", 
                                                        name		: "change_this_only",
                                                    },{
                                                        type:"newcolumn"
                                                    },{
                                                        type		: "button", 
                                                        value		: "<b>Cancel Selection</b>", 
                                                        name		: "cancel_selection",
                                                    }]
                                                }
                                            ];

                                            var ConfBox_3ButtonForm = ConfBox_3Button.attachForm();
                                            ConfBox_3ButtonForm.loadStruct(formData);
                                            
                                            ConfBox_3ButtonForm.attachEvent("onButtonClick", function(name){
                                                if(name === 'change_all') {
                                                    $.post(
                                                        preTally.Initialize.encryptURL('warehouse/approveMultipleDescription.php'),
                                                        { DS_Id : listItemNoftGrid.cells(rId,cInd).getValue() , DS_Description : listItemNoftGrid.cells(rId,cInd).getText(), DS_Id_Old : listItemNoftGrid.getUserData(rId, "DS_Id_Old") },
                                                        function(responseText) {

                                                            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            } else {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            }

                                                            listItemNoftGrid.clearAll();
                                                            preTally.Settings.progressOn(true, dhxLayout, null);

                                                            listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                                                                $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                                                                preTally.Settings.progressOff(true, dhxLayout, null);
                                                            });

                                                            dhtmlx.message({text: responseText });

                                                        }
                                                    );  
                                          
                                                    dhxWins.window('ConfBox_3Button').close();
                                                }
                                                if(name === 'change_this_only') {
                                                    dhxWins.window('ConfBox_3Button').close();
                                                }
                                                if(name === 'cancel_selection') {
                                                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                        var column = 12;
                                                    } else {
                                                        var column = 9;
                                                    }
                                                    listItemNoftGrid.cells(rId,column).setValue(0)
                                                    listItemNoftGrid.cells(rId,cInd).setValue(oValue);
                                                    dhxWins.window('ConfBox_3Button').close();
                                                }
                                            });
                                        
    
                                    /*    dhtmlx.confirm({
                                            title: "Approve Item "+listItemNoftGrid.cells(rId,cInd).getText(),
                                            //type:"confirm-warning",
                                            ok  : "Change All", cancel : "Change This Only",
                                            text: "Do you want to change All " + "'" + listItemNoftGrid.getUserData(rId, "DS_Description_Old") + "'" + " description to " + "'" + listItemNoftGrid.cells(rId,cInd).getText() + "'" +" ?",
                                            callback: function(response) {
                                                if(response){
                                                    $.post(
                                                        preTally.Initialize.encryptURL('warehouse/approveMultipleDescription.php'),
                                                        { DS_Id : listItemNoftGrid.cells(rId,cInd).getValue() , DS_Description : listItemNoftGrid.cells(rId,cInd).getText(), DS_Id_Old : listItemNoftGrid.getUserData(rId, "DS_Id_Old") },
                                                        function(responseText) {

                                                            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            } else {
                                                                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                                                            }

                                                            listItemNoftGrid.clearAll();
                                                            preTally.Settings.progressOn(true, dhxLayout, null);

                                                            listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                                                                $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                                                                preTally.Settings.progressOff(true, dhxLayout, null);
                                                            });

                                                            dhtmlx.message({text: responseText });

                                                        }
                                                    );  
                                                }
                                            }
                                        });
                                        
                                        */
                                    }
    //                                console.log( listItemNoftGrid.cells(rId,cInd).getValue()+"--"+ listItemNoftGrid.cells(rId,cInd).getText() +"--"+ listItemNoftGrid.getUserData(rId, "IT_Id_Old") );
                                }
                            }
                            return true;
                        });
  
                        
                        dhxNotificationTab.cells("a1").showStatusBar();
                        
                        listItemNoftGrid.attachEvent("onCheckbox", function(rId,cInd,state){
                            if(state == false) {
                                listItemNoftGrid.setRowTextStyle(rId,false);
                            }
                        }); 
          
                    } else {
                        dhxMiddleBlockTabs.tabs("menuNotfItem").setActive();
                    }
                }
                
                if(id=='NPT'){ 
                    if (!dhxMiddleBlockTabs.cells("menuNotfPayment")) {
                        dhxMiddleBlockTabs.addTab("menuNotfPayment", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Payment Notifications", 200);
                        dhxMiddleBlockTabs.tabs("menuNotfPayment").setActive();
                        
                        var dhxPaymentNotfLayout =  dhxMiddleBlockTabs.cells("menuNotfPayment").attachLayout("1C");
                        
                        ptNotificationTabbar = dhxPaymentNotfLayout.cells("a").attachTabbar();
                        
                        var ACL_LC = unescape(JGG1P3bDnUSDL7Mui7KzYjj28UjPdWxCCtGkJSHeuo); 
                        var ACL_BNK = unescape(JGG1P3bDnUSDL8Mui7KzYjj28UjPdWxCCtGkJSHeuo);          
                        
                        if(ACL_LC == 1 && ACL_BNK == 1){ 
                            ptNotificationTabbar.addTab("viewBranchPaymentNF", "Cash Payment Notifications");
                            ptNotificationTabbar.addTab("viewBankPaymentNF", "Bank Payment Notifications");
                            ptNotificationTabbar.tabs("viewBranchPaymentNF").setActive();
                        }else if(ACL_LC == 1){
                            ptNotificationTabbar.addTab("viewBranchPaymentNF", "Cash Payment Notifications");
                            ptNotificationTabbar.tabs("viewBranchPaymentNF").setActive();
                        }else if(ACL_BNK == 1){
                            ptNotificationTabbar.addTab("viewBankPaymentNF", "Bank Payment Notifications");
                            ptNotificationTabbar.tabs("viewBankPaymentNF").setActive();
                        }
                        
                        var actvId = ptNotificationTabbar.getActiveTab();
                        
                        if(actvId == 'viewBranchPaymentNF') preTally.Notification.viewBranchPaymentNF();
                        if(actvId == 'viewBankPaymentNF')   preTally.Notification.viewBankPaymentNF();
                        
                        ptNotificationTabbar.attachEvent("onSelect", function(id, last_id){ 

                            if(id == 'viewBranchPaymentNF') preTally.Notification.viewBranchPaymentNF();
                            if(id == 'viewBankPaymentNF')   preTally.Notification.viewBankPaymentNF();
                            return true;

                        });

                    } else {
                        dhxMiddleBlockTabs.tabs("menuNotfPayment").setActive();
                    }
                }
                /*if(id=='ND'){ 
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
                }*/
                if(id=='MSG') {     // User personalized messages section
                    if (!dhxMiddleBlockTabs.cells("menuMyMessages")) {
                        dhxMiddleBlockTabs.addTab("menuMyMessages", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;My Messages", 200);
                        dhxMiddleBlockTabs.tabs("menuMyMessages").setActive();
                        messagesLayout = dhxMiddleBlockTabs.cells("menuMyMessages").attachLayout('2U');    
                        messagesLayout.cells("a").setText("My Messages");
                        messagesLayout.cells("b").setText("Message List");
                        messagesLayout.cells("a").setWidth(200);
                        
                        preTally.Notification.receivedMessagesLoadXML();
                        
                        messagesLeftTree = messagesLayout.cells("a").attachTree();
                        messagesLeftTree.enableHighlighting(true);                 
                        messagesLeftTree.setImagePath("assets/tree/codebase/imgs/dhxtree_skyblue/"); 
                        messagesLeftTree.loadXML(preTally.Initialize.encryptURL("requisites/messagesTree.php"), function(){
                        }); 
                        messagesLeftTree.attachEvent("onClick", function(itemid){
                            preTally.Settings.progressOn(true, messagesLayout, null);
                            if(itemid == "Main_Item") {
                                preTally.Notification.receivedMessagesLoadXML();
                            }
                            else if(itemid == "Inbox") {
                                preTally.Notification.receivedMessagesLoadXML();   
                            }
                            else if(itemid == "Outbox") {
                                OutboxMessagesGrid = messagesLayout.cells("b").attachGrid();
                                OutboxMessagesGrid.enableColSpan(true);
                                OutboxMessagesGrid.attachEvent("onXLS", function() {
                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                });
                                OutboxMessagesGrid.attachEvent("onXLE", function() {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                });
                                
                                var rowId = 0;
                                OutboxMessagesGrid.loadXML(preTally.Initialize.encryptURL("requisites/viewSentMessages.php"), function() { 
                                    
                                    var flg = [];
                                    flg[rowId] = 0;
                                    OutboxMessagesGrid.attachEvent("onRowSelect", function(rId) {                                    
                                        rowId = rId;                                        
                                        if (flg[rowId] == 1) {                                       
                                            OutboxMessagesGrid.cells(rowId, 1).close();
                                            flg[rowId] = 0;                                        
                                        }
                                        else{
                                            OutboxMessagesGrid.cells(rowId, 1).open();
                                            flg[rowId] = 1;  
                                        }
                                    });          
                                });
                            }
                            else if(itemid == "Trash") {
                                preTally.Notification.trashMessagesLoadXML();   
                            }
                            preTally.Settings.progressOff(true, messagesLayout, null);
                        });
                    }
                    else {
                        dhxMiddleBlockTabs.tabs("menuMyMessages").setActive();
                    }
                }
                
                if(id== 'PL') {
                    preTally.Settings.progressOn(true, dhxLayout, null);       
                    preTally.Settings.menu_ApproveLeave();
                    setTimeout(function(){ 
                        preTally.Settings.applyPendingLeaveFilter('1');
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    }, 1500);
                    
                    
                    
                }
                if(id== 'LE') {
                    preTally.Settings.progressOn(true, dhxLayout, null);       
                    preTally.Settings.menu_LateEntriesList();
                    setTimeout(function(){ 
                        preTally.Settings.applyLateEntryFilter('1');
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    }, 1500);
                }
        },
        receivedMessagesLoadXML : function() {
            var rowId = 0;
            myMessagesGrid = messagesLayout.cells("b").attachGrid();
            preTally.Settings.progressOn(true, dhxLayout, null);            
            myMessagesGrid.enableColSpan(true);            ;
            myMessagesGrid.init();
            
            myMessagesGrid.loadXML(preTally.Initialize.encryptURL("requisites/viewReceivedMessages.php"), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);                
                var filterObject = myMessagesGrid.getFilterElement(1);
                filterObject.id = 'msgStatusFilter';                
                $("#msgStatusFilter").change(function() {
                    if(myMessagesGrid.getRowsNum() == 0) {  // no messages found
                        myMessagesGrid.addRow("msgRow",'<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No messages found</div>');
                        myMessagesGrid.setColspan("msgRow",0,8);
                    }
                });                
                myMessagesGrid.attachEvent("onFilterEnd", function(elements){
                    var k=1;
                    for(i=0;i<myMessagesGrid.getRowsNum();i++){
                        myMessagesGrid.cells(myMessagesGrid.getRowId(i),0).setValue(k);
                        k++;
                    }
                   
                });
                
                preTally.Notification.messageStatusFilter();
                var flg = [];
                flg[rowId] = 0;
              
                myMessagesGrid.attachEvent("onRowSelect", function(id) {                     
                    rowId = id;                    
                    var ReadStatus = myMessagesGrid.cells(rowId,1).getValue();                    
                    if (flg[rowId] == 1) {
                        myMessagesGrid.cells(rowId, 3).close();
                        flg[rowId] = 0;
                    }
                    else {                        
                        myMessagesGrid.cells(rowId, 3).open();
                        flg[rowId] = 1;  
                        
                        if(ReadStatus == 0) 
                            preTally.Notification.updateMessageReadStatus(rowId);
                    }
                });   
                myMessagesGrid.attachEvent("onSubRowOpen", function(id){                    
                    
                    var ReadStatus = myMessagesGrid.cells(id,1).getValue();                    
                    if ((flg[id] != 0) && ReadStatus == 0) {
                        preTally.Notification.updateMessageReadStatus(id);
                    }
                });
            });
        },
        trashMessagesLoadXML : function() {
            trashMessagesGrid = messagesLayout.cells("b").attachGrid();
            preTally.Settings.progressOn(true, dhxLayout, null);
            trashMessagesGrid.enableColSpan(true);
            var rowId = 0;
            trashMessagesGrid.loadXML(preTally.Initialize.encryptURL("requisites/viewTrashMessages.php"), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
                
                var flg = [];
                flg[rowId] = 0;
                trashMessagesGrid.attachEvent("onRowSelect", function(rId) {   
                   
                    rowId = rId;                                        
                    if (flg[rowId] == 1) {                                       
                        trashMessagesGrid.cells(rowId, 2).close();
                        flg[rowId] = 0;                                        
                    }
                    else{
                        trashMessagesGrid.cells(rowId, 2).open();
                        flg[rowId] = 1;  
                        
                        var readStatus = trashMessagesGrid.getUserData(rowId,"ReadStatus");
                        if(readStatus == 0) {
                            myMessagesGrid.cells(rowId,2).setValue("<img src='images/icon/read_message.png' title='Read' />");
                            myMessagesGrid.setUserData(rowId,"ReadStatus","1");
                        }
                    }
                });       
            });
        },
        deleteMessage : function(inp, msgId) {
            
            dhtmlx.confirm({
                title: "Delete Message",
                type:"confirm-warning",
                ok  : "Yes", cancel : "No",
                text: "Do you want to Delete " + " " + myMessagesGrid.cells(msgId,4).getValue() + " ?",
                callback: function(response) {
                    if(response){
                        $.ajax({
                            url : preTally.Initialize.encryptURL("warehouse/deleteMessage.php&msgId="+msgId)
                        }).done(function(data) {
                            preTally.Notification.receivedMessagesLoadXML();  
                        });  
                    }
                }
            });
  
        },
        updateMessageReadStatus : function(rowId) {
            $.ajax({
                url : preTally.Initialize.encryptURL("warehouse/messageReadStatusUpdate.php&mid="+rowId)
            }).done(function(data) {
                myMessagesGrid.cells(rowId,1).setValue("1");
                myMessagesGrid.cells(rowId,9).setValue("1");
                myMessagesGrid.cells(rowId,2).setValue("<img src='images/icon/read_message.png' title='Read' />");
                messagesLeftTree.setItemText("Inbox",data);
                preTally.Notification.messageStatusFilter();
            });  
        },
        messageStatusFilter : function() {
            myMessagesGrid.refreshFilters();
             $c = 0; 
            $("#msgStatusFilter option").each(function() {
                if($c > '0') {
                    if($(this).val() === '1') { $(this).text('Read'); }
                    if($(this).val() === '0') { $(this).text('UnRead'); }
                }
                $c++;
            });
            myMessagesGrid.attachEvent("onSubRowOpen", function(id,state){
                if(state==false){
                    statusHidden=$("#msgStatusFilter" ).val();
                    myMessagesGrid.filterBy(9,statusHidden);
                    if(myMessagesGrid.getRowsNum() == 0) {  // no messages found
                           myMessagesGrid.addRow("msgRow",'<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No messages found</div>');
                           myMessagesGrid.setColspan("msgRow",0,9);
                    }
                }
    
            });
        
        },
        viewPendingSHItems : function(){
            
            var filtrPSHInterval ;
            
            if(listPendingSHGrid) listPendingSHGrid.destructor();
            
            preTally.Settings.progressOn(true,dhxLayout, null);
            
            pendingItemButtonBar = dhxNotificationTab.tabs("a2").attachStatusBar({
                text  : "<div class='pagingDiv'><span id='notifPendingSH' ></span></div>\
                         <input type='button' value='SAVE' onclick='preTally.Notification.pendingSHItemsSave();' style='margin:5px 10px 5px 0; float:right;' />",
                height: 35
            });
            
            listPendingSHGrid = dhxNotificationTab.cells("a2").attachGrid();
            listPendingSHGrid.enableEditEvents(true,true,true);
            listPendingSHGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false");
            
            //listPendingSHGrid.enableSmartRendering(true,50);
            
            listPendingSHGrid.setPagingWTMode(true,true,true,[10,20,30,40,50]);
            listPendingSHGrid.enablePaging(true,50,5,'notifPendingSH',false);
            listPendingSHGrid.setPagingSkin("toolbar");
            
            listPendingSHGrid.init();
                        
            listPendingSHGrid.loadXML(preTally.Initialize.encryptURL("requisites/listPendingSHItems.php"), function() {
                preTally.Settings.progressOff(true,dhxLayout, null);
                
                var itemPSHCombo = new dhtmlXCombo("itmPF");
                var params = 'filter=Pending&type=filt';
                itemPSHCombo.load(preTally.Initialize.encryptURL("requisites/itemsFilter.php&"+ params), function(){
                    itemPSHCombo.setPlaceholder('Item ');
                    itemPSHCombo.setFilterHandler(function(mask, option){
                        var r = false;
                        if (mask.length == 0) {
                                r = true;
                        } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                r = true;
                        }
                        return r;
                    });
                });
                itemPSHCombo.setOptionWidth(250);
                itemPSHCombo.attachEvent("onChange", function() {
                    var itmComboVal = itemPSHCombo.getSelectedValue();
                    if(!itemPSHCombo.getSelectedValue() && itemPSHCombo.getComboText()) itmComboVal = itemPSHCombo.getComboText();
                    $( "#itmPF" ).val(itmComboVal);
                    preTally.Notification.applyPendingSHFilter();
                });

                var branchCombo = new dhtmlXCombo("brnPF");
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
                    $( "#brnPF" ).val(brComboVal);
                    preTally.Notification.applyPendingSHFilter();
                });


                $.ajax({
                    url : preTally.Initialize.encryptURL("requisites/usersReportsMe.php&type=filt")
                }).done(function(data) {
                    var UserlistCombo = data;

                    var addedByPSHCombo = new dhtmlXCombo("addPF");
                    addedByPSHCombo.load(UserlistCombo, function(){
                        addedByPSHCombo.setPlaceholder('Added By');
                        addedByPSHCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                    r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                    r = true;
                            }
                            return r;
                        });
                    });
                    addedByPSHCombo.setOptionWidth(180);
                    addedByPSHCombo.attachEvent("onChange", function() {
                        var adComboVal = addedByPSHCombo.getSelectedValue();
                        if( !addedByPSHCombo.getSelectedValue() && addedByPSHCombo.getComboText()) adComboVal = addedByPSHCombo.getComboText();
                        $( "#addPF" ).val(adComboVal);
                        preTally.Notification.applyPendingSHFilter();
                    });
                    var approvedByPSHCombo = new dhtmlXCombo("aprPF");
                    approvedByPSHCombo.load(UserlistCombo, function(){
                        approvedByPSHCombo.setPlaceholder('Approved By');
                        approvedByPSHCombo.setFilterHandler(function(mask, option){
                            var r = false;
                            if (mask.length == 0) {
                                    r = true;
                            } else if (option.text.match(new RegExp("^"+mask,"i")) != null) {
                                    r = true;
                            }
                            return r;
                        });
                    });
                    approvedByPSHCombo.setOptionWidth(180);
                    approvedByPSHCombo.attachEvent("onChange", function() {
                        var aprComboVal = approvedByPSHCombo.getSelectedValue();
                        if( !approvedByPSHCombo.getSelectedValue() && approvedByPSHCombo.getComboText()) aprComboVal = approvedByPSHCombo.getComboText();
                        $( "#aprPF" ).val(aprComboVal);
                        preTally.Notification.applyPendingSHFilter();
                    });
                });


                $( ".text_filter" ).keyup(function() {
                    if(filtrPSHInterval) clearInterval(filtrPSHInterval);

                    filtrPSHInterval = setInterval( function() { 
                        preTally.Notification.applyPendingSHFilter(); 
                        clearInterval(filtrPSHInterval); 
                    }, 500);
                });

                $( ".select_filter" ).change(function() {
                    preTally.Notification.applyPendingSHFilter(); 
                });
                
                
                preTally.Settings.progressOff(true,dhxLayout, null);

            });


            var itemIDs = new Array();

            listPendingSHGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                if(stage == 0) {
                    if(cInd == 1){
                        var entryTypeCombo  = listPendingSHGrid.cells(rId,cInd).getCellCombo();
                        var optnCnt = entryTypeCombo.getOptionsCount();

                        if(optnCnt == 0) {
                            entryTypeCombo.addOption([
                                ["1","Income"],
                                ["2","Expense"],
                            ]);

                            entryTypeCombo.attachEvent("onChange", function() {
                                if(itemIDs.indexOf(rId) >= 0) {
                                    listPendingSHGrid.cells(rId,4).setValue('');
                                    listPendingSHGrid.cells(rId,5).setValue('');
                                    listPendingSHGrid.setUserData(rId,"MH_Type",entryTypeCombo.getSelectedValue());  
                                } else {
                                     itemIDs.push(rId);
                                }
                            });
                        }

                    } else if(cInd == 2){

                        var MHComboType = listPendingSHGrid.getUserData(rId, "MH_Type");
                        var ITColCombo  = listPendingSHGrid.cells(rId,cInd).getCellCombo();
                        var params = "type=" + MHComboType;
                        ITColCombo.clearAll();
                        ITColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/ItemsCombo.php&"+ params));
                        ITColCombo.setOptionWidth(310);

                        ITColCombo.attachEvent("onChange", function(loader, response) {
                            listPendingSHGrid.setUserData(rId,"IT_Id",ITColCombo.getSelectedValue());
                        });
                    } else if(cInd == 3){

                        var ITComboType  = listPendingSHGrid.getUserData(rId, "IT_Id");//listPendingSHGrid.cellById(rId, 2).getAttribute("id");
                        var DSColCombo   = listPendingSHGrid.cells(rId,cInd).getCellCombo();
                        var params = "IT_Id=" +ITComboType+"&filter=1";
                        DSColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/descriptions.php& "+ params));
                        DSColCombo.setOptionWidth(310);

                    } else if(cInd == 4){
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            var MHComboType  = listPendingSHGrid.getUserData(rId, "MH_Type"); // listPendingSHGrid.cellById(rId, 1).getAttribute("id");
                            var SHColCombo   = listPendingSHGrid.cells(rId,cInd).getCellCombo();
                            SHColCombo.clearAll();
                            var params = "type=" +MHComboType+ "&for=notfEdit";
                            SHColCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                            SHColCombo.setOptionWidth(310);
                            var onChgEvnt = SHColCombo.attachEvent("onChange", function(value, text) {

                                var jsonResponse = SHColCombo.getSelectedValue();
                                var obj = jQuery.parseJSON( jsonResponse );
                                if(jsonResponse) {
                                    listPendingSHGrid.cells(rId,5).setValue(obj.MHName);
                                    SHColCombo.setComboValue(obj.SHId);
                                    SHColCombo.detachEvent(onChgEvnt);
                                } else {
                                    listPendingSHGrid.cells(rId,5).setValue('');
                                }
                            });
                        }
                    }
                } else if(stage == 2){
                    if(oValue != nValue) listPendingSHGrid.cells(rId,10).setValue(1);
                }
                return true;
            });

            
            dhxNotificationTab.cells("a2").showStatusBar();
            listPendingSHGrid.attachEvent("onCheckbox", function(rId,cInd,state){
                if(state == false) {
                    listPendingSHGrid.setRowTextStyle(rId,false);
                }
            }); 
            
        },
        applyNotficationFilter:  function(value){

            /*$('#notification_select_filter').val()
            $('#itmF').val()
            $('#decF').val()
            $('#shF').val()
            $('#mhF').val()
            $('#amtF').val()
            $('#addF').val()
            $('#aprF').val()*/
            
            if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
            } else {
                var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
            }
//            console.log($('#ieF').val());
            listItemNoftGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyPendingSHFilter:  function(value){

            var filterValue = new Array($('#iePF').val(), $('#itmPF').val(), $('#decPF').val(), $('#shPF').val(), $('#mhPF').val(), $('#amtPF').val(), $('#addPF').val(), $('#brnPF').val(), $('#aprPF').val()); //, $('#aprvlPF').val()
            
            listPendingSHGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            
            listPendingSHGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listPendingSHItems.php&filter="+filterValue), function() {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        /*filterNotification:  function(value){
                var filterValue = value;
                console.log(filterValue);
                listItemNoftGrid.clearAll();
                preTally.Settings.progressOn(true, dhxLayout, null);
                //listItemNoftGrid.enableSmartRendering(true,50);
                listItemNoftGrid.load("requisites/listItemDescNotifications.php?"+encrypt('filter='+value), function() { 
                    preTally.Settings.progressOff(true, dhxLayout, null);

                    for(var j=0; j<=7; j++) {
                        var inp = listItemNoftGrid.getFilterElement(j+1); // 'j'-index of the column (zero-based numbering)
                        inp.value = filterValue[j]; // inputted value
                    }
                });
        },*/
        notificationSave: function() {
//            var actvId = dhxNotificationTab.getActiveTab(); console.log(actvId);
//            if(actvId == 'a1') 
//                var gridItmObj = listItemNoftGrid;  
//            else if(actvId == 'a2')
//                var gridItmObj = listPendingSHGrid;
                             
            var itemObject      = {};
            var error           = 0;
            var ieArray         = new Array();
            ieArray['Income']   = '1';
            ieArray['Expense']  = '2';
                    
            listItemNoftGrid.forEachRow(function(rId){
                if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                    var column = 12;
                } else {
                    var column = 9;
                }
                if(listItemNoftGrid.cells(rId,column).getValue() == 1) {
                    var itemDetails = {}; 

                    itemDetails['MH_Type'] = listItemNoftGrid.cells(rId,2).getValue();
                    itemDetails['IT_Name'] = listItemNoftGrid.cells(rId,3).getText();
                    itemDetails['DS_Description'] = listItemNoftGrid.cells(rId,4).getText();
                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                        itemDetails['SH_Name'] = listItemNoftGrid.cells(rId,5).getValue();
                        itemDetails['MH_Name'] = listItemNoftGrid.cells(rId,6).getValue();
                        itemDetails['BS_Amount']  = listItemNoftGrid.cells(rId,7).getValue();
                    } else {
                        itemDetails['SH_Name'] = 1;
                        itemDetails['MH_Name'] = 1;
                        itemDetails['BS_Amount']  = 1;
                    }
                    
                    if(!itemDetails['MH_Type'] || !itemDetails['IT_Name'].trim() || !itemDetails['DS_Description'].trim() || !itemDetails['SH_Name'] || !itemDetails['MH_Name'] || !itemDetails['BS_Amount']) {
                        dhtmlx.message({text: '<b style="color:#FF0000;">Item ' + listItemNoftGrid.cells(rId,1).getValue() + ' is Error. Please Verify.</b>'});
                        error = 1;
                    } else {

                        if (/^[\],:{}\s]*$/.test(listItemNoftGrid.cells(rId,5).getValue().replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                            var obj = jQuery.parseJSON( listItemNoftGrid.cells(rId,5).getValue() );
                        }else{
                            var obj = jQuery.parseJSON( listItemNoftGrid.getUserData(rId, "SHDetails") );
                        }
                        itemDetails['MH_Type'] = ieArray[listItemNoftGrid.cells(rId,2).getText()];
                        itemDetails['IT_Name'] = listItemNoftGrid.cells(rId,3).getText();
                        itemDetails['DS_Description'] = listItemNoftGrid.cells(rId,4).getText();
                        itemDetails['SH_Name'] = obj.SHId;
                        itemDetails['MH_Name'] = obj.MHId;
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            itemDetails['BS_Amount']  = listItemNoftGrid.cells(rId,7).getValue();
                        } else { 
                            itemDetails['BS_Amount']  = listItemNoftGrid.getUserData(rId, "BS_Amount");
                        }
                        
                        itemDetails['IT_Id_Old'] = listItemNoftGrid.getUserData(rId, "IT_Id_Old");
                        itemDetails['IT_Id_New'] = listItemNoftGrid.cells(rId,3).getValue();
                        itemDetails['DS_Id_Old'] = listItemNoftGrid.getUserData(rId, "DS_Id_Old");
                        itemDetails['DS_Id_New'] = listItemNoftGrid.cells(rId,4).getValue();
                        
//                        alert(listItemNoftGrid.cells(rId,2).getText());
                        itemObject[rId] = itemDetails;
                    }
                }
            });
            if(error == 0) {
//                console.log(itemObject);
                $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL("warehouse/notificationSave.php"),
                    data   : itemObject                    
                    
                }).done(function(data) { 
                    if (data != '') {
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            listItemNoftGrid.hdr.rows[1].cells[12].getElementsByTagName("INPUT")[0].checked = false;
                            var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), $('#shF').val(), $('#mhF').val(), $('#amtF').val(), $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                        } else {
                            listItemNoftGrid.hdr.rows[1].cells[9].getElementsByTagName("INPUT")[0].checked = false;
                            var filterValue = new Array($('#ieF').val(), $('#itmF').val(), $('#decF').val(), '', '', '', $('#addF').val(), $('#brnF').val(), $('#aprF').val(), $('#aprvlF').val(), $('#itmRadio').val());
                        }

                        listItemNoftGrid.clearAll();
                        preTally.Settings.progressOn(true, dhxLayout, null);

                        listItemNoftGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php&filter="+filterValue), function() {
                            $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
                            preTally.Settings.progressOff(true, dhxLayout, null);
                        });
                                                            
                        dhtmlx.message({text: data });
//                        listItemNoftGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listItemDescNotifications.php"), true, true, function() {
//                            $('.nt_cnt_tot').html("# : "+listItemNoftGrid.getUserData("", "TL_Count")+" ");
//                        });    
                    }else {
                       dhtmlx.message({text: '<b style="color:#FF0000;">Please Select an Item to Save.</b>'});
                    }
                                        
                });
                
                
                /*var count = 1;
                
                for (var key in itemObject) {
                   console.log(key);
                   listItemNoftGrid.deleteRow(key);
                   listItemNoftGrid.setRowHidden(key,true);
                }
                
                listItemNoftGrid.forEachRow(function(rId){
                    
                    
                    console.log(listItemNoftGrid.doesRowExist(rId));
                    
                    listItemNoftGrid.cells(rId,0).setValue(count);
                    count ++ ;
                });*/
            }
            
            //dhtmlx.message({text: "Error <br />Error <br />Error <br />Error <br />Error <br />Error" });
            //dpNotificationSave.sendData();
            
        },
        pendingSHItemsSave: function() {
                             
            var itemObject      = {};
            var error           = 0;
            var ieArray         = new Array();
            ieArray['Income']   = '1';
            ieArray['Expense']  = '2';
                    
            listPendingSHGrid.forEachRow(function(rId){
                if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                    var column = 10;
                } else {
                    var column = 7;
                }
                if(listPendingSHGrid.cells(rId,column).getValue() == 1) {
                    var itemDetails = {}; 

                    itemDetails['MH_Type'] = listPendingSHGrid.cells(rId,1).getValue();
                    itemDetails['IT_Name'] = listPendingSHGrid.cells(rId,2).getText();
                    itemDetails['DS_Description'] = listPendingSHGrid.cells(rId,3).getText();
                    if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                        itemDetails['SH_Name'] = listPendingSHGrid.cells(rId,4).getValue();
                        itemDetails['MH_Name'] = listPendingSHGrid.cells(rId,5).getValue();
                        itemDetails['BS_Amount']  = listPendingSHGrid.cells(rId,6).getValue();
                    } else {
                        itemDetails['SH_Name'] = 1;
                        itemDetails['MH_Name'] = 1;
                        itemDetails['BS_Amount']  = 1;
                    }
                    
                    if(!itemDetails['MH_Type'] || !itemDetails['IT_Name'].trim() || !itemDetails['DS_Description'].trim() || !itemDetails['SH_Name'] || !itemDetails['MH_Name'] || !itemDetails['BS_Amount']) {
                        dhtmlx.message({text: '<b style="color:#FF0000;">Item ' + listPendingSHGrid.cells(rId,0).getValue() + ' is Error. Please Verify.</b>'});
                        error = 1;
                    } else {

                        if (/^[\],:{}\s]*$/.test(listPendingSHGrid.cells(rId,4).getValue().replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                            var obj = jQuery.parseJSON( listPendingSHGrid.cells(rId,4).getValue() );
                        }else{
                            var obj = jQuery.parseJSON( listPendingSHGrid.getUserData(rId, "SHDetails") );
                        }
                        itemDetails['MH_Type'] = ieArray[listPendingSHGrid.cells(rId,1).getText()];
                        itemDetails['IT_Name'] = listPendingSHGrid.cells(rId,2).getText();
                        itemDetails['DS_Description'] = listPendingSHGrid.cells(rId,3).getText();
                        itemDetails['SH_Name'] = obj.SHId;
                        itemDetails['MH_Name'] = obj.MHId;
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            itemDetails['BS_Amount']  = listPendingSHGrid.cells(rId,6).getValue();
                        } else { 
                            itemDetails['BS_Amount']  = listPendingSHGrid.getUserData(rId, "BS_Amount");
                        }
                        itemObject[rId] = itemDetails;
                    }
                }
            });
            if(error == 0) {
                $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL("warehouse/notificationSave.php"),
                    data   : itemObject
                }).done(function(data) { 
                    if (data != '') {
                        dhtmlx.message({text: data });
                        if(unescape(JGG1P3bDnUSDL5Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1) {
                            listPendingSHGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                        } else {
                            listPendingSHGrid.hdr.rows[1].cells[7].getElementsByTagName("INPUT")[0].checked = false;
                        }
                        var filterValue = new Array($('#iePF').val(), $('#itmPF').val(), $('#decPF').val(), $('#shPF').val(), $('#mhPF').val(), $('#amtPF').val(), $('#addPF').val(), $('#brnPF').val(), $('#aprPF').val()); 
                        listPendingSHGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listPendingSHItems.php"), true, true, function() {});    
                        

                    }else {
                       dhtmlx.message({text: '<b style="color:#FF0000;">Please Select an Item to Save.</b>'});
                    }
//                    dhtmlx.message({text: 'Entry Saved Successfully' });
//                    listPendingSHGrid.updateFromXML("requisites/listPendingSHItems.php", true, true, function() {});                       
                });
   
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
                            preTally.Initialize.encryptURL('warehouse/approveItem.php'),
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
                            preTally.Initialize.encryptURL('warehouse/approveDescription.php'),
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
            swpWindowForm.loadStruct(preTally.Initialize.encryptURL("requisites/map_itemNotf.php&"+params), function() {     

                    ItmCombo = swpWindowForm.getCombo("IT_MapId");
                    ItmCombo.setPlaceholder('Please Select Item To Swap');
                    
                    var MhTypCombo = swpWindowForm.getCombo("MH_Type");
                    var SH_Combo = swpWindowForm.getCombo("SH_Id");
                    swpWindowForm.setItemValue("MH_Type", listItemNoftGrid.getUserData(IT_Id, "MH_Type"));
                    preTally.Settings.comboSelectPreload(SH_Combo,"subheadcombo");
                    preTally.Settings.comboSelectPreload(ItmCombo,"itmapcombo");
                    var params="type=" + listItemNoftGrid.getUserData(IT_Id, "MH_Type");
                    SH_Combo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
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
                            SH_Combo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/subheads.php&"+ params));
                        }
                    });
                    SH_Combo.attachEvent("onChange", function() {
                        ItmCombo.clearAll();
                        ItmCombo.setComboText("");
                        ItmCombo.setComboValue("");
                        var SH_Id = SH_Combo.getSelectedValue();
                        if ((SH_Id) && (SH_Id != 0)) {
                            params="SH_Id=" + SH_Id+"&filter=1";
                            ItmCombo.load(preTally.Initialize.encryptURL("requisites/items.php&"+ params), function() {
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
                                swpWindowForm.send(preTally.Initialize.encryptURL('warehouse/map_itemNotf.php'), function(loader, response) {
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
            swpWindowForm.loadStruct(preTally.Initialize.encryptURL("requisites/map_descNotf.php&"+ params), function() {
                
                    DescCombo = swpWindowForm.getCombo("DS_MapId");
                    var params ="IT_Id="+IT_Id+"&filter=1";
                    DescCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/descriptions.php&"+ params), true);
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
                                swpWindowForm.send(preTally.Initialize.encryptURL('warehouse/map_descNotf.php'), function(loader, response) {
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
                        var itemResponseMap = dhx4.ajax.postSync(preTally.Initialize.encryptURL('warehouse/mapItemPreTally.php&item='+ITId), encodeURI(1));
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
        viewBranchPaymentNF : function(){
            
//            ptNotificationTabbar.addTab("viewBranchPaymentNF", "Branch Based Payment Notifications");
//            ptNotificationTabbar.tabs("viewBranchPaymentNF").setActive();

                var tb_data_txt = ' <div class="tb_data_txt_secl">\ \n\
                                        <div class="npc_cnt_tot"># : 0</div>\
                                    </div>';
                
                var tbRptObj = ptNotificationTabbar.cells("viewBranchPaymentNF").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 23             // custom height
                });


                
                
            preTally.Settings.progressOn(true, dhxLayout, null);
            var filtrInterval;

            dhxCashPaymentGrid = ptNotificationTabbar.cells("viewBranchPaymentNF").attachGrid();

            dhxCashPaymentGrid.setImagePath("../../codebase/imgs/");
            dhxCashPaymentGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'csh_select_filter' id='ieCNF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  class='cshrpt_text_filter' id ='itmCNF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shCNF' >,<input type='text'  class='cshrpt_text_filter' id='amtCNF' style='width: 90%;' placeholder='Amount'>,<input type='text'  class='cshrpt_text_filter' id='brnCNF' style='width: 90%;' placeholder='Branch'>,Confirm ");
            dhxCashPaymentGrid.setInitWidths("60,80,*,0,100,150,150")
            dhxCashPaymentGrid.setColAlign("center,center,left,left,right,left,center")
            dhxCashPaymentGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");

            dhxCashPaymentGrid.setColSorting("na,na,na,na,na,na,na")
//                dhxCashPaymentGrid.enableEditEvents(true,true,true);
            dhxCashPaymentGrid.init();
            dhxCashPaymentGrid.setSkin("dhx_skyblue")
            dhxCashPaymentGrid.enableSmartRendering(true,50);

            dhxCashPaymentGrid.enableTooltips("false,false,false,false,false,false,false,false");

            $( ".csh_select_filter" ).change(function() {
                preTally.Notification.applyCashFilter();
            });


            $( ".cshrpt_text_filter" ).keyup(function() {
                if(filtrInterval) clearInterval(filtrInterval);

                filtrInterval = setInterval( function() { 
                    preTally.Notification.applyCashFilter(); 
                    clearInterval(filtrInterval); 
                }, 500);

            });

            dhxCashPaymentGrid.loadXML(preTally.Initialize.encryptURL("requisites/listCashPaymentNotf.php&"), function() {
                $('.npc_cnt_tot').html("# : "+dhxCashPaymentGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
            dhxCashPaymentGrid.attachEvent("onRowSelect", function(id,ind){                
                if(ind != 5 && ind != 6) 
                    $( ".targetCash_"+id ).click();
            });
            
        },
        viewBankPaymentNF : function(){
            
//            ptNotificationTabbar.addTab("viewBankPaymentNF", "Bank Based Payment Notifications");
//            ptNotificationTabbar.tabs("viewBankPaymentNF").setActive();

                var tb_data_txt = ' <div class="tb_data_txt_secl">\ \n\
                                        <div class="npb_cnt_tot"># : 0</div>\
                                    </div>';
                
                var tbRptObj = ptNotificationTabbar.cells("viewBankPaymentNF").attachStatusBar({
                    text:   tb_data_txt,   // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                    height: 23             // custom height
                });
                

            preTally.Settings.progressOn(true, dhxLayout, null);
            var filtrInterval;
            dhxBankPaymentGrid = ptNotificationTabbar.cells("viewBankPaymentNF").attachGrid();

            dhxBankPaymentGrid.setImagePath("../../codebase/imgs/");
            dhxBankPaymentGrid.setHeader("SlNo,<select style = 'width:60px;' class = 'bnk_select_filter' id='ieBNF'><option value ='0'>All</option><option value ='1'>Income</option><option value ='2'>Expense</option></select>,<input type='text'  class='bnkrpt_text_filter' id ='itmBNF' style='width: 90%;' placeholder='Enter a name of Income or Expense to search . . .'>,<input type='hidden' id='shBNF' >,<input type='text' class='bnkrpt_text_filter' id ='amtBNF' style='width: 90%;' placeholder='Amount'>,<input type='text'  class='bnkrpt_text_filter' id='brnBNF' style='width: 90%;' placeholder='Branch'>,Date,Confirm ");
            dhxBankPaymentGrid.setInitWidths("60,80,*,0,100,150,150,150")
            dhxBankPaymentGrid.setColAlign("center,center,left,left,right,left,left,center")
            dhxBankPaymentGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");

            dhxBankPaymentGrid.setColSorting("na,na,na,na,na,na,na,na");
//                dhxBankPaymentGrid.enableEditEvents(true,true,true);
            dhxBankPaymentGrid.init();
            dhxBankPaymentGrid.setSkin("dhx_skyblue")
            dhxBankPaymentGrid.enableSmartRendering(true,50);

            dhxBankPaymentGrid.enableTooltips("false,false,false,false,false,false,false,false,false");

            $( ".bnk_select_filter" ).change(function() {
                preTally.Notification.applyBankFilter();
            });

            $( ".bnkrpt_text_filter" ).keyup(function() {
                if(filtrInterval) clearInterval(filtrInterval);

                filtrInterval = setInterval( function() { 
                    preTally.Notification.applyBankFilter(); 
                    clearInterval(filtrInterval); 
                }, 500);

            });

            dhxBankPaymentGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBankPaymentNotf.php&"), function() {
                $('.npb_cnt_tot').html("# : "+dhxBankPaymentGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
            
            dhxBankPaymentGrid.attachEvent("onRowSelect", function(id,ind){
                if(ind != 5 && ind != 7) 
                    $( ".targetBank_"+id ).click();
            });
            
        },
        applyCashFilter:  function(){
//            console.log($('#ieCNF').val()+"--"+$('#itmBNF').val()+"--"+$('#brnBNF').val()+"--"+$('#addCNF').val()+"--"+$( "#shBNF" ).val());
            var amount = $( "#amtCNF" ).val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#ieCNF').val(), $('#itmCNF').val(), $('#brnCNF').val(),$('#addCNF').val(),$( "#shCNF" ).val(),amount);
            dhxCashPaymentGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxCashPaymentGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listCashPaymentNotf.php&filter="+filterValue), function() { 
                $('.npc_cnt_tot').html("# : "+dhxCashPaymentGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyBankFilter:  function(){
//            console.log($('#ieBNF').val()+"--"+$('#itmBNF').val()+"--"+$('#brnBNF').val()+"--"+$('#addCNF').val()+"--"+$( "#shBNF" ).val());
            var amount = $( "#amtBNF" ).val().replace( /,/g, "" );  // remove , from amount
            var filterValue = new Array($('#ieBNF').val(), $('#itmBNF').val(), $('#brnBNF').val(),$('#addBNF').val(),$( "#shBNF" ).val(),amount);
            dhxBankPaymentGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            dhxBankPaymentGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBankPaymentNotf.php&filter="+filterValue), function() { 
                $('.npb_cnt_tot').html("# : "+dhxBankPaymentGrid.getUserData("", "TL_Count")+" ");                
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        confirmCashPaymentNotf: function(inp,BS_Id,IT_Id) {
            
           dhxCashPMDetails = new dhtmlXWindows();
            
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
 
                bsCashPMDetailsWin = dhxCashPMDetails.createWindow("bsCashPMDetailsWin_" + IT_Id, x, y, 850, 400);
                bsCashPMDetailsWin.center();
                bsCashPMDetailsWin.button("minmax1").hide();
                bsCashPMDetailsWin.button("minmax2").hide();
                bsCashPMDetailsWin.button("park").hide();
                bsCashPMDetailsWin.setModal(true);
                bsCashPMDetailsWin.setText("Approve Cash Amount Receival");
                bsCashPMDetailsForm = bsCashPMDetailsWin.attachForm();
                preTally.Settings.progressOn(true, bsCashPMDetailsWin, null);

                var params = "ITID="+IT_Id+"&BSID="+BS_Id;
                bsCashPMDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/confirmCashPaymentDetails.php&"+params), function() {
                    
                    preTally.Settings.progressOff(true, bsCashPMDetailsWin, null);

                    var BS_BNKCombo =   bsCashPMDetailsForm.getCombo("BNK_Id");
                    var BS_BBCombo  =   bsCashPMDetailsForm.getCombo("BB_Id");
                    var BS_BACombo  =   bsCashPMDetailsForm.getCombo("BA_Id");
                    
                    if(bsCashPMDetailsForm.isItem("CHQ_Number"))  var BS_BChqCombo =   bsCashPMDetailsForm.getCombo("CHQ_Number");
                    
                    if (bsCashPMDetailsForm.isItem("DS_Description")) { 
                        bsCashPMDetailsForm.setItemValue("DS_Description",dhxCashPaymentGrid.getUserData(BS_Id,"LC_Name"));
                    }
                    if (bsCashPMDetailsForm.isItem("BS_RcvdFrm")) { 
                        bsCashPMDetailsForm.setItemValue("BS_RcvdFrm",dhxCashPaymentGrid.getUserData(BS_Id,"LC_Name"));
                    }
//                    if (bsCashPMDetailsForm.isItem("BS_PaidTo")) { 
//                        bsCashPMDetailsForm.setItemValue("BS_PaidTo",dhxCashPaymentGrid.getUserData(BS_Id,"LC_Name"));
//                    }
                    if (bsCashPMDetailsForm.isItem("Amount")) { 
                        bsCashPMDetailsForm.setItemValue("Amount",dhxCashPaymentGrid.getUserData(BS_Id,"BS_Amount"));
                    }
                    if (bsCashPMDetailsForm.isItem("TR_Id")) { 
                        bsCashPMDetailsForm.setItemValue("TR_Id",dhxCashPaymentGrid.getUserData(BS_Id,"TR_Track"));
                    }
                    
                    if (bsCashPMDetailsForm.isItem("LC_Id")) {  
                        var LC_IdCombo = bsCashPMDetailsForm.getCombo("LC_Id");
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                            var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                            bsCashPMDetailsForm.setItemValue("LC_Id",LC_Name);
                            LC_IdCombo.setComboValue(dhxCashPaymentGrid.getUserData(BS_Id,"LC_Id"));
                        });
                    }
                    
                    if (bsCashPMDetailsForm.isItem("BS_RcvdBy")) { 
                        var BS_RcvdByCombo = bsCashPMDetailsForm.getCombo("BS_RcvdBy");
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                            var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                            bsCashPMDetailsForm.setItemValue("BS_RcvdBy",rcvdByUser_Name);
                            BS_RcvdByCombo.setComboValue(dhxCashPaymentGrid.getUserData(BS_Id,"US_Id"));
                        });
                    }
//
                    if (bsCashPMDetailsForm.isItem("PM_Id")) {
                        bsCashPMDetailsForm.hideItem("BS_PaidDate");
                        bsCashPMDetailsForm.hideItem("BS_PayType");
                        bsCashPMDetailsForm.hideItem("BNK_Id");
                        bsCashPMDetailsForm.hideItem("BB_Id");
                        bsCashPMDetailsForm.hideItem("BA_Id");
                        bsCashPMDetailsForm.hideItem("CHQ_Number");
                        bsCashPMDetailsForm.hideItem("BS_Transaction");
                        bsCashPMDetailsForm.hideItem("BS_PayersBank");
                        bsCashPMDetailsForm.hideItem("BS_PayersChQ");
                        var item_list = new Array( "BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id","CHQ_Number", "BS_PayersChQ", "BS_PayersBank" );
                        var patmentMode = bsCashPMDetailsForm.getCombo("PM_Id");
                        patmentMode.setComboValue(bsCashPMDetailsForm.getUserData("PM_Id","cId")); 

                        if (bsCashPMDetailsForm.getUserData("PM_Id","cId") == '2'){
                            for(i=0;i<=10;i++){
                               bsCashPMDetailsForm.showItem(item_list[i]); 
                            }
                        }

                        patmentMode.attachEvent("onChange", function() {
                            var pmMode = patmentMode.getSelectedValue();
                            for(i=0;i<=10;i++){
                                 if (pmMode == '1'){ 
                                    bsCashPMDetailsForm.hideItem(item_list[i]); }
                                 else {  
                                    bsCashPMDetailsForm.showItem(item_list[i]); }
                            }

                        });
                        if (bsCashPMDetailsForm.isItem("BS_PayType")) {
                            BS_PayTypeCombo = bsCashPMDetailsForm.getCombo("BS_PayType");

                            BS_PayTypeCombo.attachEvent("onChange", function() {
                                var pmType = BS_PayTypeCombo.getSelectedValue();
                                if(pmType == '2'){
                                    bsCashPMDetailsForm.hideItem("BS_Transaction");
                                    bsCashPMDetailsForm.showItem("CHQ_Number");
                                }
                                if(pmType == '3'){
                                    bsCashPMDetailsForm.setItemLabel("BS_Transaction","DD Number" );
                                    bsCashPMDetailsForm.showItem("BS_Transaction");
                                    bsCashPMDetailsForm.hideItem("CHQ_Number");
                                    bsCashPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                }
                                if(pmType == '4' || pmType == '5' || pmType == '6'){
                                    bsCashPMDetailsForm.setItemLabel("BS_Transaction","Transaction Id" );
                                    bsCashPMDetailsForm.showItem("BS_Transaction");
                                    bsCashPMDetailsForm.hideItem("CHQ_Number");
                                    bsCashPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                }
                            });
                        }
                    }
                    
                    if (bsCashPMDetailsForm.isItem("BB_Id")) {

                        BS_BBCombo.readonly(true);
                        BS_BACombo.readonly(true);

                        BS_BNKCombo.attachEvent("onClose", function() {
                            BS_BBCombo.clearAll();
                            BS_BACombo.clearAll();

                            BS_BBCombo.setComboText('');
                            BS_BACombo.setComboText('');
                            BS_BACombo.setComboValue('');

                            if(bsCashPMDetailsForm.isItem("CHQ_Number")){
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
                        });

                        BS_BBCombo.attachEvent("onClose", function() {
                            BS_BACombo.clearAll();                        

                            BS_BACombo.setComboText('');
                            BS_BACombo.setComboValue('');

                            if(bsCashPMDetailsForm.isItem("CHQ_Number")){
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
                        });

                        BS_BACombo.attachEvent("onClose", function() {

                            if(bsCashPMDetailsForm.isItem("CHQ_Number")){
                                BS_BChqCombo.clearAll()

                                BS_BChqCombo.setComboText('');
                                BS_BChqCombo.setComboValue('');

                                var BNK_AC_Id = BS_BACombo.getSelectedValue();
                                if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                    var params="BA_Id=" + BNK_AC_Id;
                                    BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);
                                }
                            }
                        });
                    }
                    if (bsCashPMDetailsForm.isItem("BS_IEByLC")) {  
                        var ieByLCCombo = bsCashPMDetailsForm.getCombo("BS_IEByLC");
                        var ieByLCid   = bsCashPMDetailsForm.getUserData("BS_IEByLC","cId");
                        var ieByLCname = bsCashPMDetailsForm.getUserData("BS_IEByLC","cValue");
                        var params="";
                        if(!ieByLCid || ieByLCid == 0){
                            params = "&mask=Self";
                        }else{
                            params = "&mask="+ieByLCname;
                        }

                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"+params), function(xml){
                            ieByLCCombo.load(xml.xmlDoc.responseText);                                       
                            if(ieByLCid) bsCashPMDetailsForm.setItemValue("BS_IEByLC",ieByLCid);

                            var ieByUSCombo = bsCashPMDetailsForm.getCombo("BS_IEByUS");                                                              
                            var ieByUSid    = bsCashPMDetailsForm.getUserData("BS_IEByUS","cId");
                            var ieByUSname  = bsCashPMDetailsForm.getUserData("BS_IEByUS","cValue");
                            var params="";
                            if(!ieByUSid){
                                params = "&ctype=check&mask=Self&LCId="+ieByLCCombo.getSelectedValue();
                            }else{
                                params = "&ctype=check&mask="+ieByUSname+"&LCId="+ieByLCCombo.getSelectedValue();
                            }

                            ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php"+params));
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
                                ieByUSCombo.load(xml.xmlDoc.responseText);
                                if(ieByUSid) bsCashPMDetailsForm.setItemValue("BS_IEByUS",ieByUSid);
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
                    
                    bsCashPMDetailsForm.attachEvent("onButtonClick", function(name) {

                        if (name == "confirmCashPayment") {


                            if (bsCashPMDetailsForm.isItem("PM_Id")) {
                                var patmentMode = bsCashPMDetailsForm.getCombo("PM_Id");
                                var pmMode = patmentMode.getSelectedValue();
                                if (pmMode == 2){ 
        //                            bsCashPMDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
        //                            bsCashPMDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
                                    bsCashPMDetailsForm.setValidation('BNK_Id', 'NotEmpty'); 
                                    bsCashPMDetailsForm.setValidation('BB_Id', 'NotEmpty'); 
                                    bsCashPMDetailsForm.setValidation('BA_Id', 'NotEmpty'); 
                                    bsCashPMDetailsForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                                    bsCashPMDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty'); 

                                    if (bsCashPMDetailsForm.isItem("BS_PayType")) {
                                        var paymentType = bsCashPMDetailsForm.getCombo("BS_PayType");
                                        var pmType = paymentType.getSelectedValue();
                                        if(pmType == '2'){
                                            bsCashPMDetailsForm.setValidation('CHQ_Number', 'NotEmpty'); 
                                        }else if(pmType == '3' || pmType == '4' || pmType == '5'){
                                           bsCashPMDetailsForm.setItemValue("CHQ_Number","");
                                           bsCashPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                        }
                                    }
                                } else {
                                    bsCashPMDetailsForm.setItemValue("BNK_Id","");
                                    bsCashPMDetailsForm.setItemValue("BB_Id","");
                                    bsCashPMDetailsForm.setItemValue("BB_Id","");
                                    bsCashPMDetailsForm.setItemValue("BA_Id","");
                                    bsCashPMDetailsForm.setItemValue("CHQ_Number","");
                                    bsCashPMDetailsForm.setItemValue("BS_PayersBank","");
                                    bsCashPMDetailsForm.setItemValue("BS_PayersChQ","");
                                    bsCashPMDetailsForm.setValidation('BNK_Id', 'null'); 
                                    bsCashPMDetailsForm.setValidation('BB_Id', 'null'); 
                                    bsCashPMDetailsForm.setValidation('BB_Id', 'null');
                                    bsCashPMDetailsForm.setValidation('BA_Id', 'null');
                                    bsCashPMDetailsForm.setValidation('CHQ_Number', 'null');
                                    bsCashPMDetailsForm.setValidation('BS_PayersBank', 'null'); 
                                    bsCashPMDetailsForm.setValidation('BS_PayersChQ', 'null'); 
                                }
                            }

                            bsCashPMDetailsForm.attachEvent("onValidateError", function (name, value, result){
                               bsCashPMDetailsForm.setValidateCss(name, false, 'validate_red');
                               return false;
                            });
                            
                            var messageValidate = bsCashPMDetailsForm.validate();
                            if (messageValidate) {
                                preTally.Settings.progressOn(true, bsCashPMDetailsWin, null);
                                
                                bsCashPMDetailsForm.send(preTally.Initialize.encryptURL('warehouse/confirmPayment.php&') , function(loader, response) {
                                    preTally.Settings.progressOff(true, bsCashPMDetailsWin, null);
                                    if(response != 1 && response != 'fail') {
                                        bsCashPMDetailsForm.clear();
                                        dhxCashPMDetails.window("bsCashPMDetailsWin_"+ IT_Id).close();
                                        
                                        var filterValue = new Array($('#ieCNF').val(), $('#itmCNF').val(), $('#brnCNF').val(),$('#addCNF').val(),$( "#shCNF" ).val());
                                        dhxCashPaymentGrid.clearAll();
                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                        dhxCashPaymentGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listCashPaymentNotf.php&"+chartCashBSFilterParams+"&filter="+filterValue), function() { 
                                            $('.npc_cnt_tot').html("# : "+dhxCashPaymentGrid.getUserData("", "TL_Count")+" ");
                                            preTally.Settings.progressOff(true, dhxLayout, null);
                                        });
                                        //dhxCashPaymentGrid.clearAndLoad("requisites/reportCashBSData.php?"+encrypt(chartCashBSFilterParams), function() { });
                                    }else if(response == 'fail'){
                                        response = "Confirmation of this entry is Restricted.";
                                    } else {
                                        response = 'Invalid Entry. Please Verify.';
                                    }
                                    dhtmlx.message({text:response });
                                });
                            }
                        } else {
                            dhxCashPMDetails.window("bsCashPMDetailsWin_"+ IT_Id).close();
                        }
                    });
                });
        },
        confirmBankPaymentNotf: function(inp,BS_Id,IT_Id) {
            
           dhxBankPMDetails = new dhtmlXWindows();
            
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
 
                bsBankPMDetailsWin = dhxBankPMDetails.createWindow("bsBankPMDetailsWin_" + IT_Id, x, y, 850, 400);
                bsBankPMDetailsWin.center();
                bsBankPMDetailsWin.button("minmax1").hide();
                bsBankPMDetailsWin.button("minmax2").hide();
                bsBankPMDetailsWin.button("park").hide();
                bsBankPMDetailsWin.setModal(true);
                bsBankPMDetailsWin.setText("Approve Cash Amount Receival");
                bsBankPMDetailsForm = bsBankPMDetailsWin.attachForm();
                preTally.Settings.progressOn(true, bsBankPMDetailsWin, null);

                var params = "ITID="+IT_Id+"&BSID="+BS_Id;
                bsBankPMDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/confirmBankPaymentDetails.php&"+params), function() {
                    
                    preTally.Settings.progressOff(true, bsBankPMDetailsWin, null);

                    var BS_BNKCombo =   bsBankPMDetailsForm.getCombo("BNK_Id");
                    var BS_BBCombo  =   bsBankPMDetailsForm.getCombo("BB_Id");
                    var BS_BACombo  =   bsBankPMDetailsForm.getCombo("BA_Id");
                    if(bsBankPMDetailsForm.isItem("CHQ_Number"))  var BS_BChqCombo =   bsBankPMDetailsForm.getCombo("CHQ_Number");
                    
//                    if (bsBankPMDetailsForm.isItem("DS_Description")) { 
//                        bsBankPMDetailsForm.setItemValue("DS_Description",dhxBankPaymentGrid.getUserData(BS_Id,"DS_Description"));
//                    }
//                    if (bsBankPMDetailsForm.isItem("BS_RcvdFrm")) { 
//                        bsBankPMDetailsForm.setItemValue("BS_RcvdFrm",dhxBankPaymentGrid.getUserData(BS_Id,"DS_Description"));
//                    }
//                    if (bsBankPMDetailsForm.isItem("BS_PaidTo")) { 
//                        bsBankPMDetailsForm.setItemValue("BS_PaidTo",dhxBankPaymentGrid.getUserData(BS_Id,"LC_Name"));
//                    }
                    if (bsBankPMDetailsForm.isItem("Amount")) { 
                        bsBankPMDetailsForm.setItemValue("Amount",dhxBankPaymentGrid.getUserData(BS_Id,"BS_Amount"));
                    }
                    if (bsBankPMDetailsForm.isItem("TR_Id")) { 
                        bsBankPMDetailsForm.setItemValue("TR_Id",dhxBankPaymentGrid.getUserData(BS_Id,"TR_Track"));
                    }
                    
                    if (bsBankPMDetailsForm.isItem("BS_RcvdFrm")) { 
                        bsBankPMDetailsForm.setItemValue("BS_RcvdFrm",dhxBankPaymentGrid.getUserData(BS_Id,"LC_Name"));
                    }
                    
                    if (bsBankPMDetailsForm.isItem("BS_Description")) { 
                        
                        bsBankPMDetailsForm.setItemValue("BS_Description",dhxBankPaymentGrid.getUserData(BS_Id,"LC_Name"));
                        
                        var DS_IdCombo = bsBankPMDetailsForm.getCombo("BS_Description");
                        DS_IdCombo.setComboText(dhxBankPaymentGrid.getUserData(BS_Id,"LC_Name"));
                        bsBankPMDetailsForm.setItemValue("DS_Description", DS_IdCombo.getComboText());
                                         
                        DS_IdCombo.attachEvent("onChange", function() {
                            bsBankPMDetailsForm.setItemValue("DS_Description", DS_IdCombo.getComboText());
                        });
                    }
                    
                    if (bsBankPMDetailsForm.isItem("LC_Id")) {  
                        var LC_IdCombo = bsBankPMDetailsForm.getCombo("LC_Id");
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"), function(xml){
                            var LC_Name = LC_IdCombo.load(xml.xmlDoc.responseText);
                            bsBankPMDetailsForm.setItemValue("LC_Id",LC_Name);
                            LC_IdCombo.setComboValue(dhxBankPaymentGrid.getUserData(BS_Id,"LC_Id"));
                        });
                    }
                    
                    if (bsBankPMDetailsForm.isItem("BS_RcvdBy")) { 
                        var BS_RcvdByCombo = bsBankPMDetailsForm.getCombo("BS_RcvdBy");
                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"), function(xml){
                            var rcvdByUser_Name = BS_RcvdByCombo.load(xml.xmlDoc.responseText);
                            bsBankPMDetailsForm.setItemValue("BS_RcvdBy",rcvdByUser_Name);
                            BS_RcvdByCombo.setComboValue(dhxBankPaymentGrid.getUserData(BS_Id,"US_Id"));
                        });
                    }
//
                    if (bsBankPMDetailsForm.isItem("PM_Id")) {
                        bsBankPMDetailsForm.hideItem("BS_PaidDate");
                        bsBankPMDetailsForm.hideItem("BS_PayType");
                        bsBankPMDetailsForm.hideItem("BNK_Id");
                        bsBankPMDetailsForm.hideItem("BB_Id");
                        bsBankPMDetailsForm.hideItem("BA_Id");
                        bsBankPMDetailsForm.hideItem("CHQ_Number");
                        bsBankPMDetailsForm.hideItem("BS_Transaction");
                        bsBankPMDetailsForm.hideItem("BS_PayersBank");
                        bsBankPMDetailsForm.hideItem("BS_PayersChQ");
                        var item_list = new Array( "BS_PaidDate", "BS_PayType", "BNK_Id", "BB_Id", "BA_Id","CHQ_Number", "BS_PayersChQ", "BS_PayersBank" );
                        var patmentMode = bsBankPMDetailsForm.getCombo("PM_Id");
                        patmentMode.setComboValue(bsBankPMDetailsForm.getUserData(BS_Id,"PM_Id")); 

                        if (bsBankPMDetailsForm.getUserData(BS_Id,"PM_Id") == '2'){
                            for(i=0;i<=10;i++){
                               bsBankPMDetailsForm.showItem(item_list[i]); 
                            }
                        }

                        patmentMode.attachEvent("onChange", function() {
                            var pmMode = patmentMode.getSelectedValue();
                            for(i=0;i<=10;i++){
                                 if (pmMode == '1'){ 
                                    bsBankPMDetailsForm.hideItem(item_list[i]); }
                                 else {  
                                    bsBankPMDetailsForm.showItem(item_list[i]); }
                            }

                        });
                        if (bsBankPMDetailsForm.isItem("BS_PayType")) {
                            BS_PayTypeCombo = bsBankPMDetailsForm.getCombo("BS_PayType");

                            BS_PayTypeCombo.attachEvent("onChange", function() {
                                var pmType = BS_PayTypeCombo.getSelectedValue();
                                if(pmType == '2'){
                                    bsBankPMDetailsForm.hideItem("BS_Transaction");
                                    bsBankPMDetailsForm.showItem("CHQ_Number");
                                }
                                if(pmType == '3'){
                                    bsBankPMDetailsForm.setItemLabel("BS_Transaction","DD Number" );
                                    bsBankPMDetailsForm.showItem("BS_Transaction");
                                    bsBankPMDetailsForm.hideItem("CHQ_Number");
                                    bsBankPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                }
                                if(pmType == '4' || pmType == '5' || pmType == '6'){
                                    bsBankPMDetailsForm.setItemLabel("BS_Transaction","Transaction Id" );
                                    bsBankPMDetailsForm.showItem("BS_Transaction");
                                    bsBankPMDetailsForm.hideItem("CHQ_Number");
                                    bsBankPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                }
                            });
                        }
                    }
                    if (bsBankPMDetailsForm.isItem("BB_Id")) {

                        BS_BBCombo.readonly(true);
                        BS_BACombo.readonly(true);

                        BS_BNKCombo.attachEvent("onClose", function() {
                            BS_BBCombo.clearAll();
                            BS_BACombo.clearAll();

                            BS_BBCombo.setComboText('');
                            BS_BACombo.setComboText('');
                            BS_BACombo.setComboValue('');

                            if(bsBankPMDetailsForm.isItem("CHQ_Number")){
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
                        });

                        BS_BBCombo.attachEvent("onClose", function() {
                            BS_BACombo.clearAll();                        

                            BS_BACombo.setComboText('');
                            BS_BACombo.setComboValue('');

                            if(bsBankPMDetailsForm.isItem("CHQ_Number")){
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
                        });

                        BS_BACombo.attachEvent("onClose", function() {

                            if(bsBankPMDetailsForm.isItem("CHQ_Number")){
                                BS_BChqCombo.clearAll()

                                BS_BChqCombo.setComboText('');
                                BS_BChqCombo.setComboValue('');

                                var BNK_AC_Id = BS_BACombo.getSelectedValue();
                                if ((BNK_AC_Id) && (BNK_AC_Id != 0)) {
                                    var params="BA_Id=" + BNK_AC_Id;
                                    BS_BChqCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/getAccountChqNo.php&"+params),false);
                                }
                            }
                        });
                    }
                    if (bsBankPMDetailsForm.isItem("BS_IEByLC")) {  
                        var ieByLCCombo = bsBankPMDetailsForm.getCombo("BS_IEByLC");
                        var ieByLCid   = bsBankPMDetailsForm.getUserData("BS_IEByLC","cId");
                        var ieByLCname = bsBankPMDetailsForm.getUserData("BS_IEByLC","cValue");
                        var params="";
                        if(!ieByLCid || ieByLCid == 0){
                            params = "&mask=Self";
                        }else{
                            params = "&mask="+ieByLCname;
                        }

                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/locations.php"+params), function(xml){
                            ieByLCCombo.load(xml.xmlDoc.responseText);                                       
                            if(ieByLCid) bsBankPMDetailsForm.setItemValue("BS_IEByLC",ieByLCid);

                            var ieByUSCombo = bsBankPMDetailsForm.getCombo("BS_IEByUS");                                                              
                            var ieByUSid    = bsBankPMDetailsForm.getUserData("BS_IEByUS","cId");
                            var ieByUSname  = bsBankPMDetailsForm.getUserData("BS_IEByUS","cValue");
                            var params="";
                            if(!ieByUSid){
                                params = "&ctype=check&mask=Self&LCId="+ieByLCCombo.getSelectedValue();
                            }else{
                                params = "&ctype=check&mask="+ieByUSname+"&LCId="+ieByLCCombo.getSelectedValue();
                            }

                            ieByUSCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/persons.php"+params));
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php"+params), function(xml){
                                ieByUSCombo.load(xml.xmlDoc.responseText);
                                if(ieByUSid) bsBankPMDetailsForm.setItemValue("BS_IEByUS",ieByUSid);
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
                    
                    bsBankPMDetailsForm.attachEvent("onButtonClick", function(name) {

                        if (name == "confirmBankPayment") {


                            if (bsBankPMDetailsForm.isItem("PM_Id")) {
                                var patmentMode = bsBankPMDetailsForm.getCombo("PM_Id");
                                var pmMode = patmentMode.getSelectedValue();
                                if (pmMode == 2){ 
        //                            bsBankPMDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
        //                            bsBankPMDetailsForm.setValidation('BS_PayType', 'NotEmpty'); 
                                    bsBankPMDetailsForm.setValidation('BNK_Id', 'NotEmpty'); 
                                    bsBankPMDetailsForm.setValidation('BB_Id', 'NotEmpty'); 
                                    bsBankPMDetailsForm.setValidation('BA_Id', 'NotEmpty'); 
                                    bsBankPMDetailsForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                                    bsBankPMDetailsForm.setValidation('BS_PayersChQ', 'NotEmpty'); 

                                    if (bsBankPMDetailsForm.isItem("BS_PayType")) {
                                        var paymentType = bsBankPMDetailsForm.getCombo("BS_PayType");
                                        var pmType = paymentType.getSelectedValue();
                                        if(pmType == '2'){
                                            bsBankPMDetailsForm.setValidation('CHQ_Number', 'NotEmpty'); 
                                        }else if(pmType == '3' || pmType == '4' || pmType == '5'){
                                           bsBankPMDetailsForm.setItemValue("CHQ_Number","");
                                           bsBankPMDetailsForm.setValidation('CHQ_Number', 'null'); 
                                        }
                                    }
                                } else {
                                    bsBankPMDetailsForm.setItemValue("BNK_Id","");
                                    bsBankPMDetailsForm.setItemValue("BB_Id","");
                                    bsBankPMDetailsForm.setItemValue("BB_Id","");
                                    bsBankPMDetailsForm.setItemValue("BA_Id","");
                                    bsBankPMDetailsForm.setItemValue("CHQ_Number","");
                                    bsBankPMDetailsForm.setItemValue("BS_PayersBank","");
                                    bsBankPMDetailsForm.setItemValue("BS_PayersChQ","");
                                    bsBankPMDetailsForm.setValidation('BNK_Id', 'null'); 
                                    bsBankPMDetailsForm.setValidation('BB_Id', 'null'); 
                                    bsBankPMDetailsForm.setValidation('BB_Id', 'null');
                                    bsBankPMDetailsForm.setValidation('BA_Id', 'null');
                                    bsBankPMDetailsForm.setValidation('CHQ_Number', 'null');
                                    bsBankPMDetailsForm.setValidation('BS_PayersBank', 'null'); 
                                    bsBankPMDetailsForm.setValidation('BS_PayersChQ', 'null'); 
                                }
                            }

                            bsBankPMDetailsForm.attachEvent("onValidateError", function (name, value, result){
                               bsBankPMDetailsForm.setValidateCss(name, false, 'validate_red');
                               return false;
                            });
                            
                            var messageValidate = bsBankPMDetailsForm.validate();
                            if (messageValidate) {
                                preTally.Settings.progressOn(true, bsBankPMDetailsWin, null);
                                
                                bsBankPMDetailsForm.send(preTally.Initialize.encryptURL('warehouse/confirmPayment.php&') , function(loader, response) {
                                    preTally.Settings.progressOff(true, bsBankPMDetailsWin, null);
                                    if(response != 1 && response != 'fail') {
                                        bsBankPMDetailsForm.clear();
                                        dhxBankPMDetails.window("bsBankPMDetailsWin_"+ IT_Id).close();
                                        
                                        var filterValue = new Array($('#ieBNF').val(), $('#itmBNF').val(), $('#brnBNF').val(),$('#addBNF').val(),$( "#shBNF" ).val());
                                        dhxBankPaymentGrid.clearAll();
                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                        dhxBankPaymentGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBankPaymentNotf.php&filter="+filterValue), function() { 
                                            $('.npb_cnt_tot').html("# : "+dhxBankPaymentGrid.getUserData("", "TL_Count")+" ");                
                                            preTally.Settings.progressOff(true, dhxLayout, null);
                                        });
                                        //dhxBankPaymentGrid.clearAndLoad("requisites/reportCashBSData.php?"+encrypt(chartCashBSFilterParams), function() { });
                                    }else if(response == 'fail'){
                                        dhtmlx.message({text : "Confirmation of this entry is Restricted."});
                                    } else {
                                        response = 'Invalid Entry. Please Verify.';
                                    }
                                    dhtmlx.message({text:response });
                                });
                            }
                        } else {
                            dhxBankPMDetails.window("bsBankPMDetailsWin_"+ IT_Id).close();
                        }
                    });
                });
        },
        sendNotfCorrectionMsg: function(inp, BS_Id, SH_Id) {

            dhxNTMsgWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            rptNTMsgWin = dhxNTMsgWin.createWindow("rptNTMsgWin_" + BS_Id, x, y, 540, 400);
            rptNTMsgWin.center();
            rptNTMsgWin.button("minmax1").hide();
            rptNTMsgWin.button("minmax2").hide();
            rptNTMsgWin.button("park").hide();
            rptNTMsgWin.setModal(true);
            rptNTMsgWin.setText("Send Correction Message");
            rptNTMsgForm = rptNTMsgWin.attachForm();
            preTally.Settings.progressOn(true, rptNTMsgWin, null);            
            rptNTMsgForm.loadStruct(preTally.Initialize.encryptURL("requisites/sendCorrectionMsg.php"), function() { 
                
                rptNTMsgForm.setItemValue("US_Id", listItemNoftGrid.getUserData(BS_Id, "US_Id"));
                rptNTMsgForm.setItemValue("MSG_To", listItemNoftGrid.getUserData(BS_Id, "US_Name"));
                rptNTMsgForm.setItemValue("Entry", listItemNoftGrid.getUserData(BS_Id, "Entry"));
                rptNTMsgForm.setItemValue("BS_Id", BS_Id);
                rptNTMsgForm.setItemValue("SH_Id", SH_Id);
                preTally.Settings.progressOff(true, rptNTMsgWin, null);
                rptNTMsgForm.attachEvent("onButtonClick", function(name) {
                    if (name == "rptMsgSend") {
                        var messageValidate = rptNTMsgForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, rptNTMsgWin, null);
                            var param = "type=notf";
                            rptNTMsgForm.send(preTally.Initialize.encryptURL("warehouse/sendCorrectionMsg.php&"+param), function(loader, response) {
                                preTally.Settings.progressOff(true, rptNTMsgWin, null);
                                if(response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    rptNTMsgForm.clear();
                                    dhxNTMsgWin.window("rptNTMsgWin_" + BS_Id).close();
                                }
                                else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if(name == 'addRecpt') {
                        dhxNTUserWin = new dhtmlXWindows();
                        recpNTWin = dhxNTUserWin.createWindow("addRecpWin", x, y, 1050, 350);
                        recpNTWin.button("minmax1").hide();
                        recpNTWin.button("minmax2").hide();
                        recpNTWin.button("park").hide();
                        recpNTWin.center();
                        recpNTWin.setModal(true);
                        recpNTWin.setText("Add Recipients");
                        listNTUserGrid = recpNTWin.attachGrid();                        
                        listNTUserGrid.enableAutoWidth(true);                        
                        preTally.Settings.progressOn(true, recpNTWin, null);         

                        var listRecipientsXML= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked="+rptNTMsgForm.getItemValue('US_Id')); 
                        listNTUserGrid.parse(listRecipientsXML.xmlDoc.responseText,function(){
                            preTally.Settings.progressOff(true, recpNTWin, null);
                        });

                        recpNTWin.attachStatusBar({
                            text  : "<input type='button' value='SAVE' onclick='preTally.Notification.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });
                        
                    } else {
                        rptNTMsgForm.resetValidateCss();
                        rptNTMsgForm.clear();
                    }
                });
            });
        },
        addRecipients: function() {           
            var userDetails = []; 
            var userIds     = [];
            
            listNTUserGrid.forEachRow(function(rId){
                if(listNTUserGrid.cells(rId,6).getValue() == 1) { // if checked
                    userDetails.push(listNTUserGrid.cells(rId,2).getValue());
                    userIds.push(rId);
                }
            });
            
            rptNTMsgForm.setItemValue("MSG_To",userDetails);
            rptNTMsgForm.setItemValue('US_Id',userIds);
            dhxNTUserWin.window("addRecpWin").close();
             
        },
        editBalSheetDetailsFromMsg : function (inp, BS_Id, SH_Id) {
            dhxBSMsgDetails = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);

            bsMsgDetailsWin = dhxBSMsgDetails.createWindow("bsDetailsWinMsg_" + BS_Id, x, y, 900, 400);
            bsMsgDetailsWin.center();
            bsMsgDetailsWin.button("minmax1").hide();
            bsMsgDetailsWin.button("minmax2").hide();
            bsMsgDetailsWin.button("park").hide();
            bsMsgDetailsWin.setModal(true);
            bsMsgDetailsWin.setText("Edit Details");
            bsMsgDetailsForm = bsMsgDetailsWin.attachForm();
            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);

            var params = "SHID="+SH_Id+"&BSID="+BS_Id;
            bsMsgDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/reportCashBSEditMsg.php&"+params), function() {

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

                        bsMsgDetailsForm.setValidation('BS_Amount', 'ValidInteger,NotEmpty');
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
                        if (bsMsgDetailsForm.isItem("BS_Description")) bsMsgDetailsForm.setValidation('BS_Description', 'ValidInteger,NotEmpty');

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
                        
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, bsMsgDetailsWin, null);
                            var params="bsId="+BS_Id+"&TR_Track="+BS_TRCombo.getSelectedText();
                            bsMsgDetailsForm.send(preTally.Initialize.encryptURL('warehouse/BSItemForm.php&'+params) , function(loader, response) {
                                preTally.Settings.progressOff(true, bsMsgDetailsWin, null);
                                if(response != 1 && response != 2 ) {
                                    bsMsgDetailsForm.clear();
                                    dhxBSMsgDetails.window("bsDetailsWinMsg_"+ BS_Id).close();
                                } else if (response == '2'){
                                    response = 'Edit is restricted for this entry.';
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
        }
    };
})(jQuery, this);