<?php
include_once($BASEPATH . "preTallyClass/PaymodeClass.php");
$PaymodeObj = new PaymodeClass();
$PaymodeObj->Paymode_Data = array(        
	'PM_Name' 	=> trim(htmlspecialchars($_REQUEST['PM_Name'], ENT_QUOTES)),        
	'PM_Comments'	=> htmlspecialchars($_REQUEST['PM_Comments'], ENT_QUOTES),
        'PM_Status' 	=> htmlspecialchars($_REQUEST['PM_Status'], ENT_QUOTES),
        'PM_MDate' 	=> date('Y-m-d H:i:s')
);

if( $PaymodeObj->verifyPaymode(htmlspecialchars($_REQUEST['PM_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['PM_Id'], ENT_QUOTES) == 0) {
                $PaymodeObj->Paymode_Data["PM_CDate"] = date('Y-m-d H:i:s'); 
		echo $PaymodeObj->newPaymode();                
	} else {
		echo $PaymodeObj->updatePaymode(htmlspecialchars($_REQUEST['PM_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>