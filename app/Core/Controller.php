<?php
namespace App\Core;

abstract class Controller {
    protected function view($view, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);

        $viewFile = __DIR__ . "/../../views/" . $view . ".php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("La vista $view no existe.");
        }
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($path) {
        header("Location: " . \App\Config\Config::APP_URL . "/" . $path);
        exit;
    }
}
