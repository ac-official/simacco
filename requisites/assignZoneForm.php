<?php
require_once($BASEPATH . 'includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		          
                <item type="hidden" name="US_Id" value="0"/>
                <item type="hidden" name="ZN_Id" value="0"/>
                <item type="template" name="US_Name" label=" User Name" value="" offsetTop="30">
			<note width="150">User Name</note>
		</item>
                <item type="combo" name="ZN_IdCombo" label="Zones" value="" comboType="checkbox">
			<note width="150">Zones</note>
		</item> 		
		
		<item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newAssignZoneValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newAssignZoneCancel"/>
		</item>
		
	</items>';
?>