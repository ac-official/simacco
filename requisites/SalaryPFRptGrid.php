<?php
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$AttObj = new AttendanceClass();
$year   = $REQUEST['att_year'];
$month  = $REQUEST['att_month'];
$filters= $REQUEST['Filters'];


$filter="";$order="";
$filterValues=explode(",",$filters);

if($filterValues[0]!="")
    $filter.= " AND CONCAT(ESR.US_FName,' ',ESR.US_LName) LIKE '".$filterValues[0]."%'";
if($filterValues[1]!="")
    $filter.= " AND ESR.LC_Name LIKE '".$filterValues[1]."%' AND ESR.US_DedEPF   != '0' ";


$filter.= " AND ESR.US_DedEPF   != '0' ";


$SalPFRptCnt  =  $AttObj->CountEmpSalPFESIReport($month,$preTally_user_ofid,$year,$filter);
$AttObj->listEmpSalPFESIReport($month,$preTally_user_ofid,$year,$filter,$_GET["posStart"],$_GET["count"]);
$ESR_Obj = $AttObj->listEmpSalPFReportArray;
//print_r($ESR_Obj);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows  total_count="'.$SalPFRptCnt.'" pos="'.$_GET["posStart"].'">';
    
    $j=$_GET["posStart"]+1;
        if($ESR_Obj) {
            foreach($ESR_Obj as $rw){

                echo '<row id="'.$rw->ESR_Id.'">
                        <cell name="id">'.$j.'</cell>
                        <cell name="PFNumber">'.$rw->US_PFNo.'</cell>    
                        <cell name="Name">'.$rw->US_FName." ".$rw->US_LName.'</cell>
                        <cell name="Branch">'.$rw->LC_Name.'</cell>
                        <cell name="LOP">'.$rw->EP_SalDeductableLeave.'</cell>                                                
                        <cell name="PFSalary">'.$rw->ESR_PFESI_Sal.'</cell>    
                        </row>';
                        $j++;
            }
        }else {
            echo '<row id="0"> 
            <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            </row>';
       }

    echo '</rows>';

?>