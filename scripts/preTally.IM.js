;(function ($, window, undefined) {
    var myUSID = unescape(JGG1P3bDnUSDL3Mui7KzYjj28UjPdWxCCtGkJSHeuo);
    var myNAME = unescape(JGG1P3bDnUSDL21Mui7KzYjj28UjPdWxCCtGkJSHeuo);
    
    var $pCaption       = {};
    var iProgress       = {};
    var aProgress       = {};
    var iProgressCTX    = {};
            
    //var IMHistDays = ['','Today','Yesterday'];
    function IMTimePopulate(metime) {
        var meDate;
        if (metime === '0') {
            meDate = new Date();
        } else {
            meDate = new Date(metime);
        }
        var meDateHrs = meDate.getHours();
        var meDateMin = meDate.getMinutes();
        var meDateAPM = 'AM';
        if (meDateHrs > 12) {
            meDateHrs = meDateHrs - 12;
            meDateAPM = 'PM';
        }
        if (meDateMin < 10) {
            meDateMin = '0' + meDateMin;
        }
        return meDateHrs + ':' + meDateMin + ' ' + meDateAPM;
    }
    function DateToDisplayDate(rawDate) {
        /*if(rawDate == 'Today') {
         rawDate = Date.toISOString() 
         } else if(rawDate == 'Yesterday') {
         rawDate = Date.parse('yesterday').toISOString() 
         }*/
        //console.log('Input RawDate : '+rawDate);
        //rawDate = Date.parse(rawDate) 
        var date1 = new Date();
        var date2 = new Date(rawDate);
        var diffDays = Math.round((date1 - date2) / (1000 * 3600 * 24));

        //console.log('Input RawDate : '+rawDate);
        //console.log('Date Difference : '+diffDays);

        if (diffDays == 0) {
            return 'Today';
        } else if (diffDays == 1) {
            return 'Yesterday';
        } else {
            var IMPresentDay = new Date(rawDate);
            return IMPresentDay.toString('dddd,  MMMM d,  yyyy');
        }
    }
    function dhxIMMembersProfileImage(USIMID) {
        if (!$.isNumeric(USIMID)) {
            USIMID = USIMID.replace("session", "");
        }
        var IMPP = dhxIMMembers.getUserData('session' + USIMID, "IMPP");
        //console.log(USIMID);
        //console.log(IMPP);
        var IMPPImage = 'images/icon/user_80.png';
        if (IMPP != 'avatar.png') {
            IMPPImage = 'uploads/profileImage/' + IMPP
        }
        //console.log(IMPPImage);
        return IMPPImage;
    }
    function IMNotificationBubble() {
        $('#noti_bubble').show();
        var pendingChatCount = 0;
        dhxIMRecentMembers.forEachRow(function (id) {
            if (dhxIMRecentMembers.cellById(id, 2).getValue() != '') {
                pendingChatCount++;
            }
        });
        $('#noti_bubble').html(pendingChatCount);
        if (pendingChatCount == 0) {
            $('#noti_bubble').hide();
        }
        ;
    }

    preTally.IM = {
        IMInitialize: function () {
            
            
            //var $pc = $('#progressController');
            /*var $pCaption = $('.IMProgress p');
            var iProgress = document.getElementById('inactiveProgress');
            var aProgress = document.getElementById('activeProgress');
            var iProgressCTX = iProgress.getContext('2d');


            drawInactive(iProgressCTX);
            
            var percentage = 0 / 100;*/
            //drawProgress(aProgress, percentage, $pCaption);
        
        
        
        
            var IMTyping = {};
            var IMTimeout = {};
            var allIMUserObj;

            
            

            if (!dhxMiddleBlockTabs.cells("IMTab")) {
                preTally.Settings.progressOn(true, dhxLayout, null);
                dhxToolbar.setItemImage('im', 'im.png');
                dhxMiddleBlockTabs.addTab("IMTab", "<img src='images/icon/im.png' style='margin-top:2px; width:16px; height:16px;' id='chatTabIcon' />&nbsp;&nbsp;IM", 100);
                dhxMiddleBlockTabs.tabs("IMTab").setActive();

                dhxIMLayout = dhxMiddleBlockTabs.tabs("IMTab").attachLayout("2U");
                dhxIMLayout.cells("a").setText('<div class="chatMemberHeading">Members</div> <div id="noti_Container" class="noti_Container"><img src="images/icon/im.png" /><div id="noti_bubble" class="noti_bubble">0</div></div><br />\
                                                <div class="chatMemberSearch"><input placeholder="Search" class="chatMemberSearchInput" id="IMSearch" /><img src="images/icon/IMIcon/close_18.png" class="chatMemberSearchClose" /></div>\
                                                <img src="images/icon/IMIcon/home_20.png" id="IMHomeIcon" class="IMIcon" /><img src="images/icon/IMIcon/info_20.png" id="IMProfileIcon" class="IMIcon" /><img src="images/icon/IMIcon/plus_20.png" id="IMRoomIcon" class="IMIcon" /><img src="images/icon/IMIcon/pin_20.png" id="IMDummyIcon" class="IMIcon" />\
                                              ');
                dhxIMLayout.cells("a").setWidth(300);
                dhxIMLayout.cells("b").setText("Chats");
                dhxIMLayout.cells("b").hideHeader();
                dhxIMLayout.cells("a").hideArrow();
                dhxIMLayout.cells("a")['cell']['firstChild']['style'] = 'height:105px;';

                var IMTabs = dhxIMLayout.cells("a").attachTabbar();
                IMTabs.setArrowsMode("auto");
                IMTabs.addTab("a1", "Contacts", "100px");
                IMTabs.addTab("a2", "Recent", "100px");
                IMTabs.tabs("a2").setActive();

                //---------------- Show Header For Layout IM -------------------
                dhxIMLayout.cells("a").showHeader();

                IMChatTab = dhxIMLayout.cells("b").attachTabbar();
                IMChatTab.addTab("User_Info", "Information", 100);
                IMChatTab.tabs("User_Info").setActive();

                //-------------- All Members Grid ------------------------------
                dhxIMMembers = IMTabs.cells('a1').attachGrid();
                dhxIMMembers.enableTooltips("false,false");
                dhxIMMembers.setImagePath("assets/grid/codebase/imgs/");
                dhxIMMembers.setHeader('#cspan,#cspan');
                dhxIMMembers.setNoHeader(true);  //hides the header
                dhxIMMembers.setInitWidths("30,*");
                dhxIMMembers.setColAlign("center,left");
                dhxIMMembers.setColTypes("ro,ro");
                dhxIMMembers.setColSorting("str,str");
                dhxIMMembers.init();
                dhxIMMembers.objBox.style.overflowX = "hidden";
                //dhxIMMembers.objBox.style.overflowY = "hidden";

                //-------------- Recent Members Grid ---------------------------
                dhxIMRecentMembers = IMTabs.cells('a2').attachGrid();
                dhxIMRecentMembers.enableTooltips("false,false");
                dhxIMRecentMembers.setImagePath("assets/grid/codebase/imgs/");
                dhxIMRecentMembers.setHeader('#cspan,#cspan,#cspan,#cspan');
                dhxIMRecentMembers.setNoHeader(true);  //hides the header
                dhxIMRecentMembers.setInitWidths("60,*,30,20");
                dhxIMRecentMembers.setColAlign("left,left,center,center");
                dhxIMRecentMembers.setColTypes("ro,ro,ro,ro");
                dhxIMRecentMembers.setColSorting("str,str,na,na");
                dhxIMRecentMembers.setColumnColor("white,white,white,white");
                dhxIMRecentMembers.enableRowsHover(true, "IMRowhover");
                dhxIMRecentMembers.enableCollSpan(true);
                dhxIMRecentMembers.init();
                dhxIMRecentMembers.objBox.style.overflowX = "hidden";

                /*dhxIMRecentMembers.attachEvent("onMouseOver", function(id,ind){
                 // your code here
                 console.log(id);
                 console.log(id.indexOf("dummyRow"));
                 if(id.indexOf("dummyRow") >= 0) {
                 dhxIMRecentMembers.setRowColor(id,"red");
                 }
                 });*/
                dhxIMRecentMembers.attachEvent("onBeforeSelect", function (new_row, old_row) {
                    if (new_row.indexOf("dummyRow") >= 0) {
                        return false;
                    }
                    return true;
                });

                //-------------- Search Members PopUp ---------------------------
                var IMSearchPopUp = new dhtmlXPopup({mode: "bottom"});
                IMSearchPopUp.show(0, 0, 0, 0);

                //-------------- Search Members Grid ---------------------------
                var GHHeight = $(document).height() - 158;
                //console.log($(document).height()+' -- '+GHHeight);
                IMSearchGrid = IMSearchPopUp.attachGrid(298, GHHeight);
                IMSearchGrid.setHeader('#cspan,#cspan,#cspan,#cspan');
                IMSearchGrid.setNoHeader(true);  //hides the header
                IMSearchGrid.setInitWidths("30,*,30,20");
                IMSearchGrid.setColAlign("center,left,center,center");
                IMSearchGrid.setColTypes("ro,ro,ro,ro");
                IMSearchGrid.init();
                IMSearchGrid.objBox.style.overflowX = "hidden";

                $('#' + IMSearchPopUp._nodeId).parent().css("cssText", "padding: 0px !important;");
                $('#' + IMSearchPopUp._nodeId).parents('.dhx_popup_area').css("cssText", "border:none !important; padding: 0px !important; box-shadow: 0px !important; margin-left: 12px !important; position:fixed !important; left:-9px; top:117px;");
                $('#' + IMSearchPopUp._nodeId).parents('.dhx_popup_dhx_skyblue').children('.dhx_popup_arrow').css("cssText", "display: none !important;");
                IMSearchPopUp.hide();
                $('.chatMemberSearchClose').hide();

                $('.chatMemberSearchClose').click(function () {
                    IMSearchPopUp.hide();
                    popupDisplayStatus = 0;
                    $('.chatMemberSearchClose').hide();
                    $("#IMSearch").val('');
                });
                $('#IMHomeIcon').click(function () {
                    //alert(1);
                    IMChatTab.tabs("User_Info").setActive();

                });
                $('#IMProfileIcon').click(function () {
                    //alert(1);

                });
                //socket.emit('userlist', 'session'+myUSID, myNAME);
                //-------------- Load All Members Grid -------------------------
                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/userDataIM.php&IMS=1"), function (dataSearch) {

                    allIMUserObj = JSON.parse(dataSearch.xmlDoc.responseText);
                    $.each(allIMUserObj, function (key, value) {
                        dhxIMMembers.addRow('session' + value['US_Id'], ['<img src="images/icon/IMIcon/of.png" />', value['US_FName'] + ' ' + value['US_LName']]);
                        dhxIMMembers.setUserData('session' + value['US_Id'], "IMPP", value['US_Image']);
                        IMSearchGrid.addRow('session' + value['US_Id'], ['<img src="images/icon/IMIcon/of.png" />', value['US_FName'] + ' ' + value['US_LName']]);
                    });
                    //socket.emit('userlist', 'session'+myUSID, myNAME);

                    // To List All Recent Users From Last Week Whom I Chat
                    socket.on("UnReadChatUserListLoad", function (data) {

                        //console.log('Am Here To Test................');
                        var weekday = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Today");
                        var thisWeekDay = 7;
                        for (var index in data) {
                            if (data.hasOwnProperty(index)) {
                                var RULID = 0;
                                var RULName = 0;
                                var RULDate = new Date(data[index]['CDate']);
                                //console.log(data[index]['CDate']);
                                if (data[index]['CFrom'] == myUSID) {
                                    RULID = data[index]['CTo'];
                                    RULName = dhxIMMembers.cellById('session' + data[index]['CTo'], 1).getValue();
                                } else if (data[index]['CTo'] == myUSID) {
                                    RULID = data[index]['CFrom'];
                                    RULName = dhxIMMembers.cellById('session' + data[index]['CFrom'], 1).getValue();
                                }
                                if (thisWeekDay != weekday[RULDate.getDay()]) {
                                    var todayDate = new Date();
                                    var displayDay = weekday[RULDate.getDay()];
                                    if (todayDate.getDate() === RULDate.getDate()) {
                                        var displayDay = 'Today';
                                    }
                                    if ((todayDate.getDate() - 1) === RULDate.getDate()) {
                                        var displayDay = 'Yesterday';
                                    }
                                    dhxIMRecentMembers.addRow('dummyRow' + RULID, ['<div class="dummyRowOlder">' + displayDay + '</div>', , , ]);
                                    thisWeekDay = weekday[RULDate.getDay()];
                                    dhxIMRecentMembers.setColspan('dummyRow' + RULID, 0, 2);
                                }


                                /*var IMPP = dhxIMMembers.getUserData('session'+RULID,"IMPP");
                                 var IMPPImage = 'images/icon/user_80.png';
                                 if(IMPP != 'avatar.png') {
                                 IMPPImage = 'uploads/profileImage/'+IMPP
                                 }*/

                                dhxIMRecentMembers.addRow('session' + RULID, ['<div class="IMProfilePic"><img src="' + dhxIMMembersProfileImage(RULID) + '" class="IMPPImg" /><img src="images/icon/IMIcon/of_circle_14.png" id="IMRUserStatus_session' + RULID + '" class="IMPPIcon" /></div>', RULName, , ]);
                            }
                        }
                        //return;
                        //----------- Trigger Online Users After Loading Recent Users-------------
                        //socket.emit('userlist', 'session'+myUSID, myNAME);

                        // To List All Users For the Firsat Time While IM Initiates
                        socket.on("OnlineUserList", function (data) {
                            for (var index in data) {
                                if (data.hasOwnProperty(index)) {
                                    if (dhxIMMembers.doesRowExist(data[index]['CID']) && ('session' + myUSID != data[index]['CID'])) {
                                        dhxIMMembers.cellById(data[index]['CID'], 0).setValue('<img src="images/icon/IMIcon/on.png" />');
                                        IMSearchGrid.cellById(data[index]['CID'], 0).setValue('<img src="images/icon/IMIcon/on.png" />');
                                        $('#IMRUserStatus_' + data[index]['CID']).attr({'src': 'images/icon/IMIcon/on_tick_14.png'});
                                    }
                                }
                            }
                            dhxIMMembers.sortRows(1, "str", "asc");
                            dhxIMMembers.sortRows(0, "str", "desc");

                            IMSearchGrid.sortRows(1, "str", "asc");
                            IMSearchGrid.sortRows(0, "str", "desc");

                            preTally.Settings.progressOff(true, dhxLayout, null);

                        });
                        socket.emit('OnlineUserList', 'session' + myUSID);
                    });

                    socket.on("updatemychat", function (CHTMsg, CHTFrom, CHTTo) {
                        if (dhxMiddleBlockTabs.cells("IMTab")) {
                            if (IMChatTab.cells(CHTTo)) {
                                var thisClass = 'IMChatContent';
                                if ($('#IMChatContainer' + CHTTo).children().last().hasClass('me')) {
                                    thisClass = 'IMChatContentBox';
                                }
                                $('#IMChatContainer' + CHTTo).append('<i class="IMChatTime">' + IMTimePopulate('0') + '</i><div class="' + thisClass + ' me">' + CHTMsg + '</div>');
                                $('#IMChatContainer' + CHTTo).scrollTop($('#IMChatContainer' + CHTTo)[0].scrollHeight);
                            }
                        }
                    });

                    socket.on("updateUnReadChat", function (CHTID, CHTMsg, CHTFrom, CHTTo, CHDate) {
                        if (dhxIMRecentMembers.doesRowExist(CHTFrom)) {
                            dhxIMRecentMembers.cellById(CHTFrom, 2).setValue('<img src="images/icon/IMIcon/nc.gif" />');
                        } else {
                            if ('session' + myUSID != CHTFrom) {
                                if (!dhxIMRecentMembers.doesRowExist('dummyRowOlder')) {
                                    dhxIMRecentMembers.addRow('dummyRowOlder', ['<div class="dummyRowOlder">Older</div>', , , ]);
                                }
                                dhxIMRecentMembers.addRow(CHTFrom, ['<div class="IMProfilePic"><img src="' + dhxIMMembersProfileImage(CHTFrom) + '" class="IMPPImg" /><img src="images/icon/IMIcon/of_circle_14.png" id="IMRUserStatus_' + CHTFrom + '" class="IMPPIcon" /></div>', dhxIMMembers.cellById(CHTFrom, 1).getValue(), , ]);
                                dhxIMRecentMembers.cellById(CHTFrom, 2).setValue('<img src="images/icon/IMIcon/nc.gif" />');
                            }
                        }

                        /*$('#noti_bubble').show();
                         var pendingChatCount = 0;
                         dhxIMRecentMembers.forEachRow(function(id){
                         if(dhxIMRecentMembers.cellById(id,2).getValue() != '') {
                         pendingChatCount++;
                         }
                         });
                         $('#noti_bubble').html(pendingChatCount);
                         if(pendingChatCount == 0) { 
                         $('#noti_bubble').hide(); 
                         };*/
                        IMNotificationBubble();
                        /*if(!IMUserChats[CHTFrom]) {
                         IMUserChats[CHTFrom] = '<i class="IMChatTime">'+IMTimePopulate(CHDate)+'</i><div class="IMChatContent you" CHID="'+CHTID+'">'+CHTMsg+'</div>';
                         } else {
                         IMUserChats[CHTFrom] += '<i class="IMChatTime">'+IMTimePopulate(CHDate)+'</i><div class="IMChatContentBox you" CHID="'+CHTID+'">'+CHTMsg+'</div>';
                         }*/
                    });

                    socket.emit('UnReadChatUserListRequest', 'session' + myUSID);

                    joinLeftUserIM();
                });
                function joinLeftUserIM() {
                    // Trigger When A User Join / Left Chat
                    socket.on("joinleft", function (data, JLStatus) {
                        if (dhxMiddleBlockTabs.cells("IMTab")) {
                            if (dhxIMMembers.doesRowExist(data['CID'])) {
                                //console.log('Testing Here ... '+data['CID']+'---------'+data['CName']+'-------'+JLStatus);


                                var JLData = 'of';
                                var JLIcon = 'of_circle_14';
                                var JLStatusText = 'Away';

                                if (JLStatus == 1) {
                                    JLData = 'on';
                                    JLIcon = 'on_tick_14';
                                    JLStatusText = 'Online';
                                }

                                dhxIMMembers.cellById(data['CID'], 0).setValue('<img src="images/icon/IMIcon/' + JLData + '.png" />');
                                IMSearchGrid.cellById(data['CID'], 0).setValue('<img src="images/icon/IMIcon/' + JLData + '.png" />');
                                $('#IMRUserStatus_' + data['CID']).attr({'src': 'images/icon/IMIcon/' + JLIcon + '.png'});
                                $('#IMUserTabStatusIcon' + data['CID']).attr({'src': 'images/icon/IMIcon/' + JLData + '.png'});
                                $('#IMUserDetailsStatusIcon' + data['CID']).attr({'src': 'images/icon/IMIcon/' + JLIcon + '.png'});
                                $('#IMUserDetailsStatusMsg' + data['CID']).html(JLStatusText);
                                dhxIMMembers.sortRows(1, "str", "asc");
                                dhxIMMembers.sortRows(0, "str", "desc");

                                IMSearchGrid.sortRows(1, "str", "asc");
                                IMSearchGrid.sortRows(0, "str", "desc");
                            }
                        }
                    });
                }

                var popupDisplayStatus = 0;
                $("#IMSearch").keyup(function () {
                    if ($("#IMSearch").val() && (popupDisplayStatus == 0)) {
                        IMSearchPopUp.show(0, 0, 0, 0);
                        popupDisplayStatus = 1;
                        $('.chatMemberSearchClose').show();
                    }
                    if (!$("#IMSearch").val()) {
                        IMSearchPopUp.hide();
                        popupDisplayStatus = 0;
                        $('.chatMemberSearchClose').hide();
                    }
                    IMSearchGrid.filterBy(1, $("#IMSearch").val());
                });

                //To Update Status When User Opens IM Tab
                IMChatTab.attachEvent("onSelect", function (id, lastId) {
                    IMUserID = id;
                    if (dhxIMRecentMembers.doesRowExist(IMUserID)) {
                        socket.emit('IMRead', IMUserID, myUSID);
                        dhxIMRecentMembers.cellById(IMUserID, 2).setValue('');
                        dhxIMRecentMembers.selectRowById(IMUserID);
                        IMChatTab.tabs(IMUserID).setText(IMChatTab.tabs(IMUserID).getText().replace("nc.gif", "on.png"));
                        /*var pendingChatCount = 0;
                         dhxIMRecentMembers.forEachRow(function(id){
                         if(dhxIMRecentMembers.cellById(id,2).getValue() != '') {
                         pendingChatCount++;
                         }
                         });
                         $('#noti_bubble').html(pendingChatCount);
                         if(pendingChatCount === 0) {
                         $('#noti_bubble').hide();
                         }*/
                        IMNotificationBubble();
                    }
                    return true;
                });
                IMChatTab.attachEvent("onTabClose", function (id, lastId) {
                    IMUserID = id;
                    delete IMDatePrevious[IMUserID];
                    return true;
                });
                //delete myObject[prop];
                dhxIMRecentMembers.attachEvent("onRowSelect", function (id, ind) {
                    IMUserID = id;
                    IMGridMemberSelect(IMUserID);
                });
                dhxIMMembers.attachEvent("onRowSelect", function (id, ind) {
                    IMUserID = id;
                    IMGridMemberSelect(IMUserID);
                });
                IMSearchGrid.attachEvent("onRowSelect", function (id, ind) {
                    IMUserID = id;
                    IMGridMemberSelect(IMUserID);
                    IMSearchPopUp.hide();
                    popupDisplayStatus = 0;
                    $('.chatMemberSearchClose').hide();
                    $("#IMSearch").val('');
                });

                function IMGridMemberSelect(IMUserID) {
                    IMTyping[IMUserID] = false;
                    IMTimeout[IMUserID] = undefined;
                    IMDatDifference[IMUserID] = 0;
                    //IMItemPosition[IMUserID]  = 0;

                    if (!IMChatTab.cells(IMUserID)) {

                        var IMUserTabStatusIcon = $(dhxIMMembers.cellById(IMUserID, 0).getValue()).attr('src');
                        //var IMPP = $(dhxIMMembers.cellById(IMUserID, 0).getValue()).attr('IMPP');
                        //console.log(IMPP);


                        //console.log(dhxIMMembers.getUserData(IMUserID,"IMPP"));
                        var IMPP = dhxIMMembers.getUserData(IMUserID, "IMPP");
                        //console.log(IMPP);


                        var IMPPImage = 'images/icon/user_80.png';
                        if (IMPP != 'avatar.png') {
                            IMPPImage = 'uploads/profileImage/' + IMPP
                        }

                        var DLIcon = 'on_tick_14';
                        var IMUserDetailsStatusMsg = 'Online';
                        //console.log('--'+IMUserTabStatusIcon);
                        //console.log(IMUserTabStatusIcon.indexOf("on.png"));
                        if (IMUserTabStatusIcon.indexOf("on.png") < 0) {
                            DLIcon = 'of_circle_14';
                            IMUserDetailsStatusMsg = 'Away';
                        }

                        //console.log(IMUserTabIcon.attr('src'));

                        //console.log(JSON.stringify(IMUserTabIcon));
                        IMChatTab.addTab(IMUserID, '<img src="' + IMUserTabStatusIcon + '" id="IMUserTabStatusIcon' + IMUserID + '" />&nbsp;&nbsp;&nbsp;' + dhxIMMembers.cellById(IMUserID, 1).getValue(), null, null, null, true);
                        IMChatTab.tabs(IMUserID).setActive();

                        IMChatTab.tabs(IMUserID).attachHTMLString('<div id="IMChatHeader' + IMUserID + '" class="IMChatHeader"><div class="IMChatHeaderImg"><img src="' + IMPPImage + '" /></div><div class="IMChatHeaderDetails"><div id="IMCHDName' + IMUserID + '" class="IMCHDName">My Name</div><div class="IMCHDDetails"><img src="images/icon/IMIcon/' + DLIcon + '.png" id="IMUserDetailsStatusIcon' + IMUserID + '" /><b id="IMUserDetailsStatusMsg' + IMUserID + '">' + IMUserDetailsStatusMsg + '</b>  || <label id="IMUserDetailsInfo' + IMUserID + '"> - - </label></div></div></div><div id="IMChatContainer' + IMUserID + '" class="IMChatContainer"><label id="DayLabel' + IMUserID + '" class="DayLabel">Today</label></div><div id="IMChatStatus' + IMUserID + '" class="IMChatStatus"><label id="imTypingData' + IMUserID + '"><img src="images/icon/pencil.png" />' + dhxIMMembers.cellById(IMUserID, 1).getValue() + '</label><label id="imTyping' + IMUserID + '"> is typing </label><label id="imStoppedTyping' + IMUserID + '"> stopped typing </label></div><div class="chatTypeArea"><hr /><textarea id="TA' + IMUserID + '"></textarea><div id="CA' + IMUserID + '" style="margin:10px;"></div><img src="images/icon/attach-24.png" class="attachIcon" /></div>');

                        $('.attachIcon').click(function () {
                            $('#IMFileShareFormFile').trigger('click');
                        });
                        $('#IMCHDName' + IMUserID).html(dhxIMMembers.cellById(IMUserID, 1).getValue());

                        var originalText = $('#IMChatStatus' + IMUserID + ' label#imTyping' + IMUserID).text(), i = 0;
                        setInterval(function () {
                            $('#IMChatStatus' + IMUserID + ' label#imTyping' + IMUserID).append(".");
                            i++;
                            if (i == 6) {
                                $('#IMChatStatus' + IMUserID + ' label#imTyping' + IMUserID).html(originalText);
                                i = 0;
                            }
                        }, 500);


                        dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/IMUserDetailsInfo.php&USID=" + IMUserID.replace('session', '')), function (dataSearch) {
                            var IMInfoObj = JSON.parse(dataSearch.xmlDoc.responseText);
                            $('#IMUserDetailsInfo' + IMUserID).html(IMInfoObj);
                        });


                        preTally.IM.chatStatusDisplay(0, 0, 0, IMUserID);

                        //var tmpCount = 0;
                        $('#IMChatContainer' + IMUserID).on('scroll', function () {

                            var scrollTop = $('#IMChatContainer' + IMUserID).scrollTop();
                            var thisDayLabel = 0;
                            $('div#IMChatContainer' + IMUserID).children('.IMChatDateSeperatorBlockHidden').each(function () {

                                var selectedItemTopPosition = $('div#IMChatContainer' + IMUserID).scrollTop() + $(this).offset().top - 216;
                                if (selectedItemTopPosition < scrollTop) {
                                    thisDayLabel = $(this).attr('IMDate');
                                }
                            });

                            if (thisDayLabel == 0) {
                                $('#DayLabel' + IMUserID).html('');
                            } else {


                                $('#DayLabel' + IMUserID).html(DateToDisplayDate(thisDayLabel));


                                /*var date1 = new Date();
                                 var date2 = new Date(thisDayLabel); 
                                 var diffDays = Math.round((date1-date2)/(1000 * 3600 * 24));
                                 //console.log(diffDays);
                                 if(diffDays < 1) {
                                 $('#DayLabel'+IMUserID).html('Today');
                                 } else if(diffDays == 1) {
                                 $('#DayLabel'+IMUserID).html('Yesterday');
                                 } else {
                                 var IMThisDay = new Date(thisDayLabel);
                                 var IMDisplayDate = IMThisDay.toString('dddd,  MMMM d,  yyyy');
                                 $('#DayLabel'+IMUserID).html(IMDisplayDate);
                                 }*/
                            }

                            var IMVievDivScrollPosition = $('#IMChatContainer' + IMUserID).scrollTop();
                            if (IMVievDivScrollPosition == 0) {

                                var lastLoadedIMID = 0;
                                if ($('div#IMChatContainer' + IMUserID).children('div')) {
                                    if ($('div#IMChatContainer' + IMUserID).children('div').first().attr('CHID')) {
                                        lastLoadedIMID = $('div#IMChatContainer' + IMUserID).children('div').first().attr('CHID');
                                    }
                                }
                                console.log('session' + myUSID+' -- '+IMUserID+' -- '+lastLoadedIMID+' -- '+IMDatDifference[IMUserID]);
                                //if(tmpCount < 5) {
                                socket.emit('chatHistoryRequest', 'session' + myUSID, IMUserID, lastLoadedIMID, IMDatDifference[IMUserID]);
                                //tmpCount ++;
                                //}
                            }
                        });

                        $("#TA" + IMUserID).emojioneArea({
                            container: "#CA" + IMUserID,
                            hideSource: true,
                            useSprite: true,
                            placeholder: 'Type a message here',
                            events: {
                                focus: function (editor, event) {
                                    //console.log('event:focus');
                                },
                                keyup: function (editor, event) {
                                    //console.log('event:keyup');
                                    var key = event.which;
                                    if (IMTyping[IMUserID] == false) {
                                        IMTyping[IMUserID] = true
                                        //socket.emit(typingMessage);
                                        postTypingStatus(1);
                                        //console.log('Typing - '+IMUserID);
                                        IMTimeout[IMUserID] = setTimeout(IMTimeoutFunction, 5000, IMUserID);
                                    } else {
                                        clearTimeout(IMTimeout[IMUserID]);
                                        IMTimeout[IMUserID] = setTimeout(IMTimeoutFunction, 5000, IMUserID);
                                    }
                                    if ((event.keyCode == 10 || event.keyCode == 13) && (event.ctrlKey || event.metaKey)) {
                                        //console.log('2. Ctel + Enter Pressed');
                                        this.setText(this.getText() + '\n\t')
                                        return false;
                                    }
                                    if (key == 13) {
                                        if (!$.trim(this.getText())) {
                                            this.setText('');
                                            return false;
                                        }
                                        //console.log('CA'+IMUserID);
                                        if ($('#CA' + IMUserID).find('div.emojionearea-button').hasClass("active")) {
                                            $('#CA' + IMUserID).find('.emojionearea-button-close').trigger('click');
                                        }
                                        //if($('div#IMChatContainer'+IMUserID).children('.IMChatDateSeperator').last().attr('SeperatorDisplayAttribute') != 'Today') {   
                                        if (DateToDisplayDate($('div#IMChatContainer' + IMUserID).children('.IMChatDateSeperatorBlockHidden').last().attr('imdate')) != 'Today') {

                                            //var IMTodayDate = Date.today('today').toISOString();
                                            var IMTodayDate = (new Date(Date.now() - ((new Date()).getTimezoneOffset() * 60000))).toISOString();
                                            var IMLabelDate = new Date;

                                            //console.log('--------'+IMTodayDate);
                                            //console.log('--------'+Date.today());
                                            //console.log('--------'+DateToDisplayDate(IMTodayDate));
                                            //$('#IMChatContainer'+IMUserID).append('<span class="IMChatDateSeperatorBlock" IMDate="'+IMTodayDate+'"  /><hr class="IMChatDateSeperator" SeperatorDisplayAttribute="Today" />');
                                            $('#IMChatContainer' + IMUserID).append('<span class="IMChatDateSeperatorBlock" /><hr class="IMChatDateSeperator" SeperatorDisplayAttribute="Today" />');
                                            $('#IMChatContainer' + IMUserID).append('<span class="IMChatDateSeperatorBlockHidden" IMDate="' + IMTodayDate + '"  />');
                                        }
                                        postChat(this.getText());
                                        var thisClass = 'IMChatContent';
                                        if ($('#IMChatContainer' + IMUserID).children().last().hasClass('me')) {
                                            thisClass = 'IMChatContentBox';
                                        }
                                        $('#IMChatContainer' + IMUserID).append('<i class="IMChatTime">' + IMTimePopulate('0') + '</i><div class="' + thisClass + ' me">' + this.getText() + '</div>');
                                        $('#IMChatContainer' + IMUserID).scrollTop($('#IMChatContainer' + IMUserID)[0].scrollHeight);
                                        this.setText('');
                                        if (!dhxIMRecentMembers.doesRowExist(IMUserID)) {
                                            console.log('Am Hereeeeeeee ..........' + IMUserID);
                                            dhxIMRecentMembers.addRow(IMUserID, ['<div class="IMProfilePic"><img src="' + dhxIMMembersProfileImage(IMUserID) + '" class="IMPPImg" /><img src="images/icon/IMIcon/of_circle_14.png" id="IMRUserStatus_' + IMUserID + '" class="IMPPIcon" /></div>', dhxIMMembers.cellById(IMUserID, 1).getValue(), , ]);
                                        }
                                        var moveToRowID = 0;
                                        if (dhxIMRecentMembers.cellById(dhxIMRecentMembers.getRowId(0), 1).getValue().indexOf("Today") < 0) {
                                            dhxIMRecentMembers.addRow('dummyRow', '', 0, '');
                                            dhxIMRecentMembers.addRow('dummyRowToday', ['<div class="dummyRowToday">Today</div>', , , ]);
                                            dhxIMRecentMembers.moveRowTo('dummyRowToday', 'dummyRow', "move");
                                            dhxIMRecentMembers.setColspan('dummyRowToday', 0, 2);
                                            dhxIMRecentMembers.deleteRow('dummyRow');
                                            moveToRowID = 'dummyRowToday';
                                        } else {
                                            moveToRowID = dhxIMRecentMembers.getRowId(0);
                                        }
                                        dhxIMRecentMembers.moveRowTo(IMUserID, moveToRowID, "move");

                                        if (dhxIMMembers.cellById(IMUserID, 0).getValue().indexOf("on.png") > 0) {
                                            $('#IMRUserStatus_' + IMUserID).attr({'src': 'images/icon/IMIcon/on_tick_14.png'});
                                        }







                                        IMTabs.tabs("a2").setActive();
                                        return false;
                                    }
                                }
                            }
                        });
                        function postChat(msgText) {
                            clearTimeout(IMTimeout[IMUserID]);
                            IMTyping[IMUserID] = false;
                            socket.emit('sendchat', {
                                type    :'1',
                                msg     : $.trim(msgText),
                                from    : 'session' + myUSID,
                                to      : IMUserID
                            });
                        }
                        function postTypingStatus(typStatus) {
                            socket.emit('typingStatus', {
                                msg: typStatus,
                                from: 'session' + myUSID,
                                to: IMUserID
                            });
                        }
                        function IMTimeoutFunction(IMUserID) {
                            IMTyping[IMUserID] = false;
                            postTypingStatus(2);
                        }
                        /*if(IMUserChats[IMUserID]) {
                         //console.log(IMUserChats[IMUserID]);
                         //$('#IMChatContainer'+IMUserID).append(IMUserChats[IMUserID]);
                         
                         //$('#IMChatContainer'+IMUserID).scrollTop($('#IMChatContainer'+IMUserID)[0].scrollHeight);
                         delete IMUserChats[IMUserID];
                         }*/


                        //console.log($('div#IMChatContainer'+IMUserID+' :first-child'));

                        var lastLoadedIMID = 0;
                        if ($('div#IMChatContainer' + IMUserID).children('div')) {
                            if ($('div#IMChatContainer' + IMUserID).children('div').first().attr('CHID')) {
                                lastLoadedIMID = $('div#IMChatContainer' + IMUserID).children('div').first().attr('CHID');
                            }
                        }
                        //lastLoadedIMID = $('div#IMChatContainer'+IMUserID).children('.IMChatContent').first().attr('CHID');
                        //console.log(lastLoadedIMID);



                        socket.emit('chatHistoryRequest', 'session' + myUSID, IMUserID, lastLoadedIMID, IMDatDifference[IMUserID]);
                        //console.log('Load My Previous Chats');
                    } else {
                        IMChatTab.tabs(IMUserID).setActive();
                    }
                }

            } else {
                dhxMiddleBlockTabs.tabs("IMTab").show(true);
                //dhxMiddleBlockTabs.tabs("IMTab").setActive();

            }
            dhxMiddleBlockTabs.attachEvent("onSelect", function (id, lastId) {
                dhxToolbar.setItemImage('im', 'im.png');
                $('#chatTabIcon').attr({'src': 'images/icon/im.png'});
                return true;
            });
        },
        chatHistory: function (IMID, IMMsg, IMType, IMFrom, IMTo, IMDate, IMSameUser, ThisDatDifference) {
            //console.log('Chat History Called');
            //var thisClass = 'IMChatContentBox';
            var fromClass = 'me';
            var chatLoadID = IMTo;
            if ('session' + myUSID === IMTo) {
                chatLoadID = IMFrom;
                fromClass = 'you';

            }

            var old_height = $('#IMChatContainer' + chatLoadID).prop('scrollHeight');
            var old_scroll = $('#IMChatContainer' + chatLoadID).scrollTop(); //remember the scroll position
            if (IMSameUser == 0) {
                thisClass = 'IMChatContent';
            }
            if (!IMDatePrevious[chatLoadID]) {
                //var IMPresentDay = new Date(IMDate);
                //var IMPresentDate = IMPresentDay.toString('dddd,  MMMM d,  yyyy');
                //$('#IMChatContainer' + chatLoadID).prepend('<span class="IMChatDateSeperatorBlockHidden" IMDate="' + IMDate + '"  />');
                preTally.IM.IMMessageTypeDisplay(IMID, IMMsg, IMType, chatLoadID, IMTo, IMDate, 'SBH', false, 'prepend');
            }

            //console.log('ThisDatDifference : '+ThisDatDifference+' -- '+IMMsg);
            if (ThisDatDifference == 0) {
                preTally.IM.IMMessageTypeDisplay(IMID, IMMsg, IMType, chatLoadID, IMTo, IMDate, fromClass, false, 'prepend');
                //$('#IMChatContainer' + chatLoadID).prepend('<i class="IMChatTime">' + IMTimePopulate(IMDate) + '</i><div class="' + thisClass + ' ' + fromClass + '" CHID="' + IMID + '" CHDate="' + IMDate + '">' + IMMsg + '</div>');
                //$('#IMChatContainer'+chatLoadID).prepend('<i class="IMChatTime">'+IMTimePopulate(IMDate)+'</i><div class="'+thisClass+' '+fromClass+'" CHID="'+IMID+'" CHDate="'+IMDate+'">'+IMID+' -- '+IMMsg+' -- '+IMDate+'</div>');
            } else {
                if (IMDatePrevious[chatLoadID]) {


                    //var IMDisplayDate = IMDatePrevious[chatLoadID];
                    //var IMPresentDate = IMDate;
                    /*console.log('======== '+IMDatDifference[chatLoadID]);
                     if(IMDatDifference[chatLoadID] <= 2) {
                     IMDisplayDate = IMHistDays[IMDatDifference[chatLoadID]];
                     } else {
                     console.log(IMDatePrevious[chatLoadID]);
                     var IMThisDay = new Date(IMDatePrevious[chatLoadID]);
                     var IMDisplayDate = IMThisDay.toString('dddd,  MMMM d,  yyyy');
                     
                     var IMPresentDay = new Date(IMDate);
                     var IMPresentDate = IMPresentDay.toString('dddd,  MMMM d,  yyyy');
                     }*/
                    IMDatDifference[chatLoadID] = ThisDatDifference;
                    preTally.IM.IMMessageTypeDisplay(IMID, IMMsg, IMType, chatLoadID, IMTo, IMDate, 'SB', false, 'prepend');
                    preTally.IM.IMMessageTypeDisplay(IMID, IMMsg, IMType, chatLoadID, IMTo, IMDate, 'SBH', false, 'prepend');
                    //$('#IMChatContainer'+chatLoadID).prepend('<span class="IMChatDateSeperatorBlock" IMDate="'+IMDate+'"  /><hr class="IMChatDateSeperator" SeperatorDisplayAttribute="'+DateToDisplayDate(IMDatePrevious[chatLoadID])+'" />');
                    //$('#IMChatContainer' + chatLoadID).prepend('<span class="IMChatDateSeperatorBlock" /><hr class="IMChatDateSeperator" SeperatorDisplayAttribute="' + DateToDisplayDate(IMDatePrevious[chatLoadID]) + '" />');
                    //$('#IMChatContainer' + chatLoadID).prepend('<span class="IMChatDateSeperatorBlockHidden" IMDate="' + IMDate + '"  />');
                }
            }
            var new_height = $('#IMChatContainer' + chatLoadID).prop('scrollHeight');
            //console.log(old_height+' -- '+old_scroll+' -- '+new_height);
            $('#IMChatContainer' + chatLoadID).scrollTop(old_scroll + new_height - old_height); //restore "scroll position"
            IMDatePrevious[chatLoadID] = IMDate;

            if ($('#IMChatContainer' + chatLoadID).scrollTop() == 0) {
                $('#DayLabel' + chatLoadID).html(DateToDisplayDate($('div#IMChatContainer' + chatLoadID).children('.IMChatDateSeperatorBlockHidden').first().attr('IMDate')));
            }
        },
        updatechatIM: function (type, data, from, to) {
            //return;
            if (data) {
                if (dhxMiddleBlockTabs.cells("IMTab")) {
                    if (IMChatTab.cells(from)) {
                        preTally.IM.chatStatusDisplay(0, 0, 0, from);

                        if (IMChatTab.getActiveTab() != from) {
                            dhxIMRecentMembers.cellById(from, 2).setValue('<img src="images/icon/IMIcon/nc.gif" />');
                            IMChatTab.tabs(from).setText(IMChatTab.tabs(from).getText().replace("on.png", "nc.gif"));
                            /*$('#noti_bubble').show();
                             var pendingChatCount = 0;
                             dhxIMRecentMembers.forEachRow(function(id){
                             if(dhxIMRecentMembers.cellById(id,2).getValue() != '') {
                             pendingChatCount++;
                             }
                             });
                             $('#noti_bubble').html(pendingChatCount);
                             if(pendingChatCount === 0) {
                             $('#noti_bubble').hide();
                             }*/
                            IMNotificationBubble();
                        } else {
                            //console.log(from+'--'+to);
                            socket.emit('IMRead', from, to);
                        }

                        /*var thisClass = 'IMChatContent';
                        if ($('#IMChatContainer' + from).children().last().hasClass('you')) {
                            var thisClass = 'IMChatContentBox';
                        }
                        if(type == 1) {
                            $('#IMChatContainer' + from).append('<i class="IMChatTime">' + IMTimePopulate('0') + '</i><div class="' + thisClass + ' you">' + data + '</div>');
                        } else if(type == 2) {
                            $('#IMChatContainer' + from).append('<i class="IMChatTime">' + IMTimePopulate('0') + '</i><div class="' + thisClass + ' you FShare"><a href="IMFileShare/' + data + '" data-lightbox="image-1" ><img src="images/icon/IMIcon/view.png" class="FShareImgFromSenderViewIcon" /></a><img src="IMFileShare/thumbnail/' + data + '" class="FShareImgFromSender" /></div>');
                        }
                        
                        $('#IMChatContainer' + from).scrollTop($('#IMChatContainer' + from)[0].scrollHeight);*/
                        
                        preTally.IM.IMMessageTypeDisplay('0', data, type, from, to, '0', 'you', true, 'append');
                        
                        dhxIMRecentMembers.moveRowTo(from, dhxIMRecentMembers.getRowId(0), "move");
                        console.log('Chat Tab Opened');
                    } else {
                        console.log('Chat Tab Not Opened - Testing...');

                        if (dhxIMRecentMembers.doesRowExist(from)) {
                            dhxIMRecentMembers.cellById(from, 2).setValue('<img src="images/icon/IMIcon/nc.gif" />');
                        } else {
                            dhxIMRecentMembers.addRow(from, ['<div class="IMProfilePic"><img src="' + dhxIMMembersProfileImage(from) + '" class="IMPPImg" /><img src="images/icon/IMIcon/on_tick_14.png" id="IMRUserStatus_' + from + '" class="IMPPIcon" /></div>', dhxIMMembers.cellById(from, 1).getValue(), , ]);
                            dhxIMRecentMembers.cellById(from, 2).setValue('<img src="images/icon/IMIcon/nc.gif" />');
                        }

                        /*$('#noti_bubble').show();
                         var pendingChatCount = 0;
                         dhxIMRecentMembers.forEachRow(function(id){
                         if(dhxIMRecentMembers.cellById(id,2).getValue() != '') {
                         pendingChatCount++;
                         }
                         });
                         $('#noti_bubble').html(pendingChatCount);
                         if(pendingChatCount === 0) {
                         $('#noti_bubble').hide();
                         }*/
                        IMNotificationBubble();

                        /*if(!IMUserChats[from]) {
                         IMUserChats[from] = '<i class="IMChatTime">'+IMTimePopulate('0')+'</i><div class="IMChatContent you">'+data+'</div>';
                         } else {
                         IMUserChats[from] += '<i class="IMChatTime">'+IMTimePopulate('0')+'</i><div class="IMChatContentBox you">'+data+'</div>';
                         }*/
                        dhxIMRecentMembers.moveRowTo(from, dhxIMRecentMembers.getRowId(0), "move");
                    }
                    if (dhxMiddleBlockTabs.cells("IMTab") && (dhxMiddleBlockTabs.getActiveTab() != 'IMTab')) {
                        $('#chatTabIcon').attr({'src': 'images/icon/blink.gif'});
                        dhxToolbar.setItemImage('im', 'blink.gif');
                    }


                } else {
                    console.log('IM Main Chat Tab Not Opened');
                    dhxToolbar.setItemImage('im', 'blink.gif');
                }
            }
        },
        typingStatusIM: function (data, from, to) {
            if (data) {
                if (dhxMiddleBlockTabs.cells("IMTab") && (dhxMiddleBlockTabs.getActiveTab() == 'IMTab')) {

                    if (IMChatTab.cells(from)) {
                        if (data == 1) {
                            preTally.IM.chatStatusDisplay(1, 1, 0, from);
                        } else if (data == 2) {
                            preTally.IM.chatStatusDisplay(1, 0, 1, from);

                            setTimeout(function () {
                                preTally.IM.chatStatusDisplay(0, 0, 0, from);
                            }, 3000);
                        }
                    }
                }
            }
        },
        unReadChats: function () {
            dhxToolbar.setItemImage('im', 'blink.gif');
        },
        chatStatusDisplay: function (imD, imT, imS, imUser) {
            if (imD == 1)
                $('#IMChatStatus' + imUser + ' label#imTypingData' + imUser).show();
            else
                $('#IMChatStatus' + imUser + ' label#imTypingData' + imUser).hide();
            if (imT == 1)
                $('#IMChatStatus' + imUser + ' label#imTyping' + imUser).show();
            else
                $('#IMChatStatus' + imUser + ' label#imTyping' + imUser).hide();
            if (imS == 1)
                $('#IMChatStatus' + imUser + ' label#imStoppedTyping' + imUser).show();
            else
                $('#IMChatStatus' + imUser + ' label#imStoppedTyping' + imUser).hide();
        },
        InitializeConnection: function () {
            //socket.emit('mySessionID', 'session<?php echo $preTally_user_id; ?>', '<?php echo $preTally_user_name; ?>');
            console.log('session' + myUSID + ' - - ' + myNAME);
            socket.emit('mySessionID', 'session' + myUSID, myNAME);

            socket.on('updatechat', function (type, data, from, to) {
                preTally.IM.updatechatIM(type, data, from, to);
            });
            socket.on('updateTypingStatus', function (data, from, to) {
                preTally.IM.typingStatusIM(data, from, to);
            });
            socket.on('unReadChats', function (data) {
                preTally.IM.unReadChats(data);
            });
            socket.on("chatHistoryLoad", function (IMID, IMMsg, IMType, IMFrom, IMTo, IMDate, IMSameUser, ThisDatDifference) {
                //console.log(ThisDatDifference+' : ThisDatDifference');
                preTally.IM.chatHistory(IMID, IMMsg, IMType, IMFrom, IMTo, IMDate, IMSameUser, ThisDatDifference);
            });
        },
        ReConnect: function () {
            //console.log('Re Connected');
            socket = io.connect('http://128.199.181.200:3000', {'forceNew': true});
            preTally.IM.InitializeConnection();
        },
        DisConnect: function () {
            //console.log('Dis Connected');
            socket.emit('forceDisconnect');
        },
        IMFileShare: function () {
            console.log('File Uploaded');
            console.log(IMUserID);
            //IMChatStatussession2395
            //console.log($('#IMChatStatus' + IMUserID).offset().top + ' - - ' + $('#IMChatStatus' + IMUserID).offset().left);
            //$('#IMProgress').css({'top': $('#IMChatStatus' + IMUserID).offset().top, 'left': ($('#IMChatStatus' + IMUserID).offset().left + $('#IMChatStatus' + IMUserID).width() - 376)});

            //var d = new Date();
            var randomTimeID = new Date().getTime();
            var thisClass = 'IMChatContent';
            if ($('#IMChatContainer' + IMUserID).children().last().hasClass('me')) {
                thisClass = 'IMChatContentBox';
            }
            
            /*
                <div style="display:none;">\
                    <form id="IMFileShareForm' + randomTimeID + '" action="IMFileShare.php" method="post" enctype="multipart/form-data">\
                        <input type="file" size="60" name="IMFileShareFormFile' + randomTimeID + '" id="IMFileShareFormFile' + randomTimeID + '" onChange="preTally.IM.IMFileShare(' + randomTimeID + ');">\
                    </form>\
                </div>\
            */
           
            $('#IMChatContainer' + IMUserID).append('<i class="IMChatTime">' + IMTimePopulate('0') + '</i><div class="' + thisClass + ' me FShare" id="FShare' + randomTimeID + '"></div>');
            $('#IMChatContainer' + IMUserID).scrollTop($('#IMChatContainer' + IMUserID)[0].scrollHeight);
            $('#IMFileShareForm').ajaxForm(preTally.IM.IMFileShareForm(randomTimeID));
            
            

            

            var IMFileObj = $('#IMFileShareFormFile');
            //var countFiles = IMFileObj[0].files.length;
            var imgPath = IMFileObj[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#FShare" + randomTimeID);
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof (FileReader) != "undefined") {
                    //loop for each file selected for uploaded.
                    //for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#FShareImgDummy' + randomTimeID).remove();
                            $("<img />", {
                                "src": e.target.result,
                                "id": 'FShareImg' + randomTimeID
                            }).appendTo(image_holder);
                            //$('#FShareImg' + randomTimeID).attr({'src':e.target.result});
                            console.log(e.target.result);
                            
                        }
                        image_holder.show();
                        //reader.readAsDataURL(IMFileObj[0].files[i]);
                        reader.readAsDataURL(IMFileObj[0].files[0]);
                    //}
                    //console.log('Hereee - - '+$('#FShare' + randomTimeID).offset().left+' - - '+$('#FShare' + randomTimeID).offset().top);
                    //$('#IMProgress').show();
                    //$('#IMProgress').css({'display': 'block'});
                    //$('#IMProgress').css("cssText", "display:block !important; left:"+($('#FShare' + randomTimeID).offset().left+40)+"px;  top:"+($('#FShare' + randomTimeID).offset().top+20)+"px");
                    
                    
                }else {
                    //$("<img />", { "src": 'images/file200.png', "id": 'FShareImg' + randomTimeID }).appendTo(image_holder);
                    $('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
                }
            } else if(extn == "doc" || extn == "docx" || extn == "docm") {
                $('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
            } else if(extn == "xlsx" || extn == "xls" || extn == "csv") {
                $('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
            } else if(extn == "ppt" || extn == "pptm" || extn == "pptx" || extn == "ppsx") {
                $('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
            } else {
                $('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
            }
            $('#FShare' + randomTimeID).append('<img src="images/loader200.gif" id="FShareImgDummy' + randomTimeID + '" />');
            $('#FShare' + randomTimeID).append('<div class="IMProgress" id="IMProgress' + randomTimeID + '">\
                    <canvas id="inactiveProgress' + randomTimeID + '" class="progress-inactive" height="100px" width="100px"></canvas>\
                    <canvas id="activeProgress' + randomTimeID + '" class="progress-active"  height="100px" width="100px"></canvas>\
                    <p>0%</p>\
                </div>');
            
            $('#FShareImg' + randomTimeID).css({ opacity: 0.2 });
            //console.log($('#FShareImg' + randomTimeID).height());
            //console.log(($('#FShareImg' + randomTimeID).height() / 2));
            //console.log(($('#FShareImg' + randomTimeID).height() / 2)-50);
            $('#IMProgress' + randomTimeID).css("cssText", "top:"+(($('#FShareImgDummy' + randomTimeID).height() / 2)-60)+"px");
            
            $pCaption[randomTimeID]       = $('#IMProgress' + randomTimeID + ' p');
            iProgress[randomTimeID]       = document.getElementById('inactiveProgress' + randomTimeID);
            aProgress[randomTimeID]       = document.getElementById('activeProgress' + randomTimeID);
            iProgressCTX[randomTimeID]    = iProgress[randomTimeID].getContext('2d');
            
            preTally.IM.drawInactive(iProgressCTX[randomTimeID]);

            $('#IMFileShareForm').submit();
        },
        IMFileShareSuccess: function (response, randomTimeID) {
            console.log(response.responseText);
            var thisClass = 'IMChatContent';
            if ($('#IMChatContainer' + IMUserID).children().last().hasClass('me')) {
                thisClass = 'IMChatContentBox';
            }
            socket.emit('sendchat', {
                type    :'2',
                msg     : response.responseText,
                from    : 'session' + myUSID,
                to      : IMUserID
            });
            //$('#FShareImg' + randomTimeID).css({ opacity: 1 });
            //$('#IMProgress' + randomTimeID).remove();
            //$('#IMChatContainer'+IMUserID).append('<i class="IMChatTime">'+IMTimePopulate('0')+'</i><div class="'+thisClass+' me FShare"><img src="images/file48.png" width="80" /></div>');
            //$('#IMChatContainer'+IMUserID).scrollTop($('#IMChatContainer'+IMUserID)[0].scrollHeight);
            //$('#IMProgress').css({'display': 'none'});
        },
        IMFileShareFailure: function () {
            console.log('Failure');
        },
        IMFileShareForm : function (randomTimeID) {
            var IMFileShare = {
                beforeSend: function () {
                    //$("#IMProgress").show();
                    var percentage = 0 / 100;
                    console.log(percentage + '%');
                    preTally.IM.drawProgress(aProgress[randomTimeID], percentage, $pCaption[randomTimeID]);
                    //clear everything
                    //$("#IMProgressBar").width('0%');
                    //$("#message").html("");
                    //$("#IMProgressPercent").html("0%");
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    //$("#IMProgressBar").width(percentComplete + '%');
                    //$("#IMProgressPercent").html(percentComplete + '%');
                    var percentage = percentComplete / 100;
                    console.log(percentage + '%');
                    preTally.IM.drawProgress(aProgress[randomTimeID], percentage, $pCaption[randomTimeID]);
                    
                    if(percentComplete >= 100) {
                        $('#FShareImg' + randomTimeID).css({ opacity: 1 });
                        $('#IMProgress' + randomTimeID).remove();
                    }
                },
                success: function () {
                    //$("#IMProgressBar").width('100%');
                    //$("#IMProgressPercent").html('100%');
                    var percentage = 100 / 100;
                    console.log(percentage + '%');
                    preTally.IM.drawProgress(aProgress[randomTimeID], percentage, $pCaption[randomTimeID]);
                },
                complete: function (response) {
                    //$("#message").html("<font color='green'>"+response.responseText+"</font>");
                    preTally.IM.IMFileShareSuccess(response, randomTimeID);
                },
                error: function () {
                    //$("#message").html("<font color='red'> ERROR: unable to upload files</font>");
                    preTally.IM.IMFileShareFailure(randomTimeID);
                }
            }; 
            
            return IMFileShare;
        },
        IMMessageTypeDisplay : function (IMID, IMMsg, IMType, IMMsgFrom, IMMsgTo, IMTime, IMForClass, IMScroll, IMAppPrep) {
            
            if(IMForClass == 'SB') {
                $('#IMChatContainer' + IMMsgFrom).prepend('<span class="IMChatDateSeperatorBlock" /><hr class="IMChatDateSeperator" SeperatorDisplayAttribute="' + DateToDisplayDate(IMDatePrevious[IMMsgFrom]) + '" />');
                return;
            }
            if(IMForClass == 'SBH') {
                $('#IMChatContainer' + IMMsgFrom).prepend('<span class="IMChatDateSeperatorBlockHidden" IMDate="' + IMTime + '"  />');
                return;
            }
            
            //console.log(IMMsg, IMType, IMMsgFrom, IMMsgTo, IMForClass);
            var CHID            = '';
            var fShare          = '';
            var IMMsgDisplay    = '';
            var thisClass       = 'IMChatContent';
            
            if(IMID != 0) {
                 CHID = ' CHID="' + IMID + '" ';
            }
            
            if ($('#IMChatContainer' + IMMsgFrom).children().last().hasClass(IMForClass)) {
                thisClass = 'IMChatContentBox';
            }
            
            if(IMType == 1) {
                IMMsgDisplay = IMMsg;
            } else {
                fShare = 'FShare';
                var extn = IMMsg.split('.').pop().toLowerCase();
                //extn = extn.toLowerCase();
                if(extn == "jpg" || extn == "jpeg" || extn == "gif" || extn == "png" || extn == "bmp") {
                    IMMsgDisplay = '<a href="IMFileShare/' + IMMsg + '" data-lightbox="image-1" ><img src="images/icon/IMIcon/view.png" class="FShareImgFromSenderViewIcon" /></a><img src="IMFileShare/thumbnail/' + IMMsg + '" class="FShareImgFromSender" />';
                    //$('#FShareImg' + randomTimeID).attr({'src':'images/file200.png'});
                } else if(extn == "doc" || extn == "docx" || extn == "docm") {
                    IMMsgDisplay = '';
                } else if(extn == "xlsx" || extn == "xls" || extn == "csv") {
                    IMMsgDisplay = '';
                } else if(extn == "ppt" || extn == "pptm" || extn == "pptx" || extn == "ppsx") {
                    IMMsgDisplay = '';
                } else {
                    IMMsgDisplay = '';
                }  
            }
            //console.log($('#IMChatContainer' + IMMsgFrom)[0]);
            if(IMAppPrep == 'append') {
                $('#IMChatContainer' + IMMsgFrom).append('<i class="IMChatTime">' + IMTimePopulate(IMTime) + '</i><div class="'+ thisClass +' '+ IMForClass +' '+ fShare +'" CHDate="' + IMTime + '" '+CHID+'>'+IMMsgDisplay+'</div>');
            } else if(IMAppPrep == 'prepend') {
                $('#IMChatContainer' + IMMsgFrom).prepend('<i class="IMChatTime">' + IMTimePopulate(IMTime) + '</i><div class="'+ thisClass +' '+ IMForClass +' '+ fShare +'" CHDate="' + IMTime + '" '+CHID+'>'+IMMsgDisplay+'</div>');
            }
            //$('#IMChatContainer' + chatLoadID).prepend('<i class="IMChatTime">' + IMTimePopulate(IMDate) + '</i><div class="' + thisClass + ' ' + fromClass + '" CHID="' + IMID + '" CHDate="' + IMDate + '">' + IMMsg + '</div>');
            if(IMScroll == true) {
                $('#IMChatContainer' + IMMsgFrom).scrollTop($('#IMChatContainer' + IMMsgFrom)[0].scrollHeight);
            }
        },
        drawInactive : function (iProgressCTX){
            iProgressCTX.lineCap = 'square';

            //progress bar
            iProgressCTX.beginPath();
            iProgressCTX.lineWidth = 10;
            //iProgressCTX.fillStyle = '#e6e6e6';
            iProgressCTX.strokeStyle = '#e6e6e6';
            iProgressCTX.arc(50,50,44,0,2*Math.PI);
            iProgressCTX.stroke();

            //progressbar caption
            iProgressCTX.beginPath();
            iProgressCTX.lineWidth = 0;
            iProgressCTX.fillStyle = 'rgba(255,255,255,.5)';
            iProgressCTX.arc(50,50,36,0,2*Math.PI);
            iProgressCTX.fill();

        },
        drawProgress : function (bar, percentage, $pCaption){
            var barCTX = bar.getContext("2d");
            var quarterTurn = Math.PI / 2;
            var endingAngle = ((2*percentage) * Math.PI) - quarterTurn;
            var startingAngle = 0 - quarterTurn;

            bar.width = bar.width;
            barCTX.lineCap = 'square';

            barCTX.beginPath();
            barCTX.lineWidth = 10;
            barCTX.strokeStyle = '#008000';
            barCTX.arc(50,50,44,startingAngle, endingAngle);
            barCTX.stroke();

            $pCaption.text( (parseInt(percentage * 100, 10)) + '%');
        }		

    };
})(jQuery, this);
