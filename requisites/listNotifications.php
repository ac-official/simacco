<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php");



$DescObj = new NotificationClass();
$DescObj->viewDescriptions(' WHERE ( DS_Status !=1 && DS_Status != 2 ) ORDER BY DS_Description');
$DS_Obj = $DescObj->NotfArray;


$DescObj->viewItems('IT_Id,IT_Name,IT_CDate',' WHERE ( IT_Status !=1 && IT_Status != 2 ) ORDER BY IT_Name');
$IT_Obj = $DescObj->NotfArray;

$arrayN= array_merge($DS_Obj,$IT_Obj); //print_r($arrayN);

function date_compare($a, $b)
{
    $t1 = strtotime($a['DS_CDate']);
    $t2 = strtotime($b['IT_CDate']);
    return $t1 - $t2;
}
usort($arrayN, "date_compare");
        
//foreach ($arrayN as $value) {
//    $NArray = 
//}

//print_r($arrayN); 

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">departments</userdata>
		<userdata name="db_primary">DS_Id</userdata>
                <head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="str"> Description </column>
			<column width="60" type="ro" align="center" sort="na">	Status </column>
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
					<param>,#text_filter,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($arrayN) {
				$j = 1;
				foreach($arrayN as $rw) {
					
					echo '<row id="'.$rw->DS_Id.'">
                                            <userdata name="DS_Description">'.$rw->DS_Description.'</userdata>
                                            <userdata name="DS_Status">'.$rw->DS_Status.'</userdata>
                                            <cell>'.$j.'</cell>
                                            <cell name="DS_Description">'.$rw->DS_Description.'</cell>';
                                            if($rw->DS_Status == '0') { 
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