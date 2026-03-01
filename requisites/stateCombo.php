<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");

$AttStObj = new AttendanceClass();
$AttStObj ->getStates($preTally_user_ofid);
$Att_StObj = $AttStObj->StateArray;

echo '<complete >'
. '<option value="0" selected="true">All</option>';
if($Att_StObj){      
    foreach($Att_StObj as $rw) {
        echo '<option value="'.$rw->ST_Id.'" >'.$rw->ST_Name.'</option>';
    }
}
echo '</complete>';
?>