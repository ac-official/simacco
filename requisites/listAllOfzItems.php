<?php
error_reporting(E_ALL ^ E_NOTICE);

//$ajax = 'true';

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");
$HI_ItemObj    = new HistoryClass();
    
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$IE_Type = array('','Income','Expense');
//print_r($REQUEST);

$filterData = explode(",",$REQUEST['filter']);
//print_r($filterData);

$filter = 'AND 1 ';
if($filterData[0]) {
    $filter .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1]){
    $filter .='AND IT.IT_Id = "'.$filterData[1].'"' ;
}
if($filterData[2]){
    $filter .='AND SH.SH_Name like "%'.$filterData[2].'%"';
}
if($filterData[3]){
    $filter .='AND MH.MH_Id = "'.$filterData[3].'"';
}
if($filterData[4] != ''){
    $filter .='AND ( IT.IT_Status = "'.$filterData[4].'" )';
}

if($preTally_user_ofid == 1) {
    $cond = " IT.OF_Id != 1 AND IT.OF_Id_Alias != 1 AND IT.IT_Status = 1 ";
} else {
    $cond = " IT.OF_Id = ".$preTally_user_ofid." AND IT.IT_Status !=4 ";
}

$IT_Status = array('Suspended','Published','Just Created','Senior Approved','Deleted');

$tot_count = $HI_ItemObj->viewAllOfzItemsCount($cond,$filter ); 
$HI_ItemObj->viewAllOfzItems($cond,$filter,$_GET["posStart"],$_GET["count"] );    
$HI_ITObj = $HI_ItemObj->HistoryArray;
//print_r($IT_NotfObj);

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">
<userdata name="TL_Count">'.$tot_count.'</userdata>';
    
        if($HI_ITObj) {
            $j=$_GET["posStart"]+1;
            foreach($HI_ITObj as $rw) {
                
                $statImg    = ($rw->IT_Status == '0') ? "cross.png" : "tick.png";
                $statLabel  = ($rw->IT_Status == '0') ? "Blocked By Admin" : "Approved";     
                              
                echo '<row id="'.$rw->IT_Id.'">
                    
                    <cell>'.$j.'</cell>
                    <cell id="'.$rw->MH_Type.'" >'.$IE_Type[$rw->MH_Type].'</cell>
                    <cell name="IT_Name" >'.$rw->IT_Name.'</cell>
                    <cell name="SH_Name" >'.$rw->SH_Name.'</cell>
                    <cell name="MH_Name" >'.$rw->MH_Name.'</cell>
                    <cell name="MH_Name" >'.$IT_Status[$rw->IT_Status].'</cell>
                    <cell name="HS_Name" ><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'View Item History\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>
                </row>';
                $j++;
            }
        } else {
            echo '<row id="0"><cell></cell> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';

        }
		  
echo '</rows>';
?>