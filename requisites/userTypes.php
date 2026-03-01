<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/ACLClass.php");
if($REQUEST["type"]=="filt") $ofid = $preTally_user_ofid;    
else $ofid = isset($REQUEST['ofid']) ? $REQUEST['ofid'] : 0 ;    
$filter = "ACL.OF_Id = ".$ofid." AND OF1.OF_Id = ACL.OF_Id AND ACL.ACL_Status=1";
$ACLObj = new ACLClass();
$ACLObj->viewACL(' WHERE '.$filter.' ORDER BY ACL.ACL_Name');

$ACL_Obj = $ACLObj->ACLArray;

echo '<complete >';
if($ACL_Obj){
    if(($_GET['r'] == '') && ($REQUEST["type"]!="filt"))  echo '<option value="" selected="true">Select ACL Type</option>';
    else if ($REQUEST["type"]=="filt") echo '<option value="All" selected="true">All</option>';
    foreach($ACL_Obj as $rw) {
       
        $selected = '';
        if($_GET['r'] == $rw->ACL_Id) $selected = 'selected="true"';
        echo '<option value="'.$rw->ACL_Id.'" '.$selected.' >'.$rw->ACL_Name.'</option>';
    }
}
echo '</complete>';
?>