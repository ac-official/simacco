<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/OldStockClass.php");

$OldStockObj  = new OldStockClass();
        
$OldStockObj->viewStockBalances($preTally_user_ofid);
$OS_Obj = $OldStockObj->OldStockArray;
$Month = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
			<column width="50" type="ro" align="center" sort="na"> SlNo </column>
			<column width="*" type="ro" align="left" sort="na">Branch</column>                        
                        <column width="*" type="ro" align="left" sort="na">Stock Opening Balance</column>
                        <column width="*" type="ro" align="left" sort="na">Track </column>
                        <column width="*" type="ro" align="left" sort="na">Date</column>
                        <column width="0" type="ro" align="left" sort="na">Month</column>
                        <column width="*" type="ro" align="left" sort="na">Status</column>
                       <column width="*" type="ro" align="left" sort="na"></column>
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
			<!--afterInit>
				<call command="attachHeader">
                                <param>,#text_filter_inc,,,,#select_filter_strict,</param>
                                </call>	
                                
			</afterInit-->

		  </head>';
		  if($OS_Obj) {
                        $j = 1;
                        foreach($OS_Obj as $rw) {

                            echo '<row id="'.$rw->OS_Id.'">       
                                <userdata name="OS_Status">'.$rw->OS_Status.'</userdata>
                                <userdata name="OS_Id">'.$rw->OS_Id.'</userdata>
                                <userdata name="LC_Name">'.$rw->LC_Name.'</userdata>
                                        

                                <cell>'.$j.'</cell>
                                <cell name="LC_Name">'.$rw->LC_Name.'</cell>
                                <cell name="OS_OpenBal">'.$rw->OS_OpenBal.'</cell>
                                <cell name="TR_Track">'.$rw->TR_Track.'</cell>
                                <cell name="OS_Date">'.date('d/m/Y',strtotime($rw->OS_Date)).'</cell>';
                            echo'<cell>'.$Month[date("m",strtotime($rw->OS_Date))].'</cell>'; 
                            if($rw->OS_Status==0){$status='Not Set'; }else{ $status='Set'; }
                            
                            echo '<cell name="OS_Status">'.$status. ' </cell>   ';
                                if($rw->OS_Status==0){echo'<cell><![CDATA[<img src="images/icon/bag.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Not Set\');" onmouseout="preTally.Settings.hideLabel(this);" />]]></cell>';}
                                else{echo'<cell><![CDATA[<img src="images/icon/cash.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Set\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';}
                            echo '</row>';
                            $j++;
                        }
                    }
		  
echo '</rows>';
?>