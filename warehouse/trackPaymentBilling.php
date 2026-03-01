<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");

$UserObj = new UserClass();
$OffObj  = new OfficeClass();
$AttObj  = new AttestationClass();
$AJ_Id   = $_REQUEST['AJ_Id'];   // Job Id
$BS_Id   = 0;

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
if($offAdm == $preTally_user_id) $IT_Approval = 0 ;

$AttObj->getBalsheetsettings($preTally_user_ofid);
$BSSettingsArray = $AttObj->DataArray;

$CandidateObj  =  $AttObj->getDetails(' attestation_job_details AS AJD LEFT JOIN attestation_job_billing AS AJB ON AJD.AJ_Id = AJB.AJ_Id' , 
                                      ' AJB.AB_Id,AJB.AB_TotalAmount,AJD.AJ_Track_Id,AJD.AJ_FName,AJD.AJ_ReceivedDate', 
                                      ' WHERE AJD.AJ_Id = '.$AJ_Id);
$CandidateDetails = (array) $AttObj->DataArray[0];
$AB_Id = $CandidateDetails['AB_Id'];

$AttObj->Billing_Data = array(    
    'AJ_Id'                   => htmlspecialchars(trim($AJ_Id), ENT_QUOTES), 
    'AB_TotalAmount'          => htmlspecialchars(trim($_REQUEST['TPBTotalAmount']), ENT_QUOTES),  
    'AB_ReceivedDate'         => date('Y-m-d H:i:s')
);

if($AB_Id == 0) { 
    $AB_Id=$AttObj->trackBill();//save billing details
}else{
    $AttObj->updateTrackBill($AJ_Id);
}

//----Updating Table attestation_job_invoice_receipts ~ STARTS ------//
$subTot         = sprintf ("%.2f", $AttObj->Billing_Data['AB_TotalAmount']/1.14);   // 14% of service tax + sub total = total
$statutoryAmt   = sprintf ("%.2f", $AttObj->Billing_Data['AB_TotalAmount']*0.4);   // 40% of total
$serviceTax     = sprintf ("%.2f", $subTot*0.14); 

$AttObj->updateReceipt($statutoryAmt,$serviceTax,$AJ_Id,$subTot);

//----Updating Table Job Details ~ STARTS ------//
$AttObj->Data = array(  
    'AJ_TotalAmount' => $AttObj->Billing_Data['AB_TotalAmount']
);
if($_REQUEST['save_status'] == 1) {   // clicked on save button
    $AttObj->Data['AJ_Status'] = 1;
}
$AttObj->updateRecords('attestation_job_details','AJ_Id ='. $AJ_Id);

//$AttObj->trackBillPayment($AJ_Id, $_REQUEST['save_status']);  // insert/update to billing tables

/** ~imp
$JobFrom_IT_Id = $BSSettingsArray[1]; //Attestation Job Received from
$checkJobReceivedEntry = $AttObj->getValue('balance_sheets', 'BS_Id', " WHERE IT_Id = $JobFrom_IT_Id AND TR_Id = ".$CandidateDetails['AJ_Track_Id']); // check whether entry for job received is already in balance sheet 

//----------- Job received balance sheet entry. Starts ---------//
$AttObj->InsertData = array(  
    'US_Id'             => $preTally_user_id,
    'IT_Id'             => $JobFrom_IT_Id,
    'OF_Id'             => $preTally_user_ofid,
    'DS_Description'    => trim(htmlspecialchars($CandidateDetails['AJ_FName'], ENT_QUOTES)),
    'DS_Approval'       => $IT_Approval,
    'DS_Approved'       => $preTally_user_id,
    'DS_Notf'           => '',
    'DS_Status'         => 1,
    'DS_CDate'          => date('Y-m-d H:i:s'),
    'DS_MDate'          => date('Y-m-d H:i:s')  
);
$BS_Description = $AttObj->getValue('descriptions', 'DS_Id', " WHERE DS_Description = '".$AttObj->InsertData['DS_Description']."' AND IT_Id = ".$AttObj->InsertData['IT_Id']." AND OF_Id = ".$preTally_user_ofid." AND DS_Status != 4");
if(!$BS_Description) {
    $BS_Description = $AttObj->insertReturnId('descriptions');
}

$AttObj->Data = array(  
    'US_Id'             => $preTally_user_id,
    'IT_Id'             => $JobFrom_IT_Id,
    'LC_Id'             => $preTally_user_lcid,
    'PM_Id'             => 1,
    'BS_Amount'         => trim(htmlspecialchars($_REQUEST['TPBTotalAmount'], ENT_QUOTES)),
    'BS_Description'    => $BS_Description,
    'TR_Id'             => $CandidateDetails['AJ_Track_Id'],
    'BS_DualEntry'      => 0,
    'BS_Complete'       => 1,
    'BS_PaidDate' 	=> $CandidateDetails['AJ_ReceivedDate'],
    'BS_Date'           => date('Y-m-d H:i:s'),
    'BS_CDate'          => date('Y-m-d H:i:s'),
    'BS_MDate'          => date('Y-m-d H:i:s'),
    'BS_Status'         => 1
);


if(!$checkJobReceivedEntry)     // if entry is not in balance sheet
    $AttObj->insertRecords('balance_sheets');
else   // update if entry already exist
    $AttObj->updateRecords('balance_sheets',' BS_Id = '.$checkJobReceivedEntry);
 * 
 */

