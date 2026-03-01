<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$salRepObj = new AttendanceClass();
$Flter='';
if($_REQUEST["SA_Id"]){
    $Flter=" AND SA.SA_Id=".$_REQUEST["SA_Id"];
}else{
    $Flter=" AND SA.SA_Id=0";
}
$monthArray = array('01' =>'January','02' =>'February','03'=>'March','04' =>'April','05' =>'May','06' =>'June','07' =>'July','08' =>'August','09' =>'September','10' => 'October','11' => 'November','12' => 'December');
$salRepObj->repaymentDetails($preTally_user_ofid,$Flter);
$SR_Obj = $salRepObj->salAdvnceDetailsArray;
$repaymentStatus= array(1=>'Pending',2=>"Skipped",3=>"Paid");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows >';
        $j=1;
		  if($SR_Obj) {
				foreach($SR_Obj as $rw) {
                                    $SRS_Month=explode("-",$rw->SRS_Month);
                                    
					echo '<row id="'.$rw->SRS_Id.'">						                                        
						<cell>'.$j.'</cell>
                                                <cell name="US_Name">'.$rw->Name .'</cell>
                                                <cell name="LC_Name">'.$rw->LC_Name.'</cell> 
                                                <cell name="SRS_RepaymentAmt ">'.$rw->SRS_RepaymentAmt .'</cell>
                                                <cell>'.$monthArray[$SRS_Month[0]]."-".$SRS_Month[1].'</cell>
                                                <cell name="SRS_Status ">'.$repaymentStatus[$rw->SRS_Status] .'</cell>
                                                <cell name="SRS_Status ">'.$rw->SRS_Status .'</cell>';
                                                    if($rw->SRS_Status==1){
                                                echo '<cell type="ro" title="Skip"><![CDATA[<img src="images/icon/skip.png" style="margin:2px 0; cursor:pointer;" onclick="this.disabled=true;preTally.UserProfile.skipSalAdvance('.$rw->SRS_Id.',2);" />]]></cell>';
                                                    }
                                                    else{
                                                        echo '<cell type="ro"></cell>';
                                                    }
                                                echo'</row>';
                                        $j++;
				}
                    }else {
                        echo '<row id="0"> 
                        <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                    }
                       		  
echo '</rows>';
?>