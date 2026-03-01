<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/MainheadClass.php");

$MHType = $REQUEST['MH_Type'];
$MainheadObj = new MainheadClass();
$MainheadObj->viewMainheads(' WHERE MH_Status = 1 AND MH_type ='.$MHType.' ORDER BY MH_Name');
$MH_Obj = $MainheadObj->MainheadArray;

echo '<complete >';
if($MH_Obj){
    foreach($MH_Obj as $rw) {
        echo '<option value="'.$rw->MH_Id.'" >'.$rw->MH_Name.'</option>';
    }
}
echo '</complete>';
?>