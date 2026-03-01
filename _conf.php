<?php
abstract class Settings {
    static private $protected = array(); // For DB / passwords etc
    static private $public = array(); // For all public strings such as meta stuff for site

    public static function getProtected($key) {
        return isset(self::$protected[$key]) ? self::$protected[$key] : false;
    }

    public static function getPublic($key) {
        return isset(self::$public[$key]) ? self::$public[$key] : false;
    }

    public static function setProtected($key,$value) {
        self::$protected[$key] = $value;
    }

    public static function setPublic($key,$value) {
        self::$public[$key] = $value;
    }

    public function __get($key) {//$this->key // returns public->key
        return isset(self::$public[$key]) ? self::$public[$key] : false;
    }

    public function __isset($key) {
        return isset(self::$public[$key]);
    }
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
    $url = "https://";   
else  
    $url = "http://"; 
Settings::setProtected('db_hostname', '127.0.0.1');
Settings::setProtected('db_port', '3306');
Settings::setProtected('db_username', 'root');
Settings::setProtected('db_password', 'Password@123');
Settings::setProtected('db_database', 'simmaco');
Settings::setProtected('db_charset', 'UTF-8');
Settings::setProtected('ajax_check', 'false');
Settings::setProtected('remote_address', $url.'192.168.1.8/simacco/');
Settings::setProtected('error_reporting', 'E_ALL ^ E_NOTICE');
Settings::setProtected('time_zone', 'Asia/Kolkata');
Settings::setProtected('http_referer', 'index.php');
Settings::setPublic('site_title', 'Simacco|| Powered By Muble Solutions');
Settings::setPublic('site_charset', 'UTF-8');
Settings::setPublic('site_root', $_SERVER['DOCUMENT_ROOT']."/simacco/");
Settings::setPublic('icon_path', $url.'192.168.1.8/simacco/images/icon/');
Settings::setPublic('base_path', $url.'192.168.1.8/simacco');
//echo Settings::getProtected('db_hostname');
//echo Settings::getPublic('site_title');
?>