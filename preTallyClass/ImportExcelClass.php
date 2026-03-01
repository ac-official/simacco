<?php
include_once("connection.php");

class ImportExcelClass
{
    function readExcel($excelData) {
        //  Include PHPExcel_IOFactory
        include ($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php'); 
        $inputFileName = $BASEPATH.'uploads/excelFile/'.$excelData['newfilename'];
        //  Read your Excel workbook
        try {
            $inputFileType      = PHPExcel_IOFactory::identify($inputFileName);
            $objReader          = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel        = $objReader->load($inputFileName);
        } 
        catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        //  Get worksheet dimensions
        $sheet                      = $objPHPExcel->getSheet(0); 
        $highestRow                 = $sheet->getHighestDataRow();
        $highestColumn              = PHPExcel_Cell::stringFromColumnIndex($excelData['cols']-1); 
        $cellData                   = array();
        $rowData                    = array();
        $rowData['excelFileName']   = $inputFileName;
        $rowData['numRows']         = $highestRow; 
        $rowData['numCols']         = $excelData['cols']; 
        
        for ($row = 1; $row <= $highestRow; $row++){  
            $cell = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);
            $cellData[]     = $cell[0];
        }
        $rowData['cells']   = $cellData;
        return json_encode($rowData); 
    }
    
