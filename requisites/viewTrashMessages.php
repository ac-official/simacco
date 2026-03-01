<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
header('Content-type: text/html; charset=utf-8');
require_once($BASEPATH . "preTallyClass/MessageClass.php");
//date_default_timezone_set('Asia/Kolkata');
$MsgObj = new MessageClass();
$MsgObj->viewTrashMessages($preTally_user_id);

$MsgArray = $MsgObj->TrashArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>			
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="40" type="ro" align="center" sort="na"> </column>
        <column width="40" type="ro" align="center" sort="na"> </column>
        <column width="*" type="ro" align="justify" sort="na">Message</column>
        <column width="130" type="ro" align="left" sort="na">From</column>  
        <column width="120" type="ro" align="left" sort="na">Message Type</column>  
        <column width="150" type="ro" align="left" sort="na">Date</column>
        
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
                <cell title=" " >'.$j.'</cell>';
                if($rw->MSG_ReadStatus == 0) {
                    echo '<cell><![CDATA[<img src="images/icon/unread_message.png" title="UnRead" />]]></cell>';
                }
                else {
                    echo '<cell><![CDATA[<img src="images/icon/read_message.png" title="Read" />]]></cell>';
                }
                echo '<cell title="Expand/Collapse" type="sub_row"><![CDATA[<div style="font-size:10pt;">'.nl2br($rw->MSG_Message).'</br></div>]]></cell>
                <cell title=" " name="MSG_Sub">'.$rw->MSG_Sub.'</cell>         
                <cell title=" " name="US_Name">'.$rw->US_FName. ' '.$rw->US_LName.' ('.$rw->DP_Name.')</cell>   
                <cell title=" " name="MT_Type">'.$rw->MT_Type.'</cell> 
                <cell title=" " name="MSG_CreatedOn">'.$MsgObj->formatDate($rw->MSG_CreatedOn).'</cell>
                
                </row>';	
            $j++;
        }
    }	  
    else {
        echo '<row id="0"> 
            <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No messages found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>