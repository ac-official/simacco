<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$AttObj->Data = array(        
    'ASD_Document' => htmlspecialchars(trim($_REQUEST['ASD_Document']), ENT_QUOTES),  
    'OF_Id'        => htmlspecialchars(trim($preTally_user_ofid), ENT_QUOTES), 
    'ASD_Status'   => htmlspecialchars(trim($_REQUEST['ASD_Status']), ENT_QUOTES)
);

if($_REQUEST['ASD_Id'] != 0) { // update
    if($AttObj->updateRecords('attestation_supporting_documents','ASD_Id = '.$_REQUEST['ASD_Id']) == "success")
        echo "Document successfully updated";
    else
        echo "fail";
} else {  // insert
    if($AttObj->getValue('attestation_supporting_documents', 'ASD_Id', ' WHERE ASD_Document = "'.$_REQUEST['ASD_Document'].'" AND OF_Id = '.$preTally_user_ofid)) 
        echo "Document already exists";
    else {
        if($AttObj->insertRecords('attestation_supporting_documents') == "success")
            echo "Document successfully created";
        else
            echo "fail";
    }
}