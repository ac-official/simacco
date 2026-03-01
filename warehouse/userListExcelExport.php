<?php
require_once($BASEPATH .'includes/functions.php');
require_once($BASEPATH ."preTallyClass/ExportExcelClass.php");
$ExcelObj         = new ExportExcelClass();
$likeFilter="";
$filterValues = $REQUEST['filter'];
if($filterValues['userIDFilter']!=""){
    $userIDFilter= trim($filterValues['userIDFilter']);
    $likeFilter.=" AND USAUTH.US_EMPID LIKE '".$userIDFilter."%'";
}
if($filterValues['nameFilter']!=""){
    $nameFilter=trim($filterValues['nameFilter']);
    $likeFilter.=" AND USAUTH.US_FName LIKE '".$nameFilter."%'";
}
if($filterValues['AclTypeFilter']!=""){
    $AclTypeFilter=$filterValues['AclTypeFilter'];
    $likeFilter.=" AND  USAUTH.UT_Id  =". $AclTypeFilter;
}
if($filterValues['DesignationFilter']!=""){
    $DesignationFilter=$filterValues['DesignationFilter'];   
    $likeFilter.=" AND  DG.DG_Id  = '".$DesignationFilter."'";   
}
if($filterValues['locationFilter']!=""){
    $locationFilter=trim($filterValues['locationFilter']);
    $likeFilter.=" AND  LC.LC_Id  = ".$locationFilter; 
}
if($filterValues['departmentFilter']!=""){
   $departmentFilter=$filterValues['departmentFilter'];
    $likeFilter.=" AND  DP.DP_Id = '".$departmentFilter."'";
    
}
if($filterValues['empstatusFilter']!=""){   
    $empstatusFilter=$filterValues['empstatusFilter'];    
    $likeFilter.=" AND  ES.ES_Id=".$empstatusFilter;
}
if($filterValues['ApprovedBlockedFilter']!="All" && $filterValues['ApprovedBlockedFilter'] != ""){    
        $ApprovedBlockedFilter=$filterValues['ApprovedBlockedFilter'];
        $likeFilter.=" AND  USAUTH.US_Status=".$ApprovedBlockedFilter;
}
if($filterValues['skipUserFilter']!="All" && $filterValues['skipUserFilter'] != ""){    
        $skipUserFilter=$filterValues['skipUserFilter'];
        $likeFilter.=" AND USAUTH.US_AttndFlag =".$skipUserFilter;
}
$isBasic        = (isset($filterValues['isBasic'])) ? (int)$filterValues['isBasic'] :0; //20-03-25
//$filterUSR        = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
$filterUSR      = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', 5, 20, 146, 36);
$ExcelObj->viewUserGrid($filterUSR,36,$likeFilter);

