<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/UserClass.php");
$ofid = isset($REQUEST['ofid']) ? $REQUEST['ofid'] : 0 ;

//if($preTally_user_ofid == 1) {
//    $flds='SS.SS_Id, SS.SS_Name, OF.OF_Name';
//    $tbls='as SS, offices as OF';
//    $filter = 'SS.OF_Id=OF.OF_Id';
//}else {
    $flds='*';
    $filter = "OF_Id =".$ofid." AND SS_Status=1";
//}
$UserObj = new UserClass();
$UserObj->viewSalStruct($flds,$tbls,'WHERE '.$filter.' ORDER BY SS_Name');
$USal_Obj = $UserObj->SalStructArray;

echo '<complete >';
if($USal_Obj){
    echo '<option value="" selected="true">Select Salary Mode</option>';
    foreach($USal_Obj as $rw) {
        echo '<option value="'.$rw->SS_Id.'" >'.$rw->SS_Name.'</option>';
    }
}
echo '</complete>';
?>