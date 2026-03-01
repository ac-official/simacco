<?php
include_once($BASEPATH . "preTallyClass/ItemClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttestObj  = new AttestationClass();
$User_Obj   = new UserClass();
$IT_Obj     = new ItemClass();
$OffObj     = new OfficeClass();

/** ~imp
$JobAmount = $AttestObj->getValue('attestation_job_documents', 'AJD_Amount', " WHERE AJ_Id = ".$REQUEST['id']." ");
if($JobAmount){
   $JobReturnITId = $AttestObj->getValue('attestation_job_balsheet_settings', 'IT_Id ', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 4");
   $AttestObj->getDetails('attestation_job_details ', 'AJ_Track_Id,AJ_FName', " WHERE  AJ_Id = ".$REQUEST['id']." ");
   $JobDetailsArray = $AttestObj->DataArray;
   $JobReturnTRId  =  $JobDetailsArray[0]->AJ_Track_Id;
   $JobReturnUser  =  $JobDetailsArray[0]->AJ_FName;
   
   
    $rptPntTree = array();
    $DS_Notf = $IT_Obj->getReportingTree($preTally_user_id,$rptPntTree);
    $offAdm  = $OffObj->offzAdmin($preTally_user_ofid);
        
    if($offAdm == $preTally_user_id) {
        $DS_Approval = 0;
        $Status      = 1;
    }else if( $ACL_Obj->ACL_Item == 1 ){
        $DS_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status = 1;
    } else {
        $DS_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status      = 2;
    }
   
   $AttestObj->InsertData = array(  
        'US_Id'             => $preTally_user_id,
        'IT_Id'             => $JobReturnITId,
        'OF_Id'             => $preTally_user_ofid,
        'DS_Description'    => $JobReturnUser,
        'DS_Approval'       => $DS_Approval,
        'DS_Approved'       => $preTally_user_id,
        'DS_Notf'           => $DS_Notf,
        'DS_Status'         => $Status,
        'DS_CDate'          => date('Y-m-d H:i:s'),
        'DS_MDate'          => date('Y-m-d H:i:s')  
    );
    $JobReturnDSId = $AttestObj->getValue('descriptions', 'DS_Id', " WHERE DS_Description = '".$AttestObj->InsertData['DS_Description']."' AND IT_Id = ".$AttestObj->InsertData['IT_Id']." AND OF_Id = ".$preTally_user_ofid." AND DS_Status != 4");
    if(!$JobReturnDSId) {
        $JobReturnDSId = $AttestObj->insertReturnId('descriptions');
    }

    $AttestObj->Data = array(  
        'US_Id'         => $preTally_user_id,
        'IT_Id'         => $JobReturnITId,
        'LC_Id'         => $preTally_user_lcid,
        'PM_Id'         => 1,
        'BS_Amount'     => $JobAmount,
        'BS_Description'=> $JobReturnDSId,
        'TR_Id'         => $JobReturnTRId,
        'BS_DualEntry'  => 0,
        'BS_Complete'   => 1,
        'BS_PaidDate'   => date('Y-m-d H:i:s'),
        'BS_Date'       => date('Y-m-d H:i:s'),
        'BS_CDate'      => date('Y-m-d H:i:s'),
        'BS_MDate'      => date('Y-m-d H:i:s'),
        'BS_Status'     => 1
    );
    $AttestObj->insertRecords('balance_sheets');
}
 * 
 */

if($REQUEST['type'] == 'delete')
    $AJ_Status = 2; //Deleted
else
    $AJ_Status = 0; //Incompleted Registration
$AttestObj->Data = array(
    'AJ_Status' => $AJ_Status
);                          // cancel job
$AttestObj->updateRecords('attestation_job_details','AJ_Id = '.$REQUEST['id']);