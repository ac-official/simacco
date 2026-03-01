<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


require_once($BASEPATH . "preTallyClass/MasterReportsLocationBasedClass.php");
$MstrRptObj = new MasterReportsLocationBasedClass();

$filter = 'BS.IT_Id = '.$REQUEST['ITId'].' AND BS.BS_IEByLC = '.$REQUEST['LCId'] .' AND LC.OF_Id ='.$preTally_user_ofid;
$MstrRptObj->reportItemBasedData($REQUEST['f'],$REQUEST['t'],$filter);
$MR_Obj = $MstrRptObj->MasterReportArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
echo '<rows total_count="'.$Count.'" pos="'.$_GET["posStart"].'">
      <userdata name="TL_Count">'.$Count.'</userdata>';
    if($MR_Obj) {        
        $j=$_GET["posStart"]+1;                    
        foreach($MR_Obj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
            $color = 'black';
            if($rw->BS_MinAmount != '' && $rw->BS_MaxAmount != '') {
                if($rw->BS_Amount < $rw->BS_MinAmount ) $color = '#4DB84D; font-weight : bold;';
                else if($rw->BS_Amount > $rw->BS_MaxAmount) $color = 'red';
            }             
            $footer     = $footerStyle = $cellFormat = '';
            $footer         .= ",{#stat_total}";
            $footerStyle    .= ",text-align:right;";
            $cellFormat     .= ",70";            
           echo '<head>';           
            echo '<afterInit>
                    <call command="setColumnMinWidth"><param>,'.$cellFormat.',,</param></call>
                    <call command="attachFooter"><param>Total,#cspan,#cspan,#cspan,{#stat_total},#cspan,#cspan</param>
                    <param>text-align:left'.$footerStyle.',text-align:right,text-align:right,text-align:right</param></call>
            </afterInit> ';
        echo '</head>';  
            
            echo '
                <row id="'.$rw->BS_Id.'">
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>    
                    <userdata name="SH_Track">'.$rw->SH_Track.'</userdata>
                    <userdata name="TR_Id">'.$rw->TR_Id.'</userdata>
                    <userdata name="CHQ_Number">'.$rw->CHQ_Number.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'; if($rw->DS_Description != ''){echo $rw->DS_Description;}else{ echo "--"; } echo '</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                     
                    <cell style="color : '.$color.'">'.round($rw->BS_Amount).'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0,1).'</cell>    
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell></cell><cell></cell><cell><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>