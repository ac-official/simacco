<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");
include_once($BASEPATH . "includes/functions.php");
$NotificationObj = new NotificationClass();
$UsrObj  = new UserClass();
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];   

$NotificationObj->viewNotifyDescriptions('WHERE IT.IT_Id = DS.IT_Id AND DS.US_Id = US.US_Id AND DS.DS_Approval IN ('.$Rprtid.') ORDER BY DS.DS_Description');
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
        <column width="*" type="ro" align="left" sort="int"> Approved By </column>';
        if($ACL_Obj->ACL_Description ==1 ){
            echo '<column width="100" type="ro" align="center" sort="str"> Edit/Save </column>
                <column width="60" type="ro" align="center" sort="na"> Swap </column>';
        } else {
            echo '<column width="80" type="ro" align="center" sort="str"> Approve </column>';
        }
        echo '<settings>
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
                <param>,#text_filter,#select_filter,#select_filter,#select_filter,,</param>
            </call>
        </afterInit>

    </head>';
    if($DS_NotfObj) {
        $j = 1;
        foreach($DS_NotfObj as $rw) {
            
            $DS_Approved = $UsrObj->getUserName($rw->DS_Approved);
                if ($rw->DS_Approved == $preTally_user_id) $DS_Approved = 'Waiting for your Approval. ..';

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
                <cell name="DS_Approved">'.$DS_Approved.'</cell>';    
            
            if($ACL_Obj->ACL_Item ==1 ){
                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" DSId="'.$rw->DS_Id.'" rID="'.$j.'" class="descriptionSave"/>]]></cell>
                <cell><![CDATA[<img src="images/icon/swap.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Notification.mapDesc(this,'.$rw->DS_Id.',\''.$rw->DS_Description.'\','.$j.',\''.$rw->IT_Id.'\');" />]]></cell>';
            } else {
                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" DSId="'.$rw->DS_Id.'" rID="'.$j.'" onclick="preTally.Notification.approveDescription(this,'.$rw->DS_Id.',\''.$rw->DS_Description.'\','.$j.');" />]]></cell>';
            }
            
            echo '</row>';
            $j++;
        }
    } else {
        echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';
        
    }

echo '</rows>';
?>

