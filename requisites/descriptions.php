<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$key = $_REQUEST["mask"]; 
include_once($BASEPATH."preTallyClass/DescriptionClass.php");
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
require_once($BASEPATH.'crypt/crypt.php');

$DescriptionObj = new DescriptionClass();
$AttObj         = new AttestationClass();

$IT_Id = $REQUEST['IT_Id'];
$DS_Id = $REQUEST['DS_Id'];
//if($IT_Id != 0)
//{
//    $DescriptionObj -> viewDescriptions('WHERE DS.IT_Id = "'.$IT_Id.'" AND DS.DS_Status=1 AND IT.SH_Id = SH.SH_Id AND DS.IT_Id = IT.IT_Id AND DS.OF_Id = "'.$preTally_user_ofid.'"');
//}else {
//    $DescriptionObj -> viewDescriptions('WHERE DS.DS_Description LIKE "'.$key.'%" AND DS.DS_Status=1 AND IT.SH_Id = SH.SH_Id AND DS.IT_Id = IT.IT_Id AND IT.IT_Status = 1 AND DS.OF_Id="'.$preTally_user_ofid.'"');
//}
if($REQUEST['type']=='init') $limit= "LIMIT 0,30";
if($REQUEST['filter']) $filter = 'DS.DS_Description LIKE "%'.$key.'%"';
    else $filter = 1;

if(isset($REQUEST['trackAction'])) {
   $IT_Id = $AttObj->getValue('attestation_job_balsheet_settings', 'IT_Id', " WHERE OF_Id = $preTally_user_ofid AND AJBS_Type = 6");
   $filter = 'DS.DS_Description LIKE "'.$key.'%"';
   
}
if($REQUEST['updateType']=='rpt'){
    $DescriptionObj -> viewDescriptions('WHERE DS.IT_Id = "'.$IT_Id.'" AND ( DS.DS_Status=1 ||  DS.DS_Status=3 || DS.DS_Id = '.$DS_Id.') AND IT.SH_Id = SH.SH_Id AND DS.IT_Id = IT.IT_Id AND DS.OF_Id="'.$preTally_user_ofid.'" AND '.$filter.'',$limit);
}else{
    $DescriptionObj -> viewDescriptions('WHERE DS.IT_Id = "'.$IT_Id.'" AND ( DS.DS_Status=1 ||  DS.DS_Status=3 ) AND IT.SH_Id = SH.SH_Id AND DS.IT_Id = IT.IT_Id AND DS.OF_Id="'.$preTally_user_ofid.'" AND '.$filter.'',$limit);
}
$DescObj = $DescriptionObj->DescriptionArray;

echo '<complete >';
if($DescObj){    
    foreach($DescObj as $rw) {
            echo '<option value="'.$rw->DS_Id.'" >'.$rw->DS_Description.'</option>';
        }            
    }
echo '</complete>';
?>