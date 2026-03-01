<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	//header("Content-type: application/xhtml+xml"); } else {
	//header("Content-type: text/xml");
}

/*echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");*/

require_once($BASEPATH . "preTallyClass/BalanceSheetClass.php");
require_once($BASEPATH . "preTallyClass/LocationClass.php");
require_once($BASEPATH . "requisites/BSItemArrayList.php");

$SHID   =  $REQUEST['SHID'];
$BSID = $REQUEST['BSID'];
$BalSheetObj = new BalanceSheetClass();

$BalSheetObj->viewSavedBSItems($BSID);
$BS_Obj = $BalSheetObj->BalanceSheetArray;//print_r($BS_Obj);

$curField = $updateFields[$SHID];//print_r($BS_Items);
?>

<link rel="stylesheet" type="text/css" href="assets/form/codebase/skins/dhtmlxform_dhx_skyblue.css">
<script src="assets/form/codebase/dhtmlxcommon.js"></script>
<script src="assets/form/codebase/dhtmlxform.js"></script>
<script language="javascript">
    
    var BS_Form_Data = [{
        type		: "settings",
        labelWidth	: 200,
      
        inputWidth	: 200
    },    
    <?php
    
    $sortArray = array();
    foreach ($curField as $key => $value) {
        $sortArray[$BS_Items[$value]['block']][$key] = $value;
    }

    function arraySort($a, $b){
        $priority = array('item','payment','general','user','approvals','');
        return array_search($a, $priority) - array_search($b, $priority);
    } 
    
    function arrayArrange($value) {
        $OItm = $EItm = array();
        $ic = 0;
        foreach ($value as $arraySort) {
            if($ic % 2 ==0) {
                $OItm[$ic] = $arraySort;
            } else {
                $EItm[$ic] = $arraySort;
            }
            $ic ++;
        }
        return array_merge((array)$OItm, (array)$EItm);   
    }

    uksort($sortArray, "arraySort");
    
    foreach ($sortArray as $key => $value) {
        $count = 1;
        $fieldCount = ceil(count($value)/2);
        
        echo '{type: "fieldset", label: " '.ucfirst ($key) .' Details ", inputWidth: "auto", list:[ ';
        foreach (arrayArrange($value) as $keyParam => $valueParam) {

            $itemValue = '';
            $itemValue = $BalSheetObj->getColNamebyId($valueParam,$BS_Obj[0]->$valueParam);
            if($valueParam == 'BS_Amount') 
                $itemValue=$BS_Obj[0]->BS_Amount;
                
            if($valueParam == 'BS_PaidDate') 
                $itemValue=date('Y-m-d H:i:s');    
                

            echo ' { type : "template", name : "'.$valueParam.'", label : "'.$BS_Items[$valueParam]['title'].'", value : "'.$itemValue.'"';
              
            echo '}, ';

            if($fieldCount == $count) 
                echo '{ type:"newcolumn", offset:"20" },';    
            $count++;
        }
        echo ']}, ';
    }
    ?>   
 ];
    BSForm[<?php echo $BSID; ?>] = new dhtmlXForm("BS_Form_<?php echo $BSID; ?>", BS_Form_Data);  
</script>
<div id="BS_Form_<?php echo $BSID; ?>" style="margin: 10px 0;"></div>