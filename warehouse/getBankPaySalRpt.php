<?php
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
$AttObj->getBankName($preTally_user_ofid);
$bnkObj=$AttObj->getBankNameArray;
echo json_encode($bnkObj);
?>