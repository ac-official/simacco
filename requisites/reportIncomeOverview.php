<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/IncomeProfitReportClass.php");
$BSRptObj       = new IncomeProfitReportClass();
if(is_numeric($REQUEST['y']))
    $year=$REQUEST['y'];
else
    $year=date("Y");
//$year           = $REQUEST['y'] ? $REQUEST['y'] : date("Y");   
$day            =  cal_days_in_month(CAL_GREGORIAN, ltrim($REQUEST['m'],'0'), $year);    
if($REQUEST['m']) {   // month is selected
    $flag = 'm';   // month flag
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$year;
    
    if($REQUEST['m'] == date('m'))   // current month
        $day = date('d');
    
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$year;
} else if($REQUEST['y']) {
    $flag = 'y';
    
   /* if(($year+1) == date('Y'))
        $REQUEST['t']   = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y')).'-'.date('n').'-'.($year+1);
    else */
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

$BSRptObj->reportReportOverview($REQUEST['f'],$REQUEST['t'],$preTally_user_ofid,$filter);
$monthArray         = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$MRP_Obj            = $BSRptObj->BranchArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50" type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="ovInc_Sort_BIR"/></div>]]></column>
        <column width="150"  type="ro" align="left" ><![CDATA[<div><div id="ovIncBranchItmF" style="width: 70%;float:left;" placeholder="Branch"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="ovInc_Sort_BIR"/></div></div>]]></column>
        <column width="160" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Business <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="2" class="ovInc_Sort_BIR" /> </div>]]></column>
        <column width="160" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Statutory Expense <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="3" class="ovInc_Sort_BIR" /> </div>]]></column>
        <column width="160" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Branch Fixed <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="4" class="ovInc_Sort_BIR" /> </div>]]></column>
        <column width="160" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Branch Flexi <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="5" class="ovInc_Sort_BIR" /> </div>]]></column>
        <column width="160" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> General Expense <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="6" class="ovInc_Sort_BIR" /> </div>]]></column>
        <column width="*" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;"> Net Balance <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="7" class="ovInc_Sort_BIR" /> </div>]]></column>
        <afterInit>
            <call command="enableTooltips"><param>false,false,false,false,false,false,false,false</param></call>
            <call command="attachFooter"><param>Total,#cspan,{#stat_total},{#stat_total},{#stat_total},{#stat_total},{#stat_total},{#stat_total}</param>
            <param>text-align:left,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right,text-align:right</param></call>
        </afterInit>
        </head>';   
    if($MRP_Obj) {
        $j = 1;
        foreach($MRP_Obj as $rw) {
             $namecellcolor=''; 
            if($rw->LC_Status==0)
            $namecellcolor='style="color:#ff6600;font-weight:bold;"';
            elseif($rw->LC_Status==2)
            $namecellcolor='style="color:red;font-weight:bold;"';
            elseif($rw->LC_Status==1)
            $namecellcolor=''; 
            
            echo '<row id = "'.$rw->LC_Id.'">
                    <cell title= " ">'.$j.'</cell>                   
                    <cell name="LC_Name" title= " " '.$namecellcolor.'>'.$rw->LC_Name.'</cell>
                    <cell name="Business" title= " ">';
            
                    $businessReturned = ($BSRptObj->BussReturnedArray[$rw->LC_Id]) ? round($BSRptObj->BussReturnedArray[$rw->LC_Id]) : 0;
                    $businessReceived = ($BSRptObj->BussRecvArray[$rw->LC_Id]) ? round($BSRptObj->BussRecvArray[$rw->LC_Id]) : 0;
                    $business = ($businessReceived-$businessReturned);
                    if($business != 0) echo number_format($business, 2, '.', ''); else echo 0;
                    echo '</cell>
                    <cell name="Statutory" title= " ">';
                    echo $statutory = (!empty($BSRptObj->StatutoryOverviewArray)) ? round($BSRptObj->StatutoryOverviewArray[$rw->LC_Id]) : '0';
                    echo '</cell>
                    <cell name="FixedExpense" title= " ">';
                    echo $fixed     = (!empty($BSRptObj->FixedExpOverviewArray )) ? round($BSRptObj->FixedExpOverviewArray[$rw->LC_Id]) : '0' ;
                    echo '</cell>
                    <cell name="VariableExpense" title= " ">';
                    echo $variable  = (!empty($BSRptObj->VariableExpOverviewArray )) ? round($BSRptObj->VariableExpOverviewArray[$rw->LC_Id]) : '0' ;
                    echo '</cell>
                    <cell name="GeneralExpense" title= " ">';
                    echo $general   = (!empty($BSRptObj->GeneralOverviewArray )) ? round($BSRptObj->GeneralOverviewArray[$rw->LC_Id]) : '0' ;
                    echo '</cell>    
                    <cell name="NetBal" title= " ">';
                    echo ($business-($statutory+$fixed+$variable+$general)) ? $business-($statutory+$fixed+$variable+$general) : 0 ;
                    echo '</cell>
            </row>';
            $j++;
        }
    } else { 
        echo '<row id="no_record"> 
                <cell colspan="8" title= " "><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    }   
echo '</rows>';
?>