<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
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

    public function login() {
        if (AuthService::check()) {
                $baseUrl = $this->getBaseUrlFromRequest();
                header("Location: " . $baseUrl . "/dashboard");
                exit;
            }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($usuario, $password)) {
                $redirect = $_SESSION['return_to'] ?? 'dashboard';
                unset($_SESSION['return_to']);
                $redirect = ltrim($redirect, '/');
                $baseUrl = $this->getBaseUrlFromRequest();
                header("Location: " . $baseUrl . "/" . $redirect);
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
                return $this->view('auth/login', ['error' => $error]);
            }
        }

        return $this->view('auth/login');
    }

    public function logout() {
        $this->authService->logout();
        $baseUrl = $this->getBaseUrlFromRequest();
        header("Location: " . $baseUrl . "/login");
        exit;
    }
}
