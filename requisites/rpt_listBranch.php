<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH."preTallyClass/LocationClass.php");
$LocationObj = new LocationClass();

if($preTally_user_ofid == 1) {
    $flds     =   'OF.OF_Name,LC.LC_Id,LC.US_Id,LC.LC_Name,LC.OF_Id,LC.AP_Id,LC.LC_Street,LC.LC_Building,LC.LC_Phone,LC.LC_Pincode,LC.CT_Id,LC.ST_Id ,LC.CN_Id,LC.LC_Comments,LC.LC_Status ';
    $filter   =   'AS LC, offices AS OF WHERE LC.OF_Id=OF.OF_Id AND LC.LC_Status != 5 ORDER BY LC.LC_Name';
}else {
    $flds     =   '*';
    $filter   =   'WHERE OF_Id = '.$preTally_user_ofid.' AND LC.LC_Status != 5 ORDER BY LC_Name';
}
$LocationObj->viewLocations($flds,$filter);
$LC_Obj = $LocationObj->LocationArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>'
. '<row id = "all"><cell>ALL</cell></row>';
    if($LC_Obj) {
        $j = 1;
      
        foreach($LC_Obj as $rw) {
            echo '<row id="'.$rw->LC_Id.'">
                <cell name="LC_Name">'.$rw->LC_Name.'</cell>
            </row>';
            $j++;
        }
    }
		  
echo '</rows>';
?>