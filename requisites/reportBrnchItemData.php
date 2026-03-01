<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/MasterReportClass.php");
require_once($BASEPATH . "includes/functions.php");
$MstrRptObj = new MasterReportClass();
$filterData = $REQUEST['filter'];
$type_filtr = $REQUEST['type_filtr'];
$item_filtr = $REQUEST['item_filtr'];
$subhead_filter = $REQUEST['subhead_filter'];
$mainhead_filter = $REQUEST['mainhead_filter'];
$amount_filter = $REQUEST['amount_filter'];
//$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND BS.BS_Status = 1 ';
$filter     = 'LC.OF_Id = '.$preTally_user_ofid.' AND (BS.BS_Status = 1 OR BS.BS_Status = 2)'; //06-12-2024
//if($filterData && $REQUEST['mode']=='2')     $filter .=' AND IT.IT_Id IN ('.$filterData.')';
if($type_filtr !='null' && $type_filtr !=''){
    if($type_filtr=="InternalTransferReceived")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Transfers =1';
    if($type_filtr=="InternalTransferPaid")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Transfers =1';
    if($type_filtr=="BusinessReceived")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =1';
    if($type_filtr=="BusinessReturned")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =1';
    if($type_filtr=="Income")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =0 AND IT.IT_Transfers =0';
    if($type_filtr=="Expense")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =0 AND IT.IT_Transfers =0';    
}   
    // 26-12-2024 Start
    $filterAry      = [];
    if (isset($REQUEST['item_text_filtr']) && $REQUEST['item_text_filtr'] != "") {
        $filterAry      = explode('-$Plus$-',$REQUEST['item_text_filtr']);
        foreach($filterAry As $pname) {  
            $filter     .= (trim($pname) != '') ? " AND IT.IT_Name LIKE '%" . trim($pname) . "%'":"";
        }
    }
    // End 
if($filterData !='null' && $filterData !='')     $filter .=' AND IT.IT_Id ='.$filterData.' ';
if($subhead_filter !='null' && $subhead_filter !='')     $filter .=' AND SH.SH_Name ="'.$subhead_filter.'" ';
if($mainhead_filter !='null' && $mainhead_filter !='')     $filter .=' AND MH.MH_Name ="'.$mainhead_filter.'" ';
if($amount_filter !='null' && $amount_filter !='')     $filter .=' AND BS.BS_Amount ="'.$amount_filter.'" ';
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

   /* if(($toMonth >= 4)&&($toMonth <= 9)) { // current financial year
        $fromYear   = date('Y')-1;
        $toYear     = ($REQUEST['m'] <= 3) ? date('Y')-1 : date('Y');
        $REQUEST['f']   = '01-10-'.$fromYear;
        $fromMonth=10;
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
    }*/
     $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $fromYear   = date('Y')-1;
    $toYear     = date('Y');
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear;   
} 

