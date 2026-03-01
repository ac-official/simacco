<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/ExportExcelClass.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");


$GenObj       = new GeneralClass();
$ExcelObj     = new ExportExcelClass();

$filterValues = $_REQUEST['filter'];

$headerArray['SL NO']            = "Sl No";
$headerArray['Branch']           = "Branch";
$headerArray['StatutoryExp']     = "Statutory Expense";
$headerArray['BranchFixed']      = "Branch Fixed";
$headerArray['BranchFlexi']      = "Branch Flexi";
$headerArray['GeneralExp']       = "General Exp";
$headerArray['NetBalance']       = "Net Balance";
$headerArray['BranchShare']      = "Net Profit Branch Share";
$headerArray['GroupShare']       = "Net Profit Group Share";
$headerArray['StaffNos']         = "Total Staff  Nos";
$headerArray['WorkingDaysStaff'] = "Working days of Staff";
$headerArray['OneDayBonus']      = "Bonus of one days";


echo $filterValues['y'];
$year  = ($filterValues['y']) ? $filterValues['y'] : date("Y");   
$day   =  cal_days_in_month(CAL_GREGORIAN, ltrim($filterValues['m'],'0'), $year);
    
if($filterValues['m']) {   // month is selected
    if(($filterValues['m'] <= 3 && date('n') <= 3) || ($filterValues['m'] >= 4 && date('n') >= 4)){
       $y   = date("Y");
    }else if($filterValues['m'] >= 4 && date('n') <= 3){
       $y  = date("Y")-1;
    }

    $REQUEST['f']   = '01-'.$filterValues['m'].'-'.$y;
    
    if($filterValues['m'] == date('m'))   // current month
        $day = date('d');
    
    $REQUEST['t']   = $day.'-'.$filterValues['m'].'-'.$y;
} else if($filterValues['y']) {    
  $currentYear = date('Y');
     if($currentYear==$filterValues['y']){
        if((date('n') >= 4)&&(date('n') <= 9)) { // current financial year
            $fromYear   = date('Y')-1;
            $toYear     = (date('n') <= 3) ? date('Y')-1 : date('Y');
            $REQUEST['f']   = '01-10-'.$fromYear;
           
        } else if(date('n') <= 3) {
            $fromYear   = date('Y')-1;
            $toYear     = (date('n') >= 4) ? date('Y')-1 : date('Y');
            $REQUEST['f']   =  '01-'.(date('n')+6).'-'.$fromYear;
        } else if(date('n') >= 10) {
            $fromYear   = date('Y');
            $toYear     = (date('n') >= 4) ? date('Y') : date('Y')-1;
            $REQUEST['f']   =  '01-'.(date('n')-6).'-'.$fromYear;
        }
        $day            =  cal_days_in_month(CAL_GREGORIAN,date('n'), $toYear);
        $REQUEST['t']   = $day.'-'.date('n').'-'.$toYear;
     }else{
        $REQUEST['t']   = '31-03-'.($year+1);
        $REQUEST['f']       = '01-04-'.($filterValues['y']);
     }
}




$filter = "AND 1 ";
if(isset($filterValues['LCID']) && is_numeric($filterValues['LCID']))
    $filter .= ' AND LC.LC_Id = '.$filterValues['LCID'];

$LocDayArray        = array();
$monthRangeArray    = array();
$BonusSettingArray  = array();

$startDate          = date("Y-m-d", strtotime($REQUEST['f']));
$lastDay            = date("Y-m-d", strtotime($REQUEST['t']));

$startDayArray      = explode('-',$REQUEST['f']);
$endDayArray        = explode('-',$REQUEST['t']);

$GenObj->ViewDetails("UA.LC_Id,UA.US_Id,EP.EP_WorkDays","users_auth AS UA LEFT JOIN employee_payroll AS EP ON UA.US_Id = EP.US_Id"," UA.OF_Id=".$preTally_user_ofid." AND UA.US_Status != 5 AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$startDayArray[0]."')) >= '".$startDate."' AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$endDayArray[0]."')) <= '".$lastDay."' AND ((UA.US_ResignFlag  = 0) 
                OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '".$lastDay."')) ","UA.LC_Id ASC",'0, 2000000');
$userObj            = $GenObj->DataArray;

foreach ($userObj as $rw){
    if(!$LocDayArray[$rw->LC_Id])
        $LocDayArray[$rw->LC_Id] = 0;
    
    $LocDayArray[$rw->LC_Id] = $LocDayArray[$rw->LC_Id] + $rw->EP_WorkDays;
}

$BonusSettings   = $GenObj->ViewDetails("CONCAT(BPS_Year,'-',BPS_Month) AS BPS_Date,BPS_BranchShare,BPS_GroupShare","bonus_percentage_settings","OF_Id = $preTally_user_ofid","BPS_Year",$limit='0, 2000000');
$BonusSettingObj = $GenObj->DataArray;
foreach ($BonusSettingObj as $rw){
    $BonusSettingArray[$rw->BPS_Date] = $rw;
}

