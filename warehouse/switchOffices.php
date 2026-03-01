<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
	
$UserObj = new UserClass();
$UserObj->US_Login = htmlspecialchars($REQUEST['userName'], ENT_QUOTES);

$UserObj->UserLogout();
$UserObj->switchAccount( $flag = 1 );
?>
