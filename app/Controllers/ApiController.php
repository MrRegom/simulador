<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class ApiController extends Controller {

    public function estado() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        try {
            // Intentar obtener parámetros de múltiples fuentes (GET, POST, o JSON)
            $id = $_GET['id'] ?? $_POST['id'] ?? null;
            $ip = $_GET['ip'] ?? $_POST['ip'] ?? null;

            // Si es una petición JSON (como la del agente moderno)
            $jsonInput = json_decode(file_get_contents('php://input'), true);
            if ($jsonInput) {
                $id = $id ?? $jsonInput['id'] ?? $jsonInput['equipo_id'] ?? null;
                $ip = $ip ?? $jsonInput['ip'] ?? null;
            }

            if (!$ip && !$id) {
                http_response_code(400);
                echo json_encode(['error' => 'PARAMETROS_FALTANTES', 'msg' => 'Se requiere ID o IP del simulador']);
                return;
            }

            // Auto-detectar IP del cliente para actualizar en BD
            $clientIp = $_SERVER['REMOTE_ADDR'];
            if ($clientIp === '::1') $clientIp = '127.0.0.1';
            
            // Si el cliente viene de un proxy o red compleja, intentar capturar la real
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $clientIp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT id, nombre, ip_address, estado, hora_fin FROM equipos WHERE ";
            $params = [];

            if ($id) {
                $sql .= "id = :id";
                $params[':id'] = $id;
            } else {
                $sql .= "ip_address = :ip";
                $params[':ip'] = $ip;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$equipo) {
                echo json_encode(['bloqueado' => true, 'mensaje' => 'EQUIPO_NO_REGISTRADO_EN_NUCLEO']);
                return;
            }

            // Actualización forzada de IP y Heartbeat cada vez que el agente llama
            $upd = $db->prepare("UPDATE equipos SET ip_address = :new_ip, ultimo_heartbeat = NOW() WHERE id = :id");
            $upd->execute([':new_ip' => $clientIp, ':id' => $equipo['id']]);

            // Lógica de Estado
            $bloqueado = true;
            $mensaje = "TIEMPO AGOTADO";
            $tiempo_restante = 0;

            if ($equipo['estado'] == 'en_uso') {
                // Verificar si el tiempo ya expiró
                if (!empty($equipo['hora_fin'])) {
                    $fin = strtotime($equipo['hora_fin']);
                    $ahora = time();
                    
                    if ($fin > $ahora) {
                        $bloqueado = false;
                        $tiempo_restante = $fin - $ahora;
                        $mensaje = "TIEMPO RESTANTE: " . gmdate("H:i:s", $tiempo_restante);
                    } else {
                        $mensaje = "TIEMPO FINALIZADO";
                    }
                }
            } elseif ($equipo['estado'] == 'libre') {
                $mensaje = "DISPONIBLE - PASE A CAJA";
            }

            echo json_encode([
                'id' => $equipo['id'],
                'nombre' => $equipo['nombre'],
                'bloqueado' => $bloqueado,
                'tiempo_restante' => $tiempo_restante,
                'mensaje' => $mensaje
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function listar_equipos() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, nombre FROM equipos ORDER BY nombre ASC");
            $stmt->execute();
            $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'equipos' => $equipos]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function monitor_sync() {
        header('Content-Type: application/json');
        try {
            $service = new \App\Services\EquipoService();
            $equipos = $service->getAllEquipos();
            
            foreach ($equipos as &$equipo) {
                $equipo['tiempo_restante'] = 0;
                if ($equipo['estado'] == 'en_uso' && !empty($equipo['hora_fin'])) {
                    $fin = strtotime($equipo['hora_fin']);
                    $ahora = time();
                    if ($fin > $ahora) {
                        $equipo['tiempo_restante'] = $fin - $ahora;
                    }
                }
            }
            
            echo json_encode(['success' => true, 'equipos' => $equipos]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function offline() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? $_POST['id'] ?? $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID_REQUERIDO']);
                return;
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE equipos 
                                  SET estado = 'bloqueado', hora_fin = NOW(), ip_address = NULL, ultimo_heartbeat = NULL 
                                  WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
