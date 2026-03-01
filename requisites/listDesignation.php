<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/DesignationClass.php");

if($preTally_user_ofid == 1) {
    $flds='DG.DG_Id,DG.OF_Id, DG.DG_Name,DG_Comments,DG_Status, OF.OF_Name';
    $tbls='as DG, offices as OF';
    $filter = 'DG.OF_Id=OF.OF_Id';
}else {
    $flds='DG_Id,OF_Id, DG_Name, DG_Comments,DG_Status';
    $filter = "OF_Id = $preTally_user_ofid";
}
$DesignationObj = new DesignationClass();
$DesignationObj->viewDesignations($flds,$tbls,'WHERE  '.$filter.' ORDER BY DG_Name');
$DG_Obj = $DesignationObj->DesignationArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">designations</userdata>
		<userdata name="db_primary">DG_Id</userdata>
		<userdata name="db_date">DG_MDate</userdata>
		<userdata name="db_status">DG_Status</userdata>
		<head>
			<column width="50" type="ro" align="center" > SlNo </column>
			<column width="*" type="ro" align="left" > Designation </column>';
                        if($preTally_user_ofid == 1) { echo '<column width="*" type="ro" align="left"  id="selectFilter"> Company </column>'; }
			echo '<column width="0" type="ro" align="center" >DGStatus </column>';
                        echo '<column width="60" type="ro" align="center">Status</column>
	
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
                        if($DG_Obj) {
				$j = 1;
				foreach($DG_Obj as $rw) {
					
					echo '<row id="'.$rw->DG_Id.'">
						<userdata name="DG_Name">'.$rw->DG_Name.'</userdata>
                                                <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>
						<userdata name="DG_Comments">'.$rw->DG_Comments.'</userdata>
						<userdata name="DG_Status">'.$rw->DG_Status.'</userdata>
						<cell>'.$j.'</cell>
						<cell name="DG_Name">'.$rw->DG_Name.'</cell>';
                                                if($preTally_user_ofid == 1) { echo '<cell name="DG_Name">'.$rw->OF_Name.'</cell>'; }
                                                if($rw->DG_Status == '0') { 
                                                        $DGStatus="Blocked";
                                                }else{
                                                        $DGStatus="Approved";
                                                }
                                                echo '<cell>'.$DGStatus.'</cell>';
						if($rw->DG_Status == '0') { 
                                                        echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Blocked\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                } else {
                                                        echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                                }
                                        echo '</row>';
					$j++;
				}
			}
		  
echo '</rows>';
?>