<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Repositories\RoleRepository;
use App\Repositories\PermisoRepository;

class RoleController extends Controller {
    private $roleRepo;
    private $permisoRepo;

    public function __construct() {
        if (!AuthService::isAdmin()) {
            header("Location: " . \App\Config\Config::getAppUrl() . "/dashboard");
            exit;
        }
        $this->roleRepo = new RoleRepository();
        $this->permisoRepo = new PermisoRepository();
    }

    public function index() {
        $roles = $this->roleRepo->all();
        return $this->view('roles/index', ['roles' => $roles]);
    }

    public function permisos() {
        $rolId = $_GET['id'] ?? null;
        if (!$rolId) {
            header("Location: index");
            exit;
        }

        $rol = $this->roleRepo->find($rolId);
        $permisosAgrupados = $this->permisoRepo->getAllGroupedByModulo();
        $permisosActuales = $this->roleRepo->getPermisosPorRol($rolId);

        return $this->view('roles/permisos', [
            'rol' => $rol,
            'permisos' => $permisosAgrupados,
            'actuales' => $permisosActuales
        ]);
    }

    public function save() {
        $data = [
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion']
        ];

        if (!empty($_POST['id'])) {
            $this->roleRepo->update($_POST['id'], $data);
        } else {
            $this->roleRepo->create($data);
        }

        return $this->json(['success' => true]);
    }

    public function save_permisos() {
        $rolId = $_POST['rol_id'] ?? null;
        $permisosIds = $_POST['permisos'] ?? [];

        if ($rolId) {
            $this->roleRepo->savePermisos($rolId, $permisosIds);
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }

    public function delete() {
        if (!empty($_POST['id'])) {
            // El admin (ID 1) no se puede borrar
            if($_POST['id'] == 1) return $this->json(['success' => false, 'error' => 'No se puede borrar el perfil Maestro']);
            $this->roleRepo->delete($_POST['id']);
        }
        return $this->json(['success' => true]);
    }
}
