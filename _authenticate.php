<?php
require_once("preTallyClass/UserClass.php");
require_once ('includes/BrowserDetect.php');

if(isset($_POST['submt']) && ($_POST['submt'] == 1)) { 
    $UserObj = new UserClass();
    $browserDetect = new BrowserDetect;	
    $systemType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
    $scriptVersion = $browserDetect->getScriptVersion();

    $UserObj->AN_IPData       = array(
//       'US_Id'            => $preTally_user_id,
        'AN_IP'            => gethostbyname($_SERVER['REMOTE_ADDR']), //192.168.1.52
//        'AN_Time'          => date('Y-m-d H:i:s'), //2015-06-22 12:32:34
        'AN_SystemType'    => $systemType, //Computer / Tablet/ Mobile
        'AN_Browser'       => htmlentities($_SERVER['HTTP_USER_AGENT']), //Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:38.0)
        'AN_ScriptVersion' => $scriptVersion, //2.8.14
        'AN_LoginType'     => 'L', //Login
//        'AN_CDate'         => date('Y-m-d')
    );
    
    $UserObj->US_Login      = htmlspecialchars($_REQUEST['username'], ENT_QUOTES);
    $UserObj->US_Password   = md5($_REQUEST['password']);
    $UserObj->US_Remember   = $_REQUEST['remember'];  
    $msg = $UserObj->signInUser(0);

//require_once ('includes/BrowserDetect.php');
//$browserDetect = new BrowserDetect;
//$browserType = ($browserDetect->isMobile() ? ($browserDetect->isTablet() ? 'tablet' : 'phone') : 'computer');
//$scriptVersion = $browserDetect->getScriptVersion();
//echo "<br>".htmlentities($_SERVER['HTTP_USER_AGENT']);
//echo "<br>".gethostbyaddr($_SERVER['REMOTE_ADDR']);
//echo "<br>".gethostbyname($_SERVER['REMOTE_ADDR']);
//echo "<br>".$preTally_user_id;
//die();
}
if(isset($_POST['pwd_reset']) && ($_POST['pwd_reset'] == 1)) {
require_once("includes/_define.php");    
require_once("smtp/smtpMail.php");
$UserPwdObj = new UserClass();
$token=md5(date('Y-m-d').$_REQUEST['reset_email'].date('Y-m-d H:i:s'));
$UserPwdObj->US_PwdData       = array(
        'US_Email'             => trim($_REQUEST['reset_email']),
        'RQ_Token'             => $token,
        'RQ_Status'            => 0, 
        'RQ_Time'              => date('Y-m-d H:i:s'));
    
$response=$UserPwdObj->resetPassword();
if($response!="fail"){
$name=$UserPwdObj->UserDetails['US_FName'].'&nbsp;'.$UserPwdObj->UserDetails['US_LName'];    
$mail->addAddress($UserPwdObj->UserDetails['US_Email'],htmlspecialchars($name, ENT_QUOTES));
$mail->Subject 	= 'PreTally,Password Recovery';
$mailContent 	= file_get_contents('mailTemplate/pwdReset.html');
$find		= array("{path}", "{user}", "{user_name}", "{url}", "{base_path}");
$replace	= array(constant("BASE_PATH")."/mailTemplate/images", $name, $_REQUEST['US_EMPID'], constant("BASE_PATH").'/resetPwd.php?auth='.$token, constant("BASE_PATH"));

$mailContent 	= str_replace($find, $replace, $mailContent);

$mail->msgHTML($mailContent, dirname(__FILE__));
    if (!$mail->send()) {
            $msg= "Mailer Error: " . $mail->ErrorInfo;
    } else {
            $msg= "An email with instructions to choose a new password has been sent to you";
    }
}
 else {
     $msg="No User Exists";
}
}   
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="<?php echo Settings::getPublic('site_charset'); ?>" />
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
<title><?php echo Settings::getPublic('site_title'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="AugMob Solutions" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="assets/form/codebase/skins/dhtmlxform_dhx_skyblue.css">
<script src="scripts/jquery.js"></script>
<script src="scripts/jquery-ui.js"></script>
<script src="assets/common/codebase/dhtmlxcommon.js"></script>
<script src="assets/form/codebase/dhtmlxform.js"></script>

<link rel="stylesheet" type="text/css" href="assets/combo/codebase/skins/dhtmlxcombo_dhx_skyblue.css">
<script  src="assets/combo/codebase/dhtmlxcombo.js"></script>
<script  src="assets/form/codebase/ext/dhtmlxform_item_combo.js"></script>
<!-- <script  src="assets/combo/codebase/ext/dhtmlxcombo_extra.js"></script> -->

<script type="text/javascript" src="assets/message/codebase/dhtmlxmessage.js"></script>
<link rel="stylesheet" type="text/css" href="assets/message/codebase/skins/dhtmlxmessage_dhx_skyblue.css">

<link rel="stylesheet" type="text/css" href="assets/captcha/jquery.realperson.css"> 
<script type="text/javascript" src="assets/captcha/jquery.realperson.js"></script>

<script src="scripts/cookie.js"></script>
<link rel="stylesheet" type="text/css" href="css/authenticate.style.css"> 

<style type="text/css">

#loadingClass{
    position: absolute;
    background-color: white;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    opacity: 0.75;
    z-index: 10000;
    background-image: url("assets/tabbar/codebase/imgs/dhxtabbar_skyblue/dhxtabbar_cell_progress.gif");
    background-position: center center;
    background-repeat: no-repeat;
}
.preloader_img
{
    background-image: url("images/loading_hourglass.gif");
    background-repeat: no-repeat;
    background-color: #FFFFFF;
    position: fixed; /* forces the element to stay fixed in relation to the viewport */
    top: 50%; /* sets the top of the image 50% of the page height */
    left: 50%; /* sets the left side of the image 50% across the page */
    margin-left: -100px; /* moves the image half of its own width to the left-side of the page */
    margin-top: -93px; /* moves the image half its height 'up' the page */
    box-shadow: 0.5em 0.5em 0.7em #333; /* to give the illusion of 'floating'*/
    border-radius: 1em;    
    width: 92px;
    height: 92px;
    z-index: 99999;
    
}
.overlay{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10;
  background-color: rgba(0,0,0,0.1); /*dim the background*/
}
</style>
</head>
<body>        
    <div class="preloader">
        <div class="overlay"></div>
        <div class="preloader_img"></div>
    </div>
     <div class="container" >
    <form id="loginForm" method="POST" enctype="multipart/form-data"><div id="signInForm"></div></form>
    <form id="resetPass" method="POST" enctype="multipart/form-data"><div id="resetForm"  style="display:none;"></div></form>
