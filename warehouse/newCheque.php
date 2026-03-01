<?php
include_once($BASEPATH . "preTallyClass/BankClass.php");

$ChequeObj = new BankClass();
$ChequeObj->Cheque_Data = array(        
	'BA_Id' 	=> htmlspecialchars($_REQUEST['BA_Id'], ENT_QUOTES),        	
        'CHQ_BookNo' 	=> trim(htmlspecialchars($_REQUEST['CHQ_BookNo'], ENT_QUOTES)),
        'CHQ_Firstleaf' => trim(htmlspecialchars($_REQUEST['CHQ_Firstleaf'], ENT_QUOTES)),        
        'CHQ_Nextleaf'  => trim(htmlspecialchars($_REQUEST['CHQ_Firstleaf'], ENT_QUOTES)),
	'CHQ_Lastleaf' 	=> trim(htmlspecialchars($_REQUEST['CHQ_Lastleaf'], ENT_QUOTES)),                     
        'CHQ_Status'    => htmlspecialchars($_REQUEST['CHQ_Status'], ENT_QUOTES),
        'OF_Id'         => $preTally_user_ofid);

if( $ChequeObj->verifyCheque(htmlspecialchars($_REQUEST['CHQ_Id'], ENT_QUOTES),$preTally_user_ofid )) {
	if(htmlspecialchars($_REQUEST['CHQ_Id'], ENT_QUOTES) == 0) {
		$ChequeObj->Cheque_Data['CHQ_CDate']=date('Y-m-d H:i:s');
                $ChequeObj->Cheque_Data['CHQ_MDate']=date('Y-m-d H:i:s');               
		$id= $ChequeObj->newCheque();
                echo $ChequeObj->newChqLeafs($id);
                
	} else {
		echo $ChequeObj->updateCheque(htmlspecialchars($_REQUEST['CHQ_Id'], ENT_QUOTES));
                $ChequeObj->Cheque_Data['CHQ_MDate']=date('Y-m-d H:i:s');
	}
} else { echo 'fail'; }
?>