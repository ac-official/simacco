<?php
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
$LeaveObj = new LeaveClass();
$fromDate=$_REQUEST['LR_FromDate'];
$toDate=$_REQUEST['LR_ToDate'];
$fromDate=date("Y-m-d", strtotime($fromDate));
$toDate=date("Y-m-d", strtotime($toDate));
$grpId=$LeaveObj->createLVRptGrp($preTally_user_ofid);
$dateArray=array();
    
    if($_REQUEST['LR_NumOFDays']>1){
        $dateArray = $LeaveObj->getAllDatesBetweenTwoDates($fromDate, $toDate);        
        $length = count($dateArray);       
        //if($_REQUEST['LR_NumOFDays']==0.5){$LRD_Days=0.5;}else {$LRD_Days=1;}
        for($i=0;$i<$length;$i++){
            $LeaveObj->Leave_Data=array(
            'US_Id'=>$_REQUEST['US_Id'],
            'OF_Id'=>$preTally_user_ofid,    
            'Lrpt_Date' => $dateArray[$i], 
            'Lrpt_Type' =>$_REQUEST["LR_duration"],   
            'Lrpt_Reason'=>htmlspecialchars($_REQUEST['LR_Reason']),    
            'Lrpt_AddedBy'=>$preTally_user_id,        
            'Lrpt_GrpID'=>$grpId,
            'Lrpt_AddedDate'=>date("Y-m-d H:i:s")
            );
            $LeaveObj->ReportSubLeave();
        }
        echo $i." Leaves Reported";
        }else{
            $LeaveObj->Leave_Data=array(
            'US_Id'=> $_REQUEST['US_Id'],
            'OF_Id'=>$preTally_user_ofid,
            'Lrpt_Date' => $fromDate,   
            'Lrpt_Reason'=>$_REQUEST["LR_Reason"],    
            'Lrpt_AddedBy'=> $preTally_user_id,        
            'Lrpt_GrpID' =>$grpId,
            'Lrpt_AddedDate'=>date("Y-m-d H:i:s")
            );
            $LeaveObj->ReportSubLeave();
            echo "Leave Reported";
    }
?>