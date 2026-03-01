<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/NotificationClass.php" );
require_once($BASEPATH . "preTallyClass/UserClass.php" );

$NotfItmObj = new NotificationClass();
$UsrObj     = new UserClass();

$UsrObj->userNotificationHierarchy(' AND US.OF_Id ='.$preTally_user_ofid.' ORDER BY US.US_Report' );
$USLC_Obj = $UsrObj->UserLocationArray;
   
if (!isset($_GET["posStart"]))
    $_GET["posStart"] = 0;
if (!isset($_GET["count"]))
    $_GET["count"] = 50;

$IE_Type = array('','Income','Expense');
//print_r($REQUEST);

$filterData = explode(",",$REQUEST['filter']);
//print_r($filterData);

/*
$MH_Type = $REQUEST['MH_Type'];
$IT_Name = $REQUEST['IT_Name'];
$DS_Description = $REQUEST['DS_Description'];
$SH_Name = $REQUEST['SH_Name'];
$MH_Name = $REQUEST['MH_Name'];
$IT_AddedBy = $REQUEST['IT_AddedBy'];
$IT_ApprovedBy = $REQUEST['IT_ApprovedBy'];
*/

$filter = 'AND LC.OF_Id = "'. $preTally_user_ofid .'" ';
if($filterData[0]) {
    $filter .='AND IT.MH_Type = "'.$filterData[0].'" ';
}
if($filterData[1] != ''  && $filterData[1] != 'All'){
    $filter .='AND IT.IT_Id = "'.$filterData[1].'"' ;
}
if($filterData[2] != ''){
    $filter .='AND DS_Description like "'.$filterData[2].'%"';
}
if($filterData[3] != ''){
    $filter .='AND SH.SH_Name like "'.$filterData[3].'%"';
}
if($filterData[4]){
    $filter .='AND MH.MH_Id = "'.$filterData[4].'"';
//}else if($IT_AddedBy){
//    $filter .='AND IT.IT_Name ='.$IT_AddedBy;
//}else if($IT_ApprovedBy){
//    $filter .='AND IT.IT_Name ='.$IT_ApprovedBy;
}
if($filterData[5] != ''){
    $filter .='AND BS.BS_Amount = "'.$filterData[5].'"';
}
if($filterData[6] != '' && $filterData[6] != 'All'){
    $ITfilter .='AND ( IT.US_Id = "'.$filterData[6].'" ) ';
    $DSfilter .='AND ( DS.US_Id = "'.$filterData[6].'" AND IT.IT_Status=1)'; 
}
if($filterData[7] != 0 && $filterData[7] != 'All'){
    $UsrObj->userIds('US_Id',' AND OF_Id ='.$preTally_user_ofid.' AND LC_Id = '.$filterData[7].' ' );
    $USID_Obj   = $UsrObj->UserArray;      
    $USID =  rtrim($USID_Obj, ",");
    $ITfilter .='AND IT.US_Id IN ('.$USID.' )';
    $DSfilter .='AND ( DS.US_Id IN ('.$USID.') AND IT.IT_Status=1 )';
}
//if($filterData[7]){
//    $filter .='AND LC.LC_Name like "'.$filterData[7].'%" ';
//}
if($filterData[8] != '' && $filterData[8] != 'All'){
    $ITfilter .='AND ( IT.IT_Approved = "'.$filterData[8].'" )';
    $DSfilter .='AND ( DS.DS_Approved = "'.$filterData[8].'" AND IT.IT_Status=1)';
}
if($filterData[9] != '' && $filterData[9] != 'All'){
    $ITfilter .='AND ( IT.IT_Approval = "'.$filterData[9].'" )';
    $DSfilter .='AND ( DS.DS_Approval = "'.$filterData[9].'" AND IT.IT_Status=1)'; 
}
/*
if($filterData[5]){
    $filter .='AND BS.BS_Amount = "'.$filterData[5].'"';
}
if($filterData[6]){
    $filter .='AND ( DS.US_Id = "'.$filterData[6].'" || IT.US_Id = "'.$filterData[6].'") ';
}
if($filterData[7]){
    $filter .='AND LC.LC_Name like "'.$filterData[7].'%" ';
}
if($filterData[8]){
    $filter .='AND ( IT.IT_Approved = "'.$filterData[8].'" || DS.DS_Approved = "'.$filterData[8].'") ';
}
if($filterData[9]){
    $filter .='AND ( IT.IT_Approval = "'.$filterData[9].'" || DS.DS_Approval = "'.$filterData[9].'") ';
}*/


$tot_count = $NotfItmObj->viewPendingItemsCount($filter,$_GET["posStart"],$_GET["count"], $ITfilter,$DSfilter);
$NotfItmObj->viewPendingItems($filter,$_GET["posStart"],$_GET["count"], $ITfilter,$DSfilter);
$IT_NotfObj = $NotfItmObj->NotfArray;

