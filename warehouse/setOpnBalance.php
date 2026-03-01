<?php
require_once($BASEPATH . "preTallyClass/CashBSClass.php");
$OpnBal = new CashBSClass();
$obid=htmlspecialchars($_REQUEST['OB_Id']);
$OpnBal->Bal_Data=array(
 'OB_Date'      => htmlspecialchars($_REQUEST['OB_Date'], ENT_QUOTES),   
 'OB_OpenBal'   => htmlspecialchars($_REQUEST['OB_OpenBal'], ENT_QUOTES),  
 'OB_Status'   => 1
);
echo $OpnBal->updateOpenBal($obid);
?>