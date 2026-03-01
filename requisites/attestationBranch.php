<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$filter = "";
if($_REQUEST['mask'])
    $filter = " AND LC_Name LIKE '".$_REQUEST['mask']."%' ";
$AttObj -> getDetails(' locations', '*', ' WHERE LC_Status = 1 AND OF_Id = '.$preTally_user_ofid .' '.$filter.' ORDER BY LC_Name');
$City_Obj = $AttObj->DataArray;
echo '<complete >';
if($City_Obj){      
    foreach($City_Obj as $rw) {
        echo '<option value="'.$rw->LC_Id.'" >'.$rw->LC_Name.'</option>';
    }
}
else {  
    echo '<option value="0" selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>
