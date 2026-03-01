<?php
include_once($BASEPATH."preTallyClass/TrackClass.php");
$TrackObj = new TrackClass();
if($REQUEST['type'] == 'trackName') {
    $TrackObj->TR_Data = array(
        'TR_Track'  => strtoupper($TrackObj->cleanData($REQUEST['TR_Track']))
    );
    if(preg_match('/^[a-z0-9 _\-]+$/i',$REQUEST['TR_Track'])) {
        if($TrackObj->checkTrack($preTally_user_ofid, $REQUEST['TR_Id']) == 1) {
            echo "exists";
            exit;
        }
    }
    else {
        echo "invalid";
        exit;
    }
}
else if($REQUEST['type'] == 'status') {
   $TrackObj->TR_Data = array(
    'TR_Status'  => $REQUEST['TR_Status']
   ); 
}
echo $TrackObj->updateTrack(" WHERE TR_Id = ".$REQUEST['TR_Id']);
exit;