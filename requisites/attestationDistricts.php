<?php
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$AttObj = new AttestationClass();
if($_REQUEST['mask'])   // auto filtering
    $filter = " AND DT_Name LIKE '".$_REQUEST['mask']."%' ";
$AttObj->getDistrict($filter);
$DT_Obj = $AttObj->DataArray;
echo '<complete>';

if($DT_Obj){
    foreach($DT_Obj as $rw){
        if($REQUEST['DT_Id'] == $rw->DT_Id) 
            $selected = 'selected = "true"';
        else
            $selected= '';
        echo '<option '.$selected.' value="'.$rw->DT_Id.'" >'.$rw->DT_Name.' / '.$rw->ST_Name.' / '.$rw->CN_Name.'</option>';
    }
}
else 
    echo '<option value="" selected="true">No Records Found</option>'; 
echo '</complete>';