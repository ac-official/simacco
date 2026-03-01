<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/BankClass.php");
$BA_Id = $REQUEST['BA_Id'];
//$mask = $_REQUEST['mask'];
//$filter = 'AND CL.CL_Leaf like "'.$mask.'%"';
$chqlist=$_REQUEST['chqSelArray'];
if($chqlist){
$filter = " AND CL.CL_Id NOT IN (".$chqlist.") ";
}
$ChQObj = new BankClass();
$ChQObj->getChqNumber($BA_Id ,$filter);
$CHQ_Obj = $ChQObj->Cheque;

echo '<complete >';
if($CHQ_Obj){      
    foreach($CHQ_Obj as $rw) {
        echo '<option value="'.$rw->CL_Id.'" >'.$rw->CL_Leaf.'</option>';
    }
}else { echo '<option value="ZeroVal" selected="true">No Records Found</option>'; }
echo '</complete>';
?>