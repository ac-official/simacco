<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/TrackReportClass.php");
$AtObj      = new TrackReportClass();
$monthArray = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$filter     = "UA.OF_Id = $preTally_user_ofid ";
$statusFilter ="AND AE.AE_Status = 1";

$filterData = explode(",",$REQUEST['filter']);
if($filterData[0] != 0 && is_numeric($filterData[0]))
    $filter .= ' AND ST.ST_Id = '.$filterData[0];

$flag = ($filterData[1]) ? 1 :  0;

$year       = ($REQUEST['y']) ? $REQUEST['y'] : date("Y");

if($REQUEST['m']) {   // month is selected
   /* if((date('n') >= 4)&&(date('n') <= 9)) { // current financial year
        $fromYear   = date('Y')-1;
        $toYear     = ($REQUEST['m'] <= 3) ? date('Y')-1 : date('Y');
        $REQUEST['f']   = '01-10-'.$fromYear;
    } else if(date('n') <= 3) {
        $fromYear   = date('Y')-1;
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
    }*/
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
            $toYear     = (date('n') >= 4) ? date('Y'): date('Y')-1 ;
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

$fromMonth              = ltrim($fromDateElements[1],0);
$toMonth                = ltrim($toDateElements[1],0);

$AtObj->reportEnquiryCountStateData($filter,$REQUEST['f'],$REQUEST['t'],$statusFilter);
$City_Obj    = $AtObj->EnquiryCountReportArray;

$AtObj->getEnquiryStateFooter($filter,$REQUEST['f'],$REQUEST['t'],$statusFilter);
$colArray               = array();


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';

    if(!$flag) {
    echo '<head>
    <beforeInit> 
        <call command="setSkin">
            <param>dhx_skyblue</param>
        </call> 
        <call command="setImagePath">
            <param>assets/grid/codebase/imgs/</param>
        </call> 
    </beforeInit> 		
    <column type="ro" sort = "na" align="center" width ="60" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="tkSt_Sort_BIR"/></div>]]></column>    
    <column width="150"  sort = "na" type="ro" align="left" ><![CDATA[<div><div id="EstateFlt" style="width: 70%;float:left;" placeholder="City"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="tkSt_Sort_BIR"/></div></div>]]></column>';

    $colNum     = 2;
    $footer     = $footerStyle = $cellFormat  = $toolTip = '';
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
            $colArray[$colNum] = 0;
            echo '<column width="*" sort = "na"  type="ron" align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="tkSt_Sort_BIR"/> </div>]]></column>';
            $monthLimit--;
            $footer         .= ",";
            $toolTip        .= ",false";
            $footerStyle    .= ",text-align:right;";
            $cellFormat     .= ",70";   // minimum cell width
            $colNum++;
        }
    }
    $colArray[$colNum] = 0;  // for total column
    echo '<column  sort = "na"  type="ron" align="right" width = "140" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="tkSt_Sort_BIR" /> </div>]]></column>
        <afterInit>
            <call command="enableTooltips"><param>false,false'.$toolTip.',false</param></call>
            <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call> 
            <call command="attachFooter"><param>Total,#cspan'.$footer.',</param>
            <param>text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
    </afterInit> ';
    echo '</head>';   
    }
    if($City_Obj) {
        $j = 1;
        foreach($City_Obj as $rw) {
            echo '<row id = "'.$rw->ST_Id.'">
                <cell title= " ">'.$j.'</cell> ';                        
                echo '<cell name="CT_Name" title= " ">'.$rw->ST_Name.'</cell>';
                $colNum         = 2;
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

                        echo '<cell name="month'.$y.$i.'" title= " ">';
                        echo $amount        = ($AtObj->EStateMonthReportArray[$rw->ST_Id][$y][$monthLimit]) ? $AtObj->EStateMonthReportArray[$rw->ST_Id][$y][$monthLimit] : 0;
                        $colArray[$colNum]  = $colArray[$colNum]+$amount;
                        echo '</cell>';
                        $colArray[$colNum]  = ($AtObj->FooterArray[$y][$monthLimit]) ? $AtObj->FooterArray[$y][$monthLimit] : 0;
                        $monthLimit--;
                        $colNum++;
                    }
                }
                echo '<cell name="AJ_TotalAmount" title= " ">';
                
                echo $total         = ($rw->ENQUIRY) ? $rw->ENQUIRY : 0;
                $colArray[$colNum]  = $colArray[$colNum]+$total;
                echo '</cell>
            </row>';
            $j++;
        }
        echo '<userdata name="colSum">'.json_encode($colArray).'</userdata>';
    }  
echo '</rows>';
?>