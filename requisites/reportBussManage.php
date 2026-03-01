<?php
require_once($BASEPATH . "preTallyClass/TrackReportClass.php");
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

$BusinessObj    = new TrackReportClass();
$AtObj          = new AttestationClass();

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filterData     = explode(",",$REQUEST['filter']);
$havingFlt      = '';

$filter_mask    = 'AND TR.OF_Id = '.$preTally_user_ofid;
if($filterData[0] != '') {
    $filter_mask .=' AND TR.TR_Track like "'.$filterData[0].'%" ';
}

if($filterData[1] != ''){
    $filter_mask .=' AND IT.IT_Name like "%'.$filterData[1].'%"' ;
}

if($filterData[2] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[2].'%" ' ;
}

if($filterData[3] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[3].'%"';
}

if($filterData[4] != '') {
    if(!preg_match("/^[0-9.]+$/", $filterData[4]) )
        $havingFlt = ' HAVING BS_Amount = "'.$filterData[4].'"';
    else
        $havingFlt = ' HAVING BS_Amount = "'.+$filterData[4].'"';
}

if($filterData[5] != '') {
    if($filterData[5] == 2)
        $filter_mask .=' AND (TM.TM_Id IS NULL ) ';
    else if($filterData[5] == 1)
        $filter_mask .=' AND TM.TM_Id != "NULL" ';
}

$AtObj->getDetails('tracks_manageids', 'TR_Id', ' WHERE TM_Status = 1');
$TrackArray = array();
$TKObj      = $AtObj->DataArray;
foreach($TKObj as $rw) {
    array_push($TrackArray, $rw->TR_Id);
}

$BusinessObj->manageBusinessReport($REQUEST['f'],$REQUEST['t'],$filter_mask,$_GET["posStart"],$_GET["count"],$havingFlt);
$Count          = $BusinessObj->manageBusinessReportCount($REQUEST['f'],$REQUEST['t'],$filter_mask,$havingFlt);
$COC_Obj        = $BusinessObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>';
    if($COC_Obj) {
        $j  =   $_GET["posStart"]+1;
        foreach($COC_Obj as $rw) {
            $amount = number_format($rw->BS_Amount,2);     
            echo '
                <row id="'.$rw->TR_Id.'">
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->TR_Track. '</cell>
                    <cell>Attestation Job Received From</cell>
                    <cell>'.$amount.'</cell> 
                    <cell>'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell title = "Click here to hide the Track ID from reports">';
                    if(in_array($rw->TR_Id, $TrackArray))
                        echo '1';
                    else
                        echo '0';
                    echo '</cell>
                </row>';
            $j++;
        }
    } else { 
        echo '<row id="0"> <cell colspan = "7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell></row>';
    }
echo '</rows>';
?>