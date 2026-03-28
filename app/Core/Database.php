<?php
namespace App\Core;

use PDO;
use PDOException;
use App\Config\Config;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::getDBName() . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, Config::getDBUser(), Config::getDBPass(), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, verifique su configuración.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
