<?php
require_once($BASEPATH . "preTallyClass/AddressClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$ST_Id = $REQUEST['sid'];
//$ST_Id = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : 17 ;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$PlaceObj = new AddressClass();

    $PlaceObj->viewPlaces();
    $Pl_Obj = $PlaceObj->DataArray;

echo '<complete>';
     echo '<option value="0" selected="true">Select City</option>';	
if($CT_Obj){
    foreach($CT_Obj as $rw){
        echo '<option value="'.$rw->CT_Id.'">'.str_replace("&","&amp;",$rw->CT_Name).'</option>';
    }
}
echo '</complete>';
?>