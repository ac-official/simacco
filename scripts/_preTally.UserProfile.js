;
(function ($, window, undefined) {
    var SalRptFlag = 0;
    var SalPFRptFlag = 0;
    var SalESIRptFlag = 0;
    var PayModRptFlag = 0;
    var SalBSEntriesWin;
    var selected_bnk_id;
    var attsrtFlg = 0;
    var attord = 'asc';
    var attSortdRow = 2;
    var payroll_sel_month;
    var payroll_sel_year;
    var mnthlyatt_sel_month;
    var mnthlyatt_sel_year;
    var salrpt_sel_month;
    var salrpt_sel_year;
    var paymodesalrpt_sel_month;
    var paymodesalrpt_sel_year;
    var myattendance_month;
    var myattendance_year;
    var userDetails;
    var SalHistBrCombo;
    var SalHistDojCombo;
    var SHSrtFlg = 0;
    var reportShowAttendance;
    var prev_id;
    var SalNullOverride = 0;
    preTally.UserProfile = {
        menuSignOutProfile: function () {
            dhtmlx.confirm({
                //type:"confirm-warning",
                title: "Logout Pre-Tally",
                ok: "Yes", cancel: "No",
                text: "Confirm Logout Pre-Tally",
                callback: function (result) {
                    if (result == true)
                        $.ajax({
                            type: "POST",
                            url: preTally.Initialize.encryptURL("logout.php"),
                        }
                        ).done(function () {
                            location.href = 'index.php';
                        })

                }
            });
        },
        menuWallet: function () {
            if (!dhxMiddleBlockTabs.cells("menuWallet")) {
                dhxMiddleBlockTabs.addTab("menuWallet", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;My Wallet", 120);
                dhxMiddleBlockTabs.tabs("menuWallet").setActive();
                myWallet = dhxMiddleBlockTabs.cells("menuWallet").attachDataView();
                //myWallet.define("type", "ficon");
                myWallet.define("type", "ftiles");
                //myWallet.define("type", "ftable");

                myWallet.customize({
                    icons_src_dir: "assets/dataview/codebase/images"
                });
                myWallet.load("requisites/myWallet.php", function () {
                });
                preTally.UserProfile.walletClick(1);
                var OFType_Options = [
                    ['ficon', 'obj', 'Icon View', 'office.gif'],
                    ['ftiles', 'obj', 'Title View', 'department.gif'],
                    ['ftable', 'obj', 'Table View', 'hierarchy.gif'],
                ];
                myWalletTlbr = dhxMiddleBlockTabs.tabs("menuWallet").attachToolbar();
                myWalletTlbr.setIconsPath("images/icon/");
                myWalletTlbr.addButtonSelect('fldrType', '2', '', OFType_Options, '', '', true, true, 3, 'select');
                myWalletTlbr.setAlign('right');
                myWalletTlbr.setListOptionSelected('fldrType', 'ftiles');
                myWalletTlbr.attachEvent('onClick', function (id, type) {
                    //preTally.Settings.reLoadOffz('BS',id,ptyRprtTree);
                    if (id == 'fldrType') {
                        myWallet.types[type].icons_src_dir = "assets/dataview/codebase/images";
                        myWallet.define("type", type);
                    }
                    if (id == 'bkFldr') {
                        //console.log(myWalletPath);
                        myWalletPath = myWalletPath.substr(0, myWalletPath.lastIndexOf("/"));
                        myWallet.clearAll();
                        // console.log('requisites/myWallet.php?path=' + myWalletPath);
                        myWallet.load('requisites/myWallet.php?path=' + myWalletPath, function () {
                        });
                    }
                });
                myWalletTlbr.addButton('bkFldr', '1', 'Back', 'arrow.up.icon.gif', 'arrow.down.icon.gif');
                walletCMenu = new dhtmlXMenuObject();
                walletCMenu.setIconsPath("images/icon/context_menu/");
                walletCMenu.renderAsContextMenu();
                //console.log(myWallet.$view.id);
                walletCMenu.addContextZone(myWallet.$view.id);
                //walletCMenu.addContextZone("menuWallet");
                walletCMenu.loadStruct("requisites/myWalletMenu.php", function () {

                });
                walletCMenu.attachEvent("onContextMenu", function (zoneId, ev) {
                    if (folderMenuFlag == 1) {
                        walletCMenu.hideContextMenu();
                        walletItemCMenu.setItemDisabled('new');
                        walletItemCMenu.setItemDisabled('refresh');
                    } else {
                        walletItemCMenu.hideContextMenu();
                        walletCMenu.setItemDisabled('open');
                        walletCMenu.setItemDisabled('rename');
                        walletCMenu.setItemDisabled('delete');
                    }
                    folderMenuFlag = 0;
                });
                walletItemCMenu = new dhtmlXMenuObject();
                walletItemCMenu.setIconsPath("images/icon/context_menu/");
                walletItemCMenu.renderAsContextMenu();
                walletItemCMenu.loadStruct("requisites/myWalletMenu.php");
                //console.log(myWallet.$view.id);

                myWallet.attachEvent("onBeforeContextMenu", function (id, e) {
                    folderMenuFlag = 1;
                    walletItemCMenu._doOnContextBeforeCall(e, {id: id});
                    myWallet.select(id);
                    return false;
                });
                walletCMenu.attachEvent("onClick", function (id, zoneId) {
                    console.log("<b>onClick</b>: " + id + " was clicked, context menu at zone <b>" + zoneId + "</b><br>");
                    if (id == 'folder') {
                        myWallet.clearAll();
                        myWallet.load('requisites/myWallet.php?nf=1&path=' + myWalletPath, function () {
                        });
                    }
                    if ((id == 'ficon') || (id == 'ftiles') || (id == 'ftable')) {
                        // myWallet.types[id].icons_src_dir = "assets/dataview/codebase/images";
                        myWallet.define("type", id);
                    }
                    if (id == 'refresh') {
                        myWallet.clearAll();
                        myWallet.load('requisites/myWallet.php?path=' + myWalletPath, function () {
                        });
                    }
                });
                walletItemCMenu.attachEvent("onClick", function (id, zoneId) {
                    //console.log("<b>onClick</b>: " + id + " was clicked, context menu at zone <b>" + zoneId + "</b><br>");
                    if (id == 'delete') {
                        //console.log(zoneId);
                        //console.log($('div[dhx_f_id="'+zoneId+'"] div.dhx_item_text').html());//dhx_f_id
                        var f = $('div[dhx_f_id="' + zoneId + '"] div.dhx_item_text').html();
                        myWallet.clearAll();
                        myWallet.load('requisites/myWallet.php?df=1&f=' + f + '&path=' + myWalletPath, function () {
                        });
                    }
                    if (id == 'rename') {
                        myWallet.edit(zoneId);
                    }
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuWallet").setActive();
            }
        },
        walletClick: function () {
            myWallet.attachEvent("onItemDblClick", function (itemId) {
                if (myWallet.get(itemId).type != 'file') {
                    myWalletPath = myWalletPath + '/' + myWallet.get(itemId).name;
                    myWallet.clearAll();
                    //console.log('requisites/myWallet.php?path=' + myWalletPath);
                    myWallet.load('requisites/myWallet.php?path=' + myWalletPath, function () {

                    });
                }
            });
        },
        menuMyPassword: function () {

            if (!dhxMiddleBlockTabs.cells("menuMyPassword")) {
                dhxMiddleBlockTabs.addTab("menuMyPassword", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Password", 160);
                //dhxMiddleBlockTabs.setTabActive("menuMyPassword");
                dhxMiddleBlockTabs.tabs("menuMyPassword").setActive();
                addPasswordForm = dhxMiddleBlockTabs.cells("menuMyPassword").attachForm();
                addPasswordForm.loadStruct(preTally.Initialize.encryptURL("requisites/changePass.php"), function () {

                    addPasswordForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newPasswordSave') {
                            var newItem = addPasswordForm.validate();
                            if (newItem) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addPasswordForm.send(preTally.Initialize.encryptURL('warehouse/changePass.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    dhtmlx.message({text: response});
                                    addPasswordForm.resetValidateCss();
                                    addPasswordForm.clear();
                                });
                            }
                        } else {
                            addPasswordForm.resetValidateCss();
                            addPasswordForm.clear();
                        }
                    });
                });
            } else {
                //dhxMiddleBlockTabs.setTabActive("menuMyPassword");
                dhxMiddleBlockTabs.tabs("menuMyPassword").setActive();
            }
            //dhxMiddleBlockTabs.setTabActive("menuMyPassword");
        },
        admResetPassword: function (usId) {
            dhxPassWin = new dhtmlXWindows();
            passWin = dhxPassWin.createWindow("wins_pass", 350, 600, 400, 150);
            passWin.button("minmax1").hide();
            passWin.button("minmax2").hide();
            passWin.button("park").hide();
            passWin.hideHeader();
            passWin.center();
            passWin.setModal(true);
            passWin.setText("New Password");
            setPassForm = passWin.attachForm();
            setPassForm.loadStruct(preTally.Initialize.encryptURL("requisites/admPassForm.php"), function () {
                setPassForm.setItemValue("ResUS_Id", usId);
                setPassForm.attachEvent("onButtonClick", function (name) {
                    if (name == "admPassSave") {
                        if (setPassForm.getItemValue("newPassword") == setPassForm.getItemValue("confrmPassword")) {
                            setPassForm.send(preTally.Initialize.encryptURL("warehouse/admResetPass.php"), function (loader, response) {
                                dhxPassWin.window("wins_pass").close();
                                dhtmlx.message({text: response});
                            });
                        } else {
                            setPassForm.setValidateCss("confrmPassword", false, "validate_red");
                            dhtmlx.message({text: "Password Mismatch"});
                        }
                    } else {
                        dhxPassWin.window("wins_pass").close();
                    }
                });
            });
        },
        menuNewUser: function (usId, viewFlg) {
            var tab_flag = 0;
            if (usId == '0')
                userModeForm = 'new_user';
            else if (usId == 'self')
                userModeForm = 'self_user';
            else if (viewFlg == '1') {
                userModeForm = 'view_user' + usId;
            } else {
                userModeForm = 'edit_user_' + usId;
                tab_flag = 1;
            }
            if (!dhxMiddleBlockTabs.tabs(userModeForm)) {
                if (tab_flag == 1) {
                    if (dhxMiddleBlockTabs.tabs('new_user'))
                        dhxMiddleBlockTabs.tabs('new_user').close();
                    else if (dhxMiddleBlockTabs.tabs('self_user'))
                        dhxMiddleBlockTabs.tabs('self_user').close();
                }
                var title = "Edit User";
                if (usId == 'self')
                    title = "My Profile";
                if (usId == '0')
                    title = "New User";
                if (viewFlg == 1)
                    title = "View User";
                dhxMiddleBlockTabs.addTab(userModeForm, "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;" + title, 120);
                dhxMiddleBlockTabs.tabs(userModeForm).setActive();
                new_user_form[userModeForm] = dhxMiddleBlockTabs.cells(userModeForm).attachForm();
                var params = "r=" + usId + "&view=" + viewFlg;
                new_user_form[userModeForm].attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                new_user_form[userModeForm].attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                new_user_form[userModeForm].loadStruct(preTally.Initialize.encryptURL("requisites/newUser.php&" + params), function () {
                    SalNullOverride = 0;
                    var us_id = new_user_form[userModeForm].getItemValue("US_ID");
                    $('#changeProfileImage' + us_id).click(function () {
                        uploadSrc = '#profileImage' + us_id;
                        uploadPath = 'uploads/profileImage/';
                        uploadForm = new_user_form[userModeForm];
                        uploadField = 'US_Image';
                        document.getElementById("prog_bar").value = "uploadProgress" + us_id;
                        $('#uptext').trigger('click');
                    });
                    preTally.UserProfile.filterUserForm('authentication', usId, viewFlg);
                    if (viewFlg == 1) {
                        $('#changeProfileImage' + usId).remove();
                    }
                    //new_user_form[userModeForm].setItemValue("SP_Id", new_user_form[userModeForm].getItemValue("H_SP_Id"));
                    //var EmpStatusGrid = new dhtmlXGrid('empStatusContainer'); 
                    if (usId != '0') {
                        var EmpStatusGrid = new dhtmlXGridObject(new_user_form[userModeForm].getContainer("empStatusGrid"));
                        EmpStatusGrid.enableColSpan(true);
                        EmpStatusGrid.loadXML(preTally.Initialize.encryptURL("requisites/listempStatusHistory.php&usid=" + us_id));
                    }
                    if (viewFlg != 1) {
                        new_user_form[userModeForm].setItemValue("US_Gender", new_user_form[userModeForm].getItemValue("H_US_Gender"));
                        LocConCombo[userModeForm] = new_user_form[userModeForm].getCombo("CN_Id");
                        LocSteCombo[userModeForm] = new_user_form[userModeForm].getCombo("ST_Id");
                        LocCtyCombo[userModeForm] = new_user_form[userModeForm].getCombo("CT_Id");
                    }
                    if (usId != 'self' && viewFlg != 1) {
                        SPCombo[userModeForm] = new_user_form[userModeForm].getCombo("SP_Id");
                        SPCombo[userModeForm].attachEvent("onChange", function () {
                            payable[userModeForm] = SPCombo[userModeForm].getSelectedValue();
                            if (payable[userModeForm] == 1) {
                                new_user_form[userModeForm].showItem('Cmp_BankDetails');
                                new_user_form[userModeForm].setRequired("Bank_AC", true);
                            } else {
                                new_user_form[userModeForm].hideItem('Cmp_BankDetails');
                                new_user_form[userModeForm].setRequired("Bank_AC", false);
                            }

                        });
                    }
                    if ((usId != 'self') && viewFlg != 1 && new_user_form[userModeForm].isItem("US_Report"))
                    {
                        params = "us_id=" + usId;
                        US_ReportCombo[userModeForm] = new_user_form[userModeForm].getCombo("US_Report");
                        US_ReportCombo[userModeForm].enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php&" + params), true);
                        preTally.Settings.comboFilterPreload(US_ReportCombo[userModeForm], "RptCombo");
                    }
                    if (viewFlg != 1) {
                        LocConCombo[userModeForm].attachEvent("onClose", function () {
                            LocSteCombo[userModeForm].clearAll();
                            LocCtyCombo[userModeForm].clearAll();
                            LocCtyCombo[userModeForm].setComboText("");
                            var country_id = LocConCombo[userModeForm].getSelectedValue();
                            if ((country_id)) {
                                var params = "cid=" + country_id;
                                LocSteCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/states.php&" + params), function () {
                                });
                            }
                        });
                        LocSteCombo[userModeForm].attachEvent("onClose", function () {
                            LocCtyCombo[userModeForm].clearAll();
                            LocCtyCombo[userModeForm].setComboText("");
                            var state_id = LocSteCombo[userModeForm].getSelectedValue();
                            if ((state_id) && (state_id != 0)) {
                                var params = "sid=" + state_id;
                                LocCtyCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/cities.php&" + params), function () {
                                });
                            }
                        });
                        if (new_user_form[userModeForm].getItemValue("H_CN_Id")) {
                            new_user_form[userModeForm].setItemValue("CN_Id", new_user_form[userModeForm].getItemValue("H_CN_Id"));
                        }
                        if (new_user_form[userModeForm].getItemValue("H_ST_Id")) {
                            var params = "cid=" + new_user_form[userModeForm].getItemValue("H_CN_Id");
                            LocSteCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/states.php&" + params), function () {
                                new_user_form[userModeForm].setItemValue("ST_Id", new_user_form[userModeForm].getItemValue("H_ST_Id"));
                            });
                        }
                        if (new_user_form[userModeForm].getItemValue("H_CT_Id") != 0) {
                            var params = "sid=" + new_user_form[userModeForm].getItemValue("H_ST_Id");
                            LocCtyCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/cities.php&" + params), function () {
                                new_user_form[userModeForm].setItemValue("CT_Id", new_user_form[userModeForm].getItemValue("H_CT_Id"));
                            });
                        }
                    }


                    if (usId != 'self' && viewFlg != 1) {
                        var US_DojCalendar = new dhtmlXCalendarObject("US_DOJ");
                        US_DojCalendar.setSensitiveRange(null, "2014-09-23");
                        if (usId != '0') {

                            new_user_form[userModeForm].setItemValue("US_Status", new_user_form[userModeForm].getItemValue("H_US_Status"));
                            new_user_form[userModeForm].setItemValue("OF_Id", new_user_form[userModeForm].getItemValue("H_OF_Id"));
                            if (new_user_form[userModeForm].isItem("US_Report"))
                            {
                                US_ReportCombo[userModeForm] = new_user_form[userModeForm].getCombo("US_Report");
                                dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/persons.php&us_id=" + usId + "&mask=" + new_user_form[userModeForm].getItemValue("H_US_Report_Name")), function (xml) {
                                    US_ReportCombo[userModeForm].load(xml.xmlDoc.responseText);
                                    new_user_form[userModeForm].setItemValue("US_Report", new_user_form[userModeForm].getItemValue("H_US_Report_Id"));
                                });
//                                US_ReportCombo.setComboValue( new_user_form[userModeForm].getItemValue("H_US_Report_Id"));
//                                US_ReportCombo.setComboText( new_user_form[userModeForm].getItemValue("H_US_Report_Name"));

                            }
                            dhxMiddleBlockTabs.tabs(userModeForm).setText("Edit - " + new_user_form[userModeForm].getItemValue("US_EMPID"));
                        }

                        if (viewFlg != 1) {
                            officeCombo[userModeForm] = new_user_form[userModeForm].getCombo("OF_Id");
                            locateCombo[userModeForm] = new_user_form[userModeForm].getCombo("LC_Id");
                            UTCombo[userModeForm] = new_user_form[userModeForm].getCombo("US_Type");
                            DPCombo[userModeForm] = new_user_form[userModeForm].getCombo("DP_Id");
                            DGCombo[userModeForm] = new_user_form[userModeForm].getCombo("DG_Id");
                            ESCombo[userModeForm] = new_user_form[userModeForm].getCombo("ES_Id");
                            loginHrsCombo[userModeForm] = new_user_form[userModeForm].getCombo("login_Hr");
                            loginMinsCombo[userModeForm] = new_user_form[userModeForm].getCombo("login_Min");
                            logoutHrsCombo[userModeForm] = new_user_form[userModeForm].getCombo("logout_Hr");
                            logoutMinsCombo[userModeForm] = new_user_form[userModeForm].getCombo("logout_Min");
                            //RprtCombo = new_user_form[userModeForm].getCombo("US_Report");
                            SSCombo[userModeForm] = new_user_form[userModeForm].getCombo("SS_Id");
                            preTally.Settings.comboSelectPreload(officeCombo[userModeForm], "officeCombo");
                            preTally.Settings.comboSelectPreload(locateCombo[userModeForm], "locateCombo");
                            preTally.Settings.comboSelectPreload(UTCombo[userModeForm], "UTCombo");
                            preTally.Settings.comboSelectPreload(DPCombo[userModeForm], "DPCombo");
                            preTally.Settings.comboSelectPreload(DGCombo[userModeForm], "DGCombo");
                            preTally.Settings.comboSelectPreload(SSCombo[userModeForm], "SSCombo");
                            preTally.Settings.comboSelectPreload(ESCombo[userModeForm], "ESCombo");
                            new_user_form[userModeForm].setReadonly('OF_Id', true);
                            new_user_form[userModeForm].setReadonly('LC_Id', true);
                            new_user_form[userModeForm].setReadonly('US_Type', true);
                            new_user_form[userModeForm].setReadonly('DP_Id', true);
                            new_user_form[userModeForm].setReadonly('DG_Id', true);
                            new_user_form[userModeForm].setReadonly('ES_Id', true);
                            new_user_form[userModeForm].setReadonly('SS_Id', true);
                            new_user_form[userModeForm].setReadonly('US_Blood', true);
                            new_user_form[userModeForm].setReadonly('US_Status', true);
                        }
                        if ((usId != 'self') && viewFlg != 1) {
                            var login = new_user_form[userModeForm].getItemValue("US_LoginTime");
                            var logout = new_user_form[userModeForm].getItemValue("US_LogoutTime");
                            login_arr = login.split(":");
                            logout_arr = logout.split(":");
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getTime.php&tmode=HRS"), function (xml) {
                                loginHrsCombo[userModeForm].load(xml.xmlDoc.responseText, function () {
                                    new_user_form[userModeForm].setItemValue("login_Hr", login_arr[0]);
                                });
                                logoutHrsCombo[userModeForm].load(xml.xmlDoc.responseText, function () {
                                    new_user_form[userModeForm].setItemValue("logout_Hr", logout_arr[0]);
                                });
                            });
                            dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/getTime.php&tmode=MINS"), function (xml) {
                                loginMinsCombo[userModeForm].load(xml.xmlDoc.responseText, function () {
                                    new_user_form[userModeForm].setItemValue("login_Min", login_arr[1]);
                                });
                                logoutMinsCombo[userModeForm].load(xml.xmlDoc.responseText, function () {
                                    new_user_form[userModeForm].setItemValue("logout_Min", logout_arr[1]);
                                });
                            });
                        }
                        var offz_id_prev = 0;
                        var dateToday = Date.today().toString("yyyy-MM-dd");
                        ESCombo[userModeForm].attachEvent("onClose", function () {
                            new_user_form[userModeForm].setItemValue("ESH_Date", dateToday);
                            //new_user_form[userModeForm].getCalendar("ESH_Date").setFormatedDate("Y-m-d", dateToday);                            
                            if (ESCombo[userModeForm].getSelectedValue() == new_user_form[userModeForm].getItemValue("H_ES_Resign")) {
                                dhtmlx.confirm({
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Do you want to block user from Pretally",
                                    callback: function (status) {
                                        if (status == true) {
                                            var blkparams = "usid=" + usId + "&flag=0";
                                            $.post(preTally.Initialize.encryptURL("warehouse/blockUser.php&" + blkparams), function (data) {
                                                new_user_form[userModeForm].setItemValue("US_Status", 0);
                                                dhtmlx.message({text: "Employee Blocked"});
                                            });
                                        }
                                    }});
                            }
                            /* if((usId != 'self')&&(usId != 0) && viewFlg!=1){                           
                             if(new_user_form[userModeForm].getItemValue("H_ES_Id")!=ESCombo[userModeForm].getSelectedValue())
                             {
                             dhtmlx.confirm({
                             type:"confirm-warning",
                             ok:"Yes", cancel:"No",
                             text:"Do you want to Change Employment Status",
                             callback:function(status){
                             if(status==false)
                             new_user_form[userModeForm].setItemValue("ES_Id", new_user_form[userModeForm].getItemValue("H_ES_Id"));
                             else if(status==true){
                             var resignFlg=0;
                             if(ESCombo[userModeForm].getSelectedValue()==new_user_form[userModeForm].getItemValue("H_ES_Resign")){
                             resignFlg=1;
                             }else if(new_user_form[userModeForm].getItemValue("H_ES_Id")==new_user_form[userModeForm].getItemValue("H_ES_Resign")){
                             resignFlg=2;                                                            
                             }
                             new_user_form[userModeForm].setItemValue("H_ES_Id", new_user_form[userModeForm].getItemValue("ES_Id"));
                             var params="usid="+usId+"&stats="+ESCombo[userModeForm].getSelectedValue()+"&resgnStats="+resignFlg;
                             $.post( preTally.Initialize.encryptURL("warehouse/changeEmpStatus.php&"+params), function( data ) {
                             dhtmlx.message({text: "Employee Status Updated"});
                             });
                             if(resignFlg==1){  
                             dhtmlx.confirm({
                             type:"confirm-warning",
                             ok:"Yes", cancel:"No",
                             text:"Do you want to block user from Pretally",
                             callback:function(status){
                             if(status==true){
                             var blkparams="usid="+usId+"&flag=0";
                             $.post( preTally.Initialize.encryptURL("warehouse/blockUser.php&"+blkparams), function(data){
                             new_user_form[userModeForm].setItemValue("US_Status",0);
                             dhtmlx.message({text: "Employee Blocked"});});
                             } 
                             
                             if (dhxMiddleBlockTabs.tabs("list_user")) {                                            
                             var currpage=listPreTallyUser.currentPage ; 
                             var filterValue = new Array($('#id_USR').val(),$('#name_USR').val(),$('#acl_USR').val(), $('#des_USR').val(), $('#loc_USR').val(),$('#estat_USR').val(), $('#dept_USR').val(), $('#stat_USR').val(),$('#skip_USR').val());
                             listPreTallyUser.clearAndLoad(preTally.Initialize.encryptURL("requisites/listUsers.php&filter="+filterValue), function() {
                             listPreTallyUser.changePage(currpage); 
                             $('.tb_userlist_tot').html("Total : "+listPreTallyUser.getRowsNum()+" ");                                           
                             });}
                             
                             }
                             });
                             }
                             
                             
                             }
                             }
                             
                             });
                             }
                             } */
                        });
                        officeCombo[userModeForm].attachEvent("onOpen", function () {
                            userModeForm = dhxMiddleBlockTabs.getActiveTab();
                            offz_id_prev = officeCombo[userModeForm].getSelectedValue();
                        });
                        officeCombo[userModeForm].attachEvent("onClose", function () {
                            //console.log(userModeForm);
                            userModeForm = dhxMiddleBlockTabs.getActiveTab();
                            var offz_id = officeCombo[userModeForm].getSelectedValue();
                            if (offz_id_prev != offz_id)
                            {
                                locateCombo[userModeForm].clearAll();
                                locateCombo[userModeForm].setComboText("Select Branch");
                                UTCombo[userModeForm].clearAll();
                                DPCombo[userModeForm].clearAll();
                                DGCombo[userModeForm].clearAll();
                                ESCombo[userModeForm].clearAll();
                                //RprtCombo.clearAll();
                                SSCombo[userModeForm].clearAll();
                                if ((offz_id) && (offz_id != 0)) {
                                    var params = "ofid=" + offz_id;
                                    locateCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/locations.php&" + params), function () {
                                    });
                                    UTCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/userTypes.php&" + params), function () {
                                    });
                                    DPCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/departments.php&" + params), function () {
                                    });
                                    DGCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/designations.php&" + params), function () {
                                    });
                                    ESCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/employeestatus.php&" + params), function () {
                                    });
                                    //RprtCombo.load("requisites/persons.php?"+encrypt(params), function () { });
                                    SSCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/salaryStructures.php&" + params), function () {
                                    });
                                    if (US_ReportCombo[userModeForm]) {
                                        US_ReportCombo[userModeForm].setComboText("");
                                        US_ReportCombo[userModeForm].setComboValue("");
                                    }
                                }
                            }
                        });
                        if (new_user_form[userModeForm].getItemValue("H_OF_Id")) {

                            var params = "ofid=" + new_user_form[userModeForm].getItemValue("H_OF_Id");
                            locateCombo[userModeForm].clearAll();
                            UTCombo[userModeForm].clearAll();
                            DPCombo[userModeForm].clearAll();
                            DGCombo[userModeForm].clearAll();
                            ESCombo[userModeForm].clearAll();
                            SSCombo[userModeForm].clearAll();
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            locateCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/locations.php&" + params), function () {
                                new_user_form[userModeForm].setItemValue("LC_Id", new_user_form[userModeForm].getItemValue("H_LC_Id"));
                            });
                            UTCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/userTypes.php&" + params), function () {

                                new_user_form[userModeForm].setItemValue("US_Type", new_user_form[userModeForm].getItemValue("H_UT_Id"));
                            });
                            DPCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/departments.php&" + params), function () {

                                new_user_form[userModeForm].setItemValue("DP_Id", new_user_form[userModeForm].getItemValue("H_DP_Id"));
                            });
                            DGCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/designations.php&" + params), function () {

                                new_user_form[userModeForm].setItemValue("DG_Id", new_user_form[userModeForm].getItemValue("H_DG_Id"));
                            });
                            ESCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/employeestatus.php&" + params), function () {

                                new_user_form[userModeForm].setItemValue("ES_Id", new_user_form[userModeForm].getItemValue("H_ES_Id"));
                            });
                            SSCombo[userModeForm].load(preTally.Initialize.encryptURL("requisites/salaryStructures.php&" + params), function () {

                                new_user_form[userModeForm].setItemValue("SS_Id", new_user_form[userModeForm].getItemValue("H_SS_Id"));
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if (usId != "self" && viewFlg != 1) {
                                    var Structid = SSCombo[userModeForm].getSelectedValue();
                                    preTally.UserProfile.getSalaryStructure(Structid, usId);
                                }
                            });
                        }

                        //dhxMiddleBlockTabs.setLabel(userModeForm,"Edit" + new_user_form[userModeForm].getItemValue("US_EMPID"))

                        //var SalStructCombo[userModeForm] = new_user_form[userModeForm].getCombo("SS_Id");                        

                        new_user_form[userModeForm].attachEvent("onInputChange", function (name) {
                            struct_val = new_user_form[userModeForm].getItemValue("SS_Id");
                            if (name == 'US_GrossSal') {
                                if (struct_val == 0 || struct_val == null) {
                                    dhtmlx.message("Select salary structure");
                                    new_user_form[userModeForm].setItemValue("US_GrossSal", 0);
                                } else
                                {

                                    preTally.UserProfile.salaryCalculate();
                                }
                            }
                        });
                        SSCombo[userModeForm].attachEvent("onClose", function () {
                            //SSCombo[userModeForm]=new_user_form[userModeForm].getCombo("SS_Id");                            
                            var Structid = SSCombo[userModeForm].getSelectedValue();
                            preTally.UserProfile.getSalaryStructure(Structid, usId);
                        });
                    }
                    new_user_form[userModeForm].attachEvent("onButtonClick", function (name) {
                        if (name == 'GEN_Pass') {
                            var randomStr = preTally.Settings.randomString(6, 'aA#');
                            new_user_form[userModeForm].setItemValue("US_Password", randomStr);
                        } else if (name == "resetUserPassword") {
                            var resUsId = new_user_form[userModeForm].getItemValue("US_ID");
                            preTally.UserProfile.admResetPassword(resUsId);
                        } else if (name == 'doSentWelcomeMail')
                        {
                            new_user_form[userModeForm].send(preTally.Initialize.encryptURL("warehouse/newUserMail.php"), function (loader, response) {
                                dhtmlx.message(response);
                            });
                        } else if (name == 'newUserValidate') {
                            userModeForm = dhxMiddleBlockTabs.getActiveTab();
                            var logInTimeHr = new_user_form[userModeForm].getItemValue("login_Hr");
                            var logInTimeMin = new_user_form[userModeForm].getItemValue("login_Min");
                            var logOutTimeHr = new_user_form[userModeForm].getItemValue("logout_Hr");
                            var logOutTimeMin = new_user_form[userModeForm].getItemValue("logout_Min");
                            var Time_err = 0;
                            if (logInTimeHr >= logOutTimeHr) {
                                Time_err = 1;
                            }
                            var SalErFlag = 0;
                            var SalNullFlag = 0;
                            if (usId != 'self' && viewFlg != 1) {
                                var PrevStruct = new_user_form[userModeForm].getItemValue("H_SS_Id");
                                var PrevGross = new_user_form[userModeForm].getItemValue("H_US_GrossSal");
                                var Structid = SSCombo[userModeForm].getSelectedValue();
                                var GrossSal = new_user_form[userModeForm].getItemValue("US_GrossSal");
                                if ((Structid == null || Structid == 0) && GrossSal != 0)
                                    SalErFlag = 1;
                                if ((PrevStruct != 0 && Structid == 0) || (PrevGross != 0 && GrossSal == 0) && SalNullOverride != 1) {
                                    SalNullFlag = 1
                                }
                            }
                            var newUserProfile = new_user_form[userModeForm].validate();
                            var values = new_user_form[userModeForm].getFormData();
                            var Cvalidate = preTally.Validate.Validate(values, 'US_Email', new_user_form[userModeForm], 'email');
                            if (newUserProfile && Cvalidate && sal_flag !== 1 && Time_err !== 1 && SalErFlag != 1 && SalNullFlag != 1) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                new_user_form[userModeForm].send(preTally.Initialize.encryptURL('warehouse/newUser.php&r=' + usId), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail' && usId != 'self' && viewFlg != 1 && response != 'id_fail' && response != 'reporting_fail' && response != 'sal_error') {
                                        new_user_form[userModeForm].resetValidateCss();
                                        new_user_form[userModeForm].clear();
                                        US_ReportCombo[userModeForm] = new_user_form[userModeForm].getCombo('US_Report');
                                        if (US_ReportCombo[userModeForm]) {
                                            US_ReportCombo[userModeForm].clearAll();
                                            US_ReportCombo[userModeForm].setComboText("");
                                            US_ReportCombo[userModeForm].setComboValue("");
                                        }
                                        LocConCombo[userModeForm].setComboValue("");
                                        //LocSteCombo[userModeForm].clearAll();
                                        new_user_form[userModeForm].setItemValue(usId, 0);
                                        if (userModeForm == 'edit_user_' + usId) {
                                            dhxMiddleBlockTabs.tabs(userModeForm).close();
                                        }
                                        $("#profileImage" + usId).attr("src", "uploads/profileImage/avatar.png");
                                        dhtmlx.message({text: response});
                                    } else if (response == 'sal_error') {
                                        dhtmlx.message('Invalid amount in Salary');
                                    } else if (response == 'reporting_fail') {
                                        dhtmlx.message('Invalid Reporting User.');
                                    } else if (response == "id_fail") {
                                        dhtmlx.message('UserId already Exists.Please Retry');
                                    } else {
                                        dhtmlx.message({text: response});
                                    }
                                    if (dhxMiddleBlockTabs.tabs("list_user")) {
                                        var currpage = listPreTallyUser.currentPage;
                                        var filterValue = new Array($('#id_USR').val(), $('#name_USR').val(), $('#acl_USR').val(), $('#des_USR').val(), $('#loc_USR').val(), $('#estat_USR').val(), $('#dept_USR').val(), $('#stat_USR').val(), $('#skip_USR').val());
                                        listPreTallyUser.clearAndLoad(preTally.Initialize.encryptURL("requisites/listUsers.php&filter=" + filterValue), function () {
                                            listPreTallyUser.changePage(currpage);
                                            $('.tb_userlist_tot').html("Total : " + listPreTallyUser.getRowsNum() + " ");
                                        });
                                    }
                                });
                            } else if (sal_flag == 1) {
                                dhtmlx.message("Gross salary below the scale");
                                new_user_form[userModeForm].setValidateCss("US_GrossSal", false);
                            } else if (SalNullFlag == 1) {
                                new_user_form[userModeForm].setValidateCss("US_GrossSal", false);
                                dhtmlx.confirm({
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Gross Salary Changed from" + PrevGross + "&nbsp;to&nbsp;" + GrossSal + ",Do you want to rollback?",
                                    callback: function (status) {
                                        if (status == true) {
                                            SalNullFlag = 0;
                                            SSCombo[userModeForm].setComboValue(PrevStruct);
                                            new_user_form[userModeForm].setItemValue("US_GrossSal", PrevGross);
                                            setTimeout(function () {
                                                preTally.UserProfile.salaryCalculate();
                                            }, 1000);
                                        } else {
                                            SalNullFlag = 0;
                                            SalNullOverride = 1;
                                            new_user_form[userModeForm].setValidateCss("US_GrossSal", true);
                                            return;
                                        }
                                    }
                                });
                            } else if (Time_err == 1) {
                                dhtmlx.message("Work schedule time error");
                                new_user_form[userModeForm].setValidateCss("login_Hr", false);
                                new_user_form[userModeForm].setValidateCss("login_Min", false);
                                new_user_form[userModeForm].setValidateCss("logout_Hr", false);
                                new_user_form[userModeForm].setValidateCss("logout_Min", false);
                            } else if (SalErFlag == 1) {
                                dhtmlx.message("Salary Structure error");
                                new_user_form[userModeForm].setValidateCss("SS_Id", false);
                            } else {

                                dhtmlx.message('Failed');
                            }
                        } else {
                            $("#profileImage" + usId).attr("src", "uploads/profileImage/avatar.png");
                            new_user_form[userModeForm].resetValidateCss();
                            new_user_form[userModeForm].clear();
                            if (LocConCombo[userModeForm])
                            {
                                LocConCombo[userModeForm].setComboValue("");
                            }
                            if (US_ReportCombo[userModeForm])
                            {
                                US_ReportCombo[userModeForm].clearAll();
                                US_ReportCombo[userModeForm].setComboText("");
                                US_ReportCombo[userModeForm].setComboValue("");
                            }
                            if (userModeForm == "new_user")
                                new_user_form[userModeForm].setItemValue(usId, 0);
                            if (userModeForm == "self_user")
                                dhxMiddleBlockTabs.tabs(userModeForm).close();
                        }


                    });
                });
                ptNewUserToolbar[userModeForm] = dhxMiddleBlockTabs.cells(userModeForm).attachToolbar();
                ptNewUserToolbar[userModeForm].loadStruct(preTally.Initialize.encryptURL("requisites/newUserToolbar.php&" + new Date().getTime()), function () {
                    preTally.UserProfile.disabledButton('authentication', 'select_16.gif');
                    ptNewUserToolbar[userModeForm].attachEvent("onClick", function (displayItem) {
                        userModeForm = dhxMiddleBlockTabs.getActiveTab();
                        ptNewUserToolbar[userModeForm].forEachItem(function (displayItem) {
                            if (!ptNewUserToolbar[userModeForm].isEnabled(displayItem)) {
                                ptNewUserToolbar[userModeForm].enableItem(displayItem);
                            }
                        });
                        preTally.UserProfile.disabledButton(displayItem, 'select_16.gif');
                        preTally.UserProfile.filterUserForm(displayItem, usId, viewFlg);
                    });
                });
                ptNewUserToolbar[userModeForm].setAlign('right');
            } else {
                preTally.Reload.reInitialize(userModeForm);
                dhxMiddleBlockTabs.tabs(userModeForm).setActive();
            }
        }, getSalaryStructure: function (Structid, usId) {
            if ((usId != 'self')) {
                basic_per = da_per = hra_per = convey_per = edu_per = medi_per = misc_per = 0;
                basic_type = da_type = hra_type = cca_type = convey_type = edu_type = medi_type = ded_esi_type = ded_saltds_type = ded_epf_type = 0;
                custom_flag = 0;
                if (Structid != 0) {
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/getSalStructPerc.php&ssid=" + Structid)
                    }).done(function (data) {
                        Struct_array = jQuery.parseJSON(data);
                        basic_per = parseFloat(Struct_array[0].SS_Basic);
                        basic_type = parseInt(Struct_array[0].SS_Basic_Type);
                        da_per = parseFloat(Struct_array[0].SS_DA);
                        da_type = parseInt(Struct_array[0].SS_DA_Type);
                        hra_per = parseFloat(Struct_array[0].SS_HRA);
                        hra_type = parseInt(Struct_array[0].SS_HRA_Type);
                        cca_per = parseFloat(Struct_array[0].SS_CCA);
                        cca_type = parseInt(Struct_array[0].SS_CCA_Type);
                        convey_per = parseFloat(Struct_array[0].SS_Convey);
                        convey_type = parseInt(Struct_array[0].SS_Convey_Type);
                        edu_per = parseFloat(Struct_array[0].SS_Edu);
                        edu_type = parseInt(Struct_array[0].SS_Edu_Type);
                        medi_per = parseFloat(Struct_array[0].SS_Medic);
                        medi_type = parseInt(Struct_array[0].SS_Medic_Type);
                        //misc_per        = parseInt(Struct_array[0].SS_Misc);                                        
                        //misc_type       = parseInt(Struct_array[0].SS_Misc_Type);

                        ded_esi = parseFloat(Struct_array[0].SS_DedESI);
                        ded_esi_type = parseInt(Struct_array[0].SS_DedESI_Type);
                        ded_epf = parseFloat(Struct_array[0].SS_DedEPF);
                        ded_epf_type = parseInt(Struct_array[0].SS_DedEPF_Type);
                        ded_proftds = parseInt(Struct_array[0].SS_DedProfTDS);
                        ded_proftds_type = parseInt(Struct_array[0].SS_DedProfTDS_Type);
                        ded_lwf = parseFloat(Struct_array[0].SS_DedLWF);
                        ded_lwf_type = parseInt(Struct_array[0].SS_DedLWF_Type);
                        if (basic_type == 1)
                            new_user_form[userModeForm].setItemValue("US_BasicSal", basic_per);
                        else
                            new_user_form[userModeForm].setItemValue("H_BasicSal", basic_per);
//                                        if(da_type==1)new_user_form[userModeForm].setItemValue("US_DaSal", da_per);
//                                        else new_user_form[userModeForm].setItemValue("H_DaSal", da_per);
                        if (hra_type == 1)
                            new_user_form[userModeForm].setItemValue("US_HraSal", hra_per);
                        else
                            new_user_form[userModeForm].setItemValue("H_HraSal", hra_per);
                        if (cca_type == 1)
                            new_user_form[userModeForm].setItemValue("US_CcaSal", cca_per);
                        else
                            new_user_form[userModeForm].setItemValue("H_CcaSal", cca_per);
                        if (convey_type == 1)
                            new_user_form[userModeForm].setItemValue("US_ConveySal", convey_per);
                        else
                            new_user_form[userModeForm].setItemValue("H_ConveySal", convey_per);
                        if (edu_type == 1)
                            new_user_form[userModeForm].setItemValue("US_EduSal", edu_type);
                        else
                            new_user_form[userModeForm].setItemValue("H_EduSal", edu_type);
                        if (medi_type == 1)
                            new_user_form[userModeForm].setItemValue("US_MedSal", medi_type);
                        else
                            new_user_form[userModeForm].setItemValue("H_MedSal", medi_type);
//                                        if(misc_type==1) new_user_form[userModeForm].setItemValue("US_MiscSal", misc_type);
//                                        else new_user_form[userModeForm].setItemValue("H_MiscSal", misc_type);                                                                                                               
//                                        new_user_form[userModeForm].setItemValue("H_SS_CFlag", custom_flag); 
                        new_user_form[userModeForm].setReadonly('US_BasicSal', true);
//                                        new_user_form[userModeForm].setReadonly('US_DaSal', true);
                        new_user_form[userModeForm].setReadonly('US_HraSal', true);
                        new_user_form[userModeForm].setReadonly('US_CcaSal', true);
                        new_user_form[userModeForm].setReadonly('US_ConveySal', true);
                        new_user_form[userModeForm].setReadonly('US_EduSal', true);
                        new_user_form[userModeForm].setReadonly('US_MedSal', true);
                        new_user_form[userModeForm].setReadonly('US_MiscSal', true);
                        //$('.salbasic').addClass('gross');
                        new_user_form[userModeForm].setReadonly('US_GrossSal', false);
                        preTally.UserProfile.salaryCalculate();
                        /*if (usId == 0)
                         {
                         new_user_form[userModeForm].setItemValue("US_BasicSal", 0);
                         //                                            new_user_form[userModeForm].setItemValue("US_DaSal", 0);
                         new_user_form[userModeForm].setItemValue("US_HraSal", 0);
                         new_user_form[userModeForm].setItemValue("US_CcaSal", 0);
                         new_user_form[userModeForm].setItemValue("US_ConveySal", 0);
                         new_user_form[userModeForm].setItemValue("US_EduSal", 0);
                         new_user_form[userModeForm].setItemValue("US_MedSal", 0);
                         new_user_form[userModeForm].setItemValue("US_MiscSal", 0);
                         }    */
                    });
                }
            }
        }
        , salaryCalculate: function () {
            sal_flag = 0;
            var gross = new_user_form[userModeForm].getItemValue('US_GrossSal');
            if (!isNaN(gross)) {
                if (basic_type == 0)
                    basic = gross * basic_per / 100;
                else
                    basic = basic_per;
//          if(da_type==0) da = gross * da_per / 100;
//          else da=da_per;
                if (hra_type == 0)
                    hra = gross * hra_per / 100;
                else
                    hra = hra_per;
                if (cca_type == 0)
                    cca = gross * cca_per / 100;
                else
                    cca = cca_per;
                if (convey_type == 0)
                    convey = gross * convey_per / 100;
                else
                    convey = convey_per;
                if (edu_type == 0)
                    edu = gross * edu_per / 100;
                else
                    edu = edu_per;
                if (medi_type == 0)
                    medi = gross * medi_per / 100;
                else
                    medi = medi_per;
                //if(misc_type==0) misc = gross * misc_per / 100;
                //else misc=misc_per;
                var sub_total = basic + cca + hra + convey + edu + medi;
                misc = gross - sub_total;
                if (misc < 0) {
                    //misc=0;
                    sal_flag = 1;
                } else {
                    sal_flag = 0;
                }
                var deduct_subtotal = basic + cca + convey + edu + medi + misc;
                if (ded_esi_type == 0)
                    esi_val = deduct_subtotal * ded_esi / 100;
                else
                    esi_val = ded_esi;
                if (ded_epf_type == 0)
                    epf_val = deduct_subtotal * ded_epf / 100;
                else
                    epf_val = ded_epf;
                /*if(ded_proftds_type==0)*/ proftds_val = gross * ded_proftds / 100;
                /*else proftds_val=ded_proftds;  */
                if (ded_lwf_type == 0)
                    lwf_val = gross * ded_lwf / 100;
                else
                    lwf_val = ded_lwf;
                sub_total = basic + hra + cca + convey + edu + medi;
                new_user_form[userModeForm].setItemValue("US_BasicSal", basic.toFixed(2));
                //new_user_form[userModeForm].setItemValue("US_DaSal", da);
                new_user_form[userModeForm].setItemValue("US_HraSal", hra.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_CcaSal", cca.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_ConveySal", convey.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_EduSal", edu.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_MedSal", medi.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_MiscSal", misc.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_DedEPF", epf_val.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_DedESI", esi_val.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_DedProfTDS", proftds_val.toFixed(2));
                new_user_form[userModeForm].setItemValue("US_DedLWF", lwf_val.toFixed(2));
            }
        },
        menuListUser: function () {
            if (!dhxMiddleBlockTabs.cells("list_user")) {
                dhxMiddleBlockTabs.addTab("list_user", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;List Users", 130);
                dhxMiddleBlockTabs.tabs("list_user").setActive();
                userIDFilter = '';
                nameFilter = '';
                AclTypeFilter = '';
                DesignationFilter = '';
                locationFilter = '';
                empstatusFilter = '';
                departmentFilter = '';
                ApprovedBlockedFilter = '';
                var tb_data_txt = ' <div class="tb_data_txt_secl">\
                                    <div class="tb_userlist_tot">Total : 0</div>\
                                    </div><div id="user_paging"></div>';
                listUserStbar = dhxMiddleBlockTabs.cells("list_user").attachStatusBar({
                    text: "<input type='button' value='Export' className= 'button_export' onclick='preTally.UserProfile.exportUsersListReport(userIDFilter,nameFilter,AclTypeFilter,DesignationFilter,locationFilter,empstatusFilter,departmentFilter,ApprovedBlockedFilter);' style='float:right; background-image: url(images/icon/default_18/excel.png);background-repeat: no-repeat; background-position: 0px 3px ;padding-left: 22px;height: 25px' />" + "" + tb_data_txt,
                    height: 23
                });
                listPreTallyUser = dhxMiddleBlockTabs.cells("list_user").attachGrid();
                listPreTallyUser.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listPreTallyUser.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listPreTallyUser.attachEvent("onCheck", function (usid, cInd, state) {
                    if (cInd == 12) {
                        dhtmlx.confirm({
                            title: "Attendance",
                            type: "confirm-warning",
                            text: "Confirm Skipping Attendance?",
                            callback: function (response) {
                                if (response) {
                                    var userDetails = {};
                                    userDetails['UsId'] = usid;
                                    userDetails['Type'] = 'skipatt';
                                    var stats;
                                    if (state == true)
                                        stats = 1;
                                    else
                                        stats = 0;
                                    userDetails['Status'] = stats;
                                    $.post(preTally.Initialize.encryptURL("warehouse/changeAttStats.php"), {StatValues: userDetails}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.UserProfile.applyListUserFilter(1);
                                    });
                                } else {
                                    if (listPreTallyUser.cells(usid, 12).getValue() == 1)
                                        listPreTallyUser.cells(usid, 12).setValue(0);
                                    else
                                        listPreTallyUser.cells(usid, 12).setValue(1);
                                }
                            }
                        });
                    }
                    if (cInd == 13) {
                        dhtmlx.confirm({
                            title: "Attendance",
                            type: "confirm-warning",
                            text: "Confirm Skipping Working Hours?",
                            callback: function (response) {
                                if (response) {
                                    var userDetails = {};
                                    userDetails['UsId'] = usid;
                                    userDetails['Type'] = 'wrkhr';
                                    var stats;
                                    if (state == true)
                                        stats = 1;
                                    else
                                        stats = 0;
                                    userDetails['Status'] = stats;
                                    $.post(preTally.Initialize.encryptURL("warehouse/changeAttStats.php"), {StatValues: userDetails}, function (data) {
                                        dhtmlx.message({text: data});
                                        preTally.UserProfile.applyListUserFilter(1);
                                    });
                                } else {
                                    if (listPreTallyUser.cells(usid, 13).getValue() == 1)
                                        listPreTallyUser.cells(usid, 13).setValue(0);
                                    else
                                        listPreTallyUser.cells(usid, 13).setValue(1);
                                }
                            }
                        });
                    }
                });
                listPreTallyUser.setColSorting("false,false,false,false,false,false,false,false,false,false,false,false");
                listPreTallyUser.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false");
                listPreTallyUser.enableAutoWidth(true);
                listPreTallyUser.setPagingWTMode(true, false, true, [15, 30, 50]);
                listPreTallyUser.enablePaging(true, 50, 5, "user_paging", true);
                listPreTallyUser.setPagingSkin("toolbar", "dhx_skyblue");
                listPreTallyUser.attachHeader(",<input type='text'  id='id_USR' class='listUsr_text_filter' style='width: 90%;' placeholder='User ID'>\
                            ,<input type='text'  id='name_USR' class='listUsr_text_filter' style='width: 90%;' placeholder='Name'>,\
                            <div id='acl_USR' style='width: 90%;' placeholder='ACL'></div>,\
                            <div id='des_USR' style='width: 90%;' placeholder='Desigmation'></div>,\
                            <div id='loc_USR' style='width: 90%;' placeholder='Location'></div>,\
                            <div id='estat_USR' style='width: 90%;' placeholder='Emplyee Status'></div>,\
                            <div id='dept_USR' style='width: 90%;' placeholder='Department'></div>,,\
                            <select id='stat_USR' style='width:90%; font-size:8pt; font-family:Tahoma;'><option value='All'>All</option><option value='1' selected='true'>Approved</option><option value='0'>Blocked</option></select>,,,<select id='skip_USR' style='width:90%; font-size:8pt; font-family:Tahoma;'><option value='All'>All</option><option value='1'>Enabled</option><option value='0'>Disabled</option></select>,<select id='skip_WrkHrs' style='width:90%; font-size:8pt; font-family:Tahoma;'><option value='All'>All</option><option value='1'>Enabled</option><option value='0'>Disabled</option></select>");
                listPreTallyUser.init();
                listPreTallyUser.loadXML(preTally.Initialize.encryptURL("requisites/listUsers.php&calltype=new"), function () {
                    $('.tb_userlist_tot').html("Total : " + listPreTallyUser.getUserData("", "TL_Count") + " ");
                    listPreTallyUser.attachEvent("onRowSelect", function (rId, cId) {
                        if (cId === 10) {
                            if (listPreTallyUser.getUserData('', 'acl_status') == 1) {
                                preTally.UserProfile.menuNewUser(rId, 0);
                            }
                        } else
                            preTally.UserProfile.menuNewUser(rId, 1);
                    });
                    preTally.UserProfile.applyListempFilters();
                });
                listPreTallyUser.attachEvent("onFilterEnd", function (a) {
                    $('.tb_userlist_tot').html("Total : " + listPreTallyUser.getUserData("", "TL_Count") + " ");
                });
            } else {
                //dhxMiddleBlockTabs.setTabActive("list_user");
                dhxMiddleBlockTabs.tabs("list_user").setActive();
            }
        }, applyListempFilters: function () {
            aclFiltrCombo = new dhtmlXCombo("acl_USR");
            aclFiltrCombo.load(preTally.Initialize.encryptURL("requisites/userTypes.php&type=filt"), function () {
                aclFiltrCombo.setPlaceholder('ACL');
                preTally.UserProfile.applyFilterHandler(aclFiltrCombo);
                aclFiltrCombo.setOptionWidth(130);
            });
            aclFiltrCombo.attachEvent("onChange", function () {
                var aclFiltrComboVal = aclFiltrCombo.getSelectedValue();
                if (!aclFiltrCombo.getSelectedValue() && aclFiltrCombo.getComboText())
                    aclFiltrComboVal = aclFiltrCombo.getComboText();
                $("#acl_USR").val(aclFiltrComboVal);
                preTally.UserProfile.applyListUserFilter();
            });
            desFiltrCombo = new dhtmlXCombo("des_USR");
            desFiltrCombo.load(preTally.Initialize.encryptURL("requisites/designations.php&type=filt"), function () {
                desFiltrCombo.setPlaceholder('Designation');
                preTally.UserProfile.applyFilterHandler(desFiltrCombo);
                desFiltrCombo.setOptionWidth(150);
            });
            desFiltrCombo.attachEvent("onChange", function () {
                var desFiltrComboVal = desFiltrCombo.getSelectedValue();
                if (!desFiltrCombo.getSelectedValue() && desFiltrCombo.getComboText())
                    desFiltrComboVal = desFiltrCombo.getComboText();
                $("#des_USR").val(desFiltrComboVal);
                preTally.UserProfile.applyListUserFilter();
            });
            locFiltrCombo = new dhtmlXCombo("loc_USR");
            locFiltrCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check"), function () {
                locFiltrCombo.setPlaceholder('Location');
                preTally.UserProfile.applyFilterHandler(locFiltrCombo);
                locFiltrCombo.setOptionWidth(150);
            });
            locFiltrCombo.attachEvent("onChange", function () {
                var locFiltrComboVal = locFiltrCombo.getSelectedValue();
                if (!locFiltrCombo.getSelectedValue() && locFiltrCombo.getComboText())
                    locFiltrComboVal = locFiltrCombo.getComboText();
                $("#loc_USR").val(locFiltrComboVal);
                preTally.UserProfile.applyListUserFilter();
            });
            estatFiltrCombo = new dhtmlXCombo("estat_USR");
            estatFiltrCombo.load(preTally.Initialize.encryptURL("requisites/employeestatus.php&type=filt"), function () {
                estatFiltrCombo.setPlaceholder('Employee Status');
                preTally.UserProfile.applyFilterHandler(estatFiltrCombo);
                estatFiltrCombo.setOptionWidth(100);
            });
            estatFiltrCombo.attachEvent("onChange", function () {
                var estatFiltrComboVal = estatFiltrCombo.getSelectedValue();
                if (!estatFiltrCombo.getSelectedValue() && estatFiltrCombo.getComboText())
                    estatFiltrComboVal = estatFiltrCombo.getComboText();
                $("#estat_USR").val(estatFiltrComboVal);
                preTally.UserProfile.applyListUserFilter();
            });
            deptFiltrCombo = new dhtmlXCombo("dept_USR");
            deptFiltrCombo.load(preTally.Initialize.encryptURL("requisites/departments.php&type=filt"), function () {
                deptFiltrCombo.setPlaceholder('Department');
                preTally.UserProfile.applyFilterHandler(deptFiltrCombo);
                deptFiltrCombo.setOptionWidth(100);
            });
            deptFiltrCombo.attachEvent("onChange", function () {
                var deptFiltrComboVal = deptFiltrCombo.getSelectedValue();
                if (!deptFiltrCombo.getSelectedValue() && deptFiltrCombo.getComboText())
                    deptFiltrComboVal = deptFiltrCombo.getComboText();
                $("#dept_USR").val(deptFiltrComboVal);
                preTally.UserProfile.applyListUserFilter();
            });
            var filtrInterval;
            $(".listUsr_text_filter").keyup(function () {
                if (filtrInterval)
                    clearInterval(filtrInterval);
                filtrInterval = setInterval(function () {
                    preTally.UserProfile.applyListUserFilter();
                    clearInterval(filtrInterval);
                }, 500);
            });
            $("#stat_USR").change(function () {
                preTally.UserProfile.applyListUserFilter();
            });
            $("#skip_USR").change(function () {
                preTally.UserProfile.applyListUserFilter();
            });
            $("#skip_WrkHrs").change(function () {
                preTally.UserProfile.applyListUserFilter();
            });
        },
        applyFilterHandler: function (obj) {
            obj.setFilterHandler(function (mask, option) {
                var r = false;
                if (mask.length == 0) {
                    r = true;
                } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                    r = true;
                }
                return r;
            });
        },
        applyListUserFilter: function (flag) {
            var currentPage = listPreTallyUser.currentPage;
            var filterValue = new Array($('#id_USR').val(), $('#name_USR').val(), $('#acl_USR').val(), $('#des_USR').val(), $('#loc_USR').val(), $('#estat_USR').val(), $('#dept_USR').val(), $('#stat_USR').val(), $('#skip_USR').val(), $('#skip_WrkHrs').val());
            listPreTallyUser.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            listPreTallyUser.clearAndLoad(preTally.Initialize.encryptURL("requisites/listUsers.php&filter=" + filterValue), function () {
                if (flag)
                    listPreTallyUser.changePage(currentPage);
                if (listPreTallyUser.getRowsNum() == 0) {
                    listPreTallyUser.addRow(0, ['', '', 'No Records Found...', '', '', '', '', '', '', ''], 0);
                    listPreTallyUser.setRowTextStyle(0, "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
                } else {
                    listPreTallyUser.deleteRow(0);
                }
                $('.tb_userlist_tot').html("Total : " + listPreTallyUser.getUserData("", "TL_Count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        exportUsersListReport: function () {
            var exportfilterValue = {};
            exportfilterValue['userIDFilter'] = $('#id_USR').val();
            exportfilterValue['nameFilter'] = $('#name_USR').val();
            exportfilterValue['AclTypeFilter'] = $('#acl_USR').val();
            exportfilterValue['DesignationFilter'] = $('#des_USR').val();
            exportfilterValue['locationFilter'] = $('#loc_USR').val()
            exportfilterValue['empstatusFilter'] = $('#estat_USR').val();
            exportfilterValue['departmentFilter'] = $('#dept_USR').val();
            exportfilterValue['ApprovedBlockedFilter'] = $('#stat_USR').val();
            exportfilterValue['skipUserFilter'] = $('#skip_USR').val();
            $.post(preTally.Initialize.encryptURL('warehouse/userListExcelExport.php'),
                    {filter: exportfilterValue},
                    function (data) {
                        fileName = data.split("XL_");
                        if (fileName[1]) {
                            document.location = "uploads/excelFile/" + fileName[1];
                        }
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
        },
        filterUserForm: function (displayItem, usId, viewFlg) {
            userModeForm = dhxMiddleBlockTabs.getActiveTab();
            new_user_form[userModeForm].forEachItem(function (itemID) {
                if (new_user_form[userModeForm].getUserData(itemID) != 'all') {
                    new_user_form[userModeForm].hideItem(itemID);
                    if (new_user_form[userModeForm].getUserData(itemID) == displayItem) {
                        new_user_form[userModeForm].showItem(itemID);
                    }
                }

                if (new_user_form[userModeForm].getItemType(itemID) == 'template')
                    new_user_form[userModeForm].clearNote(itemID);
            });
            if (displayItem == "salary") {
                if (usId != 'self' && viewFlg != 1) {
                    SPCombo[userModeForm].detachEvent("onXLE");
                    SPCombo[userModeForm].attachEvent("onXLE", function () {
                        payable[userModeForm] = SPCombo[userModeForm].getSelectedValue();
                        if (payable[userModeForm] == 1) {
                            new_user_form[userModeForm].showItem('Cmp_BankDetails');
                            new_user_form[userModeForm].setRequired("Bank_AC", true);
                        } else {
                            new_user_form[userModeForm].hideItem('Cmp_BankDetails');
                            new_user_form[userModeForm].setRequired("Bank_AC", false);
                        }
                    });
                    payable[userModeForm] = SPCombo[userModeForm].getSelectedValue();
                    if (payable[userModeForm] == 1) {
                        new_user_form[userModeForm].showItem('Cmp_BankDetails');
                        new_user_form[userModeForm].setRequired("Bank_AC", true);
                    } else {
                        new_user_form[userModeForm].hideItem('Cmp_BankDetails');
                        new_user_form[userModeForm].setRequired("Bank_AC", false);
                    }
                }
            }
        },
        disabledButton: function (id, img) {
            userModeForm = dhxMiddleBlockTabs.getActiveTab();
            ptNewUserToolbar[userModeForm].disableItem(id);
            ptNewUserToolbar[userModeForm].setItemImageDis(id, 'images/icon/' + img);
        },
        ProfiledisabledButton: function (id, img) {
            ptViewUserToolbar.disableItem(id);
            ptViewUserToolbar.setItemImageDis(id, 'images/icon/' + img);
        },
        updateUpload: function (name) {
            userModeForm = dhxMiddleBlockTabs.getActiveTab();
            uploadForm = new_user_form[userModeForm];
            if (uploadSrc)
                $(uploadSrc).attr('src', uploadPath + name);
            if (uploadField)
                $(uploadField).val(name);
            if (uploadField && uploadForm)
                uploadForm.setItemValue(uploadField, name);
        },
        menuUserMapTree: function () {
            if (!dhxMiddleBlockTabs.cells("menuUserMapTree")) {
                dhxMiddleBlockTabs.addTab("menuUserMapTree", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Map Users", 130);
                dhxMiddleBlockTabs.tabs("menuUserMapTree").setActive();
                aboutForm = dhxMiddleBlockTabs.cells('menuUserMapTree').attachURL("requisites/userMapTree.php");
            } else {
                dhxMiddleBlockTabs.tabs("menuUserMapTree").setActive();
            }
        },
        attendanceSave: function (rowId, cellInd, state, dataDate, dataME) {

            var dataDate = ptPayrollAttendence.cells(rowId, cellInd).getAttribute("dataDate");
            var dataME = ptPayrollAttendence.cells(rowId, cellInd).getAttribute("dataME");
            var userID = ptPayrollAttendence.cells(rowId, cellInd).getAttribute("userID");
            $.post(
                    preTally.Initialize.encryptURL('warehouse/attendance.php'),
                    {dataDate: dataDate, dataME: dataME, userID: userID, state: state},
                    function (responseText) {

                    }, "html");
        },
        markAttendance: function ()
        {
            dhxAttWin = new dhtmlXWindows();
            attWin = dhxAttWin.createWindow("wins_att", 200, 400, 350, 125);
            attWin.button("minmax1").hide();
            attWin.button("minmax2").hide();
            attWin.button("park").hide();
            attWin.hideHeader();
            attWin.center();
            attWin.setModal(true);
            attWin.setText("Attendance");
            setAttForm = attWin.attachForm();
            setAttForm.loadStruct(preTally.Initialize.encryptURL("requisites/attendForm.php"), function () {
                setAttForm.attachEvent("onButtonClick", function (name) {
                    if (name == "MarkAttend") {
                        setAttForm.send(preTally.Initialize.encryptURL("warehouse/attendance.php"), function (loader, response) {
                            dhxAttWin.window("wins_att").close();
                            JGG1P3bDnUSDL4Mui7KzYjj28UjPdWxCCtGkJSHeuo = 1;
                            dhtmlx.message({text: response});
                        });
                    } else {
                        dhxAttWin.window("wins_att").close();
                    }
                });
            });
        },
        listAttendance: function () {
            if (!dhxMiddleBlockTabs.cells("menulistAttendance")) {
                dhxMiddleBlockTabs.addTab("menulistAttendance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Attendance & Payroll Management", 260);
                dhxMiddleBlockTabs.tabs("menulistAttendance").setActive();
                AttTabbar = dhxMiddleBlockTabs.cells("menulistAttendance").attachTabbar();
                var ACLAttendView = unescape(JGG1P3bDnUSDL19Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var ACLAttendEdit = unescape(JGG1P3bDnUSDL14Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var ACLPayrollView = unescape(JGG1P3bDnUSDL15Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var ACLPayrollEdit = unescape(JGG1P3bDnUSDL16Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var ACLMnthlyAttend =unescape(JGG1P3bDnUSDL2Mui7KzYjj26UjPdWxCCtGkJSHeuo);
                var Attenabled = 0;
                var Mnthenabled = 0;
                var Payrollenabled = 0
                if (ACLAttendView == 1) {
                    AttTabbar.addTab("a1", "Daily Attendance   <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='refreshAttTab'/>");                   
                    AttTabbar.tabs("a1").setActive();
                    Attenabled = 1;
                }
                if(ACLMnthlyAttend==1 || ACLAttendEdit == 1){
                     AttTabbar.addTab("a2", "Monthly Attendance");
                     preTally.UserProfile.reportUserAttendance();
                     Mnthenabled=1;
                     if(Attenabled==0)
                     AttTabbar.tabs("a2").setActive();
                 }
                if (ACLPayrollView == 1 || ACLPayrollEdit == 1) {
                    AttTabbar.addTab("a3", "Employee Payroll Manage");
                    Payrollenabled = 1;
                    if(Mnthenabled==0)
                    AttTabbar.tabs("a3").setActive();
                }
                var attFlag = 0;
                var payrollFlag = 0;
                AttTabbar.attachEvent("onSelect", function (id, last_id) {
                    if (id == "a1" && attFlag == 0 && Attenabled == 1) {
                        preTally.UserProfile.listUserAttendance();
                        attFlag = 1;
                    }
                    if (id == "a3" && payrollFlag == 0 && Payrollenabled == 1) {
                        preTally.UserProfile.ListEmpPayroll();
                        payrollFlag = 1;
                    }
                    return true;
                });
                //preTally.Settings.progressOn(true, dhxLayout, null);
                if (Attenabled == 1) {
                    preTally.UserProfile.listUserAttendance();
                }
                if (Payrollenabled == 1) {
                    preTally.UserProfile.ListEmpPayroll();
                }
            } else {
                dhxMiddleBlockTabs.tabs("menulistAttendance").setActive();
            }           
        },
        listUserAttendance: function ()
        {
            listAttendanceLayout = AttTabbar.tabs("a1").attachLayout('1C');
            listAttendanceLayout.cells("a").hideHeader();
            listAttendanceToolbar = listAttendanceLayout.cells("a").attachToolbar();
            listAttendanceToolbar.addText("text_date", null, "Date");
            listAttendanceToolbar.addInput("att_date", null, "", 75);
            listAttendanceToolbar.addSeparator();
            listAttendanceToolbar.addButton("att_btn", null, "Search", "images/icon/search.png", null);
            var att_Inp_date = listAttendanceToolbar.getInput("att_date");
            $(".refreshAttTab").unbind("click").click(function () {
                var date = listAttendanceToolbar.getValue("att_date");
                var params = "ST_Date=" + date;
                AttendGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendanceUsers.php&" + params), function () {
                    $('.Att_count').html("Total:" + AttendGrid.getRowsNum());
                    $('.att_rpt_mark').html("Marked: " + AttendGrid.getUserData("", "att_marked") + " ");
                    $('.att_rpt_mark').attr("count", AttendGrid.getUserData("", "att_marked"));
                    $('.att_rpt_unmark').html("Unmarked: " + AttendGrid.getUserData("", "att_unmarked") + " ");
                    $('.att_rpt_unmark').attr("count", AttendGrid.getUserData("", "att_unmarked"));
                    $('.att_rpt_signout').html("Sign Out: " + AttendGrid.getUserData("", "att_signout") + " ");
                    $('.att_rpt_signout').attr("count", AttendGrid.getUserData("", "att_signout"));
                    $('.att_rpt_norm').html("Signed In : " + AttendGrid.getUserData("", "att_signedin") + " ");
                    $('.att_rpt_norm').attr("count", AttendGrid.getUserData("", "att_signedin"));
                    $('.att_rpt_late').html("Late SignIn: " + AttendGrid.getUserData("", "att_latein") + " ");
                    $('.att_rpt_late').attr("count", AttendGrid.getUserData("", "att_latein"));
                    $('.att_lvapplied').html("Leave Applied : " + AttendGrid.getUserData("", "att_lvapplied") + " ");
                    $('.att_lvapplied').attr("count", AttendGrid.getUserData("", "att_lvapplied"));
                    $('.att_lvrptd').html("Leave Reported : " + AttendGrid.getUserData("", "att_lvrptd") + " ");
                    $('.att_lvrptd').attr("count", AttendGrid.getUserData("", "att_lvrptd"));
                    $('.att_invalid').html("Not Signed Out : " + AttendGrid.getUserData("", "att_invalid") + " ");
                    $('.att_invalid').attr("count", AttendGrid.getUserData("", "att_invalid"));
                    $('.att_ntmarked').html("Not Marked : " + AttendGrid.getUserData("", "att_ntmarked") + " ");
                    $('.att_ntmarked').attr("count", AttendGrid.getUserData("", "att_ntmarked"));
                    $('.att_erlysout').html("Early Sign Out : " + AttendGrid.getUserData("", "att_earlyout") + " ");
                    $('.att_erlysout').attr("count", AttendGrid.getUserData("", "att_earlyout"));
                    $('.att_propsout').html("Proper Sign Out : " + AttendGrid.getUserData("", "att_propout") + " ");
                    $('.att_propsout').attr("count", AttendGrid.getUserData("", "att_propout"));
                    $('.att_latesout').html("Late Sign Out : " + AttendGrid.getUserData("", "att_lateout") + " ");
                    $('.att_latesout').attr("count", AttendGrid.getUserData("", "att_lateout"));
                    $('.btn_Sort_Datt').click(function () {
                        var colId = $(this).attr("colNum");
                        if (srtFlg == 0) {
                            $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                            AttendGrid.sortRows(colId, "str", "asc");
                            srtFlg = 1;
                        } else {
                            $(this).attr("src", 'images/icon/sort-descending-icon.png');
                            AttendGrid.sortRows(colId, "str", "des");
                            srtFlg = 0;
                        }
                    });
                });
            });
            att_Inp_date.setAttribute("readOnly", "true");
            Att_Calendar = new dhtmlXCalendarObject([att_Inp_date]);
            Att_Calendar.setDateFormat("%d.%m.%Y");
            var dateToday = Date.today().toString("dd.MM.yyyy");
            Att_Calendar.setSensitiveRange(null, dateToday);
            listAttendanceToolbar.setValue('att_date', dateToday, false);
            //listAttendanceToolbar.setAlign('right');
            listAttendanceToolbar.setIconsPath("images/icon/default_18/");
            var tb_data_txt = ' <div class="att_data_txt_secl">\
                                        <div class="Att_count" style="width:80px;">Total:0</div>\
                                        <div class="att_footer_click att_rpt_mark" count="0" filt="1" style="width:280px;text-align: center;" srt="Marked">Marked : 0</div>\
                                        <div class="att_footer_click att_rpt_unmark" count="0"  filt="1" style="width:300px;text-align: center;" srt="Unmarked">Unmarked : 0</div>\
                                        <div class="att_footer_click att_rpt_signout" count="0" filt="0" style="width:315px;text-align: center;" srt="Sign Out">Sign Out : 0</div>\
                                        </div>\
                                        <div class="att_data_txt_secl" style="top:30px;">\
                                        <div class="att_footer_click att_rpt_all" count="1" filt="1" srt="All" style="width:80px;">All</div>\
                                        <div class="att_footer_click att_rpt_norm" count="0" filt="1" srt="Signed In" style="width:80px;">Signed In : 0</div>\
                                        <div class="att_footer_click att_rpt_late" count="0" filt="1" srt="Late Sign In" style="width:90px;" >Late SignIn : 0</div>\
                                        <div class="att_footer_click att_invalid" count="0" filt="1"  srt="Not Signed Out" style="width:110px;">Not Signed Out : 0</div>\
                                        <div class="att_footer_click att_lvapplied" count="0" filt="1" style="width:100px;"  srt="Leave Applied">Leave Applied : 0</div>\
                                        <div class="att_footer_click att_lvrptd" count="0" filt="1"  style="width:110px;" srt="Leave Reported">Leave Reported : 0</div>\
                                        <div class="att_footer_click att_ntmarked" count="0" filt="1"  style="width:90px;" srt="Not Marked">Not Marked : 0</div>\
                                        <div class="att_footer_click att_propsout" count="0" filt="0" style="width:105px;"  srt="Proper Sign Out">Proper Sign Out: 0</div>\
                                        <div class="att_footer_click att_erlysout" count="0" filt="0" style="width:105px;" srt="Early Sign Out">Early Sign Out: 0</div>\
                                        <div class="att_footer_click att_latesout" count="0" filt="0" style="width:105px;" srt="Late Sign Out">Late Sign Out: 0</div>\
                                    </div>';
            var tbAttObj = listAttendanceLayout.cells("a").attachStatusBar({
                text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
                height: 60             // custom height
            });
            $(".att_footer_click").click(function () {
                var filtval = $(this).attr("filt");
                var srtval = $(this).attr("srt");
                var count = $(this).attr("count");
                if (filtval == 1) {
                    if (srtval == 'All') {
                        AttendGrid.getFilterElement(4).value = "";
                        AttendGrid.getFilterElement(5).value = "";
                    } else if (srtval == 'Marked' || srtval == 'Unmarked') {
                        AttendGrid.getFilterElement(5).value = srtval;
                        AttendGrid.getFilterElement(4).value = "";
                    } else {
                        AttendGrid.getFilterElement(4).value = srtval;
                        AttendGrid.getFilterElement(5).value = "";
                    }
                    if (count != 0)
                        AttendGrid.filterByAll();
                }
            });
            var srtFlg = 0;
            listAttendanceToolbar.attachEvent("onClick", function (id) {
                //var pId = listAttendanceToolbar.getParentId(id);
                if (id == 'att_btn') {
                    //console.log(id);
                    var date = listAttendanceToolbar.getValue("att_date");
                    var params = "ST_Date=" + date;
                    AttendGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listAttendanceUsers.php&" + params), function () {
                        $('.Att_count').html("Total:" + AttendGrid.getRowsNum());
                        $('.att_rpt_mark').html("Marked: " + AttendGrid.getUserData("", "att_marked") + " ");
                        $('.att_rpt_mark').attr("count", AttendGrid.getUserData("", "att_marked"));
                        $('.att_rpt_unmark').html("Unmarked: " + AttendGrid.getUserData("", "att_unmarked") + " ");
                        $('.att_rpt_unmark').attr("count", AttendGrid.getUserData("", "att_unmarked"));
                        $('.att_rpt_signout').html("Sign Out: " + AttendGrid.getUserData("", "att_signout") + " ");
                        $('.att_rpt_signout').attr("count", AttendGrid.getUserData("", "att_signout"));
                        $('.att_rpt_norm').html("Signed In : " + AttendGrid.getUserData("", "att_signedin") + " ");
                        $('.att_rpt_norm').attr("count", AttendGrid.getUserData("", "att_signedin"));
                        $('.att_rpt_late').html("Late SignIn: " + AttendGrid.getUserData("", "att_latein") + " ");
                        $('.att_rpt_late').attr("count", AttendGrid.getUserData("", "att_latein"));
                        $('.att_lvapplied').html("Leave Applied : " + AttendGrid.getUserData("", "att_lvapplied") + " ");
                        $('.att_lvapplied').attr("count", AttendGrid.getUserData("", "att_lvapplied"));
                        $('.att_lvrptd').html("Leave Reported : " + AttendGrid.getUserData("", "att_lvrptd") + " ");
                        $('.att_lvrptd').attr("count", AttendGrid.getUserData("", "att_lvrptd"));
                        $('.att_invalid').html("Not Signed Out : " + AttendGrid.getUserData("", "att_invalid") + " ");
                        $('.att_invalid').attr("count", AttendGrid.getUserData("", "att_invalid"));
                        $('.att_ntmarked').html("Not Marked : " + AttendGrid.getUserData("", "att_ntmarked") + " ");
                        $('.att_ntmarked').attr("count", AttendGrid.getUserData("", "att_ntmarked"));
                        $('.att_erlysout').html("Early Sign Out : " + AttendGrid.getUserData("", "att_earlyout") + " ");
                        $('.att_erlysout').attr("count", AttendGrid.getUserData("", "att_earlyout"));
                        $('.att_propsout').html("Proper Sign Out : " + AttendGrid.getUserData("", "att_propout") + " ");
                        $('.att_propsout').attr("count", AttendGrid.getUserData("", "att_propout"));
                        $('.att_latesout').html("Late Sign Out : " + AttendGrid.getUserData("", "att_lateout") + " ");
                        $('.att_latesout').attr("count", AttendGrid.getUserData("", "att_lateout"));
                        $('.btn_Sort_Datt').click(function () {
                            var colId = $(this).attr("colNum");
                            var type = "str"
                            if (colId == 0)
                                type = "int"
                            if (srtFlg == 0) {
                                $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                                AttendGrid.sortRows(colId, type, "asc");
                                srtFlg = 1;
                            } else {
                                $(this).attr("src", 'images/icon/sort-descending-icon.png');
                                AttendGrid.sortRows(colId, type, "des");
                                srtFlg = 0;
                            }
                        });
                    });
                }
            });
            AttendGrid = listAttendanceLayout.cells("a").attachGrid();
            AttendGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, dhxLayout, null);
            });
            AttendGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                /*AttendGrid.forEachRow(function(rid){
                 if(AttendGrid.getUserData(rid,"attndFlag")==1)
                 AttendGrid.setRowColor(rid,"#ceecb1");
                 });*/
            });
            AttendGrid.attachEvent("onFilterEnd", function () {
                var rowID = 0;
                var i;
                for (i = 0; i < AttendGrid.getRowsNum(); i++) {
                    rowID = AttendGrid.getRowId(i);
                    AttendGrid.cells(rowID, 0).setValue(i + 1);
                }
                ;
            });
            AttendGrid.enableAutoWidth(true);
            AttendGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
            AttendGrid.enableTooltips("false,false,false,false,false,false,false,true");
            AttendGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendanceUsers.php"), function () {
                $('.Att_count').html("Total:" + AttendGrid.getRowsNum());
                $('.att_rpt_mark').html("Marked: " + AttendGrid.getUserData("", "att_marked") + " ");
                $('.att_rpt_mark').attr("count", AttendGrid.getUserData("", "att_marked"));
                $('.att_rpt_unmark').html("Unmarked: " + AttendGrid.getUserData("", "att_unmarked") + " ");
                $('.att_rpt_unmark').attr("count", AttendGrid.getUserData("", "att_unmarked"));
                $('.att_rpt_signout').html("Sign Out: " + AttendGrid.getUserData("", "att_signout") + " ");
                $('.att_rpt_signout').attr("count", AttendGrid.getUserData("", "att_signout"));
                $('.att_rpt_norm').html("Signed In : " + AttendGrid.getUserData("", "att_signedin") + " ");
                $('.att_rpt_norm').attr("count", AttendGrid.getUserData("", "att_signedin"));
                $('.att_rpt_late').html("Late SignIn: " + AttendGrid.getUserData("", "att_latein") + " ");
                $('.att_rpt_late').attr("count", AttendGrid.getUserData("", "att_latein"));
                $('.att_lvapplied').html("Leave Applied : " + AttendGrid.getUserData("", "att_lvapplied") + " ");
                $('.att_lvapplied').attr("count", AttendGrid.getUserData("", "att_lvapplied"));
                $('.att_lvrptd').html("Leave Reported : " + AttendGrid.getUserData("", "att_lvrptd") + " ");
                $('.att_lvrptd').attr("count", AttendGrid.getUserData("", "att_lvrptd"));
                $('.att_invalid').html("Not Signed Out : " + AttendGrid.getUserData("", "att_invalid") + " ");
                $('.att_invalid').attr("count", AttendGrid.getUserData("", "att_invalid"));
                $('.att_ntmarked').html("Not Marked : " + AttendGrid.getUserData("", "att_ntmarked") + " ");
                $('.att_ntmarked').attr("count", AttendGrid.getUserData("", "att_ntmarked"));
                $('.att_erlysout').html("Early Sign Out : " + AttendGrid.getUserData("", "att_earlyout") + " ");
                $('.att_erlysout').attr("count", AttendGrid.getUserData("", "att_earlyout"));
                $('.att_propsout').html("Proper Sign Out : " + AttendGrid.getUserData("", "att_propout") + " ");
                $('.att_propsout').attr("count", AttendGrid.getUserData("", "att_propout"));
                $('.att_latesout').html("Late Sign Out : " + AttendGrid.getUserData("", "att_lateout") + " ");
                $('.att_latesout').attr("count", AttendGrid.getUserData("", "att_lateout"));
                $('.btn_Sort_Datt').click(function () {
                    var colId = $(this).attr("colNum");
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        AttendGrid.sortRows(colId, "str", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        AttendGrid.sortRows(colId, "str", "des");
                        srtFlg = 0;
                    }
                });
            });
            AttendGrid.attachEvent("onFilterEnd", function (elements) {
                $('.Att_count').html("Total:" + AttendGrid.getRowsNum());
                if (AttendGrid.getRowsNum() == 0) {
                    AttendGrid.addRow(0, ['', '', 'No Records Found...', '', '', '', ''], 0);
                    AttendGrid.setRowTextStyle(0, "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
                } else {
//                         ApproveLeaveGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function() {});
                }

            });
        },
        applyRptAttFilter: function () {
            var filtrInterval;
            col1img = 'ascending';
            if (attSortdRow == 1 && attsrtFlg == 0)
                col1img = 'ascending';
            else if (attSortdRow == 1 && attsrtFlg == 1)
                col1img = 'descending';
            col2img = 'ascending';
            if (attSortdRow == 2 && attsrtFlg == 0)
                col2img = 'ascending';
            else if (attSortdRow == 2 && attsrtFlg == 1)
                col2img = 'descending';
            AttReportsGrid.setColLabel(1, '<input id="attRptNameFiltr" class="mnthatt_txt_filter" type="text" placeholder="Enter a name  to search . . ." style="width: 80%;"><img src="images/icon/sort-' + col1img + '-icon.png" title="Click here to Sort" colNum="1" class="btn_MnthlyAttSort" />');
            AttReportsGrid.setColLabel(2, '<div id="attRptBranchFiltr" class="mnthatt_filter" style="width: 90%;"><img src="images/icon/sort-' + col2img + '-icon.png" title="Click here to Sort" colNum="2" class="btn_MnthlyAttSort" style="float:right;" /></div>');
            AttFiltrcombo = new dhtmlXCombo("attRptBranchFiltr", "FiltCombo", "100%");
            AttFiltrcombo.setOptionWidth(150);
            var selectedLocation = '&filter=all&seltdQffz=0';
            if (ofceFilter)
                selectedLocation = '&filter=all&seltdQffz=' + ofceFilter;
            AttFiltrcombo.load(preTally.Initialize.encryptURL("requisites/locations.php" + selectedLocation));
            AttFiltrcombo.setFilterHandler(function (mask, option) {
                var r = false;
                if (mask.length == 0) {
                    r = true;
                } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                    r = true;
                }
                return r;
            });
            AttFiltrcombo.attachEvent("onChange", function () { // Filter for Office Location Change
                preTally.UserProfile.applyAttFilter(2, 'asc');
            });
            $(".btn_MnthlyAttSort").click(function () {
                var ColNum = $(this).attr("colNum");
                attSortdRow = ColNum;
                var colimg;
                if (attsrtFlg == 0) {
                    $(this).attr("src", 'images/icon/sort-descending-icon.png');
                    attord = 'asc';
                    attsrtFlg = 1;
                } else {
                    $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                    attord = 'des';
                    attsrtFlg = 0;
                }
                preTally.UserProfile.applyAttFilter(ColNum, attord);
            });
            $(".mnthatt_txt_filter").keyup(function () {
                if (filtrInterval)
                    clearInterval(filtrInterval);
                filtrInterval = setInterval(function () {
                    preTally.UserProfile.applyAttFilter(2, 'asc');
                    clearInterval(filtrInterval);
                }, 500);
            });
        },
        reportUserAttendance: function ()
        {
            var Months_Options = [];
            var Year_Options = [];
            var mnthLimit;
            var monthNames = [
                "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
            ];
            if (mnthlyatt_sel_year < Date.today().getFullYear()) {
                mnthLimit = 12;
            } else {
                mnthLimit = Date.today().getMonth() + 1
                mnthLimit = 12;
            }
            for (i = mnthLimit; i >= 1; i--) {
                j = i;
                if (j < 10) {
                    j = '0' + i;
                }
                Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
            }
            for (j = Date.today().getFullYear(); j >= 2014; j--) {
                Year_Options.push(['y' + j, 'obj', j, "calendar_M.png"]);
            }
            var month = Date.today().getMonth();
            mnthlyatt_sel_month = month + 1;
            mnthlyatt_sel_year = Date.today().getFullYear();
            RptAttendanceLayout = AttTabbar.tabs("a2").attachLayout('1C');
            RptAttendanceToolbar = RptAttendanceLayout.cells("a").attachToolbar();
            RptAttendanceLayout.cells("a").hideHeader();
            RptAttendanceToolbar.setIconsPath("images/icon/default_18/");
            RptAttendanceToolbar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, 'calendar_M.png', '', true, true, 10, 'select');
            RptAttendanceToolbar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, 'calendar_Y.png', '', true, true, 10, 'select');
            RptAttendanceToolbar.setItemText('att_month_filter', monthNames[Date.today().getMonth()]);
            RptAttendanceToolbar.setItemText('att_year_filter', Date.today().getFullYear());
            RptAttendanceToolbar.addText("spacer", '2', "");
            RptAttendanceToolbar.disableItem("spacer");
            RptAttendanceToolbar.setWidth("spacer", 100);
            RptAttendanceToolbar.addButton("payrollReport", '3', 'Generate Payroll Report', 'other.gif');
            RptAttendanceToolbar.attachEvent("onClick", function (id) {
                currentDate = new Date();
                var prev_month = mnthlyatt_sel_month;
                var prev_year = mnthlyatt_sel_year;
                var pId = RptAttendanceToolbar.getParentId(id);
                if (pId == 'att_month_filter') {
                    nameFilter = "";
                    mnthlyatt_sel_month = id.substr(1);
                    if (RptAttendanceToolbar.getListOptionSelected("att_year_filter")) {
                        mnthlyatt_sel_year = RptAttendanceToolbar.getListOptionSelected("att_year_filter");
                        mnthlyatt_sel_year = mnthlyatt_sel_year.substr(1);
                    }
                    var params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
                    if (mnthlyatt_sel_month > Date.today().getMonth() + 1 && mnthlyatt_sel_year == Date.today().getFullYear())
                    {
                        dhtmlx.message("Cant go above Current Month");
                        var CellDate = AttReportsGrid.getUserData("", "cellDate");
                        var mntArr = CellDate.split("-");
                        RptAttendanceToolbar.setListOptionSelected('att_month_filter', mntArr[1]);
                        RptAttendanceToolbar.setItemText('att_month_filter', monthNames[mntArr[1] - 1]);
                        RptAttendanceToolbar.setListOptionSelected('att_year_filter', mntArr[0]);
                        RptAttendanceToolbar.setItemText('att_year_filter', mntArr[0]);
                        mnthlyatt_sel_month = mntArr[1];
                        mnthlyatt_sel_year = mntArr[0];
                    } else {
                        AttReportsGrid.destructor();
                        AttReportsGrid = RptAttendanceLayout.cells("a").attachGrid();
                        AttReportsGrid.enableColSpan(true);
                        AttReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                        AttReportsGrid.enablePaging(true, 50, 5, "pagingbox", true);
                        AttReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                        AttReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                        AttReportsGrid.attachEvent("onXLS", function () {
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            //console.log('Call Requested');
                        });
                        AttReportsGrid.attachEvent("onXLE", function () {
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            //console.log('Call Completed');
                            preTally.UserProfile.reportUserAttendanceFunction(true);
                            if (nameFilter != '')
                                $('#attRptNameFiltr').val(nameFilter);
                        });
                        AttReportsGrid.init();
                        ofceFilter = 0;
                        var params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
                        AttReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listRptAttendance.php&" + params), function () {
                        }, "json");
                    }

                }
                if (pId == 'att_year_filter') {
                    nameFilter = "";
                    mnthlyatt_sel_year = id.substr(1);
                    if (RptAttendanceToolbar.getListOptionSelected("att_month_filter")) {
                        mnthlyatt_sel_month = RptAttendanceToolbar.getListOptionSelected("att_month_filter");
                        mnthlyatt_sel_month = mnthlyatt_sel_month.substr(1);
                    }
                    //var params  = "att_month=" + mnthlyatt_sel_month + "&att_year="+mnthlyatt_sel_year ;                    
                    if (mnthlyatt_sel_month > Date.today().getMonth() + 1 && mnthlyatt_sel_year == Date.today().getFullYear())
                    {
                        dhtmlx.message("Cant go beyond Current Month");
                        var CellDate = AttReportsGrid.getUserData("", "cellDate");
                        var mntArr = CellDate.split("-");
                        RptAttendanceToolbar.setListOptionSelected('att_month_filter', mntArr[1]);
                        RptAttendanceToolbar.setItemText('att_month_filter', monthNames[mntArr[1] - 1]);
                        RptAttendanceToolbar.setListOptionSelected('att_year_filter', mntArr[0]);
                        RptAttendanceToolbar.setItemText('att_year_filter', mntArr[0]);
                        mnthlyatt_sel_month = mntArr[1];
                        mnthlyatt_sel_year = mntArr[0];
                    } else {
                        AttReportsGrid.destructor();
                        AttReportsGrid = RptAttendanceLayout.cells("a").attachGrid();
                        AttReportsGrid.enableColSpan(true);
                        AttReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                        AttReportsGrid.enablePaging(true, 50, 5, "pagingbox", true);
                        AttReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
                        AttReportsGrid.setImagePath("assets/grid/codebase/imgs/");
                        AttReportsGrid.attachEvent("onXLS", function () {
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            //console.log('Call Requested');
                        });
                        AttReportsGrid.attachEvent("onXLE", function () {
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            //console.log('Call Completed');
                            preTally.UserProfile.reportUserAttendanceFunction(true);
                            if (nameFilter != '')
                                $('#attRptNameFiltr').val(nameFilter);
                        });
                        AttReportsGrid.init();
                        ofceFilter = 0;
                        var params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
                        AttReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listRptAttendance.php&" + params), function () {
                        }, "json");
                    }
                }
                if (id == "payrollReport") {
                    var params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
                    $.post(preTally.Initialize.encryptURL("warehouse/generatePayrollReport.php&" + params), function (data, status) {

                        if (data == "1") {
                            AttReportsGrid.enableEditEvents(false, false, false);
                            attFlag = 2;
                            dhtmlx.message({text: "Payroll Successfully Generated"});
                        } else if (data == "2") {
                            dhtmlx.message({text: "Payroll already exist"});
                        } else if (data == "3") {
                            dhtmlx.message({text: "Cant generate Current month's Payroll Slip"});
                        } else {
                            var Datastatus = jQuery.parseJSON(data);
                            if (Datastatus.Status == "salary_negative") {
                                dhtmlx.message({text: "Salary Negative error"});
                                preTally.UserProfile.showNegateSalUsers(Datastatus)
                            } else {
                                preTally.UserProfile.showModalBox(data, 0);
                            }

                        }
                    });
                }
            });
            AttReportsGrid = RptAttendanceLayout.cells("a").attachGrid();
            AttReportsGrid.enableColSpan(true);
            AttReportsGrid.setImagePath("assets/grid/codebase/imgs/");
            /*AttReportsGrid.attachEvent("onFilterEnd", function(elements){
             if(AttReportsGrid.getRowsNum() == 0 ){
             AttReportsGrid.addRow("no_records",['No Records Found...'],1); 
             AttReportsGrid.setColspan("no_records",0,2);  
             AttReportsGrid.setRowTextStyle("no_records", "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
             }else{
             AttReportsGrid.deleteRow(0);
             }                         
             }); */


            AttReportsGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, dhxLayout, null);
                //console.log('Call Requested');
            });
            AttReportsGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                //console.log('Call Completed');
                preTally.UserProfile.reportUserAttendanceFunction(true);
                if (nameFilter != '')
                    $('#attRptNameFiltr').val(nameFilter);
            });
            RptAttendanceLayout.cells("a").attachStatusBar({
                text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;padding-left:50px;font-size:14px;'><font color='green'>P-Present</font>&nbsp;&nbsp;&nbsp;<font color='orange'>P2-Halfday</font>&nbsp;&nbsp;&nbsp;<font color='#8258FA'>H-Holiday</font>&nbsp;&nbsp;&nbsp;<font color='#8258FA'>W-Weekend off</font>&nbsp;&nbsp;&nbsp;<font color='#8258FA'>S-Sunday off</font>&nbsp;&nbsp;&nbsp;<font color='red'>L-Leave</font></div>\
                            </div><div style='float:right;' id='pagingbox'></div>", // status bar text
                height: 35                              // custom height
            });
            AttReportsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
            AttReportsGrid.enablePaging(true, 50, 5, "pagingbox", true);
            AttReportsGrid.setPagingSkin("toolbar", "dhx_skyblue");
            AttReportsGrid.enableTooltips("false,false,false,false,false,false,false,true");
            AttReportsGrid.init();
            nameFilter = '';
            ofceFilter = '';
            //AttReportsGrid.loadXML(preTally.Initialize.encryptURL("requisites/listRptAttendance.php"),function(){  
            // First Time Loads            
            AttReportsGrid.load(preTally.Initialize.encryptURL("requisites/listRptAttendance.php"), function () {

                //AttReportsGrid.splitAt(3); 
                //console.log('Call Initiated');
                //preTally.UserProfile.reportUserAttendanceFunction(true);
            }, "json");
            //preTally.Settings.progressOff(true, dhxLayout, null);

        },
        applyAttFilter: function (sortFilter, ord) {// Change Office

            var params = '';
            /*var filterMonth = RptAttendanceToolbar.getListOptionSelected('att_month_filter');     
             if(filterMonth)*/ params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
            nameFilter = $('#attRptNameFiltr').val();
            ofceFilter = AttFiltrcombo.getSelectedValue();
            var filterValue = new Array(nameFilter, ofceFilter, sortFilter, ord);
            var filtr = params + "&Filters=" + filterValue + "&calltype=upd";
            AttReportsGrid.enableTooltips("false,false,false,false,false,false,false,true");
            AttReportsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listRptAttendance.php&" + filtr), function () {
                //AttReportsGrid.splitAt(3);   
                //preTally.UserProfile.reportUserAttendanceFunction(false);                
                //AttReportsGrid.setColSpan("null_row",1,10) ;
                if (AttReportsGrid.getRowsNum() == 0) {
                    AttReportsGrid.addRow("null_row", '<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>');
                    var colNum = AttReportsGrid.getColumnsNum();
                    AttReportsGrid.setColspan("null_row", 0, colNum);
                    AttReportsGrid.setRowExcellType("null_row", "ro");
                }
                //$('#attRptNameFiltr').focus();                            
                $('#attRptNameFiltr').focus().val('').val(nameFilter);
            }, "json");
        },
        reportUserAttendanceFunction: function (drawFilter) {
            currentDate = new Date();
            /*var nowMonth ; var nowYear ;
             if(!isNaN(month)){                
             nowMonth =month; 
             }   else{
             nowMonth = parseInt(currentDate.getMonth())+1;
             } 
             if(!isNaN(year)){                
             nowYear =year; 
             }   else{
             nowYear = currentDate.getFullYear();
             }*/

            if (drawFilter == true) {
                preTally.UserProfile.applyRptAttFilter();
                preTally.UserProfile.intializeRptAttEdit();
            }

            var columnCount = AttReportsGrid.getColumnsNum();
            var leaveCount = AttReportsGrid.getUserData("", "leaveCount");
            var totalDays = AttReportsGrid.getUserData("", "totalDays");
            AttReportsGrid.forEachRow(function (id) {
                for (var m = 3; m <= (parseInt(totalDays) + 3); m++) {
                    var cellValue = AttReportsGrid.cells(id, m).getValue();
                    var cellColor = 'red';
                    if (cellValue == 'P') {
                        cellColor = 'green';
                    } else if (cellValue == 'P2') {
                        cellColor = 'orange';
                    } else if (cellValue == 'L') {
                        cellColor = 'red';
                    } else if (cellValue == 'H' || cellValue == 'W' || cellValue == 'S') {
                        cellColor = '#8258FA';
                    }
                    AttReportsGrid.setCellTextStyle(id, m, "color:" + cellColor + ";");
                }
                for (var m = leaveCount; m >= 0; m--) {
                    //AttReportsGrid.setCellTextStyle(id,(columnCount - (m+2)),"color:green;");
                }
                AttReportsGrid.setCellTextStyle(id, (columnCount - ((parseInt(leaveCount)) + 5)), "color:green;");
                AttReportsGrid.setCellTextStyle(id, (columnCount - ((parseInt(leaveCount)) + 4)), "color:orange;");
                AttReportsGrid.setCellTextStyle(id, (columnCount - ((parseInt(leaveCount)) + 3)), "color:red;");
                AttReportsGrid.setCellTextStyle(id, (columnCount - 2), "color:red;");
            });
        },
        reportShowAttendance: function (id) {
            var selectedUserId = id;
            var userAttendanceWin = new dhtmlXWindows();
            var userAttWin = userAttendanceWin.createWindow("Attendance", 600, 200, 800, 550);
            userAttWin.button("minmax1").hide();
            userAttWin.button("minmax2").hide();
            userAttWin.button("park").hide();
            userAttWin.center();
            userAttWin.setModal(true);
            userAttWin.setText("Attendance");
            var CellDate = AttReportsGrid.getUserData("", "cellDate");
            var Info = CellDate.split("-");
            var yearInfo = Info[0];
            var mInfo = Info[1];
            var monthInfo = mInfo - 01;
            if (monthInfo < 10) {
                var monthInfoNew = parseInt(monthInfo, 10);
            } else {
                var monthInfoNew = monthInfo;
            }
            var Months_Options = [];
            var Year_Options = [];
            var sel_month;
            var month = Date.today().getMonth();
            var monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            var mnthLimit;
            if (myattendance_year < Date.today().getFullYear()) {
                mnthLimit = 12;
            } else {
                //mnthLimit=Date.today().getMonth()+1
                mnthLimit = 12;
            }
            for (i = mnthLimit; i >= 1; i--) {
                j = i;
                if (j < 10) {
                    j = '0' + i;
                }
                Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
            }
            for (j = Date.today().getFullYear(); j >= 2014; j--) {
                Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
            }
            var userAttendancetab = userAttWin.attachTabbar();
            userAttendancetab.addTab("AttendanceSheet", "Attendance", 130);
            userAttendancetab.tabs("AttendanceSheet").setActive();
            userAttendanceLayout = userAttendancetab.tabs("AttendanceSheet").attachLayout("1C");
            myuserattToolBar = userAttendancetab.tabs("AttendanceSheet").attachToolbar();
            myuserattToolBar.setIconsPath("images/icon/default_18/");
            myuserattToolBar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
            myuserattToolBar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
            myuserattToolBar.setItemText('att_month_filter', monthNames[monthInfoNew]);
            myuserattToolBar.setItemText('att_year_filter', yearInfo);
            myuserattToolBar.addText("spacer", '2', "");
            myuserattToolBar.disableItem("spacer");
            myuserattToolBar.setWidth("spacer", 600);
            // myattendance_year=Date.today().getFullYear();
            //myattendance_month=Date.today().getMonth()+1;
            myattendance_year = yearInfo;
            myattendance_month = mInfo;
            myuserattToolBar.attachEvent("onClick", function (id) {
                var pId = myuserattToolBar.getParentId(id);
                if (pId == 'att_month_filter') {
                    myattendance_month = id.substr(1);
                    if (myuserattToolBar.getListOptionSelected("att_year_filter")) {
                        myattendance_year = myuserattToolBar.getListOptionSelected("att_year_filter");
                        myattendance_year = myattendance_year.substr(1);
                    }
                    var firstDay = new Date(myattendance_year, myattendance_month - 1, 1).toString("yyyy-MM-dd");
                    var lastDay = new Date(myattendance_year, myattendance_month, 0).toString("yyyy-MM-dd");
                    var params = "ST_Date=" + firstDay + "&LST_Date=" + lastDay + "&US_Id=" + selectedUserId;
                    userAttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listUsersAttendance.php&" + params), function () {
                        $('.tb_f_days').html("Full Day: " + userAttGrid.getUserData("", "full_days") + " ");
                        $('.tb_h_days').html("Half Day: " + userAttGrid.getUserData("", "half_days") + " ");
                        $('.tb_o_days').html("Off Day : " + userAttGrid.getUserData("", "off_days") + " ");
                        $('.tb_l_days').html("Leaves : " + userAttGrid.getUserData("", "leave_days") + " ");
                        userAttGrid.attachEvent("onFilterEnd", function (elements) {
                            if (userAttGrid.getRowsNum() == 0) {
                                preTally.Functions.recordNotFound(userAttGrid, 6);
                            }
                        });
                    });
                }
                if (pId == 'att_year_filter') {
                    myattendance_year = id.substr(1);
                    if (myuserattToolBar.getListOptionSelected("att_month_filter")) {
                        myattendance_month = myuserattToolBar.getListOptionSelected("att_month_filter");
                        myattendance_month = myattendance_month.substr(1);
                    }
                    var firstDay = new Date(myattendance_year, myattendance_month - 1, 1).toString("yyyy-MM-dd");
                    var lastDay = new Date(myattendance_year, myattendance_month, 0).toString("yyyy-MM-dd");
                    var params = "ST_Date=" + firstDay + "&LST_Date=" + lastDay + "&US_Id=" + selectedUserId;
                    userAttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listUsersAttendance.php&" + params), function () {
                        $('.tb_f_days').html("Full Day: " + userAttGrid.getUserData("", "full_days") + " ");
                        $('.tb_h_days').html("Half Day: " + userAttGrid.getUserData("", "half_days") + " ");
                        $('.tb_o_days').html("Off Day : " + userAttGrid.getUserData("", "off_days") + " ");
                        $('.tb_l_days').html("Leaves : " + userAttGrid.getUserData("", "leave_days") + " ");
                        userAttGrid.attachEvent("onFilterEnd", function (elements) {
                            if (userAttGrid.getRowsNum() == 0) {
                                preTally.Functions.recordNotFound(userAttGrid, 6);
                            }
                        });
                    });
                }

            });
            userAttendanceLayout.cells("a").hideHeader();
            userAttGrid = userAttendanceLayout.cells("a").attachGrid();
            userAttGrid.enableCollSpan(true);
            userAttGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, userAttendanceLayout, null);
            });
            userAttGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, userAttendanceLayout, null);
            });
            userAttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listUsersAttendance.php&US_Id=" + selectedUserId + "&celldate=" + CellDate), function () {
                userAttGrid.makeFilter("ATStatus", 5);
                userAttGrid.enableTooltips("false,false,false,false,false,false,false");
                userAttGrid.attachEvent("onFilterEnd", function (elements) {

                    if (userAttGrid.getRowsNum() == 0) {
                        preTally.Functions.recordNotFound(userAttGrid, 6);
                    }
                });
            });
        },
        showModalBox: function (data, stat) {
            var Datastatus;
            if (stat == 0) {
                Datastatus = jQuery.parseJSON(data);
                userDetails = Datastatus;
            } else {
                Datastatus = userDetails;
            }
            box = dhtmlx.modalbox({
                title: "User Information",
                text: "<img src='images/icon/user.gif'><strong>salary structure not set for " + Datastatus.count + " User(s)<br><a   href='#'  onclick=' preTally.UserProfile.showNoSalStructUser();'> Show user details?</a></strong><br/><br/>still you want to generate payroll Report.",
                buttons: ["Yes", "No"],
                callback: function (index) {
                    var params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year + "&datas=" + 1;
                    if (index == 0) {
                        $.post(preTally.Initialize.encryptURL("warehouse/generatePayrollReport.php&" + params), function (data, status) {

                            if (data == "1") {
                                dhtmlx.message({text: "Payroll Successfully Generated"});
                                attFlag = 2;
                                AttReportsGrid.enableEditEvents(false, false, false);
                            } else if (data == "2") {
                                dhtmlx.message({text: "Payroll already exist"});
                            } else {
                                var Datastatus = jQuery.parseJSON(data);
                                dhtmlx.message({text: "Salary Negative error"});
                                if (Datastatus.Status == "salary_negative") {
                                    preTally.UserProfile.showNegateSalUsers(Datastatus);
                                }
                            }
                        });
                    } else if (index == 1) {

                    }

                }

            });
        },
        showNoSalStructUser: function () {
            //var evnt=arguments.callee.caller.arguments[0];
            //evnt.preventDefault();                                
            dhtmlx.modalbox.hide(box);
            var details = '';
            for (var i = 1; i < (Object.keys(userDetails).length - 2); i++) {
                details += userDetails[i] + "<br>";
            }
            var userDetailsWin = new dhtmlXWindows();
            var userSalWin = userDetailsWin.createWindow("UserDetails", 500, 100, 400, 450);
            userSalWin.button("minmax").hide();
            userSalWin.button("park").hide();
            userSalWin.setText("User list");
            userSalWin.attachEvent("onClose", function (win) {
                $('.dhxwin_brd').removeClass('windowNoBorder');
                preTally.UserProfile.showModalBox(userDetails, 1);
                return true;
            });
            var formData = [
                {type: "settings", position: "label-left", labelWidth: 0, inputWidth: 480},
                {type: "template", name: "user", labelHeight: 50, label: ":", value: "<b>" + details + "</b>"},
            ];
            var userDetalsForm = userSalWin.attachForm();
            userDetalsForm.loadStruct(formData);
        },
        showNegateSalUsers: function (data) {
            var details = '';
            for (var i = 0; i < (Object.keys(data).length - 1); i++) {
                details += data[i] + "<br>";
            }
            var NgSaluserDetailsWin = new dhtmlXWindows();
            var userNgSalWin = NgSaluserDetailsWin.createWindow("UserDetails", 500, 100, 400, 450);
            userNgSalWin.button("minmax").hide();
            userNgSalWin.button("park").hide();
            userNgSalWin.setText("Salary Negative-User list");
            var formData = [
                {type: "settings", position: "label-left", labelWidth: 0, inputWidth: 480},
                {type: "template", name: "user", labelHeight: 50, label: ":", value: "<b>" + details + "</b>"},
            ];
            var userNgSalForm = userNgSalWin.attachForm();
            userNgSalForm.loadStruct(formData);
        },
        intializeRptAttEdit: function () {
            currentDate = new Date();
            var nowMonth = currentDate.getMonth();
            AttReportsGrid.attachEvent("onMouseOver", function (id, ind) {
//                if (ind == 0)
                //var cellValue = AttReportsGrid.cells(id,ind).getValue();
//                /*var newTitle = 'null';
//                if(cellValue == 'P') newTitle = 'Present';
//                else if(cellValue == 'P2') newTitle = 'Half Day';
//                else if(cellValue == 'W') newTitle = 'Weekend Off';
//                else if(cellValue == 'S') newTitle = 'Sunday';
//                else if(cellValue == 'H') newTitle = 'Holiday';
//                else if(cellValue == 'L') newTitle = 'Leave';
//                else if(cellValue == 'LP2') newTitle = 'Half day Leave';*/
                var newTitle = '';
//                if(cellValue == 'P') newTitle = '';
//                else if(cellValue == 'P2') newTitle = '';
//                else if(cellValue == 'W') newTitle = '';
//                else if(cellValue == 'S') newTitle = '';
//                else if(cellValue == 'H') newTitle = '';
//                else if(cellValue == 'L') newTitle = '';
//                else if(cellValue == 'LP2') newTitle = '';
                /*if(cellValue == 'O' || cellValue == 'L' || cellValue == 'CL') {*/
                /* else{    var cellTypeLabel = AttReportsGrid.getColLabel(ind);
                 var getOffType   = AttReportsGrid.getUserData(id,"offType");                    
                 if(getOffType != 'null') {
                 var offTypeJSON  = jQuery.parseJSON( getOffType );
                 newTitle = offTypeJSON[cellTypeLabel] ? offTypeJSON[cellTypeLabel] :'';
                 } 
                 }*/
                /*}*/
                this.cells(id, ind).cell.title = newTitle;
            });
            var isEditable = AttReportsGrid.getUserData("", "isEditable");
            var ACLedit = unescape(JGG1P3bDnUSDL14Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            if (unescape(JGG1P3bDnUSDL15Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1 || unescape(JGG1P3bDnUSDL16Mui7KzYjj28UjPdWxCCtGkJSHeuo) == 1)
                PayrollBtn = 1;
            if (ACLedit == 0)
                RptAttendanceToolbar.hideItem('payrollReport');
            if (isEditable == 1 && ACLedit == 1) {
                AttReportsGrid.enableEditEvents(true, false, true);
            } else {
                AttReportsGrid.enableEditEvents(false, false, false);
            }

            AttReportsGrid.detachEvent(onRowSelectAttendance);
            onRowSelectAttendance = AttReportsGrid.attachEvent("onRowSelect", function (id, ind) {
                if (isEditable == 0) {
                    dhtmlx.message({text: "Can't Edit..Payroll Already Generated"});
                }
            });
            AttReportsGrid.detachEvent(onEditCellAttendance);
            onEditCellAttendance = AttReportsGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                //0-before start; can be canceled if return false,
                //1 - the editor is opened,
                //2- the editor is closed
                var ltype;
                if (stage == 0) {
                    LeaveEdtCombo = AttReportsGrid.cells(rId, cInd).getCellCombo();
                    LeaveEdtCombo.setOptionWidth(150);
                    LeaveEdtCombo.attachEvent("onClose", function () {
                        ltype = LeaveEdtCombo.getSelectedValue();
                        if (ltype == "Pre") {
                            AttReportsGrid.cells(rId, cInd).setValue("P");
                            AttReportsGrid.cells(rId, cInd).setTextColor('green');
                        } else if (ltype == "Hlf") {
                            AttReportsGrid.cells(rId, cInd).setValue("Hlf");
                            AttReportsGrid.cells(rId, cInd).setTextColor('orange');
                        } else if (ltype == "Abs") {
                            AttReportsGrid.cells(rId, cInd).setValue("L");
                            AttReportsGrid.cells(rId, cInd).setTextColor('red');
                        } else if (ltype == "RH") {
                            AttReportsGrid.cells(rId, cInd).setValue("RH");
                            AttReportsGrid.cells(rId, cInd).setTextColor('red');
                        } else {
                            //AttReportsGrid.cells(rId,cInd).setValue("L")
                            AttReportsGrid.cells(rId, cInd).setTextColor('red');
                        }

                    });
                } else if (stage == 1) {
                    //var cellType = AttReportsGrid.cells(rId,cInd).getAttribute("cellType");



                    var getCellType = AttReportsGrid.getUserData(rId, "cellType");
                    var cellTypeJSON = jQuery.parseJSON(getCellType);
                    var cellTypeLabel = AttReportsGrid.getColLabel(cInd);
                    var cellType = cellTypeJSON[cellTypeLabel];
                    //console.log(cellType);

                    LeaveEdtCombo.clearAll();
                    //LeaveEdtCombo.load(preTally.Initialize.encryptURL("requisites/leavetypeCombo.php&cType="+cellType));

                    var leaveObjTypes = AttReportsGrid.getUserData("", "leaveTypes");
                    var leaveObj = jQuery.parseJSON(leaveObjTypes);
                    var myLeaveTypes = '';
                    jQuery.each(leaveObj, function (i, val) {
                        myLeaveTypes += '{value: "' + val['LT_Id'] + '_FL", text: "' + val['LT_Name'] + ' (Full)"},';
                        myLeaveTypes += '{value: "' + val['LT_Id'] + '_FN", text: "' + val['LT_Name'] + ' (Mrng)"},';
                        myLeaveTypes += '{value: "' + val['LT_Id'] + '_AN", text: "' + val['LT_Name'] + ' (Eve)"},';
                    });
                    var NRM_RH;
                    if (cellType == 'R') {
                        NRM_RH = '{value: "RH", text: "RH"},{value: "Abs", text: "Absent"}';
                    } else {
                        NRM_RH = '{value: "Abs", text: "Absent"}';
                    }

                    LeaveEdtCombo.load('{options:[' +
                            '{value: "Pre", text: "Present"},' +
                            '{value: "Hlf", text: "Half Day"},' +
                            myLeaveTypes +
                            NRM_RH +
                            ']}');
                    return true;
                } else if (stage == 2) {
                    /*if(nValue != oValue){*/

                    //console.log(cInd);
                    var dateLabel = AttReportsGrid.getColLabel(cInd);
                    var date = AttReportsGrid.getUserData("", "cellDate") + '-' + dateLabel;
                    var usid = AttReportsGrid.getUserData(rId, "userID");
                    var leaveType = nValue;
                    var cellValue = oValue;
                    var lvtype = LeaveEdtCombo.getSelectedValue();
                    console.log(lvtype);
                    //var cellType    = AttReportsGrid.cells(rId,cInd).getAttribute("cellType");
                    filterData = "&date=" + date + "&usid=" + usid + "&updType=" + leaveType + "&prevVal=" + cellValue + "&cmbval=" + lvtype;
                    //console.log(filterData);
                    $.post(preTally.Initialize.encryptURL("warehouse/updateAttReport.php" + filterData), function (data, status) {
                        //var currpage = AttReportsGrid.currentPage ;      
                        //var strt = 0                    
                        //var mnth = parseInt(id);             
                        //params= "att_month=" +mnth+"&calltype=upd";      
                        //AttFiltrcombo.selectOption(0,true);
                        //$("#attRptNameFiltr").val("");
                        var params = '';
                        /*var filterMonth = RptAttendanceToolbar.getListOptionSelected('att_month_filter');     
                         if(filterMonth)*/ params = "att_month=" + mnthlyatt_sel_month + "&att_year=" + mnthlyatt_sel_year;
                        var nameFilter = $('#attRptNameFiltr').val();
                        var ofceFilter = AttFiltrcombo.getSelectedValue();
                        var filterValue = new Array(nameFilter, ofceFilter);
                        var filtr = params + "&Filters=" + filterValue + "&calltype=upd";
                        //var params = 1;                           
                        window.dhx4.ajax.post(preTally.Initialize.encryptURL("requisites/listRptAttendance.php&" + filtr + "&calltype=upd&userFilter=" + usid), function (data, status) {


                            //var responseJSON  = jQuery.parseJSON( data.xmlDoc.responseText );
                            //var responseArray = jQuery.makeArray( data.xmlDoc.responseText );

                            AttReportsGrid.enableTooltips("false,false,false,false,false,false,false,true");
                            var columnCount = AttReportsGrid.getColumnsNum();
                            var leaveCount = AttReportsGrid.getUserData("", "leaveCount");
                            var responseArray = data.xmlDoc.responseText.split(",");
                            for (var j = 0; j < (columnCount - 3); j++) {
                                var cellValue = responseArray[(j + 6)].replace(/"/g, "");
                                var cellColor = 'black';
                                if (cellValue == 'P') {
                                    cellColor = 'green';
                                } else if (cellValue == 'P2') {
                                    cellColor = 'orange';
                                } else if (cellValue == 'L') {
                                    cellColor = 'red';
                                } else if (cellValue == 'H' || cellValue == 'W' || cellValue == 'S') {
                                    cellColor = '#8258FA';
                                }
                                AttReportsGrid.cellById(rId, (j + 3)).setTextColor(cellColor);
                                AttReportsGrid.cellById(rId, (j + 3)).setValue(cellValue);
                            }
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - (parseInt(leaveCount) + 5)), "color:green;");
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - (parseInt(leaveCount) + 4)), "color:orange;");
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - (parseInt(leaveCount) + 3)), "color:red;");
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - (parseInt(leaveCount) + 2)), "color:black;");
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - (parseInt(leaveCount) + 1)), "color:black;");
                            AttReportsGrid.setCellTextStyle(rId, (columnCount - 2), "color:red;");
                        });
                    });
                    /*}   */
                    return true;
                }

            });
        },
        ListEmpPayroll: function () {
            var ACLPayrollEdit = unescape(JGG1P3bDnUSDL16Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            empPayrollLayout = AttTabbar.tabs("a3").attachLayout('1C');
            empPayrollLayout.cells("a").hideHeader();
            var params = "att_month=" + "abc";
            empPayrollGrid = empPayrollLayout.cells("a").attachGrid();
            empPayrollLayout.cells("a").attachStatusBar({text: "<div id='payroll_paging'></div>",
                height: 23});
            //empPayrollGrid.setHeader("#,User Details,#cspan,Duration,#cspan,Salary Additions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Gross Salary,PF/ESI Sal,Salary Deductions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Salary Adjustments,#cspan,Take Home Salary");
            //empPayrollGrid.attachHeader("#,#text_filter_inc,#select_filter,Month,Year,Basic Salary,HRA Allowance,CCA Allowance,Conveyance Allowance,Education Allowance,Medical,Other Allowance,Gross Salary,PF/ESI Sal,EPF,ESI,LWF,LOP,Professional Tax,TDS,Salary Advance,Loan,Addition,Deduction,Take Home Salary");
            //empPayrollGrid.setInitWidths("30,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,100");
            //empPayrollGrid.setColTypes("ro,ro,ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ro,ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ro");
            empPayrollGrid.setSkin("dhx_skyblue");
            empPayrollGrid.setImagePath("assets/grid/codebase/imgs/");
            empPayrollGrid.setHeader("SlNo,User Details,#cspan,Gross Salary,Take Home Salary,Salary Additions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,PF/ESI Sal,Salary Deductions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Salary Adjustments,#cspan");
            empPayrollGrid.setInitWidths("30,120,120,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,100");
            if (ACLPayrollEdit == 1)
                empPayrollGrid.setColTypes("ro,ro,ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ro,ro,ro,ed,ed,ed,ed,ed,ed,ro,ro,ed,ed");
            else
                empPayrollGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ed,ed");
            empPayrollGrid.setColAlign("center,left,left,right,right,right,right,right,center,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right,right");
            empPayrollGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
            empPayrollGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
            empPayrollGrid.attachHeader(",<input type='text' id='PayrolRptName' class='PayrolTextFilter' placeholder='Enter Staff Name' style='width: 90%;'><div><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' ColName='NM' class='btn_EmpPaySort' /></div>,<input type='text' id='PayrolRptLoc' class='PayrolTextFilter' placeholder='Location' style='width: 90%;'><div><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' ColName='LC' class='btn_EmpPaySort' /></div>,<div>Gross Salary <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' ColName='GS' class='btn_EmpPaySort' /></div>,<div>Take Home Salary<input type='hidden' id='hid_PayrolSort' value='0'/><input type='hidden' id='hid_PayrolCol' value=''/><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' ColName='THS' class='btn_EmpPaySort' /></div>,Basic Salary,HRA Allowance,CCA Allowance,Conveyance Allowance,Education Allowance,Medical Allowance,Other Allowance,PF/ESI Sal,EPF,ESI,LWF,LOP,Professional Tax,Prof TDS,Sal TDS,Meal Card,Salary Advance,Loan,Addition,Deduction");
            empPayrollGrid.attachFooter(",,,<div  class='footer gross'></div>,<div class='footer ths'></div>,<div  class='footer basic'></div>,<div  class='footer hra'></div>,<div  class='footer cca'></div>,\
                                            <div  class='footer convey'></div>,<div  class='footer edu'></div>,<div  class='footer medic'></div>,<div  class='footer other'></div>,\
                                            <div  class='footer epfsal'></div>,<div  class='footer epf'></div>,<div  class='footer esi'></div>,\
                                            <div  class='footer lwf'></div>,<div  class='footer lop'></div>,<div  class='footer prftax'></div>,<div  class='footer proftds'></div>,<div  class='footer saltds'></div>,<div  class='footer mealcard'></div>,\
                                            <div  class='footer adv'></div>,<div  class='footer loan'></div>,<div  class='footer addtn'></div>,<div  class='footer dedctn'></div>");
            empPayrollGrid.enableColSpan(true);
            //empPayrollGrid.enableSmartRendering(true,50);  
            empPayrollGrid.init();
            empPayrollGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
            empPayrollGrid.enablePaging(true, 50, 5, "payroll_paging", true);
            empPayrollGrid.setPagingSkin("toolbar", "dhx_skyblue");
            //empPayrollGrid.splitAt(5);
            var filtrInterval;
            $(".PayrolTextFilter").keyup(function () {
                if (filtrInterval)
                    clearInterval(filtrInterval);
                filtrInterval = setInterval(function () {
                    preTally.UserProfile.applyPayrollFilter();
                    clearInterval(filtrInterval);
                }, 500);
            });
            empPayrollGrid.loadXML(preTally.Initialize.encryptURL("requisites/empPayrollGrid.php&" + params));
            var srtFlg = 0;
            $('.btn_EmpPaySort').click(function () {
                var ColName = $(this).attr("ColName")
                $('#hid_PayrolCol').val(ColName);
                if (srtFlg == 0) {
                    $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                    $('#hid_PayrolSort').val(1);
                    srtFlg = 1;
                } else {
                    $(this).attr("src", 'images/icon/sort-descending-icon.png');
                    $('#hid_PayrolSort').val(0);
                    srtFlg = 0;
                }
                preTally.UserProfile.applyPayrollFilter();
            });
            var Months_Options = [];
            var Year_Options = [];
            var monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            var mnthLimit;
            if (payroll_sel_year < Date.today().getFullYear()) {
                mnthLimit = 12;
            } else {
                //mnthLimit=Date.today().getMonth()+1
                mnthLimit = 12;
            }
            for (i = mnthLimit; i >= 1; i--) {
                j = i;
                if (j < 10) {
                    j = '0' + i;
                }
                Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
            }
            for (j = Date.today().getFullYear(); j >= 2014; j--) {
                Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
            }
            var month = Date.today().getMonth();
            empPayrollToolbar = empPayrollLayout.cells("a").attachToolbar();
            empPayrollToolbar.setIconsPath("images/icon/default_18/");
            empPayrollToolbar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, 'calendar_M.png', '', true, true, 10, 'select');
            empPayrollToolbar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, 'calendar_Y.png', '', true, true, 10, 'select');
            empPayrollToolbar.setItemText('att_month_filter', monthNames[month]);
            empPayrollToolbar.setItemText('att_year_filter', Date.today().getFullYear());
            empPayrollToolbar.addText("spacer", '2', "");
            empPayrollToolbar.disableItem("spacer");
            empPayrollToolbar.setWidth("spacer", 100);
            empPayrollToolbar.addButton("salaryReport", '3', 'Generate Salary Report', 'other.gif');
            empPayrollToolbar.addButton("payrollExport", '4', 'Excel Export', 'excel.png');
            payroll_sel_year = Date.today().getFullYear();
            payroll_sel_month = Date.today().getMonth() + 1;
            empPayrollToolbar.attachEvent("onClick", function (id) {

                pyrlflag = 0;
                var pId = empPayrollToolbar.getParentId(id);
                if (pId == 'att_month_filter') {
                    if (empPayrollToolbar.getListOptionSelected("att_year_filter")) {
                        payroll_sel_year = empPayrollToolbar.getListOptionSelected("att_year_filter");
                        payroll_sel_year = payroll_sel_year.substr(1);
                    }
                    id = id.substr(1);
                    payroll_sel_month = id;
                    params = "att_month=" + payroll_sel_month + "&att_year=" + payroll_sel_year;
                    preTally.Settings.progressOn(true, empPayrollLayout, null);
                    empPayrollGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/empPayrollGrid.php&" + params), function () {
                        preTally.Settings.progressOff(true, empPayrollLayout, null);
                        $('#PayrolRptName').val('');
                        $('#PayrolRptLoc').val('');
                    });
                }
                if (pId == 'att_year_filter') {
                    if (empPayrollToolbar.getListOptionSelected("att_month_filter")) {
                        payroll_sel_month = empPayrollToolbar.getListOptionSelected("att_month_filter");
                        payroll_sel_month = payroll_sel_month.substr(1);
                    }
                    id = id.substr(1);
                    payroll_sel_year = id;
                    params = "att_month=" + payroll_sel_month + "&att_year=" + payroll_sel_year;
                    preTally.Settings.progressOn(true, empPayrollLayout, null);
                    empPayrollGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/empPayrollGrid.php&" + params), function () {
                        preTally.Settings.progressOff(true, empPayrollLayout, null);
                        $('#PayrolRptName').val('');
                        $('#PayrolRptLoc').val('');
                    });
                }
                if (pId == 'att_year_filter' || pId == 'att_month_filter') {
                    var params = "EP_Month=" + payroll_sel_month + "&EP_Year=" + payroll_sel_year;
                    $.post(preTally.Initialize.encryptURL("warehouse/payrollHistory.php&" + params), function (data, status) {
                        if (data == "00000") {
                            pyrlflag = 1;
                            empPayrollGrid.enableEditEvents(false, false, false);
                        } else if (data == "11111") {
                            empPayrollGrid.enableEditEvents(true, true, true);
                        }
                    }).done(function () {
                        var params = "EP_Month=" + payroll_sel_month + "&EP_Year=" + payroll_sel_year;
                        preTally.UserProfile.getPayrollFooter(params);
                    });
                }
                if (id == "salaryReport") {
                    params = "att_month=" + payroll_sel_month + "&att_year=" + payroll_sel_year;
                    $.post(preTally.Initialize.encryptURL("warehouse/generateEmpSalReport.php&" + params), function (data, status) {
                        //dhtmlx.message({text: data});
                        if (data == "1") {
                            empPayrollGrid.enableEditEvents(false, false, false);
                            pyrlflag = 1;
                            dhtmlx.message({text: "Salary Report Generated Successfully"});
                        } else if (data == "2") {
                            dhtmlx.message({text: " Employee Salary Report  Already Exists"});
                        } else if (data == "3") {
                            dhtmlx.message({text: " Cant generate Current month's Salary Slip"});
                        } else if (data == "4") {
                            dhtmlx.message({text: " Generate payroll first"});
                        }
                    });
                }
                if (id == "payrollExport") {
                    $.post(
                            preTally.Initialize.encryptURL('warehouse/payrollExcelExport.php'),
                            {Month: payroll_sel_month, Year: payroll_sel_year},
                            function (data) {
                                fileName = data.split("XL_");
                                if (fileName[1]) {
                                    document.location = "uploads/excelFile/" + fileName[1];
                                }
                                preTally.Settings.progressOff(true, dhxLayout, null);
                            });
                }
            });
            empPayrollGrid.attachEvent("onRowSelect", function (id, ind) {
                if (pyrlflag == 1) {
                    dhtmlx.message({text: "Can't edit..Salary report already Generated"});
                }
            });
            empPayrollGrid.attachEvent("onFilterEnd", function (elements) {
                if (empPayrollGrid.getRowsNum() == 0) {
                    empPayrollGrid.addRow("row1", ['', 'Record not Found', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], 0);
                    empPayrollGrid.setColspan("row1", 1, 23);
                    empPayrollGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                } else {

                }

            });
            var empPayrollGridcolName = {3: 'US_GrossSal', 4: 'EP_TakehomeSal', 5: 'US_BasicSal', 6: 'US_HRASal', 7: 'US_CcaSal', 8: 'US_ConveySal',
                9: 'US_EduSal', 10: 'US_MedSal', 11: 'EP_Otherallowance', 12: 'Gross-lop', 13: 'US_DedEPF', 14: 'US_DedESI', 15: 'US_DedLWF', 16: 'EP_Lop', 17: 'EP_Proftax', 18: 'EP_ProfTds', 19: 'EP_SalTds', 20: 'EP_MealCard', 21: 'EP_Salaryadvance', 22: 'EP_Loan', 23: 'EP_AdjstmntAddition', 24: 'EP_AdjstmntDeduction'};
            empPayrollGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                empPayrollGrid.setColValidators("NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty,NotEmpty");
                Montharray = {January: '01', February: '02', March: '03', April: '04', May: '05', June: '06', July: '07', August: '08', September: '09', October: '10', November: '11', December: '12'}
                MonthData = empPayrollGrid.getUserData("", "EP_Month");
                MonthData = Montharray[MonthData];
                var date = new Date();
                var year = empPayrollGrid.getUserData("", "EP_Year");
                if (stage == 2) {
                    if (!isNaN(nValue) && (nValue != "")) {
                        if (nValue != oValue) {
                            if (nValue >= 0) {
                                //var params = "EP_Month=" + MonthData ;
                                //$.post(preTally.Initialize.encryptURL("warehouse/payrollHistory.php&" + params), function(data, status){
                                //varifysalreport=data; 
                                //if(varifysalreport=="11111"){
                                allowance = 0, deduction = 0, addition = 1;
                                takeHomeSal = parseFloat(empPayrollGrid.cellById(rId, 4).getValue());
                                lop = parseFloat(empPayrollGrid.cellById(rId, 16).getValue());
                                THS = 0;
                                gross_Lop = parseFloat(empPayrollGrid.cellById(rId, 12).getValue());
                                SS_DedESI = empPayrollGrid.getUserData(rId, "SS_DedESI");
                                SS_DedProfTDS = empPayrollGrid.getUserData(rId, "SS_DedProfTDS");
                                SS_DedEPF = empPayrollGrid.getUserData(rId, "SS_DedEPF");
                                SS_DedLWF = empPayrollGrid.getUserData(rId, "SS_DedLWF");
                                SS_EmpConESI = empPayrollGrid.getUserData(rId, "SS_EmpConESI");
                                SS_EmpConEPF = empPayrollGrid.getUserData(rId, "SS_EmpConEPF");
                                SS_EmpConLWF = empPayrollGrid.getUserData(rId, "SS_EmpConLWF");
                                EP_EmpConEPF = empPayrollGrid.getUserData(rId, "EP_EmpConEPF");
                                EP_EmpConESI = empPayrollGrid.getUserData(rId, "EP_EmpConESI");
                                EP_EmpConLWF = empPayrollGrid.getUserData(rId, "EP_EmpConLWF");
                                SS_DedESI_Type = empPayrollGrid.getUserData(rId, "SS_DedESI_Type");
                                SS_DedEPF_Type = empPayrollGrid.getUserData(rId, "SS_DedEPF_Type");
                                SS_DedProfTDS_Type = empPayrollGrid.getUserData(rId, "SS_DedProfTDS_Type");
                                SS_DedLWF_Type = empPayrollGrid.getUserData(rId, "SS_DedLWF_Type");
                                SS_EmpConESI_Type = empPayrollGrid.getUserData(rId, "SS_EmpConESI_Type");
                                SS_EmpConEPF_Type = empPayrollGrid.getUserData(rId, "SS_EmpConEPF_Type");
                                SS_EmpConLWF_Type = empPayrollGrid.getUserData(rId, "SS_EmpConLWF_Type");
                                if (cInd == 5 || cInd == 6 || cInd == 7 || cInd == 8 || cInd == 9 || cInd == 10 || cInd == 11) { //Salary Splitups
                                    allowance = 1;
                                    grossSalary = empPayrollGrid.cellById(rId, 3).getValue();
                                    grossSal = grossSalary - parseFloat(oValue);
                                    grossSalSum = 0;
                                    for (i = 5; i <= 11; i++) {
                                        grossSalSum = parseFloat(grossSalSum) + parseFloat(empPayrollGrid.cellById(rId, i).getValue());
                                    }
                                    grossSalSum = Math.round(grossSalSum);
                                    daysInamonth = new Date(payroll_sel_year, payroll_sel_month, 0).getDate();
                                    EP_SalDeductableLeave = empPayrollGrid.getUserData(rId, "EP_SalDeductableLeave");
                                    var AttndSkip = empPayrollGrid.getUserData(rId, "US_AttndFlag");
                                    if (grossSalSum != 0 && AttndSkip == 0) {
                                        lop = Math.round((grossSalSum / daysInamonth) * EP_SalDeductableLeave);
                                        empPayrollGrid.cellById(rId, 16).setValue(lop);
                                    } else {
                                        lop = 0;
                                        empPayrollGrid.cellById(rId, 16).setValue(0);
                                    }

                                    gross_Lop = Math.round(grossSalSum - lop);
                                    if (SS_DedESI_Type == 0) {
                                        ESI = Math.round((gross_Lop * SS_DedESI) / 100);
                                        empPayrollGrid.cellById(rId, 14).setValue(ESI);
                                    }
                                    if (SS_EmpConESI_Type == 0) {
                                        EP_EmpConESI = Math.round((gross_Lop * SS_EmpConESI) / 100);
                                        empPayrollGrid.setUserData(rId, "EP_EmpConESI", EP_EmpConESI);
                                    }
                                    if (SS_EmpConEPF_Type == 0) {
                                        EP_EmpConEPF = Math.round((gross_Lop * SS_EmpConEPF) / 100);
                                        empPayrollGrid.setUserData(rId, "EP_EmpConEPF", EP_EmpConEPF);
                                    }
                                    if (SS_DedEPF_Type == 0) {
                                        EPF = Math.round((gross_Lop * SS_DedEPF) / 100);
                                        empPayrollGrid.cellById(rId, 13).setValue(EPF);
                                    }
                                    if (SS_DedProfTDS_Type == 0) {
                                        ProfTDS = Math.round((gross_Lop * SS_DedProfTDS) / 100);
                                        empPayrollGrid.cellById(rId, 18).setValue(ProfTDS);
                                    }
                                    if (SS_DedLWF_Type == 0) {
                                        LWF = Math.round((grossSalSum * SS_DedLWF) / 100);
                                        empPayrollGrid.cellById(rId, 15).setValue(LWF);
                                    }
                                    if (SS_EmpConLWF_Type == 0) {
                                        EP_EmpConLWF = Math.round((grossSalSum * SS_EmpConLWF) / 100);
                                        empPayrollGrid.setUserData(rId, "EP_EmpConLWF", EP_EmpConLWF);
                                    }
                                    if (empPayrollGrid.cellById(rId, 13).getValue() == 0 && empPayrollGrid.cellById(rId, 14).getValue() == 0)
                                        gross_Lop = 0;
                                    empPayrollGrid.cellById(rId, 12).setValue(gross_Lop);
                                    THS = grossSalSum;
                                    for (i = 13; i <= 24; i++) {
                                        if (i == 23) {
                                            continue;
                                        }
                                        THS = parseFloat(THS) - parseFloat(empPayrollGrid.cellById(rId, i).getValue());
                                    }

                                    THS = parseFloat(THS) + parseFloat(empPayrollGrid.cellById(rId, 23).getValue());
                                    THS = Math.round(THS);
                                } else if (cInd == 23) { //ADDITION
                                    addition = 1;
                                    //takeHomeSal=Math.round(parseFloat(takeHomeSal)-parseFloat(oValue));
                                    //THS=Math.round(parseFloat(takeHomeSal)+parseFloat(nValue));
                                    grossSalSum = parseFloat(empPayrollGrid.cellById(rId, 3).getValue());
                                    THS = grossSalSum;
                                    for (i = 13; i <= 24; i++) {
                                        if (i == 23) {
                                            continue;
                                        }
                                        THS = parseFloat(THS) - parseFloat(empPayrollGrid.cellById(rId, i).getValue());
                                    }

                                    THS = parseFloat(THS) + parseFloat(empPayrollGrid.cellById(rId, 23).getValue());
                                    THS = Math.round(THS);
                                } else if (cInd == 15 || cInd == 17 || cInd == 18 || cInd == 19 || cInd == 20 || cInd == 21 || cInd == 22 || cInd == 24) { //Sal Deductions
                                    deduction = 1;
                                    //takeHomeSal=parseFloat(takeHomeSal)+parseFloat(oValue);
                                    //THS=Math.round(parseFloat(takeHomeSal)-parseFloat(nValue));
                                    grossSalSum = parseFloat(empPayrollGrid.cellById(rId, 3).getValue());
                                    THS = grossSalSum;
                                    // grossSalSum=parseFloat(empPayrollGrid.cellById(rId,12).getValue());
                                    for (i = 13; i <= 24; i++) {
                                        if (i == 23) {
                                            continue;
                                        }
                                        THS = parseFloat(THS) - parseFloat(empPayrollGrid.cellById(rId, i).getValue());
                                    }

                                    THS = parseFloat(THS) + parseFloat(empPayrollGrid.cellById(rId, 23).getValue());
                                    THS = Math.round(THS);
                                } else if (cInd == 16) { //LOP row edit
                                    deduction = 1;
                                    grossSalSum = parseFloat(empPayrollGrid.cellById(rId, 3).getValue());
                                    gross_Lop = Math.round(grossSalSum - lop);
                                    if (SS_DedESI_Type == 0) {
                                        ESI = Math.round((gross_Lop * SS_DedESI) / 100);
                                        empPayrollGrid.cellById(rId, 14).setValue(ESI);
                                    }
                                    if (SS_DedEPF_Type == 0) {
                                        EPF = Math.round((gross_Lop * SS_DedEPF) / 100);
                                        empPayrollGrid.cellById(rId, 13).setValue(EPF);
                                    }
                                    if (SS_DedProfTDS_Type == 0) {
                                        ProfTDS = Math.round((gross_Lop * SS_DedProfTDS) / 100);
                                        empPayrollGrid.cellById(rId, 18).setValue(ProfTDS);
                                    }
                                    if (SS_DedLWF_Type == 0) {
                                        LWF = Math.round((grossSalSum * SS_DedLWF) / 100);
                                        empPayrollGrid.cellById(rId, 15).setValue(LWF);
                                    }
                                    if (SS_EmpConESI_Type == 0) {
                                        EP_EmpConESI = Math.round((gross_Lop * SS_EmpConESI) / 100);
                                        // empPayrollGrid.setUserData(rId,"EP_EmpConESI",empconESI);
                                    }
                                    if (SS_EmpConEPF_Type == 0) {
                                        EP_EmpConEPF = Math.round((gross_Lop * SS_EmpConEPF) / 100);
                                        // empPayrollGrid.setUserData(rId,"EP_EmpConEPF",empconEPF);
                                    }
                                    if (SS_EmpConLWF_Type == 0) {
                                        EP_EmpConLWF = Math.round((grossSalSum * SS_EmpConLWF) / 100);
                                        // empPayrollGrid.setUserData(rId,"EP_EmpConLWF",EP_EmpConLWF);
                                    }
                                    if (empPayrollGrid.cellById(rId, 13).getValue() == 0 && empPayrollGrid.cellById(rId, 14).getValue() == 0)
                                        gross_Lop = 0;
                                    empPayrollGrid.cellById(rId, 12).setValue(gross_Lop);
                                    THS = grossSalSum;
                                    for (i = 13; i <= 24; i++) {
                                        if (i == 23) {
                                            continue;
                                        }
                                        THS = parseFloat(THS) - parseFloat(empPayrollGrid.cellById(rId, i).getValue());
                                    }

                                    THS = parseFloat(THS) + parseFloat(empPayrollGrid.cellById(rId, 23).getValue());
                                    THS = Math.round(THS);
                                }
                                US_Id = empPayrollGrid.getUserData(rId, "US_Id");
                                EPF = parseFloat(empPayrollGrid.cellById(rId, 13).getValue());
                                ESI = parseFloat(empPayrollGrid.cellById(rId, 14).getValue());
                                LWF = parseFloat(empPayrollGrid.cellById(rId, 15).getValue());
                                PTDS = parseFloat(empPayrollGrid.cellById(rId, 18).getValue());
                                var params = "EPH_ColLabel=" + empPayrollGridcolName[cInd] + "&EP_Id=" + rId + "&US_Id=" + US_Id + "&EPH_ColValue=" + nValue + "& EP_TakehomeSal=" + THS + "& Month=" + MonthData + "&US_GrossSal=" + grossSalSum + "&lop=" + lop + "&EPF=" + EPF + "&ESI=" + ESI + "&EP_PFESI_Sal=" + gross_Lop + "&LWF=" + LWF;
                                $.post(preTally.Initialize.encryptURL("warehouse/payrollHistory.php"),
                                        {EPH_ColLabel: empPayrollGridcolName[cInd],
                                            EP_Id: rId,
                                            US_Id: US_Id,
                                            EPH_ColValue: nValue,
                                            EP_TakehomeSal: THS,
                                            Month: payroll_sel_month,
                                            Year: payroll_sel_year,
                                            US_GrossSal: grossSalSum,
                                            lop: lop,
                                            EPF: EPF,
                                            ESI: ESI,
                                            LWF: LWF,
                                            empconESI: EP_EmpConESI,
                                            empconEPF: EP_EmpConEPF,
                                            empconLWF: EP_EmpConLWF,
                                            ProfTDS: PTDS,
                                            EP_PFESI_Sal: gross_Lop}, function (data, status) {
                                    empPayrollGrid.cellById(rId, 3).setValue(grossSalSum);
                                    empPayrollGrid.cellById(rId, 4).setValue(THS);
                                    empPayrollGrid.setCellTextStyle(rId, cInd, "background-color: pink");
                                }).done(function () {
                                    var params = "EP_Month=" + payroll_sel_month + "&EP_Year=" + payroll_sel_year;
                                    preTally.UserProfile.getPayrollFooter(params);
                                });
                                //}
                                //else{
                                //    empPayrollGrid.cellById(rId,cInd).setValue(oValue);
                                //    dhtmlx.message({text: "Cant edit...Report already generated"});
                                //}
                                //});
                            } else {
                                empPayrollGrid.cellById(rId, cInd).setValue(oValue);
                                dhtmlx.message({text: "Please enter valid data"});
                            }
                        }

                    } else {
                        empPayrollGrid.cellById(rId, cInd).setValue(oValue);
                        dhtmlx.message({text: "Please enter valid data"});
                    }

                }
                return true;
            });
        }, applyPayrollFilter: function () {
            params = "att_month=" + payroll_sel_month + "&att_year=" + payroll_sel_year;
            var filterValue = new Array($('#PayrolRptName').val(), $('#PayrolRptLoc').val(), $('#hid_PayrolSort').val(), $('#hid_PayrolCol').val());
            var filtr = params + "&Filters=" + filterValue;
            empPayrollGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/empPayrollGrid.php&" + filtr), function () {

            });
        },
        getPayrollFooter: function (params) {
            $.post(preTally.Initialize.encryptURL("warehouse/payrollFooter.php&" + params), function (data, status) {
                var FooterData = jQuery.parseJSON(data)
                $('.basic').html(FooterData[0].Basic);
                $('.hra').html(FooterData[0].HRA);
                $('.cca').html(FooterData[0].CCA);
                $('.convey').html(FooterData[0].Convey);
                $('.edu').html(FooterData[0].Edu);
                $('.medic').html(FooterData[0].Medic);
                $('.other').html(FooterData[0].Other);
                $('.gross').html(FooterData[0].Gross);
                $('.epfsal').html(FooterData[0].PFESI_Sal);
                $('.epf').html(FooterData[0].DedEPF);
                $('.esi').html(FooterData[0].DedESI);
                $('.lwf').html(FooterData[0].DedLWF);
                $('.lop').html(FooterData[0].EP_Lop);
                $('.prftax').html(FooterData[0].EP_Proftax);
                $('.proftds').html(FooterData[0].EP_ProfTds);
                $('.saltds').html(FooterData[0].EP_SalTds);
                $('.mealcard').html(FooterData[0].EP_MealCard);
                $('.adv').html(FooterData[0].SalAdv);
                $('.loan').html(FooterData[0].Loan);
                $('.addtn').html(FooterData[0].Addition);
                $('.dedctn').html(FooterData[0].Deduct);
                $('.ths').html(FooterData[0].THS);
            });
        },
        leaveList: function (LR_Id, status) {
            var comment = myLeaveGrid.cells(LR_Id, 10).getValue();
            params = "LR_Id=" + LR_Id + "&status=" + 5 + "&comment=" + comment;
            $.post(preTally.Initialize.encryptURL("warehouse/leaveApprove.php&" + params), function (data) {
                var searchFromdate = myLeaveToolBar.getValue("myleave_date_from");
                var searchTodate = myLeaveToolBar.getValue("myleave_date_till");
                var params = "appFromDate=" + searchFromdate + "&appToDate=" + searchTodate;
                myLeaveGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/myleavegrid.php&" + params), true, true);
                dhtmlx.message({text: data});
                Att_scheduler.clearAll();
                Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
            });
        },
        salaryReports: function () {
            SalRptFlag = 0;
            PayModRptFlag = 0;
            SalPFRptFlag = 0;
            SalESIRptFlag = 0;
            if (!dhxMiddleBlockTabs.cells("menuSalaryReport")) {
                dhxMiddleBlockTabs.addTab("menuSalaryReport", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Salary Reports", 250);
                dhxMiddleBlockTabs.tabs("menuSalaryReport").setActive();
                salRptTabbar = dhxMiddleBlockTabs.tabs("menuSalaryReport").attachTabbar();
                var ACLPayrollEdit = unescape(JGG1P3bDnUSDL16Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var SalPaymodewiseRptBranch = unescape(JGG1P3bDnUSDL17Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                var SalPaymodewiseRptAll = unescape(JGG1P3bDnUSDL18Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                salRptTabbar.addTab("a1", "Salary Report");
                if (SalPaymodewiseRptBranch == 1 || SalPaymodewiseRptAll == 1)
                    salRptTabbar.addTab("a2", "Paymodewise Report");
                salRptTabbar.addTab("a3", "PF Salary Report");
                salRptTabbar.addTab("a4", "ESI Salary Report");
                salRptTabbar.cells("a1").setActive();
                preTally.UserProfile.listSalaryReport();
                salRptTabbar.attachEvent("onSelect", function (id, last_id) {
                    if (id == "a1") {
                        preTally.UserProfile.listSalaryReport();
                    } else if (id == "a2") {
                        if (SalPaymodewiseRptBranch == 1 || SalPaymodewiseRptAll == 1)
                            preTally.UserProfile.listPaymentModeWiseSalRpt(SalPaymodewiseRptBranch, SalPaymodewiseRptAll);
                    } else if (id == "a3") {
                        preTally.UserProfile.listPFSalaryReport();
                    } else if (id == "a4") {
                        preTally.UserProfile.listESISalaryReport();
                    }
                    return true;
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuSalaryReport").setActive();
            }
        },
        listSalaryReport: function () {
            if (SalRptFlag == 0) {
                nameFilter = '';
                branchFilter = '';
                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (salrpt_sel_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    //mnthLimit=Date.today().getMonth()+1
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }
                salApprovedBlockedFilter = '';
                SalaryReport = salRptTabbar.tabs("a1").attachLayout('1C');
                SalaryReport.cells("a").hideHeader();
                SalaryRptToolbar = SalaryReport.cells("a").attachToolbar();
                SalaryRptToolbar.setIconsPath("images/icon/default_18/");
                SalaryRptToolbar.setSkin("dhx_skyblue");
                SalaryRptToolbar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                SalaryRptToolbar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                SalaryRptToolbar.setItemText('att_month_filter', monthNames[month]);
                SalaryRptToolbar.setItemText('att_year_filter', Date.today().getFullYear());
                SalaryRptToolbar.addText("spacer", '2', "");
                SalaryRptToolbar.disableItem("spacer");
                SalaryRptToolbar.setWidth("spacer", 600);
                //SalaryRptToolbar.addButton("createBSEntry",'4','Create BS Entries',"../account.png");
                SalaryRptToolbar.addButton("exportSalaryReport", '3', "Export", "excel.png");
                SalaryRptToolbar.addButton("sentMail", '4', 'Sent Mail', "../message_reply.png");
                SalaryRptGrid = SalaryReport.cells("a").attachGrid();
                SalaryRptGrid.setHeader("SlNo,User Details,#cspan,Duration,#cspan,Gross Sal,Take Home Salary,Salary Additions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,PF/ESI Sal,Salary Deductions,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,Salary Adjustments,#cspan,Check,ESR_status,Status");
                SalaryRptGrid.attachHeader(',<input type="text" id="SlryRptName" class="SalRptTextFilter" placeholder="Enter Staff Name" style="width: 90%;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" ColName="NM" class="btn_SalRptSort" />,<input type="text" id="SlryRptLoc" class="SalRptTextFilter" placeholder="Enter Branch Name" style="width: 90%;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" ColName="LC" class="btn_SalRptSort" />,Month,Year,<div>Gross Sal <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" ColName="GS" class="btn_SalRptSort" /></div>,<div>Take Home Salary<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" ColName="THS" class="btn_SalRptSort" /><input type="hidden" id="hid_SalRptSort" value="0"/><input type="hidden" id="hid_SalSortCol" value=""/></div>,Basic Salary,HRA Allowance,CCA Allowance,Conveyance Allowance,Education Allowance,Medical Allowance,Other Allowance,PF/ESI Sal,EPF,ESI,LWF,LOP,Professional Tax,Prof TDS,Sal TDS,Meal Card,Salary Advance,Loan,Addition,Deduction,#master_checkbox,,<select id="ESR_Status" style="width:90%; font-size:8pt; font-family:Tahoma;"><option value="all">All</option><option value="1">Sent</option><option value="0">Not Sent</option></select>');
                SalaryRptGrid.setInitWidths("30,140,140,0,0,80,80,80,80,80,80,80,100,80,80,80,80,80,80,80,80,80,80,80,80,80,100,80,0,80");
                SalaryRptGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ch,ro,ro");
                SalaryRptGrid.init();
                SalaryRptGrid.setSkin("dhx_skyblue");
                SalaryRptGrid.setImagePath("assets/grid/codebase/imgs/");
                SalaryRptGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
                SalaryRptGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
                SalaryRptGrid.attachFooter(",,,,,<div id='footer_gross'>0</div>,<div id='footer_ths'>0</div>,<div id='footer_basic'>0</div>,\n\
                                        <div id='footer_hra'>0</div>,<div id='footer_cca'>0</div>,<div id='footer_convey'>0</div>,<div id='footer_edu'>0</div>,\n\
<div id='footer_medic'>0</div>,<div id='footer_other'>0</div>,<div id='footer_pfesi'>0</div>,<div id='footer_epf'>0</div>,\n\
<div id='footer_esi'>0</div>,<div id='footer_lwf'>0</div>,<div id='footer_lop'>0</div>,<div id='footer_proftax'>0</div>,<div id='footer_ptds'>0</div>,\n\
<div id='footer_saltds'>0</div>,<div id='footer_meal'>0</div>,<div id='footer_adv'>0</div>,<div id='footer_loan'>0</div>,<div id='footer_addit'>0</div>,\n\
<div id='footer_deduct'>0</div>,,,");
                SalaryRptGrid.enableColSpan(true);
                SalaryRptGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                SalaryRptGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                //SalaryRptGrid.splitAt(3);
                var tb_data_txt = '<div id="salrpt_paging"></div>';
                SalaryReport.cells("a").attachStatusBar({
                    text: tb_data_txt,
                    height: 23
                });
                SalaryRptGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                SalaryRptGrid.enablePaging(true, 50, 5, "salrpt_paging", true);
                SalaryRptGrid.setPagingSkin("toolbar", "dhx_skyblue");
                SalaryRptGrid.enableRowsHover(true, "bonusReportHover");
                var filtrInterval;
                $(".SalRptTextFilter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.applySalRptFilter();
                        clearInterval(filtrInterval);
                    }, 500);
                });
                $("#ESR_Status").change(function () {
                    preTally.UserProfile.applySalRptFilter();
                });
                //SalaryRptGrid.splitAt(4);
                var sel_month = null;
                var srtFlg = 0;
                $('.btn_SalRptSort').click(function () {
                    var ColName = $(this).attr("ColName")
                    $('#hid_SalSortCol').val(ColName);
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        $('#hid_SalRptSort').val(1);
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        $('#hid_SalRptSort').val(0);
                        srtFlg = 0;
                    }
                    preTally.UserProfile.applySalRptFilter();
                });
                salrpt_sel_month = Date.today().getMonth() + 1;
                salrpt_sel_year = Date.today().getFullYear();
                SalaryRptToolbar.attachEvent("onClick", function (id) {
                    sel_month = SalaryRptToolbar.getListOptionSelected("att_month_filter");
                    if (id == 'sentMail') {
                        preTally.UserProfile.SalaryReportSentMail();
                    }
                    var pId = SalaryRptToolbar.getParentId(id);
                    if (pId == 'att_month_filter') {
                        if (SalaryRptToolbar.getListOptionSelected("att_year_filter")) {
                            salrpt_sel_year = SalaryRptToolbar.getListOptionSelected("att_year_filter");
                            salrpt_sel_year = salrpt_sel_year.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_month = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        // preTally.Settings.progressOn(true, SalaryReport, 'a');
                        SalaryRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salaryRptGrid.php&" + params), function () {
                            preTally.UserProfile.getSalRptFooter(params);
                        });
                        if (SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked == true) {
                            SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked = false;
                        }

                        //preTally.Settings.progressOff(true, SalaryReport, 'a');
                    }
                    if (pId == 'att_year_filter') {
                        if (SalaryRptToolbar.getListOptionSelected("att_month_filter")) {
                            salrpt_sel_month = SalaryRptToolbar.getListOptionSelected("att_month_filter");
                            salrpt_sel_month = salrpt_sel_month.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_year = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        // preTally.Settings.progressOn(true, SalaryReport, 'a');
                        SalaryRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salaryRptGrid.php&" + params), function () {
                            preTally.UserProfile.getSalRptFooter(params);
                        });
                        if (SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked == true) {
                            SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked = false;
                        }

                        //preTally.Settings.progressOff(true, SalaryReport, 'a');
                    }
                    if (id == "exportSalaryReport") {
                        preTally.UserProfile.exportSalaryReport(salrpt_sel_month, salrpt_sel_year, nameFilter, branchFilter, salApprovedBlockedFilter);
                    }
                });
                params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                SalaryRptGrid.loadXML(preTally.Initialize.encryptURL("requisites/salaryRptGrid.php&" + params), function () {
                    //SalaryRptGrid.makeFilter("ESR_Status",26);
                    preTally.UserProfile.getSalRptFooter(params);
                });
                SalaryRptGrid.attachEvent("onFilterEnd", function (elements) {
                    if (SalaryRptGrid.getRowsNum() == 0) {
                        if (SalaryRptGrid.doesRowExist("msgRow"))
                            SalaryRptGrid.deleteRow("msgRow");
                        SalaryRptGrid.addRow('msgRow', "No records found");
                        SalaryRptGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        SalaryRptGrid.setColspan("msgRow", 0, 25);
                    }
                    nameFilter = SalaryRptGrid.getFilterElement(1).value;
                    branchFilter = SalaryRptGrid.getFilterElement(2).value;
                    salApprovedBlockedFilter = SalaryRptGrid.getFilterElement(26).value;
                });
                SalRptFlag = 1;
            }
        }, applySalRptFilter: function () {
            params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
            var filterValue = new Array($('#SlryRptName').val(), $('#SlryRptLoc').val(), $("#ESR_Status").val(), $('#hid_SalRptSort').val(), $('#hid_SalSortCol').val());
            var filtr = params + "&Filters=" + filterValue;
            SalaryRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salaryRptGrid.php&" + filtr), function () {
            });
        }, getSalRptFooter: function (params) {
            $.post(preTally.Initialize.encryptURL("warehouse/salrptFooter.php&" + params), function (data, status) {
                var FooterData = jQuery.parseJSON(data);
                $('#footer_basic').html(FooterData.Basic);
                $('#footer_hra').html(FooterData.HRA);
                $('#footer_cca').html(FooterData.CCA);
                $('#footer_convey').html(FooterData.Convey);
                $('#footer_edu').html(FooterData.Edu);
                $('#footer_medic').html(FooterData.Med);
                $('#footer_other').html(FooterData.Other);
                $('#footer_gross').html(FooterData.Gross);
                $('#footer_pfesi').html(FooterData.PFESI);
                $('#footer_epf').html(FooterData.DedEPF);
                $('#footer_esi').html(FooterData.DedESI);
                $('#footer_lwf').html(FooterData.DedLWF);
                $('#footer_lop').html(FooterData.LOP);
                $('#footer_proftax').html(FooterData.Ptax);
                $('#footer_ptds').html(FooterData.PTDS);
                $('#footer_saltds').html(FooterData.Saltds);
                $('#footer_meal').html(FooterData.Meal);
                $('#footer_adv').html(FooterData.SalAdv);
                $('#footer_loan').html(FooterData.Loan);
                $('#footer_addit').html(FooterData.AdjAdd);
                $('#footer_deduct').html(FooterData.AdjDeduct);
                $('#footer_ths').html(FooterData.THS);
            });
        },
        listPFSalaryReport: function () {
            if (SalPFRptFlag == 0) {

                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (salrpt_sel_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }

                SalaryPFReport = salRptTabbar.tabs("a3").attachLayout('1C');
                SalaryPFReport.cells("a").hideHeader();
                SalaryPFRptToolbar = SalaryPFReport.cells("a").attachToolbar();
                SalaryPFRptToolbar.setIconsPath("images/icon/default_18/");
                SalaryPFRptToolbar.setSkin("dhx_skyblue");
                SalaryPFRptToolbar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                SalaryPFRptToolbar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                SalaryPFRptToolbar.setItemText('att_month_filter', monthNames[month]);
                SalaryPFRptToolbar.setItemText('att_year_filter', Date.today().getFullYear());
                SalaryPFRptToolbar.addText("spacer", '2', "");
                SalaryPFRptToolbar.disableItem("spacer");
                SalaryPFRptToolbar.setWidth("spacer", 600);
                //SalaryPFRptToolbar.addButton("createBSEntry",'4','Create BS Entries',"../account.png");

                SalaryPFRptToolbar.addButton("PF_DetailsExport", "3", "PF Export", "excel.png");
//                SalaryPFRptToolbar.addSeparator();
//                SalaryPFRptToolbar.addButton("ESI_DetailsExport","4","ESI Export","excel.png")
//                SalaryPFRptToolbar.addSeparator();
//
//                SalaryPFRptToolbar.addButton("exportSalaryPFReport",'5',"Export","excel.png");
//                SalaryPFRptToolbar.addButton("sentMail",'6','Sent Mail',"../message_reply.png");            
                SalaryPFRptGrid = SalaryPFReport.cells("a").attachGrid();
                SalaryPFRptGrid.setHeader("SlNo,PF Number,Employee Name,Employee Branch,LOP Days,PF Salary");
                SalaryPFRptGrid.attachHeader(',,<input type="text" id="expPF_USName" class="expPF_TextFilter" placeholder="Enter Staff Name" style="width: 90%;">,<input type="text" id="expPF_LCName" class="expPF_TextFilter" placeholder="Enter Branch Name" style="width: 90%;">,,,');
                SalaryPFRptGrid.setInitWidths("80,*,*,*,*,*");
                SalaryPFRptGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                SalaryPFRptGrid.init();
                SalaryPFRptGrid.setSkin("dhx_skyblue");
                SalaryPFRptGrid.setImagePath("assets/grid/codebase/imgs/");
                SalaryPFRptGrid.enableTooltips("false,false,false,false,false,false");
                SalaryPFRptGrid.setColSorting("na,na,na,na,na,na");
                SalaryPFRptGrid.enableColSpan(true);
                SalaryPFRptGrid.enableRowsHover(true, "bonusReportHover");
                SalaryPFRptGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                SalaryPFRptGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                var tb_data_txt = '<div id="salPFrpt_paging"></div>';
                SalaryPFReport.cells("a").attachStatusBar({
                    text: tb_data_txt,
                    height: 23
                });
                SalaryPFRptGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                SalaryPFRptGrid.enablePaging(true, 50, 5, "salPFrpt_paging", true);
                SalaryPFRptGrid.setPagingSkin("toolbar", "dhx_skyblue");
                var filtrInterval;
                $(".expPF_TextFilter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.applyPFSalRptFilter();
                        clearInterval(filtrInterval);
                    }, 500);
                });
                //SalaryPFRptGrid.splitAt(4);
                var sel_month = null;
                var srtFlg = 0;
                salrpt_sel_month = Date.today().getMonth() + 1;
                salrpt_sel_year = Date.today().getFullYear();
                SalaryPFRptToolbar.attachEvent("onClick", function (id) {
                    sel_month = SalaryPFRptToolbar.getListOptionSelected("att_month_filter");
                    var pId = SalaryPFRptToolbar.getParentId(id);
                    if (pId == 'att_month_filter') {
                        if (SalaryPFRptToolbar.getListOptionSelected("att_year_filter")) {
                            salrpt_sel_year = SalaryPFRptToolbar.getListOptionSelected("att_year_filter");
                            salrpt_sel_year = salrpt_sel_year.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_month = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        // preTally.Settings.progressOn(true, SalaryPFReport, 'a');
                        SalaryPFRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryPFRptGrid.php&" + params), function () {
//                            preTally.UserProfile.getSalRptFooter(params);
                        });
//                        if (SalaryPFRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked==true) {
//                             SalaryPFRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked = false;
//                        }

                        //preTally.Settings.progressOff(true, SalaryPFReport, 'a');
                    }
                    if (pId == 'att_year_filter') {
                        if (SalaryPFRptToolbar.getListOptionSelected("att_month_filter")) {
                            salrpt_sel_month = SalaryPFRptToolbar.getListOptionSelected("att_month_filter");
                            salrpt_sel_month = salrpt_sel_month.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_year = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        // preTally.Settings.progressOn(true, SalaryPFReport, 'a');
                        SalaryPFRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryPFRptGrid.php&" + params), function () {
//                            preTally.UserProfile.getSalRptFooter(params);
                        });
//                        if (SalaryPFRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked==true) {
//                             SalaryPFRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked = false;
//                        }

                        //preTally.Settings.progressOff(true, SalaryPFReport, 'a');
                    }
                    if (id == "PF_DetailsExport") {
                        preTally.UserProfile.exportSalPFDetails(salrpt_sel_month, salrpt_sel_year);
                    }
                    if (id == "ESI_DetailsExport") {
                        preTally.UserProfile.exportSalESIDetails(salrpt_sel_month, salrpt_sel_year);
                    }
                });
                params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                SalaryPFRptGrid.loadXML(preTally.Initialize.encryptURL("requisites/SalaryPFRptGrid.php&" + params), function () {
                    //SalaryPFRptGrid.makeFilter("ESR_Status",26);
//                    preTally.UserProfile.getSalRptFooter(params);
                });
                SalaryPFRptGrid.attachEvent("onFilterEnd", function (elements) {
                    if (SalaryPFRptGrid.getRowsNum() == 0) {
                        if (SalaryPFRptGrid.doesRowExist("msgRow"))
                            SalaryPFRptGrid.deleteRow("msgRow");
                        SalaryPFRptGrid.addRow('msgRow', "No records found");
                        SalaryPFRptGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        SalaryPFRptGrid.setColspan("msgRow", 0, 6);
                    }

                });
                SalPFRptFlag = 1;
            }
        },
        applyPFSalRptFilter: function () {
            var params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
            var filterValue = new Array($('#expPF_USName').val(), $('#expPF_LCName').val());
            var filtr = params + "&Filters=" + filterValue;
            SalaryPFRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryPFRptGrid.php&" + filtr), function () {
            });
        },
        listESISalaryReport: function () {
            if (SalESIRptFlag == 0) {

                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (salrpt_sel_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }

                SalaryESIReport = salRptTabbar.tabs("a4").attachLayout('1C');
                SalaryESIReport.cells("a").hideHeader();
                SalaryESIRptToolbar = SalaryESIReport.cells("a").attachToolbar();
                SalaryESIRptToolbar.setIconsPath("images/icon/default_18/");
                SalaryESIRptToolbar.setSkin("dhx_skyblue");
                SalaryESIRptToolbar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                SalaryESIRptToolbar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                SalaryESIRptToolbar.setItemText('att_month_filter', monthNames[month]);
                SalaryESIRptToolbar.setItemText('att_year_filter', Date.today().getFullYear());
                SalaryESIRptToolbar.addText("spacer", '2', "");
                SalaryESIRptToolbar.disableItem("spacer");
                SalaryESIRptToolbar.setWidth("spacer", 600);
                SalaryESIRptToolbar.addButton("ESI_DetailsExport", "3", "ESI Export", "excel.png");
                SalaryESIRptGrid = SalaryESIReport.cells("a").attachGrid();
                SalaryESIRptGrid.setHeader("SlNo,IP Number,Name,Employee Branch,No: of Working Days,ESI Salary");
                SalaryESIRptGrid.attachHeader(',,<input type="text" id="expESI_USName" class="expESI_TextFilter" placeholder="Enter Staff Name" style="width: 90%;">,<input type="text" id="expESI_LCName" class="expESI_TextFilter" placeholder="Enter Branch Name" style="width: 90%;">,,,');
                SalaryESIRptGrid.setInitWidths("80,*,*,*,*,*");
                SalaryESIRptGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                SalaryESIRptGrid.init();
                SalaryESIRptGrid.enableRowsHover(true, "bonusReportHover");
                SalaryESIRptGrid.setSkin("dhx_skyblue");
                SalaryESIRptGrid.setImagePath("assets/grid/codebase/imgs/");
                SalaryESIRptGrid.enableTooltips("false,false,false,false,false,false");
                SalaryESIRptGrid.setColSorting("na,na,na,na,na,na");
                SalaryESIRptGrid.enableColSpan(true);
                SalaryESIRptGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                SalaryESIRptGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                var tb_data_txt = '<div id="salESIrpt_paging"></div>';
                SalaryESIReport.cells("a").attachStatusBar({
                    text: tb_data_txt,
                    height: 23
                });
                SalaryESIRptGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                SalaryESIRptGrid.enablePaging(true, 50, 5, "salESIrpt_paging", true);
                SalaryESIRptGrid.setPagingSkin("toolbar", "dhx_skyblue");
                var filtrInterval;
                $(".expESI_TextFilter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.applyESISalRptFilter();
                        clearInterval(filtrInterval);
                    }, 500);
                });
                var sel_month = null;
                var srtFlg = 0;
                salrpt_sel_month = Date.today().getMonth() + 1;
                salrpt_sel_year = Date.today().getFullYear();
                SalaryESIRptToolbar.attachEvent("onClick", function (id) {
                    sel_month = SalaryESIRptToolbar.getListOptionSelected("att_month_filter");
                    var pId = SalaryESIRptToolbar.getParentId(id);
                    if (pId == 'att_month_filter') {
                        if (SalaryESIRptToolbar.getListOptionSelected("att_year_filter")) {
                            salrpt_sel_year = SalaryESIRptToolbar.getListOptionSelected("att_year_filter");
                            salrpt_sel_year = salrpt_sel_year.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_month = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        SalaryESIRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryESIRptGrid.php&" + params), function () {
                        });
                    }
                    if (pId == 'att_year_filter') {
                        if (SalaryESIRptToolbar.getListOptionSelected("att_month_filter")) {
                            salrpt_sel_month = SalaryESIRptToolbar.getListOptionSelected("att_month_filter");
                            salrpt_sel_month = salrpt_sel_month.substr(1);
                        }
                        id = id.substr(1);
                        salrpt_sel_year = id;
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                        SalaryESIRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryESIRptGrid.php&" + params), function () {
                        });
                    }
                    if (id == "PF_DetailsExport") {
                        preTally.UserProfile.exportSalPFDetails(salrpt_sel_month, salrpt_sel_year);
                    }
                    if (id == "ESI_DetailsExport") {
                        preTally.UserProfile.exportSalESIDetails(salrpt_sel_month, salrpt_sel_year);
                    }
                });
                params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                SalaryESIRptGrid.loadXML(preTally.Initialize.encryptURL("requisites/SalaryESIRptGrid.php&" + params), function () {
                });
                SalaryESIRptGrid.attachEvent("onFilterEnd", function (elements) {
                    if (SalaryESIRptGrid.getRowsNum() == 0) {
                        if (SalaryESIRptGrid.doesRowExist("msgRow"))
                            SalaryESIRptGrid.deleteRow("msgRow");
                        SalaryESIRptGrid.addRow('msgRow', "No records found");
                        SalaryESIRptGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        SalaryESIRptGrid.setColspan("msgRow", 0, 6);
                    }

                });
                SalESIRptFlag = 1;
            }
        },
        applyESISalRptFilter: function () {
            var params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
            var filterValue = new Array($('#expESI_USName').val(), $('#expESI_LCName').val());
            var filtr = params + "&Filters=" + filterValue;
            SalaryESIRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/SalaryESIRptGrid.php&" + filtr), function () {
            });
        },
        listPaymentModeWiseSalRpt: function (SalPaymodewiseRptBranch, SalPaymodewiseRptAll) {
            if (PayModRptFlag == 0) {
                nameFilter = '';
                branchFilter = '';
                useridFilter = '';
                var Months_Options = [];
                var modeOfPay_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (paymodesalrpt_sel_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    //mnthLimit=Date.today().getMonth()+1
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }
                var modeOfPayName;
                modeOfPayName = ["All", "Bank", "Cash", "Cheque"];
                for (k = 0; k < modeOfPayName.length; k++) {
                    modeOfPay_Options.push([k, 'obj', modeOfPayName[k], "cash.png"]);
                }

                paymentModeWiseSalRptLayout = salRptTabbar.tabs("a2").attachLayout('1C');
                paymentModeWiseSalRptLayout.cells("a").hideHeader();
                paymodeWiseSalRptToolbar = paymentModeWiseSalRptLayout.cells("a").attachToolbar();
                paymodeWiseSalRptToolbar.setIconsPath("images/icon/default_18/");
                paymodeWiseSalRptToolbar.setSkin("dhx_skyblue");
                paymodeWiseSalRptToolbar.addButtonSelect("sal_rpt_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                paymodeWiseSalRptToolbar.addButtonSelect("sal_rpt_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                paymodeWiseSalRptToolbar.setItemText('sal_rpt_month_filter', monthNames[month]);
                paymodeWiseSalRptToolbar.setItemText('sal_rpt_year_filter', Date.today().getFullYear());
                paymodeWiseSalRptToolbar.addSeparator();
                paymodeWiseSalRptToolbar.addText("ModeOfPayment", '2', "Mode Of Payment");
                paymodeWiseSalRptToolbar.addButtonSelect("modeofpay_filter", '3', "Mode Of Payment", modeOfPay_Options, '', '', true, true, 10, 'select');
                if (SalPaymodewiseRptAll != 1) {
                    paymodeWiseSalRptToolbar.setListOptionSelected("modeofpay_filter", 2);
                } else {
                    paymodeWiseSalRptToolbar.setItemText('modeofpay_filter', "All");
                    paymodeWiseSalRptToolbar.setListOptionSelected('modeofpay_filter', 0);
                }
                paymodeWiseSalRptToolbar.addButton("exportPaymodeWiseSalReport", '6', "Export", "excel.png");
                paymodeWiseSalRptToolbar.addSeparator();
                paymodeWiseSalRptToolbar.addButton("createBSEntry", '7', 'Create BS Entries', "../account.png", "../account_dis.png");
                paymodeWiseSalRptToolbar.disableItem("createBSEntry");
                //paymodeWiseSalRptToolbar.hideItem("createBSEntry");
                /*if(unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo)!=4){
                 paymodeWiseSalRptToolbar.hideItem("createBSEntry");
                 }*/
                paymentModeWiseSalaryRptGrid = paymentModeWiseSalRptLayout.cells("a").attachGrid();
                paymentModeWiseSalaryRptGrid.setImagePath("assets/grid/codebase/imgs/");
                paymentModeWiseSalaryRptGrid.setSkin("dhx_skyblue");
                paymentModeWiseSalaryRptGrid.setHeader("<div>SlNo<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='0' class='btn_BnkSalRptSort' /></div>,User ID,Name,Branch,Month,Year,<div>Take Home Salary<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_BnkSalRptSort' /></div>,Account No,Bank Name,Status,#master_checkbox");
                paymentModeWiseSalaryRptGrid.attachHeader(",#text_filter,#text_filter,#text_filter,,,,,,,");
                paymentModeWiseSalaryRptGrid.attachFooter("Total amount,#cspan,#cspan,#cspan,#cspan,#cspan,<div id='sr_q'>{#stat_total}</div>,#cspan,#cspan,#cspan,#cspan");
                paymentModeWiseSalaryRptGrid.setInitWidths("30,0,*,*,*,*,*,*,*,50,50");
                paymentModeWiseSalaryRptGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ch");
                paymentModeWiseSalaryRptGrid.init();
                paymentModeWiseSalaryRptGrid.enableRowsHover(true, "bonusReportHover");
                r1 = paymentModeWiseSalaryRptGrid.getFilterElement(2).placeholder = "Enter Staff Name";
                r2 = paymentModeWiseSalaryRptGrid.getFilterElement(3).placeholder = "Enter Branch Name";
                //r1.attr("placeholder","Enter A Name");
                //r1.setPlaceholder("Enter Staff Name");
                //r2.setPlaceholder("Enter Branch Name");
                paymentModeWiseSalaryRptGrid.enableColSpan(true);
                paymentModeWiseSalaryRptGrid.setColAlign("center,left,left,left,left,left,left,left,left,center,center");
                paymentModeWiseSalaryRptGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false");
                var srtFlg = 0;
                $('.btn_BnkSalRptSort').click(function () {
                    var colId = $(this).attr("colNum")
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        paymentModeWiseSalaryRptGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        paymentModeWiseSalaryRptGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                });
                sel_month = null;
                sel_modeOfPay = null;
                flag = 0;
                paymodesalrpt_sel_month = Date.today().getMonth() + 1;
                paymodesalrpt_sel_year = Date.today().getFullYear();
                paymodeWiseSalRptToolbar.attachEvent("onClick", function (id) {
                    var sel_month = paymodeWiseSalRptToolbar.getListOptionSelected("sal_rpt_month_filter");
                    var sel_modeOfPay = paymodeWiseSalRptToolbar.getListOptionSelected("modeofpay_filter");
                    var pId = paymodeWiseSalRptToolbar.getParentId(id);
                    if (pId == 'sal_rpt_month_filter' || pId == "sal_rpt_year_filter" || pId == "modeofpay_filter" || pId == "bankName") {
                        paymentModeWiseSalaryRptGrid.setColumnHidden(10, true);
                        paymentModeWiseSalaryRptGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                        var params = "";
                        if (pId == 'sal_rpt_month_filter') {
                            paymodeWiseSalRptToolbar.setListOptionSelected("modeofpay_filter", 0);
                            sel_modeOfPay = 0;
                            paymodeWiseSalRptToolbar.removeItem("bankName");
                            paymodeWiseSalRptToolbar.disableItem("createBSEntry");
                            paymentModeWiseSalaryRptGrid.uncheckAll();
                            params = '';
                            if (paymodeWiseSalRptToolbar.getListOptionSelected("sal_rpt_year_filter")) {
                                paymodesalrpt_sel_year = paymodeWiseSalRptToolbar.getListOptionSelected("sal_rpt_year_filter");
                                paymodesalrpt_sel_year = paymodesalrpt_sel_year.substr(1);
                            }
                            id = id.substr(1);
                            paymodesalrpt_sel_month = id;
                            if (SalPaymodewiseRptAll != 1) {
                                paymentModeWiseSalaryRptGrid.setColumnHidden(7, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(8, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(10, false);
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "0");
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "1");
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "3");
                                paymodeWiseSalRptToolbar.setListOptionSelected("modeofpay_filter", "2");
                                sel_modeOfPay = 2;
                                paymodeWiseSalRptToolbar.enableItem("createBSEntry");
                            }
                            params = "att_month=" + paymodesalrpt_sel_month + "&att_year=" + paymodesalrpt_sel_year + "& sel_modeOfPay=" + sel_modeOfPay;
                        }
                        if (pId == 'sal_rpt_year_filter') {
                            paymodeWiseSalRptToolbar.setListOptionSelected("modeofpay_filter", 0);
                            sel_modeOfPay = 0;
                            paymodeWiseSalRptToolbar.removeItem("bankName");
                            paymodeWiseSalRptToolbar.disableItem("createBSEntry");
                            paymentModeWiseSalaryRptGrid.uncheckAll();
                            if (paymodeWiseSalRptToolbar.getListOptionSelected("sal_rpt_month_filter")) {
                                paymodesalrpt_sel_month = paymodeWiseSalRptToolbar.getListOptionSelected("sal_rpt_month_filter");
                                paymodesalrpt_sel_month = paymodesalrpt_sel_month.substr(1);
                            }
                            params = '';
                            id = id.substr(1);
                            paymodesalrpt_sel_year = id;
                            if (SalPaymodewiseRptAll != 1) {
                                paymentModeWiseSalaryRptGrid.setColumnHidden(7, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(8, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(10, false);
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "0");
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "1");
                                paymodeWiseSalRptToolbar.hideListOption('modeofpay_filter', "3");
                                paymodeWiseSalRptToolbar.setListOptionSelected("modeofpay_filter", "2");
                                sel_modeOfPay = 2;
                                paymodeWiseSalRptToolbar.enableItem("createBSEntry");
                            }
                            if (sel_modeOfPay == null)
                                sel_modeOfPay = parseInt("0");
                            params = "att_month=" + paymodesalrpt_sel_month + "&att_year=" + paymodesalrpt_sel_year + "& sel_modeOfPay=" + sel_modeOfPay;
                        } else if (pId == "modeofpay_filter") {
                            paymodeWiseSalRptToolbar.disableItem("createBSEntry");
                            if (id == 0 || id == 2 || id == 3) {
                                if (flag == 1) {
                                    paymodeWiseSalRptToolbar.removeItem("bankName");
                                }
                            }
                            if (id == 2 || id == 3) {
                                paymentModeWiseSalaryRptGrid.setColumnHidden(7, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(8, true);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(10, false);
                                paymodeWiseSalRptToolbar.enableItem("createBSEntry");
                            } else {
                                if (id == 1) {
                                    if (flag == 1) {
                                        paymodeWiseSalRptToolbar.removeItem("bankName");
                                    }

                                    bankNameArray = [];
                                    bankIndexArray = [];
                                    bank_Options = [];
                                    flag = 1;
                                    $.post(preTally.Initialize.encryptURL("warehouse/getBankPaySalRpt.php"), function (data, status) {
                                        bankdetails = JSON.parse(data);
                                        for (i = 0; i < bankdetails.length; i++) {
                                            bankIndexArray.push('B_' + bankdetails[i].Sal_BnkId);
                                            bankNameArray.push(bankdetails[i].Sal_BnkName);
                                        }
                                        for (i = 0; i < bankNameArray.length; i++) {
                                            bank_Options.push([bankIndexArray[i], 'obj', bankNameArray[i], "bank.png"]);
                                        }
                                        paymodeWiseSalRptToolbar.addButtonSelect("bankName", '4', "Bank Name", bank_Options, '', '', true, true, 10, 'select');
                                    });
                                }
                                paymentModeWiseSalaryRptGrid.setColumnHidden(7, false);
                                paymentModeWiseSalaryRptGrid.setColumnHidden(8, false);
                            }
                            if (sel_month == null)
                                sel_month = Date.today().getMonth() + 1;
                            else {
                                sel_month = sel_month.substr(1);
                            }

                            params = "att_month=" + paymodesalrpt_sel_month + "&att_year=" + paymodesalrpt_sel_year + "& sel_modeOfPay=" + id;
                        } else if (pId == "bankName") {
                            if (sel_modeOfPay == null)
                                sel_modeOfPay = parseInt("0");
                            if (sel_month == null)
                                sel_month = Date.today().getMonth() + 1;
                            else {
                                sel_month = sel_month.substr(1);
                            }
                            var bnk_id = id.substr(2);
                            selected_bnk_id = bnk_id;
                            paymentModeWiseSalaryRptGrid.setColumnHidden(10, false);
                            paymodeWiseSalRptToolbar.enableItem("createBSEntry");
                            params = "att_month=" + paymodesalrpt_sel_month + "&att_year=" + paymodesalrpt_sel_year + "& sel_modeOfPay=" + sel_modeOfPay + "&Sal_BnkId=" + bnk_id;
                        }
                        preTally.Settings.progressOn(true, paymentModeWiseSalRptLayout, null);
                        paymentModeWiseSalaryRptGrid.getFilterElement(1).value = "";
                        paymentModeWiseSalaryRptGrid.getFilterElement(2).value = "";
                        paymentModeWiseSalaryRptGrid.getFilterElement(3).value = "";
                        paymentModeWiseSalaryRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/paymentModeWiseSalaryRptGrid.php&" + params));
                        preTally.Settings.progressOff(true, paymentModeWiseSalRptLayout, null);
                    }
                    if (id == 'createBSEntry') {
                        preTally.UserProfile.createSalBSEntries(sel_modeOfPay, paymodesalrpt_sel_month, paymodesalrpt_sel_year);
                    }
                    if (id == "exportPaymodeWiseSalReport") {
                        if (sel_modeOfPay == null)
                            sel_modeOfPay = parseInt("0");
                        if (flag == 1) {
                            var sel_bank = paymodeWiseSalRptToolbar.getListOptionSelected("bankName");
                        } else {
                            sel_bank = "abc";
                        }
                        preTally.UserProfile.exportPaymodeWiseSalReport(paymodesalrpt_sel_month, paymodesalrpt_sel_year, sel_modeOfPay, sel_bank, useridFilter, nameFilter, branchFilter);
                    }

                });
                paymentModeWiseSalaryRptGrid.attachEvent("onFilterEnd", function (elements) {
                    if (paymentModeWiseSalaryRptGrid.getRowsNum() == 0) {
                        if (paymentModeWiseSalaryRptGrid.doesRowExist("msgRow"))
                            paymentModeWiseSalaryRptGrid.deleteRow("msgRow");
                        paymentModeWiseSalaryRptGrid.addRow('msgRow', "No records found");
                        paymentModeWiseSalaryRptGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        paymentModeWiseSalaryRptGrid.setColspan("msgRow", 0, 10);
                    }
                    useridFilter = paymentModeWiseSalaryRptGrid.getFilterElement(1).value;
                    nameFilter = paymentModeWiseSalaryRptGrid.getFilterElement(2).value;
                    branchFilter = paymentModeWiseSalaryRptGrid.getFilterElement(3).value;
                });
                if (sel_month == null && sel_modeOfPay == null)
                    params = "att_month=" + "abc" + "& sel_modeOfPay=" + "abc";
                paymentModeWiseSalaryRptGrid.loadXML(preTally.Initialize.encryptURL("requisites/paymentModeWiseSalaryRptGrid.php&" + params));
                PayModRptFlag = 1
            }
        },
        createSalBSEntries: function (payType, payMonth, payYear) {
            if (payType == 1 || payType == 3) {
                var dhxSalBSEntriesWin = new dhtmlXWindows();
                var winWidth = 400;
                var winHeight = 250;
                if (payType == 3) {
                    winWidth = 800;
                    winHeight = 400;
                }

            }
            var SalChkdIds = paymentModeWiseSalaryRptGrid.getCheckedRows(10);
            if (SalChkdIds != "") {
                if (payType == 1 || payType == 3) {
                    SalBSEntriesWin = dhxSalBSEntriesWin.createWindow("salBSEntries", 300, 100, winWidth, winHeight);
                    SalBSEntriesWin.button("minmax1").hide();
                    SalBSEntriesWin.button("minmax2").hide();
                    SalBSEntriesWin.button("park").hide();
                    SalBSEntriesWin.center();
                    SalBSEntriesWin.setModal(true);
                }
                if (payType == 1) {

                    SalBSEntriesWin.setText("Create Salary BS Entries");
                    var BSEntryForm = SalBSEntriesWin.attachForm();
                    BSEntryForm.loadStruct(preTally.Initialize.encryptURL("requisites/SalBSEntryForm.php&frmtype=" + payType), function () {
                        BSEntryForm.setItemValue("CmpnyAccId", SalChkdIds);
                        var ChkIds = SalChkdIds.split(",");
                        var Salsum = 0;
                        ChkIds.forEach(function (ids) {
                            Salsum = Salsum + parseInt(paymentModeWiseSalaryRptGrid.cells(ids, 6).getValue());
                        });
                        BSEntryForm.setItemValue("total_amt", Salsum);
                        BSEntryForm.attachEvent("onButtonClick", function (id) {
                            if (id == "createBSEntry") {

                                BSEntryForm.setItemValue("rowsId", SalChkdIds);
                                BSEntryForm.setItemValue("payType", payType);
                                BSEntryForm.setItemValue("attMonth", payMonth);
                                BSEntryForm.setItemValue("attYear", payYear);
                                BSEntryForm.send(preTally.Initialize.encryptURL("warehouse/generateSalaryEntries.php"), function (loader, response) {
                                    preTally.UserProfile.validateSalEntries(response);
                                    SalBSEntriesWin.close();
                                    paymentModeWiseSalaryRptGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                                    var params = "att_month=" + payMonth + "&att_year=" + payYear + "&sel_modeOfPay=" + payType + "&Sal_BnkId=" + selected_bnk_id;
                                    paymentModeWiseSalaryRptGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/paymentModeWiseSalaryRptGrid.php&" + params), true, true);
                                });
                            } else if (id == "cancelBSEntry") {
                                SalBSEntriesWin.close();
                                paymentModeWiseSalaryRptGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                            }
                        });
                    });
                } else if (payType == 2) {
                    //Cash entries
                    $.post(preTally.Initialize.encryptURL("warehouse/generateSalaryEntries.php"), {rowsId: SalChkdIds, attMonth: payMonth, attYear: payYear, payType: 2}, function (data) {
                        preTally.UserProfile.validateSalEntries(data);
                        paymentModeWiseSalaryRptGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                        var params = "att_month=" + payMonth + "&att_year=" + payYear + "&sel_modeOfPay=" + payType;
                        paymentModeWiseSalaryRptGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/paymentModeWiseSalaryRptGrid.php&" + params), true, true);
                        //SalBSEntriesWin.close();
                    });
                } else if (payType == 3) {
                    SalBSEntriesWin.setText("Create Salary BS Entries-Cheque");
                    BSEntryChqGrid = SalBSEntriesWin.attachGrid();
                    var params = "&sel_modeOfPay=" + payType + "&att_month=" + payMonth + "att_year=" + payYear;
                    BSEntryChqGrid.setHeader("SlNo,Name,Branch,THS,Account,Employee Bank Name,Company Account,Cheque");
                    BSEntryChqGrid.setColTypes("ro,ro,ro,ro,ro,ro,combo,combo");
                    BSEntryChqGrid.setInitWidths("50,*,100,100,120,120,120,120");
                    BSEntryChqGrid.enableColSpan(true);
                    BSEntryChqGrid.enableEditEvents(true, false, true);
                    BSEntryChqGrid.attachFooter("Total,#cspan,#cspan,{#stat_total},#cspan,#cspan,#cspan,#cspan");
                    BSEntryChqGrid.init();
                    $.post(preTally.Initialize.encryptURL("requisites/SalChqPayGrid.php"), {att_month: payMonth, att_year: payYear, SalChkdIds: SalChkdIds, sel_modeOfPay: payType}, function (response) {
                        BSEntryChqGrid.parse(response, function () {
                            var assigndChqs = new Array();
                            var bankaccResponse;
                            $.post(preTally.Initialize.encryptURL("requisites/bankaccounts.php"), function (response) {
                                bankaccResponse = response;
                            })
                            BSEntryChqGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                                if (stage == 0) {
                                    var Acc_id = 0;
                                    if (cInd == 6) {
                                        var SalBankAccCombo = BSEntryChqGrid.cells(rId, 6).getCellCombo();
                                        SalBankAccCombo.setOptionWidth(150);
                                        SalBankAccCombo.load(bankaccResponse);
                                        SalBankAccCombo.attachEvent("onChange", function () {
                                            BSEntryChqGrid.setUserData(rId, "BA_Id", SalBankAccCombo.getSelectedValue());
                                        });
                                    } else if (cInd == 7) {
                                        var SalChqCombo = BSEntryChqGrid.cells(rId, 7).getCellCombo();
                                        SalChqCombo.setOptionWidth(150);
                                        var chqSelArray = assigndChqs;
                                        $.post(preTally.Initialize.encryptURL("requisites/getAccountChqLeaf.php&BA_Id=" + BSEntryChqGrid.getUserData(rId, "BA_Id")), {chqSelArray: chqSelArray.join()}, function (response) {
                                            SalChqCombo.load(response);
                                        });
                                        SalChqCombo.attachEvent("onClose", function () {
                                            var chqid = SalChqCombo.getSelectedValue();
                                            // assigndChqs=BSEntryChqGrid.getUserData("","Assgnd_CHQ");
                                            var indx = assigndChqs.indexOf(BSEntryChqGrid.getUserData(rId, "CHQ_Id"));
                                            if (indx != -1) {
                                                assigndChqs.splice(indx, 1);
                                            }
                                            if (chqid != 'ZeroVal') {
                                                assigndChqs.push(chqid);
                                                BSEntryChqGrid.setUserData("", "Assgnd_CHQ", assigndChqs);
                                                BSEntryChqGrid.setUserData(rId, "CHQ_Id", chqid);
                                            }
                                        });
                                    }
                                } else if (stage == 2) {
                                    if (cInd == 7) {

                                    }
                                }
                                return true;
                            });
                        });
                    });
                    SalBSEntriesWin.attachStatusBar({text: "<input type='button' id='btnChqBSSave' style='float:right;' value='Save' onClick='preTally.UserProfile.createChqSalBSEntries(" + payMonth + "," + payYear + ");'></input>", height: 30});
                }
            } else {
                dhtmlx.message({text: "Select Salary Entity"});
            }
        }, validateSalEntries: function (data) {
            if (data == "Item_err") {
                dhtmlx.alert({
                    title: "Operation Failed",
                    type: "alert-error",
                    text: "Salary Item not defined"
                });
            } else if (data == "null_err") {
                dhtmlx.alert({
                    title: "Operation Failed",
                    type: "alert-error",
                    text: "Selected Entries are already generated in Balance Sheet"
                });
            } else {
                msg = jQuery.parseJSON(data)
                if (msg.acc_err > 0) {
                    dhtmlx.alert({
                        title: "Operation Failed",
                        type: "alert-error",
                        text: msg.acc_err + " users having invalid Payment Details."
                    });
                    msg.err_ids.forEach(function (rowId) {
                        SalaryRptGrid.setRowColor(rowId, "orange");
                    });
                } else {
                    dhtmlx.message({text: msg.counter + " Entries Created"});
                }
            }
        }, createChqSalBSEntries: function (month, year) {
            var chqSalObject = {};
            var errorFlag = 0;
            BSEntryChqGrid.forEachRow(function (rId) {
                if (BSEntryChqGrid.getUserData(rId, "BA_Id") == "") {
                    BSEntryChqGrid.setCellTextStyle(rId, 6, "border:1px solid red;");
                    errorFlag = 1;
                } else
                    BSEntryChqGrid.setCellTextStyle(rId, 6, "");
                if (BSEntryChqGrid.getUserData(rId, "CHQ_Id") == "") {
                    BSEntryChqGrid.setCellTextStyle(rId, 7, "border:1px solid red;");
                    errorFlag = 1;
                } else
                    BSEntryChqGrid.setCellTextStyle(rId, 7, "");
                var chqSalDetails = {};
                chqSalDetails['ESR_Id'] = rId;
                chqSalDetails['BA_Id'] = BSEntryChqGrid.getUserData(rId, "BA_Id");
                chqSalDetails['CL_Id'] = BSEntryChqGrid.getUserData(rId, "CHQ_Id");
                chqSalObject[rId] = chqSalDetails;
            });
            if (errorFlag == 0) {
                $.post(preTally.Initialize.encryptURL("warehouse/generateSalaryEntries.php"), {chqSalEntryDetails: chqSalObject, attMonth: month, attYear: year, payType: 'CHQ'}, function (data) {
                    preTally.UserProfile.validateSalEntries(data);
                    SalBSEntriesWin.close();
                    paymentModeWiseSalaryRptGrid.hdr.rows[1].cells[10].getElementsByTagName("INPUT")[0].checked = false;
                }).done(function (data) {
                    var params = "att_month=" + month + "&att_year=" + year + "&sel_modeOfPay=3";
                    paymentModeWiseSalaryRptGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/paymentModeWiseSalaryRptGrid.php&" + params), true, true);
                });
            }
        },
        SalaryReportSentMail: function () {
            var GenSalReportGridCkedRw = SalaryRptGrid.getCheckedRows(27);
            if ((GenSalReportGridCkedRw != "")) {
                if (GenSalReportGridCkedRw != 0) {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    $.post(preTally.Initialize.encryptURL("warehouse/generatedSalarySlipSendMail.php"), {rowId: GenSalReportGridCkedRw}, function (data, status) {
                        preTally.Settings.progressOff(true, dhxLayout, null);
                        dhtmlx.message({text: data});
                    });
                    var params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                    if (salrpt_sel_month == null) {
                        params = "att_month=" + "abc";
                    } else {
                        params = "att_month=" + salrpt_sel_month + "&att_year=" + salrpt_sel_year;
                    }
                    var filterValue = new Array($('#SlryRptName').val(), $('#SlryRptLoc').val(), $("#ESR_Status").val(), $('#hid_SalRptSort').val(), $('#hid_SalSortCol').val());
                    var filtr = params + "&Filters=" + filterValue;
                    SalaryRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salaryRptGrid.php&" + filtr));
                    if (SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked == true) {
                        SalaryRptGrid.hdr.rows[2].cells[27].getElementsByTagName("INPUT")[0].checked = false;
                    }


                } else {
                    dhtmlx.message({text: "Please Select Data From Grid"});
                }
            } else {
                dhtmlx.message({text: "Please Select Data From Grid"});
            }
        },
        exportSalPFDetails: function (MN, YR) {
            var filterValue = new Array(MN, YR, $('#expPF_USName').val(), $('#expPF_LCName').val(), "PF");
            $.post(preTally.Initialize.encryptURL('warehouse/salaryPFReportExcelExport.php'), {filterParams: filterValue}, function (data) {
                fileName = data.split("XL_");
                if (fileName[1]) {
                    document.location = "uploads/excelFile/" + fileName[1];
                }
            });
        },
        exportSalESIDetails: function (MN, YR) {
            var filterValue = new Array(MN, YR, $('#expESI_USName').val(), $('#expESI_LCName').val(), "ESI");
            $.post(preTally.Initialize.encryptURL('warehouse/salaryPFReportExcelExport.php'), {filterParams: filterValue}, function (data) {
                fileName = data.split("XL_");
                if (fileName[1]) {
                    document.location = "uploads/excelFile/" + fileName[1];
                }
            });
        },
        exportSalaryReport: function (sel_month, sel_year, nameFilter, branchFilter, salApprovedBlockedFilter) {
            var filterValue = new Array(sel_month, sel_year, $('#SlryRptName').val(), $('#SlryRptLoc').val(), $("#ESR_Status").val(), $('#hid_SalRptSort').val(), $('#hid_SalSortCol').val());
            $.post(preTally.Initialize.encryptURL('warehouse/salaryReportExcelExport.php'), {filterParams: filterValue},
                    function (data) {
                        fileName = data.split("XL_");
                        if (fileName[1]) {
                            document.location = "uploads/excelFile/" + fileName[1];
                        }
                        //preTally.Settings.progressOff(true, dhxLayout, null);
                    });
        },
        exportPaymodeWiseSalReport: function (month, year, paymode, sel_bank, useridFilter, nameFilter, branchFilter) {
            params = "month=" + month + "& paymode=" + paymode + "& BA_Id=" + sel_bank + "& useridFilter=" + useridFilter + "& nameFilter=" + nameFilter + "& branchFilter=" + branchFilter;
            $.post(preTally.Initialize.encryptURL('warehouse/paymodeWiseSalRptExcelExport.php'), {month: month, year: year, paymode: paymode, BA_Id: sel_bank, useridFilter: useridFilter, nameFilter: nameFilter, branchFilter: branchFilter},
                    function (data) {

                        fileName = data.split("XL_");
                        if (fileName[1]) {
                            document.location = "uploads/excelFile/" + fileName[1];
                        }
                        preTally.Settings.progressOff(true, dhxLayout, null);
                    });
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptmylvTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptmylvTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        applyLeave: function () {
            var dhxApplyLeaveWin = new dhtmlXWindows();
            var applyLeaveWin = dhxApplyLeaveWin.createWindow("applyLeave", 500, 100, 400, 450);
            applyLeaveWin.button("minmax1").hide();
            applyLeaveWin.button("minmax2").hide();
            applyLeaveWin.button("park").hide();
            applyLeaveWin.center();
            applyLeaveWin.setModal(true);
            applyLeaveWin.setText("Apply Leave");
            var applyLeaveForm = applyLeaveWin.attachForm();
            applyLeaveForm.loadStruct(preTally.Initialize.encryptURL("requisites/applyForLeave.php"), function () {
                applyLeaveForm.setCalendarDateFormat("LR_FromDate", "%d.%m.%Y", "%d.%m.%Y");
                applyLeaveForm.setCalendarDateFormat("LR_ToDate", "%d.%m.%Y", "%d.%m.%Y");
                applyLeaveForm.disableItem("LR_duration");
                comboaplyforLeaveDuration = applyLeaveForm.getCombo('LR_duration');
                applyLeaveForm.attachEvent("onChange", function (name) {
                    if (name == "LR_duration") {
                        if (comboaplyforLeaveDuration.getSelectedValue() != 0) {
                            applyLeaveForm.setItemValue("LR_NumOFDays", 0.5);
                        } else {
                            applyLeaveForm.setItemValue("LR_NumOFDays", date_differ);
                        }
                    } else if (name == "LR_FromDate")
                    {
                        applyLeaveForm.setItemValue("LR_ToDate", "");
                        applyLeaveForm.setItemValue("LR_NumOFDays", "");
                    } else if (name == "LR_ToDate") {
                        FromDatevalue = applyLeaveForm.getItemValue("LR_FromDate");
                        toDatevalue = applyLeaveForm.getItemValue("LR_ToDate");
                        //Get 1 day in milliseconds
                        var one_day = 1000 * 60 * 60 * 24;
                        // Convert both dates to milliseconds
                        var date1_ms = FromDatevalue.getTime();
                        var date2_ms = toDatevalue.getTime();
                        // Calculate the difference in milliseconds
                        var difference_ms = date2_ms - date1_ms;
                        date_diff = difference_ms / one_day;
                        date_diff = date_diff + 1;
                        date_differ = date_diff.toFixed(1)
                        if (date_differ == 1.0) {
                            applyLeaveForm.enableItem("LR_duration");
                            applyLeaveForm.setItemValue("LR_NumOFDays", date_differ);
                        } else {
                            comboaplyforLeaveDuration.setComboValue(0);
                            applyLeaveForm.disableItem("LR_duration");
                            applyLeaveForm.setItemValue("LR_NumOFDays", date_differ);
                        }

                    }
                });
                leaveCombo = applyLeaveForm.getCombo('LT_Id');
                leaveCombo.attachEvent("onChange", function () {
                    leaveType = leaveCombo.getSelectedValue();
                    if ((leaveType != " ") && (leaveType != "0")) {
                        if (applyLeaveForm.getCalendar("LR_FromDate").getDate(true) != null && applyLeaveForm.getCalendar("LR_ToDate").getDate(true) != null)
                        {
                            var params = "lType=" + leaveType + "&fromDate=" + applyLeaveForm.getCalendar("LR_FromDate").getDate(true) + "&toDate=" + applyLeaveForm.getCalendar("LR_ToDate").getDate(true);
                            $.post(preTally.Initialize.encryptURL("warehouse/checkLeaveAvailable.php&" + params), function (data) {
                                if (data == "1110") {
                                    dhtmlx.message({text: "You are not elegible for this leave"})
                                    applyLeaveForm.setItemValue("LR_eligibleLeave", "");
                                } else if (data == "YErr") {
                                    dhtmlx.message({text: "Date on Different years cannot be applied"})
                                    applyLeaveForm.setItemValue("LR_eligibleLeave", "");
                                } else {
                                    if (data == "") {
                                        data = "0.0";
                                    }
                                    applyLeaveForm.setItemValue("LR_eligibleLeave", data);
                                }
                            });
                        } else {

                            dhtmlx.message({text: "Select From and To Dates"});
                            applyLeaveForm.setValidateCss('LR_FromDate', true, 'validate_red');
                            applyLeaveForm.setValidateCss('LR_ToDate', true, 'validate_red');
                        }

                    } else {
                        dhtmlx.message({text: "Please choose leave type"});
                    }

                });
                var fromDate = applyLeaveForm.getCalendar("LR_FromDate");
                var toDate = applyLeaveForm.getCalendar("LR_ToDate");
                fromDate.attachEvent("onClick", function (d) {
                    toDate.setSensitiveRange(d.toString('dd.MM.yyyy'), null);
                });
                applyLeaveForm.attachEvent("onInputChange", function (name, value, applyLeaveForm) {
                    if (name == "LR_ToDate") {
                        if (FromDatevalue > toDatevalue) {
                            dhtmlx.message({text: "To date should be after from date"})
                            applyLeaveForm.disableItem("LR_duration");
                            applyLeaveForm.setItemValue("LR_NumOFDays");
                            comboaplyforLeaveDuration.setComboValue(0);
                        } else {
                            if (FromDatevalue == toDatevalue) {
                                applyLeaveForm.enableItem("LR_duration");
                                comboaplyforLeaveDuration.setComboValue(0);
                            }
                        }

                    }
                    if (name == "LR_FromDate") {
                        applyLeaveForm.disableItem("LR_duration");
                        comboaplyforLeaveDuration.setComboValue(0);
                    }
                });
                applyLeaveForm.attachEvent("onButtonClick", function (name, value) {
                    var eligibleLeave = applyLeaveForm.getItemValue("LR_eligibleLeave");
                    var totalLeave = applyLeaveForm.getItemValue("LR_NumOFDays");
                    eligibleLeave = parseFloat(eligibleLeave);
                    totalLeave = parseFloat(totalLeave);
                    if (name == 'CancelApplyLeave') {
                        applyLeaveWin.close();
                    } else if (name == 'saveApplyLeave') {
                        var ApplyLvFrm = applyLeaveForm.validate();
                        if (ApplyLvFrm && (totalLeave <= eligibleLeave)) {
                            console.log('tl:' + totalLeave + 'el:' + eligibleLeave);
                            if ((totalLeave < 0) || (eligibleLeave == "")) {
                                dhtmlx.message({text: "Total Leave Applied is not Valid"});
                            } else {
                                var params = "name1=" + name;
                                applyLeaveForm.send(preTally.Initialize.encryptURL("warehouse/applyLeave.php&" + params), function (loader, response) {
                                    dhtmlx.message({text: response})
                                    var searchFromdate = myLeaveToolBar.getValue("myleave_date_from");
                                    var searchTodate = myLeaveToolBar.getValue("myleave_date_till");
                                    var params = "appFromDate=" + searchFromdate + "&appToDate=" + searchTodate;
                                    myLeaveGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/myleavegrid.php&" + params));
                                    applyLeaveWin.close();
                                    Att_scheduler.clearAll();
                                    Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
                                });
                            }
                        } else if (totalLeave > eligibleLeave) {
                            dhtmlx.message({text: "Total Leave limit exceeded"});
                        }
                    }
                });
            });
        },
        applyLateEntry: function () {
            var dhxApplyLateEntryWin = new dhtmlXWindows();
            var applyLateEntryWin = dhxApplyLateEntryWin.createWindow("applyLateEntry", 500, 100, 400, 450);
            applyLateEntryWin.button("minmax1").hide();
            applyLateEntryWin.button("minmax2").hide();
            applyLateEntryWin.button("park").hide();
            applyLateEntryWin.center();
            applyLateEntryWin.setModal(true);
            applyLateEntryWin.setText("Apply Late Entry");
            var applyLateEntryForm = applyLateEntryWin.attachForm();
            applyLateEntryForm.loadStruct(preTally.Initialize.encryptURL("requisites/applyForLateEntry.php"), function () {
                applyLateEntryForm.setCalendarDateFormat("Att_Lt_From_Date", "%d.%m.%Y", "%d.%m.%Y");
                applyLateEntryForm.setCalendarDateFormat("Att_Lt_To_Date", "%d.%m.%Y", "%d.%m.%Y");
                applyLeaveForm.attachEvent("onChange", function (name) {
                    if (name == "Att_Lt_From_Date")
                    {
                        applyLateEntryForm.setItemValue("Att_Lt_To_Date", "");
                        applyLateEntryForm.setItemValue("Att_Lt_NumOFDays", "");
                    } else if (name == "Att_Lt_To_Date") {
                        FromDatevalue = applyLateEntryForm.getItemValue("Att_Lt_From_Date");
                        toDatevalue = applyLateEntryForm.getItemValue("Att_Lt_To_Date");
                        //Get 1 day in milliseconds
                        var one_day = 1000 * 60 * 60 * 24;
                        // Convert both dates to milliseconds
                        var date1_ms = FromDatevalue.getTime();
                        var date2_ms = toDatevalue.getTime();
                        // Calculate the difference in milliseconds
                        var difference_ms = date2_ms - date1_ms;
                        date_diff = difference_ms / one_day;
                        date_diff = date_diff + 1;
                        date_differ = date_diff.toFixed(1)
                        if (date_differ == 1.0) {
                            // applyLeaveForm.enableItem("LR_duration");
                            applyLateEntryForm.setItemValue("Att_Lt_NumOFDays", date_differ);
                        } else {
                            // comboaplyforLeaveDuration.setComboValue(0);
                            // applyLeaveForm.disableItem("LR_duration");
                            applyLateEntryForm.setItemValue("Att_Lt_NumOFDays", date_differ);
                        }

                    }
                });

                applyLeaveForm.attachEvent("onChange", function (name) {
                    if (name == "Att_Lt_From_Date") {
                        FromDatevalue = applyLateEntryForm.getItemValue("Att_Lt_From_Date");
                    }
                    if (name == "Att_Lt_To_Date") {
                        toDatevalue = applyLateEntryForm.getItemValue("Att_Lt_To_Date");
                    }
                });

                var fromDate = applyLateEntryForm.getCalendar("Att_Lt_From_Date");
                var toDate = applyLateEntryForm.getCalendar("Att_Lt_To_Date");

                fromDate.attachEvent("onClick", function (d) {
                    toDate.setSensitiveRange(d.toString('dd.MM.yyyy'), null);
                });
                applyLateEntryForm.attachEvent("onInputChange", function (name, value, applyLateEntryForm) {
                    if (name == "Att_Lt_To_Date") {
                        if (FromDatevalue > toDatevalue) {
                            dhtmlx.message({text: "To date should be after from date"})
                        }
                    }
                });
                applyLateEntryForm.attachEvent("onButtonClick", function (name, value) {
                    if (name == 'CancelApplyLateEntry') {
                        applyLateEntryWin.close();
                    } else if (name == 'saveApplyLateEntry') {
                        var ApplyLvFrm = applyLateEntryForm.validate();
                        if (ApplyLvFrm) {
                            // console.log('tl:' + totalLeave + 'el:' + eligibleLeave);
                            // if ((totalLeave < 0) || (eligibleLeave == "")) {
                            //     dhtmlx.message({text: "Total Leave Applied is not Valid"});
                            // } else {
                                var params = "name1=" + name;
                                applyLateEntryForm.send(preTally.Initialize.encryptURL("warehouse/applyLateEntry.php&" + params), function (loader, response) {
                                    dhtmlx.message({text: response})
                                    var searchFromdate = myLeaveToolBar.getValue("mylate_date_from");
                                    var searchTodate = myLeaveToolBar.getValue("mylate_date_till");
                                    var params = "appFromDate=" + searchFromdate + "&appToDate=" + searchTodate;
                                    myLateEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/mylateentrygrid.php&" + params));
                                    applyLateEntryWin.close();
                                    // Att_scheduler.clearAll();
                                    // Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
                                });
                            // }
                        }
                    }
                });
            });
        },
        menuMyAttendance: function () {
            if (!dhxMiddleBlockTabs.cells("menuMyAttendance")) {
                dhxMiddleBlockTabs.addTab("menuMyAttendance", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Attendance", 150);
                dhxMiddleBlockTabs.tabs("menuMyAttendance").setActive();
                dhxMiddleBlockLayout = dhxMiddleBlockTabs.cells("menuMyAttendance").attachLayout("3L");
                dhxMiddleBlockLayout.cells("a").setWidth(200);
                dhxMiddleBlockLayout.cells("a").hideHeader();
                dhxMiddleBlockLayout.cells("b").setHeight(100);
                dhxMiddleBlockLayout.cells("b").fixSize(true, true);
                dhxMiddleBlockLayout.cells("b").hideHeader();
                dhxMiddleBlockLayout.cells("c").hideHeader();
                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (myattendance_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    //mnthLimit=Date.today().getMonth()+1
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }
                //------
//                var tb_data_txt = ' <div class="tb_att_txt_secl">\
//                                        <div class="tb_f_days">Full Day : 0</div>\
//                                        <div class="tb_h_days">Half Day : 0</div>\
//                                        <div class="tb_o_days">Off Day : 0</div>\
//                                         <div class="tb_l_days">Leave : 0</div>\
//                                    </div>';
//
//                var tbRptObj = dhxMiddleBlockLayout.cells("c").attachStatusBar({
//                    text: tb_data_txt, // status bar text text:   tb_data_txt+"<div class='tb_cnt_tot' style='float:right;'>Total Number of Items : 0</div>",
//                    height: 23             // custom height
//                });

                MyAttendanceTabbar = dhxMiddleBlockLayout.cells("c").attachTabbar();
                MyAttendanceTabbar.enableTabCloseButton(false);
                MyAttendanceTabbar.addTab("AttendanceSheet", "Attendance", 130);
                MyAttendanceTabbar.addTab("LeaveSheet", "My Leave", 130);
                MyAttendanceTabbar.addTab("lateEntrySheet", "My Late Entries", 130);
                MyAttendanceTabbar.tabs("AttendanceSheet").setActive();
                AttendanceLayout = MyAttendanceTabbar.tabs("AttendanceSheet").attachLayout("1C");
                myattToolBar = MyAttendanceTabbar.tabs("AttendanceSheet").attachToolbar();
                myattToolBar.setIconsPath("images/icon/default_18/");
                myattToolBar.addButtonSelect("att_month_filter", '1', "Select Month", Months_Options, '', '', true, true, 10, 'select');
                myattToolBar.addButtonSelect("att_year_filter", '2', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                myattToolBar.setItemText('att_month_filter', monthNames[month]);
                myattToolBar.setItemText('att_year_filter', Date.today().getFullYear());
                myattToolBar.addText("spacer", '2', "");
                myattToolBar.disableItem("spacer");
                myattToolBar.setWidth("spacer", 600);
                myattToolBar.addButton("applyLeave", null, "Apply Leave", "new.gif");
                myattToolBar.addButton("applyLateEntry", null, "Apply Late Entry", "new.gif");
                myattendance_year = Date.today().getFullYear();
                myattendance_month = Date.today().getMonth() + 1;
                myattToolBar.attachEvent("onClick", function (id) {
                    if (id == "applyLeave") {
                        preTally.UserProfile.applyLeave();
                    }
                    if (id == "applyLateEntry") {
                        preTally.UserProfile.applyLateEntry();
                    }
                    var pId = myattToolBar.getParentId(id);
                    if (pId == 'att_month_filter') {
                        myattendance_month = id.substr(1);
                        if (myattToolBar.getListOptionSelected("att_year_filter")) {
                            myattendance_year = myattToolBar.getListOptionSelected("att_year_filter");
                            myattendance_year = myattendance_year.substr(1);
                        }
                        var firstDay = new Date(myattendance_year, myattendance_month - 1, 1).toString("yyyy-MM-dd");
                        var lastDay = new Date(myattendance_year, myattendance_month, 0).toString("yyyy-MM-dd");
                        var params = "ST_Date=" + firstDay + "&LST_Date=" + lastDay;
                        
                        Att_scheduler.clearAll();
//                        AttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendance.php&" + params), function () {
                        Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php"), function () {
                            Att_scheduler.setCurrentView(new Date(firstDay), "month");
//                            $('.tb_f_days').html("Full Day: " + AttGrid.getUserData("", "full_days") + " ");
//                            $('.tb_h_days').html("Half Day: " + AttGrid.getUserData("", "half_days") + " ");
//                            $('.tb_o_days').html("Off Day : " + AttGrid.getUserData("", "off_days") + " ");
//                            $('.tb_l_days').html("Leaves : " + AttGrid.getUserData("", "leave_days") + " ");
//                            AttGrid.attachEvent("onFilterEnd", function (elements) {
//                                if (AttGrid.getRowsNum() == 0) {
//                                    preTally.Functions.recordNotFound(AttGrid, 6);
//                                }
//                            });
                        });
                    }
                    if (pId == 'att_year_filter') {
                        myattendance_year = id.substr(1);
                        if (myattToolBar.getListOptionSelected("att_month_filter")) {
                            myattendance_month = myattToolBar.getListOptionSelected("att_month_filter");
                            myattendance_month = myattendance_month.substr(1);
                        }
                        var firstDay = new Date(myattendance_year, myattendance_month - 1, 1).toString("yyyy-MM-dd");
                        var lastDay = new Date(myattendance_year, myattendance_month, 0).toString("yyyy-MM-dd");
                        var params = "ST_Date=" + firstDay + "&LST_Date=" + lastDay;
                        
                        Att_scheduler.clearAll();
//                        AttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendance.php&" + params), function () {
                        Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php"), function () {
                            Att_scheduler.setCurrentView(new Date(firstDay), "month");
//                            $('.tb_f_days').html("Full Day: " + AttGrid.getUserData("", "full_days") + " ");
//                            $('.tb_h_days').html("Half Day: " + AttGrid.getUserData("", "half_days") + " ");
//                            $('.tb_o_days').html("Off Day : " + AttGrid.getUserData("", "off_days") + " ");
//                            $('.tb_l_days').html("Leaves : " + AttGrid.getUserData("", "leave_days") + " ");
//                            AttGrid.attachEvent("onFilterEnd", function (elements) {
//                                if (AttGrid.getRowsNum() == 0) {
//                                    preTally.Functions.recordNotFound(AttGrid, 6);
//                                }
//                            });
                        });
                    }

                })

                //my leave
                myLeaveToolBar = MyAttendanceTabbar.tabs("LeaveSheet").attachToolbar();
                myLeaveToolBar.setIconsPath("images/icon/default_18/");
                myLeaveToolBar.addText("text_from", null, "FromDate");
                myLeaveToolBar.addInput("myleave_date_from", null, "", 75);
                myLeaveToolBar.addButton("myleave_df_clear", null, "", "close.gif");
                myLeaveToolBar.addSeparator();
                myLeaveToolBar.addText("text_till", null, "Todate");
                myLeaveToolBar.addInput("myleave_date_till", null, "", 75);
                myLeaveToolBar.addButton("myleave_dt_clear", null, "", "close.gif");
                myLeaveToolBar.addSeparator();
                myLeaveToolBar.addButton("myleave_date_filter", null, "Search", "save.gif");
                myLeaveToolBar.addText("spacer", '9', "");
                myLeaveToolBar.disableItem("spacer");
                myLeaveToolBar.setWidth("spacer", 220);
                myLeaveToolBar.addButton("applyLeave", null, "Apply Leave", "new.gif");
                var mylvTb_Inp_Frm = myLeaveToolBar.getInput("myleave_date_from");
                mylvTb_Inp_Frm.setAttribute("readOnly", "true");
                mylvTb_Inp_Frm.onclick = function () {
                    if (myLeaveToolBar.getValue("myleave_date_till"))
                        preTally.UserProfile.setSens(mylvTb_Inp_Til, "max");
                }
                var mylvTb_Inp_Til = myLeaveToolBar.getInput("myleave_date_till");
                mylvTb_Inp_Til.setAttribute("readOnly", "true");
                mylvTb_Inp_Til.onclick = function () {
                    if (myLeaveToolBar.getValue("myleave_date_from"))
                        preTally.UserProfile.setSens(mylvTb_Inp_Frm, "min");
                }
                ptmylvTb_Calendar = new dhtmlXCalendarObject([mylvTb_Inp_Frm, mylvTb_Inp_Til]);
                ptmylvTb_Calendar.setDateFormat("%d.%m.%Y");
                myLeaveToolBar.attachEvent("onClick", function (id) {
                    if (id == "applyLeave") {
                        preTally.UserProfile.applyLeave();
                    }
                    if (id == "myleave_date_filter") {
                        var searchFromdate = myLeaveToolBar.getValue("myleave_date_from");
                        var searchTodate = myLeaveToolBar.getValue("myleave_date_till");
                        var params = "appFromDate=" + searchFromdate + "&appToDate=" + searchTodate;
                        myLeaveGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/myleavegrid.php&" + params), true, true)
                        Att_scheduler.clearAll();
                        Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
                    }
                    if (id == "myleave_df_clear") {
                        myLeaveToolBar.setValue('myleave_date_from', '', false);
                    }
                    if (id == "myleave_dt_clear") {
                        myLeaveToolBar.setValue('myleave_date_till', '', false);
                    }

                });
                LeaveLayout = MyAttendanceTabbar.tabs("LeaveSheet").attachLayout("1C");
                LeaveLayout.cells("a").hideHeader();
                myLeaveGrid = LeaveLayout.cells("a").attachGrid();
                myLeaveGrid.enableColSpan(true);
                myLeaveGrid.enableTooltips("false,false,false,false,false,false,false,false,false,true");
                myLeaveGrid.loadXML(preTally.Initialize.encryptURL("requisites/myleavegrid.php"), function () {
                    Att_scheduler.clearAll();
                    Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
                    /*myLeaveGrid.attachEvent("onMouseOver", function(id,ind) { 
                     if(ind == 9) {
                     this.cells(id,ind).cell.title = 'Click here to Cancel';
                     return false;
                     }
                     
                     });*/
                });
                AttendanceLayout.cells("a").hideHeader();
//                AttGrid = AttendanceLayout.cells("a").attachGrid();
//                AttGrid.enableCollSpan(true);
//                AttGrid.attachEvent("onXLS", function() {
//                    preTally.Settings.progressOn(false, dhxMiddleBlockLayout, 'c');
//                });
//                AttGrid.attachEvent("onXLE", function() {
//                    preTally.Settings.progressOff(false, dhxMiddleBlockLayout, 'c');
//                });
//                AttGrid.loadXML(preTally.Initialize.encryptURL("requisites/listAttendance.php"), function() {
//                       AttGrid.makeFilter("ATStatus",5);
//                       AttGrid.enableTooltips("false,false,false,false,false,false,false");
////                    $('.tb_f_days').html("Full Day: " + AttGrid.getUserData("", "full_days") + " ");
////                    $('.tb_h_days').html("Half Day: " + AttGrid.getUserData("", "half_days") + " ");
////                    $('.tb_o_days').html("Off Day : " + AttGrid.getUserData("", "off_days") + " ");
////                    $('.tb_l_days').html("Leaves : " + AttGrid.getUserData("", "leave_days") + " ");
//                });
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                Att_scheduler = AttendanceLayout.cells("a").attachScheduler();
                Att_scheduler.config.xml_date = "%d-%m-%Y %H:%i";
                Att_scheduler.config.readonly = true;
                Att_scheduler.config.resize_month_events = true;
                Att_scheduler.config.resize_month_timed = true;
                Att_scheduler.config.start_on_monday = false;
                Att_scheduler.setLoadMode("month");
                Att_scheduler.setCurrentView(new Date(), "month");
                Att_scheduler.templates.event_bar_date = function (start, end, ev) {
                    return "";
                };
                Att_scheduler.clearAll();
                dhx4.ajax.post(preTally.Initialize.encryptURL('requisites/getWeekendList.php'), function (response) {
                    Att_scheduler.deleteMarkedTimespan();
                    var responsetext = response.xmlDoc.responseText.split('**');
                    var weekendArr = responsetext[1].split(',');
                    for (var i = 0; i < weekendArr.length; i++) {
                        var day = parseInt(weekendArr[i]);
                        if (day == 7) {
                            day = 0;
                        }
                        Att_scheduler.addMarkedTimespan({
                            days: day,
                            zones: "fullday",
                            css: "scheduler_weekends",
                            type: "dhx_time_block"
                        });
                        Att_scheduler.updateView(new Date(), "month");
                        Att_scheduler.setCurrentView();
                    }
                });
                Att_scheduler.clearAll();
                Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php"), function () {

                });

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





                AttForm = dhxMiddleBlockLayout.cells("b").attachForm();
                AttForm.loadStruct(preTally.Initialize.encryptURL("requisites/attSignout.php"), function () {
                    AttForm.hideItem("MarkSignIn");
                    AttForm.hideItem("MarkSignOut");
                    $.ajax({
                        url: preTally.Initialize.encryptURL("requisites/getAttendStatus.php")
                    }).done(function (data) {
                        if (data == 1) {
                            AttForm.showItem("MarkSignIn");
                            AttForm.hideItem("MarkSignOut");
                            AttForm.disableItem("MarkSignIn");
                        } else {
                            var att_flg = unescape(JGG1P3bDnUSDL4Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                            if (att_flg == 0) {
                                AttForm.showItem("MarkSignIn");
                            } else {
                                AttForm.showItem("MarkSignOut");
                            }
                        }
                    });
                    AttForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'MarkSignOut') {
                            AttForm.hideItem("MarkSignOut");
                            AttForm.showItem("MarkSignIn");
                            AttForm.disableItem("MarkSignIn");
                            AttForm.setItemValue("attnd", 1);
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            AttForm.send(preTally.Initialize.encryptURL("warehouse/attendance.php"), function (loader, response) {
//                                AttGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttendance.php"), true, true, function () {
                                Att_scheduler.clearAll();
                                Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php"), function () {
//                                        $('.tb_f_days').html("Full Day: " + AttGrid.getUserData("", "full_days") + " ");
//                                    $('.tb_h_days').html("Half Day: " + AttGrid.getUserData("", "half_days") + " ");
//                                    $('.tb_o_days').html("Off Day : " + AttGrid.getUserData("", "off_days") + " ");
//                                    $('.tb_l_days').html("Leaves : " + AttGrid.getUserData("", "leave_days") + " ");
                                });
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                dhtmlx.message({text: response});
                            });
                        } else if (name == "MarkSignIn") {
                            AttForm.hideItem("MarkSignIn");
                            AttForm.showItem("MarkSignOut");
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            AttForm.setItemValue("attnd", 0);
                            AttForm.send(preTally.Initialize.encryptURL("warehouse/attendance.php"), function (loader, response) {
//                                AttGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAttendance.php"), true, true, function () {
////                                    $('.tb_f_days').html("Full Day: " + AttGrid.getUserData("", "full_days") + " ");
////                                    $('.tb_h_days').html("Half Day: " + AttGrid.getUserData("", "half_days") + " ");
////                                    $('.tb_o_days').html("Off Day : " + AttGrid.getUserData("", "off_days") + " ");
////                                    $('.tb_l_days').html("Leaves : " + AttGrid.getUserData("", "leave_days") + " ");
//                                });
                                Att_scheduler.clearAll();
                                Att_scheduler.load(preTally.Initialize.encryptURL("requisites/AttendanceScheduler.php", "xml"));
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                dhtmlx.message({text: response});
                                AttForm.setItemValue("attnd", 0);
                            });
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuMyAttendance").setActive();
            }
        },
        salaryAdvance: function () {
            if (!dhxMiddleBlockTabs.cells("menulistSalAdvance")) {
                dhxMiddleBlockTabs.addTab("menulistSalAdvance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Salary Advance And Repayment", 260);
                dhxMiddleBlockTabs.tabs("menulistSalAdvance").setActive();
                salAdvanceTabbar = dhxMiddleBlockTabs.cells("menulistSalAdvance").attachTabbar();
                salAdvanceTabbar.addTab("a1", "Salary Advance Payment");
                salAdvanceTabbar.addTab("a2", "Repayment Schedule");
//                salAdvanceTabbar.addTab("a3", "Salary ss");
                salAdvanceTabbar.tabs("a1").setActive();
                salAdvanceLayout = salAdvanceTabbar.tabs("a1").attachLayout('2E');
                salAdvanceTabbar.attachEvent("onTabClick", function (id, last_id) {
                    if (id == "a1") {
                        preTally.UserProfile.salAdvance();
                    }
                    if (id == "a2") {
                        preTally.UserProfile.rePayment();
                    }
//                    if(id=="a3"){
//                        
//                    }
                    return true;
                });
                preTally.UserProfile.salAdvance();
            } else {
                dhxMiddleBlockTabs.tabs("menulistSalAdvance").setActive();
            }

        },
        salAdvance: function () {
            salAdvanceLayout.cells("a").setWidth(200);
            salAdvanceLayout.cells("a").hideHeader();
            salAdvanceLayout.cells("b").fixSize(true, true);
            salAdvanceLayout.cells("b").hideHeader();
            salAdvanceLayout.cells("a").setHeight(300);
            salAdvanceForm = salAdvanceLayout.cells("a").attachForm();
            salAdvanceGrid = salAdvanceLayout.cells("b").attachGrid();
            salAdvanceLayout.cells("b").attachStatusBar({
                text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='salAdv_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='salAdvance_paging'></div>",
                height: 30
            });
            salAdvanceGrid.setHeader("SlNo,Staff Name,Advance Amount,Payment Duration,Payment Start From,Monthly Repayment Amount,Added By");
            salAdvanceGrid.attachHeader(",<input type='text'  id='Usr_name' class='salAdv_TextFilter' style='width: 90%;' placeholder='Staff Name'>\
                            ,<input type='text'  id='SA_Amount' class='salAdv_TextFilter' style='width: 90%;' placeholder='Advance Amount'>\
                            ,<input type='text'  id='SA_PaymentTime' class='salAdv_TextFilter' style='width: 90%;' placeholder='Repayment Duration'>\
                           ,,,\
             ");
            salAdvanceGrid.setInitWidths("40,250,250,200,200,100,*");
            salAdvanceGrid.setColAlign("left,left,left,left,left,left,left");
            salAdvanceGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
            salAdvanceGrid.enableTooltips("false,false,false,false,false,false");
            salAdvanceGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
            salAdvanceGrid.enablePaging(true, 50, 5, "salAdvance_paging", true);
            salAdvanceGrid.setPagingSkin("toolbar", "dhx_skyblue");
            salAdvanceGrid.enableColSpan(true);
            salAdvanceGrid.setImagePath('assets/grid/codebase/imgs/');
            salAdvanceGrid.init();
            salAdvanceGrid.loadXML(preTally.Initialize.encryptURL("requisites/salAdvanceGrid.php"), function () {
                $('.salAdv_cnt_tot').html("# : " + salAdvanceGrid.getUserData("", "TL_Count") + " ");
                salAdvanceGrid.attachEvent("onRowSelect", function (id, ind) {
                    if (id != 0) {
                        salAdvanceTabbar.tabs("a2").setActive();
                        preTally.UserProfile.rePayment(id);
                    }
                });
            });
            salAdvanceForm.loadStruct(preTally.Initialize.encryptURL("requisites/salAdvanceForm.php"), function () {
                usernameCombo = salAdvanceForm.getCombo("US_Id");
                monthCombo = salAdvanceForm.getCombo("BS_PaymentStartMonth");
                paymentDurCombo = salAdvanceForm.getCombo("SA_PaymentDuration");
                salAdvanceForm.hideItem("BS_PayType");
                salAdvanceForm.hideItem("BNK_Id");
                salAdvanceForm.hideItem("BB_Id");
                salAdvanceForm.hideItem("BA_Id");
                salAdvanceForm.hideItem("CHQ_Number");
                salAdvanceForm.hideItem("BS_Transaction");
                var BankCombo = salAdvanceForm.getCombo("BNK_Id");
                var BankBrnch = salAdvanceForm.getCombo("BB_Id");
                var BnkAccCombo = salAdvanceForm.getCombo("BA_Id");
                var BnkChqCombo = salAdvanceForm.getCombo("CHQ_Number");
                var LocCombo = salAdvanceForm.getCombo("LC_Id");
                var PaidByCombo = salAdvanceForm.getCombo("BS_PaidBy");
                var item_list = new Array("BS_PayType", "BNK_Id", "BB_Id", "BA_Id");
                var paymodeCombo = salAdvanceForm.getCombo("PM_Id");
                paymodeCombo.attachEvent("onClose", function () {
                    var paymodeValue = paymodeCombo.getSelectedValue();
                    for (i = 0; i <= 4; i++) {
                        if (paymodeValue == '1') {
                            salAdvanceForm.hideItem(item_list[i]);
                        } else {
                            salAdvanceForm.showItem(item_list[i]);
                        }
                    }
                    if (paymodeValue == 2) {
                        salAdvanceForm.setValidation('BS_PayType', 'NotEmpty');
                        salAdvanceForm.setValidation('BNK_Id', 'NotEmpty');
                        salAdvanceForm.setValidation('BB_Id', 'NotEmpty');
                        salAdvanceForm.setValidation('BA_Id', 'NotEmpty');
                        /*salAdvanceForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                         salAdvanceForm.setValidation('BS_PayersChQ', 'NotEmpty'); */

                        var paymentType = salAdvanceForm.getCombo("BS_PayType");
                        var pmType = paymentType.getSelectedValue();
                        if (pmType == '2') {
                            salAdvanceForm.setValidation('CHQ_Number', 'NotEmpty');
                        }
                        if (pmType == '3') {
                            // BSForm[BsId].setValidation('CHQ_Number', 'null'); 
                            salAdvanceForm.clearValidation('CHQ_Number');
                        }

                    } else {
                        salAdvanceForm.clearValidation('BNK_Id');
                        salAdvanceForm.clearValidation('BB_Id');
                        salAdvanceForm.clearValidation('BA_Id');
                        salAdvanceForm.clearValidation('CHQ_Number');
                        salAdvanceForm.clearValidation('BS_Transaction');
                        salAdvanceForm.hideItem('CHQ_Number');
                        salAdvanceForm.hideItem('BS_Transaction');
                    }

                });
                if (salAdvanceForm.isItem("BS_PayType")) {
                    var paymentType = salAdvanceForm.getCombo("BS_PayType");
                    paymentType.attachEvent("onClose", function () {
                        var pmType = paymentType.getSelectedValue();
                        if (pmType == '2') {
                            salAdvanceForm.hideItem("BS_Transaction");
                            salAdvanceForm.showItem("CHQ_Number");
                        }
                        if (pmType == '3') {
                            salAdvanceForm.setItemLabel("BS_Transaction", "DD Number");
                            salAdvanceForm.showItem("BS_Transaction");
                            salAdvanceForm.hideItem("CHQ_Number");
                        }
                        if (pmType == '4' || pmType == '5' || pmType == '6') {
                            salAdvanceForm.setItemLabel("BS_Transaction", "Transaction Id");
                            salAdvanceForm.showItem("BS_Transaction");
                            salAdvanceForm.hideItem("CHQ_Number");
                        }
                    });
                }
                if (salAdvanceForm.isItem("BNK_Id")) {
                    BankCombo.attachEvent("onClose", function () {
                        BankBrnch.clearAll();
                        BankBrnch.setComboValue("");
                        BankBrnch.setComboText("");
                        BnkAccCombo.clearAll();
                        BnkAccCombo.setComboValue("");
                        BnkAccCombo.setComboText("");
                        var bnk_id = BankCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BankBrnch.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                            });
                        }
                    });
                }
                BankBrnch.attachEvent("onClose", function () {
                    BnkAccCombo.clearAll();
                    BnkAccCombo.setComboText('');
                    if (salAdvanceForm.isItem("CHQ_Number")) {
                        BnkChqCombo.clearAll();
                        BnkChqCombo.setComboText('');
                        BnkChqCombo.setComboValue('');
                    }

                    var bnk_acc_id = BankBrnch.getSelectedValue();
                    if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                        var params = "BB_Id=" + bnk_acc_id;
                        BnkAccCombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                        });
                    }
                });
                BnkAccCombo.attachEvent("onClose", function () {
                    if (salAdvanceForm.isItem("CHQ_Number")) {
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
                salAdvanceForm.attachEvent("onButtonClick", function (name) {
                    if (name == "saveSalAdvanceDetails") {

                        salAdvanceFormValidate = salAdvanceForm.validate();
                        if (salAdvanceFormValidate == false) {
                            return false;
                        } else {
                            salAdvanceForm.send(preTally.Initialize.encryptURL('warehouse/salaryAdvance.php'), function (loader, response) {
                                dhtmlx.message({text: response});
                                salAdvanceForm.clear();
                                usernameCombo.setComboText("");
                                usernameCombo.setComboValue("");
                                monthCombo.setComboText("");
                                monthCombo.setComboValue("");
                                monthCombo.clearAll();
                                monthCombo.load(preTally.Initialize.encryptURL("requisites/getNextMonth.php"));
                                BankCombo.setComboText("");
                                BankCombo.setComboValue("");
                                BankBrnch.setComboText("");
                                BankBrnch.setComboValue("");
                                BnkAccCombo.setComboText("");
                                BnkAccCombo.setComboValue("");
                                BnkChqCombo.setComboText("");
                                BnkChqCombo.setComboValue("");
                                paymentType.setComboText("");
                                paymentType.setComboValue("");
                                LocCombo.setComboText("");
                                LocCombo.setComboValue("");
                                LocCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=BMR"));
                                PaidByCombo.setComboText("");
                                PaidByCombo.setComboValue("");
                                PaidByCombo.load(preTally.Initialize.encryptURL("requisites/persons.php&mask=Self"));
                                var d = new Date();
                                var mnth = parseInt(d.getMonth() + 1);
                                if (mnth < 10)
                                    mnth = '0' + mnth;
                                var dt = d.getFullYear() + '-' + mnth + '-' + d.getDate();
                                salAdvanceForm.setItemValue("BS_PaidDate", dt);
                                salAdvanceForm.hideItem("BS_PayType");
                                salAdvanceForm.hideItem("BNK_Id");
                                salAdvanceForm.hideItem("BB_Id");
                                salAdvanceForm.hideItem("BA_Id");
                                salAdvanceForm.hideItem("CHQ_Number");
                                salAdvanceForm.hideItem("BS_Transaction");
                                salAdvanceGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salAdvanceGrid.php"), function () {
                                    $('.salAdv_cnt_tot').html("# : " + salAdvanceGrid.getUserData("", "TL_Count") + " ");
                                });
                            });
                        }

                    }
                });
                salAdvanceForm.attachEvent("onValidateError", function (name, value, result) {
                    salAdvanceForm.setValidateCss(name, result, 'validate_red');
                    return false;
                });
                var filtrInterval;
                $(".salAdv_TextFilter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.applysalAdvanceFlter();
                        clearInterval(filtrInterval);
                    }, 500);
                });
            });
        },
        applysalAdvanceFlter: function () {

            var filterValue = new Array($('#Usr_name').val(), $('#SA_Amount').val(), $('#SA_PaymentTime').val());
            var filtr = "&Filters=" + filterValue;
            salAdvanceGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/salAdvanceGrid.php" + filtr), function () {
                $('.salAdv_cnt_tot').html("# : " + salAdvanceGrid.getUserData("", "TL_Count") + " ");
            });
        },
        rePayment: function (id) {
            if (id) {
                var params = "SA_Id=" + id;
            } else {
                var params = "SA_Id=" + "";
            }
            rePaymentLayout = salAdvanceTabbar.tabs("a2").attachLayout('1C');
            rePaymentLayout.cells("a").hideHeader();
            repaymentTlbr = rePaymentLayout.cells("a").attachToolbar();
            repaymentTlbr.addText('repaymentTlbr', '0', 'Staff Name');
            repaymentTlbr.addText('repaymentTlbr', '1', '<div style="font-weight:bold;width:250px;" id="EmpNamere_Amt"></div>');
            repaymentTlbr.setIconSize(32);
            EmpNamere_AmtCombo = new dhtmlXCombo("EmpNamere_Amt");
            EmpNamere_AmtCombo.readonly(true);
            $.post(preTally.Initialize.encryptURL("requisites/rePaymentDetailsCombo.php&" + params), function (data) {
                EmpNamere_AmtCombo.load(data);
                preTally.UserProfile.rePaymentFlter(EmpNamere_AmtCombo.getSelectedValue());
            });
            EmpNamere_AmtCombo.attachEvent("onChange", function () {
                preTally.UserProfile.rePaymentFlter(EmpNamere_AmtCombo.getSelectedValue());
            });
            EmpNamere_AmtCombo.readonly(false);
            var combofiltrInterval;
            EmpNamere_AmtCombo.attachEvent("onKeyPressed", function (keyCode) {
                if (combofiltrInterval)
                    clearInterval(combofiltrInterval);
                combofiltrInterval = setInterval(function () {
                    EmpNamere_AmtCombo.load(preTally.Initialize.encryptURL("requisites/rePaymentDetailsCombo.php&mask=" + EmpNamere_AmtCombo.getComboText()), function (data) {
                        EmpNamere_AmtCombo.openSelect();
                        $(".dhxcombolist_dhx_skyblue").height(176);
                        if (EmpNamere_AmtCombo.getOptionsCount() == 0) {
                            EmpNamere_AmtCombo.closeAll();
                        }
                    });
                    clearInterval(combofiltrInterval);
                }, 500);
            });
        },
        rePaymentFlter: function (id) {
            repaymentGrid = rePaymentLayout.cells("a").attachGrid();
            repaymentGrid.setHeader("SlNo,Staff Name,Branch,Repayment Amount,Month,Status,,Skip Salary Advance");
            repaymentGrid.attachHeader(",,,,,<select id='SRS_Status' style='width:90%; font-size:8pt; font-family:Tahoma; align:center'><option value=''>All</option><option value='1'>Pending</option><option value='2'>Skipped</option><option value='3'>Paid</option></select>");
            repaymentGrid.setInitWidths("40,*,*,*,150,150,0,100");
            repaymentGrid.setColAlign("left,left,left,left,left,left,left,center");
            repaymentGrid.enableTooltips("false,false,false,false,false,false,false,false");
            repaymentGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
            repaymentGrid.enableColSpan(true);
            repaymentGrid.init();
            if (id) {
                var params = "SA_Id=" + id;
            } else {
                var params = "SA_Id=" + "";
            }
            repaymentGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listRepaymentGrid.php&" + params), function () {
                $("#SRS_Status").change(function () {
                    val = $("#SRS_Status").val();
                    repaymentGrid.filterBy(6, val);
                    if (repaymentGrid.getRowsNum() == 0) {
                        repaymentGrid.addRow(0, ['', '', 'No Records Found...', '', '', '', '', ], 0);
                        repaymentGrid.setRowTextStyle(0, "font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;");
                    }
                });
            });
        },
        monthwiseSalReports: function () {
            var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var monthData, yearData;
            dhxLayout.cells("b").collapse();
            if (!dhxMiddleBlockTabs.cells("MonthwiseSalRpt")) {
                //dhxMiddleBlockTabs.addTab("MonthwiseSalRpt", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Month Wise Salary Report&nbsp; <img src='images/icon/refresh-icon.png' style = 'margin-bottom:-4px;' class='rptRefreshTab'/>", 245);
                dhxMiddleBlockTabs.addTab("MonthwiseSalRpt", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Monthwise Salary Increment History", 345);
                dhxMiddleBlockTabs.tabs("MonthwiseSalRpt").setActive();
                SalRptTab = dhxMiddleBlockTabs.cells("MonthwiseSalRpt").attachTabbar();
                SalRptTab.addTab("a1", "Monthwise Salary Increment History");
                SalRptTab.addTab("a2", "Monthly Salary Report");
                SalRptTab.tabs("a1").setActive();
                SalHistRptLayout = SalRptTab.tabs("a1").attachLayout("1C");
                monthSalRptLayout = SalRptTab.tabs("a2").attachLayout("1C");
                monthSalRptLayout.cells("a").hideHeader();
                SalHistRptLayout.cells("a").hideHeader();
                monthWiseSalToolbar = monthSalRptLayout.cells("a").attachToolbar();
                monthWiseSalToolbar.setIconsPath("images/icon/default_18/");
                monthWiseSalToolbar.setAlign('right');
                var Month_Options = [];
                var Year_Options = [];
                for (i = Date.today().getMonth() + 1; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Month_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (i = Date.today().getFullYear(); i >= 2024; i--) {
                    if (i == Date.today().getFullYear()) {
                        if (Date.today().getMonth() + 1 > 3)
                            Year_Options.push([i, 'obj', i + "-" + (i + 1), "calendar_Y.png"]);
                    } else
                        Year_Options.push([i, 'obj', i + "-" + (i + 1), "calendar_Y.png"]);
                }
                monthWiseSalToolbar.addButtonSelect("salrpt_year_filter", '1', "Select Year", Year_Options, '', '', true, true, 10, 'select');
                monthWiseSalToolbar.addButtonSelect("salrpt_month_filter", '1', "Select Month", Month_Options, '', '', true, true, 10, 'select');
                var date = new Date();
                var m_names = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                monthData = date.getMonth();
                monthWiseSalToolbar.setItemText('salrpt_month_filter', m_names[monthData]);
                monthData = monthData + 1;
                dhxMiddleBlockTabs.cells("MonthwiseSalRpt").setActive();
                preTally.UserProfile.viewMonthWiseSalrpt(monthData, yearData);
                monthWiseSalToolbar.attachEvent("onClick", function (id) {
                    var pId = monthWiseSalToolbar.getParentId(id);
                    if (pId == 'salrpt_month_filter') {
                        yearData = '';
                        if (monthWiseSalToolbar.getListOptionSelected("salrpt_month_filter"))
                            monthData = monthWiseSalToolbar.getListOptionSelected("salrpt_month_filter").replace(/^m/, '');
                        monthWiseSalToolbar.setItemText('salrpt_year_filter', 'Select Year');
                    }
                    if (pId == 'salrpt_year_filter') {
                        monthData = '';
                        if (monthWiseSalToolbar.getListOptionSelected("salrpt_year_filter"))
                            yearData = monthWiseSalToolbar.getListOptionSelected("salrpt_year_filter").replace(/^m/, '');
                        monthWiseSalToolbar.setItemText('salrpt_month_filter', 'Select Month');
                    }
                    preTally.UserProfile.viewMonthWiseSalrpt(monthData, yearData);
                });
                preTally.UserProfile.viewSalHistrpt();
            } else {
                dhxMiddleBlockTabs.tabs("MonthwiseSalRpt").setActive();
//                var actvId = BSBonusReportsTabbar.getActiveTab(); 
//                if(actvId == 'viewMonthwiseBusinessRpt') preTally.BusinessBonusReport.viewMonthwiseBusinessReport();
            }

        },
        viewMonthWiseSalrpt: function (monthData, yearData) {
            monthSalRptGrid = monthSalRptLayout.cells("a").attachGrid();
            monthSalRptGrid.setImagePath("../../codebase/imgs/");
            monthSalRptGrid.setSkin("dhx_skyblue");
            monthSalRptGrid.enableColSpan(true);
            preTally.UserProfile.applyMonthwiseSalRptFilter(monthData, yearData);
        },
        viewSalHistrpt: function () {
            var filtrInterval;
            SalHistRpt = SalHistRptLayout.cells("a").attachGrid();
            SalHistRptLayout.cells("a").attachStatusBar({
                text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='salAdv_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='salHist_paging'></div>",
                height: 30
            });
            SalHistRpt.setImagePath("../../codebase/imgs/");
            SalHistRpt.setSkin("dhx_skyblue");
            SalHistRpt.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
            SalHistRpt.setPagingWTMode(true, false, true, [15, 30, 50]);
            SalHistRpt.enablePaging(true, 50, 5, "salHist_paging", true);
            SalHistRpt.setPagingSkin("toolbar", "dhx_skyblue");
            SalHistRpt.setImagePath('assets/grid/codebase/imgs/');
            SalHistRpt.init();
            SalHistRpt.enableColSpan(true);
            //SalHistRpt.attachHeader(",,<input type='text' id='nmFilter' class='salHistFiltr' placeholder='Search' style='width:90%;'></input>,<div id='brFilter' class='salHistFiltr' style='width:90%;'></div>,<input type='text' id='lcFilter' class='salHistFiltr' placeholder='Search' style='width:90%;'>,<input type='text' id='empIdFilter' class='salHistFiltr' placeholder='Search' style='width:90%;'>,<input type='text' id='desIdFilter' class='salHistFiltr' placeholder='Search' style='width:90%;'>,<input type='text' id='wkFilter' class='salHistFiltr' placeholder='Search' style='width:90%;'>,<div id='dojFilter' class='salHistFiltr' style='width:40%;'></div>,,,,,,,");



            var params = "&orderBy=1&type=asc";
            preTally.Settings.progressOn(true, dhxLayout, null);
            SalHistRpt.load(preTally.Initialize.encryptURL("requisites/salHistoryGrid.php&=" + params), function () {
                $('.salAdv_cnt_tot').html("# : " + SalHistRpt.getUserData("", "SH_count") + " ");
                SalHistRpt.enableRowsHover(true, "SHRowhover");
                SalHistRpt.setColumnHidden(4, true);
                SalHistRpt.setColumnHidden(5, true);
                SalHistRpt.setColumnHidden(6, true);
                SalHistRpt.setColumnHidden(7, true);
                SalHistRpt.setColumnHidden(8, true);
                preTally.Settings.progressOff(true, dhxLayout, null);
                SalHistBrCombo = new dhtmlXCombo("brFilter");
                SalHistBrCombo.readonly(false);
                SalHistBrCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&ctype=check"), function () {
                    SalHistBrCombo.setFilterHandler(function (mask, option) {
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                });
                SalHistBrCombo.attachEvent("onChange", function () {
                    preTally.UserProfile.salHistGridFiltr("asc", 1);
                });
                $(".SH_Sort").click(function () {
                    var colId = $(this).attr("colNum");
                    if (SHSrtFlg == 0) {
                        preTally.UserProfile.salHistGridFiltr("dsc", colId);
                        SHSrtFlg = 1;
                    } else {
                        preTally.UserProfile.salHistGridFiltr("asc", colId);
                        SHSrtFlg = 0;
                    }
                });
                $(".salHistFiltr").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.salHistGridFiltr();
                        clearInterval(filtrInterval);
                    }, 500);
                });
                SalHistDojCombo = new dhtmlXCombo("dojFilter");
                SalHistDojCombo.readonly(false);
                SalHistDojCombo.addOption([
                    ["0", "All"], ["01", "January"], ["02", "February"], ["03", "March"], ["04", "April"], ["05", "May"], ["06", "June"], ["07", "July"], ["08", "August"], ["09", "September"], ["10", "October"], ["11", "November"], ["12", "December"]
                ]);
                SalHistDojCombo.attachEvent("onChange", function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                        preTally.UserProfile.salHistGridFiltr("asc", 1);
                        clearInterval(filtrInterval);
                    }, 500);
                });
                SalHistDojCombo.setPlaceholder("All");
                var ob_flag = 1;
                var inc_flag = 1;
                $('.btn_BR').click(function () {
                    var type = $(this).attr("id");
                    if (type == "br") {
                        if (ob_flag == 1) {
                            $(this).attr("src", 'images/icon/dbl_left_20.png');
                            // $(this).attr("title", 'Hide Location/Employee ID/Designation/Working Area/Date of Joining of Branch');

                            SalHistRpt.setColumnHidden(4, false);
                            SalHistRpt.setColumnHidden(5, false);
                            SalHistRpt.setColumnHidden(6, false);
                            SalHistRpt.setColumnHidden(7, false);
                            SalHistRpt.setColumnHidden(8, false);
                            ob_flag = 0;
                        } else {
                            $(this).attr("src", 'images/icon/dbl_right_20.png');
                            // $(this).attr("title", 'View Location/Employee ID/Designation/Working Area/Date of Joining of Branch');
                            SalHistRpt.setColumnHidden(4, true);
                            SalHistRpt.setColumnHidden(5, true);
                            SalHistRpt.setColumnHidden(6, true);
                            SalHistRpt.setColumnHidden(7, true);
                            SalHistRpt.setColumnHidden(8, true);
                            ob_flag = 1;
                        }
                    }
                });
            });
            SalHistRpt.attachEvent("onRowSelect", function (rId) {
                if (SalHistRpt.cells(rId, 0).open) {
                    if (SalHistRpt.getRowById(rId)._expanded)
                        SalHistRpt.cells(rId, 0).close()
                    else
                        SalHistRpt.cells(rId, 0).open()
                }
                return true;
            });
        },
        salHistGridFiltr: function (order, colId) {

            if (SalHistBrCombo.getComboText() == '' || SalHistBrCombo.getComboText() == null)
                var brText = 'All';
            else
                var brText = SalHistBrCombo.getComboText();
            var filterValue = new Array($("#nmFilter").val(), brText, $('#lcFilter').val(), $('#empIdFilter').val(), $('#desIdFilter').val(), $('#wkFilter').val(), SalHistDojCombo.getSelectedValue());
            if (!order) {
                var params = "&orderBy=1&type=asc";
            } else {
                var params = "&orderBy=" + colId + "&type=" + order;
            }

            SalHistRpt.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            SalHistRpt.clearAndLoad(preTally.Initialize.encryptURL("requisites/salHistoryGrid.php&filter=" + filterValue + params), function () {

                $('.salAdv_cnt_tot').html("# : " + SalHistRpt.getUserData("", "SH_count") + " ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyMonthwiseSalRptFilter: function (monthData, yearData) {
            var srtFlg = 0;
            preTally.Settings.progressOn(true, dhxLayout, null);
            var rptFilterParams = '&m=' + monthData + '&y=' + yearData;
            monthSalRptGrid.clearAll();
            monthSalRptGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/reportMonthwiseSalary.php" + rptFilterParams), function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                var nameCombo = monthSalRptGrid.getFilterElement(1);
                nameCombo.setPlaceholder("Staff Name");
                var brnchCombo = monthSalRptGrid.getFilterElement(2);
                brnchCombo.setPlaceholder("Branch");
                $('.MS_Sort_BIR').click(function () {
                    var colId = $(this).attr("colNum");
                    if (srtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        monthSalRptGrid.sortRows(colId, "int", "asc");
                        srtFlg = 1;
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        monthSalRptGrid.sortRows(colId, "int", "desc");
                        srtFlg = 0;
                    }
                });
            });
        },
        skipSalAdvance: function (id) {
            if (prev_id == id)
                return;
            if (id) {
                prev_id = id;
                var params = "SRS_Id=" + id;
            }
            $.post(preTally.Initialize.encryptURL("warehouse/skipSalAdvance.php&" + params), function (data) {
                dhtmlx.message({text: "Salary Repayment skipped Successfully"});
                preTally.UserProfile.rePayment(data);
                setTimeout(function () {
                    prev_id = 0;
                }, 2000);
            });
        },
        mealAllowance: function () {
            if (!dhxMiddleBlockTabs.cells("mealCard")) {
                dhxMiddleBlockTabs.addTab("mealCard", "<img src='images/icon/graph_icon.png' style='margin-top:2px;' />&nbsp;&nbsp;Meal Allowance", 245);
                dhxMiddleBlockTabs.tabs("mealCard").setActive();
                var Months_Options = [];
                var Year_Options = [];
                var sel_month;
                var month = Date.today().getMonth();
                var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                var mnthLimit;
                if (myattendance_year < Date.today().getFullYear()) {
                    mnthLimit = 12;
                } else {
                    //mnthLimit=Date.today().getMonth()+1
                    mnthLimit = 12;
                }
                for (i = mnthLimit; i >= 1; i--) {
                    j = i;
                    if (j < 10) {
                        j = '0' + i;
                    }
                    Months_Options.push(['m' + j, 'obj', monthNames[i - 1], "calendar_M.png"]);
                }
                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push(['y' + j, 'obj', j, "calendar_Y.png"]);
                }
                mealCardLayout = dhxMiddleBlockTabs.cells("mealCard").attachLayout("1C");
                mealCardLayout.cells("a").hideHeader();
                mealCardToolbar = mealCardLayout.cells("a").attachToolbar();
                mealCardToolbar.setIconsPath("images/icon/default_18/");
                mealCardToolbar.addButtonSelect("meal_month_filter", '1', "Select Month", Months_Options, 'calendar_M.png', '', true, true, 10, 'select');
                mealCardToolbar.addButtonSelect("meal_year_filter", '2', "Select Year", Year_Options, 'calendar_Y.png', '', true, true, 10, 'select');
                mealCardToolbar.setItemText('meal_month_filter', monthNames[Date.today().getMonth()]);
                mealCardToolbar.setItemText('meal_year_filter', Date.today().getFullYear());
                mealCard_sel_month = parseInt(Date.today().getMonth() + 1);
                mealCard_sel_year = Date.today().getFullYear();
                //var params="meal_month=" + parseInt(Date.today().getMonth()+1) + "&meal_year="+Date.today().getFullYear() ;
                var params = "meal_month=" + mealCard_sel_month + "&meal_year=" + mealCard_sel_year;
                mealCardToolbar.attachEvent("onClick", function (id) {
                    var pId = mealCardToolbar.getParentId(id);
                    if (pId == "meal_month_filter") {
                        mealCard_sel_month = id.substr(1);
                        if (mealCardToolbar.getListOptionSelected("meal_year_filter")) {
                            mealCard_sel_year = mealCardToolbar.getListOptionSelected("meal_year_filter");
                            mealCard_sel_year = mealCard_sel_year.substr(1);
                        }
                        params = "meal_month=" + mealCard_sel_month + "&meal_year=" + mealCard_sel_year;
                    }
                    if (pId == "meal_year_filter") {
                        mealCard_sel_year = id.substr(1);
                        if (mealCardToolbar.getListOptionSelected("meal_month_filter")) {
                            mealCard_sel_month = mealCardToolbar.getListOptionSelected("meal_month_filter");
                            mealCard_sel_month = mealCard_sel_month.substr(1);
                        }
                        params = "meal_month=" + mealCard_sel_month + "&meal_year=" + mealCard_sel_year;
                    }
                    mealCardGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/mealCardGrid.php&" + params));
                });
                mealCardGrid = mealCardLayout.cells("a").attachGrid();
                mealCardGrid.setImagePath("assets/grid/codebase/imgs/");
                mealCardGrid.setHeader("SlNo,Employee Id,Name,Branch,Month,Year,Meal Allowance,Status,#master_checkbox");
                mealCardGrid.setInitWidths("80,*,*,*,120,120,120,80,80");
                mealCardGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ch");
                mealCardGrid.enableTooltips("false,false,false,false,false,false,false,false");
                mealCardGrid.enableColSpan(true);
                mealCardGrid.attachHeader(",#text_filter,#text_filter,#text_filter,,,,,");
                mealCardLayout.attachStatusBar({text: "<input type='button' id='btnMealSave' style='float:right;' value='Save' onClick='preTally.UserProfile.createMealCardEntries();'></input>", height: 30});
                mealCardGrid.init();
                mealCardGrid.loadXML(preTally.Initialize.encryptURL("requisites/mealCardGrid.php&" + params), function () {
                    mealCardGrid.hdr.rows[1].cells[8].getElementsByTagName("INPUT")[0].checked = false;
                });
            } else {
                dhxMiddleBlockTabs.tabs("mealCard").setActive();
            }
        }, createMealCardEntries: function (month, year) {
            var selMealAllowancestr = mealCardGrid.getCheckedRows(8);
            var selMealAllowance = selMealAllowancestr.split(",");
            var sum = 0
            console.log(typeof (selMealAllowance));
            selMealAllowance.forEach(function (id) {
                if (mealCardGrid.cells(id, 7).getValue() != 'Paid')
                    sum += parseInt(mealCardGrid.cells(id, 6).getValue());
                else
                    mealCardGrid.cells(id, 8).setValue(0);
            });
            dhxMealWin = new dhtmlXWindows();
            MealWin = dhxMealWin.createWindow("wins_meal", 350, 600, 1250, 300);
            MealWin.button("minmax1").hide();
            MealWin.button("minmax2").hide();
            MealWin.button("park").hide();
            //MealWin.hideHeader();
            MealWin.center();
            MealWin.setModal(true);
            MealWin.setText("Meal Allowance Payment Details");
            MealCardBSForm = MealWin.attachForm();
            MealCardBSForm.loadStruct(preTally.Initialize.encryptURL("requisites/mealCardBSForm.php&month=" + mealCard_sel_month + "&year=" + mealCard_sel_year), function () {
                MealCardBSForm.setItemValue("ML_Amount", sum);
                MealCardBSForm.setItemValue("SM_Id", selMealAllowancestr);
                MealCardBSForm.hideItem("BS_PayType");
                MealCardBSForm.hideItem("BNK_Id");
                MealCardBSForm.hideItem("BB_Id");
                MealCardBSForm.hideItem("BA_Id");
                MealCardBSForm.hideItem("CHQ_Number");
                MealCardBSForm.hideItem("BS_Transaction");
                var BankCombo = MealCardBSForm.getCombo("BNK_Id");
                var BankBrnch = MealCardBSForm.getCombo("BB_Id");
                var BnkAccCombo = MealCardBSForm.getCombo("BA_Id");
                var BnkChqCombo = MealCardBSForm.getCombo("CHQ_Number");
                var LocCombo = MealCardBSForm.getCombo("LC_Id");
                var PaidByCombo = MealCardBSForm.getCombo("BS_PaidBy");
                var item_list = new Array("BS_PayType", "BNK_Id", "BB_Id", "BA_Id");
                var paymodeCombo = MealCardBSForm.getCombo("PM_Id");
                paymodeCombo.attachEvent("onClose", function () {
                    var paymodeValue = paymodeCombo.getSelectedValue();
                    for (i = 0; i <= 4; i++) {
                        if (paymodeValue == '1') {
                            MealCardBSForm.hideItem(item_list[i]);
                        } else {
                            MealCardBSForm.showItem(item_list[i]);
                        }
                    }
                    if (paymodeValue == 2) {
                        MealCardBSForm.setValidation('BS_PayType', 'NotEmpty');
                        MealCardBSForm.setValidation('BNK_Id', 'NotEmpty');
                        MealCardBSForm.setValidation('BB_Id', 'NotEmpty');
                        MealCardBSForm.setValidation('BA_Id', 'NotEmpty');
                        /*MealCardBSForm.setValidation('BS_PayersBank', 'NotEmpty'); 
                         MealCardBSForm.setValidation('BS_PayersChQ', 'NotEmpty'); */

                        var paymentType = MealCardBSForm.getCombo("BS_PayType");
                        var pmType = paymentType.getSelectedValue();
                        if (pmType == '2') {
                            MealCardBSForm.setValidation('CHQ_Number', 'NotEmpty');
                        }
                        if (pmType == '3') {
                            // BSForm[BsId].setValidation('CHQ_Number', 'null'); 
                            MealCardBSForm.clearValidation('CHQ_Number');
                        }

                    } else {
                        MealCardBSForm.clearValidation('BNK_Id');
                        MealCardBSForm.clearValidation('BB_Id');
                        MealCardBSForm.clearValidation('BA_Id');
                        MealCardBSForm.clearValidation('CHQ_Number');
                        //MealCardBSForm.clearValidation('BS_PayersChQ'); 
                    }

                });
                if (MealCardBSForm.isItem("BS_PayType")) {
                    var paymentType = MealCardBSForm.getCombo("BS_PayType");
                    paymentType.attachEvent("onClose", function () {
                        var pmType = paymentType.getSelectedValue();
                        if (pmType == '2') {
                            MealCardBSForm.hideItem("BS_Transaction");
                            MealCardBSForm.showItem("CHQ_Number");
                        }
                        if (pmType == '3') {
                            MealCardBSForm.setItemLabel("BS_Transaction", "DD Number");
                            MealCardBSForm.showItem("BS_Transaction");
                            MealCardBSForm.hideItem("CHQ_Number");
                        }
                        if (pmType == '4' || pmType == '5' || pmType == '6') {
                            MealCardBSForm.setItemLabel("BS_Transaction", "Transaction Id");
                            MealCardBSForm.showItem("BS_Transaction");
                            MealCardBSForm.hideItem("CHQ_Number");
                        }
                    });
                }
                if (MealCardBSForm.isItem("BNK_Id")) {
                    BankCombo.attachEvent("onClose", function () {
                        var bnk_id = BankCombo.getSelectedValue();
                        if ((bnk_id) && (bnk_id != 0)) {
                            var params = "Bnk_Id=" + bnk_id;
                            BankBrnch.load(preTally.Initialize.encryptURL("requisites/getBankBranches.php&" + params), function () {
                            });
                        }
                    });
                }
                BankBrnch.attachEvent("onClose", function () {
                    BnkAccCombo.clearAll();
                    BnkAccCombo.setComboText('');
                    if (MealCardBSForm.isItem("CHQ_Number")) {
                        BnkChqCombo.clearAll();
                        BnkChqCombo.setComboText('');
                        BnkChqCombo.setComboValue('');
                    }

                    var bnk_acc_id = BankBrnch.getSelectedValue();
                    if ((bnk_acc_id) && (bnk_acc_id != 0)) {
                        var params = "BB_Id=" + bnk_acc_id;
                        BnkAccCombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                        });
                    }
                });
                BnkAccCombo.attachEvent("onClose", function () {
                    if (MealCardBSForm.isItem("CHQ_Number")) {
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
                MealCardBSForm.attachEvent("onButtonClick", function (name) {
                    if (name == "saveMealAllowanceBSEntry") {

                        MealCardBSFormValidate = MealCardBSForm.validate();
                        if (MealCardBSFormValidate == false) {
                            return false;
                        } else {
                            MealCardBSForm.send(preTally.Initialize.encryptURL('warehouse/mealAllowanceBSEntry.php'), function (loader, response) {
                                dhtmlx.message({text: response});
                                MealCardBSForm.clear();
                                //usernameCombo.setComboText("");
                                //usernameCombo.setComboValue("");
                                //monthCombo.setComboText("");
                                //monthCombo.setComboValue("");
                                BankCombo.setComboText("");
                                BankCombo.setComboValue("");
                                BankBrnch.setComboText("");
                                BankBrnch.setComboValue("");
                                BnkAccCombo.setComboText("");
                                BnkAccCombo.setComboValue("");
                                BnkChqCombo.setComboText("");
                                BnkChqCombo.setComboValue("");
                                paymentType.setComboText("");
                                paymentType.setComboValue("");
                                LocCombo.setComboText("");
                                LocCombo.setComboValue("");
                                PaidByCombo.setComboText("");
                                PaidByCombo.setComboValue("");
                                MealCardBSForm.hideItem("BS_PayType");
                                MealCardBSForm.hideItem("BNK_Id");
                                MealCardBSForm.hideItem("BB_Id");
                                MealCardBSForm.hideItem("BA_Id");
                                MealCardBSForm.hideItem("CHQ_Number");
                                MealCardBSForm.hideItem("BS_Transaction");
                                MealWin.close();
                                var params = "meal_month=" + mealCard_sel_month + "&meal_year=" + mealCard_sel_year;
                                mealCardGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/mealCardGrid.php&" + params), function () {
                                    mealCardGrid.hdr.rows[1].cells[8].getElementsByTagName("INPUT")[0].checked = false;
                                });
                            });
                        }

                    }
                });
                MealCardBSForm.attachEvent("onValidateError", function (name, value, result) {
                    MealCardBSForm.setValidateCss(name, result, 'validate_red');
                    return false;
                });
            });
        }

    };
})(jQuery, this);