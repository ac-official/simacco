<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<complete>';
if($REQUEST['tmode']=="HRS"){    
    for($i=0;$i<24;$i++){ 
    echo '<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';    
    }    
}    
else if($REQUEST['tmode']=="MINS"){
    $minarray=array(0,15,30,45);    
    foreach($minarray as $mins){        
    echo '<option value="'.str_pad($mins,2, "0", STR_PAD_LEFT).'">'.str_pad($mins,2, "0", STR_PAD_LEFT).'</option>';    
    }
}
   echo '</complete>';

?>

