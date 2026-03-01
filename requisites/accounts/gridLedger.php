<?php
/**
* List all ledger in a grid based on the user searched word
* check the word present in ledger or parent ledger then show the grid with ledger, parent, group, type
* Created By Bilin @ 06-08-2025
*/
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
// accounts settings related class
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj			= new AccountsClass();

// find the inputs
$search 		= (isset($_REQUEST['search'])) ? trim($_REQUEST['search']): ''; 

$result_data 	= $accObj->listLedgerCbo(['status'=>1,''=>$preTally_user_ofid, 'search'=>$search, 'flag'=>1]);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
	echo '<userdata name="Data_Count">'.count($result_data).'</userdata>';
	$i 			= 0;
	if (!empty($result_data)) {
		foreach($result_data as $rw) {
			$i++;
			echo '<row id="jl'.$i.'" style="cursor:pointer;">';

			echo '<userdata name="ledger_id">'.$rw->id.'</userdata>';
			echo '<userdata name="ledger_name">'.$rw->ledger.'</userdata>';
			echo '<userdata name="ie_type">'.$rw->ie_type.'</userdata>';
			echo '<cell>'.$i.'</cell>';
			echo '<cell>'.$rw->ledger.'</cell>';
			echo '<cell>'.$rw->parent.'</cell>';
			echo '<cell>'.$rw->group_name.'</cell>';
			echo '<cell>'.$rw->type.'</cell>';

			echo '</row>';
		}
	} else {
		echo '<row id="jl0"><userdata name="ledger_id">0</userdata> <cell></cell> <cell colspan="3"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found.</div>]]></cell><cell></cell></row>';
	}
echo '</rows>';

/*echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';  
echo '<head>
    <column width="50" type="ro" align="center" > SlNo </column>
    <column width="*" type="ro" align="left" > Ledger </column>
    <column width="*" type="ro" align="left" > Parent </column>
    <column width="*" type="ro" align="left" > Group </column>
    <column width="100" type="ro" align="left" > Type </column>
    <settings>
        <colwidth>px</colwidth>
    </settings>
    <beforeInit> 
        <call command="setSkin">
            <param>dhx_skyblue</param>
        </call> 
        <call command="setImagePath">
            <param>assets/grid/codebase/imgs/</param>
        </call> 
        <call command="enableSmartRendering">
            <param>false</param>
        </call> 
    </beforeInit>';
$viewHtml   = ''; 
	$i 		= 0;
	if (!empty($result_data)) {
		foreach($result_data as $rw) {
			$i++;
			$viewHtml .= '<row id="je'.$rw->id.'>  
        		<userdata name="ledger_id">'.$rw->id.'</userdata>
        		<userdata name="ledger_name">'.$rw->ledger.'</userdata>
        		<cell>'.$i.'</cell>
        		<cell>'.$rw->ledger.'</cell>
        		<cell>'.$rw->parent.'</cell>
        		<cell>'.$rw->group_name.'</cell>
        		<cell>'.$rw->type.'</cell>';
        	$viewHtml .= '</row>';

		}
	}else{
		$viewHtml 	.= '<row id="0"> 	
		<cell colspan="5"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No records found</div>]]></cell>
		</row>';
	}
echo '</head>'; 
echo $viewHtml;
echo '</rows>';*/

?>