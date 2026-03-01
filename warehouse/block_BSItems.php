<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");

$bsId=$REQUEST['data'];
$BkupObj    =   new BackupClass();
$BSObj      =   new BalanceSheetClass();

$BkupObj->backupDetails('BS_Id = '.$bsId,$preTally_user_id, 'balance_sheets_bkup','balance_sheets');

echo $BSObj->blockBSItems($bsId, $REQUEST['CHQ_Number']);

if($REQUEST['IT_PettyCash'] == 1){
    $BSObj->updatePettyCashRefrenceEntries($bsId);
}

if(is_numeric($REQUEST['BS_PettyCashRefId']) && $REQUEST['BS_PettyCashRefId'] != 0){
    $BSObj->updatePettyCashAmount($REQUEST['BS_PettyCashRefId']);
}

?>