<?php
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
require_once($BASEPATH . "preTallyClass/UserClass.php");

echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");
echo '<rows>		
<userdata name=""></userdata>
<head>
    <column width="30" type="ro" align="center" >Members</column>
    <column width="*" type="ro" align="left" >#cspan</column>
    <column width="20" type="ro" align="center" >#cspan</column>


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

        </call>
    </afterInit>  

</head>';

    echo '<row id="1">						
        <cell><![CDATA[<img src="images/icon/IMIcon/on.png" />]]></cell>
        <cell name="Anoopkumar">Anoopkumar</cell>
        <cell><![CDATA[<img src="images/icon/IMIcon/nc.gif" />]]></cell>
    </row>
    <row id="2">						
        <cell><![CDATA[<img src="images/icon/IMIcon/on.png" />]]></cell>
        <cell name="Besin">Besin</cell>
        <cell></cell>
    </row>
    <row id="3">						
        <cell><![CDATA[<img src="images/icon/IMIcon/on.png" />]]></cell>
        <cell name="Preeja">Preeja</cell>
        <cell></cell>
    </row>
    <row id="4">						
        <cell><![CDATA[<img src="images/icon/IMIcon/on.png" />]]></cell>
        <cell name="Deepak">Deepak</cell>
        <cell></cell>
    </row>
    <row id="5">						
        <cell><![CDATA[<img src="images/icon/IMIcon/on.png" />]]></cell>
        <cell name="Reshma">Reshma</cell>
        <cell></cell>
    </row>
    <row id="6">						
        <cell><![CDATA[<img src="images/icon/IMIcon/of.png" />]]></cell>
        <cell name="Reeni">Reeni</cell>
        <cell></cell>
    </row>
    <row id="7">						
        <cell><![CDATA[<img src="images/icon/IMIcon/of.png" />]]></cell>
        <cell name="Arya">Arya</cell>
        <cell></cell>
    </row>';
       
   

echo '</rows>';
?>