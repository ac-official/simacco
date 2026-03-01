<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
///$AttObj->getMainProcessDetails('attestation_process_main','APM_Id, APM_Title,APM_Title_Alias,APMA_Id, APM_Description, APM_Status');
$AttObj->getMainProcessDetails('APM.APM_Id, APM.APM_Title, APM.APM_Title_Alias, APMA.APMA_Id, APMA.APMA_Title, APM.APM_Description, APM.APM_Status',$preTally_user_ofid);
$DocObj = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> Main Process </column>
        <column width="*" type="ro" align="left" sort="na"> Alias Name </column>
        <column width="*" type="ro" align="left" sort="na"> Authority </column>
        <column width="0" type="ro" align="center" sort="na"> </column>
        <column width="100" type="ro" align="center" sort="na">	Status </column>
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
            echo '<row id="'.$rw->APM_Id.'">
                    <userdata name="UData_APM_Id">'.$rw->APM_Id.'</userdata>
                    <userdata name="UData_APM_Title">'.$rw->APM_Title.'</userdata>
                    <userdata name="UData_APM_Title_Alias">'.$rw->APM_Title_Alias.'</userdata>
                    <userdata name="UData_APMA_Title">'.$rw->APMA_Id.'</userdata>
                    <userdata name="UData_APM_Description">'.$rw->APM_Description.'</userdata>
                    <userdata name="UData_APM_Status">'.$rw->APM_Status.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="APM_Title">'.$rw->APM_Title.'</cell>
                    <cell title=" " name="APM_Title_Alias">'.$rw->APM_Title_Alias.'</cell>
                    <cell title=" " name="APMA_Title">'.$rw->APMA_Title.'</cell>';
                    if($rw->APM_Status == 1 ) {
                        $APM_Status = "Published";
                    } 
                    else { 
                        $APM_Status = "Blocked";
                    }
                    echo '<cell>'.$APM_Status.'</cell>';  
                    if($rw->APM_Status == 0) { 
                        echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } 
                    else {
                        echo '<cell title="Published"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    }
            echo '</row>';
            $j++;
        }
    }
    else {
        echo '<row id="0"> 
            <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>