<?php
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$CN_Id = $REQUEST['cid'];
$ST_Id = '';
//$CN_Id = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : 90 ;
$GeneralObj = new GeneralClass();
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
if($CN_Id != 0)
{
    $GeneralObj->GetStates(' * ', 'states', $CN_Id.' AND ST_Status != 0' ,' ST_Name ');
    $ST_Obj = $GeneralObj->DataArray;
    $flag = 0;
} else {
    if($REQUEST['type'] == 'rpt') {
        $filter = ' ST_Status != 0 ';
        if($REQUEST['mask'] && strtolower($REQUEST['mask']) != 'all') {
            $filter .= " AND ST_Name LIKE '".trim($REQUEST['mask'])."%' ) ";
        } else {
            if($REQUEST['ST_Id'] && is_numeric($REQUEST['ST_Id'])) {
                $filter .= " ORDER BY ST_Name LIMIT 0,29) UNION (SELECT * FROM states WHERE ST_Id = ".$REQUEST['ST_Id'].")";
                $ST_Id = $REQUEST['ST_Id'];
            } else
                $filter .= " ORDER BY ST_Name LIMIT 0,30) ";
        }
        $GeneralObj->unionValues(' * ', 'states', $filter );
        $ST_Obj = $GeneralObj->DataArray;
        $flag = 1;
    }
}
echo '<complete>';
//if(isset($REQUEST['cid'])) 
if($flag) {
    echo '<option value="0" ';
    if(!isset($REQUEST['mask']) && !$REQUEST['ST_Id'] && !$REQUEST['flag']) 
        echo 'selected="true"';
    echo '>All</option>'; 
} else
    echo '<option value="0" selected="true">Select State</option>';  
if($ST_Obj){
    foreach($ST_Obj as $rw){
        $selected = '';
        if($rw->ST_Id == $ST_Id) $selected = ' selected="true" ' ;
        //if($rw->ST_Id == 17) $selected = ' selected="true" ';
        echo '<option value="'.$rw->ST_Id.'" '.$selected.'>'.str_replace("&","&amp;",$rw->ST_Name).'</option>';
    }
}

echo '</complete>';
?>