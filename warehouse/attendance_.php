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

if($_REQUEST['attnd']==0){
$AttObj->US_Data = array(
    'US_Id' 		=> $preTally_user_id,
	'AT_Date'		=> date('Y-m-d'),	
        'AT_SignIn'		=> date('H:i:s'),	
        'AT_SignOut'		=> "00:00:00",
        'AT_Hours'              => 0,
        'AT_Status'             =>0,
        'AT_IPAddr'             =>gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
	'AT_CDate' 		=> date('Y-m-d'),
);
if(isset($_COOKIE['uuid']))$AttObj->US_Data=array_merge($AttObj->US_Data, array('uuid'=>$_COOKIE['uuid']));
if($AttObj->checkAttendance($preTally_user_id)==0){
    echo $AttObj->MarkEntry();
    $UserObj->IPTracking();
}
 else {
    echo "Already Signed In.";
}
}
 else if($_REQUEST['attnd']==1){     
     if($AttObj->CheckSignOut($preTally_user_id)==1){
        $date=date('Y-m-d');$time=date('H:i:s');
        $uuid=isset($_COOKIE["uuid"]) ? $_COOKIE["uuid"] : '';
        echo $AttObj->MarkExit($preTally_user_id,$date,$time,$uuid);
        $UserObj->IPTracking();
}
 else{
        echo "Already Signed Out";
    }
}

?>