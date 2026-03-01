<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/TrackClass.php");
$InstnObj  = new TrackClass;
$InstnObj->listInstruction();
$Instructions = $InstnObj->instnArray;

$w = $REQUEST['w'];
$h = $REQUEST['h'];
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<items>
        <item type="settings" position="label-left" labelWidth="0" offsetLeft="0" offsetTop="0" />
        <item type="editor" label="" name="ATPInstruction" toolbar = "true"  iconsPath= "assets/editor/codebase/imgs/" validate="NotEmpty"  inputWidth = "'.($w-40).'" inputHeight="'.($h-200).'" value="'.$InstnObj->instnArray[0]->ATPI_Instruction.'" >
            <note style="padding-bottom: 0px;">General Instructions For Track Users</note>
        </item> 
        <item type="button" value="UPDATE" offsetLeft="'.($w-150).'"  position="label-left" name="manageATPInstruction" />
      </items>';
?>