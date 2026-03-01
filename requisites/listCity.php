<?php
include_once($BASEPATH . "preTallyClass/CityClass.php");

if (!isset($_GET["posStart"]))
		$_GET["posStart"] = 0;
	if (!isset($_GET["count"]))
		$_GET["count"] = 50;

$CityObj = new CityClass();
$count=$CityObj->viewCitiesGrid($_GET["posStart"],$_GET["count"]);
$CT_Obj = $CityObj->CityArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows  total_count="2000" pos="'.$_GET["posStart"].'">';
           
            if($CT_Obj){ $j=$_GET["posStart"]+1;
                        foreach ($CT_Obj as $rw) {
                                echo '<row id="'.$rw->CT_Id.'">
                                <userdata name="CT_Name">'.$rw->CT_Name.'</userdata>
                                <userdata name="ST_Id">'.$rw->ST_Id.'</userdata>
                                <userdata name="CN_Id">'.$rw->CN_Id.'</userdata>
				<userdata name="CT_Status">'.$rw->CT_Status.'</userdata>
                                <cell>'.$j.'</cell>
                                <cell name="CT_Name">'.$rw->CT_Name.'</cell>
				<cell name="ST_Name">'.$rw->ST_Name.'</cell>
                                <cell name="CT_Name">'.$rw->CN_Name.'</cell>'; 
                                if($rw->CT_Status == '0') { 
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