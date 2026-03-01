<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

if($REQUEST['AA_VisaType'] != '') // for track automate form
    $VisaTypeArray = explode(",", $REQUEST['AA_VisaType']);

echo '<complete>';
    $checked = in_array(1,$VisaTypeArray) ? 1 : 0 ; echo'<option value="1" checked="'.$checked.'" >Job Visa</option>';
    $checked = in_array(2,$VisaTypeArray) ? 1 : 0 ; echo'<option value="2" checked="'.$checked.'" >Visiting Visa</option>';
    $checked = in_array(3,$VisaTypeArray) ? 1 : 0 ; echo'<option value="3" checked="'.$checked.'" >Business Visa</option>';
    $checked = in_array(4,$VisaTypeArray) ? 1 : 0 ; echo'<option value="4" checked="'.$checked.'" >Family Visa</option>';
    $checked = in_array(5,$VisaTypeArray) ? 1 : 0 ; echo'<option value="5" checked="'.$checked.'" >Transit Visa</option>';
    $checked = in_array(6,$VisaTypeArray) ? 1 : 0 ; echo'<option value="6" checked="'.$checked.'" >Tourist Visa</option>
</complete>';
    
/*
$AttestObj = new AttestationClass();
$AttestObj->getDetails('attestation_documents', 'ADOC_Id,ADOC_Document', ' WHERE OF_Id='.$OF_Id.' AND ADOC_Status = 1 '.$filter.' ORDER BY ADOC_Document');
$AUTH_Obj = $AttestObj->DataArray;
echo '<complete>';
if($AUTH_Obj){
    foreach($AUTH_Obj as $rw) {
        $optionParam = '';
        if($REQUEST['ADOCId'] != '') { // for track automate form
            $checked = in_array($rw->ADOC_Id,$AdocIdArray) ? 1 : 0 ;
            $optionParam = 'checked="'.$checked.'" ' ;
        }
        echo '<option value="'.$rw->ADOC_Id.'" '.$optionParam.'>'.$rw->ADOC_Document.'</option>';
    }
}
else 
    echo '<option value="" selected="true">No Records Found</option>'; 
echo '</complete>';
 * 
 */
?>