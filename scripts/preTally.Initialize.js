;(function ($, window, undefined) {
   
    preTally.Initialize = {
        initACL : function() {

        },
        initMainLayout : function() {
            //----------------- Home Window Layout --------------------//			
                dhxLayout = new dhtmlXLayoutObject(preTally.Data.mainLayout());			
                dhxLayout.cells("a").setCollapsedText("PreTally Menu");
                dhxLayout.cells("b").setCollapsedText("Main Block");
                dhxMiddleBlockTabs = dhxLayout.cells("a").attachTabbar();
                dhxMiddleBlockTabs.enableTabCloseButton(true);			
                dhxMiddleBlockTabs.attachEvent("onTabClose", function(id){
                    if(id=== 'view_editEntry'){
                        dhxLayout.cells("b").expand();                            
                    }
                    if(id == "viewMasterReports"){  
                        itemRptFlag   = 0;  
                        branchRptFlag = 0;
                        visualRptFlag = 0;
                        branchItemRptFlag = 0;
                        ItemBasedRptFlag = 0;
                        rptBrnchItemId = 'null';
                    }
                    if(id == "TracksTab") {
                        trackConsoleSideBar.setSideWidth(0);
                    }
                    if(id == "IMTab") {
                        //alert(1);
                        dhxMiddleBlockTabs.goToPrevTab();
                        //dhxMiddleBlockTabs.moveTab("IMTab", 1);
                        dhxMiddleBlockTabs.tabs("IMTab").hide(false);
                        return false;
                    }
                    return true;
                });
                dhxMiddleBlockTabs.attachEvent("onSelect", function(id, last_id){
                    requestedTab = id;                    
                    if(last_id === 'list_user' || last_id === 'menuNotfItem' || last_id === "viewMasterReports" || last_id === 'view_stockreports' || last_id === 'menuNewSalStruct' || last_id === 'menulistAttendance' || last_id === 'manageChequeLeaves' || last_id === 'menuListEditCustomItem' || last_id === 'view_editEntry' || last_id === 'menuHolidays' || last_id === 'TracksTab' || last_id === 'misReport' || last_id === 'view_pettycashreports' || last_id === 'viewMyBankBook' || last_id === 'view_monthlybusinessreports' || last_id === 'income_monthlybusinessreports' || last_id === 'oldJobsTab' || last_id === 'view_trackReports' ||  last_id === 'manage_businessrprt' || last_id==='ApproveLeave' || last_id==='menulistSalAdvance' || last_id==='view_jobCountReports' || last_id==='view_certificateCountReports' || last_id==='view_enquiryCountReports' || last_id==="view_trackdupreports"  || last_id=="viewBranchMasterReports" || last_id=='menuNewBalSheet' || last_id=='menuSalaryReport' || last_id=='viewUserIEReports' || last_id=='listOfzItem' || last_id=='listAllDescEntry' || last_id== 'manageZones' || last_id=='menuManageJobEntry' || id=='view_businessreports' || id=='view_cashbsreports') {
                        dhxLayout.cells("b").expand();
                    }
                    if(id === 'list_user' || id === 'menuNotfItem' || id === "viewMasterReports" || id === 'view_stockreports' || id === 'view_ExpenseControl' || id === 'menuNewSalStruct' || id === 'menulistAttendance' || id === 'manageChequeLeaves' || id === 'menuListEditCustomItem' || id === 'view_editEntry' || id === 'menuHolidays' || id === 'TracksTab' || id === 'misReport' || id === 'misGrid'  || id === 'view_pettycashreports' || last_id === 'misGrid' ||  id === 'viewMyBankBook' || id === 'view_monthlybusinessreports' || id === 'income_monthlybusinessreports' || id === 'oldJobsTab' || id === 'view_trackReports' || id === 'manage_businessrprt' || id==='ApproveLeave'  || id==='menulistSalAdvance' || id==='view_jobCountReports' || id==='view_certificateCountReports' || id==='view_enquiryCountReports' || id==='menuNewBalSheetSample' || id==="view_trackdupreports" || id=="viewBranchMasterReports" || id === 'PurchaseApproveGrid' || id === 'PurchaseRequestGrid' || id==='PurchaseTeamGrid'  || id=='menuNewBalSheet' || id=='menuSalaryReport' || id=='viewUserIEReports' || id=='listOfzItem' || id=='listAllDescEntry' || id== 'manageZones' || id=='menuMyAttendance' || id=='view_businessreports'  || id=='view_cashbsreports' || id=='menuManageJobEntry' || id=='acc_CashBSReports' || id=='acc_BankBSReports' || id=='ManageRule' || id=='Adj_Attendance' || id=='menuBreakTimes' || id=='listBreakTimes'  || id=='ApproveLateEntry' || id=='acc_Groups' || id=='acc_Ledger' || id=='acc_MapItems'|| id=='acc_Journal' || id=='acc_JournalEntry' || id=='acc_JournalData' || id=='acc_TdsRules' || id=='acc_Vendors' || id=='acc_Bills' || id=='acc_RecurringBill' || id=='acc_PandL' || id=='acc_CPandL' || id=='acc_BalanceSheet' || id=='acc_CashFlow' || id=='acc_LedgerReport' || id=='acc_BnkCashRpt' || id=='acc_OpenBalance' || id=='acc_VendorReport' || id =='acc_NotTransfered' || id =='acc_ExtraData' || id =='list_all_tracks' ) {
                        dhxLayout.cells("b").collapse();
                    }                    
                    if(id == 'viewReports' || id == 'view_cashbsreports' || id == 'view_businessreports' || id == 'view_branchbsreports') {
                        dhxAccord.cells("a4").show();
                        dhxAccord.cells("a4").open();                                 
                    } else {
                        dhxAccord.cells("a4").hide();
                        dhxAccord.cells("a1").open();  
                    }
                    if(id != 'viewReports') {
                        if(dhxWins.window("chartOtherDetails")) dhxWins.window("chartOtherDetails").close(); 
                    }
                    if(id != 'menuNotfItem') {
                        if(dhxWins.window("mapItemNotfWindow")) dhxWins.window("mapItemNotfWindow").close(); 
                    }
                    if(id != 'menuNotfDesc') {
                        if(dhxWins.window("mapDescNotfWindow")) dhxWins.window("mapDescNotfWindow").close(); 
                    }
                    if (IE_DetailDataPop && IE_DetailDataPop.isVisible()) IE_DetailDataPop.hide();
                    return true;
                });
                dhxMiddleBlockTabs.attachEvent("onTabClick", function(id, last_id){
                    var myPop = new dhtmlXPopup();
                    if($('.popupTransaction').data('clicked')){
                        $(".popupTransaction").click(function(){
                                 x = getAbsoluteLeft(this);
                                 y = getAbsoluteTop(this); 
                                 w = this.offsetWidth;
                                 h = this.offsetHeight;
                                 //var myPop = new dhtmlXPopup();
                                 myPop.attachHTML("Transaction Summary Report");
                                    myPop.show(x ,y,w,h);//params are: x, y, width, height
                            });
                            $(".popupTransaction").mouseout(function(){
                                $(this).data('clicked', false);
                            });
                    }
                    else if($('.popupBussiness').data('clicked')){
                        $(".popupTransaction").click(function(){
                                 x = getAbsoluteLeft(this);
                                 y = getAbsoluteTop(this); 
                                 w = this.offsetWidth;
                                 h = this.offsetHeight;
                                myPop.attachHTML("Bussiness Summary Report");
                                myPop.show(x ,y,w,h);
                         });
                         $(".popupBussiness").mouseout(function(){
                              $(this).data('clicked', false);
                         });
                    }
                    else if($('.popupStock ').data('clicked')){
                        $(".popupStock").click(function(){
                                 x = getAbsoluteLeft(this);
                                 y = getAbsoluteTop(this); 
                                 w = this.offsetWidth;
                                 h = this.offsetHeight;
                                myPop.attachHTML("Stock Summary Report");
                                myPop.show(x ,y,w,h);
                         });
                         $(".popupStock").mouseout(function(){
                                $(this).data('clicked', false);
                         });
                    }
                    else if($('.popupBank').data('clicked')){
                        $(".popupBank").click(function(){
                                 x = getAbsoluteLeft(this);
                                 y = getAbsoluteTop(this); 
                                 w = this.offsetWidth;
                                 h = this.offsetHeight;
                                myPop.attachHTML("Bank Balance Sheet");
                                myPop.show(x ,y,w,h);
                         });
                         $(".popupBank").mouseout(function(){
                                $(this).data('clicked', false);
                         });
                    }
                    else if($('.popupMaster').data('clicked')){
                        $(".popupMaster").click(function(){
                                 x = getAbsoluteLeft(this);
                                 y = getAbsoluteTop(this); 
                                 w = this.offsetWidth;
                                 h = this.offsetHeight;
                                myPop.attachHTML("Master Report");
                                myPop.show(x ,y,w,h);
                         });
                        myPop.show(420,30,20,20); //params are: x, y, width, height
                            $(".popupMaster").mouseout(function(){
                                $(this).data('clicked', false);
                            });
                    }
                    else{
                        preTally.Reload.reInitialize(id, last_id);
                    }
                });
           $(document).on('click', 'input[type="text"],.dhxform_textarea', function () { 
                $(this).select();
            });
        },
        initMainToolbar : function() {
            dhxToolbar = dhxLayout.attachMenu();
            //dhxToolbar.setOverflowHeight(20);
            dhxToolbar.setIconsPath("images/icon/");
            dhxToolbar.loadStruct(preTally.Initialize.encryptURL("requisites/menu.php&r=" + new Date().getTime()),function(){          

                //$('.dhxlayout_menu').children().children().last().css('margin-left','100px');
                //dhxToolbarCalendarPop = new dhtmlXPopup();                      
                dhxToolbar.attachEvent("onClick", doOnMenuCall);
                //preTally.Initialize.initNotification();
            });

        },
        initMenuAccord : function(){
            //----------------- Attach Accordian in Left Block --------------------//
            dhxAccord = dhxLayout.cells("b").attachAccordion();
            dhxAccord.addItem("a1", "<img src='images/icon/user_icon.png' />&nbsp;&nbsp;&nbsp;Account Visualization"); 	                    
            var xhr = new XMLHttpRequest();
            xhr.open("POST", preTally.Initialize.encryptURL("mapACL.php&r=rbAcdn"), false);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send();
            eval(xhr.responseText);                  
            dhxAccord.cells("a1").open();
            dhxAccord._enableOpenEffect = true;
            dhxAccord.attachEvent("onActive", function(itemId, state) {
                return true; // item will be opened
            });
            dhxAccord.attachEvent("onBeforeActive", function(itemId){

                if((itemId == 'a2') && (loadAccountHead == false)) {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    dhxAccHeadTree.loadXML(preTally.Initialize.encryptURL("requisites/accHead.php"), function(){
                        dhxAccHeadTree.setDragHandler(preTally.Settings.dragACL);
                        loadAccountHead = true;
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });  
                }
                
                if((itemId == 'a3') && (loadOfficeTree == false)) {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    dhxOffzTree.loadXML(preTally.Initialize.encryptURL("requisites/offzTree.php&f=HR&r=OF_H"), function(){
                        dhxOffzTree.setDragHandler(preTally.Settings.dragACL);
                        loadOfficeTree = true;
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
                    var OFType_Options = [
                        ['OF_H', 'obj', 'Hierarchy Map', 'hierarchy.gif'],
                    ];  
                }
                
                return true; // item will be opened
            });
            dhxAccord.addItem("a4");
            //dhxAccord.cells("a4").hide();
        },
        initMainMenu : function() {
            dhxVislnLayout = dhxAccord.cells("a1").attachLayout("2E");
            dhxVislnLayout.cells("a").setText("<img src='images/icon/graph_icon.png' />&nbsp;&nbsp;&nbsp;My Expense");
            dhxVislnLayout.cells("b").setText("<img src='images/icon/graph_icon.png' />&nbsp;&nbsp;&nbsp;My Income");
            dhxVislnExpChart = dhxVislnLayout.cells("a").attachChart({
                view: "bar",
                container: "chartDiv",
                value: "#sales#",
                label: "#sales#",                
                color: "#66ccff",
                gradient: "rising",
                radius: 0,
                width: 30,
                tooltip: {
                    template: "#sales#"
                },
                xAxis: {
                    title: "",
                    template: "'#month#",
                    lines: false
                },
                padding: {
                    left: 10,
                    right: 10,
                    top: 50
                }
            });

            dhxVislnIncChart = dhxVislnLayout.cells("b").attachChart({
                view: "bar",
                container: "chart1",
                value: "#sales#",
                color: "#C2E184",
                //color: "#66ccff",
                label: "#sales#",
                gradient: "rising",
//                        pieInnerText: "#sales#",
//                        shadow: 0,
                radius: 0,
                width : 30,
//                        x: 125,
//                        y: 0,
                tooltip: {
                    template: "#sales#"
                },
                xAxis: {
                    title: "",
                    template: "'#month#",
                    lines: false
                },
                padding: {
                    left: 10,
                    right: 10,
                    top: 50
                }
            });

            //dhxVislnIncChart.parse(month_dataset, "json");
            setTimeout(function() {
                //dhxVislnExpChart.load(preTally.Initialize.encryptURL("requisites/myIEGraph.php&ie=e"));
                //dhxVislnIncChart.load(preTally.Initialize.encryptURL("requisites/myIEGraph.php&ie=i"));
                
            }, 5100); 
            
        },
        initCombo : function() {
                window.dhx_globalImgPath = "assets/combo/codebase/imgs/";
        },
        initPopUp : function() {
            preTallyNotfPop = new dhtmlXPopup();
            preTallyNotfPop.attachEvent("onClick", function(id) {
                preTally.Notification.viewNotification(id);
            });
        },
        initWindow : function() {

                //----------------- Create Windows --------------------//
                dhxWins = new dhtmlXWindows();
                //dhxWins.enableAutoViewport(false);
                dhxWins.attachViewportTo(document.body);
                //dhxWins.setImagePath("assets/window/codebase/imgs/");
                var idPrefix = 1;

        },
        initStatusBar : function() {
            //----------------- Attach Status bar (Bottom of Document) --------------------//
            dhxStatusBar = dhxLayout.attachStatusBar();                    
            dhxStatusBar.setText('<div style="float:left;color:#0C52A7;"><font color="#F67728">Company Name : </font>'+ off_name +'&nbsp;&nbsp;&nbsp;&nbsp;<font color="#F67728">Branch : </font>'+ loc_name +'&nbsp;&nbsp;&nbsp;<font color="#F67728">Time : </font> <span id="clock">&nbsp;</span></div>&nbsp;Powered By <b><font color="#F67728">UroGulf</font><font color="#0C52A7"> Group</font> Of Companies</b>&nbsp;&nbsp;');
        },
        initHomeTab : function() {
            var bsp = unescape(JGG1P3bDnUSDL2Mui7KzYjj28UjPdWxCCtGkJSHeuo);    
            if(bsp == 5) {
                preTally.Settings.adminDashboard(); 
//            } else if((unescape(JGG1P3bDnUSDL12Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 'true')) {
//                preTally.Reports.offzAdminDashboard();
            } else {     

                var attendenceStatus = unescape(JGG1P3bDnUSDL4Mui7KzYjj28UjPdWxCCtGkJSHeuo); 
                //alert(unescape(JGG1P3bDnUSDL12Mui7KzYjj28UjPdWxCCtGkJSHeuo));
                if((attendenceStatus == 0) && (unescape(JGG1P3bDnUSDL12Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 'false') && JGG1PMRKATNC != 0) {    
                    preTally.UserProfile.markAttendance();
                }
                preTally.BalanceSheet.menuNewBalSheet(); 

                
                if(unescape(JGG1P3bDnUSDL11Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 0) {
                    preTally.Settings.setBranchDetails();
                }
                /*$.ajax({

                        url: preTally.Initialize.encryptURL("requisites/getBankOBStatus.php")
                }).done(function(data) { 
                    if(data == 0) {
                        //setTimeout(function(){
                            preTally.Settings.setBranchDetails();

                        //},1);
                    }
                });*/

                //preTally.Reports.viewReports();
            }
        },
        initNotification : function () {
            setTimeout(function() {
             preTally.Notification.countNotification();
            }, 5000); 
        },
        encryptURL:function(url) {
            if(idleState == true) { 
                if(errorHandlerFlag == false) preTally.Initialize.logMeOut();
            }
            //console.log(url);
            
            timeoutStamp = new Date().getTime();
//            console.log(url);
            //console.clear();
            
            //console.clear();
            
            var encURL = 'p=' + url;
//            var _0xe095=["\x62\x61\x73\x65\x50\x61\x74\x68","\x3F","\x73\x74\x72\x69\x6E\x67\x69\x66\x79","\x50\x72\x65\x54\x61\x6C\x6C\x79\x50\x61\x73\x73\x50\x68\x72\x61\x73\x65","\x65\x6E\x63\x72\x79\x70\x74","\x41\x45\x53"];return definePath[_0xe095[0]]+_0xe095[1]+encodeURIComponent(CryptoJS[_0xe095[5]][_0xe095[4]](JSON[_0xe095[2]](encURL),_0xe095[3],{format:CryptoJSAesJson}).toString());
            //console.clear();
            return definePath['basePath'] + "?" + encURL;
            //return definePath['basePath'] + "?" + encodeURIComponent(CryptoJS.AES.encrypt(JSON.stringify(encURL), "Use Key Here", {format: CryptoJSAesJson}).toString());
            //return definePath['basePath'] + "?" + encrypt(encURL);
        },
        logMeOut : function () {
            //console.log('You R Logged Out');
            errorHandlerFlag = true; //To avoid recursion in encryptURL & logMeOut
            $.ajax({
                type: "POST",
                url: preTally.Initialize.encryptURL('logout.php'),
                //url: definePath['basePath'] + "?"+"p=logout.php",
//                url: definePath['basePath'] + "?" + encrypt("p=logout.php"),
            }).done(function() {
//                preTally.IM.DisConnect();
                preTally.Initialize.reSignInPopUp();
            });
        },
        reSignInPopUp:function () {
            errorHandlerFlag = true;
            confirmBox = dhtmlx.confirm({
                title   : "Session Expired !!",
                type    : "confirm-warning",
                text    : "Relogin To Continue?",
                id      : "signInConfirm",
                ok      : "SignIn",
                cancel  : "Logout",
                callback: function(status) {
                    if(status == true) {
                        preTally.Initialize.signInPopUp(this);
                        return true;
                    } else {
                        location.reload();
                    }
                }
            });
        },
        signInPopUp : function(inp) { 
            // dhtmlx.message.hide('signInConfirm');
            //var empId = unescape(JGG1P3bDnUSDL9Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            var empId = unescape(JGG1P3bDnUSDL1USRIDJS); //04-12-2024            

            dhxSignInWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            signInWin = dhxSignInWin.createWindow("signInWin", x, y, 440, 225);
            signInWin.center();
            signInWin.button("minmax1").hide();
            signInWin.button("minmax2").hide();
            signInWin.button("park").hide();
            signInWin.setModal(true);
            signInWin.hideHeader();
            signInWin.setText("Sign In");

            signInForm = signInWin.attachForm(preTally.Data.reLoginLayout(empId));
            signInForm.setItemFocus("password");
            signInForm.attachEvent("onButtonClick", function(name) {
//                if(name == 'cancelSignIn') { 
//                    dhxSignInWin.window("signInWin").close();
//                    preTally.Initialize.errorHandler(); 
//                }
                if(name == 'signIn') preTally.Initialize.doSignIn(); 
            });
            signInForm.attachEvent("onEnter", function() {
                preTally.Initialize.doSignIn();
            });
        },
        doSignIn : function() { 
            var doValidateSignIn = signInForm.validate();
            if(doValidateSignIn) {    
                preTally.Settings.progressOn(true, signInWin, null); 
                signInForm.send(preTally.Initialize.encryptURL('warehouse/signInSessionExpire.php') , function(loader, response) {
                    preTally.Settings.progressOff(true, signInWin, null);
                    if(response == "success") {
                        dhtmlx.message.hide('signInConfirm');
                        dhtmlx.message({text:"Successfully logged in" });
                        dhxSignInWin.window("signInWin").close();
                        errorHandlerFlag = false;
//                        preTally.IM.ReConnect();
                    }
                    else
                        dhtmlx.message({text:response });
                });
            }
        },
        inActivity : function () {

            $('*').bind('mousemove keydown scroll', function () {
                clearTimeout(idleTimer);
                if (idleState == true) { 
                    // Reactivated event    
                    //console.log('Welcome Back');
                }
                idleState = false;

                idleTimer = setTimeout(function () { 
                    // Idle Event
                    //console.log("You've been idle for " + idleWait/1000 + " seconds.");
                    
                    idleState = true; 
                    preTally.Initialize.logMeOut();
                }, idleWait);
            });
            $("body").trigger("mousemove");
        },
        checkNetConnection : function () {
            clearInterval(netConnectivityTimer);
            $(".netConnectivity_msg").css({
                "background-color"  : "#EBF3FF",
                "color"             : "#837427"
            }); 
            $(".netConnectivity_msg").html('Attempting To Reconnect. &nbsp;&nbsp;&nbsp; <img src="images/loading_green.gif" style="height:11px;" /> ');
            //console.log('Retry Internet Connection');
            
            $.ajaxSetup({async:false});
            response    = "";
            randomNO    = Math.round(Math.random() * 10000);
            $.get(preTally.Initialize.encryptURL('requisites/checkNetConnection.php'), { subins : randomNO },function(data) {
                response = true;
                $(".netConnectivity").hide(); 
                preTally.Settings.progressOff(true, dhxLayout, null);
                doOnMenuCall(requestedMenu);
                netConnectivityError = false;
                netConnectivityRequests = 0;
                //console.log(requestedMenu);
                //console.log('Internet Connection Success');
                //alert('Net Aayegaaaaaaaaaaaaaaa...........');
            }).error(function(jqXHR){
                response = false;
                //console.log(jqXHR);
                if(jqXHR.status==0) {
                    ConnectivityErrorMessage = 'Your Device Lost Internet Connection. ';  
                } else {
                    ConnectivityErrorMessage = 'Unexpected Error. Please Hold-on / Refresh. ';
                }
                //console.log('Internet Connection Fail');
                setTimeout(preTally.Initialize.netConnectionError, 5000);
            });
            return response;  
        },
        netConnectionError : function () {
            //console.log('Internet Error');
            var requestDealy = 5000;
            $(".netConnectivity_msg").css({
                "background-color"  : "#F9AFB0",
                "color"             : "#752B2B"
            });   
            $(".netConnectivity_msg").html(ConnectivityErrorMessage + ' Re-Trying In <b id="show-time">5</b> Seconds.'); 

            netConnectivityRequests ++ ;
            if(netConnectivityRequests > 5 && netConnectivityRequests <= 10) {
                $("b[id=show-time]").html(10);
                requestDealy = 10000;
            }
            if(netConnectivityRequests > 10 && netConnectivityRequests <= 15) {
                $("b[id=show-time]").html(30);
                requestDealy = 30000;
            }
            if(netConnectivityRequests > 15) {
                idleState = true; 
                preTally.Initialize.logMeOut();
            }
            
            netConnectivityTimer = setInterval(function() {
                var timeCounter = $("b[id=show-time]").html();
                var updateTime = eval(timeCounter)- eval(1);
                $("b[id=show-time]").html(updateTime);
            }, 1000);
            
            setTimeout(preTally.Initialize.checkNetConnection, requestDealy);
        },
        updateClock:function() {
            var currentTime = new Date ( );
            var currentHours = currentTime.getHours ( );
            var currentMinutes = currentTime.getMinutes ( );
            var currentSeconds = currentTime.getSeconds ( );
            var currentDate = currentTime.getDate();
            var currentMonth = currentTime.getMonth();
            var currentYear = currentTime.getFullYear();
            // Pad the minutes and seconds with leading zeros, if required
            currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
            currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
            // Choose either "AM" or "PM" as appropriate
            var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
            // Convert the hours component to 12-hour format if needed
            currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
            // Convert an hours component of "0" to "12"
            currentHours = ( currentHours == 0 ) ? 12 : currentHours;
            // Compose the string for display
            currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
            currentDateString = currentDate+ "/" + currentMonth + "/" + currentYear;
            // Update the time display
            document.getElementById("clock").firstChild.nodeValue = currentTimeString;
        }
    };
})(jQuery, this);
