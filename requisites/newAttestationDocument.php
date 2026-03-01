<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="180" noteWidth="180" offsetLeft="20"/>
    <item type="hidden" name="ADOC_Id" value="0"/>
    
    <item type="combo" label="Document Type" name="ADOC_Type" readonly="true" offsetTop="30" >
        <option value="1" label="Education" selected="true" />
        <option value="2" label="Non-Education" selected="false" />
        <option value="3" label="Commercial" selected="false" />
        <option value="4" label="Passport" selected="false" />
        <option value="5" label="Registration certification for renewal" selected="false" />
        <option value="6" label="PCC Certificate" selected="false" />
        <option value="7" label="Courier" selected="false" />
        <note width="150">Type of Documents</note>
    </item>

    <item type="input" name="ADOC_Document" label="Document" required="true" validate="^[a-zA-Z0-9 ]+$">
        <note width="150">Document</note>
    </item>

    <item type="combo" label="Status" name="ADOC_Status" readonly="true">
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