<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");



require_once($BASEPATH . "preTallyClass/UserClass.php");
$LC_ID = $REQUEST["LC_Id"]; 
$key = $_REQUEST["mask"];
$LCUser_Obj = new UserClass();
$LCUser_Obj->viewOfficeStaffs("Where US_FName like '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($key))."%'  AND  LC_Id=".$LC_ID." AND US_Status != 5");
$LCUSObj = $LCUser_Obj->UserArray;

echo '<complete>';
echo '<option value="0" >All Branch Staff</option>';
if($LCUSObj){   
    foreach($LCUSObj as $rw){
            echo '<option value="'.$rw->US_Id.'" >'.$rw->US_FName.' '.$rw->US_LName.'</option>';
    }
}
echo '</complete>';
?>