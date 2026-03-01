<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
//date_default_timezone_set('Asia/Kolkata');

include_once("../spotterClass/UserClass.php");

$UserObj = new UserClass();

if($_REQUEST['password'] == $_REQUEST['rePassword']) {
	$UserObj->US_Password 	= md5($_REQUEST['password']);
	$UserObj->US_Email 		= htmlspecialchars($_SESSION['resetEmail'], ENT_QUOTES);
	$response = $UserObj->updateResetPassword();
	echo $response;
} else { echo 'Error!!, Please Re-Type Your Password Again.'; }
//print_r($_REQUEST);
?>