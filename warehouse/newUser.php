<?php
include_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj = new UserClass();
$user_id = $REQUEST['r'];
//print_r($_REQUEST);
    /*--------------------------------------------Authentication Details--------------------------------------------------------*/
    $UserObj->US_AuthData       = array(
        'US_Image'              => htmlspecialchars($_REQUEST['US_Image'], ENT_QUOTES),
        'US_MDate'              => date('Y-m-d H:i:s')
    );
    /*--------------------------------------------Personal Details--------------------------------------------------------*/
    $UserObj->US_PersonalData   = array(
        'US_Gender'             => htmlspecialchars($_REQUEST['US_Gender'], ENT_QUOTES),
        'US_DOB'                => htmlspecialchars($_REQUEST['US_DOB'], ENT_QUOTES),
        'US_Address'            => htmlspecialchars($_REQUEST['US_Address'], ENT_QUOTES),        
        'US_Pemail1'            => htmlspecialchars($_REQUEST['US_Pemail1'], ENT_QUOTES),
        'US_Pemail2'            => htmlspecialchars($_REQUEST['US_Pemail2'], ENT_QUOTES),
        'US_Mobile1'            => trim(htmlspecialchars($_REQUEST['US_Mobile1'], ENT_QUOTES)),
        'US_Mobile2'            => trim(htmlspecialchars($_REQUEST['US_Mobile2'], ENT_QUOTES)),
        'US_Landline1'          => trim(htmlspecialchars($_REQUEST['US_Landline1'], ENT_QUOTES)),
        'US_Landline2'          => trim(htmlspecialchars($_REQUEST['US_Landline2'], ENT_QUOTES)),
        'CT_Id'                 => htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES),
        'ST_Id'                 => htmlspecialchars($_REQUEST['ST_Id'], ENT_QUOTES),
        'CN_Id'                 => htmlspecialchars($_REQUEST['CN_Id'], ENT_QUOTES),
        'US_Relative'           => trim(htmlspecialchars($_REQUEST['US_Relative'], ENT_QUOTES)),
        'BG_Id'                 => htmlspecialchars($_REQUEST['US_Blood'], ENT_QUOTES),
        'US_Guardian'           => trim(htmlspecialchars($_REQUEST['US_Guardian'], ENT_QUOTES)),
        'US_Guardianphone'      => trim(htmlspecialchars($_REQUEST['US_Guardianphone'], ENT_QUOTES)),
        'US_Emergencyperson'    => trim(htmlspecialchars($_REQUEST['US_Emergencyperson'], ENT_QUOTES)),
        'US_Emergencynumber'    => trim(htmlspecialchars($_REQUEST['US_Emergencynumber'], ENT_QUOTES)),
        'US_Emergencyrelation'  => trim(htmlspecialchars($_REQUEST['US_Emergencyrelation'], ENT_QUOTES)),
        'US_Passport'           => trim(htmlspecialchars($_REQUEST['US_Passport'], ENT_QUOTES)));
    /*--------------------------------------------Qualification Details--------------------------------------------------------*/
    $UserObj->US_QualData       = array( 
        'US_Qualification'      => trim(htmlspecialchars($_REQUEST['US_Qualification'], ENT_QUOTES)),
        'US_Specialization'     => trim(htmlspecialchars($_REQUEST['US_Specialization'], ENT_QUOTES)),
        'US_Experience'         => trim(htmlspecialchars($_REQUEST['US_Experience'], ENT_QUOTES)),
        'US_LastEmployee'       => trim(htmlspecialchars($_REQUEST['US_LastEmployee'], ENT_QUOTES)));
    /*--------------------------------------------Account Details--------------------------------------------------------*/
    
    $UserObj->US_AccountData    = array(
        'US_PAN'                => trim(htmlspecialchars($_REQUEST['US_PAN'], ENT_QUOTES)),        
        'US_AccNo'              => trim(htmlspecialchars($_REQUEST['US_AccNo'], ENT_QUOTES)),
        'US_Bankname'           => trim(htmlspecialchars($_REQUEST['US_Bankname'], ENT_QUOTES)),
        'US_BankBranch'         => trim(htmlspecialchars($_REQUEST['US_BankBranch'], ENT_QUOTES)),
        'US_PFNo'               => trim(htmlspecialchars($_REQUEST['US_PFNo'], ENT_QUOTES)),
        'US_ESI'                => trim(htmlspecialchars($_REQUEST['US_ESI'], ENT_QUOTES)),
        'SP_Id'                 => htmlspecialchars($_REQUEST['SP_Id'], ENT_QUOTES));
    /*--------------------------------------------Salary Details--------------------------------------------------------*/
    $UserObj->US_SalaryData     = array();
    $work_allow_Off             = (isset($_REQUEST['allowed_office']) && $_REQUEST['allowed_office'] != '') ? explode(',',$_REQUEST['allowed_office']) :[];  //28-03-2025

    if($user_id != "self") {
        
        $US_Report = isset($_REQUEST['US_Report']) ? $_REQUEST['US_Report'] : $_REQUEST['H_US_Report_Id'] ;
        $loginTime= $_REQUEST["login_Hr"].":".$_REQUEST["login_Min"].":00";
        $logoutTime= $_REQUEST["logout_Hr"].":".$_REQUEST["logout_Min"].":00";       
        $wrkhrs=round(abs(strtotime($logoutTime) - strtotime($loginTime)) / 60);
        $UserObj->US_AuthData['OF_Id']          =  htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES);
        $UserObj->US_AuthData['LC_Id']          =  htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES);
        $UserObj->US_AuthData['US_EMPID']       =  trim(htmlspecialchars($_REQUEST['US_EMPID'], ENT_QUOTES));   
        $UserObj->US_AuthData['US_UName']       =  trim(preg_replace('/[^A-Za-z0-9_\-]/', '', $_REQUEST["US_UName"]));	
        if($user_id == 0) {
            $UserObj->US_AuthData['US_Password']    =  md5(trim(htmlspecialchars($_REQUEST['US_Password'], ENT_QUOTES)));	
        }
        $UserObj->US_AuthData['UT_Id']          =  htmlspecialchars($_REQUEST['US_Type'], ENT_QUOTES);	
        $UserObj->US_AuthData['US_FName']       =  trim(htmlspecialchars($_REQUEST['US_FName'], ENT_QUOTES));
	    $UserObj->US_AuthData['US_LName']= trim(htmlspecialchars($_REQUEST['US_LName'], ENT_QUOTES));
        $UserObj->US_AuthData['US_DOJ']         =  htmlspecialchars($_REQUEST['US_DOJ'], ENT_QUOTES);
        $UserObj->US_AuthData['DP_Id']          =  htmlspecialchars($_REQUEST['DP_Id'], ENT_QUOTES);        
        $UserObj->US_AuthData['DG_Id']          =  htmlspecialchars($_REQUEST['DG_Id'], ENT_QUOTES);       
        $UserObj->US_AuthData['US_Email']       =  trim(htmlspecialchars($_REQUEST['US_Email'], ENT_QUOTES));
        $UserObj->US_AuthData['US_Report']      =  $US_Report;
	    $UserObj->US_AuthData['US_Status']   =  htmlspecialchars($_REQUEST['US_Status'], ENT_QUOTES);
        $UserObj->US_AuthData['US_LoginTime']   =  $loginTime;
        $UserObj->US_AuthData['US_LogoutTime']  =  $logoutTime; 
        $UserObj->US_AuthData['US_WrkHours']    =  $wrkhrs; 

        // Start grace time allowed user details Start @ 24-07-2025
        $UserObj->GraceTimeAry  = ['edit'=>0, 'save'=>0, 'user_id'=>$user_id, 'worktime'=>$wrkhrs, 'end_date'=>'', 'reason'=>'', 'is_tempoary'=>1, 'login_user'=>$preTally_user_id ];
        $graceSetting = $UserObj->getUserTimes($user_id, $UserObj->US_AuthData['OF_Id']);
        $UserObj->GraceTimeAry['old_time'] = json_encode(array ("in"=>$graceSetting->CS_OfficeStart, "out"=>$graceSetting->CS_OfficeEnds, 'whour'=>$graceSetting->CP_WHours));

        if ($wrkhrs < $graceSetting->CP_WHours) {
            $UserObj->US_AuthData['US_Time_Reduced']  =  1;
            $UserObj->US_AuthData['US_Grace_Temp']    = $_REQUEST['US_Grace_Temp'];
            if ($graceSetting->US_WrkHours < $graceSetting->CP_WHours) {
                $UserObj->GraceTimeAry['edit'] = 1;
                if ($graceSetting->US_WrkHours != $wrkhrs) { //existing time change (existing have grace time)
                    $UserObj->GraceTimeAry['save'] = 1;
                    if ($graceSetting->US_WrkHours < $wrkhrs) {
                        $UserObj->US_AuthData['US_Grace_Temp']    = $graceSetting->US_Grace_Temp;  
                    }
                } else { // no change in timing

                    $UserObj->US_AuthData['US_Grace_Temp']    = $graceSetting->US_Grace_Temp;
                    $UserObj->GraceTimeAry['edit'] = 0;
                    if ($graceSetting->US_Time_Reduced == 0) { 
                        $UserObj->GraceTimeAry['save'] = 1;
                        $UserObj->GraceTimeAry['edit'] = 1;
                    }
                }
            } else {
                $UserObj->GraceTimeAry['save'] = 1;
            }
            $UserObj->GraceTimeAry['end_date']  = htmlspecialchars($_REQUEST['Grace_End_Date'], ENT_QUOTES);
            $UserObj->GraceTimeAry['reason']    = htmlspecialchars($_REQUEST['Grace_Reason'], ENT_QUOTES);
            $UserObj->GraceTimeAry['is_tempoary'] = $UserObj->US_AuthData['US_Grace_Temp'];
        } else {
            $UserObj->US_AuthData['US_Time_Reduced']  =  0; 
            $UserObj->GraceTimeAry['edit'] = ($graceSetting->US_WrkHours < $graceSetting->CP_WHours) ? 1 :0;
        }
        // end the grace time realted sections @ 24-07-2025
        if ($UserObj->GraceTimeAry['save'] == 1) {
            if ($graceSetting->US_WrkHours > $wrkhrs || $graceSetting->US_WrkHours >= $graceSetting->CP_WHours) { //old time greater than newly added time
                $UserObj->GraceTimeAry['old_time'] = json_encode(array ("in"=>$graceSetting->US_LoginTime, "out"=>$graceSetting->US_LogoutTime, 'whour'=>$graceSetting->US_WrkHours));
            } else if ($graceSetting->US_Grace_Temp == 0 && $UserObj->US_AuthData['US_Grace_Temp'] == 1) {
                $UserObj->GraceTimeAry['old_time'] = json_encode(array ("in"=>$graceSetting->US_LoginTime, "out"=>$graceSetting->US_LogoutTime, 'whour'=>$graceSetting->US_WrkHours));
            } 
        }
        // grace time with existing old timing json @ 30-07-2025


        if($_REQUEST['H_ES_Id']!=$_REQUEST['ES_Id']){            
            $UserObj->US_AuthData['ES_Id']  =  $_REQUEST['ES_Id'];     
            $resignFlag=0;
            $H_ES_Resign  = explode(',', $_REQUEST['H_ES_Resign']);
            //if($_REQUEST['H_ES_Resign']==$_REQUEST['ES_Id'])
            if (in_array($_REQUEST['ES_Id'],$H_ES_Resign)) { // change if @ 29-07-25 Bilin
                $resignFlag=1;
            }
            $UserObj->changeEmpStatus($user_id,$_REQUEST['ES_Id'],$resignFlag,$_REQUEST['ESH_Date']);
        }
        if($_REQUEST['US_Status']==0){
            $blktime = date("Y-m-d");        
        } else{
            $blktime = "1970-01-01";
        }        
        $UserObj->US_AuthData['US_BlkdDate']  =$blktime;
        if(trim(htmlspecialchars($_REQUEST['SP_Id'], ENT_QUOTES)) != 1){
            $Sal_BnkId              = 0;
        }
        else {    
            if($_REQUEST['Bank_AC'])
                $Sal_BnkId              = trim(htmlspecialchars($_REQUEST['Bank_AC'], ENT_QUOTES));
            else
                $Sal_BnkId              = 0;
        }
        $UserObj->US_SalaryData    = array(
            'SS_Id'                 =>  htmlspecialchars($_REQUEST['SS_Id'],ENT_QUOTES),
            'US_GrossSal'           => htmlspecialchars($_REQUEST['US_GrossSal'], ENT_QUOTES),
            'US_BasicSal'           => htmlspecialchars($_REQUEST['US_BasicSal'], ENT_QUOTES),
            'US_DaSal'              => 0,
            'US_CcaSal'             => htmlspecialchars($_REQUEST['US_CcaSal'], ENT_QUOTES),
            'US_HraSal'             => htmlspecialchars($_REQUEST['US_HraSal'], ENT_QUOTES),
            'US_ConveySal'          => htmlspecialchars($_REQUEST['US_ConveySal'], ENT_QUOTES),
            'US_EduSal'             => htmlspecialchars($_REQUEST['US_EduSal'], ENT_QUOTES),
            'US_MedSal'             => htmlspecialchars($_REQUEST['US_MedSal'], ENT_QUOTES),
            'US_MiscSal'            => htmlspecialchars($_REQUEST['US_MiscSal'], ENT_QUOTES),            
            'US_DedEPF'             => htmlspecialchars($_REQUEST['US_DedEPF'], ENT_QUOTES),
            'US_DedESI'             => htmlspecialchars($_REQUEST['US_DedESI'], ENT_QUOTES),
            'US_DedSalTDS'          => htmlspecialchars($_REQUEST['US_DedSalTDS'], ENT_QUOTES),
            'US_DedProfTDS'         => htmlspecialchars($_REQUEST['US_DedProfTDS'], ENT_QUOTES),
            'US_DedMealCard'         => htmlspecialchars($_REQUEST['US_DedMealCard'], ENT_QUOTES),
            'US_DedLWF'             => htmlspecialchars($_REQUEST['US_DedLWF'], ENT_QUOTES),            
            'Sal_BnkId'             => $Sal_BnkId);   
    }
    if($_REQUEST['US_MiscSal']<0){
        echo "sal_error";
        exit();
    }
     if($_REQUEST['SP_Id']==""){
          $UserObj->US_AccountData['SP_Id']=0; 
    }
    if($user_id == "self") {$_REQUEST['SS_Id']=$_REQUEST['H_SS_Id'];} 
    if($_REQUEST['SS_Id']=="")
    {
        $UserObj->US_SalaryData['SS_Id']=0;
         $UserObj->US_SalaryData    = array(
            'SS_Id'                 =>  0,
            'US_GrossSal'           =>  0,
            'US_DaSal'              =>  0,
            'US_CcaSal'             =>  0, 
            'US_HraSal'             =>  0,
            'US_ConveySal'          =>  0,
            'US_EduSal'             =>  0,
            'US_MedSal'             =>  0,
            'US_MiscSal'            =>  0,
            'US_BasicSal'           =>  0,
            'US_DedEPF'             =>  0,
            'US_DedESI'             =>  0,
            'US_DedSalTDS'          =>  0,
            'US_DedMealCard'        =>  0,
            'US_DedProfTDS'         =>  0,
            'US_DedLWF'             =>  0,
            'Sal_BnkId'             => $Sal_BnkId);   
    }
     if($user_id == "self") { $US_Report= $_REQUEST['H_US_Report_Id']; } 
