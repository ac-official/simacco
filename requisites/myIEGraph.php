<?php
//error_reporting(E_ALL ^ E_NOTICE);
//require_once('../includes/sessions.php');

//$role = 'adm';
//$ajax = 'false';

//die('Anoop');
require_once($BASEPATH . 'preTallyClass/ReportClass.php');

$ReportObj = new ReportClass();
$ReportObj->myIEReport($REQUEST['ie'],$preTally_user_id);
$RP_Obj = $ReportObj->ReportArray;

$colorArray = Array('#ee4339','#ee9336','#eed236','#d3ee36','#a7ee70','#58dccd');


if(isset($RP_Obj)) {
    
    $j = 1;
    
    $text= '<data>';
    foreach($RP_Obj as $rw) {
        
        $text.='<item id="'.$j.'">';
        $text.='<sales>'.round($rw->IE,2).'</sales>';
        $text.='<color>'.$colorArray[$j-1].'</color>';
        $text.='<month>'.substr($rw->MName,'0','3').'</month>';
        $text.='</item>';
        
        $j++;
    }
    $text.= '</data>';
}
echo $text;

?>
