<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->Data = array (
        'AE_Status' => 4
    );
$AttObj->updateRecords(' attestation_job_enquiry', 'AE_Id = '.$_REQUEST['AE_Id']);
?>
