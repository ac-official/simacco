<?php
include_once($BASEPATH . "/preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$UnderProDocIdArray = array();
$CompleteDocIdArray = array();

$docRefArray = $REQUEST['AJDRD_Id'];


$BId = $REQUEST['BId'];
$docRefId = implode(',', $docRefArray);

$AttObj->getDetails('attestation_job_doc_reference_details AS AJDR', 
        'AJDR.AJD_Id,AJDR.AJG_Id,AJDR.Send_LC_Id,AJD.AJD_Status', 
        'LEFT JOIN attestation_job_documents AS AJD ON AJD.AJD_Id = AJDR.AJD_Id WHERE AJDR.AJDRD_Id IN ('.$docRefId.') AND AJDR.AJDRD_Status = 1');
$resultArray = $AttObj->DataArray;

$value = ' ';
foreach ($resultArray as $rw) {
    if($value!=' '){
        $value .=',';
        $docId .=',';
    } 
    $value .="('".$rw->AJD_Id."','".$rw->AJG_Id."','".$rw->Send_LC_Id."','".$preTally_user_lcid."','".$BId."','2','".date('Y-m-d')."')";
    $docId .=$rw->AJD_Id;
    
    
    $JobTrackingData[] = array(   // attestation_job_documents 
        'US_Id'         => $preTally_user_id,
        'LC_Id'         => $preTally_user_lcid,
//        'AJ_Id'         => $rw->AJ_Id,
        'AJG_Id'        => $rw->AJG_Id,
        'AJD_Id'        => $rw->AJD_Id,
        'APS_Id'        => 0,
        'AJT_FromLC'    => $rw->Send_LC_Id,
        'AJT_ToLC'      => 0,
        'AJT_ReceiveLC' => $preTally_user_lcid,
//        'AJ_Status'     => 0,
//        'AJD_Status'    => 0, // Document in Transit
        'AJS_Status'    => 0,
        'AJT_CDate'     => date('Y-m-d H:i:s'),
        'AJT_Status'    => 4
    ); 
}

$AttObj->getDetails('attestation_job_subprocess', 'AJD_Id,APS_Id,AJS_Status',' WHERE AJD_Id IN ('.$docId.')');

foreach ($AttObj->DataArray as $rw) {
        if(($rw->AJS_Status=='1' || $rw->AJS_Status=='2') && !(in_array($rw->AJD_Id, $UnderProDocIdArray))) {
            array_push($UnderProDocIdArray, $rw->AJD_Id);
        }else{
            array_push($CompleteDocIdArray, $rw->AJD_Id);
        }
}
$UndrProDocId=implode(",", array_unique($UnderProDocIdArray));
$CompleteDocId=implode(",", array_unique($CompleteDocIdArray));
if(!empty($CompleteDocId)) {
    $AttObj->Data =array(
        'AJD_Status'=> 4
    );
    if($AttObj->updateRecords('attestation_job_documents',' AJD_Id IN ('.$CompleteDocId.')') != 'success') {
        echo 'fail';
        exit();
    }
}
$AttObj->Data = array(
    'AJD_Status' => 2
);
$result = $AttObj->reciveDocument($value,$UndrProDocId);

if($result=='success') {
    //start Job Status change
    $jobId = $AttObj->getDistinctIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN( '.$docId.') ');
        $AJIdArray = explode(',', $jobId);
        $AttObj->UpdateData = ' AJ_Status = CASE ' ;
        foreach ($AJIdArray as $AJId) {
            $PC_Count = $AttObj->getValue('attestation_job_documents', ' COUNT(AJ_Id) AS COUNT ', 'WHERE AJ_Id = '.$AJId.' AND AJD_Status NOT IN (4) '); //Id's of Subprocess that are in Pending/Sumbitted
            if($PC_Count || $PC_Count != 0 ){
                $AttObj->UpdateData .= 'WHEN AJ_Id = '.$AJId.' THEN 3'; //Job Status become "Under Process ."
            }else $AttObj->UpdateData .= 'WHEN AJ_Id = '.$AJId.' THEN 4 ';//Job Status become "Job Completed."
        }
        $AttObj->UpdateData .= ' ELSE AJ_Status END ';
        
        if($AttObj->updateMultipleRecords('attestation_job_details', 'AJ_Id IN ('.$jobId.')') != 'success'){
           echo 'fail';
        }
    //End Job Status Change
        
    $AttObj->getDocTrackData($docId);
    $JobDetails = $AttObj->docArrayList;   
  
    foreach ($JobTrackingData as $key => $value) {
        $JobTrackingData[$key]['AJ_Id']      =  $JobDetails[$value['AJD_Id']]->AJ_Id ;
        $JobTrackingData[$key]['AJ_Status']  =  $JobDetails[$value['AJD_Id']]->AJ_Status ;
        $JobTrackingData[$key]['AJD_Status'] =  $JobDetails[$value['AJD_Id']]->AJD_Status ;
    }

    unset($AttObj->Data);
    $AttObj->Data = $JobTrackingData;
    $AttObj->insertMultipleData('attestation_job_tracking');
            
    $docList = $AttObj->getIdsList('attestation_job_doc_reference_details', 'AJD_Id', 'WHERE AJNB_Id = '.$BId.' AND AJDRD_Status = 1');
    $status  = $AttObj->getIdsList('attestation_job_documents', 'AJD_Status', 'WHERE AJD_Id IN ('.$docList.')');
    if(!mb_substr_count("$status", "3")){
        $AttObj->Data = array(
            'AJNB_Status' => 2
        );
        if($AttObj->updateRecords('attestation_job_notifi_batch', 'AJNB_Id = '.$BId)!='success') {
            echo 'fail';
            exit();
        }else {
            echo 'success';
        }
    }else {
        echo 'success';
    }
} else {
    echo 'fail';
}





    







//die();
//if($result=='success') {
//    //start Job Status change
//    $Aj_Id = $AttObj->getIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN ('.$docId.')');
//    $JobIdListArray = $AttObj->getJobDetails($Aj_Id);
//    foreach ($AttObj->DataArray as $jobRw) {
//       $count =0;
//       foreach ($jobRw as $rw) {
//           if($rw->AJD_Status==4)
//                $count++;
//       }
//       if(count($jobRw)==$count){
//            $UpdateAJ_Status = 4; //Job Status become "Job Completed"
//        } else {
//            $UpdateAJ_Status = 3; //Job Status become "Job Under Process"
//        }   
//        $AttObj->Data = array(
//            'AJ_Status' => $UpdateAJ_Status
//        );
//        if($AttObj->updateRecords('attestation_job_details', 'AJ_Id = '.$rw->AJ_Id) != 'success') {
//            echo 'fail';
//            exit();
//        }
//    }
//    //End Job Status Change
//    
//    $docList = $AttObj->getIdsList('attestation_job_doc_reference_details', 'AJD_Id', 'WHERE AJNB_Id = '.$BId.' AND AJDRD_Status = 1');
//    $status  = $AttObj->getIdsList('attestation_job_documents', 'AJD_Status', 'WHERE AJD_Id IN ('.$docList.')');
//    if(!mb_substr_count("$status", "3")){
//        $AttObj->Data = array(
//            'AJNB_Status' => 2
//        );
//        if($AttObj->updateRecords('attestation_job_notifi_batch', 'AJNB_Id = '.$BId)!='success') {
//            echo 'fail';
//            exit();
//        }else {
//            echo 'success';
//        }
//    }else {
//        echo 'success';
//    }
//} else {
//    echo 'fail';
//}
?>