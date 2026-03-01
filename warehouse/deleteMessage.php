<?php
include_once($BASEPATH . "preTallyClass/MessageClass.php");
$msgId = $REQUEST['msgId'];
$MsgObj = new MessageClass();
$MsgObj->updateFieldValue('user_messages','MSG_Id='.$msgId,'MSG_Status = 0');
exit;