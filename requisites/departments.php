<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/DepartmentClass.php");
if($REQUEST["type"]=="filt") $ofid = $preTally_user_ofid;    
else $ofid = isset($REQUEST['ofid']) ? $REQUEST['ofid'] : 0 ;    
    $flds='DP_Id, DP_Name';
    $filter = "OF_Id =".$ofid." AND DP_Status=1";


$DepartmentObj = new DepartmentClass();
$DepartmentObj->viewDepartments($flds,$tbls,'WHERE  '.$filter.' ORDER BY DP_Name');
$DP_Obj = $DepartmentObj->DepartmentArray;

echo '<complete >';
if($REQUEST["type"]!="filt")echo '<option selected="true">Select Department</option>';
else if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
if($DP_Obj){
    
    foreach($DP_Obj as $rw) {       
        echo '<option value="'.$rw->DP_Id.'" >'.$rw->DP_Name.'</option>';
    }
}
echo '</complete>';
?>