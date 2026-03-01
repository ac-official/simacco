<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$filter =" WHERE APS.OF_Id = '".$preTally_user_ofid."' ORDER BY APS.APS_Id, APM.APM_Id";
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
//if($_REQUEST['mask']){
//    //if($_REQUEST['flag'])
//        $filter .= ' WHERE APS_Title like "%'.$_REQUEST['mask'].'%" ';
//} 

$AttObj->getDetails("attestation_process_sub AS APS LEFT JOIN attestation_process_main AS APM ON APS.APM_Id = APM.APM_Id","APS.APS_Id, APS.APS_Title, APM.APM_Id, APM.APM_Title",$filter);
$process_obj = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($process_obj){
    $id=0;
    $option="All";
    echo "<option value='".$id."' $selected >".str_replace("&","&amp;",$option).'</option>';  
    foreach($process_obj as $rw){
        echo "<option value='".$rw->APS_Id."' $selected >".str_replace("&","&amp;",$rw->APS_Title)." - ".str_replace("&","&amp;",$rw->APM_Title)."</option>";  
    }
}
echo '</complete>';

?>
