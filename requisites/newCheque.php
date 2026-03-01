<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>		
		<item type="hidden" name="CHQ_Id" value="0"/>
		
                <item type="combo" label="Account" name="BA_Id" readonly="true"  required="true" connector="requisites/bankaccounts.php">
			<note width="150">Acccount Number</note>
		</item> 
                <item type="input" name="CHQ_BookNo" label="Cheque Book Number" value=""  required="true" validate="^\d+$">
			<note width="150">Cheque Book Number</note>
		</item>  
                <item type="input" name="CHQ_Firstleaf" label="Cheque Start Leaf" value=""  required="true" validate="^\d+$">
			<note width="150">Start Leaf Number</note>
		</item>  
                <item type="input" name="CHQ_Lastleaf" label="Cheque Last Leaf" value=""  required="true" validate="^\d+$">
			<note width="150">Last Leaf Number</note>
		</item>                  
                <item type="combo" label="Status" name="CHQ_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newChequeValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newChequeCancel"/>
		</item>		
	</items>';
?>