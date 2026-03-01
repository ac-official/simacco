<?php
require_once("connection.php");

class FeedbackClass
{
	var $ReportBugArray;
        var $ContactUsArray;
        function SendBugReport()
        {
            $sql = "INSERT INTO bug_reports ( " . implode(', ',array_keys($this->RB_Data)) . ") VALUES (" . "'" . implode("','", array_values($this->RB_Data)) . "'" . ")";
	    mysqli_query($GLOBALS['con'],$sql);
            if(mysqli_insert_id($GLOBALS['con'])) {
                return "Bug Report Send Successfully";
            } else {
                return "fail";
            }
		
        }
//-----------------------------------------Adding Comment------------------------------------------//
        function UpdateComment($bgid,$BR_Status)
        {
            $sql= "UPDATE bug_reports SET BR_Status='".$BR_Status."' WHERE BR_Id=".$bgid;
            mysqli_query($GLOBALS['con'],$sql);
            return "Comment Added Successfully";
        }
//----------------------------------------- All Designations ----------------------------------------//
	function viewBugReports($filter="",$pos,$cnt)
	{			
		$count=0; 
		$this->ReportBugArray = array();                
                $sql="SELECT BR.BR_Id, BR.BR_Desc,BR.BR_Type,BR.BR_Status, BR.BR_Subject,BR.BR_Comment, US.US_Id, US.US_FName, US.US_LName, US.US_Email, UP.US_Mobile1 , BR.BR_Date ,OF.OF_Name
                        FROM bug_reports as BR , users_auth as US , users_personal as UP ,offices as OF
                         WHERE US.US_Id = BR.US_Id AND US.US_Id = UP.US_Id AND BR.OF_Id = OF.OF_Id ".$filter. " LIMIT ".$pos.",".$cnt;
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->ReportBugArray[$count]=$row;
			$count++;
		}	
	}
        function viewBugReportsCount($filter="")
	{			
		$count=0; 
		$this->ReportBugArrayCount = array();                
                $sql= "SELECT  COUNT(BR.BR_Id)
                        FROM bug_reports as BR , users_auth as US , users_personal as UP ,offices as OF
                         WHERE US.US_Id = BR.US_Id AND US.US_Id = UP.US_Id AND BR.OF_Id = OF.OF_Id ".$filter;
		$result=mysqli_query($GLOBALS['con'],$sql);
		$row = mysqli_fetch_array($result,MYSQLI_NUM);
                return $row[0];
                //$this->ReportBugArrayCount = $row[0];	
	}
        function viewEmp($ofId,$Flter){
            $count=0; 
		$this->ReportBugEmp = array();                
                $sql="SELECT CONCAT(US.US_FName,' ', US.US_LName) AS Name,US.US_Id FROM users_auth AS US
                      LEFT JOIN acl AS AC ON US.UT_Id=AC.ACL_Id WHERE US.OF_Id=".$ofId ." AND US.US_Status!=0 AND AC.ACL_FeedbackRpt=1".$Flter;
		$result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->ReportBugEmp[$count]=$row;
			$count++;
		}
            
        }
        function insertReturnId($table) {  // insert to table and return last insert id
            $sql = "INSERT INTO $table ( " . implode(', ', array_keys($this->InsertData)) . ") VALUES (" . "'" . implode("','", array_values($this->InsertData)) . "'" . ")";
            mysqli_query($GLOBALS['con'],$sql);
//            return mysqli_insert_id($GLOBALS['con']);
        }
        function getDetails($fields,$table,$condition){
            $count = 0;
            $sql="SELECT ".$fields ." FROM ". $table.$condition;
            $result=mysqli_query($GLOBALS['con'],$sql);
		while($row=mysqli_fetch_object($result)) {
			$this->getDetailsArray[$count]=$row;
			$count++;
		}
        }
        function getBugreportsData($BRId){
            $count = 0;
            $sql="SELECT BR_Desc,BR_Date,CONCAT(UA.US_FName,' ', UA.US_LName) AS CreatedUserName FROM bug_reports AS BR"
                    . " LEFT JOIN users_auth AS UA ON UA.US_Id=BR.US_Id WHERE BR.BR_Id=".$BRId;
            $result=mysqli_query($GLOBALS['con'],$sql);
		$this->getBugreportsDataArray=mysqli_fetch_assoc($result);
        }
}
?>