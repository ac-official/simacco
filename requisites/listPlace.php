<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/AddressClass.php");

$PlaceObj = new AddressClass();
$PlaceObj->listPlaces();
$PL_Obj = $PlaceObj->PlaceArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <head>
            <column width="40" type="ro" align="center" sort="int"> #</column>
            <column width="*" type="ro" align="left" sort="str"> Place </column>
            <column width="*" type="ro" align="left" sort="str"> City </column>            
	    <column width="60" type="ro" align="center" sort="na">Status</column>
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
				 
            </beforeInit> 
            <afterInit>
            <call command="attachHeader">
                <param>,#text_filter,#select_filter_strict,</param>
            </call>
            
            </afterInit>
            </head>';
            if($PL_Obj){ $j=1;
                        foreach ($PL_Obj as $rw) {
                                echo '<row id="'.$rw->AP_Id.'">
                                 <userdata name="AP_Id">'.$rw->AP_Id.'</userdata>    
                                <userdata name="AP_Name">'.$rw->AP_Name.'</userdata>
                                <userdata name="CT_Id">'.$rw->CT_Id.'</userdata>                              
                                <userdata name="CT_Name">'.$PlaceObj->getCityName($rw->CT_Id).'</userdata>        
				<userdata name="AP_Status">'.$rw->AP_Status.'</userdata>
                                <cell>'.$j.'</cell>
                                <cell name="AP_Name">'.$rw->AP_Name.'</cell>				
                                <cell name="CT_Name">'.$PlaceObj->getCityName($rw->CT_Id).'</cell>'; 
                                if($rw->AP_Status == '0') { 
                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
				} else {
                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                                }
                                echo '</row>';
				$j++;
				}
                                      
            }
echo '</rows>';

?>