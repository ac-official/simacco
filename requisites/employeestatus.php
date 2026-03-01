<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/EmployeeStatusClass.php");
if($REQUEST["type"]=="filt") $ofid = $preTally_user_ofid;    
else $ofid = isset($REQUEST['ofid']) ? $REQUEST['ofid'] : 0 ;    
    $flds='ES_Id, ES_Name';
    $filter = "OF_Id =".$ofid." AND ES_Status=0";

$EmpStatusObj = new EmployeeStatusClass();
$EmpStatusObj->viewEmployeeStatus($flds,'WHERE  '.$filter.' ORDER BY ES_Id');
$DG_Obj = $EmpStatusObj->EmployeeStatusArray;

echo '<complete >';
if($REQUEST["type"]!="filt")echo '<option selected="true">Select Employee Status</option>';
else if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
if($DG_Obj){   
    foreach($DG_Obj as $rw) {
            echo '<option value="'.$rw->ES_Id.'" >'.$rw->ES_Name.'</option>';
    }
}
echo '</complete>';
?>