//if($_REQUEST['US_ID']!=0) {   
//    echo $UserObj->updateUser($_REQUEST['US_ID']);     
//} else {
//    echo $UserObj->newUser();
//}
//print_r(  $UserObj->US_SalaryData ); die();
foreach ($UserObj->US_SalaryData as $key => $value) {
    if (is_null($value) || $value=="") {
         $UserObj->US_SalaryData[$key] = 0;
    }
}
// reporting officer parent level checking if any infinite loop presend then fail retunr
// This checking added by bIlin @ 17-10-2024
if($UserObj->checkReportUserTree($US_Report, $user_id,[]) == 0) {
    echo 'reporting_fail';
    return;
}
// Checking Ends

if( $UserObj->verifyReportingUser(htmlspecialchars($US_Report, ENT_QUOTES)) ) {    
	if(htmlspecialchars($_REQUEST['US_ID'], ENT_QUOTES) == 0) {
        $UserObj->US_AuthData['US_RptFlag']     =  0;
        $UserObj->US_AuthData['ES_Id']          =  htmlspecialchars($_REQUEST['ES_Id'], ENT_QUOTES);
		$esid   = htmlspecialchars($_REQUEST['ES_Id'], ENT_QUOTES);
        $UserObj->US_AuthData['US_CDate']       =  date('Y-m-d H:i:s');
        $UserObj->US_AuthData['US_CreatedBY']   = $preTally_user_id;
        $usid   = $UserObj->newUser();
                //if(!is_int($usid)) { echo 'id_fail'; }
        if ($usid <= 0) { 
            echo ($UserObj->newUsrFlag == 'ok') ? 'id_fail'.$usid: $UserObj->newUsrFlag; //04-12-2024
        } else {
			$UserObj->takeSalaryHistory($usid,$UserObj->US_SalaryData['US_GrossSal'],'new');
            $UserObj->createEmpStatusRecord($usid,$esid);

            $UserObj->mapOfficeUsers($usid, $work_allow_Off, 1, $preTally_user_id); //28-03-2025
            echo 'New User Created Successfully'; 
        }
	} else {                
        $UserObj->US_AuthData['US_MDate']       =  date('Y-m-d H:i:s');
        $UserObj->copyUser($_REQUEST['US_ID'],$preTally_user_id);     
        $UserObj->takeSalaryHistory($_REQUEST['US_ID'],$UserObj->US_SalaryData['US_GrossSal'],'upd');
        $UserObj->updateEmpStatusDate($_REQUEST['US_ID'],$_REQUEST['ES_Id'],$_REQUEST['ESH_Date']);
        $UserObj->mapOfficeUsers($_REQUEST['US_ID'], $work_allow_Off, 2, $preTally_user_id); //28-03-2025
		echo $UserObj->updateUser($_REQUEST['US_ID']); 
	}
    $UserObj->updateReportingFlg($US_Report,$_REQUEST['H_US_Report_Id']); 
} else { echo 'reporting_fail'; }
?>