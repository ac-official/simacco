<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/DesignationClass.php");
if($REQUEST["type"]=="filt") $ofid = $preTally_user_ofid;    
else $ofid = isset($REQUEST['ofid']) ? $REQUEST['ofid'] : 0 ;    
//if($preTally_user_ofid == 1) {
//    $flds='DG.DG_Id, DG.DG_Name, OF.OF_Name';
//    $tbls='as DG, offices as OF';
//    $filter = 'DG.OF_Id=OF.OF_Id';
//}else {
    $flds='DG_Id, DG_Name';
    $filter = "OF_Id =".$ofid." AND DG_Status=1";
//}

$DesignationObj = new DesignationClass();
echo $DesignationObj->viewDesignations($flds,$tbls,'WHERE  '.$filter.' ORDER BY DG_Name');
$DG_Obj = $DesignationObj->DesignationArray;

echo '<complete >';
if($REQUEST["type"]!="filt")echo '<option selected="true">Select Designation</option>';
else if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
if($DG_Obj){
   
    foreach($DG_Obj as $rw) {
            echo '<option value="'.$rw->DG_Id.'" >'.$rw->DG_Name.'</option>';
    }
}
echo '</complete>';
?>