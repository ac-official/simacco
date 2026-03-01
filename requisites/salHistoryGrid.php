<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;

$filterData = explode(",",$REQUEST['filter']);
$header = 0 ;
$filter = "";
if($filterData[0]) {
    $filter.= " AND CONCAT(UA.US_FName,' ',UA.US_LName) LIKE '".$filterData[0]."%'";
    $header = 1;
}
if($filterData[1] != ''){ 
   if($filterData[1] == 'All'){
       $filter.= "";
       $header = 1;
   } else{
       $filter.= " AND LC.LC_Name like '".$filterData[1]."%' ";
       $header = 1;
   }
}
if($filterData[2] != ''){
    $filter.= "AND AL.ALC_Name LIKE '".$filterData[2]."%'";
    $header = 1;
}
if($filterData[3] != ''){
    $filter.= "AND UA.US_EMPID LIKE '".$filterData[3]."%'";
    $header = 1;
}
if($filterData[4]){
    $filter.= "AND DG.DG_Name LIKE '".$filterData[4]."%'";
    $header = 1;
}
if($filterData[5] != ''){
    $filter.= "AND DP.DP_Name LIKE '".$filterData[5]."%'";
    $header = 1;
}
if($filterData[6] != '' && $filterData[6] != 'All'){
    if($filterData[6]== 0){
    $filter.= '';
    $header = 1;  
    }else{
    $filter.= "AND UA.US_DOJ LIKE '%-".$filterData[6]."-%'";
    $header = 1;
    }
}


if($REQUEST['type']=='asc')
    $type=" ASC";
else $type=" DESC";
$orderBy=" ORDER BY CONCAT(UA.US_FName,' ',UA.US_LName)".$type;
if($REQUEST['orderBy']==1)
    $orderBy= " ORDER BY CONCAT(UA.US_FName,' ',UA.US_LName)".$type;
if($REQUEST['orderBy']==2)
    $orderBy= " ORDER BY LC.LC_Name".$type;
if($REQUEST['orderBy']==3)
    $orderBy= " ORDER BY US.US_GrossSal".$type;


$SalHistObj = new AttendanceClass();
$Count=$SalHistObj->listSalaryHistory($preTally_user_ofid,$filter,$orderBy,$_GET["posStart"],$_GET["count"],$filSal);
$SalHistArr = $SalHistObj->salHistDetailsArray;

