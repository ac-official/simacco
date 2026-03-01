<?php 

define('FPDF_FONTPATH', 'font/');
require($BASEPATH ."preTallyClass/fpdf.php");  

$data = json_decode($_REQUEST['data']);
if($_REQUEST['type'])
   $data = json_decode(json_encode($data), true);

$pdf = new FPDF();
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

$y_axis_initial = 10;
$row_height = 6;
//print column titles for the actual page
$pdf->SetFont('Times', 'B', 16);
$pdf->SetY($y_axis_initial);
$pdf->SetX(10);
$pdf->SetY(20);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(180,6,"Muble Solutions Pvt. Ltd", 0, 0, 'C', 1);
$y_axis = $y_axis + $row_height;

$pdf->SetY($y_axis_initial);
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 14);    

$pdf->SetX(30);
$pdf->SetY(30);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(180, 8, "XLI-725, C-2 Second floor ,C.P Ummer Road,Ernakulam 682035", 0, 0, 'C', 1);

$pdf->SetY(38);
$pdf->Cell(180, 8, "Ph. 0484-3934839,3934838", 0, 0, 'C', 1);
$pdf->Cell(180, 8, $AttObj->salarySlipPdfDataArray[0]." ".$AttObj->salarySlipPdfDataArray[1], 0, 0, 'L', 1);

$pdf->SetY(46);
$pdf->SetFont('Arial', '', 17);
$pdf->Cell(180, 8, "CASH RECEIPT", 0, 0, 'C', 1);

$pdf->SetY(54);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(85, 8, "Sl. No.: ".$data['CashReceiptID'], 0, 0, 'L', 1);
$pdf->Cell(95, 8, "Reff No: ".$data['DocumentID'], 0, 0, 'R', 1);
    
$pdf->SetY(62);
$pdf->SetFont('Arial', '', 11);	
$pdf->Cell(15, 8, "Date : ", 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);	
$pdf->Cell(25, 8, $data['CashReceivedDate'], 0, 0, 'L', 1);
$pdf->SetFont('Arial', '', 11);	
$pdf->Cell(140, 8, "Estimate Amount : ".$data['TotalAmount']."/-" , 0, 0, 'R', 1);

$pdf->SetY(70);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(65, 8, "Received the following amount from : ", 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 8, $data['FirstName'].",".$data['LastName'], 0, 0, 'L', 1);

$pdf->SetY(78);
$phoneData = $data['Mobile'];
if($data['Landline'])
    $phoneData .= " / ".$data['Landline'];
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(20, 8, "Phone No : ", 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(100, 8, $phoneData, 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);	

$pdf->SetY(88);
$pdf->Cell(95, 6, "Narration", 1, 0, 'L', 1);
$pdf->Cell(45, 6, "Qty", 1, 0, 'L', 1);
$pdf->Cell(45, 6, "Amount", 1, 0, 'L', 1);

$pdf->SetY(94);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, "ATTESTATION ADVANCE", 1, 0, 'L', 1);
$pdf->Cell(45, 6, "1", 1, 0, 'L', 1);
$pdf->Cell(45, 6, $data['ReceivedAmount'], 1, 0, 'L', 1);

$pdf->SetY(105);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(180, 6, "For Muble Solutions PVT LTD", 0, 0, 'R', 1);

$pdf->SetY(115);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(180, 6, "Authorised Signatory", 0, 0, 'R', 1);

$file_name = substr(mt_rand().mt_rand().mt_rand(),4,6)."_".date("H_i_s")."_CashReceipt.pdf";
$pdf->Output("uploads/trackReceipts/".$file_name,"F");
echo $file_name;