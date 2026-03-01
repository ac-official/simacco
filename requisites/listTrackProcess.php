<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;


$dateFiltter = "WHERE  LC.OF_Id = $preTally_user_ofid ";
if($REQUEST['f'] || $REQUEST['t'] ){
    if($REQUEST['f']) {
        $frmDate= date("Y-m-d", strtotime($REQUEST['f']));  
    } else{
        $frmDate = date("Y-m-d", strtotime('2013-01-01'));
    }
    
    if($REQUEST['t']) {
        $tillDate= date("Y-m-d", strtotime($REQUEST['t']));
    }  else {
        $tillDate = date("Y-m-d");
    }
    $dateFiltter .= " AND AJD.AJ_ReceivedDate BETWEEN '".$frmDate."' AND '".$tillDate."'";
}

$filterData = explode("--",$REQUEST['filter']);

if($filterData[0]) {
    $dateFiltter .=' AND APS.APS_Title LIKE "'.$filterData[0].'%" ';
}
if($filterData[1]){
    $dateFiltter .=' AND APM.APM_Title like "'.$filterData[1].'%"' ;
}
if($filterData[2]){
    $dateFiltter .=' AND AD.ADOC_Document like "'.$filterData[2].'%"';
}
if($filterData[3]){
    $dateFiltter .=' AND AJD.AJ_FName  like "'.$filterData[3].'%" ' ;
}
if($filterData[4]){
    $dateFiltter .=' AND TR.TR_Track like "'.$filterData[4].'%" ' ;
}
if($filterData[5]){
    $dateFiltter .=' AND CONCAT(UA.US_FName," ",UA.US_LName) like "'.$filterData[5].'%" ' ;
}
if($filterData[6]){
    $dateFiltter .=' AND LC.LC_Name like "'.$filterData[6].'%" ' ;
}
if($filterData[7]!=0) {
    if($filterData[7]==1) {
        $dateFiltter .=' AND AJS.AJS_Status = 1';
    } else if($filterData[7]==2){
        $dateFiltter .=' AND AJS.AJS_Status = 2';
    } else if($filterData[7]==3){
        $dateFiltter .=' AND AJS.AJS_Status = 3';
    } else if($filterData[7]==4){
        $dateFiltter .=' AND AJS.AJS_Status = 4';
    }
}

$AttObj = new AttestationClass();
$AttObj->listTrackProcess($dateFiltter,$_GET["posStart"],$_GET["count"]);
$Count = $AttObj->listTrackProcessCount($dateFiltter);
$DocObj = $AttObj->DataArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($DocObj) {
         $j=$_GET["posStart"]+1;
        foreach($DocObj as $rw) {
            echo '<row id="'.$rw->AJS_Id.'">
                    <userdata name="UData_AJ_Id">'.$rw->AJS_Id.'</userdata>
                    <cell ></cell>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="AJ_FName">'.$rw->APS_Title.'</cell>
                    <cell title=" " name="TR_Track">'.$rw->APM_Title.'</cell>
                    <cell title=" " name="AB_TotalAmount">'.$rw->ADOC_Document.'</cell>
                    <cell title=" " name="NO_DOC">'.$rw->AJ_FName.'</cell>
                    <cell title=" " name="NO_SubPr">'.$rw->TR_Track.'</cell>
                    <cell title=" " name="US_EMPID">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell title=" " name="LC_Name ">'.$rw->LC_Name.'</cell>';
                    if($rw->AJS_Status == '1') { 
                        echo '<cell title=" " name="AJ_Status ">Pending</cell>';
                    } else if($rw->AJS_Status == '2'){
                        echo '<cell title=" " name="AJ_Status ">Proceeding</cell>';
                    } else if($rw->AJS_Status == '3'){
                        echo '<cell title=" " name="AJ_Status ">Completed</cell>';
                    } else if($rw->AJS_Status == '4'){
                        echo '<cell title=" " name="AJ_Status ">Rejected</cell>';
                    }
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"><cell type="ro" colspan="9"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>