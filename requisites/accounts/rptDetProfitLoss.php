<?php
/**
* Profit and loss account based on the user filter (date range, company, branch..)
* This is the detailed p and l for ceo 
* Created By Bilin @ 15-01-2026
*/
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj		= new AccountsClass();
// return file header setup start
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
// find the inputs from the user side
$inparams 					= [];
$filterData 				= (isset($REQUEST['filter'])) ? explode(",",$REQUEST['filter']) : [];
$inparams['from_date']		= (isset($filterData[0]) && $filterData[0] != "") ? date("Y-m-d", strtotime($filterData[0])) : NULL;
$inparams['to_date']		= (isset($filterData[1]) && $filterData[1] != "") ? date("Y-m-d", strtotime($filterData[1])) : NULL;
$inparams['company_id']		= (isset($filterData[2])) ? (int)$filterData[2] : 0;
$inparams['branch_id']		= (isset($filterData[3])) ? (int)$filterData[3] : 0;
$inparams['trans_type']		= (isset($filterData[4])) ? (int)$filterData[4] : 0;
// check the searched from date is greater than bank/ branch opening balance entering date
$inparams['strat_date']		= "2025-04-01";
$inparams['get_bal_tot']	= 0; // jiust get the opening balance added by the office
if ($inparams['from_date'] > $inparams['strat_date']) {
	$inparams['get_bal_tot']= 1; // added opening + start date to searched from date based all data Total.
}


// fetch data from database via model class 
$accObj->listDetailProfitLoss($inparams);
$result_data 		= $accObj->data_list;

// $groupcodes 		= ['purchase'=>'l0', 'direct_expense'=>'l1', 'sales'=>'r0', 'direct_income'=>'r1', 'indirect_expense'=>'l2', 'indirect_income'=>'r2'];

