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
$filters=$REQUEST['Filters'];
$filter="";$order="";
$filterValues=explode(",",$filters);
if($filterValues[0]!="")
    $filter.= " AND CONCAT(EP.US_FName,' ',EP.US_LName) LIKE '".$filterValues[0]."%'";
if($filterValues[1]!="")
    $filter.= " AND EP.LC_Name LIKE '".$filterValues[1]."%'";
if($filterValues[3]=='NM')
    $colmn="CONCAT(EP.US_FName,' ',EP.US_LName)";
else if($filterValues[3]=='LC')
    $colmn="EP.LC_Name";
else if($filterValues[3]=='THS')
    $colmn="EP.EP_TakehomeSal";
else
    $colmn="EP.US_GrossSal";
if($filterValues[2]==1){
    $order= " ORDER BY ".$colmn." ASC";
}elseif($filterValues[2]==0){    
    $order= " ORDER BY ".$colmn." DESC";
}
$AttObj     = new AttendanceClass();
$month  = $REQUEST['att_month'];
$year   = $REQUEST['att_year'];
$Payroll_Count=$AttObj->countEmpPayrollReport($month,$preTally_user_ofid,$year,$filter);
$AttObj->listEmpPayrollReport($month,$preTally_user_ofid,$year,$filter,$order,$_GET["posStart"],$_GET["count"]);
$Payroll_Count =$Payroll_Count !='' ? $Payroll_Count : 0; 
$GE_Obj=$AttObj->listEmpPayrollReportArray;
$colNames = array('US_BasicSal','US_HRASal','US_CcaSal','US_ConveySal','US_EduSal','US_MedSal','EP_Otherallowance','US_GrossSal','gross_lop','US_DedEPF','US_DedESI','US_DedLWF','EP_Lop','EP_Proftax','EP_ProfTds','EP_SalTds','EP_Salaryadvance','EP_Loan','EP_AdjstmntAddition','EP_AdjstmntDeduction','EP_TakehomeSal');
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
     echo '<rows total_count="'.$Payroll_Count.'" pos="'.$_GET["posStart"].'">';
                    $j=$_GET["posStart"]+1;
                        if($GE_Obj) {
				foreach($GE_Obj as $rw){
                                    unset($color);
                                    $ephColName = explode(',', $rw->EPH_ColLabel);
                                     foreach ($colNames as $cols){ 
                                         $color[$cols] = 'style ="cursor:pointer !important;"';
                                     }
                                    foreach ($ephColName as $value) { 
                                        //echo '---------'.$value.'--------';
                                        if(in_array($value, $colNames))
                                            $color[$value] = 'style = "background-color:pink;cursor:pointer !important;"';                                        
                                        else
                                            $color[$value] = 'style ="cursor:pointer !important;"';                                        
                                    }
                                    
                                    $Month=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
                                    $grosslop=$rw->US_GrossSal-$rw->EP_Lop;
					echo '<row id="'.$rw->EP_Id.'">
                                                <userdata name="US_Id">'.$rw->US_Id.'</userdata>
                                                <userdata name="US_AttndFlag">'.$rw->US_AttndFlag.'</userdata>    
                                                <userdata name="EP_SalDeductableLeave">'.$rw->EP_SalDeductableLeave.'</userdata>
                                                <userdata name="SS_DedESI">'.$rw->SS_DedESI.'</userdata>
                                                <userdata name="SS_DedESI_Type">'.$rw->SS_DedESI_Type.'</userdata>
                                                <userdata name="SS_DedEPF">'.$rw->SS_DedEPF.'</userdata>
                                                <userdata name="SS_DedEPF_Type">'.$rw->SS_DedEPF_Type.'</userdata>
                                                <userdata name="SS_DedLWF">'.$rw->SS_DedLWF.'</userdata>
                                                <userdata name="SS_DedLWF_Type">'.$rw->SS_DedLWF_Type.'</userdata>
                                                <userdata name="SS_DedProfTDS">'.$rw->SS_DedProfTDS.'</userdata>
                                                <userdata name="SS_DedProfTDS_Type">'.$rw->SS_DedProfTDS_Type.'</userdata>
                                                <userdata name="SS_EmpConEPF">'.$rw->SS_EmpConEPF.'</userdata>
                                                <userdata name="SS_EmpConEPF_Type">'.$rw->SS_EmpConEPF_Type.'</userdata>
                                                <userdata name="SS_EmpConESI">'.$rw->SS_EmpConESI.'</userdata>
                                                <userdata name="SS_EmpConESI_Type">'.$rw->SS_EmpConESI_Type.'</userdata>
                                                <userdata name="SS_EmpConLWF">'.$rw->SS_EmpConLWF.'</userdata>
                                                <userdata name="SS_EmpConLWF_Type">'.$rw->SS_EmpConLWF_Type.'</userdata>
                                                <userdata name="EP_EmpConEPF">'.$rw->EP_EmpConEPF.'</userdata>
                                                <userdata name="EP_EmpConESI">'.$rw->EP_EmpConESI.'</userdata>
                                                <userdata name="EP_EmpConLWF">'.$rw->EP_EmpConLWF.'</userdata>
                                                <userdata name="EP_Month">'.$Month[$rw->EP_Month].'</userdata>
                                                <userdata name="EP_Year">'.$rw->EP_Year.'</userdata>
                                                <cell name="id">'.$j.'</cell>
                                                <cell name="Name">'.$rw->US_FName." ".$rw->US_LName.'</cell>  
                                                <cell name="LC_Name">'.$rw->LC_Name.'</cell>
                                                    
                                               
                                                <cell name="US_GrossSal"  >'.$rw->US_GrossSal.'</cell>  
                                                <cell name="EP_TakehomeSal ">'.$rw->EP_TakehomeSal .'</cell>    
                                                <cell name="US_BasicSal" '.$color['US_BasicSal'].' >'.$rw->US_BasicSal.'</cell>
                                                <cell name="US_HRASal" '.$color['US_HRASal'].' >'.$rw->US_HRASal.'</cell>
                                                <cell name="US_CcaSal" '.$color['US_CcaSal'].' >'.$rw->US_CcaSal.'</cell>
                                                    
                                                <cell name="US_ConveySal" '.$color['US_ConveySal'].' >'.$rw->US_ConveySal.'</cell>
                                                <cell name="US_EduSal" '.$color['US_EduSal'].' >'.$rw->US_EduSal .'</cell>
                                                <cell name="US_MedSal " '.$color['US_MedSal'].' >'.$rw->US_MedSal .'</cell>
                                                    
                                                <cell name="EP_Otherallowance" '.$color['EP_Otherallowance'].' >'.$rw->EP_Otherallowance.'</cell>
                                               
                                                <cell name="gross_lop"  >'.$rw->EP_PFESI_Sal.'</cell>  
                                                <cell name="US_DedEPF" >'.$rw->US_DedEPF .'</cell>
                                                <cell name="US_DedESI" >'.$rw->US_DedESI .'</cell>
                                                    
                                                <cell name="US_DedLWF" '.$color['US_DedLWF'].' >'.$rw->US_DedLWF .'</cell>
                                                <cell name="EP_Lop" '.$color['EP_Lop'].' >'.$rw->EP_Lop.'</cell>
                                                
                                                 
                                                <cell name="EP_Proftax" '.$color['EP_Proftax'].' >'.$rw-> EP_Proftax.'</cell>
                                                <cell name="EP_ProfTds" '.$color['EP_ProfTds'].'> '.$rw->EP_ProfTds.'</cell>                                                
                                                <cell name="EP_SalTds" '.$color['EP_SalTds'].'> '.$rw->EP_SalTds.'</cell>
                                                <cell name="EP_MealCard" '.$color['EP_MealCard'].'> '.$rw->EP_MealCard.'</cell>        
                                                <cell name="EP_Salaryadvance" '.$color['EP_Salaryadvance'].' >'.$rw->EP_Salaryadvance.' </cell>
                                                
                                                <cell name="EP_Loan" '.$color['EP_Loan'].' >'.$rw->EP_Loan.'</cell>
                                                <cell name="EP_AdjstmntAddition" '.$color['EP_AdjstmntAddition'].' >'.$rw->EP_AdjstmntAddition.'</cell>
                                                <cell name="EP_AdjstmntDeduction" '.$color['EP_AdjstmntDeduction'].' >'.$rw->EP_AdjstmntDeduction.'</cell>    
                                                
                                                </row>';
                                                $j++;
				}
			}
                        else {
                            echo '<row id="0"> 
                            <cell colspan="23"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                       }
		  
            echo '</rows>';

?>