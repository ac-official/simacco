<?php
require_once($BASEPATH . "includes/_define.php");
require_once($BASEPATH . "smtp/smtpMail.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");

$GeneralObj = new GeneralClass();

function randomString($length = 8){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
$password = randomString();
$GeneralObj->Update(' users ',' US_Password="'.md5($passwod).'" ',' US_Id = '.$_REQUEST['US_Id']);

//$mail->addAddress($_REQUEST['US_Email'], $_REQUEST['US_Name']);
$mail->addAddress('arundev@mublesolutions.com', 'Anoopkumar');
$mail->Subject 	= 'PreTally, User Registration Mail';

$mailContent 	= file_get_contents('../mailTemplate/newUser.html');

$find			= array("{path}", "{user}", "{user_name}", "{password}", "{base_path}");
$replace		= array(constant("BASE_PATH")."/mailTemplate/images", $_REQUEST['US_Name'], $_REQUEST['US_Login'], $password, constant("BASE_PATH"));

$mailContent 	= str_replace($find, $replace, $mailContent);

$mail->msgHTML($mailContent, dirname(__FILE__));
if (!$mail->send()) {
	echo "Mailer Error: " . $mail->ErrorInfo;
} else {
	echo "Welcome Mail Sent Successfully..";
}
?>
