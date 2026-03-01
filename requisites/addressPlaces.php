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

$filter     = $mask_filter = '';

if($REQUEST['ALC_Id'])
    $filter .= " AND AL.ALC_Id = ".$REQUEST['ALC_Id'];

if($REQUEST['ST_Id'])
    $filter .= " AND ST.ST_Id = ".$REQUEST['ST_Id'];

if($REQUEST['CT_Id'])
    $filter .= " AND CT.CT_Id = ".$REQUEST['CT_Id'];

if($REQUEST['mask']) {
    $mask           = $REQUEST['mask'];
    $mask_filter    = " AND AP.PL_Name LIKE '".$mask."%' ";
}

if($REQUEST['PL_Id'] && is_numeric($REQUEST['PL_Id'])) {
    $filter = "AP.PL_Status = 1 AND ST.ST_Status = 1 $filter AND CT.CT_Status = 1 AND AL.ALC_Status = 1 ORDER BY AP.PL_Name LIMIT 0,29) 
                UNION 
                (SELECT AP.PL_Name,AP.PL_Id 
                    FROM addr_places AS AP 
                    LEFT JOIN addr_locations AS AL ON AP.ALC_Id = AL.ALC_Id 
                    LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                    LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                    WHERE ST.ST_Status = 1 AND CT.CT_Status = 1 AND AL.ALC_Status = 1 
                    AND AP.PL_Id = ".$REQUEST['PL_Id']." )";   
} else {
   $filter  = " AP.PL_Status = 1 AND AL.ALC_Status = 1 $filter $mask_filter ORDER BY AP.PL_Name LIMIT 0,30) ";
}

$GeneralObj->unionValues('  AP.PL_Name,AP.PL_Id ', 'addr_places AS AP LEFT JOIN addr_locations AS AL ON AP.ALC_Id = AL.ALC_Id LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
        LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id', $filter );
$APObj      = $GeneralObj->DataArray; 

echo '<complete>  
<option value = "0" ';
if(!$REQUEST['PL_Id'] && !$REQUEST['mask'] && !$REQUEST['flag'])
echo "selected = 'true'";
echo '>All</option>';

if($APObj){
    foreach($APObj as $rw){
        $selected = ($REQUEST['PL_Id'] == $rw->PL_Id) ? 'selected = "true"' : '';
        echo '<option '.$selected.' value="'.$rw->PL_Id.'">'.(trim(str_replace("&","&amp;",$rw->PL_Name))).'</option>';
    }
}
echo '</complete>';
?>