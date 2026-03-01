<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$user_id = $REQUEST['r'];

$upd_type   = '';
$UserObj    = new UserClass();
$GeneralObj = new GeneralClass();
if ($user_id == 'self') {
    $upd_type = 'self';
    $user_id = $_SESSION['preTally_user_id'];
}
$desigDet           = [];
$graceTimeDet       = [];
if ($user_id != NULL) {
    $UserDetails    = $UserObj->viewSingleUser($user_id);
    $desigDet       = $UserObj->listDesigUser($user_id, $UserDetails->DG_Id, $UserDetails->US_MDate); //06-10-2025
    // check the working time of an employee less than 9 hour and greater than 0 27-11-2025
    if ($UserDetails->US_WrkHours < 540 && $UserDetails->US_WrkHours > 0) {
        $graceTimeDet   = $UserObj->getGraceTimeUser($user_id);
    } 
}
//$sibling_id     = $GeneralObj->getValue('offices', 'Sibling_id', 'WHERE OF_Id = '.$preTally_user_ofid); //27-03-2025
$sibling_id     = 0; //28-03-2025
$loadsilbing    = 0; //28-03-2025
$UserObj->viewUserTypes();
$UserObj->viewUserDesig();
$UserObj->viewUserDepts();
$UserObj->viewUserLocation('WHERE LC_Status != 5');
$UserObj->viewStates('ORDER BY ST_Name');
$UserObj->viewCities('ORDER BY CT_Name');
$UserObj->viewBloodGroups();
$UserObj->viewSalPayModes();
$UserObj->viewSalStruct();
$UserObj->selectCompanySettings($preTally_user_ofid);
$cmpnySettings = $UserObj->CompanySettingsArray;
$UT_Obj = $UserObj->UserTypeArray;
$UDept_Obj = $UserObj->UserDeptArray;
$ULoc_Obj = $UserObj->UserLocArray;
$UDesig_Obj = $UserObj->UserDesigArray;
$UCt_Obj = $UserObj->CityArray;
$USt_Obj = $UserObj->StateArray;
$UBG_Obj = $UserObj->BloodArray;
$USP_Obj = $UserObj->SalPayArray;
$US_Image = $UserDetails->US_Image ? $UserDetails->US_Image : 'avatar.png';
$CSOffStrt = $UserDetails->US_LoginTime ? $UserDetails->US_LoginTime : $cmpnySettings['CS_OfficeStart'];
$loginArr = explode(":", $CSOffStrt);
$CSOffEnd = $UserDetails->US_LogoutTime ? $UserDetails->US_LogoutTime : $cmpnySettings['CS_OfficeEnds'];
$logoutArr = explode(":", $CSOffEnd);
$GendArray = array('0' => "Not Specified", '1' => "Male", '2' => "Female", '3' => "Others");
$alt_width = 0;
if ($REQUEST['r'] == 'self') {
    $US_Address_Type = $US_Pemail1_Type = $US_Mobile1_Type = $US_Landline1_Type = $US_Relative_Type = $US_Guardian_Type = $US_Guardianphone_Type = $US_Emergencyperson_Type = $US_Emergencynumber_Type = $US_Emergencyrelation_Type = $US_Passport_Type = $US_Qualification_Type = $US_Specialization_Type = $US_Experience_Type = $US_LastEmployee_Type = $US_AccNo_Type = $US_Bankname_Type = $US_BankBranch_Type = $US_PFNo_Type = $US_ESI_Type = $US_PAN = $US_Pemail2_Type = $US_Mobile2_Type = $US_Landline2_Type = 'input';
    $US_DOB_Type = 'calendar';
    $StatHist_Type='fieldset';
    $US_UName_Type = 'template';
    //$US_UName_Type = 'input';
    $CN_Id_Type = $ST_Id_Type = $CT_Id_Type = $US_Blood_Type = $US_Gender_Type = 'combo';
    $LC_Id_Type = $OF_Id_Type = $US_Type_Type = $US_Report_Type = $DP_Id_Type = $ES_Id_Type = $DG_Id_Type = $US_EMPID_Type = $US_Email_Type = $US_FName_Type = $US_LName_Type = $US_DOJ_Type = $ES_Dt_Type = $US_Report_Type = $US_GrossSal_Type = $US_BasicSal_Type = $US_DaSal_Type = $US_HraSal_Type = $US_CcaSal_Type = $US_ConveySal_Type = $US_EduSal_Type = $US_MedSal_Type = $US_MiscSal_Type = $US_DedEPF_Type = $US_DedESI_Type = $US_DedProfTDS_Type = $US_DedSalTDS_Type = $US_DedMealCard_Type = $US_DedLWF_Type = $US_PaymodeType = $US_SalBankName = $US_WrkSchedule = 'template';
    $US_Password_Type = $US_Status_Type = $SS_Id_Type = $US_WelcomeMail = $US_GEN_Pass = $US_RES_Pass = 'hidden';
    $H_Gross_Type = $H_BasicSal_Type = $H_DaSal_Type = $H_HraSal_Type = $H_ConveySal_Type = $H_EduSal_Type = $H_MedSal_Type = $H_MiscSal_Type = 'hidden';
    $required = ' ';
    $US_Status_Required = '';
    $US_DOJ_ReadOnly = '';
    $US_Sal_ReadOnly = '';
    $Paymode_ReadOnly = '';
    $SalBank_ReadOnly = '';
    $validation_keys = '';
    $LC_Id_Connector = '';
    $Paymode_Connector = '';
    $SalBank_Connector = '';
    $US_Save_Btn = 'button';
    $US_Cnc_Btn = 'button';
} else {
    if ($REQUEST['view'] == 1) {
        $alt_width = 150;
        $StatHist_Type='fieldset';        
        $US_DOB_Type = $US_Address_Type = $US_Pemail1_Type = $US_Mobile1_Type = $US_Landline1_Type = $US_Relative_Type = $US_Guardian_Type = $US_Guardianphone_Type = $US_Emergencyperson_Type = $US_Emergencynumber_Type = $US_Emergencyrelation_Type = $US_Passport_Type = $US_Qualification_Type = $US_Specialization_Type = $US_Experience_Type = $US_LastEmployee_Type = $US_AccNo_Type = $US_Bankname_Type = $US_BankBranch_Type = $US_PFNo_Type = $US_ESI_Type = $US_PAN = $US_Pemail2_Type = $US_Mobile2_Type = $US_Landline2_Type = $US_UName_Type = 'template';
        $CN_Id_Type = $ST_Id_Type = $CT_Id_Type = $US_Blood_Type = $US_Gender_Type = 'template';
        $LC_Id_Type = $OF_Id_Type = $US_Type_Type = $US_Report_Type = $DP_Id_Type = $ES_Id_Type = $DG_Id_Type = $US_EMPID_Type = $US_Email_Type = $US_FName_Type = $US_LName_Type = $ES_Dt_Type = $US_DOJ_Type = $US_Report_Type = $US_GrossSal_Type = $US_BasicSal_Type = $US_DaSal_Type = $US_HraSal_Type = $US_CcaSal_Type = $US_ConveySal_Type = $US_EduSal_Type = $US_MedSal_Type = $US_MiscSal_Type = $US_DedEPF_Type = $US_DedESI_Type = $US_DedProfTDS_Type = $US_DedSalTDS_Type = $US_DedMealCard_Type = $US_DedLWF_Type = $US_PaymodeType = $US_SalBankName = $US_WrkSchedule = 'template';
        $US_Password_Type = $US_Status_Type = $SS_Id_Type = $US_WelcomeMail = $US_GEN_Pass = $US_RES_Pass = 'hidden';
        $H_Gross_Type = $H_BasicSal_Type = $H_DaSal_Type = $H_HraSal_Type = $H_ConveySal_Type = $H_EduSal_Type = $H_MedSal_Type = $H_MiscSal_Type = 'hidden';
        $required = ' ';
        $US_Status_Required = '';
        $US_DOJ_ReadOnly = '';
        $US_Sal_ReadOnly = '';
        $Paymode_ReadOnly = '';
        $SalBank_ReadOnly = '';
        $validation_keys = '';
        $LC_Id_Connector = '';
        $Paymode_Connector = '';
        $SalBank_Connector = '';
        $US_Save_Btn = 'hidden';
        $US_Cnc_Btn = 'hidden';
    } else {
        $loadsilbing = 0; //28-03-2025
        if($REQUEST['r']==0) { $StatHist_Type='hidden'; }
        else { $StatHist_Type='fieldset';} 
         
        $US_Address_Type = $US_Pemail1_Type = $US_Mobile1_Type = $US_Landline1_Type = $US_Relative_Type = $US_Guardian_Type = $US_Guardianphone_Type = $US_Emergencyperson_Type = $US_Emergencynumber_Type = $US_Emergencyrelation_Type = $US_Passport_Type = $US_Qualification_Type = $US_Specialization_Type = $US_Experience_Type = $US_LastEmployee_Type = $US_AccNo_Type = $US_Bankname_Type = $US_BankBranch_Type = $US_PFNo_Type = $US_ESI_Type = $US_PAN = $US_Pemail2_Type = $US_Mobile2_Type = $US_Landline2_Type = 'input';
        $US_DOB_Type = 'calendar';
        $CN_Id_Type = $ST_Id_Type = $CT_Id_Type = $US_Blood_Type = $US_Gender_Type = 'combo';
        $LC_Id_Type = $OF_Id_Type = $US_Type_Type = $US_Report_Type = $DP_Id_Type = $ES_Id_Type = $DG_Id_Type = $US_Report_Type = $SS_Id_Type = $US_PaymodeType = $US_SalBankName = $US_WrkSchedule = 'combo';
        $US_EMPID_Type = $US_Email_Type = $US_FName_Type = $US_LName_Type = $US_GrossSal_Type = $US_BasicSal_Type = $US_DaSal_Type = $US_HraSal_Type = $US_CcaSal_Type = $US_ConveySal_Type = $US_EduSal_Type = $US_MedSal_Type = $US_MiscSal_Type = $US_DedEPF_Type = $US_DedESI_Type = $US_DedProfTDS_Type = $US_DedSalTDS_Type = $US_DedMealCard_Type = $US_DedLWF_Type = $US_UName_Type = 'input';
        $US_Password_Type = 'password';
        $US_DOJ_Type = $ES_Dt_Type = 'calendar';
        $US_Status_Type = 'combo';
        $US_WelcomeMail = 'hidden';
        $US_GEN_Pass = 'button';
        $US_Save_Btn = 'button';
        $US_Cnc_Btn = 'button';
        $US_RES_Pass = 'hidden';
        $H_Gross_Type = $H_BasicSal_Type = $H_DaSal_Type = $H_HraSal_Type = $H_CcaSal_Type = $H_ConveySal_Type = $H_EduSal_Type = $H_MedSal_Type = $H_MiscSal_Type = 'hidden';
        $required = 'required="true" ';
        $US_Status_Required = ' required="true" validate="NotEmpty" ';
        $US_DOJ_ReadOnly = ' readonly="true" ';
        $US_Sal_ReadOnly = ' readonly="true" ';
        $Paymode_ReadOnly = ' readonly="true" ';
        $SalBank_ReadOnly = ' readonly="true" ';
        $LC_Id_Connector = "requisites/locations.php";
        $validation_keys = 'required="true" validate="NotEmpty,ValidNumeric"';
        if ($UserDetails->SP_Id == 0)
            $spid = 2;
        else
            $spid = $UserDetails->SP_Id;
        $Paymode_Connector = ' connector="requisites/payableMode.php&amp;r=' . $spid . '"';
        $SalBank_Connector = ' connector="requisites/bankAccountCombo.php&amp;id=' . $UserDetails->Sal_BnkId . '"';
    }
}
if (($REQUEST['r'] != '0') || ($REQUEST['r'] == 'self')) {
    if ($REQUEST['r'] != 'self' && $REQUEST['r'] != $preTally_user_id && $REQUEST['view'] != 1) {
        $US_RES_Pass = $US_WelcomeMail = 'button';
    }
    $US_Password_Type = $US_GEN_Pass = 'hidden';
    $US_EMPID_Type = 'template';
    $US_UName_Type = ($REQUEST['view'] == 1 || $REQUEST['r'] == 'self') ? 'template':'input';
    //$US_EMPID_Type = $US_UName_Type = 'template';
    $required = '';
    $LC_Id_Connector = "requisites/locations.php&amp;ofid=" . $UserDetails->OF_Id;
}
$passrules = ($US_Password_Type == "password" ) ? "NotEmpty" :""; // 04-12-2024

