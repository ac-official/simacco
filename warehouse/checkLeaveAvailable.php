<?php
require_once($BASEPATH . "preTallyClass/LeaveClass.php");
$LeaveObj = new LeaveClass();
    $lType= $REQUEST['lType'];
    if(isset($REQUEST['subId']))
        $USId = $REQUEST['subId'];
    else
        $USId = $preTally_user_id;
    $LeaveObj->selectmaxleave($lType);
    $maxLeave=$LeaveObj->selectmaxleaveArray[0];
    $eligibleEmpStatus=$LeaveObj->selectmaxleaveArray[1];
    $LeaveObj->selectEmpStatus($USId);
    $empStatus=$LeaveObj->selectEmpStatusArray[0];
    $ESH_Date=$LeaveObj->selectEmpStatusArray[1];
    $LeaveObj->selectdoj($USId,$lType);
    $doj=$LeaveObj->DojArray[0];
    $LvFromDate  =$REQUEST['fromDate'];
    $LvToDate    =$REQUEST['toDate'];   
    $LvFromYear  =date("Y", strtotime($LvFromDate));
    $LvToYear    =date("Y", strtotime($LvToDate));
    if( $LvFromYear!=$LvToYear){
        die("YErr");        
    }
    else{
    $LvMnth    =date("m", strtotime($LvToDate));
    if($currYrtakenLeave==""){
        $currYrtakenLeave=0;
    }
    if($ESH_Date==""||$ESH_Date==null){
        $ESH_Date=$doj;
    }  
    $eligibleEmpStatus=explode(",",$eligibleEmpStatus);
    for($i=0;$i<count($eligibleEmpStatus);$i++){
        $eligibleEmpStatusIndividual=explode(":",$eligibleEmpStatus[$i]);
        if($eligibleEmpStatusIndividual[0]==$empStatus){
            if($eligibleEmpStatusIndividual[1]=="1"){
                $ESH_Dateformatted = date("Y-m-d", strtotime($ESH_Date));
                $ESH_formattedDateArray=explode("-", $ESH_Dateformatted);
                $ESH_formattedYear=$ESH_formattedDateArray[0];
                $ESH_formattedMonth=$ESH_formattedDateArray[1];
                $ESH_formattedDate=$ESH_formattedDateArray[2];
                $currentYear=date("Y");
                if($LvFromYear==$currentYear)
                $currentMonth=date("m");
                else
                $currentMonth=$LvMnth;    
                $monthDiff=$currentMonth-$ESH_formattedMonth+1; 
                if($currentYear==$ESH_formattedYear){                                      
                       $currYrtakenLeave=$LeaveObj->checktakenLeaves($USId, $ESH_Dateformatted, date($LvFromYear.'-12-31'),$lType);                                      
                }else{
                    $monthDiff=$currentMonth;//month of new year(jan);
                    $currYrtakenLeave=$LeaveObj->checktakenLeaves($USId,date($LvFromYear.'-01-01'), date($LvFromYear.'-12-31'),$lType); 
                }   
                if($monthDiff==0)
                    $monthDiff=1;                
                $eligibleLeave=$maxLeave*$monthDiff;                
                $actualEligibleLeave=$eligibleLeave-$currYrtakenLeave;
                if($actualEligibleLeave<0){$actualEligibleLeave=0;}
                $actualEligibleLeave=number_format($actualEligibleLeave,1);
                echo $actualEligibleLeave;                    
            }
            else{
                echo "1110";
            }            
        }        
    }    
}
?>