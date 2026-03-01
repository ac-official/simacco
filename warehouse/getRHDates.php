<?php
include_once ($BASEPATH . 'preTallyClass/AttendanceClass.php');
$HD_Id=$REQUEST['btchId'];
$HD_Dt=$REQUEST['hddate'];
$AttObj=new AttendanceClass();
echo $AttObj->getRHDays($HD_Id,$HD_Dt);
echo json_encode($AttObj->RH_Days);
?>