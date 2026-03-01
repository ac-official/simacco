<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

if($_REQUEST['ASD_Id'] != '') // for track automate form
    $supDocIdArray = explode(",", $_REQUEST['ASD_Id']);

$filter = "";
if($_REQUEST['mask'])
    $filter = " AND ASD_Document LIKE '".$_REQUEST['mask']."%' ";

$AttObj->getDetails('attestation_supporting_documents', '*', ' WHERE ASD_Status = 1 AND OF_Id = '.$preTally_user_ofid.' '.$filter.' ORDER BY ASD_Document');
$SuppDocs_Obj = $AttObj->DataArray;

echo '<complete >';
if($SuppDocs_Obj){      
    foreach($SuppDocs_Obj as $rw) {
        
        $optionParam = '';
        if($_REQUEST['ASD_Id'] != '') { // for track automate form
            $checked = in_array($rw->ASD_Id,$supDocIdArray) ? 1 : 0 ;
            $optionParam = 'checked="'.$checked.'" ' ;
        }

        echo '<option value="'.$rw->ASD_Id.'" '.$optionParam.' >'.$rw->ASD_Document.'</option>';
    }
}
echo '</complete>';
?>
