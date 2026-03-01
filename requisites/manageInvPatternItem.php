<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="150" noteWidth="125" offsetLeft="20" offsetTop="25"/> 
            <item type="combo" label="Inventory Type" name="InvTypeCombo"/>
		<item type="newcolumn"/>
                <item type="button" value="Save" name="newInvItemSave"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="newInvItemCancel"/>
		
		
	</items>';
?>