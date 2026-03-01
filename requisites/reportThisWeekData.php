<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH."preTallyClass/PerformanceReportClass.php");
$PerfRptObj = new PerformanceReportClass();

$filterOB = $filter = 'OF_Id = '.$preTally_user_ofid.' ';
$cfields = 'SUM(OB_OpenBal) AS OB , min(MONTH(OB_Date)) AS BS_Date';
$bfields = 'SUM(BnkOB_OpenBal) AS OB';
$bfilter = 'US.OF_Id = '.$preTally_user_ofid.' ';

$firstDay = strtotime("last monday") ;
$lastDay  = strtotime(date("d-m-Y",strtotime("last monday"))."+6 days");
$sDate    = date("d-m-Y",$firstDay);
$eDate    = date("d-m-Y",$lastDay);

for ($i=$firstDay; $i<=$lastDay; $i+=86400) {
    $dates[]= date("d-m-Y", $i) ;  
}  

//echo $monday = strtotime("last monday"); echo "\n";
//echo $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday; echo "\n";
// 
//echo $sunday = strtotime(date("d-m-Y",$monday)." +6 days"); echo "\n";
// 
//$this_week_sd = date("d-m-Y",$monday);
//$this_week_ed = date("d-m-Y",$sunday);
// 
//echo "Current week range from $this_week_sd to $this_week_ed ";
//
//echo date("l"); echo "\n";
//echo date('l', strtotime($this_week_sd));
//die();

//-------------------------- Cash Opening Balance ----------------------//

$PerfRptObj->getThisYearCashOB($sDate,$eDate,$cfields, $filter);
$COB_Obj = $PerfRptObj->PerfReportArray;

foreach ($COB_Obj as $key => $value) {
    if( $value->MH_Type == 1 ){
        $CSH_INC += $value->IE;
    }
    if( $value->MH_Type == 2 ){
        $CSH_EXP += $value->IE;
    }
    if($value->OB) $CSH_OB = $value->OB  ;
}



//-------------------------- Bank Opening Balance ----------------------//

$PerfRptObj->getThisYearBankOB($sDate,$eDate,$bfields, $filterOB, $bfilter);
$BOB_Obj = $PerfRptObj->PerfReportArray;
//print_r($BOB_Obj);
$BOB['1'] = $BOB['2'] = $BOB['3'] = $BOB['4'] = 0;

$BOB[$BOB_Obj[1]->MH_Type] = $BOB_Obj[1]->IE;
$BOB[$BOB_Obj[2]->MH_Type] = $BOB_Obj[2]->IE;

$BOB['3'] = ($BOB_Obj[0]->OB + $BOB['1']) - $BOB['2'];
$BOB['4'] = ($BOB['3'] + $BNK_INC) - $BNK_EXP;

//-------------------------- Current Year Reports ----------------------//

$PerfRptObj->viewThisMonthCashReports($sDate,$eDate, $filter);
$CRpt_Obj = $PerfRptObj->PerfReportArray;

foreach ($CRpt_Obj as $key => $value) {
    
    $BS_Date= date("d-m-Y", strtotime($value->BS_Date));
    
    if( $value->IT_Business == 0 && $value->MH_Type == 1 ){
        $thisYrArray[$BS_Date]['CSH_INC'] += $value->IE;
    }
    if( $value->IT_Business == 0 && $value->MH_Type == 2 ){
        $thisYrArray[$BS_Date]['CSH_EXP'] += $value->IE;
    }
    if( $value->IT_Business == 1 && $value->MH_Type == 1 ){
        $thisYrArray[$BS_Date]['CSH_BUS'] += $value->IE;
    }
    if( $value->IT_Business == 1 && $value->MH_Type == 2 ){
        $thisYrArray[$BS_Date]['CSH_BUS'] -= $value->IE;
    }
    
    $thisYrArray[$BS_Date]['CSH_PL'] = $thisYrArray[$BS_Date]['CSH_INC'] - $thisYrArray[$BS_Date]['CSH_EXP'] ;
    
}


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
        <head>
            <column width="40" type="ro" align="center" sort="int"> # </column>
            <column width="100" type = "ro" align="center" sort="na">Date</column>
            <column width="100" type = "ro" align="center" sort="na">Day</column>
            <column width="*" type = "ro" align="center" sort="na">Opening Balance</column>
            <column width="*" type = "ro" align="center" sort="na">Business</column>
            <column width="*" type = "ro" align="center" sort="na">Income</column>
            <column width="*" type = "ro" align="center" sort="na">Expense</column>
            <column width="*" type = "ro" align="center" sort="na">Profit / Loss</column>
            <column width="*" type = "ro" align="center" sort="na">Closing Balance</column>
            
            <settings>
		<colwidth>px</colwidth>
            </settings>
                        
            <beforeInit> 
                <call command="setSkin">
                    <param>dhx_skyblue</param>
		</call> 
                <call command="setImagePath">
                    <param>assets/grid/codebase/imgs/</param>
                </call> 
            </beforeInit> 
            
        </head>';
        $j=1;
        foreach ($dates as $value) {
           
            $stDate= date("d-m-Y", strtotime($sDate)); 
            $date = date( (date(d,strtotime($value))- 01 )."-m-Y");
            $pDate = date("d-m-Y", strtotime($date));
            
            $thisYrArray[$stDate]['CSH_OB'] = $CSH_OB + $CSH_INC - $CSH_EXP;
            if(!$thisYrArray[$value]['CSH_OB']) $thisYrArray[$value]['CSH_OB'] = $thisYrArray[$pDate]['CSH_CB'];
            $thisYrArray[$value]['CSH_CB'] = $thisYrArray[$value]['CSH_OB'] + $thisYrArray[$value]['CSH_INC'] - $thisYrArray[$value]['CSH_EXP'] ;
            
            echo '<row id = "'.$j.'" >
                    <cell>'.$j.'</cell>
                    <cell>'.$value.'</cell>
                    <cell>'.date('l', strtotime($value)).'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_OB'].'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_BUS'].'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_INC'].'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_EXP'].'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_PL'].'</cell>
                    <cell>'.$thisYrArray[$value]['CSH_CB'].'</cell>
                    </row>';
            $j++;
        }
echo '</rows>';
?>