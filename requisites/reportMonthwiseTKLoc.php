<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/TrackReportClass.php");
$AtObj          = new TrackReportClass();

$monthArray     = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$filterData     = explode(",",$REQUEST['filter']);
$filter         = ' AJ.AJ_Status != 0 AND AJ.AJ_Status != 2 AND AL.ALC_Id != "NULL" AND AJ.OF_Id = '.$preTally_user_ofid;
$fields         = " AL.ALC_Name, AL.ALC_Id,SUM(AB.ABP_AmountRecieved) AS ABP_AmountRecieved,CT.CT_Id,ST.ST_Id  ";
$flt            = " GROUP BY AL.ALC_Id ";

if (!isset($_GET["posStart"]))
    $_GET["posStart"]   = 0;
if (!isset($_GET["count"]))
    $_GET["count"]      = 50;

if($filterData[0]) {
    $REQUEST['m'] = $filterData[0];
}

if($filterData[1]) {
    $REQUEST['y'] = $filterData[1];
}

if($filterData[2] && is_numeric($filterData[2])) {
    $filter     .= ' AND AJ.CT_Id = '.$filterData[2]; 
}

if($filterData[3] && is_numeric($filterData[3])) {
    $filter     .= ' AND CT.ST_Id = '.$filterData[3];
}

if($filterData[4]) {   
    $filter     .= ' AND AL.ALC_Id = '.$filterData[4];
}

if($filterData[5]) {   // after headers are loaded
    $flag       = 1;
}
$slNo = 1;      // ascending sl no:
if($filterData[6] != '' && $filterData[7] != '') {   // after headers are loaded
   
    if($filterData[7]  ==  0){
        $ascFlter    =   "ASC";
    } else if($filterData[7] ==  1){
        $ascFlter    =   "DESC";
    }  

    if($filterData[6] == 'a' || $filterData[6] == 'b') {
        if($filterData[6] == 'a' && $ascFlter == "DESC")
            $slNo = 2;              // descending sl no:
        
        $flt    .=  " ORDER BY AL.ALC_Name  $ascFlter";
    } else if($filterData[6] != 13 ) {

        $fields .= " ,SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 1 THEN AB.ABP_AmountRecieved ELSE 0 END) AS January,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 2 THEN AB.ABP_AmountRecieved ELSE 0 END) AS February,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 3 THEN AB.ABP_AmountRecieved ELSE 0 END) AS March,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 4 THEN AB.ABP_AmountRecieved ELSE 0 END) AS April,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 5 THEN AB.ABP_AmountRecieved ELSE 0 END) AS May,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 6 THEN AB.ABP_AmountRecieved ELSE 0 END) AS June,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 7 THEN AB.ABP_AmountRecieved ELSE 0 END) AS July,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 8 THEN AB.ABP_AmountRecieved ELSE 0 END) AS August,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 9 THEN AB.ABP_AmountRecieved ELSE 0 END) AS September,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 10 THEN AB.ABP_AmountRecieved ELSE 0 END) AS October,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 11 THEN AB.ABP_AmountRecieved ELSE 0 END) AS November,
                SUM(CASE WHEN MONTH(AJ.AJ_ReceivedDate) = 12 THEN AB.ABP_AmountRecieved ELSE 0 END) AS December"; 
        $flt    .= " ORDER BY ".$monthArray[$filterData[6]]."  ".$ascFlter;
    } else {
        $flt    .= " ORDER BY SUM(AB.ABP_AmountRecieved)  ".$ascFlter;
    }
} else {
    $flt        .= " ORDER BY AL.ALC_Name ASC";
}

$year           = ($REQUEST['y']) ? $REQUEST['y'] : date("Y");

