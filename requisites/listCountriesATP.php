<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}

include_once($BASEPATH . "preTallyClass/GeneralClass.php");
$GeneralObj = new GeneralClass();
$GeneralObj->ViewDetails(' * ', 'countries', ' 1 ', ' CN_Name ');
$CN_Obj = $GeneralObj->DataArray;

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");


echo '<rows>	
        <head>
            <column width="40" type="ro" align="center" sort="int"> # </column>
            <column width="*" type="ro" align="left" sort="str"> Country </column>';
            if($REQUEST['for'] == 'TO') echo '<column width="80" type="ch" align="center" sort="str"> Select </column>';
            echo '<settings>
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
                    <param>,#text_filter,</param>
                </call>

            </afterInit>
        </head>';
        if($CN_Obj){
            $j = 1;
            if($REQUEST['for'] == 'TO') {
                echo '<row id = "1000"><cell>'.$j.'</cell><cell>General</cell><cell>0</cell></row>';
                $j++;
            }
            foreach($CN_Obj as $rw) {
                echo '<row id="'.$rw->CN_Id.'">
                        <cell>'.$j.'</cell>
                        <cell><![CDATA['.$rw->CN_Name.']]></cell>';
                        if($REQUEST['for'] == 'TO')  echo '<cell>0</cell>';
                      echo '</row>';
                $j++;
            }
        }
echo '</rows>';
?>