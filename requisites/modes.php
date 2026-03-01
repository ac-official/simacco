<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$Mode_type = $REQUEST['mode'];

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

include_once($BASEPATH . "preTallyClass/UserClass.php");
if($Mode_type == 'time')
{
    echo '<complete >';
        echo '<option value="1" selected="true">Pay Now - No Credit</option>
              <option value="2" >Pay Later - Credit</option>';
    echo '</complete>';
}else if($Mode_type == 'mode')
{
    echo '<complete >';
        echo '<option value="1" selected="true">Cash (Pay Cash)</option>
              <option value="2" >Bank (Pay thru Bank)</option>';
    echo '</complete>';
}




?>