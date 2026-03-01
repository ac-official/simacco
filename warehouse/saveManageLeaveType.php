<?php
include_once($BASEPATH."preTallyClass/LeaveClass.php");
$lvObj = new LeaveClass();
$LT_Id=$_REQUEST['LT_Id'];
$emp_StatusComboChecked=$REQUEST['emp_StatusComboChecked'];
$status=$_REQUEST['employee_Status'];
$emp_Status=(explode(",",$status));
$emp_StatusComboCheckedStatus=(explode(",",$emp_StatusComboChecked));
$ES_Status="";
for($i=0;$i<count($emp_Status);$i++){
    if (in_array($emp_Status[$i], $emp_StatusComboCheckedStatus)){
        $ES_Status.=$emp_Status[$i].":"."1".",";
    }else{
        $ES_Status.=$emp_Status[$i].":"."0".",";
    }
}
$ES_Status=rtrim($ES_Status,",");
$lvObj->LeaveTypeData=array('OF_Id'         =>  $preTally_user_ofid,
                            'LT_Name'       =>  htmlspecialchars($_REQUEST['LT_Name'], ENT_QUOTES),
                            'LT_MaxCount'   =>  $_REQUEST['LT_MaxCount'],
                            'LT_LOP'        =>  $_REQUEST['LT_LOP'],
                            'ES_Status'     =>  $ES_Status,
                            'LT_Status'     =>  $_REQUEST['LT_Status']);

 if( $lvObj->verifyLeaveType(htmlspecialchars($_REQUEST['LT_Id'], ENT_QUOTES)) ) {
	if(htmlspecialchars($LT_Id, ENT_QUOTES) == 0){
		echo $lvObj->addLeaveType();
                
	} else {
		echo $lvObj->updateLeaveType(htmlspecialchars($LT_Id, ENT_QUOTES));
	}
} else { echo 'Leave Type Already Exist';}
?>