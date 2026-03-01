<?php   
include_once($BASEPATH . "includes/_define.php");
include_once($BASEPATH . "smtp/smtpMail.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");
$UserObj= new UserClass();
$mail->addAddress( $_REQUEST['US_Email'],htmlspecialchars($_REQUEST['US_FName'], ENT_QUOTES));
$mail->Subject 	= 'PreTally,Password Reset';
$mailContent 	= file_get_contents($BASEPATH . 'mailTemplate/sndResetPwd.html');
$split=  explode("-",$_REQUEST['US_EMPID']);          
$UsrPwd=$UserObj->randomPassword();
$UserObj->changePassword($_REQUEST["US_ID"],$UsrPwd);
$find			= array("{path}", "{user}", "{user_name}", "{password}", "{base_path}");
$replace		= array(BASE_PATH ."/mailTemplate/images", $_REQUEST['US_FName'].'&nbsp;'.$_REQUEST['US_LName'], $_REQUEST['US_EMPID'], $UsrPwd, BASE_PATH);

$mailContent 	= str_replace($find, $replace, $mailContent);

$mail->msgHTML($mailContent, dirname(__FILE__));
if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
} else {
        echo "Welcome Mail Sent Successfully..";
}
?>