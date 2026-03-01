<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();

$key = $_REQUEST["mask"]; 

$GeneralObj->getComboDetails(' ST_Id,ST_Name,CN_Name ', 'states as ST , countries as CN',"ST_Name like '".ucfirst($key)."%' AND ST.CN_Id=CN.CN_Id",' ST_Name ');
$CN_Obj = $GeneralObj->DataArray;

echo '<complete>';
if($CN_Obj){
       
	foreach($CN_Obj as $rw)
        {
		echo '<option value="'.$rw->ST_Id.'">'.str_replace("&","&amp;",$rw->ST_Name).'  ('.str_replace("&","&amp;",$rw->CN_Name).')'.'</option>';
	}
}else { echo '<option value="ZeroVal" selected="true">No Records Found</option>'; }
echo '</complete>';
?>

