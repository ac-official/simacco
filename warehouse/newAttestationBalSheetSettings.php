<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttObj = new AttestationClass();
$AJBS_Id = htmlspecialchars(trim($_REQUEST['AJB_Id']), ENT_QUOTES);
 if(!is_numeric($AJBS_Id)) { //Insert into attestation_job_balsheet_settings
     $AttObj->Data = array(
        'OF_Id'         => $preTally_user_ofid,
        'IT_Id'         => htmlspecialchars(trim($_REQUEST['AJB_IT']), ENT_QUOTES),
        'AJBS_Type'     => htmlspecialchars(trim($_REQUEST['AJBS_Type']), ENT_QUOTES),
        'AJBS_Status'   => '1'
     );
    if($AttObj->insertRecords('attestation_job_balsheet_settings') == 'success') 
        echo "Balancesheet Settings have been Successfully Saved.";
    else 
        echo "fail";
     
 } else {
    $AttObj->Data = array(        
        'IT_Id'         => htmlspecialchars(trim($_REQUEST['AJB_IT']), ENT_QUOTES), 
        'AJBS_Status'   => '1'
    );

    if($_REQUEST['AJB_Id'] != 0) { // update
        if($AttObj->getValue('attestation_job_balsheet_settings', 'AJBS_Id', ' WHERE IT_Id = "'.$AttObj->Data['IT_Id'].'" AND AJBS_Status = "'.$AttObj->Data['AJBS_Status'])) 
            echo "Authority successfully updated";
        else {
            if($AttObj->updateRecords('attestation_job_balsheet_settings','AJBS_Id = '.$AJBS_Id) == "success")
                echo "Balancesheet Settings have been Successfully Updated";
            else
                echo "fail";
        }
    }
 }