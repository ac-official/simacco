<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj = new UserClass();

if($_REQUEST['newPassword'] == $_REQUEST['rePassword']) {
        $UserObj->US_Id=$_SESSION['preTally_user_id'];
	$UserObj->US_OldPassword 	= md5($_REQUEST['currPassword']);
        $UserObj->US_NewPassword 	= md5($_REQUEST['newPassword']);	
	$response = $UserObj->updatePassword();
	echo $response;
} else { echo 'Error!!, Please Re-Type Your Password Again.'; }
//print_r($_REQUEST);
?>