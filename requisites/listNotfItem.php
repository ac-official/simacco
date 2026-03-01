<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php" );
require_once($BASEPATH . "preTallyClass/UserClass.php" );
include_once($BASEPATH . "includes/functions.php");
$NotificationObj = new NotificationClass();
$UsrObj  = new UserClass();
$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
generate_reptIDS($preTally_user_ofid,$preTally_user_id,$UsrObj->UserArray);
$Rprtid=$_SESSION['user_report_id'];    
$NotfItmObj->viewNotifyItems('IT.IT_Id, IT.US_Id, IT.IT_Name,IT.IT_Approved, IT.IT_Comments,IT.IT_Status, IT.IT_Business, IT.IT_Transfers, IT.IT_CDate, IT.SH_Id, IT.MH_Type, US.US_FName, US.US_LName','as IT, users_auth as US WHERE IT.US_Id = US.US_Id AND IT.IT_Approval IN  ('.$Rprtid.') ORDER BY IT.IT_Name');
$IT_NotfObj = $NotfItmObj->NotfArray;
//print_r($ACL_Obj->ACL_Item );
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
        <userdata name="db_table">items</userdata>
        <userdata name="db_primary">IT_Id</userdata>
        <userdata name="db_date">IT_MDate</userdata>
        <userdata name="db_status">IT_Status</userdata>
        <head>
            <column width="50" type="ro" align="center" sort="int"> SlNo </column>
            <column width="*" type="ro" align="left" sort="int"> Item </column>
            <column width="*" type="ro" align="left" sort="int"> Added By </column>
            <column width="*" type="ro" align="left" sort="int"> Approved By </column>';
            if($ACL_Obj->ACL_Item == 1 && $preTally_user_ofid != 1 ){
                echo '<column width="100" type="ro" align="center" sort="str"> Edit / Save </column>
                    <column width="60" type="ro" align="center" sort="na"> Swap </column>';
            } else if($preTally_user_ofid == 1 ) {
                echo '<column width="210" type="ro" align="center" sort="str"> Add Item to Pretally Item Pool </column>';
            } else {
                echo '<column width="80" type="ro" align="center" sort="str"> Approve </column>
                    <column width="60" type="ro" align="center" sort="na"> Swap </column>';
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
                <param>,#text_filter,#select_filter,#select_filter,,</param>
            </call>
        </afterInit>

        </head>';
        if($IT_NotfObj) {
            $j = 1;
            foreach($IT_NotfObj as $rw) {
                //$aprvdUser = unserialize($rw->IT_Notf);  $aprvdUser[0]             
                $IT_Approved = $UsrObj->getUserName($rw->IT_Approved);
                if ($rw->IT_Approved == $preTally_user_id) $IT_Approved = 'Waiting for your Approval . .';
                echo '<row id="'.$rw->IT_Id.'">
                    <userdata name="US_Id">'.$rw->US_Id.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>
                    <userdata name="IT_Status">'.$rw->IT_Status.'</userdata>
                    <userdata name="IT_Business">'.$rw->IT_Business.'</userdata>   
                    <userdata name="IT_Transfers">'.$rw->IT_Transfers.'</userdata>   
                    <cell>'.$j.'</cell>
                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>
                    <cell name="US_Name">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell name="IT_Approved">'.$IT_Approved.'</cell>';    
            if($ACL_Obj->ACL_Item == 1 && $preTally_user_ofid != 1){
                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'" rID="'.$j.'" class="itemSave"/>]]></cell>
                    <cell><![CDATA[<img src="images/icon/swap.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Notification.mapItem(this,'.$rw->IT_Id.',\''.$rw->IT_Name.'\','.$j.');"/>]]></cell>';
            } else if($preTally_user_ofid == 1 ) {
                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'" rID="'.$j.'" onclick="preTally.Notification.aliasNotfItem('.$rw->IT_Id.');" />]]></cell>';
            } else {
                echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'" rID="'.$j.'" onclick="preTally.Notification.approveItem(this,'.$rw->IT_Id.',\''.$rw->IT_Name.'\','.$j.');" />]]></cell>'
                        . '<cell><![CDATA[<img src="images/icon/swap.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Notification.mapItem(this,'.$rw->IT_Id.',\''.$rw->IT_Name.'\','.$j.');"/>]]></cell>';
            }
            
                    
                echo '</row>';
                $j++;
            }
        } else {
            echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';

        }
		  
echo '</rows>';
?>
