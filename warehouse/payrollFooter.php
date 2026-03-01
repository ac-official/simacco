<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
$AttObj->sumPayrollFooter($REQUEST['EP_Month'],$preTally_user_ofid,$REQUEST['EP_Year'],"");
echo json_encode($AttObj->payrollFooterArray);
?>