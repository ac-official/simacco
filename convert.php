<?php
error_reporting(0); 
include_once("includes/_define.php");
include_once("preTallyClass/fpdf.php");    
include_once("_conf.php");
include_once("preTallyClass/AttendanceClass.php");
define('FPDF_FONTPATH', 'font/');
/*if(!$_REQUEST['GS']){
die("Invalid Call");
}*/
$AttObj     = new AttendanceClass();
$stat=$AttObj->checkSalarySlip($_REQUEST['GS']);
$locate="uploads/salary_slips/".$stat;
if(!file_exists(BASE_PATH.'/'.$locate)){    

$AttObj->salarySlipPdfData($_REQUEST['GS']);
if(!empty($AttObj->salarySlipPdfDataArray)){
$Month=array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=> "November",'12'=>"December");
//Create new pdf file
$pdf=new FPDF();

//Open file
$pdf->Open();

//Disable automatic page break
$pdf->SetAutoPageBreak(false);

//Add first page
$pdf->AddPage();

//set initial y axis position per page
$y_axis_initial = 10;
$row_height = 6;
//print column titles for the actual page
$pdf->SetFont('Times', 'B', 11);
$pdf->SetY($y_axis_initial);
$pdf->SetX(10);
$pdf->Image('images/'.$AttObj->salarySlipPdfDataArray['OF_Logo'],10,10,-200);
$pdf->SetX(50);
$pdf->SetFillColor(255,254,255);
$pdf->SetFont('Times', '', 14);
$pdf->Cell(100,6,$AttObj->salarySlipPdfDataArray['OF_Name'], 0, 0, 'C', 1);
$pdf->SetY(30);
$pdf->SetFont('Times', 'B', 11);
$pdf->SetFillColor(180, 200, 255);
$pdf->Cell(180,6,"Salary Slip", 0, 0, 'C', 1);
$y_axis = $y_axis + $row_height;
$pdf->SetFillColor(232, 232, 232);


//initialize counter
$i = 0;
//Set maximum rows per page
$max = 25;

//Set Row Height
$pdf->SetY($y_axis_initial);
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 12);    
    $pdf->SetX(10);
    $pdf->SetY(40);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(90, 8, "EmpCode : ".$AttObj->salarySlipPdfDataArray['US_EMPID'], 0, 0, 'L', 1);
    $pdf->Cell(90, 8, "Date : ".date("d.M.Y"), 0, 0, 'R', 1);
    $pdf->SetY(48);
    $pdf->Cell(45, 8, "Name", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['US_FName']." ".$AttObj->salarySlipPdfDataArray['US_LName'], 0, 0, 'L', 1);
    $pdf->Cell(45, 8, "Working Days", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['ESR_TotWrkDays'], 0, 0, 'L', 1);    
    $pdf->SetY(56);
    $pdf->Cell(45, 8, "Designation",0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['DG_Name'], 0, 0, 'L', 1);
    $pdf->Cell(45, 8, "Days Present", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['ESR_PrsntDays'], 0, 0, 'L', 1);    
    $pdf->SetY(64);
    $pdf->Cell(45, 8, "Department", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['DP_Name'], 0, 0, 'L', 1);
    $pdf->Cell(45, 8, "Half Days", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['ESR_HlfDays'], 0, 0, 'L', 1);    
    $pdf->SetY(72);
    $pdf->Cell(45, 8, "Month&Year", 0, 0, 'L', 1);
    $pdf->Cell(45, 8,  ": ".$Month[$AttObj->salarySlipPdfDataArray['ESR_Month']].",".$AttObj->salarySlipPdfDataArray['ESR_Year'], 0, 0, 'L', 1);
    $pdf->Cell(45, 8, "LOP", 0, 0, 'L', 1);
    $pdf->Cell(45, 8, ": ".$AttObj->salarySlipPdfDataArray['ESR_LOPDays'], 0, 0, 'L', 1);    
    $pdf->SetFont('Arial', 'B', 11);	
    $pdf->SetY(82);
    $pdf->Cell(180,8,"Salary Details", 0, 0, 'C', 1);
    $pdf->SetFont('Arial', '', 10);

    $pdf->SetY(90);

    $pdf->Cell(45, 6, "Emoluments", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, "Amount", 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Deductions", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, "Amount", 1, 0, 'R', 1);
    $pdf->SetY(95);
    $pdf->Cell(45, 6, "Basic + DA ", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,  $AttObj->salarySlipPdfDataArray['US_BasicSal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Professional Tax", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,  $AttObj->salarySlipPdfDataArray['ESR_Proftax'], 1, 0, 'R', 1);

    $pdf->SetY(100);
    
    $pdf->Cell(45, 6, "Conveyance Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['US_ConveySal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "LWF", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,$AttObj->salarySlipPdfDataArray['US_DedLWF'] , 1, 0, 'R', 1);

    $pdf->SetY(106);
    
    $pdf->Cell(45, 6, "Medical Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,$AttObj->salarySlipPdfDataArray['US_MedSal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "EPF", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['US_DedEPF'], 1, 0, 'R', 1);

    $pdf->SetY(112);
   
    $pdf->Cell(45, 6, "CCA Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['US_CcaSal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "ESI", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,$AttObj->salarySlipPdfDataArray['US_DedESI'], 1, 0, 'R', 1);

$pdf->SetY(117);
   
    $pdf->Cell(45, 6, "Educational Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6,$AttObj->salarySlipPdfDataArray['US_EduSal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Loan", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['ESR_Loan'] ,1, 0, 'R', 1);

$pdf->SetY(123);
   
    $pdf->Cell(45, 6, "Other Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['ESR_Otherallowance'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Salary Advance", 1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['ESR_Salaryadvance'], 1, 0, 'R', 1);

$pdf->SetY(128);
   
    $pdf->Cell(45,6,"HRA Allowance", 1, 0, 'L', 1);
    $pdf->Cell(45,6, $AttObj->salarySlipPdfDataArray['US_HRASal'], 1, 0, 'R', 1);
    $pdf->Cell(45, 6, "TDS",1, 0, 'L', 1);
    $pdf->Cell(45, 6,  $AttObj->salarySlipPdfDataArray['ESR_SalTds']+$AttObj->salarySlipPdfDataArray['ESR_ProfTds'],1, 0, 'R', 1);
    
$pdf->SetY(133);
   
   $pdf->Cell(45, 6, "Adjustment Addition",1, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['ESR_AdjstmntAddition'],1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Adjustment Deduction",1, 0, 'L', 1);
    $pdf->Cell(45, 6,  $AttObj->salarySlipPdfDataArray['ESR_AdjstmntDeduction'],1, 0, 'R', 1);    
$pdf->SetY(138);

    $pdf->Cell(45,6,"",1, 0, 'L', 1);
    $pdf->Cell(45,6, "",1, 0, 'R', 1);
    $pdf->Cell(45, 6, "Leave Deduction",1, 0, 'L', 1);
    $pdf->Cell(45, 6,   $AttObj->salarySlipPdfDataArray['ESR_Lop'],1, 0, 'R', 1); 
    $pdf->SetFont('Arial', '', 10);   

$pdf->SetY(145);
$pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(45,6,"Gross Salary:", 0, 0, 'L', 1);
    $pdf->Cell(45,6,  $AttObj->salarySlipPdfDataArray['US_GrossSal'], 0, 0, 'R', 1);
    $pdf->Cell(45, 6, "Net Salary:", 0, 0, 'L', 1);
    $pdf->Cell(45, 6,   $AttObj->salarySlipPdfDataArray['ESR_TakeHomeSalary'], 0, 0, 'R', 1); 
    $pdf->SetFont('Arial', '', 10);
    
    $pdf->SetY(160);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(180,6,"Account Details", 0, 0, 'C', 1);
    $pdf->SetFont('Arial', '', 10);
   
    $pdf->SetY(168);

    $pdf->Cell(45, 6, "Account Number", 0, 0, 'L', 1);
    $pdf->Cell(45, 6, $AttObj->salarySlipPdfDataArray['US_AccNo'], 0, 0, 'L', 1);
    $pdf->Cell(45, 6, "", 0, 0, 'L', 1);
    $pdf->Cell(45, 6, "", 0, 0, 'L', 1);	
    
    $pdf->SetY(175);

    $pdf->Cell(45, 6, "Bank", 0, 0, 'L', 1);
    $pdf->Cell(45, 6,   $AttObj->salarySlipPdfDataArray['US_Bankname'], 0, 0, 'L', 1);
    $pdf->Cell(45, 6, "Branch", 0, 0, 'L', 1);
    $pdf->Cell(45, 6,   $AttObj->salarySlipPdfDataArray['US_BankBranch'], 0, 0, 'L', 1);	

    $pdf->SetY(210);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial', 'I', 8);    
    $pdf->Cell(180, 6, "This is a computer generated report and doesn't require any signature", 0, 0, 'C', 1);
    //Go to next row
    $y_axis = $y_axis + $row_height;
    $i = $i + 1;


$file_name=$_REQUEST["GS"]."_".date("H_i_s")."SalSlip.pdf";
$AttObj->updateSlipName($_REQUEST["GS"],$file_name);
//Create file
$pdf->Output("uploads/salary_slips/".$file_name,"F");
$pdf->Output($file_name,"D");
}
}else{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($locate));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($locate));
    readfile($locate);
    //exit;

    
}
//header("location:index.php");*/
?>