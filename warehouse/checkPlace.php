<?php
require_once($BASEPATH . "/preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AP_NameCity=$_REQUEST['Text'];
//$AP_Name =explode("/", $AP_NameCity);
//$AP_Name = $AP_Name[0];
$AP_IdArray=$AttObj->getValue('address_places', 'AP_Id', 'WHERE AP_Name = '."'". $AP_NameCity ."'");
$AP_Id=$AP_IdArray[0];
if(!$AP_Id|| $AP_Id==""){
    echo "0";
}else{
    echo $AP_Id;
}



?>