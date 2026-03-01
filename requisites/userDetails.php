<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$attObj = new AttendanceClass();
$key  = $_REQUEST["mask"]; 
if($key)  {$filtr = " AND CONCAT(US.US_FName,' ', US.US_LName) LIKE '".$key."%'";}
$attObj->listUser($preTally_user_ofid,$filtr);
$attObj = $attObj->listUserArray;
echo '<complete>';
if($attObj){      
    foreach($attObj as $rw) {
        echo '<option value="'.$rw->US_Id.'" >'.$rw->Name.' </option>';
    }
}else{
     echo '<option value=" " selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>