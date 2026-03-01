<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();
$GeneralObj->ViewDetails(' * ', 'countries', ' 1 ',' CN_Name ');
$CN_Obj = $GeneralObj->DataArray;

echo '<complete>';
if($REQUEST['form'] != 'automate')echo '<option value="0" img="globe.png" selected="true">Select Country</option>';
else echo '<option value="1000">General</option>';
if($CN_Obj){
    foreach($CN_Obj as $rw) {
        $selected = '';
//        if($rw->CN_Id == 90) $selected = ' selected="true" ';
        echo '<option value="'.$rw->CN_Id.'" img= "'.$rw->CN_Flag.'" '.$selected.'>'.str_replace("&","&amp;",$rw->CN_Name).'</option>';
    }
}
echo '</complete>';
?>