<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttestObj = new AttestationClass();

$filter = "";
if($REQUEST['id'])      // edit document
    $filter .= " AND AST_Id = ".$REQUEST['id'];

if($_REQUEST['mask'])   // auto filtering
    $filter .= " AND AST_State LIKE '".$_REQUEST['mask']."%' ";

$AttestObj->getAttestationStates($filter);
$AST_Obj = $AttestObj->DataArray;
$selected = "";
echo '<complete>';
if($AST_Obj){
    foreach($AST_Obj as $rw) {
        if(isset($REQUEST['ST_Id'])) {
            if($REQUEST['ST_Id'] == $rw->AST_Id)
                $selected =' selected = "true" ';
        }
        echo '<option '.$selected.' value="'.$rw->AST_Id.'" >'.$rw->AST_State.'</option>';
    }
} else { 
    if(!$REQUEST['type'])
        echo '<option value="" selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>