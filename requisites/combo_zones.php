<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/ZoneClass.php");
$ZoneObj = new ZoneClass();
$ZoneObj ->viewZones('WHERE ZN_Status != 2 AND OF_Id='.$preTally_user_ofid.' ORDER BY ZN_Name');
$Zone_Obj = $ZoneObj->ZoneArray;

echo '<complete >';
if($Zone_Obj){      
    foreach($Zone_Obj as $rw) {
        $css="";
        if($rw->ZN_Status==0)$css="css='color:red;'";
        echo '<option value="'.$rw->ZN_Id.'" '.$css.' >'.$rw->ZN_Name.'</option>';
    }
}
echo '</complete>';
?>