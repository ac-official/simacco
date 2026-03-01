<?php
// Always suppress errors here to keep XML valid
include_once($BASEPATH."preTallyClass/BalanceSheetClass.php");
$attObj = new BalanceSheetClass();
// Send headers
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$key = '';
$usid=0;
if($preTally_user_ofid == 1) {
    $fields='DISTINCT US.US_Id, US.US_FName, US.US_LName, US.OF_Id, OF.OF_Name';
    $tbls='users_auth as US, offices as OF';
    $filter = 'US.OF_Id=OF.OF_Id AND US.US_Status=1 AND US.US_Id!='.$usid;
}else {
    $fields='DISTINCT US.US_Id, US.US_FName, US.US_LName, LC.LC_Name';
    $tbls='users_auth as US, locations as LC';
    $filter = "US.LC_Id=LC.LC_Id AND US.US_Status=1 AND US.OF_Id = ".$preTally_user_ofid." AND US.US_Id!=".$usid;
}
$attObj->viewUser($fields, $tbls, $key , $filter);
$User_Obj = $attObj->BalanceSheetArray;
echo '<complete >';
if($User_Obj){  
    echo '<option value="" selected="true">Select User</option>'    ;
    foreach($User_Obj as $rw) {
        echo '<option value="'.$rw->US_Id.'" >'.$rw->US_FName.' '.$rw->US_LName.' ('. $rw->LC_Name .')'.$OFNM.'</option>';
    }
}else { 
    echo '<option value="ZeroVal" selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>

