<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/UserClass.php");
$EMP_Id = $REQUEST['Emp_Id'];
if($EMP_Id != 0)
{
    $USObj = new UserClass();
    $USObj->viewEmpId($EMP_Id);
    $EMP_Obj = $USObj->UserArray;
}


echo '<complete >';
if($EMP_Obj){      
    foreach($EMP_Obj as $rw) {
        echo '<option value="'.$rw->US_Id.'" selected="true">'.$rw->US_EMPID.'</option>';
    }
}
echo '</complete>';
?>