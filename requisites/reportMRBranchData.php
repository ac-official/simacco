<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/UserBranchReportClass.php");
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
require_once($BASEPATH . "includes/functions.php");
$MstrRptObj = new UserBranchReportClass();
$ZoneObj = new ZoneClass();
$filterData = $REQUEST['filter'];
$type_filtr = $REQUEST['type_filtr'];
$item_filtr = $REQUEST['item_filtr'];
$subhead_filter = $REQUEST['subhead_filter'];
$mainhead_filter = $REQUEST['mainhead_filter'];
$amount_filter = $REQUEST['amount_filter'];
$filter     = 'IT.OF_Id='.$preTally_user_ofid.' AND BS.BS_Status = 1 ';
if($REQUEST['znid']!='null' && $REQUEST['znid']!='All' && $REQUEST['znid']!='null' && $REQUEST['znid']!='')
{
$znlcid=$ZoneObj->getZoneLocations($REQUEST['znid']);
$filter.= " AND BS.BS_IEByLC IN (".$znlcid.") ";
}else if($ACL_Obj->ACL_BSheet==2){
$filter.= " AND BS.BS_IEByLC = ".$preTally_user_lcid." ";    
}
if($filterData && $REQUEST['mode']!='2')     $filter .=' AND BS.IT_Id IN ('.$filterData.')';
if($type_filtr !='null' && $type_filtr !=''){
    if($type_filtr=="Internal Transfer Received")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Transfers =1';
    if($type_filtr=="Internal Transfer Paid")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Transfers =1';
    if($type_filtr=="Business Received")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =1';
    if($type_filtr=="Business Returned")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =1';
    if($type_filtr=="Income")
    $filter .=' AND MH.MH_Type=1 AND IT.IT_Business =0 AND IT.IT_Transfers =0';
    if($type_filtr=="Expense")
    $filter .=' AND MH.MH_Type=2 AND IT.IT_Business =0 AND IT.IT_Transfers =0';    
}else    
if($item_filtr !='null' && $item_filtr !='')     $filter .=' AND IT.IT_Id ='.$item_filtr.' ';
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
     $day            =  cal_days_in_month(CAL_GREGORIAN, $REQUEST['m'], $toYear);
    $fromYear   = date('Y')-1;
    $toYear     = date('Y');
    $REQUEST['f']   = '01-'.$REQUEST['m'].'-'.$fromYear;
    $REQUEST['t']   = $day.'-'.$REQUEST['m'].'-'.$toYear;   
} 

$MstrRptObj->reportItemBranchData($filter,$REQUEST['f'],$REQUEST['t']);
$monthArray             = array("","January", "February", "March","April", "May", "June","July", "August", "September","October", "November", "December");
$MRP_Obj                = $MstrRptObj->MasterReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows>';
    echo '<head>
        <column width="50"  type="ro" align="center" ><![CDATA[<div style="text-align:left;"> SlNo <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="0" class="btn_Sort_BIR"/></div>]]></column>
        <column width="150" type="ro" align="left" ><![CDATA[<div style="float:left"><select type="text" style="width:72%;float:left;" id="LC_MR_Name"></select><img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="1" class="btn_Sort_BIR" style="float:left;"/></div>]]></column>';
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

                echo '<column width="*" type="ron" format = "0,000" align="right" ><![CDATA[<div style="float:left;" class="MRBheader">'.$monthArray[$monthLimit] .'<br>'.$y .'<img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR"/> </div>]]></column>';                                
                $footer         .= ",<div id='totMR_".$monthLimit.'_'.$y."' month='".$monthLimit."' mode='".$REQUEST['mode']."' year='".$y."' class='MRBfooter' style='cursor: pointer;'>{#stat_total}</div>";
                $footerStyle    .= ",text-align:right;";
                $cellFormat     .= ",68";   // minimum cell width
                $colNum++;
                $monthLimit--;
            }
        }        
        echo '<column width="100" format = "0,000" type="ron" align="right" ><![CDATA[<div style="float:left;"> Total <img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="'.$colNum.'" class="btn_Sort_BIR" /> </div>]]></column>
            <column width="2" type= "ro" align="center"></column>            
            <afterInit>
                <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call> 
                <call command="attachFooter"><param><![CDATA[Total,#cspan'.$footer.',{#stat_total},#cspan]]></param>
                <param>text-align:left'.$footerStyle.',text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';   
         echo '<userdata name = "IT_IdMain" >'.$filterData.'</userdata>';
        $j = 1;
        $arr_amts=array(); 
        foreach($MRP_Obj as $rw) {
            if(!$rw->LC_Id){$rw->LC_Id=0; $rw->LC_Name="Not Assigned"; }
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