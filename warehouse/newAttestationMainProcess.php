<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
include_once($BASEPATH . 'includes/saveGoogleAddress.php' );
$AttObj                 = new AttestationClass();
$APM_Title              = htmlspecialchars(trim($_REQUEST['APM_Title']), ENT_QUOTES);
$APM_Description        = htmlspecialchars(trim($_REQUEST['APM_Description']), ENT_QUOTES);
$APM_Title_Alias        = htmlspecialchars(trim($_REQUEST['APM_Title_Alias']), ENT_QUOTES);
$APMA_Title             = htmlspecialchars(trim($_REQUEST['APMA_Title']), ENT_QUOTES);
$APMA_Id                = htmlspecialchars(trim($_REQUEST['APMA_Id']), ENT_QUOTES);
$AttObj->Data = array(   
    'APM_Id'            => $_REQUEST['APM_Id'], 
    'OF_Id'             => $preTally_user_ofid,
    'APM_Title'         => htmlspecialchars(trim($_REQUEST['APM_Title']), ENT_QUOTES), 
    'APM_Title_Alias'   => htmlspecialchars(trim($_REQUEST['APM_Title_Alias']), ENT_QUOTES),
    'APMA_Id'           => htmlspecialchars(trim($_REQUEST['APMA_Id']), ENT_QUOTES),
    'APM_Description'   => htmlspecialchars(trim($_REQUEST['APM_Description']), ENT_QUOTES),
    'APM_Status'        => htmlspecialchars(trim($_REQUEST['APM_Status']), ENT_QUOTES)
);

if($_REQUEST['APM_Id'] != 0){
        $dataUpdate = $AttObj->getRowDetails('attestation_process_main', 'APM_Id', ' WHERE APM_Title = "'.$_REQUEST['APM_Title'].'" AND APM_Id != "'.$AttObj->Data['APM_Id'].'" AND OF_Id = "'.$preTally_user_ofid.'"' ) ;  
        if(!$dataUpdate['APM_Id'] ){
                if($AttObj->updateRecords('attestation_process_main','APM_Id = '.$_REQUEST['APM_Id']) == "success")
                    echo "Main Process successfully updated";
                else
                    echo "fail";
        } 
        else echo 'Main Process already exists';     
 } 
 else{
        if($dataInsertCheck=$AttObj->getMainProcessValueInsert('attestation_process_main', 'APM_Id', ' WHERE APM_Title = "'.$_REQUEST['APM_Title'].'" AND APM_Id != "'.$AttObj->Data['APM_Id'].'" AND OF_Id = "'.$preTally_user_ofid.'"' ) ) {   
            echo 'Main Process already exists';
        } 
        else{
                if($AttObj->insertRecords('attestation_process_main') == "success")
                    echo "Main Process successfully created";
                else
                    echo 'fail';
        }
     
}