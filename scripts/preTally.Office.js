;(function($, window, undefined) {
    preTally.Office = {
        menuListOfficeAdmin: function() {
            
            if (!dhxMiddleBlockTabs.cells("menuListOfficeAdmin")) {
                dhxMiddleBlockTabs.addTab("menuListOfficeAdmin", "<img src='images/icon/company_12.png' style='margin-top:2px;' />&nbsp;&nbsp;List Company Admins", 250);
                dhxMiddleBlockTabs.tabs("menuListOfficeAdmin").setActive();
                
                var listOfficeAdminLayout = dhxMiddleBlockTabs.cells("menuListOfficeAdmin").attachLayout('2U');
                listOfficeAdminLayout.cells("a").setWidth(200);
                listOfficeAdminLayout.cells("a").setText("");
                listOfficeAdminLayout.cells("b").setText("Map Offices");
                
                listOfficeAdminGrid = listOfficeAdminLayout.cells("b").attachGrid();
                
                listOfficeAdminGrid.attachEvent("onXLS", function() {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listOfficeAdminGrid.attachEvent("onXLE", function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listOfficeAdminGrid.init();
                listOfficeAdminGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOfficeAdmin.php"), function() {  });
                
            }else{
                dhxMiddleBlockTabs.tabs("menuListOfficeAdmin").setActive();
            }
        },
        
        listOfzAdmin : function(inp,USId){
            
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            
            dhxMapOfzWin = new dhtmlXWindows();
            mapOfzWin = dhxMapOfzWin.createWindow("mapOfzWin", x, y, 750, 350);
            mapOfzWin.button("minmax1").hide();
            mapOfzWin.button("minmax2").hide();
            mapOfzWin.button("park").hide();
            mapOfzWin.center();
            mapOfzWin.setModal(true);
            mapOfzWin.setText("Map Admin Accounts");
            listOfzAdminGrid = mapOfzWin.attachGrid();
            listOfzAdminGrid.enableAutoWidth(true);
            listOfzAdminGrid.enableColSpan(true);
            preTally.Settings.progressOn(true, mapOfzWin, null);         

            var ofzAdmins= dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listOfzAdmin.php&USId="+USId), "checked=true"); 
            listOfzAdminGrid.parse(ofzAdmins.xmlDoc.responseText,function(){
                preTally.Settings.progressOff(true, mapOfzWin, null);
            });

            mapOfzWin.attachStatusBar({
                text  : "<input type='button' value='SAVE' onclick='preTally.Office.mapOfzAdmin("+USId+");' style='margin:5px 10px 5px 0; float:right;' />",
                height: 35
            });
            
        },
        mapOfzAdmin: function(USId) {   
            
            var selectedUsers = '';
            if(listOfzAdminGrid.getCheckedRows(3))
                selectedUsers = listOfzAdminGrid.getCheckedRows(3)+","+USId;
            var UserMapList = selectedUsers.split(",") ;
            
            $.ajax({
                    type   : "POST",
                    url    : preTally.Initialize.encryptURL('warehouse/mapOfzAdmin.php&USId='+USId+'&c=' + UserMapList ),
                    
            }).done(function(data) { 
                if (data != '') {
                    dhtmlx.message({text: data });
                    dhxMapOfzWin.window("mapOfzWin").close();
                    listOfficeAdminGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listOfficeAdmin.php"), true, true, function() {});
                }
            });
        },
        menuMyOffices: function() {
            
            if (!dhxMiddleBlockTabs.cells("menuMyOffices")) {
                dhxMiddleBlockTabs.addTab("menuMyOffices", "<img src='images/icon/company_12.png' style='margin-top:2px;' />&nbsp;&nbsp;List Company Admins", 250);
                dhxMiddleBlockTabs.tabs("menuMyOffices").setActive();
                
                var listOfficeAdminLayout = dhxMiddleBlockTabs.cells("menuMyOffices").attachLayout('2U');
                listOfficeAdminLayout.cells("a").setWidth(200);
                listOfficeAdminLayout.cells("a").setText("");
                listOfficeAdminLayout.cells("b").setText("List My Offices");
                
                var listOfficeAdminGrid = listOfficeAdminLayout.cells("b").attachGrid();
                
                listOfficeAdminGrid.attachEvent("onXLS", function() {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listOfficeAdminGrid.attachEvent("onXLE", function() {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listOfficeAdminGrid.init();
                listOfficeAdminGrid.loadXML(preTally.Initialize.encryptURL("requisites/listMyOffices.php"), function() { 
                    listOfficeAdminGrid.attachEvent("onRowSelect", function(id) {
                        dhtmlx.confirm({
                            title : "Switch Accounts",
                            ok : "Yes", cancel : "No",
                            text  : " Do you want to login as "+ listOfficeAdminGrid.getUserData(id,'OF_Name') +" ?",
                            callback : function(result){
                                if(result == true){
                                    
                                    var signInObject = {};
                                    signInObject['username']   = listOfficeAdminGrid.getUserData(id,'US_EMPID');
                                    signInObject['password']   = listOfficeAdminGrid.getUserData(id,'US_Password');
                            
                                    $.ajax({
                                        type : "POST",
                                        url  : preTally.Initialize.encryptURL('warehouse/switchOffices.php'),
                                        data : signInObject
                                    }).done(function(data){
                                        location.reload();
                                    });
                                }
                            }
                        });
                    });
                });
                
            }else{
                dhxMiddleBlockTabs.tabs("menuMyOffices").setActive();
            }
        },
        menuSwitchUserAccount: function(empID) {
            
            var userName = empID.split(/_(.+)?/)[1]
           
            $(".preloader").show(); 
           
            $.ajax({
                type: "POST",
                url: preTally.Initialize.encryptURL("warehouse/switchOffices.php&userName="+userName),
            }).done(function(){
                location.reload();
//                location.href ='index.php';
            });
        },
    };
})(jQuery, this);