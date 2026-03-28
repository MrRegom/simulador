<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\PairingRepository;
use App\Repositories\EquipoRepository;

class PairingController extends Controller {
    private $pairRepo;

    public function __construct() {
        $this->pairRepo = new PairingRepository();
    }

    private function endsWith($haystack, $needle) {
        if ($needle === '') return true;
        return substr($haystack, -strlen($needle)) === $needle;
    }

    private function getBaseUrlFromRequest() {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';

        $basePath = '';
        if ($script) {
            if ($this->endsWith($script, '/public/index.php')) {
                $basePath = substr($script, 0, -strlen('/public/index.php'));
            } elseif ($this->endsWith($script, '/index.php')) {
                $basePath = substr($script, 0, -strlen('/index.php'));
            } else {
                $basePath = rtrim(dirname($script), '/');
            }
        }

        return rtrim($scheme . '://' . $host . $basePath, '/');
    }

    public function index() {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            echo "TOKEN_INVALIDO";
            return;
        }

        $equipoRepo = new EquipoRepository();
        $equipos = $equipoRepo->all();
        $baseUrl = $this->getBaseUrlFromRequest();

        $this->view('pair/index', [
            'page_title' => 'Vincular Simulador',
            'token' => $token,
            'equipos' => $equipos,
            'base_url' => $baseUrl
        ]);
    }

    public function newToken() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $clientName = $input['client_name'] ?? ($_POST['client_name'] ?? null);
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;

            $ttl = 300; // 5 minutos
            $data = $this->pairRepo->createToken($clientIp, $clientName, $ttl);
            $baseUrl = $this->getBaseUrlFromRequest();

            echo json_encode([
                'success' => true,
                'token' => $data['token'],
                'pair_url' => $baseUrl . '/pair?token=' . urlencode($data['token']),
                'expires_at' => $data['expires_at'],
                'expires_in' => $ttl
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function status() {
        header('Content-Type: application/json');
        try {
            $token = $_GET['token'] ?? null;
            if (!$token) {
                echo json_encode(['success' => false, 'error' => 'TOKEN_REQUERIDO']);
                return;
            }

            $row = $this->pairRepo->getToken($token);
            if (!$row) {
                echo json_encode(['success' => false, 'error' => 'TOKEN_INVALIDO']);
                return;
            }

            $expired = (strtotime($row['expires_at']) < time());
            if ($expired) {
                echo json_encode(['success' => true, 'expired' => true, 'assigned' => false]);
                return;
            }

            $assigned = !empty($row['equipo_id']);
            $equipo = $assigned ? $this->pairRepo->getAssignedEquipo($token) : null;

            echo json_encode([
                'success' => true,
                'assigned' => $assigned,
                'equipo_id' => $equipo['equipo_id'] ?? null,
                'equipo_nombre' => $equipo['nombre'] ?? null
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function assign() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $token = $input['token'] ?? ($_POST['token'] ?? null);
            $equipoId = $input['equipo_id'] ?? ($_POST['equipo_id'] ?? null);

            if (!$token || !$equipoId) {
                echo json_encode(['success' => false, 'error' => 'DATOS_INCOMPLETOS']);
                return;
            }

            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'NO_AUTH']);
                return;
            }

            $ok = $this->pairRepo->assignToken($token, $equipoId, $userId);
            if ($ok) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'TOKEN_INVALIDO_O_EXPIRADO']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
