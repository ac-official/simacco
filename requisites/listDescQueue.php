<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php" );

$NotificationObj = new NotificationClass();
$UsrObj = new UserClass();

$NotificationObj->viewNotifyDescriptions('WHERE IT.IT_Id = DS.IT_Id AND DS.US_Id = US.US_Id AND DS.DS_Status = 2 AND DS.DS_Approval != DS.DS_Approved  AND DS.DS_Approval != 0 AND DS.OF_Id='.$preTally_user_ofid.' ORDER BY DS.DS_Description');
$DS_NotfObj = $NotificationObj->NotfArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <userdata name="db_table">departments</userdata>
    <userdata name="db_primary">DS_Id</userdata>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="*" type="ro" align="left" sort="str"> Description </column>
        <column width="*" type="ro" align="left" sort="str"> Item </column>
        <column width="*" type="ro" align="left" sort="int"> Added By </column>
        <column width="*" type="ro" align="left" sort="int"> Approved By </column>
        <column width="*" type="ro" align="left" sort="int"> Waiting For </column>
        <column width="*" type="ro" align="left" sort="int"> Added On</column>
        <column width="*" type="ro" align="left" sort="int"> Last Approved On</column>   
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
                <param>,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,,</param>
            </call>
        </afterInit>

    </head>';
    if($DS_NotfObj) {
        $j = 1;
        foreach($DS_NotfObj as $rw) {
            $DS_Approved = "---";
            $DS_Approval = "---";
           if ($rw->DS_Approved != $rw->US_Id) $DS_Approved = $UsrObj->getUserName($rw->DS_Approved);
           if ($rw->DS_Approval != $rw->US_Id)  $DS_Approval = $UsrObj->getUserName($rw->DS_Approval);
            echo '<row id="'.$j.'">
                <userdata name="DS_Id">'.$rw->DS_Id.'</userdata>
                <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                <userdata name="DS_Description">'.$rw->DS_Description.'</userdata>
                <userdata name="DS_Status">'.$rw->DS_Status.'</userdata>
                <cell>'.$j.'</cell>
                <cell name="DS_Description">'.$rw->DS_Description.'</cell>
                <cell name="DS_Description">'.$rw->IT_Name.'</cell>
                <cell name="US_Name">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                <cell name="DS_Approved">'.$DS_Approved.'</cell> 
                <cell name="DS_Approved">'.$DS_Approval.'</cell>
                <cell name="DS_Approved">'.$rw->DS_CDate.'</cell> 
                <cell name="DS_Approved">'.$rw->DS_MDate.'</cell></row>';
            $j++;
        }
    } else {
        echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';
        
    }

echo '</rows>';
?>

