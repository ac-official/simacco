<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/MainheadClass.php");
include_once($BASEPATH . "preTallyClass/SubheadClass.php");
include_once($BASEPATH . "preTallyClass/ItemClass.php");

$MainheadObj    = new MainheadClass();
$SubheadObj     = new SubheadClass();
$ItemObj        = new ItemClass();

$MainheadObj->viewMainheads(' WHERE (MH_Type=1 || MH_Type=2) AND MH_Status=1 GROUP BY MH_Type ORDER BY MH_Type');
$MH_Obj = $MainheadObj->MainheadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<tree id="0">';
    if($MH_Obj) {
        $j = 1;
	foreach($MH_Obj as $rwMH) {


            echo '<item text="'.array_pop(explode(' ', $rwMH->MH_Name)).'" id="MH_'.$rwMH->MH_Id.'">'; 
            
                if($rwMH->MH_Type == 1) $MHID = ' (MH_Id=1 || MH_Id=3) ';
                if($rwMH->MH_Type == 2) $MHID = ' (MH_Id=2 || MH_Id=4) ';
            
                $SubheadObj->viewSubheads(' WHERE '.$MHID.' AND SH_Status=1 ORDER BY SH_Name');
                $SH_Obj = $SubheadObj->SubheadArray;
                
                if($SH_Obj) {
                    $k = 1;                  
                    foreach($SH_Obj as $rwSH) {                       
                        $data = '';
                        $ItemObj->myMapItem($preTally_user_ofid);    
                        $Map_Obj = $ItemObj->ItemMapArray;

                        $find       = array('[',']','"',"'");
                        $replace    = array('','','','');
                        $mapItem    = str_replace($find, $replace, $Map_Obj[0]->IC_Map);
                        if($mapItem == '') $mapItem = '""';

                        echo $ItemObj->viewItems('IT_Id, IT_Name',' WHERE ((SH_Id='.$rwSH->SH_Id.' AND OF_Id = '.$preTally_user_ofid.' ) OR (SH_Id='.$rwSH->SH_Id.' AND IT_Id IN ('.$mapItem.')))  AND IT_Status=1 ORDER BY IT_Name');
                        $IT_Obj = $ItemObj->ItemArray;
                        if($IT_Obj) {
                            $l = 1;
                            foreach($IT_Obj as $rwIT) {
                                $data .=  '<item text="'.$rwIT->IT_Name.'" id="IT_'.$rwIT->IT_Id.'"></item>';
                            }
                        }
                        if($data)
                            echo '<item text="'.$rwSH->SH_Name.'" id="SH_'.$rwSH->SH_Id.'">'.$data.'</item>';    
                    }                  
                }
            echo '</item>';
        }
    }
echo '</tree>';
?>