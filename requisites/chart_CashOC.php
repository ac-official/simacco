<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/CashBSClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
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

$CashBSObj = new CashBSClass();
$CashBSObj->ocBarChartCashData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $CashBSObj->CashBSArray;


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
    $incData .= ' { "id":"10", "amount":"'.$iOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.CashBalanceSheet.chartOtherDetails(1);\' >View More</a>)"},';
}
if($eOthers > 0) {
    $expData .= ' { "id":"10", "amount":"'.$eOthers.'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.CashBalanceSheet.chartOtherDetails(2);\' >View More</a>)"},';
}
   $income = '['.rtrim($incData, ',').']';      
   $expense = '['.rtrim($expData, ',').']';      

$chartData = array($income,$expense);
echo json_encode($chartData);

?>
