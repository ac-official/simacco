<?php
require_once($BASEPATH . "preTallyClass/UnitClass.php");

$UnitObj = new UnitClass();
//die($_REQUEST['SH_Id']);
$UnitObj->Unit_Data = array(        
	'UT_Name' 	=> trim(htmlspecialchars($_REQUEST['UT_Name'], ENT_QUOTES)),        
	'UT_Comments'	=> htmlspecialchars($_REQUEST['UT_Comments'], ENT_QUOTES),
        'UT_Status' 	=> htmlspecialchars($_REQUEST['UT_Status'], ENT_QUOTES),
	'UT_MDate' 	=> date('Y-m-d H:i:s')
);
//die($SubheadObj->verifySubhead());
if( $UnitObj->verifyUnit(htmlspecialchars($_REQUEST['UT_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['UT_Id'], ENT_QUOTES) == 0) {
		$UnitObj->Unit_Data['UT_CDate']=date('Y-m-d H:i:s');
		echo $UnitObj->newUnit();
                
	} else {
		echo $UnitObj->updateUnit(htmlspecialchars($_REQUEST['UT_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>