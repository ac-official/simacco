<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");
$HI_ItemObj    = new HistoryClass();
$filter = " IT.OF_Id = ".$preTally_user_ofid." AND IT_Status!=4";



$HI_ItemObj->allOfzItems($filter);    
$HI_ITObj = $HI_ItemObj->HistoryArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
echo '<option value="0" selected="true">All</option>';
if($HI_ITObj){
    foreach($HI_ITObj as $rw){   
        if($rw->IT_Name)
        echo '<option value="'.$rw->IT_Id.'">'.str_replace("&","&amp;",$rw->IT_Name).'</option>';
    }
}
echo '</complete>';
?>