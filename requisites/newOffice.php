<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="OF_Id" value="0"/>
                <item type="hidden" name="US_Id" value="0"/>
		<item type="input" name="OF_Name" label="Title" value="" offsetTop="30" required="true">
			<note width="150">Company Title</note>
		</item>
		
                <item type="input" name="US_Email" label="Company Email" value="" required="true">
                        <note width="150">Company Email</note>
                </item>
                <item type="template" name="US_Emailtemp" label="Company Email">
                        <note width="150">Company Email</note>
                </item>
                <item type="input" name="US_FName" label="First Name" value="" required="true" validate="^[a-zA-Z]+$">
                        <note width="150">First Name</note>
                </item>
                <item type="input" name="US_LName" label="Last Name" value="" required="true"  validate="^[a-zA-Z]+$">
                        <note width="150">Last Name</note>
                </item>
                
               <item type="input" name="OF_Building" label="Building Name/Number" required="true">
			<note width="150">Building Name/Number</note>
		</item>
                <item type="input" name="GOGL_OFPlaceSearch" label="Landmark"  >
			<note width="150">Search Location</note>
		</item>	
                <item type="input" name="GOGL_Street" label="Street" required="true" >
			<note width="150">Street</note>
		</item>	
                <item type="input" name="GOGL_Place" label="Place"  required="true">
			<note width="150">Place</note>
		</item>	
                <item type="input" name="GOGL_Location" label="Location"  required="true">
			<note width="150">Location</note>
		</item>	
                <item type="input" name="GOGL_City" label="City"  required="true">
			<note width="150">City</note>
		</item>	
                <item type="input" name="GOGL_State" label="State"  required="true">
			<note width="150">State</note>
		</item>	
                <item type="input" name="GOGL_Country" label="Country"  required="true">
			<note width="150">Country</note>
		</item>	                
                <item type="input" name="GOGL_Pincode" label="Pin Code"  required="true">
			<note width="150">PinCode</note>
		</item>	
                
                <item type="combo" name="CR_Id" label="Currency" value="" connector="requisites/currencies.php" required="true" readonly = "true" >
			<note width="150">Currency</note>
		</item>
                
                <item type="combo" name="TZ_Id" label="Time zone" value="" connector="requisites/time_zones.php" required="true" readonly = "true" >
			<note width="150">Time Zone</note>
		</item>
                
                <item type="input" name="OF_Comments" label="Remarks" value="" rows="2" >
			<note width="150">Remarks</note>
		</item>
				
		<item type="combo" label="Status" name="OF_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		
		<item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newOfficeValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newOfficeCancel"/>
		</item>
		
	</items>';
?>