<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");
$HI_Obj = new HistoryClass();

$HI_Obj->bsEntryUpdateSettingsHistory($preTally_user_ofid);
$HIObj = $HI_Obj->HistoryArray;
//print_r($HIObj);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>
        <head>
            <column width="50" type="ro" align="center" sort="na"> SlNo </column>
            <column width="*" type="ro" align="left" sort="na">Date</column>
            <column width="*" type="ro" align="left" sort="na">Added By</column>
            <column width="0" type="ro" align="left" sort="na">Updated By</column>
            <column width="*" type="ro" align="left" sort="na">Updated On</column>    
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
        </head>';
        if($HIObj) {
            $j = 1;
            foreach($HIObj as $rw) {
                $updatedBy = $rw->UpdatedBy != '' ? $rw->UpdatedBy : "----";
                echo '<row id="'.$j.'">
                        <cell>'.$j.'</cell>
                        <cell>'.date("d-m-Y",strtotime($rw->BES_Date)).'</cell>
                        <cell>'.$rw->AddedBy.'</cell>
                        <cell>'.$updatedBy.'</cell>
                        <cell>'.date("d-m-Y",strtotime($rw->UpdatedDate)).'</cell> ';                                              
                echo '</row>';	
                $j++;
            }
        }
echo '</rows>';
?>
