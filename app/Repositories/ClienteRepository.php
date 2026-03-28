<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClienteRepository {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function buscarPorTelefono($telefono) {
        $sql = "SELECT * FROM clientes WHERE telefono = :telefono";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':telefono' => $telefono]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorRut($rut) {
        $sql = "SELECT * FROM clientes WHERE rut = :rut";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rut' => $rut]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($telefono, $nombre, $rut = null, $email = null) {
        $sql = "INSERT INTO clientes (telefono, nombre, rut, email, visitas) 
                VALUES (:telefono, :nombre, :rut, :email, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':telefono' => $telefono,
            ':nombre' => $nombre,
            ':rut' => $rut,
            ':email' => $email
        ]);
        return $this->db->lastInsertId();
    }

    public function registrarVisita($id) {
        $sql = "UPDATE clientes SET visitas = visitas + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function actualizarRut($id, $rut) {
        $sql = "UPDATE clientes SET rut = :rut WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':rut' => $rut]);
    }

    public function actualizarNombre($id, $nombre) {
        $sql = "UPDATE clientes SET nombre = :nombre WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':nombre' => $nombre]);
    }

    public function actualizarTelefono($id, $telefono) {
        $sql = "UPDATE clientes SET telefono = :telefono WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':telefono' => $telefono]);
    }

    public function actualizarEmail($id, $email) {
        $sql = "UPDATE clientes SET email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':email' => $email]);
    }

    public function sugerir($term) {
        $cleanSearch = preg_replace('/[^0-9a-zA-Z]/', '', $term);
        
        $sql = "SELECT id, nombre, rut, telefono, email 
                FROM clientes 
                WHERE REPLACE(REPLACE(rut, '.', ''), '-', '') LIKE :clean
                   OR nombre LIKE :term1
                   OR telefono LIKE :term2
                   OR email LIKE :term3
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $likeTerm = "%$term%";
        $likeClean = "%$cleanSearch%";
        
        $stmt->execute([
            ':clean' => $likeClean,
            ':term1' => $likeTerm,
            ':term2' => $likeTerm,
            ':term3' => $likeTerm
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
