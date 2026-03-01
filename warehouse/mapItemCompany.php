<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ITObj = new ItemClass();

$ITObj->IT_Data = array(
    'IC_Map'		=> $REQUEST['c'],
    'OF_Id'             => $preTally_user_ofid,
    'IC_MDate' 		=> date('Y-m-d H:i:s'),
    'IC_Status' 	=> 1
);

$temp = $ITObj->verifyMapItem(htmlspecialchars($preTally_user_ofid, ENT_QUOTES));
if($temp == 1 ) {
    $ITObj->IT_Data["IC_CDate"] = date('Y-m-d H:i:s'); 
    echo $ITObj->newMapItemCompany();
} else {
    echo $ITObj->mapItemCompany($preTally_user_ofid);
}        
?>