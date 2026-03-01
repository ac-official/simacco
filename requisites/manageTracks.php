<?php
include_once($BASEPATH . "preTallyClass/TrackClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
$TrackObj = new TrackClass();
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filter = "";
//echo $REQUEST['filter'];exit;
if($REQUEST['filter']) {
    $filterData = explode(",",$REQUEST['filter']);
    
    if($filterData[0] || $filterData[0] == 0) {
        $filter .=' AND t.TR_Track like "'.$TrackObj->cleanData($filterData[0]).'%" ';
    }
    if($filterData[1]){
        $filter .=' AND CONCAT(US_FName," ",US_LName) like "'.$TrackObj->cleanData($filterData[1]).'%"' ;
    }
    if($filterData[2]){
        $filter .=' AND l.LC_Name like "'.$TrackObj->cleanData($filterData[2]).'%"';
    }
    if($filterData[3] != "")
        $filter .= " AND t.TR_Status =".$filterData[3];
}

$TrackObj->viewTrackList($preTally_user_ofid,$_GET["posStart"],$_GET["count"],$filter);
$Count      = $TrackObj->countViewTrackList($preTally_user_ofid,$filter);
$Trk_Obj    = $TrackObj->TrackArray;
$ST_Type    = array('Suspend','Active','Process Finished','Delivered');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">';
if($Trk_Obj) {
    $j = $_GET["posStart"]+1;    
    foreach($Trk_Obj as $rw) {
        echo '<row id="'.$rw->TR_Id.'">              
            <cell title=" ">'.$j.'</cell>                   
            <cell title="Click to edit the track ID">'.$rw->TR_Track.'</cell>
            <cell title=" ">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
            <cell title=" ">'.$rw->LC_Name.'</cell>
            <cell title="Click to edit the status">'.$ST_Type[$rw->TR_Status].'</cell>
            <cell><![CDATA[<img src="images/icon/more_button.png" onmouseover="preTally.Settings.showLabel(this,\'View all track entries\');" onmouseout="preTally.Settings.hideLabel(this);" style="margin:2px 0; cursor:pointer;width:50px;" title="View all track entries" onclick="preTally.ManageTracks.viewTrackEntries('.$rw->TR_Id.',this)" />]]></cell>
        </row>';
        $j++;
    }
} 
else { 
    echo '<row id="0">
    <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
    </row>';
}
echo '</rows>';
?>