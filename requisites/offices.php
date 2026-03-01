<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();
$filter     =  1;
$sibling_id = (isset($_REQUEST['sibling_id'])) ? (int)$_REQUEST['sibling_id']: 0; //27-03-2025
if($preTally_user_ofid == 1) {
    $filter   =  1;
}else if($sibling_id > 0) { //27-03-2025
    $filter   =   'Sibling_id = '.$sibling_id;
} else {

    /*$sibling_id = $GeneralObj->getValue('offices', 'Sibling_id','WHERE OF_Id = '.$preTally_user_ofid);
    if ($sibling_id > 0) {

        $filter   =   'Sibling_id = '.$sibling_id;
    } else {*/

        $filter   =   'OF_Id = '.$preTally_user_ofid;
    //}
}
$GeneralObj->ViewDetails(' * ', 'offices', $filter,' OF_Name ');
$OF_Obj = $GeneralObj->DataArray;

echo '<complete>';
/*if($selid==0)
{*/
echo '<option value="" selected="true" >Select Company </option>';
/*}*/
if($OF_Obj){
   
    foreach($OF_Obj as $rw){$selected='';
       /* if($selid==$rw->OF_Id)$sel='selected';
        else  $sel='';*/
        //if($rw->OF_Id == $preTally_user_ofid ) { $selected = ' selected="true" '; }
        echo '<option value="'.str_replace("&","&amp;",$rw->OF_Id).'"  '.$selected.' >'.$rw->OF_Name.'</option>';
    }
}
echo '</complete>';
?>