<?php
include_once($BASEPATH . "preTallyClass/AttestationClass.php");
$AttObj = new AttestationClass();
echo $AST_Id = $AttObj->getValue('attestation_state', 'AST_CS', ' WHERE AST_Id = '.$REQUEST['AST_Id']);