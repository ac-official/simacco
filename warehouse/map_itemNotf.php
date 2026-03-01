<?php
require_once($BASEPATH . "preTallyClass/NotificationClass.php");

$IT_MapObj = new NotificationClass();

if( $IT_MapObj->verifyMappedItem(htmlspecialchars($_REQUEST['IT_MapId'], ENT_QUOTES))) {
    $IT_MapObj->IT_MapData = array(
        'IT_Id'       =>htmlspecialchars($_REQUEST['IT_MapId'], ENT_QUOTES),    
    );
    echo $IT_MapObj->swapItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES));
} else { echo 'fail'; }



//$IT_MapObj->IT_MapData = array(
//    'IT_Id'       =>htmlspecialchars($_REQUEST['IT_MapId'], ENT_QUOTES),    
//);
//
//   
//echo $IT_MapObj->swapItem(htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES));
?>
