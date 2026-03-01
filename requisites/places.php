<?php
include_once($BASEPATH . "preTallyClass/AddressClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$key = $_REQUEST["mask"];
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$PlaceObj = new AddressClass();
$ct_id=$REQUEST['ctid'];
if($ct_id!=0 && $ct_id!=""){
     $PlaceObj->viewPlaces($key,"AND CT.CT_Id=".$ct_id);
}
else{
  $PlaceObj->viewPlaces($key,"");    
}
$Pl_Obj = $PlaceObj->PlaceArray;
echo '<complete>';    
if($Pl_Obj){
    foreach($Pl_Obj as $rw){
        echo '<option value="'.$rw->AP_Id.'">'.str_replace("&","&amp;",$rw->AP_Name).'</option>';
    }
}
echo '</complete>';
?>