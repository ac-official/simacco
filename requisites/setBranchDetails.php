<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/UserClass.php");
require_once($BASEPATH . "preTallyClass/OfficeClass.php");
require_once($BASEPATH . "preTallyClass/CashBSClass.php");
require_once($BASEPATH . "preTallyClass/OldStockClass.php");

$USObj = new UserClass();
$OffObj = new OfficeClass();
$CashBSObj = new CashBSClass();
$OldStockObj  = new OldStockClass();

$offAdm = $OffObj->offzAdmin($preTally_user_ofid);

$USObj->viewEmpId($preTally_user_id);
$EMP_Obj = $USObj->UserArray;
//echo $EMP_Obj[0]->US_EMPID;
$US_EMP = explode('-',$EMP_Obj[0]->US_EMPID);
$ArrLen = count($US_EMP);

//$CashBSObj->viewOldStockAmounts($preTally_user_lcid);
//$Cash_BSObj = $CashBSObj->CashBSArray;
//
//$BS_Amount = $Cash_BSObj[0]->BS_Amount;


//$OSId = $OldStockObj->getOldStockId($preTally_user_lcid);
//
//
//if($OSId){
//    $type = 'hidden';
//}else{
//    $type = 'input';
//}

//if($Cash_BSObj){
//    $TR_Track = $Cash_BSObj[0]->TR_Track;
//}else {
    $Rid = $preTally_user_lcid+222;
    $TR_Track = 'OS_'.strtoupper(substr($preTally_user_lcname,0,3)).$Rid ;
//}
//echo $offAdm.'--'.$preTally_user_id;
    
$filter = 'LC_Id = '.$preTally_user_lcid.' ';
$OS_Status = $OldStockObj->verifyOldStockStatus($filter);


echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

//<option value="0" label="Select Mainhead Type" selected="true" />
// <item type="hidden" name="IT_Id" value="219"/>
echo '<items>
	<item type="settings" position="label-left" labelWidth="180" inputWidth="200" noteWidth="150" offsetLeft="50"  offsetTop="20"/>
      
        <item type="template" name=""  labelWidth="500" label="Please fill the below details if you are an authorised person or you can skip. " >
        
        </item>
        
        <item type="input" name="OB_OpenBal" label="Cash Opening Balance : " required="true"  validate="ValidNumeric" >
            <note width="150">Cash In Hand </note>
        </item> 
        
        <item type="input" name="OS_OpenBal" label="Old Stock Amount : " required="true"  validate="ValidNumeric" >
            <note width="250">Old Stock Amount till 31.03.2025</note>
            </item> 
        
        <item type="hidden" name="TR_Track" label="Track Id : " value = "'.$TR_Track.'" >
        
        </item> 
        
        <item type="block" width="300" offsetTop="5">
			<item type="button" value="Save" name="SaveBranchDetails"/>
			<item type="newcolumn"/>
			<item type="button" value="Skip" name="SkipBranchDetails"/>
		</item>
       </items>';
?>