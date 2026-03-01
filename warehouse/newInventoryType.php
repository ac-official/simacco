<?php 
include_once($BASEPATH . "preTallyClass/InventoryClass.php"); 
$InventoryTypeObj = new InventoryClass();
$InventoryTypeObj1234 = new InventoryClass();
$InventoryTypeObj->InvTypeArray = array('INV_TypeName'=>$_REQUEST["InvntTypeName"],
        'INV_TypeStatus'=>1,'INV_TypeRemarks'=>"");
	if(htmlspecialchars($_REQUEST['INV_TypeID'], ENT_QUOTES) == 0) {		
		echo $InventoryTypeObj->AddInventoryType();
                
	} else {
		echo $InventoryTypeObj->updateInventoryItem(htmlspecialchars($_REQUEST['INV_ItemID'], ENT_QUOTES));
	} 
        ?>