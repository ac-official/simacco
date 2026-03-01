<?php
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/BussRptClass.php");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
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
$CashOBObj = new BussRptClass();
$CashOBObj->reportBusinessSummary($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$CRP_Obj = $CashOBObj->BusinessArray;

$OldStock = $CRP_Obj['CurrentPeriodOldStock'] + $CRP_Obj['Old_Business'] - $CRP_Obj['Old_Income'] ;

echo '[{"value":"'.$OldStock.'","IE":"Income","color":"#A9EE36"},{"value":"'.$CRP_Obj['BussRecv'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$CRP_Obj['BussReturned'].'","IE":"Expense","color":"#f4161e"}]';

////echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"}]';
//
//$CashOBObj->getOpeningBalance($REQUEST['f'],$REQUEST['t'],$fields, $filter);
//$OBO_Obj = $CashOBObj->BusinessArray;
////print_r($OBO_Obj);
////$OB['1'] = $OB['2'] = $OB['3'] = $OB['4'] = 0;
//$OB['1'] = $OB['2'] = 0;
//
//$OB[$OBO_Obj[1]->MH_Type] = $OBO_Obj[1]->IE;
//$OB[$OBO_Obj[2]->MH_Type] = $OBO_Obj[2]->IE;
//
///*$OB['3'] = ($OBO_Obj[0]->OB + $OB['1']) - $OB['2'];
//$OB['4'] = ($OB['3'] + $IE['1']) - $IE['2'];*/
//
///*echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"},
//       {"value":"'.$OB['3'].'","OB":" OP BALANCE","color":"#FFBD51"},{"value":"'.$OB['4'].'","OB":" CL BALANCE ","color":"#E06666"}]';*/
//echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"}]';
//?>
