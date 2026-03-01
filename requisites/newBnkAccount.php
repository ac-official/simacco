<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20" />		

                <item type="hidden" name="BA_Id"  value="0" ></item>

                <item type="input" name="BA_No" label="Account Number" value=""  required="true"  offsetTop="30" validate="^\d+$">
			<note width="150">Account Number</note>
		</item> 
                <item type="input" label="Display Name" name="BA_DispName" required="true">			
			<note width="200">Name to identify account easily</note>
		</item>
                <item type="combo" label="Branch" name="LC_Id" readonly="true" required="true" connector="requisites/locations.php">
			<note width="150">Company Branch</note>
		</item>
                <item type="combo" label="Bank Branch" name="BB_Id" readonly="true"  required="true" connector="requisites/bankbranch.php">
			<note width="150">Bank Branch</note>
		</item>                                
                <item type="combo" label="Status" name="BA_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newBnkAccValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newBnkAccCancel"/>
		</item>		
	</items>';
?>