<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/DepartmentClass.php");

$DepartmentObj = new DepartmentClass();
if($preTally_user_ofid == 1) {
    $flds='DP.DP_Id, DP.OF_Id, DP.DP_Name, DP_Comments,DP_Status, OF.OF_Name , DH.DH_Weekends';
    $tbls='as DP, offices as OF ,department_holidays AS DH';
    $filter = 'DP.OF_Id=OF.OF_Id';
}else {
    $flds='DP.DP_Id, DP.OF_Id, DP.DP_Name, DP.DP_Comments,DP.DP_Status,DH.DH_Weekends';
    $tbls='as DP,department_holidays AS DH';
    $filter = "DH.DP_Id=DP.DP_Id AND DP.OF_Id = $preTally_user_ofid";
}
echo $DepartmentObj->viewDepartments($flds,$tbls,'WHERE  '.$filter.' ORDER BY DP_Name');
$DP_Obj = $DepartmentObj->DepartmentArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na"> Department </column>';
                        if($preTally_user_ofid==1){
                            echo'<column width="*" type="ro" align="center" sort="na">Company</column>';
                        }
			echo '<column width="200" type="ro" align="center" sort="na">Days</column>
                            <column width="0" type="ro" align="center" sort="na">DeptStatus</column>
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
				<call command="enableSmartRendering">
					<param>false</param>
				</call> 
            </beforeInit> 
			

		  </head>';
                    if($DP_Obj) {
				$j = 1;
				foreach($DP_Obj as $rw) {
                                        $dayarray = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
					$weekarray=  explode(",", $rw->DH_Weekends);
                                        $weekdays="";
                                        foreach($weekarray as $wk){
                                            $weekdays.=$dayarray[$wk-1].",";
                                        }
					echo '<row id="'.$rw->DP_Id.'">
                                                <userdata name="OF_Id">'.$rw->OF_Id.'</userdata>
						<userdata name="DP_Name">'.$rw->DP_Name.'</userdata>
						<userdata name="DP_Comments">'.$rw->DP_Comments.'</userdata>
						<userdata name="DP_Status">'.$rw->DP_Status.'</userdata>
                                                <userdata name="DH_Weekends">'.$rw->DH_Weekends.'</userdata>
                                                <userdata name="DH_WeekString">'.trim($weekdays,",").'</userdata>    
						<cell>'.$j.'</cell>
						<cell name="DP_Name">'.$rw->DP_Name.'</cell>';
                                                if($preTally_user_ofid==1)
                                                {
                                                    echo '<cell name="Company_Name">'.$rw->OF_Name.'</cell>';
                                                }
                                                echo '<cell name="Days">'.trim($weekdays,",").'</cell>';
                                                if($rw->DP_Status == '0') {
                                                        $DeptStatus="Blocked";
                                                }else{
                                                        $DeptStatus="Approved";
                                                }
                                                
                                                echo '<cell name="DeptStatus">'.$DeptStatus.'</cell>';
						if($rw->DP_Status == '0') { 
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