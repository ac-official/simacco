<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/ItemClass.php");
$key  = $_REQUEST["mask"]; 
//print_r($REQUEST);
$ItemObj = new ItemClass();
if($key)  {$filtr = "AND IT_Name LIKE '".$key."%'";}
$ItemObj->viewItems("*","WHERE IT_Status=1 ".$filtr);
$IT_Obj = $ItemObj->ItemArray;
echo '<complete>';
echo '<option selected="true">Select Item</option>';
if($IT_Obj){      
    foreach($IT_Obj as $rw) {
        echo '<option value="'.$rw->IT_Id.'" >'.$rw->IT_Name.' </option>';
    }
}
echo '</complete>';
?>