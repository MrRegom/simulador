<?php
namespace App\Services;

use App\Repositories\SesionRepository;
use App\Repositories\EquipoRepository;
use Exception;

class SesionService {
    private $sesionRepository;
    private $equipoRepository;

    public function __construct() {
        $this->sesionRepository = new SesionRepository();
        $this->equipoRepository = new EquipoRepository();
    }

    public function startSesion($equipoId, $usuarioId, $minutos) {
        $this->db = \App\Core\Database::getInstance()->getConnection();
        
        try {
            $this->db->beginTransaction();

            $horaInicio = date('Y-m-d H:i:s');
            $horaFin = date('Y-m-d H:i:s', strtotime("+$minutos minutes"));

            $sesionId = $this->sesionRepository->createSesion([
                'equipo_id' => $equipoId,
                'usuario_id' => $usuarioId,
                'tiempo_asignado_min' => $minutos,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin
            ]);

            $this->equipoRepository->updateEstado($equipoId, 'en_uso');

            $this->db->commit();
            return $sesionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function checkExpiredSessions() {
        $activas = $this->sesionRepository->getActiveSessions();
        $now = date('Y-m-d H:i:s');
        $expired = [];

        foreach ($activas as $sesion) {
            if ($now >= $sesion['hora_fin']) {
                $this->sesionRepository->closeSesion($sesion['id']);
                $this->equipoRepository->updateEstado($sesion['equipo_id'], 'bloqueado');
                $expired[] = $sesion;
            }
        }
        return $expired;
    }
}
