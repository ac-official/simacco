<?php

include_once($BASEPATH . "preTallyClass/ACLClass.php");
$ACLObj = new ACLClass();
$ACLObj->ACL_Data = array(
    'ACL_Name' => trim(htmlspecialchars($_REQUEST['ACL_Name'], ENT_QUOTES)),
    'ACL_HR' => htmlspecialchars($_REQUEST['ACL_HR'], ENT_QUOTES),
    'ACL_HR_VM' => htmlspecialchars($_REQUEST['ACL_HR_VM'], ENT_QUOTES),
    'ACL_BSheet' => htmlspecialchars($_REQUEST['ACL_BSheet'], ENT_QUOTES),
    'ACL_BSheet_VM' => htmlspecialchars($_REQUEST['ACL_BSheet_VM'], ENT_QUOTES),      
    'ACL_MasterReports' => htmlspecialchars($_REQUEST['ACL_MasterReports'], ENT_QUOTES),
    'ACL_BankReports' => htmlspecialchars($_REQUEST['ACL_BankReports'], ENT_QUOTES),
    'ACL_MISReports' => htmlspecialchars($_REQUEST['ACL_MISReports'], ENT_QUOTES),
    'ACL_User' => htmlspecialchars($_REQUEST['ACL_User'], ENT_QUOTES),
    'ACL_ListUser' => htmlspecialchars($_REQUEST['ACL_ListUser'], ENT_QUOTES),
    'ACL_SalDetail' => htmlspecialchars($_REQUEST['ACL_SalDetail'], ENT_QUOTES),
    'ACL_BnkDetail' => htmlspecialchars($_REQUEST['ACL_BnkDetail'], ENT_QUOTES),
    'ACL_Attendance' => htmlspecialchars($_REQUEST['ACL_Attendance'], ENT_QUOTES),
    'ACL_MnthlyAttendance'=>htmlspecialchars($_REQUEST['ACL_MnthlyAttendance'], ENT_QUOTES),
    'ACL_AttendanceEdt' => htmlspecialchars($_REQUEST['ACL_AttendanceEdt'], ENT_QUOTES),
    'ACL_MnthlyAttendanceSummary'=>htmlspecialchars($_REQUEST['ACL_MnthlyAttendanceSummary'], ENT_QUOTES),
    'ACL_ManageLateEntry'=>htmlspecialchars($_REQUEST['ACL_ManageLateEntry'], ENT_QUOTES),
    'ACL_ApproveLeave' => htmlspecialchars($_REQUEST['ACL_ApproveLeave'], ENT_QUOTES),
    'ACL_Payroll' => htmlspecialchars($_REQUEST['ACL_Payroll'], ENT_QUOTES),
    'ACL_PayrollEdt' => htmlspecialchars($_REQUEST['ACL_PayrollEdt'], ENT_QUOTES),
    'ACL_MealAllowance' => htmlspecialchars($_REQUEST['ACL_MealAllowance'], ENT_QUOTES),
    'ACL_State' => htmlspecialchars($_REQUEST['ACL_State'], ENT_QUOTES),
    'ACL_City' => htmlspecialchars($_REQUEST['ACL_City'], ENT_QUOTES),
    'ACL_Company' => htmlspecialchars($_REQUEST['ACL_Company'], ENT_QUOTES),
    'ACL_Branch' => htmlspecialchars($_REQUEST['ACL_Branch'], ENT_QUOTES),
    'ACL_Dept' => htmlspecialchars($_REQUEST['ACL_Dept'], ENT_QUOTES),
    'ACL_Desig' => htmlspecialchars($_REQUEST['ACL_Desig'], ENT_QUOTES),
    'ACL_MH' => htmlspecialchars($_REQUEST['ACL_MH'], ENT_QUOTES),
    'ACL_SH' => htmlspecialchars($_REQUEST['ACL_SH'], ENT_QUOTES),
    'ACL_Item' => htmlspecialchars($_REQUEST['ACL_Item'], ENT_QUOTES),
    'ACL_Description' => htmlspecialchars($_REQUEST['ACL_Desc'], ENT_QUOTES),
    'ACL_Unit' => htmlspecialchars($_REQUEST['ACL_Unit'], ENT_QUOTES),
    'ACL_NotifyQueue' => htmlspecialchars($_REQUEST['ACL_NotifyQueue'], ENT_QUOTES),
    'ACL_Paymode' => htmlspecialchars($_REQUEST['ACL_Paymode'], ENT_QUOTES),
    'ACL_SalStruct' => htmlspecialchars($_REQUEST['ACL_SalStruct'], ENT_QUOTES),
    'ACL_SalPayMode' => htmlspecialchars($_REQUEST['ACL_SalPayMode'], ENT_QUOTES),
    'ACL_Access' => htmlspecialchars($_REQUEST['ACL_Access'], ENT_QUOTES),
    'ACL_SidebarMH' => htmlspecialchars($_REQUEST['ACL_SidebarMH'], ENT_QUOTES),
    'ACL_SidebarOFF' => htmlspecialchars($_REQUEST['ACL_SidebarOFF'], ENT_QUOTES),
    'ACL_ManageTracks' => htmlspecialchars($_REQUEST['ACL_ManageTracks'], ENT_QUOTES),
    'ACL_DeleteEntries' => htmlspecialchars($_REQUEST['ACL_DeleteEntries'], ENT_QUOTES),
    'ACL_ManageBSDate' => htmlspecialchars($_REQUEST['ACL_ManageBSDate'], ENT_QUOTES),
    'ACL_Track' => htmlspecialchars($_REQUEST['ACL_Track'], ENT_QUOTES),
    'ACL_TrackInvReceipt' => htmlspecialchars($_REQUEST['ACL_TrackInvReceipt'], ENT_QUOTES),
    'ACL_NotfLC' => htmlspecialchars($_REQUEST['ACL_NotfLC'], ENT_QUOTES),
    'ACL_NotfBNK' => htmlspecialchars($_REQUEST['ACL_NotfBNK'], ENT_QUOTES),
    'ACL_Att_Master' => htmlspecialchars($_REQUEST['ACL_Att_Master'], ENT_QUOTES),
    'ACL_FeedbackRpt' => htmlspecialchars($_REQUEST['ACL_FeedbackRpt'], ENT_QUOTES),
    'ACL_SalPMwiseBranch' => htmlspecialchars($_REQUEST['ACL_SalPMwiseBranch'], ENT_QUOTES),
    'ACL_SalPMwiseAll' => htmlspecialchars($_REQUEST['ACL_SalPMwiseAll'], ENT_QUOTES),
    'ACL_ManageBusinessAmt' => htmlspecialchars($_REQUEST['ACL_ManageBusinessAmt'], ENT_QUOTES),
    'ACL_SalAdvance' => htmlspecialchars($_REQUEST['ACL_SalAdvance'], ENT_QUOTES),
    'ACL_ZonalManage' => htmlspecialchars($_REQUEST['ACL_ZonalManage'], ENT_QUOTES),
    'ACL_ZonalHead' => htmlspecialchars($_REQUEST['ACL_ZonalHead'], ENT_QUOTES),
    'ACL_AllItem' => htmlspecialchars($_REQUEST['ACL_AllItem'], ENT_QUOTES),
    'ACL_Status' => htmlspecialchars($_REQUEST['ACL_Status'], ENT_QUOTES),
    'ACL_Purchase_Team' => htmlspecialchars($_REQUEST['ACL_Purchase_Team'], ENT_QUOTES),
    'ACL_Purchase_Approval' => htmlspecialchars($_REQUEST['ACL_Purchase_Approval'], ENT_QUOTES),
    'ACL_EditAccEntries' => htmlspecialchars($_REQUEST['ACL_EditAccEntries'], ENT_QUOTES),
    'ACL_MDate' => date('Y-m-d H:i:s')
);

