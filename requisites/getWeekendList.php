<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$WeekendDetailsObj = new AttendanceClass();
$WeekendDetailsObj->listWeekends($preTally_user_id);
$WeekendDetails_Obj = $WeekendDetailsObj->WeekendDetailsArray;
foreach ($WeekendDetails_Obj as $rwii) {
    $weekends = $rwii->DH_Weekends;
}
echo "**".$weekends;
?>