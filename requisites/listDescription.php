<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/DescriptionClass.php");

$DescriptionObj = new DescriptionClass();
if($preTally_user_ofid==1) {
    $filter = 1;
}else {
    $filter = " DS.OF_Id= $preTally_user_ofid";
}
$DescriptionObj->viewDescriptions(' WHERE '.$filter.' AND IT.IT_Id=DS.IT_Id AND IT.SH_Id = SH.SH_Id AND IT.IT_Status = 1 AND DS.DS_Status != 2 ORDER BY DS.DS_Description');
$DS_Obj = $DescriptionObj->DescriptionArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">descriptions</userdata>
		<userdata name="db_primary">DS_Id</userdata>
                <head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="str"> Description </column>
                        <column width="*" type="ro" align="left" sort="str"> Item </column>
			<column width="60" type="ro" align="center" sort="na">	Status </column>
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
				<call command="attachHeader">
					<param>,#text_filter,#select_filter_strict,</param>
				</call>
				
			</afterInit>

		  </head>';
		  if($DS_Obj) {
				$j = 1;
				foreach($DS_Obj as $rw) {
					
					echo '<row id="'.$rw->DS_Id.'">
                                            <userdata name="IT_Id">'.$rw->IT_Id.'</userdata>
                                            <userdata name="MH_Type">'.$rw->MH_Type.'</userdata>
                                            <userdata name="SH_Id">'.$rw->SH_Id.'</userdata>
                                            <userdata name="DS_Description">'.$rw->DS_Description.'</userdata>                                            
                                            <userdata name="DS_Status">'.$rw->DS_Status.'</userdata>
                                            <cell>'.$j.'</cell>
                                            <cell name="DS_Description">'.$rw->DS_Description.'</cell>    
                                            <cell name="IT_Name">'.$rw->IT_Name.'</cell>';
                                            if($rw->DS_Status == '0') { 
                                                    echo '<cell><![CDATA[<img src="images/icon/cross.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                                            } else {
                                                    echo '<cell><![CDATA[<img src="images/icon/tick.png" style="margin:2px 0; cursor:pointer;"/>]]></cell>';
                                            }
                                        echo '</row>';
					$j++;
				}
			  } else {echo '<row id="0"> <cell></cell><cell><![CDATA[<div style="font-size:14px;color:#0979B1;text-align:center !important;font-family: serif;padding-top: 5px;" >No Records Found.</div>]]></cell></row>';}
		  
echo '</rows>';
?>