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

$filter = " AND t.TR_Status = 1 ";

if($REQUEST['filter']) {
    $filterData = explode(",",$REQUEST['filter']);
    if($filterData[0] != '') {
        $filter .=' AND t.TR_Track like "'.$TrackObj->cleanData($filterData[0]).'%" ';
    }
    if($filterData[1]){
        $filter .=' AND CONCAT(u.US_FName," ",u.US_LName) like "'.$TrackObj->cleanData($filterData[1]).'%"' ;
    }
    if($filterData[2]){
        $filter .=' AND l.LC_Name like "'.$TrackObj->cleanData($filterData[2]).'%"';
    }
    if($filterData[3])
        $filter .=' AND CONCAT(US.US_FName," ",US.US_LName) like "'.$TrackObj->cleanData($filterData[3]).'%"' ;
    
    if($filterData[4] != "") {
        if($filterData[4] == 0)
            $filter .=' AND (TOJ.APS_Id IS NULL OR TOJ.APS_Id = ""  ) ';
        else if($filterData[4] == 1)
            $filter .=' AND TOJ.APS_Id != "" ';
    }
}

$TrackObj->oldTrackSubProcess($preTally_user_ofid,$_GET["posStart"],$_GET["count"],$filter);
$Count      = $TrackObj->countOldTrackSubProcess($preTally_user_ofid,$filter);
$Trk_Obj    = $TrackObj->TrackArray;
$ST_Type    = array('Suspend','Active','Process Finished','Delivered');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>'  ;
if($Trk_Obj) {
    $j      = $_GET["posStart"]+1;    
    foreach($Trk_Obj as $rw) {
        echo '<row id="'.$rw->TR_Id.'">  
            <userdata name = "LC_Id">'.$rw->LC_Id.'</userdata>
                
            <cell title = " ">'.$j.'</cell>                   
            <cell title = " ">'.$rw->TR_Track.'</cell>
            <cell title = " ">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
            <cell title = " ">'.$rw->LC_Name.'</cell>
            <cell title = " ">'.$rw->FName.' '.$rw->LName.'</cell>
            <cell title = " ">';
            echo ($rw->TK_Count) ? $rw->TK_Count : '';
            echo '</cell>
            <cell>';
            if($rw->TK_Count == 0) 
                echo '<![CDATA[<img src="images/icon/add.png" style="margin:2px 0; cursor:pointer;width:30px;" title="" onclick="preTally.TrackOldJobProcesses.viewSubProcesses('.$rw->TR_Id.',this)" />]]>';
            else
                echo '<![CDATA[<img src="images/icon/update.png" style="margin:2px 0; cursor:pointer;width:50px;" title="" onclick="preTally.TrackOldJobProcesses.viewSubProcesses('.$rw->TR_Id.',this)" />]]>';
            echo '</cell>
        </row>';
        $j++;
    }
} 
else { 
    echo '<row id="0">
    <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
    </row>';
}
echo '</rows>';
?>