<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\EquipoService;
use App\Core\Database;

/**
 * Controlador para gestión de Simuladores
 * Responsabilidad: Mostrar el monitor de simuladores en tiempo real
 */
class SimuladorController extends Controller {

    public function index() {
        $service = new EquipoService();
        $equipos = $service->getAllEquipos();
        
        // Calcular tiempo restante para cada equipo activo
        foreach ($equipos as &$equipo) {
            $equipo['tiempo_restante'] = 0;
            if ($equipo['estado'] == 'en_uso' && !empty($equipo['hora_fin'])) {
                $fin = strtotime($equipo['hora_fin']);
                $ahora = time();
                if ($fin > $ahora) {
                    $equipo['tiempo_restante'] = $fin - $ahora; // Segundos restantes
                }
            }
        }
        
        $data = [
            'page_title' => 'Monitor de Simuladores',
            'equipos' => $equipos
        ];
        
        $this->view('simuladores/index', $data);
    }

    public function iniciar() {
        header('Content-Type: application/json');
        
        try {
            // Leer JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new \Exception('Datos inválidos');
            }

            $equipoId = $input['equipo_id'];
            $tiempo = $input['tiempo'];
            $monto = $input['monto'];
            $metodo = $input['metodo'];
            $modo = $input['modo'] ?? 'controlado';
            $pista = $input['pista'] ?? null;
            if (!in_array($modo, ['controlado', 'manual'], true)) {
                $modo = 'controlado';
            }

            if ($modo === 'controlado') {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT ip_address, ultimo_heartbeat FROM equipos WHERE id = ?");
                $stmt->execute([$equipoId]);
                $eq = $stmt->fetch();
                if (!$eq) {
                    throw new \Exception('EQUIPO_NO_EXISTE');
                }
                $lastHeartbeat = !empty($eq['ultimo_heartbeat']) ? strtotime($eq['ultimo_heartbeat']) : 0;
                $isOnline = ($lastHeartbeat > (time() - 300));
                $ip = $eq['ip_address'] ?? '';
                $isOffline = !$isOnline || $ip === '0.0.0.0' || empty($ip);
                if ($isOffline) {
                    throw new \Exception('AGENTE_OFFLINE');
                }
            }
            
            // Lógica Cliente
            $clienteId = null;
            $telefono = $input['telefono'] ?? null;
            $rut = $input['rut'] ?? null;
            $nombre = $input['nombre_cliente'] ?? null;
            $email = $input['email'] ?? null;

            if ($telefono || $rut) {
                $cliRepo = new \App\Repositories\ClienteRepository();
                $cliente = null;
                
                if ($telefono) {
                    $cliente = $cliRepo->buscarPorTelefono($telefono);
                }
                
                if (!$cliente && $rut) {
                    $cliente = $cliRepo->buscarPorRut($rut);
                }
                
                if ($cliente) {
                    $clienteId = $cliente['id'];
                    $cliRepo->registrarVisita($clienteId);

                    // Actualizar datos faltantes inteligentemente
                    if (!$cliente['rut'] && $rut) {
                        $cliRepo->actualizarRut($clienteId, $rut);
                    }
                    if (!$cliente['telefono'] && $telefono) {
                        $cliRepo->actualizarTelefono($clienteId, $telefono);
                    }
                    if ($nombre && (!$cliente['nombre'] || $cliente['nombre'] !== $nombre)) {
                        $cliRepo->actualizarNombre($clienteId, $nombre);
                    }
                    if ($email && (!$cliente['email'] || $cliente['email'] !== $email)) {
                        $cliRepo->actualizarEmail($clienteId, $email);
                    }
                } else {
                    // Nuevo Cliente (Piloto) - Ahora capturamos el nombre real
                    $nombre = $nombre ?? 'PILOTO_NUEVO';
                    $email = $email ?? null;
                    $clienteId = $cliRepo->crear($telefono, $nombre, $rut, $email);
                }
            }

            $repo = new \App\Repositories\SesionRepository();
            $resultado = $repo->iniciarSesion($equipoId, $tiempo, $monto, $metodo, $clienteId, $pista, $modo);

            if ($resultado === true) {
                echo json_encode(['success' => true, 'message' => 'Sesión iniciada correctamente']);
            } else {
                throw new \Exception($resultado); // Esto lanzará el error de DB capturado
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function libre() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new \Exception('Datos inválidos');
            }

            $equipoId = $input['equipo_id'] ?? null;
            $equipoIp = $input['equipo_ip'] ?? null;
            $pista = $input['pista'] ?? null;
            $nota = $input['nota'] ?? 'DESBLOQUEO_LIBRE';

            if (!$equipoId && $equipoIp) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT id FROM equipos WHERE ip_address = ? LIMIT 1");
                $stmt->execute([$equipoIp]);
                $eq = $stmt->fetch();
                if ($eq && isset($eq['id'])) {
                    $equipoId = $eq['id'];
                }
            }

