<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->getDetails('attestation_authorities', 'AAUTH.*,AST.AST_Id,AST.AST_State,AST.AST_CS ', ' AS AAUTH 
                                    LEFT JOIN attestation_state AS AST ON AAUTH.AST_Id = AST.AST_Id 
                                        ORDER BY AAUTH_Authority');
//$AttObj->getAuthorityList();
$AuthObj = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> Authority </column>
        <column width="120" type="ro" align="left" sort="na"> State </column>
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
            <call command="enableColSpan">
                <param>true</param>
            </call>   
        </beforeInit> 
    </head>';
    if($AuthObj) {
        $j = 1;
        foreach($AuthObj as $rw) {
            echo '<row id="'.$rw->AAUTH_Id.'">
                    <userdata name="UData_AAUTH_Authority">'.$rw->AAUTH_Authority.'</userdata>
                    <userdata name="UData_ST_Id">'.$rw->AST_Id.'</userdata>
                    <userdata name="UData_AST_CS">'.$rw->AST_CS.'</userdata>
                    <userdata name="UData_AAUTH_Status">'.$rw->AAUTH_Status.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="AAUTH_Authority">'.$rw->AAUTH_Authority.'</cell>
                    <cell title=" " name="AAUTH_ST_Name">'.$rw->AST_State.'</cell>';
                    if($rw->AAUTH_Status ==1 ) {
                        $AAUTH_Status="Published";
                    } else { 
                        $AAUTH_Status="Blocked";
                    }
                    echo '<cell>'.$AAUTH_Status.'</cell>';  
                    if($rw->AAUTH_Status == '0') { 
                        echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } else {
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