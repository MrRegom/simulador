<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class PistaRepository {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM pistas WHERE is_active = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($nombre, $id = null) {
        if ($id) {
            $stmt = $this->db->prepare("UPDATE pistas SET nombre = ? WHERE id = ?");
            return $stmt->execute([$nombre, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO pistas (nombre, is_active, created_at) VALUES (?, 1, NOW())");
            return $stmt->execute([$nombre]);
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE pistas SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
