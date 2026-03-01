<?php

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");

$BalSheetUpdateObj = new BalanceSheetClass();

//print_r($_REQUEST);
$fields=str_replace("\\","",$_REQUEST['db_fields']);

$BS_Id          = $_REQUEST['gr_id'];
$BS_Date = implode("-", array_reverse(explode("/", $_REQUEST['BS_Date']))); 
$BS_PaidDate = implode("-", array_reverse(explode("/", $_REQUEST['BS_PaidDate']))); 
$db_fields      = unserialize($fields) ;
$fieldValues    = $_REQUEST;


$patternValues  = array_intersect_key($fieldValues, $db_fields);

$patternValues['BS_Complete'] = 0;
$patternValues['BS_Date'] = $BS_Date;
$patternValues['BS_PaidDate'] = $BS_PaidDate;

$BalSheetUpdateObj->BL_Update_Data = $patternValues;
$BalSheetUpdateObj->updateBalSheet(htmlspecialchars($BS_Id, ENT_QUOTES));


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo "<data>";
    echo "<action type='delete' sid='".$_REQUEST['gr_id']."' tid='0'/>";
echo "</data>";

?>