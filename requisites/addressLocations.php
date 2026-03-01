<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
require_once($BASEPATH . "preTallyClass/AttestationClass.php");
require_once($BASEPATH . "preTallyClass/GeneralClass.php");
$AttObj     = new AttestationClass();
$GeneralObj = new GeneralClass();

$filter     = $mask = '';

if($REQUEST['ST_Id'])
    $filter .= " AND ST.ST_Id = ".$REQUEST['ST_Id'];

if($REQUEST['CT_Id'])
    $filter .= " AND CT.CT_Id = ".$REQUEST['CT_Id'];

if($REQUEST['mask']) {
    $mask           = $REQUEST['mask'];
    $mask_filter    = " AND AL.ALC_Name LIKE '".$mask."%' ";
}

if($REQUEST['ALC_Id'] && is_numeric($REQUEST['ALC_Id'])) {
    $filter = "ST.ST_Status = 1 $filter AND CT.CT_Status = 1 AND AL.ALC_Status = 1 ORDER BY AL.ALC_Name LIMIT 0,29) 
                UNION 
                (SELECT AL.ALC_Id, AL.ALC_Name,CT.CT_Name, ST.ST_Name
                    FROM addr_locations AS AL  
                    LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                    LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                    WHERE ST.ST_Status = 1 AND CT.CT_Status = 1 AND AL.ALC_Status = 1 
                    AND AL.ALC_Id = ".$REQUEST['ALC_Id']." )";   
} else {
   $filter  = " AL.ALC_Status = 1 $filter $mask_filter ORDER BY AL.ALC_Name LIMIT 0,30) ";
}

$GeneralObj->unionValues(' AL.ALC_Id, AL.ALC_Name,CT.CT_Name, ST.ST_Name ', 'addr_locations AS AL LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id', $filter );
$APObj = $GeneralObj->DataArray; 

echo '<complete>
<option value = "0" ';
if(!$REQUEST['ALC_Id'] && !$REQUEST['mask'] && !isset($REQUEST['flag']))
    echo "selected = 'true'";
echo '>All</option>';

if($APObj){
    foreach($APObj as $rw){
        $selected = ($REQUEST['ALC_Id'] == $rw->ALC_Id) ? 'selected = "true"' : '';
        echo '<option '.$selected.' value="'.$rw->ALC_Id.'">'. trim(str_replace("&","&amp;",$rw->ALC_Name)).'</option>';
    }
}
echo '</complete>';
?>