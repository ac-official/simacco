<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/ItemClass.php");
$type = $REQUEST['type'];
$Shid = $REQUEST['SH_Id'];
$key  = $_REQUEST["mask"]; 
$filter = $REQUEST['filter'];
$typeOfEntryFiter='';
 if($REQUEST['typeOfEntry']){
    if($REQUEST['typeOfEntry']!="All"){
        if($REQUEST['typeOfEntry']=="InternalTransferReceived"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  IT.MH_Type = 1 ";//Internal Transfer Received;
        }else if($REQUEST['typeOfEntry']=="InternalTransferPaid"){
            $typeOfEntryFiter = " AND IT.IT_Transfers=1 AND  IT.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReceived"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  IT.MH_Type = 1 ";
        }else if($REQUEST['typeOfEntry']=="BusinessReturned"){
            $typeOfEntryFiter = " AND IT.IT_Business=1 AND  IT.MH_Type = 2 ";
        }else if($REQUEST['typeOfEntry']=="Income"){
            $typeOfEntryFiter = " AND IT.MH_Type = 1 AND IT.IT_Transfers=0";
        }else if($REQUEST['typeOfEntry']=="Expense"){
            $typeOfEntryFiter = " AND IT.MH_Type = 2 AND IT.IT_Transfers=0";
        }
    }
 }

//print_r($REQUEST);
$ItemObj = new ItemClass();
$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;
//$old = array('"', "[", "]");
//$new   = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';
if($filter){
    if($Shid) $filtr = "AND IT.SH_Id = ".$Shid;
    if($type) $filtr = "AND IT.MH_Type = ".$type;
} else {
    if($Shid) $filtr = "AND IT.SH_Id = ".$Shid;
    if($type) $filtr = "AND IT.MH_Type = ".$type;
//    if($Shid) $filtr = "AND SH.SH_Track!= 1 AND IT.SH_Id = ".$Shid;
//    if($type) $filtr = "AND SH.SH_Track!= 1 AND IT.MH_Type = ".$type;
}
//if($Shid) $filtr = "AND IT.SH_Id = ".$Shid;
$statusFilter = " IT.IT_Status = 1 ";
if($key && $key != 'master')  $filtr = "AND IT.IT_Name LIKE '%".$key."%' ";
else {
    $statusFilter = " IT.IT_Status != 4 ";
    if(isset($_REQUEST['IT_Name']))
        $statusFilter .= " AND IT.IT_Name LIKE '%".trim($_REQUEST['IT_Name'])."%' ";
}

if(isset($REQUEST['fixedExp']))   // Business & Bonus Reports -> fixed Expense item combo
    $statusFilter .= " AND IT.MH_Type = 2 ";


//else if($_REQUEST['itemId']) $filtr = "AND IT.IT_Id = ".$_REQUEST['itemId'];
//else $filtr = 'AND IT.IT_Status != 4 ';

$flag = $count = '';
$sqlFilter = "as IT, sub_heads as SH WHERE IT.SH_Id = SH.SH_Id  AND ".$statusFilter." ".$filtr." AND (IT.OF_Id=".$preTally_user_ofid." OR IT.IT_Id IN (".$itemMap."))".$typeOfEntryFiter." ORDER BY IT.IT_Name";
if($_REQUEST['itemId'] && $_REQUEST['itemId'] != 'null' && $_REQUEST['itemId'] != 'undefined') {
    $flag = 'union';
    $sqlFilter .=  ' LIMIT 0,30) UNION (SELECT IT_Id,IT_Name FROM items WHERE IT_Id ='.$_REQUEST['itemId'].')';
} else if($key && $key == 'master') {
    if(!isset($_REQUEST['IT_Name']))
        $count = 1 ;
    $sqlFilter .= ' LIMIT 0,30 ';
}
//$ItemObj->viewItems("IT_Id,IT_Name","as IT, sub_heads as SH WHERE IT.SH_Id = SH.SH_Id  AND IT.IT_Status=1 ".$filtr." AND (IT.OF_Id=".$preTally_user_ofid." OR IT.IT_Id IN (".$itemMap.")) ORDER BY IT.IT_Name");

$ItemObj->viewItems("IT_Id,IT_Name",$sqlFilter,$flag);
$IT_Obj = $ItemObj->ItemArray;


echo '<complete > ';
if(!$key && !isset($REQUEST['fixedExp'])) echo '<option value = "" selected="true"> Select Item</option>';
if(isset($REQUEST['fixedExp'])) echo '<option value = "" >All</option>';
if($IT_Obj){ 
    foreach($IT_Obj as $rw) {
        if($key == 'master' && $count == 1) {$selected = 'selected="true"';  $count++;} else $selected = 'selected="false"';
        echo '<option value="'.$rw->IT_Id.'" '.$selected.' >'.$rw->IT_Name.' </option>';
       
    }
}
echo '</complete>';
?>