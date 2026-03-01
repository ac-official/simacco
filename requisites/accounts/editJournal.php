<?php
/**
* Save or edit single balance sheet entry into one or more journal entry 
* balance sheet entry id and journal parent id are of input (save time and edit time)
* created by Bilin @ 06-08-2025
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
$parent_id 		= (isset($_REQUEST['jid'])) ? (int)$_REQUEST['jid']:0; // journal parent id
$bsid 			= (isset($_REQUEST['bsid'])) ? (int)$_REQUEST['bsid']:0; // balance sheet id
$jid 			= (isset($_REQUEST['id'])) ? (int)$_REQUEST['id']:0; // journal id
$flags 			= (isset($_REQUEST['flags'])) ? (int)$_REQUEST['flags']:0; // flag id(9/6)
$office_id 		= 0;
$location_id 	= 0;
$track_id 		= 0;
$date_entry		= '';
$amount 		= 0;
$converted 		= 0;
$ratio 			= 0;
$amt_type 		= 0;
$ba_id 			= 0;
$ledger_id 		= 0;
$ledger_name 	= '';
$company_id 	= 0;
$branch_id 		= 0;
$olddetail 		= '';
$Sibling_id 	= 0;
$remarks 		= '';
$confirm_entry	= '';
$temp_id 		= '';
$vendor_id 		= 0;
$ie_type 		= 0; 
$trackno 		= '';

// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();
$getBsData 		= $accObj->getItemEntry($bsid); // get the balance sheet entry details
if ( !empty($getBsData) ) {
	
	$olddetail 	= "<strong> ITEM:- </strong>".$getBsData->IT_Name ." - ".filter_var($getBsData->DS_Description, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)." &emsp;&emsp;&emsp;&emsp;<strong> BRANCH:- </strong>".$getBsData->LC_Name."<br/><strong>AMOUNT:- </strong>".number_format($getBsData->BS_Amount,2)."&emsp;&emsp;&emsp;&emsp;<strong>DATE:- </strong>".date('M d, Y', strtotime($getBsData->BS_Date)).($getBsData->TR_Track != '' ? ".&emsp;&emsp;&emsp;&emsp;<strong> TRACK NO:- </strong>".$getBsData->TR_Track:"");//. "&emsp;".($getBsData->MH_Type ==2 ? "-&emsp;(Expense)" :"-&emsp;(Income)");
	if ($getBsData->BS_BranchTo > 0) {
		$olddetail .= "&emsp;&emsp;&emsp;&emsp;<strong> TO BRANCH:- </strong>".$getBsData->branch_name;
	}
	$olddetail 	= htmlentities($olddetail);
	$company_id = ($getBsData->company_id > 0) ? $getBsData->company_id:$getBsData->OF_Id;
	$branch_id 	= ($getBsData->BS_BranchTo > 0) ? $getBsData->BS_BranchTo:$getBsData->LC_Id;
	$ledger_id  = ($getBsData->ledger_id > 0) ? $getBsData->ledger_id:0;
	$ledger_name= ($getBsData->ledger_id > 0) ? $getBsData->ledger:'';
	$amount 	= $getBsData->BS_Amount;
	$amount1 	= $getBsData->BS_Amount;
	$office_id 	= $getBsData->OF_Id;
	$location_id= $getBsData->LC_Id;
	$track_id 	= $getBsData->TR_Id;
	$date_entry	= $getBsData->BS_Date;
	$amt_type 	= $getBsData->amt_type;
	$vendor_id  = $getBsData->vendor_id;
	$ba_id 		= $getBsData->BA_Id;
	$ie_type 	= $getBsData->MH_Type;
	$ie_type1 	= $getBsData->MH_Type;
	$trackno 	= $getBsData->TR_Track;
	if ($getBsData->ie_type > 0) {
		$ie_type 	= $getBsData->ie_type;
		$ie_type1 	= $getBsData->ie_type;
	}
}
// list all vendors
$list_vendor 	= $accObj->getCustomField("acc_vendors", "id,name,gst_no", "WHERE status=1");
if ($parent_id > 0) {
	//format vendor list
	$vendors  	= [];
	foreach ($list_vendor as $rw) {
		$vendors[$rw->id] = $rw->name." - (".$rw->gst_no.")";
    }
	$getJournls	= $accObj->getJournals($parent_id);
	if (!empty($getJournls)) {
		$i 			= 0;
		$journals 	= [];
		foreach($getJournls AS $ky => $rv) {
			if ($jid == $rv->id) {
				$track_id 		= $rv->track_id;
				$trackno 		= ($rv->trackno != "") ? $rv->trackno : $trackno;
				$company_id 	= $rv->company_id;
				$branch_id 		= $rv->branch_id;
				$bsid 			= $rv->balance_sheet_id;
				$ledger_id 		= $rv->ledger_id;
				$amount1 		= $rv->amount;
				$amt_type 		= $rv->amt_type;
				$ba_id 			= $rv->ba_id;
				$date_entry 	= $rv->date_entry;
				$ledger_name 	= $rv->ledger;
				$remarks 		= $rv->remarks;
				$vendor_id 		= $rv->vendor_id;
				$temp_id 		= "tmp".$i;
				$converted 		= $rv->converted;
				$ratio 			= ($rv->converted > 0) ? ($rv->converted/$rv->amount):0;
				$ie_type1 		= $rv->ie_type;
				$ie_type 		= $rv->ie_typel;
			}
			$ratio1 		= ($rv->converted > 0) ? ($rv->converted/$rv->amount):0;
			$vendor_name 	= (isset($vendors[$rv->vendor_id])) ? $vendors[$rv->vendor_id] :'';
			$journals[$i] 	= ['ledger_id'=>$rv->ledger_id, 'ledger_name'=>$rv->ledger, 'tocompany_id'=>$rv->company_id, 'tobranch_id'=>$rv->branch_id, 'tocompany_name'=>htmlentities($rv->company), 'tobranch_name'=>htmlentities($rv->branch), 'amount'=>round($rv->amount,4), 'converted'=>round($rv->converted,4), 'ratio'=>round($ratio1 ,4), 'remarks'=>htmlentities($rv->remarks), 'id'=>'tmp'.$i, 'server_id'=>$rv->id, 'vendor_id'=>$rv->vendor_id, 'vendor_name'=>$vendor_name, 'trackno'=>$rv->trackno];
			$i++;
		}
		$confirm_entry	= json_encode($journals);
	} else {
		$parent_id 	= 0;
		$jid 		= 0;
	}
}

// get all active office list
$list_office 	= $accObj->getCustomField('offices', 'OF_Id, OF_Name, Sibling_id', ' WHERE OF_Status = 1');
foreach ($list_office as $value) {
	if ($value->OF_Id == $preTally_user_ofid) {
		$Sibling_id  = $value->Sibling_id;
		break;
	}
}

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo "<items>";
	echo "<item type='hidden' name='parent_id' value='".$parent_id."'/>";
	echo "<item type='hidden' name='temp_id' value='".$temp_id."'/>";
	echo "<item type='hidden' name='balance_sheet_id' value='".$bsid."'/>";
	echo "<item type='hidden' name='office_id' value='".$office_id."'/>";
	echo "<item type='hidden' name='location_id' value='".$location_id."'/>";
	echo "<item type='hidden' name='branch_id' value='".$branch_id."'/>";
	echo "<item type='hidden' name='track_id' value='".$track_id."'/>";
	//echo "<item type='hidden' name='date_entry' value='".$date_entry."'/>";
	//echo "<item type='hidden' name='amt_type' value='".$amt_type."'/>";
	//echo "<item type='hidden' name='ba_id' value='".$ba_id."'/>";
	echo "<item type='hidden' name='ledger_id' value='".$ledger_id."'/>";
	echo "<item type='hidden' name='flags' value='".$flags."'/>"; //05-01-2026
	echo "<item type='hidden' name='total_amount' value='".(float)$amount."'/>";
	echo "<item type='hidden' name='confirm_entry' value='".$confirm_entry."'/>";

	echo "<item type='label' className='custlabels' width='780'  label='OLD ACCOUNT ENTRY DETAILS'></item>";

	// Existing data or item details shown
	//echo "<item type='fieldset' label='Old Account Entry Details' offsetLeft='10' offsetTop='0' width='735'  >";
	echo "<item type='template' style='line-height: 2;' value='".$olddetail."' offsetTop='0' offsetLeft='10' width='790' />";
	//echo "</item>";

	echo "<item type='label' className='custlabels' width='740'  label='NEW LEDGER ENTRY'></item>";

	//Select new details for save
	echo "<item type='fieldset' label='Search Ledger' offsetLeft='10' offsetTop='2' width='775' >";		
		echo "<item type='input' name='search_ledger' width='710' validate='^[-_0-9a-zA-Z ]+$' placeholder='Search Ledger Name Here' >
			<note width='300'>SEARCH LEDGER NAME</note>
		</item>";

		echo "<item type='container' name='ledgerContainer' id='ledgerContainer' width='710' className='accPopContainer' offsetTop='8'></item>";
		
	echo "</item>";

	echo "<item type='settings' position='label-top' width='790' labelWidth='150' inputWidth='360' noteWidth='150' offsetLeft='10' />";

	echo "<item type='newcolumn'/>"; 
	echo "<item type='template' style='font-weight:bold;padding-left:3px;' name='ledger_name' label='Ledger Name:' value='".$ledger_name."' inputWidth='275'></item>";
	echo "<item type='newcolumn'/>";

	echo "<item type='input' name='amount' label='Amount:' validate='^[.0-9 ]+$' value='".round($amount1,4)."' inputWidth='100' labelWidth='100'></item>";
	echo "<item type='newcolumn'/>"; 
	echo "<item type='input' name='amount_ratio' label='Ratio:' validate='^[.0-9 ]+$' value='".round($ratio,4)."' inputWidth='70' labelWidth='70'></item>";
	echo "<item type='newcolumn'/>";
	echo "<item type='input' name='converted' label='Converted:' validate='^[.0-9 ]+$' value='".round($converted,4)."' inputWidth='100' labelWidth='100'></item>";
	echo "<item type='newcolumn'/>"; 

	echo "<item type='button' value='Add +' name='addAcJournal' offsetTop='22' /><item type='newcolumn'/>";
	echo "<item type='button' value='Clear' name='clearAcJournal' offsetTop='22' />";

	echo "<item type='newcolumn'/>";

	echo "<item type='combo' name='company_id'  label='Company Name:' readonly='true'  required='true'  inputWidth='345'>";		
		echo "<option value='0' ".($company_id > 0 ? "":"selected='true'")." text='Select Company'/>";
		foreach ($list_office as $rw) {
			if (($Sibling_id > 0 && $Sibling_id == $rw->Sibling_id) || $rw->OF_Id == $preTally_user_ofid) {

				$selected 		= ($rw->OF_Id == $company_id) ? "selected='true'":"";
            	echo "<option value='".$rw->OF_Id."' ".$selected." text='".$rw->OF_Name."'/>";
			}        	
        }
    echo "</item>";
    echo "<item type='newcolumn'/>"; 

    echo "<item type='combo' name='branch'  label='Branch Name:' required='true' inputWidth='375'>";		
		echo "<option value='0' selected='true' text='Select Branch'/>";
    echo "</item>";
    echo "<item type='newcolumn'/>"; 

    echo "<item type='combo' name='vendor_id'  label='Vendor / Debtor / Creditor' inputWidth='350' labelWidth='230'>";
		echo "<option value='' ".($vendor_id > 0 ? "":"selected='true'")." text='Select'/>";
        foreach ($list_vendor as $rw) {
        	$selected 		= ($rw->id == $vendor_id) ? "selected='true'":"";
            echo "<option value='".$rw->id."' ".$selected." text='".$rw->name." - (".$rw->gst_no.")'/>";
        }
    echo "</item>";

    echo "<item type='newcolumn'/>";  
    echo "<item type='input' name='trackno' label='Track No'  value='".$trackno."' inputWidth='200' labelWidth='200'></item>";
	echo "<item type='newcolumn'/>"; 

    echo "<item type='input' name='remarks' label='Remarks:' rows='2' inputWidth='775' value='".$remarks."' validate='^[-_.0-9a-zA-Z ]+$' />";
    echo "<item type='newcolumn'/>";
    
    echo "<item type='fieldset' label='Confirm New Ledger Entry' offsetLeft='10' offsetTop='10' width='780' >";
	    echo "<item type='template' name='journalconfirmmsg' value='Please add ledger entry and save' width='700' />";
	    echo "<item type='container' name='journalContainer' id='journalContainer' width='720' className='accPopContainer' offsetTop='8'></item>";    
	    echo "<item type='block' width='760' offsetTop='1' style='float:right;padding-left:0px !importent;' offsetLeft='0' >";
	    	if ($ie_type > 0) {
		    	echo "<item type='combo' name='ie_type'  label='Add/ Subtract'  offsetLeft='0' inputWidth='100' labelWidth='100'  offsetTop='11'  position='label-left'>";
		    		if ($ie_type == 1) {
		    			echo "<option value='1' ".($ie_type1 == 1 ? "selected='true'":"")." text='Add 1'/>";
						echo "<option value='2' ".($ie_type1 == 2 ? "selected='true'":"")." text='Subtract 2'/>";
		    		} else {
		    			echo "<option value='1' ".($ie_type1 == 1 ? "selected='true'":"")." text='Subtract 1'/>";
						echo "<option value='2' ".($ie_type1 == 2 ? "selected='true'":"")." text='Add 2'/>";
		    		}
			    echo "</item>";
		    	echo "<item type='newcolumn'/>"; 
		    } else {
		    	echo "<item type='hidden' name='ie_type' value='".$ie_type."'/>";
		    }
		    echo "<item type='combo' name='amt_type'  label='Amount Type'  offsetLeft='20' inputWidth='100' labelWidth='90'  offsetTop='11'  position='label-left'>";	    		
    			echo "<option value='1' ".($amt_type == 1 ? "selected='true'":"")." text='Bank'/>";
				echo "<option value='2' ".($amt_type == 2 ? "selected='true'":"")." text='Cash'/>";	    		
		    echo "</item>";
		    echo "<item type='newcolumn'/>";
			$list_bnkacc 	= $accObj->getCustomField("bank_accounts", "BA_Id,BA_DispName,OF_Id", "WHERE BA_Status=1 AND OF_Id=".$office_id);
			echo "<item type='combo' name='ba_id'  label='Bank Accounts' validate='^[0-9]+$' offsetLeft='20' inputWidth='150' labelWidth='100'  offsetTop='11'  position='label-left'>";
	        echo "<option value=''  ".($ba_id <= 0 ? "selected='true'":"")." text='Select'/>";
	        foreach ($list_bnkacc as $rw) {        	
	            echo "<option value='".$rw->BA_Id."' ".($ba_id == $rw->BA_Id ? "selected='true'":"")." text='".$rw->BA_DispName."'/>";
	        }
	    	echo "</item>";
	    	echo "<item type='newcolumn'/>";
	    	echo "<item type='calendar' name='date_entry' labelWidth='60' offsetLeft='0' offsetTop='11' position='label-left' label='Date' value='".$date_entry."' serverDateFormat='%Y-%m-%d' dateFormat='%d.%m.%Y' required='true'  readonly='true' inputWidth='100'></item>";
	    	echo "<item type='newcolumn'/>";
	    	echo "<item type='template' name='total_camt' labelWidth='100' offsetLeft='20' offsetTop='11' position='label-left' label='Total Amount:' value='0.00' inputWidth='100'></item>";
	    	echo "<item type='newcolumn'/>";	
	    			
			echo "<item type='button' value='Save' style='float:right;' offsetLeft='5' name='saveAcJournal'/>";	
			if ($parent_id > 0) {	
				echo "<item type='newcolumn'/>";			
				echo "<item type='button' value='Delete All' style='float:right;margin-right:10px;' offsetLeft='0' name='deleteAcJournal'/>";					
			}
					
	    echo "</item>";
	echo "</item>";
    //echo "<item type='settings' position='label-left' labelWidth='125' inputWidth='400' noteWidth='300' offsetLeft='10' offsetTop='10' />";	

    echo "<item type='newcolumn'/>";
echo "</items>";
?>
