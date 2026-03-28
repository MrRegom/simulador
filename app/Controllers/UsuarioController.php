<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Repositories\UsuarioRepository;

class UsuarioController extends Controller {
    private $usuarioRepo;

    public function __construct() {
        // Solo admins pueden entrar aquí
        if (!AuthService::isAdmin()) {
            header("Location: " . \App\Config\Config::getAppUrl() . "/dashboard");
            exit;
        }
        $this->usuarioRepo = new UsuarioRepository();
    }

    public function index() {
        $usuarios = $this->usuarioRepo->all();
        // Cargar roles dinámicamente para el selector del modal
        $roleRepo = new \App\Repositories\RoleRepository();
        $roles = $roleRepo->all();
        return $this->view('usuarios/index', [
            'usuarios' => $usuarios,
            'roles'    => $roles
        ]);
    }

    // Métodos API para gestión (AJAX)
    public function save() {
        $data = [
            'nombre' => $_POST['nombre'],
            'usuario' => $_POST['usuario'],
            'password' => $_POST['password'],
            'rol' => $_POST['rol'],
            'estado' => $_POST['estado']
        ];

        if (!empty($_POST['id'])) {
            $this->usuarioRepo->update($_POST['id'], $data);
        } else {
            $this->usuarioRepo->create($data);
        }

        return $this->json(['success' => true]);
    }

    public function delete() {
        try {
            if (!empty($_POST['id'])) {
                $this->usuarioRepo->delete($_POST['id']);
                return $this->json(['success' => true]);
            }
            return $this->json(['success' => false, 'error' => 'ID no proporcionado']);
        } catch (\Exception $e) {
            // Si hay sesiones vinculadas, no se puede borrar físicamente
            return $this->json([
                'success' => false, 
                'error' => 'No se puede eliminar el usuario porque tiene registros vinculados (sesiones o pagos). Recomiendo desactivar el usuario en lugar de eliminarlo.'
            ]);
        }
    }
}
