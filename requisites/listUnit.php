<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/UnitClass.php");

$UnitObj = new UnitClass();
$UnitObj->viewUnits(' ORDER BY UT_Name');
$Unit_Obj = $UnitObj->UnitArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">Units</userdata>
		<userdata name="db_primary">Unit_Id</userdata>
		<userdata name="db_name">Unit_name</userdata>		
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Unit </column>
                        
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
		  </head>';
		  if($Unit_Obj) {
				$j = 1;
				foreach($Unit_Obj as $rw) {
					
					echo '<row id="'.$rw->UT_Id.'">
						<userdata name="UT_Name">'.$rw->UT_Name.'</userdata>
						<userdata name="UT_Comments">'.$rw->UT_Comments.'</userdata>
                                               <userdata name="UT_Status">'.$rw->UT_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="Unit_Name">'.$rw->UT_Name.'</cell> ';                                              
						if($rw->UT_Status == '0') { 
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
