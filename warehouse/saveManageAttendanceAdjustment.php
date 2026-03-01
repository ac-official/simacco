<?php
include_once($BASEPATH."preTallyClass/AttendanceAdjustmentClass.php");
include_once($BASEPATH."preTallyClass/AttendanceClass.php");
$lvObj = new AttendanceAdjustmentClass();
$attendanceObj = new AttendanceClass();
$userId = htmlspecialchars($_REQUEST['Att_Adj_US_Id'], ENT_QUOTES);
$att_Adj_Id = htmlspecialchars($_REQUEST['Att_Adj_Id'], ENT_QUOTES);
$permitInsertion = false;
if(!$lvObj->checkAttendanceExistance($userId, $_REQUEST)){
	$permitInsertion = true;
}
if($permitInsertion == true){
	$response = $lvObj->updateAttendanceAdjustment($_REQUEST['Att_Adj_US_Id'], $_REQUEST['Att_Adj_Date'], $att_Adj_Id);
	if($response==true){
		$permitInsertion = true;
		$lvObj->AdjustmentData=array(
			'Att_Adj_Id' => $att_Adj_Id,
		    'Att_Adj_US_Id' =>  $userId,
		    'Att_Adj_Date'=>  DateTime::createFromFormat('d/m/Y', $_REQUEST['Att_Adj_Date'])->format('Y-m-d'),
		    'Att_Adj_Type' => $_REQUEST['Att_Adj_Type'],
		    'Att_Adj_Remarks' => htmlentities($_REQUEST['Att_Adj_Remarks']),
		    'Att_Adj_Created_By' => $preTally_user_id
		);
		$response = $lvObj->addAdjustment();
		if($response==true){
			$actionText = 'added';
			if($_REQUEST['Att_Adj_Id']!=0){
				$actionText = 'updated';
			}
			$jsonResponse = [
		    	'status' => true,
		    	'message' => 'Attendance Adjustment '.$actionText.' successfully'
			];
		}else{
			$actionText = 'adding';
			if($_REQUEST['Att_Adj_Id']!=0){
				$actionText = 'updating';
			}
			$jsonResponse = [
		    	'status' => false,
		    	'message' => 'Error while '.$actionText.' the attendance adjustment'
			];
		}
	}else{
		$jsonResponse = [
	    	'status' => false,
	    	'message' => 'Some error occured while saving the record'
		];
	}
}else{
	$jsonResponse = [
    	'status' => false,
    	'message' => 'Same entry already exist'
	];
}
echo json_encode($jsonResponse);
exit();
?>


