<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj     = new AttestationClass();

$DOCArray = json_decode($_REQUEST['DOC_Array']);   // documents array
$AJ_Id    = $_REQUEST['AJ_Id'];
$newDoc   = 0;
$Amount   = 0;

$AttObj->getDocmentStatus($AJ_Id);
$ADstatusArray = $AttObj->StatusArray;
$AttObj->getSubprocessStatus($AJ_Id);
$SPStatusArray = $AttObj->SubProStusArray;

$AJDIdArray     = array();

$AttObj->Data = array('AJD_Status' => 0);
$AttObj->updateRecords('attestation_job_documents',' AJ_Id = '.$AJ_Id);
$AttObj->deleteRecords('attestation_job_subprocess', ' WHERE AJD_Id IN (SELECT AJD_Id FROM attestation_job_documents WHERE AJ_Id ="'.$AJ_Id.'")');
//$AttObj->deleteRecords('attestation_job_supporting_documents', ' WHERE  AJ_Id = '.$AJ_Id);

//unset($AttObj->Data);
//$AttObj->Data = array( //attestation_job_supporting_documents 
//    'AJ_Id'         => htmlspecialchars(trim($_REQUEST['AJ_Id']), ENT_QUOTES),  
//    'ASD_Id'        => htmlspecialchars(trim($_REQUEST['ASD_Id']), ENT_QUOTES)  
//);
//if($_REQUEST['ASD_Id']) $AttObj->insertRecords('attestation_job_supporting_documents');

foreach($DOCArray as $DocDetails) { 

    $AttObj->Certf_Data = array(   // attestation_job_documents 
        'AJ_Id'             => $AJ_Id,  
        'LC_Id'             => $preTally_user_lcid,
        'ADOC_Id'           => htmlspecialchars(trim($DocDetails->ADOC_Id), ENT_QUOTES),
        'ST_Id'             => $AttObj->getValue('attestation_process_sub', 'ST_Id', " WHERE APS_Id = '".$DocDetails->APS_Id."' "),        
        'APS_Id'            => htmlspecialchars(trim($DocDetails->APS_Id), ENT_QUOTES),
        'AJD_LastProcess'   => htmlspecialchars(trim($DocDetails->AJD_LastProcess), ENT_QUOTES),
        'AJD_VisitingCNId'  => htmlspecialchars(trim($DocDetails->AJD_VisitingCNId), ENT_QUOTES),
        'AJD_IssuingCNId'   => htmlspecialchars(trim($DocDetails->AJD_IssuingCNId), ENT_QUOTES),
        'AJD_VisaType'      => htmlspecialchars(trim($DocDetails->AJD_VisaType), ENT_QUOTES),
        'AJD_Amount'        => htmlspecialchars(trim($DocDetails->AJD_Amount), ENT_QUOTES),
        'AJD_Year'          => htmlspecialchars(trim($DocDetails->AJD_Year), ENT_QUOTES),
        'AJD_UniqueNo'      => htmlspecialchars(trim($DocDetails->AJD_UniqueNo), ENT_QUOTES),
        'AJD_Comment'       => htmlspecialchars(trim($DocDetails->AJD_Comment), ENT_QUOTES),
        'AJD_CDate'         => date('Y-m-d H:i:s')
    );  // certificate details 
    $Amount = $Amount + $DocDetails->AJD_Amount;

    $AJD_Id  = $AttObj->getValue('attestation_job_documents', ' AJD_Id ', ' WHERE ADOC_Id = '.$AttObj->Certf_Data['ADOC_Id'].' AND AJD_IssuingCNId = '.$AttObj->Certf_Data['AJD_IssuingCNId'].' AND AJD_VisaType = '.$AttObj->Certf_Data['AJD_VisaType'].' AND AJ_Id = '.$AJ_Id.' AND AJD_VisitingCNId = '.$AttObj->Certf_Data['AJD_VisitingCNId'].' AND AJD_Year = '.$AttObj->Certf_Data['AJD_Year'].' AND APS_Id = '.$AttObj->Certf_Data['APS_Id']);
    if($AJD_Id) { // Checking document status, save current status
        $AJDStatus = array_key_exists($AJD_Id,$ADstatusArray) ? $ADstatusArray[$AJD_Id] : 1;
        unset($AttObj->Data);
        $AttObj->Data = array(
            'AJD_Amount' => $AttObj->Certf_Data['AJD_Amount'],
            'AJD_Status' => $AJDStatus,
            'AJD_Remarks'       => htmlspecialchars(trim($DocDetails->AJD_Remarks), ENT_QUOTES)
        );
        $AttObj->updateRecords('attestation_job_documents',' AJD_Id ='.$AJD_Id);
    }else{
        $AttObj->InsertData = $AttObj->Certf_Data;
        $AJD_Id = $AttObj->insertReturnId('attestation_job_documents');
        $newDoc++;
        
        array_push($AJDIdArray, $AJD_Id);
    }
    
    //attestation_job_subprocess
    unset($AttObj->Data);
    foreach((array) $DocDetails->processIds as $key => $processId) {
        
        $subPStatus = isset($SPStatusArray[$AJD_Id][$processId]) ? $SPStatusArray[$AJD_Id][$processId] : 1;
        
        $AttObj->Data = array(
            'AJD_Id'     => $AJD_Id,
            'APS_Id'     => $processId,
            'AJS_Order'  => $key,
            'AJS_Status' => $subPStatus
        );
        $AttObj->insertRecords('attestation_job_subprocess');
    }
    
    //attestation_job_supporting_documents
    $supProcsIds = implode(',', (array) $DocDetails->supDocIds);
    unset($AttObj->Data);
    $AttObj->Data = array( //attestation_job_supporting_documents 
        'AJD_Id'         => $AJD_Id,
        'ASD_Id'         => $supProcsIds
    );
    
    $AJSD_Id = $AttObj->getValue('attestation_job_supporting_documents', 'AJSD_Id', ' WHERE AJD_Id = '.$AJD_Id);
    if(!$AJSD_Id)
        $AttObj->insertRecords('attestation_job_supporting_documents');
    else
        $AttObj->updateRecords('attestation_job_supporting_documents','AJD_Id = '.$AJD_Id);
}

