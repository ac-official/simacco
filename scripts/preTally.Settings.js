;
(function ($, window, undefined) {
    preTally.Settings = {
        dragACL: function (from, to) {

            dhtmlx.confirm({
                //type:"confirm-warning",
                title: "ACL Permissions",
                ok: "Yes", cancel: "No",
                text: "Confirm Moving Node <b>" + dhxACLTree.getItemText(from) + "</b> to Item <b>" + dhxACLTree.getItemText(to) + "</b>?",
                callBack: function (result) {
                    return true;
                }
            });
        },
        menuNewDesignation: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewDesignation")) {
                dhxMiddleBlockTabs.addTab("menuNewDesignation", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Designations", 130);
                dhxMiddleBlockTabs.tabs("menuNewDesignation").setActive();
                var menuDesignationLayout = dhxMiddleBlockTabs.cells("menuNewDesignation").attachLayout('2U');
                menuDesignationLayout.cells("a").setText("New Designation");
                menuDesignationLayout.cells("b").setText("List Designation");
                menuDesignationLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Designation --------------------//
                listDesignationGrid = menuDesignationLayout.cells("b").attachGrid();
                listDesignationGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listDesignationGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                offId = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);


                if (offId == 1) {
                    listDesignationGrid.enableTooltips("false,false,false,true");
                    listDesignationGrid.attachHeader(',#text_filter,#select_filter,,<select id="DGStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>,');
                } else {
                    listDesignationGrid.enableTooltips("false,false,true");
                    listDesignationGrid.attachHeader(',#text_filter,,<select id="DGStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                }
                listDesignationGrid.enableTooltips("false,false,false");
                listDesignationGrid.init();
                listDesignationGrid.loadXML(preTally.Initialize.encryptURL("requisites/listDesignation.php"), function () {
                    if (offId == 1) {
                        listDesignationGrid.makeFilter("DGStatus", 3);
                        listDesignationGrid.getFilterElement(3).value = "Approved";
                        listDesignationGrid.filterBy(3, "Approved");
                    } else {
                        listDesignationGrid.makeFilter("DGStatus", 2);
                        listDesignationGrid.getFilterElement(2).value = "Approved";
                        listDesignationGrid.filterBy(2, "Approved");
                        /*Set Order*/
                        rowVal = listDesignationGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listDesignationGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            listDesignationGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }
                    listDesignationGrid.attachEvent("onRowSelect", preTally.Settings.editDesignation);
                });
                //----------------- Attach Form for Designation --------------------//
                addDesignationForm = menuDesignationLayout.cells("a").attachForm();
                addDesignationForm.loadStruct(preTally.Initialize.encryptURL("requisites/newDesignation.php&r=" + new Date().getTime()), function () {
                    addDesignationForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newDesignationValidate') {
                            var newDesignation = addDesignationForm.validate();
                            if (newDesignation) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addDesignationForm.send(preTally.Initialize.encryptURL('warehouse/newDesignation.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addDesignationForm.resetValidateCss();
                                        addDesignationForm.clear();
                                        addDesignationForm.setItemValue("DG_Id", 0);
                                    } else {
                                        response = 'Designation Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listDesignationGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listDesignation.php"), true, true, function () {
                                        //preTally.Settings.editDesignation();
                                    });
                                });
                            }
                        } else {
                            addDesignationForm.resetValidateCss();
                            addDesignationForm.clear();
                            addDesignationForm.setItemValue("DG_Id", 0);

                        }
                    });

                });
                listDesignationGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listDesignationGrid.getRowsNum() == 0) {
                        listDesignationGrid.addRow("row1", ['', 'Record Not Found', ''], 0);
                        listDesignationGrid.setRowTextStyle("row1", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        //preTally.MasterReports.calculateFooterValues();  
                        /*Set Order*/
                        rowVal = listDesignationGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listDesignationGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            listDesignationGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }
                });
            } else {

                dhxMiddleBlockTabs.tabs("menuNewDesignation").setActive();
            }
        },
        editDesignation: function (rowId) {
            var DGId = rowId;
            //listDesignationGrid.attachEvent("onRowSelect", function(DGId) {
            addDesignationForm.setItemValue("DG_Id", DGId);
            addDesignationForm.setItemValue("DG_Name", listDesignationGrid.getUserData(DGId, "DG_Name"));
            addDesignationForm.setItemValue("DG_Comments", listDesignationGrid.getUserData(DGId, "DG_Comments"));
            addDesignationForm.setItemValue("DG_Status", listDesignationGrid.getUserData(DGId, "DG_Status"));
            addDesignationForm.setItemValue("OF_Id", listDesignationGrid.getUserData(DGId, "OF_Id"));

            //});
        },
        menuNewLocation: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewLocation")) {
                dhxMiddleBlockTabs.addTab("menuNewLocation", "<img src='images/icon/branch_15.png' style='margin-top:2px;' />&nbsp;&nbsp;Branch", 170);
                //dhxMiddleBlockTabs.setTabActive("menuNewLocation");
                dhxMiddleBlockTabs.tabs("menuNewLocation").setActive();
                var menuLocationLayout = dhxMiddleBlockTabs.cells("menuNewLocation").attachLayout('2U');
                menuLocationLayout.cells("a").setText("New Branch");
                menuLocationLayout.cells("b").setText("List Branches");
                menuLocationLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Location --------------------//

                listLocationGrid = menuLocationLayout.cells("b").attachGrid();
                BranchStbar = menuLocationLayout.cells("b").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='branch_cnt_tot'># : 0</div>\
                            </div>",
                    height: 30
                });
                offId = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);

                if (offId == 1) {
                    listLocationGrid.enableTooltips("false,false,false,true");
                    listLocationGrid.attachHeader(',#text_filter_inc,#select_filter,,<select id="LLStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                } else {
                    listLocationGrid.enableTooltips("false,false,true");
                    listLocationGrid.attachHeader(',#text_filter_inc,,<select id="LLStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                }
                listLocationGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listLocationGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listLocationGrid.enableTooltips("false,false,false");
                listLocationGrid.enableColSpan(true);
                listLocationGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listLocationGrid.getRowsNum() == 0) {
                        if (listLocationGrid.doesRowExist("msgRow"))
                            listLocationGrid.deleteRow("msgRow");
                        listLocationGrid.addRow('msgRow', "No records found");
                        listLocationGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listLocationGrid.setColspan("msgRow", 0, 2);
                        $('.branch_cnt_tot').html("Total : " + 0);
                    } else {
                        $('.branch_cnt_tot').html("Total : " + listLocationGrid.getRowsNum());

                        /*Set Order*/
                        rowVal = listLocationGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listLocationGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            listLocationGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }
                });


                listLocationGrid.init();
                listLocationGrid.loadXML(preTally.Initialize.encryptURL("requisites/listLocation.php"), function () {
                    if (offId == 1) {
                        listLocationGrid.makeFilter("LLStatus", 3);
                        listLocationGrid.getFilterElement(3).value = "Published";
                        listLocationGrid.filterBy(3, "Published");
                    } else {
                        listLocationGrid.makeFilter("LLStatus", 2);
                        listLocationGrid.getFilterElement(2).value = "Published";
                        listLocationGrid.filterBy(2, "Published");
                        /*Set Order*/
                        rowVal = listLocationGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listLocationGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            newVal = listLocationGrid.cells(eachRowId, 0).getValue();
                            listLocationGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }
                    listLocationGrid.attachEvent("onRowSelect", preTally.Settings.editLocation);
                    $('.branch_cnt_tot').html("Total : " + listLocationGrid.getRowsNum());
                });

                //----------------- Attach Form for Location --------------------//
                addLocationForm = menuLocationLayout.cells("a").attachForm();
                addLocationForm.loadStruct(preTally.Initialize.encryptURL("requisites/newLocation.php&r=" + new Date().getTime()), function () {

//--------------------------------------------------Google Code---------------------------------------//
                    /*Updation on filter place*/
                    $('[name="GOGL_PlaceSearch"]')
                            .geocomplete()
                            .bind("geocode:result", function (event, result) {
                                //console.log(Object.keys(result['address_components']).length);

                                var postalCode = Object.keys(result['address_components']).length;
                                //console.log(postalCode);
                                //console.log('--'+result.address_components[(postalCode-1)].types[0]+'--');
                                //postal_code

                                var GOGLData = {
                                    "country": "GOGL_Country",
                                    "administrative_area_level_1": "GOGL_State",
                                    "administrative_area_level_2": "GOGL_City",
                                    "locality": "GOGL_Location",
                                    "sublocality_level_1": "GOGL_Place",
                                    "route": "GOGL_Street",
                                    "postal_code": "GOGL_Pincode"
                                };
                                var count = 0;
                                var GOGLDataCount = 0;
                                var GOGLResult = result['address_components'].reverse();

                                addLocationForm.setItemValue("LC_Pincode", "");
                                Object.keys(GOGLData).forEach(function (key) {
                                    // do something with obj[key]
                                    //console.log(GOGLData[key]);
                                    addLocationForm.setItemValue(GOGLData[key], "");
                                });
                                //GOGLData.forEach(function(entry) {
                                //console.log(entry);
                                //udForm.setItemValue(entry,"");
                                //});

                                var GOGLStreet = '';
                                var GOGLNeighbourhood = '';
                                for (var i in GOGLResult) {
                                    if (result['address_components'].hasOwnProperty(i)) {
                                        //console.log(result.address_components[count].types[0]);

                                        if ((result.address_components[count].types[0] == 'sublocality_level_2' ||
                                                result.address_components[count].types[0] == 'neighborhood' ||
                                                !result.address_components[count].types[0]) &&
                                                (addLocationForm.getItemValue('GOGL_City') == addLocationForm.getItemValue('GOGL_PlaceSearch'))) {
                                            addLocationForm.setItemValue('GOGL_Location', addLocationForm.getItemValue('GOGL_Place'));
                                            addLocationForm.setItemValue('GOGL_Place', result.address_components[count].long_name);
                                            //udForm.setItemValue('GOGL_Place',result.address_components[(count-1)].long_name);
                                        }
                                        if ((result.address_components[count].types[0] == 'natural_feature') || (result.address_components[count].types[0] == 'neighborhood')) {
                                            GOGLNeighbourhood = result.address_components[count].long_name;
                                        }
                                        if (result.address_components[count].types[0] == 'route') {
                                            GOGLStreet = result.address_components[count].long_name;
                                        }
                                        addLocationForm.setItemValue(GOGLData[result.address_components[count].types[0]], result.address_components[count].long_name);
                                        count++;
                                    }
                                }
                                if (GOGLStreet == '') {
                                    addLocationForm.setItemValue('GOGL_Street', GOGLNeighbourhood);
                                }
                                //console.log('Out Heree');

                                /*for (var i in GOGLResult) {
                                 if (result['address_components'].hasOwnProperty(i)) {
                                 if(result.address_components[count].types[0] === "postal_code") {
                                 //console.log(result.address_components[count].long_name +' -- Postal Code'); 
                                 udForm.setItemValue("CF_Pincode", result.address_components[count].long_name);
                                 
                                 //GOGL_Country
                                 } else {
                                 //console.log(result.address_components[count].long_name +' -- Location');
                                 udForm.setItemValue(GOGLData[GOGLDataCount], result.address_components[count].long_name);
                                 GOGLDataCount++;
                                 }
                                 count++;
                                 }
                                 }*/


                                //console.log(result.address_components[0].long_name);
                                //console.log(result.address_components[1].long_name);
                                //console.log(result.address_components[2].long_name);
                                //console.log(result.address_components[3].long_name);
                                //console.log(result.address_components[4].long_name);
                                //console.log(result.address_components[5].long_name);                        
                            });
//---------------------------------------Google Code Ends-------------------------------------//

                    /*  LocConCombo = addLocationForm.getCombo("CN_Id");
                     LocSteCombo = addLocationForm.getCombo("ST_Id");
                     LocCtyCombo = addLocationForm.getCombo("CT_Id");
                     LocPlaceCombo = addLocationForm.getCombo("AP_Id");                    
                     LocPlaceCombo.disableAutocomplete();
                     preTally.Settings.comboFilterPreload(LocPlaceCombo, "locationcombo");
                     //preTally.Settings.comboFilterPreload(LocCtyCombo, "citycombo");
                     LocPlaceCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/places.php"), true);
                     var cnt_id = 0;
                     var st_id = 0;
                     var ct_id = 0;
                     edt_flag=0;
                     LocConCombo.attachEvent("onClose",function(){
                     var cid=LocConCombo.getSelectedValue();
                     LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&cid=" + cid), function() {                            
                     });
                     });
                     LocSteCombo.attachEvent("onClose",function(){
                     var stid=LocSteCombo.getSelectedValue();
                     LocCtyCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&sid=" + stid), function() {                                    
                     });
                     });
                     LocCtyCombo.attachEvent("onClose",function(){
                     LocPlaceCombo.clearAll();
                     var ctid=LocCtyCombo.getSelectedValue();
                     LocPlaceCombo.enableFilteringMode(true,preTally.Initialize.encryptURL("requisites/places.php&ctid="+ctid), true);
                     }); */
                    /* LocPlaceCombo.attachEvent("onClose", function() {
                     
                     var place_id = LocPlaceCombo.getSelectedValue();
                     if (!isNaN(place_id) && place_id !== null && place_id != 0)
                     {
                     LocSteCombo.clearAll();
                     LocCtyCombo.clearAll();
                     
                     $.ajax({
                     url: preTally.Initialize.encryptURL("warehouse/getPlaceIds.php&loc=" + place_id)
                     }).done(function(data) {
                     var str = new Object();
                     str = jQuery.parseJSON(data);
                     cnt_id = str[0].CN_Id;
                     st_id = str[0].ST_Id;
                     ct_id = str[0].CT_Id;
                     addLocationForm.setItemValue("CN_Id", cnt_id);
                     LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&cid=" + cnt_id), function() {
                     addLocationForm.setItemValue("ST_Id", st_id);
                     });
                     LocCtyCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&sid=" + st_id), function() {
                     addLocationForm.setItemValue("CT_Id", ct_id);
                     });
                     
                     });
                     }
                     
                     }); */

                    /*   LocCtyCombo.attachEvent("onChange", function() {                    
                     var city_id = LocCtyCombo.getSelectedValue();
                     if ((!isNaN(city_id)) && (city_id !== null) && (city_id != 0) && edt_flag==0)
                     { //alert(city_id)
                     $.ajax({
                     url: preTally.Initialize.encryptURL("warehouse/getPlaceIds.php&ctid=" + city_id)
                     }).done(function(data) {
                     var str = new Object();
                     str = jQuery.parseJSON(data);
                     cnt_id = str[0].CN_Id;
                     st_id = str[0].ST_Id;
                     ct_id = str[0].CT_Id;
                     ct_name = str[0].CT_Name;
                     addLocationForm.setItemValue("CN_Id", cnt_id);
                     LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&cid=" + cnt_id), function() {
                     addLocationForm.setItemValue("ST_Id", st_id);
                     });
                     var params = "sid=" + st_id;
                     LocCtyCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/cities.php&sid=" + st_id), function() {
                     LocCtyCombo.setComboText(str[0].ct_name)
                     addLocationForm.setItemValue("CT_Id", ct_id);
                     });
                     
                     });
                     }
                     });*/

                    addLocationForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newLocationValidate') {
                            /* if(LocPlaceCombo.getSelectedValue()==null){
                             addLocationForm.setItemValue("AP_Name","new");
                             }*/
                            var newLocation = addLocationForm.validate();

                            if (newLocation) {

                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addLocationForm.send(preTally.Initialize.encryptURL('warehouse/newLocation.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail' && response != 'city_fail') {
                                        addLocationForm.resetValidateCss();
                                        addLocationForm.clear();
                                        addLocationForm.setItemValue("LC_Id", 0);

                                        addLocationForm.setItemValue("CN_Id", 0);
                                        addLocationForm.setItemValue("ST_Id", 0);
                                        addLocationForm.setItemValue("AP_Id", 0);
                                        /*LocPlaceCombo.clearAll();
                                         LocCtyCombo.clearAll();
                                         LocPlaceCombo.setComboText("");
                                         LocCtyCombo.setComboText("");*/
                                        listLocationGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listLocation.php"), true, true, function () {
                                            //preTally.Settings.editLocation();
                                            $('.branch_cnt_tot').html("Total : " + listLocationGrid.getRowsNum());

                                        });
                                    } else if (response == 'city_fail') {
                                        response = 'Invalid City,Select From the list';
                                    } else {
                                        response = 'Branch Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                });
                            }
                        } else {
                            addLocationForm.resetValidateCss();
                            addLocationForm.clear();
                            //LocPlaceCombo.setComboText("");
                            addLocationForm.setItemValue("LC_Id", 0);

                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewLocation").setActive();
            }
        },
        editLocation: function (rowId) {

            var LCId = rowId;
            edt_flag = 1;
            //listLocationGrid.attachEvent("onRowSelect", function(LCId) {
            // LocSTId = listLocationGrid.getUserData(LCId, "ST_Id");
            //LocCTId = listLocationGrid.getUserData(LCId, "CT_Id");

            /*LocSteCombo.clearAll();
             LocCtyCombo.clearAll();
             LocPlaceCombo.clearAll();
             var locCN_Id = listLocationGrid.getUserData(LCId, "CN_Id");
             LocSTId = listLocationGrid.getUserData(LCId, "ST_Id");
             LocCTId = listLocationGrid.getUserData(LCId, "CT_Id");*/

            addLocationForm.setItemValue("LC_Id", LCId);
            addLocationForm.setItemValue("LC_Name", listLocationGrid.getUserData(LCId, "LC_Name"));
            addLocationForm.setItemValue("OF_Id", listLocationGrid.getUserData(LCId, "OF_Id"));
            addLocationForm.setItemValue("LC_Building", listLocationGrid.getUserData(LCId, "LC_Building"));
            addLocationForm.setItemValue("GOGL_Street", listLocationGrid.getUserData(LCId, "SR_Name"));
            addLocationForm.setItemValue("GOGL_Place", listLocationGrid.getUserData(LCId, "PL_Name"));
            addLocationForm.setItemValue("GOGL_Location", listLocationGrid.getUserData(LCId, "ALC_Name"));
            addLocationForm.setItemValue("GOGL_City", listLocationGrid.getUserData(LCId, "CT_Name"));
            addLocationForm.setItemValue("GOGL_State", listLocationGrid.getUserData(LCId, "ST_Name"));
            addLocationForm.setItemValue("GOGL_Country", listLocationGrid.getUserData(LCId, "CN_Name"));
            addLocationForm.setItemValue("GOGL_Pincode", listLocationGrid.getUserData(LCId, "LC_Pincode"));
            addLocationForm.setItemValue("LC_PHone", listLocationGrid.getUserData(LCId, "LC_Phone"));
            addLocationForm.setItemValue("LC_Comments", listLocationGrid.getUserData(LCId, "LC_Comments"));
            /*addLocationForm.setItemValue("CN_Id", listLocationGrid.getUserData(LCId, "CN_Id"));
             var params = "cid=" + listLocationGrid.getUserData(LCId, "CN_Id");
             
             LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&" + params), function() {
             addLocationForm.setItemValue("ST_Id", listLocationGrid.getUserData(LCId, "ST_Id"));
             });
             var params = "sid=" + listLocationGrid.getUserData(LCId, "ST_Id");           
             dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/cities.php&" + params), function(xml) {
             LocCtyCombo.load(xml.xmlDoc.responseText,function(){
             addLocationForm.setItemValue("CT_Id", listLocationGrid.getUserData(LCId, "CT_Id"));
             });
             
             });
             
             if (listLocationGrid.getUserData(LCId, "AP_Name") != 0)
             {
             dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/places.php&ctid="+listLocationGrid.getUserData(LCId, "CT_Id")+"&mask=" + listLocationGrid.getUserData(LCId, "AP_Name")), function(xml) {
             LocPlaceCombo.load(xml.xmlDoc.responseText);
             addLocationForm.setItemValue("AP_Id", listLocationGrid.getUserData(LCId, "AP_Id"));
             
             });
             }
             else
             {
             addLocationForm.setItemValue("AP_Id", '');
             LocPlaceCombo.setComboText("");
             }*/
            addLocationForm.setItemValue("LC_Phone", listLocationGrid.getUserData(LCId, "LC_Phone"));


            addLocationForm.setItemValue("GOGL_Pincode", listLocationGrid.getUserData(LCId, "LC_Pincode"));
            addLocationForm.setItemValue("LC_Comments", listLocationGrid.getUserData(LCId, "LC_Comments"));
            addLocationForm.setItemValue("LC_Status", listLocationGrid.getUserData(LCId, "LC_Status"));
            // });
        },
        loadOffCity: function (office_id, loc_id, form_name) {
            locateCombo.clearAll();
            locateCombo.load(preTally.Initialize.encryptURL("requisites/officecity.php&ofid=" + office_id), function () {
                if (loc_id == 0) {
                    form_name.setItemValue("HLOC_Id", form_name.getItemValue('HLC_Id'));
                    edof = 1;
                }
            });
        },
        menuNewOffice: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewOffice")) {
                dhxMiddleBlockTabs.addTab("menuNewOffice", "<img src='images/icon/company_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Companies", 150);
                //dhxMiddleBlockTabs.setTabActive("menuNewOffice");
                dhxMiddleBlockTabs.tabs("menuNewOffice").setActive();


                var menuOfficeLayout = dhxMiddleBlockTabs.cells("menuNewOffice").attachLayout('2U');
                menuOfficeLayout.cells("a").setText("Company");
                menuOfficeLayout.cells("b").setText("List of Companies");
                menuOfficeLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Office --------------------//
                listOfficeGrid = menuOfficeLayout.cells("b").attachGrid();
                listOfficeGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listOfficeGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listOfficeGrid.init();
                listOfficeGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOffice.php"), function () {
                    listOfficeGrid.attachEvent("onRowSelect", preTally.Settings.editOffice);
                    //preTally.Settings.editOffice();
                });

                //----------------- Attach Form for Office --------------------//
                addOfficeForm = menuOfficeLayout.cells("a").attachForm();
                addOfficeForm.loadStruct(preTally.Initialize.encryptURL("requisites/newOffice.php&r=" + new Date().getTime()), function () {
                    addOfficeForm.hideItem("US_Emailtemp");
                    addOfficeForm.showItem("US_Email");

                    var temp;
                    var cnt_id = 0;
                    var st_id = 0;
                    var ct_id = 0;
//--------------------------------------------------Google Code---------------------------------------//
                    /*Updation on filter place*/
                    $('[name="GOGL_OFPlaceSearch"]')
                            .geocomplete()
                            .bind("geocode:result", function (event, result) {
                                var postalCode = Object.keys(result['address_components']).length;
                                var GOGLData = {
                                    "country": "GOGL_Country",
                                    "administrative_area_level_1": "GOGL_State",
                                    "administrative_area_level_2": "GOGL_City",
                                    "locality": "GOGL_Location",
                                    "sublocality_level_1": "GOGL_Place",
                                    "route": "GOGL_Street",
                                    "postal_code": "GOGL_Pincode"
                                };
                                var count = 0;
                                var GOGLDataCount = 0;
                                var GOGLResult = result['address_components'].reverse();

                                addOfficeForm.setItemValue("LC_Pincode", "");
                                Object.keys(GOGLData).forEach(function (key) {
                                    addOfficeForm.setItemValue(GOGLData[key], "");
                                });

                                var GOGLStreet = '';
                                var GOGLNeighbourhood = '';
                                for (var i in GOGLResult) {
                                    if (result['address_components'].hasOwnProperty(i)) {
                                        if ((result.address_components[count].types[0] == 'sublocality_level_2' ||
                                                result.address_components[count].types[0] == 'neighborhood' ||
                                                !result.address_components[count].types[0]) &&
                                                (addOfficeForm.getItemValue('GOGL_City') == addOfficeForm.getItemValue('GOGL_OFPlaceSearch'))) {
                                            addOfficeForm.setItemValue('GOGL_Location', addOfficeForm.getItemValue('GOGL_Place'));
                                            addOfficeForm.setItemValue('GOGL_Place', result.address_components[count].long_name);
                                        }
                                        if ((result.address_components[count].types[0] == 'natural_feature') || (result.address_components[count].types[0] == 'neighborhood')) {
                                            GOGLNeighbourhood = result.address_components[count].long_name;
                                        }
                                        if (result.address_components[count].types[0] == 'route') {
                                            GOGLStreet = result.address_components[count].long_name;
                                        }
                                        addOfficeForm.setItemValue(GOGLData[result.address_components[count].types[0]], result.address_components[count].long_name);
                                        count++;
                                    }
                                }
                                if (GOGLStreet == '') {
                                    addOfficeForm.setItemValue('GOGL_Street', GOGLNeighbourhood);
                                }

                            });
//---------------------------------------Google Code Ends-------------------------------------//
                    /*  var LocConCombo = addOfficeForm.getCombo("CN_Id");
                     LocSteCombo = addOfficeForm.getCombo("ST_Id");
                     LocCtyCombo = addOfficeForm.getCombo("CT_Id");
                     LocPlaceCombo = addOfficeForm.getCombo("AP_Id");
                     preTally.Settings.comboFilterPreload(LocPlaceCombo, "locationcombo");
                     preTally.Settings.comboFilterPreload(LocCtyCombo, "citycombo");
                     LocPlaceCombo.attachEvent("onChange", function() {
                     var place_id = LocPlaceCombo.getSelectedValue();
                     if (!isNaN(place_id) && place_id !== null && place_id != 0)
                     {
                     LocSteCombo.clearAll();
                     LocCtyCombo.clearAll();
                     
                     $.ajax({
                     url: preTally.Initialize.encryptURL("warehouse/getPlaceIds.php&loc=" + place_id)
                     }).done(function(data) {
                     var str = new Object();
                     str = jQuery.parseJSON(data);
                     cnt_id = str[0].CN_Id;
                     st_id = str[0].ST_Id;
                     ct_id = str[0].CT_Id;
                     addOfficeForm.setItemValue("CN_Id", cnt_id);
                     var params = "cid=" + cnt_id;
                     LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&cid=" + cnt_id), function() {
                     addOfficeForm.setItemValue("ST_Id", st_id);
                     });
                     var params = "sid=" + st_id;
                     LocCtyCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&sid=" + st_id), function() {
                     addOfficeForm.setItemValue("CT_Id", ct_id);
                     });
                     
                     });
                     }
                     });
                     
                     LocCtyCombo.attachEvent("onChange", function() {
                     
                     var city_id = LocCtyCombo.getSelectedValue();
                     
                     if (!isNaN(city_id) && city_id !== null && city_id != 0)
                     {
                     $.ajax({
                     url: preTally.Initialize.encryptURL("warehouse/getPlaceIds.php&ctid=" + city_id)
                     }).done(function(data) {
                     var str = new Object();
                     str = jQuery.parseJSON(data);
                     cnt_id = str[0].CN_Id;
                     st_id = str[0].ST_Id;
                     ct_id = str[0].CT_Id;
                     ct_name = str[0].CT_Name;
                     addOfficeForm.setItemValue("CN_Id", cnt_id);
                     LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&cid=" + cnt_id), function() {
                     addOfficeForm.setItemValue("ST_Id", st_id);
                     });
                     var params = "sid=" + st_id;
                     LocCtyCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/cities.php&" + params), function() {
                     LocCtyCombo.setComboText(str[0].ct_name)
                     addOfficeForm.setItemValue("CT_Id", ct_id);
                     });
                     
                     });
                     }
                     
                     
                     
                     
                     }); */

                    addOfficeForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newOfficeValidate') {
                            var newOffice = addOfficeForm.validate();
                            var values = addOfficeForm.getFormData();
                            var Cvalidate = preTally.Validate.Validate(values, 'OF_Name', addOfficeForm, 'title');
                            var CvalidateEmail = preTally.Validate.Validate(values, 'US_Email', addOfficeForm, 'email');
                            if (newOffice && Cvalidate && CvalidateEmail) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addOfficeForm.send(preTally.Initialize.encryptURL('warehouse/newOffice.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if ((response != 'fail') && (response != 'false')) {
                                        addOfficeForm.resetValidateCss();
                                        addOfficeForm.clear();
                                        addOfficeForm.setItemValue("OF_Id", 0);
                                        addOfficeForm.setItemValue("US_Id", 0);
                                        addOfficeForm.setItemValue("CN_Id", 90);
                                        addOfficeForm.setItemValue("ST_Id", 17);
                                        addOfficeForm.setItemValue("AP_Id", '');
                                        //LocPlaceCombo.setComboText("");
                                        addOfficeForm.hideItem("US_Emailtemp");
                                        addOfficeForm.showItem("US_Email");

                                    } else if (response == 'false') {
                                        response = 'Company Email Already Registered. Please Re-Try.';
                                    } else {
                                        response = 'Company Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listOfficeGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listOffice.php"), true, true, function () {
                                        //preTally.Settings.editOffice();
                                    });
                                });
                            }
                        } else {
                            addOfficeForm.hideItem("US_Emailtemp");
                            addOfficeForm.showItem("US_Email");

                            addOfficeForm.resetValidateCss();
                            addOfficeForm.clear();
                            addOfficeForm.setItemValue("OF_Id", 0);
                            addOfficeForm.setItemValue("US_Id", 0);
                        }
                    });
                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewOffice").setActive();
            }
        },
        editOffice: function (rowId) {
            var OFId = rowId;
            //listOfficeGrid.attachEvent("onRowSelect", function(OFId) {
            /*LocSteCombo.clearAll();
             LocCtyCombo.clearAll();
             var locCN_Id = listOfficeGrid.getUserData(OFId, "CN_Id");
             LocSTId = listOfficeGrid.getUserData(OFId, "ST_Id");
             LocCTId = listOfficeGrid.getUserData(OFId, "CT_Id");
             var params = "+cid=" + locCN_Id;
             LocSteCombo.load(preTally.Initialize.encryptURL("requisites/states.php&"+ params), function() {
             
             addOfficeForm.setItemValue("ST_Id", LocSTId);
             });
             var params = "+sid=" + LocSTId;
             LocCtyCombo.load(preTally.Initialize.encryptURL("requisites/cities.php&" +params), function() {
             addOfficeForm.setItemValue("CT_Id", LocCTId);
             }); */
            addOfficeForm.hideItem("US_Email");
            addOfficeForm.showItem("US_Emailtemp");
            addOfficeForm.setItemValue("OF_Id", OFId);
            addOfficeForm.setItemValue("OF_Name", listOfficeGrid.getUserData(OFId, "OF_Name"));
            addOfficeForm.setItemValue("OF_Building", listOfficeGrid.getUserData(OFId, "OF_Building"));
            addOfficeForm.setItemValue("OF_Street", listOfficeGrid.getUserData(OFId, "OF_Street"));
            /* if (listOfficeGrid.getUserData(OFId, "AP_Name") != 0)
             {
             LocPlaceCombo.clearAll();
             dhx4.ajax.get(preTally.Initialize.encryptURL("requisites/places.php&mask=" + listOfficeGrid.getUserData(OFId, "AP_Name")), function(xml) {
             LocPlaceCombo.load(xml.xmlDoc.responseText);
             addOfficeForm.setItemValue("AP_Id", listOfficeGrid.getUserData(OFId, "AP_Id"));
             
             });
             }
             else
             {
             addOfficeForm.setItemValue("AP_Id", '');
             LocPlaceCombo.setComboText("");
             }
             addOfficeForm.setItemValue("CN_Id", locCN_Id);*/
            addOfficeForm.setItemValue("GOGL_Pincode", listOfficeGrid.getUserData(OFId, "OF_Pincode"));
            addOfficeForm.setItemValue("GOGL_Street", listOfficeGrid.getUserData(OFId, "SR_Name"));
            addOfficeForm.setItemValue("GOGL_Place", listOfficeGrid.getUserData(OFId, "PL_Name"));
            addOfficeForm.setItemValue("GOGL_Location", listOfficeGrid.getUserData(OFId, "ALC_Name"));
            addOfficeForm.setItemValue("GOGL_City", listOfficeGrid.getUserData(OFId, "CT_Name"));
            addOfficeForm.setItemValue("GOGL_State", listOfficeGrid.getUserData(OFId, "ST_Name"));
            addOfficeForm.setItemValue("GOGL_Country", listOfficeGrid.getUserData(OFId, "CN_Name"));
            addOfficeForm.setItemValue("OF_Comments", listOfficeGrid.getUserData(OFId, "OF_Comments"));
            addOfficeForm.setItemValue("OF_Status", listOfficeGrid.getUserData(OFId, "OF_Status"));
            addOfficeForm.setItemValue("US_Email", listOfficeGrid.getUserData(OFId, "US_Email"));
            addOfficeForm.setItemValue("US_Emailtemp", listOfficeGrid.getUserData(OFId, "US_Email"));
            addOfficeForm.setItemValue("US_FName", listOfficeGrid.getUserData(OFId, "US_FName"));
            addOfficeForm.setItemValue("US_LName", listOfficeGrid.getUserData(OFId, "US_LName"));
            addOfficeForm.setItemValue("US_Id", listOfficeGrid.getUserData(OFId, "US_Id"));
            addOfficeForm.setItemValue("CR_Id", listOfficeGrid.getUserData(OFId, "CR_Id"));
            addOfficeForm.setItemValue("TZ_Id", listOfficeGrid.getUserData(OFId, "TZ_Id"));
            //});
        },
        menuOpenBalance: function () {
            if (!dhxMiddleBlockTabs.cells("menuOpenBalance")) {
                var filtrInterval;
                dhxMiddleBlockTabs.addTab("menuOpenBalance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Branch Opening Balance", 200);
                dhxMiddleBlockTabs.tabs("menuOpenBalance").setActive();
                var menuOpenBalanceLayout = dhxMiddleBlockTabs.cells("menuOpenBalance").attachLayout('1C');
                menuOpenBalanceLayout.cells("a").setText("Branch Opening Balances");
                listOpenBalanceGrid = menuOpenBalanceLayout.cells("a").attachGrid();
                listOpenBalanceGrid.attachHeader(',<input style="width:80%;" type="text" id="BOS_BranchFilter" >,,<select style="width:90%" id="BOS_MonthFilter" onChange="preTally.Settings.applyMonthFilterBranchOB()"><option value="">All</option><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select>,,<select style="width:80%" id="BOS_StatusFilter" onChange="preTally.Settings.applyMonthFilterBranchOB()"><option value="">All</option><option value="Not Set">Not Set</option><option value="Set">Set</option></select>');

                listOpenBalanceGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listOpenBalanceGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listOpenBalanceGrid.enableTooltips("false,false,false,false,false,false");
                listOpenBalanceGrid.enableColSpan(true);
                listOpenBalanceGrid.init();
                listOpenBalanceGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBrnchOpnBal.php"), function () {
                    listOpenBalanceGrid.attachEvent("onRowSelect", preTally.Settings.setBalance);
                    $("#BOS_BranchFilter").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.Settings.applyMonthFilterBranchOB();
                            clearInterval(filtrInterval);
                        }, 500);
                    });
                });
            } else
            {
                dhxMiddleBlockTabs.tabs("menuOpenBalance").setActive();
            }
        },
        setBalance: function (rowId) {
            if (rowId != "msgRow") {
                var OB_Id = rowId;
                var status = listOpenBalanceGrid.getUserData(rowId, "OB_Status");
//            if (status == 0)
//            {
                var dhxBalWin = new dhtmlXWindows();
                var balWin = dhxBalWin.createWindow("wins_bal", 200, 400, 500, 250);
                balWin.button("minmax1").hide();
                balWin.button("minmax2").hide();
                balWin.button("park").hide();
                balWin.center();
                balWin.setModal(true);
                balWin.setText("Opening Balance");
                var setBalForm = balWin.attachForm();
                setBalForm.loadStruct(preTally.Initialize.encryptURL("requisites/setOpnBalance.php"), function () {

                    var todayDt = Date.today().getDate();
                    if (todayDt < 10)
                        todayDt = '0' + todayDt;

                    var todayMt = Date.today().getMonth() + 1;
                    if (todayMt < 10) {
                        todayMt = '0' + todayMt;
                    }

                    var todayYr = Date.today().getFullYear();

                    var fullDate = todayYr + '-' + todayMt + '-' + todayDt;

                    var obcalendar = setBalForm.getCalendar("OB_Date");
                    obcalendar.setSensitiveRange(null, fullDate);

                    /* obcalendar = setBalForm.getCalendar("OB_Date");
                     //var OB_CalObj = new dhtmlXCalendarObject("OB_Date");
                     var currentTime = new Date();
                     var month = currentTime.getMonth() + 1;
                     var day = currentTime.getDate()+1;
                     var year = currentTime.getFullYear();
                     if(day<10)
                     {
                     day='0'+day;
                     }
                     var date = year + '-' + month + '-' + day;                    
                     obcalendar.setInsensitiveRange(date,null);*/
                    setBalForm.setItemValue("OB_Id", OB_Id);
                    setBalForm.attachEvent("onButtonClick", function (name) {
                        if (name == "SaveOpnBalance")
                        {
                            var OpenBalance = setBalForm.validate();
                            if (OpenBalance)
                            {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                setBalForm.send(preTally.Initialize.encryptURL("warehouse/setOpnBalance.php"), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != "fail")
                                    {
                                        setBalForm.resetValidateCss();
                                        setBalForm.clear();
                                        setBalForm.setItemValue("OB_Id", 0);
                                        dhxBalWin.window("wins_bal").close();
                                    } else {
                                        response = 'Opening Balance cannot be set';
                                    }
                                    dhtmlx.message({text: response});
                                    listOpenBalanceGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBrnchOpnBal.php"), true, true, function () {
                                        preTally.Settings.applyMonthFilterBranchOB();
                                    });
                                });
                            }
                        } else
                        {
                            setBalForm.resetValidateCss();
                            setBalForm.clear();
                            setBalForm.setItemValue("OB_Id", 0);
                            dhxBalWin.window("wins_bal").close();
                        }
                    });
                });
            }
