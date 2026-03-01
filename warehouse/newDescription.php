<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");


$DescriptionObj = new DescriptionClass();
$UserObj = new UserClass();
$OffObj = new OfficeClass();

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
if($offAdm == $preTally_user_id) $IT_Approval = 0 ;
//die($_REQUEST['DS_Id']);
if($offAdm == $preTally_user_id) {
    $DSStatus = htmlspecialchars($_REQUEST['DS_Status'], ENT_QUOTES);
} else {
    $DSStatus = 2;
}
$arraykey='';
$DescriptionObj->DS_Data = array(  
    //'US_Id'             => htmlspecialchars($_REQUEST['US_Id'], ENT_QUOTES),
    'OF_Id'             => $preTally_user_ofid,
    'IT_Id'             => htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),
    //'DS_Description'	=> htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES),
    'OF_Id'             => $preTally_user_ofid,
    'DS_Approval'	=> $IT_Approval,
    'DS_Approved'	=> $preTally_user_id,
    'DS_Status' 	=> $DSStatus,
    'DS_MDate'          => date('Y-m-d H:i:s')  
);
foreach ($_REQUEST['DS_Description'] as $key => $value) {
    $DescriptionObj->DS_Data["DS_Description"] = trim(htmlspecialchars($value, ENT_QUOTES));
    $temp = $DescriptionObj->verifyDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
    if( $temp != 0 || $temp != '') { echo $key ; return false; }
    if($key != 1)$arraykey = in_array($value, array_values($DescriptionObj->DS_Data_Description['DS_Description']));
    if ($arraykey != '' ) { echo $key ; return false; }
    $DescriptionObj->DS_Data_Description["DS_Description"][] = $value;
}
//if( $tmpvalue == 0 || $tmpvalue == '') {
if(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES) == 0) {
    $DescriptionObj->DS_Data["US_Id"] = $preTally_user_id;
    $DescriptionObj->DS_Data["DS_CDate"] = date('Y-m-d H:i:s'); 
    echo $DescriptionObj->newMultipleDescription();
} else {
    $DescriptionObj->DS_Data["DS_Description"] = trim(htmlspecialchars($_REQUEST['DS_Description'][1], ENT_QUOTES));
    echo $DescriptionObj->updateDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
    unset($DescriptionObj->DS_Data_Description["DS_Description"][0]); 
    if(sizeof($DescriptionObj->DS_Data_Description['DS_Description']) > 0)
        echo $DescriptionObj->newMultipleDescription();
}
    
?>