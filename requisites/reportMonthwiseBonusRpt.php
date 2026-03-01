<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/BusinessBonusReportClass.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");

$BSRptObj       = new BusinessBonusReportClass();
$GenObj         = new GeneralClass();

$year           = ($REQUEST['y']) ? $REQUEST['y'] : date("Y");   
$day            =  cal_days_in_month(CAL_GREGORIAN, ltrim($REQUEST['m'],'0'), $year); 

if($REQUEST['m']) {   // month is selected
   /* if(($REQUEST['m'] <= 3 && date('n') <= 3) || ($REQUEST['m'] >= 4 && date('n') >= 4)){
       $y   = date("Y");
    }else if($REQUEST['m'] >= 4 && date('n') <= 3){
       $y  = date("Y")-1;
    }

    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$y;
    
    if($REQUEST['m'] == date('m'))   // current month
        $day = date('d'); */
    $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $fromYear   = date('Y')-1;
    $toYear     = date('Y');
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear;      
    
} else if($REQUEST['y']) {
    
   /* if(($year+1) == date('Y'))
        $REQUEST['t']   = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y')).'-'.date('n').'-'.($year+1);
    else*/
        $REQUEST['t']   = '31-03-'.($year+1);
    $REQUEST['f']   = '01-04-'.$year;

}

$colArray           = array();
$NetProfitArray     = array();
$monthRangeArray    = array();
$BonusSettingArray  = array();

$startDate          = date("Y-m-d", strtotime($REQUEST['f']));
$lastDay            = date("Y-m-d", strtotime($REQUEST['t']));

$startDayArray      = explode('-',$REQUEST['f']);
$endDayArray        = explode('-',$REQUEST['t']);

$filter = " UA.OF_Id= $preTally_user_ofid ";
if(isset($REQUEST['LC_Id']) && is_numeric($REQUEST['LC_Id']))
    $filter .= ' AND LC.LC_Id = '.$REQUEST['LC_Id'];

$bonusFilt = " AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$startDayArray[0]."')) >= '".$startDate."' AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$endDayArray[0]."')) <= '".$lastDay."'";  
if(trim($REQUEST['US_Name'])) {
    $filter .= " AND CONCAT(UA.US_FName,' ', UA.US_LName) LIKE '".trim($REQUEST['US_Name'])."%'";
}

/* Get bonus settings */
$BonusSettings = $GenObj->ViewDetails("CONCAT(BPS_Year,'-',BPS_Month) AS BPS_Date,BPS_BranchShare,BPS_GroupShare","bonus_percentage_settings","OF_Id = $preTally_user_ofid","BPS_Year",$limit='0, 2000000');
$BonusSettingObj = $GenObj->DataArray;
foreach ($BonusSettingObj as $rw){
    $BonusSettingArray[$rw->BPS_Date] = $rw;
}

/* Get bonus report details */
$BSRptObj->reportBonusData($bonusFilt,$startDate,$lastDay,$preTally_user_ofid,$filter);
$RptObj             = $BSRptObj->UserArray;

$BonusDataObj       = $BSRptObj->DataArray;

/* total worked days of all staff */
$GenObj->ViewDetails("UA.LC_Id,UA.US_Id,EP.EP_WorkDays","users_auth AS UA LEFT JOIN employee_payroll AS EP ON UA.US_Id = EP.US_Id"," UA.OF_Id=".$preTally_user_ofid." AND UA.US_Status != 5 AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$startDayArray[0]."')) >= '".$startDate."' AND DATE(CONCAT(EP.EP_Year,'-',EP.EP_Month,'-','".$endDayArray[0]."')) <= '".$lastDay."' AND ((UA.US_ResignFlag  = 0) 
                OR (UA.US_ResignFlag = 1 AND UA.US_ResignDate > '".$lastDay."'))   ","UA.LC_Id ASC",'0, 2000000');
$userObj            = $GenObj->DataArray;

$totalWorkingDays = 0;
foreach ($userObj as $rw){
    if(!$LocDayArray[$rw->LC_Id])
        $LocDayArray[$rw->LC_Id] = 0;
    
    $LocDayArray[$rw->LC_Id] = $LocDayArray[$rw->LC_Id] + $rw->EP_WorkDays;
    $totalWorkingDays += $rw->EP_WorkDays;
}

/* Calculate branch and group share Start */

$BSRptObj->reportReportOverview($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,'');
$MRP_Obj        = $BSRptObj->BranchArray;

if($REQUEST['y']) {
    $start    = (new DateTime($startDate))->modify('first day of this month');
    $end      = (new DateTime($lastDay))->modify('last day of this month');

    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $monthRangeArray[] = $dt->format("Y-n");
    }
}

