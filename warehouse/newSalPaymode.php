<?php
include_once($BASEPATH . "preTallyClass/SalPaymodeClass.php");

$SalPaymodeObj = new SalPaymodeClass();
$SalPaymodeObj->SalPaymode_Data = array(        
	'SP_Name' 	=> trim(htmlspecialchars($_REQUEST['SP_Name'], ENT_QUOTES)),        
	'SP_Comments'	=> htmlspecialchars($_REQUEST['SP_Comments'], ENT_QUOTES),
        'SP_Status' 	=> htmlspecialchars($_REQUEST['SP_Status'], ENT_QUOTES),
	'SP_MDate' 	=> date('Y-m-d H:i:s')
);

if( $SalPaymodeObj->verifySalPaymode(htmlspecialchars($_REQUEST['SP_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['SP_Id'], ENT_QUOTES) == 0) {
            $SalPaymodeObj->SalPaymode_Data["SP_CDate"] = date('Y-m-d H:i:s');
		echo $SalPaymodeObj->newSalPaymode();                
	} else {
		echo $SalPaymodeObj->updateSalPaymode(htmlspecialchars($_REQUEST['SP_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>