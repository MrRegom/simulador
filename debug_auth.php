<?php
// Script de diagnóstico profundo para ServiRec (STANDALONE)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader manual para evitar el Router
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Core\Database;
use App\Config\Config;

echo "<h3>Diagnóstico de Autenticación ServiRec</h3>";

try {
    $db = Database::getInstance()->getConnection();
    echo "1. Conexión a DB: <span style='color:green'>OK</span> (" . Config::getDBName() . ")<br>";

    $usuario = 'admin';
    echo "2. Buscando usuario: '$usuario'...<br>";
    
    $stmt = $db->prepare("SELECT id, nombre, usuario, email, password, estado, rol FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user) {
        echo "- Usuario encontrado en DB: <span style='color:green'>SÍ</span> (ID: " . $user['id'] . ")<br>";
        echo "- Estado: " . ($user['estado'] == 1 ? "<span style='color:green'>ACTIVO</span>" : "<span style='color:red'>INACTIVO</span>") . "<br>";
        echo "- Hash en DB: <code>" . $user['password'] . "</code><br>";
        
        $pass_test = 'admin123';
        echo "3. Verificando contraseña '$pass_test' contra el hash almacenado...<br>";
        
        if (password_verify($pass_test, $user['password'])) {
            echo "- Resultado: <span style='color:green'>✓ CONTRASEÑA VÁLIDA</span><br>";
            echo "<h4>¡TODO PARECE ESTAR CORRECTO!</h4>";
            echo "Si no puedes entrar, limpia las cookies de tu navegador.";
        } else {
            echo "- Resultado: <span style='color:red'>✗ CONTRASEÑA INVÁLIDA</span><br>";
            echo "- Generando nuevo hash para repararlo...<br>";
            
            $new_hash = password_hash($pass_test, PASSWORD_DEFAULT);
            $upd = $db->prepare("UPDATE usuarios SET password = ?, estado = 1 WHERE id = ?");
            if ($upd->execute([$new_hash, $user['id']])) {
                echo "- <span style='color:blue'>¡HASH ACTUALIZADO CORRECTAMENTE!</span><br>";
                echo "Vuelve al login e intenta con <b>admin / admin123</b>";
            }
        }
    } else {
        echo "- <span style='color:red'>USUARIO NO ENCONTRADO.</span> Creándolo ahora...<br>";
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $db->prepare("INSERT INTO usuarios (nombre, usuario, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
        if ($ins->execute(['Administrador Master', 'admin', 'admin@servirec.cl', $new_hash, 'admin', 1])) {
            echo "- <span style='color:blue'>¡USUARIO 'admin' CREADO CON ÉXITO!</span><br>";
            echo "Prueba el login con <b>admin / admin123</b>";
        }
    }

} catch (Exception $e) {
    echo "<span style='color:red'>ERROR CRÍTICO: " . $e->getMessage() . "</span>";
}
