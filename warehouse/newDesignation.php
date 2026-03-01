<?php
require_once($BASEPATH . "preTallyClass/DesignationClass.php");

$DesignationObj = new DesignationClass();
$DesignationObj->DG_Data = array(
    'US_Id'             => $preTally_user_id,
    'DG_Name' 		=> trim(htmlspecialchars($_REQUEST['DG_Name'], ENT_QUOTES)),
    'DG_Comments'	=> htmlspecialchars($_REQUEST['DG_Comments'], ENT_QUOTES),
    'DG_Status' 	=> htmlspecialchars($_REQUEST['DG_Status'], ENT_QUOTES),
    'DG_MDate' 		=> date('Y-m-d H:i:s')
);
$office_id=htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES);
if(($office_id=="")||($office_id==0))
{
    $office_id=$preTally_user_ofid;
}

if( $DesignationObj->verifyDesignation(htmlspecialchars($_REQUEST['DG_Id'], ENT_QUOTES), $office_id) ) {
    if(htmlspecialchars($_REQUEST['DG_Id'], ENT_QUOTES) == 0) {
        $DesignationObj->DG_Data["DG_CDate"] = date('Y-m-d H:i:s');
        $DesignationObj->DG_Data["OF_Id"] = $office_id;
        $DesignationObj->newDesignation();
        echo 'Designation Created Successfully';
    } else {
        echo $DesignationObj->updateDesignation(htmlspecialchars($_REQUEST['DG_Id'], ENT_QUOTES));
    }
} else { echo 'fail'; }
?>