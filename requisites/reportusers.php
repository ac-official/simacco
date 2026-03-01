<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$locateid   = $_GET['lcid'];
$userid     = $_GET['user_id'];
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once("../preTallyClass/UserClass.php");
$UserObj = new UserClass();
if($locateid!=0) {
    $UserObj->viewReportingUsers($locateid,$userid);
    $US_Obj = $UserObj->DataArray;
}
echo '<complete>';
echo '<option value="0" selected="true">Select User</option>';	
if($US_Obj){
    foreach($US_Obj as $rw){
        echo '<option value="'.$rw->US_Id.'">'.$rw->US_FName.' '.$rw->US_LName.'</option>';
    }
}
echo '</complete>';
?>