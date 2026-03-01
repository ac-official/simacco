<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$AttObj->Data = array(
    'AA_Status' => $_REQUEST['AAStatus'] == 1 ? 0 : 1
);
$AttObj->updateRecords('attestation_automate', ' AA_Id = '.$_REQUEST['AAId']);

?>