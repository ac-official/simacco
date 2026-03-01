<?php
/**
* Class created by Bilin @ 04-06-2025 last updated @ 10-06-2025
* Account teams reports getting class (cash, bank and other reports functions write here...)
* brief data and excel export full details getting functions here.
*/
require_once("connection.php");
class AccountRptClass {
	var $bsArray; // balance sheet data listing array
	var $bsTotal; // balance sheet data list count
	var $bsAmtTotal; // balance sheet based on the total Amount 

	// transaction summery reports gettting
	function bsTransactionList( $inparms = [])
	{
		extract($inparms);
		$this->bsArray = array(); 
		$this->bsTotal = 0; 
		$this->bsAmtTotal = ['income'=>0, 'expense'=>0, 'internal_rced'=>0, 'internal_paid'=>0];

		$where 		= ' WHERE BS.BS_Status = "1" AND IT.IT_Business = "0"';
		$tables 	= ' FROM `balance_sheets` AS BS'
					. ' INNER JOIN `items` IT ON (BS.IT_Id = IT.IT_Id)'
					. ' LEFT JOIN `sub_heads` AS SH ON (IT.SH_Id = SH.SH_Id)'
					. ' LEFT JOIN `main_heads` AS MH ON (SH.MH_Id = MH.MH_Id)'
					. ' LEFT JOIN `descriptions` AS DS ON (BS.BS_Description = DS.DS_Id)'
					. ' LEFT JOIN `tracks` AS TR ON (BS.TR_Id = TR.TR_Id)'
					. ' LEFT JOIN `locations` AS LC ON (BS.LC_Id = LC.LC_Id)' // added branch
					. ' LEFT JOIN `locations` AS BR ON (BS.BS_BranchTo = BR.LC_Id)'; // branch
		$fields 	= '';
		$joins		= '';
		if (isset($export) && $export == 'yes') {

			if ( $type == "bank" ) {
				$joins	.= ' LEFT JOIN `bank_accounts` AS BA ON (BA.BA_Id=BS.BA_Id)'
						.' LEFT JOIN `locations` AS BL ON (BA.LC_Id = BL.LC_Id)';
				$fields .= ' , BA.BA_DispName, BA.BA_No, BS.CHQ_Number, BS.BS_BRemarks, BL.LC_Name AS Paid_Branch ';
			} else {
				$fields .= ', BS.BS_Remarks';
			}
			$fields .= ', BS.BS_VoucherNo';
		} else if ( $type == "bank" ) {
			$joins	.= ' LEFT JOIN `bank_accounts` AS BA ON (BA.BA_Id=BS.BA_Id)';
			$fields .= ' , BA.BA_DispName AS bank_name ';
		}
		if (isset($type)) {
			if ( $type == "cash" ) {

				$where 	.= ' AND BS.PM_Id = 1 '
					.' AND IF( BS.BS_PettyCashRefId != 0 , MH.MH_Type = 1 , MH.MH_Type IN ("1","2"))';
			} else if ( $type == "bank" ) {

				$where 	.= ' AND BS.PM_Id = 2 '
					.'AND MH.MH_Type IN ("1","2")  AND BS.BA_Id != 0 AND BS.BS_PettyCashRefId = 0';
			}
		}
		if (isset($from_date) || isset($to_date)) {
			if ($from_date != '' && $to_date != '') {
				$where 	.= ' AND BS.BS_Date   between "'.$from_date.'" AND "'.$to_date.'"';
			} else if ($from_date != '') {
				$where 	.= ' AND BS.BS_Date >= "'.$from_date.'"';
			} else if ($to_date != '') {
				$where 	.= ' AND BS.BS_Date <= "'.$to_date.'"';
			}
		}
		if (isset($lc_id) && $lc_id > 0) {
			$where 		.= ' AND BS.LC_Id = "'.$lc_id.'"';
		}
		if (isset($branch) && $branch > 0) {
			$where 		.= ' AND BS.BS_BranchTo = "'.$branch.'"';
		}else if (isset($of_id) && $of_id > 0) {
			$where 		.= ' AND LC.OF_Id = "'.$of_id.'"';
		}
		if (isset($mh_type) && $mh_type > 0) {
			$where 		.= ' AND IT.MH_Type = "'.$mh_type.'"';
			if (isset($internal)) {
				$where 	.= ' AND IT.IT_Transfers = "'.$internal.'"';
			}
		}		
		if (isset($bank_id) && $bank_id > 0) {
			$where 		.= ' AND BS.BA_Id = "'.$bank_id.'"';
		}
		if (isset($search) && $search != '') {

			$where 		.= ' AND (IT.IT_Name like "%'.$search.'%" OR DS.DS_Description like "%'.$search.'%" OR TR.TR_Track like "%'.$search.'%" )';
		}
		if (isset($amount) && $amount != '') {
			$where 		.= ' AND (cast(BS.BS_Amount as decimal(11,2))  = "'.$amount.'" OR FLOOR(BS.BS_Amount) = "'.$amount.'")';
		}
		// find the count from the query
		$sql_count 		= 'SELECT COUNT(DISTINCT BS.BS_Id) '.$tables.$joins.' '.$where;
		$res_count 		= mysqli_query($GLOBALS['con'], $sql_count);
		$row_count		= mysqli_fetch_array($res_count,MYSQLI_NUM);
            $this->bsTotal 	= $row_count[0];
            // get the list of data 
            if ($this->bsTotal > 0) {
        	$slno 		= 0;
        	//$extrafields = ', BS.US_Id, DS.DS_Id, TR.TR_Id, IT.IT_Id, IT.SH_Id, IT.IT_Status, MH.MH_Type AS main_type ';
        	$sql 		= 'SELECT BS.BS_Id, BS.BS_Date, BS.BS_Amount, BS.BS_PettyCashAmt, DS.DS_Description, TR.TR_Track, IT.IT_Name, SH.SH_Name, IT.MH_Type, IT.IT_Transfers, LC.LC_Name, BR.LC_Name AS Branch, IT.SH_Id '.$fields
        	.' '.$tables.$joins
        	.' '.$where
        	.' GROUP BY BS.BS_Id'
        	.' ORDER BY BS.BS_Date';
        	if (isset($limit) && $limit > 0) {

        		$sql 	.= ' LIMIT '.$start.','.$limit;
        		$slno 	= $start;
        	}
        	$res 		= mysqli_query($GLOBALS['con'], $sql);
        	while ($row = mysqli_fetch_object($res)) {
                
	                $slno++;
	                $row->slno 			= $slno;
	                $row->item_type 	= $this->itemTypes($row->MH_Type, $row->IT_Transfers);
	                $row->BS_Date 		= date('d/m/Y',strtotime($row->BS_Date));
	                $this->bsArray[] 	= $row;
            	}
            	if (isset($export) && $export == 'yes') {
            	// no need total in excel export...
            	} else {
	            // find the sum of every type (income, expense, internal...)
	            $sql_sum 		= 'SELECT IT.MH_Type, IT.IT_Transfers, SUM(BS.BS_Amount) AS sumamt '.$tables.$joins.' '.$where. ' GROUP BY IT.MH_Type, IT.IT_Transfers ORDER BY IT.MH_Type';

	            $res_sum 		= mysqli_query($GLOBALS['con'], $sql_sum);
	            while ($row_sum = mysqli_fetch_object($res_sum)) {
	            	if ($row_sum->MH_Type == 2) {
	            		if ($row_sum->IT_Transfers == 1) {
							$this->bsAmtTotal['internal_paid'] 	= round($row_sum->sumamt,2);
						} else {
							$this->bsAmtTotal['expense'] 		= round($row_sum->sumamt,2);
						}
	            	} else if ($row_sum->MH_Type == 1) {
	            		if ($row_sum->IT_Transfers == 1) {
							$this->bsAmtTotal['internal_rced'] 	= round($row_sum->sumamt,2);
						} else {
							$this->bsAmtTotal['income'] 		= round($row_sum->sumamt,2);
						}
	            	}
	            }
	        }
            }
	}
	/**
	 * Opening Balance of selected Bank or all bank based on the result searching starting Date
	 * Created By Bilin @ 19-11-2025
	*/
	function BankOpenBalance( $inparms = [])
	{
	   	extract($inparms);
	   	$projectdatetime = strtotime('2025-04-01');
	   	$start_date 	 = date('Y-m-d', $projectdatetime);	
	   	$bank_id 	 = (isset($bank_id)) ? $bank_id : 0;
	   	$of_id  	 = (isset($of_id)) ? (int)$of_id : 0;
	   	$getCurrentbal 	 = 1;
	   	$balanceAmt 	 = 0;
	   	$where 		 = ' WHERE IT.IT_Business = "0" AND  BS.BA_Id != 0 AND BS.BS_Status = 1 AND MH.MH_Type IN ("1","2") AND BS.PM_Id = 2  AND BS.BS_PettyCashRefId = 0 ';
	   	$whereBank 	 = ' WHERE 1 ';
	   	// find the bank opening at start
	   	if ($of_id > 0) {
	   		$whereBank .= ' AND BA.OF_Id = "'.$of_id.'"';
	   	}
	   	if (isset($lc_id) && $lc_id > 0) {
			$whereBank .= ' AND BA.LC_Id = "'.$lc_id.'"';
			$where 	   .= ' AND BS.LC_Id = '.$lc_id;
		}
		if ($bank_id > 0) {
			$whereBank .= ' AND BO.BA_Id = "'.$bank_id.'"';
			$where 	   .= ' AND BS.BA_Id = '.$bank_id;
		}	   	
	   	if (isset($from_date)) {
	   		$whereBank .= ' AND BO.BnkOB_CDate <= "'.$from_date.'"';
	   		$where 	   .= ' AND BS.BS_Date < "'.$from_date.'"';
	   		if ($from_date > $start_date) {

	   		  $where   .= ' AND BS.BS_Date >= "'.$start_date.'"';
	   		} else if ($from_date == $start_date) {
	   		  $getCurrentbal = 0;
	   		}
	   	} else {
	   		$whereBank .= ' AND BO.BnkOB_CDate <= "'.$start_date.'"';
	   		$getCurrentbal 	 = 0;	
	   	}	   	
	   	if (isset($branch) && $branch > 0) {
			$where 		.= ' AND BS.BS_BranchTo = "'.$branch.'"';
			$whereBank 	.= (isset($lc_id) && $lc_id > 0) ? '' : ' AND BA.LC_Id = "'.$branch.'"';
		} else if (isset($of_id) && $of_id > 0) {
			$where 		.= ' AND LC.OF_Id = "'.$of_id.'"';
		}
		
		// bank opening balance query
		$sql 	= 'SELECT SUM(BO.BnkOB_OpenBal) as OB FROM bank_open_bals as BO 
                INNER JOIN bank_accounts as BA ON (BO.BA_Id = BA.BA_Id)  '.$whereBank;
       		$res 	= mysqli_query($GLOBALS['con'],$sql);
       		$row 	= mysqli_fetch_object($res);
       		$balanceAmt = $row->OB;

		// previous income and expense based searched time balance
		if ( $getCurrentbal == 1 ) {
			$result = mysqli_query($GLOBALS['con'], "SELECT SUM( BS.BS_Amount ) AS IE , MH.MH_Type
                            FROM  `balance_sheets` AS BS 
                            LEFT JOIN  `items` IT ON (BS.IT_Id = IT.IT_Id)
                            LEFT JOIN  `sub_heads` AS SH ON (IT.SH_Id = SH.SH_Id)
                            LEFT JOIN  `main_heads` AS MH ON (SH.MH_Id = MH.MH_Id)
                            LEFT JOIN `bank_accounts` AS BA ON (BA.BA_Id = BS.BA_Id) 
                            LEFT JOIN `locations` AS LC ON (BS.LC_Id = LC.LC_Id)
                            LEFT JOIN `locations` AS BR ON (BS.BS_BranchTo = BR.LC_Id) 
                            ".$where."
                            GROUP BY MH.MH_Type");
			while($row=mysqli_fetch_object($result)) {
			    $balanceAmt = ($row->MH_Type == 1) ? $balanceAmt + $row->IE: $balanceAmt - $row->IE;
			}
		}

		return $balanceAmt;
	}



















	// sub function for the static id based data view
	function itemTypes($mhtype=0, $ittrans='') {

		if ($mhtype == 2) { //Expense
			if ($ittrans == 1) {
				return "Internal Transfer Paid";
			} else if ($ittrans == 0) {
				return "Expense";
			} else {
				return "Expense";
			}
		} else if ($mhtype == 1) { //Income
			if ($ittrans == 1) {
				return "Internal Transfer Recd";
			} else if ($ittrans == 0) {
				return "Income";
			} else {
				return "Income";
			}
		}
	}
}
?>