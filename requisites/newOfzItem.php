<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

//include_once("../preTallyClass/OfficeClass.php");
//$OffObj = new OfficeClass();
//$offAdm = $OffObj->offzAdmin($preTally_user_ofid);

//echo $offAdm.'--'.$preTally_user_id;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

//<option value="0" label="Select Mainhead Type" selected="true" />
echo '<items>
	<item type="settings" position="label-left" labelWidth="120" inputWidth="400" noteWidth="180" offsetLeft="20"/>
		
		<item type="hidden" name="IT_Id" value="0"/>
                <item type="hidden" name="IT_SpId" value="0"/>
                
                <item type="input" name="IT_Name" label="Item Name" value="" offsetTop="30" required="true" >
                        <note width="150">Item Title</note>
		</item>
                       
                <item type="combo" label="Type of Entry" name="MH_Type" readonly="true" validate="NotEmpty" required="true" >
                        <option value="2" label="Expense" selected="true" />
                        <option value="1" label="Income" />
			<note>Select Income / Expense</note>
		</item>' ;
                if($ACL_Obj->ACL_Item == 1) { $validate = ' required="true" '; }  
                echo '<item type="combo" label="Subhead" name="SH_Id" '.$validate.'>
                    <note width="150">Subhead Name</note>
		</item>
		
                <item type="block" width="600" >
                    <item type="template" label="&amp;nbsp;Expense Control" inputWidth="0" className= "blockPadding" ></item>
                    <item type="newcolumn"></item>
                    <item type="input" name="IT_MinAmount" inputWidth="188" labelWidth="0" offsetLeft="-24" validate="^([0-9]*|\d*\.\d{1}?\d*)$" >
                        <note width="170">Minimum Value</note>
                    </item>
                    <item type="newcolumn"></item>
                    <item type="input" name="IT_MaxAmount" inputWidth="188" labelWidth="0" validate="^([0-9]*|\d*\.\d{1}?\d*)$" >
                        <note width="170">Maximum Value</note>
                    </item>
                </item> 
                    
		<item type="input" name="IT_Comments" label="Remarks" value="" rows="3">
			<note width="150">Remarks</note>
                </item>';
		
                if($ACL_Obj->ACL_Item == 1) {
                    echo '<item type="combo" label="Status" name="IT_Status" readonly="true">
                            <option value="1" label="Published" selected="true" />
                            <option value="0" label="Suspend" selected="false" />
                            <option value="4" label="Delete" selected="false" />
                            <note width="150">Publish Status</note>
                    </item>
                    <item type="block" width="500" offsetLeft="120">
                    <item type="checkbox" name="IT_Business" position="label-right"  label="Business Entry" labelWidth="100" offsetLeft="-20"></item>
                    <item type="newcolumn"></item>
                    <item type="checkbox" name="IT_Transfers" position="label-right"  label="Internal Transfers" labelWidth="120"></item>
                    <item type="newcolumn"></item>
                    <item type="checkbox" name="IT_PettyCash" position="label-right"  label="Petty Cash" labelWidth="100"></item>                    
                    </item>
                    <item type="block" width="500" offsetLeft="120">

                        <item type="checkbox" name="IT_DualEntry" position="label-right"  label="Dual Entry" labelWidth="100" offsetLeft="-20"></item>
                        <item type="newcolumn"></item>
                        <item type="checkbox" name="IT_OtherUser" position="label-right"  label="Other Branch" labelWidth="100"></item>
                    </item>';
                } else {
                    echo '<item type="hidden" name="IT_Status" value="0"/><item type="hidden" name="IT_Business" value="0"/>';
                }
		
                echo '
                <item type="block" width="600" offsetTop="50">
			<item type="button" value="Proceed to add Description" name="newItemProceed"/>
			<item type="newcolumn"/>
                        <item type="button" value="Save Item" name="newItemValidate"/>
			<item type="newcolumn"/>
			<item type="button" value="Cancel" name="newItemCancel"/>
		</item>
		
	</items>';
?>