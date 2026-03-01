<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/AttendanceClass.php");
$AttObj = new AttendanceClass();
$filter=" WHERE HD_Type !=0 AND holidays.OF_Id= ".$preTally_user_ofid;
$AttObj->viewHolidays($filter);
$Att_Obj = $AttObj->HolidayArray;

$h_type=array("National","State","Restricted","Others");
$statusArray = array("Blocked","Active");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>		
        <head>
            <column width="50" type="ro" align="center" sort="na"> SlNo </column>
            <column width="240" type="ro" align="left" sort="na">Date</column>
            <column width="*" type="ro" align="left" sort="na">Title</column>
            <column width="*" type="ro" align="left" sort="na">Department</column>
            <column width="115" type="ro" align="left" sort="na">Type</column>                        
            <column width="100" type="ro" align="center" sort="na">Status</column>
            <column width="0" type="ro" align="left" sort="na"></column> 
            <column width="0" type="ro" align="left" sort="na"></column> 
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
        if($Att_Obj) {
            $j = 1;
            foreach($Att_Obj as $rw) {
                if($rw->ST_Id != 0) {
                    $stateList = $AttObj->getStatesCSV($rw->ST_Id);
                }
                else {
                    $stateList = "All States";
                }
                
                echo '<row id="'.$rw->HD_Id.'">	
                    <userdata name="HD_Date">'.$rw->HD_Date.'</userdata>
                    <userdata name="HD_Comments">'.$rw->HD_Comments.'</userdata>
                    <userdata name="HD_Type">'.$rw->HD_Type.'</userdata>                                            
                    <userdata name="HD_Batch">'.$rw->HD_Batch.'</userdata>    
                    <userdata name="DP_Id">'.($rw->DP_Id == 0 ? 'All':$rw->DP_Id).'</userdata> 
                    <userdata name="HD_Status">'.$rw->HD_Status.'</userdata>
                    <userdata name="ST_Id">'.$rw->ST_Id.'</userdata>        
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" " name="HD_Date">'.date("dS F Y",strtotime($rw->HD_Date)).'</cell>
                    <cell title="'.$rw->HD_Comments." (".$stateList.")".'" name="HD_Comments">'.$rw->HD_Comments." (".$stateList.")".'</cell>
                    <cell title=" ">'.($rw->DP_Id == 0 ? 'All':$rw->DP_Name).'</cell>
                    <cell title=" " name="HD_Type">'.$h_type[$rw->HD_Type-1].'</cell>';                                              
                    if($rw->HD_Status == '0') { 
                        echo '<cell title="Blocked"><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Blocked\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                    } else {
                        echo '<cell title="Active"><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Approved\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                    }
                    echo '<cell title=" ">'.$statusArray[$rw->HD_Status].'</cell>
                        <cell title="HD_Year">'.date("Y",strtotime($rw->HD_Date)).'</cell>
                    </row>';	
                $j++;
            }
        }  
        else {
            echo '<row id="0"> 
            <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
        </row>';
        }
echo '</rows>';
?>