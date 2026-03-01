<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="MH_Id" value="0"/>
		<item type="input" name="MH_Name" label="Title" value="" offsetTop="30" required="true" >
			<note width="150">Main Head Title</note>
		</item>
		
		<item type="input" name="MH_Comments" label="Remarks" value="" rows="3">
			<note width="150">Remarks</note>
		</item>
		
                <item type="combo" label="Type" name="MH_Type" readonly="true">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note width="150">Select Type</note>
		</item>
                
		<item type="combo" label="Status" name="MH_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newMainheadValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newMainheadCancel"/>
		</item>
		
	</items>';
?>