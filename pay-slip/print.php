<?php 
/*
	** Created By ArunDev Mublesolutions
*/
error_reporting(0);
require 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
$html2pdf = new Html2Pdf('P','A4','en', false, 'UTF-8', array(10, 15, 10, 10));
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>SALARY SLIP</title>
</head>
<body style="font-size:13px;">
	<table cellpadding="0" cellspacing="1" border="0" width="700">
		<?php 
		if($_POST['template']==1)include_once('includes/urogulf.php');
		else include_once('includes/muble.php');?>
		<tr>
			<td colspan="2" style="padding-top:20px;">
			<table border="1" cellspacing="0" cellpadding="0" width="700">
				<tr>
					<td colspan="2">
						<table border="0" cellspacing="0" cellpadding="0" width="700" style="padding: 7px;">
							<tr>
								<th align="left" height="25" colspan="2">
									SALARY SLIP
								</th>
								<th align="right" colspan="2">
									MONTH : <?php echo strtoupper($_POST['month']);?> - <?php echo $_POST['year'];?>
								</th>
							</tr>
							<tr>
								<td width="110" height="20">Name</td>
								<td width="270">: <?php echo ucwords($_POST['name']);?></td>
								<td width="175">Working Days</td>
								<td width="110">: <?php echo $_POST['w_days'];?></td>
							</tr>
							<tr>
								<td height="20">Designation</td>
								<td>: <?php echo ucwords($_POST['designation']);?></td>
								<td>Worked Days</td>
								<td>: <?php echo $_POST['wd_days'];?></td>
							</tr>
							<tr>
								<td height="20">Employee Code</td>
								<td>: <?php echo strtoupper($_POST['emp_code']);?></td>
								<td>Sundays and Holidays</td>
								<td>: <?php echo $_POST['holiday'];?></td>
							</tr>
							<tr>
								<td height="20">PAN Number</td>
								<td>: <?php echo strtoupper($_POST['pan_no']);?></td>
								<td>LOP</td>
								<td>: <?php echo $_POST['lop'];?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th width="350">
						<table border="0" cellspacing="0" cellpadding="0" width="350" style="padding: 7px;">
							<tr>
								<th align="left" width="165" height="25">
									EMOLUMENTS
								</th>
								<th align="right" width="165">
									Amount Rs.
								</th>
							</tr>
						</table>						
					</th>
					<th width="350" height="30">
						<table border="0" cellspacing="0" cellpadding="0" width="350" style="padding: 7px;">
							<tr>
								<th align="left" width="165" height="25">
									DEDUCTIONS
								</th>
								<th align="right" width="165">
									Amount Rs.
								</th>
							</tr>
						</table>
					</th>
				</tr>
				<tr>
					<td>
						<table border="0" cellspacing="0" cellpadding="0" width="350" style="padding: 7px;">
							
							<tr>
								<td width="250" height="20">Basic Salary ( BS )</td>
								<td width="80" align="right"><?php echo $_POST['basic'];?></td>
							</tr>

							<tr>
								<td height="20">House Rent Allowance ( HRA )</td>
								<td align="right"><?php echo (!empty($_POST['hra']))?$_POST['hra']:'0';?></td>
							</tr>
							<tr>
								<td height="20">Medical Allowance</td>
								<td align="right"><?php echo (!empty($_POST['m_allow']))?$_POST['m_allow']:'0';?></td>
							</tr>
							<tr>
								<td height="20">Conveyance Allowance</td>
								<td align="right"><?php echo (!empty($_POST['c_allow']))?$_POST['c_allow']:'0';?></td>
							</tr>
							<tr>
								<td height="20">City Compensatory Allowance ( CCA )</td>
								<td align="right"><?php echo (!empty($_POST['cca']))?$_POST['cca']:'0';?></td>
							</tr>
							<tr>
								<td height="20">Other Emoluments</td>
								<td align="right"><?php echo (!empty($_POST['o_emo']))?$_POST['o_emo']:'0';?></td>
							</tr>
							<tr>
								<td height="20">Previous Adjustment</td>
								<td align="right"><?php echo (!empty($_POST['p_adj']))?$_POST['p_adj']:'0';?></td>
							</tr>
							<tr>
								<td colspan="2" height="60"></td>
							</tr>
							<tr>
								<th align="left" height="20">Gross Pay</th>
								<th align="right"><?php echo $_POST['g_pay'];?></th>
							</tr>
						</table>
					</td>
					<td>
						<table border="0" cellspacing="0" cellpadding="0"  width="350" style="padding: 7px;">
							
							<tr>
								<td width="250" height="20">Professional Tax ( PT )</td>
								<td width="80" align="right"><?php echo (!empty($_POST['pt']))?$_POST['pt']:'0';?></td>
							</tr>

							<tr>
								<td height="20">Tax Deducted at Source ( TDS )</td>
								<td align="right"><?php echo (!empty($_POST['tds']))?$_POST['tds']:'0';?></td>
							</tr>
							<tr>
								<td height="20">Other Deductions ( Loan, etc. )</td>
								<td align="right"><?php echo (!empty($_POST['loan']))?$_POST['loan']:'0';?></td>
							</tr>
							<tr>
								<td height="20">EPF</td>
								<td align="right"><?php echo (!empty($_POST['epf']))?$_POST['epf']:'0';?></td>
							</tr>
							<tr>
								<td height="20">ESI</td>
								<td align="right"><?php echo (!empty($_POST['esi']))?$_POST['esi']:'0';?></td>
							</tr>
							<tr>
								<td height="20">LWF</td>
								<td align="right"><?php echo (!empty($_POST['lwf']))?$_POST['lwf']:'0';?></td>
							</tr>
							<tr>
								<td height="20">LOP Amount</td>
								<td align="right"><?php echo (!empty($_POST['lop_amt']))?$_POST['lop_amt']:'0';?></td>
							</tr>
							<tr>
								<td colspan="2" height="60"></td>
							</tr>
							<tr>
								<th align="left" height="20">Net Pay</th>
								<th align="right"><?php echo $_POST['net_pay'];?></th>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th colspan="2" align="left" height="20" style="padding-left: 7px; padding-top: 5px; padding-bottom: 4px;">Payment Details</th>
				</tr>
				<tr>
					<td style="padding: 7px;">
						Bank : <?php echo strtoupper($_POST['bank']); ?>
						<?php /*<table border="0" cellspacing="0" cellpadding="0" width="350" style="padding: 7px;">
							<tr>
								<td align="left" width="110" height="25">
									Bank
								</td>
								<td align="left" width="220">: 
									<?php echo $_POST['bank'];?>
								</td>
							</tr>
						</table>*/?>						
					</td>
					<td style="padding: 7px;">
						Account Number : <?php echo $_POST['ac_no'];?>
						<?php /*<table border="0" cellspacing="0" cellpadding="0" width="350" style="padding: 7px;">
							<tr>
								<td align="left" width="110" height="25">
									Account No
								</td>
								<td align="left" width="220">
									: <?php echo $_POST['ac_no'];?>
								</td>
							</tr>
						</table> */?>
					</td>
				</tr>
				<!--<tr>
					<td colspan="2" height="100" style="font-size:11px; padding: 5px; font-weight: bold;">
						Note: This statement is computer generated and does not require signature.
					</td>
				</tr>-->
			</table>
			
			<br>
			<br>
			<br>
			<span style="font-size:11px; padding: 5px; font-weight: bold;" >
			Note: This statement is computer generated and does not require signature.
			</span> 
			</td>
		</tr>

	</table>
</body>
</html> 
<?php
$pdf = ob_get_contents();
ob_end_clean();
$html2pdf->writeHTML($pdf);
$html2pdf->output('myPdf.pdf');
?>