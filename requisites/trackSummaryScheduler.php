<?php
error_reporting(E_ALL ^ E_NOTICE);
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
} else {
    header("Content-type: text/xml");
}
echo("<?xml version='1.0' encoding='iso-8859-1'?>\n");

?>

<data>
    <event id="1">
        <text><![CDATA[Meeting]]></text>
        <start_date>06/11/2015 14:00</start_date>
        <end_date>06/11/2015 17:00</end_date>
    </event>
    <event id="2">
        <text><![CDATA[Conference]]></text>
        <start_date>06/15/2015 12:00</start_date>
        <end_date>06/18/2015 19:00</end_date>
    </event>
    <event id="3">
        <text><![CDATA[Interview]]></text>
        <start_date>06/24/2015 09:00</start_date>
        <end_date>06/24/2015 10:00</end_date>
    </event>
    <event id="4">
        <text><![CDATA[Interview]]></text>
        <start_date>06/24/2015 10:00</start_date>
        <end_date>06/24/2015 11:00</end_date>
    </event>
    <event id="5">
        <text><![CDATA[Interview]]></text>
        <start_date>06/24/2015 12:00</start_date>
        <end_date>06/24/2015 13:00</end_date>
    </event>
    <?php
    //$j = 10;
    for($j=10;$j<50;$j++)
    echo '<event id="'.$j.'">
        <text><![CDATA[Interview]]></text>
        <start_date>06/24/2015 12:00</start_date>
        <end_date>06/24/2015 13:00</end_date>
    </event>';
    ?>
</data>

