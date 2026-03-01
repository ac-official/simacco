<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
$UserAtndObj = new UserClass();
echo $AT_Status = $UserAtndObj->VerifyAttendStatus($preTally_user_id, date("Y-m-d"));
?>