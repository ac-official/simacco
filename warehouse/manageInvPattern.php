<?php
require_once($BASEPATH . "preTallyClass/InventoryClass.php");

$PLObj = new InventoryClass();

$PLObj->PL_Data = array(
    'OF_Id'             => $preTally_user_ofid,
    'INVPType_Id'       => $REQUEST['INVID'],
    'INVP_PatternMap'	=> $REQUEST['id'],
    'INVP_MDate' 	=> date('Y-m-d H:i:s'),
    'INVP_Status' 	=> 1
);
if($PLObj->verifyPatternList(htmlspecialchars($REQUEST['INVID'], ENT_QUOTES)) == 1 ) {
    $PLObj->PL_Data["INVP_CDate"] = date('Y-m-d H:i:s'); 
    echo $PLObj->newPatternList();
} else {
    echo $PLObj->mapPatternList($REQUEST['INVID']);
}        
?>