<?php
namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;
    
    public static function getConnection(): PDO
    {
        if (self::$connection === null) 
        {

            try{
                self::$connection = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASS']);
                self::$connection ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }  catch(PDOException $e){
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
            
        }
        return self::$connection;
    }
}



?>