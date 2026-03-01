<?php
error_reporting(0);

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/AttestationClass.php");

$ProcessDocumentObj = new AttestationClass();
$ProcessDocumentObj->viewTrackDocumentProcess($preTally_user_ofid);
$PDObj = $ProcessDocumentObj->TrackArray;
$processIDs = array();
$ids = trim(mysqli_real_escape_string($GLOBALS['con'],$REQUEST['ids'])); 
$processIDs = explode(',', $ids);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<complete>
	<template>
		<input><![CDATA[#name#, #mobile#]]></input>
		<columns>
                    <column width="20" header="" option="#cb#" />
                    <column width="150" header="Name" option="#name#"/>
                    <column width="150" header="Mobile" option="#mobile#"/>
		</columns>
	</template>';
	
        if ($PDObj) {
            $i = 1;
            foreach ($PDObj as $rw) {
                echo '<option value="'.$rw->APM_Id.'_'.$rw->APS_Id.'">
                    <text>
                        <cb></cb>
                        <name>'.htmlentities($rw->APM_Title).'</name>
                        <mobile>'.htmlentities($rw->APS_Title).'</mobile>
                    </text>
                </option>';
            }
	}
echo '</complete>';
?>
