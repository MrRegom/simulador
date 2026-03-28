<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class RankingRepository {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Guarda un nuevo tiempo en el ranking
     */
    public function guardar($cliente_id, $pista, $tiempo, $tiempo_ms, $foto_path = null) {
        $sql = "INSERT INTO ranking (cliente_id, pista, tiempo, tiempo_ms, foto_path) 
                VALUES (:cliente_id, :pista, :tiempo, :tiempo_ms, :foto_path)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cliente_id' => $cliente_id,
            ':pista' => $pista,
            ':tiempo' => $tiempo,
            ':tiempo_ms' => $tiempo_ms,
            ':foto_path' => $foto_path
        ]);
    }

    /**
     * Obtiene los mejores tiempos por pista
     */
    public function getTop($limit = 20, $pista = null) {
        $sql = "SELECT r.*, c.nombre as piloto_nombre, c.rut as piloto_rut
                FROM ranking r
                JOIN clientes c ON r.cliente_id = c.id
                WHERE 1=1 ";
        
        $pista = $pista ? trim($pista) : null;
        if ($pista && $pista !== '') {
            $sql .= "AND TRIM(r.pista) = :pista ";
        }
        
        $sql .= "ORDER BY r.tiempo_ms ASC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        if ($pista && $pista !== '') {
            $stmt->bindValue(':pista', $pista);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el mejor tiempo histórico
     */
    public function getBest($pista = null) {
        $pista = $pista ? trim($pista) : null;
        $sql = "SELECT r.*, c.nombre as piloto_nombre
                FROM ranking r
                JOIN clientes c ON r.cliente_id = c.id
                WHERE 1=1 ";
        if ($pista && $pista !== '') {
            $sql .= "AND TRIM(r.pista) = :pista ";
        }
        $sql .= "ORDER BY r.tiempo_ms ASC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        if ($pista && $pista !== '') {
            $stmt->bindValue(':pista', $pista);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el ID del último registro insertado
     */
    public function getLatestId() {
        $sql = "SELECT id FROM ranking ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $res = $stmt->fetch();
        return $res ? $res['id'] : null;
    }

    /**
     * Obtiene registros filtrados y paginados (para administración)
     */
    public function getAllAdmin($search = '', $limit = 50, $offset = 0) {
        $sql = "SELECT r.*, c.nombre as piloto_nombre, c.rut as piloto_rut
                FROM ranking r
                JOIN clientes c ON r.cliente_id = c.id ";
        
        $params = [];
        if (!empty($search)) {
            $sql .= "WHERE c.nombre LIKE :search OR c.rut LIKE :search OR r.pista LIKE :search ";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= "ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cuenta el total de registros filtrados
     */
    public function countAllAdmin($search = '') {
        $sql = "SELECT COUNT(*) as total
                FROM ranking r
                JOIN clientes c ON r.cliente_id = c.id ";
        
        $params = [];
        if (!empty($search)) {
            $sql .= "WHERE c.nombre LIKE :search OR c.rut LIKE :search OR r.pista LIKE :search ";
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        $res = $stmt->fetch();
        return $res ? (int)$res['total'] : 0;
    }

    /**
     * Obtiene un registro por ID
     */
    public function getById($id) {
        $sql = "SELECT r.*, c.nombre as piloto_nombre, c.rut as piloto_rut
                FROM ranking r
                JOIN clientes c ON r.cliente_id = c.id 
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Actualiza un tiempo existente
     */
    public function actualizar($id, $pista, $tiempo, $tiempo_ms) {
        $sql = "UPDATE ranking SET pista = :pista, tiempo = :tiempo, tiempo_ms = :tiempo_ms WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pista' => $pista,
            ':tiempo' => $tiempo,
            ':tiempo_ms' => $tiempo_ms,
            ':id' => $id
        ]);
    }

    /**
     * Elimina un registro
     */
    public function eliminar($id) {
        $sql = "DELETE FROM ranking WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
