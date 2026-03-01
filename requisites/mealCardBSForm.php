<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$months=array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June",
    "07"=>"July","08"=>"August","09"=>"Spetember","10"=>"October","11"=>"November","12"=>"December");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
        <item type="settings" position="label-left" labelWidth="30" offsetLeft="10" offsetTop="0" width="1000"/>
            
            
            <item type="block" width="100%" className="">                   
               <item type = "hidden" name="SM_Id"></item>
                <item type="input" name="ML_Amount" className="" label="" value="" required="true" offsetTop="20" validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft ="30"  position="label-left" inputWidth="150" readonly="true">
                <note>Total Meal Allowance Amount</note>
                </item>                
                <item type="newcolumn"/>
                <item type="input" label="" name="ML_Month" readonly="true" validate="NotEmpty" offsetTop="20" offsetLeft ="30" position="label-left" inputWidth="250" value="'.$months[$REQUEST['month']].'-'.$REQUEST['year'].'">
                    <note>Paid For Month</note>
		</item>
                
                <item type="newcolumn"/>
                <item type="button" value="SUBMIT" offsetTop="15" offsetLeft="30" position="label-left" name="saveMealAllowanceBSEntry"/>                                
            </item>
                    <item type="fieldset" label="Payment Details" inputWidth="auto" >
                    <item type="settings" inputWidth="150"/>
                    <item type="block" inputWidth="auto">
                    <item type="input" label="Voucher No" name="BS_VoucherNo" required="true" labelWidth="100"></item><item type="newcolumn"/>
                    <item type="combo" label="Branch Name" name="LC_Id" required="true" labelWidth="100" serverFiltering="requisites/locations.php"></item><item type="newcolumn"/>                    
                    <item type="combo" label="Paid By (Given By)" required="true" name="BS_PaidBy" labelWidth="100" serverFiltering="requisites/persons.php"></item><item type="newcolumn"/>
                    <item type="calendar" label="Date of Payment"  required="true" name="BS_PaidDate" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" labelWidth="100"></item>
                    </item>
                    <item type="block" inputWidth="auto">                    
                    <item type="combo" label="Payment Mode" name="PM_Id" labelWidth="100" connector="requisites/modes.php&amp;mode=mode"></item><item type="newcolumn"/>     
                    <item type="combo" label="Type Of Payment" name="BS_PayType" labelWidth="100"  connector="requisites/paymentMode.php"></item><item type="newcolumn"/>                    
                    <item type="input" label="DD/NEFT/Credit" name="BS_Transaction" labelWidth="100"></item><item type="newcolumn"/>                    
                    <item type="combo" label="Time of Payment" name="BS_PayTime" labelWidth="100"  connector="requisites/modes.php&amp;mode=time"></item><item type="newcolumn"/>
                    
                    </item>
                    <item type="block" inputWidth="auto">                                                            
                    <item type="combo" label="Bank Name" name="BNK_Id" labelWidth="100" connector="requisites/banks.php"></item><item type="newcolumn"/>
                    <item type="combo" label="Branch Name" name="BB_Id" labelWidth="100" ></item><item type="newcolumn"/>
                    <item type="combo" label="Account Number" name="BA_Id" labelWidth="100"></item><item type="newcolumn"/>
                    <item type="combo" label="Cheque Number" name="CHQ_Number" labelWidth="100"></item>                    
                    </item>                    
            </item>
	</items>';
?>