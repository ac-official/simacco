<?php
require_once($BASEPATH . "preTallyClass/ZoneClass.php");

$newZoneObj = new ZoneClass();
$newZoneObj->ZN_Data = array(
    'US_Zones'=>$_REQUEST['ZN_Id'],
    'US_ZoneUpdBy'=>$preTally_user_id    
);
echo $newZoneObj->AssignZone(htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES));
$preTally_user_ofid
?>
