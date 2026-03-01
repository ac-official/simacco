<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/BankClass.php");
$BNK_Id = $REQUEST['Bnk_Id'];

$BranchObj = new BankClass();
$BranchObj->viewBranch('*', ' WHERE OF_Id = '.$preTally_user_ofid.' AND BNK_Id='.$BNK_Id.' AND BB_Status = 1');
$BB_Obj = $BranchObj->BranchArray;

echo '<complete >';
if($BB_Obj){      
    foreach($BB_Obj as $rw) {
        echo '<option value="'.$rw->BB_Id.'" >'.$rw->BB_Name.'</option>';
    }
}
echo '</complete>';
?>