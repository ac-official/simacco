<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/ReportClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$ReportObj = new ReportClass();

$newParm='';
$H_ID = base64_decode($_REQUEST['ID']);

if($H_ID){
    $newParm = $H_ID;
} else {
    $temp=  explode('-', $REQUEST['r']);
    $keyTemp = explode('_', $temp['0']);

    foreach ($temp as $value) {
        $newTemp = explode('_', $value);
        $newFilt.= $newTemp['0']."_Id =  '".$newTemp['1']."' AND ";
    }
    if($ACL_Obj->ACL_BSheet != 5) 
    $newFilt.= filterBS_User($ACL_Obj->ACL_BSheet, '', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);
    $newFilt.='1';
    $newParm = "SELECT US_Id FROM `users_auth` WHERE ".$newFilt." AND US_Status != 5";
} 

$ReportObj->ieBarChartData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$newParm);
$RP_Obj = $ReportObj->ReportArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">departments</userdata>
    <userdata name="db_primary">DP_Id</userdata>
    <userdata name="db_date">DP_MDate</userdata>
    <userdata name="db_status">DP_Status</userdata>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na"> Category </column>
        <column width="100" type="ro" align="right" sort="na"> Amount </column>
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
            <call command="enableSmartRendering">
                <param>false</param>
            </call> 
        </beforeInit> 
        <afterInit> </afterInit>
    </head>';

if($RP_Obj) {   
    $j = 1;
    $i = 1;
    foreach($RP_Obj as $rw) {   
        if($rw->MH_Type == $REQUEST['ie']){
            if($j > 9) {
                echo '<row id="'.$i.'">
                    <cell>'.$i.'</cell>
                    <cell>'.$rw->SH_Name.'</cell>
                    <cell>'.number_format($rw->IE).'   '.$currency.' </cell>
                </row>';
                $i++;
            } 
            $j++;
        }
    }
}
echo '</rows>';
?>

