<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/BankClass.php");

$BankObj = new BankClass();
$BankObj->viewBanks(' ORDER BY BNK_Name');
$Bank_Obj = $BankObj->BankArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">Banks</userdata>
		<userdata name="db_primary">Bank_Id</userdata>
		<userdata name="db_name">Bank_name</userdata>		
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Bank </column>
                        
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
		  if($Bank_Obj) {
				$j = 1;
				foreach($Bank_Obj as $rw) {
					
					echo '<row id="'.$rw->BNK_Id.'">
						<userdata name="BNK_Name">'.$rw->BNK_Name.'</userdata>
						<userdata name="BNK_Status">'.$rw->BNK_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="Bank_Name">'.$rw->BNK_Name.'</cell> ';                                              
						if($rw->BNK_Status == '0') { 
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