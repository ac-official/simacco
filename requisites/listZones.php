<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once($BASEPATH . "preTallyClass/ZoneClass.php");
$filter=' WHERE BZ.OF_Id='.$preTally_user_ofid.' ';
if($REQUEST['zname'])
    $filter.=" AND BZ.ZN_Name LIKE '%".$REQUEST['zname']."%'";
if($REQUEST['updated'])
    $filter.=" AND CONCAT(UA2.US_FName,' ',UA2.US_LName) LIKE '%".$REQUEST['updated']."%'";
if($REQUEST['created'])
    $filter.=" AND CONCAT(UA.US_FName,' ',UA.US_LName) LIKE '%".$REQUEST['created']."%'";
if($REQUEST['lcid']!='null' && $REQUEST['lcid']!='')
    $filter.=" AND FIND_IN_SET(".$REQUEST['lcid'].",BZ.LC_Id)";
if($REQUEST['status']!='null' && $REQUEST['status']!='' && $REQUEST['status']!='All')
    $filter.=" AND BZ.ZN_Status=".$REQUEST['status']." ";

$filter.=' ORDER BY ZN_Name';
$ZoneObj = new ZoneClass();
$ZoneObj->listZones($filter);
$ZoneObj->viewLocs(' WHERE OF_Id='.$preTally_user_ofid.' AND LC_Status != 5');
$Zone_Obj = $ZoneObj->ZoneArray;
$LocObj=$ZoneObj->LocArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>';
		  if($Zone_Obj) {
				$j = 1;
				foreach($Zone_Obj as $rw) {
                                    if(!$rw->LC_Id){
                                    $BranchNames = 'No Branch Selected';
                                    }else{
                                        $BranchNames ='';
                                        $LCIDArray=explode(",",$rw->LC_Id);                                        
                                        foreach($LCIDArray as $value){
                                            $BranchNames.= $LocObj[$value].', ';
                                        }
                                    }
                                        
					
					echo '<row id="'.$rw->ZN_Id.'">
						<userdata name="ZN_Name">'.$rw->ZN_Name.'</userdata>
						<userdata name="ZN_Branches">'.$rw->LC_Id.'</userdata>
                                               <userdata name="ZN_Status">'.$rw->ZN_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="ZN_Name">'.$rw->ZN_Name.'</cell>                                              
                                                <cell name="ZN_Branches">'.rtrim($BranchNames, ', ').'</cell>
                                                <cell name="CreatedUser">'.$rw->CreatedUser.'</cell>    
                                                <cell name="ModifiedUser">'.$rw->ModifiedUser.'</cell>';                                             
						if($rw->ZN_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                                                } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
        }
                                        echo '</row>';	
                                        $j++;
				}
                        }else{
                        echo '<row id="0"> 
                        <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                        </row>';
                        }

echo '</rows>';
?>
