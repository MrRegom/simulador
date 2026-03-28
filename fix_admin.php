<?php
require_once __DIR__ . '/public/index.php'; // Esto cargará el autoloader y la sesión

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Verificar si el usuario existe
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        // Actualizar contraseña
        $stmt = $db->prepare("UPDATE usuarios SET password = ?, estado = 1 WHERE usuario = 'admin'");
        $stmt->execute([$hash]);
        echo "USUARIO 'admin' ACTUALIZADO CON ÉXITO. CONTRASEÑA: admin123";
    } else {
        // Crear usuario
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, usuario, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Administrador Master', 'admin', 'admin@servirec.cl', $hash, 'admin', 1]);
        echo "USUARIO 'admin' CREADO CON ÉXITO. CONTRASEÑA: admin123";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
