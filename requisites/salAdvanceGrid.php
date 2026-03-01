<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$salAdvObj = new AttendanceClass();

if (!isset($_GET["posStart"]))
        $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
        $_GET["count"] = 50;
$filter ='';
$filterData = explode(",",$REQUEST['Filters']);
if($filterData){
    if($filterData[0]){
        $filter .=" AND CONCAT(US.US_FName,' ', US.US_LName) LIKE '".$filterData[0]."%'";
    }
    if($filterData[1]) {
        $filter .=' AND SA.SA_Amount = "'.$filterData[1].'" ' ;
    }
    if($filterData[2]){
        $filter .=' AND  SA.SA_PaymentTime = "'.$filterData[2].'" ' ;
    } 

}
$monthArray = array('01' =>'January','02' =>'February','03'=>'March','04' =>'April','05' =>'May','06' =>'June','07' =>'July','08' =>'August','09' =>'September','10' => 'October','11' => 'November','12' => 'December');
$salAdvObj->salAdvnceDetails($preTally_user_ofid,$_GET["posStart"],$_GET["count"],$filter );
$SA_Obj = $salAdvObj->salAdvnceDetailsArray;
$Count=$salAdvObj->salAdvnceDetailsCount($preTally_user_ofid,$filter);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">';
echo '    <userdata name="TL_Count">'.$Count.'</userdata>';

$j=1+$_GET["posStart"];
		  if($SA_Obj) {
				$l = 1;
				foreach($SA_Obj as $rw) {
					$SA_PaymentStartFrom=explode("-",$rw->SA_PaymentStartFrom);
					echo '<row id="'.$rw->SA_Id.'">						                                        
						<cell>'.$j.'</cell>
                                                <cell name="US_Name">'.$rw->Name .'</cell>
                                                <cell name="SA_Amount">'.$rw->SA_Amount.'</cell>    
                                                <cell name="SA_PaymentTime ">'.$rw->SA_PaymentTime .'</cell>
                                                <cell name=" SA_PaymentStartFrom">'.$monthArray[$SA_PaymentStartFrom[0]]."-".$SA_PaymentStartFrom[1].'</cell> 
                                                <cell name=" SA_DeductAmt">'.$rw-> SA_DeductAmt.'</cell>  
                                                <cell name=" SA_DeductAmt">'.$rw-> CName.'</cell>  </row>';
                                        $j++;
				}
                    }else {
                        echo '<row id="0"> 
                        <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                    }
                       		  
echo '</rows>';
?>