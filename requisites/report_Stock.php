<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/StockRptClass.php");
$ACLReq = $ACL_Obj->ACL_BSheet;
$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);

if(($temp['1'])){
    $fields = 'OB_OpenBal as OB';
    $filter = 'LC_Id = '.$LCkeyTemp[1].' ';
}else if($ACLReq == 2){
    $fields = 'OB_OpenBal as OB';
    $filter = 'LC_Id = '.$preTally_user_lcid.' ';
}else{
    $fields = 'SUM(OB_OpenBal) as OB';
    $filter = 'OF_Id = '.$OFkeyTemp[1].' ';
}
$stock = 0;
$CashOBObj = new StockRptClass();
$CashOBObj->reportStockDataSum($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$CRP_Obj = $CashOBObj->StockArray;

$CashOBObj->reportStockDetails($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$JOB_Obj = $CashOBObj->StockArray;
$JOB     = $CRP_Obj['Business'] ;

$OLDSTK  = $JOB_Obj['Old_Stock'] + $JOB_Obj['Old_Business'] - $JOB_Obj['Old_Income'] ;
$CURRENTSTK = $OLDSTK + $JOB - $CRP_Obj['Income'];
//$stock = $CRP_Obj['Business']-$CRP_Obj['Income'];
echo '[{"value":"'.number_format($OLDSTK,2).'","SR":"Old Stock","color":"#A9EE36"},
       {"value":"'.number_format($JOB,2).'","SR":"Business","color":"#A9EE36"},
       {"value":"'.number_format($CRP_Obj['Income'],2).'","SR":"Amount Received","color":"#A9EE36"},
       {"value":"'.number_format($CRP_Obj['Expense'],2).'","SR":"Amount Spent","color":"#A9EE36"},
       {"value":"'.number_format($CURRENTSTK,2).'","SR":"Current Stock","color":"#f4161e"}]';
?>
