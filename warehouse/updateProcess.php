<?php
include_once($BASEPATH . "preTallyClass/ItemClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");
include_once($BASEPATH . "preTallyClass/DescriptionClass.php");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttObj     = new AttestationClass();
$DescObj    = new DescriptionClass();
$User_Obj   = new UserClass();
$IT_Obj     = new ItemClass();
$OffObj     = new OfficeClass();

$GPId       = trim(($_REQUEST['GP_Id']));
$DOCIds     = trim(($_REQUEST['DOC_Id']));
$APS_Id     = trim(($_REQUEST['APS_Id']));
$AJS_Status = trim(($_REQUEST['AJS_Status'])); //2 : Submit 3: COmplete  4 : Reject

$PDesc = $TDesc = 0;

$AttObj->DataArray = array( 
    'AJD_Id'      => $DOCIds,
    'APS_Id'      => $APS_Id,  
    'AJS_Status'  => $AJS_Status,
);
$result = $AttObj->updateGpProcess();

if($result != 'fail') {
    $SP_DOCIds = $AttObj->getIdsList('attestation_job_subprocess', 'AJD_Id', 'WHERE AJD_Id IN ('.$DOCIds.') AND APS_ID = '.$APS_Id);
    $AttObj->getDocTrackData($SP_DOCIds);
    $TrackDetails = $AttObj->docArrayList;
    $singleTravellingExp = round( ($_REQUEST['BS_Amount_Travel']  / count($TrackDetails)), 2);
    $singleProcessingExp = round( ($_REQUEST['BS_Amount_Process'] / count($TrackDetails)), 2);
    
    /** ~imp
    $rptPntTree = array();
    $IT_Notf = $IT_Obj->getReportingTree($preTally_user_id,$rptPntTree);
    $offAdm = $OffObj->offzAdmin($preTally_user_ofid);
        
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
    
    if($AJS_Status == 2) {
        $DescObj->DS_Data = array(   
            'US_Id'          => $preTally_user_id, 
            'IT_Id'          => trim(htmlspecialchars($_REQUEST['IT_PId'], ENT_QUOTES)),
            'OF_Id'          => $preTally_user_ofid,
            'DS_Description' => trim(htmlspecialchars($_REQUEST['DS_PDescription'], ENT_QUOTES)),
            'DS_Approval'    => $DS_Approval,
            'DS_Approved'    => $preTally_user_id,
            'DS_Notf'        => $IT_Notf,
            'DS_Status'      => $Status,
            'DS_CDate'       => date('Y-m-d H:i:s'),
            'DS_MDate'       => date('Y-m-d H:i:s')
        );  
        
        $temp = $DescObj->verifyDescription('0'); 
        if($temp == 0) { 
            $PDesc = $DescObj->newBalSheetDescription();  
        }  else {
            $PDesc = $temp;
        }
    }
    $DescObj->DS_Data = array(   
        'US_Id'          => $preTally_user_id, 
        'IT_Id'          => trim(htmlspecialchars($_REQUEST['IT_TId'], ENT_QUOTES)),
        'OF_Id'          => $preTally_user_ofid,
        'DS_Description' => trim(htmlspecialchars($_REQUEST['DS_TDescription'], ENT_QUOTES)),
        'DS_Approval'    => $DS_Approval,
        'DS_Approved'    => $preTally_user_id,
        'DS_Notf'        => $IT_Notf,
        'DS_Status'      => $Status,
        'DS_CDate'       => date('Y-m-d H:i:s'),
        'DS_MDate'       => date('Y-m-d H:i:s')
    ); 
    $temp = $DescObj->verifyDescription('0'); 
    if($temp == 0) { 
        $TDesc = $DescObj->newBalSheetDescription();  
    }  else {
        $TDesc = $temp;
    }
    
    foreach ($TrackDetails as $rw) {
    
        if($AJS_Status == 2) {
            $AttObj->Data[] = array(  
                'US_Id'         => $preTally_user_id,
                'IT_Id'         => trim(htmlspecialchars($_REQUEST['IT_PId'], ENT_QUOTES)),
                'LC_Id'         => $preTally_user_lcid,
                'PM_Id'         => 1,
                'BS_Amount'     => trim(htmlspecialchars($singleProcessingExp, ENT_QUOTES)),
                'BS_Description'=> $PDesc,
                'TR_Id'         => $rw->AJ_Track_Id,
                'BS_DualEntry'  => 0,
                'BS_Complete'   => 1,
                'BS_PaidDate'   => $rw->AJ_ReceivedDate,
                'BS_Date'       => date('Y-m-d H:i:s'),
                'BS_CDate'      => date('Y-m-d H:i:s'),
                'BS_MDate'      => date('Y-m-d H:i:s'),
                'BS_Status'     => 1
            );
        }
        $AttObj->Data[] = array(  
            'US_Id'         => $preTally_user_id,
            'IT_Id'         => trim(htmlspecialchars($_REQUEST['IT_TId'], ENT_QUOTES)),
            'LC_Id'         => $preTally_user_lcid,
            'PM_Id'         => 1,
            'BS_Amount'     => trim(htmlspecialchars($singleTravellingExp, ENT_QUOTES)),
            'BS_Description'=> $TDesc,
            'TR_Id'         => $rw->AJ_Track_Id,
            'BS_DualEntry'  => 0,
            'BS_Complete'   => 1,
            'BS_PaidDate'   => $rw->AJ_ReceivedDate,
            'BS_Date'       => date('Y-m-d H:i:s'),
            'BS_CDate'      => date('Y-m-d H:i:s'),
            'BS_MDate'      => date('Y-m-d H:i:s'),
            'BS_Status'     => 1
        );
    }
    $AttObj->insertMultipleData('balance_sheets');
     * 
     */
    
    unset( $AttObj->Data);
     $AttObj->Data = array();
        if($AJS_Status == 2) {
            $AttObj->Data[] = array(
                'DS_Id'         => $PDesc,
                'AJED_Amount'   => $singleProcessingExp,
                'AJG_Id'        => $GPId,
                'AJD_Id'        => $DOCIds,
                'US_Id'         => $preTally_user_id,
                'AJED_Cdate'    => date('Y-m-d H:i:s')
            );
        }
        $AttObj->Data[] = array(
                'DS_Id'         => $TDesc,
                'AJED_Amount'   => $singleTravellingExp,
                'AJG_Id'        => $GPId,
                'AJD_Id'        => $DOCIds,
                'US_Id'         => $preTally_user_id,
                'AJED_Cdate'    => date('Y-m-d H:i:s')
        );
        
        $AttObj->insertMultipleData('attestation_job_expense_description');
           
    
    $AttObj->UpdateData = ' AJD_Status = CASE ' ;
    if($AJS_Status=='2') 
        $AttObj->UpdateData .= 'WHEN AJD_Id IN('.$DOCIds.') THEN 2 '; //Document Status become "Under Process"
    if($AJS_Status=='3' || $AJS_Status=='4') {
        $AJDIdArray = explode(',', $DOCIds);
        
        foreach ($AJDIdArray as $AJDId) {
            $PS_SPID = $AttObj->getIdsList('attestation_job_subprocess', ' AJD_Id ', 'WHERE AJD_Id = '.$AJDId.' AND AJS_Status IN (1,2) '); //Id's of Subprocess that are in Pending/Sumbitted
            if(!$PS_SPID || !isset($PS_SPID)){
                $AttObj->UpdateData .= 'WHEN AJD_Id = '.$AJDId.' THEN 4 '; //Document Status become "Process Completed."
            }else $AttObj->UpdateData .= 'WHEN AJD_Id = '.$AJDId.' THEN 2 ';
        }
    }
    $AttObj->UpdateData .= ' ELSE AJD_Status END ';

    if($AttObj->updateMultipleRecords('attestation_job_documents', 'AJD_Id IN ('.$DOCIds.')') == 'success') {
        $jobId = $AttObj->getDistinctIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN( '.$DOCIds.') ');
        $AJIdArray = explode(',', $jobId);
        
        $AttObj->UpdateData = ' AJ_Status = CASE ' ;
        foreach ($AJIdArray as $AJId) {
            $PC_Count = $AttObj->getValue('attestation_job_documents', ' COUNT(AJ_Id) AS COUNT ', 'WHERE AJ_Id = '.$AJId.' AND AJD_Status NOT IN (0,4,5) '); //Id's of Subprocess that are in Pending/Sumbitted
            if($PC_Count || $PC_Count != 0 ){
                $AttObj->UpdateData .= 'WHEN AJ_Id = '.$AJId.' THEN 3'; //Job Status become "Under Process ."
            }else $AttObj->UpdateData .= 'WHEN AJ_Id = '.$AJId.' THEN 4 ';//Job Status become "Job Completed."
        }
        $AttObj->UpdateData .= ' ELSE AJ_Status END ';
        
        if($AttObj->updateMultipleRecords('attestation_job_details', 'AJ_Id IN ('.$jobId.')') != 'success'){
           echo 'fail';
        }
    }else {
        echo 'fail';
    }
    //For Job Tracking
    $AttObj->getDocTrackData($SP_DOCIds);
    $JobDetails = $AttObj->docArrayList;  
    unset($AttObj->Data);
    $DocIdArray = explode(',', $SP_DOCIds);
    foreach ($DocIdArray as $AJDId) {

        $AttObj->Data[] = array(  
            'US_Id'         => $preTally_user_id,
            'LC_Id'         => $preTally_user_lcid,
            'AJ_Id'         => $JobDetails[$AJDId]->AJ_Id,
            'AJG_Id'        => $GPId,
            'AJD_Id'        => $AJDId,
            'APS_Id'        => $APS_Id,
            'AJT_FromLC'    => 0,
            'AJT_ToLC'      => 0,
            'AJT_ReceiveLC' => 0,
            'AJ_Status'     => $JobDetails[$AJDId]->AJ_Status,
            'AJD_Status'    => $JobDetails[$AJDId]->AJD_Status,
            'AJS_Status'    => $AJS_Status,
            'AJT_CDate'     => date('Y-m-d H:i:s'),
            'AJT_Status'    => 5
        ); 
    }
    $AttObj->insertMultipleData('attestation_job_tracking');

}
echo $result;   
   



































    
    
