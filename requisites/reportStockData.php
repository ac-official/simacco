<?php
//error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
require_once($BASEPATH . 'includes/sessions.php');
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/StockRptClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$ACLReq = $ACL_Obj->ACL_BSheet;

$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);

if(($temp['1'])){
    $filter = 'LC_Id = '.$LCkeyTemp[1].' ';
}else if($ACLReq == 2){
    $filter = 'LC_Id = '.$preTally_user_lcid.' ';
}else{
    $filter = 'OF_Id = '.$OFkeyTemp[1].' ';
}

$mask       = $REQUEST['mask']  !=''  ?  $REQUEST['mask']   : $_REQUEST['mask'];
$SHFilter   = $REQUEST['SHName']!=''  ?  $REQUEST['SHName'] : $_REQUEST['SHName'];
$MHType     = $REQUEST['MHType']!=''  ?  $REQUEST['MHType'] : $_REQUEST['MHType'];
$LCName     = $REQUEST['LC_Name']!='' ?  $REQUEST['LC_Name']: $_REQUEST['LC_Name'];

$filterData = explode(",",$REQUEST['filter']);
//print_r($filterData);
$filter_mask = 'AND 1 ';
if($filterData[0] != '') {
    $filter_mask .=' AND TR.TR_Track like "'.$filterData[0].'%" ';
}
if($filterData[1] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    //$filter_mask_count = $filter_mask.' AND LC.LC_Id = "'.$filterData[2].'" AND IT.MH_Type = 1 ';
    $filter_mask_count = $filter_mask.' AND LC.LC_Name LIKE "'.$filterData[2].'%" '; // 30-05-2025
}else{
    $filter_mask_count = $filter_mask;
}

$StockObj = new StockRptClass();
$StockObj->reportStockData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask_count,$_GET["posStart"],$_GET["count"]);
$Count = $StockObj->reportStockDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask_count);
$COC_Obj = $StockObj->StockArray;
//print_r($COC_Obj);

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>';
    if($COC_Obj) {
        $j=$_GET["posStart"]+1;
                
        foreach($COC_Obj as $rw) {
            echo '<row id="'.$rw['TR_Id'].'">
                <cell>'.$j.'</cell>';
                    echo '<cell>'.$rw['TR_Track'].'</cell>
                    <cell>'.$rw['SH_Name'].'</cell> 
                    <cell>'.number_format($rw['Business'],2).'</cell>
                    <cell>'.number_format($rw['Income'],2).'</cell>';
                    if($ACL_Obj->ACL_MasterReports == 1 )echo '<cell>'.number_format($rw['Expense'],2).'</cell>';
                    echo '<cell>'.number_format(($rw['Business'] - $rw['Income'] ),2).'</cell>
                    <cell>';
                    //echo ($rw['IT_Business'] == 1 && $rw['MH_Type']== 1) ? $rw['LC_Name'] : "--";
                     echo  $rw['LC_Name'] ; //30-05-2025 changed as per the request of account team and midhya (export time this will always shows so we show this)
                    echo '</cell> 
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetStock_'.$rw['TR_Id'].'" onclick="preTally.StockReport.showDetailData(this,'.$rw['TR_Id'].');">'.$rw['US_FName'].' '.substr($rw['US_LName'],0, 1).'</a>]]></cell>
                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>
                </row>';
            $j++;
        }
    } 
    else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>