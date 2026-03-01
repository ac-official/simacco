<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$BB_Id = $REQUEST['BB_Id'];
if($BB_Id != 0)
{
    $GeneralObj = new GeneralClass();
    $GeneralObj->ViewDetails(' * ', 'bank_accounts', 'BB_Id= '.$BB_Id.' AND BA_Status = 1' ,' BA_No ');
    $BA_Obj = $GeneralObj->DataArray;
}

echo '<complete >';
if($BA_Obj){      
    foreach($BA_Obj as $rw) {      
        echo '<option value="'.$rw->BA_Id.'" >'.substr($rw->BA_No,-6).'-'.$rw->BA_DispName.'</option>';
    }
}
echo '</complete>';
?>