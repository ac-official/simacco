<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
$AttObj->listEmpSalFooter($REQUEST['att_month'],$preTally_user_ofid,$REQUEST['att_year']);
echo json_encode($AttObj->salRptFooterArray);
?>