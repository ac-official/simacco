<?php
/**
* Balance Sheet based on the user filter (date range, company, branch..)
* Created By Bilin @ 31-10-2025
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

// find the finiancial year start date based on the search from date
$start_year 		= date('Y', strtotime($inparams['from_date']));
if (date('m', strtotime($inparams['from_date'])) >= 4) {
	$start_date 	= $start_year."-04-01";
} else {
	$start_year 	= $start_year - 1;
	$start_date 	= $start_year."-04-01";
}

// fetch the tax amount of bills based on the filter above listed - Input Tax
// also get the common other data based on the filter above listed - Sundry Creditors / Debtors 
// fetch data from database via model class 
$inparams['fstart_date'] = $start_date;
$group_datas 		= $accObj->listBalanceSheet($inparams);
$result_data 		= $accObj->data_list;

//print_r($group_datas);
//echo $group_datas['sql'];
//print_r($result_data);
/*
SELECT j.ledger_id, SUM(j.amount) AS amount, grp.type, ledr.parent_id, IF (ledr.parent_id > 0, pldr.title, ledr.title) AS ledger_name, 
IF (grp.parent_id > 0, pgr.code, grp.code) As group_code, IF (grp.parent_id > 0, pgr.title, grp.title) As group_name, 
ledr.is_return, j.amt_type, j.ba_id, ledr.is_contra, ledr.less_id, ledr.plus_id, j.location_id, j.branch_id 
FROM acc_journal AS j  
INNER JOIN acc_ledger AS ledr ON (ledr.id = j.ledger_id) 
 INNER JOIN acc_groups AS grp ON (grp.id = ledr.group_id) 
 LEFT JOIN acc_groups AS pgr ON (pgr.id = grp.parent_id)  
LEFT JOIN acc_ledger AS pldr ON (pldr.id = ledr.parent_id)   
WHERE j.status != 3 AND grp.type NOT IN ("Income","Expense") AND grp.status != 3 AND ledr.status != 3 AND (grp.parent_id = 0 OR pgr.status != 3) 
AND j.company_id = "5" AND j.date_entry >= "2025-04-01" AND j.date_entry <= "2025-12-17" 
GROUP BY j.ledger_id, j.ba_id, j.location_id  ORDER BY grp.type, j.date_entry*/



// get the profit and loss account balance of current period
$inparams['ret_type'] = 0;
$plAmt 				= $accObj->profitAndLossTotal($inparams);
$plAmt1 			= 0;
// get the profit and loss account opening (April 1 to from date searched by the user)
if ($start_date < $inparams['from_date']) {
	$inparams['to_date'] 	= date('Y-m-d', strtotime($inparams['from_date'] . ' -1 day'));
	$inparams['from_date'] 	= $start_date;
	$plAmt1 				= $accObj->profitAndLossTotal($inparams);
}


// get opening balance of all 
$openbalparms 		= ['start_date'=>$start_date, 'company_id'=>$inparams['company_id'], 'branch_id'=>$inparams['branch_id'], 'pandl'=>$plAmt, 'pandlbefore'=>$plAmt1];
$getopenBals  		= $accObj->getOpenBalance($openbalparms);

// find and set the left and right side of balance based on the items fetched from database.
$leftSide 			= [];
$rightSide 			= [];
$leftTotal 			= 0;
$rightTotal 		= 0;
// right side open balance sections
foreach (['1','3'] As $tyk) {
	if (isset($getopenBals[$tyk])) {
		foreach($getopenBals[$tyk] AS $opval) {

			if ($opval->showtype == 2) { // total side based amount calculation
				$rightTotal = $rightTotal + $opval->amount;
			}
			$rightSide[]= $opval;
		}
	}
}
// captial others
if (!empty($result_data['capital'])) {
	foreach($result_data['capital'] AS $key=>$opval) {
		$rightTotal 	= $rightTotal + $opval->amount;		
		$rightSide[] 	= $opval;
	}	
}
//loans and fixed assets
foreach (['4','5'] As $tyk) {
	if (isset($getopenBals[$tyk])) {
		foreach($getopenBals[$tyk] AS $opval) {

			if ($opval->showtype == 2) { // total side based amount calculation
				$rightTotal = $rightTotal + $opval->amount;
			}
			$rightSide[]= $opval;
		}
	}
}
// right side sundry debors
if (!empty($group_datas['debtor'][0])) {
	foreach($group_datas['debtor'] AS $opval) {
		if ($opval->showtype == 2) { 
			$rightTotal 	= $rightTotal + $opval->amount;
		}
		$rightSide[] 	= $opval;
	}	
}
// cash in hand and bank account balance right side
if (!empty($result_data['bank'][0])) {
	foreach($result_data['bank'] AS $opval) {
		if ($opval->showtype == 2) { 
			$rightTotal 	= $rightTotal + $opval->amount;
		}
		$rightSide[] 	= $opval;
	}	
}
if (!empty($result_data['branch'][0])) {
	foreach($result_data['branch'] AS $opval) {
		if ($opval->showtype == 2) { 
			$rightTotal 	= $rightTotal + $opval->amount;
		}
		$rightSide[] 	= $opval;
	}	
}

