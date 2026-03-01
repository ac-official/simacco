<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH ."preTallyClass/UserClass.php");

$UserObj = new UserClass();
$UserObj->viewSalPayModes(" WHERE SP_Status = 1");
$USP_Obj = $UserObj->SalPayArray;

echo '<complete >';
if($USP_Obj){
    //if($REQUEST['r'] == '' || $REQUEST['r'] == 0) echo '<option value="0" selected="true">Select Payment Mode</option>';
    echo '<option value="0" ';
    if($REQUEST['r'] == '' || $REQUEST['r'] == 0) 
        echo ' selected="true" ';
    echo '>Select Payment Mode</option>';
    foreach($USP_Obj as $rw) {
        $selected = '';
        if($REQUEST['r'] == $rw->SP_Id) $selected = 'selected="true"';
        echo '<option value="'.$rw->SP_Id.'" '.$selected.' >'.$rw->SP_Name.'</option>';
    }
}
echo '</complete>';
?>