if ($US_DOJ_Type != 'calendar') {
    $US_DOJ = date('d/m/Y', strtotime($UserDetails->US_DOJ));
} else {
    $US_DOJ = $UserDetails->US_DOJ;
}
// Start Time time management section @ 23-07-2025
// check the lower than company time - set the expiry for that users 
$com_total_time     = round(abs(strtotime($cmpnySettings['CS_OfficeEnds']) - strtotime($cmpnySettings['CS_OfficeStart'])) / 60,2);
$user_total_time    = round(abs(strtotime($CSOffEnd) - strtotime($CSOffStrt)) / 60,2);
$US_Time_Reduced    = ($user_id > 0) ? (int)$UserDetails->US_Time_Reduced : 0;
$US_Grace_Temp      = ($user_id > 0) ? (int)$UserDetails->US_Grace_Temp : 1;
$maxGraceTime       = ($UserACLObj->approve_grace_time==1) ? $com_total_time:$cmpnySettings['CS_UserMaxGraceTime'];
// end checking 

echo '<items>
            <item type="settings" position="label-left" labelWidth="150" inputWidth="300" noteWidth="180" offsetLeft="20"/>

            <item type="label" label="Authentication Details" offsetTop="10" labelHeight="30" labelWidth="200">
                    <userdata>authentication</userdata>
            </item>

            <item type="label" label="Personal Details" offsetTop="10" labelWidth="250" labelHeight="30">
                    <userdata>personal</userdata>
            </item>

            <item type="label" label="Educational Details" offsetTop="10" labelHeight="30">
                    <userdata>educational</userdata>
            </item>

            <item type="label" label="Account Details" offsetTop="10" labelHeight="30">
                    <userdata>account</userdata>
            </item>

            <item type="label" label="Salary Details" offsetTop="10" labelHeight="30">
                    <userdata>salary</userdata>
            </item>

            <item type="hidden" name="US_Image" id="US_Image" value="' . $US_Image . '"/>

            <item 	type="template"
                value="&lt;img 
                        src		=\'uploads/profileImage/' . $US_Image . '\'
                        style	=\'position:absolute;width:150px;height:150px;left:550px;top:45px\'
                        id		=\'profileImage' . $user_id . '\'
                &gt; 
                &lt;div style=\'width:120px;position:absolute;left:575px;top:200px\'&gt;
                        &lt;a href=\'javascript:void(0);\' style=\'text-decoration:none;\' id=\'changeProfileImage' . $user_id . '\'&gt;Change&lt;/a&gt;                       
                        &lt;/div&gt;&lt;div id=\'uploadProgress' . $user_id . '\' style=\'width:120px;position:absolute;left:550px;top:220px\'&gt;&lt;/div&gt;">            >
                    <userdata>authentication</userdata>
            </item>
            <item type="hidden" value="' . $UserDetails->OF_Id . '" name="H_OF_Id" className="">
                    <userdata>all</userdata>
            </item>
            <item type="hidden" value="' . $UserDetails->LC_Id . '" name="H_LC_Id" className="">
                    <userdata>all</userdata>
            </item>

            <item type="hidden" value="' . $UserDetails->UT_Id . '" name="H_UT_Id" className="">
                    <userdata>all</userdata>
            </item>


            <item type="hidden" value="' . $UserDetails->CT_Id . '" name="H_CT_Id" className="">
                    <userdata>all</userdata>
            </item>                        
            <item type="hidden" value="' . $UserDetails->ST_Id . '" name="H_ST_Id" >
                    <userdata>all</userdata>
            </item>                        
            <item type="hidden" value="' . $UserDetails->CN_Id . '" name="H_CN_Id">
                    <userdata>all</userdata>
            </item>
            <item type="hidden" value="' . $UserDetails->US_Gender . '" name="H_US_Gender">
                    <userdata>all</userdata>
            </item>
            <item type="hidden" value="' . $UserDetails->US_Status . '" name="H_US_Status">
                    <userdata>all</userdata>
            </item>

            <item type="hidden" value="' . $UserDetails->DP_Id . '" name="H_DP_Id">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserDetails->DG_Id . '" name="H_DG_Id">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserDetails->ES_Id . '" name="H_ES_Id">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserDetails->US_MDate . '" name="HUS_MDate">
                    <userdata>all</userdata>
            </item> 
            
            <item type="hidden" value="' . $UserDetails->US_Report . '" name="H_US_Report_Id">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserObj->getUserName($UserDetails->US_Report) . '" name="H_US_Report_Name" >
                    <userdata>all</userdata>
            </item> 
            
            <item type="hidden" value="' . $UserDetails->SP_Id . '" name="H_SP_Id">
                    <userdata>all</userdata>
            </item>
            
            <item type="hidden" value="' . $UserDetails->SS_Id . '" name="H_SS_Id">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserDetails->US_GrossSal . '" name="H_US_GrossSal">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="0" name="H_SS_CFlag">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $CSOffStrt . '" name="US_LoginTime">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $CSOffEnd . '" name="US_LogoutTime">
                    <userdata>all</userdata>
            </item> 
            <item type="hidden" value="' . $UserObj->getResignStatsList($preTally_user_ofid) . '" name="H_ES_Resign">
                    <userdata>all</userdata>
            </item> 
            <item type="' . $OF_Id_Type . '" name="OF_Id" label="Company" value="' . $UserObj->getOfficeName($UserDetails->OF_Id) . '" ' . $validation_keys . ' connector="requisites/offices.php&amp;sibling_id='.$sibling_id.'" className="officeCombo">
                <note width="150">Company</note>
                <userdata>authentication</userdata>
            </item>';
            echo '<item type="' . $LC_Id_Type . '" name="LC_Id" label="Branch" value="' . $UserObj->getLocationName($UserDetails->LC_Id) . '" ' . $validation_keys . ' connector="' . $LC_Id_Connector . '" className="locateCombo">
                <option value="" label="Select Branch" selected="true" />
                <note width="150">Branch Location</note>
                <userdata>authentication</userdata>
            </item>';            
            if ($sibling_id > 0 && $loadsilbing == 1) { //28-03-2025
                echo '<item type="combo" name="Allow_OF_Work" label="Work Allowed Companies"  connector="requisites/siblings.php&amp;sibling_id='.$sibling_id.'&amp;user_id='.$user_id.'"   comboType="checkbox" className="allowoffcbo">
                    <note width="150">Work Allowed Companies</note>
                    <userdata>authentication</userdata>
                </item>
                <item type="hidden" name="allowed_office" value="" required="true"></item>';
            }
             echo '<item type="' . $US_UName_Type . '" name="US_UName" label="Employee Username" value="' . $UserDetails->US_UName . '" maxLength="20"'.(($US_UName_Type == "input") ? ' validate="NotEmpty"  required="true" ':'').' >
                <note width="315">Allow Letters &amp; digits. First character must be a letter. Length between 5 - 20.</note>
                <userdata>authentication</userdata>
            </item>
            <item type="' . $US_EMPID_Type . '" name="US_EMPID" label="Employee ID" value="' . $UserDetails->US_EMPID . '" required="true" >
                <note width="150">Employee ID(Unique)</note>
                <userdata>authentication</userdata>
            </item>
            <item type="' . $US_Password_Type . '" name="US_Password" label="User Password"  ' . $required . ' validate="'.$passrules.'" >
                <note width="315">Contain one digit, one small &amp; cap Letter &amp; a special character. Length (5-20).</note>
                <userdata>authentication</userdata>
            </item>
            <item type="' . $US_Type_Type . '" label="User Type" name="US_Type" value="' . $UserObj->getUserType($UserDetails->UT_Id) . '" ' . $validation_keys . ' className="UTCombo">
                <option value="" label="Select ACL Type" selected="true" />
                <note width="150">Priviliges Set on User Type</note>
                <userdata>authentication</userdata>
            </item>
            <item type="block" width="1300" offsetLeft="0">
             <item type="' . $US_Email_Type . '" name="US_Email" offsetLeft="0" label="Company Email" value="' . $UserDetails->US_Email . '" required="true" >
                    <note width="150">Company Email</note>
                    <userdata>authentication</userdata>
            </item>
            
            <item type="' . $US_FName_Type . '" name="US_FName" offsetLeft="0" label="First Name" value="' . $UserDetails->US_FName . '" required="true" validate="^[a-zA-Z ]+$">
                    <note width="150">First Name</note>
                    <userdata>authentication</userdata>
            </item>
            <item type="' . $US_LName_Type . '" name="US_LName" offsetLeft="0" label="Last Name" value="' . $UserDetails->US_LName . '" required="true" validate="^[a-zA-Z ]+$">
                    <note width="150">Last Name</note>
                    <userdata>authentication</userdata>
            </item>
            <item type="' . $US_DOJ_Type . '" name="US_DOJ" offsetLeft="0" label="Date Of Join" value="' . $US_DOJ . '" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" calendarPosition="right" tooltip= "Enter your date DOJ" required= "true"  ' . $US_DOJ_ReadOnly . ' >
                <note width="150">Date Of Join</note>
                <userdata>authentication</userdata>
            </item>            
            
            <item type="' . $US_Status_Type . '" label="User Status" offsetLeft="0" name="US_Status" ' . $US_Status_Required . '>
                    <option value="1" label="Published" selected="true" />
                    <option value="0" label="Blocked" selected="false" />
                    <note width="150">User Status</note>
                    <userdata>authentication</userdata>
            </item>
            <userdata>authentication</userdata>
            <item type="' . $DP_Id_Type . '" label="Department" offsetLeft="0" name="DP_Id" value="' . $UserObj->getDepartmentName($UserDetails->DP_Id) . '" ' . $validation_keys . ' className="DPCombo">
                 <option value="" label="Select Department" selected="true" />
                 <note width="150">Department</note>
                <userdata>authentication</userdata>
            </item>            
            <item type="' . $DG_Id_Type . '" label="Designation" offsetLeft="0" name="DG_Id" value="' . $UserObj->getDesignationName($UserDetails->DG_Id) . '" ' . $validation_keys . ' className="DGCombo">
                <option value="" label="Select Designation" selected="true" />
                <note width="150">Designation</note>
                <userdata>authentication</userdata>
            </item>
             
            <item type="' . $ES_Id_Type . '" name="ES_Id" offsetLeft="0" label="Employee Status" value="' . $UserObj->getEmployeeStatusName($UserDetails->ES_Id) . '" ' . $validation_keys . ' className="ESCombo">
            <option value="" label="Select Employee Status" selected="true" />
            <note width="150">Employee Status</note>
                    <userdata>authentication</userdata>                        
            </item>
            <item type="' . $ES_Dt_Type . '" name="ESH_Date" offsetLeft="0"    label="Effective From" value="' . $UserObj->getEmpStatusDate($user_id, $UserDetails->ES_Id,$ES_Dt_Type) . '" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" calendarPosition="right" tooltip= "Enter your date DOJ" required= "true"  ' . $US_DOJ_ReadOnly . ' >
                <note width="150">Employee Status Effective From</note>
                <userdata>authentication</userdata>
            </item>';
