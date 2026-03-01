<?php 
header('Content-Type: image/jpeg');
include('preTallyClass/ImageClass.php');
$image = new ImageClass();
$image->load('images/'.$_REQUEST['src']);
$image->resize($_REQUEST['w'],$_REQUEST['h']);
$image->output();
?>