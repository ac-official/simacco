<?php

include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj     = new AttestationClass();

$DocDetails = $DOCArray = json_decode($_REQUEST['DOC_Array']);   // documents array
$AJ_Id    = $_REQUEST['AJ_Id'];

$AJD_Id  = $AttObj->getValue('attestation_job_documents', ' AJD_Id ', ' WHERE ADOC_Id = '.$DocDetails->ADOC_Id.' AND AST_Id = '.$DocDetails->ST_Id.' AND AAUTH_Id = '.$DocDetails->AAUTH_Id.' AND AJ_Id = '.$AJ_Id.' AND AJD_UniqueNo = '.$DocDetails->AJD_UniqueNo);

$AttObj->Data = array(   // attestation_job_documents 
    'US_Id'         => $preTally_user_id,
    'LC_Id'         => $preTally_user_lcid,
    'AJ_Id'         => $AJ_Id,
    'AJG_Id'        => 0,
    'AJD_Id'        => $AJD_Id,
    'APS_Id'        => 0,
    'AJT_FromLC'    => 0,
    'AJT_ToLC'      => 0,
    'AJT_ReceiveLC' => 0,
    'AJ_Status'     => 1,
    'AJD_Status'    => 0,
    'AJS_Status'    => 0,
    'AJT_CDate'     => date('Y-m-d H:i:s'),
    'AJT_Status'    => 2
);                  // certifi

$AttObj->insertRecords('attestation_job_tracking');   
