<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once($BASEPATH . "preTallyClass/LeaveClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
$AttUpdObj = new AttendanceClass();
$year       = date("Y");
if(isset($REQUEST['EP_Month'])){ // Unused Right now.
    $month=$REQUEST['EP_Month'];
    if($AttUpdObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year)){ 
        echo "11111";
    }else{
        echo "00000";
    }
    return;
}
$LeaveObj = new LeaveClass();
$LeaveObj->getLeaveType($preTally_user_ofid);
$aObj = $LeaveObj->leaveTypeArray;
$USId       = $REQUEST["usid"];
$AttDate    = $REQUEST["date"];
$dayName    = date("l", strtotime($AttDate));
$leaveinfo=explode("_",$REQUEST["updType"]);
$updType    = $leaveinfo[0];
$prevVal    = $REQUEST["prevVal"];
$UserObj = new UserClass();
$UserObj->selectPunchingTimes($USId);
$CompObj = $UserObj->UserLogArray;
if($updType==$prevVal && $REQUEST["cmbval"]!="Abs")return;
if ($updType == "P") { //Mark as Full day Present
    $AttUpdObj->deleteRHLog($USId, $AttDate);
    $LeaveObj->deleteLeaveRecords($USId, $AttDate);
    $AttUpdObj->ATT_Data = array(
        'US_Id'         => $USId,
        'AT_Date'       => $AttDate,
        'AT_SignIn'     => $CompObj['US_LoginTime'],
        'AT_SignOut'    => $CompObj['US_LogoutTime'],
        'AT_Hours'      => $CompObj['US_WrkHours'],
        'AT_Status'     => 1,
        'AT_IPAddr'     =>gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
        'AT_CDate'      => date("Y-m-d"),
        'AT_AllotTime'  => json_encode(array ("in"=>$CompObj['US_LoginTime'], "out"=>$CompObj['US_LogoutTime'], 'whour'=>$CompObj['US_WrkHours'])),
        'AT_SignOutEarly' => 0,
        'AT_SignInDelay' => 0
    );    
    $AttUpdObj->createAttRecord();
}

else if ($updType == "Hlf") {//Change To Half Day Leave
    $AttUpdObj->deleteAttendance($USId, $AttDate); 
    $AttUpdObj->deleteRHLog($USId, $AttDate);
    $LeaveObj->deleteLeaveRecords($USId, $AttDate);
    $hlfdaytime=date("H:i:s",strtotime($CompObj['US_LoginTime'])+($CompObj['US_WrkHours']/2*60));
    $AttUpdObj->ATT_Data    = array('US_Id'         => $USId,
        'AT_Date'       => $AttDate,
        'AT_SignIn'     => $CompObj['US_LoginTime'],
        'AT_SignOut'    => $hlfdaytime,
        'AT_Hours'      => $CompObj['US_WrkHours']/2,
        'AT_Status'     => 1,
        'AT_IPAddr'     =>gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
        'AT_CDate'      => date("Y-m-d"),
        'AT_AllotTime'  => json_encode(array ("in"=>$CompObj['US_LoginTime'], "out"=>$CompObj['US_LogoutTime'], 'whour'=>$CompObj['US_WrkHours'])),
        'AT_SignOutEarly'=>($CompObj['US_WrkHours']/2),
    );     
    $AttUpdObj->createAttRecord(); 
}
else if ($updType == "L") {// For Absent
    $AttUpdObj->deleteAttendance($USId, $AttDate);
    $LeaveObj->deleteLeaveRecords($USId, $AttDate);
    $AttUpdObj->deleteRHLog($USId, $AttDate);
}
else if($updType == "RH"){
    $AttUpdObj->deleteAttendance($USId, $AttDate);
    $LeaveObj->deleteLeaveRecords($USId, $AttDate);
    $batch_Id=$AttUpdObj->getBatchByDate($AttDate,$preTally_user_ofid);
    if($batch_Id) {
        $AttUpdObj->createRHLog($USId, $batch_Id, $AttDate);
    }
}
else if (is_numeric($updType) && $updType != 0) {//Mark leave with leave type    
    if($leaveinfo[1]=='FL'){
        $AttUpdObj->deleteAttendance($USId, $AttDate);
    }
    else if($leaveinfo[1]=='FN' || $leaveinfo[1]=='AN' ){ 
        $AttUpdObj->alterAttendance($USId, $AttDate,$leaveinfo[1]);         
    } 
    $LeaveObj->deleteLeaveRecords($USId, $AttDate);
    $AttUpdObj->deleteRHLog($USId, $AttDate);
    assignLeave($USId,$AttDate,$updType,$preTally_user_id,$leaveinfo);
}


function assignLeave($USId, $LvDate, $LType, $addedBy,$leaveinfo) {
    $LeaveObj   = new LeaveClass();
    $fromDate   = $toDate = $LvDate;
    $fromDate   = date("Y-m-d", strtotime($fromDate));
    $toDate     = date("Y-m-d", strtotime($toDate));
    if($leaveinfo[1]=='FL')$days=1;
    else $days=0.5;
    $LeaveObj->Leave_Data = array(
    'US_Id'                 => $USId,     
    'LR_AppliedFor'         => $USId,     
    'LT_Id'                 => $LType,
    'LR_NumOFDays'          => $days,
    'LR_Session'            => $leaveinfo[1],
    'LR_FromDate'           => $fromDate,
    'LR_ToDate'             => $toDate,
    'LR_CreditedDate'       => date("Y-m-d"),
    'LR_CDate'       => date("Y-m-d"),    
    'LR_Reason'             => "Assigned By Admin",
    'LR_FirstApproval'      => $addedBy,
    'LR_ApprovedHR'         => $addedBy,    
    'LR_Status'             => 2,        
    );
    $LR_Id = $LeaveObj->applyLeave();

    $LeaveObj->Leave_Day_Data = array(
        'LR_Id'                 => $LR_Id,
        'US_Id'                 => $USId,
        'LT_Id'                 => $LType,
        'LRD_Date'              => $fromDate,
        'LRD_Days'              => $days,
        'LRD_Session'           => $leaveinfo[1]
    );
    $result = $LeaveObj->applyLeavedays();
}


die($updType);

?>
