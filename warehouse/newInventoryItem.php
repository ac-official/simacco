<?php
require_once($BASEPATH . "preTallyClass/InventoryClass.php");
$InventoryObj= new InventoryClass();

$InventoryObj->InvItemArray = array(        
	'OF_Id' 	=> $preTally_user_ofid,        
	'LC_Id'         => $_REQUEST['LC_Id'],
        'INV_ItemName' 	=> htmlspecialchars($_REQUEST['INV_ItemName'], ENT_QUOTES),
        'INV_ItemModel' => htmlspecialchars($_REQUEST['INV_ItemModel'], ENT_QUOTES),
	'INV_TypeID' 	=> $_REQUEST['INV_TypeID'],
        'INV_ItemQty' 	=> $_REQUEST['INV_ItemQty'],
        'INV_ItemAssignee' 	=> $_REQUEST['INV_ItemAssignee'],
        'INV_ItemStatus' 	=> $_REQUEST['INV_ItemStatus'],
        'INV_ItemRemarks' 	=> $_REQUEST['INV_ItemRemarks'],
        'INV_PurchaseDate' 	=> $_REQUEST['INV_PurchaseDate'],
        'INV_AddedBy'           => $preTally_user_id        
);

	if(htmlspecialchars($_REQUEST['INV_ItemID'], ENT_QUOTES) == 0) {
		$InventoryObj->InvItemArray['INV_ItemCDate']=date('Y-m-d H:i:s');
		echo $InventoryObj->AddInventoryItem();
                
	} else {
		echo $InventoryObj->updateInventoryItem(htmlspecialchars($_REQUEST['INV_ItemID'], ENT_QUOTES));
	}

?>