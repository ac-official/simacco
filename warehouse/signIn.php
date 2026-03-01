<?php
require_once($BASEPATH . "/preTallyClass/UserClass.php");

$UserObj = new UserClass();

$UserObj->US_Login	= htmlspecialchars($_REQUEST['username'], ENT_QUOTES);
$UserObj->US_Password	= md5($_REQUEST['password']);
$UserObj->US_Remember	= $_REQUEST['remember'];
echo $UserObj->signInUser($flag=0);
/*$_SESSION['varname'] = $result;
$_SESSION['RefCount'] = 1;
header('location:'.constant("BASE_PATH").'/index.php'); */



//header("location:http://localhost/pretally/index.php"); 
////if($result != "success") {
//    $_SESSION['varname'] = $result;
////} 
?>