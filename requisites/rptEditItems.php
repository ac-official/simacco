<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");
$type = $REQUEST['type'];
$ItemObj = new ItemClass();

$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;

//$old = array('"', "[", "]");
//$new   = array("", "", "");
//$itemMap = str_replace($old, $new, $Map_Obj[0]->IC_Map);
if($itemMap == '') $itemMap = '""';

//$ItemObj->getItemList("as IT, main_heads as MH, sub_heads as SH  WHERE IT.IT_Status=1 AND IT.SH_Id=SH.SH_Id AND SH.MH_Id=MH.MH_Id AND MH.MH_Type = ".$type." AND (IT.OF_Id=".$preTally_user_ofid." OR IT.IT_Id IN (".$itemMap."))  ORDER BY IT_Name ");
$ItemObj->getItemList("IT.IT_Id, IT.IT_Name","as IT, main_heads as MH, sub_heads as SH ","  WHERE IT.SH_Id=SH.SH_Id AND SH.MH_Id=MH.MH_Id AND MH.MH_Type = ".$type." AND IT.IT_Status!=0 AND IT.IT_Status!=4 AND (IT.OF_Id=".$preTally_user_ofid." OR IT.IT_Id IN (".$itemMap.")) ORDER BY IT_Name ");
$IT_Obj = $ItemObj->ItemArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
//echo'<option value="" selected="true"></option>';
if($IT_Obj){
    foreach($IT_Obj as $rw){      
        echo '<option value="'.$rw->IT_Id.'">'.str_replace("&","&amp;",$rw->IT_Name).'</option>';
    }
}
echo '</complete>';
?>