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
$BId = $_REQUEST['BId'];
$Count=0;
$flag = 0;
$Filtter = ' WHERE  AJDRD.Receive_LC_Id = '.$preTally_user_lcid.' AND AJDRD.AJDRD_Status =1';
$SPStatus = array('', 'Pending','Submitted','Completed','Rejected');
$MPStatus = array('', 'New','Underprocess','Transit','All Process Completed','Delivered');
$flag2=0;
$filterArray = array();
$filterArray = trim(mysqli_real_escape_string($GLOBALS['con'],$REQUEST['filter'])); ;


//if($REQUEST['filter']!='noVal') {
    $filterData = explode("--",$filterArray); 
    if($filterData[0]) {
        $Filtter .=' AND AD.ADOC_Document LIKE "'.$filterData[0].'%" ';
    }
    if($filterData[1]){
        $Filtter .=' AND AJDT.AJ_FName like "'.$filterData[1].'%"';
    }
    if($filterData[2]){
         $Filtter .=' AND TR.TR_Track  like "'.$filterData[2].'%" ' ;
    }
   
    if($filterData[3]){
        $Filtter .=' AND LC.LC_Name like "'.$filterData[3].'%" ' ;
    }
    if($filterData[4]!=0) {
        $Filtter .=' AND AJD.AJD_Status = '.$filterData[4];
    }
if(empty($BId)){
    $Filtter .=' AND AJNB.AJNB_Id = '.$filterData[5];
} else {
   $Filtter .=' AND AJNB.AJNB_Id = '.$BId;
}

$AttObj->listTrackDocumnet($Filtter,'',$_GET["posStart"],$_GET["count"],1);
$DocObj = $AttObj->DataArray;
$subObj = $AttObj->SubArray;
$dupArray = array();
$Count += $AttObj->listTrackDocumnetCount($Filtter);

$AttObj->Data = array(
    'AJNB_Status' => 1
);
$AttObj->updateRecords('attestation_job_notifi_batch', ' AJNB_Id = '.$BId);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
      if($_GET["posStart"]==0  && !isset($REQUEST['filter']) ) {
         echo '<head>
            <column width="40"  type="ro" align="center" sort="na"  >SlNo</column>
            <column width="80"  type="ro" align="left"   sort="na"  >Document</column>
            <column width="*"   type="ro" align="left"   sort="na"  ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Process</div>]]></column>
            <column width="80"  type="ro" align="left"   sort="na"  >Customer Name</column>
            <column width="70"  type="ro" align="left"   sort="na"  >Track ID</column>
            <column width="110" type="ro" align="left"   sort="na"  >Sender Branch</column>
            <column width="60"  type="ro" align="center" sort="na"  >Status</column>
            <column width="40"  type="ch" align="center" sort="na"  >#master_checkbox</column>
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
                <call command="enableSmartRendering">
                    <param>true</param>
                    <param>50</param>
                </call> 
            </beforeInit>
            </head>';
    }
        
    if($DocObj) {
        $j=$_GET["posStart"]+1;
            if($DocObj && !$_GET["posStart"]){
                foreach($DocObj as $rw) {
                    $deliSts = $AttObj->getValue('attestation_job_doc_reference_details', 'AJDRD_Id', ' WHERE AJD_Id = '.$rw->AJD_Id.' AND AJG_Id = '.$rw->AJG_Id.' AND Send_LC_Id = '.$rw->Send_LC_Id.' AND Receive_LC_Id = '.$rw->Receive_LC_Id.' AND AJNB_Id = '.$rw->AJNB_Id.' AND AJDRD_Status = 2');
                    if($rw->AJD_Status !=3 || isset($deliSts)) {
                        $type = 'ro';
                        $style = ' style = "background-color: #E3E3E3;  font-weight:normal;"';
                    }
                    else { // Document status "transit"
                        $type = 'ch';
                        $style = ' style = "background-color: none; color:#000;  font-weight:bold;"';
                    }
                    echo '<row '.$style.' id="'.$rw->AJDRD_Id.'">
                        <userdata name="UData_AJDRD_Id">'.$rw->AJDRD_Id.'</userdata>';
                    echo '
                        <cell title=" ">'.$j.'</cell>
                        <cell title=" " name="ADOC_Document">'.$rw->ADOC_Document.'</cell>';
                        $dupArray = $subObj[$rw->AJD_Id];
                        $process=' ';
                        $count=1;
                        foreach ($dupArray as $subRw){
                            $process = $process .$count++.') '.$subRw->APS_Title.'-'.$subRw->APM_Title.'-'.$SPStatus[$subRw->AJS_Status]."<br>";
                        }
                        if($process==' '){
                            $process = "---";
                        }
                        echo'<cell><![CDATA['.$process.']]></cell>'; 
                        echo'<cell title=" " name="AJ_FName">'.$rw->AJ_FName.'</cell>
                        <cell name="TR_Track">'.$rw->TR_Track.'</cell>
                        <cell name="US_EMPID">'.$rw->LC_Name.'</cell>
                        <cell name="AJD_Status ">'.$MPStatus[$rw->AJD_Status].'</cell>';
                        echo'<cell  type="'.$type.'"></cell>';

                    echo '</row>';
                    $j++;
                }
            }
        //} while ($flag);
    } else {
        echo '<row id="0"><cell type="ro" colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>