/*
<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
//print_r($_REQUEST);
$gId                = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['gId']));
$APS_Status         = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['APS_Status']));
$UnderProArray      = array();
$CompleteArray      = array();
$JobIdListArray     = array();
$UndrProDocId       = ' ';
$docIds             = trim(mysqli_real_escape_string($GLOBALS['con'],($REQUEST['docId']));
$DocIdArray         = explode(',', $docIds);
$APS_Id             = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['APS_Id']));
$flag               = 0;
$Travaling_Expans   = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['trExps']));
$SubmitingFee       = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['Process_Fee']));

$Process_Submit_IT_Id   = $AttObj->getValue('attestation_job_balsheet_settings', 'IT_Id', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 7");
$Travaling_Expans_IT_Id = $AttObj->getValue('attestation_job_balsheet_settings', 'IT_Id', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 8");
$Process_Submit_DS_Id   = $AttObj->getValue('descriptions', 'DS_Id', " WHERE IT_Id = ".$Process_Submit_IT_Id);
$Travaling_Expans_DS_Id = $AttObj->getValue('descriptions', 'DS_Id', " WHERE IT_Id = ".$Travaling_Expans_IT_Id);

$DS_Id          = $Travaling_Expans_DS_Id;
$AJED_Amount    = $Travaling_Expans;
$IT_Id          = $Travaling_Expans_IT_Id;
$BS_Description = $Travaling_Expans_DS_Id;


//if($gId ==0) { // By Documents
    $flag =1;
    $AttObj->DataArray = array( 
        'AJD_Id'      => $docIds,
        'APS_Id'      => $APS_Id,  
        'APS_Status'  => $APS_Status,
//        'Action'      => 1
    );
//} else { // By Group
//    $AttObj->DataArray = array( 
//        'AJG_Id'      => $gId,
//        'APS_Id'      => trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['APS_Id'])),  
//        'APS_Status'  => $APS_Status,
//        'Action'      => 0
//    );
//}

$Result = $AttObj->updateGpProcess();
if($Result!= 'fail') {
    $BSflag = 2; 
    $subProDocIds = $AttObj->getIdsList('attestation_job_subprocess', 'AJD_ID', 'WHERE AJD_ID IN ('.$docIds.') AND APS_ID = '.$APS_Id);
    $AttObj->getDocTrackData($subProDocIds);
    $singleDocAmount = $Travaling_Expans / count($AttObj->docArrayList);
    
   do{ 
        //start insert travaling Expanse in to attestation_job_expense_description
        $BSflag--;
        $AttObj->Data = array(
            'DS_Id'         => $DS_Id,
            'AJED_Amount'   => $AJED_Amount,
            'AJG_Id'        => $gId,
            'AJD_Id'        => htmlspecialchars($subProDocIds),
            'US_Id'         => $preTally_user_id,
            'AJED_Cdate'    => date('Y-m-d H:i:s')
        );
        if($AttObj->insertRecords('attestation_job_expense_description') == 'success'){ //End insert data in to attestation_job_expense_description   
            
            //start travaling Expanse insert into balance_sheets
            
            foreach ($AttObj->docArrayList as $rw) {
                if(!empty($rw)){
                    
                    $AttObj->Data = array(  
                        'US_Id'         => $preTally_user_id,
                        'IT_Id'         => $IT_Id,
                        'LC_Id'         => $preTally_user_lcid,
                        'PM_Id'         => 1,
                        'BS_Amount'     => $singleDocAmount,
                        'BS_Description'=> $BS_Description,
                        'TR_Id'         => $rw->AJ_Track_Id,
                        'BS_DualEntry'  => 0,
                        'BS_Complete'   => 1,
                        'BS_PaidDate'   => $rw->AJ_ReceivedDate,
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
        $processFee = trim(mysqli_real_escape_string($GLOBALS['con'],($_REQUEST['Process_Fee']));
        if(!empty($processFee)){
            $DS_Id = $Process_Submit_DS_Id;
            $AJED_Amount = $SubmitingFee;
            $IT_Id = $Process_Submit_IT_Id;
            $singleDocAmount = $SubmitingFee / count($AttObj->docArrayList);
            $BS_Description = $Process_Submit_DS_Id;
        } else {
            $BSflag--;
        }
    }while ($BSflag);
  
    if($APS_Status=='3' || $APS_Status=='4') {
        if($flag==1) { //By Documents
             $AttObj->getDetails('attestation_job_subprocess', 'AJD_Id,AJS_Id,APS_Status', ' WHERE AJD_Id IN ('.$docIds.')');
        } else{  //By Group
            $AttObj->getDetails('attestation_job_group AS AJG','AJGD.AJD_Id,AJS.AJS_Id,AJS.APS_Status',' LEFT JOIN attestation_job_group_doc AS AJGD ON AJGD.AJG_Id = AJG.AJG_Id LEFT JOIN attestation_job_subprocess AS AJS ON AJS.AJD_Id = AJGD.AJD_Id WHERE AJG.AJG_Id = '.$gId);
        }
        foreach ($AttObj->DataArray as $rw) {
            if(!(in_array($rw->AJD_Id, $CompleteArray))) {
                array_push($CompleteArray, $rw->AJD_Id);
            }
            if(($rw->APS_Status=='1' || $rw->APS_Status=='2') && !(in_array($rw->AJD_Id, $UnderProArray))) {
                array_push($UnderProArray, $rw->AJD_Id);
            }
        }
        $CompleteArray = array_diff ( $CompleteArray , $UnderProArray);
        $UndrProDocId=implode(",", $UnderProArray);
        $CompleteDocId=implode(",", $CompleteArray);
        if(!empty($CompleteDocId)) {
            $AttObj->Data =array(
                'AJD_Status'=> 4
            );
            if($AttObj->updateRecords('attestation_job_documents',' AJD_Id IN ('.$CompleteDocId.')') == 'success') {
                $Aj_Id = $AttObj->getIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN ('.$CompleteDocId.')');
                $JobIdListArray = $AttObj->getJobDetails($Aj_Id);
                foreach ($AttObj->DataArray as $jobRw) {
                   $count =0;
                   foreach ($jobRw as $rw) {
                       if($rw->AJD_Status==4)
                            $count++;
                   }
                   if(count($jobRw)==$count){
                        $UpdateAJ_Status = 4; //Job Status become "Job Completed"
                    } else {
                        $UpdateAJ_Status = 3; //Job Status become "Job Under Process"
                    }   
                    $AttObj->Data = array(
                        'AJ_Status' => $UpdateAJ_Status
                    );
                    if($AttObj->updateRecords('attestation_job_details', 'AJ_Id = '.$rw->AJ_Id) != 'success') {
                        echo 'fail1';
                        exit();
                    }
                }
            } else {
                echo 'fail2';
                exit();
            }
        }
        if(!empty($UndrProDocId)) {
            $AttObj->Data =array(
                'AJD_Status'=> 2 //Document Status become "Under Process"
            );
            
            if($AttObj->updateRecords('attestation_job_documents',' AJD_Id IN ('.$UndrProDocId.')') == 'success') {
                echo 'fail3';
                exit();
            }  else {
                $Aj_Id = $AttObj->getIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id IN ('.$CompleteDocId.')');
                $AttObj->Data = array(
                        'AJ_Status' => 3
                );
                if($AttObj->updateRecords('attestation_job_details', 'AJ_Id = '.$Aj_Id) != 'success') {
                    echo 'fail4';
                    exit();
                }
            }
        }
    }
    if($APS_Status=='2') {
        $AttObj->Data = array(
            'AJD_Status' => 2 //Document Status become "Under Process"
        );
         if($flag==0) {
            $docIds = $AttObj->getIdsList('attestation_job_group_doc', 'AJD_Id', 'WHERE AJG_Id = '.$gId);
         }
        if($AttObj->updateRecords('attestation_job_documents', 'AJD_Id IN ('.$docIds.')')=='success') {
            $jobId = $AttObj->getIdsList('attestation_job_documents', 'AJ_Id', 'WHERE AJD_Id = '.$docIds);
            $AttObj->Data = array(
                'AJ_Status' => 3 //Job Status become "Job Under Process"
            );
            if($AttObj->updateRecords('attestation_job_details', 'AJ_Id IN ('.$jobId.')')!='success'){
               echo 'fail5';
               exit();
            }
        }else {
            echo 'fail6';
            exit();
        }
    }
}
echo $Result;
    */
        
                    
