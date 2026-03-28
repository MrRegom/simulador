<?php
namespace App\Core;

class Router {
    protected $routes = [];

    public function add($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
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

    public function dispatch($url) {
        $url = trim($url, '/');
        
        // Rutas públicas (no requieren login)
        $publicRoutes = [
            'login', 
            'ranking/display', 
            'api/ranking/data',
            'api/ranking/track_summary', 
            'api/simulador/estado',
            'api/simulador/listar_equipos',
            'api/simulador/offline',
            'api/simulador/libre',
            'api/ranking/admin_list',
            'api/ranking/admin_update',
            'api/ranking/admin_delete',
            'api/inventario/list',
            'api/inventario/create',
            'api/inventario/save',
            'api/inventario/delete',
            'api/pair/new',
            'api/pair/status',
            'api/caja/status',
            'api/caja/open',
            'api/caja/close',
            'api/caja/movimientos',
            'api/caja/movimiento',
            'api/reportes/resumen'
        ];
        
        if (empty($url)) {
            $url = 'dashboard';
        }

        // Verificar Sesión
        if (!isset($_SESSION['user_id']) && !in_array($url, $publicRoutes)) {
            // Guardar URL solicitada para redirecciÃ³n post-login (incluye querystring)
            $returnTo = $url;
            if (!empty($_GET)) {
                $qs = $_GET;
                unset($qs['url']);
                if (!empty($qs)) {
                    $returnTo .= '?' . http_build_query($qs);
                }
            }
            $_SESSION['return_to'] = $returnTo;
            $baseUrl = $this->getBaseUrlFromRequest();
            header("Location: " . $baseUrl . "/login");
            exit;
        }

        if (array_key_exists($url, $this->routes)) {
            $controllerName = "App\\Controllers\\" . $this->routes[$url]['controller'];
            $action = $this->routes[$url]['action'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    die("Acción $action no encontrada en el controlador $controllerName.");
                }
            } else {
                die("Controlador $controllerName no encontrado.");
            }
        } else {
            http_response_code(404);
            echo "404 - Página no encontrada ($url)";
        }
    }
}
