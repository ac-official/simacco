<?php 
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/InventoryClass.php");
$InvObj = new InventoryClass();
if($REQUEST['type']){
$typeID=$REQUEST["typeid"];
$InvObj->getInvTypePattern($typeID);
$pattern_array=$InvObj->PatternReadArray['INVP_PatternMap'];
$old = array('"', "[", "]");
$new   = array("", "", "");
$PatternList = str_replace($old, $new, $pattern_array);
$filter="";
 if ($REQUEST['type']=='sel' ){     
    $filter=" WHERE INV_Id  IN (".$PatternList.")";
 }
 elseif($REQUEST['type']=='filt' && $PatternList!=""){
    $filter=" WHERE INV_Id  NOT IN (".$PatternList.")";
 }  
}else{
    $filter="";
}
$InvObj->listInventoryPatternItems($filter);
$Inv_Obj = $InvObj->InvItemReadArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';        
        if($Inv_Obj) {
            $j = 1;           
            foreach ($Inv_Obj as $rw) {              
                echo '<row id="'.$rw->INV_Id.'">	                                   
                    <cell title=" ">'.$j.'</cell>
                    <cell title=" ">'.$rw->INV_Title.'</cell>
                    <cell title=" "></cell>                     
                    </row>';	
                $j++;
            }
                
            }          
        else {
        }
echo '</rows>';
?>