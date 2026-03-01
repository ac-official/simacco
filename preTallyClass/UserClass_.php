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
                    $result=mysqli_query($GLOBALS['con'],"SELECT US_Id,US_FName,US_LName FROM users_auth WHERE LC_Id=".$id." AND US_Id !=".$myid);
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
                                    USAUTH.UT_Id = AC.ACL_Id ".$filterUSR." ".$filter;
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
                                    ES.ES_Name,
                                    LC.LC_Name,
                                    DP.DP_Name,
                                    OFF.OF_Name,
                                    ES.ES_Name,
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
                                    USAUTH.UT_Id = AC.ACL_Id ".$filterUSR." ".$filter." LIMIT ".$pos.",".$cnt);
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
    LC.LC_Name,ATT.AT_SignIn,ATT.AT_SignOut,ATT.AT_Hours,ATT.AT_Status,ATT.AT_IPAddr ,LRD.LRD_Id,LR.US_Id AS RptdBy,LR.LR_AppliedFor AS AppFor FROM 
    users_auth AS USAUTH    
    LEFT JOIN departments AS DP ON USAUTH.DP_Id = DP.DP_Id   
    LEFT JOIN locations AS LC ON USAUTH.LC_Id = LC.LC_Id
    LEFT JOIN attendance AS ATT ON ATT.US_Id=USAUTH.US_Id AND ATT.AT_Date='".$date."'
    LEFT JOIN leave_request AS LR ON LR.LR_AppliedFor=USAUTH.US_Id AND LR.LR_Status < 3 AND (LR.LR_FromDate<='".$date."' AND LR.LR_ToDate>='".$date."')        
    LEFT JOIN leave_reqdays AS LRD ON LRD.US_Id=USAUTH.US_Id AND LR.LR_Id=LRD.LR_Id AND LRD.LRD_Date='".$date."'   
    WHERE (USAUTH.US_ResignDate >='".$date."' OR USAUTH.US_ResignFlag= 0) AND USAUTH.US_DOJ <='".$date."' AND USAUTH.OF_Id=".$ofid." ".$filterHR." ORDER BY LC.LC_Name";
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
	function newUser(){           
		$sql = 'SELECT US_EMPID FROM users_auth WHERE US_EMPID = "'.$this->US_AuthData['US_EMPID'].'"';
		$result = mysqli_query($GLOBALS['con'],$sql);
		if(mysqli_num_rows($result) == 0)
		{
			$sql_auth = "INSERT INTO users_auth ( " . implode(', ',array_keys($this->US_AuthData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_AuthData)) . "'" . ")";
			mysqli_query($GLOBALS['con'],$sql_auth);
                        $us_id=mysqli_insert_id($GLOBALS['con']);
                        $this->US_PersonalData['US_Id']=$us_id;
                        $this->US_QualData['US_Id']=$us_id;
                        $this->US_AccountData['US_Id']=$us_id;
                        $this->US_SalaryData['US_Id']=$us_id;
                        if($this->US_PersonalData['US_DOB']=="")
                        {
                            $this->US_PersonalData['US_DOB']='1995-01-01';
                        }
                        if($this->US_AccountData['SP_Id']=="")
                        {
                            $this->US_AccountData['SP_Id']=0;
                        }
                        if($this->US_PersonalData['US_Gender']=="")
                        {
                            $this->US_PersonalData['US_Gender']=0;
                        }    
                        $sql_personal = "INSERT INTO users_personal ( " . implode(', ',array_keys($this->US_PersonalData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_PersonalData)) . "'" . ")";
                        $sql_qual = "INSERT INTO users_qualification ( " . implode(', ',array_keys($this->US_QualData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_QualData)) . "'" . ")";
			$sql_sal = "INSERT INTO users_salary( " . implode(', ',array_keys($this->US_SalaryData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_SalaryData)) . "'" . ")";
                        $sql_acc = "INSERT INTO users_accounts( " . implode(', ',array_keys($this->US_AccountData)) . ") VALUES (" . "'" . implode("','", array_values($this->US_AccountData)) . "'" . ")";
                        mysqli_query($GLOBALS['con'],$sql_personal);
                        mysqli_query($GLOBALS['con'],$sql_qual);
                        mysqli_query($GLOBALS['con'],$sql_sal);
                        mysqli_query($GLOBALS['con'],$sql_acc);                        
                        return  $us_id ; 
		} else {
			return 'User ID Already Registered. Please Re-Try.';
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
           $sql_sal     ="INSERT INTO users_salary_bkup SELECT ".$maxid.",".$user_id.",u.* FROM users_salary as u WHERE u.US_Id = ".$ITId;
            mysqli_query($GLOBALS['con'],$sql_auth);
            mysqli_query($GLOBALS['con'],$sql_personal);
            mysqli_query($GLOBALS['con'],$sql_qual);
            mysqli_query($GLOBALS['con'],$sql_acc);
            mysqli_query($GLOBALS['con'],$sql_sal);
        }        
	//----------------------------------------- Update User Details ----------------------------------------//
	function updateUser($ITId){
           
            if(count($this->US_AuthData) > 0) {   
                /*authorisation details*/
		foreach ($this->US_AuthData as $key=>$value){ 
                    $ITAuthData = $ITAuthData .$key ."='".$value."', ";
		}
		$ITAuthData = substr($ITAuthData, 0, -2);
		$sqlauth = "UPDATE users_auth SET $ITAuthData WHERE US_Id=".$ITId;               
                mysqli_query($GLOBALS['con'],$sqlauth);
                //return $sqlauth;
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
            return 'User Updated Successfully';				
        }        
	//----------------------------------------- Update My Profile ----------------------------------------//
	function updateProfile(){
		$sql="  UPDATE users SET 
				US_Name		= '$this->US_Name',
				US_Email	= '$this->US_Email',
				US_Mobile	= '$this->US_Mobile',
				US_Phone	= '$this->US_Phone',
				US_Company	= '$this->US_Company', 
				US_Country	= '$this->US_Country' 
				WHERE US_Id	= ".$this->US_Id;
		
		//return $sql;
		mysqli_query($GLOBALS['con'],$sql);
	}
	//----------------------------------------- Update My Password ----------------------------------------//
	function updatePassword(){
		$sql = 'SELECT US_Id FROM users_auth WHERE US_Id="'.$this->US_Id.'" AND US_Password="'.$this->US_OldPassword.'"';
		$login = mysqli_query($GLOBALS['con'],$sql);
		if(mysqli_num_rows($login)>0) {
			$sql = 'UPDATE users_auth SET US_Password = "'.$this->US_NewPassword.'" WHERE US_Id	= '.$this->US_Id;
			//return $sql;
			mysqli_query($GLOBALS['con'],$sql);
			return 'Password Updated Successfully';
		} else {
			return 'Current Password is Wrong. <br /> Please Try Again.';
		}
	}
        //----------------------------------------- Admin Reset Password ----------------------------------------//
	function admResetPassword(){		
			$sql = 'UPDATE users_auth SET US_Password = "'.$this->US_Password.'" WHERE US_Id	= '.$this->US_Id;			
			mysqli_query($GLOBALS['con'],$sql);
                        $this->UserDetailArray=array();
                        $sql_select="SELECT US_FName,US_LName,US_Email FROM users_auth WHERE US_Id=".$this->US_Id;
                        $result=  mysqli_query($GLOBALS['con'],$sql_select);
                        $this->UserDetailArray= mysqli_fetch_assoc($result);
			return 'Password Updated Successfully';		
	}
	// ------------ Authenticate User--------------
	function signInUser($flag) {
          $sql = 'SELECT US.US_Id,US.US_EMPID,US.US_Password,US.US_FName,US.US_LName,US.UT_Id,US.US_Status,US.OF_Id,US.LC_Id,US.DP_Id,US.US_RptFlag,OF.CR_Id, CR.CR_ISOCode, OF.OF_Admin, OF.TZ_Id, TZ.TZ_Name, OF.OF_Status, LC.LC_Status
                        FROM users_auth as US ,locations as LC, offices as OF 
                            LEFT JOIN currencies as CR ON OF.CR_Id = CR.CR_Id
                            LEFT JOIN time_zones as TZ ON OF.TZ_Id = TZ.TZ_Id
                            WHERE US.US_EMPID="'.$this->US_Login.'" AND US.US_Password="'.$this->US_Password.'" AND US.OF_Id = OF.OF_Id AND US.LC_Id = LC.LC_Id';           
           
                $login = mysqli_query($GLOBALS['con'],$sql);
                
           if(mysqli_num_rows($login)>0) {   
               
                if($row = mysqli_fetch_array($login,MYSQLI_ASSOC)) {
                    if($row['US_Status'] == 1 && $row['OF_Status'] == 1 && $row['LC_Status'] == 1 ) { 
                            
                        session_start();
                        $_SESSION['preTally_user_id']	=	$row['US_Id'];  // Creating Session with E mial.
                        $_SESSION['preTally_user_empid']=       $row['US_EMPID']; 
                        $_SESSION['preTally_user_name']	=	$row['US_FName'].' '.$row['US_LName'];// Creating Session with User Name
                        $_SESSION['preTally_user_type']	=	$row['UT_Id'];// Creating Session with User Type
                        $_SESSION['preTally_user_ofid']	=	$row['OF_Id'];// Creating Session with Company
                        $_SESSION['preTally_user_lcid']	=	$row['LC_Id'];// Creating Session with Branch
                        $_SESSION['preTally_user_dpid']	=	$row['DP_Id'];// Creating Session with Department
                        $_SESSION['preTally_user_rptflg']=	$row['US_RptFlag'];// Creating Session to determine whether user is a reporting a person 0/1
                        $_SESSION['time_zone']          =       $row['TZ_Name'];
                        $_SESSION['currency']           =       $row['CR_ISOCode'];
                        
                        $_SESSION['preTally_user_ofname']= $this->getOfficeName($row['OF_Id']); 
                        $_SESSION['preTally_user_lcname']= $this->getLocationName($row['LC_Id']);                       
                        if($row['US_Id'] == $row['OF_Admin']) $_SESSION['preTally_offzAdmin'] = 'true'; else $_SESSION['preTally_offzAdmin'] = 'false';
                        $result = mysqli_query($GLOBALS['con'],'SELECT * FROM acl WHERE ACL_Id = '.$row['UT_Id']);                        
                        $ACL_Obj = mysqli_fetch_object($result);                        
                        $_SESSION['preTally_user_acl']	=	$ACL_Obj;// Creating Session with ACL
                        date_default_timezone_set($row['TZ_Name']);                              
                        $_SESSION['attendance_flag']    =       $this->checkAttendance($row['US_Id'],  date("Y-m-d"), "");                        
                        
                        $this->AN_IPData['US_Id']    = $_SESSION['preTally_user_id'];
                        $this->AN_IPData['AN_Time']  = date('Y-m-d H:i:s');
                        $this->AN_IPData['AN_CDate'] = date('Y-m-d');
                        $this->IPTracking($this->AN_IPData);                        
                        //return 'success';
                        if($flag == 0) {
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
         //----------------------------------------- Check Attendance ----------------------------------------//
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
		unset($_SESSION['preTally_user_id']);
		unset($_SESSION['preTally_user_name']);
		unset($_SESSION['preTally_user_type']);
                unset($_SESSION['preTally_user_empid']);
                unset($_SESSION['user_report_id']);
                session_destroy();
		setcookie("uacl", "", time()-3600);
		ob_clean();
		//header("location: index.php");
	}	
	//----------------------------------------- Reset Password ----------------------------------------//
	function resetPassword(){
                $sql = 'SELECT US_Email, US_FName,US_LName FROM users_auth WHERE US_Email="'.$this->US_PwdData['US_Email'].'"';
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
		$sql = 'SELECT US_Email FROM users_auth WHERE US_Email="'.$this->US_Email.'"';
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
		$sql = 'SELECT COUNT(US_Id) FROM users_auth WHERE US_Email="'.$this->US_Email.'" AND  US_Reset="'.$this->US_Reset.'"';
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
                                        LEFT JOIN `locations` AS LC ON US.LC_Id = LC.LC_Id '.$filt);
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
            $result=mysqli_query($GLOBALS['con'],'SELECT '.$field.' FROM users_auth '.$filt);
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
		$sql = 'SELECT COUNT(US_Id) FROM users_auth WHERE ( US_Email="'.$Email.'" || US_EMPID="'.$Email.'" )';
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
            $sql = 'SELECT US.US_Id,US.US_EMPID,US.US_Password,US.US_FName,US.US_LName,US.UT_Id,US.US_Status,US.OF_Id,US.LC_Id,US.DP_Id, OF.CR_Id, CR.CR_ISOCode, OF.OF_Admin, OF.TZ_Id, TZ.TZ_Name, OF.OF_Status
                        FROM users_auth as US , offices as OF 
                            LEFT JOIN currencies as CR ON OF.CR_Id = CR.CR_Id
                            LEFT JOIN time_zones as TZ ON OF.TZ_Id = TZ.TZ_Id
                            WHERE US.US_EMPID="'.$this->US_Login.'" AND US.OF_Id = OF.OF_Id';
//           die($sql);
           $login = mysqli_query($GLOBALS['con'],$sql);
           if(mysqli_num_rows($login)>0) { 
                if($row = mysqli_fetch_array($login,MYSQLI_ASSOC)) {
                    if($row['US_Status'] == 1 && $row['OF_Status'] == 1  ) { 
                            
                        session_start();
                        $_SESSION['preTally_user_id']	=	$row['US_Id'];  // Creating Session with E mial.
                        $_SESSION['preTally_user_empid']=       $row['US_EMPID']; 
                        $_SESSION['preTally_user_name']	=	$row['US_FName'].' '.$row['US_LName'];// Creating Session with User Name
                        $_SESSION['preTally_user_type']	=	$row['UT_Id'];// Creating Session with User Type
                        $_SESSION['preTally_user_ofid']	=	$row['OF_Id'];// Creating Session with Company
                        $_SESSION['preTally_user_lcid']	=	$row['LC_Id'];// Creating Session with Branch
                        $_SESSION['preTally_user_dpid']	=	$row['DP_Id'];// Creating Session with Department
                        $_SESSION['time_zone']          =       $row['TZ_Name'];
                        $_SESSION['currency']           =       $row['CR_ISOCode'];
                        $_SESSION['preTally_user_ofname']= $this->getOfficeName($row['OF_Id']); 
                        $_SESSION['preTally_user_lcname']= $this->getLocationName($row['LC_Id']);
                        
                        if($row['US_Id'] == $row['OF_Admin']) $_SESSION['preTally_offzAdmin'] = 'true'; else $_SESSION['preTally_offzAdmin'] = 'false';
                        $result = mysqli_query($GLOBALS['con'],'SELECT * FROM acl WHERE ACL_Id = '.$row['UT_Id']);
                        $ACL_Obj = mysqli_fetch_object($result);
                        $_SESSION['preTally_user_acl']	=	$ACL_Obj;// Creating Session with ACL
                        $_SESSION['attendance_flag']    =       $this->checkAttendance($row['US_Id'],  date("Y-m-d"), "",$row['TZ_Name']);                        
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
        function IPTracking($IPDataArray)
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
            $sql="SELECT ES_Id,US_LoginTime,US_LogoutTime,US_WrkHours FROM users_auth WHERE US_Id=".$usid;
            $this->UserLogArray= mysqli_fetch_assoc(mysqli_query($GLOBALS['con'],$sql));            
        }
         function takeSalaryHistory($usid,$amt,$mode){             
            $result=mysqli_query($GLOBALS['con'],"SELECT US_GrossSal FROM users_salary WHERE US_Id=".$usid); 
            $row=mysqli_fetch_assoc($result);
            if($row["US_GrossSal"]!=$amt || $mode=='new'){
            $insertSQL = "INSERT INTO salary_history (US_Id,Sal_Amt,Sal_Date) VALUES (".$usid.",".$amt.",'".date('Y-m-d')."')";
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
}
?>
