<?php
error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/HistoryClass.php");

$HI_Obj = new HistoryClass();

$BSId = $REQUEST['BSId'];

$BS_Items = array(
    
        'US_Id'         => array('title' => 'Added by', 'name' => 'BSUSName'),
        'IT_Id'         => array('title' => 'Item',     'name' => 'IT_Name'),
        'LC_Id'         => array('title' => 'Location', 'name' => 'LC_Name'),
        'BA_Id'         => array('title' => 'Bank Account', 'name' => 'BA_Id'),
        'CHQ_Number'    => array('title' => 'Cheque Number', 'name' => 'CHQ_Number'),
        'BS_PayersBank' => array('title' => 'Payers Bank', 'name' => 'BS_PayersBank'),
        'BS_PayersChQ'  => array('title' => 'Payers Cheque',     'name' => 'BS_PayersChQ'),
        'BS_VoucherNo'  => array('title' => 'Voucher Number',     'name' => 'BS_VoucherNo'),
        'BS_Amount'     => array('title' => 'Amount', 'name' => 'BS_Amount'),
        'BS_PruchasedBy'=> array('title' => 'Pruchased By : ', 'name' => 'BS_PruchasedBy'),
        'BS_Approved'   => array('title' => 'Approved / Given By  : ', 'name' => 'BS_Approved'),
        'BS_Description'=> array('title' => 'Description', 'name' => 'DS_Description'),
        'TR_Id'         => array('title' => 'Track',     'name' => 'TR_Id'),
        'PM_Id'         => array('title' => 'Payment Mode', 'name' => 'PM_Id'),
        'BS_PaidTo'     => array('title' => 'Paid to',     'name' => 'DS_Description'),
        'BS_PaidBy'     => array('title' => 'Paid by', 'name' => 'PaidUSName'),
//        'BS_PayTime'    => array('title' => 'Paid Date', 'name' => 'BS_PayTime'),
//        'BS_PayType'    => array('title' => 'Pay Type', 'name' => 'BS_PayType'),
        'BS_PaidDate 	'    => array('title' => 'Paid Date', 'name' => 'BS_PaidDate 	'),
        'BS_PrchsdFor'  => array('title' => 'Purchased for ', 'name' => 'PrchsdForLCName'),
        'BS_Purpose'    => array('title' => 'Purpose/Reason ', 'name' => 'BS_Purpose'),
        'BS_User'       => array('title' => 'Name of the User  ', 'name' => 'UserUSName'),
        'BS_StaffId'    => array('title' => 'Staff ID / User ID', 'name' => 'StaffId'),
        'BS_AprovlGvnBy'=> array('title' => 'Approval given by ', 'name' => 'AprlGvnUSName'),
        'BS_AprovlTknBy'=> array('title' => 'Approval taken by ', 'name' => 'AprlTknUSName'),
        'BS_AprovdDate' => array('title' => 'Approved date ', 'name' => 'BS_AprovdDate'),
//        'BS_PrchsdNo'   => array('title' => 'Purchase Order No. ', 'name' => 'BS_PrchsdNo'),
        'BS_Remarks'    => array('title' => 'Remarks', 'name' => 'BS_Remarks'),
        'BS_Date'       => array('title' => 'Date', 'name' => 'BS_Date'),
        'BS_RJReason'   => array('title' => 'Rejected reason', 'name' => 'BS_RJReason'),
        'BS_BRemarks'   => array('title' => 'Remarks', 'name' => 'BS_BRemarks'),
        'BS_Status'     => array('title' => 'Status', 'name' => 'BS_Status')
    
//        'CN_Id'         => array('title' => 'Added by', 'name' => 'CN_Id'),
//        'ST_Id'         => array('title' => 'Item',     'name' => 'ST_Id'),
//        'BNK_Id'        => array('title' => 'Bank',     'name' => 'BNK_Id'),
//        'BB_Id'         => array('title' => 'Bank Branch', 'name' => 'BB_Id'),
//        'BS_Narration'  => array('title' => 'Narration', 'name' => 'BS_Narration'),
//        'BS_Transaction' => array('title' => 'Added by', 'name' => 'BSUSName'),
//        'BS_Brand'      => array('title' => 'Brand', 'name' => 'BS_Brand'),
//        'BS_Model'      => array('title' => 'Model', 'name' => 'BS_Model'),
//        'UT_Id'         => array('title' => 'Unit  ', 'name' => 'UT_Id' ),
//        'BS_Quantity'   => array('title' => 'Quantity : ', 'name' => 'BS_Quantity'),    
//        'BS_Persons'    => array('title' => 'Persons  : ', 'name' => 'BS_Persons'),
//        'BS_DualEntry'     => array('title' => 'Dual Status', 'name' => 'BS_DualEntry'),
//        'BS_Complete'     => array('title' => 'Saved',     'name' => 'BS_Complete'),
    
    
    
    
     /*   'BS_CustId'     => array('title' => 'Cust Id  : ', 'type' => 'input'),
        'CN_Id'         => array('title' => 'Country  : ', 'type' => 'combo', 'filter' => 'connector : "requisites/countries.php", readonly : true, comboType: "image", comboImagePath: "images/flags/"'),
        'BS_Fee'        => array('title' => 'Official Fee  : ', 'type' => 'input'),
        'BS_Charges'    => array('title' => 'Other Charges  : ', 'type' => 'input'),
        'BS_GrossSal'   => array('title' => 'Gross Total  : ', 'type' => 'input'),
        'BS_Document'   => array('title' => 'Document  : ', 'type' => 'input'),
        'ST_Id'         => array('title' => 'State  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/getStates.php", filterCache: true'),
        'BS_UTY'        => array('title' => 'Name of UTY  : ', 'type' => 'input'),
        'BS_VehicleNo'  => array('title' => 'No Of Vehicle  : ', 'type' => 'input'),
        'BS_VehicleName'=> array('title' => 'Vehicle Name  : ', 'type' => 'input'),
        'BS_InsuranceNo'=> array('title' => 'Insurance No  : ', 'type' => 'input'),
        
        'BS_TravelMode' => array('title' => 'Travel Mode  : ', 'type' => 'input'),
        'BS_TravelDetail' => array('title' => 'Travel Detail  : ', 'type' => 'input'),
        'BS_TravelPurpose'=> array('title' => 'Purpose Of Travel  : ', 'type' => 'input'),
        'BS_Staff'      => array('title' => 'Staff Name  : ', 'type' => 'input'),
        'BS_EmpCode'    => array('title' => 'Employee Code  : ', 'type' => 'input'),
        'BS_PF'         => array('title' => 'PF  : ', 'type' => 'input'),
        'BS_ESI'        => array('title' => 'ESI  : ', 'type' => 'input'),
        'BS_ProTax'     => array('title' => 'Professional Tax  : ', 'type' => 'input'),
        'BS_StaffWPF'   => array('title' => 'Staff WF  : ', 'type' => 'input'),
        'BS_NetSal'     => array('title' => 'Net Salary  : ', 'type' => 'input'),
        'BS_PaidBy'     => array('title' => 'Paid By (Given By) : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true','block' => 'payment'),
        'BS_PayTime'    => array('title' => 'Time of Payment : ', 'type' => 'combo', 'filter' => 'connector : "requisites/modes.php?mode=time", filterCache: true', 'block' => 'payment'),
        'BS_PaidDate'   => array('title' => 'Date of Payment  : ',  'type' => 'calendar', 'dateFormat'=> '%Y-%m-%d %H:%i', 'readonly'=>'1', 'block' => 'payment'),
        'BNK_Id'        => array('title' => 'Bank Name : ', 'type' => 'combo', 'filter' => 'connector : "requisites/banks.php", readonly : true, filterCache: true', 'block' => 'payment'),
        'BA_Id'         => array('title' => 'Account Number : ', 'type' => 'combo', 'block' => 'payment'),
        'BS_PayType'    => array('title' => 'Type of Payment : ', 'type' => 'combo', 'filter' => 'connector : "requisites/paymentMode.php", filterCache: true', 'block' => 'payment'),
        'BB_Id'         => array('title' => 'Branch Name of bank : ', 'type' => 'combo', 'block' => 'payment'),
        'CHQ_Number'    => array('title' => 'Cheque Number : ', 'type' => 'input', 'block' => 'payment','hidden'=>'true'),
        'BS_Template'   => array('title' => '&nbsp;', 'type' => 'template', 'block' => 'payment'),
        */
        
    
//  'BS_VehicleNo'=>null,'BS_VehicleName'=>null,'BS_InsuranceNo'=>null,'BS_TravelMode'=>null,'BS_TravelDetail'=>null,'BS_TravelPurpose'=>null,   'BS_Bank'=>null,'BS_Branch'=>null,'BS_AccNo'=>null,'BS_UTY'=>null,'BS_Fee'=>null,'BS_Charges'=>null,'BS_CustId'=>null,
//  'BS_Document'=>null,'BS_Staff'=>null,'BS_EmpCode'=>null,'BS_GrossSal'=>null,'BS_PF'=>null,'BS_ESI'=>null,'BS_ProTax'=>null,'BS_StaffWPF'=>null,'BS_NetSal'=>null,'BS_TDS'=>null,'BS_BReff'=>null,
);
$BS_Status = array('Deleted','Published','Waiting in Bank Book','Rejected Bank Entry');
$BS_MHType = array('','Income','Expense');

