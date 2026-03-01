<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->getAPMADetails(' attestation_process_main_authority', '*', 'ORDER BY APMA_Title');
$MainProcess_Obj = $AttObj->DataArray;
echo '<complete >';
if($MainProcess_Obj){      
    foreach($MainProcess_Obj as $rw) {
        echo '<option value="'.$rw->APMA_Id.'" >'.$rw->APMA_Title.'</option>';
    }
}
echo '</complete>';
?>
