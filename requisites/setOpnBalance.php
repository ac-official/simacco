<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
$OffObj = new OfficeClass();
$offAdm = $OffObj->offzAdmin($preTally_user_ofid);

//echo $offAdm.'--'.$preTally_user_id;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

//<option value="0" label="Select Mainhead Type" selected="true" />
echo '<items>
	<item type="settings" position="label-left" labelWidth="200" inputWidth="200" noteWidth="150" offsetLeft="50" />
        <item type="hidden" name="OB_Id" value="0"/>
        <item type="calendar" name="OB_Date" label="Date"  required="true" readonly="true" offsetTop="30">
            <note width="150">Date</note>
        </item>
        <item type="input" name="OB_OpenBal" label="Opening Balance" required="true"  validate="ValidNumeric" >
            <note width="150">Opening Balance</note>
        </item>        
        <item type="block" width="300" offsetTop="5" offsetLeft="10">
			<item type="button" value="Save" name="SaveOpnBalance"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="CancelOpnBalance" offsetLeft="5"/>
		</item>
       </items>';
?>