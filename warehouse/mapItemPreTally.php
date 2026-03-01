<?php
require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ITObj = new ItemClass();

echo $ITObj->mapItemPreTally($_REQUEST['item'],$preTally_user_id);
?>