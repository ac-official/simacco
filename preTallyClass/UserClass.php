<?php
require_once("connection.php");
class UserClass
{ 
    var $UserArray;
    var $UserDetailArray;
    var $UserTypeArray;
    var $UserAttendanceArray;
    var $UserHolidaysArray;
        var $UserDesigArray;
        var $UserLocArray;
        var $UserOffArray;
        var $UserDeptArray;
        var $CityArray;
        var $StateArray;
        var $BloodArray;
        var $SalPayArray;
        var $SalStructArray;
        var $AttendanceArray;
        var $UserLocationArray;
        var $UserLogArray;
        var $StatusArray;
//----------------------------------------- All Users Types ----------------------------------------//
    function viewUserTypes($filt='')
    {           
        $count=0;
        $this->UserTypeArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM acl ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserTypeArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- All Users Designations ----------------------------------------//
        function viewUserDesig($filt='')
    {           
        $count=0;
        $this->UserDesigArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM designations ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserDesigArray[$count]=$row;
            $count++;
        }   
    }
//---------------------------------------------All Salary Payment Modes---------------------------------------------------------//
        function viewSalPayModes($filter="")
        {
            $count=0;
        $this->SalPayArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM  salary_paymodes ".$filter);
        while($row=mysqli_fetch_object($result)) {
            $this->SalPayArray[$count]=$row;
            $count++;
                        }
        }
//-------------------------------------------All Listing reporting official-----------------------------------------------------//
    function viewReportingUsers($id,$myid)
    {
                $count=0;
        $this->DataArray = array();                
        if($myid == 0)
                    $result=mysqli_query($GLOBALS['con'],"SELECT US_Id,US_FName,US_LName FROM users_auth WHERE US_Status = 1");
                else
                    $result=mysqli_query($GLOBALS['con'],"SELECT US_Id,US_FName,US_LName FROM users_auth WHERE LC_Id=".$id." AND US_Status != 5 AND US_Id !=".$myid);
        while($row=mysqli_fetch_object($result)) {
            $this->DataArray[$count]=$row;
            $count++;
        }
    }
//----------------------------------------- All Offices ----------------------------------------//
        function viewUserOffices($filt='')
    {           
        $count=0;
        $this->UserOffArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM offices ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserOffArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- All Users Locations ----------------------------------------//
        function viewUserLocation($filt='')
    {           
        $count=0;
        $this->UserLocArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM locations ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserLocArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- All Users Departments ----------------------------------------//
        function viewUserDepts($filt='')
    {           
        $count=0;
        $this->UserDeptArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM departments ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserDeptArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- All  States ----------------------------------------//
function viewStates($filt='')
    {           
        $count=0;
        $this->StateArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM states ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->StateArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- All  States ----------------------------------------//
function viewCities($filt='')
    {           
        $count=0;
        $this->CityArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM cities ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->CityArray[$count]=$row;
            $count++;
        }   
    }
        //----------------------------------------- All  Blood Groups ----------------------------------------//
        function viewBloodGroups($filt='')
    {           
        $count=0;
        $this->BloodArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM blood_groups ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->BloodArray[$count]=$row;
            $count++;
        }   
    }
//----------------------------------------- Designation name by ID ----------------------------------------//
        function getDesignationName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT DG_Name FROM designations WHERE DG_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['DG_Name'];            
        }
        
//----------------------------------------- Employee Status by ID ----------------------------------------//
        function getEmployeeStatusName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT ES_Name FROM employee_status WHERE ES_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['ES_Name'];            
        }        
//----------------------------------------- Department name by ID ----------------------------------------//
        function getDepartmentName($id){
            "SELECT DP_Name FROM departments WHERE DP_Id=".$id;
            $result=mysqli_query($GLOBALS['con'],"SELECT DP_Name FROM departments WHERE DP_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['DP_Name'];            
        }
//----------------------------------------- Office name by ID ----------------------------------------//
        function getOfficeName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT OF_Name FROM offices WHERE OF_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['OF_Name'];            
        }
//----------------------------------------- Location name by ID ----------------------------------------//
        function getLocationName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT LC_Name FROM locations WHERE LC_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['LC_Name'];            
        }
//----------------------------------------- Usertye by ID ----------------------------------------//
        function getUserType($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT ACL_Name FROM acl WHERE ACL_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['ACL_Name'];            
        }
//----------------------------------------- Blood Group by ID ----------------------------------------//
        function getBloodGroup($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT BG_Name FROM blood_groups WHERE BG_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['BG_Name'];                       
        }

//----------------------------------------- City name by ID ----------------------------------------//
        function getCityName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT CT_Name FROM cities WHERE CT_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['CT_Name'];                       
        }
//----------------------------------------- State name by ID ----------------------------------------//
        function getStateName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT ST_Name FROM states WHERE ST_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['ST_Name'];                       
        }
//----------------------------------------- Country name by ID ----------------------------------------//
        function getCountryName($id){
            $result=mysqli_query($GLOBALS['con'],"SELECT CN_Name FROM countries WHERE CN_Id=".$id);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['CN_Name'];                       
        }
        //----------------------------------------- User name by ID ----------------------------------------//
        function getUserName($id){
            $result = mysqli_query($GLOBALS['con'],"SELECT US_FName, US_LName FROM users_auth WHERE US_Id=".$id);
            $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['US_FName'].' '.$row['US_LName'];                       
        }
       //----------------------------------------- User name by ID ----------------------------------------//
        function getUserOFId($id){
            $result = mysqli_query($GLOBALS['con'],"SELECT OF_Id FROM users_auth WHERE US_Id=".$id);
            $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['OF_Id'];                  
        } 
//----------------------------------------- Location name by ID ----------------------------------------//
        function getUserSalPayMode($pmid){
            $result=mysqli_query($GLOBALS['con'],"SELECT SP_Name FROM salary_paymodes WHERE SP_Id = ".$pmid);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['SP_Name'];            
        }
//----------------------------------------- Location name by ID ----------------------------------------//
        function getUserSalBank($bnkid){
            $result=mysqli_query($GLOBALS['con'],"SELECT Sal_BnkName FROM salary_bank_names WHERE Sal_BnkId = ".$bnkid);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            if($bnkid!=0)
            return $row['Sal_BnkName'];            
            else
            return "Not Specified";    
        }
//----------------------------------------- Location name by ID ----------------------------------------//
        function getUserLocation($USId){
            $result=mysqli_query($GLOBALS['con'],"SELECT LC.LC_Name FROM locations as LC, users_auth as US WHERE US.LC_Id=LC.LC_Id AND US.US_Id = ".$USId);
            $row=  mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['LC_Name'];            
        }
//----------------------------------------- All Users ----------------------------------------//
    function viewUser($filt='')
    {           
        $count=0;
        $this->UserArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM users_auth ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserArray[$count]=$row;
            $count++;
        }   
    }
