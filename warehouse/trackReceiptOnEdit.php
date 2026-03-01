<?php
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj  = new AttestationClass();
$receiptData = array();

function convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = ucfirst($dictionary[$number]);
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = ucfirst($dictionary[$tens]);
            if ($units) {
                $string .= $hyphen . ucfirst($dictionary[$units]);
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = ucfirst($dictionary[$hundreds]) . ' ' . ucfirst($dictionary[100]);
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . ucfirst($dictionary[$baseUnit]);
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = ucfirst($dictionary[$number]);
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}

if($_REQUEST['type'] == 1) {   // Cash Receipt
    
    $AttObj->getDetails('', ' AJ.AJ_Id, AJ.AJ_FName, AJ.AJ_ReceivedDate, ALC.ALC_Name,PL.PL_Name, SR.SR_Name, AJ.AJ_Mobile1, AJ.AJ_Landline, AJ.AJ_Email, AJ.AJ_TotalAmount, AJR.* ', 
             'attestation_job_details AS AJ 
              LEFT JOIN attestation_job_receipts AS AJR ON AJR.AJ_Id = AJ.AJ_Id              
              LEFT JOIN addr_locations AS ALC ON AJ.ALC_Id = ALC.ALC_Id 
              LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
              LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
              WHERE AJR.AJ_Id = '.$_REQUEST['AJ_Id'].' AND AJR.AJR_Id = '.$_REQUEST['AJR_Id']); 
   
    $candidateDetails   = (array) $AttObj->DataArray[0];
    
    $receiptData['ReceivedAmount']      = $candidateDetails['AJR_CRAmount'];
    $receiptData['TotalAmount']         = $candidateDetails['AJ_TotalAmount'];
    $receiptData['DocumentID']          = 'M- DR- '.str_pad($candidateDetails['AJR_DRId'], 6, '0', STR_PAD_LEFT);
    $receiptData['CashReceiptID']       = 'M- CR- '.str_pad($candidateDetails['AJR_CRId'], 6, '0', STR_PAD_LEFT);
    
} else if($_REQUEST['type'] == 2 || $_REQUEST['type'] == 3 ) {  // Document Receipt
    
    $AttObj->getDetails(' attestation_job_details AS AJ ', 'AJ.AJ_Email,AJ.AJ_FName,AJ.AJ_Mobile1,AJ.AJ_Landline,AJ.AJ_TotalAmount,AJR.AJR_DRId,AJ.AJ_ReceivedDate,ALC.ALC_Name,PL.PL_Name,SR.SR_Name ', 
               "LEFT JOIN attestation_job_receipts AS AJR ON AJR.AJ_Id = AJ.AJ_Id 
                LEFT JOIN addr_locations AS ALC ON AJ.ALC_Id = ALC.ALC_Id 
                LEFT JOIN addr_places AS PL ON AJ.PL_Id = PL.PL_Id 
                LEFT JOIN addr_streets AS SR ON AJ.SR_Id = SR.SR_Id 
                    WHERE AJ.AJ_Id = ".$_REQUEST['AJ_Id']." LIMIT 1"); 

    $candidateDetails   = (array) $AttObj->DataArray[0];
    
    $AttObj->getDetails(' attestation_job_documents AS AJD ', 'AJD.AJ_Id,AJD.AJD_Id,AJD.AJD_Year,AJD.AJD_Amount,AJD.LC_Id,AJD.AJD_CDate,AJD.AJD_Comment,AD.ADOC_Document,APS.APS_Title,LC.LC_Name ,CN.CN_Name,GROUP_CONCAT(APS1.APS_Title)', 
            "  LEFT JOIN attestation_documents AS AD ON AD.ADOC_Id=AJD.ADOC_Id 
                LEFT JOIN attestation_process_sub AS APS ON APS.APS_Id = AJD.APS_Id
                LEFT JOIN attestation_job_subprocess  AS AJS ON AJS.AJD_Id = AJD.AJD_Id
		LEFT JOIN attestation_process_sub  AS APS1 ON APS1.APS_Id = AJS.APS_Id
		LEFT JOIN countries AS CN ON CN.CN_Id = AJD.AJD_IssuingCNId
                LEFT JOIN locations AS LC ON LC.LC_Id = AJD.LC_Id
                    WHERE AJD.AJD_Status != 0 AND AJD.AJ_Id = ".$_REQUEST['AJ_Id']." GROUP BY AJD.AJD_Id"); 
    
    $DocObj             = $AttObj->DataArray;
    
    $InvObj = $AttObj->getRowDetails(' attestation_job_invoice_receipts ', '*', " WHERE AJ_Id = ".$_REQUEST['AJ_Id']); 

    $finalArray         = array();
    $totAmt             = 0;
    foreach($DocObj as $rw) {
        $docArray       = array();
        $totAmt         = $totAmt+$rw->AJD_Amount;
        $docArray[0]    = $rw->ADOC_Document.",".$rw->AJD_Year.",".$rw->APS_Title.",".$rw->CN_Name;
        $AttObj->getDetails(" attestation_job_subprocess AS AJS ", " APS.APS_Title ", " LEFT JOIN attestation_process_sub AS APS ON AJS.APS_Id = APS.APS_Id WHERE AJS.AJD_Id = ".$rw->AJD_Id." ORDER BY AJS.AJS_Order ");
        $ProcessObj     =  $AttObj->DataArray;
        $subProcess     = array();
        foreach($ProcessObj as $proc) {
           array_push($subProcess, $proc->APS_Title);
        }
        $docArray[1]    = implode(',', $subProcess);
        $docArray[2]    = $rw->AJD_Amount;
        array_push($finalArray, $docArray);
    }
    $receiptData['DocumentDetails']     = $finalArray;
    
    $receiptData['TotalAmount']         = $totAmt;
    $receiptData['DocumentID']          = 'M- DR- '.str_pad($candidateDetails['AJR_DRId'], 6, '0', STR_PAD_LEFT);
    $receiptData['InvoiceID']           = $InvObj['AJIR_InvoiceNo'];
    
    $receiptData['INVStatutory']        = $InvObj['AJIR_StatutoryAmt'];
    $receiptData['INVSubTotal']         = $InvObj['AJIR_SubTotal'];
    $receiptData['INVServiceTax']       = $InvObj['AJIR_ServiceTax'];
    $receiptData['INVTotalAmount']      = $InvObj['AJIR_Total'];
    $receiptData['INVTotalWords']       = convert_number_to_words($InvObj['AJIR_Total']).' Only';
    
} 
$receiptData['CashReceivedDate']    = date("d-m-Y", strtotime($candidateDetails['AJ_ReceivedDate']));
$receiptData['FirstName']           = $candidateDetails['AJ_FName'];
$receiptData['LastName']            = $candidateDetails['ALC_Name'];
$receiptData['Mobile']              = $candidateDetails['AJ_Mobile1'];
$receiptData['Landline']            = $candidateDetails['AJ_Landline'];
$receiptData['Email']               = $candidateDetails['AJ_Email'];
echo json_encode($receiptData);