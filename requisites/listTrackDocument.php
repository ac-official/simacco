<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filter = ' WHERE  LC.OF_Id = '.$preTally_user_ofid.' AND AJD.AJD_Status != 0 AND AJDT.AJ_Status NOT IN (0,2)';

if($REQUEST['f']) $stDate = $REQUEST['f'];
if($REQUEST['t']) $enDate = $REQUEST['t'];

if($stDate!='' && $enDate!=''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
}elseif($stDate=='' && $enDate!=''){
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   <=  '".$enDate."'";
}elseif($stDate!='' && $enDate==''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $filter .=" AND AJDT.AJ_ReceivedDate   >=  '".$stDate."'";
}

$filterData = explode("--",$REQUEST['filter']);
if($filterData[0]) {
    $filter .=' AND AD.ADOC_Document LIKE "'.$filterData[0].'%" ';
}
if($filterData[1]){
    $filter .=' AND AJDT.AJ_FName like "'.$filterData[1].'%"';
}
if($filterData[2]){
    $filter .=' AND TR.TR_Track  like "'.$filterData[2].'%" ' ;
}
if($filterData[3]){
    $filter .=' AND CONCAT(UA.US_FName," ",UA.US_LName) like "'.$filterData[3].'%" ' ;
}
if($filterData[4]){
    $filter .=' AND LC.LC_Name like "'.$filterData[4].'%" ' ;
}
if($filterData[5]!=0) {
    $filter .=' AND AJD.AJD_Status = '.$filterData[5];
}
if($filterData[6]!=0) {
//    $processArr = explode("-", $filterData[6]);
    $subProcessDocIds  = $AttObj->getSubProcessDocumentIds($filterData[6], $preTally_user_ofid,$stDate,$enDate);
    $nxtProcessDocIds  = $AttObj->getNextProcessDocumentIds($subProcessDocIds,$filterData[6]);
    $docId = implode(',',array_unique($AttObj->DocIdArray));
//    $docArray = explode(',',$docId);
    $filter .=' AND AJD.AJD_Id IN ('.$docId.') ';
}

$GPId = $REQUEST['GpRwId'];
if($GPId) {
    $docId   = $AttObj->getIdsList('attestation_job_group_doc ',' AJD_Id',' WHERE AJG_Id = '.$GPId);
    $docArray = explode(',',$docId);
    $orderBy = '( AJD.AJD_Id IN ( '.$docId.') ) DESC , ';
}else 
    $docArray  = explode(',',$REQUEST['GPIds']) ;

$Count  = $AttObj->listTrackDocumnetCount($filter);
$AttObj->listTrackDocumnet($filter,$orderBy,$_GET["posStart"],$_GET["count"],0);
$DocObj = $AttObj->DataArray;
$subObj = $AttObj->SubArray;

$SPStatus = array('', 'Pending','Submitted','Completed','Rejected');
$MPStatus = array('', 'New','Underprocess','Transit','All Process Completed','Delivered');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
      if($_GET["posStart"] == 0 && !isset($REQUEST['filter']) ) {
         echo '<head>
             <column width="40"  type="ch" align="center" sort="na" name="checkbox" >#master_checkbox</column>
             <column width="40"  type="ro" align="center" sort="na"  >SlNo</column>
             <column width="80"  type="ro" align="left"   sort="na"  >Document</column>
             <column width="*"   type="ro" align="left"   sort="na"  ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Process</div>]]></column>
             <column width="150"  type="ro" align="center" sort="na"  ><![CDATA[<div id="itmDocNxtProcess"  class="nxtProcess_select" style="width:90%;  margin-top: 25px;"></div>]]></column>
             <column width="100"  type="ro" align="left"   sort="na"  >Customer Name</column>
             <column width="80"  type="ro" align="left"   sort="na"  >Track ID</column>
             <column width="80"  type="ro" align="left"   sort="na"  >Created User</column>
             <column width="120" type="ro" align="left"   sort="na"  >Created Branch</column>
             <column width="100"  type="ro" align="center" sort="na" >Status</column>
             
            <settings>
                <colwidth>px</colwidth>
            </settings>
            <beforeInit> 
                <call command="setSkin">
                    <param>dhx_skyblue</param>
                </call> 
                <call command="setImagePath">
                    <param>assets/grid/codebase/imgs/</param>
                </call> 
                <call command="enableColSpan">
                    <param>true</param>
                </call> 
            </beforeInit>
            </head>';
    }
    if($DocObj){
        $j = $_GET["posStart"] + 1;
        foreach($DocObj as $rw) { 
            $check = 0;
            $nextProcess = '--';
            $CompletedDocFlag = 0;
            if(in_array($rw->AJD_Id, $docArray)) $check = 1;
            echo '<row id="'.$rw->AJD_Id.'">
                    <userdata name="UData_AJD_Id">'.$rw->AJD_Id.'</userdata>';
                    echo '<cell >'.$check.'</cell>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="ADOC_Document">'.$rw->ADOC_Document.'</cell>';
                    $process='';
                    $slNo = 1;
                    foreach ($subObj[$rw->AJD_Id] as $subRw){
                        $process = $process .$slNo++.') '.$subRw->APS_Title.'-'.$subRw->APM_Title.'-'.$SPStatus[$subRw->AJS_Status]."<br>";
                        if($slNo == 2 && $subRw->AJS_Status == 1)
                            $nextProcess = $subRw->APS_Title.' - <br>'.$subRw->APM_Title;
                        if($CompletedDocFlag == 1 && ( $subRw->AJS_Status == 1 || $subRw->AJS_Status == 2 ))    
                            $nextProcess = $subRw->APS_Title.' - <br>'.$subRw->APM_Title;
                        $CompletedDocFlag = $subRw->AJS_Status == 3 || $subRw->AJS_Status == 4 ? 1 : 0;
                    }
                    echo'<cell><![CDATA['.$process.']]></cell>'; 
                    echo'<cell><![CDATA['.$nextProcess.']]></cell>'; 
                    echo'<cell title=" " name="AJ_FName">'.$rw->AJ_FName.'</cell>
                    <cell name="TR_Track">'.$rw->TR_Track.'</cell>
                    <cell name="US_EMPID">'.$rw->US_FName.' '.substr($rw->US_LName,0, 1).'</cell>
                    <cell name="LC_Name ">'.$rw->LC_Name.'</cell>
                    <cell name="AJD_Status ">'.$MPStatus[$rw->AJD_Status].'</cell>';
            echo '</row>';
        $j++;
        }
    } else {
        echo '<row id="0"><cell type="ro" ></cell><cell type="ro" colspan="9"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell></row>';
    }
echo '</rows>';
?>