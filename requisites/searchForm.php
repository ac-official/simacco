<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="0" inputWidth="120" noteWidth="105" offsetLeft="5" offsetRight="2" />   
        
            <item type="fieldset" label="Filter By Date" width="268" className="searchBlock" >
                
                    <item type="calendar" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" calendarPosition="right" label="Date" name="Srch_FrmDate">
                        <note>From Date</note>
                    </item>
                    <item type="newcolumn" offsetLeft="5" />
                    <item type="calendar" serverDateFormat="%Y-%m-%d" dateFormat="%d.%m.%Y" calendarPosition="right" name="Srch_ToDate">
                        <note>To Date</note>
                    </item>
                
            </item>
            <item type="fieldset" label="Filter By Item" width="268" className="searchBlock" >
            
                <item type="combo" name="Srch_Item" label="" connector="requisites/srch_items.php">
                    <note>Item Name</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="Vch_Id" label="" >
                    <note>Voucher No</note>
                </item>
            </item>
            
            <item type="fieldset" label="Filter By Paid To / From" width="268" className="searchBlock" >
            
                <item type="input" name="Srch_Paidto" label="">
                    <note>Paid To</note>
                </item>
                <item type="newcolumn"/>
                <item type="input" name="Srch_Paidfrm" label="" >
                    <note>Paid From</note>
                </item>
            </item>
            

            <item type="fieldset" id="fs3" label="Filter By Company / User" width="268" className="searchBlock" >
            
                <item type="combo" name="OF_Id" label="" connector="requisites/offices.php">
                    <note>Company Name</note>
                </item>
                <item type="combo" name="DP_Id" label="" >
                    <note>Department Name</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="LC_Id" label="" >
                    <note>Branch Name</note>
                </item>                
                <item type="combo" name="US_Id" label="" >
                    <note>User Name</note>
                </item>

            </item>
            

            <item type="fieldset" id="fs3" label="Filter By Bank / Account" width="268" className="searchBlock" >
            
                <item type="combo" name="BNK_Id" label="" connector="requisites/banks.php">
                    <note>Bank Name</note>
                </item>
                <item type="combo" name="BA_Id" label="" >
                    <note>Account No:</note>
                </item>
                
                <item type="newcolumn"/>
                <item type="combo" name="BB_Id" label="" >
                    <note>Branch Name</note>
                </item>                
                
                <item type="combo" name="CHQ_Id" label=""  >
                    <note>Cheque No:</note>
                </item>

            </item>

            <item type="block" width="250" >
                <item type="button" value="Filter" name="Btn_Filter" inputWidth="100"></item>
                <item type="newcolumn"></item>
                <item type="button" value="Clear" name="Btn_Clr" inputWidth="100"></item> 	
            </item>
	</items>';
?>