<?php 
include_once($BASEPATH . "preTallyClass/BankClass.php");
$ChequeObj = new BankClass();
$ChequeObj->viewCheque("WHERE BA.BA_Status=1 AND BB.BB_Status=1 AND BA.BB_Id=BB.BB_Id AND BB.BNK_Id=BNK.BNK_Id AND CQ.BA_Id=BA.BA_Id AND CQ.OF_Id=".$preTally_user_ofid);
$Cheque_Obj = $ChequeObj->Cheque;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
$stats=array("Blocked","Approved");
    if($Cheque_Obj) {
        $j = 1;
        foreach($Cheque_Obj as $rw) {
            echo '<row id="'.$rw->CHQ_Id.'">
                <cell title=" ">'.$j.'</cell>
                <cell title=" " name="Cheque_book_no">'.$rw->CHQ_BookNo.'</cell>
                <cell title=" " name="Cheque_first_leaf">'.$rw->CHQ_Firstleaf.'</cell>
                <cell title=" " name="Account_ID">'.$rw->BA_No.'</cell>
                <cell title=" " name="Branch">'.$rw->BNK_Name.'-'.$rw->BB_Name.'</cell>    
                <cell>'.$stats[$rw->CHQ_Status].'</cell>';                                                                                                
                if($rw->CHQ_Status == '0') { 
                    echo '<cell title="Block"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                } else {
                    echo '<cell title="Approve"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                }
            echo '</row>';	
            $j++;
        }
    }
    else {
        echo '<row id="0"> 
        <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
    }
echo '</rows>';
?>