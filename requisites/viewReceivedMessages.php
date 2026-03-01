<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MessageClass.php");
//date_default_timezone_set('Asia/Kolkata');
$MsgObj = new MessageClass();
$MsgObj->viewReceivedMessages($preTally_user_id);
$MsgArray = $MsgObj->ReceivedArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>			
    <head>
        <column width="50" type="ro" align="center" sort="na">SlNo</column>
        <column width="0" type="ro" align="center" sort="na">#select_filter_strict</column>
        <column width="70" type="ro" align="center" sort="na">#cspan</column>
        <column width="40" type="ro" align="center" sort="na"> </column>
        <column width="*" type="ro" align="justify" sort="na">Message</column>
        <column width="130" type="ro" align="left" sort="na">From</column>  
        <column width="120" type="ro" align="left" sort="na">Message Type</column>  
        <column width="150" type="ro" align="left" sort="na">Date</column>
        <column width="70" type="ro" align="left" sort="na"></column>
        <column width="0" type="ro" align="left" sort="na">msgStatus</column>
        <settings>
            <colwidth>px</colwidth>
        </settings>
        <beforeInit> 
            <call command="setSkin">
                <param>dhx_skyblue</param>
            </call> 
            <call command="setImagePath">
                <param>assets/grid/codebase/imgs/</param>
            </call> 
            <call command="enableSmartRendering">
                <param>false</param>
            </call> 
        </beforeInit> 
    </head>';
    if($MsgArray) {
        $j = 1;
       foreach($MsgArray as $rw) {
            echo '<row id="'.$rw->MSG_Id.'">	
                <userdata name="ReadStatus">'.$rw->MSG_ReadStatus.'</userdata>
                <cell title=" " >'.$j.'</cell>
                    <cell name="MSG_ReadStatus">'.$rw->MSG_ReadStatus.'</cell> ';
                if($rw->MSG_ReadStatus == 0) {
                    echo '<cell><![CDATA[<img src="images/icon/unread_message.png" title="UnRead" />]]></cell>';
                }
                else {
                    echo '<cell><![CDATA[<img src="images/icon/read_message.png" title="Read" />]]></cell>';
                }
                echo '<cell title="Expand/Collapse" type="sub_row"><![CDATA[<div style="font-size:10pt;">'. wordwrap(html_entity_decode(str_replace("\n","&lt;br /&gt;",$rw->MSG_Message)),100,"\n",TRUE).'</div>]]></cell>
                <cell title=" "  name="MSG_Sub">'.$rw->MSG_Sub.'</cell>         
                <cell title=" "  name="US_Name">'.$rw->US_FName. ' '.$rw->US_LName.' ('.$rw->DP_Name.')</cell>   
                <cell title=" "  name="MT_Type">'.$rw->MT_Type.'</cell> 
                <cell title=" " name="MSG_CreatedOn">'.$MsgObj->formatDate($rw->MSG_CreatedOn).'</cell>
                <cell type="ro"><![CDATA[<img src="images/icon/message_reply.png" onmouseover="preTally.Settings.showLabel(this,\'Reply\');" onmouseout="preTally.Settings.hideLabel(this);" alt="Reply" style="margin:2px 0; cursor:pointer;width:20px;display:inline-block;" onclick="preTally.Settings.sendReply(this,'.$rw->MSG_Id.','.$rw->MSG_From.',\''.$rw->MSG_Sub.'\');"  title="Reply"  />
                    <img src="images/icon/delete.png" onmouseover="preTally.Settings.showLabel(this,\'Delete\');" onmouseout="preTally.Settings.hideLabel(this);" alt="Delete" title="Delete" style="margin:2px 0; cursor:pointer;width:20px;display:inline-block;" onclick="preTally.Notification.deleteMessage(this,'.$rw->MSG_Id.');" />
                ]]></cell>
                <cell title=" "  name="msgStatus">'.$rw->MSG_ReadStatus.'</cell> 
                </row>';	
            $j++;
        }        
    }	  
    else {
        echo '<row id="0"> 
              <cell colspan="9"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No messages found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>