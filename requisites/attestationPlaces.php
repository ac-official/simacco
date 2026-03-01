<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$AttObj = new AttestationClass();
$GeneralObj = new GeneralClass();

$cityFilter = '';
if($REQUEST['CT_Id'])
    $cityFilter .= " AND CT_Id = ".$REQUEST['CT_Id'];

if($REQUEST['rpt']) {   // business-location rprt
   
    if($REQUEST['APId'] && is_numeric($REQUEST['APId'])) {
        $filter = " AP_Status = 1 $cityFilter ORDER BY AP_Name LIMIT 0,29) UNION (SELECT * FROM address_places WHERE AP_Id = ".$REQUEST['APId'].")";
    }   else
        $filter = " AP_Status = 1 $cityFilter ORDER BY AP_Name LIMIT 0,30) ";
    $GeneralObj->unionValues(' * ', 'address_places', $filter );
    $APObj = $GeneralObj->DataArray;     
} else {
    if($REQUEST['APId'])
        $filter = " AND AP.AP_Id = ".$REQUEST['APId'];
    $AttObj->getPlaceCityState($filter);
    $APObj = $AttObj->DataArray;
}
$filter = '';
if($_REQUEST['mask']) {  // auto filtering
    if($_REQUEST['mask']) $mask = $_REQUEST['mask'];
    else if($REQUEST['mask']) $mask = $REQUEST['mask'];
       
    if($REQUEST['rpt']) {   // business - locatn rprt
        if($REQUEST['mask'] && strtolower($REQUEST['mask']) != 'all'){
            $filter = " AND AP.AP_Name LIKE '".$mask."%' ";
            if($cityFilter) $filter .= " AND CT.CT_Id = ".$REQUEST['CT_Id'];
        }
    } else {
        $filter = " AND AP.AP_Name LIKE '".$mask."%' ";
    }
    if($filter != '') {//echo "ddd";exit;
        $AttObj->getPlaceCityState($filter);
        $APObj = $AttObj->DataArray;
    }
}

echo '<complete>';    
if(isset($REQUEST['rpt'])) {
    echo '<option value="0" ';
    if(!$REQUEST['APId'] && !$REQUEST['mask'])
        echo "selected = 'true'";
    echo '>All</option>';
}
if($APObj){
    foreach($APObj as $rw){
        if($REQUEST['APId'] == $rw->AP_Id) 
            $selected = 'selected = "true"';
        else
            $selected= '';
        echo '<option '.$selected.' value="'.$rw->AP_Id.'">'.  ucfirst(strtolower(trim(str_replace("&","&amp;",$rw->AP_Name)))).' / '.ucfirst(strtolower(trim($rw->CT_Name))).'</option>';
    }
}
echo '</complete>';
?>