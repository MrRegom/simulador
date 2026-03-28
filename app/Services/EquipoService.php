<?php
namespace App\Services;

use App\Repositories\EquipoRepository;

class EquipoService {
    private $equipoRepository;
    private $pagoRepository;

    public function __construct() {
        $this->equipoRepository = new EquipoRepository();
        $this->pagoRepository = new \App\Repositories\PagoRepository();
    }

    public function getDashboardStats() {
        return [
            'equipos_activos' => count($this->equipoRepository->all()),
            'en_uso' => $this->equipoRepository->countByEstado('en_uso'),
            'bloqueados' => $this->equipoRepository->countByEstado('bloqueado'),
            'ingresos_dia' => $this->pagoRepository->getTotalHoy()
        ];
    }

    public function getAllEquipos() {
        return $this->equipoRepository->allVisible();
    }
}
