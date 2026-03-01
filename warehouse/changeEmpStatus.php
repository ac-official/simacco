<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
$emp_Stats  =$REQUEST['stats'];
$emp_Id     =$REQUEST['usid'];
$resgn_stats=$REQUEST['resgnStats'];
$Usr_Obj    = new UserClass();
if($emp_Stats!=0 && $emp_Stats!="" && $emp_Stats!=null)
$Usr_Obj->changeEmpStatus($emp_Id,$emp_Stats,$resgn_stats);
?>
