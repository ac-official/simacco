<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
} 
require_once($BASEPATH . "preTallyClass/BankClass.php");
$AccObj = new BankClass();
$offic_id  = (isset($_REQUEST['ofid']) && $_REQUEST['ofid'] > 0) ? $_REQUEST['ofid'] : $preTally_user_ofid; //14-11-2025
$seltd_id  = (isset($_REQUEST['seltdId']) && $_REQUEST['seltdId'] > 0) ? $_REQUEST['seltdId'] : 0; //14-11-2025s
$AccObj->viewAccounts(" WHERE BA.OF_Id=".$offic_id." AND BNK.BNK_Status=1 AND BB.BB_Status=1 AND BA.BA_Status=1 AND BA.BB_Id=BB.BB_Id AND BNK.BNK_Id=BB.BNK_Id ");
$Acc_Obj = $AccObj->BankAccount;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
$selected   = 'selected="true"'; //14-11-2025
if ( $seltd_id > 0 ) { // selected value present then no need to select default
    $selected   = '';
}
echo '<option '.$selected.' value="">Select Bank Account</option>';
if($Acc_Obj){      
    foreach($Acc_Obj as $rw) {
        if($rw->BA_DispName!="")
        {
            $bank_name=$rw->BA_DispName;
        }
        else {
            $bank_name=$rw->BNK_Abbr.'-'.$rw->BB_Name;
        }
        $selected   = ( $seltd_id ==  $rw->BA_Id) ? 'selected="true"' : ''; // selected checking added 14-11-2025        
        echo '<option  value="'.$rw->BA_Id.'" '.$selected.'>'.substr($rw->BA_No,-6).'-'.$bank_name.'</option>';
    }
}
echo '</complete>';
?>