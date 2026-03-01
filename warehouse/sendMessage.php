<?php
require_once($BASEPATH . "/preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();
$MsgObj->Message_Details = array(        
    'MSG_From'      => $MsgObj->cleanData($preTally_user_id),  
    'MSG_To'        => "",
    'MSG_Code'      => $MsgObj->cleanData(substr(mt_rand().time(),3,8)),     
    'MT_Id'         => $MsgObj->cleanData($_REQUEST['MT_Id']),
    'MSG_Sub'       => $MsgObj->cleanData($_REQUEST['MSG_Subject']),
    'MSG_Message'   => trim(mysqli_real_escape_string($GLOBALS['con'],$_REQUEST['MSG_Message'])),        
    'MSG_CreatedOn' => $MsgObj->cleanData(date('Y-m-d H:i:s'))
);
echo $Result = $MsgObj->sendMessage($MsgObj->cleanData($_REQUEST['idUserData']));
exit;