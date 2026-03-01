<?php
include_once($BASEPATH . "preTallyClass/BankClass.php");

$BranchObj = new BankClass();
$BranchObj->Account_Data = array(        
	'BA_No' 	=> htmlspecialchars($_REQUEST['BA_No'], ENT_QUOTES),  
        'BA_DispName' 	=> trim(htmlspecialchars($_REQUEST['BA_DispName'], ENT_QUOTES)),
        'BB_Id' 	=> htmlspecialchars($_REQUEST['BB_Id'], ENT_QUOTES),        
        'LC_Id'         => htmlspecialchars($_REQUEST['LC_Id'], ENT_QUOTES),
        'BA_Status' 	=> htmlspecialchars($_REQUEST['BA_Status'], ENT_QUOTES),        
	'BA_MDate' 	=> date('Y-m-d H:i:s')
);
$CHECK_1= $BranchObj->verifyAccount($preTally_user_ofid,$_REQUEST['BA_Id'] );
$CHECK_2= $BranchObj->verifyDispName($preTally_user_ofid, $_REQUEST['BA_Id']);
if($CHECK_1 && $CHECK_2 ) {
    
	if(htmlspecialchars($_REQUEST['BA_Id'], ENT_QUOTES) == 0) {
		$BranchObj->Account_Data['BA_CDate']=date('Y-m-d H:i:s');
                $BranchObj->Account_Data['OF_Id']= $preTally_user_ofid;
		$BranchObj->newAccount();
                $ba_id=  mysqli_insert_id($GLOBALS['con']);
                $BranchObj->Bal_Data = array(        
                'OF_Id' 	=> $preTally_user_ofid,        	                    	   
                'BA_Id' 	=> $ba_id,        
                'BnkOB_OpenBal'   => 0,
                'BnkOB_Status' 	=> 0,        
                'BnkOB_CDate' 	=> date('Y-m-d H:i:s')
        );
            $BranchObj->createBnkBalances();
            echo "new";
                
	} else {
             $BranchObj->updateAccount(htmlspecialchars($_REQUEST['BA_Id'], ENT_QUOTES));
             echo "update";

}
        } else { if(!$CHECK_1)echo 'Account Number Repeated';
                else if(!$CHECK_2)echo 'Display Name Repeated';}
?>