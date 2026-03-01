<?php
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$msgId = $REQUEST['mid'];
$MsgObj = new MessageClass();
$MsgObj->updateFieldValue('user_messages','MSG_Id='.$msgId,'MSG_ReadStatus = 1');
$UnreadCount = $MsgObj->getFieldValue("count(*) as count", "MSG_ReadStatus = 0 AND MSG_To=$preTally_user_id");
echo "Inbox(".$UnreadCount['count'].")";
exit;