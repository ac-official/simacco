<?php
require_once($BASEPATH . "includes/_define.php");
require_once($BASEPATH . "smtp/smtpMail.php");
include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$mailerr=0;
$mailsucc=0;
$AttObj     = new AttendanceClass();
$rowId=$_REQUEST['rowId'];
$AttObj->getMailId($rowId);
$AttMailObj=$AttObj->getMailIdArray;
foreach($AttMailObj as $rw){
    $mail->clearAddresses();
    $password = token($rw->ESR_Id);
    $mail->addAddress($rw->US_Email,$rw->US_FName);
                $mail->Subject 	= 'Simacco, Salary Report Mail';
                $mailContent 	= file_get_contents($BASEPATH.'mailTemplate/salarySlip.html');
                $find	= array("{path}", "{user}", "{password}", "{base_path}");
                $replace= array(constant("BASE_PATH")."/mailTemplate/images",$rw->US_FName." ".$rw->US_LName, constant("BASE_PATH").'/convert.php?GS='.$password, constant("BASE_PATH"));
                $mailContent 	= str_replace($find, $replace, $mailContent);
                $mail->msgHTML($mailContent, dirname(__FILE__));
                if (!$mail->send()){
                   $mailerr=1;
                } else {
                    $mailsucc=1;
                }
    $AttObj->mail_Data=array(
               'ESR_Id'=>$rw->ESR_Id,
               'SSS_Token'=>$password
    );
    $AttObj->saveSalaryMailDeatils();
    
}

$filter=str_replace(',', ' OR AND', $rowId);
$filter=str_replace('AND', 'ESR_Id=', $filter);
$AttObj->changeMailStatus($filter);
if($mailerr==1){
   echo "Mailer Error: " . $mail->ErrorInfo;
}else if($mailsucc==1){
    echo "mail sent successfully";
}
function token($ESR_Id){
    $token=date("H:i:s").$ESR_Id;
    $token=md5($token);
    return $token;
}
?>