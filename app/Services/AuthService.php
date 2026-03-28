<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;

class AuthService {
    private $usuarioRepo;

    public function __construct() {
        $this->usuarioRepo = new UsuarioRepository();
    }

    public function login($usuario, $password) {
        $user = $this->usuarioRepo->findByUsuario($usuario);

        if ($user && password_verify($password, $user['password'])) {
            // Guardar en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['rol_id'] = $user['rol_id'] ?? ($user['rol'] == 'admin' ? 1 : 2);
            
            // Cargar Permisos
            $roleRepo = new \App\Repositories\RoleRepository();
            $_SESSION['user_permisos'] = $roleRepo->getPermisosPorRol($_SESSION['rol_id']);
            
            $this->usuarioRepo->updateUltimoLogin($user['id']);
            return true;
        }

        return false;
    }

    public static function hasPermission($permission) {
        if (!isset($_SESSION['user_permisos'])) return false;
        // El admin ID 1 siempre tiene todo
        if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) return true;
        return in_array($permission, $_SESSION['user_permisos']);
    }

    public function logout() {
        session_destroy();
    }

    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
    }
}
