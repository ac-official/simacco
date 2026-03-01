<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
$OfficeObj = new OfficeClass();

$mstrPage   = 0;
$accentry   = 1;
$officeAdm  =0;
$new_item   =0;
$branchRpts =0;
$attendance =0;
$salreport  =0;
$accentrySAMP=0;
$breaktime  = $UserACLObj->view_break_time; // 04-07-2025
if($preTally_user_id==36)$accentrySAMP=1;        
if($preTally_user_ofid == 1)    { $mstrPage = 1; $accentry =0; $new_item = 1;}

if($ACL_Obj->ACL_HR==4 && $ACL_Obj->ACL_HR_VM == 1) {$officeAdm=1;} //06-06-25 (acl_hr_vm added)

if($ACL_Obj->ACL_BSheet == 2 || $ACL_Obj->ACL_BSheet == 4 ){
    $branchRpts = 1;
}
if($ACL_Obj->ACL_Attendance==1 || $ACL_Obj->ACL_AttendanceEdt==1 || $ACL_Obj->ACL_Payroll==1 || $ACL_Obj->ACL_PayrollEdt==1)$attendance=1;
if($ACL_Obj->ACL_Payroll==1 || $ACL_Obj->ACL_PayrollEdt==1 || $ACL_Obj->ACL_SalPMwiseBranch==1 || $ACL_Obj->ACL_SalPMwiseAll==1)$salreport=1;
$OFArray = explode(' ', $preTally_user_ofname);
$OFName = $OFArray[0];      
$OfficeObj->myMapOffice($preTally_user_id); 

$Map_Obj = $OfficeObj->OfficeMapArray;
$mapArray =  explode(",", $Map_Obj->UAM_Map);
$child = 'n';
$accountteam    = $UserACLObj->view_accounts_report; // 3-6-25 this is for account team acl

if(in_array($preTally_user_id, $mapArray)) {
    $child = 'y';
    $otherAccounts  = array('text' => 'Login as',   'img' => 'company.png', 'parent' => 'profile_management', 'status' => '1', 'child' => $child, 'seperator' => '0') ;
}

//$old = array("[", "]");
//$new   = array("", "");
//$officeMap = str_replace($old, $new, $Map_Obj[0]->UAM_Map);
$officeMap = implode(",", $mapArray);
if($officeMap == '') $officeMap = '""';

$OfficeObj->viewMyOffices('US.US_Id IN ('.$officeMap.') AND US.US_Id !='.$preTally_user_id.' ORDER BY OF1.OF_Name');
$OF_Obj = $OfficeObj->OfficeArray;

