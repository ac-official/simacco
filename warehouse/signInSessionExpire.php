<?php

//echo $BASEPATH;exit;
include_once($BASEPATH."_conf.php");
require_once($BASEPATH."preTallyClass/UserClass.php");
require_once ('includes/BrowserDetect.php');

$UserObj = new UserClass();
$browserDetect = new BrowserDetect;

$systemType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
$scriptVersion = $browserDetect->getScriptVersion();

$UserObj->AN_IPData       = array(
    
//    'US_Id'            => $preTally_user_id,
    'AN_IP'            => gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
//    'AN_Time'          => date('Y-m-d H:i:s'), //2015-06-22 12:32:34
    'AN_SystemType'    => $systemType, //Computer / Tablet/ Mobile
    'AN_Browser'       => htmlentities($_SERVER['HTTP_USER_AGENT']), //Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:38.0)
    'AN_ScriptVersion' => $scriptVersion, //2.8.14
    'AN_LoginType'     => 'R', //Relogin
//    'AN_CDate'         => date('Y-m-d')
);

$UserObj->US_Login	= htmlspecialchars($_REQUEST['username'], ENT_QUOTES);
$UserObj->US_Password	= md5($_REQUEST['password']);
$UserObj->US_Remember	= $_REQUEST['remember'];
echo $UserObj->signInUser($flag=1);

//$UserObj->IPTracking();

?>