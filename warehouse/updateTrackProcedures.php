<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
$AttObj->Data = array (
        htmlspecialchars(trim($_REQUEST['TPUpdateKey']), ENT_QUOTES) => htmlspecialchars(trim($_REQUEST['TPUpdateVal']), ENT_QUOTES)
    );
echo $AttObj->updateRecords('track_procedure', 'TP_Id = '.$_REQUEST['TPID']);
?>
