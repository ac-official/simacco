<?php
$ajax = 'true';
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$OffObj = new OfficeClass();
$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
$AttObj = new AttendanceClass();
$check =$AttObj->checkAttendance($preTally_user_id);
   $disabled="false";
//echo $offAdm.'--'.$preTally_user_id;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

//<option value="0" label="Select Mainhead Type" selected="true" />
echo '<items>
	<item type="settings" position="label-left" labelWidth="0" inputWidth="0" noteWidth="0" offsetLeft="250"/>
        
             <item type="fieldset" width="325" offsetTop="5" label="Mark Your Attendance" >
        <item type="hidden" value="1" name="attnd" />';
//$In_Type=$Out_type="hidden";
//if($check==0) $In_type="button";
//else $Out_type="button";
   
                           echo '<item type="button" value="Mark SignIn" name="MarkSignIn" className="attendmark" offsetLeft="10"/>';


    			echo '<item type="button" value="Mark SignOut" name="MarkSignOut" className="attendmark" offsetLeft="10" />';


                echo '</item>
       </items>';
?>