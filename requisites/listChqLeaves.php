<?php 
include_once($BASEPATH . "preTallyClass/BankClass.php");
$ChequeObj = new BankClass();
$ChequeObj->viewChqLeafs("BCL.CHQ_Id = ".$REQUEST['book_id']);
$Cheque_Obj = $ChequeObj->Cheque_leaves;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
if($REQUEST['flag'] == 0){
echo '<head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="na">Cheque Leaf Number</column>
        <column width="150" type="ro" align="left" sort="na">Item</column>
        <column width="0" type="ro" align="left" sort="na"></column>
        <column width="130" type="ro" align="left" sort="na">Paid To</column>
        <column width="120" type="ro" align="left" sort="na">Amount</column>
        <column width="120" type="combo" align="left" sort="na">Status</column>
        <column width="0" type="combo" align="left" sort="na" id="CkStatus">Status</column>
        <column width="45" type="ro" align="center" sort="na"></column>
        <settings>
                <colwidth>px</colwidth>
        </settings>
      
    </head>';
}
    if($Cheque_Obj) {
        $j = 1;
        $chq_status = array(1=>'New',2=>"Submitted",3=>"Approved",4=>"Rejected",5=>"Cancelled");
        foreach($Cheque_Obj as $rw) {
           
         
            if($rw->CL_Status != 1 &&  $rw->IT_Name!=''){
                $title="Click here to view details";
            }else{
                $title=" ";
            }
            //CL_Id 	CL_Leaf 	CHQ_Id 	CL_Status 1.New 2.Used 3.Success 4.Cancelled	CL_Issued
            echo '<row id="'.$rw->CL_Id.'">	
            <userdata name="CL_Status">'.$rw->CL_Status.'</userdata>
            <cell title=" ">'.$j.'</cell>						
            <cell name="CL_Leaf" title=" ">'.$rw->CL_Leaf.'</cell>  
            <cell name="IT_Id" title=" ">';
            if($rw->CL_Status != 1)
                echo $rw->IT_Name;
            echo '</cell>';
            echo '<cell name="popUp"><![CDATA[<a style="text-decoration:none;" class="ChqPopUp_'.$rw->CL_Id.'" onclick="preTally.Settings.chqDetailsPopUp(this,'.$rw->CL_Id.');"></a>]]></cell>
            <cell name="BS_PaidTo" title=" ">';
            if($rw->CL_Status != 1)
                echo $rw->BS_PaidTo;
            echo '</cell> ';
            if($rw->CL_Status != 1)
                $amount = $rw->BS_Amount;
            else
                $amount = '';
            echo '<cell title=" " name="Account_ID">'.$amount.'</cell>';
            echo '<cell title="Click here to edit status" name="CL_Status">'.$chq_status[$rw->CL_Status].'</cell>';            
            echo '<cell title="Click here to edit status" name="ssStatus">'.$chq_status[$rw->CL_Status].'</cell>';	
            echo '<cell name="View" title="'.$title.'"><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;"  onclick=""/>]]></cell></row>';	
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