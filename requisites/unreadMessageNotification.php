<?php
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();
$UnreadCount = $MsgObj->getFieldValue("count(*) as count", "MSG_ReadStatus = 0 AND MSG_To=$preTally_user_id");
ob_clean();
echo json_encode($UnreadCount);