if (($UserObj->getUserOFId($UserDetails->US_Report) == $preTally_user_ofid) || ($preTally_user_ofid == 1) || ($REQUEST['r'] == 0)) {
    echo '<item type="' . $US_Report_Type . '" offsetLeft="0" name="US_Report" label="Reports To" value="' . $UserObj->getUserName($UserDetails->US_Report) . '"  filterCache="true"  ' . $validation_keys . ' className="RptCombo">
                            <note width="150">Reporting Official</note>
                            <userdata>authentication</userdata>
                          </item>';
}
echo '<item type="newcolumn"/>
                <item type="fieldset" label="Work Schedule" offsetLeft="20" width="225">
                    <userdata>authentication</userdata>
                    <item type="fieldset" label="Login" offsetLeft="0" width="215">                        
                        <userdata>authentication</userdata>
                        <item type="' . $US_WrkSchedule . '" label="Time" offsetLeft="5" name="login_Hr" labelWidth="50" inputWidth="50" value="' . $loginArr[0] . '">
                        <userdata>authentication</userdata>
                        </item>
                        <item type="newcolumn"/>                
                        <item type="' . $US_WrkSchedule . '" label="Min" offsetLeft="5" name="login_Min" labelWidth="0" inputWidth="50" value="' . $loginArr[1] . '">
                        <userdata>authentication</userdata>
                        </item>
                    </item> 
                     
                    <item type="fieldset" label="Logout" offsetLeft="0" width="215">
                        <userdata>authentication</userdata>
                        <item type="' . $US_WrkSchedule . '" label="Time" offsetLeft="5" name="logout_Hr" labelWidth="50" inputWidth="50" value="' . $logoutArr[0] . '">
                        <userdata>authentication</userdata>
                        </item>
                        <item type="newcolumn"/>                
                        <item type="' . $US_WrkSchedule . '" label="Min" offsetLeft="5" name="logout_Min" labelWidth="0" inputWidth="50" value="' . $logoutArr[1] . '">
                            <userdata>authentication</userdata>
                        </item>
                    </item> ';    
                if ( !empty($graceTimeDet) ) { //27-11-2025 grace time reason showing 
                    $dateString  = ($graceTimeDet->is_temporary == 1 && $graceTimeDet->end_date != "") ? "&lt;br&gt;&lt;b&gt;Valid Till:&lt;/b&gt; ".date('M d, Y', strtotime($graceTimeDet->end_date)) :"";   
                    $cuhourss   = (int)($graceTimeDet->work_time/60);
                    $cuminutes  = $graceTimeDet->work_time -  $cuhourss*60;
                    $cuminutes  = ($cuminutes < 10) ? "0".$cuminutes:$cuminutes;
                    $cuhourss   = ($cuhourss < 10) ? "0".$cuhourss:$cuhourss;
                    $workhours  = $cuhourss.":".$cuminutes;    
                    echo '<item type="fieldset" label="Adjusted Working Hours" offsetLeft="0" width="215">
                        <userdata>authentication</userdata>
                        <item type="template" offsetLeft="0" offsetTop="0" labelWidth="0"  width="166px;"                           
                            value="&lt;b&gt;Working Hours:&lt;/b&gt; '.$workhours.'&lt;br&gt;  &lt;b&gt;Reason: &lt;/b&gt; '.$graceTimeDet->reason.$dateString.' " >
                                <userdata>authentication</userdata>
                        </item></item>';
                }
            echo'</item> ';

            // start the time checking hidden fields 23-07-2025
            echo '<item type="hidden" value="' . $com_total_time . '" name="com_total_time">
                        <userdata>all</userdata>
                </item>
                <item type="hidden" value="' . $user_total_time . '" name="user_total_time">
                        <userdata>all</userdata>
                </item>
                <item type="hidden" value="' . $US_Time_Reduced . '" name="US_Time_Reduced">
                        <userdata>all</userdata>
                </item>
                <item type="hidden" value="' . $US_Grace_Temp . '" name="US_Grace_Temp_hid">
                    <userdata>all</userdata>
                </item>
                <item type="hidden" value="' . $maxGraceTime . '" name="Max_Grace_Time">
                    <userdata>all</userdata>
                </item>';
                $showhidegrace  = ($US_Time_Reduced==0 && $com_total_time > $user_total_time) ? 1:0;
            if ($REQUEST['r'] != 'self' && $REQUEST['view'] != 1) {
                echo '<item type="fieldset" label="Grace Time Details" name="usr_grase_time" offsetLeft="20" width="270">                    
                    <userdata>authentication</userdata>
                    <item offsetLeft="0" position="label-left" labelWidth="70" inputWidth="150" type="combo" label="Is Temporary" name="US_Grace_Temp">
                        <option value="1" label="Yes" '.(($US_Grace_Temp == 1) ? ' selected="true"':''). ' />
                        <option value="0" label="No" '.(($US_Grace_Temp == 0) ? ' selected="true"':''). ' />
                        <note width="150">Grace time exception was temporary</note>
                        <userdata>authentication</userdata>
                    </item>    
                    <item offsetLeft="0" position="label-left" labelWidth="70" inputWidth="150" type="calendar" name="Grace_End_Date" label="End Date" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" calendarPosition="right" readonly="true" required="false" >
                        <note width="150">Last Date of Grace Time Allowed</note>
                        <userdata>authentication</userdata>
                    </item>
                    <item type="newcolumn"/>
                    <item offsetLeft="0" position="label-left" labelWidth="70" inputWidth="150"  type="input" name="Grace_Reason" label="Reason" value="" rows="3" >
                        <note width="150">Grace Time Reason</note>
                        <userdata>authentication</userdata>
                    </item>

                </item>'; 
            } else {
                $showhidegrace = 0;
            }
            echo '<item type="hidden" value="' . $showhidegrace . '" name="ShowHideGrace">
                        <userdata>all</userdata>
                </item>';
            // end the time checking and set the hidden fields 23-07-2025
            
            echo '<item type="newcolumn"/>'; //23-10-2025
            echo '<item type="' . $StatHist_Type . '" label="Status History" offsetLeft="20" width="300">
                <userdata>authentication</userdata> 
                <item type="container"  label="Status History" labelWidth="0" name="empStatusGrid" offsetLeft="0" inputHeight="150">
                <userdata>authentication</userdata>                   
                </item>
                </item>';

            // Designation changes history 06-10-2025
            if (!empty($desigDet)) {
                echo '<item type="fieldset" label="Designation History"  offsetLeft="20" width="300">
                    <userdata>authentication</userdata> ';
                    echo '<item type="container"  label="Designation History" labelWidth="0" name="empDesgHGrid" offsetLeft="0" inputHeight="120" width="300"><userdata>authentication</userdata></item>';
                echo '</item>';
                $ShowHideDesgH = 1;
            } else {
                 $ShowHideDesgH = 0;
            }
            echo '<item type="hidden" value="' . $ShowHideDesgH . '" name="ShowHideDesgH">
                        <userdata>authentication</userdata>    
                </item>';
            // Designation History Ends.

            echo '</item>'; // block close
                
            echo '<item type="' . $US_DOB_Type . '" name="US_DOB" label="Date Of Birth"  serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" calendarPosition="right" value="' . $UserDetails->US_DOB . '" >
                    <note width="150">Date Of Birth</note>
                    <userdata>personal</userdata>
            </item>            
            <item type="' . $US_Gender_Type . '" label="Gender" name="US_Gender" value="' . $GendArray[$UserDetails->US_Gender] . '">
            <option value="0" label="Select Gender" />			
                <option value="1" label="Male" />
                <option value="2" label="Female" />
                <option value="3" label="Others" />
                <note width="150">User Gender</note>
                <userdata>personal</userdata>
            </item>

            <item type="' . $US_Address_Type . '" name="US_Address" label="Address" value="' . $UserDetails->US_Address . '" rows="2">
                <note width="150">Address</note>
                <userdata>personal</userdata>
            </item>

            <item type="' . $CN_Id_Type . '" name="CN_Id" label="Country" value="' . $UserObj->getCountryName($UserDetails->CN_Id) . '" comboType="image" connector="requisites/countries.php"  comboImagePath = "images/flags/" >
                <note width="150">Country</note>
                <userdata>personal</userdata>
            </item>

            <item type="' . $ST_Id_Type . '" name="ST_Id" label="State"  value="' . $UserObj->getStateName($UserDetails->ST_Id) . '">
                    <option value = "0" label="Select State" selected="selected"/>
                    <note width="150">State Title</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $CT_Id_Type . '" name="CT_Id" label="City" value="' . $UserObj->getCityName($UserDetails->CT_Id) . '">
            <option value = "0" label="Select City" selected="selected"/>
                    <note width="150">City</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Pemail1_Type . '" name="US_Pemail1" label="Personal Email" value="' . $UserDetails->US_Pemail1 . '"  validate="ValidEmail">
                    <note width="150">Personal Email Id</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Mobile1_Type . '" name="US_Mobile1" label="Mobile" value="' . $UserDetails->US_Mobile1 . '"  validate="ValidNumeric">
                    <note width="150">Mobile</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Landline1_Type . '" name="US_Landline1" label="Landline" value="' . $UserDetails->US_Landline1 . '"  validate="ValidNumeric">
                    <note width="150">Landline</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Relative_Type . '" name="US_Relative" label="Father/Spouse Name" value="' . $UserDetails->US_Relative . '" validate="^[a-zA-Z]+$">
                    <note width="155">Father/Spouse Name</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Blood_Type . '" name="US_Blood" label="Blood Group" value="' . $UserObj->getBloodGroup($UserDetails->BG_Id) . '" connector="requisites/bloodGroups.php&amp;r=' . $UserDetails->BG_Id . '" >
            <note width="150">Blood Group</note>
                    <userdata>personal</userdata>
            </item>

            <item type="' . $US_Guardian_Type . '" name="US_Guardian" label="Hostel/Local Guardian Name" value="' . $UserDetails->US_Guardian . '" validate="^[a-zA-Z]+$">
                    <note width="155">Hostel/Local Guardian</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Guardianphone_Type . '" name="US_Guardianphone" label="Hostel/Local Guardian Phone" value="' . $UserDetails->US_Guardianphone . '" validate="ValidNumeric">
                    <note width="155">Hostel/Local Guardian Phone</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Emergencyperson_Type . '" name="US_Emergencyperson" label="Emergency Contact Person" value="' . $UserDetails->US_Emergencyperson . '" validate="^[a-zA-Z ]+$">
                    <note width="155">Emergency Contact Person</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Emergencynumber_Type . '" name="US_Emergencynumber" label="Emergency Contact Number" value="' . $UserDetails->US_Emergencynumber . '" validate="ValidNumeric">
                    <note width="155">Emergency Contact Number</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Emergencyrelation_Type . '" name="US_Emergencyrelation" label="Emergency Contact Relation" value="' . $UserDetails->US_Emergencyrelation . '" validate="^[a-zA-Z]+$">
                    <note width="155">Emergency Contact Relation</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Passport_Type . '" name="US_Passport" label="Passport Number" value="' . $UserDetails->US_Passport . '" validate="ValidAplhaNumeric">
                    <note width="155">Passport Number</note>
                    <userdata>personal</userdata>
            </item>
           

            <item type="' . $US_Qualification_Type . '" name="US_Qualification" label="Highest Qualification" value="' . $UserDetails->US_Qualification . '" validate="^[a-zA-Z ]+$">
                    <note width="150">Highest Qualification</note>
                    <userdata>educational</userdata>
            </item>
            <item type="' . $US_Specialization_Type . '" name="US_Specialization" label="Specialization" value="' . $UserDetails->US_Specialization . '" validate="^[a-zA-Z ]+$">
                    <note width="150">Highest Qualification</note>
                    <userdata>educational</userdata>
            </item>

            <item type="' . $US_Experience_Type . '" name="US_Experience" label="Total Experience" value="' . $UserDetails->US_Experience . '" validate="^[a-zA-Z0-9. ]+$">
                    <note width="150">Total Experience in months</note>
                    <userdata>educational</userdata>
            </item>

            <item type="' . $US_LastEmployee_Type . '" name="US_LastEmployee" label="Last Company Details" value="' . $UserDetails->US_LastEmployee . '" rows="3" >
                    <note width="150">Last Company Details</note>
                    <userdata>educational</userdata>
            </item>           

            <item type="fieldset" label="Employee Bank Details" width="500">
                <userdata>account</userdata>
                <item type="' . $US_AccNo_Type . '" name="US_AccNo" labelWidth="100" inputWidth="280" label="Employee Account No:" value="' . $UserDetails->US_AccNo . '" validate="ValidNumeric">
                        <note width="150">Account No</note>
                        <userdata>account</userdata>
                </item>

                 <item type="' . $US_Bankname_Type . '" name="US_Bankname" labelWidth="100" inputWidth="280" label="Employee Bank Name:" value="' . $UserDetails->US_Bankname . '" validate="^[a-zA-Z ]+$">
                        <note width="150">Salary Account Bank Name</note>
                        <userdata>account</userdata>
                </item>
                 <item type="' . $US_BankBranch_Type . '" name="US_BankBranch" labelWidth="100" inputWidth="280" label="Employee Bank Branch:" value="' . $UserDetails->US_BankBranch . '" validate="^[a-zA-Z ]+$">
                        <note width="150">Salary Account Bank Branch</note>
                        <userdata>account</userdata>
                </item>
                <item type="' . $US_PFNo_Type . '" name="US_PFNo" labelWidth="100" inputWidth="280" label="Employee PF No:" value="' . $UserDetails->US_PFNo . '" validate="ValidAplhaNumeric">
                        <note width="150">PF No</note>
                        <userdata>account</userdata>
                </item>

                <item type="' . $US_ESI_Type . '" name="US_ESI" labelWidth="100" inputWidth="280" label="Employee ESI No:" value="' . $UserDetails->US_ESI . '" validate="ValidAplhaNumeric">>
                        <note width="150">ESI No</note>
                        <userdata>account</userdata>
                </item>

                <item type="' . $US_PAN_Type . '" name="US_PAN" labelWidth="100" inputWidth="280" label="Employee PAN No:" value="' . $UserDetails->US_PAN . '" validate="ValidAplhaNumeric">
                        <note width="150">PAN No</note>
                        <userdata>account</userdata>
                </item>
            </item>
           
        <item type="' . $SS_Id_Type . '" name="SS_Id" label="Salary Structure"  inputWidth="150" className="SSCombo">
            <option value="0" label="Select Salary Structure" selected="true" />
            <note width="100">Salary Structure</note>
            <userdata>salary</userdata>
        </item>
