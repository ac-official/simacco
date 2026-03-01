<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttestObj  = new AttestationClass();

//print_r($_REQUEST); die("Welcome");
$AttestObj->Data = array(
    'AE_CancelStatus'   => $REQUEST['TAPCancel'], 
    'AE_CancelReason'   => $REQUEST['TAPCancel_Reason'], 
//    'AE_MDate'          => date('Y-m-d') ,
    'AE_Status'         => 3  // cancel enquiry
);                         
if($AttestObj->updateRecords('attestation_job_enquiry','AE_Id = '.$REQUEST['id']) == 'success')
    echo "Process Cancelled successfully.";
else 
    echo "Some Error Occured. Please Re-Try.";

?>