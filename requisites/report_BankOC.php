<?php

require_once($BASEPATH . "preTallyClass/BankBSClass.php");
$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);
 
if(($keyTemp['0'])== 'BA'){
    $fields = 'BnkOB_OpenBal as OB';
    $filterOB = $filter = 'BA_Id = '.$keyTemp[1].' ';
}else{
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = 'US.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'OF_Id = '.$keyTemp[1].' ';
}

$BankOBObj = new BankBSClass();
$BankOBObj->ocPieChartBankData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$BRP_Obj = $BankOBObj->BankBSArray;
//print_r($BRP_Obj);
$IE['1'] = $IE['2'] = 0;

$IE[$BRP_Obj[0]->MH_Type] = $BRP_Obj[0]->IE;
$IE[$BRP_Obj[1]->MH_Type] = $BRP_Obj[1]->IE;

//echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"}]';

$BankOBObj->getBankOpeningBalance($REQUEST['f'],$REQUEST['t'],$fields, $filterOB, $filter);
$OB_Obj = $BankOBObj->BankBSArray;
//print_r($OB_Obj);
$OB['1'] = $OB['2'] = $OB['3'] = $OB['4'] = 0;

$OB[$OB_Obj[1]->MH_Type] = $OB_Obj[1]->IE;
$OB[$OB_Obj[2]->MH_Type] = $OB_Obj[2]->IE;

$OB['3'] = ($OB_Obj[0]->OB + $OB['1']) - $OB['2'];
$OB['4'] = ($OB['3'] + $IE['1']) - $IE['2'];

echo '[{"value":"'.$IE['1'].'","IE":"Income","color":"#A9EE36"},{"value":"'.$IE['2'].'","IE":"Expense","color":"#f4161e"},
       {"value":"'.$OB['3'].'","OB":" OP BALANCE","color":"#FFBD51"},{"value":"'.$OB['4'].'","OB":" CL BALANCE ","color":"#E06666"}]';
?>
