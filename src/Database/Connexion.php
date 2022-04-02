<?php 
namespace App\Database;

use PDO;

class Connexion extends PDO {

    private static $pdo;

    public static function getPDO(bool $throwPDOException = true): PDO 
    {
        if(!isset(self::$pdo)) {
            self::$pdo = new PDO(
                'mysql:dbname='.DB_NAME.';host='.DB_HOST, 
                DB_USERNAME, 
                DB_PASSWORD
            );
            if($throwPDOException) {
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
            }
        }
        return self::$pdo;
    }

    public static function setErrModeToNormalIfNotYet(): void
    {
        if (self::$pdo->getAttribute(PDO::ATTR_ERRMODE) !== 0) { // "0" stands for "ERRMODE_SILENT"
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        }
    }

}