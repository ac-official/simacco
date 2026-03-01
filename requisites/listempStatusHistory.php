<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/UserClass.php");

$UserObj = new UserClass();
$UserObj->getEmpStatusGrid($REQUEST['usid'],$REQUEST['doj'],$REQUEST['esid']);
$Stat_Obj = $UserObj->StatusArray;
//var_dump($Stat_Obj);
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>				
		<head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Status </column>                        
			<column width="*" type="ro" align="center" sort="str">Effective From</column>
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
		  if($Stat_Obj) {
				$j = 1;
				foreach($Stat_Obj as $rw) {
					if($rw['ES_Name']){
					echo '<row id="'.$rw['ESH_Id'].'">
						<cell>'.$j.'</cell>
                                                <cell>'.$rw['ES_Name'].'</cell>     
						<cell>'.date("d/m/Y", strtotime($rw['ESH_Date'])).'</cell> ';                                              
                                        	
                                        echo '</row>';	                                        
                                        $j++;
                                        }
				}
                        }else{
                            echo '<row id="0"> 
                        <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                        }

echo '</rows>';
?>
