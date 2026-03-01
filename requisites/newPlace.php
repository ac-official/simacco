<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>        
                <item type="hidden" name="AP_Id" value="0"/>
                <item type="input" name="AP_Name" label="Name" value="" offsetTop="30" required="true" >
			<note width="150">Place Title</note>		</item>
                <item type="combo" name="CT_Id" label="City" value="" connector="requisites/cities.php" >
			<note width="150">City Title</note>
		</item>
		<item type="combo" label="Status" name="AP_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newPlaceValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newPlaceCancel"/>
		</item>
		
	</items>';
?>