<?php
//error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$MsgObj = new MessageClass();
$MsgObj->getMessageTypes();
$TypeArray = $MsgObj->TypeArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$role = 'adm';
$ajax = 'false';
echo '<items>
    <item type="settings" position="label-left" labelWidth="90" inputWidth="380" noteWidth="180" />
    
    <item type="hidden" name="idUserData" value=""></item>

    <item type="block" width="500" offsetTop="20" >
        <item type="input" name="MSG_To" label="To" value="" rows="5" readonly="true" required="true">
            <note width="150">Recipients</note>
        </item> 
        <item type="newcolumn" offsetLeft="40"></item>
    </item>     
    <item type="button" offsetLeft="110" value="Add / Manage Recipients" name="AddRecepientsBtn"  labelWidth="10" />
    <item type="input" name="MSG_Subject" label="Subject" value="" offsetLeft="20" required="true">
        <note width="150">Subject</note>
    </item> 
    
    <item type="combo" offsetLeft="20" name="MT_Id" label="Message Type" required="true" readonly="true">
        <option value = "" label="Select Type" selected="selected"/>';
        if($TypeArray) {
            foreach($TypeArray as $rw) {
                echo '<option value="'.$rw->MT_Id.'" label="'.$rw->MT_Type.'" selected="selected"/>';
            }
        }
        echo '<note width="150">Message Type</note>
    </item>    

    <item type="input" name="MSG_Message" label="Message" value="" rows="5" offsetLeft="20" required="true">
        <note width="150">Message</note>
    </item>

    <item type="block" width="200" offsetTop="7" offsetLeft="280">
        <item type="button" value="Submit" name="SendMsgBtn"/>
        <item type="newcolumn"/>
        <item type="button" value="Cancel" name="SendMsgCancel"/>
    </item>
</items>';