$colCount = $SalHistObj->getCount();

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">';
echo '<userdata name="SH_count">'.$Count.'</userdata>';
if ($_GET["posStart"] == 0 && $header == 0 ) {   
echo '<head>
        <column width="40" type="ro" sort="na" align="center"></column>
        <column width="50" type="ro" sort="na" align="center">SlNo</column>
        <column width="200" type="ro" sort="na" align="left"><![CDATA[<div>Name of the Employee <img src=\'images/icon/sort-ascending-icon.png\' title=\'Click here to Sort\' colNum=\'1\' class=\'SH_Sort\'/><br><input type=\'text\' id=\'nmFilter\' class=\'salHistFiltr\' placeholder=\'Search\' style=\'width:70%; margin-top:5px;\' value ="'.$filterData[0].'"></div>]]></column>
        <column width="150" type="ro" sort="na" align="left"><![CDATA[<div>Branch  <img src=\'images/icon/dbl_right_20.png\'  class=\'btn_BR\' id=\'br\'/><div id=\'brFilter\'  style=\'width:90%;\'></div></div>]]></column>
        <column width="120" type="ro" sort="na" align="left"><![CDATA[<div>Location <input type=\'text\' id=\'lcFilter\' class=\'salHistFiltr\' placeholder=\'Search\' style=\'width:90%;margin-top:10px; \' value ="'.$filterData[2].'" ></div>]]></column>
        <column width="120" type="ro" sort="na" align="left"><![CDATA[<div>Employee ID <input type=\'text\' id=\'empIdFilter\' class=\'salHistFiltr\' placeholder=\'Search\' style=\'width:90%;margin-top:10px; \' value ="'.$filterData[3].'"></div>]]></column>
        <column width="130" type="ro" sort="na" align="left"><![CDATA[<div>Designation <input type=\'text\' id=\'desIdFilter\' class=\'salHistFiltr\' placeholder=\'Search\' style=\'width:90%;margin-top:10px; \' value ="'.$filterData[4].'" ></div>]]></column>
        <column width="165" type="ro" sort="na" align="left"><![CDATA[<div>Working Area <input type=\'text\' id=\'wkFilter\' class=\'salHistFiltr\' placeholder=\'Search\' style=\'width:90%;margin-top:10px;\' value ="'.$filterData[5].'"></div>]]></column>
        <column width="135" type="ro" sort="na" align="left"><![CDATA[<div>Date of Joining <div id=\'dojFilter\' class=\'salHistFiltr\' style=\'width:90%;margin-top:10px;\'></div></div>]]></column>     
        <column width="140" type="ro" sort="na" align="left">Experience with us</column>
        <column width="110" type="ro" sort="na" align="right">Salary Now</column>';
        for($i = 2; $i<=$colCount;$i++){
            echo '<column width="90" type="ro" sort="na" align="right">'.$i.'</column>';
        }
        
        echo '<settings>
                        <colwidth>px</colwidth>
                </settings>
            <beforeInit> 
                <call command="setSkin">
                        <param>dhx_skyblue</param>
                </call> 
                <call command="setImagePath">
                        <param>assets/grid/codebase/imgs/</param>
                </call> 
                <call command="enableSmartRendering">
                        <param>false</param>
                </call> 
                                
            </beforeInit> 
                
    </head>';
        
 }       
    if($SalHistArr) {
        $j = $_GET["posStart"]+1;        
        foreach($SalHistArr as $rw) {   
            $salDate="--";
            $latestInc="--";
            $joinDate="--";
            $joinSal="--";
            $years=0;$months=0;
             if($rw->IncrmntsDates[0]!="" && $rw->Incrmnts[0]!="")
             {
             $latestInc=date_format(date_create($rw->IncrmntsDates[0]), 'd/m/Y');
             $lastIndx=sizeof($rw->IncrmntsDates);   
             $joinDate=date_format(date_create($rw->IncrmntsDates[$lastIndx-1]), 'd/m/Y');
             $joinSal=$rw->Incrmnts[0];  
            if(date("Y-m-d")>=$rw->IncrmntsDates[$lastIndx-1])
            $diff = abs(strtotime(date("Y-m-d")) - strtotime($rw->US_DOJ));
            else
            $diff=0;    
            if($lastIndx==1)$latestInc="--";            
             }else{
            $diff = abs(strtotime(date("Y-m-d")) - strtotime($rw->US_DOJ));
             }          
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));    
            echo '<row id="'.$rw->Sal_HistId.'">
                <cell type="sub_row_grid">requisites/salaryHistoryList.php&amp;US_Id='.$rw->US_Id.'</cell>
                <cell>'.$j.'</cell>
                <cell>'.$rw->Name.'</cell>
                <cell>'.$rw->LC_Name.'</cell>                
                <cell>'.$rw->ALC_Name.'</cell>
                <cell>'.$rw->US_EMPID.'</cell>
                <cell>'.$rw->DG_Name.'</cell>
                <cell>'.$rw->DP_Name.'</cell>
                <cell>'.$rw->US_DOJ.'</cell>
                <cell>'.$years.' Year '.$months.' Month'.'</cell>
                <cell>'.$rw->Incrmnts[0].'</cell>   ';    
               for($i = 1; $i<=$colCount;$i++){
                   echo ' <cell>'.$rw->Incrmnts[$i].'</cell>   '; 
               }  
               
          echo ' </row>';	                
            $j++;
            
            
            
        }
    }	  
    else {
        echo '<row id="0"> 
            <cell colspan="16"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Records Found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>
