<?php
include_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();

$MsgObj->Message_Details = array(        
    'MSG_From'      => $MsgObj->cleanData($preTally_user_id),  
    'MSG_To'        => "",
    'MSG_Code'      => $MsgObj->cleanData(substr(mt_rand().time(),3,8)),     
    'MT_Id'         => 3,
    'MSG_Sub'       => $MsgObj->cleanData($_REQUEST['MSG_Sub']),
    'MSG_Message'   => trim(mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['Entry'].'<br><br> '. $_REQUEST['MSG_Message'].' <br><br>')),        
    'MSG_CreatedOn' => $MsgObj->cleanData(date('Y-m-d H:i:s'))
);
$Result = $MsgObj->sendCorrectionMessage($MsgObj->cleanData($_REQUEST['US_Id']));
$MsgObj->Entry_MSG=array("BS_Id"=>$_REQUEST['BS_Id'],"Msg_Id"=>$Result);
$MsgObj->insert_CorrectionMsg();
if(is_numeric($Result))echo "success";
else echo "failed";