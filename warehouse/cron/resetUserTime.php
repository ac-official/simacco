<?php
/**
* reset the temporary grace time of users 
* This is page is a cron work on every day morning
* Created By Bilin @ 31-07-2025
*/
//http://192.168.5.90/simacco/warehouse/cron/resetUserTime.php
header('Access-Control-Allow-Origin: *'); 
// base configuration files added
require_once('../../_conf.php');
// user based class files and object creation
$BASEPATH 	= Settings::getPublic('site_root');
require_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj 	= new UserClass();

// find the Cron related query
$graceusers = $UserObj->clearActiveGraceTime([]);

echo "Temporary Grace Time Removed and set the old timing of the user";
echo "<pre>";
print_r($graceusers);




?>