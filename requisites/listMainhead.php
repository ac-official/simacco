<?php
include_once($BASEPATH . "preTallyClass/MainheadClass.php");

$MainheadObj = new MainheadClass();
$MainheadObj->viewMainheads(' WHERE 1 ORDER BY MH_Name');
$MH_Obj = $MainheadObj->MainheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">categories</userdata>
		<userdata name="db_primary">MH_Id</userdata>
		<userdata name="db_date">MH_MDate</userdata>
		<userdata name="db_status">MH_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Mainhead </column>
			<column width="60" type="ro" align="center" sort="str">	Status </column>
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
				<call command="attachHeader">
					<param>,#text_filter,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($MH_Obj) {
				$j = 1;
				foreach($MH_Obj as $rw) {
					
					echo '<row id="'.$rw->MH_Id.'">
						<userdata name="MH_Name">'.$rw->MH_Name.'</userdata>
						<userdata name="MH_Comments">'.$rw->MH_Comments.'</userdata>
						<userdata name="MH_Status">'.$rw->MH_Status.'</userdata>
                                                <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="MH_Name">'.$rw->MH_Name.'</cell>';
                                                if($rw->MH_Status == '0') { 
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