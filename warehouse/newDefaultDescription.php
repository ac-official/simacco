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

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);
$IT_Approval = $UserObj->myReportingPerson($preTally_user_id);
if($offAdm == $preTally_user_id) $IT_Approval = 0 ;

//if($ACL_Obj->ACL_Item == 1) {
//    $DSStatus = htmlspecialchars($_REQUEST['DS_Status'], ENT_QUOTES);
//} else {
//    $DSStatus = 3;
//}

$flag = 0;
$DescriptionObj->DS_Data = array(  
    'US_Id'             => $preTally_user_id,
    'OF_Id'             => $preTally_user_ofid,
    'IT_Id'             => htmlspecialchars($_REQUEST['IT_Id'], ENT_QUOTES),
//    'DS_Description'	=> trim(htmlspecialchars($_REQUEST['DS_Description'], ENT_QUOTES)),
    'DS_Approval'	=> $IT_Approval,
    'DS_Status' 	=> 1,
    'DS_Notf'	        => $DS_Notf,
    'DS_MDate'          => date('Y-m-d H:i:s')  
);

$Description = explode(",", $_REQUEST['DS_Description']);
foreach ($Description as $key => $value) {
    
    $DescriptionObj->DS_Data["DS_Description"] = trim(htmlspecialchars($value, ENT_QUOTES));
            
    $temp = $DescriptionObj->verifyDescription(htmlspecialchars($_REQUEST['DS_Id'], ENT_QUOTES));
    if( $temp == 0 || $temp == '') {
            $DescriptionObj->DS_Data["DS_CDate"] = date('Y-m-d H:i:s'); 
            $DescriptionObj->newDescription();
            
            $flag++;
    } 
}
if($flag > 0) echo "Descriptions Added Successfully.";
    else echo "Description Already Exist.Please Re-try.";
?>