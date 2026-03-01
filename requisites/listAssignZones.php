<?php

if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
$filter = "AND US.OF_Id=" . $preTally_user_ofid;
$having = "";
if ($REQUEST['uname'] != "" && $REQUEST['uname'] != "null")
    $filter .= " AND CONCAT(US.US_FName,' ',US.US_LName)  LIKE '%" . $REQUEST['uname'] . "%' ";
if ($REQUEST['lcid'] != "" && $REQUEST['lcid'] != "null")
    $filter .= " AND US.LC_Id =" . $REQUEST['lcid'] . " ";
if ($REQUEST['znid'] != 'null' && $REQUEST['znid'] != '')
    $filter .= " AND FIND_IN_SET (" . $REQUEST['znid'] . ",US.US_Zones) ";
if ($REQUEST['updby'] != "" && $REQUEST['updby'] != "null")
    $filter .= " AND CONCAT(US2.US_FName,' ',US2.US_LName) LIKE '%" . $REQUEST['updby'] . "%' ";
$ZoneObj = new ZoneClass();
$ZoneObj->getZonalManagers($filter);
$ZoneObj->viewZoneArray(" WHERE OF_Id =" . $preTally_user_ofid);
$ZoneNameArray = $ZoneObj->ZonArray;
$Zone_Obj = $ZoneObj->UserArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>';
if ($Zone_Obj) {
    $j = 1;
    foreach ($Zone_Obj as $rw) {
        if (!$rw->US_Zones) {
            $ZoneNames = 'No Zone Selected';
        } else {
            $ZoneNames = '';
            $ZNIDArray = explode(",", $rw->US_Zones);
            foreach ($ZNIDArray as $value) {
                $ZoneNames .= $ZoneNameArray[$value] . ', ';
            }
        }
        echo '<row id="' . $rw->US_Id . '">  
                                            <userdata name="US_Id">' . $rw->US_Id . '</userdata>  	
                                            <userdata name="US_Name">' . $rw->NAME . '</userdata>    
                                            <userdata name="US_Zones">' . $rw->US_Zones . '</userdata>        
						<cell>' . $j . '</cell>
						<cell name="ZN_Name">' . $rw->NAME . '</cell>                                              
                                                <cell name="LC_Name">' . $rw->LC_Name . '</cell>   
                                                <cell name="ZN_Name">' . rtrim($ZoneNames, ', ') . '</cell>                                                    
                                                <cell name="ModifiedUser">' . $rw->UPDNAME . '</cell>       
                                                <cell name="Edit_Zone"><![CDATA[<img src="images/icon/pencil.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
        echo '</row>';
        $j++;
    }
} else {
    echo '<row id="0"> 
                            <cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
}

echo '</rows>';
?>
