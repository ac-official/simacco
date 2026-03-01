<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
$month=date('M');
$curDate=date("Y-m-d");
echo $AttObj->calcAttendance($month,$curDate,36);



?>