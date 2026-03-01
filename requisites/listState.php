<?php
include_once($BASEPATH . "preTallyClass/StateClass.php");

$StateObj = new StateClass();
$StateObj->viewStatesGrid();
$ST_Obj = $StateObj->StateArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
            <column width="40" type="ro" align="center" sort="int"> #</column>
            <column width="*" type="ro" align="left" sort="str"> State </column>
            <column width="*" type="ro" align="left" sort="str"> Country </column>
	    <column width="55" type="ro" align="center" sort="na">Status </column>
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
            </beforeInit> 
			<afterInit>
				<call command="attachHeader">
					<param>,#text_filter,#select_filter_strict,</param>
				</call>
				
			</afterInit>
            </head>';
            if($ST_Obj){ $j=1;
                        foreach ($ST_Obj as $rw) {
                                echo '<row id="'.$rw->ST_Id.'">
                                <userdata name="ST_Name">'.$rw->ST_Name.'</userdata>
                                <userdata name="CN_Id">'.$rw->CN_Id.'</userdata>
				<userdata name="ST_Status">'.$rw->ST_Status.'</userdata>
                                <cell>'.$j.'</cell>
				<cell name="ST_Name">'.$rw->ST_Name.'</cell>
                                <cell name="CN_Name">'.$rw->CN_Name.'</cell> ';
                                if($rw->ST_Status == '0') { 
                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
				} else {
                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                                }
                                echo '</row>';
                                $j++;
				}
                                      
            }
echo '</rows>';
?>