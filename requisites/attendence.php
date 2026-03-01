<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

$dm 		= $_REQUEST['dm'] ? $_REQUEST['dm'] : date('Y')."-".date('m');
$offDays 	= array("Sat", "Sun");
$curDate	= date('d');
$attnImgs 	= array("cross.gif", "tick.gif");


require_once($BASEPATH . "/preTallyClass/UserClass.php");

$UserObj = new UserClass();
$UserObj->viewUser(' WHERE US_Status != 5 ORDER BY US_FName');
$US_Obj = $UserObj->UserArray;

$UserObj->viewUserAttendance(' WHERE DATE_FORMAT(AT_Date, "%Y-%m") = "'.$dm.'" ORDER BY AT_Id');
$AT_Obj = $UserObj->UserAttendanceArray;

$UserObj->viewUserHolidays(' WHERE DATE_FORMAT(HD_Date, "%Y-%m") = "'.$dm.'" ORDER BY HD_Id');
$HD_Obj = $UserObj->UserHolidaysArray;

function objectToArray ($object) {
    if(!is_object($object) && !is_array($object))
        return $object;

    return array_map('objectToArray', (array) $object);
}

function multi_array_search($array, $search) {
	$result = array();
	foreach ($array as $key => $value) {
		foreach ($search as $k => $v) {
			if (!isset($value[$k]) || $value[$k] != $v) {
				continue 2;
			}
		}
		$result[] = $key;
	}
	return $result;
}

$AT_Array = objectToArray($AT_Obj);
$HD_Array = objectToArray($HD_Obj);

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
		<userdata name="db_table">channel</userdata>
		<userdata name="db_primary">CN_Id</userdata>
		<userdata name="db_date">CN_Update</userdata>
		<userdata name="db_status">CN_Status</userdata>
		<head>
			<column width="100"  type="ro" align="left" 	sort="int">	Emp: ID </column>
			<column width="180"  type="ro"  	align="left" 	sort="int">	Name </column>';
			
			for($j=1; $j<= $curDate; $j++) {
				
				$type = 'ch';
				if ((in_array(date('D', strtotime($dm."-".$j)), $offDays)) || (multi_array_search($HD_Array, array('HD_Date' => $dm.'-'.sprintf("%02s", $j)))) || (date( 'Y-m-d', strtotime ( '-6 day' . $today ))  >   date("Y-m-d", strtotime($dm."-".$j)))) $type = 'ro';
				
				echo "<column width='35' type='".$type."' align='center' sort='str'>".date("jS (D)", strtotime($dm."-".$j))."</column>";
				echo "<column width='35' type='".$type."' align='center' sort='str'>#cspan</column>";
				//ro
			}
			echo '<column width="100" type="txt" align="left" sort="str"> Present Days </column>
			<column width="30" type="txt" align="center" sort="str"></column>
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
					<param>#text_filter,#text_filter,';
					for($j=1; $j<= $curDate; $j++) {
						if ((in_array(date('D', strtotime($dm."-".$j)), $offDays)) || (multi_array_search($HD_Array, array('HD_Date' => $dm.'-'.sprintf("%02s", $j))))) { 
							echo ",#cspan,";
						} else {
							echo 'AM,PM,';
						}
					}
					echo ',</param>
				</call>	
				
			</afterInit>

		  </head>';
		  
		if($US_Obj) {
			$i = 1;
			foreach($US_Obj as $rw) {
				$day 		= 0;
				$dayPresent = 0;
				echo '<row id="'.$i.'">
					<cell>'.$rw->US_EMPID.'</cell>
					<cell>'.$rw->US_FName.' '.$rw->US_MName.' '.$rw->US_LName.'</cell>';
					for($j=1; $j<= $curDate; $j++) {
						if ((in_array(date('D', strtotime($dm."-".$j)), $offDays)) || (multi_array_search($HD_Array, array('HD_Date' => $dm.'-'.sprintf("%02s", $j))))) {
							echo '<cell></cell>';
							echo '<cell></cell>';
						} else {
							$mAttnd = 0; $eAttnd = 0;
							if(multi_array_search($AT_Array, array('AT_Date' => $dm.'-'.sprintf("%02s", $j), 'AT_ME' => 'M', 'US_Id' => $rw->US_Id))) {
								$mAttnd = 1;
								$dayPresent++;
							}
							if(multi_array_search($AT_Array, array('AT_Date' => $dm.'-'.sprintf("%02s", $j), 'AT_ME' => 'E', 'US_Id' => $rw->US_Id))) {
								$eAttnd = 1;
								$dayPresent++;
							}
							
							if(date( 'Y-m-d', strtotime ( '-6 day' . $today ))  >   date("Y-m-d", strtotime($dm."-".$j))) {
								echo '<cell><![CDATA[<img src="images/icon/'.$attnImgs[$mAttnd].'" style="margin:2px 0;" />]]></cell>';
								echo '<cell><![CDATA[<img src="images/icon/'.$attnImgs[$eAttnd].'" style="margin:2px 0;" />]]></cell>';
							} else {
								echo '<cell dataDate="'.date("Y-m-d", strtotime($dm."-".$j)).'" dataME="M" userID="'.$rw->US_Id.'">'.$mAttnd.'</cell>';
								echo '<cell dataDate="'.date("Y-m-d", strtotime($dm."-".$j)).'" dataME="E" userID="'.$rw->US_Id.'">'.$eAttnd.'</cell>';
							}
							$day++;
						}
					}
				echo '<cell>'.($dayPresent/2).' / '.$day.' Day(s)</cell>
					  <cell><![CDATA[<img src="images/icon/setting_icon.png" style="margin:2px 0;" class="balSheet_popup"/>]]></cell>
				</row>';
				$i++;
			}
		}

echo '</rows>';
?>