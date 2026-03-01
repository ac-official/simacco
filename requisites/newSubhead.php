<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/MainheadClass.php");

$MainheadObj = new MainheadClass();
$MainheadObj->viewMainheads(' WHERE MH_Status = 1 ORDER BY MH_Name');
$MH_Obj = $MainheadObj->MainheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="SH_Id" value="0"/>
		<item type="input" name="SH_Name" label="Title" value="" offsetTop="30" required="true" >
			<note width="150">Subhead Title</note>
		</item>
                
                <item type="combo" label="Mainhead" name="MH_Id" required="true" validate="NotEmpty" readonly="true">
                    <option value="" label="Select Mainhead" selected="true" />';
                    if($MH_Obj) {
                        foreach($MH_Obj as $rwMHObj) {
                            echo '<option value="'.$rwMHObj->MH_Id.'" label="'.$rwMHObj->MH_Name.'" selected="false" />';
                        }
                    }
                    echo '<note width="150">Mainhead Name</note>
		</item>
		
		<item type="input" name="SH_Comments" label="Remarks" value="" rows="3">
			<note width="150">Remarks</note>
		</item>
		
		<item type="combo" label="Status" name="SH_Status" readonly="true">
			<option value="1" label="Published" selected="true" />
			<option value="0" label="Blocked" selected="false" />
			<note width="150">Publish Status</note>
		</item>
		<item type="checkbox" name="SH_Track"  position="label-left" label="Track By Id"></item>
		<item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newSubheadValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newSubheadCancel"/>
		</item>
		
	</items>';
?>