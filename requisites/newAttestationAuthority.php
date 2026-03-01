<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
    <item type="hidden" name="AAUTH_Id" value="0"/>
    <item type="hidden" name="AAUTH_ST_Id" value="0"/>
    <item type="hidden" name="AST_CS" value=""/>
    <item type="block" width="450"  >
        <item type="input" name="AAUTH_Authority" label="Authority" offsetTop="30" required="true" >
            <note width="150">University / Board / Council</note>
        </item>

        <item type="combo" name="ST_Id" label="State / Country" required="true" className="AAUTH_StateCombo" filterCache="true" validate="^[a-zA-Z0-9 ]+$" >
            <note width="150">State / Country</note>
        </item>
    </item>
    <item type="block" width="450" >
        <item type="radio" name="AST_CS" label="State" value="S" labelWidth="40" checked="true"></item>
        <item type="newcolumn" />  
        <item type="radio" name="AST_CS" label="Country" value="C" labelWidth="50" ></item>
    </item>
    <item type="block" width="450" >
        <item type="combo" label="Status" name="AAUTH_Status" readonly="true">
            <option value="1" label="Published" selected="true" />
            <option value="0" label="Blocked" selected="false" />
            <note width="150">Publish Status</note>
        </item>
    </item>
    <item type="block" width="300" offsetTop="30" offsetLeft="45">
        <item type="button" value="Save" name="newAuthorityValidate"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newAuthorityCancel"/>
    </item>
</items>';