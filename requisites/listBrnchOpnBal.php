<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}

require_once($BASEPATH . "preTallyClass/CashBSClass.php");

$CashBSObj = new CashBSClass();
$CashBSObj->viewOpeningBalances($preTally_user_ofid);
$Cash_BSObj = $CashBSObj->CashBSArray;



echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
            <head>
			<column width="50" type="ro" align="center" sort=""> SlNo </column>
			<column width="*" type="ro" align="left" sort="">Branch</column>                        
                        <column width="*" type="ro" align="left" sort="">Opening Balance</column>
                        <column width="*" type="ro" align="left" sort="">Date</column>
                        <column width="0" type="ro" align="left" sort="">Status</column>
                       <column width="*" type="ro" align="left" sort="">Status</column>

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
			<afterInit>';
				/*<call command="attachHeader">
                                <param>,#text_filter,,,#select_filter_strict,</param>
                                </call>	*/
                                
			echo '</afterInit>

		  </head>';
		  if($Cash_BSObj) {
                        $j = 1;
                        foreach($Cash_BSObj as $rw) {

                            echo '<row id="'.$rw->OB_Id.'">       
                                <userdata name="OB_Status">'.$rw->OB_Status.'</userdata>
                                <cell>'.$j.'</cell>
                                <cell name="LC_Name">'.$rw->LC_Name.'</cell>
                                <cell name="OB_OpenBal">'.$rw->OB_OpenBal.'</cell>
                                <cell name="OB_Date">'.date('d/m/Y',strtotime($rw->OB_Date)).'</cell>';
                            if($rw->OB_Status==0){$status='Not Set'; }else{$status='Set'; }
                            echo '<cell name="OB_Status">'.$status. ' </cell>   ';
                                if($rw->OB_Status==0) {
                                    echo'<cell><![CDATA[<img src="images/icon/bag.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Not Set\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                } else {
                                    echo'<cell><![CDATA[<img src="images/icon/cash.png" style="margin:2px 0; cursor:pointer;" onmouseover="preTally.Settings.showLabel(this,\'Set\');" onmouseout="preTally.Settings.hideLabel(this);"/>]]></cell>';
                                }
                            echo '</row>';
                            $j++;
                        }
                    }
		  
echo '</rows>';
?>