$COC_Obj        = $ExcelObj->UserArray;
if ($isBasic == 1) {

    $headerArray    = ["name"=>"Name Of Staff", "id"=>"Simmacco Id", "doj"=>"Date Of Joining", 'email'=>"Email Id", "grosssal"=>"Salary", "location"=>"Location", "wtime"=>"Work Schedule","whours"=>"Work Hours"];
} else {

    $headerArray                    = array();
    $headerArray['id']              = "User ID";
    $headerArray['name']            = "Name";
    $headerArray['aclType']         = "ACL Type";
    $headerArray['department']      = "Department";
    $headerArray['designation']     = "Designation";
    $headerArray['location']        = "Location";
    $headerArray['phone']           = "Phone";
    $headerArray['report']          = "Reporting Person";
    $headerArray['doj']             = "Date OF Join";
    $headerArray['dateofbirth']     = "Date Of Birth";
    $headerArray['address']         = "Address";
    $headerArray['gender']          = "Gender";
    $headerArray['country']         = "Country";
    $headerArray['state']           = "State";
    $headerArray['city']            = "City";
    $headerArray['email']           = "Email";
    $headerArray['guardian']        = "Name of Guardian";
    $headerArray['guardianmobile']  = "Number of Guardian";
    $headerArray['blood']           = "Blood Group";
    $headerArray['relative']        = "Name of Relative";
    $headerArray['nameemergency']   = "Emergency Person's Name";
    $headerArray['numberemergency'] = "Emergency number";
    $headerArray['qualification']   = "Qualification";
    $headerArray['specialization']  = "Specialization";
    $headerArray['experience']      = "Experience";
    $headerArray['lastemployee']    = "Last Company";
    $headerArray['passport']        = "Passport";
    $headerArray['pan']             = "PAN No";
    $headerArray['spname']          = "Salary Pay Mode";
    $headerArray['accno']           = "AccountNo";
    $headerArray['bankname']        = "Bank";
    $headerArray['branch']          = "Branch Name";
    $headerArray['pf']              = "PF";
    $headerArray['esi']             = "ESI";
    $headerArray['salary']           = "Salary";
    $headerArray['grosssal']         = "Gross Salary";
    $headerArray['basicsal']         = "Basic Salary";
    $headerArray['dasal']            = "DA Salary";
    $headerArray['hrasal']           = "HRA Salary";
    $headerArray['ccasal']           = "CCA Salary";
    $headerArray['conveysal']        = "Convey Salary";
    $headerArray['edusal']           = "Edu Salary";
    $headerArray['medsal']           = "Medical Salary";
    $headerArray['mescsal']          = "Mesc Salary";
    $headerArray['empStatus']        = "Employee Status";
    $headerArray['status']           = "Status";    
}    
foreach($COC_Obj as $rw) {
    $arrayStr      = array();
    if ($isBasic == 1) { //20-03-2025

        $arrayStr['name']           =   $rw->US_FName." ".$rw->US_LName;
        $arrayStr['id']             =   $rw->US_EMPID;
        $arrayStr['doj']            =   date("d-M-Y",strtotime($rw->US_DOJ));
        $arrayStr['email']          =   $rw->US_Pemail1;
        $arrayStr['grosssal']       =   $rw->US_GrossSal;
        $arrayStr['location']       =   $rw->LC_Name;
        $arrayStr['wtime']          =   $rw->US_LoginTime." - ".$rw->US_LogoutTime;
        if ($rw->US_WrkHours == 540) {
            $workhours  = '09:00';
        } else {
            $cuhourss   = (int)($rw->US_WrkHours/60);
            $cuminutes  = $rw->US_WrkHours -  $cuhourss*60;
            $cuminutes  = ($cuminutes < 10) ? "0".$cuminutes:$cuminutes;
            $cuhourss   = ($cuhourss < 10) ? "0".$cuhourss:$cuhourss;
            $workhours  = $cuhourss.":".$cuminutes;    
        }        
        $arrayStr['whours']         =   $workhours;
    } else {

        $gender="";
        if($rw->US_Gender==2) $gender="female";else if($rw->US_Gender==1)$gender="male";else if($rw->US_Gender==3)$gender="other";
        if($rw->US_Status==0) $status="Blocked";else if($rw->US_Status==1)$status="Approved";        
        if($preTally_user_ofid == 1) { $rwValue= $rw->OF_Name; } else { $rwValue= $rw->DG_Name; } 
        $arrayStr['id']             =   $rw->US_EMPID;
        $arrayStr['name']           =   $rw->US_FName." ".$rw->US_LName;
        $arrayStr['aclType']        =   $rw->ACL_Name;
        $arrayStr['department']     =   $rw->DP_Name;
        $arrayStr['designation']    =   $rwValue;
        $arrayStr['location']       =   $rw->LC_Name;
        $arrayStr['phone']          =   $rw->US_Mobile1;
        $arrayStr['report']         =   $rw->ReportTo;
        $arrayStr['doj']            =   $rw->US_DOJ;
        $arrayStr['dateofbirth']    =   $rw->US_DOB;
        $arrayStr['address']        =   $rw->US_Address;
        $arrayStr['gender']         =   $gender;
        $arrayStr['country']        =   $rw->CN_Name;
        $arrayStr['state']          =   $rw->ST_Name;
        $arrayStr['city']           =   $rw->CT_Name;
        $arrayStr['email']          =   $rw->US_Pemail1;
        $arrayStr['guardian']       =   $rw->US_Guardian;
        $arrayStr['guardianmobile'] =   $rw->US_Guardianphone;
        $arrayStr['blood']          =   $rw->BG_Name;
        $arrayStr['relative']       =   $rw->US_Relative;
        $arrayStr['nameemergency']  =   $rw->US_Emergencyperson;
        $arrayStr['numberemergency']=   $rw->US_Emergencynumber;   
        $arrayStr['qualification']  =   $rw->US_Qualification;
        $arrayStr['specialization'] =   $rw->US_Specialization;
        $arrayStr['experience']     =   $rw->US_Experience;
        $arrayStr['lastemployee']   =   $rw->US_LastEmployee;
        $arrayStr['passport']       =   $rw->US_Passport;
        $arrayStr['pan']            =   $rw->US_PAN;
        $arrayStr['spname']         =   $rw->SP_Name;
        $arrayStr['accno']          =   $rw->US_AccNo;
        $arrayStr['bankname']       =   $rw->US_Bankname;
        $arrayStr['branch']         =   $rw->US_BankBranch;
        $arrayStr['pf']             =   $rw->US_PFNo;
        $arrayStr['esi']            =   $rw->US_ESI;
        $arrayStr['salary']         =   htmlspecialchars_decode ($rw->SS_Name);
        $arrayStr['grosssal']       =   $rw->US_GrossSal;
        $arrayStr['basicsal']       =   $rw->US_BasicSal;
        $arrayStr['dasal']          =   $rw->US_DASal;
        $arrayStr['hrasal']         =   $rw->US_HRASal;
        $arrayStr['ccasal']         =   $rw->US_CcaSal;
        $arrayStr['conveysal']      =   $rw->US_ConveySal;
        $arrayStr['edusal']         =   $rw->US_EduSal;
        $arrayStr['medsal']         =   $rw->US_MedSal;
        $arrayStr['mescsal']        =   $rw->US_MiscSal;
        $arrayStr['empStatus']      =   $rw->ES_Name;
        $arrayStr['status']         =   $status;
    }
    $data[] = array_map('trim',$arrayStr);
}
echo $ExcelObj->createExcel($data, $headerArray);
?>