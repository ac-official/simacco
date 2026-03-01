<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/BankClass.php");

$BankAccObj = new BankClass();
$BankAccObj->viewAccounts(" WHERE BB.BB_Status=1 AND BA.BB_Id=BB.BB_Id AND BB.BNK_Id=BNK.BNK_Id AND BA.OF_Id=".$preTally_user_ofid." ORDER BY BA.BA_No");
$BranchAcc_Obj = $BankAccObj->BankAccount;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>';
    if($BranchAcc_Obj) {
				$j = 1;
				foreach($BranchAcc_Obj as $rw) {					
					echo '<row id="'.$rw->BA_Id.'">                                                
						<userdata name="BA_No">'.$rw->BA_No.'</userdata>
                                                <userdata name="BA_DispName">'.$rw->BA_DispName.'</userdata>
                                                <userdata name="LC_Id">'.$rw->LC_Id.'</userdata>    
                                                <userdata name="BB_Id">'.$rw->BB_Id.'</userdata>                                                    
                                                <userdata name="BA_Status">'.$rw->BA_Status.'</userdata>    
						<cell>'.$j.'</cell>
						<cell name="Branch_Name">'.$rw->BA_No.'</cell>
                                                <cell name="Bank_DispName">'.$rw->BA_DispName.'</cell>
                                                <cell name="Bank_Name">'.$rw->BNK_Name.','.$rw->BB_Name.'</cell>';   
                                                if($rw->BA_Status == '0') { 
                                                    $BkStatus="Blocked";
                                                }else{
                                                     $BkStatus="Approved";
                                                }
                                                echo'<cell name="BkStatus">'.$BkStatus.'</cell>';
						if($rw->BA_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Blocked\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';                                              
                                                } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';                                              
                                                }
                                                
                                        echo '</row>';	
                                        $j++;
				}
			}else{
                            echo '<row id="0"> 
                            <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                         }
		  
echo '</rows>';
?>