<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'preTallyClass/AddressClass.php');

$AttObj = new AttestationClass();
$AddressObj=new AddressClass();

$AttObj->Track_Data = array(
        'US_Id'     =>  $preTally_user_id,
        'OF_Id'     =>  $preTally_user_ofid,
        'TR_Track'  =>  '',
        'TR_Status' =>  1,
        'TR_CDate'  => date('Y-m-d H:i:s')
    );
$AJ_Track_Id = $AttObj->generateTrackID('');   // generate track id

if($_REQUEST['EnquiryId'] != 0 ){
    $AttObj->getDetails('attestation_job_enquiry','*',' WHERE AE_Id = '.$_REQUEST['EnquiryId'].' ');
    $CandDetails = $AttObj->DataArray; 
    $DocumentDetails = $CandDetails[0]->AE_AutomateData;
    $DocumentDetailsArray = json_decode($DocumentDetails);
//    $AJAPId = $CandDetails[0]->AE_Cust_Loc;
    
//    $AddressObj->getPlaceIdsLoc($AJAPId);
//    $CityDetails = $AddressObj->IdArray;
//    
//    $AJCTId = $CityDetails[0]['CT_Id'];
//    
//    $AddressObj->getPlaceIdsCty($AJCTId);
//    $StateDetails = $AddressObj->IdArray;
//    
//    $AJSTId = $StateDetails[0]['ST_Id'];
   
}

$AttObj->Job_Data = array(        
    'AJ_Track_Id'       => $AJ_Track_Id, 
    'OF_Id'             => $preTally_user_ofid,
    'LC_Id'             => $preTally_user_lcid, 
    'AJ_FName'          => htmlspecialchars(trim($_REQUEST['EnquiryFNa']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['EnquiryFNa']), ENT_QUOTES) : $CandDetails[0]->AE_Name,
    'AJ_DOB'            => '0000-00-00',
    'AJ_HouseNo'        => '', 
    'AJ_HouseName'      => '',
    'AJ_Society'        => '',
    'SR_Id'             => $CandDetails[0]->SR_Id ? $CandDetails[0]->SR_Id : 0, 
    'PL_Id'             => $CandDetails[0]->PL_Id ? $CandDetails[0]->PL_Id : 0,
    'ALC_Id'            => $CandDetails[0]->ALC_Id ? $CandDetails[0]->ALC_Id : 0,
    'CT_Id'             => $CandDetails[0]->CT_Id ? $CandDetails[0]->CT_Id : 0,         
    'ST_Id'             => $CandDetails[0]->ST_Id ? $CandDetails[0]->ST_Id : 0,
//    'AJ_Street'         => '', 
//    'AP_Id'             => $AJAPId ? $AJAPId : 0,
//    'AJ_Place'          => '', 
    'AJ_Village'        => '', 
    'AJ_Pincode'        => '', 
//    'DT_Id'             => 0,
    'AJ_Email'          => htmlspecialchars(trim($_REQUEST['EnquiryEml']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['EnquiryEml']), ENT_QUOTES) : $CandDetails[0]->AE_Email,
    'AJ_Mobile1'        => htmlspecialchars(trim($_REQUEST['EnquiryMb']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['EnquiryMb']), ENT_QUOTES) : $CandDetails[0]->AE_Mobile,
    'AJ_Mobile2'        => '', 
    'AJ_Landline'       => '', 
    'AJ_BuildingName'   => '',
    'AJ_ContactMob'     => '',
    'AJ_PoliceLoc'      => '',
    'AJ_Email2'         => '',
    'AJ_DeliveryDate'   => '0000-00-00',
    'AJ_WorkExp'        => '',
    'AJ_SubmittedType'  => 0, 
    'AJ_DeliveredType'  => 0, 
    'AJ_JobDeliverdTo'  => 0, 
    'AJ_TotalAmount'    => 0,
    'AJ_ReceivedDate'   => date('Y-m-d'),
    'AJ_Cdate'          => date('Y-m-d'),
    'AJ_MDate'          => date('Y-m-d'),
    'AJ_Status'         =>'0'
);