//echo $child;
$menuItems = array(  

    'new'                   => array('text' => 'Accounts &#x25BE;',     'img' => 'home.png', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '0'),
    'user_management'       => array('text' => 'User &#x25BE;',         'img' => 'admin_2.png', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '2'),
    'master_data'           => array('text' => 'Master Data &#x25BE;',  'img' => 'settings.gif', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '2'),
    'widgets'               => array('text' => 'Widgets &#x25BE;',      'img' => 'widget_02.png', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '2'),
    'notifications'         => array('text' => 'Notifications &#x25BE;','img' => 'notification.png', 'parent' => '0', 'status' => '1', 'child' => 'n', 'seperator' => '2'),
    'menu_help'             => array('text' => 'Help &#x25BE;',         'img' => 'about_2.png', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '2'), 
    'report_bug'            => array('text' => 'Report a Problem',      'img' => 'warning-icon.png', 'parent' => '0', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
   
    'send_message'          => array('text' => 'Send a Message',        'img' => 'messagereadicon_32.png', 'parent' => '0', 'status' => '1', 'child' => 'n', 'seperator' => '2'),    
    'track'                 => array('text' => 'Track',                 'img' => 'track_16.png', 'parent' => '0', 'status' => '0', 'child' => 'n', 'seperator' => '2'),
    'im'                    => array('text' => 'IM',                  'img' => 'im.png', 'parent' => '0', 'status' => '1', 'child' => 'n', 'seperator' => '2'),
//    'manage_entryEditDate'  => array('text' => 'edit',                 'img' => 'track_16.png', 'parent' => '0', 'status' => $ACL_Obj->ACL_Track, 'child' => 'n', 'seperator' => '2'),
    //'send_message'          => array('text' => 'Send a Message',        'img' => 'messagereadicon_32.png', 'parent' => '0', 'status' => '1', 'child' => 'n', 'seperator' => '2'),
    'account_dept' => array('text' => 'Accounts V2 &#x25BE;', 'img' => 'calculator_20.png', 'parent' => '0', 'status' => $accountteam, 'child' => 'y', 'seperator' => '2'), //08-07-2025
    
    'profile_management'    => array('text' => $preTally_user_name.' &#x25BE;', 'img' => 'user.png', 'parent' => '0', 'status' => '1', 'child' => 'y', 'seperator' => '2'),
    
    'GS_Menu'               => array('text' => 'General Settings',      'img' => 'settings_2.png', 'parent' => 'master_data', 'status' => '1', 'child' => 'y', 'seperator' => '0'),
    'BS_Menu'               => array('text' => 'Account Entry Settings','img' => 'settings_1.png', 'parent' => 'master_data', 'status' => '1', 'child' => 'y', 'seperator' => '0'),
    'HR_Menu'               => array('text' => 'HR Settings',           'img' => 'settings_03.png', 'parent' => 'master_data', 'status' => '1', 'child' => 'y', 'seperator' => '0'),
    'Excel_Menu'            => array('text' => 'Bulk Upload',           'img' => 'settings_03.png', 'parent' => 'master_data', 'status' => $ACL_Obj->ACL_HR_VM, 'child' => 'y', 'seperator' => '0'),
    'HI_Menu'               => array('text' => 'History',               'img' => 'settings_1.png',  'parent' => 'master_data', 'status' => $mstrPage, 'child' => 'y', 'seperator' => '0'),
    'inventory'             => array('text' => 'Inventory Management',   'img' => 'settings_2.png', 'parent' => 'master_data', 'status' => '1', 'child' => 'n', 'seperator' => '0'),

//    'menuNewBalSheetSample' => array('text' => 'Accounts Entry Sample', 'img' => 'account.png', 'parent' => '0', 'status' => $accentrySAMP, 'child' => 'n', 'seperator' => '2'),    
    'menuNewBalSheet'       => array('text' => 'Accounts Entry',        'img' => 'account.png', 'parent' => 'new', 'status' => $accentry, 'child' => 'n', 'seperator' => '0'), 
    'pendingTrack'          => array('text' => 'My Pending Tracks', 'img' => 'file_edit.png', 'parent' => 'new', 'status' => 1, 'child' => 'n', 'seperator' => '0'),    
    'view_expCntrl'         => array('text' => 'Expense Control',       'img' => 'chart.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),
    'view_edt_reports'      => array('text' => 'Manage Account Entries','img' => 'file_edit.png', 'parent' => 'new', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    
    'attendance_user'       => array('text' => 'Attendance and Payroll Management', 'img' => 'dept.png', 'parent' => 'new', 'status' => $attendance, 'child' => 'n', 'seperator' => '1'),
    'salary_report'         => array('text' => 'Salary Report', 'img' => 'dept.png', 'parent' => 'new', 'status' => $salreport, 'child' => 'n', 'seperator' => '0'),    
    
    'admin_bugreport'       => array('text' => 'Feedback Reports',      'img' => 'error_24.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_FeedbackRpt, 'child' => 'n', 'seperator' => '0'),
    
    'view_mstrRpt_export'   => array('text' => 'Additional Reports Download', 'img' => 'graph_icon.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '1'),    
    'view_bankbsreports'    => array('text' => 'Bank Book',    'img' => 'graph_icon.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_BankReports, 'child' => 'n', 'seperator' => '0'),
    'view_branchbsreports'  => array('text' => 'Bank Transaction Summary Reports',    'img' => 'graph_icon.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_BankReports, 'child' => 'n', 'seperator' => '0'),    
    'view_branch_mstrRpt'   => array('text' => 'Branch Master Reports ','img' => 'graph_icon.png', 'parent' => 'new', 'status' => $branchRpts, 'child' => 'n', 'seperator' => '0'),
    'businessreport_manage' => array('text' => 'Business Report - Manage','img' => 'graph_icon.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_ManageBusinessAmt, 'child' => 'n', 'seperator' => '0'),
    'view_businessreports'  => array('text' => 'Business Summary Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' => $branchRpts, 'child' => 'n', 'seperator' => '0'),
    'view_cashbsreports'    => array('text' => 'Cash Transaction Summary Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' =>$branchRpts, 'child' => 'n', 'seperator' => '0'),
    'view_reports'          => array('text' => 'Hierarchical Report',   'img' => 'graph_icon.png', 'parent' => 'new', 'status' => '1', 'child' => 'n', 'seperator' => '0'),    
    'view_trackdupreports'     => array('text' => 'Track Duplicate Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' => $branchRpts, 'child' => 'n', 'seperator' => '0'),    
    'view_master_reports'   => array('text' => 'Master Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' => ($ACL_Obj->ACL_MasterReports && $UserACLObj->view_master_report), 'child' => 'n', 'seperator' => '0'),
    'view_userie_reports'   => array('text' => $OFName.' Master Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' => ($ACL_Obj->ACL_MasterReports && $UserACLObj->view_master_report), 'child' => 'n', 'seperator' => '0'),
    'other_master_reports'   => array('text' => 'Other Master Reports','img' => 'graph_icon.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'y', 'seperator' => '0'),   
    'view_accsummary_reports'=>array('text' => 'Master Reports - New', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),
    'view_mr_location'      =>array('text' => 'Master Reports - Location Based', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),
    'track_reports'         => array('text' => 'Master Reports - Location Based Job Amount', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'), 
    'enquiry_count_reports'         => array('text' => 'Master Reports - Location Based Enquiry Count', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),    
    'job_count_reports'         => array('text' => 'Master Reports - Location Based Job [Candidate] Count', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'), 
    'certificate_count_reports'         => array('text' => 'Master Reports - Location Based Certificate Count', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),  
    'view_businessbonus_reports'   => array('text' => 'Master Reports - Business and Bonus', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),
    'income_reports'        => array('text' => 'Master Reports - Income and Profit', 'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),
    'misReport'             => array('text' => 'MIS Report',   'img' => 'graph_icon.png', 'parent' => 'other_master_reports', 'status' => $ACL_Obj->ACL_MISReports,  'child' => 'n', 'seperator' => '0'),    
    'othReports'             => array('text' => 'Other Reports',   'img' => 'graph_icon.png', 'parent' => 'new', 'status' => '1',  'child' => 'y', 'seperator' => '0'),    
    'view_mybankreports'    => array('text' => 'My Bank Book',    'img' => 'graph_icon.png', 'parent' => 'othReports', 'status' => 1, 'child' => 'n', 'seperator' => '0'),   
    'old_job_process'         => array('text' => 'Old Job Processes', 'img' => 'search.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_ManageTracks, 'child' => 'n', 'seperator' => '0'),
    'view_pettycashreports' => array('text' => 'Petty Cash Report',     'img' => 'graph_icon.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_MasterReports, 'child' => 'n', 'seperator' => '0'),    
    'monthwisesal_report'   => array('text' => 'Salary History and Increment Report', 'img' => 'dept.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_PayrollEdt, 'child' => 'n', 'seperator' => '0'),
    'view_stockreports'     => array('text' => 'Stock Summary Reports','img' => 'graph_icon.png', 'parent' => 'othReports', 'status' => $branchRpts, 'child' => 'n', 'seperator' => '0'),    
    //'misReport_2'            => array('text' => 'MIS 2',   'img' => 'graph_icon.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_MISReports,  'child' => 'n', 'seperator' => '0'),
    //  'view_branchreports'    => array('text' => 'Branch Reports',        'img' => 'graph_icon.png', 'parent' => 'othReports', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
//  'accounts_search'       => array('text' => 'Search',                'img' => 'search.png', 'parent' => 'othReports', 'status' => '1', 'child' => 'n', 'seperator' => '0'),    
    'manage_tracks'         => array('text' => 'Manage Tracks',          'img' => 'search.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_ManageTracks, 'child' => 'n', 'seperator' => '1'),
    'tracks_search'         => array('text' => 'Track Search',          'img' => 'search.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_ManageTracks, 'child' => 'n', 'seperator' => '0'),
    'tracks_all_list'       => array('text' => 'Trackwise Report', 'img' => 'search.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_ManageTracks, 'child' => 'n', 'seperator' => '0'),
    'tracks_expense'       => array('text' => 'Trackwise Expense Report', 'img' => 'search.png', 'parent' => 'othReports', 'status' => $ACL_Obj->ACL_ManageTracks, 'child' => 'n', 'seperator' => '0'),    
    
//  'approval_queue'        => array('text' => 'Approval Queue',       'img' => 'search.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_NotifyQueue, 'child' => 'n', 'seperator' => '0'),
    'admin_dashboard'       => array('text' => 'My Dashboard',          'img' => 'dashboard_24.png', 'parent' => 'new', 'status' => $mstrPage, 'child' => 'n', 'seperator' => '1'),
    
    
    'new_user'              => array('text' => 'Add a New User',        'img' => 'user_24.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_User, 'child' => 'n', 'seperator' => '0'),
    'list_user'             => array('text' => 'List of Users',         'img' => 'dept.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_ListUser, 'child' => 'n', 'seperator' => '0'),    
    'assign_zones'          => array('text' => 'Assign Zones',         'img' => 'dept.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_ZonalManage, 'child' => 'n', 'seperator' => '0'),    
    'import_users'          => array('text' => 'Import Users',          'img' => 'user_24.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_User, 'child' => 'n', 'seperator' => '0'),
    'import_users_details'  => array('text' => 'Import Users Details',  'img' => 'user_24.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_User, 'child' => 'n', 'seperator' => '0'),    
    'salary_advance'        => array('text' => 'Salary Advance And Repayment', 'img' => 'dept.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_SalAdvance, 'child' => 'n', 'seperator' => '0'),
    'meal_allowance'        => array('text' => 'Meal Allowance', 'img' => 'dept.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_MealAllowance, 'child' => 'n', 'seperator' => '0'),
 
    'menu_ApproveLeave'     => array('text' => 'Approve Leave',          'img' => 'dept.png', 'parent' => 'user_management', 'status' => 1,'child' => 'n', 'seperator' => '0'),
    'menu_LateEntriesList'     => array('text' => 'Approve Late Entry',          'img' => 'dept.png', 'parent' => 'user_management', 'status' => $UserACLObj->view_late_entry,'child' => 'n', 'seperator' => '0'),
    'leave_details'         => array('text' => 'Leave Details',          'img' => 'dept.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_ApproveLeave,'child' => 'n', 'seperator' => '0'),

    'break_time_user'       => array('text' => 'Break Time', 'img' => 'clock4.png', 'parent' => 'user_management', 'status' => $breaktime, 'child' => 'n', 'seperator' => '0'), //11-06-2025
    //27-06-2025
    'break_time_list'       => array('text' => 'Break Time Report', 'img' => 'search.png', 'parent' => 'user_management', 'status' => $breaktime, 'child' => 'n', 'seperator' => '0'),
    


//  'new_payroll'           => array('text' => 'Payroll',               'img' => 'file_edit.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_Payroll 	, 'child' => 'n', 'seperator' => '1'),
//  'list_salary'           => array('text' => 'Salary Details',        'img' => 'spread.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_SalDetail, 'child' => 'n', 'seperator' => '0'),
    'access_levels'         => array('text' => 'Access Control Levels', 'img' => 'lock.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_Access, 'child' => 'n', 'seperator' => '1'),
    'access_user_level'     => array('text' => 'Users Access Control', 'img' => 'lock.png', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_Access, 'child' => 'n', 'seperator' => '0'),
//  'user_map_tree'         => array('text' => 'User Map Tree',         'img' => 'list_users_20.gif', 'parent' => 'user_management', 'status' => $ACL_Obj->ACL_Access, 'child' => 'n', 'seperator' => '1'),
    'import_leaves'         => array('text' => 'Import Leaves',          'img' => 'dept.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_ApproveLeave,'child' => 'n', 'seperator' => '0'),
    'import_subprocess'     => array('text' => 'Import Track Subprocess', 'img' => 'settings_1.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_Att_Master,'child' => 'n', 'seperator' => '0'),
    'import_salhistory'     => array('text' => 'Import Salary History',  'img' => 'dept.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_MasterReports,'child' => 'n', 'seperator' => '0'),
    'import_updsalary'          => array('text' => 'Import Updated Salary',          'img' => 'chart.png', 'parent' => 'Excel_Menu', 'status' => $attendance, 'child' => 'n', 'seperator' => '0'),    
    'new_state'             => array('text' => 'State',                 'img' => 'globe.png', 'parent' => 'GS_Menu', 'status' => $ACL_Obj->ACL_State, 'child' => 'n', 'seperator' => '0'),
    'new_city'              => array('text' => 'City',                  'img' => 'globe_1.png', 'parent' => 'GS_Menu', 'status' => $ACL_Obj->ACL_City, 'child' => 'n', 'seperator' => '0'),
    'new_place'             => array('text' => 'Places',                'img' => 'globe_1.png', 'parent' => 'GS_Menu', 'status' => $ACL_Obj->ACL_City, 'child' => 'n', 'seperator' => '0'),
    
    'new_mainhead'          => array('text' => 'Main Heads',            'img' => 'category_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_MH, 'child' => 'n', 'seperator' => '0'),
    'new_subhead'           => array('text' => 'Sub Heads',             'img' => 'category_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_SH, 'child' => 'n', 'seperator' => '0'),
    'sh_pattern'            => array('text' => 'Manage Subhead Patterns','img' => 'category_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_SH, 'child' => 'n', 'seperator' => '0'),
    
//  'new_item'              => array('text' => 'New Item',              'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $new_item, 'child' => 'n', 'seperator' => '1'),
//  'new_company_item'      => array('text' => 'New Item',              'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '1'),
    'new_ofz_item'          => array('text' => 'New Item',              'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '1'),
    'custom_item'           => array('text' => $OFName.' Defined Items',     'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '0'),
    'pretally_item'         => array('text' => 'Pretally Defined Items','img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '0'),
    'otherCompany_item'     => array('text' => 'Other Company Defined Items','img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $mstrPage, 'child' => 'n', 'seperator' => '0'),
    'unused_ItemDesc'       => array('text' => 'Manage Unused Items and Descriptions',  'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item,  'child' => 'n', 'seperator' => '0'),
    
    'listOfzItem'           => array('text' => 'Manage All Items',       'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_AllItem, 'child' => 'n', 'seperator' => '0'),
    'listOfzDesc'           => array('text' => 'Manage All Descriptions','img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_AllItem, 'child' => 'n', 'seperator' => '0'),
    'listOfzActsEntry'      => array('text' => 'Accounts Entry ',       'img' => 'item_20.png', 'parent' => 'HI_Menu', 'status' => $mstrPage, 'child' => 'n', 'seperator' => '0'),
    
    'import_items'          => array('text' => 'Import Items',           'img' => 'item_20.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '0'),
    'import_descriptions'   => array('text' => 'Import Descriptions',  'img' => 'item_20.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_Item, 'child' => 'n', 'seperator' => '0'),
    
//  'new_description'       => array('text' => 'Item Description',      'img' => 'department_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Description, 'child' => 'n', 'seperator' => '0'),
    'new_unit'              => array('text' => 'Units',                 'img' => 'meter.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Unit, 'child' => 'n', 'seperator' => '0'),
    'new_paymode'           => array('text' => 'Payment Modes',         'img' => 'dollar.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Paymode, 'child' => 'n', 'seperator' => '0'),
    'new_bank'              => array('text' => 'Banks',                 'img' => 'bank.png', 'parent' => 'BS_Menu', 'status' => $mstrPage, 'child' => 'n', 'seperator' => '1'),
    'new_bnkbranch'         => array('text' => 'Bank Branches',         'img' => 'bank_branch.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_BnkDetail, 'child' => 'n', 'seperator' => '1'),
    'new_bnkacc'            => array('text' => 'Bank Accounts',         'img' => 'bank_account.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_BnkDetail, 'child' => 'n', 'seperator' => '0'),
    'new_cheque'            => array('text' => 'Cheque Books',          'img' => 'bank_account.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_BnkDetail, 'child' => 'n', 'seperator' => '0'),    
    'manage_chqleaves'      => array('text' => 'Manage Cheque Leaves',  'img' => 'bank_account.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_BnkDetail, 'child' => 'n', 'seperator' => '0'),        
    'manage_entryEditDate'  => array('text' => 'Manage Entry Edit Date','img' => 'bank_account.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_ManageBSDate, 'child' => 'n', 'seperator' => '0'),        
    
    'list_OldStockAmount'   => array('text' => 'Old Stock Amount',      'img' => 'branch.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Branch, 'child' => 'n', 'seperator' => '1'),
    'open_balance'          => array('text' => 'Branch Opening Balance','img' => 'branch.png', 'parent' => 'BS_Menu', 'status' => ($ACL_Obj->ACL_Branch || $UserACLObj->add_open_balance), 'child' => 'n', 'seperator' => '1'),
    'open_bnkbalance'       => array('text' => 'Bank Opening Balance','img' => 'branch.png', 'parent' => 'BS_Menu', 'status' => ($ACL_Obj->ACL_Branch || $UserACLObj->add_open_balance), 'child' => 'n', 'seperator' => '0'),
    'new_office'            => array('text' => 'Company',               'img' => 'company.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_Company, 'child' => 'n', 'seperator' => '0'),
    'listOfzAdmin'          => array('text' => 'Map Company',           'img' => 'company.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_Company, 'child' => 'n', 'seperator' => '0'),
    'new_location'          => array('text' => 'Branch',                'img' => 'branch.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_Branch, 'child' => 'n', 'seperator' => '1'),
    'manage_zones'          => array('text' => 'Manage Zones',       'img' => 'branch.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_ZonalManage, 'child' => 'n', 'seperator' => '0'),
    'import_branches'       => array('text' => 'Import Branches',       'img' => 'branch.png', 'parent' => 'Excel_Menu', 'status' => $ACL_Obj->ACL_Branch, 'child' => 'n', 'seperator' => '0'),
    
    'unused_ItemDesc'  => array('text' => 'Manage Unused Items and Descriptions',  'img' => 'item_20.png', 'parent' => 'BS_Menu', 'status' => $ACL_Obj->ACL_Item,  'child' => 'n', 'seperator' => '1'),
    'new_designation'      => array('text' => 'Designation',           'img' => 'user_24.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_Desig, 'child' => 'n', 'seperator' => '0'),
    'new_department'       => array('text' => 'Department',            'img' => 'department_20.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_Dept, 'child' => 'n', 'seperator' => '0'),
    'new_salstruct'        => array('text' => 'Salary Structure',      'img' => 'sal_struct.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_SalStruct, 'child' => 'n', 'seperator' => '1'),
    'new_salpaymode'       => array('text' => 'Salary Payable Modes',  'img' => 'salary_icon_16.png', 'parent' => 'HR_Menu', 'status' => $ACL_Obj->ACL_SalPayMode, 'child' => 'n', 'seperator' => '0'),
    'menu_holidays'        => array('text' => 'Manage Holidays',        'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $officeAdm, 'child' => 'n', 'seperator' => '1'),
    'menu_ManageLeaveType' => array('text' => 'Manage Leave Type',      'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $officeAdm, 'child' => 'n', 'seperator' => '0'),
    'menu_ManageRule' => array('text' => 'Manage Attendance Rule',      'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $officeAdm, 'child' => 'n', 'seperator' => '0'),
    'menu_Adj_Attendance' => array('text' => 'Clear Attendance',      'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $UserACLObj->view_clear_attencance, 'child' => 'n', 'seperator' => '0'),
    'menu_Adj_Time' => array('text' => 'Wroking Time Adjusted Employees', 'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $UserACLObj->view_clear_attencance, 'child' => 'n', 'seperator' => '0'),
//  'menu_Employee_Status' => array('text' => 'Manage Employee Status',      'img' => 'caution.png', 'parent' => 'HR_Menu', 'status' => $officeAdm, 'child' => 'n', 'seperator' => '0'),    
    'calculator'            => array('text' => 'Calculator',            'img' => 'calculator_20.png', 'parent' => 'widgets', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'menuToolbarCalendar'   => array('text' => 'Calendar',              'img' => 'calendar_16.png', 'parent' => 'widgets', 'status' => '1', 'child' => 'n', 'seperator' => '0'),    
    'about_pty'             => array('text'  => 'About Pretally',        'img' => 'info_18.png', 'parent' => 'menu_help', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'contact_us'            => array('text' => 'Contact Us',            'img' => 'contact_us.png', 'parent' => 'menu_help', 'status' => '1', 'child' => 'n', 'seperator' => '0'),     
    'contact_us'            => array('text' => 'Contact Us',            'img' => 'contact_us.png', 'parent' => 'menu_help', 'status' => '1', 'child' => 'n', 'seperator' => '0'), 
     'admin_bugreport'       => array('text' => 'Feedback Reports',      'img' => 'error_24.png', 'parent' => 'new', 'status' => $ACL_Obj->ACL_FeedbackRpt, 'child' => 'n', 'seperator' => '1'),
    //'admin_bugreport'       => array('text' => 'Feedback Reports',      'img' => 'error_24.png', 'parent' => 'menu_help', 'status' => $ACL_Obj->ACL_FeedbackRpt, 'child' => 'n', 'seperator' => '0'),
    
//  'my_wallet'             => array('text' => 'My Wallet',            'img' => 'profile.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'my_profile'            => array('text' => 'My Profile',            'img' => 'profile.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'my_username'           => array('text' => 'My Username',           'img' => 'profile.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'my_password'           => array('text' => 'My Password',           'img' => 'key_20.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'my_attendance'         => array('text' => 'My Attendance',         'img' => 'profile.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'mywallet'              => array('text' => 'My Wallet',             'img' => 'wallet.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '0'),
    'my_companies'          => $otherAccounts,
    'sign_out'              => array('text' => 'Logout',              'img' => 'logout_20.png', 'parent' => 'profile_management', 'status' => '1', 'child' => 'n', 'seperator' => '1'),
    // accounts teams menus start 08-07-2025  
    'account_settings'    => array('text' => 'Settings','img' => 'settings_1.png', 'parent' => 'account_dept', 'status' =>(isset($UserACLObj->view_account_settings) ? $UserACLObj->view_account_settings : 0), 'child' => 'y', 'seperator' => '0'),
      'account_group'    => array('text' => 'Manage Group','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_ledger'    => array('text' => 'Manage Ledger','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_map_item'    => array('text' => 'Map Ledger &amp; Item','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_tds'    => array('text' => 'Manage TDS Rules','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_vendor'    => array('text' => 'Manage Vendor, Debtors or Creditor','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_recurbills'    => array('text' => 'Manage Recurring Bills','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>(isset($UserACLObj->manage_bills) ? $UserACLObj->manage_bills : 0), 'child' => 'n', 'seperator' => '0'),
      'account_openbalance'    => array('text' => 'Manage Opening Balance','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>($UserACLObj->approve_account_settings || $UserACLObj->add_open_balance), 'child' => 'n', 'seperator' => '0'),
      'account_importextra'    => array('text' => 'Upload Extra Data','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),
      'account_extra_data'    => array('text' => 'Manage Extra Data','img' => 'item_20.png', 'parent' => 'account_settings', 'status' =>'1', 'child' => 'n', 'seperator' => '0'),

    'account_journal' => array('text' => 'Map Ledger Data','img' => 'item_20.png', 'parent' => 'account_dept', 'status' =>(isset($UserACLObj->approve_ledger_amount) ? $UserACLObj->approve_ledger_amount : 0), 'child' => 'n', 'seperator' => '0'),
    'account_journal_data' => array('text' => 'Add Ledger Data','img' => 'item_20.png', 'parent' => 'account_dept', 'status' =>(isset($UserACLObj->approve_ledger_amount) ? $UserACLObj->approve_ledger_amount : 0), 'child' => 'n', 'seperator' => '0'),
    'account_journal_entry' => array('text' => 'Manage Journal Entry','img' => 'item_20.png', 'parent' => 'account_dept', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'),
    'account_bills' => array('text' => 'Manage Bills','img' => 'item_20.png', 'parent' => 'account_dept', 'status' =>(isset($UserACLObj->manage_bills) ? $UserACLObj->manage_bills : 0), 'child' => 'n', 'seperator' => '0'),
    'account_team_rpt'   => array('text' => 'Reports','img' => 'chart.png', 'parent' => 'account_dept', 'status' => $accountteam, 'child' => 'y', 'seperator' => '0'),
    'account_branchbsreports'  => array('text' => 'Bank Transaction Summary Reports',    'img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' => $ACL_Obj->ACL_BankReports, 'child' => 'n', 'seperator' => '0'),  //03-06-2025 
    'account_cashbsreports'    => array('text' => 'Cash Transaction Summary Reports','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$branchRpts, 'child' => 'n', 'seperator' => '0'), 
    'account_vendor_rpt'    => array('text' => 'Vendor, Debtors or Creditor Report','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'),
    'account_ledger_rpt'    => array('text' => 'Ledger Report','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_bnkcash_rpt'    => array('text' => 'Bank And Branch Report','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'),  
    //'account_profit_loss'    => array('text' => 'Income and Expense','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_pandl'    => array('text' => 'Profit and Loss Account','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_blancesheet'    => array('text' => 'Balance Sheet','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_cashflow'    => array('text' => 'Cash Flow Statement','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_cpandl'    => array('text' => 'Detailed Profit and Loss','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_not_transfer'    => array('text' => 'Not Transfered / Blocked List','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 
    'account_change_after'    => array('text' => 'Updated After Data Mapped','img' => 'graph_icon.png', 'parent' => 'account_team_rpt', 'status' =>$accountteam, 'child' => 'n', 'seperator' => '0'), 

    // Accounts Team menus section ends
);

if($OF_Obj) {
    $j = 1;
    foreach($OF_Obj as $rw) {
        $selfAcc = array('text' => $rw->OF_Name, 'img' => 'company.png', 'parent' => 'my_companies', 'status' => '1', 'child' => 'n', 'seperator' => '0');
        $menuItems["UCMap_".$rw->US_EMPID] = $selfAcc;

    }
}
echo '<menu>'.drawMenu($menuItems,'0').'</menu>';
$IsMenu = '';
$prevStats=0;
function drawMenu($menuItems,$parent){    
    foreach ($menuItems as $key => $value) {
        if($parent == $value['parent']) {
            if($value['status'] == '1') {
                $IsMenu = drawMenu($menuItems,$key);
                if(($IsMenu || ($value['child'] == 'n'))) {
                    for($j=0; $j<$value['seperator']; $j++){
                        if($prevStats==1)
                        $subMenu .=  '<item type="separator"/>';
                    }
                    $subMenu .= '<item id="'.$key.'" text="'.$value['text'].'" img="'.$value['img'].'">';
                    $subMenu .=  $IsMenu;
                    $subMenu .=  '</item>'; 
                }
            }
            $prevStats=$value['status'];
        }
    }
    return $subMenu;   
}
?>
