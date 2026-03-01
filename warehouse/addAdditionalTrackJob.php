<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");

$AttObj             = new AttestationClass();
$UserObj            = new UserClass();
$OffObj             = new OfficeClass();

$offAdm       = $OffObj->offzAdmin($preTally_user_ofid);
$IT_Approval  = $UserObj->myReportingPerson($preTally_user_id);
if($offAdm == $preTally_user_id) $IT_Approval = 0 ;

$AttObj->getBalsheetsettings($preTally_user_ofid);
$BSSettingsArray = $AttObj->DataArray;
$detailsArray    = array();  // array for job details table updation of status

$_REQUEST['TPBBalanceAmount'] = $_REQUEST['TPBBalanceAmount'] != 0 ? ltrim($_REQUEST['TPBBalanceAmount'], '0') : 0 ;
$BS_Id = 0;
$AJ_Id = $_REQUEST['AJID'] ;

$CandidateObj    =  $AttObj->getDetails( ' attestation_job_details AS AJ' , 
                                         ' AJ.AJ_TotalAmount,AJ.AJ_Track_Id,AJ.AJ_FName,AJ.AJ_ReceivedDate', 
                                         ' WHERE AJ.AJ_Id = '.$_REQUEST['AJ_Id']);
$CandidateDetails = (array) $AttObj->DataArray[0];

