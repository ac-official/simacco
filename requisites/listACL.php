<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/ACLClass.php");
include_once($BASEPATH . "preTallyClass/OfficeClass.php");
$ACLObj = new ACLClass();
$OfficeObj = new OfficeClass();
$ofadmin_id=$OfficeObj->offzAdmin($preTally_user_ofid);
if($preTally_user_ofid==1)
    { 
    $cmpnyfilter=" WHERE ACL.OF_Id=OF1.OF_Id  ORDER BY OF1.OF_Name";      
    }
    elseif ($ofadmin_id==$preTally_user_id) 
        {
           $cmpnyfilter=" WHERE OF1.OF_Id=".$preTally_user_ofid." AND ACL.ACL_Id!=".$preTally_user_type." AND ACL.OF_Id=OF1.OF_Id ORDER BY ACL.ACL_Name";  
        }
    elseif (2995==$preTally_user_id)  //2025-04-01 allow MidhyaN for all acls in the office
        {
           $cmpnyfilter=" WHERE OF1.OF_Id=".$preTally_user_ofid." AND ACL.ACL_Id!=".$preTally_user_type." AND ACL.OF_Id=OF1.OF_Id ORDER BY ACL.ACL_Name";  
        }
else
    {
      $cmpnyfilter=" WHERE OF1.OF_Id=".$preTally_user_ofid." AND ACL.US_Id=".$preTally_user_id." AND ACL.OF_Id=OF1.OF_Id ORDER BY ACL.ACL_Name"; 
    }    
$ACLObj->viewACL($cmpnyfilter);
$ACL_Row = $ACLObj->ACLArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">designations</userdata>
    <userdata name="db_primary">DG_Id</userdata>
    <userdata name="db_date">DG_MDate</userdata>
    <userdata name="db_status">DG_Status</userdata>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> ACL </column>';
