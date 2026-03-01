<?php
include_once($BASEPATH . "preTallyClass/TrackClass.php");
$TrackObj = new TrackClass();
$TrackObj->ATPI_Data = array(
    'US_Id'             => $preTally_user_id,
    'ATPI_Instruction'  => trim(htmlspecialchars($_REQUEST['ATPInstruction'] , ENT_QUOTES)),
    'ATPI_MDate'        => date('Y-m-d H:i:s')
);
echo $TrackObj->updateInstruction();
?>

