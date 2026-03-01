<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/LeaveClass.php");
$lvObj = new LeaveClass();
 $orderBy ='ORDER BY lr.LR_CDate DESC';    
if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filterData = explode(",",$REQUEST['filter']);
$filter = ' ua.OF_Id = "'. $preTally_user_ofid .'" ';

if($ACL_Obj->ACL_ApproveLeave == 1 && $REQUEST['mode']!='All'){
    $filter .= " AND (lr.LR_Status = 1 OR (lr.LR_Status = 0 AND ua.US_Report = ".$preTally_user_id." ) OR lr.LR_Status = 2 OR lr.LR_Status = 4 OR lr.LR_Status = 6)";
}else if($ACL_Obj->ACL_ApproveLeave == 0 && $REQUEST['mode']!='All'){
    $filter .= " AND ua.US_Report = ".$preTally_user_id;
}

if($filterData[0]) {
    $filter .=" AND CONCAT(ua.US_FName,' ',ua.US_LName) LIKE '".$filterData[0]."%'"; 
}
if($filterData[1] != ''){
    $filter .=" AND CONCAT(u_au.US_FName,' ',u_au.US_LName) LIKE '".$filterData[1]."%'";
}
if($filterData[2] != ''){
    $filter .=' AND l.LC_Name like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter .=' AND lr.LT_Id  = "'.$filterData[3].'"';
}
if($filterData[4]){
    $filter .=" AND CONCAT(u_auth.US_FName,' ',u_auth.US_LName) LIKE '".$filterData[4]."%'";
}
if($filterData[5] != ''){
    $filter .=" AND CONCAT(u_hr_auth.US_FName,' ',u_hr_auth.US_LName) LIKE '".$filterData[5]."%'";
}
if($filterData[6] != ''){
    $filter .=' AND lr.LR_Status = "'.$filterData[6].'" ';
}
if($filterData[7] != ''){
    if($filterData[7]==2)
    $orderBy ="  ORDER BY lr.LR_FromDate ";
    else if($filterData[7]==3)
    $orderBy ="  ORDER BY lr.LR_ToDate ";    
    else if($filterData[7]==6)    
    $orderBy ="  ORDER BY CONCAT(ua.US_FName,' ',ua.US_LName) ";    
}
if($filterData[8] != ''){
    if($filterData[8]=="asc")
    $orderBy .=' ASC';
    else
    $orderBy .=' DESC';    
}

$lvObj->leaveApprovalList($REQUEST['appFromDate'],$REQUEST['appToDate'],$filter,$orderBy,$_GET["posStart"],$_GET["count"]);
$lv_Obj = $lvObj->listLeaveArray;
$Count  = $lvObj->LeaveCount;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
     echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
            <userdata name="TL_Count">'.$Count.'</userdata>';	
                    $j=1+$_GET["posStart"];
                    if($ACL_Obj->ACL_ApproveLeave != 1)
                        $Status=array('0'=>'Pending Approval','1'=>'First Approved','2'=>'HR Approved','3'=>'Rejected By Reporting Person','4'=>'Rejected By HR','5'=>'Cancelled','6'=>'Leave not taken');
                    else 
                        $Status=array('0'=>'Pending Approval','1'=>'First Approved','2'=>'HR Approved','4'=>'Rejected By HR','5'=>'Cancelled','6'=>'Leave not taken');
                    if($lv_Obj) {
				foreach($lv_Obj as $rw) {
                                    $originalfromDate = $rw->LR_FromDate;
                                    $newfromDate = date("d.m.Y", strtotime($originalfromDate));
                                    $originaltoDate = $rw->LR_ToDate;
                                    $newtoDate = date("d.m.Y", strtotime($originaltoDate));
					echo '<row id="'.$rw->LR_Id.'">
                                                <userdata name="ACL_ApproveLeave">'.$ACL_Obj->ACL_ApproveLeave.'</userdata>
                                                <userdata name="LR_Id">'.$rw->LR_Id.'</userdata>
                                                <userdata name="US_Id">'.$rw->US_Id.'</userdata>
                                                <userdata name="LR_Status">'.$rw->LR_Status.'</userdata>
                                                <cell name="#">'.$j.'</cell>
                                                <cell name="fromDate">'.$newfromDate.'</cell>
                                                <cell name="toDate">'.$newtoDate .'</cell>
                                                <cell name="TotalDays">'.$rw->LR_NumOFDays.'</cell>
                                                <cell name="EligibleDays">'.$rw->LR_EligibleNumOFDays.'</cell>    
                                                <cell name="name">'.$rw->US_FName." ".$rw->US_LName.'</cell>
                                                <cell name="ReportedByName">'.$rw->ReportUS_FName." ".$rw->ReportUS_LName.'</cell>
                                                <cell name="branch">'.$rw->LC_Name .'</cell>
                                                <cell name="leaveType">'.$rw->LT_Name .'</cell>
                                                <cell name="FirstApproved">'.$rw->FirstAprvUS_FName." ".$rw->FirstAprvUS_LName.'</cell>
                                                <cell name="HRApproved">'.$rw->HRAprvUS_FName." ".$rw->HRAprvUS_LName.'</cell>
                                                <cell name="reason">'.date("d.m.Y", strtotime($rw->LR_CDate )).'</cell>    
                                                <cell name="reason">'.($rw->LR_FirstDt?date("d.m.Y", strtotime($rw->LR_FirstDt )):"-NA-").'</cell>        
                                                <cell name="reason">'.$rw->LR_Reason .'</cell>
                                                <cell name="comment"> '.$rw->LR_Comments .'</cell>
                                                <cell name="status">'.$Status[$rw->LR_Status].'</cell>
                                                </row>';
                                                $j++;
				}
			}
                        else{
                            echo '<row id="0"> 
                            <cell colspan="14"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                         }
		  
        echo '</rows>';
?>