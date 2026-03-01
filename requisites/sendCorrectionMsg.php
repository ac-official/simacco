<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

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
    <item type="settings" position="label-left" labelWidth="100" inputWidth="300" noteWidth="180" offsetLeft="20"/>
    
    <item type="hidden" name="US_Id" value=""></item>
    <item type="hidden" name="BS_Id" value=""></item>
    <item type="hidden" name="SH_Id" value=""></item>

    <item type="input" name="MSG_To" label="To" offsetLeft="20" required="true" readonly = "true" rows="2">
        <note width="150">Send To</note>
    </item> 
    <item type="button" offsetLeft="120" value="Add / Manage Recipients" name="addRecpt"  labelWidth="10" />
    <item type="input" name="MSG_Sub" label="Subject" required="true" value="Correction in Accounts Entry. Please Verify.">
        <note width="150">Subject</note>
    </item> 
    <item type="template" name="EditLink" label="Link to Edit" value="Edit Enabled"></item>
    <item type="template" name="Entry" label="Entry Details"></item> 
            
    <item type="input" name="MSG_Message" label="Message" value="" rows="3" required="true">
        <note width="150">Your Message</note>
    </item>
    <item type="block" width="300" offsetTop="0">
        <item type="button" value="Send" name="rptMsgSend"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="rptMsgCancel"/>
    </item>
</items>';
?>