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

$filter = "WHERE  LC.OF_Id = $preTally_user_ofid ";

$stDate = $REQUEST['f'];
$enDate = $REQUEST['t'];
if($stDate!='' && $enDate!=''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJ.AJ_ReceivedDate   between '".$stDate."' AND '".$enDate."'";
}elseif($stDate=='' && $enDate!=''){
    $enDate  = date("Y-m-d", strtotime($enDate));
    $filter .=" AND AJ.AJ_ReceivedDate   <=  '".$enDate."'";
}elseif($stDate!='' && $enDate==''){
    $stDate  = date("Y-m-d", strtotime($stDate));
    $filter .=" AND AJ.AJ_ReceivedDate   >=  '".$stDate."'";
}

$filterData = explode("--",$REQUEST['filter']);

if($filterData[0]) {
    $filter .=' AND AJ.AJ_FName LIKE "'.$filterData[0].'%" ';
}
if($filterData[1]){
    $filter .=' AND TR.TR_Track like "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $Havefilter =' HAVING Amount = '. $filterData[2] ;
}
if($filterData[3]){
    $filter .=' AND CONCAT(UA.US_FName," ",UA.US_LName) like "'.$filterData[3].'%" ' ;
}
if($filterData[4]){
    $filter .=' AND LC.LC_Name  like "'.$filterData[4].'%" ' ;
}
if($filterData[5] != '') {
    $filter .=' AND AJ.AJ_Status  = '.$filterData[5];
}
if($ACL_Obj->ACL_BSheet==4)    
     $filter .=" AND  UA.OF_Id =". $preTally_user_ofid ;
if($ACL_Obj->ACL_BSheet==3)    
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ;
if($ACL_Obj->ACL_BSheet==2)
     $filter .=" AND  UA.LC_Id =". $preTally_user_lcid ;
if($ACL_Obj->ACL_BSheet==1)
     $filter .=" AND  UA.DP_Id =". $preTally_user_dpid ." AND  UA.LC_Id = ".$preTally_user_lcid ;
if($ACL_Obj->ACL_BSheet==0)
     $filter .=" AND  UA.US_Id =". $preTally_user_id;
$AttObj = new AttestationClass();
$AttObj->listTrackJob($filter,$Havefilter,$_GET["posStart"],$_GET["count"]);
$DocObj = $AttObj->DataArray;
$Count  = $AttObj->listJobCount($filter,$Havefilter);

$StatusArray = array('Incomplete Registration','Completed Registration','Deleted','Job UnderProcess','Job Completed','Delivered');
$ColourArray = array('#F67728;','#04580A;','#CC0099;','#A3297A;','#2E8AE6;','#6600CC;');

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$Count.'</userdata>';
    if($_GET["posStart"]==0 && !$REQUEST['filter']) {
         echo '<head>
             <column width="40" type="ro" align="center" sort="na" ></column>
             <column width="40" type="ro" align="center" sort="na" ></column>
             <column width="60" type="ro" align="center" sort="na" >SlNo</column>
             <column width="*" type="ro" align="left" sort="na"    ><![CDATA[<div style="width:90%; text-align:center; margin-top: 25px;">Name</div>]]></column>
             <column width="100" type="ro" align="left" sort="na"  >Track ID </column>
             <column width="90" type="ro" align="right" sort="na"  >Amount</column>
             <column width="90" type="ro" align="center" sort="na" >Document(s) </column>
             <column width="80" type="ro" align="center" sort="na" >Process </column>
             <column width="120" type="ro" align="left" sort="na"  >Created User </column>
             <column width="120" type="ro" align="left" sort="na"  >Created Brach </column>
             <column width="170" type="ro" align="left" sort="na"  >Status </column>
             <column width="70"  type="ro" align="center" sort="na"  >Details</column>
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
    if($DocObj) {
        $j=$_GET["posStart"]+1;
        foreach($DocObj as $rw) {
            if($rw->AJ_Status != '2' || $rw->AJ_Status != '5' ){
                $editTitle="Click here to edit"; 
            }else{
                $editTitle=" ";
            }
            echo '<row id="'.$rw->AJ_Id.'">
                    <userdata name="UData_AJ_Id">'.$rw->AJ_Id.'</userdata>
                    <cell title="'.$editTitle.'">';
                    if( ($rw->AJ_Status == '0' || $rw->AJ_Status == '1' || $rw->AJ_Status == '3' || $rw->AJ_Status == '4') && $ACL_Obj->ACL_BSheet_VM == 1)
                        echo '<![CDATA[<img src="images/icon/edit_icon.gif" title="Edit" style="cursor:pointer;" onclick="preTally.Track.TrackRegistrationTabUser(this,'.$rw->AJ_Id.');" />]]>';
                    echo '</cell>
                    <cell type="sub_row_grid" title=" ">requisites/trackItemSubGrid.php&amp;TrkjId='.$rw->AJ_Id.'</cell>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="AJ_FName">'.$rw->AJ_FName.'</cell>
                    <cell title=" " name="TR_Track">'.$rw->TR_Track.'</cell>
                    <cell title=" " name="Amount">'.$rw->Amount.'</cell>
                    <cell title=" " name="NO_DOC">'.$rw->NO_DOC.'</cell>
                    <cell title=" " name="NO_SubPr">'.$rw->NO_SubPr.'</cell>
                    <cell title=" " name="US_EMPID">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell title=" " name="LC_Name ">'.$rw->LC_Name.'</cell>
                    <cell title=" " name="AJ_Status"><![CDATA[<span style="color: '.$ColourArray[$rw->AJ_Status].'">'.$StatusArray[$rw->AJ_Status].'<span>]]></cell>
                    <cell title="Click here for More Details"><![CDATA[<img src="images/icon/info_18.png" onclick="preTally.Track.jobTracking(this,'.$rw->AJ_Id.');" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> 
            <cell type="ro" colspan="11"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>