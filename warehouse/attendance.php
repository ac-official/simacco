<?php 
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
require_once("preTallyClass/UserClass.php");
require_once ('includes/BrowserDetect.php');

$AttObj = new AttendanceClass();
$browserDetect = new BrowserDetect;
$UserObj = new UserClass();
$systemType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
$scriptVersion = $browserDetect->getScriptVersion();
if($systemType=='phone') die("Attendance From Phone Not Allowed");
$UserObj->AN_IPData       = array(
    
    'US_Id'            => $preTally_user_id,
    'AN_IP'            => gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
    'AN_Time'          => date('Y-m-d H:i:s'), //2015-06-22 12:32:34
    'AN_SystemType'    => $systemType, //Computer / Tablet/ Mobile
    'AN_Browser'       => htmlentities($_SERVER['HTTP_USER_AGENT']), //Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:38.0)
    'AN_ScriptVersion' => $scriptVersion, //2.8.14
    'AN_LoginType'     => 'A', //Attendance
    'AN_CDate'         => date('Y-m-d')
);
$UserObj->selectPunchingTimes($preTally_user_id);
$CompObj = $UserObj->UserLogArray;
if($_REQUEST['attnd']==0){
$AttObj->US_Data = array(
    'US_Id'         => $preTally_user_id,
    'AT_Date'       => date('Y-m-d'),   
        'AT_SignIn'     => date('H:i').':00',   
        'AT_SignOut'        => "00:00:00",
        'AT_Hours'              => 0,
        'AT_Status'             =>0,
        'AT_IPAddr'             =>gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
    'AT_CDate'      => date('Y-m-d'),
    'deviceIn' => isset($_REQUEST['d']) ? $_REQUEST['d'] : 0
); 
/*
* Remove buffer time 19 Aug 2024
*/
if(isset($_COOKIE['uuid']))$AttObj->US_Data=array_merge($AttObj->US_Data, array('uuid'=>$_COOKIE['uuid']));
if($AttObj->checkAttendance($preTally_user_id)==0){
    $loginTime   = strtotime(date('H:i').':00');
    $actualTime  = strtotime($CompObj['US_LoginTime']);
    $lateSignMin = 0;
    if( $loginTime > $actualTime  ) {

        $lateSignMin = ($loginTime - $actualTime) / 60;
    }
    $AttObj->US_Data=array_merge($AttObj->US_Data, array('AT_SignInDelay'=>$lateSignMin));
    echo $AttObj->MarkEntry();
    $UserObj->IPTracking();
}
 else {
    echo "Already Signed In.";
}
}
 else if($_REQUEST['attnd']==1){     
     if($AttObj->CheckSignOut($preTally_user_id)==1){
        $date=date('Y-m-d');$time=date('H:i').':00';
        $uuid=isset($_COOKIE["uuid"]) ? $_COOKIE["uuid"] : '';
        $deviceOut= isset($_REQUEST['d']) ? $_REQUEST['d'] : 0;
        $ipOut    =gethostbyname($_SERVER['REMOTE_ADDR']);
        $logoutTime   = strtotime(date('H:i').':00');
        $alotedLogoutTime   = strtotime($CompObj['US_LogoutTime']);
        $actualLoginTime   = strtotime($CompObj['US_LoginTime']);
        $earlySignOutMin = 0;
        $extraTime = 0;
        if( $logoutTime < $alotedLogoutTime  ) {

            $earlySignOutMin = ($alotedLogoutTime - $logoutTime) / 60;  //Rule:: staff should keep their alloted time
        }
        else {
            $extraTime = ($logoutTime - $alotedLogoutTime) / 60;
        }
        $actualDutyTime = ($alotedLogoutTime - $actualLoginTime) / 60;
        //log actally alloted duty timing of specific day(timing may be change daily basics)
        $actualLoginLogOutTime = json_encode(array ("in"=>$CompObj['US_LoginTime'], "out"=>$CompObj['US_LogoutTime'], 'whour'=>$CompObj['US_WrkHours']));
        echo $AttObj->MarkExit($preTally_user_id, $date, $time, $uuid, $earlySignOutMin,  $extraTime,  $ipOut, $actualDutyTime, $actualLoginLogOutTime,$deviceOut);
        // Checkout user exceed's monthly quota grace time, (if exceed log it)
        $UserObj->selectCompanySettings($preTally_user_ofid);
        $CompSett = $UserObj->CompanySettingsArray;
        $monthlyGraceTime=$CompSett['CS_WrkHrGraceTime'];
        $cMonthutilizedGraceTime = $UserObj->selectUtilizedGraceTime($preTally_user_id, $CompObj['US_WrkHours'],  $monthlyGraceTime);
        if($cMonthutilizedGraceTime['grace_used'] > $monthlyGraceTime){
            $isOverDueExist = $UserObj->selectGraceTimeLog( $preTally_user_id );/*echo $isOverDueExist["count"];*/
            if( $isOverDueExist["count"]<=0 ){ //Not Exist
                $UserObj->insertGraceTimeLog( $preTally_user_id );
            }
        }
        //-------------------------------------------
        $UserObj->IPTracking();
}
 else{
        echo "Already Signed Out";
    }
}

?>
