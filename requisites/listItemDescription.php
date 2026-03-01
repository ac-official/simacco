<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/DescriptionClass.php");

$IT_Id = $REQUEST['IT_Id'];
$DescriptionObj = new DescriptionClass();
if($IT_Id != 0) $DescriptionObj->getDescriptionName($IT_Id, '( DS_Status = 1 || DS_Status = 0 ) AND OF_Id="' . $preTally_user_ofid . '" ');
$DSObj = $DescriptionObj->DescriptionArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>

                <userdata name="db_table">descriptions</userdata>
		<userdata name="db_primary">DS_Id</userdata>
                <head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na">#text_filter_inc</column>
			<column width="150" type="ro" align="center" sort="na">	Status </column>
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
                        </beforeInit> 
			<afterInit>
								
			</afterInit>

		</head>';

                $i = 1;
                if ($DSObj) {
                    foreach ($DSObj as $rw) {
                        echo'<row id="'.$rw->DS_Id.'">
                            
                        <userdata name="IT_Id">'.$IT_Id.'</userdata>
                        <userdata name="DS_Id">'.$rw->DS_Id.'</userdata>
                        <userdata name="DS_Description">'.htmlspecialchars($rw->DS_Description).'</userdata>
                        <userdata name="DS_MinAmount">'.$rw->DS_MinAmount.'</userdata>
                        <userdata name="DS_MaxAmount">'.$rw->DS_MaxAmount.'</userdata>
                        <userdata name="DS_Status">'.$rw->DS_Status.'</userdata>

                        <cell name="DS_Id">'.$i.'</cell>
                        <cell name="DS_Desc">'.htmlspecialchars($rw->DS_Description).'</cell>';
                            if($rw->DS_Status == '0') { 
                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                            } else {
                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                            }
                        echo '</row>';
                        $i++;
                    }
                } else {
                    echo'<row id="0"><cell name="DS_Id"></cell><cell name="DS_Desc"><![CDATA[<div style="font-size:16px;font-variant:small-caps;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 10px;" >No Description found.</div>]]></cell></row>';
                }

echo '</rows>'
?>
