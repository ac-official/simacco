<?php
include_once($BASEPATH . "preTallyClass/BankClass.php");
$BankObj = new BankClass();
$BankObj->Bank_Data = array(        
	'BNK_Name' 	=> trim(htmlspecialchars($_REQUEST['BNK_Name'], ENT_QUOTES)),        	
        'BNK_Status' 	=> htmlspecialchars($_REQUEST['BNK_Status'], ENT_QUOTES),
	'BNK_MDate' 	=> date('Y-m-d H:i:s')
);
if( $BankObj->verifyBank(htmlspecialchars($_REQUEST['BNK_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($_REQUEST['BNK_Id'], ENT_QUOTES) == 0) {
		$BankObj->Bank_Data['BNK_CDate']=date('Y-m-d H:i:s');
		echo $BankObj->newBank();
                
	} else {
		echo $BankObj->updateBank(htmlspecialchars($_REQUEST['BNK_Id'], ENT_QUOTES));
	}
} else { echo 'fail'; }
?>