            if (!$equipoId) {
                throw new \Exception('EQUIPO_NO_EXISTE');
            }

            $usuarioId = $_SESSION['usuario_id'] ?? 1;
            $cajaRepo = new \App\Repositories\CajaRepository();
            $cajaId = $cajaRepo->ensureOpenCaja($usuarioId);
            $cajaRepo->addMovimiento([
                'caja_id' => $cajaId,
                'usuario_id' => $usuarioId,
                'equipo_id' => $equipoId,
                'pista' => $pista,
                'minutos' => 0,
                'tipo' => 'ajuste',
                'categoria' => 'desbloqueo_libre',
                'metodo_pago' => 'otro',
                'monto' => 0,
                'descripcion' => $nota,
                'origen' => 'sistema'
            ]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function finalizar() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['equipo_id'])) {
                throw new \Exception('ID de equipo requerido');
            }

            $equipoId = $input['equipo_id'];
            $repo = new \App\Repositories\SesionRepository();
            $resultado = $repo->finalizarSesionPorEquipo($equipoId);

            if ($resultado === true) {
                echo json_encode(['success' => true, 'message' => 'Sesión finalizada']);
            } else {
                throw new \Exception($resultado);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function extender() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new \Exception('Datos inválidos');

            $equipoId = $input['equipo_id'];
            $tiempo = $input['tiempo'];
            $monto = $input['monto'];
            $metodo = $input['metodo'];

            $repo = new \App\Repositories\SesionRepository();
            $resultado = $repo->extenderSesion($equipoId, $tiempo, $monto, $metodo);

            if ($resultado === true) {
                echo json_encode(['success' => true, 'message' => 'Tiempo añadido correctamente']);
            } else {
                throw new \Exception($resultado);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function buscarCliente() {
        header('Content-Type: application/json');
        try {
            $telefono = $_GET['telefono'] ?? '';
            $rut = $_GET['rut'] ?? '';
            
            if (!$telefono && !$rut) {
                echo json_encode(['found' => false]);
                return;
            }
            
            $repo = new \App\Repositories\ClienteRepository();
            $cliente = null;
            
            if ($telefono) {
                $cliente = $repo->buscarPorTelefono($telefono);
            }
            
            if (!$cliente && $rut) {
                $cliente = $repo->buscarPorRut($rut);
            }
            
            echo json_encode(['found' => (bool)$cliente, 'cliente' => $cliente]);
        } catch (\Exception $e) {
            echo json_encode(['found' => false]);
        }
    }
    public function sugerirClientes() {
        header('Content-Type: application/json');
        try {
            $term = $_GET['term'] ?? '';
            if (empty($term)) {
                echo json_encode([]);
                return;
            }
            $repo = new \App\Repositories\ClienteRepository();
            echo json_encode($repo->sugerir($term));
        } catch (\Exception $e) {
            echo json_encode([]);
        }
    }
}

