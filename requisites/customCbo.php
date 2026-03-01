<?php
/**
 * created BY Bilin At 17-06-2025 
 * Common combo box data listing in xml format 
 * combo list based on the flag input default break type
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

// base  inputs for specify the combo box
$flag 		= (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$mask       = (isset($_REQUEST['mask'])) ? (string)trim($_REQUEST['mask']):'';
$selted 	= (isset($_REQUEST['selted'])) ? (int)$_REQUEST['selted']:0;

echo '<complete>';
if ($flag == 0) {
	// break type base class declaration
	include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
	$btObj			= new BreakTimeClass();

	// list break types
	$break_types 	= $btObj->getAllBreakTypes('1');

	// find the current time
	//$cu_time 		= strtotime(date('H:i'));
	//$setsel 		= 0;
	echo '<option value="0" selected="true">All</option>';
	foreach ($break_types as $rw) {
    	$selected 		= '';    	
    	/*if ($break_id > 0) {
    		$setsel 	= 1;
    		$selected 	= ($break_id == $rw->id) ? 'selected="true"':'';
    	} else if ($cu_time >= strtotime($rw->from_time) && $cu_time <= strtotime($rw->to_time)) {
    		$setsel 	= 1;
    		$selected 	= 'selected="true"';
    	} else if ($rw->from_time == "" && $setsel == 0) {
    		$selected 	= 'selected="true"';
    	}*/
        echo '<option value="'.$rw->id.'" '.$selected.'>'.$rw->title.'</option>';
    }

} else if ($flag == 2 || $flag ==3 || $flag ==4 || $flag ==5 || $flag ==6 || $flag ==7) {
	// accounts settings related class
	include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
	$accObj		= new AccountsClass();
	$parent_id  = (isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != "") ? (string)$_REQUEST['parent_id']:"";
	$type  		= (isset($_REQUEST['type'])) ? trim($_REQUEST['type']):"";
	$status  	= (isset($_REQUEST['status'])) ? trim($_REQUEST['status']):"1";
	$group_id 	= (isset($_REQUEST['group_id'])) ? (int)$_REQUEST['group_id']:0;
	$ledger_id 	= (isset($_REQUEST['ledger_id'])) ? (int)$_REQUEST['ledger_id']:0;
	$item_id 	= (isset($_REQUEST['item_id'])) ? (int)$_REQUEST['item_id']:0;
	$delete_id 	= (isset($_REQUEST['delete_id'])) ? (int)$_REQUEST['delete_id']:0;
	$seltid 	= 0;
	if ($mask == "") echo '<option value="" selected="true">Select</option>';

	if ($flag == 2) {
		$results 	= $accObj->ListGroupTypes();
	} else if ($flag == 3) {
		$results 	= $accObj->ListGroups(['status'=>$status, 'type'=>$type, 'parent_id'=>$parent_id]);
		$seltid 	= $group_id;
	} else if ($flag == 4) {
		$results 	= $accObj->listLedger(['status'=>$status, 'parent_id'=>$parent_id, 'group_id'=>$group_id, 'office_id'=>$preTally_user_ofid, 'ledger_id'=>$ledger_id ]);
		$seltid 	= $ledger_id;
	} else if ($flag == 5) {
		// items listing
		$condtion	= (isset($_REQUEST['status']) && $status != "") ? " AND IT_Status='".$status."'":" AND IT_Status != '4' "; // (IT_Status='1' OR IT_Status='0')
		$condtion	.= ($parent_id > 0) ? " AND SH_Id = '".$parent_id."'" :"";
		$condtion	.= ($mask != "") ? " AND IT_Name LIKE '%".$mask."%'" :"";
		$results 	= $accObj->getCustomField("items","IT_Id As id, IT_Name AS title, IT_Status AS status"," WHERE OF_Id='".$preTally_user_ofid."' ".$condtion." ORDER BY IT_Name");
		$seltid 	= $item_id;
	} else if ($flag == 6) {
		// Parent ledgers with type-sub group and ledger name
		$results 	= $accObj->listLedgerCbo(['status'=>$status, 'parent_id'=>$parent_id, 'group_id'=>$group_id, 'office_id'=>$preTally_user_ofid ]);
		$seltid 	= $ledger_id;
	} else if ($flag == 7) {
		// all sub group with parent group and type name
		$results 	= $accObj->listGroupsCbo(['status'=>$status, 'type'=>$type, 'parent_id'=>$parent_id ]);
		$seltid 	= $ledger_id;
	}
	if (!empty($results)) {
		if ($flag == 2) {
			foreach ($results as $rk=>$rw) {

				echo '<option value="'.$rk.'">'.$rw.'</option>';
			}
		} else {
			foreach ($results as $rw) {
				if ($delete_id == $rw->id) {
					continue;
				}
				$selected 		= ($seltid == $rw->id) ? 'selected="true"' : ''; 
				$optCss 		= ($rw->status == 1 || $flag == 5) ? '' : 'css="color:red"';
				echo '<option value="'.$rw->id.'" '.$selected.' '.$optCss.'>'.$rw->title.'</option>';
			}
		}
	}
} else if ($flag == 9) {
	// list all company names combo @ 05-01-2026
	include_once($BASEPATH . "preTallyClass/GeneralClass.php");
	$GeneralObj = new GeneralClass();
	include_once($BASEPATH . "preTallyClass/BreakTimeClass.php");
	$btObj			= new BreakTimeClass();
	// get all offices
	$filters  	= '1';	
	$filters	.= ($mask != "") ? " AND OF_Name LIKE '%".$mask."%'" :"";
	$GeneralObj->getComboDetails('OF_Id, OF_Name','offices',$filters,'OF_Name');
	$OF_Obj 	= $GeneralObj->DataArray;
	if($OF_Obj) {
   
	    foreach($OF_Obj as $rw){
	    	$selected = ($selted == $rw->OF_Id) ? 'selected="true"' :'';
	        echo '<option value="'.$rw->OF_Id.'"  '.$selected.'>'.$rw->OF_Name.'</option>';
	    }
	}
}





echo '</complete>';
?>