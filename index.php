<?php
require_once('_conf.php');

if(strstr($_SERVER['HTTP_HOST'],'www')) { header('location:'.Settings::getPublic('base_path')); exit(); }
if(strstr($_SERVER['REQUEST_URI'],'index.php')) { header('location:'.Settings::getPublic('base_path')); exit(); }
session_start();
$GLOBALS['BASEPATH'] = Settings::getPublic('site_root');
error_reporting(Settings::getProtected('error_reporting'));
Settings::setProtected('time_zone', $_SESSION['time_zone']);
date_default_timezone_set(Settings::getProtected('time_zone'));
require_once($BASEPATH.'crypt/crypt.php');

    $preTally_user_id       = $_SESSION['preTally_user_id']; 
    $preTally_user_name 	= $_SESSION['preTally_user_name']; 
    $preTally_user_empid    = $_SESSION['preTally_user_empid'];
    $preTally_user_uname    = $_SESSION['preTally_user_uname'];
    $preTally_user_type 	= $_SESSION['preTally_user_type']; 
    $preTally_user_ofid 	= $_SESSION['preTally_user_ofid']; 
    $preTally_user_lcid 	= $_SESSION['preTally_user_lcid'];
    $preTally_user_ofname 	= $_SESSION['preTally_user_ofname']; 
    $preTally_user_lcname 	= $_SESSION['preTally_user_lcname'];
    $preTally_user_dpid 	= $_SESSION['preTally_user_dpid'];
    $ACL_Obj                = $_SESSION['preTally_user_acl'];
    $att_flag               = $_SESSION['attendance_flag']; 
    $currency               = $_SESSION['currency'];
    $pretally_offzAdmin     = $_SESSION['preTally_offzAdmin'];
    $UserACLObj             = $_SESSION['preTally_user_sacl']; // 04-07-2025
    $sessionToken           = $_SESSION['token'];
	
//aa
//require_once($BASEPATH . 'crypt/crypt.php');
//if(!empty($REQUEST['p'])) {
//this._loader.setRequestHeader("X-Requested-With","XMLHttpRequest");
if((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $REQUEST['p'] != "warehouse/signInSessionExpire.php") || $REQUEST['p'] == 'requisites/myIEGraph.php' ) {
    
    //echo 'SESSION EXPIRED';    
    if((!$preTally_user_id) || (!$preTally_user_name) || (!$preTally_user_type)) {        
        //echo 'SESSION EXPIRED';
        
        /*header("Content-type: text/xml", true, 401);
        echo "<data>";
        echo "<action type='error'>";
        echo "<![CDATA[\n";
        echo "var message='SESSION EXPIRED';";
        echo "\n]]>";
        echo "</action>";
        echo "</data>";
        die();*/
    }
    
    $z = 0;
    if($z == 1) {
        header("Content-type: text/xml", true, 401);
        echo "  <data>
                    <action type='error'>
                        <![CDATA[\n var message='SESSION EXPIRED'; \n]]>
                    </action>
                </data>";
        die();
    }
    $ajax = Settings::getProtected('ajax_check');
    require_once($BASEPATH . 'includes/sessions.php');
    require_once($BASEPATH . $REQUEST['p']);
    exit();

} else {
	require_once("preTallyClass/UserClass.php");
    $UserObj   = new UserClass();
    $authtoken = $UserObj->getToken($preTally_user_id);
    if($REQUEST['p'] == "warehouse/signInSessionExpire.php") {
        require_once($BASEPATH . $REQUEST['p']);
    }
    else if(($preTally_user_id) && ($preTally_user_name) && ($preTally_user_type) && $sessionToken==$authtoken['token']) { 
        require_once('home.php');
    } else {	
        if(isset($_SESSION['token']))session_destroy();	
        require_once('authenticate.php');
    }
}
?>
