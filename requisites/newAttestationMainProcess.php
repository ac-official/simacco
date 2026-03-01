<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
    <item type="hidden" name="APM_Id" value="0"/>
    <item type="input" name="APM_Title" label="Main Process" offsetTop="20" required="true" validate="^[a-zA-Z/ ]+$">
        <note width="150">Main Process</note>
    </item>
    <item type="input" name="APM_Title_Alias" label="Alias Name" required="true" validate="^[a-zA-Z/ ]+$">
        <note width="150">Alias Name</note>
    </item>
    <item type="combo" name="APMA_Title" label="Authority"  required="true" offsetTop="20" className="APM_APMA_Combo" filterCache="true">
        <note width="150">Certificate Processing Authority</note>
    </item>
    <item type="hidden" name="APMA_Id" value="0"/>
    <item type="input" name="APM_Description" label="Description" rows="3">
        <note width="150">Description</note>
    </item>
    <item type="combo" label="Status" name="APM_Status" readonly="true" required="true">
        <option value="1" label="Published" selected="true" />
        <option value="0" label="Blocked" selected="false" />
        <note width="150">Publish Status</note>
    </item>
    <item type="block" width="300" offsetTop="50">
        <item type="button" value="Save" name="newMainProcessValidate"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newMainProcessCancel"/>
    </item>
</items>';