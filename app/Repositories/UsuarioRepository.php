<?php
namespace App\Repositories;

use PDO;

class UsuarioRepository extends BaseRepository {
    protected $table = 'usuarios';

    public function findByUsuario($usuario) {
        // Permitimos buscar por el identificador de usuario o por el correo electrónico
        $stmt = $this->db->prepare("SELECT id, nombre, usuario, email, password, rol, rol_id, estado FROM {$this->table} WHERE (usuario = ? OR email = ?) AND estado = 1");
        $stmt->execute([$usuario, $usuario]);
        return $stmt->fetch();
    }

    public function updateUltimoLogin($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ultimo_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nombre, usuario, password, rol, estado) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nombre'],
            $data['usuario'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['rol'] ?? 'operador',
            $data['estado'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET nombre = ?, usuario = ?, rol = ?, estado = ?";
        $params = [$data['nombre'], $data['usuario'], $data['rol'], $data['estado']];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    /**
     * Elimina un usuario por ID.
     * Lanza excepción si tiene registros vinculados (sesiones, pagos).
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
