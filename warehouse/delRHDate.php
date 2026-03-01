<?php
include_once ($BASEPATH . 'preTallyClass/AttendanceClass.php');
$HD_Id=$REQUEST['hdId'];
$AttObj=new AttendanceClass();
echo $AttObj->delRHDays($HD_Id);
echo "Restricted Holiday Deleted";
?>


