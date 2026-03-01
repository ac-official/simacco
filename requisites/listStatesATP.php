<?php

include_once($BASEPATH . "preTallyClass/StateClass.php");

$StateObj = new StateClass();
$StateObj->viewStates('WHERE CN_Id='.$REQUEST['CNID'].' AND ST_Status=1 ');
$ST_Obj = $StateObj->StateArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
            <column width="40" type="ro" align="center" sort="int"> #</column>
            <column width="*" type="ro" align="left" sort="str"> State </column>
	    <column width="70" type="ch" align="center" sort="na">Select </column>
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
                    <param>true</param>
                    <param>50</param>
                </call> 
                <call command="enableColSpan">
                    <param>true</param>
                </call> 
            </beforeInit> 
            <afterInit>
                <call command="attachHeader">
                    <param>,#text_filter, &lt;input type=\'checkbox\' onclick=\'preTally.Track.listStatesATP_MCB();\' id=\'listStatesATP_MTB\' /&gt; ,</param>
                </call>

            </afterInit>
            </head>';
//#master_checkbox
if ($ST_Obj) {
    $j = 1;
    foreach ($ST_Obj as $rw) {
        echo '<row id="' . $rw->ST_Id . '">
                <cell>' . $j . '</cell>
                <cell>' . $rw->ST_Name . '</cell>
                <cell>0</cell>
              </row>';
        $j++;
    }
} else {
    echo '<row id="10000">
            <cell colspan="3">No States</cell>
          </row>';
}
echo '</rows>';
?>