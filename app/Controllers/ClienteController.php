<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class ClienteController extends Controller {

    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM clientes ORDER BY nombre ASC");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('clientes/index', [
            'page_title' => 'Gestión de Clientes',
            'clientes' => $clientes
        ]);
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) throw new \Exception('ID Requerido');

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM clientes WHERE id = ?");
            $success = $stmt->execute([$id]);

            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function save() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) throw new \Exception('ID Requerido');

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) throw new \Exception('Cliente no encontrado');

            $nombre = trim($input['nombre'] ?? $current['nombre'] ?? '');
            $telefono = trim($input['telefono'] ?? $current['telefono'] ?? '');
            $rut = trim($input['rut'] ?? $current['rut'] ?? '');
            $email = trim($input['email'] ?? $current['email'] ?? '');

            if ($nombre === '') throw new \Exception('Nombre requerido');

            $upd = $db->prepare("UPDATE clientes SET nombre = ?, telefono = ?, rut = ?, email = ? WHERE id = ?");
            $ok = $upd->execute([$nombre, $telefono, $rut, $email, $id]);

            echo json_encode(['success' => $ok]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function foto() {
        header('Content-Type: application/json');
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) throw new \Exception('ID Requerido');

            $reset = isset($_POST['reset']) && $_POST['reset'] == '1';
            $db = Database::getInstance()->getConnection();

            if ($reset) {
                $stmt = $db->prepare("UPDATE ranking SET foto_path = NULL WHERE cliente_id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'reset' => true]);
                return;
            }

            if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
                throw new \Exception('No hay foto vÃ¡lida');
            }

            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = 'piloto_' . $id . '_' . time() . '.' . $ext;
            $upload_dir = __DIR__ . '/../../public/uploads/pilotos/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
                throw new \Exception('Error al mover archivo');
            }

            $path = 'uploads/pilotos/' . $filename;
            $stmt = $db->prepare("UPDATE ranking SET foto_path = ? WHERE cliente_id = ?");
            $stmt->execute([$path, $id]);

            echo json_encode(['success' => true, 'path' => $path]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