//---------------------------------------------------Salary Structures-----------------------------------------------------//
function viewSalStruct($fields='*', $tbls='', $filt='')
    {           
        $count=0; 
        $this->SalStructArray = array();
                $result=mysqli_query($GLOBALS['con'],"SELECT ".$fields." FROM salary_structures ".$tbls."  ".$filt);
        //$result=mysqli_query($GLOBALS['con'],"SELECT * FROM salary_structures ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->SalStructArray[$count]=$row;
            $count++;
        }   
    }
        function countUserGrid($filterUSR,$usid,$filter) {          
        $count=0;
        $this->UserArray = array();                
        //if($OfId != 1 ){ $userfilter = "USAUTH.OF_Id =".$OfId."  AND"; } else $userfilter="";
        $sql="SELECT 
                                    COUNT(USAUTH.US_Id) AS CNT  
                                    FROM
                                    users_auth AS USAUTH,
                                    users_personal AS USPERS,
                                    locations AS LC,
                                    designations AS DG,
                                    departments as DP,
                                    offices as OFF,
                                    acl as AC,
                                    employee_status AS ES
                                    WHERE
                                    USAUTH.LC_Id = LC.LC_Id AND 
                                    USAUTH.DP_Id = DP.DP_Id AND 
                                    USAUTH.DG_Id = DG.DG_Id AND 
                                    USAUTH.OF_Id = OFF.OF_Id AND 
                                    USAUTH.US_Id = USPERS.US_Id AND 
                                    USAUTH.ES_Id = ES.ES_Id AND
                                    USAUTH.UT_Id = AC.ACL_Id AND USAUTH.US_Status != 5 ".$filterUSR." ".$filter;
        $result=mysqli_query($GLOBALS['con'],$sql);
        $row=mysqli_fetch_assoc($result);
        return $row["CNT"];
        //USAUTH.US_Id <> ".$id." AND
    }
    //---------------------------------All users for Grid-----------------------------------------------------------------------//
    function viewUserGrid($filterUSR,$usid,$filter,$pos,$cnt) {         
        $count=0;
        $this->UserArray = array();                        
        $result=mysqli_query($GLOBALS['con'],"SELECT 
                                    USAUTH.US_Id,
                                    USAUTH.US_EMPID,
                                    USAUTH.US_FName,
                                    USAUTH.US_LName,
                                    USAUTH.OF_Id,
                                    USAUTH.LC_Id,
                                    USAUTH.US_Email,
                                    USAUTH.US_Report,
                                    USAUTH.US_Status,
                                    USAUTH.US_AttndFlag,
                                    USAUTH.US_WrkHrFlag,
                                    USPERS.US_Mobile1,
                                    DG.DG_Name,
                                    DP.DP_Name,
                                    LC.LC_Name,
                                    DP.DP_Name,
                                    OFF.OF_Name,
                                    ES.ES_Name,
                                    ES.ES_Resign,
                                    USPERS.CN_Id,
                                    USPERS.ST_Id,
                                    USPERS.CT_Id,
                                    ACL_Name 
                                    FROM
                                    users_auth AS USAUTH,
                                    users_personal AS USPERS,
                                    locations AS LC,
                                    designations AS DG,
                                    departments as DP,
                                    offices as OFF,
                                    acl as AC,
                                    employee_status AS ES
                                    WHERE
                                    USAUTH.LC_Id = LC.LC_Id AND 
                                    USAUTH.DP_Id = DP.DP_Id AND 
                                    USAUTH.DG_Id = DG.DG_Id AND 
                                    USAUTH.OF_Id = OFF.OF_Id AND 
                                    USAUTH.US_Id = USPERS.US_Id AND 
                                    USAUTH.ES_Id = ES.ES_Id AND
                                    USAUTH.UT_Id = AC.ACL_Id AND USAUTH.US_Status != 5 ".$filterUSR." ".$filter." LIMIT ".$pos.",".$cnt);
        while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
        }        
    }
    
    function viewApprovedUserGrid($filterUSR,$usid) {           
        $count=0;
        $this->UserArray = array();                
        //if($OfId != 1 ){ $userfilter = "USAUTH.OF_Id =".$OfId."  AND"; } else $userfilter="";
        
        $result=mysqli_query($GLOBALS['con'],"SELECT 
            USAUTH.US_Id,
                                    USAUTH.US_EMPID,
                                    USAUTH.US_FName,
                                    USAUTH.US_LName,
                                    USAUTH.OF_Id,
                                    USAUTH.LC_Id,
                                    USAUTH.US_Email,
                                    USAUTH.US_Report,
                                    USAUTH.US_Status,
                                    USPERS.US_Mobile1,
                                    DG.DG_Name,
                                    LC.LC_Name,
                                    OFF.OF_Name,
                                    USPERS.CN_Id,
                                    USPERS.ST_Id,
                                    USPERS.CT_Id,
                                    ACL_Name 
                                    FROM
                                    users_auth AS USAUTH,
                                    users_personal AS USPERS,
                                    locations AS LC,
                                    designations AS DG,
                                    departments as DP,
                                    offices as OFF,
                                    acl as AC 
                                    WHERE
                                    USAUTH.LC_Id = LC.LC_Id AND 
                                    USAUTH.DP_Id = DP.DP_Id AND 
                                    USAUTH.DG_Id = DG.DG_Id AND 
                                    USAUTH.OF_Id = OFF.OF_Id AND 
                                    USAUTH.US_Id = USPERS.US_Id AND 
                                    USAUTH.UT_Id = AC.ACL_Id AND
                                    USAUTH.US_Status = 1".$filterUSR);
        while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
        }
        //USAUTH.US_Id <> ".$id." AND
    }
    
    
    
    
    
    
    //---------------------------------Single User---------------------------------------------------------------------//
        function viewSingleUser($id)
    {
            $this->UserArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM users_auth USAUTH
                                    LEFT JOIN users_personal USPER ON USAUTH.US_Id = USPER.US_Id
                                    LEFT JOIN users_qualification AS USQUAL ON USAUTH.US_Id = USQUAL.US_Id 
                                    LEFT JOIN users_accounts AS USACC ON USAUTH.US_Id = USACC.US_Id 
                                    LEFT JOIN users_salary AS USSAL ON USAUTH.US_Id = USSAL.US_Id 
                                    WHERE USAUTH.US_Id=".$id);
        $row=mysqli_fetch_object($result);
                return $row;        
        
    }
        //-------------------------------------View User Profile-------------------------------//
        function viewUserProfile($id)
        {
                $this->UserArray = array();             
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM users as US,designations as DG, acl as UT,departments as DP,locations as LC,countries AS CN,states as ST,cities as CT WHERE US.LC_Id = LC.LC_Id AND US.DP_Id = DP.DP_Id AND US.UT_Id=UT.UT_Id AND US.US_Country=CN.CN_Id AND US.US_State=ST.ST_Id AND US.US_City=CT.CT_id AND DG.DG_Id=US.DG_Id AND US.US_Id=".$id);
        $row=mysqli_fetch_object($result);
                return $row;
            
        }
    //----------------------------------------- All Users Attendance----------------------------------------//
    function viewUserAttendance($filt='')
    {           
        $count=0;
        $this->UserAttendanceArray = array();
        //die("SELECT * FROM attendance ".$filt);
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM attendance ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserAttendanceArray[$count]=$row;
            $count++;
        }   
    }
    
       function viewAttendanceGrid($filterHR,$ofid,$date) {         
        $count=0;
        $this->UserArray = array();                
        //if($OfId != 1 ){ $userfilter = "USAUTH.OF_Id =".$OfId."  AND"; } else $userfilter="";
 $sql="SELECT 
    USAUTH.US_Id,USAUTH.US_EMPID,USAUTH.US_FName,USAUTH.US_LName,USAUTH.OF_Id,USAUTH.LC_Id,DP.DP_Name,
    USAUTH.US_Email,USAUTH.US_Report,USAUTH.US_Status,USAUTH.US_LoginTime,USAUTH.US_LogoutTime,USAUTH.US_WrkHours,USAUTH.US_AttndFlag,USAUTH.US_AttndDate,USAUTH.US_WrkHrFlag,USAUTH.US_WrkHrDate,
    LC.LC_Name,ATT.AT_SignIn,ATT.AT_SignOut,ATT.AT_Hours,ATT.AT_Status,ATT.AT_IPAddr ,LRD.LRD_Id,LR.US_Id AS RptdBy,LR.LR_AppliedFor AS AppFor, ATT.AT_SignInDelay, ATT.AT_SignOutEarly, ATT.AT_AllotTime, ATT.AT_ExtraTime,ATT.uuid,(SELECT COUNT(*) 
     FROM attendance AS ATT2 
     WHERE ATT2.uuid = ATT.uuid  AND ATT2.AT_Date = '".$date."') AS device FROM 
    users_auth AS USAUTH    
    LEFT JOIN departments AS DP ON USAUTH.DP_Id = DP.DP_Id   
    LEFT JOIN locations AS LC ON USAUTH.LC_Id = LC.LC_Id
    LEFT JOIN attendance AS ATT ON ATT.US_Id=USAUTH.US_Id AND ATT.AT_Date='".$date."'
    LEFT JOIN leave_request AS LR ON LR.LR_AppliedFor=USAUTH.US_Id AND LR.LR_Status < 3 AND (LR.LR_FromDate<='".$date."' AND LR.LR_ToDate>='".$date."')        
    LEFT JOIN leave_reqdays AS LRD ON LRD.US_Id=USAUTH.US_Id AND LR.LR_Id=LRD.LR_Id AND LRD.LRD_Date='".$date."'   
    WHERE (USAUTH.US_ResignDate >='".$date."' OR USAUTH.US_ResignFlag= 0) AND (USAUTH.US_BlkdDate >='".$date."' OR USAUTH.US_Status= 1) AND USAUTH.US_DOJ <='".$date."' AND USAUTH.US_Status != 5 AND USAUTH.OF_Id=".$ofid." ".$filterHR." ORDER BY LC.LC_Name";
        $result=mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
        }  
        
    }
    //--------------------------------select sigin,signout details of office-------------
    function selectCompanySettings($filt){
        $count=0;
        $this->CompanySettingsArray=array();
        $query="SELECT * from company_settings where OF_Id=".$filt;
        $row=mysqli_query($GLOBALS['con'],$query);
        $this->CompanySettingsArray=mysqli_fetch_assoc($row);
    }
        
        
    //----------------------------------------- All Users Holidays----------------------------------------//
    function viewUserHolidays($filt='')
    {           
        $count=0;
        $this->UserHolidaysArray = array();
        //die("SELECT * FROM attendance ".$filt);
        $result=mysqli_query($GLOBALS['con'],"SELECT * FROM holidays ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserHolidaysArray[$count]=$row;
            $count++;
        }   
    }
    
    //----------------------------------------- New User ----------------------------------------//
    // Updated By Bilin @ 4-12-2024
    function newUser(){  

        $this->newUsrFlag = 'ok'; // 04-12-2024       
        $sql = 'SELECT US_EMPID FROM users_auth WHERE (US_EMPID = "'.$this->US_AuthData['US_EMPID'].'" OR US_UName = "'.$this->US_AuthData['US_EMPID'].'")';
        $result = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($result) > 0)
        {
            $this->newUsrFlag = 'id_fail';
        } else { // 04-12-2024       
            $sql1 = 'SELECT US_UName FROM users_auth WHERE (US_UName = "'.$this->US_AuthData['US_UName'].'" OR US_EMPID="'.$this->US_AuthData['US_UName'].'")';
            $result1 = mysqli_query($GLOBALS['con'],$sql1);
            if(mysqli_num_rows($result1) > 0)
            {
                $this->newUsrFlag = 'usr_fail';
            }
        }

        if($this->newUsrFlag == 'ok')
        {
            $sql_auth = "INSERT INTO users_auth ( " . implode(', ',array_keys($this->US_AuthData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_AuthData)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql_auth);
            $us_id  = mysqli_insert_id($GLOBALS['con']);
            $this->US_PersonalData['US_Id'] = $us_id;
            $this->US_QualData['US_Id']     = $us_id;
            $this->US_AccountData['US_Id']  = $us_id;
            $this->US_SalaryData['US_Id']   = $us_id;
            if($this->US_PersonalData['US_DOB']=="")
            {
                $this->US_PersonalData['US_DOB']    = '1995-01-01';
            }
            if($this->US_AccountData['SP_Id']=="")
            {
                $this->US_AccountData['SP_Id']      = 0;
            }
            if($this->US_PersonalData['US_Gender']=="")
            {
                $this->US_PersonalData['US_Gender'] = 0;
            }    
            $sql_personal = "INSERT INTO users_personal ( " . implode(', ',array_keys($this->US_PersonalData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_PersonalData)) . "'" . ")";

            $sql_qual = "INSERT INTO users_qualification ( " . implode(', ',array_keys($this->US_QualData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_QualData)) . "'" . ")";

            $sql_sal = "INSERT INTO users_salary( " . implode(', ',array_keys($this->US_SalaryData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_SalaryData)) . "'" . ")";

            $sql_acc = "INSERT INTO users_accounts( " . implode(', ',array_keys($this->US_AccountData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_AccountData)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql_personal);
            mysqli_query($GLOBALS['con'],$sql_qual);
            mysqli_query($GLOBALS['con'],$sql_sal);
            mysqli_query($GLOBALS['con'],$sql_acc);  
            // insert into User Based Acl 04-07-2025
            $sql_acl = "INSERT INTO user_acl (us_id,view_break_time, created_at) VALUES ('".$us_id."','1','".date('Y-m-d')."')";
            mysqli_query($GLOBALS['con'],$sql_acl);
            // grace time history of new user 24-07-2025
            if (isset($this->GraceTimeAry) && $this->GraceTimeAry['save'] == 1) {
                $this->GraceTimeAry['user_id'] = $us_id;
                $this->saveUserGraceTime();
            }

            return  $us_id ; 
        } else {
            return 0;//'User ID Already Registered. Please Re-Try.';
        }
    }   
    
    //----------------------------------------- Copy User Details ----------------------------------------//
        function copyUser($ITId,$user_id){
            
           $maxid_result=  mysqli_query($GLOBALS['con'],"SELECT MAX(BK_Id) FROM users_auth_bkup");
           $row=mysqli_fetch_array($maxid_result,MYSQLI_NUM);
           if($row[0]==NULL)
           {
               $maxid=1;
           }
            else 
           {
              $maxid=$row[0]+1;
             }
           $sql_auth    ="INSERT INTO users_auth_bkup SELECT ".$maxid.",".$user_id.",u.* FROM users_auth as u WHERE u.US_Id = ".$ITId;
           $sql_personal="INSERT INTO users_personal_bkup SELECT ".$maxid.",".$user_id.",u.* FROM users_personal as u WHERE u.US_Id = ".$ITId;
           $sql_qual    ="INSERT INTO users_qualification_bkup SELECT ".$maxid.",".$user_id.",u.* FROM users_qualification as u WHERE u.US_Id = ".$ITId;
           $sql_acc     ="INSERT INTO users_accounts_bkup SELECT ".$maxid.",".$user_id.",u.* FROM users_accounts as u WHERE u.US_Id = ".$ITId;
           $sql_sal     ="INSERT INTO users_salary_bkup (Upd_By, US_Id, SS_Id, US_GrossSal, US_DASal, US_CcaSal, US_HRASal, US_ConveySal, US_EduSal, US_MedSal, US_MiscSal, US_BasicSal, US_DedEPF, US_DedESI, US_DedSalTDS, US_DedProfTDS, US_DedLWF, US_DedMealCard, Sal_BnkId) SELECT ".$user_id.", u.Upd_By, u.US_Id, u.SS_Id, u.US_GrossSal, u.US_DASal, u.US_CcaSal, u.US_HRASal, u.US_ConveySal, u.US_EduSal, u.US_MedSal, u.US_MiscSal, u.US_BasicSal, u.US_DedEPF, u.US_DedESI, u.US_DedSalTDS, u.US_DedProfTDS, u.US_DedLWF, u.US_DedMealCard, u.Sal_BnkId FROM users_salary as u WHERE u.US_Id = ".$ITId;
            mysqli_query($GLOBALS['con'],$sql_auth);
            mysqli_query($GLOBALS['con'],$sql_personal);
            mysqli_query($GLOBALS['con'],$sql_qual);
            mysqli_query($GLOBALS['con'],$sql_acc);
            mysqli_query($GLOBALS['con'],$sql_sal);
        }        
    //----------------------------------------- Update User Details ----------------------------------------//
    function updateUser($ITId){
        
        // user name checking start 04-12-2024
        if (isset($this->US_AuthData) && isset($this->US_AuthData['US_UName'])) {
            
            $sql1 = 'SELECT US_UName FROM users_auth WHERE (US_UName = "'.$this->US_AuthData['US_UName'].'" OR US_EMPID="'.$this->US_AuthData['US_UName'].'") AND US_Id !='.$ITId;
            $result1 = mysqli_query($GLOBALS['con'],$sql1);
            if(mysqli_num_rows($result1) > 0)
            {
                return 'usr_fail'; 
            }
        }
        // user name checking end 

        if(count($this->US_AuthData) > 0) {   
                /*authorisation details*/
            foreach ($this->US_AuthData as $key=>$value){ 
                        $ITAuthData = $ITAuthData .$key ."='".$value."', ";
            }
            $ITAuthData = substr($ITAuthData, 0, -2);
            $sqlauth = "UPDATE users_auth SET $ITAuthData WHERE US_Id=".$ITId;               
                mysqli_query($GLOBALS['con'],$sqlauth);
                //return $sqlauth;
            if (isset($this->US_AuthData['US_UName']) && $this->US_AuthData['US_UName'] != "") {
                if (!$_SESSION) { session_start(); }
                $_SESSION['preTally_user_uname'] =  $this->US_AuthData['US_UName'];
            }
        }
        if(count($this->US_PersonalData) > 0) {  
            /*personal details*/
            foreach ($this->US_PersonalData as $key=>$value){ 
                $ITPerData = $ITPerData .$key ."='".$value."', ";
            }
            $ITPerData = substr($ITPerData, 0, -2);
            $sqlpersonal = "UPDATE users_personal SET $ITPerData WHERE US_Id=".$ITId;
            mysqli_query($GLOBALS['con'],$sqlpersonal);
                    //return $sqlpersonal;
        }
        if(count($this->US_QualData) > 0) {  
                /*qualification details*/                
            foreach ($this->US_QualData as $key=>$value){ 
                $ITQualData = $ITQualData .$key ."='".$value."', ";
            }
            $ITQualData = substr($ITQualData, 0, -2);
            $sqlqual = "UPDATE users_qualification SET $ITQualData WHERE US_Id=".$ITId;
            mysqli_query($GLOBALS['con'],$sqlqual);
        }
        if(count($this->US_AccountData) > 0) {  
            /*account details*/
            foreach ($this->US_AccountData as $key=>$value){ 
                $ITAccData = $ITAccData .$key ."='".$value."', ";
            }                
            $ITAccData = substr($ITAccData, 0, -2);                
            $sqlacc = "UPDATE users_accounts SET $ITAccData WHERE US_Id=".$ITId;
            mysqli_query($GLOBALS['con'],$sqlacc);
            //return $sqlacc;
        }
        if(count($this->US_SalaryData) > 0) {  
            /*salary details*/
            foreach ($this->US_SalaryData as $key=>$value){ 
                $ITSalData = $ITSalData .$key ."='".$value."', ";
            }
            $ITSalData = substr($ITSalData, 0, -2);
            $sqlsal = "UPDATE users_salary SET $ITSalData WHERE US_Id=".$ITId;
            mysqli_query($GLOBALS['con'],$sqlsal);
            //return $sqlsal;
        }  
        // check the user based acl was present if not present then add entry 04-07-2025
        $sqlacl = 'SELECT id FROM user_acl WHERE US_Id ='.$ITId;
        $resacl = mysqli_query($GLOBALS['con'],$sqlacl);
        if(mysqli_num_rows($resacl) <= 0)
        {
            $sql_acl = "INSERT INTO user_acl (us_id, view_break_time, created_at) VALUES ('".$ITId."','1','".date('Y-m-d')."')";
            mysqli_query($GLOBALS['con'],$sql_acl);
        }  
        // grace time history of existing user- update or save 24-07-2025
        if (isset($this->GraceTimeAry) && ($this->GraceTimeAry['edit'] == 1 || $this->GraceTimeAry['save'] == 1)) {
            $this->saveUserGraceTime();
        }            

        return 'User Updated Successfully';             
    }        
    //----------------------------------------- Update My Profile ----------------------------------------//
    function updateProfile(){
        $sql="  UPDATE users SET 
                US_Name     = '$this->US_Name',
                US_Email    = '$this->US_Email',
                US_Mobile   = '$this->US_Mobile',
                US_Phone    = '$this->US_Phone',
                US_Company  = '$this->US_Company', 
                US_Country  = '$this->US_Country' 
                WHERE US_Id = ".$this->US_Id;
        
        //return $sql;
        mysqli_query($GLOBALS['con'],$sql);
    }
    //----------------------------------------- Update My Password ----------------------------------------//
    function updatePassword(){
        $sql = 'SELECT US_Id FROM users_auth WHERE US_Id="'.$this->US_Id.'" AND US_Password="'.$this->US_OldPassword.'"';
        $login = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($login)>0) {
            $sql = 'UPDATE users_auth SET US_Password = "'.$this->US_NewPassword.'", PassChangeDate="'.date('Y-m-d H:i:s').'" WHERE US_Id = '.$this->US_Id;
            //return $sql;
            mysqli_query($GLOBALS['con'],$sql);
            return 'Password Updated Successfully';
        } else {
            return 'Current Password is Wrong. <br /> Please Try Again.';
        }
    }
        //----------------------------------------- Admin Reset Password ----------------------------------------//
    function admResetPassword(){        
            $sql = 'UPDATE users_auth SET US_Password = "'.$this->US_Password.'" WHERE US_Id    = '.$this->US_Id;           
            mysqli_query($GLOBALS['con'],$sql);
                        $this->UserDetailArray=array();
                        $sql_select="SELECT US_FName,US_LName,US_Email FROM users_auth WHERE US_Id=".$this->US_Id;
                        $result=  mysqli_query($GLOBALS['con'],$sql_select);
                        $this->UserDetailArray= mysqli_fetch_assoc($result);
            return 'Password Updated Successfully';     
    }
    // ------------ Authenticate User--------------
    function signInUser($flag) {
          // change OF to OF1 because mysql get errors @23-07-2024
          $sql = 'SELECT US.US_Id,US.US_EMPID,US.US_UName,US.US_Password,US.US_FName,US.US_LName,US.UT_Id,US.US_Status,US.OF_Id,US.LC_Id,US.DP_Id,US.US_RptFlag,OF1.CR_Id, CR.CR_ISOCode, OF1.OF_Admin, OF1.TZ_Id, TZ.TZ_Name, OF1.OF_Status, LC.LC_Status
                        FROM users_auth as US ,locations as LC, offices as OF1 
                            LEFT JOIN currencies as CR ON OF1.CR_Id = CR.CR_Id
                            LEFT JOIN time_zones as TZ ON OF1.TZ_Id = TZ.TZ_Id
                            WHERE (US.US_UName = "' . $this->US_Login . '") AND US.US_Password="'.$this->US_Password.'" AND US.OF_Id = OF1.OF_Id AND US.LC_Id = LC.LC_Id'; 
                            //US.US_EMPID="'.$this->US_Login.'" OR           
           
                $login = mysqli_query($GLOBALS['con'],$sql);
                
           if(mysqli_num_rows($login)>0) {   
               
                if($row = mysqli_fetch_array($login,MYSQLI_ASSOC)) {
                    if($row['US_Status'] == 1 && $row['OF_Status'] == 1 && $row['LC_Status'] == 1 ) { 
                        $token = bin2hex(random_bytes(32)); 
                        $this->updateUserToken($token, $row['US_Id']);
                        session_start();
                        $_SESSION['preTally_user_id']   =   $row['US_Id'];  // Creating Session with E mial.
                        $_SESSION['token'] = $token;
                        $_SESSION['preTally_user_empid']=   $row['US_EMPID']; 
                        $_SESSION['preTally_user_uname']=   $row['US_UName']; 
                        $_SESSION['preTally_user_name'] =   $row['US_FName'].' '.$row['US_LName'];// Creating Session with User Name
                        $_SESSION['preTally_user_type'] =   $row['UT_Id'];// Creating Session with User Type
                        $_SESSION['preTally_user_ofid'] =   $row['OF_Id'];// Creating Session with Company
                        $_SESSION['preTally_user_lcid'] =   $row['LC_Id'];// Creating Session with Branch
                        $_SESSION['preTally_user_dpid'] =   $row['DP_Id'];// Creating Session with Department
                        $_SESSION['preTally_user_rptflg']=  $row['US_RptFlag'];// Creating Session to determine whether user is a reporting a person 0/1
                        $_SESSION['time_zone']          =       $row['TZ_Name'];
                        $_SESSION['currency']           =       $row['CR_ISOCode'];
                        
                        $_SESSION['preTally_user_ofname']= $this->getOfficeName($row['OF_Id']); 
                        $_SESSION['preTally_user_lcname']= $this->getLocationName($row['LC_Id']);                       
                        if($row['US_Id'] == $row['OF_Admin']) $_SESSION['preTally_offzAdmin'] = 'true'; else $_SESSION['preTally_offzAdmin'] = 'false';
                        $result = mysqli_query($GLOBALS['con'],'SELECT * FROM acl WHERE ACL_Id = '.$row['UT_Id']);                        
                        $ACL_Obj = mysqli_fetch_object($result);                        
                        $_SESSION['preTally_user_acl']  =   $ACL_Obj;// Creating Session with ACL
                        // Creating Session with user based ACL 04-07-2025
                        $result1 = mysqli_query($GLOBALS['con'],'SELECT * FROM user_acl WHERE us_id = '.$row['US_Id']);                        
                        $UserACLObj = mysqli_fetch_object($result1);                        
                        $_SESSION['preTally_user_sacl']  =   $UserACLObj;
                        date_default_timezone_set($row['TZ_Name']);                              
                        $_SESSION['attendance_flag']    =       $this->checkAttendance($row['US_Id'],  date("Y-m-d"), "");                        
                        
                        $this->AN_IPData['US_Id']    = $_SESSION['preTally_user_id'];
                        $this->AN_IPData['AN_Time']  = date('Y-m-d H:i:s');
                        $this->AN_IPData['AN_CDate'] = date('Y-m-d');
                         $this->AN_IPData['uuid']     = isset($_COOKIE["uuid"]) ? $_COOKIE["uuid"] : '';
                        $this->IPTracking($this->AN_IPData);                        
                        //return 'success';
                        if($flag == 0) {
                            $this->trackUser();
                            header('location:index.php');
                            exit();
                        }
                        else if($flag == 1)
                            return "success";
                    } else if($row['US_Status'] == 0 || $row['OF_Status'] != 1 ) {
                        ob_clean();
                        //return 'System Under Maintenance </br>Will be live on Nov 1<sup>st</sup>';
                        return 'Your Account is Blocked by Administrator. <br/> Please Contact PreTally Team for Details.'; // Alert for Blocked Account

                    } else if($row['US_Status'] == 2) {
                        ob_clean();
                        return 'Your Account is Not Yet Activated. <br/> Please Contact PreTally Team for Details.'; // Alert for Blocked Account
                    } else if($row['LC_Status'] == 0) {
                        ob_clean();
                        return 'Your Branch is Blocked by Administrator. <br/> Please Contact PreTally Team for Details.';  // Alert for Blocked Account
                    }
                }
                else {
                     ob_clean();
                     return 'Invalid Username / Password'; // Alert for Invalid EmailId/Password
                }
           } else {
                ob_clean();
                return 'Invalid Username / Password'; // Alert for Invalid EmailId/Password
           }
    }
    /*
        * Set a login token to a user
        * Created By Master Of Programing
    */
    function updateUserToken($token = '', $user = '')
    {
        $sql = "update users_auth set US_Log_Token ='".$token."' where US_Id='".$user."'";
        return mysqli_query($GLOBALS['con'],$sql);

    }
    /*
        * Check whether a user havae a valid token
        * Created By Master Of Programing
     */
     function getToken($user)
     {
        $sql = 'SELECT US_Log_Token as token FROM users_auth WHERE US_Id="' . $user . '"';
        $result=mysqli_query($GLOBALS['con'],$sql);

        return mysqli_fetch_assoc($result);
     }
     /*
        * Force logout users
        * Created By Master Of Programing
     */
     function forceLogout($users=array())
     {
        $sql = "update users_auth set US_Log_Token ='' where US_Id IN (".implode($users,',').")";
        return mysqli_query($GLOBALS['con'],$sql);
     }
     /*
        * Force Remove Account Menu Permissions
        * Created By Master Of Programing
     */
     function forceRemoveMenuPermission()
     {
        $sql = "update user_acl set view_accounts_report ='0'";
        return mysqli_query($GLOBALS['con'],$sql);
     }
    /*
        * Track the user login
        * Created By Master Of Programing
    */
    function trackUser()
    {
        $so_catch=isset($_COOKIE["so-catch"]) ? $_COOKIE["so-catch"] : '';
        if(empty($so_catch)){
            setcookie('so-catch',uniqid(),0,'/',false,true);
        }
        $uuid   = uniqid();
        $_today = isset($_COOKIE["today"]) ? $_COOKIE["today"] : '';
        $today = date('Y-m-d');
        if( empty($_today) ){
            setcookie("today", $today, time()+24*60*60,"/","","",true);
            setcookie("uuid",  $uuid, time() + 24*60*60,"/","","",true);
        }
        else{
                if($_today != $today){
                    setcookie("today", $today, time()+24*60*60,"/","","",true);
                    setcookie("uuid",  $uuid, time() + 24*60*60,"/","","",true);
                }
        }
    }
    /*
        * Return the total count of employees who sign in after their scheduled time, day-wise for the current month.
        * Created By Master Of Programing
    */
    function getLateLoginCount()
    {
        $sql ="SELECT a.AT_CDate, COUNT(DISTINCT a.US_Id) AS LateLoginUserCount FROM attendance a JOIN users_auth u ON a.US_Id = u.US_Id WHERE a.AT_CDate >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND a.AT_CDate < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH) AND a.AT_SignIn > u.US_LoginTime GROUP BY a.AT_CDate ORDER BY a.AT_CDate";
        $result=mysqli_query($GLOBALS['con'],$sql);
        $LateLoginArray = [];
        $totalDays = date('d');
        for ($i = 0; $i < $totalDays; $i++) {
            $LateLoginArray[$i] = []; 
        }
        while($row=mysqli_fetch_object($result)) {
            $day = (int) date('j', strtotime($row->AT_CDate)); 
            $LateLoginArray[$day-1]=$row;
        }   
        return $LateLoginArray;
    }
    /*
        * Return the total count of employees who sign out before their scheduled time, day-wise for the current month.
        * Created By Master Of Programing
    */
    function getEarlySignOutCount()
    {
        $sql ="SELECT a.AT_CDate, COUNT(DISTINCT a.US_Id) AS EarlySignoutUserCount FROM attendance a JOIN users_auth u ON a.US_Id = u.US_Id WHERE a.AT_CDate >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND a.AT_CDate < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH) AND a.AT_SignOut < u.US_LogoutTime GROUP BY a.AT_CDate ORDER BY a.AT_CDate";
        $result=mysqli_query($GLOBALS['con'],$sql);
        $EarlySignOutArray=[]; 
        $totalDays = date('d');
        for ($i = 0; $i < $totalDays; $i++) {
            $EarlySignOutArray[$i] = []; 
        }
        $this->EarlySignOutArray = array();
        while($row=mysqli_fetch_object($result)) {
             $day = (int) date('j', strtotime($row->AT_CDate)); 
            $EarlySignOutArray[$day-1]=$row;
        }   
        return $EarlySignOutArray;
    }
    /*
        * Return the total count of employees who not completed their scheduled working hours, day-wise for the current month.
        * Created By Master Of Programing
    */
    function getHoursIncompleteCount()
    {
        $sql ="SELECT a.AT_CDate, COUNT(DISTINCT a.US_Id) AS IncompleteHoursUserCount FROM attendance a JOIN users_auth u ON a.US_Id = u.US_Id WHERE a.AT_CDate >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND a.AT_CDate < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH) AND TIMESTAMPDIFF( MINUTE, a.AT_SignIn, a.AT_SignOut ) < TIMESTAMPDIFF( MINUTE, u.US_LoginTime, u.US_LogoutTime ) and AT_Status='1' GROUP BY a.AT_CDate ORDER BY a.AT_CDate";
        $result=mysqli_query($GLOBALS['con'],$sql);
        $HoursIncompleteArray = []; 
        $totalDays = date('d');
        for ($i = 0; $i < $totalDays; $i++) {
            $HoursIncompleteArray[$i] = []; 
        }
        while($row=mysqli_fetch_object($result)) {
            $day = (int) date('j', strtotime($row->AT_CDate)); 
            $HoursIncompleteArray[$day-1] = $row;
        }   

        return $HoursIncompleteArray;
    }
    /*
        * Return the total count of employees who utilize morethan alloted break hours in day-wise for the current month.
        * Created By Master Of Programing
    */
    function getBreakTimeExceedCount()
    {
        $sql="SELECT bt.break_date, COUNT(*) AS ExceededBreakUserCount FROM (SELECT b.US_Id, b.break_date, SUM(b.time_taken) AS TotalBreakTaken FROM break_time_user b WHERE b.break_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND b.break_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH) and b.break_id!=5 GROUP BY b.US_Id, b.break_date HAVING SUM(b.time_taken) > (SELECT total_break FROM break_settings LIMIT 1) ) bt GROUP BY bt.break_date ORDER BY bt.break_date";
        $result=mysqli_query($GLOBALS['con'],$sql);
        $BreakTimeExceedArray = array();
        $totalDays = date('d');
        for ($i = 0; $i < $totalDays; $i++) {
            $BreakTimeExceedArray[$i] = []; 
        }
        while($row=mysqli_fetch_object($result)) {
            $day = (int) date('j', strtotime($row->break_date)); 
            $BreakTimeExceedArray[$day-1] = $row;
        }   
        
        return $BreakTimeExceedArray;

    }
         //------------------- Check Attendance --------------//
    function checkAttendance($usid, $date, $session) {             
        $sql    = 'SELECT COUNT(*) AS ATT_CNT FROM attendance WHERE US_Id=' . $usid .' AND AT_Date="'. $date .'"';
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row    = mysqli_fetch_array($result,MYSQLI_NUM);
        return $row[0];
    }
    
    function getAttendance($usid, $date) {
        $sql = 'SELECT AT_SignIn,AT_SignOut,AT_Hours,AT_Status FROM attendance WHERE US_Id="' . $usid . '" AND AT_Date="' . $date . '" ';
        $count=0;
        $this->AttendanceArray = array();
        //die("SELECT * FROM attendance ".$filt);
        $result=mysqli_query($GLOBALS['con'],$sql);
        $this->AttendanceArray=  mysqli_fetch_assoc($result);
        
    }
       //----------------------------------------- Verify Attendance ----------------------------------------//
        function VerifyAttendStatus($USId, $Date) {
            $sql = 'SELECT AT_Status FROM attendance WHERE US_Id='.$USId. ' AND AT_Date="' . $Date . '" ';
            $result = mysqli_query($GLOBALS['con'],$sql);
            $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
            return $row['AT_Status'];
        }
    
    function UserLogout()
    {
        $this->AN_IPData['US_Id']    = $_SESSION['preTally_user_id'];
        $this->AN_IPData['AN_Time']  = date('Y-m-d H:i:s');
        $this->AN_IPData['AN_CDate'] = date('Y-m-d');
         $this->AN_IPData['uuid']     = isset($_COOKIE["uuid"]) ? $_COOKIE["uuid"] : '';
        $this->IPTracking($this->AN_IPData);               

        unset($_SESSION['preTally_user_id']);
        unset($_SESSION['preTally_user_name']);
        unset($_SESSION['preTally_user_type']);
                unset($_SESSION['preTally_user_empid']);
                unset($_SESSION['preTally_user_uname']);
                unset($_SESSION['user_report_id']);
                session_destroy();
        setcookie("uacl", "", time()-3600);
        ob_clean();
        //header("location: index.php");
    }   
    //----------------------------------------- Reset Password ----------------------------------------//
    function resetPassword(){
                $sql = 'SELECT US_Email, US_FName,US_LName FROM users_auth WHERE US_Email="'.$this->US_PwdData['US_Email'].'" AND US_Status != 5';
        $login = mysqli_query($GLOBALS['con'],$sql);                
        if(mysqli_num_rows($login)==1) {
                        $sql_pwd = "INSERT INTO user_resetpwd ( " . implode(', ',array_keys($this->US_PwdData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_PwdData)) . "'" . ")";         
            //return $sql;
            $pwd_check=mysqli_query($GLOBALS['con'],$sql_pwd);
            //$row = mysqli_fetch_array($login);
            $this->UserDetails=  mysqli_fetch_assoc($login);
        } else {
            return 'fail';
        }
    }           
    //----------------------------------------- Update Password ----------------------------------------//
    function updateResetPassword(){
        $sql = 'SELECT US_Email FROM users_auth WHERE US_Email="'.$this->US_Email.'" AND US_Status != 5';
        $login = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($login)>0) {
            $sql = 'UPDATE users_auth SET US_Password = "'.$this->US_Password.'" WHERE US_Email = "'.$this->US_Email.'"';
            mysqli_query($GLOBALS['con'],$sql);
            return 'success';
        } else {
            return 'Error!!, Please Re-Type Your Password Again.';
        }
    }
    
    //----------------------------------------- Verify Reset URL ----------------------------------------//
    function verifyResetURL(){
        $sql = 'SELECT COUNT(US_Id) FROM users_auth WHERE US_Email="'.$this->US_Email.'" AND  US_Reset="'.$this->US_Reset.'" AND US_Status != 5';
        $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count > 0){
            return 'success';
        } else {
            return 'fail';
        }
    }
        
        //---------------------------------------------- View User Employee Id----------------------------------------//
        function viewEmpId($US_Id){
                $count=0;
        $this->UserArray = array(); 
        $result=mysqli_query($GLOBALS['con'],"SELECT US_Id,US_EMPID FROM users_auth WHERE  US_Id=".$US_Id);
        while($row=mysqli_fetch_object($result)) {
            $this->UserArray[$count]=$row;
            $count++;
        }
        }
        function viewOfficeStaffs($filt=''){    
        $count=0;
        $this->UserArray = array();
        $result=mysqli_query($GLOBALS['con'],"SELECT US_Id, US_FName, US_LName FROM users_auth ".$filt);
        while($row=mysqli_fetch_object($result)) {
            $this->UserArray[$count]=$row;
            $count++;
        }   
    }
        //----------------------------------------- All Users ----------------------------------------//
    function userHierarchy($field='',$filt=''){         
            $count=0;
            $this->UserArray = array();
            $result=mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM users_auth '.$filt);
            while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
            }   
    }
        //----------------------------------------- All Users ----------------------------------------//
    function userReportsMe($field='',$filt=''){ 
            $count=0;
            $this->UserArray = array();           
            $result=mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM users_auth '.$filt);
            while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $this->UserNameArray[$row->US_Id]=  ucfirst(strtolower(ltrim($row->US_FName)))." ".substr($row->US_LName,0, 1);
                $count++;
            }   
    }
                //----------------------------------------- All Users ----------------------------------------//
    function userNotificationHierarchy($filt=''){               
            $count=0;
            $this->UserArray = array();
           $result=mysqli_query($GLOBALS['con'],'SELECT US.US_Id, US.US_FName, US.US_LName, US.OF_Id, US.LC_Id, US.US_Report, LC.LC_Id, LC.LC_Name 
                                        FROM `users_auth` AS US 
                                        LEFT JOIN `locations` AS LC ON US.LC_Id = LC.LC_Id WHERE US.US_Status != 5 '.$filt);
            while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $this->UserLocationArray[$row->US_Id]['US_Name']  =  ucfirst(strtolower(ltrim($row->US_FName)))." ".substr($row->US_LName,0, 1);
                $this->UserLocationArray[$row->US_Id]['LC_Name']  =  ucfirst(strtolower(ltrim($row->LC_Name)));
                $count++;
            }   
    }
        function userIds($field='',$filt=''){           
            $count=0;
            $this->UserArray ='';
            $result=mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM users_auth WHERE US_Status != 5 '.$filt);
            while($row=mysqli_fetch_object($result)) {
                $this->UserArray.=$row->US_Id.',';
                $count++;
            }   
    }
        function changePassword($usid,$pass){
            if(strlen($pass)>=8){
              $sql = 'UPDATE users_auth SET US_Password = "'.md5($pass).'" WHERE US_Id= '.$usid;            
        mysqli_query($GLOBALS['con'],$sql);
            }
        }
        //------------------------------------------- Random Generate Password ---------------------------------//
        function randomPassword() {
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array(); 
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); 
        }
        //----------------------------------------- Verify Email ----------------------------------------//
    function verifyCompanyEmail($Email){
        $sql = 'SELECT COUNT(US_Id) FROM users_auth WHERE ( US_Email="'.$Email.'" || US_EMPID="'.$Email.'" ) AND US_Status != 5';
        $result = mysqli_query($GLOBALS['con'],$sql);                     
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
            return true;
        } else {
            return false;   
        }
        }
         //----------------------------------------- Verify Reporting User ----------------------------------------//
    function verifyReportingUser($US_Report){
            
        $result=mysqli_query($GLOBALS['con'],"SELECT COUNT(*) FROM users_auth USAUTH
                                    LEFT JOIN users_personal USPER ON USAUTH.US_Id = USPER.US_Id
                                    LEFT JOIN users_qualification AS USQUAL ON USAUTH.US_Id = USQUAL.US_Id 
                                    LEFT JOIN users_accounts AS USACC ON USAUTH.US_Id = USACC.US_Id 
                                    LEFT JOIN users_salary AS USSAL ON USAUTH.US_Id = USSAL.US_Id 
                                    WHERE USAUTH.US_Id = '$US_Report'");                          
                $row = mysqli_fetch_array($result,MYSQLI_NUM);
                $count=$row[0];
                if($count == 0){
            return false;
        } else {
            return true;    
        }
        }
        //----------------- My Reporting Person ---------------
        function myReportingPerson($USID){
            $sql = 'SELECT US_Report FROM users_auth WHERE US_Id = '.$USID;            
            $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);      
            return $result[0];
        }
        //----------------- User's Reporting Person ---------------
        function getReportingPerson($USID){
            $sql = 'SELECT US1.US_Report, US2.US_FName, US2.US_LName
                        FROM users_auth US1
                            INNER JOIN users_auth US2 ON US1.US_Report = US2.US_Id 
                                WHERE US1.US_Id = '.$USID;
            $result = mysqli_fetch_object(mysqli_query($GLOBALS['con'],$sql));
//            $row = mysqli_fetch_object($result);
            if($result->US_Report != 1){
                $this->UserArray['US_Report'] = $result->US_Report;
                $this->UserArray['US_Name']   = $result->US_FName." ".$result->US_LName;
            }
        }       
        function changeEmpStatus($usid,$statid,$resignFlg,$resignDate){
            if($resignFlg==1){
                $resign_sql=",US_ResignFlag=1,US_ResignDate= '".$resignDate."'";
            }else if($resignFlg==2){
                $resign_sql=",US_ResignFlag=0,US_ResignDate=NULL";    
            }
            $sql_copy="INSERT INTO employee_status_history (ES_Id,US_Id,ESH_Date) SELECT ES_Id,".$usid.",'".$resignDate."' FROM users_auth WHERE US_Id=".$usid;
            mysqli_query($GLOBALS['con'],$sql_copy);
           $sql="UPDATE users_auth SET ES_Id=".$statid." ".$resign_sql." WHERE US_Id=".$usid;
            mysqli_query($GLOBALS['con'],$sql);            
        }

        function getEmpStatusGrid($usid,$doj,$esid){
            $sqluser="SELECT US_DOJ,ES_Id FROM users_auth WHERE US_Id=".$usid;
            $result_user=mysqli_query($GLOBALS['con'],$sqluser);   
            $row_user= mysqli_fetch_object($result_user);
            $emp_status=$this->getEmployeeStatusName($row_user->ES_Id);
            $emp_doj=$row_user->US_DOJ;
            $sqlselect="SELECT ESH.ESH_Id,ESH.ESH_Date,ES_Name FROM employee_status_history ESH LEFT JOIN employee_status AS ES ON ES.ES_Id=ESH.ES_Id WHERE ESH.US_Id=".$usid." ORDER BY ESH.ESH_Id DESC";
            $result=mysqli_query($GLOBALS['con'],$sqlselect);   
           $this->StatusArray[0]['ESH_Id']='Current';            
            $this->StatusArray[0]['ES_Name']=$emp_status;
            $count=1;
            while($row=mysqli_fetch_assoc($result)){                
                $this->StatusArray[$count]=$row;
                $this->StatusArray[$count-1]['ESH_Date']=$row['ESH_Date'];
                $count++;
            }
            $this->StatusArray[$count-1]['ESH_Date']=$row_user->US_DOJ;
            
        }
        //-----------------------------Geting employee status that resigns----------------------//
        function getResignStats($ofid){
            $sql=" SELECT ES_Id FROM employee_status WHERE ES_Resign=1 AND OF_Id=".$ofid;
            $result=mysqli_query($GLOBALS['con'],$sql);            
            $row=mysqli_fetch_assoc($result);
            return $row["ES_Id"];
        }

        // Return the list of status which holds ES_Resign = 1
        function getResignStatsList($ofid) {
            $sql = "SELECT ES_Id FROM employee_status WHERE ES_Resign = 1 AND OF_Id = " . intval($ofid);
            $result = mysqli_query($GLOBALS['con'], $sql);

            $ids = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $ids[] = $row['ES_Id'];
            }

            return implode(',', $ids);
        }
     //---------------------------------------Creating Employee Status Record------------------------------------------//
        function createEmpStatusRecord($usid,$esid)
        {
            $sql="INSERT INTO employee_status_history (ES_Id,US_Id,ESH_Date) VALUES (".$esid.",".$usid.",".date("Y-m-d H:i:s").")";
            mysqli_query($GLOBALS['con'],$sql);
        }   
        function getEmpStatusDate($usid,$esid,$frmt){
            $sql="SELECT ESH_Id,ESH_Date,UA.US_DOJ FROM users_auth UA  LEFT JOIN  employee_status_history AS ESH ON UA.US_Id=ESH.US_Id WHERE UA.US_Id=".$usid.' ORDER BY ESH_Id DESC LIMIT 0,1';
            $result=mysqli_query($GLOBALS['con'],$sql);
            $row=mysqli_fetch_assoc($result);
            $result = $row["ESH_Date"] ? $row["ESH_Date"] : $row["US_DOJ"];
            if($frmt!='calendar')
            return date("d/m/Y",strtotime($result));     
            else
            return $result;         
        }
        function updateEmpStatusDate($usid,$esid,$esdate){
           $sql="SELECT ESH.ESH_Id FROM users_auth UA  LEFT JOIN  employee_status_history AS ESH ON UA.US_Id=ESH.US_Id WHERE UA.US_Id=".$usid." ORDER BY ESH.ESH_Id DESC  LIMIT 0,1";
            $result=mysqli_query($GLOBALS['con'],$sql);
            $row=mysqli_fetch_assoc($result); 
           if($row['ESH_Id']){
            $sqlUpdate="UPDATE employee_status_history SET ESH_Date='".$esdate."' WHERE ESH_Id=".$row['ESH_Id'];
            mysqli_query($GLOBALS['con'],$sqlUpdate);
           }
        }
        
        // ------------ Authenticate User--------------
    function switchAccount($flag=0) {
            $sql = 'SELECT US.US_Id,US.US_UName,US.US_EMPID,US.US_Password,US.US_FName,US.US_LName,US.UT_Id,US.US_Status,US.OF_Id,US.LC_Id,US.DP_Id,US.US_RptFlag, OF1.CR_Id, CR.CR_ISOCode, OF1.OF_Admin, OF1.TZ_Id, TZ.TZ_Name, OF1.OF_Status
                        FROM users_auth as US , offices as OF1 
                            LEFT JOIN currencies as CR ON OF1.CR_Id = CR.CR_Id
                            LEFT JOIN time_zones as TZ ON OF1.TZ_Id = TZ.TZ_Id
                            WHERE (US.US_EMPID="'.$this->US_Login.'" OR US.US_UName="'.$this->US_Login.'") AND US.OF_Id = OF1.OF_Id';
//           die($sql);
           $login = mysqli_query($GLOBALS['con'],$sql);
           if(mysqli_num_rows($login)>0) { 
                if($row = mysqli_fetch_array($login,MYSQLI_ASSOC)) {
                    if($row['US_Status'] == 1 && $row['OF_Status'] == 1  ) { 
                            
                        session_start();
                        $_SESSION['preTally_user_id']   =   $row['US_Id'];  // Creating Session with E mial.
                        $_SESSION['preTally_user_empid']=   $row['US_EMPID']; 
                        $_SESSION['preTally_user_uname']=   $row['US_UName']; 
                        $_SESSION['preTally_user_name'] =   $row['US_FName'].' '.$row['US_LName'];// Creating Session with User Name
                        $_SESSION['preTally_user_type'] =   $row['UT_Id'];// Creating Session with User Type
                        $_SESSION['preTally_user_ofid'] =   $row['OF_Id'];// Creating Session with Company
                        $_SESSION['preTally_user_lcid'] =   $row['LC_Id'];// Creating Session with Branch
                        $_SESSION['preTally_user_dpid'] =   $row['DP_Id'];// Creating Session with Department
                        $_SESSION['time_zone']          =       $row['TZ_Name'];
                        $_SESSION['currency']           =       $row['CR_ISOCode'];
                        $_SESSION['preTally_user_ofname']= $this->getOfficeName($row['OF_Id']); 
                        $_SESSION['preTally_user_lcname']= $this->getLocationName($row['LC_Id']);
                        $_SESSION['preTally_user_rptflg']= $row['US_RptFlag']; // updated arun (21/10/22)
                        if($row['US_Id'] == $row['OF_Admin']) $_SESSION['preTally_offzAdmin'] = 'true'; else $_SESSION['preTally_offzAdmin'] = 'false';
                        $result = mysqli_query($GLOBALS['con'],'SELECT * FROM acl WHERE ACL_Id = '.$row['UT_Id']);
                        $ACL_Obj = mysqli_fetch_object($result);
                        $_SESSION['preTally_user_acl']  =   $ACL_Obj;// Creating Session with ACL
                        $_SESSION['attendance_flag']    =       $this->checkAttendance($row['US_Id'],  date("Y-m-d"), "",$row['TZ_Name']);
                        // find the user based or logined user based acls 04-07-2025
                        $result1 = mysqli_query($GLOBALS['con'],'SELECT * FROM user_acl WHERE us_id = '.$row['US_Id']);                        
                        $UserACLObj = mysqli_fetch_object($result1);                        
                        $_SESSION['preTally_user_sacl']  =   $UserACLObj;                        
                        //return 'success';
                        if($flag == 0)
                            header('location:index.php');
                        else if($flag == 1)
                            return "success";
                    } else if($row['US_Status'] == 0 || $row['OF_Status'] != 1 ) {
                        ob_clean();
                        //return 'System Under Maintenance </br>Will be live on Nov 1<sup>st</sup>';
                        return 'Your Account is Blocked by Administrator. <br/> Please Contact PreTally Team for Details.'; // Alert for Blocked Account

                    } else if($row['US_Status'] == 2) {
                        ob_clean();
                        return 'Your Account is Not Yet Activated. <br/> Please Contact PreTally Team for Details.'; // Alert for Blocked Account
                    }
                }
                else {
                     ob_clean();
                     return 'Invalid Username / Password'; // Alert for Invalid EmailId/Password
                }
           } else {
                ob_clean();
                return 'Invalid Username / Password'; // Alert for Invalid EmailId/Password
           }
    }
        //---------------------------------------Block/Unblock User--------------------------//
        function blockUser($usid,$flag,$blktime){
            if($flag!=0){            
            $blktime = "1970-01-01"; 
            }
           $sqlblk="UPDATE users_auth SET US_Status=".$flag.",US_BlkdDate='".$blktime."' WHERE US_Id=".$usid; 
            mysqli_query($GLOBALS['con'],$sqlblk);
        }
        //------------------------------- User login System Details ------------------------------//
        function IPTracking($IPDataArray=array())
        {
            if(!$IPDataArray) $IPDataArray = $this->AN_IPData ; 
            $sql="INSERT INTO analytics ( " . implode(', ',array_keys($IPDataArray)) . ") VALUES (" . "'" . implode("','", array_values($IPDataArray)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
        }  
        function changeAttStatus($usid,$status,$type){
            
            if($status==1 && $type=='skipatt'){
                $resign_sql=" US_AttndFlag=1,US_AttndDate= '".date("Y-m-d")."',US_WrkHrFlag=0,US_WrkHrDate=NULL";
            }else if($status==0 && $type=='skipatt'){
                $resign_sql=" US_AttndFlag=0,US_AttndDate=NULL";    
            }else if($status==1 && $type=='wrkhr'){
                $resign_sql=" US_WrkHrFlag=1,US_WrkHrDate= '".date("Y-m-d")."', US_AttndFlag=0,US_AttndDate=NULL";
            }else if($status==0 && $type=='wrkhr'){
                $resign_sql=" US_WrkHrFlag=0,US_WrkHrDate=NULL";    
            }
            
            $sql="UPDATE users_auth SET ".$resign_sql." WHERE US_Id=".$usid;
            mysqli_query($GLOBALS['con'],$sql);
            return "Attendance Status Updated";
        }
        function selectPunchingTimes($usid){
            $sql="SELECT US_LoginTime,US_LogoutTime,US_WrkHours FROM users_auth WHERE US_Id=".$usid;
            $this->UserLogArray= mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));            
        }
         function takeSalaryHistory($usid,$amt,$mode){             
            $result=mysqli_query($GLOBALS['con'],"SELECT US_GrossSal FROM users_salary WHERE US_Id=".$usid); 
            $row=mysqli_fetch_assoc($result);
            if($row["US_GrossSal"]!=$amt || $mode=='new'){
            $insertSQL = "INSERT INTO salary_history (US_Id,Sal_Amt,Sal_Date,Sal_month) VALUES (".$usid.",".$amt.",'".date('Y-m-d')."','".date('Y-m-')."01')";
            mysqli_query($GLOBALS['con'],$insertSQL);
            }
        }
        function mealAllowanceGrid($ofid,$year,$month){
            $count=0;
            $this->UserArray = array();          
            $sql="SELECT SM_Id,UAUTH.US_Id,CONCAT(UAUTH.US_FName,' ',UAUTH.US_LName) AS UNAME,UAUTH.US_EMPID,LC.LC_Name,SM.SM_Month,SM.SM_Year,SM.SM_Amount,SM_Status "
               . " FROM salary_meal_allowance as SM LEFT JOIN users_auth AS UAUTH ON SM.US_Id=UAUTH.US_Id "
                . " LEFT JOIN locations AS LC ON UAUTH.LC_Id=LC.LC_Id WHERE UAUTH.OF_Id=".$ofid." AND SM.SM_Month=".$month." AND SM.SM_Year=".$year;
            $result=mysqli_query($GLOBALS['con'],$sql);
            while($row=mysqli_fetch_object($result)) {
                $this->UserArray[$count]=$row;
                $count++;
            }   
        }
        function getMealCardItem($preTally_user_ofid){
         $query="SELECT CS_MealCardItemId FROM company_settings WHERE OF_Id=".$preTally_user_ofid;
        $result=mysqli_query($GLOBALS['con'],$query);
        $row=mysqli_fetch_assoc($result);
        return $row['CS_MealCardItemId'];
    }
    function updateMealCardStats($ids){
        $updquery= "UPDATE salary_meal_allowance SET SM_Status=1 WHERE SM_Id IN (".$ids.") ";
        mysqli_query($GLOBALS['con'],$updquery);
    }
    function getDescriptions($month,$mealItem){
        $this->BSSalAdvArray=array();
       $query="SELECT DS.DS_Id  FROM descriptions AS DS  WHERE DS_Description='".$month."' AND DS.DS_Id IS NOT NULL AND DS.IT_Id=".$mealItem;
        $result=mysqli_query($GLOBALS['con'],$query);
        $row=mysqli_fetch_row($result);
        return $row[0];
        
    }
    function createMealCardBSEntries(){        
        if($this->BSSalRptArray['CHQ_Number']!= ''){
            $sql = "UPDATE bank_cheque_leafs SET CL_Status = 2 WHERE CL_Id=".$this->BSSalRptArray['CHQ_Number'];
            mysqli_query($GLOBALS['con'],$sql);
            } 
          $entryarray=$this->BSSalRptArray;
          $keyString="";
          $valueString="";          
        $sqlinsert="INSERT INTO balance_sheets (".implode(array_keys($entryarray),",").") VALUES ".rtrim("(".implode(array_values($entryarray),",")."),",",");
          mysqli_query($GLOBALS['con'],$sqlinsert);
          return "Balance Sheet Entries Created";
    } 
    function userDataIM ($filter,$myID,$OFID) {        
        $count = 0;
        $this->UserArray = array(); 
        $filterSQL = 1;
        if($filter != 1) {
            $filterSQL = "US_FName LIKE '".$filter."%' OR US_LName LIKE '".$filter."%";
        }
        $sql = "SELECT US_Id, US_FName, US_LName, US_Image FROM users_auth WHERE ".$filterSQL." AND OF_Id = ".$OFID." AND US_ResignFlag = 0 AND US_Status = 1 AND US_Id != ".$myID." ORDER BY US_FName, US_LName";
        //die($sql);
        $result = mysqli_query($GLOBALS['con'],$sql);
        while($row = mysqli_fetch_object($result)) {
            $this->UserArray[$count]=$row;
            $count++;
        }
    }
    function userDataIMInfo($filter) {
        
        $sql = "SELECT US.US_Id, LC.LC_Name, CT.CT_Name, ST.ST_Name, DG.DG_Name FROM `users_auth` AS US 
                LEFT JOIN `locations` AS LC ON US.LC_Id = LC.LC_Id
                LEFT JOIN `designations` AS DG ON US.DG_Id = DG.DG_Id
                LEFT JOIN `cities` AS CT ON LC.CT_Id = CT.CT_Id
                LEFT JOIN `states` AS ST ON LC.ST_Id = ST.ST_Id
                WHERE US.US_Id = ".$filter;
        $result = mysqli_query($GLOBALS['con'],$sql);
        $row = mysqli_fetch_assoc($result);
        return '<b>'.$row[DG_Name].', '.$row[LC_Name].' Office</b>, '.$row[CT_Name].', '.$row[ST_Name];
    }
    function updateReportingFlg($usid,$prev_id){
        $sqlupd="UPDATE users_auth SET US_RptFlag =1 WHERE US_Id =".$usid;
        mysqli_query($GLOBALS['con'],$sqlupd);        
        if($prev_id!=NULL && $prev_id!="" && $prev_id!=$usid){
          $sqlcheck="SELECT count(US_Id) as COUNT FROM users_auth WHERE US_Report=".$prev_id;
            $result= mysqli_query($GLOBALS['con'],$sqlcheck);
            $row=mysqli_fetch_assoc($result);                   
            if($row[COUNT]==0){
               $sql="UPDATE users_auth SET US_RptFlag =0 WHERE US_Id =".$prev_id;
                mysqli_query($GLOBALS['con'],$sql);        
            }
        }
        
    }
    /*
        * Created By ArunDev
    */
    function selectUtilizedGraceTime($usid, $allotedHours, $graceTime){
        $leastMins = $allotedHours - $graceTime;
        $sql="SELECT sum($allotedHours- IF(AT_Hours>$allotedHours, $allotedHours,AT_Hours )) as grace_used from attendance where  (month(`AT_CDate`) = month(curdate())) and (year(`AT_CDate`) = year(curdate())) and US_Id='$usid' and AT_Hours>0 and AT_Hours>$leastMins";
          
          return mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));            
    }
    /*
        * Created By ArunDev
    */
    function selectGraceTimeLog($usid){
        $sql="SELECT count(*) as count from attendance_grace_over where month(`limit_overcome_date`) = month(curdate()) and user_id='$usid'";

        return mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));   
    }
    /*
        * Created By ArunDev
    */
    function insertGraceTimeLog($usid){
        $sql="INSERT INTO `attendance_grace_over` (`user_id`, `limit_overcome_date`) VALUES ('$usid', now());";

        mysqli_query($GLOBALS['con'],$sql);
    }
    /*
        * Created By ArunDev
    */
    function selectLastGraceTimeUsedDate($usid){
        $sql="SELECT limit_overcome_date as date from attendance_grace_over where month(`limit_overcome_date`) = month(curdate()) and user_id='$usid'";

        return mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));
    }
    /*
        * Created By ArunDev Updated By Bilin @ 23-08-2024
    */
    function getOfficeIdByUserId($usid){
        $sql="SELECT US.OF_Id,US.DP_Id, LC.ST_Id,US.US_LoginTime,US.US_LogoutTime,US.US_WrkHours, US.US_ResignFlag, US.US_ResignDate FROM users_auth AS US LEFT JOIN locations AS LC ON ( LC.LC_Id = US.LC_Id ) where US.US_Id='$usid' limit 1";

        return mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));
    }
    /**
     * New function for reporting offers loop parents checking
     * Created By Bilin @ 17-10-2024
    */
    function checkReportUserTree($reportid, $userid, $parents=array())
    {    
        if ($reportid <= 0) {
           return 1; 
        }
        if ($reportid == $userid) {
            return 0;
        }    
        $sql    = 'SELECT US_Report FROM users_auth WHERE US_Id = '.$reportid;
        $result = mysqli_fetch_array(mysqli_query($GLOBALS['con'],$sql),MYSQLI_NUM);
        if ($result[0] == 1) {
            return 1;
        }
        if (in_array($result[0], $parents) || $result[0] == $userid) {
            return 0;
        } else {
            array_push($parents, $result[0]);
            return $this->checkReportUserTree($result[0],$userid,$parents);
        } 
    }
    /**
    * Update the Username 
    * 05-12-2024
    */
    function updateUsername() 
    {
        $sql    = 'SELECT US_EMPID FROM users_auth WHERE (US_EMPID = "'.$this->US_UName.'" OR US_UName = "'.$this->US_UName.'") AND US_Id != "'.$this->US_Id.'" AND US_Status != 5';
        $result = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($result) > 0)
        {
            return 'User Name already Exists.Please type another One';
        } else {
            $sqlu   = 'UPDATE users_auth SET US_UName = "'.$this->US_UName.'", US_MDate="'.date('Y-m-d H:i:s').'" WHERE US_Id = '.$this->US_Id;
            mysqli_query($GLOBALS['con'],$sqlu);
            if (!$_SESSION) { session_start(); }
            $_SESSION['preTally_user_uname'] =   $this->US_UName;
            return 'success';
        }   
    }
    /**
     * Work allowed offices 
     * map the office id and user id
     * created by Bilin @ 28-03-2025
    */
    function mapOfficeUsers($userid=0, $officeary=[], $type, $upduser=0)
    {
        if (!empty($officeary) && $userid > 0) {
            $exofid     = [];
            if ($type == 2) {
                $sql    = "SELECT GROUP_CONCAT(DISTINCT OF_Id) AS offids FROM user_office WHERE US_Id =".$userid;
                $result = mysqli_query($GLOBALS['con'],$sql);
                $row    = mysqli_fetch_array($result,MYSQLI_NUM);
                $exofid = explode(',',$row[0]);
                $sql1   = 'UPDATE user_office SET status = 0  WHERE US_Id = '.$userid;
                mysqli_query($GLOBALS['con'],$sql1);
            }
            foreach ($officeary AS $offid) {
                if (in_array($offid, $exofid)) {
                    $sqlu   = 'UPDATE user_office SET status = "1", updated_by="'.$upduser.'", updated_at="'.date("Y-m-d H:i:s").'" WHERE US_Id = '.$userid.' AND OF_Id='.$offid;
                } else {
                    $sqlu   = "INSERT INTO `user_office` (`US_Id`, `OF_Id`, `created_at`, `status`, `updated_by`) VALUES ('".$userid."','".$offid."','".date('Y-m-d H:i:s')."', '1','".$upduser."')";
                }
                mysqli_query($GLOBALS['con'],$sqlu);
            }
        }
        return 1;
    }
    /**
     * Get the company and existing user based login and log out timing 
     * Created By Bilin @ 24-07-2025
    */
    function getUserTimes($user_id=0, $office_id=0)
    {
        if ($user_id > 0) {
            $sql    = 'SELECT ua.US_Id, ua.US_LoginTime, ua.US_LogoutTime, ua.US_WrkHours, ua.US_Time_Reduced, ua.US_Grace_Temp, cy.CS_OfficeStart, cy.CS_OfficeEnds, cy.CS_UserMaxGraceTime '
            .' FROM users_auth AS ua '
            .' INNER JOIN company_settings AS cy ON (cy.OF_Id = ua.OF_Id)'
            .' WHERE ua.US_Id = "'.$user_id.'"' 
            .' ORDER BY ua.US_Id' ;
        } else if ($office_id > 0) {
            $sql = 'SELECT cy.OF_Id, cy.CS_OfficeStart AS US_LoginTime, cy.CS_OfficeEnds AS US_LogoutTime,  "0" AS US_WrkHours, "0" AS US_Time_Reduced, "1" AS US_Grace_Temp, cy.CS_OfficeStart, cy.CS_OfficeEnds, cy.CS_UserMaxGraceTime '
            .' FROM company_settings AS cy'
            .' WHERE cy.OF_Id = "'.$office_id.'"' 
            .' ORDER BY cy.OF_Id';
        }
        $res        = mysqli_query($GLOBALS['con'], $sql);                        
        $row        = mysqli_fetch_object($res);             
        if (!empty($row)) {

            $row->US_WrkHours   = ($user_id > 0) ? $row->US_WrkHours : round(abs(strtotime($row->US_LogoutTime) - strtotime($row->US_LoginTime)) / 60,2);
            $row->CP_WHours     = round(abs(strtotime($row->CS_OfficeEnds) - strtotime($row->CS_OfficeStart)) / 60,2);
        }

        return $row;    
    }
    /**
     * working hours with grass time users history save and update
     * Created By Bilin @ 24-07-2025
    */
    function saveUserGraceTime()
    {
        $today  = date('Y-m-d');
        $ctime  = date('Y-m-d H:i:s');
        // close the existing grace time entry 
        if ($this->GraceTimeAry['edit'] == 1) {
            $sqlupd = 'UPDATE user_grace_time SET end_date = "'.$today.'", updated_by="'.$this->GraceTimeAry['login_user'].'", is_active=0 WHERE is_active =1 AND us_id = "'.$this->GraceTimeAry['user_id'].'"';
            mysqli_query($GLOBALS['con'], $sqlupd);
        }
        // save the new grace time entry of the user
        if ($this->GraceTimeAry['save'] == 1) {
            $end_date = ($this->GraceTimeAry['end_date'] != "") ? "'".date('Y-m-d', strtotime($this->GraceTimeAry['end_date']))."'":NULL;
            $sql    = "INSERT INTO user_grace_time (us_id, work_time, is_temporary, start_date, end_date, reason, is_active, created_by, created_at, old_time) VALUES ('".$this->GraceTimeAry['user_id']."', '".$this->GraceTimeAry['worktime']."', '".$this->GraceTimeAry['is_tempoary']."', '".$today."', ".$end_date.", '".$this->GraceTimeAry['reason']."', '1', '".$this->GraceTimeAry['login_user']."', '".$ctime."', '".$this->GraceTimeAry['old_time']."') ";
            mysqli_query($GLOBALS['con'], $sql);
        }
    }
    /**
    * Get the active temparory grace time allowed list ended today
    * created by Bilin @ 30-07-2025
    */
    function getActiveGraceUsers($inputs=[])
    {
        extract($inputs);
        $cudate     = (isset($cudate) && $cudate != "") ? $cudate : date('Y-m-d');
        $retlist    = [];
        $sql        = 'SELECT * FROM user_grace_time WHERE is_active = 1 AND is_temporary = 1 AND ugt.end_date IS NOT NULL AND end_date <= "'.$cudate.'" ORDER BY id ASC';
        $res    = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($res)) {
            $retlist[]=$row;
        }

        return $retlist;
    }
    /**
     * get selected users active grace time details for showing in the profile page
     * created By Bilin @ 27-11-2025
    */
    function getGraceTimeUser($user_id=0) 
    {
        $sql    = 'SELECT * FROM user_grace_time WHERE is_active = 1 AND us_id="'.$user_id.'" ORDER BY id DESC LIMIT 0, 1';
        $res    = mysqli_query($GLOBALS['con'],$sql);
        if ($row=mysqli_fetch_object($res)) {
            return (isset($row->id)) ? $row:[];
        } else {
            return [];
        }
    }
    /**
    * Update the user login and logout time updated to old
    * Created BY Bilin @ 30-07-2025
    */
    function clearActiveGraceTime()
    {
        $updUsers   = [];
        $cudate     = date('Y-m-d');
        $sql        = 'SELECT ugt.*, cs.CS_OfficeStart, cs.CS_OfficeEnds, ua.US_LoginTime, ua.US_LogoutTime, ua.US_WrkHours '
            .' FROM user_grace_time AS ugt '
            .' LEFT JOIN users_auth AS ua ON (ua.US_Id=ugt.us_id)'
            .' LEFT JOIN company_settings AS cs ON (cs.OF_Id = ua.OF_Id)'
            .' WHERE ugt.is_active = 1 AND ugt.is_temporary = "1" AND ugt.end_date IS NOT NULL AND ugt.end_date <= "'.$cudate.'"' // AND ua.US_Status = 1
            .' ORDER BY ugt.id ASC';
        $res    = mysqli_query($GLOBALS['con'],$sql);
        while($row=mysqli_fetch_object($res)) {

            $CP_WHours     = round(abs(strtotime($row->CS_OfficeEnds) - strtotime($row->CS_OfficeStart)) / 60,2);
            $old_times     = ($row->old_time != "") ? json_decode($row->old_time, true):['in'=>$row->CS_OfficeStart, 'out'=>$row->CS_OfficeEnds, 'whour'=>$CP_WHours];
            $old_times['oin']       = $row->US_LoginTime;
            $old_times['oout']      = $row->US_LogoutTime;
            $old_times['owhour']    = $row->US_WrkHours;

            $sqlupd = "UPDATE user_grace_time SET end_date = '".$cudate."', is_active=0, old_time='".json_encode($old_times)."' WHERE id = '".$row->id."'";
            mysqli_query($GLOBALS['con'], $sqlupd);

            $US_Time_Reduced = ($old_times['whour'] >= $CP_WHours) ? 0 : 1;
            $sqlupdu = "UPDATE users_auth SET US_LoginTime = '".$old_times['in']."', US_LogoutTime = '".$old_times['out']."', US_WrkHours = '".$old_times['whour']."', US_Time_Reduced = '".$US_Time_Reduced."' WHERE US_Id = '".$row->us_id."'";
            mysqli_query($GLOBALS['con'], $sqlupdu);

            if ($old_times['whour'] < $CP_WHours) { // save new grace time settings for 1 months

                $end_date       = date('Y-m-d', strtotime('+30 days', strtotime($cudate)));
                $old_timenw     = json_encode(array ("in"=>$row->CS_OfficeStart, "out"=>$row->CS_OfficeEnds, 'whour'=>$CP_WHours));

                $sqlinc    = "INSERT INTO user_grace_time (us_id, work_time, is_temporary, start_date, end_date, is_active, created_at, old_time) VALUES ('".$row->us_id."', '".$old_times['whour']."', '1', '".$cudate."', '".$end_date."', '1', '".date('Y-m-d H:i:s')."', '".$old_timenw."') ";
                mysqli_query($GLOBALS['con'], $sqlinc);
            }
            $updUsers[] = ['user_id'=>$row->us_id, 'times'=>json_encode($old_times)];
        }
        return $updUsers;
    }
    /**
    * List all degignation allowed to the selected user id
    * createdby Bilin @ 06-10-2025
    */
    function listDesigUser($userid, $ds_id=0, $date='') {
        
        $cuds_id    = 0;
        $desgAry    = [];
        $i          = 0;
        $sql        = 'SELECT bua.BK_Id,bua.DG_Id, d.DG_Name, bua.US_MDate, bua.US_CDate FROM users_auth_bkup AS bua INNER JOIN designations as d ON(d.DG_Id=bua.DG_Id) WHERE bua.US_Id='.$userid.' ORDER BY bua.BK_Id ASC';
        $res        = mysqli_query($GLOBALS['con'],$sql);
        while($row  = mysqli_fetch_object($res)) {
            if ($cuds_id != $row->DG_Id) {

                $desgAry[$i]= ['title'=>$row->DG_Name, 'from_date'=> ($cuds_id == 0) ? date('d-m-Y', strtotime($row->US_CDate)) : date('d-m-Y', strtotime($row->US_MDate))];
                $cuds_id    = $row->DG_Id;
                $i++;
            }
        }
        if ($cuds_id != 0 && $cuds_id != $ds_id) {
            $sql        = 'SELECT DG_Id, DG_Name FROM  designations WHERE DG_Id='.$ds_id.' ORDER BY DG_Id ASC';
            $res        = mysqli_query($GLOBALS['con'],$sql);
            $row        = mysqli_fetch_object($res);
            $desgAry[$i]= ['title'=>$row->DG_Name, 'from_date'=> date('d-m-Y', strtotime($date))];
        }

        return $desgAry;
    }
    /**
     * 
     * 
     * 
     * 
     * */

    function getRunningSalaryByMonth($monthYear, $userId) {
        list($year, $month) = explode('-', $monthYear);

        // Create a safe date to compare
        $targetDate = "$year-$month-01";

        // 1. Try to get salary for the given month
        $sql = 'SELECT Sal_Amt FROM salary_history
                WHERE US_Id = "' . $userId . '"
                AND MONTH(Sal_month) = "' . $month . '"
                AND YEAR(Sal_month) = "' . $year . '"
                ORDER BY Sal_HistId DESC
                LIMIT 1';

        $result = mysqli_query($GLOBALS['con'], $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }

        // 2. Fallback: Get latest salary before the target month (exclude current & future)
        $fallbackSql = 'SELECT Sal_Amt FROM salary_history
                        WHERE US_Id = "' . $userId . '"
                        AND Sal_month < "' . $targetDate . '"
                        ORDER BY Sal_month DESC, Sal_HistId DESC
                        LIMIT 1';

        $fallbackResult = mysqli_query($GLOBALS['con'], $fallbackSql);
        if ($fallbackResult && mysqli_num_rows($fallbackResult) > 0) {
            return mysqli_fetch_assoc($fallbackResult);
        }

        // 3. No data at all
        return false;
    }
    /**
     * Tempary time adjusted users list with reason (if reason is present)
     * some details not added in the grace time table
     * Created By Bilin @ 08-12-2025
    */
    function listTimeAdjustedUser($params=[])
    {
        extract($params);
        $total      = 0;
        $slno       = 0;
        $userids    = [];
        $datalist   = [];
        $usernames  = [];
        $worktime   = (isset($worktime) && $worktime) ? $worktime : 540;
        $where      = ' WHERE ua.US_Grace_Temp = 1 AND (ua.US_WrkHours < '.$worktime.' OR ugt.work_time < '.$worktime.')AND ua.US_Status = "1" ';
        $sql        = 'SELECT ua.US_Id, ua.US_FName, ua.US_LName, lc.LC_Name, ua.US_WrkHours, ugt.start_date, ugt.end_date, ugt.reason, ugt.created_by, ugt.work_time, ugt.is_active '
        .' FROM users_auth AS ua '
        .' INNER JOIN locations AS lc ON (lc.LC_Id = ua.LC_Id)'
        .' LEFT JOIN user_grace_time AS ugt ON(ugt.us_id = ua.US_Id)';
        if (isset($search) && $search != "") {
            $where  .= ' AND (ua.US_FName LIKE "%'.$search.'%" OR ua.US_LName LIKE "%'.$search.'%" OR lc.LC_Name LIKE "%'.$search.'%")';
        }
        if (isset($status) && $status != "") {
            if ( $status == 1) {
                $where  .= ' AND (ugt.is_active = "'.$status.'"  OR ugt.us_id is NULL )';
            } else {
                $where  .= ' AND ugt.is_active = "'.$status.'"';
            }            
        }
        $sql        .= $where.' ORDER BY ';
        $sortby     = (isset($sortby)) ? trim(strtolower($sortby)) :"id";
        $orderby    = (isset($orderby) && trim($orderby) != "DESC") ? "ASC" :"DESC";
        switch ($sortby) {
            case "enddate"  : $sql  .= 'ugt.end_date'; 
            break;
            case "startdate": $sql  .= 'ugt.start_date'; 
            break;
            case "staff"    : $sql  .= 'ua.US_FName'; 
            break;
            case "time"     : $sql  .= ( $status == 1) ? 'ua.US_WrkHours':'ugt.work_time';  
            break;
            default         : $sql  .= 'ugt.end_date'; 
            break;
        }
        $sql    .= ' '.$orderby.', ua.US_Id ASC';
        if (isset($limit) && $limit > 0) {
            $sql    .= ' LIMIT '.$start.','.$limit;
            $slno    = $start;
        }
        $res        = mysqli_query($GLOBALS['con'], $sql);
        while ($row = mysqli_fetch_object($res)) {
            $slno++;
            $row->slno      = $slno;
            $row->end_date  = ($row->end_date != "") ? date("d/m/Y", strtotime($row->end_date)):"";
            $row->start_date= ($row->start_date != "") ? date("d/m/Y", strtotime($row->start_date)):"";
            $row->US_WrkHours= ($row->work_time != "") ? $row->work_time:$row->US_WrkHours;
            $datalist[]     = $row;
            if ($row->created_by != '')
                $userids[]      = $row->created_by;
        }
        if ($start > 10 &&  count($datalist) >= $limit) {
            $sql_count  = 'SELECT COUNT(DISTINCT ua.US_Id) AS ucount FROM users_auth AS ua '
            .' INNER JOIN locations AS lc ON (lc.LC_Id = ua.LC_Id)'
            .' LEFT JOIN user_grace_time AS ugt ON(ugt.us_id = ua.US_Id) '.$where;
            $res_count  = mysqli_query($GLOBALS['con'], $sql_count);
            $row_count  = mysqli_fetch_array($res_count,MYSQLI_NUM);
            $total      = $row_count[0];
        } else {
            $total      = count($datalist);
        }
        // get the approved person names based on the user id fetched.
        if (!empty($userids)) {
            $userids    = array_unique($userids);
            $sql1        = 'SELECT ua.US_Id, ua.US_FName, ua.US_LName FROM users_auth AS ua WHERE ua.US_Id IN ('.implode(",",$userids).') order By ua.US_Id ';
            $res        = mysqli_query($GLOBALS['con'], $sql1);
            while ($row = mysqli_fetch_object($res)) {
                $usernames[$row->US_Id] = $row->US_FName.' '. $row->US_LName;
            }
        }

        return ['total'=>$total, 'list'=>$datalist, 'sql'=>$sql, 'approved'=>$usernames];
        // 117 - 2546 - 10:00:00    18:00:00    480 - 2018-02-06 ,
        //158 - 2546 - 09:30:00 17:30:00  480 - 2018-02-20,
        //367 - 2546 -  09:00:00    17:00:00  480 - 2018-02-20,
        //368  - 2546 - 09:30:00   18:00:00   510 - 2017-06-05 - ,
        // -------- 582  - 2546 - 09:30:00    17:30:00  480 - 2018-02-15,
        //3204  - 3442 - 09:30:00   17:30:00    480-  2022-10-04,
        //3293  - 3442 - 09:00:00   17:00:00    480 - 2023-01-03,
        //3296 - 3442 - 08:45:00    17:15:00    510 - 2023-04-04,
        //3371  - 3636 - 09:00:00   16:00:00    420 - 2024-08-21,
        //3445  - 3442- 10:00:00 18:00:00  480 - 2023-04-04,
        //3614  - 3250 - 09:00:00   16:00:00    420 - 2024-08-28,
        //3689  - 3636 - 09:00:00   17:30:00    510 - 2024-04-24 ,
        //3714  - 3636 - 09:00:00   17:30:00    510 - 2024-07-23,        
        //3716  - 3636 - 09:30:00   17:30:00    480- 2024-12-05 ,        
        //3726 -  3636 -09:00:00 17:30:00    510 - 2024-08-16,

        //3753 - 3736 - 09:30:00    17:45:00    495- 2025-01-31
        //SELECT ua.US_Id, ua.US_FName, ua.US_WrkHours, ua.US_MDate FROM users_auth as ua WHERE ua.US_Id not in (select us_id FROM user_grace_time) AND ua.US_Status = 1 AND ua.US_WrkHours < 540 AND ua.US_Grace_Temp = 1
    }
}
?>
