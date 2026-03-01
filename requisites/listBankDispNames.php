<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/BankBSClass.php");

$BankObj = new BankBSClass();
$BankObj->viewBankAccounts($preTally_user_ofid,'');
$Bank_Obj  = $BankObj->BankBSArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Bank Accounts </column>
			<column width="60" type="ch" align="center" sort="str"></column>
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
				<param>,#text_filter_inc,#master_checkbox</param>
                            </call>
			</afterInit>
                        
		  </head>';
		  if($Bank_Obj) {
				$j = 1;
				foreach($Bank_Obj as $rw) {
					echo '<row id="'.$rw->BA_Id.'">
						<cell>'.$j.'</cell>
						<cell name="Bank_Name">'.$rw->BA_DispName.'</cell>
						<cell></cell>';
                                        echo '</row>';	
                                        $j++;
				}
			}
		  
echo '</rows>';
?>