<?php
if($_REQUEST['checked']) {
    $hiddenUserID = explode(',',$_REQUEST['checked']);
}

if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "includes/functions.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
$filterUSR = filterHR_User($ACL_Obj->ACL_HR, 'USAUTH', $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);

$UserObj = new UserClass();
$UserObj->viewApprovedUserGrid($filterUSR,$preTally_user_id);
$US_Obj = $UserObj->UserArray;

if($preTally_user_ofid == 1) { $colField = "Company"; } else { $colField = "Designation"; }

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">users</userdata>
    <userdata name="db_primary">US_Id</userdata>
    <userdata name="db_date">US_MDate</userdata>
    <userdata name="db_status">US_Status</userdata>
    <userdata name="acl_status">'.$ACL_Obj->ACL_HR_VM.'</userdata>
    <head>
        <column width="50" type="ro" align="center" sort="int">	# </column>
        <column width="100" type="ro" align="left" sort="str">	EMP ID</column>
        <column width="*" type="ro" align="left" sort="str">Name </column>
        <column width="150" type="ro" align="left" sort="str">ACL Type</column>
        <column width="150" type="ro" align="left" sort="str"> '. $colField .' </column>
        <column width="*" type="ro" align="left" sort="str"> Location </column>
        
        <column width="60" type="ch" align="center" sort="na"></column>
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
                <param>,#text_filter,#text_filter_inc,#select_filter_strict,#select_filter_strict,#text_filter,#master_checkbox</param>
            </call>';
        echo '</afterInit>
    </head>';

    if($US_Obj) {
        $j = 1;
        foreach($US_Obj as $rw) {	
            if($preTally_user_ofid == 1) { $rwValue= $rw->OF_Name; } else { $rwValue= $rw->DG_Name; } 
            
            echo '<row id="'.$rw->US_Id.'">
                <userdata name="nameUserdata">'.$rw->US_FName.' '.$rw->US_LName.'</userdata>
                <cell>'.$j.'</cell>
                <cell>'.$rw->US_EMPID.'</cell>
                <cell>'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                <cell>'.$rw->ACL_Name.'</cell>
                <cell>'.$rwValue.'</cell>
                <cell>'.$rw->LC_Name.'</cell>
                <cell>';
            if($hiddenUserID) {   // if already checked user
                if (in_array($rw->US_Id, $hiddenUserID)) {
                    echo "1";
                }
            }        
            echo '</cell>     
            </row>';	
            $j++;
        }
    }
echo '</rows>';
?>