//            }
//            else
//            {
//                dhtmlx.message({text: "Cant edit! Opening Balance already set"});
//            }
        },
        setBranchDetails: function () {

            dhxBranchDetailsWin = new dhtmlXWindows();
            branchDetailsWin = dhxBranchDetailsWin.createWindow("wins_branchDetails", 200, 400, 600, 350);
            branchDetailsWin.button("minmax1").hide();
            branchDetailsWin.button("minmax2").hide();
            branchDetailsWin.button("park").hide();
//                branchDetailsWin.hideHeader();
            branchDetailsWin.center();
            branchDetailsWin.setModal(true);
            branchDetailsWin.setText("Cash Opening Balance & Old Stock Amount");
            setBranchDetailsForm = branchDetailsWin.attachForm();
            setBranchDetailsForm.loadStruct(preTally.Initialize.encryptURL("requisites/setBranchDetails.php"), function () {

                setBranchDetailsForm.attachEvent("onButtonClick", function (name) {
                    if (name == "SaveBranchDetails")
                    {
                        var BranchDetails = setBranchDetailsForm.validate();
                        if (BranchDetails) {
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            setBranchDetailsForm.send(preTally.Initialize.encryptURL("warehouse/setBranchDetails.php"), function (loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if (response != "fail" && response != "success")
                                {
                                    dhxBranchDetailsWin.window("wins_branchDetails").close();
                                    dhtmlx.alert({
                                        title: "Success",
                                        text: "Your settings have been saved successfully !! Please note this Track Id for further entries of old stocks <br> TRACK ID : " + response,
                                        //callback: function() {dhtmlx.alert("Test alert");}
                                    });
                                    dhxGridBalSheet.loadXML(preTally.Initialize.encryptURL("requisites/listBSItems.php"), function () { });
                                } else if (response == 'success') {
                                    dhxBranchDetailsWin.window("wins_branchDetails").close();
                                    dhtmlx.message({text: 'Cash Opening Balance Successfully Saved.'});
                                }
//                                    
                            });
                        }
                    } else {
                        dhxBranchDetailsWin.window("wins_branchDetails").close();
                    }
                });
            });

        },
        menuBnkOpenBalance: function () {
            if (!dhxMiddleBlockTabs.cells("menuBnkOpenBalance")) {
                var filtrInterval;
                dhxMiddleBlockTabs.addTab("menuBnkOpenBalance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Bank Opening Balance", 200);
                dhxMiddleBlockTabs.tabs("menuBnkOpenBalance").setActive();
                var menuBnkOpenBalanceLayout = dhxMiddleBlockTabs.cells("menuBnkOpenBalance").attachLayout('1C');
                menuBnkOpenBalanceLayout.cells("a").setText("Bank Opening Balances");
                listBnkBalanceGrid = menuBnkOpenBalanceLayout.cells("a").attachGrid();
                listBnkBalanceGrid.attachHeader(',<input style="width:80%;" type="text" id="BOB_BranchFilter" >,,<select style="width:90%" id="BOB_MonthFilter" onChange="preTally.Settings.applyMonthFilterBankOB()"><option value="">All</option><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select>,,<select style="width:80%" id="BOB_StatusFilter" onChange="preTally.Settings.applyMonthFilterBankOB()"><option value="">All</option><option value="Not Set">Not Set</option><option value="Set">Set</option></select>');

                listBnkBalanceGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listBnkBalanceGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listBnkBalanceGrid.enableTooltips("false,false,false,false,false,false");
                listBnkBalanceGrid.init();
                listBnkBalanceGrid.enableColSpan(true);

                listBnkBalanceGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBnkOpnBal.php"), function () {
                    listBnkBalanceGrid.attachEvent("onRowSelect", preTally.Settings.setBnkBalance);
                    $("#BOB_BranchFilter").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.Settings.applyMonthFilterBankOB();
                            clearInterval(filtrInterval);
                        }, 500);
                    });
                });
            } else
            {
                dhxMiddleBlockTabs.tabs("menuBnkOpenBalance").setActive();
            }
        },
        setBnkBalance: function (rowId) {
            if (rowId != "msgRow") {
                var BnkOB_Id = rowId;
                var status = listBnkBalanceGrid.getUserData(rowId, "OB_Status");
                if (status == 0)
                {
                    var dhxBnkBalWin = new dhtmlXWindows();
                    var bnkbalWin = dhxBnkBalWin.createWindow("wins_bal", 200, 400, 500, 250);
                    bnkbalWin.button("minmax1").hide();
                    bnkbalWin.button("minmax2").hide();
                    bnkbalWin.button("park").hide();
                    bnkbalWin.center();
                    bnkbalWin.setModal(true);
                    bnkbalWin.setText("Bank Opening Balance");
                    var setBnkBalForm = bnkbalWin.attachForm();
                    setBnkBalForm.loadStruct(preTally.Initialize.encryptURL("requisites/setOpnBalance.php"), function () {
                        obbnkcalendar = setBnkBalForm.getCalendar("OB_Date");
//                    var currentTime = new Date();
//                    var month = currentTime.getMonth() + 1;
//                    var day = currentTime.getDate();
//                    var year = currentTime.getFullYear();
//                    var date = day + '.' + month + '.' + year;
//                    obbnkcalendar.setInsensitiveRange(date, null);
                        setBnkBalForm.setItemValue("OB_Id", BnkOB_Id);
                        setBnkBalForm.attachEvent("onButtonClick", function (name) {
                            if (name == "SaveOpnBalance")
                            {
                                var OpenBalance = setBnkBalForm.validate();
                                if (OpenBalance)
                                {
                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                    setBnkBalForm.send(preTally.Initialize.encryptURL("warehouse/setBnkOpnBalance.php"), function (loader, response) {
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                        if (response != "fail")
                                        {
                                            setBnkBalForm.resetValidateCss();
                                            setBnkBalForm.clear();
                                            setBnkBalForm.setItemValue("OB_Id", 0);
                                            dhxBnkBalWin.window("wins_bal").close();
                                        } else {
                                            response = 'Bank Opening Balance cannot be set';
                                        }
                                        dhtmlx.message({text: response});
                                        listBnkBalanceGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBnkOpnBal.php"), true, true, function () {
                                            preTally.Settings.applyMonthFilterBankOB();
                                        });
                                    });
                                }
                            } else
                            {
                                setBnkBalForm.resetValidateCss();
                                setBnkBalForm.clear();
                                setBnkBalForm.setItemValue("OB_Id", 0);
                                dhxBnkBalWin.window("wins_bal").close();
                            }
                        });
                    });
                } else
                {
                    dhtmlx.message({text: "Cant edit! Bank Balance already set"});
                }
            }
        },
        menuOldStockAmount: function () {
            if (!dhxMiddleBlockTabs.cells("menuOldStockAmount")) {
                dhxMiddleBlockTabs.addTab("menuOldStockAmount", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Old Stock Details", 200);
                dhxMiddleBlockTabs.tabs("menuOldStockAmount").setActive();
                var menuOldStockAmountLayout = dhxMiddleBlockTabs.cells("menuOldStockAmount").attachLayout('1C');
                menuOldStockAmountLayout.cells("a").setText("Old Stock Details");
                listOldStockAmntGrid = menuOldStockAmountLayout.cells("a").attachGrid();
                //,#text_filter_inc,,,,#select_filter_strict,
                listOldStockAmntGrid.attachHeader(",#text_filter_inc,,,<select id='oldStkDate' style='width:90%; font-size:8pt; font-family:Tahoma;' ><option value=''>All</option><option value='January'>Jan</option><option value='February'>Feb</option><option value='March'>Mar</option><option value='April'>Apr</option><option value='May'>May</option><option value='June'>Jun</option><option value='July'>Jul</option><option value='August'>Aug</option><option value='September'>Sep</option><option value='October'>Oct</option><option value='November'>Nov</option><option value='December'>Dec</option></select>,,#select_filter_strict,");
                listOldStockAmntGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listOldStockAmntGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listOldStockAmntGrid.init();

                listOldStockAmntGrid.enableTooltips("false,false,false,false,false,false");

//                listOldStockAmntGrid.attachEvent("onMouseOver", function(id,ind) { 
//                    //if(ind == 6) {
//                        this.cells(id,ind).cell.title = 'Click to Set Opening Balance.';
//                        return false;
//                    //}
//                });

                listOldStockAmntGrid.loadXML(preTally.Initialize.encryptURL("requisites/listOldStockAmount.php"), function () {
                    //listOldStockAmntGrid.makeFilter("oldStkDate",5);
                    $('#oldStkDate').change(function () {
                        listOldStockAmntGrid.filterBy(5, this.value)
                    });
                    listOldStockAmntGrid.attachEvent("onRowSelect", preTally.Settings.setOldStockAmount);
                });
                listOldStockAmntGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listOldStockAmntGrid.getRowsNum() == 0) {
                        listOldStockAmntGrid.addRow("msgRow", ['', '', '', 'Record Not Found', '', '', ''], 0);
                        listOldStockAmntGrid.setRowTextStyle("msgRow", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        //preTally.MasterReports.calculateFooterValues();  
                    }

                });
            } else
            {
                dhxMiddleBlockTabs.tabs("menuOldStockAmount").setActive();
            }
        },
        setOldStockAmount: function (rowId) {
            if (rowId != "msgRow") {
                dhxStockWin = new dhtmlXWindows();
                stockWin = dhxStockWin.createWindow("wins_oldStock", 200, 400, 400, 260);
                stockWin.button("minmax1").hide();
                stockWin.button("minmax2").hide();
                stockWin.button("park").hide();
                stockWin.center();
                stockWin.setModal(true);
                stockWin.setText("Old Stock Amount");
                setStockForm = stockWin.attachForm();
//                var params = "LCId="+rowId+"&TR_Track="+listOldStockAmntGrid.getUserData(rowId, "TR_Track");
                var params = "OSId=" + rowId;
                setStockForm.loadStruct(preTally.Initialize.encryptURL("requisites/setOldStockAmnt.php&" + params), function () {

                    setStockForm.setItemValue("OS_Status", listOldStockAmntGrid.getUserData(rowId, "OS_Status"));

//                    setStockForm.setItemValue("BS_Amount", listOldStockAmntGrid.getUserData(rowId, "BS_Amount"));
//                    setStockForm.setItemValue("BS_Date", listOldStockAmntGrid.getUserData(rowId, "BS_Date"));
                    setStockForm.setItemValue("LC_Name", listOldStockAmntGrid.getUserData(rowId, "LC_Name"));
                    setStockForm.setItemValue("LC_Id", rowId);
//                    setStockForm.setItemValue("BS_Id", listOldStockAmntGrid.getUserData(rowId, "BS_Id"));
//                    setStockForm.setItemValue("US_Id", listOldStockAmntGrid.getUserData(rowId, "US_Id"));
//                    setStockForm.setItemValue("TR_Track", 'OS_'+rowId+'_'+listOldStockAmntGrid.getUserData(rowId, "US_Id"));

                    setStockForm.attachEvent("onButtonClick", function (name) {
                        if (name == "SaveOldStkAmt")
                        {
                            var OldStock = setStockForm.validate();
                            if (OldStock) {

                                preTally.Settings.progressOn(true, dhxLayout, null);
                                setStockForm.send(preTally.Initialize.encryptURL("warehouse/setOldStockAmnt.php"), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != "fail" && response != 'success')
                                    {
                                        dhxStockWin.window("wins_oldStock").close();
                                        dhtmlx.alert({
                                            title: "Success",
                                            text: "Your settings have been saved successfully !! Please note this Track Id for further entries of old stocks <br> TRACK ID : " + response,
                                        });
                                        response = 'Old Stock Amount Added Successfully';
                                    } else if (response == 'success') {
                                        response = 'Old Stock Amount Updated Successfully';
                                        dhxStockWin.window("wins_oldStock").close();
                                    }
                                    dhtmlx.message({text: response});
                                    listOldStockAmntGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listOldStockAmount.php"), true, true, function () {
                                    });
                                });
                            }
                        } else
                        {
                            setStockForm.resetValidateCss();
                            setStockForm.clear();
                            setStockForm.setItemValue("BS_Id", 0);
                            dhxStockWin.window("wins_oldStock").close();
                        }
                    });
                });
            }
        },
        menuNewDepartment: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewDepartment")) {
                dhxMiddleBlockTabs.addTab("menuNewDepartment", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Departments", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewDepartment");
                dhxMiddleBlockTabs.tabs("menuNewDepartment").setActive();
                var menuDepartmentLayout = dhxMiddleBlockTabs.cells("menuNewDepartment").attachLayout('2U');
                menuDepartmentLayout.cells("a").setText("New Department");
                menuDepartmentLayout.cells("b").setText("List Department");
                menuDepartmentLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Department --------------------//
                listDepartmentGrid = menuDepartmentLayout.cells("b").attachGrid();
                listDepartmentGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listDepartmentGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listDepartmentGrid.enableTooltips("false,false,false,false");
                var offId = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (offId == 1) {
                    listDepartmentGrid.enableTooltips("false,false,false,false,false,true");
                    listDepartmentGrid.attachHeader(',#text_filter_inc,#select_filter,,,<select id="DeptStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                } else {
                    listDepartmentGrid.enableTooltips("false,false,false,false,true");
                    listDepartmentGrid.attachHeader(',#text_filter_inc,,,<select id="DeptStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                }
                listDepartmentGrid.init();
                listDepartmentGrid.loadXML(preTally.Initialize.encryptURL("requisites/listDepartment.php"), function () {
                    if (offId == 1) {
                        listDepartmentGrid.makeFilter("DeptStatus", 4);
                        listDepartmentGrid.getFilterElement(4).value = "Approved";
                        listDepartmentGrid.filterBy(4, "Approved");
                    } else {
                        listDepartmentGrid.makeFilter("DeptStatus", 3);
                        listDepartmentGrid.getFilterElement(3).value = "Approved";
                        listDepartmentGrid.filterBy(3, "Approved");
                        /*Set Order*/
                        rowVal = listDepartmentGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listDepartmentGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            listDepartmentGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }

                    listDepartmentGrid.attachEvent("onRowSelect", preTally.Settings.editDepartment);

                });

                //----------------- Attach Form for Department --------------------//
                addDepartmentForm = menuDepartmentLayout.cells("a").attachForm();
                addDepartmentForm.loadStruct(preTally.Initialize.encryptURL("requisites/newDepartment.php&r=" + new Date().getTime()), function () {
                    var weekCombo = addDepartmentForm.getCombo("DH_Weekends");
                    weekCombo.setComboText("Select Off Days");
                    weekCombo.attachEvent("onClose", function () {
                        var weekArray = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
                        var weekString = String(weekCombo.getChecked());

                        var dayArray = weekString.split(",");
                        var combostring = "";
                        for (i = 0; i < dayArray.length; i++) {
                            combostring = combostring + "," + weekArray[dayArray[i] - 1];
                        }
                        combostring = combostring.replace(/^,/, '');
                        //console.log(dayArray.length);
                        if (!dayArray[dayArray.length - 1]) {
                            combostring = "Select Off Days";
                        }
                        weekCombo.setComboText(combostring);
                    });
                    addDepartmentForm.attachEvent("onButtonClick", function (name) {
                        var weekCombo = addDepartmentForm.getCombo("DH_Weekends");
                        if (name == 'newDepartmentValidate') {

                            var weekDays = weekCombo.getChecked();
                            if (weekDays.length != 0)
                                addDepartmentForm.setItemValue("H_Weekend", weekDays);
                            else
                                addDepartmentForm.setItemValue("H_Weekend", "");
                            var newDepartment = addDepartmentForm.validate();
                            var dh_status = true;
                            if (weekDays.length == 0) {
                                addDepartmentForm.setValidateCss("DH_Weekends", false, "validate_red");
                                dh_status = false;
                            }
                            if (newDepartment && dh_status) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addDepartmentForm.send(preTally.Initialize.encryptURL('warehouse/newDepartment.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addDepartmentForm.resetValidateCss();
                                        addDepartmentForm.clear();
                                        addDepartmentForm.setItemValue("DP_Id", 0);
                                        listDepartmentGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listDepartment.php"), true, true, function () {
                                            // preTally.Settings.editDepartment();
                                        });
                                        for (var i = 0; i < 7; i++) {
                                            weekCombo.setChecked(i, false);
                                        }
                                        weekCombo.setComboText("Select Off Days");
                                    } else {
                                        response = 'Department Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                });
                            }
                        } else {
                            addDepartmentForm.resetValidateCss();
                            addDepartmentForm.clear();
                            addDepartmentForm.setItemValue("DP_Id", 0);
                            addDepartmentForm.setItemValue("OF_Id", "");
                            for (var i = 0; i < 7; i++) {
                                weekCombo.setChecked(i, false);
                            }
                            weekCombo.setComboText("Select Off Days");
                        }
                    });
                });
                listDepartmentGrid.attachEvent("onFilterEnd", function (elements) {



                    if (listDepartmentGrid.getRowsNum() == 0) {
                        listDepartmentGrid.addRow("row1", ['', 'Record Not Found', '', ''], 0);
                        //listDepartmentGrid.setColspan("row1",1,5)
                        listDepartmentGrid.setRowTextStyle("row1", "font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                    } else {
                        /*Set Order*/
                        rowVal = listDepartmentGrid.getAllRowIds();
                        exprowval = rowVal.split(',');
                        for (var i = 0; i < listDepartmentGrid.getRowsNum(); i++) {
                            eachRowId = exprowval[i];
                            listDepartmentGrid.cells(eachRowId, 0).setValue(i + 1);
                        }
                        /*Set Order*/
                    }
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewDepartment").setActive();
            }
        },
        editDepartment: function (rowId) {
            var DPId = rowId;
            addDepartmentForm.setItemValue("DP_Id", DPId);
            addDepartmentForm.setItemValue("DP_Name", listDepartmentGrid.getUserData(DPId, "DP_Name"));
            addDepartmentForm.setItemValue("DP_Comments", listDepartmentGrid.getUserData(DPId, "DP_Comments"));
            addDepartmentForm.setItemValue("DP_Status", listDepartmentGrid.getUserData(DPId, "DP_Status"));
            addDepartmentForm.setItemValue("OF_Id", listDepartmentGrid.getUserData(DPId, "OF_Id"));
            var Weekdays = listDepartmentGrid.getUserData(DPId, "DH_Weekends");
            var weekCombo = addDepartmentForm.getCombo("DH_Weekends");
            var WeekArray = Weekdays.split(",");
            for (var i = 0; i < 7; i++) {
                weekCombo.setChecked(i, false);
                if (WeekArray.indexOf(weekCombo.getOptionByIndex(i).value) > -1) {
                    weekCombo.setChecked(i, true);
                }
            }
            var WeekString = listDepartmentGrid.getUserData(DPId, "DH_WeekString");
            weekCombo.setComboText(WeekString);
        },
        menuNewUnit: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewUnit"))
            {
                dhxMiddleBlockTabs.addTab("menuNewUnit", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Units", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewUnit");
                dhxMiddleBlockTabs.tabs("menuNewUnit").setActive();
                var menuUnitLayout = dhxMiddleBlockTabs.cells("menuNewUnit").attachLayout('2U');
                menuUnitLayout.cells("a").setText("New Unit");
                menuUnitLayout.cells("b").setText("List Unit");
                menuUnitLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Unit--------------------//
                listUnitGrid = menuUnitLayout.cells("b").attachGrid();
                listUnitGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listUnitGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listUnitGrid.init();
                listUnitGrid.loadXML(preTally.Initialize.encryptURL("requisites/listUnit.php"), function () {
                    listUnitGrid.attachEvent("onRowSelect", preTally.Settings.editUnit);
                });
                //----------------- Attach Grid for Unit--------------------//
                addUnitForm = menuUnitLayout.cells("a").attachForm();
                addUnitForm.loadStruct(preTally.Initialize.encryptURL("requisites/newUnit.php&r=" + new Date().getTime()), function () {

                    addUnitForm.attachEvent("onButtonClick", function (name) {

                        if (name == 'newUnitValidate') {
                            var newUnit = addUnitForm.validate();

                            if (newUnit) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addUnitForm.send(preTally.Initialize.encryptURL('warehouse/newUnit.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addUnitForm.resetValidateCss();
                                        addUnitForm.clear();
                                        addUnitForm.setItemValue("UT_Id", 0);
                                    } else {
                                        response = 'Unit Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listUnitGrid.clearAll();
                                    //listUnitGrid.load("requisites/jsontest.php","json");
                                    listUnitGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listUnit.php"), true, true, function () {
                                        //preTally.Settings.editUnit();
                                    });
                                });
                            }
                        } else {
                            addUnitForm.resetValidateCss();
                            addUnitForm.clear();
                            addUnitForm.setItemValue("UT_Id", 0);
                        }

                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewUnit").setActive();
            }
        },
        editUnit: function (rowId) {
            var UnitId = rowId;
            // listUnitGrid.attachEvent("onRowSelect", function(UnitId) {
            addUnitForm.setItemValue("UT_Id", UnitId);
            addUnitForm.setItemValue("UT_Name", listUnitGrid.getUserData(UnitId, "UT_Name"));
            addUnitForm.setItemValue("UT_Comments", listUnitGrid.getUserData(UnitId, "UT_Comments"));
            addUnitForm.setItemValue("UT_Status", listUnitGrid.getUserData(UnitId, "UT_Status"));

            // });
        },
        menuNewPaymode: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewPaymode"))
            {
                dhxMiddleBlockTabs.addTab("menuNewPaymode", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Payment Mode", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewPaymode");
                dhxMiddleBlockTabs.tabs("menuNewPaymode").setActive();
                var menuPaymodeLayout = dhxMiddleBlockTabs.cells("menuNewPaymode").attachLayout('2U');
                menuPaymodeLayout.cells("a").setText("New Payment Mode");
                menuPaymodeLayout.cells("b").setText("List Payment Mode");
                menuPaymodeLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for PaymentMode--------------------//
                listPaymodeGrid = menuPaymodeLayout.cells("b").attachGrid();
                listPaymodeGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listPaymodeGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listPaymodeGrid.init();
                listPaymodeGrid.loadXML(preTally.Initialize.encryptURL("requisites/listPaymode.php"), function () {
                    listPaymodeGrid.attachEvent("onRowSelect", preTally.Settings.editPayMode);
                });
                addPaymodeForm = menuPaymodeLayout.cells("a").attachForm();
                addPaymodeForm.loadStruct(preTally.Initialize.encryptURL("requisites/newPaymode.php&r=" + new Date().getTime()), function () {
                    addPaymodeForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newModeValidate') {
                            var newPaymode = addPaymodeForm.validate();

                            if (newPaymode) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addPaymodeForm.send(preTally.Initialize.encryptURL('warehouse/newPaymode.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addPaymodeForm.resetValidateCss();
                                        addPaymodeForm.clear();
                                        addPaymodeForm.setItemValue("PM_Id", 0);
                                    } else {
                                        response = 'Payment Mode Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listPaymodeGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listPaymode.php"), true, true, function () {

                                    });
                                });
                            }
                        } else {
                            addPaymodeForm.resetValidateCss();
                            addPaymodeForm.clear();
                            addPaymodeForm.setItemValue("PM_Id", 0);
                        }

                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewPaymode").setActive();
            }
        },
        editPayMode: function (rowId) {
            //listPaymodeGrid.attachEvent("onRowSelect", function(PMId) { //$('.PaymodeEdit').click(function(e) {                                
            var PMId = rowId;
            addPaymodeForm.setItemValue("PM_Id", PMId);
            addPaymodeForm.setItemValue("PM_Name", listPaymodeGrid.getUserData(PMId, "PM_Name"));
            addPaymodeForm.setItemValue("PM_Comments", listPaymodeGrid.getUserData(PMId, "PM_Comments"));
            addPaymodeForm.setItemValue("PM_Status", listPaymodeGrid.getUserData(PMId, "PM_Status"));
            //});
        },
        menuNewSalPaymode: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewSalPaymode"))
            {
                dhxMiddleBlockTabs.addTab("menuNewSalPaymode", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Salary Payment Mode", 180);
                //dhxMiddleBlockTabs.setTabActive("menuNewSalPaymode");
                dhxMiddleBlockTabs.tabs("menuNewSalPaymode").setActive();
                var menuSalPaymodeLayout = dhxMiddleBlockTabs.cells("menuNewSalPaymode").attachLayout('2U');
                menuSalPaymodeLayout.cells("a").setText("New Salary Payment Mode");
                menuSalPaymodeLayout.cells("b").setText("List Salary Payment Mode");
                menuSalPaymodeLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Unit--------------------//
                listSalPaymodeGrid = menuSalPaymodeLayout.cells("b").attachGrid();
                listSalPaymodeGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listSalPaymodeGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listSalPaymodeGrid.init();
                listSalPaymodeGrid.loadXML(preTally.Initialize.encryptURL("requisites/listSalPaymode.php"), function () {
                    listSalPaymodeGrid.attachEvent("onRowSelect", preTally.Settings.editSalPayMode);
                });
                //----------------- Attach Grid for Unit--------------------//
                addSalPaymodeForm = menuSalPaymodeLayout.cells("a").attachForm();
                addSalPaymodeForm.loadStruct(preTally.Initialize.encryptURL("requisites/newSalPaymode.php&r=" + new Date().getTime()), function () {

                    addSalPaymodeForm.attachEvent("onButtonClick", function (name) {



                        if (name == 'newSalPayValidate') {
                            var newSalPaymode = addSalPaymodeForm.validate();

                            if (newSalPaymode) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addSalPaymodeForm.send(preTally.Initialize.encryptURL('warehouse/newSalPaymode.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addSalPaymodeForm.resetValidateCss();
                                        addSalPaymodeForm.clear();
                                        addSalPaymodeForm.setItemValue("SP_Id", 0);
                                    } else {
                                        response = 'Payment Mode Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listSalPaymodeGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listSalPaymode.php"), true, true, function () {
                                        //preTally.Settings.editSalPayMode();
                                    });
                                });
                            }
                        } else {
                            addSalPaymodeForm.resetValidateCss();
                            addSalPaymodeForm.clear();
                            addSalPaymodeForm.setItemValue("SP_Id", 0);
                        }

                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewSalPaymode").setActive();
            }
        },
        editSalPayMode: function (rowId) {
            //listSalPaymodeGrid.attachEvent("onRowSelect", function(SPId) { //$('.SalPaymodeEdit').click(function(e) {      
            var SPId = rowId;
            addSalPaymodeForm.setItemValue("SP_Id", SPId);
            addSalPaymodeForm.setItemValue("SP_Name", listSalPaymodeGrid.getUserData(SPId, "SP_Name"));
            addSalPaymodeForm.setItemValue("SP_Comments", listSalPaymodeGrid.getUserData(SPId, "SP_Comments"));
            addSalPaymodeForm.setItemValue("SP_Status", listSalPaymodeGrid.getUserData(SPId, "SP_Status"));
            //});
        },
        menuNewSalStruct: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewSalStruct")) {
                dhxMiddleBlockTabs.addTab("menuNewSalStruct", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Salary Structure", 180);
                //dhxMiddleBlockTabs.setTabActive("menuNewSalStruct");
                dhxMiddleBlockTabs.tabs("menuNewSalStruct").setActive();
                var menuSalStructLayout = dhxMiddleBlockTabs.cells("menuNewSalStruct").attachLayout('2U');
                menuSalStructLayout.cells("a").setText("New Salary Structure ");
                menuSalStructLayout.cells("b").setText("List Salary Structure ");
                menuSalStructLayout.cells("a").setWidth(850);
                //----------------- Attach Grid for Unit--------------------//
                listSalStructGrid = menuSalStructLayout.cells("b").attachGrid();
                listSalStructGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listSalStructGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                var offId = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                if (offId == 1) {
                    listSalStructGrid.enableTooltips("false,false,false,false,true");
                    listSalStructGrid.attachHeader(',,,,<select id="LSStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>,');
                } else {
                    listSalStructGrid.enableTooltips("false,false,false,true");
                    listSalStructGrid.attachHeader(',,,<select id="LSStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                }
                listSalStructGrid.enableTooltips("false,false,false");
                listSalStructGrid.init();

                listSalStructGrid.loadXML(preTally.Initialize.encryptURL("requisites/listSalStruct.php"), function () {
                    if (offId == 1) {
                        listSalStructGrid.makeFilter("LSStatus", 3);
                    } else {
                        listSalStructGrid.makeFilter("LSStatus", 2);
                    }
                    listSalStructGrid.attachEvent("onRowSelect", preTally.Settings.editSalStruct);
                    //preTally.Settings.editSalStruct();
                });
                //----------------- Attach Grid for Unit--------------------//
                addSalStructForm = menuSalStructLayout.cells("a").attachForm();
                addSalStructForm.loadStruct(preTally.Initialize.encryptURL("requisites/newSalStruct.php&r=" + new Date().getTime()), function () {
                    addSalStructForm.attachEvent("onButtonClick", function (name) {
                        basic_per = parseInt(addSalStructForm.getItemValue("SS_Basic"));
                        //da_per      = parseInt(addSalStructForm.getItemValue("SS_DA"));
                        hra_per = parseInt(addSalStructForm.getItemValue("SS_HRA"));
                        cca_per = parseInt(addSalStructForm.getItemValue("SS_CCA"));
                        convey_per = parseInt(addSalStructForm.getItemValue("SS_Convey"));
                        edu_per = parseInt(addSalStructForm.getItemValue("SS_Edu"));
                        medi_per = parseInt(addSalStructForm.getItemValue("SS_Medic"));
                        misc_per = parseInt(addSalStructForm.getItemValue("SS_Misc"));
                        basic_type = parseInt(addSalStructForm.getItemValue("SS_Basic_Type"));
                        da_type = parseInt(addSalStructForm.getItemValue("SS_DA_Type"));
                        hra_type = parseInt(addSalStructForm.getItemValue("SS_HRA_Type"));
                        cca_type = parseInt(addSalStructForm.getItemValue("SS_CCA_Type"));
                        convey_type = parseInt(addSalStructForm.getItemValue("SS_Convey_Type"));
                        edu_type = parseInt(addSalStructForm.getItemValue("SS_Edu_Type"));
                        medi_type = parseInt(addSalStructForm.getItemValue("SS_Medic_Type"));
                        misc_type = parseInt(addSalStructForm.getItemValue("SS_Misc_Type"));
                        var tot = 0;
                        if (basic_type == 0)
                            tot += basic_per;
                        //if(da_type==0)tot+=da_per;
                        if (hra_type == 0)
                            tot += hra_per;
                        if (cca_type == 0)
                            tot += cca_per;
                        if (convey_type == 0)
                            tot += convey_per;
                        if (edu_type == 0)
                            tot += edu_per;
                        if (medi_type == 0)
                            tot += medi_per;
                        if (misc_type == 0)
                            tot += misc_per;

                        //tot = basic_per + da_per + hra_per + convey_per + edu_per + medi_per + misc_per;

                        if (name == 'newSalStructValidate') {
                            if (tot > 100)
                                dhtmlx.message("Percentage greater than 100%");
                            else {
                                var newSalStruct = addSalStructForm.validate();
                                if (newSalStruct) {
                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                    addSalStructForm.send(preTally.Initialize.encryptURL('warehouse/newSalStruct.php'), function (loader, response) {
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                        if (response != 'fail') {
                                            addSalStructForm.resetValidateCss();
                                            addSalStructForm.clear();
                                            addSalStructForm.setItemValue("SS_Id", 0);
                                        } else {
                                            response = 'Structure Exists. Please Re-Try';
                                        }
                                        dhtmlx.message({text: response});
                                        listSalStructGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listSalStruct.php"), true, true, function () {
                                            //preTally.Settings.editSalStruct();
                                        });
                                    });
                                }
                            }
                        } else {
                            addSalStructForm.resetValidateCss();
                            addSalStructForm.clear();
                            addSalStructForm.setItemValue("SS_Id", 0);
                            addSalStructForm.setItemValue("OF_Id", 0);
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewSalStruct").setActive();
            }
        },
        editSalStruct: function (rowId) {
            var SalStructId = rowId;
            addSalStructForm.setItemValue("SS_CFlag", listSalStructGrid.getUserData(SalStructId, "SS_CFlag"));
            //preTally.Settings.changeSalStructForm();
            addSalStructForm.setItemValue("SS_Id", SalStructId);
            addSalStructForm.setItemValue("OF_Id", listSalStructGrid.getUserData(SalStructId, "OF_Id"));
            addSalStructForm.setItemValue("SS_Name", listSalStructGrid.getUserData(SalStructId, "SS_Name"));
            addSalStructForm.setItemValue("SS_Basic", listSalStructGrid.getUserData(SalStructId, "SS_Basic"));
            addSalStructForm.setItemValue("SS_Basic_Type", listSalStructGrid.getUserData(SalStructId, "SS_Basic_Type"));
            //addSalStructForm.setItemValue("SS_DA", listSalStructGrid.getUserData(SalStructId, "SS_DA"));
            //addSalStructForm.setItemValue("SS_DA_Type", listSalStructGrid.getUserData(SalStructId, "SS_DA_Type"));
            addSalStructForm.setItemValue("SS_HRA", listSalStructGrid.getUserData(SalStructId, "SS_HRA"));
            addSalStructForm.setItemValue("SS_HRA_Type", listSalStructGrid.getUserData(SalStructId, "SS_HRA_Type"));
            addSalStructForm.setItemValue("SS_CCA", listSalStructGrid.getUserData(SalStructId, "SS_CCA"));
            addSalStructForm.setItemValue("SS_CCA_Type", listSalStructGrid.getUserData(SalStructId, "SS_CCA_Type"));
            addSalStructForm.setItemValue("SS_Convey", listSalStructGrid.getUserData(SalStructId, "SS_Convey"));
            addSalStructForm.setItemValue("SS_Convey_Type", listSalStructGrid.getUserData(SalStructId, "SS_Convey_Type"));
            addSalStructForm.setItemValue("SS_Edu", listSalStructGrid.getUserData(SalStructId, "SS_Edu"));
            addSalStructForm.setItemValue("SS_Edu_Type", listSalStructGrid.getUserData(SalStructId, "SS_Edu_Type"));
            addSalStructForm.setItemValue("SS_Medic", listSalStructGrid.getUserData(SalStructId, "SS_Medic"));
            addSalStructForm.setItemValue("SS_Medic_Type", listSalStructGrid.getUserData(SalStructId, "SS_Medic_Type"));
            addSalStructForm.setItemValue("SS_Misc", listSalStructGrid.getUserData(SalStructId, "SS_Misc"));
            addSalStructForm.setItemValue("SS_Misc_Type", listSalStructGrid.getUserData(SalStructId, "SS_Misc_Type"));
            addSalStructForm.setItemValue("SS_Status", listSalStructGrid.getUserData(SalStructId, "SS_Status"));

            addSalStructForm.setItemValue("SS_DedESI", listSalStructGrid.getUserData(SalStructId, "SS_DedESI"));
            addSalStructForm.setItemValue("SS_DedESI_Type", listSalStructGrid.getUserData(SalStructId, "SS_DedESI_Type"));
            addSalStructForm.setItemValue("SS_DedEPF", listSalStructGrid.getUserData(SalStructId, "SS_DedEPF"));
            addSalStructForm.setItemValue("SS_DedEPF_Type", listSalStructGrid.getUserData(SalStructId, "SS_DedEPF_Type"));
            addSalStructForm.setItemValue("SS_DedLWF", listSalStructGrid.getUserData(SalStructId, "SS_DedLWF"));
            addSalStructForm.setItemValue("SS_DedLWF_Type", listSalStructGrid.getUserData(SalStructId, "SS_DedLWF_Type"));
            addSalStructForm.setItemValue("SS_DedProfTDS", listSalStructGrid.getUserData(SalStructId, "SS_DedProfTDS"));
            //addSalStructForm.setItemValue("SS_DedProfTDS_Type", listSalStructGrid.getUserData(SalStructId, "SS_DedProfTDS_Type"));            
            addSalStructForm.setItemValue("SS_EmpConEPF", listSalStructGrid.getUserData(SalStructId, "SS_EmpConEPF"));
            addSalStructForm.setItemValue("SS_EmpConEPF_Type", listSalStructGrid.getUserData(SalStructId, "SS_EmpConEPF_Type"));
            addSalStructForm.setItemValue("SS_EmpConESI", listSalStructGrid.getUserData(SalStructId, "SS_EmpConESI"));
            addSalStructForm.setItemValue("SS_EmpConESI_Type", listSalStructGrid.getUserData(SalStructId, "SS_EmpConESI_Type"));
            addSalStructForm.setItemValue("SS_EmpConLWF", listSalStructGrid.getUserData(SalStructId, "SS_EmpConLWF"));
            addSalStructForm.setItemValue("SS_EmpConLWF_Type", listSalStructGrid.getUserData(SalStructId, "SS_EmpConLWF_Type"));


        },
        changeSalStructForm: function () {
            if (addSalStructForm.getItemValue("SS_CFlag") == 1) {
                addSalStructForm.setItemWidth("SS_Basic", "80");
                addSalStructForm.setItemWidth("SS_DA", "80");
                addSalStructForm.setItemWidth("SS_HRA", "80");
                addSalStructForm.setItemWidth("SS_Convey", "80");
                addSalStructForm.setItemWidth("SS_Edu", "80");
                addSalStructForm.setItemWidth("SS_Medic", "80");
                addSalStructForm.setItemWidth("SS_Misc", "80");
                addSalStructForm.setNote("SS_Basic", {text: "Basic Amount", width: 90});
                addSalStructForm.setNote("SS_DA", {text: "DA Amount", width: 90});
                addSalStructForm.setNote("SS_HRA", {text: "HRA Amount", width: 90});
                addSalStructForm.setNote("SS_Convey", {text: "Conveyance Amount", width: 150});
                addSalStructForm.setNote("SS_Edu", {text: "Education Amount", width: 150});
                addSalStructForm.setNote("SS_Medic", {text: "Meidcal Amount", width: 150});
                addSalStructForm.setNote("SS_Misc", {text: "Miscellanious Amount", width: 150});
//                                addSalStructForm.setReadonly("SS_Basic", true);
//                                addSalStructForm.setReadonly("SS_DA", true);
//                                addSalStructForm.setReadonly("SS_HRA", true);
//                                addSalStructForm.setReadonly("SS_Convey", true);
//                                addSalStructForm.setReadonly("SS_Edu", true);
//                                addSalStructForm.setReadonly("SS_Medic", true);
//                                addSalStructForm.setReadonly("SS_Misc", true);
            } else {

                addSalStructForm.setItemValue("SS_Basic", "");
                addSalStructForm.setItemValue("SS_DA", "");
                addSalStructForm.setItemValue("SS_HRA", "");
                addSalStructForm.setItemValue("SS_Convey", "");
                addSalStructForm.setItemValue("SS_Edu", "");
                addSalStructForm.setItemValue("SS_Medic", "");
                addSalStructForm.setItemValue("SS_Misc", "");
                addSalStructForm.setItemWidth("SS_Basic", "20");
                addSalStructForm.setItemWidth("SS_DA", "20");
                addSalStructForm.setItemWidth("SS_HRA", "20");
                addSalStructForm.setItemWidth("SS_Convey", "20");
                addSalStructForm.setItemWidth("SS_Edu", "20");
                addSalStructForm.setItemWidth("SS_Medic", "20");
                addSalStructForm.setItemWidth("SS_Misc", "20");
                addSalStructForm.setNote("SS_Basic", {text: "Basic Percentage", width: 90});
                addSalStructForm.setNote("SS_DA", {text: "DA Percentage", width: 90});
                addSalStructForm.setNote("SS_HRA", {text: "HRA Percentage", width: 90});
                addSalStructForm.setNote("SS_Convey", {text: "Conveyance Percentage", width: 150});
                addSalStructForm.setNote("SS_Edu", {text: "Education Percentage", width: 150});
                addSalStructForm.setNote("SS_Medic", {text: "Meidcal Percentage", width: 150});
                addSalStructForm.setNote("SS_Misc", {text: "Miscellanious Percentage", width: 150});
                addSalStructForm.setReadonly("SS_Basic", false);
                addSalStructForm.setReadonly("SS_DA", false);
                addSalStructForm.setReadonly("SS_HRA", false);
                addSalStructForm.setReadonly("SS_Convey", false);
                addSalStructForm.setReadonly("SS_Edu", false);
                addSalStructForm.setReadonly("SS_Medic", false);
                addSalStructForm.setReadonly("SS_Misc", false);
            }
        },
        menuNewState: function () {

            if (!dhxMiddleBlockTabs.cells("menuNewState")) {
                dhxMiddleBlockTabs.addTab("menuNewState", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;States", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewState");
                dhxMiddleBlockTabs.tabs("menuNewState").setActive();

                var menuStateLayout = dhxMiddleBlockTabs.cells("menuNewState").attachLayout('2U');
                menuStateLayout.cells("a").setText("New State");
                menuStateLayout.cells("b").setText("List State");
                menuStateLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for State --------------------//
                listStateGrid = menuStateLayout.cells("b").attachGrid();
                listStateGrid.init();
                listStateGrid.enableSmartRendering(true, 50);
                listStateGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listStateGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listStateGrid.loadXML(preTally.Initialize.encryptURL("requisites/listState.php"), function () {
                    listStateGrid.attachEvent("onRowSelect", preTally.Settings.editState);
                });
                //----------------- Attach Form for State --------------------//
                addStateForm = menuStateLayout.cells("a").attachForm();
                addStateForm.loadStruct(preTally.Initialize.encryptURL("requisites/newState.php"), function () {

                    addStateForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newStateValidate') {
                            var newState = addStateForm.validate();
                            if (newState) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addStateForm.send(preTally.Initialize.encryptURL('warehouse/newState.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addStateForm.resetValidateCss();
                                        addStateForm.clear();
                                        addStateForm.setItemValue("ST_Id", 0);
                                    } else {
                                        response = 'State Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listStateGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listState.php"), true, true, function () {
                                        //preTally.Settings.editState();
                                    });
                                });
                            }
                        } else {
                            addStateForm.resetValidateCss();
                            addStateForm.clear();
                            addStateForm.setItemValue("ST_Id", 0);
                        }
                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewState").setActive();
            }
        },
        editState: function (rowId) {
            var STId = rowId;
            //listStateGrid.attachEvent("onRowSelect", function(STId) {
            addStateForm.setItemValue("ST_Id", STId);
            addStateForm.setItemValue("ST_Name", listStateGrid.getUserData(STId, "ST_Name"));
            addStateForm.setItemValue("ST_Country", listStateGrid.getUserData(STId, "CN_Id"));
            addStateForm.setItemValue("ST_Status", listStateGrid.getUserData(STId, "ST_Status"));

            //});
        },
        menuNewCity: function () {

            if (!dhxMiddleBlockTabs.cells("menuNewCity")) {
                dhxMiddleBlockTabs.addTab("menuNewCity", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;City", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewCity");
                dhxMiddleBlockTabs.tabs("menuNewCity").setActive();

                var menuCityLayout = dhxMiddleBlockTabs.cells("menuNewCity").attachLayout('2U');
                menuCityLayout.cells("a").setText("New City");
                menuCityLayout.cells("b").setText("List City");
                menuCityLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for City --------------------//
                listCityGrid = menuCityLayout.cells("b").attachGrid();
//                listCityGrid.enableSmartRendering(true, 50);
                listCityGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listCityGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listCityGrid.setImagePath("../../codebase/imgs/");
                listCityGrid.setSkin("dhx_skyblue")
                listCityGrid.setHeader("SlNo,City,State,Country,Status");
                listCityGrid.attachHeader(",#text_filter,#select_filter_strict,#select_filter_strict,");
                listCityGrid.setInitWidths("40,*,*,*,60")
                listCityGrid.setColAlign("center,left,left,left,center")
                listCityGrid.setColTypes("ro,ro,ro,ro,ro");
                listCityGrid.setColSorting("int,str,str,str,na");

                listCityGrid.init();
                listCityGrid.enableSmartRendering(true, 150);
                listCityGrid.loadXML(preTally.Initialize.encryptURL("requisites/listCity.php"), function () {
                    listCityGrid.attachEvent("onRowSelect", preTally.Settings.editCity);
                });


                //----------------- Attach Form for City --------------------//
                addCityForm = menuCityLayout.cells("a").attachForm();
                addCityForm.loadStruct(preTally.Initialize.encryptURL("requisites/newCity.php&r=" + new Date().getTime()), function () {

                    LocConCombo = addCityForm.getCombo("CN_Id");
                    LocSteComboCty = addCityForm.getCombo("ST_Id");

                    LocConCombo.attachEvent("onClose", function () {
                        LocSteComboCty.clearAll();
                        var country_id = LocConCombo.getSelectedValue();
                        if ((country_id) && (country_id != 0)) {
                            var params = "+cid=" + country_id;
                            LocSteComboCty.load(preTally.Initialize.encryptURL("requisites/states.php&" + params), function () {
                            });
                        }
                    });

                    addCityForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newCityValidate') {
                            var newCity = addCityForm.validate();
                            if (newCity) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addCityForm.send(preTally.Initialize.encryptURL('warehouse/newCity.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);

                                    if (response != 'fail') {
                                        addCityForm.resetValidateCss();
                                        addCityForm.clear();
                                        addCityForm.setItemValue("ST_Id", 0);
                                        addCityForm.setItemValue("CN_Id", 0);
                                        LocSteComboCty.clearAll();
                                    } else {
                                        response = 'City Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listCityGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listCity.php"), true, true, function () {
                                        //preTally.Settings.editCity();
                                    });
                                });
                            }
                        } else {
                            addCityForm.resetValidateCss();
                            addCityForm.clear();
                            addCityForm.setItemValue("ST_Id", 0);
                        }
                    });


                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewCity").setActive();
            }
        },
        editCity: function (rowId) {
            var CTId = rowId;
            // LocSteComboCty.clearAll();
            var locCN_Id = listCityGrid.getUserData(CTId, "CN_Id");
            ;
            LocSTId = listCityGrid.getUserData(CTId, "ST_Id");
            LocSteComboCty.clearAll();
            var params = "+cid=" + locCN_Id;
            LocSteComboCty.load(preTally.Initialize.encryptURL("requisites/states.php&" + params), function () {
                addCityForm.setItemValue("ST_Id", LocSTId);
            });

            addCityForm.setItemValue("CT_Id", CTId);
            addCityForm.setItemValue("CN_Id", locCN_Id);
            addCityForm.setItemValue("CT_Name", listCityGrid.getUserData(CTId, "CT_Name"));
            addCityForm.setItemValue("CT_Status", listCityGrid.getUserData(CTId, "CT_Status"));

        },
        menuNewPlace: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewPlace")) {
                dhxMiddleBlockTabs.addTab("menuNewPlace", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Places", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewPlace");
                dhxMiddleBlockTabs.tabs("menuNewPlace").setActive();

                var menuPlaceLayout = dhxMiddleBlockTabs.cells("menuNewPlace").attachLayout('2U');
                menuPlaceLayout.cells("a").setText("New Place");
                menuPlaceLayout.cells("b").setText("List Place");
                menuPlaceLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Place --------------------//
                listPlaceGrid = menuPlaceLayout.cells("b").attachGrid();
                listPlaceGrid.enableSmartRendering(true);
                listPlaceGrid.init();
                listPlaceGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listPlaceGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listPlaceGrid.loadXML(preTally.Initialize.encryptURL("requisites/listPlace.php"), function () {
                    listPlaceGrid.attachEvent("onRowSelect", preTally.Settings.editPlace)
                });

                //----------------- Attach Form for Place --------------------//
                addPlaceForm = menuPlaceLayout.cells("a").attachForm();
                addPlaceForm.loadStruct(preTally.Initialize.encryptURL("requisites/newPlace.php"), function () {
                    LocCtyCombo = addPlaceForm.getCombo("CT_Id");


                    addPlaceForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newPlaceValidate') {
                            var newPlace = addPlaceForm.validate();
                            if (newPlace) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addPlaceForm.send(preTally.Initialize.encryptURL('warehouse/newPlace.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);

                                    if (response != 'fail') {
                                        addPlaceForm.resetValidateCss();
                                        addPlaceForm.clear();
                                        addPlaceForm.setItemValue("ST_Id", 0);
                                        addPlaceForm.setItemValue("CN_Id", 0);
                                        //LocSteComboCty.clearAll();
                                    } else {
                                        response = 'Place Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listPlaceGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listPlace.php"), true, true, function () {
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                    });

                                });
                            }
                        } else {
                            addPlaceForm.resetValidateCss();
                            addPlaceForm.clear();

                        }
                    });


                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewPlace").setActive();
            }

        },
        editPlace: function (rowId)
        {
            var APId = rowId
            LocCtyCombo = addPlaceForm.getCombo("CT_Id");
            //LocCtyCombo.clearAll();
            addPlaceForm.setItemValue("AP_Id", APId);
            addPlaceForm.setItemValue("CT_Id", listPlaceGrid.getUserData(APId, "CT_Id"));
            //LocCtyCombo.setComboText(listPlaceGrid.getUserData(APId, "CT_Name"));
            addPlaceForm.setItemValue("AP_Name", listPlaceGrid.getUserData(APId, "AP_Name"));
            addPlaceForm.setItemValue("AP_Status", listPlaceGrid.getUserData(APId, "AP_Status"));
        },
        validateCss: function (name, value, res) {
            dhxNewBalSheetForm.setValidateCss(name, res, 'validate_red');
            return false;
        },
        menuAccessLevelUser: function () {
            if (!dhxMiddleBlockTabs.cells("access_level")) {
                dhxMiddleBlockTabs.addTab("access_level", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Access Control Levels", 200);
                //dhxMiddleBlockTabs.setTabActive("access_level");
                dhxMiddleBlockTabs.tabs("access_level").setActive();


                var access_level = dhxMiddleBlockTabs.cells("access_level").attachLayout('2U');
                access_level.cells("a").setText("Access Control Levels");
                access_level.cells("b").setText("List Access Control Levels");
                access_level.cells("a").setWidth(635);
                //----------------- Attach Grid for ACL --------------------//
                listACLGrid = access_level.cells("b").attachGrid();

                listACLGrid.attachEvent("onXLS", function () {

                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listACLGrid.attachEvent("onXLE", function () {

                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listACLGrid.enableTooltips("false,false,false");

                listACLGrid.init();

                var ofid = unescape(JGG1P3bDnUSDL10Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                //console.log(ofid);
                if (ofid == 1) {
                    listACLGrid.attachHeader(',#text_filter_inc,#select_filter,,<select id="aclStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                    filterpos = 3;
                    //console.log(ofid+"  =1");
                } else {
                    listACLGrid.attachHeader(',#text_filter_inc,,<select id="aclStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                    filterpos = 2;
                    // console.log(ofid+"  =4");
                }

                listACLGrid.loadXML(preTally.Initialize.encryptURL("requisites/listACL.php"), function () {
                    listACLGrid.attachEvent("onRowSelect", preTally.Settings.editACL);
                    listACLGrid.attachEvent("onFilterEnd", function (elements) {
                        if (listACLGrid.getRowsNum() == 0) {
                            if (listACLGrid.doesRowExist("msgRow"))
                                listACLGrid.deleteRow("msgRow");
                            listACLGrid.addRow("msgRow", [, 'No Records Found'], 1);
                            listACLGrid.setRowTextStyle("msgRow", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                        } else {
                            listACLGrid.deleteRow("msgRow");
                        }
                    });
                    listACLGrid.makeFilter("aclStatus", filterpos);
                    listACLGrid.filterBy(filterpos, "Approved");
                    var filterObject = listACLGrid.getFilterElement(filterpos);
                    filterObject.value = 'Approved';
                    if (listACLGrid.getRowsNum() == 0) {
                        if (listACLGrid.doesRowExist("msgRow"))
                            listACLGrid.deleteRow("msgRow");
                        listACLGrid.addRow("msgRow", [, 'No Records Found'], 1);
                        listACLGrid.setRowTextStyle("msgRow", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    } else {
                        listACLGrid.deleteRow("msgRow");
                    }
                });

                //----------------- Attach Form for ACL --------------------//
                manageACLForm = access_level.cells("a").attachForm();

                manageACLForm.loadStruct(preTally.Initialize.encryptURL("requisites/manageACL.php"), function () {
                    manageACLForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'ACLButtonSave') {
                            var newItem = manageACLForm.validate();
                            if (newItem) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                manageACLForm.send(preTally.Initialize.encryptURL('warehouse/newACL.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    dhtmlx.message({text: response});
                                    manageACLForm.resetValidateCss();
                                    manageACLForm.clear();
                                    manageACLForm.setItemValue("ACL_Id", 0);
                                    listACLGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listACL.php"), true, true, function () {
                                        listACLGrid.filterBy(filterpos, "Approved");
                                        var filterObject = listACLGrid.getFilterElement(filterpos);
                                        filterObject.value = 'Approved';
                                        manageACLForm.resetValidateCss();
                                        manageACLForm.clear();
                                        manageACLForm.setItemValue("ACL_Id", 0);
                                    });
                                });
                            }
                        } else {
                            manageACLForm.resetValidateCss();
                            manageACLForm.clear();
                            manageACLForm.setItemValue("ACL_Id", 0);

                        }
                    });
                    manageACLForm.attachEvent('onChange', function (name, value, state) {
                        if (name == "ACL_MnthlyAttendance" && state == true) {
                            manageACLForm.setItemValue("ACL_AttendanceEdt", 0);
                        }
                        if (name == "ACL_AttendanceEdt" && state == true) {
                            manageACLForm.setItemValue("ACL_MnthlyAttendance", 0);
                        }
                        if (name == "ACL_Payroll" && state == true) {
                            manageACLForm.setItemValue("ACL_PayrollEdt", 0);
                        }
                        if (name == "ACL_PayrollEdt" && state == true) {
                            manageACLForm.setItemValue("ACL_Payroll", 0);
                        }
                        if (name == "ACL_SalPMwiseBranch" && state == true) {
                            manageACLForm.setItemValue("ACL_SalPMwiseAll", 0);
                        }
                        if (name == "ACL_SalPMwiseAll" && state == true) {
                            manageACLForm.setItemValue("ACL_SalPMwiseBranch", 0);
                        }

                    });
                });
            } else
            {
                dhxMiddleBlockTabs.tabs("access_level").setActive();
            }
        },
        editACL: function (rowId) {
            var id = rowId;
            var AclHrViewCombo = manageACLForm.getCombo("ACL_HR_VM");
            AclHrViewCombo.readonly("Enable");
            var AclBSheetViewCombo = manageACLForm.getCombo("ACL_BSheet_VM");
            AclBSheetViewCombo.readonly("Enable");
            var AclHrCombo = manageACLForm.getCombo("ACL_HR");
            AclHrCombo.readonly("Enable");
            var AclBSheetCombo = manageACLForm.getCombo("ACL_BSheet");
            AclBSheetCombo.readonly("Enable");
            manageACLForm.setItemValue("ACL_Id", id);
            manageACLForm.setItemValue("ACL_Name", listACLGrid.getUserData(id, "ACL_Name"));
            AclHrCombo.setComboValue(listACLGrid.getUserData(id, "ACL_HR"));
            AclBSheetCombo.setComboValue(listACLGrid.getUserData(id, "ACL_BSheet"));
            AclHrViewCombo.setComboValue(listACLGrid.getUserData(id, "ACL_HR_VM"));
            AclBSheetViewCombo.setComboValue(listACLGrid.getUserData(id, "ACL_BSheet_VM"));
//            manageACLForm.setItemValue("ACL_CashReports", listACLGrid.getUserData(id, "ACL_CashReports"));
            manageACLForm.setItemValue("ACL_MasterReports", listACLGrid.getUserData(id, "ACL_MasterReports"));
            manageACLForm.setItemValue("ACL_BankReports", listACLGrid.getUserData(id, "ACL_BankReports"));
            manageACLForm.setItemValue("ACL_MISReports", listACLGrid.getUserData(id, "ACL_MISReports"));
            manageACLForm.setItemValue("ACL_User", listACLGrid.getUserData(id, "ACL_User"));
            manageACLForm.setItemValue("ACL_ListUser", listACLGrid.getUserData(id, "ACL_ListUser"));
            manageACLForm.setItemValue("ACL_SalDetail", listACLGrid.getUserData(id, "ACL_SalDetail"));
            manageACLForm.setItemValue("ACL_BnkDetail", listACLGrid.getUserData(id, "ACL_BnkDetail"));
            manageACLForm.setItemValue("ACL_Payroll", listACLGrid.getUserData(id, "ACL_Payroll"));
            manageACLForm.setItemValue("ACL_PayrollEdt", listACLGrid.getUserData(id, "ACL_PayrollEdt"));
            manageACLForm.setItemValue("ACL_MealAllowance", listACLGrid.getUserData(id, "ACL_MealAllowance"));
            manageACLForm.setItemValue("ACL_Attendance", listACLGrid.getUserData(id, "ACL_Attendance"));
            manageACLForm.setItemValue("ACL_AttendanceEdt", listACLGrid.getUserData(id, "ACL_AttendanceEdt"));
            manageACLForm.setItemValue("ACL_MnthlyAttendance", listACLGrid.getUserData(id, "ACL_MnthlyAttendance"));
            manageACLForm.setItemValue("ACL_MnthlyAttendanceSummary", listACLGrid.getUserData(id, "ACL_MnthlyAttendanceSummary"));
            manageACLForm.setItemValue("ACL_ManageLateEntry", listACLGrid.getUserData(id, "ACL_ManageLateEntry"));
            manageACLForm.setItemValue("ACL_ApproveLeave", listACLGrid.getUserData(id, "ACL_ApproveLeave"));
            manageACLForm.setItemValue("ACL_State", listACLGrid.getUserData(id, "ACL_State"));
            manageACLForm.setItemValue("ACL_City", listACLGrid.getUserData(id, "ACL_City"));
            manageACLForm.setItemValue("ACL_Company", listACLGrid.getUserData(id, "ACL_Company"));
            manageACLForm.setItemValue("ACL_Branch", listACLGrid.getUserData(id, "ACL_Branch"));
            manageACLForm.setItemValue("ACL_Dept", listACLGrid.getUserData(id, "ACL_Dept"));
            manageACLForm.setItemValue("ACL_Desig", listACLGrid.getUserData(id, "ACL_Desig"));
            manageACLForm.setItemValue("ACL_MH", listACLGrid.getUserData(id, "ACL_MH"));
            manageACLForm.setItemValue("ACL_SH", listACLGrid.getUserData(id, "ACL_SH"));
            manageACLForm.setItemValue("ACL_Item", listACLGrid.getUserData(id, "ACL_Item"));
            manageACLForm.setItemValue("ACL_AllItem", listACLGrid.getUserData(id, "ACL_AllItem"));
            manageACLForm.setItemValue("ACL_NotifyQueue", listACLGrid.getUserData(id, "ACL_NotifyQueue"));
            manageACLForm.setItemValue("ACL_Desc", listACLGrid.getUserData(id, "ACL_Desc"));
            manageACLForm.setItemValue("ACL_Unit", listACLGrid.getUserData(id, "ACL_Unit"));
            manageACLForm.setItemValue("ACL_Paymode", listACLGrid.getUserData(id, "ACL_Paymode"));
            manageACLForm.setItemValue("ACL_SalStruct", listACLGrid.getUserData(id, "ACL_SalStruct"));
            manageACLForm.setItemValue("ACL_SalPayMode", listACLGrid.getUserData(id, "ACL_SalPayMode"));
            manageACLForm.setItemValue("ACL_Access", listACLGrid.getUserData(id, "ACL_Access"));
            manageACLForm.setItemValue("ACL_SidebarMH", listACLGrid.getUserData(id, "ACL_SidebarMH"));
            manageACLForm.setItemValue("ACL_SidebarOFF", listACLGrid.getUserData(id, "ACL_SidebarOFF"));
            manageACLForm.setItemValue("ACL_ManageTracks", listACLGrid.getUserData(id, "ACL_ManageTracks"));
            manageACLForm.setItemValue("ACL_DeleteEntries", listACLGrid.getUserData(id, "ACL_DeleteEntries"));
            manageACLForm.setItemValue("ACL_ManageBSDate", listACLGrid.getUserData(id, "ACL_ManageBSDate"));
            manageACLForm.setItemValue("ACL_Track", listACLGrid.getUserData(id, "ACL_Track"));
            manageACLForm.setItemValue("ACL_TrackInvReceipt", listACLGrid.getUserData(id, "ACL_TrackInvReceipt"));
            manageACLForm.setItemValue("ACL_NotfLC", listACLGrid.getUserData(id, "ACL_NotfLC"));
            manageACLForm.setItemValue("ACL_NotfBNK", listACLGrid.getUserData(id, "ACL_NotfBNK"));
            manageACLForm.setItemValue("ACL_Att_Master", listACLGrid.getUserData(id, "ACL_Att_Master"));
            manageACLForm.setItemValue("ACL_FeedbackRpt", listACLGrid.getUserData(id, "ACL_FeedbackRpt"));
            manageACLForm.setItemValue("ACL_SalPMwiseBranch", listACLGrid.getUserData(id, "ACL_SalPMwiseBranch"));
            manageACLForm.setItemValue("ACL_SalPMwiseAll", listACLGrid.getUserData(id, "ACL_SalPMwiseAll"));
            manageACLForm.setItemValue("ACL_SalAdvance", listACLGrid.getUserData(id, "ACL_SalAdvance"));
            manageACLForm.setItemValue("ACL_ZonalManage", listACLGrid.getUserData(id, "ACL_ZonalManage"));
            manageACLForm.setItemValue("ACL_ZonalHead", listACLGrid.getUserData(id, "ACL_ZonalHead"));
            manageACLForm.setItemValue("ACL_ManageBusinessAmt", listACLGrid.getUserData(id, "ACL_ManageBusinessAmt"));
            manageACLForm.setItemValue("ACL_Status", listACLGrid.getUserData(id, "ACL_Status"));
            manageACLForm.setItemValue("OF_Id", listACLGrid.getUserData(id, "OF_Id"));
            manageACLForm.setItemValue("ACL_Purchase_Team", listACLGrid.getUserData(id, "ACL_Purchase_Team"));
            manageACLForm.setItemValue("ACL_Purchase_Approval", listACLGrid.getUserData(id, "ACL_Purchase_Approval"));            
            manageACLForm.setItemValue("ACL_EditAccEntries", listACLGrid.getUserData(id, "ACL_EditAccEntries"));
        },
        menuCalculator: function () {

            if (!winCalculator) {
                var winData = new Array();
                winData = {
                    'initialize': dhxWins,
                    'title': 'Widget || Calculator',
                    'id': 'widgetCalculator',
                    //'iconEnable'  : 'home_20.png', 
                    //'iconDisable' : 'key_20.png', 
                    'X': 300,
                    'Y': 5,
                    'W': 449,
                    'H': 540
                };

                winCalculator = winData['initialize'].createWindow(winData['id'], winData['X'], winData['Y'], winData['W'], winData['H']);
                winCalculator.denyResize();
                //win.denyPark();
                winCalculator.setText(winData['title']);
                //winCalc.attachHTMLString('<embed src="widgets/calculator.swf" quality="high" width="265" height="342">');
                winCalculator.attachURL("calculator.php");

                winCalculator.attachEvent("onClose", function (win) { // To Close Calculator In Track
                    $(".dhxsidebar_side_items .dhxsidebar_item:nth-child(9)").removeClass("dhxsidebar_item_selected");
                    winCalculator = '';
                    return true;
                });
            }
        },
        calculatorModeManage: function (mode) {
            if (mode === 'basic') {
                winCalculator.setDimension(null, 651); // only height is set
            } else {
                //winCalculator.setDimension(null, 540); // only height is set
                setTimeout(function () {
                    winCalculator.setDimension(null, 540);
                }, 200);
            }

        },
        menuPayroll: function () {
            if (!dhxMiddleBlockTabs.cells("menuPayroll")) {
                dhxMiddleBlockTabs.addTab("menuPayroll", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Payroll", 130);
                //dhxMiddleBlockTabs.setTabActive("menuPayroll");
                dhxMiddleBlockTabs.tabs("menuPayroll").setActive();

                ptPayrollToolbar = dhxMiddleBlockTabs.cells("menuPayroll").attachToolbar();
                ptPayrollToolbar.loadStruct(preTally.Initialize.encryptURL("requisites/attendanceToolbar.php&" + new Date().getTime()), function () {

                    ptPayrollToolbarCalendarPop = new dhtmlXPopup({
                        toolbar: ptPayrollToolbar,
                        id: "attendanceCalendar"
                    });

                    var atnCalendar = ptPayrollToolbarCalendarPop.attachCalendar();
                    atnCalendar.hideTime();
                    atnCalendar.attachEvent("onClick", function () {
                        calendarPop.hide();
                    });
                });
                ptPayrollToolbar.setAlign('right');



                ptPayrollAttendence = dhxMiddleBlockTabs.cells("menuPayroll").attachGrid();
                //ptPayrollAttendence.attachHeader(["#rspan", "#text_filter", "Title", "Author", "#rspan", "#rspan", "#rspan", "Bestseller", "Published"]);
                ptPayrollAttendence.init();
                //ptPayrollAttendence.splitAt(2);
                ptPayrollAttendence.loadXML(preTally.Initialize.encryptURL("requisites/attendence.php"), function () {
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuPayroll").setActive();
            }
        },
        menuCalendar: function () {
            /*if (dhxToolbarCalendarPop.isVisible()) {
             dhxToolbarCalendarPop.hide();
             } else {
             dhxToolbarCalendarPop.show(0, 0, 900, 30);
             menuCalendar = dhxToolbarCalendarPop.attachCalendar();
             menuCalendar.hideTime();
             menuCalendar.attachEvent("onClick", function() {
             dhxToolbarCalendarPop.hide();
             });
             }*/
            if (!dhxMiddleBlockTabs.cells("menuCalendar")) {
                dhxMiddleBlockTabs.addTab("menuCalendar", "<img src='images/icon/company_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Calendar", 150);
                //dhxMiddleBlockTabs.setTabActive("menuNewOffice");
                dhxMiddleBlockTabs.tabs("menuCalendar").attachURL("scheduler.php")
                dhxMiddleBlockTabs.tabs("menuCalendar").setActive();


                //var schedulerTab = dhxMiddleBlockTabs.cells("menuCalendar").attachScheduler(new Date(2014,05,30), "year");
                //$(".dhx_cal_navline").append('<div class="dhx_cal_tab" name="year_tab" style="right:280px;">Year</div>');
                //schedulerTab.config.multi_day = true;
                //schedulerTab.config.xml_date="%Y-%m-%d %H:%i";
                //schedulerTab.config.year_x = 2;
                //dhx_cal_navline
                //schedulerTab.locale.labels.year_tab ="Year";


                //dhxLayout.cells("a").attachScheduler(new Date(2009,05,30), "week");
                //scheduler.load("./data/events.xml")

            } else {
                dhxMiddleBlockTabs.tabs("menuCalendar").setActive();
            }
        },
        menuNewBank: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewBank"))
            {
                dhxMiddleBlockTabs.addTab("menuNewBank", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Banks", 130);

                dhxMiddleBlockTabs.tabs("menuNewBank").setActive();
                var menuBankLayout = dhxMiddleBlockTabs.cells("menuNewBank").attachLayout('2U');
                menuBankLayout.cells("a").setText("New Bank");
                menuBankLayout.cells("b").setText("List Bank");
                menuBankLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Bank--------------------//
                listBankGrid = menuBankLayout.cells("b").attachGrid();
                listBankGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listBankGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listBankGrid.init();
                listBankGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBank.php"), function () {
                    listBankGrid.attachEvent("onRowSelect", preTally.Settings.editBank);
                });
                //----------------- Attach Grid for Bank--------------------//
                addBankForm = menuBankLayout.cells("a").attachForm();
                addBankForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBank.php&r=" + new Date().getTime()), function () {
                    addBankForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newBankValidate') {
                            var newBank = addBankForm.validate();
                            var values = addBankForm.getFormData();
                            var Cvalidate = preTally.Validate.Validate(values, 'BNK_Name', addBankForm, 'title');
                            if (newBank && Cvalidate) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addBankForm.send(preTally.Initialize.encryptURL('warehouse/newBank.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);

                                    if (response != 'fail') {
                                        addBankForm.resetValidateCss();
                                        addBankForm.clear();
                                        addBankForm.setItemValue("BNK_Id", 0);
                                    } else {
                                        response = 'Bank Name Exists. Please Re-Try';
                                        addBankForm.clear();
                                        addBankForm.setItemValue("BNK_Id", 0);
                                    }
                                    dhtmlx.message({text: response});
                                    listBankGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBank.php"), true, true, function () {
                                        //preTally.Settings.editBank();
                                    });
                                });
                            }
                        } else {
                            addBankForm.resetValidateCss();
                            addBankForm.clear();
                            addBankForm.setItemValue("BNK_Id", 0);
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewBank").setActive();
            }
        },
        editBank: function (rowId) {
            var BankId = rowId;
            addBankForm.setItemValue("BNK_Id", BankId);
            addBankForm.setItemValue("BNK_Name", listBankGrid.getUserData(BankId, "BNK_Name"));
            var BankCombo = addBankForm.getCombo("BNK_Status");
            BankCombo.readonly("Enable");
            BankCombo.setComboValue(listBankGrid.getUserData(BankId, "BNK_Status"));
        },
        menuNewBnkBranch: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewBnkBranch")) {
                dhxMiddleBlockTabs.addTab("menuNewBnkBranch", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Bank Branches", 130);
                dhxMiddleBlockTabs.tabs("menuNewBnkBranch").setActive();
                var menuBnkBranchLayout = dhxMiddleBlockTabs.cells("menuNewBnkBranch").attachLayout('2U');
                menuBnkBranchLayout.cells("a").setText("New Bank Branch");
                menuBnkBranchLayout.cells("b").setText("List Bank Branch");
                menuBnkBranchLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for BnkBranch--------------------//
                listBnkBranchGrid = menuBnkBranchLayout.cells("b").attachGrid();
                listBnkBranchGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listBnkBranchGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listBnkBranchGrid.attachHeader('#rspan,#text_filter_inc,#text_filter_inc,#rspan,<select id="BBStatus" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>');
                listBnkBranchGrid.enableTooltips("false,false,false,false");
                listBnkBranchGrid.enableColSpan(true);
                listBnkBranchGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listBnkBranchGrid.getRowsNum() == 0) {
                        if (listBnkBranchGrid.doesRowExist("msgRow"))
                            listBnkBranchGrid.deleteRow("msgRow");
                        listBnkBranchGrid.addRow('msgRow', "No records found");
                        listBnkBranchGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listBnkBranchGrid.setColspan("msgRow", 0, 5);
                    }
                });
                listBnkBranchGrid.init();
                listBnkBranchGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBnkBranch.php"), function () {
                    listBnkBranchGrid.makeFilter("BBStatus", 3);
                    listBnkBranchGrid.attachEvent("onRowSelect", preTally.Settings.editBnkBranch);
                });
                //----------------- Attach Grid for BnkBranch--------------------//
                addBnkBranchForm = menuBnkBranchLayout.cells("a").attachForm();
                addBnkBranchForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBnkBranch.php&r=" + new Date().getTime()), function () {
                    addBnkBranchForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newBranchValidate') {
                            var newBnkBranch = addBnkBranchForm.validate();
                            var values = addBnkBranchForm.getFormData();
                            var Cvalidate = preTally.Validate.Validate(values, 'BB_Name', addBnkBranchForm, 'title');
                            if (newBnkBranch && Cvalidate) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addBnkBranchForm.send(preTally.Initialize.encryptURL('warehouse/newBankBranch.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if (response != 'fail') {
                                        addBnkBranchForm.resetValidateCss();
                                        addBnkBranchForm.clear();
                                        addBnkBranchForm.setItemValue("BB_Id", 0);
                                    } else {
                                        response = 'Bank Branch Name Exists. Please Re-Try';
                                    }
                                    dhtmlx.message({text: response});
                                    listBnkBranchGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBnkBranch.php"), true, true, function () {
                                        //preTally.Settings.editBnkBranch();
                                    });
                                });
                            }
                        } else {
                            addBnkBranchForm.resetValidateCss();
                            addBnkBranchForm.clear();
                            addBnkBranchForm.setItemValue("BB_Id", 0);
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewBnkBranch").setActive();
            }
        },
        editBnkBranch: function (rowId) {
            var BnkBranchId = rowId;
            addBnkBranchForm.setItemValue("BB_Id", BnkBranchId);
            addBnkBranchForm.setItemValue("BB_Name", listBnkBranchGrid.getUserData(BnkBranchId, "BB_Name"));
            addBnkBranchForm.setItemValue("BB_Address", listBnkBranchGrid.getUserData(BnkBranchId, "BB_Address"));
            addBnkBranchForm.setItemValue("BB_Comments", listBnkBranchGrid.getUserData(BnkBranchId, "BB_Comments"));
            var BnkBranchCombo = addBnkBranchForm.getCombo("BNK_Id");
            BnkBranchCombo.setComboValue(listBnkBranchGrid.getUserData(BnkBranchId, "BNK_Id"));
            //BnkBranchCombo.setComboText(listBnkBranchGrid.getUserData(BnkBranchId, "BNK_Name"));
            var BnkBranchStCombo = addBnkBranchForm.getCombo("BB_Status");
            BnkBranchStCombo.setComboValue(listBnkBranchGrid.getUserData(BnkBranchId, "BB_Status"));
        },
        menuNewBnkAccount: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewBnkAccount")) {
                dhxMiddleBlockTabs.addTab("menuNewBnkAccount", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Bank Accounts", 150);
                dhxMiddleBlockTabs.tabs("menuNewBnkAccount").setActive();
                var menuBnkAccountLayout = dhxMiddleBlockTabs.cells("menuNewBnkAccount").attachLayout('2U');
                menuBnkAccountLayout.cells("a").setText("New Bank Account");
                menuBnkAccountLayout.cells("b").setText("List Bank Account");
                menuBnkAccountLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Bank Account--------------------//
                listBnkAccountGrid = menuBnkAccountLayout.cells("b").attachGrid();
                listBnkAccountGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listBnkAccountGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listBnkAccountGrid.setHeader("SlNo,Account Number,Display Name,Bank Name,BkStatus,Status,BnkStatus");
                listBnkAccountGrid.setInitWidths("40,*,*,*,0,60,0");
                listBnkAccountGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                listBnkAccountGrid.attachHeader("#rspan,#text_filter_inc,#text_filter_inc,#select_filter,#rspan,<select id='BkStatus' style='width:90%; font-size:8pt; font-family:Tahoma;'></select>");
                listBnkAccountGrid.enableTooltips("false,false,false,false,false");
                listBnkAccountGrid.enableColSpan(true);
                listBnkAccountGrid.init();
                listBnkAccountGrid.loadXML(preTally.Initialize.encryptURL("requisites/listBnkAccount.php"), function () {
                    listBnkAccountGrid.makeFilter("BkStatus", 4);
                    listBnkAccountGrid.attachEvent("onRowSelect", preTally.Settings.editBnkAccount);
                });

                listBnkAccountGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listBnkAccountGrid.getRowsNum() == 0) {
                        if (listBnkAccountGrid.doesRowExist("msgRow"))
                            listBnkAccountGrid.deleteRow("msgRow");
                        listBnkAccountGrid.addRow('msgRow', "No records found");
                        listBnkAccountGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listBnkAccountGrid.setColspan("msgRow", 0, 5);
                    }
                });

                //----------------- Attach Grid for BnkAccount--------------------//
                addBnkAccountForm = menuBnkAccountLayout.cells("a").attachForm();
                addBnkAccountForm.loadStruct(preTally.Initialize.encryptURL("requisites/newBnkAccount.php&r=" + new Date().getTime()), function () {

                    BABnkBrhCombo = addBnkAccountForm.getCombo("BB_Id");
                    BnkAccountStCombo = addBnkAccountForm.getCombo("BA_Status");
                    LocCombo = addBnkAccountForm.getCombo("LC_Id");
                    addBnkAccountForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newBnkAccValidate') {
                            var newBnkAccount = addBnkAccountForm.validate();
                            if (newBnkAccount) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                addBnkAccountForm.send(preTally.Initialize.encryptURL('warehouse/newBnkAccount.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    if ((response == 'new') || (response == 'update')) {
                                        addBnkAccountForm.resetValidateCss();
                                        addBnkAccountForm.clear();
                                        BABnkBrhCombo.setComboText('');
                                        BABnkBrhCombo.setComboValue('');
                                        LocCombo.setComboText('');
                                        LocCombo.setComboValue('');
                                        listBnkAccountGrid.refreshFilters();
                                        $('#bcnkAcnt').val(0);
                                        listBnkAccountGrid.getFilterElement(1).value = "";
                                        listBnkAccountGrid.getFilterElement(2).value = "";
                                        if (response == 'new') {
                                            response = "Bank Account Added";
                                        } else if (response == 'update') {
                                            response = "Bank Account Updated";
                                        }
                                        listBnkAccountGrid.refreshFilters();
                                        addBnkAccountForm.setItemValue("BA_Id", 0);
                                        listBnkAccountGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listBnkAccount.php"), true, true, function () {

                                        });
                                    }
                                    dhtmlx.message({text: response});
                                });
                            }
                        } else {
                            addBnkAccountForm.resetValidateCss();
                            addBnkAccountForm.clear();
                            BABnkBrhCombo.setComboText('');
                            BABnkBrhCombo.setComboValue('');
                            LocCombo.setComboText('');
                            LocCombo.setComboValue('');
                            addBnkAccountForm.setItemValue("BA_Id", 0);

                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuNewBnkAccount").setActive();
            }
        },
        editBnkAccount: function (rowId) {
            var BnkAccountId = rowId;
            addBnkAccountForm.setItemValue("BA_Id", BnkAccountId);
            addBnkAccountForm.setItemValue("BA_No", listBnkAccountGrid.getUserData(BnkAccountId, "BA_No"));
            addBnkAccountForm.setItemValue("BA_DispName", listBnkAccountGrid.getUserData(BnkAccountId, "BA_DispName"));
            if (LocCombo.getOption(listBnkAccountGrid.getUserData(BnkAccountId, "LC_Id"))) {
                LocCombo.setComboValue(listBnkAccountGrid.getUserData(BnkAccountId, "LC_Id"));
            } else
                LocCombo.setComboValue("");
            BnkAccountStCombo.setComboValue(listBnkAccountGrid.getUserData(BnkAccountId, "BA_Status"));
            BABnkBrhCombo.setComboValue(listBnkAccountGrid.getUserData(BnkAccountId, "BB_Id"));
        },
        menuNewCheque: function () {
            if (!dhxMiddleBlockTabs.cells("menuNewCheque")) {
                dhxMiddleBlockTabs.addTab("menuNewCheque", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Cheque Book", 150);
                dhxMiddleBlockTabs.tabs("menuNewCheque").setActive();
                var menuChequeLayout = dhxMiddleBlockTabs.cells("menuNewCheque").attachLayout('2U');
                menuChequeLayout.cells("a").setText("New Cheque Book");
                menuChequeLayout.cells("b").setText("List Cheque Book");
                menuChequeLayout.cells("a").setWidth(400);
                //----------------- Attach Grid for Bank Account--------------------//
                listChequeGrid = menuChequeLayout.cells("b").attachGrid();
                listChequeGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listChequeGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listChequeGrid.setHeader("SlNo,Cheque Book Number,Bank Account,Branch Name,,Status");
                listChequeGrid.attachHeader(",#text_filter_inc,#text_filter_inc,#text_filter_inc,,<select id='chq_filter'></select>");
                listChequeGrid.setInitWidths("50,150,150,*,0,100");
                listChequeGrid.setColTypes("ro,ro,ro,ro,ro,ro");
                listChequeGrid.setColAlign("center,left,left,left,left,center");
                listChequeGrid.enableColSpan(true);
                listChequeGrid.init();
                listChequeGrid.enableTooltips("false,false,false,false,false");
                listChequeGrid.loadXML(preTally.Initialize.encryptURL("requisites/listCheque.php"), function () {
                    listChequeGrid.makeFilter("chq_filter", 4);
                    listChequeGrid.attachEvent("onRowSelect", preTally.Settings.editCheque);
                });
                listChequeGrid.attachEvent("onFilterEnd", function (elements) {
                    if (listChequeGrid.getRowsNum() == 0) {
                        if (listChequeGrid.doesRowExist("msgRow"))
                            listChequeGrid.deleteRow("msgRow");
                        listChequeGrid.addRow('msgRow', "No records found");
                        listChequeGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listChequeGrid.setColspan("msgRow", 0, 6);
                    }
                });
                //----------------- Attach Grid for Cheque--------------------//
                addChequeForm = menuChequeLayout.cells("a").attachForm();
                addChequeForm.loadStruct(preTally.Initialize.encryptURL("requisites/newCheque.php&r=" + new Date().getTime()), function () {
                    BnkAccCombo = addChequeForm.getCombo("BA_Id");
                    addChequeForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'newChequeValidate') {

                            var NewCheque = addChequeForm.validate();

                            if (NewCheque) {

                                chqfirst = parseInt(addChequeForm.getItemValue("CHQ_Firstleaf"));
                                chqlast = parseInt(addChequeForm.getItemValue("CHQ_Lastleaf"));


                                if (chqfirst < chqlast) {
                                    preTally.Settings.progressOn(true, dhxLayout, null);
                                    addChequeForm.send(preTally.Initialize.encryptURL('warehouse/newCheque.php'), function (loader, response) {
                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                        if (response != 'fail') {
                                            addChequeForm.resetValidateCss();
                                            addChequeForm.clear();
//                                        BnkBrhCombo.clearAll();
//                                        BnkAccCombo.clearAll();
//                                        BnkCombo.setComboText('');
//                                        BnkBrhCombo.setComboText('');
//                                        BnkAccCombo.setComboText('');
                                            addChequeForm.setItemValue("CHQ_Id", 0);
//                                            addChequeForm.setReadonly("CHQ_Firstleaf",false);            
//                                            addChequeForm.setReadonly("CHQ_Lastleaf",false);
                                            addChequeForm.enableItem("CHQ_Firstleaf");
                                            addChequeForm.enableItem("CHQ_Lastleaf");
                                        } else {
                                            response = 'Cheque Book Exists. Please Re-Try';
                                        }
                                        dhtmlx.message({text: response});
                                        listChequeGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listCheque.php"), true, true, function () {
                                            //preTally.Settings.editCheque();
                                        });
                                    });
                                } else {
                                    dhtmlx.message({text: "First Cheque number greater than last"});
                                }
                            }
                        } else {
                            addChequeForm.resetValidateCss();
                            addChequeForm.clear();
                            addChequeForm.setItemValue("CHQ_Id", 0);
//                            addChequeForm.setReadonly("CHQ_Firstleaf",false);            
//                            addChequeForm.setReadonly("CHQ_Lastleaf",false);
                            addChequeForm.enableItem("CHQ_Firstleaf");
                            addChequeForm.enableItem("CHQ_Lastleaf");
                        }

                    });

                });

            } else {
                dhxMiddleBlockTabs.tabs("menuNewCheque").setActive();
            }
        },
        editCheque: function (rowId) {
            var ChequeId = rowId;
            addChequeForm.setItemValue("CHQ_Id", ChequeId);
            addChequeForm.setItemValue("CHQ_BookNo", listChequeGrid.getUserData(ChequeId, "CHQ_BookNo"));
            addChequeForm.setItemValue("CHQ_Firstleaf", listChequeGrid.getUserData(ChequeId, "CHQ_Firstleaf"));

            addChequeForm.setItemValue("CHQ_Lastleaf", listChequeGrid.getUserData(ChequeId, "CHQ_Lastleaf"));
            addChequeForm.disableItem("CHQ_Firstleaf");
            addChequeForm.disableItem("CHQ_Lastleaf");
//            addChequeForm.setReadonly("CHQ_Firstleaf",true);
//            addChequeForm.setReadonly("CHQ_Lastleaf",true);
            //addChequeForm.setItemValue("CHQ_Nextleaf", listChequeGrid.getUserData(ChequeId, "CHQ_Nextleaf"));
            addChequeForm.setItemValue("CHQ_Status", listChequeGrid.getUserData(ChequeId, "CHQ_Status"));
            var BankAccCombo = addChequeForm.getCombo("BA_Id");
            BankAccCombo.setComboValue(listChequeGrid.getUserData(ChequeId, "BA_Id"));
            var ChequeStCombo = addChequeForm.getCombo("CHQ_Status");
            ChequeStCombo.readonly("Enable");
            ChequeStCombo.setComboValue(listChequeGrid.getUserData(ChequeId, "BA_Status"));
        },
        manageChqLeaves: function () {
            if (!dhxMiddleBlockTabs.cells("manageChequeLeaves")) {
                dhxMiddleBlockTabs.addTab("manageChequeLeaves", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Cheque Leaves", 210);
                dhxMiddleBlockTabs.tabs("manageChequeLeaves").setActive();
                var menuChequeLayout = dhxMiddleBlockTabs.cells("manageChequeLeaves").attachLayout('2U');
                menuChequeLayout.cells("a").setText("Cheque Books");
                menuChequeLayout.cells("b").setText("Cheque Leaves");
                menuChequeLayout.cells("a").setWidth(520);

                listChqBooks = menuChequeLayout.cells("a").attachGrid();
                listChqBooks.setImagePath("assets/grid/codebase/imgs/");
                listChqBooks.setSkin("dhx_skyblue");
                listChqBooks.setHeader("SlNo,Cheque Book Number,First Leaf,Bank Account,Branch Name,, Status");
                listChqBooks.attachHeader(",#text_filter_inc,#text_filter_inc,#text_filter_inc,#text_filter_inc,,<select id='chq_book_filter'></select>");
                listChqBooks.enableSmartRendering(true);
                listChqBooks.enableEditEvents(true, false, true);
                listChqBooks.setColTypes("ro,ro,ro,ro,ro,ro,ro");
                listChqBooks.setInitWidths("40,*,110,110,100,0,60");
                listChqBooks.setColAlign("center,left,left,left,left,left,center");
                listChqBooks.enableColSpan(true);
                listChqBooks.init();

                listChqLeaves = menuChequeLayout.cells("b").attachGrid();
                listChqLeaves.attachHeader(",#text_filter_inc,#text_filter_inc,,#text_filter_inc,#text_filter_inc,<select id='CkStatus'></select>,");
                listChqLeaves.setImagePath("assets/grid/codebase/imgs/");
                listChqLeaves.setSkin("dhx_skyblue");
                listChqLeaves.enableEditEvents(true, false, true);
                listChqLeaves.enableColSpan(true);
                listChqLeaves.init();
                listChqLeaves.attachEvent("onEditCell", function (stage, rId, cId, nValue, oValue) {
                    //console.log(stage+"-"+rId+"-"+cId);

                    if (cId == 6) {
                        if (stage == 0) {
                            ChqStatusCombo = listChqLeaves.cells(rId, cId).getCellCombo();
                            ChqStatusCombo.clearAll();
                            //ChqStatusCombo.addOption([["1","New"],["2","Approved"],["3","Rejected"],["4","Cancelled"]]);
                            ChqStatusCombo.addOption([["1", "New"], ["2", "Submitted"], ["3", "Approved"], ["4", "Rejected"], ["5", "Cancelled"]]);
                        }

                        if (stage == 2) {
                            if (nValue != listChqLeaves.getUserData(rId, "CL_Status")) {
                                dhtmlx.confirm({
                                    ok: "Yes", cancel: "No",
                                    title: "Warning Cheque Status",
                                    text: "Do you want to Change Cheque Status to &nbsp; '" + listChqLeaves.cells(rId, cId).getText() + "'",
                                    callback: function (status) {
                                        if (status == true) {
                                            var params = "&lv_id=" + rId + "&stats=" + nValue;
                                            $.post(preTally.Initialize.encryptURL("warehouse/changeChqStatus.php" + params), function (data) {
                                                listChqLeaves.updateFromXML(preTally.Initialize.encryptURL("requisites/listChqLeaves.php&book_id=" + listChqBooks.getSelectedRowId()), function () {});

                                                dhtmlx.message({text: data});
                                            });
                                        } else if (status == false)
                                        {
                                            listChqLeaves.cells(rId, cId).setValue(listChqLeaves.getUserData(rId, "CL_Status"));
                                        }
                                    }
                                });
                            }
                        }
                    }
                    return true;
                });

                listChqBooks.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });

                listChqBooks.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listChqBooks.loadXML(preTally.Initialize.encryptURL("requisites/listChequeDetails.php"), function () {
//                    listChqBooks.attachEvent("onRowSelect",preTally.Settings.listChqLeaves);
                    listChqBooks.makeFilter('chq_book_filter', 5);
                });
                var listChqLfFlag = 0
                listChqBooks.attachEvent("onRowSelect", function (id) {
                    preTally.Settings.listChqLeaves(id, listChqLfFlag);
                    if (listChqLfFlag == 0)
                        listChqLfFlag = 1;

                });
                listChqBooks.attachEvent("onFilterEnd", function (elements) {
                    if (listChqBooks.getRowsNum() == 0) {
                        if (listChqBooks.doesRowExist("msgRow"))
                            listChqBooks.deleteRow("msgRow");
                        listChqBooks.addRow('msgRow', "No records found");
                        listChqBooks.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listChqBooks.setColspan("msgRow", 0, 5);
                    }
                });

                listChqLeaves.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listChqLeaves.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listChqLeaves.attachEvent("onRowSelect", function (rId, cId) {
                    if (cId != 0 && cId != 3 && cId != 6) {
                        $(".ChqPopUp_" + rId).click();
                    }
                });

                listChqLeaves.attachEvent("onFilterEnd", function (elements) {
                    if (listChqLeaves.getRowsNum() == 0) {
                        if (listChqLeaves.doesRowExist("msgChRow"))
                            listChqLeaves.deleteRow("msgChRow");
                        listChqLeaves.addRow('msgChRow', "No records found");
                        listChqLeaves.setRowTextStyle('msgChRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                        listChqLeaves.setColspan("msgChRow", 0, 6);
                    }
                });
            } else {
                dhxMiddleBlockTabs.tabs("manageChequeLeaves").setActive();
            }
        },
        chqDetailsPopUp: function (inp, rId) {
            if (listChqLeaves.cells(rId, 6).getValue() != 'New' && listChqLeaves.cells(rId, 2).getValue() != '') {
                preTally.Settings.progressOn(true, dhxLayout, null);
                if (!Cheq_DetailPop) {
                    Cheq_DetailPop = new dhtmlXPopup({mode: "left"});
                }
                if (Cheq_DetailPop.isVisible()) {
                    Cheq_DetailPop.hide();
                }
                var x = getAbsoluteLeft(inp);
                var y = getAbsoluteTop(inp);
                var w = inp.offsetWidth;
                var h = inp.offsetHeight;
                var chqDetailsPop = Cheq_DetailPop.attachForm();
                var params = "CL_Id=" + rId;
                chqDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/chequeDetailsPop.php&" + params), function () {
                    Cheq_DetailPop.show(x, y, w, h);
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    var column0 = chqDetailsPop.getColumnNode("fieldsetname", 0);
                    column0.style.borderRight = "1px solid #a4bed4";
                });
                $('.ChqPopUp_' + rId).unbind();
            }
        },
        listChqLeaves: function (id, listChqLfFlag) {

            listChqLeaves.clearAndLoad(preTally.Initialize.encryptURL("requisites/listChqLeaves.php&book_id=" + id + "&flag=" + listChqLfFlag), function () {
                listChqLeaves.getFilterElement(1).value = "";
                listChqLeaves.getFilterElement(2).value = "";
                listChqLeaves.getFilterElement(4).value = "";
                listChqLeaves.getFilterElement(5).value = "";
                listChqLeaves.refreshFilters();
                listChqLeaves.makeFilter('CkStatus', 7);
            });
        },
        hideChqDetailData: function () {
            if (Cheq_DetailPop.isVisible()) {
                Cheq_DetailPop.hide();
            }
        },
        searchAccount: function () {
            if (!dhxMiddleBlockTabs.cells("searchAccount")) {
                dhxMiddleBlockTabs.addTab("searchAccount", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Search", 130);
                dhxMiddleBlockTabs.tabs("searchAccount").setActive();
                var menuSearchLayout = dhxMiddleBlockTabs.cells("searchAccount").attachLayout('2U');
                menuSearchLayout.cells("a").setText("Search");
                menuSearchLayout.cells("b").setText("Results");
                menuSearchLayout.cells("a").hideHeader();
                menuSearchLayout.cells("b").hideHeader();
                menuSearchLayout.cells("a").setWidth(300);
                menuSearchLayout.cells("a").fixSize(true, false);
                //----------------- Attach Grid for Search--------------------//
                listSearchGrid = menuSearchLayout.cells("b").attachGrid();
                listSearchGrid.init();
                listSearchGrid.loadXML(preTally.Initialize.encryptURL("requisites/searchBSItems.php"));
                //----------------- Attach From for Search--------------------//
                addSearchForm = menuSearchLayout.cells("a").attachForm();
                addSearchForm.loadStruct(preTally.Initialize.encryptURL("requisites/searchForm.php"), function () {
                    //-----------------Combo Boxes--------------------------//
                    officeCombo = addSearchForm.getCombo("OF_Id");
                    locateCombo = addSearchForm.getCombo("LC_Id");
                    DPCombo = addSearchForm.getCombo("DP_Id");
                    UsrCombo = addSearchForm.getCombo("US_Id");
                    BnkCombo = addSearchForm.getCombo("BNK_Id");
                    BnkBrhCombo = addSearchForm.getCombo("BB_Id");
                    BnkAccCombo = addSearchForm.getCombo("BA_Id");
                    //DGCombo         = addSearchForm.getCombo("DG_Id");

                    var ptRpTb_Inp_Frm = addSearchForm.getInput("Srch_FrmDate");
                    var ptRpTb_Inp_Til = addSearchForm.getInput("Srch_ToDate");
                    ptRpTb_Calendar = new dhtmlXCalendarObject([ptRpTb_Inp_Frm, ptRpTb_Inp_Til]);
                    ptRpTb_Calendar.setDateFormat("%d.%m.%Y");
                    ptRpTb_Inp_Frm.setAttribute("readOnly", "true");
                    ptRpTb_Inp_Frm.onclick = function () {
                        if (addSearchForm.getItemValue("Srch_ToDate"))
                            preTally.Settings.setSens(ptRpTb_Inp_Til, "max");
                    }

                    ptRpTb_Inp_Til.setAttribute("readOnly", "true");
                    ptRpTb_Inp_Til.onclick = function () {

                        if (addSearchForm.getItemValue("Srch_FrmDate"))
                            preTally.Settings.setSens(ptRpTb_Inp_Frm, "min");
                    }

                    officeCombo.attachEvent("onClose", function () {
                        locateCombo.clearAll();
                        DPCombo.clearAll();
                        var offz_id = officeCombo.getSelectedValue();
                        var params = "ofid=" + offz_id;
                        locateCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&" + params), function () {
                        });
                        DPCombo.load(preTally.Initialize.encryptURL("requisites/departments.php&" + params), function () {
                        });
                        UsrCombo.enableFilteringMode(true, preTally.Initialize.encryptURL("requisites/persons.php"));
                    });
                    BnkCombo.attachEvent("onClose", function () {
                        BnkBrhCombo.clearAll();
                        BnkBrhCombo.setComboText('');
                        BnkBrhCombo.setComboValue('');
                        BnkAccCombo.clearAll();
                        BnkAccCombo.setComboText('');
                        BnkAccCombo.setComboValue('');
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
                        BnkAccCombo.setComboValue('');
                        var bnk_brnch_id = BnkBrhCombo.getSelectedValue();
                        if ((bnk_brnch_id) && (bnk_brnch_id != 0)) {
                            var params = "BB_Id=" + bnk_brnch_id;
                            BnkAccCombo.load(preTally.Initialize.encryptURL("requisites/getBankAccounts.php&" + params), function () {
                            });
                        }

                    });
                });

                addSearchForm.attachEvent("onButtonClick", function (name) {
                    if (name == 'Btn_Filter') {
                        Frmcalendar = addSearchForm.getCalendar("Srch_FrmDate");
                        Tocalendar = addSearchForm.getCalendar("Srch_ToDate");
                        var frm_date = "&Srch_FrmDate=" + Frmcalendar.getFormatedDate("%Y-%m-%d");
                        var to_date = "&Srch_ToDate=" + Tocalendar.getFormatedDate("%Y-%m-%d");
                        var item_name = "&Srch_Item=" + addSearchForm.getItemValue("Srch_Item");
                        var paid_to = "&Srch_Paidto=" + addSearchForm.getItemValue("Srch_Paidto");
                        var paid_from = "&Srch_Paidfrm=" + addSearchForm.getItemValue("Srch_Paidfrm");
                        var cmpny_name = "&Srch_Cmpny=" + addSearchForm.getItemValue("OF_Id");
                        var brnch_name = "&Srch_Brnch=" + addSearchForm.getItemValue("LC_Id");
                        var dept_name = "&Srch_Dept=" + addSearchForm.getItemValue("DP_Id");
                        var usr_name = "&Srch_UserName=" + addSearchForm.getItemValue("US_Id");
                        var bnk_name = "&Srch_BnkName=" + addSearchForm.getItemValue("BNK_Id");
                        var bnk_branch = "&Srch_BnkBrnch=" + addSearchForm.getItemValue("BB_Id");
                        var acc_no = "&Srch_Accno=" + addSearchForm.getItemValue("BA_Id");
                        var chq_no = "&Srch_Cheque=" + addSearchForm.getItemValue("CHQ_Id");
                        var vchno = "&Srch_Voucher=" + addSearchForm.getItemValue("Vch_Id");

                        var filter = frm_date + to_date + item_name + paid_to + paid_from + cmpny_name + brnch_name + dept_name + usr_name + bnk_name + bnk_branch + acc_no + chq_no + vchno;
                        listSearchGrid.attachEvent("onRowSelect", function (id, ind) {
                            if (ind != 6)
                                $(".target_" + id).click();
                        });
                        listSearchGrid.loadXML(preTally.Initialize.encryptURL("requisites/searchBSItems.php&srch=1" + filter));


                    } else
                    {
                        addSearchForm.clear();
                        listSearchGrid.clearAll();
                    }
                });
            } else {
                dhxMiddleBlockTabs.tabs("searchAccount").setActive();
            }
        },
        searchTrack: function () {
            if (!dhxMiddleBlockTabs.cells("menuSearchTrack")) {
                dhxMiddleBlockTabs.addTab("menuSearchTrack", "<img src='images/icon/balsheet.gif' style='margin-top:2px;' />&nbsp;&nbsp;Search Tracks", 150);
                dhxMiddleBlockTabs.tabs("menuSearchTrack").setActive();
                dhxMiddleBlockLayout = dhxMiddleBlockTabs.cells("menuSearchTrack").attachLayout("3L");
                dhxMiddleBlockLayout.cells("a").setWidth(200);
                dhxMiddleBlockLayout.cells("a").hideHeader();
                dhxMiddleBlockLayout.cells("b").setHeight(300);
                dhxMiddleBlockLayout.cells("b").fixSize(true, true);
                dhxMiddleBlockLayout.cells("b").hideHeader();
                dhxMiddleBlockLayout.cells("c").hideHeader();
                searchTrackGrid = dhxMiddleBlockLayout.cells("c").attachGrid();
                searchTrackGrid.enableColSpan(true);
                searchTrackGrid.enableTooltips("false,false,false,false,false,false,false");
                searchTrackGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(false, dhxMiddleBlockLayout, 'c');
                });
                searchTrackGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(false, dhxMiddleBlockLayout, 'c');
                });
                searchTrackForm = dhxMiddleBlockLayout.cells("b").attachForm();
                searchTrackForm.loadStruct(preTally.Initialize.encryptURL("requisites/srchTrackForm.php"), function () {
                    trackCombo = searchTrackForm.getCombo("Track_id");
//                    trackCombo.enableFilteringMode(true);
//                    trackCombo.load(preTally.Initialize.encryptURL("requisites/tracks.php"));
                    searchTrackForm.attachEvent("onButtonClick", function (name) {

                        if (name == "Btn_Filter") {
                            var trname = searchTrackForm.getItemValue("Track_id");
                            searchTrackGrid.clearAll();
                            params = "trname=" + trname;
                            searchTrackGrid.loadXML(preTally.Initialize.encryptURL("requisites/searchTrack.php&" + params), function () {
                                searchTrackForm.setItemValue("job_inc", searchTrackGrid.getUserData("", "job_income"));
                                searchTrackForm.setItemValue("job_exp", searchTrackGrid.getUserData("", "job_expense"));
                                searchTrackForm.setItemValue("track_inc", searchTrackGrid.getUserData("", "track_income"));
                                searchTrackForm.setItemValue("track_exp", searchTrackGrid.getUserData("", "track_expense"));
                                searchTrackForm.setItemValue("track_bal", searchTrackGrid.getUserData("", "track_balance"));

                            });
                        } else
                        {
                            searchTrackGrid.clearAll();
                            trackCombo.clearAll();
                            trackCombo.setComboText("");
                            trackCombo.setComboValue(0);
                            searchTrackForm.setItemValue("job_inc", 0);
                            searchTrackForm.setItemValue("job_exp", 0);
                            searchTrackForm.setItemValue("track_inc", 0);
                            searchTrackForm.setItemValue("track_exp", 0);
                            searchTrackForm.setItemValue("track_bal", 0);
                        }
                    });
                });
            } else {

                dhxMiddleBlockTabs.tabs("menuSearchTrack").setActive();
            }
        },
        menuHolidays: function () {
            if (!dhxMiddleBlockTabs.cells("menuHolidays")) {
                var filtrInterval;

                dhxMiddleBlockTabs.addTab("menuHolidays", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Holidays", 130);
                //dhxMiddleBlockTabs.setTabActive("menuNewPaymode");
                dhxMiddleBlockTabs.tabs("menuHolidays").setActive();
                var dhxHolidayLayout = dhxMiddleBlockTabs.cells("menuHolidays").attachLayout('2U');
                dhxHolidayLayout.cells("a").setText("New Holiday");
                dhxHolidayLayout.cells("b").setText("List Holidays");

                dhxHolidayLayout.cells("a").setWidth(400);
                dhxHolidayLayout.cells("a").hideHeader();
                dhxHolidayLayout.cells("b").fixSize(true, true);
                dhxHolidayLayout.cells("b").hideHeader();

                holidayGrid = dhxHolidayLayout.cells("b").attachGrid();
                holidayGrid.enableColSpan(true);
                holidayGrid.enableMultiline(true);
                holidayGrid.attachHeader(',<select style="width:50%" id="monthFilter" onChange="preTally.Settings.applyFilter()"><option value="">All</option><option value="January">January</option><option value="February">February</option><option value="March">March</option><option value="April">April</option><option value="May">May</option><option value="June">June</option><option value="July">July</option><option value="August">August</option><option value="September">September</option><option value="October">October</option><option value="November">November</option><option value="December">December</option></select><select style="width:30%" id="yearFilter" onChange="preTally.Settings.applyFilter()"></select>,<input type="text" id="titleFilter" placeholder="Title">,,<select style="width:90%" id="typeFilter" onChange="preTally.Settings.applyFilter()"><option value="">All</option><option value="National">National</option><option value="State">State</option><option value="Restricted">Restricted</option><option value="Others">Others</option></select>,<select style="width:100%" id="statusFilter" onChange="preTally.Settings.applyFilter()"><option value="">All</option><option value="Active">Active</option><option value="Blocked">Blocked</option></select>,');

                holidayGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(false, dhxHolidayLayout, 'b');
                });
                holidayGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(false, dhxHolidayLayout, 'b');
                });
                holidayGrid.enableTooltips("false,false,false,false,false,false");
                holidayGrid.loadXML(preTally.Initialize.encryptURL("requisites/listHolidays.php"), function () {
                    var items = holidayGrid.collectValues(6);
                    $('#yearFilter').append($('<option>', {
                        value: '',
                        text: 'All'
                    }));
                    $.each(items, function (i, item) {
                        $('#yearFilter').append($('<option>', {
                            value: item,
                            text: item
                        }));
                    });
                    holidayGrid.attachEvent("onRowSelect", preTally.Settings.editHolidays);
                    $("#titleFilter").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.Settings.applyFilter();
                            clearInterval(filtrInterval);
                        }, 500);

                    });
                });

                holidayForm = dhxHolidayLayout.cells("a").attachForm();
                holidayForm.enableLiveValidation(true);
                holidayForm.loadStruct(preTally.Initialize.encryptURL("requisites/newHolidayForm.php"), function () {
                    var stateCombo = holidayForm.getCombo('States');
                    stateCombo.attachEvent("onCheck", function (value, state) {
                        if (value == '0') {
                            for (var i = 0; i < stateCombo.getOptionsCount(); i++) {
                                if (stateCombo.getOptionByIndex(i).value != 0) {
                                    if (state == true) {
                                        stateCombo.setChecked(i, true);
                                    } else if (state == false)
                                        stateCombo.setChecked(i, false);
                                }
                            }
                        } else {
                            if (stateCombo.isChecked(0)) {
                                stateCombo.setChecked(0, false);
                            }
                        }
                        return true;
                    });
                    itm_id = 1;
                    hd_count.push(0);
                    hdtypeCombo = holidayForm.getCombo("HD_Type");
                    hdtypeCombo.attachEvent("onClose", function () {
                        var hdid = holidayForm.getItemValue("HD_Id");
                        var hdtype = hdtypeCombo.getSelectedValue();
                        if (hdtype == 3) {
                            holidayForm.showItem("AddDays");
                            holidayForm.setItemValue("HD_Count", 0);
                            holidayForm.setItemValue("DP_Id", "All"); //09-09-2025
                            holidayForm.hideItem("DP_Id");//09-09-2025
                        } else {
                            holidayForm.hideItem("AddDays");
                            preTally.Settings.cleardateBlocks();
                            holidayForm.showItem("DP_Id"); //09-09-2025                           
                        }

                    });
                    hd_pos = 7;
                    holidayForm.attachEvent("onButtonClick", function (name) {
                        var btn = name.split("_");
                        var stateList = stateCombo.getChecked();
                        if (name == "SaveHoliday") {
                            if (holidayForm.validate()) {
                                if (stateList.length == 0) {
                                    holidayForm.setValidateCss("States", false, "validate_red");
                                } else {
                                    if (stateCombo.isChecked(0))
                                        holidayForm.setItemValue('StateList', 0);
                                    else
                                        holidayForm.setItemValue('StateList', stateList);
                                    preTally.Settings.progressOn(false, dhxHolidayLayout, 'a');
                                    holidayForm.send(preTally.Initialize.encryptURL("warehouse/newHoliday.php"), function (loader, response) {
                                        preTally.Settings.progressOff(false, dhxHolidayLayout, 'a');
                                        dhtmlx.message({text: response});
                                        //holidayGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listHolidays.php"),function(){});
                                        $('#monthFilter').val('');
                                        $("#titleFilter").val('');
                                        $('#typeFilter').val('');
                                        $('#statusFilter').val('');
                                        holidayGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listHolidays.php"), true, true, preTally.Settings.applyFilter());

                                        for (var i = 0; i < stateCombo.getOptionsCount(); i++) {
                                            stateCombo.setChecked(i, false);
                                        }
                                        preTally.Settings.cleardateBlocks();
                                        holidayForm.clear();
                                        holidayForm.setItemValue("HD_Id", 0);
                                        holidayForm.setItemValue("HD_Batch", 0);
                                    });
                                }
                            }
                        } else if (name == 'AddDays') {
                            var blkname = "HD_BLK_" + itm_id;
                            var txtname = "HD_Date_" + itm_id;
                            var hidname = "HD_hid_" + itm_id;
                            //var titlename="HD_Comments_"+itm_id;
                            var btnname = "btnrem_" + itm_id;

                            //console.log(name);
                            var itemData = {
                                "type": "block", name: blkname, "text": "File", width: "500", offsetLeft: "0", "list": [
                                    {type: "calendar", dateFormat: "%Y-%m-%d", offsetLeft: "0", required: "true", name: txtname, label: "Date", calendarPosition: "right", inputWidth: "130", labelWidth: "120", readOnly: true},
                                    {type: "newcolumn"},
                                    {type: "hidden", name: hidname, value: "0"},
                                    {type: "button", name: btnname, inputWidth: "20", offsetTop: "0", value: "", className: "btn_remove"}]}
                            holidayForm.addItem(null, itemData, hd_pos);
                            holidayForm.setReadonly(txtname, true);
                            str_hdid = holidayForm.getItemValue("HD_Count");
                            hd_count = str_hdid.split(",");
                            hd_count.push(itm_id);
                            itm_id = parseInt(itm_id) + 1;
                            hd_pos = parseInt(hd_pos) + 1;
                            holidayForm.setItemValue("HD_Count", hd_count.toString());
                        } else if (btn[0] == "btnrem") {
                            hd_id = holidayForm.getItemValue("HD_Id");
                            if (hd_id != 0) {
                                var del_id = hd_ids[btn[1]];
                                hd_ids.splice(btn[1], 1);
                                delhd_array.push(del_id);
                                holidayForm.setItemValue("HD_Id", hd_ids.toString());
                                holidayForm.setItemValue("HD_Del", delhd_array.toString());
//                                 $.ajax({
//                        type:"POST",    
//                        async: false,
//                        url: preTally.Initialize.encryptURL("warehouse/delRHDate.php&hdId=" + del_id )                        
//                        }).done(function(data) {
//                            
//                        });
                            }
                            holidayForm.removeItem("HD_BLK_" + btn[1]);
                            holidayForm.removeItem("HD_Date_" + btn[1]);
                            //holidayForm.removeItem("HD_Comments_"+btn[1]);
                            holidayForm.removeItem("btnrem_" + btn[1]);
                            hd_pos = parseInt(hd_pos) - 1;
                            var indx = hd_count.indexOf(parseInt(btn[1]));

                            if (indx != -1)
                            {
                                hd_count.splice(indx, 1);
                                //hd_count.remove(pos);    
                                //delete hd_count[pos];
                            }
                            holidayForm.setItemValue("HD_Count", hd_count.toString());
                        } else {
                            if (stateList.length > 0) {
                                for (var i = 0; i < stateCombo.getOptionsCount(); i++) {
                                    stateCombo.setChecked(i, false);
                                }
                            }
                            preTally.Settings.cleardateBlocks();
                            holidayForm.clear();
                            holidayForm.setItemValue("HD_Id", 0);
                            holidayForm.setItemValue("HD_Count", 0);
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuHolidays").setActive();
            }
        },
        editHolidays: function (rowId) {
            var date = new Date();
            var currdateString = new Date(date.getFullYear().toString(), date.getMonth() + 1, date.getDate());
            //var nwdate = split("-",holidayGrid.getUserData(rowId, "HD_Date"));
            var nwdate = holidayGrid.getUserData(rowId, "HD_Date").split('-');
            var hddate = new Date(nwdate[0], nwdate[1], nwdate[2]);
            var hdid = rowId;

            var stateCombo = holidayForm.getCombo('States');
            if (hddate < currdateString) {
                dhtmlx.message({text: "Can't edit previous holidays"});
                preTally.Settings.cleardateBlocks();
                for (var i = 0; i < stateCombo.getOptionsCount(); i++)
                    stateCombo.setChecked(i, false);
                holidayForm.clear();
            } else {
                preTally.Settings.cleardateBlocks();
                itm_id = 1;
                var batch_Id = holidayGrid.getUserData(hdid, "HD_Batch");
                holidayForm.setItemValue("HD_Batch", batch_Id);
                var hdtype = holidayGrid.getUserData(hdid, "HD_Type");
                var hddate = holidayGrid.getUserData(hdid, "HD_Date");
                if (batch_Id != 0 && hdtype == 3)
                {
                    holidayForm.showItem("AddDays");                    
                    holidayForm.setItemValue("DP_Id", "All"); //09-09-2025
                    holidayForm.hideItem("DP_Id");//09-09-2025
                    hd_ids = new Array();
                    hd_count = new Array();
                    hd_count.push(0);
                    hd_ids.push(hdid);
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: preTally.Initialize.encryptURL("warehouse/getRHDates.php&btchId=" + batch_Id + "&hddate=" + hddate)
                    }).done(function (data) {
                        itm_id = 1;
                        RH_DatesArray = jQuery.parseJSON(data);
                        for (var i = 0; i < RH_DatesArray.length; i++)
                        {
                            //itm_id=RH_DatesArray[i].HD_Id
                            var blkname = "HD_BLK_" + itm_id;
                            var txtname = "HD_Date_" + itm_id;
                            var hidname = "HD_hid_" + itm_id;
                            //var titlename="HD_Comments_"+itm_id;
                            var btnname = "btnrem_" + itm_id;
                            var itemData = {
                                "type": "block", name: blkname, "text": "File", width: "500", offsetLeft: "0", "list": [
                                    {type: "calendar", dateFormat: "%Y-%m-%d", offsetLeft: "0", required: "true", name: txtname, value: RH_DatesArray[i].HD_Date, label: "Date", calendarPosition: "right", inputWidth: "130", labelWidth: "120", readOnly: true},
                                    {type: "hidden", name: hidname, value: RH_DatesArray[i].HD_Id},
                                    {type: "newcolumn"},
                                    {type: "button", name: btnname, inputWidth: "20", offsetTop: "0", value: "", className: "btn_remove"}]}
                            holidayForm.setReadonly(txtname, true);
                            holidayForm.addItem(null, itemData, hd_pos);
                            hd_count.push(itm_id);
                            hd_ids.push(RH_DatesArray[i].HD_Id);
                            //pos=parseInt(pos)+2;             
                            itm_id = parseInt(itm_id) + 1;
                            hd_pos = parseInt(hd_pos) + 1;
                            var resdate = RH_DatesArray[i].HD_Date.split('-');
                            var reshddate = new Date(resdate[0], resdate[1], resdate[2]);
                            if (reshddate < currdateString) {
                                holidayForm.disableItem(btnname);
                                holidayForm.disableItem(txtname);
                            }
                        }
                    });

                    holidayForm.setItemValue("HD_Id", hd_ids.toString());
                    holidayForm.setItemValue("HD_Count", hd_count.toString());
                } else
                {
                    holidayForm.setItemValue("HD_Id", hdid);  
                    holidayForm.showItem("DP_Id");//09-09-2025
                    holidayForm.setItemValue("DP_Id", holidayGrid.getUserData(hdid, "DP_Id")); //09-09-2025
                }
                holidayForm.setItemValue("HD_Date_0", holidayGrid.getUserData(hdid, "HD_Date"));
                holidayForm.setItemValue("HD_hid_0", hdid);
                holidayForm.setItemValue("HD_Type", holidayGrid.getUserData(hdid, "HD_Type"));
                holidayForm.setItemValue("HD_Status", holidayGrid.getUserData(hdid, "HD_Status"));                
                holidayForm.setItemValue("HD_Comments", holidayGrid.getUserData(hdid, "HD_Comments"));

                if (holidayGrid.getUserData(hdid, "ST_Id") == 0) {
                    for (var i = 0; i < stateCombo.getOptionsCount(); i++) {
                        stateCombo.setChecked(i, true);
                    }
                } else {
                    var ST_Array = holidayGrid.getUserData(hdid, "ST_Id").split(',');
                    for (var i = 0; i < stateCombo.getOptionsCount(); i++) {
                        stateCombo.setChecked(i, false);
                        if (ST_Array.indexOf(stateCombo.getOptionByIndex(i).value) > -1) {
                            stateCombo.setChecked(i, true);
                        }
                    }
                }


            }
        },
        cleardateBlocks: function ()
        {
            holidayForm.hideItem("AddDays");
            holidayForm.showItem("DP_Id");//09-09-2025
            holidayForm.setItemValue("DP_Id", "All"); //09-09-2025
            str_hdcount = holidayForm.getItemValue("HD_Count");
            hdcount = new Array();
            hdcount.push(0);
            delhd_array = new Array();
            holidayForm.setItemValue("HD_Del", 0);
            itm_id = 1;
            hdcount = str_hdcount.split(",");
            for (var i = 0; i < hdcount.length; i++) {
                if (hdcount[i] != 0) {
                    holidayForm.removeItem("HD_BLK_" + hdcount[i]);
                    holidayForm.removeItem("HD_Date_" + hdcount[i]);
                    holidayForm.removeItem("HD_hid_" + hdcount[i]);
                    holidayForm.removeItem("btnrem_" + hdcount[i]);
                }
                hd_pos = 7;
                holidayForm.setItemValue("HD_Count", 0);
                holidayForm.setItemValue("HD_Batch", 0);
            }
            hdcount = new Array();
            arr_hdcount = new Array();
        },
        applyFilter: function (ob) {
            var monthVal = $('#monthFilter').val();
            var titleVal = $("#titleFilter").val().toLowerCase();
            var typeVal = $('#typeFilter').val();
            var statusVal = $('#statusFilter').val();
            var yearVal = $('#yearFilter').val();
            var c = 0;

            if (holidayGrid.doesRowExist("msgRow"))
                holidayGrid.deleteRow("msgRow");
            if (monthVal != "" || typeVal != "" || titleVal != "" || statusVal != "" || yearVal != "") {
                for (var i = 0; i < holidayGrid.getRowsNum(); i++) {

                    var dateData = holidayGrid.cells2(i, 1).getValue();
                    var titleData = holidayGrid.cells2(i, 2).getValue().toString().toLowerCase();
                    var typeData = holidayGrid.cells2(i, 3).getValue();
                    var statusData = holidayGrid.cells2(i, 5).getValue();
                    var YearData = holidayGrid.cells2(i, 6).getValue();
                    var split = dateData.split(' ');
                    var month = split[1];
                    if ((monthVal == "" || month == monthVal) && (titleVal == "" || titleData.indexOf(titleVal) == 0) && (typeVal == "" || typeData.indexOf(typeVal) == 0) && (statusVal == "" || statusVal == statusData) && (yearVal == "" || yearVal == YearData)) {
                        holidayGrid.setRowHidden(holidayGrid.getRowId(i), false);
                    } else {
                        holidayGrid.setRowHidden(holidayGrid.getRowId(i), true);
                        c++;
                    }
                }
            } else {
                for (var i = 0; i < holidayGrid.getRowsNum(); i++) {
                    holidayGrid.setRowHidden(holidayGrid.getRowId(i), false);
                }
            }
            if (holidayGrid.getRowsNum() == c) {
                if (holidayGrid.doesRowExist("msgRow"))
                    holidayGrid.deleteRow("msgRow");
                holidayGrid.addRow('msgRow', "No records found");
                holidayGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                holidayGrid.setColspan("msgRow", 0, 5);
            }
        },
        menu_ApproveLeave: function () {

            if (!dhxMiddleBlockTabs.cells("ApproveLeave")) {
                dhxMiddleBlockTabs.addTab("ApproveLeave", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Approve Leave", 150);
                dhxMiddleBlockTabs.tabs("ApproveLeave").setActive();
                ptapproveToolbar = dhxMiddleBlockTabs.cells("ApproveLeave").attachToolbar();
                ptapproveToolbar.setIconsPath("images/icon/default_18/");
                ptapproveToolbar.addText("text_from", null, "From Date");
                ptapproveToolbar.addInput("appleave_date_from", null, "", 75);
                ptapproveToolbar.addButton("appleave_df_clear", null, "", "close.gif");
                ptapproveToolbar.addSeparator();
                ptapproveToolbar.addText("text_till", null, "To Date");
                ptapproveToolbar.addInput("appleave_date_till", null, "", 75);
                ptapproveToolbar.addButton("appleave_dt_clear", null, "", "close.gif");
                ptapproveToolbar.addSeparator();
                ptapproveToolbar.addButton("appleave_date_filter", null, "Search", "save.gif");
                ptapproveToolbar.addText("spacer", '9', "");
                ptapproveToolbar.disableItem("spacer");
                ptapproveToolbar.setWidth("spacer", 100);
                ptapproveToolbar.addButton("subord_Leave", null, "Report Leave for Subordinates", "save.gif");
                var applvTb_Inp_Frm = ptapproveToolbar.getInput("appleave_date_from");
                applvTb_Inp_Frm.setAttribute("readOnly", "true");
                applvTb_Inp_Frm.onclick = function () {
                    if (ptapproveToolbar.getValue("appleave_date_till"))
                        preTally.Settings.setSensitive(applvTb_Inp_Til, "max");
                }
                var applvTb_Inp_Til = ptapproveToolbar.getInput("appleave_date_till");
                applvTb_Inp_Til.setAttribute("readOnly", "true");
                applvTb_Inp_Til.onclick = function () {
                    if (ptapproveToolbar.getValue("appleave_date_from"))
                        preTally.Settings.setSensitive(applvTb_Inp_Frm, "min");
                }
                applvTb_Calendar = new dhtmlXCalendarObject([applvTb_Inp_Frm, applvTb_Inp_Til]);
                applvTb_Calendar.setDateFormat("%d.%m.%Y");
                var ACLApproveLeave = unescape(JGG1P3bDnUSDL13Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                ptapproveToolbar.attachEvent("onClick", function (id) {
                    if (id == "appleave_date_filter") {
                        if (ACLApproveLeave == 1) {
                            preTally.Settings.applyApproveLeaveFilter();
                        }
                        preTally.Settings.applyPendingLeaveFilter();
                    }
                    if (id == "appleave_df_clear") {
                        ptapproveToolbar.setValue('appleave_date_from', '', false);
                    }
                    if (id == "appleave_dt_clear") {
                        ptapproveToolbar.setValue('appleave_date_till', '', false);
                    }
                    if (id == "subord_Leave") {
                        preTally.Settings.applySubordLeave();
                    }

                });


                var dhxApproveLeaveLayout = dhxMiddleBlockTabs.cells("ApproveLeave").attachLayout('1C');
                var LvAppTab = dhxApproveLeaveLayout.cells("a").attachTabbar();
                LvAppTab.addTab('lv_approve', 'Pending Approval');
                LvAppTab.cells('lv_approve').setActive();
                var LvAppLayout = LvAppTab.cells('lv_approve').attachLayout("1C");
                ApproveLeaveStbarPend = LvAppTab.cells("lv_approve").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='aprLeavePend_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='approveLeavePend_paging'></div>",
                    height: 30
                });
                LvAppLayout.cells('a').hideHeader();
                ApproveLeaveGrid = LvAppLayout.cells("a").attachGrid();
                preTally.Settings.PendingLeaveGrid();

                if (ACLApproveLeave == 1) {
                    LvAppTab.addTab('lv_all', 'All Leaves');
                    var LvAllLayout = LvAppTab.cells('lv_all').attachLayout("1C");
                    ApproveLeaveStbarAll = LvAppTab.cells("lv_all").attachStatusBar({
                        text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='aprLeaveAll_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='approveLeaveAll_paging'></div>",
                        height: 30
                    });
                    LvAllLayout.cells('a').hideHeader();
                    LeaveListGrid = LvAllLayout.cells("a").attachGrid();
                    preTally.Settings.AllLeaveGird();
                }
            } else {
                dhxMiddleBlockTabs.tabs("ApproveLeave").setActive();
            }

        }, AllLeaveGird: function () {
            LeaveListGrid.setHeader("SlNo,From Date<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='2' class='btn_AppLvSort' style='float:right;' />,To Date<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_AppLvSort' style='float:right;' />,Total Days,Eligible Days,Name,Applied By,Branch,Leave Type,First Approval,HR Approval,Applied On,First Approved On,Reason,Comment,Status");
            LeaveListGrid.setInitWidths("30,80,80,40,50,90,110,110,80,80,100,100,100,*,0,*");
            LeaveListGrid.setColAlign("center,left,left,center,center,left,left,left,left,left,left,left,left,left,left,left");
            LeaveListGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
            LeaveListGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ed,ro");
            LeaveListGrid.enableColSpan(true);
            LeaveListGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false");
            LeaveListGrid.enableEditEvents(true, true, true, true, true, true, true, true, true, true, true, true, true, true);
            LeaveListGrid.init();
            LeaveListGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
            LeaveListGrid.enablePaging(true, 50, 5, "approveLeaveAll_paging", true);
            LeaveListGrid.setPagingSkin("toolbar", "dhx_skyblue");
            LeaveListGrid.setImagePath('assets/grid/codebase/imgs/');
            var ACLApproveLeave = unescape(JGG1P3bDnUSDL13Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            if (ACLApproveLeave == 1) {
                LeaveListGrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,\n\
                                                <input type='text' class='appLv_text_filter' id='userALF' style='width: 90%;' placeholder='User'><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_AppLvSort' style='float:right;' />,\n\
                                                <input type='text' class='appLv_text_filter' id='appliedByALF' style='width: 90%;' placeholder='Applied By'>,\n\
                                                <input type='text' class='appLv_text_filter' id='branchALF' style='width: 90%;' placeholder='Branch'>,\n\
                                                <div id='leaveTypeALFAll' style='width: 90%;' placeholder='Leave Type'></div>,\n\
                                                <input type='text'  class='appLv_text_filter' id='fstAprvlALF' style='width: 90%;' placeholder=' '>,\n\
                                                <input type='text'  class='appLv_text_filter' id='hrAprvlALF' style='width: 90%;' placeholder=''>,#rspan,#rspan,#rspan,#rspan,\n\
                                                <select style = 'width:80%;' class = 'appLv_select_filter' id='statusALF'>\n\
                                                    <option value =''>All</option>\n\
                                                    <option value ='0'>Pending Approval</option>\n\
                                                    <option value ='1'>First Approved</option>\n\
                                                    <option value ='2'>HR Approved</option>\n\
                                                    <option value ='4'>Rejected By HR</option>\n\
                                                    <option value ='5'>Cancelled</option>\n\
                                                    <option value ='6'>Leave not taken</option>\n\
                                                </select>");
            } else {
                LeaveListGrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,\n\
                                                <input type='text' class='appLv_text_filter' id='userALF' style='width: 90%;' placeholder='User'><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_AppLvSort' style='float:right;' />,\n\
                                                <input type='text' class='appLv_text_filter' id='appliedByALF' style='width: 90%;' placeholder='Applied By'>,\n\
                                                <input type='text' class='appLv_text_filter' id='branchALF' style='width: 90%;' placeholder='Branch'>,\n\
                                                <div id='leaveTypeALFAll' style='width: 90%;' placeholder='Leave Type'></div>,\n\
                                                <input type='text'  class='appLv_text_filter' id='fstAprvlALF' style='width: 90%;' placeholder=' '>,\n\
                                                <input type='text'  class='appLv_text_filter' id='hrAprvlALF' style='width: 90%;' placeholder=''>,#rspan,#rspan,#rspan,#rspan,\n\
                                                <select style = 'width:80%;' class = 'appLv_select_filter' id='statusALF'>\n\
                                                    <option value =''>All</option>\n\
                                                    <option value ='0'>Pending Approval</option>\n\
                                                    <option value ='1'>First Approved</option>\n\
                                                    <option value ='2'>HR Approved</option>\n\
                                                    <option value ='3'>Rejected By Reporting Person</option>\n\
                                                    <option value ='4'>Rejected By HR</option>\n\
                                                    <option value ='5'>Cancelled</option>\n\
                                                    <option value ='6'>Leave not taken</option>\n\
                                                </select>");
            }
            preTally.Settings.progressOn(true, dhxLayout, null);

            LeaveListGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, dhxLayout, null);
            });
            LeaveListGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });

            LeaveListGrid.loadXML(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                var filtrALInterval;
                var leaveTypeCombo = new dhtmlXCombo("leaveTypeALFAll");
                leaveTypeCombo.load(preTally.Initialize.encryptURL("requisites/leaveTypeFiltr.php"), function () {
                    leaveTypeCombo.setPlaceholder('Leave Type ');
                    leaveTypeCombo.setFilterHandler(function (mask, option) {
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                });
                leaveTypeCombo.setOptionWidth(250);
                leaveTypeCombo.attachEvent("onChange", function () {
                    var leaveTypeComboVal = leaveTypeCombo.getSelectedValue();
//                        if(!leaveTypeCombo.getSelectedValue() && leaveTypeCombo.getComboText()) leaveTypeComboVal = leaveTypeCombo.getComboText();
                    $("#leaveTypeALFAll").val(leaveTypeComboVal);
                    preTally.Settings.applyApproveLeaveFilter(null, 2, 'dsc');
                });

                $(".appLv_text_filter").keyup(function () {
                    if (filtrALInterval)
                        clearInterval(filtrALInterval);

                    filtrALInterval = setInterval(function () {
                        preTally.Settings.applyApproveLeaveFilter(null, 2, 'dsc');
                        clearInterval(filtrALInterval);
                    }, 500);
                });
                var appLvsrtFlg = 1
                $(".btn_AppLvSort").click(function () {

                    var ColNum = $(this).attr("colNum");
                    appLvSortdRow = ColNum;
                    var colimg;
                    if (appLvsrtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        appOrd = 'asc';
                        appLvsrtFlg = 1;

                    } else {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        appOrd = 'des';
                        appLvsrtFlg = 0;
                    }
                    preTally.Settings.applyApproveLeaveFilter(null, appLvSortdRow, appOrd);
                });
                $(".appLv_select_filter").change(function () {
                    preTally.Settings.applyApproveLeaveFilter(null, 2, 'dsc');
                });
                //                        LeaveListGrid.attachEvent("onMouseOver", function(id,ind) { 
                //                            if(ind == 10) {
                //                                this.cells(id,ind).cell.title = 'Click here for Approve';
                //                                return false;
                //                            }
                //                            if(ind == 11) {
                //                                this.cells(id,ind).cell.title = 'Click here to Cancel';
                //                                return false;
                //                            }
                //                        });
                comment = "";
                $('.aprLeaveAll_cnt_tot').html("# : " + LeaveListGrid.getUserData("", "TL_Count"));
                LeaveListGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                    if (stage == 0) {
                        if (cInd == 15) {
                            status = LeaveListGrid.getUserData(rId, "LR_Status");
                            LR_Id = LeaveListGrid.getUserData(rId, "LR_Id");
                            ACL_ApproveLeave = LeaveListGrid.getUserData(rId, "ACL_ApproveLeave");
                            gridCombovalue = LeaveListGrid.cells(rId, cInd).getValue();
                            cellComboObject = LeaveListGrid.cells(rId, cInd).getCellCombo();
                            cellComboObject.readonly(true);
                            preTally.Settings.selectStatusOptions(status, gridCombovalue, '', ACL_ApproveLeave);
                        }
                        if (cInd == 14)
                            comment = LeaveListGrid.cells(rId, 14).getValue();
                    } else if (stage == 2) {
                        if (cInd == 15) {
                            var newgridCombovalue = cellComboObject.getSelectedValue();
                            comment = LeaveListGrid.cells(rId, 14).getValue();
                            var newgridCombovalue = cellComboObject.getSelectedValue();
                            var newgridComboText = cellComboObject.getSelectedText();
                            if (newgridCombovalue != null) {
                                dhtmlx.confirm({
                                    title: "Confirm Edit",
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Do you want to change the status from " + gridCombovalue + " to " + newgridComboText + "?",
                                    callback: function (response) {
                                        if (response) {
                                            preTally.Settings.leaveList(LR_Id, newgridCombovalue, comment);
                                            comment = "";
                                        } else {
                                            LeaveListGrid.cells(rId, 15).setValue(oValue);
                                        }
                                    }
                                });
                            }
                        }
                    }
                    return true;
                });

            });

            LeaveListGrid.attachEvent("onFilterEnd", function (elements) {
                $('.aprLeaveAll_cnt_tot').html("# : " + LeaveListGrid.getUserData("", "TL_Count"));
                if (LeaveListGrid.getRowsNum() === 0) {
                    LeaveListGrid.addRow('0', "No records found", 0);
                    LeaveListGrid.setColspan('0', 0, 15);
                    LeaveListGrid.setRowTextStyle('0', "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                } else {
                    //LeaveListGrid.deleteRow(0);
                    // LeaveListGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function() {});
                }

            });

        }, PendingLeaveGrid: function () {
            ApproveLeaveGrid.setHeader("SlNo,From Date<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='2' class='btn_AppLvSort' style='float:right;' />,To Date<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='3' class='btn_AppLvSort' style='float:right;' />,Total Days,Eligible Days,Name,Applied By,Branch,Leave Type,First Approval,HR Approval,Applied On,First Approved On,Reason,Comment,Status");
            ApproveLeaveGrid.setInitWidths("30,80,80,40,50,90,110,110,75,75,90,80,80,*,*,*");
            ApproveLeaveGrid.setColAlign("center,left,left,center,center,left,left,left,left,left,left,left,left,left,left,left");
            ApproveLeaveGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
            ApproveLeaveGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ed,combo");
            ApproveLeaveGrid.enableColSpan(true);
            ApproveLeaveGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false");
            ApproveLeaveGrid.enableEditEvents(true, true, true, true, true, true, true, true, true, true, true, true, true, true);
            ApproveLeaveGrid.init();
            ApproveLeaveGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
            ApproveLeaveGrid.enablePaging(true, 50, 5, "approveLeavePend_paging", true);
            ApproveLeaveGrid.setPagingSkin("toolbar", "dhx_skyblue");
            ApproveLeaveGrid.setImagePath('assets/grid/codebase/imgs/');
            var ACLApproveLeave = unescape(JGG1P3bDnUSDL13Mui7KzYjj28UjPdWxCCtGkJSHeuo);
            if (ACLApproveLeave == 1) {
                ApproveLeaveGrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,\n\
                                                <input type='text' class='appLvP_text_filter' id='userALFPend' style='width: 90%;' placeholder='User'><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_AppLvSort' style='float:right;' />,\n\
                                                <input type='text' class='appLvP_text_filter' id='appliedByALFPend' style='width: 90%;' placeholder='Applied By'>,\n\
                                                <input type='text' class='appLvP_text_filter' id='branchALFPend' style='width: 90%;' placeholder='Branch'>,\n\
                                                <div id='leaveTypeALFPend' style='width: 90%;' placeholder='Leave Type'></div>,\n\
                                                <input type='text'  class='appLvP_text_filter' id='fstAprvlALFPend' style='width: 90%;' placeholder=' '>,\n\
                                                <input type='text'  class='appLvP_text_filter' id='hrAprvlALFPend' style='width: 90%;' placeholder=''>,#rspan,#rspan,#rspan,#rspan,\n\
                                                <select style = 'width:80%;' class = 'appLvP_select_filter' id='statusALFPend'>\n\
                                                    <option value =''>All</option>\n\
                                                    <option value ='0'>Pending Approval</option>\n\
                                                    <option value ='1'>First Approved</option>\n\
                                                    <option value ='2'>HR Approved</option>\n\
                                                    <option value ='4'>Rejected By HR</option>\n\
                                                    <option value ='5'>Cancelled</option>\n\
                                                    <option value ='6'>Leave not taken</option>\n\
                                                </select>");
            } else {
                ApproveLeaveGrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,\n\
                                                <input type='text' class='appLvP_text_filter' id='userALFPend' style='width: 90%;' placeholder='User'><img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='6' class='btn_AppLvSort' style='float:right;' />,\n\
                                                <input type='text' class='appLvP_text_filter' id='appliedByALFPend' style='width: 90%;' placeholder='Applied By'>,\n\
                                                <input type='text' class='appLvP_text_filter' id='branchALFPend' style='width: 90%;' placeholder='Branch'>,\n\
                                                <div id='leaveTypeALFPend' style='width: 90%;' placeholder='Leave Type'></div>,\n\
                                                <input type='text'  class='appLvP_text_filter' id='fstAprvlALFPend' style='width: 90%;' placeholder=' '>,\n\
                                                <input type='text'  class='appLvP_text_filter' id='hrAprvlALFPend' style='width: 90%;' placeholder=''>,#rspan,#rspan,#rspan,#rspan,\n\
                                                <select style = 'width:80%;' class = 'appLvP_select_filter' id='statusALFPend'>\n\
                                                    <option value =''>All</option>\n\
                                                    <option value ='0'>Pending Approval</option>\n\
                                                    <option value ='1'>First Approved</option>\n\
                                                    <option value ='2'>HR Approved</option>\n\
                                                    <option value ='3'>Rejected By Reporting Person</option>\n\
                                                    <option value ='4'>Rejected By HR</option>\n\
                                                    <option value ='5'>Cancelled</option>\n\
                                                    <option value ='6'>Leave not taken</option>\n\
                                                </select>");
            }
            preTally.Settings.progressOn(true, dhxLayout, null);

            ApproveLeaveGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, dhxLayout, null);
            });
            ApproveLeaveGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });

            ApproveLeaveGrid.loadXML(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
                var filtrALInterval;
                var leaveTypeCombo = new dhtmlXCombo("leaveTypeALFPend");
                leaveTypeCombo.load(preTally.Initialize.encryptURL("requisites/leaveTypeFiltr.php"), function () {
                    leaveTypeCombo.setPlaceholder('Leave Type ');
                    leaveTypeCombo.setFilterHandler(function (mask, option) {
                        var r = false;
                        if (mask.length == 0) {
                            r = true;
                        } else if (option.text.match(new RegExp("^" + mask, "i")) != null) {
                            r = true;
                        }
                        return r;
                    });
                });
                leaveTypeCombo.setOptionWidth(250);
                leaveTypeCombo.attachEvent("onChange", function () {
                    var leaveTypeComboVal = leaveTypeCombo.getSelectedValue();
//                        if(!leaveTypeCombo.getSelectedValue() && leaveTypeCombo.getComboText()) leaveTypeComboVal = leaveTypeCombo.getComboText();
                    $("#leaveTypeALFPend").val(leaveTypeComboVal);
                    preTally.Settings.applyPendingLeaveFilter(null, 2, 'dsc');
                });

                $(".appLvP_text_filter").keyup(function () {
                    if (filtrALInterval)
                        clearInterval(filtrALInterval);
                    filtrALInterval = setInterval(function () {
                        preTally.Settings.applyPendingLeaveFilter(null, 2, 'dsc');
                        clearInterval(filtrALInterval);
                    }, 500);
                });
                var appLvsrtFlg = 1
                $(".btn_AppLvSort").click(function () {

                    var ColNum = $(this).attr("colNum");
                    appLvSortdRow = ColNum;
                    var colimg;
                    if (appLvsrtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        appOrd = 'asc';
                        appLvsrtFlg = 1;

                    } else {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        appOrd = 'des';
                        appLvsrtFlg = 0;
                    }
                    preTally.Settings.applyPendingLeaveFilter(null, appLvSortdRow, appOrd);
                });
                $(".appLvP_select_filter").change(function () {
                    preTally.Settings.applyPendingLeaveFilter(null, 2, 'dsc');
                });
                //                        ApproveLeaveGrid.attachEvent("onMouseOver", function(id,ind) { 
                //                            if(ind == 10) {
                //                                this.cells(id,ind).cell.title = 'Click here for Approve';
                //                                return false;
                //                            }
                //                            if(ind == 11) {
                //                                this.cells(id,ind).cell.title = 'Click here to Cancel';
                //                                return false;
                //                            }
                //                        });
                comment = "";
                $('.aprLeaveAll_cnt_tot').html("# : " + ApproveLeaveGrid.getUserData("", "TL_Count"));
                ApproveLeaveGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                    if (stage == 0) {
                        if (cInd == 15) {
                            status = ApproveLeaveGrid.getUserData(rId, "LR_Status");
                            LR_Id = ApproveLeaveGrid.getUserData(rId, "LR_Id");
                            ACL_ApproveLeave = ApproveLeaveGrid.getUserData(rId, "ACL_ApproveLeave");
                            gridCombovalue = ApproveLeaveGrid.cells(rId, cInd).getValue();
                            cellComboObject = ApproveLeaveGrid.cells(rId, cInd).getCellCombo();
                            cellComboObject.readonly(true);
                            preTally.Settings.selectStatusOptions(status, gridCombovalue, '', ACL_ApproveLeave);
                        }
                        if (cInd == 14)
                            comment = ApproveLeaveGrid.cells(rId, 14).getValue();
                    } else if (stage == 2) {
                        if (cInd == 15) {
                            var newgridCombovalue = cellComboObject.getSelectedValue();
                            comment = ApproveLeaveGrid.cells(rId, 14).getValue();
                            var newgridCombovalue = cellComboObject.getSelectedValue();
                            var newgridComboText = cellComboObject.getSelectedText();
                            if (newgridCombovalue != null) {
                                dhtmlx.confirm({
                                    title: "Confirm Edit",
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Do you want to change the status from " + gridCombovalue + " to " + newgridComboText + "?",
                                    callback: function (response) {
                                        if (response) {
                                            preTally.Settings.leaveList(LR_Id, newgridCombovalue, comment);
                                            comment = "";
                                        } else {
                                            ApproveLeaveGrid.cells(rId, 15).setValue(oValue);
                                        }
                                    }
                                });
                            }
                        }
                    }
                    return true;
                });
                $('.aprLeavePend_cnt_tot').html("# : " + ApproveLeaveGrid.getUserData("", "TL_Count"));
            });

            ApproveLeaveGrid.attachEvent("onFilterEnd", function (elements) {
                $('.aprLeavePend_cnt_tot').html("# : " + ApproveLeaveGrid.getUserData("", "TL_Count"));
                if (ApproveLeaveGrid.getRowsNum() === 0) {
                    ApproveLeaveGrid.addRow('0', "No records found", 0);
                    ApproveLeaveGrid.setColspan('0', 0, 14);
                    ApproveLeaveGrid.setRowTextStyle('0', "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                } else {
                    //ApproveLeaveGrid.deleteRow(0);
                    // ApproveLeaveGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function() {});
                }

            });
        },
        applyApproveLeaveFilter: function (value, colNum, Ord) {

            if (value == 1) { // Notification section - Leave Application
                $('#statusALF').val(0);
            }

            var dateFilter = "&appFromDate=" + ptapproveToolbar.getValue("appleave_date_from") + "&appToDate=" + ptapproveToolbar.getValue("appleave_date_till");
            var filterValue = new Array($('#userALF').val(), $('#appliedByALF').val(), $('#branchALF').val(), $('#leaveTypeALFAll').val(), $('#fstAprvlALF').val(), $('#hrAprvlALF').val(), $('#statusALF').val(), colNum, Ord);

            preTally.Settings.progressOn(true, dhxLayout, null);
            LeaveListGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php&mode=All&filter=" + filterValue + dateFilter), function () {
                $('.aprLeaveAll_cnt_tot').html("# : " + LeaveListGrid.getUserData("", "TL_Count"));
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        applyPendingLeaveFilter: function (value, colNum, Ord) {

            if (value == 1) { // Notification section - Leave Application
                $('#statusALFPend').val(0);
            }

            var dateFilter = "&appFromDate=" + ptapproveToolbar.getValue("appleave_date_from") + "&appToDate=" + ptapproveToolbar.getValue("appleave_date_till");
            var filterValue = new Array($('#userALFPend').val(), $('#appliedByALFPend').val(), $('#branchALFPend').val(), $('#leaveTypeALFPend').val(), $('#fstAprvlALFPend').val(), $('#hrAprvlALFPend').val(), $('#statusALFPend').val(), colNum, Ord);

//            ApproveLeaveGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);
            ApproveLeaveGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php&filter=" + filterValue + dateFilter), function () {
                $('.aprLeavePend_cnt_tot').html("# : " + ApproveLeaveGrid.getUserData("", "TL_Count"));
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        selectLateEntryStatusOptions: function (value) {
            cellLateEntryComboObject.clearAll();

            if (value == "pending approval") {
                cellLateEntryComboObject.addOption('approved', 'Approved');
                cellLateEntryComboObject.addOption('rejected', 'Rejected');
            }
        },
        selectStatusOptions: function (status, value, rId, ACL_ApproveLeave) {
            cellComboObject.clearAll();

            if (value == "Pending Approval") {
                if (ACL_ApproveLeave == 1) {
                    cellComboObject.addOption('2', 'HR Approved');
                    cellComboObject.addOption('4', 'Rejected By HR');
                } else {
                    cellComboObject.addOption('1', 'First Approved');
                    cellComboObject.addOption('3', 'Rejected By Reporting Person');
                }
            } else if (value == "First Approved") {
                if (ACL_ApproveLeave == 1) {
                    cellComboObject.addOption('2', 'HR Approved');
                    cellComboObject.addOption('4', 'Rejected By HR');
                } else {
                    cellComboObject.addOption('6', 'Leave not Taken');
                }
            } else if (value == "HR Approved") {
                cellComboObject.addOption('6', 'Leave not Taken');
            }
        },
        applySubordLeave: function () {
            var dhxSubLeave = new dhtmlXWindows();
            var SubLeaveWin = dhxSubLeave.createWindow("SubLeave", 200, 400, 450, 500);
            SubLeaveWin.button("minmax1").hide();
            SubLeaveWin.button("minmax2").hide();
            SubLeaveWin.button("park").hide();
            SubLeaveWin.center();
            SubLeaveWin.setModal(true);
            SubLeaveWin.setText("Subordinate Leave");
            var SubLeaveFrm = SubLeaveWin.attachForm();
            SubLeaveFrm.load(preTally.Initialize.encryptURL("requisites/applySubordLeave.php"), function () {

                subordinateCombo = SubLeaveFrm.getCombo('LR_AppliedFor');
                subleaveCombo = SubLeaveFrm.getCombo('LT_Id');
                subordinateCombo.attachEvent("onChange", function (id) {
                    var subordinateID = subordinateCombo.getSelectedValue();
                    subleaveCombo.load(preTally.Initialize.encryptURL("requisites/leaveType.php&subordId=" + subordinateID));
                });
                subleaveCombo.attachEvent("onChange", function (id) {
                    subordinateID = subordinateCombo.getSelectedValue();
                    leaveType = subleaveCombo.getSelectedValue();
                    if (!subordinateID) {
                        dhtmlx.message({text: "Please choose subordinate"});
                    } else if ((leaveType != " ") && (leaveType != "0")) {
                        if (SubLeaveFrm.getCalendar("LR_FromDate").getDate(true) != null && SubLeaveFrm.getCalendar("LR_ToDate").getDate(true) != null)
                        {
                            var params = "lType=" + leaveType + "&subId=" + subordinateID + "&fromDate=" + SubLeaveFrm.getCalendar("LR_FromDate").getDate(true) + "&toDate=" + SubLeaveFrm.getCalendar("LR_ToDate").getDate(true);
                            $.post(preTally.Initialize.encryptURL("warehouse/checkLeaveAvailable.php&" + params), function (data) {
                                if (data == "1110") {
                                    dhtmlx.message({text: "You are not elegible for this leave"});
                                    SubLeaveFrm.setItemValue("LR_eligibleLeave", "");
                                } else if (data == "YErr") {
                                    dhtmlx.message({text: "Date on Different years cannot be applied"})
                                    SubLeaveFrm.setItemValue("LR_eligibleLeave", "");
                                } else {
                                    if (data == "") {
                                        data = "0.0";
                                    }
                                    SubLeaveFrm.setItemValue("LR_eligibleLeave", data);
                                }
                            });
                        } else {
                            dhtmlx.message({text: "Select From and To Dates"});
                            SubLeaveFrm.setValidateCss('LR_FromDate', true, 'validate_red');
                            SubLeaveFrm.setValidateCss('LR_ToDate', true, 'validate_red');
                        }
                    }

                });
                SubLeaveFrm.attachEvent("onChange", function (id) {
                    var date_differ = 1;
                    if (id == "LR_FromDate") {
                        SubLeaveFrm.setItemValue("LR_ToDate", SubLeaveFrm.getItemValue("LR_FromDate"));
                        SubLeaveFrm.setItemValue("LR_NumOFDays", 1);

                        SubLeaveFrm.enableItem("LR_duration");
                    }

                    if (id == "LR_ToDate") {

                        var FromDatevalue = SubLeaveFrm.getItemValue("LR_FromDate");
                        var toDatevalue = SubLeaveFrm.getItemValue("LR_ToDate");
                        //Get 1 day in milliseconds
                        var one_day = 1000 * 60 * 60 * 24;
                        // Convert both dates to milliseconds
                        var date1_ms = FromDatevalue.getTime();
                        var date2_ms = toDatevalue.getTime();
                        // Calculate the difference in milliseconds
                        var difference_ms = date2_ms - date1_ms;
                        var date_diff = difference_ms / one_day;
                        date_diff = date_diff + 1;
                        date_differ = date_diff.toFixed(1);
                        if (SubLeaveFrm.getCombo('LR_duration').getSelectedValue() == 1) {
                            if (0.5 * date_differ < 0) {
                                dhtmlx.message({text: "Invalid date selection"});
                                SubLeaveFrm.setItemValue("LR_NumOFDays", '');
                            } else
                                SubLeaveFrm.setItemValue("LR_NumOFDays", 0.5 * date_differ);
                        } else {
                            if (date_differ < 0) {
                                dhtmlx.message({text: "Invalid date selection"});
                                SubLeaveFrm.setItemValue("LR_NumOFDays", '');
                            } else
                                SubLeaveFrm.setItemValue("LR_NumOFDays", date_differ);
                        }
                        if (date_differ == 1)
                            SubLeaveFrm.enableItem("LR_duration");
                        else {
                            SubLeaveFrm.disableItem("LR_duration");
                            SubLeaveFrm.setItemValue("LR_duration", 0);
                        }
                    }
                    if (id == "LR_duration") {
                        if (SubLeaveFrm.getCombo('LR_duration').getSelectedValue() != 0)
                            SubLeaveFrm.setItemValue("LR_NumOFDays", 0.5);
                        else
                            SubLeaveFrm.setItemValue("LR_NumOFDays", date_differ);
                    }
                });
                SubLeaveFrm.attachEvent("onButtonClick", function (id) {
                    if (id == "saveApplyLeave") {

                        var SubLvFrm = SubLeaveFrm.validate();
                        if (SubLvFrm) {
                            if (SubLeaveFrm.getItemValue("LR_eligibleLeave") == '0.0' || parseInt(SubLeaveFrm.getItemValue("LR_eligibleLeave")) < parseInt(SubLeaveFrm.getItemValue("LR_NumOFDays")))
                                dhtmlx.message({text: "Subordinate exceed eligible leave limit"});
                            else if (SubLeaveFrm.getItemValue("LR_eligibleLeave") && SubLeaveFrm.getItemValue("LR_NumOFDays")) {
                                //SubLeaveFrm.send(preTally.Initialize.encryptURL("warehouse/applySubordLeave.php"),function(loader,response){
                                var params = "name1=" + id;
                                SubLeaveFrm.send(preTally.Initialize.encryptURL("warehouse/applyLeave.php&" + params), function (loader, response) {
                                    if (response == "exists") {
                                        dhtmlx.message({text: "Leave already taken on selected date"});
                                        SubLeaveFrm.setItemValue("LR_eligibleLeave", "");
                                    } else {
                                        dhtmlx.message({text: response});
                                        SubLeaveWin.close();
                                        //preTally.Settings.applyApproveLeaveFilter();
//                                        ApproveLeaveGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"),function() {$('.aprLeave_cnt_tot').html("# : "+ApproveLeaveGrid.getUserData("", "TL_Count"));});
                                    }
                                });
                            }
                        }
                    } else {
                        SubLeaveFrm.clear();
                        //SubLeaveWin.close();
                    }
                });
            });
        },
        menu_ManageLeaveType: function () {
            if (!dhxMiddleBlockTabs.cells("ManageLeaveType")) {
                dhxMiddleBlockTabs.addTab("ManageLeaveType", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Leave Type", 170);
                dhxMiddleBlockTabs.tabs("ManageLeaveType").setActive();
                dhxManageLeaveTypeLayout = dhxMiddleBlockTabs.cells("ManageLeaveType").attachLayout('2U');
                dhxManageLeaveTypeLayout.cells("a").setText("New Leave Type");
                dhxManageLeaveTypeLayout.cells("b").setText("List Leave Type");
                ManageLeaveTypeForm = dhxManageLeaveTypeLayout.cells("a").attachForm();
                ManageLeaveTypeForm.loadStruct(preTally.Initialize.encryptURL("requisites/ManageLeaveTypeForm.php"), function () {
                    checkEmpStatus = ManageLeaveTypeForm.getItemValue("employee_Status");
                    checkEmpStatus = checkEmpStatus.split(",");
                    emp_StatusCombo = ManageLeaveTypeForm.getCombo('emp_Status');

                    ManageLeaveTypeForm.attachEvent("onChange", function (name, value) {
                        if (name == "LT_LOP") {
                            if (ManageLeaveTypeForm.getCheckedValue("LT_LOP") == 1)
                                ManageLeaveTypeForm.setItemValue("LT_MaxCount", "30");
                        }

                    });

                    ManageLeaveTypeForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveLeaveType") {
                            emp_StatusComboChecked = emp_StatusCombo.getChecked();
                            if (emp_StatusComboChecked.length != 0) {
                                params = "emp_StatusComboChecked=" + emp_StatusComboChecked;
                                ManageLeaveTypeForm.send(preTally.Initialize.encryptURL("warehouse/saveManageLeaveType.php&" + params), function (loader, response) {
                                    ManageLeaveTypeGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listManageLeaveType.php"), true, true);
                                    dhtmlx.message({text: response});
                                    ManageLeaveTypeForm.setItemValue("LT_Id", "");
                                    ManageLeaveTypeForm.setItemValue("LT_Name", "");
                                    ManageLeaveTypeForm.setItemValue("LT_MaxCount", "");
                                    ManageLeaveTypeForm.setItemValue("LT_Status", "");
                                    ManageLeaveTypeForm.checkItem("LT_LOP", "0");
                                    for (count = 0; count < checkEmpStatus.length; count++) {
                                        emp_StatusCombo.setChecked(count, false);
                                    }

                                });
                            } else {
                                dhtmlx.message({text: "At least one employee status should be selected"});
                            }

                        }
                        if (name == "CancelLeaveType") {
                            ManageLeaveTypeForm.setItemValue("LT_Id", "");
                            ManageLeaveTypeForm.setItemValue("LT_Name", "");
                            ManageLeaveTypeForm.setItemValue("LT_MaxCount", "");
                            ManageLeaveTypeForm.setItemValue("LT_Status", "");
                            ManageLeaveTypeForm.checkItem("LT_LOP", "0");
                            for (count = 0; count < checkEmpStatus.length; count++) {
                                emp_StatusCombo.setChecked(count, false);
                            }
                        }
                    });
                });
                ManageLeaveTypeGrid = dhxManageLeaveTypeLayout.cells("b").attachGrid();
                ManageLeaveTypeGrid.enableCollSpan(true);
                ManageLeaveTypeGrid.enableTooltips("false,false,false,true");
                ManageLeaveTypeGrid.loadXML(preTally.Initialize.encryptURL("requisites/listManageLeaveType.php"), function (id) {
                    ManageLeaveTypeGrid.makeFilter("LMLeave", 3);
                });

                ManageLeaveTypeGrid.attachEvent("onRowSelect", function (id) {
                    ManageLeaveTypeForm.setItemValue("LT_Id", ManageLeaveTypeGrid.getUserData(id, "LT_Id"));
                    ManageLeaveTypeForm.setItemValue("OF_Id", ManageLeaveTypeGrid.getUserData(id, "OF_Id"));
                    ManageLeaveTypeForm.setItemValue("LT_Name", ManageLeaveTypeGrid.getUserData(id, "LT_Name"));
                    ManageLeaveTypeForm.setItemValue("LT_MaxCount", ManageLeaveTypeGrid.getUserData(id, "LT_MaxCount"));
                    ManageLeaveTypeForm.setItemValue("LT_LOP", ManageLeaveTypeGrid.getUserData(id, "LT_LOP"));
                    ManageLeaveTypeForm.setItemValue("LT_Status", ManageLeaveTypeGrid.getUserData(id, "LT_Status"));
                    ES_Status = ManageLeaveTypeGrid.getUserData(id, "ES_Status");
//                           if(ES_Status==""||ES_Status==null){
//                               for(count=0;count<checkEmpStatus.length;count++){
//                                        ManageLeaveTypeForm.uncheckItem(checkEmpStatus[count]);
//                                }
//                           }
//                           else{
                    ES_StatusCheckedArray = ES_Status.split(",");
                    for ($k = 0; $k < ES_StatusCheckedArray.length; $k++) {
                        ES_StatusCheckedSingleArray = ES_StatusCheckedArray[$k].split(":");
                        if (ES_StatusCheckedSingleArray[1] == 1) {
                            emp_StatusCombo.setChecked($k, true);
                        }
                        if (ES_StatusCheckedSingleArray[1] == 0) {
                            emp_StatusCombo.setChecked($k, false);
                        }

                    }
                    //                         }*/
                });

            } else {
                dhxMiddleBlockTabs.tabs("ManageLeaveType").setActive();
            }

        },
        menu_LateEntriesList: function () {
            if (!dhxMiddleBlockTabs.cells("ApproveLateEntry")) {
                dhxMiddleBlockTabs.addTab("ApproveLateEntry", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Approve Late Entry", 150);
                dhxMiddleBlockTabs.tabs("ApproveLateEntry").setActive();
                ptapproveToolbar = dhxMiddleBlockTabs.cells("ApproveLateEntry").attachToolbar();
                ptapproveToolbar.setIconsPath("images/icon/default_18/");
                ptapproveToolbar.addText("text_from", null, "From Date");
                ptapproveToolbar.addInput("applateentry_date_from", null, "", 75);
                ptapproveToolbar.addButton("applateentry_df_clear", null, "", "close.gif");
                ptapproveToolbar.addSeparator();
                ptapproveToolbar.addText("text_till", null, "To Date");
                ptapproveToolbar.addInput("applateentry_date_till", null, "", 75);
                ptapproveToolbar.addButton("applateentry_dt_clear", null, "", "close.gif");
                ptapproveToolbar.addSeparator();
                ptapproveToolbar.addButton("applateentry_date_filter", null, "Search", "save.gif");
                ptapproveToolbar.addText("spacer", '9', "");
                ptapproveToolbar.disableItem("spacer");
                ptapproveToolbar.setWidth("spacer", 100);
                // ptapproveToolbar.addButton("subord_LateEntry", null, "Report Late Entry for Subordinates", "save.gif");
                var applvTb_Inp_Frm = ptapproveToolbar.getInput("applateentry_date_from");
                applvTb_Inp_Frm.setAttribute("readOnly", "true");
                applvTb_Inp_Frm.onclick = function () {
                    if (ptapproveToolbar.getValue("applateentry_date_till"))
                        preTally.Settings.setSensitive(applvTb_Inp_Til, "max");
                }
                var applvTb_Inp_Til = ptapproveToolbar.getInput("applateentry_date_till");
                applvTb_Inp_Til.setAttribute("readOnly", "true");
                applvTb_Inp_Til.onclick = function () {
                    if (ptapproveToolbar.getValue("applateentry_date_from"))
                        preTally.Settings.setSensitive(applvTb_Inp_Frm, "min");
                }
                applvTb_Calendar = new dhtmlXCalendarObject([applvTb_Inp_Frm, applvTb_Inp_Til]);
                applvTb_Calendar.setDateFormat("%d.%m.%Y");
                // var ACLApproveLateEntry = unescape(JGG1P3bDnUSDL13Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                ptapproveToolbar.attachEvent("onClick", function (id) {
                    if (id == "applateentry_date_filter") {
                        preTally.Settings.applyLateEntryFilter();
                    }
                    if (id == "applateentry_df_clear") {
                        ptapproveToolbar.setValue('applateentry_date_from', '', false);
                    }
                    if (id == "applateentry_dt_clear") {
                        ptapproveToolbar.setValue('applateentry_date_till', '', false);
                    }

                });
                var dhxApproveLateEntryLayout = dhxMiddleBlockTabs.cells("ApproveLateEntry").attachLayout('1C');
                var LtAppTab = dhxApproveLateEntryLayout.cells("a").attachTabbar();
                LtAppTab.addTab('lt_approve', 'Late Entry Approval');
                LtAppTab.cells('lt_approve').setActive();
                var LtAppLayout = LtAppTab.cells('lt_approve').attachLayout("1C");
                ApproveLateEntryStbarPend = LtAppTab.cells("lt_approve").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='aprLateEntryAll_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='aprLateEntryAll_paging'></div>",
                    height: 30
                });
                LtAppLayout.cells('a').hideHeader();
                ApproveLateEntryGrid = LtAppLayout.cells("a").attachGrid();
                preTally.Settings.AllLateEntryGird();
            } else {
                dhxMiddleBlockTabs.tabs("ApproveLateEntry").setActive();
            }

        },
        applyLateEntryFilter: function (value, colNum, Ord) {
            if (value == 1) { // Notification section - LateEntry Application
                $('#statusALFLate').val(0);
            }

            var dateFilter = "&appFromDate=" + ptapproveToolbar.getValue("applateentry_date_from") + "&appToDate=" + ptapproveToolbar.getValue("applateentry_date_till");
            var filterValue = new Array($('#appliedByATF').val(), $('#branchATF').val(), $('#departmentATF').val(), $('#statusALFLate').val(), colNum, Ord);

            preTally.Settings.progressOn(true, dhxLayout, null);
            // console.log(filterValue);return false;
            ApproveLateEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listLateEntries.php&mode=All&filter=" + filterValue + dateFilter), function () {
                $('.aprLateEntryAll_cnt_tot').html("# : " + ApproveLateEntryGrid.getUserData("", "TL_Count"));
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        AllLateEntryGird: function () {
            ApproveLateEntryGrid.setHeader("SlNo,Applied By,Branch,Department,From Date,To Date,Total Days,Late Session,Late Duration,Applied On <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' colNum='2' class='btn_AppLtSort' style='float:right;' />,Reason,Verified By,Verified At,Remarks,Status");

            ApproveLateEntryGrid.setInitWidths("40,90,*,90,90,90,60,60,*,*,*,80,*,*,*");
            ApproveLateEntryGrid.setColAlign("center,left,left,left,left,left,left,left,left,left,left,left,left,left,center");
            ApproveLateEntryGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
            ApproveLateEntryGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ed,combo");

            ApproveLateEntryGrid.enableColSpan(true);
            ApproveLateEntryGrid.enableTooltips("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false");
            ApproveLateEntryGrid.enableEditEvents(true, true, true, true, true, true, true, true, true, true, true, true, true, true, true);

            ApproveLateEntryGrid.init();
            ApproveLateEntryGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
            ApproveLateEntryGrid.enablePaging(true, 50, 5, "aprLateEntryAll_paging", true);
            ApproveLateEntryGrid.setPagingSkin("toolbar", "dhx_skyblue");
            ApproveLateEntryGrid.setImagePath('assets/grid/codebase/imgs/');

            ApproveLateEntryGrid.attachHeader(
                "#rspan," +
                "<input type='text' class='appLt_text_filter' id='appliedByATF' style='width: 90%;' placeholder='Applied By'>," +
                "<input type='text' class='appLt_text_filter' id='branchATF' style='width: 90%;' placeholder='Branch'>," +
                "<input type='text' class='appLt_text_filter' id='departmentATF' style='width: 90%;' placeholder='Department'>," +
                "#rspan,#rspan,#rspan,#rspan," +
                "#rspan," +  // For Late Duration (index 8)
                "#rspan," + // Applied On (index 9)
                "#rspan,#rspan,#rspan,#rspan," +
                "<select style='width:90%;' class='appLtP_select_filter' id='statusALFLate'>" +
                    "<option value=''>All</option>" +
                    "<option value='pending'>Pending Approval</option>" +
                    "<option value='approved'>Approved</option>" +
                    "<option value='rejected'>Rejected</option>" +
                "</select>"
            );
            
            preTally.Settings.progressOn(true, dhxLayout, null);

            ApproveLateEntryGrid.attachEvent("onXLS", function () {
                preTally.Settings.progressOn(true, dhxLayout, null);
            });
            ApproveLateEntryGrid.attachEvent("onXLE", function () {
                preTally.Settings.progressOff(true, dhxLayout, null);
            });

            ApproveLateEntryGrid.loadXML(preTally.Initialize.encryptURL("requisites/listLateEntries.php"), function () {
                var filtrALInterval;
                $(".appLt_text_filter").keyup(function () {
                    if (filtrALInterval)
                        clearInterval(filtrALInterval);
                    filtrALInterval = setInterval(function () {
                        preTally.Settings.applyLateEntryFilter(null, 9, 'dsc');
                        clearInterval(filtrALInterval);
                    }, 500);
                });
                var appLvsrtFlg = 1
                $(".btn_AppLtSort").click(function () {

                    var ColNum = $(this).attr("colNum");
                    appLvSortdRow = ColNum;
                    var colimg;
                    if (appLvsrtFlg == 0) {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        appOrd = 'asc';
                        appLvsrtFlg = 1;

                    } else {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        appOrd = 'des';
                        appLvsrtFlg = 0;
                    }
                    preTally.Settings.applyLateEntryFilter(null, appLvSortdRow, appOrd);
                });
                $(".appLtP_select_filter").change(function () {
                    preTally.Settings.applyLateEntryFilter(null, 9, 'dsc');
                });
                comment = "";
                $('.aprLateEntryAll_cnt_tot').html("# : " + ApproveLateEntryGrid.getUserData("", "TL_Count"));
                let lateEntryPermission = ApproveLateEntryGrid.getUserData("", "LateEntryApprovePermission");
                ApproveLateEntryGrid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
                    // ======== For Combo Column (Status) ========
                    if (!lateEntryPermission && (cInd == 13 || cInd == 14)) {
                        return false;
                    }
                    if (cInd == 14) { // Combo column
                        if (stage == 0) {
                            // Capture existing value before editing
                            // status = 'approved';
                            // status = ApproveLateEntryGrid.getUserData(rId, "Att_Lt_Status");alert(status);
                            gridCombovalue = ApproveLateEntryGrid.cells(rId, cInd).getValue();
                            status = gridCombovalue.toLowerCase();;
                            cellLateEntryComboObject = ApproveLateEntryGrid.cells(rId, cInd).getCellCombo();
                            cellLateEntryComboObject.readonly(true);
                            preTally.Settings.selectLateEntryStatusOptions(status, gridCombovalue);
                        } else if (stage == 2) {
                            var newComboValue = ApproveLateEntryGrid.cells(rId, cInd).getValue();
                            var comboObj = ApproveLateEntryGrid.getColumnCombo(cInd); // Correct method
                            var newComboText = comboObj.getComboText(newComboValue);

                            // alert(newComboValue + '=>' + newComboText + '=>' + gridCombovalue);
                            if (newComboValue != null && newComboValue !== gridCombovalue) {
                                // alert(newComboValue+"=>"+gridCombovalue);
                                let newComboValueLabel = newComboValue.charAt(0).toUpperCase() + newComboValue.slice(1);
                                let gridCombovalueLabel = gridCombovalue.charAt(0).toUpperCase() + gridCombovalue.slice(1)
                                dhtmlx.confirm({
                                    title: "Confirm Change",
                                    type: "confirm-warning",
                                    ok: "Yes", cancel: "No",
                                    text: "Do you want to change the status from " + gridCombovalueLabel + " to " + newComboValueLabel + "?",
                                    callback: function (result) {
                                        if (result) {
                                            // preTally.Settings.progressOn(true, dhxLayout, null);
                                            var params = "Att_Lt_Id=" + rId + "&Att_Lt_Status=" + newComboValue;
                                            $.post(preTally.Initialize.encryptURL("warehouse/verifyLateEntry.php&" + params), function (response) {
                                                // alert('response received'); // This will now show
                                                console.log(response); // Optional, to inspect the full response

                                                var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;

                                                // alert(jsonResponse.status);
                                                if (jsonResponse.status) {
                                                    dhtmlx.message({ text: jsonResponse.message });
                                                    ApproveLateEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listLateEntries.php"));
                                                    preTally.Notification.countNotification();
                                                } else {
                                                    dhtmlx.message({ type: "error", text: jsonResponse.message });
                                                    ApproveLateEntryGrid.cells(rId, cInd).setValue(oValue);
                                                }
                                            });
                                        } else {
                                            ApproveLateEntryGrid.cells(rId, cInd).setValue(oValue);
                                        }
                                    }
                                });
                            }
                        }
                    }

                    // ======== For Editable Text Column (Remarks) ========
                    else if (cInd == 13) {
                        // Get the current status from column 14
                        let currentStatus = ApproveLateEntryGrid.cells(rId, 14).getValue().toLowerCase();
                        if (currentStatus !== 'pending approval') {
                            // Disallow editing
                            if (stage == 0) return false;
                        }
                        if (stage == 0) {
                            oldRemarks = ApproveLateEntryGrid.cells(rId, 13).getValue().trim();
                        } else if (stage == 2) {
                            var newRemarks = ApproveLateEntryGrid.cells(rId, 13).getValue().trim();
                            newRemarks = newRemarks.replace(/\s+/g, ' ');
                            if (newRemarks !== oldRemarks) {
                                // alert(newRemarks+"=>"+oldRemarks);
                                var params = "Att_Lt_Id=" + rId + "&Att_Lt_Verified_Remark=" + encodeURIComponent(newRemarks);
                                console.log(params);
                                $.post(preTally.Initialize.encryptURL("warehouse/verifyLateEntry.php&" + params), function (response) {
                                    // alert('response received'); // This will now show
                                    console.log(response); // Optional, to inspect the full response

                                    var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;

                                    // alert(jsonResponse.status);
                                    if (jsonResponse.status) {
                                        dhtmlx.message({ text: jsonResponse.message });
                                        ApproveLateEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listLateEntries.php"));
                                    } else {
                                        dhtmlx.message({ type: "error", text: jsonResponse.message });
                                        ApproveLateEntryGrid.cells(rId, cInd).setValue(oValue);
                                    }
                                });
                            }
                        }
                    }

                    return true;
                });
            });

            ApproveLateEntryGrid.attachEvent("onFilterEnd", function (elements) {
                $('.aprLateEntryAll_cnt_tot').html("# : " + ApproveLateEntryGrid.getUserData("", "TL_Count"));
                if (ApproveLateEntryGrid.getRowsNum() === 0) {
                    ApproveLateEntryGrid.addRow('0', "No records found", 0);
                    ApproveLateEntryGrid.setColspan('0', 0, 14);
                    ApproveLateEntryGrid.setRowTextStyle('0', "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");

                } else {
                    //ApproveLateEntryGrid.deleteRow(0);
                    // ApproveLateEntryGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"), function() {});
                }

            });

        },
        menu_ManageRule: function () {
            if (!dhxMiddleBlockTabs.cells("ManageRule")) {
                dhxMiddleBlockTabs.addTab("ManageRule", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Attendance Rule", 230);
                dhxMiddleBlockTabs.tabs("ManageRule").setActive();
                dhxManageRuleLayout = dhxMiddleBlockTabs.cells("ManageRule").attachLayout('3L');
                dhxManageRuleLayout.cells("a").setWidth(350);
                dhxManageRuleLayout.cells("a").setText("New Attendance Rule");
                dhxManageRuleLayout.cells("b").setText("Manage Attendance Rule");
                dhxManageRuleLayout.cells("c").setText("Sandwich Rules");
                ManageRuleForm = dhxManageRuleLayout.cells("a").attachForm();
                ManageRuleGrid = dhxManageRuleLayout.cells("b").attachGrid();
                ManageSandwichRuleGrid = dhxManageRuleLayout.cells("c").attachGrid();
                ManageRuleForm.loadStruct(preTally.Initialize.encryptURL("requisites/ManageRuleForm.php"), function () {
                    var todayd  = new Date(); //12-05-2025
                    todayd.setHours(0, 0, 0, 0);
                    ManageRuleForm.getCalendar("RL_Effective_Date").setSensitiveRange(todayd, null);
                    // for disable future dates too, then uncomment below line 
                    //ManageRuleForm.getCalendar("RL_Effective_Date").setSensitiveRange(todayd, new Date());
                    ManageRuleForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveRule") {
                            var isValid = ManageRuleForm.validate(); // Now the validators are already defined
                            if (isValid) {
                                ManageRuleForm.send(preTally.Initialize.encryptURL("warehouse/saveManageRule.php"), function (loader, response) {
                                    var json = JSON.parse(response);
                                    if(json.status==true){
                                        var grid = (json.rule_type == 1) ? ManageSandwichRuleGrid : ManageRuleGrid;
                                        grid.updateFromXML(preTally.Initialize.encryptURL("requisites/listManageRule.php&type=" + json.rule_type), true, true);
                                        ManageRuleForm.clear();
                                    }
                                    dhtmlx.message({ text: json.message });
                                });
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            }
                        }
                        if (name == "CancelRule") {
                            ManageRuleForm.clear();
                            var combo = ManageRuleForm.getCombo("RL_Leave_Duration");
                            combo.clearAll();
                            combo.addOption([
                                { text: 'Select Option', value: '' },
                            ]);
                            ManageRuleForm.setItemValue("RL_Leave_Duration", '');
                        }
                    });

                    ManageRuleForm.attachEvent("onChange", function (name, value) {
                        if (name === "RL_Is_Sandwich") {
                            if (value === "1") { // Yes
                                ManageRuleForm.showItem("RL_Sandwich_Type");
                                setTimeout(function () {
                                    var combo = ManageRuleForm.getCombo("RL_Sandwich_Type");
                                    if (combo && combo.DOMelem) {
                                        combo.DOMelem.style.width = "150px";

                                        // Also resize inner input if present
                                        var input = combo.DOMelem.querySelector("input");
                                        if (input) input.style.width = "198px";
                                    }

                                    // Bonus: force a redraw by resetting selection
                                    var selectedIndex = combo.getSelectedIndex();
                                    combo.selectOption(selectedIndex >= 0 ? selectedIndex : 0, false, true);
                                }, 50);
                                ManageRuleForm.setRequired("RL_Start_Day", false);
                                ManageRuleForm.setRequired("RL_End_Day", false);
                                ManageRuleForm.setRequired("RL_Leave_Duration", false);
                                // Sandwich type - hide start and end days
                                ManageRuleForm.hideItem("RL_Start_Day");
                                ManageRuleForm.hideItem("RL_End_Day");
                                ManageRuleForm.hideItem("RL_Leave_Duration");
                            } else { // No
                                ManageRuleForm.hideItem("RL_Sandwich_Type");
                                ManageRuleForm.setItemValue("RL_Sandwich_Type", ""); // Optional: Clear value when hidden

                                ManageRuleForm.setRequired("RL_Start_Day", true);
                                ManageRuleForm.setRequired("RL_End_Day", true);
                                ManageRuleForm.setRequired("RL_Leave_Duration", true);
                                // Normal type - show start and end days
                                ManageRuleForm.showItem("RL_Start_Day");
                                ManageRuleForm.showItem("RL_End_Day");
                                ManageRuleForm.showItem("RL_Leave_Duration");
                            }
                        }
                        if (name === "RL_End_Day" || name === "RL_Start_Day") {
                            var combo = ManageRuleForm.getCombo("RL_Leave_Duration");
                            combo.clearAll();
                            // Get values from both dropdowns
                            var startDayCombo = ManageRuleForm.getCombo("RL_Start_Day");
                            var endDayCombo = ManageRuleForm.getCombo("RL_End_Day");

                            // Get selected value (not text)
                            var startDayValue = ManageRuleForm.getItemValue("RL_Start_Day");
                            var endDayValue = ManageRuleForm.getItemValue("RL_End_Day");

                            // Get selected text (not value)
                            var startDay = startDayCombo.getSelectedText();
                            var endDay = endDayCombo.getSelectedText();
                            // Only proceed if both have values
                            if (startDayValue && endDayValue) {
                                console.log(ManageRuleForm.getCombo("RL_Leave_Duration"));
                                startDay = startDay.charAt(0).toUpperCase() + startDay.slice(1);
                                endDay = endDay.charAt(0).toUpperCase() + endDay.slice(1);
                                
                                var allCombined = startDay + " to " + endDay;
                                //var anyCombined = "Any day between " + startDay + " to " + endDay;
                                var anyCombined = "Any day";

                                // Re-add 'single' with new text but same value
                                combo.addOption([
                                    { text: anyCombined, value: 'single' },
                                    { text: allCombined, value: 'all' },
                                ]);
                            }
                        }
                    });
                });

                /****  Other rule grid ****/

                ManageRuleGrid.enableCollSpan(true);
                ManageRuleGrid.loadXML(preTally.Initialize.encryptURL("requisites/listManageRule.php&type=0"));
                /*ManageRuleGrid.attachEvent("onRowSelect", function (id) {
                    ManageRuleForm.setItemValue("RL_Id", ManageRuleGrid.getUserData(id, "RL_Id"));
                    ManageRuleForm.setItemValue("RL_Name", ManageRuleGrid.getUserData(id, "RL_Name"));
                    ManageRuleForm.setItemValue("RL_Description", ManageRuleGrid.getUserData(id, "RL_Description"));
                    ManageRuleForm.setItemValue("RL_Start_Day", ManageRuleGrid.getUserData(id, "RL_Start_Day"));
                    ManageRuleForm.setItemValue("RL_End_Day", ManageRuleGrid.getUserData(id, "RL_End_Day"));
                    ManageRuleForm.setItemValue("RL_Status", ManageRuleGrid.getUserData(id, "RL_Status"));
                    ManageRuleForm.showItem("RL_Start_Day");
                    ManageRuleForm.showItem("RL_End_Day");
                });*/

                /**** Sandwich leave grid ****/

                ManageSandwichRuleGrid.enableCollSpan(true);
                ManageSandwichRuleGrid.loadXML(preTally.Initialize.encryptURL("requisites/listManageRule.php&type=1"));
                /*ManageSandwichRuleGrid.attachEvent("onRowSelect", function (id) {
                    ManageRuleForm.setItemValue("RL_Id", ManageSandwichRuleGrid.getUserData(id, "RL_Id"));
                    ManageRuleForm.setItemValue("RL_Name", ManageSandwichRuleGrid.getUserData(id, "RL_Name"));
                    ManageRuleForm.setItemValue("RL_Description", ManageSandwichRuleGrid.getUserData(id, "RL_Description"));
                    ManageRuleForm.setItemValue("RL_Status", ManageSandwichRuleGrid.getUserData(id, "RL_Status"));
                    ManageRuleForm.hideItem("RL_Start_Day");
                    ManageRuleForm.hideItem("RL_End_Day");
                });*/
            } else {
                dhxMiddleBlockTabs.tabs("ManageRule").setActive();
            }
        },
        editRule: function (elem, id, type) {
            if (!ManageRuleForm) return; // safety check

            var grid = (type == 1) ? ManageSandwichRuleGrid : ManageRuleGrid;

            // Now fetch the values from grid and set to form
            ManageRuleForm.setItemValue("RL_Id", grid.getUserData(id, "RL_Id"));
            ManageRuleForm.setItemValue("RL_Name", grid.getUserData(id, "RL_Name"));
            ManageRuleForm.setItemValue("RL_Description", grid.getUserData(id, "RL_Description"));
            ManageRuleForm.setItemValue("RL_Is_Sandwich", type);
            ManageRuleForm.setItemValue("RL_Leave_Duration", grid.getUserData(id, "RL_Leave_Duration"));
            ManageRuleForm.setItemValue("RL_Status", grid.getUserData(id, "RL_Status"));
            ManageRuleForm.setItemValue("RL_Is_LOP", grid.getUserData(id, "RL_Is_LOP"));
            ManageRuleForm.setItemValue("RL_Except_Dept_office", grid.getUserData(id, "RL_Except_Dept_office"));
            ManageRuleForm.setItemValue("RL_Effective_Date", new Date());
            ManageRuleForm.hideItem("RL_Is_Sandwich");
            if (type == 1) {
                ManageRuleForm.setRequired("RL_Start_Day", false);
                ManageRuleForm.setRequired("RL_End_Day", false);
                ManageRuleForm.setRequired("RL_Sandwich_Type", true);
                ManageRuleForm.setRequired("RL_Leave_Duration", false);
                // Sandwich type - hide start and end days
                ManageRuleForm.hideItem("RL_Start_Day");
                ManageRuleForm.hideItem("RL_End_Day");
                ManageRuleForm.hideItem("RL_Leave_Duration");
                ManageRuleForm.showItem("RL_Sandwich_Type");
                ManageRuleForm.setItemValue("RL_Sandwich_Type", grid.getUserData(id, "RL_Sandwich_Type"));
                setTimeout(function () {
                    var combo = ManageRuleForm.getCombo("RL_Sandwich_Type");
                    if (combo && combo.DOMelem) {
                        combo.DOMelem.style.width = "150px";

                        // Also resize inner input if present
                        var input = combo.DOMelem.querySelector("input");
                        if (input) input.style.width = "198px";
                    }

                    // Bonus: force a redraw by resetting selection
                    var selectedIndex = combo.getSelectedIndex();
                    combo.selectOption(selectedIndex >= 0 ? selectedIndex : 0, false, true);
                }, 50);
            } else {
                ManageRuleForm.setRequired("RL_Start_Day", true);
                ManageRuleForm.setRequired("RL_End_Day", true);
                ManageRuleForm.setRequired("RL_Sandwich_Type", false);
                // Normal type - show start and end days
                ManageRuleForm.showItem("RL_Start_Day");
                ManageRuleForm.showItem("RL_End_Day");
                ManageRuleForm.showItem("RL_Leave_Duration");
                ManageRuleForm.setItemValue("RL_Start_Day", grid.getUserData(id, "RL_Start_Day"));
                ManageRuleForm.setItemValue("RL_End_Day", grid.getUserData(id, "RL_End_Day"));
                ManageRuleForm.hideItem("RL_Sandwich_Type");
                var combo = ManageRuleForm.getCombo("RL_Leave_Duration");
                combo.clearAll();
                // Get values from both dropdowns
                var startDayCombo = ManageRuleForm.getCombo("RL_Start_Day");
                var endDayCombo = ManageRuleForm.getCombo("RL_End_Day");

                // Get selected value (not text)
                var startDayValue = ManageRuleForm.getItemValue("RL_Start_Day");
                var endDayValue = ManageRuleForm.getItemValue("RL_End_Day");

                // Get selected text (not value)
                var startDay = startDayCombo.getSelectedText();
                var endDay = endDayCombo.getSelectedText();
                // Only proceed if both have values
                if (startDayValue && endDayValue) {
                    console.log(ManageRuleForm.getCombo("RL_Leave_Duration"));
                    startDay = startDay.charAt(0).toUpperCase() + startDay.slice(1);
                    endDay = endDay.charAt(0).toUpperCase() + endDay.slice(1);
                    
                    var allCombined = startDay + " and " + endDay;
                    //var anyCombined = "Any day between " + startDay + " to " + endDay;
                    var anyCombined = "Any day";

                    // Re-add 'single' with new text but same value
                    combo.addOption([
                        { text: anyCombined, value: 'single' },
                        { text: allCombined, value: 'all' },
                    ]);
                }
                ManageRuleForm.setItemValue("RL_Leave_Duration", grid.getUserData(id, "RL_Leave_Duration"));
            }
        },
        menu_Adj_Attendance: function () {
            if (!dhxMiddleBlockTabs.cells("Adj_Attendance")) {
                dhxMiddleBlockTabs.addTab("Adj_Attendance", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Clear Attendance", 230);
                dhxMiddleBlockTabs.tabs("Adj_Attendance").setActive();
                dhxAdj_AttendanceLayout = dhxMiddleBlockTabs.cells("Adj_Attendance").attachLayout('2U');
                dhxAdj_AttendanceLayout.cells("a").setWidth(400);
                dhxAdj_AttendanceLayout.cells("a").setText("Adjust Attendance");
                dhxAdj_AttendanceLayout.cells("b").setText("Adjusted Attendance List");

                expCntrlToolbar = dhxAdj_AttendanceLayout.cells("b").attachToolbar();                
                expCntrlToolbar.setIconsPath("images/icon/default_18/");                
                
                var Months_Options = [];
                var Years_Options = [];

                var monthNames = [ 
                    "January", "February", "March","April", "May", "June",
                    "July", "August", "September","October", "November", "December"
                ];
                //Date.today().getMonth()+1
                for (i = 12; i >=1 ; i--) {
                    j = i;    
                    if(j<10){
                        j='0'+i;
                    } 
                        Months_Options.push(['m'+j,'obj',monthNames[i-1],"calendar_M.png"]);
                }
                
                for (i = Date.today().getFullYear(); i >= 2024 ; i--) {
                        Years_Options.push([i,'obj',i,"calendar_Y.png"]);
                }
                expCntrlToolbar.addButtonSelect( "rpt_year_filter",'1',"Select Year",Years_Options, 'calendar_M.png','', true, true, 10, 'select');
                expCntrlToolbar.addButtonSelect( "rpt_month_filter",'1',"Select Month",Months_Options, 'calendar_Y.png','', true, true, 10, 'select');                
               
                var currMonth = new Date().getMonth() + 1;
                var currYear = new Date().getFullYear();

                // Pad month to 'm01'... 'm12'
                var currMonthId = 'm' + (currMonth < 10 ? '0' + currMonth : currMonth);

                // Set default selection for month
                var currMonthText = monthNames[currMonth - 1];
                expCntrlToolbar.setItemText("rpt_month_filter", currMonthText);
                expCntrlToolbar.setValue("rpt_month_filter", currMonthId);

                // Set default selection for year
                expCntrlToolbar.setItemText("rpt_year_filter", currYear);
                expCntrlToolbar.setValue("rpt_year_filter", currYear);
               
                expCntrlToolbar.addSeparator();

                expCntrlToolbar.attachEvent("onClick", function(id){
                    var pId = expCntrlToolbar.getParentId(id);
                    if (pId == 'rpt_month_filter') {
                        // id = 'm01', 'm02', ..., so extract display text
                        var selectedText = expCntrlToolbar.getListOptionText(pId, id);
                        expCntrlToolbar.setItemText(pId, selectedText); // Sets visible text
                        expCntrlToolbar.setValue(pId, id);              // Sets internal value

                        preTally.Settings.applyAttendanceAdjFilter();
                    }

                    if (pId == 'rpt_year_filter') {
                        var selectedText = expCntrlToolbar.getListOptionText(pId, id);
                        expCntrlToolbar.setItemText(pId, selectedText);
                        expCntrlToolbar.setValue(pId, id);

                        preTally.Settings.applyAttendanceAdjFilter();
                    }
                });

                dhxAdj_AttendanceLayout.cells("b").attachStatusBar({
                    text  : '<div class="expCntrl_cnt_tot_att" style="width:100px !important"># : 0</div><div id="expCntrl_paging_att"></div>',
                    height: 35
                }); 
                Adj_AttendanceForm = dhxAdj_AttendanceLayout.cells("a").attachForm();
                Adj_AttendanceGrid = dhxAdj_AttendanceLayout.cells("b").attachGrid();
                Adj_AttendanceGrid.setHeader("Sl No,User &nbsp <img src='images/icon/sort-descending-icon.png' title='Click here to Sort' colNum='2' class='btn_Sort_Exp'/>,Branch &nbsp <img src='images/icon/sort-descending-icon.png' title='Click here to Sort' colNum='3' class='btn_Sort_Exp'/>,Attendance Date &nbsp <img src='images/icon/sort-descending-icon.png' title='Click here to Sort' colNum='4' class='btn_Sort_Exp'/>, Attendance Type, Remarks, Created On, Created By, Edit");   
                Adj_AttendanceGrid.setColAlign("center,left,left,left,left,left,left,left")  
                Adj_AttendanceGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
                Adj_AttendanceGrid.setInitWidths("50,150,200,120,100,*,100,100,50");
                Adj_AttendanceGrid.setColumnMinWidth("40,100,150,80,80,300,75,80,30");
                Adj_AttendanceGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                Adj_AttendanceGrid.enableColSpan(true);
                Adj_AttendanceGrid.setColSorting("na,na,na,na,na,na,na,na,na");
                // Adj_AttendanceGrid.setImagePath("assets/grid/codebase/imgs/"); 
                // Adj_AttendanceGrid.setSkin("dhx_skyblue");
                Adj_AttendanceGrid.init();
                Adj_AttendanceGrid.setPagingWTMode(true,false,true,[15,30,50,80]);
                Adj_AttendanceGrid.enablePaging(true,50,5,"expCntrl_paging_att",true);
                Adj_AttendanceGrid.setPagingSkin("toolbar", "dhx_skyblue");
                var srtFlg = 0;
                colId = '';
                $('.btn_Sort_Exp').click(function() { 
                    colId = $(this).attr("colNum");
                    if(srtFlg == 0){
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        preTally.Settings.applyAttendanceAdjFilter(srtFlg); 
                        srtFlg = 1;
                    }else{
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        preTally.Settings.applyAttendanceAdjFilter(srtFlg); 
                        srtFlg = 0;
                    }
                });


                Adj_AttendanceForm.loadStruct(preTally.Initialize.encryptURL("requisites/ManageAttendanceAdjustmentForm.php"), function () {
                    var todayd = new Date();
                    todayd.setHours(0, 0, 0, 0); // normalize today's date

                    // Calculate date 30 days before today
                    var pastDate = new Date(todayd);
                    pastDate.setDate(todayd.getDate() - 30);

                    // Set yesterday as upper limit (excludes today)
                    var yesterday = new Date(todayd);
                    yesterday.setDate(todayd.getDate() - 1);

                    // Set sensitive range: from 30 days ago to today (no future dates allowed)
                    Adj_AttendanceForm.getCalendar("Att_Adj_Date").setSensitiveRange(pastDate, yesterday);
                    Adj_AttendanceForm.attachEvent("onButtonClick", function (name) {
                        if (name == "saveAttendanceAdj") {
                            var remark = Adj_AttendanceForm.getItemValue("Att_Adj_Remarks");
                            // Allow letters, numbers, space, ', ", /, ., and , and :
                            /*var isRemarkValid = /^[a-zA-Z0-9 "'\/.,:-]+$/.test(remark);
                            if (!isRemarkValid) {
                                alert("Remarks contain invalid characters.\nOnly letters, numbers, spaces, quotes ('\"), slashes (/), periods (.), and commas (,) are allowed.");
                                return false; // Stop submission
                            }*/
                            var isValid = Adj_AttendanceForm.validate(); // Now the validators are already defined
                            if (isValid) {
                                dhtmlx.confirm({
                                    ok: "Yes, Confirm", cancel: "Cancel",
                                    title: "Confirm the submission ?",
                                    text: "This entry can't be undone. Please verify before submission.",
                                    callback: function (status) {
                                        if (status == true) {
                                            Adj_AttendanceForm.send(preTally.Initialize.encryptURL("warehouse/saveManageAttendanceAdjustment.php"), function (loader, response) {
                                                var json = JSON.parse(response);
                                                if(json.status==true){
                                                    var grid = Adj_AttendanceGrid;
                                                    grid.clearAll();
                                                    grid.attachEvent("onXLE", function() {
                                                        // Only fire once after load
                                                        this.sortRows(6, "str", "desc"); // sort if needed
                                                        $('.expCntrl_cnt_tot_att').html("# Total : " + this.getUserData("", "TL_Count") + " ");
                                                        this.detachEvent("onXLE"); // prevent duplicate triggers
                                                    });
                                                    grid.load(preTally.Initialize.encryptURL("requisites/listManageAttendanceAdjustment.php"));
                                                    Adj_AttendanceForm.clear();
                                                    dhtmlx.message({ text: json.message });
                                                    Adj_AttendanceForm.clear();
                                                }else{
                                                    dhtmlx.message({ type: "error", text: json.message });
                                                }
                                            });
                                        } else if (status == false)
                                        {
                                            return false;
                                        }
                                    }
                                });
                            } else {
                                dhtmlx.message({ type: "error", text: "Please correct the highlighted fields." });
                            }
                        }
                        if (name == "CancelAttendanceAdj") {
                            Adj_AttendanceForm.clear();
                        }
                    });
                    document.querySelector('input[name="Att_Adj_Date"]').setAttribute('placeholder', 'Select Date');
                    // Delay combo setup just slightly to allow rendering
                    setTimeout(function () {
                        $('.expCntrl_cnt_tot_att').html("# Total : "+Adj_AttendanceGrid.getUserData("", "TL_Count")+" ");
                        var combo = Adj_AttendanceForm.getCombo("Att_Adj_US_Id");
                        if (combo) {
                            combo.load(preTally.Initialize.encryptURL("requisites/personsWithBranch.php"), function () {
                                combo.setPlaceholder("Users");
                                preTally.Settings.applyFilterHandler(combo);
                            });
                        } else {
                            console.error("Combo 'Att_Adj_US_Id' not found.");
                        }
                    }, 50); // 50ms delay usually enough
                });
                Adj_AttendanceGrid.enableCollSpan(true);
                Adj_AttendanceGrid.loadXML(preTally.Initialize.encryptURL("requisites/listManageAttendanceAdjustment.php"));
            } else {
                dhxMiddleBlockTabs.tabs("Adj_Attendance").setActive();
            }
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
        applyAttendanceAdjFilter:function (srtFlg){   
            var filterValue = new Array(srtFlg,colId);            
            // console.log("Selected Month ID:", expCntrlToolbar.getValue("rpt_month_filter"));
            // console.log("Selected Month Label:", expCntrlToolbar.getItemText("rpt_month_filter"));  
            // console.log("Selected Year Label:", expCntrlToolbar.getItemText("rpt_year_filter"));     
            expCntrlFilterParams = '&rpt_month_filter='+expCntrlToolbar.getItemText("rpt_month_filter")+'&rpt_year_filter='+expCntrlToolbar.getItemText("rpt_year_filter");
            Adj_AttendanceGrid.clearAll();
            preTally.Settings.progressOn(true, dhxLayout, null);            
            Adj_AttendanceGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listManageAttendanceAdjustment.php&filter="+filterValue+expCntrlFilterParams), function() {
                $('.expCntrl_cnt_tot_att').html("# Total : "+Adj_AttendanceGrid.getUserData("", "TL_Count")+" ");
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        editAdjAttendance: function (elem, id) {
            if (!Adj_AttendanceForm) return; // safety check

            var grid = Adj_AttendanceGrid;
            // alert("user ID => " + grid.getUserData(id, "Att_Adj_US_Id"));
            // Now fetch the values from grid and set to form
            Adj_AttendanceForm.setItemValue("Att_Adj_Id", grid.getUserData(id, "Att_Adj_Id"));
            Adj_AttendanceForm.setItemValue("Att_Adj_US_Id", grid.getUserData(id, "Att_Adj_US_Id"));
            Adj_AttendanceForm.setItemValue("Att_Adj_Date", grid.getUserData(id, "Att_Adj_Date"));
            Adj_AttendanceForm.setItemValue("Att_Adj_Type", grid.getUserData(id, "Att_Adj_Type"));
            Adj_AttendanceForm.setItemValue("Att_Adj_Remarks", grid.getUserData(id, "Att_Adj_Remarks"));
            Adj_AttendanceForm.setItemValue("Att_Adj_Status", grid.getUserData(id, "Att_Adj_Status"));
        },
        leaveReports: function (rowId) {
            if (!dhxMiddleBlockTabs.cells("LeaveReports")) {
                dhxMiddleBlockTabs.addTab("LeaveReports", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Leave Type", 170);
                dhxMiddleBlockTabs.tabs("LeaveReports").setActive();
                LeaveRptGrid = dhxMiddleBlockTabs.cells("LeaveReports").attachGrid();
                LeaveRptGrid.load(preTally.Initialize.encryptURL("requisites/rptLeaveReport.php"));
            } else {
                dhxMiddleBlockTabs.tabs("LeaveReports").setActive();
            }
        },
        setSens: function (inp, k) {
            if (k == "min") {
                ptRpTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                ptRpTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        setSensitive: function (inp, k) {
            if (k == "min") {
                applvTb_Calendar.setSensitiveRange(inp.value, null);
            } else {
                applvTb_Calendar.setSensitiveRange(null, inp.value);
            }
        },
        showSrchDetailData: function (inp, BS_Id, SH_Id) {
            // preTally.Settings.progressOn(true, dhxLayout, null);
            if (!SearchPop) {
                SearchPop = new dhtmlXPopup({mode: "left"});

            }
            if (SearchPop.isVisible()) {
                SearchPop.hide();
            }

            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;
            var rptDetailsPop = SearchPop.attachForm();
            //rptDetailsPop.style.backgroundColor="red";
            var params = "SHID=" + SH_Id + "&BSID=" + BS_Id
            rptDetailsPop.loadStruct(preTally.Initialize.encryptURL("requisites/report_iePop.php&" + params), function () {
                SearchPop.show(x, y, w, h);
                //preTally.Settings.progressOff(true, dhxLayout, null);
                var column0 = rptDetailsPop.getColumnNode("fieldsetname", 0);
                var column1 = rptDetailsPop.getColumnNode("fieldsetname", 1)
                column0.style.borderRight = "1px solid #a4bed4";
                //column1.style.paddingLeft = "6px";
                //console.log(column0);


            });
            $(".target_" + BS_Id).unbind();
        },
        hideSrchDetailData: function () {
            if (SearchPop.isVisible()) {
                SearchPop.hide();
            }
        },
        aboutPretally: function () {
            if (!dhxMiddleBlockTabs.cells("aboutPretally"))
            {
                dhxMiddleBlockTabs.addTab("aboutPretally", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;About Pretally", 130);
                dhxMiddleBlockTabs.tabs("aboutPretally").setActive();
                aboutForm = dhxMiddleBlockTabs.cells('aboutPretally').attachURL("aboutPretally.html");
            } else {
                dhxMiddleBlockTabs.tabs("aboutPretally").setActive();
            }
        },
        reportBug: function (inp) {
            dhxBugWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            bugWin = dhxBugWin.createWindow("wins_bug", x, y, 700, 500);
            bugWin.button("minmax1").hide();
            bugWin.button("minmax2").hide();
            bugWin.button("park").hide();
            bugWin.center();
            bugWin.setModal(true);
            bugWin.setText("Report a Problem");
            reportBugForm = bugWin.attachForm();
            reportBugForm.loadStruct(preTally.Initialize.encryptURL('requisites/reportBug.php'), function () {
                reportBugForm.attachEvent("onButtonClick", function (Name) {
                    if (Name == 'ReprtBugSend') {
                        var newBugReport = reportBugForm.validate();
                        if (newBugReport) {
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            reportBugForm.send(preTally.Initialize.encryptURL('warehouse/newBugReport.php'), function (loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if (response != 'fail') {
                                    reportBugForm.resetValidateCss();
                                    reportBugForm.clear();
                                } else {
                                    response = 'Report Problem Sending Failed';
                                }
                                dhtmlx.message({text: response});
                                reportBugForm.clear();
                                preTally.Settings.hideBugWin();
                            });
                        }
                    } else {
                        reportBugForm.resetValidateCss();
                        reportBugForm.clear();
                        bugWin.close();

                    }

                });
            });
        },
        hideBugWin: function () {
            dhxBugWin.window("wins_bug").close();
        },
        showCommentWin: function (inp, BR_Id) {

            dhxCommentWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            commentWin = dhxCommentWin.createWindow("wins_" + BR_Id, x, y, 700, 500);
            commentWin.center();
            commentWin.button("minmax1").hide();
            commentWin.button("minmax2").hide();
            commentWin.button("park").hide();
            commentWin.setModal(true);
            commentWin.setText("Add Comment on Bug-" + BugGrid.cells(BR_Id, 2).getValue());
            commentForm = commentWin.attachForm();
            commentForm.loadStruct(preTally.Initialize.encryptURL("requisites/bug_comment.php&bgid=" + BR_Id), function () {
                commentForm.attachEvent("onButtonClick", function (name) {
                    if (name == "newCommentSave")
                    {
                        commentForm.send(preTally.Initialize.encryptURL("warehouse/updateComment.php"), function (loader, response)
                        {
                            filterValue = '';
                            currentBugPage = BugGrid.currentPage;
                            filterValue += '&filter=' + new Array($('#bugMonthFilter').val() + "--" + $("#bugFilter").val() + "--" + $("#bugStatusFilter").val());
                            BugGrid.clearAndLoad(preTally.Initialize.encryptURL('requisites/listBugs.php' + filterValue), function () {
                                BugGrid.changePage(currentBugPage);
                                $('.feedback_cnt_tot').html("# : " + BugGrid.getUserData("", "TL_Count") + " ");
//                                $('#bugMonthFilter').val('');
//                                $("#bugFilter").val('');
                                dhtmlx.message({text: response});
                                preTally.Settings.hideCommentWin(BR_Id);
                            });
                        });

                    } else
                    {
                        preTally.Settings.hideCommentWin(BR_Id);
                    }

                })
            });
        },
        leaveList: function (LR_Id, status, comment, Gridparams) {
            params = "LR_Id=" + LR_Id + "&status=" + status + "&comment=" + comment;
            preTally.Settings.progressOn(true, dhxLayout, null);
            $.post(preTally.Initialize.encryptURL("warehouse/leaveApprove.php&" + params), function (data) {
                preTally.Settings.progressOff(true, dhxLayout, null);
                preTally.Settings.applyPendingLeaveFilter();
//                    ApproveLeaveGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listApproveLeave.php"),true,true,function(){preTally.Settings.progressOff(true, dhxLayout, null);});
//                    dhtmlx.message({text: data});
//                    return 1;

            });
        },
        menu_Employee_Status: function () {


        },
        hideCommentWin: function (BR_Id) {
            dhxCommentWin.window("wins_" + BR_Id).close();
        },
        contactUs: function () {
            if (!dhxMiddleBlockTabs.cells("contactUs"))
            {
                dhxMiddleBlockTabs.addTab("contactUs", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Contact Us", 130);
                dhxMiddleBlockTabs.tabs("contactUs").setActive();
                contactForm = dhxMiddleBlockTabs.cells('contactUs').attachForm();
                contactForm.loadStruct(preTally.Initialize.encryptURL('requisites/contactUs.php'));

            } else {
                dhxMiddleBlockTabs.tabs("contactUs").setActive();
            }
        },
        adminDashboard: function () {
            if (!dhxMiddleBlockTabs.cells("adminDashboard")) {
                dhxMiddleBlockTabs.addTab("adminDashboard", "<img src='images/icon/home_12.gif' style='margin-top:2px;' />&nbsp;&nbsp;Dashboard", 130);
                dhxMiddleBlockTabs.tabs("adminDashboard").setActive();
                DashboardForm = dhxMiddleBlockTabs.cells('adminDashboard').attachForm();
                DashboardForm.loadStruct(preTally.Initialize.encryptURL('requisites/admin_launch.php'));

            } else {
                dhxMiddleBlockTabs.tabs("adminDashboard").setActive();
            }
        },
        adminBugReports: function () {
            if (!dhxMiddleBlockTabs.cells("adminBugReports")) {
                dhxMiddleBlockTabs.addTab("adminBugReports", "<img src='images/icon/error_12.png' style='margin-top:2px;' />&nbsp;&nbsp; Feedback Report&nbsp; <img src='images/icon/refresh-icon.png' style='margin-top:2px;' class='feedbackRfsh'/>", 200);

                dhxMiddleBlockTabs.tabs("adminBugReports").setActive();

                dhxMiddleBlockTabs.cells('adminBugReports').attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='feedback_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='feedback_paging'></div>",
                    height: 30
                });
                BugGrid = dhxMiddleBlockTabs.cells('adminBugReports').attachGrid();
                BugGrid.setHeader("SlNo,,Subject,Bug,Name,Phone,Status,Date,Add Updations");
                BugGrid.setInitWidths("40,40,*,150,150,150,200,150,75");
                BugGrid.setColAlign("left,left,left,left,left,left,left,left,center");
                BugGrid.setColTypes("ro,sub_row,ro,ro,ro,ro,ro,ro,ro");
                BugGrid.setPagingWTMode(true, false, true, [15, 30, 50]);
                BugGrid.enablePaging(true, 50, 5, "feedback_paging", true);
                BugGrid.setPagingSkin("toolbar", "dhx_skyblue");
                BugGrid.attachHeader(',,,<select style="width:130px; font-size:8pt; font-family:Tahoma;" id="bugFilter" onChange="preTally.Settings.applyFilterBug()"><option value="">All</option><option value="2">Technical</option><option value="1">Accounts</option><option value="3">Others</option></select>,,,<select style="width:130px; font-size:8pt; font-family:Tahoma;" id="bugStatusFilter" onChange="preTally.Settings.applyFilterBug()"><option value="">All</option><option value="1">New</option><option value="2">Read</option><option value="3">Under Process</option><option value="4">Completed</option></select>,<select style="width:130px; font-size:8pt; font-family:Tahoma;" id="bugMonthFilter" onChange="preTally.Settings.applyFilterBug()"><option value="">All</option><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select>,');
                BugGrid.setSkin('dhx_skyblue');
                BugGrid.enableColSpan(true);
                BugGrid.setImagePath('assets/grid/codebase/imgs/');
                BugGrid.init();
                var rowId = 0;
                BugGrid.enableTooltips("false,false,false,false,false,false,false,false,false");
                BugGrid.loadXML(preTally.Initialize.encryptURL('requisites/listBugs.php'), function () {
                    $('.feedback_cnt_tot').html("# : " + BugGrid.getUserData("", "TL_Count") + " ");
                    var flg = [];
                    flg[rowId] = 0;
                    BugGrid.attachEvent("onRowSelect", function (id) {
                        rowId = id;
                        if (id != 0) {
                            if (flg[rowId] == 1) {
                                BugGrid.cells(rowId, 1).close();
                                flg[rowId] = 0;
                            } else {
                                BugGrid.cells(rowId, 1).open();
                                flg[rowId] = 1;
                            }
                        }
                    });
                    BugGrid.attachEvent("onSubRowOpen", function (id) {
                        flg[rowId] = 1;

                    });
                });
                $(".feedbackRfsh").click(function () {
                    BugGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBugs.php"), function () {
                        $('.feedback_cnt_tot').html("# : " + BugGrid.getUserData("", "TL_Count") + " ");
                        $('#bugMonthFilter').val('');
                        $("#bugFilter").val('');
                        $("#bugStatusFilter").val('');
                    });
                });

            } else {
                dhxMiddleBlockTabs.tabs("adminBugReports").setActive();
            }
        },
        sendMessage: function (inp) {
            dhxMessageWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            messageWin = dhxMessageWin.createWindow("wins_msg", x, y, 570, 500);
            messageWin.button("minmax1").hide();
            messageWin.button("minmax2").hide();
            messageWin.button("park").hide();
            messageWin.center();
            messageWin.setModal(true);
            messageWin.setText("Send a Message");
            sendMessageForm = messageWin.attachForm();
            preTally.Settings.progressOn(true, messageWin, null);

            sendMessageForm.loadStruct(preTally.Initialize.encryptURL('requisites/sendMessageFormXML.php'), function () {
                preTally.Settings.progressOff(true, messageWin, null);
                sendMessageForm.attachEvent("onButtonClick", function (Name) {
                    if (Name == 'SendMsgBtn') {
                        sendMessageForm.enableLiveValidation(true);
                        var messageValidate = sendMessageForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, messageWin, null);
                            sendMessageForm.send(preTally.Initialize.encryptURL("warehouse/sendMessage.php"), function (loader, response) {
                                preTally.Settings.progressOff(true, messageWin, null);
                                if (response == "success") {
                                    sendMessageForm.resetValidateCss();
                                    sendMessageForm.clear();
                                    dhtmlx.message({text: "Successfully send message"});
                                    preTally.Settings.hideMessageWin();
                                } else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else if (Name == 'AddRecepientsBtn') {

                        recipientWin = dhxMessageWin.createWindow("wins_recp", x, y, 1050, 350);
                        recipientWin.button("minmax1").hide();
                        recipientWin.button("minmax2").hide();
                        recipientWin.button("park").hide();
                        recipientWin.center();
                        recipientWin.setModal(true);
                        recipientWin.setText("Add Recipients");
                        listPreTallyUserGrid = recipientWin.attachGrid();
                        listPreTallyUserGrid.enableAutoWidth(true);
                        preTally.Settings.progressOn(true, recipientWin, null);
                        var listRecipientsXML = dhx4.ajax.postSync(preTally.Initialize.encryptURL("requisites/listRecipients.php"), "checked=" + sendMessageForm.getItemValue('idUserData'));
                        listPreTallyUserGrid.parse(listRecipientsXML.xmlDoc.responseText, function () {
                            preTally.Settings.progressOff(true, recipientWin, null);
                        });
                        recipientWin.attachStatusBar({
                            text: "<input type='button' value='SAVE' onclick='preTally.Settings.addRecipients();' style='margin:5px 10px 5px 0; float:right;' />",
                            height: 35
                        });

                    } else {
                        sendMessageForm.resetValidateCss();
                        sendMessageForm.clear();
                        preTally.Settings.hideMessageWin();
                    }
                });
            });
        },
        hideMessageWin: function () {
            dhxMessageWin.window("wins_msg").close();
        },
        sendReply: function (inp, MSG_Id, From_Id, Subject) {

            dhxReplyWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            replyWin = dhxReplyWin.createWindow("wins_" + MSG_Id, x, y, 800, 400);
            replyWin.center();
            replyWin.button("minmax1").hide();
            replyWin.button("minmax2").hide();
            replyWin.button("park").hide();
            replyWin.setModal(true);
            replyWin.setText("Reply for " + Subject);
            replyForm = replyWin.attachForm();
            preTally.Settings.progressOn(true, replyWin, null);
            var params = "msg_id=" + MSG_Id + "&from_id=" + From_Id;
            replyForm.loadStruct(preTally.Initialize.encryptURL("requisites/messageReplyForm.php&" + params), function () {
                preTally.Settings.progressOff(true, replyWin, null);
                replyForm.attachEvent("onButtonClick", function (name) {
                    if (name == "replySave") {
                        replyForm.enableLiveValidation(true);
                        var messageValidate = replyForm.validate();
                        if (messageValidate) {
                            preTally.Settings.progressOn(true, replyWin, null);
                            replyForm.send(preTally.Initialize.encryptURL("warehouse/sendReply.php"), function (loader, response) {
                                preTally.Settings.progressOff(true, replyWin, null);
                                if (response == "success") {
                                    dhtmlx.message({text: "Successfully send your message"});
                                    replyForm.clear();
                                    preTally.Settings.hideReplyWin(MSG_Id);
                                } else {
                                    dhtmlx.message({text: "Some error has occured."});
                                }
                            });
                        }
                    } else {
                        preTally.Settings.hideReplyWin(MSG_Id);
                    }
                });
            });
        },
        hideReplyWin: function (MSG_Id) {
            dhxReplyWin.window("wins_" + MSG_Id).close();
        },
        addRecipients: function () {
            var userDetails = [];
            var userIds = [];

            listPreTallyUserGrid.forEachRow(function (rId) {
                if (listPreTallyUserGrid.cells(rId, 6).getValue() == 1) { // if checked
                    userDetails.push(listPreTallyUserGrid.cells(rId, 2).getValue());
                    userIds.push(rId);
                }
            });

            sendMessageForm.setItemValue("MSG_To", userDetails);
            sendMessageForm.setItemValue('idUserData', userIds);
            dhxMessageWin.window("wins_recp").close();

        },
        viewMsgRecipients: function (inp, MSG_Code, Subject, type) {
            dhxRecpWin = new dhtmlXWindows();
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            recpWin = dhxRecpWin.createWindow("wins_" + MSG_Code, x, y, 700, 400);
            recpWin.center();
            recpWin.button("minmax1").hide();
            recpWin.button("minmax2").hide();
            recpWin.button("park").hide();
            recpWin.setModal(true);
            if (type == 'viewed') {
                recpWin.setText("Viewed recipients of " + Subject);
            } else if (type == 'total') {
                recpWin.setText("Recipients of " + Subject);
            }
            recepGrid = recpWin.attachGrid();
            recepGrid.attachHeader(",#combo_filter,#combo_filter,#combo_filter");
            recepGrid.enableTooltips("false,false,false,false");

            recepGrid.enableColSpan(true);
            recepGrid.attachEvent("onFilterEnd", function () {
                var rowID = 0;
                var i;
                for (i = 0; i < recepGrid.getRowsNum(); i++) {
                    rowID = recepGrid.getRowId(i);
                    recepGrid.cells(rowID, 0).setValue(i + 1);

                }
                ;
            });
            preTally.Settings.progressOn(true, recpWin, null);
            var params = "msg_code=" + MSG_Code + "&type=" + type;
            recepGrid.loadXML(preTally.Initialize.encryptURL("requisites/viewMsgRecipients.php&" + params), function () {
                preTally.Settings.progressOff(true, recpWin, null);
            });
            recepGrid.attachEvent("onFilterEnd", function (elements) {
                if (recepGrid.getRowsNum() == 0) {
                    recepGrid.addRow("row1", ['', 'Record not Found', ''], 0);
                    recepGrid.setColspan("row1", 1, 3);
                    recepGrid.setRowTextStyle("row1", "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }

            });
        },
        approvalQueue: function () {
            if (!dhxMiddleBlockTabs.cells("approvalQueue")) {
                dhxMiddleBlockTabs.addTab("approvalQueue", "<img src='images/icon/error_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Approval Queue", 150);
                dhxMiddleBlockTabs.tabs("approvalQueue").setActive();
                dhxApprovalLayout = dhxMiddleBlockTabs.cells("approvalQueue").attachLayout("1C");
                dhxApprovalTab = dhxApprovalLayout.cells("a").attachTabbar();
                dhxApprovalTab.addTab("ItmQueue", "Items");
                dhxApprovalTab.addTab("DescQueue", "Descriptions");
                dhxApprovalTab.tabs("ItmQueue").setActive();
                ItemQueueGrid = dhxApprovalTab.cells("ItmQueue").attachGrid();
                ItemQueueGrid.loadXML(preTally.Initialize.encryptURL("requisites/listItemQueue.php"));
                DescQueueGrid = dhxApprovalTab.cells("DescQueue").attachGrid();
                DescQueueGrid.loadXML(preTally.Initialize.encryptURL("requisites/listDescQueue.php"));

            } else {
                dhxMiddleBlockTabs.tabs("approvalQueue").setActive();
            }
        },
        ConverttoEXL: function (GridName) {
            GridName.toExcel("assets/grid/codebase/grid_export/generate.php");
        },
        showLabel: function (inp, label, custom = null) {
            if (!labelPop) {
                labelPop = new dhtmlXPopup({mode: "left"});
            }
            var x = getAbsoluteLeft(inp);
            var y = getAbsoluteTop(inp);
            var w = inp.offsetWidth;
            var h = inp.offsetHeight;
            if(custom===undefined){
                labelPop.attachHTML(label);
            }else{
                var styledLabel = `<div style="width: min(300px, 90vw); padding: 3px;">${label}</div>`;
                labelPop.attachHTML(styledLabel);
            }
            labelPop.show(x, y, w, h);
        },
        hideLabel: function (inp) {
            if (labelPop.isVisible()) {
                labelPop.hide();
            }
        },
        reLoadOffz: function (req, id, contObj) {
            //console.log(contObj);
            contObj.deleteChildItems(0);
            contObj.loadXML(preTally.Initialize.encryptURL('requisites/offzTree.php&f=' + req + '&r=' + id), function () {
                if (id == 'OF_H') {
                    var bsl = unescape(JGG1P3bDnUSDL3Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                } else {
                    var bsl = unescape(JGG1P3bDnUSDL1Mui7KzYjj28UjPdWxCCtGkJSHeuo);
                }
                if (bsl) {
                    ptyRprtTree.selectItem(bsl, 'onClick', true);
                    preTally.Reports.filterReport(bsl);
                }
            });
        },
        progressOn: function (fullLayout, layout, cell) {
            if (fullLayout) {
                layout.progressOn();
            } else {
                layout.cells(cell).progressOn();
            }
        },
        progressOff: function (fullLayout, layout, cell) {
            if (fullLayout) {
                layout.progressOff();
            } else {
                layout.cells(cell).progressOff();
            }
        },
        randomString: function (length, chars) {
            var mask = '';
            if (chars.indexOf('a') > -1)
                mask += 'abcdefghijklmnopqrstuvwxyz';
            if (chars.indexOf('A') > -1)
                mask += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if (chars.indexOf('#') > -1)
                mask += '0123456789';
            if (chars.indexOf('!') > -1)
                mask += '~`!@#$%^&*()_+-={}[]:";\'<>?,./|\\';
            var result = '';
            for (var i = length; i > 0; --i)
                result += mask[Math.round(Math.random() * (mask.length - 1))];
            return result;
        },
        comboFilterPreload: function (ComboObj, ClassName)//Code for showing Preloader in Filter Combo Load
        {
            $("." + ClassName).find(".dhxcombo_select_button").css("background-image", "none");
            $("." + ClassName).find(".dhxcombo_select_button").css("border", "none");
            $("." + ClassName).find(".dhxcombo_select_img").css("display", "none");
            $("." + ClassName).find(".dhxcombo_select_img").css("background-image", "url('images/preload_combo.GIF')");
            $("." + ClassName).find(".dhxcombo_select_img").css("background-repeat", "no-repeat");
            ComboObj.attachEvent("onKeyPressed", function () {
                $("." + ClassName).find(".dhxcombo_select_img").css("display", "block");
                setTimeout(function () {
                    $("." + ClassName).find(".dhxcombo_select_img").css("display", "none");
                }, 3000);
            });
            ComboObj.attachEvent("onOpen", function () {
                $("." + ClassName).find(".dhxcombo_select_img").css("display", "none");

            });
        },
        comboSelectPreload: function (ComboObj, ClassName)//Code for showing Preloader in Normal Combo Load
        {//alert(ClassName);
            //$("." + ClassName).find(".dhxcombo_select_button").css("background-image", "none");
            //$("." + ClassName).find(".dhxcombo_select_button").css("border", "none");
            // $("." + ClassName).find(".dhxcombo_select_img").css("display", "none");

            ComboObj.attachEvent("onXLS", function () {
                $("." + ClassName).find(".dhxcombo_select_img").css("display", "block");
                $("." + ClassName).find(".dhxcombo_select_img").css("background-image", "url('images/preload_combo.GIF')");
                $("." + ClassName).find(".dhxcombo_select_img").css("background-repeat", "no-repeat");
                setTimeout(function () {
                    $("." + ClassName).find(".dhxcombo_select_img").css("display", "block");
                    $("." + ClassName).find(".dhxcombo_select_img").css("background-image", "url('images/dhxcombo_arrow_down.gif')");
                    $("." + ClassName).find(".dhxcombo_select_img").css("background-repeat", "no-repeat");
                }, 3000);
            });
            ComboObj.attachEvent("onXLE", function () {
                setTimeout(function () {
                    $("." + ClassName).find(".dhxcombo_select_img").css("display", "block");
                    $("." + ClassName).find(".dhxcombo_select_img").css("background-image", "url('images/dhxcombo_arrow_down.gif')");
                    $("." + ClassName).find(".dhxcombo_select_img").css("background-repeat", "no-repeat");
                }, 1000);
            });
        }, applyFilterBug: function () {
            var filterValue = '';
//            var monthVal          = $('#bugMonthFilter').val();
//            var bugVal            = $("#bugFilter").val();
//            var bugStatusVal      = $("#bugStatusFilter").val();
            filterValue += '&filter=' + new Array($('#bugMonthFilter').val() + "--" + $("#bugFilter").val() + "--" + $("#bugStatusFilter").val());
            BugGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listBugs.php" + filterValue), function () {
                $('.feedback_cnt_tot').html("# : " + BugGrid.getUserData("", "TL_Count") + " ");
            });

//            exit;
//            var c = 0;
//            if(BugGrid.doesRowExist("msgRow"))
//                BugGrid.deleteRow("msgRow");
//            if(monthVal != "" || bugVal != "") {
//                for(var i=0; i < BugGrid.getRowsNum();i++){
//                    var dateData = BugGrid.cells2(i,7).getValue();
//                    var bugData  = BugGrid.cells2(i,3).getValue();
//
//                    var split       = dateData.split('-');
//                    var month       = split[1]; 
//                    
//                    if((monthVal == "" || month == monthVal) && (bugVal == "" || bugData.indexOf(bugVal)== 0)  ) {
//                        BugGrid.setRowHidden(BugGrid.getRowId(i),false);
//                    }else {
//                        BugGrid.setRowHidden(BugGrid.getRowId(i),true);
//                        c++;
//                    }
//                }
//            }
//            else {
//                for(var i = 0; i < BugGrid.getRowsNum();i++){
//                    BugGrid.setRowHidden(BugGrid.getRowId(i),false);
//                }
//            }
//            if(BugGrid.getRowsNum() == c) {
//                if(BugGrid.doesRowExist("msgRow"))
//                    BugGrid.deleteRow("msgRow");
//                BugGrid.addRow('msgRow',"No records found");
//                BugGrid.setRowTextStyle('msgRow','font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
//                BugGrid.setColspan("msgRow",0,9);
//            }
        },
        applyMonthFilterBranchOB: function (ob) {
            var branchFilterVal = $('#BOS_BranchFilter').val().toLowerCase();
            var monthFilterVal = $("#BOS_MonthFilter").val().toLowerCase();
            var statusFilterVal = $('#BOS_StatusFilter').val();

            var c = 0;
            if (listOpenBalanceGrid.doesRowExist("msgRow"))
                listOpenBalanceGrid.deleteRow("msgRow");
            if (branchFilterVal != "" || monthFilterVal != "" || statusFilterVal != "") {
                for (var i = 0; i < listOpenBalanceGrid.getRowsNum(); i++) {
                    var branchData = listOpenBalanceGrid.cells2(i, 1).getValue().toString().toLowerCase();
                    var monthData = listOpenBalanceGrid.cells2(i, 3).getValue();
                    var statusData = listOpenBalanceGrid.cells2(i, 4).getValue();
                    var split = monthData.split('/');
                    var month = split[1];
                    if ((monthFilterVal == "" || month == monthFilterVal) && (branchFilterVal == "" || branchData.indexOf(branchFilterVal) == 0) && (statusFilterVal == "" || statusFilterVal == statusData)) {
                        listOpenBalanceGrid.setRowHidden(listOpenBalanceGrid.getRowId(i), false);
                    } else {
                        listOpenBalanceGrid.setRowHidden(listOpenBalanceGrid.getRowId(i), true);
                        c++;
                    }
                }
            } else {
                for (var i = 0; i < listOpenBalanceGrid.getRowsNum(); i++) {
                    listOpenBalanceGrid.setRowHidden(listOpenBalanceGrid.getRowId(i), false);
                }
            }
            if (listOpenBalanceGrid.getRowsNum() == c) {
                if (listOpenBalanceGrid.doesRowExist("msgRow"))
                    listOpenBalanceGrid.deleteRow("msgRow");
                listOpenBalanceGrid.addRow('msgRow', "No records found");
                listOpenBalanceGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                listOpenBalanceGrid.setColspan("msgRow", 0, 5);
            }
        },
        applyMonthFilterBankOB: function () {
            var branchFilterVal = $('#BOB_BranchFilter').val().toLowerCase();
            var monthFilterVal = $("#BOB_MonthFilter").val().toLowerCase();
            var statusFilterVal = $('#BOB_StatusFilter').val();

            var c = 0;
            if (listBnkBalanceGrid.doesRowExist("msgRow"))
                listBnkBalanceGrid.deleteRow("msgRow");
            if (branchFilterVal != "" || monthFilterVal != "" || statusFilterVal != "") {
                for (var i = 0; i < listBnkBalanceGrid.getRowsNum(); i++) {
                    var branchData = listBnkBalanceGrid.cells2(i, 1).getValue().toString().toLowerCase();
                    var monthData = listBnkBalanceGrid.cells2(i, 3).getValue();
                    var statusData = listBnkBalanceGrid.cells2(i, 4).getValue();

                    var split = monthData.split('/');
                    var month = split[1];
                    if ((monthFilterVal == "" || month == monthFilterVal) && (branchFilterVal == "" || branchData.indexOf(branchFilterVal) == 0) && (statusFilterVal == "" || statusFilterVal == statusData)) {
                        listBnkBalanceGrid.setRowHidden(listBnkBalanceGrid.getRowId(i), false);
                    } else {
                        listBnkBalanceGrid.setRowHidden(listBnkBalanceGrid.getRowId(i), true);
                        c++;
                    }
                }
            } else {
                for (var i = 0; i < listBnkBalanceGrid.getRowsNum(); i++) {
                    listBnkBalanceGrid.setRowHidden(listBnkBalanceGrid.getRowId(i), false);
                }
            }
            if (listBnkBalanceGrid.getRowsNum() == c) {
                if (listBnkBalanceGrid.doesRowExist("msgRow"))
                    listBnkBalanceGrid.deleteRow("msgRow");
                listBnkBalanceGrid.addRow('msgRow', "No records found");
                listBnkBalanceGrid.setRowTextStyle('msgRow', 'font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;');
                listBnkBalanceGrid.setColspan("msgRow", 0, 5);
            }
        },
        clearGridFilters: function (GridObj) {
            var cols = GridObj.getColumnsNum();
            for (var i = 0; i < cols; i++) {
                if (GridObj.getFilterElement(i))
                    GridObj.getFilterElement(i).value = "";
            }
        },
        entryEditBlockDate: function () {
            if (!dhxMiddleBlockTabs.cells("menuEntryEditBlockDate")) {
                dhxMiddleBlockTabs.addTab("menuEntryEditBlockDate", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;'/>&nbsp;&nbsp;Set Blocking Date", 200);
                dhxMiddleBlockTabs.tabs("menuEntryEditBlockDate").setActive();

                var menuEntryEditBlockDateLayout = dhxMiddleBlockTabs.cells("menuEntryEditBlockDate").attachLayout('2E');
                menuEntryEditBlockDateLayout.cells("a").setText("New Date");
                menuEntryEditBlockDateLayout.cells("b").setText("Blocked Date History");
                menuEntryEditBlockDateLayout.cells("a").setHeight(100);

                listBlockedDateHistory = menuEntryEditBlockDateLayout.cells("b").attachGrid();
                listBlockedDateHistory.enableTooltips("false,false,false,false");
                listBlockedDateHistory.init();
                listBlockedDateHistory.loadXML(preTally.Initialize.encryptURL("requisites/listBSEntryEditSettingsHistory.php"), function () {

                });


                var setBlockingDateForm = [
                    {type: "settings", position: "label-left", offsetTop: 20, offsetLeft: 30},
                    {type: "calendar", label: "Set Date ", labelWidth: 100, name: "BES_Date", required: true, serverDateFormat: "%Y-%m-%d", dateFormat: "%d.%m.%Y"},
                    {type: "newcolumn"},
                    {type: "button", value: "Submit", name: "confirmDate", offsetTop: 16}
                ];

                addBlockingDateForm = menuEntryEditBlockDateLayout.cells("a").attachForm(setBlockingDateForm);

                var today = new Date();
                today.setDate(today.getDate() - 1);

                var BesCalendar = addBlockingDateForm.getCalendar("BES_Date");
                BesCalendar.setSensitiveRange(null, today);

                addBlockingDateForm.attachEvent("onButtonClick", function (name) {
                    if (name == 'confirmDate') {
                        if (addBlockingDateForm.validate()) {
                            preTally.Settings.progressOn(true, dhxLayout, null);
                            addBlockingDateForm.send(preTally.Initialize.encryptURL('warehouse/setEntryUpdateDate.php'), function (loader, response) {
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                if (response != 'fail') {
                                    addBlockingDateForm.resetValidateCss();
                                    addBlockingDateForm.clear();
                                    listBlockedDateHistory.loadXML(preTally.Initialize.encryptURL("requisites/listBSEntryEditSettingsHistory.php"), function () {});
                                } else {
                                    response = 'Invalid. Please Re-Try';
                                }
                                dhtmlx.message({text: response});
                            });
                        }
                    }
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuEntryEditBlockDate").setActive();
            }

        },
        leaveDetails: function () {
            if (!dhxMiddleBlockTabs.cells("leaveDetails")) {
                var filtrInterval;
                dhxMiddleBlockTabs.addTab("leaveDetails", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Leave Details", 150);
                dhxMiddleBlockTabs.tabs("leaveDetails").setActive();

                var Year_Options = [];

                for (j = Date.today().getFullYear(); j >= 2014; j--) {
                    Year_Options.push([j, 'obj', j, "calendar_Y.png"]);
                }

                leaveDetailsLayout = dhxMiddleBlockTabs.cells("leaveDetails").attachLayout('1C');
                leaveDetailsToolbar = leaveDetailsLayout.cells("a").attachToolbar();
                leaveDetailsLayout.cells("a").hideHeader();
                leaveDetailsToolbar.setIconsPath("images/icon/default_18/");
                leaveDetailsToolbar.addButtonSelect("lv_year_filter", '1', "Select Year", Year_Options, 'calendar_Y.png', '', true, true, 10, 'select');
                leaveDetailsToolbar.setListOptionSelected("lv_year_filter", Date.today().getFullYear());

                leaveDetailsGrid = leaveDetailsLayout.cells("a").attachGrid();
                leaveDetailsGrid.enableColSpan(true);

                leaveDetailsToolbar.attachEvent("onClick", function (id) {
                    if (leaveDetailsToolbar.getParentId(id) == "lv_year_filter") {
                        preTally.Settings.applyUserLeaveFilter();
                    }
                });

                dhxMiddleBlockTabs.cells("leaveDetails").attachStatusBar({
                    text: "<div class='tb_data_txt_secl'>\
                                <div style='float:left;font-weight:bold;' class='leave_cnt_tot'># : 0</div>\
                            </div><div style='float:right;' id='leaveDetails_paging'></div>",
                    height: 30
                });
                leaveDetailsGrid.setPagingWTMode(true, false, true, [15, 30, 50, 80]);
                leaveDetailsGrid.enablePaging(true, 50, 5, "leaveDetails_paging", true);
                leaveDetailsGrid.setPagingSkin("toolbar", "dhx_skyblue");

                var filterValue = new Array(leaveDetailsToolbar.getListOptionSelected('lv_year_filter'));

                preTally.Settings.progressOn(true, dhxLayout, null);
                leaveDetailsGrid.loadXML(preTally.Initialize.encryptURL("requisites/leaveDetails.php&filter=" + filterValue), function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                    $('.leave_cnt_tot').html("# : " + leaveDetailsGrid.getUserData("", "TL_Count") + " ");

                    if (leaveDetailsGrid.getRowsNum() === 0) {
                        leaveDetailsGrid.addRow('0', "No records found", 0);
                        leaveDetailsGrid.setColspan('0', 0, leaveDetailsGrid.getColumnsNum());
                        leaveDetailsGrid.setRowTextStyle('0', "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                    }

                    $(".leave_text_filter").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);
                        filtrInterval = setInterval(function () {
                            preTally.Settings.applyUserLeaveFilter();
                            clearInterval(filtrInterval);
                        }, 500);
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("leaveDetails").setActive();
            }
        },
        applyUserLeaveFilter: function () {
            var filterValue = new Array(leaveDetailsToolbar.getListOptionSelected('lv_year_filter'), $('#leave_user_name').val(), $('#leave_branch').val(), 1);
            preTally.Settings.progressOn(true, dhxLayout, null);
            leaveDetailsGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/leaveDetails.php&filter=" + filterValue), function () {
                $('.leave_cnt_tot').html("# : " + leaveDetailsGrid.getUserData("", "TL_Count") + " ");

                if (leaveDetailsGrid.getRowsNum() === 0) {
                    leaveDetailsGrid.addRow('0', "No records found", 0);
                    leaveDetailsGrid.setColspan('0', 0, leaveDetailsGrid.getColumnsNum());
                    leaveDetailsGrid.setRowTextStyle('0', "font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;");
                }
                preTally.Settings.progressOff(true, dhxLayout, null);
            });
        },
        menuManageZones: function () {
            if (!dhxMiddleBlockTabs.cells("manageZones")) {
                dhxMiddleBlockTabs.addTab("manageZones", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Manage Zones", 150);
                dhxMiddleBlockTabs.tabs("manageZones").setActive();
                var manageZones = dhxMiddleBlockTabs.cells("manageZones").attachLayout('2U');
                manageZones.cells("a").setText("New Zone");
                manageZones.cells("b").setText("List Zone");
                manageZones.cells("a").setWidth(400);
                listZoneGrid = manageZones.cells("b").attachGrid();
                listZoneGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listZoneGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });
                listZoneGrid.enableTooltips("false,false,false");
                listZoneGrid.setHeader("Slno,Zone Name,Branches,Created By,Last Updated By,Status");
                listZoneGrid.attachHeader(",<input type='text' class='addzone_filter' id='addzone_txtname' style='width:90%'/>,<div class='addzone_filter' id='addzone_brname'  style='width:90%'></div>,<input type='text' style='width:90%;' class='addzone_filter' id='addzone_created'/>,<input type='text' style='width:90%;' class='addzone_filter' id='addzone_updated'/>,<div class='addzone_filter' id='addzone_status'  style='width:90%'></div>");
                listZoneGrid.setInitWidths("50,100,*,100,100,80");
                listZoneGrid.setColAlign("center,left,left,left,left,center");
                listZoneGrid.enableColSpan('true');
                listZoneGrid.setColTypes("ro,ro,ro,ro,ro,ro")
                listZoneGrid.enableRowsHover(true, "bonusReportHover");
                listZoneGrid.init();
                var addZoneBranchCombo = new dhtmlXCombo("addzone_brname");
                var addZoneStatusCombo = new dhtmlXCombo("addzone_status");
                addZoneBranchCombo.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=CHK"));
                addZoneBranchCombo.enableFilteringMode("between");
                addZoneStatusCombo.addOption([
                    ["All", "All", null, null, true],
                    ["1", "Published"],
                    ["0", "Blocked"]
                ]);

                addZoneBranchCombo.attachEvent("onChange", function () {
                    preTally.Settings.applyNewZoneGridFitler($("#addzone_txtname").val(), addZoneBranchCombo.getSelectedValue(), addZoneStatusCombo.getSelectedValue(),$("#addzone_created").val(),$("#addzone_updated").val());
                });
                addZoneStatusCombo.attachEvent("onChange", function () {
                     preTally.Settings.applyNewZoneGridFitler($("#addzone_txtname").val(), addZoneBranchCombo.getSelectedValue(), addZoneStatusCombo.getSelectedValue(),$("#addzone_created").val(),$("#addzone_updated").val());
                });
                var filtrInterval;
                $(".addzone_filter").keyup(function () {
                    if (filtrInterval)
                        clearInterval(filtrInterval);
                    filtrInterval = setInterval(function () {
                         preTally.Settings.applyNewZoneGridFitler($("#addzone_txtname").val(), addZoneBranchCombo.getSelectedValue(), addZoneStatusCombo.getSelectedValue(),$("#addzone_created").val(),$("#addzone_updated").val());
                        clearInterval(filtrInterval);
                    }, 500);
                });

                listZoneGrid.load(preTally.Initialize.encryptURL("requisites/listZones.php"));
                listZoneGrid.attachEvent('onRowSelect', function (id) {
                    manageZoneForm.setItemValue("ZN_Id", id);
                    manageZoneForm.setItemValue("ZN_Name", listZoneGrid.getUserData(id, "ZN_Name"));
                    var LocCombo = manageZoneForm.getCombo("LC_IdCombo");                    
                    var LocationVals = listZoneGrid.getUserData(id, "ZN_Branches");
                    for (var i = 0; i < LocCombo.getOptionsCount(); i++) {
                        LocCombo.setChecked(i, false);
                    }
                    ValArray = LocationVals.split(",");
                    ValArray.forEach(function (Vals) {                        
                        var indx = LocCombo.getIndexByValue(Vals);
                        LocCombo.setChecked(indx, true);
                    });
                    
                    LocCombo.setComboText(LocCombo.getChecked().length + " Branches Selected");
                    if (!manageZoneForm.getCombo("ZN_Status").getOption("2")) {
                        manageZoneForm.getCombo("ZN_Status").addOption("2", "Delete", null, null, true);
                    }
                    manageZoneForm.setItemValue("ZN_Status", listZoneGrid.getUserData(id, "ZN_Status"));
                });
                manageZoneForm = manageZones.cells("a").attachForm();
                manageZoneForm.loadStruct(preTally.Initialize.encryptURL("requisites/manageZone.php"), function () {
                    var LocCombo = manageZoneForm.getCombo("LC_IdCombo");
                    LocCombo.setPlaceholder("Select Branch")
                    LocCombo.attachEvent("onClose", function () {
                                LocCombo.setComboText(LocCombo.getChecked().length + " Branches Selected");
                            });
                    manageZoneForm.attachEvent("onButtonClick", function (name) {                        
                        if (name == 'newZoneValidate') {
                            if(manageZoneForm.validate() && LocCombo.getChecked().length!=0){
                            manageZoneForm.setItemValue("LC_Id", LocCombo.getChecked());
                            manageZoneForm.send(preTally.Initialize.encryptURL("warehouse/addZones.php"), function (loader, response) {
                                if (response == 'fail') {
                                    dhtmlx.message("Zone already exist");
                                } else {
                                    manageZoneForm.getCombo("ZN_Status").deleteOption("2");
                                    dhtmlx.message({text: response});
                                    listZoneGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listZones.php"), true, true);
                                    manageZoneForm.clear();
                                    manageZoneForm.setItemValue("ZN_Id",'0');
                                    LocCombo.forEachOption(function (indx) {
                                        LocCombo.setChecked(indx.index, false);
                                    });
                                    LocCombo.setComboText(LocCombo.getChecked().length + " Branches Selected");
                                }
                            });
                        }else if(LocCombo.getChecked().length==0){
                            manageZoneForm.setValidateCss("LC_IdCombo", false, "validate_red");
                        }
                        } else if (name == 'newZoneCancel') {                                 
                            LocCombo.forEachOption(function (indx) {                                
                                LocCombo.setChecked(indx.index, false);
                            });
                             manageZoneForm.clear();
                             manageZoneForm.setItemValue("ZN_Id",'0');
                            LocCombo.setComboText(LocCombo.getChecked().length + " Branches Selected");
                           
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("manageZones").setActive();
            }

        }, applyNewZoneGridFitler: function (name, lcid, status,createdby,updatedby) {
            var newZoneFiltr = "&zname=" + name + "&lcid=" + lcid + '&status=' + status+'&created='+createdby+'&updated='+updatedby;
            listZoneGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listZones.php" + newZoneFiltr));
        }, menuAssignZones: function () {
            if (!dhxMiddleBlockTabs.cells("assignZones")) {
                dhxMiddleBlockTabs.addTab("assignZones", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Assign Zone", 200);
                dhxMiddleBlockTabs.tabs("assignZones").setActive();
                var assignZones = dhxMiddleBlockTabs.cells("assignZones").attachLayout('1C');
                assignZones.cells("a").setText("Assign Zone");
                listZoneAssignGrid = assignZones.cells("a").attachGrid();
                listZoneAssignGrid.attachEvent("onXLS", function () {
                    preTally.Settings.progressOn(true, dhxLayout, null);
                });
                listZoneAssignGrid.attachEvent("onXLE", function () {
                    preTally.Settings.progressOff(true, dhxLayout, null);
                });

                listZoneAssignGrid.setHeader("SlNo,Name,Branch,Zones,Last Updated By,Edit");
                listZoneAssignGrid.attachHeader(",<input type='text' id='assgn_zn_namefilter' class='assign_zn_filter' style='width:90%;'></input>,<div id='assgn_zn_brfilter' style='width:90%;'></div>,<div id='assgn_zn_filter' style='width:90%;'></div>,<input type='text' id='assgn_zn_updfilter' class='assign_zn_filter' style='width:90%;'></input>,");
                listZoneAssignGrid.setInitWidths("40,150,150,*,100,50");
                listZoneAssignGrid.setColAlign("left,left,left,left,left,");
                listZoneAssignGrid.setColTypes("ro,ro,ro,ro,ro,ro,");
                listZoneAssignGrid.enableTooltips("false,false,false,false,false,false,false");
                listZoneAssignGrid.enableRowsHover(true, "bonusReportHover");
                listZoneAssignGrid.init();
                listZoneAssignGrid.enableColSpan(true);
                listZoneAssignGrid.load(preTally.Initialize.encryptURL("requisites/listAssignZones.php"), function () {
                    var ZoneFilter = new dhtmlXCombo("assgn_zn_filter");
                    ZoneFilter.load(preTally.Initialize.encryptURL("requisites/zone_filter.php"));
                    ZoneFilter.enableFilteringMode("between");
                    var ZnBranchFilter = new dhtmlXCombo("assgn_zn_brfilter");
                    ZnBranchFilter.load(preTally.Initialize.encryptURL("requisites/locations.php&filter=CHK"));
                    ZnBranchFilter.enableFilteringMode("between");
                    ZoneFilter.attachEvent("onChange", function () {
                        preTally.Settings.applyZoneGridFitler($("#assgn_zn_namefilter").val(), ZnBranchFilter.getSelectedValue(), ZoneFilter.getSelectedValue(),$("#assgn_zn_updfilter").val());
                    });
                    ZnBranchFilter.attachEvent("onChange", function () {
                        preTally.Settings.applyZoneGridFitler($("#assgn_zn_namefilter").val(), ZnBranchFilter.getSelectedValue(), ZoneFilter.getSelectedValue(),$("#assgn_zn_updfilter").val());
                    });
                    var filtrInterval;
                    $(".assign_zn_filter").keyup(function () {
                        if (filtrInterval)
                            clearInterval(filtrInterval);

                        filtrInterval = setInterval(function () {
                            preTally.Settings.applyZoneGridFitler($("#assgn_zn_namefilter").val(), ZnBranchFilter.getSelectedValue(), ZoneFilter.getSelectedValue(),$("#assgn_zn_updfilter").val());
                            clearInterval(filtrInterval);
                        }, 500);
                    });
                });
                listZoneAssignGrid.attachEvent('onRowSelect', function (id,ind) {
                    if(ind=='5'){
                    dhxManageZoneWin = new dhtmlXWindows();
                    ManageZoneWin = dhxManageZoneWin.createWindow("wins_branchDetails", 200, 400, 400, 250);
                    //ManageZoneWin.button("minmax1").hide();
                    //ManageZoneWin.button("minmax2").hide();
                    ManageZoneWin.button("park").hide();
                    ManageZoneWin.center();
                    ManageZoneWin.setModal(true);
                    ManageZoneWin.setText("Assign Zone");
                    AssignZoneForm = ManageZoneWin.attachForm();
                    AssignZoneForm.loadStruct(preTally.Initialize.encryptURL("requisites/assignZoneForm.php"), function () {
                        AssignZoneForm.setItemValue("US_Id", listZoneAssignGrid.getUserData(id, "US_Id"));
                        AssignZoneForm.setItemValue("US_Name", listZoneAssignGrid.getUserData(id, "US_Name"));
                        ZoneCombo = AssignZoneForm.getCombo("ZN_IdCombo");
                        ZoneCombo.load(preTally.Initialize.encryptURL("requisites/combo_zones.php"), function () {
                            ZoneCombo.enableFilteringMode(true);
                            ZoneCombo.attachEvent("onClose", function () {
                                ZoneCombo.setComboText(ZoneCombo.getChecked().length + " Zone Selected");
                            });
                            var ZoneVals = listZoneAssignGrid.getUserData(id, "US_Zones");

                            var ValArray = ZoneVals.split(",");
                            ValArray.forEach(function (Vals) {
                                var indx = ZoneCombo.getIndexByValue(Vals);
                                ZoneCombo.setChecked(indx, true);
                            });
                            ZoneCombo.setComboText(ZoneCombo.getChecked().length + " Zone Selected");
                        });
                        AssignZoneForm.attachEvent("onButtonClick", function (name) {
                            if (name == 'newAssignZoneValidate') {
                                if(AssignZoneForm.validate() && ZoneCombo.getChecked().length!=0){
                                   AssignZoneForm.setItemValue("ZN_Id", ZoneCombo.getChecked());
                                AssignZoneForm.send(preTally.Initialize.encryptURL("warehouse/assignZones.php"), function (loader, response) {
                                    if (response == 'fail') {
                                        dhtmlx.message("Zone assignment failed");
                                    } else {
                                        dhtmlx.message({text: response});
                                        listZoneAssignGrid.updateFromXML(preTally.Initialize.encryptURL("requisites/listAssignZones.php"), true, true);
                                        ZoneCombo.forEachOption(function (indx) {
                                            ZoneCombo.setChecked(indx.index, false);
                                        });
                                        ManageZoneWin.close();
                                        
                                    }
                                }); 
                                }else if(!ZoneCombo.getChecked().length){
                                   dhtmlx.message("No Zone selected");
                                }
                                
                            } else if (name == 'newAssignZoneCancel') {                                
                                ZoneCombo.forEachOption(function (indx) {                                    
                                    ZoneCombo.setChecked(indx.index, false);                                    
                                });
                                AssignZoneForm.clear();
                                ZoneCombo.setComboText(ZoneCombo.getChecked().length + " Branches Selected");
                                dhxManageZoneWin.window('wins_branchDetails').close();
                            }
                        });

                        AssignZoneForm.setItemValue("ZN_Status", listZoneAssignGrid.getUserData(id, "ZN_Status"));
                    });
                }
                });
            
            } else {
                dhxMiddleBlockTabs.tabs("assignZones").setActive();
            }

        }, applyZoneGridFitler: function (name, lcid, znid,updby) {
            var assgnZoneFiltr = "&uname=" + name + "&lcid=" + lcid + "&znid=" + znid+"&updby="+updby;
            listZoneAssignGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listAssignZones.php&filter=" + assgnZoneFiltr));
        },
        // user id based access control levels list and adding start
        menuUserAccessLevel: function() {
            if (!dhxMiddleBlockTabs.cells("access_user_level")) {
                dhxMiddleBlockTabs.addTab("access_user_level", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Users Access Control Level", 205);
                dhxMiddleBlockTabs.tabs("access_user_level").setActive();
                // layout and heading start
                var usrAccessLevel = dhxMiddleBlockTabs.cells("access_user_level").attachLayout('2U');
                usrAccessLevel.cells("a").setText("Users Access Controls");
                usrAccessLevel.cells("b").setText("List Users Access Control Levels");
                usrAccessLevel.cells("a").setWidth(635);

                // status bar or pagination and count
                usrAccessLevel.cells("b").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('usracls'),
                    height: 35
                }); 
                 //----------------- Attach Grid for ACL --------------------//
                listUsrACLGrid = usrAccessLevel.cells("b").attachGrid();                
                listUsrACLGrid.setHeader("SlNo,Staff Name <input type='text' id ='fltAclUser' style='width: 90%;' placeholder='Enter Staff Name'>");
                listUsrACLGrid.setInitWidths("60,*");
                listUsrACLGrid.setColAlign("center,left");
                listUsrACLGrid.setColTypes("ro,ro");
                listUsrACLGrid.setColSorting("na,na");              
                listUsrACLGrid.enableTooltips("false,false");
                preTally.Attendance.commonGridDefine(listUsrACLGrid, 'usracls');
                // loading the list Xml files atart..
                preTally.Settings.loadUserAcls();

                //filter employee names
                var filtrInterval;
                $( "#fltAclUser" ).keyup(function(value) {
                    if(filtrInterval) clearInterval(filtrInterval);
                    filtrInterval = setInterval( function() { 
                        preTally.Settings.loadUserAcls();
                        clearInterval(filtrInterval); 
                    }, 500);
                });
                userACLForm = usrAccessLevel.cells("a").attachForm();

                userACLForm.loadStruct(preTally.Initialize.encryptURL("requisites/manageUsersACL.php"), function () {
                    
                    // enable search in user cbo
                    var usraclcbo = userACLForm.getCombo("ua_user_id");
                    if (usraclcbo) {
                        preTally.UserProfile.applyFilterHandler(usraclcbo);
                        usraclcbo.setOptionWidth(230);
                        // on change user id stored in hidden fields.
                        usraclcbo.attachEvent("onChange", function () {
                            var usid = usraclcbo.getSelectedValue();
                            userACLForm.setItemValue("us_id", usid);
                        });
                    }
                    // click on save or cancel button
                    userACLForm.attachEvent("onButtonClick", function (name) {
                        if (name == 'ACLBtnSave') {
                            var newItemsave = userACLForm.validate();
                            if (newItemsave) {
                                preTally.Settings.progressOn(true, dhxLayout, null);
                                userACLForm.send(preTally.Initialize.encryptURL('warehouse/newUserACL.php'), function (loader, response) {
                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                    dhtmlx.message({text: response});
                                    userACLForm.resetValidateCss();
                                    userACLForm.clear();
                                    userACLForm.setItemValue("aclid", 0);
                                    userACLForm.setItemValue("us_id", 0);
                                    userACLForm.getCombo("ua_user_id").setComboValue('');
                                    userACLForm.getCombo("ua_user_id").setComboText('Select User');
                                    preTally.Settings.loadUserAcls();
                                });
                            }
                        } else {
                            userACLForm.resetValidateCss();
                            userACLForm.clear();
                            userACLForm.setItemValue("aclid", 0);
                            userACLForm.setItemValue("us_id", 0);                            
                            userACLForm.getCombo("ua_user_id").setComboValue('');
                            userACLForm.getCombo("ua_user_id").setComboText('Select User');
                        }
                    });                
                });  
            } else {
                dhxMiddleBlockTabs.tabs("access_user_level").setActive(); 
            }
        },
        loadUserAcls: function() {
            // get all filters in an array
            var filterValue = new Array($('#fltAclUser').val());
            listUsrACLGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/listUsersACL.php&filter="+filterValue), function() { 
                $('#total_usracls').html("# : "+listUsrACLGrid.getUserData("", "Data_Count")+" ");
                // click on grid row loaded into form for edit
                listUsrACLGrid.attachEvent("onRowSelect", function (id, ind) {
                    userACLForm.setItemValue("aclid", listUsrACLGrid.getUserData(id, "aclid"));
                    userACLForm.setItemValue("us_id", listUsrACLGrid.getUserData(id, "us_id"));
                    userACLForm.setItemValue("ua_user_id", listUsrACLGrid.getUserData(id, "us_id"));
                    userACLForm.setItemValue("add_break_time", listUsrACLGrid.getUserData(id, "add_break_time"));
                    userACLForm.setItemValue("view_break_time", listUsrACLGrid.getUserData(id, "view_break_time"));
                    userACLForm.setItemValue("view_late_entry", listUsrACLGrid.getUserData(id, "view_late_entry"));
                    userACLForm.setItemValue("approve_late_entry", listUsrACLGrid.getUserData(id, "approve_late_entry"));
                    userACLForm.setItemValue("add_clear_attendance", listUsrACLGrid.getUserData(id, "add_clear_attendance"));
                    userACLForm.setItemValue("view_clear_attencance", listUsrACLGrid.getUserData(id, "view_clear_attencance"));
                    userACLForm.setItemValue("view_attendance_detail", listUsrACLGrid.getUserData(id, "view_attendance_detail"));
                    userACLForm.setItemValue("view_accounts_report", listUsrACLGrid.getUserData(id, "view_accounts_report"));                    
                    userACLForm.setItemValue("view_master_report", listUsrACLGrid.getUserData(id, "view_master_report"));                    
                    userACLForm.setItemValue("view_account_settings", listUsrACLGrid.getUserData(id, "view_account_settings"));                    
                    userACLForm.setItemValue("add_account_settings", listUsrACLGrid.getUserData(id, "add_account_settings"));                    
                    userACLForm.setItemValue("approve_account_settings", listUsrACLGrid.getUserData(id, "approve_account_settings"));                    
                    userACLForm.setItemValue("approve_grace_time", listUsrACLGrid.getUserData(id, "approve_grace_time"));                    
                    userACLForm.setItemValue("approve_ledger_amount", listUsrACLGrid.getUserData(id, "approve_ledger_amount"));                    
                    userACLForm.setItemValue("manage_bills", listUsrACLGrid.getUserData(id, "manage_bills"));                    
                    userACLForm.setItemValue("manage_all_bills", listUsrACLGrid.getUserData(id, "manage_all_bills"));                    
                    userACLForm.setItemValue("close_others_tracks", listUsrACLGrid.getUserData(id, "close_others_tracks"));                    
                    userACLForm.setItemValue("view_all_company", listUsrACLGrid.getUserData(id, "view_all_company"));                    
                    userACLForm.setItemValue("bank_reconciliation", listUsrACLGrid.getUserData(id, "bank_reconciliation"));                    
                    userACLForm.setItemValue("add_open_balance", listUsrACLGrid.getUserData(id, "add_open_balance"));                    
                }); 
            });
        },
        // list all users with reduce or adjusted company working time 
        timeAdjustedUsers: function() {
            if (!dhxMiddleBlockTabs.cells("time_adjusted_users")) {
                dhxMiddleBlockTabs.addTab("time_adjusted_users", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;' />&nbsp;&nbsp;Working Time Temporary Adjusted Employees ", 330);
                dhxMiddleBlockTabs.tabs("time_adjusted_users").setActive();
                 // layout and heading start
                userTimeAdjLayout   = dhxMiddleBlockTabs.cells("time_adjusted_users").attachLayout("1C");
                userTimeAdjLayout.cells("a").hideHeader();
                // status bar or pagination and count
                userTimeAdjLayout.cells("a").attachStatusBar({
                    text  : preTally.Attendance.commonStatusBar('adjtime'),
                    height: 35
                });
                //----------------- Attach Grid --------------------//
                userTimeAdjGrid     = userTimeAdjLayout.cells("a").attachGrid();
                userTimeAdjGrid.setHeader("Slno, Staff Name<img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='staff' class='adjtimeSort' /> <input type='text' id ='fltADTUser' style='width: 90%;' placeholder='Enter Staff Name'>,\
                Working Time <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='time' class='adjtimeSort' />,\
                Start Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='startdate' class='adjtimeSort' />, \
                End Date <img src='images/icon/sort-ascending-icon.png' title='Click here to Sort' sortField='enddate' class='adjtimeSort' />,\
                Reason, Approved By, Status <select style='width:98%;' id='fltADTStatus'><option value='1'>Active</option><option value='0'>Completed</option></select>");
                userTimeAdjGrid.setInitWidths("70,*,80,100,100,*,120, 100");
                userTimeAdjGrid.setColAlign("left,left,left,center,center,left,left,center");
                userTimeAdjGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro"); 
                userTimeAdjGrid.setColSorting("na,na,na,na,na,na,na,na");     
                userTimeAdjGrid.enableTooltips("false,false,false,false,false,false,false,false");
                preTally.Attendance.commonGridDefine(userTimeAdjGrid, 'adjtime');

                //filter employee names
                var filtrInterval;
                $( "#fltADTUser" ).keyup(function(value) {
                    if(filtrInterval) clearInterval(filtrInterval);
                    filtrInterval = setInterval( function() { 
                        preTally.Settings.loadUserTimeAdj('');
                        clearInterval(filtrInterval); 
                    }, 500);
                });
                $( "#fltADTStatus" ).change(function(value) {
                    preTally.Settings.loadUserTimeAdj('');
                });
                // sort field setups
                $('.adjtimeSort').click(function () {
                    var sfield  = $(this).attr("sortField"); 
                    var sorder  = 'ASC';
                    if ($(this).attr("src") == 'images/icon/sort-descending-icon.png') {
                        $(this).attr("src", 'images/icon/sort-ascending-icon.png');
                        sorder  = 'ASC';
                    } else {
                        $(this).attr("src", 'images/icon/sort-descending-icon.png');
                        sorder  = 'DESC'; 
                    }
                    preTally.Settings.loadUserTimeAdj("&sort="+sfield+"&order="+sorder);
                });
                // load the grace time table data (temp)
                preTally.Settings.loadUserTimeAdj('');
            } else {
                dhxMiddleBlockTabs.tabs("time_adjusted_users").setActive(); 
            }
        },
        loadUserTimeAdj: function(extra) {
            var filterValue = new Array($('#fltADTUser').val(),$('#fltADTStatus').val());
            userTimeAdjGrid.clearAndLoad(preTally.Initialize.encryptURL("requisites/timeAdjustedUser.php&filter="+filterValue+extra), function() { 
                $('#total_adjtime').html("# : "+userTimeAdjGrid.getUserData("", "Data_Count")+" ");
            });
        },

    };
})(jQuery, this);