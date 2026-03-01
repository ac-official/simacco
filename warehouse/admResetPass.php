<?php
require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "includes/_define.php");    
require_once($BASEPATH . "smtp/smtpMail.php");
$UserObj = new UserClass();

if($_REQUEST['newPassword'] == $_REQUEST['confrmPassword']) {
        $UserObj->US_Id=$_REQUEST['ResUS_Id'];	
        $UserObj->US_Password 	= md5($_REQUEST['newPassword']);	
	$response = $UserObj->admResetPassword();
	        
        $name=$UserObj->UserDetailArray['US_FName'].'&nbsp;'.$UserObj->UserDetailArray['US_LName'];    
        $mail->addAddress($UserObj->UserDetailArray['US_Email'],htmlspecialchars($name, ENT_QUOTES));
        $mail->Subject 	= 'PreTally,Password Reset';
        $mailContent 	= file_get_contents($BASEPATH . 'mailTemplate/admPwdresetMail.html');
        $find		= array("{path}","{user}", "{base_path}");
        $replace	= array(constant("BASE_PATH")."/mailTemplate/images", $name,constant("BASE_PATH"));

        $mailContent 	= str_replace($find, $replace, $mailContent);

        $mail->msgHTML($mailContent, dirname(__FILE__));
    if (!$mail->send()) {
            $msg= "Mailer Error: " . $mail->ErrorInfo;
    } else {
            $msg= "An email with instructions to choose a new password has been sent to you";
    }
    echo $response.$msg;
} else { echo 'Error!!, Please Re-Type Your Password Again.'; }
//print_r($_REQUEST);
?>