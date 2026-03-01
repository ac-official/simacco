<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$w = $REQUEST['w'];
$y= floor(($w-288)/6);
$w = floor(($w-550)/2);
//$y = floor($w/3);

echo '<items>
        <item type="settings" position="label-left" labelWidth="0" offsetLeft="0" offsetTop="0"/>
            <item type="hidden" name="IT_Name" value=""/>
            <item type="hidden" name="DS_Description" value=""/>
            <item type="hidden" name="TR_Track" value=""/>
            <item type="block" className="newBalSheet_main">   
            <item type="block" className="newBalSheet_2">   
                <item type="combo" label="" name="MH_Type" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="80">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note>ENTRY TYPE</note>
		</item> 
                <item type="newcolumn"/>
               
                <item type="combo" name="IT_Id" className="itemcombo" label="" value="" required="true" offsetTop="20" position="label-left" inputWidth="'.$w.'" >
                <note>NAME OF THE EXPENSE</note>
                </item>
                <item type="newcolumn"/>
                
                <item type="combo" name="BS_Description" className="desccombo" label="" value=""  validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="'.$w.'" >
                        <note>PAID TO [ NAME AND DETAILS PARTIES ]</note>
                </item>                
                <item type="newcolumn"/>
                
                <item type="input" name="BS_Amount" label="" value="" inputWidth="80"  validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$"  offsetTop="20" position="label-left">
                        <note>AMOUNT</note>
                </item>
                <item type="newcolumn"/>
                
                <item type="input" name="BS_Invoice" label="" value=""  offsetTop="20" position="label-left" inputWidth="105"  validate="NotEmpty">
                        <note>INVOICE NUMBER</note>
                </item>
                <item type="newcolumn"/>
                <item type="combo" name="BS_Track" className="trackCombo" label="" value=""  offsetTop="20" position="label-left" inputWidth="80" validate="^[a-zA-Z0-9 _\-]+$">
                        <note>TRACK ID</note>
                </item>     
            </item>
            <item type="block"  className="newLocDetail" offsetLeft="85" style="display:none;padding-right:2px;">                 
                <item type="input" name="GOGL_PlaceSearch" placeholder="" label="" value=""  offsetTop="6" position="label-left" inputWidth="'.$y.'"  validate="NotEmpty">
                        <note>PLACE</note>
                </item>
                <item type="newcolumn"/>
                 <item type="input" name="GOGL_Place" label="" value=""  validate="NotEmpty"  offsetTop="6" position="label-left" inputWidth="'.$y.'">
                        <note>LOCATION</note>
                </item>
                <item type="newcolumn"/> 
                
                <item type="input" name="GOGL_City" label="" value=""  validate="NotEmpty"  offsetTop="6" position="label-left" inputWidth="'.$y.'"> 
                        <note>CITY</note>
                </item>
                
                <item type="newcolumn"/>
                <item type="input" name="GOGL_State" label="" value=""  validate="NotEmpty"  offsetTop="6" position="label-left" inputWidth="'.$y.'">
                        <note>STATE/PROVINCE</note>
                </item>
                <item type="newcolumn"/>
               <item type="input" name="GOGL_Country" label="" value=""  validate="NotEmpty"  offsetTop="6" position="label-left" inputWidth="'.$y.'" >
                        <note>COUNTRY</note>
                </item>   
                <item type="newcolumn"/>
                <item type="input" name="BS_GST" label="" value=""  validate="NotEmpty"  offsetTop="6" position="label-left" inputWidth="'.$y.'">
                        <note>GST/VAT NO: OF SHOP/PAYEE</note>
                </item>   
                
              </item>
              </item>
              <item type="newcolumn"/>
              <item type="button" value="SUBMIT" position="label-left" offsetTop="15" name="saveBalanceSheetItem" className="save1"/>
              <item type="button" value="SUBMIT" position="label-left" offsetTop="60" name="saveBalanceSheetItem" className="save2"/>                 
                
	</items>';
?>
