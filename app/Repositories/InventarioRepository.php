<?php
namespace App\Repositories;

class InventarioRepository extends BaseRepository {
    protected $table = 'inventario_productos';

    public function listAll($search = '') {
        $params = [];
        $where = '';
        if ($search) {
            $where = "WHERE nombre LIKE :s OR codigo LIKE :s";
            $params[':s'] = '%' . $search . '%';
        }
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (nombre, codigo, precio, stock, imagen_path, activo, created_at, updated_at)
                VALUES (:nombre, :codigo, :precio, :stock, :imagen, :activo, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':codigo' => $data['codigo'],
            ':precio' => $data['precio'],
            ':stock' => $data['stock'],
            ':imagen' => $data['imagen_path'],
            ':activo' => $data['activo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET nombre = :nombre,
                    codigo = :codigo,
                    precio = :precio,
                    stock = :stock,
                    imagen_path = :imagen,
                    activo = :activo,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':codigo' => $data['codigo'],
            ':precio' => $data['precio'],
            ':stock' => $data['stock'],
            ':imagen' => $data['imagen_path'],
            ':activo' => $data['activo'] ?? 1
        ]);
        return true;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function codeExists($codigo, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE codigo = :c";
        $params = [':c' => $codigo];
        if ($excludeId) {
            $sql .= " AND id <> :id";
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function generateCode($nombre) {
        $clean = preg_replace('/[^a-zA-Z]/', '', $nombre ?? '');
        $prefix = strtoupper(substr($clean, 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $stmt = $this->db->prepare("SELECT codigo FROM {$this->table} WHERE codigo LIKE :p ORDER BY CAST(SUBSTRING(codigo, 4) AS UNSIGNED) DESC LIMIT 1");
        $stmt->execute([':p' => $prefix . '%']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $next = 1;
        if ($row && isset($row['codigo'])) {
            $num = (int)substr($row['codigo'], 3);
            $next = $num + 1;
        }
        return $prefix . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }
}

