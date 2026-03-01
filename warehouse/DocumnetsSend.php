<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
include_once($BASEPATH . "preTallyClass/ItemClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");
include_once($BASEPATH . "preTallyClass/DescriptionClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$AttObj     = new AttestationClass();
$DescObj    = new DescriptionClass();
$User_Obj   = new UserClass();
$IT_Obj     = new ItemClass();
$OffObj     = new OfficeClass();

$AJG_Id         = $_REQUEST['gid'] ? trim(htmlspecialchars($_REQUEST['gid'], ENT_QUOTES)) : 0 ;
$AJD_Ids        = trim(htmlspecialchars($REQUEST['docId'], ENT_QUOTES));
$LCId           = trim(htmlspecialchars($REQUEST['LC_Id'], ENT_QUOTES));
$AJED_Amount    = trim(htmlspecialchars($_REQUEST['AJED_Amount'], ENT_QUOTES));
$BS_Description = trim(htmlspecialchars($REQUEST['Description'], ENT_QUOTES));
$DocIdArray     = explode(',', $AJD_Ids);
$AJNB_Id        = 0;

$DS_Id          = 0;

// To get Job Id and its Status
$AttObj->getDocTrackData($AJD_Ids);
$JobDetails = $AttObj->docArrayList;

/** ~imp

$Courier_Send_IT_Id = $AttObj->getValue('attestation_job_balsheet_settings', 'IT_Id', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 6");
$tempDSId = $AttObj->getValue('descriptions', 'DS_Id', 'WHERE DS_Description = "'.$BS_Description.'" AND IT_Id = "'.$Courier_Send_IT_Id.'"');
if($tempDSId == 0 || $tempDSId == '') {
    $rptPntTree = array();
    $IT_Notf = $IT_Obj->getReportingTree($preTally_user_id,$rptPntTree);
    $offAdm  = $OffObj->offzAdmin($preTally_user_ofid);

    if($offAdm == $preTally_user_id) {
        $IT_Approval = $preTally_user_id ;
        $DS_Approval = 0;
        $Status      = 1;
    }else if( $ACL_Obj->ACL_Item == 1 ){
        $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status = 1;
    } else {
        $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status      = 2;
    }
    
    $DescObj->DS_Data = array(   
        'US_Id'          => $preTally_user_id, 
        'IT_Id'          => $Courier_Send_IT_Id, 
        'OF_Id'          => $preTally_user_ofid,
        'DS_Description' => trim(htmlspecialchars($_REQUEST['BS_Description'], ENT_QUOTES)),
        'DS_Approval'    => $DS_Approval,
        'DS_Approved'    => $preTally_user_id,
        'DS_Notf'        => $IT_Notf,
        'DS_Status'      => $Status,
        'DS_CDate'       => date('Y-m-d H:i:s'),
        'DS_MDate'       => date('Y-m-d H:i:s')
        ); 
    
    $DS_Id = $DescObj->newBalSheetDescription();  
}  else {
    $DS_Id = $tempDSId;
}
* 
*/

//start insert data in to attestation_job_expense_description
$AttObj->Data = array(
    'US_Id'         => $preTally_user_id,
    'DS_Id'         => $DS_Id,
    'AJED_Amount'   => $AJED_Amount,
    'AJG_Id'        => $AJG_Id,
    'AJD_Id'        => $AJD_Ids,
    'AJED_Cdate'    => date('Y-m-d H:i:s')
);

if($AttObj->insertRecords('attestation_job_expense_description') != 'fail'){ //End insert data in to attestation_job_expense_description   
    unset($AttObj->Data);
    /** ~imp
    foreach ($DocIdArray as $rw) {  
        $singleDocAmount = $AJED_Amount / count($DocIdArray);
        $AttObj->Data[] = array(  
            'US_Id'         => $preTally_user_id,
            'IT_Id'         => $Courier_Send_IT_Id,
            'LC_Id'         => $preTally_user_lcid,
            'PM_Id'         => 1,
            'BS_Amount'     => $singleDocAmount,
            'BS_Description'=> $DS_Id,
//            'TR_Id'         => $AttObj->docArrayList[$rw]->AJ_Track_Id,
            'BS_DualEntry'  => 0,
            'BS_Complete'   => 1,
//            'BS_PaidDate'   => $AttObj->docArrayList[$rw]->AJ_ReceivedDate,
            'BS_PaidDate'   => date('Y-m-d H:i:s'),   
            'BS_Date'       => date('Y-m-d H:i:s'),
            'BS_CDate'      => date('Y-m-d H:i:s'),
            'BS_MDate'      => date('Y-m-d H:i:s'),
            'BS_Status'     => 1
        );
    }
    
    if($AttObj->insertMultipleData('balance_sheets') == 'success'){// end data insert balance_sheets
    
     * 
     */
        // Insert notification Batch Details
        $AttObj->InsertData = array(
            'AJNB_Description'  => 'From '.$preTally_user_lcname.' ('.date('d-m-Y').')',
            'AJNB_Cdate'        => date('Y-m-d H:i:s'),
            'AJNB_Status'       => 0
        );
        $AJNB_Id = $AttObj->insertReturnId('attestation_job_notifi_batch');
        if($AJNB_Id==0 || $AJNB_Id=='fail'){
            echo 'fail';
        }

        // Insert Group Reference Details
        unset($AttObj->InsertData);
        if($AJG_Id != 0 || !empty($AJG_Id)) { 
            $AttObj->InsertData = array(
                'AJG_Id'        => $AJG_Id,
                'LC_Id'         => $LCId,
                'AJGRD_Status'  => 1,
                'AJGRD_CDate'   => date('Y-m-d')
            );
            if($AttObj->insertReturnId('attestation_job_group_reference_details') == 'fail'){
                echo 'fail';
            }
        }

        //Insert Document Reference Details
        unset($AttObj->Data);
        foreach ($DocIdArray as $rw) {
            $AttObj->Data[] = array(
                'AJD_Id'        =>  $rw,
                'AJG_Id'        =>  $AJG_Id,
                'Send_LC_Id'    =>  $preTally_user_lcid,
                'Receive_LC_Id' =>  $LCId,
                'AJNB_Id'       =>  $AJNB_Id,
                'AJDRD_Status'  =>  1,
                'AJDRD_CDate'   =>  date('Y-m-d')
            );
            
            $JobTrackingData[] = array(   // attestation_job_documents 
                'US_Id'         => $preTally_user_id,
                'LC_Id'         => $preTally_user_lcid,
                'AJ_Id'         => $JobDetails[$rw]->AJ_Id,
                'AJG_Id'        => $AJG_Id,
                'AJD_Id'        => $rw,
                'APS_Id'        => 0,
                'AJT_FromLC'    => $preTally_user_lcid,
                'AJT_ToLC'      => $LCId,
                'AJT_ReceiveLC' => 0,
                'AJ_Status'     => $JobDetails[$rw]->AJ_Status,
                'AJD_Status'    => 3, // Document in Transit
                'AJS_Status'    => 0,
                'AJT_CDate'     => date('Y-m-d H:i:s'),
                'AJT_Status'    => 3
            ); 
        }
        if($AttObj->insertMultipleData('attestation_job_doc_reference_details')=='success') {
            //Document Reference Details  Insert Successfuly, Change Document Status in to "Transit"
            unset($AttObj->Data);
            $AttObj->Data = array(
                'AJD_Status' => 3
            );
            if($AttObj->updateRecords('attestation_job_documents', ' AJD_Id IN ('.$AJD_Ids.')') !='success') {
                echo 'fail';
            }
            unset($AttObj->Data);
            $AttObj->Data = $JobTrackingData;
            $AttObj->insertMultipleData('attestation_job_tracking');   
        }else 
            echo 'fail';

        echo 'success';
        /** ~imp
    }else 
        echo 'fail';
         * 
         */
}else
    echo 'fail';


















































/*

<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
include_once($BASEPATH . "preTallyClass/ItemClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");
include_once($BASEPATH . "preTallyClass/DescriptionClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$AttObj     = new AttestationClass();
$DescObj    = new DescriptionClass();
$User_Obj   = new UserClass();
$IT_Obj     = new ItemClass();
$OffObj     = new OfficeClass();
//print_r($REQUEST);

$AJG_Id         = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['gid']));
$AJD_Ids        = trim(mysqli_real_escape_string($GLOBALS['con'],($REQUEST['docId']));
$branchId       = trim(mysqli_real_escape_string($GLOBALS['con'],($REQUEST['branchId']));
$AJED_Amount    = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['AJED_Amount']));
$AJGRD_Id       = 0;
$AJNB_Id        = 0;
$value          = ' ';
$DocIdArray     = explode(',', $AJD_Ids);
$BS_Description = trim(mysqli_real_escape_string($GLOBALS['con'],($REQUEST['BS_Description']));
$paidToText         =trim(mysqli_real_escape_string($GLOBALS['con'],($REQUEST['paidTo']));
$temp           = 0;
$Courier_Send_IT_Id = $AttObj->getValue('attestation_job_balsheet_settings', 'IT_Id', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 6");

//if($_REQUEST['BS_Description'] && !is_numeric($_REQUEST['BS_Description'])) {    
    $rptPntTree = array();
    $IT_Notf = $IT_Obj->getReportingTree($preTally_user_id,$rptPntTree);
    $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
        
    if($offAdm == $preTally_user_id) {
        $IT_Approval = $preTally_user_id ;
        $DS_Approval = 0;
        $Status      = 1;
    }else if( $ACL_Obj->ACL_Item == 1 ){
        $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status = 1;
    } else {
        $DS_Approval = $IT_Approval = $User_Obj->myReportingPerson($preTally_user_id);
        $Status      = 2;
    }

    $DescObj->DS_Data = array(   
        'US_Id'          => $preTally_user_id, 
        'IT_Id'          => $Courier_Send_IT_Id, 
        'OF_Id'          => $preTally_user_ofid,
        'DS_Description' => $BS_Description,
        'DS_Approval'    => $DS_Approval,
        'DS_Approved'    => $preTally_user_id,
        'DS_Notf'        => $IT_Notf,
        'DS_Status'      => $Status,
        'DS_CDate'       => date('Y-m-d H:i:s'),
        'DS_MDate'       => date('Y-m-d H:i:s')
    );   
       $temp = $AttObj->getValue('descriptions', 'DS_Id', 'WHERE DS_Description = "'.$paidToText.'" AND IT_Id = "'.$Courier_Send_IT_Id.'"');
   
        //if(!$temp){
            //$temp = $AttObj->getValue('descriptions', 'DS_Id', 'WHERE DS_Id = "'.$BS_Description.'" AND IT_Id = "'.$Courier_Send_IT_Id.'"');
            //echo "third";
        //}
       
     
 
    if($temp == 0) { 
        $BS_Description = $DescObj->newBalSheetDescription();  
    }  else {
        $BS_Description = $temp;
    }

//    if(!$DescObj->verifyDescription('0')) {
//       $BS_Description = $DescObj->newBalSheetDescription();
//    }
//}

//start insert data in to attestation_job_expense_description
$AttObj->Data = array(
    'DS_Id'         => $BS_Description,
    'AJED_Amount'   => $AJED_Amount,
    'AJG_Id'        => $AJG_Id,
    'AJD_Id'        => htmlspecialchars($AJD_Ids),
    'US_Id'         => $preTally_user_id,
    'AJED_Cdate'    => date('Y-m-d H:i:s')
);

if($AttObj->insertRecords('attestation_job_expense_description') == 'success'){ //End insert data in to attestation_job_expense_description   
    $AttObj->getDocTrackData($AJD_Ids);
//start data insert into balance_sheets
    foreach ($DocIdArray as $rw) {
        
        if(!empty($rw)){
            $singleDocAmount = $AJED_Amount / count($DocIdArray);
             $AttObj->Data = array(  
            'US_Id'         => $preTally_user_id,
            'IT_Id'         => $Courier_Send_IT_Id,
            'LC_Id'         => $preTally_user_lcid,
            'PM_Id'         => 1,
            'BS_Amount'     => $singleDocAmount,
            'BS_Description'=> $BS_Description,
            'TR_Id'         => $AttObj->docArrayList[$rw]->AJ_Track_Id,
            'BS_DualEntry'  => 0,
            'BS_Complete'   => 1,
            'BS_PaidDate'   => $AttObj->docArrayList[$rw]->AJ_ReceivedDate,
            'BS_Date'       => date('Y-m-d H:i:s'),
            'BS_CDate'      => date('Y-m-d H:i:s'),
            'BS_MDate'      => date('Y-m-d H:i:s'),
            'BS_Status'     => 1
        );
            $AttObj->insertRecords('balance_sheets');
        }
    }
// end data insert balance_sheets
}
if($AJG_Id==0 || empty($AJG_Id)) {
    if(empty($AJG_Id)) 
            $AJG_Id = 0;
    $FunctionFlag = 1; // Docments send By Without Group
} else {
    $FunctionFlag = 0; // Documnet Send By Group
    //$docId = $AttObj->getIdsList('attestation_job_group_doc', 'AJD_Id', ' WHERE AJG_Id = '.$gId);
    $AttObj->InsertData = array(
        'AJG_Id'        => $AJG_Id,
        'LC_Id'         => $branchId,
        'AJGRD_Status'  => 1,
        'AJGRD_CDate'   => date('Y-m-d')
    );
    // Insert Group Reference Details
   $AJGRD_Id = $AttObj->insertReturnId('attestation_job_group_reference_details');
    if($AJGRD_Id==0 || $AJGRD_Id=='fail'){
        echo 'fail';
        exit();
    }
}
$AttObj->InsertData = array(
    'AJNB_Description'  => 'From '.$preTally_user_lcname.' ('.date('d-m-Y').')',
    'AJNB_Cdate'        => date('Y-m-d H:i:s'),
    'AJNB_Status'       => 0
);

// Insert notification Batch Details
$AJNB_Id = $AttObj->insertReturnId('attestation_job_notifi_batch');
if($AJNB_Id==0 || $AJNB_Id=='fail'){
    echo 'fail';
    exit();
}


    foreach ($DocIdArray as $rw) {
        if(!empty($rw)){
            if($value==' '){
                $value = "('".$rw."','".$AJG_Id."','".$preTally_user_lcid."', '".$branchId."', '".$AJNB_Id."', '1', '".date('Y-m-d')."')"; 
            }else {
                $value .= ",('".$rw."','".$AJG_Id."','".$preTally_user_lcid."', '".$branchId."', '".$AJNB_Id."', '1', '".date('Y-m-d')."')"; 
            }
        }
    }
    //Insert Document Reference Details
    if($AttObj->insertMultipleRecords('attestation_job_doc_reference_details', 'AJD_Id,AJG_Id,Send_LC_Id,Receive_LC_Id,AJNB_Id,AJDRD_Status,AJDRD_CDate', $value)=='success') {
        $AttObj->Data = array(
            'AJD_Status' => 3
        );
        //Document Reference Details  Insert Successfuly, Change Document Status in to "Transit"
        if($AttObj->updateRecords('attestation_job_documents', ' AJD_Id IN ('.$AJD_Ids.')')=='success') {
            echo 'success';
            //
        }
        else {
            echo 'fail';
        }
        
    }else {
        echo 'fail';
        exit();
    }
?>
 * 
 */
?>