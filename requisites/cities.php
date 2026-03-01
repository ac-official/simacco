<?php
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$ST_Id = $REQUEST['sid'];
$ST_Name=$_REQUEST['mask'];
//$ST_Id = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : 17 ;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
$GeneralObj = new GeneralClass();
echo '<complete>';
$CT_Id = '';
if(isset($REQUEST['type']) && $REQUEST['type'] == 'rpt') {
    $filter = ' CT_Status != 0 ';
    echo '<option value = "0" ';
    if($REQUEST['ST_Id'] && is_numeric($REQUEST['ST_Id']))
        $filter .= " AND ST_Id = ".$REQUEST['ST_Id'];
            
    if($REQUEST['mask'] && strtolower($REQUEST['mask']) != 'all') {
        $filter .= " AND CT_Name LIKE '".trim($REQUEST['mask'])."%' ) ";
    } else {
        if(is_numeric($REQUEST['CT_Id'])) {
            $filter .= " ORDER BY CT_Name LIMIT 0,29) UNION (SELECT * FROM cities WHERE CT_Id = ".$REQUEST['CT_Id'].")";
            $CT_Id = $REQUEST['CT_Id'];
            if($CT_Id == 0 && !isset($REQUEST['flag']))    // option All
                echo 'selected="true"';
        } else {
            $filter .= " ORDER BY CT_Name LIMIT 0,30) ";
            if(!isset($REQUEST['flag'])) 
                echo 'selected="true"';
        }
    }
    echo '>All</option>'; 
    $GeneralObj->unionValues(' * ', 'cities', $filter );
    $CT_Obj = $GeneralObj->DataArray;
} else {
    if($ST_Id!=0 && $ST_Name=="") {    
        $GeneralObj->GetCities(' * ', 'cities', $ST_Id.' AND CT_Status != 0' ,' CT_Name ');
        $CT_Obj = $GeneralObj->DataArray;
        echo '<option value="0" selected="true">Select City</option>';	
    }
     elseif($ST_Name!="") {
           $GeneralObj->GetCityFiltr(' * ', 'cities',$ST_Name,'CT_Name ');
        $CT_Obj = $GeneralObj->DataArray;
    }
}
if($CT_Obj){
    foreach($CT_Obj as $rw){
        $selected = '';
        if($rw->CT_Id == $CT_Id) $selected = ' selected="true" ' ;
        echo '<option '.$selected.' value="'.$rw->CT_Id.'">'. $GeneralObj->clean(str_replace("&","&amp;",$rw->CT_Name)).'</option>';
    }
}
echo '</complete>';
?>