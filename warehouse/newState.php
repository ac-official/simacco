<?php
require_once($BASEPATH . "preTallyClass/StateClass.php");

$newStateObj = new StateClass();
$newStateObj->ST_Data = array(
    'US_Id' => $preTally_user_id,
    'ST_Name'     =>trim(htmlspecialchars($_REQUEST['ST_Name'], ENT_QUOTES)),
    'CN_Id'       =>htmlspecialchars($_REQUEST['ST_Country'], ENT_QUOTES),
    'ST_Status'   =>htmlspecialchars($_REQUEST['ST_Status'], ENT_QUOTES),
    'ST_MDate' 	=> date('Y-m-d H:i:s')
);

if($newStateObj->verifyState(htmlspecialchars($_REQUEST['ST_Id'], ENT_QUOTES))){
    if(htmlspecialchars($_REQUEST['ST_Id'], ENT_QUOTES) == 0){
        $newStateObj->ST_Data['ST_CDate'] = date('Y-m-d H:i:s');
        echo $newStateObj->newState();
    }else {
        echo $newStateObj->updateState(htmlspecialchars($_REQUEST['ST_Id'], ENT_QUOTES));
    }
    
} else {  echo'fail'; }
?>
