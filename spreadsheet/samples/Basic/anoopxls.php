<?php
require_once 'anoop.php';
$xls = new Excel('Report');

$title = "Sheet1";
    $colors = array("red", "blue", "green", "yellow", "orange", "purple");
    
foreach ($rows as $num => $row) {
  $xls->home();
  $xls->label($row['id']);
  $xls->right();
  $xls->label($row['title']);
  $xls->down();
}
ob_start();
$data = ob_get_clean();
file_put_contents(__DIR__ .'/report.xls', $data);
?>