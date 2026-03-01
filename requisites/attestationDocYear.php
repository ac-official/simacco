<?php
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$currYear   = date("Y");
echo '<complete>';


    for($i=$currYear;$i>=1990;$i--){
     
        echo '<option  value="'.$i.'" >'.$i.'</option>';
    }


echo '</complete>';