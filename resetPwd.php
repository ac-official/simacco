<?php
include_once("_conf.php");
require_once("preTallyClass/ResetPwdClass.php");
if(isset($_REQUEST['pwd_reset'])&&$_REQUEST['pwd_reset']==1){
    if($_REQUEST['new_Pass']==$_REQUEST['confirm_Pass']){
    $ResetPwdObj= new ResetPwdClass(); 
    $ResetPwdObj->rec_Email=$_REQUEST['pwd_email'];
    $ResetPwdObj->rec_ID=$_REQUEST['pwd_id'];
    $ResetPwdObj->rec_Password=$_REQUEST['confirm_pass'];
    $resp=$ResetPwdObj->resetPassword();
    if($resp=="PwdChange_Success"){  
        $msg="Password Changed Successfully";
        header("location:index.php");
    }
    }
    else{
        $msg="Password Mismatch";
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
<meta charset="UTF-8" />
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
<title>PreTally</title>
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

<?php if($_REQUEST['auth'])
{ 
$PassCheck = new ResetPwdClass();
$token=$_REQUEST['auth'];
$status=$PassCheck->checkPassToken($token);
if($status=="token_error"){
    $alert= "Invalid Request";    
}
elseif($status=="token_expired"){
    $alert= "Token Expired Plz do the recovery process once more..";    
}
else{
    $rec_id=$PassCheck->rec_ID;
    $email_rec=md5($PassCheck->rec_Mail);
    ?>
<script type="text/javascript" src="scripts/preTally.ResetPassword.js"></script>
<?php
}
} 
 else {
header("location:index.php");     
}
?>
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
         <form id="resetPassForm" method="POST" enctype="multipart/form-data">
             <div id="resetForm"></div>
             <input type="hidden" name="pwd_email" value="<?php echo $email_rec;?>">
              <input type="hidden" name="pwd_id" value="<?php echo $rec_id;?>">
         </form>    
</div>   
</body>
<script language="javascript">    
    var alert = '<?php echo $alert; ?>';
    var msg = '<?php echo $msg; ?>';
    if(alert){
        dhtmlx.alert({title:"Warning!",text: alert, callback: function() {window.location="index.php";}});        
    }
    $(document).ready(function(){        
      setTimeout(function(){
          $(".preloader").hide();
      },2000);      
    });
    $(document).load(function(){
       $(".preloader").show();     
    });
    $("#resetPassForm").submit(function(){
       $(".preloader").show();      
    });
    
</script>
</html>
