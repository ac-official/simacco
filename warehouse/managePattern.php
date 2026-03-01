<?php
require_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");

$PLObj = new SubheadPatternClass();

$PLObj->PL_Data = array(
    'OF_Id'             => $preTally_user_ofid,
    'SH_Id'             => $REQUEST['SHID'],
    'PL_PatternMap'	=> $REQUEST['id'],
    'PL_MDate' 		=> date('Y-m-d H:i:s'),
    'PL_Status' 	=> 1
);

if($PLObj->verifyPatternList(htmlspecialchars($REQUEST['SHID'], ENT_QUOTES)) == 1 ) {
    $PLObj->PL_Data["PL_CDate"] = date('Y-m-d H:i:s'); 
    echo $PLObj->newPatternList();
} else {
    echo $PLObj->mapPatternList($REQUEST['SHID']);
}        
?>