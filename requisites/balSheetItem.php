<?php
$id = isset($_GET['user']) ? (int) $_GET['user'] : null;
if (!$id) { // === 0 || === null
  //die('Un-Authorized Access');
}

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/ItemClass.php");
$category=$_GET['cat'];
$ItemObj = new ItemClass();
$ItemObj->viewItems(' WHERE SH_Id='.$category.' ORDER BY IT_Name');
$IT_Obj = $ItemObj->ItemArray;
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">channel</userdata>
		<userdata name="db_primary">CN_Id</userdata>
		<userdata name="db_date">CN_Update</userdata>
		<userdata name="db_status">CN_Status</userdata>
		<head>
			<column width="40" type="ro" align="center" sort="int"> </column>
			<column width="50" type="ro" align="center" sort="int"> SlNo </column>
			<column width="*" type="ro" align="left" sort="int"> Category</column>
			<column width="50" type="ro" align="center" sort="str">	Count</column>
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
		  </head>';
		  
		  /*
		  			<afterInit>
				<call command="attachHeader"><param>1,#cspan,3,#cspan,5,#cspan,7,#cspan</param></call>
				<call command="attachHeader"><param>1,2,3,4,5,6,7,8</param></call>
				<call command="attachFooter"><param>1,#cspan,3,#cspan,5,#cspan,7,#cspan</param></call>			
			</afterInit>
		  */
      $i=1;         
foreach($IT_Obj as $rowItem)
{
echo	'<row id="'.$rowItem->IT_Id.'">
            <cell>'.$i.'</cell>
            <cell>1</cell>
            <cell>'.$rowItem->IT_Name.'</cell>
            <cell>5</cell>
	</row>';
$i++;
}	


echo '</rows>';
?>