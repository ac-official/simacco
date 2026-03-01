<?php
$buttonclick    =   $REQUEST['name1'];
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
$LeaveObj       =   new LeaveClass();
if(isset($buttonclick)){
    $fromDate   =   $_REQUEST['LR_FromDate'];
    $toDate     =   $_REQUEST['LR_ToDate'];
    $fromDate   =   date("Y-m-d", strtotime($fromDate));
    $toDate     =   date("Y-m-d", strtotime($toDate));
     if($_REQUEST['LR_duration'] == 1){$session = 'FN';}
        elseif($_REQUEST['LR_duration'] == 2){ $session = "AN"; }
        else $session = "FL";
    if(isset($_REQUEST['LR_AppliedFor'])) {       
        $LR_AppliedFor      = $_REQUEST['LR_AppliedFor'];
        $LR_Status          = ($ACL_Obj->ACL_ApproveLeave) ? 2 : 1 ;    // first approval/hr approval
        if($ACL_Obj->ACL_ApproveLeave)
            $LR_ApprovedHR  = $preTally_user_id;
        else
            $LR_FirstApproval   = $preTally_user_id;
    } else
        $LR_AppliedFor      = $preTally_user_id;

    
    if($LeaveObj->checkLeaveExistence($LR_AppliedFor,$fromDate,$toDate) > 0 ) {
        echo "Leave already applied";
    } else {        
        $LeaveObj->Leave_Data       =   array(
            'US_Id'                 =>  $preTally_user_id ,
            'LR_AppliedFor'         =>  $LR_AppliedFor,
            'LR_NumOFDays'          =>  $_REQUEST['LR_NumOFDays'],
            'LT_Id'                 =>  $_REQUEST['LT_Id'],
            'LR_Session'            =>  $session,
            'LR_FromDate'           =>  $fromDate,
            'LR_ToDate'             =>  $toDate,
            'LR_CreditedDate'       =>  date("Y-m-d"),
            'LR_CDate'              =>  date("Y-m-d"),
            'LR_Reason'             => htmlspecialchars($_REQUEST['LR_Reason']),
            'LR_EligibleNumOFDays'  => $_REQUEST['LR_eligibleLeave'],
            'LR_FirstApproval'      =>  ($LR_FirstApproval) ? $LR_FirstApproval : 0,
            'LR_Status'             =>  ($LR_Status) ? $LR_Status : 0
        );
        if(isset($_REQUEST['LR_AppliedFor'])) {
        $LeaveObj->Leave_Data['LR_FirstDt'] = date("Y-m-d");
        }
        if(isset($LR_ApprovedHR))
            $LeaveObj->Leave_Data['LR_ApprovedHR'] = $LR_ApprovedHR;
        if($_REQUEST['LT_Id'] !="0" && $_REQUEST['LR_NumOFDays'] !="0" && $_REQUEST['LR_NumOFDays'] > 0 && $_REQUEST['LR_FromDate'] != "" && $_REQUEST['LR_ToDate'] != ""&& $_REQUEST['LR_Reason'] != "0"){
          $LR_Id    = $LeaveObj->applyLeave();
        }
        else{
            echo "Invalid Leave application.";
        }  
        $dateArray  =   $LeaveObj->getAllDatesBetweenTwoDates($fromDate, $toDate);
        $length     =   count($dateArray);
        if($_REQUEST['LR_NumOFDays'] == 0.5){$LRD_Days = 0.5;}else { $LRD_Days = 1; }
       
        for($i = 0;$i < $length;$i++){
            $LeaveObj->Leave_Day_Data   =   array(
                'LR_Id'                 =>  $LR_Id,
                'US_Id'                 =>  $LR_AppliedFor,
                'LT_Id'                 =>  $_REQUEST['LT_Id'],
                'LRD_Date'              =>  $dateArray[$i],
                'LRD_Days'              =>  $LRD_Days,
                'LRD_Session '          =>  $session
            );
            echo $result    =   $LeaveObj->applyLeavedays();
        }
        echo "Leave applied Successfully";
    }
}
?>