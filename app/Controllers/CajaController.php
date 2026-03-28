<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\CajaRepository;

class CajaController extends Controller {
    public function status() {
        header('Content-Type: application/json');
        try {
            $repo = new CajaRepository();
            $open = $repo->getOpenCaja();
            $todayFrom = date('Y-m-d 00:00:00');
            $todayTo = date('Y-m-d 23:59:59');
            $resumen = $repo->getResumen($todayFrom, $todayTo);
            echo json_encode([
                'success' => true,
                'open' => $open ? true : false,
                'caja' => $open,
                'resumen' => $resumen
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function open() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $saldo = isset($input['saldo_inicial']) ? (float)$input['saldo_inicial'] : 0;
            $notas = $input['notas'] ?? null;
            $usuarioId = $_SESSION['usuario_id'] ?? 1;

            $repo = new CajaRepository();
            $open = $repo->getOpenCaja();
            if ($open) {
                throw new \Exception('CAJA_YA_ABIERTA');
            }

            $id = $repo->openCaja($usuarioId, $saldo, $notas, 0);
            echo json_encode(['success' => true, 'caja_id' => $id]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function close() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $saldoFinal = isset($input['saldo_final']) ? (float)$input['saldo_final'] : 0;
            $notas = $input['notas'] ?? null;
            $repo = new CajaRepository();
            $open = $repo->getOpenCaja();
            if (!$open) {
                throw new \Exception('NO_HAY_CAJA_ABIERTA');
            }
            $totals = $repo->closeCaja($open['id'], $saldoFinal, $notas);
            echo json_encode(['success' => true, 'totals' => $totals]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function movimientos() {
        header('Content-Type: application/json');
        try {
            $range = $_GET['range'] ?? 'today';
            $filters = [
                'tipo' => $_GET['tipo'] ?? null,
                'categoria' => $_GET['categoria'] ?? null,
                'metodo' => $_GET['metodo'] ?? null,
                'search' => $_GET['search'] ?? null,
                'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 100,
                'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0,
            ];

            $date = $this->resolveRange($range);
            if ($date['from']) $filters['from'] = $date['from'];
            if ($date['to']) $filters['to'] = $date['to'];

            $repo = new CajaRepository();
            $data = $repo->getMovimientos($filters);
            echo json_encode(['success' => true, 'data' => $data['data'], 'total' => $data['total']]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function movimiento() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new \Exception('DATOS_INVALIDOS');

            $tipo = $input['tipo'] ?? null;
            $categoria = $input['categoria'] ?? null;
            $metodo = $input['metodo_pago'] ?? 'efectivo';
            $monto = isset($input['monto']) ? (float)$input['monto'] : 0;

            if (!$tipo || !$categoria || $monto <= 0) {
                throw new \Exception('DATOS_INCOMPLETOS');
            }

            $usuarioId = $_SESSION['usuario_id'] ?? 1;
            $repo = new CajaRepository();
            $cajaId = $repo->ensureOpenCaja($usuarioId);

            $id = $repo->addMovimiento([
                'caja_id' => $cajaId,
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'categoria' => $categoria,
                'metodo_pago' => $metodo,
                'monto' => $monto,
                'referencia' => $input['referencia'] ?? null,
                'descripcion' => $input['descripcion'] ?? null,
                'origen' => 'manual'
            ]);

            echo json_encode(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function resolveRange($range) {
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
        return ['from' => $from, 'to' => $to];
    }
}
