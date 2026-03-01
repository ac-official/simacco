<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
$BalSheetObj = new BalanceSheetClass();
$BS_Id   = $REQUEST['BSId'];
$PCRefId = $REQUEST['PCRefId'];
echo $BS_Amount = $BalSheetObj->getAmount($BS_Id,$PCRefId);

?>