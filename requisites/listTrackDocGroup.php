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

$filtter = ' WHERE TR.OF_Id = '.$preTally_user_ofid.' AND AJD.AJD_Status !=0 AND AJDT.AJ_Status NOT IN (0,2)' ;
$filterData = explode("--",$REQUEST['filter']);
if($filterData[0]){
    $filtter .=' AND AJG.AJG_Name like "'.$filterData[0].'%" ' ;
}
if($filterData[1] == 1) {
    $filtter .=' AND AJG.AJG_Status = 0';
}else if($filterData[1] == 2){
    $filtter .=' AND AJG.AJG_Status = 1';
} 

$Count = $AttObj->listTrackDocumnetGpCount($filtter);
$AttObj->listTrackDocumnetGp($filtter,$_GET["posStart"],$_GET["count"]);
$GrpObj = $AttObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Gp_Count">'.$Count.'</userdata>';
    if($_GET["posStart"]==0 && !$REQUEST['filter']) {
         echo '<head>
            <column width="40" type="ro" align="center" sort="na"  >SlNo</column>
            <column width="*"  type="ro" align="left"   sort="na"  >Group Name</column>
            <column width="90" type="ro" align="left"   sort="na"  >Group Number</column>
            <column width="60" type="ro" align="center" sort="na"  >No Of Documnet</column>
            <column width="60" type="ro" align="center" sort="na"  >Status</column>
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
                <call command="enableColSpan">
                    <param>true</param>
                </call> 
                <call command="enableSmartRendering">
                    <param>true</param>
                    <param>50</param>
                </call> 
            </beforeInit>
            </head>';
    }
    if($GrpObj) {
        $j=$_GET["posStart"]+1;
        foreach($GrpObj as $rw) {
            echo '<row id="'.$rw->AJG_Id.'">
                    <userdata name="UData_AJG_Name">'.$rw->AJG_Name.'</userdata>
                    <userdata name="UData_docCount">'.$rw->docCount.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="AJG_Name">'.$rw->AJG_Name.'</cell>
                    <cell title=" " name="AJG_Name">'.$rw->AJG_Number.'</cell>
                    <cell title=" " name="docCount">'.$rw->docCount.'</cell>';
                    if($rw->AJG_Status == '0') { 
                            echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    } else {
                            echo '<cell title="Published"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                    }
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>