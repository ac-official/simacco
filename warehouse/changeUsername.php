<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj 	= new UserClass();
$US_UName 	= trim(preg_replace('/[^A-Za-z0-9_\-]/', '', $_REQUEST["US_UName"]));
if($US_UName != "") {
        $UserObj->US_Id 	= $_SESSION['preTally_user_id'];
	$UserObj->US_UName 	= $US_UName;	
	$response 		= $UserObj->updateUsername();
	echo $response;
} else { echo 'Error!!, Please Enter Username.'; }
?>