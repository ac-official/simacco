<?php
    require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
    $AttObj     = new AttendanceClass();
    
     $att_month=$REQUEST['att_month'];
     $year       =$REQUEST['att_year'];
     if(isset($REQUEST['Sal_BnkId'])){
         $Sal_BnkId=$REQUEST['Sal_BnkId'];
     }else{
         $Sal_BnkId=null;
     }
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;
     $sel_modeOfPay=$REQUEST['sel_modeOfPay'];
     
     if($ACL_Obj->ACL_SalPMwiseAll==1)
     $SalPMWiseACL=1; //1:Full Branch (All modes) 2:BranchWise(Cash Only)
     else 
     $SalPMWiseACL=0;
     /*Will show all users with SALARY AS CASH if user having SALARY DETAIL ACL in other case it will show only the users branch*/
    $AttObj->getPaymodeWiseEmpSalRpt($att_month,$year,$sel_modeOfPay,$preTally_user_ofid,$preTally_user_lcid,$SalPMWiseACL,$Sal_BnkId,$filter,$order,$_GET["posStart"],$_GET["count"]);
    $ESR_Obj=$AttObj->getPaymodeWiseEmpSalRptArray;
    if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
    }
    echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows total_count="'.$PMWrepot_count.'" pos="'.$_GET["posStart"].'">';
                    $j=1;
                        if($ESR_Obj) {
				foreach($ESR_Obj as $rw){
                                   $stats_icon="cross.png";
                                   if($rw->ESR_BSStatus==1)$stats_icon="tick.png";
                                    $Month=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
                                    echo '<row id="'.$rw->ESR_Id.'">
                                                <userdata name="BS_Status">'.$rw->ESR_BSStatus.'</userdata>
                                                <cell name="id">'.$j.'</cell>
                                                <cell name="US_Id">'.$rw->US_EMPID.'</cell>    
                                                <cell name="Name">'.$rw->US_FName." ".$rw->US_LName.'</cell>
                                                <cell name="Branch">'.$rw->LC_Name.'</cell>
                                                <cell name="Month">'.$Month[$rw->ESR_Month].'</cell>
                                                <cell name="Year">'.$rw->ESR_Year.'</cell>
                                                <cell name="THS"> '.$rw->ESR_TakeHomeSalary.'</cell>
                                                <cell name="AccNo">'.$rw->US_AccNo.'</cell>
                                                <cell name="BankName">'.$rw->US_Bankname.'</cell>';
                                                if($rw->ESR_BSStatus==1)
                                                 echo '<cell title="Entry Created"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Entry Created\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                else
                                                 echo '<cell title="Entry Not Created"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Not Created\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';   
                                              
                                                echo '<cell></cell></row>';
                                                $j++;
				}
			}
                        else {
                            echo '<row id="0"> 
                            <cell colspan="11"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                       }
		  
    echo '</rows>';
     
?>