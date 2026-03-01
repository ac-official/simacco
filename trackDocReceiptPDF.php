<?php
define('FPDF_FONTPATH', 'font/');
require('mc_table.php');
$data   =   json_decode($_REQUEST['data']);
if($_REQUEST['type'])
   $data = json_decode(json_encode($data), true);

$pdf    =   new PDF_MC_Table();
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

$pdf->SetY(46);
$pdf->SetFont('Arial', '', 17);
$pdf->Cell(180, 8, "DOCUMENT RECEIPT/ ESTIMATE", 0, 0, 'C', 1);

$pdf->SetY(54);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 8, "Sl. No.: ".$data['DocumentID'], 0, 0, 'L', 1);
    
$pdf->SetY(62);
$pdf->SetFont('Arial', '', 11);	
$pdf->Cell(15, 8, "Date : ", 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);	
$pdf->Cell(25, 8, $data['CashReceivedDate'], 0, 0, 'L', 1);

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

$pdf->SetY(86);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(15, 8, "Email :  " , 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(55, 8, $data['Email'] , 0, 0, 'L', 1);

$pdf->SetY(94);
$i = 1;
$pdf->SetWidths(array(15,100,30,40));
$pdf->Row(array("Sl No","Name and Details of Documents","Qty","Amount"));
$pdf->SetFont('Arial', '', 11);
foreach($data['DocumentDetails'] as $docData) {
    srand(microtime()*1000000);
    $pdf->Row(array($i,$docData[0]."\nPROCESS - ".$docData[1],"1",$docData[2]));
    $i++;
}
$pdf->SetWidths(array(115,30,40));
$pdf->SetAligns('R','R');
$pdf->Row(array("Total",$i-1,$data['TotalAmount']));

$pdf->SetY($pdf->GetY()+10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(180, 6, "Terms & Conditions:", 0, 0, 'L', 1);

$pdf->SetY($pdf->GetY()+10);
$pdf->SetWidths(array(185));
$pdf->SetAligns('L','L');
$pdf->Row(array("1) The concerned authorities may change the rules without any notice. "
    . "Due to this our services, charges, durations etc., also may change.\n"
    . "2) As per our commitment, we will try to complete all the attestation and "
    . "related procedures on time, if we are unable to complete the "
    . "attestation formalities on time due to valid reason we will return all "
    . "original documents along with the advance payment if any. \n"
    . "3) In case fake documents are submitted, it will be seized by the "
    . "concerned authorities. So these documents and advance amount will not be returned. \n"
    . "4) Documents must be collected within '90'days after completion of the process, "
    . "we are not responsible for the LOSS of the documents. If not collected within the above "
    . "duration. Assurance: In case the certificates may misplaced / or loss from us, we will "
    . "bear the complete expenses to get a duplicate one from the university / "
    . "concerned department. Your co-operation is a mandatory to get in done. Declaration: "
    . "I solemnly declare that the documents submitted for authentication or authentications/"
    . " attestation (if any) made on it are genuine to the best of my knowledge and belief. "
    . "If the documents submitted by me are found fake or information furnished by me are "
    . "false, I am responsible for the same and action may be taken against me as found"
    . " necessary."));

$pdf->SetY($pdf->GetY()+10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(90, 6, "Customer's Name & Signature", 0, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, "For Muble Solutions PVT LTD", 0, 0, 'R', 1);

$pdf->SetY($pdf->GetY()+10);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(185, 6, "Authorised Signatory", 0, 0, 'R', 1);

$file_name = substr(mt_rand().mt_rand().mt_rand(),4,6)."_".date("H_i_s")."_DocReceipt.pdf";
$pdf->Output("uploads/trackReceipts/".$file_name,"F");
echo $file_name;