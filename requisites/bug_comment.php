<?php
//date_default_timezone_set('Asia/Kolkata');
$bid=$REQUEST['bgid'];
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="100" inputWidth="400" noteWidth="180" offsetLeft="40" offsetTop="30"/>
        <item type="combo" label="Status" name="BR_Status" inputWidth="500" required="true"  validate="NotEmpty" readonly="true"> 
                <option value="1" label="New" />
                <option value="2" label="Read" />
                <option value="3" label="UnderProcess" />
                <option value="4" label="Completed" />
                <note width="50">Status</note>
        </item>
        <item type="combo" label="Assigned To" name="BR_AssignedUsId" inputWidth="500" serverFiltering="requisites/BugRptEmp.php" filterCashe="true"> 
               
        </item>
        <item type="input" name="BR_Comment" label="Comment" value="" rows="9" required="true" width="500">
	<note width="150">Comments on Bug Report</note>
	</item>
        <item type="hidden" name="BR_Id" label="Comment" value="'.$bid.'" />
        <item type="block" width="300" offsetTop="0" offsetLeft="400">
		<item type="button" value="Save"  offsetTop="0" offsetLeft="0" name="newCommentSave"/>
		<item type="newcolumn"/>
		<item type="button" value="Cancel" offsetTop="0" offsetLeft="0" name="newCommentCancel"/>
	</item>
    </items>';
    ?>