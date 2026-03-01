<?php
/*
	* Created By ArunDev
*/
if(!isset($_SESSION))session_start();
// Database configuration
$hostname = "localhost"; 
$username = "root"; 
$password = "@d3%TSZh5n?2DG3QWjAN"; 
$database = "analytics";


$mysqli =  mysqli_connect($hostname, $username, $password, $database);


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$visited_page    = !empty($_REQUEST['QUERY_STRING']) ? htmlspecialchars($_REQUEST['QUERY_STRING'],ENT_QUOTES):$_SERVER['REQUEST_URI'];
$submited_params = json_encode($_REQUEST);
$user_id         = isset($_SESSION['preTally_user_id']) ? $_SESSION['preTally_user_id'] : 0;
$todayDate		 = date('Y-m-d h:s:i');

$sql = "INSERT INTO users_statistics (visited_page, submited_params, user_id,created_date)
VALUES ('$visited_page', '$submited_params', '$user_id', '$todayDate')";

$mysqli->query($sql);

mysqli_close($mysqli);
?>