<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->Data = array (
        'AJ_Comments' => htmlspecialchars(trim($_REQUEST['jobComment']), ENT_QUOTES)
    );
echo $AttObj->updateRecords('attestation_job_details', 'AJ_Id = '.$_REQUEST['TrackJobId']);
?>
