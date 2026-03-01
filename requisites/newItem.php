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
	<item type="settings" position="label-left" labelWidth="120" inputWidth="200" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="IT_Id" value="0"/>
                <item type="hidden" name="IT_SpId" value="0"/>
                    <item type="input" name="IT_Name" label="Item Name" value="" offsetTop="30" required="true" >
                            <note width="150">Item Title</note>
		</item>
                <item type="combo" label="Type of Entry" name="MH_Type" readonly="true" validate="NotEmpty" required="true" >
                        <option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" />
			<note>Select Income / Expense</note>
		</item>' ;
                if($offAdm == $preTally_user_id) { $validate = ' required="true" '; }  
                echo '<item type="combo" label="Subhead" name="SH_Id" '.$validate.'>
                    <note width="150">Subhead Name</note>
		</item>
		
		<item type="input" name="IT_Comments" label="Remarks" value="" rows="3">
			<note width="150">Remarks</note>
		</item>';
		
                if($offAdm == $preTally_user_id) {
                    echo '<item type="combo" label="Status" name="IT_Status" readonly="true">
                            <option value="1" label="Published" selected="true" />
                            <option value="0" label="Blocked" selected="false" />
                            <note width="150">Publish Status</note>
                    </item>
                    <item type="checkbox" name="IT_Business" position="label-left"  label="Business Entry"></item>
                     <item type="newcolumn"></item>
                    <item type="checkbox" name="IT_Transfers" position="label-left"  label="Branch loan / Bank Deposit"></item>';
                } else {
                    echo '<item type="hidden" name="IT_Status" value="0"/><item type="hidden" name="IT_Business" value="0"/>';
                }
		
                echo '
                <item type="block" width="300" offsetTop="50">
			<item type="button" value="Save" name="newItemValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newItemCancel"/>
		</item>
		
	</items>';
?>