<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/PaymodeClass.php");

$PaymodeObj = new PaymodeClass();
$PaymodeObj->viewPaymodes('WHERE PM_Status = 1 ORDER BY PM_Name');
$Paymode_Obj = $PaymodeObj->PaymodeArray;

echo '<complete >';
if($Paymode_Obj){
    echo '<option value="" selected="true">Select Payment Mode</option>';
    foreach($Paymode_Obj as $rw) {
        echo '<option value="'.$rw->PM_Id.'" >'.$rw->PM_Name.'</option>';
    }
}
echo '</complete>';
?>