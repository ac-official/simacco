<?php
/**
 * Auto filling combo box listing options based on the user input text/mask charactor
*/
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n"); 
include_once($BASEPATH . "preTallyClass/AccountsClass.php"); 
$accObj         = new AccountsClass();
$key            = $_REQUEST["mask"];
$flag           = (isset($_REQUEST['flag'])) ? (int)$_REQUEST['flag']:0;
$datalist       = [];

echo '<complete >';
if($REQUEST['ctype']=='selt') echo '<option value="0">Select</option>';

if ($flag == 0) { //Bills listing
    $condition  = " WHERE abl.bill_name LIKE '".$key."%'";
    if ($UserACLObj->manage_all_bills == 0) {
       $condition  .= " AND abl.company_id =".$preTally_user_ofid;  
    }
    $datalist   = $accObj->getCustomField("acc_bills AS abl LEFT JOIN locations AS l ON (l.LC_Id = abl.branch_id)", "abl.id, CONCAT(abl.bill_name,' - ',DATE_FORMAT(abl.bill_date,'%d-%m-%Y'), '  ' ,IF (abl.branch_id > 0, l.LC_Name,'')) AS title", $condition." ORDER BY abl.bill_name ASC LIMIT 0,50");     
} else {
}
if (!empty($datalist)) {    
    foreach($datalist as $rw) {
        echo '<option value="'.$rw->id.'" >'.trim($rw->title).'</option>';
    }

} else if (empty($datalist) && $REQUEST['nodata']=='yes') {
    echo '<option value="-1" selected="true">No Records Found</option>';
} else if (empty($datalist)) {
    echo '<option value="" selected="true"></option>';
}
echo '</complete>';
?>
