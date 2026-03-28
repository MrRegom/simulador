<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\SesionRepository;
use App\Repositories\EquipoRepository;
use App\Repositories\PagoRepository;

class SesionController extends Controller {

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); return;
        }

        // Datos del Modal
        $equipoId = $_POST['equipo_id'] ?? null;
        $tiempo = $_POST['tiempo'] ?? 0;
        $monto = $_POST['monto'] ?? 0;
        $metodo = $_POST['metodo'] ?? 'efectivo';

        // Validaciones Básicas
        if (!$equipoId || $tiempo <= 0) {
            echo json_encode(['error' => 'Datos Incompletos']);
            return;
        }

        // Hora Fin
        $inicio = date('Y-m-d H:i:s');
        $fin = date('Y-m-d H:i:s', strtotime("+$tiempo minutes"));

        // 1. Crear Sesión
        $sesionRepo = new SesionRepository();
        $sesionId = $sesionRepo->create([
            'equipo_id' => $equipoId,
            'usuario_id' => 1, // Hardcoded usuario admin por ahora
            'tiempo' => $tiempo,
            'inicio' => $inicio,
            'fin' => $fin
        ]);

        if ($sesionId) {
            // 2. Registrar Pago
            $pagoRepo = new PagoRepository();
            $pagoRepo->create([
                'sesion_id' => $sesionId,
                'monto' => $monto,
                'metodo' => $metodo
            ]);

            // 3. Desbloquear Equipo
            $equipoRepo = new EquipoRepository();
            $equipoRepo->updateEstado($equipoId, 'en_uso');

            echo json_encode(['status' => 'success', 'msg' => 'Sesión Iniciada']);
        } else {
            echo json_encode(['error' => 'Error al crear sesión']);
        }
    }
}
