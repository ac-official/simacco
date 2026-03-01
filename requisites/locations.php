<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
$LocationObj = new LocationClass();
$filter = $_REQUEST["mask"] !='' ? $_REQUEST["mask"] : $REQUEST["mask"]; 

if ($filter) {
    $LocationObj->viewLocations("*", "WHERE LC_Status=1 AND OF_Id = ".$preTally_user_ofid." AND LC_Name like '".$filter."%' ORDER BY LC_Name"); 
}else if (isset($REQUEST['ofid']) && $REQUEST['ofid'] > 0) {   
    $LocationObj->viewLocations("*", "WHERE LC_Status=1 AND OF_Id = ".$REQUEST['ofid']." ORDER BY LC_Name");            
}else if ($REQUEST['filter']== 'all') {
    $LocationObj->viewLocations("*", "WHERE OF_Id = ".$preTally_user_ofid." AND LC_Status != 5 ORDER BY LC_Name");    
}else if ($REQUEST['filter']== 'CHK') { //Showing only branches other than permanently closed
    $LocationObj->viewLocations("*", "WHERE LC_Status!=2 AND LC_Status != 5 AND OF_Id = ".$preTally_user_ofid." ORDER BY LC_Name");    
}else if (isset($REQUEST['sibling_id']) ) { //03-09-25
    $LocationObj->viewAllOfficeLocation("", "WHERE lc.LC_Status=1 AND offc.Sibling_id =".$REQUEST['sibling_id']."  ORDER BY lc.LC_Name");
}else {
    $LocationObj->viewLocations("*", "WHERE OF_Id = ".$preTally_user_ofid." AND LC_Status != 5 ORDER BY LC_Name");    
}


//if($ofid!=""){
//    $ofid=$REQUEST['ofid'];
//    $LocationObj->viewLocations("*", "WHERE OF_Id = ".$ofid);
//} else {
//    $LocationObj->viewLocations("*", " WHERE LC_Name like '".mysqli_real_escape_string($GLOBALS['con'],(ucfirst($key))."%' Order By LC_Name");
//}

$LC_Obj = $LocationObj->LocationArray;

echo '<complete>';
if($filter == 'Self')  { 
    echo '<option value="'.$preTally_user_lcid.'"  selected ="true">Self Office( '.$preTally_user_lcname.' )</option>'; 
} else {
$AllSelected = '';
//if($REQUEST['seltdQffz'] == 0) echo '<option value="0" selected="true" >All</option>';
if($REQUEST['ctype']=='check' || $REQUEST['type']=='filt' || $REQUEST['filter']=='all'){
    if(isset($REQUEST['seltdQffz']) && $REQUEST['seltdQffz'] != '' && $REQUEST['seltdQffz'] != 0) 
    echo '<option value="0">All</option>';
    else
    echo '<option value="0" selected="true" >All</option>';    
}
else if(!$filter && (!isset($REQUEST['seltdQffz']) || $REQUEST['seltdQffz']==0) && $REQUEST['filter'] != 'BMR' && $REQUEST['filter'] != 'CHK' ) echo '<option value="" selected="true">Select Branch</option>';
}
if($LC_Obj){
    
    foreach($LC_Obj as $rw) {
        $css="";
        if($rw->LC_Status==0 && $REQUEST['filter'] == 'CHK')$css="css='color:red;'";
        $selected = 'selected ="false"';
        if($preTally_user_lcid == $rw->LC_Id && $REQUEST['filter'] == 'BMR') $selected = 'selected ="true"';
        if(isset($REQUEST['seltdQffz']) && isset($REQUEST['seltdQffz']) != '' && $REQUEST['seltdQffz'] === $rw->LC_Id) $selected = 'selected ="true"';
        echo '<option value="'.$rw->LC_Id.'" '.$selected.' '.$css.'>'.str_replace("&","&amp;",$rw->LC_Name).'</option>';
    }
}else { if($filter != 'Self') echo '<option value="ZeroVal" selected="true">No Records Found</option>'; }
echo '</complete>';
?>
