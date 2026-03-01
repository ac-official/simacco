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
			<column width="60"  type="dyn"  	align="left" 	sort="int">	Sales</column>
			<column width="*"   type="ed" 		align="left" 	sort="str">	Book title</column>
			<column width="120" type="txt" 		align="left" 	sort="str">	Author</column>
			<column width="80"  type="price" 	align="right" 	sort="int">	Price</column>
			<column width="80"  type="ch" 		align="center" 	sort="str">	In Store</column>
			<column width="80"  type="coro" 	align="center" 	sort="str">	Shipping</column>
			<column width="80"  type="ra" 		align="center" 	sort="str">	Bestseller</column>
			<column width="100" type="ro" 		align="center" 	sort="date">Publication Date</column>
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
					<param>,#text_filter,#select_filter,#text_filter,,,,</param>
				</call>
				
			</afterInit>

		  </head>';  
		  
?>
	<row id="1">
		<cell>-1500</cell>
		<cell>A Time to Kill</cell>
		<cell>John Grisham</cell>
		<cell>12.99</cell>
		<cell>1</cell>
		<cell>24</cell>
		<cell>0</cell>
		<cell>05/01/1998</cell>
	</row>
	<row id="2">
		<cell>1000</cell>
		<cell>Blood and Smoke</cell>
		<cell>Stephen King</cell>
		<cell>0</cell>
		<cell>1</cell>
		<cell>24</cell>
		<cell>0</cell>
		<cell>01/01/2000</cell>
	</row>
	<row id="3" selected="1">
		<cell>-200</cell>
		<cell>The Rainmaker</cell>
		<cell>John Grisham</cell>
		<cell>7.99</cell>
		<cell>0</cell>
		<cell>48</cell>
		<cell>0</cell>
		<cell>12/01/2001</cell>
	</row>
	<row id="4">
		<cell>350</cell>
		<cell>The Green Mile</cell>
		<cell>Stephen King</cell>
		<cell>11.10</cell>
		<cell>1</cell>
		<cell>24</cell>
		<cell>0</cell>
		<cell>01/01/1992</cell>
	</row>
	<row id="5">
		<cell>700</cell>
		<cell>Misery</cell>
		<cell>Stephen King</cell>
		<cell>7.70</cell>
		<cell>0</cell>
		<cell>na</cell>
		<cell>0</cell>
		<cell>01/01/2003</cell>
	</row>
	<row id="6">
		<cell>-1200</cell>
		<cell>The Dark Half</cell>
		<cell>Stephen King</cell>
		<cell>0</cell>
		<cell>0</cell>
		<cell>48</cell>
		<cell>0</cell>
		<cell>10/30/1999</cell>
	</row>
	<row id="7">
		<cell>1500</cell>
		<cell>The Partner</cell>
		<cell>John Grisham</cell>
		<cell>12.99</cell>
		<cell>1</cell>
		<cell>48</cell>
		<cell>1</cell>
		<cell>01/01/2005</cell>
	</row>
	<row id="8">
		<cell>500</cell>
		<cell>It</cell>
		<cell>Stephen King</cell>
		<cell>9.70</cell>
		<cell>0</cell>
		<cell>na</cell>
		<cell>0</cell>
		<cell>10/15/2001</cell>
	</row>
	<row id="9">
		<cell>400</cell>
		<cell>Cousin Bette</cell>
		<cell>Honore de Balzac</cell>
		<cell>0</cell>
		<cell>1</cell>
		<cell>1</cell>
		<cell>0</cell>
		<cell>12/01/1991</cell>
	</row>
	<row id="10">
		<cell>-100</cell>
		<cell>Boris Godunov</cell>
		<cell>Alexandr Pushkin</cell>
		<cell>7.15</cell>
		<cell>1</cell>
		<cell>1</cell>
		<cell>0</cell>
		<cell>01/01/1999</cell>
	</row>
	<row id="11">
		<cell>-150</cell>
		<cell>Alice in Wonderland</cell>
		<cell>Lewis Carroll</cell>
		<cell>4.15</cell>
		<cell>1</cell>
		<cell>1</cell>
		<cell>0</cell>
		<cell>01/01/1999</cell>
	</row>

<?php
echo '</rows>';
?>