<?php

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
$key = $REQUEST["sortKey"];
$BalSheetObj = new BalanceSheetClass();
$BalSheetObj->getSavedBSItemList($preTally_user_id,$key);
$BS_Obj = $BalSheetObj->BalanceSheetArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">balance_sheets</userdata>
    <userdata name="db_primary">BS_Id</userdata>
    <userdata name="db_date">BS_MDate</userdata>
    <userdata name="db_status">BS_Status</userdata>
    <head>
        <column width="40" type="ro" align="center" sort="na"> </column>
        <column width="60" type="ro" align="center" sort="na"> SL No. </column>
        <column width="*"  type="ro" align="left"   sort="na"> Balance Sheet Items </column>
        <column width="150"  type="ro" align="right"   sort="int"> Amount </column>
        <column width="100" type="ro" align="center" sort="na">Date</column>
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
                <param>true</param><param>20</param>
            </call> 
            <call command="enableAutoWidth">
                <param>true</param>
            </call> 
             
        </beforeInit> 
        <afterInit>
           
        </afterInit>

    </head>';
    

    if($BS_Obj) {
        $j = 1;
        foreach($BS_Obj as $rw) {

            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <cell type="sub_row_ajax">requisites/BSItem_Details.php&SHID='.$rw->SH_Id.'&amp;BSID='.$rw->BS_Id.'</cell>
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->IT_Name.' - '.$rw->DS_Description.'</cell>
                    <cell>'.number_format($rw->BS_Amount,2).'</cell>
                    <cell>'.$rw->BS_Date.'</cell>
                     <cell><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Reports.showDetailData(this,'.$rw->BS_Id.');" />]]></cell>
                </row>';
            $j++;
        }
    }
echo '</rows>';
?>