<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

abstract class BaseRepository {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function all() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function allVisible() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_visible = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    public function toggleVisibility($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_visible = NOT is_visible WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
