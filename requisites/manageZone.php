<?php
require_once($BASEPATH . 'includes/sessions.php');

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		          
                <item type="hidden" name="ZN_Id" value="0"/>
                <item type="hidden" name="LC_Id" value="0"/>
                <item type="input" name="ZN_Name" label="Zone Name" value="" offsetTop="30" required="true" validate="^[A-Za-z][A-Za-z0-9 .]*$">
			<note width="150">Zone Name</note>
		</item>
                <item type="combo" name="LC_IdCombo" label="Branch Name" value="" comboType="checkbox" connector="requisites/locations.php&amp;filter=CHK" filtering = "true"  required="true" >
			<note width="150">Branches</note>
		</item> 
		<item type="combo" label="Status" name="ZN_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		
		<item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newZoneValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newZoneCancel"/>
		</item>
		
	</items>';
?>