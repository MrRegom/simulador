<?php
namespace App\Repositories;

class EquipoRepository extends BaseRepository {
    protected $table = 'equipos';

    public function countByEstado($estado) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE estado = ?");
        $stmt->execute([$estado]);
        return $stmt->fetchColumn();
    }
}