//$MstrRptObj->reportItemBranchData($filter,$REQUEST['f'],$REQUEST['t']); 
$MstrRptObj->reportItmBrnchData($filter,$REQUEST['f'],$REQUEST['t']); //06-12-2024
$monthArray             = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$MRP_Obj                = $MstrRptObj->MasterReportArray;
//echo "---".$MstrRptObj->sql."-------";
//print_r($MRP_Obj);
//die();
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50"  type="ro" align="center" ><![CDATA[<div style="text-align:left;">SlNo<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="btn_Sort_BIR"/></div>]]></column>
        <column width="150" type="ro" align="left" ><![CDATA[<div style="float:left; "><select type="text" style="width:70%;float:left;" id="LC_MR_Name"></select></div><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="btn_Sort_BIR" style="float:right;"/>]]></column>';
    if(!empty($MRP_Obj)) {
        
        $subHead    = ''; // 29-11-2024
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

            /*for($i = $monthLimit; $i >= $month; $i--) {
                if($monthLimit == 0)   // start from December of next year
                   $monthLimit = 12;

                echo '<column width="*" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR"/> </div>]]></column>';
                //$footer         .= ",{#stat_total}";
                $footer         .= ",<div id='totMstr_".$monthLimit.'_'.$y."' month='".$monthLimit."' mode='".$REQUEST['mode']."' year='".$y."' class='MastRBfooter' style='cursor: pointer;'>{#stat_total}</div>";
                $footerStyle    .= ",text-align:right;";
                $cellFormat     .= ",68";   // minimum cell width
                $colNum++;
                $monthLimit--;
            }*/
            // chnaged at 29-11-2024
            for($i = $monthLimit; $i >= $month; $i--) {
                if($monthLimit == 0)   // start from December of next year
                   $monthLimit = 12;

                echo '<column width="*" type="ron" format = "0,000" align="right" ><![CDATA[<div style="text-align:center;">'.$monthArray[$monthLimit] .' '.$y .'</div>]]></column><column width="*" format = "0,000" type="ron" align="right">#cspan</column>';
                //$footer         .= ",{#stat_total}";
                $footer         .= ",<div id='totMstr_".$monthLimit.'_'.$y."' month='".$monthLimit."' mode='".$REQUEST['mode']."' year='".$y."' class='MastRBfooter' style='cursor: pointer;'>{#stat_total}</div>,<div id='totMstr_".$monthLimit.'_'.$y."' month='".$monthLimit."' mode='".$REQUEST['mode']."' year='".$y."' class='MastRBfooter' style='cursor: pointer;'>{#stat_total}</div>";
                $footerStyle    .= ",text-align:right;,text-align:right;";
                $cellFormat     .= ",80,80";   // minimum cell width
                
                $subHead        .= '<div style="float:left;" class="" data-month="'.$monthLimit.'" data-year="'.$y.'">Verified<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR" colSortTyp="num"/> </div>,';
                $colNum++;
                $subHead        .= '<div style="float:left;" class="" data-month="'.$monthLimit.'" data-year="'.$y.'">Unverified<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR" colSortTyp="num"/> </div>,';
                $colNum++;
                $monthLimit--;
            }
        }  
        //29-11-24  
        echo '<column  width="90" format="0,000" type="ron" align="right"><![CDATA[<div style="text-align:center;"> Total </div>]]></column><column format = "0,000"  type="ron" align="right" width = "90">#cspan</column>';   
        $subHead.='<div style="float:left;">Verified<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR totalMonthCell" colSortTyp="num" /> </div>,';
        $colNum++;
        $subHead.='<div style="float:left;">Unverified<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR totalMonthCell" colSortTyp="num" /> </div>';
        // End
        //echo '<column width="100" format = "0,000" type="ron" align="right" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR" /> </div>]]></column>';
        echo '<column width="2" type= "ro" align="center"></column>            
            <afterInit>';
        //29-11-2024
        echo  '<call command="attachHeader"><param><![CDATA[,#cspan,'.$subHead.']]></param></call>';
        // end    
        echo  '<call command="setColumnMinWidth"><param>,'.$cellFormat.',,,</param></call> 
                <call command="attachFooter"><param><![CDATA[Total,#cspan'.$footer.',{#stat_total},{#stat_total},#cspan]]></param>
                <param>text-align:left'.$footerStyle.',text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';   
         echo '<userdata name = "IT_IdMain" >'.$filterData.'</userdata>';
        $j = 1;
        $arr_amts=array(); 
        foreach($MRP_Obj as $rw) {
            /*echo '<row id = "'.$rw->LC_Id.'">
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
                        } */ 

            //06-12-2024 start
            echo '<row id = "'.$rw['LC_Id'].'">
                <userdata name = "IT_Id" >'.$rw['IT_Id'].'</userdata>
                <cell title= " ">'.$j.'</cell>';  
            switch($rw['LC_Status']) {
                case 0  : $namecellcolor='style="color:orange;font-weight:bold;"';
                break;
                case 2  : $namecellcolor='style="color:red;font-weight:bold;"';
                break;
                case 1  : $namecellcolor='';
                break;
                default : $namecellcolor=''; 
                break;
            }
            echo '<cell name="LC_Name" title= " " '.$namecellcolor.'>'.trim(ucfirst($rw['LC_Name'])).'</cell>';
            for($y = $toYear; $y >= $fromYear; $y--) {
                $monthLimit = 12;
                if($y == $fromYear) {
                    if($fromYear == $toYear) {
                        $monthLimit = $toMonth;
                    } 
                    $month = $fromMonth;
                } else {
                    $month = 1;  
                    if ($y == $toYear) { // upto the year
                        $monthLimit = $toMonth;
                    }
                }  
                $vamtary    = (isset($rw[1])) ? arrayFlatten($rw[1]) :[0];
                //$uamtary    = (isset($rw[2])) ? arrayFlatten($rw[2]):[0];
                $vmaxamt    = 0;
                //$umaxamt    = 0;
                $vmaxamt    = max($vamtary);
                //$umaxamt    = max($uamtary);                       
                for($i = $monthLimit; $i >= $month; $i--) {
                    $i = ($i == 0) ? 12 : $i;    
                    // verified
                    $bgcolor = ($vmaxamt>0 && $vmaxamt==$rw[1][$y."-".$i]) ? 'style="background-color:#33cc33;"':''; 
                    echo '<cell name="month'.$y.$i.'"  month="'.$i.'" year = "'.$y.'" title= " " '.$bgcolor.'>';
                    echo (isset($rw[1][$y."-".$i]) && $rw[1][$y."-".$i]) ? $rw[1][$y."-".$i] : '0';
                    echo '</cell>';

                    //unverified
                    //$bgcolor = ($umaxamt>0 && $umaxamt==$rw[2][$y."-".$i]) ? 'style="background-color:#33cc33;"':''; 
                    $amtunv     = (isset($rw[2]) && isset($rw[2][$y."-".$i])) ? $rw[2][$y."-".$i]: 0;
                    $bgcolor = ($amtunv > 0) ? 'style="background-color:#ed9f3c;"':'';    
                    echo '<cell name="month'.$y.$i.'"  month="'.$i.'" year = "'.$y.'" title= " " '.$bgcolor.'>';
                    echo $amtunv;
                    echo '</cell>';
                }
                        //end 06-12-2024
                        /*$arr_amts=  arrayFlatten($MstrRptObj->MonthReportArray[$rw->LC_Id]);                        
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
                        }*/
                    }
                    // 06-12-2024 start
                    echo '<cell name="BSAmount" title= " ">'.round($rw[0][1]).'</cell>'; 
                    echo '<cell name="BSAmountu" title= " ">'.round($rw[0][2]).'</cell>'; 
                        //end 06-12-2024
                    /*
                    echo '<cell name="BSAmount" title= " ">'.round($rw->BS_Amount).'</cell>';
                    */                    
                    echo '<cell name="View" title="Click to view more details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw['IT_Id'].'"  onclick=""/>]]></cell>    
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