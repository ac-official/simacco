<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj = new ItemClass();
$filter  = "IT.OF_Id != 1 AND IT.IT_Status = 1 ";
$filterData = explode(",",$REQUEST['filter']);

if($filterData[0]) {
    $filter .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1]){
    $filter .='AND IT.IT_Id like "'.$filterData[1].'%"' ;
}
if($filterData[2]){
    $filter .='AND SH.SH_Name like "'.$filterData[3].'%"';
}
if($filterData[3]){
    $filter .='AND MH.MH_Id = "'.$filterData[4].'"';
}
if($filterData[4]){
    $filter .='AND IT.OF_Id = "'.$filterData[4].'"';
}

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$tot_count = $ItemObj->viewOtherItemsCount($filter);
$ItemObj->viewOtherItems($filter);    
$IT_Obj = $ItemObj->ItemArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '
<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">
     <userdata name="ACL_Type">'.$ACL_Obj->ACL_Item.'</userdata>';
      
    if($IT_Obj) {
        $j = 1;
        foreach($IT_Obj as $rw) {

            $typeLabel  = ($rw->MH_Type == '1') ? "Income" : (($rw->MH_Type == '2') ? "Expense" : "");
            $imgCheckBox = 0;
            if(in_array($rw->IT_Id, $mapArray)) {
                $imgCheckBox = 1;
            }
            
            echo '<row id="'.$rw->IT_Id.'">
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <userdata name="OF_Id_Alias">'.$preTally_user_ofid.'</userdata> 
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    
                    <cell>'.$j.'</cell>
                    <cell name="MH_Type">'.$typeLabel.'</cell>
                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>
                    <cell name="SH_Name">'.$rw->SH_Name.'</cell>
                    <cell name="MH_Name">'.$rw->MH_Name.'</cell>
                    <cell >'.$rw->OF_Name.'</cell>
                    <cell>'.$imgCheckBox.'</cell>
                    <cell></cell>
            </row>';
            $j++;
        }
    } else {echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;" >No Records Found.</div>]]></cell><cell></cell></row>';}
		  
echo '</rows>';
?>