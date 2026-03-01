<?php
require_once '../../../../preTallyClass/conmanager.php';
$conVariable = new MySqlConnectionManager();

$db_host    = $conVariable->hostName;
$db_port    = $conVariable->hostPort;
$db_user    = $conVariable->userName;
$db_pass    = $conVariable->passWord;
$db_name    = $conVariable->dataBase;
$db_prefix  = 'ss_';
$db_type    = 'MySQL';
?>