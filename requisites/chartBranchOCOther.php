<?php
require_once($BASEPATH . 'includes/functions.php');
require_once($BASEPATH . "preTallyClass/BranchBSClass.php");

$temp=  explode('-', $REQUEST['r']);
$keyTemp = explode('_', $temp['0']);
$LCkeyTemp = explode('_', $temp['1']);
$BAkeyTemp  = explode('_', $temp['2']);

$ACLReq = $ACL_Obj->ACL_BSheet;

if( $temp['1']  && $temp['2'] ){
    $fields    = ' BnkOB_OpenBal as OB';
    $filter    = ' BA.LC_Id = '.$LCkeyTemp[1].'  AND BA.BA_Id = '.$BAkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' AND BA.BA_Id = '.$BAkeyTemp[1].' ';
}else if( $temp['1']  && !$temp['2'] ){
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$LCkeyTemp[1].' ';
    $filterOB  = 'BA.LC_Id = '.$LCkeyTemp[1].' AND BA.OF_Id = '.$keyTemp[1].' ';
}else if($ACLReq == 2){
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    $filter = ' BA.LC_Id = '.$preTally_user_lcid.' ';
    $filterOB  = 'BA.LC_Id = '.$preTally_user_lcid;
}else{
    $fields = 'SUM(BnkOB_OpenBal) as OB';
    /*$filter = 'US.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'OF_Id = '.$keyTemp[1].' ';*/
    $filter = 'US.OF_Id = '.$keyTemp[1].' AND  BA.OF_Id = '.$keyTemp[1].' ';
    $filterOB  = 'BA.OF_Id = '.$keyTemp[1].' ';
}

$BranchBSObj = new BranchBSClass();
$BranchBSObj->ocBarChartBranchData($REQUEST['r'],$REQUEST['f'],$REQUEST['t'],$filter);
$RP_Obj = $BranchBSObj->BranchBSArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <head>
        <column width= "50" type="ro" align="center" sort = "na"> SlNo </column>
        <column width= "*"  type="ro" align="left" sort = "na"> Category </column>
        <column width="100" type="ro" align="right" sort = "na"> Amount </column>
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
                    <cell>'.number_format($rw->IE).'  '.$currency.' </cell>
                </row>';
                $i++;
            } 
            $j++;
        }
    }
}
echo '</rows>';
?>

