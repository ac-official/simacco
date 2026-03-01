<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

include_once($BASEPATH . "preTallyClass/LocationClass.php");

$LocationObj = new LocationClass();
$LocationObj->getCurrencies();
$LC_Obj = $LocationObj->LocationArray;

echo '<complete>';
echo '<option value="" selected="true">Select Currency</option>';
if($LC_Obj){
    foreach($LC_Obj as $rw) {
        $selected = '';
//        if($rw->CN_Id == 90) $selected = ' selected="true" ';
        echo '<option value="'.$rw->CR_Id.'" '.$selected.'>'.str_replace("&","&amp;",$rw->CR_Name).'</option>';
    }
}
echo '</complete>';
?>