if($preTally_user_ofid==1)
    {
        echo'<column width="*" type="ro" align="center" sort="na">Company</column>';
    }
        echo'<column width="0" type="ro" align="center" sort="na">ACL_Stats</column>
            <column width="65" type="ro" align="center" sort="na">Status </column>
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
        </afterInit>

    </head>';
    if($ACL_Row) {
        $j = 1;
        foreach($ACL_Row as $rw) {
            echo '<row id="'.$rw->ACL_Id.'">
                    <userdata name="ACL_Name">'.$rw->ACL_Name.'</userdata>
                    <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>
                    <userdata name="ACL_HR">'.$rw->ACL_HR.'</userdata>    
                    <userdata name="ACL_HR_VM">'.$rw->ACL_HR_VM.'</userdata>
                    <userdata name="ACL_BSheet">'.$rw->ACL_BSheet.'</userdata>    
                    <userdata name="ACL_BSheet_VM">'.$rw->ACL_BSheet_VM.'</userdata>
                    <userdata name="ACL_CashReports">'.$rw->ACL_CashReports.'</userdata>  
                    <userdata name="ACL_MasterReports">'.$rw->ACL_MasterReports.'</userdata>  
                    <userdata name="ACL_BankReports">'.$rw->ACL_BankReports.'</userdata> 
                    <userdata name="ACL_MISReports">'.$rw->ACL_MISReports.'</userdata> 
                    <userdata name="ACL_User">'.$rw->ACL_User.'</userdata>
                    <userdata name="ACL_ListUser">'.$rw->ACL_ListUser.'</userdata>
                    <userdata name="ACL_SalDetail">'.$rw->ACL_SalDetail.'</userdata>    
                    <userdata name="ACL_BnkDetail">'.$rw->ACL_BnkDetail.'</userdata>        
                    <userdata name="ACL_Payroll">'.$rw->ACL_Payroll.'</userdata>    
                    <userdata name="ACL_Attendance">'.$rw->ACL_Attendance.'</userdata>  
                    <userdata name="ACL_MnthlyAttendance">'.$rw->ACL_MnthlyAttendance.'</userdata>
                    <userdata name="ACL_MnthlyAttendanceSummary">'.$rw->ACL_MnthlyAttendanceSummary.'</userdata>
                    <userdata name="ACL_ManageLateEntry">'.$rw->ACL_ManageLateEntry.'</userdata>    
                    <userdata name="ACL_AttendanceEdt">'.$rw->ACL_AttendanceEdt.'</userdata>      
                    <userdata name="ACL_ApproveLeave">'.$rw->ACL_ApproveLeave.'</userdata>     
                    <userdata name="ACL_State">'.$rw->ACL_State.'</userdata>
                    <userdata name="ACL_City">'.$rw->ACL_City.'</userdata>
                    <userdata name="ACL_Company">'.$rw->ACL_Company.'</userdata>  	
                    <userdata name="ACL_Branch">'.$rw->ACL_Branch.'</userdata>  	
                    <userdata name="ACL_Dept">'.$rw->ACL_Dept.'</userdata>  	
                    <userdata name="ACL_Desig">'.$rw->ACL_Desig.'</userdata>  	
                    <userdata name="ACL_MH">'.$rw->ACL_MH.'</userdata>  	
                    <userdata name="ACL_SH">'.$rw->ACL_SH.'</userdata>  	
                    <userdata name="ACL_Item">'.$rw->ACL_Item.'</userdata>  
                    <userdata name="ACL_AllItem">'.$rw->ACL_AllItem.'</userdata>      
                    <userdata name="ACL_NotifyQueue">'.$rw->ACL_NotifyQueue.'</userdata>      
                    <userdata name="ACL_Desc">'.$rw->ACL_Description.'</userdata>      
                    <userdata name="ACL_Unit">'.$rw->ACL_Unit.'</userdata>  	
                    <userdata name="ACL_Paymode">'.$rw->ACL_Paymode.'</userdata>   	
                    <userdata name="ACL_PayrollEdt">'.$rw->ACL_PayrollEdt.'</userdata> 
                    <userdata name="ACL_MealAllowance">'.$rw->ACL_MealAllowance.'</userdata>    
                    <userdata name="ACL_SalStruct">'.$rw->ACL_SalStruct.'</userdata>  	
                    <userdata name="ACL_SalPayMode">'.$rw->ACL_SalPayMode.'</userdata> 
                    <userdata name="ACL_Access">'.$rw->ACL_Access.'</userdata>
                    <userdata name="ACL_SidebarMH">'.$rw->ACL_SidebarMH.'</userdata>
                    <userdata name="ACL_SidebarOFF">'.$rw->ACL_SidebarOFF.'</userdata> 
                    <userdata name="ACL_ManageTracks">'.$rw->ACL_ManageTracks.'</userdata>
                    <userdata name="ACL_DeleteEntries">'.$rw->ACL_DeleteEntries.'</userdata>    
                    <userdata name="ACL_ManageBSDate">'.$rw->ACL_ManageBSDate.'</userdata>       
                    <userdata name="ACL_Track">'.$rw->ACL_Track.'</userdata>    
                    <userdata name="ACL_TrackInvReceipt">'.$rw->ACL_TrackInvReceipt.'</userdata> 
                    <userdata name="ACL_NotfLC">'.$rw->ACL_NotfLC.'</userdata> 
                    <userdata name="ACL_NotfBNK">'.$rw->ACL_NotfBNK.'</userdata>  
                    <userdata name="ACL_Att_Master">'.$rw->ACL_Att_Master.'</userdata> 
                    <userdata name="ACL_FeedbackRpt">'.$rw->ACL_FeedbackRpt.'</userdata> 
                    <userdata name="ACL_SalPMwiseBranch">'.$rw->ACL_SalPMwiseBranch.'</userdata> 
                    <userdata name="ACL_SalPMwiseAll">'.$rw->ACL_SalPMwiseAll.'</userdata> 
                    <userdata name="ACL_ManageBusinessAmt">'.$rw->ACL_ManageBusinessAmt.'</userdata> 
                    <userdata name="ACL_SalAdvance">'.$rw->ACL_SalAdvance.'</userdata>     
                    <userdata name="ACL_ZonalManage">'.$rw->ACL_ZonalManage.'</userdata>     
                    <userdata name="ACL_ZonalHead">'.$rw->ACL_ZonalHead.'</userdata>         
                    <userdata name="ACL_Status">'.$rw->ACL_Status.'</userdata> 
                    <userdata name="ACL_Purchase_Team">'.$rw->ACL_Purchase_Team.'</userdata> 
                    <userdata name="ACL_Purchase_Approval">'.$rw->ACL_Purchase_Approval.'</userdata> 
                    <userdata name="ACL_EditAccEntries">'.$rw->ACL_EditAccEntries.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell name="ACL_Name">'.$rw->ACL_Name.'</cell>';
                    if($preTally_user_ofid==1)
                    {
                        echo'<cell name="OF_Name">'.$rw->OF_Name.'</cell>';
                    }
                    if($rw->ACL_Status==1) {
                        $aclstats="Approved";
                    } else { 
                        $aclstats="Blocked";
                    }
                     echo'<cell>'.$aclstats.'</cell>';
                   if($rw->ACL_Status == '0') { 
                        echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$aclstats.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                    } else {
                        echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$aclstats.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                    }
            echo '</row>';
            $j++;
        }
    }
		  
echo '</rows>';
?>
