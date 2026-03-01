<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
$LocationObj = new LocationClass();
$ZoneObj = new ZoneClass();
if($REQUEST['ZnId']=='All')
{ 
$ZoneObj ->getUserZoneLCId($preTally_user_id);    
$ZoneObj->viewLocations("LC_Id,LC_Name,LC_Status", "WHERE LC_Id = ".$preTally_user_lcid." ORDER BY LC_Name"); 
$LC_Obj=array_merge($ZoneObj->ZonArray,$ZoneObj->LocationArray);
} else{
    $lcid=$ZoneObj->getZoneLocations($REQUEST['ZnId']);
$ZoneObj->viewLocations("*", "WHERE LC_Id IN (" . $lcid . ") AND LC_Status != 5 ORDER BY LC_Name");
$LC_Obj = $ZoneObj->LocationArray;
}   


echo '<complete>';
if($REQUEST['RPT_TYPE']=='BSR')
    echo '<option value="All" selected="true">All</option>';
if ($LC_Obj) {
    foreach ($LC_Obj as $rw) {
        $css='';
         if($rw['LC_Status']==0)$css="css='color:red;'";
        echo '<option value="' . $rw['LC_Id'] . '" '.$css.' >' . str_replace("&", "&amp;", $rw['LC_Name']) . '</option>';
    }
} else {
    if ($filter != 'Self')       
        echo '<option value="ZeroVal" selected="true">No Records Found</option>';
}
echo '</complete>';
?>
