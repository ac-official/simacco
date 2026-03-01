<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$APMId   = $REQUEST['APMId'];
$AttObj->getDetails('attestation_process_main ','APM_Title_Alias AS AliasName , APMA_Id',' WHERE APM_Id ="'.$APMId.'" AND OF_Id = "'.$preTally_user_ofid.'"');
$APMObj = $AttObj->DataArray;

echo json_encode(array($APMObj[0]->AliasName,$APMObj[0]->APMA_Id));


//if($APMObj[0]->AliasName)
//    echo $APMObj[0]->AliasName." Process" ;
//else echo "Sub process";
?>