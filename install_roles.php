<?php
// Script para instalar el módulo de Roles y Permisos
require_once __DIR__ . '/public/index.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $sql = file_get_contents(__DIR__ . '/app/Config/roles_permisos.sql');
    
    // MariaDB/MySQL no soporta ejecutar múltiples sentencias con prepare/execute normal en algunos drivers
    // Usaremos exec para el script completo si el driver lo permite, o dividiremos por ;
    
    // Una forma más segura de procesar el SQL:
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        $q = trim($query);
        if (!empty($q)) {
            $db->exec($q);
        }
    }
    
    echo "<h3>SISTEMA DE ROLES INSTALADO CON ÉXITO</h3>";
    echo "<p>Se han creado las tablas y asignado permisos base.</p>";
    echo "<a href='dashboard'>Volver al Panel</a>";

} catch (Exception $e) {
    echo "<h3>ERROR EN INSTALACIÓN</h3>";
    echo "<p style='color:red'>" . $e->getMessage() . "</p>";
}
