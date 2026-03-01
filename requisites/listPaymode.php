<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/PaymodeClass.php");

$PaymodeObj = new PaymodeClass();
$PaymodeObj->viewPaymodes(' ORDER BY PM_Name');
$Paymode_Obj = $PaymodeObj->PaymodeArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">Payment_modes</userdata>
		<userdata name="db_primary">PM_Id</userdata>
		<userdata name="db_name">PM_Name</userdata>		
		<head>
			<column width="40" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int">Payment Mode</column>
                        
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
		  if($Paymode_Obj) {
				$j = 1;
				foreach($Paymode_Obj as $rw) {
					
					echo '<row id="'.$rw->PM_Id.'">
						<userdata name="PM_Name">'.$rw->PM_Name.'</userdata>
						<userdata name="PM_Comments">'.$rw->PM_Comments.'</userdata>
                                                <userdata name="PM_Status">'.$rw->PM_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="PM_Name">'.$rw->PM_Name.'</cell> ';                                              
						if($rw->PM_Status == '0') { 
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