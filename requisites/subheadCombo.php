<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/SubheadClass.php");

$Sub_headObj = new SubheadClass();
$fields = "SH.SH_Id, SH.SH_Name";
$tables = "as SH";

$cond  = " WHERE SH.SH_Status=1 ";
$cond .= " ORDER BY SH_Name ";  


$Sub_headObj->getSubHeads($fields, $tables,$cond);
$SH_Obj = $Sub_headObj->SubheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($REQUEST['ctype']=='check')echo '<option value="0">All</option>';
if($SH_Obj){
    foreach($SH_Obj as $rw){      
        
            echo "<option value='".$rw->SH_Id."' >".str_replace("&","&amp;",$rw->SH_Name).'</option>';  
        
    }
}
echo '</complete>';
?>