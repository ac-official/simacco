<?php
error_reporting('E_ALL ^ E_NOTICE');
include_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "smtp/smtpMail.php");
$UserPwdObj = new UserClass();
echo $token=md5(date('Y-m-d').$_REQUEST['reset_email'].date('Y-m-d H:i:s'));
$UserPwdObj->US_PwdData       = array(
        'US_Email'             => trim($_REQUEST['email']),
        'RQ_Token'             => $token,
        'RQ_Status'            => 0, 
        'RQ_Time'              => date('Y-m-d H:i:s'));
$response=$UserPwdObj->resetPassword();
if($response!="fail"){
$mail->addAddress($UserObj->US_Email,htmlspecialchars($UserObj->US_Fname, ENT_QUOTES));
$mail->Subject 	= 'PreTally,Password Recovery';
$mailContent 	= file_get_contents($BASEPATH . 'mailTemplate/pwdReset.html');
$split=  explode("-",$_REQUEST['US_EMPID']);          
$UsrPwd=$split[1];
$find			= array("{path}", "{user}", "{user_name}", "{password}", "{base_path}");
$replace		= array(constant("BASE_PATH")."/mailTemplate/images", $_REQUEST['US_FName'].'&nbsp;'.$_REQUEST['US_LName'], $_REQUEST['US_EMPID'], $UsrPwd, constant("BASE_PATH"));

$mailContent 	= str_replace($find, $replace, $mailContent);

$mail->msgHTML($mailContent, dirname(__FILE__));
    if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
            echo "Reset Mail Sent Successfully..";
    }
}
 else {
     echo "No User Exists";
}
  ?>