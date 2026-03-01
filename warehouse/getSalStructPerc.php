<?php
include_once ($BASEPATH . 'preTallyClass/SalStructClass.php');
$SS_Id=$REQUEST['ssid'];
$SalStructObj=new SalStructClass();
echo $SalStructObj->getSalPerc($SS_Id);
//var_dump($SalStructObj->SalStructArray);
echo json_encode($SalStructObj->SalStructArray);
?>


