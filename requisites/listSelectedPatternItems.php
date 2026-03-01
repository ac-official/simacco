<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/SubheadPatternClass.php");

$PatternObj = new SubheadPatternClass();
$PatternObj->getPatternList($REQUEST['SH_Id']);
$PatternList_Obj = $PatternObj->PatternArray;

$old = array('"', "[", "]");
$new   = array("", "", "");
$PatternList = str_replace($old, $new, $PatternList_Obj[0]->PL_PatternMap);

$PatternObj->listSHPatterns($PatternList);
$Pattern_Obj = $PatternObj->PatternArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>
		<head>
			<column width="60" type="ro" align="center" sort="na"> SL.No </column>
			<column width="*" type="ro" align="left" sort="na"> Pattern Items </column>
			<settings>
				<colwidth>px</colwidth>
			</settings>
			<beforeInit> 
                                <call command="setSkin">
					<param>dhx_skyblue</param>
				</call> 
				<call command="setImagePath">
					<param>assets/grid/codebase/imgs/</param>
				</call> 
				<call command="enableSmartRendering">
					<param>false</param>
				</call> 
                        </beforeInit> 
			<afterInit>				

			</afterInit>

		  </head>';
		  if($Pattern_Obj) {
                            $j = 1;
                            foreach($Pattern_Obj as $rw) {
                                    echo '<row id="'.$rw->PI_Id.'">
                                            <cell>'.$j.'</cell>
                                            <cell name="PI_Title">'.$rw->PI_Title.'</cell> ';                                              
                                    echo '</row>';	
                                    $j++;
                            }
                    }
		  
echo '</rows>';
?>