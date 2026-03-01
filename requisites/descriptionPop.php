<?php
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");
$DescriptionObj = new DescriptionClass();
$IT_Id = $REQUEST['IT_Id'];
$IT_Name = $REQUEST['IT_Name'];
$DescriptionObj->getDescriptionName($IT_Id, 'DS_Status = 1 AND OF_Id="' . $preTally_user_ofid . '" ');
$DSPopObj = $DescriptionObj->DescriptionArray;
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>';
$i = 1;
if ($DSPopObj) {
    foreach ($DSPopObj as $rw) {
        echo'<row id="'.$rw->DS_Id.'">
        <cell title=" " name="DS_Id">'. $i .'</cell>
        <cell title=" " name="DS_Desc">'. htmlspecialchars($rw->DS_Description).'</cell>
        </row>';
        $i++;
    }
} else {
    echo'<row id="0"><cell name="DS_Id"></cell><cell name="DS_Desc"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Description found.</div>]]></cell></row>';
}

echo '</rows>';
?>