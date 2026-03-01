<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/DescriptionClass.php");

$DescriptionObj = new DescriptionClass();
if ($preTally_user_ofid == 1) {
    $filter = 1;
} else {
    $filter = " DS.OF_Id= $preTally_user_ofid";
}
if($REQUEST['descFilter'])
    $filter.= ' AND DS.DS_Description LIKE "%' .$REQUEST['descFilter'].'%"' ;
if($REQUEST['itmFilter']!="" && $REQUEST['itmFilter']!="null" && $REQUEST['itmFilter']!="0")
    $filter.= ' AND IT.IT_Id = '.$REQUEST['itmFilter'].' ' ;
if($REQUEST['addedByFilter']!='null' && $REQUEST['addedByFilter']!='All' && $REQUEST['addedByFilter']!='')    
    $filter.= ' AND DS.US_Id=' .$REQUEST['addedByFilter'] ;
if($REQUEST['status']!='null' && $REQUEST['status']!='')
    $filter.= ' AND DS.DS_Status=' .$REQUEST['status'] ;    
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;
$limit = " LIMIT " . $_GET["posStart"] . "," . $_GET["count"] = 50;
$DescriptionObj->listAllDescriptions(' WHERE ' . $filter . ' AND DS.DS_Status != 4 AND IT.IT_Status!=4 ORDER BY IT.IT_Name,DS.DS_Description', $limit);
$DS_Obj = $DescriptionObj->DescriptionArray;
$DS_Count = $DescriptionObj->DescCount;
$StatusArr=array("0"=>"Suspended","1"=>"Published","2"=>"Notification","3"=>"Semi Approved","4"=>"Deleted");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="' . $DS_Count . '" pos="' . $_GET["posStart"] . '">'
        . '<userdata name="DS_Count">'.$DS_Count.'</userdata>';
if ($DS_Obj) {
    $j = $_GET["posStart"]+1;
    foreach ($DS_Obj as $rw) {

        echo '<row id="' . $rw->DS_Id . '">                                            
                                            <cell>' . $j . '</cell>
                                            <cell name="DS_Description">' . $rw->DS_Description . '</cell>    
                                            <cell name="IT_Name">' . $rw->IT_Name . '</cell>
                                            <cell name="DS_CDate">' . $rw->USNAME . '</cell>                                              
                                            <cell name="DS_Status">' . $StatusArr[$rw->DS_Status] . '</cell>
                                            <cell name="HS_Name" ><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'View Description History\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';

        echo '</row>';
        $j++;
    }
} else {
    echo '<row id="0"> <cell colspan="5"><![CDATA[<div style="font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;" >No Records Found.</div>]]></cell></row>';
}

echo '</rows>';
?>