<item type="block" width="900">            
<userdata>salary</userdata>
        <item type="fieldset" label="Salary" width="380" offsetLeft="5">
        <userdata>salary</userdata>
            
            <item type="' . $US_GrossSal_Type . '" name="US_GrossSal" offsetLeft="0" label="Gross Salary" value="' . $UserDetails->US_GrossSal . '" inputWidth="100"  className="salgross" validate="ValidNumeric">
            <note width="100">Gross Salary</note>
            <userdata>salary</userdata>
            </item>            
            <item type="' . $H_Gross_Type . '" name="H_GrossSal" label="%" value="" inputWidth="30"  labelWidth="15" inputLeft="0" >
            <note width="15">Percent</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $US_BasicSal_Type . '" name="US_BasicSal" offsetLeft="0" label="Basic + DA" value="' . $UserDetails->US_BasicSal . '" inputWidth="100" ' . $US_DOJ_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">Basic + DA</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_BasicSal_Type . '" name="H_BasicSal" label="%" value="" inputWidth="30"  labelWidth="15" >
            <note width="15">Percent</note>
            <userdata>salary</userdata>
            </item>';
//            echo'<item type="'.$US_DaSal_Type.'" name="US_DaSal"  offsetLeft="0" label="DA" value="'.$UserDetails->US_DASal.'" inputWidth="100" className="salbasic" validate="ValidNumeric">
//            <note width="100">DA</note>
//            <userdata>salary</userdata>
//            </item>
//            <item type="'.$H_DaSal_Type.'" name="H_DaSal" label="%" value="" inputWidth="30"  labelWidth="15" readonly="true">
//            <userdata>salary</userdata>
//            </item>';
echo '<item type="' . $US_HraSal_Type . '" name="US_HraSal"  offsetLeft="0" label="HRA" value="' . $UserDetails->US_HRASal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">HRA</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_HraSal_Type . '" name="H_HraSal"  label="%" value="" inputWidth="30"  labelWidth="15" >
            <userdata>salary</userdata>
            </item>
            <item type="' . $US_CcaSal_Type . '" name="US_CcaSal"  offsetLeft="0" label="CCA" value="' . $UserDetails->US_CcaSal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">City Compensatory Allowance</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_CcaSal_Type . '" name="H_CcaSal" label="%" value="" inputWidth="30"  labelWidth="15" >
            <userdata>salary</userdata>
            </item>
            <item type="' . $US_ConveySal_Type . '" name="US_ConveySal"  offsetLeft="0" label="Conveyance" value="' . $UserDetails->US_ConveySal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">Conveyance</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_ConveySal_Type . '" name="H_ConveySal" label="%" value="" inputWidth="30"  labelWidth="15" >
            <userdata>salary</userdata>
            </item>
            <item type="' . $US_EduSal_Type . '" name="US_EduSal"  offsetLeft="0" label="Education Allowance" value="' . $UserDetails->US_EduSal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">Education Allowance</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_EduSal_Type . '" name="H_EduSal" label="%" value="" inputWidth="30"  labelWidth="15"  validate="ValidNumeric">
            <userdata>salary</userdata>
            </item> 
            <item type="' . $US_MedSal_Type . '" name="US_MedSal"  offsetLeft="0" label="Medical Allowance" value="' . $UserDetails->US_MedSal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">Medical Allowance</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_MedSal_Type . '" name="H_MedSal" label="%" value="" inputWidth="30"  labelWidth="15"  validate="ValidNumeric">
            <userdata>salary</userdata>
            </item> 
            <item type="' . $US_MiscSal_Type . '" name="US_MiscSal"  offsetLeft="0" label="Other Allowance" value="' . $UserDetails->US_MiscSal . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
            <note width="100">Other Allowance</note>
            <userdata>salary</userdata>
            </item>
            <item type="' . $H_MiscSal_Type . '" name="H_MiscSal" label="%" value="" inputWidth="30"  labelWidth="15"  validate="ValidNumeric">                    
            <userdata>salary</userdata>
            </item>
        </item>
        <item type="newcolumn" offset="5">            
            <userdata>salary</userdata>
        </item>
            <item type="fieldset" width="450" label="Deductions">
            <userdata>salary</userdata>
                <item type="' . $US_DedEPF_Type . '"  name="US_DedEPF"  offsetLeft="0" label="EPF" value="' . $UserDetails->US_DedEPF . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
                        <note width="150">Employee Provident Fund</note>
                        <userdata>salary</userdata>
                </item>
                <item type="' . $US_DedESI_Type . '"  name="US_DedESI"  offsetLeft="0" label="ESI" value="' . $UserDetails->US_DedESI . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
                        <note width="150">Employee State Insurance</note>
                        <userdata>salary</userdata>
                </item>
                <item type="' . $US_DedLWF_Type . '"  name="US_DedLWF"  offsetLeft="0" label="LWF" value="' . $UserDetails->US_DedLWF . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
                        <note width="150">Labour Welfare Fund</note>
                        <userdata>salary</userdata>
                </item>
                <item type="' . $US_DedProfTDS_Type . '"  name="US_DedProfTDS"  offsetLeft="0" label="Prof TDS" value="' . $UserDetails->US_DedProfTDS . '" inputWidth="100" ' . $US_Sal_ReadOnly . ' className="salbasic" validate="ValidNumeric">
                        <note width="150">Professional TDS</note>
                        <userdata>salary</userdata>
                </item>
                 <item type="' . $US_DedSalTDS_Type . '"  name="US_DedSalTDS"  offsetLeft="0" label="Salary TDS" value="' . $UserDetails->US_DedSalTDS . '" inputWidth="100"  validate="ValidNumeric">
                        <note width="150">Salary TDS</note>
                        <userdata>salary</userdata>
                </item>
                <item type="' . $US_DedMealCard_Type . '"  name="US_DedMealCard"  offsetLeft="0" label="Meal Card" value="' . $UserDetails->US_DedMealCard . '" inputWidth="100"  validate="ValidNumeric">
                        <note width="150">Meal Card</note>
                        <userdata>salary</userdata>
                </item>
            </item>
            <item type="fieldset" label="Payment Mode" width="450">
                <item type="' . $US_PaymodeType . '" name="SP_Id" value="' . $UserObj->getUserSalPayMode($UserDetails->SP_Id) . '" label="Payable through " labelWidth="150" inputWidth="180" ' . $Paymode_ReadOnly . ' ' . $Paymode_Connector . ' ' . $validation_keys . '>
                    <note width="150">Mode of salary transfer</note>
                    <userdata>salary</userdata>
                </item>
                <userdata>salary</userdata>
            </item>
            
            <item type="fieldset" label="Company Bank Details" name="Cmp_BankDetails" width="450">
                <userdata>salary</userdata>
                <item type="' . $US_SalBankName . '" name="Bank_AC" value="' . $UserObj->getUserSalBank($UserDetails->Sal_BnkId) . '" label="Company Bank Name" ' . $SalBank_ReadOnly . ' ' . $SalBank_Connector . ' labelWidth="150" inputWidth="180">
                    <note width="150">Company Bank Accounts</note>
                    <userdata>salary</userdata>
                </item>
            </item>
            
