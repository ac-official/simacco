<?php
require_once($BASEPATH ."preTallyClass/UserClass.php");
$UserObj=new UserClass();
$attribs=$_REQUEST['StatValues'];
echo $UserObj->changeAttStatus($attribs["UsId"],$attribs["Status"],$attribs['Type']);
?>

