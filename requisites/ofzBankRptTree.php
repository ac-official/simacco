<?php
require_once($BASEPATH . 'includes/functions.php');
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/GeneralClass.php");
include_once($BASEPATH . "preTallyClass/LocationClass.php");
include_once($BASEPATH . "preTallyClass/DepartmentClass.php");
require_once($BASEPATH . "preTallyClass/BankBSClass.php");
include_once($BASEPATH . "preTallyClass/UserClass.php");

$GeneralObj     = new GeneralClass();
$LocationObj    = new LocationClass();
$UserObj        = new UserClass();
$DepartmentObj  = new DepartmentClass();
$BankObj        = new BankBSClass();

$filterHR['OF'] = 1;
if($REQUEST['f'] == 'HR') {
    $ACLReq = $ACL_Obj->ACL_HR;
}
if($REQUEST['f'] == 'BS') {
    $ACLReq = $ACL_Obj->ACL_BSheet;
}

if($ACLReq != 5) 
    $filterHR = filterHR($ACLReq, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);

$GeneralObj->ViewDetails(' OF_Id, OF_Name ', 'offices', $filterHR['OF'] ,' OF_Name ');
$OF_Obj = $GeneralObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<tree id="0">';
    if($OF_Obj) {
        $j = 1;
       
        if($REQUEST['r'] == 'OF_O') {
            foreach($OF_Obj as $rwOF) { 
                echo '<item text="'.$rwOF->OF_Name.'" id="OF_'.$rwOF->OF_Id.'" im1="../../../../../images/icon/company.png"  im2="../../../../../images/icon/company.png">';           
                    $LocationObj->viewLocations('*',' WHERE OF_Id='.$rwOF->OF_Id.' AND LC_Status != 5 '.$filterHR['LC'].' ORDER BY LC_Name');
                    $LC_Obj = $LocationObj->LocationArray;
                        if($LC_Obj) {
                        $k = 1;
                        foreach($LC_Obj as $rwLC) {
                            if($rwLC->LC_Status==2)           
                            $style_text='style="color: red;"';
                            else $style_text='style="color: black;"';                            
                            echo '<item '.$style_text.' text="'.$rwLC->LC_Name.'" id="OF_'.$rwOF->OF_Id.'-LC_'.$rwLC->LC_Id.'" im1="../../../../../images/icon/office.gif"  im2="../../../../../images/icon/office.gif">';                      
                                $BankObj->viewBankAccounts($preTally_user_ofid,' AND LC_Id = '.$rwLC->LC_Id);
                                $OB_Obj = $BankObj->BankBSArray;
                                    if($OB_Obj) {
                                    $k = 1;
                                        foreach($OB_Obj as $rwBA) {
                                            $fontcolrstyle  = ($rwBA->BA_Status == 1) ? '' : 'style="color: red;"'; //30-12-2025
                                            echo '<item '.$fontcolrstyle.' text="'.$rwBA->BA_DispName.'" id="OF_'.$rwOF->OF_Id.'-LC_'.$rwLC->LC_Id.'-BA_'.$rwBA->BA_Id.'" im1="../../../../../images/icon/office.gif"  im2="../../../../../images/icon/office.gif">';                      
                                            echo '</item>';
                                        }
                                    }
                            echo '</item>';
                        }
                    }
                echo '</item>';
            }
        } 
    }
echo '</tree>';

?>