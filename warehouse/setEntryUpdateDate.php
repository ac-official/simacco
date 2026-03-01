<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . "preTallyClass/BackupClass.php");
$AttestObj  = new AttestationClass();
$BkupObj    = new BackupClass();

$AttestObj->Data = array(
    'US_Id'     => $preTally_user_id,
    'OF_Id'     => $preTally_user_ofid,
    'BES_Date'  => $_REQUEST['BES_Date'],
    'BES_MDate' => date('Y-m-d H:i:s')
);
$BES_Count = $AttestObj->getValue('bs_entry_update_settings', 'COUNT(BES_Id) AS COUNT', ' WHERE OF_Id  = '.$preTally_user_ofid) ;

if($BES_Count > 0) {
    $BkupObj->backupDetails('BES_Id = 1',$preTally_user_id,'bs_entry_update_settings_bkup','bs_entry_update_settings');

    if($AttestObj->updateRecords('bs_entry_update_settings','OF_Id  = '.$preTally_user_ofid) == 'success')
        echo "Date Settings set successfully.";
    else 
        echo "fail";
}else{
    $AttestObj->Data['BES_CDate'] = date('Y-m-d H:i:s');
    if($AttestObj->insertRecords('bs_entry_update_settings') == 'success')
        echo "Date Settings set successfully.";
    else 
        echo "fail";
}

?>