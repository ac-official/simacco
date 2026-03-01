<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

$w = $REQUEST['w'];

$w = floor(($w-360)/2);

echo '<items>
        <item type="settings" position="label-left" labelWidth="0" offsetLeft="0" offsetTop="0"/>
            <item type="hidden" name="IT_Name" value=""/>
            <item type="hidden" name="DS_Description" value=""/>
            <item type="hidden" name="TR_Track" value=""/>
            
            <item type="block" width="100%" className="newBalSheet">   
                <item type="combo" label="" name="MH_Type" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="150">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note>Document</note>
		</item> 
                <item type="newcolumn"/>
               
                <item type="combo" label="" name="MH_Type" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="150">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note>State</note>
		</item> 
                <item type="newcolumn"/>
                
                <item type="combo" label="" name="MH_Type" readonly="true" validate="NotEmpty" offsetTop="20" position="label-left" inputWidth="150">
			<option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" selected="false" />
			<note>University/Board/Council</note>
		</item> 
                <item type="newcolumn"/>
                
                <item type="input" name="BS_Amount" label="" value="" inputWidth="100"  validate="NotEmpty,^([0-9]*|\d*\.\d{1}?\d*)$"  offsetTop="20" position="label-left">
                        <note>Amount</note>
                </item>
                
                <item type="newcolumn"/>
                <item type="button" value="Add Process" offsetTop="15" position="label-left" name="trackAddDocProcess"/>
                
                <item type="newcolumn"/>
                <item type="button" value="SUBMIT" offsetTop="15" position="label-left" name="saveBalanceSheetItem"/>

            </item>
            
            <item type="block" width="100%" className="newBalSheet">
            


                <item type="input" value="" offsetTop="15" width="700" rows="2" position="label-left" name="trackTempSelectedProcess"/>


            </item>
	</items>';
?>