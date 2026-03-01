<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
include_once( $BASEPATH.'preTallyClass/NotificationClass.php' );

$NotfItmObj = new NotificationClass();
$NotfItmObj->viewItemsReportFilter($preTally_user_ofid);

$IT_NotfObj = $NotfItmObj->NotfArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
if($IT_NotfObj){
    $count = 1;
    foreach($IT_NotfObj as $rw){ 
            echo '<row id="'.$rw->IT_Id.'">
                <cell name="IT_Name">'.$rw->IT_Name.'</cell>';
            echo '</row>';
            $j++;
    }
}
echo '</rows>';
?>