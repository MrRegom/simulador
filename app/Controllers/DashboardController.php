<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller {
    private $equipoService;

    public function __construct() {
        $this->equipoService = new \App\Services\EquipoService();
    }

    public function index() {
        $stats = $this->equipoService->getDashboardStats();
        $equipos = $this->equipoService->getAllEquipos();

        // Detectar IP del Servidor para ayuda de configuración
        $server_ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());

        $data = [
            'page_title' => 'Panel de Control',
            'stats' => $stats,
            'equipos' => $equipos,
            'server_ip' => $server_ip
        ];
        
        $this->view('dashboard/index', $data);
    }
}