//----------------------- Get Balance Sheet Entry Details ------------------------//

$HI_Obj->getBSEntryDetails($BSId);
$BSDetails = $HI_Obj->HistoryArray;
$BSDetCompare = $BSDetails;
array_splice($BSDetCompare,73);
//print_r($BSDetails);
//----------------------- Get Balance Sheet Back Up Entry Details ------------------------//

$HI_Obj->getBS_BkupEntryDetails($BSId);
$BSBkupDetails = $HI_Obj->HistoryArray;
$BSBkupDetArray = $BSBkupDetails;
//print_r($BSBkupDetails);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>';
echo '<item type="template" name="" label="ACCOUNTS ENTRY HISTORY" value="" className="historyClass" offsetTop = "20" offsetLeft = "20"></item>';
echo '<item type="template" label="" value="'.$BSDetails['IT_Name'].' - '.$BSDetails['DS_Description'].' - '.$BSDetails['TR_Track'].'" className="historyClass"  offsetLeft = "40"></item>';

$count = 0;
foreach ($BSBkupDetArray as $key=>$value) { 
    
    if($count == 0)  $NewItemArray = $BSDetails;
    else  $NewItemArray = $OldItemArray;
        
    $OldItemArray = $value ;
//    $BKUpArray = $value;
    array_splice($value, 0,1);
    array_splice($value, 73);
    $BKupDetArray = array_splice($BSBkupDetArray, 73,75);

    $CompareRes =  array_diff_assoc($BSDetCompare,$value);
    unset($CompareRes['BS_MDate']);
    $BSDetCompare = $value ;
   
    if (!empty($CompareRes)) actsEntryHistory($CompareRes, $NewItemArray , $OldItemArray, $BS_Items, $BS_Status, $BS_MHType);
}


