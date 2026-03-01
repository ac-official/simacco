<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
    <item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20" />
    
    <item type="hidden" name="AJB_Id" value="0"/>
    <item type="hidden" name="AJB_IT_Id" value="0"/>
    <item type="hidden" name="AJB_Item_Name" value="0"/>
    <item type="hidden" name="AJBS_Type" value="0"/>
    
    <item type="block" width="450" >
        <item type="input" name="AJB_Name" label="Balance Sheet Settings" offsetTop="30" required="true" disabled="true" readonly="true">
            <note width="150">Balance Sheet Settings</note>
        </item>
        
        <item type="combo" label="Entry Type" name="MH_Type" readonly="true" required="true" disabled="true">
            <option value="" label="Select Entry Type" selected = "true"/>
            <option value="1" label="Income" />
            <option value="2" label="Expense" />
            <note width="150">Entry Type</note>
        </item>
        
        <item type="combo" name="AJB_IT" label="Item" required="true" className="AJB_ItemCombo" filterCache="true" validate="^[a-zA-Z0-9 ]+$" disabled="true">
            <option value="0" label="Select Item" />
            <note width="150">Item</note>
        </item>
    </item>
    
    <item type="block" width="300" offsetTop="30" offsetLeft="45">
        <item type="button" value="Save" name="newBalSheetSettingsValidate" disabled="true"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="newBalSheetSettingsCancel" disabled="true"/>
    </item>
</items>';