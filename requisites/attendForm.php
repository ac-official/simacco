<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/OfficeClass.php");
$OffObj = new OfficeClass();
$offAdm = $OffObj->offzAdmin($preTally_user_ofid);

//echo $offAdm.'--'.$preTally_user_id;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

//<option value="0" label="Select Mainhead Type" selected="true" />
echo '<items>
	<item type="settings" position="label-left" labelWidth="0" inputWidth="0" noteWidth="0" offsetLeft="5"/>
        
             <item type="fieldset" width="325" offsetTop="5" label="Mark Your Attendance" >
        <item type="block" width="250" offsetTop="5">
        		<item type="hidden" value="0" name="attnd" />
			<item type="button" value="Mark" name="MarkAttend" className="attendmark"/>
			<item type="newcolumn"/>
			<item type="button" value="Skip" name="SkipAttend" className="attendskip"/>
		</item>
                </item>
       </items>';
?>