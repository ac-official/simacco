<?php
require_once($BASEPATH . 'includes/functions.php');
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

$BankBSObj = new BankBSClass();
$BankBSObj->ocBarChartBankData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $BankBSObj->BankBSArray;


$incData = '';
$expData = '';
$color = array("","#ee4339","#ee9336","#eed236","#d3ee36","#a7ee70","#58dccd","#36abee","#476cee","#a244ea","#e33fc7","#a7ee70","#ee4339");

if($RP_Obj) {
    
    $j = 1;
    $iCount = $eCount = 1;
    $iOthers = $eOthers = 0;
    foreach($RP_Obj as $rw) {
        
        $titleStr = (strlen($rw->SH_Name) > 16) ? substr($rw->SH_Name,0,16).'...' : $rw->SH_Name;
        $titleStr = wordwrap($titleStr, 14, "<br />");
        
        if($rw->MH_Type == 1){   
            if($iCount < 10) {
                $incData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"'.$titleStr.'", "color": "'.$color[$iCount].'", "category": "'.$rw->SH_Name.'", "shId" : "'.$rw->SH_Id.'" },';
            } else {
                $iOthers += $rw->IE;
            }
            $iCount++;
        } else if($rw->MH_Type == 2){      
            if($eCount < 10) {
                $expData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"'.$titleStr.'", "color": "'.$color[$eCount].'", "category": "'.$rw->SH_Name.'", "shId" : "'.$rw->SH_Id.'" },';
            } else {
                $eOthers += $rw->IE;
            }
            $eCount++;
        }
        $j++;
    }
}
   

if($iOthers > 0) {
    $incData .= ' { "id":"10", "amount":"'.$iOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.BankBalanceSheet.chartOtherDetails(1);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
if($eOthers > 0) {
    $expData .= ' { "id":"10", "amount":"'.$eOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.BankBalanceSheet.chartOtherDetails(2);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
   $income = '['.rtrim($incData, ',').']';      
   $expense = '['.rtrim($expData, ',').']';      

$chartData = array($income,$expense);
echo json_encode($chartData);

?>
