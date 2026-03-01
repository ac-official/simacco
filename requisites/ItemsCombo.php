<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");
$key  = $_REQUEST['mask'];
$type = $REQUEST['type'];
$ItemObj = new ItemClass();

$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;

//$old = array('"', "[", "]");
//$new   = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';
 
if($key){
    $ItemObj->getItemList("IT_Id, IT_Name","","  WHERE IT_Status=1 AND MH_Type = ".$type." AND (OF_Id=".$preTally_user_ofid." ) AND IT_Name Like '".$key."%' ORDER BY IT_Name ");
} else {
    $ItemObj->getItemList("IT_Id, IT_Name","","  WHERE ( IT_Status = 1 || IT_Status = 0 ) AND (OF_Id=".$preTally_user_ofid." ) ORDER BY IT_Name ");
}
//$ItemObj->getItemList("IT_Id, IT_Name","","  WHERE IT_Status=1 AND MH_Type = ".$type." AND (OF_Id=".$preTally_user_ofid." OR IT_Id IN (".$itemMap.")) AND IT_Name Like '".$key."%' ORDER BY IT_Name ");
$IT_Obj = $ItemObj->ItemArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($REQUEST['ctype']=='check')echo '<option value="0" >All</option>';
else if ($REQUEST["ctype"]=="filt") echo '<option value="All" selected="true">All</option>';
if($IT_Obj){
    foreach($IT_Obj as $rw){      
        echo '<option value="'.$rw->IT_Id.'">'.str_replace("&","&amp;",$rw->IT_Name).'</option>';
    }
}
echo '</complete>';
?>