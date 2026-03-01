<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj = new ItemClass();

if($preTally_user_ofid == 1) {
    $filter   = " IT.OF_Id = 1 && IT.IT_Status = 1 ";
    $headType = '';
    $colType  = 'ro';
} else {
    $filter   = " IT.OF_Id = 1 && IT.IT_Status = 1 && IT.OF_Id_Alias != $preTally_user_ofid  ";
    $headType = '#master_checkbox';
    $colType  = 'ch';
}

$filterData = explode(",",$REQUEST['filter']);

if($filterData[0]) {
    $filter .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter .='AND IT.IT_Name like "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter .='AND SH.SH_Name like "'.$filterData[3].'%"';
}
if($filterData[3]){
    $filter .='AND MH.MH_Id = "'.$filterData[4].'"';
}


if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$tot_count = $ItemObj->viewPretallyItemsCount($filter);
$ItemObj->viewPretallyItems($filter);
$IT_Obj = $ItemObj->ItemArray;

$ItemObj->myMapItem($preTally_user_ofid);
$Map_Obj = $ItemObj->ItemMapArray;
$mapArray =  explode('"', $Map_Obj[0]->IC_Map);
$mapCount = floor(count($mapArray)/2);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">
     <userdata name="ACL_Type">'.$ACL_Obj->ACL_Item.'</userdata>';
    if($IT_Obj) {
        $j = 1;
        foreach($IT_Obj as $rw) {

            $statImg    = ($rw->IT_Status == '0') ? "cross.png" : "tick.png";
            $statLabel  = ($rw->IT_Status == '0') ? "Blocked By Admin" : "Approved";
            $typeLabel  = ($rw->MH_Type == '1') ? "Income" : (($rw->MH_Type == '2') ? "Expense" : "");
            if($colType == 'ro')
                $imgCheckBox = '<![CDATA[<img src="images/icon/'.$statImg.'" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$statLabel.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]>';
            else {
                $imgCheckBox = 0;
                $type = '';
                if(in_array($rw->IT_Id, $mapArray)) {
                    $imgCheckBox = 1;
                    $type = ' disabled  = "true" ';
                }
            }
            // userdata "IT_OtherUser" added 28-05-25
            echo '<row id="'.$rw->IT_Id.'">
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <userdata name="IT_Status">'.$rw->IT_Status.'</userdata>
                    <userdata name="IT_Business">'.$rw->IT_Business.'</userdata> 
                    <userdata name="IT_DualEntry">'.$rw->IT_DualEntry.'</userdata>
                    <userdata name="IT_OtherUser">'.$rw->IT_OtherUser.'</userdata>
                    <userdata name="IT_DualItem">'.$rw->IT_DualItem.'</userdata> 
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell name="MH_Type">'.$typeLabel.'</cell>
                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>
                    <cell name="SH_Name">'.$rw->SH_Name.'</cell>
                    <cell name="MH_Name">'.$rw->MH_Name.'</cell>
                    <cell '.$type.'>'.$imgCheckBox.'</cell>
                    <cell></cell>
            </row>';
            $j++;
        }
    } else {echo '<row id="0"> <cell colspan = "6" ><![CDATA[<div style="font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;" >No Records Found.</div>]]></cell></row>';}
		  
echo '</rows>';
?>