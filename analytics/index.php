<?php
session_start();
$permission = array('36','2403');
$activeUser = isset($_SESSION['preTally_user_id'])?$_SESSION['preTally_user_id']:0;
$domain = "http://".$_SERVER['SERVER_NAME'];
if(empty($_SESSION) || !in_array($activeUser,$permission)){
    http_response_code(403);
    die('Forbidden');
}
?>
<!DOCTYPE html > <html> <head> <title> SIMACCO:: Live Analytics </title> <meta name="Author" content="Developed By ArunDev"> <link rel="icon" href="../images/favicon.ico" type="image/x-icon" /> <style> .styled-table{width:100%;border-collapse:collapse;margin:15px 0;font-size:.9em;font-family:sans-serif;min-width:400px;box-shadow:0 0 20px rgba(0,0,0,.15)}.styled-table thead tr{background-color:#009879;color:#fff;text-align:left}.styled-table td,.styled-table th{padding:12px 15px}.styled-table tbody tr{border-bottom:1px solid #ddd}.styled-table tbody tr:nth-of-type(2n){background-color:#f3f3f3}.styled-table tbody tr:last-of-type{border-bottom:2px solid #009879}.styled-table tbody tr.active-row{font-weight:700;color:#009879} .offline{float:left;color:#D92550;font-weight: bold;margin-left: 5;font-size: 18;display: inline-block;} .blink{display: none;} </style> </head> <body> <div class="offline" style="color: #1A73E8;">Recently Offline : </div>&nbsp;<off-person class="offline"> Not Available</off-person> <table class="styled-table"> <thead><tr><th colspan="6" style="text-align: center;font-weight: bold; background-color: #D92550;">ONLINE USERS <count></count></th><tr><th>Si.No</th><th>Name</th><th>Time</th><th>Signed IP</th><th>Signed Location</th><th>Browser</th></tr></thead> <tbody id="users-list"></tbody> </table> <script src = "../scripts/app.js" ></script> </body> </html>