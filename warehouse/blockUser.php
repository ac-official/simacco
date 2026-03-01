<?php
include_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj = new UserClass();
$usid=$REQUEST["usid"];
$flag=$REQUEST["flag"];
$UserObj->blockUser($usid, $flag, date('Y-m-d'));
?>

