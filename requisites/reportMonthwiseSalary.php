<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
} 
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$MsalObj    = new AttendanceClass();
$filterData = $REQUEST['filter']; 
$filter     = ' AND SR.OF_Id = '.$preTally_user_ofid.'';

$year       = ($REQUEST['y']) ? $REQUEST['y'] : date("Y");
$month      = ($REQUEST['m']) ? $REQUEST['m'] : date('n');
if($month == "09" || $month == "04" || $month == "06" || $month == "11"){
    $day    = '30';
}
else if($month == "02") { // february
    if((($year % 4) == "0") && ((($year % 100) != "0") || (($year % 400) == "0"))) // leap year
        $day = '29';
    else 
        $day = '28';
} else{
    $day     = '31';
}
    

if($REQUEST['m']) {   // month is selected
    
    
    
    $toMonth            = ltrim($REQUEST['m'],'m');
    $fromMonth          = 4;
    if($toMonth < 4)    // before april which is starting of a financial year
        $REQUEST['f']   = '01-04-'.(date("Y")-1);
    else 
        $REQUEST['f']   = '01-04-'.date("Y");
    
    $REQUEST['t']       = $day.'-'.$REQUEST['m'].'-'.date('Y');

} else if($REQUEST['y']) {
    
    if($REQUEST['y'] == date("Y") ) {
        $REQUEST['t']   = $day.'-'.date('n').'-'.$REQUEST['y'];
    } else
        $REQUEST['t']   = '31-03-'.($REQUEST['y']+1);
    $REQUEST['f']       = '01-04-'.($REQUEST['y']);
}

$fromDateElements       = explode('-', $REQUEST['f']);
$toDateElements         = explode('-', $REQUEST['t']);

$fromYear               = $fromDateElements[2];
$toYear                 = $toDateElements[2];

if($fromYear == $toYear) {
    $ESR_Month_From  ='04';
    $ESR_Year_From   =$fromYear-1;
    $ESR_Month_To    ='03';
    $ESR_Year_To     =$toYear;

} else {
    $ESR_Month_From  ='04';
    $ESR_Year_From   =$fromYear-1;
    $ESR_Month_To    ='03';
    $ESR_Year_To     =$toYear-1;

}
$fromMonth          = ltrim($fromDateElements[1],0);
$toMonth            = ltrim($toDateElements[1],0);

$MsalObj->getMonthwiseSalRpt($filter,$REQUEST['f'],$REQUEST['t'],$ESR_Month_From,$ESR_Year_From,$ESR_Month_To,$ESR_Year_To,$preTally_user_ofid);
$monthArray         = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$User_Obj           = $MsalObj->getUserArray;


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50"  type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="MS_Sort_BIR"/></div>]]></column>
        <column width="120"  type="ro" align="left" >#combo_filter</column>
        <column width="120"  type="ro" align="left" >#combo_filter</column>';
    if($User_Obj) {
        
        $colNum     = 3;
        $footer     = $footerStyle = $cellFormat = '';
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

                echo '<column width="*" type="ron"  align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="MS_Sort_BIR"/> </div>]]></column>';
                $colNum++;
                $monthLimit--;
                $footer         .= ",{#stat_total}";
                $footerStyle    .= ",text-align:right;";
                $cellFormat     .= ",70";   // minimum cell width
            }
        }
        echo '<column width="140"  type="ron" align="left" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="MS_Sort_BIR" /> </div>]]></column>
            
            <afterInit>
                <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call> 
                <call command="attachFooter"><param>Total,#cspan,#cspan'.$footer.',{#stat_total}</param>
                <param>text-align:left'.$footerStyle.',text-align:right,text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';   

        $j = 1;
        foreach($User_Obj as $rw) {
            $total = "0";
            echo '<row id = "'.$rw->LC_Id.'">
                    <userdata name = "IT_Id" >'.$rw->IT_Id.'</userdata>
                    <cell title= " ">'.$j.'</cell> ';    
                    echo '<cell name="US_Name" title= " ">'.$rw->Name.'</cell>';
                    echo '<cell name="LC_Name" title= " ">'.$rw->LC_Name.'</cell>';//LC_Name
                    
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
//                            $businessReturned = ($BSRptObj->BussReturnedArray[$rw->LC_Id][$monthLimit][$y]) ? $BSRptObj->BussReturnedArray[$rw->LC_Id][$monthLimit][$y] : 0;
//                            $businessReceived = ($BSRptObj->BussRecvArray[$rw->LC_Id][$monthLimit][$y]) ? ($BSRptObj->BussRecvArray[$rw->LC_Id][$monthLimit][$y]) : 0;
//                            echo $business = ($businessReceived-$businessReturned);
//                            $totBusiness += $business;
                           echo ($MsalObj->monthSalArray[$rw->US_Id][$monthLimit][$y]) ? $MsalObj->monthSalArray[$rw->US_Id][$monthLimit][$y] : 0;
                           $total += ($MsalObj->monthSalArray[$rw->US_Id][$monthLimit][$y]) ? $MsalObj->monthSalArray[$rw->US_Id][$monthLimit][$y] : 0;
                            
                            echo '</cell>';
                            $monthLimit--;
                        }
                    }
                    echo '<cell name="Total" title= " ">'.$total.'</cell>
                    
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