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
$AttObj     = new AttendanceClass();
$year       = $REQUEST['att_year'];
$month  = $REQUEST['att_month'];
$filters=$REQUEST['Filters'];
$filter="";$order="";
$filterValues=explode(",",$filters);
if($filterValues[0]!="")
    $filter.= " AND CONCAT(US_FName,' ',US_LName) LIKE '".$filterValues[0]."%'";
if($filterValues[1]!="")
    $filter.= " AND LC_Name LIKE '".$filterValues[1]."%'";
if($filterValues[2]!="all" && $filterValues[2]!="")
    $filter.= " AND ESR_Status= ".$filterValues[2];
if($filterValues[4]=='NM')
    $colmn="CONCAT(US_FName,' ',US_LName)";
else if($filterValues[4]=='LC')
    $colmn="LC_Name";
else if($filterValues[4]=='THS')
    $colmn="ESR_TakeHomeSalary";
else
    $colmn="US_GrossSal";
if($filterValues[3]==1){
    $order= " ORDER BY ".$colmn." ASC ,ESR_Id ASC";
}elseif($filterValues[3]==0){
    
    $order= " ORDER BY ".$colmn." DESC ,ESR_Id ASC";
}
$Salrepot_count=$AttObj->CountEmpSalReport($month,$preTally_user_ofid,$year,$filter);
$AttObj->listEmpSalReport($month,$preTally_user_ofid,$year,$filter,$order,$_GET["posStart"],$_GET["count"]);
$ESR_Obj=$AttObj->listEmpSalReportArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
     echo '<rows  total_count="'.$Salrepot_count.'" pos="'.$_GET["posStart"].'">';
                    $j=$_GET["posStart"]+1;
                        if($ESR_Obj) {
				foreach($ESR_Obj as $rw){
                                    if($rw->ESR_Status==0) {
                                                    $ESR_Status="Not Sent";
                                                    $CellType="ch";
                                                } else { 
                                                    $ESR_Status="Already Sent";
                                                    $CellType="ro";
                                                }
                                    if($rw->ESR_TakeHomeSalary<0){$TakeHomeSal=0;}else{$TakeHomeSal=$rw->ESR_TakeHomeSalary;}
                                    $Month=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
					echo '<row id="'.$rw->ESR_Id.'">
                                                <cell name="id">'.$j.'</cell>
                                                <cell name="Name">'.$rw->US_FName." ".$rw->US_LName.'</cell>
                                                <cell name="Branch">'.$rw->LC_Name.'</cell>
                                                    
                                                <cell name="Month">'.$Month[$rw->ESR_Month].'</cell>
                                                <cell name="Year">'.$rw->ESR_Year.'</cell>                                                
                                                <cell name="Grosssal">'.$rw->US_GrossSal.'</cell>    
                                                <cell name="TakeHomeSalary">'.$TakeHomeSal.'</cell>
                                                <cell name="BasicSal"> '.$rw->US_BasicSal.'</cell>
                                                <cell name="HRAsal">'.$rw->US_HRASal.'</cell>
                                                <cell name="CcaSal">'.$rw->US_CcaSal.'</cell>
                                                
                                                    
                                                <cell name="ConveySal">'.$rw->US_ConveySal.'</cell>
                                                <cell name="EduSal">'.$rw->US_EduSal .'</cell>
                                                <cell name="MedSal">'.$rw->US_MedSal .'</cell>
                                                    
                                               <cell name="Otheralowance">'.$rw-> ESR_Otherallowance .'</cell>                                               
                                               <cell name="PFESISal">'.$rw->ESR_PFESI_Sal.'</cell>   
                                               <cell name="DedEPF">'.$rw->US_DedEPF.'</cell>
                                               <cell name="DedESI">'.$rw->US_DedESI .'</cell>
                                                   
                                               <cell name="DedLWF">'.$rw-> US_DedLWF .'</cell>
                                               <cell name="Lop">'.$rw->ESR_Lop.'</cell>
                                                   

                                               <cell name="Proftax">'.$rw-> ESR_Proftax .'</cell>
                                               <cell name="ProfTds">'.$rw->ESR_ProfTds.'</cell>
                                               <cell name="SalTds">'.$rw->ESR_SalTds.'</cell>
                                               <cell name="MealCrd">'.$rw->ESR_MealCard.'</cell>
                                               <cell name="Salaryadvance">'.$rw->ESR_Salaryadvance .'</cell>
                                                   
                                               <cell name="Loan">'.$rw->ESR_Loan .'</cell>
                                               <cell name="Addition">'.$rw->ESR_AdjstmntAddition.'</cell>
                                               <cell name="Deduction">'.$rw->ESR_AdjstmntDeduction .'</cell>
                                               
                                                    

                                                <cell name="Check" type="'.$CellType.'"></cell>';                                                
                                                echo'<cell>'.$ESR_Status.'</cell>';
                                                if($rw->ESR_Status==0){
                                                    echo'<cell type="ro"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Not Sent\');" onmouseout="preTally.Settings.hideLabel(this);" />]]></cell>';
                                                } else{
                                                    echo'<cell type="ro"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Already Sent\');" onmouseout="preTally.Settings.hideLabel(this);" />]]></cell>';
                                                }
                                                echo'</row>';
                                                $j++;
				}
			}
                        else {
                            echo '<row id="0"> 
                            <cell colspan="29"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                       }
		  
            echo '</rows>';

?>