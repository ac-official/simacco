<?php
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();

$msgId  = trim(strip_tags(htmlspecialchars($REQUEST['msg_id'], ENT_QUOTES)));
$fromId = trim(strip_tags(htmlspecialchars($REQUEST['from_id'], ENT_QUOTES)));
$fieldValue = $MsgObj->getFieldValue("MSG_Sub,MT_Id ", "MSG_Id=".$msgId);

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
    <item type="settings" position="label-left" labelWidth="200" inputWidth="500" noteWidth="180" offsetLeft="20"/>
    <item type="input" name="MSG_Sub" label="Subject" value="Re : '.$fieldValue['MSG_Sub'].'" rows="2" offsetLeft="20" offsetTop="50" required="true">
        <note width="150">Subject</note>
    </item> 
    <item type="input" name="MSG_Message" label="Message" value="" rows="9" required="true">
        <note width="150">Your Message</note>
    </item>
    <item type="hidden" name="MSG_ParentMessage" value="'.$msgId.'" />
    <item type="hidden" name="MSG_From" value="'.$preTally_user_id.'" />
    <item type="hidden" name="MT_Id" value="'.$fieldValue['MT_Id'].'" />
    <item type="hidden" name="MSG_To" value="'.$fromId.'" />
    <item type="block" width="300" offsetTop="0" offsetLeft="100">
        <item type="button" value="Send" name="replySave"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="replyCancel"/>
    </item>
</items>';
?>