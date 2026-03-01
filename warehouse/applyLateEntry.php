<?php
include_once($BASEPATH . "preTallyClass/LateEntryClass.php");
$lateEntryObj       =   new LateEntryClass();
$fromDate   =   $_REQUEST['Att_Lt_From_Date'];
$toDate     =   $_REQUEST['Att_Lt_To_Date'];
$att_Lt_Id = htmlspecialchars($_REQUEST['Att_Lt_Id'], ENT_QUOTES);
$Att_Lt_Duration = $_REQUEST['late_hr'].":".$_REQUEST['late_min'];
if($lateEntryObj->checkLateEntryExistence($fromDate,$toDate, $preTally_user_id, $_REQUEST['Att_Lt_Id'])) {
    $jsonResponse = [
        'status' => false,
        'message' => 'Duplicate entry found, Already have an entry with the submitted details'
    ];
} else {
    $lateEntryObj->LateEntryData    =   array(
        'Att_Lt_Id'             =>  $att_Lt_Id,
        'Att_Lt_US_Id'          =>  $preTally_user_id,
        'Att_Lt_From_Date'      =>  date("Y-m-d", strtotime($fromDate)),
        'Att_Lt_To_Date'        =>  date("Y-m-d", strtotime($toDate)),
        'Att_Lt_NumOFDays'      =>  htmlspecialchars($_REQUEST['Att_Lt_NumOFDays']),
        'Att_Lt_Duration'       =>  $Att_Lt_Duration,
        'Att_Lt_Type'           =>  htmlspecialchars($_REQUEST['Att_Lt_Type']),
        'Att_Lt_Reason'         =>  $_REQUEST['Att_Lt_Reason'],
    );
    $response = $lateEntryObj->applyLateEntry();
    if($response==true){
        $actionText = 'submitted';
        if($_REQUEST['Att_Lt_Id']!=0){
            $actionText = 'updated';
        }
        $jsonResponse = [
            'status' => true,
            'message' => 'Late entry request has been '.$actionText.' successfully'
        ];
    }else{
        $actionText = 'submitting';
        if($_REQUEST['Att_Lt_Id']!=0){
            $actionText = 'updating';
        }
        $jsonResponse = [
            'status' => false,
            'message' => 'Some error occured while '.$actionText.' the record'
        ];
    }
}
echo json_encode($jsonResponse);
exit();
?>