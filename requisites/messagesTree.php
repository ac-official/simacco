<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/MessageClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$UserObj = new UserClass();
$userName = $UserObj->getUserName($preTally_user_id);
$MsgObj = new MessageClass();
$UnreadCount = $MsgObj->getFieldValue("count(*) as count", "MSG_ReadStatus = 0 AND MSG_To=$preTally_user_id");

echo '<tree id="0">
    <item text="'.$userName.'" id="Main_Item" open="1">
    <item text="Inbox('.$UnreadCount['count'].')" id="Inbox" />
    <item text="Sent" id="Outbox" />
    <item text="Trash" id="Trash" />
    </item>
</tree>';           