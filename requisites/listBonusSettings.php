<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GenObj     = new GeneralClass();
$GenObj->GetValues("*","bonus_percentage_settings","OF_Id","$preTally_user_ofid","BPS_Year DESC, BPS_Month ASC");
$Bonus_Obj  = $GenObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="BPS_BranchShare">BPS_BranchShare</userdata>
    <userdata name="BPS_GroupShare">BPS_GroupShare</userdata>
    <userdata name="BPS_Year">BPS_Year</userdata>
    <userdata name="BPS_Month">BPS_Month</userdata>

    <head>
        <column width="60" type="ro" align="center"> SlNo </column>
        <column width="*" type="ro" align="left"> Month</column>
        <column width="150" type="ro" align="left">Year</column>
        <column width="200" type="ro" align="left">Branch Share</column>
        <column width="200" type="ro" align="left">Group Share</column>
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
    if($Bonus_Obj) {
        $j = 1;
        foreach($Bonus_Obj as $rw) {
            echo '<row id="'.$rw->BPS_Id.'">
                <userdata name="DG_Name">'.$rw->BPS_Year.'</userdata>
                <userdata name="BPS_Year">'.$rw->BPS_Year.'</userdata>
                <userdata name="BPS_Month">'.$rw->BPS_Month.'</userdata>
                <userdata name="BPS_BranchShare">'.round($rw->BPS_BranchShare).'</userdata>
                <userdata name="BPS_GroupShare">'.round($rw->BPS_GroupShare).'</userdata>
                <cell>'.$j.'</cell>
                <cell name="BPS_Month">'.date('F', mktime(0,0,0,$rw->BPS_Month)).'</cell>
                <cell name="BPS_Year">'.$rw->BPS_Year.'</cell>
                <cell name="BPS_BranchShare">'.round($rw->BPS_BranchShare).'</cell>
                <cell name="BPS_GroupShare">'.round($rw->BPS_GroupShare).'</cell>';
            echo '</row>';
            $j++;
        }
    } else { 
        echo '<row id="no_record"> 
                <cell colspan="5" title= " "><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    } 
echo '</rows>';
?>