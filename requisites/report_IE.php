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
$ReportObj->iePieChartData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$newParm);
$RP_Obj = $ReportObj->ReportArray;

//print_r($RP_Obj);


//for($i=0;$i<count($RP_Obj);$i++){  
   // foreach($RP_Obj[27] as $key=>$value){
       
      //  echo $key."=".$value;
     
    //}
//}
      

/*echo "income".$RP_Obj['CshInc'];
echo "expence".$RP_Obj['CshExp'];
echo "Buss".$RP_Obj['Buss'];
echo "ITcd".$RP_Obj['CshTransRecv'];
echo "ITPaid".$RP_Obj['CshTransPaid'];*/


//$IE['1'] = $IE['2'] = 0;

//$IE[$RP_Obj[0]->MH_Type] = $RP_Obj[0]->IE;
//$IE[$RP_Obj[1]->MH_Type] = $RP_Obj[1]->IE;

echo '[{"value":"'.number_format($RP_Obj['CshInc'],2).'","IE":"Income","color":"#A9EE36"},'
        . '{"value":"'.number_format($RP_Obj['CshExp'],2).'","IE":"Expense","color":"#A9EE36"},'
        . '{"value":"'.number_format($RP_Obj['CshTransPaid'],2).'","IE":"IT_Paid","color":"#A9EE36"},'
        . '{"value":"'.number_format($RP_Obj['CshTransRecv'],2).'","IE":"IT_Received","color":"#A9EE36"},'
        . '{"value":"'.number_format($RP_Obj['Buss'],2).'","IE":"Business","color":"#A9EE36"}]';

?>
