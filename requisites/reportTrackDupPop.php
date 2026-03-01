<?php
require_once($BASEPATH . "preTallyClass/TrackDupRptClass.php");
    $TrackObj = new TrackDupRptClass();
    $filter = " AND TR.TR_Id = ".$REQUEST['trname']." ORDER BY IT.IT_Name ASC";
    $TrackObj->viewSearchTracks($filter);
    $Trk_Obj = $TrackObj->TrackArray;
    if(!$_REQUEST['F_Date'])$_REQUEST['F_Date']="1970-01-01";
    if(!$_REQUEST['T_Date'])$_REQUEST['T_Date']="2100-01-01";
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="db_table">balance_sheets</userdata>
    <userdata name="db_primary">BS_Id</userdata>
    <userdata name="db_date">BS_MDate</userdata>
    <userdata name="db_status">BS_Status</userdata>
    <head>
        
        <column width="50"  type="ro" align="center" sort="na">SlNo </column>
        <column width="*"   type="ro" align="left"   sort="na">Track Items </column>
        <column width="100" type="ro" align="right"  sort="na">Income &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="3" class="btn_Sort_trackDupPop"/&gt;</column>
        <column width="0"   type="ro" align="left"   sort="int">Inc</column>
        <column width="100" type="ro" align="right"  sort="na">Expense  &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="5" class="btn_Sort_trackDupPop"/&gt;</column>        
        <column width="0"   type="ro" align="left"   sort="int">Exp</column>
        <column width="100" type="ro" align="center" sort="na">Date &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="6" class="btn_Sort_trackDupPop"/&gt;</column>
        <column width="100" type="ro" align="right"  sort="na">Added By  &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="7" class="btn_Sort_trackDupPop"/&gt;</column>
        <column width="150" type="ro" align="center" sort="str">Branch &lt;img src="images/icon/sort-ascending-icon.png" title="Click here to Sort" colNum="8" class="btn_Sort_trackDupPop"/&gt;</column>
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
            if($_REQUEST['IT_Id']==$rw->IT_Id && strtotime($_REQUEST['F_Date'])<= strtotime($rw->BS_Date) && strtotime($_REQUEST['T_Date'])>= strtotime($rw->BS_Date))                
                    $bgcolor = 'style="background-color:#33cc33;"';                
                            else
                    $bgcolor = ''; 
            $amount  = number_format($rw->BS_Amount,2);
            $income  = ($rw->MH_Type == '1') ? $amount : "---";
            $alt_income  = ($rw->MH_Type == '1') ? $rw->BS_Amount : "---";
            $expense = ($rw->MH_Type == '2') ? $amount : "---";
            $alt_expense = ($rw->MH_Type == '2') ? $rw->BS_Amount : "---";           
            echo '<row id="'.$rw->BS_Id.'" '.$bgcolor.'>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>                    
                    <cell title=" ">'.$j.'</cell>                   
                    <cell title=" ">'.$rw->IT_Name.' - '.$rw->DS_Description.' - '. $rw->TR_Track.'</cell>
                    <cell title=" ">'.$income.'</cell>                    
                    <cell title=" ">'.$alt_income.'</cell>                    
                    <cell title=" ">'.$expense.'</cell>                    
                    <cell title=" ">'.$alt_expense.'</cell>    
                    <cell title=" ">'.date("d/m/Y", strtotime($rw->BS_Date)).'</cell>
                    <cell title=" ">'.$rw->US_FName.' '.$rw->US_LName.'</cell>    
                    <cell title=" ">'.$rw->LC_Name.'</cell>    
                </row>';
            $j++;
        }
    } else { 
        echo '<row id="0"> <cell></cell><cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
        
    }
        echo'</rows>';

?>
   

