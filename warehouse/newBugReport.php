<?php
require_once($BASEPATH . "preTallyClass/FeedbackClass.php");
$FeedbackObj = new FeedBackClass();

$Desc=$_REQUEST['BR_Desc'];
$FeedbackObj->RB_Data = array(
    'US_Id'     => $preTally_user_id,
    'OF_Id'     => $preTally_user_ofid,
    'BR_Subject'=> trim(htmlspecialchars($_REQUEST['BR_Subject'], ENT_QUOTES)),
    'BR_Desc' 	=> trim(htmlspecialchars($Desc, ENT_QUOTES)),
    'BR_Type'	=> htmlspecialchars($_REQUEST['BR_Type'], ENT_QUOTES),
    'BR_Date'   => date('Y-m-d H:i:s'),
    'BR_Status' =>1
);
echo $FeedbackObj->SendBugReport();        
?>