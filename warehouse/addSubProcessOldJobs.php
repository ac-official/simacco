<?php
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AtObj = new AttestationClass();
$checkTrack = $AtObj->getValue('tracks_old_jobs', '*', ' WHERE TR_Id = '.$REQUEST['TR_Id']);
if($checkTrack) {
    $AtObj->deleteRecords('tracks_old_jobs',' WHERE TR_Id = '.$REQUEST['TR_Id']);
} 
foreach ($REQUEST['checked'] as $rw) {
    $AtObj->Data = array(
        'TR_Id'             => $REQUEST['TR_Id'],
        'LC_Id'             => $REQUEST['LC_Id'],
        'APS_Id'            => $rw,
        'TOJ_CreatedBy'     => $preTally_user_id,
        'TOJ_LastUpdated'   => $preTally_user_id,
        'TOJ_MDate'         => date('Y-m-d H:i:s'),
        'TOJ_CDate'         => date('Y-m-d H:i:s')
    );
    $AtObj->insertRecords('tracks_old_jobs');
}