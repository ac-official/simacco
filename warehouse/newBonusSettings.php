<?php
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GenObj             = new GeneralClass();

$BPS_Id             = $_REQUEST['BPS_Id'];
$OF_Id              = $preTally_user_ofid;
$BPS_BranchShare    = trim($_REQUEST['BPS_BranchShare']);
$BPS_GroupShare     = trim($_REQUEST['BPS_GroupShare']);
$BPS_Year           = trim($_REQUEST['BPS_Year']);
$BPS_Month          = trim($_REQUEST['BPS_Month']);
$BPS_AddedBy        = $preTally_user_id;
$BPS_LastUpdatedBy  = $preTally_user_id;
$BPS_CDate          = date('Y-m-d H:i:s');
$BPS_MDate          = date('Y-m-d H:i:s');

$count = $GenObj->Count("*",'bonus_percentage_settings',"BPS_Year = ".$_REQUEST['BPS_Year']." AND BPS_Month = ".$_REQUEST['BPS_Month']." AND OF_Id = ".$preTally_user_ofid );
if($BPS_Id) {
    if($_REQUEST['BPS_Id']) {
        $GenObj->Update('bonus_percentage_settings'," BPS_BranchShare = $BPS_BranchShare,BPS_GroupShare = $BPS_GroupShare,BPS_Year = $BPS_Year,BPS_Month = $BPS_Month,BPS_LastUpdatedBy = $BPS_LastUpdatedBy,BPS_MDate = '$BPS_MDate'", "BPS_Id = $BPS_Id");
        echo "Settings Updated Successfully";
    } 
} else {
    if($count)
        echo "fail";
    else {
        $GenObj->Insert('bonus_percentage_settings',"OF_Id,BPS_BranchShare,BPS_GroupShare,BPS_Year,BPS_Month,BPS_AddedBy,BPS_LastUpdatedBy,BPS_CDate,BPS_MDate","$OF_Id,$BPS_BranchShare,$BPS_GroupShare,$BPS_Year,$BPS_Month,$BPS_AddedBy,$BPS_LastUpdatedBy,'$BPS_CDate','$BPS_MDate'");
        echo 'Settings Created Successfully';
    }
}
?>