    /* Function for adding branch details uploaded through excel*/
    function newBranchExcel($excelObjectArray,$preTally_user_id,$preTally_user_ofid) { 
        // get swaped positions of each entries
        $titlePosition       = $this->cleanData($_REQUEST['IBB_Title']);
        $officePosition      = $this->cleanData($_REQUEST['IBB_Office']);
        $phonePosition       = $this->cleanData($_REQUEST['IBB_Phone']);
        $buildingPosition    = $this->cleanData($_REQUEST['IBB_Building']);
        $streetPosition      = $this->cleanData($_REQUEST['IBB_Street']);
        $placePosition       = $this->cleanData($_REQUEST['IBB_Place']);
        $locationPosition    = $this->cleanData($_REQUEST['IBB_Location']);
        $cityPosition        = $this->cleanData($_REQUEST['IBB_City']);
        $statePosition       = $this->cleanData($_REQUEST['IBB_State']);
        $countryPosition     = $this->cleanData($_REQUEST['IBB_Country']);
        $pincodePosition     = $this->cleanData($_REQUEST['IBB_Pincode']);
        $remarksPosition     = $this->cleanData($_REQUEST['IBB_Remarks']);
        
        $finalInsertArray   = array();
        $this->errorArray   = array();   // error array
        $branchArray        = array();
        $insertArray        = array();
        $excelArray         = json_decode(json_encode($excelObjectArray['cells']), true);  // converting object array to normal PHP array
        $curDate            = date('Y-m-d');
       
        for($i = 1; $i < $excelObjectArray['numRows']; $i++) {  // for each records in array
            $totalCells     = $excelObjectArray['numCols'];  // get total cells in array 
            $errorColumns   = array();    // error columns
            $remarks        = array();          // remarks for errors
            unset($insertArray);
            $flag           = 0;
            if($excelArray[$i][$officePosition] == "" || $excelArray[$i][$officePosition] == "null") {
                array_push($errorColumns,"Company");
                array_push($errorColumns,"Company can't be left empty");
                $flag = 1;
            }  else {
                $officeQuery = mysqli_query($GLOBALS['con'],"SELECT OF_Id FROM offices WHERE OF_Name='".$this->cleanData($excelArray[$i][$officePosition])."'");

                if(mysqli_num_rows($officeQuery) > 0) {       // if office exists
                    $officeResultset = mysqli_fetch_array($officeQuery,MYSQLI_ASSOC);                    
                    $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC_Id FROM locations WHERE LC_Name='".$this->cleanData($excelArray[$i][$titlePosition])."' AND LC_Status != 5 AND OF_Id=".$officeResultset['OF_Id']);                    
                    if(mysqli_num_rows($branchQuery) > 0 ) {   // if branch exists for the company
                        array_push($errorColumns,"Branch Name");
                        array_push($remarks,"Branch Name already exists");
                        $flag = 1;
                    }   else {
                        for($j = 1; $j < $i; $j++) {  // check whether branch exists for a company in the array of records to be inserted
                            if(($excelArray[$i][$titlePosition] == $excelArray[$j][$titlePosition]) && 
                                $excelArray[$i][$officePosition] == $excelArray[$j][$officePosition] ) {
                                array_push($errorColumns,"Office Name");
                                array_push($remarks,"Office is already added from uploaded excel");
                                $flag  = 1;
                            }
                        }
                    }
                    
                    if($preTally_user_ofid == 1 || $this->offzAdmin($officeResultset['OF_Id']) == $preTally_user_id) { // if admin
                        $insertArray['OF_Id'] = $this->cleanData($officeResultset['OF_Id']);
                    }   else {  
                            if($preTally_user_ofid == $officeResultset['OF_Id']) { // if logged in user is of the company given in the excel row
                                $insertArray['OF_Id'] = $this->cleanData($officeResultset['OF_Id']);
                            }  else { 
                                array_push($errorColumns,"Office Name");
                                array_push($remarks,"Logged in user is not permitted to insert this record");
                                $flag = 1;
                            }
                    }
                }  else {  // if office doesnot exist
                    array_push($errorColumns,"Company");
                    array_push($remarks,"Company doesnot exist");
                    $flag = 1;
                }
            }
            if($flag == 0 ) {
                if($excelArray[$i][$titlePosition] != "" && $excelArray[$i][$titlePosition] != "null"  
                    && $excelArray[$i][$countryPosition] != "" && $excelArray[$i][$countryPosition] != 'null'  
                    && $insertArray['OF_Id'] != ""   
                    && $excelArray[$i][$locationPosition] != "" && $excelArray[$i][$locationPosition] != 'null' 
                    && $excelArray[$i][$cityPosition] != "" && $excelArray[$i][$cityPosition] != 'null'  
                    && $excelArray[$i][$statePosition] != "" && $excelArray[$i][$statePosition] != 'null'
                    && $excelArray[$i][$placePosition] != "" && $excelArray[$i][$placePosition] != 'null'
                    && $excelArray[$i][$locationPosition] != "" && $excelArray[$i][$locationPosition] != 'null'    ) {

                        $countryQuery = mysqli_query($GLOBALS['con'],"SELECT CN_Id FROM countries WHERE CN_Name='".$this->cleanData($excelArray[$i][$countryPosition])."'");
                        if(mysqli_num_rows($countryQuery) > 0) {  // if country exists
                            $countryResultset = mysqli_fetch_array($countryQuery,MYSQLI_ASSOC);
                            $insertArray['CN_Id'] = $this->cleanData($countryResultset['CN_Id']);

                        $stateQuery = mysqli_query($GLOBALS['con'],"SELECT ST_Id FROM states WHERE CN_Id=".$insertArray['CN_Id']." AND ST_Name='".$this->cleanData($excelArray[$i][$statePosition])."'");
                            if(mysqli_num_rows($stateQuery) > 0) {
                                $stateResultset = mysqli_fetch_array($stateQuery,MYSQLI_ASSOC);
                                $insertArray['ST_Id'] = $this->cleanData($stateResultset['ST_Id']);
                            } else {
                                $newStateQuery = mysqli_query($GLOBALS['con'],"INSERT INTO states (ST_Name,CN_Id,US_Id,ST_CDate,ST_MDate) VALUES ('".$excelArray[$i][$statePosition]."',".$insertArray['CN_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                                $insertArray['ST_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }

                        $cityQuery = mysqli_query($GLOBALS['con'],"SELECT CT_Id FROM cities WHERE ST_Id=".$insertArray['ST_Id']." AND CT_Name='".$this->cleanData($excelArray[$i][$cityPosition])."'");
                            if(mysqli_num_rows($cityQuery) > 0) {
                                $cityResultset = mysqli_fetch_array($cityQuery,MYSQLI_ASSOC);
                                $insertArray['CT_Id'] = $this->cleanData($cityResultset['CT_Id']);
                            }   else {
                                $newCityQuery = mysqli_query($GLOBALS['con'],"INSERT INTO cities (CT_Name,ST_Id,US_Id,CT_CDate,CT_MDate) VALUES ('".$excelArray[$i][$cityPosition]."',".$insertArray['ST_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                                $insertArray['CT_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }

                        $locationQuery = mysqli_query($GLOBALS['con'],"SELECT ALC_Id FROM addr_locations WHERE CT_Id=".$insertArray['CT_Id']." AND ALC_Name='".$this->cleanData($excelArray[$i][$locationPosition])."'");
                            if(mysqli_num_rows($locationQuery) > 0) { 
                                $locationResultset = mysqli_fetch_array($locationQuery,MYSQLI_ASSOC);
                                $insertArray['ALC_Id'] = $this->cleanData($locationResultset['ALC_Id']);
                            }  else { 
                                $newlocationQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_locations (ALC_Name,US_Id,CT_Id,ALC_CDate,ALC_MDate,ALC_Status) VALUES ('".$excelArray[$i][$locationPosition]."',0,".$insertArray['CT_Id'].",'".$curDate."','".$curDate."',1)");
                                $insertArray['ALC_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }
                       
                        $placeQuery = mysqli_query($GLOBALS['con'],"SELECT PL_Id FROM addr_places WHERE ALC_Id=".$insertArray['ALC_Id']." AND PL_Name='".$this->cleanData($excelArray[$i][$placePosition])."'");
                        if(mysqli_num_rows($placeQuery) > 0) { 
                            $placeResultset = mysqli_fetch_array($placeQuery,MYSQLI_ASSOC);
                            $insertArray['PL_Id'] = $this->cleanData($placeResultset['PL_Id']);
                        }  else { 
                            $newplaceQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_places (PL_Name,US_Id,ALC_Id,PL_CDate,PL_MDate,PL_Status) VALUES ('".$excelArray[$i][$placePosition]."',0,".$insertArray['ALC_Id'].",'".$curDate."','".$curDate."',1)");
                            $insertArray['PL_Id'] = mysqli_insert_id($GLOBALS['con']);
                        }
                       
                        $streetQuery = mysqli_query($GLOBALS['con'],"SELECT SR_Id FROM addr_streets WHERE PL_Id=".$insertArray['PL_Id']." AND SR_Name='".$this->cleanData($excelArray[$i][$streetPosition])."'");
                        if(mysqli_num_rows($streetQuery) > 0) { 
                            $streetResultset = mysqli_fetch_array($streetQuery,MYSQLI_ASSOC);
                            $insertArray['SR_Id'] = $this->cleanData($streetResultset['SR_Id']);
                        }  else { 
                            $newstreetQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_streets (SR_Name,US_Id,PL_Id,SR_CDate,SR_MDate,SR_Status) VALUES ('".$excelArray[$i][$streetPosition]."',0,".$insertArray['PL_Id'].",'".$curDate."','".$curDate."',1)");
                            $insertArray['SR_Id'] = mysqli_insert_id($GLOBALS['con']);
                        }
                        
                    }   else {  // if country doesnot exists

                        $newCountryQuery = mysqli_query($GLOBALS['con'],"INSERT INTO countries (CN_Name) VALUES ('".$this->cleanData($excelArray[$i][$countryPosition])."')");
                        $insertArray['CN_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newStateQuery = mysqli_query($GLOBALS['con'],"INSERT INTO states (ST_Name,CN_Id,US_Id,ST_CDate,ST_MDate) VALUES ('".$this->cleanData($excelArray[$i][$statePosition])."',".$insertArray['CN_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                        $insertArray['ST_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newCityQuery = mysqli_query($GLOBALS['con'],"INSERT INTO cities (CT_Name,ST_Id,US_Id,CT_CDate,CT_MDate) VALUES ('".$this->cleanData($excelArray[$i][$cityPosition])."',".$insertArray['ST_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                        $insertArray['CT_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newlocationQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_locations (ALC_Name,US_Id,CT_Id,ALC_CDate,ALC_MDate,ALC_Status) VALUES ('".$excelArray[$i][$locationPosition]."',0,".$insertArray['CT_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['ALC_Id'] = mysqli_insert_id($GLOBALS['con']);
                        
                        $newplaceQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_places (PL_Name,US_Id,PL_Id,PL_CDate,PL_MDate,PL_Status) VALUES ('".$excelArray[$i][$placePosition]."',0,".$insertArray['ALC_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['PL_Id'] = mysqli_insert_id($GLOBALS['con']);
                        
                        $newstreetQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_streets (SR_Name,US_Id,PL_Id,SR_CDate,SR_MDate,SR_Status) VALUES ('".$excelArray[$i][$streetPosition]."',0,".$insertArray['PL_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['SR_Id'] = mysqli_insert_id($GLOBALS['con']);
                        
                    }

                    $insertArray['US_Id']       = $preTally_user_id;
                    $insertArray['LC_Name']     = $this->cleanData($excelArray[$i][$titlePosition]);
                    $insertArray['LC_Phone']    = $this->cleanData($excelArray[$i][$phonePosition]);
                    $insertArray['LC_Pincode']  = $this->cleanData($excelArray[$i][$pincodePosition]);
                    $insertArray['LC_Street']   = $this->cleanData($excelArray[$i][$streetPosition]);
                    $insertArray['LC_Building'] = $this->cleanData($excelArray[$i][$buildingPosition]);
                    $insertArray['LC_Comments'] = $this->cleanData($excelArray[$i][$remarksPosition]);
                    $insertArray['LC_CDate']    = $insertArray['LC_MDate'] = $curDate;
                }
                else {   // if record have missing data
                    $flag = 1;
                    array_push($errorColumns,"Office Name/Company/Phone Number/Building Name/Street/Place/Location/City/State/Country");
                    array_push($remarks,"Can't be left empty/Logged in user not permitted to insert this record");
                }
            }
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);            
            if($flag == 1) 
                $this->errorArray[] = $excelArray[$i]; 
            else 
                $finalInsertArray[] = $insertArray;  // append successful records to the final array to be inserted
        
        }
        $insertSQL = "INSERT INTO locations (CN_Id,ST_Id,PL_Id,ALC_Id,SR_Id,US_Id,LC_Name,OF_Id,LC_Phone,LC_Pincode,LC_Street,LC_Building,LC_Comments,LC_CDate,LC_MDate,CT_Id) VALUES ";
        
        foreach ($finalInsertArray as $branchArray) {
            $valuesSQL = "(" . "'".$branchArray['CN_Id']. "','".$branchArray['ST_Id'] ."','".$branchArray['PL_Id']."','".$branchArray['ALC_Id']."','".$branchArray['SR_Id']."','".$branchArray['US_Id']."','".$branchArray['LC_Name']."','".$branchArray['OF_Id']."','".$branchArray['LC_Phone']."','".$branchArray['LC_Pincode']."','".$branchArray['LC_Street']."','".$branchArray['LC_Building']."','".$branchArray['LC_Comments']."','".$branchArray['LC_CDate']."','".$branchArray['LC_MDate']."','".$branchArray['CT_Id']."')";
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
            $LC_Id = mysqli_insert_id($GLOBALS['con']);
            mysqli_query($GLOBALS['con'],"INSERT INTO cash_open_bals (OF_Id,LC_Id,OB_Date) VALUES (".$branchArray['OF_Id'].",".$LC_Id.",'".$branchArray['LC_CDate']."') ");
            mysqli_query($GLOBALS['con'],"INSERT INTO stock_open_bals (OF_Id,LC_Id,OS_Date) VALUES (".$branchArray['OF_Id'].",".$LC_Id.",'".$branchArray['LC_CDate']."') ");
        }
        $path = $BASEPATH . "uploads/excelFile/".$excelObjectArray['excelFileName'];
        unlink($path);
    }
    
    /* Function for creating new users through excel importing */
    function newUserExcel($userExcelData,$preTally_user_id,$preTally_user_ofid)   {
        // get swaped positions of each entries
        
        $companyPosition       = $this->cleanData($_REQUEST['IU_Company']);
        $branchPosition        = $this->cleanData($_REQUEST['IU_Branch']);
        $userIDPosition        = $this->cleanData($_REQUEST['IU_UserID']);
        $userPswdPosition      = $this->cleanData($_REQUEST['IU_UserPswd']);
        $aclPosition           = $this->cleanData($_REQUEST['IU_ACL']);
        $emailPosition         = $this->cleanData($_REQUEST['IU_Email']);
        $fNamePosition         = $this->cleanData($_REQUEST['IU_FName']);
        $lNamePosition         = $this->cleanData($_REQUEST['IU_LName']);
        $joinDatePosition      = $this->cleanData($_REQUEST['IU_JoinDate']);
        $departmentPosition    = $this->cleanData($_REQUEST['IU_Department']);
        $designationPosition   = $this->cleanData($_REQUEST['IU_Designation']);
        $empStatusPosition     = $this->cleanData($_REQUEST['IU_EmpStatus']);
        $reportsToPosition     = $this->cleanData($_REQUEST['IU_ReportsTo']);
        
        $finalInsertArray     = array();
        $this->userErrorArray = array();   // error array
        $insertArray          = array();
        $emailArray           = array();
        $userIDArray          = array();
        
        $excelArray           = json_decode(json_encode($userExcelData['cells']), true);  // converting object array to normal PHP array
       
        for($i = 1; $i < $userExcelData['numRows']; $i++) {  // for each records in array
            $totalCells       = $userExcelData['numCols'];  // get total cells in array 
            $errorColumns     = array();    // error columns
            $remarks          = array();          // remarks for errors
            unset($insertArray);
            $flag             = 0;
            if($excelArray[$i][$companyPosition] != "" && $excelArray[$i][$companyPosition] != 'null' 
                && $excelArray[$i][$branchPosition] != "" && $excelArray[$i][$branchPosition] != 'null' 
                && $excelArray[$i][$userIDPosition] != "" && $excelArray[$i][$userIDPosition] != 'null'  
                && $excelArray[$i][$userPswdPosition] != "" && $excelArray[$i][$userPswdPosition] != 'null'  
                && $excelArray[$i][$aclPosition] != "" && $excelArray[$i][$aclPosition] != 'null' 
                && $excelArray[$i][$emailPosition] != "" && $excelArray[$i][$emailPosition] != 'null'  
                && $excelArray[$i][$fNamePosition] != "" && $excelArray[$i][$fNamePosition] != 'null' 
                && $excelArray[$i][$lNamePosition] != "" && $excelArray[$i][$lNamePosition] != 'null'  
                && $excelArray[$i][$joinDatePosition] != "" && $excelArray[$i][$joinDatePosition] != 'null'  
                && $excelArray[$i][$departmentPosition] != "" && $excelArray[$i][$departmentPosition] != 'null'  
                && $excelArray[$i][$designationPosition] != "" && $excelArray[$i][$designationPosition] != 'null'  
                && $excelArray[$i][$empStatusPosition] != "" && $excelArray[$i][$empStatusPosition] != 'null'    
                && $excelArray[$i][$reportsToPosition] != "" && $excelArray[$i][$reportsToPosition] != 'null') {
                
                $companyQuery = mysqli_query($GLOBALS['con'],"SELECT OF_Id FROM offices WHERE OF_Name='".$this->cleanData($excelArray[$i][$companyPosition])."'");
                if(mysqli_num_rows($companyQuery) > 0) {
                    $companyResult = mysqli_fetch_array($companyQuery,MYSQLI_ASSOC);
                    
                    if($preTally_user_ofid == 1 || $this->offzAdmin($companyResult['OF_Id']) == $preTally_user_id) { // if admin
                        $insertArray['OF_Id'] = $this->cleanData($companyResult['OF_Id']);
                    }  else {  
                        if($preTally_user_ofid == $companyResult['OF_Id']) {
                            $insertArray['OF_Id'] = $this->cleanData($companyResult['OF_Id']);
                        }  else { 
                            array_push($errorColumns,"Branch");
                            array_push($remarks,"Logged in user is not permitted to insert this record");
                            $flag = 1;
                        }
                    }
                    $compny_sql=mysqli_query($GLOBALS['con'],"SELECT * FROM company_settings WHERE OF_Id=".$insertArray['OF_Id']);        
                    $settings_array=  mysqli_fetch_assoc($compny_sql);
                    
                    $branchQuery = mysqli_query($GLOBALS['con'],"SELECT LC_Id FROM locations WHERE OF_Id = ".$insertArray['OF_Id']." AND LC_Status != 5 AND LC_Name='".$this->cleanData($excelArray[$i][$branchPosition])."'");
                    if(mysqli_num_rows($branchQuery) > 0) {
                        $branchResult = mysqli_fetch_array($branchQuery,MYSQLI_ASSOC);
                        $insertArray['LC_Id'] = $this->cleanData($branchResult['LC_Id']);
                    } else {
                        array_push($errorColumns,"Branch");
                        array_push($remarks,"Branch doesn't exist");
                        $flag = 1;
                    }
                    
                    $aclQuery = mysqli_query($GLOBALS['con'],"SELECT ACL_Id FROM acl WHERE OF_Id=".$insertArray['OF_Id']." AND ACL_Name='".$this->cleanData($excelArray[$i][$aclPosition])."'");
                    if(mysqli_num_rows($aclQuery) > 0) {
                        $aclResult = mysqli_fetch_array($aclQuery,MYSQLI_ASSOC);
                        $insertArray['UT_Id'] = $this->cleanData($aclResult['ACL_Id']);
                    }  else {
                        array_push($errorColumns,"User ACL Type");
                        array_push($remarks,"ACL doesn't exist");
                        $flag = 1;
                    }
                    
                    $departmentQuery = mysqli_query($GLOBALS['con'],"SELECT DP_Id FROM departments WHERE DP_Name='".$this->cleanData($excelArray[$i][$departmentPosition])."' AND OF_Id=".$insertArray['OF_Id']);
                    if(mysqli_num_rows($departmentQuery) > 0) {
                       $departmentResult = mysqli_fetch_array($departmentQuery,MYSQLI_ASSOC);
                       $insertArray['DP_Id'] = $this->cleanData($departmentResult['DP_Id']);
                    }   else { 
                        array_push($errorColumns,"Department");
                        array_push($remarks,"Department doesn't exist");
                        $flag = 1;
                    }

                    $designationQuery = mysqli_query($GLOBALS['con'],"SELECT DG_Id FROM designations WHERE DG_Name='".$this->cleanData($excelArray[$i][$designationPosition])."' AND OF_Id=".$insertArray['OF_Id']);
                    if(mysqli_num_rows($designationQuery) > 0) {
                       $designationResult = mysqli_fetch_array($designationQuery,MYSQLI_ASSOC);
                       $insertArray['DG_Id'] = $this->cleanData($designationResult['DG_Id']);
                    }    else { 
                        array_push($errorColumns,"Designation");
                        array_push($remarks,"Designation doesn't exist");
                        $flag = 1;
                    }

                    if($insertArray['OF_Id']) {
                        $empStatus = $this->cleanData($excelArray[$i][$empStatusPosition]);
                        $empStatusQuery = mysqli_query($GLOBALS['con'],"SELECT ES_Id FROM employee_status WHERE ES_Name='".$empStatus."' AND OF_Id = ".$insertArray['OF_Id']."  AND ES_Status = 0");
                        if(mysqli_num_rows($empStatusQuery) > 0) {
                            $empStatusResult = mysqli_fetch_array($empStatusQuery,MYSQLI_ASSOC);
                            $insertArray['ES_Id'] = $this->cleanData($empStatusResult['ES_Id']);
                        }  else {
                            if(ctype_alpha($empStatus)) {
                                mysqli_query($GLOBALS['con'],"INSERT INTO employee_status (OF_Id,ES_Name,ES_Status) VALUES (".$insertArray['OF_Id'].",'".$empStatus."',0)");
                                $insertArray['ES_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }  else {
                                array_push($errorColumns,"Employee Status");
                                array_push($remarks,"Invalid Employee Status");
                                $flag = 1;
                            }
                        }
                    }
                    
                    $reportstoQuery = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_auth WHERE '".$this->cleanData($excelArray[$i][$reportsToPosition])."' = CONCAT(`US_FName`,' ',`US_LName`)"." AND US_Status != 5 AND OF_Id=".$insertArray['OF_Id']);
                    if(mysqli_num_rows($reportstoQuery) > 0) {
                        $reportstoResult = mysqli_fetch_array($reportstoQuery,MYSQLI_ASSOC);
                        $insertArray['US_Report'] = $this->cleanData($reportstoResult['US_Id']);
                    }   else { 
                        array_push($errorColumns,"Reports To");
                        array_push($remarks,"Reporting person doesn't exist");
                        $flag = 1;
                    }
                }  else { 
                    array_push($errorColumns,"Company");
                    array_push($remarks,"Company doesn't exist");
                    $flag = 1;
                }
                
                $userIDArray[]      =  $excelArray[$i][$userIDPosition];
                $userIDArrayCount   = array_count_values($userIDArray);
                $userQuery          = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_auth WHERE US_EMPID='".$this->cleanData($excelArray[$i][$userIDPosition])."' AND US_Status != 5");
                if(mysqli_num_rows($userQuery) > 0 || $userIDArrayCount[$excelArray[$i][$userIDPosition]] > 1) { 
                    array_push($errorColumns,"User ID");
                    array_push($remarks,"User ID already exists");
                    $flag = 1;
                } else {
                    $insertArray['US_EMPID'] = $this->cleanData($excelArray[$i][$userIDPosition]);
                }
                
                $emailArray[]       =  $excelArray[$i][$emailPosition];
                $emailArrayCount    = array_count_values($emailArray);
                $email              = $this->cleanData($excelArray[$i][$emailPosition]);
                $emailQuery         = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_auth WHERE US_Email='".$email."' AND US_Status != 5");
                if(mysqli_num_rows($emailQuery) > 0 || $emailArrayCount[$email] > 1 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errorColumns,"Company Email");
                    array_push($remarks,"Company Email already exists/invalid");
                    $flag = 1;
                }  else 
                    $insertArray['US_Email'] = $email;
              
                $joinDateInfo = date_parse($excelArray[$i][$joinDatePosition]);
                if($joinDateInfo['warning_count'] == 0 && $joinDateInfo['error_count'] == 0 ){
                    $joinDate = new DateTime($excelArray[$i][$joinDatePosition]);
                    $insertArray['US_DOJ'] = $joinDate->format('Y-m-d');
                }else {
                    array_push($errorColumns,"Date of Join");
                    array_push($remarks,"Invalid Date of Join");
                    $flag = 1;
                }
            } else {   // if record have missing data
                array_push($errorColumns,"-");
                array_push($remarks,"All fields are mandatory");
                $flag = 1;
            }
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            if($flag == 1) {
                $this->userErrorArray[] = $excelArray[$i];
            } else { 
                $insertArray['US_Password'] = md5($this->cleanData($excelArray[$i][$userPswdPosition]));
                $insertArray['US_FName']    = $this->cleanData($excelArray[$i][$fNamePosition]);
                $insertArray['US_LName']    = $this->cleanData($excelArray[$i][$lNamePosition]);
                $insertArray['US_Status']   = 1;
                $date                       = date('Y-m-d h:i:s', time());
                $insertArray['US_CDate']    = $date;
                $insertArray['US_MDate']    = $date;
                $insertArray['US_BlkdDate'] = "1970-01-01";
                $insertArray['US_LoginTime']=$settings_array['CS_OfficeStart'];
                $insertArray['US_LogoutTime']=$settings_array['CS_OfficeEnds'];
                $from_time                  = strtotime($settings_array['CS_OfficeStart']);
                $to_time                    = strtotime($settings_array['CS_OfficeEnds']);
                $insertArray['US_WrkHours'] =round(abs($to_time - $from_time) / 60,2);
                $insertArray['US_RptFlag'] =0;
                $finalInsertArray[]         = $insertArray; 
            }
        }
             
        $valuesSQL = "";
        $insertSQL = "INSERT INTO users_auth (OF_Id,LC_Id,US_EMPID,US_Password,UT_Id,US_FName,US_LName,US_DOJ,DP_Id,DG_Id,ES_Id,US_Report,US_Email,US_Status,US_CDate,US_MDate,US_BlkdDate,US_LoginTime,US_LogoutTime,US_WrkHours,US_RptFlag) VALUES ";
        $counter = 0;
        foreach ($finalInsertArray as $userArray) {
            $valuesSQL = "(" . $userArray['OF_Id']. ",".$userArray['LC_Id'] .",'".$userArray['US_EMPID']."','".$userArray['US_Password']."',".$userArray['UT_Id'].",'".$userArray['US_FName']."','".$userArray['US_LName']."','".$userArray['US_DOJ']."',".$userArray['DP_Id'].",".$userArray['DG_Id'].",".$userArray['ES_Id'].",".$userArray['US_Report'].",'".$userArray['US_Email']."',".$userArray['US_Status'].",'".$userArray['US_CDate']."','".$userArray['US_MDate']."','".$userArray['US_BlkdDate']."','".$userArray['US_LoginTime']."','".$userArray['US_LogoutTime']."',".$userArray['US_WrkHours'].",".$userArray['US_RptFlag'].")";                        
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
            $new_user_id =  mysqli_insert_id($GLOBALS['con']);
            $usr_persnl  = mysqli_query($GLOBALS['con'],"INSERT INTO users_personal (US_Id,US_Gender,US_DOB) VALUES (".$new_user_id.",'','1995-01-01')");
            $usr_qual    = mysqli_query($GLOBALS['con'],"INSERT INTO users_qualification (US_Id) VALUES (".$new_user_id.")");
            $usr_accs    = mysqli_query($GLOBALS['con'],"INSERT INTO users_accounts (US_Id,SP_Id,BA_Id) VALUES (".$new_user_id.",0,0)");
            $usr_sal     = mysqli_query($GLOBALS['con'],"INSERT INTO users_salary (US_Id,SS_Id) VALUES (".$new_user_id.",0)");
        }
        $path = $BASEPATH . "uploads/excelFile/".$userExcelData['excelFileName'];
        unlink($path);  //echo "fff";exit;
    }
    
    /* Function for adding more user details through excel uploading */
    function userDetailsExcel($excelData,$preTally_user_id,$preTally_user_ofid)   {
        // get swaped positions of each entries
        $usernamePosition            = $this->cleanData($_REQUEST['username']);
        $DOBPosition                 = $this->cleanData($_REQUEST['US_DOB']);
        $genderPosition              = $this->cleanData($_REQUEST['US_Gender']);
        $addressPosition             = $this->cleanData($_REQUEST['US_Address']);
        $countryPosition             = $this->cleanData($_REQUEST['Country']);
        $statePosition               = $this->cleanData($_REQUEST['State']);
        $cityPosition                = $this->cleanData($_REQUEST['City']);
        $pEmailPosition              = $this->cleanData($_REQUEST['US_Pemail']);
        $altEmailPosition            = $this->cleanData($_REQUEST['US_Altemail']);
        $mobilePosition              = $this->cleanData($_REQUEST['US_Mobile']);
        $altMobilePosition           = $this->cleanData($_REQUEST['US_AltMobile']);
        $landlinePosition            = $this->cleanData($_REQUEST['US_Landline']);
        $altLandlinePosition         = $this->cleanData($_REQUEST['US_AltLandline']);
        $relativePosition            = $this->cleanData($_REQUEST['US_Relative']);
        $bloodPosition               = $this->cleanData($_REQUEST['US_Blood']);
        $guardianPosition            = $this->cleanData($_REQUEST['US_Guardian']);
        $guardianPhonePosition       = $this->cleanData($_REQUEST['US_Guardianphone']);
        $EmgPersonPosition           = $this->cleanData($_REQUEST['US_Emergencyperson']);
        $EmgNumberPosition           = $this->cleanData($_REQUEST['US_Emergencynumber']);
        $EmgRelationPosition         = $this->cleanData($_REQUEST['US_Emergencyrelation']);
        $passportPosition            = $this->cleanData($_REQUEST['US_Passport']);
        $qualificationPosition       = $this->cleanData($_REQUEST['US_Qualification']);
        $specializationPosition      = $this->cleanData($_REQUEST['US_Specialization']);
        $experiencePosition          = $this->cleanData($_REQUEST['US_Experience']);
        $lastEmployeePosition        = $this->cleanData($_REQUEST['US_LastEmployee']);
        $PANPosition                 = $this->cleanData($_REQUEST['PAN']);
        $payModePosition             = $this->cleanData($_REQUEST['Pay_Mode']);
        $cmpACNoPosition             = $this->cleanData($_REQUEST['BA_Id']);
        $accNoPosition               = $this->cleanData($_REQUEST['US_AccNo']);
        $banknamePosition            = $this->cleanData($_REQUEST['US_Bankname']);
        $bankBranchPosition          = $this->cleanData($_REQUEST['US_BankBranch']);
        $PFNoPosition                = $this->cleanData($_REQUEST['US_PFNo']);
        $ESIPosition                 = $this->cleanData($_REQUEST['US_ESI']);
        $salStructPosition           = $this->cleanData($_REQUEST['Salary_Struct']);
        $GrossSalPosition            = $this->cleanData($_REQUEST['US_GrossSal']);
        
        $finalInsertArray           = array();
        $this->userDetailsErrorArray= array();   // error array
        $insertArray                = array();
        $pEmailArray                = array();
        $altEmailArray              = array();
        $curDate                    = date('Y-m-d');
        
        $excelArray = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php');
        
        for($i = 1; $i < $excelData['numRows']; $i++) {  // for each records in array
            $totalCells     = $excelData['numCols'];  // get total cells in array 
            $errorColumns   = array();    // error columns
            $remarks        = array();          // remarks for errors
            unset($insertArray);
            $flag = 0;
            
            $userQuery = mysqli_query($GLOBALS['con'],"SELECT US_Id,OF_Id FROM users_auth WHERE US_EMPID='".$this->cleanData($excelArray[$i][$usernamePosition])."' AND US_Status=1");
            if(mysqli_num_rows($userQuery) > 0 ) { // if user is registered
                
                $userResult = mysqli_fetch_array($userQuery,MYSQLI_ASSOC);
                if($preTally_user_ofid == 1 )  // if admin
                    $flag = 0;
                else if($preTally_user_ofid == $userResult['OF_Id'] ||  $this->offzAdmin($userResult['OF_Id']) == $preTally_user_id) 
                    $flag = 0; 
                else {
                    array_push($errorColumns,"-");
                    array_push($remarks,"Logged in user is not permitted to insert this record");
                    $flag = 1;
                }

                $insertArray['US_Id'] = $userResult['US_Id']; 
                
                if($excelArray[$i][$DOBPosition] != 0 && $excelArray[$i][$DOBPosition] != "" && $excelArray[$i][$DOBPosition] != "null") {
                    $DOBDateInfo = date_parse($excelArray[$i][$DOBPosition]);
                    if($DOBDateInfo['warning_count'] == 0 && $DOBDateInfo['error_count'] == 0 ){
                        $DOBDate = new DateTime($excelArray[$i][$DOBPosition]);
                        $insertArray['US_DOB']  = $DOBDate->format('Y-m-d');
                    }else{
                        $insertArray['US_DOB']  = "";
                        array_push($errorColumns,"Date Of Birth");
                        array_push($remarks,"Invalid Date Of Birth");
                        $flag = 1;
                    }
                    $excelArray[$i][$DOBPosition] = $insertArray['US_DOB'];
                }
                $insertArray['CN_Id']       = 0;
                $insertArray['ST_Id']       = 0;
                $insertArray['CT_Id']       = 0;
                
                if($excelArray[$i][$cityPosition] != "" && $excelArray[$i][$cityPosition] != "null") {
                    if($excelArray[$i][$statePosition] == "" || $excelArray[$i][$statePosition] == "null" 
                      || $excelArray[$i][$countryPosition] == "" || $excelArray[$i][$countryPosition] == "null") {
                        array_push($errorColumns,"State/Country");
                        array_push($remarks,"State/Country can't be empty");
                        $flag = 1; 
                    }  else {
                        $countryQuery = mysqli_query($GLOBALS['con'],"SELECT CN_Id FROM countries WHERE CN_Name='".$this->cleanData($excelArray[$i][$countryPosition])."'");
                        if(mysqli_num_rows($countryQuery)) { // if country exists
                            $countryResult              = mysqli_fetch_array($countryQuery,MYSQLI_ASSOC);
                            $insertArray['CN_Id']       = $countryResult['CN_Id'];
                            $stateQuery = mysqli_query($GLOBALS['con'],"SELECT ST_Id FROM states WHERE ST_Name='".$this->cleanData($excelArray[$i][$statePosition])."' AND CN_Id=".$insertArray['CN_Id']);
                            if(mysqli_num_rows($stateQuery)) {  // if state exists
                                $stateResult            = mysqli_fetch_array($stateQuery,MYSQLI_ASSOC);
                                $insertArray['ST_Id']   = $stateResult['ST_Id'];

                                $cityQuery = mysqli_query($GLOBALS['con'],"SELECT CT_Id FROM cities WHERE CT_Name='".$this->cleanData($excelArray[$i][$cityPosition])."'");
                                if(mysqli_num_rows($cityQuery) > 0) { // if city exists
                                    $cityResult           = mysqli_fetch_array($cityQuery,MYSQLI_ASSOC);
                                    $insertArray['CT_Id'] = $cityResult['CT_Id'];
                                }  else {  // if city doesnot exists
                                    mysqli_query($GLOBALS['con'],"INSERT INTO cities (CT_Name,ST_Id,US_Id,CT_CDate,CT_MDate) VALUES ('".$excelArray[$i][$cityPosition]."',".$insertArray['ST_Id'].",$preTally_user_id,'$curDate','$curDate')");
                                    $insertArray['CT_Id'] = mysqli_insert_id($GLOBALS['con']);
                                }
                            }      else {   // if state doesnot exists 
                                array_push($errorColumns,"State");
                                array_push($remarks,"State doesn't exist");
                                $flag = 1; 
                            }  
                        }    else {   // if country doesnot exists
                            array_push($errorColumns,"Country");
                            array_push($remarks,"Country doesn't exist");
                            $flag = 1; 
                        }
                    }
                }  else {  // city is not added in excel
                   
                    if($excelArray[$i][$statePosition] != "" && $excelArray[$i][$statePosition] != "null") {  // if state is added in excel
                        if($excelArray[$i][$countryPosition] == "" || $excelArray[$i][$countryPosition] == "null") {  // if country is missing
                            array_push($errorColumns,"Country");
                            array_push($remarks,"Country can't be left empty");
                            $flag  = 1; 
                        }  else {  // country is added in excel
                            $countryQuery = mysqli_query($GLOBALS['con'],"SELECT CN_Id FROM countries WHERE CN_Name='".$this->cleanData($excelArray[$i][$countryPosition])."'");
                            if(mysqli_num_rows($countryQuery)) { // if country exists
                                $countryResult              = mysqli_fetch_array($countryQuery,MYSQLI_ASSOC);
                                $insertArray['CN_Id']       = $countryResult['CN_Id'];
                                $stateQuery = mysqli_query($GLOBALS['con'],"SELECT ST_Id FROM states WHERE ST_Name='".$this->cleanData($excelArray[$i][$statePosition])."' AND CN_Id=".$insertArray['CN_Id']);
                                if(mysqli_num_rows($stateQuery)) {  // if state exists
                                    $stateResult            = mysqli_fetch_array($stateQuery,MYSQLI_ASSOC);
                                    $insertArray['ST_Id']   = $stateResult['ST_Id'];
                                }  else {
                                    array_push($errorColumns,"State");
                                    array_push($remarks,"State doesn't exist");
                                    $flag = 1;
                                }
                            }  else {
                                array_push($errorColumns,"Country");
                                array_push($remarks,"Country doesn't exist");
                                $flag  = 1;
                            }
                        }
                    } else {
                        if($excelArray[$i][$countryPosition] != "" && $excelArray[$i][$countryPosition] != "null") {
                            $countryQuery = mysqli_query($GLOBALS['con'],"SELECT CN_Id FROM countries WHERE CN_Name='".$this->cleanData($excelArray[$i][$countryPosition])."'");
                            if(mysqli_num_rows($countryQuery)) { // if country exists
                                $countryResult              = mysqli_fetch_array($countryQuery,MYSQLI_ASSOC);
                                $insertArray['CN_Id']       = $countryResult['CN_Id'];
                            } else {
                                array_push($errorColumns,"Country");
                                array_push($remarks,"Country doesn't exist");
                                $flag = 1;
                            }
                        }
                    }
                }
                
                $pEmail             = $this->cleanData($excelArray[$i][$pEmailPosition]);
                $pEmailArray[]      = $pEmail;
                $pemailArrayCount   = array_count_values($pEmailArray);
                
                $altEmail           = $this->cleanData($excelArray[$i][$altEmailPosition]);
                $altEmailArray[]    = $altEmail;
                $altEmailArrayCount = array_count_values($altEmailArray);
                
                $emailQuery         = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_auth WHERE US_Email='".$pEmail."' AND US_Status != 5 AND US_Id != ".$insertArray['US_Id']);
                $regUserEmailExist  = mysqli_num_rows($emailQuery);
               
                $pEmailQuery = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_personal WHERE (US_Pemail1='".$pEmail."' OR US_Pemail2='".$pEmail."') AND US_Id != ".$insertArray['US_Id']);
                $pEmailExist = mysqli_num_rows($pEmailQuery);
                
                if($pEmail != "" && $pEmail != "null") {
                    if($regUserEmailExist > 0 || $pEmailExist > 0 || $altEmailArrayCount[$pEmail] > 0 || $pemailArrayCount[$pEmail] > 1 || !filter_var($pEmail, FILTER_VALIDATE_EMAIL)) {
                        array_push($errorColumns,"Personal Email ID");
                        array_push($remarks,"Personal Email already exists/invalid");
                        $flag = 1; 
                    }   else {
                        $insertArray['US_Pemail1'] = $pEmail;
                    }
                }
               
                $altEmailRegQuery       = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_auth WHERE US_Email='".$altEmail."' AND US_Status != 5 AND US_Id != ".$insertArray['US_Id']);
                $regUserAltEmailExist   = mysqli_num_rows($altEmailRegQuery);
               
                $altEmailQuery          = mysqli_query($GLOBALS['con'],"SELECT US_Id FROM users_personal WHERE (US_Pemail1='".$altEmail."' OR US_Pemail2='".$altEmail."') AND US_Id != ".$insertArray['US_Id']);
                $altEmailExist          = mysqli_num_rows($altEmailQuery);
                
                if($altEmail != "" && $altEmail != "null") {
                    if($regUserAltEmailExist > 0 || $altEmailExist > 0 || $pemailArrayCount[$altEmail] > 0 || $altEmailArrayCount[$altEmail] > 1 || !filter_var($altEmail, FILTER_VALIDATE_EMAIL)) {
                        array_push($errorColumns,"Alternate Email ID");
                        array_push($remarks,"Alternate Email already exists/invalid");
                        $flag = 1;
                    }  else {
                        $insertArray['US_Pemail2'] = $altEmail;
                    }
                }
            
                if($excelArray[$i][$genderPosition] != "" && $excelArray[$i][$genderPosition] != "null") {
                    if(strcasecmp($excelArray[$i][$genderPosition], "male") == 0) {
                        $insertArray['US_Gender'] = 1;
                    }
                    else if(strcasecmp($excelArray[$i][$genderPosition], "female") == 0) {
                        $insertArray['US_Gender'] = 2;
                    }
                    else if(strcasecmp($excelArray[$i][$genderPosition], "others") == 0) {
                        $insertArray['US_Gender'] = 3;
                    }   else {
                        array_push($errorColumns,"Gender");
                        array_push($remarks,"Invalid Gender");
                        $flag = 1;  
                    }
                }
                
                if($this->cleanData($excelArray[$i][$addressPosition]) != "null" && $this->cleanData($excelArray[$i][$addressPosition]) != "")
                    $insertArray['US_Address']  = $this->cleanData($excelArray[$i][$addressPosition]);
                
                $mblPhone = $this->cleanData($excelArray[$i][$mobilePosition]);
                if($mblPhone != "" && $mblPhone != "null") {
                    if(is_numeric($mblPhone)) {
                        $insertArray['US_Mobile1']  = $mblPhone;
                    }  else {
                        array_push($errorColumns,"Mobile No:");
                        array_push($remarks,"Mobile No: should be numeric only");
                        $flag = 1; 
                    }
                }
                
                $altMblPhone = $this->cleanData($excelArray[$i][$altMobilePosition]);
                if($altMblPhone != "" && $altMblPhone != "null") {
                    if(is_numeric($altMblPhone)) {
                        $insertArray['US_Mobile2']  = $altMblPhone;
                    }  else {
                        array_push($errorColumns,"Alternate Mobile No:");
                        array_push($remarks,"Alternate Mobile No: should be numeric only");
                        $flag = 1;
                    }
                }
                
                $landPhone = $this->cleanData($excelArray[$i][$landlinePosition]);
                if($landPhone != "" && $landPhone != "null") {
                    if(is_numeric($landPhone)) {
                        $insertArray['US_Landline1']  = $landPhone;
                    }  else {
                        array_push($errorColumns,"Landline No:");
                        array_push($remarks,"Landline No: should be numeric only");
                        $flag = 1;
                    }
                }
                
                $landPhone2 = $this->cleanData($excelArray[$i][$altLandlinePosition]);
                if($landPhone2 != "" && $landPhone2 != "null") {
                    if(is_numeric($landPhone2)) {
                        $insertArray['US_Landline2']  = $landPhone2;
                    }  else {
                        array_push($errorColumns,"Alternate Landline No:");
                        array_push($remarks,"Alternate Landline No: should be numeric only");
                        $flag = 1;
                    }
                }
                
                $insertArray['US_Relative'] = $this->cleanData($excelArray[$i][$relativePosition]);
                
                $bloodGroup = $this->cleanData($excelArray[$i][$bloodPosition]);
                if($bloodGroup != "" && $bloodGroup != "null") {
                    $bloodGrpQuery = mysqli_query($GLOBALS['con'],"SELECT BG_Id FROM blood_groups WHERE BG_Name='".$bloodGroup."'");
                    if(mysqli_num_rows($bloodGrpQuery) > 0) {
                        $bloodGrpResult = mysqli_fetch_array($bloodGrpQuery,mysqli_assoc);
                        $insertArray['BG_Id'] = $bloodGrpResult['BG_Id'];
                    } else {
                        array_push($errorColumns,"Blood Group");
                        array_push($remarks,"Invalid Blood Group");
                        $flag = 1; 
                    }
                }
                
                if($this->cleanData($excelArray[$i][$guardianPosition]) != "null") 
                    $insertArray['US_Guardian']         = $this->cleanData($excelArray[$i][$guardianPosition]);
                
                if($this->cleanData($excelArray[$i][$guardianPhonePosition]) != "null") 
                    $insertArray['US_Guardianphone']    = $this->cleanData($excelArray[$i][$guardianPhonePosition]);
                
                if($this->cleanData($excelArray[$i][$EmgPersonPosition]) != "null")
                    $insertArray['US_Emergencyperson']  = $this->cleanData($excelArray[$i][$EmgPersonPosition]);
               
                $emgNo = $this->cleanData($excelArray[$i][$EmgNumberPosition]);
                if($emgNo != "" && $emgNo != "null") {
                    if(is_numeric($emgNo)) {
                        $insertArray['US_Emergencynumber']  = $emgNo;
                    } else {
                        array_push($errorColumns,"Emergency Contact No:");
                        array_push($remarks,"Emergency Contact No: should be numeric only");
                        $flag = 1;
                    }
                }
                
                $insertArray['US_Emergencyrelation']= $this->cleanData($excelArray[$i][$EmgRelationPosition]);
                
                $passportNo = $this->cleanData($excelArray[$i][$passportPosition]);
                if($passportNo != "" && $passportNo != "null") {
                    if($this->alphanumericValidation($passportNo)) {
                        $insertArray['US_Passport']     = $passportNo;
                    }  else {
                        array_push($errorColumns,"Passport No:");
                        array_push($remarks,"Passport No: should be alphanumeric only");
                        $flag = 1; 
                    }
                }
                    
                if($this->cleanData($excelArray[$i][$qualificationPosition]) != "null")
                    $insertArray['US_Qualification']    = $this->cleanData($excelArray[$i][$qualificationPosition]);
                
                if($this->cleanData($excelArray[$i][$specializationPosition]) != "null")
                    $insertArray['US_Specialization']   = $this->cleanData($excelArray[$i][$specializationPosition]);
                
                if($this->cleanData($excelArray[$i][$experiencePosition]) != "null")
                    $insertArray['US_Experience']       = $this->cleanData($excelArray[$i][$experiencePosition]);
                
                if($this->cleanData($excelArray[$i][$lastEmployeePosition]) != "null")
                    $insertArray['US_LastEmployee']     = $this->cleanData($excelArray[$i][$lastEmployeePosition]); 
                
                $panNo = $this->cleanData($excelArray[$i][$PANPosition]);
                if($panNo != "" && $panNo != "null") {
                    if($this->alphanumericValidation($panNo)) {
                        $insertArray['US_PAN']         = $panNo;
                    } else {
                        array_push($errorColumns,"PAN No:");
                        array_push($remarks,"PAN No: should be alphanumeric only");
                        $flag = 1; 
                    }
                }
                
                $paymentMode = $this->cleanData($excelArray[$i][$payModePosition]);
                if($paymentMode != "" && $paymentMode != "null") {
                    $paymentModeQuery = mysqli_query($GLOBALS['con'],"SELECT SP_Id FROM salary_paymodes WHERE SP_Name='".$paymentMode."' AND SP_Status=1");
                    if(mysqli_num_rows($paymentModeQuery) > 0) {
                        $paymentModeResult      = mysqli_fetch_array($paymentModeQuery,MYSQLI_ASSOC);
                        $insertArray['SP_Id']   = $paymentModeResult['SP_Id'];
                    }   else {
                        array_push($errorColumns,"Salary Structure");
                        array_push($remarks,"Salary Structure doesn't exist");
                        $flag = 1; 
                    }
                }
                
                $cmpACNo = $this->cleanData($excelArray[$i][$cmpACNoPosition]);
                if(strtolower($paymentMode) == 'bank') {
                    $cmpACQuery = mysqli_query($GLOBALS['con'],"SELECT BA_Id FROM bank_accounts WHERE BA_No = '".$cmpACNo."' AND OF_Id = ".$userResult['OF_Id']." AND BA_Status = 1");
                    if(mysqli_num_rows($cmpACQuery) > 0) {
                        $cmpACResult = mysqli_fetch_array($cmpACQuery,MYSQLI_ASSOC);
                        $insertArray['BA_Id']   = $cmpACResult['BA_Id'];
                    }  else {
                        array_push($errorColumns,"Company Bank Account No:");
                        array_push($remarks,"Company Bank Account No: doesn't exist");
                        $flag = 1; 
                    }
                } else {
                    $checkACNoQuery         = mysqli_query($GLOBALS['con'],"SELECT BA_Id FROM users_accounts WHERE US_Id = ".$insertArray['US_Id']);
                    $checkACNoResult        = mysqli_fetch_array($checkACNoQuery,MYSQLI_ASSOC);
                    $insertArray['BA_Id']   = $checkACNoResult['BA_Id'];
                }
                
                $acNo = $this->cleanData($excelArray[$i][$accNoPosition]);
                if($acNo != "" && $acNo != "null") {
                    if($this->alphanumericValidation($this->cleanData($excelArray[$i][$accNoPosition]))) {
                        $insertArray['US_AccNo']   = $this->cleanData($excelArray[$i][$accNoPosition]);
                    }  else {
                        array_push($errorColumns,"Employee Bank Account No:");
                        array_push($remarks,"Employee Bank Account No: should be alphanumeric only");
                        $flag = 1;
                    }
                }
                
                if($this->cleanData($excelArray[$i][$banknamePosition]) != "null")
                    $insertArray['US_Bankname']     = $this->cleanData($excelArray[$i][$banknamePosition]);
                
                if($this->cleanData($excelArray[$i][$bankBranchPosition]) != "null")
                    $insertArray['US_BankBranch']   = $this->cleanData($excelArray[$i][$bankBranchPosition]);
                
                $pfNo = $this->cleanData($excelArray[$i][$PFNoPosition]);
                if($pfNo != "" && $pfNo != "null") {
                    if($this->alphanumericValidation($pfNo)) {
                        $insertArray['US_PFNo']   = $pfNo;
                    } else {
                        array_push($errorColumns,"PF No:");
                        array_push($remarks,"PF No: should be alphanumeric only");
                        $flag = 1;
                    }
                }
               
                $esiNo = $this->cleanData($excelArray[$i][$ESIPosition]);
                if($esiNo != "" && $esiNo != "null") {
                    if($this->alphanumericValidation($esiNo)) {
                        $insertArray['US_ESI']   = $esiNo;
                    }  else {
                        array_push($errorColumns,"ESI No:");
                        array_push($remarks,"ESI No: should be alphanumeric only");
                        $flag = 1; 
                    }
                }
                
                if($this->cleanData($excelArray[$i][$salStructPosition]) != "" && 
                    $this->cleanData($excelArray[$i][$salStructPosition]) != "null" && 
                    $this->cleanData($excelArray[$i][$GrossSalPosition]) != "" && 
                    $this->cleanData($excelArray[$i][$GrossSalPosition]) !="null") {
                    $salaryQuery = mysqli_query($GLOBALS['con'],"SELECT * FROM salary_structures WHERE SS_Name='".$this->cleanData($excelArray[$i][$salStructPosition])."' AND OF_Id=".$userResult['OF_Id']." AND SS_Status=1");
                    if(mysqli_num_rows($salaryQuery) > 0) { // if salary structure exists
                        $salaryResult                       = mysqli_fetch_array($salaryQuery,MYSQLI_ASSOC);
                        $insertArray['SS_Id']               = $salaryResult['SS_Id'];

                        if(is_numeric($this->cleanData($excelArray[$i][$GrossSalPosition]))) {  // if salary is numeric
                            $insertArray['US_GrossSal']     = $this->cleanData($excelArray[$i][$GrossSalPosition]);
                            $insertArray['US_BasicSal']     = 0;
                            $insertArray['US_CcaSal']       = 0;
                            $insertArray['US_DASal']        = 0;
                            $insertArray['US_HRASal']       = 0;
                            $insertArray['US_ConveySal']    = 0;
                            $insertArray['US_EduSal']       = 0;
                            $insertArray['US_MedSal']       = 0;
                            $totalSalaryPartitions          = 0;
                            $insertArray['US_MiscSal']      = 0;
                            
                            if($salaryResult['SS_Basic_Type'] == 0) {
                                $insertArray['US_BasicSal']     = ($salaryResult['SS_Basic'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_BasicSal']     =  $salaryResult['SS_Basic']; 
                            }
//                            if($salaryResult['SS_DA_Type'] == 0) {
//                                $insertArray['US_DASal']     = ($salaryResult['SS_DA'] / 100) * $insertArray['US_GrossSal']; 
//                            }else{
//                                $insertArray['US_DASal']     = $salaryResult['SS_DA']; 
//                            }
                            if($salaryResult['SS_CCA_Type'] == 0) {
                                $insertArray['US_CcaSal']      = ($salaryResult['SS_CCA'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_CcaSal']      =  $salaryResult['SS_CCA']; 
                            }
                            if($salaryResult['SS_HRA_Type'] == 0) {
                                $insertArray['US_HRASal']      = ($salaryResult['SS_HRA'] / 100) * $insertArray['US_GrossSal']; 
                            }else{ 
                                $insertArray['US_HRASal']      =  $salaryResult['SS_HRA']; 
                            }
                            if($salaryResult['SS_Convey_Type'] == 0) {
                                $insertArray['US_ConveySal']   = ($salaryResult['SS_Convey'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_ConveySal']   =  $salaryResult['SS_Convey']; 
                            }
                            if($salaryResult['SS_Edu_Type'] == 0) {
                                $insertArray['US_EduSal']      = ($salaryResult['SS_Edu'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_EduSal']      =  $salaryResult['SS_Edu']; 
                            }
                            if($salaryResult['SS_Medic_Type'] == 0) {
                                $insertArray['US_MedSal']    = ($salaryResult['SS_Medic'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_MedSal']    =  $salaryResult['SS_Medic']; 
                            }
                            if($salaryResult['SS_DedLWF_Type'] == 0) {
                                $insertArray['US_DedLWF']   = ($salaryResult['SS_DedLWF'] / 100) * $insertArray['US_GrossSal']; 
                            }else{
                                $insertArray['US_DedLWF']   =  $salaryResult['SS_DedLWF']; 
                            }
                            
                            $sub_total = $insertArray['US_BasicSal']  + $insertArray['US_HRASal'] + $insertArray['US_CcaSal'] + $insertArray['US_ConveySal'] + $insertArray['US_EduSal'] + $insertArray['US_MedSal'];
                            
                            $insertArray['US_MiscSal']     =  $insertArray['US_GrossSal'] - $sub_total; 
                            if($insertArray['US_MiscSal'] < 0) {
                                array_push($errorColumns,"Gross Salary");
                                array_push($remarks,"Miscellaneous salary should be greater than 0");
                                $flag = 1;
                            }
                            if($insertArray['US_BasicSal']+$insertArray['US_CcaSal']+$insertArray['US_HRASal']+$insertArray['US_ConveySal']+$insertArray['US_EduSal']+$insertArray['US_MedSal']+$insertArray['US_MiscSal'] != $insertArray['US_GrossSal']) {
                                array_push($errorColumns,"Gross Salary");
                                array_push($remarks,"Gross salary should be equal to the sum of basic,CCA,HRA,Conveyance,Education Allowance,Medical Allowance and miscellaneous salary");
                                $flag = 1;
                            }
                            
                            $deduct_subtotal =  $insertArray['US_BasicSal'] + $insertArray['US_CcaSal'] + $insertArray['US_ConveySal'] + $insertArray['US_EduSal'] + $insertArray['US_MedSal'] + $insertArray['US_MiscSal'] ;
                            
                            if($salaryResult['SS_DedESI_Type'] == 0) {
                                $insertArray['US_DedESI']   = ($salaryResult['SS_DedESI'] / 100) * $deduct_subtotal; 
                            }else{
                                $insertArray['US_DedESI']   =  $salaryResult['SS_DedESI']; 
                            }
                            if($salaryResult['SS_DedEPF_Type'] == 0) {
                                $insertArray['US_DedEPF']   = ($salaryResult['SS_DedEPF'] / 100) * $deduct_subtotal; 
                            }else{
                                $insertArray['US_DedEPF']   =  $salaryResult['SS_DedEPF']; 
                            }
                            
                            /*if($salaryResult['SS_CFlag'] == 1) {  // custom salary structure
                                $insertArray['US_BasicSal']     = $salaryResult['SS_Basic'];
                                $insertArray['US_DASal']        = $salaryResult['SS_DA'];
                                $insertArray['US_HRASal']       = $salaryResult['SS_HRA'];
                                $insertArray['US_ConveySal']    = $salaryResult['SS_Convey'];
                                $insertArray['US_EduSal']       = $salaryResult['SS_Edu'];
                                $insertArray['US_MedSal']       = $salaryResult['SS_Medic'];
                                $totalSalaryPartitions          = $insertArray['US_BasicSal']+$insertArray['US_DASal']+$insertArray['US_HRASal']+$insertArray['US_ConveySal']+$insertArray['US_EduSal']+$insertArray['US_MedSal'];
                                $insertArray['US_MiscSal']      = $salaryResult['SS_Misc']+($insertArray['US_GrossSal']-$totalSalaryPartitions);
                            }
                            else if($salaryResult['SS_CFlag'] == 0) {  // salary percentage distribution
                                $insertArray['US_BasicSal']     = ($salaryResult['SS_Basic'] / 100) * $insertArray['US_GrossSal']; 
                                $insertArray['US_DASal']        = ($salaryResult['SS_DA'] / 100) * $insertArray['US_GrossSal'];
                                $insertArray['US_HRASal']       = ($salaryResult['SS_HRA'] / 100) * $insertArray['US_GrossSal'];
                                $insertArray['US_ConveySal']    = ($salaryResult['SS_Convey'] / 100) * $insertArray['US_GrossSal'];
                                $insertArray['US_EduSal']       = ($salaryResult['SS_Edu'] / 100) * $insertArray['US_GrossSal'];
                                $insertArray['US_MedSal']       = ($salaryResult['SS_Medic'] / 100) * $insertArray['US_GrossSal'];
                                $insertArray['US_MiscSal']      = ($salaryResult['SS_Misc'] / 100) * $insertArray['US_GrossSal'];
                            }*/
                        }  else {  // if salary is not numeric
                            array_push($errorColumns,"Gross Salary");
                            array_push($remarks,"Gross Salary should be numeric only");
                            $flag = 1;
                        }
                    }  // if salary structure doesnot exists
                    else {
                        array_push($errorColumns,"Salary Structure");
                        array_push($remarks,"Salary Structure doesn't exist");
                        $flag = 1; 
                    }   
                }
            }   else {  // if user with given username doesnot exists
                array_push($errorColumns,"User ID");
                array_push($remarks,"User ID doesn't exist");
                $flag = 1;
            }
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            if($flag == 1) {  // if error
                $this->userDetailsErrorArray[] = $excelArray[$i];// append error record to array
            }  else 
                $finalInsertArray[] = $insertArray; 
        }
        foreach ($finalInsertArray as $userArray) {
            mysqli_query($GLOBALS['con'],"UPDATE users_personal SET US_Gender='".$userArray['US_Gender']."',US_DOB='".$userArray['US_DOB']."',US_Address='".$userArray['US_Address']."',CT_Id='".$userArray['CT_Id']."',ST_Id='".$userArray['ST_Id']."',CN_Id='".$userArray['CN_Id']."',US_Pemail1='".$userArray['US_Pemail1']."',US_Pemail2='".$userArray['US_Pemail2']."',US_Mobile1='".$userArray['US_Mobile1']."',US_Mobile2='".$userArray['US_Mobile2']."',US_Landline1='".$userArray['US_Landline1']."',US_Landline2='".$userArray['US_Landline2']."',US_Relative='".$userArray['US_Relative']."',BG_Id='".$userArray['BG_Id']."',US_Guardian='".$userArray['US_Guardian']."',US_Guardianphone='".$userArray['US_Guardianphone']."',US_Emergencyperson='".$userArray['US_Emergencyperson']."',US_Emergencynumber='".$userArray['US_Emergencynumber']."',US_Emergencyrelation='".$userArray['US_Emergencyrelation']."',US_Passport='".$userArray['US_Passport']."' WHERE US_Id=".$userArray['US_Id']);
            mysqli_query($GLOBALS['con'],"UPDATE users_qualification SET US_Qualification='".$userArray['US_Qualification']."',US_Specialization='".$userArray['US_Specialization']."',US_Experience='".$userArray['US_Experience']."',US_LastEmployee='".$userArray['US_LastEmployee']."' WHERE US_Id=".$userArray['US_Id']);
            mysqli_query($GLOBALS['con'],"UPDATE users_accounts SET US_PAN='".$userArray['US_PAN']."',SP_Id='".$userArray['SP_Id']."',US_AccNo='".$userArray['US_AccNo']."',US_Bankname='".$userArray['US_Bankname']."',US_BankBranch='".$userArray['US_BankBranch']."',US_PFNo='".$userArray['US_PFNo']."',US_ESI='".$userArray['US_ESI']."',BA_Id=".$userArray['BA_Id']." WHERE US_Id=".$userArray['US_Id']);
            mysqli_query($GLOBALS['con'],"UPDATE users_salary SET SS_Id='".$userArray['SS_Id']."',US_GrossSal='".$userArray['US_GrossSal']."',US_DASal='".$userArray['US_DASal']."',US_HRASal='".$userArray['US_HRASal']."',US_ConveySal='".$userArray['US_ConveySal']."',US_EduSal='".$userArray['US_EduSal']."',US_MedSal='".$userArray['US_MedSal']."',US_MiscSal='".$userArray['US_MiscSal']."',US_BasicSal='".$userArray['US_BasicSal']."' WHERE US_Id=".$userArray['US_Id']);
        }

        $path = $BASEPATH."uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
    }
    
    /* Function for adding new item through excel uploading */
    function newItemExcel($excelData,$preTally_user_id,$preTally_user_ofid)   {
        // get swaped positions of each entries
        $itemnamePosition       = $this->cleanData($_REQUEST['IT_Name']);
        $subheadPosition        = $this->cleanData($_REQUEST['SH_Name']);
        $remarksPosition        = $this->cleanData($_REQUEST['IT_Comments']);
        $businessPosition       = $this->cleanData($_REQUEST['IT_Business']);
        $transferPosition       = $this->cleanData($_REQUEST['IT_Transfers']);
        
        $rptPntTree             = array();
        $finalInsertArray       = array();
        $this->itemErrorArray   = array();   // error array
        $insertArray            = array();
        $itemNameArray          = array();
        $curDate                = date('Y-m-d');
        $excelArray             = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
       
        for($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks            = array();          // remarks for errors
            unset($insertArray);
            $flag = 0;
            if($excelArray[$i][$itemnamePosition] != "" && $excelArray[$i][$itemnamePosition] != 'null' 
                && $excelArray[$i][$subheadPosition] != "" && $excelArray[$i][$subheadPosition] != 'null') {
                
                if(strcasecmp($excelArray[$i][$businessPosition], "yes") == 0 && strcasecmp($excelArray[$i][$transferPosition], "yes") == 0) {
                    array_push($errorColumns,"Business Entry/Internal Transfers");
                    array_push($remarks,"Both can't be YES");
                    $flag = 1;
                }
                $checkItemName      = $this->getValues("count(*) as count", " IT_Name = '".$this->cleanData($excelArray[$i][$itemnamePosition])."' AND OF_Id=$preTally_user_ofid AND IT_Status != 4", "items");
                $itemNameArray[]    =  $excelArray[$i][$itemnamePosition];
                $itemNameArrayCount = array_count_values($itemNameArray);
                
                if($checkItemName['count'] > 0  || $itemNameArrayCount[$excelArray[$i][$itemnamePosition]] > 1) {
                    array_push($errorColumns,"Item Name");
                    array_push($remarks,"Item already exists");
                    $flag = 1;
                }  else {
                    $subheadQuery = mysqli_query($GLOBALS['con'],"SELECT sh.SH_Id,mh.MH_Type FROM sub_heads as sh JOIN main_heads as mh ON sh.MH_Id=mh.MH_Id AND sh.SH_Name = '".$this->cleanData($excelArray[$i][$subheadPosition])."' AND sh.SH_Status=1");
                    if(mysqli_num_rows($subheadQuery) > 0) {
                        $subheadResult = mysqli_fetch_array($subheadQuery,MYSQLI_ASSOC);
                        $insertArray['US_Id']       = $preTally_user_id;
                        $insertArray['OF_Id']       = $preTally_user_ofid;
                        $insertArray['SH_Id']       = $subheadResult['SH_Id'];
                        $insertArray['MH_Type']     = $subheadResult['MH_Type'];
                        $insertArray['IT_Name']     = $this->cleanData($excelArray[$i][$itemnamePosition]);
                        $insertArray['IT_Comments'] = $this->cleanData($excelArray[$i][$remarksPosition]);
                        $insertArray['IT_Approved'] = $preTally_user_id;
                        
                        $IT_Notf = $this->getReportingTree($preTally_user_id,$rptPntTree);
                        $insertArray['IT_Notf']	= $IT_Notf;
                        $offAdm = $this->getValues("OF_Admin","OF_Id=$preTally_user_ofid","offices");
                        
                        $offAdm = $offAdm['OF_Admin'];
                        if($offAdm == $preTally_user_id) { 
                            $insertArray['IT_Approval']   = 0 ;
                            $insertArray['IT_Status']     = 1;
                        }   else {
                            $IT_Approval = $this->getValues("US_Report", " US_Id = $preTally_user_id", "users_auth");
                            $insertArray["IT_Approval"]   = $IT_Approval['US_Report'];
                            $insertArray["IT_Status"]     = 3;
                        }
                        if (strcasecmp($excelArray[$i][$businessPosition], "yes") == 0) {
                            $insertArray["IT_Business"] = 1;
                        }  else if(strcasecmp($excelArray[$i][$businessPosition], "no") == 0 || $excelArray[$i][$businessPosition] == "" || $excelArray[$i][$businessPosition] == "null") {
                            $insertArray["IT_Business"] = 0;
                        }   else {
                            array_push($errorColumns,"Business Entry");
                            array_push($remarks,"Business Entry should be yes or no");
                            $flag  = 1;
                        }
                        
                        if(strcasecmp($excelArray[$i][$transferPosition], "yes") == 0) {
                            $insertArray["IT_Transfers"] = 1;
                        } else if(strcasecmp($excelArray[$i][$transferPosition], "no") == 0 || $excelArray[$i][$transferPosition] == "" || $excelArray[$i][$transferPosition] == "null") {
                            $insertArray["IT_Transfers"] = 0;
                        }  else {
                            array_push($errorColumns,"Internal Transfers");
                            array_push($remarks,"Internal Transfers should be yes or no");
                            $flag  = 1;
                        }
                        
                        $insertArray['IT_MDate'] = $curDate;
                        $insertArray['IT_CDate'] = $curDate;
                    } else {
                        array_push($errorColumns,"Subhead");
                        array_push($remarks,"Doesn't exist");
                        $flag = 1;
                    }
                }
            } else {
                array_push($errorColumns,"Item Name/Subhead");
                array_push($remarks,"Mandatory fields");
                $flag = 1;
            }    
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            if($flag == 1) 
                $this->itemErrorArray[] = $excelArray[$i];
            else 
                $finalInsertArray[] = $insertArray; 
        }
        $valuesSQL = "";
        $insertSQL = "INSERT INTO items (US_Id,OF_Id,SH_Id,MH_Type,IT_Name,IT_Comments,IT_Approval,IT_Approved,IT_Notf,IT_Business,IT_Transfers,TR_Id,IT_MDate,IT_CDate,IT_Status) VALUES ";
        $counter=0;
        foreach ($finalInsertArray as $itemArray) {
            if (!empty($valuesSQL)) 
                $valuesSQL .= ", ";
            $valuesSQL .= "(" . $itemArray['US_Id']. ",".$itemArray['OF_Id'] .",".$itemArray['SH_Id'].",".$itemArray['MH_Type'].",'".$itemArray['IT_Name']."','".$itemArray['IT_Comments']."',".$itemArray['IT_Approval'].",".$itemArray['IT_Approved'].",'".$itemArray['IT_Notf']."',".$itemArray['IT_Business'].",".$itemArray['IT_Transfers'].",NULL,'".$itemArray['IT_MDate']."','".$itemArray['IT_CDate']."',".$itemArray['IT_Status'].")";

            if($counter == 1000) {
                $counter = 0;
                mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
                $valuesSQL = "";
            }
            $counter++;
        }
        if(!empty($valuesSQL)) 
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);

        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
    }
    
    
    function newDescriptionExcel($excelData,$preTally_user_id,$preTally_user_ofid) {
        // get swaped positions of each entries
        $descriptionPosition   = $this->cleanData($_REQUEST['DS_Description']);
        $itemNamePosition      = $this->cleanData($_REQUEST['IT_Name']);
        $excelArray           = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        $this->descErrorArray = array();   // error array
        $finalInsertArray     = array();
        $insertArray          = array();
        $curDate              = date('Y-m-d');
        
        for($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            $totalCells     = $excelData['numCols'];  // get total cells in array 
            $errorColumns   = array();    // error columns
            $remarks        = array();          // remarks for errors
            unset($insertArray);
            $flag = 0;
            if($excelArray[$i][$descriptionPosition] != "" && $excelArray[$i][$descriptionPosition] != 'null' 
                && $excelArray[$i][$itemNamePosition] != "" && $excelArray[$i][$itemNamePosition] != 'null') {
                
                $itemResult = $this->getValues("IT_Id", "IT_Name = '".$this->cleanData($excelArray[$i][$itemNamePosition])."' AND OF_Id=$preTally_user_ofid  AND (IT_Status != 0 AND IT_Status != 4)", "items");
                if($itemResult['IT_Id']) {
                    $insertArray['IT_Id']  = $itemResult['IT_Id'];
                    
                    for($j=1; $j < $i; $j++) {
                        if(($excelArray[$i][$descriptionPosition] == $excelArray[$j][$descriptionPosition]) && 
                            $excelArray[$i][$itemNamePosition] == $excelArray[$j][$itemNamePosition] ) {
                            array_push($errorColumns, "Description");  
                            array_push($remarks, "Description already exists in excel");  
                            $flag  = 1;
                        }
                    }
                    
                    $descExistResult = $this->getValues("count(*) as count", "DS_Description = '".$this->cleanData($excelArray[$i][$descriptionPosition])."' AND OF_Id=$preTally_user_ofid AND DS_Status != 4  AND IT_Id=".$insertArray['IT_Id'], "descriptions");
                    if($descExistResult['count'] > 0) { // description already exists
                        array_push($errorColumns, "Description");   
                        array_push($remarks, "Description already exist");  
                        $flag = 1;
                    }  else {
                        $insertArray['DS_Description']  = $this->cleanData($excelArray[$i][$descriptionPosition]);
                        $insertArray['US_Id']           = $preTally_user_id;
                        $insertArray['OF_Id']           = $preTally_user_ofid;
                        $offAdm = $this->getValues("OF_Admin"," OF_Id=$preTally_user_ofid ","offices");
                        
                        $offAdm = $offAdm['OF_Admin'];
                        if($offAdm == $preTally_user_id) { 
                            $insertArray['DS_Approval']   = 0 ;
                            $insertArray['DS_Status']     = 1;
                        }   else {
                            $IT_Approval = $this->getValues("US_Report", " US_Id = $preTally_user_id", "users_auth");
                            $insertArray["DS_Approval"]   = $IT_Approval['US_Report'];
                            $insertArray["DS_Status"]     = 3;
                        }
                        $insertArray['DS_Approved']     = $preTally_user_id;
                        $insertArray["DS_CDate"]        = $curDate;
                        $insertArray["DS_MDate"]        = $curDate;
                    }
                }  else {
                    array_push($errorColumns, "Item Name"); 
                    array_push($remarks, "Item doesnot exist");  
                    $flag = 1;
                }    
            }   else {
                array_push($errorColumns, "Description/Item Name"); 
                array_push($remarks, "Mandatory fields");  
                $flag = 1;      
            }    
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
             
            if($flag == 1) 
                $this->descErrorArray[]     = $excelArray[$i];
            else 
                $finalInsertArray[]         = $insertArray; 
        }
        $valuesSQL  = "";
        $insertSQL  = "INSERT INTO descriptions (US_Id,IT_Id,OF_Id,DS_Description,DS_Approval,DS_Approved,DS_Notf,DS_Status,DS_CDate,DS_MDate) VALUES ";
        $counter    = 0;
       
        foreach ($finalInsertArray as $itemArray) {
            if (!empty($valuesSQL))  
                $valuesSQL .= ", ";
            $valuesSQL .= "(" . $itemArray['US_Id']. ",".$itemArray['IT_Id'] .",".$itemArray['OF_Id'].",'".$itemArray['DS_Description']."',".$itemArray['DS_Approval'].",".$itemArray['DS_Approved'].",'',".$itemArray['DS_Status'].",'".$itemArray['DS_CDate']."','".$itemArray['DS_MDate']."')";

            if($counter == 1000) {
                $counter = 0;
                mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
                $valuesSQL = "";
            }
            $counter++;
        }
        if(!empty($valuesSQL)) {
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
        }
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
        
    }
    
    function leaveExcel($excelData,$preTally_user_id,$preTally_user_ofid) {
        // get swaped positions of each entries
        $userIDPosition          = $_REQUEST['US_Id'];
        $leaveTypePosition       = $_REQUEST['LT_Name'];
        $datePosition            = $_REQUEST['Date'];
        $sessionPosition        = $_REQUEST['Session'];
        $excelArray             = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        $this->leaveErrorArray  = array();   // error array
        $finalInsertArray       = array();
        $insertArray            = array();
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php'); 
        for($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks = array();          // remarks for errors
            unset($insertArray);
            $flag               = 0;
            if($excelArray[$i][$userIDPosition] != "" && $excelArray[$i][$userIDPosition] != 'null'  
                && $excelArray[$i][$leaveTypePosition] != "" && $excelArray[$i][$leaveTypePosition] != 'null'  
                && $excelArray[$i][$datePosition] != "" && $excelArray[$i][$sessionPosition] != 'null') {
                
                $userResult     = $this->getValues("US_Id,OF_Id", " US_EMPID = '".$this->cleanData($excelArray[$i][$userIDPosition])."'", "users_auth");
                
                if($userResult['US_Id']) {
                    if($preTally_user_ofid == 1 ) { // if admin
                        $flag = 0;
                    }   else if($preTally_user_ofid == $userResult['OF_Id'] ||  $this->offzAdmin($userResult['OF_Id']) == $preTally_user_id) {
                        $flag = 0; 
                    } else {
                        array_push($errorColumns,"User ID");
                        array_push($remarks,"Logged in user is not allowed to insert this record");
                        $flag = 1;
                    }
                    
                    $insertArray['US_Id']  = $userResult['US_Id'];
                    
                    $leaveTypeResult = $this->getValues("COUNT(*) AS count, LT_Id", "LT_Name = '".$this->cleanData($excelArray[$i][$leaveTypePosition])."' AND OF_Id= ".$userResult['OF_Id']."  AND LT_Status = 1 ", "leave_type");
                    if($leaveTypeResult['count'] == 0) { 
                        array_push($errorColumns,"Leave Type");
                        array_push($remarks,"Leave Type doesn't exist");
                        $flag = 1;
                    } else { 
                        $insertArray['LT_Id']           = $leaveTypeResult['LT_Id'];
                        $insertArray['LR_NumOFDays']    = 1;
                        
                        $dateArray = explode("-",$excelArray[$i][$datePosition]);
                        if(count($dateArray) == 0) {
                            array_push($errorColumns,"Date");
                            array_push($remarks,"Date should be in MM-DD-YYYY format");
                            $flag = 1;
                        }
                        
                        $LRDateInfo = date_parse($excelArray[$i][$datePosition]);
                        if($LRDateInfo['warning_count'] == 0 && $LRDateInfo['error_count'] == 0 ){
                            $LRDate = new DateTime($excelArray[$i][$datePosition]);
                            $insertArray['LR_FromDate']     = $LRDate->format('Y-m-d');
                            $insertArray['LR_ToDate']       = $insertArray['LR_FromDate'];
                            $insertArray['LR_CreditedDate'] = $insertArray['LR_FromDate'];
                            $insertArray['LR_CDate']        = date("Y-m-d");
                            $insertArray['LRD_Date']        = $insertArray['LR_FromDate'];
                        } else{
                            array_push($errorColumns,"Date");
                            array_push($remarks,"Invalid date(Date should be in MM-DD-YYYY format)");
                            $flag = 1;
                        }
                        if($excelArray[$i][$sessionPosition]=="FL" || 
                            $excelArray[$i][$sessionPosition]=="FN" || 
                            $excelArray[$i][$sessionPosition]=="AN"){                            
                            $insertArray['LR_Session']=$excelArray[$i][$sessionPosition];
                            if($excelArray[$i][$sessionPosition]=="FL")
                                $insertArray['LR_NumOFDays']=1;
                            else
                                $insertArray['LR_NumOFDays']=0.5;
                        }else{
                            array_push($errorColumns,"Session");
                            array_push($remarks,"Invalid Session");
                            $flag = 1;
                        }
                        
                        $insertArray['LR_CreditedbyUserId'] = $preTally_user_id;
                        $insertArray['LR_Status']           = 2;
                        $insertArray['LRD_Days']            = 1;
                    }
                }  else {
                    array_push($errorColumns,"User ID");
                    array_push($remarks,"User ID doesn't exist");
                    $flag = 1;
                }    
            }  else {
                array_push($errorColumns,"User ID/Leave Type/Date");
                array_push($remarks,"All fields are mandatory");
                $flag = 1;      
            }     
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            
            if($flag == 1) {
                $this->leaveErrorArray[]    = $excelArray[$i];
            }  else {
                $finalInsertArray[]         = $insertArray; 
            }
        }
       
        $insertSQL = "INSERT INTO leave_request (US_Id,LR_AppliedFor,LT_Id,LR_NumOFDays,LR_FromDate,LR_ToDate,LR_Session,LR_CreditedDate,LR_Status,LR_CDate,LR_FirstApproval,LR_ApprovedHR,LR_Reason) VALUES ";
        foreach ($finalInsertArray as $leaveArray) {
            $valuesSQL  = "(" . $leaveArray['US_Id'].",".$leaveArray['US_Id'] . ",".$leaveArray['LT_Id'] .",".$leaveArray['LR_NumOFDays'].",'".$leaveArray['LR_FromDate']."','".$leaveArray['LR_ToDate']."','".$leaveArray['LR_Session']."','".$leaveArray['LR_CreditedDate']."',".$leaveArray['LR_Status'].",'".date("Y-m-d")."',".$insertArray['LR_CreditedbyUserId'].",".$insertArray['LR_CreditedbyUserId'].",'Assigned By Admin')";            
            
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
            $LR_Id      = mysqli_insert_id($GLOBALS['con']);            
            mysqli_query($GLOBALS['con'],"INSERT INTO leave_reqdays (LR_Id,US_Id,LT_Id,LRD_Date,LRD_Days,LRD_Session) VALUES (".$LR_Id.",".$leaveArray['US_Id'].",".$leaveArray['LT_Id'].",'".$leaveArray['LR_FromDate']."','".$leaveArray['LR_NumOFDays']."','".$leaveArray['LR_Session']."') ");
        }
     
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
    }
    
    function trackSubProcessExcel($excelData,$OFId) {
        $mainProcessPosition     = $_REQUEST['TK_Main'];
        $subProcessPosition      = $_REQUEST['TK_SubProcess'];
        $streetPosition          = $_REQUEST['TK_Street'];
        $placePosition           = $_REQUEST['TK_Place'];
        $locationPosition        = $_REQUEST['TK_Location'];
        $cityPosition            = $_REQUEST['TK_City'];
        $statePosition           = $_REQUEST['TK_State'];
        $countryPosition         = $_REQUEST['TK_Country'];
        $pincodePosition         = $_REQUEST['TK_Pincode'];
        $statutoryAmtPosition    = $_REQUEST['TK_StatutoryAmt'];
        $StatutoryUAmtPosition   = $_REQUEST['TK_StatutoryUAmt'];
        $extraNAmtPosition       = $_REQUEST['TK_ExtraNAmt'];
        $extraUAmtPosition       = $_REQUEST['TK_ExtraUAmt'];
        $courierNAmtPosition     = $_REQUEST['TK_CourierNAmt'];
        $courierUAmtPosition     = $_REQUEST['TK_CourierUAmt'];
        $travellingNAmtPosition  = $_REQUEST['TK_TravellingNAmt'];
        $travellingUAmtPosition  = $_REQUEST['TK_TravellingUAmt'];
        $manpowerNAmtPosition    = $_REQUEST['TK_ManpowerNAmt'];
        $manpowerUAmtPosition    = $_REQUEST['TK_ManpowerUAmt'];
        $serviceNAmtPosition     = $_REQUEST['TK_ServiceNAmt'];
        $serviceUAmtPosition     = $_REQUEST['TK_ServiceUAmt'];
        $fromYearPosition        = $_REQUEST['TK_FromYear'];
        $toYearPosition          = $_REQUEST['TK_ToYear'];
        
        $excelArray             = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        $this->subProcessErrorArray  = array();   // error array
        $finalInsertArray       = array();
        $insertArray            = array();
        
        $processPosArray        = array($mainProcessPosition,$streetPosition,$placePosition,$locationPosition,$cityPosition,$statePosition,$countryPosition);
        $errorColsArray         = array("Main Process","Sub Process","Street","Place","Location","City","State","Country");
        $mandatoryColsArray     = array("Main Process","Sub Process","Street","Place","Location","City","State","Country","Pincode","Statutory Amount","Statutory Amount - Urgent","Extra Amount - Normal","Extra Amount - Urgent","Courier Charge - Normal","Courier Charge - Urgent","Travelling Expense - Normal ","Travelling Expense - Urgent","Manpower Charge - Normal","Manpower Charge - Urgent","Service Charge - Normal","Service Charge - Urgent","From Year","To Year");
        $remarksArray           = array("Main Process should contain alphabets only","Sub Process should contain alphabets only","Street should contain alphabets only",
                                        "Place should contain alphabets only","Location should contain alphabets only","City should contain alphabets only",
                                        "State should contain alphabets only","Country should contain alphabets only");
        /*
         * ,
                                        $statutoryAmtPosition,$StatutoryUAmtPosition,$extraNAmtPosition,$extraUAmtPosition,$courierNAmtPosition,$courierUAmtPosition,
                                        $travellingNAmtPosition,$travellingUAmtPosition,$manpowerNAmtPosition,$manpowerUAmtPosition,$serviceNAmtPosition,$serviceUAmtPosition
         * 
         * 
         * ,"Statutory Amount","Statutory Amount - Urgent",
                                        "Extra Amount - Normal","Extra Amount - Urgent","Courier Charge - Normal","Courier Charge - Urgent","Travelling Expense - Normal",
                                        "Travelling Expense - Urgent","Manpower Charge - Normal","Manpower Charge - Urgent", "Service Charge - Normal","Service Charge - Urgent"
         * 
         * ,
                                        "Statutory Amount should be numeric only","Statutory Urgent Amount should be numeric only",
                                        "Extra Amount - Normal should be numeric only","Extra Amount - Urgent should be numeric only",
                                        "Courier Charge - Normal should be numeric only","Courier Charge - Urgent should be numeric only",
                                        "Travelling Expense - Normal should be numeric only","Travelling Expense - Urgent should be numeric only",
                                        "Manpower Charge - Normal should be numeric only","Manpower Charge - Urgent should be numeric only",
                                        "Service Charge - Normal should be numeric only","Service Charge - Urgent should be numeric only",
         */
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php'); 
        for($i = 1; $i < $excelData['numRows']; $i++) {  // for each records in array
            
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks            = array();          // remarks for errors
            $curDate            = date('Y-m-d H:i:s');
            unset($insertArray);
            $flag               = 0;
           
            $excelArray[$i] = array_map('strval', $excelArray[$i]); // to convert integer values to string

            if( ! array_search("", $excelArray[$i])) {
//            if($excelArray[$i][$mainProcessPosition] != "" && $excelArray[$i][$mainProcessPosition] != 'null'  
//                && $excelArray[$i][$subProcessPosition] != "" && $excelArray[$i][$subProcessPosition] != 'null') {
                
                $resultArray = $this->dataValdation($excelArray[$i],$processPosArray,$errorColsArray,$remarksArray);
              
                if( array_search("", $resultArray[0]) || !$resultArray[0] ) { 
                    $countryQuery = mysqli_query($GLOBALS['con'],"SELECT CN_Id FROM countries WHERE CN_Name='".$this->cleanData($excelArray[$i][$countryPosition])."'");
                        if(mysqli_num_rows($countryQuery) > 0) {  // if country exists
                            $countryResultset = mysqli_fetch_array($countryQuery,MYSQLI_ASSOC);
                            $insertArray['CN_Id'] = $this->cleanData($countryResultset['CN_Id']);

                        $stateQuery = mysqli_query($GLOBALS['con'],"SELECT ST_Id FROM states WHERE CN_Id=".$insertArray['CN_Id']." AND ST_Name='".$this->cleanData($excelArray[$i][$statePosition])."'");
                            if(mysqli_num_rows($stateQuery) > 0) {
                                $stateResultset = mysqli_fetch_array($stateQuery,MYSQLI_ASSOC);
                                $insertArray['ST_Id'] = $this->cleanData($stateResultset['ST_Id']);
                            } else {
                                $newStateQuery = mysqli_query($GLOBALS['con'],"INSERT INTO states (ST_Name,CN_Id,US_Id,ST_CDate,ST_MDate) VALUES ('".$excelArray[$i][$statePosition]."',".$insertArray['CN_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                                $insertArray['ST_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }

                        $cityQuery = mysqli_query($GLOBALS['con'],"SELECT CT_Id FROM cities WHERE ST_Id=".$insertArray['ST_Id']." AND CT_Name='".$this->cleanData($excelArray[$i][$cityPosition])."'");
                            if(mysqli_num_rows($cityQuery) > 0) {
                                $cityResultset = mysqli_fetch_array($cityQuery,MYSQLI_ASSOC);
                                $insertArray['CT_Id'] = $this->cleanData($cityResultset['CT_Id']);
                            }   else {
                                $newCityQuery = mysqli_query($GLOBALS['con'],"INSERT INTO cities (CT_Name,ST_Id,US_Id,CT_CDate,CT_MDate) VALUES ('".$excelArray[$i][$cityPosition]."',".$insertArray['ST_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                                $insertArray['CT_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }

                        $locationQuery = mysqli_query($GLOBALS['con'],"SELECT ALC_Id FROM addr_locations WHERE CT_Id=".$insertArray['CT_Id']." AND ALC_Name='".$this->cleanData($excelArray[$i][$locationPosition])."'");
                            if(mysqli_num_rows($locationQuery) > 0) { 
                                $locationResultset = mysqli_fetch_array($locationQuery,MYSQLI_ASSOC);
                                $insertArray['ALC_Id'] = $this->cleanData($locationResultset['ALC_Id']);
                            }  else { 
                                $newlocationQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_locations (ALC_Name,US_Id,CT_Id,ALC_CDate,ALC_MDate,ALC_Status) VALUES ('".$excelArray[$i][$locationPosition]."',0,".$insertArray['CT_Id'].",'".$curDate."','".$curDate."',1)");
                                $insertArray['ALC_Id'] = mysqli_insert_id($GLOBALS['con']);
                            }

                        $placeQuery = mysqli_query($GLOBALS['con'],"SELECT PL_Id FROM addr_places WHERE ALC_Id=".$insertArray['ALC_Id']." AND PL_Name='".$this->cleanData($excelArray[$i][$placePosition])."'");
                        if(mysqli_num_rows($placeQuery) > 0) { 
                            $placeResultset = mysqli_fetch_array($placeQuery,MYSQLI_ASSOC);
                            $insertArray['PL_Id'] = $this->cleanData($placeResultset['PL_Id']);
                        }  else { 
                            $newplaceQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_places (PL_Name,US_Id,ALC_Id,PL_CDate,PL_MDate,PL_Status) VALUES ('".$excelArray[$i][$placePosition]."',0,".$insertArray['ALC_Id'].",'".$curDate."','".$curDate."',1)");
                            $insertArray['PL_Id'] = mysqli_insert_id($GLOBALS['con']);
                        }

                        $streetQuery = mysqli_query($GLOBALS['con'],"SELECT SR_Id FROM addr_streets WHERE PL_Id=".$insertArray['PL_Id']." AND SR_Name='".$this->cleanData($excelArray[$i][$streetPosition])."'");
                        if(mysqli_num_rows($streetQuery) > 0) { 
                            $streetResultset = mysqli_fetch_array($streetQuery,MYSQLI_ASSOC);
                            $insertArray['SR_Id'] = $this->cleanData($streetResultset['SR_Id']);
                        }  else { 
                            $newstreetQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_streets (SR_Name,US_Id,PL_Id,SR_CDate,SR_MDate,SR_Status) VALUES ('".$excelArray[$i][$streetPosition]."',0,".$insertArray['PL_Id'].",'".$curDate."','".$curDate."',1)");
                            $insertArray['SR_Id'] = mysqli_insert_id($GLOBALS['con']);
                        }

                    }   else { // if country doesnot exists

                        $newCountryQuery = mysqli_query($GLOBALS['con'],"INSERT INTO countries (CN_Name) VALUES ('".$this->cleanData($excelArray[$i][$countryPosition])."')");
                        $insertArray['CN_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newStateQuery = mysqli_query($GLOBALS['con'],"INSERT INTO states (ST_Name,CN_Id,US_Id,ST_CDate,ST_MDate) VALUES ('".$this->cleanData($excelArray[$i][$statePosition])."',".$insertArray['CN_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                        $insertArray['ST_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newCityQuery = mysqli_query($GLOBALS['con'],"INSERT INTO cities (CT_Name,ST_Id,US_Id,CT_CDate,CT_MDate) VALUES ('".$this->cleanData($excelArray[$i][$cityPosition])."',".$insertArray['ST_Id'].",".$preTally_user_ofid.",'".$curDate."','".$curDate."')");
                        $insertArray['CT_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newlocationQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_locations (ALC_Name,US_Id,CT_Id,ALC_CDate,ALC_MDate,ALC_Status) VALUES ('".$excelArray[$i][$locationPosition]."',0,".$insertArray['CT_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['ALC_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newplaceQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_places (PL_Name,US_Id,PL_Id,PL_CDate,PL_MDate,PL_Status) VALUES ('".$excelArray[$i][$placePosition]."',0,".$insertArray['ALC_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['PL_Id'] = mysqli_insert_id($GLOBALS['con']);

                        $newstreetQuery = mysqli_query($GLOBALS['con'],"INSERT INTO addr_streets (SR_Name,US_Id,PL_Id,SR_CDate,SR_MDate,SR_Status) VALUES ('".$excelArray[$i][$streetPosition]."',0,".$insertArray['PL_Id'].",'".$curDate."','".$curDate."',1)");
                        $insertArray['SR_Id'] = mysqli_insert_id($GLOBALS['con']);
                    }
                    $mainProcess    = $this->cleanData($excelArray[$i][$mainProcessPosition]);
//                        if(preg_match("/^[a-zA-Z ]+$/", $mainProcess)) {
                    $mainProcessResult          = $this->getValues("APM_Id", " APM_Title = '".$mainProcess."' AND OF_Id = '".$OFId."' ", "attestation_process_main"); 
                    if($mainProcessResult['APM_Id']) {
                        $insertArray['APM_Id'] = $mainProcessResult['APM_Id'];
                        
                        //$subProcess = $this->cleanData($excelArray[$i][$subProcessPosition]);
                        $subProcess = htmlspecialchars($excelArray[$i][$subProcessPosition], ENT_QUOTES); // to add special characters 
                        $subProcessResult = $this->getValues("COUNT(*) AS COUNT", " APM_Id = ".$mainProcessResult['APM_Id']." AND APS_Title = '".$subProcess."' AND OF_Id = '".$OFId."'", "attestation_process_sub");
                        if($subProcessResult['COUNT'] > 0) { 
                            array_push($errorColumns,"Sub Process");
                            array_push($remarks,"Sub Process already exists");
                            $flag = 1; 
                        } else {
                            $insertArray['APS_Title'] = $subProcess;
                            $insertArray['OF_Id']     = $OFId;
                            $insertArray['APS_StatutoryNAmt']   = ($this->cleanData($excelArray[$i][$statutoryAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$statutoryAmtPosition]) : '';
                            if($insertArray['APS_StatutoryNAmt']) {
                                if(!is_numeric($insertArray['APS_StatutoryNAmt'])) {
                                    array_push($errorColumns,"Statutory Amount");
                                    array_push($remarks,"Statutory Amount should be numeric only");
                                    $flag = 1; 
                                }
                            } 
                            
                            $insertArray['APS_Pincode']   = ($this->cleanData($excelArray[$i][$pincodePosition]) != '') ? $this->cleanData($excelArray[$i][$pincodePosition]) : '';
                            if($insertArray['APS_Pincode']) {
                                if(!is_numeric($insertArray['APS_Pincode'])) {
                                    array_push($errorColumns,"Pincode");
                                    array_push($remarks,"Pincode Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }  
                            
                            $insertArray['APS_StatutoryUAmt']   = ($this->cleanData($excelArray[$i][$StatutoryUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$StatutoryUAmtPosition]) : '';
                            if($insertArray['APS_StatutoryUAmt']) {
                                if(!is_numeric($insertArray['APS_StatutoryUAmt'])) {
                                    array_push($errorColumns,"Statutory Urgent Amount");
                                    array_push($remarks,"Statutory Urgent Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }  

                            $insertArray['APS_ExtraNAmt']   = ($this->cleanData($excelArray[$i][$extraNAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$extraNAmtPosition]) : '';
                            if($insertArray['APS_ExtraNAmt']) {
                                if(!is_numeric($insertArray['APS_ExtraNAmt'])) {
                                    array_push($errorColumns,"Extra Amount - Normal");
                                    array_push($remarks,"Extra Amount - Normal should be numeric only");
                                    $flag = 1; 
                                }
                            }
                            $insertArray['APS_ExtraUAmt']   = ($this->cleanData($excelArray[$i][$extraUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$extraUAmtPosition]) : '';
                            if($insertArray['APS_ExtraUAmt']) {
                                if(!is_numeric($insertArray['APS_ExtraUAmt'])) {
                                    array_push($errorColumns,"Extra Amount - Urgent");
                                    array_push($remarks,"Extra Amount - Urgent should be numeric only");
                                    $flag = 1; 
                                }
                            }

                            $insertArray['APS_CourierNAmt']  = ($this->cleanData($excelArray[$i][$courierNAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$courierNAmtPosition]) : '';
                            if($insertArray['APS_CourierNAmt']) {
                                if(!is_numeric($insertArray['APS_CourierNAmt'])) {
                                    array_push($errorColumns,"Courier Amount - Normal");
                                    array_push($remarks,"Courier Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }  
                            $insertArray['APS_CourierUAmt']  = ($this->cleanData($excelArray[$i][$courierUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$courierUAmtPosition]) : '';
                            if($insertArray['APS_CourierUAmt']) {
                                if(!is_numeric($insertArray['APS_CourierUAmt'])) {
                                    array_push($errorColumns,"Courier Amount - Urgent");
                                    array_push($remarks,"Courier Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }

                            $insertArray['APS_TravellingNAmt']   = ($this->cleanData($excelArray[$i][$travellingNAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$travellingNAmtPosition]) : '';
                            if($insertArray['APS_TravellingNAmt']) {
                                if(!is_numeric($insertArray['APS_TravellingNAmt'])) {
                                    array_push($errorColumns,"Travelling Amount - Normal");
                                    array_push($remarks,"Travelling Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }
                            $insertArray['APS_TravellingUAmt']   = ($this->cleanData($excelArray[$i][$travellingUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$travellingUAmtPosition]) : '';
                            if($insertArray['APS_TravellingUAmt']) {
                                if(!is_numeric($insertArray['APS_TravellingUAmt'])) {
                                    array_push($errorColumns,"Travelling Amount - Urgent");
                                    array_push($remarks,"Travelling Amount should be numeric only");
                                    $flag = 1; 
                                }
                            }  

                            $insertArray['APS_ManpowerNAmt']   = ($this->cleanData($excelArray[$i][$manpowerNAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$manpowerNAmtPosition]) : '';
                            if($insertArray['APS_ManpowerNAmt']) {
                                if(!is_numeric($insertArray['APS_ManpowerNAmt'])) {
                                    array_push($errorColumns,"Manpower Amount - Normal");
                                    array_push($remarks,"Manpower Amount - Normal should be numeric only");
                                    $flag = 1; 
                                }
                            }
                            $insertArray['APS_ManpowerUAmt']   = ($this->cleanData($excelArray[$i][$manpowerUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$manpowerUAmtPosition]) : '';
                            if($insertArray['APS_ManpowerUAmt']) {
                                if(!is_numeric($insertArray['APS_ManpowerUAmt'])) {
                                    array_push($errorColumns,"Manpower Amount - Urgent");
                                    array_push($remarks,"Manpower Amount - Urgent should be numeric only");
                                    $flag = 1; 
                                }
                            }  

                            $insertArray['APS_ServiceNAmt']   = ($this->cleanData($excelArray[$i][$serviceNAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$serviceNAmtPosition]) : '';
                            if($insertArray['APS_ServiceNAmt']) {
                                if(!is_numeric($insertArray['APS_ServiceNAmt'])) {
                                    array_push($errorColumns,"Service Amount - Normal");
                                    array_push($remarks,"Service Amount - Normal should be numeric only");
                                    $flag = 1; 
                                }
                            }
                            $insertArray['APS_ServiceUAmt']   = ($this->cleanData($excelArray[$i][$serviceUAmtPosition]) != '') ? $this->cleanData($excelArray[$i][$serviceUAmtPosition]) : '';
                            if($insertArray['APS_ServiceUAmt']) {
                                if(!is_numeric($insertArray['APS_ServiceUAmt'])) {
                                    array_push($errorColumns,"Service Amount - Urgent");
                                    array_push($remarks,"Service Amount - Urgent should be numeric only");
                                    $flag = 1; 
                                }
                            }  

                            $insertArray['APS_FromYear']   = ($this->cleanData($excelArray[$i][$fromYearPosition]) != '') ? $this->cleanData($excelArray[$i][$fromYearPosition]) : '';
                            if($insertArray['APS_FromYear']) {
                                if( ($insertArray['APS_FromYear'] < 1950 ) || !is_numeric($insertArray['APS_FromYear']) ) { //  || ($insertArray['APS_FromYear'] > date("Y"))
//                                if(!is_numeric($insertArray['APS_FromYear'])) {
                                    array_push($errorColumns,"From Year");
                                    array_push($remarks,"Invalid From Year");
                                    $flag = 1; 
                                }
                            }
                            $insertArray['APS_ToYear']   = ($this->cleanData($excelArray[$i][$toYearPosition]) != '') ? $this->cleanData($excelArray[$i][$toYearPosition]) : '';
                            if( ($insertArray['APS_ToYear'] < 1950 ) || !is_numeric($insertArray['APS_ToYear']) ) { //  || ($insertArray['APS_FromYear'] > date("Y"))
                                if(!is_numeric($insertArray['APS_ToYear']) ) {
                                    array_push($errorColumns,"To Year");
                                    array_push($remarks,"Invalid To Year");
                                    $flag = 1; 
                                }
                            }

                            $insertArray['APS_CDate'] = $curDate;
                            $insertArray['APS_MDate'] = $curDate;
                        }
                        
                    } else {
                        array_push($errorColumns,"Main Process");
                        array_push($remarks,"Main Process dosen't exist");
                        $flag = 1; 
                                
//                        $newMainProcessQuery    = mysqli_query($GLOBALS['con'],"INSERT INTO attestation_process_main (APM_Title,APM_Title_Alias, APM_Description,CN_Id,ST_Id,CT_Id,PL_Id,ALC_Id,SR_Id,APM_CDate,APM_MDate,APM_Status) VALUES "
//                                                                . "('".$excelArray[$i][$mainProcessPosition]."','".$excelArray[$i][$mainProcessPosition]."','','".$insertArray['CN_Id']."','".$insertArray['ST_Id']."','".$insertArray['CT_Id']."','".$insertArray['ALC_Id']."','".$insertArray['PL_Id']."','".$insertArray['SR_Id']."','".$curDate."','".$curDate."',1)");
//                        $insertArray['APM_Id'] = mysqli_insert_id($GLOBALS['con']);
                    } 
//                        } else {
//                            array_push($errorColumns,"Main Process");
//                            array_push($remarks,"Main Process should contain alphabets only");
//                            $flag = 1; 
//                        }
                    
                }else{ 
                    foreach($resultArray[0] as $key => $value){
                        array_push($errorColumns,$value);
                        $flag = 1;
                    }
                    foreach($resultArray[1] as $key => $value){
                        array_push($remarks,$value);
                        $flag = 1;
                    }
                }
            }  else {
                $errorColumKeys = array_keys($excelArray[$i], "");
                $mandatoryCols = "";
                foreach ($errorColumKeys as $value) {
                   $mandatoryCols .=  $mandatoryColsArray[$value].', ' ;
                }
                array_push($errorColumns,  substr($mandatoryCols, 0, -2));
                array_push($remarks,"Mandatory fields");
                $flag = 1;      
            }     
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            
            if($flag == 1) {
                $this->subProcessErrorArray[] = $excelArray[$i];
            }  else {
                $finalInsertArray[]         = $insertArray; 
            }
        }
        
        $sql = "INSERT INTO attestation_process_sub ( " . implode(', ', array_keys($finalInsertArray[0])) . ") VALUES ";
        foreach ($finalInsertArray as $key => $value) {
            $sql .= "( '" . implode("','", array_values($finalInsertArray[$key])) . "'" . "),";
        }

        $sql=rtrim($sql, ",");
        mysqli_query($GLOBALS['con'],$sql);

//        $insertSQL = "INSERT INTO attestation_process_sub (APM_Id,APS_Title,APS_StatutoryNAmt,APS_ExtraNAmt,APS_CDate,APS_MDate) VALUES ";
//        foreach ($finalInsertArray as $subProcessArray) {
//            $valuesSQL  = "(" . $subProcessArray['TK_Main']. ",'".$subProcessArray['TK_SubProcess'] ."','".$subProcessArray['TK_StatutoryAmt']."','".$subProcessArray['TK_ExtraAmt']."','".$subProcessArray['APS_CDate']."','".$subProcessArray['APS_MDate']."')";
//            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);
//        }
     
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
    }
    function salHistExcel($excelData,$preTally_user_id,$preTally_user_ofid) {
        // get swaped positions of each entries
        $userIDPosition          = $_REQUEST['US_Id'];
        $SalHistAmtPosition       = $_REQUEST['SHist_Amt'];
        $datePosition            = $_REQUEST['Date'];
        
        $excelArray             = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        $this->SalHistErrorArray  = array();   // error array
        $finalInsertArray       = array();
        $insertArray            = array();
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php'); 
        for($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks = array();          // remarks for errors
            unset($insertArray);
            $flag               = 0;
            if($excelArray[$i][$userIDPosition] != "" && $excelArray[$i][$userIDPosition] != 'null'  
                && $excelArray[$i][$SalHistAmtPosition] != "" && $excelArray[$i][$SalHistAmtPosition] != 'null'  
                && $excelArray[$i][$datePosition] != "" && $excelArray[$i][$datePosition] != 'null') {
                
                $userResult     = $this->getValues("US_Id,OF_Id", " US_EMPID = '".$this->cleanData($excelArray[$i][$userIDPosition])."'", "users_auth");
                
                if($userResult['US_Id']) {
                    $insertArray['US_Id']  = $userResult['US_Id']; 
                    if(is_numeric($excelArray[$i][$SalHistAmtPosition])){
                    $insertArray['Sal_Amt']  = $excelArray[$i][$SalHistAmtPosition];
                    }else{
                    array_push($errorColumns,"Salary Amount");
                    array_push($remarks,"Amount should be numeric");
                    $flag = 1;
                    }
                    $SHDateInfo = date_parse($excelArray[$i][$datePosition]);
                        if($SHDateInfo['warning_count'] == 0 && $SHDateInfo['error_count'] == 0 ){
                            $LRDate = new DateTime($excelArray[$i][$datePosition]);
                            $insertArray['Sal_Date']     = $LRDate->format('Y-m-d');                            
                        } else{
                            array_push($errorColumns,"Salary Date");
                            array_push($remarks,"Date Fromat Error");  
                            $flag = 1;
                        }                    
                }  else {
                    array_push($errorColumns,"User ID");
                    array_push($remarks,"User ID doesn't exist");
                    $flag = 1;
                }    
            }  else {
                array_push($errorColumns,"User ID/Leave Type/Date");
                array_push($remarks,"All fields are mandatory");
                $flag = 1;      
            }     
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);
            
            if($flag == 1) {
                $this->leaveErrorArray[]    = $excelArray[$i];
            }  else {
                $finalInsertArray[]         = $insertArray; 
            }
        }
       
        $insertSQL = "INSERT INTO salary_history (US_Id,Sal_Amt,Sal_Date) VALUES ";
        foreach ($finalInsertArray as $salHistData) {
            $valuesSQL  = "(" . $salHistData['US_Id']. ",".$salHistData['Sal_Amt'] .",'".$salHistData['Sal_Date']."')";
            mysqli_query($GLOBALS['con'],$insertSQL.$valuesSQL);            
           //print($insertSQL.$valuesSQL);
        }
     
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);  
    }
    // 21-03-2025 new function for save the uploaded salary of employees
    // Created by Bilin 
    function salUpdtExcel($excelData,$preTally_user_id,$preTally_user_ofid) {
        // get swaped positions of each entries
        $userIDPosition     = $_REQUEST['US_Id'];
        $SalAmtPosition     = $_REQUEST['SUpd_Amt'];
        $namePosition       = $_REQUEST['Su_Name'];
        $monthPosition       = $_REQUEST['Su_Month'];
        $dateinc            = date('Y-m-d');
        $newhisSql          = "INSERT INTO salary_history (US_Id,Sal_Amt,Sal_Date,Sal_month) VALUES ";
        $userids            = [];
        $usrOldData         = [];
        $selUsrSql          = mysqli_query($GLOBALS['con'], "SELECT ua.US_Id, ua.US_EMPID, usl.US_GrossSal, usl.US_DASal, usl.US_CcaSal, usl.US_HRASal, usl.US_ConveySal, usl.US_EduSal, usl.US_MedSal, usl.US_MiscSal, usl.US_BasicSal, usl.SS_Id FROM users_auth As ua INNER JOIN users_salary AS usl ON (usl.US_Id=ua.US_Id) WHERE  ua.US_Status != 5 ORDER BY ua.US_Id");
        while($row = mysqli_fetch_object($selUsrSql)) {
            $usrOldData["'".base64_encode($row->US_EMPID)."'"] = $row;
        }
        $excelArray         = json_decode(json_encode($excelData['cells']), true);  // converting object array to normal PHP array
        $this->leaveErrorArray  = array();   // error array
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php');
        //$finalInsertArray       = array();
        $updDataAry            = array();
        for($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks            = array();    // remarks for errors
            $flag               = 0;
            if ($excelArray[$i][$userIDPosition] !="" && $excelArray[$i][$userIDPosition] !='null' 
                && $excelArray[$i][$SalAmtPosition] !="" && $excelArray[$i][$SalAmtPosition] != 'null') {
                $uniquusId      = base64_encode(trim($excelArray[$i][$userIDPosition]));
                if (isset($usrOldData["'".$uniquusId."'"])) {
                    $singleData = $usrOldData["'".$uniquusId."'"];
                    if (is_numeric($excelArray[$i][$SalAmtPosition]) ) {
                        if ($singleData->US_GrossSal != $excelArray[$i][$SalAmtPosition]) {
                            // need to update
                            $newhisSql  .= (!empty($userids)) ? ", ":"";
                            array_push($userids, $singleData->US_Id);

                            $newhisSql  .= " (".$singleData->US_Id.",".$excelArray[$i][$SalAmtPosition].",'".$dateinc."', '".date("Y-m-d",strtotime($excelArray[$i][$monthPosition]))."')";
                            $basicSalary = $singleData->US_BasicSal;
                            if ($singleData->SS_Id > 0 && $singleData->US_DASal == 0 && $singleData->US_CcaSal == 0 && $singleData->US_HRASal == 0 && $singleData->US_ConveySal == 0 && $singleData->US_EduSal == 0 && $singleData->US_MedSal == 0 && $singleData->US_MiscSal == 0) { // salary structue have not split up then update the basic salary 27-06-2025
                                $basicSalary = $excelArray[$i][$SalAmtPosition];
                            }

                            $updDataAry[] = ['newsal'=>$excelArray[$i][$SalAmtPosition], 'userid'=>$singleData->US_Id, 'basic'=>$basicSalary];                            
                        }
                    } else{
                        array_push($errorColumns,"Salary Amount");
                        array_push($remarks,"Amount should be numeric");
                        $flag = 1;
                    }
                } else {
                    array_push($errorColumns,"User ID");
                    array_push($remarks,"User ID doesn't exist");
                    $flag = 1;
                }  
            } else {
                array_push($errorColumns,"User ID/Salary");
                array_push($remarks,"All fields are mandatory");
                $flag = 1;      
            } 
            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);

            if($flag == 1) {
                $this->leaveErrorArray[]    = $excelArray[$i];
            }  
        }
        // insert and back the new salary details
        if (!empty($userids)) {

            $backSql     = "INSERT INTO users_salary_bkup (Upd_By, US_Id, SS_Id, US_GrossSal, US_DASal, US_CcaSal, US_HRASal, US_ConveySal, US_EduSal, US_MedSal, US_MiscSal, US_BasicSal, US_DedEPF, US_DedESI, US_DedSalTDS, US_DedProfTDS, US_DedLWF, US_DedMealCard, Sal_BnkId) SELECT ".$preTally_user_id.", u.US_Id, u.SS_Id, u.US_GrossSal, u.US_DASal, u.US_CcaSal, u.US_HRASal, u.US_ConveySal, u.US_EduSal, u.US_MedSal, u.US_MiscSal, u.US_BasicSal, u.US_DedEPF, u.US_DedESI, u.US_DedSalTDS, u.US_DedProfTDS, u.US_DedLWF, u.US_DedMealCard, u.Sal_BnkId FROM users_salary as u WHERE u.US_Id IN (".implode(',',$userids).")";
            mysqli_query($GLOBALS['con'],$backSql);
            mysqli_query($GLOBALS['con'],$newhisSql);
            foreach ($updDataAry AS $singemp) {
                $updSql     = "UPDATE users_salary SET US_GrossSal=".$singemp['newsal'].", US_BasicSal =".$singemp['basic']." WHERE US_Id = ".$singemp['userid'];
                mysqli_query($GLOBALS['con'],$updSql);
            }            
        }
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path); 
    }
    // end the salary update 24-03-2025
    /**
     *  save the custom account entry from the excel file
     * Created By Bilin @ 08-01-2026
    */ 
    function pandLUpdExcel($excelData,$preTally_user_id,$preTally_user_ofid)
    {
        // get swaped positions of each entries
        $DatePos    = $_REQUEST['Date'];
        $ProcessPos = $_REQUEST['Process'];
        $AmountPos  = $_REQUEST['Amount'];
        $BranchPos  = $_REQUEST['Branch'];
        $company_id = (isset($_REQUEST['company_id'])) ? $_REQUEST['company_id'] : $preTally_user_ofid;
        $type       = $_REQUEST['type'];
        $Month      = $_REQUEST['Month'];
        $Year       = $_REQUEST['Year'];
        $isNew      = $_REQUEST['isNew'];
        $addtype    = 0;
        $dateupd    = date('Y-m-d H:i:s');
        // backup old and delete from the main table
        if ($isNew != 1) {
            mysqli_query($GLOBALS['con'], 'INSERT INTO acc_back_extra_journal  SELECT NULL, aej.* FROM acc_extra_journal as aej WHERE aej.company_id ='.$company_id.' AND aej.type ="'.$type.'" AND aej.jobmonth ="'.$Month.'" AND aej.jobyear="'.$Year.'" AND aej.addtype=0');
            mysqli_query($GLOBALS['con'], 'DELETE FROM acc_extra_journal WHERE company_id ="'.$company_id.'" AND type ="'.$type.'" AND jobmonth ="'.$Month.'" AND jobyear="'.$Year.'" AND addtype=0'); 
        }
        //get all branch names and ids 
        $locAry     = [];
        $sqlLoc     = mysqli_query($GLOBALS['con'], 'SELECT LC_Id, LC_Name FROM locations WHERE LC_Status = 1 AND OF_Id ='.$company_id);
        while ($rl  = mysqli_fetch_object($sqlLoc)) {
            $locname= strtolower(preg_replace('/[^a-zA-Z]/', '', $rl->LC_Name));
            $locAry[$locname] = $rl->LC_Id;
        }
        // ledger array for save the ledger ids
        $ledgrAry   = [];
        $sqlledg    = mysqli_query($GLOBALS['con'], 'SELECT id, title FROM acc_ledger WHERE status = 1');
        while ($rl  = mysqli_fetch_object($sqlledg)) {
            $ledgnam= strtolower(preg_replace('/[^a-zA-Z]/', '', $rl->title));
            $ledgrAry[$ledgnam] = $rl->id;
        }
        // save into the actual table
        $newAccSql  = "INSERT INTO acc_extra_journal (type, title, ledger_id, job_date, amount, company_id, branch_id, branch_name, jobmonth, jobyear, addtype, created_at, created_by) VALUES ";

        // converting object array to normal PHP array
        $excelArray            = json_decode(json_encode($excelData['cells']), true);
        
        $this->leaveErrorArray  = array();   // error array
        include_once($BASEPATH.'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php');
        $updDataAry             = array();
        $uj                     = 0;
        $delrw                  = 0;
        for ($i=1; $i < $excelData['numRows']; $i++) {  // for each records in array
            
            $totalCells         = $excelData['numCols'];  // get total cells in array 
            $errorColumns       = array();    // error columns
            $remarks            = array();    // remarks for errors
            $flag               = 0;
            if ($excelArray[$i][$DatePos] !="" && $excelArray[$i][$DatePos] !='null' && 
                $excelArray[$i][$AmountPos] !="" && $excelArray[$i][$AmountPos] != 'null' && 
                $excelArray[$i][$BranchPos] !="" && $excelArray[$i][$BranchPos] != 'null' && is_numeric($excelArray[$i][$AmountPos])) {
                // create the location and ledger array key
                $locname    = strtolower(preg_replace('/[^a-zA-Z]/', '', $excelArray[$i][$BranchPos]));
                $ledgnam    = strtolower(preg_replace('/[^a-zA-Z]/', '', $excelArray[$i][$ProcessPos]));
                $branch_id  = (isset($locAry[$locname])) ? $locAry[$locname]:0;
                $ledger_id  = (isset($ledgrAry[$ledgnam])) ? $ledgrAry[$ledgnam]:0;
                // need to update
                $newAccSql  .= ($uj > 0) ? ", ":"";
                $newAccSql  .= " ('".$type."', '".$excelArray[$i][$ProcessPos]."', ".$ledger_id.", '".date("Y-m-d",strtotime($excelArray[$i][$DatePos]))."', '".$excelArray[$i][$AmountPos]."', ".$company_id.", ".$branch_id.", '".$excelArray[$i][$BranchPos]."', ".$Month.", ".$Year.", ".$addtype.", '".$dateupd."', ".$preTally_user_id.")";                
                $uj++;
            } else {
                array_push($errorColumns,"Date/ Amount/ Branch");
                array_push($remarks,"All fields are mandatory");
                $flag = 1;      
            } 

            $excelArray[$i][$totalCells]    = implode(',<br> ', array_unique($errorColumns));
            $excelArray[$i][$totalCells+1]  = implode(',<br> ', $remarks);

            if($flag == 1) {
                $this->leaveErrorArray[]    = $excelArray[$i];
            } 
        }
        mysqli_query($GLOBALS['con'],$newAccSql);
        // delete the excel file uploaded into server
        $path = $BASEPATH . "uploads/excelFile/".$excelData['excelFileName'];
        unlink($path);
    }
    // End account custom entry 08-01-2026
    
    function offzAdmin($OFId){          
        $sql = 'SELECT OF_Admin FROM offices WHERE OF_Id = '.$OFId;
        $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);
        return $result[0];
    }
        
    function getValues($field, $filter, $table)  {
        $query = mysqli_query($GLOBALS['con'],"SELECT $field FROM $table WHERE $filter");
        return mysqli_fetch_array($query,MYSQLI_ASSOC);
    }
    
    function getReportingTree($USId,$rptPntTree)  {          
        $sql = 'SELECT US_Report FROM users_auth WHERE US_Id = '.$USId;
        $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);

        array_push($rptPntTree, $result[0]);
        if( $result[0] != 1)
            return $this->getReportingTree($result[0],$rptPntTree);
        else 
            return mysqli_real_escape_string($GLOBALS['con'],serialize($rptPntTree));
    }
       
    function alphanumericValidation($string) {
        if(preg_match("/^[a-zA-Z0-9]+$/", $string))  // Only numbers and letters only
            return true;
        else 
            return false;
    }
    
    /* Function for removing unwanted characters or spaces */
    function cleanData($data)  {
       $data    =   trim(strip_tags(htmlspecialchars($data, ENT_QUOTES)));
       $data    =   mysqli_real_escape_string($GLOBALS['con'],$data);
       return $data;
    }
    function dataValdation($dataArray,$processPosArray,$errorColsArray,$remarksArray){
        $errorColumns  = array();    // error columns
        $remarks       = array();          // remarks for errors
        foreach ($processPosArray as $pos) {
            if(!preg_match("/^[a-zA-Z ]+$/", $this->cleanData($dataArray[$pos])) ){
                array_push($errorColumns,$errorColsArray[$pos]);
                array_push($remarks,$remarksArray[$pos]);
                $flag = 1; 
            }
        }
        return array($errorColumns,$remarks);
    }
}