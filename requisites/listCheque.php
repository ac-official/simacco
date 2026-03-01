<?php 
include_once($BASEPATH . "preTallyClass/BankClass.php");
$ChequeObj = new BankClass();
$ChequeObj->viewCheque("WHERE BA.BA_Status=1 AND BB.BB_Status=1 AND BA.BB_Id=BB.BB_Id AND BB.BNK_Id=BNK.BNK_Id AND CQ.BA_Id=BA.BA_Id AND CQ.OF_Id=".$preTally_user_ofid);
$Cheque_Obj = $ChequeObj->Cheque;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$stats=array("Blocked","Approved");
echo '<rows>';
		  if($Cheque_Obj) {
				$j = 1;
				foreach($Cheque_Obj as $rw) {
					
					echo '<row id="'.$rw->CHQ_Id.'">
						<userdata name="CHQ_BookNo">'.$rw->CHQ_BookNo.'</userdata>
                                                <userdata name="BA_Id">'.$rw->BA_Id.'</userdata>
                                                <userdata name="BA_No">'.$rw->BA_No.'</userdata>    
                                                <userdata name="BNK_Id">'.$rw->BNK_Id.'</userdata>
                                                <userdata name="BNK_Name">'.$rw->BNK_Name.'</userdata>
                                                <userdata name="BB_Id">'.$rw->BB_Id.'</userdata>
                                                <userdata name="BB_Name">'.$rw->BB_Name.'</userdata>                                                
                                                <userdata name="CHQ_Firstleaf">'.$rw->CHQ_Firstleaf.'</userdata>
                                                <userdata name="CHQ_Lastleaf">'.$rw->CHQ_Lastleaf.'</userdata>
                                                <userdata name="CHQ_Nextleaf">'.$rw->CHQ_Nextleaf.'</userdata>    
                                                <userdata name="CHQ_Status">'.$rw->CHQ_Status.'</userdata>                                               
						<cell>'.$j.'</cell>
						<cell name="Cheque_book_no">'.$rw->CHQ_BookNo.'</cell>
                                                <cell name="Account_ID">'.$rw->BA_No.'</cell>
                                                <cell name="Branch">'.$rw->BNK_Name.'-'.$rw->BB_Name.'</cell>
                                                <cell>'.$stats[$rw->CHQ_Status].'</cell>';                                                                                                
						if($rw->CHQ_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Blocked\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                }
                                        echo '</row>';	
                                        $j++;
				}
			}
		  
echo '</rows>';
?>