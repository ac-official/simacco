<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}


require_once($BASEPATH . "preTallyClass/PettyCashClass.php");
$PettyCashObj = new PettyCashClass();

$filter = 'BS.BS_PettyCashRefId = '.$REQUEST['BSId'];
$PettyCashObj->reportPettyCashDetails($REQUEST['f'],$REQUEST['t'],$filter);
$MR_Obj = $PettyCashObj->PettyCashArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 

echo '<rows>
        <head>
            <column width="50"  type="ro" align="center" sort="na"> SlNo </column>
            <column width="*"   type="ro" align="left"   sort="na">#text_filter_inc</column>
            <column width="*"   type="ro" align="left"   sort="na">#text_filter_inc</column>
            <column width="120" type="ro" align="center" sort="na">#text_filter_inc</column>
            <column width="100" type="ro" align="center" sort="na">Amount</column>
            <column width="120" type="ro" align="center" sort="na">Date</column>
            <column width="150" type="ro" align="center" sort="na">#combo_filter</column>
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
            <afterInit>

            </afterInit>

	</head>';

echo '<userdata name = "BS_RefId">'.$REQUEST['BSId'].'</userdata>';
    if($MR_Obj) {
        $j = 1;
        foreach($MR_Obj as $rw) {            
            $date = new DateTime($rw->BS_Date); 
            
            echo '
                <row id="'.$rw->BS_Id.'">
                    <cell>'.$j.'</cell>
                    <cell>'.$rw->IT_Name.'</cell>
                    <cell>'; if($rw->DS_Description != ''){echo $rw->DS_Description;}else{ echo "--"; } echo '</cell>                    
                    <cell>'; if($rw->TR_Track != ''){echo $rw->TR_Track;}else{ echo "--"; } echo '</cell>     
                     
                    <cell>'.$rw->BS_Amount.'</cell>
                    <cell>'.$date->format('d/m/Y').'</cell>    
                    <cell>'.$rw->US_FName.' '.substr($rw->US_LName,0,1).'</cell>    
                </row>';
            $j++;
        }
     } else { echo '<row id="0"> <cell colspan="7"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
echo '</rows>';
?>