<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
$ZoneObj = new ZoneClass();
$zn_string=$ZoneObj ->getUserZone($preTally_user_id);
if($ACL_Obj->ACL_BSheet=="4"){
$filter=" AND OF_Id =".$preTally_user_ofid;    
}else{
$filter=" AND ZN_Id IN (".$zn_string.") ";    
}

$ZoneObj ->viewZones('WHERE ZN_Status != 2 '.$filter.' ORDER BY ZN_Name');
$Zone_Obj = $ZoneObj->ZoneArray;

echo '<complete >';
if($REQUEST['type']=='UIE' && $ACL_Obj->ACL_BSheet=="2")
    echo '<option value="All">Self Branch ('.$preTally_user_lcname.')</option>';    
    else    
    echo '<option value="All">All</option>';
if($Zone_Obj){       
    foreach($Zone_Obj as $rw) {
        $css="";
        if($rw->ZN_Status==0)$css="css='color:red;'";
        echo '<option value="'.$rw->ZN_Id.'" '.$css.' >'.$rw->ZN_Name.'</option>';
    }
}
echo '</complete>';
?>