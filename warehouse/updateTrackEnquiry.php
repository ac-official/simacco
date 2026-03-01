<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->Data = array (
        'AE_Name'   => htmlspecialchars(trim($_REQUEST['AE_Name']), ENT_QUOTES),
        'AE_Mobile' => htmlspecialchars(trim($_REQUEST['AE_Mobile']), ENT_QUOTES),
        'AE_Email'  => htmlspecialchars(trim($_REQUEST['AE_Email']), ENT_QUOTES),
        'AE_Remarks'=> htmlspecialchars(trim($_REQUEST['AE_Remarks']), ENT_QUOTES),
        'AE_Status' => htmlspecialchars(trim($_REQUEST['AE_Status']), ENT_QUOTES)
    );
echo $AttObj->updateRecords('attestation_job_enquiry', 'AE_Id = '.$_REQUEST['AE_Id']);
?>
