<?php
require_once($BASEPATH . "preTallyClass/ZoneClass.php");

$newZoneObj = new ZoneClass();
$newZoneObj->ZN_Data = array(
    'US_Id' => $preTally_user_id,
    'OF_Id' => $preTally_user_ofid,
    'ZN_Name'     =>trim(htmlspecialchars($_REQUEST['ZN_Name'], ENT_QUOTES)),
    'LC_Id'       =>htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES),    
    'ZN_Status'   =>htmlspecialchars($_REQUEST['ZN_Status'], ENT_QUOTES),
    'ZN_MDate' 	=> date('Y-m-d H:i:s')
);

if($newZoneObj->verifyZone(htmlspecialchars($_REQUEST['ZN_Id'], ENT_QUOTES))){
    if(htmlspecialchars($REQUEST['ZN_Id'], ENT_QUOTES) == 0){
        $newZoneObj->ZN_Data['ZN_CDate'] = date('Y-m-d H:i:s');
        $newZoneObj->ZN_Data['ZN_Created'] = $preTally_user_id;
        $newZoneObj->ZN_Data['ZN_Modified'] = $preTally_user_id;
        echo $newZoneObj->newZone();
    }else{
        $newZoneObj->ZN_Data['ZN_Modified'] = $preTally_user_id;
        echo $newZoneObj->updateZone(htmlspecialchars($_REQUEST['ZN_Id'], ENT_QUOTES));
    }
    
} else {  echo'fail'; }
?>
