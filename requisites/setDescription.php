<?php
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");

$DescriptionObj = new DescriptionClass();

$DescriptionObj->DS_Data = array(  
    'DS_Status' 	=> 0,
    'DS_MDate'          => date('Y-m-d H:i:s')  
);

$DescriptionObj->updateDescription(htmlspecialchars($REQUEST['DSID'], ENT_QUOTES));
?>

