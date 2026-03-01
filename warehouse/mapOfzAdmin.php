<?php
require_once($BASEPATH . "preTallyClass/OfficeClass.php");

$OFObj = new OfficeClass();

$OFObj->UAM_Data = array(
    'UAM_Map'		=> $REQUEST['c'],
    'US_Id'             => $REQUEST['USId'],
    'UAM_MDate' 		=> date('Y-m-d H:i:s'),
    'UAM_Status' 	=> 1
);

$temp = $OFObj->verifyMapOfz(htmlspecialchars($REQUEST['USId'], ENT_QUOTES));
if($temp == 1 ) {
    $OFObj->UAM_Data["UAM_CDate"] = date('Y-m-d H:i:s'); 
    echo $OFObj->newMapOffices();
} else {
    echo $OFObj->updateMapOffices($REQUEST['USId']);
}        
?>