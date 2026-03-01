<?php
//error_reporting(E_ALL ^ E_NOTICE);
ob_start();
//$ajax = 'true';
//require_once('../includes/sessions.php');
require_once($BASEPATH . 'preTallyClass/NotificationClass.php');
require_once($BASEPATH . 'preTallyClass/MessageClass.php');
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
require_once($BASEPATH . "preTallyClass/LateEntryClass.php");

$NotfObj = new NotificationClass();
$UsrObj  = new UserClass();
$GenObj  = new GeneralClass();

$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];
$RptFlag=$_SESSION['preTally_user_rptflg'];
//echo json_encode(array($Rprtid));
//$NF_Obj = $NotfObj->countNotf($preTally_user_id);
//$count = $NF_Obj['0'] + $NF_Obj['1'];
//if($count > 9) $count = '9+';
//$notfData = array($NF_Obj['0'],$NF_Obj['1'],$count);

/* Unread messages count start */
$MsgObj = new MessageClass();
$unreadMsgCount = $MsgObj->getFieldValue("count(*) as count", "MSG_ReadStatus = 0 AND MSG_To=$preTally_user_id");

// Taking pending late entry count
// written By Achu on 11-07-2025
$pendingLateEntryCount = 0;
if($UserACLObj->view_late_entry){
    $lateEntryObj = new LateEntryClass();
    $pendingLateEntries = $lateEntryObj->getFieldValue("count(*) as count", "Att_Lt_Status = 'pending' AND Att_Lt_Verified_At IS NULL AND Att_Lt_Verified_By = 0");
    $pendingLateEntryCount = $pendingLateEntries['count'];
}
//array_push($notfData,$unreadMsgCount['count']);
/* Unread messages count end */

$filter_mask = 'AND 1 ';
$Cnt_CshPaymnt = $Cnt_BnkPaymnt = 0;

//----------------------- Cash Payment Notification Count -------------------//

if($ACL_Obj->ACL_NotfLC == 1){
    $filter = 'DS.DS_Description = "'.$preTally_user_lcname.'"  AND LC.OF_Id = "'.$preTally_user_ofid.'" ';
    $Cnt_CshPaymnt = $NotfObj->viewCashPaymentNoftCount($filter, $filter_mask);
}
    
//----------------------- Bank Payment Notification Count -------------------//

if($ACL_Obj->ACL_NotfBNK == 1){
    $filter = $preTally_user_lcid ;
    $filter_mask_bank = 'AND LC.OF_Id = "'.$preTally_user_ofid.'"  ';
    $Cnt_BnkPaymnt = $NotfObj->viewBankPaymentNoftCount($filter, $filter_mask_bank);
}

$NFCnt = $Cnt_CshPaymnt + $Cnt_BnkPaymnt;

//----------------------- Item & Description Notification Count -------------//
$Cnt_Notification=0;
$leaveCount=0;
if($RptFlag==1){
$myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );    
$currDate=date("Y-m-d");
$Cnt_Notification = $NotfObj->viewNotificationItemsCount($Rprtid,' AND BS_Date BETWEEN "'.$myDate.'" AND "'.$currDate.'"','','', '','');
//array_push($notfData,$NFCnt);

$leaveCount = $GenObj->Count("lr.LR_Id","leave_request as lr
                            LEFT JOIN leave_type AS lt ON lr.LT_Id = lt.LT_Id
                            LEFT JOIN users_auth AS ua ON ua.US_Id = lr.LR_AppliedFor ",'ua.OF_Id = "'.$preTally_user_ofid.'"  AND (lr.LR_Status = 1 OR (lr.LR_Status = 0 AND ua.US_Report = '.$preTally_user_id.') OR lr.LR_Status = 2 OR lr.LR_Status = 4 OR lr.LR_Status = 6) AND lr.LR_Status = "0" ');
}

$count = $unreadMsgCount['count'] + $NFCnt + $Cnt_Notification + $leaveCount + $pendingLateEntryCount;
// if($count > 9) $count = '9+';  // commented by Achu on 11-07-2025
$notfData = array($Cnt_Notification, $unreadMsgCount['count'], $NFCnt, $count, $leaveCount, $pendingLateEntryCount);

ob_clean();
echo json_encode($notfData);
?>
