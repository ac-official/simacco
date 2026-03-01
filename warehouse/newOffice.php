<?php
require_once($BASEPATH . "includes/_define.php");
require_once($BASEPATH . "smtp/smtpMail.php");

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/ACLClass.php");
require_once($BASEPATH . "preTallyClass/DepartmentClass.php");
require_once($BASEPATH . "preTallyClass/DesignationClass.php");
require_once($BASEPATH . "preTallyClass/EmployeeStatusClass.php");
require_once($BASEPATH . "preTallyClass/SalStructClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/AddressClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "includes/saveGoogleAddress.php");
$OfficeObj      = new OfficeClass();
$LocationObj    = new LocationClass();
$ACLObj         = new ACLClass();
$DepartmentObj  = new DepartmentClass();
$DesignationObj = new DesignationClass();
$EmpStatusObj   = new EmployeeStatusClass();
$SalStructObj   = new SalStructClass();
$UserObj        = new UserClass();
$AddressObj     = new AddressClass();
$ITObj          = new ItemClass();

 $addressArray = array(
    'GOGL_Country'	=> trim(htmlspecialchars($_REQUEST['GOGL_Country'], ENT_QUOTES)),
    'GOGL_State'	=> trim(htmlspecialchars($_REQUEST['GOGL_State'], ENT_QUOTES)),
    'GOGL_City'         => trim(htmlspecialchars($_REQUEST['GOGL_City'], ENT_QUOTES)),
    'GOGL_Location'	=> trim(htmlspecialchars($_REQUEST['GOGL_Location'], ENT_QUOTES)),
    'GOGL_Place'	=> trim(htmlspecialchars($_REQUEST['GOGL_Place'], ENT_QUOTES)),
    'GOGL_Street'	=> trim(htmlspecialchars($_REQUEST['GOGL_Street'], ENT_QUOTES)),
);
$addressIds = json_decode(saveGoglAddress($addressArray));
/*
if(!is_numeric(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES)))
{   
        $AddressObj ->AD_Data = array(
            'AP_Name'   => trim(htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES)),
            'CT_Id'     => htmlspecialchars($_REQUEST['CT_Id'], ENT_QUOTES),
            'AP_Status' => 1,
            'AP_CDate'  => date('Y-m-d H:i:s'),
            'AP_MDate'  => date('Y-m-d H:i:s')
        );
        
       $LocId = $AddressObj->addPlaces();

}
else
{
    $LocId=htmlspecialchars($_REQUEST['AP_Id'], ENT_QUOTES);
}*/

$OfficeObj->OF_Data = array(
        'US_Id' 	=> $preTally_user_id,
        'OF_Name' 	=> trim(htmlspecialchars($_REQUEST['OF_Name'], ENT_QUOTES)),
        'OF_Building'   => htmlspecialchars($_REQUEST['OF_Building'], ENT_QUOTES),
        'OF_Street'     => htmlspecialchars($_REQUEST['OF_Street'], ENT_QUOTES),           
        'OF_Pincode'	=> htmlspecialchars($_REQUEST['GOGL_Pincode'], ENT_QUOTES),
        'ALC_Id'        => $addressIds->ALC_Id,
        'PL_Id'         => $addressIds->PL_Id,        
        'SR_Id'         => $addressIds->SR_Id, 
        'CT_Id'         => $addressIds->CT_Id, 
        'ST_Id'         => $addressIds->ST_Id, 
        'CN_Id'         => $addressIds->CN_Id, 
        'CR_Id'         => htmlspecialchars($_REQUEST['CR_Id'], ENT_QUOTES),        
        'TZ_Id'         => htmlspecialchars($_REQUEST['TZ_Id'], ENT_QUOTES),
        'OF_Parent'     =>0,
        'OF_Comments'	=> htmlspecialchars($_REQUEST['OF_Comments'], ENT_QUOTES),
        'OF_Status' 	=> htmlspecialchars($_REQUEST['OF_Status'], ENT_QUOTES),
        'OF_MDate' 	=> date('Y-m-d H:i:s')
);

if( $UserObj->verifyCompanyEmail(htmlspecialchars($_REQUEST['US_Email'], ENT_QUOTES)) ) {

        if( $OfficeObj->verifyOffice(htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES)) ) {
                if(htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES) == 0) {
                        $OfficeObj->OF_Data["OF_CDate"] = date('Y-m-d H:i:s'); 
                        $OfficeObj->OF_Data["OF_Admin"] = 0;
                        $OFId = $OfficeObj->newOffice();

                        $LocationObj->LC_Data = array(
                                'US_Id' 	=> $preTally_user_id,
                                'LC_Name' 	=> 'Corporate Office',
                                'OF_Id' 	=> $OFId,
                                'LC_Building'   => htmlspecialchars($_REQUEST['OF_Building'], ENT_QUOTES),
                                'LC_Street'     => htmlspecialchars($_REQUEST['OF_Street'], ENT_QUOTES),
                                'AP_Id'         => 0,  
                                'LC_Phone'      => "00000000",
                                'LC_Pincode'	=> htmlspecialchars($_REQUEST['GOGL_Pincode'], ENT_QUOTES),
                                'ALC_Id'        => $addressIds->ALC_Id,
                                'PL_Id'         => $addressIds->PL_Id,        
                                'SR_Id'         => $addressIds->SR_Id, 
                                'CT_Id'         => $addressIds->CT_Id, 
                                'ST_Id'         => $addressIds->ST_Id, 
                                'CN_Id'         => $addressIds->CN_Id, 
                                'LC_Status' 	=> 1,
                                'LC_CDate' 	=> date('Y-m-d H:i:s'),
                                'LC_MDate' 	=> date('Y-m-d H:i:s')           
                        );                
                        $LCId = $LocationObj->newLocation();
                        
                        $LocationObj->Bal_Data = array(        
                            'OF_Id'             => $OFId,        	
                            'LC_Id'             => $LCId,        
                            'OB_OpenBal'        => 0,
                            'OB_Status' 	=> 0,        
                            'OB_Date'           => date('Y-m-d H:i:s')
                        );	 	 	 	 	 	
                        $LocationObj->createOpeningBalances();
                        
                        $LocationObj->OS_Data = array(        
                            'OF_Id'         => $preTally_user_ofid,        	
                            'LC_Id'         => $lcid,        
                            'OS_OpenBal'    => 0,
                            'OS_Status'     => 0,        
                            'OS_Date'       => date('Y-m-d H:i:s')
                        );	 	 	 	 	 	
                        $LocationObj->createStockOpeningBalances();

                        $ACLObj->ACL_Data = array(
                                'ACL_Name'          => 'Company Admin',
                                'US_Id'             => $preTally_user_id,
                                'OF_Id'             => $OFId,
                                'ACL_HR' 		=> 4,
                                'ACL_HR_VM'         => 1,
                                'ACL_BSheet'        => 4,
                                'ACL_BSheet_VM'     => 1,
                                'ACL_BankReports'   => 1,	
                                'ACL_MasterReports' => 1,	
                                'ACL_CashReports'   => 1,	
                                'ACL_MISReports'    => 1,
                                'ACL_User'          => 1,
                                'ACL_ListUser'      => 1,
                                'ACL_Attendance'    => 1,
                                'ACL_AttendanceEdt' => 1,	
                                'ACL_ApproveLeave'  => 1,	
                                'ACL_SalDetail'     => 1,
                                'ACL_BnkDetail'     => 1,
                                'ACL_Payroll'       => 1,
                                'ACL_PayrollEdt'    => 1,
                                'ACL_State'         => 0,
                                'ACL_City'          => 0,
                                'ACL_Company'       => 0,
                                'ACL_Branch'        => 1,
                                'ACL_Dept'          => 1,
                                'ACL_Desig'         => 1,
                                'ACL_MH'            => 0,
                                'ACL_SH'            => 0,
                                'ACL_Item'          => 1,
                                'ACL_Description'   => 1,	
                                'ACL_NotifyQueue'   => 0,	
                                'ACL_Unit'          => 0,
                                'ACL_Paymode'       => 0,
                                'ACL_SalStruct'     => 1,
                                'ACL_SalPayMode'    => 0,
                                'ACL_Access'        => 1,
                                'ACL_SidebarMH'     => 1,
                                'ACL_SidebarOFF'    => 1,
                                'ACL_ManageTracks'  => 1,	
                                'ACL_DeleteEntries' => 1,	
                                'ACL_ManageBSDate'  => 1,	
                                'ACL_Track'         => 0,
                                'ACL_TrackInvReceipt' =>0,
                                'ACL_NotfLC'        => 1,
                                'ACL_NotfBNK'       => 1,
                                'ACL_Att_Master'    => 1,
                                'ACL_FeedbackRpt'   => 1,
                                'ACL_SalPMwiseBranch' => 1,
                                'ACL_SalPMwiseAll'  => 1,	
                                'ACL_ManageBusinessAmt' => 1,	 
                                'ACL_Status'        =>1,
                                'ACL_CDate'         => date('Y-m-d H:i:s'),
                                'ACL_MDate'         => date('Y-m-d H:i:s')
                            );
                       $UTId = $ACLObj->newACL();
                       

                        $DepartmentObj->DP_Data = array(
                                'US_Id' 	=> $preTally_user_id,
                                'OF_Id'         => $OFId,
                                'DP_Name' 	=> 'Managment Department',
                                'DP_Comments'	=> ' ',
                                'DP_Status' 	=> 1,
                                'DP_CDate' 	=> date('Y-m-d H:i:s'),
                                'DP_MDate' 	=> date('Y-m-d H:i:s')
                        );
                        $DPId = $DepartmentObj->newDepartment();

                        $DesignationObj->DG_Data = array(
                                'US_Id'         => $preTally_user_id,
                                'OF_Id'         => $OFId,
                                'DG_Name' 	=> 'Administrator',
                                'DG_Comments'	=> ' ',
                                'DG_Status' 	=> 1,
                                'DG_CDate' 	=> date('Y-m-d H:i:s'),
                                'DG_MDate' 	=> date('Y-m-d H:i:s')
                        ); 
                        $DGId = $DesignationObj->newDesignation();
                        $EmpStatusObj->ES_Data = array(
                            'OF_Id'     => $OFId,
                            'ES_Name'   => 'Confirmed',
                            'ES_Status' => 0
                        );
                        $ESId = $EmpStatusObj->newEmployeeStatus();
                        $SalStructObj->SalStruct_Data = array(        
                                'SS_Name'       => 'Salary Structure 1',        
                                'OF_Id'         => $OFId,
                                'SS_Basic'	=> 0,
                                'SS_DA'         => 0,
                                'SS_HRA'	=> 0,
                                'SS_Convey'	=> 0,
                                'SS_Edu'        => 0,
                                'SS_Medic'	=> 0,
                                'SS_Misc'	=> 0,
                                'SS_CFlag'      => 0,
                                'SS_DedESI'     => 0,
                                'SS_DedESI_Type'=> 0,
                                'SS_DedEPF'     => 0,
                                'SS_DedEPF_Type'=> 0,
                                'SS_DedLWF'     => 0,
                                'SS_DedLWF_Type'=> 0,
                                'SS_Status'     => 1,
                                'SS_CDate' 	=> date('Y-m-d H:i:s'),
                                'SS_MDate' 	=> date('Y-m-d H:i:s')
                        );
                        $SSId = $SalStructObj->newSalStruct();
                        $UsrPwd =  $UserObj->randomPassword();
                        $UserObj->US_AuthData = array(
                            'OF_Id'         =>  $OFId,
                            'LC_Id'         =>  $LCId,
                            'US_EMPID'      =>  htmlspecialchars($_REQUEST['US_Email'], ENT_QUOTES),
                            //'US_Password'   =>  md5($UsrPwd),
                            'US_Password'   =>  md5('admin'),
                            'UT_Id'         =>  $UTId,
                            'US_FName'      =>  htmlspecialchars($_REQUEST['US_FName'], ENT_QUOTES),
                            'US_LName'      =>  htmlspecialchars($_REQUEST['US_LName'], ENT_QUOTES),
                            'US_DOJ'        =>  date('Y-m-d H:i:s'),
                            'DP_Id'         =>  $DPId,
                            'DG_Id'         =>  $DGId,
                            'ES_Id'         =>  $ESId,
                            'US_Email'      =>  htmlspecialchars($_REQUEST['US_Email'], ENT_QUOTES),
                            'US_Report'     =>  1,
                            'US_Status'     =>  1,
                            'US_Image'      =>  'avatar.png',
                            'US_CDate'      =>  date('Y-m-d H:i:s'),
                            'US_MDate'      =>  date('Y-m-d H:i:s')
                        );
                        $UserObj->US_PersonalData   = array(
                            'US_Gender'             => 0,
                            'US_DOB'                => '',
                            'US_Address'            => '',        
                            'US_Pemail1'            => '',
                            'US_Pemail2'            => '',
                            'US_Mobile1'            => '',
                            'US_Mobile2'            => '',
                            'US_Landline1'          => '',
                            'US_Landline2'          => '',
                            'CT_Id'                 => 0,
                            'ST_Id'                 => 0,
                            'CN_Id'                 => 0,
                            'US_Relative'           => '',
                            'BG_Id'                 => 0,
                            'US_Guardian'           => '',
                            'US_Guardianphone'      => '',
                            'US_Emergencyperson'    => '',
                            'US_Emergencynumber'    => '',
                            'US_Emergencyrelation'  => '',
                            'US_Passport'           => ''
                        );
                        $UserObj->US_QualData       = array( 
                            'US_Qualification'      => '',
                            'US_Specialization'     => '',
                            'US_Experience'         => '',
                            'US_LastEmployee'       => ''
                        );
                        $UserObj->US_AccountData    = array(
                            'US_PAN'                => '',
                            'SP_Id'                 => 0,
                            'US_AccNo'              => '',
                            'US_Bankname'           => '',
                            'US_BankBranch'         => '',
                            'US_PFNo'               => '',
                            'US_ESI'                => '',
                            'BA_Id'                 => 0 
                        );
                        $UserObj->US_SalaryData    = array(
                            'SS_Id'                 =>  $SSId,
                            'US_GrossSal'           =>  0,
                            'US_DaSal'              =>  0,
                            'US_HraSal'             =>  0,
                            'US_ConveySal'          =>  0,
                            'US_EduSal'             =>  0,
                            'US_MedSal'             =>  0,
                            'US_MiscSal'            =>  0,
                            'US_BasicSal'           =>  0,
                            'US_DedESI'             =>  0,
                            'US_DedEPF'             =>  0,
                            'US_DedLWF'             =>  0                            
                        );                           
                        $US_Id = $UserObj->newUser();
                        
                        $ITObj->IT_Data = array(
                            'IC_Map'		=> "[]",
                            'OF_Id'             => $preTally_user_ofid,
                            'IC_CDate' 		=> date('Y-m-d H:i:s'),
                            'IC_MDate' 		=> date('Y-m-d H:i:s'),
                            'IC_Status' 	=> 1
                        );
                        $ITObj->newMapItemCompany();
                        
                        $OfficeObj->OF_Data["OF_Admin"] = $US_Id;
                        $OfficeObj->updateOffice($OFId);
                        echo "Company Created Successfully";
                        //$mail->addAddress(htmlspecialchars($_REQUEST['US_Email'], ENT_QUOTES),  htmlspecialchars($_REQUEST['US_FName'], ENT_QUOTES));
//                        $mail->addAddress('youremail@gmail.com', 'Your Name');
//                        $mail->Subject 	= 'PreTally, User Registration Mail';
//
//                        $mailContent 	= file_get_contents('../mailTemplate/newUser.html');
//
//                        $find			= array("{path}", "{user}", "{user_name}", "{password}", "{base_path}");
//                        $replace		= array(constant("BASE_PATH")."/mailTemplate/images", $_REQUEST['US_FName'].$_REQUEST['US_LName'], $_REQUEST['US_FName'], $UsrPwd, constant("BASE_PATH"));
//
//                        $mailContent 	= str_replace($find, $replace, $mailContent);
//
//                        $mail->msgHTML($mailContent, dirname(__FILE__));
//                        if (!$mail->send()) {
//                                echo "Mailer Error: " . $mail->ErrorInfo;
//                        } else {
//                                echo "Welcome Mail Sent Successfully..";
//                        }
                }
                            
        } else {
                echo 'fail';
                
        }
} else if($_REQUEST['US_Id'] != 0){
                $UserObj->US_AuthData = array(
                    'US_FName'      =>  htmlspecialchars($_REQUEST['US_FName'], ENT_QUOTES),
                    'US_LName'      =>  htmlspecialchars($_REQUEST['US_LName'], ENT_QUOTES),
                    'US_MDate'      =>  date('Y-m-d H:i:s')                 
                );
                $UserObj->updateUser($_REQUEST['US_Id']);
                $OfficeObj->OF_Data["OF_Admin"] = $_REQUEST['US_Id'];
                echo $OfficeObj->updateOffice(htmlspecialchars($_REQUEST['OF_Id'], ENT_QUOTES));
}else { echo 'false'; }
?>