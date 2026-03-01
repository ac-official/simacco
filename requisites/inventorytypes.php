<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/InventoryClass.php");

$InvObj = new InventoryClass();
$InvObj->listInventoryTypes();
$InvArr = $InvObj->InvItemReadArray;

echo '<complete >';
if($InvArr){
    echo '<option value="0" selected="true">Select Type</option>';
    foreach($InvArr as $rw) {
        echo '<option value="'.$rw->INV_TypeID.'" >'.$rw->INV_TypeName.'</option>';
    }
}
echo '</complete>';
?>