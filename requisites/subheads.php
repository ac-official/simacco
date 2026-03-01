<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/SubheadClass.php");

$Sub_headObj = new SubheadClass();

$type   = $REQUEST['type'];
$filter = $REQUEST['filter'];
$SHId   = $REQUEST["SHId"];
$mask   = $_REQUEST['mask'];
$for    = $REQUEST['for'];

$fields = "SH.SH_Id, SH.SH_Name, MH.MH_Id, MH.MH_Name";
$tables = "as SH, main_heads as MH";

if($filter) {
    $cond = "WHERE SH.SH_Status=1 AND SH.MH_Id=MH.MH_Id AND MH.MH_Type = ".$type." ORDER BY SH_Name ";
} else if($SHId && $SHId != '') {
    $cond = "WHERE SH.SH_Status=1 AND SH.MH_Id=MH.MH_Id AND MH.MH_Type = ".$type." AND SH.SH_Id = ".$SHId." ORDER BY SH_Name " ;
    $selected = 'selected="true"' ;
} else {
    $cond = "WHERE SH.SH_Status=1 AND SH.MH_Id=MH.MH_Id AND MH.MH_Type = ".$type." AND SH.SH_Name like '".$mask."%' ORDER BY SH_Name ";
    
}

$Sub_headObj->getSubHeads($fields, $tables,$cond);
$SH_Obj = $Sub_headObj->SubheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
//echo '<userdata>jhsdgfhjsd</userdata>';
//echo'<option value="" selected="true">Select Subhead</option>';
//if($SH_Obj){
//    foreach($SH_Obj as $rw){      
//        echo '<option value="'.$rw->SH_Id.'" '.$selected.'>'.str_replace("&","&amp;",$rw->SH_Name).'</option>';
//    }
//}

if($SH_Obj){
    foreach($SH_Obj as $rw){      
        if(isset($for) && isset($for) == 'notfEdit') {
            echo "<option value='{\"SHId\":\"".$rw->SH_Id."\",\"MHId\":\"".$rw->MH_Id."\",\"MHName\":\"".$rw->MH_Name."\"}' ".$selected." >".str_replace("&","&amp;",$rw->SH_Name)."</option>";
        } else {
            echo "<option value='".$rw->SH_Id."' ".$selected." >".str_replace("&","&amp;",$rw->SH_Name).'</option>';  
        }
    }
}else{
    echo '<option value=" " selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>