<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$AttObj = new AttestationClass();

$AttObj->getSubProcessList($REQUEST['MP_Id'],$REQUEST['docId']);
$DocObj = $AttObj->DataArray;

echo '<complete >
        <option selected="true">Select Sub Process</option>';
        if($DocObj){
            foreach($DocObj as $rw) {       
                echo '<option value="'.$rw->APS_Id.'" >'.$rw->APS_Title.'</option>';
            }
        }
echo '</complete>';
?>