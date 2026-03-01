<?php
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AtObj = new AttestationClass();
$checkTrack = $AtObj->getValue('tracks_manageids', 'TR_Id', " WHERE TR_Id = ".$REQUEST['TR_Id']);

if($REQUEST['state'] == 'true') {   // checked
   if($checkTrack) {
        $AtObj->Data = array(
            'TOJ_LastUpdated'   => $preTally_user_id,
            'TOJ_MDate'         => date('Y-m-d H:i:s')
        );
        $AtObj->updateRecords('tracks_manageids',"TR_Id = ".$REQUEST['TR_Id']);
    } else { 
        $AtObj->Data = array(
            'TR_Id'             => $REQUEST['TR_Id'],
            'LC_Id'             => $preTally_user_lcid,
            'TM_CreatedBy'      => $preTally_user_id,
            'TM_LastUpdated'    => $preTally_user_id,
            'TM_MDate'          => date('Y-m-d H:i:s'),
            'TM_CDate'          => date('Y-m-d H:i:s')
        );
        $AtObj->insertRecords('tracks_manageids');
    }
} else {   // unchecked
    $AtObj->deleteRecords('tracks_manageids',' WHERE TR_Id = '.$REQUEST['TR_Id']);
}