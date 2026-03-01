<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj = new ItemClass();
if($preTally_user_ofid == 1){
    $IT_Cnt_Slf = $ItemObj->getItemCount('SH.SH_Id=IT.SH_Id AND SH.MH_Id=MH.MH_Id AND IT.IT_Status =1 AND OF_Id !=1 AND IT.OF_Id_Alias !='.$preTally_user_ofid);  
    $IT_Cnt_Tot = $ItemObj->getItemCount('SH.SH_Id=IT.SH_Id AND SH.MH_Id=MH.MH_Id AND IT.IT_Status!=2 AND IT.OF_Id=1');  
} else {
    $IT_Cnt_Slf = $ItemObj->getItemCount('SH.SH_Id=IT.SH_Id AND SH.MH_Id=MH.MH_Id AND IT.IT_Status!=2 AND OF_Id ='.$preTally_user_ofid);    
    $IT_Cnt_Tot = $ItemObj->getItemCount('SH.SH_Id=IT.SH_Id AND SH.MH_Id=MH.MH_Id AND IT.IT_Status =1 AND IT.OF_Id=1 AND IT.OF_Id_Alias !='.$preTally_user_ofid);  
}
$ItemObj->myMapItem($preTally_user_ofid);    
$Map_Obj = $ItemObj->ItemMapArray;
$mapArray =  explode('"', $Map_Obj[0]->IC_Map);
$IT_Cnt_Map = floor(count($mapArray)/2);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '
<rows>
    <userdata name="db_table">items</userdata>
    <userdata name="db_primary">IT_Id</userdata>
    <userdata name="db_date">IT_MDate</userdata>
    <userdata name="db_status">IT_Status</userdata>
    <userdata name="OF_Id">'.$preTally_user_ofid.'</userdata>
    <head>
        <column width="30" type="sub_row" align="center" sort="int"> </column>
        <column width="50" type="ro" align="center" sort="int"> SlNo </column>
        <column width="*" type="ro" align="left" sort="int"> Item Type</column>
        <column width="100" type="ro" align="center" sort="str"> Count </column>
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
            
        </afterInit>
    </head>
    <row id="1">
        <cell type="sub_row_grid">requisites/listItem.php&amp;f=c</cell>
        <cell>1</cell>
        <cell>Custom Items</cell>
        <cell>'.$IT_Cnt_Slf.'</cell>
    </row>
    <row id="2">
        <cell type="sub_row_grid">requisites/listItem.php&amp;f=p</cell>
        <cell>2</cell>
        <cell>PreTally Defined Items</cell>';
        if($preTally_user_ofid == 1){
             echo ' <cell>'.$IT_Cnt_Tot.'</cell>';
        }else{
            echo ' <cell>'.$IT_Cnt_Map.'/'.$IT_Cnt_Tot.'</cell>';
        }
    echo '</row>		  
</rows>';
?>