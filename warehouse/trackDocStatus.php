<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$docId = implode(",", $_REQUEST['docId']);
$AttObj->getDetails('attestation_job_documents', 'AJD_Status', 'WHERE AJD_Id IN ('.$docId.')');
$resultArray = $AttObj->DataArray;
foreach ($resultArray as $rw) {
    if(in_array($rw->AJD_Status, $_REQUEST['status'])){
        echo 'fail';
        exit();
    } 
}
echo 'success';
?>