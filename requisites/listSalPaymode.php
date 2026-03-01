<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/SalPaymodeClass.php");

$SalPaymodeObj = new SalPaymodeClass();
$SalPaymodeObj->viewSalPaymodes(' ORDER BY SP_Name');
$SalPaymode_Obj = $SalPaymodeObj->SalPaymodeArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">salary_paymodes</userdata>
		<userdata name="db_primary">SP_Id</userdata>
		<userdata name="db_name">SP_Name</userdata>		
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
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
		  if($SalPaymode_Obj) {
				$j = 1;
				foreach($SalPaymode_Obj as $rw) {
					
					echo '<row id="'.$rw->SP_Id.'">
						<userdata name="SP_Name">'.$rw->SP_Name.'</userdata>
						<userdata name="SP_Comments">'.$rw->SP_Comments.'</userdata>
                                                <userdata name="SP_Status">'.$rw->SP_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="SP_Name">'.$rw->SP_Name.'</cell> ';                                              
						if($rw->SP_Status == '0') { 
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