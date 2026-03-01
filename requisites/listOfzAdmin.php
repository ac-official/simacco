<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");

$OfficeObj = new OfficeClass();

$USId = $REQUEST['USId'];
$OfficeObj->viewOfficeAdmins(' AND US.US_Id != '.$USId.' AND ( find_in_set( '.$USId.', UAM.UAM_Map) || UAM.UAM_Map IS NULL )');
$OF_Obj = $OfficeObj->OfficeArray;

$OfficeObj->myMapOffice($USId);    
$Map_Obj = $OfficeObj->OfficeMapArray;
$mapArray =  explode(",", $Map_Obj->UAM_Map);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
                        <column width="*" type="ro" align="left" sort="int">Company Admin</column>
			<column width="*" type="ro" align="left" sort="int">Company</column>
                        <column width="60" type="ch" align="left" sort="int"></column>
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
					<param>,#text_filter,#text_filter,#master_checkbox,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($OF_Obj) {
				$j = 1;
				foreach($OF_Obj as $rw) {
//					
					$imgCheckBox = 0;
                                        if(in_array($rw->US_Id, $mapArray)) {
                                            $imgCheckBox = 1;
                                        }
                                                
					echo '<row id="'.$rw->US_Id.'">
						<userdata name="OF_Name">'.$rw->OF_Name.'</userdata>
                                                <userdata name="US_Id">'.$rw->US_Id.'</userdata>     
                                                <userdata name="US_EMPID">'.$rw->US_EMPID.'</userdata>    
                                                <userdata name="US_FName">'.$rw->US_FName.'</userdata>
                                                <userdata name="US_LName">'.$rw->US_LName.'</userdata>
						
                                                <cell>'.$j.'</cell>
                                                <cell name="US_Name">'.$rw->US_FName.'  '.$rw->US_LName.' </cell>
						<cell name="OF_Name">'.$rw->OF_Name.'</cell>
						<cell>'.$imgCheckBox.'</cell>
                                            </row>';
					$j++;
				}
			} else { echo '<row id="0" ><cell colspan = "4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';}
		  
echo '</rows>';
?>