<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/AddressClass.php");
$OfficeObj = new OfficeClass();
$AddressObj = new AddressClass();
$OfficeObj->viewOfficeGrid();
$OF_Obj = $OfficeObj->OfficeArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">offices</userdata>
		<userdata name="db_primary">OF_Id</userdata>
		<userdata name="db_date">OF_MDate</userdata>
		<userdata name="db_status">OF_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int">Company</column>
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
		  if($OF_Obj) {
				$j = 1;
				foreach($OF_Obj as $rw) {
					
					echo '<row id="'.$rw->OF_Id.'">
						<userdata name="OF_Name">'.$rw->OF_Name.'</userdata>
						<userdata name="AP_Id">'.$rw->AP_Id.'</userdata> 	 	
                                                <userdata name="AP_Name">'.$AddressObj->getPlaceName($rw->AP_Id).'</userdata>    
                                                <userdata name="OF_Street">'.$rw->OF_Street.'</userdata>
                                                <userdata name="OF_Building">'.$rw->OF_Building.'</userdata>
                                                <userdata name="OF_Phone">'.$rw->OF_Phone.'</userdata>
                                                <userdata name="OF_Pincode">'.$rw->OF_Pincode.'</userdata>
                                                <userdata name="SR_Name">'.$rw->SR_Name.'</userdata>
                                                <userdata name="PL_Name">'.$rw->PL_Name.'</userdata>
                                                <userdata name="ALC_Name">'.$rw->ALC_Name.'</userdata>                                                    
                                                <userdata name="CT_Name">'.$rw->CT_Name.'</userdata>
						<userdata name="ST_Name">'.$rw->ST_Name.'</userdata>
						<userdata name="CN_Name">'.$rw->CN_Name.'</userdata>
						<userdata name="OF_Comments">'.$rw->OF_Comments.'</userdata>
						<userdata name="OF_Status">'.$rw->OF_Status.'</userdata>
                                                <userdata name="US_Id">'.$rw->US_Id.'</userdata>     
                                                <userdata name="US_Email">'.$rw->US_Email.'</userdata>    
                                                <userdata name="US_FName">'.$rw->US_FName.'</userdata>
                                                <userdata name="US_LName">'.$rw->US_LName.'</userdata>
                                                <userdata name="TZ_Id">'.$rw->TZ_Id.'</userdata>
                                                <userdata name="CR_Id">'.$rw->CR_Id.'</userdata>   
						<cell>'.$j.'</cell>
						<cell name="OF_Name">'.$rw->OF_Name.'</cell>';
						if($rw->OF_Status == '0') { 
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