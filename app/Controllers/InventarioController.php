<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\InventarioRepository;

class InventarioController extends Controller {

    public function list() {
        header('Content-Type: application/json');
        try {
            $search = $_GET['search'] ?? '';
            $repo = new InventarioRepository();
            $items = $repo->listAll($search);
            echo json_encode(['success' => true, 'items' => $items]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function create() {
        header('Content-Type: application/json');
        try {
            $nombre = trim($_POST['nombre'] ?? '');
            $codigo = trim($_POST['codigo'] ?? '');
            $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

            if ($nombre === '') throw new \Exception('Nombre requerido');

            $repo = new InventarioRepository();
            if ($codigo === '') {
                $codigo = $repo->generateCode($nombre);
            } else {
                if ($repo->codeExists($codigo)) {
                    throw new \Exception('Código ya existe');
                }
            }

            $imagenPath = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/uploads/productos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadDir . $filename)) {
                    $imagenPath = 'uploads/productos/' . $filename;
                }
            }

            $id = $repo->create([
                'nombre' => $nombre,
                'codigo' => $codigo,
                'precio' => $precio,
                'stock' => $stock,
                'imagen_path' => $imagenPath,
                'activo' => $activo
            ]);

            echo json_encode(['success' => true, 'id' => $id, 'codigo' => $codigo]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function save() {
        header('Content-Type: application/json');
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = trim($_POST['nombre'] ?? '');
            $codigo = trim($_POST['codigo'] ?? '');
            $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

            if (!$id) throw new \Exception('ID requerido');
            if ($nombre === '') throw new \Exception('Nombre requerido');

            $repo = new InventarioRepository();
            if ($codigo === '') {
                $codigo = $repo->generateCode($nombre);
            } else {
                if ($repo->codeExists($codigo, $id)) {
                    throw new \Exception('Código ya existe');
                }
            }

            $imagenPath = $_POST['imagen_actual'] ?? null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/uploads/productos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadDir . $filename)) {
                    $imagenPath = 'uploads/productos/' . $filename;
                }
            }

            $repo->update($id, [
                'nombre' => $nombre,
                'codigo' => $codigo,
                'precio' => $precio,
                'stock' => $stock,
                'imagen_path' => $imagenPath,
                'activo' => $activo
            ]);

            echo json_encode(['success' => true, 'codigo' => $codigo]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) throw new \Exception('ID requerido');
            $repo = new InventarioRepository();
            $repo->delete($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

