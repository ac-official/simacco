<?php
// header('Content-Type: application/json');
include_once($BASEPATH."preTallyClass/RuleClass.php");
$lvObj = new RuleClass();
$RL_Id=$_REQUEST['RL_Id'];
$RL_Is_Sandwich = $_REQUEST['RL_Is_Sandwich'];
if($RL_Is_Sandwich==0){
	$RL_Start_Day = $_REQUEST['RL_Start_Day'];
	$RL_End_Day = $_REQUEST['RL_End_Day'];
	$RL_Sandwich_Type = 'none';
	$RL_Leave_Duration = $_REQUEST['RL_Leave_Duration'];
	
}else{
	$RL_Start_Day = 'none';
	$RL_End_Day = 'none';
	$RL_Leave_Duration = 'single';
	$RL_Sandwich_Type = $_REQUEST['RL_Sandwich_Type'];
	$RL_Leave_Duration = 'all';
}
$RL_Leave_Duration = ($RL_Leave_Duration=="Any day")?'single':$RL_Leave_Duration;
$lvObj->RuleData=array(
    'RL_Name' =>  htmlspecialchars($_REQUEST['RL_Name'], ENT_QUOTES),
    'RL_Description'=>  $_REQUEST['RL_Description']??'',
    'RL_Is_Sandwich' => $RL_Is_Sandwich,
    'RL_Sandwich_Type' => $RL_Sandwich_Type,
    'RL_Start_Day' => $RL_Start_Day,
    'RL_End_Day' => $RL_End_Day,
    'RL_Leave_Duration' => $RL_Leave_Duration,
    'RL_Is_LOP' => $_REQUEST['RL_Is_LOP'],
    'RL_Status' =>  $_REQUEST['RL_Status'],
    'RL_Created_By' => $preTally_user_id
);
$insertFlag = true;

$checkValidate = $lvObj->checkValidate($_REQUEST['RL_Name'], $_REQUEST['RL_Description']);
if ($checkValidate===true) {
	if( $lvObj->verifyRule(htmlspecialchars($_REQUEST['RL_Id'], ENT_QUOTES)) ) {
		$returnResponse = true;
		if(htmlspecialchars($RL_Id, ENT_QUOTES) != 0){
			$lvObj->RuleData['RL_Effective_Date'] = DateTime::createFromFormat('d/m/Y', $_REQUEST['RL_Effective_Date'])->format('Y-m-d');
			$singleRuleEffectiveDate = $lvObj->getRuleEffectiveDate($RL_Id);
			if($singleRuleEffectiveDate==$lvObj->RuleData['RL_Effective_Date']){
				$insertFlag = false;
				$lvObj->UpdateRuleData=array(
				    'RL_Name' =>  htmlspecialchars($_REQUEST['RL_Name'], ENT_QUOTES),
				    'RL_Description'=>  $_REQUEST['RL_Description']??'',
				    'RL_Is_Sandwich' => $RL_Is_Sandwich,
				    'RL_Sandwich_Type' => $RL_Sandwich_Type,
				    'RL_Start_Day' => $RL_Start_Day,
				    'RL_End_Day' => $RL_End_Day,
				    'RL_Leave_Duration' => $RL_Leave_Duration,
				    'RL_Is_LOP' => $_REQUEST['RL_Is_LOP'],
				    'RL_Status' =>  $_REQUEST['RL_Status']
				);
			}else{
				$lvObj->UpdateRuleData=array(
					'RL_End_Date' => DateTime::createFromFormat('d/m/Y', $_REQUEST['RL_Effective_Date'])->format('Y-m-d')
				);
			}
			$response = $lvObj->updateRule(htmlspecialchars($RL_Id, ENT_QUOTES));
			if($response!=true){
				$returnResponse = false;
			}
		}
		if($returnResponse == true){			
			if($insertFlag == true){
				$lvObj->RuleData['RL_Parent_ID'] = ($RL_Id)?$RL_Id:0;
				$lvObj->RuleData['RL_Except_Dept_office'] = $_REQUEST['RL_Except_Dept_office'];
				unset($lvObj->RuleData['RL_End_Date']);
				$lvObj->RuleData['RL_Effective_Date'] = DateTime::createFromFormat('d/m/Y', $_REQUEST['RL_Effective_Date'])->format('Y-m-d');
				$response = $lvObj->addRule();
				if($response==true){
					$jsonResponse = [
				    	'status' => true,
				    	'rule_type' => $RL_Is_Sandwich,
				    	'message' => 'Rule added successfully'
					];
				}else{
					$jsonResponse = [
				    	'status' => false,
				    	'message' => 'Error while adding the rule'
					];
				}
			}else{
				$jsonResponse = [
			    	'status' => true,
			    	'rule_type' => $RL_Is_Sandwich,
			    	'message' => 'Rule updated successfully'
				];
			}
		}
	} else {
		$jsonResponse = [
	    	'status' => false,
	    	'message' => 'Rule already exist'
		];
	}
}else{
	$jsonResponse = [
    	'status' => false,
    	'message' => $checkValidate['nameValid'].$checkValidate['descValid']
	];
}
echo json_encode($jsonResponse);
exit();
?>