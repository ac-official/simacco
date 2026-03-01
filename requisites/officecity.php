<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$Officeid= $_GET['ofid'];
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/GeneralClass.php");

$GeneralObj = new GeneralClass();
if($Officeid!=0)
{
//$GeneralObj->GetValues(' * ', 'locations',' OF_Id ', $Officeid ,' LC_Name ');
$GeneralObj->getComboDetails(' * ', 'locations',' OF_Id="'.$Officeid.'" AND LC_Status != 5',' LC_Name ','0, 1500'); //03-04-2025
$ST_Obj = $GeneralObj->DataArray;
}
echo '<complete>
	<option value="" selected="selected">Select Location</option>';
if($ST_Obj){
	foreach($ST_Obj as $rw){
		echo '<option value="'.$rw->LC_Id.'">'.str_replace("&","&amp;",$rw->LC_Name).'</option>';
	}
}
echo '</complete>';
?>