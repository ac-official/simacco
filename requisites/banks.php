<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/BankClass.php");
$key = $_REQUEST["mask"]; 


$BanksObj = new BankClass();
$BanksObj ->viewBanks('WHERE BNK_Status = 1 ORDER BY BNK_Name');
$Banks_Obj = $BanksObj->BankArray;

echo '<complete >'
.'<option value="" selected="true">Select Bank</option>';
if($Banks_Obj){      
    foreach($Banks_Obj as $rw) {
        echo '<option value="'.$rw->BNK_Id.'" >'.$rw->BNK_Name.'</option>';
    }
}
echo '</complete>';
?>