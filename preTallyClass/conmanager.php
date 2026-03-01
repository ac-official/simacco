<?php 
class ConnectionManager {
    var $hostName;
    var $userName;
    var $passWord;
    var $conHandle;
    var $dataBase;
    var $hostPort;
    function getConnectionHandle() {
        return $this->conHandle;
    }
}

class MySqlConnectionManager extends ConnectionManager {
    function MySqlConnectionManager() {
        $this->hostName = Settings::getProtected('db_hostname');
        $this->hostPort = Settings::getProtected('db_port');
        $this->userName = Settings::getProtected('db_username');
        $this->passWord = Settings::getProtected('db_password');
        $this->dataBase = Settings::getProtected('db_database');
        
        /*$this->hostName = "localhost";
        $this->hostPort = "3306";
        $this->userName = "root";
        $this->passWord = "augmob";
        $this->dataBase = "pretally";*/
    }

    function doConnection() {
        if(!($this->conHandle = mysqli_connect($this->hostName, $this->userName, $this->passWord,$this->dataBase))) {			
            die("Cannot Connect to Host");
        }        
    }

    function selectDatabase() {
        mysql_select_db($this->dataBase, $this->conHandle);		
    }	
}
?>