function xmlcellcreate($rw, $isblank=0) {
	if ($isblank == 1) {
		$listrow = '<cell></cell><cell></cell><cell></cell>';
	} else if ($isblank == 2) {
		$listrow = '<cell></cell><cell></cell><cell><![CDATA[<strong>'.number_format($rw['amt'],2,".","").'<strong>]]></cell>';
	} else if ($rw['type'] == 0) {
		$listrow = '<cell><![CDATA[<strong>'.htmlspecialchars_decode($rw['title']).'<strong>]]></cell>';
		$listrow .= '<cell></cell>';		
		$listrow .= '<cell><![CDATA[<strong>'.number_format($rw['amt'],2,".","").'<strong>]]></cell>';
	} else {
		$listrow = '<cell><![CDATA[&nbsp;&nbsp;&nbsp;'.htmlspecialchars_decode($rw['title']).']]></cell>';
		$listrow .= '<cell>'.number_format($rw['amt'],2,".","").'</cell>';
		$listrow .= '<cell></cell>';
	}

	return $listrow;
}
//echo $accObj->sql_query;
//print_r($result_data);
// return result in a selected xml grid format
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
	if (!empty($result_data)) {
		$purchase 		= (isset($result_data['l0'])) ? $result_data['l0']:[];
		$direct_expense = (isset($result_data['l1'])) ? $result_data['l1']:[];
		$sales 			= (isset($result_data['r0'])) ? $result_data['r0']:[];
		$direct_income 	= (isset($result_data['r1'])) ? $result_data['r1']:[];
		$indirect_expense 	= (isset($result_data['l2'])) ? $result_data['l2']:[];
		$indirect_income 	= (isset($result_data['r2'])) ? $result_data['r2']:[];
		$grossprofit 	= 0;
		$grossloss 		= 0;
		$netprofit 		= 0;
		$netloss 		= 0; 

		$l1 				= 0; // direct expense list count
		$dir_exp_ary    	= []; // left side array top
		$direct_exp  		= 0; // direct expense + purchase total
		// purchase and direct expense - p and l - Debit Side - first section top
		if (isset($result_data['l0'])) {
			$dir_exp_ary[$l1] = [ 'title'=>$result_data['l0']['title'], 'amt'=>$result_data['l0']['total'], 'type'=>0 ];
			$l1++;
			foreach($result_data['l0']['list'] AS $rw) {

				$dir_exp_ary[$l1] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$l1++;
			}
			$direct_exp 	+= $result_data['l0']['total'];
		}
		if (isset($result_data['l1'])) {
			$dir_exp_ary[$l1] = [ 'title'=>$result_data['l1']['title'], 'amt'=>$result_data['l1']['total'], 'type'=>0 ];
			$l1++;
			foreach($result_data['l1']['list'] AS $rw) {

				$dir_exp_ary[$l1] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$l1++;
			}
			$direct_exp 	+= $result_data['l1']['total'];
		}

		$r1 				= 0; // direct income list count
		$sales_ary    		= []; // right side array top		
		$direct_inc 		= 0; // direct income //sales account total
		// sales and direct income - p and l - credit Side - first section top
		if (isset($result_data['r0'])) {
			$sales_ary[$r1] = [ 'title'=>$result_data['r0']['title'], 'amt'=>$result_data['r0']['total'], 'type'=>0 ];
			$r1++;
			foreach($result_data['r0']['list'] AS $rw) {

				$sales_ary[$r1] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$r1++;
			}
			$direct_inc 	+= $result_data['r0']['total'];
		}
		if (isset($result_data['r1'])) {
			$sales_ary[$r1] = [ 'title'=>$result_data['r1']['title'], 'amt'=>$result_data['r1']['total'], 'type'=>0 ];
			$r1++;
			foreach($result_data['r1']['list'] AS $rw) {

				$sales_ary[$r1] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$r1++;
			}
			$direct_inc 	+= $result_data['r1']['total'];
		}
		
		$l2 				= 0; // in direct expense list count
		$idir_exp_ary 		= []; // left side array bottom
		$idirect_exp  		= 0; // in direct expense total
		// purchase and direct expense - p and l - Debit Side - first section top
		if (isset($result_data['l2'])) {
			$idir_exp_ary[$l2] = [ 'title'=>$result_data['l2']['title'], 'amt'=>$result_data['l2']['total'], 'type'=>0 ];
			$l2++;
			foreach($result_data['l2']['list'] AS $rw) {

				$idir_exp_ary[$l2] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$l2++;
			}
			$idirect_exp 	+= $result_data['l2']['total'];
		}

		$r2 				= 0; // in direct income list count
		$idir_inc_ary    	= []; // right side array bottom		
		$idirect_inc 		= 0; // indirect income total
		// indirect income - p and l - credit Side - Bottom section 
		if (isset($result_data['r2'])) {
			$idir_inc_ary[$r2] = [ 'title'=>$result_data['r2']['title'], 'amt'=>$result_data['r2']['total'], 'type'=>0 ];
			$r2++;
			foreach($result_data['r2']['list'] AS $rw) {

				$idir_inc_ary[$r2] = [ 'title'=>$rw['title'], 'amt'=>$rw['amt'], 'type'=>1 ];
				$r2++;
			}
			$idirect_inc 	+= $result_data['r2']['total'];
		}


		// rows create start based on the formated arrays
		// create the top coloumn section with direct income and direct expense
		$n  = ($l1 > $r1) ? $l1 : $r1;
		for ($i = 0; $i < $n; $i++ ) {
			echo '<row id="lt'.$i.'">';
			if (isset($dir_exp_ary[$i])) {
				echo xmlcellcreate($dir_exp_ary[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			if (isset($sales_ary[$i])) {
				echo xmlcellcreate($sales_ary[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			echo '</row>';
		}
		if ( $direct_exp <  $direct_inc) { // direct income greaterthan expense	
			$grossprofit 	= $direct_inc - $direct_exp;
			echo '<row id="mid0">';
			echo xmlcellcreate([ 'title'=>"Gross profit c/o", 'amt'=>$grossprofit, 'type'=>0 ],0);
			echo xmlcellcreate([],1);
			echo '</row>';
			echo '<row id="mid1">';
			echo xmlcellcreate(['amt'=>$direct_inc],2);
			echo xmlcellcreate(['amt'=>$direct_inc],2);
			echo '</row>';
			echo '<row id="mid2">';
			echo xmlcellcreate([],1);
			echo xmlcellcreate([ 'title'=>"Gross profit b/f", 'amt'=>$grossprofit, 'type'=>0 ],0);
			echo '</row>';
		} else if ( $direct_exp >  $direct_inc) {  // direct expense greater than income
			$grossloss 		= $direct_exp - $direct_inc;	
			echo '<row id="mid0">';		
			echo xmlcellcreate([],1);
			echo xmlcellcreate([ 'title'=>"Gross Loss c/o", 'amt'=>$grossloss, 'type'=>0 ],0);
			echo '</row>';
			echo '<row id="mid1">';
			echo xmlcellcreate(['amt'=>$direct_exp],2);
			echo xmlcellcreate(['amt'=>$direct_exp],2);
			echo '</row>';
			echo '<row id="mid2">';
			echo xmlcellcreate([ 'title'=>"Gross Loss b/f", 'amt'=>$grossloss, 'type'=>0 ],0);
			echo xmlcellcreate([],1);
			echo '</row>';
		}
		
		// create the bottom column section with indirect expense and indirect expense
		$n  = ($l2 > $r2) ? $l2 : $r2;
		for ($i = 0; $i < $n; $i++ ) {
			echo '<row id="lb'.$i.'">';
			if (isset($idir_exp_ary[$i])) {
				echo xmlcellcreate($idir_exp_ary[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			if (isset($idir_inc_ary[$i])) {
				echo xmlcellcreate($idir_inc_ary[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			echo '</row>';
		}
		$idirect_exp 	+= $grossloss;
		$idirect_inc  	+= $grossprofit; 
		if ( $idirect_exp  < $idirect_inc) {
			$netprofit 	= $idirect_inc - $idirect_exp;
			echo '<row id="bot0">';
			echo xmlcellcreate([ 'title'=>"Net Profit", 'amt'=>$netprofit, 'type'=>0 ],0);
			echo xmlcellcreate([],1);
			echo '</row>';
			echo '<row id="bot1">';
			echo xmlcellcreate([ 'title'=>"Total", 'amt'=>$idirect_inc, 'type'=>0 ],0);
			echo xmlcellcreate([ 'title'=>"Total", 'amt'=>$idirect_inc, 'type'=>0 ],0);
			echo '</row>';
		} else if ( $idirect_exp >  $idirect_inc) {
			$netloss 	= $idirect_exp - $idirect_inc;
			echo '<row id="bot0">';
			echo xmlcellcreate([],1);
			echo xmlcellcreate([ 'title'=>"Net Loss", 'amt'=>$netloss, 'type'=>0 ],0);			
			echo '</row>';
			echo '<row id="bot1">';
			echo xmlcellcreate([ 'title'=>"Total", 'amt'=>$idirect_exp, 'type'=>0 ],0);
			echo xmlcellcreate([ 'title'=>"Total", 'amt'=>$idirect_exp, 'type'=>0 ],0);
			echo '</row>';
		}

	} else {
		echo '<row id="0"> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell></row>';
	}
echo '</rows>';

// Expense       Split Amt    Total  Amt      Opening abalabce     split amt   amt
//											  Income
// Closing
//(open+income-expense)     

/**
 * Rules
 * Find all bank and branch balance added at the first time
 * Find the transactions sum (cash and bank) in the period of opening date to first date of the user search
 * Show the sum of the above rules into Opening Banalce of next year
 * Fetch all Expenses from the database in the time period user search
 * Fetch all income and show the the right side 
 * Fetch the extra ledger data added list left side shown
 * find the close balance based on the (opening + incomde) - expense 
 *  
 * 
*/
?>