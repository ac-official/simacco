<?php
echo 'OPCache is ' . (is_array(opcache_get_status()) ? 'enabled' : 'disabled');
?>