<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>		
		<item type="hidden" name="BB_Id" value="0"/>
		<item type="input" name="BB_Name" label="Branch Name" value="" offsetTop="30" required="true" >
			<note width="150">Bank Branch Name</note>
		</item>
                
                <item type="combo" label="Bank Name" name="BNK_Id" readonly="true" connector="requisites/banks.php" required="true">
			<note width="150">Bank Name</note>
		</item>                 
                <item type="input" label="Address" name="BB_Address" rows="3">			
			<note width="150">Address</note>
		</item>
                <item type="input" label="Remarks" name="BB_Comments" rows="3">			
			<note width="150">Remarks</note>
		</item>
                <item type="combo" label="Status" name="BB_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newBranchValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newBranchCancel"/>
		</item>		
	</items>';
?>