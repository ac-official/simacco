<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/InventoryClass.php");
$InvObj = new InventoryClass();
$filter=" WHERE IT.OF_Id= ".$preTally_user_ofid;
$InvObj->listInventoryItems($filter);
$Inv_Obj = $InvObj->InvItemReadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
        $StatusArray=array(1=>"Active",2=>"Disposed",3=>"Instock",4=>"Missing",5=>"Repair",6=>"Stolen");
        if($Inv_Obj) {
            $j = 1;           
            foreach ($Inv_Obj as $rw) {  
$location=$rw->LC_Name !='' ? $rw->LC_Name : "All" ;               
                echo '<row id="'.$rw->INV_ItemID.'">	
                    <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>
                    <userdata name="LC_Id">'.$rw->LC_Id.'</userdata>
                    <userdata name="INV_ItemName">'.$rw->INV_ItemName.'</userdata>
                    <userdata name="INV_ItemModel">'.$rw->INV_ItemModel.'</userdata>
                    <userdata name="INV_TypeID">'.$rw->INV_TypeID.'</userdata>
                    <userdata name="INV_ItemQty">'.$rw->INV_ItemQty.'</userdata>
                    <userdata name="INV_ItemAssignee">'.$rw->INV_ItemAssignee.'</userdata>
                    <userdata name="INV_ItemStatus">'.$rw->INV_ItemStatus.'</userdata>    
                    <userdata name="INV_ItemRemarks">'.$rw->INV_ItemRemarks.'</userdata>
                    <userdata name="INV_PurchaseDate">'.$rw->INV_PurchaseDate.'</userdata>
                    <userdata name="INV_AddedBy">'.$rw->INV_AddedBy.'</userdata>    
                    <userdata name="INV_ItemCDate">'.$rw->INV_ItemCDate.'</userdata> 
                    <userdata name="INV_TypeName">'.$rw->INV_TypeName.'</userdata> 
                    <userdata name="INV_ItemRemarks">'.$rw->INV_ItemRemarks.'</userdata>    
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" ">'.$rw->INV_ItemName.'</cell>
                    <cell title=" ">'.$rw->INV_ItemModel.'</cell>
                    <cell title=" ">'.$rw->INV_TypeName.'</cell>
                    <cell title=" ">'.$rw->INV_ItemQty.'</cell>
                    <cell title=" ">'.$rw->Assignee.'</cell>
                    <cell title=" ">'.$location.'</cell>
                    <cell title=" ">'.$StatusArray[$rw->INV_ItemStatus].'</cell>
                    <cell title=" ">'.$rw->INV_PurchaseDate.'</cell>
                    <cell><![CDATA[<img src="images/icon/edit_icon.gif" style="margin:2px 0; cursor:pointer;" INV_ItemID="'.$rw->INV_ItemID.'"); onclick="preTally.Inventory.editInventoryItem(this,'.$rw->INV_ItemID.');"/>]]></cell>
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" ">'.$j.'</cell>    
                    </row>';	
                $j++;
            }
                
            }          
        else {
            echo '<row id="0"> 
            <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
        }
echo '</rows>';
?>