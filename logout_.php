<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj = new UserClass();
$UserObj->UserLogout();
//header("location: index.php");
?>