$AJ_Id = $AttObj->newJobDetails();



foreach($DocumentDetailsArray as $key => $DocDetails) { 
   /* Track Automate
    if($key == 1) {
        $AttObj->Data = array( //attestation_job_supporting_documents 
            'AJ_Id'         => $AJ_Id,  
            'ASD_Id'        => implode(",", $DocDetails[2])
        );
        $AttObj->insertRecords('attestation_job_supporting_documents');
    }
    */
    $date = new DateTime();
    $uniqueId = floor(rand() + $date->getTimestamp() + rand());
//    $AttObj->Certf_Data = array(   // attestation_job_documents 
//        'AJ_Id'         => $AJ_Id,  
//        'ADOC_Id'       => $DocDetails[0][2],
//        'AST_Id'        => $AttObj->getValue('attestation_authorities', 'AST_Id', " WHERE AAUTH_Id = '".$DocDetails[0][4]."' "),        
//        'AAUTH_Id'      => $DocDetails[0][4],
//        'AJD_Amount'    => $DocDetails[3],
//        'AJD_Year'      => $DocDetails[0][3],
//        'AJD_UniqueNo'  => $uniqueId,
//    );                  // certificate details
    $AttObj->Certf_Data = array(   // attestation_job_documents 
        'AJ_Id'             => $AJ_Id,  
        'LC_Id'             => $preTally_user_lcid,
        'ADOC_Id'           => $DocDetails[0][2],
        'ST_Id'             => $AttObj->getValue('attestation_process_sub', 'ST_Id', " WHERE APS_Id = '".$DocDetails[0][4]."' "),        
        'APS_Id'            => $DocDetails[0][4],
        'AJD_LastProcess'   => $CandDetails[0]->AE_LastProcess,
        'AJD_VisitingCNId'  => $DocDetails[0][0],
        'AJD_IssuingCNId'   => $DocDetails[0][5],
        'AJD_VisaType'      => $DocDetails[0][1],
        'AJD_Amount'        => $DocDetails[3],
        'AJD_Year'          => $DocDetails[0][3],
        'AJD_UniqueNo'      => $uniqueId,
        'AJD_Comment'       => $DocDetails[4],
        'AJD_Remarks'       => $DocDetails[7],
        'AJD_Status'        => 1,
        'AJD_CDate'         => date('Y-m-d H:i:s')
    );   

    $AttObj->InsertData = $AttObj->Certf_Data; 
    $AJD_Id = $AttObj->insertReturnId('attestation_job_documents');
    
    $AttObj->Data = array( //attestation_job_supporting_documents 
        'AJD_Id'        => $AJD_Id,  
        'ASD_Id'        => implode(",", $DocDetails[2])
    );
    $AttObj->insertRecords('attestation_job_supporting_documents');
   
    //attestation_job_subprocess
    $APS_Id_Array   = array();   // sub process array

    /*
    foreach( $DocDetails[1] as $key => $processId) {
        $ID                 = explode("_", $processId);  
        $APS_Id_Array[$key]   = $ID[1];   // sub process from array of main and sub processes
    }
     * 
     */

    unset($AttObj->Data);
    foreach($DocDetails[1] as $key => $subProcess) { 
        $AttObj->Data = array(
            'AJD_Id'     => $AJD_Id,
            'APS_Id'     => $subProcess,
            'AJS_Order'  => $key,
            'AJS_Status' => 1
        );
        $AttObj->insertRecords('attestation_job_subprocess');
    }
}

if($AJ_Id) {
    $AttObj->Data = array (
        'AE_Status' => 2,
        'AE_AutomateData' => '{}'
    );
    if($AttObj->updateRecords('attestation_job_enquiry', 'AE_Id = '.$_REQUEST['EnquiryId']) == 'success') {
        echo $AJ_Id;
    }
} else echo 'fail';