//print_r($IT_NotfObj);

if($tot_count < $_GET["count"]) $_GET["count"] = $tot_count;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows total_count="'.$tot_count.'" pos="'.$_GET["posStart"].'">';
echo ' <userdata name="ACL_Type">'.$ACL_Obj->ACL_Item.'</userdata>';
    if ($_GET["posStart"] == 0 && !isset($REQUEST['filter']) ) {   
       if($ACL_Obj->ACL_BSheet_VM =='1'){
       echo '<head>
            <column width="50"  type="ro" align="center" sort="na" > SlNo </column>
            <column width="60"  type="combo" align="center" sort="na"><![CDATA[<select style = "width:50px;" class="select_filter" id="iePF"><option value ="0">All</option><option value ="1">Income</option><option value ="2">Expense</option></select>]]></column>
            <column width="*"   type="combo" align="left" sort="na" ><![CDATA[<div id="itmPF" style="width: 90%;" placeholder="Item"></div>]]></column>
            <column width="*"   type="combo" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="decPF" style="width: 90%;" placeholder="Description">]]></column>
            <column width="180" type="combo" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="shPF" style="width: 90%;" placeholder="Sub Head">]]></column>
            <column width="100" type="ro" align="left" sort="na" ><![CDATA[<select style = "width:80px;" class="select_filter" id="mhPF"><option value ="0">All</option><option value ="1">Direct Income</option><option value ="2">Direct Expense</option><option value ="3">Indirect Income</option><option value ="4">Indirect Expense</option><option value ="5">Assets</option></select>]]></column>
            <column width="80"  type="ed" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="amtPF" style="width: 50px;" placeholder="Amount">]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="addPF" style="width: 90%;" placeholder="Added By"></div>]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="brnPF" style="width: 90%;" placeholder="Branch"></div>]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="aprPF" style="width: 90%;" placeholder="Approved By"></div>]]></column>
            <column width="40"  type="ch" align="center" sort="na">#master_checkbox</column>';
        }else{
            echo '<head>
            <column width="50"  type="ro" align="center" sort="na" > SlNo </column>
            <column width="60"  type="ro" align="center" sort="na"><![CDATA[<select style = "width:50px;" class="select_filter" id="iePF"><option value ="0">All</option><option value ="1">Income</option><option value ="2">Expense</option></select>]]></column>
            <column width="*"   type="ro" align="left" sort="na" ><![CDATA[<div id="itmPF" style="width: 90%;" placeholder="Item"></div>]]></column>
            <column width="*"   type="ro" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="decPF" style="width: 90%;" placeholder="Description">]]></column>
            <column width="180" type="ro" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="shPF" style="width: 90%;" placeholder="Sub Head">]]></column>
            <column width="100" type="ro" align="left" sort="na" ><![CDATA[<select style = "width:80px;" class="select_filter" id="mhPF"><option value ="0">All</option><option value ="1">Direct Income</option><option value ="2">Direct Expense</option><option value ="3">Indirect Income</option><option value ="4">Indirect Expense</option><option value ="5">Assets</option></select>]]></column>
            <column width="80"  type="ro" align="left" sort="na" ><![CDATA[<input type="text"  class="text_filter" id="amtPF" style="width: 50px;" placeholder="Amount">]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="addPF" style="width: 90%;" placeholder="Added By"></div>]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="brnPF" style="width: 90%;" placeholder="Branch"></div>]]></column>
            <column width="100" type="ro" align="left" sort="na"><![CDATA[<div id="aprPF" style="width: 90%;" placeholder="Approved By"></div>]]></column>
            <column width="40"  type="ch" align="center" sort="na">#master_checkbox</column>';
        }