function actsEntryHistory($CompareRes, $NewItemArray, $OldItemArray, $BS_Items, $BS_Status, $BS_MHType){
    
    $diff= abs(strtotime(date("Y-m-d H:i:s")) - strtotime($OldItemArray['BK_CDate']));
    $days = floor(($diff)/ (60*60*24));
    $Date = $days .' days';
    if($days > 31) {
        $months = floor(($diff ) / (30*60*60*24));
        $Date = $months. ' months';
    }
    if($months > 12){
        $years = floor($diff / (365*60*60*24));
        $Date = $years. ' years';
    }
    
    echo '<item type="settings" position="label-left" offsetLeft="30" width = "90%" />
                <item type="fieldset"  label="Updated By '.$OldItemArray['BKUSName'].' '.$Date.' ago."  offsetTop="10" >';
                    
                    foreach ($CompareRes as $key => $value) { 
                        
                        $oldValue = $OldItemArray[$BS_Items[$key]['name']] ? $OldItemArray[$BS_Items[$key]['name']] : '---';
                        $newValue = $NewItemArray[$BS_Items[$key]['name']] ? $NewItemArray[$BS_Items[$key]['name']] : '---';
                        
                        if($key == 'BS_Status'){
                            $oldValue = $BS_Status[$OldItemArray[$BS_Items[$key]['name']]];
                            $newValue = $BS_Status[$NewItemArray[$BS_Items[$key]['name']]];
                        }
                        
                        if($key == 'MH_Type'){
                            $oldValue = $BS_MHType[$OldItemArray[$BS_Items[$key]['name']]];
                            $newValue = $BS_MHType[$NewItemArray[$BS_Items[$key]['name']]];
                        }
                                             
                        if (array_key_exists($key,$BS_Items))
            		echo '<item type="template" name="'.$key.'" label="'.$BS_Items[$key]['title'].'" value="changed from '.$oldValue.' to '.$newValue.'" className = "titleClass"></item>';
                    }
                    
                echo '</item>';
}
echo '</items>';


?>