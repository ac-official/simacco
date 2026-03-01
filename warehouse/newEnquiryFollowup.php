<?php 

require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttObj = new AttestationClass();

$AttObj->Data = array(
    'AE_Id'         => $_REQUEST['AE_Id'],
    'US_Id'         => $preTally_user_id,
    'OF_Id'         => $preTally_user_ofid,
    'LC_Id'         => $preTally_user_lcid,
    'EF_Comments'   => $_REQUEST['EF_Comments'],
    'EF_NextDate'   => $_REQUEST['EF_NextDate'],
    'EF_Chance'     => $_REQUEST['EF_Chance'],    
    'EF_CDate'      => date('Y-m-d H:i:s'),
    'EF_Status'     => 1
);
if($AttObj->insertRecords('attestation_enquiry_followup') != 'fail'){     
    $AttObj->UpdateData = ' AE_Status = CASE WHEN AE_Status = 1 THEN 4 ELSE AE_Status END' ;
    if($AttObj->updateMultipleRecords(' attestation_job_enquiry', ' AE_Id = '.$_REQUEST['AE_Id']) != 'success'){
        echo 'fail';
    }else
        echo "success";
}else{
    echo "fail";
}

?>