<?php
/**
 * user  
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/UserClass.php");

$UserObj = new UserClass();
$desigDet       = $UserObj->listDesigUser($REQUEST['usid'],$REQUEST['desgid'],$REQUEST['mdate']);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>				
		<head>
			<column width="40" type="ro" align="center" sort="int"> SlNo </column>
			<column width="185" type="ro" align="left" sort="str"> Designation </column>                        
			<column width="75" type="ro" align="center" sort="str">From Date</column>
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
		  </head>';
		  	if($desigDet) {
				$j = 1;
				$desigDetr = array_reverse($desigDet);
				foreach ($desigDetr AS $rw) {
					echo '<row id="Dsg'.$j.'">
							<cell>'.$j.'</cell><cell>'.htmlentities($rw['title']).'</cell>     
							<cell>'.$rw['from_date'].'</cell></row>';	                                        
	                $j++;
				}				
            }else{
                echo '<row id="0"> 
            	<cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
            	</row>';
            }

echo '</rows>';
?>