$totalBranchShare = 0;
if($MRP_Obj) {
    $j = 1;
    foreach($MRP_Obj as $rw) {
        $fixedTotal = $statutory =  $varTotal = $genTotal = $business = 0;
    
        foreach($BSRptObj->BussArray[$rw->LC_Id] as $busRow) {
            $business += $busRow;
        }
        $tax    = ($business/115) * 15;

        foreach($BSRptObj->StatutoryOverviewArray[$rw->LC_Id] as $stRow) {
            $statutory += $stRow;
        }
        $statutory = round($statutory);

        foreach($BSRptObj->FixedMonthReportArray[$rw->LC_Id] as $fixedRow) {
            $fixedTotal += $fixedRow;
        }
        $fixed     = round($fixedTotal);

        foreach($BSRptObj->VariableExpOverviewArray[$rw->LC_Id] as $varRow) {
            $varTotal += $varRow;
        }
        $variable  = round($varTotal);

        foreach($BSRptObj->GeneralExpOverviewArray[$rw->LC_Id] as $genRow) {
            $genTotal += $genRow;
        }
        $general   = round($genTotal) ;
        
        $netBal = ($business-($statutory+$fixed+$variable+$general)) ? round($business-($statutory+$fixed+$variable+$general)) : 0 ;
        $GrossAmount    = $business - ($statutory + $tax);
                 
        if($REQUEST['y']) {    // year filter ; add % share of each months in selected financial yr
            foreach($monthRangeArray as $rangeArray) {
                
                $grossProfit = $BSRptObj->BussArray[$rw->LC_Id][$rangeArray] - ($BSRptObj->StatutoryOverviewArray[$rw->LC_Id][$rangeArray] + $BSRptObj->TaxArray[$rw->LC_Id][$rangeArray]);
                $NetProfit   =  $grossProfit - ($BSRptObj->FixedMonthReportArray[$rw->LC_Id][$rangeArray] + $BSRptObj->VariableExpOverviewArray[$rw->LC_Id][$rangeArray] + $BSRptObj->GeneralExpOverviewArray[$rw->LC_Id][$rangeArray]);
                
                $NetProfitArray[$rw->LC_Id]['BranchShare'] += ($BonusSettingArray[$rangeArray]->BPS_BranchShare) ? $NetProfit * ($BonusSettingArray[$rangeArray]->BPS_BranchShare/100) : 0;
                $NetProfitArray[$rw->LC_Id]['GroupShare']  += ($BonusSettingArray[$rangeArray]->BPS_GroupShare) ? $NetProfit * ($BonusSettingArray[$rangeArray]->BPS_GroupShare/100) : 0;
            }         
        } else {
            $NetProfit      = $GrossAmount - ($fixed + $variable + $general);
            $toDate         = strtotime($REQUEST['t']);
            $NetProfitArray[$rw->LC_Id]['BranchShare']     = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare) ? $NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare/100) : 0;
            $NetProfitArray[$rw->LC_Id]['GroupShare']      = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare) ? $NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare/100) : 0;
        }
        
        $totalBranchShare   = $totalBranchShare+$NetProfitArray[$rw->LC_Id]['BranchShare'];
    }
} 
/* Calculate branch and group share End */

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    if(!isset($REQUEST['rfrshflag'])) {
    echo '<head>
        <column width="50" type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="bns_Sort_BIR"/></div>]]></column>
        <column width="*"  type="ro" align="left" ><![CDATA[<div><input type="text" id="bonususer" style="width: 70%;float:left;" placeholder="User" /><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="bns_Sort_BIR"/></div></div>]]></column>
        <column width="180"  type="ro" align="left" ><![CDATA[<div><div id="bonusBranchItmF" style="width: 70%;float:left;" placeholder="Branch"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="2" class="bns_Sort_BIR"/></div></div>]]></column>
        <column width="150" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Staff Worked Days <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="3" class="bns_Sort_BIR" /> </div>]]></column>
        <column width="150" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Branch Share Bonus <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="4" class="bns_Sort_BIR" /> </div>]]></column>
        <column width="150" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Group Share Bonus <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="5" class="bns_Sort_BIR" /> </div>]]></column>
        <column width="150" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Total Bonus <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="6" class="bns_Sort_BIR" /> </div>]]></column>
        <afterInit>
            <call command="enableTooltips"><param>false,false,false,false,false,false,false</param></call>
            <call command="attachFooter"><param>Total,#cspan,#cspan,,,,</param>
            <param>text-align:left,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right</param></call>
        </afterInit>
        </head>';   
    }
    if($RptObj) {
        $j = 1;
        foreach($RptObj as $rw) {
            echo '<row id = "'.$rw->US_Id.'">
                    <cell title= " ">'.$j.'</cell>               
                    <cell name="US_Name" title= " ">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell name="LC_Name" title= " ">'.$rw->LC_Name.'</cell>
                    
                    <cell name = "StaffCount">';

                    echo $workDays = ($BonusDataObj[$rw->US_Id]->EP_WorkDays) ?  $BonusDataObj[$rw->US_Id]->EP_WorkDays : 0;
                    $colArray[3] = $colArray[3]+$workDays;
                    echo '</cell> <cell>';
                    
                    echo $branchShare = ($NetProfitArray[$rw->LC_Id]['BranchShare'] > 0 && $NetProfitArray[$rw->LC_Id]['GroupShare'] > 0 ) ? round(($NetProfitArray[$rw->LC_Id]['BranchShare']/$LocDayArray[$rw->LC_Id])*$workDays) : 0;
                    $colArray[4] = $colArray[4]+$branchShare;
                    echo '</cell><cell>';
                    echo $groupShare    = ($totalBranchShare > 0 ) ? round(($totalBranchShare/$totalWorkingDays)*$workDays) : 0;
                    
                    $colArray[5] = $colArray[5]+$groupShare;
                    echo '</cell><cell>';
                    echo $totalBonus  = round($branchShare+$groupShare);
                    $colArray[6] = $colArray[6]+$totalBonus;
                    echo '</cell>';
            echo '</row>';
            $j++;
        }
        echo '<userdata name="colSum">'.json_encode($colArray).'</userdata>';
    } else { 
        echo '<row id="no_record"> 
                <cell colspan="7" title= " "><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    }   
echo '</rows>';
?>