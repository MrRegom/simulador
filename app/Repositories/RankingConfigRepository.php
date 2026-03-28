<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class RankingConfigRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function get($key, $default = null) {
        $stmt = $this->db->prepare("SELECT value_text FROM ranking_config WHERE key_name = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value_text'] : $default;
    }

    public function set($key, $value) {
        $sql = "INSERT INTO ranking_config (key_name, value_text) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE value_text = VALUES(value_text)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$key, $value]);
    }
    
    public function getAllConfigs() {
        try {
            $stmt = $this->db->query("SELECT * FROM ranking_config");
            $configs = [];
            if ($stmt) {
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $configs[$row['key_name']] = $row['value_text'];
                }
            }
            return $configs;
        } catch (\PDOException $e) {
            // Retorna un array vacío de configs si la tabla aún no existe
            return [];
        }
    }
}
