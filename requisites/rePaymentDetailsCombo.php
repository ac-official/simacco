<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$salRepObj = new AttendanceClass();
$SA_Id = $REQUEST['SA_Id'];
if($key  = $_REQUEST["mask"]){
    $filter .=" AND CONCAT(US.US_FName,' ', US.US_LName) LIKE '".$key."%'";
}
$salRepObj->viewDetails("CONCAT(US.US_FName ,' ', US.US_LName) AS Name,SA_Amount,SA.SA_Id",
        "salary_advance_payment AS SA LEFT JOIN users_auth AS US ON US.US_Id=SA.US_Id",
        " WHERE US.OF_Id=".$preTally_user_ofid ." AND US.US_Status!=0" .$filter);
$SA_Obj = $salRepObj->DetailsArray;
$count=0;
echo '<complete > ';
if($SA_Obj){ 
    foreach($SA_Obj as $rw) {
        if($SA_Id){
            if($SA_Id==$rw->SA_Id){$selected = 'selected="true"';}
        }else{
            if($count==0){$selected = 'selected="true"';  $count++;} else $selected = 'selected="false"';
        }
        echo '<option value="'.$rw->SA_Id.'" '.$selected.' >'.$rw->Name.'-'.$rw->SA_Amount. '</option>';
    }
}
echo '</complete>';
?>