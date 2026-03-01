<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$filter = " AJDRD.Receive_LC_Id = $preTally_user_lcid AND AJNB.AJNB_Status != 2 AND AJDRD.AJDRD_Status = 1 ";
$AttObj->getBatchDetails($filter,0,6);

if(Count($AttObj->DataArray) > 5){
    array_push($AttObj->DataArray, array('AJNB_Id' =>'More','AJNB_Description' => "<div style = 'font-weight: bold; margin-left: 25%;'>See More</div>"));
}
echo json_encode($AttObj->DataArray);








//die();
//$batchIds = $AttObj->getIdsList('attestation_job_notifi_batch', 'AJNB_Id', 'WHERE AJNB_Status = 1');
//$batchIdArray = explode(',', $batchIds);
//foreach ($batchIdArray as $batchId) {
//    $SenddocIds = $AttObj->getDetails('attestation_job_doc_reference_details', 'AJD_Id', 'WHERE AJNB_Id = '.$batchId.' AND AJDRD_Status  = 1');
//    $sendDocCount = count($AttObj->DataArray);
//    $RedocIds = $AttObj->getDetails('attestation_job_doc_reference_details', 'AJD_Id', 'WHERE AJNB_Id = '.$batchId.' AND AJDRD_Status  = 2');
//    $ReDocCount = count($AttObj->DataArray);
//    //$status  = $AttObj->getIdsList('attestation_job_documents', 'AJD_Status', 'WHERE AJD_Id IN ('.$docIds.')');
//    if($sendDocCount==$ReDocCount){
//        $AttObj->Data = array(
//            'AJNB_Status' => 2
//        );
//        if($AttObj->updateRecords('attestation_job_notifi_batch', 'AJNB_Id = '.$batchId)!='success') {
//           echo json_encode('fail');
//            exit();
//        }
//    }
//}
//$AttObj->getBatchDetails('attestation_job_notifi_batch AS AJNB', 
//                    'AJNB.AJNB_Id,AJNB.AJNB_Description,COUNT(DISTINCT AJDRD.AJDRD_Id) AS NO_DOC,AJNB.AJNB_Status', 
//                    'LEFT JOIN attestation_job_doc_reference_details AS AJDRD ON AJDRD.AJNB_Id = AJNB.AJNB_Id WHERE AJNB.AJNB_Status != 2 AND AJDRD.AJDRD_Status = 1 AND AJDRD.Receive_LC_Id = '.$preTally_user_lcid.' GROUP BY AJNB.AJNB_Id ORDER BY AJNB.AJNB_Cdate DESC LIMIT 0,6'
//                );

//if(Count($AttObj->DataArray)>5){
//    array_push($AttObj->DataArray, array('AJNB_Id' =>'More','AJNB_Description' => "<div style = 'font-weight: bold; margin-left: 25%;'>See More</div>"));
//}
//echo json_encode($AttObj->DataArray);

?>