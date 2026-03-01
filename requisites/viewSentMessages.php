<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MessageClass.php");
header('Content-type: text/html; charset=utf-8');

//date_default_timezone_set('Asia/Kolkata');
$MsgObj = new MessageClass();
$MsgObj->viewSentMessages($preTally_user_id);
$MsgArray = $MsgObj->SentArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>			
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="40" type="ro" align="center" sort="na"> </column>
        <column width="*" type="ro" align="justify" sort="na">Message</column>
        <column width="200" type="ro" align="left" sort="na">To</column>  
        <column width="100" type="ro" align="left" sort="na">Message Type</column>  
        <column width="100" type="ro" align="left" sort="na">View Status</column>  
        <column width="180" type="ro" align="left" sort="na">Date</column>
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
            
            $MsgObj->getRecipients($rw->MSG_Code,"total");
            $messageTo = $MsgObj->RecpArray;
            
            echo '<row id="'.$rw->MSG_Id.'">	
                <cell title=" " >'.$j.'</cell>
                <cell title="Expand/Collapse" type="sub_row"><![CDATA[<div style="font-size:10pt;">'.nl2br($rw->MSG_Message).'</br></div>]]></cell>
                <cell title=" "  name="MSG_Sub">'.$rw->MSG_Sub.'</cell>         
                <cell  title=" " type="ro"><![CDATA['.$rw->US_FName. ' '.$rw->US_LName.' ('.$rw->DP_Name.')';
            if(count($messageTo) > 1) {
                echo '<img src="images/icon/more_button.png" onmouseover="preTally.Settings.showLabel(this,\'View All Recipients\');" onmouseout="preTally.Settings.hideLabel(this);" style="margin:2px 0; cursor:pointer;width:50px;" title="More" onclick="preTally.Settings.viewMsgRecipients(this,'.$rw->MSG_Code.',\''.$rw->MSG_Sub.'\',\'total\');" />';
            }
            echo ']]></cell>
                <cell title=" "  name="MT_Type">'.$rw->MT_Type.'</cell>';
                $viewResult = $MsgObj->getFieldValue("COUNT(*) as count", "MSG_ReadStatus =1 AND MSG_Code='$rw->MSG_Code'");
                $viewCount  =  $viewResult['count'];
                $totalMsgResult = $MsgObj->getFieldValue("COUNT(*) as count", "MSG_Code='$rw->MSG_Code'");
                $totalMsg = $totalMsgResult['count'];
                
                echo '<cell name="MSG_Status"><![CDATA[<div style="display:inline-block;cursor: pointer;text-decoration: underline;" onmouseover="preTally.Settings.showLabel(this,\'Viewed Recipients\');" onmouseout="preTally.Settings.hideLabel(this);" onclick="preTally.Settings.viewMsgRecipients(this,'.$rw->MSG_Code.',\''.$rw->MSG_Sub.'\',\'viewed\');">'.$viewCount.'</div> \ <div style="display:inline-block;cursor: pointer;text-decoration: underline;" onmouseover="preTally.Settings.showLabel(this,\'Total Recipients\');" onmouseout="preTally.Settings.hideLabel(this);" onclick="preTally.Settings.viewMsgRecipients(this,'.$rw->MSG_Code.',\''.$rw->MSG_Sub.'\',\'total\');">'.$totalMsg.'</div>]]></cell>
                <cell title=" " name="MSG_CreatedOn">'.$MsgObj->formatDate($rw->MSG_CreatedOn).'</cell>
                </row>';	
            $j++;
        }
    }	  
    else{
        echo '<row id="0"> 
        <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No messages found</div>]]></cell>
        <cell/><cell/><cell/><cell/><cell/>
        </row>';
    }
echo '</rows>';
?>