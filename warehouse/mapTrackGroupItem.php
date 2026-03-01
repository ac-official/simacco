<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$errorCount = 0;
$j=1;
$upStatus=0;
foreach($_REQUEST as $key=>$value) {
    if(is_numeric($key)) {
        $AttObj->Data = array(        
            'AJG_Id'        => htmlspecialchars(trim($_REQUEST[$key]['AJG_Id']), ENT_QUOTES),  
            'AJD_Id'        => htmlspecialchars(trim($_REQUEST[$key]['AJD_Id']), ENT_QUOTES),
            'AJGD_Status'   => 0
        );
    }
    if($_REQUEST[$key]['upStatus']==1) {
        $upStatus=1;
    }
    
    $result = $AttObj->insertRecords(' attestation_job_group_doc');
    
    if($result == "fail" && $j!= 1) {
        $errorCount++;
        $deltResult = $AttObj->deleteRecords('attestation_job_group_doc','WHERE AJG_Id='.$AttObj->Data[AJS_Id]);
    } 
    $j++;
}
if($errorCount) {
    echo "fail";
}else {
    //success
    if($upStatus){//Update
        echo 'update';
    } else {//Insert
        $grpNo = $AttObj->getValue ('attestation_job_group', 'AJG_Number', 'WHERE AJG_Id ='.$AttObj->Data['AJG_Id']);
        echo $grpNo;
    }
}
?>