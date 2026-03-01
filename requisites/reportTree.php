<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/GeneralClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "preTallyClass/UserClass.php");

$GeneralObj     = new GeneralClass();
$LocationObj    = new LocationClass();
$UserObj        = new UserClass();

$GeneralObj->ViewDetails(' * ', 'offices', ' 1 ',' OF_Name ');
$OF_Obj = $GeneralObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<tree id="0">';
    //echo '<item text="Offices" id="0">';
        if($OF_Obj) {
            $j = 1;
            foreach($OF_Obj as $rwOF) { 
                echo '<item text="'.$rwOF->OF_Name.'" id="OF_'.$rwOF->OF_Id.'">';           
                    $LocationObj->viewLocations("*"," WHERE OF_Id=".$rwOF->OF_Id." AND LC_Status=1 ORDER BY LC_Name");
                    $LC_Obj = $LocationObj->LocationArray;
                    if($LC_Obj) {
                        $k = 1;
                        foreach($LC_Obj as $rwLC) {
                            echo '<item text="'.$rwLC->LC_Name.'" id="LC_'.$rwLC->LC_Id.'">';                      
                                $UserObj->viewUser(' WHERE LC_Id='.$rwLC->LC_Id.' AND US_Status=1 ORDER BY US_FName');
                                $US_Obj = $UserObj->UserArray;
                                if($US_Obj) {
                                    $l = 1;
                                    foreach($US_Obj as $rwUS) {
                                        echo '<item text="'.$rwUS->US_FName.' '.$rwUS->US_LName.'" id="US_'.$rwUS->US_Id.'"></item>';
                                    }
                                }
                            echo '</item>';
                        }
                    }
                echo '</item>';
            }
        }
    //echo '</item>';
echo '</tree>';
?>