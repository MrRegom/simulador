<?php
namespace App\Repositories;

use PDO;

class RoleRepository extends BaseRepository {
    protected $table = 'roles';

    public function getPermisosPorRol($rolId) {
        $sql = "SELECT p.nombre FROM permisos p 
                JOIN roles_permisos rp ON p.id = rp.permiso_id 
                WHERE rp.rol_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rolId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function savePermisos($rolId, $permisosIds) {
        $this->db->beginTransaction();
        try {
            // Limpiar anteriores
            $stmt = $this->db->prepare("DELETE FROM roles_permisos WHERE rol_id = ?");
            $stmt->execute([$rolId]);

            // Insertar nuevos
            if (!empty($permisosIds)) {
                $stmt = $this->db->prepare("INSERT INTO roles_permisos (rol_id, permiso_id) VALUES (?, ?)");
                foreach ($permisosIds as $pId) {
                    $stmt->execute([$rolId, $pId]);
                }
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nombre, descripcion) VALUES (?, ?)");
        return $stmt->execute([$data['nombre'], $data['descripcion']]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nombre = ?, descripcion = ? WHERE id = ?");
        return $stmt->execute([$data['nombre'], $data['descripcion'], $id]);
    }
}
