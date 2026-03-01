<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;


$filterData = explode("--",$REQUEST['filter']);
$filter = ' AND 1 ';
if($filterData[0]) {
    $filter .=' AND APS.APS_Title LIKE "'.$filterData[0].'%" ';
}
if($filterData[1]){
    $filter .=' AND APM.APM_Id = '.$filterData[1] ;
}
if($filterData[2] != ''){
    $filter .=' AND APS.APS_Status = '. $filterData[2] ;
}

$AttObj->getDetails('attestation_process_sub APS', 
                    'APS.APS_Id,APS.APM_Id,APM.APM_Title,APS.APS_Title,APS.APS_Description,APS_StatutoryNAmt,APS_ExtraNAmt,APS.APS_CDate,APS.APS_MDate,APS.APS_Status', 
                    'LEFT JOIN attestation_process_main APM ON APM.APM_Id=APS.APM_Id 
                        WHERE APS.OF_Id = "'.$preTally_user_ofid.'" '.$filter.'
                            LIMIT '. $_GET["posStart"].' ,' .$_GET["count"] .' '
                   );
$DocObj = $AttObj->DataArray;

$count = $AttObj->getValue('attestation_process_sub APS', 'COUNT(APS.APS_Id)', 'LEFT JOIN attestation_process_main APM ON APM.APM_Id=APS.APM_Id WHERE APS.OF_Id = "'.$preTally_user_ofid.'" '.$filter.' ');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$count.'" pos="'.$_GET["posStart"].'" >
    <userdata name="TL_Count">'.$count.'</userdata>';
    if($_GET["posStart"]==0 && !$REQUEST['filter']) {    
        echo '<head>
            <column width="50" type="ro" align="center" sort="na"> SlNo </column>
            <column width="*" type="ro" align="left" sort="na"> Sub Process </column>
            <column width="120" type="ro" align="left" sort="na"> Main Process </column>
            <column width="80" type="ro" align="left" sort="na"> Description </column>
            <column width="60" type="ro" align="left" sort="na"> Statutory Amount </column>
            <column width="60" type="ro" align="left" sort="na"> Extra Normal Amount </column>
            <column width="0" type="ro" align="center" sort="na"></column>
            <column width="100" type="ro" align="center" sort="na">	Status </column>
            <column width="100" type="ro" align="center" sort="na">	Info </column>
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
    }
    if($DocObj) {
        $j=$_GET["posStart"]+1;
        foreach($DocObj as $rw) {
            echo '<row id="'.$rw->APS_Id.'">

                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="APS_Title">'.$rw->APS_Title.'</cell>
                    <cell title=" " name="APM_Title">'.$rw->APM_Title.'</cell>
                    <cell title=" " name="APS_Description">'.$rw->APS_Description.'</cell>
                    <cell title=" " name="APS_StatutoryNAmt">'.$rw->APS_StatutoryNAmt.'</cell>
                    <cell title=" " name="APS_ExtraNAmt">'.$rw->APS_ExtraNAmt.'</cell>';
                    if($rw->APS_Status == 1 ) {
                        $APS_Status = "Published";
                    } else { 
                        $APS_Status = "Blocked";
                    }
                    echo '<cell>'.$APS_Status.'</cell>';  
                    if($rw->APS_Status == '0') { 
                        echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } else {
                        echo '<cell title="Published"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    }
                    echo '<cell title=""><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Track.showDetailBackDateData(this,'.$rw->APS_Id.');" />]]></cell>';
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>