<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/BankBSClass.php");

$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);

if(($keyTemp['0'])== 'BA'){
    $fields = 'BnkOB_OpenBal as OB';
    $filterOB = $filter = 'BA_Id = '.$keyTemp[1].' ';
}else{
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = 'US.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'OF_Id = '.$keyTemp[1].' ';
}

$BankBSObj = new BankBSClass();
$BankBSObj->ocBarChartBankData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $BankBSObj->BankBSArray;

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

