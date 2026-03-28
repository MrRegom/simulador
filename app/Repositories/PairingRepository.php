<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class PairingRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS pairing_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(64) NOT NULL UNIQUE,
            client_name VARCHAR(120) NULL,
            client_ip VARCHAR(45) NULL,
            equipo_id INT NULL,
            assigned_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            assigned_at DATETIME NULL,
            INDEX idx_pairing_expires (expires_at),
            INDEX idx_pairing_equipo (equipo_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->exec($sql);
    }

    public function createToken($clientIp, $clientName, $ttlSeconds = 300) {
        $token = bin2hex(random_bytes(8));
        $expires = date('Y-m-d H:i:s', time() + $ttlSeconds);

        $stmt = $this->db->prepare("INSERT INTO pairing_tokens (token, client_ip, client_name, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$token, $clientIp, $clientName, $expires]);

        return [
            'token' => $token,
            'expires_at' => $expires
        ];
    }

    public function getToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM pairing_tokens WHERE token = ? LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function assignToken($token, $equipoId, $userId) {
        $stmt = $this->db->prepare("UPDATE pairing_tokens 
            SET equipo_id = ?, assigned_by = ?, assigned_at = NOW()
            WHERE token = ? AND expires_at > NOW() AND equipo_id IS NULL");
        $stmt->execute([$equipoId, $userId, $token]);
        return $stmt->rowCount() > 0;
    }

    public function getAssignedEquipo($token) {
        $sql = "SELECT pt.equipo_id, e.nombre 
                FROM pairing_tokens pt 
                LEFT JOIN equipos e ON e.id = pt.equipo_id
                WHERE pt.token = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
