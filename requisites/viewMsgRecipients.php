<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/MessageClass.php");
$msgCode = $REQUEST['msg_code'];
$MsgObj = new MessageClass();
$MsgObj->getRecipients($msgCode,$REQUEST['type']);
$MsgArray = $MsgObj->RecpArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>			
    <head>
        <column width="40" type="ro" align="center" sort="na">SlNo</column>
        <column width="*" type="ro" align="left" sort="na">Recipients</column>
        <column width="*" type="ro" align="left" sort="na">Department</column>
        <column width="*" type="ro" align="left" sort="na">Branch</column> 
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
            //<cell name="MSG_ReadStatus">'.$rw['US_FName'].' '.$rw['US_LName'].' ('.$rw['DP_Name'].')</cell>
            echo '<row id="'.$j.'">	
                <cell>'.$j.'</cell>
                <cell name="MSG_ReadStatus">'.$rw['US_FName']." ".$rw['US_LName'].'</cell>
                <cell name="MSG_ReadStatus">'.$rw['DP_Name'].'</cell>
                <cell name="MSG_ReadStatus">'.$rw['LC_Name'].'</cell>                    
            </row>';	
            $j++;
        }
    }	  
    else {
        echo '<row id="0"> 
            <cell colspan="2"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >';
           if($REQUEST['type'] == 'total') {
                echo 'No recipients found';
            }
            else if($REQUEST['type'] == 'viewed'){
                echo 'No viewed recipients found';
            }
        echo '</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>
