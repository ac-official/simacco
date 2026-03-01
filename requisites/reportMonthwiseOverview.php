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
        $day = date('d');
    */
    $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $fromYear   = date('Y')-1;
    $toYear     = date('Y');
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear;        
} else if($REQUEST['y']) {    
  $currentYear = date('Y');
     if($currentYear==$REQUEST['y']){
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
        $REQUEST['f']       = '01-04-'.($REQUEST['y']);
     }
}
$filter = "AND 1 ";
if(isset($REQUEST['LC_Id']) && is_numeric($REQUEST['LC_Id']))
    $filter .= ' AND LC.LC_Id = '.$REQUEST['LC_Id'];

$LocDayArray        = array();
$colArray           = array();
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

$BSRptObj->reportReportOverview($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$filter);
$MRP_Obj      = $BSRptObj->BranchArray;

if($REQUEST['y']) {
    $start    = (new DateTime($startDate))->modify('first day of this month');
    $end      = (new DateTime($lastDay))->modify('last day of this month');

    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $monthRangeArray[] = $dt->format("Y-n");
    }
}
        
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50" type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="ov_Sort_BIR"/></div>]]></column>
        <column width="150"  type="ro" align="left" ><![CDATA[<div><div id="ovBranchItmF" style="width: 70%;float:left;" placeholder="Branch"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="ov_Sort_BIR"/></div></div>]]></column>
        <column width="110" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Business <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="2" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="110" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Statutory Expense <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="3" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Branch Fixed <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="4" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="110" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Branch Flexi <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="5" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="150" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> General Expense <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="6" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Net Balance <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="7" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" align="right" ><![CDATA[<div style="float:left;"> Net Profit Branch Share<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="8" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" align="right" ><![CDATA[<div style="float:left;"> Net Profit Group Share<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="9" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" align="right" ><![CDATA[<div style="float:left;"> Total Staff Nos:<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="10" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" align="right" ><![CDATA[<div style="float:left;"> Worked Days of all Staff<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="11" class="ov_Sort_BIR" /> </div>]]></column>
        <column width="120" type="ron" align="right" ><![CDATA[<div style="float:left;"> Bonus of One Day<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="12" class="ov_Sort_BIR" /> </div>]]></column>
        <afterInit>
            <call command="enableTooltips"><param>false,false,false,false,false,false,false,false,false,false,false,false,false</param></call>
            <call command="attachFooter"><param>Total,#cspan,,,,,,,,,,,</param>
            <param>text-align:left,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right</param></call>
        </afterInit>
        </head>';   
    if($MRP_Obj) {
        $j = 1;
        foreach($MRP_Obj as $rw) {
            $fixedTotal = $statutory =  $varTotal = $genTotal = $business = $statutoryN = 0;
             $namecellcolor=''; 
            if($rw->LC_Status==0)
            $namecellcolor='style="color:#ff6600;font-weight:bold;"';
            elseif($rw->LC_Status==2)
            $namecellcolor='style="color:red;font-weight:bold;"';
            elseif($rw->LC_Status==1)
            $namecellcolor=''; 
            
            echo '<row id = "'.$rw->LC_Id.'">
                    <cell title= " ">'.$j.'</cell>                   
                    <cell name="LC_Name" title= " "  '.$namecellcolor.'>'.$rw->LC_Name.'</cell>
                    <cell name="Business" title= " ">';
                    foreach($BSRptObj->BussArray[$rw->LC_Id] as $busRow) {
                        $business += $busRow;
                    }
                    $tax                = ( $business / 115 ) * 15;
                    if($business != 0) echo round($business); else echo 0;
                    $colArray[2] = $colArray[2]+round($business);
                    echo '</cell>
                        
                    <cell name="Statutory" title= " ">';
                    /*
                     * statutory + extra amount
                     */
                    foreach($BSRptObj->StatutoryOverviewArray[$rw->LC_Id] as $stRow) {
                        $statutory += $stRow;
                    }
                    echo $statutory = round($statutory);
                    $colArray[3]    = $colArray[3]+$statutory;
                    echo '</cell>';
                    
                    /*
                     * Only statutory amount to calculte net profit
                     */
                    
                    foreach($BSRptObj->StatutoryArray[$rw->LC_Id] as $stNRow) {
                        $statutoryN += $stNRow;
                    }
                    $statutoryN = round($statutoryN); 
                        
                    foreach($BSRptObj->FixedMonthReportArray[$rw->LC_Id] as $fixedRow) {
                        $fixedTotal += $fixedRow;
                    }
                    echo '<cell name="FixedExpense" title= " ">';
                    echo $fixed     = round($fixedTotal);
                    $colArray[4]    = $colArray[4]+$fixed;
                    echo '</cell>';
                    
                    foreach($BSRptObj->VariableExpOverviewArray[$rw->LC_Id] as $varRow) {
                        $varTotal += $varRow;
                    }
                    echo '<cell name="VariableExpense" title= " ">';
                    echo $variable  = round($varTotal);
                    $colArray[5]    = $colArray[5]+$variable;
                    echo '</cell>';
                    
                    foreach($BSRptObj->GeneralExpOverviewArray[$rw->LC_Id] as $genRow) {
                        $genTotal += $genRow;
                    }
                    echo '<cell name="GeneralExpense" title= " ">';
                    echo $general   = round($genTotal) ;
                    $colArray[6]    = $colArray[6]+$general;
                    echo '</cell>  
                        
                    <cell name="NetBal" title= " ">';
                    echo $netBal = ($business-($statutoryN+$fixed+$variable+$general)) ? round($business-($statutoryN+$tax+$fixed+$variable+$general)) : 0 ;
                    echo '</cell>';
                    
                    $colArray[7]    = $colArray[7]+$netBal;
                    echo $GrossAmount    = $business - ($statutoryN + $tax);
                    
                    $BranchShare =  $GroupShare = 0;
                    if($REQUEST['y']) {
                        foreach($monthRangeArray as $rangeArray) {

                            $grossProfit = $BSRptObj->BussArray[$rw->LC_Id][$rangeArray] - ($BSRptObj->StatutoryOverviewArray[$rw->LC_Id][$rangeArray] + $BSRptObj->TaxArray[$rw->LC_Id][$rangeArray]);
                            $NetProfit   =  $grossProfit - ($BSRptObj->FixedMonthReportArray[$rw->LC_Id][$rangeArray] + $BSRptObj->VariableExpOverviewArray[$rw->LC_Id][$rangeArray] + $BSRptObj->GeneralExpOverviewArray[$rw->LC_Id][$rangeArray]);
                            
                            $BranchShare += ($BonusSettingArray[$rangeArray]->BPS_BranchShare) ? round($NetProfit * ($BonusSettingArray[$rangeArray]->BPS_BranchShare/100)) : 0;
                            $GroupShare  += ($BonusSettingArray[$rangeArray]->BPS_GroupShare) ? round($NetProfit * ($BonusSettingArray[$rangeArray]->BPS_GroupShare/100)) : 0;
                        }         
                    } else {
                        $NetProfit      = $GrossAmount - ($fixed + $variable + $general);
                        $toDate         = strtotime($REQUEST['t']);
                        $BranchShare    = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare) ? round($NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_BranchShare/100)) : 0;
                        $GroupShare     = ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare) ? round($NetProfit * ($BonusSettingArray[date("Y",$toDate).'-'.date("n",$toDate)]->BPS_GroupShare/100)) : 0;
                    }

                    echo '<cell name="BranchShare">'.$BranchShare.'</cell>
                    <cell name = "GroupShare">'.$GroupShare.'</cell>
                    <cell name = "StaffCount">';
                    echo $staffCount = ($rw->StaffCount) ? $rw->StaffCount : 0;
                    echo '</cell> 
                        
                    <cell name="TotalWorking">';
                    echo $totalWorkDays = ($LocDayArray[$rw->LC_Id]) ? $LocDayArray[$rw->LC_Id] : 0; 
                    echo '</cell>
                        
                    <cell>';
                    echo $bonusOneDay = ($BranchShare > 0 && $GroupShare > 0 ) ? round($BranchShare*$LocDayArray[$rw->LC_Id]) : 0;
                    echo '</cell>';
                    $colArray[8]    = $colArray[8]+$BranchShare;
                    $colArray[9]    = $colArray[9]+$GroupShare;
                    $colArray[10]   = $colArray[10]+$staffCount;
                    $colArray[11]   = $colArray[11]+$totalWorkDays;
                    $colArray[12]   = $colArray[12]+$bonusOneDay;
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