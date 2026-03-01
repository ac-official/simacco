<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "includes/functions.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$filterData = explode(",",$REQUEST['filter']);
$filter = 'AND 1 ';
if($filterData[0] != ''){
    $filter .=' AND USAUTH.US_EMPID LIKE"'.$filterData[0].'%"' ;
}
if($filterData[1] != ''){
    $filter .=' AND CONCAT(USAUTH.US_FName," ",USAUTH.US_LName) LIKE"'.$filterData[1].'%"' ;
}
if($filterData[2] != '' && $filterData[2] != 'All'){
    $filter .=' AND USAUTH.UT_Id ='.$filterData[2] ;
}
if($filterData[3] != '' && $filterData[3] != 'All'){
    $filter .=' AND USAUTH.DG_Id ='.$filterData[3] ;
}
if($filterData[4] != 0){
    $filter .=' AND USAUTH.LC_Id ='.$filterData[4] ;
}
if($filterData[5] != '' && $filterData[5] != 'All'){
    $filter .=' AND USAUTH.ES_Id ='.$filterData[5] ;
}
if($filterData[6] != '' && $filterData[6] != 'All'){
    $filter .=' AND USAUTH.DP_Id ='.$filterData[6] ;
}
if(($filterData[7] != 'All') && ($filterData[7] != '')){
    $filter .=' AND USAUTH.US_Status ='.$filterData[7] ;
}
else if ($filterData[7] == ''){
    $filter .=' AND USAUTH.US_Status =1';
}
if(($filterData[8] != 'All') && ($filterData[8] != '')){
    $filter .=' AND USAUTH.US_AttndFlag ='.$filterData[8] ;
}
if(($filterData[9] != 'All') && ($filterData[9] != '')){
    $filter .=' AND USAUTH.US_WrkHrFlag ='.$filterData[9] ;
}
$filterUSR = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
$UserObj = new UserClass();
$TL_Count=$UserObj->countUserGrid($filterUSR,$preTally_user_id,$filter);
$UserObj->viewUserGrid($filterUSR,$preTally_user_id,$filter,$_GET["posStart"],$_GET["count"]);

$US_Obj = $UserObj->UserArray;

if($preTally_user_ofid == 1) { $colField = "Company"; } else { $colField = "Designation"; }

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$TL_Count.'" pos="'.$_GET["posStart"].'">
    <userdata name="TL_Count">'.$TL_Count.'</userdata>
    <userdata name="db_table">users</userdata>
    <userdata name="db_primary">US_Id</userdata>
    <userdata name="db_date">US_MDate</userdata>
    <userdata name="db_status">US_Status</userdata>
    <userdata name="acl_status">'.$ACL_Obj->ACL_HR_VM.'</userdata>';
    if($_GET["posStart"]==0 && $_GET["calltype"]=="new"){
    echo '<head>
        <column width="50" type="ed" align="center" sort="na">	Slno </column>
        <column width="100" type="ro" align="left" sort="na">	User ID</column>
        <column width="*" type="ro" align="left" sort="na">Name </column>
        <column width="150" type="ro" align="left" sort="na">ACL Type</column>
        <column width="150" type="ro" align="left" sort="na"> '. $colField .' </column>
        <column width="*" type="ro" align="left" sort="na"> Branch </column>
        <column width="100" type="ro" align="left" sort="na">Employee Status</column>
        <column width="100" type="ro" align="left" sort="na">Department</column>
        <column width="0" type="ro" align="center" sort="na">USStatus</column>
        <column width="60" type="ro" align="center" sort="na">Status</column>';
        if($ACL_Obj->ACL_User){
            echo '<column width="40" type="ro" align="center" sort="na">Edit</column>';
            echo '<column width="40" type="ro" align="center" sort="na">View</column>';
            echo '<column width="100" type="ch" align="center" sort="na">Attendance Not Mandatory</column>';
            echo '<column width="100" type="ch" align="center" sort="na">Attendance Timing Not Mandatory</column>';
        }
        echo '<settings>
                <colwidth>px</colwidth>
        </settings>
        <beforeInit> 
            <call command="setSkin">
                <param>dhx_skyblue</param>
            </call> 
            <call command="setImagePath">
                <param>assets/grid/codebase/imgs/</param>
            </call> 
            <call command="enableSmartRendering">
                <param>false</param>
            </call> 
        </beforeInit> 
        <afterInit>
        </afterInit>

    </head>'; 
        }
    if($US_Obj) {
        $j=$_GET["posStart"]+1; 
        foreach($US_Obj as $rw) {	
            if($preTally_user_ofid == 1) { $rwValue= $rw->OF_Name; } else { $rwValue= $rw->DG_Name; }            
            echo '<row id="'.$rw->US_Id.'">
                <userdata name="db_resign_status">'.$rw->ES_Resign.'</userdata>
                <cell>'.$j.'</cell>
                <cell>'.$rw->US_EMPID.'</cell>
                <cell>'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                <cell>'.$rw->ACL_Name.'</cell>
                <cell>'.$rwValue.'</cell>
                <cell>'.$rw->LC_Name.'</cell>
                <cell>'.$rw->ES_Name.'</cell>
                <cell>'.$rw->DP_Name.'</cell>';   
            $statLabel  = ($rw->US_Status == '0') ? "Blocked By Admin" : "Approved";
                if($rw->US_Status == '0') { 
                    $statusImg = 'cross.png';
                    $USStatus="Blocked";
                } else { 
                    $statusImg = 'tick.png';
                    $USStatus="Approved";
                }
                echo'<cell>'.$USStatus.'</cell>';  
                echo '<cell><![CDATA[<img src="images/icon/'.$statusImg.'" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$statLabel.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                if($ACL_Obj->ACL_User){
                    if($rw->ES_Resign!=1){
                        echo '<cell><![CDATA[<img src="images/icon/pencil.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Edit Profile\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';  
                    }else{
                        echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$statLabel.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';  
                    }  
                echo '<cell><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'View Profile\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';    
                echo '<cell name="att_check">'.$rw->US_AttndFlag.'</cell>';  
                 echo '<cell name="wrkhrs_check">'.$rw->US_WrkHrFlag.'</cell>';  
                }
                echo '</row>';	
            $j++;
        }
    }

echo '</rows>';
?>