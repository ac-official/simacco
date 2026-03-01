<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/BankClass.php");

$BranchObj = new BankClass();

echo $BranchObj->viewBranchGrid("WHERE BB.BNK_Id=BNK.BNK_Id AND BB.OF_Id=".$preTally_user_ofid." ORDER BY BB_Name");
$Branch_Obj = $BranchObj->BranchArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<userdata name="db_table">Bank_Branches</userdata>
		<userdata name="db_primary">BB_Id</userdata>
		<userdata name="db_name">BB_Name</userdata>
                <userdata name="db_status">BB_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na"> Branch</column>                    
                        <column width="*" type="ro" align="left" sort="na"> Bank Name</column>
                        <column width="0" type="ro" align="center" >BB_Status</column>
			<column width="60" type="ro" align="center">Status</column>
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
		  if($Branch_Obj) {
				$j = 1;
				foreach($Branch_Obj as $rw) {
					
					echo '<row id="'.$rw->BB_Id.'">
						<userdata name="BB_Name">'.$rw->BB_Name.'</userdata>
                                                <userdata name="BNK_Id">'.$rw->BNK_Id.'</userdata>
                                                <userdata name="BNK_Name">'.$rw->BNK_Name.'</userdata>    
                                                <userdata name="BB_Address">'.$rw->BB_Address.'</userdata>        
                                                <userdata name="BB_Comments">'.$rw->BB_Comments.'</userdata>    
						<userdata name="BB_Status">'.$rw->BB_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="Branch_Name">'.$rw->BB_Name.'</cell>
                                                <cell name="Bank_Name">'.$rw->BNK_Name.'</cell>'; 
                                                   if($rw->BB_Status==1) {
                                                        $BB_Status="Approved";
                                                    } else { 
                                                        $BB_Status="Blocked";
                                                    }
                                                echo'<cell>'.$BB_Status.'</cell>';                                             
						if($rw->BB_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$BB_Status.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$BB_Status.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                }
                                        echo '</row>';	
                                        $j++;
				}
			}
		  
echo '</rows>';
?>