if($REQUEST['m']) {   // month is selected
    /*if((date('n') >= 4)&&(date('n') <= 9)) { // current financial year
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

$fromMonth              = ltrim($fromDateElements[1],0);
$toMonth                = ltrim($toDateElements[1],0);

$AtObj->reportLocData($fields,$filter,$REQUEST['f'],$REQUEST['t'],$_GET["posStart"],$_GET["count"],$flt);        
$monthArray             = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$Loc_Obj               = $AtObj->LocReportArray;

$AtObj->getLocFooter($filter,$REQUEST['f'],$REQUEST['t']);
$Count                  = $AtObj->reportLocDataCount($filter,$REQUEST['f'],$REQUEST['t']);

if($tot_count < $_GET["count"]) $_GET["count"] = $Count;
$colArray               = array();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>' ;
    if (($_GET["posStart"] == 0 && !isset($flag))) {
        echo '<head>
        <beforeInit> 
            <call command="setSkin">
                <param>dhx_skyblue</param>
            </call> 
            <call command="setImagePath">
                <param>assets/grid/codebase/imgs/</param>
            </call> 
        </beforeInit> 		
        <column type="ro" sort = "na" align="center" width ="60" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="a" class="tkLC_Sort_BIR"/></div>]]></column>    
        <column width="150" sort = "na"  type="ro" align="left" ><![CDATA[<div><div id="loctnFlt" style="width: 70%;float:left;" placeholder="City"></div><div style="float:left;margin-left:3px;margin-top:3px;"><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="b" class="tkLC_Sort_BIR"/></div></div>]]></column>';
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
                $colArray[$colNum] = 0;
                echo '<column width="*" sort = "na" type="ron" align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$monthLimit.'" class="tkLC_Sort_BIR"/> </div>]]></column>';
                $monthLimit--;
                $footer         .= ",";
                $footerStyle    .= ",text-align:right;";
                $toolTip        .= ",false";
                $cellFormat     .= ",70";   // minimum cell width
                $colNum++;
            }
        }
        $colArray[$colNum] = 0;  // for total column
        echo '<column  sort = "na" type="ron" align="right" width = "140" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="13" class="tkLC_Sort_BIR" /> </div>]]></column>
            <afterInit>
                <call command="enableTooltips"><param>false,false'.$toolTip.',false</param></call>
                <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call> 
                <call command="attachFooter"><param>Total,#cspan'.$footer.',</param>
                <param>text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
        </afterInit> ';
        echo '</head>';   
    }
    if($Loc_Obj) {
        $j = ($slNo == 2) ? $Count : $_GET["posStart"]+1;
        foreach($Loc_Obj as $rw) {
            echo '<row id = "'.$rw->ALC_Id.'">
                <userdata name = "ST_Id" >'.$rw->ST_Id.'</userdata> 
                <userdata name = "CT_Id" >'.$rw->CT_Id.'</userdata>     
                <cell title = " ">'.$j.'</cell> ';                        
                echo '<cell name = "ALC_Name" title = " ">'.$rw->ALC_Name.'</cell>';
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
                        echo $amount       = ($AtObj->LocMonthReportArray[$rw->ALC_Id][$y][$monthLimit]) ? $AtObj->LocMonthReportArray[$rw->ALC_Id][$y][$monthLimit] : 0;
                        $colArray[$colNum] = $colArray[$colNum]+$amount;
                        echo '</cell>';
                        $colArray[$colNum] = ($AtObj->FooterArray[$y][$monthLimit]) ? $AtObj->FooterArray[$y][$monthLimit] : 0;
                        $monthLimit--;
                        $colNum++;
                    }
                }

                echo '<cell name="AJ_TotalAmount" title= " ">';
                echo $total         = ($rw->ABP_AmountRecieved) ? $rw->ABP_AmountRecieved : 0;
                $colArray[$colNum]  = $colArray[$colNum]+$total;
                echo '</cell>
            </row>';
            if($slNo == 2)
                $j--;
            else
                $j++; 
        }
        echo '<userdata name="colSum">'.json_encode($colArray).'</userdata>';
    }
echo '</rows>';
?>