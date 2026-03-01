<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

$HRType = array('Self', 'Self Offz Department', 'Self Office', 'Self Department', 'Self Company', 'All Companies');
$ACCEntry = array('Self', 'Self Offz Department', 'Self Office', 'Self Department', 'Self Company', 'All Companies');

$VM_Type = array('View', 'Modify');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$role = 'adm';
$ajax = 'true';



echo '<items>
        <item type="settings" position="label-left" labelWidth="0" inputWidth="250" offsetLeft="10" />
        
        <item type="fieldset" name="type3" labelWidth="0" position="label-right"  label="ACL Name" inputWidth="590" className="manageACLFieldSet" offsetTop="10" >
            <item type="hidden" name="ACL_Id" value="0"></item>
            <item type="hidden" name="OF_Id" value="'.$preTally_user_ofid.'"></item>
            <item type="input" name="ACL_Name" label="Name" labelWidth="50" inputWidth="150" required="true" validate="^[a-zA-Z ]+$">
            <note width="100">ACL Name</note>
        </item>
            
        <item type="newcolumn" ></item>
        
        <item type="combo" name="ACL_Status" labelWidth="0" inputWidth="150">
            <option value="1" label="Published" selected="true" />
            <option value="0" label="Blocked" selected="false" />
            <note width="150">Publish Status</note>
        </item>
        <item type="newcolumn" ></item>
            <item type="button" value="Save" name="ACLButtonSave" offsetTop="0" />
            <item type="newcolumn"/>
            <item type="button" value="Cancel" name="ACLButtonCancel" offsetTop="0" />
        </item>       
        <item type="fieldset" name="type" labelWidth="auto" position="label-right"  label="Human Resource" inputWidth="590" className="manageACLFieldSet" >
            <item type="combo" name="ACL_HR" labelWidth="50" inputWidth="150" position="label-left" label="Level" readonly="true">';
                for($j=0; $j<=$ACL_Obj->ACL_HR; $j++){
                    echo '<option value="'.$j.'" label="'.$HRType[$j].'"/>';
                }
            echo'</item>
            <item type="newcolumn" offset="0"></item>
            <item type="combo" name="ACL_HR_VM" labelWidth="50" inputWidth="150" position="label-left" label="Type" readonly="true">';
                for($j=0; $j<=$ACL_Obj->ACL_HR_VM; $j++){
                    echo '<option value="'.$j.'" label="'.$VM_Type[$j].'"/>';
                }
            echo '</item>
        </item>
           
        <item type="fieldset" name="BSCheck" labelWidth="auto" position="label-right"  label="Balance Sheet" inputWidth="590" className="manageACLFieldSet" >
            <item type="combo" name="ACL_BSheet" labelWidth="50" inputWidth="150" position="label-left" label="Level" readonly="true">';
                for($j=0; $j<=$ACL_Obj->ACL_BSheet; $j++){
                    echo '<option value="'.$j.'" label="'.$ACCEntry[$j].'"/>';
                }
            echo'</item>
            <item type="newcolumn" offset="0"></item>
            <item type="combo" name="ACL_BSheet_VM" labelWidth="50" inputWidth="150" position="label-left" label="Type" readonly="true">';
                for($j=0; $j<=$ACL_Obj->ACL_BSheet_VM; $j++){
                    echo '<option value="'.$j.'" label="'.$VM_Type[$j].'"/>';
                }
            echo '</item>
        </item>  
        
        <item type="fieldset" name="gen1" labelWidth="400" inputWidth="590" position="label-right"  label="General Settings" className="manageACLFieldSet" >';
            
            if($ACL_Obj->ACL_State) {
                echo '<item type="checkbox" name="ACL_State" labelWidth="auto" position="label-right"  label="State"></item>
                <item type="newcolumn" offset="0"></item>';
            }

            if($ACL_Obj->ACL_City) {
                echo '<item type="checkbox" name="ACL_City" labelWidth="auto" position="label-right"  label="City"></item>
                <item type="newcolumn" offset="0"></item>';
            }

            if($ACL_Obj->ACL_Access) {
                echo '<item type="checkbox" name="ACL_Access" labelWidth="auto" position="label-right"  label="ACL"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_Unit) {
                echo '<item type="checkbox" name="ACL_Unit" labelWidth="auto" position="label-right"  label="Units" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_Paymode) {
                echo '<item type="checkbox" name="ACL_Paymode" labelWidth="auto" position="label-right"  label="Payment Modes" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_FeedbackRpt) {
                echo '<item type="checkbox" name="ACL_FeedbackRpt" labelWidth="auto" position="label-right"  label="Feedback Reports" ></item>';
            }
            if($ACL_Obj->ACL_Purchase_Team) {
                echo '<item type="newcolumn" offset="0"></item>';
                echo '<item type="checkbox" name="ACL_Purchase_Team" labelWidth="auto" position="label-right"  label="Purchase" ></item>';
            }
            if($ACL_Obj->ACL_Purchase_Approval) {
                echo '<item type="newcolumn" offset="0"></item>';
                echo '<item type="checkbox" name="ACL_Purchase_Approval" labelWidth="auto" position="label-right"  label="Purchase Approval" ></item>';
            }

        echo '</item>
            
        <item type="fieldset" name="hr2" labelWidth="400" inputWidth="590" position="label-right"  label="HR Settings" className="manageACLFieldSet" >';
            
            
            
            if($ACL_Obj->ACL_ListUser) {
                echo '<item type="checkbox" name="ACL_ListUser" labelWidth="auto" position="label-right"  label="List User"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_User) {
                echo '<item type="checkbox" name="ACL_User" labelWidth="auto" position="label-right"  label="Manage User"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SalDetail) {
                echo '<item type="checkbox" name="ACL_SalDetail" labelWidth="auto" position="label-right"  label="Salary Details" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SalStruct) {
                echo '<item type="checkbox" name="ACL_SalStruct" labelWidth="auto" position="label-right"  label="Salary Strucutre" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SalPayMode) {
                echo '<item type="checkbox" name="ACL_SalPayMode" labelWidth="auto" position="label-right"  label="Salary Paymodes" ></item>'
                . ' <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_ApproveLeave) {
                echo '<item type="checkbox" name="ACL_ApproveLeave" labelWidth="auto" position="label-right"  label="Final Leave Approval" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_Attendance) {
                echo '<item type="checkbox" name="ACL_Attendance" labelWidth="auto" position="label-right"  label="Daily Attendance View" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_MnthlyAttendance) {
                echo '<item type="checkbox" name="ACL_MnthlyAttendance" labelWidth="auto" position="label-right"  label="Monthly Attendance View" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_AttendanceEdt) {
                echo '<item type="checkbox" name="ACL_AttendanceEdt" labelWidth="auto" position="label-right"  label="Monthly Attendance Edit" ></item>
                <item type="newcolumn" offset="0"></item>';
            }  
            if($ACL_Obj->ACL_MnthlyAttendanceSummary) {
                echo '<item type="checkbox" name="ACL_MnthlyAttendanceSummary" labelWidth="auto" position="label-right"  label="Monthly Attendance Summary" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_ManageLateEntry) {
                echo '<item type="checkbox" name="ACL_ManageLateEntry" labelWidth="auto" position="label-right"  label="Manage Late Entry" ></item>
                <item type="newcolumn" offset="0"></item>';
            } 
            if($ACL_Obj->ACL_Payroll) {
                echo '<item type="checkbox" name="ACL_Payroll" labelWidth="auto" position="label-right"  label="Payroll View" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_PayrollEdt) {
                echo '<item type="checkbox" name="ACL_PayrollEdt" labelWidth="auto" position="label-right"  label="Payroll Edit" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_MealAllowance) {
                echo '<item type="checkbox" name="ACL_MealAllowance" labelWidth="auto" position="label-right"  label="Meal Allowance" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_SalPMwiseBranch) {
                echo '<item type="checkbox" name="ACL_SalPMwiseBranch" labelWidth="auto" position="label-right"  label="Sal Paymodewise Branch" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_SalPMwiseAll) {
                echo '<item type="checkbox" name="ACL_SalPMwiseAll" labelWidth="auto" position="label-right"  label="Sal Paymodewise All" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_SalAdvance) {
                echo '<item type="checkbox" name="ACL_SalAdvance" labelWidth="auto" position="label-right"  label="Salary Advance" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            
        echo '</item>
            
        <item type="fieldset" name="reports" labelWidth="400" inputWidth="590" position="label-right"  label="Reports Settings" className="manageACLFieldSet" >';
            
//            if($ACL_Obj->ACL_CashReports) {
//                echo '<item type="checkbox" name="ACL_CashReports" labelWidth="auto" position="label-right"  label="Cash Balance Sheet Reports"></item>
//                <item type="newcolumn" offset="0"></item>';
//            }
        
            if($ACL_Obj->ACL_MasterReports) {
                echo '<item type="checkbox" name="ACL_MasterReports" labelWidth="auto" position="label-right"  label="Master Reports"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            
            if($ACL_Obj->ACL_BankReports) {
                echo '<item type="checkbox" name="ACL_BankReports" labelWidth="auto" position="label-right"  label="Bank Book"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_MISReports) {
                echo '<item type="checkbox" name="ACL_MISReports" labelWidth="auto" position="label-right"  label="MIS Reports"></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_ManageBusinessAmt) {
                echo '<item type="checkbox" name="ACL_ManageBusinessAmt" labelWidth="auto" position="label-right"  label="Manage Business Amount"></item>
                <item type="newcolumn" offset="0"></item>';
            }

        echo '</item>

        <item type="fieldset" name="hr3" labelWidth="400" inputWidth="590" position="label-right"  label="Company/Branch Settings" className="manageACLFieldSet" >';
            
            if($ACL_Obj->ACL_Company) {
                echo '<item type="checkbox" name="ACL_Company" labelWidth="auto" position="label-right"  label="Companies" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SalStruct) {
                echo '<item type="checkbox" name="ACL_Branch" labelWidth="auto" position="label-right"  label="Branch" ></item>
                <item type="newcolumn" offset="0"></item>'; 
            }
            
            if($ACL_Obj->ACL_SalStruct) {
                echo '<item type="checkbox" name="ACL_Dept" labelWidth="auto" position="label-right"  label="Department" ></item>                                                 
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SalStruct) {
                echo '<item type="checkbox" name="ACL_Desig" labelWidth="auto" position="label-right"  label="Designation" ></item>   <item type="newcolumn" offset="0"></item><item type="newcolumn" offset="0"></item>';
                
            }            
            if($ACL_Obj->ACL_ZonalManage) {
                echo '<item type="checkbox" name="ACL_ZonalManage" labelWidth="auto" position="label-right"  label="Manage Zones" ></item>   <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_ZonalHead) {
                echo '<item type="checkbox" name="ACL_ZonalHead" labelWidth="auto" position="label-right"  label="Manage Zonal Head" ></item>';
            }
        echo '</item>

        <item type="fieldset" name="bs3" labelWidth="400" inputWidth="590" position="label-right"  label="Accounts Settings" className="manageACLFieldSet" >';
        
            if($ACL_Obj->ACL_MH) {
                echo '<item type="checkbox" name="ACL_MH" labelWidth="auto" position="label-right"  label="Mainhead" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SH) {
                echo '<item type="checkbox" name="ACL_SH" labelWidth="auto" position="label-right"  label="Subhead" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_Item) {
                echo '<item type="checkbox" name="ACL_Item" labelWidth="auto" position="label-right"  label="Items" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_AllItem) {
                echo '<item type="checkbox" name="ACL_AllItem" labelWidth="auto" position="label-right"  label="Manage Items &amp; Descriptions" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            if($ACL_Obj->ACL_Description) {
                echo '<item type="checkbox" name="ACL_Desc" labelWidth="auto" position="label-right"  label="Description" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            /*if($ACL_Obj->ACL_NotifyQueue) {
                echo '<item type="checkbox" name="ACL_NotifyQueue" labelWidth="auto" position="label-right"  label="Approval Queue" ></item>
                <item type="newcolumn" offset="0"></item>';
            }*/
            
            if($ACL_Obj->ACL_BnkDetail) {
                echo '<item type="checkbox" name="ACL_BnkDetail" labelWidth="auto" position="label-right"  label="Bank Details" ></item>
                <item type="newcolumn" offset="0"></item>';
            }  
            
            if($ACL_Obj->ACL_ManageTracks) {
                echo '<item type="checkbox" name="ACL_ManageTracks" labelWidth="auto" position="label-right"  label="Manage Tracks" ></item>
                <item type="newcolumn" offset="0"></item>';
            } 
            
            if($ACL_Obj->ACL_DeleteEntries) {
                echo '<item type="checkbox" name="ACL_DeleteEntries" labelWidth="auto" position="label-right"  label="Delete Entries" ></item>
                <item type="newcolumn" offset="0"></item>';
            } 
            
            if($ACL_Obj->ACL_ManageBSDate) {
                echo '<item type="checkbox" name="ACL_ManageBSDate" labelWidth="auto" position="label-right"  label="Manage Entry Edit Date" ></item>';
            } 
            if($ACL_Obj->ACL_EditAccEntries) {
                echo '<item type="checkbox" name="ACL_EditAccEntries" labelWidth="auto" position="label-right"  label="Edit Account Entries" ></item>';
            } 
            
        echo '</item>
                             
        <item type="fieldset" name="notf3" labelWidth="400" inputWidth="590" position="label-right"  label="Notifications"  className="manageACLFieldSet" >';
            
            if($ACL_Obj->ACL_NotfLC) {
                echo '<item type="checkbox" name="ACL_NotfLC" labelWidth="auto" position="label-right"  label="Branch Transaction Notifications" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_NotfBNK) {
                echo '<item type="checkbox" name="ACL_NotfBNK" labelWidth="auto" position="label-right"  label="Bank Transaction Notifications" ></item>';
            }
            
        echo '</item>
            
        <item type="fieldset" name="type6" labelWidth="400" inputWidth="590" position="label-right"  label="Sidebar Right"  className="manageACLFieldSet" >';
            
            if($ACL_Obj->ACL_SidebarMH) {
                echo '<item type="checkbox" name="ACL_SidebarMH" labelWidth="auto" position="label-right"  label="Main Heads Menu" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_SidebarOFF) {
                echo '<item type="checkbox" name="ACL_SidebarOFF" labelWidth="auto" position="label-right"  label="Offices Menu" ></item>';
            }
            
        echo '</item>
            
        <item type="fieldset" name="attestation" labelWidth="400" inputWidth="590" position="label-right"  label="Attestation"  className="manageACLFieldSet" >';
            
            if($ACL_Obj->ACL_Track) {
                echo '<item type="checkbox" name="ACL_Track" labelWidth="auto" position="label-right"  label="Track" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_Att_Master) {
                echo '<item type="checkbox" name="ACL_Att_Master" labelWidth="auto" position="label-right"  label="Master Data" ></item>
                <item type="newcolumn" offset="0"></item>';
            }
            
            if($ACL_Obj->ACL_TrackInvReceipt) {
                echo '<item type="checkbox" name="ACL_TrackInvReceipt" labelWidth="auto" position="label-right"  label="Invoice Receipt" ></item>';
            }
            
        echo '</item>
        
        <item type="template" offsetTop="20" />
    </items>';
?>