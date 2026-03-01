<?php
require_once($BASEPATH . "preTallyClass/MainheadClass.php");
$MainheadObj = new MainheadClass();
$MainheadObj->MH_Data = array(
        'US_Id' 	=> $preTally_user_id,
	'MH_Name' 	=> trim(htmlspecialchars($_REQUEST['MH_Name'], ENT_QUOTES)),
	'MH_Comments'	=> htmlspecialchars($_REQUEST['MH_Comments'], ENT_QUOTES),
	'MH_Status' 	=> htmlspecialchars($_REQUEST['MH_Status'], ENT_QUOTES),
        'MH_Type' 	=> htmlspecialchars($_REQUEST['MH_Type'], ENT_QUOTES),
	'MH_MDate' 	=> date('Y-m-d H:i:s')
);
//die($MainheadObj->verifyMainhead());
if( $MainheadObj->verifyMainhead(htmlspecialchars($_REQUEST['MH_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['MH_Id'], ENT_QUOTES) == 0) {
		$MainheadObj->MH_Data["MH_CDate"] = date('Y-m-d H:i:s'); 
		echo $MainheadObj->newMainhead();
	} else {
		echo $MainheadObj->updateMainhead(htmlspecialchars($_REQUEST['MH_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>