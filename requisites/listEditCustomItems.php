<?php
error_reporting(E_ALL ^ E_NOTICE);

//$ajax = 'true';

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/NotificationClass.php");
require_once($BASEPATH . "preTallyClass/ItemClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$NotfItmObj = new NotificationClass();
$ItemObj    = new ItemClass();
$UsrObj     = new UserClass();

$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
$US_Obj = $UsrObj->UserArray;
$USLC_Obj = $UsrObj->UserLocationArray;
    
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$IE_Type = array('','Income','Expense');
//print_r($REQUEST);

$filterData = explode(",",$REQUEST['filter']);
//print_r($filterData);

$filter = 'AND 1 ';
if($filterData[0]) {
    $filter .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != '' && $filterData[1] != 'All'){
    $filter .='AND IT.IT_Id = "'.$filterData[1].'%"' ;
}
if($filterData[2] != ''){
    $filter .='AND DS_Description like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter .='AND SH.SH_Name like "'.$filterData[3].'%"';
}
if($filterData[4]){
    $filter .='AND MH.MH_Id = "'.$filterData[4].'"';
}
if($filterData[5] != '' && $filterData[5] != 'All'){
    $filter .='AND ( IT.US_Id = "'.$filterData[5].'" ) ';
}
if($filterData[6] != 0 && $filterData[6] != 'All'){
    $UsrObj->userIds('US_Id',' AND OF_Id ='.$preTally_user_ofid.' AND LC_Id = '.$filterData[6].' ' );
    $USID_Obj   = $UsrObj->UserArray;  
    $USID =  rtrim($USID_Obj, ",");
    $filter .='AND IT.US_Id IN ('.$USID.' )';
}
if($filterData[7] != '' && $filterData[7] != 'All'){
    $filter .='AND ( IT.IT_Approved = "'.$filterData[7].'" )';
}
if($filterData[8] != ''){
    $filter .='AND ( IT.IT_Status = "'.$filterData[8].'" )';
}else if($filterData[8] == '0'){
    $filter .='AND ( IT.IT_Status = "'.$filterData[8].'" )';
}


//if($filterData[8]){
//    $ITfilter .='AND ( IT.IT_Approved = "'.$filterData[8].'" )';
//    $DSfilter .='AND ( DS.DS_Approved = "'.$filterData[8].'" AND IT.IT_Status=1)';
//}
//if($filterData[9]){
//    $ITfilter .='AND ( IT.IT_Approval = "'.$filterData[9].'" )';
//    $DSfilter .='AND ( DS.DS_Approval = "'.$filterData[9].'" AND IT.IT_Status=1)'; 
//}


//if($preTally_user_ofid == 1) {
//    $cond = " IT.OF_Id != 1 AND IT.OF_Id_Alias != 1 AND IT.IT_Status = 1 ";
//} else {
    $cond = " IT.OF_Id = $preTally_user_ofid AND ( IT.IT_Status = 1 || IT.IT_Status = 0 ) ";
//}

$tot_count = $ItemObj->viewCustomItemsCount($cond,$filter,$ITfilter,$DSfilter ); 
$ItemObj->viewCustomItems($cond,$filter,$_GET["posStart"],$_GET["count"], $ITfilter,$DSfilter );    
$IT_Obj = $ItemObj->ItemArray;
//print_r($IT_NotfObj);

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">';
echo ' <userdata name="ACL_Type">'.$ACL_Obj->ACL_Item.'</userdata>';
    if ($_GET["posStart"] == 0) {   
        /*echo '<head>
            <column width="50" type="ro" align="center" sort="int" id="BS_Id" > SlNo </column>
            <column width="100" type="combo" align="center" sort="int" id="MH_Type"> Entry Type </column>
            <column width="*" type="combo" align="left" sort="int" id="IT_Name"> Item </column>
            <column width="*" type="combo" align="left" sort="int" id="DS_Description"> Description </column>';
            if($ACL_Obj->ACL_Item == 1 ){
                echo '<column width="*" type="combo" align="left" sort="int" id="SH_Id"> Sub Head </column>
                      <column width="120" type="ro" align="left" sort="int" id="MH_Id"> Main Head </column>
                      <column width="80" type="ed" align="left" sort="int" id="BS_Amount"> Amount </column>';
            }
            echo '<column width="*" type="ro" align="left" sort="int"> Added By </column>
            <column width="*" type="ro" align="left" sort="int"> Approved By </column>';
            if($preTally_user_ofid != 1 ){
                echo '<column width="60" type="ch" align="center" sort="str" id="IT_Save"> Save </column>';
            } else if($preTally_user_ofid == 1 ) {
                echo '<column width="210" type="ro" align="center" sort="str"> Add Item to Pretally Item Pool </column>';
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
                
            </beforeInit> 
        <afterInit>
            <call command="attachHeader">';
                 if($ACL_Obj->ACL_Item == 1 ){echo '<param>,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#master_checkbox,</param>';}
                 else { echo '<param>,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#master_checkbox,</param>'; }
            echo '</call>
        </afterInit>

        </head>';
        
         */
    }
        if($IT_Obj) {
            $j=$_GET["posStart"]+1;
            foreach($IT_Obj as $rw) {
                
                $statImg    = ($rw->IT_Status == '0') ? "cross.png" : "tick.png";
                $statLabel  = ($rw->IT_Status == '0') ? "Blocked By Admin" : "Approved";     
                              
                $AddedByName     = $USLC_Obj[$rw->US_Id]['US_Name']; 
                $ApprovedByName  = $USLC_Obj[$rw->IT_Approved]['US_Name']; 
                $LCName          = $USLC_Obj[$rw->US_Id]['LC_Name'];
                $SHcolor         = ($rw->SH_Id == 58 || $rw->SH_Id == 59) ?  'red' : 'black';
                 
                if ($rw->US_Id == $preTally_user_id) $AddedByName = $preTally_user_name;
                if ($ApprovedById == $rw->US_Id) $ApprovedByName = '--';
//                if ($ApprovalForId == $preTally_user_id) $ApprovalForName = 'Waiting for your Approval . .';
                
//                if($rw->SH_Id == 58 || $rw->SH_Id == 59) { 
//                    $SHcolor = 'red';
//                } else { 
//                    $SHcolor = 'black'; 
//                }
                // user data IT_OtherUser added 28-05-25
                echo '<row id="'.$rw->IT_Id.'">
                    
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>   
                    <userdata name="IT_Business">'.$rw->IT_Business.'</userdata>   
                    <userdata name="IT_Transfers">'.$rw->IT_Transfers.'</userdata>
                    <userdata name="IT_PettyCash">'.$rw->IT_PettyCash.'</userdata>
                    <userdata name="IT_DualEntry">'.$rw->IT_DualEntry.'</userdata>
                    <userdata name="IT_DualItem">'.$rw->IT_DualItem.'</userdata> 
                    <userdata name="IT_MinAmount">'.$rw->IT_MinAmount.'</userdata>
                    <userdata name="IT_MaxAmount">'.$rw->IT_MaxAmount.'</userdata> 
                    <userdata name="IT_Status">'.$rw->IT_Status.'</userdata>
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>   
                    <userdata name="US_Id">'.$rw->US_Id.'</userdata>
                    <userdata name="IT_Id_Old">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name_Old">'.$rw->IT_Name.'</userdata>
                    <userdata name="SH_Id_Old">'.$rw->SH_Id.'</userdata>
                    <userdata name="MH_Id_Old">'.$rw->MH_Id.'</userdata>
                    <userdata name="IT_OtherUser">'.$rw->IT_OtherUser.'</userdata>
                    
                    <userdata name="SHDetails">{"SHId":"'.$rw->SH_Id.'","MHId":"'.$rw->MH_Id.'","MHName":"'.$rw->MH_Name.'"}</userdata>
                        
                    <cell>'.$j.'</cell>
                    <cell id="'.$rw->MH_Type.'" >'.$IE_Type[$rw->MH_Type].'</cell>
                    <cell name="IT_Name" >'.$rw->IT_Name.'</cell>
                    <cell name="DS_Description">'.$rw->DS_Description.'</cell>
                    <cell name="SH_Name"  style="color:'.$SHcolor.';" >'.$rw->SH_Name.'</cell>
                    <cell name="MH_Name" >'.$rw->MH_Name.'</cell>
                    <cell name="IT_AddedBy">'.$AddedByName.'</cell>
                    <cell name="LC_Name">'.$LCName.'</cell>
                    <cell name="IT_Approved">'.$ApprovedByName.'</cell>
                    <cell><![CDATA[<img src="images/icon/'.$statImg.'" style="margin:2px 0; cursor:pointer;"/>]]></cell>
                    <cell><![CDATA[<img src="images/icon/info_18.png" style="margin:2px 0; cursor:pointer;" onclick="preTally.Item.showDescription(this,'.$rw->IT_Id.');" />]]></cell>
                    <cell><![CDATA[<img src="images/icon/edit_icon.gif" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'"  onclick="preTally.Item.editCompanyItem(this,'.$rw->IT_Id.');"/>]]></cell>
                    <cell></cell>';
//                    if($preTally_user_ofid != 1){
//                        echo '<cell></cell>';
//                    } else if($preTally_user_ofid == 1 ) {
//                        echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'" rID="'.$j.'" onclick="preTally.Notification.aliasNotfItem('.$rw->IT_Id.');" />]]></cell>';
//                    }      
                    
                echo '</row>';
                $j++;
            }
        } else {
            echo '<row id="0"><cell colspan= "13" ><![CDATA[<div style="font-size:16px;color:#0979B1;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';

        }
		  
echo '</rows>';
?>