<?php
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
$ZoneObj = new ZoneClass();
$ZNString=$ZoneObj ->getUserZone($preTally_user_id);
if($ZNString)
    echo 1;
else 
    echo 0;
?>