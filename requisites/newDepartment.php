<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="DP_Id" value="0"/>
                <item type="hidden" name="OF_Id" value="'.$preTally_user_ofid.'"></item>
                    
		<item type="input" name="DP_Name" label="Title" value="" offsetTop="30" required="true" validate="^[a-zA-Z0-9 ]+$">
			<note width="150">Department Title</note>
		</item>
		
		<item type="input" name="DP_Comments" label="Remarks" value="" rows="3">
			<note width="150">Remarks</note>
		</item>
		<item type="combo" label="Weekend Off Days" name="DH_Weekends" readonly="true"  required="true" comboType="checkbox" >                        			
                        <option value="1" label="Monday" selected="false" />
			<option value="2" label="Tuesday" selected="false" />    
                        <option value="3" label="Wednesday" selected="false" />
			<option value="4" label="Thursday" selected="false" />
                        <option value="5" label="Friday" selected="false" />
			<option value="6" label="Saturday" selected="false" />
                        <option value="7" label="Sunday" selected="false" />
                    <note width="150">Weekend Days</note>
		</item>  
		<item type="combo" label="Status" name="DP_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="hidden" name="H_Weekend" value=""/>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newDepartmentValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newDepartmentCancel"/>
		</item>
		
	</items>';
?>