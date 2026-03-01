<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$AJG_Id     = $_REQUEST['gid'] ? trim(htmlspecialchars($_REQUEST['gid'], ENT_QUOTES)) : 0 ;
$AJD_Ids    = trim(htmlspecialchars($REQUEST['docId'], ENT_QUOTES));
$DocIdArray = explode(',',$AJD_Ids);

//Insert Document Reference Details
foreach ($DocIdArray as $rw) {
    $AttObj->Data[] = array(
        'AJD_Id'        =>  $rw,
        'AJG_Id'        =>  $AJG_Id,
        'Send_LC_Id'    =>  $preTally_user_lcid,
        'Receive_LC_Id' =>  0,
        'AJNB_Id'       =>  0,
        'AJDRD_Status'  =>  3,
        'AJDRD_CDate'   =>  date('Y-m-d')
    );
    
    $JobTrackingData[] = array( 
        'US_Id'         => $preTally_user_id,
        'LC_Id'         => $preTally_user_lcid,
//        'AJ_Id'         => 0,
        'AJG_Id'        => $AJG_Id,
        'AJD_Id'        => $rw,
        'APS_Id'        => 0,
        'AJT_FromLC'    => $preTally_user_lcid,
        'AJT_ToLC'      => 0,
        'AJT_ReceiveLC' => 0,
//        'AJ_Status'     => 0,
//        'AJD_Status'    => 3, // Document in Transit
        'AJS_Status'    => 0,
        'AJT_CDate'     => date('Y-m-d H:i:s'),
        'AJT_Status'    => 6
    ); 

}
if($AttObj->insertMultipleData('attestation_job_doc_reference_details')=='success') {
    
    if($AJG_Id !=0) {
        // Insert Group Reference Details
        $AttObj->InsertData = array(
            'AJG_Id'        => $AJG_Id,
            'LC_Id'         => $preTally_user_lcid,
            'AJGRD_Status'  => 3,
            'AJGRD_CDate'   => date('Y-m-d')
        );
        if($AttObj->insertReturnId('attestation_job_group_reference_details') == 'fail'){
            echo 'fail';
        }
    }
    //Document Reference Details  Insert Successfuly, Change Document Status in to "Deliverd"
    unset($AttObj->Data);
    $AttObj->Data = array(
        'AJD_Status' => 5,
        'AJD_DeliveryRemarks' => trim(htmlspecialchars($REQUEST['delvRemarks'], ENT_QUOTES))
    );
    if($AttObj->updateRecords('attestation_job_documents', ' AJD_Id IN ('.$AJD_Ids.')') =='success') {
        //Job Status change
        $jobId = $AttObj->getDistinctIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN( '.$AJD_Ids.') ');
        $AJIdArray = explode(',', $jobId);

        $AttObj->UpdateData = ' AJ_Status = CASE ' ;
        foreach ($AJIdArray as $AJId) {
            $PD_Count = $AttObj->getValue('attestation_job_documents', ' COUNT(AJ_Id) AS COUNT ', 'WHERE AJ_Id = '.$AJId.' AND AJD_Status NOT IN (5) '); 
            if(!$PD_Count || $PD_Count == 0 ){
                $AttObj->UpdateData .= ' WHEN AJ_Id = '.$AJId.' THEN 5 '; //Job Status become "Delivered."
            }
        }
        $AttObj->UpdateData .= ' ELSE AJ_Status END ';
        
        if($AttObj->updateMultipleRecords('attestation_job_details', 'AJ_Id IN ('.$jobId.')') != 'success'){
           echo 'fail';
        }else{
            $AttObj->getDocTrackData($AJD_Ids);
            $JobDetails = $AttObj->docArrayList;   

            foreach ($JobTrackingData as $key => $value) {
                $JobTrackingData[$key]['AJ_Id']      =  $JobDetails[$value['AJD_Id']]->AJ_Id ;
                $JobTrackingData[$key]['AJ_Status']  =  $JobDetails[$value['AJD_Id']]->AJ_Status ;
                $JobTrackingData[$key]['AJD_Status'] =  $JobDetails[$value['AJD_Id']]->AJD_Status ;
            }

            unset($AttObj->Data);
            $AttObj->Data = $JobTrackingData;
            $AttObj->insertMultipleData('attestation_job_tracking');
        }
        echo 'success'; //End Job Status Change
    }else
        echo 'fail';
}else 
    echo 'fail';
?>