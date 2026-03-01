<?php
require_once($BASEPATH . "preTallyClass/BankClass.php");

$BranchObj = new BankClass();
$BranchObj->Branch_Data = array(        
	'BB_Name' 	=> trim(htmlspecialchars($_REQUEST['BB_Name'], ENT_QUOTES)),        	
        'BB_Address' 	=> htmlspecialchars($_REQUEST['BB_Address'], ENT_QUOTES),
        'BB_Comments' 	=> htmlspecialchars($_REQUEST['BB_Comments'], ENT_QUOTES),
        'BNK_Id' 	=> htmlspecialchars($_REQUEST['BNK_Id'], ENT_QUOTES),
        'OF_Id'         => $preTally_user_ofid,
        'BB_Status' 	=> htmlspecialchars($_REQUEST['BB_Status'], ENT_QUOTES),
	'BB_MDate' 	=> date('Y-m-d H:i:s')
);

if( $BranchObj->verifyBranch($_REQUEST['BB_Id'],$preTally_user_ofid) ) {
	if(htmlspecialchars($_REQUEST['BB_Id'], ENT_QUOTES) == 0) {
		$BranchObj->Branch_Data['BB_CDate']=date('Y-m-d H:i:s');
		echo $BranchObj->newBranch();
                
	} else {
		echo $BranchObj->updateBranch(htmlspecialchars($_REQUEST['BB_Id'], ENT_QUOTES));
	}
} else { echo 'Bank Branch already exists'; }
?>