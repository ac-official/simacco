<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$UserObj = new UserClass();
$UserObj->viewBloodGroups();
$UBG_Obj = $UserObj->BloodArray;

echo '<complete >';
if($UBG_Obj){
    if($REQUEST['r'] == '')  echo '<option value="0" selected="true">Select Blood Group</option>';
    foreach($UBG_Obj as $rw) {
        $selected = '';
        if($REQUEST['r'] == $rw->BG_Id) $selected = 'selected="true"';
        echo '<option value="'.$rw->BG_Id.'" '.$selected.' >'.$rw->BG_Name.'</option>';
    }
}
echo '</complete>';
?>