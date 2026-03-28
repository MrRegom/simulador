<?php
/**
 * Front Controller - ServiRec
 * @author Antigravity
 */

// 1. Mostrar errores en desarrollo (Cambiar a 0 en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Autoloader PSR-4 Simplificado
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 3. Inicializar sesión
session_start();

// 4. Enrutamiento
use App\Core\Router;

$router = new Router();

// Definir rutas (URL, Controlador, Acción)
$router->add('dashboard', 'DashboardController', 'index');
$router->add('login', 'AuthController', 'login');
$router->add('logout', 'AuthController', 'logout');
$router->add('usuarios', 'UsuarioController', 'index');
$router->add('simuladores', 'SimuladorController', 'index');
$router->add('api/simulador/iniciar', 'SimuladorController', 'iniciar');
$router->add('api/simulador/libre', 'SimuladorController', 'libre');
$router->add('api/simulador/finalizar', 'SimuladorController', 'finalizar');
$router->add('api/inventario/list', 'InventarioController', 'list');
$router->add('api/inventario/create', 'InventarioController', 'create');
$router->add('api/inventario/save', 'InventarioController', 'save');
$router->add('api/inventario/delete', 'InventarioController', 'delete');
$router->add('api/simulador/offline', 'ApiController', 'offline');
$router->add('api/cliente/buscar', 'SimuladorController', 'buscarCliente');
$router->add('api/cliente/sugerir', 'SimuladorController', 'sugerirClientes');
$router->add('api/simulador/extender', 'SimuladorController', 'extender');
$router->add('api/simulador/estado', 'ApiController', 'estado');
$router->add('equipos', 'EquipoController', 'index');
$router->add('api/equipo/toggle-visibility', 'EquipoController', 'toggleVisibility');
$router->add('api/equipo/save', 'EquipoController', 'save');
$router->add('sesiones', 'SesionController', 'index');
$router->add('pagos', 'PagoController', 'index');
$router->add('reportes', 'ReporteController', 'index');
$router->add('api/caja/status', 'CajaController', 'status');
$router->add('api/caja/open', 'CajaController', 'open');
$router->add('api/caja/close', 'CajaController', 'close');
$router->add('api/caja/movimientos', 'CajaController', 'movimientos');
$router->add('api/caja/movimiento', 'CajaController', 'movimiento');
$router->add('api/reportes/resumen', 'ReporteController', 'api_resumen');
$router->add('api/simulador/listar_equipos', 'ApiController', 'listar_equipos');
$router->add('api/check', 'ApiController', 'check');
$router->add('api/monitor/sync', 'ApiController', 'monitor_sync');
// Pairing QR (Agente)
$router->add('pair', 'PairingController', 'index');
$router->add('api/pair/new', 'PairingController', 'newToken');
$router->add('api/pair/status', 'PairingController', 'status');
$router->add('api/pair/assign', 'PairingController', 'assign');
// Módulo Ranking
$router->add('ranking', 'RankingController', 'index');
$router->add('ranking/admin', 'RankingController', 'admin'); // New route
$router->add('ranking/display', 'RankingController', 'display');
$router->add('ranking/config', 'RankingController', 'config');
$router->add('api/ranking/save', 'RankingController', 'save');
$router->add('api/ranking/data', 'RankingController', 'api_data');
$router->add('api/ranking/track_summary', 'RankingController', 'api_track_summary');
$router->add('api/ranking/config_save', 'RankingController', 'config_save'); // New route
$router->add('api/ranking/admin_list', 'RankingController', 'admin_list'); // New route
$router->add('api/ranking/admin_update', 'RankingController', 'admin_update'); // New route
$router->add('api/ranking/admin_delete', 'RankingController', 'admin_delete'); // New route
$router->add('api/ranking/ads/save', 'RankingController', 'ads_save');
$router->add('api/ranking/ads/delete', 'RankingController', 'ads_delete');
$router->add('api/ranking/ads/toggle', 'RankingController', 'ads_toggle');
$router->add('api/ranking/ads/list', 'RankingController', 'ads_list_paginated');
$router->add('api/ranking/ads/duration', 'RankingController', 'ads_update_duration');
$router->add('api/ranking/marquee/list', 'RankingController', 'marquee_list');
$router->add('api/ranking/marquee/save', 'RankingController', 'marquee_save');
$router->add('api/ranking/marquee/delete', 'RankingController', 'marquee_delete');
$router->add('api/ranking/marquee/toggle', 'RankingController', 'marquee_toggle');
$router->add('api/usuario/save', 'UsuarioController', 'save');
$router->add('api/usuario/delete', 'UsuarioController', 'delete');
$router->add('roles', 'RoleController', 'index');
$router->add('roles/permisos', 'RoleController', 'permisos');
$router->add('api/roles/save', 'RoleController', 'save');
$router->add('api/roles/delete', 'RoleController', 'delete');
$router->add('api/roles/save_permisos', 'RoleController', 'save_permisos');

// Mantenedores
$router->add('pistas', 'PistaController', 'index');
$router->add('api/pistas/save', 'PistaController', 'save');
$router->add('api/pistas/delete', 'PistaController', 'delete');
$router->add('clientes_reg', 'ClienteController', 'index'); // Clientes registrados
$router->add('api/clientes/save', 'ClienteController', 'save');
$router->add('api/clientes/delete', 'ClienteController', 'delete');

$router->add('locked', 'ApiController', 'locked');

// 5. Despachar la petición
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$router->dispatch($url);
