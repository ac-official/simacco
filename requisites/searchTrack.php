<?php
require_once($BASEPATH . "preTallyClass/TrackClass.php");

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$job_inc=0;
$job_exp=0;
$inc=0;
$exp=0;
$balance=0;
$filter=" AND TR.TR_Id=".$REQUEST['trname'];
$TrackObj = new TrackClass();
$TrackObj->viewSearchTracks($filter);
$Trk_Obj = $TrackObj->TrackArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
    <userdata name="db_table">balance_sheets</userdata>
    <userdata name="db_primary">BS_Id</userdata>
    <userdata name="db_date">BS_MDate</userdata>
    <userdata name="db_status">BS_Status</userdata>
    
    <head>
        
        <column width="55" type="ro" align="center" sort="na">Sl No. </column>
        <column width="*"  type="ro" align="left"   sort="na">Track Items </column>
        <column width="90"  type="ro" align="right"   sort="na">Job Amount</column>
        <column width="90"  type="ro" align="right"   sort="na">Income</column>
        <column width="90"  type="ro" align="right"   sort="na">Expense</column>
        <column width="118" type="ro" align="left" sort="na">Added By</column>
        <column width="100" type="ro" align="left" sort="na">Branch</column>
        <column width="79" type="ro" align="left" sort="na">Date</column>
        
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
            //$income  = ($rw->MH_Type == '1') ? $amount : "---";
            $expense = ($rw->MH_Type == '2') ? $amount : "---";
            // 15-05-2025 changes start 
            $income  = "---";
            $jobamt  = "---";
            if ($rw->MH_Type == '1') {
                if($rw->IT_Business=="1")
                {
                    $jobamt = $rw->BS_Amount;
                } else {
                    $income = $rw->BS_Amount;
                }
            }
            // changes ends (extra column for job amt and hide the job actual from income)

            if($rw->MH_Type=="1")
            {
                if($rw->IT_Business=="1")
                {
                    $job_inc+=$rw->BS_Amount;
                }
                else {
                   $inc+=$rw->BS_Amount; 
                }
            }
            else if($rw->MH_Type=="2")
            {
                if($rw->IT_Business=="1")
                {
                    $job_exp+=$rw->BS_Amount;
                }
                else
                {
                   $exp+=$rw->BS_Amount; 
                }
            }
            $bgcolor = ($rw->BS_Status == 1) ? '' :'style="color:#ef8429;"';
            echo '<row id="'.$rw->BS_Id.'" '.$bgcolor.'>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>                    
                    <cell>'.$j.'</cell>                   
                    <cell><![CDATA[<div onmouseover="preTally.Settings.showLabel(this,\''.$rw->IT_Name.' - '.$rw->DS_Description.' - '.$rw->TR_Track.'\');" onmouseout="preTally.Settings.hideLabel(this);"> '.$rw->IT_Name.' - '.$rw->DS_Description.' - '.$rw->TR_Track.'</div>]]></cell>
                    <cell>'.$jobamt.'</cell>
                    <cell>'.$income.'</cell> <cell>'.$expense.'</cell>
                    <cell>'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell>'.$rw->LC_Name.'</cell>
                    <cell>'.date('d/m/Y',strtotime($rw->BS_Date)).'</cell>
                    <cell></cell>
                </row>';
            $j++;
        }
    } else { 
        echo '<row id="0"> <cell colspan="8"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
        $balance=$job_inc-$job_exp-$inc;
            echo '<userdata name="job_income">'.number_format($job_inc,2).'</userdata>
                <userdata name="job_expense">'.number_format($job_exp,2).'</userdata>
                <userdata name="track_income">'.number_format($inc,2).'</userdata>
                <userdata name="track_expense">'.number_format($exp,2).'</userdata>
                <userdata name="track_balance">'.number_format($balance,2).'</userdata>
        </rows>';
?>