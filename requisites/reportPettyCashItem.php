<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/PettyCashClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$ACLReq = $ACL_Obj->ACL_BSheet;

if($REQUEST['list'] == 'self')
    $filter = 'OF_Id = '.$preTally_user_ofid.' AND DS.DS_Description  = "'.$preTally_user_name.'" ';
else
    $filter = 'OF_Id = '.$preTally_user_ofid.' ';

$filterData = explode(",",$REQUEST['filter']);

$filter_mask = 'AND 1 ';
if($filterData[0]) {
    $filter_mask .=' AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''){
    $filter_mask .=' AND IT.IT_Name like "%'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter_mask .=' AND LC.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter_mask .=' AND CONCAT(US_FName," ",US_LName) like "'.$filterData[3].'%" ' ;
}
if($filterData[4] != ''){
    $filter_mask .=' AND DS.DS_Description like "'.$filterData[4].'%" ' ;
}

$UserObj = new UserClass();
$PettyCashObj = new PettyCashClass();
$PettyCashObj->reportPettyCashData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask,$_GET["posStart"],$_GET["count"]);
$Count = $PettyCashObj->reportPettyCashDataCount($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter,$filter_mask);
$W_Balance = $PettyCashObj->reportPettyCashBalance($filter);
$COC_Obj = $PettyCashObj->PettyCashArray;

$IE_Type = array('','Income','Expense');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="MW_Balance">'.$W_Balance.'</userdata>
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($COC_Obj) {
        $j=$_GET["posStart"]+1;
        foreach($COC_Obj as $rw) {
            $pettyCash = '';
            $UserObj->getReportingPerson($rw->US_Id);
            $US_Obj = $UserObj->UserArray;
            $amount = $rw->BS_Amount ? number_format($rw->BS_Amount,2) : 0 ; 
            /*if($rw->BS_PettyCashAmt != 0) */ $pettyCash = $rw->BS_PettyCashAmt ? number_format($rw->BS_PettyCashAmt,2) : 0;
            
            echo '<row id="'.$rw->BS_Id.'">';
                echo '<cell>'.$j.'</cell>
                <cell>'.$IE_Type[$rw->MH_Type].'</cell>';
                echo '<cell>'.$rw->IT_Name;
                if($rw->DS_Description != ''){echo '-'. $rw->DS_Description;}
                if($rw->TR_Track != ''){echo '-Track : '.$rw->TR_Track;}
                echo '</cell>
                <cell>'.$pettyCash.'</cell>
                <cell>'.$amount.'</cell> 
                <cell>'.$rw->LC_Name.'</cell>
                <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0, 1).'</cell>
                <cell>'.$rw->DS_Description.'</cell>
                <cell>'.date("d/m/Y", strtotime($rw->BS_Date)).'</cell>    
                <cell title="Click to view more details"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>
            </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell colspan ="10"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>