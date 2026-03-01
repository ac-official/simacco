<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php" );
require_once($BASEPATH . "preTallyClass/UserClass.php" );

$NotfItmObj = new NotificationClass();
$UsrObj = new UserClass();

$NotfItmObj->viewNotifyItems('IT.IT_Id, IT.US_Id, IT.IT_Name,IT.IT_Approved,IT.IT_Approval, IT.IT_Comments, IT.IT_CDate, IT.SH_Id, IT.MH_Type,IT.IT_CDate,IT.IT_MDate, US.US_FName, US.US_LName','as IT, users_auth as US WHERE IT.US_Id = US.US_Id AND IT.IT_Status = 2  AND IT.IT_Approval!=IT.IT_Approved AND IT.IT_Approval != 0 AND IT.OF_Id='.$preTally_user_ofid.' ORDER BY IT.IT_Name');
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
                <param>,#text_filter,#select_filter_strict,#select_filter_strict,#select_filter_strict,,</param>
            </call>
        </afterInit>

        </head>';
        if($IT_NotfObj) {
            $j = 1;
            foreach($IT_NotfObj as $rw) {
                //$aprvdUser = unserialize($rw->IT_Notf);  $aprvdUser[0]  
                $IT_Approved = "---";
                $IT_Approval = "---";
               if ($rw->IT_Approved != $rw->US_Id) $IT_Approved = $UsrObj->getUserName($rw->IT_Approved);
               if ($rw->IT_Approval != $rw->US_Id) $IT_Approval = $UsrObj->getUserName($rw->IT_Approval);
             
                echo '<row id="'.$j.'">
                    <userdata name="US_Id">'.$rw->US_Id.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>
                    <cell name="US_Name">'.$rw->US_FName.' '.$rw->US_LName.'</cell>
                    <cell name="IT_Approved">'.$IT_Approved.'</cell>
                    <cell name="IT_Approved">'.$IT_Approval.'</cell>
                    <cell name="IT_Approved">'.$rw->IT_CDate.'</cell>
                    <cell name="IT_Approved">'.$rw->IT_MDate.'</cell></row>';
                $j++;
            }
        } else {
            echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';

        }
		  
echo '</rows>';
?>