$ExcelObj->reportReportOverview($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$filter);
$MRP_Obj      = $ExcelObj->BranchArray;

if($filterValues['y']) {
    $start    = (new DateTime($startDate))->modify('first day of this month');
    $end      = (new DateTime($lastDay))->modify('last day of this month');

    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $monthRangeArray[] = $dt->format("Y-n");
    }
}

if($MRP_Obj) {
    $j = 1;
    foreach($MRP_Obj as $rw) {

        $arrayStr       = array();
        $arrayStr['SL NO']                = $j;

        $fixedTotal = $statutory =  $varTotal = $genTotal = $business = 0;

        $arrayStr['BRANCH'] = htmlspecialchars_decode($rw->LC_Name, ENT_QUOTES);
        
        foreach($ExcelObj->BussArray[$rw->LC_Id] as $busRow) {
            $business += $busRow;
        }
        $tax  = ( $business / 115 ) * 15;
        
        foreach($ExcelObj->StatutoryOverviewArray[$rw->LC_Id] as $stRow) {
            $statutory += $stRow;
        }
        $statutory = round($statutory);
        $arrayStr['StatutoryExp'] = htmlspecialchars_decode(round($statutory), ENT_QUOTES);

        foreach($ExcelObj->FixedMonthReportArray[$rw->LC_Id] as $fixedRow) {
            $fixedTotal += $fixedRow;
        }
        $fixed     = round($fixedTotal);
        $arrayStr['BranchFixed'] = htmlspecialchars_decode(round($fixedTotal), ENT_QUOTES);

        foreach($ExcelObj->VariableExpOverviewArray[$rw->LC_Id] as $varRow) {
            $varTotal += $varRow;
        }
        $variable  = round($varTotal);
        $arrayStr['BranchFlexi'] = htmlspecialchars_decode(round($varTotal), ENT_QUOTES);

        foreach($ExcelObj->GeneralExpOverviewArray[$rw->LC_Id] as $genRow) {
            $genTotal += $genRow;
        }
        $general   = round($genTotal) ;
        $arrayStr['GeneralExp'] = htmlspecialchars_decode(round($genTotal), ENT_QUOTES);
        $arrayStr['NetBalance'] = ($business-($statutory+$fixed+$variable+$general)) ? round($business-($statutory+$fixed+$variable+$general)) : 0 ;
        $GrossAmount    = $business - ($statutory + $tax);

        $BranchShare =  $GroupShare = 0;
        if($filterValues['y']) {
            foreach($monthRangeArray as $rangeArray) {

                $grossProfit = $ExcelObj->BussArray[$rw->LC_Id][$rangeArray] - ($ExcelObj->StatutoryOverviewArray[$rw->LC_Id][$rangeArray] + $ExcelObj->TaxArray[$rw->LC_Id][$rangeArray]);
                $NetProfit   =  $grossProfit - ($ExcelObj->FixedMonthReportArray[$rw->LC_Id][$rangeArray] + $ExcelObj->VariableExpOverviewArray[$rw->LC_Id][$rangeArray] + $ExcelObj->GeneralExpOverviewArray[$rw->LC_Id][$rangeArray]);

                $BranchShare += ($BonusSettingArray[$rangeArray]->BPS_BranchShare) ? round($NetProfit * ($BonusSettingArray[$rangeArray]->BPS_BranchShare/100)) : 0;
                $GroupShare  += ($BonusSettingArray[$rangeArray]->BPS_GroupShare) ? round($NetProfit * ($BonusSettingArray[$rangeArray]->BPS_GroupShare/100)) : 0;
            }         
        } else {
            $NetProfit      = $GrossAmount - ($fixed + $variable + $general);
            $toDate         = strtotime($REQUEST['t']);
            $BranchShare    = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare) ? round($NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare/100)) : 0;
            $GroupShare     = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare) ? round($NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare/100)) : 0;
        }

        $arrayStr['BranchShare'] = htmlspecialchars_decode($BranchShare, ENT_QUOTES);
        $arrayStr['GroupShare'] = htmlspecialchars_decode($GroupShare, ENT_QUOTES);
        $arrayStr['StaffNos'] = ($rw->StaffCount) ? $rw->StaffCount : 0;
        $arrayStr['WorkingDaysStaff'] = ($LocDayArray[$rw->LC_Id]) ? $LocDayArray[$rw->LC_Id] : 0;
        $arrayStr['OneDayBonus'] = ($BranchShare > 0 && $GroupShare > 0 ) ? round($BranchShare*$LocDayArray[$rw->LC_Id]) : 0;

        $j++;
        $data[]                           = array_map('trim',$arrayStr);
    }
}
        
echo $ExcelObj->createExcel($data, $headerArray);
exit;