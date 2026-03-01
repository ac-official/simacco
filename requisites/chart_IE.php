<?php
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/ReportClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$newParm='';

if($_REQUEST['ID']){
    $newParm = $_REQUEST['ID'];
} else {
    $temp=  explode('-', $REQUEST['r']);
    $keyTemp = explode('_', $temp['0']);

    foreach ($temp as $value) {
        $newTemp = explode('_', $value);
        $newFilt.= $newTemp['0']."_Id =  '".$newTemp['1']."' AND ";
    }
    if($ACL_Obj->ACL_BSheet != 5) 
    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet, '', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
    $newFilt.='1';
    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt." AND US_Status != 5";
} 

$ReportObj = new ReportClass();
$ReportObj->ieBarChartData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$newParm);
$RP_Obj = $ReportObj->ReportArray;


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

        if($rw->SH_Id != 0){   
            if($rw->MH_Type == 1){   
                if($iCount < 10) {
                    $incData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"'.$titleStr.'", "color": "'.$color[$iCount].'", "category": "'.$rw->SH_Name.'"},';
                } else {
                    $iOthers += $rw->IE;
                }
                $iCount++;
            } else if($rw->MH_Type == 2){      
                if($eCount < 10) {
                    $expData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"'.$titleStr.'", "color": "'.$color[$eCount].'", "category": "'.$rw->SH_Name.'"},';
                } else {
                    $eOthers += $rw->IE;
                }
                $eCount++;
        }
        }else {
            if($rw->MH_Type == 1){   
                $incData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"1", "color": "'.$color[$iCount].'", "category": "Pending for Approval"},';
            } else if($rw->MH_Type == 2){      
                $expData .= ' { "id":"'.$j.'", "amount":"'.$rw->IE.'", "title":"2", "color": "'.$color[$eCount].'", "category": "Pending for Approval"},';
            }
        }
        $j++;
    }
}
   

if($iOthers > 0) {
    $incData .= ' { "id":"10", "amount":"'.round($iOthers,2).'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.Reports.chartOtherDetails(1);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
if($eOthers > 0) {
    $expData .= ' { "id":"10", "amount":"'.round($eOthers,2).'", "title":"Others", "color": "'.$color[10].'", "category": "Others (<a href=\'javascript:void(0);\' onclick=\'preTally.Reports.chartOtherDetails(2);\' ><img src=\'images/icon/expand_icon.png\' /> View More</a>)"},';
}
   $income = '['.rtrim($incData, ',').']';      
   $expense = '['.rtrim($expData, ',').']';      

$chartData = array($income,$expense);
echo json_encode($chartData);

?>
