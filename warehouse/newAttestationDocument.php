<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->Data = array(        
    'ADOC_Document' => htmlspecialchars(trim($_REQUEST['ADOC_Document']), ENT_QUOTES),  
    'OF_Id'         => htmlspecialchars(trim($preTally_user_ofid), ENT_QUOTES), 
    'ADOC_Type'   => htmlspecialchars(trim($_REQUEST['ADOC_Type']), ENT_QUOTES),
    'ADOC_Status'   => htmlspecialchars(trim($_REQUEST['ADOC_Status']), ENT_QUOTES)
);

if($AttObj->getValue('attestation_documents', 'ADOC_Id', ' WHERE ADOC_Document = "'.$_REQUEST['ADOC_Document'].'" AND ADOC_Id != '.$_REQUEST['ADOC_Id'].' AND OF_Id = '.$preTally_user_ofid)) {
    echo "Document already exists";
}else{
    if($_REQUEST['ADOC_Id'] != 0) {
        if($AttObj->updateRecords('attestation_documents','ADOC_Id = '.$_REQUEST['ADOC_Id']) == "success")
            echo "Document successfully updated";
        else
            echo "fail";
    }else{
        if($AttObj->insertRecords('attestation_documents') == "success")
            echo "Document successfully created";
        else
            echo "fail";
    }
}
       

/**
if($_REQUEST['ADOC_Id'] != 0) { // update
    if($AttObj->updateRecords('attestation_documents','ADOC_Id = '.$_REQUEST['ADOC_Id']) == "success")
        echo "Document successfully updated";
    else
        echo "fail";
}else {  // insert
    if($AttObj->getValue('attestation_documents', 'ADOC_Id', ' WHERE ADOC_Document = "'.$_REQUEST['ADOC_Document'].'" AND OF_Id = '.$preTally_user_ofid)) 
        echo "Document already exists";
    else {
        if($AttObj->insertRecords('attestation_documents') == "success")
            echo "Document successfully created";
        else
            echo "fail";
    }
}
 * 
 */