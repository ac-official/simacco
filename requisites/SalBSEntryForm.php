<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="150" noteWidth="150" offsetLeft="20"/>';
       if($REQUEST['frmtype']==1){
        echo '<item type="combo" name="BA_Id" label="Bank Account"  connector="requisites/bankaccounts.php" validate="NotEmpty" readonly="true" required="true">
			<note width="150">Choose Bank Account</note>
        </item>        
        <item type="input" name="Ref_text" label="Reference/Remarks" height="500" rows="3">            
        </item>';
       }else if($REQUEST['frmtype']==2){
        echo '<item type="combo" name="Cr_Loc" label="Created From"  connector="requisites/locations.php" validate="NotEmpty" readonly="true" required="true">
			<note width="150">Choose Bank Account</note>
        </item>
        <item type="combo" name="Paid_Loc" label="Paid For"  connector="requisites/locations.php" validate="NotEmpty" readonly="true" required="true">
			<note width="150">Choose Bank Account</note>
        </item>';
       }
        echo '<item type="input" name="total_amt" label="Total Amount" readonly="true" ></item>
            <item type="block" width="300" offsetTop="5">
			<item type="button" value="Save" name="createBSEntry"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="cancelBSEntry"/>
        </item>
        <item type="hidden" name="payType" value="0"/>
        <item type="hidden" name="rowsId"/>
        <item type="hidden" name="attMonth" value="0"/>
        <item type="hidden" name="attYear" value="0"/>
       </items>';

  
?>
