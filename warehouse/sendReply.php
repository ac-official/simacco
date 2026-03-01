<?php
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();
$MsgObj->messageReply = array(        
    'MSG_From'          => $MsgObj->cleanData($_REQUEST['MSG_From']),  
    'MSG_To'            => $MsgObj->cleanData($_REQUEST['MSG_To']),  
    'MSG_Code'          => $MsgObj->cleanData(substr(mt_rand().time(),3,8)),  
    'MT_Id' => $MsgObj->cleanData($_REQUEST['MT_Id']),
    'MSG_ParentMessage' => $MsgObj->cleanData($_REQUEST['MSG_ParentMessage']), 
    'MSG_Sub'           => $MsgObj->cleanData($_REQUEST['MSG_Sub']), 
    'MSG_Message'       => $_REQUEST['MSG_Message'],        
    'MSG_CreatedOn'     => $MsgObj->cleanData(date('Y-m-d H:i:s'))
);
echo $Result = $MsgObj->sendReply();
exit;