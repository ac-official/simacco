<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->getDetails('attestation_supporting_documents', '*', ' WHERE OF_Id = '.$preTally_user_ofid.' ORDER BY ASD_Document');
$DocObj = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="int"> SlNo </column>
        <column width="*" type="ro" align="left" sort="int"> Document </column>
        <column width="0" type="ro" align="center" sort="str"></column>
        <column width="120" type="ro" align="center" sort="str">Status </column>
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
            <call command="enableColSpan">
                <param>true</param>
            </call>  
        </beforeInit> 
    </head>';
    if($DocObj) {
        $j = 1;
        foreach($DocObj as $rw) {
            $ASD_Status  =  $rw->ASD_Status == 1 ? "Published" : "Blocked";
            
            echo '<row id="'.$rw->ASD_Id.'">
                    <userdata name="UData_ASD_Document">'.$rw->ASD_Document.'</userdata>
                    <userdata name="UData_ASD_Status">'.$rw->ASD_Status.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="ADOC_Document">'.$rw->ASD_Document.'</cell>
                    <cell>'.$ASD_Status.'</cell>';  
                    if($rw->ASD_Status == '0') { 
                        echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } else {
                        echo '<cell title="Published"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    }
            echo '</row>';
            $j++;
        }
    }
echo '</rows>';
?>