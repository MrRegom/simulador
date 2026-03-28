<?php
namespace App\Repositories;

class PagoRepository extends BaseRepository {
    protected $table = 'pagos';

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (sesion_id, monto, metodo_pago, referencia_pago, fecha_pago) 
                VALUES (:sesion, :monto, :metodo, :referencia, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sesion' => $data['sesion_id'],
            ':monto' => $data['monto'],
            ':metodo' => $data['metodo'],
            ':referencia' => $data['referencia'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }

    public function getTotalHoy() {
        $sql = "SELECT COALESCE(SUM(monto), 0) as total 
                FROM {$this->table} 
                WHERE DATE(fecha_pago) = CURDATE()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    public function getResumenFinanciero() {
        // Ingresos Hoy
        $hoy = $this->getTotalHoy();

        // Ingresos Semana
        $sqlSemana = "SELECT SUM(monto) as total FROM {$this->table} WHERE YEARWEEK(fecha_pago, 1) = YEARWEEK(CURDATE(), 1)";
        $stmt = $this->db->prepare($sqlSemana);
        $stmt->execute();
        $semana = $stmt->fetch()['total'] ?? 0;

        // Ingresos Mes
        $sqlMes = "SELECT SUM(monto) as total FROM {$this->table} WHERE MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())";
        $stmt = $this->db->prepare($sqlMes);
        $stmt->execute();
        $mes = $stmt->fetch()['total'] ?? 0;

        return ['hoy' => $hoy, 'semana' => $semana, 'mes' => $mes];
    }

    public function getIngresosDiarios($dias = 7) {
        $sql = "SELECT DATE(fecha_pago) as fecha, SUM(monto) as total 
                FROM {$this->table} 
                GROUP BY DATE(fecha_pago) 
                ORDER BY fecha DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $dias, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTrazabilidad($limit = 50, $filtros = []) {
        $sql = "SELECT p.*, s.tiempo_asignado_min, e.nombre as equipo, c.nombre as cliente, c.rut 
                FROM pagos p
                JOIN sesiones s ON p.sesion_id = s.id
                JOIN equipos e ON s.equipo_id = e.id
                LEFT JOIN clientes c ON s.cliente_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['fecha'])) {
            $sql .= " AND DATE(p.fecha_pago) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        } elseif (!empty($filtros['semana'])) {
            $sql .= " AND YEARWEEK(p.fecha_pago, 1) = :semana";
            $params[':semana'] = $filtros['semana'];
        } elseif (!empty($filtros['mes']) && !empty($filtros['anio'])) {
            $sql .= " AND MONTH(p.fecha_pago) = :mes AND YEAR(p.fecha_pago) = :anio";
            $params[':mes'] = $filtros['mes'];
            $params[':anio'] = $filtros['anio'];
        } elseif (!empty($filtros['anio'])) {
            $sql .= " AND YEAR(p.fecha_pago) = :anio";
            $params[':anio'] = $filtros['anio'];
        }

        $sql .= " ORDER BY p.fecha_pago DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
