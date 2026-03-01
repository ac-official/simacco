<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

$OF_Id = $preTally_user_ofid;
$filter = "";
if($REQUEST['id'])     // editing documents
    $filter .= " AND ADOC_Id = ".$REQUEST['id'];

if($_REQUEST["mask"])  // auto filtering
    $filter .= ' AND ADOC_Document LIKE "'.mysqli_real_escape_string($GLOBALS['con'],$_REQUEST["mask"]).'%"';

if($_REQUEST['ADOCId'] != '') // for track automate form
    $AdocIdArray = explode(",", $_REQUEST['ADOCId']);

$AttestObj = new AttestationClass();
$AttestObj->getDetails('attestation_documents', 'ADOC_Id,ADOC_Document,ADOC_Type', ' WHERE OF_Id='.$OF_Id.' AND ADOC_Status = 1 '.$filter.' ORDER BY ADOC_Type, ADOC_Document');
$AUTH_Obj = $AttestObj->DataArray;
//SELECT ADOC_Id,ADOC_Document,ADOC_Type FROM attestation_documents  WHERE OF_Id=4 AND ADOC_Status = 1
//  ORDER BY ADOC_Type, ADOC_Document


if($_REQUEST['form'] == 'automate'){
//    $docTypeArray   =   array("","[E]","[N]","[C]");
    $educationIds = $nonEducationIds = $commercialIds = array();
    foreach($AUTH_Obj as $rw) {
        if($rw->ADOC_Type == 1)
            array_push($educationIds,$rw->ADOC_Id);
        if($rw->ADOC_Type == 2)
            array_push($nonEducationIds,$rw->ADOC_Id);
        if($rw->ADOC_Type == 3)
            array_push($commercialIds,$rw->ADOC_Id);
    }
}
echo '<complete>';
if($AUTH_Obj){
    $typeFlag = 0 ;
    foreach($AUTH_Obj as $rw) {
        $optionParam = '';
        if($_REQUEST['ADOCId'] != '') { // for track automate form
            $checked = in_array($rw->ADOC_Id,$AdocIdArray) ? 1 : 0 ;
            $optionParam = 'checked="'.$checked.'" ' ;
        }
        if($_REQUEST['form'] == 'automate') {
            if($typeFlag == 0 && $rw->ADOC_Type == 1){
                echo '<option value="'.implode(',', $educationIds).'" css ="font-weight : bold; color : blue;" >Education</option>';
                $typeFlag = 1;
            }
            if($typeFlag == 1 && $rw->ADOC_Type == 2){
                echo '<option value="'.implode(',', $nonEducationIds).'" css ="font-weight : bold; color : blue;" >Non-Education</option>';
                $typeFlag = 2;
            }
            if($typeFlag == 2 && $rw->ADOC_Type == 3){
                echo '<option value="'.implode(',', $commercialIds).'" css ="font-weight : bold; color : blue;" >Commercial</option>';
                $typeFlag = 3;
            }
//            
//            echo '<option value="'.$rw->ADOC_Id.'" '.$optionParam.'>'.$rw->ADOC_Document.'</option>';
        }//else
            echo '<option value="'.$rw->ADOC_Id.'" '.$optionParam.'>'.$rw->ADOC_Document.'</option>';
    }
}
else 
    echo '<option value="" selected="true">No Records Found</option>'; 
echo '</complete>';
?>