<?php
include_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();

if($REQUEST['type'] == "branch") {
   $link = "preTally.BranchBSReports.editBalSheetDetailsFromMsg(this,'".$_REQUEST['BS_Id']."','".$_REQUEST['SH_Id']."')";
}
else if($REQUEST['type'] == "cash") {
   $link = "preTally.CashBalanceSheet.editBalSheetDetailsFromMsg(this,'".$_REQUEST['BS_Id']."','".$_REQUEST['SH_Id']."')";
}else if($REQUEST['type'] == "notf") {
   $link = "preTally.Notification.editBalSheetDetailsFromMsg(this,'".$_REQUEST['BS_Id']."','".$_REQUEST['SH_Id']."')";
}else if($REQUEST['type'] == "expcntrl") {
   $link = "preTally.ExpenseControl.editExpCnrtlEntryDetails(this,'".$_REQUEST['BS_Id']."','".$_REQUEST['SH_Id']."')";
}else if($REQUEST['type'] == "hireport"){
   $link = "preTally.Reports.editBalSheetDetailsFromMsg(this,'".$_REQUEST['BS_Id']."','".$_REQUEST['SH_Id']."')";
}
$MsgObj->Message_Details = array(        
    'MSG_From'      => $MsgObj->cleanData($preTally_user_id),  
    'MSG_To'        => "",
    'MSG_Code'      => $MsgObj->cleanData(substr(mt_rand().time(),3,8)),     
    'MT_Id'         => 3,
    'MSG_Sub'       => $MsgObj->cleanData($_REQUEST['MSG_Sub']),
    'MSG_Message'   => trim(mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['Entry'].'<br><br> '. $_REQUEST['MSG_Message'].' <br><br> <a style="cursor:pointer;text-decoration:underline;" onclick="'.$link.'">Click Here to Edit Item </a>')),        
    'MSG_CreatedOn' => $MsgObj->cleanData(date('Y-m-d H:i:s'))
);
echo $Result = $MsgObj->sendMessage($MsgObj->cleanData($_REQUEST['US_Id']));