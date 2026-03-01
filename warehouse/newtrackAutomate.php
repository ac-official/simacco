<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

$AttObj->Data = array(
    'US_Id'         => $preTally_user_id ,
    'OF_Id'         => $preTally_user_ofid ,
    'CN_Id'         => trim(htmlspecialchars(implode(",",$_REQUEST['CN_Id']), ENT_QUOTES)) ,
    'AA_VisaType'   => trim(htmlspecialchars(implode(",",$_REQUEST['AA_VisaType']), ENT_QUOTES)) ,
    'ADOC_Id'       => trim(htmlspecialchars(implode(",",$_REQUEST['ADOC_Id']), ENT_QUOTES)) ,
    'AA_Issuing_CNId'  => trim(htmlspecialchars(implode(",",$_REQUEST['AA_Issuing_CNId']), ENT_QUOTES)) ,
    'AAUTH_Id'      => trim(htmlspecialchars(implode(",",$_REQUEST['AAUTH_Id']), ENT_QUOTES)) ,
    'AA_FromYear'   => trim(htmlspecialchars($_REQUEST['AA_FromYear'], ENT_QUOTES)) ,
    'AA_ToYear'     => trim(htmlspecialchars($_REQUEST['AA_ToYear'], ENT_QUOTES)) ,
    'AA_Remarks'    => trim(htmlspecialchars($_REQUEST['AA_Remarks'], ENT_QUOTES)) ,
    'AA_AutomateData' =>  trim(($_REQUEST['AA_AutomateData'])) ,
    'AA_MDate'      => date('Y-m-d H:i:s') ,
    'AA_Status'     => 1
);

if(htmlspecialchars($_REQUEST['AA_Id'], ENT_QUOTES) == 0) {
    $AttObj->Data["AA_CDate"] = date('Y-m-d H:i:s');
    if($AttObj->insertRecords('attestation_automate') == "success")
        echo "Successfully Created";
    else
        echo "fail";
} else {
    if($AttObj->updateRecords('attestation_automate', ' AA_Id = '.$_REQUEST['AA_Id'] ) == 'success' )
        echo "Successfully Updated";
    else
        echo "fail";   
}
?>