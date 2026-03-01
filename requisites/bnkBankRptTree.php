<?php
require_once($BASEPATH . 'includes/functions.php');
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/BankBSClass.php");

$OfficeObj  = new OfficeClass();
$BankObj    = new BankBSClass();

$OfficeObj->viewOffices('OF_Id, OF_Name','WHERE OF_Id = '.$preTally_user_ofid.' ');
$OF_Obj = $OfficeObj->OfficeArray;

$ACLReq = $ACL_Obj->ACL_BSheet;
$filterHR = filterHR($ACLReq, $preTally_user_ofid, $preTally_user_dpid, $preTally_user_lcid, $preTally_user_id);


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<tree id="0">';
    if($OF_Obj) {
        $j = 1;
       
        if($REQUEST['r'] == 'OF_O') {
            foreach($OF_Obj as $rwOF) { 
                echo '<item text="'.$rwOF->OF_Name.'" id="OF_'.$rwOF->OF_Id.'" im1="../../../../../images/icon/company.png"  im2="../../../../../images/icon/company.png">';           
                $BankObj->viewBankAccounts($preTally_user_ofid,$filterHR['LC']);
                $OB_Obj = $BankObj->BankBSArray;
                    if($OB_Obj) {
                    $k = 1;
                        foreach($OB_Obj as $rwOB) {
                            echo '<item text="'.$rwOB->BA_DispName.'" id="BA_'.$rwOB->BA_Id.'" im1="../../../../../images/icon/office.gif"  im2="../../../../../images/icon/office.gif">';                      
                            echo '</item>';
                        }
                    }
                echo '</item>';
            }
        } 
    }
echo '</tree>';

?>