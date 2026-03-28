<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\PistaRepository;

class PistaController extends Controller {

    public function index() {
        $pistaRepo = new PistaRepository();
        $pistas = $pistaRepo->all();
        $this->view('pistas/index', [
            'page_title' => 'Gestión de Pistas',
            'pistas' => $pistas
        ]);
    }

    public function save() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['nombre'])) throw new \Exception('El nombre es obligatorio');

            $pistaRepo = new PistaRepository();
            $success = $pistaRepo->save($input['nombre'], $input['id'] ?? null);

            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            if (!$id) throw new \Exception('ID Requerido');

            $pistaRepo = new PistaRepository();
            $success = $pistaRepo->delete($id);

            echo json_encode(['success' => $success]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
