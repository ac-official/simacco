<?php
require_once($BASEPATH . "preTallyClass/BankBSClass.php");
$OpnBal = new BankBSClass();
$obid   = trim(htmlspecialchars($_REQUEST['OB_Id']));
$OpnBal->Bal_Data=array(
    'BnkOB_CDate'      => trim(htmlspecialchars($_REQUEST['OB_Date'], ENT_QUOTES)),   
    'BnkOB_OpenBal'    => trim(htmlspecialchars($_REQUEST['OB_OpenBal'], ENT_QUOTES)),  
    'BnkOB_Status'     => 1
);
echo $OpnBal->updateBnkOpenBal($obid);
?>