function replacenulls(& $item, $key) {
    if ($item === '') {
        $item = 0;
    }
}

function replaceupdnulls(& $item, $key) {
    if ($item === '' || $item === null) {
        $item = -1;
    }
}

$office_id = htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES);
if (($office_id == "") || ($office_id == 0)) {
    $office_id = $preTally_user_ofid;
}
if ($ACLObj->verifyACL(htmlspecialchars($_REQUEST['ACL_Id'], ENT_QUOTES), $office_id)) {
    if (htmlspecialchars($_REQUEST['ACL_Id'], ENT_QUOTES) == 0) {
        array_walk_recursive($ACLObj->ACL_Data, 'replacenulls');
        $ACLObj->ACL_Data["ACL_CDate"] = date('Y-m-d H:i:s');
        $ACLObj->ACL_Data['OF_Id'] = $preTally_user_ofid;
        $ACLObj->ACL_Data['US_Id'] = $preTally_user_id;
        $ACLObj->newACL();
        echo 'ACL Created Successfully';
    } else {
        array_walk_recursive($ACLObj->ACL_Data, 'replaceupdnulls');
        echo $ACLObj->updateACL(htmlspecialchars($_REQUEST['ACL_Id'], ENT_QUOTES));
    }
} else {
    echo 'ACL Name already Exists';
}
?>