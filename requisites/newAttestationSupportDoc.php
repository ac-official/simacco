<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
    <item type="hidden" name="ASD_Id" value="0"/>
    
    <item type="input" name="ASD_Document" label="Document" offsetTop="30" required="true" validate="^[a-zA-Z0-9 ]+$">
        <note width="150">Document</note>
    </item>

    <item type="combo" label="Status" name="ASD_Status" readonly="true">
        <option value="1" label="Published" selected="true" />
        <option value="0" label="Blocked" selected="false" />
        <note width="150">Publish Status</note>
    </item>

    <item type="block" width="300" offsetTop="50">
        <item type="button" value="Save" name="newDocumentValidate"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newDocumentCancel"/>
    </item>
</items>';