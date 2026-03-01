<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
require_once($BASEPATH . 'includes/sessions.php');
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/TrackDupRptClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$ACLReq = $ACL_Obj->ACL_BSheet;
if($preTally_user_ofid){
$filter = 'LC.OF_Id = '.$preTally_user_ofid.' ';

$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);

if(($temp['1'])){
    $filter = 'LC.LC_Id = '.$LCkeyTemp[1].' ';
}
else{
    $filter = 'LC.OF_Id = '.$OFkeyTemp[1].' ';
}

$filter_mask = 'AND 1 ';
$filter_having= 'AND 1 ';
$filterData = explode(",",$REQUEST['filter']);
if($filterData[0] != '') {
    $filter_mask .=' AND IT.IT_Name like "%'.$filterData[0].'%" ';
}
if($filterData[1] != ''){
    $filter_mask .=' AND TR.TR_Track like "'.$filterData[1].'%" ';
    
}
if($filterData[2] != '' && $filterData[2] != '0'){
    $filter_having =' AND FIND_IN_SET('.$filterData[2].',LC_Id)';    
}
if($filterData[3] != ''){
    $sort_Query=$filterData[3];    
}
$TrackDupObj = new TrackDupRptClass();
$TrackDupObj->reportTrackDupData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask,$filter_having,$_GET["posStart"],$_GET["count"],$sort_Query);
$Count = $TrackDupObj->DataCount;
$COC_Obj = $TrackDupObj->TrackDupArray;
$IE_Type = array('','Income','Expense');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>
        <userdata name="F_Date">'.$REQUEST['f'].'</userdata>
        <userdata name="T_Date">'.$REQUEST['t'].'</userdata>';
    if($COC_Obj) {
        $j=$_GET["posStart"]+1;
                
        foreach($COC_Obj as $rw) {   
            
            echo '<row id="'.$rw->BS_Id.'">                
                <userdata name="TR_Id" >'.$rw->TR_Id.'</userdata>
                <userdata name="IT_Id" >'.$rw->IT_Id.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->TR_Track.'</cell>
                    <cell>'.$rw->IT_Name.'</cell> 
                    <cell>'.$rw->LC_Name.'</cell>     
                    <cell>'.$rw->no_rows.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetTrackDup_'.$rw->BS_Id.'" onclick="preTally.TrackDupReports.showDetailData(this,'.$rw->TR_Id.');"></a>]]></cell>    
                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>
                </row>';
            $j++;
            
        }
    } 
    else { echo '<row id="0" ><cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
}
?>