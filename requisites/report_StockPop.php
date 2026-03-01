<?php
require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");
require_once($BASEPATH . "preTallyClass/TrackClass.php");

if($REQUEST['SHID']!=58 && $REQUEST['SHID']!=59){
    $SHID   = $REQUEST['SHID'];
    $BSID   = $REQUEST['BSID'];
    $BalSheetObj = new BalanceSheetClass();
    $PatternObj = new SubheadPatternClass();
    $UserObj = new UserClass();

    $TrackObj = new TrackClass();
    $filter = " AND BS.BS_Status = 1 AND TR.TR_Id = ".$REQUEST['trname'];
    $TrackObj->viewSearchTracks($filter);
    $Trk_Obj = $TrackObj->TrackArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="db_table">balance_sheets</userdata>
    <userdata name="db_primary">BS_Id</userdata>
    <userdata name="db_date">BS_MDate</userdata>
    <userdata name="db_status">BS_Status</userdata>
    
    <head>
        
        <column width="50"  type="ro" align="center" sort="na">SlNo</column>
        <column width="*"   type="ro" align="left"   sort="na">Track Items </column>
        <column width="100" type="ro" align="right"  sort="na">Income</column>
        <column width="100" type="ro" align="right"  sort="na">Expense</column>
        <column width="100" type="ro" align="center" sort="str">Date</column>
        <column width="100" type="ro" align="center" sort="str">Branch</column>
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
    if($Trk_Obj) {
        $j = 1;        
        foreach($Trk_Obj as $rw) {
            $amount  = number_format($rw->BS_Amount,2);
            $income  = ($rw->MH_Type == '1') ? $amount : "---";
            $expense = ($rw->MH_Type == '2') ? $amount : "---";
                       
            echo '<row id="'.$rw->BS_Id.'">
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>                    
                    <cell title=" ">'.$j.'</cell>                   
                    <cell title=" ">'.$rw->IT_Name.' - '.$rw->DS_Description.' - '. $rw->TR_Track.'</cell>
                    <cell title=" ">'.$income.'</cell>
                    <cell title=" ">'.$expense.'</cell>
                    <cell title=" ">'.date("d/m/Y", strtotime($rw->BS_Date)).'</cell>
                    <cell title=" ">'.$rw->LC_Name.'</cell>    
                </row>';
            $j++;
        }
    } else { 
        echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
        echo'</rows>';
}
?>
   