</div>   
   <!--<h1 align="center" style="color: #E06666;">Pretally is under upgrade !!!</h1>
    <p align="center" style="color: #0096eb; width: 750px; margin: auto;">
Due to the upgrade and maintenance process, 
our web site will not be available at the moment,<br/> 
We apologize for the inconvenience and the site will be fully accessible on <font style="color: #FF0000;">January 1<sup>st</sup> 2015</font>. </br>
If you have any questions or comments or messages please send an email to <font style="color: #FF0000;">contact@pretally.in</font>. 
    </p>-->
<!--<h1 align="center" style="color: #E06666;">Simacco is under upgrade!!!</h1>
    <p align="center" style="color: #0096eb; width: 750px; margin: auto;">
Due to the upgrade and maintenance process, 
our web site will not be available at the moment.<br/> 
We apologize for the inconvenience and the site will be fully accessible soon.-->
<!--<h2 align="center" style="color: #E06666;">Simacco User Id Updated!!!</h2>
<p align="center" style="color: #0C5BB1; width: 750px; margin: auto;">Sign In with new User Id on 1st April 2022 onwards. For further details contact HR department.</p>-->
</body>
<script type="text/javascript" src="scripts/preTally.Authenticate.js"></script>
<script language="javascript">    
    var msg = '<?php echo $msg; ?>';
    if(msg){
        dhtmlx.message({text: msg, expire: 10000});
    }
    $(document).ready(function(){        
        setTimeout(function(){
            $(".preloader").hide();
        },2000); 
        $(':input').on('focus',function(){
            //$(this).attr('autocomplete', 'off');
            //$(this).attr('autocorrect', 'off');
            //$(this).attr('autocapitalize', 'off');
            
            //$(this).val('');

            //var type = $(this).attr( 'type');
            //$(this).attr( 'type', '_' + type );
            //$(this).attr( 'type', type );
        });
        //$('input').removeAttr('autocomplete');
        
        $(':input').attr('autocomplete', 'off');
        $(':input').attr('autocorrect', 'off');
        $(':input').attr('autocapitalize', 'off');
        
    });
    $(document).load(function(){
       $(".preloader").show();     
    });
    $("#loginForm").submit(function(){
       $(".preloader").show();      
    });
    
</script>
</html>
