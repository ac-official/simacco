<?php
require_once($BASEPATH . "preTallyClass/SubheadClass.php");

$SubheadObj = new SubheadClass();
//die($_REQUEST['SH_Id']);
$SubheadObj->SH_Data = array(
        'US_Id' 	=> $preTally_user_id,
	'SH_Name' 	=> trim(htmlspecialchars($_REQUEST['SH_Name'], ENT_QUOTES)),
        'MH_Id' 	=> htmlspecialchars($_REQUEST['MH_Id'], ENT_QUOTES),
        'SH_Track'      => htmlspecialchars($_REQUEST['SH_Track'], ENT_QUOTES),
	'SH_Comments'	=> htmlspecialchars($_REQUEST['SH_Comments'], ENT_QUOTES),
	'SH_Status' 	=> htmlspecialchars($_REQUEST['SH_Status'], ENT_QUOTES),
	'SH_MDate' 	=> date('Y-m-d H:i:s')
);
//die($SubheadObj->verifySubhead());
if( $SubheadObj->verifySubhead(htmlspecialchars($_REQUEST['SH_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['SH_Id'], ENT_QUOTES) == 0) {
		$SubheadObj->SH_Data["SH_CDate"] = date('Y-m-d H:i:s'); 
		echo $SubheadObj->newSubhead();
	} else {
		echo $SubheadObj->updateSubhead(htmlspecialchars($_REQUEST['SH_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>