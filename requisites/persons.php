<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
$key = $_REQUEST["mask"];
$usid=0;

if(($REQUEST["us_id"]!=null) && ($REQUEST["us_id"]!=0)){
$usid =$REQUEST["us_id"];
}
if($REQUEST['LCId'])
    $cond = ' AND LC_Id ='.$REQUEST['LCId'];

$PersonsObj = new BalanceSheetClass();

if($preTally_user_ofid == 1) {
    $fields='DISTINCT US.US_Id, US.US_FName, US.US_LName, US.OF_Id, OF.OF_Name';
    $tbls='users_auth as US, offices as OF';
    $filter = 'US.OF_Id=OF.OF_Id AND US.US_Status=1 AND US.US_Id!='.$usid;
}else {
    $fields='DISTINCT US_Id, US_FName, US_LName';
    $tbls='users_auth';
    $filter = "US_Status=1 AND OF_Id = ".$preTally_user_ofid." AND US_Id!=".$usid . $cond;
}
$PersonsObj -> viewUser($fields, $tbls, $key , $filter);
$User_Obj = $PersonsObj->BalanceSheetArray;

echo '<complete >';
if($REQUEST['ctype']=='check')echo '<option value="0">All</option>';
if($key == 'Self') echo '<option value="'.$preTally_user_id.'"  selected ="true">Self ( '.$preTally_user_name.' )</option>';
if($User_Obj){      
    foreach($User_Obj as $rw) {
        if($preTally_user_ofid == 1) { $OFNM = '( '.$rw->OF_Name.' )';}
        echo '<option value="'.$rw->US_Id.'" >'.$rw->US_FName.' '.$rw->US_LName.' '.$OFNM.'</option>';
    }
}else { if($key != 'Self' && $REQUEST['ctype'] != 'check') echo '<option value="ZeroVal" selected="true">No Records Found</option>'; }
echo '</complete>';
?>
