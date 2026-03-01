<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
$LeaveObj = new LeaveClass();
$LeaveObj->getLeaveType($preTally_user_ofid );
$type = $REQUEST['cType'];
$aObj = $LeaveObj->leaveTypeArray; 
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
//echo '<option selected="true">Select Leave Type</option>'; 
echo '<option value="Pre">Present</option><option value="Hlf">Half Day</option>'; 
if($type == "RH") {
    echo '<option value="RH">RH</option>';
} else {
    echo '<option value="Abs">Absent</option>';
}
foreach($aObj as $rw) {    
   echo '<option value="'.$rw->LT_Id.'"  '.$selected.'>'.str_replace("&","&amp;",$rw->LT_Name).'</option>';     
} 
echo '</complete>';

?>