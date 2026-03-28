<?php
namespace App\Controllers;
use App\Core\Controller;

use App\Repositories\PagoRepository;

class ReporteController extends Controller {
    public function index() {
        $repo = new PagoRepository();
        
        $tipo = $_GET['tipo'] ?? 'mes';
        $filtros = [];
        
        // Determinar filtros según el tipo seleccionado
        switch($tipo) {
            case 'dia':
                $filtros['fecha'] = $_GET['fecha'] ?? date('Y-m-d');
                break;
            case 'semana':
                $filtros['semana'] = $_GET['semana'] ?? date('YW');
                break;
            case 'mes':
                $filtros['mes'] = $_GET['mes'] ?? date('m');
                $filtros['anio'] = $_GET['anio'] ?? date('Y');
                break;
            case 'anio':
                $filtros['anio'] = $_GET['anio'] ?? date('Y');
                break;
        }

        $resumen = $repo->getResumenFinanciero();
        $trazabilidad = $repo->getTrazabilidad(100, $filtros);

        // Calcular total del período filtrado
        $totalPeriodo = 0;
        foreach ($trazabilidad as $t) {
            $totalPeriodo += $t['monto'];
        }

        $this->view('reportes/index', [
            'page_title' => 'Auditoría y Finanzas',
            'resumen' => $resumen,
            'trazabilidad' => $trazabilidad,
            'total_periodo' => $totalPeriodo,
            'filtros' => $filtros,
            'tipo_filtro' => $tipo
        ]);
    }

    public function api_resumen() {
        header('Content-Type: application/json');
        try {
            $range = $_GET['range'] ?? '30d';
            $from = null;
            $to = null;
            switch ($range) {
                case 'today':
                    $from = date('Y-m-d 00:00:00');
                    $to = date('Y-m-d 23:59:59');
                    break;
                case '7d':
                    $from = date('Y-m-d 00:00:00', strtotime('-6 days'));
                    $to = date('Y-m-d 23:59:59');
                    break;
                case '30d':
                    $from = date('Y-m-d 00:00:00', strtotime('-29 days'));
                    $to = date('Y-m-d 23:59:59');
                    break;
                case 'month':
                    $from = date('Y-m-01 00:00:00');
                    $to = date('Y-m-t 23:59:59');
                    break;
                case 'all':
                default:
                    $from = null;
                    $to = null;
                    break;
            }

            $db = \App\Core\Database::getInstance()->getConnection();
            $where = "WHERE 1=1";
            $params = [];
            if ($from) {
                $where .= " AND m.created_at >= :from";
                $params[':from'] = $from;
            }
            if ($to) {
                $where .= " AND m.created_at <= :to";
                $params[':to'] = $to;
            }

            $sqlTotals = "SELECT
                            COALESCE(SUM(CASE WHEN m.tipo = 'ingreso' THEN m.monto ELSE 0 END), 0) as ingresos,
                            COALESCE(SUM(CASE WHEN m.tipo = 'egreso' THEN m.monto ELSE 0 END), 0) as egresos,
                            COALESCE(SUM(CASE WHEN m.tipo = 'ingreso' THEN 1 ELSE 0 END), 0) as ventas,
                            COALESCE(AVG(CASE WHEN m.tipo = 'ingreso' THEN m.monto END), 0) as ticket_promedio
                          FROM caja_movimientos m {$where}";
            $stmt = $db->prepare($sqlTotals);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $totals = $stmt->fetch(\PDO::FETCH_ASSOC);

            $sqlMetodo = "SELECT m.metodo_pago, SUM(m.monto) as total
                          FROM caja_movimientos m
                          {$where} AND m.tipo = 'ingreso'
                          GROUP BY m.metodo_pago
                          ORDER BY total DESC";
            $stmt = $db->prepare($sqlMetodo);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $byMetodo = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlCategoria = "SELECT m.categoria, m.tipo, SUM(m.monto) as total
                             FROM caja_movimientos m
                             {$where}
                             GROUP BY m.categoria, m.tipo
                             ORDER BY total DESC";
            $stmt = $db->prepare($sqlCategoria);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $byCategoria = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlEquipo = "SELECT e.nombre as equipo, SUM(m.monto) as total
                          FROM caja_movimientos m
                          LEFT JOIN equipos e ON m.equipo_id = e.id
                          {$where} AND m.tipo = 'ingreso'
                          GROUP BY e.nombre
                          ORDER BY total DESC
                          LIMIT 10";
            $stmt = $db->prepare($sqlEquipo);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $byEquipo = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlPista = "SELECT m.pista, SUM(m.monto) as total
                         FROM caja_movimientos m
                         {$where} AND m.tipo = 'ingreso' AND m.pista IS NOT NULL AND m.pista <> ''
                         GROUP BY m.pista
                         ORDER BY total DESC
                         LIMIT 10";
            $stmt = $db->prepare($sqlPista);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $byPista = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlUsuario = "SELECT u.nombre as usuario, SUM(m.monto) as total
                           FROM caja_movimientos m
                           LEFT JOIN usuarios u ON m.usuario_id = u.id
                           {$where} AND m.tipo = 'ingreso'
                           GROUP BY u.nombre
                           ORDER BY total DESC";
            $stmt = $db->prepare($sqlUsuario);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $byUsuario = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $sqlDaily = "SELECT DATE(m.created_at) as fecha, SUM(m.monto) as total
                         FROM caja_movimientos m
                         {$where} AND m.tipo = 'ingreso'
                         GROUP BY DATE(m.created_at)
                         ORDER BY fecha DESC
                         LIMIT 30";
            $stmt = $db->prepare($sqlDaily);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
            $stmt->execute();
            $daily = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'totals' => $totals,
                'by_metodo' => $byMetodo,
                'by_categoria' => $byCategoria,
                'by_equipo' => $byEquipo,
                'by_pista' => $byPista,
                'by_usuario' => $byUsuario,
                'daily' => $daily
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