</item>            

            <item type="block" width="650">
            <item type="hidden" value="' . $user_id . '" name="US_ID"></item>

                <item type="' . $US_Save_Btn . '" value="Save" name="newUserValidate">
                    <userdata>all</userdata>
                </item>
                <item type="newcolumn">
                    <userdata>all</userdata>
                </item>
                <item type="' . $US_Cnc_Btn . '" value="Cancel" name="newUserCancel">
                    <userdata>all</userdata>
                </item>
                <item type="newcolumn">
                    <userdata>all</userdata>
                </item>
                <item type="' . $US_WelcomeMail . '" value="Sent Password Reset Mail" name="doSentWelcomeMail">
                    <userdata>all</userdata>
                </item>
                <item type="newcolumn">
                    <userdata>all</userdata>
                </item>
                <item type="' . $US_RES_Pass . '" value="Reset Password" name="resetUserPassword">
                    <userdata>all</userdata>
                </item>
                <userdata>all</userdata>
            </item>';

            

             echo '<item type="newcolumn" offset="-40" width="150" />

            <item type="hidden"  name="GEN_Pass" value="" className="genpass"  offsetTop="195" tooltip= "Generate Passowrd" labelWidth="0">
                <userdata>authentication</userdata>
            </item>
            
            <item type="' . $US_Pemail2_Type . '"  name="US_Pemail2" label="Alternate Email Id" value="' . $UserDetails->US_Pemail2 . '"  validate="ValidEmail" offsetTop="368" labelWidth="' . $alt_width . '">
                    <note width="150">Alternate Email Id</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Mobile2_Type . '" name="US_Mobile2" label="Alternate Mobile" value="' . $UserDetails->US_Mobile2 . '"  validate="ValidNumeric"  labelWidth="' . $alt_width . '">
                    <note width="150">Alternate Mobile</note>
                    <userdata>personal</userdata>
            </item>
            <item type="' . $US_Landline2_Type . '" name="US_Landline2" label="Alternate Landline"  value="' . $UserDetails->US_Landline2 . '"  validate="ValidNumeric"  labelWidth="' . $alt_width . '">
                    <note width="150">Alternate Landline</note>
                    <userdata>personal</userdata>
            </item>';


echo ' </items>';
?>