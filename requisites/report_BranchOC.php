<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/BranchBSClass.php");

$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);
$BAkeyTemp  = explode('_', $temp['2']);

$ACLReq = $ACL_Obj->ACL_BSheet;

if( $temp['1']  && $temp['2'] ){
    $fields = 'BO.BnkOB_OpenBal as OB';
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].'  AND BA.BA_Id = '.$BAkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' AND BA.BA_Id = '.$BAkeyTemp[1].' ';
    //$filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' ';
}else if( $temp['1']  && !$temp['2'] ){
    $fields = 'SUM(BO.BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' ';
    //$filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' ';
}else if($ACLReq == 2){
    $fields = 'SUM(BO.BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$preTally_user_lcid.' ';
    $filterOB  = 'BA.LC_Id = '.$preTally_user_lcid.' ';
}else{
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = 'US.OF_Id = '.$keyTemp[1].' AND  BA.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'BA.OF_Id = '.$keyTemp[1].' ';
}
$BranchOBObj = new BranchBSClass();
$BranchOBObj->ocPieChartBranchData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$BRP_Obj = $BranchOBObj->BranchBSArray;
$IE['1'] = $IE['2'] = 0;

$IE[$BRP_Obj[0]->MH_Type] = $BRP_Obj[0]->IE;
$IE[$BRP_Obj[1]->MH_Type] = $BRP_Obj[1]->IE;
//19-05-2025 start
$INE['1'] = $INE['2'] = 0;
$INE[$BRP_Obj[0]->MH_Type] = $BRP_Obj[0]->INE;
$INE[$BRP_Obj[1]->MH_Type] = $BRP_Obj[1]->INE;
// end;

$BranchOBObj->getBranchOpeningBalance($REQUEST['f'],$REQUEST['t'],$fields, $filterOB, $filter);
$OB_Obj = $BranchOBObj->BranchBSArray;

$OB['1'] = $OB['2'] = $OB['3'] = $OB['4'] = 0;

$OB[$OB_Obj[1]->MH_Type] = $OB_Obj[1]->IE;
$OB[$OB_Obj[2]->MH_Type] = $OB_Obj[2]->IE;

$OB['3'] = ($OB_Obj[0]->OB + $OB['1']) - $OB['2'];
$OB['4'] = ($OB['3'] + $IE['1']) - $IE['2'];

echo '[{"value":"'.number_format($IE['1'],2).'","IE":"Income","color":"#A9EE36"},{"value":"'.number_format($IE['2'],2).'","IE":"Expense","color":"#f4161e"},
    {"value":"'.number_format($OB['3'],2).'","OB":" OP BALANCE","color":"#FFBD51"},{"value":"'.number_format($OB['4'],2).'","OB":" CL BALANCE ","color":"#E06666"},
    {"value":"'.number_format($INE['1'],2).'","INE":"Internal Transfer Received","color":"#A9EE36"},{"value":"'.number_format($INE['2'],2).'","INE":"Internal Transfer Paid","color":"#f4161e"}]';
?>
