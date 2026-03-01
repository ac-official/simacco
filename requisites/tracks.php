<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/TrackClass.php");
$TrackObj = new TrackClass();

$IT_Id = $REQUEST['IT_Id'];
$key = $_REQUEST['mask'];
$key    =   $_REQUEST['mask']!='' ?    $_REQUEST['mask'] : $REQUEST['mask'] ;

$TrackObj -> viewTracks("WHERE OF_Id = '".$preTally_user_ofid."' AND TR_Track like '".$key."%' AND TR_Status != 0 LIMIT 0,10");
$TRObj = $TrackObj->TrackArray;

echo '<complete >';
if($TRObj){    
    foreach($TRObj as $rw) {
        echo '<option value="'.$rw->TR_Id.'" >'.$rw->TR_Track.'</option>';
    }
}
echo '</complete>';
?>