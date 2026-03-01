<?php
require_once($BASEPATH . 'includes/functions.php');
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
include_once($BASEPATH . "preTallyClass/LeaveClass.php");
$filterUSR = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
$year       = date("Y");
$currentMonth=date("m");
$curruentDate=date("Y-m-d H:i:s");
$empSalStruct=array();
if($REQUEST['att_year'] && $REQUEST['att_month'] && str_pad($REQUEST['att_month'], 2,"0",STR_PAD_LEFT).'-'.$REQUEST['att_year'] != date('m-Y')){       
    $year = $REQUEST['att_year']; 
    $month  = str_pad($REQUEST['att_month'], 2,"0",STR_PAD_LEFT);
  $g_date     =$year.'-'.$month.'-1';
  $lstdate = date("t", strtotime($g_date));
}
 else {    
     echo 3;
     return;
}
$a_date  = $year."-".$month."-".$lstdate;

$AttObj = new AttendanceClass();
if($AttObj->verifyMonth("employee_payroll","EP_Month",$month,$preTally_user_ofid,"EP_Year",$year)){ 
    $userSalCount=0;
    if(!isset($REQUEST['datas'])){
        $AttObj->empSalPayroll($month, $year, $preTally_user_ofid);
        $Att_Objuser    = $AttObj->userAttendance;
        foreach ($Att_Objuser as $rws){
            $user_saldetails=$rws["USR_Details"];  
            if(($user_saldetails['US_GrossSal']==0)||($user_saldetails['SS_Status']==0)||($user_saldetails['SS_Status']==3) ){
                $userSalCount++;
                $empSalStructNames=$user_saldetails['US_EMPID']." ".$user_saldetails['US_FName']." ".$user_saldetails['US_LName'];
                array_push($empSalStruct,$empSalStructNames);
            } 

        }
        
    }
    if($userSalCount!=0){
            $empSalStruct["status"]="salary_struct_err";
            $empSalStruct["count"]=$userSalCount;
            echo json_encode($empSalStruct);
            return;
    }else{
            $UserObj = new UserClass();
            $UserObj->selectCompanySettings($preTally_user_ofid);
            $CompSett=$UserObj->CompanySettingsArray;
            $AttObj->getHolidays($a_date,$preTally_user_ofid);
            $AttObj->getWeekendOffs($month,$year,$preTally_user_ofid);
            $AttObj->getOfficeRH($preTally_user_ofid);
            $AttObj->getLeaveType($preTally_user_ofid);
            $AttLv=$AttObj->getLeaveTypeArray;
            foreach($AttLv as $rows){
                    $LT_Id.=$rows->LT_Id;
                    $LT_Id.=",";
            }
            $LT_Ids=rtrim($LT_Id,",");
            $AttObj->listReptAttendancePayroll($month, $year, $preTally_user_ofid,$CompSett['CS_WrkHrGraceTime']);
            $holiday_count=0;
            $WeekendOffs= array();
            $Att_Obj= array();
            $Holidays = array();
            $Holidays   = $AttObj->Holidays;
            $WeekendOffs = $AttObj->WeekOffs;
            $Att_Obj    = $AttObj->RptAttendance;            
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            for ($j = 1; $j <= $lstdate; $j++) {
                    if ($j < 10)
                        $j = "0" . $j;
                }
                 
$dataPayroll = array();
$repayment_ids=array();
$errorPayroll=array();
$count=0;$err_count=0;$err_Flag=0;
    if ($Att_Obj) 
    {
        foreach ($Att_Obj as $rw){
                        $user_details=$rw["USR_Details"];
                        $att_details=$rw["ATT_Dates"];  
                        $array_user = array_keys($att_details); 
                        $att_attr= array_values($att_details); 

                        $allleaves="";
                        $sumofAllLeaves=0;
                        $final_arrayPayroll = array();
                        $AttObj->tmp_rharray=array(); 
                        $AttObj->getAdjMnthAttendance($user_details['US_Id'],$month, $year); 
                    if($AttObj->AdjcntAttendance["last"]!=null)                              
                        array_push($array_user,$AttObj->AdjcntAttendance["last"]);
                    if($AttObj->AdjcntAttendance["next"]!=null)
                        array_push($array_user,$AttObj->AdjcntAttendance["next"]);
                        $EmpConEPF=$EmpConESI=$EmpConLWF=0;
                        if(($user_details['US_GrossSal']!=0)&&($user_details['SS_Status']!=0)&&($user_details['SS_Status']!=3)){ 
                                $onedaySal=($user_details['US_GrossSal'])/$daysInMonth;
                                $LTId=$LT_Ids;
                                $AttObj->getApprovedLeaveDates($user_details['US_Id'],$month, $year,$LT_Ids);//Leave Type Dates
                                $LT_Id=explode(",",$LTId);
                                for($k=0;$k<count($LT_Id);$k++){

                                    $AttObj->getApprovedLeave($user_details['US_Id'],$month, $year,$LT_Id[$k]);//Leave Type Sums
                                    $approvedLeave    = $AttObj->getApprovedLeaveArray[0];
                                    $approvedLeaveName=$AttObj->getApprovedLeaveArray[1];
                                        if(($approvedLeave=="null")||($approvedLeave=="")){
                                            $approvedLeave=0; 
                                        }
                                    $allleaves.=$approvedLeaveName.":".$approvedLeave."-";
                                }

                                $half=0;$full=0;$absent=0;
                                unset($AttObj->tmp_hol);
                                $AttObj->tmp_hol=array(); 
                                $allleaves=rtrim($allleaves,"-"); 
                                $individualLeaves=explode("-",$allleaves);
                                for($j=0;$j<count($individualLeaves);$j++){  
                                    $indiLeavesCountAndName=explode(":",$individualLeaves[$j]);
                                    $sumofAllLeaves=$sumofAllLeaves+$indiLeavesCountAndName[1];
                                }
                                $rh_count=0; 
                                $stid=$user_details['ST_Id']; 
                                $dpid=$user_details['DP_Id'];   

                                    
                                $half=0;$full=0;$absent=0;$holiday_count=0;
                                unset($AttObj->tmp_hol);
                                $AttObj->tmp_hol=array();
                                for ($j = 1; $j <= $lstdate; $j++) {      
                                    $AttObj->tmp_rharray=array_merge((array)$AttObj->RH_Holidays[0],(array)$AttObj->RH_Holidays[$stid]);  
                                    $AttObj->getTakenRHBatches($user_details['US_Id']);                            
                                    $flp_array=array_flip($AttObj->RH_Batches);
                                    $rh_holiday=array_diff_key($AttObj->tmp_rharray, $flp_array);                                    
                                    if ($j < 10) $j = "0" . $j;                            
                                    $date = $year . "-" . $month . "-" . $j;   
                                    $approvedLeaveDates=array();
                                    if(!empty($AttObj->ApprovedLeaveDays))
                                    $approvedLeaveDates=array_column($AttObj->ApprovedLeaveDays, 'LRD_Date'); 
                                                                 
                                    $array_intr=array();
                                    $array_intr=array_intersect($approvedLeaveDates, $array_user);
                                    //print_r($array_intr);
                                    foreach($array_intr as $days){
                                    $chrs = $att_details[$days]['AT_Hours'];    
                                    $loginTime   =$logoutTime="00:00:01";
                                    $loginTime   =$att_details[$days]['AT_SignIn'];
                                    $logoutTime  =$att_details[$days]['AT_SignOut'];        
                                    $meanDifAM   =abs(strtotime("12:00:00")-strtotime($loginTime));
                                    $meanDifPM   =abs(strtotime("12:00:00")-strtotime($logoutTime));
                                    $minWrkHrs   =($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime'];
                                    if($meanDifAM > $meanDifPM)
                                        $attSession="FN";
                                    else 
                                        $attSession="AN";     
                                    $key_L      = array_search($days,$approvedLeaveDates);                                    
                                    $session    =$AttObj->ApprovedLeaveDays[$key_L]['LRD_Session'];    
                                    if (($key = array_search($days, $array_user)) !== false && $session=="FL") {
                                         unset($array_user[$key]);
                                    }
                                    else if (($key = array_search($days, $array_user)) !== false && $chrs < $minWrkHrs && ($session=="FN" || $session=="AN")) {
                                        if($attSession==$session){
                                            unset($array_user[$key]);                                   
                                        }
                                    }
                                    }
                                    if (in_array($date, $array_user) && (!multi_array_search($date, $approvedLeaveDates))){  

                                            unset($AttObj->tmp_hol);
                                            $AttObj->tmp_hol=array();
                                            $hrs = $att_details[$date]['AT_Hours'];  
                                            if($hrs>=($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                                     $full+=1;
                                            }
                                            elseif ($hrs>=(($user_details['US_WrkHours']/2)-$CompSett['CS_WrkHrGraceTime']) && $hrs<($user_details['US_WrkHours']-$CompSett['CS_WrkHrGraceTime'])){
                                                $half+=1;

                                            }
                                    } 
                                    else{                                            
                                            
                                                $AttObj->getRHTakenDays($user_details['US_Id']);
                                                $RH_takendays=$AttObj->RH_TaknDays; 
                                                $m_Hol = array();                                                               
                                                $m_Hol=$WeekendOffs[$dpid];                                                               
                                                $m_Hol_2 = array_merge((array)$Holidays[$stid]['dates'],(array)$Holidays[0]['dates']);                                                            
                                                $m_Hol_3 = array_merge((array)$WeekendOffs[$dpid],(array)$RH_takendays);                                                          
                                                $m_Hol   = array_merge((array)$m_Hol_3,(array)$m_Hol_2);
                                                sort($m_Hol);
                                                $AttObj->chkadjDays($array_user,$m_Hol,$date);
                                            if ((!in_array($date, $WeekendOffs[$dpid]))
                                                &&(!in_array($date, $Holidays[0]['dates']))
                                                &&(!in_array($date, $Holidays[$stid]['dates'])
                                                &&(!multi_array_search($date, $approvedLeaveDates))
                                                &&(!in_array($date, $AttObj->tmp_hol))                
                                                &&(multi_array_search($date,$rh_holiday)))){
                                                        $RH_Status=$AttObj->checkRHDate($rh_holiday,$date,$user_details['US_Id']);
                                            }

                                            $AttObj->getRHTakenDays($user_details['US_Id']);
                                            $RH_takendays=array();
                                            $RH_takendays=$AttObj->RH_TaknDays; 

                                            if ((in_array($date, $WeekendOffs[$dpid])|| in_array($date, $Holidays[0]['dates']) || 
                                                in_array($date, $Holidays[$stid]['dates']))&&(!in_array($date, $AttObj->tmp_hol) 
                                                && !multi_array_search($date, $approvedLeaveDates)&&!in_array($date,$RH_takendays))){                                                                                                    
                                                                    $holiday_count+=1;
                                            } 
                                            else{  
                                                   if(in_array($date,$RH_takendays)){
                                                            $holiday_count+=1; 
                                                            $rh_count+=1;                                                            
                                                    }
                                                    elseif(is_numeric(array_search($date, $approvedLeaveDates))){
                                                                        //$absent+=1;
                                                                        
                                                    $key_H      = array_search($date,$approvedLeaveDates);                                                    
                                                    $session    =$AttObj->ApprovedLeaveDays[$key_H]['LRD_Session'];                                                    
                                                    if($session!='FL'){
                                                        $halftooltip="H"; 
                                                        $halfTitle="Half Day";                 
                                                        $absent+=0.5;
                                                        if (in_array($date, $array_user)){
                                                        $half+=1;     
                                                        unset($m_Hol);
                                                        $m_Hol = array();     
                                                        }
                                                    }
                                                    }else{
                                                            $absent+=1;

                                                    }  
                                            }

                                        }

                                }
                                //print($full.",".$half.",".$holiday_count.",".$sumofAllLeaves.":");//21,1,5,2.5
                                $totworking=($half/2)+$full+$holiday_count;
                                $lop=$lstdate-(($half/2)+$full+$holiday_count);
                                $totalLeave=$lop-$sumofAllLeaves;
                                if($totalLeave<0)$totalLeave=0;                                
                                if($user_details["US_AttndFlag"]==1)$lopdeduction=0;
                                else $lopdeduction=round(($totalLeave*$onedaySal));                                
                                $grosslop=$user_details['US_GrossSal']-$lopdeduction;
                                $grosslop=round(($grosslop));
                                $AttObj->getDedSalType($user_details['SS_Id']);
                                if($AttObj->getDedSalTypeArray['SS_DedESI_Type']==0){//checking ESI is in persentage
                                    $ESI=round(($grosslop*$AttObj->getDedSalTypeArray['SS_DedESI'])/100);
                                }else{                              // ESI calculted based on amount
                                   $ESI=round($user_details['US_DedESI']); 
                                }
                                if($AttObj->getDedSalTypeArray['SS_DedEPF_Type']==0){
                                    $EPF=round(($grosslop*$AttObj->getDedSalTypeArray['SS_DedEPF'])/100);
                                }else{
                                    $EPF=round($user_details['US_DedEPF']);
                                }
                                if($AttObj->getDedSalTypeArray['SS_EmpConEPF_Type']==0){
                                    $EmpConEPF=round(($grosslop*$AttObj->getDedSalTypeArray['SS_EmpConEPF'])/100);
                                }else{
                                    $EmpConEPF=round($AttObj->getDedSalTypeArray['SS_EmpConEPF']);
                                }
                                if($AttObj->getDedSalTypeArray['SS_EmpConESI_Type']==0){
                                    $EmpConESI=round(($grosslop*$AttObj->getDedSalTypeArray['SS_EmpConESI'])/100);
                                }else{
                                    $EmpConESI=round($AttObj->getDedSalTypeArray['SS_EmpConESI']);
                                }
                                if($AttObj->getDedSalTypeArray['SS_EmpConLWF_Type']==0){
                                    $EmpConLWF=round(($user_details['US_GrossSal']*$AttObj->getDedSalTypeArray['SS_EmpConLWF'])/100);
                                }else{
                                    $EmpConLWF=round($AttObj->getDedSalTypeArray['SS_EmpConLWF']);
                                }                                
                                $ProfTds=round(($grosslop*$AttObj->getDedSalTypeArray['SS_DedProfTDS'])/100);
                                $pfesiwfDeduction=$ESI+$EPF+$user_details['US_DedLWF']+$ProfTds+round($user_details['US_DedSalTDS']);
                                $AttObj->getRepaymentAmounts($user_details['US_Id'],$month,$year);
                                $deduction=$lopdeduction+$pfesiwfDeduction+round($AttObj->repaymentArray["Advance"])+round($AttObj->repaymentArray["Loan"])+round($user_details['US_DedMealCard']);
                                $takeHomeSalary= round(($user_details['US_GrossSal'] -$deduction));
                                //print_r($AttObj->repaymentArray);die();
                                if($ESI==0 && $EPF==0) $grosslop=0;                                
                                $final_arrayPayroll= array(
                                            'US_Id'=> $user_details['US_Id'],
                                            'OF_Id'=>$user_details['OF_Id'],
                                            'LC_Name'=>"'".$user_details['LC_Name']."'",
                                            'US_FName'=>"'".$user_details['US_FName']."'",
                                            'US_LName'=> "'".$user_details['US_LName']."'",
                                            'EP_Month'=>$month,
                                            'EP_Year'=>$year,
                                            'US_GrossSal'=>round($user_details['US_GrossSal']),
                                            'US_BasicSal'=>round($user_details['US_BasicSal']),
                                            'US_HRASal'=>round($user_details['US_HRASal']),
                                            'US_CcaSal'=>round($user_details['US_CcaSal']),
                                            'US_ConveySal'=>round($user_details['US_ConveySal']),
                                            'US_EduSal'=>round($user_details['US_EduSal']),
                                            'US_MedSal'=>round($user_details['US_MedSal']),
                                            'EP_Otherallowance'=>round($user_details['US_MiscSal']),
                                            'US_DedEPF'=>$EPF,
                                            'US_DedESI'=>$ESI,
                                            'US_DedLWF'=>round($user_details['US_DedLWF']),
                                            'EP_EmpConEPF'=>$EmpConEPF,
                                            'EP_EmpConESI'=>$EmpConESI,
                                            'EP_EmpConLWF'=>$EmpConLWF,
                                            'EP_Lop'=>$lopdeduction,
                                            'EP_TakehomeSal'=>$takeHomeSalary,
                                            'EP_Proftax'=>0,
                                            'EP_ProfTds'=>$ProfTds,
                                            'EP_SalTds'=>round($user_details['US_DedSalTDS']),
                                            'EP_MealCard'=>round($user_details['US_DedMealCard']),
                                            'EP_Salaryadvance'=>$AttObj->repaymentArray["Advance"],
                                            'EP_Loan'=>$AttObj->repaymentArray["Loan"],
                                            'EP_AdjstmntAddition'=>0,
                                            'EP_AdjstmntDeduction'=>0,
                                            'EP_SalDeductableLeave'=>$totalLeave,
                                            'EP_PFESI_sal'=>$grosslop,
                                            'EP_WorkDays'=>$totworking,
                                            'EP_TotWrkDays'=>$lstdate,
                                            'EP_PrsntDays'=>$full+($half/2),
                                            'EP_HlfDays'=>$half,
                                            'EP_LOPDays'=>$totalLeave,
                                            'EP_GeneratedBy'=>$preTally_user_id,
                                            'EP_CDate'=>"'".$curruentDate."'"
                                );
                                $totalLoan=$AttObj->repaymentArray["Advance"]+$AttObj->repaymentArray["Loan"];
                                if($totalLoan>0 && $final_arrayPayroll['EP_TakehomeSal']<$totalLoan){
                                    $errorPayroll["Status"]="salary_negative";
                                    $names=$user_details['US_FName']." ".$user_details['US_LName'];
                                    array_push($errorPayroll,$names);
                                    $err_Flag=1;                                    
                                }
                                else{
                                $dataPayroll[$count] = $final_arrayPayroll;                                
                                if(!empty($AttObj->repaymentArray["SR_Ids"])){
                                    $repayment_ids=array_merge((array)$repayment_ids,(array)$AttObj->repaymentArray["SR_Ids"]);                                                                                                            
                                }
                                $count++;
                                }
                               

                        }else{
                            
                        }           
                    }
            }    
          if($err_Flag==0){
          echo $result=$AttObj->generateEmployeePayroll("employee_payroll",$dataPayroll);
          if($result==1){
          foreach($repayment_ids as $ids){   
          $AttObj->updateRepaymentStatus($ids,3);
          }
          }          
          }else{              
              echo json_encode($errorPayroll);
                                    return 4;          
          }
    }
    
}else{
                    echo 2;//already exist
   }
?>