//            if($preTally_user_ofid != 1 ){
//                echo '<column width="60" type="ch" align="center" sort="str" id="IT_Save"> Save </column>';
//            } else if($preTally_user_ofid == 1 ) {
//                echo '<column width="210" type="ro" align="center" sort="str"> Add Item to Pretally Item Pool </column>';
//            } else {
//                echo '<column width="80" type="ro" align="center" sort="str"> Approve </column>';
//                    
//            }
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
            
        </afterInit>

        </head>';
        
         
    }
        if($IT_NotfObj) {
            $j=$_GET["posStart"]+1;
            foreach($IT_NotfObj as $rw) {
                //$aprvdUser = unserialize($rw->IT_Notf);  $aprvdUser[0]             
                              
//                $IT_Added     = $rw->IT_Status == 1 ? $USLC_Obj[$rw->DSUS_Id]['US_Name']     : $USLC_Obj[$rw->ITUS_Id]['US_Name']; 
                $IT_Approved  = $rw->IT_Status == 1 ? $USLC_Obj[$rw->DS_Approved]['US_Name'] : $USLC_Obj[$rw->IT_Approved]['US_Name']; 
                $IT_Approval  = $rw->IT_Status == 1 ? $USLC_Obj[$rw->DS_Approval]['US_Name'] : $USLC_Obj[$rw->IT_Approval]['US_Name']; 
//                $ITDS_Added   = $rw->IT_Status == 1 ? $rw->DSUS_Id : $rw->ITUS_Id; 
                $ITDS_Approved  = $rw->IT_Status == 1 ? $rw->DS_Approved : $rw->IT_Approved; 
                $ITDS_Approval  = $rw->IT_Status == 1 ? $rw->DS_Approval : $rw->IT_Approval; 
//                $LCName       = $USLC_Obj[$ITDS_Added]['LC_Name'];
                $ITcolor = $rw->IT_Status == 2 ? 'red' : 'black';
                $DScolor = $rw->DS_Status == 2 ? 'red' : 'black';
                if($rw->SH_Id == 58 || $rw->SH_Id == 59) $SHcolor = 'red';
//                $IT_Approved = $UsrObj->getUserName($US_ApprovedID);
                
                
                $IT_Added     = $USLC_Obj[$rw->BSUS_Id]['US_Name'];
                $ITDS_Added       = $rw->BSUS_Id; 
                $LCName          = $USLC_Obj[$ITDS_Added]['LC_Name'];
                
                
//                if ($ITDS_Added == $preTally_user_id) $IT_Added = $preTally_user_name;
                
                if ($ITDS_Approved == $ITDS_Added) $IT_Approved = '--';
                if ($ITDS_Approval == $preTally_user_id) $IT_Approval = 'Waiting for your Approval . .';
                
                //<cell name="IT_Name" '.$cellType.'><![CDATA[<div style = "color:'.$color.'"> '.$rw->IT_Name.'</div>]]></cell>
                echo '<row id="'.$rw->BS_Id.'">
                    <userdata name="BS_Id">'.$rw->BS_Id.'</userdata>
                    <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <userdata name="IT_Status">'.$rw->IT_Status.'</userdata>
                    <userdata name="DS_Status">'.$rw->DS_Status.'</userdata>

                    <userdata name="IT_Id_Old">'.$rw->IT_Id.'</userdata>
                    <userdata name="IT_Name_Old">'.$rw->IT_Name.'</userdata>
                    <userdata name="SH_Id_Old">'.$rw->SH_Id.'</userdata>
                    <userdata name="MH_Id_Old">'.$rw->MH_Id.'</userdata>
                    <userdata name="BS_Amount">'.$rw->BS_Amount.'</userdata>

                    <userdata name="SHDetails">{"SHId":"'.$rw->SH_Id.'","MHId":"'.$rw->MH_Id.'","MHName":"'.$rw->MH_Name.'"}</userdata>
                        
                    <cell>'.$j.'</cell>
                    <cell id="'.$rw->MH_Type.'" >'.$IE_Type[$rw->MH_Type].'</cell>
                    <cell name="IT_Name"  style="color:'.$ITcolor.';">'.$rw->IT_Name.'</cell>
                    <cell name="DS_Description" style="color:'.$DScolor.';">'.$rw->DS_Description.'</cell>';
                    if($ACL_Obj->ACL_Item == 1 ){ 
                        echo '<cell name="SH_Name" validate="NotEmpty" style="color:'.$SHcolor.';" >'.$rw->SH_Name.'</cell>
                        <cell name="MH_Name" validate="NotEmpty">'.$rw->MH_Name.'</cell>
                        <cell name="BS_Amount">'.$rw->BS_Amount.'</cell>';
                    }
                    echo '<cell name="IT_AddedBy">'.$IT_Added.'</cell>
                    <cell name="LC_Name">'.$LCName.'</cell>
                    <cell name="IT_Approval">'.$IT_Approved.'</cell>';    
                    
                    if($preTally_user_ofid != 1){
                        echo '<cell></cell>';
                    } else if($preTally_user_ofid == 1 ) {
                        echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;" ITId="'.$rw->IT_Id.'" rID="'.$j.'" onclick="preTally.Notification.aliasNotfItem('.$rw->IT_Id.');" />]]></cell>';
                    }      
                    
                echo '</row>';
                $j++;
            }
        } else {
            echo '<row id="0"><cell></cell> <cell></cell><cell><![CDATA[<div style="font-size:16px;color:#0979B1;font-family: serif;padding-top: 10px;" >No Records Found.</div>]]></cell></row>';

        }
		  
echo '</rows>';
?>