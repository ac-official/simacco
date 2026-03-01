<?php
require_once($BASEPATH . "preTallyClass/CashBSClass.php");

$BankBSObj = new CashBSClass();
$filter = 'LC_Id = '.$preTally_user_lcid.' ';
echo $OB_Status = $BankBSObj->VerifyCashOBStatus($filter);

?>