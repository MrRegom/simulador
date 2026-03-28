<?php
namespace App\Controllers;
use App\Core\Controller;

use App\Repositories\EquipoRepository;

class EquipoController extends Controller {
    public function index() {
        $repo = new EquipoRepository();
        $equipos = $repo->all();
        
        $this->view('equipos/index', [
            'page_title' => 'Gestión de Equipos',
            'equipos' => $equipos
        ]);
    }

    public function toggleVisibility() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['id'])) throw new \Exception('ID requerido');

            $repo = new EquipoRepository();
            $success = $repo->toggleVisibility($input['id']);
            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function save() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['nombre'])) throw new \Exception('El nombre de la unidad es obligatorio');

            $db = \App\Core\Database::getInstance()->getConnection();
            $nombre = trim($input['nombre']);
            $id = !empty($input['id']) ? $input['id'] : null;

            // VALIDACIÓN SENIOR: Nombre único (Case-insensitive)
            $checkSql = "SELECT id FROM equipos WHERE LOWER(nombre) = LOWER(?) ";
            $checkParams = [$nombre];
            if ($id) {
                $checkSql .= " AND id <> ?";
                $checkParams[] = $id;
            }
            $stmtCheck = $db->prepare($checkSql);
            $stmtCheck->execute($checkParams);
            if ($stmtCheck->fetch()) {
                throw new \Exception("Ya existe una unidad registrada con el nombre '$nombre'");
            }

            if ($id) {
                // Actualizar unidad existente
                $stmt = $db->prepare("UPDATE equipos SET nombre = ?, updated_at = NOW() WHERE id = ?");
                $success = $stmt->execute([$nombre, $id]);
            } else {
                // Registrar nueva unidad (Cabina)
                $stmt = $db->prepare("INSERT INTO equipos (nombre, estado, is_visible, created_at) VALUES (?, 'libre', 1, NOW())");
                $success = $stmt->execute([$nombre]);
            }

            echo json_encode(['success' => $success, 'message' => 'Configuración guardada correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
