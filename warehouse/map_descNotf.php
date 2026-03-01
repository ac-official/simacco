<?php
require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");

$DS_MapObj = new NotificationClass();
$BkupObj   = new BackupClass();

if( $DS_MapObj->verifyMappedDesc(htmlspecialchars($_REQUEST['DS_MapId'], ENT_QUOTES))) {
    $DS_MapObj->DS_MapData = array(
        'BS_Description' =>htmlspecialchars($_REQUEST['DS_MapId'], ENT_QUOTES),    
    );
    
    $BkupObj->backupDetails('BS_Description = '.$_REQUEST['DS_Id'],$preTally_user_id, 'balance_sheets_bkup','balance_sheets');
    
    echo $DS_MapObj->swapDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
} else { echo 'fail'; } 

?>
