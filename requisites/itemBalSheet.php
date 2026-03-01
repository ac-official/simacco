<?php
$id = isset($_GET['user']) ? (int) $_GET['user'] : null;
if (!$id) { // === 0 || === null
  die('Un-Authorized Access');
}

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">channel</userdata>
		<userdata name="db_primary">CN_Id</userdata>
		<userdata name="db_date">CN_Update</userdata>
		<userdata name="db_status">CN_Status</userdata>
		<head>
			<column width="95" type="ro" align="left" sort="str"> SlNo </column>
			<column width="150" type="ed" align="left" sort="str"> Item </column>
			<column width="150" type="txt" align="left" sort="str"> Price </column>
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
			<afterInit></afterInit>
		  </head>';
?>
	<row id="1">
		<cell>Item 1</cell>
        <cell>a</cell>
		<cell>a</cell>
	</row>
	<row id="2">
		<cell>Item 2</cell>
        <cell></cell>
		<cell></cell>
	</row>
	<row id="3" selected="1">
		<cell>Item 3</cell>
        <cell></cell>
		<cell></cell>
	</row>
	<row id="4">
		<cell>Item 4</cell>
        <cell></cell>
		<cell></cell>
	</row>
	<row id="5">
		<cell>Item 5</cell>
        <cell></cell>
		<cell></cell>
	</row>


<?php
echo '</rows>';
?>