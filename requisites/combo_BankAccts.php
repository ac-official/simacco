<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
} 
require_once($BASEPATH . "preTallyClass/BankClass.php");
$AccObj = new BankClass();
$AccObj->viewAccounts(" WHERE BA.OF_Id=".$preTally_user_ofid." AND BNK.BNK_Status=1 AND BB.BB_Status=1 AND BA.BA_Status=1 AND BA.BB_Id=BB.BB_Id AND BNK.BNK_Id=BB.BNK_Id ORDER BY BA.BA_DispName ASC");
$Acc_Obj = $AccObj->BankAccount;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
echo '<option selected="true" value="">Select Bank Account</option>';
if($Acc_Obj){      
    foreach($Acc_Obj as $rw) {        
            $bank_name=$rw->BNK_Abbr.'-'.substr($rw->BA_No,-6).'-'.$rw->BB_Name;
        echo '<option  value="'.$rw->BNK_Id.'_'.$rw->BB_Id.'_'.$rw->BA_Id.'" >'.$rw->BA_DispName.'</option>';
    }
}
echo '</complete>';
?>