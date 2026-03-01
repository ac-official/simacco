<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'includes/saveGoogleAddress.php' );
$AttObj = new AttestationClass();

if(trim($_REQUEST['clientNo']) != 0 ){
    $AttObj->getDetails('attestation_job_enquiry','*',' WHERE AE_Mobile = '.trim($_REQUEST['clientNo']).' AND AE_Status = 1 ');
    $CandDetails = $AttObj->DataArray;
}


$APSData  =  substr($_REQUEST['APS_Id'], 1, -1);
$APSArray =  explode(',', $APSData);

$addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street'], ENT_QUOTES)),
);
$addressIds = json_decode(saveGoglAddress($addressArray));

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
    'AE_Pincode'        => 0,//htmlspecialchars(trim($_REQUEST['GOGL_Pincode']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['GOGL_Pincode']), ENT_QUOTES) : $CandDetails[0]->AE_Pincode, 
//    'AE_Cust_Loc'      => $AP_Id ? $AP_Id : $CandDetails[0]->AE_Cust_Loc , 
    'AE_Certificate'    => htmlspecialchars(trim($_REQUEST['ADOC_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['ADOC_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Certificate , 
    'APS_Id'            => htmlspecialchars(trim($_REQUEST['APS_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['APS_Id']), ENT_QUOTES) : $CandDetails[0]->APS_Id ,
    'AE_LastProcess'    => htmlspecialchars(trim($_REQUEST['AE_LastProcess']), ENT_QUOTES) !== '' ? htmlspecialchars(trim($_REQUEST['AE_LastProcess']), ENT_QUOTES) : $CandDetails[0]->AE_LastProcess ,
    'AE_Authorities'    => 0,
//    'AE_State'        => 0, 
//    'AE_Authorities'    => htmlspecialchars(trim($_REQUEST['AAUTH_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AAUTH_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Authorities , 
    'AE_Year'           => htmlspecialchars(trim($_REQUEST['AA_IssuedYear']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_IssuedYear']), ENT_QUOTES) : $CandDetails[0]->AE_Year , 
    'AE_Certi_Mode'     => $_REQUEST['AA_CourseType'] ? $_REQUEST['AA_CourseType'] : $CandDetails[0]->AE_Certi_Mode ,  
    'AE_Certi_StudyLoc' => htmlspecialchars(trim($_REQUEST['AA_Issuing_CNId']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_Issuing_CNId']), ENT_QUOTES) : $CandDetails[0]->AE_Certi_StudyLoc , 
    'AE_Att_For'        => htmlspecialchars(trim($_REQUEST['AA_VisaType']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['AA_VisaType']), ENT_QUOTES) : $CandDetails[0]->AA_VisaType ,   
    'AE_Visiting_CNId'  => htmlspecialchars(trim($_REQUEST['CN_Id']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['CN_Id']), ENT_QUOTES) : $CandDetails[0]->AE_Visiting_CNId , 
    'AE_Email'          => htmlspecialchars(trim($_REQUEST['clientEmail']), ENT_QUOTES) ? htmlspecialchars(trim($_REQUEST['clientEmail']), ENT_QUOTES) : $CandDetails[0]->AE_Email , 
//    'AE_AutomateData'   => $_REQUEST['AE_JobAutomateData'],
    'AE_Remarks'        => '',
//    'AE_CDate'          => date('Y-m-d'),
    'AE_Status'         => 1
);

if($CandDetails[0]){
    $AttObj->Data = $AttObj->InsertData;
    if($AttObj->updateRecords('attestation_job_enquiry', ' AE_Id = '.$CandDetails[0]->AE_Id.' ') !='success' ){
        echo 'fail';
    }else echo $CandDetails[0]->AE_Id;
}else{
    $AttObj->InsertData['AE_CDate']        = date('Y-m-d');
    $AttObj->InsertData['AE_AutomateData'] = '';
    $AE_Id = $AttObj->insertReturnId('attestation_job_enquiry') ;
    echo $AE_Id;
//    if($AttObj->insertReturnId('attestation_job_enquiry') == 'fail'){
//        echo 'fail';
//    }else echo 'success';
}    
?>