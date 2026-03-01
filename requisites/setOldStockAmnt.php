<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/UserClass.php");


//$LCId = $REQUEST['LCId'];
//$TR_Track = $REQUEST['TR_Track'];
$OSId = $REQUEST['OSId'];
//$UserObj  = new UserClass();
//$UserObj->viewOfficeStaffs(' WHERE LC_Id='.$LCId.' ORDER BY US_FName');
//$US_Obj = $UserObj->UserArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="160" inputWidth="150" noteWidth="150" offsetLeft="20" offsetTop="10"/>
        
        <item type="hidden" name="LC_Id" />
        <item type="hidden" name="LC_Name" />
        <item name = "OS_Id" type = "hidden" value = "'.$OSId.'" />
        <item name = "OS_Status" type = "hidden"  value = "0" />
                
        <item type="calendar" name="OS_Date" label="Date  :"  required="true" readonly="true">
            <note width="150">Date</note>
        </item>
        <item type="input" name="OS_OpenBal" label="Old Stock Amount  :" required="true"  validate="ValidNumeric" >
            <note width="150">Old Stock Amount</note>
        </item>        
        <item type="block" width="300" offsetTop="5">
                <item type="button" value="Save" name="SaveOldStkAmt"/>
                <item type="newcolumn"/>
                <item type="button" value="Cancel" name="CancelOldStkAmt"/>
        </item>
       </items>';
?>