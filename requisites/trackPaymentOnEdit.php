<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj    = new AttestationClass();
$BillingDetails = array();
if($REQUEST['AJ_Id']) {
    $totAmt = $AttObj->getValue('attestation_job_documents', 'SUM(AJD_Amount) AS AJD_Amount', " WHERE AJD_Status != 0 AND AJ_Id = ".$REQUEST['AJ_Id']);
    
    $BillingDetailQuery = $AttObj->getDetails('attestation_job_billing AS AJB ', 
                ' AJB.* ', 
                " WHERE AJB.`AJ_Id` = ".$REQUEST['AJ_Id']);
    $BillingDetails   = (array) $AttObj->DataArray[0];
    
    $PaymentDetailQuery = $AttObj->getDetails('attestation_job_billing_payment AS AJP ', 
                ' SUM(AJP.ABP_AmountRecieved) AS AmtSum', 
                " WHERE AJP.`AB_Id` = ".$BillingDetails['AB_Id']);
    $PaymentDetails   = (array) $AttObj->DataArray[0];
}
if($PaymentDetails['AmtSum']) 
    $label = 'Balance Amount';
else 
    $label = 'Advance Amount';
echo '[
    {type: "settings", position: "label-left", labelWidth: 130, inputWidth: 120, offsetLeft : 20},
        {type: "hidden", name: "AJ_Id", value : '.$REQUEST['AJ_Id'].'},
        {type: "hidden", name: "ABP_AmountRecieved_Tot", value : "'.$PaymentDetails['AmtSum'].'"},
        {type: "hidden", name: "save_status"},
        {type: "input", name: "TPBTotalAmount",  label: "Total Amount", value: "'.$totAmt.'", readonly : "true", offsetTop : 20 },
        {type: "input", name: "TPBAdvanceAmount", label: "'.$label.'", validate : "ValidNumeric" },
        {type: "combo", name: "AB_RemitMode",label: "Remit Mode", options:[
            {text: "Cash", value: "0", selected: true},
            {text: "Bank", value: "1"}
        ]},
        {type: "combo", name: "TPBAccount", label: "Bank Account Number",hidden: "true" },
        {type: "input", name: "TPBPayeeAccount", label: "Payee Account Number", hidden: "true" ,  validate : "ValidNumeric"},
        {type: "input", name: "TPBPayeeBank", label: "Payee Bank", hidden: "true",  validate : "^[a-zA-Z ]+$"},
        {type: "input", name: "TPBPayeeChqDD", label: "Payee Cheque/DD Number", hidden: "true",  validate : "ValidNumeric"},
        {type: "button", name:"TPBProceed", value: "Proceed", offsetLeft : 90},
        {type: "button", name:"TPBDocReceipt", disabled:"true", value: "Print Document Receipt"},';
        if($ACL_Obj->ACL_TrackInvReceipt)
            echo ' {type: "button", name:"TPBTax", disabled:"true", value: "Print Tax Invoice"}';
echo ']';