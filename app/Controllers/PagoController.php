<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\PagoRepository;

class PagoController extends Controller {
    /**
     * Vista principal de la Caja (Movimientos)
     */
    public function index() {
        $repo = new PagoRepository();
        
        // Obtener resumen de hoy
        $totalHoy = $repo->getTotalHoy();
        $ingresosRecientes = $repo->getTrazabilidad(50, ['fecha' => date('Y-m-d')]);
        
        // Estadísticas para comparativa
        $resumen = $repo->getResumenFinanciero();
        
        $this->view('pagos/index', [
            'page_title' => 'Gestión de Caja',
            'total_hoy' => $totalHoy,
            'ingresos' => $ingresosRecientes,
            'resumen' => $resumen
        ]);
    }

    /**
     * Procesa un nuevo pago (si se hace manualmente desde caja)
     */
    public function procesar() {
        // En SRP, esto debería ir a un Service, pero seguiremos el patrón actual
        // por ahora para mantener consistencia con los otros controladores
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lógica de guardado
            // header('Location: pagos');
        }
    }
}
