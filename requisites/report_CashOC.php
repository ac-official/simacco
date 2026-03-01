<?php
require_once($BASEPATH . 'includes/functions.php');
include_once($BASEPATH . "preTallyClass/CashBSClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
$ACLReq = $ACL_Obj->ACL_BSheet;
$ZoneObj = new ZoneClass();
//$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = $REQUEST['OFID'];
$LCkeyTemp = $REQUEST['LCID'];
$ZNkeyTemp = $REQUEST['ZNID'];
$fields = 'SUM(OB_OpenBal) as OB';
if($REQUEST['ZNID']!='null' && $REQUEST['ZNID']!='All' && $REQUEST['ZNID']!='null' && $REQUEST['ZNID']!='' && $REQUEST['LCID']=='All')
{
$znlcid=$ZoneObj->getZoneLocations($REQUEST['ZNID']); 
$filter= 'LC_Id IN ('.$znlcid.')';
}else if($REQUEST['LCID'] && $REQUEST['LCID']!='All'){
$filter= 'LC_Id ='.$REQUEST['LCID'].' ';    
}else if($ACLReq == 2){
    $fields = 'SUM(OB_OpenBal) as OB';
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

$CashOBObj = new CashBSClass();
$CashOBObj->ocPieChartCashData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$CRP_Obj = $CashOBObj->CashBSArray;
//print_r($CRP_Obj);
$IE['1'] = $IE['2'] = 0;

$IE[$CRP_Obj[0]->MH_Type] = $CRP_Obj[0]->IE;
$IE[$CRP_Obj[1]->MH_Type] = $CRP_Obj[1]->IE;

//19-05-2025 start
$INE['1'] = $INE['2'] = 0;
$INE[$CRP_Obj[0]->MH_Type] = $CRP_Obj[0]->INE;
$INE[$CRP_Obj[1]->MH_Type] = $CRP_Obj[1]->INE;
// end;

//echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"}]';

$CashOBObj->getOpeningBalance($REQUEST['f'],$REQUEST['t'],$fields, $filter);
$OBO_Obj = $CashOBObj->CashBSArray;
//print_r($OBO_Obj);
$OB['1'] = $OB['2'] = $OB['3'] = $OB['4'] = 0;

$OB[$OBO_Obj[1]->MH_Type] = $OBO_Obj[1]->IE;
$OB[$OBO_Obj[2]->MH_Type] = $OBO_Obj[2]->IE;

$OB['3'] = ($OBO_Obj[0]->OB + $OB['1']) - $OB['2'];
$OB['4'] = ($OB['3'] + $IE['1']) - $IE['2'];

echo '[{"value":"'.number_format((float)$IE['1'], 2, '.', '').'","IE":"Income","color":"#A9EE36"},{"value":"'.number_format((float)$IE['2'], 2, '.', '').'","IE":"Expense","color":"#f4161e"},
       {"value":"'.number_format((float)$OB['3'], 2, '.', '').'","OB":" OP BALANCE","color":"#FFBD51"},{"value":"'.number_format((float)$OB['4'], 2, '.', '').'","OB":" CL BALANCE ","color":"#E06666"},
    {"value":"'.number_format($INE['1'],2).'","INE":"Internal Transfer Received","color":"#A9EE36"},{"value":"'.number_format($INE['2'],2).'","INE":"Internal Transfer Paid","color":"#f4161e"}]';
?>
