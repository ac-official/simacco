<?php 
require_once ('BrowserDetect.php');
$browserDetect = new BrowserDetect;

echo $browserType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
echo "<br>".$scriptVersion = $browserDetect->getScriptVersion();
echo "<br>".htmlentities($_SERVER['HTTP_USER_AGENT']);
echo "<br>".gethostbyaddr($_SERVER['REMOTE_ADDR']);
echo "<br>".gethostbyname($_SERVER['REMOTE_ADDR']);
die();
?>