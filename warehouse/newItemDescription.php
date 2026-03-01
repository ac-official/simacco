<?php
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$DescriptionObj = new DescriptionClass();
$OffObj         = new OfficeClass();
$ItemObj        = new ItemClass();
$UserObj        = new UserClass();

$rptPntTree = array();
$DS_Notf = $ItemObj->getReportingTree($preTally_user_id,$rptPntTree);

$DS_MinAmnt       = $_REQUEST['DS_MinAmount'] ? $_REQUEST['DS_MinAmount'] : 0 ;
$DS_MaxAmnt       = $_REQUEST['DS_MaxAmount'] ? $_REQUEST['DS_MaxAmount'] : 0 ;

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
if($offAdm == $preTally_user_id) $IT_Approval = 0 ;
//die($_REQUEST['DS_Id']);
//if($offAdm == $preTally_user_id) {
if($ACL_Obj->ACL_Item == 1) {
    $DSStatus = htmlspecialchars($_REQUEST['DS_Status'], ENT_QUOTES);
} else {
    $DSStatus = 3;
}
$DescriptionObj->DS_Data = array(  
    'US_Id'             => $preTally_user_id,
    'OF_Id'             => $preTally_user_ofid,
    'IT_Id'             => htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),
    'DS_Description'	=> trim(htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES)),
    'OF_Id'             => $preTally_user_ofid,
    'DS_Approval'	=> $IT_Approval,
    'DS_Approved'       => $preTally_user_id,
    'DS_Status' 	=> $DSStatus,
    'DS_MinAmount'      =>trim(htmlspecialchars($_REQUEST['DS_MinAmount'], ENT_QUOTES)) ? htmlspecialchars($_REQUEST['DS_MinAmount'], ENT_QUOTES) : htmlspecialchars($DS_MaxAmnt, ENT_QUOTES),
    'DS_MaxAmount'      =>trim(htmlspecialchars($_REQUEST['DS_MaxAmount'], ENT_QUOTES)) ? htmlspecialchars($_REQUEST['DS_MaxAmount'], ENT_QUOTES) : htmlspecialchars($DS_MinAmnt, ENT_QUOTES),
    'DS_Notf'	        => $DS_Notf,
    'DS_MDate'          => date('Y-m-d H:i:s')  
);
$temp = $DescriptionObj->verifyDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
if( $temp == 0 || $temp == '') {
    if(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES) == 0) {
        $DescriptionObj->DS_Data["DS_CDate"] = date('Y-m-d H:i:s'); 
        echo $DescriptionObj->newDescription();
    } else {
        echo $DescriptionObj->updateDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
    }
} else { echo 'fail'; }
?>