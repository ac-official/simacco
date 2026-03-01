<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
$ExcelObj     = new ExportExcelClass();

if($REQUEST['type']=='track_enquiry'){/*Enquiry List*/
$dateFilter=$_REQUEST['filter'];   
    
$filter = " UA.OF_Id = $preTally_user_ofid ";
//print($ACL_Obj->ACL_HR);die();
if($ACL_Obj->ACL_HR==3)    
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ;
if($ACL_Obj->ACL_HR==2)
     $filter .=" AND  UA.LC_Id =". $preTally_user_lcid ;
if($ACL_Obj->ACL_HR==1)
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ." AND  UA.LC_Id = ".$preTally_user_lcid ;
if($ACL_Obj->ACL_HR==0)
     $filter .=" AND  UA.US_Id =". $preTally_user_id;
if($dateFilter['From_Date']) $stDate = $dateFilter['From_Date'];
if($dateFilter['To_Date']) $enDate = $dateFilter['To_Date'];

if($stDate!='' && $enDate!=''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AE.AE_CDate   between '".$stDate."' AND '".$enDate."'";
}elseif($stDate=='' && $enDate!=''){
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AE.AE_CDate   <=  '".$enDate."'";
}elseif($stDate!='' && $enDate==''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $filter .=" AND AE.AE_CDate   >=  '".$stDate."'";
}    
    
$headerArray['SL NO']                   = "SL NO";
$headerArray['Name']                    = "NAME";
$headerArray['Mobile No']               = "MOBILE NO";
$headerArray['Email']                   = "EMAIL";
$headerArray['NumFollowUps']            = "NUMBER OF FOLLOW UPS";
$headerArray['LastContUser']            = "LAST CONTACT USER";
$headerArray['LastContBranch']          = "LAST CONTACT BRANCH";
$headerArray['LastContDate']            = "LAST CONTACT DATE";
$headerArray['NxtFollowUps']            = "NEXT FOLLOW UP";
$headerArray['GettingJobStats']         = "CHANCE OF GETTING JOB";
$headerArray['CreatedUser']             = "CREATED USER";
$headerArray['CreatedBranch']           = "CREATED BRANCH";
$headerArray['Status']                  = "STATUS";
$ExcelResult = $ExcelObj->excelTrackEnquiry($filter);
$TR_Obj      = $ExcelObj->EnquiryDataArray;
$efStatus   = array('','Low','Normal','High','Very High');
$statusArray= array("1"=>"New", "2"=>"Processed", "3"=>"Cancelled", "4"=>"Follow up");
$i = 1;
foreach($TR_Obj as $option){ 
    $arrayStr       = array();
    $lastDate = $option->EF_CDate != '' ? date('d-m-Y', strtotime($option->EF_CDate)) : "--";
    $nextDate = $option->EF_NextDate != '' ? date('d-m-Y', strtotime($option->EF_NextDate)) : "--";
    $arrayStr['SL NO']                  = $i;
    $arrayStr['Name']                   = htmlspecialchars_decode($option->AE_Name, ENT_QUOTES);
    $arrayStr['Mobile No']              = htmlspecialchars_decode($option->AE_Mobile, ENT_QUOTES);
    $arrayStr['Email']                  = htmlspecialchars_decode($option->AE_Email, ENT_QUOTES);
    $arrayStr['NumFollowUps']           = htmlspecialchars_decode($option->NO_FollowUp, ENT_QUOTES);
    $arrayStr['LastContUser']           = htmlspecialchars_decode($option->LastUser, ENT_QUOTES);
    $arrayStr['LastContBranch']         = htmlspecialchars_decode($option->LastBranch, ENT_QUOTES);
    $arrayStr['LastContDate']           = htmlspecialchars_decode($lastDate, ENT_QUOTES);
    $arrayStr['NxtFollowUps']           = htmlspecialchars_decode($nextDate, ENT_QUOTES);
    $arrayStr['GettingJobStats']        = $efStatus[$option->EF_Chance];
    $arrayStr['CreatedUser']            = $option->US_FName.' '.$option->US_LName;
    $arrayStr['CreatedBranch']          = $option->LC_Name;
    $arrayStr['Status']                 = $statusArray[$option->AE_Status];
    
    $i++;
    
    
    $data[]                           = array_map('trim',$arrayStr);
}
}
elseif ($REQUEST['type']=='job_list') { /*JOB List*/
$filterValues = $_REQUEST['filter'];
$headerArray['SL NO']                = "SL NO";
$headerArray['BRANCH']               = "BRANCH";
$headerArray['TRACK ID']             = "TRACK ID";
$headerArray['CANDIDATE NAME']       = "CANDIDATE NAME";
$headerArray['DOCUMENTS']            = "DOCUMENTS";
$headerArray['STATE']                = "STATE";
$headerArray['BOARD']                = "BOARD";
$headerArray['PROCESS']              = "PROCESS";
$headerArray['JOB AMOUNT']           = "JOB AMOUNT";
$headerArray['STATUTORY AMOUNT']     = "STATUTORY AMOUNT";
$headerArray['EXTRA AMOUNT']         = "EXTRA AMOUNT";
$headerArray['DATE']                 = "JOB DATE";

$ExcelResult = $ExcelObj->reportTrackData($filterValues['From_Date'],$filterValues['To_Date'],$preTally_user_ofid);
$TR_Obj      = $ExcelObj->TrackDataArray;

$i = 1;
foreach($TR_Obj as $option){ 
    $arrayStr       = array();
    
    $arrayStr['SL NO']                = $i;
    $arrayStr['BRANCH']               = htmlspecialchars_decode($option->LC_Name, ENT_QUOTES);
    $arrayStr['TRACK ID']             = htmlspecialchars_decode($option->TR_Track, ENT_QUOTES);
    $arrayStr['CANDIDATE NAME']       = htmlspecialchars_decode($option->AJ_FName, ENT_QUOTES);
    $arrayStr['DOCUMENTS']            = htmlspecialchars_decode($option->ADOC_Document, ENT_QUOTES);
    $arrayStr['STATE']                = htmlspecialchars_decode($option->ST_Name, ENT_QUOTES);
    $arrayStr['BOARD']                = htmlspecialchars_decode($option->APS_Title, ENT_QUOTES);
    $arrayStr['PROCESS']              = htmlspecialchars_decode($option->Process, ENT_QUOTES);
    $arrayStr['JOB AMOUNT']           = htmlspecialchars_decode($option->JobAmount, ENT_QUOTES);
    $arrayStr['STATUTORY AMOUNT']     = $option->StatutoryNAmt ? htmlspecialchars_decode($option->StatutoryNAmt, ENT_QUOTES) : 0;
    $arrayStr['EXTRA AMOUNT']         = $option->StatutoryNAmt ? htmlspecialchars_decode($option->ExtraNAmt, ENT_QUOTES) : 0;
    $arrayStr['DATE']                 = $option->AJ_ReceivedDate;
    
    $i++;
    
    
    $data[]                           = array_map('trim',$arrayStr);
} 
}
elseif($REQUEST['type']=="document_wise"){ /*Documentwise List*/ 
    
    $headerArray['SL NO']                   = "SL NO";
    $headerArray['Document']                = "Document";
    $headerArray['Process']                 = "Process";
    $headerArray['Next Process']            = "Next Process";
    $headerArray['CutomerName']             = "Customer Name";
    $headerArray['TrackID']                 = "Track ID";
    $headerArray['CreatedUser']             = "Created User";
    $headerArray['CreatedBranch']           = "Created Branch";
    $headerArray['Status']                  = "Status";  
    
    $filter = ' WHERE  LC.OF_Id = '.$preTally_user_ofid.' AND AJD.AJD_Status != 0 AND AJDT.AJ_Status NOT IN (0,2)';
$dateFilter=$_REQUEST['filter'];
if($dateFilter['From_Date']) $stDate = $dateFilter['From_Date'];
if($dateFilter['To_Date']) $enDate = $dateFilter['To_Date'];

if($stDate!='' && $enDate!=''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
}elseif($stDate=='' && $enDate!=''){
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   <=  '".$enDate."'";
}elseif($stDate!='' && $enDate==''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   >=  '".$stDate."'";
}
    
    
    
    $ExcelResult = $ExcelObj->ExportTrackDocument($filter,"");
    $DocObj = $ExcelObj->DataArray;
    $subObj = $ExcelObj->SubArray;

$SPStatus = array('', 'Pending','Submitted','Completed','Rejected');
$MPStatus = array('', 'New','Underprocess','Transit','All Process Completed','Delivered');
    $i = 1;
foreach($DocObj as $option){ 
    $arrayStr       = array();
    $process='';
                    $slNo = 1;
                    foreach ($subObj[$option->AJD_Id] as $subRw){
                        $process = $process .$slNo++.') '.$subRw->APS_Title.'-'.$subRw->APM_Title.'-'.$SPStatus[$subRw->AJS_Status].' ';
                        if($slNo == 2 && $subRw->AJS_Status == 1)
                            $nextProcess = $subRw->APS_Title.' - '.'  '.$subRw->APM_Title;
                        if($CompletedDocFlag == 1 && ( $subRw->AJS_Status == 1 || $subRw->AJS_Status == 2 ))    
                            $nextProcess = $subRw->APS_Title.' - '.'  '.$subRw->APM_Title;
                        $CompletedDocFlag = $subRw->AJS_Status == 3 || $subRw->AJS_Status == 4 ? 1 : 0;
                    }
    $arrayStr['SL NO']                   = $i;
    $arrayStr['Document']                = $option->ADOC_Document;
    $arrayStr['Process']                 = $process;
    $arrayStr['Next Process']            = $nextProcess;
    $arrayStr['CutomerName']             = $option->AJ_FName;
    $arrayStr['TrackID']                 = $option->TR_Track;
    $arrayStr['CreatedUser']             = $option->US_FName.' '.substr($rw->US_LName,0, 1);
    $arrayStr['CreatedBranch']           = $option->LC_Name;
    $arrayStr['Status']                  = $MPStatus[$option->AJD_Status];  
    
    $i++;
    
    
    $data[]                           = array_map('trim',$arrayStr);
} 
}
echo $ExcelObj->createExcel($data, $headerArray);
exit;

