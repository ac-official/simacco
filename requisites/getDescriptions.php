<?php
error_reporting(E_ALL ^ E_NOTICE);
$ajax = 'true';
require_once('../includes/sessions.php');

require_once('../crypt/crypt.php');
include_once("../preTallyClass/DescriptionClass.php");

$DescriptionObj = new DescriptionClass();
$IT_Id   = $REQUEST['IT_Id'];
$DescriptionObj -> getDescriptionName($IT_Id,'DS_Status != 0 AND OF_Id="'.$preTally_user_ofid.'" ');
$DSPopObj = $DescriptionObj->DescriptionArray;
//print_r($DSPopObj); die();
echo json_encode($DSPopObj);
?>

