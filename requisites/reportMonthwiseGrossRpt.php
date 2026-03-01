<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/BusinessBonusReportClass.php");
require_once($BASEPATH . "includes/functions.php");

$BSRptObj   = new BusinessBonusReportClass();
$filter     = 'LC.OF_Id = '.$preTally_user_ofid;
if(isset($REQUEST['LC_Id']) && is_numeric($REQUEST['LC_Id']))
    $filter .= ' AND LC.LC_Id = '.$REQUEST['LC_Id'];

$year       = ($REQUEST['y']) ? $REQUEST['y'] : date("Y");
$month      = ($REQUEST['m']) ? $REQUEST['m'] : date('n');

if($REQUEST['m']) {   // month is selected
   /* if((date('n') >= 4)&&(date('n') <= 9)) { // current financial year
        $fromYear   = $year-1; 
        $toYear     = ($REQUEST['m'] <= 3) ? date('Y')-1 : date('Y');
        $REQUEST['f']   = '01-10-'.$fromYear;
    } else if(date('n') <= 3) {
        $fromYear   =  $year-1;
        $toYear     = ($REQUEST['m'] >= 4) ? date('Y')-1 : date('Y');
        $REQUEST['f']   =  '01-'.($REQUEST['m']+6).'-'.$fromYear;
    } else if(date('n') >= 10) {
        $fromYear   = date('Y');
        $toYear     = ($REQUEST['m'] >= 4) ? date('Y') : date('Y')-1;
        $frmMonth    = ($REQUEST['m'] > 6) ? ($REQUEST['m']-6) : ($REQUEST['m']+6);
        if($REQUEST['m'] > 6)$frmMonth=$REQUEST['m']-6;
        else{
            $fromYear   = date('Y')-1; 
            $frmMonth   = $REQUEST['m']+6;            
        } 
        $REQUEST['f']   =  '01-'.$frmMonth.'-'.$fromYear;
    } */
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

$fromDateElements       = explode('-', $REQUEST['f']);
$toDateElements         = explode('-', $REQUEST['t']);

$fromYear               = $fromDateElements[2];
$toYear                 = $toDateElements[2];

$fromMonth          = ltrim($fromDateElements[1],0);
$toMonth            = ltrim($toDateElements[1],0);

$BSRptObj->reportGrossData($filter,$REQUEST['f'],$REQUEST['t'],$preTally_user_ofid);
$monthArray         = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$MRP_Obj            = $BSRptObj->BranchArray;
$GrossArray         = array();

foreach($MRP_Obj as $rw) {
    for($y = $toYear; $y >= $fromYear; $y--) {
        $monthLimit = 12;
        if($y == $fromYear) {
            if($fromYear == $toYear) {
                $monthLimit = $toMonth;
            } 
            $month = $fromMonth;
        } else {
            $month = 1;  
            if($y == $toYear)  // upto the year
                $monthLimit = $toMonth;
        }
        for($i = $monthLimit; $i >= $month; $i--) {
            if($monthLimit == 0) 
                $monthLimit = 12;
            $GrossArray[$rw->LC_Id][$y][$i] = round($BSRptObj->BusinessArray[$rw->LC_Id][$y][$i]) - (round($BSRptObj->StatMonthReportArray[$rw->LC_Id][$y][$i]) + round($BSRptObj->TaxArray[$rw->LC_Id][$y][$i]));
            $monthLimit--;
        }
    }
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50"  type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="gross_Sort_BIR"/></div>]]></column>
        <column width="120"  type="ro" align="left" ><![CDATA[<div><div id="branchGross" style="width: 70%;float:left;" placeholder="Branch"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="gross_Sort_BIR"/></div></div>]]></column>';
    if($MRP_Obj) {
        
        $colNum     = 2;
        $footer     = $footerStyle = $cellFormat = $toolTip = '';
        for($y = $toYear; $y >= $fromYear; $y--) {
            $monthLimit = 12;
            if($y == $fromYear) {
                if($fromYear == $toYear) 
                    $monthLimit = $toMonth;   // current month; if current year
                $month = $fromMonth;
            }  else {
                $month = 1;         // from January
                if($y == $toYear)  // upto the year
                    $monthLimit = $toMonth;
            }

            for($i = $monthLimit; $i >= $month; $i--) {
                if($monthLimit == 0)   // start from December of next year
                   $monthLimit = 12;

                echo '<column width="*" format = "0,000" type="ron" align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="gross_Sort_BIR"/> </div>]]></column>';
                $colNum++;
                
                $monthLimit--;
                $footer         .= ",{#stat_total}";
                $footerStyle    .= ",text-align:right;";
                $toolTip        .= ",false";
                $cellFormat     .= ",70";   // minimum cell width
                
            }
        }
        echo '<column width="140" format = "0,000" type="ron" align="right" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="gross_Sort_BIR" /> </div>]]></column>
            <afterInit>
                <call command="enableTooltips"><param>false,false'.$toolTip.',false</param></call>
                <call command="setColumnMinWidth"><param>,'.$cellFormat.',</param></call> 
                <call command="attachFooter"><param>Total,#cspan'.$footer.',{#stat_total}</param>
                <param>text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';   
        $arr_amts = array(); 
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
                    <userdata name = "IT_Id" >'.$rw->IT_Id.'</userdata>
                    <cell title= " ">'.$j.'</cell> ';                        
                    echo '<cell name="LC_Name" title= " " '.$namecellcolor.'>'.$rw->LC_Name.'</cell>';
                    $totBusiness = 0;
                    for($y = $toYear; $y >= $fromYear; $y--) {
                        
                        $monthLimit = 12;
                        if($y == $fromYear) {
                            if($fromYear == $toYear) {
                                $monthLimit = $toMonth;
                            } 
                            $month = $fromMonth;
                        } else {
                            $month = 1;  
                            if($y == $toYear)  // upto the year
                                $monthLimit = $toMonth;
                        }
                        $arr_amts   =  arrayFlatten($GrossArray[$rw->LC_Id]);                        
                        $maxamt     =  0;
                        $maxamt     =  max($arr_amts);
                        for($i = $monthLimit; $i >= $month; $i--) {
                            if($monthLimit == 0) 
                                $monthLimit = 12;

                            if($maxamt == $GrossArray[$rw->LC_Id][$y][$i] && $maxamt > 0)
                                $bgcolor = 'style="background-color:#33cc33;"';
                            else
                                $bgcolor = ''; 
                            echo '<cell name="month'.$y.$i.'" title= " " '.$bgcolor.'>';
                            echo $business = ($GrossArray[$rw->LC_Id][$y][$monthLimit]) ? round($GrossArray[$rw->LC_Id][$y][$monthLimit]) : 0;
                            $totBusiness += $business;
                            echo '</cell>';
                            $monthLimit--;
                        }
                    }
                    echo '<cell name="BSAmount" title= " ">'.$totBusiness.'</cell>
            </row>';
            $j++;
        }
    } else { 
        echo ' <column width="*"  type="ro" align="right">Total</column>
            </head>
            <row id="no_record"> 
                <cell colspan="3" title= " "><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    }   
echo '</rows>';
?>