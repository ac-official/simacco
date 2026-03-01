<?php 
require_once($BASEPATH . "preTallyClass/LateEntryClass.php");
$lateEntryObj = new LateEntryClass();

$Att_Lt_Id = htmlspecialchars($_REQUEST['Att_Lt_Id'], ENT_QUOTES);
$Att_Lt_Status = htmlspecialchars($_REQUEST['Att_Lt_Status'], ENT_QUOTES);
$Att_Lt_Verified_Remark = $_REQUEST['Att_Lt_Verified_Remark'];
$verifiedBy = $preTally_user_id;
$verifiedOn = date("Y-m-d H:i:s");
if($_REQUEST['Att_Lt_Verified_Remark']){
    $updateQuery = "
        UPDATE attendance_late_entry 
        SET 
            Att_Lt_Verified_Remark = '" . mysqli_real_escape_string($GLOBALS['con'], $Att_Lt_Verified_Remark) . "',
            Att_Lt_Verified_By = '$verifiedBy',
            Att_Lt_Verified_At = '$verifiedOn'
        WHERE Att_Lt_Id = '$Att_Lt_Id'
    ";
    $message = "Remark";
}else{
    $updateQuery = "
        UPDATE attendance_late_entry 
        SET 
            Att_Lt_Status = '$Att_Lt_Status',
            Att_Lt_Verified_By = '$verifiedBy',
            Att_Lt_Verified_At = '$verifiedOn'
        WHERE Att_Lt_Id = '$Att_Lt_Id'
    ";
    $message = "Status";
}

$result = mysqli_query($GLOBALS['con'], $updateQuery);
if (!$result) {
    "MySQL Update Error: " . mysqli_error($GLOBALS['con']);
    return false;
}
if ($result) {
    $jsonResponse = ['status' => true, 'message' => 'Late Entry '.$message.' Updated Successfully.'];
} else {
    $jsonResponse = ['status' => false, 'message' => 'Error updating record.'];
}

echo json_encode($jsonResponse);
exit();
?>
