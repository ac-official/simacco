<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->getDetails('attestation_documents', '*', ' WHERE OF_Id = '.$preTally_user_ofid.' ORDER BY ADOC_Document ');
$DocObj = $AttObj->DataArray;

$docTypeArray   =   array("","Education","Non-Education","Commercial","Passport","Registration certification for renewal","PCC Certificate","Courier");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> Document </column>
        <column width="100" type="ro" align="left" sort="na">	Document Type </column>
        <column width="0" type="ro" align="center" sort="na"></column>
        <column width="120" type="ro" align="center" sort="na">	Status </column>
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
    if($DocObj) {
        $j = 1;
        foreach($DocObj as $rw) {
            $ADOC_Status  =  $rw->ADOC_Status == 1 ? "Published" : "Blocked";
            
            echo '<row id="'.$rw->ADOC_Id.'">
                <userdata name="UData_ADOC_Document">'.$rw->ADOC_Document.'</userdata>
                <userdata name="UData_ADOC_Type">'.$rw->ADOC_Type.'</userdata>
                <userdata name="UData_ADOC_Status">'.$rw->ADOC_Status.'</userdata>
                <cell title=" ">'.$j.'</cell>
                <cell title=" " name="ADOC_Document">'.$rw->ADOC_Document.'</cell>
                <cell title=" " name="ADOC_Type">'.$docTypeArray[$rw->ADOC_Type].'</cell>
                <cell>'.$ADOC_Status.'</cell>';  
                if($rw->ADOC_Status == '0') { 
                    echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                } else {
                    echo '<cell title="Published"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                }
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>