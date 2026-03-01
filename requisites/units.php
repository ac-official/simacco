<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/UnitClass.php");

$UnitObj = new UnitClass();
$UnitObj->viewUnits('WHERE UT_Status = 1 ORDER BY UT_Name');
$Unit_Obj = $UnitObj->UnitArray;

echo '<complete >';
if($Unit_Obj){
    echo '<option value="0" selected="true">Select Unit</option>';
    foreach($Unit_Obj as $rw) {
        echo '<option value="'.$rw->UT_Id.'" >'.$rw->UT_Name.'</option>';
    }
}
echo '</complete>';
?>