<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'includes/saveGoogleAddress.php' );
$AttObj = new AttestationClass();

if($_REQUEST['AE_Id'] != 0 ){
    $AttObj->getDetails('attestation_job_enquiry','*',' WHERE AE_Id = '.$_REQUEST['AE_Id'].' ');
    $CandDetails = $AttObj->DataArray;
}

$addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street'], ENT_QUOTES)),
);
$addressIds = json_decode(saveGoglAddress($addressArray));

/*
$ST_Id  = htmlspecialchars(trim($_REQUEST['clientState']), ENT_QUOTES) ;

//if(!is_numeric($_REQUEST['cityText'])){
    $CTName = htmlspecialchars(trim($_REQUEST['cityText']), ENT_QUOTES);
    $CT_Id  = $AttObj->getValue('cities', 'CT_Id', " WHERE CT_Name = '".$CTName."' AND ST_Id = '".$ST_Id."' AND CT_Status = 1"); 
    if(!$CT_Id) {
        $AttObj->InsertData = array(
            'CT_Name'   =>  $CTName,
            'ST_Id'     =>  $ST_Id,
            'US_Id'     =>  $preTally_user_id,
            'CT_CDate'  =>  date('Y-m-d'),
            'CT_MDate'  =>  date('Y-m-d'),
            'CT_Status' =>  1
        );
        $CT_Id = $AttObj->insertReturnId('cities');
    }
//
//}else{
//    $CT_Id = htmlspecialchars(trim($_REQUEST['cityText']), ENT_QUOTES);
//}

//if(! is_numeric($_REQUEST['locationText'])){
    $APName = htmlspecialchars(trim($_REQUEST['locationText']), ENT_QUOTES);
    $AP_Id  = $AttObj->getValue('address_places', 'AP_Id', " WHERE AP_Name = '".$APName."' AND CT_Id = '".$CT_Id."' AND AP_Status = 1"); 
    if(!$AP_Id) {
        $AttObj->InsertData = array( 
            'AP_Name'   =>  htmlspecialchars(trim($_REQUEST['locationText']), ENT_QUOTES),
            'CT_Id'     =>  $CT_Id,
            'AP_Status' =>  1,
            'AP_CDate'  =>  date("Y-m-d"),
            'AP_MDate'  =>  date("Y-m-d"),
        );
        $AP_Id = $AttObj->insertReturnId("address_places");
    }
//
//}else{
//    $AP_Id = htmlspecialchars(trim($_REQUEST['locationText']), ENT_QUOTES);
//}
    
*/
$AttObj->InsertData = array(        
    'AE_Name'           => htmlspecialchars(trim($_REQUEST['clientName']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['clientName']), ENT_QUOTES) : $CandDetails[0]->AE_Name , 
    'AE_Cust_Name'      => '',//htmlspecialchars(trim($_REQUEST['certificateHolderName']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['certificateHolderName']), ENT_QUOTES) : $CandDetails[0]->AE_Cust_Name , 
    'US_Id'             => $preTally_user_id,
    'AE_Mobile'         => htmlspecialchars(trim($_REQUEST['clientNo']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['clientNo']), ENT_QUOTES) : $CandDetails[0]->AE_Mobile , 
    'SR_Id'             => $addressIds->SR_Id, 
    'PL_Id'             => $addressIds->PL_Id,
    'ALC_Id'            => $addressIds->ALC_Id,
    'CT_Id'             => $addressIds->CT_Id,        
    'ST_Id'             => $addressIds->ST_Id, 
    'CN_Id'             => $addressIds->CN_Id,
    'AE_Pincode'        => 0,//htmlspecialchars(trim($_REQUEST['GOGL_Pincode']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['GOGL_Pincode']), ENT_QUOTES) : $CandDetails[0]->AE_Pincode , 
//    'AE_Cust_Loc'       => $AP_Id ? $AP_Id : $CandDetails[0]->AE_Cust_Loc , 
    'AE_Certificate'    => htmlspecialchars(trim($_REQUEST['ADOC_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['ADOC_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Certificate , 
    'APS_Id'            => htmlspecialchars(trim($_REQUEST['APS_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['APS_Id']), ENT_QUOTES) : $CandDetails[0]->APS_Id ,
    'AE_LastProcess'    => htmlspecialchars(trim($_REQUEST['AE_LastProcess']), ENT_QUOTES) !== '' ? htmlspecialchars(trim($_REQUEST['AE_LastProcess']), ENT_QUOTES) : $CandDetails[0]->AE_LastProcess ,
    'AE_Authorities'    => 0,
//    'AE_State'          => 0, 
//    'AE_Authorities'    => htmlspecialchars(trim($_REQUEST['AAUTH_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AAUTH_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Authorities , 
    'AE_Year'           => htmlspecialchars(trim($_REQUEST['AA_IssuedYear']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_IssuedYear']), ENT_QUOTES) : $CandDetails[0]->AE_Year , 
    'AE_Certi_Mode'     => $_REQUEST['AA_CourseType'] ? $_REQUEST['AA_CourseType'] : $CandDetails[0]->AE_Certi_Mode ,  
    'AE_Certi_StudyLoc' => htmlspecialchars(trim($_REQUEST['AA_Issuing_CNId']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_Issuing_CNId']), ENT_QUOTES) : $CandDetails[0]->AE_Certi_StudyLoc , 
    'AE_Att_For'        => htmlspecialchars(trim($_REQUEST['AA_VisaType']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_VisaType']), ENT_QUOTES) : $CandDetails[0]->AA_VisaType ,  
    'AE_Visiting_CNId'  => htmlspecialchars(trim($_REQUEST['CN_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['CN_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Visiting_CNId , 
    'AE_Email'          => htmlspecialchars(trim($_REQUEST['clientEmail']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['clientEmail']), ENT_QUOTES) : $CandDetails[0]->AE_Email , 
//    'AE_AutomateData'   => $_REQUEST['AE_JobAutomateData'],
    'AE_Remarks'        => '',
    'AE_CDate'          => date('Y-m-d'),
    'AE_Status'         => 1
);

if($REQUEST['form'] == 'enq'){
    $AttObj->InsertData['AE_AutomateData'] = $_REQUEST['AE_AutomateData'];
    
    if($_REQUEST['AE_Id'] == 0 ){
        if($AttObj->insertReturnId('attestation_job_enquiry') == 'fail'){
            echo 'fail';
        }else echo 'success';
    }else{
        $AttObj->Data['AE_AutomateData'] = $_REQUEST['AE_AutomateData'];
        if($AttObj->updateRecords('attestation_job_enquiry', ' AE_Id = '.$_REQUEST['AE_Id'].' ') !='success' ){
            echo 'fail';
        }else echo 'success';
    }
} else {
    if($_REQUEST['AE_Id'] == 0 ){
        $enqAutomateData = (array) json_decode($_REQUEST['AE_EnqAutomateData']);
        if(!empty($enqAutomateData)){
            $AttObj->InsertData['AE_AutomateData'] = $_REQUEST['AE_EnqAutomateData'];
            $AttObj->insertReturnId('attestation_job_enquiry');
        }
    }else{
        $AttObj->Data['AE_AutomateData'] = $_REQUEST['AE_EnqAutomateData'];
        $AttObj->Data['AE_Status']       = 2;
        $AttObj->updateRecords('attestation_job_enquiry', ' AE_Id = '.$_REQUEST['AE_Id'].' ');
        if($AttObj->updateRecords('attestation_job_enquiry', ' AE_Id = '.$_REQUEST['AE_Id'].' ') !='success' ){
            //echo 'fail';
        }//else echo 'success';
    }
    //--------------------------------Converting to Job------------------------------------------------------//
    
    
    
    $AttObj->Track_Data = array(
            'US_Id'     =>  $preTally_user_id,
            'OF_Id'     =>  $preTally_user_ofid,
            'TR_Track'  =>  '',
            'TR_Status' =>  1,
            'TR_CDate'  => date('Y-m-d H:i:s')
        );
    $AJ_Track_Id = $AttObj->generateTrackID('');   // generate track id

    $DocumentDetailsArray = json_decode($_REQUEST['AE_JobAutomateData']);

    $AttObj->Job_Data = array(        
        'AJ_Track_Id'       => $AJ_Track_Id, 
        'OF_Id'             => $preTally_user_ofid,
        'LC_Id'             => $preTally_user_lcid, 
        'AJ_FName'          => htmlspecialchars(trim($_REQUEST['clientName']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['clientName']), ENT_QUOTES) : $CandDetails[0]->AE_Name,
        'AJ_DOB'            => '0000-00-00',
        'AJ_HouseNo'        => '', 
        'AJ_HouseName'      => '',
        'AJ_Society'        => '',
        'SR_Id'             => $addressIds->SR_Id, 
        'PL_Id'             => $addressIds->PL_Id,
        'ALC_Id'            => $addressIds->ALC_Id,
        'CT_Id'             => $addressIds->CT_Id,        
        'ST_Id'             => $addressIds->ST_Id, 
        'AJ_Village'        => '', 
        'AJ_Pincode'        => '', 
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

        $date = new DateTime();
        $uniqueId = floor(rand() + $date->getTimestamp() + rand());

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
    if($AJ_Id) echo $AJ_Id;      
    else echo 'fail';
    
    
//    $jobAutomateData = (array) json_decode($_REQUEST['AE_JobAutomateData']);
//    if(!empty($jobAutomateData))
//        $AttObj->InsertData['AE_AutomateData'] = $_REQUEST['AE_JobAutomateData'];
//
//    $Result = $AttObj->insertReturnId('attestation_job_enquiry');
//    if( $Result != 'fail' ) echo $Result;
//    else echo 'fail';
}
?>