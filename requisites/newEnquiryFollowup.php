<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
        
            <item type="hidden" value="'.$REQUEST['AEId'].'" name="AE_Id"/>
            <item type="block"  width="800" offsetTop="20">
            
                <item type="input" name="EF_Comments" label="Remarks" value="" rows="3" validate="NotEmpty" required="true">
                        <note width="150">Remarks</note>
                </item>

                <item type="newcolumn"/>

                <item type="calendar" name="EF_NextDate" label="Next Followup Date" readonly="true" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" position="label-left" >
                    <note width="150">Next Followup Date</note>
                </item>

                <item type="combo" label="Chance of getting Job" name="EF_Chance" readonly="true" validate="NotEmpty" required="true" >
                        <option value="1" label="Low" selected="true" />
                        <option value="2" label="Normal" />
                        <option value="3" label="High" />
                        <option value="4" label="Very High" />
                        <note>Select Priority</note>
                </item>
            </item>
            
            <item type="block" width="300" offsetTop="10" offsetLeft="300">
                    <item type="button" value="Save" name="newFollowupValidate"/>
                    <item type="newcolumn"/>
                    <item type="button" value="Cancel" name="newFollowupCancel"/>
            </item>
		
	</items>';
?>