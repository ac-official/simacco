<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");

$ItemObj = new ItemClass();

if($preTally_user_ofid == 1) {
    if($REQUEST['f'] == 'p'){
        $filter = " && ( IT.OF_Id = 1 ) && IT.IT_Status!=2 ";
    }
    if($REQUEST['f'] == 'c'){
        $filter = " && ( IT.OF_Id != 1 ) && IT.OF_Id_Alias != 1 && IT.IT_Status=1 ";
    }
    $colType = 'ro';
    $headType = '';
} else {
    $colType = 'ro';
    $headType = '';
    if($REQUEST['f'] == 'p'){
        $filter = " && ( IT.OF_Id=1 ) && IT.IT_Status =1 && IT.OF_Id_Alias != $preTally_user_ofid";
        $colType = 'ch';
        $headType = '#master_checkbox';
    }
    if($REQUEST['f'] == 'c'){
        $filter = " && ( IT.OF_Id = $preTally_user_ofid ) && IT.IT_Status!=2 ";
    }
}
$ItemObj->viewItems('IT.IT_Id,IT.IT_Name,IT.SH_Id,IT.IT_Comments, IT.IT_Business,IT.IT_Transfers,IT.OF_Id,IT.IT_Status, SH.SH_Name, MH.MH_Type',' AS IT, sub_heads AS SH , main_heads as MH WHERE SH.SH_Id=IT.SH_Id && SH.MH_Id=MH.MH_Id  '.$filter.' ORDER BY IT.IT_Name');    
$IT_Obj = $ItemObj->ItemArray;

$ItemObj->myMapItem($preTally_user_ofid);
$Map_Obj = $ItemObj->ItemMapArray;
$mapArray =  explode('"', $Map_Obj[0]->IC_Map);
$mapCount = floor(count($mapArray)/2);
//echo $mapCount;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '
<rows>
    <userdata name="db_table">items</userdata>
    <userdata name="db_primary">IT_Id</userdata>
    <userdata name="db_date">IT_MDate</userdata>
    <userdata name="db_status">IT_Status</userdata>
    <userdata name="OF_Id">'.$preTally_user_ofid.'</userdata>
    <userdata name="MP_CNT">'.$mapCount.'</userdata>
    
    <head>
        <column width="50" type="ro" align="center" sort="int"> SlNo </column>
        <column width="*" type="ro" align="left" sort="int"> Item </column>
        <column width="*" type="ro" align="left" sort="int"> Subhead </column>
        <column width="80" type="ro" align="left" sort="int"> Type </column>
        <column width="60" type="'.$colType.'" align="center" sort="str">	Status </column>
        <column width="25" type="ro" align="center" sort="str"> </column>
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
                <param>,#text_filter,#select_filter_strict,#select_filter_strict,'.$headType.'</param>
            </call>';
            if((($preTally_user_ofid == 1) && $REQUEST['f'] == 'p') || (($preTally_user_ofid != 1) && $REQUEST['f'] == 'c')) {
                echo '<call command="attachEvent">
                    <param>onRowSelect</param>
                    <param>preTally.BalanceSheet.editItem</param>
                </call>';
            }
            if(($preTally_user_ofid == 1) && ($REQUEST['f'] == 'c')) {
                echo '<call command="attachEvent">
                    <param>onRowSelect</param>
                    <param>preTally.BalanceSheet.aliasItem</param>
                </call>';
            }
        echo '</afterInit>

    </head>';
    if($IT_Obj) {
        $j = 1;
        foreach($IT_Obj as $rw) {

            $statImg    = ($rw->IT_Status == '0') ? "cross.png" : "tick.png";
            $statLabel  = ($rw->IT_Status == '0') ? "Blocked By Admin" : "Approved";
            $typeLabel  = ($rw->MH_Type == '1') ? "Income" : (($rw->MH_Type == '2') ? "Expense" : "");
            if($colType == 'ro')
                $imgCheckBox = '<![CDATA[<img src="images/icon/'.$statImg.'" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\''.$statLabel.'\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]>';
            else {
                $imgCheckBox = 0;
                if(in_array($rw->IT_Id, $mapArray)) {
                    $imgCheckBox = 1;
                }
            }
            echo '<row id="'.$rw->IT_Id.'">
                    <userdata name="IT_Name">'.$rw->IT_Name.'</userdata>
                    <userdata name="IT_Comments">'.$rw->IT_Comments.'</userdata>
                    <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                    <userdata name="IT_Status">'.$rw->IT_Status.'</userdata>
                    <userdata name="IT_Business">'.$rw->IT_Business.'</userdata>   
                    <userdata name="IT_Transfers">'.$rw->IT_Transfers.'</userdata>       
                    <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                    <cell>'.$j.'</cell>
                    <cell name="IT_Name">'.$rw->IT_Name.'</cell>
                    <cell name="SH_Name">'.$rw->SH_Name.'</cell>
                    <cell name="MH_Type">'.$typeLabel.'</cell>
                    <cell>'.$imgCheckBox.'</cell>
                    <cell></cell>
            </row>';
            $j++;
        }
    } else {echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;" >No Records Found.</div>]]></cell></row>';}
		  
echo '</rows>';
?>