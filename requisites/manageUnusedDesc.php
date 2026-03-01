<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UnusedItemDescClass.php");
$UnusedDescObj = new UnusedItemDescClass();

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filter_mask = "";
$filterData = json_decode($REQUEST['filter']);
if($filterData[0]!="") {
    $filter_mask .=' AND DS.DS_Description like "'.$filterData[0].'%"' ;
}
if($filterData[1]!=""){
    $filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
}

$UnusedDescObj->unusedDesc($filter_mask,$_GET["posStart"],$_GET["count"],$preTally_user_ofid);  
$Dsc_Obj = $UnusedDescObj->DescArray;
$tot_count = $UnusedDescObj->unusedDescCount($filter_mask,$preTally_user_ofid); 

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">'
        . '<userdata name="TL_Count">'.$tot_count.'</userdata>';
if($Dsc_Obj) {
    $j=$_GET["posStart"]+1;
    foreach($Dsc_Obj as $rw) {
        echo '<row id="'.$rw->DS_Id.'">
            <cell title=" ">'.$j.'</cell>
            <cell name="DS_Description"  title="Click here to edit the description">'.$rw->DS_Description.'</cell>
            <cell name="IT_Name" title=" ">'.$rw->IT_Name.'</cell>
            <cell title=" "></cell>
        </row>';
        $j++;
    } 
}
else {
    echo '<row id="0"> 
        <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
    </row>';
}
echo' </rows>';