//------------ Job received balance sheet entry. Ends -----------------//        
        

/**** Job advance received balance sheet entry. Starts ****/  
if($_REQUEST['TPBAdvanceAmount']) {   // advance is given
    /** ~imp
    $AdvanceFrom_IT_Id  = $BSSettingsArray[2] ;   // advance to be added -- Attestation Advance Payment Received from
    $checkAdvanceReceivedEntry  = $AttObj->getValue('balance_sheets', 'BS_Id', " WHERE IT_Id = $AdvanceFrom_IT_Id AND TR_Id = ".$CandidateDetails['AJ_Track_Id']); // check whether advance for job received is already in balance sheet 

    $AttObj->InsertData = array(  
        'US_Id'             => $preTally_user_id,
        'IT_Id'             => $AdvanceFrom_IT_Id,
        'OF_Id'             => $preTally_user_ofid,
        'DS_Description'    => trim(htmlspecialchars($CandidateDetails['AJ_FName'], ENT_QUOTES)),
        'DS_Approval'       => $IT_Approval,
        'DS_Approved'       => $preTally_user_id,
        'DS_Notf'           => '',
        'DS_Status'         => 1,
        'DS_CDate'          => date('Y-m-d H:i:s'),
        'DS_MDate'          => date('Y-m-d H:i:s')  
    );

    $BSAdvance_Description = $AttObj->getValue('descriptions', 'DS_Id', " WHERE DS_Description = '".$AttObj->InsertData['DS_Description']."' AND IT_Id = ".$AttObj->InsertData['IT_Id']." AND OF_Id = ".$preTally_user_ofid." AND DS_Status != 4");
    if(!$BSAdvance_Description) {
        $BSAdvance_Description = $AttObj->insertReturnId('descriptions');
    }
    $AttObj->InsertData = array(
        'US_Id'             => $preTally_user_id,
        'IT_Id'             => $AdvanceFrom_IT_Id,
        'LC_Id'             => $preTally_user_lcid,
        'PM_Id'             => 1,
        'BS_Amount'         => trim(htmlspecialchars($_REQUEST['TPBAdvanceAmount'], ENT_QUOTES)),
        'BS_Description'    => $BSAdvance_Description,
        'TR_Id'             => $CandidateDetails['AJ_Track_Id'],
        'BS_DualEntry'      => 0,
        'BS_Complete'       => 1,
        'BS_PaidDate' 	    => $CandidateDetails['AJ_ReceivedDate'],
        'BS_Date'           => date('Y-m-d H:i:s'),
        'BS_CDate'          => date('Y-m-d H:i:s'),
        'BS_MDate'          => date('Y-m-d H:i:s'),
        'BS_Status'         => 1
    );
   
    if($_REQUEST['AB_RemitMode'] == 1)  { // bank 
        $BankDetails = $AttObj->getDetails( 'bank_accounts AS BA LEFT JOIN bank_branches AS BB ON BA.BB_Id = BB.BB_Id', 
                                            ' BB.BNK_Id,BA.BA_Id,BA.BB_Id ', 
                                            ' WHERE BA.BA_Id = '.$_REQUEST['TPBAccount']);
        $BankDetails = (array) $AttObj->DataArray[0];
        $AttObj->InsertData['PM_Id']  = 2;
        $AttObj->InsertData['BNK_Id'] = $BankDetails['BNK_Id'];
        $AttObj->InsertData['BB_Id']  = $BankDetails['BB_Id'];
        $AttObj->InsertData['BA_Id']  = $BankDetails['BA_Id'];
        $AttObj->InsertData['BS_PayersBank'] = htmlspecialchars(trim($_REQUEST['TPBPayeeBank']), ENT_QUOTES);
        $AttObj->InsertData['BS_PayersChQ']  = htmlspecialchars(trim($_REQUEST['TPBPayeeChqDD']), ENT_QUOTES);
        $AttObj->InsertData['BS_Status'] = 2;
    }

    if(!$checkAdvanceReceivedEntry)
        $BS_Id = $AttObj->insertReturnId('balance_sheets');
    else {
        $AttObj->Data = $AttObj->InsertData;
        $AttObj->updateRecords('balance_sheets',' BS_Id = '.$checkAdvanceReceivedEntry);
        $BS_Id = $checkAdvanceReceivedEntry;
    }
 * 
 */
        
    $AttObj->Payment_Data = array(   
        'AB_Id'               => $AB_Id,
        'ABP_AmountRecieved'  => htmlspecialchars(trim($_REQUEST['TPBAdvanceAmount']), ENT_QUOTES),
        'ABP_RemitMode'       => htmlspecialchars(trim($_REQUEST['AB_RemitMode']), ENT_QUOTES),
        'BA_Id'               => (empty($_REQUEST['TPBAccount'])) ? 0 : htmlspecialchars(trim($_REQUEST['TPBAccount']), ENT_QUOTES), 
        'ABP_PayeeAc'         => htmlspecialchars(trim($_REQUEST['TPBPayeeAccount']), ENT_QUOTES),
        'ABP_PayeeBank'       => htmlspecialchars(trim($_REQUEST['TPBPayeeBank']), ENT_QUOTES),
        'ABP_PayeeChequeDD'   => htmlspecialchars(trim($_REQUEST['TPBPayeeChqDD']), ENT_QUOTES),
        'BS_Id'               => $BS_Id,
        'ABP_ReceivedDate'    => date('Y-m-d H:i:s')
    );
    $ABP_Id = $AttObj->getValue('attestation_job_billing_payment', ' ABP_Id ', ' WHERE AB_Id = '.$AB_Id);
    if($ABP_Id == 0)  $AttObj->trackPayment(); // save payment details
    else  $AttObj->updateTrackPayment($ABP_Id);
    
    $AJDR_Count = $AttObj->getValue('attestation_job_cash_receipts', 'AJCR_SlNo', ' WHERE AJDR_Id = '.$AJDR_Id);
    if(!$AJDR_Count) {  // cash receipt corresponding to document doesnot exist
//        $ABP_Id     = $AttObj->getValue('attestation_job_billing_payment', ' ABP_Id ', ' WHERE AB_Id = '.$AB_Id);
        $AJDR_Id    = $AttObj->getValue('attestation_job_doc_receipts', 'AJDR_Id', ' WHERE AJ_Id = '.$AJ_Id);
        $AJCR_Id    = $AttObj->cashReceiptEntry($AJ_Id,$ABP_Id,$AJDR_Id);
        $AJCR_SlNo  = 'M- CR- '.str_pad($AJCR_Id, 6, '0', STR_PAD_LEFT);
        $AttObj->cashReceiptUpdate($AJCR_SlNo,$AJCR_Id);
    }
}
echo $AttObj->getReceiptsData($AJ_Id,$preTally_user_lcid);