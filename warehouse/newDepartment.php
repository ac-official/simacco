<?php
include_once($BASEPATH . "preTallyClass/DepartmentClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$DepartmentObj = new DepartmentClass();
$WeekObj = new AttendanceClass();
$DepartmentObj->DP_Data = array(
        'US_Id' 	=> $preTally_user_id,        
	'DP_Name' 	=> trim(htmlspecialchars($_REQUEST['DP_Name'], ENT_QUOTES)),
	'DP_Comments'	=> htmlspecialchars($_REQUEST['DP_Comments'], ENT_QUOTES),
	'DP_Status' 	=> htmlspecialchars($_REQUEST['DP_Status'], ENT_QUOTES),
	'DP_MDate' 	=> date('Y-m-d H:i:s')
);

$office_id=htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES);
if(($office_id=="")||($office_id==0))
{
    $office_id=$preTally_user_ofid;
}
if( $DepartmentObj->verifyDepartment(htmlspecialchars($_REQUEST['DP_Id'], ENT_QUOTES),$office_id ) ) {
	if(htmlspecialchars($_REQUEST['DP_Id'], ENT_QUOTES) == 0) {
                $DepartmentObj->DP_Data["OF_Id"] = $office_id; 
		$DepartmentObj->DP_Data["DP_CDate"] = date('Y-m-d H:i:s'); 
		$dpid=$DepartmentObj->newDepartment();	
                $WeekObj->Weekend_Data = array(        
                'DP_Id' 	=> $dpid,        
                'DH_Weekends'	=> $_REQUEST['H_Weekend'],
                'DH_Status' 	=> 1
                );                
                $WeekObj->newWeekend();                
                echo 'Department Created Successfully';
	} else {
		$DepartmentObj->updateDepartment(htmlspecialchars($_REQUEST['DP_Id'], ENT_QUOTES));
                $WeekObj->Weekend_Data = array(
                'DH_Weekends'	=> $_REQUEST['H_Weekend']                
                );
                $WeekObj->updateWeekend($_REQUEST['DP_Id']);
                echo 'Department Updated Successfully';
	}        
        } else {
	echo "fail";
	}

?>