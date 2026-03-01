<?php
error_reporting(E_ALL ^ E_NOTICE);

$ajax = 'true';
require_once('../includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

include_once("../preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();

$key = $_GET["mask"]; 

$GeneralObj->getComboDetails(' * ', 'countries',"CN_Name like '".ucfirst($key)."%'",' CN_Name ');
$CN_Obj = $GeneralObj->DataArray;

echo '<complete>';
if($CN_Obj){
       
	foreach($CN_Obj as $rw)
        {
		echo '<option value="'.$rw->CN_Id.'">'.str_replace("&","&amp;",$rw->CN_Name).'</option>';
	}
}
echo '</complete>';
?>


<?php
/*error_reporting(E_ALL ^ E_NOTICE);

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

include_once("../preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();

$key = $_GET["mask"]; 

$GeneralObj->getComboDetails(' * ', 'countries',"CN_Name like '".ucfirst($key)."%'",' CN_Name ');
$CN_Obj = $GeneralObj->DataArray;

echo '<complete>';
if($CN_Obj){
       
	foreach($CN_Obj as $rw)
        {
                        echo $rw[0];
		echo '<option value="'.$rw[0].'">'.str_replace("&","&amp;",$rw[1]).'</option>';
	}
}
echo '</complete>';*/
?>