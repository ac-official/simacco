<?php
require_once($BASEPATH . "preTallyClass/BankClass.php");
$chq_Stats  =$REQUEST['stats'];
$chq_Id     =$REQUEST['lv_id'];
$Chq_Obj    = new BankClass();
echo $Chq_Obj->changeChqStatus($chq_Id,$chq_Stats);
?>