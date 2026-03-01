<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AJ_Id = $REQUEST['TrkjId'];
$SPStatus = array('','Pending','Submitted','Completed','Rejected');

$AttObj = new AttestationClass();
$AttObj->getSubProcess($AJ_Id);
$DocObj = $AttObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
   echo' <head>
        <column width="55" type="ro" align="center" sort="int">SL NO</column>
        <column width="*" type="ro" align="left" sort="na">Document</column>
        <column width="*" type="ro" align="left" sort="na" >Process</column>
        <column width="*" type="ro" align="left" sort="na">Sub-Process</column>
        <column width="*" type="ro" align="left" sort="na">Supporting Documents</column>  
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
            <call command="enableAutoWidth">
                <param>true</param>
            </call> 
        </beforeInit> 
    </head>';
    if($DocObj) {
        $j = 0;
        foreach($DocObj as $rw) {
            $flag = TRUE;
            $Mp = $Sp = $supDoc = '';$SubProcess = '';
            foreach ($rw as $key => $value) {
                if($flag) {
                    echo '<row id="'.$value->AJD_Id.'">
                           <cell title=" ">'.++$j.'</cell>
                           <cell title= " ">'.$value->ADOC_Document.'</cell>';
                    $supDoc = $value->ASD_Document;
                    if($supDoc != NULL){
                        $supportingDocArray = explode(",",$supDoc);
                        $k=0;
                        foreach ($supportingDocArray as $rw) {
                            $SubProcess .="<br>".++$k.". ".$rw;
                        }
                    }else $SubProcess = "No Supporting Documents Added.";
                }
                $Mp .= $value->APM_Title."<br>";
                $Sp .= $value->APS_Title.' - '.$SPStatus[$value->AJS_Status]."<br>";
                $flag = FALSE;
            }
            echo '<cell title=" "><![CDATA[';echo ($Mp!='<br>')?$Mp :'--'; echo']]></cell>';
            echo '<cell title=" "><![CDATA[';echo ($Sp!=' - <br>')?$Sp :'--'; echo']]></cell>';
            echo '<cell title=" "><![CDATA[';echo ($SubProcess!=' - <br>')?$SubProcess :'--'; echo']]></cell>';
            echo '</row>';
        }
        echo '<row></row>';
//        echo '<row></row><row rowspan="2"><cell></cell>';   
//        if($supDoc) {
//            $supportingDocArray = explode(",",$supDoc);
//            $k=0;
//            foreach ($supportingDocArray as $rw) {
//                $SubProcess .="<br>".++$k.". ".$rw;
//            }
//            echo '<cell title=" ">Supporting Documents</cell><cell title=" "><![CDATA['.$SubProcess.']]></cell><cell></cell><cell></cell>'; 
//        }else {
//            echo'<cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Supporting Documents found</div>]]></cell>';
//        }
//        echo '</row>';
    }else {
        echo '<row id="0"> 
                <cell></cell><cell></cell><cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    } 
    echo'</rows>';
 ?>
