<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$DOCIds = explode(",",htmlspecialchars(trim($_REQUEST['AJD_Id']), ENT_QUOTES));

$AttObj->InsertData = array(        
    'AJG_Name'   => htmlspecialchars(trim($_REQUEST['AJG_Name']), ENT_QUOTES),
    'AJG_MDate'  => date('Y-m-d'),
    'AJG_Status' => 1
);

if($_REQUEST['AJG_Id'] == 0) { 
    $AttObj->InsertData['AJG_Cdate']  = date('Y-m-d');
    $groupName = $AttObj->InsertData['AJG_Number'] = $AttObj->generateGroupNumber('attestation_job_group', 'AJG_Number', 'ORDER BY AJG_Id DESC LIMIT 1');
    
    $AJGId = $AttObj->insertReturnId('attestation_job_group');
    if($AJGId) {
        foreach ($DOCIds as $key => $value) {
            $AttObj->Data[] = array(        
                'AJG_Id'        => $AJGId,  
                'AJD_Id'        => htmlspecialchars(trim($value), ENT_QUOTES),
                'AJGD_Status'   => 0
            );
        }
        $AttObj->insertMultipleData('attestation_job_group_doc');
        echo $groupName;
    } else {
        echo "fail";
    }
} else {
    
       unset($AttObj->Data);
       $AttObj->Data = array( 
                'AJG_Name'      => htmlspecialchars(trim($_REQUEST['AJG_Name']), ENT_QUOTES),
                'AJG_MDate'  => date('Y-m-d')
            );
    if($AttObj->updateRecords('attestation_job_group','AJG_Id = '.$_REQUEST['AJG_Id']) == "success"){
        if($AttObj->deleteRecords(' attestation_job_group_doc',' WHERE AJG_Id = '.$_REQUEST['AJG_Id'])=="success") {
            unset($AttObj->Data);
            foreach ($DOCIds as $key => $value) {
                $AttObj->Data[] = array(        
                    'AJG_Id'        => htmlspecialchars(trim($_REQUEST['AJG_Id'], ENT_QUOTES)),  
                    'AJD_Id'        => htmlspecialchars(trim($value), ENT_QUOTES),
                    'AJGD_Status'   => 0
                );
            }
            $AttObj->insertMultipleData('attestation_job_group_doc');
            echo "updated";
        }else {
            echo "fail";
        }
    } else {
        echo "fail";
    }
}
?>
