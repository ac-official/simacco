<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$filter = "";
if($REQUEST['aid'])     // editing documents
    $filter .= " AND AAUTH_Id = ".$REQUEST['aid'];
else if($REQUEST['id'])        // filtering on state change
    $filter .= " AND AST_Id = ".$REQUEST['id'];

if($_REQUEST['mask'])           // auto filtering
    $filter .= " AND AAUTH_Authority LIKE  '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($_REQUEST['mask']))."%' ";

if($_REQUEST['AAUTHId'] != '') // for track automate form
    $AauthIdArray = explode(",", $_REQUEST['AAUTHId']);

$AttestObj = new AttestationClass();


if($_REQUEST['form'] == 'automate'){
    $AttestObj->getAuthorityList();
    $AUTH_Obj = $AttestObj->DataArray;
    
    echo '<complete>';
    if($AUTH_Obj){
        foreach($AUTH_Obj as $rw) {
            if($_REQUEST['form'] == 'automate' && $rw['AST_AUId'] != '') {
                echo '<option value="'.$rw['AST_AUId'].'" css ="font-weight : bold; color : blue;" >'.$rw['AST_State'].'</option>';
            }

            $optionParam = '';
            if($_REQUEST['AAUTHId'] != '') { // for track automate form
                $checked = in_array($rw['AAUTH_Id'],$AauthIdArray) ? 1 : 0 ;
                $optionParam = 'checked="'.$checked.'" ' ;
            }

            echo '<option ';
            if($REQUEST['aid'] && $REQUEST['aid'] == $rw['AAUTH_Id']) 
                echo 'selected = "true"';
            echo ' value="'.$rw['AAUTH_Id'].'" '.$optionParam.'>'.$rw["AAUTH_Authority"].'</option>';
        }
    }
    else 
        echo '<option value="" selected="true">No Records Found</option>'; 
    echo '</complete>';
    
}else{
    $AttestObj->getDetails('attestation_authorities', 'AAUTH_Id,AAUTH_Authority', ' WHERE AAUTH_Status = 1 '.$filter.' ORDER BY AAUTH_Authority');

    $AUTH_Obj = $AttestObj->DataArray;

    echo '<complete>';
    if($AUTH_Obj){
        foreach($AUTH_Obj as $rw) {
            $optionParam = '';
            if($_REQUEST['AAUTHId'] != '') { // for track automate form
                $checked = in_array($rw->AAUTH_Id,$AauthIdArray) ? 1 : 0 ;
                $optionParam = 'checked="'.$checked.'" ' ;
            }

            echo '<option ';
            if($REQUEST['aid'] && $REQUEST['aid'] == $rw->AAUTH_Id) 
                    echo 'selected = "true"';
            echo ' value="'.$rw->AAUTH_Id.'" '.$optionParam.'>'.$rw->AAUTH_Authority.'</option>';
        }
    }
    else 
        echo '<option value="" selected="true">No Records Found</option>'; 
    echo '</complete>';
}
?>
