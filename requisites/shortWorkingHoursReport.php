<?php
/**
 * Email Friendly Report
 * Created By Master Of Programing
 */
session_start();
if(!isset($_SESSION['token'])){
	http_response_code(404);
	include_once('../404.php');
	die();
}
require_once('../_conf.php');
require_once("../preTallyClass/UserClass.php");
$user = new UserClass();
$lateSign = $user->getLateLoginCount();
$earlySignOut = $user->getEarlySignOutCount();
$hoursIncomplete = $user->getHoursIncompleteCount();
$breakTimeExceed = $user->getBreakTimeExceedCount();
?>
<html>
<head>
	<title>SHORT WORKING HOURS REPORT</title>
	<style>
		.heading{
			width: 100%;
			text-align: center;
			margin-bottom: 45px;
			margin-top: 15px;
			font-size: 18px;
		}
		table, th, td {
		  border: 1px solid black;
		  border-collapse: collapse;
		}
		table tr{
			text-align: center;
		}
	</style>
</head>
<body>
<div class="heading"><b>SHORT WORKING HOURS REPORT <font color="blue"><?php echo date('F Y');?></font></b></div>
<table width="100%" cellpadding="5" cellspacing="5">
	<thead>
		<tr>
			<th width="10"><b>Si.No</b></th>
			<th><b>Day</b></th>
			<th><b>Late Sign In</b></th>
			<th><b>Early Sign Out</b></th>
			<th><b>Hours Incomplete</b></th>
			<th><b>Break Exceeds</b></th>
		</tr>
	</thead>
	<tbody>
		<?php
			for($i=0;$i<count($lateSign);$i++){
			?>
				<tr>
					<td><?php echo ($i+1)?></td>
					<td><?php echo ($i+1)?></td>
					<td><?php echo (isset($lateSign[$i]->LateLoginUserCount))?$lateSign[$i]->LateLoginUserCount:'0'?></td>
					<td><?php echo (isset($earlySignOut[$i]->EarlySignoutUserCount))?$earlySignOut[$i]->EarlySignoutUserCount:'0'?></td>
					<td><?php echo (isset($hoursIncomplete[$i]->IncompleteHoursUserCount))?$hoursIncomplete[$i]->IncompleteHoursUserCount:0?></td>
					<td>
					<?php 
						if(isset($breakTimeExceed[$i]->ExceededBreakUserCount)){
						echo $breakTimeExceed[$i]->ExceededBreakUserCount;
					}else{
						echo '0';
					}?>
						</td>
				</tr>
			<?php
			}
		?>
	</tbody>
</table>
<div style="background-color: orange;min-height: 50px;">   
          <p>Powered By&nbsp;<strong>Simacco</strong>.</p>
    <p>This mail is system generated</p>
    </div>
</body>
<html>