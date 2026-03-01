<?php 

require_once($BASEPATH . "preTallyClass/LeaveClass.php");
 $lvObj = new LeaveClass();
 $fromDate="";
 $toDate="";
 if(($REQUEST['appFromDate'])&&($REQUEST['appToDate'])){
         $fromDate=$REQUEST['appFromDate'];
         $toDate=$REQUEST['appToDate'];         
         $fromDate = date("Y-m-d", strtotime($fromDate));
         $toDate = date("Y-m-d", strtotime($toDate));                   
    }    
 else if($REQUEST['appFromDate']){
     $fromDate=$REQUEST['appFromDate'];
     $fromDate = date("Y-m-d", strtotime($fromDate));
 }
 else if($REQUEST['appToDate']){
     $toDate=$REQUEST['appToDate'];
     $toDate = date("Y-m-d", strtotime($toDate));
 }
 
$userId=$preTally_user_id ;
$lvObj->listmyLeave($userId,$fromDate,$toDate);
$lv_Obj = $lvObj->listmyLeaveArray;
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
        header("Content-type: text/xml");
}
        echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
            echo '<rows>		
		<head>
                        <column width="30" type="ro" align="center" >SlNo</column>	 			 	 	
                        <column width="90" type="ro" align="left" >From Date</column>
                        <column width="90" type="ro" align="left" >To Date</column>
                        <column width="60" type="ro" align="left" >Total Days</column>
                        <column width="60" type="ro" align="left" >Eligible Days</column>
                        <column width="100" type="ro" align="left" >Name</column>
                        <column width="90" type="ro" align="left" >Leave</column>
                        <column width="115" type="ro" align="left" >Status</column>
                        <column width="90" type="ro" align="left" >Applied On</column>
                        <column width="*" type="ro" align="left" >Reason</column>
                        <column width="*" type="ed" align="left" >Comment</column>
                        <column width="70" type="ro" align="center" sort="na">Cancel</column>
			<settings>
				<colwidth>px</colwidth>
			</settings>
			<beforeInit> 
                                <call command="setSkin">
					<param>dhx_skyblue</param>
				</call> 
				<call command="setImagePath">
					<param>assets/grid/codebase/imgs/</param>
				</call> 
				<call command="enableSmartRendering">
					<param>false</param>
				</call> 
                        </beforeInit> 	
                        <afterInit>       
                            <call command="attachHeader">
                                <param>#rspan,#rspan,#rspan,#rspan,#rspan,#select_filter,#select_filter,#rspan,#rspan,#rspan,#rspan,#rspan</param>
                            </call>
                        </afterInit>       
                        
		  </head>';
                   $j=1;
                   $Status=array('0'=>'Pending Approval','1'=>'First Approved','2'=>'HR Approved','3'=>'Rejected By Reporting Person','4'=>'Rejected By HR','5'=>'Cancelled','6'=>'Leave not taken');
                   
                    if($lv_Obj) {
				foreach($lv_Obj as $rw) {
                                    $originalfromDate = $rw->LR_FromDate;
                                    $newfromDate = date("d.m.Y", strtotime($originalfromDate));
                                    $originaltoDate = $rw->LR_ToDate;
                                    $newtoDate = date("d.m.Y", strtotime($originaltoDate));
                                    
                                    
					echo '<row id="'.$rw->LR_Id.'">
                                                <cell name="from">'.$j.'</cell>
                                                <cell name="from">'.$newfromDate.'</cell>
                                                <cell name="to">'.$newtoDate.'</cell>
                                                <cell name="totalDays">'.$rw->LR_NumOFDays .'</cell>
                                                <cell name="eligibleDays">'.$rw->LR_EligibleNumOFDays .'</cell>   
                                                <cell name="firstName">'.$rw->US_FName.'</cell>
                                                <cell name="leaveType">'.$rw->LT_Name.'</cell>
                                                <cell name="status">'.$Status[$rw->LR_Status].'</cell>
                                                <cell name="appliedon">'.$rw->LR_CDate.'</cell>
                                                <cell name="reason">'.$rw->LR_Reason.'</cell>
                                                <cell name="comment">'.$rw->LR_Comments.'</cell>';
                                                if($rw->LR_Status==0){
                                                echo'<cell type="ro" title="Click here to cancel"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.UserProfile.leaveList('.$rw->LR_Id.',3);" />]]></cell>';
                                                }
                                                else{
                                                    echo'<cell type="ro" title=" "></cell>';
                                                }
                                               echo'</row>';
                                               $j++;
                                               
				}
			}
                        else {
                            echo '<row id="0"> 
                            <cell colspan="10"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                       }
                       
                echo '</rows>';
?>