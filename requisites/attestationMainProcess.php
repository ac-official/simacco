<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$filter = "";
if($_REQUEST['mask'])
    $filter = " AND APM_Title LIKE '".$_REQUEST['mask']."%' ";
$AttObj->getDetails('attestation_process_main', '*', ' WHERE APM_Status = 1 AND OF_Id = "'.$preTally_user_ofid.'" '.$filter.' ORDER BY APM_Title');
$SubProcess_Obj = $AttObj->DataArray;
echo '<complete >';
if($SubProcess_Obj){      
    foreach($SubProcess_Obj as $rw) {
        echo '<option value="'.$rw->APM_Id.'" >'.$rw->APM_Title.'</option>';
    }
}
echo '</complete>';
?>
