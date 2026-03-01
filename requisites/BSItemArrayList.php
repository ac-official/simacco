<?php
$BS_Items = array(  

        'BS_VoucherNo'  => array('title' => 'Voucher No : ', 'type' => 'input', 'block' => 'payment'),
        'BS_Brand'      => array('title' => 'Brand : ', 'type' => 'input', 'block' => 'item'),
        'BS_Model'      => array('title' => 'Model : ', 'type' => 'input', 'block' => 'item'),
        'BS_Price'      => array('title' => 'Unit Price : ', 'type' => 'input', 'block' => 'item'),
        'BS_Quantity'   => array('title' => 'Quantity : ', 'type' => 'input', 'block' => 'item'),
        'BS_Amount'     => array('title' => 'Amount : ', 'type' => 'input', 'block' => 'item'),
        'BS_Remarks'    => array('title' => 'Remarks : ', 'type' => 'input', 'block' => 'general'),
//        'BS_Bank'       => array('title' => 'Bank : ', 'type' => 'input', 'block' => 'payment'),
//        'BS_Branch'     => array('title' => 'Branch : ', 'type' => 'input', 'block' => 'payment'),
//        'BS_AccNo'      => array('title' => 'Account No : ', 'type' => 'input', 'block' => 'payment'),
    
        'UT_Id'         => array('title' => 'Unit : ', 'type' => 'combo', 'block' => 'item', 'filter' => 'connector : "requisites/units.php", readonly : true'),
        'PM_Id'         => array('title' => 'Mode Of Payment : ','type' => 'combo', 'filter' => 'connector : "requisites/modes.php&mode=mode", filterCache: true', 'block' => 'payment'),
    
        'LC_Id'         => array('title' => 'Branch Name  : ', 'type' => 'combo', 'block' => 'payment', 'filter' => 'serverFiltering : "requisites/locations.php", filterCache: true'),
        //'BS_PruchasedBy'=> array('title' => 'Person / Staff  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true'),
        //'BS_Approved'   => array('title' => 'Approved / Give  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true'),
        
        'BS_PaidTo'     => array('title' => 'Paid To  : ', 'type' => 'input', 'block' => 'payment'),
        'BS_TDS'        => array('title' => 'Tax--Tds  : ', 'type' => 'input'),
    
        'BS_CustId'     => array('title' => 'Cust Id  : ', 'type' => 'input'),
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

        'BS_Persons'    => array('title' => 'Persons  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true'),
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
    
        'BS_PrchsdFor'  => array('title' => 'Purchased for  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/getBranches.php", filterCache: true', 'block' =>'user'),
        'BS_Purpose'    => array('title' => 'Purpose/Reason  : ', 'type' => 'input', 'block' =>'user'),
        'BS_User'       => array('title' => 'Name of the User  : ', 'type' => 'combo',  'block' =>'user'),
        'BS_StaffId'    => array('title' => 'Staff ID / User ID  : ', 'type' => 'combo', 'block' =>'user'),
 
        'BS_AprovlGvnBy'=> array('title' => 'Approval given by  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true', 'block' =>'approvals'),
        'BS_AprovlTknBy'=> array('title' => 'Approval taken by  : ', 'type' => 'combo', 'filter' => 'serverFiltering : "requisites/persons.php", filterCache: true', 'block' =>'approvals'),
        'BS_AprovdDate' => array('title' => 'Approved date  : ', 'type' => 'calendar', 'dateFormat'=> '%Y-%m-%d %H:%i', 'readonly'=>'1', 'enableTime'=> 'true', 'block' =>'approvals'),
        'BS_PrchsdNo'   => array('title' => 'Purchase Order No.  : ', 'type' => 'input', 'block' =>'approvals')
);
$updateFields = array(

    1 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks' ),
    2 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks' ),

    
    3 => array( 'BS_Brand', 'BS_Model', 'BS_Quantity', 'UT_Id', 'BS_Amount', 'BS_Price', 
                'BS_Template', 'BS_VoucherNo', 'BS_PaidBy', 'BS_PaidTo', 'BS_PayTime', 'PM_Id', 'BS_PaidDate', 'BS_PayType', 'BNK_Id', 'BB_Id', 'BA_Id', 'CHQ_Number', 
                'BS_PrchsdFor', 'BS_Purpose', 'BS_User' , 'BS_StaffId',
                'BS_AprovlGvnBy','BS_AprovlTknBy','BS_AprovdDate','BS_PrchsdNo'
        ),
    
    
    
    4 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
    5 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
    6 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
    7 => array( 'BS_PaidTo', 'BS_VoucherNo', 'BS_Amount', 'BS_TDS', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo'),          
    8 => array( 'BS_CustId', 'BS_VoucherNo', 'CN_Id', 'BS_Fee', 'BS_Charges', 'BS_Amount', 'BS_Remarks' ),
    9 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
   10 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
   11 => array( 'BS_PaidTo', 'BS_VoucherNo', 'BS_GrossSal', 'BS_TDS', 'BS_Amount', 'BS_Remarks','PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_PruchasedBy','BS_Approved'),
   13 => array( 'BS_VoucherNo', 'BS_PaidTo', 'BS_Document', 'BS_Price' , 'BS_Amount', 'BS_Remarks'),
   14 => array( 'BS_CustId', 'BS_VoucherNo', 'ST_Id', 'BS_Fee', 'BS_Charges', 'BS_Amount', 'BS_Remarks' ),
   15 => array( 'BS_CustId', 'BS_VoucherNo', 'ST_Id', 'BS_UTY', 'BS_Fee', 'BS_Charges', 'BS_Amount', 'BS_Remarks' ),
   16 => array( 'BS_PaidDate', 'BS_Quantity', 'BS_VoucherNo', 'BS_Price' , 'UT_Id','BS_VehicleNo', 'BS_Amount', 'BS_Remarks' ),
   17 => array( 'BS_VehicleNo', 'BS_VoucherNo', 'BS_VehicleName', 'BS_InsuranceNo', 'BS_PaidTo', 'BS_Amount', 'BS_Remarks'),
   18 => array( 'BS_Persons', 'BS_VoucherNo', 'BS_TravelMode', 'BS_TravelDetail', 'BS_TravelPurpose', 'BS_Amount', 'BS_Remarks'),
   19 => array( 'BS_VoucherNo', 'BS_Persons', 'BS_Amount','BS_Remarks'), 
   20 => array( 'BS_Staff', 'BS_EmpCode', 'BS_GrossSal', 'BS_PF', 'BS_ESI', 'BS_ProTax', 'BS_TDS', 'BS_StaffWPF',  'BS_Amount', 'BS_Remarks'),
   21 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks' ),
   22 => array( 'BS_CustId', 'BS_VoucherNo', 'ST_Id', 'BS_Fee', 'BS_Charges', 'BS_Amount', 'BS_Remarks' ),
   23 => array( 'BS_PaidDate', 'BS_Quantity', 'BS_VoucherNo', 'BS_Price' , 'UT_Id','BS_VehicleNo', 'BS_Amount', 'BS_Remarks' ),
   24 => array( 'BS_VehicleNo', 'BS_VoucherNo', 'BS_VehicleName', 'BS_InsuranceNo', 'BS_PaidTo', 'BS_Amount', 'BS_Remarks'),
   25 => array( 'BS_PaidTo', 'BS_VoucherNo', 'BS_Amount', 'BS_TDS', 'PM_Id', 'BS_Bank', 'BS_Branch','BS_AccNo'),
   26 => array( 'BS_CustId', 'BS_VoucherNo', 'CN_Id','BS_Fee','BS_Charges', 'BS_Amount', 'BS_Remarks'), 
   27 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch',  'BS_AccNo', 'BS_Remarks'),
   28 => array( 'BS_VoucherNo', 'BS_PaidTo', 'BS_Document', 'BS_Price' , 'BS_Amount', 'BS_Remarks'),
   29 => array( 'BS_VoucherNo', 'BS_PaidTo', 'BS_Document', 'BS_Price' , 'BS_Amount', 'BS_Remarks'),
   30 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks'),
   31 => array( 'BS_VoucherNo', 'UT_Id', 'BS_Price', 'BS_Quantity', 'BS_Amount', 'PM_Id', 'BS_Bank', 'BS_Branch', 'BS_AccNo', 'BS_Remarks')
);
//$curField = $updateFields[$SHID];
?>