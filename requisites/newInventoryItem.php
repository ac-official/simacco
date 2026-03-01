<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="150" noteWidth="125" offsetLeft="20"/> 
        <item type="block" width="650" >
                <item type="hidden" name="INV_ItemID" value="0"/>
                <item type="input" name="INV_ItemName" label="Inventory Brand" value="" offsetTop="" required="true" >
                    <note width="150">Inventory Item</note>		
                </item> 
                <item type="newcolumn"/>
                <item type="input" name="INV_ItemModel" label="Inventory Model" value="" required="true" >
                    <note width="150">Inventory Item</note>		
                </item>    
                
        </item>
        <item type="block" width="650" >
                <item type="combo" name="INV_TypeID" label="Type" required="true"  >
			<note width="150">Inventory Type</note>
		</item>                
                <item type="newcolumn"/>
                 <item type="input" name="INV_ItemQty" label="Quantity" required="true">
			<note width="150">Inventory Item Quantity</note>
		</item>

        </item>
        
        <item type="block" width="650" >
                <item type="combo" name="LC_Id" label="Branch" value="" connector="" required="true">
                    <note width="150">Assigned Branch</note>
		</item>                
                <item type="newcolumn"/>
                <item type="combo" name="INV_ItemAssignee" label="Owner/Assignee" value="" required="true">
			<note width="150">Owner/Assignee of item</note>
		</item>
                
        </item>
        
        
        

         <item type="block" width="650" >
                <item type="combo" label="Status" name="INV_ItemStatus" readonly="true" required="true">
			<option value="1" label="Active" selected="true" />
			<option value="2" label="Disposed" selected="false" />
                        <option value="3" label="In Stock" selected="false" />
			<option value="4" label="Missing" selected="false" />
                        <option value="5" label="Repair" selected="false" />
			<option value="6" label="Stolen" selected="false" />
			<note width="150">Publish Status</note>
		</item>
                
                <item type="newcolumn"/>
                <item type="calendar" label="Purchased Date" name="INV_PurchaseDate" readonly="true" required="true"></item>
        </item>
        

         <item type="block" width="650" >
               <item type="input" name="INV_ItemRemarks" label="Remarks" rows="5" inputWidth="450">
			<note width="150">Remarks</note>
		</item>
        </item>        
		<item type="block" width="650" >
			<item type="button" value="Save" name="newInvItemSave"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newInvItemCancel"/>
		</item>
		
	</items>';
?>