<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/BranchBSClass.php");

$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);
$BAkeyTemp  = explode('_', $temp['2']);

$ACLReq = $ACL_Obj->ACL_BSheet;

if( $temp['1']  && $temp['2'] ){
    $fields    = ' BnkOB_OpenBal as OB';
    $filter    = ' BA.LC_Id = '.$LCkeyTemp[1].'  AND BA.BA_Id = '.$BAkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' AND BA.BA_Id = '.$BAkeyTemp[1].' ';
}else if( $temp['1']  && !$temp['2'] ){
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' ';
}else if($ACLReq == 2){
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$preTally_user_lcid.' ';
    $filterOB  = 'BA.LC_Id = '.$preTally_user_lcid;
}else{
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    /*$filter = 'US.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'OF_Id = '.$keyTemp[1].' ';*/
    $filter = 'US.OF_Id = '.$keyTemp[1].' AND  BA.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'BA.OF_Id = '.$keyTemp[1].' ';
}

$BranchBSObj = new BranchBSClass();
$BranchBSObj->ocBarChartBranchData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $BranchBSObj->BranchBSArray;

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
    $incData .= ' { "id":"10", "amount":"'.$iOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.BranchBSReports.chartOtherDetails(1);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
if($eOthers > 0) {
    $expData .= ' { "id":"10", "amount":"'.$eOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.BranchBSReports.chartOtherDetails(2);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
   $income = '['.rtrim($incData, ',').']';      
   $expense = '['.rtrim($expData, ',').']';      

$chartData = array($income,$expense);
echo json_encode($chartData);

?>
