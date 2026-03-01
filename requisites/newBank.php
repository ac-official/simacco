<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>		
		<item type="hidden" name="BNK_Id" value="0"/>
		<item type="input" name="BNK_Name" label="Title" value="" offsetTop="30" required="true">
			<note width="150">Bank Name</note>
		</item>
                <item type="combo" label="Status" name="BNK_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newBankValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newBankCancel"/>
		</item>		
	</items>';
?>