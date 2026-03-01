<?php
/*
	* Modified By Arun Dev
*/
include_once("conmanager.php");
$conCls = new MySqlConnectionManager();
$conCls->doConnection();
$con= $conCls->getConnectionHandle();
//$conCls->selectDatabase();
unset($conCls);

/*if(file_exists('./plugins/trackActivity.php'))
	include_once('./plugins/trackActivity.php');*/
?>