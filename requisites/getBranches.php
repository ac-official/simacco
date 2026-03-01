<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$key = $_REQUEST["mask"]; 
include_once($BASEPATH . "preTallyClass/LocationClass.php");
$Brch_Obj = new LocationClass();

if(isset($REQUEST['rpt'])) {
    $filter = "  OF_Id = ".$preTally_user_ofid;
    if($REQUEST['LC_Name'])
        $filter .= " AND LC_Name like '".mysqli_real_escape_string($GLOBALS['con'],$REQUEST['LC_Name'])."%'";
    $Brch_Obj ->viewLocations("LC_Id,LC_Name","WHERE $filter AND LC_Status != 5 ORDER BY LC_Name ");
} else {
    $Brch_Obj -> viewLocations("LC_Id,LC_Name","WHERE LC_Status = 1 AND LC_Name like '".mysqli_real_escape_string($GLOBALS['con'],ucfirst($key))."%' AND OF_Id = ".$preTally_user_ofid);
}

$LCObj = $Brch_Obj->LocationArray;

echo '<complete>';
if(isset($REQUEST['rpt'])) {
    echo '<option value="">All</option>';
}
if($LCObj){   
    foreach($LCObj as $rw){
              echo '<option value="'.str_replace("&","&amp;",$rw->LC_Id).'" >'.str_replace("&","&amp;",$rw->LC_Name).'</option>';
    }
}else { 
    if(!isset($REQUEST['rpt']))
        echo '<option value="ZeroVal" selected="true">No Records Found</option>'; 
    
}
echo '</complete>';
?>