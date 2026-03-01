<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/LocationClass.php");
$NewLocationObj = new LocationClass();

if($preTally_user_ofid == 1) {
    $filter   =   'ORDER BY OF_Name';
}else {
    $filter   =   'WHERE OF_Id = '.$preTally_user_ofid.' ORDER BY OF_Name';
}
$NewLocationObj->getOffices($filter);
$NLC_Obj = $NewLocationObj->NewLocationArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="LC_Id" value="0"/>
		<item type="input" name="LC_Name" label="Title" value="" offsetTop="30" required="true" validate="^[a-zA-Z ]+$">
			<note width="150">Location Title</note>
		</item>
        
		<item type="combo" name="OF_Id" label="Office" readonly="true" required="true">
                <option value = "" label="Select Office" selected="selected"/>';
                if($NLC_Obj) {
				$j = 1;
				foreach($NLC_Obj as $rw) {
					echo '<option value="'.$rw->OF_Id.'" label="'.$rw->OF_Name.'" selected="selected"/>';
                                }
                }
                	echo '<note width="150">Location Office</note>
		</item>         
                <item type="input" name="LC_Building" label="Building Name/Number" required="true">
			<note width="150">Building Name/Number</note>
		</item>
                <item type="input" name="GOGL_PlaceSearch" label="Landmark"  >
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
                <item type="input" name="LC_Phone" label="Phone Number" value="" validate="ValidNumeric" required="true">
			<note width="150">Location Phone Number</note>
		</item>
                <item type="input" name="LC_Comments" label="Remarks"  rows="2" >
			<note width="150">Remarks</note>
		</item>
		
		<item type="combo" label="Status" name="LC_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Suspended" selected="false" />
                        <option value="2" label="Permanently Closed" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		
		<item type="block" width="300" offsetTop="10">
			<item type="button" value="Save" name="newLocationValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newLocationCancel"/>
		</item>
		
	</items>';
?>