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

$filter = " UA.OF_Id = $preTally_user_ofid ";
//print($ACL_Obj->ACL_HR);die();
if($ACL_Obj->ACL_HR==3)    
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ;
if($ACL_Obj->ACL_HR==2)
     $filter .=" AND  UA.LC_Id =". $preTally_user_lcid ;
if($ACL_Obj->ACL_HR==1)
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ." AND  UA.LC_Id = ".$preTally_user_lcid ;
if($ACL_Obj->ACL_HR==0)
     $filter .=" AND  UA.US_Id =". $preTally_user_id;
if($REQUEST['f']) $stDate = $REQUEST['f'];
if($REQUEST['t']) $enDate = $REQUEST['t'];

if($stDate!='' && $enDate!=''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AE.AE_CDate   between '".$stDate."' AND '".$enDate."'";
}elseif($stDate=='' && $enDate!=''){
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AE.AE_CDate   <=  '".$enDate."'";
}elseif($stDate!='' && $enDate==''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $filter .=" AND AE.AE_CDate   >=  '".$stDate."'";
}

$filterData = explode("--",$REQUEST['filter']);

if($filterData[0]) {
    $filter .=' AND AE.AE_Name LIKE "'.$filterData[0].'%" ';
}
if($filterData[1]){
    $filter .=' AND CONCAT(UA1.US_FName," ",UA1.US_LName) like "'.$filterData[1].'%" ' ;
}
if($filterData[2]){
    $filter .=' AND LC1.LC_Name  like "'.$filterData[2].'%" ' ;
}
if($filterData[3] != 0) {
    $filter .=' AND EF.EF_Chance  = '.$filterData[3];
}
if($filterData[4]){
    $filter .=' AND CONCAT(UA.US_FName," ",UA.US_LName) like "'.$filterData[4].'%" ' ;
}
if($filterData[5]){
    $filter .=' AND LC.LC_Name  like "'.$filterData[5].'%" ' ;
}
if($filterData[6] != 0) {
    $filter .=' AND AE.AE_Status  = '.$filterData[6];
}

$count  = $AttObj->listTrackEnquiryCount($filter);
$AttObj->listTrackEnquiry($filter,$_GET["posStart"],$_GET["count"]);
$EnqObj = $AttObj->DataArray;

$statusArray= array("1"=>"New", "2"=>"Processed", "3"=>"Cancelled", "4"=>"Follow up");
$efStatus   = array('','Low','Normal','High','Very High');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$count.'</userdata>';
    if($_GET["posStart"]==0 && !$REQUEST['filter']) {
         echo '<head>
            <column width="40" type="ro" align="center" sort="na" >SlNo</column>
            <column width="150" type="ed" align="left" sort="na" ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Name</div>]]></column>
            <column width="100" type="ed" align="left" sort="na" ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Mobile No</div>]]></column>
            <column width="120" type="ed" align="left" sort="na" ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Email</div>]]></column>
            
            <column width="120" type="ed" align="center" sort="na" ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Number of follow ups</div>]]></column>
            <column width="*" type="txt" align="left" sort="na"  ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Last Contact Details</div>]]></column>
            <column width="120" type="ro" align="left" sort="na"  >#cspan</column>
            <column width="120" type="ro" align="center" sort="na"  >#cspan</column>
            <column width="120" type="ro" align="center" sort="na"  ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Next Follow up Date</div>]]></column>
            <column width="120" type="ro" align="left" sort="na"  ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Chance of getting job</div>]]></column>
                        
            <column width="120" type="ro" align="left" sort="na"  ><![CDATA[<div style="text-align:center; margin-top: 25px;">Created User</div>]]></column>
            <column width="120" type="ro" align="left" sort="na"  ><![CDATA[<div style="text-align:center; margin-top: 25px;">Created Branch</div>]]></column>
            <column width="80" type="combo" align="left" sort="na"><![CDATA[<div style="text-align:center; margin-top: 25px;">Status</div>]]></column>
            
            <column width="50" type="ro" align="center" sort="na" >Info</column>
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
                    <param>false</param>
                    <param>50</param>
                </call> 
            </beforeInit>
            </head>';
    }
    if($EnqObj) {
        $j = $_GET["posStart"]+1;
        foreach($EnqObj as $rw) {
            
            $lastDate = $rw->EF_CDate != '' ? date('d-m-Y', strtotime($rw->EF_CDate)) : "--";
            $nextDate = $rw->EF_NextDate != '' ? date('d-m-Y', strtotime($rw->EF_NextDate)) : "--";
            echo '<row id="'.$rw->AE_Id.'">
                    <userdata name="UData_AE_Id">'.$rw->AE_Id.'</userdata>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="AE_Name">'.$rw->AE_Name.'</cell>
                    <cell title=" " name="AE_Mobile">'.$rw->AE_Mobile.'</cell>
                    <cell title=" " name="AE_Email">'.$rw->AE_Email.'</cell>
                        
                    <cell title=" " name="NO_FollowUp">'.$rw->NO_FollowUp.'</cell>
                    <cell title=" " name="LastUser">'.$rw->LastUser.'</cell>
                    <cell title=" " name="LastBranch">'.$rw->LastBranch.'</cell>
                    <cell title=" " name="EF_CDate">'.$lastDate.'</cell>
                    <cell title=" " name="EF_NextDate">'.$nextDate.'</cell>                    
                    <cell title=" " name="EF_Status">'.$efStatus[$rw->EF_Chance].'</cell>            

                    <cell title=" " name="US_EMPID">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell title=" " name="LC_Name">'.$rw->LC_Name.'</cell>
                    <cell title=" " name="AJ_Status" xmlcontent="1" editable="0">'.$statusArray[$rw->AE_Status];
                    if($rw->AE_Status==1) 
                        echo'<option value="3">Cancelled</option>';
                    echo'</cell>';
                    echo '<cell title="Click here for More Details"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
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