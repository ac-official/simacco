<?php    
require_once($BASEPATH . "preTallyClass/FeedbackClass.php");
$FeedbackObj = new FeedBackClass();
$BR_Id=$_REQUEST["BR_Id"];
$comment=$_REQUEST["BR_Comment"];
//$comment = preg_replace("/[\n\r]/"," ",$comment);  
//$comment = preg_replace('!\s+!', ' ', $comment);
$BR_Status=$_REQUEST["BR_Status"];
echo $FeedbackObj->UpdateComment($BR_Id,$BR_Status);

$FeedbackObj->InsertData = array(
            'BR_Id'             => $BR_Id,
            'BR_ModifiedUSId'   => $preTally_user_id,
            'BR_Status'         => $_REQUEST["BR_Status"],
            'BR_modifiedDate'   => date('Y-m-d'),
            'BR_AssignedUsId'   => $_REQUEST["BR_AssignedUsId"]?$_REQUEST["BR_AssignedUsId"]:0,
            'BR_Comment'        => trim(htmlspecialchars($comment, ENT_QUOTES)),
        );
$FeedbackObj->insertReturnId(bug_report_details);
?>