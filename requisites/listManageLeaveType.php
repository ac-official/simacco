<?php 
include_once($BASEPATH."preTallyClass/LeaveClass.php");
$lvObj = new LeaveClass();
$lvObj->listManageLeaveType($preTally_user_ofid);
$lv_Obj = $lvObj->listManageLeaveTypeArray;
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
     echo '<rows>		
                    <head>
                   	<column width="50" type="ro" align="left" >SlNo</column>	 			 		
                        <column width="*" type="ro" align="left" >Leave Type Name</column>
                        <column width="*" type="ro" align="left" >Max Count</column>
                        <column width="0" type="ro" align="left" >LT_Status</column>
                        <column width="60" type="ro" align="center"><![CDATA[<select id="LMLeave" style="width:90%; font-size:8pt; font-family:Tahoma;"></select>]]></column>
                        
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
                        $j=1;
                        if($lv_Obj) {
				foreach($lv_Obj as $rw) {
					echo '<row id="'.$rw->LT_Id.'">	
                                            <userdata name="LT_Id">'.$rw->LT_Id.'</userdata>
                                            <userdata name="LT_Name">'.$rw->LT_Name.'</userdata>
                                            <userdata name="LT_MaxCount">'.$rw->LT_MaxCount.'</userdata>
                                            <userdata name="LT_LOP">'.$rw->LT_LOP.'</userdata>
                                            <userdata name="LT_Status">'.$rw->LT_Status.'</userdata>
                                            <userdata name="ES_Status">'.$rw->ES_Status.'</userdata>    
                                            <cell name="No">'.$j.'</cell>
                                            <cell name="LT_Name">'.$rw->LT_Name.'</cell>
                                            <cell name="LT_MaxCount">'.$rw->LT_MaxCount .'</cell>';
                                            if($rw->LT_Status==1) {
                                                $LT_Status="Approved";
                                            }
                                            else { 
                                                $LT_Status="Blocked";
                                            }
                                            echo'<cell>'.$LT_Status.'</cell>'; 
                                            if($rw->LT_Status==1){
                                                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$LT_Status.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                            }
                                            else{   
                                                echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$LT_Status.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                            }
                                            echo'</row>';
                                            $j++;
				}
			}
                        else {
                            echo '<row id="0">
                            <cell colspan="4"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
                            </row>';
                        }
		  
            echo '</rows>';

?>