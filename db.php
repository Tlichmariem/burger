<?php

class DataBase {
    private static $host = "127.0.0.1";
    private static $socket = '/opt/lampp/var/mysql/mysql.sock'; // Chemin du socket
    private static $dbName = "burger";
    private static $dbuser = "root";
    private static $dbpass = "";  
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection == null) {
            try {
                self::$connection = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8", self::$dbuser, self::$dbpass);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function disconnect()
    {
        self::$connection = null;
    }
}

DataBase::connect();
?>
