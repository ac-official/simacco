<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/BussRptClass.php");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$ACLReq = $ACL_Obj->ACL_BSheet;
$ZoneObj = new ZoneClass();
//$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = $REQUEST['OFID'];
$LCkeyTemp = $REQUEST['LCID'];
$ZNkeyTemp = $REQUEST['ZNID'];
if($REQUEST['ZNID']!='null' && $REQUEST['ZNID']!='All' && $REQUEST['ZNID']!='null' && $REQUEST['ZNID']!='' && $REQUEST['LCID']=='All')
{
$znlcid=$ZoneObj->getZoneLocations($REQUEST['ZNID']); 
$filter= 'LC_Id IN ('.$znlcid.')';
}else if($REQUEST['LCID'] && $REQUEST['LCID']!='All'){
$filter= 'LC_Id ='.$REQUEST['LCID'].' ';    
}else if($ACLReq == 2){
    $ZoneObj->getUserZoneLCId($preTally_user_id);
    $ZoneArray=$ZoneObj->ZonArray;   
    $ZoneArrayString=implode(',',array_column($ZoneArray, 'LC_Id')); 
    if($ZoneArrayString)
    $filter= 'LC_Id IN('.$ZoneArrayString.')  ';
    else
    $filter= 'LC_Id ='.$preTally_user_lcid;
}else{
    $filter = 'OF_Id = '.$OFkeyTemp.' ';
}

$mask       = $REQUEST['mask']  !=''  ?  $REQUEST['mask']   : $_REQUEST['mask'];
$SHFilter   = $REQUEST['SHName']!=''  ?  $REQUEST['SHName'] : $_REQUEST['SHName'];
$MHType     = $REQUEST['MHType']!=''  ?  $REQUEST['MHType'] : $_REQUEST['MHType'];
$LCName     = $REQUEST['LC_Name']!='' ?  $REQUEST['LC_Name']: $_REQUEST['LC_Name'];

/*if($mask) {
    $filter_mask = "AND IT.IT_Name like '".$mask."%'";
}else if($LCName) {
    $filter_mask = "AND LC.LC_Name like '".$LCName."%'";
}else if($SHFilter) {
    $filter_mask = "AND SH.SH_Name like '".$SHFilter."%'";
}else if($MHType) {
    $filter_mask = "AND IT.MH_Type =".$MHType."";
}else{
    $filter_mask = 'AND 1';
}*/

$filterData = explode(",",$REQUEST['filter']);

$filter_mask = 'AND 1 ';
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    //$filter_mask .=' AND IT.IT_Name like "'.$filterData[1].'%"' ;
    // change the old filter and enter new below 16-05-2025
    $filter_mask .=' AND (IT.IT_Name like "%'.$filterData[1].'%" OR DS.DS_Description like "%'.$filterData[1].'%" OR TR.TR_Track like "%'.$filterData[1].'%" )' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[3].'%" ' ;
}
if($filterData[4]){
    $filter_mask .=' AND IT.SH_Id = "'.$filterData[4].'%" ' ;
}
if($filterData[5] != '' ) {
    if(!preg_match("/^[+-]?[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/", (float)$filterData[5]) )
    $filter_mask .= ' AND BS.BS_Amount LIKE "'.$filterData[5].'%"';            
    else
    $filter_mask .= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$filterData[5].'" OR FLOOR(BS.BS_Amount) = "'.$filterData[5].'")';            
}


$BusinessObj = new BussRptClass();
$BusinessObj->reportBusinessData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask,$_GET["posStart"],$_GET["count"]);
$Count = $BusinessObj->reportBusinessDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask);
$COC_Obj = $BusinessObj->BusinessArray;
//print_r($COC_Obj);

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($COC_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($COC_Obj as $rw) {
            $amount = number_format($rw->BS_Amount,2);            
//            $income  = ($rw->MH_Type == '1') ? $amount : "---";
//            $expense = ($rw->MH_Type == '2') ? $amount : "---";
            echo '
                <row id="'.$rw->BS_Id.'">
                    <cell>'.$j.'</cell>
                    <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                    echo '<cell>'.$rw->IT_Name;
                    if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                    if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                    echo '</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.$amount.'</cell> 
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell><![CDATA[<a style="text-decoration:none;" class="targetCash_'.$rw->BS_Id.'" onclick="preTally.BusinessReport.showDetailData(this,'.$rw->BS_Id.','.$rw->SH_Id.');">'.$rw->US_FName.' '.substr($rw->US_LName,0, 1).'</a>]]></cell>
                    <cell></cell>
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell></row>';}
echo '</rows>';
?>