<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/CashBSClass.php");

$ACLReq = $ACL_Obj->ACL_BSheet;

$temp=  explode('-', $REQUEST['r']);
$OFkeyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);

if(($temp['1'])){
    $filter = 'LC_Id = '.$LCkeyTemp[1].' ';
}else if($ACLReq == 2){
    $filter = 'LC_Id = '.$preTally_user_lcid.' ';
}else{
    $filter = 'OF_Id = '.$OFkeyTemp[1].' ';
}

$CashBSObj = new CashBSClass();
$CashBSObj->ocBarChartCashData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $CashBSObj->CashBSArray;

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
                    <userdata name = "shId" >'.$rw->SH_Id.'</userdata>
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

