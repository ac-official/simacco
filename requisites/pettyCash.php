<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
$BalSheetObj = new BalanceSheetClass();

//$filter = 'US.US_Id = "'.$preTally_user_id.'" ';
$filter = ' IT.OF_Id ='.$preTally_user_ofid ;
$BalSheetObj->viewPettyCashPaid($filter);
$BS_Obj = $BalSheetObj->BalanceSheetArray;

echo '<complete >
<option value="0">Select Petty Cash</option>';
if($BS_Obj){
    foreach($BS_Obj as $rw) {       
        echo "<option value='".$rw->BS_Id."' >".$rw->BS_Amount." ( Bal : ".$rw->BS_PettyCashAmt." ) - ".$rw->DS_Description." ( ".date('d-M-y', strtotime($rw->BS_Date))." ) </option>";
    }
}
echo '</complete>';
?>
<!--//UPDATE `sh_pattern_list` SET `PL_PatternMap` = CONCAT(REPLACE(`PL_PatternMap`,']',''), ',"57"]')-->