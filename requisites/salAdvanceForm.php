<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
        <item type="settings" position="label-left" labelWidth="30" offsetLeft="10" offsetTop="0" width="1100"/>
                    <item type="fieldset" label="Payment Details" inputWidth="auto" >
                    <item type="block" width="100%" className="">   
                <item type="combo" label="" name="US_Id" serverFiltering="requisites/userDetails.php" filterCashe="true" required="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="255">
			<note>Staff Name</note>
		</item> 
                <item type="newcolumn"/>
               <item type="hidden" name="UserName" value="'.$preTally_user_name.'"></item>
               <item type="hidden" name="UserId" value="'.$preTally_user_id.'"></item>
               <item type="hidden" name="LocId" value="'.$preTally_user_lcid.'"></item>    
                <item type="input" name="SA_Amount" className="" label="" value="" required="true" offsetTop="20" validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$" offsetLeft ="30"  position="label-left" inputWidth="130" >
                <note>Advance Amount</note>
                </item>
                <item type="newcolumn"/>
                
                <item type="combo" name="SA_PaymentDuration" className="" label="" value=" " readonly="true" required="true" validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$" offsetTop="20" offsetLeft ="30"  position="label-left" inputWidth="130" >';
                for($j=1;$j<=25;$j++) {
			echo ' <option value="'.$j.'" label="'.$j.'" />';
                }
                echo '<note>Repayment Duration In Month</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" label="" name="BS_PaymentStartMonth" connector="requisites/getNextMonth.php" readonly="true" validate="NotEmpty" offsetTop="20" offsetLeft ="30" position="label-left" inputWidth="130">
                    <note>Payment Start From Salary Month</note>
		</item>
                
                <item type="newcolumn"/>
                
            </item>
                    <item type="settings" inputWidth="125"/>
                    <item type="block" inputWidth="auto">
                    <item type="input" label="Voucher No" name="BS_VoucherNo" required="true" labelWidth="100"></item><item type="newcolumn"/>
                    <item type="combo" label="Branch Name" name="LC_Id" required="true" labelWidth="100" connector="requisites/locations.php&amp;filter=BMR"></item><item type="newcolumn"/>                    
                    <item type="combo" label="Paid By (Given By)" required="true" name="BS_PaidBy" labelWidth="150" connector="requisites/persons.php&amp;mask=Self"></item><item type="newcolumn"/>                    
                    </item>
                    <item type="block" inputWidth="auto">                    
                    <item type="calendar" label="Date of Payment"  required="true" name="BS_PaidDate" serverDateFormat="%Y-%m-%d" dateFormat="%d/%m/%Y" labelWidth="100" value="'.date("Y-m-d").'"></item><item type="newcolumn"/>   
                    <item type="combo" label="Payment Mode" name="PM_Id" labelWidth="100" connector="requisites/modes.php&amp;mode=mode"></item><item type="newcolumn"/>                                             
                    <item type="combo" label="Time of Payment" name="BS_PayTime" labelWidth="150"  connector="requisites/modes.php&amp;mode=time"></item>
                    
                    </item>
                    <item type="block" inputWidth="auto">                        
                    <item type="combo" label="Type Of Payment" name="BS_PayType" labelWidth="100"  connector="requisites/paymentMode.php"></item><item type="newcolumn"/>             
                    <item type="combo" label="Bank Name" name="BNK_Id" labelWidth="100" connector="requisites/banks.php"></item><item type="newcolumn"/>
                    <item type="combo" label="Branch Name" name="BB_Id" labelWidth="150" ></item>
                    </item>
                    <item type="block" inputWidth="auto">    
                    <item type="combo" label="Account Number" name="BA_Id" labelWidth="100"></item><item type="newcolumn"/><item type="newcolumn"/>
                    <item type="combo" label="Cheque Number" name="CHQ_Number" labelWidth="100"></item><item type="newcolumn"/>                    
                    <item type="input" label="DD/NEFT/Credit" name="BS_Transaction" labelWidth="100"></item>  
                    </item>
                    <item type="button" value="SUBMIT" offsetTop="15" offsetLeft="30" position="label-left" name="saveSalAdvanceDetails"/>                                
            </item>
	</items>';
?>