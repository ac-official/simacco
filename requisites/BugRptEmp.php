<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/FeedbackClass.php");
$BugObj = new FeedbackClass();
$key  = $_REQUEST["mask"]; 
if($key)  {$filtr = " AND CONCAT(US.US_FName,' ', US.US_LName) LIKE '".$key."%'";}
$BugObj->viewEmp($preTally_user_ofid,$filtr);
$Bug_Obj = $BugObj->ReportBugEmp;
echo '<complete>';
//echo '<option selected="true">Select Name</option>';
if($Bug_Obj){      
    foreach($Bug_Obj as $rw) {
        echo '<option value="'.$rw->US_Id.'" >'.$rw->Name.' </option>';
    }
}else{
     echo '<option value="0" selected="true">No Records Found</option>'; 
}
echo '</complete>';
?>