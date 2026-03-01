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
    $filter = " AND CT_Name LIKE '".$_REQUEST['mask']."%' ";
$AttObj->getDetails('cities', '*', ' WHERE CT_Status = 1 '.$filter.' ORDER BY CT_Name');
$City_Obj = $AttObj->DataArray;
echo '<complete >';
if($City_Obj){      
    foreach($City_Obj as $rw) {
        if($REQUEST['CT_Id'] == $rw->CT_Id) 
            $selected = 'selected = "true"';
        else
            $selected= '';
        echo '<option value="'.$rw->CT_Id.'" '.$selected.'>'.$rw->CT_Name.'</option>';
    }
}
echo '</complete>';
?>
