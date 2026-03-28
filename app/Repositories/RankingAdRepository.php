<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class RankingAdRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = null) {
        $sql = "SELECT * FROM ranking_ads";
        if ($status !== null) $sql .= " WHERE is_active = " . (int)$status;
        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive() {
        // Ahora devolvemos path + duración
        $stmt = $this->db->prepare("SELECT image_path, duration_seconds FROM ranking_ads WHERE is_active = 1 ORDER BY order_index ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($path, $duration = 10) {
        $stmt = $this->db->prepare("INSERT INTO ranking_ads (image_path, duration_seconds) VALUES (?, ?)");
        return $stmt->execute([$path, $duration]);
    }

    public function updateDuration($id, $duration) {
        $stmt = $this->db->prepare("UPDATE ranking_ads SET duration_seconds = ? WHERE id = ?");
        return $stmt->execute([$duration, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM ranking_ads WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggle($id, $status) {
        $stmt = $this->db->prepare("UPDATE ranking_ads SET is_active = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function count($status = null) {
        $sql = "SELECT COUNT(*) FROM ranking_ads";
        if ($status !== null) $sql .= " WHERE is_active = " . (int)$status;
        return $this->db->query($sql)->fetchColumn();
    }

    public function getPaged($status = null, $limit = 6, $offset = 0) {
        $sql = "SELECT * FROM ranking_ads";
        if ($status !== null) $sql .= " WHERE is_active = " . (int)$status;
        $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