//$AttObj->insertMultipleData('attestation_job_subprocess');
if($newDoc > 0){ //updation job status
    $AJ_Status = $AttObj->getValue('attestation_job_details', 'AJ_Status', ' WHERE AJ_Id = '.$AJ_Id);
    if($AJ_Status==4){
        unset($AttObj->Data);
        $AttObj->Data = array(
            'AJ_Status' => 3
        );
        $AttObj->updateRecords('attestation_job_details',' AJ_Id ='.$AJ_Id);
        $AJ_Status = 3 ;
    }    
}   else  $AJ_Status = 1 ;

// Updating Total Amount in Job Details
unset($AttObj->Data);
$AttObj->Data = array(
    'AJ_TotalAmount' => $Amount
);
$AttObj->updateRecords('attestation_job_details',' AJ_Id ='.$AJ_Id);

unset($AttObj->Data);
foreach ($AJDIdArray as $key => $DocId) {
    $AttObj->Data[] = array(  
        'US_Id'         => $preTally_user_id,
        'LC_Id'         => $preTally_user_lcid,
        'AJ_Id'         => $AJ_Id,
        'AJG_Id'        => 0,
        'AJD_Id'        => $DocId,
        'AJS_Id'        => 0,
        'AJT_FromLC'    => 0,
        'AJT_ToLC'      => 0,
        'AJT_ReceiveLC' => 0,
        'AJ_Status'     => $AJ_Status,
        'AJD_Status'    => 1,
        'AJS_Status'    => 0,
        'AJT_CDate'     => date('Y-m-d H:i:s'),
        'AJT_Status'    => 1
    );
}
$AttObj->insertMultipleData('attestation_job_tracking');

if($REQUEST['t_flag']) {    // on job edit
   $AttObj->updateDocReceipt($Amount,$_REQUEST['AJ_Id']);
}
echo $AttObj->getReceiptsData($_REQUEST['AJ_Id']);  // receipt details
?>