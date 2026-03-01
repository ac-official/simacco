<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
} 
require_once($BASEPATH . "preTallyClass/BankClass.php");
$AccObj = new BankClass();
$AccObj->viewCompnySalBankName($preTally_user_ofid);
$Acc_Obj = $AccObj->BankNames;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($REQUEST['id'] != "" && $REQUEST['id'] != 0) { 
    $selected = '';
}
else { 
    $selected = ' selected="true" ';
}
echo '<option '.$selected.' value="">Select Bank Account</option>';
if($Acc_Obj){      
    foreach($Acc_Obj as $rw) {       
        echo '<option ';
        if(isset($REQUEST['id'])) {
            if($REQUEST['id'] == $rw->Sal_BnkId ) {
                echo ' selected="true" ';
            }
        }
        echo ' value="'.$rw->Sal_BnkId.'" >'.$rw->Sal_BnkName.'</option>';
    }
}
echo '</complete>';
?>