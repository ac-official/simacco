<?php
include_once($BASEPATH . "preTallyClass/TrackClass.php");
$InstnObj  = new TrackClass;
$InstnObj->listInstruction();
$Instructions = $InstnObj->instnArray;
echo htmlspecialchars_decode($InstnObj->instnArray[0]->ATPI_Instruction);
?>