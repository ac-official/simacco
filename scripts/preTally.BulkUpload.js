;(function($, window, undefined) {
    preTally.BulkUpload = {
        menuImportBranches: function() {
            if (!dhxMiddleBlockTabs.cells("menuImportBnkBranches")) {
                dhxMiddleBlockTabs.addTab("menuImportBnkBranches", "<img src='images/icon/bank_branch.png' style='margin-top:2px;' />&nbsp;&nbsp;Import Branches", 170);
                dhxMiddleBlockTabs.tabs("menuImportBnkBranches").setActive();
                var menuImportBranchesLayout = dhxMiddleBlockTabs.cells("menuImportBnkBranches").attachLayout('3J');
                menuImportBranchesLayout.cells("a").setText("New Branch Preview");
                menuImportBranchesLayout.cells("b").setText("Error List");
                menuImportBranchesLayout.cells("c").setText("Import Excel");
                menuImportBranchesLayout.cells("a").setHeight(200);
                menuImportBranchesLayout.cells("b").setWidth(600);

                var fileNewName = Math.random().toString(36).substring(5);
             
                var excelVault   =   menuImportBranchesLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl:preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });
                
                menuImportBranchesLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/BranchExcel.xls">Download Excel Format</a>', height:30});

                excelVault.attachEvent("onFileRemove",function() {
                   excelVault.setFilesLimit(1);
                   menuImportBranchesLayout.cells("c").detachObject(); 
                });
               
                excelVault.attachEvent("onClear", function(file){
                   menuImportBranchesLayout.cells("c").detachObject(); 
                });
                
                excelVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportBranchesLayout.cells("b").detachObject(); 
                    menuImportBranchesLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext!= 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelVault.attachEvent("onUploadComplete", function(){
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData=excelVault.getData();
                    var file    = uploadedFileData[0];
                    var ext     =  file.name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"12"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) {
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelVault.clear();
                        } else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data);
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportBranchesLayout.cells("c").attachForm();
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/branchExcelForm.php"), function() {                                    
                                    preTally.Settings.progressOff(true, dhxLayout, null);                                  
                                    if(str.cells[0][0] == "Office Name" && str.cells[0][1] == "Company" && 
                                    str.cells[0][2] == "Phone Number" && str.cells[0][3] == "Building Name" 
                                    && str.cells[0][4] == "Street Name" && str.cells[0][5] == "Place"  && str.cells[0][6] == "Location" 
                                    && str.cells[0][7] == "City" && str.cells[0][8] == "State" 
                                    && str.cells[0][9] == "Country" && str.cells[0][10] == "Pincode" 
                                    && str.cells[0][11] == "Remarks") {

                                        excelDataForm.setItemValue("IBB_ExcelArray",data);
                                        branchTitle = excelDataForm.getCombo("IBB_Title");   

                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            branchTitle.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                branchTitle.setComboText(str.cells[1][0]);
                                                branchTitle.setComboValue(i);
                                            }
                                        }

                                        branchOffice = excelDataForm.getCombo("IBB_Office");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            branchOffice.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                branchOffice.setComboText(str.cells[1][1]);
                                                branchOffice.setComboValue(i);
                                            }
                                        }

                                        branchPhone = excelDataForm.getCombo("IBB_Phone");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchPhone.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                branchPhone.setComboText(str.cells[1][2]);
                                                branchPhone.setComboValue(i);
                                            }
                                        }

                                        branchBuilding = excelDataForm.getCombo("IBB_Building");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchBuilding.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                branchBuilding.setComboText(str.cells[1][3]);
                                                branchBuilding.setComboValue(i);
                                            }
                                        }

                                        branchStreet = excelDataForm.getCombo("IBB_Street");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchStreet.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 4) {
                                                branchStreet.setComboText(str.cells[1][4]);
                                                branchStreet.setComboValue(i);
                                            }
                                        }
                                        
                                        branchPlace = excelDataForm.getCombo("IBB_Place");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchPlace.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 5) {
                                                branchPlace.setComboText(str.cells[1][5]);
                                                branchPlace.setComboValue(i);
                                            }
                                        }

                                        branchLocation = excelDataForm.getCombo("IBB_Location");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            branchLocation.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 6){
                                                branchLocation.setComboText(str.cells[1][6]);
                                                branchLocation.setComboValue(i);
                                            }
                                        }

                                        branchCity = excelDataForm.getCombo("IBB_City");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchCity.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 7){
                                                branchCity.setComboText(str.cells[1][7]);
                                                branchCity.setComboValue(i);
                                            }
                                        }

                                        branchState = excelDataForm.getCombo("IBB_State");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            branchState.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 8){
                                                branchState.setComboText(str.cells[1][8]);
                                                branchState.setComboValue(i);
                                            }
                                        }

                                        branchCountry = excelDataForm.getCombo("IBB_Country");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchCountry.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 9){
                                                branchCountry.setComboText(str.cells[1][9]);
                                                branchCountry.setComboValue(i);
                                            }
                                        }

                                        branchPincode = excelDataForm.getCombo("IBB_Pincode");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchPincode.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 10){
                                                branchPincode.setComboText(str.cells[1][10]);
                                                branchPincode.setComboValue(i);
                                            }
                                        }

                                        branchRemarks = excelDataForm.getCombo("IBB_Remarks");   
                                        for(i=0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            branchRemarks.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 11){
                                                branchRemarks.setComboText(str.cells[1][11]);
                                                branchRemarks.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "IBB_Button"){
                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/newBranchExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords = str.numRows - 1;  // except excel header row
                                                    errorJSArray = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";
                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+
                                                        errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+errorJSArray[i][5]+'","'+
                                                        errorJSArray[i][6]+'","'+errorJSArray[i][7]+'","'+errorJSArray[i][8]+'","'+
                                                        errorJSArray[i][9]+'","'+errorJSArray[i][10]+'","'+errorJSArray[i][11]+'","'+
                                                        errorJSArray[i][12]+'","'+errorJSArray[i][13]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridBrnch = menuImportBranchesLayout.cells("b").attachGrid();
                                                    errorGridBrnch.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridBrnch.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridBrnch.setHeader("Office Name, Company, Phone Number, Building Name, Street Name,Place,Location, City, State,Country, Pincode, Remarks,Error Columns, Error Remarks");
                                                    errorGridBrnch.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro"); 
                                                    errorGridBrnch.setInitWidths("100,50,50,50,50,50,50,50,50,50,50,50,50,50");
                                                    errorGridBrnch.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,left,left");
                                                    errorGridBrnch.init();
                                                    errorGridBrnch.parse(errorGridJsonData,"json");                                                    
                                                    menuImportBranchesLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridBrnch)">', height:30});
                                                    excelVault.clear();
                                                });
                                            }
                                        });
                                    }
                                    else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelVault.clear();
                                    }  
                                });
                            }
                            else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelVault.clear();
                            }
                        }
                    });
                });
            }  else {
                dhxMiddleBlockTabs.tabs("menuImportBnkBranches").setActive();
            }
        },
        importUsers : function() {
            if (!dhxMiddleBlockTabs.cells("menuImportUsers")) {
                dhxMiddleBlockTabs.addTab("menuImportUsers", "<img src='images/icon/user_24.png' style='margin-top:2px;width:15px;' />&nbsp;&nbsp;Import Users", 170);
                dhxMiddleBlockTabs.tabs("menuImportUsers").setActive();
                var menuImportUsersLayout = dhxMiddleBlockTabs.cells("menuImportUsers").attachLayout('3J');
                menuImportUsersLayout.cells("a").setText("New User Preview");
                menuImportUsersLayout.cells("b").setText("Error List");
                menuImportUsersLayout.cells("c").setText("Import Excel");
                menuImportUsersLayout.cells("a").setHeight(200);
                menuImportUsersLayout.cells("b").setWidth(600);

                var fileNewName = Math.random().toString(36).substring(5);
                var excelUserVault   =   menuImportUsersLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl:  preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportUsersLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportUserExcel.xls">Download Excel Format</a>', height:30});

                excelUserVault.attachEvent("onFileRemove",function() {
                   excelUserVault.setFilesLimit(1);
                   menuImportUsersLayout.cells("c").detachObject(); 
                });
               
                excelUserVault.attachEvent("onClear", function(file){
                   menuImportUsersLayout.cells("c").detachObject(); 
                });

                excelUserVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportUsersLayout.cells("b").detachObject(); 
                    menuImportUsersLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelUserVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData=excelUserVault.getData();
                    var ext =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"13"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) {
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelUserVault.clear();
                        }  else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportUsersLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/usrExcelForm.php"), function() {

                                    if(str.cells[0][0] == "Company" && str.cells[0][1] == "Branch" && 
                                        str.cells[0][2] == "User ID" && str.cells[0][3] == "User Password" 
                                        && str.cells[0][4] == "User ACL Type" && str.cells[0][5] == "Company Email" 
                                        && str.cells[0][6] == "First Name" && str.cells[0][7] == "Last Name" 
                                        && str.cells[0][8] == "Date of Join" && str.cells[0][9] == "Department" 
                                        && str.cells[0][10] == "Designation" && str.cells[0][11] == "Employee Status"  
                                        && str.cells[0][12] == "Reports To") {
                                        excelDataForm.setItemValue("IU_ExcelArray",data);
                                        userCompany = excelDataForm.getCombo("IU_Company");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userCompany.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                userCompany.setComboText(str.cells[1][0]);
                                                userCompany.setComboValue(i);
                                            }
                                        }

                                        userBranch = excelDataForm.getCombo("IU_Branch");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userBranch.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                userBranch.setComboText(str.cells[1][1]);
                                                userBranch.setComboValue(i);
                                            }
                                        }

                                        userID = excelDataForm.getCombo("IU_UserID");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userID.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                userID.setComboText(str.cells[1][2]);
                                                userID.setComboValue(i);
                                            }
                                        }

                                        userPassword = excelDataForm.getCombo("IU_UserPswd");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userPassword.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                userPassword.setComboText(str.cells[1][3]);
                                                userPassword.setComboValue(i);
                                            }
                                        }

                                        userACL = excelDataForm.getCombo("IU_ACL");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userACL.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 4) {
                                                userACL.setComboText(str.cells[1][4]);
                                                userACL.setComboValue(i);
                                            }
                                        }

                                        userEmail = excelDataForm.getCombo("IU_Email");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userEmail.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 5){
                                                userEmail.setComboText(str.cells[1][5]);
                                                userEmail.setComboValue(i);
                                            }
                                        }

                                        userFName = excelDataForm.getCombo("IU_FName");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userFName.addOption(i,str.cells[0][i]+ " "+optionText);

                                            if(i === 6){
                                                userFName.setComboText(str.cells[1][6]);
                                                userFName.setComboValue(i);
                                            }
                                        }

                                        userLName = excelDataForm.getCombo("IU_LName");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userLName.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 7){
                                                userLName.setComboText(str.cells[1][7]);
                                                userLName.setComboValue(i);
                                            }
                                        }

                                        userJoinDate = excelDataForm.getCombo("IU_JoinDate");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userJoinDate.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 8){
                                                userJoinDate.setComboText(str.cells[1][8]);
                                                userJoinDate.setComboValue(i);
                                            }
                                        }

                                        userDepartment = excelDataForm.getCombo("IU_Department");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userDepartment.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 9){
                                                userDepartment.setComboText(str.cells[1][9]);
                                                userDepartment.setComboValue(i);
                                            }
                                        }

                                        userDesignation = excelDataForm.getCombo("IU_Designation");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userDesignation.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 10){
                                                userDesignation.setComboText(str.cells[1][10]);
                                                userDesignation.setComboValue(i);
                                            }
                                        }

                                        employeeStatus = excelDataForm.getCombo("IU_EmpStatus");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            employeeStatus.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 11){
                                                employeeStatus.setComboText(str.cells[1][11]);
                                                employeeStatus.setComboValue(i);
                                            }
                                        }        

                                        userReportsTo = excelDataForm.getCombo("IU_ReportsTo");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userReportsTo.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 12){
                                                userReportsTo.setComboText(str.cells[1][12]);
                                                userReportsTo.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "IU_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/newUserExcel.php"),function(loader, response){ 
                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords      = str.numRows - 1;  // except excel header row
                                                    errorJSArray          = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";
                                                    var errorListLength   = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+
                                                        errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+errorJSArray[i][5]+'","'+
                                                        errorJSArray[i][6]+'","'+errorJSArray[i][7]+'","'+errorJSArray[i][8]+'","'+
                                                        errorJSArray[i][9]+'","'+errorJSArray[i][10]+'","'+errorJSArray[i][11]+'","'+
                                                        errorJSArray[i][12]+'","'+errorJSArray[i][13]+'","'+errorJSArray[i][14]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridUsr = menuImportUsersLayout.cells("b").attachGrid();
                                                    errorGridUsr.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridUsr.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridUsr.setHeader("Company, Branch, User ID, User Password, User ACL Type,Company Email, First Name, Last Name,Date of Join, Department, Designation,Employee Status,Reports To, Error Columns, Error Remarks");
                                                    errorGridUsr.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro"); 
                                                    errorGridUsr.setInitWidths("100,50,50,50,50,50,50,50,50,50,50,50,50,50,50");
                                                    errorGridUsr.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,left,left");
                                                    errorGridUsr.init();
                                                    errorGridUsr.parse(errorGridJsonData,"json");
                                                    menuImportUsersLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridUsr)">', height:30});
                                                    excelUserVault.clear();
                                                });
                                            }
                                        });
                                    }  else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelUserVault.clear();
                                    }  
                                });
                            }  else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelUserVault.clear();
                            }
                        }
                    });
                });
            }  else {
                dhxMiddleBlockTabs.tabs("menuImportUsers").setActive();
            }
        },
        importUsersDetails : function() {
            if (!dhxMiddleBlockTabs.cells("menuImportUserDetails")) {
                dhxMiddleBlockTabs.addTab("menuImportUserDetails", "<img src='images/icon/user_24.png' style='margin-top:2px;width:15px;' />&nbsp;&nbsp;Import Users Details", 190);
                dhxMiddleBlockTabs.tabs("menuImportUserDetails").setActive();
                var menuImportUserDetailsLayout = dhxMiddleBlockTabs.cells("menuImportUserDetails").attachLayout('3J');
                menuImportUserDetailsLayout.cells("a").setText("User Details Preview");
                menuImportUserDetailsLayout.cells("b").setText("Error List");
                menuImportUserDetailsLayout.cells("c").setText("Import Excel");
                menuImportUserDetailsLayout.cells("a").setHeight(200);
                menuImportUserDetailsLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelUserVault      =   menuImportUserDetailsLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl:  preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });
                
                menuImportUserDetailsLayout.cells("a").attachStatusBar({
                    text:'<div style="line-height : 20px !important;"><a href="plugins/excelTemplates/ImportUserDetailsExcel.xls">Download Excel Format</a></div><div><div style="color: red; height: 10px; float: left;line-height : 20px !important;">WARNING : </div><div style="white-space : pre-line !important;line-height : 20px !important;"> Make sure that all fields containing value(not empty). Otherwise current data will get replaced with empty values.</div></div>', 
                    height:60,
                });

                excelUserVault.attachEvent("onFileRemove",function() {
                   excelUserVault.setFilesLimit(1);
                   menuImportUserDetailsLayout.cells("c").detachObject(); 
                });
               
                excelUserVault.attachEvent("onClear", function(file){
                   menuImportUserDetailsLayout.cells("c").detachObject(); 
                });

                excelUserVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportUserDetailsLayout.cells("b").detachObject(); 
                    menuImportUserDetailsLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelUserVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelUserVault.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"35"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) {
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelUserVault.clear();
                        } else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportUserDetailsLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/userDetailsExcel.php"), function() {

                                    if(str.cells[0][0] == "User ID" && str.cells[0][1] == "Date Of Birth" && 
                                        str.cells[0][2] == "Gender" && str.cells[0][3] == "Address" 
                                        && str.cells[0][4] == "Country" && str.cells[0][5] == "State" 
                                        && str.cells[0][6] == "City" && str.cells[0][7] == "Personal Email ID" 
                                        && str.cells[0][8] == "Alternate Email ID" && str.cells[0][9] == "Mobile No:" 
                                        && str.cells[0][10] == "Alternate Mobile No:" && str.cells[0][11] == "Landline No:" 
                                        && str.cells[0][12] == "Alternate Landline No:" && str.cells[0][13] == "Father/Spouse Name"
                                        && str.cells[0][14] == "Blood Group" && str.cells[0][15] == "Hostel/Local Guardian Name"
                                        && str.cells[0][16] == "Hostel/Local Guardian Phone" && str.cells[0][17] == "Emergency Contact Person"
                                        && str.cells[0][18] == "Emergency Contact No:" && str.cells[0][19] == "Emergency Contact Relation"
                                        && str.cells[0][20] == "Passport No:" && str.cells[0][21] == "Highest Qualification"
                                        && str.cells[0][22] == "Specialization" && str.cells[0][23] == "Total Experience"
                                        && str.cells[0][24] == "Last Company Details" && str.cells[0][25] == "PAN No:" 
                                        && str.cells[0][26] == "Payment Mode" && str.cells[0][27] == "Company Bank Account No:"   
                                        && str.cells[0][28] == "Employee Bank Account No:" 
                                        && str.cells[0][29] == "Employee Bank Name" && str.cells[0][30] == "Employee Bank Branch" 
                                        && str.cells[0][31] == "PF No:" && str.cells[0][32] == "ESI No:" 
                                        && str.cells[0][33] == "Salary Structure" && str.cells[0][34] == "Gross Salary") {
                                        excelDataForm.setItemValue("IU_ExcelArray",data);
                                        username = excelDataForm.getCombo("username");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            username.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                username.setComboText(str.cells[1][0]);
                                                username.setComboValue(i);
                                            }
                                        }

                                        userDob = excelDataForm.getCombo("US_DOB");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userDob.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                userDob.setComboText(str.cells[1][1]);
                                                userDob.setComboValue(i);
                                            }
                                        }

                                        userGender = excelDataForm.getCombo("US_Gender");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userGender.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                userGender.setComboText(str.cells[1][2]);
                                                userGender.setComboValue(i);
                                            }
                                        }

                                        userAddress = excelDataForm.getCombo("US_Address");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userAddress.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                userAddress.setComboText(str.cells[1][3]);
                                                userAddress.setComboValue(i);
                                            }
                                        }

                                        userCountry = excelDataForm.getCombo("Country");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userCountry.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 4) {
                                                userCountry.setComboText(str.cells[1][4]);
                                                userCountry.setComboValue(i);
                                            }
                                        }

                                        userState = excelDataForm.getCombo("State");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userState.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 5){
                                                userState.setComboText(str.cells[1][5]);
                                                userState.setComboValue(i);
                                            }
                                        }

                                        userCity = excelDataForm.getCombo("City");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userCity.addOption(i,str.cells[0][i]+ " "+optionText);

                                            if(i === 6){
                                                userCity.setComboText(str.cells[1][6]);
                                                userCity.setComboValue(i);
                                            }
                                        }

                                        userPEmail = excelDataForm.getCombo("US_Pemail");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userPEmail.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 7){
                                                userPEmail.setComboText(str.cells[1][7]);
                                                userPEmail.setComboValue(i);
                                            }
                                        }

                                        userAltEmail = excelDataForm.getCombo("US_Altemail");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userAltEmail.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 8){
                                                userAltEmail.setComboText(str.cells[1][8]);
                                                userAltEmail.setComboValue(i);
                                            }
                                        }

                                        userMobile = excelDataForm.getCombo("US_Mobile");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userMobile.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 9){
                                                userMobile.setComboText(str.cells[1][9]);
                                                userMobile.setComboValue(i);
                                            }
                                        }

                                        userAltMobile = excelDataForm.getCombo("US_AltMobile");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userAltMobile.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 10){
                                                userAltMobile.setComboText(str.cells[1][10]);
                                                userAltMobile.setComboValue(i);
                                            }
                                        }

                                        userLandline = excelDataForm.getCombo("US_Landline");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            userLandline.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 11){
                                                userLandline.setComboText(str.cells[1][11]);
                                                userLandline.setComboValue(i);
                                            }
                                        }

                                        userAltLandline = excelDataForm.getCombo("US_AltLandline");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userAltLandline.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 12){
                                                userAltLandline.setComboText(str.cells[1][11]);
                                                userAltLandline.setComboValue(i);
                                            }
                                        }

                                        userRelative = excelDataForm.getCombo("US_Relative");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userRelative.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 13){
                                                userRelative.setComboText(str.cells[1][11]);
                                                userRelative.setComboValue(i);
                                            }
                                        }

                                        userBlood = excelDataForm.getCombo("US_Blood");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userBlood.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 14){
                                                userBlood.setComboText(str.cells[1][11]);
                                                userBlood.setComboValue(i);
                                            }
                                        }

                                        userGuardian = excelDataForm.getCombo("US_Guardian");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userGuardian.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 15){
                                                userGuardian.setComboText(str.cells[1][11]);
                                                userGuardian.setComboValue(i);
                                            }
                                        }

                                        userGuardianphone = excelDataForm.getCombo("US_Guardianphone");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userGuardianphone.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 16){
                                                userGuardianphone.setComboText(str.cells[1][11]);
                                                userGuardianphone.setComboValue(i);
                                            }
                                        }

                                        userEmergencyperson = excelDataForm.getCombo("US_Emergencyperson");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userEmergencyperson.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 17){
                                                userEmergencyperson.setComboText(str.cells[1][11]);
                                                userEmergencyperson.setComboValue(i);
                                            }
                                        }

                                        userEmergencyNo = excelDataForm.getCombo("US_Emergencynumber");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userEmergencyNo.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 18){
                                                userEmergencyNo.setComboText(str.cells[1][11]);
                                                userEmergencyNo.setComboValue(i);
                                            }
                                        }

                                        userEmergencyRelation = excelDataForm.getCombo("US_Emergencyrelation");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userEmergencyRelation.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 19){
                                                userEmergencyRelation.setComboText(str.cells[1][11]);
                                                userEmergencyRelation.setComboValue(i);
                                            }
                                        }

                                        userPassport = excelDataForm.getCombo("US_Passport");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userPassport.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 20){
                                                userPassport.setComboText(str.cells[1][11]);
                                                userPassport.setComboValue(i);
                                            }
                                        }

                                        userQualification = excelDataForm.getCombo("US_Qualification");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userQualification.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 21){
                                                userQualification.setComboText(str.cells[1][11]);
                                                userQualification.setComboValue(i);
                                            }
                                        }

                                        userSpecialization = excelDataForm.getCombo("US_Specialization");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userSpecialization.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 22){
                                                userSpecialization.setComboText(str.cells[1][11]);
                                                userSpecialization.setComboValue(i);
                                            }
                                        }

                                        userExperience = excelDataForm.getCombo("US_Experience");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userExperience.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 23){
                                                userExperience.setComboText(str.cells[1][11]);
                                                userExperience.setComboValue(i);
                                            }
                                        }

                                        userLastEmployee = excelDataForm.getCombo("US_LastEmployee");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userLastEmployee.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 24){
                                                userLastEmployee.setComboText(str.cells[1][11]);
                                                userLastEmployee.setComboValue(i);
                                            }
                                        }

                                        userPAN = excelDataForm.getCombo("PAN");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userPAN.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 25){
                                                userPAN.setComboText(str.cells[1][11]);
                                                userPAN.setComboValue(i);
                                            }
                                        }

                                        userPayMode = excelDataForm.getCombo("Pay_Mode");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userPayMode.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 26){
                                                userPayMode.setComboText(str.cells[1][11]);
                                                userPayMode.setComboValue(i);
                                            }
                                        }

                                        userCmpAC = excelDataForm.getCombo("BA_Id");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userCmpAC.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 27){
                                                userCmpAC.setComboText(str.cells[1][11]);
                                                userCmpAC.setComboValue(i);
                                            }
                                        }

                                        userAccNo = excelDataForm.getCombo("US_AccNo");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userAccNo.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 28){
                                                userAccNo.setComboText(str.cells[1][11]);
                                                userAccNo.setComboValue(i);
                                            }
                                        }

                                        userBankName = excelDataForm.getCombo("US_Bankname");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userBankName.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 29){
                                                userBankName.setComboText(str.cells[1][11]);
                                                userBankName.setComboValue(i);
                                            }
                                        }

                                        userBankBranch = excelDataForm.getCombo("US_BankBranch");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userBankBranch.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 30){
                                                userBankBranch.setComboText(str.cells[1][11]);
                                                userBankBranch.setComboValue(i);
                                            }
                                        }

                                        userPFNo = excelDataForm.getCombo("US_PFNo");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userPFNo.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 31){
                                                userPFNo.setComboText(str.cells[1][11]);
                                                userPFNo.setComboValue(i);
                                            }
                                        }

                                        userESI = excelDataForm.getCombo("US_ESI");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userESI.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 32){
                                                userESI.setComboText(str.cells[1][11]);
                                                userESI.setComboValue(i);
                                            }
                                        }

                                        userSalarySt = excelDataForm.getCombo("Salary_Struct");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userSalarySt.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 33){
                                                userSalarySt.setComboText(str.cells[1][11]);
                                                userSalarySt.setComboValue(i);
                                            }
                                        }

                                        userGrossSal = excelDataForm.getCombo("US_GrossSal");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            userGrossSal.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 34){
                                                userGrossSal.setComboText(str.cells[1][11]);
                                                userGrossSal.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){
                                            if(id   ==  "IU_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/userDetailsExcel.php"),function(loader, response){ 

                                                preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords      = str.numRows - 1;  // except excel header row
                                                    errorJSArray          = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";
                                                    var errorListLength   = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+
                                                        errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+errorJSArray[i][5]+'","'+
                                                        errorJSArray[i][6]+'","'+errorJSArray[i][7]+'","'+errorJSArray[i][8]+'","'+
                                                        errorJSArray[i][9]+'","'+errorJSArray[i][10]+'","'+errorJSArray[i][11]+'","'+
                                                        errorJSArray[i][12]+'","'+errorJSArray[i][13]+'","'+errorJSArray[i][14]+'","'+
                                                        errorJSArray[i][15]+'","'+errorJSArray[i][16]+'","'+errorJSArray[i][17]+'","'+        
                                                        errorJSArray[i][18]+'","'+errorJSArray[i][19]+'","'+errorJSArray[i][20]+'","'+        
                                                        errorJSArray[i][21]+'","'+errorJSArray[i][22]+'","'+errorJSArray[i][23]+'","'+        
                                                        errorJSArray[i][24]+'","'+errorJSArray[i][25]+'","'+errorJSArray[i][26]+'","'+
                                                        errorJSArray[i][27]+'","'+errorJSArray[i][28]+'","'+errorJSArray[i][29]+'","'+
                                                        errorJSArray[i][30]+'","'+errorJSArray[i][31]+'","'+errorJSArray[i][32]+'","'+
                                                        errorJSArray[i][33]+'","'+errorJSArray[i][34]+'","'+errorJSArray[i][35]+'","'+
                                                        errorJSArray[i][36]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridUsrDetail = menuImportUserDetailsLayout.cells("b").attachGrid();
                                                    errorGridUsrDetail.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridUsrDetail.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridUsrDetail.setHeader("User ID, Date Of Birth, Gender, Address, Country,State, City, Personal Email ID,Alternate Email ID, Mobile No:, Alternate Mobile No:,Landline No:,Alternate Landline No:,Father/Spouse Name,Blood Group,Hostel/Local Guardian Name,Hostel/Local Guardian Phone,Emergency Contact Person,Emergency Contact No:,Emergency Contact Relation,Passport No:,Highest Qualification,Specialization,Total Experience,Last Company Details,PAN No:,Payment Mode,Company Bank Account No:, Employee Bank Account No:,Employee Bank Name,Employee Bank Branch,PF No:,ESI No:,Salary Structure,Gross Salary,Error Columns, Error Remarks");
                                                    errorGridUsrDetail.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro"); 
                                                    errorGridUsrDetail.setInitWidths("100,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50");
                                                    errorGridUsrDetail.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,left,left");
                                                    errorGridUsrDetail.init();
                                                    errorGridUsrDetail.parse(errorGridJsonData,"json");
                                                    menuImportUserDetailsLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridUsrDetail)">', height:30});
                                                    excelUserVault.clear();
                                              });
                                            }
                                        });
                                    } else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelUserVault.clear();
                                    }  
                                });
                            }  else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelUserVault.clear();
                            }
                        }
                    });
                });
            }
            else {
                dhxMiddleBlockTabs.tabs("menuImportUserDetails").setActive();
            }
        },
        importItems : function() {
            if (!dhxMiddleBlockTabs.cells("menuImportItems")) {
                dhxMiddleBlockTabs.addTab("menuImportItems", "<img src='images/icon/item_20.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Items", 170);
                dhxMiddleBlockTabs.tabs("menuImportItems").setActive();
                var menuImportItemsLayout = dhxMiddleBlockTabs.cells("menuImportItems").attachLayout('3J');
                menuImportItemsLayout.cells("a").setText("New Item Preview");
                menuImportItemsLayout.cells("b").setText("Error List");
                menuImportItemsLayout.cells("c").setText("Import Excel");
                menuImportItemsLayout.cells("a").setHeight(200);
                menuImportItemsLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelItemVault      =   menuImportItemsLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportItemsLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportItemExcel.xls">Download Excel Format</a>', height:30});

                excelItemVault.attachEvent("onFileRemove",function() {
                   excelItemVault.setFilesLimit(1);
                   menuImportItemsLayout.cells("c").detachObject(); 
                });
               
                excelItemVault.attachEvent("onClear", function(file){
                   menuImportItemsLayout.cells("c").detachObject(); 
                });

                excelItemVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportItemsLayout.cells("b").detachObject(); 
                    menuImportItemsLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelItemVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelItemVault.getData();
                    var ext                 = uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"5"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) {
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelItemVault.clear();
                        }
                        else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data);  
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportItemsLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/itemExcelForm.php"), function() {

                                    if(str.cells[0][0] == "Item Name" && str.cells[0][1] == "Subhead" && 
                                        str.cells[0][2] == "Remarks" && str.cells[0][3] == "Business Entry" 
                                        && str.cells[0][4] == "Internal Transfers") {
                                        excelDataForm.setItemValue("IU_ExcelArray",data);
                                        itemName = excelDataForm.getCombo("IT_Name");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            itemName.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                itemName.setComboText(str.cells[1][0]);
                                                itemName.setComboValue(i);
                                            }
                                        }

                                        itemSubhead = excelDataForm.getCombo("SH_Name");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            itemSubhead.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                itemSubhead.setComboText(str.cells[1][1]);
                                                itemSubhead.setComboValue(i);
                                            }
                                        }

                                        itemRemarks = excelDataForm.getCombo("IT_Comments");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            itemRemarks.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                itemRemarks.setComboText(str.cells[1][2]);
                                                itemRemarks.setComboValue(i);
                                            }
                                        }

                                        itemBusinessEntry = excelDataForm.getCombo("IT_Business");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            } else {
                                                var optionText = "";
                                            }
                                            itemBusinessEntry.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                itemBusinessEntry.setComboText(str.cells[1][3]);
                                                itemBusinessEntry.setComboValue(i);
                                            }
                                        }

                                        itemTransfers = excelDataForm.getCombo("IT_Transfers");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            itemTransfers.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 4) {
                                                itemTransfers.setComboText(str.cells[1][4]);
                                                itemTransfers.setComboValue(i);
                                            }
                                        }
                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "IU_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/newItemExcel.php"),function(loader, response){ 
                                                  preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords = str.numRows - 1;  // except excel header row
                                                    errorJSArray     = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+
                                                        errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+
                                                        errorJSArray[i][5]+'","'+errorJSArray[i][6]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridItems = menuImportItemsLayout.cells("b").attachGrid();
                                                    errorGridItems.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridItems.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridItems.setHeader("Item Name, Subhead, Remarks, Business Entry, Internal Transfers, Error Columns, Error Remarks");
                                                    errorGridItems.setColTypes("ro,ro,ro,ro,ro,ro,ro"); 
                                                    errorGridItems.setInitWidths("100,180,120,100,100,100,100");
                                                    errorGridItems.setColAlign("center,center,center,center,center,center,center");
                                                    errorGridItems.init();
                                                    errorGridItems.parse(errorGridJsonData,"json");
                                                    menuImportItemsLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridItems)">', height:30});
                                                    excelItemVault.clear();
                                                });
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelItemVault.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelItemVault.clear();
                            }
                        }
                    });
                });
            }  else {
                dhxMiddleBlockTabs.tabs("menuImportItems").setActive();
            }
        },
        importDescriptions : function() {
            if (!dhxMiddleBlockTabs.cells("menuImportDescriptions")) {
                dhxMiddleBlockTabs.addTab("menuImportDescriptions", "<img src='images/icon/item_20.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Descriptions", 170);
                dhxMiddleBlockTabs.tabs("menuImportDescriptions").setActive();
                var menuImportDescriptionLayout = dhxMiddleBlockTabs.cells("menuImportDescriptions").attachLayout('3J');
                menuImportDescriptionLayout.cells("a").setText("New Description Preview");
                menuImportDescriptionLayout.cells("b").setText("Error List");
                menuImportDescriptionLayout.cells("c").setText("Import Excel");
                menuImportDescriptionLayout.cells("a").setHeight(200);
                menuImportDescriptionLayout.cells("b").setWidth(600);

                var fileNewName             = Math.random().toString(36).substring(5);
                var excelDescriptionVault   =   menuImportDescriptionLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportDescriptionLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportDescriptionExcel.xls">Download Excel Format</a>', height:30});

                excelDescriptionVault.attachEvent("onFileRemove",function() {
                   excelDescriptionVault.setFilesLimit(1);
                   menuImportDescriptionLayout.cells("c").detachObject(); 
                });
               
                excelDescriptionVault.attachEvent("onClear", function(file){
                   menuImportDescriptionLayout.cells("c").detachObject(); 
                });

                excelDescriptionVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportDescriptionLayout.cells("b").detachObject(); 
                    menuImportDescriptionLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelDescriptionVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData = excelDescriptionVault.getData();
                    var ext              = uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"2"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) { 
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelDescriptionVault.clear();
                        }   else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportDescriptionLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/descriptionExcelForm.php"), function() {

                                    if(str.cells[0][0] == "Description" && str.cells[0][1] == "Item Name") {
                                        excelDataForm.setItemValue("DS_ExcelArray",data);
                                        descriptionName = excelDataForm.getCombo("DS_Description");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            descriptionName.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                descriptionName.setComboText(str.cells[1][0]);
                                                descriptionName.setComboValue(i);
                                            }
                                        }

                                        itemName = excelDataForm.getCombo("IT_Name");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            itemName.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                itemName.setComboText(str.cells[1][1]);
                                                itemName.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "DS_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/newDescriptionExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords = str.numRows - 1;  // except excel header row
                                                    errorJSArray = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'"   ]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridDesc = menuImportDescriptionLayout.cells("b").attachGrid();
                                                    errorGridDesc.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridDesc.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridDesc.setHeader("Description,Item Name,Error Columns,Error Remarks");
                                                    errorGridDesc.setColTypes("ro,ro,ro,ro"); 
                                                    errorGridDesc.setInitWidths("300,300,200,300");
                                                    errorGridDesc.setColAlign("left,left,left,left");
                                                    errorGridDesc.init();
                                                    errorGridDesc.parse(errorGridJsonData,"json");
                                                    menuImportDescriptionLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridDesc)">', height:30});
                                                    excelDescriptionVault.clear();
                                                });
                                            }
                                        });
                                    }  else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelDescriptionVault.clear();
                                    }  
                                });
                            } else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDescriptionVault.clear();
                            }
                        }
                    });
                });
            }  else {
                dhxMiddleBlockTabs.tabs("menuImportDescriptions").setActive();
            }
        },
        importLeaves: function()  {
            if (!dhxMiddleBlockTabs.cells("menuImportLeaves")) {
                dhxMiddleBlockTabs.addTab("menuImportLeaves", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Leaves", 170);
                dhxMiddleBlockTabs.tabs("menuImportLeaves").setActive();
                var menuImportLeavesLayout = dhxMiddleBlockTabs.cells("menuImportLeaves").attachLayout('3J');
                menuImportLeavesLayout.cells("a").setText("New Leave Preview");
                menuImportLeavesLayout.cells("b").setText("Error List");
                menuImportLeavesLayout.cells("c").setText("Import Excel");
                menuImportLeavesLayout.cells("a").setHeight(200);
                menuImportLeavesLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelLeaveVault     =   menuImportLeavesLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportLeavesLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportLeaveExcel.xls">Download Excel Format</a>', height:30});

                excelLeaveVault.attachEvent("onFileRemove",function() {
                   excelLeaveVault.setFilesLimit(1);
                   menuImportLeavesLayout.cells("c").detachObject(); 
                });
               
                excelLeaveVault.attachEvent("onClear", function(file){
                   menuImportLeavesLayout.cells("c").detachObject(); 
                });

                excelLeaveVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportLeavesLayout.cells("b").detachObject(); 
                    menuImportLeavesLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelLeaveVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelLeaveVault.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"4"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) { 
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelLeaveVault.clear();
                        }   else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportLeavesLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/leaveExcelForm.php"), function() {

                                    if(str.cells[0][0] == "User ID" && str.cells[0][1] == "Leave Type" && str.cells[0][2] == "Date" && str.cells[0][3] == "Session") {
                                        excelDataForm.setItemValue("LT_ExcelArray",data);
                                        userID = excelDataForm.getCombo("US_Id");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userID.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                userID.setComboText(str.cells[1][0]);
                                                userID.setComboValue(i);
                                            }
                                        }

                                        leaveType = excelDataForm.getCombo("LT_Name");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            leaveType.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                leaveType.setComboText(str.cells[1][1]);
                                                leaveType.setComboValue(i);
                                            }
                                        }
                                        
                                        leaveDate = excelDataForm.getCombo("Date");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            leaveDate.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                leaveDate.setComboText(str.cells[1][2]);
                                                leaveDate.setComboValue(i);
                                            }
                                        }
                                        leaveSession = excelDataForm.getCombo("Session");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            leaveSession.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                leaveSession.setComboText(str.cells[1][2]);
                                                leaveSession.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "LT_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/leaveExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords    = str.numRows - 1;  // except excel header row
                                                    errorJSArray        = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+errorJSArray[i][5]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridLeaves = menuImportLeavesLayout.cells("b").attachGrid();
                                                    errorGridLeaves.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridLeaves.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridLeaves.setHeader("User ID,Leave Type,Date,Session,Error Columns, Error Remarks");
                                                    errorGridLeaves.setColTypes("ro,ro,ro,ro,ro,ro"); 
                                                    errorGridLeaves.setInitWidths("200,200,200,200,200,200");
                                                    errorGridLeaves.setColAlign("left,left,left,left,left,left");
                                                    errorGridLeaves.init();
                                                    errorGridLeaves.parse(errorGridJsonData,"json");
                                                    menuImportLeavesLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridLeaves)">', height:30});
                                                    excelLeaveVault.clear();
                                                });
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelLeaveVault.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelLeaveVault.clear();
                            }
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuImportLeaves").setActive();
            }
        },
        importTrackSubProcess : function() {
            if (!dhxMiddleBlockTabs.cells("menuImportSubProcess")) {
                dhxMiddleBlockTabs.addTab("menuImportSubProcess", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Track Sub Process", 210);
                dhxMiddleBlockTabs.tabs("menuImportSubProcess").setActive();
                var menuImportSubProcessLayout = dhxMiddleBlockTabs.cells("menuImportSubProcess").attachLayout('3J');
                menuImportSubProcessLayout.cells("a").setText("New Sub Process Preview");
                menuImportSubProcessLayout.cells("b").setText("Error List");
                menuImportSubProcessLayout.cells("c").setText("Import Excel");
                menuImportSubProcessLayout.cells("a").setHeight(200);
                menuImportSubProcessLayout.cells("b").setWidth(800);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelLeaveVault     = menuImportSubProcessLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportSubProcessLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportSubprocessExcel.xls">Download Excel Format</a>', height:30});

                excelLeaveVault.attachEvent("onFileRemove",function() {
                   excelLeaveVault.setFilesLimit(1);
                   menuImportSubProcessLayout.cells("c").detachObject(); 
                });
               
                excelLeaveVault.attachEvent("onClear", function(file){
                   menuImportSubProcessLayout.cells("c").detachObject(); 
                });

                excelLeaveVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportSubProcessLayout.cells("b").detachObject(); 
                    menuImportSubProcessLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name); 
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelLeaveVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelLeaveVault.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"23"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) {
                        if(data == "") { 
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelLeaveVault.clear();
                        }   else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportSubProcessLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/trackSubProcessExcelForm.php"), function() {

                                    if(str.cells[0][0] == "Main Process" && str.cells[0][1] == "Sub Process" && 
                                       str.cells[0][2] == "Street" && str.cells[0][3] == "Place" && 
                                       str.cells[0][4] == "Location" && str.cells[0][5] == "City" && 
                                       str.cells[0][6] == "State" && str.cells[0][7] == "Country" && str.cells[0][8] == "Pincode" && 
                                       str.cells[0][9] == "Statutory Amount – Normal" && str.cells[0][10] == "Statutory  Amount – Urgent" && 
                                       str.cells[0][11] == "Extra Amount – Normal" && str.cells[0][12] == "Extra Amount – Urgent" && 
                                       str.cells[0][13] == "Courier Charge – Normal" && str.cells[0][14] == "Courier Charge – Urgent" && 
                                       str.cells[0][15] == "Travelling Expense – Normal" && str.cells[0][16] == "Travelling Expense – Urgent" && 
                                       str.cells[0][17] == "Manpower Charge – Normal" && str.cells[0][18] == "Manpower Charge – Urgent" && 
                                       str.cells[0][19] == "Company Service – Normal" && str.cells[0][20] == "Company Service – Urgent" &&
                                       str.cells[0][21] == "From Year" && str.cells[0][22] == "To Year") {
                                        
                                        excelDataForm.setItemValue("TK_ExcelArray",data);
                                        
                                        var TK_ComboArray = ["TK_Main","TK_SubProcess","TK_Street","TK_Place","TK_Location","TK_City","TK_State","TK_Country","TK_Pincode","TK_StatutoryAmt","TK_StatutoryUAmt","TK_ExtraNAmt","TK_ExtraUAmt","TK_CourierNAmt","TK_CourierUAmt","TK_TravellingNAmt","TK_TravellingUAmt","TK_ManpowerNAmt","TK_ManpowerUAmt","TK_ServiceNAmt","TK_ServiceUAmt","TK_FromYear","TK_ToYear"];
                                        
                                        $.each(TK_ComboArray, function( index, value ) {
                                            var comboName  = value+'_Combo';
                                            comboName = excelDataForm.getCombo(value);   
                                            for(i = 0; i < str.numCols; i++) {
                                                if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                    var optionText = "("+str.cells[1][i]+")";
                                                }  else {
                                                    var optionText = "";
                                                }
                                                comboName.addOption(i,str.cells[0][i]+" "+optionText);
                                                if(i === index) {
                                                    comboName.setComboText(str.cells[1][index]);
                                                    comboName.setComboValue(i);
                                                }
                                            }
                                        });

//                                        mainProcess = excelDataForm.getCombo("TK_Main");   
//                                        for(i = 0; i < str.numCols; i++) {
//                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
//                                                var optionText = "("+str.cells[1][i]+")";
//                                            }  else {
//                                                var optionText = "";
//                                            }
//                                            mainProcess.addOption(i,str.cells[0][i]+ " "+optionText);
//                                            if(i === 0) {
//                                                mainProcess.setComboText(str.cells[1][0]);
//                                                mainProcess.setComboValue(i);
//                                            }
//                                        }
//
//                                        subProcess = excelDataForm.getCombo("TK_SubProcess");   
//                                        for(i = 0; i < str.numCols; i++) {
//                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
//                                                var optionText = "("+str.cells[1][i]+")";
//                                            }   else {
//                                                var optionText = "";
//                                            }
//                                            subProcess.addOption(i,str.cells[0][i]+ " "+optionText);
//                                            if(i === 1) {
//                                                subProcess.setComboText(str.cells[1][1]);
//                                                subProcess.setComboValue(i);
//                                            }
//                                        }
//                                        
//                                        statutoryAmt = excelDataForm.getCombo("TK_StatutoryAmt");   
//                                        for(i = 0; i < str.numCols; i++) {
//                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
//                                                var optionText = "("+str.cells[1][i]+")";
//                                            }   else {
//                                                var optionText = "";
//                                            }
//                                            statutoryAmt.addOption(i,str.cells[0][i]+ " "+optionText);
//                                            if(i === 2) {
//                                                statutoryAmt.setComboText(str.cells[1][2]);
//                                                statutoryAmt.setComboValue(i);
//                                            }
//                                        }
//
//                                        extraAmt = excelDataForm.getCombo("TK_ExtraAmt");   
//                                        for(i = 0; i < str.numCols; i++) {
//                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
//                                                var optionText = "("+str.cells[1][i]+")";
//                                            }   else {
//                                                var optionText = "";
//                                            }
//                                            extraAmt.addOption(i,str.cells[0][i]+ " "+optionText);
//                                            if(i === 3) {
//                                                extraAmt.setComboText(str.cells[1][3]);
//                                                extraAmt.setComboValue(i);
//                                            }
//                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "TK_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/trackSubProcessExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);//console.log(response);
                                                    var totalRecords    = str.numRows - 1;  // except excel header row
                                                    errorJSArray        = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";
                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }
                                                    
                                                    var grdColLenght = parseInt(str.numCols)+2;
                                                    
                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:[" ;
                                                        for(j = 0; j < grdColLenght ; j++) {
                                                            errorGridJsonData += '"'+errorJSArray[i][j]+'",' ;
                                                        }
                                                        errorGridJsonData = errorGridJsonData.replace(/,\s*$/, "");
                                                        errorGridJsonData += ']}';
//                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'","'+errorJSArray[i][4]+'","'+errorJSArray[i][5]+'"]}';
                                                    }
                                                    
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridSubProcess = menuImportSubProcessLayout.cells("b").attachGrid();
                                                    errorGridSubProcess.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridSubProcess.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridSubProcess.setHeader("Main Process,Sub Process,Street,Place,Location,City,State,Country,Pincode,Statutory Amount, Statutory Amount - Urgent, Extra Amount - Normal, Extra Amount - Urgent, Courier Charge - Normal, Courier Charge - Urgent, Travelling Expense - Normal , Travelling Expense - Urgent,Manpower Charge - Normal, Manpower Charge - Urgent, Service Charge - Normal, Service Charge - Urgent,From Year, To Year,Error Columns, Error Remarks");
                                                    errorGridSubProcess.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro"); 
                                                    errorGridSubProcess.setInitWidths("200,200,200,150,150,200,250,200,200,200,150,150,150,200,250,200,200,200,150,150,200,100,100,250,300");
                                                    errorGridSubProcess.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");
                                                    errorGridSubProcess.init();
                                                    errorGridSubProcess.enableMultiline(true);
                                                    errorGridSubProcess.parse(errorGridJsonData,"json");
                                                    menuImportSubProcessLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridSubProcess)">', height:30});
                                                    excelLeaveVault.clear();
                                                });
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelLeaveVault.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelLeaveVault.clear();
                            }
                        }
                    });
                });
            } else {
                dhxMiddleBlockTabs.tabs("menuImportSubProcess").setActive();
            }
        },salaryHistory: function()  {
            if (!dhxMiddleBlockTabs.cells("menusalaryHistory")) {
                dhxMiddleBlockTabs.addTab("menusalaryHistory", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Salary History", 170);
                dhxMiddleBlockTabs.tabs("menusalaryHistory").setActive();
                var menuImportSalHistLayout = dhxMiddleBlockTabs.cells("menusalaryHistory").attachLayout('3J');
                menuImportSalHistLayout.cells("a").setText("Salary History Preview");
                menuImportSalHistLayout.cells("b").setText("Error List");
                menuImportSalHistLayout.cells("c").setText("Import Excel");
                menuImportSalHistLayout.cells("a").setHeight(200);
                menuImportSalHistLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelSalHistVault     =   menuImportSalHistLayout.cells("a").attachVault({
                    parent: document.body,             // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });

                menuImportSalHistLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportSalHistoryExcel.xls">Download Excel Format</a>', height:30});

                excelSalHistVault.attachEvent("onFileRemove",function() {
                   excelSalHistVault.setFilesLimit(1);
                   menuImportSalHistLayout.cells("c").detachObject(); 
                });
               
                excelSalHistVault.attachEvent("onClear", function(file){
                   menuImportSalHistLayout.cells("c").detachObject(); 
                });

                excelSalHistVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImportSalHistLayout.cells("b").detachObject(); 
                    menuImportSalHistLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                
                excelSalHistVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelSalHistVault.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") 
                        ext = "xls";
                    
                    var fileData = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"3"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) { 
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelSalHistVault.clear();
                        }   else {
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImportSalHistLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/salhistExcelForm.php"), function() {

                                    if(str.cells[0][0] == "User ID" && str.cells[0][1] == "Salary" && str.cells[0][2] == "Date") {
                                        excelDataForm.setItemValue("SH_ExcelArray",data);
                                        userID = excelDataForm.getCombo("US_Id");   

                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userID.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                userID.setComboText(str.cells[1][0]);
                                                userID.setComboValue(i);
                                            }
                                        }

                                        histamt = excelDataForm.getCombo("SHist_Amt");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            histamt.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                histamt.setComboText(str.cells[1][1]);
                                                histamt.setComboValue(i);
                                            }
                                        }
                                        
                                        hisDate = excelDataForm.getCombo("Date");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            hisDate.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                hisDate.setComboText(str.cells[1][2]);
                                                hisDate.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "SH_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/salHistExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords    = str.numRows - 1;  // except excel header row
                                                    errorJSArray        = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'","'+errorJSArray[i][4]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridSalHist = menuImportSalHistLayout.cells("b").attachGrid();
                                                    errorGridSalHist.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridSalHist.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridSalHist.setHeader("User Id,Salary ,Date,Error Columns, Error Remarks");
                                                    errorGridSalHist.setColTypes("ro,ro,ro,ro,ro"); 
                                                    errorGridSalHist.setInitWidths("200,200,200,200,200");
                                                    errorGridSalHist.setColAlign("left,left,left,left,left");
                                                    errorGridSalHist.init();
                                                    errorGridSalHist.parse(errorGridJsonData,"json");
                                                    menuImportSalHistLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridSalHist)">', height:30});
                                                    excelSalHistVault.clear();
                                                });
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelSalHistVault.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelSalHistVault.clear();
                            }
                        }
                    });
                });
           
         } else {
                dhxMiddleBlockTabs.tabs("menusalaryHistory").setActive();
            }
        },
        // 21-03-2025 - updated salary imported into database
        salaryUpdated: function()  {
            if (!dhxMiddleBlockTabs.cells("menusalaryUpdated")) {
                dhxMiddleBlockTabs.addTab("menusalaryUpdated", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Updated Salary", 170);
                dhxMiddleBlockTabs.tabs("menusalaryUpdated").setActive();
                var menuImpSalUpdLayout = dhxMiddleBlockTabs.cells("menusalaryUpdated").attachLayout('3J');
                menuImpSalUpdLayout.cells("a").setText("Salary Updated Preview");
                menuImpSalUpdLayout.cells("b").setText("Error List");
                menuImpSalUpdLayout.cells("c").setText("Import Excel");
                menuImpSalUpdLayout.cells("a").setHeight(200);
                menuImpSalUpdLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelSalUpdVault    =   menuImpSalUpdLayout.cells("a").attachVault({
                    parent: document.body,    // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });
                // above section common for all uploads
                // samle file links
                menuImpSalUpdLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportUpdatedSalary.xls">Download Excel Format</a>', height:30});
                //common file uploading checking and clearing
                excelSalUpdVault.attachEvent("onFileRemove",function() {
                   excelSalUpdVault.setFilesLimit(1);
                   menuImpSalUpdLayout.cells("c").detachObject(); 
                });               
                excelSalUpdVault.attachEvent("onClear", function(file){
                   menuImpSalUpdLayout.cells("c").detachObject(); 
                });
                excelSalUpdVault.attachEvent("onBeforeFileAdd", function(file){
                    menuImpSalUpdLayout.cells("b").detachObject(); 
                    menuImpSalUpdLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                excelSalUpdVault.attachEvent("onUploadComplete", function(){
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelSalUpdVault.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") {
                        ext         = "xls";
                    }                    
                    var fileData    = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"4"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) { 
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelSalUpdVault.clear();
                        }  else { // after upload success then start porcessing
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = menuImpSalUpdLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/salUpdExcelForm.php"), function() {

                                    if(str.cells[0][0] == "User ID" && str.cells[0][1] == "Salary" && str.cells[0][2] == "Name" && str.cells[0][3] == "Month") {
                                        excelDataForm.setItemValue("SU_ExcelArray",data);
                                        userID = excelDataForm.getCombo("US_Id");   
                                        //excel values in proper columns (load into combo for manuval check)
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            userID.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                userID.setComboText(str.cells[1][0]);
                                                userID.setComboValue(i);
                                            }
                                        }
                                        supdamt = excelDataForm.getCombo("SUpd_Amt");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            supdamt.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                supdamt.setComboText(str.cells[1][1]);
                                                supdamt.setComboValue(i);
                                            }
                                        }                                        
                                        updname = excelDataForm.getCombo("Su_Name");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            updname.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                updname.setComboText(str.cells[1][2]);
                                                updname.setComboValue(i);
                                            }
                                        }

                                        updmonth = excelDataForm.getCombo("Su_Month");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            updmonth.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                updmonth.setComboText(str.cells[1][3]);
                                                updmonth.setComboValue(i);
                                            }
                                        }

                                        excelDataForm.attachEvent("onButtonClick",function(id){

                                            if(id   ==  "SU_Button"){

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/salUpdtExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords    = str.numRows - 1;  // except excel header row
                                                    errorJSArray        = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'","'+errorJSArray[i][4]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridSalUpLst = menuImpSalUpdLayout.cells("b").attachGrid();
                                                    errorGridSalUpLst.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridSalUpLst.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridSalUpLst.setHeader("User Id,Salary,Full Name,Error Columns, Error Remarks");
                                                    errorGridSalUpLst.setColTypes("ro,ro,ro,ro,ro"); 
                                                    errorGridSalUpLst.setInitWidths("200,200,200,200,200");
                                                    errorGridSalUpLst.setColAlign("left,left,left,left,left");
                                                    errorGridSalUpLst.init();
                                                    errorGridSalUpLst.parse(errorGridJsonData,"json");
                                                    menuImpSalUpdLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridSalUpLst)">', height:30});
                                                    excelSalUpdVault.clear();
                                                });
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({text: "Please upload an excel file of provided format"});
                                        excelSalUpdVault.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelSalUpdVault.clear();
                            }
                        }
                    });
                });               

            } else {
                dhxMiddleBlockTabs.tabs("menusalaryUpdated").setActive();
            }
        },
        // salary updated excel uploaded into the database (on by one)
        // take the back up and update it
        // Completed on 21-03-2025
        //custom p and l data uploaded from excel - 07-01-2026
        acc_PandLData : function() {
            if (!dhxMiddleBlockTabs.cells("acc_PandLData")) {
                dhxMiddleBlockTabs.addTab("acc_PandLData", "<img src='images/icon/user_icon_12.png' style='margin-top:2px;width:10px;' />&nbsp;&nbsp;Import Custom Account Data", 220);
                dhxMiddleBlockTabs.tabs("acc_PandLData").setActive();
                var custPandLDtLayout = dhxMiddleBlockTabs.cells("acc_PandLData").attachLayout('3J');
                custPandLDtLayout.cells("a").setText("Custom Account Data Preview");
                custPandLDtLayout.cells("b").setText("Error List");
                custPandLDtLayout.cells("c").setText("Import Excel");
                custPandLDtLayout.cells("a").setHeight(200);
                custPandLDtLayout.cells("b").setWidth(600);

                var fileNewName         = Math.random().toString(36).substring(5);
                var excelcustPandLDt    =   custPandLDtLayout.cells("a").attachVault({
                    parent: document.body,    // html container for vault
                    uploadUrl: preTally.Initialize.encryptURL("warehouse/upload_handler.php&name="+fileNewName),
                    filesLimit: 1
                });
                // above section common for all uploads
                // samle file links
                custPandLDtLayout.cells("a").attachStatusBar({text:'<a href="plugins/excelTemplates/ImportPandLDt.xls">Download Excel Format</a>', height:30});
                //common file uploading checking and clearing
                excelcustPandLDt.attachEvent("onFileRemove",function() {
                   excelcustPandLDt.setFilesLimit(1);
                   custPandLDtLayout.cells("c").detachObject(); 
                });               
                excelcustPandLDt.attachEvent("onClear", function(file) {
                   custPandLDtLayout.cells("c").detachObject(); 
                });                
                excelcustPandLDt.attachEvent("onBeforeFileAdd", function(file) {
                    custPandLDtLayout.cells("b").detachObject(); 
                    custPandLDtLayout.cells("b").detachStatusBar();
                    var ext = this.getFileExtension(file.name);
                    if(ext != 'xls' && ext != 'xlsx') {
                        dhtmlx.message({text: "Please upload a file of type xls or xlsx"});
                    } else {
                        return true;
                    }
                });
                excelcustPandLDt.attachEvent("onUploadComplete", function() {
                    
                    preTally.Settings.progressOn(true, dhxLayout, null);
                    var uploadedFileData    = excelcustPandLDt.getData();
                    var ext                 =  uploadedFileData[0].name.split('.').pop();
                    if(ext == "") {
                        ext         = "xls";
                    }                    
                    var fileData    = '{"newfilename":"' + fileNewName+'.'+ext+'","cols":"4"}';
                    $.ajax({
                        url: preTally.Initialize.encryptURL("warehouse/uploadImportFile.php&data=" + fileData)
                    }).done(function(data) { 
                        if(data == "") {
                            dhtmlx.message({text: "Please upload an excel of provided format."});
                            preTally.Settings.progressOff(true, dhxLayout, null);
                            excelcustPandLDt.clear();
                        }  else { // after upload success then start porcessing
                            var str = new Object();
                            str     = jQuery.parseJSON(data); 
                            if(str.numRows > 1) {  // if uploaded excel file has entries

                                var excelDataForm = custPandLDtLayout.cells("c").attachForm();
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelDataForm.loadStruct(preTally.Initialize.encryptURL("requisites/accounts/pandlExcelForm.php"), function() {

                                    if(str.cells[0][0] == "Date" && str.cells[0][1] == "Process" && str.cells[0][2] == "Amount" && str.cells[0][3] == "Branch") {
                                        excelDataForm.setItemValue("accPL_ExcelArray",data);
                                        //excel values in proper columns (load into combo for manuval check)
                                        cboAcDate = excelDataForm.getCombo("Date");                                           
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }  else {
                                                var optionText = "";
                                            }
                                            cboAcDate.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 0) {
                                                cboAcDate.setComboText(str.cells[1][0]);
                                                cboAcDate.setComboValue(i);
                                            }
                                        }
                                        cboAcProcess = excelDataForm.getCombo("Process");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            cboAcProcess.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 1) {
                                                cboAcProcess.setComboText(str.cells[1][1]);
                                                cboAcProcess.setComboValue(i);
                                            }
                                        }                                        
                                        cboAcAmt = excelDataForm.getCombo("Amount");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            cboAcAmt.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 2) {
                                                cboAcAmt.setComboText(str.cells[1][2]);
                                                cboAcAmt.setComboValue(i);
                                            }
                                        }

                                        cboAcBranch = excelDataForm.getCombo("Branch");   
                                        for(i = 0; i < str.numCols; i++) {
                                            if(str.cells[1][i] != "" && str.cells[1][i] != undefined) {
                                                var optionText = "("+str.cells[1][i]+")";
                                            }   else {
                                                var optionText = "";
                                            }
                                            cboAcBranch.addOption(i,str.cells[0][i]+ " "+optionText);
                                            if(i === 3) {
                                                cboAcBranch.setComboText(str.cells[1][3]);
                                                cboAcBranch.setComboValue(i);
                                            }
                                        }                                       
                                        excelDataForm.attachEvent("onButtonClick",function(id) {

                                            if (id   ==  "accPL_Button" && excelDataForm.getItemValue("type") != "" && excelDataForm.getItemValue("Month") != "" && excelDataForm.getItemValue("Year") != "") {

                                                preTally.Settings.progressOn(true, dhxLayout, null);
                                                excelDataForm.send(preTally.Initialize.encryptURL("warehouse/accounts/pandLUpdExcel.php"),function(loader, response){ 

                                                    preTally.Settings.progressOff(true, dhxLayout, null);
                                                    var totalRecords    = str.numRows - 1;  // except excel header row
                                                    errorJSArray        = jQuery.parseJSON(response);
                                                    var errorGridJsonData = "{rows:[";

                                                    var errorListLength = 0;
                                                    for(var k in errorJSArray) {
                                                        if(errorJSArray.hasOwnProperty(k)) 
                                                            errorListLength++;
                                                    }

                                                    for( i = 0; i < errorListLength; i++) {   
                                                        if(i > 0) {
                                                            errorGridJsonData = errorGridJsonData+",";
                                                        }
                                                        errorGridJsonData = errorGridJsonData+ "{id:"+i+",data:["
                                                        +'"'+errorJSArray[i][0]+'","'+errorJSArray[i][1]+'","'+errorJSArray[i][2]+'","'+errorJSArray[i][3]+'","'+errorJSArray[i][4]+'"]}';
                                                    }
                                                    var successRecord = totalRecords - errorListLength;
                                                    errorGridJsonData = errorGridJsonData+ " ]}";

                                                    errorGridCusPandL = custPandLDtLayout.cells("b").attachGrid();
                                                    errorGridCusPandL.attachEvent("onXLS", function() {
                                                        preTally.Settings.progressOn(true, dhxLayout, null);
                                                    });
                                                    errorGridCusPandL.attachEvent("onXLE", function() {
                                                        preTally.Settings.progressOff(true, dhxLayout, null);
                                                    });
                                                    errorGridCusPandL.setHeader("Date, Process, Amount, Branch,Error Columns, Error Remarks");
                                                    errorGridCusPandL.setColTypes("ro,ro,ro,ro,ro,ro"); 
                                                    errorGridCusPandL.setInitWidths("100,200,100,200,200,200");
                                                    errorGridCusPandL.setColAlign("left,left,left,left,left,left");
                                                    errorGridCusPandL.init();
                                                    errorGridCusPandL.parse(errorGridJsonData,"json");
                                                    custPandLDtLayout.cells("b").attachStatusBar({text:"Successful Insertions : "+successRecord+" ; "+"  Failed Insertions : "+errorListLength+' <input type="button" value="Get Error List as Excel" onclick="preTally.Settings.ConverttoEXL(errorGridCusPandL)">', height:30});
                                                    excelcustPandLDt.clear();
                                                });
                                            } else {
                                                dhtmlx.message({type: "error", text: "Please select all required fields"});
                                            }
                                        });
                                    }   else {
                                        dhtmlx.message({type: "error", text: "Please upload an excel file of provided format"});
                                        excelcustPandLDt.clear();
                                    }  
                                });
                            }   else  {
                                dhtmlx.message({type: "error", text: "Uploaded excel file cannot be empty"});
                                preTally.Settings.progressOff(true, dhxLayout, null);
                                excelcustPandLDt.clear();
                            }
                        }
                    });
                }); 

            } else {
                dhxMiddleBlockTabs.tabs("acc_PandLData").setActive();
            }
        },
        // custom p and l data uploaded..
    };
})(jQuery, this);