<?php
require_once($BASEPATH . "preTallyClass/IMClass.php");

$IMObj = new IMClass();

$IMObj->IM_Data = array(  
    'CHT_FM_US_Id'      => $preTally_user_id,
    'CHT_TO_US_Id'      => $REQUEST['IMTO'],
    'CHT_Text'          => $REQUEST['IMTXT'],
    'CHT_CDate'          => date('Y-m-d H:i:s')
);

$IMObj->newIM(); 
$IMData = $IMObj->viewIM($IMObj->IM_Data['CHT_FM_US_Id']);
//print_r($IMObj->IMArray);
echo json_encode($IMObj->IMArray);
?>