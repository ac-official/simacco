<?php
require_once($BASEPATH . "preTallyClass/UnusedItemDescClass.php");
require_once($BASEPATH . "preTallyClass/BackupClass.php");
$UnusedObj = new UnusedItemDescClass();
$BkupObj   = new BackupClass();

if($REQUEST['type'] == 'item') {    // edit selected item
    $UnusedObj->IT_Data = array(
        'IT_Name'  => $UnusedObj->cleanData($REQUEST['IT_Name'])
    );
    
    $BkupObj->backupDetails('IT_Id = '.$REQUEST['IT_Id'],$preTally_user_id,'items_bkup','items');

    echo $UnusedObj->updateItem($preTally_user_ofid, " WHERE IT_Id = ".$REQUEST['IT_Id'], $REQUEST['IT_Id']);
}
else if($REQUEST['type'] == 'desc') {   // edit selected description
    $UnusedObj->DS_Data = array(
        'DS_Description'  => $UnusedObj->cleanData($REQUEST['DS_Description'])
    );
    echo $UnusedObj->updateDesc($preTally_user_ofid, " WHERE DS_Id = ".$REQUEST['DS_Id'], $REQUEST['DS_Id']);
}
else if($_REQUEST['delete_ItemsID']) {   // delete selected items
    $condition = " WHERE IT_Id IN (".$_REQUEST['delete_ItemsID'].")";
    echo $UnusedObj->deleteUnused("items",$condition,"IT_Status");
}
else if($_REQUEST['delete_DescID']) {   // delete selected descriptions
    $condition = " WHERE DS_Id IN (".$_REQUEST['delete_DescID'].")";
    echo $UnusedObj->deleteUnused("descriptions",$condition,"DS_Status");
}
exit;