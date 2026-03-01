<?php
/*
	* Logout All User In An emergency situation
	* Created By ArunDev
*/
session_start();
$allowedUsers = array('36','3830','2403','2995');
function notFound(){
	http_response_code(404);
	include_once('404.php');
	die();
}
if(!isset($_SESSION['token']) || !isset($_GET['secret'])){
	notFound();
}
else{ 
		if(in_array($_SESSION['preTally_user_id'],$allowedUsers)){
			$accountingUsers = array('3830','3750','3752','3761','3783','3784');
			require_once('_conf.php');
			require_once("preTallyClass/UserClass.php");
			$UserObj   = new UserClass();
			$UserObj->forceLogout($accountingUsers);
			$UserObj->forceRemoveMenuPermission();
		?>
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<script src="scripts/force.Zm9yY2U.js"></script>
				<script>
					document.addEventListener("DOMContentLoaded", function () {
					    document.getElementById("logOutAll").addEventListener("click", function () {
					    	document.getElementById("msg").innerHTML="Users are logout from simacco!..";
					    	document.getElementById("logOutAll").style.display = "none";
					    });
					});
			    </script>
			</head>
			<body>
				<p id="msg"></p>
				<button id="logOutAll">LOGOUT USERS</button>
			</body>
		</html>
		<?php
		}
		else{
			notFound();
		}
}
?>