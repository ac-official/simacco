<?php
    require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
    $AttObj     = new AttendanceClass();    
    $att_month=$_REQUEST['att_month'];
    $year       = $_REQUEST['att_year'];;
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
    $esrids=$_REQUEST['SalChkdIds'];
    $filter="";
    if($esrids!=null)$filter=" AND ESR.ESR_Id IN(".$esrids.") AND ESR_BSStatus=0";
    $AttObj->getPaymodeWiseEmpSalRpt($att_month,$year,$sel_modeOfPay,$preTally_user_ofid,$preTally_user_lcid,$ACL_Obj->ACL_SalPMwiseAll,$Sal_BnkId,$filter,$order,$_GET["posStart"],$_GET["count"]);
    $ESR_Obj=$AttObj->getPaymodeWiseEmpSalRptArray;
    if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
    }
    echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
    echo '<rows total_count="'.$PMWrepot_count.'" pos="'.$_GET["posStart"].'">
            <userdata name="Assgnd_CHQ"></userdata>';
                    $j=1;
                        if($ESR_Obj) {
				foreach($ESR_Obj as $rw){
                                   
                                    $Month=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
					echo '<row id="'.$rw->ESR_Id.'">
                                                <userdata name="BA_Id"></userdata>
                                                <userdata name="CHQ_Id"></userdata>
                                                <cell name="id">'.$j.'</cell>                                                
                                                <cell name="Name">'.$rw->US_FName." ".$rw->US_LName.'</cell>
                                                <cell name="Branch">'.$rw->LC_Name.'</cell>
                                                <cell name="THS"> '.$rw->ESR_TakeHomeSalary.'</cell>
                                                <cell name="AccNo">'.$rw->US_AccNo.'</cell>
                                                <cell name="BankName">'.$rw->US_Bankname.'</cell>                                                
                                                <cell name="cmpny_acc"></cell>  
                                                <cell name="chqno"></cell>     
                                                </row>';
                                                $j++;
				}
			}
                        else {
                            echo '<row id="0"> 
                            <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                       }
		  
    echo '</rows>';
     
?>