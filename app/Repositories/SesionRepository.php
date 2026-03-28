<?php
namespace App\Repositories;

class SesionRepository extends BaseRepository {
    protected $table = 'sesiones';

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (equipo_id, usuario_id, tiempo_asignado_min, hora_inicio, hora_fin, estado) 
                VALUES (:equipo_id, :usuario_id, :tiempo, :inicio, :fin, 'activa')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':equipo_id' => $data['equipo_id'],
            ':usuario_id' => $data['usuario_id'],
            ':tiempo' => $data['tiempo'],
            ':inicio' => $data['inicio'],
            ':fin' => $data['fin']
        ]);
        
        return $this->db->lastInsertId();
    }

    public function iniciarSesion($equipoId, $tiempo, $monto, $metodo, $clienteId = null, $pista = null, $modo = 'controlado') {
        try {
            $this->db->beginTransaction();

            $inicio = date('Y-m-d H:i:s');
            // Nota: hora_fin aquí es la ESTIMADA, pero la real será NULL o NOW() al finalizar
            $fin = date('Y-m-d H:i:s', strtotime("+$tiempo minutes"));
            $usuarioId = $_SESSION['usuario_id'] ?? 1;

            // 1. Crear Sesión
            $sqlSesion = "INSERT INTO sesiones (equipo_id, usuario_id, cliente_id, pista, modo, tiempo_asignado_min, hora_inicio, hora_fin, estado, created_at) 
                          VALUES (:equipo_id, :usuario_id, :cliente_id, :pista, :modo, :tiempo, :inicio, :fin, 'activa', NOW())";
            
            $stmt = $this->db->prepare($sqlSesion);
            $stmt->execute([
                ':equipo_id' => $equipoId,
                ':usuario_id' => $usuarioId,
                ':cliente_id' => $clienteId,
                ':pista' => $pista,
                ':modo' => $modo,
                ':tiempo' => $tiempo,
                ':inicio' => $inicio,
                ':fin' => $fin
            ]);
            $sesionId = $this->db->lastInsertId();

            // 2. Registrar Pago
            $sqlPago = "INSERT INTO pagos (sesion_id, monto, metodo_pago, fecha_pago) 
                        VALUES (:sesion_id, :monto, :metodo, NOW())";
            $stmt = $this->db->prepare($sqlPago);
            $stmt->execute([
                ':sesion_id' => $sesionId,
                ':monto' => $monto,
                ':metodo' => $metodo
            ]);
            $pagoId = $this->db->lastInsertId();

            if ($monto > 0) {
                $cajaRepo = new \App\Repositories\CajaRepository();
                $cajaId = $cajaRepo->ensureOpenCaja($usuarioId);
                $movId = $cajaRepo->addMovimiento([
                    'caja_id' => $cajaId,
                    'sesion_id' => $sesionId,
                    'usuario_id' => $usuarioId,
                    'cliente_id' => $clienteId,
                    'equipo_id' => $equipoId,
                    'pista' => $pista,
                    'minutos' => $tiempo,
                    'tipo' => 'ingreso',
                    'categoria' => 'venta_sesion',
                    'metodo_pago' => $metodo,
                    'monto' => $monto,
                    'origen' => 'sistema'
                ]);
                $upd = $this->db->prepare("UPDATE pagos SET caja_movimiento_id = :mov WHERE id = :id");
                $upd->execute([':mov' => $movId, ':id' => $pagoId]);
            }

            // 3. Actualizar Estado Equipo
            $sqlEquipo = "UPDATE equipos SET estado = 'en_uso', hora_fin = :fin WHERE id = :id";
            $stmt = $this->db->prepare($sqlEquipo);
            $stmt->execute([
                ':fin' => $fin,
                ':id' => $equipoId
            ]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return "DB Error: " . $e->getMessage();
        }
    }

    public function extenderSesion($equipoId, $tiempoExtra, $monto, $metodo) {
        try {
            $this->db->beginTransaction();

            // 1. Obtener sesión activa
            $sqlSes = "SELECT id, tiempo_asignado_min, hora_fin FROM sesiones WHERE equipo_id = :id AND estado = 'activa' LIMIT 1";
            $stmt = $this->db->prepare($sqlSes);
            $stmt->execute([':id' => $equipoId]);
            $sesion = $stmt->fetch();

            if (!$sesion) throw new \Exception("No hay sesión activa para extender");

            // 2. Calcular nuevos tiempos
            $nuevoTotalMin = $sesion['tiempo_asignado_min'] + $tiempoExtra;
            $nuevaHoraFin = date('Y-m-d H:i:s', strtotime($sesion['hora_fin'] . " +$tiempoExtra minutes"));

            // 3. Actualizar Sesión
            $updSes = "UPDATE sesiones SET tiempo_asignado_min = :t, hora_fin = :f WHERE id = :id";
            $stmt = $this->db->prepare($updSes);
            $stmt->execute([':t' => $nuevoTotalMin, ':f' => $nuevaHoraFin, ':id' => $sesion['id']]);

            // 4. Registrar Pago extra
            $sqlPago = "INSERT INTO pagos (sesion_id, monto, metodo_pago, fecha_pago) VALUES (:sid, :m, :met, NOW())";
            $stmt = $this->db->prepare($sqlPago);
            $stmt->execute([':sid' => $sesion['id'], ':m' => $monto, ':met' => $metodo]);
            $pagoId = $this->db->lastInsertId();

            if ($monto > 0) {
                $cajaRepo = new \App\Repositories\CajaRepository();
                $usuarioId = $_SESSION['usuario_id'] ?? 1;
                $cajaId = $cajaRepo->ensureOpenCaja($usuarioId);
                $movId = $cajaRepo->addMovimiento([
                    'caja_id' => $cajaId,
                    'sesion_id' => $sesion['id'],
                    'usuario_id' => $usuarioId,
                    'equipo_id' => $equipoId,
                    'tipo' => 'ingreso',
                    'categoria' => 'extension',
                    'metodo_pago' => $metodo,
                    'monto' => $monto,
                    'origen' => 'sistema'
                ]);
                $upd = $this->db->prepare("UPDATE pagos SET caja_movimiento_id = :mov WHERE id = :id");
                $upd->execute([':mov' => $movId, ':id' => $pagoId]);
            }

            // 5. Actualizar Equipo
            $updEq = "UPDATE equipos SET hora_fin = :f WHERE id = :id";
            $stmt = $this->db->prepare($updEq);
            $stmt->execute([':f' => $nuevaHoraFin, ':id' => $equipoId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    public function finalizarSesionPorEquipo($equipoId) {
        try {
            $this->db->beginTransaction();

            // 1. Cerrar Sesión Activa
            $sqlSesion = "UPDATE sesiones SET estado = 'finalizada', hora_fin = NOW(), updated_at = NOW() 
                          WHERE equipo_id = :equipo_id AND estado = 'activa'";
            $stmt = $this->db->prepare($sqlSesion);
            $stmt->execute([':equipo_id' => $equipoId]);

            // 2. Liberar Equipo
            // Importante: Poner hora_fin a NULL para que la API sepa que no hay tiempo asignado
            $sqlEquipo = "UPDATE equipos SET estado = 'libre', hora_fin = NULL WHERE id = :id";
            $stmt = $this->db->prepare($sqlEquipo);
            $stmt->execute([':id' => $equipoId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return "DB Error: " . $e->getMessage();
        }
    }
}
