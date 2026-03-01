<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
 $LeaveObj = new LeaveClass();
 $LeaveObj->getLeaveType($preTally_user_ofid );
 $aObj=$LeaveObj->leaveTypeArray; 
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
echo '<option value=""  selected="true">All</option>'; 

 foreach($aObj as $rw) {    
            echo '<option value="'.$rw->LT_Id.'"  '.$selected.'>'.str_replace("&","&amp;",$rw->LT_Name).'</option>';
     
}
echo '</complete>';

?>