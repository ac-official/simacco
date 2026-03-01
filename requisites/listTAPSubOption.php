<?php
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

echo '<rows>
    <head>
        <column width="50" type="ro" align="center" sort="na"> SlNo </column>
        <column width="200" type="ro" align="left" sort="na"> Process </column>
        <column width="40" type="ro" align="center" sort="na">Remove </column>
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
            <call command="enableDragAndDrop">
                <param>true</param>
            </call>

        </beforeInit> 
        <afterInit>
        </afterInit>

    </head>';

echo '<row id="1">
        <cell>1</cell>
        <cell>Process 1</cell>
        <cell><![CDATA[<img src="images/icon/trash.png" style="margin:2px 0; cursor:pointer;" />]]></cell>
    </row>
    <row id="2">
        <cell>2</cell>
        <cell>Process 2</cell>
        <cell><![CDATA[<img src="images/icon/trash.png" style="margin:2px 0; cursor:pointer;" />]]></cell>
    </row>
    <row id="3">
        <cell>3</cell>
        <cell>Process 3</cell>
        <cell><![CDATA[<img src="images/icon/trash.png" style="margin:2px 0; cursor:pointer;" />]]></cell>
    </row>
    <row id="4">
        <cell colspan="3">&lt;textarea style="width:99%;"&gt;&lt;/textarea&gt;</cell>
    </row>
    <row id="5">
        <cell colspan="3">&lt;textarea style="width:99%;"&gt;&lt;/textarea&gt;</cell>
    </row>';

		  
echo '</rows>';
?>
