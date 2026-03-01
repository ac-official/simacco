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

if($REQUEST['PL_Id'])
    $filter .= " AND AP.PL_Id = ".$REQUEST['PL_Id'];

if($REQUEST['ALC_Id'])
    $filter .= " AND AL.ALC_Id = ".$REQUEST['ALC_Id'];

if($REQUEST['ST_Id'])
    $filter .= " AND ST.ST_Id = ".$REQUEST['ST_Id'];

if($REQUEST['CT_Id'])
    $filter .= " AND CT.CT_Id = ".$REQUEST['CT_Id'];

if($REQUEST['mask']) {
    $mask           = $REQUEST['mask'];
    $mask_filter    = " AND SR.SR_Name LIKE '".$mask."%' ";
}

if($REQUEST['SR_Id'] && is_numeric($REQUEST['SR_Id'])) {
    $filter = " SR.SR_Status = 1 AND AP.PL_Status = 1 AND ST.ST_Status = 1 $filter AND CT.CT_Status = 1 AND AL.ALC_Status = 1 ORDER BY SR.SR_Name LIMIT 0,29) 
                UNION 
                (SELECT SR.SR_Name,SR.SR_Id 
                    FROM addr_streets AS SR 
                    LEFT JOIN addr_places AS AP ON SR.PL_Id = AP.PL_Id 
                    LEFT JOIN addr_locations AS AL ON AP.ALC_Id = AL.ALC_Id 
                    LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
                    LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id
                    WHERE SR.SR_Status = 1 AND AP.PL_Status = 1 AND ST.ST_Status = 1 AND CT.CT_Status = 1 AND AL.ALC_Status = 1 
                    AND SR.SR_Id = ".$REQUEST['SR_Id']." )";   
} else {
   $filter  = " AP.PL_Status = 1 AND AL.ALC_Status = 1 $filter $mask_filter ORDER BY AP.PL_Name LIMIT 0,30) ";
}

$GeneralObj->unionValues('SR.SR_Name,SR.SR_Id ', 'addr_streets AS SR 
            LEFT JOIN addr_places AS AP ON SR.PL_Id = AP.PL_Id  
            LEFT JOIN addr_locations AS AL ON AP.ALC_Id = AL.ALC_Id LEFT JOIN cities AS CT ON CT.CT_Id = AL.CT_Id
            LEFT JOIN states AS ST ON ST.ST_Id = CT.ST_Id', $filter );
$APObj  = $GeneralObj->DataArray; 

echo '<complete>
<option value = "0" ';
if(!$REQUEST['SR_Id'] && !$REQUEST['mask'] && !$REQUEST['flag'])
    echo "selected = 'true'";
echo '>All</option>';

if($APObj){
    foreach($APObj as $rw){
        $selected = ($REQUEST['SR_Id'] == $rw->SR_Id) ? 'selected = "true"' : '';
        echo '<option '.$selected.' value="'.$rw->SR_Id.'">'.(trim(str_replace("&","&amp;",$rw->SR_Name))).'</option>';
    }
}
echo '</complete>';
?>