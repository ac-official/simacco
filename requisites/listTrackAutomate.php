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

$filter = " AA.OF_Id = $preTally_user_ofid ";
//
//$stDate = $REQUEST['f'];
//$enDate = $REQUEST['t'];
//if($stDate!='' && $enDate!=''){
//    $stDate  = date("Y-m-d", strtotime($stDate));
//    $enDate  = date("Y-m-d", strtotime($enDate));
//    $filter .=" AND AJ.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
//}elseif($stDate=='' && $enDate!=''){
//    $enDate  = date("Y-m-d", strtotime($enDate));
//    $filter .=" AND AJ.AJ_ReceivedDate   <=  '".$enDate."'";
//}elseif($stDate!='' && $enDate==''){
//    $stDate  = date("Y-m-d", strtotime($stDate));
//    $filter .=" AND AJ.AJ_ReceivedDate   >=  '".$stDate."'";
//}
//
//$filterData = explode("--",$REQUEST['filter']);
//
//if($filterData[0]) {
//    $filter .=' AND AJ.AJ_FName LIKE "'.$filterData[0].'%" ';
//}
//if($filterData[1]){
//    $filter .=' AND TR.TR_Track like "'.$filterData[1].'%"' ;
//}
//if($filterData[2] != ''){
//    $Havefilter =' HAVING Amount = '. $filterData[2] ;
//}
//if($filterData[3]){
//    $filter .=' AND CONCAT(UA.US_FName," ",UA.US_LName) like "'.$filterData[3].'%" ' ;
//}
//if($filterData[4]){
//    $filter .=' AND LC.LC_Name  like "'.$filterData[4].'%" ' ;
//}
//if($filterData[5] != '') {
//    $filter .=' AND AJ.AJ_Status  = '.$filterData[5];
//}

$AttObj = new AttestationClass();
$AttObj->getCountryName();
$CNObj = $AttObj->DataArray;

$AttObj->listTrackAutomate($filter,$_GET["posStart"],$_GET["count"]);
$automateObj = $AttObj->DataArray;
$Count  = $AttObj->listTrackAutomateCount($filter);
//print_r($automateObj);
$VisaTypeNameArray = array('','Job Visa','Visiting Visa','Business Visa','Family Visa','Transit Visa','Tourist Visa');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>';
    if($_GET["posStart"]==0 && !$REQUEST['filter']) {
         echo '<head>
             <column width="60" type="ro" align="center" sort="na">SlNo</column>
             <column width="*" type="ro" align="left" sort="na">Country</column>
             <column width="*" type="ro" align="left" sort="na">Visa Type</column>
             <column width="*" type="ro" align="left" sort="na">Certificate</column>
             <column width="*" type="ro" align="left" sort="na">Issuing Country</column>
             <column width="*" type="ro" align="left" sort="na">Authority</column>
             <column width="80" type="ro" align="left" sort="na">From year </column>
             <column width="60" type="ro" align="left" sort="na">To Year </column>
             <column width="60" type="ro" align="center" sort="na">Status</column>
             <column width="50" type="ro" align="center" sort="na">Edit</column>
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
    if($automateObj) {
        $j = $_GET["posStart"]+1;
        foreach($automateObj as $rw) { 
            
            $visa = $certificate = $authority = $country = $issuingCntry = '';
            
            $visaTypeArray    = explode(",", $rw->AA_VisaType);
            $certificateArray = explode(",", $rw->Document);
            $authorityArray   = explode(",", $rw->Authority);
            $countryArray     = explode(",", $rw->CN_Id);
            $issuingCNArray   = explode(",", $rw->AA_Issuing_CNId);
            
            foreach ($visaTypeArray as $key=>$value) {
                $visa .= $VisaTypeNameArray[$value].'</br>';
            }
            foreach ($certificateArray as $key=>$value) {
                $certificate .= $value.'</br>';
            }
            foreach ($authorityArray as $key=>$value) {
                $authority .= $value.'</br>';
            }
            foreach ($countryArray as $key=>$value) {
                $country .= $CNObj[$value].'</br>';
            }
            foreach ($issuingCNArray as $key=>$value) {
                $issuingCntry .= $CNObj[$value].'</br>';
            }
            $statImg    = ($rw->AA_Status == '0') ? "cross.png" : "tick.png";
            
            echo '<row id="'.$rw->AA_Id.'">
                    <userdata name="AA_Id">'.$rw->AA_Id.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " ><![CDATA['.$country.']]></cell>
                    <cell title=" " ><![CDATA['.$visa.']]></cell>
                    <cell title=" " ><![CDATA['.$certificate.']]></cell>
                    <cell title=" " ><![CDATA['.$issuingCntry.']]></cell>
                    <cell title=" " ><![CDATA['.$authority.']]></cell>
                    <cell title=" " >'.$rw->AA_FromYear.'</cell>
                    <cell title=" " >'.$rw->AA_ToYear.'</cell>
                    <cell title="Click here to Publish/Block"><![CDATA[<img src="images/icon/'.$statImg.'" onclick="preTally.Track.changeAutomateStatus('.$rw->AA_Id.','.$rw->AA_Status.');" style="margin:2px 0; cursor:pointer;"/>]]></cell>
                    <cell title="Click here to Edit"><![CDATA[<img src="images/icon/info_18.png" onclick="preTally.Track.trackAutomate('.$rw->AA_Id.');" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell type="ro" colspan="9"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>