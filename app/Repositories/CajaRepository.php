<?php
namespace App\Repositories;

class CajaRepository extends BaseRepository {
    protected $table = 'caja_movimientos';

    public function getOpenCaja() {
        $sql = "SELECT * FROM caja_turnos WHERE estado = 'abierta' ORDER BY opened_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function openCaja($usuarioId, $saldoInicial = 0, $notas = null, $auto = 0) {
        $sql = "INSERT INTO caja_turnos (usuario_id, saldo_inicial, opened_at, estado, notas, auto_opened)
                VALUES (:usuario_id, :saldo_inicial, NOW(), 'abierta', :notas, :auto_opened)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':saldo_inicial' => $saldoInicial,
            ':notas' => $notas,
            ':auto_opened' => $auto ? 1 : 0
        ]);
        return $this->db->lastInsertId();
    }

    public function closeCaja($cajaId, $saldoFinal, $notas = null) {
        $totals = $this->getTotalesByCaja($cajaId);
        $sql = "UPDATE caja_turnos
                SET closed_at = NOW(),
                    saldo_final = :saldo_final,
                    total_ingresos = :ingresos,
                    total_egresos = :egresos,
                    estado = 'cerrada',
                    notas_cierre = :notas
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':saldo_final' => $saldoFinal,
            ':ingresos' => $totals['ingresos'],
            ':egresos' => $totals['egresos'],
            ':notas' => $notas,
            ':id' => $cajaId
        ]);
        return $totals;
    }

    public function ensureOpenCaja($usuarioId) {
        $open = $this->getOpenCaja();
        if ($open && isset($open['id'])) {
            return $open['id'];
        }
        return $this->openCaja($usuarioId, 0, 'APERTURA_AUTOMATICA', 1);
    }

    public function addMovimiento($data) {
        $sql = "INSERT INTO caja_movimientos
                (caja_id, sesion_id, usuario_id, cliente_id, equipo_id, pista, minutos,
                 tipo, categoria, metodo_pago, monto, referencia, descripcion, origen)
                VALUES
                (:caja_id, :sesion_id, :usuario_id, :cliente_id, :equipo_id, :pista, :minutos,
                 :tipo, :categoria, :metodo_pago, :monto, :referencia, :descripcion, :origen)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':caja_id' => $data['caja_id'] ?? null,
            ':sesion_id' => $data['sesion_id'] ?? null,
            ':usuario_id' => $data['usuario_id'] ?? null,
            ':cliente_id' => $data['cliente_id'] ?? null,
            ':equipo_id' => $data['equipo_id'] ?? null,
            ':pista' => $data['pista'] ?? null,
            ':minutos' => $data['minutos'] ?? null,
            ':tipo' => $data['tipo'],
            ':categoria' => $data['categoria'],
            ':metodo_pago' => $data['metodo_pago'] ?? 'efectivo',
            ':monto' => $data['monto'],
            ':referencia' => $data['referencia'] ?? null,
            ':descripcion' => $data['descripcion'] ?? null,
            ':origen' => $data['origen'] ?? 'sistema'
        ]);
        return $this->db->lastInsertId();
    }

    public function getTotalesByCaja($cajaId) {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END), 0) as ingresos,
                    COALESCE(SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END), 0) as egresos
                FROM caja_movimientos
                WHERE caja_id = :caja_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':caja_id' => $cajaId]);
        $row = $stmt->fetch();
        return [
            'ingresos' => $row['ingresos'] ?? 0,
            'egresos' => $row['egresos'] ?? 0
        ];
    }

    public function getMovimientos($filters = []) {
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 100;
        $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
        if ($limit < 1) $limit = 50;
        if ($limit > 300) $limit = 300;

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['from'])) {
            $where .= " AND m.created_at >= :from";
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where .= " AND m.created_at <= :to";
            $params[':to'] = $filters['to'];
        }
        if (!empty($filters['tipo'])) {
            $where .= " AND m.tipo = :tipo";
            $params[':tipo'] = $filters['tipo'];
        }
        if (!empty($filters['categoria'])) {
            $where .= " AND m.categoria = :categoria";
            $params[':categoria'] = $filters['categoria'];
        }
        if (!empty($filters['metodo'])) {
            $where .= " AND m.metodo_pago = :metodo";
            $params[':metodo'] = $filters['metodo'];
        }
        if (!empty($filters['search'])) {
            $where .= " AND (c.nombre LIKE :search OR e.nombre LIKE :search OR m.pista LIKE :search OR m.referencia LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT m.*, e.nombre as equipo, c.nombre as cliente, u.nombre as usuario
                FROM caja_movimientos m
                LEFT JOIN equipos e ON m.equipo_id = e.id
                LEFT JOIN clientes c ON m.cliente_id = c.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                {$where}
                ORDER BY m.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) as total FROM caja_movimientos m {$where}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'] ?? 0;

        return ['data' => $rows, 'total' => (int)$total];
    }

    public function getResumen($from = null, $to = null) {
        $where = "WHERE 1=1";
        $params = [];
        if ($from) {
            $where .= " AND created_at >= :from";
            $params[':from'] = $from;
        }
        if ($to) {
            $where .= " AND created_at <= :to";
            $params[':to'] = $to;
        }

        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END), 0) as ingresos,
                    COALESCE(SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END), 0) as egresos,
                    COALESCE(SUM(CASE WHEN tipo = 'ingreso' THEN 1 ELSE 0 END), 0) as ventas,
                    COALESCE(AVG(CASE WHEN tipo = 'ingreso' THEN monto END), 0) as ticket_promedio
                FROM caja_movimientos
                {$where}";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
