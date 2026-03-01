<?php
include_once($BASEPATH . "preTallyClass/BankClass.php");
$ChequeObj = new BankClass();
$Cheque_Obj= $ChequeObj->viewChequeDetails(" BCL.CL_Id = ".$REQUEST['CL_Id']);

$formData = '[{type: "settings",position: "label-left", labelWidth: "130", inputWidth: "auto", offsetLeft:"6"},';   
$formData .= '{type: "fieldset", name:"fieldsetname", class: "mydata",  label: "<img src=\'images/icon/close_button_icon.png\' style=\'cursor:pointer;\' onclick=\'preTally.Settings.hideChqDetailData()\' />", width:"auto", list:[';
$i = 1;

$label['BS_Amount']         = 'Amount';
$label['BNK_Id']            = 'Bank Name';
$label['BB_Id']             = 'Branch Name';
if($Cheque_Obj['MH_Type'] == 1) {   // income
    $label['BS_PaidTo']     = 'Paid To';
    $label['BS_PaidBy']     = 'Paid By';
}
else if($Cheque_Obj['MH_Type'] == 2) {  // expense
    $label['BS_PaidTo']     = 'Recieved From';
    $label['BS_PaidBy']     = 'Recieved By';
}
$label['US_Id']             = 'Created By';
$label['BS_Date']           = 'Created On';
$label['BS_PaidDate']       = 'Paid Date';
$label['BS_Remarks']        = 'Remarks';
$label['IT_Name']           = 'Item';
$label['LC_Id']             = 'Location';
$label['BS_VoucherNo']      = 'Voucher No:';
$label['BS_PayTime']        = 'Pay Time';
$label['BS_Purpose']        = 'Purpose';
$label['BS_AprovlGvnBy']    = 'Approval Given By';
$label['BS_AprovlTknBy']    = 'Approval Taken By';
$label['BS_AprovdDate']     = 'Approved On';
$fieldCount = ceil((count($Cheque_Obj)+2)/2);
if($Cheque_Obj) {
    foreach ($Cheque_Obj as $key => $value) {
        if($fieldCount == $i) 
            $formData .= '{ type:"newcolumn"},';     
        if($key != 'MH_Type' && $key != 'US_Id' && $key != 'BS_PaidBy' && $key != 'BS_AprovlGvnBy' && $key != 'BS_AprovlTknBy' && $key != 'LC_Id' && $key != 'BNK_Id' && $key != 'BB_Id') {
            if($key == 'BS_PayTime') {
                if( $value == 1 ){
                    $value = "Pay Now - No Credit";
                }elseif ( $value == 2 ) {
                    $value = "Pay Later - Credit";
                }
            }
            $formData .= ' { type : "template", name : "'.$key.'", label : "'.$label[$key].'", value : " <b>'.$value.'</b>" },';
        }
        else if($key != 'MH_Type') {
            $result = $ChequeObj->getChequeDetails($key, $value);
            $formData .= ' { type : "template", name : "'.$key.'", label : "'.$label[$key].'", value : " <b>'.$result.'</b>" },';
        }
        $i++;
    } 
}
$formData .= ']}]';
echo $formData;