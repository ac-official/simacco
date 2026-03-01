<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/SubheadClass.php");

$SubheadObj = new SubheadClass();
$SubheadObj->viewSubheads(' AS SH, main_heads AS MH  WHERE SH.MH_Id=MH.MH_Id ORDER BY SH.SH_Name');
$SH_Obj = $SubheadObj->SubheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">Subheads</userdata>
		<userdata name="db_primary">SH_Id</userdata>
		<userdata name="db_date">SH_MDate</userdata>
		<userdata name="db_status">SH_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Subhead </column>
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
					<param>,#text_filter,#select_filter_strict,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($SH_Obj) {
				$j = 1;
				foreach($SH_Obj as $rw) {
					
					echo '<row id="'.$rw->SH_Id.'">
                                                <userdata name="SH_Track">'.$rw->SH_Track.'</userdata>
						<userdata name="SH_Name">'.$rw->SH_Name.'</userdata>
						<userdata name="SH_Comments">'.$rw->SH_Comments.'</userdata>
                                                <userdata name="MH_Id">'.$rw->MH_Id.'</userdata>
						<userdata name="SH_Status">'.$rw->SH_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="ST_Name">'.$rw->SH_Name.'</cell>
                                                <cell name="MH_Name">'.$rw->MH_Name.'</cell>';
						if($rw->SH_Status == '0') { 
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