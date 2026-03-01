<?php 
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once ('includes/BrowserDetect.php');

$browserDetect = new BrowserDetect;

$systemType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
$scriptVersion = $browserDetect->getScriptVersion();

$UserObj = new UserClass();

$UserObj->AN_IPData       = array(
    
//    'US_Id'            => $preTally_user_id,
    'AN_IP'            => gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
//    'AN_Time'          => date('Y-m-d H:i:s'), //2015-06-22 12:32:34
    'AN_SystemType'    => $systemType, //Computer / Tablet/ Mobile
    'AN_Browser'       => htmlentities($_SERVER['HTTP_USER_AGENT']), //Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:38.0)
    'AN_ScriptVersion' => $scriptVersion, //2.8.14
    'AN_LoginType'     => 'LT', //Relogin
//    'AN_CDate'         => date('Y-m-d')
);
$UserObj->UserLogout();
if(isset($_COOKIE['so-catch'])) setcookie('so-catch','',time() - 3600,'/',false,true);
//header("location: index.php");
?>
<script type="text/javascript">location.href="http://143.110.177.16";</script>
<!-- <script type="text/javascript">const socket = io("http://simacco.in:3005");socket.emit("forceLogout")</script> -->
