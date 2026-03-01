<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/MasterReportsLocationBasedClass.php");
require_once($BASEPATH . "includes/functions.php");
$MstrRptObj = new MasterReportsLocationBasedClass();
$filterData = $REQUEST['filter'];
$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.BS_Status = 1 ';
if($filterData)     $filter .=' AND IT.IT_Id = '.$filterData;
if(!$REQUEST['f'] ) {   // no from date
    if($REQUEST['t']){
        $tyear= date("Y",strtotime($REQUEST['t']))-1;
        $REQUEST['f'] = "01-04-".$tyear;  // show yealry data;
    }
    else {              //  no till date
        $REQUEST['f'] = "01-04-".date("Y")-1;
        $REQUEST['t'] = date('d-m-Y', time());
    }    
}
$fromMonth  = ($REQUEST['f']) ? ltrim(date("m",strtotime($REQUEST['f'])),'0') : 1;
$fromYear   = ($REQUEST['f']) ? date("Y",strtotime($REQUEST['f'])) : date('Y');
$toYear     = ($REQUEST['t']) ? date("Y",strtotime($REQUEST['t'])) : date('Y');

if($REQUEST['t']) {   // if to date
    $toMonth = ltrim(date("m",strtotime($REQUEST['t'])),'0');
} else {  
    $toMonth = ($toYear == date('Y')) ? date('n') : 12;
}
  
if($fromYear == ($toYear-1) && $fromYear == date("Y") ) {   // current financial year
    $toMonth = date('n');   // show upto current month
    $toYear  = date("Y");
} 

if($REQUEST['m']) {   // month is selected
    $toMonth            = ltrim($REQUEST['m'],'0');
//    $fromMonth          = 4;
//    if($toMonth < 4) {   // before april which is starting of a financial year
//        $toYear         = ($REQUEST['t']) ? date("Y",strtotime($REQUEST['t'])) : date('Y');
//        $fromYear       = ($REQUEST['f']) ? (date("Y",strtotime($REQUEST['f']))-1) : (date('Y')-1);
//        $REQUEST['t']   = '31-'.$toMonth.'-'.$toYear;
//    } 
//    $REQUEST['f']       = '01-04-'.$fromYear;

    if(($toMonth >= 4)&&($toMonth <= 9)) { // current financial year
        $fromYear   = date('Y')-1;
        $toYear     = ($REQUEST['m'] <= 3) ? date('Y')-1 : date('Y');
        $REQUEST['f']   = '01-05-'.$fromYear;
        $fromMonth=05;
    } else if($toMonth <= 3) {
        $fromYear   = date('Y')-1;
        $toYear     = ($REQUEST['m'] >= 4) ? date('Y')-1 : date('Y');
        $REQUEST['f']   =  '01-'.($REQUEST['m']+1).'-'.$fromYear;
        $fromMonth = $REQUEST['m']+1;
    } else if($toMonth >= 10) {
        $fromYear   = date('Y');
        $toYear     = ($REQUEST['m'] >= 4) ? date('Y') : date('Y')-1;
        $REQUEST['f']   =  '01-'.($REQUEST['m']-6).'-'.$fromYear;
        $fromMonth = $REQUEST['m']-6;
    }
    $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear; 
} 

$MstrRptObj->reportItemBranchData($filter,$REQUEST['f'],$REQUEST['t']);
$monthArray             = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$MRP_Obj                = $MstrRptObj->MasterReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50"  type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="btn_Sort_LBM"/></div>]]></column>
        <column width="150" type="ro" align="left" ><![CDATA[<div style="float:left"><select type="text" style="width:72%;float:left;" id="LC_LBMR_Name"></select><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="btn_Sort_LBM" style="float:left;"/></div>]]></column>';
    if($MRP_Obj) {
        
        $colNum     = 2;
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

                echo '<column width="*" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_LBM"/> </div>]]></column>';
                $colNum++;
                $monthLimit--;
                $footer         .= ",{#stat_total}";
                $footerStyle    .= ",text-align:right;";
                $cellFormat     .= ",68";   // minimum cell width
            }
        }        
        echo '<column width="100" format = "0,000" type="ron" align="right" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_LBM" /> </div>]]></column>
            <column width="2" type= "ro" align="center"></column>
            <afterInit>
                <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call> 
                <call command="attachFooter"><param>Total,#cspan'.$footer.',{#stat_total},#cspan</param>
                <param>text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';   

        $j = 1;
        $arr_amts=array(); 
        foreach($MRP_Obj as $rw) {
            echo '<row id = "'.$rw->LC_Id.'">
                    <userdata name = "IT_Id" >'.$rw->IT_Id.'</userdata>
                    <cell title= " ">'.$j.'</cell> ';  
            if($rw->LC_Status==0)
            $namecellcolor='style="color:orange;font-weight:bold;"';
            elseif($rw->LC_Status==2)
            $namecellcolor='style="color:red;font-weight:bold;"';
            elseif($rw->LC_Status==1)
            $namecellcolor='';        
                    echo '<cell name="LC_Name" title= " " '.$namecellcolor.'>'.trim(ucfirst($rw->LC_Name)).'</cell>';
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
                        $arr_amts=  arrayFlatten($MstrRptObj->MonthReportArray[$rw->LC_Id]);                        
                        $maxamt=0;
                        $maxamt=max($arr_amts);
                        for($i = $monthLimit; $i >= $month; $i--) {
                                                       
                           
                            if($i == 0) 
                                $i = 12;
                            if($maxamt==$MstrRptObj->MonthReportArray[$rw->LC_Id][$y][$i] && $maxamt>0)
                            $bgcolor='style="background-color:#33cc33;"';
                            else
                            $bgcolor=''; 
                            echo '<cell name="month'.$y.$i.'"  month="'.$i.'" year = "'.$y.'" title= " " '.$bgcolor.'>';
                            if($MstrRptObj->MonthReportArray[$rw->LC_Id][$y][$i]) echo $MstrRptObj->MonthReportArray[$rw->LC_Id][$y][$i]; else echo '0';
                            echo '</cell>';
                            //$monthLimit--;
                        }
                    }

                    echo '<cell name="BSAmount" title= " ">'.round($rw->BS_Amount).'</cell>
                    <cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick=""/>]]></cell>    
            </row>';
            $j++;
        }
    } else { 
        echo ' <column width="*"  type="ro" align="right">Total</column>
                <column width="2"  type="ro" align="center"></column>
            </head>
            <row id="0"> 
                <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
    }   
echo '</rows>';
?>