if($_REQUEST['TPBTotalAmount'] != '') {

    // Check whether there is entry in balance sheet for job received 
    /** ~imp
    $JobFrom_IT_Id = $BSSettingsArray[1]; //Attestation Job Received from
    $checkBalanceSheetEntry = $AttObj->getValue('balance_sheets', 'BS_Id', " WHERE IT_Id = $JobFrom_IT_Id AND TR_Id ='".$CandidateDetails['AJ_Track_Id']."'");
    if(!$checkBalanceSheetEntry) {
        $AttObj->InsertData = array(  
            'US_Id'             => $preTally_user_id,
            'IT_Id'             => $JobFrom_IT_Id,
            'OF_Id'             => $preTally_user_ofid,
            'DS_Description'    => $CandidateDetails['AJ_FName'],
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
        //----- Add description for job received from - End --------//
        $AttObj->Data = array(  
            'US_Id'         => $preTally_user_id,
            'IT_Id'         => $JobFrom_IT_Id,
            'LC_Id'         => $preTally_user_lcid,
            'PM_Id'         => 1,
            'BS_Amount'     => trim(htmlspecialchars($_REQUEST['TPBTotalAmount'], ENT_QUOTES)),
            'BS_Description'=> $BS_Description,
            'TR_Id'         => $CandidateDetails['AJ_Track_Id'],
            'BS_DualEntry'  => 0,
            'BS_Complete'   => 1,
            'BS_PaidDate'   => $CandidateDetails['AJ_ReceivedDate'],
            'BS_Date'       => date('Y-m-d H:i:s'),
            'BS_CDate'      => date('Y-m-d H:i:s'),
            'BS_MDate'      => date('Y-m-d H:i:s'),
            'BS_Status'     => 1
        );
        $AttObj->insertRecords('balance_sheets'); //adding into balance_sheets while editing
    }
     * 
     */
    
    /* If job is completed and amount is greater than amount in billing table */
    /** ~imp
    if($CandidateDetails['AJ_TotalAmount'] && ($_REQUEST['TPBTotalAmount'] > $CandidateDetails['AJ_TotalAmount']) ) {  // if job is added to billing table ie, completed job (additional job Received)
        $additionalAmt = $_REQUEST['TPBTotalAmount'] - $CandidateDetails['AJ_TotalAmount']; 
        $JobRC_ITId    = $BSSettingsArray[3]; //Attestation Additional Job Received from
    } else if($CandidateDetails['AJ_TotalAmount'] && ($_REQUEST['TPBTotalAmount'] < $CandidateDetails['AJ_TotalAmount']) ) { // attestation job returned entry
        $JobRC_ITId    = $BSSettingsArray[4]; //Attestation job returned
        $additionalAmt = $CandidateDetails['AJ_TotalAmount'] - $_REQUEST['TPBTotalAmount'] ; 
    }
    if($JobRC_ITId) {
        $AttObj->InsertData = array(  
            'US_Id'            => $preTally_user_id,
            'IT_Id'            => $JobRC_ITId,
            'OF_Id'            => $preTally_user_ofid,
            'DS_Description'   => trim(htmlspecialchars($CandidateDetails['AJ_FName'], ENT_QUOTES)),
            'DS_Approval'      => $IT_Approval,
            'DS_Approved'      => $preTally_user_id,
            'DS_Notf'          => '',
            'DS_Status'        => 1,
            'DS_CDate'         => date('Y-m-d H:i:s'),
            'DS_MDate'         => date('Y-m-d H:i:s')  
        );
        $BS_Description = $AttObj->getValue('descriptions', 'DS_Id', " WHERE DS_Description = '".$AttObj->InsertData['DS_Description']."' AND IT_Id = ".$AttObj->InsertData['IT_Id']." AND OF_Id = ".$preTally_user_ofid." AND DS_Status != 4");
        if(!$BS_Description) {
            $BS_Description = $AttObj->insertReturnId('descriptions');
        }
        $AttObj->InsertData = array(  
            'US_Id'           => $preTally_user_id,
            'IT_Id'           => $JobRC_ITId,
            'LC_Id'           => $preTally_user_lcid,
            'PM_Id'           => 1,
            'BS_Amount'       => $additionalAmt,
            'BS_Description'  => $BS_Description,
            'TR_Id'           => $CandidateDetails['AJ_Track_Id'],
            'BS_DualEntry'    => 0,
            'BS_Complete'     => 1,
            'BS_PaidDate'     => date('Y-m-d H:i:s'),
            'BS_Date'         => date('Y-m-d H:i:s'),
            'BS_CDate'        => date('Y-m-d H:i:s'),
            'BS_MDate'        => date('Y-m-d H:i:s'),
            'BS_Status'       => 1
        );
        $AttObj->insertReturnId('balance_sheets');
    }
     * 
     */
}
if($_REQUEST['TPBBalanceAmount'] !== NULL) { 
    if(($_REQUEST['TPBTotalAmount'] - $_REQUEST['TPBPaidAmount']) >= $_REQUEST['TPBBalanceAmount']) {   // valid advance/balance amount 
      
        /* Add Billing Details Starts */
        $AttObj->InsertData = array(  
            'AJ_Id'               => $AJ_Id,
            'ABP_AmountRecieved'  => htmlspecialchars(trim($_REQUEST['TPBBalanceAmount']), ENT_QUOTES),
            'ABP_RemitMode'       => htmlspecialchars(trim($_REQUEST['AB_RemitMode']), ENT_QUOTES),
            'BA_Id'               => (empty($_REQUEST['TPBAccount'])) ? 0 : htmlspecialchars(trim($_REQUEST['TPBAccount']), ENT_QUOTES), 
            'ABP_PayeeAc'         => htmlspecialchars(trim($_REQUEST['TPBPayeeAccount']), ENT_QUOTES),
            'ABP_PayeeBank'       => htmlspecialchars(trim($_REQUEST['TPBPayeeBank']), ENT_QUOTES),
            'ABP_PayeeChequeDD'   => htmlspecialchars(trim($_REQUEST['TPBPayeeChqDD']), ENT_QUOTES),
            'BS_Id'               => $BS_Id,
            'ABP_ReceivedDate'    => date('Y-m-d H:i:s')
        );
        $ABP_Id = $AttObj->insertReturnId('attestation_job_billing_payment'); //If valid advance save it into attestation_job_billing_payment
        /* Add Billing Details Ends */
        
        
        
        /* Add Job Receipt Starts */
//        $AJR_Details     = $AttObj->getRowDetails('attestation_job_receipts', 'AJR_DRId', " WHERE AJ_Id = ".$AJ_Id);
        $AJR_Details = $AttObj->getRowDetails('attestation_job_receipts', 'AJR_DRId,AJR_CDate', " WHERE AJ_Id = ".$AJ_Id." ORDER BY AJR_CDate ASC LIMIT 0,1");
        $LastAJR_Details = $AttObj->getRowDetails('attestation_job_receipts', 'MAX(AJR_DRId) AS AJR_DRId, MAX(AJR_CRId) AJR_CRId', " WHERE OF_Id = ".$preTally_user_ofid." ");
       
        if(!$AJR_Details){
           
            $AJR_CRId = $LastAJR_Details['AJR_CRId'] + 1;
            $AJR_DRId = $LastAJR_Details['AJR_DRId'] + 1;
            $AJR_Details['AJR_CDate'] = date('Y-m-d') ;
            
            $AttObj->Data = array(
                'AJ_Id'         => $AJ_Id,
                'OF_Id'         => $preTally_user_ofid,
                'AJR_DR_Prefix' => 'DR',
                'AJR_DRId'      => $AJR_DRId,
                'AJR_CR_Prefix' => 'CR',
                'AJR_CRId'      => $AJR_CRId,
                'AJR_CRAmount'  => htmlspecialchars(trim($_REQUEST['TPBBalanceAmount']), ENT_QUOTES),
                'AJR_CDate'     => date('Y-m-d'),
                'AJR_Status'    => 1
            );
            $AttObj->insertRecords('attestation_job_receipts');
        }else{
            
            $AJR_CRId = $LastAJR_Details['AJR_CRId'] + 1;
            $AJR_DRId = $AJR_Details['AJR_DRId'];
            
            $AttObj->Data = array(
                'AJ_Id'         => $AJ_Id,
                'OF_Id'         => $preTally_user_ofid,
                'AJR_DR_Prefix' => 'DR',
                'AJR_DRId'      => $AJR_DRId,
                'AJR_CR_Prefix' => 'CR',
                'AJR_CRId'      => $AJR_CRId,
                'AJR_CRAmount'  => htmlspecialchars(trim($_REQUEST['TPBBalanceAmount']), ENT_QUOTES),
                'AJR_CDate'     => date('Y-m-d'),
                'AJR_Status'    => 1
            );
            $AttObj->insertRecords('attestation_job_receipts'); 
        }
        /* Add Job Receipt Ends */
        
        
        /* Fetch Invoice Receipt Details   */
        
        $Invoice_Details = $AttObj->getRowDetails('attestation_job_invoice_receipts', 'AJIR_InvoiceNo,AJIR_StatutoryAmt,AJIR_SubTotal,AJIR_ServiceTax,AJIR_Total', " WHERE AJ_Id = ".$AJ_Id." ");
        $jobReceiptDetails = array(
            'AJR_CRId'      => 'M- CR- '.str_pad($AJR_CRId, 6, '0', STR_PAD_LEFT),
            'AJR_DRId'      => 'M- DR- '.str_pad($AJR_DRId, 6, '0', STR_PAD_LEFT), 
            'AJR_INId'      => $Invoice_Details['AJIR_InvoiceNo'], 
            'AJR_Statutory' => $Invoice_Details['AJIR_StatutoryAmt'], 
            'AJR_Subtotal'  => $Invoice_Details['AJIR_SubTotal'], 
            'AJR_Service'   => $Invoice_Details['AJIR_ServiceTax'],
            'AJR_Total'     => $Invoice_Details['AJIR_Total'],
            'AJR_CDate'     => date("d-m-Y",strtotime($AJR_Details['AJR_CDate']))
        );   
        
         /** ~imp
        if($AdvanceJob) $AdvanceFrom_IT_Id  = $BSSettingsArray[5] ;    // advance already received -- Attestation Balance Payment Received from
        else $AdvanceFrom_IT_Id  = $BSSettingsArray[2] ;   // advance to be added -- Attestation Advance Payment Received from
        
        //------- new---------//
        if($_REQUEST['TPBAmountMode'] == 1 ) $AdvanceFrom_IT_Id  = $BSSettingsArray[2] ;   // advance to be added -- Attestation Advance Payment Received from
        else  $AdvanceFrom_IT_Id  = $BSSettingsArray[5] ;   // advance already received -- Attestation Balance Payment Received from
        
        if($AdvanceFrom_IT_Id) { //--- If save/proceed is clicked and advance amount is inserted to balance sheet as balance amount received from ---//
            $AttObj->InsertData = array(  
                'US_Id'             => $preTally_user_id,
                'IT_Id'             => $AdvanceFrom_IT_Id,
                'OF_Id'             => $preTally_user_ofid,
                'DS_Description'    => $CandidateDetails['AJ_FName'],
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
                'US_Id' 	=> $preTally_user_id,
                'IT_Id' 	=> $AdvanceFrom_IT_Id,
                'LC_Id'         => $preTally_user_lcid,
                'PM_Id'         => 1,
                'BS_Amount'	=> $_REQUEST['TPBBalanceAmount'],
                'BS_Description'=> $BSAdvance_Description,
                'TR_Id'         => $CandidateDetails['AJ_Track_Id'],
                'BS_DualEntry'  => 0,
                'BS_Complete'   => 1,
                'BS_PaidDate' 	=> $CandidateDetails['AJ_ReceivedDate'],
                'BS_Date'       => date('Y-m-d H:i:s'),
                'BS_CDate' 	=> date('Y-m-d H:i:s'),
                'BS_MDate' 	=> date('Y-m-d H:i:s'),
                'BS_Status'     => 1
            );
            if($_REQUEST['AB_RemitMode'] == 1)  { // bank 
                $BankDetails = $AttObj->getDetails('bank_accounts AS BA LEFT JOIN bank_branches AS BB ON BA.BB_Id = BB.BB_Id', 
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
            $BS_Id = $AttObj->insertReturnId('balance_sheets');
        }
         * 
         */
    } else{
        echo "amtErr";
        exit;
    }
}

/* Click of save button. Starts */
unset($AttObj->Data);
$AJ_Status = $AttObj->getValue('attestation_job_details', 'AJ_Status', 'WHERE AJ_Id = '.$AJ_Id);
if($AJ_Status == 0) {
    $detailsArray['AJ_Status'] = 1;
}
$AttObj->Data = $detailsArray;
$AttObj->Data['AJ_TotalAmount'] = $_REQUEST['TPBTotalAmount'];
$AttObj->updateRecords('attestation_job_details'," AJ_Id = ".$AJ_Id);
/* Click of save button. Ends */

echo json_encode($jobReceiptDetails); 
?>