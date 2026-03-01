<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj             = new AttestationClass();

$AJR_Details = $AttObj->getRowDetails('attestation_job_receipts', 'AJR_DRId,AJR_CDate', " WHERE AJ_Id = ".$REQUEST['AJ_Id']." ORDER BY AJR_CDate ASC LIMIT 0,1");
if(!$AJR_Details['AJR_DRId']) { $label = "Confirm &amp; Save Job"; $jobStatus = 0; }
else { $label = "Confirm Payment"; $jobStatus = 1; }

$AJR_INId = $AttObj->getValue('attestation_job_invoice_receipts', 'AJIR_InvoiceNo', " WHERE AJ_Id = ".$REQUEST['AJ_Id']);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
	<item type="settings" position="label-left" labelWidth="160" inputWidth="160" noteWidth="180"  offsetLeft="20"/>		
                <item type="hidden" name="ACL_TrackInvReceipt" value="'.$ACL_Obj->ACL_TrackInvReceipt.'" />
                <item type="hidden" name="AJID" value="0" />
                <item type="hidden" name="Job_Status" value="'.$jobStatus.'" />
                <item type="hidden" name="AJR_DRId" value="M- DR- '.str_pad($AJR_Details['AJR_DRId'], 6, '0', STR_PAD_LEFT).'" />    
                <item type="hidden" name="AJR_INId" value="'.$AJR_INId.'" />    
                <item type="hidden" name="AJR_CDate" value="'.date("d-m-Y",strtotime($AJR_Details['AJR_CDate'])).'" />    

		<item type="input" name="TPBTotalAmount" label="Total Amount" value="" offsetTop="20" required="true" readonly="true" >
			<note width="150">Job Total Amount</note>
		</item>
                <item type="input" name="TPBPaidAmount" label="Amount Paid" value="0" readonly="true" className="noBorder" >
			<note width="150">Amount Paid</note>
		</item>
                
                <item type="block" width="360" offsetLeft="0" className = "leftMove">
                    <item type="combo" label="Bank Name" name="TPBAmountMode" readonly="true" labelWidth="0" offsetLeft="0" inputWidth="150">
                        <note width="100">Payment Remit Type</note>
                        <option value="1" label="Advance Payment" selected="true" />
			<option value="2" label="Part Payment" selected="false" />
                        <option value="3" label="Balance Payment" selected="false" />
                    </item>  
                    <item type="newcolumn"/>
                    <item type="input" name="TPBBalanceAmount" label="Total Amount" value="" required="true" labelWidth="0" offsetLeft="13" validate="[0-9]*">
                            <note width="100">Payment Amount</note>
                    </item> 
                </item>	
                
                <item type="template" name="TPBBalanceAmountData" label="Pending Amount To Pay" value="0" >
			<note width="150">Balance Amount To Be Paid</note>
		</item>
                
                <item type="combo" label="Remit Mode" name="AB_RemitMode" readonly="true" >
                    <note width="100">Payment Type</note>
                    <option value="0" label="Cash" selected="true" />
                    <option value="1" label="Bank" selected="false" />
                </item>  
                
                <item type="combo" label="Bank Account Number" name="TPBAccount" readonly="true" >
                    <note width="150">Bank Account Number</note>
                </item>  
                    
                <item type="input"  name="TPBPayeeAccount" label="Payee Account Number"  validate="ValidNumeric">			
			<note width="150">Payee Account Number</note>
		</item>
                <item type="input" name="TPBPayeeBank" label="Payee Bank"  validate="^[a-zA-Z ]+$">			
			<note width="150">Payee Bank Details</note>
		</item>
                <item type="input" name="TPBPayeeChqDD" label="Payee Cheque/DD Number"  validate="ValidNumeric">			
			<note width="150">Payee Cheque/DD Number</note>
		</item>
		<item type="button" value="'.$label.'" name="TPBProceed" offsetLeft="182" />
	</items>';
/*
 * <item type="button" value="Proceed"                 name="TPBProceed"        offsetLeft="90" />
                <item type="button" value="Print Cash Receipt"      name="TPBCashReceipt"   hidden="true" />
                <item type="button" value="Print Document Receipt"  name="TPBDocReceipt"    hidden="true" />
                <item type="button" value="Print Tax Invoice"       name="TPBTax"           hidden="true" />
 */
?>