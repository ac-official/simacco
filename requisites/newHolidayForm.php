<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetTop="5" offsetLeft="20"/>
		
        <item type="hidden" name="HD_Id" value="0" />
        <item type="hidden" name="HD_Count" value="0"/>        
        <item type="hidden" name="HD_Batch" value="0"/>
        <item type="hidden" name="HD_Del" value="0"/>
        <item type="combo" name="HD_Type" label="Type" required="true" readonly="true" offsetTop="30">
            <option value="" label="Select" />
            <option value="1" label="National Holiday" />
            <option value="2" label="State Holiday" />
            <option value="3" label="Restricted Holiday" />
            <option value="4" label="Others" />
                <note width="150">Type of Holiday</note>
        </item>	
        <item type="input" name="HD_Comments" label="Title" required="true">
            <note width="150">Title</note>
        </item>	
        <item type="calendar" name="HD_Date_0" label="Date" value=""  required="true"  readonly="true">
            <note width="150">Holiday Date</note>
        </item>
        <item type="hidden" name="HD_hid_0" value="0"/>
        
        <item type="button" value="Add" name="AddDays" hidden="true" offsetLeft="275"/>
        <item type="combo" name="States" comboType="checkbox" label="State" connector="requisites/stateCombo.php" required="true" readonly="true">
            <note width="150">Select States</note>
        </item>

        <item type="combo" name="DP_Id" label="Department" connector="requisites/departments.php&amp;type=filt" className="deptCombo">
                <note width="150">Only for this department </note>
        </item>

        <item type="combo" label="Status" name="HD_Status" readonly="true">
            <option value="1" label="Published" selected="true" />
            <option value="0" label="Blocked" selected="false" />
            <note width="150">Publish Status</note>
        </item>
        <item type="hidden" name="StateList"></item>

        <item type="block" width="300" offsetTop="50">
            <item type="button" value="Save" name="SaveHoliday"/>
            <item type="newcolumn"/>
            <item type="button" value="Cancel" name="CancelHoliday"/>
        </item>
</items>';
?>