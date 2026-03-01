<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/LocationClass.php");
include_once($BASEPATH . "preTallyClass/AddressClass.php");
$LocationObj = new LocationClass();
$AddressObj = new AddressClass();
if($preTally_user_ofid != 1){
    $flds     =   '*';
    $filter   =   'WHERE OF_Id = '.$preTally_user_ofid.' AND LC_Status = 1 ORDER BY LC_Name ';
} else {
    $flds     = '*';
    $filter   = 'WHERE LC_Status != 5';
}
$LocationObj->viewLocations($flds,$filter);
$LC_Obj = $LocationObj->LocationArray;



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int">Branch</column>
			<column width="80" type="ch" align="center" sort="str"></column>
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
                            <call command="attachHeader">
				<param>,#text_filter_inc,#master_checkbox</param>
                            </call>
				
			</afterInit>

		  </head>';
		  if($LC_Obj) {
                        $j = 1;
                        foreach($LC_Obj as $rw) {

                            echo '<row id="'.$rw->LC_Id.'">
                               
                                <cell>'.$j.'</cell>
                                <cell name="LC_Name">'.$rw->LC_Name.'</cell>
                                <cell></cell>
                            </row>';
                            $j++;
                        }
                    }
		  
echo '</rows>';
?>