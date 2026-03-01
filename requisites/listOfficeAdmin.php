<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");

$OfficeObj = new OfficeClass();
$OfficeObj->viewOfficeAdmins();
$OF_Obj = $OfficeObj->OfficeArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
                        <column width="*" type="ro" align="left" sort="int">Company Admin</column>
			<column width="*" type="ro" align="left" sort="int">Company</column>
                        <column width="60" type="ro" align="left" sort="int">Count</column>
			<column width="60" type="ro" align="center" sort="str">	 </column>
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
					<param>,#text_filter,#text_filter,,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($OF_Obj) {
				$j = 1;
				foreach($OF_Obj as $rw) {
                                    
                                        $OF_Map = explode(",", $rw->UAM_Map);
                                        echo $key = array_search($rw->US_Id,$OF_Map);
                                        if(!empty($key) || $key == 0) unset($OF_Map[$key]);
                                        $mapCount = count(array_filter($OF_Map) );
					
					echo '<row id="'.$rw->OF_Id.'">
						<userdata name="OF_Name">'.$rw->OF_Name.'</userdata>
                                                <userdata name="US_Id">'.$rw->US_Id.'</userdata>     
                                                <userdata name="US_EMPID">'.$rw->US_EMPID.'</userdata>    
                                                <userdata name="US_FName">'.$rw->US_FName.'</userdata>
                                                <userdata name="US_LName">'.$rw->US_LName.'</userdata>
						
                                                <cell>'.$j.'</cell>
                                                <cell name="OF_Name">'.$rw->US_FName.'  '.$rw->US_LName.' </cell>
						<cell name="OF_Name">'.$rw->OF_Name.'</cell>
						<cell>'.$mapCount.'</cell>
                                                <cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Office.listOfzAdmin(this,'.$rw->US_Id.');"/>]]></cell>';
                                        echo '</row>';
					$j++;
				}
			}
		  
echo '</rows>';
?>