// left side tax 
if (!empty($group_datas['tax'][0])) {
	$leftSide[] 		= $group_datas['tax'][0];
	if (!empty($group_datas['tax']['out'])) {
		foreach($group_datas['tax']['out'] AS $opval) {
			$leftTotal 	= $leftTotal + $opval->amount;
			$leftSide[] = $opval;
		}
	} else {
		$leftSide[] = (object)['amount'=>0, 'title'=>"Output Tax", 'showtype'=>2];
	}
	if (!empty($group_datas['tax']['inp'])) {
		foreach($group_datas['tax']['inp'] AS $opval) {
			$leftTotal 	= $leftTotal + $opval->amount;
			$leftSide[] = $opval;
		}		
	}
}
// left side sundry creditors
if (!empty($group_datas['creditor'][0])) {
	foreach($group_datas['creditor'] AS $opval) {
		if ($opval->showtype == 2) { 
			$leftTotal 	= $leftTotal + $opval->amount;
		}
		$leftSide[] 	= $opval;
	}	
}
// left side other liability
if (!empty($result_data['liability'])) {
	foreach($result_data['liability'] AS $libmain) {
		$leftSide[] = (object)['amount'=>$libmain['amount'], 'title'=>$libmain['title'], 'showtype'=>1];
		foreach($libmain['list'] AS $opval) {
			$leftTotal 		= $leftTotal + $opval->amount;
			$leftSide[] 	= $opval;
		}
	}	
	/*foreach($result_data['liability'] AS $opval) {
		if ($opval->showtype == 2) { 
			$leftTotal 	= $leftTotal + $opval->amount;
		}
		$leftSide[] 	= $opval;
	}*/
}
// right side other assets 12-02-2026
if (!empty($result_data['asset'])) {
	foreach($result_data['asset'] AS $libmain) {
		$rightSide[] = (object)['amount'=>$libmain['amount'], 'title'=>$libmain['title'], 'showtype'=>1];
		foreach($libmain['list'] AS $opval) {
			$rightTotal 	= $rightTotal + $opval->amount;
			$rightSide[] 	= $opval;
		}
	}
}
// left side opening balance sections
$tyk = 2;
if (isset($getopenBals[$tyk])) {
	foreach($getopenBals[$tyk] AS $opval) {

		if ($opval->showtype == 2) { // total side based amount calculation
			$leftTotal 	= $leftTotal + $opval->amount;
		}
		$leftSide[] 	= $opval;
	}

}


			//$retData[$type] = ['0' =>(object)['amount'=>$pandl, 'title'=>$typeAry[$type], 'showtype'=>1]];
        	//$retData[$type][] = (object)['showtype'=>2, 'ledger_id'=>0, 'title'=>'Current Period', 'amt_type'=>($pandl < 0)? 0:1, 'amount'=>abs($pandl)];




//echo $plAmt;
//echo $plAmt1;
//print_r($getopenBals);	

// cell create function commonly used
function xmlcellcreate($rw=[], $isblank=0) {
	if ($isblank == 1) {
		$listrow = '<cell></cell><cell></cell><cell></cell>';
	}  else if ($rw->showtype == 1) {
		$listrow = '<cell><![CDATA[<strong>'.htmlspecialchars_decode($rw->title).'<strong>]]></cell>';
		$listrow .= '<cell></cell>';		
		$listrow .= '<cell><![CDATA[<strong>'.number_format($rw->amount,2,".","").'<strong>]]></cell>';
	} else {
		$listrow = '<cell><![CDATA[&nbsp;&nbsp;&nbsp;'.htmlspecialchars_decode($rw->title).']]></cell>';
		$listrow .= '<cell>'.number_format($rw->amount,2,".","").'</cell>';
		$listrow .= '<cell></cell>';
	}

	return $listrow;
}

// return result in a selected xml grid format
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
	if (!empty($leftSide) || !empty($rightSide)) {
		$ncount 	= (count($leftSide) > count($rightSide)) ? count($leftSide): count($rightSide);
		for ($i = 0; $i < $ncount; $i++) {
			echo '<row id="bs'.$i.'">';

			if (isset($leftSide[$i])) {
				echo xmlcellcreate($leftSide[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			if (isset($rightSide[$i])) {
				echo xmlcellcreate($rightSide[$i],0);
			} else {
				echo xmlcellcreate([],1);
			}
			echo '</row>';
		}
		echo '<row id="bs'.$i.'">';
		echo xmlcellcreate([],1);
		echo xmlcellcreate([],1);
		echo '</row>';
		$i++;
		echo '<row id="bs'.$i.'">';
		echo xmlcellcreate((object)['showtype'=>'1', 'title'=>'Total', 'amount'=>$leftTotal],0);
		echo xmlcellcreate((object)['showtype'=>'1', 'title'=>'Total', 'amount'=>$rightTotal],0);
		echo '</row>';
	} else {
		echo '<row id="0"> <cell colspan="6"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >Under Construction....'.json_encode($getopenBals).'<br>'.$plAmt.'</div>]]></cell></row>';
	}
echo '</rows>';
?>