<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$key = $_REQUEST["mask"]; 
require_once($BASEPATH . "preTallyClass/BankClass.php");
$BNK_Id=$_REQUEST['BNK_Id'];
$BranchObj = new BankClass();
$BranchObj->viewBranch('BB.BB_Id,BB.BB_Name,BB.BB_Comments,BB.BNK_Id,BB.OF_Id,BB.BB_Status,BNK.BNK_Name',' AS BB,banks AS BNK WHERE BB.OF_Id='.$preTally_user_ofid.' AND BB.BB_Status=1 AND BNK.BNK_Status=1 AND BB.BNK_Id=BNK.BNK_Id');
$Branch_Obj = $BranchObj->BranchArray;
echo '<complete >';
echo '<option selected="true" value="">Select Bank Branch</option>';
if($Branch_Obj){      
    foreach($Branch_Obj as $rw) {
        echo '<option value="'.$rw->BB_Id.'" >'.$rw->BNK_Name.'-'.$rw->BB_Name.'</option>';
    }
}
echo '</complete>';
?>