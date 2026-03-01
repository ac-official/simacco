<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UnusedItemDescClass.php");
$UnusedItemObj = new UnusedItemDescClass();

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filter_mask = "";
$filterData = json_decode($REQUEST['filter']);
if($filterData[0]!="") {
    $filter_mask .=' AND IT.IT_Name like "'.$filterData[0].'%"' ;
}
if($filterData[1]!=""){
    $filter_mask .=' AND SH.SH_Name like "'.$filterData[1].'%"' ;
}
if($filterData[2]){
    $filter_mask .=' AND MH.MH_Id = "'.$filterData[2].'"';
}
$UnusedItemObj->unusedItem($filter_mask, $_GET["posStart"], $_GET["count"], $preTally_user_ofid);     
$IT_Obj = $UnusedItemObj->ItemArray;
$tot_count = $UnusedItemObj->unusedItemCount($filter_mask, $preTally_user_ofid); 

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">'
          . '<userdata name="TL_Count">'.$tot_count.'</userdata>';
if($IT_Obj) {
    $j=$_GET["posStart"]+1;
    foreach($IT_Obj as $rw) {
        echo '<row id="'.$rw->IT_Id.'">
            <cell title=" ">'.$j.'</cell>
            <cell name="IT_Name" title="Click here to edit the item">'.$rw->IT_Name.'</cell>
            <cell name="SH_Name" title=" ">'.$rw->SH_Name.'</cell>
            <cell name="MH_Name" title=" ">'.$rw->MH_Name.'</cell>
            <cell title=" "></cell>
        </row>';
        $j++;
    } 
}
else {
    echo '<row id="0"> 
        <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
    </row>';
}
echo' </rows>';